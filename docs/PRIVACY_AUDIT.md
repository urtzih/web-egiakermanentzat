# Auditoría de privacidad

Fecha técnica: 2026-07-22. Alcance: tema `kermanentzat-prototype`, MU-plugin, contenido sincronizado y navegación pública anónima. Este documento describe la implementación técnica; no sustituye una revisión jurídica.

## Resultado ejecutivo

La web pública no utiliza cookies, almacenamiento del navegador, analítica, píxeles, iframes, formularios ni recursos automáticos de terceros. No procede mostrar un banner sin finalidades opcionales. Las cookies técnicas de WordPress están limitadas al acceso administrativo solicitado por personas autorizadas.

La identidad publicable se centraliza en `inc/privacy.php`. El número registral permanece vacío hasta recibir resolución o certificado oficial. El identificador no acreditado que figuraba en cuatro documentos fue introducido por el commit `228570f`; se ha retirado por falta de evidencia suficiente.

## Arquitectura

- `kermanentzat_legal_config()` concentra identidad, versión y pendientes verificables.
- `kermanentzat_service_registry()` declara `necessary` como no configurable, tres categorías opcionales y versión `1.0.0`.
- `kermanentzat_optional_services` es el único punto de registro de adaptadores futuros. La entrada exige id, categoría válida y activación explícita.
- Mientras la lista opcional esté vacía, no se ejecuta el hook de controles, ni existe banner, panel o almacenamiento de preferencias.
- `inc/legal-content.php` es la fuente bilingüe que consume el seed idempotente.
- El MU-plugin aplica CSP y cabeceras defensivas al frontal en cualquier entorno; correo, indexación e integraciones se bloquean solo fuera de producción.

## Tratamientos observados

| Actividad | Datos mínimos | Finalidad | Base prevista | Destinatarios | Conservación |
|---|---|---|---|---|---|
| Consultas voluntarias por correo | Remitente, contenido y metadatos del mensaje | Responder y documentar seguimiento | Medidas solicitadas e interés legítimo según el asunto | Proveedor de correo y personas autorizadas | Mientras sea necesario y para posibles responsabilidades |
| Transferencias bancarias | Ordenante, cuenta/operación, importe, concepto y datos de incidencia | Contabilidad, obligaciones fiscales/documentales, justificantes, incidencias y devoluciones | Obligación legal, gestión de la aportación e interés legítimo antifraude | Banco, asesoría y administraciones cuando proceda | Plazos legales contables/fiscales aplicables, pendientes de validación concreta |
| Logs del futuro hosting | IP, fecha, recurso, agente y diagnóstico mínimo | Seguridad, disponibilidad y resolución de fallos | Interés legítimo | Proveedor de hosting y soporte autorizado | Mínimo contractual aún pendiente |
| Administración WordPress | Identificador, rol, autenticación y eventos técnicos | Publicación y mantenimiento seguro | Interés legítimo y relación organizativa/contractual | Personas administradoras y hosting | Mientras exista autorización y el mínimo necesario para seguridad |

No existen decisiones automatizadas, perfiles, cuentas públicas, formularios, newsletter, CAPTCHA, pasarela, plataforma de donación ni analítica.

## Recursos y terceros

Los recursos de carga automática son del mismo origen. Instagram, saretu.es y el correo son enlaces activados por la persona visitante. No se contacta con esos proveedores durante la carga. Gmail y el futuro hosting deberán documentarse como encargados o proveedores según el servicio y contrato real.

## Cabeceras

El frontal sirve `Content-Security-Policy`, `X-Content-Type-Options: nosniff`, `Referrer-Policy: no-referrer`, `Permissions-Policy` y `X-Frame-Options: DENY`. La CSP limita recursos al origen propio, bloquea objetos y frames y restringe conexiones. Se admite código inline porque WordPress lo genera actualmente; retirar esa excepción exige nonce/hash y una regresión separada. HSTS queda pendiente del hosting HTTPS definitivo.

## Riesgos y mantenimiento

- No publicar el número registral hasta comprobar evidencia oficial.
- Validar hosting, contrato de Gmail, transferencias internacionales, conservación y responsables operativos.
- Confirmar con asesoría si la asociación cumple la Ley 49/2002 antes de anunciar deducciones.
- Someter los textos a revisión jurídica y lingüística profesional en euskera.
- Antes de registrar cualquier servicio opcional: actualizar inventario, finalidad/base, adaptador, versión, textos y pruebas; mantenerlo bloqueado hasta consentimiento afirmativo cuando corresponda.

## Requisitos finales

| Requisito | Estado | Evidencia | Acción pendiente |
|---|---|---|---|
| Frontal sin cookies ni almacenamiento | completado | Registro vacío y prueba automatizada | Repetir en cada despliegue |
| Banner de consentimiento | no aplicable | No hay servicios opcionales | Implementar solo si cambia el inventario |
| Seis páginas legales bilingües | completado | Seed, rutas y pie | Revisión jurídica/lingüística |
| Identidad legal verificada | completado | Nombre, NIF, domicilio y correo centralizados | Incorporar registro solo con certificado |
| Tratamientos reales documentados | completado | Política e inventario | Validar contratos y retención |
| Donaciones transparentes | completado | Dos páginas de ayuda y privacidad | Confirmar fiscalidad Ley 49/2002 |
| Cabeceras defensivas | completado | MU-plugin y prueba HTTP | Valorar HSTS en producción HTTPS |
| Analytics | no aplicable | Sin servicio, ID, etiqueta o solicitud | Seguir guía futura si se aprueba |
| Hosting y Gmail | pendiente | Campos centrales nulos | Revisar contrato, región y garantías |
| Validación jurídica y euskera | pendiente | Avisos visibles en los textos | Revisión humana cualificada |

