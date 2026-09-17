$ErrorActionPreference = 'Stop'
if (Test-Path variable:PSNativeCommandUseErrorActionPreference) {
    $PSNativeCommandUseErrorActionPreference = $false
}

$workspacePath = Split-Path -Parent $PSScriptRoot
Push-Location $workspacePath
try {
    docker compose config --quiet
    if ($LASTEXITCODE -ne 0) { throw 'La configuración de Docker Compose no es válida.' }

    docker compose up -d db wordpress
    if ($LASTEXITCODE -ne 0) { throw 'No se pudo iniciar WordPress.' }

    docker compose exec -T wordpress sh -lc "find /var/www/html/wp-content/plugins/kermanentzat-editorial /var/www/html/wp-content/themes/kermanentzat-prototype -type f -name '*.php' -print0 | xargs -0 -n1 php -l"
    if ($LASTEXITCODE -ne 0) { throw 'Falló la validación de sintaxis PHP.' }

    docker compose --profile tools run --rm wpcli plugin activate kermanentzat-editorial
    if ($LASTEXITCODE -ne 0) { throw 'No se pudo activar el plugin editorial.' }

    docker compose --profile tools run --rm wpcli kermanentzat editorial verify
    if ($LASTEXITCODE -ne 0) { throw 'Falló la verificación editorial en WordPress.' }

    docker compose --profile tools run --rm wpcli kermanentzat editorial migrate --strict --force
    if ($LASTEXITCODE -ne 0) { throw 'Falló la migración editorial.' }
    $secondMigration = docker compose --progress quiet --profile tools run --rm wpcli kermanentzat editorial migrate --dry-run --strict --force 2>$null | Out-String
    if ($LASTEXITCODE -ne 0 -or $secondMigration -notmatch '0 operaciones planificadas') { throw 'La migración no es idempotente.' }

    $caseId = (docker compose --progress quiet --profile tools run --rm wpcli post list --post_type=page --name=kasuaren-laburpena --field=ID 2>$null | Out-String).Trim()
    if ($caseId -notmatch '^\d+$') { throw 'No se pudo localizar la página de resumen.' }
    $page = docker compose --progress quiet --profile tools run --rm wpcli post get $caseId --field=content 2>$null | Out-String
    docker compose --profile tools run --rm wpcli eval-file wp-content/themes/kermanentzat-prototype/inc/seed.php | Out-Null
    $pageAfterSeed = docker compose --progress quiet --profile tools run --rm wpcli post get $caseId --field=content 2>$null | Out-String
    if ($page -cne $pageAfterSeed) { throw 'El seed sobrescribió el contenido editorial.' }

    $registry = docker compose --progress quiet --profile tools run --rm wpcli eval 'echo wp_json_encode(kermanentzat_service_registry());' 2>$null | Out-String
    if ($registry -notmatch '"version":"3\.3\.0"') {
        throw 'El registro de servicios no mantiene la versión esperada.'
    }
    $disabledRegistry = docker compose --progress quiet --profile tools run --rm -e KERMANENTZAT_SENDER_APPROVED=false wpcli eval 'echo wp_json_encode(kermanentzat_service_registry());' 2>$null | Out-String
    if ($disabledRegistry -match 'sender_newsletter') {
        throw 'El registro no retira Sender cuando se fuerza su desactivación.'
    }

    $port = '8080'
    $envPath = Join-Path $workspacePath '.env'
    if (Test-Path -LiteralPath $envPath) {
        $portLine = Get-Content -LiteralPath $envPath | Where-Object { $_ -match '^WP_PORT=' } | Select-Object -First 1
        if ($portLine) { $port = ($portLine -split '=', 2)[1].Trim() }
    }
    $subscriptionPublic = $false
    if (Test-Path -LiteralPath $envPath) {
        $flagLine = Get-Content -LiteralPath $envPath | Where-Object { $_ -match '^KERMANENTZAT_SUBSCRIPTION_PUBLIC=' } | Select-Object -First 1
        if ($flagLine) { $subscriptionPublic = (($flagLine -split '=', 2)[1].Trim() -match '^(?i:true|1|yes|on)$') }
    }

    foreach ($route in @('/berriak/', '/es/actualidad/', '/kronologia/', '/es/cronologia/', '/hemeroteka/', '/es/hemeroteca/', '/kasuaren-laburpena/', '/es/resumen-del-caso/')) {
        $response = Invoke-WebRequest -UseBasicParsing -Uri "http://localhost:$port$route" -MaximumRedirection 5
        if ($response.StatusCode -ne 200) { throw "$route no devuelve HTTP 200." }
    }
    foreach ($route in @('/harpidetza/', '/es/suscripcion/')) {
        if ($subscriptionPublic) {
            $subscriptionHtml = [string](Invoke-WebRequest -UseBasicParsing -Uri "http://localhost:$port$route").Content
            if ($subscriptionHtml -notmatch 'page-hero--subscription' -or
                $subscriptionHtml -notmatch 'subscription-wordmark' -or
                ([regex]::Matches($subscriptionHtml, '<h1')).Count -ne 1) {
                throw "$route no conserva el page-hero de suscripción o duplica el encabezado principal."
            }
        } else {
            try {
                Invoke-WebRequest -UseBasicParsing -Uri "http://localhost:$port$route" -MaximumRedirection 0 -ErrorAction Stop | Out-Null
                throw "$route debe quedar oculta cuando KERMANENTZAT_SUBSCRIPTION_PUBLIC=false."
            } catch {
                $status = $_.Exception.Response.StatusCode.value__
                if ($status -ne 404) { throw "$route devuelve HTTP $status; se esperaba 404 con la suscripción oculta." }
            }
        }
    }
    $homeEu = [string](Invoke-WebRequest -UseBasicParsing -Uri "http://localhost:$port/").Content
    $homeEs = [string](Invoke-WebRequest -UseBasicParsing -Uri "http://localhost:$port/es/").Content
    $sitemapEu = [string](Invoke-WebRequest -UseBasicParsing -Uri "http://localhost:$port/sitemap-eu.xml").Content
    $sitemapEs = [string](Invoke-WebRequest -UseBasicParsing -Uri "http://localhost:$port/sitemap-es.xml").Content
    if ($subscriptionPublic) {
        if ($homeEu -notmatch '/harpidetza/' -or $homeEs -notmatch '/es/suscripcion/' -or $sitemapEu -notmatch '/harpidetza/' -or $sitemapEs -notmatch '/es/suscripcion/') {
            throw 'La suscripción pública no aparece en navegación o sitemap.'
        }
    } else {
        if ($homeEu -match '/harpidetza/' -or $homeEs -match '/es/suscripcion/' -or $sitemapEu -match '/harpidetza/' -or $sitemapEs -match '/es/suscripcion/') {
            throw 'La suscripción oculta sigue enlazada en navegación o sitemap.'
        }
    }

    $updatesEu = [string](Invoke-WebRequest -UseBasicParsing -Uri "http://localhost:$port/berriak/").Content
    $updatesEs = [string](Invoke-WebRequest -UseBasicParsing -Uri "http://localhost:$port/es/actualidad/").Content
    foreach ($updatesPage in @(
        @{ Html = $updatesEu; Hero = 'BERRIAK'; Archive = 'argitalpenak'; Share = 'Partekatu'; Read = 'Irakurri albistea' },
        @{ Html = $updatesEs; Hero = 'ACTUALIDAD'; Archive = 'publicaciones'; Share = 'Compartir'; Read = 'Leer la noticia' }
    )) {
        if (([regex]::Matches($updatesPage.Html, 'page-hero--updates')).Count -ne 1 -or
            ([regex]::Matches($updatesPage.Html, '<h1\b')).Count -ne 1 -or
            ([regex]::Matches($updatesPage.Html, 'class="kerman-card ')).Count -lt 2 -or
            $updatesPage.Html -notmatch [regex]::Escape($updatesPage.Hero) -or
            $updatesPage.Html -notmatch ('id="' + $updatesPage.Archive + '"') -or
            $updatesPage.Html -notmatch [regex]::Escape($updatesPage.Share) -or
            $updatesPage.Html -notmatch [regex]::Escape($updatesPage.Read) -or
            $updatesPage.Html -notmatch 'kerman-card__read-link' -or
            $updatesPage.Html -match 'kerman-card__title"><a' -or
            $updatesPage.Html -notmatch 'data-share-root' -or
            $updatesPage.Html -notmatch 'data-share-url="https://orain\.eus/' -or
            $updatesPage.Html -notmatch 'data-share-url="https://gasteizberri\.com/2026/08/alcaldesa-agresiones-mitika-no-me-consta/' -or
            $updatesPage.Html -notmatch 'GasteizBerri' -or
            $updatesPage.Html -notmatch 'wa\.me/\?text=' -or
            $updatesPage.Html -notmatch 'facebook\.com/sharer/sharer\.php' -or
            $updatesPage.Html -notmatch 'kermanentzat-editorial/assets/editorial\.js') {
            throw 'La pÃ¡gina de actualidad no contiene una Ãºnica hero ni la interfaz bilingÃ¼e de comparticiÃ³n esperada.'
        }
    }
    if ($updatesEu -match 'Atala eraikitzen' -or $updatesEs -match 'SecciÃ³n en construcciÃ³n') {
        throw 'La hero de actualidad conserva el mensaje provisional de construcciÃ³n.'
    }
    $caseEu = [string](Invoke-WebRequest -UseBasicParsing -Uri "http://localhost:$port/kasuaren-laburpena/").Content
    $caseEs = [string](Invoke-WebRequest -UseBasicParsing -Uri "http://localhost:$port/es/resumen-del-caso/").Content
    if (([regex]::Matches($caseEu, 'kerman-timeline__item')).Count -lt 10 -or $caseEu -match 'case-timeline') { throw 'La cronología EU no quedó estructurada.' }
    if (([regex]::Matches($caseEs, 'kerman-timeline__item')).Count -lt 10 -or $caseEs -match 'case-timeline') { throw 'La cronología ES no quedó estructurada.' }
    foreach ($caseHtml in @($caseEu, $caseEs)) {
        if ($caseHtml -notmatch 'kerman-timeline-section content-band content-band--light' -or
            $caseHtml -notmatch 'content-wrap split-grid' -or
            $caseHtml -notmatch 'class="section-heading"' -or
            $caseHtml -notmatch 'kerman-timeline__body') {
            throw 'La cronología dinámica no conserva la composición editorial del resumen.'
        }
    }
    $timelineCss = Get-Content -Raw 'wp-content/plugins/kermanentzat-editorial/assets/editorial.css'
    if ($timelineCss -match 'border-left:\s*2px' -or $timelineCss -match 'margin:\s*0\s+0\s+2\.25rem') {
        throw 'La cronología ha recuperado estilos genéricos ajenos al sistema visual.'
    }

    New-Item -ItemType Directory -Force -Path 'tmp' | Out-Null
    python scripts/prepare-sender-import.py tests/fixtures/sender-import.csv --output tmp/sender-import.csv --report tmp/sender-import-report.json
    if ($LASTEXITCODE -ne 0) { throw 'Falló la preparación de la importación.' }
    $report = Get-Content -Raw 'tmp/sender-import-report.json' | ConvertFrom-Json
    if ($report.accepted -ne 1 -or $report.rejected -ne 3) { throw 'La validación del Excel/CSV no produjo el resultado esperado.' }
    $reportText = Get-Content -Raw 'tmp/sender-import-report.json'
    if ($reportText -match '@') { throw 'El informe de rechazo expone direcciones.' }

    openspec validate --all --strict
    if ($LASTEXITCODE -ne 0) { throw 'OpenSpec no es válido.' }

    docker compose --profile tools run --rm `
        -e WORDPRESS_TABLE_PREFIX=kpr_test_20260917_ `
        -e WP_ENVIRONMENT_TYPE=local `
        -v "${workspacePath}\tests\production-release-integration.php:/tmp/production-release-integration.php:ro" `
        -v "${workspacePath}\tools\kermanentzat-production-release:/tmp/release:ro" `
        -v "${workspacePath}\tools\kermanentzat-berriak-update:/tmp/kbu:ro" `
        -v "${workspacePath}\tmp\case-media-public:/tmp/kpr-media:ro" `
        wpcli --skip-wordpress eval-file /tmp/production-release-integration.php
    if ($LASTEXITCODE -ne 0) { throw 'Falló la prueba aislada de publicación y restauración.' }

    Write-Host 'Pruebas editoriales completadas.'
}
finally {
    Pop-Location
}
