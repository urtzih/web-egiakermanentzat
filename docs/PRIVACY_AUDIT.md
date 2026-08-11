# Auditoría de privacidad

Fecha técnica: 2026-08-11. Alcance: tema `kermanentzat-prototype`, plugin editorial, MU-plugin, contenido sincronizado, suscripciones, sitemaps y navegación pública.

## Resultado ejecutivo

Google Analytics 4 queda preparado como servicio opcional, desactivado por defecto y protegido por tres condiciones: entorno de producción, identificador válido y aprobación operativa explícita. Aun cumpliéndose, el navegador no descarga la etiqueta ni contacta con Google hasta recibir consentimiento analítico afirmativo.

El rechazo conserva únicamente una preferencia versionada durante seis meses. La retirada deshabilita Analytics, elimina las cookies `_ga`/`_ga_*` y recarga la página para impedir solicitudes posteriores. Aceptar y rechazar tienen la misma jerarquía visual; el control permanente del pie permite revisar la elección.

Sender está activado en el entorno local mediante aprobación explícita, secreto, grupo, remitente y formulario bilingüe con double opt-in. El SDK y el formulario se cargan automáticamente solo en las rutas específicas de suscripción; WordPress no conserva emails. La prueba real de alta/baja y la evidencia documental de DPA, transferencias y conservación siguen pendientes antes de producción.

Las rutas `/harpidetza/` y `/es/suscripcion/` integran directamente el formulario cuando Sender está configurado. Actualidad/Berriak y Contacto/Kontaktua muestran llamadas compactas hacia esas rutas y no cargan recursos del proveedor. Con Sender desactivado, todas mantienen una salida local informativa.

## Arquitectura

- `kermanentzat_legal_config()` mantiene identidad y pendientes documentales.
- `kermanentzat_service_registry()` usa la versión `3.2.0`; registra GA4 solo con sus tres condiciones y Sender solo cuando el adaptador está aprobado y completamente configurado.
- `assets/js/consent.js` es el único adaptador autorizado para almacenamiento, cookies y carga de Google.
- Consent Mode v2 parte de analítica y publicidad denegadas. Google Signals y personalización publicitaria permanecen desactivados.
- La CSP solo amplía `script-src`, `img-src` y `connect-src` cuando el servicio está activo.
- `inc/legal-content.php` sigue siendo la fuente bilingüe que consume el seed.
- La primera capa resume la finalidad general de la analítica sin enumerar eventos bancarios; el detalle permanece en la política y en el panel de preferencias. Aceptar y rechazar conservan igual visibilidad y facilidad de uso.
- El registro cambia a `3.2.0` porque el formulario único bilingüe se carga directamente solo en la ruta específica y las páginas secundarias pasan a utilizar llamadas locales sin SDK.
- La página de ayuda ofrece el correo público existente para comunicar posibles agresiones relacionadas con el entorno de Mitika, y la página de contacto incluye una referencia breve que enlaza directamente a esa orientación en el idioma correspondiente. No se incorporan formularios ni nuevos destinatarios y se advierte que la documentación sensible debe compartirse solo después de acordar un canal adecuado; no se publica una garantía absoluta de confidencialidad que el correo ordinario no pueda acreditar.
- La invitación para hacerse socio/a se limita a un primer correo que expresa interés. No pide nombre, apellidos, DNI/NAN, teléfono ni documentación; ese flujo no añade por sí mismo proveedores ni almacenamiento. La versión `3.2.0` responde a la arquitectura de suscripción condicionada a Sender, no al alta de socios.

## Tratamientos

| Actividad | Datos mínimos | Finalidad/base | Destinatarios | Conservación |
|---|---|---|---|---|
| Consultas por correo | Remitente, contenido y metadatos | Responder; medidas solicitadas/interés legítimo según asunto | Correo y personas autorizadas | Necesidad y posibles responsabilidades |
| Interés inicial en hacerse socio/a | Remitente, expresión de interés y metadatos del mensaje | Informar del procedimiento y atender la solicitud inicial | Correo y personas autorizadas | Hasta responder y durante el tiempo necesario para posibles responsabilidades |
| Transferencias | Ordenante, operación, importe, concepto e incidencias | Gestión, contabilidad y obligaciones legales | Banco, asesoría y autoridades cuando proceda | Plazos legales aplicables |
| Analytics aceptado | Navegación, fuente/campaña, país aproximado, idioma, interacción y eventos sin contenido bancario | Estadística agregada; consentimiento | Google Ireland Limited | GA4: 2 meses; preferencia/cookies: hasta 6 meses |
| Logs de hosting | IP, fecha, recurso y agente mínimo | Seguridad/disponibilidad; interés legítimo | Hosting y soporte autorizado | Pendiente del contrato |
| Administración | Identificador, rol, autenticación y eventos técnicos | Mantenimiento seguro | Personas administradoras y hosting | Mientras exista autorización y necesidad |
| Suscripción confirmada | Email y estados de confirmación, entrega y baja | Avisos editoriales; consentimiento | UAB Sender.lt y personas autorizadas en su cuenta | Hasta la baja; supresión posterior pendiente de contrato validado |

No hay formulario general de contacto, cuentas públicas, CAPTCHA, pasarela, perfiles publicitarios, Google Ads, User-ID ni decisiones automatizadas. El único formulario externo previsto es la suscripción, desactivada hasta completar las condiciones indicadas.

## Datos bancarios y eventos

`copy_iban` y `copy_bank_details` se emiten solo después de una escritura correcta en el portapapeles y si Analytics ya está activo. El adaptador acepta exclusivamente esos dos nombres. El valor de `data-copy-value`, el IBAN y el bloque copiado nunca se pasan a `gtag`.

## Indexación

`/sitemap.xml` enlaza `/sitemap-eu.xml` y `/sitemap-es.xml`. Cada hijo contiene ocho páginas publicadas con origen fijo `https://egiakermanentzat.eus`, HTTPS y `lastmod`, incluidas las rutas de actualidad desde que contienen cobertura real; no incluye autores, adjuntos, búsquedas ni administración. `robots.txt` declara el índice y conserva el bloqueo administrativo.

## Riesgos y validaciones pendientes

- No activar `KERMANENTZAT_GA_APPROVED` hasta aceptar y archivar las condiciones de tratamiento de Google, validar las garantías de transferencia y confirmar los ajustes de la propiedad.
- No activar `KERMANENTZAT_SENDER_APPROVED` hasta disponer de cuenta institucional, DPA y subencargados revisados, garantías de transferencia, criterio de conservación y supresión, dominio autenticado, formulario bilingüe con double opt-in y prueba humana de alta y baja.
- Revisar el Excel original y excluir toda dirección sin fecha, origen y alcance de consentimiento demostrables; aplicar siempre las supresiones antes de importar.
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
| Inventario y políticas versión `3.2.0` | Implementado; revisión jurídica/lingüística pendiente |
| Eventos sin valores bancarios | Implementado y comprobado estáticamente |
| Sitemaps bilingües y robots | Implementado |
| Cuenta GA4, contrato y garantías | Pendiente de la asociación |
| Search Console y DNS | Pendiente del despliegue público |
| Primer contacto para hacerse socio/a sin DNI/NAN | Implementado; procedimiento completo de alta pendiente |
| Sender bloqueado por configuración y aprobación | Implementado; cuenta y validaciones externas pendientes |
| Alta, double opt-in, baja y campaña real | Pendiente de staging con cuenta institucional |
