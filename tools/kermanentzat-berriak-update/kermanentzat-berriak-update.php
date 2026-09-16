<?php
/**
 * Plugin Name: Kermanentzat · Actualizar noticias (septiembre 2026)
 * Description: Vista previa, copia y actualización puntual de Berriak y Actualidad. No cambia contenido al activarse.
 * Version: 1.0.0
 * Requires PHP: 8.0
 * Author: Egia Kermanentzat
 */
namespace Kermanentzat\BerriakUpdate;
defined('ABSPATH') || exit;

const BACKUP = 'kermanentzat_berriak_backup_20260916_v1';
const LOCK = 'kermanentzat_berriak_lock_20260916_v1';

function news(): array {
    static $data;
    if ($data === null) {
        $data = json_decode(file_get_contents(__DIR__ . '/news.json'), true, 512, JSON_THROW_ON_ERROR);
        if (count($data) !== 35) throw new \RuntimeException('El paquete no contiene las 35 referencias esperadas.');
    }
    return $data;
}

function article(array $item, string $lang): string {
    $n = $item[$lang];
    $label = $lang === 'eu' ? 'Kazetaritza-estaldura' : 'Cobertura periodística';
    if ($item['kind'] === 'opinion') $label = $lang === 'eu' ? 'Iritzi-gutuna' : 'Carta de opinión';
    if ($item['kind'] === 'audio') $label = $lang === 'eu' ? 'Irrati-elkarrizketa' : 'Entrevista de radio';
    $link = $lang === 'eu' ? 'Jatorrizko argitalpena' : 'Publicación original';
    $tab = $lang === 'eu' ? 'fitxa berri batean irekiko da' : 'se abre en una pestaña nueva';
    $date = $item['date'];
    $display = strlen($date) === 10 ? substr($date,8,2).'/'.substr($date,5,2).'/'.substr($date,0,4) : substr($date,5,2).'/'.substr($date,0,4);
    return '<article class="updates-entry"><div class="updates-entry__meta"><span class="updates-entry__nature">'.esc_html($label).'</span><span>'.esc_html($item['medium']).'</span><time datetime="'.esc_attr($date).'">'.esc_html($display).'</time></div><div class="updates-entry__body"><h3>'.esc_html($n['title']).'</h3><p>'.esc_html($n['summary']).'</p><a class="updates-entry__link" href="'.esc_url($n['url']).'" target="_blank" rel="noopener noreferrer">'.esc_html($link).' <span aria-hidden="true">↗</span><span class="screen-reader-text"> ('.esc_html($tab).')</span></a></div></article>';
}

function normalize_url(string $url): string {
    return rtrim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'), '/');
}

/** Preserve existing cards verbatim; only add missing references within the legacy feed. */
function merge_content(string $content, string $lang): array {
    $pattern = '~<section\b[^>]*class=["\'][^"\']*\bupdates-feed\b[^"\']*["\'][^>]*>.*?</section>~si';
    if (preg_match_all($pattern, $content, $sections, PREG_OFFSET_CAPTURE) !== 1) {
        throw new \RuntimeException('No se reconoce un único listado de noticias del formato anterior. No se ha cambiado la página.');
    }
    [$section, $offset] = $sections[0][0];
    preg_match_all('~<article\b[^>]*>.*?</article>~si', $section, $matches, PREG_OFFSET_CAPTURE);
    if (!$matches[0]) throw new \RuntimeException('El listado no contiene la noticia existente esperada.');
    $start = $matches[0][0][1];
    $last = end($matches[0]);
    $length = $last[1] + strlen($last[0]) - $start;
    $region = substr($section, $start, $length);
    if (trim(preg_replace('~<article\b[^>]*>.*?</article>~si', '', $region)) !== '') {
        throw new \RuntimeException('Hay contenido entre las noticias que requiere revisión manual.');
    }
    $existing = [];
    $urls = [];
    foreach ($matches[0] as [$markup]) {
        preg_match('~<time\b[^>]*datetime=["\']([^"\']+)~i', $markup, $time);
        $existing[] = ['html'=>$markup, 'date'=>$time[1] ?? '', 'order'=>count($existing)];
        preg_match_all('~href=["\']([^"\']+)["\']~i', $markup, $links);
        foreach ($links[1] as $url) $urls[normalize_url($url)] = true;
    }
    $added = 0;
    foreach (news() as $item) {
        if (isset($urls[normalize_url($item[$lang]['url'])])) continue;
        $existing[] = ['html'=>article($item,$lang), 'date'=>$item['date'], 'order'=>count($existing)];
        $urls[normalize_url($item[$lang]['url'])] = true;
        $added++;
    }
    usort($existing, fn($a,$b) => strcmp($b['date'],$a['date']) ?: ($a['order'] <=> $b['order']));
    $updated = substr_replace($section, implode("\n",array_column($existing,'html')), $start, $length);
    return ['content'=>substr_replace($content,$updated,$offset,strlen($section)), 'added'=>$added, 'total'=>count($existing), 'feed'=>$updated];
}

function plans(): array {
    $plans = [];
    foreach (['eu'=>'berriak','es'=>'es/actualidad'] as $lang=>$path) {
        $post = get_page_by_path($path, OBJECT, 'page');
        if (!$post || $post->post_status !== 'publish') throw new \RuntimeException('No se encuentra la página publicada: '.$path);
        if (!current_user_can('edit_post',$post->ID)) throw new \RuntimeException('No tienes permiso para editar '.$path);
        $merged = merge_content($post->post_content,$lang);
        $plans[$lang] = ['id'=>$post->ID, 'path'=>$path, 'before'=>$post->post_content, 'after'=>$merged['content'], 'hash'=>hash('sha256',$post->post_content), 'added'=>$merged['added'], 'total'=>$merged['total'], 'feed'=>$merged['feed']];
    }
    return $plans;
}

function authorize(): void {
    if (!current_user_can('manage_options') || !current_user_can('unfiltered_html')) {
        throw new \RuntimeException('Esta herramienta requiere una cuenta administradora con permiso para guardar HTML.');
    }
}

/** Check every page before either write, including stale previews and post-update edits. */
function validate_saved(array $saved): void {
    foreach ($saved['pages'] as $page) {
        $post = get_post($page['id']);
        if (!$post || $post->post_type !== 'page' || $post->post_status !== 'publish' || get_page_uri($post) !== $page['path'] || !current_user_can('edit_post',$post->ID)) {
            throw new \RuntimeException('Una página cambió de identidad, estado o permisos. No se continuará.');
        }
        if (!in_array($post->post_content,[$page['before'],$page['after']],true)) {
            throw new \RuntimeException('Hay una edición posterior en '.$page['path'].'. Se conserva; no se sobrescribe.');
        }
    }
}

function write_pages(array $saved, string $field): void {
    validate_saved($saved);
    foreach ($saved['pages'] as $page) {
        $post = get_post($page['id']);
        if ($post->post_content === $page[$field]) continue;
        // Only existing published pages are updated: no editorial entities or Sender queue.
        $result = wp_update_post(wp_slash(['ID'=>$page['id'],'post_content'=>$page[$field]]),true);
        if (is_wp_error($result)) throw new \RuntimeException($result->get_error_message());
        clean_post_cache($page['id']);
        if (get_post($page['id'])->post_content !== $page[$field]) {
            throw new \RuntimeException('WordPress no conservó el contenido esperado. La copia está disponible para restaurar.');
        }
    }
}

function apply(array $hashes): void {
    authorize();
    $saved = get_option(BACKUP);
    if (!$saved) {
        $plans = plans();
        foreach ($plans as $lang=>$plan) {
            if (!isset($hashes[$lang]) || !hash_equals($plan['hash'],(string)$hashes[$lang])) throw new \RuntimeException('El contenido cambió desde la vista previa. Vuelve a revisarlo.');
        }
        $pages = [];
        foreach ($plans as $lang=>$plan) $pages[$lang] = array_intersect_key($plan,array_flip(['id','path','before','after']));
        $saved = ['version'=>1,'created_at'=>gmdate('c'),'site'=>home_url('/'),'pages'=>$pages];
        if (!add_option(BACKUP,$saved,'',false)) throw new \RuntimeException('No se pudo guardar la copia de seguridad. No se ha publicado nada.');
        if (get_option(BACKUP) !== $saved) throw new \RuntimeException('No se pudo verificar la copia de seguridad.');
    }
    write_pages($saved,'after');
}

function restore(): void {
    authorize();
    $saved = get_option(BACKUP);
    if (!$saved) throw new \RuntimeException('No hay copia de seguridad de esta actualización.');
    write_pages($saved,'before');
}

add_action('admin_menu',function() {
    add_management_page('Actualizar noticias','Actualizar noticias','manage_options','kermanentzat-berriak-update',__NAMESPACE__.'\\screen');
});

add_filter('plugin_action_links_'.plugin_basename(__FILE__),function($links) {
    array_unshift($links,'<a href="'.esc_url(admin_url('tools.php?page=kermanentzat-berriak-update')).'">Vista previa y publicación</a>');
    return $links;
});

function screen(): void {
    echo '<div class="wrap"><h1>Actualizar Berriak y Actualidad</h1>';
    try {
        authorize();
        $saved = get_option(BACKUP);
        $plans = plans();
        echo '<p>Paquete del 16/09/2026: 35 referencias por idioma. Las noticias existentes se conservan. Solo se actualiza el listado de estas dos páginas, con sus estilos actuales.</p>';
        echo '<p>Las tres parejas de EITB se agrupan; se omite el enlace repetido de elDiario.es y se corrige la terminación del enlace del homenaje de EITB. Las referencias de Noticias de Álava usan los títulos y enlaces aportados: el medio ha bloqueado la consulta automática.</p>';
        if ($saved) {
            validate_saved($saved);
            echo '<div class="notice notice-info inline"><p>Hay una copia de seguridad guardada. Aplicar de nuevo no duplica las noticias. Restaurar recupera únicamente el contenido anterior de estas dos páginas.</p></div>';
        }
        foreach ($plans as $lang=>$p) {
            echo '<h2>'.esc_html($lang==='eu'?'Berriak · Euskara':'Actualidad · Castellano').'</h2><p>'.(int)$p['added'].' referencias pendientes · '.(int)$p['total'].' tarjetas en el resultado.</p>';
            echo '<details><summary>Ver contenido resultante</summary><div class="kbu-preview" lang="'.esc_attr($lang).'">'.wp_kses_post($p['feed']).'</div></details>';
        }
        echo '<style>.kbu-preview{max-width:1100px;background:#f5f0e7;color:#222;padding:24px;margin:16px 0}.kbu-preview article{border-top:1px solid #bbb;padding:20px 0;display:grid;grid-template-columns:190px 1fr;gap:24px}.kbu-preview span,.kbu-preview time{display:block}.kbu-preview h3{font-size:20px;margin-top:0}.kbu-preview p{font-size:16px;line-height:1.6}.kbu-preview .screen-reader-text{display:none}@media(max-width:700px){.kbu-preview article{grid-template-columns:1fr}}</style>';
        foreach (['download'=>'Descargar copia de seguridad','apply'=>'Aplicar noticias','restore'=>'Restaurar contenido anterior'] as $op=>$label) {
            if ($op==='restore' && !$saved) continue;
            echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'" style="display:inline-block;margin:24px 12px 0 0"><input type="hidden" name="action" value="kbu_update"><input type="hidden" name="operation" value="'.esc_attr($op).'">';
            wp_nonce_field('kbu_'.$op);
            foreach ($plans as $lang=>$p) echo '<input type="hidden" name="hashes['.esc_attr($lang).']" value="'.esc_attr($p['hash']).'">';
            submit_button($label,$op==='apply'?'primary':'secondary','submit',false);
            echo '</form>';
        }
        echo '<p>La copia se guarda automáticamente antes de publicar y permanece en WordPress aunque elimines el plugin. Descárgala también para conservarla fuera del sitio. Si se editan las páginas después, la restauración se bloquea para proteger esas ediciones.</p>';
    } catch (\Throwable $e) {
        echo '<div class="notice notice-error inline"><p>'.esc_html($e->getMessage()).'</p></div>';
        // Backup download remains accessible even after later incompatible edits.
        if (get_option(BACKUP) && current_user_can('manage_options')) {
            echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="kbu_update"><input type="hidden" name="operation" value="download">';
            wp_nonce_field('kbu_download'); submit_button('Descargar copia guardada'); echo '</form>';
        }
    }
    echo '</div>';
}

add_action('admin_post_kbu_update',function() {
    $locked = false;
    try {
        authorize();
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') throw new \RuntimeException('Se requiere POST.');
        $op = sanitize_key($_POST['operation'] ?? '');
        if (!in_array($op,['apply','restore','download'],true)) throw new \RuntimeException('Operación desconocida.');
        check_admin_referer('kbu_'.$op);
        if ($op === 'download') {
            $saved = get_option(BACKUP);
            if (!$saved) {
                $pages=[];
                foreach (plans() as $lang=>$p) $pages[$lang]=array_intersect_key($p,array_flip(['id','path','before','after']));
                $saved=['version'=>1,'created_at'=>gmdate('c'),'site'=>home_url('/'),'pages'=>$pages];
            }
            nocache_headers();
            header('Content-Type: application/json; charset=utf-8');
            header('Content-Disposition: attachment; filename="berriak-copia-20260916.json"');
            echo wp_json_encode($saved,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            exit;
        }
        $locked=add_option(LOCK,gmdate('c'),'',false);
        if (!$locked) throw new \RuntimeException('Ya hay una actualización en curso. No se inicia otra.');
        if ($op==='apply') apply(is_array($_POST['hashes']??null)?wp_unslash($_POST['hashes']):[]);
        else restore();
    } catch (\Throwable $e) {
        if ($locked) delete_option(LOCK);
        wp_die(esc_html($e->getMessage()).'<p>Si una página llegó a actualizarse, vuelve a Herramientas → Actualizar noticias y usa Restaurar contenido anterior. La copia original no se borra.</p>','Actualización detenida',['back_link'=>true]);
    }
    if ($locked) delete_option(LOCK);
    wp_safe_redirect(admin_url('tools.php?page=kermanentzat-berriak-update'));
    exit;
});
