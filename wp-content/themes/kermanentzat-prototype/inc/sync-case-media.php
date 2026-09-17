<?php

defined('ABSPATH') || exit;

require_once get_theme_file_path('inc/case-media-content.php');
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

function kermanentzat_case_sync_log(string $message): void
{
    if (class_exists('WP_CLI')) {
        WP_CLI::log($message);
    }
}

function kermanentzat_case_sync_error(string $message): void
{
    if (class_exists('WP_CLI')) {
        WP_CLI::error($message);
    }
    throw new RuntimeException($message);
}

function kermanentzat_case_sync_source_dir(?string $source = null): string
{
    $source = $source ?: (getenv('KERMANENTZAT_CASE_MEDIA_SOURCE_DIR') ?: '/case-media');
    $real = realpath($source);
    if ($real === false || !is_dir($real)) {
        kermanentzat_case_sync_error('No existe la carpeta fuente de medios: ' . $source);
    }
    return $real;
}

function kermanentzat_case_sync_upload_dir(): array
{
    $uploads = wp_upload_dir();
    if (!empty($uploads['error'])) {
        kermanentzat_case_sync_error((string) $uploads['error']);
    }

    $basedir = trailingslashit((string) $uploads['basedir']) . 'kermanentzat-case-media';
    $baseurl = trailingslashit((string) $uploads['baseurl']) . 'kermanentzat-case-media';
    if (!wp_mkdir_p($basedir)) {
        kermanentzat_case_sync_error('No se pudo crear la carpeta de subida: ' . $basedir);
    }

    return ['dir' => $basedir, 'url' => $baseurl];
}

function kermanentzat_case_sync_sha256(string $path): string
{
    $hash = hash_file('sha256', $path);
    if ($hash === false) {
        kermanentzat_case_sync_error('No se pudo calcular SHA-256 de ' . $path);
    }
    return $hash;
}

function kermanentzat_case_sync_attachment_for_key(string $key): ?WP_Post
{
    $posts = get_posts([
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'numberposts' => 1,
        'meta_key' => '_kermanentzat_case_media_key',
        'meta_value' => $key,
    ]);

    return $posts[0] ?? null;
}

function kermanentzat_case_sync_import_attachment(string $key, array $asset, string $source_dir, string $target_dir): int
{
    $source = $source_dir . DIRECTORY_SEPARATOR . $asset['source_file'];
    if (!is_file($source)) {
        kermanentzat_case_sync_error('Falta el archivo fuente: ' . $source);
    }

    $target = $target_dir . DIRECTORY_SEPARATOR . $asset['public_file'];
    $hash = kermanentzat_case_sync_sha256($source);

    if (!is_file($target) || kermanentzat_case_sync_sha256($target) !== $hash) {
        if (!copy($source, $target)) {
            kermanentzat_case_sync_error('No se pudo copiar ' . $source . ' a ' . $target);
        }
    }

    $attachment = kermanentzat_case_sync_attachment_for_key($key);
    $filetype = wp_check_filetype($target);
    $relative = 'kermanentzat-case-media/' . basename($target);
    $title = is_array($asset['title'] ?? null) ? $asset['title']['es'] : basename($target);
    $postarr = [
        'post_mime_type' => $filetype['type'] ?: 'application/octet-stream',
        'post_title' => $title,
        'post_content' => '',
        'post_status' => 'inherit',
    ];

    if ($attachment instanceof WP_Post) {
        $attachment_id = (int) $attachment->ID;
        $postarr['ID'] = $attachment_id;
        wp_update_post(wp_slash($postarr), true);
        update_attached_file($attachment_id, $relative);
    } else {
        $attachment_id = wp_insert_attachment(wp_slash($postarr), $relative);
        if (is_wp_error($attachment_id)) {
            kermanentzat_case_sync_error($attachment_id->get_error_message());
        }
    }

    update_post_meta($attachment_id, '_kermanentzat_case_media_key', $key);
    update_post_meta($attachment_id, '_kermanentzat_case_media_sha256', $hash);
    update_post_meta($attachment_id, '_kermanentzat_case_media_original_name', $asset['source_file']);
    wp_update_attachment_metadata($attachment_id, wp_generate_attachment_metadata($attachment_id, $target));
    kermanentzat_case_sync_log('Medio listo: ' . $asset['public_file'] . ' (#' . $attachment_id . ')');

    return (int) $attachment_id;
}

function kermanentzat_case_sync_format_timestamp(float $seconds): string
{
    $milliseconds = (int) round(($seconds - floor($seconds)) * 1000);
    $total = (int) floor($seconds);
    $hours = intdiv($total, 3600);
    $minutes = intdiv($total % 3600, 60);
    $secs = $total % 60;

    return sprintf('%02d:%02d:%02d.%03d', $hours, $minutes, $secs, $milliseconds);
}

function kermanentzat_case_sync_write_tracks(array $assets, string $target_dir): void
{
    foreach ($assets as $key => $asset) {
        foreach (($asset['tracks'] ?? []) as $language => $segments) {
            if ($segments === []) {
                continue;
            }

            $lines = ['WEBVTT', ''];
            foreach ($segments as $index => $segment) {
                $lines[] = (string) ($index + 1);
                $lines[] = kermanentzat_case_sync_format_timestamp((float) $segment[0]) . ' --> ' . kermanentzat_case_sync_format_timestamp((float) $segment[1]);
                $lines[] = (string) $segment[2];
                $lines[] = '';
            }

            $filename = kermanentzat_case_media_track_filename($key, $language);
            file_put_contents($target_dir . DIRECTORY_SEPARATOR . $filename, implode("\n", $lines));
            kermanentzat_case_sync_log('Subtitulos listos: ' . $filename);
        }
    }
}

function kermanentzat_case_sync_copy_posters(array $assets, string $source_dir, string $target_dir): void
{
    foreach ($assets as $asset) {
        if (empty($asset['poster_file'])) {
            continue;
        }
        $source = $source_dir . DIRECTORY_SEPARATOR . 'posters' . DIRECTORY_SEPARATOR . $asset['poster_file'];
        $target = $target_dir . DIRECTORY_SEPARATOR . $asset['poster_file'];
        if (!is_file($source)) {
            kermanentzat_case_sync_error('Falta la portada generada: ' . $source);
        }
        if (!is_file($target) || kermanentzat_case_sync_sha256($target) !== kermanentzat_case_sync_sha256($source)) {
            if (!copy($source, $target)) {
                kermanentzat_case_sync_error('No se pudo copiar la portada: ' . $source);
            }
        }
        kermanentzat_case_sync_log('Portada lista: ' . $asset['poster_file']);
    }
}

function kermanentzat_case_sync_backup_page(WP_Post $page, string $backup_dir): string
{
    if (!wp_mkdir_p($backup_dir)) {
        kermanentzat_case_sync_error('No se pudo crear la carpeta de backups: ' . $backup_dir);
    }
    file_put_contents($backup_dir . '/.htaccess', "Require all denied\n");

    $timestamp = gmdate('Ymd\THis\Z');
    $path = $backup_dir . DIRECTORY_SEPARATOR . $timestamp . '-' . $page->post_name . '-' . $page->ID . '.json';
    $payload = [
        'id' => $page->ID,
        'post_name' => $page->post_name,
        'post_title' => $page->post_title,
        'post_content' => $page->post_content,
        'post_excerpt' => $page->post_excerpt,
        'post_status' => $page->post_status,
        'post_modified_gmt' => $page->post_modified_gmt,
        'meta' => get_post_meta($page->ID),
    ];

    file_put_contents($path, wp_json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    update_post_meta($page->ID, '_kermanentzat_case_media_last_backup', $path);

    return $path;
}

function kermanentzat_case_sync_page_by_path(string $path): WP_Post
{
    $page = get_page_by_path($path);
    if (!$page instanceof WP_Post) {
        kermanentzat_case_sync_error('No existe la pagina: ' . $path);
    }
    return $page;
}

function kermanentzat_case_sync_update_pages(string $backup_dir): void
{
    $caseArt = '<div class="page-hero__art" aria-hidden="true"><picture><source srcset="' . esc_url(wp_make_link_relative(get_theme_file_uri('assets/images/kerman-portrait-clean.webp'))) . '" type="image/webp"><img src="' . esc_url(wp_make_link_relative(get_theme_file_uri('assets/images/kerman-portrait-clean.png'))) . '" width="717" height="762" fetchpriority="high" loading="eager" decoding="async" alt=""></picture></div>';
    $pages = [
        'es/resumen-del-caso' => kermanentzat_case_summary_content('es', $caseArt),
        'kasuaren-laburpena' => kermanentzat_case_summary_content('eu', $caseArt),
    ];

      foreach ($pages as $path => $html) {
          $page = kermanentzat_case_sync_page_by_path($path);
          if (str_contains($page->post_content, 'kermanentzat_timeline')) {
              $html = preg_replace('/<section\b[^>]*>(?:(?!<section\b).)*case-timeline(?:(?!<section\b).)*?<\/section>/s', '[kermanentzat_timeline featured="true"]', $html);
          }
        $backup = kermanentzat_case_sync_backup_page($page, $backup_dir);
        $content = "<!-- wp:html -->\n{$html}\n<!-- /wp:html -->";
        $updated = wp_update_post(wp_slash([
            'ID' => $page->ID,
            'post_content' => $content,
        ]), true);
        if (is_wp_error($updated)) {
            kermanentzat_case_sync_error($updated->get_error_message());
        }
        kermanentzat_case_sync_log('Pagina actualizada: ' . $path . ' (backup: ' . $backup . ')');
    }
}

function kermanentzat_case_media_sync_run(?string $source = null): void
{
    $source_dir = kermanentzat_case_sync_source_dir($source);
    $upload = kermanentzat_case_sync_upload_dir();
    $assets = kermanentzat_case_media_assets();

    foreach ($assets as $key => $asset) {
        if (!isset($asset['public_file'], $asset['source_file'])) {
            continue;
        }
        if ($key === 'informe' || str_ends_with((string) $asset['public_file'], '.mp4')) {
            kermanentzat_case_sync_import_attachment($key, $asset, $source_dir, $upload['dir']);
        }
    }

    kermanentzat_case_sync_copy_posters($assets, $source_dir, $upload['dir']);
    kermanentzat_case_sync_write_tracks($assets, $upload['dir']);
    kermanentzat_case_sync_update_pages($upload['dir'] . '-backups');
    update_option('kermanentzat_case_media_sync_version', gmdate('c'));
}

if (class_exists('WP_CLI') && defined('WP_CLI') && WP_CLI && !defined('KERMANENTZAT_CASE_MEDIA_DEFER')) {
    kermanentzat_case_media_sync_run();
    WP_CLI::success('Resumen del caso actualizado con videos e informe.');
}
