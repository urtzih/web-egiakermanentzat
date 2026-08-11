<?php

namespace Kermanentzat\Editorial;

defined('ABSPATH') || exit;

const UPDATE_POST_TYPE = 'kerman_update';
const TIMELINE_POST_TYPE = 'kerman_timeline';
const SOURCE_POST_TYPE = 'kerman_source';
const UPDATE_TAXONOMY = 'kerman_update_type';

function register_content_hooks(): void
{
    add_action('init', __NAMESPACE__ . '\register_content_types');
    add_action('init', __NAMESPACE__ . '\register_editorial_meta', 20);
    add_action('init', __NAMESPACE__ . '\seed_update_types', 30);
    add_action('save_post_' . SOURCE_POST_TYPE, __NAMESPACE__ . '\ensure_source_identifier', 20, 3);
    add_filter('post_type_link', __NAMESPACE__ . '\localized_update_permalink', 10, 2);
    add_action('init', __NAMESPACE__ . '\register_localized_rewrite_rules', 40);
    add_filter('query_vars', static function (array $vars): array {
        $vars[] = 'kerman_language';
        return $vars;
    });
}

function editorial_capabilities(): array
{
    return [
        'edit_kerman_content',
        'read_kerman_content',
        'delete_kerman_content',
        'edit_kerman_contents',
        'edit_others_kerman_contents',
        'publish_kerman_contents',
        'read_private_kerman_contents',
        'delete_kerman_contents',
        'delete_private_kerman_contents',
        'delete_published_kerman_contents',
        'delete_others_kerman_contents',
        'edit_private_kerman_contents',
        'edit_published_kerman_contents',
    ];
}

function register_editorial_role(): void
{
    $caps = [
        'read' => true,
        'upload_files' => true,
        'edit_pages' => true,
        'edit_published_pages' => true,
        'publish_pages' => true,
        'delete_pages' => false,
        'unfiltered_html' => false,
    ];

    foreach (editorial_capabilities() as $capability) {
        $caps[$capability] = true;
    }

    add_role('kermanentzat_editor', __('Editora Kermanentzat', 'kermanentzat-editorial'), $caps);

    foreach (['administrator', 'editor', 'kermanentzat_editor'] as $role_name) {
        $role = get_role($role_name);
        if (!$role) {
            continue;
        }
        foreach (editorial_capabilities() as $capability) {
            $role->add_cap($capability);
        }
    }
}

function post_type_capabilities(): array
{
    return [
        'edit_post' => 'edit_kerman_content',
        'read_post' => 'read_kerman_content',
        'delete_post' => 'delete_kerman_content',
        'edit_posts' => 'edit_kerman_contents',
        'edit_others_posts' => 'edit_others_kerman_contents',
        'publish_posts' => 'publish_kerman_contents',
        'read_private_posts' => 'read_private_kerman_contents',
        'delete_posts' => 'delete_kerman_contents',
        'delete_private_posts' => 'delete_private_kerman_contents',
        'delete_published_posts' => 'delete_published_kerman_contents',
        'delete_others_posts' => 'delete_others_kerman_contents',
        'edit_private_posts' => 'edit_private_kerman_contents',
        'edit_published_posts' => 'edit_published_kerman_contents',
        'create_posts' => 'edit_kerman_contents',
    ];
}

function register_content_types(): void
{
    $capabilities = post_type_capabilities();

    register_post_type(UPDATE_POST_TYPE, [
        'labels' => [
            'name' => __('Berriak / Actualidad', 'kermanentzat-editorial'),
            'singular_name' => __('Publicación', 'kermanentzat-editorial'),
            'add_new_item' => __('Añadir publicación', 'kermanentzat-editorial'),
            'edit_item' => __('Editar publicación', 'kermanentzat-editorial'),
        ],
        'public' => true,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-megaphone',
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions'],
        'has_archive' => false,
        'rewrite' => false,
        'capabilities' => $capabilities,
        'map_meta_cap' => false,
        'delete_with_user' => false,
    ]);

    register_post_type(TIMELINE_POST_TYPE, [
        'labels' => [
            'name' => __('Cronología', 'kermanentzat-editorial'),
            'singular_name' => __('Hito', 'kermanentzat-editorial'),
            'add_new_item' => __('Añadir hito', 'kermanentzat-editorial'),
            'edit_item' => __('Editar hito', 'kermanentzat-editorial'),
        ],
        'public' => false,
        'publicly_queryable' => false,
        'show_ui' => true,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-backup',
        'supports' => ['title', 'editor', 'excerpt', 'revisions'],
        'capabilities' => $capabilities,
        'map_meta_cap' => false,
        'delete_with_user' => false,
    ]);

    register_post_type(SOURCE_POST_TYPE, [
        'labels' => [
            'name' => __('Fuentes', 'kermanentzat-editorial'),
            'singular_name' => __('Fuente', 'kermanentzat-editorial'),
            'add_new_item' => __('Añadir fuente', 'kermanentzat-editorial'),
            'edit_item' => __('Editar fuente', 'kermanentzat-editorial'),
        ],
        'public' => false,
        'publicly_queryable' => false,
        'show_ui' => true,
        'show_in_rest' => false,
        'menu_icon' => 'dashicons-media-document',
        'supports' => ['title', 'excerpt', 'revisions'],
        'capabilities' => $capabilities,
        'map_meta_cap' => false,
        'delete_with_user' => false,
    ]);

    register_taxonomy(UPDATE_TAXONOMY, [UPDATE_POST_TYPE], [
        'labels' => [
            'name' => __('Tipos de publicación', 'kermanentzat-editorial'),
            'singular_name' => __('Tipo de publicación', 'kermanentzat-editorial'),
        ],
        'public' => true,
        'show_ui' => true,
        'show_in_rest' => true,
        'hierarchical' => false,
        'rewrite' => false,
        'meta_box_cb' => false,
    ]);
}

function update_type_definitions(): array
{
    return [
        'news' => ['es' => 'Noticia', 'eu' => 'Albistea'],
        'press-release' => ['es' => 'Nota de prensa', 'eu' => 'Prentsa-oharra'],
        'statement' => ['es' => 'Comunicado', 'eu' => 'Komunikatua'],
        'activity' => ['es' => 'Actividad', 'eu' => 'Jarduera'],
        'press-archive' => ['es' => 'Hemeroteca', 'eu' => 'Hemeroteka'],
    ];
}

function seed_update_types(): void
{
    if (!taxonomy_exists(UPDATE_TAXONOMY)) {
        return;
    }

    foreach (update_type_definitions() as $slug => $labels) {
        if (!term_exists($slug, UPDATE_TAXONOMY)) {
            wp_insert_term($labels['es'] . ' / ' . $labels['eu'], UPDATE_TAXONOMY, ['slug' => $slug]);
        }
    }
}

function editorial_meta_definitions(): array
{
    return [
        UPDATE_POST_TYPE => [
            '_kerman_editorial_date' => ['type' => 'string', 'sanitize_callback' => __NAMESPACE__ . '\sanitize_date'],
            '_kerman_featured' => ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'],
            '_kerman_event_start' => ['type' => 'string', 'sanitize_callback' => __NAMESPACE__ . '\sanitize_datetime'],
            '_kerman_event_end' => ['type' => 'string', 'sanitize_callback' => __NAMESPACE__ . '\sanitize_datetime'],
            '_kerman_event_location' => ['type' => 'string', 'sanitize_callback' => 'sanitize_text_field'],
            '_kerman_event_url' => ['type' => 'string', 'sanitize_callback' => 'esc_url_raw'],
            '_kerman_external_outlet' => ['type' => 'string', 'sanitize_callback' => 'sanitize_text_field'],
            '_kerman_external_author' => ['type' => 'string', 'sanitize_callback' => 'sanitize_text_field'],
            '_kerman_external_date' => ['type' => 'string', 'sanitize_callback' => __NAMESPACE__ . '\sanitize_date'],
            '_kerman_external_url' => ['type' => 'string', 'sanitize_callback' => 'esc_url_raw'],
            '_kerman_source_ids' => ['type' => 'array', 'sanitize_callback' => __NAMESPACE__ . '\sanitize_id_list'],
            '_kerman_sensitive' => ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'],
            '_kerman_sensitive_checks' => ['type' => 'array', 'sanitize_callback' => __NAMESPACE__ . '\sanitize_slug_list'],
            '_kerman_approval_ref' => ['type' => 'string', 'sanitize_callback' => 'sanitize_text_field'],
        ],
        TIMELINE_POST_TYPE => [
            '_kerman_timeline_start' => ['type' => 'string', 'sanitize_callback' => __NAMESPACE__ . '\sanitize_date'],
            '_kerman_timeline_end' => ['type' => 'string', 'sanitize_callback' => __NAMESPACE__ . '\sanitize_date'],
            '_kerman_timeline_precision' => ['type' => 'string', 'sanitize_callback' => __NAMESPACE__ . '\sanitize_date_precision'],
            '_kerman_featured' => ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'],
            '_kerman_source_ids' => ['type' => 'array', 'sanitize_callback' => __NAMESPACE__ . '\sanitize_id_list'],
            '_kerman_sensitive' => ['type' => 'boolean', 'sanitize_callback' => 'rest_sanitize_boolean'],
            '_kerman_sensitive_checks' => ['type' => 'array', 'sanitize_callback' => __NAMESPACE__ . '\sanitize_slug_list'],
            '_kerman_approval_ref' => ['type' => 'string', 'sanitize_callback' => 'sanitize_text_field'],
        ],
        SOURCE_POST_TYPE => [
            '_kerman_source_id' => ['type' => 'string', 'sanitize_callback' => 'sanitize_text_field'],
            '_kerman_source_entity' => ['type' => 'string', 'sanitize_callback' => 'sanitize_text_field'],
            '_kerman_source_date' => ['type' => 'string', 'sanitize_callback' => __NAMESPACE__ . '\sanitize_date'],
            '_kerman_source_url' => ['type' => 'string', 'sanitize_callback' => 'esc_url_raw'],
            '_kerman_source_archive_url' => ['type' => 'string', 'sanitize_callback' => 'esc_url_raw'],
            '_kerman_source_checked_at' => ['type' => 'string', 'sanitize_callback' => __NAMESPACE__ . '\sanitize_date'],
        ],
        'attachment' => [
            '_kerman_media_credit' => ['type' => 'string', 'sanitize_callback' => 'sanitize_text_field'],
            '_kerman_media_rights_status' => ['type' => 'string', 'sanitize_callback' => __NAMESPACE__ . '\sanitize_rights_status'],
            '_kerman_media_permission_ref' => ['type' => 'string', 'sanitize_callback' => 'sanitize_text_field'],
        ],
    ];
}

function register_editorial_meta(): void
{
    foreach (editorial_meta_definitions() as $post_type => $fields) {
        foreach ($fields as $key => $args) {
            $schema = [
                'type' => $args['type'],
                'single' => true,
                'default' => $args['type'] === 'boolean' ? false : ($args['type'] === 'array' ? [] : ''),
                'sanitize_callback' => $args['sanitize_callback'],
                'auth_callback' => static function (bool $allowed, string $meta_key, int $post_id): bool {
                    return current_user_can('edit_post', $post_id);
                },
                'show_in_rest' => false,
            ];
            register_post_meta($post_type, $key, $schema);
        }
    }
}

function sanitize_date($value): string
{
    $value = sanitize_text_field((string) $value);
    return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : '';
}

function sanitize_datetime($value): string
{
    $value = sanitize_text_field((string) $value);
    return preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $value) ? $value : '';
}

function sanitize_date_precision($value): string
{
    $value = sanitize_key((string) $value);
    return in_array($value, ['day', 'month', 'year', 'range'], true) ? $value : 'day';
}

function sanitize_rights_status($value): string
{
    $value = sanitize_key((string) $value);
    return in_array($value, ['owned', 'licensed', 'permission', 'external-only', 'unknown'], true) ? $value : 'unknown';
}

function sanitize_id_list($value): array
{
    $values = is_array($value) ? $value : explode(',', (string) $value);
    return array_values(array_unique(array_filter(array_map('absint', $values))));
}

function sanitize_slug_list($value): array
{
    $values = is_array($value) ? $value : [];
    return array_values(array_unique(array_filter(array_map('sanitize_key', $values))));
}

function ensure_source_identifier(int $post_id, \WP_Post $post, bool $update): void
{
    if (wp_is_post_revision($post_id) || get_post_meta($post_id, '_kerman_source_id', true)) {
        return;
    }

    update_post_meta($post_id, '_kerman_source_id', sprintf('SRC-%03d', $post_id));
}

function editorial_language_for_post(int $post_id): string
{
    if (function_exists('pll_get_post_language')) {
        $language = pll_get_post_language($post_id, 'slug');
        if (in_array($language, ['eu', 'es'], true)) {
            return $language;
        }
    }

    $stored = get_post_meta($post_id, '_kerman_language', true);
    return $stored === 'es' ? 'es' : 'eu';
}

function localized_update_permalink(string $permalink, \WP_Post $post): string
{
    if ($post->post_type !== UPDATE_POST_TYPE) {
        return $permalink;
    }

    $base = editorial_language_for_post($post->ID) === 'es' ? '/es/actualidad/' : '/berriak/';
    return home_url($base . $post->post_name . '/');
}

function register_localized_rewrite_rules(): void
{
    add_rewrite_rule('^berriak/([^/]+)/?$', 'index.php?post_type=' . UPDATE_POST_TYPE . '&name=$matches[1]&kerman_language=eu', 'top');
    add_rewrite_rule('^es/actualidad/([^/]+)/?$', 'index.php?post_type=' . UPDATE_POST_TYPE . '&name=$matches[1]&kerman_language=es', 'top');
}
