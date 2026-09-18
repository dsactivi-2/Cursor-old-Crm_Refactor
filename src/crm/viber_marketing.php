<?php
include("includes/functions.php");

/* SLANJE ZA MEDICINSKE TEHNICARE -  novi poslodavac u sarajevu */

// $mobitel = "38763453481"; //emir
// $mobitel = "38762473740"; //benjo
// $mobitel = "38762406765"; //adis
// $mobitel = "38761210765"; //deno
$text_sms = "SRETAN RODENDAN OD DEV TIMA";
$image_url_array = array("https://crm.job-step.com/deno/dev_team.jpg","https://crm.job-step.com/deno/dev_office2.jpg","https://crm.job-step.com/deno/dev_harun.jpg","https://crm.job-step.com/deno/dev_working_hard.jpg");
$text_viber_array = array('SRETAN', 'ROĐENDAN', 'OD', 'DEV', 'TIMA');
foreach($text_viber_array as $text_viber){

	$params = array(
		"scenarioKey" => "7A32331B2103607D2F890C04FEB34942",
		"destinations" => array(
			"to" => array(
				"phoneNumber" => $mobitel,
				)
		),
		"sms" => array(
			"text" =>$text_sms,
		),
		"viber" => array(
			"text" => $text_viber,
			"isPromotional" => "true"
		)
	);

	$data = json_encode($params);
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://ej8w3r.api.infobip.com/omni/1/advanced",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => $data,
	  CURLOPT_HTTPHEADER => array(
		"accept: application/json",
		"authorization: Basic ZWJlbmRlcjE6RUJlbmRlcjY0MzI=",
		"content-type: application/json"
	  ),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
	  echo "cURL Error #:" . $err;
	} else {
	  echo $response;
	}
	sleep(4);
}
foreach($image_url_array as $image_url){
	$params = array(
		"scenarioKey" => "7A32331B2103607D2F890C04FEB34942",
		"destinations" => array(
			"to" => array(
				"phoneNumber" => $mobitel,
				)
		),
		"sms" => array(
			"text" =>$text_sms,
		),
		"viber" => array(
		"imageURL" => $image_url,
		"isPromotional" => "true"
	)
	);

	$data = json_encode($params);
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://ej8w3r.api.infobip.com/omni/1/advanced",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => $data,
	  CURLOPT_HTTPHEADER => array(
		"accept: application/json",
		"authorization: Basic ZWJlbmRlcjE6RUJlbmRlcjY0MzI=",
		"content-type: application/json"
	  ),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
	  echo "cURL Error #:" . $err;
	} else {
	  echo $response;
	}
	sleep(2);
}
/*
$params = array(
	"scenarioKey" => "7A32331B2103607D2F890C04FEB34942",
	"destinations" => array(
		"to" => array(
			"phoneNumber" => $mobitel,
			)
	),
	"sms" => array(
		"text" =>$text_sms,
	),
	"viber" => array(
		"imageURL" => $image_url,
		"isPromotional" => "true"
	)
);*/
/*
// STARI CURL ZA SLANJE JEDNO PO JEDNO
	$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://ej8w3r.api.infobip.com/omni/1/advanced",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => "{ \"scenarioKey\":\"7A32331B2103607D2F890C04FEB34942\", \"destinations\":[ { \"to\":{ \"phoneNumber\":\"".$kandidat_mobitel."\" } } ], \"sms\":{ \"text\":\"".$text_sms_l."\" }, \"viber\":{ \"text\":\"".$text_viber."\", \"imageURL\":\"".$image_url."\", \"buttonText\":\"PRIJAVI SE\", \"buttonURL\":\"".$link."\", \"isPromotional\":\"true\" } }",
		  CURLOPT_HTTPHEADER => array(
			"accept: application/json",
			"authorization: Basic ZWJlbmRlcjE6RUJlbmRlcjY0MzI=",
			"content-type: application/json"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  echo "cURL Error #:" . $err;
		} else {
		  echo $response;
		}
/*
/*
 CURLOPT_POSTFIELDS => "{ \"scenarioKey\":\"7A32331B2103607D2F890C04FEB34942\", \"destinations\":[ { \"to\":{ \"phoneNumber\":\"".$kandidat_mobitel."\" } } ], \"imageURL\":\"".$image_url."\", \"isPromotional\":\"true\" } }",
*/



////// SLANJE ZA MEDICINSKE TEHNICARE -  novi poslodavac u sarajevu
/*$text_sms = 'Ovo je tvoj put iz snova u pokrajine Brandenburg i Bayern! Ceka te siguran smjestaj, kurs njemackog cak do B2 nivoa, a primanja sezu preko 2175 EUR i to je samo pocetna plata! Ali ovdje se ne zavrsava ponuda, poslodavac ti nudi pripreme za polaganje nostrifikacionog ispita te podrsku tokom same nostrifikacije diplome! Zvuci neodoljivo? Bas to i mi mislimo, cekamo te! Jobstep tim! Prijavi se: ' ;
$text_viber = 'Ovo je tvoj put iz snova u pokrajine Brandenburg i Bayern!\n\nČeka te siguran smještaj, kurs njemačkog čak do B2 nivoa, a primanja sežu preko 2175 EUR i to je samo početna plata!\n\nAli ovdje se ne završava ponuda, poslodavac ti nudi pripreme za polaganje nostrifikacionog ispita te podršku tokom same nostrifikacije diplome!\n\nZvuči neodoljivo? Baš to i mi mislimo, čekamo te!\n\n Jobstep tim!' ;
$q_get_all_cand = $db->prepare("
					SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_mobitel 
					FROM idk_kandidati 
					WHERE kandidat_group IN (2,48) 
					AND (kandidat_mobitel LIKE '+387%' OR kandidat_mobitel LIKE '+49%' )
					AND kandidat_zaposlen_kod IS NULL
					AND kandidat_datumrodjenja > '1975-12-31' 
					AND kandidat_id BETWEEN 3503 AND 7811
					"
);

// 	521,628
	$check_projekat = $db->prepare("
			SELECT pk_projectid
			FROM idk_project_kandidati
			WHERE pk_kandidatid = $kandidat_id AND pk_projectid IN (521, 522, 618)");

	$check_projekat->execute(); 
	$broj_projekata = $check_projekat->rowCount();
	if($broj_projekata == 0){*/

/* SLANJE ZA TEHNICKE STRUKE*/
/*
$text_sms = 'Vec ste pokrenuli nostrifikaciju za neku od tehnickih skola? Zanima Vas posao u Njemackoj? Vase znanje njemackog je na A2 nivou i jos ucite? U doba pandemije mozete obaviti razgovor sa njemackim poslodavcem i zapoceti novi zivot i karijeru u Njemackoj! Prijavi se: ' ;
$text_viber = 'Već ste pokrenuli nostrifikaciju za neku od tehničkih škola?\n\nZanima Vas posao u Njemačkoj?\n\nVaše znanje njemačkog je na A2 nivou i još učite?\n\nU doba pandemije možete obaviti razgovor sa njemačkim poslodavcem i započeti novi život i karijeru u Njemačkoj!' ;
$q_get_all_cand = $db->prepare("
					SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_mobitel, kandidat_status_prijave, kandidat_group
					FROM idk_kandidati 
					WHERE kandidat_group IN (46,39,25) 
					AND (kandidat_mobitel LIKE '+387%')
					AND kandidat_zaposlen_kod IS NULL
					AND kandidat_datumrodjenja > '1975-12-31'
					AND kandidat_status != 3					
					AND kandidat_id BETWEEN 10788 AND 11194
					"
);

$q_get_all_cand->execute();
$link = "https://jobstep-app.com/tehnicari_prijava/".$kandidat_id;
*/

/* DAN OTVORENIH VRATA */
/*
$text_sms = 'Imate nedoumice vezane za novi zakon i nostrifikaciju diplome? Prilika da ih otklonite i jos vise saznate: Sutra 05.02.2021. Dan otvorenih vrata na temu NOSTRIFIKACIJA DIPLOME U NJEMACKOJ. Vise informacija na linku: ' ;
$text_viber = 'Imate nedoumice vezane za novi zakon i nostrifikaciju diplome?\n\nPrilika da ih otklonite i još više saznate:\n\nSutra 05.02.2021. Dan otvorenih vrata na temu NOSTRIFIKACIJA DIPLOME U NJEMAČKOJ' ;
$q_get_all_cand = $db->prepare("
					SELECT id_broj_nd_kandidata, ime_nd_kandidata, prezime_nd_kandidata, grad_nd_kandidata, mobilni_nd_kandidata, status_nd_kandidata, pstatus_nd_kandidata
					FROM idk_nd_kandidata 
					WHERE grad_nd_kandidata = 'Sarajevo' AND status_nd_kandidata IN (1,7)
					AND (mobilni_nd_kandidata LIKE '+387%')
					AND id_broj_nd_kandidata BETWEEN 1600 AND 14161
					"
);

$q_get_all_cand->execute();
$ct = 1;
$image_url = "https://jobstep-app.com/images/dan_otv_vrata.png";
$link = "https://job-step.net/novost/dan-otvorenih-vrata/34";
*/


/* HAMMER CASTING - Servisni tehnicari i gradjevinski radnici */

// $text_sms = 'Zelis li raditi kao servisni tehnicar u Njemackoj? Imas iskustvo elektro ili neke druge tehnicke struke, tim bolje!  Ovo je prilika za tebe! Ako posjedujes poznavanje njemackog jezika na minimalno A2 nivou, prijavi se: ' ;
// $text_viber = 'Želiš li raditi kao servisni tehničar u Njemačkoj? Imaš iskustvo elektro ili neke druge tehničke struke, tim bolje!  Ovo je prilika za tebe!\n\nPOSLODAVAC NUDI:\nBrutto platu od min 2.200 €\n\nOsiguran smještaj\n\nMogućnost stručnog usavršavanja\n\nAko posjeduješ:\n - Poznavanje njemačkog jezika na minimalno A2 nivou\n - Vozačku dozvolu B kategorije\n\nPrijavi se i uprkos pandemiji započni karijeru u Njemačkoj!' ;
//-----------------------------------------------------------------------------------------------------------------------------
//HAMMER - drugi dio (svima)
/*
$text_sms = 'Zanima te posao u Njemackoj? Zelis raditi kao internet montazer ili mozda gradevinski radnik, ali po njemackom standardu. Prijavi se i pocetkom aprila prisustvuj velikom kastingu u Sarajevu! ' ;
$text_viber = 'Zanima te posao u Njemačkoj? Želiš raditi kao internet montažer ili možda građevinski radnik, ali po njemačkom standardu?\n\nPrijavi se i početkom aprila prisustvuj velikom kastingu u Sarajevu!' ;

$q_get_all_cand = $db->prepare("
					SELECT kandidat_id,kandidat_ime, kandidat_prezime, kandidat_mobitel, kandidat_status, kandidat_status_messenger, kandidat_group, kandidat_datumrodjenja
					FROM idk_kandidati 
					WHERE 
					kandidat_status != 3
					AND kandidat_zaposlen_kod IS NULL
					AND(kandidat_status_prijave != '4' OR kandidat_status_prijave is NULL)
					AND kandidat_mobitel LIKE '+387%' 
					AND TIMESTAMPDIFF(year,kandidat_datumrodjenja, now()) <= 45
					AND kandidat_id BETWEEN 24754 AND 30911
					GROUP BY kandidat_id
					"
);

$image_url = "https://jobstep-app.com/images/viber_hammer_oba.png";
/*
//-----------------------------------------------------------------------------------------------------------------------------
AND kandidat_datumrodjenja is null
AND TIMESTAMPDIFF(year,kandidat_datumrodjenja, now()) <= 45

AND edu.ke_naziv IN ('Mješovita srednja škola', 'Elektrotehnička škola', 'Mašinska škola', 'Mašinski fakultet', 'Tehnički fakultet', 'Tehnicka skola', 'Tehnološka škola', 'PTT skola', 'Fakultet informacionih tehnologija', 'Fakultet tehnickih nauka', 'Visoka tehnička škola', 'Masinsko saobracjna mjesovita skola', 'Elektroenergetska skola')
					
kandidat_mobitel LIKE '+387%' 
kandidat_group IN (2,48) 
AND(kandidat_status_prijave != '4' OR kandidat_status_prijave is NULL)
AND kandidat_zaposlen_kod IS NULL
AND kandidat_datumrodjenja > '1975-12-31' 
AND kandidat_id BETWEEN 3503 AND 7811
*/
/*

SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_mobitel, kandidat_status, kandidat_status_messenger, kandidat_group, kandidat_porijeklo, nd.datum_rodjenja, nd.skola_nd_kandidata
					FROM idk_kandidati 
					JOIN idk_nd_kandidata nd ON kandidat_mobitel = nd.mobilni_nd_kandidata
					WHERE 
					kandidat_status != 3
					AND kandidat_zaposlen_kod IS NULL
					AND(kandidat_status_prijave != '4' OR kandidat_status_prijave is NULL)
					AND kandidat_mobitel LIKE '+387%' 
					AND kandidat_group IN (7) 
					AND kandidat_datumrodjenja is null
					AND kandidat_porijeklo = 4
					GROUP BY kandidat_id
*/

/**** CITAV PRVI QUERY (samo serv tehnicari sa skolama)

SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_mobitel, kandidat_status, kandidat_status_messenger, kandidat_group, edu.ke_naziv, kandidat_datumrodjenja
					FROM idk_kandidati 
					LEFT JOIN idk_kandidat_edukacija edu ON kandidat_id = edu.ke_kandidat_id
					WHERE 
					kandidat_status != 3
					AND edu.ke_naziv IN ('Mješovita srednja škola', 'Elektrotehnička škola', 'Mašinska škola', 'Mašinski fakultet', 'Tehnički fakultet', 'Tehnicka skola', 'Tehnološka škola', 'PTT skola', 'Fakultet informacionih tehnologija', 'Fakultet tehnickih nauka', 'Visoka tehnička škola', 'Masinsko saobracjna mjesovita skola', 'Elektroenergetska skola')
					AND kandidat_zaposlen_kod IS NULL
					AND(kandidat_status_prijave != '4' OR kandidat_status_prijave is NULL)
					AND kandidat_mobitel LIKE '+387%' 
					AND TIMESTAMPDIFF(year,kandidat_datumrodjenja, now()) <= 45
					AND kandidat_group IN (7) 
					AND kandidat_id BETWEEN 25655 AND 29547
					GROUP BY kandidat_id
*/

//Slanje za HM PETROL  ----------------------------------------------------------------------------------------------- START
/*
$text_sms = 'Poduzece HM Petrol d.o.o. koje posluje pod okriljem Opal Grupe raspisuje oglas za popunjavanje radnih mjesta: Voditelj prodaje (m/z) – 1 izvrsioc PRIJAVI SE – link vodi na nasu stranicu na konkurs:' ;
$text_viber = 'Poduzeće HM Petrol d.o.o. koje posluje pod okriljem Opal Grupe raspisuje oglas za popunjavanje radnih mjesta:\nVoditelj prodaje (m/ž) – 1 izvršioc\nPRIJAVI SE – link vodi na nasu stranicu na konkurs:' ;

$q_get_all_cand = $db->prepare("
					SELECT DISTINCT kandidat_id,kandidat_ime, kandidat_prezime, kandidat_mobitel, kandidat_status, kandidat_status_messenger, kandidat_group, kandidat_datumrodjenja 
					FROM idk_kandidati 
					JOIN idk_project_kandidati on kandidat_id=pk_kandidatid 
					WHERE pk_projectid in (1392,1391,1390,1389,1388,1387,1386,1385)
					"
);

$image_url = "https://jobstep-app.com/images/viber_HM_petrol.png";

//Slanje za HN PETROL  ----------------------------------------------------------------------------------------------- END

*/

//HAMMER - treci dio (samo montazeri/servisni tehnicari koji nisu reagovali)
/*
$text_sms = 'Zelis li raditi kao internet montazer u Njemackoj? Imas iskustvo elektro ili neke druge tehnicke struke, tim bolje!  Ovo je prilika za tebe! Ako posjedujes poznavanje njemackog jezika na minimalno A2 nivou, prijavi se: ' ;
$text_viber = 'Želiš li raditi kao internet montažer u Njemačkoj? Imaš iskustvo elektro ili neke druge tehničke struke, tim bolje!  Ovo je prilika za tebe!\n\nPOSLODAVAC NUDI:\nBrutto platu od min 2.200 €\n\nOsiguran smještaj\n\nMogućnost stručnog usavršavanja\n\nAko posjeduješ:\n - Poznavanje njemačkog jezika na minimalno A2 nivou\n - Vozačku dozvolu B kategorije\n\nPrijavi se i uprkos pandemiji započni karijeru u Njemačkoj!' ;

$q_get_all_cand = $db->prepare("
					SELECT kandidat_id,kandidat_ime, kandidat_prezime, kandidat_mobitel, kandidat_status, kandidat_status_messenger, kandidat_group, kandidat_datumrodjenja
					FROM idk_kandidati 
					WHERE 
					kandidat_status != 3
					AND kandidat_zaposlen_kod IS NULL
					AND(kandidat_status_prijave != '4' OR kandidat_status_prijave is NULL)
					AND kandidat_mobitel LIKE '+387%' 
					AND TIMESTAMPDIFF(year,kandidat_datumrodjenja, now()) <= 45
					AND kandidat_id NOT IN (SELECT pk_kandidatid FROM idk_project_kandidati WHERE pk_projectid IN (1474,1475,1476,1477,1432,1489,1473,1430,1426,1424,1423,1422,1510,1511,1512))
					AND kandidat_id BETWEEN 25553 AND 32107
					GROUP BY kandidat_id
					"
);

// AND kandidat_datumrodjenja is null
// AND TIMESTAMPDIFF(year,kandidat_datumrodjenja, now()) <= 45
// AND kandidat_id NOT IN (SELECT pk_kandidatid FROM idk_project_kandidati WHERE pk_projectid IN (1474,1475,1476,1477,1432,1489,1473,1430,1426,1424,1423,1422))


$image_url = "https://jobstep-app.com/images/viber_montazer.png";
*/

//HAMMER - treci dio END

//Slanje za medicinskog tehničara za sve medicinare i one bez škola START

// $text_sms = 'Ukoliko imas iskustva na pedijatriji kao medicinski tehnicar, klinika u Kolnu (Njemacka) te ceka! Uz odlican iznos plate i mnoge pogodnosti, Jobstep te vodi do cilja! Prijavi se: ' ;
// $text_viber = 'Ukoliko imaš iskustva na pedijatriji kao medicinski tehničar, klinika u Kölnu (Njemačka) te čeka! Uz odličan iznos plate i mnoge pogodnosti, Jobstep te vodi do cilja! Prijavi se!' ;
// $image_url = "https://jobstep-app.com/images/medicinska_sestra_2.png";
// $link = "https://jobstep-app.com/registracija/506/korak1";
// $text_sms_link = $text_sms.$link;

// $q_get_all_cand = $db->prepare("
				// SELECT kandidat_id,kandidat_ime, kandidat_prezime, kandidat_mobitel, kandidat_email 
					// FROM idk_kandidati WHERE kandidat_status != 3 AND (kandidat_mobitel LIKE '+381%' OR kandidat_mobitel LIKE '+387%') 
					// AND kandidat_id between 11637 AND 13924
					

// ");

// AND kandidat_datumrodjenja is null

// AND kandidat_id NOT IN (SELECT pk_kandidatid FROM idk_project_kandidati WHERE pk_projectid IN (1474,1475,1476,1477,1432,1489,1473,1430,1426,1424,1423,1422))





//Slanje za medicinskog tehničara za sve medicinare i one bez škola END



// SLANJE ZA DAN OTVORENIH VRATA KANDIDATI IZ  MOSTARA I OKOLICE START

// $q_get_all_cand = $db->prepare("
// SELECT kan.id_broj_nd_kandidata, kan.mobilni_nd_kandidata FROM idk_nd_kandidata kan
		// WHERE (TIMESTAMPDIFF(year,kan.datum_rodjenja, now()) <= 45 OR kan.datum_rodjenja is NULL)
		// AND kan.mobilni_nd_kandidata LIKE '+387%'
		// AND ((kan.status_nd_kandidata = 1 AND kan.pstatus_nd_kandidata = 2)
			 // OR (kan.status_nd_kandidata = 1 AND kan.pstatus_nd_kandidata = 4)
			 // OR (kan.status_nd_kandidata = 7))
        // limit 1
        
        

		// UNION 

		// SELECT kan2.id_broj_nd_kandidata, kan2.mobilni_nd_kandidata FROM idk_nd_kandidata kan2

		// JOIN
			// (SELECT * FROM idk_nd_kandidata_status_log sublog
			 // WHERE sublog.broj_dana_statusa_nd_kandidata is not NULL
			 // AND sublog.broj_dana_statusa_nd_kandidata > 60
			 // ORDER BY sublog.vrijeme_promjene_statusa_nd_kandidata DESC) log
			 
		// ON log.idd_broj_nd_kandidata = kan2.id_broj_nd_kandidata

		// WHERE (TIMESTAMPDIFF(year,kan2.datum_rodjenja, now()) <= 45 OR kan2.datum_rodjenja is NULL)
		// AND kan2.mobilni_nd_kandidata = 1
		// AND kan2.pstatus_nd_kandidata = 3
		// GROUP BY (kan2.id_broj_nd_kandidata)
		// HAVING MIN(log.vrijeme_promjene_statusa_nd_kandidata)         
// ");

// $text_sms = "Postovani, ovim putem Vas obavijestevamo da, nazalost, niste usli u uzi krug kastinga.\n
// Vasu prijavu cemo zadrzati, te Vas kontaktirati za sve buduce poslovne prilike.\n
// Vas Jobstep Team";
// $text_viber = "Poštovani, ovim putem Vas obaviještevamo da, nažalost, niste ušli u uži krug kastinga.\n
// Vašu prijavu ćemo zadržati, te Vas kontaktirati za sve buduće poslovne prilike.\n
// Vaš Jobstep Team";
// SLANJE ZA DAN OTVORENIH VRATA KANDIDATI IZ  MOSTARA I OKOLICE END




// $link = "https://job-step.net/nostrifikacija_prijava/95/bs";
// $text_sms = 'Da biste dobili posao u Njemackoj, potrebna Vam je nostrificirana/priznata diploma. Jobstep International je pravo mjesto za vas, a samo u maju ove godine Jobstep uslugu nostrifikacije obavlja po 20% nizoj cijeni! Prilika za Njemacku koju ne zelis propustiti.'.$link;
// $text_viber = 'Da biste dobili posao u Njemačkoj, potrebna Vam je nostrificirana/priznata diploma. Jobstep International je pravo mjesto za vas, a samo u maju ove godine Jobstep uslugu nostrifikacije obavlja po 20% nižoj cijeni! Prilika za Njemačku koju ne želiš propustiti.';
// $image_url = "https://jobstep-app.com/images/nd_akcijaMaj_20_3.png";





// $link = "https://job-step.net/nostrifikacija_prijava/108/bs";
// $text_sms = 'Iskoristi priliku! Prijavi se do kraja maja i nostrificiraj svoju diplomu po 20% nizoj cijeni.'.$link;
// $text_viber = 'Iskoristi priliku! Prijavi se do kraja maja i nostrificiraj svoju diplomu po 20% nižoj cijeni.';
// $image_url = "https://jobstep-app.com/images/akcija_maj_5dana.png";


// $q_get_all_cand = $db->prepare("
// SELECT mobilni_nd_kandidata FROM idk_nd_kandidata WHERE skola_nd_kandidata IS NULL AND (mobilni_nd_kandidata LIKE '+387%')

// UNION

// SELECT mobilni_nd_kandidata FROM idk_nd_kandidata WHERE skola_nd_kandidata IN (11,19,23,83) AND mobilni_nd_kandidata LIKE '+387%'

// UNION

// SELECT kandidat_mobitel FROM idk_kandidati WHERE kandidat_group IN (2,3,6,23,48) AND kandidat_mobitel LIKE '+387%' AND kandidat_status != 3
      
// ");

// $q_get_all_cand->execute();
/*
$link = "https://jobstep-app.com/registracija/520/korak1";
$text_sms = 'Posao medicinske sestre/medicinskog tehničara u Berlinu – Njemacka, za 2300€ te ceka. Samo uz Jobstep, prilika kakvu zelis! Prijavi se odmah. '.$link;
$text_viber = 'Posao medicinske sestre/medicinskog tehničara u Berlinu – Njemačka, za 2300€ te čeka. Samo uz Jobstep, prilika kakvu želiš! Prijavi se odmah.';
$image_url = "https://jobstep-app.com/images/medicinska_sestra_tehnicar_berlin.jpg";
$ct = 1;

?> 
<table>
<?php
$brojevi=array();
while($row_cand = $q_get_all_cand->fetch()){

	
	// $kandidat_id = $row_cand['kandidat_id'];
	// $kandidat_ime = $row_cand['kandidat_ime'];
	// $kandidat_prezime = $row_cand['kandidat_prezime'];
	$kandidat_mobitel = $row_cand['mobilni_nd_kandidata'];
	// $kandidat_status = $row_cand['kandidat_status'];
	// $kandidat_podstatus = $row_cand['kandidat_status_messenger'];
	// $kandidat_group = $row_cand['kandidat_group'];
	// $kandidat_datumrodjenja = $row_cand['kandidat_datumrodjenja'];
	// $godine = $row_cand['godine'];
	array_push($brojevi, $kandidat_mobitel);
	//emir
	//$kandidat_mobitel = "+38761938892";
	//IRMA
	//$kandidat_mobitel = "38763030362";
	//FATIMA
	//$kandidat_mobitel = "38761038441";
	//FATIMA samo SMS
	//$kandidat_mobitel = "38761398468";
	//hari
	//$kandidat_mobitel = "38761395909";
	//amanda
	//$kandidat_mobitel = "38762926573";
	//benjo
	//$kandidat_mobitel = "38762473740";
	//zizu
	//$kandidat_mobitel = "38762149588";

	//$link = "https://jobstep-app.com/tehnicari_prijava/".$kandidat_id;
	//$link = "https://jobstep-app.com/casting_prijava/1473/".$kandidat_id;
	//$link = "https://job-step.net/konkurs/voditelj-prodaje/78";
	
	//$text_sms_l = $text_sms.$link;
	
		?>
		<tr>
		<td><?php echo  $ct++; ?> </td>
		<td><?php echo  $kandidat_id; ?> </td>
		<td><?php echo  $kandidat_ime; ?> </td>
		<td><?php echo  $kandidat_prezime; ?> </td>
		<td><?php echo  $kandidat_mobitel; ?> </td>
		<td><?php echo  "status--".$kandidat_status."-mess".$kandidat_podstatus; ?> </td>
		<td><?php echo  "grupa--".$kandidat_group; ?> </td>
		<td><?php echo  "skola--".$kandidat_datumrodjenja."datum"; ?> </td>
		<td>
		<?php
// SLANJE SA RAZLIČITIM LINKOVIMA BEZ BULKA ---------------------------------------------------------------- START
		// $params = array(
			
		// "scenarioKey" => "7A32331B2103607D2F890C04FEB34942",
		// "destinations" => array(
			// "to" => array(
				// "phoneNumber" => $kandidat_mobitel,
				// )
		// ),
		// "sms" => array(
			// "text" =>$text_sms,
			// ),
		// "viber" => array(
			// "text" => $text_viber,
			// // "imageURL" => $imageURL,
			// // "buttonText" => $buttonText,
			// // "buttonURL" => $buttonURL,
			// // "isPromotional" => "true"
		// )
		// );


		// $data = json_encode($params);
		// $curl = curl_init();

		// curl_setopt_array($curl, array(
		  // CURLOPT_URL => "https://ej8w3r.api.infobip.com/omni/1/advanced",
		  // CURLOPT_RETURNTRANSFER => true,
		  // CURLOPT_ENCODING => "",
		  // CURLOPT_MAXREDIRS => 10,
		  // CURLOPT_TIMEOUT => 30,
		  // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  // CURLOPT_CUSTOMREQUEST => "POST",
		  // CURLOPT_POSTFIELDS => $data,
		  // CURLOPT_HTTPHEADER => array(
			// "accept: application/json",
			// "authorization: Basic ZWJlbmRlcjE6RUJlbmRlcjY0MzI=",
			// "content-type: application/json"
		  // ),
		// ));

		// $response = curl_exec($curl);
		// $err = curl_error($curl);

		// curl_close($curl);

		// if ($err) {
		  // echo "cURL Error #:" . $err;
		// } else {
		  // echo $response;
		// }
		
// SLANJE SA RAZLIČITIM LINKOVIMA BEZ BULKA ---------------------------------------------------------------- END
	?>
	</td></tr>
	<?php
}
?>
</table>
<?php
		
		//array_push($brojevi,"38762473740");
		//array_push($brojevi,"38761938892");
		//array_push($brojevi,"38762731424");
		//array_push($brojevi,"38762406765");
		//array_push($brojevi,"38761395909");
		//array_push($brojevi,"38761210765");
		$i = 1;
		foreach ($brojevi as $broj) {
			echo $i.". ".$broj."<br>";
			$i++;
		}
		$data='{"to":{"phoneNumber":"'.implode('"}},{"to":{"phoneNumber":"',$brojevi).'"}}';
		*/
		// var_dump('{"scenarioKey":"7A32331B2103607D2F890C04FEB34942","bulkId":"1f13979b-9457-4cd2-8e42-bfaad8c0f07c","destinations":['.$data.'],"sms":{"text":"'.$text_sms.'"},"viber":{"text":"'.$text_viber.'","imageURL":"'.$image_url.'","buttonText":"PRIJAVI SE","buttonURL":"'.$link.'","isPromotional":"true"}}');
//SLANJE SA ISTIM LINKOM BULK BRATE --------------------------------------------------------------------------START
		// $curl = curl_init();

		
		// curl_setopt_array($curl, array(
		  // CURLOPT_URL => "https://ej8w3r.api.infobip.com/omni/1/advanced",
		  // CURLOPT_RETURNTRANSFER => true,
		  // CURLOPT_ENCODING => "",
		  // CURLOPT_MAXREDIRS => 10,
		  // CURLOPT_TIMEOUT => 30,
		  // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  // CURLOPT_CUSTOMREQUEST => "POST",
		  // CURLOPT_POSTFIELDS =>'{"scenarioKey":"7A32331B2103607D2F890C04FEB34942","bulkId":"1f13979b-9457-4cd2-8e42-bfaad8c0f07c","destinations":['.$data.'],"sms":{"text":"'.$text_sms.'"},"viber":{"text":"'.$text_viber.'","imageURL":"'.$image_url.'","buttonText":"PRIJAVI SE","buttonURL":"'.$link.'","isPromotional":"true"}}',
		     
		  // CURLOPT_HTTPHEADER => array(
			// "accept: application/json",
			// "authorization: Basic ZWJlbmRlcjE6RUJlbmRlcjY0MzI=",
			// "content-type: application/json"
		  // ),
		// ));

		// $response = curl_exec($curl);
		// $err = curl_error($curl);

		// curl_close($curl);

		// if ($err) {
		  // echo "cURL Error #:" . $err;
		// } else {
		  // echo $response;
		// }
//SLANJE SA ISTIM LINKOM BULK BRATE --------------------------------------------------------------------------END
?>