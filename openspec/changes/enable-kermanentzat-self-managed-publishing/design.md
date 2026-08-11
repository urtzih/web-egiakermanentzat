## Context

El tema clásico `kermanentzat-prototype` concentra actualmente contenido, rutas, metadatos, sitemaps y privacidad. `inc/seed.php` guarda cada página como un bloque `core/html` y la actualiza en cada ejecución, de modo que una edición desde WordPress puede perderse. No existen tipos editoriales ni plugins ordinarios; el frontal y el MU-plugin de protección ya están operativos y deben preservarse.

La asociación trabajará normalmente con una sola editora, publicará en EU o ES sin esperar siempre a la otra lengua y conservará aprobaciones sensibles fuera del CMS. Las suscripciones arrancarán previsiblemente con 501–1.000 contactos consentidos y uno o dos avisos mensuales.

## Goals / Non-Goals

**Goals:**

- Separar modelo editorial y presentación para que el contenido sobreviva a cambios de tema y despliegues.
- Mantener una administración pequeña, comprensible y exportable con dependencias gratuitas.
- Conservar URLs, apariencia, privacidad por defecto, indexación controlada y rollback.
- Automatizar un aviso solo cuando la editora lo solicita y demostrar que no puede duplicarse.

**Non-Goals:**

- Reproducir un sistema de gestión documental, pruebas procesales o `CLM` en WordPress.
- Alojar CCTV, documentos restringidos, firmas u originales de terceros sin permiso.
- Añadir publicación social automática, comentarios, cuentas públicas, pagos o traducción automática publicable.
- Garantizar que un plan externo continuará siendo gratuito; un cambio de límites requiere una decisión nueva.

## Decisions

### 1. Plugin propio para el dominio editorial

Se creará `wp-content/plugins/kermanentzat-editorial/` para registrar tipos, metadatos, permisos, patrones, migraciones, integración de suscripción y comandos WP-CLI. El tema conservará plantillas, estilos y renderizado público. Así los datos no dependen del tema y la lógica queda versionada.

Se usará Polylang Free de forma opcional para relaciones lingüísticas. Los campos administrativos esenciales se registrarán en el plugin propio mediante metadatos y metaboxes PHP versionados, sin depender de ACF ni de definiciones existentes solo en la base de datos. Si falta Polylang, el plugin mostrará un aviso, conservará lengua y grupo de traducción mediante metadatos propios y evitará perder relaciones.

Alternativa descartada: implementar todo en el tema. Simplifica el primer despliegue, pero acopla contenido y presentación y dificulta exportación o cambio de tema.

### 2. Modelo de datos pequeño y consultable

- `kerman_update`: título, contenido, extracto e imagen nativos; taxonomía cerrada `kerman_update_type` con `news`, `press-release`, `statement`, `activity`, `press-archive`; fecha editorial, destacado y campos específicos.
- `kerman_timeline`: fecha inicial, fecha final opcional, precisión de fecha, resumen, contenido, destacado y fuentes.
- `kerman_source`: privado y no consultable públicamente; identificador `SRC-###`, entidad/autor, fecha, URL, URL archivada y fecha de comprobación.
- Adjuntos: crédito, situación de derechos, permiso/licencia y alternativa textual; no se duplicarán binarios externos para crear una ficha.

Los metadatos se registrarán con tipos, sanitización, autorización y exposición REST únicamente cuando el editor lo necesite. Las taxonomías y claves internas permanecerán estables para exportación.

### 3. Traducciones opcionales con Polylang

Cada entrada es un post WordPress independiente con lengua propia y relación Polylang opcional. Los archivos consultan solo la lengua actual. El selector usa la contraparte cuando existe; si falta, dirige al archivo del otro idioma y muestra un aviso localizado.

Canonical y `hreflang` se calcularán sobre el objeto consultado. No se publicará `hreflang` para una contraparte ausente. La campaña utiliza el grupo de traducción como clave idempotente para evitar dos avisos sobre la misma novedad.

Alternativa descartada: campos ES/EU dentro de una sola entrada. Reduce posts, pero complica slugs, borradores independientes, indexación y traducciones opcionales.

### 4. Páginas Gutenberg y proyecciones dinámicas

Las páginas institucionales conservarán sus slugs y pasarán de un bloque HTML monolítico a bloques nativos y patrones `contentOnly` con encabezados y componentes estructurales bloqueados. Cronología, novedades y suscripción se insertarán mediante bloques dinámicos o shortcodes renderizados en servidor, de modo que la editora no copie listados.

El archivo de `kerman_update` ofrecerá filtros GET indexables solo en su vista canónica, paginación y tarjetas diferenciadas. Hemeroteca será una vista filtrada. Las actividades se separarán entre próximas y finalizadas por fecha, sin cambiar el estado del post.

### 5. Fuentes, medios y aprobación proporcionada

Las fuentes se vincularán mediante relación a `kerman_source`; no habrá entidad `CLM`. Una checklist de contenido sensible exigirá confirmar atribución, minimización, derechos y referencia de aprobación externa. Esa referencia será texto administrativo no público y no admitirá documentos sensibles.

Los adjuntos sin crédito, derechos o alt aplicable mostrarán errores editoriales. Una referencia externa puede publicarse sin imagen; no se descargará automáticamente la imagen de origen.

### 6. Seed de bootstrap y migración dirigida

`inc/seed.php` dejará de actualizar páginas existentes. Solo podrá crear páginas ausentes y configurar opciones iniciales. Un comando WP-CLI separado convertirá cada página conocida desde el HTML versionado a bloques, importará la primera referencia de hemeroteca y registrará una versión de migración. Tendrá modo `--dry-run`, comprobará estado esperado y no volverá a modificar objetos migrados.

Antes de migrar se exportarán base de datos y medios. El rollback restaura la copia y la versión anterior del código; no se mantendrá un seed destructivo como rollback.

### 7. Sender para altas y campañas ordinarias

Sender se selecciona porque el plan gratuito vigente cubre el volumen previsto y su API permite crear y enviar campañas ordinarias a un grupo. Se crearán dos formularios localizados conectados al mismo grupo, con double opt-in y baja del proveedor.

El bloque de suscripción muestra inicialmente HTML local. El iframe del formulario alojado se crea solo tras una acción explícita; habrá un enlace alternativo al mismo formulario. WordPress no replica direcciones. Las URLs de formulario y el grupo son opciones administrativas; el token API se lee exclusivamente de `KERMANENTZAT_SENDER_API_TOKEN` o secreto equivalente del entorno.

Una editora activa `notify_subscribers`, desmarcado por defecto. Al primer paso a `publish`, el plugin usa la identidad estable del grupo de traducción, persiste el estado y agenda un único evento. El evento crea una campaña HTML para el grupo y después solicita su envío. Se desactivan Google Analytics y auto-followup en la campaña.

Estados: `not_requested`, `queued`, `sending`, `sent`, `failed`, `cancelled`. Se guardan campaña, marcas temporales, intentos y error sanitizado; nunca email ni token. Un cron real invocará WP-Cron cada cinco minutos. Tras tres fallos no se reintenta sin acción administrativa.

Alternativas descartadas: `wp_mail`, por entregabilidad y gestión de bajas; MailPoet gratuito, por su límite inferior al volumen; y campañas transaccionales, porque estos avisos son comunicaciones editoriales a una lista.

### 8. Privacidad y seguridad como condición de activación

Sender permanecerá desactivado hasta disponer de cuenta institucional, dominio remitente verificado, condiciones/DPA y transferencias revisadas, formularios double opt-in, texto bilingüe aprobado y configuración SPF/DKIM/DMARC. El registro central describirá proveedor, finalidad, datos, activación, base, conservación, baja y garantías.

El formulario no reutilizará el consentimiento de Analytics. Sender se registrará como servicio opcional independiente, pero no se añadirá un banner: la persona inicia la carga, acepta la información del formulario y confirma por email. La CSP solo permitirá como `frame-src` los orígenes HTTPS de los formularios cuando la integración esté aprobada y completamente configurada.

El Excel se procesa fuera de WordPress. Una herramienta de validación genera un informe sin direcciones completas, rechaza filas sin consentimiento acreditable y produce un archivo temporal importable que no se versiona. La supresión del proveedor prevalece sobre cualquier importación.

### 9. Manual administrativo basado en la interfaz verificada

Después de migrar staging se capturará la administración real con una cuenta editorial y contenido autorizado. El manual explicará en castellano las etiquetas y equivalencias EU, distinguirá contenido editable, proyecciones dinámicas y estructura técnica, y cubrirá noticias, cronología, hemeroteca, traducciones, medios, fuentes, programación y Sender.

Las capturas se revisarán individualmente y no mostrarán usuarios, emails, referencias privadas, secretos ni contenido restringido. Los flujos y diagnósticos complejos se representarán con Mermaid. La aceptación exige que una persona no técnica complete una edición de cronología y una publicación bilingüe siguiendo únicamente la guía.

## Risks / Trade-offs

- [Polylang cambia su edición gratuita] → Mantener lengua e identidad de traducción de respaldo registradas por código, exportación documentada y pruebas sin dependencia premium.
- [Los patrones limitan demasiado o permiten romper el diseño] → Probar las tareas editoriales reales y limitar únicamente estructura, no texto ni medios autorizados.
- [Una migración cambia HTML, SEO o URLs] → Dry-run, página piloto, comparación automatizada y backup restaurable antes del lote.
- [WP-Cron no se ejecuta en un sitio con poco tráfico] → Cron de hosting cada cinco minutos y estado visible de la cola.
- [Un guardado duplica campañas] → Clave única por grupo de traducción, transición de estado atómica e identificador externo persistido.
- [El proveedor cambia límites o tratamiento] → Aviso al 80 %, feature flag de apagado y nueva decisión antes de pagar o migrar.
- [El Excel contiene consentimientos antiguos o ambiguos] → Simulación obligatoria y exclusión por defecto sin evidencia suficiente.
- [El iframe del formulario introduce solicitudes no documentadas] → Carga tras interacción, origen CSP derivado de configuración, prueba de red y bloqueo de activación hasta actualizar registro y textos.

## Migration Plan

1. Introducir el plugin desactivado funcionalmente y sus pruebas; instalar y configurar Polylang en staging si se usará para gestionar relaciones lingüísticas.
2. Exportar base de datos y medios, ejecutar dry-run y migrar una pareja de páginas piloto.
3. Comparar rutas, contenido, metadatos, accesibilidad, sitemaps y frontend; restaurar backup si hay diferencias no aceptadas.
4. Migrar páginas restantes, crear archivos y activar la administración editorial. Confirmar que reejecutar el seed no modifica nada.
5. Probar con la editora la actualización del resumen, un hito, cada tipo de novedad, hemeroteca y traducción opcional.
6. Preparar Sender con cuenta institucional y destinatarios de prueba; activar formularios y campañas solo en staging/no-op hasta completar privacidad y DNS.
7. Importar una muestra consentida, enviar una campaña de prueba y comprobar alta, baja, rebotes, duplicados, logs y límites.
8. Importar la lista validada y activar la cola en producción. La desactivación del feature flag detiene altas y nuevos envíos sin borrar la lista externa.
9. Capturar la interfaz aceptada, publicar el manual ilustrado y repetir con una persona editora las tareas ordinarias antes de archivar.

El despliegue no archiva el cambio hasta que ambas fases estén aceptadas. La retirada de `build-kermanentzat-editorial-platform` se realiza junto con esta propuesta y su historial queda disponible en Git.
