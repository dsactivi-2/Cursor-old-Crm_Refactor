<?php
		//Error log enabled
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		
		include("includes/connect.php");
		//PHPMailer
		require 'mail/Exception.php';
		require 'mail/PHPMailer.php';
		require 'mail/SMTP.php';
		use PHPMailer\PHPMailer\PHPMailer;
		use PHPMailer\PHPMailer\Exception;
		
		function getPartnerStatus($partner_id){
			Global $db;
			
			$query = $db->prepare('SELECT jp_position
									FROM idk_jobstep_partners
									WHERE jp_id = :jp_id
								');
			$query->execute(array(
							':jp_id' => $partner_id
								));
			$row = $query->fetch();
			
			return $row['jp_position'];
		}
		
		
		/****************************************
					NALOZI -> CHECK FOR PAYMENTS
		****************************************/
		$read_query = $db->prepare('
								SELECT DISTINCT kandidat_id, kandidat_partnerid, idk_nalozi.nalog_partner_provizija
								FROM idk_kandidati
								INNER JOIN idk_project_kandidati ON idk_kandidati.kandidat_id = idk_project_kandidati.pk_kandidatid
								INNER JOIN idk_projects ON idk_project_kandidati.pk_projectid = idk_projects.project_id	
								INNER JOIN idk_nalozi ON idk_projects.project_nalogid = idk_nalozi.nalog_id
								WHERE kandidat_status_prijave = :kandidat_status_prijave
								AND kandidat_partnerid IS NOT NULL
								AND NOT EXISTS(
									SELECT NULL
									FROM idk_partner_uplate
									WHERE idk_kandidati.kandidat_id = idk_partner_uplate.jp_uplate_kandidatid
									)
								');  
						
		$read_query->execute(array(':kandidat_status_prijave' => 4));   
		
		while($row = $read_query->fetch()){
			
			$partner_id = $row['kandidat_partnerid'];
			$kandidat_id = $row['kandidat_id'];
			$date = date('Y-m-d H:i:s');
			$provizija = $row['nalog_partner_provizija'];
			
			
			$insert_query = $db->prepare("
								INSERT INTO idk_partner_uplate 
									(jp_uplate_partnerid, jp_uplate_kandidatid, jp_uplate_status, jp_uplate_zaposlenikid, jp_uplate_datum, jp_uplate_provizija,jp_uplate_vrsta) 
								VALUES 
									(:jp_uplate_partnerid, :jp_uplate_kandidatid, :jp_uplate_status, :jp_uplate_zaposlenikid, :jp_uplate_datum, :jp_uplate_provizija,:jp_uplate_vrsta)
							");
			$insert_query->execute(array(
								':jp_uplate_partnerid' => $partner_id,
								':jp_uplate_kandidatid' => $kandidat_id,
								':jp_uplate_status' => 2,
								':jp_uplate_zaposlenikid' => 0,
								':jp_uplate_datum' => $date,
								':jp_uplate_provizija' => $provizija,
								':jp_uplate_vrsta' => 1
							));
		}
		
		
		/****************************************
					DIPL -> CHECK FOR PAYMENTS
		****************************************/
		$read_query_dipl = $db->prepare("SELECT id_broj_nd_kandidata, zaduzen_zaposlenik_nd_kandidata, kandidat_idd, partner_nd_status
										FROM idk_nd_kandidata 
										INNER JOIN idk_jobstep_partners ON idk_nd_kandidata.kandidat_idd = idk_jobstep_partners.jp_id 
										INNER JOIN idk_predracuni ON idk_nd_kandidata.id_broj_nd_kandidata = idk_predracuni.pr_kandidat_id 
										WHERE povijest_nd_kandidata = 3	
										AND povijest_vrsta_nd_kandidata = 1
										AND idk_predracuni.pr_uplaceno = 1
										UNION
										SELECT id_broj_nd_kandidata, zaduzen_zaposlenik_nd_kandidata, kandidat_idd, partner_nd_status
										FROM idk_nd_kandidata 
										INNER JOIN idk_jobstep_partners ON idk_nd_kandidata.kandidat_idd = idk_jobstep_partners.jp_id 
										INNER JOIN idk_nd_rate ON idk_nd_kandidata.id_broj_nd_kandidata = idk_nd_rate.id_nd_kan
										WHERE partner_nd_status IS NOT NULL
										UNION 
										SELECT id_broj_nd_kandidata, zaduzen_zaposlenik_nd_kandidata, kandidat_idd, idk_kandidati.kandidat_partnerid
										FROM idk_nd_kandidata 
										INNER JOIN idk_kandidati ON idk_nd_kandidata.kandidat_idd = idk_kandidati.kandidat_id 
										INNER JOIN idk_nd_rate ON idk_nd_kandidata.id_broj_nd_kandidata = idk_nd_rate.id_nd_kan 
										WHERE idk_kandidati.povezan_na_dipl = 1 AND idk_kandidati.kandidat_partnerid IS NOT NULL
									");
														
								// povijest_vrsta_nd_kandidata se gleda iz razloga što bi mogle biti kasnije promjene u vrsti nostrifikacije te da se ne mijenja svugdje 						
								
		$read_query_dipl->execute();   
		
		while($row = $read_query_dipl->fetch()){
			
			$kandidat_id = $row['id_broj_nd_kandidata'];
			
			if($row['partner_nd_status'] > 10){
				
				$partner_id = $row['partner_nd_status'];
				$partner_nd_status = getPartnerStatus($partner_id);
				if($partner_nd_status == 1){
						$dipl_price = 20;
					}elseif($partner_nd_status == 2){
						$dipl_price = 22.5;
					}elseif($partner_nd_status == 3){
						$dipl_price = 25;
					}elseif($partner_nd_status == 4){
						$dipl_price = 25;
					}elseif($partner_nd_status == 5){
						$dipl_price = 25;
					}
			}else 
			{
				
				$partner_id = $row['kandidat_idd'];
				$partner_nd_status = $row['partner_nd_status'];
				
				if($partner_nd_status == 1){
					$dipl_price = 20;
				}elseif($partner_nd_status == 2){
					$dipl_price = 22.5;
				}elseif($partner_nd_status == 3){
					$dipl_price = 25;
				}elseif($partner_nd_status == 4){
					$dipl_price = 25;
				}elseif($partner_nd_status == 5){
					$dipl_price = 25;
				}
			}
			
			$zaposlenik = $row['zaduzen_zaposlenik_nd_kandidata'];
			$date = date('Y-m-d H:i:s');
			
			
			$insert_query_dipl = $db->prepare("
								INSERT INTO idk_partner_uplate 
									(jp_uplate_partnerid, jp_uplate_kandidatid, jp_uplate_status, jp_uplate_zaposlenikid, jp_uplate_datum, jp_uplate_provizija,jp_uplate_vrsta) 
								VALUES 
									(:jp_uplate_partnerid, :jp_uplate_kandidatid, :jp_uplate_status, :jp_uplate_zaposlenikid, :jp_uplate_datum, :jp_uplate_provizija,:jp_uplate_vrsta)
								");
											
			$insert_query_dipl->execute(array(
								':jp_uplate_partnerid' => $partner_id,
								':jp_uplate_kandidatid' => $kandidat_id,
								':jp_uplate_status' => 2,
								':jp_uplate_zaposlenikid' => $zaposlenik,
								':jp_uplate_datum' => $date,
								':jp_uplate_provizija' => $dipl_price,
								':jp_uplate_vrsta' => 2 //  2 => DIPL
							));
				// POŠALJI MAIL AJLI 
		}
?>