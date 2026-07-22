<?php

defined('ABSPATH') || exit;

require_once get_theme_file_path('inc/legal-content.php');

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

$imagePng = esc_url(get_theme_file_uri('assets/images/kerman-portrait-clean.png'));
$imageWebp = esc_url(get_theme_file_uri('assets/images/kerman-portrait-clean.webp'));
$heroPictureEs = '<picture><source srcset="' . $imageWebp . '" type="image/webp"><img src="' . $imagePng . '" width="719" height="762" fetchpriority="high" loading="eager" decoding="async" alt="Retrato gráfico en blanco y negro de Kerman"></picture>';
$heroPictureEu = '<picture><source srcset="' . $imageWebp . '" type="image/webp"><img src="' . $imagePng . '" width="719" height="762" fetchpriority="high" loading="eager" decoding="async" alt="Kermanen zuri-beltzeko erretratu grafikoa"></picture>';
$caseArt = '<div class="page-hero__art" aria-hidden="true"><picture><source srcset="' . $imageWebp . '" type="image/webp"><img src="' . $imagePng . '" width="719" height="762" fetchpriority="high" loading="eager" decoding="async" alt=""></picture></div>';
$legalConfig = kermanentzat_legal_config();
$email = $legalConfig['email'];
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
  <div class="campaign-copy" data-reveal><p class="lead">El 23 de febrero de 2025, Kerman Villate Beitia murió tras recibir un puñetazo de un portero de la discoteca Mitika, en Vitoria-Gasteiz.</p><p>Una vida interrumpida, una familia destruida y una comunidad rota.</p><p>Abrimos este espacio para honrar su memoria, pedir justicia y reclamar verdad y reparación.</p><a class="button button--inverse" href="/es/resumen-del-caso/">Conocer lo sucedido</a></div>
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
  <div class="campaign-copy" data-reveal><p class="lead">2025eko otsailaren 23an, Kerman Villate Beitia hil zen Gasteizko Mitika dantzalekuko atezain baten ukabilkada jaso ondoren.</p><p>Bizi bat etenda, familia bat suntsituta, lagunarte bat apurtuta.</p><p>Espazio hau ireki dugu haren memoria ohoratzeko, justizia eskatzeko eta egia eta erreparazioa aldarrikatzeko.</p><a class="button button--inverse" href="/kasuaren-laburpena/">Gertatutakoa ezagutu</a></div>
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

$caseEs = <<<HTML
<header class="page-hero page-hero--case content-band--light">{$caseArt}<div class="content-wrap"><h1>Resumen del caso</h1><p>Qué ocurrió aquella noche, cómo ha evolucionado el proceso y por qué seguimos reclamando verdad, justicia y reparación.</p></div></header>
<section class="content-band content-band--dark"><div class="content-wrap split-grid"><div><h2 class="campaign-heading"><span>Kerman no murió,</span><strong>lo mataron</strong></h2></div><div class="reading-copy"><p class="lead">Kerman Villate Beitia tenía 31 años. En la madrugada del 23 de febrero de 2025, un portero de la discoteca Mitika de Vitoria-Gasteiz le propinó un puñetazo. Kerman murió como consecuencia del golpe.</p><p>Quienes queríamos a Kerman reclamamos que su muerte se investigue y se juzgue con todas las garantías, y que se depuren las responsabilidades que correspondan.</p></div></div></section>
<section class="content-band content-band--light"><div class="content-wrap split-grid"><div data-reveal><h2 class="section-heading">Qué ocurrió aquella noche</h2></div><div class="reading-copy" data-reveal><p class="lead">Kerman llegó al control de acceso de Mitika con sus amigos. Ellos continuaron hacia el interior, mientras a él le indicaron que permaneciera fuera.</p><p>Al revisar las grabaciones, observamos que Kerman esperó y habló con otras personas con tranquilidad, sin que las imágenes mostraran una confrontación previa. Minutos después se produjo una conversación entre trabajadores del local y unos gestos que consideramos relevantes para comprender lo sucedido.</p><p>Segundos más tarde, un portero se situó junto a Kerman y lo golpeó por sorpresa. Kerman cayó de inmediato. Después del golpe, el portero regresó al interior del local.</p></div></div></section>
<section class="content-band content-band--soft"><div class="content-wrap split-grid"><div data-reveal><h2 class="section-heading">Lo que recogió la instrucción</h2></div><div class="reading-copy" data-reveal><p class="lead">El Juzgado de Instrucción número 3 de Vitoria-Gasteiz acordó la prisión provisional del portero investigado.</p><p>En su resolución del 30 de septiembre de 2025, el juez instructor apreció indicios para que los hechos fueran juzgados por un jurado como homicidio o asesinato. Describió una acción voluntaria y sorpresiva y consideró que debía debatirse en juicio la posible aceptación de un resultado mortal. Aquella resolución no era firme.</p><p>La información forense citada en la instrucción relacionó la muerte con un traumatismo facial causado por el golpe.</p></div></div></section>
<section class="content-band content-band--light"><div class="content-wrap split-grid"><div data-reveal><h2 class="section-heading">El cambio en el recorrido judicial</h2></div><div class="reading-copy" data-reveal><p>El 26 de noviembre de 2025, la Audiencia Provincial de Álava revocó el criterio del instructor, ordenó continuar por el procedimiento ordinario y acordó la libertad provisional bajo una fianza de 6.000 euros. Situó los hechos en el ámbito de las lesiones y el homicidio imprudente.</p><p>La familia recurrió esa decisión. El 30 de abril de 2026 se conoció que el Tribunal Supremo no admitía el recurso por una cuestión procesal. El Supremo no entró a analizar el fondo de los hechos ni decidió si debían calificarse como homicidio o asesinato.</p><p class="lead">Consideramos que este recorrido ha impedido discutir en juicio las calificaciones más graves. Seguimos reclamando un proceso con todas las garantías y una valoración completa de las pruebas.</p></div></div></section>
<section class="content-band content-band--dark"><div class="content-wrap split-grid"><div><h2 class="section-heading">Advertencias y respuesta institucional</h2></div><div class="reading-copy"><p>Antes de la muerte de Kerman ya existían actuaciones y advertencias relacionadas con la violencia ejercida en el acceso al local. Tras lo sucedido, la familia pidió aclarar qué se conocía, qué medidas se habían adoptado y qué responsabilidades administrativas podían existir.</p><p>El 25 de mayo de 2026, el Ararteko recomendó al Ayuntamiento de Vitoria-Gasteiz que respondiera de manera expresa, motivada e íntegra a la denuncia y que reforzara el control preventivo para garantizar el cumplimiento de las condiciones de seguridad en los locales de ocio.</p><p class="lead">Consideramos insuficiente la respuesta institucional recibida. Trabajamos para que se expliquen las advertencias previas, se revisen las actuaciones realizadas y se adopten medidas que eviten que algo similar vuelva a suceder.</p></div></div></section>
<section class="content-band content-band--light"><div class="content-wrap split-grid"><div><h2 class="section-heading">Cronología esencial</h2></div><div class="reading-copy"><div class="evidence-list case-timeline">
  <div class="evidence-row"><strong>Mayo de 2024</strong><p>Constan actuaciones y advertencias previas relacionadas con la violencia en el acceso a Mitika. Su alcance sigue formando parte de las cuestiones que reclamamos esclarecer.</p></div>
  <div class="evidence-row"><strong>23–25 de febrero de 2025</strong><p>Kerman murió el 23 de febrero tras recibir el golpe. Dos días después, el juzgado acordó la prisión provisional del portero investigado.</p></div>
  <div class="evidence-row"><strong>30 de septiembre de 2025</strong><p>El juez instructor apreció indicios para un juicio con jurado por homicidio o asesinato. La resolución se hizo pública a comienzos de octubre y no era firme.</p></div>
  <div class="evidence-row"><strong>26 de noviembre de 2025</strong><p>La Audiencia Provincial cambió el procedimiento y la calificación provisional y acordó la libertad bajo fianza.</p></div>
  <div class="evidence-row"><strong>Enero de 2026</strong><p>La familia recurrió ante el Tribunal Supremo la decisión de la Audiencia Provincial.</p></div>
  <div class="evidence-row"><strong>18 de febrero de 2026</strong><p>Los padres de Kerman comparecieron en el Parlamento Vasco para pedir un juicio con todas las garantías y medidas de prevención.</p></div>
  <div class="evidence-row"><strong>30 de abril de 2026</strong><p>El Tribunal Supremo inadmitió el recurso sin entrar a analizar el fondo del caso.</p></div>
  <div class="evidence-row"><strong>25 de mayo de 2026</strong><p>El Ararteko emitió sus recomendaciones al Ayuntamiento sobre la respuesta a la denuncia y el control preventivo.</p></div>
  <div class="evidence-row"><strong>Junio de 2026</strong><p>Presentamos Egia Kermanentzat Elkartea y compartimos públicamente el relato, el recorrido del caso y nuestras líneas de trabajo.</p></div>
  <div class="evidence-row"><strong>8 de julio de 2026</strong><p>Expusimos ante la Comisión municipal de Seguridad nuestra valoración de la respuesta del Ayuntamiento y del cumplimiento de las recomendaciones.</p></div>
</div></div></div></section>
<section class="content-band content-band--soft"><div class="content-wrap support-grid"><div class="support-callout">Memoria, justicia y <span>prevención</span></div><div class="reading-copy"><h2 class="section-heading">Por qué seguimos</h2><p class="lead">Creamos Egia Kermanentzat Elkartea para esclarecer y dar a conocer lo ocurrido, preservar la memoria de Kerman y reclamar justicia y reparación.</p><p>Trabajamos para que las respuestas judiciales e institucionales se revisen y se expliquen, para que la verdad sea reconocida socialmente y para promover cambios que impidan que una situación semejante vuelva a repetirse.</p></div></div></section>
HTML;

$caseEu = <<<HTML
<header class="page-hero page-hero--case content-band--light">{$caseArt}<div class="content-wrap"><h1>Kasuaren laburpena</h1><p>Gau hartan zer gertatu zen, prozesuak zer bilakaera izan duen eta zergatik jarraitzen dugun egia, justizia eta erreparazioa eskatzen.</p></div></header>
<section class="content-band content-band--dark"><div class="content-wrap split-grid"><div><h2 class="campaign-heading"><span>Kerman ez zen hil,</span><strong>hil egin zuten</strong></h2></div><div class="reading-copy"><p class="lead">Kerman Villate Beitiak 31 urte zituen. 2025eko otsailaren 23ko goizaldean, Gasteizko Mitika dantzalekuko atezain batek ukabilkada bat eman zion. Kerman kolpearen ondorioz hil zen.</p><p>Kerman maite genuenok haren heriotza berme guztiekin ikertu eta epaitzea eskatzen dugu, eta dagozkion erantzukizunak argitzea.</p></div></div></section>
<section class="content-band content-band--light"><div class="content-wrap split-grid"><div data-reveal><h2 class="section-heading">Gau hartan zer gertatu zen</h2></div><div class="reading-copy" data-reveal><p class="lead">Kerman lagunekin iritsi zen Mitikako sarbide-kontrolera. Haiek barrurantz jarraitu zuten; Kermani, berriz, kanpoan geratzeko esan zioten.</p><p>Grabazioak aztertzean, Kerman lasai itxaroten eta beste pertsona batzuekin hitz egiten ikusten dugu, irudietan aurretiko liskarrik agertu gabe. Minutu batzuk geroago, lokaleko langileen arteko elkarrizketa bat eta gertatutakoa ulertzeko garrantzitsutzat jotzen ditugun keinu batzuk izan ziren.</p><p>Segundo batzuk geroago, atezain bat Kermanen alboan jarri eta ustekabean jo zuen. Kerman berehala erori zen. Kolpearen ondoren, atezaina lokal barrura itzuli zen.</p></div></div></section>
<section class="content-band content-band--soft"><div class="content-wrap split-grid"><div data-reveal><h2 class="section-heading">Instrukzioak jaso zuena</h2></div><div class="reading-copy" data-reveal><p class="lead">Gasteizko 3. Instrukzio Epaitegiak ikertutako atezaina behin-behinean espetxeratzea erabaki zuen.</p><p>2025eko irailaren 30eko ebazpenean, instrukzio-epaileak zantzuak ikusi zituen gertakariak herri-epaimahai batek homizidio edo hilketa gisa epaitzeko. Ekintza borondatezkoa eta ustekabekoa izan zela deskribatu zuen, eta heriotza-emaitza onartu izanaren aukera epaiketan eztabaidatu behar zela iritzi zion. Ebazpena ez zen irmoa.</p><p>Instrukzioan aipatutako auzitegi-medikuntzako informazioak heriotza kolpeak eragindako aurpegiko traumatismoarekin lotu zuen.</p></div></div></section>
<section class="content-band content-band--light"><div class="content-wrap split-grid"><div data-reveal><h2 class="section-heading">Ibilbide judizialaren aldaketa</h2></div><div class="reading-copy" data-reveal><p>2025eko azaroaren 26an, Arabako Probintzia Auzitegiak instrukzio-epailearen irizpidea baliogabetu, prozedura arruntetik jarraitzea agindu eta 6.000 euroko fidantzapeko behin-behineko askatasuna erabaki zuen. Gertakariak lesioen eta zuhurtziagabekeriazko homizidioaren esparruan kokatu zituen.</p><p>Familiak erabaki horren aurkako helegitea jarri zuen. 2026ko apirilaren 30ean jakin zen Espainiako Auzitegi Gorenak helegitea ez zuela onartu, prozesu-arrazoi batengatik. Gorenak ez zuen gertakarien funtsa aztertu, eta ez zuen erabaki homizidio edo hilketa gisa kalifikatu behar ziren.</p><p class="lead">Ibilbide horrek kalifikazio larrienak epaiketan eztabaidatzea eragotzi duela uste dugu. Berme guztietako prozesua eta frogen balorazio osoa eskatzen jarraitzen dugu.</p></div></div></section>
<section class="content-band content-band--dark"><div class="content-wrap split-grid"><div><h2 class="section-heading">Ohartarazpenak eta erakundeen erantzuna</h2></div><div class="reading-copy"><p>Kerman hil aurretik, lokaleko sarreran erabilitako indarkeriari lotutako jarduketak eta ohartarazpenak zeuden. Gertatutakoaren ondoren, familiak zer zekiten, zer neurri hartu ziren eta zer administrazio-erantzukizun egon zitezkeen argitzeko eskatu zuen.</p><p>2026ko maiatzaren 25ean, Arartekoak Gasteizko Udalari gomendatu zion salaketari berariaz, arrazoituta eta osorik erantzuteko, eta aisialdiko lokaletan segurtasun-baldintzak betetzen direla bermatzeko prebentzio-kontrola indartzeko.</p><p class="lead">Jasotako erakunde-erantzuna nahikoa ez dela uste dugu. Aurretiko ohartarazpenak azaldu, egindako jarduketak berrikusi eta antzeko zerbait berriro gerta ez dadin neurriak har daitezen lan egiten dugu.</p></div></div></section>
<section class="content-band content-band--light"><div class="content-wrap split-grid"><div><h2 class="section-heading">Funtsezko kronologia</h2></div><div class="reading-copy"><div class="evidence-list case-timeline">
  <div class="evidence-row"><strong>2024ko maiatza</strong><p>Mitikako sarbideko indarkeriari lotutako aurretiko jarduketak eta ohartarazpenak jasota daude. Haien irismena argitzeko eskatzen jarraitzen dugu.</p></div>
  <div class="evidence-row"><strong>2025eko otsailaren 23a–25a</strong><p>Kerman otsailaren 23an hil zen kolpea jaso ondoren. Bi egun geroago, epaitegiak ikertutako atezaina behin-behinean espetxeratzea erabaki zuen.</p></div>
  <div class="evidence-row"><strong>2025eko irailaren 30a</strong><p>Instrukzio-epaileak zantzuak ikusi zituen herri-epaimahai batek homizidio edo hilketa gisa epaitzeko. Ebazpena urriaren hasieran jakinarazi zen, eta ez zen irmoa.</p></div>
  <div class="evidence-row"><strong>2025eko azaroaren 26a</strong><p>Probintzia Auzitegiak prozedura eta behin-behineko kalifikazioa aldatu zituen, eta fidantzapeko askatasuna erabaki zuen.</p></div>
  <div class="evidence-row"><strong>2026ko urtarrila</strong><p>Familiak Probintzia Auzitegiaren erabakiaren aurkako helegitea jarri zuen Espainiako Auzitegi Gorenean.</p></div>
  <div class="evidence-row"><strong>2026ko otsailaren 18a</strong><p>Kermanen gurasoak Eusko Legebiltzarrean agertu ziren, berme guztietako epaiketa eta prebentzio-neurriak eskatzeko.</p></div>
  <div class="evidence-row"><strong>2026ko apirilaren 30a</strong><p>Espainiako Auzitegi Gorenak helegitea ez zuen onartu, auziaren funtsa aztertu gabe.</p></div>
  <div class="evidence-row"><strong>2026ko maiatzaren 25a</strong><p>Arartekoak salaketaren erantzunari eta prebentzio-kontrolari buruzko gomendioak egin zizkion Udalari.</p></div>
  <div class="evidence-row"><strong>2026ko ekaina</strong><p>Egia Kermanentzat Elkartea aurkeztu genuen, eta kasuaren kontakizuna, ibilbidea eta gure lan-ildoak publikoki partekatu genituen.</p></div>
  <div class="evidence-row"><strong>2026ko uztailaren 8a</strong><p>Udaleko Segurtasun Batzordean azaldu genuen Udalaren erantzunari eta gomendioen betetzeari buruzko gure balorazioa.</p></div>
</div></div></div></section>
<section class="content-band content-band--soft"><div class="content-wrap support-grid"><div class="support-callout">Memoria, justizia eta <span>prebentzioa</span></div><div class="reading-copy"><h2 class="section-heading">Zergatik jarraitzen dugu</h2><p class="lead">Egia Kermanentzat Elkartea sortu dugu gertatutakoa argitu eta gizarteratzeko, Kermanen memoria gordetzeko eta justizia eta erreparazioa eskatzeko.</p><p>Erantzun judizial eta instituzionalak berrikusi eta azal daitezen, egia gizartean aitor dadin eta antzeko egoera bat berriro gertatzea eragotziko duten aldaketak sustatzeko lan egiten dugu.</p></div></div></section>
HTML;

$supportEs = <<<HTML
<header class="page-hero page-hero--support content-band--light"><div class="support-wordmark" aria-hidden="true">APOYA</div><div class="content-wrap"><span class="content-label content-label--campaign">Apoyo colectivo</span><h1>Ayuda y donaciones</h1><p>Ayúdanos a preservar la memoria de Kerman y a sostener nuestro trabajo colectivo.</p></div></header>
<section class="content-band content-band--soft"><div class="content-wrap split-grid"><div><span class="content-label">Transferencia bancaria</span><h2 class="section-heading">Haz tu aportación directamente.</h2></div><div class="reading-copy"><div class="bank-preview"><dl><div><dt>Titular</dt><dd>Egia Kermanentzat Elkartea</dd></div><div><dt>IBAN</dt><dd><code>{$ibanDisplay}</code></dd></div><div><dt>BIC / SWIFT</dt><dd><code>{$bic}</code></dd></div><div><dt>Concepto recomendado</dt><dd><code>{$conceptEs}</code></dd></div></dl><div class="bank-actions"><button class="button button--primary" type="button" data-copy-value="{$iban}" data-feedback-target="#copy-bank-es" data-success-message="IBAN copiado.">Copiar IBAN</button><button class="button" type="button" data-copy-value="{$copyBankEs}" data-feedback-target="#copy-bank-es" data-success-message="Datos de transferencia copiados.">Copiar todos los datos</button></div><p class="copy-feedback" id="copy-bank-es" role="status" aria-live="polite"></p></div><p class="bank-note">Si necesitas un justificante o gestionar una incidencia o devolución, escribe a <a href="mailto:{$email}">{$email}</a>. Consulta antes de asumir que la aportación genera una deducción fiscal.</p></div></div></section>
<section class="content-band content-band--light"><div class="content-wrap support-grid"><div class="support-callout">Tu apoyo <span>sostiene</span></div><div class="reading-copy"><h2 class="section-heading">Para qué se utilizan las aportaciones</h2><p>Las aportaciones nos permiten investigar, ordenar y conservar la documentación del caso; difundir la memoria de Kerman y las actualizaciones contrastadas; impulsar actuaciones jurídicas, informativas, sociales, de sensibilización y prevención; atender gastos técnicos, administrativos y de comunicación; y mantener nuestros canales de participación y apoyo.</p><p>Este es el destino general de los fondos; no establecemos una asignación porcentual cerrada.</p></div></div></section>
<section class="content-band content-band--dark"><div class="content-wrap split-grid"><div><span class="content-label">Privacidad y transparencia</span><h2 class="section-heading">Información sobre tu aportación</h2></div><div class="reading-copy"><p>El receptor es Egia Kermanentzat Elkartea. La transferencia se destina a las finalidades generales descritas en esta página. La entidad bancaria nos comunicará los datos asociados a la operación; los utilizaremos para la gestión contable, fiscal y documental, y para atender justificantes, incidencias o devoluciones.</p><p>No prometemos que la aportación permita aplicar una deducción fiscal. Esa posibilidad queda pendiente de asesoría y de confirmar los requisitos legales de la asociación.</p><p>Consulta la <a href="/es/politica-de-privacidad/">política de privacidad</a> o escribe a <a href="mailto:{$email}">{$email}</a>.</p></div></div></section>
HTML;

$supportEu = <<<HTML
<header class="page-hero page-hero--support content-band--light"><div class="support-wordmark" aria-hidden="true">BABESTU</div><div class="content-wrap"><span class="content-label content-label--campaign">Babes kolektiboa</span><h1>Lagundu eta ekarpenak</h1><p>Lagundu Kermanen memoria gordetzen eta gure lan kolektiboa sostengatzen.</p></div></header>
<section class="content-band content-band--soft"><div class="content-wrap split-grid"><div><span class="content-label">Banku-transferentzia</span><h2 class="section-heading">Egin zure ekarpena zuzenean.</h2></div><div class="reading-copy"><div class="bank-preview"><dl><div><dt>Titularra</dt><dd>Egia Kermanentzat Elkartea</dd></div><div><dt>IBAN</dt><dd><code>{$ibanDisplay}</code></dd></div><div><dt>BIC / SWIFT</dt><dd><code>{$bic}</code></dd></div><div><dt>Gomendatutako kontzeptua</dt><dd><code>{$conceptEu}</code></dd></div></dl><div class="bank-actions"><button class="button button--primary" type="button" data-copy-value="{$iban}" data-feedback-target="#copy-bank-eu" data-success-message="IBANa kopiatu da.">Kopiatu IBANa</button><button class="button" type="button" data-copy-value="{$copyBankEu}" data-feedback-target="#copy-bank-eu" data-success-message="Transferentziaren datuak kopiatu dira.">Kopiatu datu guztiak</button></div><p class="copy-feedback" id="copy-bank-eu" role="status" aria-live="polite"></p></div><p class="bank-note">Egiaztagiria behar baduzu edo gorabehera nahiz itzulketa bat kudeatzeko, idatzi <a href="mailto:{$email}">{$email}</a> helbidera. Kontsultatu ekarpenak zerga-kenkaria sortzen duela ondorioztatu aurretik.</p></div></div></section>
<section class="content-band content-band--light"><div class="content-wrap support-grid"><div class="support-callout">Zure babesak <span>eusten dio</span></div><div class="reading-copy"><h2 class="section-heading">Zertarako erabiltzen dira ekarpenak</h2><p>Ekarpenek aukera ematen digute kasuaren dokumentazioa ikertu, antolatu eta gordetzeko; Kermanen memoria eta egiaztatutako eguneraketak zabaltzeko; jarduera juridikoak, informatiboak, sozialak, sentsibilizaziokoak eta prebentziokoak bultzatzeko; gastu tekniko, administratibo eta komunikaziokoak artatzeko; eta gure parte-hartze eta laguntza kanalak mantentzeko.</p><p>Hori da funtsen xede orokorra; ez dugu ehunekoen araberako esleipen itxirik ezartzen.</p></div></div></section>
<section class="content-band content-band--dark"><div class="content-wrap split-grid"><div><span class="content-label">Pribatutasuna eta gardentasuna</span><h2 class="section-heading">Zure ekarpenari buruzko informazioa</h2></div><div class="reading-copy"><p>Hartzailea Egia Kermanentzat Elkartea da. Transferentzia orri honetan deskribatutako helburu orokorretara bideratzen da. Bankuak eragiketari lotutako datuak jakinaraziko dizkigu; kontabilitate-, zerga- eta dokumentazio-kudeaketarako eta egiaztagiriak, gorabeherak edo itzulketak artatzeko erabiliko ditugu.</p><p>Ez dugu agintzen ekarpenak zerga-kenkaria aplikatzeko aukera emango duenik. Aukera hori aholkularitzaren eta elkarteak lege-baldintzak betetzen dituela egiaztatzearen zain dago.</p><p>Ikusi <a href="/pribatutasun-politika/">pribatutasun-politika</a> edo idatzi <a href="mailto:{$email}">{$email}</a> helbidera.</p></div></div></section>
HTML;

$contactEs = <<<HTML
<header class="page-hero page-hero--contact content-band--light"><div class="contact-wordmark" aria-hidden="true"><div><span>HABLEMOS · CONTACTO · COLABORA · </span><span>HABLEMOS · CONTACTO · COLABORA · </span></div></div><div class="content-wrap"><span class="content-label">Contacto directo</span><h1>Hablemos</h1><p>Para colaborar, aportar información, solicitar declaraciones o contactar como medio de comunicación.</p></div></header>
<section class="content-band content-band--soft"><div class="content-wrap contact-grid"><div><h2 class="section-heading">Estamos al otro lado.</h2><p>Indica en el asunto si se trata de prensa, colaboración, documentación, donaciones o administración.</p></div><div><div class="contact-method"><h2>Correo electrónico</h2><div class="contact-email"><a href="mailto:{$email}">{$email}</a></div><button class="button" type="button" data-copy-value="{$email}" data-feedback-target="#copy-es" data-success-message="Correo copiado.">Copiar correo</button><p class="copy-feedback" id="copy-es" role="status" aria-live="polite"></p></div><div class="contact-method"><h2>Privacidad</h2><p>No envíes documentación sensible ni datos personales innecesarios. Si necesitas compartir documentación delicada, escribe primero para acordar un canal adecuado.</p></div><div class="contact-method"><h2>Instagram</h2><p><a href="{$instagram}" target="_blank" rel="noopener noreferrer">@justizia.kermanentzat</a></p></div></div></div></section>
HTML;

$contactEu = <<<HTML
<header class="page-hero page-hero--contact content-band--light"><div class="contact-wordmark" aria-hidden="true"><div><span>HITZ EGIN DEZAGUN · HARREMANA · LAGUNDU · </span><span>HITZ EGIN DEZAGUN · HARREMANA · LAGUNDU · </span></div></div><div class="content-wrap"><span class="content-label">Harreman zuzena</span><h1>Hitz egin dezagun</h1><p>Laguntzeko, informazioa emateko, adierazpenak eskatzeko edo hedabide gisa harremanetan jartzeko.</p></div></header>
<section class="content-band content-band--soft"><div class="content-wrap contact-grid"><div><h2 class="section-heading">Beste aldean gaude.</h2><p>Adierazi gaian prentsa, lankidetza, dokumentazioa, ekarpenak edo administrazioa den.</p></div><div><div class="contact-method"><h2>Posta elektronikoa</h2><div class="contact-email"><a href="mailto:{$email}">{$email}</a></div><button class="button" type="button" data-copy-value="{$email}" data-feedback-target="#copy-eu" data-success-message="Helbidea kopiatu da.">Helbidea kopiatu</button><p class="copy-feedback" id="copy-eu" role="status" aria-live="polite"></p></div><div class="contact-method"><h2>Pribatutasuna</h2><p>Ez bidali dokumentazio sentikorrik edo beharrezkoa ez den datu pertsonalik. Dokumentazio delikatua partekatu behar baduzu, idatzi lehenik kanal egoki bat adosteko.</p></div><div class="contact-method"><h2>Instagram</h2><p><a href="{$instagram}" target="_blank" rel="noopener noreferrer">@justizia.kermanentzat</a></p></div></div></div></section>
HTML;

$legacyEuHome = get_page_by_path('eu');
if ($legacyEuHome) {
    wp_update_post(['ID' => $legacyEuHome->ID, 'post_name' => 'hasiera']);
}

$es = kermanentzat_seed_page('Inicio', 'es', $homeEs);
$eu = kermanentzat_seed_page('Hasiera', 'hasiera', $homeEu);
$legalPages = kermanentzat_legal_pages();
kermanentzat_seed_page('Resumen del caso', 'resumen-del-caso', $caseEs, $es);
kermanentzat_seed_page('Ayuda y donaciones', 'ayuda-y-donaciones', $supportEs, $es);
kermanentzat_seed_page('Contacto', 'contacto', $contactEs, $es);
kermanentzat_seed_page('Aviso legal', 'aviso-legal', $legalPages['es']['legal'], $es);
kermanentzat_seed_page('Política de privacidad', 'politica-de-privacidad', $legalPages['es']['privacy'], $es);
kermanentzat_seed_page('Política de cookies', 'politica-de-cookies', $legalPages['es']['cookies'], $es);
kermanentzat_seed_page('Kasuaren laburpena', 'kasuaren-laburpena', $caseEu);
kermanentzat_seed_page('Lagundu eta ekarpenak', 'lagundu-eta-ekarpenak', $supportEu);
kermanentzat_seed_page('Kontaktua', 'kontaktua', $contactEu);
kermanentzat_seed_page('Lege-oharra', 'lege-oharra', $legalPages['eu']['legal']);
$privacyEu = kermanentzat_seed_page('Pribatutasun-politika', 'pribatutasun-politika', $legalPages['eu']['privacy']);
kermanentzat_seed_page('Cookie-politika', 'cookie-politika', $legalPages['eu']['cookies']);

update_option('blogname', 'Egia Kermanentzat');
update_option('blogdescription', 'Memoria, egia eta justizia');
update_option('blog_public', '0');
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

WP_CLI::success('Contenido bilingüe del MVP creado o actualizado.');
