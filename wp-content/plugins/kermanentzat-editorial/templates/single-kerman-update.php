<?php

defined('ABSPATH') || exit;

get_header();
while (have_posts()) {
    the_post();
    $post_id = get_the_ID();
    $language = \Kermanentzat\Editorial\editorial_language_for_post($post_id);
    $type = \Kermanentzat\Editorial\update_type_for_post($post_id);
    $date = (string) \Kermanentzat\Editorial\meta_value($post_id, '_kerman_editorial_date', get_the_date('Y-m-d'));
    ?>
    <article class="content-band content-band--light kerman-single-update">
        <div class="content-wrap reading-copy">
            <p class="content-label"><?php echo esc_html(\Kermanentzat\Editorial\update_type_label($type, $language)); ?></p>
            <h1><?php the_title(); ?></h1>
            <time datetime="<?php echo esc_attr($date); ?>"><?php echo esc_html(\Kermanentzat\Editorial\format_editorial_date($date, $language)); ?></time>
            <?php if (has_post_thumbnail()) : ?><figure><?php the_post_thumbnail('large'); ?></figure><?php endif; ?>
            <?php if ($type === 'activity') : \Kermanentzat\Editorial\render_activity_meta(get_post()); endif; ?>
            <?php the_content(); ?>
            <?php \Kermanentzat\Editorial\render_public_sources($post_id); ?>
        </div>
    </article>
    <?php
}
get_footer();
