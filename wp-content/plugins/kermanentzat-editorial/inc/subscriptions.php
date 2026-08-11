<?php

namespace Kermanentzat\Editorial;

defined('ABSPATH') || exit;

const META_NOTIFY = '_kerman_notify_subscribers';
const META_CAMPAIGN_STATE = '_kerman_campaign_state';
const META_CAMPAIGN_ID = '_kerman_campaign_id';
const META_CAMPAIGN_ATTEMPTS = '_kerman_campaign_attempts';
const META_CAMPAIGN_ERROR = '_kerman_campaign_error';
const META_CAMPAIGN_SENT_AT = '_kerman_campaign_sent_at';

function register_subscription_hooks(): void
{
    add_action('init', __NAMESPACE__ . '\register_subscription_meta', 25);
    add_shortcode('kermanentzat_subscription', __NAMESPACE__ . '\render_subscription_shortcode');
    add_action('transition_post_status', __NAMESPACE__ . '\queue_scheduled_publication', 20, 3);
    add_action(CRON_HOOK, __NAMESPACE__ . '\process_campaign', 10, 1);
    add_action('admin_post_kermanentzat_retry_campaign', __NAMESPACE__ . '\retry_campaign_action');
    add_filter('kermanentzat_optional_services', __NAMESPACE__ . '\register_sender_service');
}

function register_sender_service(array $services): array
{
    if (!subscription_is_configured()) {
        return $services;
    }
    $services[] = [
        'id' => 'sender_newsletter',
        'category' => 'marketing',
        'enabled' => true,
        'provider' => 'UAB Sender.lt',
        'purpose' => 'Gestionar altas confirmadas, bajas y avisos de nuevas publicaciones',
        'legal_basis' => 'consent',
        'trigger' => 'dedicated_subscription_page_load_and_confirmed_submit',
        'storage' => ['email en Sender', 'estado de confirmación, entrega y supresión en Sender'],
        'retention' => 'Hasta la baja; supresión mínima posterior según obligaciones y contrato validados',
        'withdrawal' => 'enlace de baja en cada mensaje o solicitud al responsable',
        'safeguards' => 'Activación condicionada a DPA, transferencias, DNS y revisión humana documentados',
        'script_origins' => ['https://cdn.sender.net'],
        'style_origins' => ['https://cdn.sender.net'],
        'img_origins' => ['https://cdn.sender.net'],
        'connect_origins' => ['https://cdn.sender.net', 'https://stats.sender.net', 'https://www.cloudflare.com'],
        'form_origins' => ['https://stats.sender.net'],
        'frame_origins' => ['https://stats.sender.net'],
    ];
    return $services;
}

function register_subscription_meta(): void
{
    $fields = [
        META_NOTIFY => ['type' => 'boolean', 'default' => false],
        META_CAMPAIGN_STATE => ['type' => 'string', 'default' => 'not_requested'],
        META_CAMPAIGN_ID => ['type' => 'string', 'default' => ''],
        META_CAMPAIGN_ATTEMPTS => ['type' => 'integer', 'default' => 0],
        META_CAMPAIGN_ERROR => ['type' => 'string', 'default' => ''],
        META_CAMPAIGN_SENT_AT => ['type' => 'string', 'default' => ''],
    ];
    foreach ($fields as $key => $args) {
        register_post_meta(UPDATE_POST_TYPE, $key, [
            'type' => $args['type'],
            'single' => true,
            'default' => $args['default'],
            'show_in_rest' => false,
            'sanitize_callback' => $args['type'] === 'boolean' ? 'rest_sanitize_boolean' : ($args['type'] === 'integer' ? 'absint' : 'sanitize_text_field'),
            'auth_callback' => static fn(bool $allowed, string $meta_key, int $post_id): bool => current_user_can('edit_post', $post_id),
        ]);
    }
}

function render_notification_controls(\WP_Post $post): void
{
    if ($post->post_type !== UPDATE_POST_TYPE) {
        return;
    }
    $identity = campaign_identity_post_id($post->ID);
    $state = (string) meta_value($identity, META_CAMPAIGN_STATE, 'not_requested');
    $campaign_id = (string) meta_value($identity, META_CAMPAIGN_ID);
    $sent_at = (string) meta_value($identity, META_CAMPAIGN_SENT_AT);
    $error = (string) meta_value($identity, META_CAMPAIGN_ERROR);
    ?>
    <hr>
    <p><strong><?php esc_html_e('Aviso por email', 'kermanentzat-editorial'); ?></strong></p>
    <p>
        <label>
            <input type="checkbox" name="<?php echo esc_attr(META_NOTIFY); ?>" value="1" <?php checked((bool) meta_value($post->ID, META_NOTIFY, false)); ?> <?php disabled($state === 'sent' || !subscription_is_configured()); ?>>
            <?php esc_html_e('Enviar aviso al publicar', 'kermanentzat-editorial'); ?>
        </label>
    </p>
    <p class="description"><?php echo esc_html(subscription_is_configured()
        ? __('Está desmarcado por defecto. Guardar o traducir no volverá a enviar.', 'kermanentzat-editorial')
        : __('Se habilitará cuando Sender esté aprobado y completamente configurado.', 'kermanentzat-editorial')); ?></p>
    <dl class="kerman-campaign-status">
        <dt><?php esc_html_e('Estado', 'kermanentzat-editorial'); ?></dt><dd><?php echo esc_html(campaign_state_label($state)); ?></dd>
        <?php if ($campaign_id !== '') : ?><dt>ID</dt><dd><code><?php echo esc_html($campaign_id); ?></code></dd><?php endif; ?>
        <?php if ($sent_at !== '') : ?><dt><?php esc_html_e('Enviado', 'kermanentzat-editorial'); ?></dt><dd><?php echo esc_html($sent_at); ?></dd><?php endif; ?>
        <?php if ($error !== '') : ?><dt><?php esc_html_e('Error', 'kermanentzat-editorial'); ?></dt><dd><?php echo esc_html($error); ?></dd><?php endif; ?>
    </dl>
    <?php if ($state === 'failed' && current_user_can('publish_kerman_contents')) : ?>
        <p><a class="button" href="<?php echo esc_url(wp_nonce_url(admin_url('admin-post.php?action=kermanentzat_retry_campaign&post_id=' . $identity), 'kermanentzat_retry_campaign_' . $identity)); ?>"><?php esc_html_e('Reintentar', 'kermanentzat-editorial'); ?></a></p>
    <?php endif; ?>
    <?php
}

function campaign_state_label(string $state): string
{
    $labels = [
        'not_requested' => __('No solicitado', 'kermanentzat-editorial'),
        'queued' => __('En cola', 'kermanentzat-editorial'),
        'sending' => __('Enviando', 'kermanentzat-editorial'),
        'sent' => __('Enviado', 'kermanentzat-editorial'),
        'failed' => __('Fallido', 'kermanentzat-editorial'),
        'cancelled' => __('Cancelado', 'kermanentzat-editorial'),
    ];
    return $labels[$state] ?? $labels['not_requested'];
}

function save_notification_request(int $post_id): void
{
    $identity = campaign_identity_post_id($post_id);
    if ((string) meta_value($identity, META_CAMPAIGN_STATE, 'not_requested') === 'sent') {
        return;
    }
    $requested = subscription_is_configured() && isset($_POST[META_NOTIFY]);
    update_post_meta($post_id, META_NOTIFY, $requested);
    if (!$requested) {
        if ((string) meta_value($identity, META_CAMPAIGN_STATE, 'not_requested') === 'queued') {
            update_post_meta($identity, META_CAMPAIGN_STATE, 'cancelled');
            wp_clear_scheduled_hook(CRON_HOOK, [$identity]);
            delete_option(campaign_lock_key($identity));
        }
        return;
    }
    if (get_post_status($post_id) === 'publish') {
        maybe_queue_campaign($post_id);
    }
}

function queue_scheduled_publication(string $new_status, string $old_status, \WP_Post $post): void
{
    if ($post->post_type !== UPDATE_POST_TYPE || $new_status !== 'publish' || $old_status === 'publish') {
        return;
    }
    if ((bool) meta_value($post->ID, META_NOTIFY, false)) {
        maybe_queue_campaign($post->ID);
    }
}

function campaign_identity_post_id(int $post_id): int
{
    $ids = [$post_id];
    if (function_exists('pll_get_post_translations')) {
        $translations = pll_get_post_translations($post_id);
        if (is_array($translations)) {
            $ids = array_merge($ids, array_map('absint', array_values($translations)));
        }
    }
    $group = (string) get_post_meta($post_id, '_kerman_translation_group', true);
    if ($group !== '') {
        $matches = get_posts([
            'post_type' => UPDATE_POST_TYPE,
            'post_status' => 'any',
            'numberposts' => -1,
            'fields' => 'ids',
            'meta_key' => '_kerman_translation_group',
            'meta_value' => $group,
        ]);
        $ids = array_merge($ids, array_map('absint', $matches));
    }
    $ids = array_values(array_filter(array_unique($ids)));
    return $ids === [] ? $post_id : min($ids);
}

function campaign_lock_key(int $identity): string
{
    return 'kermanentzat_campaign_lock_' . $identity;
}

function maybe_queue_campaign(int $post_id): bool
{
    $identity = campaign_identity_post_id($post_id);
    $state = (string) meta_value($identity, META_CAMPAIGN_STATE, 'not_requested');
    if (in_array($state, ['queued', 'sending', 'sent'], true)) {
        return false;
    }

    if ($state === 'cancelled') {
        delete_option(campaign_lock_key($identity));
    }

    if (!add_option(campaign_lock_key($identity), (string) time(), '', false)) {
        return false;
    }

    update_post_meta($identity, META_CAMPAIGN_STATE, 'queued');
    update_post_meta($identity, META_CAMPAIGN_ATTEMPTS, 0);
    delete_post_meta($identity, META_CAMPAIGN_ERROR);
    wp_schedule_single_event(time() + MINUTE_IN_SECONDS, CRON_HOOK, [$identity]);
    return true;
}

function retry_campaign_action(): void
{
    $post_id = absint($_GET['post_id'] ?? 0);
    if (!$post_id || get_post_type($post_id) !== UPDATE_POST_TYPE || !current_user_can('publish_kerman_contents')) {
        wp_die(esc_html__('No autorizado.', 'kermanentzat-editorial'), 403);
    }
    check_admin_referer('kermanentzat_retry_campaign_' . $post_id);
    update_post_meta($post_id, META_CAMPAIGN_STATE, 'queued');
    update_post_meta($post_id, META_CAMPAIGN_ATTEMPTS, 0);
    delete_post_meta($post_id, META_CAMPAIGN_ERROR);
    wp_clear_scheduled_hook(CRON_HOOK, [$post_id]);
    wp_schedule_single_event(time() + 5, CRON_HOOK, [$post_id]);
    wp_safe_redirect(get_edit_post_link($post_id, 'url'));
    exit;
}

function process_campaign(int $identity): void
{
    $identity = campaign_identity_post_id($identity);
    if ((string) meta_value($identity, META_CAMPAIGN_STATE, '') !== 'queued') {
        return;
    }
    if (!subscription_is_configured()) {
        fail_campaign($identity, __('Sender no está aprobado o configurado.', 'kermanentzat-editorial'), false);
        return;
    }

    update_post_meta($identity, META_CAMPAIGN_STATE, 'sending');
    $campaign_id = (string) meta_value($identity, META_CAMPAIGN_ID);
    if ($campaign_id === '') {
        $created = sender_create_campaign($identity);
        if (is_wp_error($created)) {
            fail_campaign($identity, $created->get_error_message(), true);
            return;
        }
        $campaign_id = $created;
        update_post_meta($identity, META_CAMPAIGN_ID, $campaign_id);
    }

    $sent = sender_request('/campaigns/' . rawurlencode($campaign_id) . '/send', 'POST');
    if (is_wp_error($sent)) {
        fail_campaign($identity, $sent->get_error_message(), true);
        return;
    }

    update_post_meta($identity, META_CAMPAIGN_STATE, 'sent');
    update_post_meta($identity, META_CAMPAIGN_SENT_AT, current_time('mysql', true));
    delete_post_meta($identity, META_CAMPAIGN_ERROR);
}

function fail_campaign(int $identity, string $message, bool $retry): void
{
    $attempts = absint(meta_value($identity, META_CAMPAIGN_ATTEMPTS, 0)) + 1;
    update_post_meta($identity, META_CAMPAIGN_ATTEMPTS, $attempts);
    update_post_meta($identity, META_CAMPAIGN_ERROR, sanitize_text_field(wp_strip_all_tags($message)));
    if ($retry && $attempts < 3) {
        update_post_meta($identity, META_CAMPAIGN_STATE, 'queued');
        $delays = [5 * MINUTE_IN_SECONDS, 15 * MINUTE_IN_SECONDS, HOUR_IN_SECONDS];
        wp_schedule_single_event(time() + $delays[$attempts - 1], CRON_HOOK, [$identity]);
        return;
    }
    update_post_meta($identity, META_CAMPAIGN_STATE, 'failed');
}

function sender_create_campaign(int $identity)
{
    $posts = campaign_posts($identity);
    if ($posts === []) {
        return new \WP_Error('kermanentzat_campaign_empty', __('No hay contenido publicado para la campaña.', 'kermanentzat-editorial'));
    }
    $config = settings();
    $primary = $posts[0];
    $language = editorial_language_for_post($primary->ID);
    $subject = count($posts) > 1
        ? 'Egia Kermanentzat · ' . get_the_title($primary)
        : get_the_title($primary);
    $environment = wp_get_environment_type();
    if ($environment !== 'production') {
        $subject = '[' . strtoupper($environment) . '] ' . $subject;
    }
    $payload = [
        'title' => 'WordPress #' . $identity . ' · ' . $subject,
        'subject' => $subject,
        'from' => (string) $config['sender_from_name'],
        'reply_to' => (string) $config['sender_reply_to'],
        'preheader' => $language === 'eu' ? 'Egia Kermanentzaten argitalpen berria' : 'Nueva publicación de Egia Kermanentzat',
        'content_type' => 'html',
        'google_analytics' => 0,
        'auto_followup_active' => false,
        'groups' => [(string) $config['sender_group_id']],
        'segments' => [],
        'content' => campaign_html($posts),
    ];
    $response = sender_request('/campaigns', 'POST', $payload);
    if (is_wp_error($response)) {
        return $response;
    }
    $campaign_id = sanitize_text_field((string) ($response['data']['id'] ?? ''));
    return $campaign_id !== '' ? $campaign_id : new \WP_Error('kermanentzat_sender_response', __('Sender no devolvió el identificador de campaña.', 'kermanentzat-editorial'));
}

function campaign_posts(int $identity): array
{
    $posts = [];
    $primary = get_post($identity);
    if ($primary instanceof \WP_Post && $primary->post_type === UPDATE_POST_TYPE && $primary->post_status === 'publish') {
        $posts[$primary->ID] = $primary;
    }
    if (function_exists('pll_get_post_translations')) {
        foreach ((array) pll_get_post_translations($identity) as $post_id) {
            $post = get_post(absint($post_id));
            if ($post instanceof \WP_Post && $post->post_type === UPDATE_POST_TYPE && $post->post_status === 'publish') {
                $posts[$post->ID] = $post;
            }
        }
    }
    $group = (string) get_post_meta($identity, '_kerman_translation_group', true);
    if ($group !== '') {
        foreach (get_posts([
            'post_type' => UPDATE_POST_TYPE,
            'post_status' => 'publish',
            'numberposts' => -1,
            'meta_key' => '_kerman_translation_group',
            'meta_value' => $group,
        ]) as $post) {
            $posts[$post->ID] = $post;
        }
    }
    uasort($posts, static fn(\WP_Post $a, \WP_Post $b): int => strcmp(editorial_language_for_post($a->ID), editorial_language_for_post($b->ID)));
    return array_values($posts);
}

function campaign_html(array $posts): string
{
    $environment = wp_get_environment_type();
    $environment_banner = $environment !== 'production'
        ? sprintf(
            '<p style="margin:0 0 24px;padding:12px;background:#ffeb3b;color:#090909;font:700 14px Arial,sans-serif;text-align:center">[%s] ENTORNO DE PRUEBAS · PROBA INGURUNEA</p>',
            esc_html(strtoupper($environment))
        )
        : '';
    $sections = '';
    foreach ($posts as $post) {
        $language = editorial_language_for_post($post->ID);
        $excerpt = has_excerpt($post) ? get_the_excerpt($post) : wp_trim_words(wp_strip_all_tags($post->post_content), 38);
        $type = update_type_label(update_type_for_post($post->ID), $language);
        $button = $language === 'eu' ? 'Irakurri argitalpena' : 'Leer la publicación';
        $sections .= sprintf(
            '<section style="padding:24px 0;border-bottom:1px solid #d8d8d8"><p style="margin:0 0 8px;font:700 12px Arial,sans-serif;letter-spacing:.08em;text-transform:uppercase">%s · %s</p><h1 style="margin:0 0 12px;font:700 28px Arial,sans-serif;line-height:1.15">%s</h1><p style="margin:0 0 20px;font:16px Arial,sans-serif;line-height:1.55">%s</p><p><a href="%s" style="display:inline-block;padding:12px 18px;background:#090909;color:#fff;text-decoration:none;font:700 15px Arial,sans-serif">%s</a></p></section>',
            esc_html(strtoupper($language)),
            esc_html($type),
            esc_html(get_the_title($post)),
            esc_html($excerpt),
            esc_url(get_permalink($post)),
            esc_html($button)
        );
    }
    return '<!doctype html><html><body style="margin:0;background:#f3f1eb;color:#090909"><div style="display:none;max-height:0;overflow:hidden">Egia Kermanentzat</div><main style="max-width:640px;margin:0 auto;padding:32px 24px;background:#fff">' . $environment_banner . '<p style="font:900 20px Arial,sans-serif">EGIA KERMANENTZAT</p>' . $sections . '<footer style="padding-top:24px;font:13px Arial,sans-serif;line-height:1.5"><p>Egia Kermanentzat Elkartea</p><p><a href="{{unsubscribe_link}}">{{unsubscribe_text}}</a></p><p>{{ account.address }}, {{ account.city }}, {{ account.country }}</p></footer></main></body></html>';
}

function sender_request(string $path, string $method = 'GET', ?array $payload = null)
{
    if (!subscription_is_configured()) {
        return new \WP_Error('kermanentzat_sender_disabled', __('Sender no está configurado.', 'kermanentzat-editorial'));
    }
    $args = [
        'method' => $method,
        'timeout' => 20,
        'redirection' => 0,
        'headers' => [
            'Authorization' => 'Bearer ' . sender_api_token(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ],
    ];
    if ($payload !== null) {
        $args['body'] = wp_json_encode($payload);
    }
    $response = wp_remote_request('https://api.sender.net/v2' . $path, $args);
    if (is_wp_error($response)) {
        return new \WP_Error('kermanentzat_sender_http', __('No se pudo contactar con Sender.', 'kermanentzat-editorial'));
    }
    $status = (int) wp_remote_retrieve_response_code($response);
    $body = json_decode((string) wp_remote_retrieve_body($response), true);
    if ($status < 200 || $status >= 300 || !is_array($body)) {
        return new \WP_Error('kermanentzat_sender_api', sprintf(__('Sender respondió con estado %d.', 'kermanentzat-editorial'), $status));
    }
    return $body;
}

function subscription_form_url(): string
{
    $config = settings();
    return esc_url_raw((string) $config['sender_form_url']);
}

function render_subscription_shortcode($attributes): string
{
    $language = current_editorial_language();
    $config = settings();
    $url = subscription_form_url();
    $account_id = sanitize_text_field((string) $config['sender_account_public_id']);
    $form_id = sanitize_text_field((string) $config['sender_form_id']);
    $form_embed_id = sanitize_text_field((string) $config['sender_form_embed_id']);
    $is_subscription_page = is_page(['harpidetza', 'suscripcion']);
    $is_updates_page = is_page(['berriak', 'actualidad']);
    if (!subscription_is_configured() || $url === '') {
        $title = $language === 'eu' ? 'Jaso berriak posta elektronikoz' : 'Recibe las novedades por email';
        $description = $language === 'eu'
            ? 'Harpidetza prestatzen ari gara. Oraindik ez dugu helbide elektronikorik jasotzen; zerbitzua aktibatzen denean, formularioa orri honetan bertan agertuko da.'
            : 'Estamos preparando la suscripción. Todavía no recogemos direcciones de email; cuando el servicio esté activo, el formulario aparecerá en esta misma página.';
        $updates_url = $language === 'eu' ? home_url('/berriak/') : home_url('/es/actualidad/');
        $updates_label = $language === 'eu' ? 'Ikusi bitartean berriak' : 'Consulta mientras tanto la actualidad';
        ob_start();
        ?>
        <section class="kerman-subscription kerman-subscription--pending">
            <div>
                <?php if (!$is_subscription_page) : ?>
                    <h2><?php echo esc_html($title); ?></h2>
                <?php endif; ?>
                <p><?php echo esc_html($description); ?></p>
            </div>
            <?php if (!$is_updates_page) : ?>
                <div>
                    <p><a class="button button--inverse" href="<?php echo esc_url($updates_url); ?>"><?php echo esc_html($updates_label); ?></a></p>
                </div>
            <?php endif; ?>
        </section>
        <?php
        return (string) ob_get_clean();
    }

    $title = $language === 'eu' ? 'Jaso berriak posta elektronikoz' : 'Recibe las novedades por email';
    $subscription_page_url = $language === 'eu' ? home_url('/harpidetza/') : home_url('/es/suscripcion/');
    if (!$is_subscription_page) {
        $teaser = $language === 'eu'
            ? 'Harpidetu argitalpen berrien abisuak jasotzeko. Ez dugu publizitaterik bidaltzen.'
            : 'Suscríbete para recibir avisos de nuevas publicaciones. No enviamos publicidad.';
        $teaser_button = $language === 'eu' ? 'Harpidetu' : 'Suscribirme';
        ob_start();
        ?>
        <section class="kerman-subscription kerman-subscription--teaser">
            <div>
                <h2><?php echo esc_html($title); ?></h2>
                <p><?php echo esc_html($teaser); ?></p>
            </div>
            <a class="button button--primary" href="<?php echo esc_url($subscription_page_url); ?>"><?php echo esc_html($teaser_button); ?></a>
        </section>
        <?php
        return (string) ob_get_clean();
    }

    wp_enqueue_script('kermanentzat-subscription', PLUGIN_URL . 'assets/subscription.js', [], VERSION, true);
    $description = $language === 'eu'
        ? 'Bi urrats besterik ez: bete inprimakia eta berretsi alta posta elektronikoz. Edonoiz eman dezakezu baja.'
        : 'Solo son dos pasos: completa el formulario y confirma el alta por email. Podrás darte de baja en cualquier momento.';
    $retry = $language === 'eu' ? 'Saiatu berriro' : 'Volver a intentarlo';
    $loading = $language === 'eu' ? 'Harpidetza-inprimakia kargatzen…' : 'Cargando el formulario de suscripción…';
    $error = $language === 'eu'
        ? 'Ezin izan dugu inprimakia kargatu. Ireki aparteko orrian edo saiatu berriro.'
        : 'No hemos podido cargar el formulario. Ábrelo en una página independiente o vuelve a intentarlo.';
    $fallback = $language === 'eu' ? 'Ireki aparteko orri batean' : 'Abrir en una página independiente';
    $privacy = $language === 'eu' ? home_url('/pribatutasun-politika/') : home_url('/es/politica-de-privacidad/');
    ob_start();
    ?>
    <section class="kerman-subscription kerman-subscription--page" data-kerman-subscription data-auto-load data-account-id="<?php echo esc_attr($account_id); ?>" data-form-id="<?php echo esc_attr($form_id); ?>">
        <div class="kerman-subscription__action">
            <button class="button button--primary kerman-subscription__retry" type="button" data-open-subscription hidden><?php echo esc_html($retry); ?></button>
            <p class="kerman-subscription__fallback"><a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($fallback); ?></a></p>
            <p class="kerman-subscription__status" data-subscription-status data-loading-text="<?php echo esc_attr($loading); ?>" data-error-text="<?php echo esc_attr($error); ?>" role="status" aria-live="polite"></p>
            <div class="kerman-subscription__form" data-subscription-form-container hidden tabindex="-1">
                <div class="sender-form-field" data-sender-form-id="<?php echo esc_attr($form_embed_id); ?>"></div>
            </div>
            <noscript><p class="kerman-subscription__noscript"><a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($fallback); ?></a></p></noscript>
        </div>
        <div class="kerman-subscription__intro">
            <p><?php echo esc_html($description); ?></p>
            <p><a href="<?php echo esc_url($privacy); ?>"><?php echo esc_html($language === 'eu' ? 'Irakurri pribatutasun-informazioa' : 'Leer la información de privacidad'); ?></a></p>
        </div>
    </section>
    <?php
    return (string) ob_get_clean();
}

function render_capacity_notice(): void
{
    if (!subscription_is_approved()) {
        return;
    }
    $config = settings();
    $subscriber_ratio = (int) $config['sender_subscriber_count'] / max(1, (int) $config['sender_subscriber_limit']);
    $monthly_ratio = (int) $config['sender_monthly_projection'] / max(1, (int) $config['sender_monthly_limit']);
    if (max($subscriber_ratio, $monthly_ratio) < 0.8) {
        return;
    }
    echo '<div class="notice notice-warning"><p>';
    echo esc_html__('Sender ha alcanzado al menos el 80 % de un límite configurado. Revisa la capacidad antes del próximo aviso.', 'kermanentzat-editorial');
    echo '</p></div>';
}
