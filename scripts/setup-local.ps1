$ErrorActionPreference = 'Stop'

$workspacePath = Split-Path -Parent $PSScriptRoot
$envPath = Join-Path $workspacePath '.env'

if (-not (Test-Path -LiteralPath $envPath)) {
    Copy-Item -LiteralPath (Join-Path $workspacePath '.env.example') -Destination $envPath
    Write-Host 'Se ha creado .env desde .env.example. Ajusta las contraseñas locales si lo deseas.'
}

$settings = @{}
Get-Content -LiteralPath $envPath | ForEach-Object {
    if ($_ -match '^([^#=]+)=(.*)$') {
        $settings[$matches[1].Trim()] = $matches[2].Trim()
    }
}

$port = $settings['WP_PORT']
$siteUrl = "http://localhost:$port"

Push-Location $workspacePath
try {
    docker compose up -d db wordpress

    Write-Host 'Esperando a que WordPress esté disponible...'
    $ready = $false
    for ($attempt = 1; $attempt -le 60; $attempt++) {
        $containerId = docker compose ps -q wordpress
        if ($containerId) {
            $health = (docker inspect --format='{{if .State.Health}}{{.State.Health.Status}}{{else}}{{.State.Status}}{{end}}' $containerId).Trim()
            if ($health -eq 'healthy') {
                $ready = $true
                break
            }
        }
        Start-Sleep -Seconds 2
    }
    if (-not $ready) { throw 'WordPress no alcanzó el estado healthy.' }

    docker compose --profile tools run --rm wpcli core is-installed
    $isInstalled = $LASTEXITCODE -eq 0
    if (-not $isInstalled) {
        docker compose --profile tools run --rm wpcli core install `
            --url=$siteUrl `
            --title='Egia Kermanentzat' `
            --admin_user=$settings['WP_ADMIN_USER'] `
            --admin_password=$settings['WP_ADMIN_PASSWORD'] `
            --admin_email=$settings['WP_ADMIN_EMAIL'] `
            --skip-email
    }

    docker compose --profile tools run --rm wpcli theme activate kermanentzat-prototype
    docker compose --profile tools run --rm wpcli rewrite structure '/%postname%/' --hard
    docker compose --profile tools run --rm wpcli eval-file wp-content/themes/kermanentzat-prototype/inc/seed.php
    docker compose --profile tools run --rm wpcli rewrite flush --hard

    Write-Host ''
    Write-Host "Euskera (idioma predeterminado): $siteUrl/"
    Write-Host "Castellano: $siteUrl/es/"
    Write-Host "Administración: $siteUrl/wp-admin/"
    Write-Host 'Las credenciales locales están en .env, que Git ignora.'
}
finally {
    Pop-Location
}
