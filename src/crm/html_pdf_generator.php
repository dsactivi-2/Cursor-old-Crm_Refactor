<?php
//26 do 29 sa taksom
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
require_once('tcpdf-main/tcpdf.php');
// include('includes/functions.php');
include('includes/connect.php');
// include('pdf_generator.php');
// DEFINISANJE NOVE KLASE KOJA SE KORISTI U generisiUgovor();
class bgTCPDF extends TCPDF {
	
    public function Header() {
        $auto_page_break = $this->AutoPageBreak;
        $this->SetAutoPageBreak(false, 0);
		$img_file = 'tcpdf-main/images/memorandumCH051023.jpg';
		$this->Image($img_file, 0, 3, 210, 293, '', '', '', false, 300, '', false, false, 0);
        $this->SetAutoPageBreak($auto_page_break, 40);
        $this->setPageMark();
    }
}

class bgTCPDFnew extends TCPDF {
	
    public function Header() {
        $auto_page_break = $this->AutoPageBreak;
        $this->SetAutoPageBreak(false, 0);
		$img_file = 'tcpdf-main/images/memorandumSrbija.jpg';
		$this->Image($img_file, 0, 3, 210, 293, '', '', '', false, 300, '', false, false, 0);
        $this->SetAutoPageBreak($auto_page_break, 40);
        $this->setPageMark();
    }
}
//FUNKCIJA KOJA GENERIŠE UGOVORE ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~START
function generisiUgovor($kandidat_id, $ugovor_jezik){

	Global $db;
	//QUERY KOJI POVLAČI STATUS UGOVORA, TOKEN, FILE NA NJEMAČKOM, FILE NA KLIJENTOVOM JEZIKU, ///////////////////////////////////////////////////////
	// I IP ADRESU + TIMESTAMP UKOLIKO JE FUNKCIJA POZVANA RADI "PREVODA" NAPRAVLJENOG UGOVORA NA NJEMAČKI (VRŠI SE PROVJEROM is_null(ug_file) kasnije
	$query_get_status_ugovora = $db->prepare('
		SELECT ug_id, ug_status, ug_token, ug_file, ug_file_de, ug_ip_adresa, ug_timestamp, ug_datum_prihvatanja, ug_razlog_odbijanja, ug_jezik, ug_datum_otvaranja_linka 
		FROM idk_nd_ugovori																													
		WHERE ug_kandidat_id = :kandidat_id
		ORDER BY ug_id DESC
	');

	$query_get_status_ugovora -> execute(array(
		':kandidat_id' => $kandidat_id
	));

	$row_get_status_ugovora = $query_get_status_ugovora -> fetch();

	$ug_id = $row_get_status_ugovora['ug_id'];
	$ug_status = $row_get_status_ugovora['ug_status'];
	$ug_token = $row_get_status_ugovora['ug_token'];
	$ug_file = $row_get_status_ugovora['ug_file'];
	$ug_file_de = $row_get_status_ugovora['ug_file_de'];
	$ug_datum_prihvatanja = $row_get_status_ugovora['ug_datum_prihvatanja'];
	$ug_datum_otvaranja_linka = $row_get_status_ugovora['ug_datum_otvaranja_linka'];
	$ug_ip_adresa = $row_get_status_ugovora['ug_ip_adresa'];
	$ug_timestamp = $row_get_status_ugovora['ug_timestamp'];
	$ug_razlog_odbijanja = $row_get_status_ugovora['ug_razlog_odbijanja'];
	$ug_jezik_org = $row_get_status_ugovora['ug_jezik'];
	
	if(is_null($ug_datum_otvaranja_linka) && $ug_status == 1){
		$query_set_otvorio_link = $db -> prepare('
			UPDATE idk_nd_ugovori
			SET ug_status = 4, ug_datum_otvaranja_linka = now()
			WHERE ug_id = :ug_id
			');
			
		$query_set_otvorio_link -> execute(array(':ug_id' => $ug_id));
		$ug_status = 4;
	}

	include('lang/'.$ug_jezik_org.'.php');
	$main_text 	= "";
	$is_copy 	= false;
	if($ug_status == 2 && is_null($ug_file) == false && is_null($ug_file_de) == true){
		$is_copy 	= true;
	}
	else{
		$is_copy 	= false;
	}
	
	//AKO JE UGOVOR VEC ODBIJEN PREUSMJERI KORISNIKA NA PAGE SA MOGUCNOSCU FEEDBACKA ZASTO JE ODBIJEN, AKO JE VEC PRETHODNO NEKAD UNESEN RAZLOG ODBIJANJA,
	//PROSLIJEDI GA NA PAGE KOJI NE ZAHTJEVA UNOS RAZLOGA I KOJI VIŠE NEMA POZIVA OVE FUNKCIJE////////////////////////////////////////////////////////////
	if ($ug_status == 3){
		$url_odbijen = getSiteUrlr();
		if(is_null($ug_razlog_odbijanja) == false){
			$url_odbijen = $url_odbijen.'ugovor_handler.php?action=odbijen_razlogom&token='.$ug_token.'&jezik='.$ug_jezik_org;
		}
		else{
			$url_odbijen = $url_odbijen.'ugovor_handler.php?action=odbijen&token='.$ug_token.'&jezik='.$ug_jezik_org;
		}
		header("Location:".$url_odbijen);
	}
	else if($ug_status == 0){
		$url_arhiviran = getSiteUrlr().'ugovor_handler.php?action=arhiviran&jezik='.$ug_jezik_org;
		header("Location:".$url_arhiviran);
	}



	//QUERY KOJI POVLAČI POTREBNE PODATKE KANDIDATA KOJEM SE ŠALJE UGOVOR//////////////////////////////
	$query_get_kandidat = $db -> prepare('														
		SELECT ime_nd_kandidata, prezime_nd_kandidata, grad_nd_kandidata, ulica_nd_kandidata, postanski_broj_nd_kandidata,
		jmbg_nd_kandidata, broj_licne_karte_nd_kandidata, vrsta_ugovora_nd_kandidata
		FROM idk_nd_kandidata
		WHERE id_broj_nd_kandidata = :kandidat_id
	');

	$query_get_kandidat -> execute(array(
		':kandidat_id' => $kandidat_id
	));

	$row_get_kandidat 				= $query_get_kandidat -> fetch();
	$kandidat_ime 					= $row_get_kandidat['ime_nd_kandidata'];
	$kandidat_prezime 				= $row_get_kandidat['prezime_nd_kandidata'];
	$kandidat_fullname 				= $kandidat_ime.' '.$kandidat_prezime;
	$postanski_broj_nd_kandidata 	= $row_get_kandidat['postanski_broj_nd_kandidata'];
	$vrsta_ugovora_nd_kandidata 	= $row_get_kandidat['vrsta_ugovora_nd_kandidata'];
	$grad_nd_kandidata			 	= $row_get_kandidat['grad_nd_kandidata'];
	$ulica_nd_kandidata			 	= $row_get_kandidat['ulica_nd_kandidata'];
	$jmbg_nd_kandidata			 	= $row_get_kandidat['jmbg_nd_kandidata'];
	$broj_licne_karte_nd_kandidata 	= $row_get_kandidat['broj_licne_karte_nd_kandidata'];
	$bez_takse = true; // true - bez takse, false - sa taksom

	if(in_array($vrsta_ugovora_nd_kandidata, array(26,27,28,29))){
		//dio koda za kupljenje teksta za ugovore sa taksom
		//20.01.2023 rađene su sitne izmjene na ugovorima bez takse, ako i kada budu isli ugovori sa taksom svakako ce ih se trebati pregledati
		//ponovo, pa tada uzeti u obzir i izmjene koje su radjene 20.01.2023
		if($is_copy){
			$main_text = $text_clanovi_1_copy.$text_clanovi_sa_taksom_copy.$text_clanovi_2_copy;
		}
		else{
			$main_text = $text_clanovi_1.$text_clanovi_sa_taksom.$text_clanovi_2;
			// if($ug_jezik_org == 'bs' && $ug_status == 2){
				// $main_text .= '<div></div><div></div><div></div>';
			// }
		}
		$bez_takse = false;
	}
	else{
		if($is_copy){
		// var_dump($is_copy);
			$main_text =$text_clanovi_1_copy.$text_clanovi_bez_takse_copy.$text_clanovi_2_copy;				
		}		
		else{
			if($ug_jezik_org == 'bs' AND $ug_status == 2){				
				$main_text = $text_clanovi_1.'<div></div><div></div><div></div><div></div>'.$text_clanovi_bez_takse.$text_clanovi_2;
			}
			else if($ug_jezik_org == 'bs'){
				$main_text = $text_clanovi_1.'<div></div><div></div><div></div>'.$text_clanovi_bez_takse.$text_clanovi_2;
			}
			else{
				$main_text = $text_clanovi_1.$text_clanovi_bez_takse.$text_clanovi_2;
			}
		}
		$bez_takse = true;
	}
	//NA KOJEM JEZIKU SE PRAVI UGOVOR? -> INCLUDE TEXT KOJI ĆE SE KORISTITI KOD UGOVORA + PRIPREMI STRING KOJI ĆE POPUNJAVATI TEXT IZNAD NASLOVA//////////////////////////////////////////
	if($ug_jezik_org == "bs"){
		// include('lang/bs.php');
		if($is_copy){
			$podaci_kandidat = '
					Jobstep Int GmbH<br>
					Sumpfstrasse 26,<br>
					6312 Steinhausen<br>
					(Im Folgenden: Auftragnehmer)
					<br><br>Und<br><br>
					'.$kandidat_fullname.'<br>
					'.$grad_nd_kandidata.',<br>
					'.$ulica_nd_kandidata.',<br>
					ID-Nummer ('.$broj_licne_karte_nd_kandidata.')<br>
					(Im Folgenden: Auftraggeber)<br><br>
					Schließen diesen
					</p>
			';
		}else{
			$podaci_kandidat = '
					Jobstep Int GmbH, ul. Sumpfstrasse 26, 6312 Steinhausen, ID: CHE-406.071.325 zastupan po direktoru Selmanović Denisu
					(u daljem tekstu: nalogoprimac)
					<br><br> i <br><br>
					'.$kandidat_fullname.' iz '.$grad_nd_kandidata
					.' ul. '.$ulica_nd_kandidata.', JMBG: '.$jmbg_nd_kandidata.' broj lične karte: '
					.$broj_licne_karte_nd_kandidata.' (u daljnjem tekstu: nalogodavac),<br><br>zaključuju ovaj
			';
		}
	}
	else if($ug_jezik_org == "sr"){
		// include('lang/sr.php');
		if($is_copy){
			$podaci_kandidat = '
					Jobstep Int GmbH<br>
					Sumpfstrasse 26,<br>
					6312 Steinhausen<br>
					(Im Folgenden: Auftragnehmer)
					<br><br>Und<br><br>
					'.$kandidat_fullname.'<br>
					'.$grad_nd_kandidata.',<br>
					'.$ulica_nd_kandidata.',<br>
					ID-Nummer ('.$broj_licne_karte_nd_kandidata.')<br>
					(Im Folgenden: Auftraggeber)<br><br>
					Schließen diesen
					</p>
			';
		}else{
			$podaci_kandidat ='
					Jobstep Int GmbH, ul. Sumpfstrasse 26, 6312 Steinhausen, ID: CHE-406.071.325 zastupan po direktoru Selmanović Denisu (u nastavku teksta: pružalac usluge)
					<br><br>i<br><br>
					'.$kandidat_fullname.' iz '.$grad_nd_kandidata
					.' ul. '.$ulica_nd_kandidata.' '.$postanski_broj_nd_kandidata.', JMBG: '.$jmbg_nd_kandidata.', broj LK: '
					.$broj_licne_karte_nd_kandidata.' (u nastavku teksta: korisnik usluge)
					<br><br>sklapaju ovaj
			';
			$podaci_kandidat = '<div><h3 class="textC mb"><i>UGOVOR O OBRADI PODATAKA U VEZI SA<br>NOSTRIFIKACIJOM/EVALUACIJOM DIPLOMA</i></h3></div>'.$podaci_kandidat;
		}
	}
	else if($ug_jezik_org == "de"){
		// include('lang/de.php');

			$podaci_kandidat = '
					Jobstep Int GmbH<br>
					Sumpfstrasse 26,<br>
					6312 Steinhausen<br>
					(Im Folgenden: Auftragnehmer)
					<br><br>Und<br><br>
					'.$kandidat_fullname.'<br>
					'.$grad_nd_kandidata.',<br>
					'.$ulica_nd_kandidata.',<br>
					ID-Nummer ('.$broj_licne_karte_nd_kandidata.')<br>
					(Im Folgenden: Auftraggeber)<br><br>
					Schließen diesen
			';
			//  ID: CHE-406.071.325 vertreten durch Denis Selmanović (im Folgenden: Auftragnehmer) IZBAČENO IZ NJEMAČKOG UGOVORA?, JMBG NE IDE???
		//smth to worry about later
		// if($ug_jezik_org == "bs"){
			// $text_clanovi = $text_prvi_dio.$text_artikel3_bs.$text_drugi_dio.$text_artikel8_bs.$text_treci_dio;
		// }
		// else if($ug_jezik_org == "sr"){
			// $text_clanovi = $text_prvi_dio.$text_artikel3_sr.$text_drugi_dio.$text_artikel8_sr.$text_treci_dio;
		// }
		// else if($ug_jezik_org == "de"){
			// $text_clanovi = $text_prvi_dio.$text_artikel3_de.$text_drugi_dio.$text_artikel8_de.$text_treci_dio;
		// }
	}

	//KREIRANJE NOVOG OBJEKTA I DEFINISANJE ISTOG/////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$novi_ugovor = new bgTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); //konstruktor (portrait, mm, a4, unicode,)
	$novi_ugovor->SetTitle('Ugovor');
	$novi_ugovor->SetMargins(15, 45, 20, true);
	$novi_ugovor->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$novi_ugovor->setFontSubsetting(true);

	$img_file = 'tcpdf-main/images/memorandumCH051023.jpg';
	$novi_ugovor->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
	$novi_ugovor->SetFont('dejavuserif','',11);


	$novi_ugovor->AddPage();

	//POVLAČENJE IP ADRESE KLIJENTA KOJI PRIHVATA UGOVOR U SLUČAJU DA GA VEĆ NIJE POTPISAO////
	//AKO JE IPAK POTPISAO, KORISTI IP ADRESU I TIMESTAMP IZ BAZE							//
	if(is_null($ug_file) == true){
		if(is_null($ug_file) == true ){
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			}
			elseif (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
				$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
			}
			else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
		}
		$timestamp = date("F j, Y, G:i T");

	}
	else{
		$ip = $ug_ip_adresa;
		$timestamp = $ug_timestamp;
	}
	$datum_potpisa = date("d.m.Y.",time());


	//DIO KODA KOJI DEFINIŠE ZADNJI TEKST NA OSNOVU TRENUTNOG STATUSA UGOVORA 2-POSLAN->STAVI POTPIS, ELSE->STAVI BUTTON DA PRIHVATI/ODBIJE 
	//*ZA URADITI* DUGME ZA ODBIJANJE/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if($ug_status == 2){
		if($is_copy == false){
			if($ug_jezik_org == "bs"){
				$text_na_kraju = '<div></div><div></div><div></div><div></div><div></div><div></div>
					<p>
						U Steinhausenu, dana '.$datum_potpisa.' godine
					</p>
					<table width="100%" class="pt pb">
						<tr>
							<td width="40%">Nalogodavac (Denis Selmanović):</td>
							<td width="10%"></td>
							<td width="50%">Nalogoprimac ('.$kandidat_ime.' '.$kandidat_prezime.'):</td>
						</tr>
						<tr>
							
							<td width="50%"></td>
							<td width="50%" class="f9"><u>ip: '.$ip.', '.$timestamp.'</u></td>
						</tr>
						
					</table>
				';
				if(!$bez_takse){
					$text_na_kraju = '<div></div><div></div><div></div><div></div><div></div><div></div>'.$text_na_kraju;
				}
			}
			else if($ug_jezik_org == "sr"){
				$text_na_kraju = '
					<p>
						U Steinhausenu, dana '.$datum_potpisa.' godine
					</p>
					<table width="100%" class="pt pb">
						<tr>
							<td width="40%">Pružalac usluge (Denis Selmanović):</td>
							<td width="10%"></td>
							<td width="50%">Korisnik usluge ('.$kandidat_ime.' '.$kandidat_prezime.'):</td>
						</tr>
						<tr>
							
							<td width="50%"></td>
							<td width="50%" class="f9"><u>ip: '.$ip.', '.$timestamp.'</u></td>
						</tr>
						
					</table>
				';			
			}
			else if($ug_jezik_org == "de"){
				$text_na_kraju = '
					<p>
						In Steinhausen, den '.$datum_potpisa.'
					</p>
					<table width="100%" class="pt pb">
						<tr>
							<td width="40%">Auftragnehmer (Denis Selmanović):</td>
							<td width="10%"></td>
							<td width="50%">Auftraggeber ('.$kandidat_ime.' '.$kandidat_prezime.'):</td>
						</tr>
						<tr>
							
							<td width="50%"></td>
							<td width="50%" class="f9"><u>ip: '.$ip.', '.$timestamp.'</u></td>
						</tr>
						
					</table>
				';
				// if($bez_takse){
					$text_na_kraju = '<div></div><div></div>'.$text_na_kraju;
				// }
			}			
		}
		else{
			$text_na_kraju = '
				<div>
					<p>
						In Steinhausen, den '.$datum_potpisa.'
					</p>
					<table width="100%" class="pt pb">
						<tr>
							<td width="40%">Auftragnehmer (Denis Selmanović):</td>
							<td width="10%"></td>
							<td width="50%">Auftraggeber ('.$kandidat_ime.' '.$kandidat_prezime.'):</td>
						</tr>
						<tr>
							
							<td width="50%"></td>
							<td width="50%" class="f9"><u>ip: '.$ip.', '.$timestamp.'</u></td>
						</tr>
						
					</table>
				</div>
			';
			if($ug_jezik_org == 'bs'){
				$text_na_kraju = "<div></div><div></div><div></div><div></div><div></div>".$text_na_kraju;
			}
			// else if($ug_jezik_org == 'de'){
				// $text_na_kraju = "<div></div><div></div><div></div><div></div><div></div><div></div>".$text_na_kraju;
			// }
		}
	}
	
	else{
		
		$text_prihvati_ugovor = "";
		$text_odbij_ugovor = "";

		if($ug_jezik_org == 'de'){
			$text_prihvati_ugovor 	= "VERTRAG ANNEHMEN";
			$text_odbij_ugovor 		= "VERTRAG ABLEHNEN";
		}
		else{
			$text_prihvati_ugovor 	= "PRIHVATI UGOVOR";
			$text_odbij_ugovor 		= "ODBIJ UGOVOR";			
		}
		
		$url_prihvatam = getSiteUrlr();//kreiranje url-a koji vodi na ugovor_handler radi unosa svih podataka vezanih za file 																	//
		$url_prihvatam = $url_prihvatam.'ugovor_handler.php?action=prihvacen&token='.$ug_token.'&jezik='.$ug_jezik_org;
		$url_odbijam = getSiteUrlr();
		$url_odbijam = $url_odbijam.'ugovor_handler.php?action=odbijen&token='.$ug_token.'&jezik='.$ug_jezik_org;
		$text_na_kraju = '																																										
			<table style="margin-top: 40px;" width="100%">
				<tr>
					<td width="10%" height="15px"></td>
					<td width="30%" height="15px" class="button-iza textC"><a class="button-text" style="padding-right: 20px;"  href="'.$url_prihvatam.'">'.$text_prihvati_ugovor.'</a></td>
					<td width="20%" height="15px"></td>
					<td width="30%" height="15px" class="button-iza textC"><a class="button-text" style="padding-right: 20px;"  href="'.$url_odbijam.'">'.$text_odbij_ugovor.'</a></td>
					<td width="10%" height="15px"></td>
				</tr>
			</table>';
		$podaci_kandidat = $text_na_kraju.'<br><br>'.$podaci_kandidat;
		
	}
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//STYLOVI KOJI SE KORISTE PRI HTML KODU KOJIM SE GENERIŠE PDF FILE
		$style = '
		<style>
		.justify{
			text-align: justify;
			text-justify: inter-word;
		}
		.bt{
			border-top: 1px solid black;
		}
		.bb{
			border-bottom: 1px solid black;
		}
		.bl{
			border-left: 1px solid black;
		}
		.br{
			border-right: 1px solid black;
		}
		.pt{
			padding-top: 6px;
		}
		.pb{
			padding-bottom: 6px;
		}
		.pr{
			padding-right: 6px;
		}
		.pl{
			padding-left: 6px;
		} 
		.mt{
			margin-top: 6px;
		}
		.mb{
			margin-bottom: 6px;
		}
		.mr{
			margin-right: 6px;
		}
		.ml{
			margin-left: 6px;
		} 
		
		.f11{
			font-size: 12px;
		}
		
		.f9{
			font-size: 9px;
		}
		
		.textC{
			text-align: center;
		}
		.textR{
			text-align: right;
		}
		.footer {
		width: 100%;
		text-align: center;
		}

		.button-text {
			text-decoration: none;
			color: #FFFFFF;
			font-size: 11 px;
		}
		
		.button-iza{

			background-color: #383d51; 
			border-top: 1px solid #383d51;
			border-right: 2px solid #5d959e;
			border-bottom: 2px solid #5d959e;
		}
		</style>';
	//*ZA URADITI* STANJITI OVAJ STYLE STRING, IZBACITI KLASE KOJE NE KORISTIM

//SPAJANJE SVIH STRINGOVA KOJI ČINE FILE
	$html = <<<EOD
	$podaci_kandidat
	$main_text
	$style
	$text_na_kraju
EOD;
///////////////////////////////////////


	$text_downloaded 		= "";
	$ug_name 				= "";
	$drzava_predracun 		= "";
	
	if($ug_jezik_org == "bs"){
		$ug_name			= "UGCHB-";
		$drzava_predracun 	= "BiH";
		$text_downloaded 	= "Ugovor";
	} 
		
	else if($ug_jezik_org == "de"){
		$ug_name			= "UGCHD-";
		$drzava_predracun 	= "Njemacka";
		$text_downloaded 	= "Vertrag";
	}
	
	else if($ug_jezik_org == "sr"){
		$ug_name			= "UGCHS-";
		$drzava_predracun 	= "Srbija";
		$text_downloaded 	= "Ugovor";
	}
	
	$query_ug_redni_broj = $db ->prepare('
		SELECT COUNT(ug_id) as cnt FROM idk_nd_ugovori 
		WHERE ug_file IS NOT NULL 
		AND year(CURRENT_DATE()) = year(ug_datum_prihvatanja)
	');
	
	$query_ug_redni_broj -> execute();
	$row_ug_redni_broj = $query_ug_redni_broj -> fetch();
	$cnt = $row_ug_redni_broj['cnt'];
	if(is_null($ug_file)==true){
		$cnt=$cnt+1;	
	}

	$ug_name = $ug_name.$cnt."-".date ("m-y", time());
	$novi_ugovor->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true); // GENERIŠE FILE
	$url_header = "";
	//UKOLIKO JE VEĆ PRIHVAĆEN UGOVOR, I UKOLIKO POSTOJI FILE NA KLIJENTOVOM JEZIKU, A NE POSTOJI KOPIJA NA NJEMAČKOM, DODAJ C NA IME FAJLA,//////////////////////////
	//UKOLIKO JE VEĆ PRIHVAĆEN UGOVOR, I UKOLIKO NE POSTOJI FILE NA KLIJENTOVOM JEZIKU, NAPRAVI GA U SVAKOM DRUGOM SLUCAJU GA SAMO ISPISI/////////////////////////////
	if($ug_status == 2){

		if(is_null($ug_file) == false && is_null($ug_file_de) == true){	//pravi kopiju
			$novi_ugovor->Output(__DIR__ . '/tcpdf-main/ugovori/'.$ug_name.'C.pdf', 'F');
			$url_header = getSiteUrlr();
			$url_header = $url_header.'ugovor_handler.php?action=kopija_de&token='.$ug_token.'&jezik='.$ug_jezik_org.'&fileName='.$ug_name.'C.pdf';			
		}
		else if(is_null($ug_file) == true){//pravi org
		
			$novi_ugovor->Output(__DIR__ . '/tcpdf-main/ugovori/'.$ug_name.'.pdf', 'F');
			$url_header = getSiteUrlr();
			$url_header = $url_header.'ugovor_handler.php?action=generisi_ugovor&token='.$ug_token.'&jezik='.$ug_jezik_org.'&fileName='.$ug_name.'.pdf&&ug_ip_adresa='.$ip.'&ug_timestamp='.$timestamp;							
		}			
		else if(is_null($ug_file) == false && is_null($ug_file_de) == false){
			//DODANO POTREBNO TESTIRATI START
			$query_get_predracun = $db->prepare('
				SELECT pr_id, pr_file, pr_file_de
				FROM idk_predracuni 
				WHERE pr_rata = 1
				AND pr_status = 1
				AND pr_uplaceno = 0
				AND pr_naplata_preko = 1
				AND pr_kandidat_id = :kandidat_id
			');
			$query_get_predracun -> execute(array(':kandidat_id' => $kandidat_id));
			$row_cnt 			= $query_get_predracun -> rowCount();
			if($row_cnt != 0){

				$row_get_predracun 	= $query_get_predracun -> fetch();
				$predracun_id 		= $row_get_predracun['pr_id'];
				$pr_file 			= $row_get_predracun['pr_file'];
				$pr_file_de 		= $row_get_predracun['pr_file_de'];
				
				if(is_null($pr_file)){
					if($drzava_predracun == "Njemacka"){
						generisiPredracun($predracun_id, $drzava_predracun);
						$url_header = getSiteUrlr();																														
						$url_header = $url_header.'ugovor_handler.php?action=typage&token='.$ug_token.'&jezik='.$ug_jezik_org.'&fileName='.$ug_file;	
					}
					else{
						generisiPredracun($predracun_id, $drzava_predracun);
						$url_header = getSiteUrlr();
						$url_header = $url_header.'ugovor_handler.php?action=generisi_predracun&token='.$ug_token.'&jezik='.$ug_jezik_org;			
					}
				}
				else if(is_null($pr_file_de) && $drzava_predracun != 'Njemacka'){
					generisiPredracun($predracun_id, "Njemacka");
					$url_header = getSiteUrlr();																														
					$url_header = $url_header.'ugovor_handler.php?action=typage&token='.$ug_token.'&jezik='.$ug_jezik_org.'&fileName='.$ug_file;	
				}
				else{
					$url_header = getSiteUrlr();																														
					$url_header = $url_header.'ugovor_handler.php?action=typage&token='.$ug_token.'&jezik='.$ug_jezik_org.'&fileName='.$ug_file;
				}
				
			}
			else{
				$url_header = getSiteUrlr();																														
				$url_header = $url_header.'ugovor_handler.php?action=typage&token='.$ug_token.'&jezik='.$ug_jezik_org.'&fileName='.$ug_file;
			}
			//DODANO POTREBNO TESTIRATI END
			
		}
		else{
			$url_header = getSiteUrlr();																														
			$url_header = $url_header.'ugovor_handler.php?action=typage&token='.$ug_token.'&jezik='.$ug_jezik_org.'&fileName='.$ug_file;	
		}
		// var_dump($url_header);
		// exit();
		header("Location:".$url_header);

	}
	else{		
		$novi_ugovor->Output($text_downloaded.".pdf", 'I');
	}

		// $novi_ugovor->Output('ugovor_tajitaj.pdf', 'I');		
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

}

//FUNKCIJA KOJA GENERIŠE UGOVORE ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~KRAJ


function generisiPredracun($predracun_id, $drzava){
	Global $db; 
	$datum_kreiranja = date('d.m.Y');
	$platiti_do = date('d.m.Y', strtotime($datum_kreiranja. ' + 7 days'));
	$get_predracun = $db->prepare("
					SELECT pr_broj_predracuna, nk.ime_nd_kandidata, nk.prezime_nd_kandidata, nk.ulica_nd_kandidata, nk.postanski_broj_nd_kandidata, nk.grad_nd_kandidata, pr_rata, pr_vrijednost_BAM, pr_vrijednost_EUR, pr_vrijednost_RSD, pr_file, nk.vrsta_ugovora_nd_kandidata, pr_domaca_valuta, jmbg_nd_kandidata, pr_stornirano, pr_kandidat_id, pr_zaposlenik
					FROM idk_predracuni
					JOIN idk_nd_kandidata nk
					ON nk.id_broj_nd_kandidata = pr_kandidat_id
					WHERE pr_id = :pr_id
					");

	$get_predracun->execute(array(':pr_id' => $predracun_id));
	$predracun_row = $get_predracun->fetch();
	
	$naziv_predracuna = $predracun_row["pr_broj_predracuna"];
	$kandidat_ime = $predracun_row["ime_nd_kandidata"];
	$kandidat_prezime = $predracun_row["prezime_nd_kandidata"];
	$ulica = $predracun_row["ulica_nd_kandidata"];
	$pbroj = $predracun_row["postanski_broj_nd_kandidata"];
	$grad = $predracun_row["grad_nd_kandidata"];
	$kandidat_jmbg = $predracun_row["jmbg_nd_kandidata"];
	$pr_stornirano = $predracun_row["pr_stornirano"];
	$pr_kandidat_id = $predracun_row["pr_kandidat_id"];
	$predracunZaposlenik = $predracun_row["pr_zaposlenik"];
	
	$pr_rata = $predracun_row["pr_rata"];
	$pr_vrijednost_BAM = $predracun_row["pr_vrijednost_BAM"];
	$pr_vrijednost_RSD = $predracun_row["pr_vrijednost_RSD"];
	$pr_vrijednost_EUR = $predracun_row["pr_vrijednost_EUR"];
	$pr_file = $predracun_row["pr_file"];
	$vrsta_ugovora = $predracun_row["vrsta_ugovora_nd_kandidata"];
	$pr_domaca_valuta = $predracun_row["pr_domaca_valuta"];
	
	$kandidat_adresa = $ulica.", ".$pbroj." ".$grad;
	
	if($pr_stornirano == 1){
		//Treba obavijestiti administraciju da je kandidat kome se stornirao racun, prihvatio ugovor, kako bi oni mogli da oznace uplatu
		$zaposleniciObavijestNizExp = array(67,63,208,50); //U slucaju da treba jos nekom poslati mail - ovdje samo dodati id-eve - NPR: array(67,75)
		//array_push($predracunZaposlenik, $zaposleniciObavijestNizExp);
		$zaposleniciObavijestNizImp = implode(",", $zaposleniciObavijestNizExp);
		if(count($zaposleniciObavijestNizExp) != 0){
			foreach($zaposleniciObavijestNizExp AS $valueZaposlenici){
				$user_query = $db->prepare("
					SELECT 
						employee_firstname, employee_lastname, employee_email
					FROM 
						idk_employees
					WHERE 
						employee_id = :employee_id
						AND 
						employee_status NOT LIKE '0'
				");

				$user_query->execute(array(
					':employee_id' => $valueZaposlenici
				));
				if ($user_query->rowCount() > 0) {

					$user = $user_query->fetch();

					$employee_firstname = $user['employee_firstname'];
					$employee_lastname = $user['employee_lastname'];
					$employee_email = $user['employee_email'];

					//Send email to user
					$mail_email = $employee_email;
					$mail_name = $employee_firstname . ' ' . $employee_lastname;
					$mail_subject = "Označavanje uplate za kandidata #".$pr_kandidat_id."";
					$mail_url = "" . getSiteUrlr() . "/nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$pr_kandidat_id."";
					$mail_url = '<a href = "'.$mail_url.'" >LINK</a>';
					$mail_body = "
						<p>
						Poštovani, <br><br>
						Za kandidata čiji profil možete pogledati na ".$mail_url."-u, izvršena je stornacija računa zbog pogrešno unešenih podataka.
						Kandidat je prihvatio novi ugovor koji mu se poslao tako da sad možete označiti uplatu po predračunu broj: ".$naziv_predracuna.".
						</p>
					";
					$mail_altbody = "
						<p>
						Poštovani, <br><br>
						Za kandidata čiji profil možete pogledati na ".$mail_url."-u, izvršena je stornacija računa zbog pogrešno unešenih podataka.
						Kandidat je prihvatio novi ugovor koji mu se poslao tako da sad možete označiti uplatu po predračunu broj: ".$naziv_predracuna.".
						</p>
					";
					
					sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);
				}
			}
		}
	}
	
	switch($pr_rata){
		case 1:
			$rate_text1 = " - Prvi ";
			$rate_text1_de = " - erster ";
		break;
		case 2:
			$rate_text1 = " - Drugi ";
			$rate_text1_de = " - zweiter ";
		break;
		case 3:
			$rate_text1 = " - Treći ";
			$rate_text1_de = " - dritter ";
		break;
		case 4:
			$rate_text1 = " - Četvrti ";
			$rate_text1_de = " - vierter ";
		break;
		case 5:
			$rate_text1 = " - Peti ";
			$rate_text1_de = " - fünfter ";
		break;
		default:
			$rate_text1 = "";
	}
	
	if($drzava == "BiH"){
		$slovo_drz = "B";
		$domaca_valuta = "BAM";
		$ukupno_za_pl = number_format($pr_vrijednost_BAM, 2, '.', '');
		$ukupno_za_pl_txt1 = "Ukupno za plaćanje BAM";
		$ukupno_za_pl_txt2 = $ukupno_za_pl." KM";
		
		$text_kupac = "Kupac";
		$text_ime_prezime = "Ime i prezime";
		$text_plativo_do = "Plativo do";
		$text_avansni_racun = "Avansni Račun";
		$text_obrada_podataka_ND = "Obrada podataka za nostrifikaciju diplome ";
		$text_iznos = "Iznos";
		$text_sifra = "Šifra";
		$text_naziv = "Naziv";
		$text_kolicina = "Količina";
		$text_cijena = "Cijena";
		$text_ukupno = "Ukupno";
		$text_pdv = "PDV";
		$text_popust = "Popust";
		$text_adresa = "Adresa";
		$text_ukupno_placanje = "Ukupno za plaćanje";
		$text_uslovi_placanja = "Uslovi plaćanja: Račun plativ u roku od 7 dana.";
		
	}elseif($drzava == "Srbija"){
		$slovo_drz = "S";
		$domaca_valuta = "RSD";
		$ukupno_za_pl = number_format($pr_vrijednost_RSD, 2, '.', '');
		$ukupno_za_pl_txt1 = "Ukupno za plaćanje RSD";
		$ukupno_za_pl_txt2 = $ukupno_za_pl." RSD";
		
		$text_kupac = "Kupac";
		$text_ime_prezime = "Ime i prezime";
		$text_plativo_do = "Plativo do";
		$text_avansni_racun = "Avansni Račun";
		$text_obrada_podataka_ND = "Obrada podataka za nostrifikaciju diplome ";
		$text_iznos = "Iznos";
		$text_sifra = "Šifra";
		$text_naziv = "Naziv";
		$text_kolicina = "Količina";
		$text_cijena = "Cena";
		$text_ukupno = "Ukupno";
		$text_pdv = "PDV";
		$text_popust = "Popust";
		$text_adresa = "Adresa";
		$text_ukupno_placanje = "Ukupno za plaćanje";
		$text_uslovi_placanja = "Uslovi plaćanja: Račun plativ u roku od 7 dana.";
		
	}elseif($drzava == "Njemacka"){
		//nista ne ispisuje u zadnjem redu kad je Njemacka u pitanju
		$slovo_drz = "D";
		$domaca_valuta = "EUR";
		$ukupno_za_pl = number_format($pr_vrijednost_EUR, 2, '.', '');
		$ukupno_za_pl_txt1 = "";
		$ukupno_za_pl_txt2 = "";
		
		$text_kupac = "Kunde";
		$text_ime_prezime = "Name und Vorname";
		$text_plativo_do = "Zahlbar bis";
		$text_avansni_racun = "Vorschussrechnung";
		$text_obrada_podataka_ND = "Dienstleistung Datenverarbeitung ";
		$text_iznos = "Betrag";
		$text_sifra = "Pos";
		$text_naziv = "Bezeichnung";
		$text_kolicina = "Menge";
		$text_ukupno = "Total";
		$text_cijena = "Preis";
		$text_pdv = "MwSt";
		$text_popust = "Rabat";
		$text_adresa = "Adresse";
		$text_ukupno_placanje = "Zahlungsbetrag";
		$text_uslovi_placanja = "Zahlungsbedigungen: Zahlung innerhalb von 7 Tagen ab Rechnungseingang ohne Abzüge.";
		
	}
	$pdv_stopa = 0;
	$ukupno_EUR = $pr_vrijednost_EUR/(1+$pdv_stopa);
	$ukupno_EUR_f = number_format($ukupno_EUR, 2, '.', '');
	$ukupno_za_pl_EUR_f = number_format($pr_vrijednost_EUR, 2, '.', '');
	
	$pdv_txt = $pdv_stopa * 100;
	$pdv_txt_f = number_format($pdv_txt, 2, '.', '');
	
	$pdv_iznos = $ukupno_za_pl_EUR_f - $ukupno_EUR_f;
	$pdv_iznos_f = number_format($pdv_iznos, 2, '.', '');
	
	if(in_array(($vrsta_ugovora), array(21,22,23,24,25,61,62,63,64,65))){
		$popust_part = true;
		$popust_procent = 20;
	}elseif(in_array(($vrsta_ugovora), array(2,4,6,8,10,12,71,72,73,74,75))){
		$popust_part = true;
		$popust_procent = 30;
	}elseif(in_array(($vrsta_ugovora), array(9,82,83,84,85))){ //sve dok je 10 popust na placanje u cijelosti(1 rata-> 9)
		$popust_part = true;
		$popust_procent = 10;
	}elseif(in_array(($vrsta_ugovora), array(51,52,53,54,55))){
		$popust_part = true;
		$popust_procent = 50;
	}elseif(in_array(($vrsta_ugovora), array(41,42,43,44,45))){
		$popust_part = true;
		$popust_procent = 70;
	}elseif($vrsta_ugovora == 99){ 
		$popust_part = true;
		$popust_procent = 100;
	}else{
		$popust_part = false;
		$popust_procent = 0;
	}
	
	if($popust_procent != 100){
		$iznos = $ukupno_EUR * (100 / (100 - $popust_procent));
	}else{
		$iznos = 485.73;
	}
	
	$iznos_f = number_format($iznos, 2, '.', '');
	$popust_procent_txt = ''.$popust_procent.'';
	$popust = $iznos_f - $ukupno_EUR_f;
	$popust_f = number_format($popust, 2, '.', '');
	
	if($popust_part){
		
		$vodoravne_cijene = '
		<tr>
			<td class = "bt bb bl" width="8%"><b>'.$text_sifra.'</b></td>
			<td class = "bt bb" width="42%"><b>'.$text_naziv.'</b></td>
			<td class = "bt bb textC" width="10%"><b>'.$text_kolicina.'</b></td>
			<td class = "bt bb textR" width="12%"><b>'.$text_cijena.'</b></td>
			<td class = "bt bb textR" width="14%"><b>'.$text_popust.'</b></td>
			<td class = "bt bb br textR" width="14%"><b>'.$text_ukupno.'</b></td>
		</tr>
		<tr>
			<td width="8%">1</td>
			<td width="42%">'.$text_obrada_podataka_ND.'</td>
			<td class = "textC" width="10%">1</td>
			<td class = "textR" width="12%">
				&euro;&nbsp;&nbsp; '.$iznos_f.'				
			</td>
			<td class = "textR" width="14%">
				&nbsp;&nbsp;- '.$popust_procent_txt.' %
			</td>
			<td class = "textR" width="14%">
				&euro;&nbsp;&nbsp;'.$ukupno_EUR_f.'
			</td>
		</tr>
		';
		
		$popust_txt = '
		<tr>
			<td width="33%" class="bt"></td>
			<td width="51%" class="bt">'.$text_iznos.'</td>
			<td width="16%" class="textR bt">&euro;&nbsp;&nbsp;'.$iznos_f.'</td>
		</tr>
		<tr>
			<td width="33%"></td>
			<td width="51%">'.$text_popust.' '.$popust_procent_txt.'%</td>
			<td width="16%" class="textR">&euro;&nbsp;&nbsp;-'.$popust_f.'</td>
		</tr>
		<tr>
			<td width="33%"></td>
			<td width="51%">'.$text_ukupno.'</td>
			<td width="16%" class="textR">&euro;&nbsp;&nbsp;'.$ukupno_EUR_f.'</td>
		</tr>
		';
	}else{
		$popust_txt = '
		<tr>
			<td width="33%" class="bt"></td>
			<td width="51%" class="bt">'.$text_ukupno.'</td>
			<td width="16%" class="textR bt">&euro;&nbsp;&nbsp;'.$ukupno_EUR_f.'</td>
		</tr>
		';
		$vodoravne_cijene = '
		<tr>
			<td class = "bt bb bl" width="8%"><b>'.$text_sifra.'</b></td>
			<td class = "bt bb" width="50%"><b>'.$text_naziv.'</b></td>
			<td class = "bt bb textC" width="10%"><b>'.$text_kolicina.'</b></td>
			<td class = "bt bb textC" width="16%"><b>'.$text_cijena.'</b></td>
			<td class = "bt bb br textC" width="16%"><b>'.$text_ukupno.'</b></td>
		</tr>
		<tr>
			<td width="8%">1</td>
			<td width="50%">'.$text_obrada_podataka_ND.'</td>
			<td class = "textC" width="10%">1</td>
			<td class = "textC" width="16%">
				&euro;&nbsp;&nbsp; '.$iznos_f.'				
			</td>
			<td class = "textR" width="16%">
				&euro;&nbsp;&nbsp;'.$ukupno_EUR_f.'
			</td>
		</tr>
		';
	}
	if($vrsta_ugovora == 26 OR $vrsta_ugovora == 27 /*OR $vrsta_ugovora == 28 OR $vrsta_ugovora == 29*/){
		$cijena_takse_f = number_format($cijena_takse, 2, '.', '');
		$taksa_txt = '
			<tr>
				<td width="8%">2</td>
				<td width="50%">Taksaaaa ceka se tekst</td>
				<td class = "textC" width="10%">1</td>
				<td class = "textC" width="16%">
					&euro;&nbsp;&nbsp; '.$cijena_takse_f.'				
				</td>
				<td class = "textR" width="16%">
					&euro;&nbsp;&nbsp;'.$cijena_takse_f.'
				</td>
			</tr>
		';
	}else{
		$taksa_txt = '';
	}
	
	$novi_predracun = new bgTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); //konstruktor (portrait, mm, a4, unicode,)
	$novi_predracun->SetTitle('Predračun');
	$novi_predracun->SetMargins(15, 65, 20, true);
	$novi_predracun->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	$novi_predracun->setFontSubsetting(true);
	$img_file = 'tcpdf-main/images/memorandumCH051023.jpg';
	$novi_predracun->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
	$novi_predracun->SetFont('dejavuserif','',9);

	$novi_predracun->AddPage();
	$html = <<<EOD
	<style>
	.bt{
		border-top: 1px solid black;
	}
	.bb{
		border-bottom: 1px solid black;
	}
	.bl{
		border-left: 1px solid black;
	}
	.br{
		border-right: 1px solid black;
	}
	.pt{
		padding-top: 4px;
	}
	.pb{
		padding-bottom: 4px;
	}
	.pr{
		padding-right: 6px;
	}
	.pl{
		padding-left: 6px;
	} 
	.mt{
		margin-top: 6px;
	}
	.mb{
		margin-bottom: 6px;
	}
	.mr{
		margin-right: 6px;
	}
	.ml{
		margin-left: 6px;
	} 
	
	.f11{
		font-size: 12px;
	}
	
	.textC{
		text-align: center;
	}
	.textR{
		text-align: right;
	}
	.footer {
	  width: 100%;
	  text-align: center;
	}
	</style>
	<br>
	<br>
	<table class="pb pt">
		<tr>
			<td class="bb bt pb pt" colspan="2">$text_kupac:</td>
		</tr>
		<tr>
			<td width="40%">$text_ime_prezime: </td>
			<td>$kandidat_ime $kandidat_prezime</td>
		</tr>
		<tr class="pb pt">
			<td width="40%">$text_adresa: </td>
			<td>$kandidat_adresa</td>
		</tr>
	</table>
	<hr>
	<br>
	<div>
		<p>Steinhausen, $datum_kreiranja</p>
	</div>
	<div class="mb">
		<h3 class="bb">$text_avansni_racun: $naziv_predracuna</h3>
	</div>
	
	<div></div>
	
	<div>
		<table class="pb pt">
		
			$vodoravne_cijene
			$taksa_txt
			<tr>
				<td colspan="5" class="bb"></td>
			</tr>
			
			$popust_txt
			<tr>
				<td width="33%"></td>
				<td width="51%">$text_pdv ( $pdv_txt_f % )</td>
				<td width="16%" class="textR">&euro;&nbsp;&nbsp;&nbsp;&nbsp;$pdv_iznos_f</td>
			</tr>
			<tr>
				<td width="33%"></td>
				<td width="51%">$text_ukupno_placanje EUR</td>
				<td width="16%" class="textR">&euro;&nbsp;&nbsp;$ukupno_za_pl_EUR_f</td>
			</tr>
			<tr>
				<td width="33%"></td>
				<td width="51%">$ukupno_za_pl_txt1</td>
				<td width="16%" class="textR">$ukupno_za_pl_txt2</td>
			</tr>
		</table>
	</div>
	<div>
		<p>$text_uslovi_placanja</p>
	</div>

EOD;


	if(is_null($pr_file)){
		$year_skr = date('y');
		$tmp2 = explode('-', $naziv_predracuna);
		$brojac_predracuna = $tmp2[1];
		$file_datum = date('YmdHis'); 
		$predracun_putanja = $file_datum."DIPL".$slovo_drz.$brojac_predracuna.".pdf";

		$update_predracun = $db->prepare("
			UPDATE idk_predracuni
			SET pr_file = :pr_file, pr_datum_kreiranja = now(), pr_stornirano = 0
			WHERE pr_id = :pr_id
		");
		$update_predracun->execute(array(
			':pr_file' => $predracun_putanja,
			':pr_id' => $predracun_id
		));


		// Print text using writeHTMLCell()
		$filename="/files/predracuni_dipl/".$predracun_putanja."";
		$novi_predracun->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
		$novi_predracun->Output(__DIR__ .$filename, 'F');
	
		//$novi_predracun->Output('ugovor_tajitaj.pdf', 'I');
		if($drzava != "Njemacka"){
			return;
		}else{
			$update_predracun = $db->prepare("
				UPDATE idk_predracuni
				SET pr_file_de = :pr_file_de
				WHERE pr_id = :pr_id
			");
			$update_predracun->execute(array(
				':pr_file_de' => $predracun_putanja,
				':pr_id' => $predracun_id
			));
			return;
		}
		
	}else{
		
		$tmp = explode('.', $pr_file);
		$pf_file_de = $tmp[0]."C.".$tmp[1];
		$update_predracun = $db->prepare("
			UPDATE idk_predracuni
			SET pr_file_de = :pr_file_de
			WHERE pr_id = :pr_id
		");
		$update_predracun->execute(array(
			':pr_file_de' => $pf_file_de,
			':pr_id' => $predracun_id
		));
		
		// Print text using writeHTMLCell()
		$filename="/files/predracuni_dipl/".$pf_file_de."";
		$novi_predracun->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
		$novi_predracun->Output(__DIR__ .$filename, 'F');

		return;
		
	}
}

function generisiRacun($predracun_id, $drzava, $storniran = FALSE){
	Global $db; 
	$year_skr = date('y');
	$datum_kreiranja = date('d.m.Y');
	$platiti_do = date('d.m.Y', strtotime($datum_kreiranja. ' + 10 days'));
	
	$get_predracun = $db->prepare("
					SELECT pr_broj_predracuna, nk.id_broj_nd_kandidata, nk.ime_nd_kandidata, nk.prezime_nd_kandidata, nk.ulica_nd_kandidata, nk.postanski_broj_nd_kandidata, nk.grad_nd_kandidata, pr_rata, pr_vrijednost_BAM, pr_vrijednost_EUR, pr_vrijednost_RSD, pr_file, nk.vrsta_ugovora_nd_kandidata, pr_domaca_valuta, jmbg_nd_kandidata
					FROM idk_predracuni
					JOIN idk_nd_kandidata nk
					ON nk.id_broj_nd_kandidata = pr_kandidat_id
					WHERE pr_id = :pr_id
					");

	$get_predracun->execute(array(':pr_id' => $predracun_id));
	$predracun_row = $get_predracun->fetch();
	
	$kandidat_id = $predracun_row["id_broj_nd_kandidata"];
	$kandidat_ime = $predracun_row["ime_nd_kandidata"];
	$kandidat_prezime = $predracun_row["prezime_nd_kandidata"];
	$ulica = $predracun_row["ulica_nd_kandidata"];
	$pbroj = $predracun_row["postanski_broj_nd_kandidata"];
	$grad = $predracun_row["grad_nd_kandidata"];
	$kandidat_jmbg = $predracun_row["jmbg_nd_kandidata"];
	
	$pr_rata = $predracun_row["pr_rata"];
	$pr_vrijednost_BAM = $predracun_row["pr_vrijednost_BAM"];
	$pr_vrijednost_RSD = $predracun_row["pr_vrijednost_RSD"];
	$pr_vrijednost_EUR = $predracun_row["pr_vrijednost_EUR"];
	$pr_file = $predracun_row["pr_file"];
	$vrsta_ugovora = $predracun_row["vrsta_ugovora_nd_kandidata"];
	$pr_domaca_valuta = $predracun_row["pr_domaca_valuta"];
	
	$kandidat_adresa = $ulica.", ".$pbroj." ".$grad;
	$storno_racun_predznak = "";
	$racun_status = 1;
	$text_storno_racun = "";
	if($storniran){
		$query_get_broj_racuna = $db->prepare('
			SELECT racun_broj
			FROM idk_racuni
			WHERE predracun_id = :predracun_id
			AND racun_stornirano = 1
			AND date(racun_datum_storniranja) = CURRENT_DATE();
		');
		$query_get_broj_racuna -> execute(array(':predracun_id' => $predracun_id));
		$row_get_broj_racuna = $query_get_broj_racuna -> fetch();
		$storno_racun_broj = $row_get_broj_racuna['racun_broj'];
		$novi_racun_naziv = $storno_racun_broj;
		$storno_racun_predznak = "- ";
		$racun_status = 2;
		$text_storno_racun = "Storno ";
		$text_minus_popust = "";
		$prethodni_broj_racuna = "";
		$brojac_racuna = "";
	}
	else{
		$brojac_racuna = createBrojRacuna("ch");
		//ako je prvi racun bio na bosanskom ili srpskom onda se mora uzeti i koristiti prethodni broj, jer se treba unijeti kod kreiranja pdf-a za racun na njemackom
		$prethodni_broj_racuna = $brojac_racuna - 1;
		$novi_racun_naziv = "DIPLRCH-".$brojac_racuna."-".$year_skr;
		$text_minus_popust = "-";
	}
	$file_datum = date('YmdHis'); 		
	if($drzava == "Njemacka"){
		
		if($pr_domaca_valuta != "EUR"){
			//fja generisiRacun se prvi put poziva i proslijedjuje joj se drzava kandidata.
			//drugi put se poziva samo ako se radi o bih ili srb i tad proslijedjuje njemacku kako bi se generisao racun_file_de.
			//ovaj if uslov postoji da se ne bi dva puta insertovao racun u bazu, tj kada drugi put pozivamo f-ju proslijedjujemo
			//drzavu Njemacka a posto valuta nije EUR onda ne treba insertovati
			//potrebno je naci prethodno uneseni racun i updateovati racun_file_de
			
			$racun_file = $file_datum."DIPLRCH".$prethodni_broj_racuna."C.pdf";
			if(!$storniran)
				$novi_racun_naziv = "DIPLRCH-".$prethodni_broj_racuna."-".$year_skr;
			
			$update_racun = $db->prepare("
				UPDATE idk_racuni
				SET racun_file_de = :racun_file_de
				WHERE racun_broj = :racun_broj ORDER BY racun_id DESC LIMIT 1
			");
			$update_racun->execute(array(
				':racun_file_de' => $racun_file,
				':racun_broj' => $novi_racun_naziv
			));
		}else{
			//radi se njemackom kandidatu i o njemackom racunu, tako da su oba file-a ista
			$racun_file = $file_datum."DIPLRCH".$brojac_racuna.".pdf";
			//INSERT RACUN
			$insert_racun = $db->prepare("	
						INSERT INTO idk_racuni	
						(racun_broj,  racun_kandidat_id, racun_status, racun_domaca_valuta, racun_vrijednost_BAM, racun_vrijednost_RSD, racun_vrijednost_EUR, racun_file, racun_file_de, predracun_id)	
						VALUES	
						(:racun_broj,:racun_kandidat_id,:racun_status,:racun_domaca_valuta,:racun_vrijednost_BAM,:racun_vrijednost_RSD,:racun_vrijednost_EUR,:racun_file,:racun_file_de,:predracun_id)	
						");	
			$insert_racun->execute(array(	
						':racun_broj' => $novi_racun_naziv,	
						':racun_kandidat_id' => $kandidat_id,	
						':racun_status' => $racun_status,	
						':racun_domaca_valuta' => $pr_domaca_valuta,	
						':racun_vrijednost_BAM' => $pr_vrijednost_BAM,	
						':racun_vrijednost_RSD' => $pr_vrijednost_RSD,	
						':racun_vrijednost_EUR' => $pr_vrijednost_EUR,	
						':racun_file' => $racun_file,
						':racun_file_de' => $racun_file,
						':predracun_id' => $predracun_id
						));
			$racun_id = $db->lastInsertId();
			
			//UPDATE IZDAN RACUN ZA PREDRACUNE
			$update_predracuni = $db->prepare("
										UPDATE idk_predracuni
										SET pr_izdan_racun = 1
										WHERE pr_id = :pr_id
										");
			
			$update_predracuni->execute(array(
				':pr_id' => $predracun_id
			));
		}
	}else{
		$racun_file = $file_datum."DIPLRCH".$brojac_racuna.".pdf";
		//INSERT RACUN
		$insert_racun = $db->prepare("	
					INSERT INTO idk_racuni	
					(racun_broj,  racun_kandidat_id, racun_status, racun_domaca_valuta, racun_vrijednost_BAM, racun_vrijednost_RSD, racun_vrijednost_EUR, racun_file, predracun_id)	
					VALUES	
					(:racun_broj,:racun_kandidat_id,:racun_status,:racun_domaca_valuta,:racun_vrijednost_BAM,:racun_vrijednost_RSD,:racun_vrijednost_EUR,:racun_file,:predracun_id)	
					");	
		$insert_racun->execute(array(	
					':racun_broj' => $novi_racun_naziv,	
					':racun_kandidat_id' => $kandidat_id,	
					':racun_status' => $racun_status,	
					':racun_domaca_valuta' => $pr_domaca_valuta,	
					':racun_vrijednost_BAM' => $pr_vrijednost_BAM,	
					':racun_vrijednost_RSD' => $pr_vrijednost_RSD,	
					':racun_vrijednost_EUR' => $pr_vrijednost_EUR,	
					':racun_file' => $racun_file,
					':predracun_id' => $predracun_id
					));
		$racun_id = $db->lastInsertId();
		
		//UPDATE IZDAN RACUN ZA PREDRACUNE
		$update_predracuni = $db->prepare("
									UPDATE idk_predracuni
									SET pr_izdan_racun = 1
									WHERE pr_id = :pr_id
									");
		
		$update_predracuni->execute(array(
			':pr_id' => $predracun_id
		));
	}
	
	switch($pr_rata){
		case 1:
			$rate_text1 = " - Prvi ";
			$rate_text1_de = " - Erste ";
		break;
		case 2:
			$rate_text1 = " - Drugi ";
			$rate_text1_de = " - Zweite ";
		break;
		case 3:
			$rate_text1 = " - Treći ";
			$rate_text1_de = " - Dritte ";
		break;
		case 4:
			$rate_text1 = " - Četvrti ";
			$rate_text1_de = " - Vierte ";
		break;
		case 5:
			$rate_text1 = " - Peti ";
			$rate_text1_de = " - Funfte ";
		break;
		default:
			$rate_text1 = "";
	}
	
	if($drzava == "BiH"){
		$slovo_drz = "B";
		$domaca_valuta = "BAM";
		$ukupno_za_pl = number_format($pr_vrijednost_BAM, 2, '.', '');
		$ukupno_za_pl_txt1 = "Ukupno za plaćanje BAM";
		$ukupno_za_pl_txt2 = $storno_racun_predznak.$ukupno_za_pl." KM";
		
		$text_kupac = "Kupac";
		$text_ime_prezime = "Ime i prezime";
		$text_plativo_do = "Plativo do";
		$text_avansni_racun = "Račun";
		$text_obrada_podataka_ND = "Obrada podataka za nostrifikaciju diplome ".$rate_text1."dio usluge";
		$text_iznos = "Iznos";
		$text_sifra = "Šifra";
		$text_naziv = "Naziv";
		$text_kolicina = "Količina";
		$text_cijena = "Cijena";
		$text_ukupno = "Ukupno";
		$text_pdv = "PDV";
		$text_popust = "Popust";
		$text_adresa = "Adresa";
		$text_ukupno_placanje = "Ukupno za plaćanje";
		
	}elseif($drzava == "Srbija"){
		$slovo_drz = "S";
		$domaca_valuta = "RSD";
		$ukupno_za_pl = number_format($pr_vrijednost_RSD, 2, '.', '');
		$ukupno_za_pl_txt1 = "Ukupno za plaćanje RSD";
		$ukupno_za_pl_txt2 = $storno_racun_predznak.$ukupno_za_pl." RSD";
		
		$text_kupac = "Kupac";
		$text_ime_prezime = "Ime i prezime";
		$text_plativo_do = "Plativo do";
		$text_avansni_racun = "Račun";
		$text_obrada_podataka_ND = "Obrada podataka za nostrifikaciju diplome ".$rate_text1." dio usluge";
		$text_iznos = "Iznos";
		$text_sifra = "Šifra";
		$text_naziv = "Naziv";
		$text_kolicina = "Količina";
		$text_cijena = "Cena";
		$text_ukupno = "Ukupno";
		$text_pdv = "PDV";
		$text_popust = "Popust";
		$text_adresa = "Adresa";
		$text_ukupno_placanje = "Ukupno za plaćanje";
		
	}elseif($drzava == "Njemacka"){
		//nista ne ispisuje u zadnjem redu kad je Njemacka u pitanju
		$slovo_drz = "D";
		$domaca_valuta = "EUR";
		$ukupno_za_pl = number_format($pr_vrijednost_EUR, 2, '.', '');
		$ukupno_za_pl_txt1 = "";
		$ukupno_za_pl_txt2 = "";
		
		$text_kupac = "Kunde";
		$text_ime_prezime = "Name und Vorname";
		$text_plativo_do = "Zahlbar bis";
		$text_avansni_racun = "Rechnung";
		$text_obrada_podataka_ND = "Dienstleistung Datenverarbeitung ";
		$text_iznos = "Betrag";
		$text_sifra = "Pos";
		$text_naziv = "Bezeichnung";
		$text_kolicina = "Menge";
		$text_ukupno = "Total";
		$text_cijena = "Preis";
		$text_pdv = "MwSt";
		$text_popust = "Rabat";
		$text_adresa = "Adresse";
		$text_ukupno_placanje = "Zahlungsbetrag ";
		
	}
	$pdv_stopa = 0;
	$ukupno_EUR = $pr_vrijednost_EUR/(1+$pdv_stopa);
	$ukupno_EUR_f = number_format($ukupno_EUR, 2, '.', '');
	$ukupno_za_pl_EUR_f = number_format($pr_vrijednost_EUR, 2, '.', '');
	
	$pdv_txt = $pdv_stopa * 100;
	$pdv_txt_f = number_format($pdv_txt, 2, '.', '');
	
	$pdv_iznos = $ukupno_za_pl_EUR_f - $ukupno_EUR_f;
	$pdv_iznos_f = number_format($pdv_iznos, 2, '.', '');
	
	if(in_array(($vrsta_ugovora), array(21,22,23,24,25,61,62,63,64,65))){
		$popust_part = true;
		$popust_procent = 20;
	}elseif(in_array(($vrsta_ugovora), array(2,4,6,8,10,12,71,72,73,74,75))){
		$popust_part = true;
		$popust_procent = 30;
	}elseif(in_array(($vrsta_ugovora), array(9,82,83,84,85))){ //sve dok je 10 popust na placanje u cijelosti(1 rata-> 9)
		$popust_part = true;
		$popust_procent = 10;
	}elseif(in_array(($vrsta_ugovora), array(51,52,53,54,55))){
		$popust_part = true;
		$popust_procent = 50;
	}elseif(in_array(($vrsta_ugovora), array(41,42,43,44,45))){
		$popust_part = true;
		$popust_procent = 70;
	}elseif($vrsta_ugovora == 99){ 
		$popust_part = true;
		$popust_procent = 100;
	}else{
		$popust_part = false;
		$popust_procent = 0;
	}
	
	if($popust_procent != 100){
		$iznos = $ukupno_EUR * (100 / (100 - $popust_procent));
	}else{
		$iznos = 485.73;
	}
	
	$iznos_f = number_format($iznos, 2, '.', '');
	$popust_procent_txt = ''.$popust_procent.'';
	$popust = $iznos_f - $ukupno_EUR_f;
	$popust_f = number_format($popust, 2, '.', '');
	
	if($popust_part){
		
		$vodoravne_cijene = '
		<tr>
			<td class = "bt bb bl" width="8%"><b>'.$text_sifra.'</b></td>
			<td class = "bt bb" width="42%"><b>'.$text_naziv.'</b></td>
			<td class = "bt bb textC" width="10%"><b>'.$text_kolicina.'</b></td>
			<td class = "bt bb textR" width="12%"><b>'.$text_cijena.'</b></td>
			<td class = "bt bb textR" width="14%"><b>'.$text_popust.'</b></td>
			<td class = "bt bb br textR" width="14%"><b>'.$text_ukupno.'</b></td>
		</tr>
		<tr>
			<td width="8%">1</td>
			<td width="42%">'.$text_obrada_podataka_ND.'</td>
			<td class = "textC" width="10%">1</td>
			<td class = "textR" width="12%">
				&euro;&nbsp;&nbsp; '.$storno_racun_predznak.$iznos_f.'				
			</td>
			<td class = "textR" width="14%">
				&nbsp;&nbsp;'.$text_minus_popust.' '.$popust_procent_txt.' %
			</td>
			<td class = "textR" width="14%">
				&euro;&nbsp;&nbsp;'.$storno_racun_predznak.$ukupno_EUR_f.'
			</td>
		</tr>
		';
		
		$popust_txt = '
		<tr>
			<td width="33%" class="bt"></td>
			<td width="51%" class="bt">'.$text_iznos.'</td>
			<td width="16%" class="textR bt">&euro;&nbsp;&nbsp;'.$storno_racun_predznak.$iznos_f.'</td>
		</tr>
		<tr>
			<td width="33%"></td>
			<td width="51%">'.$text_popust.' '.$popust_procent_txt.'%</td>
			<td width="16%" class="textR">&euro;&nbsp;&nbsp;'.$text_minus_popust.$popust_f.'</td>
		</tr>
		<tr>
			<td width="33%"></td>
			<td width="51%">'.$text_ukupno.'</td>
			<td width="16%" class="textR">&euro;&nbsp;&nbsp;'.$storno_racun_predznak.$ukupno_EUR_f.'</td>
		</tr>
		';
	}else{
		$popust_txt = '
		<tr>
			<td width="33%" class="bt"></td>
			<td width="51%" class="bt">'.$text_ukupno.'</td>
			<td width="16%" class="textR bt">&euro;&nbsp;&nbsp;'.$storno_racun_predznak.$ukupno_EUR_f.'</td>
		</tr>
		';
		$vodoravne_cijene = '
		<tr>
			<td class = "bt bb bl" width="8%"><b>'.$text_sifra.'</b></td>
			<td class = "bt bb" width="50%"><b>'.$text_naziv.'</b></td>
			<td class = "bt bb textC" width="10%"><b>'.$text_kolicina.'</b></td>
			<td class = "bt bb textC" width="16%"><b>'.$text_cijena.'</b></td>
			<td class = "bt bb br textC" width="16%"><b>'.$text_ukupno.'</b></td>
		</tr>
		<tr>
			<td width="8%">1</td>
			<td width="50%">'.$text_obrada_podataka_ND.'</td>
			<td class = "textC" width="10%">1</td>
			<td class = "textC" width="16%">
				&euro;&nbsp;&nbsp; '.$storno_racun_predznak.$iznos_f.'				
			</td>
			<td class = "textR" width="16%">
				&euro;&nbsp;&nbsp;'.$storno_racun_predznak.$ukupno_EUR_f.'
			</td>
		</tr>
		';
	}
	if($vrsta_ugovora == 26 OR $vrsta_ugovora == 27 /*OR $vrsta_ugovora == 28 OR $vrsta_ugovora == 29*/){
		$cijena_takse_f = number_format($cijena_takse, 2, '.', '');
		$taksa_txt = '
			<tr>
				<td width="8%">2</td>
				<td width="50%">Taksaaaa ceka se tekst</td>
				<td class = "textC" width="10%">1</td>
				<td class = "textC" width="16%">
					&euro;&nbsp;&nbsp; '.$cijena_takse_f.'				
				</td>
				<td class = "textR" width="16%">
					&euro;&nbsp;&nbsp;'.$cijena_takse_f.'
				</td>
			</tr>
		';
	}else{
		$taksa_txt = '';
	}
	
	$novi_racun = new bgTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); //konstruktor (portrait, mm, a4, unicode,)
	$novi_racun->SetTitle('Račun');
	$novi_racun->SetMargins(15, 65, 20, true);
	$novi_racun->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	
	$novi_racun->setFontSubsetting(true);
	
	$img_file = 'tcpdf-main/images/memorandumCH051023.jpg';
	$novi_racun->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
	$novi_racun->SetFont('dejavuserif','',9);
	
	
	$novi_racun->AddPage();
	$html = <<<EOD
	<style>
	.bt{
		border-top: 1px solid black;
	}
	.bb{
		border-bottom: 1px solid black;
	}
	.bl{
		border-left: 1px solid black;
	}
	.br{
		border-right: 1px solid black;
	}
	.pt{
		padding-top: 4px;
	}
	.pb{
		padding-bottom: 4px;
	}
	.pr{
		padding-right: 6px;
	}
	.pl{
		padding-left: 6px;
	} 
	.mt{
		margin-top: 6px;
	}
	.mb{
		margin-bottom: 6px;
	}
	.mr{
		margin-right: 6px;
	}
	.ml{
		margin-left: 6px;
	} 
	
	.f11{
		font-size: 12px;
	}
	
	.textC{
		text-align: center;
	}
	.textR{
		text-align: right;
	}
	.footer {
	  width: 100%;
	  text-align: center;
	}
	</style>
	<br>
	<br>
	<table class="pb pt">
		<tr>
			<td class="bb bt pb pt" colspan="2">$text_kupac:</td>
		</tr>
		<tr>
			<td width="40%">$text_ime_prezime: </td>
			<td>$kandidat_ime $kandidat_prezime</td>
		</tr>
		<tr class="pb pt">
			<td width="40%">$text_adresa: </td>
			<td>$kandidat_adresa</td>
		</tr>
	</table>
	<hr>
	<br>
	<div>
		<p>Steinhausen, $datum_kreiranja</p>
		<p>$text_plativo_do: $platiti_do</p>
	</div>
	<div class="mb">
		<h3 class="bb">$text_storno_racun$text_avansni_racun: $novi_racun_naziv</h3>
	</div>
	
	<div></div>
	
	<div>
		<table class="pb pt">
		
			$vodoravne_cijene
			$taksa_txt
			<tr>
				<td colspan="5" class="bb"></td>
			</tr>
			
			$popust_txt
			<tr>
				<td width="33%"></td>
				<td width="51%">$text_pdv ( $pdv_txt_f % )</td>
				<td width="16%" class="textR">&euro;&nbsp;&nbsp;&nbsp;&nbsp;$pdv_iznos_f</td>
			</tr>
			<tr>
				<td width="33%"></td>
				<td width="51%">$text_ukupno_placanje EUR</td>
				<td width="16%" class="textR">&euro;&nbsp;&nbsp;$storno_racun_predznak$ukupno_za_pl_EUR_f</td>
			</tr>
			<tr>
				<td width="33%"></td>
				<td width="51%">$ukupno_za_pl_txt1</td>
				<td width="16%" class="textR">$ukupno_za_pl_txt2</td>
			</tr>
		</table>
	</div>

EOD;
	
	// Print text using writeHTMLCell()
	$filename="/files/racuni_dipl/".$racun_file."";
	$novi_racun->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
	$novi_racun->Output(__DIR__ .$filename, 'F');
	
}

function generisiInoUplatnicu($pr_id){
	Global $db;
	Global $logged_employee_id;
	
	$query_get_info = $db->prepare('
		SELECT pr_broj_predracuna, nk.id_broj_nd_kandidata, nk.ime_nd_kandidata, nk.prezime_nd_kandidata, pr_vrijednost_EUR, pr_rata
		FROM idk_predracuni
		JOIN idk_nd_kandidata nk
		ON nk.id_broj_nd_kandidata = pr_kandidat_id
		WHERE pr_id = :pr_id
	');
	
	$query_get_info -> execute(array(':pr_id' => $pr_id));
	
	$row_get_info = $query_get_info->fetch();
	
	$predracun_naziv = $row_get_info['pr_broj_predracuna'];
	$kandidat_id = $row_get_info['id_broj_nd_kandidata'];
	$kandidat_ime_prezime = $row_get_info['ime_nd_kandidata']." ".$row_get_info['prezime_nd_kandidata'];
	$pr_vrijednost_EUR =  $row_get_info['pr_vrijednost_EUR'];
	$pr_rata =  $row_get_info['pr_rata'];
	
	$mjesec = date('m');
	$godina = date('y');
	$brojac_uplatnice = createBrojUplatnice("Ino");
	$uplatnica_naziv = "UPI-".$brojac_uplatnice."-".$mjesec."-".$godina;
	
	$query_insert_uplatnicu = $db -> prepare('
				INSERT INTO idk_nd_kandidata_dokumenti	
					(naziv_dokument_nd,  naziv_dokument_ostali_nd, id_kandidata_dokument_nd, vrijeme_dodavanja_dokument_nd, dodao_zaposlenik_dokument_nd, tip_dokumenta, broj_rate)	
				VALUES	
					(:naziv_dokument_nd,:naziv_dokument_ostali_nd,:id_kandidata_dokument_nd, now(),:dodao_zaposlenik_dokument_nd,:tip_dokumenta, :broj_rate)	
	');
	
	
	$query_insert_uplatnicu -> execute (array(
		':naziv_dokument_nd' => $uplatnica_naziv.'.pdf',
		':naziv_dokument_ostali_nd' => 'uplatnica',
		':id_kandidata_dokument_nd' => $kandidat_id,
		':dodao_zaposlenik_dokument_nd' => $logged_employee_id,
		':broj_rate' => $pr_rata,
		':tip_dokumenta' => '2'
	));
	
	$nova_ino_uplatnica = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); //konstruktor (portrait, mm, a4, unicode,) 	
	$nova_ino_uplatnica->setPrintHeader(false);
	$nova_ino_uplatnica->setPrintFooter(false);
	$nova_ino_uplatnica->SetTitle('Ino Uplatnica');																												
	$nova_ino_uplatnica->SetMargins(15, 10, 20, true);																											
	$nova_ino_uplatnica->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);																						
																																						
	$nova_ino_uplatnica->setFontSubsetting(true);																												
																					
	$nova_ino_uplatnica->SetFont('dejavuserif','',9);																											
																																						
																																						
	$nova_ino_uplatnica->AddPage();
	$html = <<<EOD
	<style>
	.bt{
		border-top: 1px solid black;
	}
	.bb{
		border-bottom: 1px solid black;
	}
	.bl{
		border-left: 1px solid black;
	}
	.br{
		border-right: 1px solid black;
	}
	.pt{
		padding-top: 4px;
	}
	.pb{
		padding-bottom: 4px;
	}
	.pr{
		padding-right: 6px;
	}
	.pl{
		padding-left: 6px;
	} 
	.mt{
		margin-top: 6px;
	}
	.mb{
		margin-bottom: 6px;
	}
	.mr{
		margin-right: 6px;
	}
	.ml{
		margin-left: 6px;
	} 
	
	.f11{
		font-size: 12px;
	}
	
	.textC{
		text-align: center;
	}
	.textR{
		text-align: right;
	}
	.footer {
	  width: 100%;
	  text-align: center;
	}
	</style>
	<p style="font-size:20px;"><b> Zahlungsdaten </b></p>
	<table class="pb pt">
		<tr>
			<td width="100%">Begünstiger:</td>
		</tr>
		<tr>
			<td class="bb bt br bl" width="100%"> Jobstep Int GmbH</td>
		</tr>
		<tr>
			<td width="45%">IBAN/Kontonummer:</td>
			<td width="10%"></td>
			<td width="45%">BIC/Bankleitzahl:</td>
		</tr>
		<tr>
			<td class="bb bt br bl" width="45%">CH76 0027 3273 1956 1160 C</td>
			<td width="10%"></td>
			<td class="bb bt br bl" width="45%">UBSWCHZH80A</td>
		</tr>
		<tr>
			<td width="45%">Kreditinstitut:</td>
			<td width="10%"></td>
			<td width="45%">Betrag:</td>
		</tr>
		<tr>
			<td class="bb bt br bl" width="45%">UBS Switzerland AG</td>
			<td width="10%"></td>
			<td class="bb bt br bl" width="45%">&euro; $pr_vrijednost_EUR </td>
		</tr>
		<tr>
			<td width="100%">Verwendugszweck:</td>
		</tr>
		<tr>
			<td class="bb bt br bl" width="100%">$predracun_naziv</td>
		</tr>
	</table>
	

EOD;
	$nova_ino_uplatnica->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
	$filename="/files/ugovori_uplatnice_dipl/".$uplatnica_naziv.".pdf";
	$nova_ino_uplatnica->Output(__DIR__ .$filename, 'F');
	// $nova_ino_uplatnica->Output('testUplatnica.pdf', 'I');
}

//FUNKCIJA ZA GENERISANJE UGOVORA ZA NAČIN NAPLATE PREKO DOMAĆE ZEMLJE (SRBIJA Za Sad, A MOGUCE KASNIJE I BIH) 
//SA OMOGUĆENIM NOVIM PRINCIPOM PRIHVATANJA UGOVORA ELEKTRONSKI
function generisiUgovorNew($kandidat_id, $ugovor_jezik){
	Global $db;
	//QUERY KOJI POVLAČI STATUS UGOVORA, TOKEN, FILE NA KLIJENTOVOM JEZIKU, ///////////////////////////////////////////////////////
	// I IP ADRESU + TIMESTAMP UKOLIKO JE FUNKCIJA POZVANA RADI "PREVODA" NAPRAVLJENOG UGOVORA NA NJEMAČKI (VRŠI SE PROVJEROM is_null(ug_file) kasnije
	$query_get_status_ugovora = $db->prepare('
		SELECT ug_id, ug_broj, ug_status, ug_token, ug_file, ug_ip_adresa, ug_timestamp, ug_datum_prihvatanja, ug_razlog_odbijanja, ug_jezik, ug_datum_otvaranja_linka 
		FROM idk_nd_ugovori																													
		WHERE ug_kandidat_id = :kandidat_id
		ORDER BY ug_id DESC
	');

	$query_get_status_ugovora -> execute(array(
		':kandidat_id' => $kandidat_id
	));

	$row_get_status_ugovora = $query_get_status_ugovora -> fetch();

	$ug_id = $row_get_status_ugovora['ug_id'];
	$ug_broj = $row_get_status_ugovora['ug_broj'];
	$ug_status = $row_get_status_ugovora['ug_status'];
	$ug_token = $row_get_status_ugovora['ug_token'];
	$ug_file = $row_get_status_ugovora['ug_file'];
	$ug_datum_prihvatanja = $row_get_status_ugovora['ug_datum_prihvatanja'];
	$ug_datum_otvaranja_linka = $row_get_status_ugovora['ug_datum_otvaranja_linka'];
	$ug_ip_adresa = $row_get_status_ugovora['ug_ip_adresa'];
	$ug_timestamp = $row_get_status_ugovora['ug_timestamp'];
	$ug_razlog_odbijanja = $row_get_status_ugovora['ug_razlog_odbijanja'];
	$ug_jezik_org = $row_get_status_ugovora['ug_jezik'];

	if(is_null($ug_datum_otvaranja_linka) && $ug_status == 1){
		$query_set_otvorio_link = $db -> prepare('
			UPDATE idk_nd_ugovori
			SET ug_status = 4, ug_datum_otvaranja_linka = now()
			WHERE ug_id = :ug_id
			');
			
		$query_set_otvorio_link -> execute(array(':ug_id' => $ug_id));
		$ug_status = 4;
	}
	
	include('lang/only_'.$ug_jezik_org.'.php');
	$main_text 	= "";
	
	//AKO JE UGOVOR VEC ODBIJEN PREUSMJERI KORISNIKA NA PAGE SA MOGUCNOSCU FEEDBACKA ZASTO JE ODBIJEN, AKO JE VEC PRETHODNO NEKAD UNESEN RAZLOG ODBIJANJA,
	//PROSLIJEDI GA NA PAGE KOJI NE ZAHTJEVA UNOS RAZLOGA I KOJI VIŠE NEMA POZIVA OVE FUNKCIJE////////////////////////////////////////////////////////////
	if ($ug_status == 3){
		$url_odbijen = getSiteUrlr();
		if(is_null($ug_razlog_odbijanja) == false){
			$url_odbijen = $url_odbijen.'ugovor_handler_new.php?action=odbijen_razlogom&token='.$ug_token.'&jezik='.$ug_jezik_org;
		}
		else{
			$url_odbijen = $url_odbijen.'ugovor_handler_new.php?action=odbijen&token='.$ug_token.'&jezik='.$ug_jezik_org;
		}
		header("Location:".$url_odbijen);
	}
	else if($ug_status == 0){
		$url_arhiviran = getSiteUrlr().'ugovor_handler_new.php?action=arhiviran&jezik='.$ug_jezik_org;
		header("Location:".$url_arhiviran);
	}

	//QUERY KOJI POVLAČI POTREBNE PODATKE KANDIDATA KOJEM SE ŠALJE UGOVOR//////////////////////////////
	$query_get_kandidat = $db -> prepare('														
		SELECT ime_nd_kandidata, prezime_nd_kandidata, grad_nd_kandidata, ulica_nd_kandidata, postanski_broj_nd_kandidata,
		jmbg_nd_kandidata, broj_licne_karte_nd_kandidata, vrsta_ugovora_nd_kandidata
		FROM idk_nd_kandidata
		WHERE id_broj_nd_kandidata = :kandidat_id
	');

	$query_get_kandidat -> execute(array(
		':kandidat_id' => $kandidat_id
	));

	$row_get_kandidat 				= $query_get_kandidat -> fetch();
	$kandidat_ime 					= $row_get_kandidat['ime_nd_kandidata'];
	$kandidat_prezime 				= $row_get_kandidat['prezime_nd_kandidata'];
	$kandidat_fullname 				= $kandidat_ime.' '.$kandidat_prezime;
	$postanski_broj_nd_kandidata 	= $row_get_kandidat['postanski_broj_nd_kandidata'];
	$vrsta_ugovora_nd_kandidata 	= $row_get_kandidat['vrsta_ugovora_nd_kandidata'];
	$grad_nd_kandidata			 	= $row_get_kandidat['grad_nd_kandidata'];
	$ulica_nd_kandidata			 	= $row_get_kandidat['ulica_nd_kandidata'];
	$jmbg_nd_kandidata			 	= $row_get_kandidat['jmbg_nd_kandidata'];
	$broj_licne_karte_nd_kandidata 	= $row_get_kandidat['broj_licne_karte_nd_kandidata'];

	if($ug_jezik_org == 'bs' AND $ug_status == 2){				
		$main_text = $text_clanovi_1.'<div></div><div></div><div></div><div></div>'.$text_clanovi_bez_takse.$text_clanovi_2;
	}
	else if($ug_jezik_org == 'bs'){
		$main_text = $text_clanovi_1.'<div></div><div></div><div></div>'.$text_clanovi_bez_takse.$text_clanovi_2;
	}
	else{
		$main_text = $text_clanovi_1.$text_clanovi_bez_takse.$text_clanovi_2;
	}

	if($ug_status == 2){
		$datum_zaključivanja = date("d.m.Y", strtotime($ug_datum_prihvatanja));
	}else{
		$datum_zaključivanja = date("d.m.Y.",time());
	}
	//NA KOJEM JEZIKU SE PRAVI UGOVOR? -> INCLUDE TEXT KOJI ĆE SE KORISTITI KOD UGOVORA + PRIPREMI STRING KOJI ĆE POPUNJAVATI TEXT IZNAD NASLOVA//////////////////////////////////////////
	if($ug_jezik_org == "bs"){
		//KADA I AKO IKADA BUDE SE BOSNA VRACALA NA STARI NACIN UZ ELEKTRONSKO PRIHVATANJE ONDA CE I OVO TREBATI
		$podaci_kandidat = '
				(u daljem tekstu: nalogoprimac)
				<br><br> i <br><br>
				'.$kandidat_fullname.' iz '.$grad_nd_kandidata
				.' ul. '.$ulica_nd_kandidata.', JMBG: '.$jmbg_nd_kandidata.' broj lične karte: '
				.$broj_licne_karte_nd_kandidata.' (u daljnjem tekstu: nalogodavac),<br><br>zaključuju ovaj
		';
	
	}else if($ug_jezik_org == "sr"){
		$podaci_kandidat ='
			Jobstep International d.o.o. Beograd, ( Jobstep International d.o.o.) sa adresom sedišta:
			Bulevar Mihajla Pupina 165 G, 11070 Novi Beograd; matični broj (MB): 21569143; poreski identifikacioni broj (PIB): 111915414, koje zastupa direktor Denis Selmanović, kao pružalac usluge sa jedne strane (dalje: pružalac usluge)

				<br><br>i<br><br>
				'.$kandidat_fullname.' iz '.$grad_nd_kandidata
				.' ul. '.$ulica_nd_kandidata.' '.$postanski_broj_nd_kandidata.', JMBG: '.$jmbg_nd_kandidata.', broj LK: '
				.$broj_licne_karte_nd_kandidata.' kao korisnik usluge sa druge strane (dalje: korisnik usluge),
				<br><br>(zajedno označeni kao: ugovorne strane),
				<br><br>Dana '.$datum_zaključivanja.' godine zaključuju 
		';
		$podaci_kandidat = '<div><h3 class="textC mb"><i>UGOVOR O PRUŽANJU USLUGE POSREDOVANJA U POSTUPKU <br>NOSTRIFIKACIJE/EVALUACIJE DIPLOME</i></h3></div>'.$podaci_kandidat;
	}

	//KREIRANJE NOVOG OBJEKTA I DEFINISANJE ISTOG/////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$novi_ugovor = new bgTCPDFnew(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); //konstruktor (portrait, mm, a4, unicode,)
	$novi_ugovor->SetTitle('Ugovor');
	$novi_ugovor->SetMargins(15, 60, 20, true);
	$novi_ugovor->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$novi_ugovor->setFontSubsetting(true);

	$img_file = 'tcpdf-main/images/memorandumSrbija.jpg';
	$novi_ugovor->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
	$novi_ugovor->SetFont('dejavuserif','',11);


	$novi_ugovor->AddPage();

	//POVLAČENJE IP ADRESE KLIJENTA KOJI PRIHVATA UGOVOR U SLUČAJU DA GA VEĆ NIJE POTPISAO////
	//AKO JE IPAK POTPISAO, KORISTI IP ADRESU I TIMESTAMP IZ BAZE							//
	if(is_null($ug_file) == true ){
		
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
			$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
		}
		else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		$timestamp = date("F j, Y, G:i T");

	}
	else{
		$ip = $ug_ip_adresa;
		$timestamp = $ug_timestamp;
	}
	$datum_potpisa = date("d.m.Y.",time());

	//DIO KODA KOJI DEFINIŠE ZADNJI TEKST NA OSNOVU TRENUTNOG STATUSA UGOVORA 2-POSLAN->STAVI POTPIS, ELSE->STAVI BUTTON DA PRIHVATI/ODBIJE 
	//*ZA URADITI* DUGME ZA ODBIJANJE/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if($ug_status == 2){
		
		if($ug_jezik_org == "bs"){
			$text_na_kraju = '<div></div><div></div><div></div><div></div><div></div><div></div>';
		}
		else if($ug_jezik_org == "sr"){
			$text_na_kraju = '
				<p>
					U Beogradu, dana '.$datum_potpisa.' godine
				</p>
				<table width="100%" class="pt pb">
					<tr>
						<td width="40%">Pružalac usluge (Denis Selmanović):</td>
						<td width="10%"></td>
						<td width="50%">Korisnik usluge ('.$kandidat_ime.' '.$kandidat_prezime.'):</td>
					</tr>
					<tr>
						
						<td width="50%"></td>
						<td width="50%" class="f9"><u>ip: '.$ip.', '.$timestamp.'</u></td>
					</tr>
					
				</table>
			';			
		}
		$broj_ugovora = "Broj ugovora: ".$ug_broj."<br>";
	}else{
		
		$text_prihvati_ugovor 	= "PRIHVATI UGOVOR";
		$text_odbij_ugovor 		= "ODBIJ UGOVOR";
		
		$url_prihvatam = getSiteUrlr();//kreiranje url-a koji vodi na ugovor_handler radi unosa svih podataka vezanih za file 																	//
		$url_prihvatam = $url_prihvatam.'ugovor_handler_new.php?action=prihvacen&token='.$ug_token.'&jezik='.$ug_jezik_org;
		$url_odbijam = getSiteUrlr();
		$url_odbijam = $url_odbijam.'ugovor_handler_new.php?action=odbijen&token='.$ug_token.'&jezik='.$ug_jezik_org;
		$text_na_kraju = '																																										
			<table style="margin-top: 40px;" width="100%">
				<tr>
					<td width="10%" height="15px"></td>
					<td width="30%" height="15px" class="button-iza textC"><a class="button-text" style="padding-right: 20px;"  href="'.$url_prihvatam.'">'.$text_prihvati_ugovor.'</a></td>
					<td width="20%" height="15px"></td>
					<td width="30%" height="15px" class="button-iza textC"><a class="button-text" style="padding-right: 20px;"  href="'.$url_odbijam.'">'.$text_odbij_ugovor.'</a></td>
					<td width="10%" height="15px"></td>
				</tr>
			</table>';
		$podaci_kandidat = $text_na_kraju.'<br>'.$podaci_kandidat;
		$broj_ugovora = "Broj ugovora: ".$ug_broj."<br><br><br>";
		
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//STYLOVI KOJI SE KORISTE PRI HTML KODU KOJIM SE GENERIŠE PDF FILE
	$style = '
	<style>
		.justify{
			text-align: justify;
			text-justify: inter-word;
		}
		.bt{
			border-top: 1px solid black;
		}
		.bb{
			border-bottom: 1px solid black;
		}
		.bl{
			border-left: 1px solid black;
		}
		.br{
			border-right: 1px solid black;
		}
		.pt{
			padding-top: 6px;
		}
		.pb{
			padding-bottom: 6px;
		}
		.pr{
			padding-right: 6px;
		}
		.pl{
			padding-left: 6px;
		} 
		.mt{
			margin-top: 6px;
		}
		.mb{
			margin-bottom: 6px;
		}
		.mr{
			margin-right: 6px;
		}
		.ml{
			margin-left: 6px;
		} 
		
		.f11{
			font-size: 12px;
		}
		
		.f9{
			font-size: 9px;
		}
		
		.textC{
			text-align: center;
		}
		.textR{
			text-align: right;
		}
		.footer {
		width: 100%;
		text-align: center;
		}

		.button-text {
			text-decoration: none;
			color: #FFFFFF;
			font-size: 11 px;
		}
		
		.button-iza{

			background-color: #383d51; 
			border-top: 1px solid #383d51;
			border-right: 2px solid #5d959e;
			border-bottom: 2px solid #5d959e;
		}
	</style>';
	//*ZA URADITI* STANJITI OVAJ STYLE STRING, IZBACITI KLASE KOJE NE KORISTIM
	
	//SPAJANJE SVIH STRINGOVA KOJI ČINE FILE
	$html = <<<EOD
	$podaci_kandidat
	$main_text
	$style
	$broj_ugovora
	$text_na_kraju
EOD;
///////////////////////////////////////


	$text_downloaded 		= "";
	$ug_name 				= "";
	$drzava_predracun 		= "";
	
	if($ug_jezik_org == "bs"){
		$ug_name			= "UGCHB-";
		$drzava_predracun 	= "BiH";
		$text_downloaded 	= "Ugovor";
	}else if($ug_jezik_org == "sr"){
		$ug_name			= "UGCHS-";
		$drzava_predracun 	= "Srbija";
		$text_downloaded 	= "Ugovor";
	}
	
	$query_ug_redni_broj = $db ->prepare('
		SELECT COUNT(ug_id) as cnt FROM idk_nd_ugovori 
		WHERE ug_file IS NOT NULL 
		AND year(CURRENT_DATE()) = year(ug_datum_prihvatanja)
	');
	
	$query_ug_redni_broj -> execute();
	$row_ug_redni_broj = $query_ug_redni_broj -> fetch();
	$cnt = $row_ug_redni_broj['cnt'];
	if(is_null($ug_file)==true){
		$cnt=$cnt+1;	
	}

	$ug_name = $ug_name.$cnt."-".date ("m-y", time());
	$novi_ugovor->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true); // GENERIŠE FILE
	$url_header = "";

	if($ug_status == 2){

		if(is_null($ug_file) == true){//pravi org
		
			$novi_ugovor->Output(__DIR__ . '/tcpdf-main/ugovori/'.$ug_name.'.pdf', 'F');
			$url_header = getSiteUrlr();
			$url_header = $url_header.'ugovor_handler_new.php?action=generisi_ugovor&token='.$ug_token.'&jezik='.$ug_jezik_org.'&fileName='.$ug_name.'.pdf&&ug_ip_adresa='.$ip.'&ug_timestamp='.$timestamp;							
		}			
		else if(is_null($ug_file) == false){
			//DODANO POTREBNO TESTIRATI START
			$query_get_predracun = $db->prepare('
				SELECT pr_id, pr_file, pr_file_de
				FROM idk_predracuni 
				WHERE pr_rata = 1
				AND pr_status = 1
				AND pr_uplaceno = 0
				AND pr_naplata_preko = 2
				AND pr_kandidat_id = :kandidat_id
			');
			$query_get_predracun -> execute(array(':kandidat_id' => $kandidat_id));
			$row_cnt 			= $query_get_predracun -> rowCount();
			if($row_cnt != 0){

				$row_get_predracun 	= $query_get_predracun -> fetch();
				$predracun_id 		= $row_get_predracun['pr_id'];
				$pr_file 			= $row_get_predracun['pr_file'];
				$pr_file_de 		= $row_get_predracun['pr_file_de'];
				
				if(is_null($pr_file)){
					
					$url_header = getSiteUrlr();
					$url_header = $url_header.'ugovor_handler_new.php?action=generisi_predracun&token='.$ug_token.'&jezik='.$ug_jezik_org.'&pr_id='.$predracun_id;
				}
				else{
					$url_header = getSiteUrlr();																														
					$url_header = $url_header.'ugovor_handler_new.php?action=typage&token='.$ug_token.'&jezik='.$ug_jezik_org.'&fileName='.$ug_file;
				}
				
			}
			else{
				$url_header = getSiteUrlr();																														
				$url_header = $url_header.'ugovor_handler_new.php?action=typage&token='.$ug_token.'&jezik='.$ug_jezik_org.'&fileName='.$ug_file;
			}
			//DODANO POTREBNO TESTIRATI END
			
		}
		// var_dump($url_header);
		// exit();
		header("Location:".$url_header);

	}
	else{		
		$novi_ugovor->Output($text_downloaded.".pdf", 'I');
	}
}

