[CmdletBinding()]
param(
    [Parameter(Position = 0)]
    [ValidateSet('status', 'pull', 'deploy', 'migrate', 'verify', 'logs', 'restart', 'restore')]
    [string]$Command = 'status',

    [string]$ExpectedSha,
    [string]$BackupId,
    [switch]$ConfirmRestore,
    [switch]$Follow
)

$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest

if ($Follow -and $Command -ne 'logs') {
    throw '-Follow solo se puede usar con el comando logs.'
}
if ($Command -in @('deploy', 'migrate', 'verify') -and $ExpectedSha -notmatch '^[0-9a-fA-F]{7,40}$') {
    throw "$Command requiere -ExpectedSha con un SHA Git de 7 a 40 caracteres."
}
if ($Command -eq 'restore') {
    if ($BackupId -notmatch '^\d{8}T\d{6}Z-[0-9a-f]{7,40}$') {
        throw 'restore requiere -BackupId con el identificador exacto mostrado al crear la copia.'
    }
    if (-not $ConfirmRestore) {
        throw 'restore reemplaza la base de datos y uploads. Repite con -ConfirmRestore tras revisar el BackupId.'
    }
}

$projectRoot = Split-Path -Parent $PSScriptRoot
$envFile = Join-Path $projectRoot '.env.staging.local'
$requiredKeys = @(
    'STAGING_SSH_HOST',
    'STAGING_REPOSITORY_PATH',
    'STAGING_COMPOSE_FILE',
    'STAGING_COMPOSE_ENV_FILE',
    'STAGING_BRANCH',
    'STAGING_URL',
    'STAGING_HEALTH_URL',
    'STAGING_HEALTH_HOST'
)

function Read-EnvFile {
    param([Parameter(Mandatory)][string]$Path)

    if (-not (Test-Path -LiteralPath $Path -PathType Leaf)) {
        throw "No existe $Path. Copia .env.staging.example como .env.staging.local."
    }

    $values = @{}
    foreach ($line in Get-Content -LiteralPath $Path) {
        $trimmed = $line.Trim()
        if (-not $trimmed -or $trimmed.StartsWith('#')) {
            continue
        }

        $parts = $trimmed.Split('=', 2)
        if ($parts.Count -ne 2 -or -not $parts[0].Trim()) {
            throw "Línea no válida en ${Path}: $line"
        }

        $values[$parts[0].Trim()] = $parts[1].Trim()
    }

    return $values
}

function Assert-SafeConfigValue {
    param(
        [Parameter(Mandatory)][string]$Name,
        [Parameter(Mandatory)][string]$Value
    )

    if ($Value -notmatch '^[A-Za-z0-9_./:@-]+$') {
        throw "El valor de $Name contiene caracteres no admitidos."
    }
}

function Invoke-RemoteScript {
    param([Parameter(Mandatory)][string]$Script)

    $payload = [Convert]::ToBase64String([Text.Encoding]::UTF8.GetBytes($Script))
    & ssh `
        -o BatchMode=yes `
        -o ConnectTimeout=10 `
        -- $config.STAGING_SSH_HOST `
        "printf '%s' '$payload' | base64 -d | bash -se"

    if ($LASTEXITCODE -ne 0) {
        throw "La operación remota terminó con código $LASTEXITCODE."
    }
}

$config = Read-EnvFile -Path $envFile
foreach ($key in $requiredKeys) {
    if (-not $config.ContainsKey($key) -or -not $config[$key]) {
        throw "Falta $key en $envFile."
    }
    Assert-SafeConfigValue -Name $key -Value $config[$key]
}

$expected = if ($ExpectedSha) { $ExpectedSha.ToLowerInvariant() } else { '' }
$remotePrelude = @'
set -euo pipefail
repo='__REPO__'
compose='__COMPOSE__'
compose_env='__COMPOSE_ENV__'
branch='__BRANCH__'
url='__URL__'
health_url='__HEALTH_URL__'
health_host='__HEALTH_HOST__'
expected_sha='__EXPECTED_SHA__'
backup_root='/opt/staging/backups/web-egiakermanentzat'

test -d "$repo/.git" || { echo "No existe el repositorio: $repo" >&2; exit 20; }

assert_compose_ready() {
  test -f "$compose" || { echo "No existe el Compose: $compose" >&2; exit 21; }
  test -f "$compose_env" || { echo "No existe el archivo de entorno: $compose_env" >&2; exit 26; }
  docker compose --env-file "$compose_env" -f "$compose" config --quiet
}

compose_command() {
  docker compose --env-file "$compose_env" -f "$compose" "$@"
}

wpcli() {
  compose_command --profile tools run --rm --interactive=false -T wpcli --url="$url" "$@"
}

assert_expected_sha() {
  [ -z "$expected_sha" ] && return 0
  actual=$(git -C "$repo" rev-parse HEAD)
  case "$actual" in
    "$expected_sha"*) return 0 ;;
    *) echo "Commit remoto inesperado: $actual; se esperaba $expected_sha." >&2; exit 24 ;;
  esac
}

wait_for_wordpress() {
  for attempt in $(seq 1 18); do
    if curl -fsS -H "Host: $health_host" -H 'X-Forwarded-Proto: https' "$health_url" >/dev/null; then
      return 0
    fi
    sleep 5
  done

  compose_command ps >&2
  compose_command logs --tail 100 wordpress >&2
  echo 'WordPress no superó la comprobación de disponibilidad.' >&2
  return 1
}

verify_staging_frontend() {
  mode="${1:-full}"
  sender_enabled=$(wpcli eval 'echo \Kermanentzat\Editorial\subscription_is_configured() ? "yes" : "no";')
  routes='/ /es/'
  if [ "$mode" = 'full' ]; then
    routes="$routes /kasuaren-laburpena/ /es/resumen-del-caso/ /berriak/ /es/actualidad/ /kronologia/ /es/cronologia/ /hemeroteka/ /es/hemeroteca/ /harpidetza/ /es/suscripcion/"
  fi
  for route in $routes; do
    headers=$(mktemp)
    body=$(mktemp)
    status=$(curl -sS -D "$headers" -o "$body" -w '%{http_code}' -H "Host: $health_host" -H 'X-Forwarded-Proto: https' "${health_url%/}$route")
    if [ "$status" != '200' ]; then
      echo "$route devolvió HTTP $status; se esperaba 200." >&2
      rm -f "$headers" "$body"
      return 1
    fi
    if grep -qi '^Set-Cookie:' "$headers"; then
      echo "$route ha enviado Set-Cookie a una visita anónima." >&2
      rm -f "$headers" "$body"
      return 1
    fi
    if [ "$mode" = 'full' ]; then
      case "$route" in
        /harpidetza/|/es/suscripcion/)
          if [ "$sender_enabled" = 'yes' ]; then
            grep -Fq 'data-sender-form-id=' "$body" || { echo "$route no contiene el formulario Sender configurado." >&2; rm -f "$headers" "$body"; return 1; }
            grep -Fq 'kermanentzat-editorial/assets/subscription.js' "$body" || { echo "$route no carga el adaptador local de Sender." >&2; rm -f "$headers" "$body"; return 1; }
          elif grep -Eqi 'https://(cdn|stats)\.sender\.net' "$body"; then
            echo "$route carga Sender sin configuración completa." >&2
            rm -f "$headers" "$body"
            return 1
          fi
          ;;
        *)
          if grep -Eqi 'https://(cdn|stats)\.sender\.net' "$body"; then
            echo "$route carga directamente Sender fuera de las páginas de suscripción." >&2
            rm -f "$headers" "$body"
            return 1
          fi
          ;;
      esac
    fi
    if [ "$route" = '/' ]; then
      grep -qi '^X-Robots-Tag:.*noindex' "$headers" || { echo 'Falta X-Robots-Tag noindex.' >&2; return 1; }
      grep -qi '^Content-Security-Policy:' "$headers" || { echo 'Falta Content-Security-Policy.' >&2; return 1; }
      grep -Fq "https://$health_host" "$body" || { echo 'El frontal no genera enlaces HTTPS de staging.' >&2; return 1; }
    fi
    rm -f "$headers" "$body"
  done
  echo "Web verificada: $url"
}

verify_editorial_runtime() {
  wpcli plugin is-active kermanentzat-editorial
  wpcli kermanentzat editorial verify
  sender_state=$(wpcli eval '
    if (!\Kermanentzat\Editorial\subscription_is_approved()) { echo "sender=off"; return; }
    if (!\Kermanentzat\Editorial\subscription_is_configured()) { WP_CLI::error("Sender está aprobado pero incompleto."); }
    $group = (string) \Kermanentzat\Editorial\settings()["sender_group_id"];
    $response = \Kermanentzat\Editorial\sender_request("/groups/" . rawurlencode($group));
    if (is_wp_error($response)) { WP_CLI::error("Sender no superó la comprobación de conectividad."); }
    echo "sender=on";
  ')
  printf '%s' "$sender_state" | grep -Eq 'sender=(on|off)' || { echo 'No se pudo determinar el estado seguro de Sender.' >&2; return 1; }
  compose_command ps --status running cron | grep -Fq 'cron' || { echo 'El servicio cron no está activo.' >&2; return 1; }
  echo "Plugin editorial, $sender_state y cron verificados."
}

create_backup() {
  umask 077
  mkdir -p "$backup_root"
  created_backup_id="$(date -u +%Y%m%dT%H%M%SZ)-$(git -C "$repo" rev-parse --short=12 HEAD)"
  backup_dir="$backup_root/$created_backup_id"
  case "$backup_dir" in "$backup_root"/*) ;; *) echo 'Ruta de backup no segura.' >&2; return 1 ;; esac
  mkdir -m 700 "$backup_dir"

  compose_command exec -T db sh -c 'exec mariadb-dump --single-transaction --quick --lock-tables=false -u"$MARIADB_USER" -p"$MARIADB_PASSWORD" "$MARIADB_DATABASE"' </dev/null | gzip -9 > "$backup_dir/database.sql.gz"
  compose_command exec -T wordpress tar -C /var/www/html/wp-content -czf - uploads </dev/null > "$backup_dir/uploads.tar.gz"
  gzip -t "$backup_dir/database.sql.gz"
  tar -tzf "$backup_dir/uploads.tar.gz" >/dev/null
  test -s "$backup_dir/database.sql.gz"
  test -s "$backup_dir/uploads.tar.gz"

  restore_db="kerman_restore_$(date -u +%Y%m%d%H%M%S)_$$"
  compose_command exec -T -e RESTORE_DB="$restore_db" db sh -c 'mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" -e "CREATE DATABASE ${RESTORE_DB} CHARACTER SET utf8mb4"' </dev/null
  if ! gzip -dc "$backup_dir/database.sql.gz" | compose_command exec -T -e RESTORE_DB="$restore_db" db sh -c 'mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" "$RESTORE_DB"'; then
    compose_command exec -T -e RESTORE_DB="$restore_db" db sh -c 'mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" -e "DROP DATABASE IF EXISTS ${RESTORE_DB}"' </dev/null || true
    return 1
  fi
  table_count=$(compose_command exec -T -e RESTORE_DB="$restore_db" db sh -c 'mariadb -N -uroot -p"$MARIADB_ROOT_PASSWORD" -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema=\"${RESTORE_DB}\""' </dev/null)
  compose_command exec -T -e RESTORE_DB="$restore_db" db sh -c 'mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" -e "DROP DATABASE ${RESTORE_DB}"' </dev/null
  [ "$table_count" -gt 0 ] || { echo 'La restauración aislada no contiene tablas.' >&2; return 1; }

  sha256sum "$backup_dir/database.sql.gz" "$backup_dir/uploads.tar.gz" > "$backup_dir/SHA256SUMS"
  {
    echo "backup_id=$created_backup_id"
    echo "created_at=$(date -u +%Y-%m-%dT%H:%M:%SZ)"
    echo "commit=$(git -C "$repo" rev-parse HEAD)"
    echo "compose_sha256=$(sha256sum "$compose" | awk '{print $1}')"
    echo "restore_test_tables=$table_count"
    echo 'retention_days=14'
  } > "$backup_dir/manifest.txt"
  chmod 600 "$backup_dir"/*
  echo "Backup verificado: $created_backup_id"
}
'@

$replacements = @{
    '__REPO__' = $config.STAGING_REPOSITORY_PATH
    '__COMPOSE__' = $config.STAGING_COMPOSE_FILE
    '__COMPOSE_ENV__' = $config.STAGING_COMPOSE_ENV_FILE
    '__BRANCH__' = $config.STAGING_BRANCH
    '__URL__' = $config.STAGING_URL
    '__HEALTH_URL__' = $config.STAGING_HEALTH_URL
    '__HEALTH_HOST__' = $config.STAGING_HEALTH_HOST
    '__EXPECTED_SHA__' = $expected
}
foreach ($entry in $replacements.GetEnumerator()) {
    $remotePrelude = $remotePrelude.Replace($entry.Key, $entry.Value)
}

$cleanCheckoutGuard = @'
current_branch=$(git -C "$repo" symbolic-ref --short -q HEAD || true)
if [ "$current_branch" != "$branch" ]; then
  echo "Rama remota no válida: ${current_branch:-detached}; se esperaba $branch." >&2
  exit 22
fi
dirty=$(git -C "$repo" status --porcelain --untracked-files=all | awk '$0 != "?? .env.local"')
if [ -n "$dirty" ]; then
  echo 'El checkout remoto tiene cambios inesperados:' >&2
  printf '%s\n' "$dirty" >&2
  exit 23
fi
'@

switch ($Command) {
    'status' {
        Invoke-RemoteScript -Script ($remotePrelude + @'

echo "Rama: $(git -C "$repo" symbolic-ref --short -q HEAD || echo detached)"
echo "Commit: $(git -C "$repo" log -1 --format='%h %s')"
assert_compose_ready
compose_command ps
verify_staging_frontend basic
'@)
    }
    'pull' {
        Invoke-RemoteScript -Script ($remotePrelude + "`n" + $cleanCheckoutGuard + @'

git -C "$repo" fetch origin "$branch"
git -C "$repo" merge --ff-only "origin/$branch"
echo "Actualizado a $(git -C "$repo" rev-parse --short HEAD); no se han tocado contenedores ni base de datos."
'@)
    }
    'deploy' {
        Invoke-RemoteScript -Script ($remotePrelude + "`n" + $cleanCheckoutGuard + @'

git -C "$repo" fetch origin "$branch"
git -C "$repo" merge --ff-only "origin/$branch"
assert_expected_sha
assert_compose_ready
compose_command up -d db wordpress cron
wait_for_wordpress
wpcli core is-installed
wpcli theme activate kermanentzat-prototype
wpcli plugin activate kermanentzat-editorial
wpcli rewrite structure '/%postname%/' --hard
wpcli eval-file wp-content/themes/kermanentzat-prototype/inc/seed.php
wpcli rewrite flush --hard
wpcli kermanentzat editorial migrate --dry-run --strict
verify_editorial_runtime
verify_staging_frontend basic
compose_command ps
echo "Código desplegado en $(git -C "$repo" rev-parse --short HEAD). La migración solo se ha planificado."
'@)
    }
    'migrate' {
        Invoke-RemoteScript -Script ($remotePrelude + "`n" + $cleanCheckoutGuard + @'

assert_expected_sha
assert_compose_ready
wait_for_wordpress
created_backup_id=''
create_backup
trap 'echo "La migración falló. Backup disponible: $created_backup_id" >&2' ERR
wpcli kermanentzat editorial migrate --dry-run --strict
wpcli kermanentzat editorial migrate --strict
idempotence=$(wpcli kermanentzat editorial migrate --dry-run --strict --force)
printf '%s\n' "$idempotence"
printf '%s' "$idempotence" | grep -Fq '0 operaciones planificadas' || { echo 'La migración no es idempotente.' >&2; exit 28; }
verify_editorial_runtime
verify_staging_frontend full
trap - ERR
echo "Migración completada. Backup: $created_backup_id"
'@)
    }
    'verify' {
        Invoke-RemoteScript -Script ($remotePrelude + "`n" + $cleanCheckoutGuard + @'

assert_expected_sha
assert_compose_ready
wait_for_wordpress
verify_editorial_runtime
verify_staging_frontend full
compose_command ps
'@)
    }
    'logs' {
        $followFlag = if ($Follow) { '-f' } else { '' }
        Invoke-RemoteScript -Script ($remotePrelude + @"

assert_compose_ready
compose_command logs $followFlag --tail 200
"@)
    }
    'restart' {
        Invoke-RemoteScript -Script ($remotePrelude + @'

assert_compose_ready
compose_command restart wordpress cron
wait_for_wordpress
verify_staging_frontend basic
compose_command ps wordpress cron
'@)
    }
    'restore' {
        $restoreScript = @'

assert_compose_ready
backup_id='__BACKUP_ID__'
backup_dir="$backup_root/$backup_id"
case "$backup_dir" in "$backup_root"/*) ;; *) echo 'Ruta de restauración no segura.' >&2; exit 29 ;; esac
test -d "$backup_dir" || { echo "No existe el backup $backup_id." >&2; exit 30; }
gzip -t "$backup_dir/database.sql.gz"
tar -tzf "$backup_dir/uploads.tar.gz" >/dev/null
(cd "$backup_dir" && sha256sum -c SHA256SUMS)
wpcli maintenance-mode activate || true
if ! gzip -dc "$backup_dir/database.sql.gz" | compose_command exec -T db sh -c 'mariadb -u"$MARIADB_USER" -p"$MARIADB_PASSWORD" "$MARIADB_DATABASE"'; then
  wpcli maintenance-mode deactivate || true
  exit 31
fi
compose_command exec -T wordpress sh -c 'target=/var/www/html/wp-content/uploads; [ "$target" = /var/www/html/wp-content/uploads ] || exit 32; rm -rf -- "$target"; mkdir -p "$target"' </dev/null
gzip -dc "$backup_dir/uploads.tar.gz" | compose_command exec -T wordpress tar -C /var/www/html/wp-content -xzf -
wpcli maintenance-mode deactivate || true
wait_for_wordpress
verify_staging_frontend basic
echo "Restaurado el backup $backup_id. El código debe revertirse mediante un commit Git separado si procede."
'@
        Invoke-RemoteScript -Script ($remotePrelude + $restoreScript.Replace('__BACKUP_ID__', $BackupId))
    }
}
