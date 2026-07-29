<?php
/**
 * Plugin Name: Egia Kermanentzat Privacy and Environment Guard
 * Description: Applies public security headers everywhere and isolates non-production external actions.
 */

defined('ABSPATH') || exit;

$kermanentzat_is_non_production = wp_get_environment_type() !== 'production';
$kermanentzat_allow_local_indexing = defined('KERMANENTZAT_LOCAL_INDEXING') && KERMANENTZAT_LOCAL_INDEXING;
$kermanentzat_block_indexing = $kermanentzat_is_non_production && !$kermanentzat_allow_local_indexing;

add_action('send_headers', static function (): void {
    if (is_admin() || ($GLOBALS['pagenow'] ?? '') === 'wp-login.php') {
        return;
    }

    header_remove('X-Powered-By');
    header('X-Content-Type-Options: nosniff', true);
    header('Referrer-Policy: no-referrer', true);
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=()', true);
    header('X-Frame-Options: DENY', true);
    if (wp_get_environment_type() === 'production' && is_ssl()) {
        header('Strict-Transport-Security: max-age=31536000', true);
    }

    $analytics_enabled = function_exists('kermanentzat_has_optional_service')
        && kermanentzat_has_optional_service('google_analytics_4');
    $script_src = ["'self'", "'unsafe-inline'"];
    $img_src = ["'self'", 'data:'];
    $connect_src = ["'self'"];
    if ($analytics_enabled) {
        $script_src[] = 'https://www.googletagmanager.com';
        $img_src[] = 'https://www.google-analytics.com';
        $connect_src[] = 'https://www.google-analytics.com';
        $connect_src[] = 'https://region1.google-analytics.com';
    }

    $policy = [
        "default-src 'self'",
        "base-uri 'self'",
        "object-src 'none'",
        "frame-ancestors 'none'",
        "form-action 'self'",
        'script-src ' . implode(' ', $script_src),
        "style-src 'self' 'unsafe-inline'",
        'img-src ' . implode(' ', $img_src),
        "font-src 'self' data:",
        'connect-src ' . implode(' ', $connect_src),
        "media-src 'self'",
        "frame-src 'none'",
        "manifest-src 'self'",
    ];
    header('Content-Security-Policy: ' . implode('; ', $policy), true);
});

if ($kermanentzat_block_indexing) {
    add_filter('wp_robots', static function (array $robots): array {
        $robots['noindex'] = true;
        $robots['nofollow'] = true;
        $robots['noarchive'] = true;
        $robots['nosnippet'] = true;
        return $robots;
    });

    add_action('send_headers', static function (): void {
        header('X-Robots-Tag: noindex, nofollow, noarchive, nosnippet', true);
    });
}

if ($kermanentzat_is_non_production) {
    add_filter('pre_wp_mail', static fn () => false, PHP_INT_MAX);
    add_filter('xmlrpc_enabled', '__return_false');
    add_filter('wp_is_application_passwords_available', '__return_false');

    add_action('admin_notices', static function () use ($kermanentzat_block_indexing): void {
        if ($kermanentzat_block_indexing) {
            echo '<div class="notice notice-warning"><p><strong>Entorno local:</strong> la indexación, el correo y las integraciones externas están desactivados.</p></div>';
            return;
        }

        echo '<div class="notice notice-info"><p><strong>Entorno local:</strong> la indexación está permitida mediante <code>KERMANENTZAT_LOCAL_INDEXING</code>, pero el correo y las integraciones externas siguen desactivados.</p></div>';
    });
}
