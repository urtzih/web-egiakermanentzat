# Egia Kermanentzat — entorno local

Versión final local del MVP bilingüe de Egia Kermanentzat Elkartea. Utiliza WordPress 7.0.2 y MariaDB en Docker. La web permite consultar el caso, copiar los datos bancarios verificados y abrir los canales oficiales de contacto.

## Arranque

1. Abrir Docker Desktop.
2. Ejecutar `docker compose up -d`.
3. Ejecutar `powershell -ExecutionPolicy Bypass -File scripts/setup-local.ps1` la primera vez o para sincronizar el contenido del MVP.
4. Abrir `http://localhost:8082/` para euskera o `http://localhost:8082/es/` para castellano.

El panel queda en `http://localhost:8082/wp-admin/`. Las credenciales de desarrollo están en `.env`, archivo ignorado por Git. Para detener el entorno: `docker compose down`. No usar `docker compose down -v` salvo que se quiera borrar deliberadamente la base de datos local.

## Entorno y publicación

- La raíz representa `egiakermanentzat.eus` en euskera; el castellano vive bajo `/es/`.
- El entorno local lleva `X-Robots-Tag: noindex`, bloquea correo e integraciones y solo escucha en `127.0.0.1`.
- Estas protecciones se desactivan cuando WordPress se configura explícitamente como `production`.
- La transferencia publica únicamente titular, IBAN, BIC y concepto; el certificado bancario no forma parte del sitio.
- Antes del despliegue aún deben configurarse hosting, dominio, HTTPS, copias, correo operativo y textos legales.
