# Web Quality — guía operativa local

Fecha de revisión: 2026-07-22.

## Alcance y fuentes

Esta guía corresponde al MVP WordPress 7/PHP 8.4 ejecutado con Docker en
`http://localhost:8082`. No presupone Astro, Geist, GSAP, mapas ni una página
«Empresa».

Las skills de `.agents/skills` ayudan a aplicar un flujo repetible, pero las
fuentes de verdad son:

- [Google Search Central](https://developers.google.com/search/) para SEO técnico.
- [Schema.org](https://schema.org/) y las [políticas de datos estructurados de Google](https://developers.google.com/search/docs/appearance/structured-data/sd-policies).
- [WCAG 2.2](https://www.w3.org/TR/WCAG22/) para accesibilidad.
- [Lighthouse](https://developer.chrome.com/docs/lighthouse/overview/) para auditoría reproducible local.
- [PageSpeed Insights](https://pagespeed.web.dev/) para una URL pública.
- [Google Search Console](https://search.google.com/search-console/about) para un dominio verificado.

Las seis skills Web Quality proceden del commit
`95d6e255afe1596b557d7a8498517884438f5b3a` de
[`addyosmani/web-quality-skills`](https://github.com/addyosmani/web-quality-skills)
y se distribuyen bajo licencia MIT. La procedencia completa está en
`.agents/skills/WEB-QUALITY-SKILLS-SOURCE.md`.

## Matriz de rutas

| Plantilla | Euskera | Castellano |
|---|---|---|
| Portada | `/` | `/es/` |
| Caso | `/kasuaren-laburpena/` | `/es/resumen-del-caso/` |
| Ayuda y donaciones | `/lagundu-eta-ekarpenak/` | `/es/ayuda-y-donaciones/` |
| Contacto | `/kontaktua/` | `/es/contacto/` |

## Comprobaciones locales

Arrancar y comprobar el entorno:

```powershell
docker compose up -d
docker compose config --quiet
docker compose ps
```

Validar PHP y JavaScript sin reescribir archivos:

```powershell
docker compose exec -T wordpress sh -lc "find /var/www/html/wp-content/themes/kermanentzat-prototype /var/www/html/wp-content/mu-plugins -type f -name '*.php' -print0 | xargs -0 -n1 php -l"
node --check wp-content/themes/kermanentzat-prototype/assets/js/site.js
```

Comprobar las ocho respuestas desde PowerShell:

```powershell
$routes = @(
  '/', '/kasuaren-laburpena/', '/lagundu-eta-ekarpenak/', '/kontaktua/',
  '/es/', '/es/resumen-del-caso/', '/es/ayuda-y-donaciones/', '/es/contacto/'
)
$routes | ForEach-Object {
  $response = Invoke-WebRequest -UseBasicParsing ("http://localhost:8082" + $_)
  [pscustomobject]@{ Route = $_; Status = $response.StatusCode }
}
```

Validar las tarjetas y los metadatos sociales:

```powershell
powershell -ExecutionPolicy Bypass -File scripts/test-social-metadata.ps1
```

Después de desplegar un cambio de tarjeta, comprobar `/` y `/es/` en
[Meta Sharing Debugger](https://developers.facebook.com/tools/debug/) y solicitar
un nuevo rastreo. WhatsApp puede conservar temporalmente una previsualización
anterior aunque el HTML público ya sea correcto; los nombres versionados de las
imágenes ayudan a invalidar la caché cuando el crawler vuelve a consultar la URL.
La comprobación final debe hacerse pegando el enlace en una conversación nueva.

Ejecutar Lighthouse 13.4.1 con perfil móvil y guardar los JSON fuera del
repositorio:

```powershell
$routes = @(
  '/', '/kasuaren-laburpena/', '/lagundu-eta-ekarpenak/', '/kontaktua/',
  '/es/', '/es/resumen-del-caso/', '/es/ayuda-y-donaciones/', '/es/contacto/'
)
foreach ($route in $routes) {
  $slug = if ($route -eq '/') { 'eu-home' } else { $route.Trim('/').Replace('/', '-') }
  npx --yes lighthouse@13.4.1 ("http://localhost:8082" + $route) `
    --quiet --chrome-flags="--headless --no-sandbox" --output=json `
    --output-path=(Join-Path $env:TEMP "kermanentzat-lighthouse-$slug.json")
}
```

El `noindex` local y la ausencia de HTTPS producirán observaciones SEO y de
producción esperadas. No deben desactivarse para mejorar artificialmente la
puntuación de la preview privada.

`web-quality-audit/scripts/analyze.sh` no forma parte del flujo obligatorio:
requiere Bash y `jq`, y `jq` no está instalado actualmente en Windows. Además,
su análisis estático de archivos HTML no representa por sí solo el PHP
renderizado por WordPress.

## Revisión manual antes de enseñar la preview

- Recorrer todas las rutas solo con teclado y comprobar salto al contenido,
  orden de foco, navegación móvil y selector contextual de idioma.
- Verificar un único `h1`, jerarquía de encabezados, landmarks, nombres
  accesibles y alternativas de imágenes.
- Revisar contraste, zoom al 200 %, reflow estrecho y objetivos táctiles.
- Probar la copia de datos bancarios y su anuncio `aria-live`.
- Activar `prefers-reduced-motion` y confirmar que no se pierde información ni
  operabilidad.
- Revisar consola, enlaces rotos, CLS y el elemento LCP de cada plantilla.

## Trabajo SEO posterior

La implementación pertenece a la tarea OpenSpec 7.2: títulos y descripciones
únicos, `theme-color`, Open Graph/Twitter, `hreflang` completo, canonicals,
sitemap de producción y JSON-LD que describa exclusivamente contenido visible.

El primer tipo estructurado previsto es `Organization`, acompañado solo de
tipos de página verificables. No se añadirán `BreadcrumbList`, `CreativeWork` ni
tipos especulativos hasta que la interfaz y el contenido visible los justifiquen.

PageSpeed Insights, Rich Results Test y Search Console se ejecutarán después de
disponer de una URL pública HTTPS. Search Console requiere además verificar el
dominio; no se conectará a la preview local.

## Actualización de OpenSpec

`.agents/skills` es la única fuente persistente de skills del proyecto. No se
debe ejecutar `openspec update` como paso final porque OpenSpec 1.6 genera sus
copias para Codex bajo `.codex/skills`.

Usar siempre:

```powershell
powershell -ExecutionPolicy Bypass -File scripts/update-openspec-skills.ps1
```

El script genera temporalmente las skills, valida los seis workflows del perfil
core, los promueve a `.agents/skills` y elimina `.codex/skills`.
