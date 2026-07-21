# 06 — Decisión de CMS y condiciones de operación

Fecha de revisión: 2026-07-21. WordPress gestionado queda seleccionado para el MVP; proveedor, solución bilingüe y configuración final siguen pendientes.

Leyenda: `CONFIRMADO` · `HIPÓTESIS` · `PENDIENTE DE VERIFICACIÓN` · `DECISIÓN NECESARIA` · `RIESGO` · `FUENTE NECESARIA`.

## Decisión

`CONFIRMADO` Los contenidos serán actualizados por socios de la asociación sin conocimientos informáticos. Para el MVP se utilizará **WordPress gestionado**, en instalación única, con el editor nativo de bloques (Gutenberg) y una interfaz editorial reducida.

La decisión responde a continuidad y autonomía editorial: WordPress ofrece edición visual de páginas y entradas, roles de usuario y revisiones de contenido, y existe un mercado amplio de soporte y relevo técnico. No implica todavía elegir proveedor, tema, plugin bilingüe ni empresa de mantenimiento.

`CONFIRMADO` No se utilizarán Elementor, Divi u otros constructores visuales pesados como base del sitio. El diseño se implementará mediante un tema ligero y patrones de bloques aprobados. Los socios editarán textos, imágenes y datos previstos; no administrarán plugins, tema, código ni estructura global.

`CONFIRMADO` Markdown/Git, Strapi, Directus y otros CMS headless quedan descartados como interfaz editorial principal del MVP. Podrán reevaluarse en el futuro solo si aparecen necesidades que WordPress no pueda resolver de forma mantenible.

## Configuración mínima exigida

- WordPress en modalidad gestionada y sitio único; Multisite solo si surge una necesidad demostrable.
- Editor nativo de bloques con patrones reutilizables, estilos controlados y bloqueo de estructura donde un cambio pueda romper la página.
- Número mínimo de plugins, mantenidos y con una función justificada.
- Solución bilingüe que vincule ES/EU, preserve la página equivalente y permita bloquear una traducción no revisada.
- Cuentas individuales, MFA cuando el proveedor/configuración lo permita, mínimo privilegio y baja de accesos documentada.
- Uno o dos administradores técnicos; socios como editores y, si procede, autores/colaboradores.
- Entorno de pruebas, actualizaciones controladas, copias externas, restauración ensayada, monitorización y soporte.
- Exportación estándar de contenido y medios para reducir dependencia del proveedor.
- Registro editorial de fuentes, revisiones, traducciones, aprobación y correcciones. Las revisiones nativas de WordPress ayudan a restaurar contenido, pero no sustituyen por sí solas el flujo de aprobación ni la trazabilidad `SRC/CLM`.

## Prueba de aceptación antes de construir

Dos socios no técnicos deberán poder, con una guía breve:

1. corregir un texto de Inicio sin alterar su diseño;
2. actualizar el resumen del caso en ES/EU y previsualizar ambas versiones;
3. sustituir una imagen únicamente por un activo aprobado con alt ES/EU;
4. enviar un cambio sensible a revisión sin publicarlo accidentalmente;
5. recuperar una revisión anterior con ayuda del responsable técnico;
6. localizar a quién pedir soporte y qué hacer si pierden el acceso.

`DECISIÓN NECESARIA` Elegir la solución bilingüe después de probar el flujo real. Polylang y WPML son candidatos conocidos, pero no se seleccionará uno por inercia: se comprobarán paridad, permisos, compatibilidad con bloques, SEO, exportación, coste y continuidad.

`DECISIÓN NECESARIA` Elegir alojamiento gestionado y soporte según coste total anual, copias, restauración, staging, actualizaciones, seguridad, residencia de datos, atención y posibilidad de migración.

## Roles operativos propuestos

| Rol | Puede hacer | No debería hacer |
|---|---|---|
| Administrador técnico | Configuración, usuarios, actualizaciones, backups, restauración e incidencias. | Aprobar por sí solo el relato sensible. |
| Editor de la asociación | Editar páginas, coordinar ES/EU, revisar y publicar contenido aprobado. | Instalar plugins, cambiar tema o editar código. |
| Autor/colaborador | Preparar borradores y añadir fuentes. | Publicar contenido sensible o modificar páginas globales. |
| Revisor autorizado | Validar hechos, lenguaje, traducción, derechos o aprobación familiar según el contenido. | Cambiar configuración técnica. |

`HIPÓTESIS` En el MVP, con cuatro páginas y baja frecuencia, parte del flujo puede resolverse con estados/campos sencillos y una lista de comprobación, evitando instalar un sistema editorial excesivo. Debe conservarse quién revisó cada versión sensible.

## Decisiones todavía abiertas

- Responsable y suplente de administración técnica y soporte.
- Presupuesto inicial y anual para alojamiento, licencias y mantenimiento.
- Proveedor de hosting gestionado, dominio, correo y copias externas.
- Plugin/estrategia bilingüe y mecanismo de aprobación editorial.
- Número definitivo de cuentas y quién tendrá permiso de publicación.
- RPO/RTO, calendario de actualizaciones y procedimiento de incidente.

`RIESGO` WordPress no reduce el riesgo por sí mismo: demasiados plugins, cuentas compartidas, actualizaciones sin responsable o un constructor propietario pueden dejar la web insegura y difícil de mantener.

## Fuentes

- WordPress: [editor de bloques](https://wordpress.org/documentation/article/wordpress-block-editor/), [roles y capacidades](https://wordpress.org/documentation/article/roles-and-capabilities/) y [revisiones](https://wordpress.org/documentation/article/revisions/).
- `CONFIRMADO` Decisión comunicada por el responsable del proyecto el 2026-07-21: la asociación editará la web mediante socios sin conocimientos informáticos.
