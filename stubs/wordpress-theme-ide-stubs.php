<?php
/**
 * IDE-only WordPress stubs for this repo.
 *
 * This project tracks `wp-content` but not the full WordPress core, so
 * language servers such as Intelephense can flag core functions as undefined.
 * This file is never loaded by WordPress; it only gives the editor enough
 * symbols to analyze the theme cleanly.
 */

if (!class_exists('WP_Theme')) {
    class WP_Theme
    {
        public function get(string $header): string
        {
            return '';
        }
    }
}

if (!function_exists('add_action')) {
    function add_action(string $hook_name, callable $callback, int $priority = 10, int $accepted_args = 1): bool
    {
        return true;
    }
}

if (!function_exists('add_editor_style')) {
    function add_editor_style($stylesheet = 'editor-style.css'): void
    {
    }
}

if (!function_exists('add_filter')) {
    function add_filter(string $hook_name, callable $callback, int $priority = 10, int $accepted_args = 1): bool
    {
        return true;
    }
}

if (!function_exists('add_theme_support')) {
    function add_theme_support(string $feature, ...$args): void
    {
    }
}

if (!function_exists('bloginfo')) {
    function bloginfo(string $show = ''): void
    {
    }
}

if (!function_exists('body_class')) {
    function body_class($css_class = ''): void
    {
    }
}

if (!function_exists('esc_attr')) {
    function esc_attr($text = ''): string
    {
        return (string) $text;
    }
}

if (!function_exists('esc_html')) {
    function esc_html($text = ''): string
    {
        return (string) $text;
    }
}

if (!function_exists('esc_url')) {
    function esc_url($url = '', $protocols = null, $_context = 'display'): string
    {
        return (string) $url;
    }
}

if (!function_exists('get_post_field')) {
    function get_post_field(string $field, int $post_id = 0, string $context = 'display')
    {
        return '';
    }
}

if (!function_exists('get_queried_object_id')) {
    function get_queried_object_id(): int
    {
        return 0;
    }
}

if (!function_exists('get_theme_file_uri')) {
    function get_theme_file_uri(string $file = ''): string
    {
        return $file;
    }
}

if (!function_exists('get_theme_file_path')) {
    function get_theme_file_path(string $file = ''): string
    {
        return $file;
    }
}

if (!function_exists('home_url')) {
    function home_url(string $path = '', $scheme = null): string
    {
        return $path;
    }
}

if (!function_exists('is_admin')) {
    function is_admin(): bool
    {
        return false;
    }
}

if (!function_exists('is_front_page')) {
    function is_front_page(): bool
    {
        return false;
    }
}

if (!function_exists('is_page')) {
    function is_page($page = ''): bool
    {
        return false;
    }
}

if (!function_exists('remove_action')) {
    function remove_action(string $hook_name, $callback, int $priority = 10): bool
    {
        return true;
    }
}

if (!function_exists('remove_query_arg')) {
    function remove_query_arg($key, $query = false): string
    {
        return '';
    }
}

if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool
    {
        return $needle === '' || strpos($haystack, $needle) === 0;
    }
}

if (!function_exists('wp_body_open')) {
    function wp_body_open(): void
    {
    }
}

if (!function_exists('wp_enqueue_script')) {
    function wp_enqueue_script(string $handle, string $src = '', array $deps = [], $ver = false, $args = []): void
    {
    }
}

if (!function_exists('wp_enqueue_style')) {
    function wp_enqueue_style(string $handle, string $src = '', array $deps = [], $ver = false, string $media = 'all'): void
    {
    }
}

if (!function_exists('wp_footer')) {
    function wp_footer(): void
    {
    }
}

if (!function_exists('wp_get_theme')) {
    function wp_get_theme($stylesheet = null, $theme_root = null): WP_Theme
    {
        return new WP_Theme();
    }
}

if (!function_exists('wp_head')) {
    function wp_head(): void
    {
    }
}

if (!function_exists('wp_parse_url')) {
    function wp_parse_url(string $url, int $component = -1)
    {
        return parse_url($url, $component);
    }
}

if (!function_exists('wp_safe_redirect')) {
    function wp_safe_redirect(string $location, int $status = 302, string $x_redirect_by = 'WordPress'): bool
    {
        return true;
    }
}

if (!function_exists('wp_script_add_data')) {
    function wp_script_add_data(string $handle, string $key, $value): bool
    {
        return true;
    }
}
