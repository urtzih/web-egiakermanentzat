<?php

defined('ABSPATH') || exit;

function kermanentzat_setup(): void
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('align-wide');
    add_theme_support('responsive-embeds');
    add_theme_support('wp-block-styles');
    add_theme_support('editor-styles');
    add_editor_style('assets/css/editor.css');
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'style', 'script']);
}
add_action('after_setup_theme', 'kermanentzat_setup');

add_action('wp_head', static function (): void {
    echo '<link rel="icon" href="' . esc_url(get_theme_file_uri('assets/images/favicon.svg')) . '" type="image/svg+xml">';
}, 2);

function kermanentzat_assets(): void
{
    $version = wp_get_theme()->get('Version');
    wp_enqueue_style('kermanentzat-main', get_theme_file_uri('assets/css/main.css'), [], $version);
    wp_enqueue_script('kermanentzat-site', get_theme_file_uri('assets/js/site.js'), [], $version, true);
    wp_script_add_data('kermanentzat-site', 'defer', true);
}
add_action('wp_enqueue_scripts', 'kermanentzat_assets');

function kermanentzat_language(): string
{
    $path = trim((string) wp_parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
    return $path === 'es' || str_starts_with($path, 'es/') ? 'es' : 'eu';
}

function kermanentzat_page_map(): array
{
    return [
        'es' => [
            'home' => '/es/',
            'case' => '/es/resumen-del-caso/',
            'support' => '/es/ayuda-y-donaciones/',
            'contact' => '/es/contacto/',
        ],
        'eu' => [
            'home' => '/',
            'case' => '/kasuaren-laburpena/',
            'support' => '/lagundu-eta-ekarpenak/',
            'contact' => '/kontaktua/',
        ],
    ];
}

function kermanentzat_page_key(): string
{
    if (!is_page()) {
        return 'home';
    }
    $slug = get_post_field('post_name', get_queried_object_id());
    return match ($slug) {
        'resumen-del-caso', 'kasuaren-laburpena' => 'case',
        'ayuda-y-donaciones', 'lagundu-eta-ekarpenak' => 'support',
        'contacto', 'kontaktua' => 'contact',
        default => 'home',
    };
}

function kermanentzat_url(string $language, string $key): string
{
    $map = kermanentzat_page_map();
    return home_url($map[$language][$key] ?? $map[$language]['home']);
}

function kermanentzat_is_home(): bool
{
    return is_front_page() || is_page('es');
}

add_filter('body_class', static function (array $classes): array {
    $classes[] = 'kermanentzat-site';
    $classes[] = 'lang-' . kermanentzat_language();
    $classes[] = 'page-key-' . kermanentzat_page_key();
    return $classes;
});

add_action('template_redirect', static function (): void {
    if (is_admin()) {
        return;
    }

    $path = '/' . trim((string) wp_parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/') . '/';
    $legacyEuRoutes = [
        '/eu/' => '/',
        '/eu/kasuaren-laburpena/' => '/kasuaren-laburpena/',
        '/eu/lagundu-eta-ekarpenak/' => '/lagundu-eta-ekarpenak/',
        '/eu/kontaktua/' => '/kontaktua/',
    ];

    if (isset($legacyEuRoutes[$path])) {
        wp_safe_redirect(home_url($legacyEuRoutes[$path]), 301);
        exit;
    }

    if (isset($_GET['variant']) && kermanentzat_is_home()) {
        wp_safe_redirect(remove_query_arg('variant'));
        exit;
    }
});

add_action('wp_head', static function (): void {
    $language = kermanentzat_language();
    $other = $language === 'es' ? 'eu' : 'es';
    $key = kermanentzat_page_key();
    printf('<link rel="alternate" hreflang="%s" href="%s">', esc_attr($other), esc_url(kermanentzat_url($other, $key)));
    echo '<script>document.documentElement.classList.add("js");</script>';
}, 1);

add_filter('document_title_parts', static function (array $parts): array {
    $parts['site'] = 'Egia Kermanentzat';
    return $parts;
});

remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');
