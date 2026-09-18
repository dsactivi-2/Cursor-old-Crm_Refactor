// <?php
// include("includes/functions.php");
// require_once('tcpdf-main/tcpdf.php');
// // include('includes/functions.php');
// include('includes/connect.php');
// // DEFINISANJE NOVE KLASE KOJA SE KORISTI U generisiUgovor();
// class bgTCPDF extends TCPDF {
	
    // public function Header() {
        // $auto_page_break = $this->AutoPageBreak;
        // $this->SetAutoPageBreak(false, 0);
		// $img_file = 'tcpdf-main/images/memorandum.jpg';
		// $this->Image($img_file, 0, 3, 210, 293, '', '', '', false, 300, '', false, false, 0);
        // $this->SetAutoPageBreak($auto_page_break, 40);
        // $this->setPageMark();
    // }
// }
// function FIX_AFTER_MIGRATION_generisiRacun($predracun_id, $drzava, $arg_datum, $storniran = NULL){
	// // var_dump($drzava);
	// // exit();
	// Global $db; 
	// $year_skr = date('y');
	// $datum_kreiranja = date('d.m.Y', strtotime($arg_datum));
	// $platiti_do = date('d.m.Y', strtotime($datum_kreiranja. ' + 10 days'));
	// // var_dump($datum_kreiranja);
	// // var_dump($platiti_do);
	// $get_predracun = $db->prepare("
					// SELECT pr_broj_predracuna, nk.id_broj_nd_kandidata, nk.ime_nd_kandidata, nk.prezime_nd_kandidata, nk.ulica_nd_kandidata, nk.postanski_broj_nd_kandidata, nk.grad_nd_kandidata, pr_rata, pr_vrijednost_BAM, pr_vrijednost_EUR, pr_vrijednost_RSD, pr_file, nk.vrsta_ugovora_nd_kandidata, pr_domaca_valuta, jmbg_nd_kandidata
					// FROM idk_predracuni
					// JOIN idk_nd_kandidata nk
					// ON nk.id_broj_nd_kandidata = pr_kandidat_id
					// WHERE pr_id = :pr_id
					// ");

	// $get_predracun->execute(array(':pr_id' => $predracun_id));
	// $predracun_row = $get_predracun->fetch();
	
	// $kandidat_id = $predracun_row["id_broj_nd_kandidata"];
	// $kandidat_ime = $predracun_row["ime_nd_kandidata"];
	// $kandidat_prezime = $predracun_row["prezime_nd_kandidata"];
	// $ulica = $predracun_row["ulica_nd_kandidata"];
	// $pbroj = $predracun_row["postanski_broj_nd_kandidata"];
	// $grad = $predracun_row["grad_nd_kandidata"];
	// $kandidat_jmbg = $predracun_row["jmbg_nd_kandidata"];
	
	// $pr_rata = $predracun_row["pr_rata"];
	// $pr_vrijednost_BAM = $predracun_row["pr_vrijednost_BAM"];
	// $pr_vrijednost_RSD = $predracun_row["pr_vrijednost_RSD"];
	// $pr_vrijednost_EUR = $predracun_row["pr_vrijednost_EUR"];
	// $pr_file = $predracun_row["pr_file"];
	// $vrsta_ugovora = $predracun_row["vrsta_ugovora_nd_kandidata"];
	// $pr_domaca_valuta = $predracun_row["pr_domaca_valuta"];
	
	// $kandidat_adresa = $ulica.", ".$pbroj." ".$grad;
	// $storno_racun_predznak = "";
	// $racun_status = 1;
	// $text_storno_racun = "";
	// if($storniran){
		// $query_get_broj_racuna = $db->prepare('
			// SELECT racun_broj
			// FROM idk_racuni
			// WHERE predracun_id = :predracun_id
			// AND racun_stornirano = 1
			// AND date(racun_datum_storniranja) = CURRENT_DATE();
		// ');
		// $query_get_broj_racuna -> execute(array(':predracun_id' => $predracun_id));
		// $row_get_broj_racuna = $query_get_broj_racuna -> fetch();
		// $storno_racun_broj = $row_get_broj_racuna['racun_broj'];
		// $novi_racun_naziv = $storno_racun_broj;
		// $storno_racun_predznak = "- ";
		// $racun_status = 2;
		// $text_storno_racun = "Storno ";
		// $text_minus_popust = "";
	// }
	// else{
		// $brojac_racuna = createBrojRacuna("ch");
		// $prethodni_broj_racuna = $brojac_racuna - 1;
		// $novi_racun_naziv = "DIPLRCH-".$brojac_racuna."-".$year_skr;
		// $text_minus_popust = "-";
	// }
	// $file_datum = date('YmdHis', strtotime($arg_datum)); 		
	// if($drzava == "Njemacka"){
		
		// if($pr_domaca_valuta != "EUR"){
			// //fja generisiRacun se prvi put poziva i proslijedjuje joj se drzava kandidata.
			// //drugi put se poziva samo ako se radi o bih ili srb i tad proslijedjuje njemacku kako bi se generisao racun_file_de.
			// //ovaj if uslov postoji da se ne bi dva puta insertovao racun u bazu, tj kada drugi put pozivamo f-ju proslijedjujemo
			// //drzavu Njemacka a posto valuta nije EUR onda ne treba insertovati
			// //potrebno je naci prethodno uneseni racun i updateovati racun_file_de
			
			// $racun_file = $file_datum."DIPLRCH".$prethodni_broj_racuna."C.pdf";
			// if(!$storniran)
				// $novi_racun_naziv = "DIPLRCH-".$prethodni_broj_racuna."-".$year_skr;
			
			// $update_racun = $db->prepare("
				// UPDATE idk_racuni
				// SET racun_file_de = :racun_file_de
				// WHERE racun_broj = :racun_broj ORDER BY racun_id DESC LIMIT 1
			// ");
			// $update_racun->execute(array(
				// ':racun_file_de' => $racun_file,
				// ':racun_broj' => $novi_racun_naziv
			// ));
		// }else{
			// //radi se njemackom kandidatu i o njemackom racunu, tako da su oba file-a ista
			// $racun_file = $file_datum."DIPLRCH".$brojac_racuna.".pdf";
			// //INSERT RACUN
			// $insert_racun = $db->prepare("	
						// INSERT INTO idk_racuni	
						// (racun_broj,  racun_kandidat_id, racun_status, racun_domaca_valuta, racun_vrijednost_BAM, racun_vrijednost_RSD, racun_vrijednost_EUR, racun_file, racun_file_de, predracun_id, racun_datum_kreiranja)	
						// VALUES	
						// (:racun_broj,:racun_kandidat_id,:racun_status,:racun_domaca_valuta,:racun_vrijednost_BAM,:racun_vrijednost_RSD,:racun_vrijednost_EUR,:racun_file,:racun_file_de,:predracun_id, :racun_datum_kreiranja)	
						// ");	
			// $insert_racun->execute(array(	
						// ':racun_broj' => $novi_racun_naziv,	
						// ':racun_kandidat_id' => $kandidat_id,	
						// ':racun_status' => $racun_status,	
						// ':racun_domaca_valuta' => $pr_domaca_valuta,	
						// ':racun_vrijednost_BAM' => $pr_vrijednost_BAM,	
						// ':racun_vrijednost_RSD' => $pr_vrijednost_RSD,	
						// ':racun_vrijednost_EUR' => $pr_vrijednost_EUR,	
						// ':racun_file' => $racun_file,
						// ':racun_file_de' => $racun_file,
						// ':predracun_id' => $predracun_id,
						// ':racun_datum_kreiranja' => $arg_datum
						// ));
			// $racun_id = $db->lastInsertId();
			// echo("Error description: " . $insert_racun -> error);
			
			// //UPDATE IZDAN RACUN ZA PREDRACUNE
			// $update_predracuni = $db->prepare("
										// UPDATE idk_predracuni
										// SET pr_izdan_racun = 1
										// WHERE pr_id = :pr_id
										// ");
			
			// $update_predracuni->execute(array(
				// ':pr_id' => $predracun_id
			// ));
			// // var_dump($insert_racun);
			// // var_dump($novi_racun_naziv);
			// // var_dump($kandidat_id);
			// // var_dump($racun_status);
			// // var_dump($pr_domaca_valuta);
			// // var_dump($pr_vrijednost_BAM);
			// // var_dump($pr_vrijednost_RSD);
			// // var_dump($pr_vrijednost_EUR);
			// // var_dump($racun_file);
			// // var_dump($predracun_id);
			// // var_dump($arg_datum);
			// // exit();
		// }
	// }else{
		// $racun_file = $file_datum."DIPLRCH".$brojac_racuna.".pdf";
		// //INSERT RACUN
		// $insert_racun = $db->prepare("	
					// INSERT INTO idk_racuni	
					// (racun_broj,  racun_kandidat_id, racun_status, racun_domaca_valuta, racun_vrijednost_BAM, racun_vrijednost_RSD, racun_vrijednost_EUR, racun_file, predracun_id, racun_datum_kreiranja)	
					// VALUES	
					// (:racun_broj,:racun_kandidat_id,:racun_status,:racun_domaca_valuta,:racun_vrijednost_BAM,:racun_vrijednost_RSD,:racun_vrijednost_EUR,:racun_file,:predracun_id, :racun_datum_kreiranja)	
					// ");	
		// $insert_racun->execute(array(	
					// ':racun_broj' => $novi_racun_naziv,	
					// ':racun_kandidat_id' => $kandidat_id,	
					// ':racun_status' => $racun_status,	
					// ':racun_domaca_valuta' => $pr_domaca_valuta,	
					// ':racun_vrijednost_BAM' => $pr_vrijednost_BAM,	
					// ':racun_vrijednost_RSD' => $pr_vrijednost_RSD,	
					// ':racun_vrijednost_EUR' => $pr_vrijednost_EUR,	
					// ':racun_file' => $racun_file,
					// ':predracun_id' => $predracun_id,
					// ':racun_datum_kreiranja' => $arg_datum
					// ));
					// // echo("Error description: " . $insert_racun -> error);

		// $racun_id = $db->lastInsertId();
		// // var_dump($insert_racun);
		// // var_dump($novi_racun_naziv);
		// // var_dump($kandidat_id);
		// // var_dump($racun_status);
		// // var_dump($pr_domaca_valuta);
		// // var_dump($pr_vrijednost_BAM);
		// // var_dump($pr_vrijednost_RSD);
		// // var_dump($pr_vrijednost_EUR);
		// // var_dump($racun_file);
		// // var_dump($predracun_id);
		// // var_dump($arg_datum);
		// // exit();
		// //UPDATE IZDAN RACUN ZA PREDRACUNE
		// $update_predracuni = $db->prepare("
									// UPDATE idk_predracuni
									// SET pr_izdan_racun = 1
									// WHERE pr_id = :pr_id
									// ");
		
		// $update_predracuni->execute(array(
			// ':pr_id' => $predracun_id
		// ));
		
	// }
	
	// switch($pr_rata){
		// case 1:
			// $rate_text1 = " - Prvi ";
			// $rate_text1_de = " - Erste ";
		// break;
		// case 2:
			// $rate_text1 = " - Drugi ";
			// $rate_text1_de = " - Zweite ";
		// break;
		// case 3:
			// $rate_text1 = " - Treći ";
			// $rate_text1_de = " - Dritte ";
		// break;
		// case 4:
			// $rate_text1 = " - Četvrti ";
			// $rate_text1_de = " - Vierte ";
		// break;
		// case 5:
			// $rate_text1 = " - Peti ";
			// $rate_text1_de = " - Funfte ";
		// break;
		// default:
			// $rate_text1 = "";
	// }
	
	// if($drzava == "BiH"){
		// $slovo_drz = "B";
		// $domaca_valuta = "BAM";
		// $ukupno_za_pl = number_format($pr_vrijednost_BAM, 2, '.', '');
		// $ukupno_za_pl_txt1 = "Ukupno za plaćanje BAM";
		// $ukupno_za_pl_txt2 = $storno_racun_predznak.$ukupno_za_pl." KM";
		
		// $text_kupac = "Kupac";
		// $text_ime_prezime = "Ime i prezime";
		// $text_plativo_do = "Plativo do";
		// $text_avansni_racun = "Račun";
		// $text_obrada_podataka_ND = "Obrada podataka za nostrifikaciju diplome ".$rate_text1."dio usluge";
		// $text_iznos = "Iznos";
		// $text_sifra = "Šifra";
		// $text_naziv = "Naziv";
		// $text_kolicina = "Količina";
		// $text_cijena = "Cijena";
		// $text_ukupno = "Ukupno";
		// $text_pdv = "PDV";
		// $text_popust = "Popust";
		// $text_adresa = "Adresa";
		// $text_ukupno_placanje = "Ukupno za plaćanje";
		
	// }elseif($drzava == "Srbija"){
		// $slovo_drz = "S";
		// $domaca_valuta = "RSD";
		// $ukupno_za_pl = number_format($pr_vrijednost_RSD, 2, '.', '');
		// $ukupno_za_pl_txt1 = "Ukupno za plaćanje RSD";
		// $ukupno_za_pl_txt2 = $storno_racun_predznak.$ukupno_za_pl." RSD";
		
		// $text_kupac = "Kupac";
		// $text_ime_prezime = "Ime i prezime";
		// $text_plativo_do = "Plativo do";
		// $text_avansni_racun = "Račun";
		// $text_obrada_podataka_ND = "Obrada podataka za nostrifikaciju diplome ".$rate_text1." dio usluge";
		// $text_iznos = "Iznos";
		// $text_sifra = "Šifra";
		// $text_naziv = "Naziv";
		// $text_kolicina = "Količina";
		// $text_cijena = "Cena";
		// $text_ukupno = "Ukupno";
		// $text_pdv = "PDV";
		// $text_popust = "Popust";
		// $text_adresa = "Adresa";
		// $text_ukupno_placanje = "Ukupno za plaćanje";
		
	// }elseif($drzava == "Njemacka"){
		// //nista ne ispisuje u zadnjem redu kad je Njemacka u pitanju
		// $slovo_drz = "D";
		// $domaca_valuta = "EUR";
		// $ukupno_za_pl = number_format($pr_vrijednost_EUR, 2, '.', '');
		// $ukupno_za_pl_txt1 = "";
		// $ukupno_za_pl_txt2 = "";
		
		// $text_kupac = "Kunde";
		// $text_ime_prezime = "Name und Vorname";
		// $text_plativo_do = "Zahlbar bis";
		// $text_avansni_racun = "Rechnung";
		// $text_obrada_podataka_ND = "Dienstleistung Datenverarbeitung ";
		// $text_iznos = "Betrag";
		// $text_sifra = "Pos";
		// $text_naziv = "Bezeichnung";
		// $text_kolicina = "Menge";
		// $text_ukupno = "Total";
		// $text_cijena = "Preis";
		// $text_pdv = "MwSt";
		// $text_popust = "Rabat";
		// $text_adresa = "Adresse";
		// $text_ukupno_placanje = "Zahlungsbetrag ";
		
	// }
	// $pdv_stopa = 0;
	// $ukupno_EUR = $pr_vrijednost_EUR/(1+$pdv_stopa);
	// $ukupno_EUR_f = number_format($ukupno_EUR, 2, '.', '');
	// $ukupno_za_pl_EUR_f = number_format($pr_vrijednost_EUR, 2, '.', '');
	
	// $pdv_txt = $pdv_stopa * 100;
	// $pdv_txt_f = number_format($pdv_txt, 2, '.', '');
	
	// $pdv_iznos = $ukupno_za_pl_EUR_f - $ukupno_EUR_f;
	// $pdv_iznos_f = number_format($pdv_iznos, 2, '.', '');
	
	// if(in_array(($vrsta_ugovora), array(21,22,23,24,25,61,62,63,64,65))){
		// $popust_part = true;
		// $popust_procent = 20;
	// }elseif(in_array(($vrsta_ugovora), array(2,4,6,8,10,12,71,72,73,74,75))){
		// $popust_part = true;
		// $popust_procent = 30;
	// }elseif(in_array(($vrsta_ugovora), array(9,82,83,84,85))){ //sve dok je 10 popust na placanje u cijelosti(1 rata-> 9)
		// $popust_part = true;
		// $popust_procent = 10;
	// }elseif(in_array(($vrsta_ugovora), array(51,52,53,54,55))){
		// $popust_part = true;
		// $popust_procent = 50;
	// }elseif(in_array(($vrsta_ugovora), array(41,42,43,44,45))){
		// $popust_part = true;
		// $popust_procent = 70;
	// }elseif($vrsta_ugovora == 99){ 
		// $popust_part = true;
		// $popust_procent = 100;
	// }else{
		// $popust_part = false;
		// $popust_procent = 0;
	// }
	
	// if($popust_procent != 100){
		// $iznos = $ukupno_EUR * (100 / (100 - $popust_procent));
	// }else{
		// $iznos = 485.73;
	// }
	
	// $iznos_f = number_format($iznos, 2, '.', '');
	// $popust_procent_txt = ''.$popust_procent.'';
	// $popust = $iznos_f - $ukupno_EUR_f;
	// $popust_f = number_format($popust, 2, '.', '');
	
	// if($popust_part){
		
		// $vodoravne_cijene = '
		// <tr>
			// <td class = "bt bb bl" width="8%"><b>'.$text_sifra.'</b></td>
			// <td class = "bt bb" width="42%"><b>'.$text_naziv.'</b></td>
			// <td class = "bt bb textC" width="10%"><b>'.$text_kolicina.'</b></td>
			// <td class = "bt bb textR" width="12%"><b>'.$text_cijena.'</b></td>
			// <td class = "bt bb textR" width="14%"><b>'.$text_popust.'</b></td>
			// <td class = "bt bb br textR" width="14%"><b>'.$text_ukupno.'</b></td>
		// </tr>
		// <tr>
			// <td width="8%">1</td>
			// <td width="42%">'.$text_obrada_podataka_ND.'</td>
			// <td class = "textC" width="10%">1</td>
			// <td class = "textR" width="12%">
				// &euro;&nbsp;&nbsp; '.$storno_racun_predznak.$iznos_f.'				
			// </td>
			// <td class = "textR" width="14%">
				// &nbsp;&nbsp;'.$text_minus_popust.' '.$popust_procent_txt.' %
			// </td>
			// <td class = "textR" width="14%">
				// &euro;&nbsp;&nbsp;'.$storno_racun_predznak.$ukupno_EUR_f.'
			// </td>
		// </tr>
		// ';
		
		// $popust_txt = '
		// <tr>
			// <td width="33%" class="bt"></td>
			// <td width="51%" class="bt">'.$text_iznos.'</td>
			// <td width="16%" class="textR bt">&euro;&nbsp;&nbsp;'.$storno_racun_predznak.$iznos_f.'</td>
		// </tr>
		// <tr>
			// <td width="33%"></td>
			// <td width="51%">'.$text_popust.' '.$popust_procent_txt.'%</td>
			// <td width="16%" class="textR">&euro;&nbsp;&nbsp;'.$text_minus_popust.$popust_f.'</td>
		// </tr>
		// <tr>
			// <td width="33%"></td>
			// <td width="51%">'.$text_ukupno.'</td>
			// <td width="16%" class="textR">&euro;&nbsp;&nbsp;'.$storno_racun_predznak.$ukupno_EUR_f.'</td>
		// </tr>
		// ';
	// }else{
		// $popust_txt = '
		// <tr>
			// <td width="33%" class="bt"></td>
			// <td width="51%" class="bt">'.$text_ukupno.'</td>
			// <td width="16%" class="textR bt">&euro;&nbsp;&nbsp;'.$storno_racun_predznak.$ukupno_EUR_f.'</td>
		// </tr>
		// ';
		// $vodoravne_cijene = '
		// <tr>
			// <td class = "bt bb bl" width="8%"><b>'.$text_sifra.'</b></td>
			// <td class = "bt bb" width="50%"><b>'.$text_naziv.'</b></td>
			// <td class = "bt bb textC" width="10%"><b>'.$text_kolicina.'</b></td>
			// <td class = "bt bb textC" width="16%"><b>'.$text_cijena.'</b></td>
			// <td class = "bt bb br textC" width="16%"><b>'.$text_ukupno.'</b></td>
		// </tr>
		// <tr>
			// <td width="8%">1</td>
			// <td width="50%">'.$text_obrada_podataka_ND.'</td>
			// <td class = "textC" width="10%">1</td>
			// <td class = "textC" width="16%">
				// &euro;&nbsp;&nbsp; '.$storno_racun_predznak.$iznos_f.'				
			// </td>
			// <td class = "textR" width="16%">
				// &euro;&nbsp;&nbsp;'.$storno_racun_predznak.$ukupno_EUR_f.'
			// </td>
		// </tr>
		// ';
	// }
	// if($vrsta_ugovora == 26 OR $vrsta_ugovora == 27 /*OR $vrsta_ugovora == 28 OR $vrsta_ugovora == 29*/){
		// $cijena_takse_f = number_format($cijena_takse, 2, '.', '');
		// $taksa_txt = '
			// <tr>
				// <td width="8%">2</td>
				// <td width="50%">Taksaaaa ceka se tekst</td>
				// <td class = "textC" width="10%">1</td>
				// <td class = "textC" width="16%">
					// &euro;&nbsp;&nbsp; '.$cijena_takse_f.'				
				// </td>
				// <td class = "textR" width="16%">
					// &euro;&nbsp;&nbsp;'.$cijena_takse_f.'
				// </td>
			// </tr>
		// ';
	// }else{
		// $taksa_txt = '';
	// }
	
	// $novi_racun = new bgTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); //konstruktor (portrait, mm, a4, unicode,)
	// $novi_racun->SetTitle('Račun');
	// $novi_racun->SetMargins(15, 65, 20, true);
	// $novi_racun->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	
	// $novi_racun->setFontSubsetting(true);
	
	// $img_file = 'tcpdf-main/images/memorandum.jpg';
	// $novi_racun->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
	// $novi_racun->SetFont('dejavuserif','',9);
	
	
	// $novi_racun->AddPage();
	// $html = <<<EOD
	// <style>
	// .bt{
		// border-top: 1px solid black;
	// }
	// .bb{
		// border-bottom: 1px solid black;
	// }
	// .bl{
		// border-left: 1px solid black;
	// }
	// .br{
		// border-right: 1px solid black;
	// }
	// .pt{
		// padding-top: 4px;
	// }
	// .pb{
		// padding-bottom: 4px;
	// }
	// .pr{
		// padding-right: 6px;
	// }
	// .pl{
		// padding-left: 6px;
	// } 
	// .mt{
		// margin-top: 6px;
	// }
	// .mb{
		// margin-bottom: 6px;
	// }
	// .mr{
		// margin-right: 6px;
	// }
	// .ml{
		// margin-left: 6px;
	// } 
	
	// .f11{
		// font-size: 12px;
	// }
	
	// .textC{
		// text-align: center;
	// }
	// .textR{
		// text-align: right;
	// }
	// .footer {
	  // width: 100%;
	  // text-align: center;
	// }
	// </style>
	// <br>
	// <br>
	// <table class="pb pt">
		// <tr>
			// <td class="bb bt pb pt" colspan="2">$text_kupac:</td>
		// </tr>
		// <tr>
			// <td width="40%">$text_ime_prezime: </td>
			// <td>$kandidat_ime $kandidat_prezime</td>
		// </tr>
		// <tr class="pb pt">
			// <td width="40%">$text_adresa: </td>
			// <td>$kandidat_adresa</td>
		// </tr>
	// </table>
	// <hr>
	// <br>
	// <div>
		// <p>Zug, $datum_kreiranja</p>
		// <p>$text_plativo_do: $platiti_do</p>
	// </div>
	// <div class="mb">
		// <h3 class="bb">$text_storno_racun$text_avansni_racun: $novi_racun_naziv</h3>
	// </div>
	
	// <div></div>
	
	// <div>
		// <table class="pb pt">
		
			// $vodoravne_cijene
			// $taksa_txt
			// <tr>
				// <td colspan="5" class="bb"></td>
			// </tr>
			
			// $popust_txt
			// <tr>
				// <td width="33%"></td>
				// <td width="51%">$text_pdv ( $pdv_txt_f % )</td>
				// <td width="16%" class="textR">&euro;&nbsp;&nbsp;&nbsp;&nbsp;$pdv_iznos_f</td>
			// </tr>
			// <tr>
				// <td width="33%"></td>
				// <td width="51%">$text_ukupno_placanje EUR</td>
				// <td width="16%" class="textR">&euro;&nbsp;&nbsp;$storno_racun_predznak$ukupno_za_pl_EUR_f</td>
			// </tr>
			// <tr>
				// <td width="33%"></td>
				// <td width="51%">$ukupno_za_pl_txt1</td>
				// <td width="16%" class="textR">$ukupno_za_pl_txt2</td>
			// </tr>
		// </table>
	// </div>

// EOD;
	
	// // Print text using writeHTMLCell()
	// $filename="/files/racuni_dipl/".$racun_file."";
	// $novi_racun->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
	// $novi_racun->Output(__DIR__ .$filename, 'F');
	
// }


// $pr_kandidat_id = 51801;
// $arg_datum = date('Y-m-d H:i:s', strtotime('2022-04-06 08:46:55'));
// $pr_id = 4500;
// // $FIX_AFTER_MIGRATION_generisiRacun
// $query_get_pr_domaca_valuta = $db -> prepare('
	// SELECT pr_domaca_valuta
	// FROM idk_predracuni
	// WHERE pr_id = :pr_id
// ');
// $query_get_pr_domaca_valuta -> execute(array(':pr_id' => $pr_id));
// $row_get_pr_domaca_valuta = $query_get_pr_domaca_valuta -> fetch();
// $pr_domaca_valuta = $row_get_pr_domaca_valuta['pr_domaca_valuta'];
	
// $drzava = "";
// switch($pr_domaca_valuta){
	// case 'BAM':
		// $drzava = "BiH";
	// break;
		
	// case 'RSD':
		// $drzava = "Srbija";
	// break;
	
	// default:
		// $drzava = "Njemacka";
// }

// // $file_datum = date('YmdHis', strtotime($arg_datum));
// // $datum_kreiranja = date('d.m.Y', strtotime($arg_datum));
// // $platiti_do = date('d.m.Y', strtotime($datum_kreiranja. ' + 10 days')); 


// FIX_AFTER_MIGRATION_generisiRacun($pr_id, $drzava, $arg_datum);
// if($drzava != "Njemacka"){
	// FIX_AFTER_MIGRATION_generisiRacun($pr_id, "Njemacka", $arg_datum);
// }

// $query_get_last_racun_file = $db -> prepare('
	// SELECT racun_file ,racun_file_de
	// FROM idk_racuni
	// WHERE racun_kandidat_id = :pr_kandidat_id
	// AND racun_status = 1
	// ORDER BY racun_id DESC
	// LIMIT 1
// ');

// $query_get_last_racun_file -> execute(array(':pr_kandidat_id' => $pr_kandidat_id));
// $row_get_last_racun_file = $query_get_last_racun_file -> fetch();
// $racun_file_za_send = $row_get_last_racun_file['racun_file_de'];
// // exit();
// sendMailRacun($racun_file_za_send);
// ?>