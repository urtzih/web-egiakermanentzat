[CmdletBinding()]
param(
    [string]$BaseUrl = '',
    [switch]$SkipPhpLint
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
$baseUri = [Uri]$BaseUrl
$unverifiedIdentifier = 'CEK1' + 'L0ZU0A'

$routes = @(
    '/', '/kasuaren-laburpena/', '/lagundu-eta-ekarpenak/', '/kontaktua/',
    '/lege-oharra/', '/pribatutasun-politika/', '/cookie-politika/',
    '/es/', '/es/resumen-del-caso/', '/es/ayuda-y-donaciones/', '/es/contacto/',
    '/es/aviso-legal/', '/es/politica-de-privacidad/', '/es/politica-de-cookies/'
)
$legalRoutes = @(
    '/lege-oharra/', '/pribatutasun-politika/', '/cookie-politika/',
    '/es/aviso-legal/', '/es/politica-de-privacidad/', '/es/politica-de-cookies/'
)

foreach ($route in $routes) {
    $url = "$BaseUrl$route"
    try {
        $response = Invoke-WebRequest -UseBasicParsing -Uri $url -MaximumRedirection 5
    }
    catch {
        Add-Failure "$route no respondió correctamente: $($_.Exception.Message)"
        continue
    }

    if ($response.StatusCode -ne 200) { Add-Failure "$route devolvió HTTP $($response.StatusCode)" }
    else { Add-Pass "$route devuelve 200" }

    if ($response.Headers['Set-Cookie']) { Add-Failure "$route envía Set-Cookie: $($response.Headers['Set-Cookie'])" }
    else { Add-Pass "$route no envía Set-Cookie" }

    $requiredHeaders = @{
        'X-Content-Type-Options' = 'nosniff'
        'Referrer-Policy' = 'no-referrer'
        'X-Frame-Options' = 'DENY'
    }
    foreach ($header in $requiredHeaders.GetEnumerator()) {
        if ([string]$response.Headers[$header.Key] -ne $header.Value) {
            Add-Failure "$route no contiene $($header.Key): $($header.Value)"
        }
    }
    $permissions = [string]$response.Headers['Permissions-Policy']
    if ($permissions -notmatch 'camera=\(\)' -or $permissions -notmatch 'payment=\(\)') {
        Add-Failure "$route tiene una Permissions-Policy incompleta"
    }
    $csp = [string]$response.Headers['Content-Security-Policy']
    if ($csp -notmatch "default-src 'self'" -or $csp -notmatch "frame-ancestors 'none'" -or $csp -notmatch "connect-src 'self'") {
        Add-Failure "$route tiene una CSP inesperada"
    }

    $html = [string]$response.Content
    if ($html -match [regex]::Escape($unverifiedIdentifier)) {
        Add-Failure "$route publica el identificador registral no acreditado"
    }
    if ($html -match '(?i)<iframe\b|googletagmanager|google-analytics|doubleclick|facebook\.com/tr|<img[^>]+(?:pixel|tracking)') {
        Add-Failure "$route contiene un iframe o marcador de seguimiento"
    }
    $inlineScripts = [regex]::Matches($html, '(?is)<script\b[^>]*>(.*?)</script>')
    foreach ($scriptMatch in $inlineScripts) {
        $scriptBody = $scriptMatch.Groups[1].Value
        if ($scriptBody -match '(?i)document\.cookie|\blocalStorage\b|\bsessionStorage\b|\bindexedDB\b|\bsendBeacon\b|s\.w\.org|googletagmanager|google-analytics') {
            Add-Failure "$route contiene almacenamiento o un destino externo en un script inline"
        }
    }
    if ($html -match '(?i)(class|id)=["''][^"'']*(cookie-banner|consent-banner|consent-panel)|data-consent-storage') {
        Add-Failure "$route renderiza controles o almacenamiento de consentimiento sin servicios opcionales"
    }

    $resourceMatches = [regex]::Matches($html, '(?is)<(?:script|img|iframe|source|link)\b[^>]*?\s(?:src|href)=["'']([^"'']+)["'']')
    foreach ($match in $resourceMatches) {
        $resource = $match.Groups[1].Value
        if ($resource.StartsWith('data:') -or $resource.StartsWith('#')) { continue }
        try { $resourceUri = [Uri]::new($baseUri, $resource) }
        catch { Add-Failure "$route contiene un recurso no interpretable: $resource"; continue }
        if ($resourceUri.Host -ne $baseUri.Host -or $resourceUri.Port -ne $baseUri.Port) {
            Add-Failure "$route carga automáticamente un recurso externo: $resource"
        }
    }

    if ($legalRoutes -contains $route) {
        $expectedFooter = if ($route.StartsWith('/es/')) {
            @('/es/aviso-legal/', '/es/politica-de-privacidad/', '/es/politica-de-cookies/')
        } else {
            @('/lege-oharra/', '/pribatutasun-politika/', '/cookie-politika/')
        }
        foreach ($href in $expectedFooter) {
            $footerPattern = 'href=["''](?:' + [regex]::Escape($BaseUrl) + ')?' + [regex]::Escape($href) + '["'']'
            if ($html -notmatch $footerPattern) {
                Add-Failure "$route no enlaza $href desde el pie"
            }
        }
        if ($html -notmatch 'hreflang=["'']eu["'']' -or $html -notmatch 'hreflang=["'']es["'']') {
            Add-Failure "$route no expone hreflang recíproco"
        }
        if ($html -notmatch 'class=["'']language-switch["''][^>]+hreflang=') {
            Add-Failure "$route no contiene selector de idioma contextual"
        }
    }
}

$codeFiles = Get-ChildItem -LiteralPath (Join-Path $workspacePath 'wp-content') -Recurse -File |
    Where-Object { $_.Extension -in @('.php', '.js') -and $_.Name -ne 'legal-content.php' }
$forbiddenCode = @('document\.cookie', '\blocalStorage\b', '\bsessionStorage\b', '\bindexedDB\b', '\bsendBeacon\b', 'googletagmanager', 'google-analytics', '\bgtag\s*\(', '\bGTM-[A-Z0-9]')
foreach ($file in $codeFiles) {
    $source = Get-Content -Raw -LiteralPath $file.FullName
    foreach ($pattern in $forbiddenCode) {
        if ($source -match $pattern) { Add-Failure "$($file.FullName) contiene el patrón no autorizado $pattern" }
    }
}

$styleFiles = Get-ChildItem -LiteralPath (Join-Path $workspacePath 'wp-content') -Recurse -Filter '*.css' -File
foreach ($file in $styleFiles) {
    $source = Get-Content -Raw -LiteralPath $file.FullName
    if ($source -match '(?i)@import\s+(?:url\()?\s*["'']?(?:https?:)?//|url\(\s*["'']?(?:https?:)?//') {
        Add-Failure "$($file.FullName) contiene una importación o recurso CSS externo"
    }
}

$repositoryFiles = Get-ChildItem -LiteralPath $workspacePath -Recurse -File -Force |
    Where-Object { $_.FullName -notmatch '[\\/]\.git[\\/]' -and $_.FullName -notmatch '[\\/]tmp[\\/]' }
foreach ($file in $repositoryFiles) {
    try {
        if ((Get-Content -Raw -LiteralPath $file.FullName -ErrorAction Stop) -match [regex]::Escape($unverifiedIdentifier)) {
            Add-Failure "El identificador registral no acreditado sigue en $($file.FullName)"
        }
    } catch { }
}

if (-not $SkipPhpLint) {
    Push-Location $workspacePath
    try {
        $phpFiles = Get-ChildItem -LiteralPath (Join-Path $workspacePath 'wp-content') -Recurse -Filter '*.php' -File
        foreach ($file in $phpFiles) {
            $relative = $file.FullName.Substring($workspacePath.Length).TrimStart('\').Replace('\', '/')
            $containerPath = "/var/www/html/$relative"
            $lintOutput = docker compose exec -T wordpress php -l $containerPath 2>&1
            if ($LASTEXITCODE -ne 0) { Add-Failure "PHP inválido en $relative`: $lintOutput" }
        }
        if ($failures.Count -eq 0) { Add-Pass 'Sintaxis PHP válida' }
    }
    finally { Pop-Location }
}

Write-Host "Pruebas superadas: $($passes.Count)"
if ($failures.Count -gt 0) {
    Write-Host "Fallos: $($failures.Count)" -ForegroundColor Red
    $failures | ForEach-Object { Write-Host " - $_" -ForegroundColor Red }
    exit 1
}

Write-Host 'Privacidad: OK' -ForegroundColor Green
