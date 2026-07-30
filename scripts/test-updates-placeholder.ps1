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

$routes = @(
    @{
        Path = '/berriak/'
        MenuLabel = 'Berriak'
        Heading = 'Berriak'
        StatusPattern = 'Laster eskuragarri'
        Alternate = '/es/actualidad/'
    },
    @{
        Path = '/es/actualidad/'
        MenuLabel = 'Actualidad'
        Heading = 'Actualidad'
        StatusPattern = 'Pr.ximamente disponible'
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
    if (($html -match $headingPattern) -and ($html -match [string]$route.StatusPattern)) {
        Add-Pass "$($route.Path) muestra el aviso temporal"
    } else {
        Add-Failure "$($route.Path) no muestra el título o estado esperado"
    }

    $currentPattern = '<a[^>]+aria-current=["'']page["''][^>]*>\s*' + [regex]::Escape($route.MenuLabel) + '\s*</a>|<a[^>]*>\s*' + [regex]::Escape($route.MenuLabel) + '\s*</a>'
    if ($html -match $currentPattern -and $html -match 'aria-current=["'']page["'']') {
        Add-Pass "$($route.Path) marca la sección actual en el menú"
    } else {
        Add-Failure "$($route.Path) no marca la sección actual en el menú"
    }

    if ($html -match '<meta\s+name=["'']robots["''][^>]+content=["''][^"'']*noindex') {
        Add-Pass "$($route.Path) publica noindex"
    } else {
        Add-Failure "$($route.Path) no publica noindex"
    }

    if ($html -match ('hreflang=["''](?:eu|es)["''][^>]+href=["''][^"'']*' + [regex]::Escape($route.Alternate))) {
        Add-Pass "$($route.Path) enlaza su traducción"
    } else {
        Add-Failure "$($route.Path) no enlaza su traducción"
    }
}

foreach ($sitemapPath in @('/sitemap-eu.xml', '/sitemap-es.xml')) {
    $sitemap = Invoke-WebRequest -UseBasicParsing -Uri "$BaseUrl$sitemapPath"
    if ([string]$sitemap.Content -match '/berriak/|/es/actualidad/') {
        Add-Failure "$sitemapPath incluye el marcador temporal"
    } else {
        Add-Pass "$sitemapPath excluye el marcador temporal"
    }
}

Write-Host "Pruebas superadas: $($passes.Count)"
if ($failures.Count -gt 0) {
    Write-Host "Fallos: $($failures.Count)" -ForegroundColor Red
    $failures | ForEach-Object { Write-Host " - $_" -ForegroundColor Red }
    exit 1
}

Write-Host 'Actualidad / Berriak: OK' -ForegroundColor Green
