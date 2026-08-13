# Manual editorial de WordPress

Guía de trabajo para el equipo de Egia Kermanentzat Elkartea. Está escrita en castellano; junto a cada nombre importante se indica la etiqueta o el ejemplo equivalente en euskera (EU).

Este manual cubre publicaciones, cronología, hemeroteca, fuentes, imágenes, traducciones, programación y avisos por email. No autoriza a modificar tema, plugins, usuarios, migraciones, copias de seguridad ni configuración técnica.

## 1. Acceso seguro

### Backend de staging

- **Dirección:** [https://web-egiakermanentzat.stag.urtzi.fun/wp-admin/](https://web-egiakermanentzat.stag.urtzi.fun/wp-admin/)
- **Entorno:** pruebas con datos separados de producción, pero conectado al grupo real de Sender. Publicar con el aviso marcado puede enviar emails reales.
- **Credenciales:** consulta la entrada **WordPress - Egia Kermanentzat - staging** en el gestor de contraseñas de la asociación. Este manual y el repositorio no contienen usuarios nominales ni contraseñas.

Staging puede solicitar dos accesos consecutivos:

| Paso | Usuario que debes usar | Secreto | Finalidad |
|---|---|---|---|
| Protección de staging del navegador | Cuenta compartida de acceso perimetral indicada en el gestor | Contraseña perimetral | Impide visitas externas al entorno de pruebas |
| Inicio de sesión de WordPress | Tu cuenta personal asignada | Contraseña personal de WordPress y MFA cuando esté disponible | Identifica quién edita y aplica su rol |

No intercambies ambas credenciales. La primera abre el entorno; la segunda identifica a la persona que trabaja dentro de WordPress.

### Usuarios y roles

| Cuenta | Rol | Uso permitido | No permite |
|---|---|---|---|
| Cuenta personal de cada integrante del equipo editorial | **Editora Kermanentzat** | Páginas, Berriak / Actualidad, Cronología, Fuentes, Medios, traducciones, borradores, programación y publicación | Usuarios, plugins, tema, ajustes, copias, migraciones o secretos |
| Cuenta personal del soporte responsable | **Administrador técnico** | Altas y bajas de usuarios, permisos, recuperación, configuración, despliegues, copias e incidencias | Aprobar por sí sola contenido sensible o cambios editoriales |

No se utiliza una cuenta editorial genérica. Cada persona debe entrar con su usuario individual para conservar trazabilidad. Los nombres de inicio de sesión y las contraseñas se entregan de forma privada y se actualizan en el gestor, no en este documento.

### Pasos para entrar

1. Abre la dirección de administración de staging y comprueba que empieza por `https://` y termina en `/wp-admin/`.
2. Si aparece la protección previa del navegador, utiliza el acceso perimetral guardado en el gestor de contraseñas.
3. En WordPress, inicia sesión con tu cuenta personal de rol **Editora Kermanentzat**. No compartas cuentas ni contraseñas.
4. Activa MFA en cuanto la administración técnica habilite el segundo factor. Si MFA todavía no aparece en el perfil, pide su activación; WordPress no lo incorpora por sí solo.
5. No aceptes guardar la contraseña en un ordenador compartido.
6. Al terminar, abre el menú superior derecho y pulsa **Cerrar sesión / Saioa itxi**.

### Si no puedes acceder

- No pruebes contraseñas repetidamente ni envíes capturas donde se vea una credencial.
- Comprueba que estás usando la entrada de **staging**, no la de producción.
- Solicita al soporte responsable una recuperación o rotación; nunca pidas que te envíen la contraseña por email o mensajería sin cifrar.
- Si una persona deja el equipo, el administrador técnico debe desactivar su cuenta individual y revisar sus sesiones.

El rol editorial muestra Medios, Páginas, Berriak / Actualidad, Cronología y Fuentes. No muestra Plugins, Apariencia, Ajustes ni Usuarios. Si aparecen esas opciones, detente y comunica que la cuenta tiene más permisos de los necesarios.

![Listado de publicaciones con el rol editorial y las columnas de idioma y versión vinculada](assets/admin-guide/admin-listado-editorial.png)

## 2. Qué se puede cambiar

| Área | Tipo | Cómo se mantiene | Qué no tocar |
|---|---|---|---|
| Relato del caso | Editable con cautela | Páginas → Kasuaren laburpena / Resumen del caso | No borrar el módulo de cronología ni cambiar la estructura sin revisión |
| Cronología clave | Dinámica | Cronología → un hito por fecha e idioma | No escribir la cronología a mano dentro de la página |
| Berriak / Actualidad | Dinámica | Una publicación estructurada por noticia, nota, comunicado, actividad o hemeroteca | No editar las tarjetas desde la página Berriak |
| Hemeroteka / Hemeroteca | Dinámica | Publicaciones de tipo Hemeroteca, con enlace y atribución | No copiar artículos o imágenes de terceros |
| Fuentes | Editable y privada | Fuentes → ficha interna vinculable | No subir expedientes ni datos sensibles |
| Imágenes | Editable y validada | Medios → alt, crédito, derechos y permiso | No publicar una imagen sin derechos comprobados |
| Suscripción | Dinámica y condicionada | Sender, cola y WP-Cron cuando exista aprobación | No cambiar configuración, pegar tokens ni importar Excel por cuenta propia |
| Cabecera, pie, estilos, plantillas y código | Estática/versionada | Tema, plugin y despliegue técnico | No tocar |
| Páginas legales | Editable solo con aprobación | Páginas legales ES/EU | No improvisar cambios jurídicos |

Las páginas Berriak, Actualidad, Kronologia, Cronología, Hemeroteka, Hemeroteca, Harpidetza y Suscripción son contenedores. Su contenido visible se genera a partir de las entidades editoriales. Si una tarjeta o un hito está mal, corrige la entidad; no el contenedor.

~~~mermaid
flowchart LR
    U["Berriak / Actualidad"] --> A["Noticias, notas, comunicados y actividades"]
    H["Hemeroteka / Hemeroteca"] --> P["Referencias de prensa"]
    C["Kasuaren laburpena / Resumen"] --> T["Hitos de Cronología"]
    P --> F["Fuentes privadas"]
    A --> F
    T --> F
    A --> S["Aviso Sender, cuando esté aprobado"]
~~~

## 3. Flujo editorial recomendado

No publiques directamente una primera versión. Trabaja en borrador, revisa el contenido y completa la pareja EU/ES.

~~~mermaid
flowchart TD
    B["Borrador"] --> R["Revisión de hechos, privacidad y derechos"]
    R --> T["Crear y vincular traducción EU/ES"]
    T --> V["Previsualizar ambas versiones"]
    V --> P["Publicar o programar"]
    P --> C["Comprobación pública"]
~~~

Checklist antes de publicar:

- El título y el resumen dicen lo mismo en EU y ES.
- Hechos, citas y valoraciones están atribuidos.
- Se han eliminado datos personales innecesarios.
- Los enlaces funcionan y la fuente está registrada.
- Cada imagen tiene texto alternativo, crédito y derechos.
- Si el contenido es sensible, las tres comprobaciones y la referencia de aprobación están completas.
- La fecha editorial es correcta.
- La versión vinculada apunta al contenido del otro idioma.
- **Enviar aviso al publicar** aparece marcado en contenido nuevo cuando Sender está configurado; desmárcalo antes de publicar si no deseas un único aviso real.

## 4. Crear una noticia, nota, comunicado, actividad o hemeroteca

1. Ve a **Berriak / Actualidad → Añadir publicación**.
2. Escribe el título, el cuerpo y un extracto breve.
3. En **Datos editoriales**, elige **Tipo**:

   - Noticia / Albistea.
   - Nota de prensa / Prentsa-oharra.
   - Comunicado / Komunikatua.
   - Actividad / Jarduera.
   - Hemeroteca / Hemeroteka.

4. Indica la **Fecha editorial**. No la confundas con la fecha técnica de guardado.
5. Marca **Destacar en portada** solo para una pieza prioritaria.
6. Vincula las fuentes que sostienen el contenido.
7. Completa revisión, idioma y traducción.
8. Usa **Previsualizar** antes de publicar.

![Formulario de una publicación real con tipo, fecha y controles de WordPress](assets/admin-guide/admin-formulario-noticia.png)

![Datos específicos de una referencia de hemeroteca y control de Sender](assets/admin-guide/admin-datos-noticia.png)

### Campos adicionales de una actividad

Al elegir Actividad / Jarduera aparecen inicio, fin opcional, lugar y enlace de inscripción o información. Comprueba la zona horaria y revisa el estado público:

- Próxima / Hurrengoa.
- En curso / Abian.
- Finalizada / Amaituta.

### Campos adicionales de hemeroteca

Completa medio, autoría si se conoce, fecha original y URL original. El cuerpo debe ser un resumen propio y atribuido. No pegues el artículo completo y no descargues su fotografía salvo que exista un permiso documentado.

El resultado público agrupa las piezas por tipo y mantiene el enlace externo:

![Tarjeta pública de Hemeroteka en Berriak](assets/admin-guide/public-berriak-resultado.png)

## 5. Crear y vincular la pareja EU/ES

Cada idioma es una entrada independiente. El enlace nativo funciona aunque Polylang no esté instalado.

1. Crea y guarda primero una versión, por ejemplo EU.
2. Crea la segunda versión ES.
3. En **Idioma y traducción**, selecciona **ES · Castellano** para esa segunda versión.
4. En **Versión vinculada**, elige la entrada EU correcta.
5. Guarda.
6. Vuelve al listado y comprueba las columnas **Idioma** y **Versión vinculada** en ambos sentidos.

![Selector nativo de idioma, versión vinculada y estado de aviso](assets/admin-guide/admin-traducciones-y-sender.png)

No enlaces dos entradas del mismo idioma. Si falta una traducción publicada, el selector público conduce al archivo del otro idioma e informa de que esa traducción todavía no está disponible; nunca debe mostrar el texto equivocado bajo otra etiqueta de idioma.

~~~mermaid
flowchart TD
    E["Entrada EU"] --> Q{"¿Existe versión ES publicada?"}
    Q -- Sí --> L["Selector abre la versión ES y añade hreflang"]
    Q -- No --> F["Selector abre Actualidad con aviso de traducción no disponible"]
    S["Entrada ES"] --> R{"¿Existe versión EU publicada?"}
    R -- Sí --> K["Selector abre la versión EU y añade hreflang"]
    R -- No --> G["Selector abre Berriak con aviso de traducción no disponible"]
~~~

## 6. Añadir o corregir un hito de cronología

1. Ve a **Cronología → Añadir hito**.
2. El título debe describir el hecho, no solo repetir la fecha.
3. Escribe un resumen breve y atribuible.
4. En **Datos editoriales**, completa:

   - Fecha inicial.
   - Fecha final, solo si es un periodo.
   - Precisión: Día, Mes, Año o Periodo.
   - **Destacar este hito** para que aparezca en la cronología clave del resumen.
   - Fuentes relacionadas.

5. Completa la revisión sensible cuando corresponda.
6. Crea y vincula la versión del otro idioma.
7. Publica o programa y revisa Kasuaren laburpena y Resumen del caso.

El orden no se arrastra manualmente: se calcula por fecha inicial, fecha final y un desempate estable. Para mover un hito, corrige su fecha. No pongas fechas falsas para forzar una posición.

![Listado de cronología con veinte hitos y sus parejas EU/ES](assets/admin-guide/admin-listado-cronologia.png)

![Formulario de un hito con fecha, precisión, destacado y fuentes disponibles](assets/admin-guide/admin-formulario-cronologia.png)

Resultado esperado en escritorio:

![Funtsezko kronologia renderizada desde las entidades de WordPress](assets/admin-guide/public-cronologia-resultado.png)

Resultado esperado en móvil:

![Cronología sin desbordamiento horizontal en una pantalla móvil](assets/admin-guide/public-cronologia-movil.png)

## 7. Registrar y usar una fuente

Las fuentes son privadas en WordPress: ayudan a justificar una publicación, pero su ficha no tiene página pública.

1. Ve a **Fuentes → Añadir fuente**.
2. Usa un título reconocible: entidad, idioma y fecha.
3. Completa autor o entidad, fecha, URL pública, URL archivada si existe y última comprobación.
4. Guarda como privada.
5. En la noticia o el hito, selecciónala en **Fuentes relacionadas**.

![Fuentes públicas de la hemeroteca registradas como fichas privadas](assets/admin-guide/admin-listado-fuentes.png)

![Ficha de fuente con identificador SRC, entidad, URL y última comprobación](assets/admin-guide/admin-formulario-fuente.png)

No incluyas nombres privados, teléfonos, documentos, datos de salud, datos judiciales no públicos ni rutas que revelen el expediente. La **Referencia de aprobación externa** debe ser un código o ubicación controlada, nunca el documento ni un enlace público.

## 8. Imágenes, crédito y derechos

1. Sube solo imágenes propias, licenciadas o con permiso documentado.
2. Abre el medio después de subirlo.
3. Escribe un **Texto alternativo** que explique la función de la imagen. Déjalo vacío solo si es puramente decorativa.
4. Añade **Crédito editorial**.
5. Elige **Derechos**:

   - Propio.
   - Licenciado.
   - Permiso documentado.
   - Solo enlace externo.
   - Pendiente.

6. Para Licenciado o Permiso documentado, añade una referencia de permiso sin datos sensibles.
7. No publiques mientras el estado sea Pendiente.

![Texto alternativo y pie de una imagen propia de la cronología](assets/admin-guide/admin-medio-texto-alternativo.png)

![Crédito, estado Propio y campo de referencia documental](assets/admin-guide/admin-medio-derechos.png)

WordPress bloquea la publicación si una imagen usada carece de texto alternativo, crédito o justificación de derechos. No evites el bloqueo retirando datos: completa la documentación o sustituye la imagen.

## 9. Contenido sensible

Marca **Contenido sensible** cuando la pieza trate hechos judiciales, violencia, salud, datos personales o afirmaciones con riesgo para terceros. Deben quedar marcadas:

- He atribuido hechos, declaraciones y valoraciones.
- He retirado datos y documentos innecesarios.
- He comprobado derechos y créditos de los medios.

Añade una referencia de aprobación externa que permita a la asociación localizar la revisión fuera de WordPress.

![Checklist sensible y controles de revisión editorial](assets/admin-guide/admin-revision-y-sender.png)

Si falta una comprobación o referencia, WordPress conserva la entrada como borrador. No publiques información sensible para “probar cómo queda”.

## 10. Previsualizar, programar, corregir y recuperar

### Previsualizar

Usa **Vista previa / Aurrebista** y comprueba:

- título, extracto, enlaces e imagen;
- versión EU y versión ES;
- móvil y escritorio;
- página de archivo y página individual;
- cronología, si has cambiado un hito.

### Programar

1. En la barra lateral abre la fecha de publicación.
2. Elige fecha y hora futuras.
3. Confirma que WordPress muestra **Programada / Scheduled**.
4. WP-Cron revisa la cola cada cinco minutos en staging y producción.

![Calendario de programación de WordPress](assets/admin-guide/admin-programacion.png)

### Corregir una publicación

Haz una corrección pequeña sobre la entrada publicada, explica el cambio en el texto si altera el sentido y actualiza también su traducción. Después revisa canonical, selector de idioma y tarjeta de archivo.

### Recuperar una revisión

Abre la entrada y busca **Revisiones** o la indicación de última edición. Compara la versión anterior antes de restaurarla. Restaurar una revisión no sustituye la revisión EU/ES: comprueba ambas entradas después.

## 11. Sender, suscripciones y avisos

Sender está habilitado permanentemente en staging y conectado al grupo real **Suscriptores web**. Producción es un entorno separado y no queda activado por esta configuración. Una publicación de prueba en staging puede enviar un correo real: revisa siempre la casilla antes de publicar.

Cuando Sender esté completamente configurado en el entorno:

- en una publicación nueva, la casilla aparecerá marcada por defecto;
- desmarcarla antes de publicar cancela el aviso;
- una publicación existente sin ese dato permanece desmarcada;
- mantenerla marcada solicitará un único aviso para la pareja EU/ES, con euskera primero y castellano después;
- guardar, corregir o traducir no volverá a enviar;
- WP-Cron procesará la cola;
- los estados serán No solicitado, En cola, Enviando, Enviado, Fallido o Cancelado;
- **Reintentar** solo se usará después de corregir la causa del fallo;
- tras tres fallos se detendrá el envío automático.

~~~mermaid
sequenceDiagram
    participant E as Editora
    participant W as WordPress
    participant C as WP-Cron
    participant S as Sender
    E->>W: Programa o publica EU/ES con aviso marcado
    W->>W: Crea una identidad única para la pareja
    C->>W: Procesa la cola cada 5 minutos
    W->>S: Crea una campaña bilingüe
    S-->>W: ID de campaña o error
    W-->>E: Enviado, En cola o Fallido
~~~

### Qué correos enviamos

La asociación usa Sender únicamente para avisar de noticias o publicaciones nuevas de la web. No se utiliza esta lista para publicidad comercial, promociones ni mensajes ajenos a la actividad informativa de Egia Kermanentzat.

Cada aviso automático incluye:

- una sola campaña para la pareja EU/ES;
- euskera primero y castellano después;
- título y resumen breve de ambas versiones;
- botones que abren la publicación correspondiente en la web;
- remitente **Egia Kermanentzat Elkartea**;
- dirección física y enlace obligatorio para darse de baja;
- una banda amarilla **[STAGING]** cuando el envío procede del entorno de pruebas. Esa banda no debe aparecer en producción.

![Ejemplo completo del aviso bilingüe recibido desde staging](assets/admin-guide/sender-email-recibido.png)

La captura es un ejemplo histórico de prueba. Los títulos y cifras cambian en cada campaña. Antes de enviar, comprueba que ambos botones usan HTTPS, que abren las publicaciones correctas y que el enlace de baja está presente. No envíes el artículo completo por email: el objetivo es llevar a la persona suscrita a la noticia publicada en la web.

### Dónde están los suscriptores

En Sender abre **Suscriptores**. En esta pantalla puedes buscar una dirección, filtrar por estado y seleccionar el grupo **Suscriptores web**. Ese grupo es la lista operativa que recibe los avisos creados desde WordPress.

![Filtros y cabecera del listado de suscriptores en Sender, sin datos personales](assets/admin-guide/sender-suscriptores.png)

Comprueba siempre:

- **Estado del correo electrónico:** usa suscriptores activos para los envíos normales;
- **Grupos:** selecciona **Suscriptores web**, no el grupo de pruebas;
- **Total mostrado:** es el número de registros que cumplen el filtro actual, no necesariamente el total de personas activas;
- **Bajas, rechazados o bloqueados:** no los reactives ni los vuelvas a importar manualmente;
- **Datos personales:** no exportes, fotografíes ni compartas el listado salvo que soporte lo haya autorizado para una tarea concreta.

El número de suscriptores cambia con cada alta, confirmación, baja o rebote. Para saber cuántos recibirán el próximo aviso, filtra **Suscriptores web + correo activo** justo antes de revisar la campaña.

### Dónde están los emails enviados

Abre **Campañas de correo electrónico**. Cada fila muestra el nombre de la campaña, el grupo destinatario, la fecha y sus indicadores principales. La campaña creada por WordPress debe figurar una sola vez; no hagas una copia ni la vuelvas a enviar para corregir una errata.

![Listado histórico de campañas con entregas, aperturas y clics](assets/admin-guide/sender-campanas.png)

En el ejemplo histórico se entregaron **2 correos** y la apertura terminó en **50 %**, es decir, Sender registró una apertura única entre dos entregas. Estas cifras pertenecen a una prueba y no describen el estado actual. El porcentaje de apertura se consulta siempre en la fila de la campaña concreta.

### Cómo leer el informe de una campaña

Pulsa el icono de informe situado en la columna **Acciones**. En **Resumen** aparecen el asunto, el remitente, el contenido enviado, el progreso y las estadísticas. Los datos pueden cambiar mientras el envío está en curso y durante las horas posteriores.

![Resumen histórico de una campaña durante el envío y sus estadísticas](assets/admin-guide/sender-informe-campana.png)

| Indicador | Qué significa | Qué revisar |
|---|---|---|
| Enviados | Intentos de envío iniciados por Sender | Debe coincidir con el público seleccionado, descontando exclusiones aplicadas por Sender |
| Entregados | Mensajes aceptados por el servidor de destino | Una diferencia con enviados exige revisar rechazados y rebotes |
| Abiertos | Aperturas que Sender puede registrar | Es orientativo: algunos clientes bloquean el seguimiento y otros pueden precargarlo |
| Clics únicos | Personas que han pulsado al menos un enlace medido | Revisa también el informe de clics para confirmar qué enlace recibió actividad |
| Rechazados | Mensajes que el proveedor no aceptó | No reintentes manualmente sin revisar la causa |
| Soft bounced | Fallos temporales de entrega | Observa si Sender los resuelve o termina bloqueando la dirección |
| Dados de baja | Personas que solicitaron dejar de recibir avisos | No las vuelvas a añadir ni importar |
| Spam reports | Personas que marcaron el mensaje como spam | Detén nuevos envíos si aparece un patrón y revisa consentimiento, contenido y frecuencia |

La tasa de apertura se calcula sobre los correos entregados: **aperturas únicas ÷ entregados × 100**. No es una prueba exacta de lectura. Para valorar el resultado, mira conjuntamente entregas, aperturas, clics, bajas, rechazos y spam.

### Revisión práctica después de cada aviso

1. Confirma que WordPress muestra **Enviado** y que solo existe una campaña para la pareja EU/ES.
2. En Sender, abre la campaña y verifica asunto, grupo **Suscriptores web**, remitente y estado final.
3. Comprueba que enviados y entregados tienen sentido para el número de suscriptores activos.
4. Abre el email recibido y prueba los dos botones, el enlace del medio original si existe y la baja.
5. Revisa entregas, rechazos y spam al finalizar; vuelve a mirar aperturas y clics pasadas 24-48 horas.
6. Si hay un fallo, conserva el ID de campaña y pide soporte. No republiques la noticia ni crees una segunda campaña.

No pegues tokens en WordPress. No importes el Excel real desde el administrador. La preparación e importación se realiza con soporte técnico después de revisar consentimiento, duplicados, bajas y supresiones. Nunca se reintroduce una dirección dada de baja.

## 12. Checklist después de publicar

- La URL abre sin iniciar sesión.
- La pieza aparece en Berriak / Actualidad o en la página dinámica adecuada.
- EU y ES están vinculadas.
- El selector de idioma abre la pareja correcta.
- La fecha, el tipo y la atribución son correctos.
- Los enlaces externos abren el medio original.
- La imagen conserva alt, crédito y derechos.
- En móvil no hay desplazamiento horizontal.
- Si se solicitó aviso, el estado cambia una sola vez; no repitas la publicación.
- No hay datos privados visibles.

## 13. Problemas habituales

~~~mermaid
flowchart TD
    I["Incidencia"] --> V{"¿La entrada no se ve?"}
    V -- Sí --> D{"¿Está publicada o programada y tiene el idioma correcto?"}
    D -- No --> D1["Corrige estado, fecha e idioma"]
    D -- Sí --> C{"¿Tipo, fecha editorial y página dinámica son correctos?"}
    C -- No --> C1["Corrige campos; no edites el contenedor"]
    C -- Sí --> S["Pide soporte: caché, plantilla o migración"]
    V -- No --> T{"¿Falta traducción o está mal vinculada?"}
    T -- Sí --> T1["Selecciona la pareja en Idioma y traducción"]
    T -- No --> O{"¿Orden de cronología incorrecto?"}
    O -- Sí --> O1["Corrige fecha y precisión"]
    O -- No --> E{"¿Campaña en cola o fallida?"}
    E -- Sí --> E1["No republiques; revisa estado y pide soporte antes de reintentar"]
    E -- No --> X["Si el diseño está roto, detente y pide soporte"]
~~~

### Entrada invisible

Comprueba estado, fecha de programación, idioma y tipo. Una entrada futura no aparece antes de su hora. Una entrada ES no aparece en Berriak EU.

### Traducción ausente

Comprueba las columnas Idioma y Versión vinculada. Ambas entradas deben apuntar al mismo par EU/ES y estar publicadas si se espera selector directo.

### Orden incorrecto en cronología

Corrige fecha inicial, fecha final y precisión. No dupliques el hito.

### Campaña en cola o fallida

No desmarques, vuelvas a marcar ni republiques una pieza que ya entró en cola. Si Sender está desactivado, no debe salir ningún aviso. Si está aprobado, pide soporte para revisar cron, conectividad y el error antes de usar Reintentar. Un correo iniciado no puede recuperarse con rollback.

### Diseño roto

No abras Apariencia, Plugins ni el editor de código. Guarda como borrador, anota la URL y el momento del fallo, y pide soporte.

## 14. Cuándo detenerse y pedir soporte

Detente ante cualquiera de estas situaciones:

- error de estructura, plantilla, shortcode o bloque protegido;
- plugin, tema, código, migración, base de datos o copia de seguridad;
- alta, baja, permisos o MFA de usuarios;
- activación de Sender, DNS, token, campaña duplicada o importación real;
- dato sensible publicado o posible incidente de privacidad;
- imagen sin derechos claros;
- URL, canonical, selector EU/ES o diseño que no se corrige editando el contenido;
- duda jurídica o lingüística.

No intentes arreglar un problema técnico eliminando contenido, cambiando plugins o creando una segunda publicación. Conserva el borrador y facilita a soporte la URL, la hora, el navegador y una descripción sin datos sensibles.
