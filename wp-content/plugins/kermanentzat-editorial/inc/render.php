<?php

namespace Kermanentzat\Editorial;

defined('ABSPATH') || exit;

function register_render_hooks(): void
{
    add_action('init', __NAMESPACE__ . '\register_editorial_patterns', 60);
    add_shortcode('kermanentzat_updates', __NAMESPACE__ . '\render_updates_shortcode');
    add_shortcode('kermanentzat_timeline', __NAMESPACE__ . '\render_timeline_shortcode');
    add_shortcode('kermanentzat_recent_updates', __NAMESPACE__ . '\render_recent_updates_shortcode');
    add_action('wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_editorial_assets');
    add_filter('template_include', __NAMESPACE__ . '\editorial_template_include', 20);
    add_filter('kermanentzat_sitemap_entries', __NAMESPACE__ . '\append_editorial_sitemap_entries', 10, 2);
    add_action('wp_head', __NAMESPACE__ . '\render_editorial_structured_data', 30);
}

function enqueue_editorial_assets(): void
{
    wp_enqueue_style('kermanentzat-editorial', PLUGIN_URL . 'assets/editorial.css', [], VERSION);
    wp_enqueue_script('kermanentzat-editorial', PLUGIN_URL . 'assets/editorial.js', [], VERSION, true);
    wp_script_add_data('kermanentzat-editorial', 'defer', true);
}

function current_editorial_language(): string
{
    if (function_exists('pll_current_language')) {
        $language = pll_current_language('slug');
        if (in_array($language, ['eu', 'es'], true)) {
            return $language;
        }
    }
    if (function_exists('kermanentzat_language')) {
        return kermanentzat_language();
    }
    $path = trim((string) wp_parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
    return $path === 'es' || str_starts_with($path, 'es/') ? 'es' : 'eu';
}

function update_type_for_post(int $post_id): string
{
    $terms = wp_get_object_terms($post_id, UPDATE_TAXONOMY, ['fields' => 'slugs']);
    if (is_wp_error($terms) || $terms === []) {
        return 'news';
    }
    return (string) $terms[0];
}

function update_type_label(string $type, ?string $language = null): string
{
    $language = $language ?: current_editorial_language();
    $definitions = update_type_definitions();
    return $definitions[$type][$language] ?? $definitions['news'][$language];
}

function language_query_args(): array
{
    if (function_exists('pll_current_language')) {
        return ['lang' => current_editorial_language()];
    }
    return ['meta_query' => [[
        'key' => '_kerman_language',
        'value' => current_editorial_language(),
    ]]];
}

function render_updates_shortcode($attributes): string
{
    $attributes = shortcode_atts([
        'type' => '',
        'limit' => 10,
        'featured' => '',
        'filters' => 'true',
    ], $attributes, 'kermanentzat_updates');

    $requested_type = sanitize_key((string) ($_GET['mota'] ?? $attributes['type']));
    if (!array_key_exists($requested_type, update_type_definitions())) {
        $requested_type = '';
    }
    $paged = max(1, absint(get_query_var('paged') ?: ($_GET['orria'] ?? 1)));
    $args = array_merge([
        'post_type' => UPDATE_POST_TYPE,
        'post_status' => 'publish',
        'posts_per_page' => max(1, min(50, absint($attributes['limit']))),
        'paged' => $paged,
        'meta_key' => '_kerman_editorial_date',
        'orderby' => ['meta_value' => 'DESC', 'date' => 'DESC'],
    ], language_query_args());

    if ($requested_type !== '') {
        $args['tax_query'] = [[
            'taxonomy' => UPDATE_TAXONOMY,
            'field' => 'slug',
            'terms' => [$requested_type],
        ]];
    }
    if ($attributes['featured'] === 'true') {
        $args['meta_query'][] = [
            'key' => '_kerman_featured',
            'value' => '1',
        ];
    }

    $query = new \WP_Query($args);
    ob_start();
    $archive_id = current_editorial_language() === 'eu' ? 'argitalpenak' : 'publicaciones';
    echo '<div class="kerman-updates"' . ($attributes['filters'] === 'true' ? ' id="' . esc_attr($archive_id) . '"' : '') . '>';
    if ($attributes['filters'] === 'true') {
        render_update_filters($requested_type);
    }
    if (!$query->have_posts()) {
        echo '<p class="kerman-empty">' . esc_html(current_editorial_language() === 'eu' ? 'Oraindik ez dago argitalpenik.' : 'Todavía no hay publicaciones.') . '</p>';
    } else {
        echo '<div class="kerman-card-grid">';
        while ($query->have_posts()) {
            $query->the_post();
            render_update_card(get_post());
        }
        echo '</div>';
        render_query_pagination($query, $paged);
    }
    echo '</div>';
    wp_reset_postdata();
    return (string) ob_get_clean();
}

function render_update_filters(string $selected): void
{
    $language = current_editorial_language();
    $all = $language === 'eu' ? 'Guztiak' : 'Todo';
    echo '<nav class="kerman-filters" aria-label="' . esc_attr($language === 'eu' ? 'Argitalpen motak' : 'Tipos de publicación') . '">';
    $base = remove_query_arg(['mota', 'orria', 'paged']);
    printf('<a href="%s" %s>%s</a>', esc_url($base), $selected === '' ? 'aria-current="page"' : '', esc_html($all));
    foreach (update_type_definitions() as $slug => $labels) {
        printf(
            '<a href="%s" %s>%s</a>',
            esc_url(add_query_arg('mota', $slug, $base)),
            $selected === $slug ? 'aria-current="page"' : '',
            esc_html($labels[$language])
        );
    }
    echo '</nav>';
}

function render_update_card(\WP_Post $post): void
{
    $language = editorial_language_for_post($post->ID);
    $type = update_type_for_post($post->ID);
    $date = (string) meta_value($post->ID, $type === 'press-archive' ? '_kerman_external_date' : '_kerman_editorial_date');
    $date = $date ?: get_the_date('Y-m-d', $post);
    $excerpt = has_excerpt($post) ? get_the_excerpt($post) : wp_trim_words(wp_strip_all_tags($post->post_content), 28);
    $external = (string) meta_value($post->ID, '_kerman_external_url');
    $outlet = (string) meta_value($post->ID, '_kerman_external_outlet');
    $permalink = $type === 'press-archive' && $external !== '' ? $external : get_permalink($post);
    $target = $type === 'press-archive' ? ' target="_blank" rel="noopener noreferrer"' : '';
    $share_id = wp_unique_id('kerman-share-menu-');
    $feedback_id = wp_unique_id('kerman-share-feedback-');
    $share_title = get_the_title($post);
    $share_text = trim($share_title . ' ' . $permalink);
    $labels = $language === 'eu'
        ? [
            'share' => 'Partekatu',
            'menu' => 'Partekatzeko aukerak',
            'email' => 'Posta elektronikoz',
            'copy' => 'Esteka kopiatu',
            'copied' => 'Esteka kopiatuta.',
            'read_external' => 'Irakurri albistea',
            'read_internal' => 'Irakurri gehiago',
            'new_window' => ' (beste fitxa batean irekitzen da)',
        ]
        : [
            'share' => 'Compartir',
            'menu' => 'Opciones para compartir',
            'email' => 'Por correo electrónico',
            'copy' => 'Copiar enlace',
            'copied' => 'Enlace copiado.',
            'read_external' => 'Leer la noticia',
            'read_internal' => 'Leer más',
            'new_window' => ' (se abre en una pestaña nueva)',
        ];
    $whatsapp_url = 'https://wa.me/?text=' . rawurlencode($share_text);
    $facebook_url = 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($permalink);
    $email_url = 'mailto:?subject=' . rawurlencode($share_title) . '&body=' . rawurlencode($permalink);
    ?>
    <article class="kerman-card kerman-card--<?php echo esc_attr($type); ?>">
        <?php if (has_post_thumbnail($post)) : ?>
            <a class="kerman-card__image" href="<?php echo esc_url($permalink); ?>"<?php echo $target; ?>><?php echo get_the_post_thumbnail($post, 'medium_large', ['loading' => 'lazy']); ?></a>
        <?php endif; ?>
        <div class="kerman-card__body">
            <div class="kerman-card__meta">
                <span><?php echo esc_html(update_type_label($type, $language)); ?></span>
                <?php if ($outlet !== '') : ?><span><?php echo esc_html($outlet); ?></span><?php endif; ?>
                <time datetime="<?php echo esc_attr($date); ?>"><?php echo esc_html(format_editorial_date($date, $language)); ?></time>
            </div>
            <h2 class="kerman-card__title"><?php echo esc_html(get_the_title($post)); ?></h2>
            <?php if ($excerpt !== '') : ?><p><?php echo esc_html($excerpt); ?></p><?php endif; ?>
            <?php if ($type === 'activity') : render_activity_meta($post); endif; ?>
            <div class="kerman-card__actions">
                <a class="kerman-card__read-link" href="<?php echo esc_url($permalink); ?>"<?php echo $target; ?>>
                    <?php echo esc_html($type === 'press-archive' ? $labels['read_external'] : $labels['read_internal']); ?>
                    <span aria-hidden="true"><?php echo $type === 'press-archive' ? '↗' : '→'; ?></span>
                    <?php if ($type === 'press-archive') : ?><span class="screen-reader-text"><?php echo esc_html($labels['new_window']); ?></span><?php endif; ?>
                </a>
                <div
                    class="kerman-share"
                    data-share-root
                    data-share-title="<?php echo esc_attr($share_title); ?>"
                    data-share-url="<?php echo esc_url($permalink); ?>"
                >
                    <button
                        class="kerman-share__trigger"
                        type="button"
                        aria-expanded="false"
                        aria-controls="<?php echo esc_attr($share_id); ?>"
                        data-share-trigger
                    >
                        <svg aria-hidden="true" viewBox="0 0 24 24" width="20" height="20" focusable="false">
                            <path d="M18 16a3 3 0 0 0-2.4 1.2L8.9 13.8a3.2 3.2 0 0 0 0-3.6l6.7-3.4A3 3 0 1 0 15 5c0 .2 0 .4.1.6L8.4 9A3 3 0 1 0 6 15c.9 0 1.8-.4 2.4-1l6.7 3.4A3 3 0 1 0 18 16Z" fill="currentColor"/>
                        </svg>
                        <span><?php echo esc_html($labels['share']); ?></span>
                    </button>
                    <div
                        class="kerman-share__menu"
                        id="<?php echo esc_attr($share_id); ?>"
                        role="group"
                        aria-label="<?php echo esc_attr($labels['menu']); ?>"
                        data-share-menu
                        hidden
                    >
                        <a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener noreferrer">WhatsApp</a>
                        <a href="<?php echo esc_url($facebook_url); ?>" target="_blank" rel="noopener noreferrer">Facebook</a>
                        <a href="<?php echo esc_url($email_url); ?>"><?php echo esc_html($labels['email']); ?></a>
                        <button type="button" data-share-copy><?php echo esc_html($labels['copy']); ?></button>
                    </div>
                </div>
                <p
                    class="kerman-share__feedback"
                    id="<?php echo esc_attr($feedback_id); ?>"
                    role="status"
                    aria-live="polite"
                    data-share-feedback
                    data-success-message="<?php echo esc_attr($labels['copied']); ?>"
                ></p>
            </div>
        </div>
    </article>
    <?php
}

function render_activity_meta(\WP_Post $post): void
{
    $start = (string) meta_value($post->ID, '_kerman_event_start');
    $end = (string) meta_value($post->ID, '_kerman_event_end');
    $location = (string) meta_value($post->ID, '_kerman_event_location');
    if ($start === '' && $location === '') {
        return;
    }
    echo '<dl class="kerman-event-meta">';
    if ($start !== '') {
        echo '<div><dt>' . esc_html(current_editorial_language() === 'eu' ? 'Noiz' : 'Cuándo') . '</dt><dd>' . esc_html(format_editorial_datetime($start, current_editorial_language())) . '</dd></div>';
    }
    if ($location !== '') {
        echo '<div><dt>' . esc_html(current_editorial_language() === 'eu' ? 'Non' : 'Dónde') . '</dt><dd>' . esc_html($location) . '</dd></div>';
    }
    if ($start !== '') {
        $now = current_datetime()->getTimestamp();
        $start_timestamp = strtotime($start) ?: $now;
        $end_timestamp = $end !== '' ? (strtotime($end) ?: $start_timestamp) : $start_timestamp;
        $state = $now < $start_timestamp ? 'upcoming' : ($now <= $end_timestamp && $end !== '' ? 'ongoing' : 'finished');
        $labels = current_editorial_language() === 'eu'
            ? ['upcoming' => 'Hurrengoa', 'ongoing' => 'Abian', 'finished' => 'Amaituta']
            : ['upcoming' => 'Próxima', 'ongoing' => 'En curso', 'finished' => 'Finalizada'];
        echo '<div><dt>' . esc_html(current_editorial_language() === 'eu' ? 'Egoera' : 'Estado') . '</dt><dd class="kerman-event-state kerman-event-state--' . esc_attr($state) . '">' . esc_html($labels[$state]) . '</dd></div>';
    }
    echo '</dl>';
}

function render_query_pagination(\WP_Query $query, int $current): void
{
    if ($query->max_num_pages <= 1) {
        return;
    }
    $links = paginate_links([
        'current' => $current,
        'total' => (int) $query->max_num_pages,
        'type' => 'list',
        'format' => '?orria=%#%',
        'add_args' => array_filter(['mota' => sanitize_key((string) ($_GET['mota'] ?? ''))]),
    ]);
    if ($links) {
        echo '<nav class="kerman-pagination" aria-label="' . esc_attr(current_editorial_language() === 'eu' ? 'Orrialdeak' : 'Páginas') . '">' . wp_kses_post($links) . '</nav>';
    }
}

function render_timeline_shortcode($attributes): string
{
    $attributes = shortcode_atts(['limit' => -1, 'featured' => 'false'], $attributes, 'kermanentzat_timeline');
    $is_case_summary = $attributes['featured'] === 'true';
    $args = array_merge([
        'post_type' => TIMELINE_POST_TYPE,
        'post_status' => 'publish',
        'posts_per_page' => (int) $attributes['limit'],
        'meta_key' => '_kerman_timeline_start',
        'orderby' => ['meta_value' => 'ASC', 'date' => 'ASC'],
        'order' => 'ASC',
    ], language_query_args());
    if ($attributes['featured'] === 'true') {
        $args['meta_query'][] = ['key' => '_kerman_featured', 'value' => '1'];
    }
    $items = get_posts($args);
    $language = current_editorial_language();
    $heading_id = wp_unique_id('kerman-timeline-heading-');
    $section_classes = 'kerman-timeline-section';
    $layout_classes = 'split-grid';
    if ($is_case_summary) {
        $section_classes .= ' content-band content-band--light';
        $layout_classes = 'content-wrap split-grid';
    }
    ob_start();
    echo '<section class="' . esc_attr($section_classes) . '" aria-labelledby="' . esc_attr($heading_id) . '">';
    echo '<div class="' . esc_attr($layout_classes) . '">';
    echo '<div><h2 class="section-heading" id="' . esc_attr($heading_id) . '">' . esc_html($language === 'eu' ? 'Funtsezko kronologia' : 'Cronología esencial') . '</h2></div>';
    echo '<div class="reading-copy">';
    if ($items === []) {
        echo '<p class="kerman-empty">' . esc_html($language === 'eu' ? 'Oraindik ez dago kronologiako hitorik.' : 'Todavía no hay hitos en la cronología.') . '</p></div></div></section>';
        return (string) ob_get_clean();
    }
    echo '<ol class="kerman-timeline evidence-list">';
    foreach ($items as $item) {
        $language = editorial_language_for_post($item->ID);
        $start = (string) meta_value($item->ID, '_kerman_timeline_start');
        $end = (string) meta_value($item->ID, '_kerman_timeline_end');
        $precision = (string) meta_value($item->ID, '_kerman_timeline_precision', 'day');
        $label = format_timeline_date($start, $language, $precision);
        if ($end !== '') {
            $label .= ' — ' . format_editorial_date($end, $language);
        }
        echo '<li class="kerman-timeline__item evidence-row">';
        echo '<time datetime="' . esc_attr($start) . '">' . esc_html($label) . '</time>';
        echo '<div class="kerman-timeline__body">';
        echo '<h3>' . esc_html(get_the_title($item)) . '</h3>';
        $summary = has_excerpt($item) ? get_the_excerpt($item) : wp_trim_words(wp_strip_all_tags($item->post_content), 36);
        if ($summary !== '') {
            echo '<p>' . esc_html($summary) . '</p>';
        }
        render_public_sources($item->ID);
        echo '</div></li>';
    }
    echo '</ol></div></div></section>';
    return (string) ob_get_clean();
}

function render_public_sources(int $post_id): void
{
    $source_ids = sanitize_id_list(meta_value($post_id, '_kerman_source_ids', []));
    if ($source_ids === []) {
        return;
    }
    echo '<ul class="kerman-sources">';
    foreach ($source_ids as $source_id) {
        $source = get_post($source_id);
        if (!$source instanceof \WP_Post || $source->post_type !== SOURCE_POST_TYPE) {
            continue;
        }
        $url = (string) meta_value($source_id, '_kerman_source_url');
        $entity = (string) meta_value($source_id, '_kerman_source_entity');
        $label = trim($source->post_title . ($entity !== '' ? ' — ' . $entity : ''));
        echo '<li>';
        if ($url !== '') {
            echo '<a href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer">' . esc_html($label) . '</a>';
        } else {
            echo esc_html($label);
        }
        echo '</li>';
    }
    echo '</ul>';
}

function render_recent_updates_shortcode($attributes): string
{
    $attributes = shortcode_atts(['limit' => 3], $attributes, 'kermanentzat_recent_updates');
    return render_updates_shortcode([
        'limit' => max(1, min(6, absint($attributes['limit']))),
        'featured' => 'true',
        'filters' => 'false',
    ]);
}

function format_editorial_date(string $date, string $language): string
{
    if ($date === '') {
        return '';
    }
    $timestamp = strtotime($date . ' 12:00:00');
    return $timestamp ? wp_date('j/m/Y', $timestamp) : $date;
}

function format_timeline_date(string $date, string $language, string $precision): string
{
    if ($date === '' || !preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $parts)) {
        return $date;
    }
    if ($precision === 'year') {
        return $parts[1];
    }
    if ($precision === 'month') {
        $months = $language === 'eu'
            ? [1 => 'urtarrila', 'otsaila', 'martxoa', 'apirila', 'maiatza', 'ekaina', 'uztaila', 'abuztua', 'iraila', 'urria', 'azaroa', 'abendua']
            : [1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        $month = $months[(int) $parts[2]] ?? $parts[2];
        return $language === 'eu' ? $parts[1] . 'ko ' . $month : ucfirst($month) . ' de ' . $parts[1];
    }
    return format_editorial_date($date, $language);
}

function format_editorial_datetime(string $date, string $language): string
{
    $timestamp = strtotime($date);
    return $timestamp ? wp_date('j/m/Y H:i', $timestamp) : $date;
}

function register_editorial_patterns(): void
{
    if (!function_exists('register_block_pattern')) {
        return;
    }
    register_block_pattern_category('kermanentzat', ['label' => __('Kermanentzat', 'kermanentzat-editorial')]);
    register_block_pattern('kermanentzat/case-summary', [
        'title' => __('Resumen del caso editable', 'kermanentzat-editorial'),
        'categories' => ['kermanentzat'],
        'content' => '<!-- wp:group {"className":"content-band content-band--light","templateLock":"contentOnly"} --><div class="wp-block-group content-band content-band--light"><!-- wp:group {"className":"content-wrap reading-copy"} --><div class="wp-block-group content-wrap reading-copy"><!-- wp:heading {"level":1} --><h1 class="wp-block-heading">' . esc_html__('Resumen del caso', 'kermanentzat-editorial') . '</h1><!-- /wp:heading --><!-- wp:paragraph --><p>' . esc_html__('Edita aquí el relato aprobado y conserva atribuciones claras.', 'kermanentzat-editorial') . '</p><!-- /wp:paragraph --></div><!-- /wp:group --></div><!-- /wp:group --><!-- wp:shortcode -->[kermanentzat_timeline featured="true"]<!-- /wp:shortcode -->',
    ]);
    register_block_pattern('kermanentzat/updates-archive', [
        'title' => __('Archivo de actualidad', 'kermanentzat-editorial'),
        'categories' => ['kermanentzat'],
        'content' => '<!-- wp:group {"className":"content-band content-band--light","templateLock":"contentOnly"} --><div class="wp-block-group content-band content-band--light"><!-- wp:group {"className":"content-wrap"} --><div class="wp-block-group content-wrap"><!-- wp:heading {"level":1} --><h1 class="wp-block-heading">' . esc_html__('Actualidad', 'kermanentzat-editorial') . '</h1><!-- /wp:heading --><!-- wp:shortcode -->[kermanentzat_updates]<!-- /wp:shortcode --><!-- wp:shortcode -->[kermanentzat_subscription]<!-- /wp:shortcode --></div><!-- /wp:group --></div><!-- /wp:group -->',
    ]);
}

function editorial_template_include(string $template): string
{
    if (is_singular(UPDATE_POST_TYPE)) {
        return PLUGIN_DIR . 'templates/single-kerman-update.php';
    }
    return $template;
}

function translation_for_post(int $post_id, string $language): int
{
    if (function_exists('pll_get_post')) {
        $translation = absint(pll_get_post($post_id, $language));
        if ($translation) {
            return $translation;
        }
    }
    $group = (string) get_post_meta($post_id, '_kerman_translation_group', true);
    if ($group === '') {
        return 0;
    }
    $matches = get_posts([
        'post_type' => UPDATE_POST_TYPE,
        'post_status' => 'publish',
        'numberposts' => -1,
        'meta_key' => '_kerman_translation_group',
        'meta_value' => $group,
    ]);
    foreach ($matches as $match) {
        if (editorial_language_for_post($match->ID) === $language) {
            return $match->ID;
        }
    }
    return 0;
}

function editorial_language_switch_url(): string
{
    if (!is_singular(UPDATE_POST_TYPE)) {
        return '';
    }
    $post_id = get_queried_object_id();
    $current = editorial_language_for_post($post_id);
    $other = $current === 'es' ? 'eu' : 'es';
    $translation = translation_for_post($post_id, $other);
    if ($translation && get_post_status($translation) === 'publish') {
        return get_permalink($translation);
    }
    return home_url($other === 'es' ? '/es/actualidad/?traduccion=no-disponible' : '/berriak/?itzulpena=ez-dago');
}

function editorial_hreflang_links(): array
{
    if (!is_singular(UPDATE_POST_TYPE)) {
        return [];
    }
    $post_id = get_queried_object_id();
    $language = editorial_language_for_post($post_id);
    $links = [$language => get_permalink($post_id)];
    $other = $language === 'es' ? 'eu' : 'es';
    $translation = translation_for_post($post_id, $other);
    if ($translation && get_post_status($translation) === 'publish') {
        $links[$other] = get_permalink($translation);
    }
    return $links;
}

function append_editorial_sitemap_entries(array $entries, string $language): array
{
    $posts = get_posts(array_merge([
        'post_type' => UPDATE_POST_TYPE,
        'post_status' => 'publish',
        'numberposts' => -1,
        'orderby' => 'modified',
        'order' => 'DESC',
    ], function_exists('pll_get_post_language') ? ['lang' => $language] : []));
    foreach ($posts as $post) {
        if (editorial_language_for_post($post->ID) !== $language) {
            continue;
        }
        $permalink = get_permalink($post);
        $path = (string) wp_parse_url($permalink, PHP_URL_PATH);
        $public_url = function_exists('kermanentzat_public_origin')
            ? rtrim(kermanentzat_public_origin(), '/') . '/' . ltrim($path, '/')
            : $permalink;
        $entries[] = [
            'loc' => $public_url,
            'lastmod' => get_post_modified_time('c', true, $post),
        ];
    }
    return $entries;
}

function render_editorial_structured_data(): void
{
    if (!is_singular(UPDATE_POST_TYPE)) {
        return;
    }
    $post = get_queried_object();
    if (!$post instanceof \WP_Post) {
        return;
    }
    $type = update_type_for_post($post->ID);
    $data = [
        '@context' => 'https://schema.org',
        '@type' => $type === 'activity' ? 'Event' : 'NewsArticle',
        'headline' => get_the_title($post),
        'url' => get_permalink($post),
        'datePublished' => get_post_time('c', true, $post),
        'dateModified' => get_post_modified_time('c', true, $post),
        'inLanguage' => editorial_language_for_post($post->ID),
        'publisher' => ['@type' => 'Organization', 'name' => 'Egia Kermanentzat Elkartea'],
    ];
    if ($type === 'activity') {
        $data['startDate'] = (string) meta_value($post->ID, '_kerman_event_start');
        $location = (string) meta_value($post->ID, '_kerman_event_location');
        if ($location !== '') {
            $data['location'] = ['@type' => 'Place', 'name' => $location];
        }
    }
    echo '<script type="application/ld+json">' . wp_json_encode(array_filter($data), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
}
