# Operación del entorno de staging

Este documento explica cómo consultar, actualizar y desplegar
`web-egiakermanentzat` en el servidor de staging.

- URL pública: <https://web-egiakermanentzat.stag.urtzi.fun/>
- Acceso: Basic Auth gestionado por Traefik
- URL LAN: <http://192.168.10.42:18081/>
- Host SSH: `lxc-apps-staging`
- Repositorio: `/opt/staging/projects/web-egiakermanentzat/repository`
- Compose: `/opt/staging/projects/web-egiakermanentzat/compose.staging.yml`
- Entorno Compose: `/opt/staging/projects/web-egiakermanentzat/.env.staging`
- Rama desplegable: `main`

## Configuración local

El script lee `.env.staging.local`. El archivo está ignorado por Git y solo
contiene rutas y nombres operativos; las claves SSH y credenciales de WordPress
o MariaDB no se guardan en él.

Para reconstruirlo:

```powershell
Copy-Item .env.staging.example .env.staging.local
```

La conexión utiliza el alias `lxc-apps-staging` definido en `~/.ssh/config`.

## Comandos

Ejecutar desde la raíz del repositorio:

```powershell
# Git remoto, contenedores, HTTP, noindex, CSP y ausencia de Set-Cookie
.\scripts\staging.ps1 status

# Traer origin/main sin tocar contenedores ni base de datos
.\scripts\staging.ps1 pull

# Actualizar Git, levantar servicios y sincronizar el contenido bilingüe
.\scripts\staging.ps1 deploy

# Últimas 200 líneas de log de los servicios
.\scripts\staging.ps1 logs

# Seguir los logs; Ctrl+C termina el seguimiento
.\scripts\staging.ps1 logs -Follow

# Reiniciar únicamente WordPress y comprobar que recupera la salud
.\scripts\staging.ps1 restart
```

`status` y `logs` son operaciones de lectura. `pull`, `deploy` y `restart`
modifican staging y solo deben ejecutarse por instrucción expresa.

## Flujo de despliegue

1. Confirmar que los cambios están revisados y publicados en `origin/main`.
2. Ejecutar las pruebas locales y `scripts/test-privacy.ps1`.
3. Ejecutar `.\scripts\staging.ps1 status`.
4. Ejecutar `.\scripts\staging.ps1 deploy`.
5. Repetir `status` y validar `/`, `/es/` y las páginas legales.
6. Informar del commit desplegado y del resultado de las comprobaciones.

El despliegue:

- rechaza ramas distintas de `main`, cambios versionados y archivos inesperados;
- permite únicamente el `.env.local` operativo legado durante el primer `pull`;
- exige un avance `fast-forward` desde `origin/main`;
- usa siempre el `.env.staging` remoto al invocar Compose;
- conserva los volúmenes de MariaDB y WordPress;
- exige que WordPress ya esté instalado;
- activa el tema, aplica el seed idempotente y regenera los enlaces permanentes;
- comprueba el frontal con el hostname público y protocolo reenviado HTTPS.

La comprobación se hace directamente contra el puerto LAN para no almacenar ni
transmitir en el script las credenciales de Basic Auth de Traefik. Una respuesta
`401` al abrir el dominio público sin credenciales es el comportamiento esperado.

## Comprobaciones manuales

```bash
git -C /opt/staging/projects/web-egiakermanentzat/repository status --short --branch

docker compose \
  --env-file /opt/staging/projects/web-egiakermanentzat/.env.staging \
  -f /opt/staging/projects/web-egiakermanentzat/compose.staging.yml \
  ps

curl -fsS -D - -o /dev/null \
  -H 'Host: web-egiakermanentzat.stag.urtzi.fun' \
  -H 'X-Forwarded-Proto: https' \
  http://192.168.10.42:18081/
```

## Persistencia y recuperación

La base de datos y la instalación de WordPress viven en volúmenes Docker. El
script no ejecuta `down`, no usa `-v` y no elimina volúmenes. El seed actualiza
las páginas gestionadas por el repositorio, por lo que cualquier edición manual
de esas páginas en staging será sustituida en el siguiente `deploy`.

Antes de una intervención manual o una migración se debe conservar:

- commit actualmente desplegado;
- volcado de MariaDB;
- copia de los volúmenes o del contenido necesario;
- `compose.staging.yml` y una copia protegida de `.env.staging`.

Si una sincronización falla, conservar los logs y el estado antes de intervenir.
No ejecutar `git reset --hard`, borrar el checkout, eliminar volúmenes ni cambiar
Traefik, DNS o certificados sin una instrucción específica y una copia
recuperable.
