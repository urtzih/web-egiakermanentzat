<?php
/**
 * Plugin Name: Kermanentzat · Publicación producción 2026-09-17
 * Description: Vista previa, copia, publicación y restauración de la entrega editorial y documental.
 * Version: 1.0.0
 * Requires PHP: 8.2
 * Author: Egia Kermanentzat
 */

namespace Kermanentzat\ProductionRelease;

defined('ABSPATH') || exit;

const RELEASE = '2026-09-17-case-editorial-v1';
const BACKUP_OPTION = 'kermanentzat_production_release_20260917_v1';
const EDITORIAL_PLUGIN = 'kermanentzat-editorial/kermanentzat-editorial.php';
const MEDIA_SOURCE = 'kermanentzat-release-20260917';

function register(): void
{
    add_action('admin_menu', __NAMESPACE__ . '\\menu');
    foreach (['backup', 'download', 'apply', 'restore'] as $action) {
        add_action('admin_post_kermanentzat_release_' . $action, __NAMESPACE__ . '\\handle_' . $action);
    }
}
add_action('plugins_loaded', __NAMESPACE__ . '\\register');

function menu(): void
{
    add_management_page('Publicación Kermanentzat', 'Publicación Kermanentzat', 'manage_options', 'kermanentzat-production-release', __NAMESPACE__ . '\\screen');
}

function authorize(string $action): void
{
    if (!current_user_can('manage_options') || !current_user_can('unfiltered_html')) {
        wp_die('Se requiere una cuenta administradora.', 403);
    }
    check_admin_referer('kermanentzat_release_' . $action);
    assert_environment();
}

function assert_environment(): void
{
    if (!allowed_host()) { throw new \RuntimeException('Dominio no autorizado.'); }
    if (function_exists('kermanentzat_subscription_is_public') && \kermanentzat_subscription_is_public()) {
        throw new \RuntimeException('La suscripción debe estar desactivada para esta entrega.');
    }
}

function allowed_host(): bool
{
    return in_array(strtolower((string) wp_parse_url(home_url('/'), PHP_URL_HOST)), [
        'egiakermanentzat.eus',
        'web-egiakermanentzat-stag.urtzi.fun',
        'localhost',
    ], true);
}

function load_editorial(): void
{
    $file = WP_PLUGIN_DIR . '/' . EDITORIAL_PLUGIN;
    if (!is_file($file)) {
        throw new \RuntimeException('No está instalado el plugin editorial de esta entrega.');
    }
    require_once $file;
    if (\Kermanentzat\Editorial\VERSION !== '0.2.8') {
        throw new \RuntimeException('La versión editorial no corresponde a esta publicación.');
    }
    \Kermanentzat\Editorial\register_content_types();
}

function migration_preview(): array
{
    load_editorial();
    return \Kermanentzat\Editorial\run_editorial_migration(['dry-run' => true, 'strict' => true, 'force' => true]);
}

function target_page_paths(): array
{
    return [
        'kasuaren-laburpena', 'es/resumen-del-caso', 'berriak', 'es/actualidad',
        'kronologia', 'es/cronologia', 'hemeroteka', 'es/hemeroteca',
        'harpidetza', 'es/suscripcion', 'kontaktua', 'es/contacto',
        'pribatutasun-politika', 'cookie-politika',
        'es/politica-de-privacidad', 'es/politica-de-cookies',
    ];
}

function snapshot_post(\WP_Post $post): array
{
    $fields = get_object_vars($post);
    unset($fields['filter']);
    $terms = [];
    foreach (get_object_taxonomies($post->post_type) as $taxonomy) {
        $slugs = wp_get_object_terms($post->ID, $taxonomy, ['fields' => 'slugs']);
        if (!is_wp_error($slugs) && $slugs !== []) {
            $terms[$taxonomy] = array_values($slugs);
        }
    }
    return [
        'fields' => $fields,
        'meta' => get_post_meta($post->ID),
        'terms' => $terms,
    ];
}

function target_posts(): array
{
    $posts = [];
    foreach (target_page_paths() as $path) {
        $post = get_page_by_path($path, OBJECT, 'page');
        if ($post instanceof \WP_Post) {
            $posts[$post->ID] = $post;
        }
    }
    $managed = get_posts([
        'post_type' => ['kerman_update', 'kerman_timeline', 'kerman_source'],
        'post_status' => 'any',
        'numberposts' => -1,
    ]);
    foreach ($managed as $post) {
        $posts[$post->ID] = $post;
    }
    $attachments = get_posts([
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'numberposts' => -1,
        'meta_key' => '_kermanentzat_case_media_key',
    ]);
    foreach ($attachments as $post) {
        $posts[$post->ID] = $post;
    }
    ksort($posts);
    return $posts;
}

function option_snapshot(string $name): array
{
    $sentinel = new \stdClass();
    $value = get_option($name, $sentinel);
    return $value === $sentinel ? ['exists' => false] : ['exists' => true, 'value' => $value];
}

function state(): array
{
    $posts = [];
    foreach (target_posts() as $id => $post) {
        $posts[(string) $id] = snapshot_post($post);
    }
    $options = [];
    foreach ([
        'active_plugins',
        'kermanentzat_editorial_schema_version',
        'kermanentzat_editorial_settings',
        'kermanentzat_editorial_migration_5',
        'kermanentzat_editorial_migration_6',
        'kermanentzat_case_media_sync_version',
        $GLOBALS['wpdb']->prefix . 'user_roles',
    ] as $name) {
        $options[$name] = option_snapshot($name);
    }
    return ['release' => RELEASE, 'site' => home_url('/'), 'posts' => $posts, 'options' => $options];
}

function state_hash(array $state): string
{
    return hash('sha256', wp_json_encode($state, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
}

function backup(): array
{
    assert_environment();
    migration_preview();
    $existing = get_option(BACKUP_OPTION);
    if (is_array($existing)) {
        return $existing;
    }
    $before = state();
    $backup = [
        'version' => 1,
        'release' => RELEASE,
        'created_at' => gmdate('c'),
        'before' => $before,
        'before_hash' => state_hash($before),
        'after_hash' => '',
    ];
    if (!add_option(BACKUP_OPTION, $backup, '', false)) {
        throw new \RuntimeException('No se pudo guardar la copia previa.');
    }
    return $backup;
}

function verify_media_source(): string
{
    $uploads = wp_upload_dir();
    if (!empty($uploads['error'])) {
        throw new \RuntimeException((string) $uploads['error']);
    }
    $source = trailingslashit((string) $uploads['basedir']) . MEDIA_SOURCE;
    $manifest = json_decode((string) file_get_contents(__DIR__ . '/media-manifest.json'), true);
    if (!is_array($manifest) || count($manifest) !== 17) {
        throw new \RuntimeException('El manifiesto multimedia no es válido.');
    }
    foreach ($manifest as $file) {
        $path = $source . '/' . str_replace('/', DIRECTORY_SEPARATOR, (string) $file['path']);
        if (!is_file($path) || filesize($path) !== (int) $file['size'] || hash_file('sha256', $path) !== (string) $file['sha256']) {
            throw new \RuntimeException('Falta o no coincide el recurso: ' . $file['path']);
        }
    }
    return $source;
}

function remove_source_dir(string $directory): void
{
    $uploads = wp_upload_dir();
    $expected = realpath((string) $uploads['basedir']) . DIRECTORY_SEPARATOR . MEDIA_SOURCE;
    $real = realpath($directory);
    if ($real === false || $real !== $expected) {
        throw new \RuntimeException('Ruta temporal no segura.');
    }
    $iterator = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($real, \FilesystemIterator::SKIP_DOTS),
        \RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($iterator as $item) {
        $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
    }
    rmdir($real);
}

function apply_release(): array
{
    assert_environment();
    $saved = get_option(BACKUP_OPTION);
    if (!is_array($saved) || ($saved['release'] ?? '') !== RELEASE) {
        throw new \RuntimeException('Debes crear y descargar primero la copia de seguridad.');
    }
    if (($saved['after_hash'] ?? '') !== '') {
        if (hash_equals((string) $saved['after_hash'], state_hash(state()))) {
            return ['La publicación ya estaba aplicada; no se ha duplicado nada.'];
        }
        throw new \RuntimeException('El sitio cambió después de la publicación. No se sobrescribe.');
    }
    if (!hash_equals((string) $saved['before_hash'], state_hash(state()))) {
        throw new \RuntimeException('El sitio cambió desde la copia previa. Vuelve a revisar antes de publicar.');
    }

    $source = verify_media_source();
    migration_preview();
    try {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    if (!is_plugin_active(EDITORIAL_PLUGIN)) {
        $result = activate_plugin(EDITORIAL_PLUGIN);
        if (is_wp_error($result)) {
            throw new \RuntimeException($result->get_error_message());
        }
    }
    load_editorial();
    $messages = \Kermanentzat\Editorial\run_editorial_migration(['strict' => true, 'force' => true]);

    $sync = get_theme_file_path('inc/sync-case-media.php');
    if (!is_file($sync)) {
        throw new \RuntimeException('El tema no contiene el sincronizador multimedia.');
    }
    if (!defined('KERMANENTZAT_CASE_MEDIA_DEFER')) {
        define('KERMANENTZAT_CASE_MEDIA_DEFER', true);
    }
    require_once $sync;
    \kermanentzat_case_media_sync_run($source);
    flush_rewrite_rules(false);
    $after = state();
    $saved['after_hash'] = state_hash($after);
    $saved['applied_at'] = gmdate('c');
    $saved['messages'] = $messages;
    update_option(BACKUP_OPTION, $saved, false);
    remove_source_dir($source);
    return $messages;
    } catch (\Throwable $e) {
        // Record the partial result so restoration remains possible after any
        // failed page or media write. Never replace the original backup.
        $saved['after_hash'] = state_hash(state());
        $saved['failure'] = $e->getMessage();
        update_option(BACKUP_OPTION, $saved, false);
        throw $e;
    }
}

function restore_post(array $snapshot): void
{
    $fields = $snapshot['fields'];
    $id = (int) $fields['ID'];
    $postarr = array_intersect_key($fields, array_flip([
        'ID', 'post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_title', 'post_excerpt',
        'post_status', 'comment_status', 'ping_status', 'post_password', 'post_name', 'to_ping', 'pinged',
        'post_modified', 'post_modified_gmt', 'post_content_filtered', 'post_parent', 'guid', 'menu_order',
        'post_type', 'post_mime_type', 'comment_count',
    ]));
    $result = get_post($id) ? wp_update_post(wp_slash($postarr), true) : wp_insert_post(wp_slash($postarr), true);
    if (is_wp_error($result)) {
        throw new \RuntimeException($result->get_error_message());
    }
    foreach (array_keys(get_post_meta($id)) as $key) {
        delete_post_meta($id, $key);
    }
    foreach ($snapshot['meta'] as $key => $values) {
        foreach ($values as $value) {
            add_post_meta($id, $key, maybe_unserialize($value));
        }
    }
    foreach (get_object_taxonomies((string) $fields['post_type']) as $taxonomy) {
        wp_set_object_terms($id, $snapshot['terms'][$taxonomy] ?? [], $taxonomy, false);
    }
}

function restore_release(): void
{
    assert_environment();
    $saved = get_option(BACKUP_OPTION);
    if (!is_array($saved) || empty($saved['after_hash'])) {
        throw new \RuntimeException('No existe una publicación aplicada que restaurar.');
    }
    if (!hash_equals((string) $saved['after_hash'], state_hash(state()))) {
        throw new \RuntimeException('Hay cambios posteriores. Se conserva el sitio y la copia para revisión manual.');
    }

    $before = $saved['before'];
    $before_ids = array_map('intval', array_keys($before['posts']));
    foreach (target_posts() as $id => $post) {
        if (!in_array((int) $id, $before_ids, true)) {
            wp_delete_post((int) $id, true);
        }
    }
    foreach ($before['posts'] as $snapshot) {
        restore_post($snapshot);
    }
    foreach ($before['options'] as $name => $option) {
        if ($option['exists']) {
            update_option($name, $option['value'], false);
        } else {
            delete_option($name);
        }
    }
    flush_rewrite_rules(false);
    $saved['restored_at'] = gmdate('c');
    $saved['after_hash'] = '';
    update_option(BACKUP_OPTION, $saved, false);
}

function redirect_with(string $status): never
{
    wp_safe_redirect(add_query_arg(['page' => 'kermanentzat-production-release', 'release_status' => $status], admin_url('tools.php')));
    exit;
}

function handle_backup(): void
{
    authorize('backup');
    try { backup(); redirect_with('backup-ok'); } catch (\Throwable $e) { wp_die(esc_html($e->getMessage())); }
}

function handle_download(): void
{
    authorize('download');
    $saved = get_option(BACKUP_OPTION);
    if (!is_array($saved)) { wp_die('No existe la copia.', 404); }
    nocache_headers();
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="kermanentzat-production-backup-' . RELEASE . '.json"');
    echo wp_json_encode($saved, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function handle_apply(): void
{
    authorize('apply');
    try { apply_release(); redirect_with('apply-ok'); } catch (\Throwable $e) { wp_die(esc_html($e->getMessage())); }
}

function handle_restore(): void
{
    authorize('restore');
    try { restore_release(); redirect_with('restore-ok'); } catch (\Throwable $e) { wp_die(esc_html($e->getMessage())); }
}

function form(string $action, string $label, string $class = 'button'): void
{
    echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '" style="display:inline-block;margin-right:8px">';
    echo '<input type="hidden" name="action" value="kermanentzat_release_' . esc_attr($action) . '">';
    wp_nonce_field('kermanentzat_release_' . $action);
    submit_button($label, $class, 'submit', false);
    echo '</form>';
}

function screen(): void
{
    if (!current_user_can('manage_options')) { return; }
    echo '<div class="wrap"><h1>Publicación de producción</h1>';
    echo '<p>Entrega <code>' . esc_html(RELEASE) . '</code>. La suscripción debe permanecer desactivada.</p>';
    if (!allowed_host()) {
        echo '<div class="notice notice-error"><p>Dominio no autorizado: ' . esc_html(home_url('/')) . '</p></div></div>';
        return;
    }
    if (function_exists('kermanentzat_subscription_is_public') && \kermanentzat_subscription_is_public()) {
        echo '<div class="notice notice-error"><p>La suscripción está activa. No se permite esta publicación.</p></div></div>';
        return;
    }
    $preview_ok = false;
    try {
        $messages = migration_preview();
        $preview_ok = true;
        echo '<div class="notice notice-success inline"><p>Previsualización estricta superada.</p></div>';
        echo '<details><summary>Operaciones previstas</summary><pre>' . esc_html(implode("\n", $messages)) . '</pre></details>';
    } catch (\Throwable $e) {
        echo '<div class="notice notice-error inline"><p>' . esc_html($e->getMessage()) . '</p></div>';
    }
    $saved = get_option(BACKUP_OPTION);
    echo '<p>';
    form('backup', 'Crear copia', 'button button-secondary');
    if (is_array($saved)) {
        form('download', 'Descargar copia', 'button button-secondary');
        if ($preview_ok) {
            form('apply', empty($saved['after_hash']) ? 'Publicar entrega' : 'Comprobar entrega sin cambios', 'button button-primary');
        }
        if (!empty($saved['after_hash'])) {
            form('restore', 'Restaurar copia', 'button button-secondary');
        }
    }
    echo '</p></div>';
}
