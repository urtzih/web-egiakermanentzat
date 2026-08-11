<?php

defined('ABSPATH') || exit;

/**
 * Single source of truth for public legal identity and privacy configuration.
 * Pending values must remain null until documentary evidence is available.
 */
function kermanentzat_legal_config(): array
{
    return [
        'version' => '2026.08.11',
        'last_reviewed' => '11/08/2026',
        'name' => 'Egia Kermanentzat Elkartea',
        'nif' => 'G93797744',
        'address' => 'Zubiegi kalea 16, Bitoriano, 01139 Zuia, Álava',
        'email' => 'justiziakermanentzat@gmail.com',
        'registry_number' => null,
        'registry_status' => 'pending_official_evidence',
        'hosting_provider' => null,
        'hosting_log_retention' => null,
        'gmail_contractual_setup' => null,
        'operational_contacts' => null,
        'donation_tax_status' => null,
        'google_analytics_contractual_review' => null,
        'google_analytics_transfer_safeguards' => null,
        'sender_processor_agreement' => null,
        'sender_transfer_safeguards' => null,
        'sender_retention_review' => null,
        'sender_domain_authentication' => null,
    ];
}

function kermanentzat_environment_value(string $name): string
{
    if (defined($name)) {
        $value = constant($name);
        return is_scalar($value) ? trim((string) $value) : '';
    }

    $value = getenv($name);
    return $value === false ? '' : trim($value);
}

function kermanentzat_environment_flag(string $name): bool
{
    return filter_var(
        kermanentzat_environment_value($name),
        FILTER_VALIDATE_BOOLEAN,
        FILTER_NULL_ON_FAILURE
    ) === true;
}

function kermanentzat_ga_measurement_id(): string
{
    $measurement_id = strtoupper(kermanentzat_environment_value('KERMANENTZAT_GA_MEASUREMENT_ID'));
    return preg_match('/^G-[A-Z0-9]{6,20}$/', $measurement_id) === 1 ? $measurement_id : '';
}

function kermanentzat_analytics_is_enabled(): bool
{
    return wp_get_environment_type() === 'production'
        && kermanentzat_environment_flag('KERMANENTZAT_GA_APPROVED')
        && kermanentzat_ga_measurement_id() !== '';
}

/**
 * Consent/service registry. Optional services are absent until their adapter is
 * explicitly approved and configured in production.
 */
function kermanentzat_service_registry(): array
{
    $analytics_enabled = kermanentzat_analytics_is_enabled();
    $registry = [
        'version' => '3.2.0',
        'categories' => [
            'necessary' => ['active' => true, 'required' => true, 'configurable' => false],
            'analytics' => ['active' => $analytics_enabled, 'required' => false, 'configurable' => true],
            'marketing' => ['active' => false, 'required' => false, 'configurable' => true],
            'preferences' => ['active' => false, 'required' => false, 'configurable' => true],
        ],
        'necessary_services' => [
            'wordpress_administration' => [
                'scope' => 'restricted_admin_only',
                'public_frontend' => false,
                'storage' => ['wordpress_test_cookie', 'wordpress_sec_*', 'wordpress_logged_in_*', 'wp-settings-*'],
            ],
        ],
        'optional_services' => [],
    ];

    if ($analytics_enabled) {
        $registry['optional_services'][] = [
            'id' => 'google_analytics_4',
            'category' => 'analytics',
            'enabled' => true,
            'provider' => 'Google Ireland Limited',
            'purpose' => 'Medición agregada de visitas e interacción con las acciones de apoyo',
            'legal_basis' => 'consent',
            'trigger' => 'affirmative_analytics_consent',
            'storage' => ['_ga', '_ga_*', 'kermanentzat_consent'],
            'retention' => 'GA4: 2 months; consent choice: 6 months',
            'withdrawal' => 'footer_preferences_control',
        ];
    }

    $optional = apply_filters('kermanentzat_optional_services', $registry['optional_services']);
    if (is_array($optional)) {
        foreach ($optional as $service) {
            $category = is_array($service) ? (string) ($service['category'] ?? '') : '';
            if ($category !== 'necessary' && !empty($service['enabled']) && isset($registry['categories'][$category])) {
                $registry['categories'][$category]['active'] = true;
            }
        }
    }
    $registry['optional_services'] = is_array($optional)
        ? array_values(array_filter($optional, static function ($service) use ($registry): bool {
            if (!is_array($service) || empty($service['id']) || empty($service['category'])) {
                return false;
            }
            $category = (string) $service['category'];
            return $category !== 'necessary'
                && isset($registry['categories'][$category])
                && !empty($registry['categories'][$category]['active'])
                && !empty($service['enabled']);
        }))
        : [];

    return $registry;
}

function kermanentzat_has_optional_services(): bool
{
    return kermanentzat_service_registry()['optional_services'] !== [];
}

function kermanentzat_has_optional_service(string $id): bool
{
    return kermanentzat_optional_service($id) !== null;
}

function kermanentzat_optional_service(string $id): ?array
{
    foreach (kermanentzat_service_registry()['optional_services'] as $service) {
        if (($service['id'] ?? '') === $id) {
            return $service;
        }
    }
    return null;
}

function kermanentzat_consent_text(): array
{
    if (kermanentzat_language() === 'es') {
        return [
            'preferences' => 'Preferencias de cookies',
            'preferences_short' => 'Preferencias',
            'title' => 'Analítica opcional',
            'summary' => 'Analytics nos ayuda a mejorar la web si lo aceptas. Rechazarlo no limita ninguna función.',
            'accept' => 'Aceptar',
            'reject' => 'Rechazar',
            'configure' => 'Configurar',
            'dialog_title' => 'Preferencias de privacidad',
            'dialog_intro' => 'Puedes aceptar o rechazar la analítica. Las funciones necesarias del sitio siempre permanecen activas.',
            'necessary_title' => 'Necesarias',
            'necessary_description' => 'Permiten el funcionamiento y la seguridad del sitio. No se pueden desactivar.',
            'analytics_title' => 'Google Analytics',
            'analytics_description' => 'Mide visitas, procedencia aproximada, páginas e interacción. También registra si se copia el IBAN o el bloque bancario, nunca su contenido.',
            'save' => 'Guardar preferencias',
            'cancel' => 'Cancelar',
            'policy' => 'Política de cookies',
        ];
    }

    return [
        'preferences' => 'Cookie-lehentasunak',
        'preferences_short' => 'Lehentasunak',
        'title' => 'Aukerako analitika',
        'summary' => 'Onartzen baduzu, Analytics-ek webgunea hobetzen lagunduko digu. Baztertzeak ez du funtziorik mugatzen.',
        'accept' => 'Onartu',
        'reject' => 'Baztertu',
        'configure' => 'Konfiguratu',
        'dialog_title' => 'Pribatutasun-lehentasunak',
        'dialog_intro' => 'Analitika onartu edo baztertu dezakezu. Webgunearen beharrezko funtzioak beti daude aktibatuta.',
        'necessary_title' => 'Beharrezkoak',
        'necessary_description' => 'Webgunearen funtzionamendua eta segurtasuna ahalbidetzen dituzte. Ezin dira desaktibatu.',
        'analytics_title' => 'Google Analytics',
        'analytics_description' => 'Bisitak, gutxi gorabeherako jatorria, orriak eta interakzioa neurtzen ditu. IBANa edo banku-datuen blokea kopiatzen den ere jasotzen du, baina inoiz ez edukia.',
        'save' => 'Gorde lehentasunak',
        'cancel' => 'Utzi',
        'policy' => 'Cookie-politika',
    ];
}

function kermanentzat_render_consent_controls(string $context = 'banner'): void
{
    if (!kermanentzat_has_optional_service('google_analytics_4')) {
        return;
    }

    $text = kermanentzat_consent_text();
    if ($context === 'footer') {
        printf(
            '<button class="site-footer__consent" type="button" data-consent-open aria-label="%1$s"><span class="site-footer__label--full">%2$s</span><span class="site-footer__label--compact" aria-hidden="true">%3$s</span></button>',
            esc_attr($text['preferences']),
            esc_html($text['preferences']),
            esc_html($text['preferences_short'])
        );
        return;
    }

    $registry = kermanentzat_service_registry();
    $cookie_url = kermanentzat_url(kermanentzat_language(), 'cookies');
    ?>
    <section
        class="consent-banner"
        data-consent-banner
        data-measurement-id="<?php echo esc_attr(kermanentzat_ga_measurement_id()); ?>"
        data-registry-version="<?php echo esc_attr($registry['version']); ?>"
        data-storage-key="kermanentzat_consent"
        data-max-age-days="183"
        aria-labelledby="consent-title"
        hidden
    >
        <div class="consent-banner__content">
            <div>
                <h2 id="consent-title"><?php echo esc_html($text['title']); ?></h2>
                <p><?php echo esc_html($text['summary']); ?></p>
                <div class="consent-banner__links">
                    <a href="<?php echo esc_url($cookie_url); ?>"><?php echo esc_html($text['policy']); ?></a>
                    <button type="button" data-consent-configure><?php echo esc_html($text['configure']); ?></button>
                </div>
            </div>
            <div class="consent-banner__actions">
                <button class="button button--primary" type="button" data-consent-accept><?php echo esc_html($text['accept']); ?></button>
                <button class="button button--primary" type="button" data-consent-reject><?php echo esc_html($text['reject']); ?></button>
            </div>
        </div>
    </section>
    <dialog class="consent-dialog" data-consent-dialog aria-labelledby="consent-dialog-title">
        <form method="dialog" data-consent-form>
            <h2 id="consent-dialog-title"><?php echo esc_html($text['dialog_title']); ?></h2>
            <p><?php echo esc_html($text['dialog_intro']); ?></p>
            <div class="consent-option">
                <div>
                    <strong><?php echo esc_html($text['necessary_title']); ?></strong>
                    <p><?php echo esc_html($text['necessary_description']); ?></p>
                </div>
                <span aria-hidden="true">✓</span>
            </div>
            <label class="consent-option" for="consent-analytics">
                <span>
                    <strong><?php echo esc_html($text['analytics_title']); ?></strong>
                    <span><?php echo esc_html($text['analytics_description']); ?></span>
                </span>
                <input id="consent-analytics" type="checkbox" data-consent-analytics>
            </label>
            <div class="consent-dialog__actions">
                <button class="button button--primary" type="submit" value="save"><?php echo esc_html($text['save']); ?></button>
                <button class="button button--primary" type="button" data-consent-dialog-reject><?php echo esc_html($text['reject']); ?></button>
                <button class="button" type="button" data-consent-dialog-cancel><?php echo esc_html($text['cancel']); ?></button>
            </div>
        </form>
    </dialog>
    <?php
}
