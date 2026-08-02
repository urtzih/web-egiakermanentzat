# Auditoría de privacidad

Fecha técnica: 2026-08-02. Alcance: tema `kermanentzat-prototype`, MU-plugin, contenido sincronizado, sitemaps y navegación pública.

## Resultado ejecutivo

Google Analytics 4 queda preparado como servicio opcional, desactivado por defecto y protegido por tres condiciones: entorno de producción, identificador válido y aprobación operativa explícita. Aun cumpliéndose, el navegador no descarga la etiqueta ni contacta con Google hasta recibir consentimiento analítico afirmativo.

El rechazo conserva únicamente una preferencia versionada durante seis meses. La retirada deshabilita Analytics, elimina las cookies `_ga`/`_ga_*` y recarga la página para impedir solicitudes posteriores. Aceptar y rechazar tienen la misma jerarquía visual; el control permanente del pie permite revisar la elección.

## Arquitectura

- `kermanentzat_legal_config()` mantiene identidad y pendientes documentales.
- `kermanentzat_service_registry()` usa la versión `2.0.0` y registra GA4 solo cuando `KERMANENTZAT_GA_MEASUREMENT_ID`, `KERMANENTZAT_GA_APPROVED=true` y producción coinciden.
- `assets/js/consent.js` es el único adaptador autorizado para almacenamiento, cookies y carga de Google.
- Consent Mode v2 parte de analítica y publicidad denegadas. Google Signals y personalización publicitaria permanecen desactivados.
- La CSP solo amplía `script-src`, `img-src` y `connect-src` cuando el servicio está activo.
- `inc/legal-content.php` sigue siendo la fuente bilingüe que consume el seed.
- La primera capa resume la finalidad general de la analítica sin enumerar eventos bancarios; el detalle permanece en la política y en el panel de preferencias. Aceptar y rechazar conservan igual visibilidad y facilidad de uso.
- El registro permanece en `2.0.0`: el ajuste de texto y composición no cambia finalidades, proveedor, almacenamiento ni condiciones de activación, por lo que no obliga a solicitar de nuevo una elección ya válida.
- La página de ayuda ofrece el correo público existente para comunicar posibles agresiones relacionadas con el entorno de Mitika, y la página de contacto incluye una referencia breve que enlaza directamente a esa orientación en el idioma correspondiente. No se incorporan formularios ni nuevos destinatarios y se advierte que la documentación sensible debe compartirse solo después de acordar un canal adecuado; no se publica una garantía absoluta de confidencialidad que el correo ordinario no pueda acreditar.
- La invitación para hacerse socio/a se limita a un primer correo que expresa interés. No pide nombre, apellidos, DNI/NAN, teléfono ni documentación; antes de recabar otros datos, la asociación deberá facilitar el procedimiento y la información de privacidad aplicable. Este cambio no añade proveedores, almacenamiento ni finalidades analíticas, por lo que el registro de consentimiento permanece en `2.0.0`.

## Tratamientos

| Actividad | Datos mínimos | Finalidad/base | Destinatarios | Conservación |
|---|---|---|---|---|
| Consultas por correo | Remitente, contenido y metadatos | Responder; medidas solicitadas/interés legítimo según asunto | Correo y personas autorizadas | Necesidad y posibles responsabilidades |
| Interés inicial en hacerse socio/a | Remitente, expresión de interés y metadatos del mensaje | Informar del procedimiento y atender la solicitud inicial | Correo y personas autorizadas | Hasta responder y durante el tiempo necesario para posibles responsabilidades |
| Transferencias | Ordenante, operación, importe, concepto e incidencias | Gestión, contabilidad y obligaciones legales | Banco, asesoría y autoridades cuando proceda | Plazos legales aplicables |
| Analytics aceptado | Navegación, fuente/campaña, país aproximado, idioma, interacción y eventos sin contenido bancario | Estadística agregada; consentimiento | Google Ireland Limited | GA4: 2 meses; preferencia/cookies: hasta 6 meses |
| Logs de hosting | IP, fecha, recurso y agente mínimo | Seguridad/disponibilidad; interés legítimo | Hosting y soporte autorizado | Pendiente del contrato |
| Administración | Identificador, rol, autenticación y eventos técnicos | Mantenimiento seguro | Personas administradoras y hosting | Mientras exista autorización y necesidad |

No hay formularios, cuentas públicas, newsletter, CAPTCHA, pasarela, perfiles publicitarios, Google Ads, User-ID ni decisiones automatizadas.

## Datos bancarios y eventos

`copy_iban` y `copy_bank_details` se emiten solo después de una escritura correcta en el portapapeles y si Analytics ya está activo. El adaptador acepta exclusivamente esos dos nombres. El valor de `data-copy-value`, el IBAN y el bloque copiado nunca se pasan a `gtag`.

## Indexación

`/sitemap.xml` enlaza `/sitemap-eu.xml` y `/sitemap-es.xml`. Cada hijo contiene siete páginas publicadas con origen fijo `https://egiakermanentzat.eus`, HTTPS y `lastmod`; no incluye autores, adjuntos, búsquedas ni administración. `robots.txt` declara el índice y conserva el bloqueo administrativo.

## Riesgos y validaciones pendientes

- No activar `KERMANENTZAT_GA_APPROVED` hasta aceptar y archivar las condiciones de tratamiento de Google, validar las garantías de transferencia y confirmar los ajustes de la propiedad.
- Someter los textos a revisión jurídica española y lingüística profesional en euskera.
- Validar hosting, Gmail, logs, responsables operativos y HSTS.
- Definir y validar el procedimiento completo de alta de socios: órgano de admisión, datos estrictamente necesarios, base jurídica, destinatarios, conservación, accesos y canal para facilitar documentación.
- No publicar el número registral hasta disponer de evidencia oficial.
- Confirmar con asesoría la situación fiscal antes de anunciar deducciones.
- Repetir con lector de pantalla real, zoom 200 %, teclado, móvil, escritorio y movimiento reducido.

Este documento describe controles técnicos y no declara cumplimiento jurídico integral.

## Estado de requisitos

| Requisito | Estado |
|---|---|
| Analytics bloqueado antes del consentimiento | Implementado; prueba de producción pendiente |
| Rechazo y retirada sin solicitudes futuras | Implementado; auditoría de red de producción pendiente |
| Banner y preferencias ES/EU | Implementado; revisión humana pendiente |
| Inventario y políticas versión `2.0.0` | Implementado |
| Eventos sin valores bancarios | Implementado y comprobado estáticamente |
| Sitemaps bilingües y robots | Implementado |
| Cuenta GA4, contrato y garantías | Pendiente de la asociación |
| Search Console y DNS | Pendiente del despliegue público |
| Primer contacto para hacerse socio/a sin DNI/NAN | Implementado; procedimiento completo de alta pendiente |
