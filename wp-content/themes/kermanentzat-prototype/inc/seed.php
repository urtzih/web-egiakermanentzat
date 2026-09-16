<?php

defined('ABSPATH') || exit;

require_once get_theme_file_path('inc/legal-content.php');
require_once get_theme_file_path('inc/case-media-content.php');

/**
 * Idempotent bilingual MVP content seed.
 * Run with: wp eval-file wp-content/themes/kermanentzat-prototype/inc/seed.php
 */
function kermanentzat_seed_page(string $title, string $slug, string $content, int $parent = 0): int
{
    $path = $parent ? get_post_field('post_name', $parent) . '/' . $slug : $slug;
    $existing = get_page_by_path($path);
    if (!$existing) {
        $sameSlug = get_posts([
            'name' => $slug,
            'post_type' => 'page',
            'post_status' => 'any',
            'numberposts' => 1,
        ]);
        $existing = $sameSlug[0] ?? null;
    }

    // The seed is a bootstrap, not a synchronization mechanism. Once a page
    // exists, WordPress is its source of truth and editorial changes survive
    // future deployments and setup runs.
    if ($existing instanceof WP_Post) {
        if (class_exists('WP_CLI')) {
            WP_CLI::log(sprintf('Conservada página existente: %s (#%d).', $path, $existing->ID));
        }
        return (int) $existing->ID;
    }

    $id = wp_insert_post(wp_slash([
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_title' => $title,
        'post_name' => $slug,
        'post_parent' => $parent,
        'post_content' => "<!-- wp:html -->\n{$content}\n<!-- /wp:html -->",
        'comment_status' => 'closed',
    ]), true);

    if (is_wp_error($id)) {
        throw new RuntimeException($id->get_error_message());
    }
    return (int) $id;
}

$imagePng = esc_url(wp_make_link_relative(get_theme_file_uri('assets/images/kerman-portrait-clean.png')));
$imageWebp = esc_url(wp_make_link_relative(get_theme_file_uri('assets/images/kerman-portrait-clean.webp')));
$heroPictureEs = '<picture><source srcset="' . $imageWebp . '" type="image/webp"><img src="' . $imagePng . '" width="717" height="762" fetchpriority="high" loading="eager" decoding="async" alt="Retrato gráfico en blanco y negro de Kerman"></picture>';
$heroPictureEu = '<picture><source srcset="' . $imageWebp . '" type="image/webp"><img src="' . $imagePng . '" width="717" height="762" fetchpriority="high" loading="eager" decoding="async" alt="Kermanen zuri-beltzeko erretratu grafikoa"></picture>';
$caseArt = '<div class="page-hero__art" aria-hidden="true"><picture><source srcset="' . $imageWebp . '" type="image/webp"><img src="' . $imagePng . '" width="717" height="762" fetchpriority="high" loading="eager" decoding="async" alt=""></picture></div>';
$legalConfig = kermanentzat_legal_config();
$email = $legalConfig['email'];
$instagram = 'https://www.instagram.com/justizia.kermanentzat/';
$orainEs = 'https://orain.eus/es/actualidad/sociedad/2026/08/02/nuevos-testimonios-apuntan-agresiones-reiteradas-porteros-mitika-antes-la-muerte-kerman-villate/';
$orainEu = 'https://orain.eus/eu/aktualitatea/gizartea/2026/08/02/testigantza-berriek-agerian-utzi-dituzte-mitikako-atezainek-kerman-villate-hil-aurretik-behin-eta-berriz-egindako-erasoak/';
$iban = 'ES0830350079270790062136';
$ibanDisplay = 'ES08 3035 0079 2707 9006 2136';
$bic = 'CLPEES2MXXX';
$conceptEs = 'APORTACION KERMANENTZAT';
$conceptEu = 'EKARPENA KERMANENTZAT';
$copyBankEs = esc_attr("Titular: Egia Kermanentzat Elkartea\nIBAN: {$ibanDisplay}\nBIC: {$bic}\nConcepto: {$conceptEs}");
$copyBankEu = esc_attr("Titularra: Egia Kermanentzat Elkartea\nIBAN: {$ibanDisplay}\nBIC: {$bic}\nKontzeptua: {$conceptEu}");

$homeEs = <<<HTML
<section class="hero-shell hero-panel" aria-labelledby="memory-title-es">
  <div class="hero-media">{$heroPictureEs}</div>
  <div class="hero-copy">
    <p class="hero-kicker">Memoria · verdad · justicia</p>
    <h1 class="hero-title" id="memory-title-es">Kerman</h1>
    <p class="hero-statement">Una vida interrumpida. Una familia y una comunidad rotas.</p>
    <div class="hero-actions"><a class="button button--primary" href="/es/resumen-del-caso/">Conocer el resumen</a><a class="button" href="/es/ayuda-y-donaciones/">Ayudar y apoyar</a></div>
  </div>
</section>
<section class="content-band content-band--dark"><div class="content-wrap campaign-grid">
  <div data-reveal><h2 class="campaign-heading"><span>Kerman no murió,</span><strong>lo mataron</strong></h2></div>
  <div class="campaign-copy" data-reveal><p class="lead">El 23 de febrero de 2025, Kerman Villate Beitia murió tras recibir un brutal puñetazo de un portero de la discoteca Mitika, en Vitoria-Gasteiz.</p><p>Una vida interrumpida, una familia destruida y una comunidad rota.</p><p>Abrimos este espacio para honrar su memoria, pedir justicia y reclamar verdad y reparación.</p><a class="button button--inverse" href="/es/resumen-del-caso/">Conocer lo sucedido</a></div>
</div></section>
<section class="content-band content-band--light"><div class="content-wrap split-grid">
  <div data-reveal><span class="content-label">La iniciativa</span><h2 class="section-heading">Memoria, información rigurosa y apoyo colectivo.</h2></div>
  <div class="reading-copy" data-reveal><p class="lead">Queremos que se conozca qué ocurrió aquella noche, cómo ha evolucionado el proceso judicial y por qué consideramos insuficiente la respuesta recibida.</p><div class="evidence-list"><div class="evidence-row"><strong>Comprender</strong><p>Un resumen claro de lo sucedido y de la evolución judicial conocida.</p></div><div class="evidence-row"><strong>Acompañar</strong><p>Formas directas de apoyarnos, contactar y participar.</p></div><div class="evidence-row"><strong>Informar</strong><p>Actualizaciones contrastadas para explicar la evolución del caso.</p></div></div></div>
</div></section>
<section class="content-band content-band--soft"><div class="content-wrap support-grid">
  <div class="support-callout" data-reveal>Ayuda y <span>apoya</span></div>
  <div class="reading-copy" data-reveal><p class="lead">La verdad y la justicia necesitan compromiso colectivo.</p><p>Tu aportación nos ayuda a preservar la memoria de Kerman y a sostener nuestro trabajo social, jurídico y comunicativo.</p><a class="button button--primary" href="/es/ayuda-y-donaciones/">Cómo ayudar</a></div>
</div></section>
HTML;

$homeEu = <<<HTML
<section class="hero-shell hero-panel" aria-labelledby="memory-title-eu">
  <div class="hero-media">{$heroPictureEu}</div>
  <div class="hero-copy">
    <p class="hero-kicker">Memoria · egia · justizia</p>
    <h1 class="hero-title" id="memory-title-eu">Kerman</h1>
    <p class="hero-statement">Bizi bat etenda. Familia bat eta lagunarte bat apurtuta.</p>
    <div class="hero-actions"><a class="button button--primary" href="/kasuaren-laburpena/">Laburpena ezagutu</a><a class="button" href="/lagundu-eta-ekarpenak/">Lagundu</a></div>
  </div>
</section>
<section class="content-band content-band--dark"><div class="content-wrap campaign-grid">
  <div data-reveal><h2 class="campaign-heading"><span>Kerman ez zen hil,</span><strong>hil egin zuten</strong></h2></div>
  <div class="campaign-copy" data-reveal><p class="lead">2025eko otsailaren 23an, Kerman Villate Beitia hil zen Gasteizko Mitika diskotekako atezain batek emandako ukabilkada bortitz baten ondorioz.</p><p>Bat-batean eten zen bizitza bat, familia bat suntsitu eta komunitate oso bat zauritu.</p><p>Gune hau ireki dugu haren oroimena gogoan izateko, justizia eskatzeko eta egia zein erreparazioa aldarrikatzeko.</p><a class="button button--inverse" href="/kasuaren-laburpena/">Gertatutakoa ezagutu</a></div>
</div></section>
<section class="content-band content-band--light"><div class="content-wrap split-grid">
  <div data-reveal><span class="content-label">Ekimena</span><h2 class="section-heading">Memoria, informazio zorrotza eta babes kolektiboa.</h2></div>
  <div class="reading-copy" data-reveal><p class="lead">Gau hartan zer gertatu zen, prozesu judizialak zer bilakaera izan duen eta jasotako erantzuna zergatik iruditzen zaigun nahikoa ez dela ezagutarazi nahi dugu.</p><div class="evidence-list"><div class="evidence-row"><strong>Ulertu</strong><p>Gertatutakoaren eta ezagutzen den bilakaera judizialaren laburpen argia.</p></div><div class="evidence-row"><strong>Babestu</strong><p>Gu babesteko, gurekin harremanetan jartzeko eta parte hartzeko bide zuzenak.</p></div><div class="evidence-row"><strong>Informatu</strong><p>Kasuaren bilakaera azaltzeko egiaztatutako eguneraketak.</p></div></div></div>
</div></section>
<section class="content-band content-band--soft"><div class="content-wrap support-grid">
  <div class="support-callout" data-reveal>Lagundu eta <span>babestu</span></div>
  <div class="reading-copy" data-reveal><p class="lead">Egiak eta justiziak konpromiso kolektiboa behar dute.</p><p>Zure ekarpenak Kermanen memoria gordetzen eta gure lan sozial, juridiko eta komunikatiboa sostengatzen laguntzen digu.</p><a class="button button--primary" href="/lagundu-eta-ekarpenak/">Nola lagundu</a></div>
</div></section>
HTML;

$caseEs = kermanentzat_case_summary_content('es', $caseArt);
$caseEu = kermanentzat_case_summary_content('eu', $caseArt);

$supportEs = <<<HTML
<header class="page-hero page-hero--support content-band--light"><div class="support-wordmark" aria-hidden="true">APOYA</div><div class="content-wrap"><span class="content-label content-label--campaign">Apoyo colectivo</span><h1>Ayuda y donaciones</h1><p>Ayúdanos a preservar la memoria de Kerman y a sostener nuestro trabajo colectivo.</p></div></header>
<section class="content-band content-band--soft"><div class="content-wrap split-grid"><div><span class="content-label">Transferencia bancaria</span><h2 class="section-heading">Haz tu aportación directamente.</h2></div><div class="reading-copy"><div class="bank-preview"><dl><div><dt>Titular</dt><dd>Egia Kermanentzat Elkartea</dd></div><div><dt>IBAN</dt><dd><code>{$ibanDisplay}</code></dd></div><div><dt>BIC / SWIFT</dt><dd><code>{$bic}</code></dd></div><div><dt>Concepto recomendado</dt><dd><code>{$conceptEs}</code></dd></div></dl><div class="bank-actions"><button class="button button--primary" type="button" data-copy-value="{$iban}" data-feedback-target="#copy-bank-es" data-success-message="IBAN copiado." data-analytics-event="copy_iban">Copiar IBAN</button><button class="button" type="button" data-copy-value="{$copyBankEs}" data-feedback-target="#copy-bank-es" data-success-message="Datos de transferencia copiados." data-analytics-event="copy_bank_details">Copiar todos los datos</button></div><p class="copy-feedback" id="copy-bank-es" role="status" aria-live="polite"></p></div><p class="bank-note">Si necesitas un justificante o gestionar una incidencia o devolución, escribe a <a href="mailto:{$email}">{$email}</a>. Consulta antes de asumir que la aportación genera una deducción fiscal.</p></div></div></section>
<section class="content-band content-band--dark"><div class="content-wrap split-grid"><div><span class="content-label">Pancartas de apoyo</span><h2 class="section-heading">Colabora desde tu ventana o balcón.</h2></div><div class="reading-copy"><p class="lead">Si quieres colaborar poniendo una pancarta de apoyo a Kerman en tu ventana o balcón manda un mensaje a esta dirección: <a href="mailto:{$email}">{$email}</a></p></div></div></section>
<section class="content-band content-band--soft"><div class="content-wrap split-grid"><div><span class="content-label">Charlas y presentaciones</span><h2 class="section-heading">Charla-presentación sobre el caso.</h2></div><div class="reading-copy"><p class="lead">Si quieres solicitar una charla-presentación sobre el caso, escríbenos a: <a href="mailto:{$email}">{$email}</a>.</p></div></div></section>
<section id="informacion-agresiones" class="content-band content-band--dark"><div class="content-wrap split-grid"><div><span class="content-label">Información y apoyo</span><h2 class="section-heading">Puedes contárnoslo.</h2></div><div class="reading-copy"><p class="lead">¿Has sido víctima de una agresión en el entorno de Mitika o conoces algún caso?</p><p>Puedes escribirnos a <a href="mailto:{$email}">{$email}</a>.</p><p>Trataremos tu identidad y la información que compartas con discreción y respeto. No envíes documentación sensible ni datos personales innecesarios. Si necesitas compartir información delicada, escríbenos primero para acordar un canal adecuado.</p></div></div></section>
<section class="content-band content-band--light"><div class="content-wrap support-grid"><div class="support-callout">Tu apoyo <span>sostiene</span></div><div class="reading-copy"><h2 class="section-heading">Para qué se utilizan las aportaciones</h2><p>Las aportaciones nos permiten investigar, ordenar y conservar la documentación del caso; difundir la memoria de Kerman y las actualizaciones contrastadas; impulsar actuaciones jurídicas, informativas, sociales, de sensibilización y prevención; atender gastos técnicos, administrativos y de comunicación; y mantener nuestros canales de participación y apoyo.</p><p>Este es el destino general de los fondos; no establecemos una asignación porcentual cerrada.</p><p class="lead"><strong>¿Quieres hacerte socio/a?</strong> Escríbenos a <a href="mailto:{$email}">{$email}</a> indicando en el asunto «Alta de socio/a». Te enviaremos la información y te indicaremos cómo facilitar los datos necesarios. En el primer correo, indica únicamente tu interés.</p></div></div></section>
<section class="content-band content-band--dark"><div class="content-wrap split-grid"><div><span class="content-label">Privacidad y transparencia</span><h2 class="section-heading">Información sobre tu aportación</h2></div><div class="reading-copy"><p>El receptor es Egia Kermanentzat Elkartea. La transferencia se destina a las finalidades generales descritas en esta página. La entidad bancaria nos comunicará los datos asociados a la operación; los utilizaremos para la gestión contable, fiscal y documental, y para atender justificantes, incidencias o devoluciones.</p><p>No prometemos que la aportación permita aplicar una deducción fiscal. Esa posibilidad queda pendiente de asesoría y de confirmar los requisitos legales de la asociación.</p><p>Consulta la <a href="/es/politica-de-privacidad/">política de privacidad</a> o escribe a <a href="mailto:{$email}">{$email}</a>.</p></div></div></section>
HTML;

$supportEu = <<<HTML
<header class="page-hero page-hero--support content-band--light"><div class="support-wordmark" aria-hidden="true">BABESTU</div><div class="content-wrap"><span class="content-label content-label--campaign">Babes kolektiboa</span><h1>Lagundu eta ekarpenak</h1><p>Lagundu Kermanen memoria gordetzen eta gure lan kolektiboa sostengatzen.</p></div></header>
<section class="content-band content-band--soft"><div class="content-wrap split-grid"><div><span class="content-label">Banku-transferentzia</span><h2 class="section-heading">Egin zure ekarpena zuzenean.</h2></div><div class="reading-copy"><div class="bank-preview"><dl><div><dt>Titularra</dt><dd>Egia Kermanentzat Elkartea</dd></div><div><dt>IBAN</dt><dd><code>{$ibanDisplay}</code></dd></div><div><dt>BIC / SWIFT</dt><dd><code>{$bic}</code></dd></div><div><dt>Gomendatutako kontzeptua</dt><dd><code>{$conceptEu}</code></dd></div></dl><div class="bank-actions"><button class="button button--primary" type="button" data-copy-value="{$iban}" data-feedback-target="#copy-bank-eu" data-success-message="IBANa kopiatu da." data-analytics-event="copy_iban">Kopiatu IBANa</button><button class="button" type="button" data-copy-value="{$copyBankEu}" data-feedback-target="#copy-bank-eu" data-success-message="Transferentziaren datuak kopiatu dira." data-analytics-event="copy_bank_details">Kopiatu datu guztiak</button></div><p class="copy-feedback" id="copy-bank-eu" role="status" aria-live="polite"></p></div><p class="bank-note">Egiaztagiria behar baduzu edo gorabehera nahiz itzulketa bat kudeatzeko, idatzi <a href="mailto:{$email}">{$email}</a> helbidera. Kontsultatu ekarpenak zerga-kenkaria sortzen duela ondorioztatu aurretik.</p></div></div></section>
<section class="content-band content-band--dark"><div class="content-wrap split-grid"><div><span class="content-label">Babes-pankartak</span><h2 class="section-heading">Lagundu zure leihotik edo balkoitik.</h2></div><div class="reading-copy"><p class="lead">Kermanen aldeko pankarta bat eskuratu nahi baduzu zure leihorako edo balkoirako jarri harremanetan gurekin: <a href="mailto:{$email}">{$email}</a></p></div></div></section>
<section class="content-band content-band--soft"><div class="content-wrap split-grid"><div><span class="content-label">Aurkezpen-hitzaldiak</span><h2 class="section-heading">Kasuari buruzko aurkezpen-hitzaldia.</h2></div><div class="reading-copy"><p class="lead">Kasuari buruzko aurkezpen-hitzaldi bat eskatu nahi baduzu, idatzi hona: <a href="mailto:{$email}">{$email}</a>.</p></div></div></section>
<section id="erasoei-buruzko-informazioa" class="content-band content-band--dark"><div class="content-wrap split-grid"><div><span class="content-label">Informazioa eta babesa</span><h2 class="section-heading">Ez zaude bakarrik.</h2></div><div class="reading-copy"><p class="lead">Mitikaren inguruan eraso baten biktima izan zara edo halako kasuren bat ezagutzen duzu?</p><p>Idatzi helbide honetara: <a href="mailto:{$email}">{$email}</a>.</p><p>Zure nortasuna eta ematen diguzun informazioa diskrezioz eta errespetuz tratatuko ditugu. Ez bidali dokumentazio sentikorrik edo beharrezkoa ez den datu pertsonalik. Informazio delikatua partekatu behar baduzu, idatzi lehenik kanal egoki bat adosteko.</p></div></div></section>
<section class="content-band content-band--light"><div class="content-wrap support-grid"><div class="support-callout">Zure babesak <span>eusten dio</span></div><div class="reading-copy"><h2 class="section-heading">Zertarako erabiltzen dira ekarpenak</h2><p>Ekarpenek aukera ematen digute kasuaren dokumentazioa ikertu, antolatu eta gordetzeko; Kermanen memoria eta egiaztatutako eguneraketak zabaltzeko; jarduera juridikoak, informatiboak, sozialak, sentsibilizaziokoak eta prebentziokoak bultzatzeko; gastu tekniko, administratibo eta komunikaziokoak artatzeko; eta gure parte-hartze eta laguntza kanalak mantentzeko.</p><p>Hori da funtsen xede orokorra; ez dugu ehunekoen araberako esleipen itxirik ezartzen.</p><p class="lead"><strong>Bazkide izan nahi duzu?</strong> Idatzi <a href="mailto:{$email}">{$email}</a> helbidera «Bazkide alta» gaiarekin. Beharrezko informazioa bidaliko dizugu, baita alta egiteko datuak modu egokian nola helarazi ere. Lehen mezuan, adierazi zure interesa bakarrik.</p></div></div></section>
<section class="content-band content-band--dark"><div class="content-wrap split-grid"><div><span class="content-label">Pribatutasuna eta gardentasuna</span><h2 class="section-heading">Zure ekarpenari buruzko informazioa</h2></div><div class="reading-copy"><p>Hartzailea Egia Kermanentzat Elkartea da. Transferentzia orri honetan deskribatutako helburu orokorretara bideratzen da. Bankuak eragiketari lotutako datuak jakinaraziko dizkigu; kontabilitate-, zerga- eta dokumentazio-kudeaketarako eta egiaztagiriak, gorabeherak edo itzulketak artatzeko erabiliko ditugu.</p><p>Ez dugu agintzen ekarpenak zerga-kenkaria aplikatzeko aukera emango duenik. Aukera hori aholkularitzaren eta elkarteak lege-baldintzak betetzen dituela egiaztatzearen zain dago.</p><p>Ikusi <a href="/pribatutasun-politika/">pribatutasun-politika</a> edo idatzi <a href="mailto:{$email}">{$email}</a> helbidera.</p></div></div></section>
HTML;

$updatesEs = <<<HTML
<header class="page-hero page-hero--updates content-band--dark">
  <div class="updates-wordmark" aria-hidden="true">ACTUALIDAD</div>
  <div class="content-wrap">
    <p class="updates-status">Sección en construcción</p>
    <h1>Actualidad</h1>
    <p>Estamos construyendo este espacio poco a poco para compartir noticias, comunicados y novedades. Ya puedes consultar las primeras publicaciones.</p>
    <div class="hero-actions">
      <a class="button button--primary" href="{$instagram}" target="_blank" rel="noopener noreferrer">Seguir la actividad en Instagram</a>
      <a class="button button--inverse" href="/es/contacto/">Contactar</a>
    </div>
  </div>
</header>
<section class="content-band content-band--light updates-feed" aria-labelledby="updates-heading-es">
  <div class="content-wrap">
    <div class="updates-feed__intro">
      <h2 class="section-heading" id="updates-heading-es">En los medios</h2>
      <p>Coberturas periodísticas sobre Kerman y la asociación.</p>
    </div>
    <article class="updates-entry">
      <div class="updates-entry__meta">
        <span class="updates-entry__nature">Cobertura periodística</span>
        <span>ORAIN · Radio Euskadi</span>
        <time datetime="2026-08-02">2 de agosto de 2026</time>
      </div>
      <div class="updates-entry__body">
        <h3>Nuevos testimonios apuntan a agresiones reiteradas de porteros de Mítika antes de la muerte de Kerman Villate</h3>
        <p>ORAIN recoge cuatro testimonios sobre presuntas agresiones ocurridas en los meses anteriores a la muerte de Kerman e informa de que los casos fueron denunciados ante la Ertzaintza.</p>
        <a class="updates-entry__link" href="{$orainEs}" target="_blank" rel="noopener noreferrer">Leer la noticia en ORAIN <span aria-hidden="true">↗</span><span class="screen-reader-text"> (se abre en una pestaña nueva)</span></a>
      </div>
    </article>
  </div>
</section>
HTML;

$updatesEu = <<<HTML
<header class="page-hero page-hero--updates content-band--dark">
  <div class="updates-wordmark" aria-hidden="true">BERRIAK</div>
  <div class="content-wrap">
    <p class="updates-status">Atala eraikitzen</p>
    <h1>Berriak</h1>
    <p>Gune hau pixkanaka osatzen ari gara, albisteak, komunikatuak eta berritasunak partekatzeko. Dagoeneko lehen argitalpenak kontsulta ditzakezu.</p>
    <div class="hero-actions">
      <a class="button button--primary" href="{$instagram}" target="_blank" rel="noopener noreferrer">Jarraitu Instagramen</a>
      <a class="button button--inverse" href="/kontaktua/">Jarri harremanetan</a>
    </div>
  </div>
</header>
<section class="content-band content-band--light updates-feed" aria-labelledby="updates-heading-eu">
  <div class="content-wrap">
    <div class="updates-feed__intro">
      <h2 class="section-heading" id="updates-heading-eu">Hedabideetan</h2>
      <p>Kermani eta elkarteari buruzko kazetaritza-estaldurak.</p>
    </div>
    <article class="updates-entry">
      <div class="updates-entry__meta">
        <span class="updates-entry__nature">Kazetaritza-estaldura</span>
        <span>ORAIN · Radio Euskadi</span>
        <time datetime="2026-08-02">2026ko abuztuaren 2a</time>
      </div>
      <div class="updates-entry__body">
        <h3>Testigantza berriek agerian utzi dituzte Mitikako zaindariek Kerman Villate hil aurretik behin eta berriz egindako erasoak</h3>
        <p>ORAINek Kerman hil aurreko hilabeteetan Mitikako atezainek egindako ustezko erasoei buruzko lau testigantza jaso ditu, eta kasuak Ertzaintzaren aurrean salatu zituztela adierazi du.</p>
        <a class="updates-entry__link" href="{$orainEu}" target="_blank" rel="noopener noreferrer">Irakurri albistea ORAINen <span aria-hidden="true">↗</span><span class="screen-reader-text"> (fitxa berri batean irekiko da)</span></a>
      </div>
    </article>
  </div>
</section>
HTML;

$contactEs = <<<HTML
<header class="page-hero page-hero--contact content-band--light"><div class="contact-wordmark" aria-hidden="true"><div><span>HABLEMOS · CONTACTO · COLABORA · </span><span>HABLEMOS · CONTACTO · COLABORA · </span></div></div><div class="content-wrap"><span class="content-label">Contacto directo</span><h1>Hablemos</h1><p>Para colaborar, aportar información, solicitar declaraciones o contactar como medio de comunicación.</p></div></header>
<section class="content-band content-band--soft"><div class="content-wrap contact-grid"><div><h2 class="section-heading">Estamos al otro lado.</h2><p>Indica en el asunto si se trata de prensa, colaboración, documentación, donaciones o administración.</p></div><div><div class="contact-method"><h2>Correo electrónico</h2><div class="contact-email"><a href="mailto:{$email}">{$email}</a></div><button class="button" type="button" data-copy-value="{$email}" data-feedback-target="#copy-es" data-success-message="Correo copiado.">Copiar correo</button><p class="copy-feedback" id="copy-es" role="status" aria-live="polite"></p></div><div class="contact-method"><h2>Información sobre agresiones</h2><p>Si has sufrido una agresión en el entorno de Mitika o conoces algún caso, puedes escribirnos. Consulta <a href="/es/ayuda-y-donaciones/#informacion-agresiones">cómo compartir la información con cuidado</a>.</p></div><div class="contact-method"><h2>Privacidad</h2><p>No envíes documentación sensible ni datos personales innecesarios. Si necesitas compartir documentación delicada, escribe primero para acordar un canal adecuado.</p></div><div class="contact-method"><h2>Instagram</h2><p><a href="{$instagram}" target="_blank" rel="noopener noreferrer">@justizia.kermanentzat</a></p></div></div></div></section>
HTML;

$contactEu = <<<HTML
<header class="page-hero page-hero--contact content-band--light"><div class="contact-wordmark" aria-hidden="true"><div><span>HITZ EGIN DEZAGUN · HARREMANA · LAGUNDU · </span><span>HITZ EGIN DEZAGUN · HARREMANA · LAGUNDU · </span></div></div><div class="content-wrap"><span class="content-label">Harreman zuzena</span><h1>Hitz egin dezagun</h1><p>Laguntzeko, informazioa emateko, adierazpenak eskatzeko edo hedabide gisa harremanetan jartzeko.</p></div></header>
<section class="content-band content-band--soft"><div class="content-wrap contact-grid"><div><h2 class="section-heading">Beste aldean gaude.</h2><p>Adierazi gaian prentsa, lankidetza, dokumentazioa, ekarpenak edo administrazioa den.</p></div><div><div class="contact-method"><h2>Posta elektronikoa</h2><div class="contact-email"><a href="mailto:{$email}">{$email}</a></div><button class="button" type="button" data-copy-value="{$email}" data-feedback-target="#copy-eu" data-success-message="Helbidea kopiatu da.">Helbidea kopiatu</button><p class="copy-feedback" id="copy-eu" role="status" aria-live="polite"></p></div><div class="contact-method"><h2>Erasoei buruzko informazioa</h2><p>Mitikaren inguruan eraso bat jasan baduzu edo halako kasuren bat ezagutzen baduzu, idatz diezagukezu. Ikusi <a href="/lagundu-eta-ekarpenak/#erasoei-buruzko-informazioa">informazioa arretaz nola partekatu</a>.</p></div><div class="contact-method"><h2>Pribatutasuna</h2><p>Ez bidali dokumentazio sentikorrik edo beharrezkoa ez den datu pertsonalik. Dokumentazio delikatua partekatu behar baduzu, idatzi lehenik kanal egoki bat adosteko.</p></div><div class="contact-method"><h2>Instagram</h2><p><a href="{$instagram}" target="_blank" rel="noopener noreferrer">@justizia.kermanentzat</a></p></div></div></div></section>
HTML;

$legacyEuHome = get_page_by_path('eu');
if ($legacyEuHome) {
    wp_update_post(['ID' => $legacyEuHome->ID, 'post_name' => 'hasiera']);
}

$es = kermanentzat_seed_page('Inicio', 'es', $homeEs);
$eu = kermanentzat_seed_page('Hasiera', 'hasiera', $homeEu);
$legalPages = kermanentzat_legal_pages();
kermanentzat_seed_page('Resumen del caso', 'resumen-del-caso', $caseEs, $es);
kermanentzat_seed_page('Actualidad', 'actualidad', $updatesEs, $es);
kermanentzat_seed_page('Ayuda y donaciones', 'ayuda-y-donaciones', $supportEs, $es);
kermanentzat_seed_page('Contacto', 'contacto', $contactEs, $es);
kermanentzat_seed_page('Aviso legal', 'aviso-legal', $legalPages['es']['legal'], $es);
kermanentzat_seed_page('Política de privacidad', 'politica-de-privacidad', $legalPages['es']['privacy'], $es);
kermanentzat_seed_page('Política de cookies', 'politica-de-cookies', $legalPages['es']['cookies'], $es);
kermanentzat_seed_page('Kasuaren laburpena', 'kasuaren-laburpena', $caseEu);
kermanentzat_seed_page('Berriak', 'berriak', $updatesEu);
kermanentzat_seed_page('Lagundu eta ekarpenak', 'lagundu-eta-ekarpenak', $supportEu);
kermanentzat_seed_page('Kontaktua', 'kontaktua', $contactEu);
kermanentzat_seed_page('Lege-oharra', 'lege-oharra', $legalPages['eu']['legal']);
$privacyEu = kermanentzat_seed_page('Pribatutasun-politika', 'pribatutasun-politika', $legalPages['eu']['privacy']);
kermanentzat_seed_page('Cookie-politika', 'cookie-politika', $legalPages['eu']['cookies']);

update_option('blogname', 'Egia Kermanentzat Elkartea');
update_option('blogdescription', 'Memoria, egia eta justizia');
update_option('blog_public', wp_get_environment_type() === 'production' ? '1' : '0');
update_option('show_on_front', 'page');
update_option('page_on_front', $eu);
update_option('page_for_posts', 0);
update_option('wp_page_for_privacy_policy', $privacyEu);
update_option('timezone_string', 'Europe/Madrid');
update_option('date_format', 'd/m/Y');
update_option('time_format', 'H:i');
update_option('default_comment_status', 'closed');
update_option('default_ping_status', 'closed');

foreach (['hello-world', 'sample-page', 'privacy-policy'] as $defaultSlug) {
    $default = get_page_by_path($defaultSlug, OBJECT, ['post', 'page']);
    if ($default) {
        wp_delete_post($default->ID, true);
    }
}

if (class_exists('WP_CLI')) {
    WP_CLI::success('Contenido bilingüe del MVP creado o actualizado.');
}
