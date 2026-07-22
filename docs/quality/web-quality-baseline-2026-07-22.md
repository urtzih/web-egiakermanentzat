# Web Quality — línea base local

Fecha: 2026-07-22. Entorno: WordPress 7.0.2/PHP 8.4 en Docker,
`http://localhost:8082`, Lighthouse 13.4.1 con perfil móvil.

## Resultado

| Ruta | Performance | Accessibility | Best Practices | SEO | LCP | CLS | Peso |
|---|---:|---:|---:|---:|---:|---:|---:|
| EU portada | 93 | 100 | 100 | 61 | 3,21 s | 0,005 | 489 KiB |
| EU caso | 90 | 100 | 100 | 58 | 3,69 s | 0,002 | 489 KiB |
| EU ayuda | 100 | 100 | 100 | 58 | 1,44 s | 0,001 | 65 KiB |
| EU contacto | 100 | 100 | 100 | 58 | 1,44 s | 0,001 | 65 KiB |
| ES portada | 93 | 100 | 100 | 61 | 3,21 s | 0,005 | 489 KiB |
| ES caso | 89 | 100 | 100 | 58 | 3,69 s | 0,002 | 489 KiB |
| ES ayuda | 100 | 100 | 100 | 58 | 1,44 s | 0,006 | 65 KiB |
| ES contacto | 100 | 100 | 100 | 58 | 1,44 s | 0,001 | 65 KiB |

TBT fue 0 ms en las ocho rutas. Los informes JSON se generaron fuera del
repositorio. En Windows, Lighthouse mostró de forma intermitente un error al
limpiar su perfil temporal después de escribir un informe válido; las métricas
se aceptaron únicamente cuando el JSON completo incluía `fetchTime`.

## Hallazgos priorizados

### Alta — rendimiento de portada y caso

- El LCP de portada fue 3,21 s y el del caso 3,69 s, por encima del objetivo de
  2,5 s.
- Lighthouse estima unos 334 KiB de ahorro mediante mejor entrega de la imagen
  usada en ambas plantillas.
- El PNG del retrato pesa 433.425 bytes y el banner 568.589 bytes. Antes de
  producción se deben generar variantes WebP/AVIF y tamaños responsivos sin
  sustituir los originales autorizados.
- Se detectaron recursos que bloquean el renderizado, con estimaciones variables
  de hasta 790 ms en el entorno Docker local. Deben revisarse CSS global,
  estilos de bloques de WordPress y descubrimiento del recurso LCP.

### Media — SEO pendiente de producción

- Las ocho rutas carecen de `meta description`; Lighthouse las penaliza por
  ello.
- La preview no es rastreable porque conserva `noindex` tanto en HTML como en
  `X-Robots-Tag`. Es el comportamiento correcto del entorno local y no debe
  cambiarse para elevar la puntuación.
- WordPress ya genera un canonical y el tema genera un alternativo `hreflang`
  contextual, pero faltan metadatos Open Graph/Twitter y JSON-LD. El alcance
  completo queda registrado en la tarea OpenSpec 7.2.

### Sin fallos automáticos — accesibilidad y buenas prácticas

- Lighthouse obtuvo 100 en Accessibility y Best Practices en las ocho rutas.
- La inspección en navegador confirmó `lang` ES/EU, un solo `h1`, `main`, textos
  alternativos, ausencia de overflow horizontal, navegación móvil, selector de
  idioma contextual, estado accesible tras copiar los datos y consola sin
  avisos ni errores.
- El CSS y JavaScript incluyen tratamiento para `prefers-reduced-motion`.
- Esta línea base no equivale a conformidad WCAG 2.2 AA. La automatización del
  navegador integrado no produjo una secuencia Tab fiable, por lo que teclado
  completo y lector de pantalla siguen siendo pruebas manuales obligatorias
  antes de publicar.

## Validaciones de soporte

- Las 14 skills incluidas en esta consolidación pasan `quick_validate.py` y
  tienen nombres únicos.
- Durante la validación apareció además la carpeta no versionada
  `egia-privacy-compliance`, ajena a esta tarea. Es todavía una plantilla con
  campos `TODO` y no pasa el validador; se ha dejado intacta para no interferir
  con el trabajo concurrente que la está creando.
- `.codex/skills` no permanece en disco; los seis workflows OpenSpec viven solo
  en `.agents/skills`.
- `openspec doctor --json`, `docker compose config`, PHP y JavaScript pasan.
- Las ocho rutas responden HTTP 200 y conservan la cabecera local
  `X-Robots-Tag: noindex, nofollow, noarchive, nosnippet`.

Para repetir las comprobaciones, usar
[`web-quality-runbook.md`](web-quality-runbook.md).
