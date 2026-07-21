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
    <div class="site-footer__closing">
        <p>
            <?php echo $language === 'eu' ? 'Kermanen oroimenez. Egiaren, justiziaren eta erreparazioaren alde.' : 'En memoria de Kerman. Por la verdad, la justicia y la reparación.'; ?>
            <span class="site-footer__credit"><?php if ($language === 'eu') : ?><a href="https://saretu.es" target="_blank" rel="noopener noreferrer">Saretu.es</a>-ek sortua.<?php else : ?>Creada por <a href="https://saretu.es" target="_blank" rel="noopener noreferrer">Saretu.es</a>.<?php endif; ?></span>
        </p>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
