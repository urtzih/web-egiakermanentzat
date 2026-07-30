<?php
defined('ABSPATH') || exit;
$language = kermanentzat_language();
$other = $language === 'es' ? 'eu' : 'es';
$key = kermanentzat_page_key();
$labels = $language === 'eu'
    ? ['home' => 'Hasiera', 'case' => 'Kasua', 'updates' => 'Berriak', 'support' => 'Lagundu', 'contact' => 'Kontaktua']
    : ['home' => 'Inicio', 'case' => 'El caso', 'updates' => 'Actualidad', 'support' => 'Ayuda', 'contact' => 'Contacto'];
?><!doctype html>
<html lang="<?php echo esc_attr($language); ?>">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main-content"><?php echo $language === 'eu' ? 'Joan edukira' : 'Saltar al contenido'; ?></a>
<header class="site-header">
    <a class="site-brand" href="<?php echo esc_url(kermanentzat_url($language, 'home')); ?>" aria-label="Egia Kermanentzat">
        <span>EGIA</span><span>KERMANENTZAT</span>
    </a>
    <details class="mobile-nav">
        <summary>
            <span class="mobile-nav__icon" aria-hidden="true"></span>
            <span class="mobile-nav__label mobile-nav__label--closed"><?php echo $language === 'eu' ? 'Menua' : 'Menú'; ?></span>
            <span class="mobile-nav__label mobile-nav__label--open"><?php echo $language === 'eu' ? 'Itxi' : 'Cerrar'; ?></span>
        </summary>
        <nav aria-label="<?php echo $language === 'eu' ? 'Nabigazio nagusia' : 'Navegación principal'; ?>">
            <?php foreach ($labels as $navKey => $label) : ?>
                <a href="<?php echo esc_url(kermanentzat_url($language, $navKey)); ?>" <?php echo $key === $navKey ? 'aria-current="page"' : ''; ?>><?php echo esc_html($label); ?></a>
            <?php endforeach; ?>
        </nav>
    </details>
    <nav class="desktop-nav" aria-label="<?php echo $language === 'eu' ? 'Nabigazio nagusia' : 'Navegación principal'; ?>">
        <?php foreach ($labels as $navKey => $label) : ?>
            <a href="<?php echo esc_url(kermanentzat_url($language, $navKey)); ?>" <?php echo $key === $navKey ? 'aria-current="page"' : ''; ?>><?php echo esc_html($label); ?></a>
        <?php endforeach; ?>
    </nav>
    <a class="language-switch" href="<?php echo esc_url(kermanentzat_url($other, $key)); ?>" hreflang="<?php echo esc_attr($other); ?>">
        <span class="language-muted"><?php echo strtoupper($language); ?></span><span aria-hidden="true">/</span><strong><?php echo strtoupper($other); ?></strong>
        <span class="screen-reader-text"><?php echo $language === 'eu' ? 'Gaztelaniaz ikusi' : 'Ikusi euskaraz'; ?></span>
    </a>
</header>
<?php if (kermanentzat_is_home()) : ?>
<div class="campaign-ticker" aria-hidden="true">
    <div class="campaign-ticker__track">
        <?php for ($group = 0; $group < 2; $group++) : ?>
            <div class="campaign-ticker__group">
                <span class="campaign-ticker__phrase">JUSTIZIA KERMANENTZAT</span>
                <span class="campaign-ticker__separator">·</span>
                <span class="campaign-ticker__phrase">JUSTICIA PARA KERMAN</span>
                <span class="campaign-ticker__separator">·</span>
                <span class="campaign-ticker__phrase">JUSTIZIA KERMANENTZAT</span>
                <span class="campaign-ticker__separator">·</span>
                <span class="campaign-ticker__phrase">JUSTICIA PARA KERMAN</span>
                <span class="campaign-ticker__separator">·</span>
            </div>
        <?php endfor; ?>
    </div>
</div>
<?php endif; ?>
<main id="main-content" tabindex="-1">
