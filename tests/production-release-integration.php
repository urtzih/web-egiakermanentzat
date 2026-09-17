<?php
/** Run only in local Docker with an isolated table prefix. */
if (getenv('WORDPRESS_TABLE_PREFIX') !== 'kpr_test_20260917_') exit("Wrong test prefix\n");
define('WP_INSTALLING', true);
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';
require '/var/www/html/wp-load.php';
require ABSPATH . 'wp-admin/includes/upgrade.php';

$prefix = 'kpr_test_20260917_';
if ($wpdb->prefix !== $prefix || wp_get_environment_type() !== 'local') exit("Not isolated local tables\n");
$prior = $wpdb->get_col($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($prefix) . '%'));
if ($prior) exit("Test tables already exist; refusing to overwrite\n");

$GLOBALS['release_passed'] = 0;
function release_ok($condition, string $message): void {
    if (!$condition) throw new RuntimeException($message);
    echo "PASS $message\n";
    $GLOBALS['release_passed']++;
}
function release_copy_tree(string $from, string $to): void {
    if (!is_dir($to) && !mkdir($to, 0777, true) && !is_dir($to)) throw new RuntimeException('mkdir failed');
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($from, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item) {
        $target = $to . '/' . substr($item->getPathname(), strlen($from) + 1);
        $item->isDir() ? (is_dir($target) || mkdir($target, 0777, true)) : copy($item->getPathname(), $target);
    }
}
function release_remove_tree(string $path): void {
    if (!is_dir($path)) return;
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $item) {
        $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
    }
    rmdir($path);
}

$upload_root = '/tmp/kpr-uploads-' . bin2hex(random_bytes(4));
try {
    $install = wp_install('Release isolated test', 'release_test_admin', 'test@example.invalid', false, '', 'test-' . bin2hex(random_bytes(16)));
    wp_set_current_user($install['user_id']);
    wp_installing(false);
    update_option('upload_path', $upload_root);
    switch_theme('kermanentzat-prototype');
    require_once get_theme_root() . '/kermanentzat-prototype/functions.php';

    $parent = wp_insert_post(['post_type'=>'page','post_status'=>'publish','post_title'=>'Español','post_name'=>'es']);
    foreach (['kasuaren-laburpena'=>0, 'resumen-del-caso'=>$parent] as $slug=>$parent_id) {
        wp_insert_post(wp_slash(['post_type'=>'page','post_status'=>'publish','post_title'=>$slug,'post_name'=>$slug,'post_parent'=>$parent_id,'post_content'=>'<!-- wp:shortcode -->[kermanentzat_timeline featured="true"]<!-- /wp:shortcode -->']));
    }
    foreach (['pribatutasun-politika'=>0,'cookie-politika'=>0,'politica-de-privacidad'=>$parent,'politica-de-cookies'=>$parent] as $slug=>$parent_id) {
        wp_insert_post(['post_type'=>'page','post_status'=>'publish','post_title'=>$slug,'post_name'=>$slug,'post_parent'=>$parent_id,'post_content'=>'legacy legal']);
    }

    require '/tmp/kbu/kermanentzat-berriak-update.php';
    $items = Kermanentzat\BerriakUpdate\news();
    $archive_ids = [];
    foreach (['eu'=>['berriak',0], 'es'=>['actualidad',$parent]] as $lang=>[$slug,$parent_id]) {
        $cards = array_map(fn($item) => Kermanentzat\BerriakUpdate\article($item, $lang), $items);
        $content = '<section class="updates-feed">' . implode("\n", $cards) . '</section>';
        $archive_ids[$lang] = wp_insert_post(wp_slash(['post_type'=>'page','post_status'=>'publish','post_title'=>$slug,'post_name'=>$slug,'post_parent'=>$parent_id,'post_content'=>$content]));
    }
    $original_eu = get_post($archive_ids['eu'])->post_content;

    $source = $upload_root . '/kermanentzat-release-20260917';
    release_copy_tree('/tmp/kpr-media', $source);
    file_put_contents($source . '/.htaccess', "Require all denied\n");

    require '/tmp/release/kermanentzat-production-release.php';
    Kermanentzat\ProductionRelease\migration_preview();
    release_ok(true, 'Strict preview plans complete migration');
    $backup = Kermanentzat\ProductionRelease\backup();
    release_ok($backup['before_hash'] === Kermanentzat\ProductionRelease\state_hash($backup['before']), 'Backup hash verified');
    Kermanentzat\ProductionRelease\apply_release();

    foreach (['eu','es'] as $language) {
        $count = count(get_posts(['post_type'=>'kerman_update','post_status'=>'publish','numberposts'=>-1,'meta_key'=>'_kerman_language','meta_value'=>$language]));
        release_ok($count === 35, "35 editorial entries $language");
    }
    release_ok(count(get_posts(['post_type'=>'kerman_source','post_status'=>'private','numberposts'=>-1])) === 70, '70 private source records');
    release_ok(!get_page_by_path('harpidetza') && !get_page_by_path('es/suscripcion'), 'Subscription pages not created');
    foreach (['kasuaren-laburpena','es/resumen-del-caso'] as $path) {
        $content = get_page_by_path($path)->post_content;
        release_ok(substr_count($content, '<video') === 8, "Eight videos in $path");
        release_ok(str_contains($content, 'kermanentzat_timeline') && !str_contains($content, 'case-timeline'), "Dynamic chronology retained in $path");
        release_ok(strpos($content, 'kermanentzat_timeline') < strpos($content, 'document-download'), "Documentation follows chronology in $path");
    }
    $applied_hash = get_option(Kermanentzat\ProductionRelease\BACKUP_OPTION)['after_hash'];
    Kermanentzat\ProductionRelease\apply_release();
    release_ok(get_option(Kermanentzat\ProductionRelease\BACKUP_OPTION)['after_hash'] === $applied_hash, 'Reapply is idempotent');

    Kermanentzat\ProductionRelease\restore_release();
    release_ok(get_post($archive_ids['eu'])->post_content === $original_eu, 'Static news restored exactly');
    release_ok(count(get_posts(['post_type'=>'kerman_update','post_status'=>'any','numberposts'=>-1])) === 0, 'Created editorial entries removed on restore');
    release_ok(!in_array(Kermanentzat\ProductionRelease\EDITORIAL_PLUGIN, get_option('active_plugins', []), true), 'Editorial plugin activation restored');
    echo 'SUCCESS: ' . $GLOBALS['release_passed'] . " checks\n";
} finally {
    release_remove_tree($upload_root);
    $tables = $wpdb->get_col($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($prefix) . '%'));
    foreach ($tables as $table) {
        if (!preg_match('/^kpr_test_20260917_[a-z_]+$/D', $table)) throw new RuntimeException('Unsafe test cleanup target');
        $wpdb->query('DROP TABLE `' . $table . '`');
    }
}
