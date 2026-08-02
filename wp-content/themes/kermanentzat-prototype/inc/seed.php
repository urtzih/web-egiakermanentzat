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

$imagePng = esc_url(wp_make_link_relative(get_theme_file_uri('assets/images/kerman-portrait-clean.png')));
$imageWebp = esc_url(wp_make_link_relative(get_theme_file_uri('assets/images/kerman-portrait-clean.webp')));
$heroPictureEs = '<picture><source srcset="' . $imageWebp . '" type="image/webp"><img src="' . $imagePng . '" width="717" height="762" fetchpriority="high" loading="eager" decoding="async" alt="Retrato gráfico en blanco y negro de Kerman"></picture>';
$heroPictureEu = '<picture><source srcset="' . $imageWebp . '" type="image/webp"><img src="' . $imagePng . '" width="717" height="762" fetchpriority="high" loading="eager" decoding="async" alt="Kermanen zuri-beltzeko erretratu grafikoa"></picture>';
$caseArt = '<div class="page-hero__art" aria-hidden="true"><picture><source srcset="' . $imageWebp . '" type="image/webp"><img src="' . $imagePng . '" width="717" height="762" fetchpriority="high" loading="eager" decoding="async" alt=""></picture></div>';
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

$caseEs = <<<HTML
<header class="page-hero page-hero--case content-band--light">{$caseArt}<div class="content-wrap"><h1>Resumen del caso</h1><p>Qué ocurrió aquella noche, cómo ha evolucionado el proceso y por qué seguimos reclamando verdad, justicia y reparación.</p></div></header>
<section class="content-band content-band--dark"><div class="content-wrap split-grid"><div><h2 class="campaign-heading"><span>Kerman no murió,</span><strong>lo mataron</strong></h2></div><div class="reading-copy"><p class="lead">Kerman Villate Beitia tenía 31 años. En la madrugada del 23 de febrero de 2025, un portero de la discoteca Mitika de Vitoria-Gasteiz le propinó un golpe de una violencia extrema. Esta agresión le produjo la muerte prácticamente de forma inmediata.</p><p>Quienes queríamos a Kerman reclamamos que su muerte se investigue y se juzgue con todas las garantías, y que se depuren las responsabilidades que correspondan.</p></div></div></section>
<section class="content-band content-band--light"><div class="content-wrap split-grid"><div data-reveal><h2 class="section-heading">Qué ocurrió aquella noche</h2></div><div class="reading-copy" data-reveal><p class="lead">Kerman llegó al control de acceso de Mitika junto a sus amigos. Mientras ellos pudieron acceder al hall de Dendaraba, a Kerman le denegaron el paso. Sus amigos intentaron interceder para resolver la situación. Minutos más tarde le dicen a Kerman que entrará en breve y los amigos deciden bajar al local.</p><p>Las grabaciones muestran que Kerman permaneció esperando con absoluta tranquilidad y se le ve interactuando con conocidos presentes en la zona. No se observa ninguna actitud agresiva ni situación de tensión por parte de Kerman ni de nadie presente en la zona.</p><p><strong>Menos de un minuto antes de la agresión</strong>, el responsable del equipo de porteros salió del interior del local y reunió a sus hombres. Tal y como recoge el auto del juez de instrucción, realizó dos gestos señalando de forma evidente la zona de la mandíbula mientras dirigía la mirada hacia el portero que posteriormente agrediría a Kerman. Este respondió simulando un golpe en esa misma zona de su cara y asintiendo con la cabeza.</p><p><strong>30 segundos después, y aprovechando la entrada de 2 personas que le servían de pantalla visual, el portero agresor, sin mediar palabra, ni discusión, ni interacción alguna</strong>, golpeó a Kerman con una violencia extrema que terminó provocándole la muerte. La agresión se realizó, a traición, sin ningún motivo aparente, y sin que Kerman pudiese ser consciente de que iba a ser agredido. Tras la agresión, abandonó el lugar caminando hacia el interior del local con total indiferencia.</p></div></div></section>
<section class="content-band content-band--soft"><div class="content-wrap split-grid"><div data-reveal><h2 class="section-heading">Lo que recogió la instrucción</h2></div><div class="reading-copy" data-reveal><p class="lead"><strong>El Juzgado de Instrucción número 3 de Vitoria-Gasteiz acordó la prisión provisional del portero investigado dos días después del ataque mortal.</strong></p><p>En su resolución de 30 de septiembre de 2025, el juez instructor, ante los indicios sólidos de la gravedad de los hechos, decidió que fuesen enjuiciados por un Tribunal del Jurado como un posible delito de asesinato u homicidio doloso, que es lo que corresponde en estos casos. Para ello valoró, entre otros elementos, la capacidad lesiva del agresor, la zona corporal elegida para el ataque, la forma de ejecución, la ausencia de cualquier enfrentamiento previo y la conducta posterior a la agresión, considerando la concurrencia de una alevosía sorpresiva.</p><p>El informe forense refleja la extrema violencia del impacto. La fuerza del golpe directo recibido por Kerman del agresor le provocó la fractura de ambas mandíbulas y la extracción de un fragmento óseo de aproximadamente 1,5 centímetros, que quedó incrustado en la base de la lengua.</p></div></div></section>
<section class="content-band content-band--light"><div class="content-wrap split-grid"><div data-reveal><h2 class="section-heading">El cambio en el recorrido judicial</h2></div><div class="reading-copy" data-reveal><p class="lead"><strong>El 26 de noviembre de 2025, la Audiencia Provincial de Álava revocó la decisión del juez que había investigado el caso desde el principio.</strong></p><p>Por extraño que pueda parecer, la fiscalía recurrió la decisión del juez instructor incurriendo en graves alteraciones de las evidencias y dando credibilidad a la versión del agresor, pese a que durante la investigación tanto el juez como la Policía Criminalística habían demostrado la falsedad de su relato, incompatible con los hechos documentados. La Audiencia Provincial asumió este relato, rebajó la gravedad de los hechos a un homicidio imprudente en concurso con lesiones dolosas, dejando al agresor en libertad provisional tras pagar una fianza de solo <strong>6.000 euros e impidiendo que sea juzgado por las calificaciones más graves.</strong></p><p><strong>Consideramos que esta actuación no se ajusta a la gravedad de lo ocurrido y supone un profundo desprecio hacia la víctima, su familia y la sociedad en su conjunto.</strong> Al descartar el dolo y la alevosía de forma precipitada en fase intermedia, la Audiencia de Álava invade decisiones que debían haberse adoptado en un juicio con todas las garantías, impidiendo que el hecho sea enjuiciado de forma completa por el jurado que naturalmente le corresponde.</p><p>La familia recurrió esa decisión, pero el <strong>30 de abril de 2026</strong> el Tribunal Supremo comunicó que no admitía el recurso por un motivo exclusivamente formal. Es decir, el Supremo <strong>no analizó las pruebas ni decidió cómo debían calificarse los hechos.</strong></p><p>Desde la asociación queremos informar a la ciudadanía sobre las irregularidades del proceso e intentar reconducirlo.</p></div></div></section>
<section class="content-band content-band--dark"><div class="content-wrap split-grid"><div><h2 class="section-heading">Advertencias y respuesta institucional</h2></div><div class="reading-copy"><p>Antes de la muerte de Kerman ya existían actuaciones y advertencias relacionadas con la violencia ejercida en el acceso al local. Tras lo sucedido, la familia pidió aclarar qué se conocía, qué medidas se habían adoptado y qué responsabilidades administrativas podían existir.</p><p>El 25 de mayo de 2026, el Ararteko recomendó al Ayuntamiento de Vitoria-Gasteiz que respondiera de manera expresa, motivada e íntegra a la denuncia y que reforzara el control preventivo para garantizar el cumplimiento de las condiciones de seguridad en los locales de ocio.</p><p class="lead">Consideramos insuficiente la respuesta institucional recibida. Trabajamos para que se expliquen las advertencias previas, se revisen las actuaciones realizadas y se adopten medidas que eviten que algo similar vuelva a suceder.</p></div></div></section>
<section class="content-band content-band--light"><div class="content-wrap split-grid"><div><h2 class="section-heading">Cronología esencial</h2></div><div class="reading-copy"><div class="evidence-list case-timeline">
  <div class="evidence-row"><strong>Mayo de 2024</strong><p>Constan actuaciones y advertencias previas relacionadas con la violencia en el acceso a Mitika. Su alcance sigue formando parte de las cuestiones que reclamamos esclarecer.</p></div>
  <div class="evidence-row"><strong>23–25 de febrero de 2025</strong><p>Kerman murió el 23 de febrero tras recibir el golpe. Dos días después, el juzgado acordó la prisión provisional del portero investigado.</p></div>
  <div class="evidence-row"><strong>30 de septiembre de 2025</strong><p>El juez instructor apreció indicios para un juicio con jurado por homicidio o asesinato.</p></div>
  <div class="evidence-row"><strong>26 de noviembre de 2025</strong><p>Al estimar el recurso de la Fiscalía, la Audiencia Provincial modificó el procedimiento y la calificación provisional, dando la espalda a las conclusiones de la investigación realizada durante la instrucción, y acordó la puesta en libertad bajo fianza del agresor.</p></div>
  <div class="evidence-row"><strong>Enero de 2026</strong><p>La familia recurrió ante el Tribunal Supremo la decisión de la Audiencia Provincial.</p></div>
  <div class="evidence-row"><strong>18 de febrero de 2026</strong><p>Los padres de Kerman comparecieron en el Parlamento Vasco para pedir un juicio con todas las garantías y medidas de prevención.</p></div>
  <div class="evidence-row"><strong>30 de abril de 2026</strong><p>El Tribunal Supremo inadmitió el recurso sin entrar a analizar el fondo del caso.</p></div>
  <div class="evidence-row"><strong>25 de mayo de 2026</strong><p>El Ararteko advirtió al Ayuntamiento de que debió actuar de forma más proactiva ante las reiteradas agresiones registradas en el acceso al local, y le formuló dos recomendaciones: responder de forma motivada a la denuncia presentada por la familia y reforzar los controles preventivos para evitar que se repitan situaciones como estas.</p></div>
  <div class="evidence-row"><strong>Junio de 2026</strong><p>Presentamos Egia Kermanentzat Elkartea y compartimos públicamente el relato, el recorrido del caso y nuestras líneas de trabajo.</p></div>
  <div class="evidence-row"><strong>8 de julio de 2026</strong><p>Expusimos ante la Comisión municipal de Seguridad nuestra valoración de la respuesta del Ayuntamiento y del cumplimiento de las recomendaciones. <strong>El Ayuntamiento no ha cumplido las recomendaciones del Ararteko y ha negado a la familia información que puede ser de gran importancia para el proceso judicial.</strong></p></div>
</div></div></div></section>
HTML;

$caseEu = <<<HTML
<header class="page-hero page-hero--case content-band--light">{$caseArt}<div class="content-wrap"><h1>Kasuaren laburpena</h1><p>Gau hartan zer gertatu zen, prozesuak nola egin duen aurrera eta zergatik jarraitzen dugun egia, justizia eta erreparazioa eskatzen.</p></div></header>
<section class="content-band content-band--dark"><div class="content-wrap split-grid"><div><h2 class="campaign-heading"><span>Kerman ez zen hil;</span><strong>hil egin zuten</strong></h2></div><div class="reading-copy"><p class="lead">Kerman Villate Beitiak 31 urte zituen. 2025eko otsailaren 23ko goizaldean, Gasteizko Mitika diskotekako atezain batek muturreko indarkeriaz jo zuen. Eraso horren ondorioz, berehala hil zen.</p><p>Kerman maite genuenok haren heriotza berme guztiekin ikertu eta epaitzea eskatzen dugu, baita dagozkion erantzukizun guztiak argitzea eta eskatzea ere.</p></div></div></section>
<section class="content-band content-band--light"><div class="content-wrap split-grid"><div data-reveal><h2 class="section-heading">Zer gertatu zen gau hartan</h2></div><div class="reading-copy" data-reveal><p class="lead">Kerman Mitikako sarbide-kontrolera iritsi zen bere lagunekin batera. Haiei Dendarabako hallera sartzen utzi zieten bitartean, Kermani sarrera ukatu zioten. Lagunak egoera konpontzen saiatu ziren. Minutu batzuk geroago, Kermani laster sartuko zela esan zioten, eta lagunek lokalera jaistea erabaki zuten.</p><p>Irudiek erakusten dute Kerman lasai zain geratu zela, eta inguruan zeuden ezagun batzuekin hizketan ari zela ikus daiteke. Ez da inolako jarrera oldarkorrik edo tentsio-egoerarik antzematen, ez Kermanen aldetik, ezta inguruan zegoen inoren aldetik ere.</p><p><strong>Erasoa gertatu baino minutu bat baino gutxiago lehenago</strong>, atezainen taldeko arduraduna lokaletik atera eta bere gizonak bildu zituen. Instrukzioko epailearen autoak jasotzen duen bezala, bi keinu egin zituen masailezurraren aldea modu nabarmenean seinalatuz, ondoren Kermani erasoko zion atezainari begira. Hark, erantzun gisa, kolpe bat emateko keinua egin zuen bere aurpegiko leku berean eta buruarekin baiezkoa adierazi zuen.</p><p><strong>30 segundo geroago, eta ikusmen-estalki gisa balio zioten bi pertsonaren sarrera aprobetxatuz, erasotzaileak, hitzik esan gabe, eztabaidarik gabe eta inolako aurretiazko harremanik izan gabe</strong>, muturreko indarkeriaz jo zuen Kerman; kolpe hark haren heriotza eragin zuen. Erasoa ustekabean egin zen, traizioz, itxurazko arrazoirik gabe, eta Kermanek ez zuen erasoa jasoko zuenik susmatzeko aukerarik izan. Erasoaren ondoren, erasotzailea axolagabe itzuli zen lokalaren barrura, hoztasun osoz.</p></div></div></section>
<section class="content-band content-band--soft"><div class="content-wrap split-grid"><div data-reveal><h2 class="section-heading">Instrukzioan jasotakoa</h2></div><div class="reading-copy" data-reveal><p class="lead"><strong>Gasteizko 3. Instrukzio Epaitegiak behin-behineko espetxealdia agindu zuen ikertutako atezainarentzat, eraso hilgarria gertatu eta bi egunera.</strong></p><p>2025eko irailaren 30eko ebazpenean, instrukzioko epaileak, gertakarien larritasunari buruzko zantzu sendoak ikusita, kasua Herri Epaimahai batek epaitu behar zuela erabaki zuen, balizko hilketa edo nahita egindako homizidio delitu gisa, horrelako kasuetan dagokion bezala. Horretarako, besteak beste, erasotzailearen kalte egiteko gaitasuna, eraso egiteko hautatutako gorputz-atala, exekuzio-modua, aurretiazko liskarrik eza eta erasoaren ondorengo jokabidea baloratu zituen, ustekabeko alebosiaren zantzuak zeudela iritzita.</p><p>Auzitegiko forensearen txostenak kolpearen muturreko indarkeria jasotzen du. Erasotzaileak Kermani zuzenean emandako kolpeak bi masailezurrak hautsi zizkion, eta gutxi gorabehera 1,5 zentimetroko hezur-zati bat askatu zion, mihiaren oinarrian sartuta geratu zena.</p></div></div></section>
<section class="content-band content-band--light"><div class="content-wrap split-grid"><div data-reveal><h2 class="section-heading">Ibilbide judizialaren aldaketa</h2></div><div class="reading-copy" data-reveal><p class="lead"><strong>2025eko azaroaren 26an, Arabako Probintzia Auzitegiak kasua hasieratik ikertu zuen epailearen erabakia baliogabetu zuen.</strong></p><p>Harrigarria badirudi ere, Fiskaltzak instrukzioko epailearen erabakiaren aurka egin zuen, frogak larriki desitxuratuz eta erasotzailearen bertsioari sinesgarritasuna emanez; izan ere, ikerketan zehar bai epaileak bai Kriminalistikako Poliziak frogatu zuten haren kontakizuna dokumentatutako gertakariekin bateraezina zela. Probintzia Auzitegiak kontakizun hori bere egin zuen, arduragabekeriazko homizidioa eta nahita egindako lesioak leporatu zizkion gertakarien larritasuna jaitsiz, eta erasotzailea behin-behinean aske uztea, 6.000 euroko fidantza soilik ordainduta, erabaki zuen; horrela, ez zen delitu-kalifikaziorik larrienen arabera epaituko.</p><p><strong>Gure ustez, jarduera hori ez dator bat gertatutakoaren larritasunarekin eta biktimarekiko, haren senideekiko eta gizarte osoarekiko mespretxu sakona erakusten du. Doloa eta alebosia prozesuaren tarteko fasean presaz baztertzean, Arabako Probintzia Auzitegiak epaiketan, berme guztiekin, hartu behar ziren erabakiak aurreratu zituen, eta kasua dagokion Herri Epaimahaiak osorik epaitzea eragotzi zuen.</strong></p><p>Familiak erabaki horren aurka errekurtsoa aurkeztu zuen, baina <strong>2026ko apirilaren 30ean</strong>, Auzitegi Gorenak jakinarazi zuen ez zuela onartzen arrazoi formal huts batengatik. Hau da, Auzitegi Gorenak <strong>ez zituen frogak aztertu eta ez zuen gertakariak nola kalifikatu behar ziren erabaki.</strong></p><p>Elkartetik herritarrei prozesuan izan diren irregulartasunen berri eman eta bide egokira itzultzen saiatzen gara.</p></div></div></section>
<section class="content-band content-band--dark"><div class="content-wrap split-grid"><div><h2 class="section-heading">Aurreko ohartarazpenak eta erakundeen erantzuna</h2></div><div class="reading-copy"><p>Kerman hil aurretik baziren jada lokaleko sarreran porteroek indarkeriaz eragindako erasoak eta ohartarazpenak. Gertatutakoaren ondoren, familiak argitu nahi izan zuen administrazioak zer zekien, zer neurri hartu ziren eta erantzukizunik ba ote zegoen.</p><p>2026ko maiatzaren 25ean, Arartekoak Gasteizko Udalari gomendatu zion salaketari berariaz, arrazoituta eta osorik erantzuteko, eta prebentziozko kontrola indartzeko, aisialdiko lokalen segurtasun-baldintzak betetzen direla bermatzeko.</p><p class="lead">Instituzioetatik jasotako erantzuna ez dela nahikoa iruditzen zaigu. Aurretiazko ohartarazpenak argitzea, egindako jarduketak berrikustea eta antzeko zerbait berriro gerta ez dadin neurriak hartzea lortzeko lanean ari gara.</p></div></div></section>
<section class="content-band content-band--light"><div class="content-wrap split-grid"><div><h2 class="section-heading">Funtsezko kronologia</h2></div><div class="reading-copy"><div class="evidence-list case-timeline">
  <div class="evidence-row"><strong>2024ko maiatza</strong><p>Mitikako sarbidean porteroek eragindako indarkeriazko erasoak eta ohartarazpenak jasota daude. Haien benetako irismena argitzea eskatzen jarraitzen dugu.</p></div>
  <div class="evidence-row"><strong>2025eko otsailaren 23tik 25era</strong><p>Kerman otsailaren 23an hil zen jasotako kolpearen ondorioz. Bi egun geroago, epaitegiak ikertutako atezainaren behin-behineko espetxealdia agindu zuen.</p></div>
  <div class="evidence-row"><strong>2025eko irailaren 30a</strong><p>Instrukzioko epaileak Herri Epaimahaiaren aurrean hilketa edo nahita egindako homizidioagatik epaitzeko zantzuak zeudela iritzi zuen.</p></div>
  <div class="evidence-row"><strong>2025eko azaroaren 26a</strong><p>Fiskalaren errekurtsoa onartuta, Probintzia Auzitegiak prozedura eta behin-behineko kalifikazioa aldatu zituen, instrukzioan egindako ikerketaren ondorioei bizkarra emanez, eta erasotzailea fidantzapean aske uztea erabaki zuen.</p></div>
  <div class="evidence-row"><strong>2026ko urtarrila</strong><p>Familiak Probintzia Auzitegiaren erabakiaren aurkako errekurtsoa aurkeztu zuen Auzitegi Gorenean.</p></div>
  <div class="evidence-row"><strong>2026ko otsailaren 18a</strong><p>Kermanen gurasoek agerraldia egin zuten Eusko Legebiltzarrean, berme guztiekin egingo zen epaiketa eta prebentzio-neurriak eskatzeko.</p></div>
  <div class="evidence-row"><strong>2026ko apirilaren 30a</strong><p>Auzitegi Gorenak errekurtsoa ez onartzea erabaki zuen, auziaren funtsa aztertu gabe.</p></div>
  <div class="evidence-row"><strong>2026ko maiatzaren 25a</strong><p>Arartekoak Udalari ohartarazi zion lokalaren sarreran behin eta berriz gertatutako indarkeriazko erasoen aurrean modu proaktiboagoan jardun behar zuela, eta bi gomendio egin zizkion: familiak aurkeztutako salaketari arrazoitutako erantzuna ematea eta prebentziozko kontrolak indartzea, halako egoerak berriro gerta ez daitezen.</p></div>
  <div class="evidence-row"><strong>2026ko ekaina</strong><p>Egia Kermanentzat Elkartea aurkeztu genuen, eta kasuaren kontakizuna, ibilbidea eta gure lan-ildoak jendaurrean azaldu genituen.</p></div>
  <div class="evidence-row"><strong>2026ko uztailaren 8a</strong><p>Udaleko Segurtasun Batzordearen aurrean azaldu genuen Udalaren erantzunari eta Arartekoaren gomendioen betetze-mailari buruzko gure balorazioa. <strong>Udalak ez ditu bete Arartekoaren gomendioak eta familiari prozesu judizialerako garrantzi handikoa izan daitekeen informazioa ukatu dio.</strong></p></div>
</div></div></div></section>
HTML;

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
    <p class="updates-status">Próximamente disponible</p>
    <h1>Actualidad</h1>
    <p>Estamos preparando este espacio para compartir noticias, comunicados y novedades. Esta sección se encuentra actualmente en construcción.</p>
    <div class="hero-actions">
      <a class="button button--primary" href="{$instagram}" target="_blank" rel="noopener noreferrer">Seguir la actividad en Instagram</a>
      <a class="button button--inverse" href="/es/contacto/">Contactar</a>
    </div>
  </div>
</header>
HTML;

$updatesEu = <<<HTML
<header class="page-hero page-hero--updates content-band--dark">
  <div class="updates-wordmark" aria-hidden="true">BERRIAK</div>
  <div class="content-wrap">
    <p class="updates-status">Laster eskuragarri</p>
    <h1>Berriak</h1>
    <p>Gune hau albisteak, komunikatuak eta berriak partekatzeko prestatzen ari gara. Atala laster egongo da erabilgarri.</p>
    <div class="hero-actions">
      <a class="button button--primary" href="{$instagram}" target="_blank" rel="noopener noreferrer">Jarraitu Instagramen</a>
      <a class="button button--inverse" href="/kontaktua/">Jarri harremanetan</a>
    </div>
  </div>
</header>
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
