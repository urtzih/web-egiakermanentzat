<?php
/**
 * Plugin Name: Egia Kermanentzat Privacy and Environment Guard
 * Description: Applies public security headers everywhere and isolates non-production external actions.
 */

defined('ABSPATH') || exit;

$kermanentzat_environment_flag = static function (string $name): bool {
    if (defined($name)) {
        $value = constant($name);
    } else {
        $value = getenv($name);
    }

    return is_scalar($value) && filter_var((string) $value, FILTER_VALIDATE_BOOL);
};

if (!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}
if (!defined('AUTOMATIC_UPDATER_DISABLED')) {
    define('AUTOMATIC_UPDATER_DISABLED', true);
}
if (!defined('WP_HTTP_BLOCK_EXTERNAL')) {
    define('WP_HTTP_BLOCK_EXTERNAL', true);
}
if (!defined('WP_ACCESSIBLE_HOSTS')) {
    $kermanentzat_accessible_hosts = ['api.wordpress.org', 'downloads.wordpress.org'];
    if ($kermanentzat_environment_flag('KERMANENTZAT_SENDER_APPROVED')) {
        $kermanentzat_accessible_hosts[] = 'api.sender.net';
    }
    define('WP_ACCESSIBLE_HOSTS', implode(',', $kermanentzat_accessible_hosts));
}

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
    $style_src = ["'self'", "'unsafe-inline'"];
    $img_src = ["'self'", 'data:'];
    $connect_src = ["'self'"];
    $form_action = ["'self'"];
    $frame_src = ["'none'"];
    if ($analytics_enabled) {
        $script_src[] = 'https://www.googletagmanager.com';
        $img_src[] = 'https://www.google-analytics.com';
        $connect_src[] = 'https://www.google-analytics.com';
        $connect_src[] = 'https://region1.google-analytics.com';
    }
    $sender_service = function_exists('kermanentzat_optional_service')
        ? kermanentzat_optional_service('sender_newsletter')
        : null;
    $valid_origins = static function ($origins): array {
        if (!is_array($origins)) {
            return [];
        }
        return array_values(array_filter(array_unique(array_map(static function ($origin): string {
            $origin = trim((string) $origin);
            return preg_match('#^https://[a-z0-9.-]+(?::\d+)?$#i', $origin) === 1 ? $origin : '';
        }, $origins))));
    };
    if (is_array($sender_service)) {
        $script_src = array_merge($script_src, $valid_origins($sender_service['script_origins'] ?? []));
        $style_src = array_merge($style_src, $valid_origins($sender_service['style_origins'] ?? []));
        $img_src = array_merge($img_src, $valid_origins($sender_service['img_origins'] ?? []));
        $connect_src = array_merge($connect_src, $valid_origins($sender_service['connect_origins'] ?? []));
        $form_action = array_merge($form_action, $valid_origins($sender_service['form_origins'] ?? []));
        $sender_frames = $valid_origins($sender_service['frame_origins'] ?? []);
        $frame_src = $sender_frames !== [] ? $sender_frames : ["'none'"];
    }

    $policy = [
        "default-src 'self'",
        "base-uri 'self'",
        "object-src 'none'",
        "frame-ancestors 'none'",
        'form-action ' . implode(' ', array_unique($form_action)),
        'script-src ' . implode(' ', $script_src),
        'style-src ' . implode(' ', $style_src),
        'img-src ' . implode(' ', $img_src),
        "font-src 'self' data:",
        'connect-src ' . implode(' ', $connect_src),
        "media-src 'self'",
        'frame-src ' . implode(' ', $frame_src),
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
            echo '<div class="notice notice-warning"><p><strong>Entorno no productivo:</strong> la indexación y el correo de WordPress están desactivados. Las integraciones externas requieren su aprobación y configuración propias.</p></div>';
            return;
        }

        echo '<div class="notice notice-info"><p><strong>Entorno no productivo:</strong> la indexación está permitida mediante <code>KERMANENTZAT_LOCAL_INDEXING</code>; el correo de WordPress sigue desactivado y las integraciones externas requieren aprobación propia.</p></div>';
    });
}
