<?php
	include("includes/functions.php");
	if(isset($_GET['urlid'])){
		$urlid = $_GET['urlid'];
	}else{
		$urlid = 0;
	}
	$lg_language = "de";
	
	if(isset($_GET['lang'])){
		$lg_language = $_GET['lang'];
	}else{
		$url = getCRMUrlr() . 'public_kandidati_import.php?page=getLanguageForLink';
		$ch = curl_init();
		$params = ['link_id' => $urlid];
		$data = http_build_query($params);
		
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, count($params));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$lg_language = curl_exec($ch);
		curl_close($ch);
	}
	// var_dump($lg_language);

	if(isset($_SERVER['HTTP_REFERER'])) {
		$kandidat_visitedurl = $_SERVER["HTTP_REFERER"];
	}else{
		$kandidat_visitedurl = "";
	}
	// GET PARTNER TOKEN
	if(isset($_GET['token'])){
		$token_c = $_GET['token'];
		$token = base64_decode($token_c);
		$partner_link = true;
	}else{
		$token_c = NULL;
		$token = NULL;
		$partner_link = false;
	}
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
		$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	
	$ipdat = @json_decode(file_get_contents( 
		"http://www.geoplugin.net/json.gp?ip=" . $ip)); 
		
	$countryCode = strtolower($ipdat->geoplugin_countryCode);

	if($partner_link){
		// CURL ZA NALOG TITLE
		$url = getCRMUrlr() . 'public_kandidati_import.php?page=getNalogTitle';
		$ch = curl_init();
		$params = ['link_id' => $urlid, 'lang' => $lg_language];
		$data = http_build_query($params);
		
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, count($params));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$nalog_title = curl_exec($ch);
		curl_close($ch);
		
		// CURL ZA NALOG LOCATION
		$url = getCRMUrlr() . 'public_kandidati_import.php?page=getNalogLocation';
		$ch = curl_init();
		$params = ['link_id' => $urlid];
		$data = http_build_query($params);
		
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, count($params));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$nalog_location = curl_exec($ch);
		curl_close($ch);

		// CURL ZA NALOG OPIS
		$url = getCRMUrlr() . 'public_kandidati_import.php?page=getNalogOpis';
		$ch = curl_init();
		$params = ['link_id' => $urlid, 'lang' => $lg_language];
		$data = http_build_query($params);
		
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, count($params));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$nalog_opis = curl_exec($ch);
		curl_close($ch);

		// CURL ZA NALOG IMG
		$url = getCRMUrlr() . 'public_kandidati_import.php?page=getNalogImage';
		$ch = curl_init();
		$params = ['link_id' => $urlid, 'lang' => $lg_language];
		$data = http_build_query($params);
		
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, count($params));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$nalog_img = curl_exec($ch);
		if($nalog_img != null AND $nalog_img != "none"){
			// $nalog_img_path = "https://wwt-dummyweb.digtest.us/files/partner_nalogs/".$nalog_img;
			$nalog_img_path = "https://crm.job-step.com/files/partner_nalogs/".$nalog_img;
		}else{
			$nalog_img_path = "https://crm.job-step.com/images/Jobstep_logo_new.png";
		}
		curl_close($ch);
		
	}else{
		$nalog_title = "";
		$nalog_location = "";
		$nalog_opis = "";
		$nalog_img_path = "";
	}
?>
<html>
    <!DOCTYPE html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<meta name="mobile-web-app-capable" content="yes">
		<title><?php echo $nalog_title; ?></title>
		<meta name="description" content="<?php echo $nalog_opis;?>">
		<link rel="icon" type="image/x-icon" href="<?php echo $nalog_img_path;?>">
		<meta property="og:title" content="<?php echo $nalog_title; ?>">
		<meta property="og:description" content="<?php echo $nalog_opis;?>">
		<meta property="og:image" content="<?php echo $nalog_img_path; ?>">
		<meta property="og:url" content="URL to Your Webpage">
		
		
		<!-- Style -->
		<link href="https://crm.job-step.com/css/bootstrap.min.css" rel="stylesheet">
		<link href="https://crm.job-step.com/css/jasny-bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="https://crm.job-step.com/css/bootstrap-select.css">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		<link rel="stylesheet" type="text/css" href="https://crm.job-step.com/css/calendar.css" />
		<link rel="stylesheet" type="text/css" href="https://crm.job-step.com/css/timedropper.min.css" rel="stylesheet">
		<link href="https://crm.job-step.com/css/jquery.dataTables.min.css" rel="stylesheet">
		<link href="https://crm.job-step.com/css/responsive.dataTables.min.css" rel="stylesheet">
		<link href="https://crm.job-step.com/css/responsive.bootstrap.min.css" rel="stylesheet">
		<link href="https://crm.job-step.com/css/jquery.fancybox.css" rel="stylesheet">
		<link href="https://crm.job-step.com/js/ui/trumbowyg.min.css" rel="stylesheet">
		<link href="https://crm.job-step.com/css/select2.min.css" rel="stylesheet">
		<link href="https://crm.job-step.com/css/style.css" rel="stylesheet">
		<link type="text/css" href="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/css/dataTables.checkboxes.css" rel="stylesheet" />
		<link href="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
		<link rel="icon" href="../images/Jobstep-logo_news.png">

		<!-- Fonts -->
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700&amp;subset=latin-ext" rel="stylesheet">
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700;800&family=Manrope:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
		<script src="https://use.fontawesome.com/758aa0fdaa.js"></script>
		<!-- FLATPICK -->
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
		<link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/airbnb.css">
		<!-- TELEFON -->
		<link rel="stylesheet" href="https://crm.job-step.com/buildTelInput/css/intlTelInput.css">
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<!-- <script src="https://cdn.datedropper.com/get/kqs82kw3qehj1ghchbtnnv6c6f7gzvlz"></script> -->
		<script src="https://crm.job-step.com/js/bootstrap.min.js"></script>
		<script src="https://crm.job-step.com/js/chart.min.js"></script>
		<script src="https://crm.job-step.com/js/modernizr.custom.63321.js"></script>
		<script type="text/javascript" src="https://crm.job-step.com/js/jquery.calendario.js"></script>
		<script src="https://crm.job-step.com/js/jquery.slimscroll.min.js"></script>
		<script src="https://crm.job-step.com/js/jquery.matchHeight-min.js"></script>
		<script src="https://crm.job-step.com/js/jasny-bootstrap.min.js"></script>
		<script src="https://crm.job-step.com/js/bootstrap-select.min.js"></script>
		<!-- TELEFON -->
		<script src="https://crm.job-step.com/buildTelInput/js/intlTelInput.js"></script>
		<!-- <script src="https://crm.job-step.com/js/intlTelInput-jquery.min.js"></script> -->
		<!--		<script src="https://intl-tel-input.com/node_modules/intl-tel-input/build/js/utils.js"></script>-->
		<!--	<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.min.js"></script>-->
		
		<script src="https://crm.job-step.com/js/jquery.dataTables.min.js"></script>

		<script src="https://crm.job-step.com/js/dataTables.responsive.min.js"></script>
		<script src="https://crm.job-step.com/js/responsive.bootstrap.min.js"></script>
		<script src="https://crm.job-step.com/js/timedropper.min.js"></script>
		<script src="https://crm.job-step.com/js/jquery.fancybox.js"></script>
		<script src="https://crm.job-step.com/js/jquery.mask.min.js"></script>
		<script src="https://crm.job-step.com/js/trumbowyg.min.js"></script>
		<script src="https://crm.job-step.com/js/timeago.js"></script>
		<script src="https://crm.job-step.com/js/select2.min.js"></script>
		<script src="https://crm.job-step.com/js/jquery.table2excel.js"></script>
		<script src="https://crm.job-step.com/js/jquery.password-generator-plugin.min.js"></script>
		<script type="text/javascript" src="https://crm.job-step.com/js/langs/hr.min.js"></script>
		<script type="text/javascript" src="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/js/dataTables.checkboxes.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
		<!-- FLATPICK -->
		<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
		<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
		 <link href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.css" rel="stylesheet" />
		 <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.4.1/semantic.min.js"></script>
    </head>
    <body>
        
	</head>
    <?php
   
   

	//Jezik forme - START
    
        
		
		if($lg_language == "sr"){
			$txt_jezik 				= "Jezik";
			$txt_prijava 			= "Prijava za posao";
			$txt_popunite_formu 	= "Da bi ste se prijavili za posao, popunite navedenu formu.";
			$txt_korak				= "Korak";
			$txt_osnovne_info 		= "Osnovne informacije";
			$txt_ime 				= "Ime";
			$txt_prezime 			= "Prezime";
			$txt_datum_rod 			= "Datum rođenja";
			$txt_mobilni 			= "Mobilni telefon";
			$txt_nivo_jezika		= "Nivo poznavanja jezika";
			$txt_nivo_njemackog 	= "Nivo poznavanja nemačkog jezika";
			$txt_nivo_engleskog 	= "Nivo poznavanja engleskog jezika";
			$txt_odaberi 			= "Odaberi";
			$txt_bez_znanja 		= "Bez znanja";
			$txt_vozacka_pitanje 	= "Da li imate vozačku dozvolu";
			$txt_ne 				= "Ne";
			$txt_da 				= "Da";
			$txt_zavrseno_obr_smjer = "Završeno obrazovanje/smer";
			$txt_zavrseno_obr 		= "Završeno obrazovanje";
			$txt_zvanje_smjer 		= "Zvanje/smer";
			$txt_ostalo 			= "Ostalo";
			$txt_naziv_skole 		= "Naziv škole";
			$txt_unesite_smjer 		= "Unesite smer";
			$txt_iskustvo_struka 	= "Da li imate radnog iskustva u struci?";
			$txt_iskustvo_5_god 	= "Radno iskustvo u struci u poslednjih 5 godina?";
			$txt_nema_5_god 		= "Nemam iskustva u poslednjih 5 godina";
			$txt_manje_1_god 		= "Manje od 1 godine";
			$txt_godinu 			= "godinu";
			$txt_godine 			= "godine";
			$txt_godina 			= "godina";
			$txt_drzavljanstvo 		= "Državljanstvo";
			$txt_državljanin 		= "državljanin";
			$txt_nastavi 			= "Nastavi";
			$txt_sva_polja 			= "Sva polja označena sa <span class='zvjezdica'>*</span> su obavezna";
			$txt_nastavkom 			= "Nastavkom prihvatate uslove o ";
			$txt_nastavkom_izjava 	= "Zaštiti privatnosti.";
			$txt_pogledaj_izjavu 	= "Pogledaj izjavu o zaštiti privatnosti";
			$txt_prava 				= "Sva prava zadržana";
			$txt_text_na_dnu 		= "JOBSTEP";
			$txt_izjava_privatnost 	= "Izjava o zaštiti privatnosti";
			$txt_zatvori 			= "Zatvori";
			// $txt_full_izjava 		= "Slanjem ovog obrasca dajete svoju saglasnost da Vaši podaci budu registrovani u našoj bazi podataka, pri čemu će isti biti dostupni pravnom licu JobStep Int GmbH i njegovim poslovnim saradnicima (partnerima). JobStep Int GmbH obavezan je čuvati privatnost svojih korisnika i osigurati visok stepen sigurnosti i povjerljivosti ličnih podataka prikupljenih od potencijalnih kandidata, a u skladu sa Zakonom o zaštiti ličnih podataka Švicarske i drugim relevantnim domaćim i evropskim zakonima i podzakonskim aktima. Lični podaci koji budu predmet obrade predstavljaju službenu tajnu. Lični i svi drugi podaci iz ovog obrasca koje kandidat unese u obrascu ili u bilo kojoj drugoj formi, biće korišteni isključivo od strane ovlaštenih lica u procesu procjene kandidata i posredovanja prilikom zapošljavanja.";
			$txt_full_izjava 		= "Hvala vam na interesovanju za apliciranje kod Jobstep Int GmbH. Zaštita vaših ličnih podataka nam je važna. Niže želimo da vas informišemo o tome kako postupamo sa vašim podacima:
										<br>
										<br>
										1. Prikupljanje podataka: Prikupljamo lične podatke koje nam dostavljate kao deo vaše prijave. To uključuje, između ostalog, vaše ime, kontakt informacije, profesionalno iskustvo i kvalifikacije.
										<br>
										<br>
										2. Upotreba vaših podataka: Podaci koje pružate koristiće se isključivo u okviru procesa prijave. Vaše informacije će se tretirati poverljivo i neće biti dostavljene trećim stranama bez vašeg pristanka.
										<br>
										<br>
										3. Čuvanje podataka: Vaši podaci će biti čuvani tokom trajanja procesa prijave. U slučaju uspešnog zaposlenja, vaše informacije će biti prenesene u našu bazu podataka zaposlenih.
										<br>
										<br>
										4. Bezbednost podataka: Sprovodimo odgovarajuće sigurnosne mere radi zaštite vaših podataka od neovlašćenog pristupa.
										<br>
										<br>
										5. Prava aplikanta: Imate pravo tražiti informacije o vašim sačuvanim podacima, ispraviti ih ili ih izbrisati. Molimo vas da nas kontaktirate na <b>info@job-step.com</b> za sva pitanja.
										<br>
										<br>
										Podnošenjem vaše aplikacije, saglasni ste sa navedenim uslovima.
										Hvala vam na poverenju u Jobstep Int GmbH.";
			$txt_day				= "DD";
			$txt_month				= "MM";	
			$txt_year				= "GGGG";
			$txt_fakultet 			= "Fakultet";
			$txt_srednja_skola 		= "Srednja škola";
			$txt_nivo_obrazovanja   = "Nivo obrazovanja";
			$txt_njemacki   		= "Nemački";
			$txt_engleski   		= "Engleski";
			$txt_hrvatski   		= "Hrvatski";
			$txt_srpski		   		= "Srpski";
			$txt_bosanski	   		= "Bosanski";
			$txt_francuski	   		= "Francuski";
			$txt_talijanski	   		= "Italijanski";
			$txt_vise_jezika   		= "Dodaj još jedan";
			$txt_maternji   		= "Maternji jezik";
			$txt_odaberi_opciju   	= "Odaberi opciju";
			$txt_neispravan_broj   	= "Netačan broj";
			$txt_neispravan_country_code   	= "Netačan pozivni broj";
			$txt_predug_broj   		= "Broj je predug";
			$txt_prekratak_broj   	= "Broj je prekratak";
			$txt_netacan_broj   	= "Netačan broj";
			$txt_valid_number   	= "Tačan broj";
			$txt_ispunite_polje     = "Ispunite ovo polje";
		}elseif($lg_language == "de"){
			
			$txt_jezik 				= "Sprache";
			$txt_prijava 			= "Bewerbung";
			$txt_popunite_formu 	= "Um sich für die Stelle zu bewerben, füllen Sie bitte das angegebene Formular aus";
			$txt_korak 				= "Schritt";
			$txt_osnovne_info 		= "Grundinformationen";
			$txt_ime 				= "Vorname";
			$txt_prezime 			= "Nachname";
			$txt_datum_rod 			= "Geburtsdatum";
			$txt_mobilni 			= "Handynummer";
			$txt_nivo_jezika		= "Sprachkenntnisse";
			$txt_nivo_njemackog 	= "Deutschkenntnisse";
			$txt_nivo_engleskog 	= "Englischkenntnisse";
			$txt_odaberi 			= "Wählen";
			$txt_bez_znanja 		= "Ohne Wissen";
			$txt_vozacka_pitanje 	= "Haben Sie einen Führerschein";
			$txt_ne 				= "Nein";
			$txt_da 				= "Ja";
			$txt_zavrseno_obr_smjer = "Abgeschlossene Ausbildung/Richtung";
			$txt_zavrseno_obr 		= "Abgeschlossene Ausbildung";
			$txt_zvanje_smjer 		= "Titel/Richtung";
			$txt_ostalo 			= "Sonstiges";
			$txt_naziv_skole 		= "Name der Schule";
			$txt_unesite_smjer		= "Geben Sie die Richtung ein";
			$txt_iskustvo_struka 	= "Haben Sie Berufserfahrung im ausgebildeten Beruf?";
			$txt_iskustvo_5_god 	= "Berufserfahrung im Fachgebiet in den letzten 5 Jahren";
			$txt_nema_5_god 		= "Ich habe keine Erfahrung in den letzten 5 Jahren";
			$txt_manje_1_god 		= "Weniger als 1 Jahr";
			$txt_godinu 			= "Jahr";
			$txt_godine 			= "Jahre";
			$txt_godina 			= "Jahre";
			$txt_drzavljanstvo 		= "Staatsangehörigkeit";
			$txt_državljanin 		= "Staatsbürger";
			$txt_nastavi 			= "Absenden";
			$txt_sva_polja 			= "Alle mit * gekennzeichneten Felder sind Pflichtfelder";
			$txt_nastavkom 			= "Indem Sie fortfahren, akzeptieren Sie die";
			$txt_nastavkom_izjava 	= "Datenschutzerklärung.";
			$txt_pogledaj_izjavu 	= "Sehen Sie sich die Datenschutzerklärung an";
			$txt_prava	 			= "Alle Rechte vorbehalten";
			$txt_text_na_dnu 		= "JOBSTEP";
			$txt_izjava_privatnost 	= "Datenschutzerklärung";
			$txt_zatvori 			= "Schließen";
			// $txt_full_izjava 		= "Mit dem Absenden dieses Formulars erklären Sie sich damit einverstanden, dass Ihre Daten in unserer Datenbank registriert werden, die der juristischen Person JobStep Int GmbH und ihren Geschäftspartnern zur Verfügung steht. JobStep Int GmbH verpflichtet sich, die Privatsphäre seiner Nutzer zu schützen und ein hohes Maß an Sicherheit und Vertraulichkeit der von potenziellen Kandidaten erfassten personenbezogenen Daten gemäß dem Gesetz zum Schutz personenbezogener Daten von Schweiz und anderen relevanten nationalen und europäischen Gesetzen zu gewährleisten und Vorschriften. Die verarbeiteten personenbezogenen Daten gelten als vertraulich. Persönliche und alle weiteren Daten aus diesem Formular, die der Kandidat in das Formular oder in ein anderes Formular eingibt, werden ausschließlich von autorisierten Personen zur Kandidatenbewertung und Arbeitsvermittlung verwendet.";
			$txt_full_izjava 		= "Vielen Dank für Ihr Interesse an einer Bewerbung bei der Jobstep Int GmbH. Der Schutz Ihrer persönlichen Daten ist uns wichtig. Nachfolgend möchten wir Sie darüber informieren, wie wir mit Ihren Daten umgehen:
										<br>
										<br>
										1. Datenerhebung: Wir erheben personenbezogene Daten, die Sie uns im Rahmen Ihrer Bewerbung zur Verfügung stellen. Dazu gehören unter anderem Name, Kontaktdaten, beruflicher Werdegang und Qualifikationen.
										<br>
										<br>
										2. Verwendung Ihrer Daten: Die von Ihnen bereitgestellten Daten werden ausschließlich für den Bewerbungsprozess genutzt. Ihre Informationen werden vertraulich behandelt und nicht ohne Ihre Zustimmung an Dritte weitergegeben.
										<br>
										<br>
										3. Speicherung: Ihre Daten werden für die Dauer des Bewerbungsprozesses gespeichert. Bei einer erfolgreichen Anstellung werden Ihre Informationen in unsere Mitarbeiterdatenbank übernommen.
										<br>
										<br>
										4. Datensicherheit: Wir treffen angemessene Sicherheitsmaßnahmen, um Ihre Daten vor unbefugtem Zugriff zu schützen.
										<br>
										<br>
										5. Rechte der Bewerber: Sie haben das Recht, Auskunft über Ihre gespeicherten Daten zu erhalten, diese zu korrigieren oder löschen zu lassen. Bitte kontaktieren Sie uns dazu unter <b>info@job-step.com</b>.
										<br>
										<br>
										Mit der Einreichung Ihrer Bewerbung erklären Sie sich mit den oben genannten Bedingungen einverstanden.
										Vielen Dank für Ihr Vertrauen in die Jobstep Int GmbH.";
			$txt_day				= "TT";
			$txt_month				= "MM";	
			$txt_year				= "JJJJ";
			$txt_fakultet 			= "Hochschulabschluss/Studium";
			$txt_srednja_skola 		= "Berufsausbildung";
			$txt_nivo_obrazovanja   = "Ausbildung";
			$txt_njemacki   		= "Deutsch";
			$txt_engleski   		= "Englisch";
			$txt_hrvatski   		= "Kroatisch";
			$txt_srpski		   		= "Serbisch";
			$txt_bosanski	   		= "Bosnisch";
			$txt_francuski	   		= "Französisch";
			$txt_talijanski	   		= "Italienisch";
			$txt_vise_jezika   		= "Fügen Sie eine weitere hinzu";
			$txt_maternji   		= "Muttersprache";			
			$txt_odaberi_opciju   	= "Wähle eine Option";
			$txt_neispravan_broj   	= "Falsche Nummer";
			$txt_neispravan_country_code   	= "Ungültiger Ländercode";
			$txt_predug_broj   		= "Zu lange Nummer";
			$txt_prekratak_broj   	= "Zu kurze Nummer";
			$txt_netacan_broj   	= "Falsche Nummer";
			$txt_valid_number   	= "Korrekte Nummer";
			$txt_ispunite_polje     = "Feld ausfüllen";			
		}elseif($lg_language == "en"){
			
			$txt_jezik 				= "Language";
			$txt_prijava 			= "Job Application";
			$txt_popunite_formu 	= "To apply for the position, please fill out the form below.";
			$txt_korak 				= "Step";
			$txt_osnovne_info 		= "Basic Information";
			$txt_ime 				= "First Name";
			$txt_prezime 			= "Last Name";
			$txt_datum_rod 			= "Date of Birth";
			$txt_mobilni 			= "Mobile Phone";
			$txt_nivo_jezika		= "Proficiency in Languages";
			$txt_nivo_njemackog 	= "Proficiency in German Language";
			$txt_nivo_engleskog 	= "Proficiency in English Language";
			$txt_odaberi 			= "Select";
			$txt_bez_znanja 		= "No knowledge";
			$txt_vozacka_pitanje 	= "Do you have a driver's license?";
			$txt_ne 				= "No";
			$txt_da 				= "Yes";
			$txt_zavrseno_obr_smjer = "Completed Education/Field";
			$txt_zavrseno_obr 		= "Completed Education";
			$txt_zvanje_smjer 		= "Degree/Field";
			$txt_ostalo 			= "Other";
			$txt_naziv_skole 		= "School Name";
			$txt_unesite_smjer 		= "Enter Field";
			$txt_iskustvo_struka 	= "Do you have work experience in your field?";
			$txt_iskustvo_5_god 	= "Work experience in your field in the last 5 years?";
			$txt_nema_5_god 		= "No experience in the last 5 years";
			$txt_manje_1_god 		= "Less than 1 year";
			$txt_godinu 			= "year";
			$txt_godine 			= "years";
			$txt_godina 			= "years";
			$txt_drzavljanstvo 		= "Citizenship";
			$txt_državljanin 		= "Citizen";
			$txt_nastavi 			= "Continue";
			$txt_sva_polja 			= "All fields marked with * are mandatory.";
			$txt_nastavkom 			= "By continuing, you accept the ";
			$txt_nastavkom_izjava 	= "Privacy policy.";
			$txt_prava 				= "All rights reserved";
			$txt_text_na_dnu 		= "JOBSTEP";
			$txt_izjava_privatnost 	= "Privacy Statement";
			$txt_zatvori 			= "Close";
			// $txt_full_izjava 		= "By submitting this form, you consent to your data being registered in our database, which will be available to the legal entity JobStep Int GmbH and its business partners. JobStep Int GmbH undertakes to protect the privacy of its users and ensure a high level of security and confidentiality of personal data collected from potential candidates, following the Law on the Protection of Personal Data of Switzerland and other relevant domestic and European laws and regulations. The personal data that is processed is considered confidential. Personal and all other data from this form that the candidate enters in the form or any other form will be used exclusively by authorised persons in candidate evaluation and employment mediation.";
			$txt_full_izjava 		= "Thank you for your interest in applying to Jobstep Int GmbH. The protection of your personal data is important to us. Below, we would like to inform you about how we handle your data:
										<br>
										<br>
										1. Data Collection: We collect personal data that you provide to us as part of your application. This includes, among other things, your name, contact details, professional background, and qualifications.
										<br>
										<br>
										2. Use of Your Data: The data you provide will be used exclusively for the application process. Your information will be treated confidentially and will not be disclosed to third parties without your consent.
										<br>
										<br>
										3. Storage: Your data will be stored for the duration of the application process. In the case of a successful hiring, your information will be transferred to our employee database.
										<br>
										<br>
										4. Data Security: We implement appropriate security measures to protect your data from unauthorized access.
										<br>
										<br>
										5. Applicant's Rights: You have the right to request information about your stored data, to correct it, or to have it deleted. Please contact us at <b>info@job-step.com</b> for any inquiries.
										<br>
										<br>
										By submitting your application, you agree to the conditions mentioned above.
										Thank you for your trust in Jobstep Int GmbH.";
			$txt_day				= "DD";
			$txt_month				= "MM";	
			$txt_year				= "YYYY";
			$txt_fakultet 			= "University degree";
			$txt_srednja_skola 		= "Highschool degree";
			$txt_nivo_obrazovanja   = "Degree level";		
			$txt_njemacki   		= "German";
			$txt_engleski   		= "English";
			$txt_hrvatski   		= "Croatian";
			$txt_srpski		   		= "Serbian";
			$txt_bosanski	   		= "Bosnian";
			$txt_francuski	   		= "French";
			$txt_talijanski	   		= "Italian";
			$txt_vise_jezika   		= "Add one more";
			$txt_maternji   		= "Native";
			$txt_odaberi_opciju   	= "Choose option";
			$txt_neispravan_broj   	= "Invalid number";
			$txt_neispravan_country_code   	= "Invalid country code";
			$txt_predug_broj   		= "Too long number";
			$txt_prekratak_broj   	= "Too short number";
			$txt_netacan_broj   	= "Incorrect number";
			$txt_valid_number   	= "Correct number";
			$txt_ispunite_polje     = "Fill out the field";			
		}else if($lg_language == "bs"){
			$txt_jezik 				= "Jezik";
			$txt_prijava 			= "Prijava za posao";
			$txt_popunite_formu 	= "Da bi ste se prijavili za posao, popunite navedenu formu.";
			$txt_korak 				= "Korak";
			$txt_osnovne_info 		= "Osnovne informacije";
			$txt_ime 				= "Ime";
			$txt_prezime 			= "Prezime";
			$txt_datum_rod 			= "Datum rođenja";
			$txt_mobilni 			= "Mobilni telefon";
			$txt_nivo_jezika		= "Nivo poznavanja jezika";
			$txt_nivo_njemackog 	= "Nivo poznavanja njemačkog jezika";
			$txt_nivo_engleskog 	= "Nivo poznavanja engleskog jezika";
			$txt_odaberi 			= "Odaberi";
			$txt_bez_znanja 		= "Bez znanja";
			$txt_vozacka_pitanje 	= "Da li imate vozačku dozvolu";
			$txt_ne 				= "Ne";
			$txt_da 				= "Da";
			$txt_zavrseno_obr_smjer = "Završeno obrazovanje/smjer";
			$txt_zavrseno_obr 		= "Završeno obrazovanje";
			$txt_zvanje_smjer 		= "Zvanje/smijer";
			$txt_ostalo 			= "Ostalo";
			$txt_naziv_skole 		= "Naziv škole";
			$txt_unesite_smjer 		= "Unesite smjer";
			$txt_iskustvo_struka 	= "Da li imate radnog iskustva u struci?";
			$txt_iskustvo_5_god 	= "Radno iskustvo u struci u posljednjih 5 godina?";
			$txt_nema_5_god 		= "Nemam iskustva u posljednjih 5 godina";
			$txt_manje_1_god 		= "Manje od 1 godine";
			$txt_godinu 			= "godinu";
			$txt_godine 			= "godine";
			$txt_godina 			= "godina";
			$txt_drzavljanstvo 		= "Državljanstvo";
			$txt_državljanin 		= "državljanin";
			$txt_nastavi 			= "Nastavi";
			$txt_sva_polja 			= "Sva polja označena sa <span class='zvjezdica'>*</span> su obavezna";
			$txt_nastavkom 			= "Nastavkom prihvatate uslove o ";
			$txt_nastavkom_izjava 	= "Zaštiti privatnosti.";
			$txt_pogledaj_izjavu 	= "Pogledaj izjavu o zaštiti privatnosti";
			$txt_prava 				= "Sva prava pridržana";
			$txt_text_na_dnu 		= "JOBSTEP";
			$txt_izjava_privatnost 	= "Izjava o zaštiti privatnosti";
			$txt_zatvori 			= "Zatvori";
			// $txt_full_izjava 		= "Slanjem ovog obrasca dajete svoju saglasnost da Vaši podaci budu registrovani u našoj bazi podataka, pri čemu će isti biti dostupni pravnom licu JobStep Int GmbH i njegovim poslovnim saradnicima (partnerima). JobStep Int GmbH obavezan je čuvati privatnost svojih korisnika i osigurati visok stepen sigurnosti i povjerljivosti ličnih podataka prikupljenih od potencijalnih kandidata, a u skladu sa Zakonom o zaštiti ličnih podataka Švicarske i drugim relevantnim domaćim i evropskim zakonima i podzakonskim aktima. Lični podaci koji budu predmet obrade predstavljaju službenu tajnu. Lični i svi drugi podaci iz ovog obrasca koje kandidat unese u obrascu ili u bilo kojoj drugoj formi, biće korišteni isključivo od strane ovlaštenih lica u procesu procjene kandidata i posredovanja prilikom zapošljavanja.";
			$txt_full_izjava 		= "Hvala vam na interesovanju za prijavu na Jobstep Int GmbH. Zaštita vaših ličnih podataka nam je važna. U nastavku vas želimo obavijestiti o tome kako postupamo s vašim podacima:
										<br>
										<br>
										1. Prikupljanje podataka: Prikupljamo lične podatke koje nam dostavite kao dio vaše prijave. Ovo uključuje, ali nije ograničeno na, vaše ime, kontakt informacije, profesionalno iskustvo i kvalifikacije.
										<br>
										<br>
										2. Upotreba vaših podataka: Podaci koje navedete koristit će se samo kao dio procesa prijave. Vaši podaci će se tretirati povjerljivo i neće biti otkriveni trećim licima bez vašeg pristanka.
										<br>
										<br>
										3. Pohrana podataka: Vaši podaci će biti pohranjeni za vrijeme trajanja procesa prijave. U slučaju uspješnog zaposlenja, vaši podaci će biti prebačeni u našu bazu podataka o zaposlenima.
										<br>
										<br>
										4. Sigurnost podataka: Sprovodimo odgovarajuće sigurnosne mjere kako bismo zaštitili vaše podatke od neovlaštenog pristupa.
										<br>
										<br>
										5. Prava aplikanta: Imate pravo zatražiti informacije o svojim pohranjenim podacima, ispraviti ih ili izbrisati. Za sva pitanja kontaktirajte nas na <b>info@job-step.com</b>.
										<br>
										<br>
										Podnošenjem prijave slažete se sa gore navedenim uslovima.
										Hvala vam na povjerenju u Jobstep Int GmbH.";
			$txt_day				= "DD";	
			$txt_month				= "MM";	
			$txt_year				= "GGGG";
			$txt_fakultet 			= "Fakultet";			
			$txt_srednja_skola 		= "Srednja škola";
			$txt_nivo_obrazovanja   = "Nivo obrazovanja";
			$txt_njemacki   		= "Njemački";
			$txt_engleski   		= "Engleski";
			$txt_hrvatski   		= "Hrvatski";
			$txt_srpski		   		= "Srpski";
			$txt_bosanski	   		= "Bosanski";
			$txt_francuski	   		= "Francuski";
			$txt_talijanski	   		= "Italijanski";
			$txt_vise_jezika   		= "Dodaj još jedan";
			$txt_maternji   		= "Maternji";	
			$txt_odaberi_opciju		= "Odaberi opciju";
			$txt_neispravan_broj   	= "Netačan broj";
			$txt_neispravan_country_code   	= "Netačan pozivni broj";
			$txt_predug_broj   		= "Broj je predug";
			$txt_prekratak_broj   	= "Broj je prekratak";
			$txt_netacan_broj   	= "Netačan broj";
			$txt_valid_number   	= "Tačan broj";
			$txt_ispunite_polje     = "Ispunite ovo polje";			
		}else if($lg_language == "hr"){
			$txt_jezik 				= "Jezik";
			$txt_prijava 			= "Prijava za posao";
			$txt_popunite_formu 	= "Da bi ste se prijavili za posao, popunite navedenu formu.";
			$txt_korak 				= "Korak";
			$txt_osnovne_info 		= "Osnovne informacije";
			$txt_ime 				= "Ime";
			$txt_prezime 			= "Prezime";
			$txt_datum_rod 			= "Datum rođenja";
			$txt_mobilni 			= "Mobilni telefon";
			$txt_nivo_jezika		= "Nivo poznavanja jezika";
			$txt_nivo_njemackog 	= "Nivo poznavanja njemačkog jezika";
			$txt_nivo_engleskog 	= "Nivo poznavanja engleskog jezika";
			$txt_odaberi 			= "Odaberi";
			$txt_bez_znanja 		= "Bez znanja";
			$txt_vozacka_pitanje 	= "Da li imate vozačku dozvolu";
			$txt_ne 				= "Ne";
			$txt_da 				= "Da";
			$txt_zavrseno_obr_smjer = "Završeno obrazovanje/smjer";
			$txt_zavrseno_obr 		= "Završeno obrazovanje";
			$txt_zvanje_smjer 		= "Zvanje/smijer";
			$txt_ostalo 			= "Ostalo";
			$txt_naziv_skole 		= "Naziv škole";
			$txt_unesite_smjer 		= "Unesite smjer";
			$txt_iskustvo_struka 	= "Da li imate radnog iskustva u struci?";
			$txt_iskustvo_5_god 	= "Radno iskustvo u struci u posljednjih 5 godina?";
			$txt_nema_5_god 		= "Nemam iskustva u posljednjih 5 godina";
			$txt_manje_1_god 		= "Manje od 1 godine";
			$txt_godinu 			= "godinu";
			$txt_godine 			= "godine";
			$txt_godina 			= "godina";
			$txt_drzavljanstvo 		= "Državljanstvo";
			$txt_državljanin 		= "državljanin";
			$txt_nastavi 			= "Nastavi";
			$txt_sva_polja 			= "Sva polja označena sa <span class='zvjezdica'>*</span> su obavezna";
			$txt_nastavkom 			= "Nastavkom prihvatate uslove o ";
			$txt_nastavkom_izjava 	= "Zaštiti privatnosti.";
			$txt_pogledaj_izjavu 	= "Pogledaj izjavu o zaštiti privatnosti";
			$txt_prava 				= "Sva prava pridržana";
			$txt_text_na_dnu 		= "JOBSTEP";
			$txt_izjava_privatnost 	= "Izjava o zaštiti privatnosti";
			$txt_zatvori 			= "Zatvori";
			// $txt_full_izjava 		= "Slanjem ovog obrasca dajete svoju saglasnost da Vaši podaci budu registrovani u našoj bazi podataka, pri čemu će isti biti dostupni pravnom licu JobStep Int GmbH i njegovim poslovnim saradnicima (partnerima). JobStep Int GmbH obavezan je čuvati privatnost svojih korisnika i osigurati visok stepen sigurnosti i povjerljivosti ličnih podataka prikupljenih od potencijalnih kandidata, a u skladu sa Zakonom o zaštiti ličnih podataka Švicarske i drugim relevantnim domaćim i evropskim zakonima i podzakonskim aktima. Lični podaci koji budu predmet obrade predstavljaju službenu tajnu. Lični i svi drugi podaci iz ovog obrasca koje kandidat unese u obrascu ili u bilo kojoj drugoj formi, biće korišteni isključivo od strane ovlaštenih lica u procesu procjene kandidata i posredovanja prilikom zapošljavanja.";
			$txt_full_izjava 		= "Hvala vam na interesovanju za prijavu na Jobstep Int GmbH. Zaštita vaših ličnih podataka nam je važna. U nastavku vas želimo obavijestiti o tome kako postupamo s vašim podacima:
										<br>
										<br>
										1. Prikupljanje podataka: Prikupljamo lične podatke koje nam dostavite kao dio vaše prijave. Ovo uključuje, ali nije ograničeno na, vaše ime, kontakt informacije, profesionalno iskustvo i kvalifikacije.
										<br>
										<br>
										2. Upotreba vaših podataka: Podaci koje navedete koristit će se samo kao dio procesa prijave. Vaši podaci će se tretirati povjerljivo i neće biti otkriveni trećim licima bez vašeg pristanka.
										<br>
										<br>
										3. Pohrana podataka: Vaši podaci će biti pohranjeni za vrijeme trajanja procesa prijave. U slučaju uspješnog zaposlenja, vaši podaci će biti prebačeni u našu bazu podataka o zaposlenima.
										<br>
										<br>
										4. Sigurnost podataka: Sprovodimo odgovarajuće sigurnosne mjere kako bismo zaštitili vaše podatke od neovlaštenog pristupa.
										<br>
										<br>
										5. Prava aplikanta: Imate pravo zatražiti informacije o svojim pohranjenim podacima, ispraviti ih ili izbrisati. Za sva pitanja kontaktirajte nas na <b>info@job-step.com</b>.
										<br>
										<br>
										Podnošenjem prijave slažete se sa gore navedenim uslovima.
										Hvala vam na povjerenju u Jobstep Int GmbH.";
			$txt_day				= "DD";	
			$txt_month				= "MM";	
			$txt_year				= "GGGG";
			$txt_fakultet 			= "Fakultet";			
			$txt_srednja_skola 		= "Srednja škola";
			$txt_nivo_obrazovanja   = "Nivo obrazovanja";
			$txt_njemacki   		= "Njemački";
			$txt_engleski   		= "Engleski";
			$txt_hrvatski   		= "Hrvatski";
			$txt_srpski		   		= "Srpski";
			$txt_bosanski	   		= "Bosanski";
			$txt_francuski	   		= "Francuski";
			$txt_talijanski	   		= "Italijanski";
			$txt_vise_jezika   		= "Dodaj još jedan";
			$txt_maternji   		= "Maternji";			
			$txt_odaberi_opciju		= "Odaberi opciju";	
			$txt_neispravan_broj   	= "Netačan broj";
			$txt_neispravan_country_code   	= "Netačan pozivni broj";
			$txt_predug_broj   		= "Broj je predug";
			$txt_prekratak_broj   	= "Broj je prekratak";
			$txt_netacan_broj   	= "Netačan broj";
			$txt_valid_number   	= "Tačan broj";
			$txt_ispunite_polje     = "Ispunite ovo polje";			
		}else{
			$txt_jezik 				= "Sprache";
			$txt_prijava 			= "Bewerbung";
			$txt_popunite_formu 	= "Um sich für die Stelle zu bewerben, füllen Sie bitte das angegebene Formular aus";
			$txt_korak 				= "Schritt";
			$txt_osnovne_info 		= "Grundinformationen";
			$txt_ime 				= "Vorname";
			$txt_prezime 			= "Nachname";
			$txt_datum_rod 			= "Geburtsdatum";
			$txt_mobilni 			= "Handynummer";
			$txt_nivo_jezika		= "Sprachkenntnisse";
			$txt_nivo_njemackog 	= "Deutschkenntnisse";
			$txt_nivo_engleskog 	= "Englischkenntnisse";
			$txt_odaberi 			= "Wählen";
			$txt_bez_znanja 		= "Ohne Wissen";
			$txt_vozacka_pitanje 	= "Haben Sie einen Führerschein";
			$txt_ne 				= "Nein";
			$txt_da 				= "Ja";
			$txt_zavrseno_obr_smjer = "Abgeschlossene Ausbildung/Richtung";
			$txt_zavrseno_obr 		= "Abgeschlossene Ausbildung";
			$txt_zvanje_smjer 		= "Titel/Richtung";
			$txt_ostalo 			= "Sonstiges";
			$txt_naziv_skole 		= "Name der Schule";
			$txt_unesite_smjer		= "Geben Sie die Richtung ein";
			$txt_iskustvo_struka 	= "Haben Sie Berufserfahrung im ausgebildeten Beruf?";
			$txt_iskustvo_5_god 	= "Berufserfahrung im Fachgebiet in den letzten 5 Jahren";
			$txt_nema_5_god 		= "Ich habe keine Erfahrung in den letzten 5 Jahren";
			$txt_manje_1_god 		= "Weniger als 1 Jahr";
			$txt_godinu 			= "Jahr";
			$txt_godine 			= "Jahre";
			$txt_godina 			= "Jahre";
			$txt_drzavljanstvo 		= "Staatsangehörigkeit";
			$txt_državljanin 		= "Staatsbürger";
			$txt_nastavi 			= "Absenden";
			$txt_sva_polja 			= "Alle mit * gekennzeichneten Felder sind Pflichtfelder";
			$txt_nastavkom 			= "Indem Sie fortfahren, akzeptieren Sie die ";
			$txt_nastavkom_izjava 	= "Datenschutzerklärung.";
			$txt_pogledaj_izjavu 	= "Sehen Sie sich die Datenschutzerklärung an";
			$txt_prava	 			= "Alle Rechte vorbehalten";
			$txt_text_na_dnu 		= "JOBSTEP";
			$txt_izjava_privatnost 	= "Datenschutzerklärung";
			$txt_zatvori 			= "Schließen";
			// $txt_full_izjava 		= "Mit dem Absenden dieses Formulars erklären Sie sich damit einverstanden, dass Ihre Daten in unserer Datenbank registriert werden, die der juristischen Person JobStep Int GmbH und ihren Geschäftspartnern zur Verfügung steht. JobStep Int GmbH verpflichtet sich, die Privatsphäre seiner Nutzer zu schützen und ein hohes Maß an Sicherheit und Vertraulichkeit der von potenziellen Kandidaten erfassten personenbezogenen Daten gemäß dem Gesetz zum Schutz personenbezogener Daten von Schweiz und anderen relevanten nationalen und europäischen Gesetzen zu gewährleisten und Vorschriften. Die verarbeiteten personenbezogenen Daten gelten als vertraulich. Persönliche und alle weiteren Daten aus diesem Formular, die der Kandidat in das Formular oder in ein anderes Formular eingibt, werden ausschließlich von autorisierten Personen zur Kandidatenbewertung und Arbeitsvermittlung verwendet.";
			$txt_full_izjava 		= "Vielen Dank für Ihr Interesse an einer Bewerbung bei der Jobstep Int GmbH. Der Schutz Ihrer persönlichen Daten ist uns wichtig. Nachfolgend möchten wir Sie darüber informieren, wie wir mit Ihren Daten umgehen:
										<br>
										<br>
										1. Datenerhebung: Wir erheben personenbezogene Daten, die Sie uns im Rahmen Ihrer Bewerbung zur Verfügung stellen. Dazu gehören unter anderem Name, Kontaktdaten, beruflicher Werdegang und Qualifikationen.
										<br>
										<br>
										2. Verwendung Ihrer Daten: Die von Ihnen bereitgestellten Daten werden ausschließlich für den Bewerbungsprozess genutzt. Ihre Informationen werden vertraulich behandelt und nicht ohne Ihre Zustimmung an Dritte weitergegeben.
										<br>
										<br>
										3. Speicherung: Ihre Daten werden für die Dauer des Bewerbungsprozesses gespeichert. Bei einer erfolgreichen Anstellung werden Ihre Informationen in unsere Mitarbeiterdatenbank übernommen.
										<br>
										<br>
										4. Datensicherheit: Wir treffen angemessene Sicherheitsmaßnahmen, um Ihre Daten vor unbefugtem Zugriff zu schützen.
										<br>
										<br>
										5. Rechte der Bewerber: Sie haben das Recht, Auskunft über Ihre gespeicherten Daten zu erhalten, diese zu korrigieren oder löschen zu lassen. Bitte kontaktieren Sie uns dazu unter <b>info@job-step.com</b>.
										<br>
										<br>
										Mit der Einreichung Ihrer Bewerbung erklären Sie sich mit den oben genannten Bedingungen einverstanden.
										Vielen Dank für Ihr Vertrauen in die Jobstep Int GmbH.";
			$txt_day				= "TT";
			$txt_month				= "MM";	
			$txt_year				= "J J J J";
			$txt_fakultet 			= "Hochschulabschluss/Studium";
			$txt_srednja_skola 		= "Berufsausbildung";
			$txt_nivo_obrazovanja   = "Ausbildung";
			$txt_njemacki   		= "Deutsch";
			$txt_engleski   		= "Englisch";
			$txt_hrvatski   		= "Kroatisch";
			$txt_srpski		   		= "Serbisch";
			$txt_bosanski	   		= "Bosnisch";
			$txt_francuski	   		= "Französisch";
			$txt_talijanski	   		= "Italienisch";
			$txt_vise_jezika   		= "Fügen Sie eine weitere hinzu";
			$txt_maternji   		= "Muttersprache";					
			$txt_odaberi_opciju   	= "Wähle eine Option";
			$txt_neispravan_broj   	= "Falsche Nummer";
			$txt_neispravan_country_code   	= "Ungültiger Ländercode";
			$txt_predug_broj   		= "Zu lange Nummer";
			$txt_prekratak_broj   	= "Zu kurze Nummer";
			$txt_netacan_broj   	= "Falsche Nummer";
			$txt_valid_number   	= "Korrekte Nummer";
			$txt_ispunite_polje     = "Feld ausfüllen";			
		}
	//Jezik forme - END

	
	?>
	<script>
		$(document).ready(function() {
			$('.selectpicker').selectpicker({});
            var urlid = <?php echo $urlid; ?>;
			$.ajax({
				url: '<?php getCRMUrl(); ?>public_kandidati.php?page=updBrojPregledaLinka',
				type: 'POST',
				data: {"link_id": urlid},
				dataType: 'html',
				success: function(data) {
					
				}
			});
		});
			
	</script>
	<style>
		body{
			font-family: Inter !important;
		}
		.custom-label{
			font-size: 14px !important;
			line-height: 20px !important;
			font-weight: 500 !important;
			color: #344054 !important;
			padding-top: 10px !important;
		}
		
		.lang_group{
			display: flex;  
			margin: 0px;
			gap: 10px;
		}
		
		.languageContainer{
			display: flex; 
			margin: 0px;
			gap: 10px;
		}
		
		.langtype-select {
			width: 100%;
			height: 44px;
			border: 1px solid #D0D5DD;
			background-color: #FFFFFF;
			color: #D0D5DD;
            padding: 10px 14px;
			border-radius: 8px;
			gap: 8px;
			color: #667085;
		}
		
		.langtype-select-main {
			width: 100%;
			height: 44px;
			border: 1px solid #D0D5DD;
			background-color: #FFFFFF;
			color: #D0D5DD;
            padding: 10px 14px;
			border-radius: 8px;
			gap: 8px;
			color: #667085;
		}
		
		.langlevel-select {
			width: 100%;
			height: 44px;
			border: 1px solid #D0D5DD;
			background-color: #FFFFFF;
			color: #D0D5DD;
            padding: 10px 14px;
			border-radius: 8px;
			gap: 8px;
			color: #667085;
			text-overflow: ellipsis;
			overflow: hidden !important;
			white-space: nowrap;
		}
		
		@media screen and (max-width: 345px) and (min-width: 320px) {
		  .langlevel-select {
			width: 98% !important;
			text-overflow: ellipsis;
			overflow: hidden !important;
			white-space: nowrap;
		  }
		}
		
		.langlevel_delete {
			min-width: 44px;
			height: 44px;
			border: 1px solid #D0D5DD;
			background-color: #FFFFFF;
			color: #344054;
			border-radius: 8px;
			gap: 8px;
			color: #344054;
			text-align: center;
			cursor: pointer;
			position: relative;
		}
		
		.select-posao{
			width: 100%;
			border-radius: 8px !important;
			border: 1px solid #D0D5DD !important;
			padding: 10px 14px !important;
			color: #D0D5DD !important;
		}
		
		.select-lang{
			width: 100%;
			height: 44px;
			border-radius: 8px;
			border: 1px solid #D0D5DD !important;
			padding: 10px 14px;
			color: #667085;
		}
		
       .submit-group{
		   text-align: right;
	   }
		
		.select-posao {			
			width: 100%;
			height: 44px;
			border: 1px solid rgb(0 0 0 / 74%);
			background-color: #FFFFFF;
            padding: 5px;
			color: #667085 !important;
		}
		.select-posao:focus{
			color: #101828 !important;
		}
		.input {
			width: 100%;
			height: 44px;
			border-radius: 8px;
			border: 1px solid #D0D5DD !important;
			padding: 10px 14px;
			gap: 8px;
			color: #101828;
			font-family: Inter !important;
		}
		.iti--container{
			width: 80% !important;
		}
		.iti{
			width: 100%;
		}
		.birth_group{
			
			display: flex;
			margin: 0px;
		}
		.dan-select {
			width: 100%;
			height: 44px;
			border: 1px solid #D0D5DD;
			background-color: #FFFFFF;
			color: #D0D5DD;
            padding: 10px 14px;
			border-radius: 8px;
			gap: 8px;
			color: #667085;
		}
		.mjesec-select {
			width: 100%;
			height: 44px;
			border: 1px solid #D0D5DD;
			background-color: #FFFFFF;
			color: #D0D5DD;
            padding: 10px 14px;
			border-radius: 8px;
			color: #667085;
		}
		.godina-select {
			width: 100%;
			height: 44px;
			border: 1px solid #D0D5DD;
			background-color: #FFFFFF;
			color: #D0D5DD;
            padding: 10px 14px;
			border-radius: 8px;
			gap: 8px;
			color: #667085;
		}
		.material-radio-group__check-radio{
			border: 1px solid #6097A0 !important;
		}
		.material-radio-group_success .material-radio-group__check-radio {
			border-color: #6097A0 !important;
			background-color: #EFF8FF !important;
		}
		.material-radio-group_success .material-radio-group__check-radio:after {
			background-color: #6097A0 !important;
			border-radius: 50% !important;
			width: 5px !important;
			height: 5px !important; 
		}
		
		.nastavi {
			width: 198px;
			padding: 10px 64px;
			height: 42px;
			background: #6097A0;
			color: #FFFFFF;
			font-weight: bold;
			margin-top: 10px;
			border: 1px solid #6097A0;
            border-radius: 8px;
			margin-bottom: 8px;
		}
		.more_lang_question{
			color: #475467;
			font-family: Inter;
			font-size: 14px;
			font-weight: 600;
			line-height: 20px;
		}
		.icon {
			background: url('/images/Vector.svg');
			width: 24px;
			height: 24px;
		}
        #prijavi_se_anchor {
            right: -100px;
        }
		
		
		@media (max-width: 991px) {
			
            #prijavi_se_anchor {
                height: 40px;
                display: block;
                background: #f4d82f;
                color: black;
                position: fixed;
                bottom: 100px;
                font-weight: bold;
                padding: 10px;
                right: 0px;
                line-height: 20px;
                z-index: 9999;
                cursor: pointer;
                text-decoration: none;
                transition: right .2s ease-out;
            }
			
			
		}
		
		.ui.search.dropdown>.text {
		  width:100%;
		  text-overflow: ellipsis;
		  overflow: hidden !important;
		  white-space: nowrap;
		  font-family: Inter;
		}
		
		
		@media (max-width: 480px) {
			.nastavi{
				width: 100%;
			}
			.txt-sva-polja{
				width: 100%;
			}
			.submit-group{
			   text-align: center;
			}
			#jobstep_logo{
				width: 96px;
				height: 110px;
				padding-top: 24px;
			}
			
		}
		
		#languageContainer {
			text-align: center;
		}

		#languageContainer .form-group {
			padding-right: 0;
		}

		#jobstep_logo{
			width: 140px;
			height: 160px;
			margin-top: 24px;
		}
		
		.top-15{
			padding-top: 15px !important;
		}
		
		.top-8{
			padding-top: 8px !important;
		}
		.ui.dropdown>.dropdown.icon:before{
			content: "\f107";
		}
		i.icon.dropdown:before{
			content: "\f107";
		}
		.ui.selection.dropdown{
			line-height: normal;
			color: #667085 !important;
		}
		.ui.dropdown:not(.button)>.default.text{
			color: #667085 !important;
		}
		.ui.selection.dropdown>.delete.icon, .ui.selection.dropdown>.dropdown.icon, .ui.selection.dropdown>.search.icon {
			color: #667085 !important;
			opacity: 1 !important;
			font-size: 18px !important;
			top: auto !important;
			margin: auto !important;
			padding: 0 !important;
			right: 5px;
			font-size: 14px !important;
			line-height: 20px !important;
			font-weight: 400 !important;
		}
		.ui.selection.active.dropdown .menu{
			border-color: none !important;
		}
		.ui.selection.active.dropdown:hover .menu{
			border: none !important;
		}
		.ui.search.dropdown>.text {
			font-size: 14px !important;
			line-height: 20px !important;
			color: #667085;
		}
		.nalog_title{
			color: #101828;
			font-weight: 500;
			font-family: Inter;
			font-size: 24px;
			line-height: 38px;
			overflow-wrap: break-word;
		}
		.nalog_location{
			color: #475467;
			font-weight: 400;
			font-family: Inter;
			font-size: 14px;
			line-height: 20px;
		}
		#markdownContainer{
			margin-bottom: 28px;
		}
		
		.langlevel_delete::before {
		  content: '\58'; 
		  color: #344054; 
		  position: absolute;
		  top: 50%;
		  left: 50%;
		  transform: translate(-50%, -50%);
		}
		.langlevel_delete::after {
		  content: '\58'; 
		  color: #344054; 
		  position: absolute;
		  top: 50%;
		  left: 50%;
		  transform: translate(-50%, -50%);
		}
		.custom-select{
			width: 100%;
			position: relative;
		}
		
		.custom-select select {
			appearance: none;
			-webkit-appearance: none;
			-moz-appearance: none;
			width: 100%;
			padding: 10px;
			cursor: pointer;
			font-family: Inter;
		}
		
		.custom-select select>option{
			color: #101828;
			font-family: Inter;
		}
		
		.material-radio-group__caption{
			font-family: Inter;
			font-size: 14px;
			font-style: normal;
			line-height: 20px;
		}

		.custom-select::after {
		  font-family: FontAwesome;
		  content: '\f078'; /* Unicode character for down arrow */
		  position: absolute;
		  top: 50%;
		  right: 10px;
		  transform: translateY(-50%);
		  pointer-events: none;
		  font-size: 10px;
		  color: #667085;
		}
		
		.importantColorBlack{
			color: #101828 !important;
		}

	</style>
	<body>
	<?php		
	
	?>
		<div id="content_public">
			<div class="container">
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <img id="jobstep_logo" class="img-responsive" src="https://crm.job-step.com/images/Jobstep_logo_new.png" alt="" >
                    </div>
				</div>
				<div class="row top-8">
				    <div class="col-xs-12 text-center">
						<p class="nalog_title"><?php echo $nalog_title;?></p>
					</div>
				</div>
				<div class="row top-8">
				    <div class="col-xs-12 text-center">
						<p class="nalog_location"><?php echo $nalog_location;?></p>
					</div>
				</div>
			</div>
			<hr>
			<div class="container">
                <div class="row">
                    <div class="col-xs-12">
                        <br />
						 <div class="form-group list-inline">
							<div class="row">
								<div class="col-md-2 pull-right">
									<label for="odabir_jezika" class="col-sm-3 control-label list-inline"><?php echo $txt_jezik; ?></label>
								</div>
							</div>
							<div class="row">
								<div class="col-md-2 col-sm-3 col-xs-5 pull-right">
									<form method="" action="" style="display:flex;" autocomplete="off">
										<div class="custom-select">
											<select autocomplete="off" class="select-lang pull-right" data-token="<?php echo $token_c;?>" data-urlid="<?php echo $urlid; ?>" id="odabir_jezika" name="lang" data-live-search="true" onchange="changeLang(this)" required>
												<option value="de" <?php if($lg_language == 'de') {echo "selected";}else{echo "";} ?>><?php echo $txt_njemacki;?></option>
												<option value="en" <?php if($lg_language == 'en') {echo "selected";}else{echo "";} ?>><?php echo $txt_engleski;?></option>
												<option value="bs" <?php if($lg_language == 'bs') {echo "selected";}else{echo "";} ?>><?php echo $txt_bosanski;?></option>
												<option value="sr" <?php if($lg_language == 'sr') {echo "selected";}else{echo "";} ?>><?php echo $txt_srpski;?></option>
											</select>
										</div>
									</form>
									<script>
										function changeLang(thisRow){
											
											var token = thisRow.dataset.token;
											var urlid = thisRow.dataset.urlid;
											var lang = thisRow.value;
											window.location.href = "<?php getSiteUrl(); ?>" + "job/" + urlid + "/" + token + "/" + lang;
											
										}
										(function () {
											window.onpageshow = function(event) {
												if (event.persisted) {
													window.location.reload();
												}
											};
										})()
									</script>
								</div>
							</div>
						</div>
                    </div>
                </div>
				<?php echo nl2br('<div id="markdownContainer"></div>'); ?>
				<script type="text/javascript">

				var htmlContent = marked.parse(`<?php echo $nalog_opis; ?>`);

				var container = document.getElementById("markdownContainer");
				container.innerHTML = htmlContent;
				</script>
                <script>
                    var token_za_slanje = "<?php echo $token_c; ?>";
                    var urlid_za_slanje = "<?php echo $urlid; ?>";
                    // console.log(urlid_za_slanje);
                    if(token_za_slanje != null){
                        // console.log("razlicit je");
                        $.ajax({
                            url: '<?php getCRMUrl(); ?>jobstep-partner/api?action=count',
                            type: 'POST',
                            data: {	'token': ''+token_za_slanje+'', 'id': ''+urlid_za_slanje+''	},
                            dataType: 'html',
                            success: function() {}
                        });
                    }
                </script>

                <br>

				<div class="row" id="anchor_prijavna_forma">
					<div class="col-sm-7">
						<h3><strong><?php echo $txt_prijava;?></strong></h3>
						<p><?php echo $txt_popunite_formu; ?></p>
					</div>
					<div class="col-sm-4">
						
					</div>
				</div>
				
				<div class="row" style="margin-top: 15px;">
					<div class="col-md-offset-2 col-md-8">
							<form action="<?php getCRMUrl(); ?>public_kandidati?page=add_kandidat_new_from_partner" method="post" enctype="multipart/form-data" class="form-horizontal" role="form" id="main_form">
                        	<!-- <form action="https://0689-213-91-100-21.ngrok-free.app/public_kandidati?page=add_kandidat_new_from_partner" method="post" enctype="multipart/form-data" class="form-horizontal" role="form" id="main_form">  -->
							<input type="hidden" id="countryCode" name="countryCode" value="<?php echo $countryCode; ?>">
							<input type="hidden" name="kandidat_visitedurl" value="<?php echo $kandidat_visitedurl; ?>">
							<input type="hidden" name="urlid" value="<?php echo $urlid; ?>">
							<input type="hidden" name="token" value="<?php echo $token_c; ?>">
							<input type="hidden" name="lg_language" value="<?php echo $lg_language; ?>">
							<input type="hidden" id="countryCode" name="countryCode" value="<?php echo $countryCode; ?>">
							<!-- Podatak ispod govori backendu na public_kandidati da li se radi o skraćenoj/izmijenjenoj formi  -->
                            <input type="hidden" id="carglass_form" name="carglass_form" value="0">
							
                            <!-- Kandidat_prijava_na ili grupa će uvijek biti sakrivena, api-em će se kupiti ono sto je naznačeno u linku i ako
                                postoji više grupa biće selektovana prva. Manje bitna informacija a i većinom je samo jedna grupa naznačena u linku
                                i ostavićemo ovako umjesto da skroz brišemo ovaj input. -->
                            <div class="form-group" style="display: none;">
								<label for="kandidat_prijava_na" class="col-sm-5 control-label"><strong>RADNO MJESTO NA KOJE SE PRIJAVLJUJETE</strong><span class="zvjezdica">*</span><strong>:</strong></label>
								<div class="col-sm-6">
									<select class="select-posao" id="kandidat_prijava_na" name="kandidat_prijava_na" >
									<?php 
										if($urlid != 0){
											$putanja_return = "return_groups_select";
										}else{
											$putanja_return = "return_all_groups_select";
										}
									
									?>
									<script>
										$(document).ready(function() {
											var urlid = <?php echo $urlid; ?>;
											var putanja = '<?php getCRMUrl(); ?>' + 'public_kandidati_import.php?page=' + '<?php echo $putanja_return; ?>';
											$.ajax({
												url: putanja,
												type: 'POST',
												data: {"link_id": urlid},
												dataType: 'html',
												success: function(data) {
													$('#kandidat_prijava_na').append(data);
												}
											});
										});
											
									</script>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="kandidat_ime" class="col-sm-5 control-label custom-label"><?php echo $txt_ime; ?>*:</label>
								<div class="col-sm-7">
									<input class="input" type="text" name="kandidat_ime" id="kandidat_ime" placeholder="<?php echo $txt_ime; ?>" oninvalid="this.setCustomValidity('<?php echo $txt_ispunite_polje; ?>')" onchange="this.setCustomValidity('')" required>
								</div>
							</div>
							<div class="form-group">
								<label for="kandidat_prezime" class="col-sm-5 control-label custom-label"><?php echo $txt_prezime; ?>*:</label>
								<div class="col-sm-7">
									<input class="input" type="text" name="kandidat_prezime" id="kandidat_prezime" placeholder="<?php echo $txt_prezime; ?>" oninvalid="this.setCustomValidity('<?php echo $txt_ispunite_polje; ?>')" onchange="this.setCustomValidity('')" required>
								</div>
							</div>
							<script>
							  document.getElementById('main_form').addEventListener('submit', function(event) {
								var prezimeInput = $("#kandidat_prezime")[0];
								var imeInput = $("#kandidat_ime")[0];
								var prezimeValue = $.trim(prezimeInput.value);
								var imeValue = $.trim(imeInput.value);

								// Check if the value is not an empty string
								if (prezimeValue === "") {
									prezimeInput.setCustomValidity('<?php echo $txt_ispunite_polje; ?>');
									event.preventDefault();
								} else {
									prezimeInput.setCustomValidity('');
								}
								
								if(imeValue === ""){
									imeInput.setCustomValidity('<?php echo $txt_ispunite_polje; ?>');
									event.preventDefault();
								}else{
									imeInput.setCustomValidity('');
								}
							  });
							</script>
							<div class="form-group">
								<label for="datum_dan" class="col-sm-5 control-label custom-label"><?php echo $txt_datum_rod; ?>*:</label>
								<div class="col-sm-7">
									<div class="birth_group">
										<div class="custom-select">
											<select class="dan-select" name="datum_dan" id="datum_dan" oninvalid="this.setCustomValidity('<?php echo $txt_ispunite_polje; ?>')" onchange="this.setCustomValidity('')" required>
												<option value="" selected disabled><?php echo $txt_day;?></option>
												<?php 
												for ($day=1; $day<=31; $day++){ ?>
													<option value="<?php echo $day; ?>"><?php echo $day; ?></option>
												<?php } ?>
											</select>
										</div>
										<div class="custom-select" style="margin-left: 8px;margin-right: 8px;">
											<select class="mjesec-select" name="datum_mjesec" id="datum_mjesec" oninvalid="this.setCustomValidity('<?php echo $txt_ispunite_polje; ?>')" onchange="this.setCustomValidity('')" required>
												<option value="" selected disabled><?php echo $txt_month;?></option>
												<?php 
												for ($month=1; $month<=12; $month++){ ?>
													<option value="<?php echo $month; ?>"><?php echo $month; ?></option>
												<?php } ?>
											</select>
										</div>
										<div class="custom-select">
											<select class="godina-select" name="datum_godina" id="datum_godina" oninvalid="this.setCustomValidity('<?php echo $txt_ispunite_polje; ?>')" onchange="this.setCustomValidity('')" required>
												<option value="" selected disabled><?php echo $txt_year;?></option>
												<?php 
												for ($year=2006; $year>1940; $year--){ ?>
													<option value="<?php echo $year; ?>"><?php echo $year; ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
								</div>
							</div>
							<script>
							$('.custom-select select').change(function() {
							  $(this).addClass('importantColorBlack');
							});
							</script>
							<div class="form-group">
								<label for="kki_phone2" class="col-sm-5 control-label custom-label"><?php echo $txt_mobilni; ?>*:</label>
								<div class="col-sm-7">
									<div class="">
										<input class="input" type="hidden" name="kki_phone" id="kki_phone" >
										<input class="input" type="tel" name="kki_phone2" id="kki_phone2" placeholder="61 123 456" onkeyup="this.value = this.value.replace(/[^0-9-]/g, '');" oninvalid="this.setCustomValidity('<?php echo $txt_ispunite_polje; ?>')" onchange="this.setCustomValidity('')" required>
									</div>
									<!--<p id="valid-msg" class="hide"><?php echo $txt_valid_number;?></p>-->
									<p id="error-msg" style="color: red; margin-top: 5px;" class="hide"></p>
								</div>
							</div>
							
							<script>
								$( document ).ready(function($) {
									
									var telInput = document.querySelector("#kki_phone2");
									var countryCode = document.getElementById("countryCode").value;

									iti = window.intlTelInput(telInput, {
										utilsScript: "https://crm.job-step.com/buildTelInput/js/utils.js",
										initialCountry: "DE",
										autoPlaceholder: "aggressive",
										preferredCountries: ["de","ba","hr","it","rs"],
										formatOnDisplay: true,
										separateDialCode: true,
										showFlags:false
									});
									var fullNumber = iti.getNumber();
									$('input[type=tel]').on('change', function() {
                                        console.log(iti.getNumber());
                                    });
									$("#main_form").submit(function(event) {
										
										const input = document.querySelector("#kki_phone2");
										const errorMsg = document.querySelector("#error-msg");
										// const validMsg = document.querySelector("#valid-msg");

										// here, the index maps to the error code returned from getValidationError - see readme
										const errorMap = ["<?php echo $txt_neispravan_broj; ?>", "<?php echo $txt_neispravan_country_code; ?>", "<?php echo $txt_prekratak_broj; ?>", "<?php echo $txt_predug_broj; ?>", "<?php echo $txt_netacan_broj; ?>"];

										

										const reset = () => {
										  input.classList.remove("error");
										  errorMsg.innerHTML = "";
										  errorMsg.classList.add("hide");
										  // validMsg.classList.add("hide");
										};

										  reset();
										  if (input.value.trim()) {
											if (iti.isValidNumber()) {
											  // validMsg.classList.remove("hide");
											  $("#kki_phone").val(iti.getNumber());
											} else {
											  input.classList.add("error");
											  const errorCode = iti.getValidationError();
											  // vraca -99 kad je samo jedan broj ukucan
											  if(errorCode == "-99"){
												errorMsg.innerHTML = "<?php echo $txt_netacan_broj;?>";
												errorMsg.classList.remove("hide");												
											  }else{
												errorMsg.innerHTML = errorMap[errorCode];
												errorMsg.classList.remove("hide");
											  }
												// Set focus on the input
											    $("#kki_phone2").focus();

												event.preventDefault();
											}
										  }

										// on keyup / change flag: reset
										input.addEventListener('change', reset);
										input.addEventListener('keyup', reset);
										
									});
									
								});
							</script>
                            
                            <!-- JEZIK -->
							<input type="hidden" id="langCounterInput" name="lang_counter" value="" />
							<div class="form-group">
                                <label for="kandidat_jezik_tip" class="col-sm-5 control-label custom-label"><?php echo $txt_nivo_jezika; ?>*:</label>
                                <div class="col-sm-7">
									<div class="lang_group">
										<div class="custom-select">
											<select class="langtype-select-main" id="kandidat_jezik_tip" name="tip_jezika"  data-live-search="true" oninvalid="this.setCustomValidity('<?php echo $txt_ispunite_polje; ?>')" onchange="this.setCustomValidity('')" required>
												<option value="de"><?php echo $txt_njemacki;?></option>
											</select>
										</div>
										<div class="custom-select">
											<select class="langlevel-select" id="kandidat_jezik_nivo" name="nivo_jezika"  data-live-search="true" oninvalid="this.setCustomValidity('<?php echo $txt_ispunite_polje; ?>')" onchange="this.setCustomValidity('')" required>
												<option value selected disabled><?php echo $txt_odaberi;?>...</option>
												<option value="C2">C2 - <?php echo $txt_maternji;?></option>
												<option value="C1">C1</option>
												<option value="B2">B2</option>
												<option value="B1">B1</option>
												<option value="A2">A2</option>
												<option value="A1">A1</option>
												<option value="Bez znanja"><?php echo $txt_bez_znanja;?></option>
											</select>
										</div>
									</div>
                                </div>
                            </div>
							
							<script>
							$('.langtype-select-main').change(function() {
							  $(this).addClass('importantColorBlack');
							});
							$('.langlevel-select').change(function() {
							  $(this).addClass('importantColorBlack');
							});
							</script>

							<div id="languageContainer">
								<!-- Cloned language selection fields will be appended here -->
							</div>
							
							<div class="form-group">
                                <label for="kandidat_jezik" class="col-sm-5 control-label custom-label"></label>
                                <div class="col-sm-7">
									<p class="more_lang_question"><span onclick="addLanguageFields()" style="cursor: pointer;">+ <?php echo $txt_vise_jezika;?></span></p>
                                </div>
                            </div>
							
							<script>
							  let languageCounter = 0;

							  function addLanguageFields() {
								if (languageCounter >= 3) {
								  // Maximum of 3 languages reached, disable further additions
								  document.querySelector('.more_lang_question span').style.display = 'none';
								  return;
								}

								languageCounter++;
								$("#langCounterInput").val(languageCounter)
								
								// Create a new "form-group" div
								let newLanguageDiv = document.createElement('div');
								newLanguageDiv.className = 'form-group';

								// Create and append label
								let label = document.createElement('label');
								label.className = 'col-sm-5 control-label';
								label.htmlFor = 'kandidat_jezik_' + languageCounter;
								newLanguageDiv.appendChild(label);

								// Create and append the col-sm-7 div
								let colSm7Div = document.createElement('div');
								colSm7Div.className = 'col-sm-7';

								// Create and append the language group
								let langGroup = document.createElement('div');
								langGroup.className = 'lang_group';
								
								// Create and append the custom-select
								let customSelect = document.createElement('div');
								customSelect.className = 'custom-select';
								
								langGroup.appendChild(customSelect);

								// Create and append the language type select
								let langTypeSelect = createLanguageSelect('langtype-select', 'kandidat_jezik_' + languageCounter);

								// Prevent selecting languages that are already chosen
								let selectedLanguages = Array.from(document.querySelectorAll('.langtype-select')).map(select => select.value);
								selectedLanguages = selectedLanguages.filter(language => language !== ''); // Remove empty option
								populateLanguageOptions(langTypeSelect, selectedLanguages);

								customSelect.appendChild(langTypeSelect);

								// Create and append the language level select
								let langLevelSelect = createLanguageSelect('langlevel-select', 'nivo_jezika_' + languageCounter);
								langLevelSelect.appendChild(createLanguageOption('', '<?php echo $txt_odaberi; ?>...'));
								langLevelSelect.appendChild(createLanguageOption('C2', 'C2-<?php echo $txt_maternji;?>'));
								langLevelSelect.appendChild(createLanguageOption('C1', 'C1'));
								langLevelSelect.appendChild(createLanguageOption('B2', 'B2'));
								langLevelSelect.appendChild(createLanguageOption('B1', 'B1'));
								langLevelSelect.appendChild(createLanguageOption('A2', 'A2'));
								langLevelSelect.appendChild(createLanguageOption('A1', 'A1'));
								langLevelSelect.appendChild(createLanguageOption('Bez znanja', '<?php echo $txt_bez_znanja; ?>'));
								// Add more language levels here
								
								// Create and append the custom-select
								let customSelect2 = document.createElement('div');
								customSelect2.className = 'custom-select';
								
								langGroup.appendChild(customSelect2);
								
								customSelect2.appendChild(langLevelSelect);

								// Create and append the delete button
								let deleteButton = document.createElement('div');
								deleteButton.className = 'langlevel_delete';
								
								deleteButton.addEventListener('click', function () {
								  removeLanguageRow(newLanguageDiv);
								  enableAddLanguageButton();
								});
								langGroup.appendChild(deleteButton);
								
								// Add onchange event handler to the language select
								langTypeSelect.addEventListener('change', function () {
								  langTypeSelect.classList.add('importantColorBlack');
								  const selectedLanguage = langTypeSelect.value;
								  if (selectedLanguage !== '') {
									// Remove the selected language from other rows
									removeSelectedLanguageFromOtherRows(selectedLanguage, langTypeSelect);
								  }
								});
								
								
								langLevelSelect.addEventListener('change', function () {
								  langLevelSelect.classList.add('importantColorBlack');
								});

								colSm7Div.appendChild(langGroup);
								newLanguageDiv.appendChild(colSm7Div);

								// Assign the name attribute the same value as htmlFor
								langTypeSelect.name = 'tip_jezika_' + languageCounter;
								langLevelSelect.name = 'nivo_jezika_' + languageCounter;

								// Append the new "form-group" div to the container
								document.getElementById('languageContainer').appendChild(newLanguageDiv);

								// Disable the "Add" button if the maximum number of languages is reached
								if (languageCounter >= 3) {
								  document.querySelector('.more_lang_question span').style.display = 'none';
								}
							  }

							  function createLanguageSelect(className, id) {
								let select = document.createElement('select');
								select.className = className;
								select.id = id;
								select.name = 'nivo_jezika';
								select.setAttribute('data-live-search', 'true');
								select.required = true;
								// Add oninvalid and onchange event handlers
								select.setAttribute('oninvalid', 'this.setCustomValidity(\'<?php echo $txt_ispunite_polje; ?>\')');
								select.setAttribute('onchange', 'this.setCustomValidity(\'\')');
								return select;
							  }

							  function createLanguageOption(value, text) {
								let option = document.createElement('option');
								option.value = value;
								option.textContent = text;
								return option;
							  }

							  function removeLanguageRow(row) {
								  if (languageCounter > 0) {
									languageCounter--;
									$("#langCounterInput").val(languageCounter);

									// Get the removed language option
									const langTypeSelect = row.querySelector('.langtype-select');
									const removedLanguageOption = langTypeSelect.options[langTypeSelect.selectedIndex];

									// Remove the row from the container
									document.getElementById('languageContainer').removeChild(row);
									enableAddLanguageButton();

									// Re-add the removed language option to all other selects
									const otherSelects = document.querySelectorAll('.langtype-select');
									otherSelects.forEach((select) => {
									  if (select !== langTypeSelect) {
										select.appendChild(removedLanguageOption.cloneNode(true));
									  }
									});
								  }
								}

							  function enableAddLanguageButton() {
								// Enable the "Add" button if it's disabled
								const addLanguageButton = document.querySelector('.more_lang_question span');
								addLanguageButton.style.display = 'inline';
							  }

							  function populateLanguageOptions(select, selectedLanguages) {
								// Populate language options, excluding the selected languages and the placeholder
								const availableLanguages = [
								  { value: '', text: '<?php echo $txt_odaberi; ?>' },
								  { value: 'en', text: '<?php echo $txt_engleski; ?>' },
								  { value: 'fr', text: '<?php echo $txt_francuski; ?>' },
								  { value: 'it', text: '<?php echo $txt_talijanski; ?>' },
								  // Add more languages here
								];

								availableLanguages.forEach((language) => {
								  if (!selectedLanguages.includes(language.value)) {
									const option = createLanguageOption(language.value, language.text);
									select.appendChild(option);
								  }
								});
							  }
							   function removeSelectedLanguageFromOtherRows(selectedLanguage, currentSelect) {
									const otherSelects = document.querySelectorAll('.langtype-select');
									otherSelects.forEach((select) => {
									  if (select !== currentSelect) {
										const optionToRemove = select.querySelector('option[value="' + selectedLanguage + '"]');
										if (optionToRemove) {
										  select.removeChild(optionToRemove);
										}
									  }
									});
								  }
							</script>


							<!-- AUSBUILDUNG -->
							<div class="form-group" id="nivo_obrazovanja_select">
								<label for="nivo_obrazovanja" class="col-sm-5 control-label custom-label"><?php echo $txt_nivo_obrazovanja; ?>*:</label>
								<div class="col-sm-7">
									<div class="custom-select">
										<select class="select-posao" name="nivo_obrazovanja" id="nivo_obrazovanja" oninvalid="this.setCustomValidity('<?php echo $txt_ispunite_polje; ?>')" onchange="this.setCustomValidity('')" required>
											<option value="" selected disabled><?php echo $txt_odaberi; ?>...</option>
											<option value="1"><?php echo $txt_fakultet; ?></option>
											<option value="2"><?php echo $txt_srednja_skola; ?></option>
										</select>
									</div>
								</div>
                            </div>
							
                            <div class="pick_nalog_schools" style="display:block;">
                                <div class="form-group" id="smjer_select">
                                    <label for="nalog_smjer_naziv" class="col-sm-5 control-label custom-label"><?php echo $txt_zavrseno_obr_smjer?>*:</label>
                                    <div class="col-sm-7">
										<div class="custom-select">
                                        <select class="select-posao search change_nalog_smjer_naziv" name="smjer_naziv" id="nalog_smjer_naziv" data-live-search="true"  data-style="btn-link" placeholder="">
                                            <option value disabled value=""><?php echo $txt_odaberi; ?>...</option>
                                        </select> 
										</div>
                                    </div>
									<div class="col-sm-5">
									</div>
									<div class="col-sm-7">
										<p id="selectionErrorSmjerNaziv" style="color: red; display: none;"><?php echo $txt_odaberi_opciju;?></p>
									</div>
                                </div>
                            </div>
							
							<script>
									$(document).ready(function() {
										
										$('#nivo_obrazovanja').change(function() {
											$("#nivo_obrazovanja").addClass('importantColorBlack');
											var nivo_obrazovanja = $("#nivo_obrazovanja").val();
											var urlid = '<?php echo $urlid; ?>';
											var lang = '<?php echo $lg_language; ?>';
											
											$.ajax({
												url: '<?php getCRMUrl(); ?>' + 'public_kandidati_import.php?page=return_smjerove_for_school_level',
												type: 'POST',
												data: {
													'nivo_obrazovanja': nivo_obrazovanja,
													'urlid': urlid,
													'lang': lang
												},
												dataType: 'html',
												success: function(result) {
													$('#nalog_smjer_naziv').html('');
													
													$('#nalog_smjer_naziv').dropdown('clear');

													$('#nalog_smjer_naziv').append('<option value="">Select an option</option>');

													$('#nalog_smjer_naziv').append(result);											
												},
												error: function (xhr, textStatus, errorThrown) {
													console.log(textStatus);
												}
											});
										});
										
										$('#nalog_smjer_naziv').dropdown({
											"clearable": true
										});
										$('i').removeClass('icon');
									});
							</script>
							<script>
							  document.getElementById('main_form').addEventListener('submit', function(event) {
								const nalog_smjer_naziv_value = $('#nalog_smjer_naziv').val();
								const selectionErrorSmjerNaziv = document.getElementById('selectionErrorSmjerNaziv');

								if (nalog_smjer_naziv_value == "") {
								  selectionErrorSmjerNaziv.style.display = 'flex';
								  event.preventDefault();
								  $('html,body').animate({
									scrollTop: $("#smjer_select").offset().top},
									'slow');
								} else {
								  selectionErrorSmjerNaziv.style.display = 'none';
								}
							  });
							  
							  
							   $('#nalog_smjer_naziv').change(function() {
								   	$(".ui.search.dropdown>.text").addClass('importantColorBlack');
									const selectionErrorSmjerNaziv = document.getElementById('selectionErrorSmjerNaziv');
									 selectionErrorSmjerNaziv.style.display = 'none';
								});
							</script>
							
							<!-- Ručni unos škole i smjera -->
							
							<div class="form-group" id="unos_smjera" style="display: none;">
								<label for="smjer_naziv_ru" class="col-sm-5 control-label custom-label"><?php echo $txt_unesite_smjer; ?>:</label>
								<div class="col-sm-7">
									<input class="input" type="text" name="smjer_naziv_ru" id="smjer_naziv_ru" placeholder="<?php echo $txt_unesite_smjer; ?>" >
								</div>
							</div>
							
							<!-- KATEGORIJA VOZAČKE -->
                            <div class="form-group" style="display: block">
                                <label for="kandidat_kategorija_vozacke" class="col-sm-5 control-label custom-label"><?php echo $txt_vozacka_pitanje?>*:</label>
                                <div class="col-sm-7">
									<div class="custom-select">
                                    <select class="select-posao" id="kandidat_kategorija_vozacke" name="kandidat_kategorija_vozacke" data-live-search="true" required >
                                        <option value selected disabled><?php echo $txt_odaberi; ?>...</option>
                                        <option value="Ne"><?php echo $txt_ne; ?></option>
                                        <option value="B">B</option>
                                        <option value="C1">C1</option>
                                        <option value="C">C</option>
                                        <option value="BE">BE</option>
                                        <option value="C1E">C1E</option>
                                        <option value="CE" >CE</option>
                                    </select>
									</div>
                                </div>
                            </div>
							
							<script>
							$('#kandidat_kategorija_vozacke').change(function() {
								$("#kandidat_kategorija_vozacke").addClass('importantColorBlack');
							});
							</script>
                            
                            <!-- RADNO ISKUSTVO U STRUCI -->
							<div class="form-group" id="iskustvo_u_struci_group" style="display: block">
									<label for="iskustvo_u_struci_da" class="col-sm-5 control-label custom-label"><?php echo $txt_iskustvo_struka; ?>*</label>
									<div class="top-15">
									<div class="col-sm-3">
										<label class="main-container__column material-radio-group material-radio-group_success" for="iskustvo_u_struci_da">
											<input type="radio" name="kandidat_iskustvo_u_struci" id="iskustvo_u_struci_da" class="material-radiobox" value="1" />
											<span class="material-radio-group__element material-radio-group__check-radio"></span>
											<span class="material-radio-group__element material-radio-group__caption"><?php echo $txt_da; ?></span>
										</label>
									</div>
									<div class="col-sm-4">
										<label class="main-container__column material-radio-group material-radio-group_success" for="iskustvo_u_struci_ne">
											<input type="radio" name="kandidat_iskustvo_u_struci" id="iskustvo_u_struci_ne" class="material-radiobox" value="0" />
											<span class="material-radio-group__element material-radio-group__check-radio"></span>
											<span class="material-radio-group__element material-radio-group__caption"><?php echo $txt_ne; ?></span>
										</label>
									</div>
									</div>
									<div class="col-sm-5">
									</div>
									<div class="col-sm-7">
										<p id="selectionError" style="color: red; display: none;"><?php echo $txt_odaberi_opciju;?></p>
									</div>
							</div>
							<script>
							  document.getElementById('main_form').addEventListener('submit', function(event) {
								const daInput = document.getElementById('iskustvo_u_struci_da');
								const neInput = document.getElementById('iskustvo_u_struci_ne');
								const selectionError = document.getElementById('selectionError');

								if (!daInput.checked && !neInput.checked) {
								  selectionError.style.display = 'flex';
								  event.preventDefault();
								} else {
								  selectionError.style.display = 'none';
								}
							  });
							</script>
							<div class="form-group" id="iskustvo_u_struci_trajanje_group" style="display: none;">
								<label for="iskustvo_u_struci_trajanje" class="col-sm-5 control-label custom-label"><?php echo $txt_iskustvo_5_god; ?>*</label>
								<div class="col-sm-7">
									<div class="custom-select">
										<select class="select-posao" id="iskustvo_u_struci_trajanje" name="iskustvo_u_struci_trajanje">
											<option value="" selected disabled><?php echo $txt_odaberi; ?>...</option>
											<option value="0"><?php echo $txt_nema_5_god?></option>
											<option value="1"><?php echo $txt_manje_1_god; ?></option>
											<option value="2">1 <?php echo $txt_godinu; ?></option>
											<option value="3">2 <?php echo $txt_godine; ?></option>
											<option value="4">3 <?php echo $txt_godine; ?></option>
											<option value="5">4 <?php echo $txt_godine; ?></option>
											<option value="6">5 <?php echo $txt_godina; ?></option>
										</select>
									</div>
								</div>
							</div>
							
							<script>
							$('#iskustvo_u_struci_trajanje').change(function() {
								$("#iskustvo_u_struci_trajanje").addClass('importantColorBlack');
							});
							</script>
							
							<script>
								$('#iskustvo_u_struci_da').click(function() {
									if($('#iskustvo_u_struci_da').is(':checked')) { 
										$('#iskustvo_u_struci_trajanje_group').show();
										$("#iskustvo_u_struci_trajanje").prop('required',true);
										$('#selectionError').hide();
									}
								});
								$('#iskustvo_u_struci_ne').click(function() {
									if($('#iskustvo_u_struci_ne').is(':checked')) { 
										$('#iskustvo_u_struci_trajanje_group').hide();
										$("#iskustvo_u_struci_trajanje").prop('required',false);
										$('#selectionError').hide();
									}
								});
							</script>
							
							<div class="form-group" id="kandidat_drzavljanstvo_vrsta" style="display: block">
									<label for="kandidat_drzavljanstvo_vrsta_eu" class="col-sm-5 control-label custom-label"><?php echo $txt_drzavljanstvo; ?>*</label>
									<div class="top-8">
									<div class="col-sm-3">
										<label class="main-container__column material-radio-group material-radio-group_success" for="kandidat_drzavljanstvo_vrsta_eu">
											<input type="radio" name="kandidat_drzavljanstvo_vrsta" id="kandidat_drzavljanstvo_vrsta_eu" class="material-radiobox" value="EU državljanin" />
											<span class="material-radio-group__element material-radio-group__check-radio"></span>
											<span class="material-radio-group__element material-radio-group__caption">EU <?php echo $txt_državljanin; ?></span>
										</label>
									</div>
									<div class="col-sm-4">
										<label class="main-container__column material-radio-group material-radio-group_success" for="kandidat_drzavljanstvo_vrsta_non">
											<input type="radio" name="kandidat_drzavljanstvo_vrsta" id="kandidat_drzavljanstvo_vrsta_non" class="material-radiobox" value="NON-EU državljanin" />
											<span class="material-radio-group__element material-radio-group__check-radio"></span>
											<span class="material-radio-group__element material-radio-group__caption">NON-EU <?php echo $txt_državljanin; ?></span>
										</label>
									</div>
									</div>
									<div class="col-sm-5">
									</div>
									<div class="col-sm-7">
										<p id="selectionErrorDrzavljanstvo" style="color: red; display: none;"><?php echo $txt_odaberi_opciju;?></p>
									</div>
							</div>
							
							<script>
							  document.getElementById('main_form').addEventListener('submit', function(event) {
								
								const kandidat_drzavljanstvo_vrsta_eu = document.getElementById('kandidat_drzavljanstvo_vrsta_eu');
								const kandidat_drzavljanstvo_vrsta_non = document.getElementById('kandidat_drzavljanstvo_vrsta_non');
								const selectionErrorDrzavljanstvo = document.getElementById('selectionErrorDrzavljanstvo');

								if (!kandidat_drzavljanstvo_vrsta_eu.checked && !kandidat_drzavljanstvo_vrsta_non.checked) {
								  selectionErrorDrzavljanstvo.style.display = 'flex';
								  event.preventDefault();
								} else {
								  selectionErrorDrzavljanstvo.style.display = 'none';
								}
							  });
							  
							  $('#kandidat_drzavljanstvo_vrsta_eu').click(function() {
								  	if($('#selectionErrorDrzavljanstvo').is(':checked')) { 
										$('#selectionErrorDrzavljanstvo').hide();
									}
								});
								$('#kandidat_drzavljanstvo_vrsta_non').click(function() {
									if($('#kandidat_drzavljanstvo_vrsta_non').is(':checked')) { 
										$('#selectionErrorDrzavljanstvo').hide();
									}
								});
							</script>


							<script>
								
								$('#skola_naziv').on('change', function() {
									$('.smjerovi_all').addClass('hidden');
									var skola_id = $(this).find(':selected').data('skola_id');
									$('.opt_'+skola_id+'').removeClass('hidden');
									optionSrednja =  $(this).val();
									if(optionSrednja == "ostalo"){
										$('#smjer_step1').hide();
										$('#smjer_select').hide();
										$('#unos_smjera').show();
										$("#smjer_naziv").prop('required',false);
									}else{
										$('#smjer_step1').show();
										$('#smjer_select').show();
										$('#unos_skole').hide();
										$('#unos_smjera').hide();
										$("#smjer_naziv").prop('required',true);
									}
									
								});

                                $('#nalog_smjer_naziv').on('change', function() {
                                    optionSmjer = $(this).val();
                                    if(optionSmjer == "ostalo"){
                                        $('#unos_smjera').show();
                                    }else{
                                        $('#unos_smjera').hide();
                                    }
                                });
								
							</script>
							<br />
							<div class="form-group">
								<div class="col-sm-offset-2 col-sm-10 submit-group">
									<button id="button_nastavi" type="submit" class="nastavi"><?php echo $txt_nastavi; ?></button>
									<br /><small class="txt-sva-polja"><?php echo $txt_sva_polja; ?></small>
								</div>
							</div>
							<div class="form-group">
								<label for="asd" class="col-sm-3 control-label"><span class="text-danger"></span></label>
								
								<div class="col-sm-6" style="padding-top: 10px;">
									
									<p class = "text-center"> <?php echo $txt_nastavkom; ?> 
									<br><a href="" type="button" data-toggle="modal" data-target="#privacyModal"><?php echo $txt_nastavkom_izjava; ?></a>
									<!--<br><a href="" type="button" data-toggle="modal" data-target="#privacyModal"><?php echo $txt_pogledaj_izjavu; ?></a>-->
									</p>
								</div>
							</div>
							<div class="form-group">
								<div class="col-sm-12">
									<p class = "text-center" id="text_na_dnu"><b><?php echo $txt_text_na_dnu; ?></b></p>
								</div>
							</div>
							<!-- Modal privacy -->
							<div class="modal fade modal-fullscreen" id="privacyModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
								<div class="modal-dialog" role="document">
								<div class="modal-content">
									<div class="modal-header">
									<h5 class="modal-title" id="exampleModalLabel"><?php echo $txt_izjava_privatnost; ?></h5>
									</div>
									<div class="modal-body">
									<p><?php echo $txt_full_izjava; ?></p>
									</div>
									<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $txt_zatvori; ?></button>
									</div>
								</div>
								</div>
							</div>
						</form>
					</div>
				</div>
				<footer>
					<?php
					echo "<p>©" . date('Y') . " " . $txt_prava ." - Jobstep IT Solutions</p>";
					?>
				</footer>
			</div>
		</div>
	</body>
</html>
