<?php

namespace Kermanentzat\Editorial;

defined('ABSPATH') || exit;

const MIGRATION_OPTION = 'kermanentzat_editorial_migration_3';

function register_cli_commands(): void
{
    if (!class_exists('WP_CLI')) {
        return;
    }
    \WP_CLI::add_command('kermanentzat editorial migrate', __NAMESPACE__ . '\Editorial_Migrate_Command');
    \WP_CLI::add_command('kermanentzat editorial verify', __NAMESPACE__ . '\verify_editorial_runtime');
}

function verify_editorial_runtime(): void
{
    foreach ([UPDATE_POST_TYPE, TIMELINE_POST_TYPE, SOURCE_POST_TYPE] as $post_type) {
        if (!post_type_exists($post_type)) {
            \WP_CLI::error('No se registró ' . $post_type . '.');
        }
    }
    if (!taxonomy_exists(UPDATE_TAXONOMY)) {
        \WP_CLI::error('No se registró ' . UPDATE_TAXONOMY . '.');
    }
    $role = get_role('kermanentzat_editor');
    if (!$role || !$role->has_cap('publish_kerman_contents')) {
        \WP_CLI::error('El rol editorial no puede publicar contenido.');
    }
    $registered = get_registered_meta_keys('post', UPDATE_POST_TYPE);
    foreach (['_kerman_editorial_date', '_kerman_external_url', META_CAMPAIGN_STATE] as $key) {
        if (!isset($registered[$key])) {
            \WP_CLI::error('No se registró el metadato ' . $key . '.');
        }
    }
    if (!sensitive_review_is_complete(['rights', 'attribution', 'minimization'], 'ACTA-TEST')
        || sensitive_review_is_complete(['rights', 'attribution'], 'ACTA-TEST')) {
        \WP_CLI::error('La validación de contenido sensible no es determinista.');
    }
    if (!media_review_is_complete([
        'alt' => 'Descripción',
        'credit' => 'Egia Kermanentzat',
        'rights_status' => 'permission',
        'permission_ref' => 'PERM-TEST',
    ]) || media_review_is_complete([
        'alt' => '',
        'credit' => 'Egia Kermanentzat',
        'rights_status' => 'owned',
    ])) {
        \WP_CLI::error('La validación editorial de medios no es determinista.');
    }

    $legacy_updates = '<!-- wp:group --><div class="wp-block-group"><!-- wp:heading {"level":1} --><h1 class="wp-block-heading">Berriak</h1><!-- /wp:heading --><!-- wp:shortcode -->[kermanentzat_updates]<!-- /wp:shortcode --><!-- wp:paragraph --><p>Eduki pertsonalizatua.</p><!-- /wp:paragraph --></div><!-- /wp:group -->';
    $upgraded_updates = upgrade_updates_archive_content($legacy_updates, 'eu');
    if (substr_count($upgraded_updates, 'kermanentzat-updates-hero-v2') !== 1
        || substr_count($upgraded_updates, '<h1 class="wp-block-heading">Berriak</h1>') !== 1
        || !str_contains($upgraded_updates, '[kermanentzat_updates]')
        || !str_contains($upgraded_updates, 'Eduki pertsonalizatua.')
        || upgrade_updates_archive_content($upgraded_updates, 'eu') !== $upgraded_updates) {
        \WP_CLI::error('La actualización de la cabecera de actualidad no conserva el contenido o no es idempotente.');
    }

    $blocked_post = wp_insert_post([
        'post_type' => UPDATE_POST_TYPE,
        'post_status' => 'publish',
        'post_title' => 'Sensitive publication verification',
        'meta_input' => [
            '_kerman_sensitive' => true,
            '_kerman_sensitive_checks' => ['attribution', 'rights'],
            '_kerman_approval_ref' => '',
        ],
    ], true);
    if (is_wp_error($blocked_post)) {
        \WP_CLI::error('No se pudo probar el bloqueo sensible persistido.');
    }
    try {
        if (get_post_status($blocked_post) !== 'draft') {
            \WP_CLI::error('El guardado REST/programático puede saltarse el bloqueo sensible.');
        }
    } finally {
        wp_delete_post($blocked_post, true);
    }

    $first = wp_insert_post([
        'post_type' => UPDATE_POST_TYPE,
        'post_status' => 'draft',
        'post_title' => 'Editorial verification EU',
    ], true);
    $second = wp_insert_post([
        'post_type' => UPDATE_POST_TYPE,
        'post_status' => 'draft',
        'post_title' => 'Editorial verification ES',
    ], true);
    if (is_wp_error($first) || is_wp_error($second)) {
        \WP_CLI::error('No se pudieron crear objetos temporales.');
    }
    try {
        update_post_meta($first, '_kerman_language', 'eu');
        update_post_meta($second, '_kerman_language', 'es');
        if (!link_editorial_translations($first, $second)
            || linked_editorial_translation($first, 'es', ['draft']) !== $second
            || linked_editorial_translation($second, 'eu', ['draft']) !== $first) {
            \WP_CLI::error('El enlace editorial EU/ES no funciona sin dependencias externas.');
        }
        if (campaign_identity_post_id($second) !== min($first, $second)) {
            \WP_CLI::error('La identidad traducida no es estable.');
        }
        if (!maybe_queue_campaign($first) || maybe_queue_campaign($second)) {
            \WP_CLI::error('La cola no es idempotente.');
        }
        $html = campaign_html([get_post($first), get_post($second)]);
        if (substr_count($html, '{{unsubscribe_link}}') !== 1) {
            \WP_CLI::error('La plantilla no contiene una única baja de Sender.');
        }
    } finally {
        $identity = min($first, $second);
        wp_clear_scheduled_hook(CRON_HOOK, [$identity]);
        delete_option(campaign_lock_key($identity));
        wp_delete_post($first, true);
        wp_delete_post($second, true);
    }
    \WP_CLI::success('Modelo, permisos, revisiones, medios, traducciones y cola idempotente verificados.');
}

final class Editorial_Migrate_Command
{
    /**
     * Convierte las páginas del MVP y crea las entidades editoriales iniciales.
     *
     * ## OPTIONS
     *
     * [--dry-run]
     * : Muestra las operaciones sin escribir.
     *
     * [--force]
     * : Repite comprobaciones aunque la versión figure completada; nunca sobrescribe contenido ya migrado.
     *
     * [--strict]
     * : Aborta sin escribir si falta una página estructural o la cronología heredada no se puede interpretar.
     */
    public function __invoke(array $args, array $assoc_args): void
    {
        $dry_run = \WP_CLI\Utils\get_flag_value($assoc_args, 'dry-run', false);
        $force = \WP_CLI\Utils\get_flag_value($assoc_args, 'force', false);
        $strict = \WP_CLI\Utils\get_flag_value($assoc_args, 'strict', false);
        if (get_option(MIGRATION_OPTION) && !$force) {
            \WP_CLI::success('La migración editorial 3 ya está registrada. No se ha modificado nada.');
            return;
        }

        $operations = [];
        $precondition_errors = [];
        foreach (['kasuaren-laburpena', 'es/resumen-del-caso'] as $path) {
            $page = get_page_by_path($path);
            if (!$page instanceof \WP_Post) {
                \WP_CLI::warning('No existe la página ' . $path . '.');
                $precondition_errors[] = 'Falta la página estructural ' . $path . '.';
                continue;
            }
            $has_shortcode = str_contains($page->post_content, 'kermanentzat_timeline');
            $has_legacy_timeline = str_contains($page->post_content, 'case-timeline');
            if ($has_shortcode && !$has_legacy_timeline) {
                \WP_CLI::log('Ya migrada: ' . $path);
                continue;
            }
            $language = str_starts_with($path, 'es/') ? 'es' : 'eu';
            $timeline_entries = $has_legacy_timeline ? initial_timeline_entries($language, $page->post_content) : [];
            if ($has_legacy_timeline && count($timeline_entries) !== 10) {
                \WP_CLI::warning('No se pudo interpretar de forma segura la cronología de ' . $path . '; se conserva el bloque original.');
                $precondition_errors[] = 'La cronología heredada de ' . $path . ' no contiene diez entradas interpretables.';
                continue;
            }
            foreach ($timeline_entries as $entry) {
                if (!get_page_by_path($entry['slug'], OBJECT, TIMELINE_POST_TYPE) instanceof \WP_Post) {
                    $operations[] = ['kind' => 'timeline_entry', 'entry' => $entry];
                }
            }
            $content = $has_shortcode ? $page->post_content : convert_custom_html_to_editable_blocks($page->post_content);
            if ($has_legacy_timeline) {
                $content = remove_legacy_timeline_block($content);
            }
            if (!str_contains($content, 'kermanentzat_timeline')) {
                $content .= "\n<!-- wp:shortcode -->[kermanentzat_timeline featured=\"true\"]<!-- /wp:shortcode -->";
            }
            if ($content !== $page->post_content) {
                $operations[] = ['kind' => 'page', 'id' => $page->ID, 'path' => $path, 'content' => $content];
            }
        }

        foreach (initial_legal_page_updates() as $path => $content) {
            $page = get_page_by_path($path);
            if (!$page instanceof \WP_Post) {
                \WP_CLI::warning('No existe la página legal ' . $path . '.');
                $precondition_errors[] = 'Falta la página legal ' . $path . '.';
                continue;
            }
            if (trim($page->post_content) !== trim($content)) {
                $operations[] = ['kind' => 'page', 'id' => $page->ID, 'path' => $path, 'content' => $content];
            }
        }

        foreach (initial_archive_pages() as $path => $definition) {
            $page = get_page_by_path($path);
            if (!$page instanceof \WP_Post) {
                $operations[] = ['kind' => 'create_page', 'path' => $path, 'definition' => $definition];
            } elseif (!str_contains($page->post_content, $definition['marker']) || str_contains($page->post_content, '\\n\\n')) {
                $operations[] = [
                    'kind' => 'page',
                    'id' => $page->ID,
                    'path' => $path,
                    'content' => $definition['content'],
                    'post_status' => $definition['status'] ?? null,
                ];
            } elseif (!empty($definition['layout_marker']) && !str_contains($page->post_content, $definition['layout_marker'])) {
                $operations[] = [
                    'kind' => 'page',
                    'id' => $page->ID,
                    'path' => $path,
                    'content' => upgrade_updates_archive_content($page->post_content, (string) $definition['language']),
                    'post_status' => $definition['status'] ?? null,
                ];
            } elseif (!empty($definition['status']) && $page->post_status !== $definition['status']) {
                $operations[] = ['kind' => 'page_status', 'id' => $page->ID, 'path' => $path, 'post_status' => $definition['status']];
            }
        }

        foreach (['kontaktua', 'es/contacto'] as $path) {
            $page = get_page_by_path($path);
            if (!$page instanceof \WP_Post) {
                \WP_CLI::warning('No existe la página de contacto ' . $path . '.');
                $precondition_errors[] = 'Falta la página de contacto ' . $path . '.';
            } elseif (!str_contains($page->post_content, 'kermanentzat_subscription')) {
                $operations[] = [
                    'kind' => 'page',
                    'id' => $page->ID,
                    'path' => $path,
                    'content' => rtrim($page->post_content) . "\n\n<!-- wp:shortcode -->[kermanentzat_subscription]<!-- /wp:shortcode -->",
                ];
            }
        }

        foreach (initial_press_archive_entries() as $entry) {
            $existing = get_page_by_path($entry['slug'], OBJECT, UPDATE_POST_TYPE);
            if (!$existing instanceof \WP_Post) {
                $operations[] = ['kind' => 'entry', 'entry' => $entry];
            } elseif ((string) get_post_meta($existing->ID, '_kerman_external_url', true) !== $entry['url']) {
                $operations[] = ['kind' => 'entry_source', 'id' => $existing->ID, 'entry' => $entry];
            }
        }

        if ($strict && $precondition_errors !== []) {
            \WP_CLI::error("La migración estricta no puede continuar:\n- " . implode("\n- ", array_unique($precondition_errors)));
        }

        foreach ($operations as $operation) {
            \WP_CLI::log(($dry_run ? '[DRY-RUN] ' : '') . describe_migration_operation($operation));
            if (!$dry_run) {
                apply_migration_operation($operation);
            }
        }

        if (!$dry_run) {
            link_initial_translations();
            update_option(MIGRATION_OPTION, [
                'completed_at' => current_time('mysql', true),
                'operations' => count($operations),
            ], false);
            flush_rewrite_rules(false);
        }
        \WP_CLI::success(sprintf('%d operaciones %s.', count($operations), $dry_run ? 'planificadas' : 'aplicadas'));
    }
}

function convert_custom_html_to_editable_blocks(string $content): string
{
    $content = preg_replace('/^\s*<!-- wp:html -->\s*|\s*<!-- \/wp:html -->\s*$/', '', trim($content));
    if ($content === null || $content === '') {
        return $content ?: '';
    }
    $fragments = preg_split('/(?=<(?:header|section)\b)/i', $content, -1, PREG_SPLIT_NO_EMPTY);
    if (!$fragments) {
        $fragments = [$content];
    }
    return implode("\n\n", array_map(static fn(string $fragment): string => "<!-- wp:freeform -->\n" . trim($fragment) . "\n<!-- /wp:freeform -->", $fragments));
}

function remove_legacy_timeline_block(string $content): string
{
    $updated = preg_replace(
        '/<!-- wp:freeform -->(?:(?!<!-- \/wp:freeform -->).)*case-timeline(?:(?!<!-- \/wp:freeform -->).)*<!-- \/wp:freeform -->\s*/s',
        '',
        $content
    );
    return is_string($updated) ? trim($updated) : $content;
}

function initial_timeline_entries(string $language, string $content): array
{
    preg_match_all(
        '/<div class="evidence-row"><strong>(.*?)<\/strong><p>(.*?)<\/p><\/div>/s',
        $content,
        $matches,
        PREG_SET_ORDER
    );
    if (count($matches) !== 10) {
        return [];
    }
    $dates = [
        ['2024-05-01', '', 'month', '2024-05'],
        ['2025-02-23', '2025-02-25', 'range', '2025-02-23-25'],
        ['2025-09-30', '', 'day', '2025-09-30'],
        ['2025-11-26', '', 'day', '2025-11-26'],
        ['2026-01-01', '', 'month', '2026-01'],
        ['2026-02-18', '', 'day', '2026-02-18'],
        ['2026-04-30', '', 'day', '2026-04-30'],
        ['2026-05-25', '', 'day', '2026-05-25'],
        ['2026-06-01', '', 'month', '2026-06'],
        ['2026-07-08', '', 'day', '2026-07-08'],
    ];
    $titles = $language === 'es'
        ? [
            'Advertencias previas en Mitika',
            'Muerte de Kerman y prisión provisional',
            'Apertura del procedimiento ante jurado',
            'Cambio de procedimiento y libertad bajo fianza',
            'Recurso ante el Tribunal Supremo',
            'Comparecencia en el Parlamento Vasco',
            'Inadmisión del recurso',
            'Recomendaciones del Ararteko',
            'Presentación de Egia Kermanentzat Elkartea',
            'Comparecencia en la Comisión de Seguridad',
        ]
        : [
            'Mitikako aurretiazko ohartarazpenak',
            'Kermanen heriotza eta behin-behineko espetxealdia',
            'Herri Epaimahaiaren aurreko prozedura irekitzea',
            'Prozedura aldatzea eta fidantzapeko askatasuna',
            'Auzitegi Gorenaren aurreko errekurtsoa',
            'Eusko Legebiltzarreko agerraldia',
            'Errekurtsoa ez onartzea',
            'Arartekoaren gomendioak',
            'Egia Kermanentzat Elkartearen aurkezpena',
            'Segurtasun Batzordeko agerraldia',
        ];
    $entries = [];
    foreach ($matches as $index => $match) {
        $summary = trim(wp_strip_all_tags(html_entity_decode($match[2], ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        [$start, $end, $precision, $group] = $dates[$index];
        $entries[] = [
            'language' => $language,
            'slug' => 'timeline-' . $group . '-' . $language,
            'group' => 'timeline-' . $group,
            'title' => $titles[$index],
            'summary' => $summary,
            'start' => $start,
            'end' => $end,
            'precision' => $precision,
        ];
    }
    return $entries;
}

function initial_archive_pages(): array
{
    $eu_updates = updates_archive_page_blocks('eu', "[kermanentzat_updates]\n\n[kermanentzat_subscription]");
    $es_updates = updates_archive_page_blocks('es', "[kermanentzat_updates]\n\n[kermanentzat_subscription]");
    return [
        'berriak' => ['title' => 'Berriak', 'slug' => 'berriak', 'parent' => 0, 'marker' => 'kermanentzat_updates', 'layout_marker' => 'kermanentzat-updates-hero-v2', 'language' => 'eu', 'content' => $eu_updates],
        'es/actualidad' => ['title' => 'Actualidad', 'slug' => 'actualidad', 'parent_path' => 'es', 'marker' => 'kermanentzat_updates', 'layout_marker' => 'kermanentzat-updates-hero-v2', 'language' => 'es', 'content' => $es_updates],
        'kronologia' => ['title' => 'Kronologia', 'slug' => 'kronologia', 'parent' => 0, 'marker' => 'kermanentzat_timeline', 'content' => archive_page_blocks('Kronologia', '[kermanentzat_timeline]')],
        'es/cronologia' => ['title' => 'Cronología', 'slug' => 'cronologia', 'parent_path' => 'es', 'marker' => 'kermanentzat_timeline', 'content' => archive_page_blocks('Cronología', '[kermanentzat_timeline]')],
        'hemeroteka' => ['title' => 'Hemeroteka', 'slug' => 'hemeroteka', 'parent' => 0, 'marker' => 'press-archive', 'content' => archive_page_blocks('Hemeroteka', '[kermanentzat_updates type="press-archive" filters="false"]')],
        'es/hemeroteca' => ['title' => 'Hemeroteca', 'slug' => 'hemeroteca', 'parent_path' => 'es', 'marker' => 'press-archive', 'content' => archive_page_blocks('Hemeroteca', '[kermanentzat_updates type="press-archive" filters="false"]')],
        'harpidetza' => ['title' => 'Harpidetza', 'slug' => 'harpidetza', 'parent' => 0, 'status' => 'publish', 'marker' => 'kermanentzat-subscription-hero-v1', 'content' => subscription_page_blocks('eu')],
        'es/suscripcion' => ['title' => 'Suscripción', 'slug' => 'suscripcion', 'parent_path' => 'es', 'status' => 'publish', 'marker' => 'kermanentzat-subscription-hero-v1', 'content' => subscription_page_blocks('es')],
    ];
}

function initial_legal_page_updates(): array
{
    if (!function_exists('kermanentzat_legal_pages')) {
        $source = get_theme_file_path('inc/legal-content.php');
        if (is_file($source)) {
            require_once $source;
        }
    }
    if (!function_exists('kermanentzat_legal_pages')) {
        return [];
    }
    $pages = kermanentzat_legal_pages();
    return [
        'es/politica-de-privacidad' => (string) ($pages['es']['privacy'] ?? ''),
        'es/politica-de-cookies' => (string) ($pages['es']['cookies'] ?? ''),
        'pribatutasun-politika' => (string) ($pages['eu']['privacy'] ?? ''),
        'cookie-politika' => (string) ($pages['eu']['cookies'] ?? ''),
    ];
}

function updates_archive_hero_blocks(string $language): string
{
    $is_eu = $language === 'eu';
    $wordmark = $is_eu ? 'BERRIAK' : 'ACTUALIDAD';
    $title = $is_eu ? 'Berriak' : 'Actualidad';
    $description = $is_eu
        ? 'Kermani eta Egia Kermanentzat Elkartearen lanari buruzko albisteak, komunikatuak eta kazetaritza-estaldura.'
        : 'Noticias, comunicados y cobertura periodística relacionada con Kerman y con el trabajo de Egia Kermanentzat Elkartea.';
    $archive_id = $is_eu ? 'argitalpenak' : 'publicaciones';
    $archive_label = $is_eu ? 'Ikusi argitalpenak' : 'Ver publicaciones';
    $instagram_label = $is_eu ? 'Jarraitu Instagramen' : 'Seguir en Instagram';
    $instagram = 'https://www.instagram.com/justizia.kermanentzat/';

    return '<!-- kermanentzat-updates-hero-v2 -->'
        . '<!-- wp:group {"tagName":"header","className":"page-hero page-hero--updates content-band--dark","templateLock":"contentOnly"} --><header class="wp-block-group page-hero page-hero--updates content-band--dark">'
        . '<!-- wp:html --><div class="updates-wordmark" aria-hidden="true">' . esc_html($wordmark) . '</div><!-- /wp:html -->'
        . '<!-- wp:group {"className":"content-wrap"} --><div class="wp-block-group content-wrap">'
        . '<!-- wp:heading {"level":1} --><h1 class="wp-block-heading">' . esc_html($title) . '</h1><!-- /wp:heading -->'
        . '<!-- wp:paragraph --><p>' . esc_html($description) . '</p><!-- /wp:paragraph -->'
        . '<!-- wp:html --><div class="hero-actions"><a class="button button--primary" href="#' . esc_attr($archive_id) . '">' . esc_html($archive_label) . '</a><a class="button button--inverse" href="' . esc_url($instagram) . '" target="_blank" rel="noopener noreferrer">' . esc_html($instagram_label) . '</a></div><!-- /wp:html -->'
        . '</div><!-- /wp:group --></header><!-- /wp:group -->';
}

function upgrade_updates_archive_content(string $content, string $language): string
{
    if (str_contains($content, 'kermanentzat-updates-hero-v2')) {
        return $content;
    }

    $title = $language === 'eu' ? 'Berriak' : 'Actualidad';
    $quoted_title = preg_quote($title, '~');
    $heading_pattern = '~<!-- wp:heading[^>]*-->\\s*<h1[^>]*>\\s*' . $quoted_title . '\\s*</h1>\\s*<!-- /wp:heading -->~u';
    $without_legacy_heading = preg_replace($heading_pattern, '', $content, 1);
    if (!is_string($without_legacy_heading)) {
        $without_legacy_heading = $content;
    }

    return updates_archive_hero_blocks($language) . "\n" . ltrim($without_legacy_heading);
}

function updates_archive_page_blocks(string $language, string $shortcodes): string
{
    return updates_archive_hero_blocks($language)
        . archive_page_blocks($language === 'eu' ? 'Berriak' : 'Actualidad', $shortcodes, false);
}

function subscription_page_blocks(string $language): string
{
    $is_eu = $language === 'eu';
    $wordmark = $is_eu ? 'HARPIDETZA' : 'SUSCRIPCIÓN';
    $title = $is_eu ? 'Harpidetza' : 'Suscripción';
    $description = $is_eu
        ? 'Jaso posta elektronikoz Egia Kermanentzatek hautatutako argitalpen eta berritasunak.'
        : 'Recibe por email las publicaciones y novedades seleccionadas por Egia Kermanentzat.';

    $hero = '<!-- kermanentzat-subscription-hero-v1 -->'
        . '<!-- wp:group {"tagName":"header","className":"page-hero page-hero--subscription content-band--dark","templateLock":"contentOnly"} --><header class="wp-block-group page-hero page-hero--subscription content-band--dark">'
        . '<!-- wp:html --><div class="subscription-wordmark" aria-hidden="true">' . esc_html($wordmark) . '</div><!-- /wp:html -->'
        . '<!-- wp:group {"className":"content-wrap"} --><div class="wp-block-group content-wrap">'
        . '<!-- wp:heading {"level":1} --><h1 class="wp-block-heading">' . esc_html($title) . '</h1><!-- /wp:heading -->'
        . '<!-- wp:paragraph --><p>' . esc_html($description) . '</p><!-- /wp:paragraph -->'
        . '</div><!-- /wp:group --></header><!-- /wp:group -->';

    return $hero . archive_page_blocks($title, '[kermanentzat_subscription]', false);
}

function archive_page_blocks(string $title, string $shortcodes, bool $include_title = true): string
{
    $blocks = '<!-- wp:group {"className":"content-band content-band--light","templateLock":"contentOnly"} --><div class="wp-block-group content-band content-band--light"><!-- wp:group {"className":"content-wrap"} --><div class="wp-block-group content-wrap">';
    if ($include_title) {
        $blocks .= '<!-- wp:heading {"level":1} --><h1 class="wp-block-heading">' . esc_html($title) . '</h1><!-- /wp:heading -->';
    }
    foreach (preg_split('/\n\s*\n/', $shortcodes) ?: [] as $shortcode) {
        $blocks .= '<!-- wp:shortcode -->' . trim($shortcode) . '<!-- /wp:shortcode -->';
    }
    return $blocks . '</div><!-- /wp:group --></div><!-- /wp:group -->';
}

function initial_press_archive_entries(): array
{
    return [
        [
            'language' => 'eu',
            'slug' => 'orain-mitika-testigantzak-2026-08-02-eu',
            'title' => 'Testigantza berriek agerian utzi dituzte Mitikako zaindariek Kerman Villate hil aurretik behin eta berriz egindako erasoak',
            'excerpt' => 'ORAINek Kerman hil aurreko hilabeteetan Mitikako atezainek egindako ustezko erasoei buruzko lau testigantza jaso ditu, eta kasuak Ertzaintzaren aurrean salatu zituztela adierazi du.',
            'url' => 'https://orain.eus/eu/aktualitatea/gizartea/2026/08/02/testigantza-berriek-agerian-utzi-dituzte-mitikako-atezainek-kerman-villate-hil-aurretik-behin-eta-berriz-egindako-erasoak/',
            'date' => '2026-08-02',
            'outlet' => 'ORAIN · Radio Euskadi',
            'group' => 'orain-2026-08-02',
        ],
        [
            'language' => 'es',
            'slug' => 'orain-mitika-testimonios-2026-08-02-es',
            'title' => 'Nuevos testimonios apuntan a agresiones reiteradas de porteros de Mítika antes de la muerte de Kerman Villate',
            'excerpt' => 'ORAIN recoge cuatro testimonios sobre presuntas agresiones ocurridas en los meses anteriores a la muerte de Kerman e informa de que los casos fueron denunciados ante la Ertzaintza.',
            'url' => 'https://orain.eus/es/actualidad/sociedad/2026/08/02/nuevos-testimonios-apuntan-agresiones-reiteradas-porteros-mitika-antes-la-muerte-kerman-villate/',
            'date' => '2026-08-02',
            'outlet' => 'ORAIN · Radio Euskadi',
            'group' => 'orain-2026-08-02',
        ],
        [
            'language' => 'eu',
            'slug' => 'gasteizberri-alkatea-mitika-erasoak-2026-08-07-eu',
            'title' => 'La alcaldesa, sobre las agresiones de los porteros de Mítika: «No me consta»',
            'excerpt' => 'GasteizBerrik Maider Etxebarria Gasteizko alkatearen erantzuna jaso du: Mítikako atezainei egotzitako aurretiazko erasoen berririk ez zuela adierazi zuen.',
            'url' => 'https://gasteizberri.com/2026/08/alcaldesa-agresiones-mitika-no-me-consta/',
            'date' => '2026-08-07',
            'outlet' => 'GasteizBerri',
            'group' => 'gasteizberri-2026-08-07',
        ],
        [
            'language' => 'es',
            'slug' => 'gasteizberri-alcaldesa-mitika-agresiones-2026-08-07-es',
            'title' => 'La alcaldesa, sobre las agresiones de los porteros de Mítika: «No me consta»',
            'excerpt' => 'GasteizBerri recoge la respuesta de la alcaldesa de Vitoria-Gasteiz, Maider Etxebarria, quien afirmó que no le constaban las agresiones previas atribuidas a los porteros de Mítika.',
            'url' => 'https://gasteizberri.com/2026/08/alcaldesa-agresiones-mitika-no-me-consta/',
            'date' => '2026-08-07',
            'outlet' => 'GasteizBerri',
            'group' => 'gasteizberri-2026-08-07',
        ],
    ];
}

function initial_entry_exists(string $slug): bool
{
    return get_page_by_path($slug, OBJECT, UPDATE_POST_TYPE) instanceof \WP_Post;
}

function describe_migration_operation(array $operation): string
{
    return match ($operation['kind']) {
        'page' => 'Actualizar página ' . $operation['path'] . ' (#' . $operation['id'] . ')',
        'page_status' => 'Cambiar estado a ' . $operation['post_status'] . ' ' . $operation['path'] . ' (#' . $operation['id'] . ')',
        'create_page' => 'Crear página ' . $operation['path'],
        'entry' => 'Crear hemeroteca ' . $operation['entry']['slug'],
        'entry_source' => 'Corregir fuente de hemeroteca ' . $operation['entry']['slug'],
        'timeline_entry' => 'Crear hito ' . $operation['entry']['slug'],
        default => 'Operación desconocida',
    };
}

function apply_migration_operation(array $operation): void
{
    if ($operation['kind'] === 'page') {
        $update = ['ID' => $operation['id'], 'post_content' => $operation['content']];
        if (!empty($operation['post_status'])) {
            $update['post_status'] = $operation['post_status'];
        }
        $result = wp_update_post(wp_slash($update), true);
        if (is_wp_error($result)) {
            throw new \RuntimeException($result->get_error_message());
        }
        return;
    }
    if ($operation['kind'] === 'page_status') {
        $result = wp_update_post(['ID' => $operation['id'], 'post_status' => $operation['post_status']], true);
        if (is_wp_error($result)) {
            throw new \RuntimeException($result->get_error_message());
        }
        return;
    }
    if ($operation['kind'] === 'create_page') {
        $definition = $operation['definition'];
        $parent = absint($definition['parent'] ?? 0);
        if (!$parent && !empty($definition['parent_path'])) {
            $parent_page = get_page_by_path($definition['parent_path']);
            $parent = $parent_page instanceof \WP_Post ? $parent_page->ID : 0;
        }
        $result = wp_insert_post(wp_slash([
            'post_type' => 'page',
            'post_status' => $definition['status'] ?? 'publish',
            'post_title' => $definition['title'],
            'post_name' => $definition['slug'],
            'post_parent' => $parent,
            'post_content' => $definition['content'],
            'comment_status' => 'closed',
        ]), true);
        if (is_wp_error($result)) {
            throw new \RuntimeException($result->get_error_message());
        }
        return;
    }
    if ($operation['kind'] === 'entry') {
        $entry = $operation['entry'];
        $post_id = wp_insert_post(wp_slash([
            'post_type' => UPDATE_POST_TYPE,
            'post_status' => 'publish',
            'post_title' => $entry['title'],
            'post_name' => $entry['slug'],
            'post_excerpt' => $entry['excerpt'],
            'post_content' => '<!-- wp:paragraph --><p>' . esc_html($entry['excerpt']) . '</p><!-- /wp:paragraph -->',
        ]), true);
        if (is_wp_error($post_id)) {
            throw new \RuntimeException($post_id->get_error_message());
        }
        update_post_meta($post_id, '_kerman_language', $entry['language']);
        update_post_meta($post_id, '_kerman_editorial_date', $entry['date']);
        update_post_meta($post_id, '_kerman_external_date', $entry['date']);
        update_post_meta($post_id, '_kerman_external_outlet', $entry['outlet']);
        update_post_meta($post_id, '_kerman_external_url', $entry['url']);
        update_post_meta($post_id, '_kerman_translation_group', $entry['group']);
        wp_set_object_terms($post_id, ['press-archive'], UPDATE_TAXONOMY, false);
        if (function_exists('pll_set_post_language')) {
            pll_set_post_language($post_id, $entry['language']);
        }
        return;
    }
    if ($operation['kind'] === 'entry_source') {
        update_post_meta((int) $operation['id'], '_kerman_external_url', esc_url_raw($operation['entry']['url']));
        return;
    }
    if ($operation['kind'] === 'timeline_entry') {
        $entry = $operation['entry'];
        $post_id = wp_insert_post(wp_slash([
            'post_type' => TIMELINE_POST_TYPE,
            'post_status' => 'publish',
            'post_title' => $entry['title'],
            'post_name' => $entry['slug'],
            'post_excerpt' => $entry['summary'],
            'post_content' => '<!-- wp:paragraph --><p>' . esc_html($entry['summary']) . '</p><!-- /wp:paragraph -->',
            'meta_input' => [
                '_kerman_language' => $entry['language'],
                '_kerman_translation_group' => $entry['group'],
                '_kerman_timeline_start' => $entry['start'],
                '_kerman_timeline_end' => $entry['end'],
                '_kerman_timeline_precision' => $entry['precision'],
                '_kerman_featured' => true,
                '_kerman_sensitive' => true,
                '_kerman_sensitive_checks' => ['attribution', 'minimization', 'rights'],
                '_kerman_approval_ref' => 'MIGRATION-EXISTING-PUBLISHED',
            ],
        ]), true);
        if (is_wp_error($post_id)) {
            throw new \RuntimeException($post_id->get_error_message());
        }
        if (function_exists('pll_set_post_language')) {
            pll_set_post_language($post_id, $entry['language']);
        }
    }
}

function link_initial_translations(): void
{
    $entries = get_posts([
        'post_type' => [UPDATE_POST_TYPE, TIMELINE_POST_TYPE],
        'post_status' => 'any',
        'numberposts' => -1,
        'meta_key' => '_kerman_translation_group',
    ]);
    $groups = [];
    foreach ($entries as $entry) {
        $language = editorial_language_for_post($entry->ID);
        $group = (string) get_post_meta($entry->ID, '_kerman_translation_group', true);
        if ($group === '') {
            continue;
        }
        $groups[$group][$language] = $entry->ID;
        if (function_exists('pll_set_post_language')) {
            pll_set_post_language($entry->ID, $language);
        }
    }
    if (function_exists('pll_save_post_translations')) {
        foreach ($groups as $translations) {
            if (count($translations) > 1) {
                pll_save_post_translations($translations);
            }
        }
    }
}
