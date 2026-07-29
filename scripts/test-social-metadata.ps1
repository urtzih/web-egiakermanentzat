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

function Assert-Equal([string]$Label, $Actual, $Expected) {
    if ($Actual -ne $Expected) {
        Add-Failure "$Label`: se esperaba '$Expected' y se obtuvo '$Actual'"
    } else {
        Add-Pass $Label
    }
}

function Get-MetaContent([string]$Html, [string]$Name) {
    $pattern = '(?is)<meta\s+(?:property|name)=["'']' + [regex]::Escape($Name) + '["'']\s+content=["'']([^"'']*)["'']'
    $match = [regex]::Match($Html, $pattern)
    if (-not $match.Success) { return $null }
    return [System.Net.WebUtility]::HtmlDecode($match.Groups[1].Value)
}

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

$imageDirectory = Join-Path $workspacePath 'wp-content\themes\kermanentzat-prototype\assets\images'
Add-Type -AssemblyName System.Drawing
foreach ($language in @('eu', 'es')) {
    $imagePath = Join-Path $imageDirectory "social-card-$language-v1.png"
    if (-not (Test-Path -LiteralPath $imagePath)) {
        Add-Failure "No existe social-card-$language-v1.png"
        continue
    }

    $file = Get-Item -LiteralPath $imagePath
    $image = [System.Drawing.Image]::FromFile($file.FullName)
    try {
        Assert-Equal "social-card-$language-v1.png tiene 1200 px de ancho" $image.Width 1200
        Assert-Equal "social-card-$language-v1.png tiene 630 px de alto" $image.Height 630
    } finally {
        $image.Dispose()
    }

    if ($file.Length -le 0 -or $file.Length -gt 1MB) {
        Add-Failure "social-card-$language-v1.png debe pesar entre 1 byte y 1 MB"
    } else {
        Add-Pass "social-card-$language-v1.png tiene un peso adecuado"
    }
}

$routes = @(
    @{
        Path = '/'
        Title = 'Kermanentzat memoria, egia eta justizia'
        Image = 'social-card-eu-v1.png'
        Width = '1200'
        Height = '630'
    },
    @{
        Path = '/es/'
        Title = 'Memoria, verdad y justicia para Kerman'
        Image = 'social-card-es-v1.png'
        Width = '1200'
        Height = '630'
    },
    @{
        Path = '/kasuaren-laburpena/'
        Title = 'Kasuaren laburpena'
        Image = 'kerman-portrait-clean.png'
        Width = '717'
        Height = '762'
    },
    @{
        Path = '/es/resumen-del-caso/'
        Title = 'Resumen del caso'
        Image = 'kerman-portrait-clean.png'
        Width = '717'
        Height = '762'
    }
)

foreach ($route in $routes) {
    $url = "$BaseUrl$($route.Path)"
    try {
        $response = Invoke-WebRequest -UseBasicParsing -Uri $url -MaximumRedirection 5
    } catch {
        Add-Failure "$($route.Path) no respondió: $($_.Exception.Message)"
        continue
    }

    $html = [string]$response.Content
    $imageUrl = Get-MetaContent $html 'og:image'
    Assert-Equal "$($route.Path) publica el título social correcto" (Get-MetaContent $html 'og:title') $route.Title
    Assert-Equal "$($route.Path) publica og:image:secure_url" (Get-MetaContent $html 'og:image:secure_url') $imageUrl
    Assert-Equal "$($route.Path) publica og:image:type" (Get-MetaContent $html 'og:image:type') 'image/png'
    Assert-Equal "$($route.Path) publica og:image:width" (Get-MetaContent $html 'og:image:width') $route.Width
    Assert-Equal "$($route.Path) publica og:image:height" (Get-MetaContent $html 'og:image:height') $route.Height
    Assert-Equal "$($route.Path) reutiliza la imagen en Twitter" (Get-MetaContent $html 'twitter:image') $imageUrl

    if ($imageUrl -notmatch [regex]::Escape($route.Image)) {
        Add-Failure "$($route.Path) no usa $($route.Image): $imageUrl"
    } else {
        Add-Pass "$($route.Path) usa $($route.Image)"
    }

    if (-not (Get-MetaContent $html 'og:description') -or -not (Get-MetaContent $html 'og:image:alt') -or -not (Get-MetaContent $html 'twitter:image:alt')) {
        Add-Failure "$($route.Path) carece de descripción o texto alternativo social"
    } else {
        Add-Pass "$($route.Path) publica descripción y textos alternativos"
    }

    try {
        $imageResponse = Invoke-WebRequest -UseBasicParsing -Uri $imageUrl
        if ($imageResponse.StatusCode -ne 200 -or [string]$imageResponse.Headers['Content-Type'] -notmatch '^image/png') {
            Add-Failure "$($route.Path) referencia una imagen que no responde como PNG"
        } else {
            Add-Pass "$($route.Path) referencia una imagen PNG accesible"
        }
    } catch {
        Add-Failure "$($route.Path) referencia una imagen inaccesible: $($_.Exception.Message)"
    }
}

foreach ($crawler in @(
    'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)',
    'WhatsApp/2.23.20.0 A'
)) {
    try {
        $headers = @{ 'User-Agent' = $crawler }
        $response = Invoke-WebRequest -UseBasicParsing -Uri "$BaseUrl/" -Headers $headers
        if ((Get-MetaContent ([string]$response.Content) 'og:image') -notmatch 'social-card-eu-v1\.png') {
            Add-Failure "La portada no entrega la tarjeta social al crawler $crawler"
        } else {
            Add-Pass "La portada entrega la tarjeta social al crawler $crawler"
        }
    } catch {
        Add-Failure "El crawler $crawler no pudo leer la portada: $($_.Exception.Message)"
    }
}

Write-Host "Pruebas superadas: $($passes.Count)"
if ($failures.Count -gt 0) {
    Write-Host "Fallos: $($failures.Count)" -ForegroundColor Red
    $failures | ForEach-Object { Write-Host " - $_" -ForegroundColor Red }
    exit 1
}

Write-Host 'Metadatos sociales: OK' -ForegroundColor Green
