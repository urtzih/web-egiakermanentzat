# Operación segura del entorno de staging

Staging reproduce el WordPress editorial sin reemplazar su base de datos ni sus medios.

- URL pública: <https://web-egiakermanentzat.stag.urtzi.fun/>
- URL LAN: <http://192.168.10.42:18081/>
- Host SSH: `lxc-apps-staging`
- Repositorio: `/opt/staging/projects/web-egiakermanentzat/repository`
- Compose versionado: `repository/compose.staging.yml`
- Secretos: `/opt/staging/projects/web-egiakermanentzat/.env.staging`
- Backups: `/opt/staging/backups/web-egiakermanentzat/`
- Rama desplegable: `main`

## Configuración local

```powershell
Copy-Item .env.staging.example .env.staging.local
```

`.env.staging.local` solo contiene host, rutas, URL y rama. Las credenciales de WordPress, MariaDB y Sender permanecen en el archivo remoto con permisos restringidos.

El Compose usa explícitamente `name: web-egiakermanentzat`; cambiar ese nombre crearía otros volúmenes y dejaría de utilizar los datos actuales.

## Flujo normal

```powershell
# Lectura: Git, contenedores y comprobación pública básica
.\scripts\staging.ps1 status

# Traer origin/main sin tocar contenedores ni base de datos
.\scripts\staging.ps1 pull

# Desplegar código exacto y mostrar el plan de migración, sin aplicarlo
.\scripts\staging.ps1 deploy -ExpectedSha <sha>

# Crear y probar backup, aplicar la migración y comprobar idempotencia
.\scripts\staging.ps1 migrate -ExpectedSha <sha>

# Repetir todas las comprobaciones del runtime y del frontal
.\scripts\staging.ps1 verify -ExpectedSha <sha>

# Consultar o seguir logs
.\scripts\staging.ps1 logs
.\scripts\staging.ps1 logs -Follow
```

`deploy` activa tema y plugin, ejecuta el seed no destructivo y termina con `wp kermanentzat editorial migrate --dry-run --strict`. El contenido solo cambia al ejecutar `migrate`.

`migrate` crea antes un backup SQL y de `uploads`, verifica ambos archivos y restaura el SQL en una base temporal aislada. Solo después aplica la migración. La segunda planificación forzada debe informar `0 operaciones planificadas`.

## Variables remotas

El archivo `.env.staging` debe contener las credenciales existentes y:

```dotenv
KERMANENTZAT_GA_MEASUREMENT_ID=
KERMANENTZAT_GA_APPROVED=false
KERMANENTZAT_SENDER_APPROVED=false
KERMANENTZAT_SENDER_API_TOKEN=<secreto del gestor operativo>
```

No usar el alias `SENDER_API_TOK` en configuraciones nuevas. Sender debe permanecer apagado hasta archivar el expediente contractual, transferencias, conservación, DNS, double opt-in y revisión bilingüe.

## Verificación

El script exige:

- checkout limpio, rama `main` y SHA esperado;
- WordPress, MariaDB y cron saludables;
- plugin editorial activo y su comando de verificación correcto;
- Sender desactivado;
- rutas estructurales ES/EU con HTTP 200;
- `noindex`, CSP, HTTPS y ausencia de `Set-Cookie` anónimo;
- ausencia de recursos Sender en el HTML mientras el servicio esté apagado.

Después se realiza la revisión manual indicada en `docs/EDITORIAL_OPERATIONS.md` y en el manual administrativo.

## Backups y retención

Cada copia tiene un identificador `<UTC>-<sha>`, permisos privados, `database.sql.gz`, `uploads.tar.gz`, hashes y manifiesto. Se conserva durante 14 días y se elimina manualmente solo después de confirmar que ya no es necesaria para rollback.

Para listar copias sin mostrar su contenido:

```bash
ssh lxc-apps-staging "find /opt/staging/backups/web-egiakermanentzat -mindepth 1 -maxdepth 1 -type d -printf '%f\n' | sort"
```

## Restauración

Primero se revisa el identificador y el commit descrito en su manifiesto. La restauración de base y medios requiere dos argumentos deliberados:

```powershell
.\scripts\staging.ps1 restore -BackupId <id> -ConfirmRestore
```

El comando activa mantenimiento, verifica hashes, importa el SQL y sustituye únicamente `/var/www/html/wp-content/uploads`. No cambia Git. Si también se debe volver al código anterior, se crea y despliega un commit de reversión en `main`; no se usa `git reset --hard`.

No ejecutar `docker compose down -v`, borrar volúmenes, editar la base manualmente ni usar el seed como restauración.
