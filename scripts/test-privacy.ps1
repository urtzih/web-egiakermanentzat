[CmdletBinding()]
param(
    [string]$BaseUrl = '',
    [ValidateSet('inactive', 'active')]
    [string]$ConsentService = 'inactive',
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
    '/', '/kasuaren-laburpena/', '/berriak/', '/lagundu-eta-ekarpenak/', '/kontaktua/',
    '/lege-oharra/', '/pribatutasun-politika/', '/cookie-politika/',
    '/es/', '/es/resumen-del-caso/', '/es/actualidad/', '/es/ayuda-y-donaciones/', '/es/contacto/',
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
        Add-Failure "$route contiene un iframe o marcador de seguimiento sin consentimiento"
    }
    $inlineScripts = [regex]::Matches($html, '(?is)<script\b[^>]*>(.*?)</script>')
    foreach ($scriptMatch in $inlineScripts) {
        $scriptBody = $scriptMatch.Groups[1].Value
        if ($scriptBody -match '(?i)document\.cookie|\blocalStorage\b|\bsessionStorage\b|\bindexedDB\b|\bsendBeacon\b|s\.w\.org|googletagmanager|google-analytics') {
            Add-Failure "$route contiene almacenamiento o un destino externo en un script inline"
        }
    }
    $rendersConsent = $html -match '(?i)(class|id)=["''][^"'']*(cookie-banner|consent-banner|consent-panel)|data-consent-storage'
    if ($ConsentService -eq 'active' -and -not $rendersConsent) {
        Add-Failure "$route no renderiza consentimiento pese a esperar un servicio opcional activo"
    } elseif ($ConsentService -eq 'inactive' -and $rendersConsent) {
        Add-Failure "$route renderiza consentimiento sin esperar un servicio opcional activo"
    } else {
        Add-Pass "$route coincide con el estado de consentimiento esperado ($ConsentService)"
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

$sitemapIndex = Invoke-WebRequest -UseBasicParsing -Uri "$BaseUrl/sitemap.xml"
if ($sitemapIndex.StatusCode -ne 200 -or [string]$sitemapIndex.Headers['Content-Type'] -notmatch 'application/xml') {
    Add-Failure '/sitemap.xml no devuelve XML con estado 200'
} elseif (
    [string]$sitemapIndex.Content -notmatch 'https://egiakermanentzat\.eus/sitemap-eu\.xml' -or
    [string]$sitemapIndex.Content -notmatch 'https://egiakermanentzat\.eus/sitemap-es\.xml'
) {
    Add-Failure '/sitemap.xml no referencia los dos sitemaps bilingües de producción'
} else {
    Add-Pass 'El índice de sitemap referencia ES/EU'
}

foreach ($sitemapRoute in @('/sitemap-eu.xml', '/sitemap-es.xml')) {
    $sitemapResponse = Invoke-WebRequest -UseBasicParsing -Uri "$BaseUrl$sitemapRoute"
    $sitemapXml = [string]$sitemapResponse.Content
    $urlCount = ([regex]::Matches($sitemapXml, '<url>')).Count
    if ($sitemapResponse.StatusCode -ne 200 -or $urlCount -lt 8) {
        Add-Failure "$sitemapRoute debe contener al menos las ocho URLs estructurales"
    }
    if ($sitemapXml -match '(?i)localhost|/wp-admin/|/author/|/attachment/|<loc>http://') {
        Add-Failure "$sitemapRoute contiene una URL no pública o no HTTPS"
    } else {
        Add-Pass "$sitemapRoute contiene $urlCount URLs HTTPS públicas"
    }
}

$robotsResponse = Invoke-WebRequest -UseBasicParsing -Uri "$BaseUrl/robots.txt"
$robotsText = [string]$robotsResponse.Content
if (
    $robotsText -notmatch 'Disallow: /wp-admin/' -or
    $robotsText -notmatch 'Allow: /wp-admin/admin-ajax\.php' -or
    $robotsText -notmatch 'Sitemap: https://egiakermanentzat\.eus/sitemap\.xml'
) {
    Add-Failure 'robots.txt no declara las reglas y el sitemap de producción'
} else {
    Add-Pass 'robots.txt declara administración y sitemap'
}

$codeFiles = Get-ChildItem -LiteralPath (Join-Path $workspacePath 'wp-content') -Recurse -File |
    Where-Object { $_.Extension -in @('.php', '.js') -and $_.Name -ne 'legal-content.php' }
$forbiddenCode = @('document\.cookie', '\blocalStorage\b', '\bsessionStorage\b', '\bindexedDB\b', '\bsendBeacon\b', 'googletagmanager', 'google-analytics', '\bgtag\s*\(', '\bGTM-[A-Z0-9]')
$consentAdapterFiles = @(
    (Join-Path $workspacePath 'wp-content\themes\kermanentzat-prototype\assets\js\consent.js'),
    (Join-Path $workspacePath 'wp-content\themes\kermanentzat-prototype\inc\privacy.php'),
    (Join-Path $workspacePath 'wp-content\mu-plugins\kermanentzat-prototype-guard.php')
)
foreach ($file in $codeFiles) {
    if ($consentAdapterFiles -contains $file.FullName) { continue }
    $source = Get-Content -Raw -LiteralPath $file.FullName
    foreach ($pattern in $forbiddenCode) {
        if ($source -match $pattern) { Add-Failure "$($file.FullName) contiene el patrón no autorizado $pattern" }
    }
}

$consentSource = Get-Content -Raw -LiteralPath $consentAdapterFiles[0]
if (
    $consentSource -notmatch "analytics_storage:\s*'denied'" -or
    $consentSource -notmatch "ad_storage:\s*'denied'" -or
    $consentSource -notmatch "ad_user_data:\s*'denied'" -or
    $consentSource -notmatch "ad_personalization:\s*'denied'" -or
    $consentSource -notmatch "allow_google_signals:\s*false" -or
    $consentSource -notmatch "allow_ad_personalization_signals:\s*false" -or
    $consentSource -notmatch "cookie_update:\s*false"
) {
    Add-Failure 'El adaptador de Analytics no conserva los valores de privacidad requeridos'
} else {
    Add-Pass 'El adaptador mantiene analítica y publicidad denegadas por defecto'
}
if (
    $consentSource -notmatch "\['copy_iban', 'copy_bank_details'\]" -or
    $consentSource -match "(?is)gtag\('event'.*(copyValue|dataset\.copyValue|data-copy-value)"
) {
    Add-Failure 'Los eventos de copia no están limitados o podrían enviar el contenido copiado'
} else {
    Add-Pass 'Los eventos de copia están limitados y no incluyen valores bancarios'
}
$externalHosts = [regex]::Matches($consentSource, 'https://([^/''"`]+)') |
    ForEach-Object { $_.Groups[1].Value } |
    Sort-Object -Unique
$unexpectedHosts = $externalHosts | Where-Object { $_ -notin @('www.googletagmanager.com') }
if ($unexpectedHosts) {
    Add-Failure "El adaptador contiene destinos externos inesperados: $($unexpectedHosts -join ', ')"
}

$privacySource = Get-Content -Raw -LiteralPath $consentAdapterFiles[1]
if (
    $privacySource -notmatch "'version'\s*=>\s*'3\.2\.0'" -or
    $privacySource -notmatch 'KERMANENTZAT_GA_APPROVED' -or
    $privacySource -notmatch 'KERMANENTZAT_GA_MEASUREMENT_ID' -or
    $privacySource -notmatch "wp_get_environment_type\(\)\s*===\s*'production'"
) {
    Add-Failure 'El registro no exige versión, aprobación, ID válido y producción para Analytics'
} else {
    Add-Pass 'El registro exige aprobación explícita, ID y producción'
}

$seedSource = Get-Content -Raw -LiteralPath (Join-Path $workspacePath 'wp-content\themes\kermanentzat-prototype\inc\seed.php')
$legalContentSource = Get-Content -Raw -LiteralPath (Join-Path $workspacePath 'wp-content\themes\kermanentzat-prototype\inc\legal-content.php')
if (
    $seedSource -notmatch 'Alta de socio/a' -or
    $seedSource -notmatch '<strong>¿Quieres hacerte socio/a\?</strong>' -or
    $seedSource -notmatch 'En el primer correo, indica únicamente tu interés' -or
    $seedSource -notmatch 'Bazkide alta' -or
    $seedSource -notmatch '<strong>Bazkide izan nahi duzu\?</strong>' -or
    $seedSource -notmatch 'Lehen mezuan, adierazi zure interesa bakarrik'
) {
    Add-Failure 'La invitación para hacerse socio/a no conserva el primer contacto minimizado en ES/EU'
} elseif (
    $seedSource -match '(?is)nombre.{0,80}apellidos.{0,80}DNI.{0,80}tel[eé]fono' -or
    $seedSource -match '(?is)izena.{0,80}abizenak.{0,80}NAN.{0,80}telefono'
) {
    Add-Failure 'La página solicita el conjunto completo de datos personales en el primer correo de alta'
} else {
    Add-Pass 'El primer contacto para hacerse socio/a está minimizado en ES/EU'
}
if (
    $legalContentSource -notmatch 'Antes de recabar datos adicionales' -or
    $legalContentSource -notmatch 'Datu gehiago eskatu aurretik'
) {
    Add-Failure 'La política de privacidad no explica el contacto inicial de alta en ES/EU'
} else {
    Add-Pass 'La política de privacidad explica el contacto inicial de alta en ES/EU'
}
if (
    $legalContentSource -notmatch 'Suscripción a novedades' -or
    $legalContentSource -notmatch 'Berrien harpidetza' -or
    $legalContentSource -notmatch 'UAB Sender\.lt' -or
    $legalContentSource -notmatch 'WordPress no almacena la dirección' -or
    $legalContentSource -notmatch 'WordPressek ez du helbidea gordetzen'
) {
    Add-Failure 'La política no describe la suscripción minimizada y condicionada en ES/EU'
} else {
    Add-Pass 'La política describe Sender, minimización y activación condicionada en ES/EU'
}

$subscriptionAdapter = Get-Content -Raw -LiteralPath (Join-Path $workspacePath 'wp-content\plugins\kermanentzat-editorial\assets\subscription.js')
$subscriptionPhp = Get-Content -Raw -LiteralPath (Join-Path $workspacePath 'wp-content\plugins\kermanentzat-editorial\inc\subscriptions.php')
$subscriptionCss = Get-Content -Raw -LiteralPath (Join-Path $workspacePath 'wp-content\plugins\kermanentzat-editorial\assets\editorial.css')
$subscriptionBootstrap = Get-Content -Raw -LiteralPath (Join-Path $workspacePath 'wp-content\plugins\kermanentzat-editorial\kermanentzat-editorial.php')
if (
    $subscriptionAdapter -notmatch 'universal\.js\?explicit=true' -or
    $subscriptionAdapter -notmatch 'senderForms\.render' -or
    $subscriptionAdapter -notmatch 'sender\.on' -or
    $subscriptionAdapter -notmatch 'onSenderFormsLoaded' -or
    $subscriptionAdapter -notmatch 'if \(autoLoad\) showForm\(\)' -or
    $subscriptionPhp -notmatch 'data-sender-form-id' -or
    $subscriptionPhp -notmatch 'data-auto-load' -or
    $subscriptionPhp -notmatch 'kerman-subscription--teaser' -or
    $subscriptionPhp -notmatch '(?s)kerman-subscription__action.*kerman-subscription__intro' -or
    $subscriptionCss -notmatch '(?s)\.content-band--light:has\(\.kerman-subscription--page\).*?background:\s*var\(--color-soft' -or
    $subscriptionCss -match '(?s)\.kerman-subscription__form iframe\s*\{[^}]*min-height' -or
    $subscriptionCss -notmatch '(?s)\.kerman-subscription__form iframe\s*\{[^}]*margin-bottom:\s*-2rem' -or
    $subscriptionPhp -notmatch "config\['sender_form_embed_id'\]" -or
    $subscriptionBootstrap -notmatch "'sender_form_id'\s*=>\s*'epY1RX'" -or
    $subscriptionBootstrap -notmatch "'sender_form_embed_id'\s*=>\s*'msis6hs8epy1rx9k77d'" -or
    $subscriptionAdapter -match 'api\.sender\.net/v2|Authorization|Bearer' -or
    $subscriptionPhp -match 'data-subscription-frame-container'
) {
    Add-Failure 'La suscripción no limita el SDK a la ruta dedicada o podría exponer la API privada'
} else {
    Add-Pass 'La suscripción integra el SDK en la ruta dedicada, usa llamadas locales fuera de ella y no expone la API privada'
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

$nodeCommand = Get-Command node -ErrorAction SilentlyContinue
if ($nodeCommand) {
    $javascriptFiles = Get-ChildItem -LiteralPath (Join-Path $workspacePath 'wp-content') -Recurse -Filter '*.js' -File
    foreach ($file in $javascriptFiles) {
        & $nodeCommand.Source --check $file.FullName 2>&1 | Out-Null
        if ($LASTEXITCODE -ne 0) { Add-Failure "JavaScript inválido en $($file.FullName)" }
    }
    if ($failures.Count -eq 0) { Add-Pass 'Sintaxis JavaScript válida' }
}

Write-Host "Pruebas superadas: $($passes.Count)"
if ($failures.Count -gt 0) {
    Write-Host "Fallos: $($failures.Count)" -ForegroundColor Red
    $failures | ForEach-Object { Write-Host " - $_" -ForegroundColor Red }
    exit 1
}

Write-Host 'Privacidad: OK' -ForegroundColor Green
