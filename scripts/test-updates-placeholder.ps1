[CmdletBinding()]
param(
    [string]$BaseUrl = ''
)

$ErrorActionPreference = 'Stop'
$workspacePath = Split-Path -Parent $PSScriptRoot
$failures = [System.Collections.Generic.List[string]]::new()
$passes = [System.Collections.Generic.List[string]]::new()

function Add-Failure([string]$Message) { $script:failures.Add($Message) }
function Add-Pass([string]$Message) { $script:passes.Add($Message) }

if (-not $BaseUrl) {
    $envPath = Join-Path $workspacePath '.env'
    $port = '8080'
    if (Test-Path -LiteralPath $envPath) {
        $portLine = Get-Content -LiteralPath $envPath | Where-Object { $_ -match '^WP_PORT=' } | Select-Object -First 1
        if ($portLine) { $port = ($portLine -split '=', 2)[1].Trim() }
    }
    $BaseUrl = "http://localhost:$port"
}
$BaseUrl = $BaseUrl.TrimEnd('/')
$isProduction = ([uri]$BaseUrl).Host -eq 'egiakermanentzat.eus'

$routes = @(
    @{
        Path = '/berriak/'
        MenuLabel = 'Berriak'
        Heading = 'Berriak'
        TypeLabel = 'Hemeroteka'
        Date = '2/08/2026'
        SourceUrl = 'https://orain.eus/eu/aktualitatea/gizartea/2026/08/02/testigantza-berriek-agerian-utzi-dituzte-mitikako-atezainek-kerman-villate-hil-aurretik-behin-eta-berriz-egindako-erasoak/'
        OtherLanguageTitle = 'Nuevos testimonios apuntan'
        Alternate = '/es/actualidad/'
    },
    @{
        Path = '/es/actualidad/'
        MenuLabel = 'Actualidad'
        Heading = 'Actualidad'
        TypeLabel = 'Hemeroteca'
        Date = '2/08/2026'
        SourceUrl = 'https://orain.eus/es/actualidad/sociedad/2026/08/02/nuevos-testimonios-apuntan-agresiones-reiteradas-porteros-mitika-antes-la-muerte-kerman-villate/'
        OtherLanguageTitle = 'Testigantza berriek agerian'
        Alternate = '/berriak/'
    }
)

foreach ($route in $routes) {
    try {
        $response = Invoke-WebRequest -UseBasicParsing -Uri "$BaseUrl$($route.Path)" -MaximumRedirection 5
    } catch {
        Add-Failure "$($route.Path) no respondió: $($_.Exception.Message)"
        continue
    }

    $html = [System.Net.WebUtility]::HtmlDecode([string]$response.Content)
    if ($response.StatusCode -eq 200) { Add-Pass "$($route.Path) devuelve 200" }
    else { Add-Failure "$($route.Path) devolvió HTTP $($response.StatusCode)" }

    $headingPattern = '<h1[^>]*>\s*' + [regex]::Escape($route.Heading) + '\s*</h1>'
    if (($html -match $headingPattern) -and ($html -match 'class=["'']kerman-updates["'']') -and ($html -notmatch '(?i)construcci.n|eraikitzen')) {
        Add-Pass "$($route.Path) muestra el archivo dinámico"
    } else {
        Add-Failure "$($route.Path) no muestra el título o estado esperado"
    }

    if (
        $html -match 'kerman-card--press-archive' -and
        $html -match ('<span>\s*' + [regex]::Escape($route.TypeLabel) + '\s*</span>') -and
        $html -match [regex]::Escape($route.Date) -and
        $html -match 'ORAIN\s*.\s*Radio Euskadi'
    ) {
        Add-Pass "$($route.Path) identifica naturaleza, medio y fecha"
    } else {
        Add-Failure "$($route.Path) no publica la atribución periodística completa"
    }

    $sourcePattern = 'href=["'']' + [regex]::Escape($route.SourceUrl) + '["''][^>]*target=["'']_blank["''][^>]*rel=["'']noopener noreferrer["'']'
    if ($html -match $sourcePattern) {
        Add-Pass "$($route.Path) enlaza la fuente lingüística de forma segura"
    } else {
        Add-Failure "$($route.Path) no enlaza la fuente esperada con atributos seguros"
    }

    if ($html -match [regex]::Escape($route.OtherLanguageTitle)) {
        Add-Failure "$($route.Path) mezcla la publicación del otro idioma"
    } else {
        Add-Pass "$($route.Path) limita el archivo al idioma actual"
    }

    if ($html -match 'gaztea\.eus|eitb\.scene7\.com|20250225201739_discoteka-mitika') {
        Add-Failure "$($route.Path) reutiliza una imagen de terceros"
    } else {
        Add-Pass "$($route.Path) no reutiliza recursos multimedia de ORAIN"
    }

    $currentPattern = '<a[^>]+aria-current=["'']page["''][^>]*>\s*' + [regex]::Escape($route.MenuLabel) + '\s*</a>|<a[^>]*>\s*' + [regex]::Escape($route.MenuLabel) + '\s*</a>'
    if ($html -match $currentPattern -and $html -match 'aria-current=["'']page["'']') {
        Add-Pass "$($route.Path) marca la sección actual en el menú"
    } else {
        Add-Failure "$($route.Path) no marca la sección actual en el menú"
    }

    $hasNoindex = $html -match '<meta\s+name=["'']robots["''][^>]+content=["''][^"'']*noindex'
    if ($isProduction -and $hasNoindex) {
        Add-Failure "$($route.Path) mantiene noindex en producción pese a contener publicaciones"
    } else {
        Add-Pass "$($route.Path) publica la directiva robots adecuada al entorno"
    }

    if ($html -match ('hreflang=["''](?:eu|es)["''][^>]+href=["''][^"'']*' + [regex]::Escape($route.Alternate))) {
        Add-Pass "$($route.Path) enlaza su traducción"
    } else {
        Add-Failure "$($route.Path) no enlaza su traducción"
    }
}

$functionsSource = Get-Content -Raw -LiteralPath (Join-Path $workspacePath 'wp-content\themes\kermanentzat-prototype\functions.php')
if ($functionsSource -match "kermanentzat_page_key\(\)\s*===\s*'updates'") {
    Add-Failure 'functions.php conserva un noindex específico para actualidad'
} else {
    Add-Pass 'functions.php no bloquea la indexación específica de actualidad'
}

foreach ($sitemapPath in @('/sitemap-eu.xml', '/sitemap-es.xml')) {
    $sitemap = Invoke-WebRequest -UseBasicParsing -Uri "$BaseUrl$sitemapPath"
    $expectedPath = if ($sitemapPath -eq '/sitemap-eu.xml') { '/berriak/' } else { '/es/actualidad/' }
    if ([string]$sitemap.Content -match [regex]::Escape($expectedPath)) {
        Add-Pass "$sitemapPath incluye la sección con contenido real"
    } else {
        Add-Failure "$sitemapPath no incluye $expectedPath"
    }
}

foreach ($sourceUrl in ($routes | ForEach-Object SourceUrl)) {
    try {
        $sourceResponse = Invoke-WebRequest -UseBasicParsing -Uri $sourceUrl -MaximumRedirection 5
        if ($sourceResponse.StatusCode -eq 200) { Add-Pass "La fuente ORAIN responde 200" }
        else { Add-Failure "La fuente ORAIN devolvió HTTP $($sourceResponse.StatusCode): $sourceUrl" }
    } catch {
        Add-Failure "La fuente ORAIN no respondió: $sourceUrl"
    }
}

Write-Host "Pruebas superadas: $($passes.Count)"
if ($failures.Count -gt 0) {
    Write-Host "Fallos: $($failures.Count)" -ForegroundColor Red
    $failures | ForEach-Object { Write-Host " - $_" -ForegroundColor Red }
    exit 1
}

Write-Host 'Actualidad / Berriak con publicaciones: OK' -ForegroundColor Green
