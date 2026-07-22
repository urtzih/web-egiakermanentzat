# Egia Kermanentzat — entorno local

Versión final local del MVP bilingüe de Egia Kermanentzat Elkartea. Utiliza WordPress 7.0.2 y MariaDB en Docker. La web permite consultar el caso, copiar los datos bancarios verificados y abrir los canales oficiales de contacto.

## Arranque

1. Abrir Docker Desktop.
2. Ejecutar `docker compose up -d`.
3. Ejecutar `powershell -ExecutionPolicy Bypass -File scripts/setup-local.ps1` la primera vez o para sincronizar el contenido del MVP.
4. Abrir `http://localhost:8082/` para euskera o `http://localhost:8082/es/` para castellano.

El panel queda en `http://localhost:8082/wp-admin/`. Las credenciales de desarrollo están en `.env`, archivo ignorado por Git. Para detener el entorno: `docker compose down`. No usar `docker compose down -v` salvo que se quiera borrar deliberadamente la base de datos local.

## Skills y calidad web

Las skills compartidas del proyecto viven únicamente en `.agents/skills`, que
también utiliza Codex. Para actualizar los workflows generados de OpenSpec sin
dejar copias en `.codex/skills`, ejecutar:

```powershell
powershell -ExecutionPolicy Bypass -File scripts/update-openspec-skills.ps1
```

El procedimiento de SEO, rendimiento, accesibilidad y auditoría está en
[`docs/quality/web-quality-runbook.md`](docs/quality/web-quality-runbook.md).

## Entorno y publicación

- La raíz representa `egiakermanentzat.eus` en euskera; el castellano vive bajo `/es/`.
- El entorno local lleva `X-Robots-Tag: noindex`, bloquea correo e integraciones y solo escucha en `127.0.0.1`.
- En `production` se desactivan únicamente los bloqueos de indexación, correo e integraciones; las cabeceras de seguridad del frontal se mantienen.
- La transferencia publica únicamente titular, IBAN, BIC y concepto; el certificado bancario no forma parte del sitio.
- Antes del despliegue aún deben configurarse hosting, dominio, HTTPS, copias y correo operativo, y validarse jurídicamente los textos legales bilingües.

## Privacidad y mantenimiento legal

La navegación pública anónima no crea cookies ni usa almacenamiento del navegador. Para sincronizar las seis páginas legales y el contenido de aportaciones, ejecutar `scripts/setup-local.ps1`. La auditoría, el inventario y los datos pendientes están en `docs/PRIVACY_AUDIT.md`, `docs/COOKIE_INVENTORY.md` y `docs/LEGAL_INFORMATION_REQUIRED.md`.

Después de cualquier cambio relacionado con servicios, scripts, embeds, formularios, hosting o terceros, actualizar primero el inventario y la versión del registro y ejecutar:

```powershell
powershell -ExecutionPolicy Bypass -File scripts/test-privacy.ps1
```

No registrar ni cargar un servicio opcional sin definir su adaptador, base jurídica, textos bilingües y pruebas. Mientras el registro opcional esté vacío no se renderizan banner, panel ni almacenamiento de consentimiento.
