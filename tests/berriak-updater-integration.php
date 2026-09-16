<?php
/** Run only in local Docker with WORDPRESS_TABLE_PREFIX=kbu_test_20260916_ and WP_ENVIRONMENT_TYPE=local. */
if (getenv('WORDPRESS_TABLE_PREFIX') !== 'kbu_test_20260916_') exit("Wrong test prefix\n");
define('WP_INSTALLING', true);
$_SERVER['HTTP_HOST']='localhost';
$_SERVER['REQUEST_URI']='/';
require '/var/www/html/wp-load.php';
require ABSPATH.'wp-admin/includes/upgrade.php';
$prefix='kbu_test_20260916_';
if ($wpdb->prefix !== $prefix || wp_get_environment_type() !== 'local') exit("Not isolated local tables\n");
$prior=$wpdb->get_col($wpdb->prepare('SHOW TABLES LIKE %s',$wpdb->esc_like($prefix).'%'));
if ($prior) exit("Test tables already exist; refusing to overwrite\n");
$GLOBALS['passed'] = 0;
function ok($condition,$message) {
    if (!$condition) throw new RuntimeException($message);
    echo 'PASS '.$message."\n";
    $GLOBALS['passed']++;
}
function rejects($callback,$message) {
    try { $callback(); } catch (Throwable $e) { ok(true,$message); return; }
    throw new RuntimeException('Expected rejection: '.$message);
}
try {
    $install=wp_install('Berriak isolated test','kbu_test_admin','test@example.invalid',false,'','test-only-'.bin2hex(random_bytes(16)));
    wp_set_current_user($install['user_id']);
    wp_installing(false);
    $parent=wp_insert_post(['post_type'=>'page','post_status'=>'publish','post_title'=>'Español','post_name'=>'es']);
    $originals=[]; $ids=[];
    foreach (['eu'=>'berriak','es'=>'actualidad'] as $lang=>$slug) {
        $html=file_get_contents('/tmp/kbu-fixtures/'.$lang.'.html');
        if (!preg_match('~<main\b[^>]*>(.*?)</main>~si',$html,$main)) throw new RuntimeException('No main in production fixture');
        $originals[$lang]=$main[1];
        $ids[$lang]=wp_insert_post(wp_slash(['post_type'=>'page','post_status'=>'publish','post_title'=>$slug,'post_name'=>$slug,'post_parent'=>$lang==='es'?$parent:0,'post_content'=>$main[1]]));
        ok(get_post($ids[$lang])->post_content === $originals[$lang], 'Fixture preserved '.$lang);
    }
    $other=wp_insert_post(['post_type'=>'page','post_status'=>'publish','post_title'=>'Untouched','post_name'=>'untouched','post_content'=>'Leave this alone']);
    require '/tmp/kermanentzat-berriak-update/kermanentzat-berriak-update.php';
    ok(get_post($ids['eu'])->post_content === $originals['eu'], 'Loading plugin does not publish');
    $plans=Kermanentzat\BerriakUpdate\plans();
    foreach ($plans as $lang=>$p) {
        $original_html=file_get_contents('/tmp/kbu-fixtures/'.$lang.'.html');
        $result_html=preg_replace_callback('~(<main\b[^>]*>).*?(</main>)~si',fn($m)=>$m[1].$p['after'].$m[2],$original_html);
        file_put_contents('/tmp/kbu-fixtures/result-'.$lang.'.html',$result_html);
        ok($p['added']===34 && $p['total']===35,'34 additions, 35 total '.$lang);
        preg_match('~<article\b.*?</article>~si',$p['before'],$old);
        ok(str_contains($p['after'],$old[0]),'Existing ORAIN article preserved verbatim '.$lang);
        $strip=fn($s)=>preg_replace('~<section\b[^>]*class=["\'][^"\']*updates-feed[^"\']*["\'][^>]*>.*?</section>~si','FEED',$s);
        ok($strip($p['before'])===$strip($p['after']),'Content outside news feed unchanged '.$lang);
        $again=Kermanentzat\BerriakUpdate\merge_content($p['after'],$lang);
        ok($again['added']===0 && $again['content']===$p['after'],'Merge is idempotent '.$lang);
    }
    $hashes=['eu'=>$plans['eu']['hash'],'es'=>$plans['es']['hash']];
    rejects(fn()=>Kermanentzat\BerriakUpdate\merge_content('[shortcode]','eu'),'Dynamic or unknown page layout rejected');
    rejects(fn()=>Kermanentzat\BerriakUpdate\apply(['eu'=>'stale','es'=>'stale']),'Stale preview rejected before backup/write');
    ok(!get_option(Kermanentzat\BerriakUpdate\BACKUP),'No backup or mutation after stale preview');
    wp_set_current_user(0);
    rejects(fn()=>Kermanentzat\BerriakUpdate\apply($hashes),'Anonymous user rejected');
    wp_set_current_user($install['user_id']);
    // Force the second page to fail, then verify recovery from a partial application.
    $failure=function($empty,$postarr) use($ids){return (int)($postarr['ID']??0)===$ids['es'] ? true : $empty;};
    add_filter('wp_insert_post_empty_content',$failure,10,2);
    rejects(fn()=>Kermanentzat\BerriakUpdate\apply($hashes),'Second-page write failure surfaced');
    remove_filter('wp_insert_post_empty_content',$failure,10);
    ok(get_post($ids['eu'])->post_content===$plans['eu']['after'] && get_post($ids['es'])->post_content===$originals['es'],'Partial update tracked with original backup');
    Kermanentzat\BerriakUpdate\restore();
    ok(get_post($ids['eu'])->post_content===$originals['eu'],'Partial update can be restored');
    Kermanentzat\BerriakUpdate\apply($hashes);
    Kermanentzat\BerriakUpdate\apply($hashes);
    foreach($ids as $lang=>$id) ok(get_post($id)->post_content===$plans[$lang]['after'],'Apply/reapply exact result '.$lang);
    ok(get_post($other)->post_content==='Leave this alone','Unrelated page unchanged');
    ok(get_option(Kermanentzat\BerriakUpdate\BACKUP)['pages']['eu']['before']===$originals['eu'],'Original backup not overwritten on reapply');
    ob_start(); Kermanentzat\BerriakUpdate\screen(); $screen=ob_get_clean();
    ok(str_contains($screen,'Aplicar noticias') && str_contains($screen,'_wpnonce') && str_contains($screen,'Restaurar contenido anterior'),'Admin preview, nonce and restore controls render');
    wp_update_post(wp_slash(['ID'=>$ids['es'],'post_content'=>$plans['es']['after'].'<p>Later editor change</p>']));
    rejects(fn()=>Kermanentzat\BerriakUpdate\restore(),'Later edit blocks restoration');
    rejects(fn()=>Kermanentzat\BerriakUpdate\apply($hashes),'Later edit blocks reapply');
    ok(get_post($ids['eu'])->post_content===$plans['eu']['after'],'Both pages validated before restore writes');
    wp_update_post(wp_slash(['ID'=>$ids['es'],'post_content'=>$plans['es']['after']]));
    Kermanentzat\BerriakUpdate\restore();
    foreach($ids as $lang=>$id) ok(get_post($id)->post_content===$originals[$lang],'Full original content restored '.$lang);
    $extra='<article class="updates-entry"><h3>Unrelated existing card</h3><time datetime="2026-09-01">01/09/2026</time><a href="https://example.org/other">Other</a></article>';
    $fixture=str_replace('</article>','</article>'.$extra,$originals['eu']);
    $merged=Kermanentzat\BerriakUpdate\merge_content($fixture,'eu');
    ok($merged['total']===36 && str_contains($merged['content'],$extra),'Unrelated existing news preserved');
    echo 'SUCCESS: ' . $GLOBALS['passed'] . " checks\n";
} finally {
    // Only tables created under this exact isolated prefix are eligible for cleanup.
    $tables=$wpdb->get_col($wpdb->prepare('SHOW TABLES LIKE %s',$wpdb->esc_like($prefix).'%'));
    foreach($tables as $table) {
        if (!preg_match('/^kbu_test_20260916_[a-z_]+$/D',$table)) throw new RuntimeException('Unsafe test cleanup target');
        $wpdb->query('DROP TABLE `'.$table.'`');
    }
}
