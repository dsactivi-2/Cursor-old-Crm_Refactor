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
<?php 
	include("pdf_generator.php");
	include("includes/functions.php");
	
	// sendMailPredracunBIH("20211013000000DIPLB2238.pdf", "UPB-42-10-21.pdf", 39646);
	// exit();
	
	$month = date('m');
	$day = date('d');
	$year = date('Y');
	
	
	//$valute = array("BAM");
	$valute = array("BAM", "RSD");
	$trenutniDatum = date("Y-m-d", strtotime("+1 days")); //trenutno vrijeme za potrebe kôda ispod
	//strtotime($datumKreiranja."+".$j." months")
	
	//----------------------vrijednosti kolone "vrsta_ugovora_nd_kandidata" u tabeli "idk_nd_kandidata" START
	$r1 = array(9,21,10);
	$r2 = array(1,22,2);
	$r3 = array(5,23,6);
	$r4 = array(7,24,8);
	$r5 = array(3,25,4);
	$r6 = array(26);
	$r12 = array(27);
	$rMikro = array(11,12);
	//----------------------vrijednosti kolone "vrsta_ugovora_nd_kandidata" u tabeli "idk_nd_kandidata" END
	
	//----------------------prethodne vrijednosti implode za query START
	$rata1 = implode(',',$r1);
	$rata2 = implode(',',$r2);
	$rata3 = implode(',',$r3);
	$rata4 = implode(',',$r4);
	$rata5 = implode(',',$r5);
	$rata6 = implode(',',$r6);
	$rata12 = implode(',',$r12);
	$rataMikrofin = implode(',',$rMikro);
	//----------------------prethodne vrijednosti implode za query END
	
?>
	<body>
<?php 
	foreach($valute AS $valuta){
?>
		<table id="customers">
			<tr>
				<th colspan = "9"><?php echo $valuta; ?></th>
			</tr>
			<tr>
				<th>RBP</th>
				<th>ID kandidata</th>
				<th>Ime Prezime</th>
				<th>Ukupno rata</th>
				<th>Rata</th>
				<th>Dat kr Z.U.R.</th>
				<th>Predviđeni datum</th>
				<th>Stvarni datum</th>
				<th>Pred status</th>
				<th>Iznos rate</th>
				<th>Valuta</th>
			</tr>
			<?php 
				$brojac1 = 0;
				$sumIznos = 0;
				$query1 = $db->prepare("
					SELECT
						kan.id_broj_nd_kandidata, kan.ime_nd_kandidata, kan.prezime_nd_kandidata, kan.status_nd_kandidata, kan.pstatus_nd_kandidata, kan.vrsta_ugovora_nd_kandidata, pred.pr_datum_kreiranja, pred.pr_domaca_valuta
					FROM 
						idk_nd_kandidata kan
					INNER JOIN 
						idk_predracuni pred
					ON 
						kan.id_broj_nd_kandidata = pred.pr_kandidat_id
					WHERE 
						kan.status_nd_kandidata != 7
						AND 
						pred.pr_vrsta_predracuna = 1
						AND 
						pred.pr_domaca_valuta LIKE '".$valuta."'
						AND 
						pred.pr_rata = 1
						AND 
						pred.pr_status = 2
						AND 
						pred.pr_uplaceno = 1
						AND 
						kan.vrsta_ugovora_nd_kandidata NOT IN (9,21,10,11,12)
					ORDER BY 
						pred.pr_datum_kreiranja ASC
					
				");
				$query1->execute();
				while($row1 = $query1->fetch()){
					$broj_rata = 0;
					$idKandidat = intval($row1["id_broj_nd_kandidata"]);
					$imeKandidat = $row1["ime_nd_kandidata"];
					$prezimeKandidat = $row1["prezime_nd_kandidata"];
					$statusKandidat = intval($row1["status_nd_kandidata"]);
					$podstatusKandidat = intval($row1["pstatus_nd_kandidata"]);
					$ugovorKandidat = intval($row1["vrsta_ugovora_nd_kandidata"]);
					$datumKreiranja = date("Y-m-d", strtotime($row1["pr_datum_kreiranja"]));
					$datumKreiranja_zur = date("Y-m-d", strtotime($row1["pr_datum_kreiranja"])); //datum kreiranja predracuna zadnje uplacene rate
					$valutaPredracun = $row1["pr_domaca_valuta"];
					
					if($valutaPredracun == "BAM"){
						$drzavaPredracun = "BiH";
					}else{
						$drzavaPredracun = "Srbija";
					}
					
					if(in_array($ugovorKandidat, $r1)){
						$broj_rata = 1;
					}else if(in_array($ugovorKandidat, $r2)){
						$broj_rata = 2;
					}else if(in_array($ugovorKandidat, $r3)){ 
						$broj_rata = 3;
					}else if(in_array($ugovorKandidat, $r4)){
						$broj_rata = 4;
					}else if(in_array($ugovorKandidat, $r5)){
						$broj_rata = 5;
					}else if(in_array($ugovorKandidat, $r6)){
						$broj_rata = 6;
					}else if(in_array($ugovorKandidat, $r12)){
						$broj_rata = 12;
					}else if(in_array($ugovorKandidat, $rMikro)){
						$broj_rata = 1;
					}else{
						$broj_rata = 0;
					}
					
					if($broj_rata != 0){
						if($broj_rata != 2){
							//Dio koda za 3-4-5 rata
							$broj_nekreiranih = 0;
							for($i = 2; $i <= $broj_rata; $i++){
								$queryProvjera = $db->prepare("
									SELECT 
										pr_datum_kreiranja, pr_status
									FROM 
										idk_predracuni
									WHERE 
										pr_kandidat_id = :pr_k 
										AND
										pr_vrsta_predracuna = 1
										AND 
										pr_rata = :prRata
										AND
										pr_status != 0
								");
								$queryProvjera->execute(array(
									':pr_k' => $idKandidat,
									':prRata' => $i
								));
								
								$brojProvjera = $queryProvjera->rowCount();
								if($brojProvjera != 0){
									//Znaci da je kreiran predracun
									$rowProvjera = $queryProvjera->fetch();
									$prDatumKreiranja = date("Y-m-d",strtotime($rowProvjera["pr_datum_kreiranja"]));
									$prStatus = intval($rowProvjera["pr_status"]);
									$j = 0;
									$iznosRate = 0;
									if($prStatus != 2){
										$j = $i - 1;
										$predvidjeniDatum = date("Y-m-d", strtotime($datumKreiranja."+".$j." months"));
										
										if($predvidjeniDatum <= $trenutniDatum){
											$brojac1++;
											$iznosRate = getIznosRate($ugovorKandidat, $drzavaPredracun, "rata".$i);
											$sumIznos = $sumIznos + $iznosRate;
											echo "
												<tr>
													<td>".$brojac1."</td>
													<td>".$idKandidat."</td>
													<td>".$imeKandidat." ".$prezimeKandidat."</td>
													<td>".$broj_rata."</td>
													<td>".$i."</td>
													<td>".$datumKreiranja_zur."</td>
													<td>".$predvidjeniDatum."</td>
													<td>".$prDatumKreiranja."</td>
													<td>".$prStatus."</td>
													<td>".number_format($iznosRate, 2, '.', ',')."</td>
													<td>".$valutaPredracun."</td>
												</tr>
											";
										}
										$datumKreiranja_zur = date("Y-m-d", strtotime($prDatumKreiranja)); //datum kreiranja predracuna zadnje kreirane rate
									}else{
										//Ovdje kad je uplacen
										$datumKreiranja_zur = date("Y-m-d", strtotime($rowProvjera["pr_datum_kreiranja"])); //datum kreiranja predracuna zadnje uplacene rate
									}
								}else{
									//Znaci da nije kreiran predracun
									$broj_nekreiranih++;
									$j = $i - 1;
									$predvidjeniDatum = date("Y-m-d", strtotime($datumKreiranja."+".$j." months"));
									$datum_za_kreiranje = date("Y-m-d", strtotime($datumKreiranja_zur."+".$broj_nekreiranih." months"));
									//$danas = date('d.m.Y', strtotime($datumKreiranja_zur));
									
									if($datum_za_kreiranje < $predvidjeniDatum)
										$datum_za_kreiranje = $predvidjeniDatum;
									
									if($datum_za_kreiranje <= $trenutniDatum){
										$brojac1++;
										$iznosRate = getIznosRate($ugovorKandidat, $drzavaPredracun, "rata".$i);
										$sumIznos = $sumIznos + $iznosRate;
										echo "
											<tr>
												<td>".$brojac1."</td>
												<td>".$idKandidat."</td>
												<td>".$imeKandidat." ".$prezimeKandidat."</td>
												<td>".$broj_rata."</td>
												<td>".$i."</td>
												<td>".$datumKreiranja_zur."</td>
												<td>".$predvidjeniDatum."</td>
												<td>".$datum_za_kreiranje."</td>
												<td>NEMAST</td>
												<td>".number_format($iznosRate, 2, '.', ',')."</td>
												<td>".$valutaPredracun."</td>
											</tr>
										";
										
										/*if($drzavaPredracun == "Srbija"){
											$slovo_drz = "S";
											$domaca_valuta = "RSD";
											$rata_rsd = $iznosRate;
											
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
											
										}else{
											$slovo_drz = "B";
											$domaca_valuta = "BAM";
											$rata_bam = $iznosRate;
											
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
											
											$rata_eur = $rata_bam / $EUR;
											$rata_rsd = 100*$rata_bam / $RSD;
											
											$rata_bam_f = number_format((float)$rata_bam, 2, '.', '');
											$rata_eur_f = number_format((float)$rata_eur, 2, '.', '');
											$rata_rsd_f = number_format((float)$rata_rsd, 2, '.', '');
											
										}
										$pr_status = getInkasoStatusDIPLKR($datum_za_kreiranje);
										$year_skr = date("y", strtotime($datumKreiranja_zur."+".$broj_nekreiranih." months"));
										$brojac_predracuna = createBrojPredracuna($drzavaPredracun);
										$novi_predracun = "DIPL".$slovo_drz."-".$brojac_predracuna."-".$year_skr;
										$file_datum = date('YmdHis', strtotime($datumKreiranja_zur."+".$broj_nekreiranih." months")); 
										$file_name = $file_datum."DIPL".$slovo_drz.$brojac_predracuna.".pdf";
										
										// var_dump($novi_predracun);
										// var_dump($idKandidat);
										// var_dump($datum_za_kreiranje);
										// var_dump($i);
										// var_dump($pr_status);
										// var_dump($domaca_valuta);
										// var_dump($rata_bam_f);
										// var_dump($rata_rsd_f);
										// var_dump($rata_eur_f);
										// var_dump($file_name);
										// exit();
										$insert_predracun = $db->prepare("	
													INSERT INTO idk_predracuni	
													(pr_broj_predracuna,  pr_kandidat_id, pr_datum_kreiranja, pr_zaposlenik, pr_vrsta_predracuna, pr_rata, pr_status, pr_domaca_valuta, pr_vrijednost_BAM, pr_vrijednost_RSD, pr_vrijednost_EUR, pr_file)	
													VALUES	
													(:pr_broj_predracuna,:pr_kandidat_id,:pr_datum_kreiranja,:pr_zaposlenik,:pr_vrsta_predracuna,:pr_rata,:pr_status,:pr_domaca_valuta,:pr_vrijednost_BAM,	:pr_vrijednost_RSD,:pr_vrijednost_EUR,:pr_file)	
													");	
										$insert_predracun->execute(array(	
													':pr_broj_predracuna' => $novi_predracun,	
													':pr_kandidat_id' => $idKandidat,	
													':pr_datum_kreiranja' => $datum_za_kreiranje,	
													':pr_zaposlenik' => 67,	
													':pr_vrsta_predracuna' => 1,	
													':pr_rata' => $i,	
													':pr_status' => $pr_status,	
													':pr_domaca_valuta' => $domaca_valuta,	
													':pr_vrijednost_BAM' => $rata_bam_f,	
													':pr_vrijednost_RSD' => $rata_rsd_f,	
													':pr_vrijednost_EUR' => $rata_eur_f,	
													':pr_file' => $file_name
													));
										//Get last ID
										$predracun_id = $db->lastInsertId();
										
										//kreirati predracun !!!
										//createRucnoPredracunBIH($predracun_id, $datum_za_kreiranje);
										createRucnoPredracunSRB($predracun_id, $datum_za_kreiranje);*/
									}
								}
							}
							
						}else{
							//Dio koda za 2 rate
							if($statusKandidat == 6){
								//Ovdje uzimam datum kada je prešao u status zavrsen 
								$queryZavrsen = $db->prepare("
									SELECT 
										MAX(vrijeme_promjene_statusa_nd_kandidata) AS datumZavrsen
									FROM 
										idk_nd_kandidata_status_log
									WHERE 
										idd_broj_nd_kandidata = ".$idKandidat."
										AND 
										status_nd_kandidata = 6
										AND 
										broj_dana_statusa_nd_kandidata is null
								");
								$queryZavrsen->execute();
								if($queryZavrsen->rowCount() != 0){
									$rowZavrsen = $queryZavrsen->fetch();
									$predvidjeniDatum = date("Y-m-d", strtotime($rowZavrsen["datumZavrsen"]));
								}else{
									$predvidjeniDatum = $trenutniDatum;
								}
								
								$queryProvjera = $db->prepare("
									SELECT 
										pr_datum_kreiranja, pr_status
									FROM 
										idk_predracuni
									WHERE 
										pr_kandidat_id = :pr_k 
										AND
										pr_vrsta_predracuna = 1
										AND 
										pr_rata = :prRata
										AND
										pr_status != 0
								");
								$queryProvjera->execute(array(
									':pr_k' => $idKandidat,
									':prRata' => 2
								));
								
								$brojProvjera = $queryProvjera->rowCount();
								
								if($brojProvjera == 0){
									$brojac1++;
									$iznosRate = getIznosRate($ugovorKandidat, $drzavaPredracun, "rata2");
									$sumIznos = $sumIznos + $iznosRate;
									echo "
										<tr>
											<td>".$brojac1."</td>
											<td>".$idKandidat."</td>
											<td>".$imeKandidat." ".$prezimeKandidat."</td>
											<td>".$broj_rata."</td>
											<td>2</td>
											<td>".$datumKreiranja_zur."</td>
											<td>".$predvidjeniDatum."</td>
											<td>NEMA2</td> 
											<td>NEMAST2</td> 
											<td>".number_format($iznosRate, 2, '.', ',')."</td>
											<td>".$valutaPredracun."</td>
										</tr>
									";
								}
							}
						}
					}
				}
			?>
			<tr>
				<th colspan = "7" style = "text-align: right;">Suma</th>
				<th><?php echo number_format($sumIznos, 2, '.', ',');?></th>
				<th><?php echo $valuta; ?></th>
			</tr>
		</table>
		<hr>
<?php 
	}
?>
	</body>
</html>