<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
include('mail/PHPMailerAutoload.php');
// //require('fpdf.php');
// require('tfpdf.php');
// include("includes/functions.php");

Global $logged_employee_id;

class PDF_gutschrift extends tFPDF{
	
		function GetPageWidth()
		{
			// Get current page width
			return $this->w;
		}

		function GetPageHeight()
		{
			// Get current page height
			return $this->h;
		}

	// Page header
	function Header(){
		
		global $datum_uplate;
		
		$this->AddFont('DejaVuSerif','','DejaVuSerifCondensed.ttf',true);
		$this->AddFont('DejaVuSerif','B','DejaVuSerifCondensed-Bold.ttf',true);
		$this->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
		$this->AddFont('DejaVu','B','DejaVuSansCondensed-Bold.ttf',true);
		
				
				// Logo
				$this->Image('images/jobstep-logo-de.png',10,10,40);
			
				// Arial bold 15
				$this->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
				$this->AddFont('DejaVu','B','DejaVuSansCondensed-Bold.ttf',true);
				$this->SetFont('DejaVu','',11);
				// Move to the right
				$this->Cell(10);
				
				// Title
				$this->SetFont('DejaVu','B',11);
				$this->SetLeftMargin(10);
				$this->Cell(200,5,'',0,0,'R',0);   $this->Ln();
				$this->Cell(120,5,'',0,0,'R',0);   $this->Cell(20,5,'Jobstep GmbH',0,0,'l',0); $this->Ln(); $this->SetFont('DejaVu','',11);
				$this->Cell(120,5,'',0,0,'R',0);   $this->Cell(20,5,'Boschstrasse 10',0,0,'l',0); $this->Ln();
				$this->Cell(120,5,'',0,0,'R',0);   $this->Cell(20,5,'73734 Esslingen am Neckar',0,0,'l',0); $this->Ln();
				$this->Cell(120,5,'',0,0,'R',0);   $this->Cell(20,5,'Datum: '.$datum_uplate,0,0,'l',0); $this->Ln();
			
				
			// Line break
			$this->Ln(40);
		
	}
	// Page footer
	function Footer(){
				// Position at 2 cm from bottom
				$this->SetY(-15);
				// Arial italic 8
				$this->SetFont('DejaVu','',7);
				
				
				$this->SetLeftMargin(20);
				// 1st row of the footer
				$this->Cell(60,3,'Geschäftsführer: Advan Ljubijankic ',0,0,'L',0);
				$this->Cell(60,3,'Bankverbindung: ProCredit Bank ',0,0,'L',0);
				$this->Cell(60,3,'Umsatzsteuer-Identifikationsnummer ',0,0,'L',0);
				$this->Ln();
				
				// 2nd row of the footer
				$this->Cell(60,3,'Registergericht: Amtsgericht Stuttgart',0,0,'L',0);
				$this->Cell(60,3,'IBAN: DE84 5021 0800 0058 0400 02',0,0,'L',0);
				$this->Cell(60,3,'DE320028750',0,0,'L',0);
				$this->Ln();
				
				// 3rd row of the footer
				$this->Cell(60,3,'HRB765169',0,0,'L',0);
				$this->Cell(60,3,'BIC: PRCBDEFFXXX',0,0,'L',0);
				$this->Ln();
			
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
function generate_partnerGutschrift($partner_id,$kandidat_id,$vrsta,$datum){
		
		global $db;
		global $datum_uplate;
		
		$datum_uplate = date('d.m.Y',strtotime($datum));
		if($kandidat_id  == "all"){ // ISPLATA ZA SVE KANDIDATE  (JEDNE VRSTE) ODREĐENOG PARTNERA 
							
							$ukupno_za_isplatiti = 0;
							$imena_kandidata = array();
							$provizija_po_kandidatu = array();
							$partnerName = getInfoPartnerPreporukaR($partner_id); // vraca ime i prezime partnera
							$adresa = getPartnerAdresaR($partner_id); // 3 kolone iz baze vezane za adresu, vraća kao niz
								
								$ulica = $adresa[0];
								$postanski = $adresa[1];
								$grad = $adresa[2];
								$drzava = $adresa[3];
								
								// $ulica = "Kapici 103";
								// $postanski = "77220";
								// $grad = "Cazin";
								// $drzava = "Bosna i Hercegovina";
								
								
							$read_query = $db->prepare("
									SELECT  jp_uplate_provizija, jp_uplate_kandidatid, jp_uplate_partnerid
									FROM idk_partner_uplate
									INNER JOIN idk_jobstep_partners ON jp_uplate_partnerid = idk_jobstep_partners.jp_id
									WHERE jp_uplate_partnerid = :jp_uplate_partnerid
									AND jp_uplate_vrsta = :jp_uplate_vrsta
									AND jp_uplate_datum = :jp_uplate_datum
									AND idk_jobstep_partners.jp_racun = 1
								"); // INNER JOIN RADI PROVJERE DA LI PARTNER IMA RAČUN 
							
							$read_query->execute(array(
										":jp_uplate_partnerid" => $partner_id,
										":jp_uplate_vrsta" => $vrsta,
										":jp_uplate_datum" => $datum,
										));
							$result = $read_query->fetchAll();
							foreach($result as $pojedinacna_provizija){ // get ukupan iznos uplate
							
								$ukupno_za_isplatiti += $pojedinacna_provizija['jp_uplate_provizija'] ;
								$provizija_po_kandidatu[] = $pojedinacna_provizija['jp_uplate_provizija'];
							}
							$provizija = $ukupno_za_isplatiti; //da ne mjenjam imena varijabli kroz čitavu stranicu koda 
							
							
							foreach($result as $kandidat_id){ // get imena kandidata za koje se partneru vrši isplata
								if($vrsta == "1"){
									$kandName_query = $db->prepare("SELECT kandidat_ime,kandidat_prezime 
																	FROM idk_kandidati 
																	WHERE kandidat_id = :kandidat_id");
									$kandName_query->execute(array(
										":kandidat_id" => $kandidat_id['jp_uplate_kandidatid']
										));
										
										
									$query_result = $kandName_query->fetch();
									$imena_kandidata[] = $query_result['kandidat_ime']." ".$query_result['kandidat_prezime'];
									
								}
								else
								{
									$kandName_query = $db->prepare("SELECT ime_nd_kandidata, prezime_nd_kandidata 
																	FROM idk_nd_kandidata 
																	WHERE 	id_broj_nd_kandidata = :id_broj_nd_kandidata");
									$kandName_query->execute(array(
										":id_broj_nd_kandidata" => $kandidat_id['jp_uplate_kandidatid']
										));
										
										
									$query_result = $kandName_query->fetch();
									$imena_kandidata[] = $query_result['ime_nd_kandidata']." ".$query_result['prezime_nd_kandidata'];
								}
							}	
								
								
								$pdf_naziv = "GS-".date('Y-m').uniqid()."-".$partner_id.".pdf"; // uniqueid može izazvati bug, koji bi brisao stari gutschrift i preko njega zapisao novi
								$pdf = new PDF_gutschrift();
								$pdf->AliasNbPages();
								$pdf->AddPage();
								
								
								
								
								// GUTSCHRIFT KLIJENT (PARTNER)
									$pdf->SetFont('DejaVu','B',11);
									$pdf->Cell(20,5,$partnerName,0,0,'l',0); $pdf->Ln(); $pdf->SetFont('DejaVu','',11);
									$pdf->Cell(20,5,$ulica,0,0,'l',0); $pdf->Ln();
									$pdf->Cell(20,5,$postanski." ".$grad,0,0,'l',0); $pdf->Ln();
									$pdf->Cell(20,5,$drzava,0,0,'l',0); $pdf->Ln();
								//
								
								$pdf->Ln(30); // ODVOJI INFO OD PARTNERA DOVOLJNO OD TABELE	
								// GUTSCHRIFT ID PLUS DATUM 
								$pdf->SetFont('DejaVu','B',11);
									 $pdf->Cell(190,5,'GUTSCHRIFT GS-'.date("Y-m"),0,0,'C'); 
									
								// hr line
								$pdf->Ln();

								$pdf->Cell(190,1,"",'B',0,'',0);

								$pdf->Ln(20); // ODVOJI HEADER DOVOLJNO OD SADRŽAJA PDFA	


								$pdf->SetFont('DejaVu','',9);
								// TABELA

								// TABLE HEAD
									$pdf->Cell(13,5,'Pos','T,L,B,R',0,'C');
									$pdf->Cell(13,5,'P-Nr','T,L,B,R',0,'C');
									$pdf->Cell(41,5,'Bezeichnung','T,L,B,R',0,'C');
									$pdf->Cell(31,5,' Beschreibung','T,L,B,R',0,'C');
									$pdf->Cell(15,5,'Menge','T,L,B,R',0,'C');
									$pdf->Cell(15,5,'Einheit','T,L,B,R',0,'C');
									$pdf->Cell(21,5,'Einzelpreis €','T,L,B,R',0,'C');
									$pdf->Cell(21,5,'UST%','T,L,B,R',0,'C');
									$pdf->Cell(20,5,'Betrag €','T,L,B,R',0,'C');

								//1. TABLE ROW SA ISCRTANIM GRANICAMA
								$pdf->Ln();

									$pdf->Cell(13,25,'1','T,L,B,R',0,'C');
									$pdf->Cell(13,25,'','T,L,B,R',0,'C');
									$pdf->Cell(41,25,'Empfelungsgeberprovision','T,L,B,R',0,'C');
									$pdf->Cell(31,25,'','B,L,R',0,'C');
									$pdf->Cell(15,25,'','T,L,B,R',0,'C');
									$pdf->Cell(15,25,'','T,L,B,R',0,'C');
									$pdf->Cell(21,25,'','T,L,B,R',0,'C');
									$pdf->Cell(21,25,'','T,L,B,R',0,'C');
									$pdf->Cell(20,25,''.$ukupno_za_isplatiti.',00','T,L,B,R',0,'C');

								//2. TABLE ROW BEZ ISCRTANIH GRANICA ( BETRAG (NETTO) )
								$pdf->Ln();

									$pdf->Cell(13,5,'','',0,'C');
									$pdf->Cell(13,5,'','',0,'C');
									$pdf->Cell(41,5,'','',0,'C');
									$pdf->Cell(31,5,'','',0,'C');
									$pdf->Cell(15,5,'','',0,'C');
									$pdf->Cell(15,5,'','',0,'C');
									$pdf->Cell(21,5,'Betrag(netto)','',0,'L');
									$pdf->Cell(21,5,'','',0,'C');
									
									$pdf->SetFont('DejaVu','B',8);
									$pdf->Cell(20,5,$ukupno_za_isplatiti.',00','',0,'R');
									$pdf->SetFont('DejaVu','',9);
								//3. TABLE ROW BEZ ISCRTANIH GRANICA ( UST )
								$pdf->Ln();

									$pdf->Cell(13,5,'','',0,'C');
									$pdf->Cell(13,5,'','',0,'C');
									$pdf->Cell(41,5,'','',0,'C');
									$pdf->Cell(31,5,'','',0,'C');
									$pdf->Cell(15,5,'','',0,'C');
									$pdf->Cell(15,5,'','',0,'C');
									$pdf->Cell(21,5,'UST','',0,'L');
									$pdf->Cell(21,5,'','',0,'C');
									$pdf->SetFont('DejaVu','B',8);
									$pdf->Cell(20,5,'0,00','',0,'R');
									$pdf->SetFont('DejaVu','',9);

								//3. TABLE ROW BEZ ISCRTANIH GRANICA ( RECHNUNGSUMME BRUTTO )
								$pdf->Ln();

									$pdf->Cell(13,5,'','',0,'C');
									$pdf->Cell(13,5,'','',0,'C');
									$pdf->Cell(41,5,'','',0,'C');
									$pdf->Cell(31,5,'','',0,'C');
									$pdf->Cell(15,5,'','',0,'C');
									$pdf->Cell(15,5,'','',0,'C');
									$pdf->Cell(21,5,'Rechnungsumme Brutto €','',0,'L'); 
									$pdf->Cell(21,5,'','',0,'C');
									$pdf->SetFont('DejaVu','B',8);
									$pdf->Cell(20,5,''.$ukupno_za_isplatiti.',00','',0,'R');
									$pdf->SetFont('DejaVu','',9); 



								// DVOSTRUKE <HR> LINIJE
								$pdf->Ln(15);
								$pdf->Cell(190,1,"",'B',0,'',0); $pdf->Ln();
								$pdf->Cell(190,0.5,"",'B',0,'',0); $pdf->Ln();

								//4. TABLE ROW BEZ ISCRTANIH GRANICA ( Zahlungsbetrag € )
								$pdf->SetFont('DejaVu','B',8);
								$pdf->Ln();

									$pdf->Cell(13,5,'','',0,'C');
									$pdf->Cell(13,5,'','',0,'C');
									$pdf->Cell(41,5,'','',0,'C');
									$pdf->Cell(31,5,'','',0,'C');
									$pdf->Cell(15,5,'','',0,'C');
									$pdf->Cell(15,5,'','',0,'C');
									$pdf->Cell(21,5,'Zahlungsbetrag €','',0,'L');
									$pdf->Cell(21,5,'','',0,'C');
									$pdf->Cell(20,5,''.$ukupno_za_isplatiti.',00','',0,'R');
									
								/******************************************************
								* NOVA STRANICA GDJE CE BITI TABELA U KOJOJ SE NAVODE KANDIDATI 
								******************************************************/
								$pdf->AliasNbPages();
								$pdf->AddPage();
								 
									$pdf->Cell(13,5,'Pos','T,L,B,R',0,'C');
									$pdf->Cell(13,5,'P-Nr','T,L,B,R',0,'C');
									$pdf->Cell(51,5,'Kandidattname','T,L,B,R',0,'C');
									$pdf->Cell(41,5,'Produkt','T,L,B,R',0,'C');
									$pdf->Cell(31,5,'Einheit','T,L,B,R',0,'C');
									$pdf->Cell(41,5,'Betrag €','T,L,B,R',0,'C');
								
								// TABLE ROW SA ISCRTANIM GRANICAMA ********** I IMENOM KANDIDATA ***************
								foreach($imena_kandidata as $index => $kandidat){
									$pdf->Ln();
								
									$pdf->Cell(13,10,($index+1),'T,L,B,R',0,'C');
									$pdf->Cell(13,10,'','T,L,B,R',0,'C');
									$pdf->Cell(51,10,''.$kandidat.'','T,L,B,R',0,'C');
									if($vrsta == "2"){
										$pdf->Cell(41,10,'DIPL','B,L,R',0,'C');
									}
									else{
										$pdf->Cell(41,10,'ARBEITVERMITTLUNG','B,L,R',0,'C');
									}
									$pdf->Cell(31,10,'','T,L,B,R',0,'C');
									$pdf->Cell(41,10,''.$provizija_po_kandidatu[$index].',00','T,L,B,R',0,'C');
								}
									
									
									// KRAJ PDFA
									$filename="files/partner/partner_gutschrift/".$pdf_naziv;
									$pdf->Output($filename,'F');
									
					//Send all mails
							// try {
							// 		$mail = new PHPMailer;
							// 		//$mail->isSMTP();											// Set mailer to use SMTP
							// 		$mail->setFrom('no-reply@wwtravel.net', 'World Wide Travel');    		// Add a recipient
							// 		$mail->addAddress('i.suljic@wwtravel.net','Ismail');	// Add a recipient
							// 		$mail->AddAttachment($filename);			
							// 		$mail->Subject ="Obracun za partnera ".$partnerName;
							// 		$mail->Body    = "Poštovani, u prilogu se nalazi obračun.";
							// 		$mail->AltBody = "Poštovani, u prilogu se nalazi obračun.";
										
							// 	if(!$mail->send()) {
							// 		echo 'Message could not be sent.';
							// 		echo 'Mailer Error: ' . $mail->ErrorInfo;
							// 	}else{
							// 		echo "Mail sent";
							// 	}
							// } catch (phpmailerException $e) {
							// 	echo $e->errorMessage(); //Pretty error messages from PHPMailer
							// }
		
		}// ISPLATA ZA SVE KRAJ
		else
			{ // POJEDINAČNA ISPLATA
			
							$read_query = $db->prepare("
												SELECT jp_uplate_provizija, jp_uplate_datum
												FROM idk_partner_uplate
												INNER JOIN idk_jobstep_partners ON idk_partner_uplate.jp_uplate_partnerid = idk_jobstep_partners.jp_id
												WHERE jp_uplate_partnerid = :jp_uplate_partnerid
												AND jp_uplate_vrsta = :jp_uplate_vrsta
												AND jp_uplate_datum = :jp_uplate_datum
												AND jp_uplate_kandidatid = :jp_uplate_kandidatid
												AND idk_jobstep_partners.jp_racun = :jp_racun
											");
							
							$read_query->execute(array(
										":jp_uplate_partnerid" => $partner_id,
										":jp_uplate_vrsta" => $vrsta,
										":jp_uplate_datum" => $datum,
										":jp_uplate_kandidatid" => $kandidat_id,
										":jp_racun" => 1,
										));
							$result = $read_query->fetch();
							
								$partnerName = getInfoPartnerPreporukaR($partner_id);
								$provizija = $result['jp_uplate_provizija'];
								
								if($vrsta == "1"){
									$kandidat_query = $db->prepare("SELECT kandidat_ime, kandidat_prezime 
																FROM idk_kandidati
																WHERE kandidat_id = :kandidat_id ");
									$kandidat_query -> execute ( array ( ":kandidat_id" => $kandidat_id ) );
									
									$row = $kandidat_query->fetch();
										$kandidate_name = $row['kandidat_ime']." ".$row['kandidat_prezime'];
										
								}
								else
								{
									$kandidat_query = $db->prepare("SELECT ime_nd_kandidata, prezime_nd_kandidata 
																	FROM idk_nd_kandidata 
																	WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata");
									$kandidat_query->execute(array(
										":id_broj_nd_kandidata" => $kandidat_id
										));
										
										
									$row = $kandidat_query->fetch();
										$kandidate_name = $row['ime_nd_kandidata']." ".$row['prezime_nd_kandidata'];
								}
								
								
								$adresa = getPartnerAdresaR($partner_id);
								
									$ulica = $adresa[0];
									$postanski = $adresa[1];
									$grad = $adresa[2];
									$drzava = $adresa[3];
								
									// $ulica = "Kapici 103";
									// $postanski = "77220";
									// $grad = "Cazin";
									// $drzava = "Bosna i Hercegovina";
								
								$pdf_naziv = "GS-".date('Y-m').uniqid()."-".$partner_id.".pdf"; // uniqueid može izazvati bug, koji bi brisao stari gutschrift i prekonjega zapsiao novi
								$pdf = new PDF_gutschrift();
								$pdf->AliasNbPages();
								$pdf->AddPage();
								
								
								
								
								// GUTSCHRIFT KLIJENT -
									$pdf->SetFont('DejaVu','B',11);
									$pdf->Cell(20,5,$partnerName,0,0,'l',0); $pdf->Ln(); $pdf->SetFont('DejaVu','',11);
									$pdf->Cell(20,5,$ulica,0,0,'l',0); $pdf->Ln();
									$pdf->Cell(20,5,$postanski." ".$grad,0,0,'l',0); $pdf->Ln();
									$pdf->Cell(20,5,$drzava,0,0,'l',0); $pdf->Ln();
								//

								$pdf->Ln(40); // ODVOJI HEADER DOVOLJNO OD SADRŽAJA PDFA	
								// GUTSCHRIFT ID PLUS DATUM 
								$pdf->SetFont('DejaVu','B',11);
									 $pdf->Cell(190,5,'GUTSCHRIFT GS-'.date("Y-m"),0,0,'C'); 	
								// hr line
								$pdf->Ln();

								$pdf->Cell(190,1,"",'B',0,'',0);

								$pdf->Ln(20); // ODVOJI HEADER DOVOLJNO OD SADRŽAJA PDFA	


								$pdf->SetFont('DejaVu','',9);
								// TABELA

								// TABLE HEAD
									$pdf->Cell(13,5,'Pos','T,L,B,R',0,'C');
									$pdf->Cell(13,5,'P-Nr','T,L,B,R',0,'C');
									$pdf->Cell(41,5,'Bezeichnung','T,L,B,R',0,'C');
									$pdf->Cell(31,5,' Beschreibung','T,L,R',0,'C');
									$pdf->Cell(15,5,'Menge','T,L,B,R',0,'C');
									$pdf->Cell(15,5,'Einheit','T,L,B,R',0,'C');
									$pdf->Cell(21,5,'Einzelpreis €','T,L,B,R',0,'C');
									$pdf->Cell(21,5,'UST%','T,L,B,R',0,'C');
									$pdf->Cell(20,5,'Betrag €','T,L,B,R',0,'C');

								//1. TABLE ROW SA ISCRTANIM GRANICAMA
								$pdf->Ln();

									$pdf->Cell(13,25,'1','T,L,B,R',0,'C');
									$pdf->Cell(13,25,'','T,L,B,R',0,'C');
									$pdf->Cell(41,25,'Empfelungsgeberprovision','T,L,B,R',0,'C');
									$pdf->Cell(31,25,'','B,L,R',0,'C');
									$pdf->Cell(15,25,'','T,L,B,R',0,'C');
									$pdf->Cell(15,25,'','T,L,B,R',0,'C');
									$pdf->Cell(21,25,'','T,L,B,R',0,'C');
									$pdf->Cell(21,25,'','T,L,B,R',0,'C');
									$pdf->Cell(20,25,$provizija.',00','T,L,B,R',0,'C');

								//2. TABLE ROW BEZ ISCRTANIH GRANICA ( BETRAG (NETTO) )
								$pdf->Ln();

									$pdf->Cell(13,5,'','',0,'C');
									$pdf->Cell(13,5,'','',0,'C');
									$pdf->Cell(41,5,'','',0,'C');
									$pdf->Cell(31,5,'','',0,'C');
									$pdf->Cell(15,5,'','',0,'C');
									$pdf->Cell(15,5,'','',0,'C');
									$pdf->Cell(21,5,'Betrag(netto)','',0,'L');
									$pdf->Cell(21,5,'','',0,'C');
									
									$pdf->SetFont('DejaVu','B',8);
									$pdf->Cell(20,5,$provizija.',00','',0,'R');
									$pdf->SetFont('DejaVu','',9);
								//3. TABLE ROW BEZ ISCRTANIH GRANICA ( UST )
								$pdf->Ln();

									$pdf->Cell(13,5,'','',0,'C');
									$pdf->Cell(13,5,'','',0,'C');
									$pdf->Cell(41,5,'','',0,'C');
									$pdf->Cell(31,5,'','',0,'C');
									$pdf->Cell(15,5,'','',0,'C');
									$pdf->Cell(15,5,'','',0,'C');
									$pdf->Cell(21,5,'UST','',0,'L');
									$pdf->Cell(21,5,'','',0,'C');
									$pdf->SetFont('DejaVu','B',8);
									$pdf->Cell(20,5,'0,00','',0,'R');
									$pdf->SetFont('DejaVu','',9);

								//3. TABLE ROW BEZ ISCRTANIH GRANICA ( RECHNUNGSUMME BRUTTO )
								$pdf->Ln();

									$pdf->Cell(13,5,'','',0,'C');
									$pdf->Cell(13,5,'','',0,'C');
									$pdf->Cell(41,5,'','',0,'C');
									$pdf->Cell(31,5,'','',0,'C');
									$pdf->Cell(15,5,'','',0,'C');
									$pdf->Cell(15,5,'','',0,'C');
									$pdf->Cell(21,5,'Rechnungsumme Brutto €','',0,'L'); 
									$pdf->Cell(21,5,'','',0,'C');
									$pdf->SetFont('DejaVu','B',8);
									$pdf->Cell(20,5,$provizija.',00','',0,'R');
									$pdf->SetFont('DejaVu','',9); 



								// DVOSTRUKE <HR> LINIJE
								$pdf->Ln(15);
								$pdf->Cell(190,1,"",'B',0,'',0); $pdf->Ln();
								$pdf->Cell(190,0.5,"",'B',0,'',0); $pdf->Ln();

								//4. TABLE ROW BEZ ISCRTANIH GRANICA ( Zahlungsbetrag € )
								$pdf->SetFont('DejaVu','B',8);
								$pdf->Ln();

									$pdf->Cell(13,5,'','',0,'C');
									$pdf->Cell(13,5,'','',0,'C');
									$pdf->Cell(41,5,'','',0,'C');
									$pdf->Cell(31,5,'','',0,'C');
									$pdf->Cell(15,5,'','',0,'C');
									$pdf->Cell(15,5,'','',0,'C');
									$pdf->Cell(21,5,'Zahlungsbetrag €','',0,'L');
									$pdf->Cell(21,5,'','',0,'C');
									$pdf->Cell(20,5,$provizija.',00','',0,'R');
									
									
								/******************************************************
								* NOVA STRANICA GDJE CE BITI TABELA U KOJOJ SE NAVODE KANDIDATI 
								******************************************************/
								$pdf->AliasNbPages();
								$pdf->AddPage();
								 
									$pdf->Cell(13,5,'Pos','T,L,B,R',0,'C');
									$pdf->Cell(13,5,'P-Nr','T,L,B,R',0,'C');
									$pdf->Cell(51,5,'Kandidattname','T,L,B,R',0,'C');
									$pdf->Cell(41,5,'Produkt','T,L,B,R',0,'C');
									$pdf->Cell(31,5,'Einheit','T,L,B,R',0,'C');
									$pdf->Cell(41,5,'Betrag €','T,L,B,R',0,'C');
								
								// TABLE ROW SA ISCRTANIM GRANICAMA ********** I IMENOM KANDIDATA ***************
						
									$pdf->Ln();
								
									$pdf->Cell(13,10,'1','T,L,B,R',0,'C');
									$pdf->Cell(13,10,'','T,L,B,R',0,'C');
									$pdf->Cell(51,10,''.$kandidate_name.'','T,L,B,R',0,'C');
									if($vrsta == "2"){
										$pdf->Cell(41,10,'DIPL','B,L,R',0,'C');
									}
									else{
										$pdf->Cell(41,10,'ARBEITVERMITTLUNG','B,L,R',0,'C');
									}
									$pdf->Cell(31,10,'','T,L,B,R',0,'C');
									$pdf->Cell(41,10,''.$provizija.',00','T,L,B,R',0,'C');
								
									
								
									$filename="files/partner/partner_gutschrift/".$pdf_naziv;
									$pdf->Output($filename,'F');
									
					//Send all mails
							// try {
							// 		$mail = new PHPMailer;
							// 		//$mail->isSMTP();											// Set mailer to use SMTP
							// 		$mail->setFrom('no-reply@wwtravel.net', 'World Wide Travel');    		// Add a recipient
							// 		$mail->addAddress('i.suljic@wwtravel.net','Ismail');	// Add a recipient
							// 		$mail->AddAttachment($filename);			
							// 		$mail->Subject ="Obracun za partnera ".$partnerName;
							// 		$mail->Body    = "Poštovani, u prilogu se nalazi obračun.";
							// 		$mail->AltBody = "Poštovani, u prilogu se nalazi obračun.";
										
							// 	if(!$mail->send()) {
							// 		echo 'Message could not be sent.';
							// 		echo 'Mailer Error: ' . $mail->ErrorInfo;
							// 	}else{
							// 		echo "Mail sent";
							// 	}
							// } catch (phpmailerException $e) {
							// 	echo $e->errorMessage(); //Pretty error messages from PHPMailer
							// }
			}// POJEDINAČNA ISPLATA KRAJ
}	
?>