# Informe de pruebas de privacidad

Ejecución final: 2026-07-22. Entorno: WordPress 7.0.2, PHP 8.4, Apache y MariaDB 11.8 en Docker; URL local `http://localhost:8082`.

## Resultado automatizado

Comando: `powershell -ExecutionPolicy Bypass -File scripts/test-privacy.ps1`

Resultado final: **29 comprobaciones superadas, 0 fallos**. El script recorrió las 14 rutas públicas, incluidas las seis legales, y además validó código fuente y sintaxis PHP.

| Control | Resultado | Evidencia reproducible |
|---|---|---|
| HTTP público | completado | 14 rutas con estado 200 |
| Cookies anónimas | completado | Ninguna respuesta contiene `Set-Cookie` |
| Almacenamiento/beacon | completado | Sin APIs en código ejecutable ni scripts inline |
| Tracking y embeds | completado | Sin Analytics, Tag Manager, píxeles o iframes |
| Recursos automáticos | completado | Todos los `src`/hojas de estilo son del mismo origen |
| Consentimiento inactivo | completado | Sin banner, panel o marcador de almacenamiento |
| Páginas legales | completado | Seis rutas 200, pie bilingüe, `hreflang` y selector contextual |
| Cabeceras | completado | CSP, nosniff, no-referrer, Permissions-Policy y DENY |
| Identificador no acreditado | completado | Ausente del repositorio y del HTML generado |
| Sintaxis | completado | PHP válido en tema/MU-plugin; JavaScript válido con `node --check` |
| Seed y WordPress | completado | Seed idempotente ejecutado; política WP apunta a la página publicada; tema activo |
| Base de datos | completado | `wp db check`: todas las tablas OK |

La primera revisión manual descubrió que el detector de emoji de WordPress utilizaba `sessionStorage` y contenía una posible ruta de respaldo a un dominio externo. Se desactivaron sus scripts, estilos y filtros; la prueba se amplió para inspeccionar scripts inline y la repetición final quedó verde.

## Cabeceras y cookies

Se hicieron solicitudes anónimas independientes para evitar arrastrar sesión. Todas carecen de `Set-Cookie`. La CSP observada limita `default-src`, `connect-src`, scripts y estilos al propio origen, bloquea frames y objetos y declara `frame-ancestors 'none'`. En local también se conserva `X-Robots-Tag`; este bloqueo de indexación no se aplica en producción.

## Recursos cargados

La inspección combinada de HTML y navegador no encontró recursos automáticos externos ni almacenamiento en scripts. Los únicos destinos de terceros visibles son enlaces iniciados por la persona visitante. El navegador informó `externalAutomatic: []`, `forbiddenInline: false` y cero errores de consola tras retirar el detector de emoji.

## Comprobación manual

Se revisaron las políticas y las páginas de aportaciones en castellano y euskera, en puntos de ruptura de escritorio y móvil:

- Un único H1 y secuencia H1 → H2 → H3 coherente.
- Selector de idioma contextual y navegación legal completa en el pie.
- Sin desbordamiento horizontal en los anchos efectivos observados.
- Menú móvil operativo; Escape lo cierra, retira `inert` del contenido y devuelve el foco al `summary`.
- Enlace de salto, regla `:focus-visible` de 3 px y estilos de movimiento reducido presentes.
- Áreas táctiles de los tres enlaces legales: 44 px de alto tras el ajuste.
- Texto base de 16,5 px, interlineado de 26,8 px y contraste principal negro sobre blanco; colores secundarios definidos con contraste AA en su uso previsto.
- Información de aportaciones presente en ambos idiomas, con receptor, finalidad, incidencia, enlace de privacidad y advertencia fiscal.
- No aparecieron controles de consentimiento ni errores de consola.

## Limitaciones y validación pendiente

- La automatización no sustituye pruebas con NVDA, VoiceOver u otro lector de pantalla real.
- El navegador de prueba no reflejó cambios de zoom mediante el atajo, por lo que el zoom al 200 % requiere una comprobación humana adicional en un navegador de escritorio; se verificaron diseño fluido, `text-size-adjust: 100%` y ausencia de desbordamiento en móvil.
- La preferencia del sistema estaba en movimiento normal; se comprobó la existencia y alcance de `prefers-reduced-motion`, pero conviene repetir manualmente con la opción del sistema activada.
- La revisión visual no valida por sí misma traducción jurídica al euskera ni suficiencia legal.
- Hosting, HSTS, logs, Gmail, conservación, responsables operativos y fiscalidad siguen pendientes según `LEGAL_INFORMATION_REQUIRED.md`.

