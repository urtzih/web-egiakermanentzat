<?php

defined('ABSPATH') || exit;

require_once get_theme_file_path('inc/privacy.php');

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

add_action('after_setup_theme', static function (): void {
    remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
    remove_action('wp_footer', 'wp_enqueue_global_styles', 1);
    remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');
}, 20);

add_action('wp_head', static function (): void {
    echo '<link rel="icon" href="' . esc_url(get_theme_file_uri('assets/images/favicon.svg')) . '" type="image/svg+xml">';
}, 2);

function kermanentzat_assets(): void
{
    $theme_version = wp_get_theme()->get('Version');
    $style_path = get_theme_file_path('assets/css/main.css');
    $script_path = get_theme_file_path('assets/js/site.js');
    $style_version = is_file($style_path) ? (string) filemtime($style_path) : $theme_version;
    $script_version = is_file($script_path) ? (string) filemtime($script_path) : $theme_version;

    wp_enqueue_style('kermanentzat-main', get_theme_file_uri('assets/css/main.css'), [], $style_version);
    wp_enqueue_script('kermanentzat-site', get_theme_file_uri('assets/js/site.js'), [], $script_version, true);
    wp_script_add_data('kermanentzat-site', 'defer', true);

    if (kermanentzat_has_optional_service('google_analytics_4')) {
        $consent_path = get_theme_file_path('assets/js/consent.js');
        $consent_version = is_file($consent_path) ? (string) filemtime($consent_path) : $theme_version;
        wp_enqueue_script('kermanentzat-consent', get_theme_file_uri('assets/js/consent.js'), [], $consent_version, true);
        wp_script_add_data('kermanentzat-consent', 'defer', true);
    }
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
            'legal' => '/es/aviso-legal/',
            'privacy' => '/es/politica-de-privacidad/',
            'cookies' => '/es/politica-de-cookies/',
        ],
        'eu' => [
            'home' => '/',
            'case' => '/kasuaren-laburpena/',
            'support' => '/lagundu-eta-ekarpenak/',
            'contact' => '/kontaktua/',
            'legal' => '/lege-oharra/',
            'privacy' => '/pribatutasun-politika/',
            'cookies' => '/cookie-politika/',
        ],
    ];
}

function kermanentzat_page_meta(): array
{
    return [
        'es' => [
            'home' => [
                'title' => 'Memoria, verdad y justicia para Kerman',
                'description' => 'Espacio de Egia Kermanentzat para preservar la memoria de Kerman, explicar el caso, pedir justicia y facilitar apoyo colectivo.',
            ],
            'case' => [
                'title' => 'Resumen del caso',
                'description' => 'Resumen de lo sucedido el 23 de febrero de 2025 y de la evolución judicial conocida del caso de Kerman Villate Beitia.',
            ],
            'support' => [
                'title' => 'Ayuda y donaciones',
                'description' => 'Consulta cómo apoyar a Egia Kermanentzat, realizar una aportación y conocer el destino general de los fondos.',
            ],
            'contact' => [
                'title' => 'Contacto',
                'description' => 'Canales de contacto de Egia Kermanentzat para prensa, colaboración, documentación, apoyo y consultas.',
            ],
            'legal' => [
                'title' => 'Aviso legal',
                'description' => 'Información legal, identificación de la asociación y condiciones básicas del sitio web de Egia Kermanentzat.',
            ],
            'privacy' => [
                'title' => 'Política de privacidad',
                'description' => 'Cómo trata Egia Kermanentzat los datos personales, con qué finalidades y qué derechos puede ejercer cada persona usuaria.',
            ],
            'cookies' => [
                'title' => 'Política de cookies',
                'description' => 'Información sobre cookies, almacenamiento local y opciones de consentimiento en el sitio web de Egia Kermanentzat.',
            ],
        ],
        'eu' => [
            'home' => [
                'title' => 'Kermanentzat memoria, egia eta justizia',
                'description' => 'Egia Kermanentzat elkartearen gunea, Kermanen memoria zaindu, kasua azaldu, justizia eskatu eta babes kolektiboa errazteko.',
            ],
            'case' => [
                'title' => 'Kasuaren laburpena',
                'description' => '2025eko otsailaren 23an gertatutakoaren eta kasuaren ibilbide judizial ezagunaren laburpen argia.',
            ],
            'support' => [
                'title' => 'Lagundu eta ekarpenak',
                'description' => 'Ikusi nola lagundu Egia Kermanentzat, nola egin ekarpena eta zertarako erabiltzen diren funtsak modu orokorrean.',
            ],
            'contact' => [
                'title' => 'Kontaktua',
                'description' => 'Egia Kermanentzat elkartearekin harremanetan jartzeko bideak: prentsa, lankidetza, dokumentazioa eta babesa.',
            ],
            'legal' => [
                'title' => 'Lege-oharra',
                'description' => 'Egia Kermanentzat webguneko lege-informazioa, elkartearen identifikazioa eta oinarrizko baldintzak.',
            ],
            'privacy' => [
                'title' => 'Pribatutasun-politika',
                'description' => 'Egia Kermanentzatek datu pertsonalak nola tratatzen dituen, zertarako erabiltzen dituen eta zer eskubide dauden.',
            ],
            'cookies' => [
                'title' => 'Cookie-politika',
                'description' => 'Cookieei, biltegiratze lokalari eta baimen-aukerei buruzko informazioa Egia Kermanentzat webgunean.',
            ],
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
        'aviso-legal', 'lege-oharra' => 'legal',
        'politica-de-privacidad', 'pribatutasun-politika' => 'privacy',
        'politica-de-cookies', 'cookie-politika' => 'cookies',
        default => 'home',
    };
}

function kermanentzat_current_url(): string
{
    $request_uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
    $path = (string) wp_parse_url($request_uri, PHP_URL_PATH);
    $path = $path !== '' ? $path : '/';
    return home_url($path);
}

function kermanentzat_meta_for_current_page(): array
{
    $language = kermanentzat_language();
    $key = kermanentzat_page_key();
    $meta = kermanentzat_page_meta();
    return $meta[$language][$key] ?? $meta[$language]['home'];
}

function kermanentzat_should_block_indexing(): bool
{
    $allow_local_indexing = defined('KERMANENTZAT_LOCAL_INDEXING') && KERMANENTZAT_LOCAL_INDEXING;
    return wp_get_environment_type() !== 'production' && !$allow_local_indexing;
}

add_filter('wp_robots', static function (array $robots): array {
    if (kermanentzat_should_block_indexing()) {
        $robots['noindex'] = true;
        $robots['nofollow'] = true;
        $robots['noarchive'] = true;
        $robots['nosnippet'] = true;
        return $robots;
    }

    $robots['max-snippet'] = '150';
    $robots['max-image-preview'] = 'large';
    return $robots;
}, 20);

function kermanentzat_portrait_image_url(): string
{
    return get_theme_file_uri('assets/images/kerman-portrait-clean.png');
}

function kermanentzat_social_image(): array
{
    $language = kermanentzat_language();

    if (kermanentzat_is_home()) {
        return [
            'url' => get_theme_file_uri('assets/images/social-card-' . $language . '-v1.png'),
            'type' => 'image/png',
            'width' => 1200,
            'height' => 630,
            'alt' => $language === 'es'
                ? 'Tarjeta de Egia Kermanentzat con el retrato de Kerman y el lema Memoria, verdad y justicia para Kerman'
                : 'Egia Kermanentzaten txartela, Kermanen erretratuarekin eta Kermanentzat memoria, egia eta justizia leloarekin',
        ];
    }

    return [
        'url' => kermanentzat_portrait_image_url(),
        'type' => 'image/png',
        'width' => 717,
        'height' => 762,
        'alt' => $language === 'es'
            ? 'Retrato de Kerman en blanco y negro'
            : 'Kermanen zuri-beltzeko erretratua',
    ];
}

function kermanentzat_social_image_url(): string
{
    return kermanentzat_social_image()['url'];
}

function kermanentzat_preload_image_url(): string
{
    return get_theme_file_uri('assets/images/kerman-portrait-clean.webp');
}

function kermanentzat_url(string $language, string $key): string
{
    $map = kermanentzat_page_map();
    return home_url($map[$language][$key] ?? $map[$language]['home']);
}

function kermanentzat_public_origin(): string
{
    return 'https://egiakermanentzat.eus';
}

function kermanentzat_sitemap_routes(): array
{
    return [
        'eu' => [
            '/',
            '/kasuaren-laburpena/',
            '/lagundu-eta-ekarpenak/',
            '/kontaktua/',
            '/lege-oharra/',
            '/pribatutasun-politika/',
            '/cookie-politika/',
        ],
        'es' => [
            '/es/',
            '/es/resumen-del-caso/',
            '/es/ayuda-y-donaciones/',
            '/es/contacto/',
            '/es/aviso-legal/',
            '/es/politica-de-privacidad/',
            '/es/politica-de-cookies/',
        ],
    ];
}

function kermanentzat_sitemap_entries(string $language): array
{
    $routes = kermanentzat_sitemap_routes()[$language] ?? [];
    $entries = [];

    foreach ($routes as $path) {
        $page = $path === '/'
            ? get_post((int) get_option('page_on_front'))
            : get_page_by_path(trim($path, '/'));
        if (!$page instanceof WP_Post || $page->post_status !== 'publish') {
            continue;
        }

        $entries[] = [
            'loc' => kermanentzat_public_origin() . $path,
            'lastmod' => get_post_modified_time('c', true, $page),
        ];
    }

    return $entries;
}

function kermanentzat_render_sitemap(string $path): void
{
    status_header(200);
    nocache_headers();
    header('Content-Type: application/xml; charset=UTF-8', true);
    echo '<?xml version="1.0" encoding="UTF-8"?>';

    if ($path === '/sitemap.xml') {
        echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach (['eu', 'es'] as $language) {
            $entries = kermanentzat_sitemap_entries($language);
            if ($entries === []) {
                continue;
            }
            $lastmod = max(array_column($entries, 'lastmod'));
            printf(
                '<sitemap><loc>%s</loc><lastmod>%s</lastmod></sitemap>',
                esc_xml(kermanentzat_public_origin() . '/sitemap-' . $language . '.xml'),
                esc_xml($lastmod)
            );
        }
        echo '</sitemapindex>';
        exit;
    }

    $language = $path === '/sitemap-es.xml' ? 'es' : 'eu';
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    foreach (kermanentzat_sitemap_entries($language) as $entry) {
        printf(
            '<url><loc>%s</loc><lastmod>%s</lastmod></url>',
            esc_xml($entry['loc']),
            esc_xml($entry['lastmod'])
        );
    }
    echo '</urlset>';
    exit;
}

add_action('template_redirect', static function (): void {
    $path = (string) wp_parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    if (in_array($path, ['/sitemap.xml', '/sitemap-eu.xml', '/sitemap-es.xml'], true)) {
        kermanentzat_render_sitemap($path);
    }
}, 0);

add_filter('wp_sitemaps_enabled', '__return_false');

add_filter('robots_txt', static function (string $output): string {
    $lines = [
        'User-agent: *',
        'Disallow: /wp-admin/',
        'Allow: /wp-admin/admin-ajax.php',
        '',
        'Sitemap: ' . kermanentzat_public_origin() . '/sitemap.xml',
    ];
    return implode("\n", $lines) . "\n";
}, 20);

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
    $key = kermanentzat_page_key();
    printf('<link rel="alternate" hreflang="eu" href="%s">', esc_url(kermanentzat_url('eu', $key)));
    printf('<link rel="alternate" hreflang="es" href="%s">', esc_url(kermanentzat_url('es', $key)));
    printf('<link rel="alternate" hreflang="x-default" href="%s">', esc_url(kermanentzat_url('eu', $key)));
    printf('<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>', esc_url(get_theme_file_uri('assets/fonts/anton-latin.woff2')));
    printf('<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>', esc_url(get_theme_file_uri('assets/fonts/public-sans-latin.woff2')));

    if (kermanentzat_is_home() || $key === 'case') {
        printf('<link rel="preload" href="%s" as="image" fetchpriority="high" type="image/webp">', esc_url(kermanentzat_preload_image_url()));
    }

    echo '<script>document.documentElement.classList.add("js");</script>';
}, 1);

add_action('wp_head', static function (): void {
    $language = kermanentzat_language();
    $meta = kermanentzat_meta_for_current_page();
    $current_url = kermanentzat_current_url();
    $site_name = 'Egia Kermanentzat';
    $social_image = kermanentzat_social_image();
    $image_url = $social_image['url'];
    $title = $meta['title'];
    $description = $meta['description'];
    printf('<meta name="description" content="%s">', esc_attr($description));
    echo '<meta name="theme-color" content="#090909">';
    printf('<link rel="canonical" href="%s">', esc_url($current_url));
    printf('<meta property="og:locale" content="%s">', esc_attr($language === 'es' ? 'es_ES' : 'eu_ES'));
    printf('<meta property="og:site_name" content="%s">', esc_attr($site_name));
    printf('<meta property="og:type" content="%s">', esc_attr(kermanentzat_is_home() ? 'website' : 'article'));
    printf('<meta property="og:title" content="%s">', esc_attr($title));
    printf('<meta property="og:description" content="%s">', esc_attr($description));
    printf('<meta property="og:url" content="%s">', esc_url($current_url));
    printf('<meta property="og:image" content="%s">', esc_url($image_url));
    printf('<meta property="og:image:secure_url" content="%s">', esc_url($image_url));
    printf('<meta property="og:image:type" content="%s">', esc_attr($social_image['type']));
    printf('<meta property="og:image:width" content="%d">', $social_image['width']);
    printf('<meta property="og:image:height" content="%d">', $social_image['height']);
    printf('<meta property="og:image:alt" content="%s">', esc_attr($social_image['alt']));
    echo '<meta name="twitter:card" content="summary_large_image">';
    printf('<meta name="twitter:title" content="%s">', esc_attr($title));
    printf('<meta name="twitter:description" content="%s">', esc_attr($description));
    printf('<meta name="twitter:image" content="%s">', esc_url($image_url));
    printf('<meta name="twitter:image:alt" content="%s">', esc_attr($social_image['alt']));

    $schema = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Organization',
                '@id' => home_url('/#organization'),
                'name' => $site_name,
                'url' => home_url('/'),
                'email' => 'mailto:justiziakermanentzat@gmail.com',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => kermanentzat_portrait_image_url(),
                ],
                'sameAs' => [
                    'https://www.instagram.com/justizia.kermanentzat/',
                ],
            ],
            [
                '@type' => 'WebSite',
                '@id' => home_url('/#website'),
                'url' => home_url('/'),
                'name' => $site_name,
                'inLanguage' => [$language],
                'publisher' => [
                    '@id' => home_url('/#organization'),
                ],
            ],
            [
                '@type' => 'WebPage',
                '@id' => $current_url . '#webpage',
                'url' => $current_url,
                'name' => $title,
                'description' => $description,
                'inLanguage' => $language,
                'isPartOf' => [
                    '@id' => home_url('/#website'),
                ],
                'about' => [
                    '@id' => home_url('/#organization'),
                ],
                'primaryImageOfPage' => [
                    '@type' => 'ImageObject',
                    'url' => $image_url,
                    'width' => $social_image['width'],
                    'height' => $social_image['height'],
                ],
            ],
        ],
    ];

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
}, 5);

add_filter('document_title_parts', static function (array $parts): array {
    $meta = kermanentzat_meta_for_current_page();
    $parts['title'] = $meta['title'];
    $parts['site'] = 'Egia Kermanentzat';
    unset($parts['tagline']);
    return $parts;
});

add_action('wp_enqueue_scripts', static function (): void {
    if (is_admin()) {
        return;
    }

    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('global-styles');
    wp_deregister_script('wp-embed');
}, 20);

remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'rest_output_link_wp_head');
remove_action('template_redirect', 'rest_output_link_header', 11);
remove_action('wp_head', 'wp_oembed_add_discovery_links');
remove_action('wp_head', 'wp_shortlink_wp_head');
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('admin_print_styles', 'print_emoji_styles');
remove_filter('the_content_feed', 'wp_staticize_emoji');
remove_filter('comment_text_rss', 'wp_staticize_emoji');
remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
add_filter('emoji_svg_url', '__return_false');

add_action('admin_post_kermanentzat_seed_content', static function (): void {
    if (!current_user_can('manage_options')) {
        wp_die('Permisos insuficientes.');
    }

    check_admin_referer('kermanentzat_seed_content');
    require get_theme_file_path('inc/seed.php');
    wp_safe_redirect(add_query_arg('kermanentzat_seeded', '1', admin_url('themes.php')));
    exit;
});

add_action('admin_notices', static function (): void {
    if (!current_user_can('manage_options') || get_current_screen()?->base !== 'themes') {
        return;
    }

    if (isset($_GET['kermanentzat_seeded'])) {
        echo '<div class="notice notice-success is-dismissible"><p>Contenido de Egia Kermanentzat sincronizado.</p></div>';
        return;
    }

    $url = wp_nonce_url(admin_url('admin-post.php?action=kermanentzat_seed_content'), 'kermanentzat_seed_content');
    echo '<div class="notice notice-info"><p><strong>Egia Kermanentzat:</strong> ejecuta la sincronizacion inicial para crear o actualizar las paginas bilingues del MVP.</p><p><a class="button button-primary" href="' . esc_url($url) . '">Sincronizar contenido</a></p></div>';
});
