<?php
defined('ABSPATH') || exit;
$language = kermanentzat_language();
?>
</main>
<footer class="site-footer">
    <div class="site-footer__brand"><span>EGIA</span> <strong>KERMANENTZAT</strong></div>
    <div class="site-footer__meta">
        <span>NIF G93797744</span>
        <a href="mailto:justiziakermanentzat@gmail.com">justiziakermanentzat@gmail.com</a>
        <a href="https://www.instagram.com/justizia.kermanentzat/" target="_blank" rel="noopener noreferrer">Instagram · @justizia.kermanentzat</a>
    </div>
    <p><?php echo $language === 'eu' ? 'Kermanen memoria, egia, justizia eta erreparazioa.' : 'Memoria de Kerman, verdad, justicia y reparación.'; ?></p>
</footer>
<?php wp_footer(); ?>
</body>
</html>
