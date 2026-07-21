<?php

defined('ABSPATH') || exit;

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

    $id = wp_insert_post(wp_slash([
        'ID' => $existing ? $existing->ID : 0,
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

$image = esc_url(get_theme_file_uri('assets/images/kerman-portrait-clean.png'));
$email = 'justiziakermanentzat@gmail.com';
$instagram = 'https://www.instagram.com/justizia.kermanentzat/';
$iban = 'ES0830350079270790062136';
$ibanDisplay = 'ES08 3035 0079 2707 9006 2136';
$bic = 'CLPEES2MXXX';
$conceptEs = 'APORTACION KERMANENTZAT';
$conceptEu = 'EKARPENA KERMANENTZAT';
$copyBankEs = esc_attr("Titular: Egia Kermanentzat Elkartea\nIBAN: {$ibanDisplay}\nBIC: {$bic}\nConcepto: {$conceptEs}");
$copyBankEu = esc_attr("Titularra: Egia Kermanentzat Elkartea\nIBAN: {$ibanDisplay}\nBIC: {$bic}\nKontzeptua: {$conceptEu}");

$homeEs = <<<HTML
<section class="hero-shell hero-panel" aria-labelledby="memory-title-es">
  <div class="hero-media"><img src="{$image}" width="719" height="762" fetchpriority="high" alt="Retrato gráfico en blanco y negro de Kerman"></div>
  <div class="hero-copy">
    <p class="hero-kicker">Memoria · verdad · justicia</p>
    <h1 class="hero-title" id="memory-title-es">Kerman</h1>
    <p class="hero-statement">Una vida interrumpida. Una familia y una comunidad rotas.</p>
    <div class="hero-actions"><a class="button button--primary" href="/es/resumen-del-caso/">Conocer el resumen</a><a class="button" href="/es/ayuda-y-donaciones/">Ayudar y apoyar</a></div>
  </div>
</section>
<section class="content-band content-band--dark"><div class="content-wrap campaign-grid">
  <div data-reveal><h2 class="campaign-heading"><span>Kerman no murió,</span><strong>lo mataron</strong></h2></div>
  <div class="campaign-copy" data-reveal><p class="lead">El 23 de febrero de 2025, Kerman Villate Beitia murió tras recibir un puñetazo de un portero de la discoteca Mitika, en Vitoria-Gasteiz.</p><p>Una vida interrumpida, una familia destruida y una comunidad rota.</p><p>Abrimos este espacio para honrar su memoria, pedir justicia y reclamar verdad y reparación.</p><a class="button button--inverse" href="/es/resumen-del-caso/">Conocer lo sucedido</a></div>
</div></section>
<section class="content-band content-band--light"><div class="content-wrap split-grid">
  <div data-reveal><span class="content-label">La iniciativa</span><h2 class="section-heading">Memoria, información rigurosa y apoyo colectivo.</h2></div>
  <div class="reading-copy" data-reveal><div class="evidence-list"><div class="evidence-row"><strong>Comprender</strong><p>Un resumen claro de lo sucedido y de la evolución judicial conocida.</p></div><div class="evidence-row"><strong>Acompañar</strong><p>Formas directas de apoyarnos, contactar y participar.</p></div><div class="evidence-row"><strong>Informar</strong><p>Actualizaciones contrastadas para explicar la evolución del caso.</p></div></div></div>
</div></section>
<section class="content-band content-band--soft"><div class="content-wrap support-grid">
  <div class="support-callout" data-reveal>Ayuda y <span>apoya</span></div>
  <div class="reading-copy" data-reveal><p class="lead">La verdad y la justicia necesitan compromiso colectivo.</p><p>Tu aportación nos ayuda a preservar la memoria de Kerman y a sostener nuestro trabajo social, jurídico y comunicativo.</p><a class="button button--primary" href="/es/ayuda-y-donaciones/">Cómo ayudar</a></div>
</div></section>
HTML;

$homeEu = <<<HTML
<section class="hero-shell hero-panel" aria-labelledby="memory-title-eu">
  <div class="hero-media"><img src="{$image}" width="719" height="762" fetchpriority="high" alt="Kermanen zuri-beltzeko erretratu grafikoa"></div>
  <div class="hero-copy">
    <p class="hero-kicker">Memoria · egia · justizia</p>
    <h1 class="hero-title" id="memory-title-eu">Kerman</h1>
    <p class="hero-statement">Bizi bat etenda. Familia bat eta lagunarte bat apurtuta.</p>
    <div class="hero-actions"><a class="button button--primary" href="/kasuaren-laburpena/">Laburpena ezagutu</a><a class="button" href="/lagundu-eta-ekarpenak/">Lagundu</a></div>
  </div>
</section>
<section class="content-band content-band--dark"><div class="content-wrap campaign-grid">
  <div data-reveal><h2 class="campaign-heading"><span>Kerman ez zen hil,</span><strong>hil egin zuten</strong></h2></div>
  <div class="campaign-copy" data-reveal><p class="lead">2025eko otsailaren 23an, Kerman Villate Beitia hil zen Gasteizko Mitika dantzalekuko atezain baten ukabilkada jaso ondoren.</p><p>Bizi bat etenda, familia bat suntsituta, lagunarte bat apurtuta.</p><p>Espazio hau ireki dugu haren memoria ohoratzeko, justizia eskatzeko eta egia eta erreparazioa aldarrikatzeko.</p><a class="button button--inverse" href="/kasuaren-laburpena/">Gertatutakoa ezagutu</a></div>
</div></section>
<section class="content-band content-band--light"><div class="content-wrap split-grid">
  <div data-reveal><span class="content-label">Ekimena</span><h2 class="section-heading">Memoria, informazio zorrotza eta babes kolektiboa.</h2></div>
  <div class="reading-copy" data-reveal><div class="evidence-list"><div class="evidence-row"><strong>Ulertu</strong><p>Gertatutakoaren eta ezagutzen den bilakaera judizialaren laburpen argia.</p></div><div class="evidence-row"><strong>Babestu</strong><p>Gu babesteko, gurekin harremanetan jartzeko eta parte hartzeko bide zuzenak.</p></div><div class="evidence-row"><strong>Informatu</strong><p>Kasuaren bilakaera azaltzeko egiaztatutako eguneraketak.</p></div></div></div>
</div></section>
<section class="content-band content-band--soft"><div class="content-wrap support-grid">
  <div class="support-callout" data-reveal>Lagundu eta <span>babestu</span></div>
  <div class="reading-copy" data-reveal><p class="lead">Egiak eta justiziak konpromiso kolektiboa behar dute.</p><p>Zure ekarpenak Kermanen memoria gordetzen eta gure lan sozial, juridiko eta komunikatiboa sostengatzen laguntzen digu.</p><a class="button button--primary" href="/lagundu-eta-ekarpenak/">Nola lagundu</a></div>
</div></section>
HTML;

$caseEs = <<<HTML
<header class="page-hero page-hero--case content-band--light"><div class="content-wrap"><h1>Resumen del caso</h1><p>Lo sucedido el 23 de febrero de 2025 y su recorrido judicial.</p></div></header>
<section class="content-band content-band--dark"><div class="content-wrap split-grid"><div><h2 class="campaign-heading"><span>Kerman no murió,</span><strong>lo mataron</strong></h2></div><div class="reading-copy"><p class="lead">Kerman Villate Beitia tenía 31 años. En la madrugada del 23 de febrero de 2025, un portero de la discoteca Mitika de Vitoria-Gasteiz le propinó un puñetazo. Kerman murió como consecuencia del golpe.</p><p>Quienes queríamos a Kerman reclamamos que su muerte se investigue y se juzgue con todas las garantías, y que se depuren las responsabilidades que correspondan.</p></div></div></section>
<section class="content-band content-band--light"><div class="content-wrap split-grid"><div><h2 class="section-heading">El recorrido judicial</h2></div><div class="reading-copy"><div class="evidence-list">
  <div class="evidence-row"><strong>Febrero de 2025</strong><p>El Juzgado de Instrucción número 3 de Vitoria-Gasteiz acordó la prisión provisional del portero investigado.</p></div>
  <div class="evidence-row"><strong>Octubre de 2025</strong><p>El juez instructor apreció indicios para que los hechos fueran juzgados por un jurado como homicidio o asesinato. Aquella resolución no era firme.</p></div>
  <div class="evidence-row"><strong>Noviembre de 2025</strong><p>La Audiencia Provincial de Álava revocó ese criterio, ordenó continuar por el procedimiento ordinario y acordó la libertad provisional bajo fianza. Consideró que los hechos encajaban en lesiones y homicidio imprudente.</p></div>
  <div class="evidence-row"><strong>Abril de 2026</strong><p>El Tribunal Supremo inadmitió el recurso de la familia al considerar que la resolución de la Audiencia no podía recurrirse en casación. El Supremo no entró a analizar el fondo de los hechos.</p></div>
</div></div></div></section>
<section class="content-band content-band--soft"><div class="content-wrap split-grid"><div><h2 class="section-heading">Por qué seguimos</h2></div><div class="reading-copy"><p class="lead">Creamos Egia Kermanentzat Elkartea para esclarecer y dar a conocer lo ocurrido, preservar la memoria de Kerman y reclamar justicia y reparación.</p><p>Consideramos insuficiente la respuesta judicial e institucional recibida y denunciamos deficiencias en la investigación y en la valoración del caso. Trabajamos para que estas cuestiones se conozcan, se revisen y reciban una respuesta.</p></div></div></section>
HTML;

$caseEu = <<<HTML
<header class="page-hero page-hero--case content-band--light"><div class="content-wrap"><h1>Kasuaren laburpena</h1><p>2025eko otsailaren 23an gertatutakoa eta haren ibilbide judiziala.</p></div></header>
<section class="content-band content-band--dark"><div class="content-wrap split-grid"><div><h2 class="campaign-heading"><span>Kerman ez zen hil,</span><strong>hil egin zuten</strong></h2></div><div class="reading-copy"><p class="lead">Kerman Villate Beitiak 31 urte zituen. 2025eko otsailaren 23ko goizaldean, Gasteizko Mitika dantzalekuko atezain batek ukabilkada bat eman zion. Kerman kolpearen ondorioz hil zen.</p><p>Kerman maite genuenok haren heriotza berme guztiekin ikertu eta epaitzea eskatzen dugu, eta dagozkion erantzukizunak argitzea.</p></div></div></section>
<section class="content-band content-band--light"><div class="content-wrap split-grid"><div><h2 class="section-heading">Ibilbide judiziala</h2></div><div class="reading-copy"><div class="evidence-list">
  <div class="evidence-row"><strong>2025eko otsaila</strong><p>Gasteizko 3. Instrukzio Epaitegiak ikertutako atezaina behin-behinean espetxeratzea erabaki zuen.</p></div>
  <div class="evidence-row"><strong>2025eko urria</strong><p>Instrukzio epaileak zantzuak ikusi zituen gertakariak herri epaimahai batek homizidio edo hilketa gisa epaitzeko. Ebazpena ez zen irmoa.</p></div>
  <div class="evidence-row"><strong>2025eko azaroa</strong><p>Arabako Probintzia Auzitegiak irizpide hori baliogabetu, prozedura arruntetik jarraitzea agindu eta behin-behineko askatasuna ezarri zuen fidantzapean. Gertakariak lesio eta zuhurtziagabekeriazko homizidio gisa kokatu zituen.</p></div>
  <div class="evidence-row"><strong>2026ko apirila</strong><p>Espainiako Auzitegi Gorenak familiaren helegitea ez zuen onartu, Auzitegiaren ebazpena kasazioan errekurritu ezin zela iritzita. Gorenak ez zuen gertakarien funtsa aztertu.</p></div>
</div></div></div></section>
<section class="content-band content-band--soft"><div class="content-wrap split-grid"><div><h2 class="section-heading">Zergatik jarraitzen dugu</h2></div><div class="reading-copy"><p class="lead">Egia Kermanentzat Elkartea sortu dugu gertatutakoa argitu eta gizarteratzeko, Kermanen memoria gordetzeko eta justizia eta erreparazioa eskatzeko.</p><p>Jasotako erantzun judizial eta instituzionala nahikoa ez dela uste dugu, eta ikerketan eta kasuaren balorazioan gabeziak egon direla salatzen dugu. Gai horiek ezagutarazi, berrikusi eta erantzun bat jaso dezaten lan egiten dugu.</p></div></div></section>
HTML;

$supportEs = <<<HTML
<header class="page-hero page-hero--support content-band--light"><div class="support-wordmark" aria-hidden="true">APOYA</div><div class="content-wrap"><span class="content-label content-label--campaign">Apoyo colectivo</span><h1>Ayuda y donaciones</h1><p>Ayúdanos a preservar la memoria de Kerman y a sostener nuestro trabajo colectivo.</p></div></header>
<section class="content-band content-band--soft"><div class="content-wrap split-grid"><div><span class="content-label">Transferencia bancaria</span><h2 class="section-heading">Haz tu aportación directamente.</h2></div><div class="reading-copy"><div class="bank-preview"><dl><div><dt>Titular</dt><dd>Egia Kermanentzat Elkartea</dd></div><div><dt>IBAN</dt><dd><code>{$ibanDisplay}</code></dd></div><div><dt>BIC / SWIFT</dt><dd><code>{$bic}</code></dd></div><div><dt>Concepto recomendado</dt><dd><code>{$conceptEs}</code></dd></div></dl><div class="bank-actions"><button class="button button--primary" type="button" data-copy-value="{$iban}" data-feedback-target="#copy-bank-es" data-success-message="IBAN copiado.">Copiar IBAN</button><button class="button" type="button" data-copy-value="{$copyBankEs}" data-feedback-target="#copy-bank-es" data-success-message="Datos de transferencia copiados.">Copiar todos los datos</button></div><p class="copy-feedback" id="copy-bank-es" role="status" aria-live="polite"></p></div><p class="bank-note">Si necesitas un justificante o gestionar una incidencia o devolución, escribe a <a href="mailto:{$email}">{$email}</a>. Consulta antes de asumir que la aportación genera una deducción fiscal.</p></div></div></section>
<section class="content-band content-band--light"><div class="content-wrap support-grid"><div class="support-callout">Tu apoyo <span>sostiene</span></div><div class="reading-copy"><h2 class="section-heading">Para qué se utilizan las aportaciones</h2><p>Las aportaciones nos permiten preservar y difundir la memoria de Kerman; dar a conocer el caso y sus actualizaciones; impulsar acciones informativas, sociales y de sensibilización; atender gastos jurídicos, técnicos, administrativos y de comunicación vinculados a nuestra actividad; y mantener nuestros canales de participación y apoyo.</p><p>Este es el destino general de los fondos; no establecemos una asignación porcentual cerrada.</p></div></div></section>
HTML;

$supportEu = <<<HTML
<header class="page-hero page-hero--support content-band--light"><div class="support-wordmark" aria-hidden="true">BABESTU</div><div class="content-wrap"><span class="content-label content-label--campaign">Babes kolektiboa</span><h1>Lagundu eta ekarpenak</h1><p>Lagundu Kermanen memoria gordetzen eta gure lan kolektiboa sostengatzen.</p></div></header>
<section class="content-band content-band--soft"><div class="content-wrap split-grid"><div><span class="content-label">Banku-transferentzia</span><h2 class="section-heading">Egin zure ekarpena zuzenean.</h2></div><div class="reading-copy"><div class="bank-preview"><dl><div><dt>Titularra</dt><dd>Egia Kermanentzat Elkartea</dd></div><div><dt>IBAN</dt><dd><code>{$ibanDisplay}</code></dd></div><div><dt>BIC / SWIFT</dt><dd><code>{$bic}</code></dd></div><div><dt>Gomendatutako kontzeptua</dt><dd><code>{$conceptEu}</code></dd></div></dl><div class="bank-actions"><button class="button button--primary" type="button" data-copy-value="{$iban}" data-feedback-target="#copy-bank-eu" data-success-message="IBANa kopiatu da.">Kopiatu IBANa</button><button class="button" type="button" data-copy-value="{$copyBankEu}" data-feedback-target="#copy-bank-eu" data-success-message="Transferentziaren datuak kopiatu dira.">Kopiatu datu guztiak</button></div><p class="copy-feedback" id="copy-bank-eu" role="status" aria-live="polite"></p></div><p class="bank-note">Egiaztagiria behar baduzu edo gorabehera nahiz itzulketa bat kudeatzeko, idatzi <a href="mailto:{$email}">{$email}</a> helbidera. Kontsultatu ekarpenak zerga-kenkaria sortzen duela ondorioztatu aurretik.</p></div></div></section>
<section class="content-band content-band--light"><div class="content-wrap support-grid"><div class="support-callout">Zure babesak <span>eusten dio</span></div><div class="reading-copy"><h2 class="section-heading">Zertarako erabiltzen dira ekarpenak</h2><p>Ekarpenek aukera ematen digute Kermanen memoria gorde eta zabaltzeko; kasua eta haren eguneraketak ezagutarazteko; informazio-, gizarte- eta sentsibilizazio-ekintzak bultzatzeko; gure jarduerarekin lotutako gastu juridiko, tekniko, administratibo eta komunikaziokoak artatzeko; eta gure parte-hartze eta laguntza kanalak mantentzeko.</p><p>Hori da funtsen xede orokorra; ez dugu ehunekoen araberako esleipen itxirik ezartzen.</p></div></div></section>
HTML;

$contactEs = <<<HTML
<header class="page-hero page-hero--contact content-band--light"><div class="contact-wordmark" aria-hidden="true"><div><span>HABLEMOS · CONTACTO · COLABORA · </span><span>HABLEMOS · CONTACTO · COLABORA · </span></div></div><div class="content-wrap"><span class="content-label">Contacto directo</span><h1>Hablemos</h1><p>Para colaborar, compartir documentación, solicitar información o contactar como medio de comunicación.</p></div></header>
<section class="content-band content-band--soft"><div class="content-wrap contact-grid"><div><h2 class="section-heading">Estamos al otro lado.</h2><p>Indica en el asunto si se trata de prensa, colaboración, documentación, donaciones o administración.</p></div><div><div class="contact-method"><h2>Correo electrónico</h2><div class="contact-email"><a href="mailto:{$email}">{$email}</a></div><button class="button" type="button" data-copy-value="{$email}" data-feedback-target="#copy-es" data-success-message="Correo copiado.">Copiar correo</button><p class="copy-feedback" id="copy-es" role="status" aria-live="polite"></p></div><div class="contact-method"><h2>Privacidad</h2><p>No envíes documentación sensible ni datos personales innecesarios. Si necesitas compartir documentación delicada, escribe primero para acordar un canal adecuado.</p></div><div class="contact-method"><h2>Instagram</h2><p><a href="{$instagram}" target="_blank" rel="noopener noreferrer">@justizia.kermanentzat</a></p></div></div></div></section>
HTML;

$contactEu = <<<HTML
<header class="page-hero page-hero--contact content-band--light"><div class="contact-wordmark" aria-hidden="true"><div><span>HITZ EGIN DEZAGUN · HARREMANA · LAGUNDU · </span><span>HITZ EGIN DEZAGUN · HARREMANA · LAGUNDU · </span></div></div><div class="content-wrap"><span class="content-label">Harreman zuzena</span><h1>Hitz egin dezagun</h1><p>Laguntzeko, dokumentazioa partekatzeko, informazioa eskatzeko edo hedabide gisa harremanetan jartzeko.</p></div></header>
<section class="content-band content-band--soft"><div class="content-wrap contact-grid"><div><h2 class="section-heading">Beste aldean gaude.</h2><p>Adierazi gaian prentsa, lankidetza, dokumentazioa, ekarpenak edo administrazioa den.</p></div><div><div class="contact-method"><h2>Posta elektronikoa</h2><div class="contact-email"><a href="mailto:{$email}">{$email}</a></div><button class="button" type="button" data-copy-value="{$email}" data-feedback-target="#copy-eu" data-success-message="Helbidea kopiatu da.">Helbidea kopiatu</button><p class="copy-feedback" id="copy-eu" role="status" aria-live="polite"></p></div><div class="contact-method"><h2>Pribatutasuna</h2><p>Ez bidali dokumentazio sentikorrik edo beharrezkoa ez den datu pertsonalik. Dokumentazio delikatua partekatu behar baduzu, idatzi lehenik kanal egoki bat adosteko.</p></div><div class="contact-method"><h2>Instagram</h2><p><a href="{$instagram}" target="_blank" rel="noopener noreferrer">@justizia.kermanentzat</a></p></div></div></div></section>
HTML;

$legacyEuHome = get_page_by_path('eu');
if ($legacyEuHome) {
    wp_update_post(['ID' => $legacyEuHome->ID, 'post_name' => 'hasiera']);
}

$es = kermanentzat_seed_page('Inicio', 'es', $homeEs);
$eu = kermanentzat_seed_page('Hasiera', 'hasiera', $homeEu);
kermanentzat_seed_page('Resumen del caso', 'resumen-del-caso', $caseEs, $es);
kermanentzat_seed_page('Ayuda y donaciones', 'ayuda-y-donaciones', $supportEs, $es);
kermanentzat_seed_page('Contacto', 'contacto', $contactEs, $es);
kermanentzat_seed_page('Kasuaren laburpena', 'kasuaren-laburpena', $caseEu);
kermanentzat_seed_page('Lagundu eta ekarpenak', 'lagundu-eta-ekarpenak', $supportEu);
kermanentzat_seed_page('Kontaktua', 'kontaktua', $contactEu);

update_option('blogname', 'Egia Kermanentzat');
update_option('blogdescription', 'Memoria, egia eta justizia');
update_option('blog_public', '0');
update_option('show_on_front', 'page');
update_option('page_on_front', $eu);
update_option('page_for_posts', 0);
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

WP_CLI::success('Contenido bilingüe del MVP creado o actualizado.');
