<?php

/**** UBACIVANJE OBRAČUNA ******/

include("pdf_generator.php");
include("includes/functions.php");
// include("lang/bs.php");
// sendMailPredracunUgovorBIH("ne","UGB-1-11-20.pdf","ne","1656");
createRacunBIHRucno(2020, 951); //kandidat id i bf pogresnog racuna
function createRacunBIHRucno($kandidat_id, $racun_bf){

	Global $db; 
	Global $logged_employee_id;
	Global $vrsta_dokumenta;
	$year_skr = date('y');
	$vrsta_dokumenta = "predracun";
	
	$danas = date('d.m.Y');
	$danas = date('d.m.Y', strtotime($danas.'- 1 days'));
	$rok_uplate = date('d.m.Y', strtotime($danas. ' + 10 days'));
	
	$get_kand_info = $db->prepare("
					SELECT ime_nd_kandidata, prezime_nd_kandidata, ulica_nd_kandidata, postanski_broj_nd_kandidata, grad_nd_kandidata, vrsta_ugovora_nd_kandidata, jmbg_nd_kandidata
					FROM idk_nd_kandidata
					WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
					");

	$get_kand_info->execute(array(
						':id_broj_nd_kandidata' => $kandidat_id));

	$kand_info_row = $get_kand_info->fetch();
	
	$ime = $kand_info_row["ime_nd_kandidata"];
	$prezime = $kand_info_row["prezime_nd_kandidata"];
	$ulica = $kand_info_row["ulica_nd_kandidata"];
	$pbroj = $kand_info_row["postanski_broj_nd_kandidata"];
	$grad = $kand_info_row["grad_nd_kandidata"];
	$adresa = $ulica.", ".$pbroj." ".$grad;
	$jmbg = $kand_info_row["jmbg_nd_kandidata"];
	$vrsta_ugovora = $kand_info_row["vrsta_ugovora_nd_kandidata"];
   
	switch($vrsta_ugovora){
		case 1: case 2: case 22:
			$broj_rata = 2;
			$rate_text2 = "/".$broj_rata;
		break;
		case 5: case 6: case 23:
			$broj_rata = 3;
			$rate_text2 = "/".$broj_rata;
		break;
		case 7: case 8: case 24:
			$broj_rata = 4;
			$rate_text2 = "/".$broj_rata;
		break;
		case 3: case 4: case 25:
			$broj_rata = 5;
			$rate_text2 = "/".$broj_rata;
		break;
		default:
			$broj_rata = 1;
			$rate_text2 = "";
    }

	//$brojac_racuna = createBrojRacuna("BiH");
	$brojac_racuna = 28; //unosi se rucno jer je vec rezervisan broj za ovaj racun, prepise se sa pogresnog kreiranog racuna
	$year_skr = 22;
	$novi_racun = "DIPLRB-".$brojac_racuna."-".$year_skr;
	//$novi_racun = "DIPLRB-287-21";
	// $file_datum = date('YmdHis'); 
	$file_datum = "20220210163226"; 
	$racun_file = $file_datum."DIPLRB".$brojac_racuna.".pdf";
	//$racun_file = $file_datum."DIPLRB287.pdf";
	
	$pdf = new PDF('P','mm','A4');
	

	$pdf->AliasNbPages();
	$pdf->SetDisplayMode('real', 'single');

	$pdf->AddPage();

	$pdf->Cell(180,7,"Kupac:",'T,B',0,'',0); $pdf->Ln();
	$pdf->Cell(50,7,"Ime i prezime:",0,0,'',0); 
	$pdf->Cell(130,7,$ime." ".$prezime,0,0,'',0); 
	$pdf->Ln();
	$pdf->Cell(50,7,"Adresa:",'',0,'',0); 
	$pdf->Cell(130,7,$adresa,'',0,'',0); 
	$pdf->Ln();
	$pdf->Cell(50,7,"JMBG:",'B',0,'',0); 
	$pdf->Cell(130,7,$jmbg,'B',0,'',0); 
	$pdf->Ln(14);
	$pdf->Cell(180,7,'Bihać, dana '.$danas,0,0,'',0);
	$pdf->Ln();
	$pdf->Cell(180,7,'Isporuka: '.$danas,0,0,'',0); 
	$pdf->Ln();
	$pdf->Cell(180,7,'Rok za uplatu: '.$rok_uplate,0,0,'',0); 
	$pdf->Ln(14);

	$pdf->SetFont('DejaVu','B',18);
	$pdf->Cell(80,10,'Račun: '.$novi_racun,'B'); /*$pdf->Line(11,147,90,147);*/ $pdf->Ln();
	$pdf->SetFont('DejaVu','',9);
	$pdf->Cell(80,7,'BF: '.$racun_bf,''); /*$pdf->Line(11,147,90,147);*/ $pdf->Ln(25);
	
	$pdf->SetFont('DejaVu','B',9);
	$pdf->Cell(12,7,'Šifra','T,L,B','','C',0); 
	$pdf->Cell(98,7,'Naziv','T,B','','L',0); 
	$pdf->Cell(16,7,'Količina','T,B','','C',0); 
	$pdf->Cell(27,7,'Cijena','T,B','','R',0); 
	$pdf->Cell(27,7,'Ukupno','T,B,R',1,'R',0); 
	
	$ukupno_za_placanje = 0;
	$ukupno_za_placanje_EUR = 0;
	$ukupno_za_placanje_RSD = 0;
	// for($i=1; $i<=$broj_rata; $i++){
		$get_predracuni = $db->prepare("
						SELECT pr_vrijednost_BAM, pr_vrijednost_EUR, pr_vrijednost_RSD, pr_rata, pr_id
						FROM idk_predracuni
						WHERE pr_kandidat_id = :pr_kandidat_id AND pr_status = 2 AND pr_stornirano = 0 AND pr_vrsta_predracuna = 1 AND pr_izdan_racun = 1  AND pr_rata IN(2,3,4)
						ORDER BY pr_rata
						");

		$get_predracuni->execute(array(
						':pr_kandidat_id' => $kandidat_id
						));
	$i = 1;
	$iznos = 0;
	while($predracuni_row = $get_predracuni->fetch()){
	
		$pr_rata = $predracuni_row['pr_rata'];
		
		switch($pr_rata){
			case 1:
				$rate_text1 = " - Prvi ";
			break;
			case 2:
				$rate_text1 = " - Drugi ";
			break;
			case 3:
				$rate_text1 = " - Treći ";
			break;
			case 4:
				$rate_text1 = " - Četvrti ";
			break;
			case 5:
				$rate_text1 = " - Peti ";
			break;
			default:
				$rate_text1 = "";
		}
		if($broj_rata == 1){
			$rate_txt_3 = "";
		}else{
			$rate_txt_3 = $rate_text1."dio usluge ".$pr_rata.$rate_text2;
		}
		
		$pr_vrijednost_BAM = $predracuni_row['pr_vrijednost_BAM'];
		$pr_vrijednost_EUR = $predracuni_row['pr_vrijednost_EUR'];
		$pr_vrijednost_RSD = $predracuni_row['pr_vrijednost_RSD'];
		$pr_id = $predracuni_row['pr_id'];
        
		$pr_vrijednost_BAM = number_format($pr_vrijednost_BAM, 2, '.', '');
		$pr_vrijednost_EUR = number_format($pr_vrijednost_EUR, 2, '.', '');
		$pr_vrijednost_RSD = number_format($pr_vrijednost_RSD, 2, '.', '');
		
		$ukupno_za_placanje += $pr_vrijednost_BAM;
		$ukupno_za_placanje_EUR += $pr_vrijednost_EUR;
		$ukupno_za_placanje_RSD += $pr_vrijednost_RSD;
       
        if($pr_vrijednost_BAM == 855){
            $akcija_10posto = true;
            $akcija_20posto = false;
            $cijena = 950/1.17;
            $popust_koef = 0.1;
        }else{
            $akcija_10posto = false;
			if($vrsta_ugovora == 21 OR $vrsta_ugovora == 22 OR $vrsta_ugovora == 23 OR $vrsta_ugovora == 24 OR $vrsta_ugovora == 25){
				$akcija_20posto = true;
				$cijena = ($pr_vrijednost_BAM/1.17)*1.25 ;
				$popust_koef = 0.2;
			}else{
				$akcija_20posto = false;
				$cijena = $pr_vrijednost_BAM/1.17;
				$popust_koef = 0;
			}
        }
        
		$cijena_f = number_format($cijena, 2, '.', '');
		$iznos += $cijena;
		
		$pdf->SetFont('DejaVu','',10);
		$pdf->Cell(12,7,$i,0,'','C',0); 
		$pdf->Cell(98,7,'Obrada podataka za nostrifikaciju diplome'.$rate_txt_3,0,'','L',0); 
		$pdf->Cell(16,7,'1',0,'','C',0); 
		$pdf->Cell(27,7,$cijena_f." KM",0,'','R',0); 
		$pdf->Cell(27,7,$cijena_f." KM",0,'','R',0); 
		$pdf->Ln();
		$i=$i+1 ;
		
		//UPDATE IZDAN RACUN ZA PREDRACUNE
		/*$update_predracuni = $db->prepare("
									UPDATE idk_predracuni
									SET pr_izdan_racun = 1
									WHERE pr_id = :pr_id
									");
		
		$update_predracuni->execute(array(
			':pr_id' => $pr_id
		));*/
	}
	$popust = $iznos * $popust_koef;
	$ukupno = $iznos - $popust;
	$pdv = $ukupno * 0.17;
	$ukupno_za_placanje = $ukupno + $pdv;
	
	$iznos_f = number_format($iznos, 2, '.', '');
	$popust_f = number_format($popust, 2, '.', '');
	$ukupno_f = number_format($ukupno, 2, '.', '');
	$pdv_f = number_format($pdv, 2, '.', '');
	$ukupno_za_placanje_f = number_format($ukupno_za_placanje, 2, '.', '');
	$ukupno_za_placanje_EUR_f = number_format($ukupno_za_placanje_EUR, 2, '.', '');
	
	// $ukupno_bam = $ukupno_za_placanje/1.17;
	// $pdv_bam = $ukupno_bam*0.17;
	// $ukupno_bam_f = number_format($ukupno_bam, 2, '.', '');
	
	$pdf->Ln(22);
	
    if($akcija_10posto){
		
		$pdf->Cell(100,7,'','T','','C',0); 
		$pdf->Cell(53,7,'Iznos','T','','L',0); 
		$pdf->Cell(27,7,$iznos_f." KM",'T','','R',0); $pdf->Ln();
        
        $pdf->Cell(100,7,'','','','C',0); 
        $pdf->Cell(45,7,'Popust','','','L',0); 
        $pdf->Cell(8,7,'10% - ','','','L',0); 
        $pdf->Cell(27,7,$popust_f." KM",'','','R',0); $pdf->Ln();
		
		$pdf->Cell(100,7,'','','','C',0); 
		$pdf->Cell(53,7,'Ukupno','','','L',0); 
		$pdf->Cell(27,7,$ukupno_f." KM",'','','R',0); $pdf->Ln();
        
        // $pdf->Cell(100,7,'','','','C',0); 
        // $pdf->Cell(53,7,'Ukupno','','','L',0); 
        // $pdf->Cell(27,7,$ukupno_bam_f." KM",'','','R',0); $pdf->Ln();
    }else{
		if($akcija_20posto){
			$pdf->Cell(100,7,'','T','','C',0); 
			$pdf->Cell(53,7,'Iznos','T','','L',0); 
			$pdf->Cell(27,7,$iznos_f." KM",'T','','R',0); $pdf->Ln();
			
			$pdf->Cell(100,7,'','','','C',0); 
			$pdf->Cell(45,7,'Popust','','','L',0); 
			$pdf->Cell(8,7,'20% - ','','','L',0); 
			$pdf->Cell(27,7,"162.40 KM",'','','R',0); $pdf->Ln();
			//$pdf->Cell(27,7,$popust_f." KM",'','','R',0); $pdf->Ln();
			
			$pdf->Cell(100,7,'','','','C',0); 
			$pdf->Cell(53,7,'Ukupno','','','L',0); 
			$pdf->Cell(27,7,$ukupno_f." KM",'','','R',0); $pdf->Ln();
			
		}else{
			$pdf->Cell(100,7,'','T','','C',0); 
			$pdf->Cell(53,7,'Ukupno','T','','L',0); 
			$pdf->Cell(27,7,$ukupno_f." KM",'T','','R',0); $pdf->Ln();
			
		}
		
        // $pdf->Cell(100,7,'','T','','C',0); 
        // $pdf->Cell(53,7,'Ukupno','T','','L',0); 
        // $pdf->Cell(27,7,$iznos_f." KM",'T','','R',0); $pdf->Ln();
    }
	
	$pdf->Cell(100,7,'',0,'','C',0); 
	$pdf->Cell(53,7,'PDV',0,'','L',0); 
	$pdf->Cell(27,7,$pdv_f." KM",0,'','R',0); $pdf->Ln();
	
	$pdf->Cell(100,7,'',0,'','C',0); 
	$pdf->Cell(53,7,'Ukupno za plaćanje KM',0,'','L',0); 
	$pdf->Cell(27,7,$ukupno_za_placanje_f." KM",0,'','R',0); $pdf->Ln();
	
	$pdf->Cell(100,7,'',0,'','C',0); 
	$pdf->Cell(53,7,'EUR',0,'','L',0); 
	$pdf->Cell(27,7,'€ '.$ukupno_za_placanje_EUR_f,0,'','R',0); $pdf->Ln();
	
	$pdf->Ln(22);	
	
	$filename="files/racuni_dipl/".$racun_file."";
	// var_dump($filename);
	// exit();
	
	$pdf->Output();
	//$pdf->Output($filename,'F');
	
	//INSER RACUN
	/*$insert_racun = $db->prepare("	
				INSERT INTO idk_racuni	
				(racun_broj,  racun_kandidat_id, racun_status, racun_domaca_valuta, racun_vrijednost_BAM, racun_vrijednost_RSD, racun_vrijednost_EUR, racun_file, racun_bf)	
				VALUES	
				(:racun_broj,:racun_kandidat_id,:racun_status,:racun_domaca_valuta,:racun_vrijednost_BAM,:racun_vrijednost_RSD,:racun_vrijednost_EUR,:racun_file,:racun_bf)	
				");	
	$insert_racun->execute(array(	
				':racun_broj' => $novi_racun,	
				':racun_kandidat_id' => $kandidat_id,	
				':racun_status' => 1,	
				':racun_domaca_valuta' => 'BAM',	
				':racun_vrijednost_BAM' => $ukupno_za_placanje,	
				':racun_vrijednost_RSD' => $ukupno_za_placanje_RSD,	
				':racun_vrijednost_EUR' => $ukupno_za_placanje_EUR,	
				':racun_file' => $racun_file,
				':racun_bf' => $racun_bf
				));
	
	return $racun_file;*/
}

// function uplatiObracuneTestsdfsdf($predracun_id){
	// Global $db;
	
	// $query_date = $db->prepare("SELECT pr_datum_uplate, pr_datum_kreiranja, pr_kandidat_id, pr_domaca_valuta, pr_zaposlenik, pr_rata FROM idk_predracuni WHERE pr_id = $predracun_id");
	// $query_date->execute();

	// $row_date = $query_date->fetch();
	// $pr_datum_kreiranja = $row_date['pr_datum_kreiranja'];
	// $pr_datum_kreiranja_calc = strtotime($row_date['pr_datum_kreiranja']);
	
	// $datum_uplate = $row_date['pr_datum_uplate'];
	// $pr_kandidat_id = $row_date['pr_kandidat_id'];
	// $valuta = $row_date['pr_domaca_valuta'];
	// $employee_id = $row_date['pr_zaposlenik'];
	// $agent_prodao = $row_date['pr_zaposlenik'];
	// $pr_rata = $row_date['pr_rata'];
	// $rata = $pr_rata;

	// //Za Currency exchange date
	// $month = date('m');
	// $day = date('d');
	// $year = date('Y');
	
	// $id_tima = getTeamIdByEmployee($employee_id);
	
	// $vrsta_ugovora = getVrstaUgovora($pr_kandidat_id);
	// switch($vrsta_ugovora){
		// case 1: case 2: case 22:
			// $broj_rata = 2;
		// break;
		// case 5: case 6: case 23:
			// $broj_rata = 3;
		// break;
		// case 7: case 8: case 24:
			// $broj_rata = 4;
		// break;
		// case 3: case 4: case 25:
			// $broj_rata = 5;
		// break;
		// default:
		// $broj_rata = 1;
	// }

	// $vrijeme_uplate = date('Y-m-d 07:00:00', strtotime($datum_uplate));
	// $vrijeme_uplate_f = strtotime($vrijeme_uplate);
	
	// // echo $datum_uplate;
	// if ($pr_rata == 1){
		// //Adil provizija
		// $branch_id = getBranchIdByEmployee($agent_prodao);
		// $branches = array(1,3,4,5,6,7,9); //poslovnice od kojih ne dobija proviziju
		// // $branches = array(2,8,10);
		// if(!in_array($branch_id, $branches )){
			
			// $tipovi = $db->prepare("
							// SELECT tp_iznos, tp_iznos_rs, tp_tip, tp_id
							// FROM idk_tipovi_provizija
							// WHERE tp_id = 11
							// ");

			// $tipovi->execute();
			
			// $tip_row = $tipovi->fetch();
			// $tp_iznos = $tip_row["tp_iznos"];
			// $tp_iznos_rs = $tip_row["tp_iznos_rs"];
			// $tp_tip = $tip_row["tp_tip"];
			// $tp_id = $tip_row["tp_id"];
			
			// $vrijednost_tipa = $tp_iznos;
			// $vrijednost_tipa_f = number_format((float)$vrijednost_tipa, 4, '.', '');
			// $vrijednost_kategorije_f = number_format((float)$vrijednost_tipa, 4, '.', '');
			
			// $provizija_zaposlenika_bih = $tp_iznos;
			// $ch = curl_init();
			// // Disable SSL verification
			// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			// // Will return the response, if false it print the response
			// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			// // Set the url
			// $rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
			// curl_setopt($ch, CURLOPT_URL,$rls);
			// // Execute
			// $result=curl_exec($ch);
			// curl_close($ch);

			// $data = json_decode($result, TRUE);
			// $EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
			// $EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
			// $RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
			// $RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
			
			// $EUR = str_replace(',', '.', $EUR1);
			// $RSD = str_replace(',', '.', $RSD1);
			
			// $provizija_zaposlenika_eur = $provizija_zaposlenika_bih / $EUR;
			// $provizija_zaposlenika_srb = 100*$provizija_zaposlenika_bih / $RSD;
			
			// $prov_bam_f = number_format((float)$provizija_zaposlenika_bih, 4, '.', '');
			// $prov_eur_f = number_format((float)$provizija_zaposlenika_eur, 4, '.', '');
			// $rprov_rsd_f = number_format((float)$provizija_zaposlenika_srb, 4, '.', '');
			
			// $pocetak_rada = getEmployeeDoe($agent_prodao);
			// $pocetak_rada_c = date('Y-m-d', strtotime($pocetak_rada.' +3 months'));
			
			// if($datum_uplate > $pocetak_rada_c){
				// //prosla su 3 mjeseca od pocetka rada
				// //chekira se da li je uplaceno vise od 3 predracuna za taj mjesec i ako jeste ide provizija
				// $broj_uplata = getBrojUplataAgentaZaMjesec($agent_prodao);
				// echo "vece vre upl";
				// if($broj_uplata > 3){
					// //provizija
					// echo "<br>ima vise od 3 uplate";
					// echo "<br/>".$agent_prodao."---".$predracun_id."---".$valuta."---".$vrijednost_tipa_f."---".$vrijednost_kategorije_f."---".$prov_bam_f."---".$prov_eur_f."---".$rprov_rsd_f."<br/>";
					
					// //INSERT
					// /*$insert_obracun = $db->prepare("	
								// INSERT INTO idk_obracuni	
								// (employee_id,  predracun_id, status_predracuna, valuta, rata, broj_rata, vrijednost_tipa, vrijednost_kategorije, iznos_obracuna_bam, iznos_obracuna_rsd, iznos_obracuna_eur, vrijeme_kreiranja, vrijeme_uplate, tip_provizije)	
								// VALUES	
								// (:employee_id,:predracun_id,:status_predracuna,:valuta,:rata,:broj_rata,:vrijednost_tipa,:vrijednost_kategorije,:iznos_obracuna_bam,:iznos_obracuna_rsd,:iznos_obracuna_eur,:vrijeme_kreiranja,:vrijeme_uplate,:tip_provizije)	
								// ");	
					// $insert_obracun->execute(array(	
								// ':employee_id' => 173,	
								// ':predracun_id' => $predracun_id,	
								// ':status_predracuna' => 1,	
								// ':valuta' => $valuta,	
								// ':rata' => $rata,	
								// ':broj_rata' => $broj_rata,	
								// ':vrijednost_tipa' => $vrijednost_tipa_f,	
								// ':vrijednost_kategorije' => $vrijednost_kategorije_f,	
								// ':iznos_obracuna_bam' => $prov_bam_f,	
								// ':iznos_obracuna_rsd' => $rprov_rsd_f,	
								// ':iznos_obracuna_eur' => $prov_eur_f,
								// ':vrijeme_kreiranja' => $vrijeme_uplate,
								// ':vrijeme_uplate' => $vrijeme_uplate,
								// ':tip_provizije' => $tp_id
								// ));*/
				// }else{}
			// }else{
				// //uplata se desila u prva tri mjeseca rada agenata
				// //ide provizija za svaku uplatu po 20KM
				// echo "veci poc rada";
				// echo "<br/>".$agent_prodao."---".$predracun_id."---".$valuta."---".$vrijednost_tipa_f."---".$vrijednost_kategorije_f."---".$prov_bam_f."---".$prov_eur_f."---".$rprov_rsd_f."<br/>";
				// //INSERT
				// /*$insert_obracun = $db->prepare("	
							// INSERT INTO idk_obracuni	
							// (employee_id,  predracun_id, status_predracuna, valuta, rata, broj_rata, vrijednost_tipa, vrijednost_kategorije, iznos_obracuna_bam, iznos_obracuna_rsd, iznos_obracuna_eur, vrijeme_kreiranja, vrijeme_uplate, tip_provizije)	
							// VALUES	
							// (:employee_id,:predracun_id,:status_predracuna,:valuta,:rata,:broj_rata,:vrijednost_tipa,:vrijednost_kategorije,:iznos_obracuna_bam,:iznos_obracuna_rsd,:iznos_obracuna_eur,:vrijeme_kreiranja,:vrijeme_uplate,:tip_provizije)	
							// ");	
				// $insert_obracun->execute(array(	
							// ':employee_id' => 173,	
							// ':predracun_id' => $predracun_id,	
							// ':status_predracuna' => 1,	
							// ':valuta' => $valuta,	
							// ':rata' => $rata,	
							// ':broj_rata' => $broj_rata,	
							// ':vrijednost_tipa' => $vrijednost_tipa_f,	
							// ':vrijednost_kategorije' => $vrijednost_kategorije_f,	
							// ':iznos_obracuna_bam' => $prov_bam_f,	
							// ':iznos_obracuna_rsd' => $rprov_rsd_f,	
							// ':iznos_obracuna_eur' => $prov_eur_f,
							// ':vrijeme_kreiranja' => $vrijeme_uplate,
							// ':vrijeme_uplate' => $vrijeme_uplate,
							// ':tip_provizije' => $tp_id
							// ));*/
			// }
		// }else{
			// echo "nije u toj poslovnici";
		// }
		
		// //CHECK ON TOP 72H i 48H
		
	// }
// }


//ubaciObracune(1881);
/*
$predracun_id = 214;
$vrsta_ugovora = 1;
$valuta = "RSD";
$agent_id = 104;
$datum_kreiranja = '2021-01-08 12:49:22';
$tim_id = 1;

ubaciObracune(668);
*/
//uplatiObracune(1115);
//echo "xcvxc";
//createUgovorSRB(22843);


/*
switch($vrsta_ugovora){
	case 1: case 2:
		$broj_rata = 2;
	break;
	case 5: case 6:
		$broj_rata = 3;
	break;
	case 7: case 8:
		$broj_rata = 4;
	break;
	case 3: case 4:
		$broj_rata = 5;
	break;
	default:
		$broj_rata = 1;
}

$month = date('m');
$day = date('d');
$year = date('Y');

$tipovi = $db->prepare("
				SELECT tp_iznos, tp_iznos_rs, tp_tip, tp_id
				FROM idk_tipovi_provizija
				WHERE tp_status = 1 AND (tp_tim = :tp_tim OR tp_tim = 0)
				");

$tipovi->execute(array(
				":tp_tim" => $tim_id
));

while($tip_row = $tipovi->fetch()){
	$tp_iznos = $tip_row["tp_iznos"];
	$tp_iznos_rs = $tip_row["tp_iznos_rs"];
	$tp_tip = $tip_row["tp_tip"];
	$tp_id = $tip_row["tp_id"];
	if($valuta == "BAM"){
		$vrijednost_tipa = $tp_iznos/$broj_rata;
	}elseif($valuta == "RSD"){
		$vrijednost_tipa = $tp_iznos_rs/$broj_rata;
	}
	$vrijednost_tipa_f = number_format((float)$vrijednost_tipa, 4, '.', '');
	
	$kategorije = $db->prepare("
					SELECT kp_iznos, kp_iznos_rs, kp_odjeli, kp_vrsta, kp_employee_id
					FROM idk_kategorije_provizija
					WHERE kp_status = 1 AND kp_tip_id = :kp_tip_id
					");

	$kategorije->execute(array(
					':kp_tip_id' => $tp_id
	));

	while($kategorija_row = $kategorije->fetch()){
		
		$kp_iznos = $kategorija_row["kp_iznos"];
		$kp_iznos_rs = $kategorija_row["kp_iznos_rs"];
		$kp_odjeli = $kategorija_row["kp_odjeli"];
		$kp_vrsta = $kategorija_row["kp_vrsta"];
		$kp_employee_id = $kategorija_row["kp_employee_id"];
		$kp_odjeli = $kategorija_row['kp_odjeli'];
		$odjeli = explode(",", $kp_odjeli);
		
		if($valuta == "BAM"){
			$vrijednost_kategorije = $kp_iznos/$broj_rata;
			$faktor_nula = 'AND employee_faktor_provizije_bih > 0';
		}elseif($valuta == "RSD"){
			$vrijednost_kategorije = $kp_iznos_rs/$broj_rata;
			$faktor_nula = 'AND employee_faktor_provizije_srb > 0';
		}
		$vrijednost_kategorije_f = number_format((float)$vrijednost_kategorije, 4, '.', '');
		
		//GET SUMU FAKTORA ZA TU KATEGORIJU
		$faktor_query = $db->prepare("
							SELECT SUM(employee_faktor_provizije_bih) as suma_bih, SUM(employee_faktor_provizije_srb) as suma_srb
							FROM idk_employees
							WHERE employee_odjel IN ($kp_odjeli) AND employee_status != 0 ");

		$faktor_query->execute();
		$faktor_row = $faktor_query->fetch();
		$suma_faktora_bih = $faktor_row['suma_bih'];
		$suma_faktora_srb = $faktor_row['suma_srb'];
		
		if($kp_vrsta == 1){
			$employee_id = $agent_id;
			if($valuta == "BAM"){
				$provizija_zaposlenika_bih = $kp_iznos/$broj_rata;
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
				
				$provizija_zaposlenika_eur = $provizija_zaposlenika_bih / $EUR;
				$provizija_zaposlenika_srb = 100*$provizija_zaposlenika_bih / $RSD;
			}elseif($valuta == "RSD"){
				$provizija_zaposlenika_srb = $kp_iznos_rs/$broj_rata;
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
				
				$provizija_zaposlenika_bih = ($provizija_zaposlenika_srb / 100) * $RSD ;
				$provizija_zaposlenika_eur = $provizija_zaposlenika_bih / $EUR;
			}
			$prov_bam_f = number_format((float)$provizija_zaposlenika_bih, 4, '.', '');
			$prov_eur_f = number_format((float)$provizija_zaposlenika_eur, 4, '.', '');
			$rprov_rsd_f = number_format((float)$provizija_zaposlenika_srb, 4, '.', '');
			
			echo $employee_id."---".$predracun_id."---".$valuta."---".$vrijednost_tipa_f."---".$vrijednost_kategorije_f."---".$prov_bam_f."---".$prov_eur_f."---".$rprov_rsd_f."<br/>";
			
			//INSERT
			$insert_obracun = $db->prepare("	
						INSERT INTO idk_obracuni	
						(employee_id,  predracun_id, valuta, broj_rata, vrijednost_tipa, vrijednost_kategorije, iznos_obracuna_bam, iznos_obracuna_rsd, iznos_obracuna_eur, vrijeme_kreiranja)	
						VALUES	
						(:employee_id,:predracun_id,:valuta,:broj_rata,:vrijednost_tipa,:vrijednost_kategorije,:iznos_obracuna_bam,:iznos_obracuna_rsd,:iznos_obracuna_eur,:vrijeme_kreiranja)	
						");	
			$insert_obracun->execute(array(	
						':employee_id' => $employee_id,	
						':predracun_id' => $predracun_id,	
						':valuta' => $valuta,	
						':broj_rata' => $broj_rata,	
						':vrijednost_tipa' => $vrijednost_tipa_f,	
						':vrijednost_kategorije' => $vrijednost_kategorije,	
						':iznos_obracuna_bam' => $prov_bam_f,	
						':iznos_obracuna_rsd' => $rprov_rsd_f,	
						':iznos_obracuna_eur' => $prov_eur_f,
						':vrijeme_kreiranja' => $datum_kreiranja
						));
			
			
		}elseif($kp_vrsta == 2){
			foreach($odjeli as $odjel_id){
				$odjeli_query = $db->prepare("
									SELECT employee_id, employee_faktor_provizije_bih, employee_faktor_provizije_srb
									FROM idk_employees
									WHERE employee_odjel = :employee_odjel AND employee_status != 0 $faktor_nula
									ORDER BY employee_id ");

				$odjeli_query->execute(array(
									":employee_odjel" => $odjel_id
				));
				
				while($employee_row = $odjeli_query->fetch()){

					$employee_id = $employee_row['employee_id'];
					$faktor_bih = $employee_row['employee_faktor_provizije_bih'];
					$faktor_srb = $employee_row['employee_faktor_provizije_srb'];
					
					if($valuta == "BAM"){
						$provizija_zaposlenika_bih = (($kp_iznos * $faktor_bih)/$suma_faktora_bih)/$broj_rata;
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
						
						$provizija_zaposlenika_eur = $provizija_zaposlenika_bih / $EUR;
						$provizija_zaposlenika_srb = 100*$provizija_zaposlenika_bih / $RSD;
						
						
					}elseif($valuta == "RSD"){
						$provizija_zaposlenika_srb = (($kp_iznos_rs * $faktor_srb)/$suma_faktora_srb)/$broj_rata;
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
						
						$provizija_zaposlenika_bih = ($provizija_zaposlenika_srb / 100) * $RSD ;
						$provizija_zaposlenika_eur = $provizija_zaposlenika_bih / $EUR;
					}
					$prov_bam_f = number_format((float)$provizija_zaposlenika_bih, 4, '.', '');
					$prov_eur_f = number_format((float)$provizija_zaposlenika_eur, 4, '.', '');
					$rprov_rsd_f = number_format((float)$provizija_zaposlenika_srb, 4, '.', '');
					
					echo $employee_id."---".$predracun_id."---".$valuta."---".$vrijednost_tipa_f."---".$vrijednost_kategorije_f."---".$prov_bam_f."---".$prov_eur_f."---".$rprov_rsd_f."<br/>";
					//INSERT
					$insert_obracun = $db->prepare("	
								INSERT INTO idk_obracuni	
								(employee_id,  predracun_id, valuta, broj_rata, vrijednost_tipa, vrijednost_kategorije, iznos_obracuna_bam, iznos_obracuna_rsd, iznos_obracuna_eur, vrijeme_kreiranja)	
								VALUES	
								(:employee_id,:predracun_id,:valuta,:broj_rata,:vrijednost_tipa,:vrijednost_kategorije,:iznos_obracuna_bam,:iznos_obracuna_rsd,:iznos_obracuna_eur,:vrijeme_kreiranja)	
								");	
					$insert_obracun->execute(array(	
								':employee_id' => $employee_id,	
								':predracun_id' => $predracun_id,	
								':valuta' => $valuta,	
								':broj_rata' => $broj_rata,	
								':vrijednost_tipa' => $vrijednost_tipa_f,	
								':vrijednost_kategorije' => $vrijednost_kategorije,	
								':iznos_obracuna_bam' => $prov_bam_f,	
								':iznos_obracuna_rsd' => $rprov_rsd_f,	
								':iznos_obracuna_eur' => $prov_eur_f,
								':vrijeme_kreiranja' => $datum_kreiranja
								));
				}
			}
			
		}elseif($kp_vrsta == 3){
			
			$employee_id = $kp_employee_id;
			if($valuta == "BAM"){
				$provizija_zaposlenika_bih = $kp_iznos/$broj_rata;
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
				
				$provizija_zaposlenika_eur = $provizija_zaposlenika_bih / $EUR;
				$provizija_zaposlenika_srb = 100*$provizija_zaposlenika_bih / $RSD;
			}elseif($valuta == "RSD"){
				$provizija_zaposlenika_srb = $kp_iznos_rs/$broj_rata;
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
				
				$provizija_zaposlenika_bih = ($provizija_zaposlenika_srb / 100) * $RSD ;
				$provizija_zaposlenika_eur = $provizija_zaposlenika_bih / $EUR;
			}
			$prov_bam_f = number_format((float)$provizija_zaposlenika_bih, 4, '.', '');
			$prov_eur_f = number_format((float)$provizija_zaposlenika_eur, 4, '.', '');
			$rprov_rsd_f = number_format((float)$provizija_zaposlenika_srb, 4, '.', '');
			
			echo $employee_id."---".$predracun_id."---".$valuta."---".$vrijednost_tipa_f."---".$vrijednost_kategorije_f."---".$prov_bam_f."---".$prov_eur_f."---".$rprov_rsd_f."<br/>";
			//INSERT
			$insert_obracun = $db->prepare("	
						INSERT INTO idk_obracuni	
						(employee_id,  predracun_id, valuta, broj_rata, vrijednost_tipa, vrijednost_kategorije, iznos_obracuna_bam, iznos_obracuna_rsd, iznos_obracuna_eur, vrijeme_kreiranja)	
						VALUES	
						(:employee_id,:predracun_id,:valuta,:broj_rata,:vrijednost_tipa,:vrijednost_kategorije,:iznos_obracuna_bam,:iznos_obracuna_rsd,:iznos_obracuna_eur,:vrijeme_kreiranja)	
						");	
			$insert_obracun->execute(array(	
						':employee_id' => $employee_id,	
						':predracun_id' => $predracun_id,	
						':valuta' => $valuta,	
						':broj_rata' => $broj_rata,	
						':vrijednost_tipa' => $vrijednost_tipa_f,	
						':vrijednost_kategorije' => $vrijednost_kategorije,	
						':iznos_obracuna_bam' => $prov_bam_f,	
						':iznos_obracuna_rsd' => $rprov_rsd_f,	
						':iznos_obracuna_eur' => $prov_eur_f,
						':vrijeme_kreiranja' => $datum_kreiranja
						));
		}elseif($kp_vrsta == 4){
			//SUMA FAKTORA ZA PROVIZIJE OD TIMOVA
			$faktor_tim_query = $db->prepare("
								SELECT SUM(employee_faktor_za_timove) as suma_tim
								FROM idk_employees
								WHERE employee_status != 0 ");

			$faktor_tim_query->execute();
			$faktor_tim_row = $faktor_tim_query->fetch();
			$suma_faktora_tim = $faktor_tim_row['suma_tim'];
			
			$emp_query = $db->prepare("
								SELECT employee_id, employee_firstname, employee_lastname, employee_faktor_za_timove, employee_odjel
								FROM idk_employees
								WHERE employee_status != 0 AND employee_team = 1 AND employee_faktor_za_timove > 0
								ORDER BY employee_odjel,employee_id ");

			$emp_query->execute();
			
			while($emp_row = $emp_query->fetch()){

				$employee_id = $emp_row['employee_id'];
				$employee_faktor_za_timove = $emp_row['employee_faktor_za_timove'];
				
				if($valuta == "BAM"){
					$provizija_zaposlenika_bih = (($kp_iznos * $employee_faktor_za_timove)/$suma_faktora_tim)/$broj_rata;
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
					
					$provizija_zaposlenika_eur = $provizija_zaposlenika_bih / $EUR;
					$provizija_zaposlenika_srb = 100*$provizija_zaposlenika_bih / $RSD;
					
					
				}elseif($valuta == "RSD"){
					$provizija_zaposlenika_srb = (($kp_iznos_rs * $employee_faktor_za_timove)/$suma_faktora_tim)/$broj_rata;
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
					
					$provizija_zaposlenika_bih = ($provizija_zaposlenika_srb / 100) * $RSD ;
					$provizija_zaposlenika_eur = $provizija_zaposlenika_bih / $EUR;
				}
				$prov_bam_f = number_format((float)$provizija_zaposlenika_bih, 4, '.', '');
				$prov_eur_f = number_format((float)$provizija_zaposlenika_eur, 4, '.', '');
				$rprov_rsd_f = number_format((float)$provizija_zaposlenika_srb, 4, '.', '');
				
				echo $employee_id."---".$predracun_id."---".$valuta."---".$vrijednost_tipa_f."---".$vrijednost_kategorije_f."---".$prov_bam_f."---".$prov_eur_f."---".$rprov_rsd_f."<br/>";
				//INSERT
				$insert_obracun = $db->prepare("	
							INSERT INTO idk_obracuni	
							(employee_id,  predracun_id, valuta, broj_rata, vrijednost_tipa, vrijednost_kategorije, iznos_obracuna_bam, iznos_obracuna_rsd, iznos_obracuna_eur, vrijeme_kreiranja)	
							VALUES	
							(:employee_id,:predracun_id,:valuta,:broj_rata,:vrijednost_tipa,:vrijednost_kategorije,:iznos_obracuna_bam,:iznos_obracuna_rsd,:iznos_obracuna_eur,:vrijeme_kreiranja)	
							");	
				$insert_obracun->execute(array(	
							':employee_id' => $employee_id,	
							':predracun_id' => $predracun_id,	
							':valuta' => $valuta,	
							':broj_rata' => $broj_rata,	
							':vrijednost_tipa' => $vrijednost_tipa_f,	
							':vrijednost_kategorije' => $vrijednost_kategorije,	
							':iznos_obracuna_bam' => $prov_bam_f,	
							':iznos_obracuna_rsd' => $rprov_rsd_f,	
							':iznos_obracuna_eur' => $prov_eur_f,
							':vrijeme_kreiranja' => $datum_kreiranja
							));
			}
		}
	}
}
*/

/* SLANJE MAILOVA*/
/*
require 'mail/Exception.php';
require 'mail/PHPMailer.php';
require 'mail/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$filename1 = $_SERVER['DOCUMENT_ROOT']."/files/files/companies/Infobroschure_GmbH2020.pdf";
$filename2 = $_SERVER['DOCUMENT_ROOT']."/files/files/companies/Auftragsvorlage_JobStep_GmbH_Z.E.pdf";
$signature = $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/zlata_sign.jpg";


$signatura_bih = $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/signatura_bih.jpg";
$signatura_srb = $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/signatura_srb.jpg";
$filename_predracun =  $_SERVER['DOCUMENT_ROOT']."/files/predracuni_dipl/20201118164552DIPLB20.pdf";
$filename_ugovor =  $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/UGB-5-11-20.pdf";
$filename_uplatnica =  $_SERVER['DOCUMENT_ROOT']."/files/ugovori_uplatnice_dipl/UPB-10-11-20.pdf";
//$mailovi = array('e.bender@wwtravel.net');
$mailovi = array('e.bender@wwtravsadel.net', 'asdasdasd');
// $mailovi = array('a.toromanovic@job-step.net','cajo.suljic@gmail.com');

foreach($mailovi as $mail_recepient){
	
	$mail = new PHPMailer;

	//$mail->isSMTP();											// Set mailer to use SMTP

	$mail->Host = 'smtp.strato.de;smtp.strato.de';				// Specify main and backup SMTP servers
	$mail->SMTPAuth = true;										// Enable SMTP authentication
	$mail->Username = 'no-reply@wwtravel.net';					// SMTP username
	$mail->Password = 'fdsaSD43fds';							// SMTP password
	$mail->SMTPSecure = 'ssl';									// Enable TLS encryption, `ssl` also accepted
	$mail->Port = 465;											// TCP port to connect to
	$mail->CharSet = 'UTF-8';

	//DE MAIL MORA ICIsa
	//$mail->setFrom('info@job-step.de');     		// Add a recipient
	//$mail->setFrom('nostrifikacija@job-step.net');     		// Add a recipient
	$mail->setFrom('nostrifikacija@job-step.net');     		// Add a recipient
	$mail->addAddress($mail_recepient);		// Add a recipient
	// $mail->AddAttachment($filename_predracun); 
	// $mail->AddAttachment($filename_ugovor); 
	$mail->AddAttachment($filename_uplatnica); 
	
	//BiH
	$mail->Subject = "Nostrifikacija: Ugovor i predračun";
	$mail->Body    = "Poštovani,
						<br><br>kako bismo Vam olakšali prve korake procesa nostrifikacije Vaše diplome, kao što smo se dogovorili, u prilogu Vam šaljemo:
						<br><br>1.	Ugovor o nostrifikaciji
						<br><br>2.	Predračun
						<br><br>3.	Primjer uplatnice

						<br><br>Molimo Vas da nas obavijestite o Vašoj uplati kako bismo u što kraćem roku poduzeli sljedeće korake, a Vama približili odlazak u Njemačku.

						<br><br>Ukoliko imate dodatnih pitanja, rado Vam stojimo na raspolaganju.
						<br><br>Vaš Jobstep Team
						<br><br>
						<img src='cid:logo_2u'>
	";
	
	$mail->AddEmbeddedImage($signatura_bih, 'logo_2u');
	
	//Srbija
	/*$mail->Subject = "Nostrifikacija: Ugovor i predračun";
	$mail->Body    = "Poštovani,
						<br><br>po prethodnom dogovoru, a na osnovu Vašeg iskazanog interesovanja, želje i volje, u prilogu ovog mejla Vam dostavljam sledeću dokumentaciju, a na ime pružanja usluge posredovanja u postupku nostrifikacije diplome:
						<br>- Ugovor o pružanju usluge posredovanja u postupku nostrifikacije diplome,
						<br>- Informativni list,
						<br>- Predračun,
						<br>- primer ispunjenog naloga za uplatu.

						<br><br>Molim Vas da:

						<br><br>- dati ugovor odštampate u četiri primerka i potpišete onako kako se traži na mestu naznačenom za potpis korisnika usluge,
						<br>te dva primjerka pošaljete na našu adresu
						<br>- dati informativni list odštampate u dva primerka i potpišete onako kako se traži na mestu naznačenom za potpis korisnika usluge, 
						<br>te da prethodno navedenu i tako potpisanu dokumentaciju pošaljete / dostavite na adresu firme- podaci firme:
						<b><br>Jobstep International d.o.o.
						<br>Kneza Miloša 78
						<br>11000 Beograd- Savski Venac, Srbija.</b>
						
						<br><br>Uplatu novčanog iznosa vršite po osnovu ispostavljenog predračuna ( predračun nije neophodno štampati, punovažan je u elektronskom obliku).
						<br><br>Za sve dalje informacije, pitanja, dileme i nejasnoće budite slobodni da nas kontaktirate. Stojimo Vam na raspolaganju i radujemo se uspešnoj saradnji.
						
						<br><br>Sa poštovanjem,
						<br>Vaš Jobstep Team

						<br><br>
						<br><br>
						<img src='cid:logo_2u' width='100%'>
	";
	
	$mail->AddEmbeddedImage($signatura_srb, 'logo_2u');
	
	$mail->AltBody = "ALT";
	if(!$mail->send()) {
		echo $id.'-2 Message could not be sent. ';
		echo 'Mailer Error: ' . $mail->ErrorInfo;
		echo "<br/>";
		
		$array .= '"'.$id.'"=>"2",';
	}else{
		echo $id.'-1 Mail sent';
		echo 'Mailer Error: ' . $mail->ErrorInfo;
		echo "<br/>";
		
		//$array .= '"'.$id.'"=>"1",';

	}
}

/*
foreach($mailovi as $mail_recepient){
	
	$mail = new PHPMailer;

	//$mail->isSMTP();											// Set mailer to use SMTP

	$mail->Host = 'smtp.strato.de;smtp.strato.de';				// Specify main and backup SMTP servers
	$mail->SMTPAuth = true;										// Enable SMTP authentication
	$mail->Username = 'no-reply@wwtravel.net';					// SMTP username
	$mail->Password = 'fdsaSD43fds';							// SMTP password
	$mail->SMTPSecure = 'ssl';									// Enable TLS encryption, `ssl` also accepted
	$mail->Port = 465;											// TCP port to connect to
	$mail->CharSet = 'UTF-8';

	//DE MAIL MORA ICIsa
	$mail->setFrom('info@job-step.de', 'info@job-step.de');     		// Add a recipient
	$mail->addAddress($mail_recepient);		// Add a recipient
	$mail->AddAttachment($filename_predracun); 
	$mail->AddAttachment($filename_ugovor); 
	$mail->AddAttachment($filename_uplatnica); 
	
	$mail->Subject = "Kooperation JobStep GmbH";
	$mail->Body    = "Sehr geehrte Damen und Herren, <br><br>
						Wir wissen Kompetenz sehr zu schätzen und freuen uns jedes Mal, ein neues Projekt mit Ihnen anzustoßen. Aktuell ist in unserem Bewerberpool eine große Anzahl von Bewerber in Ihrer Branche, welche an einer langfristigen Zusammenarbeit interessiert  sind. Da Sie  unser potentieler Partner  sind müssen Sie auch nichts im Voraus für die Provision zahlen, sondern erst bei Unterschrift  des Arbeitsvertrages. Wir bitten Sie im Auftrag welchen wir Ihnen im Anhang senden, anzugeben welches Personal Sie benötigen und die Voraussetzungen für den Mitarbeiter Ihrer Wahl. . Danach senden wir Ihnen Kandidatenprofile aus denen Sie sich für ihr Unternehmen den besten Kandidaten aussuchen können.
						<br><br>Bei Fragen stehe ich Ihnen jeder Zeit zur Verfügung.
						<br><br>Die JobStep GmbH freut sich auf unsere Zusammenarbeit.

						<br><br>Mit freundlichen Grüßen
						<br><br>Fr.Elkasovic
						<br><br>
						<img src='cid:logo_2u'>
	";
	$mail->AddEmbeddedImage($signature, 'logo_2u');
	$mail->AltBody = "ALT";
	if(!$mail->send()) {
		echo $id.'-2 Message could not be sent. ';
		echo 'Mailer Error: ' . $mail->ErrorInfo;
		echo "<br/>";
		
		$array .= '"'.$id.'"=>"2",';
	}else{
		echo $id.'-1 Mail sent';
		echo 'Mailer Error: ' . $mail->ErrorInfo;
		echo "<br/>";
		
		//$array .= '"'.$id.'"=>"1",';

	}
}*/