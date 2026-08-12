<?php
/**
 * Plugin Name: Kermanentzat Editorial
 * Description: Herramientas editoriales autoadministrables para Egia Kermanentzat.
 * Version: 0.2.6
 * Requires at least: 6.7
 * Requires PHP: 8.2
 * Author: Egia Kermanentzat Elkartea
 * Text Domain: kermanentzat-editorial
 */

namespace Kermanentzat\Editorial;

defined('ABSPATH') || exit;

const VERSION = '0.2.6';
const SCHEMA_VERSION = '1';
const OPTION_SCHEMA_VERSION = 'kermanentzat_editorial_schema_version';
const OPTION_SETTINGS = 'kermanentzat_editorial_settings';
const CRON_HOOK = 'kermanentzat_editorial_process_campaign';

define(__NAMESPACE__ . '\PLUGIN_FILE', __FILE__);
define(__NAMESPACE__ . '\PLUGIN_DIR', plugin_dir_path(__FILE__));
define(__NAMESPACE__ . '\PLUGIN_URL', plugin_dir_url(__FILE__));

require_once PLUGIN_DIR . 'inc/content.php';
require_once PLUGIN_DIR . 'inc/admin.php';
require_once PLUGIN_DIR . 'inc/render.php';
require_once PLUGIN_DIR . 'inc/subscriptions.php';
require_once PLUGIN_DIR . 'inc/cli.php';

function boot(): void
{
    register_content_hooks();
    register_admin_hooks();
    register_render_hooks();
    register_subscription_hooks();
    register_cli_commands();
}
add_action('plugins_loaded', __NAMESPACE__ . '\boot');

function activate(): void
{
    register_content_types();
    register_editorial_role();
    seed_update_types();
    update_option(OPTION_SCHEMA_VERSION, SCHEMA_VERSION, false);
    flush_rewrite_rules(false);
}
register_activation_hook(__FILE__, __NAMESPACE__ . '\activate');

function deactivate(): void
{
    wp_clear_scheduled_hook(CRON_HOOK);
    flush_rewrite_rules(false);
}
register_deactivation_hook(__FILE__, __NAMESPACE__ . '\deactivate');

function is_truthy_constant(string $name): bool
{
    if (defined($name)) {
        $value = constant($name);
        return is_scalar($value) && filter_var((string) $value, FILTER_VALIDATE_BOOL);
    }

    $value = getenv($name);
    return $value !== false && filter_var($value, FILTER_VALIDATE_BOOL);
}

function settings(): array
{
    $defaults = [
        'sender_group_id' => 'bq589r',
        'sender_account_public_id' => 'acc9a2abda1518',
        'sender_form_id' => 'epY1RX',
        'sender_form_embed_id' => 'msis6hs8epy1rx9k77d',
        'sender_form_url' => 'https://stats.sender.net/forms/epY1RX/view',
        'sender_form_id_eu' => '',
        'sender_form_id_es' => '',
        'sender_form_url_eu' => '',
        'sender_form_url_es' => '',
        'sender_from_name' => 'Egia Kermanentzat Elkartea',
        'sender_reply_to' => 'info@egiakermanentzat.eus',
        'sender_subscriber_limit' => 2500,
        'sender_monthly_limit' => 15000,
        'sender_subscriber_count' => 0,
        'sender_monthly_projection' => 0,
    ];

    $stored = get_option(OPTION_SETTINGS, []);
    $stored = is_array($stored) ? $stored : [];
    $config = array_merge($defaults, $stored);

    // One bilingual Sender form replaced the original per-language form plan.
    // Keep legacy values as a migration fallback for installations that saved
    // the earlier settings before the singular fields existed.
    if (trim((string) ($stored['sender_form_url'] ?? '')) === '') {
        $legacy_url = trim((string) ($stored['sender_form_url_eu'] ?? $stored['sender_form_url_es'] ?? ''));
        if ($legacy_url !== '') {
            $config['sender_form_url'] = $legacy_url;
        }
    }

    // Sender exposes a public form ID to senderForms.render() and a separate
    // embed hash for the data-sender-form-id container. Migrate the first
    // integration draft, which stored the embed hash in the render field.
    if (
        trim((string) ($stored['sender_form_embed_id'] ?? '')) === ''
        && preg_match('/^msis[a-z0-9]+$/i', (string) ($stored['sender_form_id'] ?? '')) === 1
    ) {
        $config['sender_form_embed_id'] = (string) $stored['sender_form_id'];
        $path = trim((string) wp_parse_url((string) $config['sender_form_url'], PHP_URL_PATH), '/');
        $parts = explode('/', $path);
        $forms_index = array_search('forms', $parts, true);
        if ($forms_index !== false && isset($parts[$forms_index + 1])) {
            $config['sender_form_id'] = sanitize_text_field($parts[$forms_index + 1]);
        }
    }

    return $config;
}

function subscription_is_approved(): bool
{
    return is_truthy_constant('KERMANENTZAT_SENDER_APPROVED');
}

function sender_api_token(): string
{
    if (defined('KERMANENTZAT_SENDER_API_TOKEN')) {
        $value = constant('KERMANENTZAT_SENDER_API_TOKEN');
        if (is_scalar($value) && trim((string) $value) !== '') {
            return trim((string) $value);
        }
    }

    foreach (['KERMANENTZAT_SENDER_API_TOKEN', 'SENDER_API_TOK'] as $name) {
        $value = getenv($name);
        if ($value !== false && trim($value) !== '') {
            return trim($value);
        }
    }

    return '';
}

function subscription_is_configured(): bool
{
    $config = settings();
    $form_url = (string) $config['sender_form_url'];
    $form_host = strtolower((string) wp_parse_url($form_url, PHP_URL_HOST));
    return subscription_is_approved()
        && sender_api_token() !== ''
        && trim((string) $config['sender_group_id']) !== ''
        && preg_match('/^acc[a-z0-9]{8,}$/i', (string) $config['sender_account_public_id']) === 1
        && preg_match('/^[a-z0-9]{6,}$/i', (string) $config['sender_form_id']) === 1
        && preg_match('/^[a-z0-9]{8,}$/i', (string) $config['sender_form_embed_id']) === 1
        && is_email((string) $config['sender_reply_to'])
        && filter_var($form_url, FILTER_VALIDATE_URL) !== false
        && strtolower((string) wp_parse_url($form_url, PHP_URL_SCHEME)) === 'https'
        && in_array($form_host, ['stats.sender.net', 'newsletter.egiakermanentzat.eus'], true);
}
