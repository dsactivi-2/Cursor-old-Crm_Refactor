<?php

require_once('tcpdf-main/tcpdf.php');
include('includes/connect.php');
class bgTCPDF extends TCPDF {
	
    public function Header() {
        $auto_page_break = $this->AutoPageBreak;
        $this->SetAutoPageBreak(false, 0);
		$img_file = 'tcpdf-main/images/memorandum.jpg';
		$this->Image($img_file, 0, 3, 210, 293, '', '', '', false, 300, '', false, false, 0);
        $this->SetAutoPageBreak($auto_page_break, 40);
        $this->setPageMark();
    }
}
function generisiPredracun58480($predracun_id, $drzava){
	Global $db; 
	$datum_kreiranja = '10.06.2022';
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
	$predracunZaposlenik = $predracun_row[" pr_zaposlenik"];
	
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
		$zaposleniciObavijestNizExp = array(67,63,208); //U slucaju da treba jos nekom poslati mail - ovdje samo dodati id-eve - NPR: array(67,75)
		//array_push($predracunZaposlenik, $zaposleniciObavijestNizExp);
		$zaposleniciObavijestNizImp = implode(",", $zaposleniciObavijestNizExp);
		if(count($zaposleniciObavijestNizExp) != 0){
			foreach($zaposleniciObavijestNizExp AS $valueZaposlenici){
				$user_query = $db->prepare("
					SELECT employee_firstname, employee_lastname, employee_email
					FROM idk_employees
					WHERE employee_id = :employee_id
				");

				$user_query->execute(array(
					':employee_id' => $valueZaposlenici
				));

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
	$img_file = 'tcpdf-main/images/memorandum.jpg';
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
		<p>Zug, $datum_kreiranja</p>
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
		$file_datum = '20220610141337'; 
		$predracun_putanja = $file_datum."DIPL".$slovo_drz.$brojac_predracuna.".pdf";

		$update_predracun = $db->prepare("
			UPDATE idk_predracuni
			SET pr_file = :pr_file, pr_datum_kreiranja = '2022-06-10', pr_stornirano = 0
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
generisiPredracun58480('5700', 'BiH');
generisiPredracun58480('5700', 'Njemacka');

?>