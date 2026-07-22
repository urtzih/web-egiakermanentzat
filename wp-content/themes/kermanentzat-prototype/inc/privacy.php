<?php

defined('ABSPATH') || exit;

/**
 * Single source of truth for public legal identity and privacy configuration.
 * Pending values must remain null until documentary evidence is available.
 */
function kermanentzat_legal_config(): array
{
    return [
        'version' => '2026.07.22',
        'last_reviewed' => '22/07/2026',
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
    ];
}

/**
 * Consent/service registry. Necessary services cannot be disabled. Optional
 * adapters may be registered later with the `kermanentzat_optional_services`
 * filter; no preference UI or storage exists while that list stays empty.
 */
function kermanentzat_service_registry(): array
{
    $registry = [
        'version' => '1.0.0',
        'categories' => [
            'necessary' => ['active' => true, 'required' => true, 'configurable' => false],
            'analytics' => ['active' => false, 'required' => false, 'configurable' => true],
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
    ];

    $optional = apply_filters('kermanentzat_optional_services', []);
    $registry['optional_services'] = is_array($optional)
        ? array_values(array_filter($optional, static function ($service) use ($registry): bool {
            if (!is_array($service) || empty($service['id']) || empty($service['category'])) {
                return false;
            }
            $category = (string) $service['category'];
            return $category !== 'necessary'
                && isset($registry['categories'][$category])
                && !empty($service['enabled']);
        }))
        : [];

    return $registry;
}

function kermanentzat_has_optional_services(): bool
{
    return kermanentzat_service_registry()['optional_services'] !== [];
}

function kermanentzat_render_consent_controls(): void
{
    if (!kermanentzat_has_optional_services()) {
        return;
    }

    /**
     * Future adapters must render their accessible consent controls here and
     * load optional services only after the corresponding affirmative choice.
     */
    do_action('kermanentzat_consent_controls', kermanentzat_service_registry());
}
