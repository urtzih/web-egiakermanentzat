<?php

namespace Kermanentzat\Editorial;

defined('ABSPATH') || exit;

const EDITORIAL_NONCE_ACTION = 'kermanentzat_save_editorial_meta';
const EDITORIAL_NONCE_NAME = 'kermanentzat_editorial_nonce';

function register_admin_hooks(): void
{
    add_action('admin_init', __NAMESPACE__ . '\register_editorial_settings');
    add_action('admin_menu', __NAMESPACE__ . '\register_editorial_settings_page');
    add_action('admin_notices', __NAMESPACE__ . '\render_editorial_notices');
    add_action('add_meta_boxes', __NAMESPACE__ . '\add_editorial_meta_boxes');
    add_action('save_post', __NAMESPACE__ . '\save_editorial_meta_boxes', 10, 2);
    add_filter('wp_insert_post_data', __NAMESPACE__ . '\protect_sensitive_publication', 20, 2);
    add_action('wp_after_insert_post', __NAMESPACE__ . '\enforce_persisted_publication_review', 100, 4);
    add_filter('attachment_fields_to_edit', __NAMESPACE__ . '\attachment_editorial_fields', 10, 2);
    add_filter('attachment_fields_to_save', __NAMESPACE__ . '\save_attachment_editorial_fields', 10, 2);
    add_action('admin_enqueue_scripts', __NAMESPACE__ . '\enqueue_admin_assets');
    foreach ([UPDATE_POST_TYPE, TIMELINE_POST_TYPE] as $post_type) {
        add_filter('manage_' . $post_type . '_posts_columns', __NAMESPACE__ . '\editorial_list_columns');
        add_action('manage_' . $post_type . '_posts_custom_column', __NAMESPACE__ . '\render_editorial_list_column', 10, 2);
    }
}

function enqueue_admin_assets(string $hook): void
{
    if (!in_array($hook, ['post.php', 'post-new.php', 'settings_page_kermanentzat-editorial'], true)) {
        return;
    }
    wp_enqueue_style('kermanentzat-editorial-admin', PLUGIN_URL . 'assets/admin.css', [], VERSION);
    wp_enqueue_script('kermanentzat-editorial-admin', PLUGIN_URL . 'assets/admin.js', [], VERSION, true);
    wp_script_add_data('kermanentzat-editorial-admin', 'defer', true);
}

function render_editorial_notices(): void
{
    if (!current_user_can('manage_options') && !current_user_can('edit_kerman_contents')) {
        return;
    }

    if (!function_exists('pll_get_post_language')) {
        echo '<div class="notice notice-info"><p>';
        echo esc_html__('Polylang no está activo. El selector nativo de idioma y versión vinculada mantiene las relaciones EU/ES.', 'kermanentzat-editorial');
        echo '</p></div>';
    }

    $blocked = get_transient('kermanentzat_sensitive_blocked_' . get_current_user_id());
    if ($blocked) {
        delete_transient('kermanentzat_sensitive_blocked_' . get_current_user_id());
        echo '<div class="notice notice-error"><p>';
        echo esc_html__('La publicación sensible quedó como borrador: completa atribución, minimización, derechos y referencia de aprobación externa.', 'kermanentzat-editorial');
        echo '</p></div>';
    }

    $invalid_translation = get_transient('kermanentzat_translation_invalid_' . get_current_user_id());
    if ($invalid_translation) {
        delete_transient('kermanentzat_translation_invalid_' . get_current_user_id());
        echo '<div class="notice notice-error"><p>';
        echo esc_html__('No se pudo vincular la traducción: elige una versión del mismo tipo y del otro idioma.', 'kermanentzat-editorial');
        echo '</p></div>';
    }

    $media_errors = get_transient('kermanentzat_media_blocked_' . get_current_user_id());
    if (is_array($media_errors) && $media_errors !== []) {
        delete_transient('kermanentzat_media_blocked_' . get_current_user_id());
        echo '<div class="notice notice-error"><p>';
        echo esc_html__('La publicación quedó como borrador porque hay medios sin completar:', 'kermanentzat-editorial');
        echo '</p><ul class="ul-disc">';
        foreach ($media_errors as $error) {
            echo '<li>' . esc_html((string) $error) . '</li>';
        }
        echo '</ul></div>';
    }

    if (subscription_is_approved() && !subscription_is_configured()) {
        echo '<div class="notice notice-warning"><p>';
        echo esc_html__('Sender está aprobado pero incompleto. No se cargarán formularios ni se enviarán campañas hasta configurar token, grupo y remitente.', 'kermanentzat-editorial');
        echo '</p></div>';
    }

    render_capacity_notice();
}

function add_editorial_meta_boxes(): void
{
    add_meta_box(
        'kermanentzat-editorial-details',
        __('Datos editoriales', 'kermanentzat-editorial'),
        __NAMESPACE__ . '\render_editorial_details_box',
        [UPDATE_POST_TYPE, TIMELINE_POST_TYPE, SOURCE_POST_TYPE],
        'normal',
        'high'
    );

    add_meta_box(
        'kermanentzat-editorial-review',
        __('Revisión y publicación', 'kermanentzat-editorial'),
        __NAMESPACE__ . '\render_editorial_review_box',
        [UPDATE_POST_TYPE, TIMELINE_POST_TYPE],
        'side',
        'high'
    );

    add_meta_box(
        'kermanentzat-editorial-language',
        __('Idioma y traducción', 'kermanentzat-editorial'),
        __NAMESPACE__ . '\render_editorial_language_box',
        [UPDATE_POST_TYPE, TIMELINE_POST_TYPE],
        'side',
        'high'
    );
}

function editorial_list_columns(array $columns): array
{
    $columns['kerman_language'] = __('Idioma', 'kermanentzat-editorial');
    $columns['kerman_translation'] = __('Versión vinculada', 'kermanentzat-editorial');
    return $columns;
}

function render_editorial_list_column(string $column, int $post_id): void
{
    if ($column === 'kerman_language') {
        echo esc_html(strtoupper(editorial_language_for_post($post_id)));
        return;
    }
    if ($column !== 'kerman_translation') {
        return;
    }
    $language = editorial_language_for_post($post_id) === 'es' ? 'eu' : 'es';
    $translation = linked_editorial_translation($post_id, $language, ['publish', 'future', 'draft', 'pending', 'private']);
    if (!$translation) {
        echo '<span aria-label="' . esc_attr__('Sin versión vinculada', 'kermanentzat-editorial') . '">—</span>';
        return;
    }
    echo '<a href="' . esc_url(get_edit_post_link($translation)) . '">' . esc_html(strtoupper($language) . ' · ' . get_the_title($translation)) . '</a>';
}

function render_editorial_language_box(\WP_Post $post): void
{
    $language = editorial_language_for_post($post->ID);
    $other_language = $language === 'es' ? 'eu' : 'es';
    $linked = linked_editorial_translation($post->ID, $other_language, ['publish', 'future', 'draft', 'pending', 'private']);
    $candidates = get_posts([
        'post_type' => $post->post_type,
        'post_status' => ['publish', 'future', 'draft', 'pending', 'private'],
        'numberposts' => -1,
        'orderby' => 'modified',
        'order' => 'DESC',
        'exclude' => [$post->ID],
    ]);
    ?>
    <p>
        <label for="_kerman_language"><strong><?php esc_html_e('Idioma de esta versión', 'kermanentzat-editorial'); ?></strong></label><br>
        <select class="widefat" id="_kerman_language" name="_kerman_language">
            <option value="eu" <?php selected($language, 'eu'); ?>>EU · Euskara</option>
            <option value="es" <?php selected($language, 'es'); ?>>ES · Castellano</option>
        </select>
    </p>
    <p>
        <label for="kerman_translation_post_id"><strong><?php esc_html_e('Versión vinculada', 'kermanentzat-editorial'); ?></strong></label><br>
        <select class="widefat" id="kerman_translation_post_id" name="kerman_translation_post_id">
            <option value="0"><?php esc_html_e('Sin vincular todavía', 'kermanentzat-editorial'); ?></option>
            <?php foreach ($candidates as $candidate) : ?>
                <?php $candidate_language = editorial_language_for_post($candidate->ID); ?>
                <option value="<?php echo esc_attr((string) $candidate->ID); ?>" data-kerman-language="<?php echo esc_attr($candidate_language); ?>" <?php selected($linked, $candidate->ID); ?>>
                    <?php echo esc_html(strtoupper($candidate_language) . ' · ' . get_the_title($candidate) . ' [' . get_post_status($candidate) . ']'); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <p class="description"><?php esc_html_e('Guarda primero una versión y selecciónala al crear la otra. Solo se enlazan EU y ES del mismo tipo de contenido.', 'kermanentzat-editorial'); ?></p>
    <?php
}

function meta_value(int $post_id, string $key, $default = '')
{
    $value = get_post_meta($post_id, $key, true);
    return $value === '' ? $default : $value;
}

function render_editorial_details_box(\WP_Post $post): void
{
    wp_nonce_field(EDITORIAL_NONCE_ACTION, EDITORIAL_NONCE_NAME);

    if ($post->post_type === UPDATE_POST_TYPE) {
        render_update_fields($post);
    } elseif ($post->post_type === TIMELINE_POST_TYPE) {
        render_timeline_fields($post);
    } else {
        render_source_fields($post);
    }
}

function render_update_fields(\WP_Post $post): void
{
    $selected_type = 'news';
    $terms = wp_get_object_terms($post->ID, UPDATE_TAXONOMY, ['fields' => 'slugs']);
    if (!is_wp_error($terms) && $terms !== []) {
        $selected_type = (string) $terms[0];
    }
    ?>
    <div class="kerman-editorial-fields" data-kerman-type-fields>
        <p>
            <label for="kerman_update_type"><strong><?php esc_html_e('Tipo', 'kermanentzat-editorial'); ?></strong></label><br>
            <select id="kerman_update_type" name="kerman_update_type">
                <?php foreach (update_type_definitions() as $slug => $labels) : ?>
                    <option value="<?php echo esc_attr($slug); ?>" <?php selected($selected_type, $slug); ?>><?php echo esc_html($labels['es'] . ' / ' . $labels['eu']); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php render_input($post, '_kerman_editorial_date', __('Fecha editorial', 'kermanentzat-editorial'), 'date'); ?>
        <p><label><input type="checkbox" name="_kerman_featured" value="1" <?php checked((bool) meta_value($post->ID, '_kerman_featured', false)); ?>> <?php esc_html_e('Destacar en portada', 'kermanentzat-editorial'); ?></label></p>

        <fieldset data-kerman-types="activity">
            <legend><?php esc_html_e('Datos de la actividad', 'kermanentzat-editorial'); ?></legend>
            <?php render_input($post, '_kerman_event_start', __('Inicio', 'kermanentzat-editorial'), 'datetime-local'); ?>
            <?php render_input($post, '_kerman_event_end', __('Fin opcional', 'kermanentzat-editorial'), 'datetime-local'); ?>
            <?php render_input($post, '_kerman_event_location', __('Lugar', 'kermanentzat-editorial')); ?>
            <?php render_input($post, '_kerman_event_url', __('Enlace de inscripción o información', 'kermanentzat-editorial'), 'url'); ?>
        </fieldset>

        <fieldset data-kerman-types="press-archive">
            <legend><?php esc_html_e('Referencia de hemeroteca', 'kermanentzat-editorial'); ?></legend>
            <?php render_input($post, '_kerman_external_outlet', __('Medio', 'kermanentzat-editorial')); ?>
            <?php render_input($post, '_kerman_external_author', __('Autoría opcional', 'kermanentzat-editorial')); ?>
            <?php render_input($post, '_kerman_external_date', __('Fecha original', 'kermanentzat-editorial'), 'date'); ?>
            <?php render_input($post, '_kerman_external_url', __('URL original', 'kermanentzat-editorial'), 'url'); ?>
            <p class="description"><?php esc_html_e('Resume con atribución. No copies el artículo ni su imagen sin permiso.', 'kermanentzat-editorial'); ?></p>
        </fieldset>

        <?php render_source_selector($post); ?>
    </div>
    <?php
}

function render_timeline_fields(\WP_Post $post): void
{
    render_input($post, '_kerman_timeline_start', __('Fecha inicial', 'kermanentzat-editorial'), 'date');
    render_input($post, '_kerman_timeline_end', __('Fecha final opcional', 'kermanentzat-editorial'), 'date');
    $precision = (string) meta_value($post->ID, '_kerman_timeline_precision', 'day');
    ?>
    <p>
        <label for="_kerman_timeline_precision"><strong><?php esc_html_e('Precisión', 'kermanentzat-editorial'); ?></strong></label><br>
        <select id="_kerman_timeline_precision" name="_kerman_timeline_precision">
            <?php foreach (['day' => 'Día', 'month' => 'Mes', 'year' => 'Año', 'range' => 'Periodo'] as $value => $label) : ?>
                <option value="<?php echo esc_attr($value); ?>" <?php selected($precision, $value); ?>><?php echo esc_html($label); ?></option>
            <?php endforeach; ?>
        </select>
    </p>
    <p><label><input type="checkbox" name="_kerman_featured" value="1" <?php checked((bool) meta_value($post->ID, '_kerman_featured', false)); ?>> <?php esc_html_e('Destacar este hito', 'kermanentzat-editorial'); ?></label></p>
    <?php render_source_selector($post); ?>
    <?php
}

function render_source_fields(\WP_Post $post): void
{
    $identifier = (string) meta_value($post->ID, '_kerman_source_id');
    if ($identifier !== '') {
        echo '<p><strong>' . esc_html($identifier) . '</strong></p>';
    }
    render_input($post, '_kerman_source_entity', __('Autor o entidad', 'kermanentzat-editorial'));
    render_input($post, '_kerman_source_date', __('Fecha de la fuente', 'kermanentzat-editorial'), 'date');
    render_input($post, '_kerman_source_url', __('URL pública', 'kermanentzat-editorial'), 'url');
    render_input($post, '_kerman_source_archive_url', __('URL archivada opcional', 'kermanentzat-editorial'), 'url');
    render_input($post, '_kerman_source_checked_at', __('Última comprobación', 'kermanentzat-editorial'), 'date');
}

function render_input(\WP_Post $post, string $key, string $label, string $type = 'text'): void
{
    $value = (string) meta_value($post->ID, $key);
    ?>
    <p>
        <label for="<?php echo esc_attr($key); ?>"><strong><?php echo esc_html($label); ?></strong></label><br>
        <input class="widefat" type="<?php echo esc_attr($type); ?>" id="<?php echo esc_attr($key); ?>" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($value); ?>">
    </p>
    <?php
}

function render_source_selector(\WP_Post $post): void
{
    $selected = sanitize_id_list(meta_value($post->ID, '_kerman_source_ids', []));
    $sources = get_posts([
        'post_type' => SOURCE_POST_TYPE,
        'post_status' => ['publish', 'private', 'draft'],
        'numberposts' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);
    ?>
    <fieldset>
        <legend><?php esc_html_e('Fuentes relacionadas', 'kermanentzat-editorial'); ?></legend>
        <?php if ($sources === []) : ?>
            <p class="description"><?php esc_html_e('Todavía no hay fuentes registradas.', 'kermanentzat-editorial'); ?></p>
        <?php else : ?>
            <select class="widefat" name="_kerman_source_ids[]" multiple size="5">
                <?php foreach ($sources as $source) : ?>
                    <option value="<?php echo esc_attr((string) $source->ID); ?>" <?php selected(in_array($source->ID, $selected, true)); ?>>
                        <?php echo esc_html((string) meta_value($source->ID, '_kerman_source_id') . ' — ' . $source->post_title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
    </fieldset>
    <?php
}

function render_editorial_review_box(\WP_Post $post): void
{
    $checks = sanitize_slug_list(meta_value($post->ID, '_kerman_sensitive_checks', []));
    $definitions = [
        'attribution' => __('He atribuido hechos, declaraciones y valoraciones.', 'kermanentzat-editorial'),
        'minimization' => __('He retirado datos y documentos innecesarios.', 'kermanentzat-editorial'),
        'rights' => __('He comprobado derechos y créditos de los medios.', 'kermanentzat-editorial'),
    ];
    ?>
    <p><label><input type="checkbox" name="_kerman_sensitive" value="1" <?php checked((bool) meta_value($post->ID, '_kerman_sensitive', false)); ?>> <?php esc_html_e('Contenido sensible', 'kermanentzat-editorial'); ?></label></p>
    <div class="kerman-sensitive-checks">
        <?php foreach ($definitions as $slug => $label) : ?>
            <p><label><input type="checkbox" name="_kerman_sensitive_checks[]" value="<?php echo esc_attr($slug); ?>" <?php checked(in_array($slug, $checks, true)); ?>> <?php echo esc_html($label); ?></label></p>
        <?php endforeach; ?>
        <p>
            <label for="_kerman_approval_ref"><strong><?php esc_html_e('Referencia de aprobación externa', 'kermanentzat-editorial'); ?></strong></label>
            <input class="widefat" type="text" id="_kerman_approval_ref" name="_kerman_approval_ref" value="<?php echo esc_attr((string) meta_value($post->ID, '_kerman_approval_ref')); ?>">
        </p>
        <p class="description"><?php esc_html_e('Usa un código o ubicación, nunca adjuntes documentos sensibles.', 'kermanentzat-editorial'); ?></p>
    </div>
    <?php
    render_notification_controls($post);
}

function editable_meta_keys(string $post_type): array
{
    $fields = editorial_meta_definitions()[$post_type] ?? [];
    return array_keys($fields);
}

function save_editorial_meta_boxes(int $post_id, \WP_Post $post): void
{
    if (!isset($_POST[EDITORIAL_NONCE_NAME]) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[EDITORIAL_NONCE_NAME])), EDITORIAL_NONCE_ACTION)) {
        return;
    }
    if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id) || !current_user_can('edit_post', $post_id)) {
        return;
    }
    if (!in_array($post->post_type, [UPDATE_POST_TYPE, TIMELINE_POST_TYPE, SOURCE_POST_TYPE], true)) {
        return;
    }

    $definitions = editorial_meta_definitions()[$post->post_type] ?? [];
    foreach ($definitions as $key => $args) {
        if (in_array($key, ['_kerman_source_id', '_kerman_translation_group'], true)) {
            continue;
        }

        $raw = $_POST[$key] ?? null;
        if ($args['type'] === 'boolean') {
            update_post_meta($post_id, $key, $raw !== null);
            continue;
        }
        if ($args['type'] === 'array') {
            $value = call_user_func($args['sanitize_callback'], $raw ?? []);
            update_post_meta($post_id, $key, $value);
            continue;
        }
        $value = $raw === null ? '' : call_user_func($args['sanitize_callback'], wp_unslash($raw));
        if ($value === '') {
            delete_post_meta($post_id, $key);
        } else {
            update_post_meta($post_id, $key, $value);
        }
    }

    if (in_array($post->post_type, [UPDATE_POST_TYPE, TIMELINE_POST_TYPE], true)) {
        if (function_exists('pll_set_post_language')) {
            pll_set_post_language($post_id, editorial_language_for_post($post_id));
        }
        $translation_id = absint($_POST['kerman_translation_post_id'] ?? 0);
        if ($translation_id === 0) {
            unlink_editorial_translation_group($post_id);
        } elseif (!current_user_can('edit_post', $translation_id) || !link_editorial_translations($post_id, $translation_id)) {
            set_transient('kermanentzat_translation_invalid_' . get_current_user_id(), 1, MINUTE_IN_SECONDS);
        }
    }

    if ($post->post_type === UPDATE_POST_TYPE) {
        $type = sanitize_key((string) ($_POST['kerman_update_type'] ?? 'news'));
        if (array_key_exists($type, update_type_definitions())) {
            wp_set_object_terms($post_id, [$type], UPDATE_TAXONOMY, false);
        }
        save_notification_request($post_id);
    }
}

function protect_sensitive_publication(array $data, array $postarr): array
{
    if (!in_array($data['post_type'] ?? '', [UPDATE_POST_TYPE, TIMELINE_POST_TYPE], true)) {
        return $data;
    }
    if (($data['post_status'] ?? '') !== 'publish') {
        return $data;
    }

    if (!empty($_POST['_kerman_sensitive'])) {
        $checks = sanitize_slug_list(wp_unslash($_POST['_kerman_sensitive_checks'] ?? []));
        $approval = sanitize_text_field(wp_unslash($_POST['_kerman_approval_ref'] ?? ''));
        if (!sensitive_review_is_complete($checks, $approval)) {
            $data['post_status'] = 'draft';
            set_transient('kermanentzat_sensitive_blocked_' . get_current_user_id(), 1, MINUTE_IN_SECONDS);
        }
    }

    $media_errors = public_media_review_errors($data, $postarr);
    if ($media_errors !== []) {
        $data['post_status'] = 'draft';
        set_transient('kermanentzat_media_blocked_' . get_current_user_id(), $media_errors, MINUTE_IN_SECONDS);
    }
    return $data;
}

function enforce_persisted_publication_review(int $post_id, \WP_Post $post, bool $update, ?\WP_Post $post_before): void
{
    static $enforcing = false;
    if ($enforcing || $post->post_status !== 'publish' || !in_array($post->post_type, [UPDATE_POST_TYPE, TIMELINE_POST_TYPE], true)) {
        return;
    }

    $sensitive_incomplete = (bool) meta_value($post_id, '_kerman_sensitive', false)
        && !sensitive_review_is_complete(
            sanitize_slug_list(meta_value($post_id, '_kerman_sensitive_checks', [])),
            (string) meta_value($post_id, '_kerman_approval_ref')
        );
    $media_errors = public_media_review_errors([
        'post_type' => $post->post_type,
        'post_content' => $post->post_content,
    ], ['ID' => $post_id]);
    if (!$sensitive_incomplete && $media_errors === []) {
        return;
    }

    $enforcing = true;
    wp_update_post(['ID' => $post_id, 'post_status' => 'draft']);
    $enforcing = false;
    $user_id = get_current_user_id();
    if ($user_id && $sensitive_incomplete) {
        set_transient('kermanentzat_sensitive_blocked_' . $user_id, 1, MINUTE_IN_SECONDS);
    }
    if ($user_id && $media_errors !== []) {
        set_transient('kermanentzat_media_blocked_' . $user_id, $media_errors, MINUTE_IN_SECONDS);
    }
}

function sensitive_review_is_complete(array $checks, string $approval): bool
{
    $required = ['attribution', 'minimization', 'rights'];
    return count(array_intersect($required, sanitize_slug_list($checks))) === count($required)
        && trim($approval) !== '';
}

function media_review_is_complete(array $media): bool
{
    $rights = sanitize_rights_status((string) ($media['rights_status'] ?? 'unknown'));
    $permission = trim((string) ($media['permission_ref'] ?? ''));
    return trim((string) ($media['alt'] ?? '')) !== ''
        && trim((string) ($media['credit'] ?? '')) !== ''
        && in_array($rights, ['owned', 'licensed', 'permission'], true)
        && (!in_array($rights, ['licensed', 'permission'], true) || $permission !== '');
}

function public_media_review_errors(array $data, array $postarr): array
{
    $post_id = absint($postarr['ID'] ?? 0);
    $attachment_ids = [];
    $thumbnail_id = absint($_POST['_thumbnail_id'] ?? ($post_id ? get_post_thumbnail_id($post_id) : 0));
    if ($thumbnail_id) {
        $attachment_ids[] = $thumbnail_id;
    }
    collect_block_attachment_ids(parse_blocks((string) ($data['post_content'] ?? '')), $attachment_ids);

    $errors = [];
    foreach (array_values(array_unique(array_filter(array_map('absint', $attachment_ids)))) as $attachment_id) {
        if (get_post_type($attachment_id) !== 'attachment') {
            continue;
        }
        $media = [
            'alt' => get_post_meta($attachment_id, '_wp_attachment_image_alt', true),
            'credit' => get_post_meta($attachment_id, '_kerman_media_credit', true),
            'rights_status' => get_post_meta($attachment_id, '_kerman_media_rights_status', true),
            'permission_ref' => get_post_meta($attachment_id, '_kerman_media_permission_ref', true),
        ];
        if (!media_review_is_complete($media)) {
            $label = get_the_title($attachment_id) ?: sprintf(__('Adjunto #%d', 'kermanentzat-editorial'), $attachment_id);
            $errors[] = sprintf(__('%s: falta texto alternativo, crédito o justificación de derechos.', 'kermanentzat-editorial'), $label);
        }
    }
    return $errors;
}

function collect_block_attachment_ids(array $blocks, array &$attachment_ids): void
{
    foreach ($blocks as $block) {
        $attributes = is_array($block['attrs'] ?? null) ? $block['attrs'] : [];
        foreach (['id', 'mediaId'] as $key) {
            if (!empty($attributes[$key])) {
                $attachment_ids[] = absint($attributes[$key]);
            }
        }
        if (is_array($attributes['ids'] ?? null)) {
            $attachment_ids = array_merge($attachment_ids, array_map('absint', $attributes['ids']));
        }
        if (!empty($block['innerBlocks']) && is_array($block['innerBlocks'])) {
            collect_block_attachment_ids($block['innerBlocks'], $attachment_ids);
        }
    }
}

function attachment_editorial_fields(array $fields, \WP_Post $post): array
{
    $fields['kerman_media_credit'] = [
        'label' => __('Crédito editorial', 'kermanentzat-editorial'),
        'input' => 'text',
        'value' => (string) meta_value($post->ID, '_kerman_media_credit'),
    ];
    $fields['kerman_media_rights_status'] = [
        'label' => __('Derechos', 'kermanentzat-editorial'),
        'input' => 'html',
        'html' => sprintf(
            '<select name="attachments[%1$d][kerman_media_rights_status]">%2$s</select>',
            $post->ID,
            implode('', array_map(static function (string $value, string $label) use ($post): string {
                return sprintf('<option value="%s" %s>%s</option>', esc_attr($value), selected(meta_value($post->ID, '_kerman_media_rights_status', 'unknown'), $value, false), esc_html($label));
            }, array_keys(media_rights_labels()), media_rights_labels()))
        ),
    ];
    $fields['kerman_media_permission_ref'] = [
        'label' => __('Permiso/licencia', 'kermanentzat-editorial'),
        'input' => 'text',
        'value' => (string) meta_value($post->ID, '_kerman_media_permission_ref'),
        'helps' => __('Referencia documental; no subas datos sensibles.', 'kermanentzat-editorial'),
    ];
    return $fields;
}

function media_rights_labels(): array
{
    return [
        'unknown' => __('Pendiente', 'kermanentzat-editorial'),
        'owned' => __('Propio', 'kermanentzat-editorial'),
        'licensed' => __('Licenciado', 'kermanentzat-editorial'),
        'permission' => __('Permiso documentado', 'kermanentzat-editorial'),
        'external-only' => __('Solo enlace externo', 'kermanentzat-editorial'),
    ];
}

function save_attachment_editorial_fields(array $post, array $attachment): array
{
    $post_id = absint($post['ID'] ?? 0);
    if (!$post_id || !current_user_can('edit_post', $post_id)) {
        return $post;
    }
    $values = [
        '_kerman_media_credit' => sanitize_text_field($attachment['kerman_media_credit'] ?? ''),
        '_kerman_media_rights_status' => sanitize_rights_status($attachment['kerman_media_rights_status'] ?? 'unknown'),
        '_kerman_media_permission_ref' => sanitize_text_field($attachment['kerman_media_permission_ref'] ?? ''),
    ];
    foreach ($values as $key => $value) {
        if ($value === '') {
            delete_post_meta($post_id, $key);
        } else {
            update_post_meta($post_id, $key, $value);
        }
    }
    return $post;
}

function register_editorial_settings(): void
{
    register_setting('kermanentzat_editorial', OPTION_SETTINGS, [
        'type' => 'array',
        'sanitize_callback' => __NAMESPACE__ . '\sanitize_editorial_settings',
        'default' => [],
    ]);
}

function sanitize_editorial_settings($value): array
{
    $value = is_array($value) ? $value : [];
    return [
        'sender_group_id' => sanitize_text_field($value['sender_group_id'] ?? ''),
        'sender_account_public_id' => sanitize_text_field($value['sender_account_public_id'] ?? ''),
        'sender_form_id' => sanitize_text_field($value['sender_form_id'] ?? ''),
        'sender_form_embed_id' => sanitize_text_field($value['sender_form_embed_id'] ?? ''),
        'sender_form_url' => esc_url_raw($value['sender_form_url'] ?? ''),
        'sender_form_id_eu' => sanitize_text_field($value['sender_form_id_eu'] ?? ''),
        'sender_form_id_es' => sanitize_text_field($value['sender_form_id_es'] ?? ''),
        'sender_form_url_eu' => esc_url_raw($value['sender_form_url_eu'] ?? ''),
        'sender_form_url_es' => esc_url_raw($value['sender_form_url_es'] ?? ''),
        'sender_from_name' => sanitize_text_field($value['sender_from_name'] ?? 'Egia Kermanentzat Elkartea'),
        'sender_reply_to' => sanitize_email($value['sender_reply_to'] ?? ''),
        'sender_subscriber_limit' => max(1, absint($value['sender_subscriber_limit'] ?? 2500)),
        'sender_monthly_limit' => max(1, absint($value['sender_monthly_limit'] ?? 15000)),
        'sender_subscriber_count' => absint($value['sender_subscriber_count'] ?? 0),
        'sender_monthly_projection' => absint($value['sender_monthly_projection'] ?? 0),
    ];
}

function register_editorial_settings_page(): void
{
    add_options_page(
        __('Kermanentzat Editorial', 'kermanentzat-editorial'),
        __('Kermanentzat Editorial', 'kermanentzat-editorial'),
        'manage_options',
        'kermanentzat-editorial',
        __NAMESPACE__ . '\render_editorial_settings_page'
    );
}

function render_editorial_settings_page(): void
{
    if (!current_user_can('manage_options')) {
        return;
    }
    $config = settings();
    $fields = [
        'sender_group_id' => ['Grupo de destinatarios', 'text'],
        'sender_account_public_id' => ['ID público de la cuenta', 'text'],
        'sender_form_id' => ['ID público del formulario bilingüe', 'text'],
        'sender_form_embed_id' => ['ID del contenedor embebido', 'text'],
        'sender_form_url' => ['URL alternativa del formulario', 'url'],
        'sender_from_name' => ['Nombre remitente', 'text'],
        'sender_reply_to' => ['Email remitente verificado', 'email'],
        'sender_subscriber_limit' => ['Límite de suscriptores', 'number'],
        'sender_monthly_limit' => ['Límite mensual de envíos', 'number'],
        'sender_subscriber_count' => ['Suscriptores activos (control manual)', 'number'],
        'sender_monthly_projection' => ['Proyección mensual (control manual)', 'number'],
    ];
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Kermanentzat Editorial', 'kermanentzat-editorial'); ?></h1>
        <p><?php echo subscription_is_approved() ? esc_html__('Integración Sender aprobada por configuración.', 'kermanentzat-editorial') : esc_html__('Integración Sender desactivada hasta definir KERMANENTZAT_SENDER_APPROVED=true.', 'kermanentzat-editorial'); ?></p>
        <p><?php esc_html_e('El token API debe definirse como secreto del servidor; nunca se guarda aquí.', 'kermanentzat-editorial'); ?></p>
        <form action="options.php" method="post">
            <?php settings_fields('kermanentzat_editorial'); ?>
            <table class="form-table" role="presentation">
                <?php foreach ($fields as $key => [$label, $type]) : ?>
                    <tr>
                        <th scope="row"><label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label></th>
                        <td><input class="regular-text" id="<?php echo esc_attr($key); ?>" type="<?php echo esc_attr($type); ?>" name="<?php echo esc_attr(OPTION_SETTINGS . '[' . $key . ']'); ?>" value="<?php echo esc_attr((string) $config[$key]); ?>"></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
