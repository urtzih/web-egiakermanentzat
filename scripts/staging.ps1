[CmdletBinding()]
param(
    [Parameter(Position = 0)]
    [ValidateSet('status', 'pull', 'deploy', 'logs', 'restart')]
    [string]$Command = 'status',

    [switch]$Follow
)

$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest

if ($Follow -and $Command -ne 'logs') {
    throw '-Follow solo se puede usar con el comando logs.'
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

$repo = $config.STAGING_REPOSITORY_PATH
$compose = $config.STAGING_COMPOSE_FILE
$composeEnv = $config.STAGING_COMPOSE_ENV_FILE
$branch = $config.STAGING_BRANCH
$url = $config.STAGING_URL
$healthUrl = $config.STAGING_HEALTH_URL
$healthHost = $config.STAGING_HEALTH_HOST

$remotePrelude = @"
set -euo pipefail
repo='$repo'
compose='$compose'
compose_env='$composeEnv'
branch='$branch'
url='$url'
health_url='$healthUrl'
health_host='$healthHost'

test -d "`$repo/.git" || { echo "No existe el repositorio: `$repo" >&2; exit 20; }
test -f "`$compose" || { echo "No existe el Compose: `$compose" >&2; exit 21; }
test -f "`$compose_env" || { echo "No existe el archivo de entorno: `$compose_env" >&2; exit 26; }

compose_command() {
  docker compose --env-file "`$compose_env" -f "`$compose" "`$@"
}

wait_for_wordpress() {
  for attempt in `$(seq 1 12); do
    if curl -fsS \
      -H "Host: `$health_host" \
      -H 'X-Forwarded-Proto: https' \
      "`$health_url" >/dev/null; then
      return 0
    fi
    sleep 5
  done

  compose_command ps >&2
  compose_command logs --tail 100 wordpress >&2
  echo "WordPress no superó la comprobación de disponibilidad." >&2
  return 1
}

verify_staging_frontend() {
  headers=`$(mktemp)
  body=`$(mktemp)

  status=`$(curl -sS \
    -D "`$headers" \
    -o "`$body" \
    -w '%{http_code}' \
    -H "Host: `$health_host" \
    -H 'X-Forwarded-Proto: https' \
    "`$health_url")

  if [ "`$status" != '200' ]; then
    echo "El frontal devolvió HTTP `$status; se esperaba 200." >&2
    rm -f "`$headers" "`$body"
    return 1
  fi
  if ! grep -qi '^X-Robots-Tag:.*noindex' "`$headers"; then
    echo 'Falta X-Robots-Tag con noindex en staging.' >&2
    rm -f "`$headers" "`$body"
    return 1
  fi
  if ! grep -qi '^Content-Security-Policy:' "`$headers"; then
    echo 'Falta Content-Security-Policy en staging.' >&2
    rm -f "`$headers" "`$body"
    return 1
  fi
  if grep -qi '^Set-Cookie:' "`$headers"; then
    echo 'El frontal anónimo ha enviado Set-Cookie.' >&2
    rm -f "`$headers" "`$body"
    return 1
  fi
  if ! grep -Fq "https://`$health_host" "`$body"; then
    echo 'El frontal no contiene enlaces HTTPS con el hostname de staging.' >&2
    rm -f "`$headers" "`$body"
    return 1
  fi

  rm -f "`$headers" "`$body"
  echo "Web: 200 (`$url)"
  echo 'Privacidad: noindex, CSP y respuesta anónima sin Set-Cookie'
}
"@

$cleanCheckoutGuard = @'
current_branch=$(git -C "$repo" symbolic-ref --short -q HEAD || true)
if [ "$current_branch" != "$branch" ]; then
  echo "Rama remota no válida: ${current_branch:-detached}; se esperaba $branch." >&2
  exit 22
fi

# .env.local es un archivo operativo legado del servidor. Se permite de forma
# explícita durante el primer pull; cualquier otro cambio bloquea la operación.
dirty=$(git -C "$repo" status --porcelain --untracked-files=all | awk '$0 != "?? .env.local"')
if [ -n "$dirty" ]; then
  echo "El checkout remoto tiene cambios inesperados y no se modificará:" >&2
  printf '%s\n' "$dirty" >&2
  exit 23
fi
'@

switch ($Command) {
    'status' {
        Invoke-RemoteScript -Script ($remotePrelude + @'

echo "Rama: $(git -C "$repo" symbolic-ref --short -q HEAD || echo detached)"
echo "Commit: $(git -C "$repo" log -1 --format='%h %s')"
dirty=$(git -C "$repo" status --porcelain --untracked-files=all | awk '$0 != "?? .env.local"')
if [ -n "$dirty" ]; then
  echo 'Checkout: con cambios inesperados'
  printf '%s\n' "$dirty"
else
  echo 'Checkout: limpio (se admite .env.local operativo)'
fi
echo
compose_command ps
echo
verify_staging_frontend
'@)
    }
    'pull' {
        Invoke-RemoteScript -Script ($remotePrelude + "`n" + $cleanCheckoutGuard + "`n" + @'

git -C "$repo" fetch origin "$branch"
git -C "$repo" merge --ff-only "origin/$branch"
echo "Actualizado a $(git -C "$repo" rev-parse --short HEAD); no se han tocado contenedores ni base de datos."
'@)
    }
    'deploy' {
        Invoke-RemoteScript -Script ($remotePrelude + "`n" + $cleanCheckoutGuard + "`n" + @'

git -C "$repo" fetch origin "$branch"
git -C "$repo" merge --ff-only "origin/$branch"

compose_command up -d db wordpress
wait_for_wordpress

if ! compose_command --profile tools run --rm -T wpcli core is-installed; then
  echo 'WordPress no está instalado. Ejecuta la inicialización documentada antes de desplegar.' >&2
  exit 27
fi

compose_command --profile tools run --rm -T wpcli theme activate kermanentzat-prototype
compose_command --profile tools run --rm -T wpcli rewrite structure '/%postname%/' --hard
compose_command --profile tools run --rm -T wpcli eval-file wp-content/themes/kermanentzat-prototype/inc/seed.php
compose_command --profile tools run --rm -T wpcli rewrite flush --hard

wait_for_wordpress
verify_staging_frontend
compose_command ps
echo "Desplegado $(git -C "$repo" rev-parse --short HEAD) y sincronizado el contenido bilingüe."
'@)
    }
    'logs' {
        $followFlag = if ($Follow) { '-f' } else { '' }
        Invoke-RemoteScript -Script ($remotePrelude + @"

compose_command logs $followFlag --tail 200
"@)
    }
    'restart' {
        Invoke-RemoteScript -Script ($remotePrelude + @'

compose_command restart wordpress
wait_for_wordpress
verify_staging_frontend
compose_command ps wordpress
'@)
    }
}
