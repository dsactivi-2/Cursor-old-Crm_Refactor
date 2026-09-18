<?php

//require('fpdf.php');
require('tfpdf.php');
include("includes/connect.php");
//include("includes/functions.php");

Global $logged_employee_id;

class PDF extends tFPDF{

	// Page header
	function Header(){
		
		Global $valutaCheck;
		Global $vrsta_dokumenta;
		
		$this->AddFont('DejaVuSerif','','DejaVuSerifCondensed.ttf',true);
		$this->AddFont('DejaVuSerif','B','DejaVuSerifCondensed-Bold.ttf',true);
		$this->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
		$this->AddFont('DejaVu','B','DejaVuSansCondensed-Bold.ttf',true);
		
		if($vrsta_dokumenta == "predracun" OR $vrsta_dokumenta == "ugovor_rs"){
			if($valutaCheck == "RSD"){
				
				// Logo
				$this->Image('images/Jobstep_logo_new.png',20,15,30);
				
				//Put the watermark
				//$this->Image('images/Jobstep_logo_new.png',30,60,150);
				
				$this->SetFont('DejaVuSerif','B',10);
				// Move to the right
				$this->Cell(10);
				
				// Title
				$this->SetLeftMargin(20);
				$this->Cell(200,5,'',0,0,'R',0);   $this->Ln();
				$this->Cell(40,5,'',0,0,'R',0);   $this->Cell(20,5,'Jobstep International d.o.o.',0,0,'l',0); $this->Ln();
				$this->Cell(40,5,'',0,0,'R',0);   $this->Ln();
				
				$this->SetFont('DejaVuSerif','',10);
				$this->Cell(40,6,'',0,0,'R',0);   $this->Cell(20,6,'a: Bulevar Mihajla Pupina 165 G, 11070 Novi Beograd, Srbija',0,0,'l',0); $this->Ln();
				$this->Cell(40,6,'',0,0,'R',0);   $this->Cell(20,6,'t: +381 11 4220 192',0,0,'l',0); $this->Ln();
				$this->Cell(40,6,'',0,0,'R',0);   $this->Cell(20,6,'w: www.job-step.rs',0,0,'l',0); $this->Ln();
				$this->Cell(40,6,'',0,0,'R',0);   $this->Cell(20,6,'e: info@job-step.rs',0,0,'l',0); $this->Ln();
				
			}else{
				
				// Logo
				$this->Image('images/Jobstep_logo_new.png',10,20,40);
			
				// Arial bold 15
				$this->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
				$this->AddFont('DejaVu','B','DejaVuSansCondensed-Bold.ttf',true);
				$this->SetFont('DejaVu','B',11);
				// Move to the right
				$this->Cell(10);
				
				// Title
				$this->SetLeftMargin(10);
				$this->Cell(200,5,'',0,0,'R',0);   $this->Ln();
				$this->Cell(120,5,'',0,0,'R',0);   $this->Cell(20,5,'Jobstep International doo',0,0,'l',0); $this->Ln();
				$this->Cell(120,5,'',0,0,'R',0);   $this->Cell(20,5,'Hamze Hume bb ',0,0,'l',0); $this->Ln();
				$this->Cell(120,5,'',0,0,'R',0);   $this->Cell(20,5,'77000 Bihać',0,0,'l',0); $this->Ln();
				$this->Cell(120,5,'',0,0,'R',0);   $this->Cell(20,5,'Bosna i Hercegovina',0,0,'l',0); $this->Ln();
				$this->Cell(200,5,'',0,0,'R',0);   $this->Ln();
				
				$this->SetFont('DejaVu','',11);
				$this->Cell(120,5,'',0,0,'R',0);   $this->Cell(20,5,'JIB: 4263788850005',0,0,'l',0); $this->Ln();
				$this->Cell(120,5,'',0,0,'R',0);   $this->Cell(20,5,'PDV: 263788850005',0,0,'l',0); $this->Ln();
				$this->Cell(120,7,'',0,0,'R',0);   $this->Cell(20,5,'Tran.ra: 141 475 5320046893',0,0,'l',0); $this->Ln();
				$this->Cell(120,7,'',0,0,'R',0);   $this->Cell(20,5,'IBAN: BA391414755310016026',0,0,'l',0); $this->Ln();
				$this->Cell(120,7,'',0,0,'R',0);   $this->Cell(20,5,'SWIFT: BBIBBA22XXX',0,0,'l',0); $this->Ln();
			}
				
			// Line break
			$this->Ln(10);
		}
	}
	
	// Page footer
	function Footer(){
		
		Global $valutaCheck;
		Global $vrsta_dokumenta;
		
		if($vrsta_dokumenta == "predracun" OR $vrsta_dokumenta == "ugovor_rs"){
			if($valutaCheck == "RSD"){
				// Position at 1.5 cm from bottom
				$this->SetY(-40);
				// Page number
				$this->SetLeftMargin(20);
				$this->SetFont('DejaVuSerif','',8);
				
				$this->Ln();
				$this->Cell(0,5,'','T',0,'L',0); $this->Ln();
				$this->Cell(0,5,'JOBSTEP INTERNATIONAL doo','',0,'L',0); $this->Ln();
				$this->SetFont('DejaVuSerif','',8);
				$this->Cell(50,5,'REG./MAT.BROJ: 21569143',0,0,'L',0); 
				$this->Cell(70,5,'TR: 220-0000000142731-57',0,0,'L',0); 
				$this->Cell(60,5,'SWIFT: PRCBRSBG',0,0,'L',0); $this->Ln();
				$this->Cell(50,5,'PIB: 111915414',0,0,'L',0); 
				$this->Cell(70,5,'ProCredit Bank a.d. Beograd, Republika Srbija',0,0,'L',0); 
				$this->Cell(60,5,'IBAN:   RS35220203020002473044',0,0,'L',0); $this->Ln();
				$this->SetAutoPageBreak( 'auto',  10);
			}else{
				// Position at 1.5 cm from bottom
				$this->SetY(-30);
				// Arial italic 8
				

				// Page number
				$this->SetLeftMargin(1);
				
				$this->Ln();
				$this->Cell(0,5,'Jobstep International doo',0,0,'C',0); $this->Ln();
				$this->SetFont('DejaVu','',10);
				$this->Cell(0,5,'Hamze Hume bb - 77000 Bihać - Bosna i Hercegovina',0,0,'C',0); $this->Ln();
				$this->SetAutoPageBreak( 'auto',  10);
			}
		}
		
	}
	protected $B = 0;
	protected $I = 0;
	protected $U = 0;
	protected $HREF = '';

	function WriteHTML($html){
		// HTML parser
		$html = str_replace("\n",' ',$html);
		$a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
		foreach($a as $i=>$e){
			if($i%2==0){
				// Text
				if($this->HREF)
					$this->PutLink($this->HREF,$e);
				else
					$this->Write(5,$e);
			}else{
				// Tag
				if($e[0]=='/')
					$this->CloseTag(strtoupper(substr($e,1)));
				else{
					// Extract attributes
					$a2 = explode(' ',$e);
					$tag = strtoupper(array_shift($a2));
					$attr = array();
					foreach($a2 as $v)
					{
						if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
							$attr[strtoupper($a3[1])] = $a3[2];
					}
					$this->OpenTag($tag,$attr);
				}
			}
		}
	}

	function OpenTag($tag, $attr){
		// Opening tag
		if($tag=='B' || $tag=='I' || $tag=='U')
			$this->SetStyle($tag,true);
		if($tag=='A')
			$this->HREF = $attr['HREF'];
		if($tag=='BR')
			$this->Ln(5);
	}

	function CloseTag($tag){
		// Closing tag
		if($tag=='B' || $tag=='I' || $tag=='U')
			$this->SetStyle($tag,false);
		if($tag=='A')
			$this->HREF = '';
	}

	function SetStyle($tag, $enable){
		// Modify style and select corresponding font
		$this->$tag += ($enable ? 1 : -1);
		$style = '';
		foreach(array('B', 'I', 'U') as $s)
		{
			if($this->$s>0)
				$style .= $s;
		}
		$this->SetFont('',$style);
	}

	function PutLink($URL, $txt){
		// Put a hyperlink
		$this->SetTextColor(0,0,255);
		$this->SetStyle('U',true);
		$this->Write(5,$txt,$URL);
		$this->SetStyle('U',false);
		$this->SetTextColor(0);
	}

}
	
function createRacunBIH($kandidat_id, $racun_bf){

	Global $db; 
	Global $logged_employee_id;
	Global $vrsta_dokumenta;
	$year_skr = date('y');
	$vrsta_dokumenta = "predracun";
	
	$danas = date('d.m.Y');
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
		case 1: case 2: case 22: case 62: case 72: case 52: case 82: case 42:
			$broj_rata = 2;
			$rate_text2 = "/".$broj_rata;
		break;
		case 5: case 6: case 23: case 63: case 73: case 53: case 83: case 43:
			$broj_rata = 3;
			$rate_text2 = "/".$broj_rata;
		break;
		case 7: case 8: case 24: case 64: case 74: case 54: case 84: case 44:
			$broj_rata = 4;
			$rate_text2 = "/".$broj_rata;
		break;
		case 3: case 4: case 25: case 65: case 75: case 55: case 85: case 45:
			$broj_rata = 5;
			$rate_text2 = "/".$broj_rata;
		break;
		default:
			$broj_rata = 1;
			$rate_text2 = "";
    }
	
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

	$brojac_racuna = createBrojRacuna("BiH");
	$novi_racun = "DIPLRB-".$brojac_racuna."-".$year_skr;
	$file_datum = date('YmdHis'); 
	$racun_file = $file_datum."DIPLRB".$brojac_racuna.".pdf";
	
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
						WHERE pr_kandidat_id = :pr_kandidat_id AND pr_status = 2 AND pr_stornirano = 0 AND pr_vrsta_predracuna = 1 AND pr_izdan_racun = 0
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
		
		if($popust_procent != 100){
			$cijena = ($pr_vrijednost_BAM/1.17) * (100 / (100 - $popust_procent));
		}else{
			$cijena = 811.97;
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
		$update_predracuni = $db->prepare("
									UPDATE idk_predracuni
									SET pr_izdan_racun = 1
									WHERE pr_id = :pr_id
									");
		
		$update_predracuni->execute(array(
			':pr_id' => $pr_id
		));
	}
	$pdv_stopa = 0.17;
	$ukupno = $ukupno_za_placanje / (1 + $pdv_stopa);
	$ukupno_f = number_format($ukupno, 2, '.', '');
	$ukupno_za_placanje_f = number_format($ukupno_za_placanje, 2, '.', '');
	$pdv_iznos = $ukupno_za_placanje_f - $ukupno_f;
	$pdv_iznos_f = number_format($pdv_iznos, 2, '.', '');
	$iznos_f = number_format($iznos, 2, '.', '');
	
	$popust = $iznos_f - $ukupno_f;
	
	$popust_f = number_format($popust, 2, '.', '');
	
	$pdf->Ln(22);
	
    
	if($popust_part){
		$pdf->Cell(100,7,'','T','','C',0); 
		$pdf->Cell(53,7,'Iznos','T','','L',0); 
		$pdf->Cell(27,7,$iznos_f." KM",'T','','R',0); $pdf->Ln();
		
		$pdf->Cell(100,7,'','','','C',0); 
		$pdf->Cell(45,7,'Popust','','','L',0); 
		$pdf->Cell(8,7,$popust_procent.'% - ','','','L',0); 
		$pdf->Cell(27,7,$popust_f." KM",'','','R',0); $pdf->Ln();
		//$pdf->Cell(27,7,$popust_f." KM",'','','R',0); $pdf->Ln();
		
		$pdf->Cell(100,7,'','','','C',0); 
		$pdf->Cell(53,7,'Ukupno','','','L',0); 
		$pdf->Cell(27,7,$ukupno_f." KM",'','','R',0); $pdf->Ln();
		
	}else{
		$pdf->Cell(100,7,'','T','','C',0); 
		$pdf->Cell(53,7,'Ukupno','T','','L',0); 
		$pdf->Cell(27,7,$ukupno_f." KM",'T','','R',0); $pdf->Ln();
		
	}
	
	$pdf->Cell(100,7,'',0,'','C',0); 
	$pdf->Cell(53,7,'PDV',0,'','L',0); 
	$pdf->Cell(27,7,$pdv_iznos_f." KM",0,'','R',0); $pdf->Ln();
	
	$pdf->Cell(100,7,'',0,'','C',0); 
	$pdf->Cell(53,7,'Ukupno za plaćanje KM',0,'','L',0); 
	$pdf->Cell(27,7,$ukupno_za_placanje_f." KM",0,'','R',0); $pdf->Ln();
	
	$pdf->Cell(100,7,'',0,'','C',0); 
	$pdf->Cell(53,7,'EUR',0,'','L',0); 
	$pdf->Cell(27,7,'€ '.$ukupno_za_placanje_EUR,0,'','R',0); $pdf->Ln();
	
	$pdf->Ln(22);	
	
	$filename="files/racuni_dipl/".$racun_file."";
	// var_dump($filename);
	// exit();
	
	//$pdf->Output();
	$pdf->Output($filename,'F');
	
	//INSER RACUN
	$insert_racun = $db->prepare("	
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
	
	return $racun_file;
}
	
function createPredracunBIH($predracun_id){

	Global $db; 
	Global $logged_employee_id;
	Global $vrsta_dokumenta;
	$vrsta_dokumenta = "predracun";
	
	$danas = date('d.m.Y');
	$rok_uplate = date('d.m.Y', strtotime($danas. ' + 10 days'));
	$get_predracun = $db->prepare("
					SELECT pr_broj_predracuna, nk.ime_nd_kandidata, nk.prezime_nd_kandidata, nk.ulica_nd_kandidata, nk.postanski_broj_nd_kandidata, nk.grad_nd_kandidata, pr_rata, nk.vrsta_ugovora_nd_kandidata, pr_vrijednost_BAM, pr_vrijednost_EUR, pr_file, pr_domaca_valuta, jmbg_nd_kandidata
					FROM idk_predracuni
					JOIN idk_nd_kandidata nk
					ON nk.id_broj_nd_kandidata = pr_kandidat_id
					WHERE pr_id = :pr_id
					");

	$get_predracun->execute(array(
						':pr_id' => $predracun_id));

	$predracun_row = $get_predracun->fetch();
	
	$broj_predracuna = $predracun_row["pr_broj_predracuna"];
	$ime = $predracun_row["ime_nd_kandidata"];
	$prezime = $predracun_row["prezime_nd_kandidata"];
	$ulica = $predracun_row["ulica_nd_kandidata"];
	$pbroj = $predracun_row["postanski_broj_nd_kandidata"];
	$grad = $predracun_row["grad_nd_kandidata"];
	$pr_rata = $predracun_row["pr_rata"];
	$pr_vrijednost_BAM = $predracun_row["pr_vrijednost_BAM"];
	$pr_vrijednost_EUR = $predracun_row["pr_vrijednost_EUR"];
	$pr_file = $predracun_row["pr_file"];
	$vrsta_ugovora = $predracun_row["vrsta_ugovora_nd_kandidata"];
	$pr_domaca_valuta = $predracun_row["pr_domaca_valuta"];
	$jmbg = $predracun_row["jmbg_nd_kandidata"];
	
	Global $valutaCheck;
	$valutaCheck = $pr_domaca_valuta;
	
	$pr_vrijednost_EUR_f = number_format($pr_vrijednost_EUR, 2, '.', '');
	
	$ukupno_za_placanje = $pr_vrijednost_BAM;
	$ukupno_za_placanje_f = number_format($ukupno_za_placanje, 2, '.', '');
	$pdv_stopa = 0.17;
	$ukupno = $ukupno_za_placanje / (1 + $pdv_stopa);
	$ukupno_f = number_format($ukupno, 2, '.', '');
	$pdv_iznos = $ukupno * $pdv_stopa;
	$pdv_iznos = $ukupno_za_placanje_f - $ukupno_f;
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
		$iznos = $ukupno * (100 / (100 - $popust_procent));
	}else{
		$iznos = 811.97;
	}
	$iznos_f = number_format($iznos, 2, '.', '');
	$popust = $iznos_f - $ukupno_f;
	$popust_f = number_format($popust, 2, '.', '');
	$cijena = $iznos;
	$cijena_f = number_format($cijena, 2, '.', '');
	
	// var_dump($ukupno_bam_f);
	// var_dump($pdv_bam);
	// var_dump($iznos);
	// exit();
	switch($vrsta_ugovora){
		case 1: case 2: case 22: case 62: case 72: case 52: case 82: case 42:
			$broj_rata = 2;
		break;
		case 5: case 6: case 23: case 63: case 73: case 53: case 83: case 43:
			$broj_rata = 3;
		break;
		case 7: case 8: case 24: case 64: case 74: case 54: case 84: case 44:
			$broj_rata = 4;
		break;
		case 3: case 4: case 25: case 65: case 75: case 55: case 85: case 45:
			$broj_rata = 5;
		break;
		default:
			$broj_rata = 1;
	}
	
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
		$rate_txt_3 = $rate_text1."dio usluge ".$pr_rata."/".$broj_rata;
	}
	
	$adresa = $ulica.", ".$pbroj." ".$grad;
	
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
	$pdf->Cell(80,10,'Predračun: '.$broj_predracuna.'','B'); /*$pdf->Line(11,147,90,147);*/ $pdf->Ln(25);
	
	$pdf->SetFont('DejaVu','B',9);
	$pdf->Cell(12,7,'Šifra','T,L,B','','C',0); 
	$pdf->Cell(98,7,'Naziv','T,B','','L',0); 
	$pdf->Cell(16,7,'Količina','T,B','','C',0); 
	$pdf->Cell(27,7,'Cijena','T,B','','R',0); 
	$pdf->Cell(27,7,'Ukupno','T,B,R',1,'R',0); 
	
	$pdf->SetFont('DejaVu','',10);
	$pdf->Cell(12,7,'1',0,'','C',0); 
	$pdf->Cell(98,7,'Obrada podataka za nostrifikaciju diplome'.$rate_txt_3,0,'','L',0); 
	$pdf->Cell(16,7,'1',0,'','C',0); 
	$pdf->Cell(27,7,$cijena_f." KM",0,'','R',0); 
	$pdf->Cell(27,7,$cijena_f." KM",0,'','R',0); 
	//$pdf->SetFillColor(153,153,153);
	$pdf->Ln(22);
	//$pdf->Line(11,199,190,199); $pdf->Ln();
	
	if($popust_part){
		$pdf->Cell(100,7,'','T','','C',0); 
		$pdf->Cell(53,7,'Iznos','T','','L',0); 
		$pdf->Cell(27,7,$iznos_f." KM",'T','','R',0); $pdf->Ln();
		
		$pdf->Cell(100,7,'',0,'','C',0); 
		$pdf->Cell(45,7,'Popust',0,'','L',0); 
        $pdf->Cell(8,7,$popust_procent.'% - ','','','L',0); 
		$pdf->Cell(27,7,$popust_f." KM",0,'','R',0); $pdf->Ln();
		
		$pdf->Cell(100,7,'','','','C',0); 
		$pdf->Cell(53,7,'Ukupno','','','L',0); 
		$pdf->Cell(27,7,$ukupno_f." KM",'','','R',0); $pdf->Ln();
	}else{
		$pdf->Cell(100,7,'','T','','C',0); 
		$pdf->Cell(53,7,'Ukupno','T','','L',0); 
		$pdf->Cell(27,7,$ukupno_f." KM",'T','','R',0); $pdf->Ln();
	}
	
	$pdf->Cell(100,7,'',0,'','C',0); 
	$pdf->Cell(53,7,'PDV',0,'','L',0); 
	$pdf->Cell(27,7,$pdv_iznos_f." KM",0,'','R',0); $pdf->Ln();
	
	$pdf->Cell(100,7,'',0,'','C',0); 
	$pdf->Cell(53,7,'Ukupno za plaćanje KM',0,'','L',0); 
	$pdf->Cell(27,7,$ukupno_za_placanje_f." KM",0,'','R',0); $pdf->Ln();
	
	$pdf->Cell(100,7,'',0,'','C',0); 
	$pdf->Cell(53,7,'EUR',0,'','L',0); 
	$pdf->Cell(27,7,'€ '.$pr_vrijednost_EUR_f,0,'','R',0); $pdf->Ln();
	
	$pdf->Ln(22);	
	$pdf->Cell(180,7,'Uplatom predračuna prihvaćena je ponuda ugovora o pružanju usluge posredovanja u postupku nostrifikacije diplome.',0,'','L',0);	
	
	$filename="files/predracuni_dipl/".$pr_file."";
	// var_dump($filename);
	// exit();
	
	//$pdf->Output();
	$pdf->Output($filename,'F');
}

function createRucnoPredracunBIH($predracun_id, $datum_kreiranja){

	Global $db; 
	Global $logged_employee_id;
	Global $vrsta_dokumenta;
	$vrsta_dokumenta = "predracun";
	
	$danas = date('d.m.Y', strtotime($datum_kreiranja));
	$rok_uplate = date('d.m.Y', strtotime($danas. ' + 10 days'));
	$get_predracun = $db->prepare("
					SELECT pr_broj_predracuna, nk.ime_nd_kandidata, nk.prezime_nd_kandidata, nk.ulica_nd_kandidata, nk.postanski_broj_nd_kandidata, nk.grad_nd_kandidata, pr_rata, nk.vrsta_ugovora_nd_kandidata, pr_vrijednost_BAM, pr_vrijednost_EUR, pr_file, pr_domaca_valuta, jmbg_nd_kandidata
					FROM idk_predracuni
					JOIN idk_nd_kandidata nk
					ON nk.id_broj_nd_kandidata = pr_kandidat_id
					WHERE pr_id = :pr_id
					");

	$get_predracun->execute(array(
						':pr_id' => $predracun_id));

	$predracun_row = $get_predracun->fetch();
	
	$broj_predracuna = $predracun_row["pr_broj_predracuna"];
	$ime = $predracun_row["ime_nd_kandidata"];
	$prezime = $predracun_row["prezime_nd_kandidata"];
	$ulica = $predracun_row["ulica_nd_kandidata"];
	$pbroj = $predracun_row["postanski_broj_nd_kandidata"];
	$grad = $predracun_row["grad_nd_kandidata"];
	$pr_rata = $predracun_row["pr_rata"];
	$pr_vrijednost_BAM = $predracun_row["pr_vrijednost_BAM"];
	$pr_vrijednost_EUR = $predracun_row["pr_vrijednost_EUR"];
	$pr_file = $predracun_row["pr_file"];
	$vrsta_ugovora = $predracun_row["vrsta_ugovora_nd_kandidata"];
	$pr_domaca_valuta = $predracun_row["pr_domaca_valuta"];
	$jmbg = $predracun_row["jmbg_nd_kandidata"];
	
	Global $valutaCheck;
	$valutaCheck = $pr_domaca_valuta;
	
	$pr_vrijednost_EUR_f = number_format($pr_vrijednost_EUR, 2, '.', '');
	
	$ukupno_za_placanje = $pr_vrijednost_BAM;
	$ukupno_za_placanje_f = number_format($ukupno_za_placanje, 2, '.', '');
	$pdv_stopa = 0.17;
	$ukupno = $ukupno_za_placanje / (1 + $pdv_stopa);
	$ukupno_f = number_format($ukupno, 2, '.', '');
	$pdv_iznos = $ukupno * $pdv_stopa;
	$pdv_iznos = $ukupno_za_placanje_f - $ukupno_f;
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
		$iznos = $ukupno * (100 / (100 - $popust_procent));
	}else{
		$iznos = 811.97;
	}
	$iznos_f = number_format($iznos, 2, '.', '');
	$popust = $iznos_f - $ukupno_f;
	$popust_f = number_format($popust, 2, '.', '');
	$cijena = $iznos;
	$cijena_f = number_format($cijena, 2, '.', '');
	
	// var_dump($ukupno_bam_f);
	// var_dump($pdv_bam);
	// var_dump($iznos);
	// exit();
	switch($vrsta_ugovora){
		case 1: case 2: case 22: case 62: case 72: case 52: case 82: case 42:
			$broj_rata = 2;
		break;
		case 5: case 6: case 23: case 63: case 73: case 53: case 83: case 43:
			$broj_rata = 3;
		break;
		case 7: case 8: case 24: case 64: case 74: case 54: case 84: case 44:
			$broj_rata = 4;
		break;
		case 3: case 4: case 25: case 65: case 75: case 55: case 85: case 45:
			$broj_rata = 5;
		break;
		default:
			$broj_rata = 1;
	}
	
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
		$rate_txt_3 = $rate_text1."dio usluge ".$pr_rata."/".$broj_rata;
	}
	
	$adresa = $ulica.", ".$pbroj." ".$grad;
	
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
	$pdf->Cell(80,10,'Predračun: '.$broj_predracuna.'','B'); /*$pdf->Line(11,147,90,147);*/ $pdf->Ln(25);
	
	$pdf->SetFont('DejaVu','B',9);
	$pdf->Cell(12,7,'Šifra','T,L,B','','C',0); 
	$pdf->Cell(98,7,'Naziv','T,B','','L',0); 
	$pdf->Cell(16,7,'Količina','T,B','','C',0); 
	$pdf->Cell(27,7,'Cijena','T,B','','R',0); 
	$pdf->Cell(27,7,'Ukupno','T,B,R',1,'R',0); 
	
	$pdf->SetFont('DejaVu','',10);
	$pdf->Cell(12,7,'1',0,'','C',0); 
	$pdf->Cell(98,7,'Obrada podataka za nostrifikaciju diplome'.$rate_txt_3,0,'','L',0); 
	$pdf->Cell(16,7,'1',0,'','C',0); 
	$pdf->Cell(27,7,$cijena_f." KM",0,'','R',0); 
	$pdf->Cell(27,7,$cijena_f." KM",0,'','R',0); 
	//$pdf->SetFillColor(153,153,153);
	$pdf->Ln(22);
	//$pdf->Line(11,199,190,199); $pdf->Ln();
	
	if($popust_part){
		$pdf->Cell(100,7,'','T','','C',0); 
		$pdf->Cell(53,7,'Iznos','T','','L',0); 
		$pdf->Cell(27,7,$iznos_f." KM",'T','','R',0); $pdf->Ln();
		
		$pdf->Cell(100,7,'',0,'','C',0); 
		$pdf->Cell(45,7,'Popust',0,'','L',0); 
        $pdf->Cell(8,7,$popust_procent.'% - ','','','L',0); 
		$pdf->Cell(27,7,$popust_f." KM",0,'','R',0); $pdf->Ln();
		
		$pdf->Cell(100,7,'','','','C',0); 
		$pdf->Cell(53,7,'Ukupno','','','L',0); 
		$pdf->Cell(27,7,$ukupno_f." KM",'','','R',0); $pdf->Ln();
	}else{
		$pdf->Cell(100,7,'','T','','C',0); 
		$pdf->Cell(53,7,'Ukupno','T','','L',0); 
		$pdf->Cell(27,7,$ukupno_f." KM",'T','','R',0); $pdf->Ln();
	}
	
	$pdf->Cell(100,7,'',0,'','C',0); 
	$pdf->Cell(53,7,'PDV',0,'','L',0); 
	$pdf->Cell(27,7,$pdv_iznos_f." KM",0,'','R',0); $pdf->Ln();
	
	$pdf->Cell(100,7,'',0,'','C',0); 
	$pdf->Cell(53,7,'Ukupno za plaćanje KM',0,'','L',0); 
	$pdf->Cell(27,7,$ukupno_za_placanje_f." KM",0,'','R',0); $pdf->Ln();
	
	$pdf->Cell(100,7,'',0,'','C',0); 
	$pdf->Cell(53,7,'EUR',0,'','L',0); 
	$pdf->Cell(27,7,'€ '.$pr_vrijednost_EUR_f,0,'','R',0); $pdf->Ln();
	
	$pdf->Ln(22);	
	$pdf->Cell(180,7,'Uplatom predračuna prihvaćena je ponuda ugovora o pružanju usluge posredovanja u postupku nostrifikacije diplome.',0,'','L',0);	
	
	$filename="files/predracuni_dipl/".$pr_file."";
	// var_dump($filename);
	// exit();
	
	//$pdf->Output();
	$pdf->Output($filename,'F');
}

function createPredracunSRB($predracun_id){

	Global $db;
	Global $logged_employee_id;
	Global $vrsta_dokumenta;
	$vrsta_dokumenta = "predracun";
	
	$danas = date('d.m.Y');
	$rok_uplate = date('d.m.Y', strtotime($danas. ' + 10 days'));
	$get_predracun = $db->prepare("
					SELECT pr_broj_predracuna, nk.ime_nd_kandidata, nk.prezime_nd_kandidata, nk.ulica_nd_kandidata, nk.postanski_broj_nd_kandidata, nk.grad_nd_kandidata, pr_rata, nk.vrsta_ugovora_nd_kandidata, pr_vrijednost_RSD, pr_vrijednost_EUR, pr_file, pr_domaca_valuta, jmbg_nd_kandidata
					FROM idk_predracuni
					JOIN idk_nd_kandidata nk
					ON nk.id_broj_nd_kandidata = pr_kandidat_id
					WHERE pr_id = :pr_id
					");

	$get_predracun->execute(array(
						':pr_id' => $predracun_id));

	$predracun_row = $get_predracun->fetch();
	
	$broj_predracuna = $predracun_row["pr_broj_predracuna"];
	$ime = $predracun_row["ime_nd_kandidata"];
	$prezime = $predracun_row["prezime_nd_kandidata"];
	$ulica = $predracun_row["ulica_nd_kandidata"];
	$pbroj = $predracun_row["postanski_broj_nd_kandidata"];
	$grad = $predracun_row["grad_nd_kandidata"];
	$pr_rata = $predracun_row["pr_rata"];
	$pr_vrijednost_RSD = $predracun_row["pr_vrijednost_RSD"];
	$pr_vrijednost_EUR = $predracun_row["pr_vrijednost_EUR"];
	$pr_file = $predracun_row["pr_file"];
	$vrsta_ugovora = $predracun_row["vrsta_ugovora_nd_kandidata"];
	$pr_domaca_valuta = $predracun_row["pr_domaca_valuta"];
	$jmbg = $predracun_row["jmbg_nd_kandidata"];
	
	Global $valutaCheck;
	$valutaCheck = $pr_domaca_valuta;
	
	$ukupno_za_placanje = $pr_vrijednost_RSD;
	$ukupno_za_placanje_f = number_format($ukupno_za_placanje, 2, ',', '.');
	$pdv_stopa = 0.2;
	$ukupno_bez_pdva = $ukupno_za_placanje / (1 + $pdv_stopa);
	$ukupno_bez_pdva_f = number_format($ukupno_bez_pdva, 2, ',', '.');
	$pdv_iznos = $ukupno_za_placanje - $ukupno_bez_pdva;
	$pdv_iznos_f = number_format($pdv_iznos, 2, ',', '.');
	
	
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
		$cena = $ukupno_bez_pdva * (100 / (100 - $popust_procent));
	}else{
		$cena = 49166.67;
	}
	$cena_f = number_format($cena, 2, ',', '.');
	$popust = $cena - $ukupno_bez_pdva;
	$popust_f = number_format($popust, 2, ',', '.');
	$cijena = $iznos;
	$cijena_f = number_format($cijena, 2, ',', '.');
	
	switch($vrsta_ugovora){
		case 1: case 2: case 22: case 62: case 72: case 52: case 82: case 42:
			$broj_rata = 2;
		break;
		case 5: case 6: case 23: case 63: case 73: case 53: case 83: case 43:
			$broj_rata = 3;
		break;
		case 7: case 8: case 24: case 64: case 74: case 54: case 84: case 44:
			$broj_rata = 4;
		break;
		case 3: case 4: case 25: case 65: case 75: case 55: case 85: case 45:
			$broj_rata = 5;
		break;
		default:
			$broj_rata = 1;
	}
	switch($pr_rata){
		case 1:
			if($broj_rata == 1){
				$rata_txt = "iznos novčane naknade ";
				$txt_predracun = "Uplatom Predračuna prihvata se ponuda ugovora o pružanju usluge posredovanja u postupku nostrifikacije diplome.";
				$txt_rok = "Rok za plaćanje je 10 (deset) dana od dana izdavanja predračuna.";
			}else{
				$rata_txt = "I (prvu) ratu novčanog iznosa ";
				$txt_predracun = "Uplatom Predračuna prihvata se ponuda ugovora o pružanju usluge posredovanja u postupku nostrifikacije diplome.";
				$txt_rok = "Rok za plaćanje je 7 (sedam) dana od dana izdavanja predračuna.";
			}
		break;
		case 2:
			$rata_txt = "II (drugu) ratu novčanog iznosa ";
			$txt_predracun = "Uplatom predračuna prihvaćena je ponuda ugovora o pružanju usluge posredovanja u postupku nostrifikacije diplome.";
			$txt_rok = "";
		break;
		case 3:
			$rata_txt = "III (treću) ratu novčanog iznosa ";
			$txt_predracun = "Uplatom predračuna prihvaćena je ponuda ugovora o pružanju usluge posredovanja u postupku nostrifikacije diplome.";
			$txt_rok = "";
		break;
		case 4:
			$rata_txt = "IV (četvrtu) ratu novčanog iznosa ";
			$txt_predracun = "Uplatom predračuna prihvaćena je ponuda ugovora o pružanju usluge posredovanja u postupku nostrifikacije diplome.";
			$txt_rok = "";
		break;
		case 5:
			$rata_txt = "V (petu) ratu novčanog iznosa ";
			$txt_predracun = "Uplatom predračuna prihvaćena je ponuda ugovora o pružanju usluge posredovanja u postupku nostrifikacije diplome.";
			$txt_rok = "";
		break;
		default:
			$rata_txt = "";
			$txt_predracun = "";
			$txt_rok = "";
	}
	
	$adresa = $ulica.", ".$pbroj." ".$grad;
	
	$pdf = new PDF('P','mm','A4');
	

	$pdf->AliasNbPages();
	$pdf->SetDisplayMode('real', 'single');

	$pdf->AddPage();
	
	$pdf->SetFont('DejaVuSerif','',9);
	$pdf->Cell(180,10,"Izdavalac predračuna: Jobstep International d.o.o.",'',0,'',0); $pdf->Ln();
	$pdf->Cell(180,10,"Adresa: Bulevar Mihajla Pupina 165 G, 11070 Novi Beograd, Srbija",'',0,'',0); $pdf->Ln();
	$pdf->Cell(180,10,"Matični broj: 21569143      PIB: 111915414",'',0,'',0); $pdf->Ln();
	
	$pdf->Cell(50,7,"Primalac predračuna:",'T',0,'',0); 
	$pdf->Cell(130,7,$ime." ".$prezime,'T',0,'',0); 
	$pdf->Ln();
	$pdf->Cell(50,7,"Adresa:",'',0,'',0); 
	$pdf->Cell(130,7,$adresa,'',0,'',0); 
	$pdf->Ln();
	$pdf->Cell(50,7,"JMBG:",'B',0,'',0); 
	$pdf->Cell(130,7,$jmbg,'B',0,'',0); 
	$pdf->Ln(14);
	
	$pdf->SetFont('DejaVuSerif','',10);
	$pdf->Cell(180,7,'Mesto i datum izdavanja predračuna: Beograd, '.$danas.". godine",0,0,'',0);
	$pdf->Ln();
	$pdf->Cell(180,7,'Redni broj predračuna: '.$broj_predracuna,0,0,'',0); 
	$pdf->Ln();
	$pdf->Cell(180,7,'Predračun broj: '.$broj_predracuna,0,0,'C',0);
	$pdf->Ln();	

	$pdf->SetFont('DejaVuSerif','',10);
	$pdf->Cell(0,7,'Poštovani/-a, molimo Vas da nam avansno uplatite '.$rata_txt.'na ime pružanja'); $pdf->Ln();
	$pdf->Cell(0,7,'usluge posredovanja u postupku nostrifikacije diplome, i shodno tome Vam ispostavljamo predračun: '); $pdf->Ln(12);
	

	//POCETAK TABELE
	if($popust_part){
		$pdf->Cell(12,5,'Redni','T,L,R','','L',0); 
		$pdf->Cell(42,5,'Vrsta usluge:','T,R','','L',0); 
		$pdf->Cell(15,5,'Količina','T,R','','L',0); 
		$pdf->Cell(19,5,'Cena:','T,R','','L',0); 
		$pdf->Cell(23,5,'Popust:','T,R','','L',0); 
		$pdf->Cell(19,5,'Ukupno','T,R','','L',0); 
		$pdf->Cell(31,5,'PDV:','T,R','','L',0); 
		$pdf->Cell(19,5,'Ukupno','T,R','','L',0); 
		
		$pdf->Ln();	
		$pdf->Cell(12,5,'broj:','L,R','','L',0); 
		$pdf->Cell(42,5,'','R','','L',0); 
		$pdf->Cell(15,5,'(obim)','R','','L',0); 
		$pdf->Cell(19,5,'','R','','L',0); 
		$pdf->Cell(23,5,'','R','','L',0); 
		$pdf->Cell(19,5,'bez','R','','L',0); 
		$pdf->Cell(31,5,'','R','','L',0); 
		$pdf->Cell(19,5,'sa PDV-','R','','L',0);
		
		$pdf->Ln();	
		$pdf->Cell(12,5,'','L,B,R','','L',0); 
		$pdf->Cell(42,5,'','B,R','','L',0); 
		$pdf->Cell(15,5,'usluge:','R,B','','L',0); 
		$pdf->Cell(19,5,'','R,B','','L',0); 
		$pdf->Cell(23,5,'','R,B','','L',0); 
		$pdf->Cell(19,5,'PDV-a:','B,R','','L',0); 
		$pdf->Cell(31,5,'','B,R','','L',0); 
		$pdf->Cell(19,5,'om:','B,R','','L',0);
		
		$pdf->Ln();	
		$pdf->Cell(12,5,'1.','T,L,R','','L',0); 
		$pdf->Cell(42,5,'Posredovanje u postupku','T,R','','L',0); 
		$pdf->Cell(15,5,'1,00','T,R','','L',0);
		$pdf->Cell(19,5,$cena_f,'T,R','','L',0); 
		$pdf->Cell(23,5,$popust_f,'T,R','','L',0); 
		$pdf->Cell(19,5,$ukupno_bez_pdva_f,'T,R','','L',0); 
		$pdf->Cell(31,5,$pdv_iznos_f,'T,R','','L',0); 
		$pdf->Cell(19,5,$ukupno_za_placanje_f,'T,R','','L',0); 
		
		$pdf->Ln();	
		$pdf->Cell(12,5,'','L,B,R','','L',0); 
		$pdf->Cell(42,5,'nostrifikacije diplome','B,R','','L',0); 
		$pdf->Cell(15,5,'','R,B','','L',0); 
		$pdf->Cell(19,5,'','R,B','','L',0); 
		$pdf->Cell(23,5,'(popust '.$popust_procent.'%)','R,B','','L',0); 
		$pdf->Cell(19,5,'','B,R','','L',0); 
		$pdf->Cell(31,5,'(PDV stopa 20%)','B,R','','L',0); 
		$pdf->Cell(19,5,'','B,R',1,'L',0);
	}else{
		$pdf->Cell(15,5,'Redni','T,L,R','','L',0); 
		$pdf->Cell(50,5,'Vrsta usluge:','T,R','','L',0); 
		$pdf->Cell(20,5,'Količina','T,R','','L',0); 
		$pdf->Cell(20,5,'Cena:','T,R','','L',0); 
		$pdf->Cell(20,5,'Ukupno','T,R','','L',0); 
		$pdf->Cell(35,5,'PDV:','T,R','','L',0); 
		$pdf->Cell(20,5,'Ukupno','T,R','','L',0); 
		
		$pdf->Ln();	
		$pdf->Cell(15,5,'broj:','L,R','','L',0); 
		$pdf->Cell(50,5,'','R','','L',0); 
		$pdf->Cell(20,5,'(obim)','R','','L',0); 
		$pdf->Cell(20,5,'','R','','L',0); 
		$pdf->Cell(20,5,'bez','R','','L',0); 
		$pdf->Cell(35,5,'','R','','L',0); 
		$pdf->Cell(20,5,'sa PDV-','R','','L',0);
		
		$pdf->Ln();	
		$pdf->Cell(15,5,'','L,B,R','','L',0); 
		$pdf->Cell(50,5,'','B,R','','L',0); 
		$pdf->Cell(20,5,'usluge:','R,B','','L',0); 
		$pdf->Cell(20,5,'','R,B','','L',0); 
		$pdf->Cell(20,5,'PDV-a:','B,R','','L',0); 
		$pdf->Cell(35,5,'','B,R','','L',0); 
		$pdf->Cell(20,5,'om:','B,R','','L',0);
		
		$pdf->Ln();	
		$pdf->Cell(15,5,'1.','T,L,R','','L',0); 
		$pdf->Cell(50,5,'Posredovanje u postupku','T,R','','L',0); 
		$pdf->Cell(20,5,'1,00','T,R','','L',0); 
		$pdf->Cell(20,5,$cena_f,'T,R','','L',0); 
		$pdf->Cell(20,5,$ukupno_bez_pdva_f,'T,R','','L',0); 
		$pdf->Cell(35,5,$pdv_iznos_f,'T,R','','L',0); 
		$pdf->Cell(20,5,$ukupno_za_placanje_f,'T,R','','L',0); 
		
		$pdf->Ln();	
		$pdf->Cell(15,5,'','L,B,R','','L',0); 
		$pdf->Cell(50,5,'nostrifikacije diplome','B,R','','L',0); 
		$pdf->Cell(20,5,'','R,B','','L',0); 
		$pdf->Cell(20,5,'','R,B','','L',0); 
		$pdf->Cell(20,5,'','B,R','','L',0); 
		$pdf->Cell(35,5,'(PDV stopa 20%)','B,R','','L',0); 
		$pdf->Cell(20,5,'','B,R',1,'L',0);
	}
	//KRAJ TABELE


	$pdf->SetFont('DejaVuSerif','',9);
	$pdf->Cell(120,7,$txt_rok,0,'','L',0); 
	$pdf->Cell(43,7,'Ukupno bez PDV-a:',0,'','L',0); 
	$pdf->Cell(17,7,$ukupno_bez_pdva_f." RSD",0,'','R',0); $pdf->Ln();
	
	$pdf->Cell(120,7,'',0,'','C',0); 
	$pdf->Cell(43,7,'PDV 20%:',0,'','L',0); 
	$pdf->Cell(17,7,$pdv_iznos_f." RSD",0,'','R',0); $pdf->Ln();
	
	$pdf->Cell(120,7,'',0,'','C',0); 
	$pdf->Cell(43,7,'Ukupno sa PDV-om:',0,'','L',0); 
	$pdf->Cell(17,7,$ukupno_za_placanje_f." RSD",0,'','R',0); $pdf->Ln();
	
	$pdf->Cell(120,7,'',0,'','C',0); 
	$pdf->Cell(43,7,'Ukupno za plaćanje:','T','','L',0); 
	$pdf->Cell(17,7,$ukupno_za_placanje_f." RSD",'T','','R',0); $pdf->Ln();
	
	$pdf->SetFont('DejaVuSerif','',10);
	$pdf->Cell(0,7,'Molimo Vas da dati iznos Predračuna uplatite na naš bankovni račun: 220-0000000142731-57 '); $pdf->Ln();
	$pdf->Cell(0,7,'(Pro Credit Bank) i unapred se zahvaljujemo.'); $pdf->Ln();
	$pdf->Cell(0,7,'Napomena: Predračun je urađen na računaru i punovažan je u elektronskom obliku bez pečata i potpisa, u '); $pdf->Ln();
	$pdf->Cell(0,7,'skladu sa čl. 8 i 9 Zakona o računovodstvu.'); $pdf->Ln();
	$pdf->Cell(0,7,'Prilikom uplate, kod svrhe uplate obavezno navesti broj Predračuna.'); $pdf->Ln();
	$pdf->MultiCell(0,5,$txt_predracun); $pdf->Ln();
	
	// $pdf->Cell(100,7,'',0,'','C',0); 
	// $pdf->Cell(53,7,'EUR',0,'','L',0); 
	// $pdf->Cell(27,7,'€ '.$pr_vrijednost_EUR,0,'','R',0); $pdf->Ln();
	
	$filename="files/predracuni_dipl/".$pr_file."";
	// var_dump($filename);
	// exit();
	// return $pr_file;
	// $pdf->Output();
	$pdf->Output($filename,'F');
}

function createRucnoPredracunSRB($predracun_id, $datum_kreiranja){

	Global $db;
	Global $logged_employee_id;
	Global $vrsta_dokumenta;
	$vrsta_dokumenta = "predracun";
	
	$danas = date('d.m.Y', strtotime($datum_kreiranja));
	$rok_uplate = date('d.m.Y', strtotime($danas. ' + 10 days'));
	$get_predracun = $db->prepare("
					SELECT pr_broj_predracuna, nk.ime_nd_kandidata, nk.prezime_nd_kandidata, nk.ulica_nd_kandidata, nk.postanski_broj_nd_kandidata, nk.grad_nd_kandidata, pr_rata, nk.vrsta_ugovora_nd_kandidata, pr_vrijednost_RSD, pr_vrijednost_EUR, pr_file, pr_domaca_valuta, jmbg_nd_kandidata
					FROM idk_predracuni
					JOIN idk_nd_kandidata nk
					ON nk.id_broj_nd_kandidata = pr_kandidat_id
					WHERE pr_id = :pr_id
					");

	$get_predracun->execute(array(
						':pr_id' => $predracun_id));

	$predracun_row = $get_predracun->fetch();
	
	$broj_predracuna = $predracun_row["pr_broj_predracuna"];
	$ime = $predracun_row["ime_nd_kandidata"];
	$prezime = $predracun_row["prezime_nd_kandidata"];
	$ulica = $predracun_row["ulica_nd_kandidata"];
	$pbroj = $predracun_row["postanski_broj_nd_kandidata"];
	$grad = $predracun_row["grad_nd_kandidata"];
	$pr_rata = $predracun_row["pr_rata"];
	$pr_vrijednost_RSD = $predracun_row["pr_vrijednost_RSD"];
	$pr_vrijednost_EUR = $predracun_row["pr_vrijednost_EUR"];
	$pr_file = $predracun_row["pr_file"];
	$vrsta_ugovora = $predracun_row["vrsta_ugovora_nd_kandidata"];
	$pr_domaca_valuta = $predracun_row["pr_domaca_valuta"];
	$jmbg = $predracun_row["jmbg_nd_kandidata"];
	
	Global $valutaCheck;
	$valutaCheck = $pr_domaca_valuta;
	
	$ukupno_za_placanje = $pr_vrijednost_RSD;
	$ukupno_za_placanje_f = number_format($ukupno_za_placanje, 2, ',', '.');
	$pdv_stopa = 0.2;
	$ukupno_bez_pdva = $ukupno_za_placanje / (1 + $pdv_stopa);
	$ukupno_bez_pdva_f = number_format($ukupno_bez_pdva, 2, ',', '.');
	$pdv_iznos = $ukupno_za_placanje - $ukupno_bez_pdva;
	$pdv_iznos_f = number_format($pdv_iznos, 2, ',', '.');
	
	
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
		$cena = $ukupno_bez_pdva * (100 / (100 - $popust_procent));
	}else{
		$cena = 49166.67;
	}
	$cena_f = number_format($cena, 2, ',', '.');
	$popust = $cena - $ukupno_bez_pdva;
	$popust_f = number_format($popust, 2, ',', '.');
	$cijena = $iznos;
	$cijena_f = number_format($cijena, 2, ',', '.');
	
	switch($vrsta_ugovora){
		case 1: case 2: case 22: case 62: case 72: case 52: case 82: case 42:
			$broj_rata = 2;
		break;
		case 5: case 6: case 23: case 63: case 73: case 53: case 83: case 43:
			$broj_rata = 3;
		break;
		case 7: case 8: case 24: case 64: case 74: case 54: case 84: case 44:
			$broj_rata = 4;
		break;
		case 3: case 4: case 25: case 65: case 75: case 55: case 85: case 45:
			$broj_rata = 5;
		break;
		default:
			$broj_rata = 1;
	}
	switch($pr_rata){
		case 1:
			if($broj_rata == 1){
				$rata_txt = "iznos novčane naknade ";
				$txt_predracun = "Uplatom Predračuna prihvata se ponuda ugovora o pružanju usluge posredovanja u postupku nostrifikacije diplome.";
				$txt_rok = "Rok za plaćanje je 10 (deset) dana od dana izdavanja predračuna.";
			}else{
				$rata_txt = "I (prvu) ratu novčanog iznosa ";
				$txt_predracun = "Uplatom Predračuna prihvata se ponuda ugovora o pružanju usluge posredovanja u postupku nostrifikacije diplome.";
				$txt_rok = "Rok za plaćanje je 7 (sedam) dana od dana izdavanja predračuna.";
			}
		break;
		case 2:
			$rata_txt = "II (drugu) ratu novčanog iznosa ";
			$txt_predracun = "Uplatom predračuna prihvaćena je ponuda ugovora o pružanju usluge posredovanja u postupku nostrifikacije diplome.";
			$txt_rok = "";
		break;
		case 3:
			$rata_txt = "III (treću) ratu novčanog iznosa ";
			$txt_predracun = "Uplatom predračuna prihvaćena je ponuda ugovora o pružanju usluge posredovanja u postupku nostrifikacije diplome.";
			$txt_rok = "";
		break;
		case 4:
			$rata_txt = "IV (četvrtu) ratu novčanog iznosa ";
			$txt_predracun = "Uplatom predračuna prihvaćena je ponuda ugovora o pružanju usluge posredovanja u postupku nostrifikacije diplome.";
			$txt_rok = "";
		break;
		case 5:
			$rata_txt = "V (petu) ratu novčanog iznosa ";
			$txt_predracun = "Uplatom predračuna prihvaćena je ponuda ugovora o pružanju usluge posredovanja u postupku nostrifikacije diplome.";
			$txt_rok = "";
		break;
		default:
			$rata_txt = "";
			$txt_predracun = "";
			$txt_rok = "";
	}
	
	$adresa = $ulica.", ".$pbroj." ".$grad;
	
	$pdf = new PDF('P','mm','A4');
	

	$pdf->AliasNbPages();
	$pdf->SetDisplayMode('real', 'single');

	$pdf->AddPage();
	
	$pdf->SetFont('DejaVuSerif','',9);
	$pdf->Cell(180,10,"Izdavalac predračuna: Jobstep International d.o.o.",'',0,'',0); $pdf->Ln();
	$pdf->Cell(180,10,"Adresa: Bulevar Mihajla Pupina 165 G, 11070 Novi Beograd, Srbija",'',0,'',0); $pdf->Ln();
	$pdf->Cell(180,10,"Matični broj: 21569143      PIB: 111915414",'',0,'',0); $pdf->Ln();
	
	$pdf->Cell(50,7,"Primalac predračuna:",'T',0,'',0); 
	$pdf->Cell(130,7,$ime." ".$prezime,'T',0,'',0); 
	$pdf->Ln();
	$pdf->Cell(50,7,"Adresa:",'',0,'',0); 
	$pdf->Cell(130,7,$adresa,'',0,'',0); 
	$pdf->Ln();
	$pdf->Cell(50,7,"JMBG:",'B',0,'',0); 
	$pdf->Cell(130,7,$jmbg,'B',0,'',0); 
	$pdf->Ln(14);
	
	$pdf->SetFont('DejaVuSerif','',10);
	$pdf->Cell(180,7,'Mesto i datum izdavanja predračuna: Beograd, '.$danas.". godine",0,0,'',0);
	$pdf->Ln();
	$pdf->Cell(180,7,'Redni broj predračuna: '.$broj_predracuna,0,0,'',0); 
	$pdf->Ln();
	$pdf->Cell(180,7,'Predračun broj: '.$broj_predracuna,0,0,'C',0);
	$pdf->Ln();	

	$pdf->SetFont('DejaVuSerif','',10);
	$pdf->Cell(0,7,'Poštovani/-a, molimo Vas da nam avansno uplatite '.$rata_txt.'na ime pružanja'); $pdf->Ln();
	$pdf->Cell(0,7,'usluge posredovanja u postupku nostrifikacije diplome, i shodno tome Vam ispostavljamo predračun: '); $pdf->Ln(12);
	

	//POCETAK TABELE
	if($popust_part){
		$pdf->Cell(12,5,'Redni','T,L,R','','L',0); 
		$pdf->Cell(42,5,'Vrsta usluge:','T,R','','L',0); 
		$pdf->Cell(15,5,'Količina','T,R','','L',0); 
		$pdf->Cell(19,5,'Cena:','T,R','','L',0); 
		$pdf->Cell(23,5,'Popust:','T,R','','L',0); 
		$pdf->Cell(19,5,'Ukupno','T,R','','L',0); 
		$pdf->Cell(31,5,'PDV:','T,R','','L',0); 
		$pdf->Cell(19,5,'Ukupno','T,R','','L',0); 
		
		$pdf->Ln();	
		$pdf->Cell(12,5,'broj:','L,R','','L',0); 
		$pdf->Cell(42,5,'','R','','L',0); 
		$pdf->Cell(15,5,'(obim)','R','','L',0); 
		$pdf->Cell(19,5,'','R','','L',0); 
		$pdf->Cell(23,5,'','R','','L',0); 
		$pdf->Cell(19,5,'bez','R','','L',0); 
		$pdf->Cell(31,5,'','R','','L',0); 
		$pdf->Cell(19,5,'sa PDV-','R','','L',0);
		
		$pdf->Ln();	
		$pdf->Cell(12,5,'','L,B,R','','L',0); 
		$pdf->Cell(42,5,'','B,R','','L',0); 
		$pdf->Cell(15,5,'usluge:','R,B','','L',0); 
		$pdf->Cell(19,5,'','R,B','','L',0); 
		$pdf->Cell(23,5,'','R,B','','L',0); 
		$pdf->Cell(19,5,'PDV-a:','B,R','','L',0); 
		$pdf->Cell(31,5,'','B,R','','L',0); 
		$pdf->Cell(19,5,'om:','B,R','','L',0);
		
		$pdf->Ln();	
		$pdf->Cell(12,5,'1.','T,L,R','','L',0); 
		$pdf->Cell(42,5,'Posredovanje u postupku','T,R','','L',0); 
		$pdf->Cell(15,5,'1,00','T,R','','L',0);
		$pdf->Cell(19,5,$cena_f,'T,R','','L',0); 
		$pdf->Cell(23,5,$popust_f,'T,R','','L',0); 
		$pdf->Cell(19,5,$ukupno_bez_pdva_f,'T,R','','L',0); 
		$pdf->Cell(31,5,$pdv_iznos_f,'T,R','','L',0); 
		$pdf->Cell(19,5,$ukupno_za_placanje_f,'T,R','','L',0); 
		
		$pdf->Ln();	
		$pdf->Cell(12,5,'','L,B,R','','L',0); 
		$pdf->Cell(42,5,'nostrifikacije diplome','B,R','','L',0); 
		$pdf->Cell(15,5,'','R,B','','L',0); 
		$pdf->Cell(19,5,'','R,B','','L',0); 
		$pdf->Cell(23,5,'(popust '.$popust_procent.'%)','R,B','','L',0); 
		$pdf->Cell(19,5,'','B,R','','L',0); 
		$pdf->Cell(31,5,'(PDV stopa 20%)','B,R','','L',0); 
		$pdf->Cell(19,5,'','B,R',1,'L',0);
	}else{
		$pdf->Cell(15,5,'Redni','T,L,R','','L',0); 
		$pdf->Cell(50,5,'Vrsta usluge:','T,R','','L',0); 
		$pdf->Cell(20,5,'Količina','T,R','','L',0); 
		$pdf->Cell(20,5,'Cena:','T,R','','L',0); 
		$pdf->Cell(20,5,'Ukupno','T,R','','L',0); 
		$pdf->Cell(35,5,'PDV:','T,R','','L',0); 
		$pdf->Cell(20,5,'Ukupno','T,R','','L',0); 
		
		$pdf->Ln();	
		$pdf->Cell(15,5,'broj:','L,R','','L',0); 
		$pdf->Cell(50,5,'','R','','L',0); 
		$pdf->Cell(20,5,'(obim)','R','','L',0); 
		$pdf->Cell(20,5,'','R','','L',0); 
		$pdf->Cell(20,5,'bez','R','','L',0); 
		$pdf->Cell(35,5,'','R','','L',0); 
		$pdf->Cell(20,5,'sa PDV-','R','','L',0);
		
		$pdf->Ln();	
		$pdf->Cell(15,5,'','L,B,R','','L',0); 
		$pdf->Cell(50,5,'','B,R','','L',0); 
		$pdf->Cell(20,5,'usluge:','R,B','','L',0); 
		$pdf->Cell(20,5,'','R,B','','L',0); 
		$pdf->Cell(20,5,'PDV-a:','B,R','','L',0); 
		$pdf->Cell(35,5,'','B,R','','L',0); 
		$pdf->Cell(20,5,'om:','B,R','','L',0);
		
		$pdf->Ln();	
		$pdf->Cell(15,5,'1.','T,L,R','','L',0); 
		$pdf->Cell(50,5,'Posredovanje u postupku','T,R','','L',0); 
		$pdf->Cell(20,5,'1,00','T,R','','L',0); 
		$pdf->Cell(20,5,$cena_f,'T,R','','L',0); 
		$pdf->Cell(20,5,$ukupno_bez_pdva_f,'T,R','','L',0); 
		$pdf->Cell(35,5,$pdv_iznos_f,'T,R','','L',0); 
		$pdf->Cell(20,5,$ukupno_za_placanje_f,'T,R','','L',0); 
		
		$pdf->Ln();	
		$pdf->Cell(15,5,'','L,B,R','','L',0); 
		$pdf->Cell(50,5,'nostrifikacije diplome','B,R','','L',0); 
		$pdf->Cell(20,5,'','R,B','','L',0); 
		$pdf->Cell(20,5,'','R,B','','L',0); 
		$pdf->Cell(20,5,'','B,R','','L',0); 
		$pdf->Cell(35,5,'(PDV stopa 20%)','B,R','','L',0); 
		$pdf->Cell(20,5,'','B,R',1,'L',0);
	}
	//KRAJ TABELE


	$pdf->SetFont('DejaVuSerif','',9);
	$pdf->Cell(120,7,$txt_rok,0,'','L',0); 
	$pdf->Cell(43,7,'Ukupno bez PDV-a:',0,'','L',0); 
	$pdf->Cell(17,7,$ukupno_bez_pdva_f." RSD",0,'','R',0); $pdf->Ln();
	
	$pdf->Cell(120,7,'',0,'','C',0); 
	$pdf->Cell(43,7,'PDV 20%:',0,'','L',0); 
	$pdf->Cell(17,7,$pdv_iznos_f." RSD",0,'','R',0); $pdf->Ln();
	
	$pdf->Cell(120,7,'',0,'','C',0); 
	$pdf->Cell(43,7,'Ukupno sa PDV-om:',0,'','L',0); 
	$pdf->Cell(17,7,$ukupno_za_placanje_f." RSD",0,'','R',0); $pdf->Ln();
	
	$pdf->Cell(120,7,'',0,'','C',0); 
	$pdf->Cell(43,7,'Ukupno za plaćanje:','T','','L',0); 
	$pdf->Cell(17,7,$ukupno_za_placanje_f." RSD",'T','','R',0); $pdf->Ln();
	
	$pdf->SetFont('DejaVuSerif','',10);
	$pdf->Cell(0,7,'Molimo Vas da dati iznos Predračuna uplatite na naš bankovni račun: 220-0000000142731-57 '); $pdf->Ln();
	$pdf->Cell(0,7,'(Pro Credit Bank) i unapred se zahvaljujemo.'); $pdf->Ln();
	$pdf->Cell(0,7,'Napomena: Predračun je urađen na računaru i punovažan je u elektronskom obliku bez pečata i potpisa, u '); $pdf->Ln();
	$pdf->Cell(0,7,'skladu sa čl. 8 i 9 Zakona o računovodstvu.'); $pdf->Ln();
	$pdf->Cell(0,7,'Prilikom uplate, kod svrhe uplate obavezno navesti broj Predračuna.'); $pdf->Ln();
	$pdf->MultiCell(0,5,$txt_predracun); $pdf->Ln();
	
	// $pdf->Cell(100,7,'',0,'','C',0); 
	// $pdf->Cell(53,7,'EUR',0,'','L',0); 
	// $pdf->Cell(27,7,'€ '.$pr_vrijednost_EUR,0,'','R',0); $pdf->Ln();
	
	$filename="files/predracuni_dipl/".$pr_file."";
	// var_dump($filename);
	// exit();
	
	//$pdf->Output();
	$pdf->Output($filename,'F');
}

function createUgovorBIH($kandidat_id){
	Global $db;
	Global $logged_employee_id;
	Global $vrsta_dokumenta;
	$vrsta_dokumenta = "";
	$danas = date('d.m.Y');
	$datetime = date('Y-m-d H:i:s');
	$date_ispis = date('d.m.Y');
	$mjesec = date('m');
	$godina = date('y');
	$day = date('d');
	if($day > 28)
		$day = 1;
	//pokupiti info iz baze
	$get_info = $db->prepare("
					SELECT ime_nd_kandidata, prezime_nd_kandidata, ulica_nd_kandidata, postanski_broj_nd_kandidata, grad_nd_kandidata, vrsta_ugovora_nd_kandidata, jmbg_nd_kandidata, broj_licne_karte_nd_kandidata
					FROM idk_nd_kandidata
					WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
					");

	$get_info->execute(array(
						':id_broj_nd_kandidata' => $kandidat_id));

	$info_row = $get_info->fetch();
	
	$prezime = $info_row["prezime_nd_kandidata"];
	$ime = $info_row["ime_nd_kandidata"];
	$grad = $info_row["grad_nd_kandidata"];
	$ulica = $info_row["ulica_nd_kandidata"];
	$vrsta_ugovora = $info_row["vrsta_ugovora_nd_kandidata"];
	$jmbg = $info_row["jmbg_nd_kandidata"];
	$licna = $info_row["broj_licne_karte_nd_kandidata"];
	
	$txt_iznos_bez_p = "950,00 KM (slovima:devestotinapedeset i 00/100 KM)";
	
	if($vrsta_ugovora == 1 OR $vrsta_ugovora == 2 OR $vrsta_ugovora == 22 OR $vrsta_ugovora == 52 OR $vrsta_ugovora == 62 OR $vrsta_ugovora == 72 OR $vrsta_ugovora == 82 OR $vrsta_ugovora == 42){
		$c7_txt1 = "primljeni iznos";
		$c7_txt2 = "drugu ratu";
		$c7_txt3 = "drugu ratu";
	}else{
		$c7_txt1 = "do tada uplaćene rate";
		$c7_txt2 = "preostale rate";
		$c7_txt3 = "preostale, neplaćene rate";
	}
	
	$brojac_ugovora = createBrojUgovora("BiH");
	$ugovor_putanja = "UGB-".$brojac_ugovora."-".$mjesec."-".$godina.".pdf";
	
	$txt_broj_ugovora = $brojac_ugovora."-".$mjesec."/".$godina;
	//INSERT UGOVORA
	$insert_ugovor = $db->prepare("	
				INSERT INTO idk_nd_kandidata_dokumenti	
				(naziv_dokument_nd,  naziv_dokument_ostali_nd, id_kandidata_dokument_nd, vrijeme_dodavanja_dokument_nd, dodao_zaposlenik_dokument_nd, tip_dokumenta)	
				VALUES	
				(:naziv_dokument_nd,:naziv_dokument_ostali_nd,:id_kandidata_dokument_nd,:vrijeme_dodavanja_dokument_nd,:dodao_zaposlenik_dokument_nd,:tip_dokumenta)	
				");	
	$insert_ugovor->execute(array(	
				':naziv_dokument_nd' => $ugovor_putanja,	
				':naziv_dokument_ostali_nd' => "ugovor",	
				':id_kandidata_dokument_nd' => $kandidat_id,	
				':vrijeme_dodavanja_dokument_nd' => $datetime,	
				':dodao_zaposlenik_dokument_nd' => $logged_employee_id,	
				':tip_dokumenta' => 1
				));

	// Header
	$txt_head_1 = "„Jobstep International“ d.o.o Bihać ul. Hamze Hume bb-Poslovna zona Kombiteks,JIB: 4263788850005 zastupan po direktoru  Nanić Emiru (u daljnjem tekstu: nalogoprimac), ";
	$txt_head_2 = "i  ".$prezime." ".$ime." iz ".$grad." ul.  ".$ulica.", JMB: ".$jmbg.", broj LK: ".$licna." (u daljnjem tekstu: nalogodavac), s druge strane";
	$txt_head_3 = "sklapaju ovaj";

	$txt_naslov_1 = "UGOVOR";	
	$txt_naslov_2 = "O OBRADI PODATAKA U VEZI SA";	
	$txt_naslov_3 = "NOSTRIFIKACIJOM DIPLOMA";	

	// CLAN 1
	$c1_tacka1 = "Predmet ovog ugovora  između ugovornih strana je obavljanje usluge obrade podataka  u vezi sa nostrifikacijom diploma u zemljama Europske unije, pretežito njemačkog govornog područja.";
	
	// CLAN 2
	$c2_tacka1 =  "Pod obavljanjem usluga obrade podataka u vezi sa nostrifikacijom diploma u smislu člana 1. ovog ugovora podrazumijeva se da se ovim ugovorom nalogoprimac obavezuje da  nalogodavca uputi u sljedeće:";
	$c2_tacka2 = "     -  Neophodna dokumentacija potrebna za nostrifikaciju diplome";
	$c2_tacka3 = "     -  Prevod dostavljene dokumentacije na njemački jezik";
	$c2_tacka4 = "     -  Provođenje kompletnog postupka do nadležne ustanove";
	$c2_tacka5 = "Neophodna dokumentacija će biti definisana od strane  Ustanove nadležne za nostrifikaciju i biti će sastavni dio ovog ugovora.";
	
	// CLAN 3
	$c3_tacka1 = "Za navedene poslove iz člana  2. ovog ugovora nalogodavac se obavezuje platiti naknadu u iznosu od ".$txt_iznos_bez_p;
	
	$c3_tacka2_vrsta1 = "Avans u iznosu od 475,00  KM (slovima:četiristotinesedamdesetpet i 00/100 KM) se plaća prilikom potpisivanja ugovora o obradi podataka u vezi sa nostrifikacijom diploma sa nalogoprimcom, a ostatak novca se plaća ODMAH po okončanju postupka nostrifikacije diplome bez obzira na ishod postupka.";
	$c3_tacka2_vrsta2 = "Avans u iznosu od 475,00  KM (slovima:četiristotinesedamdesetpet i 00/100 KM) se plaća prilikom potpisivanja ugovora o obradi podataka u vezi sa nostrifikacijom diploma sa nalogoprimcom, a ostatak novca se plaća ODMAH po okončanju postupka nostrifikacije diplome bez obzira na ishod postupka.";
	
	$c3_tacka2_vrsta9 = "Nalogodavac je dužan da plati cjelokupan iznos naknade naveden u stavu 1. ovog člana prilikom potpisivanja ugovora o obradi podataka u vezi sa nostrifikacijom diploma sa nalogoprimcom.";
	$c3_popusti = "Eventualni popusti na ugovoreni iznos naknade odobravaju se na osnovu  Pravilnika o uvjetima i načinu formiranja cijena usluga i odluke o odobravanju popusta.";
	
	//$c3_tacka2_vrsta2 = "Avans u iznosu od 332,5 KM (slovima:tristotinetridesetdvije i 50/100 KM) se plaća prilikom potpisivanja ugovora o obradi podataka u vezi sa nostrifikacijom diploma sa nalogoprimcom, a ostatak novca se plaća ODMAH po okončanju postupka nostrifikacije diplome bez obzira na ishod postupka.";
	
	$c3_tacka2_vrsta5 = "Naknada će se plaćati u 3 mjesečne rate kako slijedi:";
	$c3_tacka3_vrsta5 = "     1.  Iznos od 350,00 KM (slovima: tristotinepedeset i 00/100 KM) plaća se odmah po";
	$c3_tacka4_vrsta5 = "          potpisu ovog Ugovora";
	$c3_tacka5_vrsta5 = "     2.  Iznos od 600,00 KM (slovima: šeststotina i 00/100 KM) će se plaćati u dvije mjesečne";
	$c3_tacka6_vrsta5 = "          rate u iznosu od 300,00 KM (slovima: tristotine i 00/100 KM) koje dospijevaju na";
	$c3_tacka7_vrsta5 = "          naplatu do ".$day."-og u mjesecu.";
	
	$c3_tacka2_vrsta6 = "Naknada će se plaćati u 3 mjesečne rate kako slijedi:";
	$c3_tacka3_vrsta6 = "     1.  Iznos od 225,00 KM (slovima: dvijestotinedvadesetpett i 00/100 KM) plaća se odmah";
	$c3_tacka4_vrsta6 = "          po potpisu ovog Ugovora";
	$c3_tacka5_vrsta6 = "     2.  Iznos od 440,00 KM (slovima: četiristotinečetrdeset i 00/100 KM) će se plaćati u dvije";
	$c3_tacka6_vrsta6 = "          mjesečne rate u iznosu od 220,00 KM (slovima: dvijestotinedvadeset i 00/100 KM) koje";
	$c3_tacka7_vrsta6 = "          dospijevaju na naplatu do ".$day."-og u mjesecu.";
	
	$c3_tacka2_vrsta7 = "Naknada će se plaćati u 4 mjesečne rate kako slijedi:";
	$c3_tacka3_vrsta7 = "     1.  Iznos od 260,00 KM (slovima: dvijestotinešezdeset i 00/100 KM) plaća se odmah po";
	$c3_tacka4_vrsta7 = "          potpisu ovog Ugovora";
	$c3_tacka5_vrsta7 = "     2.  Iznos od 690,00 KM (slovima: šeststotinadevedeset i 00/100 KM) će se plaćati u tri ";
	$c3_tacka6_vrsta7 = "          rate u iznosu od 230,00 KM (slovima: dvijestotinetrideset i 00/100 KM) koje dospijevaju";
	$c3_tacka7_vrsta7 = "          na naplatu do ".$day."-og u mjesecu.";
	
	$c3_tacka2_vrsta8 = "Naknada će se plaćati u 4 mjesečne rate kako slijedi:";
	$c3_tacka3_vrsta8 = "     1.  Iznos od 185,00 KM (slovima: jednastotinaosamdesetpet i 00/100 KM) plaća se odmah";
	$c3_tacka4_vrsta8 = "          po potpisu ovog Ugovora";
	$c3_tacka5_vrsta8 = "     2.  Iznos od 480,00 KM (slovima: četiristotineosamdeset i 00/100 KM) će se plaćati u tri";
	$c3_tacka6_vrsta8 = "          mjesečne rate u iznosu od 160,00 KM (slovima: jednastotinašezdeset i 00/100 KM) koje";
	$c3_tacka7_vrsta8 = "          dospijevaju na naplatu do ".$day."-og u mjesecu.";
	
	$c3_tacka2_vrsta3 = "Naknada će se plaćati u 5 mjesečnih rata kako slijedi:";
	$c3_tacka3_vrsta3 = "     1.  Iznos od 190,00 KM (slovima: jednastotinadevedeset i 00/100 KM) plaća se odmah";
	$c3_tacka4_vrsta3 = "          po potpisu ovog Ugovora";
	$c3_tacka5_vrsta3 = "     2.  Iznos od 760,00 KM (slovima: sedamstotinašezdeset i 00/100 KM) će se plaćati u četiri";
	$c3_tacka6_vrsta3 = "          mjesečne rate u iznosu od 190,00 KM (slovima: jednastotinadevedeset i 00/100 KM)";
	$c3_tacka7_vrsta3 = "          koje dospijevaju na naplatu do ".$day."-og u mjesecu.";
	
	$c3_tacka2_vrsta4 = "Naknada će se plaćati u 5 mjesečnih rata kako slijedi:";
	$c3_tacka3_vrsta4 = "     1.  Iznos od 165,00 KM (slovima: jednastotinašezdesetpet i 00/100 KM) plaća se odmah";
	$c3_tacka4_vrsta4 = "          po potpisu ovog Ugovora";
	$c3_tacka5_vrsta4 = "     2.  Iznos od 500,00 KM (slovima: petstotina i 00/100 KM) će se plaćati u četiri mjesečne";
	$c3_tacka6_vrsta4 = "          rate u iznosu od 125,00 KM (slovima:jednastotinadvadesetpet i 00/100 KM) koje";
	$c3_tacka7_vrsta4 = "          dospijevaju na naplatu do ".$day."-og u mjesecu.";
	
	$c3_zadnja_tacka = "Kompletan iznos naknade nalogodavac mora platiti najkasnije po okončanju postupka nostrifikacije diplome bez obzira na ishod postupka.";
	
	// CLAN 4
	$c4_tacka1 = "Troškovi pribavljanja neophodne dokumentacije (takse, naknade i ovjera original dokumentacije), kao i trošak nostrifikacije diplome nisu uključeni u cijenu navedenu u članu 3.";
	$c4_tacka2 = "Sve neophodne dokumente dužan je pribaviti nalogodavac o svom trošku.";
	$c4_tacka3 = "Trošak takse nostrifikacije diplome dužan je snositi nalogodavac u cijelosti.";
	
	// CLAN 5
	$c5_tacka1 = "Obaveza nalogoprimca prema nalogodavcu prestaje danom okončanja postupka nostrifikacije, neovisno od ishoda postupka.";
	$c5_tacka2 = "Obaveza nalogodavca prema nalogoprimcu prestaje danom izmirenja ukupne cijene usluge obrade podataka u vezi sa nostrifikacijom diploma.";
	
	// CLAN 6
	$c6_tacka1 = "Nalogoprimac može odustati od ugovora jednostranom izjavom volje i bez povrata uplaćenih sredstava u sljedećim situacijama:";
	$c6_tacka2 = "     -  Ukoliko nalogodavac u roku od tri mjeseca od dana potpisivanja ovog ugovora ne dostavi";
	$c6_tacka3 = "        kompletnu dokumentaciju neophodnu za nostrifikaciju diplome.";
	$c6_tacka4 = "     -  Ukoliko se ustanovi da su dostavljene informacije i dokumenti od strane nalogodavca";
	$c6_tacka5 = "        falsifikati.";
	$c6_tacka6 = "Nalogodavac je obavezan izmiriti preostali dio naknade navedene u članu 3 u roku od 7 (sedam) dana od dana prijema obavijesti o raskidu ugovora.";
	$c6_tacka7 = "Nalogodavac je također obavezan da nalogoprimcu nadoknadi štetu nastalu dostavljanjem netačnih, neispravnih i falsifikovanih dokumenata i informacija.";
	
	// CLAN 7
	$c7_tacka1 = "Ako nalogodavac nakon što je pokrenut postupak nostrifikacije diplome odustane svojom voljom od postupka nostrifikacije diplome neposredno nakon pokretanja istog, nalogoprimac nije dužan da mu vrati primljeni avans i nalogodavac je dužan da izmiri preostali, neizmireni iznos naknade iz člana 3 ovog Ugovora.";
	$c7_tacka2 = "Pod pokretanjem postupka nostrifikacije smatra se uplata cijelog ili djelimičnog iznosa naknade iz člana 3. ovog ugovora.";
	// $c7_tacka3 = "Ukoliko nalogodavac odustane od obrade podataka prije pokretanja postupka nostrifikacije, odnosno prije slanja zahtjeva nadležnoj ustanovi nije obavezan plaćati ".$c7_txt2." naknade navedene u članu 3. ovog Ugovora.";
	// $c7_tacka4 = "Ukoliko nalogodavac odustane od Ugovora nakon pokretanja postupka nostrifikacije, odnosno nakon slanja zahtjeva za nostrifikaciju dužan je izmiriti i ".$c7_txt3." naknade navedene u članu 3. ovog Ugovora.";
	
	// CLAN 8
	$c8_tacka1 = "Sve eventualne sporove stranke će rješavati sporazumno.";
	$c8_tacka2 = "Ukoliko nije moguće rješenje prema stavu 1. ovog člana nadležan će biti stvarno nadležni sud u Bihaću.";
	
	// CLAN 9
	$c9_tacka1 = "Ovaj ugovor sastavljen je u 4 (slovima: četiri ) istovjetna primjerka, od kojih po 2 (slovima: dva) pripadaju svakoj od ugovornih strana.";
	
	// CLAN 10
	$c10_tacka1 = "Ovaj ugovor stupa na snagu danom potpisivanja obiju stranaka.";
	
	
	$pdf = new tFPDF();
	$pdf->AddPage();

	// Add a Unicode font (uses UTF-9)
	$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
	$pdf->AddFont('DejaVu','B','DejaVuSansCondensed-Bold.ttf',true);
	$pdf->AddFont('DejaVuSerif','','DejaVuSerifCondensed.ttf',true);
	$pdf->AddFont('DejaVuSerif','B','DejaVuSerifCondensed-Bold.ttf',true);
	$pdf->AddFont('DejaVuSerif','BI','DejaVuSerifCondensed-BoldItalic.ttf',true);

	$pdf->SetLeftMargin(20);
	$pdf->Ln(20);
	$pdf->SetFont('DejaVuSerif','',11);
	
	$pdf->MultiCell(170,5,''.$txt_head_1.'','','J',0);
	$pdf->Ln(3);
	$pdf->MultiCell(170,5,''.$txt_head_2.'','','J',0);
	$pdf->Ln(3);
	$pdf->MultiCell(170,5,''.$txt_head_3.'','','J',0);
	$pdf->Ln(5);
	
	$pdf->SetFont('DejaVuSerif','BI',12);
	$pdf->Cell(170,5,''.$txt_naslov_1.'','',1,'C',0);
	$pdf->Cell(170,5,''.$txt_naslov_2.'','',1,'C',0);
	$pdf->Cell(170,5,''.$txt_naslov_3.'','',1,'C',0);
	$pdf->Ln(15);
	
	//CLAN 1
	$pdf->SetFont('DejaVuSerif','BI',12);
	$pdf->MultiCell(170,5,'Član 1.','','C',0);
	
	$pdf->SetFont('DejaVuSerif','',11);
	$pdf->MultiCell(170,5,''.$c1_tacka1.'','','J',0);
	$pdf->Ln(10);
	
	//CLAN 2
	$pdf->SetFont('DejaVuSerif','BI',12);
	$pdf->MultiCell(170,5,'Član 2.','','C',0);
	
	$pdf->SetFont('DejaVuSerif','',11);
	$pdf->MultiCell(170,5,''.$c2_tacka1.'','','J',0);
	$pdf->MultiCell(170,5,''.$c2_tacka2.'','','J',0);
	$pdf->MultiCell(170,5,''.$c2_tacka3.'','','J',0);
	$pdf->MultiCell(170,5,''.$c2_tacka4.'','','J',0);
	$pdf->MultiCell(170,5,''.$c2_tacka5.'','','J',0);
	$pdf->Ln(10);

	//CLAN 3
	$pdf->SetFont('DejaVuSerif','BI',12);
	$pdf->MultiCell(170,5,'Član 3.','','C',0);
	
	$pdf->SetFont('DejaVuSerif','',11);
	$pdf->MultiCell(170,5,''.$c3_tacka1.'','','J',0);
	
	switch($vrsta_ugovora){
		case 1: case 2: case 22: case 52: case 62: case 72: case 82: case 42:
			$pdf->MultiCell(170,5,''.$c3_tacka2_vrsta1.'','','J',0);
		break;
		case 3: case 4: case 25: case 55: case 65: case 75: case 85: case 45:
			$pdf->MultiCell(170,5,''.$c3_tacka2_vrsta3.'','','L',0);
			$pdf->MultiCell(170,5,''.$c3_tacka3_vrsta3.'','','L',0);
			$pdf->MultiCell(170,5,''.$c3_tacka4_vrsta3.'','','L',0);
			$pdf->MultiCell(170,5,''.$c3_tacka5_vrsta3.'','','L',0);
			$pdf->MultiCell(170,5,''.$c3_tacka6_vrsta3.'','','L',0);
			$pdf->MultiCell(170,5,''.$c3_tacka7_vrsta3.'','','L',0);
			$pdf->MultiCell(170,5,''.$c3_zadnja_tacka.'','','J',0);
		break;
		case 5: case 6: case 23: case 53: case 63: case 73: case 83: case 43:
			$pdf->MultiCell(170,5,''.$c3_tacka2_vrsta5.'','','L',0);
			$pdf->MultiCell(170,5,''.$c3_tacka3_vrsta5.'','','L',0);
			$pdf->MultiCell(170,5,''.$c3_tacka4_vrsta5.'','','L',0);
			$pdf->MultiCell(170,5,''.$c3_tacka5_vrsta5.'','','L',0);
			$pdf->MultiCell(170,5,''.$c3_tacka6_vrsta5.'','','L',0);
			$pdf->MultiCell(170,5,''.$c3_tacka7_vrsta5.'','','L',0);
			$pdf->MultiCell(170,5,''.$c3_zadnja_tacka.'','','J',0);
		break;
		case 7: case 8: case 24: case 54: case 64: case 74: case 84: case 44:
			$pdf->MultiCell(170,5,''.$c3_tacka2_vrsta7.'','','L',0);
			$pdf->MultiCell(170,5,''.$c3_tacka3_vrsta7.'','','L',0);
			$pdf->MultiCell(170,5,''.$c3_tacka4_vrsta7.'','','L',0);
			$pdf->MultiCell(170,5,''.$c3_tacka5_vrsta7.'','','L',0);
			$pdf->MultiCell(170,5,''.$c3_tacka6_vrsta7.'','','L',0);
			$pdf->MultiCell(170,5,''.$c3_tacka7_vrsta7.'','','L',0);
			$pdf->MultiCell(170,5,''.$c3_zadnja_tacka.'','','J',0);
		break;
		default: 
			$pdf->MultiCell(170,5,''.$c3_tacka2_vrsta9.'','','L',0);
	}
	/*
	if($vrsta_ugovora == 1){
		$pdf->MultiCell(170,5,''.$c3_tacka2_vrsta1.'','','J',0);
	}elseif($vrsta_ugovora == 2){
		$pdf->MultiCell(170,5,''.$c3_tacka2_vrsta2.'','','J',0);
	}elseif($vrsta_ugovora == 3){
		$pdf->MultiCell(170,5,''.$c3_tacka2_vrsta3.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka3_vrsta3.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka4_vrsta3.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka5_vrsta3.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka6_vrsta3.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka7_vrsta3.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_zadnja_tacka.'','','J',0);
	}elseif($vrsta_ugovora == 4){
		$pdf->MultiCell(170,5,''.$c3_tacka2_vrsta4.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka3_vrsta4.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka4_vrsta4.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka5_vrsta4.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka6_vrsta4.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka7_vrsta4.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_zadnja_tacka.'','','J',0);
	}elseif($vrsta_ugovora == 5){
		$pdf->MultiCell(170,5,''.$c3_tacka2_vrsta5.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka3_vrsta5.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka4_vrsta5.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka5_vrsta5.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka6_vrsta5.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka7_vrsta5.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_zadnja_tacka.'','','J',0);
	}elseif($vrsta_ugovora == 6){
		$pdf->MultiCell(170,5,''.$c3_tacka2_vrsta6.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka3_vrsta6.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka4_vrsta6.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka5_vrsta6.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka6_vrsta6.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka7_vrsta6.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_zadnja_tacka.'','','J',0);
	}elseif($vrsta_ugovora == 7){
		$pdf->MultiCell(170,5,''.$c3_tacka2_vrsta7.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka3_vrsta7.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka4_vrsta7.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka5_vrsta7.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka6_vrsta7.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka7_vrsta7.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_zadnja_tacka.'','','J',0);
	}elseif($vrsta_ugovora == 8){
		$pdf->MultiCell(170,5,''.$c3_tacka2_vrsta8.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka3_vrsta8.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka4_vrsta8.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka5_vrsta8.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka6_vrsta8.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_tacka7_vrsta8.'','','L',0);
		$pdf->MultiCell(170,5,''.$c3_zadnja_tacka.'','','J',0);
	}else{
		$pdf->MultiCell(170,5,''.$c3_tacka2_vrsta9.'','','L',0);
	}
	*/
	$pdf->MultiCell(170,5,''.$c3_popusti.'','','L',0);
	$pdf->Ln(10);
	$pdf->AddPage();
	//CLAN 4
	$pdf->Ln(10);
	$pdf->SetFont('DejaVuSerif','BI',12);
	$pdf->MultiCell(170,5,'Član 4.','','C',0);
	
	$pdf->SetFont('DejaVuSerif','',11);
	$pdf->MultiCell(170,5,''.$c4_tacka1.'','','J',0);
	$pdf->MultiCell(170,5,''.$c4_tacka2.'','','J',0);
	$pdf->MultiCell(170,5,''.$c4_tacka3.'','','J',0);
	$pdf->Ln(10);
	
	
	//CLAN 5
	$pdf->SetFont('DejaVuSerif','BI',12);
	$pdf->MultiCell(170,5,'Član 5.','','C',0);
	
	$pdf->SetFont('DejaVuSerif','',11);
	$pdf->MultiCell(170,5,''.$c5_tacka1.'','','J',0);
	$pdf->MultiCell(170,5,''.$c5_tacka2.'','','J',0);
	$pdf->Ln(10);
	
	//CLAN 6
	$pdf->SetFont('DejaVuSerif','BI',12);
	$pdf->MultiCell(170,5,'Član 6.','','C',0);
	
	$pdf->SetFont('DejaVuSerif','',11);
	$pdf->MultiCell(170,5,''.$c6_tacka1.'','','J',0);
	$pdf->MultiCell(170,5,''.$c6_tacka2.'','','L',0);
	$pdf->MultiCell(170,5,''.$c6_tacka3.'','','L',0);
	$pdf->MultiCell(170,5,''.$c6_tacka4.'','','L',0);
	$pdf->MultiCell(170,5,''.$c6_tacka5.'','','L',0);
	$pdf->MultiCell(170,5,''.$c6_tacka6.'','','L',0);
	$pdf->MultiCell(170,5,''.$c6_tacka7.'','','L',0);
	$pdf->Ln(10);
	
	//CLAN 7
	$pdf->SetFont('DejaVuSerif','BI',12);
	$pdf->MultiCell(170,5,'Član 7.','','C',0);
	
	$pdf->SetFont('DejaVuSerif','',11);
	$pdf->MultiCell(170,5,''.$c7_tacka1.'','','J',0);
	$pdf->MultiCell(170,5,''.$c7_tacka2.'','','J',0);
	// if($vrsta_ugovora == 9 OR $vrsta_ugovora == 10 OR $vrsta_ugovora == 11 OR $vrsta_ugovora == 12 OR $vrsta_ugovora == 21){
		
	// }else{
		// $pdf->MultiCell(170,5,''.$c7_tacka4.'','','J',0);
	// }
	$pdf->Ln(10);
	
	//CLAN 8
	$pdf->SetFont('DejaVuSerif','BI',12);
	$pdf->MultiCell(170,5,'Član 8.','','C',0);
	
	$pdf->SetFont('DejaVuSerif','',11);
	$pdf->MultiCell(170,5,''.$c8_tacka1.'','','J',0);
	$pdf->MultiCell(170,5,''.$c8_tacka2.'','','J',0);
	$pdf->Ln(10);
	
	//CLAN 9
	$pdf->SetFont('DejaVuSerif','BI',12);
	$pdf->MultiCell(170,5,'Član 9.','','C',0);
	
	$pdf->SetFont('DejaVuSerif','',11);
	$pdf->MultiCell(170,5,''.$c9_tacka1.'','','J',0);
	$pdf->Ln(10);
	$pdf->AddPage();
	
	//CLAN 10
	$pdf->Ln(10);
	$pdf->SetFont('DejaVuSerif','BI',12);
	$pdf->MultiCell(170,5,'Član 10.','','C',0);
	
	$pdf->SetFont('DejaVuSerif','',11);
	$pdf->MultiCell(170,5,''.$c10_tacka1.'','','J',0);
	$pdf->Ln(20);
	
	if($vrsta_ugovora == 9 OR $vrsta_ugovora == 10 OR $vrsta_ugovora == 11 OR $vrsta_ugovora == 12 OR $vrsta_ugovora == 21 OR $vrsta_ugovora == 51 OR $vrsta_ugovora == 61 OR $vrsta_ugovora == 71 OR $vrsta_ugovora == 41){
		$pdf->Image('images/bih_potpis_new.png',20,55,70);
	}else{
		$pdf->Image('images/bih_potpis_new.png',20,55,70);
	}
	$pdf->Cell(120,5,'Nalogoprimac: ',0,'','L',0);
	$pdf->Cell(50,5,'Nalogodavac: ',0,'','L',0);
	$pdf->Ln(25);
	$pdf->Cell(40,5,'Bihać, dana '.$date_ispis.'',0,'','L',0);


	$filename="files/ugovori_uplatnice_dipl/".$ugovor_putanja;
	
	//$pdf->Output();
	$pdf->Output($filename,'F');
	
	return $ugovor_putanja;
}

function createUgovorSRB($kandidat_id){
	Global $db;
	Global $logged_employee_id;
	Global $vrsta_dokumenta;
	$vrsta_dokumenta = "ugovor_rs";
	Global $valutaCheck;
	$valutaCheck = "RSD";
	
	$danas = date('d.m.Y');
	$datetime = date('Y-m-d H:i:s');
	
	$day = date('d');
	$mjesec = date('m');
	$godina = date('y');
	if($day > 28)
		$day = 1;
	//pokupiti info iz baze
	$get_info = $db->prepare("
					SELECT ime_nd_kandidata, prezime_nd_kandidata, ulica_nd_kandidata, postanski_broj_nd_kandidata, grad_nd_kandidata, vrsta_ugovora_nd_kandidata, jmbg_nd_kandidata, broj_licne_karte_nd_kandidata, ulica_bor_nd_kandidata, postanski_broj_bor_nd_kandidata, grad_bor_nd_kandidata, izdao_licnu_nd_kandidata
					FROM idk_nd_kandidata
					WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
					");

	$get_info->execute(array(
						':id_broj_nd_kandidata' => $kandidat_id));

	$info_row = $get_info->fetch();
	
	$prezime = $info_row["prezime_nd_kandidata"];
	$ime = $info_row["ime_nd_kandidata"];
	$grad = $info_row["grad_nd_kandidata"];
	$ulica = $info_row["ulica_nd_kandidata"];
	$vrsta_ugovora = $info_row["vrsta_ugovora_nd_kandidata"];
	$jmbg = $info_row["jmbg_nd_kandidata"];
	$licna = $info_row["broj_licne_karte_nd_kandidata"];
	$pbroj = $info_row["postanski_broj_nd_kandidata"];
	$ulica_bor = $info_row["ulica_bor_nd_kandidata"];
	if($ulica_bor == null OR $ulica_bor == ""){
		$ulica_bor = $ulica;
		$postanski_broj_bor = $pbroj;
		$grad_bor = $grad;
	}else{
		$postanski_broj_bor = $info_row["postanski_broj_bor_nd_kandidata"];
		$grad_bor = $info_row["grad_bor_nd_kandidata"];
	}
	$izdata = $info_row["izdao_licnu_nd_kandidata"];
	
	$brojac_ugovora = createBrojUgovora("Srbija");
	$ugovor_putanja = "UGS-".$brojac_ugovora."-".$mjesec."-".$godina.".pdf";
	
	$txt_broj_ugovora = "DIPLS-".$brojac_ugovora."-".$mjesec."/".$godina;
	//INSERT UGOVORA
	$insert_ugovor = $db->prepare("	
				INSERT INTO idk_nd_kandidata_dokumenti	
				(naziv_dokument_nd,  naziv_dokument_ostali_nd, id_kandidata_dokument_nd, vrijeme_dodavanja_dokument_nd, dodao_zaposlenik_dokument_nd, tip_dokumenta)	
				VALUES	
				(:naziv_dokument_nd,:naziv_dokument_ostali_nd,:id_kandidata_dokument_nd,:vrijeme_dodavanja_dokument_nd,:dodao_zaposlenik_dokument_nd,:tip_dokumenta)	
				");	
	$insert_ugovor->execute(array(	
				':naziv_dokument_nd' => $ugovor_putanja,	
				':naziv_dokument_ostali_nd' => "ugovor",	
				':id_kandidata_dokument_nd' => $kandidat_id,	
				':vrijeme_dodavanja_dokument_nd' => $datetime,	
				':dodao_zaposlenik_dokument_nd' => $logged_employee_id,	
				':tip_dokumenta' => 1
				));
	
	$pdf = new PDF('P','mm','A4');
	

	$pdf->AliasNbPages();
	$pdf->SetDisplayMode('real', 'single');

	$pdf->AddPage();

	// Add a Unicode font (uses UTF-9)
	$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
	$pdf->AddFont('DejaVu','B','DejaVuSansCondensed-Bold.ttf',true);
	$pdf->AddFont('DejaVuSerif','','DejaVuSerifCondensed.ttf',true);
	$pdf->AddFont('DejaVuSerif','B','DejaVuSerifCondensed-Bold.ttf',true);
	$pdf->AddFont('DejaVuSerif','BI','DejaVuSerifCondensed-BoldItalic.ttf',true);

	$pdf->SetLeftMargin(20);
	$pdf->Ln(10);
	$pdf->SetFont('DejaVuSerif','',14);
	
	$pdf->MultiCell(170,7,'UGOVOR O PRUŽANJU USLUGE POSREDOVANJA U POSTUPKU NOSTRIFIKACIJE DIPLOME','','C',0);
	$pdf->Ln(5);
	
	$pdf->SetFont('DejaVuSerif','',11);
	$pdf->MultiCell(170,5,'Jobstep International d.o.o. Beograd (Jobstep International d.o.o.), sa adresom sedišta: Jurija Gagarina 32, 11070 Novi Beograd; matični broj (MB): 21569143; poreski identifikacioni broj (PIB): 111915414, koje zastupa direktor Denis Selmanović, kao pružalac usluge sa jedne strane (dalje: pružalac usluge)','','',0);
	$pdf->MultiCell(170,7,'i','','',0);
	$pdf->MultiCell(170,5,$ime.' '.$prezime.', sa adresom prebivališta: '.$ulica.', '.$pbroj.' '.$grad.'; i sa adresom boravišta: '.$ulica_bor.', '.$postanski_broj_bor.' '.$grad_bor.'; jedinstveni matični broj građana (JMBG): '.$jmbg.'; broj lične karte: '.$licna.', izdata od: '.$izdata.', kao korisnik usluge sa druge strane (dalje: korisnik usluge),','','',0);
	$pdf->MultiCell(170,7,'(zajedno označeni kao: ugovorne strane),','','',0);
	$pdf->MultiCell(170,7,'dana '.$danas.'. godine zaključuju','','',0);
	$pdf->MultiCell(170,7,'UGOVOR O PRUŽANJU USLUGE POSREDOVANJA U POSTUPKU NOSTRIFIKACIJE DIPLOME','','C',0);
	$pdf->MultiCell(170,7,'na sledeći način:','','',0);
	
	//CLAN 1
	$pdf->MultiCell(170,8,'Član 1','','C',0);
	$pdf->MultiCell(170,5,'Predmet ovog ugovora između ugovornih strana je obavljanje usluge posredovanja u postupku nostrifikacije diplome u zemljama Evropske unije, pretežno nemačkog govornog područja.','','',0);
	$pdf->Ln(7);
	
	//CLAN 2
	$pdf->MultiCell(170,8,'Član 2','','C',0);
	$pdf->MultiCell(170,5,'Pod obavljanjem usluge posredovanja u postupku nostrifikacije diplome u smislu člana 1 ovog ugovora podrazumeva se da se ovim ugovorom pružalac usluge obavezuje da korisnika usluge uputi u sledeće:','','',0);
	$pdf->MultiCell(170,5,'- neophodna dokumentacija potrebna za nostrifikaciju diplome, ','','',0);
	$pdf->MultiCell(170,5,'- prevod dostavljene dokumentacije na nemački jezik,','','',0);
	$pdf->MultiCell(170,5,'- sprovođenje kompletnog postupka do nadležne ustanove za nostrifikaciju diplome.','','',0);
	$pdf->MultiCell(170,5,'Neophodna dokumentacija će biti definisana od strane ustanove nadležne za nostrifikaciju diplome i činiće sastavni deo ovog ugovora.','','',0);
	$pdf->Ln(7);
	$pdf->AddPage();
	
	//CLAN 3
	$pdf->MultiCell(170,8,'Član 3','','C',0);
	$pdf->MultiCell(170,5,'Za navedene poslove iz člana 2 ovog ugovora korisnik usluge se obavezuje platiti novčanu naknadu u iznosu od 59.000,00 RSD (slovima: pedesetdevethiljadadinara).','','',0);
	$pdf->Ln();	
	$pdf->MultiCell(170,5,'Korisnik usluge je dužan platiti pružaocu usluge celokupan iznos novčane naknade navedene u prethodnom stavu ovog člana Ugovora prilikom potpisivanja ugovora o pružanju usluge posredovanja u postupku nostrifikacije diplome sa pružaocem usluge. ','','',0);
	$pdf->Ln();
	$pdf->MultiCell(170,5,'Ukoliko korisnik usluge ugovorenu novčanu naknadu iz ovog člana Ugovora plaća u dve rate, avans u iznosu od 50% (slovima: pedesetposto) od novčane naknade iz stava 1 ovog člana Ugovora se plaća prilikom potpisivanja ugovora o pružanju usluge posredovanja u postupku nostrifikacije diplome sa pružaocem usluge, a ostatak iznosa novčane naknade se plaća ODMAH po okončanju postupka nostrifikacije diplome, bez obzira na ishod postupka.','','',0);
	$pdf->Ln();
	$pdf->MultiCell(170,5,'Ukoliko korisnik usluge ugovorenu novčanu naknadu iz ovog člana Ugovora plaća u tri ili više rata, novčana naknada iz stava 1 ovog člana Ugovora se plaća avansno u dogovorenom broju rata, u jednakim iznosima, tako što se prva rata plaća prilikom potpisivanja ugovora o pružanju usluge posredovanja u postupku nostrifikacije diplome sa pružaocem usluge, a počev od druge rate svaka sledeća rata se plaća u roku od mesec dana od prethodno plaćene rate, dok se poslednja rata plaća ODMAH po okončanju postupka nostrifikacije diplome, bez obzira na ishod postupka. ','','',0);
	$pdf->Ln();
	$pdf->MultiCell(170,5,'Eventualne akcije (popusti) odobravaju se na osnovu i u skladu sa pravilnikom o akcijama (popustima) i/ili odlukom o akcijama (popustima). ','','',0);
	$pdf->Ln(7);
	
	//CLAN 4
	$pdf->MultiCell(170,8,'Član 4','','C',0);
	$pdf->MultiCell(170,5,'Troškovi pribavljanja neophodne dokumentacije (takse, naknade i overa original dokumentacije), kao i trošak nostrifikacije diplome nisu uključeni u cenu navedenu u članu 3 ovog ugovora.','','',0);
	$pdf->MultiCell(170,5,'Sve neophodne dokumente dužan je pribaviti korisnik usluge o svom trošku.','','',0);
	$pdf->MultiCell(170,5,'Trošak takse nostrifikacije diplome dužan je snositi korisnik usluge u celosti.','','',0);
	$pdf->Ln(7);
	
	//CLAN 5
	$pdf->MultiCell(170,8,'Član 5','','C',0);
	$pdf->MultiCell(170,5,'Obaveza pružaoca usluge prema korisniku usluge prestaje danom okončanja postupka nostrifikacije diplome.','','',0);
	$pdf->MultiCell(170,5,'Obaveza korisnika usluge prema pružaocu usluge prestaje danom izmirenja ukupne cene usluge posredovanja u postupku nostrifikacije diplome.','','',0);
	$pdf->AddPage();
	
	//CLAN 6
	$pdf->MultiCell(170,8,'Član 6','','C',0);
	$pdf->MultiCell(170,5,'Nalogoprimac može odustati od ugovora jednostranom izjavom volje i bez povrata uplaćenih sredstava u sledećim situacijama:','','',0);
	$pdf->MultiCell(170,5,'- ukoliko nalogodavac u roku od tri meseca od dana potpisivanja ovog ugovora ne dostavi kompletnu dokumentaciju neophodnu za nostrifikaciju diplome,','','',0);
	$pdf->MultiCell(170,5,'- ukoliko se ustanovi da su dostavljene informacije i dokumenti od strane korisnika usluge falsifikati.','','',0);
	$pdf->MultiCell(170,5,'Nalogodavac je obavezan izmiriti preostali deo naknade navedene u članu 3 u roku od 7 (sedam) dana od dana prijema obaveštenja o raskidu ugovora.','','',0);
	$pdf->MultiCell(170,5,'Nalogodavac je takođe obavezan da nalogoprimcu nadoknadi štetu nastalu dostavljanjem netačnih, neispravnih i falsifikovanih dokumenata.','','',0);
	$pdf->Ln(7);
	
	//CLAN 7
	$pdf->MultiCell(170,8,'Član 7','','C',0);
	$pdf->MultiCell(170,5,'Ako nalogodavac nakon što je pokrenut postupak nostrifikacije diplome odustane svojom voljom od postupka nostrifikacije diplome neposredno nakon pokretanja istog, nalogoprimac nije dužan da vrati primljeni avans i nalogodavac je dužan da izmiri preostali, neizmireni iznos naknade iz člana 3 ovog Ugovora.','','',0);
	$pdf->Ln();	
	$pdf->MultiCell(170,5,'Pod pokretanjem postupka nostrifikacije smatra se uplata celog ili delimičnog iznosa naknade iz člana 3. ovog ugovora. ','','',0);
	$pdf->Ln(7);
	
	//CLAN 8
	$pdf->MultiCell(170,8,'Član 8','','C',0);
	$pdf->MultiCell(170,5,'U skladu sa Zakonom o zaštiti podataka o ličnosti, korisnik usluge je prilikom komunikacije sa pružaocem usluge, a na ime posredovanja u postupku nostrifikacije diplome, te zaključenja ovog ugovora, obavešten od strane pružaoca usluge o svrsi i načinu obrade (prikupljanje, obrada, čuvanje i uništavanje) podataka o ličnosti korisnika usluge, a koju pružalac usluge vrši u svojstvu rukovaoca, prikupljajući podatke od korisnika usluge, koji su neophodno potrebni radi obavljanja usluge posredovanja u postupku nostrifikacije diplome- kao što su: ime i prezime, datum i mesto rođenja, adresa prebivališta, adresa boravišta, JMBG, br. lične karte i izdavalac lične karte, obrazovanje- zvanje  i zanimanje, diploma (dokument), CV, kontakt podaci- mejl adresa, adresa stanovanja, broj telefona i dr. (podaci o ličnosti korisnika usluge koji se obrađuju uključuju, ali izuzetno ne i ograničavaju prethodno navedene lične podatke). ','','',0);
	$pdf->AddPage();
	$pdf->MultiCell(170,5,'Podaci o ličnosti korisnika usluge, i to: ime i prezime, adresa stanovanja i kontakt telefon biće dati kurirskoj službi (a koja je takođe dužna voditi računa i brinuti o zaštiti podataka o ličnosti u poslovanju, shodno zakonu, propisima i ugovoru sa pružaocem usluge), a radi slanja i dostavljanja potrebne dokumentacije na ime ugovorene usluge. ','','',0);
	$pdf->Ln();
	$pdf->MultiCell(170,5,'Namera je da se podaci iznesu u Republiku Nemačku, koja se nalazi na listi iz člana 64 stav 7 Zakona o zaštiti podataka o ličnosti, tj. država, delova njihovih teritorija ili jednog ili više sektora određenih delatnosti u tim državama i međunarodnih organizacija u kojima se smatra da jeste obezbeđen primereni nivo zaštite podataka.','','',0);
	$pdf->Ln();
	$pdf->MultiCell(170,5,'O eventualnoj potrebi da lične podatke korisnika usluge obrađuje u svrhu koja je različita od svrhe za koju su dati podaci prikupljeni, pružalac usluge će pre započinjanja dalje obrade podataka obavestiti korisnika usluge o svim informacijama vezanim za tu drugu svrhu obrade datih podataka, kao i o svim drugim važnim informacijama koje su sa tim u vezi. ','','',0);
	$pdf->Ln();
	$pdf->MultiCell(170,5,'Ukoliko korisnik usluge ima bilo kakvih pitanja, dilema ili nejasnoća u vezi sa postupanjem sa njegovim ličnim podacima, može se obratiti pružaocu usluge na njegove kontakt podatke, koji su navedeni i istaknuti na internet sajtu i/ili oglasnoj tabli pružoca usluge (info@job-step.rs).','','',0);
	$pdf->Ln(7);
	
	//CLAN 9
	$pdf->MultiCell(170,8,'Član 9','','C',0);
	$pdf->MultiCell(170,5,'Korisnik usluge uz ovaj ugovor potpisuje i Informativni list, a koji je prethodno u celosti pročitao i razumeo i koji mu je rastumačen, koji čini sastavni deo ovog ugovora.','','',0);
	$pdf->Ln(7);
	
	//CLAN 10
	$pdf->MultiCell(170,8,'Član 10','','C',0);
	$pdf->MultiCell(170,5,'Na sva prava, obaveze i odgovornosti koje nisu uređene ovim ugovorom primenjuju se odgovarajuće odredbe propisa.','','',0);
	$pdf->Ln(7);
	
	//CLAN 11
	$pdf->MultiCell(170,8,'Član 11','','C',0);
	$pdf->MultiCell(170,5,'Sve eventualne sporove koji nastanu po osnovu ovog ugovora ugovorne strane će nastojati da reše mirnim putem sporazumno, a ako to ne bude moguće ugovaraju mesnu nadležnost stvarno nadležnog suda u Beogradu.','','',0);
	$pdf->Ln();
	$pdf->AddPage();
	
	
	//CLAN 12
	$pdf->MultiCell(170,8,'Član 12','','C',0);
	$pdf->MultiCell(170,5,'Obe ugovorne strane su Ugovor u celosti pročitale i razumele, isti im je rastumačen, pa ga u znaku da on u potpunosti sadrži i odražava njihovu izraženu volju svojeručno potpisuju.','','',0);
	$pdf->Ln(3);
	$pdf->MultiCell(170,5,'Ovaj ugovor sačinjen je u 4 (četiri) istovetna primerka, od kojih svaka ugovorna strana zadržava po 2 (dva) primerka. ','','',0);
	$pdf->Ln(3);
	$pdf->MultiCell(170,5,'Ovaj ugovor stupa na snagu danom potpisivanja obeju ugovornih strana.','','',0);
	$pdf->Ln(10);
	
	$pdf->MultiCell(170,5,'Broj ugovora: '.$txt_broj_ugovora.'','','',0);
	$pdf->Cell(120,5,'U Beogradu, dana '.$danas.'. godine',0,'','L',0);
	$pdf->Cell(50,5,'',0,'','L',0);
	$pdf->Ln(10);
	
	$pdf->Image('images/srb_potpis.png',20,135,35);
	
	$pdf->Cell(120,7,'ZA PRUŽAOCA USLUGE: ',0,'','L',0);
	$pdf->Cell(50,7,'ZA KORISNIKA USLUGE: ','','','L',0);
	$pdf->Ln();
	$pdf->Cell(120,7,'______________________________',0,'','L',0);
	$pdf->Cell(50,7,'______________________________',0,'','L',0);
	$pdf->Ln();
	$pdf->Cell(120,7,'(mesto i datum)',0,'','L',0);
	$pdf->Cell(50,7,'(mesto i datum)',0,'','L',0);
	$pdf->Ln();
	$pdf->Cell(120,7,'______________________________',0,'','L',0);
	$pdf->Cell(50,7,'______________________________',0,'','L',0);
	$pdf->Ln();
	$pdf->Cell(120,7,'(potpis i pečat)',0,'','L',0);
	$pdf->Cell(50,7,'(ime, prezime i potpis)',0,'','L',0);
	$pdf->Ln();
	
	
	// $pdf->Cell(120,7,'ZA PRUŽAOCA USLUGE: ',0,'','L',0);
	// $pdf->Cell(50,7,'ZA KORISNIKA USLUGE: ','','','L',0);
	// $pdf->Ln();
	// $pdf->Cell(120,7,'______________________________',0,'','L',0);
	// $pdf->Cell(50,7,'______________________________',0,'','L',0);
	// $pdf->Ln();
	// $pdf->Cell(120,7,'(mjesto i datum)',0,'','L',0);
	// $pdf->Cell(50,7,'(mjesto i datum)',0,'','L',0);
	// $pdf->Ln();
	// $pdf->Cell(120,7,'______________________________',0,'','L',0);
	// $pdf->Cell(50,7,'______________________________',0,'','L',0);
	// $pdf->Ln();
	// $pdf->Cell(120,7,'(potpis i pečat)',0,'','L',0);
	// $pdf->Cell(50,7,'(ime, prezime i potpis)',0,'','L',0);
	// $pdf->Ln();

	$filename="files/ugovori_uplatnice_dipl/".$ugovor_putanja;
	
	//$pdf->Output();
	$pdf->Output($filename,'F');
	
	return $ugovor_putanja;
}

function createUplatnicaBIH($predracun_id){

	Global $db;
	Global $logged_employee_id;
	Global $vrsta_dokumenta;
	$vrsta_dokumenta = "";
	
	$datetime = date('Y-m-d H:i:s');
	$day = date('d');
	$mjesec = date('m');
	$godina = date('y');
	$danas = date('d.m.Y');
	
	$rok_uplate = date('d.m.Y', strtotime($danas. ' + 10 days'));
	$get_predracun = $db->prepare("
					SELECT pr_broj_predracuna, nk.id_broj_nd_kandidata, nk.ime_nd_kandidata, nk.prezime_nd_kandidata, nk.ulica_nd_kandidata, nk.postanski_broj_nd_kandidata, nk.grad_nd_kandidata, pr_rata, nk.vrsta_ugovora_nd_kandidata, pr_vrijednost_BAM, pr_vrijednost_EUR, pr_file, pr_domaca_valuta, jmbg_nd_kandidata, pr_naplata_preko
					FROM idk_predracuni
					JOIN idk_nd_kandidata nk
					ON nk.id_broj_nd_kandidata = pr_kandidat_id
					WHERE pr_id = :pr_id
					");

	$get_predracun->execute(array(
						':pr_id' => $predracun_id));

	$predracun_row = $get_predracun->fetch();
	
	$broj_predracuna = $predracun_row["pr_broj_predracuna"];
	$kandidat_id = $predracun_row["id_broj_nd_kandidata"];
	$ime = $predracun_row["ime_nd_kandidata"];
	$prezime = $predracun_row["prezime_nd_kandidata"];
	$ulica = $predracun_row["ulica_nd_kandidata"];
	$pbroj = $predracun_row["postanski_broj_nd_kandidata"];
	$grad = $predracun_row["grad_nd_kandidata"];
	$pr_rata = $predracun_row["pr_rata"];
	$pr_vrijednost_BAM = $predracun_row["pr_vrijednost_BAM"];
	$pr_vrijednost_BAM_f = number_format($pr_vrijednost_BAM, 2, ',', '.');
	$pr_vrijednost_EUR = $predracun_row["pr_vrijednost_EUR"];
	$pr_file = $predracun_row["pr_file"];
	$vrsta_ugovora = $predracun_row["vrsta_ugovora_nd_kandidata"];
	$pr_domaca_valuta = $predracun_row["pr_domaca_valuta"];
	$jmbg = $predracun_row["jmbg_nd_kandidata"];
	$pr_naplata_preko = $predracun_row["pr_naplata_preko"];
	
	if($pr_naplata_preko == 1){
		$adresa_firme = "Jobstep international d.o.o.";
	}else{
		$adresa_firme = "JOBSTEP INTERNATIONAL DOO";
	}
	
	switch($vrsta_ugovora){
		case 1: case 2: case 22: case 52: case 62: case 72: case 82: case 42:
			$broj_rata = 2;
		break;
		case 5: case 6: case 23: case 53: case 63: case 73: case 83: case 43:
			$broj_rata = 3;
		break;
		case 7: case 8: case 24: case 54: case 64: case 74: case 84: case 44:
			$broj_rata = 4;
		break;
		case 3: case 4: case 25: case 55: case 65: case 75: case 85: case 45:
			$broj_rata = 5;
		break;
		default:
			$broj_rata = 1;
	}
	
	$brojac_uplatnice = createBrojUplatnice("BiH");
	$uplatnica_putanja = "UPB-".$brojac_uplatnice."-".$mjesec."-".$godina.".pdf";
	
	//INSERT UPLATNICE
	$insert_ugovor = $db->prepare("	
				INSERT INTO idk_nd_kandidata_dokumenti	
				(naziv_dokument_nd,  naziv_dokument_ostali_nd, id_kandidata_dokument_nd, vrijeme_dodavanja_dokument_nd, dodao_zaposlenik_dokument_nd, tip_dokumenta, broj_rate)	
				VALUES	
				(:naziv_dokument_nd,:naziv_dokument_ostali_nd,:id_kandidata_dokument_nd,:vrijeme_dodavanja_dokument_nd,:dodao_zaposlenik_dokument_nd,:tip_dokumenta,:broj_rate)	
				");	
	$insert_ugovor->execute(array(	
				':naziv_dokument_nd' => $uplatnica_putanja,	
				':naziv_dokument_ostali_nd' => "uplatnica",	
				':id_kandidata_dokument_nd' => $kandidat_id,	
				':vrijeme_dodavanja_dokument_nd' => $datetime,	
				':dodao_zaposlenik_dokument_nd' => $logged_employee_id,	
				':tip_dokumenta' => 2,
				':broj_rate' => $pr_rata
				));
	
	$pdf = new PDF('P','mm','A4');
	$pdf->AliasNbPages();
	
	$pdf->AddFont('DejaVuSerif','B','DejaVuSerifCondensed-Bold.ttf',true);
	$pdf->AddFont('DejaVuSerif','BI','DejaVuSerifCondensed-BoldItalic.ttf',true);

	$pdf->AddPage();
	
	$pdf->SetFont('DejaVuSerif','',9);
	
	$pdf->Line(62,15,83,15);
	$pdf->Line(13,20,83,20);
	$pdf->Line(13,25,83,25);
	$pdf->Line(35,30,83,30);
	$pdf->Line(13,35,83,35);
	$pdf->Line(13,40,83,40);
	$pdf->Line(28,45,83,45);
	$pdf->Line(13,50,83,50);
	$pdf->Line(13,55,83,55);
	$pdf->Line(38,67,58,67);
	$pdf->Line(37,83,58,83);
	$pdf->Line(40,103,58,103);
	
	//LEFT
	$pdf->Cell(3,5,"",'L,T',0,'',0);
	$pdf->Cell(55,5,"Uplatio je (ime, adresa, telefon)",'T',0,'',0); 
	$pdf->Cell(17,5,"",'T',0,'',0); 
	//RIGHT
	$pdf->Cell(100,5,"",'T,R',0,'',0); 
	$pdf->Ln();
	
	//LEFT
	$pdf->SetFont('DejaVuSerif','',10);
	$pdf->Cell(3,5,"",'L',0,'',0);
	$pdf->Cell(72,5,$ime." ".$prezime,'',0,'C',0);
	//RIGHT
	$pdf->SetFont('DejaVuSerif','',9);
	$pdf->Cell(100,5,"Račun",'R',0,'',0); 
	$pdf->Ln();
	
	//LEFT
	$pdf->Cell(3,5,"",'L',0,'',0);
	$pdf->Cell(72,5,$ulica." ".$grad,'',0,'C',0); 
	//RIGHT
	$pdf->SetFont('DejaVuSerif','',9);
	$pdf->Cell(21.4,5,"pošiljaoca",'',0,'',0); 
	$pdf->Cell(4.6,5,"",'B,L',0,'',0); 
	$pdf->Cell(4.6,5,"",'B,L',0,'',0); 
	$pdf->Cell(4.6,5,"",'B,L',0,'',0); 
	$pdf->Cell(4.6,5,"",'B,L',0,'',0); 
	$pdf->Cell(4.6,5,"",'B,L',0,'',0); 
	$pdf->Cell(4.6,5,"",'B,L',0,'',0); 
	$pdf->Cell(4.6,5,"",'B,L',0,'',0); 
	$pdf->Cell(4.6,5,"",'B,L',0,'',0); 
	$pdf->Cell(4.6,5,"",'B,L',0,'',0); 
	$pdf->Cell(4.6,5,"",'B,L',0,'',0); 
	$pdf->Cell(4.6,5,"",'B,L',0,'',0); 
	$pdf->Cell(4.6,5,"",'B,L',0,'',0); 
	$pdf->Cell(4.6,5,"",'B,L',0,'',0); 
	$pdf->Cell(4.6,5,"",'B,L',0,'',0); 
	$pdf->Cell(4.6,5,"",'B,L',0,'',0); 
	$pdf->Cell(4.6,5,"",'B,L,R',0,'',0); 
	$pdf->Cell(5,5,"",'R',0,'',0); 
	$pdf->Ln();
	
	//lEFT
	$pdf->SetFont('DejaVuSerif','',9);
	$pdf->Cell(3,5,"",'L',0,'',0);
	$pdf->Cell(30,5,"Svrha uplate: ",'',0,'',0); 
	$pdf->SetFont('DejaVuSerif','',10);
	$pdf->Cell(42,5,"",'',0,'C',0); 
	//RIGHT
	$pdf->SetFont('DejaVuSerif','',9);
	$pdf->Cell(100,5,"",'R',0,'',0); 
	$pdf->Ln();
	
	//LEFT
	$pdf->SetFont('DejaVuSerif','',10);
	$pdf->Cell(3,5,"",'L',0,'',0);
	$pdf->Cell(72,5,$broj_predracuna,'',0,'C',0); 
	//RIGHT
	$pdf->SetFont('DejaVuSerif','',9);
	$pdf->Cell(100,5,"Račun",'R',0,'',0); 
	$pdf->Ln();
	
	//LEFT
	$pdf->Cell(3,5,"",'L',0,'',0);
	$pdf->SetFont('DejaVuSerif','',10);
	$pdf->Cell(72,5,"",'',0,'C',0); 
	//RIGHT
	$pdf->SetFont('DejaVuSerif','',9);
	$pdf->Cell(21.4,5,"Primaoca",'R',0,'',0);
	$pdf->Cell(4.6,5,"1",'B,L',0,'C',0); 
	$pdf->Cell(4.6,5,"4",'B,L',0,'C',0); 
	$pdf->Cell(4.6,5,"1",'B,L',0,'C',0); 
	$pdf->Cell(4.6,5,"4",'B,L',0,'C',0); 
	$pdf->Cell(4.6,5,"7",'B,L',0,'C',0); 
	$pdf->Cell(4.6,5,"5",'B,L',0,'C',0); 
	$pdf->Cell(4.6,5,"5",'B,L',0,'C',0); 
	$pdf->Cell(4.6,5,"3",'B,L',0,'C',0); 
	$pdf->Cell(4.6,5,"2",'B,L',0,'C',0); 
	$pdf->Cell(4.6,5,"0",'B,L',0,'C',0); 
	$pdf->Cell(4.6,5,"0",'B,L',0,'C',0); 
	$pdf->Cell(4.6,5,"4",'B,L',0,'C',0); 
	$pdf->Cell(4.6,5,"6",'B,L',0,'C',0); 
	$pdf->Cell(4.6,5,"8",'B,L',0,'C',0); 
	$pdf->Cell(4.6,5,"9",'B,L',0,'C',0); 
	$pdf->Cell(4.6,5,"3",'B,L,R',0,'C',0); 
	$pdf->Cell(5,5,"",'R',0,'',0); 
	$pdf->Ln();
	
	//LEFT
	$pdf->SetFont('DejaVuSerif','',9);
	$pdf->Cell(3,5,"",'L',0,'',0);
	$pdf->Cell(30,5,"Primalac: ",'',0,'',0); 
	$pdf->Cell(42,5,"",'',0,'',0); 
	//RIGHT
	$pdf->Cell(100,5,"",'R',0,'',0); 
	$pdf->Ln();
	
	//LEFT
	$pdf->SetFont('DejaVuSerif','',10);
	$pdf->Cell(3,5,"",'L',0,'',0);
	$pdf->Cell(72,5,$adresa_firme,'',0,'C',0); 
	//RIGHT
	$pdf->Cell(10,5,"",'',0,'',0);
	$pdf->Cell(10,5,"KM",'',0,'R',0);
	$pdf->Cell(60,5,$pr_vrijednost_BAM_f,'B',0,'C',0);
	$pdf->Cell(20,5,"",'R',0,'',0); 
	$pdf->Ln();
	
	//LEFT
	$pdf->Cell(3,3,"",'L',0,'',0);
	$pdf->Cell(72,3,"",'',0,'',0);
	//RIGHT
	$pdf->Cell(100,3,"",'R',0,'',0);
	$pdf->Ln();
	
	//LEFT
	$pdf->Cell(3,2,"",'L',0,'',0);
	$pdf->Cell(72,2,"",'',0,'',0);
	
	//RIGHT
	$pdf->SetFont('DejaVuSerif','BI',9);
	$pdf->Cell(10,2,"",'B',0,'',0); 
	$pdf->Cell(50,2,"samo za uplate javnih prihoda",'',0,'',0); 
	$pdf->Cell(38,2,"",'B',0,'',0); 
	$pdf->Cell(2,2,"",'R',0,'',0); 
	$pdf->Ln();
	
	//LEFT
	$pdf->SetFont('DejaVuSerif','',9);
	$pdf->Cell(3,2,"",'L',0,'',0);
	$pdf->Cell(72,2,"",'',0,'',0);
	//RIGHT
	$pdf->Cell(90.8,2,"",'L',0,'',0); 
	$pdf->Cell(5,2,"",'R,L',0,'',0); 
	$pdf->Cell(2.2,2,"",'R',0,'',0); 
	$pdf->Cell(2,2,"",'R',0,'',0); 
	$pdf->Ln();
	
	//LEFT
	$pdf->Cell(3,4,"",'L',0,'',0);
	$pdf->Cell(30,4,"",'',0,'',0); 
	$pdf->Cell(42,4,"",'',0,'',0); 
	//RIGHT
	$pdf->SetFont('DejaVuSerif','',9);
	$pdf->Cell(24,4,"Broj poreskog",'L',0,'',0); 
	$pdf->Cell(3.6,4,"",'L',0,'',0); 
	$pdf->Cell(3.6,4,"",'L',0,'',0); 
	$pdf->Cell(3.6,4,"",'L',0,'',0); 
	$pdf->Cell(3.6,4,"",'L',0,'',0); 
	$pdf->Cell(3.6,4,"",'L',0,'',0); 
	$pdf->Cell(3.6,4,"",'L',0,'',0); 
	$pdf->Cell(3.6,4,"",'L',0,'',0); 
	$pdf->Cell(3.6,4,"",'L',0,'',0); 
	$pdf->Cell(3.6,4,"",'L',0,'',0); 
	$pdf->Cell(3.6,4,"",'L',0,'',0); 
	$pdf->Cell(3.6,4,"",'L',0,'',0); 
	$pdf->Cell(3.6,4,"",'L',0,'',0); 
	$pdf->Cell(3.6,4,"",'L,R',0,'',0); 
	$pdf->Cell(20,4,"Vrsta uplate",'',0,'',0); 
	$pdf->Cell(5,4,"",'R,B,L',0,'',0); 
	$pdf->Cell(2.2,4,"",'R',0,'',0); 
	$pdf->Cell(2,4,"",'R',0,'',0); 
	$pdf->Ln();
	
	//LEFT
	$pdf->SetFont('DejaVuSerif','',10);
	$pdf->Cell(3,2,"",'L',0,'',0);
	$pdf->Cell(35,2,"Mjesto i ",'',0,'',0); 
	$pdf->Cell(11,2,"",'',0,'',0); 
	$pdf->Cell(3,2,"",'L',0,'',0); 
	$pdf->Cell(3,2,"",'L',0,'',0); 
	$pdf->Cell(3,2,"",'L',0,'',0); 
	$pdf->Cell(3,2,"",'L',0,'',0); 
	$pdf->Cell(3,2,"",'L',0,'',0); 
	$pdf->Cell(3,2,"",'L',0,'',0); 
	$pdf->Cell(3,2,"",'L',0,'',0); 
	$pdf->Cell(3,2,"",'L,R',0,'',0);
	$pdf->Cell(2,2,"",'',0,'',0);
	//RIGHT
	$pdf->SetFont('DejaVuSerif','',9);
	$pdf->Cell(24,2,"obveznika",'L',0,'C',0); 
	$pdf->Cell(3.6,2,"",'L,B',0,'',0); 
	$pdf->Cell(3.6,2,"",'L,B',0,'',0); 
	$pdf->Cell(3.6,2,"",'L,B',0,'',0); 
	$pdf->Cell(3.6,2,"",'L,B',0,'',0); 
	$pdf->Cell(3.6,2,"",'L,B',0,'',0); 
	$pdf->Cell(3.6,2,"",'L,B',0,'',0); 
	$pdf->Cell(3.6,2,"",'L,B',0,'',0); 
	$pdf->Cell(3.6,2,"",'L,B',0,'',0); 
	$pdf->Cell(3.6,2,"",'L,B',0,'',0); 
	$pdf->Cell(3.6,2,"",'L,B',0,'',0); 
	$pdf->Cell(3.6,2,"",'L,B',0,'',0); 
	$pdf->Cell(3.6,2,"",'L,B',0,'',0); 
	$pdf->Cell(3.6,2,"",'L,B,R',0,'',0); 
	$pdf->Cell(27.2,2,"",'R',0,'',0); 
	$pdf->Cell(2,2,"",'R',0,'',0); 
	$pdf->Ln();
	
	//LEFT
	$pdf->SetFont('DejaVuSerif','',10);
	$pdf->Cell(3,5,"",'L',0,'',0);
	$pdf->Cell(35,5,"datum uplate: ",'',0,'',0); 
	$pdf->Cell(11,5,"",'',0,'',0); 
	$pdf->Cell(3,5,"",'L,B',0,'',0); 
	$pdf->Cell(3,5,"",'L,B',0,'',0); 
	$pdf->Cell(3,5,"/",'L,B',0,'',0); 
	$pdf->Cell(3,5,"",'L,B',0,'',0); 
	$pdf->Cell(3,5,"",'L,B',0,'',0); 
	$pdf->Cell(3,5,"/",'L,B',0,'',0); 
	$pdf->Cell(3,5,"",'L,B',0,'',0); 
	$pdf->Cell(3,5,"",'L,B,R',0,'',0); 
	$pdf->Cell(2,5,"",'R',0,'',0);
	//RIGHT
	$pdf->Cell(60,5,"",'',0,'',0); 
	$pdf->Cell(3,5,"",'B',0,'',0); 
	$pdf->Cell(23,5,"",'',0,'',0); 
	$pdf->Cell(12,5,"",'R,B',0,'',0); 
	$pdf->Cell(2,5,"",'R',0,'',0); 
	$pdf->Ln();
	
	//LEFT
	$pdf->Cell(3,1,"",'L',0,'',0);
	$pdf->Cell(30,1,"",'',0,'',0); 
	$pdf->Cell(42,1,"",'',0,'',0); 
	//RIGHT
	$pdf->SetFont('DejaVuSerif','',9);
	$pdf->Cell(60,1,"",'L',0,'',0); 
	$pdf->Cell(3,1,"",'L',0,'',0); 
	$pdf->Cell(23,1,"Porezni period",'',0,'',0); 
	$pdf->Cell(12,1,"",'R',0,'',0); 
	$pdf->Cell(2,1,"",'R',0,'',0); 
	$pdf->Ln();
	
	//LEFT
	$pdf->Cell(3,5,"",'L',0,'',0);
	$pdf->Cell(46,5,"",'',0,'',0); 
	$pdf->Cell(24,5,"",'T,L,R',0,'',0); 
	$pdf->Cell(2,5,"",'R',0,'',0); 
	//RIGHT
	$pdf->Cell(60,5,"",'R',0,'',0); 
	$pdf->Cell(38,5,"",'R',0,'',0); 
	$pdf->Cell(2,5,"",'R',0,'',0); 
	$pdf->Ln();
	
	//LEFT
	$pdf->SetFont('DejaVuSerif','',10);
	$pdf->Cell(3,4,"",'L',0,'',0);
	$pdf->Cell(46,4,"Potpis i pečat",'',0,'',0); 
	$pdf->Cell(24,4,"",'L,R',0,'',0); 
	$pdf->Cell(2,4,"",'R',0,'',0);
	//RIGHT
	$pdf->Cell(24,4,"Vrsta",'R',0,'',0); 
	$pdf->Cell(5,4,"",'L',0,'',0); 
	$pdf->Cell(5,4,"",'L',0,'',0); 
	$pdf->Cell(5,4,"",'L',0,'',0); 
	$pdf->Cell(5,4,"",'L',0,'',0); 
	$pdf->Cell(5,4,"",'L',0,'',0); 
	$pdf->Cell(5,4,"",'L,R',0,'',0); 
	$pdf->Cell(6,4,"",'R',0,'',0); 
	$pdf->Cell(3,4,"",'',0,'',0); 
	$pdf->Cell(7,4,"Od:",'',0,'',0); 
	$pdf->Cell(3,4,"",'L,B',0,'',0); 
	$pdf->Cell(3,4,"",'L,B',0,'',0); 
	$pdf->Cell(3,4,"/",'L,B',0,'',0); 
	$pdf->Cell(3,4,"",'L,B',0,'',0); 
	$pdf->Cell(3,4,"",'L,B',0,'',0); 
	$pdf->Cell(3,4,"/",'L,B',0,'',0); 
	$pdf->Cell(3,4,"",'L,B',0,'',0); 
	$pdf->Cell(3,4,"",'L,B,R',0,'',0); 
	$pdf->Cell(4,4,"",'R',0,'',0); 
	$pdf->Cell(2,4,"",'R',0,'',0); 
	$pdf->Ln();
	
	//LEFT
	$pdf->Cell(49,1,"",'L',0,'',0);
	$pdf->Cell(24,1,"",'L,R',0,'',0);
	$pdf->Cell(2,1,"",'R',0,'',0);
	//RIGHT
	$pdf->Cell(24,1,"",'',0,'',0); 
	$pdf->Cell(5,1,"",'L',0,'',0); 
	$pdf->Cell(5,1,"",'L',0,'',0); 
	$pdf->Cell(5,1,"",'L',0,'',0); 
	$pdf->Cell(5,1,"",'L',0,'',0); 
	$pdf->Cell(5,1,"",'L',0,'',0); 
	$pdf->Cell(5,1,"",'L,R',0,'',0); 
	$pdf->Cell(6,1,"",'R',0,'',0); 
	$pdf->Cell(38,1,"",'R',0,'',0); 
	$pdf->Cell(2,1,"",'R',0,'',0); 
	$pdf->Ln();
	
	//LEFT
	$pdf->Cell(49,1,"",'L',0,'',0);
	$pdf->Cell(24,1,"",'L,R',0,'',0);
	$pdf->Cell(2,1,"",'R',0,'',0);
	//RIGHT
	$pdf->Cell(24,1,"prihoda",'',0,'',0); 
	$pdf->Cell(5,1,"",'L,B',0,'',0); 
	$pdf->Cell(5,1,"",'L,B',0,'',0); 
	$pdf->Cell(5,1,"",'L,B',0,'',0); 
	$pdf->Cell(5,1,"",'L,B',0,'',0); 
	$pdf->Cell(5,1,"",'L,B',0,'',0); 
	$pdf->Cell(5,1,"",'L,B,R',0,'',0); 
	$pdf->Cell(6,1,"",'R',0,'',0); 
	$pdf->Cell(38,1,"",'R',0,'',0); 
	$pdf->Cell(2,1,"",'R',0,'',0); 
	$pdf->Ln();
	
	//LEFT
	$pdf->Cell(3,4,"",'L',0,'',0);
	$pdf->Cell(46,4,"nalogodavca:",'',0,'',0); 
	$pdf->Cell(24,4,"",'L,R',0,'',0);  
	$pdf->Cell(2,4,"",'R',0,'',0); 
	//RIGHT
	$pdf->Cell(60,4,"",'R',0,'',0); 
	$pdf->Cell(3,4,"",'',0,'',0); 
	$pdf->Cell(7,4,"Do:",'',0,'',0); 
	$pdf->Cell(3,4,"",'L,B',0,'',0); 
	$pdf->Cell(3,4,"",'L,B',0,'',0); 
	$pdf->Cell(3,4,"/",'L,B',0,'',0); 
	$pdf->Cell(3,4,"",'L,B',0,'',0); 
	$pdf->Cell(3,4,"",'L,B',0,'',0); 
	$pdf->Cell(3,4,"/",'L,B',0,'',0); 
	$pdf->Cell(3,4,"",'L,B',0,'',0); 
	$pdf->Cell(3,4,"",'L,B,R',0,'',0); 
	$pdf->Cell(4,4,"",'R',0,'',0); 
	$pdf->Cell(2,4,"",'R',0,'',0); 
	$pdf->Ln();
	
	//LEFT
	$pdf->Cell(3,3,"",'L',0,'',0);
	$pdf->Cell(46,3,"",'',0,'',0); 
	$pdf->Cell(24,3,"Pečat Banke",'L,R',0,'C',0); 
	$pdf->Cell(2,3,"",'R',0,'',0); 
	//RIGHT
	$pdf->Cell(60,3,"",'R',0,'',0); 
	$pdf->Cell(38,3,"",'R,B',0,'',0); 
	$pdf->Cell(2,3,"",'R',0,'',0); 
	$pdf->Ln();
	
	//LEFT
	$pdf->Cell(3,2,"",'L',0,'',0);
	$pdf->Cell(46,2,"",'',0,'',0); 
	$pdf->Cell(24,2,"",'L,R',0,'',0); 
	$pdf->Cell(2,2,"",'R',0,'',0); 
	//RIGHT
	$pdf->Cell(24,2,"",'',0,'',0); 
	$pdf->Cell(18,2,"",'',0,'',0); 
	$pdf->Cell(56,2,"Budžetska",'R',0,'',0); 
	$pdf->Cell(2,2,"",'R',0,'',0); 
	$pdf->Ln();
	
	//LEFT
	$pdf->Cell(3,5,"",'L',0,'',0);
	$pdf->Cell(46,5,"",'',0,'',0); 
	$pdf->Cell(24,5,"",'L,R',0,'',0); 
	$pdf->Cell(2,5,"",'R',0,'',0); 
	//RIGHT
	$pdf->Cell(24,5,"Opština",'R',0,'',0); 
	$pdf->Cell(5,5,"",'L,B',0,'',0); 
	$pdf->Cell(5,5,"",'L,B',0,'',0); 
	$pdf->Cell(5,5,"",'L,R,B',0,'',0); 
	$pdf->Cell(3,5,"",'',0,'',0); 
	$pdf->Cell(22,5,"organizacija",'',0,'',0); 
	$pdf->Cell(4.5,5,"",'L,B,T',0,'',0); 
	$pdf->Cell(4.5,5,"",'L,B,T',0,'',0); 
	$pdf->Cell(4.5,5,"",'L,B,T',0,'',0); 
	$pdf->Cell(4.5,5,"",'L,B,T',0,'',0); 
	$pdf->Cell(4.5,5,"",'L,B,T',0,'',0); 
	$pdf->Cell(4.5,5,"",'L,B,T',0,'',0); 
	$pdf->Cell(4.5,5,"",'L,B,R,T',0,'',0); 
	$pdf->Cell(2.5,5,"",'R',0,'',0); 
	$pdf->Cell(2,5,"",'R',0,'',0); 
	$pdf->Ln();
	
	//LEFT
	$pdf->SetFont('DejaVuSerif','',10);
	$pdf->Cell(3,5,"",'L',0,'',0);
	$pdf->Cell(46,5,"Potpis",'',0,'',0); 
	$pdf->Cell(24,5,"",'L,R',0,'',0); 
	$pdf->Cell(2,5,"",'R',0,'',0); 
	//RIGHT
	$pdf->Cell(98,5,"",'R',0,'',0); 
	$pdf->Cell(2,5,"",'R',0,'',0); 
	$pdf->Ln();
	
	//LEFT
	$pdf->Cell(3,5,"",'L',0,'',0);
	$pdf->Cell(46,5,"ovlaštenog lica:",'',0,'',0); 
	$pdf->Cell(24,5,"",'L,R',0,'',0); 
	$pdf->Cell(2,5,"",'R',0,'',0); 
	//RIGHT
	$pdf->Cell(24,5,"Poziv na broj",'',0,'',0); 
	$pdf->Cell(3,5,"",'L,B',0,'',0); 
	$pdf->Cell(3,5,"",'L,B',0,'',0); 
	$pdf->Cell(3,5,"",'L,B',0,'',0); 
	$pdf->Cell(3,5,"",'L,B',0,'',0); 
	$pdf->Cell(3,5,"",'L,B',0,'',0); 
	$pdf->Cell(3,5,"",'L,B',0,'',0); 
	$pdf->Cell(3,5,"",'L,B',0,'',0); 
	$pdf->Cell(3,5,"",'L,B',0,'',0); 
	$pdf->Cell(3,5,"",'L,B',0,'',0); 
	$pdf->Cell(3,5,"",'L,B,R',0,'',0); 
	$pdf->Cell(44,5,"",'R',0,'',0); 
	$pdf->Cell(2,5,"",'R',0,'',0); 
	$pdf->Ln();
	
	//LEFT
	$pdf->Cell(3,2,"",'L',0,'',0);
	$pdf->Cell(46,2,"",'',0,'',0); 
	$pdf->Cell(24,2,"",'L,R,B',0,'',0); 
	$pdf->Cell(2,2,"",'',0,'',0); 
	//RIGHT
	$pdf->Cell(98,2,"",'B,L,R',0,'',0); 
	$pdf->Cell(2,2,"",'R',0,'',0); 
	$pdf->Ln();
	
	//LEFT
	$pdf->Cell(3,3,"",'L,B',0,'',0);
	$pdf->Cell(30,3,"",'B',0,'',0); 
	$pdf->Cell(42,3,"",'B',0,'',0); 
	//RIGHT
	$pdf->Cell(98,3,"",'B',0,'',0); 
	$pdf->Cell(2,3,"",'R,B',0,'',0); 
	$pdf->Ln();
	
	$filename="files/ugovori_uplatnice_dipl/".$uplatnica_putanja;
	
	//$pdf->Output();
	$pdf->Output($filename,'F');
	
	return $uplatnica_putanja;
}

function createUplatnicaSRB($predracun_id){

	Global $db;
	Global $logged_employee_id;
	Global $vrsta_dokumenta;
	$vrsta_dokumenta = "";
	
	$datetime = date('Y-m-d H:i:s');
	$day = date('d');
	$mjesec = date('m');
	$godina = date('y');
	$danas = date('d.m.Y');
	
	$get_predracun = $db->prepare("
					SELECT pr_broj_predracuna, nk.id_broj_nd_kandidata, nk.ime_nd_kandidata, nk.prezime_nd_kandidata, nk.ulica_nd_kandidata, nk.postanski_broj_nd_kandidata, nk.grad_nd_kandidata, pr_rata, nk.vrsta_ugovora_nd_kandidata, pr_vrijednost_RSD, pr_vrijednost_EUR, pr_file, pr_domaca_valuta, jmbg_nd_kandidata, pr_naplata_preko
					FROM idk_predracuni
					JOIN idk_nd_kandidata nk
					ON nk.id_broj_nd_kandidata = pr_kandidat_id
					WHERE pr_id = :pr_id
					");

	$get_predracun->execute(array(
						':pr_id' => $predracun_id));

	$predracun_row = $get_predracun->fetch();
	
	$broj_predracuna = $predracun_row["pr_broj_predracuna"];
	$broj_predracuna_ispis = str_replace("-","",$broj_predracuna);
	$kandidat_id = $predracun_row["id_broj_nd_kandidata"];
	$ime = $predracun_row["ime_nd_kandidata"];
	$prezime = $predracun_row["prezime_nd_kandidata"];
	$ulica = $predracun_row["ulica_nd_kandidata"];
	$pbroj = $predracun_row["postanski_broj_nd_kandidata"];
	$grad = $predracun_row["grad_nd_kandidata"];
	$pr_rata = $predracun_row["pr_rata"];
	$pr_vrijednost_RSD = $predracun_row["pr_vrijednost_RSD"];
	
	$pr_vrijednost_RSD_f = number_format($pr_vrijednost_RSD, 0, '', '.');
	$pr_vrijednost_EUR = $predracun_row["pr_vrijednost_EUR"];
	$pr_file = $predracun_row["pr_file"];
	$vrsta_ugovora = $predracun_row["vrsta_ugovora_nd_kandidata"];
	$pr_domaca_valuta = $predracun_row["pr_domaca_valuta"];
	$jmbg = $predracun_row["jmbg_nd_kandidata"];
	$pr_naplata_preko = $predracun_row["pr_naplata_preko"];
	$ime_pre = $ime." ".$prezime;
	$adresa = $ulica.", ".$pbroj." ".$grad;
	if(strlen($adresa) > 36){
		$ime_pre = $ime_pre.", ".$ulica;
		$adresa = $pbroj." ".$grad;
		//$adresa = substr($adresa,0,35)."...";
	}
	
	if($pr_naplata_preko == 1){
		// $adresa_firme1 = "Jobstep Int GmbH";// EMIR BENJO stari naziv
		$adresa_firme1 = "Jobstep International d.o.o.";
		$adresa_firme2 = "";
	}else{
		$adresa_firme1 = "Jobstep International d.o.o.";
		$adresa_firme2 = "";
		// $adresa_firme2 = "Kneza Miloša 78, 11000 Beograd-Savski Venac";
	}
	
	switch($pr_rata){
		case 1:
			$rata_txt = "I rata";
		break;
		case 2:
			$rata_txt = "II rata";
		break;
		case 3:
			$rata_txt = "III rata";
		break;
		case 4:
			$rata_txt = "IV rata";
		break;
		case 5:
			$rata_txt = "V rata";
		break;
		default:
			$rata_txt = "";
	}
	
	$brojac_uplatnice = createBrojUplatnice("Srbija");
	
	$uplatnica_putanja = "UPS-".$brojac_uplatnice."-".$mjesec."-".$godina.".pdf";
	
	//INSERT UPLATNICE
	$insert_ugovor = $db->prepare("	
				INSERT INTO idk_nd_kandidata_dokumenti	
				(naziv_dokument_nd,  naziv_dokument_ostali_nd, id_kandidata_dokument_nd, vrijeme_dodavanja_dokument_nd, dodao_zaposlenik_dokument_nd, tip_dokumenta, broj_rate)	
				VALUES	
				(:naziv_dokument_nd,:naziv_dokument_ostali_nd,:id_kandidata_dokument_nd,:vrijeme_dodavanja_dokument_nd,:dodao_zaposlenik_dokument_nd,:tip_dokumenta,:broj_rate)	
				");	
	$insert_ugovor->execute(array(	
				':naziv_dokument_nd' => $uplatnica_putanja,	
				':naziv_dokument_ostali_nd' => "uplatnica",	
				':id_kandidata_dokument_nd' => $kandidat_id,	
				':vrijeme_dodavanja_dokument_nd' => $datetime,	
				':dodao_zaposlenik_dokument_nd' => $logged_employee_id,	
				':tip_dokumenta' => 2,
				':broj_rate' => $pr_rata
				));
	
	$pdf = new PDF('P','mm','A4');
	$pdf->AliasNbPages();
	
	$pdf->AddFont('DejaVuSerif','B','DejaVuSerifCondensed-Bold.ttf',true);
	$pdf->AddFont('DejaVuSerif','BI','DejaVuSerifCondensed-BoldItalic.ttf',true);

	$pdf->AddPage();
	
	$pdf->SetFont('DejaVu','B',16);
	
	$pdf->Cell(3,10,"",'L,T',0,'R',0);
	$pdf->Cell(170,10,"НАЛОГ ЗА УПЛАТУ",'T',0,'R',0);
	$pdf->Cell(2,10,"",'T,R',0,'R',0);
	$pdf->Ln();
	
	$pdf->SetFont('DejaVu','',8);
	
	//L
	$pdf->Cell(3,4,"",'L',0,'',0);
	$pdf->Cell(85,4,"платилац",'',0,'',0);
	//R
	$pdf->Cell(7,4,"",'',0,'',0);
	$pdf->Cell(78,4,"шифра",'',0,'',0);
	$pdf->Cell(2,4,"",'R',0,'',0);
	$pdf->Ln();
	
	//L
	$pdf->Cell(3,3,"",'L',0,'',0);
	$pdf->Cell(80,3,"",'L,T,R',0,'',0);
	$pdf->Cell(5,3,"",'R',0,'',0);
	//R
	$pdf->Cell(7,3,"",'',0,'',0);
	$pdf->Cell(12,3,"плаћања",'',0,'',0);
	$pdf->Cell(5,3,"",'',0,'',0);
	$pdf->Cell(12,3,"валута",'',0,'',0);
	$pdf->Cell(8,3,"",'',0,'',0);
	$pdf->Cell(41,3,"износ ",'',0,'',0);
	$pdf->Cell(2,3,"",'R',0,'',0);
	$pdf->Ln();
	
	//L
	$pdf->Cell(3,1,"",'L',0,'',0);
	$pdf->SetFont('DejaVu','',10);
	$pdf->Cell(80,1,$ime_pre,'L,R',0,'',0);
	$pdf->Cell(5,1,"",'R',0,'',0);
	//R
	$pdf->Cell(7,1,"",'',0,'',0);
	$pdf->Cell(78,1,"",'',0,'',0);
	$pdf->Cell(2,1,"",'R',0,'',0);
	$pdf->Ln();
	
	
	//L
	$pdf->Cell(3,7,"",'L',0,'',0);
	$pdf->Cell(80,7,"",'L,R',0,'',0);
	$pdf->Cell(5,7,"",'R',0,'',0);
	//R
	$pdf->SetFont('DejaVu','',12);
	$pdf->Cell(8,7,"",'',0,'',0);
	$pdf->Cell(12,7,"121",'T,R,B,L',0,'C',0);
	$pdf->Cell(5,7,"",'',0,'',0);
	$pdf->Cell(12,7,"RSD",'T,L,R,B',0,'C',0);
	$pdf->Cell(8,7,"",'',0,'',0);
	$pdf->Cell(40,7,$pr_vrijednost_RSD_f.",00",'T,L,B,R',0,'C',0);
	$pdf->Cell(2,7,"",'R',0,'',0);
	$pdf->Ln();
	
	//L
	$pdf->SetFont('DejaVu','',10);
	$pdf->Cell(3,2,"",'L',0,'',0);
	$pdf->Cell(80,2,$adresa,'L,R',0,'',0);
	$pdf->SetFont('DejaVu','',8);
	$pdf->Cell(5,2,"",'R',0,'',0);
	//R
	$pdf->Cell(7,2,"",'',0,'',0);
	$pdf->Cell(78,2,"",'',0,'',0);
	$pdf->Cell(2,2,"",'R',0,'',0);
	$pdf->Ln();
	
	//L
	$pdf->Cell(3,3,"",'L',0,'',0);
	$pdf->Cell(80,3,"",'L,R,B',0,'',0);
	$pdf->SetFont('DejaVu','',8);
	$pdf->Cell(5,3,"",'R',0,'',0);
	//R
	$pdf->Cell(7,3,"",'',0,'',0);
	$pdf->Cell(78,3,"рачун примаоца",'',0,'',0);
	$pdf->Cell(2,3,"",'R',0,'',0);
	$pdf->Ln();
	
	//L
	$pdf->Cell(3,1,"",'L',0,'',0);
	$pdf->Cell(85,1,"",'R',0,'',0);
	//R
	$pdf->Cell(7,1,"",'',0,'',0);
	$pdf->Cell(78,1,"",'',0,'',0);
	$pdf->Cell(2,1,"",'R',0,'',0);
	$pdf->Ln();
	
	//L
	$pdf->Cell(3,5,"",'L',0,'',0);
	$pdf->Cell(85,5,"сврха уплате",'R',0,'',0);
	//R
	$pdf->Cell(8,5,"",'',0,'',0);
	$pdf->SetFont('DejaVu','',12);
	$pdf->Cell(77,5,"220-0000000142731-57",'L,R,T',0,'',0); // EMIR BENJO
	$pdf->Cell(2,5,"",'R',0,'',0);
	$pdf->Ln();
	
	//L
	$pdf->Cell(3,2,"",'L',0,'',0);
	$pdf->Cell(80,2,"",'L,R,T',0,'',0);
	$pdf->Cell(5,2,"",'R',0,'',0);
	//R
	$pdf->Cell(8,2,"",'',0,'',0);
	$pdf->Cell(77,2,"",'L,R,B',0,'',0);
	$pdf->Cell(2,2,"",'R',0,'',0);
	$pdf->Ln();
	
	//L
	$pdf->Cell(3,2,"",'L',0,'',0);
	$pdf->SetFont('DejaVu','',10);
	$pdf->Cell(80,2,"",'L,R',0,'',0);
	$pdf->SetFont('DejaVu','',8);
	$pdf->Cell(5,2,"",'R',0,'',0);
	//R
	$pdf->Cell(7,2,"",'',0,'',0);
	$pdf->Cell(78,2,"",'',0,'',0);
	$pdf->Cell(2,2,"",'R',0,'',0);
	$pdf->Ln();
	
	//L
	$pdf->Cell(3,3,"",'L',0,'',0);
	$pdf->Cell(80,3,"",'L,R',0,'',0);
	$pdf->Cell(5,3,"",'R',0,'',0);
	//R
	$pdf->Cell(7,3,"",'',0,'',0);
	$pdf->Cell(78,3,"модел и позив на број (одобрење)",'',0,'',0);
	$pdf->Cell(2,3,"",'R',0,'',0);
	$pdf->Ln();
	
	//L
	$pdf->SetFont('DejaVu','',10);
	$pdf->Cell(3,1,"",'L',0,'',0);
	$pdf->Cell(80,1,$broj_predracuna,'L,R',0,'',0);
	$pdf->Cell(5,1,"",'R',0,'',0);
	//R
	$pdf->Cell(7,1,"",'',0,'',0);
	$pdf->Cell(78,1,"",'',0,'',0);
	$pdf->Cell(2,1,"",'R',0,'',0);
	$pdf->Ln();
	
	//L
	$pdf->Cell(3,3,"",'L',0,'',0);
	$pdf->SetFont('DejaVu','',10);
	$pdf->Cell(80,3,"",'L,R',0,'',0);
	$pdf->Cell(5,3,"",'R',0,'',0);
	//R
	$pdf->Cell(8,3,"",'',0,'',0);
	$pdf->Cell(10,3,"",'L,T,R',0,'',0);
	$pdf->Cell(7,3,"",'',0,'',0);
	$pdf->Cell(60,3,"",'L,T,R',0,'',0);
	$pdf->Cell(2,3,"",'R',0,'',0);
	$pdf->Ln();
	
	//L
	$pdf->Cell(3,2,"",'L',0,'',0);
	$pdf->SetFont('DejaVu','',10);
	$pdf->Cell(80,2,"",'L,R',0,'',0);
	$pdf->Cell(5,2,"",'R',0,'',0);
	//R
	$pdf->Cell(8,2,"",'',0,'',0);
	$pdf->Cell(10,2,"",'L,R',0,'',0);
	$pdf->Cell(7,2,"",'',0,'',0);
	$pdf->Cell(60,2,"",'L,R',0,'',0);
	$pdf->Cell(2,2,"",'R',0,'',0);
	$pdf->Ln();
	
	//L
	$pdf->Cell(3,2,"",'L',0,'',0);
	$pdf->Cell(80,2,"",'L,R',0,'',0);
	$pdf->Cell(5,2,"",'R',0,'',0);
	//R
	$pdf->Cell(8,2,"",'',0,'',0);
	$pdf->Cell(10,2,"",'L,R,B',0,'',0);
	$pdf->Cell(7,2,"",'',0,'',0);
	$pdf->Cell(60,2,"",'L,R,B',0,'',0);
	$pdf->Cell(2,2,"",'R',0,'',0);
	$pdf->Ln();
	
	//L
	$pdf->Cell(3,1,"",'L',0,'',0);
	$pdf->Cell(80,1,"",'L,R,B',0,'',0);
	$pdf->Cell(5,1,"",'R',0,'',0);
	//R
	$pdf->Cell(8,1,"",'',0,'',0);
	$pdf->Cell(77,1,"",'',0,'',0);
	$pdf->Cell(2,1,"",'R',0,'',0);
	$pdf->Ln();
	
	//L
	$pdf->Cell(3,1,"",'L',0,'',0);
	$pdf->Cell(85,1,"",'R',0,'',0);
	//R
	$pdf->Cell(7,1,"",'',0,'',0);
	$pdf->Cell(78,1,"",'',0,'',0);
	$pdf->Cell(2,1,"",'R',0,'',0);
	$pdf->Ln();
	
	//L
	$pdf->Cell(3,5,"",'L',0,'',0);
	$pdf->SetFont('DejaVu','',8);
	$pdf->Cell(85,5,"прималац",'R',0,'',0);
	//R
	$pdf->Cell(8,5,"",'',0,'',0);
	$pdf->Cell(77,5,"",'',0,'',0);
	$pdf->Cell(2,5,"",'R',0,'',0);
	$pdf->Ln();
	
	//L
	$pdf->Cell(3,2,"",'L',0,'',0);
	$pdf->Cell(80,2,"",'L,R,T',0,'',0);
	$pdf->Cell(5,2,"",'R',0,'',0);
	//R
	$pdf->Cell(87,2,"",'R',0,'',0);
	$pdf->Ln();
	
	//L
	$pdf->Cell(3,2,"",'L',0,'',0);
	$pdf->SetFont('DejaVu','',10);
	$pdf->Cell(80,2,$adresa_firme1,'L,R',0,'',0);
	$pdf->Cell(5,2,"",'R',0,'',0);
	//R
	$pdf->Cell(87,2,"",'R',0,'',0);
	$pdf->Ln();
	
	//L
	$pdf->Cell(3,4,"",'L',0,'',0);
	$pdf->Cell(80,4,"",'L,R',0,'',0);
	$pdf->Cell(5,4,"",'R',0,'',0);
	//R
	$pdf->Cell(87,4,"",'R',0,'',0);
	$pdf->Ln();
	
	//L
	$pdf->Cell(3,5,"",'L',0,'',0);
	$pdf->Cell(80,5,$adresa_firme2,'L,R',0,'',0);
	$pdf->Cell(5,5,"",'R',0,'',0);
	//R
	$pdf->Cell(87,5,"",'R',0,'',0);
	$pdf->Ln();
	
	//L
	$pdf->Cell(3,3,"",'L',0,'',0);
	$pdf->Cell(80,3,"",'L,R,B',0,'',0);
	$pdf->Cell(5,3,"",'R',0,'',0);
	//R
	$pdf->Cell(87,3,"",'R',0,'',0);
	$pdf->Ln();
	
	$pdf->Cell(3,5,"",'L',0,'',0);
	$pdf->Cell(85,5,"",'',0,'',0);
	$pdf->Cell(62,5,"",'',0,'',0);
	$pdf->Cell(5,5,"",'B,T,L,R',0,'',0);
	$pdf->Cell(18,5,"",'',0,'',0);
	$pdf->Cell(2,5,"",'R',0,'',0);
	$pdf->Ln();
	
	$pdf->Cell(3,2,"",'L',0,'',0);
	$pdf->Cell(85,2,"",'',0,'',0);
	$pdf->Cell(60,2,"",'',0,'',0);
	$pdf->SetFont('DejaVu','',7);
	$pdf->Cell(5,2,"хитно",'',0,'',0);
	$pdf->Cell(20,2,"",'',0,'',0);
	$pdf->Cell(2,2,"",'R',0,'',0);
	$pdf->Ln();
	
	$pdf->Cell(3,2,"",'L',0,'',0);
	$pdf->Cell(50,2,"",'B',0,'',0);
	$pdf->Cell(35,2,"",'',0,'',0);
	$pdf->Cell(85,2,"",'',0,'',0);
	$pdf->Cell(2,2,"",'R',0,'',0);
	$pdf->Ln();
	
	$pdf->Cell(2,4,"",'L',0,'',0);
	$pdf->SetFont('DejaVu','',8);
	$pdf->Cell(51,4,"потпис платиоца",'',0,'',0);
	$pdf->Cell(35,4,"",'',0,'',0);
	$pdf->Cell(85,4,"",'',0,'',0);
	$pdf->Cell(2,4,"",'R',0,'',0);
	$pdf->Ln();
	
	$pdf->Cell(175,7,"",'L,R',0,'',0);
	$pdf->Ln();
	
	$pdf->Cell(40,4,"",'L',0,'',0);
	$pdf->Cell(40,4,"место и датум пријема",'T',0,'',0);
	$pdf->Cell(15,4,"",'',0,'',0);
	$pdf->Cell(30,4,"датум извршења",'T',0,'',0);
	$pdf->Cell(50,4,"",'R',0,'',0);
	$pdf->Ln();
	
	$pdf->Cell(175,8,"",'L,B,R',0,'',0);
	$pdf->Ln();
	
	$filename="files/ugovori_uplatnice_dipl/".$uplatnica_putanja;
	
	//$pdf->Output();
	$pdf->Output($filename,'F');
	
	return $uplatnica_putanja;
}

function createInfoListSRB(){
	Global $db;
	Global $logged_employee_id;
	Global $vrsta_dokumenta;
	$vrsta_dokumenta = "ugovor_rs";
	Global $valutaCheck;
	$valutaCheck = "RSD";
	
	$danas = date('d.m.Y');
	
	$pdf = new PDF('P','mm','A4');
	

	$pdf->AliasNbPages();
	$pdf->SetDisplayMode('real', 'single');

	$pdf->AddPage();

	// Add a Unicode font (uses UTF-9)
	$pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
	$pdf->AddFont('DejaVu','B','DejaVuSansCondensed-Bold.ttf',true);
	$pdf->AddFont('DejaVuSerif','','DejaVuSerifCondensed.ttf',true);
	$pdf->AddFont('DejaVuSerif','B','DejaVuSerifCondensed-Bold.ttf',true);
	$pdf->AddFont('DejaVuSerif','BI','DejaVuSerifCondensed-BoldItalic.ttf',true);

	$pdf->SetLeftMargin(20);
	$pdf->Ln(10);
	$pdf->SetFont('DejaVuSerif','',12);
	
	$pdf->MultiCell(170,7,'INFORMATIVNI LIST – NOSTRIFIKACIJA DIPLOME U NEMAČKOJ','','L',0);
	$pdf->Ln(10);
	
	$pdf->SetFont('DejaVuSerif','',11);
	$pdf->MultiCell(170,5,'Nostrifikacija diplome podrazumeva proces izjednačavanja diplome stečene izvan EU sa nemačkim školskim standardom.','','',0);
	$pdf->MultiCell(170,5,'Na osnovu praktičnog i teoretskog dela nastave se upoređuje da li se diploma može izjednačiti sa nemačkim zanimanjem.','','',0);
	$pdf->MultiCell(170,5,'Od 01.03.2020. godine je na snagu stupio Zakon o useljavanju stručnog kadra u Nemačku, koji podrazumeva da svaki kandidat koji ima neko stručno zvanje može doći u Nemačku i zaposliti se u struci.','','',0);
	$pdf->MultiCell(170,5,'Kako bi se kandidat mogao zaposliti u struci neophodno je da uradi izjednačenje odnosno NOSTRIFIKACIJU diplome.','','',0);
	$pdf->MultiCell(170,5,'Kako izgleda postupak nostrifikacije diplome?','','',0);
	$pdf->MultiCell(170,5,'Nostrifikacija diplome se vrši u jednoj od preko 70 ustanova u Nemačkoj koje obrađuju zahteve za nostrifikaciju diplome.','','',0);
	$pdf->MultiCell(170,5,'Svaka pokrajna i svako zanimanje imaju različitu ustanovu u kojoj se izdaju potvrde o istovetnosti diplome, a svaka ustanova ima različit postupak pri vršenju izjednačavanja diplome- stoga je neophodno nakon izbora odgovarajuće ustanove proučiti i način nostrifikacije diplome, i u skladu sa tim dostaviti neophodnu dokumentaciju.','','',0);
	$pdf->MultiCell(170,5,'Kako biste izbegli dodatne napore i odugovlačenje procesa nostrifikacije diplome, ovaj postupak jednostavno poverite nama.','','',0);
	$pdf->MultiCell(170,5,'Naše stručno osoblje će za Vas odabrati odgovarajuću ustanovu, pripremiti svu potrebnu dokumentaciju i biti u kontaktu sa nadležnom ustanovom sve do okončanja postupka, te Vama davati informacije o statusu Vašeg predmeta.','','',0);
	$pdf->MultiCell(170,5,'Neophodno je da po izrađenom spisku dostavite dokumente, a naše kvalifikovano osoblje će završiti čitav postupak, počevši od prevoda dokumentacije do dostavljanja konačnog dokumenta o nostrifikaciji diplome na Vašu adresu.','','',0);
	$pdf->Ln(7);
	$pdf->MultiCell(170,5,'Da bismo obezbedili potpuno poštenu i transparentnu obradu Vaših ličnih podataka, pored prethodno navedenih informacija, u trenutku prikupljanja podataka o ličnosti koji se odnose na Vas, u skladu sa Zakonom o zaštiti podataka o ličnosti, obaveštavamo Vas i o sledećem: ','','',0);
	$pdf->Ln();
	$pdf->MultiCell(170,5,'-davanje datih podataka o ličnosti je neophodno potrebno za obavljanje usluge posredovanja u postupku nostrifikacije diplome; ','','',0);
	$pdf->Ln();
	$pdf->AddPage();
	$pdf->MultiCell(170,5,'-ukoliko ne date svoje lične podatke koji su neophodno potrebni za obavljanje usluge posredovanja u postupku nostrifikacije diplome neće biti moguće da se izvrši usluga posredovanja u postupku nostrifikacije diplome; ','','',0);
	$pdf->Ln();
	$pdf->MultiCell(170,5,'-rok čuvanja Vaših ličnih podataka određuje se u skladu sa propisima kojima se uređuje rok čuvanja ovakvih podataka i kriterijumima rukovaoca za određivanje roka čuvanja ove vrste podataka;','','',0);
	$pdf->Ln();
	$pdf->MultiCell(170,5,'-imate pravo da, u skladu sa članom 26 Zakona o zaštiti podataka o ličnosti, od rukovaoca zahtevate pristup podacima, kao i pravo na ispravku ili brisanje Vaših podataka o ličnosti, u skladu sa čl. 29 i 30 Zakona, pravo na ograničenje obrade, u skladu sa članom 31 Zakona, pravo na prenosivost podataka, u skladu sa članom 36 Zakona i pravo na prigovor, u skladu sa članom 37 Zakona; ','','',0);
	$pdf->Ln();
	$pdf->MultiCell(170,5,'-ako smatrate da je to opravdano u odnosu na posebnu situaciju u kojoj se nalazite, kao lice na koje se podaci odnose, imate pravo da u svakom trenutku podnesete rukovaocu prigovor na obradu Vaših podataka o ličnosti, a kada se obrada podataka vrši u skladu sa članom 12 stav 1 tač. 5) i 6) Zakona o zaštiti podataka o ličnosti, uključujući i profilisanje koje se zasniva na tim odredbama Zakona, u kom slučaju je rukovalac dužan da prekine sa obradom Vaših podataka o ličnosti, osim ako je rukovalac predočio da postoje zakonski razlozi za obradu datih podataka koji pretežu nad Vašim interesima, pravima ili slobodama kao lica na koje se podaci odnose ili su u vezi sa donošenjem, ostvarivanjem ili odbranom pravnog zahteva; ','','',0);
	$pdf->Ln();
	$pdf->MultiCell(170,5,'-ako želite da podnesete prigovor, ili imate neko pitanje, dilemu ili nejasnoću vezano za obradu Vaših podataka o ličnosti, možete se obratiti rukovaocu pisanim putem na njegove kontakt podatke, koji su istaknuti i navedeni na internet sajtu i/ili oglasnoj tabli rukovaoca (pružaoca usluge- info@job-step.rs); ','','',0);
	$pdf->Ln();
	$pdf->MultiCell(170,5,'-imate pravo da podnesete pritužbu Povereniku za informacije od javnog značaja i zaštitu podataka o ličnosti, u skladu sa članom 82 Zakona o zaštiti podataka o ličnosti, ako smatrate da se obrada podataka o Vašoj ličnosti vrši suprotno odredbama Zakona;','','',0);
	$pdf->Ln();
	$pdf->MultiCell(170,5,'-imate parvo na sudsku zaštitu, u skladu sa članom 84 Zakona o zaštiti podataka o ličnosti, ako smatrate da Vam je suprotno Zakonu radnjom obrade Vaših podataka o ličnosti od strane rukovaoca povređeno neko pravo propisano Zakonom, a podnošenje tužbe sudu ne utiče na pravo da pokrenete druge postupke upravne ili sudske zaštite; ','','',0);
	$pdf->Ln();
	$pdf->AddPage();
	$pdf->MultiCell(170,5,'-automatizovano donošenje odluke, uključujući profilisanje, kao bilo koji oblik automatizovane obrade koji se koristi da bi se ocenilo određeno svojstvo ličnosti, može se primeniti prema Vama samo u izuzetnim slučajevima i pod uslovima i na način iz člana 38 Zakona o zaštiti podataka o ličnosti, kao i skladno svrsi Zakona i njegovim odredbama koje se odnose na zaštitu ličnosti čiji se podaci obrađuju. ','','',0);
	$pdf->Ln(15);
	
	
	
	$pdf->Cell(120,7,'',0,'','L',0);
	$pdf->Cell(50,7,'ZA KLIJENTA (KORISNIKA USLUGE): ','','','R',0);
	$pdf->Ln();
	$pdf->Cell(120,7,'',0,'','L',0);
	$pdf->Cell(50,7,'______________________________',0,'','R',0);
	$pdf->Ln();
	$pdf->Cell(120,7,'',0,'','L',0);
	$pdf->Cell(50,7,'(mesto i datum)',0,'','R',0);
	$pdf->Ln();
	$pdf->Cell(120,7,'',0,'','L',0);
	$pdf->Cell(50,7,'______________________________',0,'','R',0);
	$pdf->Ln();
	$pdf->Cell(120,7,'',0,'','L',0);
	$pdf->Cell(50,7,'(ime, prezime i potpis)',0,'','R',0);
	$pdf->Ln();
	
	
	// $pdf->Cell(120,7,'ZA PRUŽAOCA USLUGE: ',0,'','L',0);
	// $pdf->Cell(50,7,'ZA KORISNIKA USLUGE: ','','','L',0);
	// $pdf->Ln();
	// $pdf->Cell(120,7,'______________________________',0,'','L',0);
	// $pdf->Cell(50,7,'______________________________',0,'','L',0);
	// $pdf->Ln();
	// $pdf->Cell(120,7,'(mjesto i datum)',0,'','L',0);
	// $pdf->Cell(50,7,'(mjesto i datum)',0,'','L',0);
	// $pdf->Ln();
	// $pdf->Cell(120,7,'______________________________',0,'','L',0);
	// $pdf->Cell(50,7,'______________________________',0,'','L',0);
	// $pdf->Ln();
	// $pdf->Cell(120,7,'(potpis i pečat)',0,'','L',0);
	// $pdf->Cell(50,7,'(ime, prezime i potpis)',0,'','L',0);
	// $pdf->Ln();


	//$pdf->Output();
}