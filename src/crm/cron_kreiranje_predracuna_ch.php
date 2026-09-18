<?php 
	//PHP KOD START
		include("includes/functions.php");
		include("html_pdf_generator.php");
		include("pdf_generator.php");
		
		$time_start = microtime(true); //Vrijeme početak
		// $text_rata = "xte";
		// $text_rata_sms = "";
		// $poruka_text_viber	= "Pozdrav! 🤗\n\nVrijeme za uplatu Vaše *".$text_rata." rate*! Predračun i elektronsku uplatnicu možete pregledati i preuzeti klikom na dugme ispod.\n\nHvala Vam što ste dio naše priče! Za sve informacije, tu smo za Vas.\n\nJSI Team";
		// $poruka_text_sms	= "Pozdrav! 🤗\n\nVrijeme za uplatu Vase *".$text_rata_sms." rate*! Predracun i elektronsku uplatnicu mozete pregledati i preuzeti klikom na dugme ispod.\n\nHvala Vam sto ste dio nase price! Za sve informacije, tu smo za Vas.\n\nJSI Team";
		// $poruka_button 		=  "012345678912345678901";
		// echo sendViberUgovorLink(259, $poruka_text_viber, $poruka_text_sms, "cxvxcv", $poruka_button);
		// exit();
		
		//Funkcije nove START
			function vrsteUgovoraVrijednostR(){
				$vrijednost = array(
					"1rata" => array(9,21,10,41,51,61,71,81),
					"2rate" => array(1,22,2,42,52,62,72,82),
					"3rate" => array(5,23,6,43,53,63,73,83),
					"4rate" => array(7,24,8,44,54,64,74,84),
					"5rata" => array(3,25,4,45,55,65,75,85),
					"6rata" => array(26),
					"12rata" => array(27),
					"Mikro" => array(11,12)
				);
				
				return $vrijednost;
			}
			function brojRataZaVrstuUgovora($vrUgovor){
				$brojRata = 0;
				$vrijednosti = vrsteUgovoraVrijednostR();
				if(in_array($vrUgovor, $vrijednosti["1rata"])){
					$brojRata = 1;
				}else if(in_array($vrUgovor, $vrijednosti["2rate"])){
					$brojRata = 2;
				}else if(in_array($vrUgovor, $vrijednosti["3rate"])){
					$brojRata = 3;
				}else if(in_array($vrUgovor, $vrijednosti["4rate"])){
					$brojRata = 4;
				}else if(in_array($vrUgovor, $vrijednosti["5rata"])){
					$brojRata = 5;
				}else if(in_array($vrUgovor, $vrijednosti["6rata"])){
					$brojRata = 6;
				}else if(in_array($vrUgovor, $vrijednosti["12rata"])){
					$brojRata = 12;
				}else if(in_array($vrUgovor, $vrijednosti["Mikro"])){
					$brojRata = 1;
				}else{
					$brojRata = 0;
				}
				
				return $brojRata;
			}
			$month = date('m');
			$day = date('d');
			$year = date('Y');
			$year_skr = date("y");
			
			$ch = curl_init();
			// Disable SSL verification
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			// Will return the response, if false it print the response
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			// Set the url
			$rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
			curl_setopt($ch, CURLOPT_URL,$rls);
			// Execute
			$result=curl_exec($ch);
			curl_close($ch);

			$data = json_decode($result, TRUE);
			$EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
			$EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
			$RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
			$RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
			
			$EUR = str_replace(',', '.', $EUR1);
			$RSD = str_replace(',', '.', $RSD1);
			
		//Funkcije nove END 
	//PHP KOD END 
?>
<html>
	<head>
		<style>
		#customers {
			font-family: Arial, Helvetica, sans-serif;
			border-collapse: collapse;
			width: 100%;
		}

		#customers td, #customers th {
			border: 1px solid #ddd;
			padding: 8px;
		}

		#customers tr:nth-child(even){background-color: #f2f2f2;}

		#customers tr:hover {background-color: #ddd;}

		#customers th {
			padding-top: 12px;
			padding-bottom: 12px;
			text-align: left;
			background-color: #04AA6D;
			color: white;
		}
		</style>
	</head>
	<body>
		<table id="customers">
			<tr>
				<th>#</th>
				<th>ID Kandidat</th>
				<th>Ime Prezime</th>
				<th>Vrsta ugovora</th>
				<th>Broj rata</th>
				<th>Datum kreiranja predračuna</th>
				<th>Rata</th>
				<th>Predračun status</th>
				<th>Domaća valuta</th>
				<th>Predviđeni datum</th>
			</tr>
<?php 
	//PHP KOD START
		//Vrste ugovora koje nije potrebno provjeravati START
			$bezJednaRataImp = "";
			$bezJednaRataExp = vrsteUgovoraVrijednostR();
			$bezJednaRataImp = implode(",", $bezJednaRataExp["1rata"]).",".implode(",", $bezJednaRataExp["Mikro"]);
			
			$trenutniDatum = date("Y-m-d");
		//Vrste ugovora koje nije potrebno provjeravati END 
		$queryPrviPredracun = $db->prepare("
			SELECT 
				kan.id_broj_nd_kandidata,
				kan.ime_nd_kandidata, 
				kan.prezime_nd_kandidata, 
				kan.zaduzen_zaposlenik_nd_kandidata, 
				kan.status_nd_kandidata, 
				kan.pstatus_nd_kandidata,
				kan.vrsta_ugovora_nd_kandidata, 
				ug.ug_zaposlenik_id, 
				ug.ug_token, 
				ug.ug_jezik, 
				ug.ug_status, 
				ug.ug_datum_slanja, 
				ug.ug_datum_prihvatanja, 
				pred.pr_naplata_preko, 
				pred.pr_datum_kreiranja, 
				pred.pr_rata,
				pred.pr_status,
				pred.pr_domaca_valuta
			FROM 
				idk_nd_kandidata kan
			INNER JOIN 
				idk_nd_ugovori ug
			ON 
				kan.id_broj_nd_kandidata = ug.ug_kandidat_id
			INNER JOIN
				idk_predracuni pred
			ON 
				ug.ug_kandidat_id = pred.pr_kandidat_id
			WHERE 
				kan.status_nd_kandidata != 7
				AND 
				kan.vrsta_ugovora_nd_kandidata NOT IN (".$bezJednaRataImp.")
				AND 
				ug.ug_status = 2
				AND
				pred.pr_rata = 1
				AND 
				pr_vrsta_predracuna = 1
				AND 
				pred.pr_status != 0
				AND 
				pred.pr_naplata_preko IN (1,2)
		");
		
		$queryPrviPredracun->execute();
		$brojPrviPredracun = $queryPrviPredracun->rowCount();
		if($brojPrviPredracun != 0){
			$count = 0;
			while($rowPrviPredracun = $queryPrviPredracun->fetch()){
				$count++;
				$idKandidat = intval($rowPrviPredracun["id_broj_nd_kandidata"]);
				$imeKandidat = $rowPrviPredracun["ime_nd_kandidata"];
				$prezimeKandidat = $rowPrviPredracun["prezime_nd_kandidata"];
				$statusKandidat = intval($rowPrviPredracun["status_nd_kandidata"]);
				$pstatusKandidat = intval($rowPrviPredracun["pstatus_nd_kandidata"]);
				$vrstaUgovora = intval($rowPrviPredracun["vrsta_ugovora_nd_kandidata"]);
				$vrstaUgovoraIspis = getVrstaUgovoraDiplR($vrstaUgovora);
				$izdaoUgovorZaposlenik = intval($rowPrviPredracun["ug_zaposlenik_id"]);
				$tokenUgovora = $rowPrviPredracun["ug_token"];
				$jezikUgovora = $rowPrviPredracun["ug_jezik"];
				$statusUgovora = intval($rowPrviPredracun["ug_status"]);
				$datumPrihvatanjaUgovora = $rowPrviPredracun["ug_datum_prihvatanja"];
				$datumKreiranjaPredracuna = $rowPrviPredracun["pr_datum_kreiranja"];
				$rataPredracuna = intval($rowPrviPredracun["pr_rata"]);
				$statusPredracuna = intval($rowPrviPredracun["pr_status"]);
				$valutaPredracuna = $rowPrviPredracun["pr_domaca_valuta"];
				$pr_naplata_preko = $rowPrviPredracun["pr_naplata_preko"];
				
				//broj rata za vrstu ugovora START
				$brojRata = 0;
				$brojRata = brojRataZaVrstuUgovora($vrstaUgovora);
				
				if($brojRata != 0){
					//Ispis informacija o prvoj rati
					echo "
						<tr style = 'background-color: aquamarine;'>
							<td rowspan='".$brojRata."'>".$count."</td>
							<td rowspan='".$brojRata."'>".$idKandidat."</td>
							<td rowspan='".$brojRata."'>".$imeKandidat." ".$prezimeKandidat."</td>
							<td rowspan='".$brojRata."'>".$vrstaUgovoraIspis."</td>
							<td rowspan='".$brojRata."'>".$brojRata."</td>
							<td>".$datumKreiranjaPredracuna."</td>
							<td>".$rataPredracuna."</td>
							<td>".$statusPredracuna."</td>
							<td>".$valutaPredracuna."</td>
							<td>".$datumKreiranjaPredracuna."</td>
						</tr>
					";
					if($brojRata != 1){
						
						for($i = 2; $i <= $brojRata; $i++){
							$months = 0;
							
							$queryProvjera = $db->prepare("
								SELECT 
									pr_datum_kreiranja, pr_status
								FROM 
									idk_predracuni
								WHERE 
									pr_kandidat_id = :pr_kandidat_id 
									AND
									pr_vrsta_predracuna = 1
									AND 
									pr_rata = :pr_rata
									AND
									pr_status != 0 
									AND 
									pr_naplata_preko IN (1,2) 
							");
							$queryProvjera->execute(array(
								':pr_kandidat_id' => $idKandidat,
								':pr_rata' => $i
							));
							$brojProvjera = $queryProvjera->rowCount();
							
							if($brojProvjera != 0){
								
								$rowProvjera = $queryProvjera->fetch();
								$prDatumKreiranja = date("Y-m-d",strtotime($rowProvjera["pr_datum_kreiranja"]));
								$prStatus = intval($rowProvjera["pr_status"]);
								echo "
									<tr style = 'background-color: aquamarine;'>
										<td>".$prDatumKreiranja."</td>
										<td>".$i."</td>
										<td>".$prStatus."</td>
										<td>".$valutaPredracuna."</td>
										<td>".$prDatumKreiranja."</td>
									</tr>
								";
							}else{
								$months = $i - 1;
								$predvidjeniDatum = date("Y-m-d", strtotime($datumKreiranjaPredracuna."+".$months." months"));
								$dospioDaNe = "";
								$dospioDaNeStyle = "";
								$izdajiPredracun = 1;
								if(($trenutniDatum >= $predvidjeniDatum)){
									$dospioDaNe = "Dospjelo je";
									$dospioDaNeStyle = "tomato";
									
									if($izdajiPredracun == 1){
										// //******************************************************************************
										// //GENERISANJE PREDRAČUNA START *************************************************
										// //******************************************************************************
										if($pr_naplata_preko == 1){
											$brojac_predracuna = createBrojPredracuna("ch");
											$novi_predracun = "DIPLCH-".$brojac_predracuna."-".$year_skr;
											
											//iznos dobijam preko fje u markama  i onda gledam drzavu klijenta i onda prebacivam valute
											$iznos_rate = getIznosRate($vrstaUgovora, "ch", "rata".$i."");
											
											$rata_eur = $iznos_rate / $EUR;
											$rata_rsd = 100*$iznos_rate / $RSD;
											
											$rata_bam_f = number_format((float)$iznos_rate, 2, '.', '');
											$rata_eur_f = number_format((float)$rata_eur, 2, '.', '');
											$rata_rsd_f = number_format((float)$rata_rsd, 2, '.', '');
											
											$slovo_drz = "";
											$domaca_valuta = "";
											$jezik_ugovora = "";
											$drzava = "";
											
											if($valutaPredracuna == "BAM"){
												$drzava = "BiH"; 
												$slovo_drz = "B";
												$domaca_valuta = "BAM";
												$jezik_ugovora = "bs";
											}elseif($valutaPredracuna == "RSD"){
												$drzava = "Srbija";
												$slovo_drz = "S";
												$domaca_valuta = "RSD";
												$jezik_ugovora = "sr";
											}elseif($valutaPredracuna == "EUR"){
												$drzava = "Njemacka";
												$slovo_drz = "D";
												$domaca_valuta = "EUR";
												$jezik_ugovora = "de";
											}else{
												$drzava = "";
												$slovo_drz = "";
												$domaca_valuta = "";
												$jezik_ugovora = "";
											}
											
											if($slovo_drz != "" AND $domaca_valuta != "" AND $jezik_ugovora != "" AND $drzava != ""){
											
												$file_datum = date('YmdHis'); 
												$predracun_putanja = $file_datum."DIPL".$slovo_drz.$brojac_predracuna.".pdf";
												$insert_predracun = $db->prepare("	
													INSERT INTO idk_predracuni	
														(
															pr_broj_predracuna, 
															pr_naplata_preko, 
															pr_kandidat_id, 
															pr_datum_kreiranja, 
															pr_zaposlenik, 
															pr_vrsta_predracuna, 
															pr_rata, 
															pr_domaca_valuta, 
															pr_vrijednost_BAM, 
															pr_vrijednost_RSD, 
															pr_vrijednost_EUR
														)	
													VALUES	
														(
															:pr_broj_predracuna,
															:pr_naplata_preko,
															:pr_kandidat_id,
															:pr_datum_kreiranja,
															:pr_zaposlenik,
															:pr_vrsta_predracuna,
															:pr_rata,
															:pr_domaca_valuta,
															:pr_vrijednost_BAM,
															:pr_vrijednost_RSD,
															:pr_vrijednost_EUR
														)	
												");	
												
												$insert_predracun->execute(array(	
													':pr_broj_predracuna' => $novi_predracun,
													':pr_naplata_preko' => 1,
													':pr_kandidat_id' => $idKandidat,
													':pr_datum_kreiranja' => date("Y-m-d 00:00:00"),
													':pr_zaposlenik' => 67,
													':pr_vrsta_predracuna' => 1,
													':pr_rata' => $i,
													':pr_domaca_valuta' => $domaca_valuta,
													':pr_vrijednost_BAM' => $rata_bam_f,
													':pr_vrijednost_RSD' => $rata_rsd_f,
													':pr_vrijednost_EUR' => $rata_eur_f
												));
												
												$predracun_id = $db->lastInsertId();
												
												//createPredracun($predracun_id, $drzava);
												//Adis Komentarisao Generisanje na Prihvati START --------------------------------------
													if($drzava != "Njemacka"){
														generisiPredracun($predracun_id, $drzava);
														generisiPredracun($predracun_id, "Njemacka");
													}else{
														generisiPredracun($predracun_id, "Njemacka");
													}
												//Adis Komentarisao Generisanje na Prihvati END -------------------------------------- 
												// var_dump("vratilo se na zivote");
												// exit();
												//tekst za ratu START
												$text_rata = "";
												$text_rata_sms = $text_rata;
												switch($i){
													case 2:
														if($drzava == "Njemacka"){
															$text_rata = "zweite";
															$text_rata_sms = $text_rata;
														}else{
															$text_rata = "druge";
															$text_rata_sms = $text_rata;
														}
													break;
													case 3:
														if($drzava == "Njemacka"){
															$text_rata = "dritte";
															$text_rata_sms = $text_rata;
														}else{
															$text_rata = "treće";
															$text_rata_sms = "trece";
														}
													break;
													case 4:
														if($drzava == "Njemacka"){
															$text_rata = "vierte";
															$text_rata_sms = $text_rata;
														}else{
															$text_rata = "četvrte";
															$text_rata_sms = "cetvrte";
														}
													break;
													case 5:
														if($drzava == "Njemacka"){
															$text_rata = "fünfte";
															$text_rata_sms = $text_rata;
														}else{
															$text_rata = "pete";
															$text_rata_sms = $text_rata;
														}
													break;
													case 6:
														if($drzava == "Njemacka"){
															$text_rata = "sechste";
															$text_rata_sms = $text_rata;
														}else{
															$text_rata = "šeste";
															$text_rata_sms = "seste";
														}
													break;
													case 7:
														if($drzava == "Njemacka"){
															$text_rata = "siebte";
															$text_rata_sms = $text_rata;
														}else{
															$text_rata = "sedme";
															$text_rata_sms = $text_rata;
														}
													break;
													case 8:
														if($drzava == "Njemacka"){
															$text_rata = "achte";
															$text_rata_sms = $text_rata;
														}else{
															$text_rata = "osme";
															$text_rata_sms = $text_rata;
														}
													break;
													case 9:
														if($drzava == "Njemacka"){
															$text_rata = "neunte";
															$text_rata_sms = $text_rata;
														}else{
															$text_rata = "devete";
															$text_rata_sms = $text_rata;
														}
													break;
													case 10:
														if($drzava == "Njemacka"){
															$text_rata = "zehnte";
															$text_rata_sms = $text_rata;
														}else{
															$text_rata = "desete";
															$text_rata_sms = $text_rata;
														}
													break;
													case 11:
														if($drzava == "Njemacka"){
															$text_rata = "elfte";
															$text_rata_sms = $text_rata;
														}else{
															$text_rata = "jedanaeste";
															$text_rata_sms = $text_rata;
														}
													break;
													case 12:
														if($drzava == "Njemacka"){
															$text_rata = "zwölfte";
															$text_rata_sms = $text_rata;
														}else{
															$text_rata = "dvanaeste";
															$text_rata_sms = $text_rata;
														}
													break;
												}
												//tekst za ratu END
												
												//ugovor link query START
												
												$ugovor_link = getSiteUrlr()."ugovor/".$tokenUgovora."/".$jezikUgovora;
												//ugovor link query END 
												
												$poruka_text_viber 	= "";
												$poruka_text_sms 	= "";
												$poruka_button		= "";
												//na mail ide $predracun_putanja, $putanja_uplatnica, $ugovor_link, odvojeni mailovi za srb i bih
												if($drzava == "Srbija"){

													createUplatnicaSRB($predracun_id);
													$poruka_text_mail	= "Pozdrav! 🤗<br><br>Vreme je za uplatu Vaše *".$text_rata." rate*! Predračun i elektronsku uplatnicu možete pregledati i preuzeti klikom na link ispod.<br><br>Hvala Vam što ste deo naše priče! Za sve informacije, tu smo za Vas. <br><a href='".$ugovor_link."'> LINK</a>";
													$poruka_text_viber	= "Pozdrav! 🤗\n\nVreme je za uplatu Vaše *".$text_rata." rate*! Predračun i elektronsku uplatnicu možete pregledati i preuzeti klikom na dugme ispod.\n\nHvala Vam što ste deo naše priče! Za sve informacije, tu smo za Vas.\n\nJSI Team";
													$poruka_text_sms	= "Pozdrav! 🤗\n\nVreme je za uplatu Vase *".$text_rata_sms." rate*! Predracun i elektronsku uplatnicu mozete pregledati i preuzeti klikom na link ispod.\n\nHvala Vam sto ste deo nase price! Za sve informacije, tu smo za Vas.\n\nJSI Team\n\n".$ugovor_link;
													$poruka_button 		= "Dokumenti";
													
												}elseif($drzava == "BiH"){

													createUplatnicaBIH($predracun_id);
													$poruka_text_mail	= "Pozdrav! 🤗<br><br>Vrijeme za uplatu Vaše *".$text_rata." rate*! Predračun i elektronsku uplatnicu možete pregledati i preuzeti klikom na link ispod.<br><br>Hvala Vam što ste dio naše priče! Za sve informacije, tu smo za Vas. <br><a href='".$ugovor_link."'> LINK</a>";
													$poruka_text_viber	= "Pozdrav! 🤗\n\nVrijeme za uplatu Vaše *".$text_rata." rate*! Predračun i elektronsku uplatnicu možete pregledati i preuzeti klikom na dugme ispod.\n\nHvala Vam što ste dio naše priče! Za sve informacije, tu smo za Vas.\n\nJSI Team";
													$poruka_text_sms	= "Pozdrav! 🤗\n\nVrijeme za uplatu Vase *".$text_rata_sms." rate*! Predracun i elektronsku uplatnicu mozete pregledati i preuzeti klikom na link ispod.\n\nHvala Vam sto ste dio nase price! Za sve informacije, tu smo za Vas.\n\nJSI Team\n\n".$ugovor_link;
													$poruka_button 		=  "Dokumenti";
													
												}else{
													
													generisiInoUplatnicu($predracun_id);
													$poruka_text_mail	= "Schöne Grüße!\n\nEs ist Zeit Ihre *".$text_rata." Rate zu zahlen!* Sie können die Pro-forma-Rechnung und den elektronischen Einzahlungsschein durchlesen, sowie herunterladen, indem Sie auf die Schaltfläche unten klicken.\n\nDanke, dass Sie ein Teil unserer Geschichte sind! \n\nFür weitere Informationen stehen wir jederzeit gerne zur Verfügung. <br><a href='".$ugovor_link."'> LINK</a>";
													$poruka_text_viber	= "Schöne Grüße!\n\nEs ist Zeit Ihre *".$text_rata." Rate zu zahlen!* Sie können die Pro-forma-Rechnung und den elektronischen Einzahlungsschein durchlesen, sowie herunterladen, indem Sie auf die Schaltfläche unten klicken.\n\nDanke, dass Sie ein Teil unserer Geschichte sind! \n\nFür weitere Informationen stehen wir jederzeit gerne zur Verfügung.\n\n";
													$poruka_text_sms	= "Schöne Grüße!\n\nEs ist Zeit Ihre *".$text_rata." Rate zu zahlen!* Sie können die Pro-forma-Rechnung und den elektronischen Einzahlungsschein durchlesen, sowie herunterladen, indem Sie auf die Schaltfläche unten klicken.\n\nDanke, dass Sie ein Teil unserer Geschichte sind! \n\nFür weitere Informationen stehen wir jederzeit gerne zur Verfügung.\n\n".$ugovor_link;
													$poruka_button 		= "Unterlagen";
													//ako kandidat nije ni iz BiH onda ide info o ino uplatama, maybe, ceka se odg od Sabine
												}
												$subject_ugovor = "";
												sendViberUgovorLink($idKandidat, $poruka_text_viber, $poruka_text_sms, $ugovor_link, $poruka_button);
												sendMailUgovorPredracun($idKandidat, $poruka_text_mail, $ugovor_link, $subject_ugovor);
												
											}
										}elseif($pr_naplata_preko == 2){
											//GENERISANJE PREDRAČUNA PREKO SRPSKE FIRME ALI SA ELEKTRONISKIM POTPISOM
											$year_skr = date("y");
											$brojac_predracuna = createBrojPredracuna("Srbija");
											$novi_predracun = "DIPLS-".$brojac_predracuna."-".$year_skr;

											$iznosRate = getIznosRate($vrstaUgovora, "Srbija", "rata".$i);

											$slovo_drz = "S";
											$domaca_valuta = "RSD";
											$jezik_ugovora = "sr";
											$rata_rsd = $iznosRate;
											$drzava = "Srbija";
											
											$ch = curl_init();
											// Disable SSL verification
											curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
											// Will return the response, if false it print the response
											curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
											// Set the url
											$rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
											// var_dump($rls);
											// exit();
											curl_setopt($ch, CURLOPT_URL,$rls);
											curl_setopt($ch, CURLOPT_FAILONERROR, true); // Required for HTTP error codes to be reported via our call to curl_error($ch)
											// Execute
											$result=curl_exec($ch);
											
											curl_close($ch);

											$data = json_decode($result, TRUE);
											$EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
											$EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
											$RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
											$RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
											
											$EUR = str_replace(',', '.', $EUR1);
											$RSD = str_replace(',', '.', $RSD1);
											$rata_bam = ($rata_rsd / 100) * $RSD ;
											$rata_eur = $rata_bam / $EUR;
											
											$rata_bam_f = number_format((float)$rata_bam, 2, '.', '');
											$rata_eur_f = number_format((float)$rata_eur, 2, '.', '');
											$rata_rsd_f = number_format((float)$rata_rsd, 2, '.', '');

											$file_datum = date('YmdHis'); 
											$predracun_putanja = $file_datum."DIPL".$slovo_drz.$brojac_predracuna.".pdf";
											$novi_predracun = "DIPL".$slovo_drz."-".$brojac_predracuna."-".$year_skr;

											$insert_predracun = $db->prepare("	
												INSERT INTO idk_predracuni	
													(
														pr_broj_predracuna, 
														pr_naplata_preko, 
														pr_kandidat_id, 
														pr_datum_kreiranja,
														pr_zaposlenik, 
														pr_vrsta_predracuna, 
														pr_rata, 
														pr_domaca_valuta, 
														pr_vrijednost_BAM, 
														pr_vrijednost_RSD, 
														pr_vrijednost_EUR,
														pr_file
													)	
												VALUES	
													(
														:pr_broj_predracuna,
														:pr_naplata_preko,
														:pr_kandidat_id,
														:pr_datum_kreiranja,
														:pr_zaposlenik,
														:pr_vrsta_predracuna,
														:pr_rata,
														:pr_domaca_valuta,
														:pr_vrijednost_BAM,
														:pr_vrijednost_RSD,
														:pr_vrijednost_EUR,
														:pr_file
													)	
											");	
											
											$insert_predracun->execute(array(	
												':pr_broj_predracuna' => $novi_predracun,
												':pr_naplata_preko' => 2,
												':pr_kandidat_id' => $idKandidat,
												':pr_datum_kreiranja' => $prDatumKreiranja,
												':pr_zaposlenik' => 67,
												':pr_vrsta_predracuna' => 1,
												':pr_rata' => $i,
												':pr_domaca_valuta' => $domaca_valuta,
												':pr_vrijednost_BAM' => $rata_bam_f,
												':pr_vrijednost_RSD' => $rata_rsd_f,
												':pr_vrijednost_EUR' => $rata_eur_f,
												':pr_file' => $predracun_putanja
											));
											
											$predracun_id = $db->lastInsertId();

											createRucnoPredracunSRB($predracun_id, $prDatumKreiranja);

											//tekst za ratu START
											$text_rata = "";
											$text_rata_sms = $text_rata;
											switch($i){
												case 2:
													if($drzava == "Njemacka"){
														$text_rata = "zweite";
														$text_rata_sms = $text_rata;
													}else{
														$text_rata = "druge";
														$text_rata_sms = $text_rata;
													}
												break;
												case 3:
													if($drzava == "Njemacka"){
														$text_rata = "dritte";
														$text_rata_sms = $text_rata;
													}else{
														$text_rata = "treće";
														$text_rata_sms = "trece";
													}
												break;
												case 4:
													if($drzava == "Njemacka"){
														$text_rata = "vierte";
														$text_rata_sms = $text_rata;
													}else{
														$text_rata = "četvrte";
														$text_rata_sms = "cetvrte";
													}
												break;
												case 5:
													if($drzava == "Njemacka"){
														$text_rata = "fünfte";
														$text_rata_sms = $text_rata;
													}else{
														$text_rata = "pete";
														$text_rata_sms = $text_rata;
													}
												break;
												case 6:
													if($drzava == "Njemacka"){
														$text_rata = "sechste";
														$text_rata_sms = $text_rata;
													}else{
														$text_rata = "šeste";
														$text_rata_sms = "seste";
													}
												break;
												case 7:
													if($drzava == "Njemacka"){
														$text_rata = "siebte";
														$text_rata_sms = $text_rata;
													}else{
														$text_rata = "sedme";
														$text_rata_sms = $text_rata;
													}
												break;
												case 8:
													if($drzava == "Njemacka"){
														$text_rata = "achte";
														$text_rata_sms = $text_rata;
													}else{
														$text_rata = "osme";
														$text_rata_sms = $text_rata;
													}
												break;
												case 9:
													if($drzava == "Njemacka"){
														$text_rata = "neunte";
														$text_rata_sms = $text_rata;
													}else{
														$text_rata = "devete";
														$text_rata_sms = $text_rata;
													}
												break;
												case 10:
													if($drzava == "Njemacka"){
														$text_rata = "zehnte";
														$text_rata_sms = $text_rata;
													}else{
														$text_rata = "desete";
														$text_rata_sms = $text_rata;
													}
												break;
												case 11:
													if($drzava == "Njemacka"){
														$text_rata = "elfte";
														$text_rata_sms = $text_rata;
													}else{
														$text_rata = "jedanaeste";
														$text_rata_sms = $text_rata;
													}
												break;
												case 12:
													if($drzava == "Njemacka"){
														$text_rata = "zwölfte";
														$text_rata_sms = $text_rata;
													}else{
														$text_rata = "dvanaeste";
														$text_rata_sms = $text_rata;
													}
												break;
											}
											//tekst za ratu END

											//ugovor link query START
												
											$ugovor_link = getSiteUrlr()."ugovorNew/".$tokenUgovora."/".$jezikUgovora;
											//ugovor link query END 
											
											$poruka_text_viber 	= "";
											$poruka_text_sms 	= "";
											$poruka_button		= "";
											//na mail ide $predracun_putanja, $putanja_uplatnica, $ugovor_link, odvojeni mailovi za srb i bih
											if($drzava == "Srbija"){

												createUplatnicaSRB($predracun_id);
												$poruka_text_mail	= "Pozdrav! 🤗<br><br>Vreme je za uplatu Vaše *".$text_rata." rate*! Predračun i elektronsku uplatnicu možete pregledati i preuzeti klikom na link ispod.<br><br>Hvala Vam što ste deo naše priče! Za sve informacije, tu smo za Vas. <br><a href='".$ugovor_link."'> LINK</a>";
												$poruka_text_viber	= "Pozdrav! 🤗\n\nVreme je za uplatu Vaše *".$text_rata." rate*! Predračun i elektronsku uplatnicu možete pregledati i preuzeti klikom na dugme ispod.\n\nHvala Vam što ste deo naše priče! Za sve informacije, tu smo za Vas.\n\nJSI Team";
												$poruka_text_sms	= "Pozdrav! 🤗\n\nVreme je za uplatu Vase *".$text_rata_sms." rate*! Predracun i elektronsku uplatnicu mozete pregledati i preuzeti klikom na link ispod.\n\nHvala Vam sto ste deo nase price! Za sve informacije, tu smo za Vas.\n\nJSI Team\n\n".$ugovor_link;
												$poruka_button 		= "Dokumenti";
												
											}elseif($drzava == "BiH"){

												createUplatnicaBIH($predracun_id);
												$poruka_text_mail	= "Pozdrav! 🤗<br><br>Vrijeme za uplatu Vaše *".$text_rata." rate*! Predračun i elektronsku uplatnicu možete pregledati i preuzeti klikom na link ispod.<br><br>Hvala Vam što ste dio naše priče! Za sve informacije, tu smo za Vas. <br><a href='".$ugovor_link."'> LINK</a>";
												$poruka_text_viber	= "Pozdrav! 🤗\n\nVrijeme za uplatu Vaše *".$text_rata." rate*! Predračun i elektronsku uplatnicu možete pregledati i preuzeti klikom na dugme ispod.\n\nHvala Vam što ste dio naše priče! Za sve informacije, tu smo za Vas.\n\nJSI Team";
												$poruka_text_sms	= "Pozdrav! 🤗\n\nVrijeme za uplatu Vase *".$text_rata_sms." rate*! Predracun i elektronsku uplatnicu mozete pregledati i preuzeti klikom na link ispod.\n\nHvala Vam sto ste dio nase price! Za sve informacije, tu smo za Vas.\n\nJSI Team\n\n".$ugovor_link;
												$poruka_button 		=  "Dokumenti";
												
											}else{
												
												generisiInoUplatnicu($predracun_id);
												$poruka_text_mail	= "Schöne Grüße!\n\nEs ist Zeit Ihre *".$text_rata." Rate zu zahlen!* Sie können die Pro-forma-Rechnung und den elektronischen Einzahlungsschein durchlesen, sowie herunterladen, indem Sie auf die Schaltfläche unten klicken.\n\nDanke, dass Sie ein Teil unserer Geschichte sind! \n\nFür weitere Informationen stehen wir jederzeit gerne zur Verfügung. <br><a href='".$ugovor_link."'> LINK</a>";
												$poruka_text_viber	= "Schöne Grüße!\n\nEs ist Zeit Ihre *".$text_rata." Rate zu zahlen!* Sie können die Pro-forma-Rechnung und den elektronischen Einzahlungsschein durchlesen, sowie herunterladen, indem Sie auf die Schaltfläche unten klicken.\n\nDanke, dass Sie ein Teil unserer Geschichte sind! \n\nFür weitere Informationen stehen wir jederzeit gerne zur Verfügung.\n\n";
												$poruka_text_sms	= "Schöne Grüße!\n\nEs ist Zeit Ihre *".$text_rata." Rate zu zahlen!* Sie können die Pro-forma-Rechnung und den elektronischen Einzahlungsschein durchlesen, sowie herunterladen, indem Sie auf die Schaltfläche unten klicken.\n\nDanke, dass Sie ein Teil unserer Geschichte sind! \n\nFür weitere Informationen stehen wir jederzeit gerne zur Verfügung.\n\n".$ugovor_link;
												$poruka_button 		= "Unterlagen";
												//ako kandidat nije ni iz BiH onda ide info o ino uplatama, maybe, ceka se odg od Sabine
											}
											$subject_ugovor = "";
											sendViberUgovorLink($idKandidat, $poruka_text_viber, $poruka_text_sms, $ugovor_link, $poruka_button);
											sendMailUgovorPredracun($idKandidat, $poruka_text_mail, $ugovor_link, $subject_ugovor, $pr_naplata_preko);
										}
										//******************************************************************************
										//GENERISANJE PREDRAČUNA END   *************************************************
										//******************************************************************************
									}
								}else{
									$dospioDaNe = "Nije dospjelo";
									$dospioDaNeStyle = "yellowgreen";
								}
								echo "
									<tr style = 'background-color: ".$dospioDaNeStyle.";'>
										<td>".$predvidjeniDatum."</td>
										<td>".$i."</td>
										<td>".$dospioDaNe."</td>
										<td>".$valutaPredracuna."</td>
										<td>".$predvidjeniDatum."</td>
									</tr>
								";
							}
						}
					}else{
						
					}
				}
				//broj rata za vrstu ugovora END 
			}
		}
	//PHP KOD END 
		$time_end = microtime(true); //Vrijeme kraj
		$execution_time = $time_end - $time_start; //Rezultat izvršavanja između Vrijeme početak i Vrijeme kraj
		$execution_time = number_format((float)$execution_time, 4, '.', ''); //Rezultat izvršavanja između Vrijeme početak i Vrijeme kraj
?>
		</table>
		
		<p style = "margin-top: 20px;">Vrijeme obrade: <?php echo $execution_time; ?> sekundi</p><br>
		
		<?php 
			if($brojPrviPredracun == 0){
		?>
			<span> U bazi nema podataka za kreiranje</span>
		<?php 
			}
		?>
	</body>
</html>