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

$kreirani_kandidati = array();
$kreirani_predracuni = array();

$month = date('m');
$day = date('d');
$year = date('Y');
// $trenutniDatum = date("Y-m-d", strtotime("+7 days")); //trenutno vrijeme za potrebe kôda ispod
$trenutniDatum = date("Y-m-d"); //trenutno vrijeme za potrebe kôda ispod
$valute = array("BAM", "RSD");

//----------------------vrijednosti kolone "vrsta_ugovora_nd_kandidata" u tabeli "idk_nd_kandidata" START
$r1 = array(9,21,10,41,51,61,71,81);
$r2 = array(1,22,2,42,52,62,72,82);
$r3 = array(5,23,6,43,53,63,73,83);
$r4 = array(7,24,8,44,54,64,74,84);
$r5 = array(3,25,4,45,55,65,75,85);
$r6 = array(26);
$r12 = array(27);
$rMikro = array(11,12);
//----------------------vrijednosti kolone "vrsta_ugovora_nd_kandidata" u tabeli "idk_nd_kandidata" END
?>
	<body>
<?php 
	foreach($valute AS $valuta){

		echo '<table id="customers">
			<tr>
				<th colspan = "11">'. $valuta .'</th>
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
			</tr>';
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
				pred.pr_datum_kreiranja > '2023-01-01 00:00:00'
				AND 
				pred.pr_naplata_preko = 0
				AND 
				kan.vrsta_ugovora_nd_kandidata NOT IN (9,21,10,11,12,41,51,61,71,81,99)
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
				if($broj_rata != 1){
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
							if($prStatus != 2){
								$j = $i - 1;
								$predvidjeniDatum = date("Y-m-d", strtotime($datumKreiranja."+".$j." months"));
							}else{
								//Ovdje kad je uplacen
							}
							$datumKreiranja_zur = date("Y-m-d", strtotime($prDatumKreiranja)); //datum kreiranja predracuna zadnje kreirane rate
						}else{
							//Znaci da nije kreiran predracun
							$broj_nekreiranih++;
							$j = $i - 1;
							$predvidjeniDatum = date("Y-m-d", strtotime($datumKreiranja."+".$j." months"));
							$datum_za_kreiranje = date("Y-m-d", strtotime($datumKreiranja_zur."+".$broj_nekreiranih." months"));
							if($datum_za_kreiranje < $predvidjeniDatum)
								$datum_za_kreiranje = $predvidjeniDatum;
							
							if($datum_za_kreiranje <= $trenutniDatum){
								$brojac1++;
								$iznosRate = getIznosRate($ugovorKandidat, $drzavaPredracun, "rata".$i);
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
								
								if($drzavaPredracun == "Srbija"){
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
								$year_skr = date("y");
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
								
								if($drzavaPredracun == "Srbija"){
									createRucnoPredracunSRB($predracun_id, $datum_za_kreiranje);
									$putanja_uplatnica = createUplatnicaSRB($predracun_id); 
									sendMailPredracunSRB($file_name, $putanja_uplatnica, $idKandidat);
								}
								else{
									createRucnoPredracunBIH($predracun_id, $datum_za_kreiranje);
									$putanja_uplatnica = createUplatnicaBIH($predracun_id);
									sendMailPredracunBIH($file_name, $putanja_uplatnica, $idKandidat);
								}
								
								array_push($kreirani_kandidati, $idKandidat);
								array_push($kreirani_predracuni, $predracun_id);
							}
						}
					}
					
				}
			}
		}
		echo "
		</table>
		<hr>";
	}
	
	//ADD TO LOGS START
	$log_kand = implode(',', $kreirani_kandidati);
	$log_pred = implode(',', $kreirani_predracuni);
	$log_date = date('Y-m-d H:i:s');
	$log_desc = "CRON PREDRACUNI -> Kreirani predracuni za kandidate IDs = [".$log_kand."] . Predracuni IDS = [".$log_pred."]. ";
	$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)");

	$log_query->execute(array(
					':log_employeeid' => 139,
					':log_desc' => $log_desc,
					':log_date' => $log_date));
	//ADD TO LOGS END
?>
	</body>
</html>
