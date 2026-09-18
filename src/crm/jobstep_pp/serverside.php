<?php
include("includes/function.php");
include("includes/connect.php");
require_once("includes/env.php");
// ini_set('display_errors', 0);
// ini_set('error_log', 'error_log');
// ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

include("includes/language/language.php"); 

$page = $_REQUEST['page'];

$languageUser = getLanguageForUser($userId);
// $languageUser = 1;
switch($page)
{
	case "list_from_project":

		$partner_ids 		= "";
		$working_positions 	= "";
		$pap_id 			= "";
		$rr_name_de 		= "";
		$krr_date 			= "";
		$lsp_datetime 		= "";
		$totalData 			= 0;


		$nalog_id 		= NULL;
		$menu 			= NULL;
		$action_flag 	= NULL;
		$partner_id 	= NULL;
		$user_type 		= NULL;
		if(isset($_REQUEST["nalog_id"]))
			$nalog_id 			= $_REQUEST["nalog_id"];
		if(isset($_REQUEST["partner_id"]))
			$partner_id			= $_REQUEST["partner_id"];
		if(isset($_REQUEST["bar_id"]))
			$bar_id 			= $_REQUEST["bar_id"];
		if(isset($_REQUEST["bar_id"]))
			$type				= $_REQUEST["type"];
		if(isset($_REQUEST["appointment_ids"]))
			$appointment_ids	= $_REQUEST["appointment_ids"];
		if(isset($_REQUEST["partner_ids"]))
			$partner_ids		= $_REQUEST["partner_ids"];
		if(isset($_REQUEST["working_positions"]))
			$working_positions	= $_REQUEST["working_positions"];

		$flag_show_id = showCandidateIdR($userId);

		$isEnpal = getCompanyAccessR($nalog_id);
		
		$overseeing_nalog = $nalog_id;
		if($nalog_id == 222){
			$nalog_id = '217,218,219,220,222';
		}
		
		
		if($type == 1){
			$add_to_select 				= "";
			$add_to_join 				= "";
			$uslov_appointment 			= "";
			$uslov_working_positions 	= "";
			$additional_condition = "";
			if($bar_id == 0){
				$search_like = "Obra";
			}
			if($bar_id == 2){
				$search_like = "Casting";
			}
			if($bar_id == 3){
				$search_like = "Intervju";
			}
			if($bar_id == 4){
				$search_like = "- Ugovor";
				$additional_condition = "OR pro.project_name LIKE ('%Kandidati poceli sa radom') OR pro.project_name LIKE ('%Zavrseni kandidati')";
			}
			if($bar_id == 5){
				$search_like = "Odbijen";
			}
			
			Global $db;
			
			$query_get_candidate_ids = $db -> prepare("
				SELECT kan.kandidat_id 
				FROM idk_kandidati kan
				JOIN idk_project_kandidati pk
				ON pk.pk_kandidatid = kan.kandidat_id
				JOIN idk_projects pro
				ON pro.project_id = pk.pk_projectid
				WHERE (pro.project_name LIKE ('%".$search_like."%') ".$additional_condition.") 
				AND pro.project_nalogid IN (".$nalog_id.")
				AND kan.kandidat_status != 3
			");
			
			$query_get_candidate_ids -> execute();
			// var_dump($query_get_candidate_ids);
			$candidate_ids = array();
			while($row_get_candidate_ids = $query_get_candidate_ids->fetch()){
				array_push($candidate_ids, $row_get_candidate_ids['kandidat_id']);
			}
			$candidate_ids = implode(',',$candidate_ids);

			if($appointment_ids != ""){
				$appointment_ids = implode(',', $appointment_ids);
				$uslov_appointment .= "AND pap.pap_id IN (".$appointment_ids.") ";
			}
			if($partner_ids != ""){
				$partner_ids = implode(',', $partner_ids);
				$uslov_appointment .= "AND sq_kan.kandidat_ppa_partner_id IN (".$partner_ids.") ";
			}
			
			if($working_positions != ""){
				foreach ($working_positions as &$working_position)
   					$working_position = "sq_kan.kandidat_pp_pozicija LIKE ('%".$working_position."%')";

				$uslov_working_positions = "AND (".implode(' OR ', $working_positions).")";
			}

			// var_dump($appointment_ids);
			// exit();
		
			if($bar_id == 3){
				$add_to_select .= ", pap.pap_id, pca.pca_appointment_id, pap.pap_date, pap.pap_city, pca.pca_time, CONCAT(pap.pap_date, ' ', pca.pca_time) as sortable_appointment";
				$add_to_join .= "
					JOIN(
						SELECT sq_pap.pap_date, sq_pap.pap_city, sq_pap.pap_id
						FROM idk_pp_appointments sq_pap
						WHERE sq_pap.pap_nalog_id IN (".$nalog_id.")
					) pap
					ON pca.pca_appointment_id  = pap.pap_id
				";
			}
			if($bar_id == 4){
				$add_to_select .= ", pap.pap_id, pca.pca_appointment_id, pap.pap_date, pap.pap_city, pca.pca_time, CONCAT(pap.pap_date, ' ', pca.pca_time) as sortable_appointment";
				$add_to_join .= "
					JOIN(
						SELECT sq_pap.pap_date, sq_pap.pap_city, sq_pap.pap_id
						FROM idk_pp_appointments sq_pap
						WHERE sq_pap.pap_nalog_id IN (".$nalog_id.")
					) pap
					ON pca.pca_appointment_id  = pap.pap_id
				";
				// $add_to_select .= ", lsp.lsp_datetime";
				// $add_to_join .= "

					// LEFT JOIN(
						// SELECT sq_lsp.lsp_kandidat_id, sq_lsp.lsp_datetime
						// FROM idk_log_statusi_prijave sq_lsp
						// JOIN (
							// SELECT ssq_pr.project_id
							// FROM idk_projects ssq_pr
							// WHERE ssq_pr.project_nalogid IN (".$nalog_id.")
							// AND ssq_pr.project_name LIKE ('%- Ugovor')
						// ) sq_pr
						// ON sq_pr.project_id = sq_lsp.lsp_projekt_id
						// WHERE sq_lsp.lsp_kandidat_id IN (".$candidate_ids.")
						// AND sq_lsp.lsp_status_prijave_id = 7
					// ) lsp
					// ON lsp.lsp_kandidat_id = sq_kan.kandidat_id
				// ";
			}
			if($bar_id == 5){
				$add_to_select .= ", rr.rr_name_de, rr.krr_date, lsp.lsp_datetime";
				$add_to_join .= "
					LEFT JOIN(
						SELECT sq_r.rr_name_de, sq_rr.krr_candidate_id, sq_rr.krr_date
						FROM idk_kandidati_reject_reasons sq_rr
						JOIN idk_reject_reasons sq_r ON sq_r.rr_id = sq_rr.krr_reason_id
						WHERE sq_rr.krr_candidate_id IN (".$candidate_ids.")
						AND sq_rr.krr_nalog_id IN (".$nalog_id.")
						GROUP BY sq_rr.krr_apointment_id
					) rr
					ON sq_kan.kandidat_id = rr.krr_candidate_id
					LEFT JOIN(
						SELECT sq_lsp.lsp_kandidat_id, sq_lsp.lsp_datetime
						FROM idk_log_statusi_prijave sq_lsp
						JOIN (
							SELECT ssq_pr.project_id
							FROM idk_projects ssq_pr
							WHERE ssq_pr.project_nalogid IN (".$nalog_id.")
							AND ssq_pr.project_name LIKE ('%- Odbijen')
						) sq_pr
						ON sq_pr.project_id = sq_lsp.lsp_projekt_id
						WHERE sq_lsp.lsp_kandidat_id IN (".$candidate_ids.")
						AND sq_lsp.lsp_status_prijave_id = 5
						GROUP BY sq_lsp.lsp_projekt_id
					) lsp
					ON lsp.lsp_kandidat_id = sq_kan.kandidat_id
				";
			}
			
			// var_dump(count($candidate_ids));
			// $unique = array_unique($candidate_ids);
			// var_dump(count($unique));
			// $duplicates = array_diff($candidate_ids, $unique);
			// var_dump($duplicates);
			// exit();

			if($candidate_ids != ''){
				/* Database connection start */
        $servername = $envConfig->DB_HOST;
        $username = $envConfig->DB_USER;
        $password = $envConfig->DB_PASSWORD;
        $dbname = $envConfig->DB_DATABASE;


				$conn = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());
				$conn->query('set character_set_client=utf8mb4');
				$conn->query('set character_set_connection=utf8mb4');
				$conn->query('set character_set_results=utf8mb4');
				$conn->query('set character_set_server=utf8mb4');
				/* Database connection end */
				
				$requestData = $_REQUEST;
				
				/*
				[0] - Da li je vozacka trazena na linku 	[block (jeste) / none (nije)]
				[1] - Koja se vozacka kategorija traži 		[vozacka kategorija / NULL]
				[2] - Koji nivo njemačkog jezika se traži 	[BZ do C2]
				[3] - Da li se traži radno iskustvo 		[1 (traži) / 0 (ne traži)]
				[4] - Da li se traži visoko obrazovanje 	[1 (traži) / 0 (ne trazi)]
				*/
				$kriterij_nalog = array();
				$kriterij_nalog = getNalogKriterij($nalog_id);	
				$columns = array();
				if($flag_show_id)
					array_push($columns, 'kandidat_id');
				
				array_push($columns, 'kandidat_full_name', 'kj_slusanje');
				array_push($columns, 'ke_vrsta_obrazovanja');
				
				if($bar_id == 3){
					array_push($columns, $txtArray['ss_naziv'][$languageUser]);
					array_push($columns,'sortable_appointment');
				}
				if($bar_id == 3 OR $bar_id == 4){
					array_push($columns, 'pca_avg_rating');
				}
				if($bar_id == 4){
					array_push($columns, 'kandidat_pp_pozicija');
					array_push($columns, 'kandidat_ppa_partner_id');
					array_push($columns, 'sortable_appointment');
					array_push($columns, 'kandidat_pp_datum_prihvatanja');
				}
				if($bar_id == 5){
					array_push($columns, $txtArray['ss_naziv'][$languageUser]);
					array_push($columns, 'kandidat_pp_razlog_odbijanja');
					array_push($columns, 'krr_date');
					
				}
				$sql = "				
					SELECT sq_kan.kandidat_status_prijave, sq_kan.kandidat_pp_plata, sq_kan.kandidat_pp_pozicija, sq_kan.kandidat_pp_lokacija, sq_kan.kandidat_full_name, sq_kan.kandidat_vozacka_kategorija, sq_kan.kandidat_id, kj.kj_slusanje, sq_ke.ke_naziv, sq_kan.kandidat_check,
					sq_kan.kandidat_pp_datum_prihvatanja, sq_ke.ke_naziv_de, sq_kan.kandidat_pp_razlog_odbijanja, sq_ke.ke_vrsta_obrazovanja, sq_ke.ss_naziv_de, sq_ke.ss_naziv_en, sq_ke.ss_naziv, kj.kj_id, sq_kan.kandidat_ppa_partner_id,  pca.pca_avg_rating".$add_to_select."
					FROM (
						SELECT  CONCAT (TRIM(kan.kandidat_ime), ' ', TRIM(kan.kandidat_prezime)) AS kandidat_full_name, kan.kandidat_pp_plata, kan.kandidat_pp_razlog_odbijanja, kan.kandidat_vozacka_kategorija, kan.kandidat_id, kan.kandidat_ppa_partner_id, kan.kandidat_check, kan.kandidat_pp_datum_prihvatanja, kan.kandidat_pp_lokacija, kan.kandidat_pp_pozicija,  kan.kandidat_status_prijave
						FROM idk_kandidati kan
						WHERE kan.kandidat_id IN (".$candidate_ids.")
					) sq_kan
					LEFT JOIN (
						SELECT sq_kj.kj_id, sq_kj.kj_slusanje, sq_kj.kj_kandidatid
						FROM idk_kandidat_jezici sq_kj
						JOIN idk_candidate_verified_languages cvl
						ON sq_kj.kj_id = cvl.cvl_id AND cvl.cvl_active = 1
						WHERE sq_kj.kj_naziv = 'Njemački' AND sq_kj.kj_kandidatid IN (".$candidate_ids.")
					) kj
					ON sq_kan.kandidat_id = kj.kj_kandidatid

					LEFT JOIN (
						SELECT ke.ke_kandidat_id, ke.ke_naziv, ke.ke_naziv_de, ke.ke_vrsta_obrazovanja, ke.ss_naziv, ke.ss_naziv_de, ke.ss_naziv_en
						FROM (
							SELECT ssq_ke.ke_kandidat_id, ssq_ke.ke_naziv, ssq_ke.ke_naziv_de, sq_skole.skola_tip_obrazovanja AS ke_vrsta_obrazovanja, ssq_sm.ss_naziv, ssq_sm.ss_naziv_de, ssq_sm.ss_naziv_en
							FROM idk_kandidat_edukacija ssq_ke 
							JOIN idk_skole_smjerovi ssq_sm 
							ON ssq_sm.ss_id = ssq_ke.ke_smjer_id
							JOIN idk_skole sq_skole
							ON sq_skole.skola_id = ssq_sm.ss_skola_id
							WHERE ssq_ke.ke_skola_id IS NOT NULL
							AND ssq_ke.ke_kandidat_id IN (".$candidate_ids.")
							AND ssq_ke.ke_prikaz_pp = 1
							ORDER BY ssq_ke.ke_vrsta_obrazovanja ASC
						) ke
						GROUP BY ke.ke_kandidat_id
					) sq_ke
					ON sq_ke.ke_kandidat_id = sq_kan.kandidat_id
					LEFT JOIN(
						SELECT sq_pca.pca_avg_rating, sq_pca.pca_kandidat_id, sq_pca.pca_appointment_id, sq_pca.pca_time
						FROM idk_pp_cand_appts sq_pca
						JOIN idk_pp_appointments sq_ppa
						ON sq_ppa.pap_id = sq_pca.pca_appointment_id
						WHERE sq_ppa.pap_nalog_id IN (".$nalog_id.") AND sq_pca.pca_id IN ( SELECT MAX(pca_id) AS max_id FROM idk_pp_cand_appts WHERE pca_kandidat_id IN (".$candidate_ids.") GROUP BY pca_kandidat_id)
						AND sq_pca.pca_kandidat_id IN (".$candidate_ids.")
					) pca
					ON pca.pca_kandidat_id = sq_kan.kandidat_id
					".$add_to_join."
					WHERE sq_kan.kandidat_id IS NOT NULL
					".$uslov_appointment."
					".$uslov_working_positions."
								
				";
				
				if( !empty($requestData['search']['value']) ) {
					$sql	   .= "AND sq_kan.kandidat_full_name LIKE '%".$requestData['search']['value']."%' ";
				}
				
				
				$query 			= mysqli_query($conn, $sql) or die("serversidedata.php: get employees");
				$totalFiltered 	= mysqli_num_rows($query);
				if($requestData['length'] == -1){
					$sql		   .= "ORDER BY ". $columns[$requestData['order'][0]['column']]."   ";
				}else{
					$sql		   .= "ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir'].",sq_kan.kandidat_full_name  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
				}
				$query = mysqli_query($conn, $sql) or die("serversidedata.php: error ocured");
				$data = array();
				$prev_kan_id = 0;
				$style_test = "";
				// var_dump($sql);
				while( $row = mysqli_fetch_array($query) ) {
					
					$kandidat_id		 			= $row['kandidat_id'];
					$kandidat_full_name 			= $row['kandidat_full_name'];
					$kj_slusanje 					= $row['kj_slusanje'];
					$kandidat_vozacka_kategorija 	= $row['kandidat_vozacka_kategorija'];
					$ke_naziv 						= $row['ke_naziv'];
					$ke_naziv_de 					= $row['ke_naziv_de'];
					$ke_vrsta_obrazovanja 			= $row['ke_vrsta_obrazovanja'];
					$ss_naziv 						= $row['ss_naziv'];
					$ss_naziv_de 					= $row['ss_naziv_de'];
					$ss_naziv_en 					= $row['ss_naziv_en'];
					$pca_avg_rating 				= $row['pca_avg_rating'];
					$kandidat_ppa_partner_id 		= $row['kandidat_ppa_partner_id'];
					$kandidat_check 				= $row['kandidat_check'];
					$kandidat_pp_razlog_odbijanja	= $row['kandidat_pp_razlog_odbijanja'];
					$kandidat_pp_lokacija			= $row['kandidat_pp_lokacija'];
					$kandidat_pp_pozicija			= $row['kandidat_pp_pozicija'];
					$kandidat_pp_plata				= $row['kandidat_pp_plata'];
					$kandidat_status_prijave		= $row['kandidat_status_prijave'];
					$kandidat_pp_datum_prihvatanja		= $row['kandidat_pp_datum_prihvatanja'];
					if($bar_id == 3){
						$pap_id							= $row['pap_id'];
					}
					if($bar_id == 5){
						$rr_name_de						= $row['rr_name_de'];
						$krr_date						= $row['krr_date'];
						$lsp_datetime					= $row['lsp_datetime'];
						
					}

					$flag_action_buttons_show 		= false;

					if($prev_kan_id == $kandidat_id){
						$style_test = "background-color:red";
					}
					$prev_kan_id = $kandidat_id;
					
					if($languageUser == 0){
						$ss_naziv_output = $ss_naziv;
					}
					elseif($languageUser == 1){
						$ss_naziv_output = $ss_naziv_de;
					}else{
						$ss_naziv_output = $ss_naziv_en;
					}
					
					if(is_null($ke_naziv_de)){
						$ke_naziv_de = $txtArray["Nije poznato"][$languageUser];
					}
					
					if($kj_slusanje == ''){
						$kj_slusanje = $txtArray["Nije poznato"][$languageUser];
					}
					else if($kj_slusanje == 'Bez znanja'){
						$kj_slusanje = $txtArray['Bez znanja'][$languageUser];
					}
					if($kandidat_vozacka_kategorija == ''){
						$kandidat_vozacka_kategorija = $txtArray["Nije poznato"][$languageUser];
					}
				
					$nestedData 		= array();
					if($flag_show_id){
						$nestedData[] 	= '<p class="text-center" style=""><a>'.$kandidat_id.'</a></p>';						
					}
					$nestedData[] 	= '<p class="text-center" id = "candidate_profile_link_'.$kandidat_check.'"><a target="_blank" href="'.getSiteUrlr().'profile?bar_id='.$bar_id.'&type='.$type.'&kandidat_id='.$kandidat_check.'&n='.$overseeing_nalog.'">'.$kandidat_full_name.'</a></p>';
					$nestedData[] 	= '<p class="text-center"><a>'.$kj_slusanje.'</a></p>';
					$nestedData[] 	= '<p class="text-center">'.$txtArray[$ke_vrsta_obrazovanja][$languageUser].'</p>';
					
					
					if($bar_id == 3){
						$nestedData[] 	= '<p class="text-center">'.$ss_naziv_output.'</p>';
						$pap_city 		= $row['pap_city'];
						$pap_date 		= $row['pap_date'];
						$pca_time 		= $row['pca_time'];
						$nestedData[] 	= '<p class="text-center">'.$pap_city.', '.date('d.m.y H:i', strtotime($pap_date.' '.$pca_time)).'</p>';					
					}
					if($bar_id == 3 || $bar_id == 4){
						$rating_output = "";
						if(is_null($pca_avg_rating)){
							if($isEnpal == 1){
								$rating_output = '
									<i class="fa fa-star-o star_table" aria-hidden="true"></i>
									<i class="fa fa-star-o star_table" aria-hidden="true"></i>
									<i class="fa fa-star-o star_table" aria-hidden="true"></i>
									<i class="fa fa-star-o star_table" aria-hidden="true"></i>
								';
							}else{
								
								$rating_output = '
									<i class="fa fa-star-o star_table" aria-hidden="true"></i>
									<i class="fa fa-star-o star_table" aria-hidden="true"></i>
									<i class="fa fa-star-o star_table" aria-hidden="true"></i>
									<i class="fa fa-star-o star_table" aria-hidden="true"></i>
									<i class="fa fa-star-o star_table" aria-hidden="true"></i>
								';
							}
						}
						else{
							$flag_action_buttons_show = true;
							if($isEnpal == 1){
								$starCounter = 4;
							}else{
								$starCounter = 5;
							}
							for($i = 1; $i<=$starCounter; $i++){
								if($i <= $pca_avg_rating){
									$rating_output .= '<i class="fa fa-star star_table" aria-hidden="true"></i>';
								}
								else{
									$rating_output .= '<i class="fa fa-star-o star_table" aria-hidden="true"></i>';
								}
							}						
						}
						$nestedData[] 	= '<p class="text-center">'.$rating_output.'</p>';
					}
					
					if($bar_id == 4){
						if(is_null($kandidat_ppa_partner_id)){
							$nestedData[] 	= '<p class="text-center"><i class="fa fa-times fa-building-o click_hire_candidate" id = "child_assign_partner'.$kandidat_check.'" nalog_id = "'.$overseeing_nalog.'" candidate_id = "'.$kandidat_check.'" aria-hidden="true" onclick="handleAsignCandidateModalOpen(this)"></i></p>';
							$nestedData[] 	= '<p class="text-center">'.$kandidat_pp_pozicija.'</p>';
						}
						else{
							$nestedData[] 	= '<p class="text-center">'.$kandidat_pp_pozicija.'</p>';
							if($kandidat_status_prijave == 7){
								$nestedData[] = '<p class = "click_edit_candidate_no_style text-center" style = "cursor:pointer;" id = "child_edit_candidate'.$kandidat_check.'" nalog_id = "'.$overseeing_nalog.'" candidate_id = "'.$kandidat_check.'" partner_id = "'.$kandidat_ppa_partner_id.'"  candidate_location = "'.$kandidat_pp_lokacija.'" candidate_position = "'.$kandidat_pp_pozicija.'" candidate_salary = "'.$kandidat_pp_plata.'" aria-hidden="true" onclick="handleEditCandidatePartnerDataModalOpen(this)"><i class="fa fa-pencil" style = "color:#FFd300;"></i> '.getCompanyNameR(getCompanyForPartnerIdR($kandidat_ppa_partner_id)).'</p>';
							}
							else{
								$nestedData[] = '<p class = "text-center"><i class="fa fa-lock" style = "color:#1cc614;"></i> '.getCompanyNameR(getCompanyForPartnerIdR($kandidat_ppa_partner_id)).'</p>';
							}
						}
						$pap_city 		= $row['pap_city'];
						$pap_date 		= $row['pap_date'];
						$pca_time 		= $row['pca_time'];
						$nestedData[] 	= '<p class="text-center">'.$pap_city.', '.date('d.m.y H:i', strtotime($pap_date.' '.$pca_time)).'</p>';
						if(!is_null($kandidat_pp_datum_prihvatanja))
							$nestedData[] 	= '<p class="text-center">'.date('d.m.Y', strtotime($kandidat_pp_datum_prihvatanja)).'</p>';
						else
							$nestedData[] 	= '<p class="text-center">'.$txtArray['Nije poznato'][$languageUser].'</p>';
						
						// if($nalog_id == 228){
							
						// }
					}
					if($bar_id == 3){
						// $nestedData[] 	= '<p class="text-center">'.$ss_naziv_output.'</p>';
						if($flag_action_buttons_show){
							$nestedData[] 	= '
								<div class = "row">
									<div class="col-6" style="text-align:right"><i class="fa fa-check click_hire_candidate"   id = "child_hire_candidate_'.$kandidat_check.'"   nalog_id = "'.$overseeing_nalog.'" candidate_id = "'.$kandidat_check.'" aria-hidden="true" onclick="handleHireCandidateModalOpen(this)"></i></div>
									<div class="col-6" style="text-align:left"> <i class="fa fa-times click_reject_candidate" id = "child_reject_candidate_'.$kandidat_check.'" nalog_id = "'.$overseeing_nalog.'" candidate_id = "'.$kandidat_check.'" pap_id = "'.$pap_id.'"aria-hidden="true" onclick="handleRejectCandidateModalOpen(this)"></i></div>
								</div>
							';						
						}
						else{
							$nestedData[] 	= '<div style = "text-align:center"><i class="fa fa-ban click_absent_candidate" id = "child_absent_candidate'.$kandidat_check.'" nalog_id = "'.$overseeing_nalog.'" candidate_id = "'.$kandidat_check.'" pap_id = "'.$pap_id.'" aria-hidden="true" onclick="handleRemoveAbsentCandidateModalOpen(this)" style = "text-align:center;"></i></div>';
						}	
					}
					
					if($bar_id == 5){
						$nestedData[] 	= '<p class="text-center">'.$ss_naziv_output.'</p>';
						if(!is_null($rr_name_de))
							$nestedData[] 	= '<p class="text-center">'.$rr_name_de.'</p>';					
						else
							$nestedData[] 	= '<p class="text-center">'.$kandidat_pp_razlog_odbijanja.'</p>';					
						if(!is_null($krr_date))
							$nestedData[] 	= '<p class="text-center">'.date('d.m.Y', strtotime($krr_date)).'</p>';
						else if (!is_null($lsp_datetime))
							$nestedData[] 	= '<p class="text-center">'.date('d.m.Y', strtotime($lsp_datetime)).'</p>';
						else
							$nestedData[] 	= '<p class="text-center">'.$txtArray['Nije poznato'][$languageUser].'</p>';
					}
					$data[] 		= $nestedData;
				

				}
				
				$json_data = array(
					"draw"            => intval( $requestData['draw'] ),
					"recordsTotal"    => intval( $totalData ),
					"recordsFiltered" => intval( $totalFiltered ),
					"data"            => $data
				);
					
			}
			else{
				$json_data = array();
			}
			echo json_encode($json_data);
		}
		else if($type == 2){
			// var_dump($type);
			/* Database connection start */
      $servername = $envConfig->DB_HOST;
      $username = $envConfig->DB_USER;
      $password = $envConfig->DB_PASSWORD;
      $dbname = $envConfig->DB_DATABASE;
			$columns = array();
			if($flag_show_id)
				array_push($columns, 'kandidat_id');
			array_push($columns, 'kandidat_ime', 'kandidat_pp_pozicija', 'kandidat_pp_lokacija', 'kandidat_pp_plata', 'kandidat_potencijalni_pocetak_rada', 'kandidat_ppa_partner_id');
			$query_select 				= "";
			$query_uslov			 	= "";
			$query_select 				= "";
			$uslov_working_positions 	= "";
			$query_join					= "";
			if($working_positions != ""){
				foreach ($working_positions as &$working_position)
   					$working_position = "kan.kandidat_pp_pozicija LIKE ('%".$working_position."%')";

				$uslov_working_positions = "AND (".implode(' OR ', $working_positions).")";
			}
			if($bar_id == 2){
				$query_uslov 	= "AND kan.kandidat_status_prijave = 7";
			}
			else if($bar_id == 3){
				$query_uslov 	= "AND kan.kandidat_status_prijave = 8";
				$query_join		= "
					LEFT JOIN (
						SELECT sq_kc.kc_candidate_id, sq_kc.kc_file_name
						FROM idk_kandidati_contracts sq_kc
						WHERE sq_kc.kc_nalog_id IN($nalog_id)
						AND sq_kc.kc_partner_id = $partner_id
						AND sq_kc.kc_visibility_status = 2
						AND sq_kc.kc_signed = 0
						ORDER BY (sq_kc.kc_id) DESC
					) kc
					ON kc.kc_candidate_id = kan.kandidat_id
				";
				$query_select = ", kc.kc_file_name";
			}
			else if($bar_id == 5){
				$query_uslov = "AND kan.kandidat_status_prijave = 9";
				$query_join		= "
					LEFT JOIN (
						SELECT sq_kc.kc_candidate_id, sq_kc.kc_file_name
						FROM idk_kandidati_contracts sq_kc
						WHERE sq_kc.kc_nalog_id IN($nalog_id)
						AND sq_kc.kc_partner_id = $partner_id
						AND sq_kc.kc_visibility_status = 2
						AND sq_kc.kc_signed = 1
						ORDER BY (sq_kc.kc_id) DESC
					) kc
					ON kc.kc_candidate_id = kan.kandidat_id
				";
				$query_select = ", kc.kc_file_name";				
			}
			else if($bar_id == 4){
				$query_uslov = "AND kan.kandidat_status_prijave = 4";				
			}
			
			if($bar_id == 2 || $bar_id == 3){
				array_push($columns, 'kandidat_status_prijave');
			}

			$conn = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());
			$conn->query('set character_set_client=utf8mb4');
			$conn->query('set character_set_connection=utf8mb4');
			$conn->query('set character_set_results=utf8mb4');
			$conn->query('set character_set_server=utf8mb4');
			/* Database connection end */
			
			$requestData = $_REQUEST;
			
			$sql = "
				SELECT kan.kandidat_potencijalni_pocetak_rada, kan.kandidat_id, kan.kandidat_check, kan.kandidat_ime, kan.kandidat_prezime, kan.kandidat_nalog_id, kan.kandidat_ppa_partner_id, kan.kandidat_pp_lokacija, kan.kandidat_pp_pozicija, kan.kandidat_pp_plata
				".$query_select."
				FROM idk_kandidati kan
				".$query_join."
				WHERE kan.kandidat_nalog_id IN (".$nalog_id.")
				AND kan.kandidat_ppa_partner_id = ".$partner_id."
				".$query_uslov."
				".$uslov_working_positions."
			";
			
			if( !empty($requestData['search']['value']) ) {
				$sql	   .= "AND CONCAT(TRIM(kan.kandidat_ime), ' ', TRIM(kan.kandidat_prezime)) LIKE '%".$requestData['search']['value']."%' ";
			}
	
	

			$query 			= mysqli_query($conn, $sql) or die("serversidedata.php: get employees");
			$totalFiltered 	= mysqli_num_rows($query);

			if($requestData['length'] == -1){
				$sql		   .= "ORDER BY ". $columns[$requestData['order'][0]['column']]."   ";
			}else{
				$sql		    .= "ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
			}
			// var_dump($sql);
			// exit();
			$query = mysqli_query($conn, $sql) or die("serversidedata.php: error ocured");
			$data = array();
			
			while($row = mysqli_fetch_array($query)){
				
				$kandidat_id		 				= $row['kandidat_id'];
				$kandidat_check		 				= $row['kandidat_check'];
				$kandidat_ime 						= $row['kandidat_ime'];
				$kandidat_prezime 					= $row['kandidat_prezime'];
				$kandidat_nalog_id 					= $row['kandidat_nalog_id'];
				$kandidat_ppa_partner_id			= $row['kandidat_ppa_partner_id'];
				$kandidat_pp_lokacija				= $row['kandidat_pp_lokacija'];
				$kandidat_pp_pozicija				= $row['kandidat_pp_pozicija'];
				$kandidat_pp_plata					= $row['kandidat_pp_plata'];
				$kandidat_potencijalni_pocetak_rada	= $row['kandidat_potencijalni_pocetak_rada'];
				
				$nestedData 	= array();
				if($flag_show_id){
					$nestedData[] 	= '<p class="text-center" style = "margin:auto;"><a>'.$kandidat_id.'</a></p>';						
				}
				$nestedData[] 	= '<p class="text-center" style = "margin:auto;"><a target="_blank" href="'.getSiteUrlr().'profile?bar_id='.$bar_id.'&type='.$type.'&kandidat_id='.$kandidat_check.'&n='.$overseeing_nalog.'">'.$kandidat_ime.' '.$kandidat_prezime.'</a></p>';
				$nestedData[] 	= '<p class="text-center" style = "margin:auto;"><a>'.$kandidat_pp_pozicija.'</a></p>';
				$nestedData[] 	= '<p class="text-center" style = "margin:auto;">'.$kandidat_pp_lokacija.'</p>';
				$nestedData[] 	= '<p class="text-center" style = "margin:auto;">'.$kandidat_pp_plata.'</p>';
				if($bar_id == 2){
					$nestedData[] 	= '<p class="text-center" style = "margin:auto;">'.str_replace('to', '-', $kandidat_potencijalni_pocetak_rada).'</p>';
					$nestedData[] 	= '<p class="text-center" style = "margin:auto;"><i style = "font-size:20px;" class="fa fa-pencil click_edit_candidate" id = "child_edit_candidate'.$kandidat_check.'" nalog_id = "'.$overseeing_nalog.'" candidate_id = "'.$kandidat_check.'" partner_id = "'.$kandidat_ppa_partner_id.'"  candidate_location = "'.$kandidat_pp_lokacija.'" candidate_position = "'.$kandidat_pp_pozicija.'" candidate_salary = "'.$kandidat_pp_plata.'" aria-hidden="true" onclick="handleEditCandidatePartnerDataModalOpen(this)"></i></p>';					
				}

				if($bar_id == 2)
					$nestedData[] = '<p class="text-center" style = "margin:auto;"><i style = "font-size:20px;" class="fa fa-files-o click_manage_documents" id = "click_manage_documents'.$kandidat_check.'" nalog_id = "'.$overseeing_nalog.'" candidate_id = "'.$kandidat_check.'" partner_id = "'.$kandidat_ppa_partner_id.'" location = "l" aria-hidden="true" onclick="handleManageContractSentModalOpen(this)"></i></p>';

				$data[] 		= $nestedData;
			}
				
			$json_data = array(
				"draw"            => intval( $requestData['draw'] ),
				"recordsTotal"    => intval( $totalData ),
				"recordsFiltered" => intval( $totalFiltered ),
				"data"            => $data
			);
			// $json_data = array();
			echo json_encode($json_data);
		}
		else if($type == 3 && $bar_id < 4){
      $servername = $envConfig->DB_HOST;
      $username = $envConfig->DB_USER;
      $password = $envConfig->DB_PASSWORD;
      $dbname = $envConfig->DB_DATABASE;
			$columns = array();
			if($flag_show_id)
				array_push($columns, 'kandidat_id');
			array_push($columns, 'kandidat_ime', 'status_nd_kandidata');

			$conn = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());
			$conn->query('set character_set_client=utf8mb4');
			$conn->query('set character_set_connection=utf8mb4');
			$conn->query('set character_set_results=utf8mb4');
			$conn->query('set character_set_server=utf8mb4');
			/* Database connection end */
			
			$requestData = $_REQUEST;
			$query_nd_condition = "";
			if($bar_id == 1){
				$query_nd_condition = " (nd.status_nd_kandidata IN (1,2,7) OR nd.status_nd_kandidata IS NULL) AND (kan.kandidat_ima_nostrifikaciju IS NULL OR kan.kandidat_ima_nostrifikaciju = 0)";
			}
			else if($bar_id == 2){
				$query_nd_condition = " nd.status_nd_kandidata IN (3,4,5)  AND (kan.kandidat_ima_nostrifikaciju IS NULL OR kan.kandidat_ima_nostrifikaciju = 0)";
			}
			else if($bar_id == 3){
				$query_nd_condition = " nd.status_nd_kandidata IN (6) OR kan.kandidat_ima_nostrifikaciju = 1";
			}
			$sql = "
				SELECT
					kan.kandidat_id, 
					CONCAT(TRIM(kan.kandidat_ime), ' ', TRIM(kan.kandidat_prezime)) as candidate_fullname, 
					kan.kandidat_check,  
					naziv_ustanove_nd,
					CASE 
						WHEN kan.kandidat_ima_nostrifikaciju = 1
						THEN 6
						ELSE
							CASE 
								WHEN nd.status_nd_kandidata IN(1,7) OR nd.status_nd_kandidata IS NULL
								THEN 1
								ELSE nd.status_nd_kandidata
							END
					END AS status_nd_kandidata
				FROM
					(
					SELECT
						kandidat_dipl_id, kandidat_id, kandidat_check, kandidat_ime, kandidat_prezime, kandidat_ima_nostrifikaciju
					FROM
						idk_kandidati
					WHERE
						kandidat_status_prijave = 9 AND kandidat_ppa_partner_id IN ($partner_id)
				) kan
				LEFT JOIN idk_nd_kandidata nd
				ON kan.kandidat_dipl_id = nd.id_broj_nd_kandidata
				LEFT JOIN idk_nd_ustanove us
				ON us.id_ustanove_nd = nd.idd_ustanova_nd
				WHERE $query_nd_condition
			";
			if( !empty($requestData['search']['value']) ) {
				$sql	   .= "AND CONCAT(TRIM(kan.kandidat_ime), ' ', TRIM(kan.kandidat_prezime)) LIKE '%".$requestData['search']['value']."%' ";
			}
			
			$query 			= mysqli_query($conn, $sql) or die("serversidedata.php: get employees");
			$totalFiltered 	= mysqli_num_rows($query);

			if($requestData['length'] == -1){
				$sql		   .= "ORDER BY ". $columns[$requestData['order'][0]['column']]."   ";
			}else{
				$sql		   .= "ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
			}
			$query = mysqli_query($conn, $sql) or die("serversidedata.php: error ocured");
			$data = array();
			
			while($row = mysqli_fetch_array($query)){
				$naziv_ustanove_nd = "";

				$candidate_id		 				= $row['kandidat_id'];
				$candidate_fullname					= $row['candidate_fullname'];
				$candidate_nostrification_status 	= $row['status_nd_kandidata'];
				$candidate_check 					= $row['kandidat_check'];
				$naziv_ustanove_nd 					= $row['naziv_ustanove_nd'];

				$nestedData 	= array();
				if($flag_show_id){
					$nestedData[] 	= '<p class="text-center" style = "margin:auto;"><a>'.$candidate_id.'</a></p>';						
				}
				$nestedData[] 	= '<p class="text-center" style = "margin:auto;"><a target="_blank" href="'.getSiteUrlr().'profile?bar_id='.$bar_id.'&type='.$type.'&kandidat_id='.$candidate_check.'&n='.$overseeing_nalog.'">'.$candidate_fullname.'</a></p>';
				$nestedData[] 	= getDIPLStatusOutput($candidate_nostrification_status, $naziv_ustanove_nd);
				
				$data[] 		= $nestedData;
			}
				
			$json_data = array(
				"draw"            => intval( $requestData['draw'] ),
				"recordsTotal"    => intval( $totalData ),
				"recordsFiltered" => intval( $totalFiltered ),
				"data"            => $data
			);
			// $json_data = array();
			echo json_encode($json_data);
		}
		else if($type == 3 AND $bar_id < 8){
      $servername = $envConfig->DB_HOST;
      $username = $envConfig->DB_USER;
      $password = $envConfig->DB_PASSWORD;
      $dbname = $envConfig->DB_DATABASE;
			$columns = array();
			if($flag_show_id)
				array_push($columns, 'kandidat_id');
			array_push($columns, 'kandidat_ime', 'candidate_language_level', 'cvl_status', 'cvl_last_updated');

			$conn = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());
			$conn->query('set character_set_client=utf8mb4');
			$conn->query('set character_set_connection=utf8mb4');
			$conn->query('set character_set_results=utf8mb4');
			$conn->query('set character_set_server=utf8mb4');
			/* Database connection end */
			
			$requestData = $_REQUEST;
			$query_language_level_condition = "";
			if($bar_id == 4){
				$query_language_level_condition = " WHERE language_table.language_level IN ('0')";
			}
			else if($bar_id == 5){
				$query_language_level_condition = " WHERE language_table.language_level IN (1)";
			}
			else if($bar_id == 6){
				$query_language_level_condition = " WHERE language_table.language_level IN (2)";
			}
			else if($bar_id == 7){
				$query_language_level_condition = " WHERE language_table.language_level IN (3,4,5,6)";
			}
			$query_get_required_language_level = $db -> prepare('
				SELECT nbp_njemacki_jezik
				FROM idk_nalozi_blokovi_prijave 
				WHERE nbp_nalogid = :nalog_id
			');
			
			$query_get_required_language_level -> execute(array(':nalog_id' => $overseeing_nalog));
			
			$row_get_required_language_level = $query_get_required_language_level -> fetch();
			$required_language_level = $row_get_required_language_level['nbp_njemacki_jezik'];
			
			$candidates_all = array();
			$candidates_exist_in_cvl = array();
			$candidates_doesnt_exist_in_kj = array();
			$candidates_to_find_max_language = array();

			$query_get_needed_candidates = "
				SELECT 
					kan.kandidat_id, 
					cvl.exists_in_cvl, 
					kj.exists_in_kj
				FROM(
					SELECT sqkan.kandidat_id
					FROM idk_kandidati sqkan
					WHERE sqkan.kandidat_ppa_partner_id IN(".$partner_id.")
					AND sqkan.kandidat_status_prijave = 9                
				)kan 
				
				LEFT JOIN (
					SELECT sqkj.kj_kandidatid, 1 as exists_in_cvl
					FROM idk_kandidat_jezici sqkj
					WHERE sqkj.kj_id IN (
						SELECT cvl.cvl_id
						FROM idk_candidate_verified_languages cvl
						WHERE cvl.cvl_active = 1
					)
				)cvl
				ON 
					cvl.kj_kandidatid = kan.kandidat_id

				LEFT JOIN (
					SELECT ssqkj.kj_kandidatid, 1 as exists_in_kj
						FROM idk_kandidat_jezici ssqkj
					WHERE ssqkj.kj_naziv LIKE ('Njemački')
				)kj
				ON 
					kj.kj_kandidatid = kan.kandidat_id
				AND 
					cvl.exists_in_cvl IS NULL  
			";
			$neede_candidates = mysqli_query($conn, $query_get_needed_candidates);

			while($row_get_needed_candidates = mysqli_fetch_array($neede_candidates)){
				$cte_candidate_id   = $row_get_needed_candidates['kandidat_id'];
				$cte_exists_in_cvl  = $row_get_needed_candidates['exists_in_cvl'];
				$cte_existnt_in_kj  = $row_get_needed_candidates['exists_in_kj'];
				array_push($candidates_all, $cte_candidate_id);
				if($cte_exists_in_cvl == 1){
					array_push($candidates_exist_in_cvl, $cte_candidate_id);
				}
				else if(is_null($cte_existnt_in_kj)){
					array_push($candidates_doesnt_exist_in_kj, $cte_candidate_id);
				}
			
			}

			$candidates_to_find_max_language = array_diff($candidates_all, $candidates_exist_in_cvl, $candidates_doesnt_exist_in_kj);

			$condition_all                  = "";
			$condition_exists_in_cvl        = "";
			$condition_existnt_in_kj        = "";
			$condition_find_max_language    = "";

			if(!count($candidates_all)){
				array_push($candidates_all, '-1');
			}
			if(!count($candidates_exist_in_cvl)){
				array_push($candidates_exist_in_cvl, '-1');
			}
			if(!count($candidates_doesnt_exist_in_kj)){
				array_push($candidates_doesnt_exist_in_kj, '-1');
			}
			if(!count($candidates_to_find_max_language)){
				array_push($candidates_to_find_max_language, '-1');
			}

			$sql = "
				SELECT
					kan.kandidat_id,
					kan.kandidat_ime,
					kan.kandidat_prezime,
					kan.kandidat_check,
					CASE 
						WHEN language_table.language_level = 6
						THEN 'C2'
						WHEN language_table.language_level = 5
						THEN 'C1'
						WHEN language_table.language_level = 4
						THEN 'B2'
						WHEN language_table.language_level = 3
						THEN 'B1'
						WHEN language_table.language_level = 2
						THEN 'A2'
						WHEN language_table.language_level = 1
						THEN 'A1'
						ELSE 0
					END as candidate_language_level,
				
					language_table.cvl_status, 
					language_table.cvl_exam_date, 
					language_table.cvl_certificate_path, 
					language_table.cvl_certificate_upload_date, 
					language_table.cvl_certficate_creation_date, 
					language_table.cvl_certificate_expiration_date, 
					DATEDIFF(now(), language_table.cvl_last_updated) as cvl_last_updated
				
				FROM(
					SELECT
						sqkan.kandidat_id,
						sqkan.kandidat_status_prijave,
						sqkan.kandidat_ppa_partner_id,
						sqkan.kandidat_ime,
						sqkan.kandidat_prezime,
						sqkan.kandidat_check
					FROM
						idk_kandidati sqkan
					WHERE
						sqkan.kandidat_ppa_partner_id IN(".$partner_id.") AND sqkan.kandidat_status_prijave = 9
				) kan
				JOIN (
					SELECT
						set1_kj.kj_kandidatid,
						set1_kj.language_level,
						set1_cvl.cvl_status,
						set1_cvl.cvl_exam_date,
						set1_cvl.cvl_certificate_path, 
						set1_cvl.cvl_certificate_upload_date, 
						set1_cvl.cvl_certficate_creation_date, 
						set1_cvl.cvl_certificate_expiration_date, 
						set1_cvl.cvl_last_updated
				
					FROM(
						SELECT
							set1_sqcvl.cvl_id,
							set1_sqcvl.cvl_status,
							set1_sqcvl.cvl_exam_date,
							set1_sqcvl.cvl_certificate_path, 
							set1_sqcvl.cvl_certificate_upload_date, 
							set1_sqcvl.cvl_certficate_creation_date, 
							set1_sqcvl.cvl_certificate_expiration_date, 
							set1_sqcvl.cvl_last_updated
						FROM
							idk_candidate_verified_languages set1_sqcvl
						WHERE
							set1_sqcvl.cvl_active = 1
					) set1_cvl
					JOIN(
						SELECT 
							set1_sqkj.kj_id,
							set1_sqkj.kj_kandidatid,
						CASE 
							WHEN set1_sqkj.kj_slusanje = 'C2'
							THEN 6
							WHEN set1_sqkj.kj_slusanje = 'C1'
							THEN 5
							WHEN set1_sqkj.kj_slusanje = 'B2'
							THEN 4
							WHEN set1_sqkj.kj_slusanje = 'B1'
							THEN 3
							WHEN set1_sqkj.kj_slusanje = 'A2'
							THEN 2
							WHEN set1_sqkj.kj_slusanje = 'A1'
							THEN 1
							ELSE 0
						END as language_level
						FROM
							idk_kandidat_jezici set1_sqkj
						WHERE
							set1_sqkj.kj_naziv LIKE('Njemački')
						AND set1_sqkj.kj_kandidatid IN (".implode(',', $candidates_exist_in_cvl).")
					) set1_kj
					ON
						set1_kj.kj_id = set1_cvl.cvl_id
				
					UNION
				
					SELECT 
						set2_kj.kj_kandidatid, 
						max(set2_kj.language_level) as language_level, 
						NULL as cvl_status,
						NULL as cvl_exam_date,
						NULL as cvl_certificate_path, 
						NULL as cvl_certificate_upload_date, 
						NULL as cvl_certficate_creation_date, 
						NULL as cvl_certificate_expiration_date, 
						NULL as cvl_last_updated
					FROM(
						SELECT 
							set2_sqkj.kj_id,
							set2_sqkj.kj_kandidatid,
							CASE 
							WHEN set2_sqkj.kj_slusanje = 'C2'
							THEN 6
							WHEN set2_sqkj.kj_slusanje = 'C1'
							THEN 5
							WHEN set2_sqkj.kj_slusanje = 'B2'
							THEN 4
							WHEN set2_sqkj.kj_slusanje = 'B1'
							THEN 3
							WHEN set2_sqkj.kj_slusanje = 'A2'
							THEN 2
							WHEN set2_sqkj.kj_slusanje = 'A1'
							THEN 1
							ELSE 0
							END as language_level
						FROM 
							idk_kandidat_jezici set2_sqkj
						WHERE 
							set2_sqkj.kj_naziv LIKE ('Njemački')
						AND set2_sqkj.kj_kandidatid IN (".implode(',', $candidates_to_find_max_language).")
						)set2_kj
					GROUP BY set2_kj.kj_kandidatid
				
					UNION
				
					SELECT 
						set3_kan.kandidat_id, 
						0 as language_level, 
						NULL as cvl_status,
						NULL as cvl_exam_date,
						NULL as cvl_certificate_path, 
						NULL as cvl_certificate_upload_date, 
						NULL as cvl_certficate_creation_date, 
						NULL as cvl_certificate_expiration_date, 
						NULL as cvl_last_updated
					FROM 
						idk_kandidati set3_kan
					WHERE 
						set3_kan.kandidat_id IN(".implode(',', $candidates_doesnt_exist_in_kj).")
					)language_table 
					ON language_table.kj_kandidatid = kan.kandidat_id
				".$query_language_level_condition
			;
			// var_dump($sql);
			// exit();
			if( !empty($requestData['search']['value']) ) {
				$sql	   .= "AND CONCAT(TRIM(kandidat_ime), ' ', TRIM(kandidat_prezime)) LIKE '%".$requestData['search']['value']."%' ";
			}
			
			$query 				= mysqli_query($conn, $sql) or die("serversidedata.php: get employees");
			$totalFiltered 		= mysqli_num_rows($query);
			if($requestData['length'] == -1){
				$sql		   .= "ORDER BY ". $columns[$requestData['order'][0]['column']]."   ";
			}else{
				$sql		    .= "ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
			}

			$query = mysqli_query($conn, $sql) or die("serversidedata.php: error ocured");
			$data = array();
			while($row = mysqli_fetch_array($query)){

				$candidate_id		 							= $row['kandidat_id'];
				$candidate_first_name							= $row['kandidat_ime'];
				$candidate_last_name							= $row['kandidat_prezime'];
				$candidate_check 								= $row['kandidat_check'];
				$candidate_language_level						= $row['candidate_language_level'];
				$candidate_language_status 						= $row['cvl_status'];
				$candidate_language_exam_date 					= $row['cvl_exam_date'];
				$candidate_language_certificate_path			= $row['cvl_certificate_path'];
				$candidate_language_certificate_upload_date		= $row['cvl_certificate_upload_date'];
				$candidate_language_certificate_creation_date 	= $row['cvl_certficate_creation_date'];
				$candidate_language_certificate_expiration_date	= $row['cvl_certificate_expiration_date'];
				$candidate_language_last_updated 				= $row['cvl_last_updated'];
				

				$nestedData 	= array();
				if($flag_show_id){
					$nestedData[] 	= '<p class = "text-center" style = "margin:auto;"><a>'.$candidate_id.'</a></p>';						
				}
				$nestedData[] 	= '<p class = "text-center" style = "margin:auto;"><a target="_blank" href="'.getSiteUrlr().'profile?bar_id='.$bar_id.'&type='.$type.'&kandidat_id='.$candidate_check.'&n='.$overseeing_nalog.'">'.$candidate_first_name.' '.$candidate_last_name.'</a></p>';
				$nestedData[] 	= getLanguageLevelOutput(1, $candidate_language_level, $candidate_language_status);
				$nestedData[] 	= getLanguageLevelStatusOutput($candidate_language_status, $candidate_language_exam_date, $candidate_language_certificate_expiration_date);
				if(is_null($candidate_language_last_updated)){
					$nestedData[] 	= '<p class = "text-center" style = "margin:auto;"><b>N/A</b></p>';
				}
				else{
					$nestedData[] 	= '<p class = "text-center" style = "margin:auto;"><b>'.$candidate_language_last_updated.'</b></p>';
				}
				
				$data[] 		= $nestedData;
			}
				
			$json_data = array(
				"draw"            => intval( $requestData['draw'] ),
				"recordsTotal"    => intval( $totalData ),
				"recordsFiltered" => intval( $totalFiltered ),
				"data"            => $data
			);
			// $json_data = array();
			echo json_encode($json_data);
		}
		else if($type == 3 AND $bar_id == 8){
        $servername = $envConfig->DB_HOST;
        $username = $envConfig->DB_USER;
        $password = $envConfig->DB_PASSWORD;
        $dbname = $envConfig->DB_DATABASE;
				$columns = array();
				if($flag_show_id)
					array_push($columns, 'kandidat_id');
				array_push($columns, 'kandidat_ime', 'candidate_language_level', 'cvl_status', 'cvl_last_updated', 'status_nd_kandidata');
	
				$conn = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());
				$conn->query('set character_set_client=utf8mb4');
				$conn->query('set character_set_connection=utf8mb4');
				$conn->query('set character_set_results=utf8mb4');
				$conn->query('set character_set_server=utf8mb4');
				/* Database connection end */
				
				$requestData = $_REQUEST;
				$query_language_level_condition = "";
				if($bar_id == 4){
					$query_language_level_condition = " WHERE table_to_filter.candidate_language_level IN ('0', '') OR table_to_filter.candidate_language_level IS NULL";
				}
				else if($bar_id == 5){
					$query_language_level_condition = " WHERE table_to_filter.candidate_language_level LIKE ('A1')";
				}
				else if($bar_id == 6){
					$query_language_level_condition = " WHERE table_to_filter.candidate_language_level LIKE ('A2')";
				}
				else if($bar_id == 7){
					$query_language_level_condition = " WHERE table_to_filter.candidate_language_level IN ('B1','B2','C1','C2')";
				}
				$query_get_required_language_level = $db -> prepare('
					SELECT nbp_njemacki_jezik
					FROM idk_nalozi_blokovi_prijave 
					WHERE nbp_nalogid = :nalog_id
				');
				
				$query_get_required_language_level -> execute(array(':nalog_id' => $overseeing_nalog));
				
				$row_get_required_language_level = $query_get_required_language_level -> fetch();
				$required_language_level = $row_get_required_language_level['nbp_njemacki_jezik'];
				
				$candidates_all = array();
				$candidates_exist_in_cvl = array();
				$candidates_doesnt_exist_in_kj = array();
				$candidates_to_find_max_language = array();

				$query_get_needed_candidates = "
					SELECT 
						kan.kandidat_id, 
						cvl.exists_in_cvl, 
						kj.exists_in_kj
					FROM(
						SELECT sqkan.kandidat_id
						FROM idk_kandidati sqkan
						WHERE sqkan.kandidat_ppa_partner_id IN(".$partner_id.")
						AND sqkan.kandidat_status_prijave = 9                
					)kan 
					
					LEFT JOIN (
						SELECT sqkj.kj_kandidatid, 1 as exists_in_cvl
						FROM idk_kandidat_jezici sqkj
						WHERE sqkj.kj_id IN (
							SELECT cvl.cvl_id
							FROM idk_candidate_verified_languages cvl
							WHERE cvl.cvl_active = 1
						)
					)cvl
					ON 
						cvl.kj_kandidatid = kan.kandidat_id

					LEFT JOIN (
						SELECT ssqkj.kj_kandidatid, 1 as exists_in_kj
							FROM idk_kandidat_jezici ssqkj
						WHERE ssqkj.kj_naziv LIKE ('Njemački')
					)kj
					ON 
						kj.kj_kandidatid = kan.kandidat_id
					AND 
						cvl.exists_in_cvl IS NULL  
				";
				$neede_candidates = mysqli_query($conn, $query_get_needed_candidates);

				while($row_get_needed_candidates = mysqli_fetch_array($neede_candidates)){
					$cte_candidate_id   = $row_get_needed_candidates['kandidat_id'];
					$cte_exists_in_cvl  = $row_get_needed_candidates['exists_in_cvl'];
					$cte_existnt_in_kj  = $row_get_needed_candidates['exists_in_kj'];
					array_push($candidates_all, $cte_candidate_id);
					if($cte_exists_in_cvl == 1){
						array_push($candidates_exist_in_cvl, $cte_candidate_id);
					}
					else if(is_null($cte_existnt_in_kj)){
						array_push($candidates_doesnt_exist_in_kj, $cte_candidate_id);
					}
				
				}

				$candidates_to_find_max_language = array_diff($candidates_all, $candidates_exist_in_cvl, $candidates_doesnt_exist_in_kj);

				$condition_all                  = "";
				$condition_exists_in_cvl        = "";
				$condition_existnt_in_kj        = "";
				$condition_find_max_language    = "";

				if(!count($candidates_all)){
					array_push($candidates_all, '-1');
				}
				if(!count($candidates_exist_in_cvl)){
					array_push($candidates_exist_in_cvl, '-1');
				}
				if(!count($candidates_doesnt_exist_in_kj)){
					array_push($candidates_doesnt_exist_in_kj, '-1');
				}
				if(!count($candidates_to_find_max_language)){
					array_push($candidates_to_find_max_language, '-1');
				}
				// var_dump(count($candidates_exist_in_cvl));
				// var_dump(count($candidates_doesnt_exist_in_kj));
				// var_dump(count($candidates_to_find_max_language));

				$sql = "
					SELECT 
						table_to_filter.*, 
						us.naziv_ustanove_nd,
						CASE 
						WHEN table_to_filter.kandidat_ima_nostrifikaciju = 1
						THEN 6
						ELSE
							CASE 
								WHEN nd.status_nd_kandidata IN(1,7) OR nd.status_nd_kandidata IS NULL
								THEN 1
								ELSE nd.status_nd_kandidata
							END
						END AS status_nd_kandidata 
					FROM(
						SELECT
							kan.kandidat_id,
							kan.kandidat_ime,
							kan.kandidat_prezime,
							kan.kandidat_check,
							kan.kandidat_dipl_id,
							kan.kandidat_ima_nostrifikaciju,
							CASE 
								WHEN language_table.language_level = 6
								THEN 'C2'
								WHEN language_table.language_level = 5
								THEN 'C1'
								WHEN language_table.language_level = 4
								THEN 'B2'
								WHEN language_table.language_level = 3
								THEN 'B1'
								WHEN language_table.language_level = 2
								THEN 'A2'
								WHEN language_table.language_level = 1
								THEN 'A1'
								ELSE 0
							END as candidate_language_level,

							language_table.cvl_status, 
							language_table.cvl_exam_date, 
							language_table.cvl_certificate_path, 
							language_table.cvl_certificate_upload_date, 
							language_table.cvl_certficate_creation_date, 
							language_table.cvl_certificate_expiration_date, 
							DATEDIFF(now(), language_table.cvl_last_updated) as cvl_last_updated
						FROM (
							SELECT
								set1_kj.kj_kandidatid,
								set1_kj.language_level,
								set1_cvl.cvl_status,
								set1_cvl.cvl_exam_date,
								set1_cvl.cvl_certificate_path, 
								set1_cvl.cvl_certificate_upload_date, 
								set1_cvl.cvl_certficate_creation_date, 
								set1_cvl.cvl_certificate_expiration_date, 
								set1_cvl.cvl_last_updated

							FROM(
								SELECT
									set1_sqcvl.cvl_id,
									set1_sqcvl.cvl_status,
									set1_sqcvl.cvl_exam_date,
									set1_sqcvl.cvl_certificate_path, 
									set1_sqcvl.cvl_certificate_upload_date, 
									set1_sqcvl.cvl_certficate_creation_date, 
									set1_sqcvl.cvl_certificate_expiration_date, 
									set1_sqcvl.cvl_last_updated
								FROM
									idk_candidate_verified_languages set1_sqcvl
								WHERE
									set1_sqcvl.cvl_active = 1
							) set1_cvl
							JOIN(
								SELECT 
									set1_sqkj.kj_id,
									set1_sqkj.kj_kandidatid,
								CASE 
									WHEN set1_sqkj.kj_slusanje = 'C2'
									THEN 6
									WHEN set1_sqkj.kj_slusanje = 'C1'
									THEN 5
									WHEN set1_sqkj.kj_slusanje = 'B2'
									THEN 4
									WHEN set1_sqkj.kj_slusanje = 'B1'
									THEN 3
									WHEN set1_sqkj.kj_slusanje = 'A2'
									THEN 2
									WHEN set1_sqkj.kj_slusanje = 'A1'
									THEN 1
									ELSE 0
								END as language_level
								FROM
									idk_kandidat_jezici set1_sqkj
								WHERE
									set1_sqkj.kj_naziv LIKE('Njemački')
								AND set1_sqkj.kj_kandidatid IN (".implode(',', $candidates_exist_in_cvl).")
							) set1_kj
							ON
								set1_kj.kj_id = set1_cvl.cvl_id

							UNION

							SELECT 
								set2_kj.kj_kandidatid, 
								max(set2_kj.language_level) as language_level, 
								NULL as cvl_status,
								NULL as cvl_exam_date,
								NULL as cvl_certificate_path, 
								NULL as cvl_certificate_upload_date, 
								NULL as cvl_certficate_creation_date, 
								NULL as cvl_certificate_expiration_date, 
								NULL as cvl_last_updated
							FROM(
								SELECT 
									set2_sqkj.kj_id,
									set2_sqkj.kj_kandidatid,
									CASE 
										WHEN set2_sqkj.kj_slusanje = 'C2'
										THEN 6
										WHEN set2_sqkj.kj_slusanje = 'C1'
										THEN 5
										WHEN set2_sqkj.kj_slusanje = 'B2'
										THEN 4
										WHEN set2_sqkj.kj_slusanje = 'B1'
										THEN 3
										WHEN set2_sqkj.kj_slusanje = 'A2'
										THEN 2
										WHEN set2_sqkj.kj_slusanje = 'A1'
										THEN 1
										ELSE 0
									END as language_level
								FROM 
									idk_kandidat_jezici set2_sqkj
								WHERE 
									set2_sqkj.kj_naziv LIKE ('Njemački')
								AND set2_sqkj.kj_kandidatid IN (".implode(',', $candidates_to_find_max_language).")
							)set2_kj
							GROUP BY set2_kj.kj_kandidatid

							UNION

							SELECT 
								set3_kan.kandidat_id, 
								0 as language_level, 
								NULL as cvl_status,
								NULL as cvl_exam_date,
								NULL as cvl_certificate_path, 
								NULL as cvl_certificate_upload_date, 
								NULL as cvl_certficate_creation_date, 
								NULL as cvl_certificate_expiration_date, 
								NULL as cvl_last_updated
							FROM 
								idk_kandidati set3_kan
							WHERE 
								set3_kan.kandidat_id IN(".implode(',', $candidates_doesnt_exist_in_kj).")						
						) language_table
						JOIN 
							idk_kandidati kan
						ON 
							kan.kandidat_id = language_table.kj_kandidatid
					)table_to_filter
					LEFT JOIN 
						idk_nd_kandidata nd
					ON
						table_to_filter.kandidat_dipl_id = nd.id_broj_nd_kandidata
					LEFT JOIN 
						idk_nd_ustanove us
					ON	 
						us.id_ustanove_nd = nd.idd_ustanova_nd
				";
				if( !empty($requestData['search']['value']) ) {
					$sql	   .= "WHERE CONCAT(TRIM(table_to_filter.kandidat_ime), ' ', TRIM(table_to_filter.kandidat_prezime)) LIKE '%".$requestData['search']['value']."%' ";
				}
				
				$query 			= mysqli_query($conn, $sql) or die("serversidedata.php: get employees");
				$totalFiltered 	= mysqli_num_rows($query);
				if($requestData['length'] == -1){
					$sql		   .= "ORDER BY ". $columns[$requestData['order'][0]['column']]."   ";
				}else{
					$sql		    .= "ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
				}				$query = mysqli_query($conn, $sql) or die("serversidedata.php: error ocured");
				$data = array();
				
				while($row = mysqli_fetch_array($query)){
	
					$candidate_id		 							= $row['kandidat_id'];
					$candidate_first_name							= $row['kandidat_ime'];
					$candidate_last_name							= $row['kandidat_prezime'];
					$candidate_check 								= $row['kandidat_check'];
					$candidate_language_level						= $row['candidate_language_level'];
					$candidate_language_status 						= $row['cvl_status'];
					$candidate_language_exam_date 					= $row['cvl_exam_date'];
					$candidate_language_certificate_path			= $row['cvl_certificate_path'];
					$candidate_language_certificate_upload_date		= $row['cvl_certificate_upload_date'];
					$candidate_language_certificate_creation_date 	= $row['cvl_certficate_creation_date'];
					$candidate_language_certificate_expiration_date	= $row['cvl_certificate_expiration_date'];
					$candidate_language_last_updated 				= $row['cvl_last_updated'];
					$candidate_nostrification_status				= $row['status_nd_kandidata'];
					$candidate_institute_name		 				= $row['naziv_ustanove_nd'];
					
	
					$nestedData 	= array();
					if($flag_show_id){
						$nestedData[] 	= '<p class = "text-center" style = "margin:auto;"><a>'.$candidate_id.'</a></p>';						
					}
					$nestedData[] 	= '<p class = "text-center" style = "margin:auto;"><a target="_blank" href="'.getSiteUrlr().'profile?bar_id='.$bar_id.'&type='.$type.'&kandidat_id='.$candidate_check.'&n='.$overseeing_nalog.'">'.$candidate_first_name.' '.$candidate_last_name.'</a></p>';
					$nestedData[] 	= getLanguageLevelOutput(checkIfLanguageMeetsCriteria($required_language_level, $candidate_language_level), $candidate_language_level, $candidate_language_status);
					$nestedData[] 	= getLanguageLevelStatusOutput($candidate_language_status, $candidate_language_exam_date, $candidate_language_certificate_expiration_date);
					if(is_null($candidate_language_last_updated)){
						$nestedData[] 	= '<p class = "text-center" style = "margin:auto;"><b>N/A</b></p>';
					}
					else{
						$nestedData[] 	= '<p class = "text-center" style = "margin:auto;"><b>'.$candidate_language_last_updated.'</b></p>';
					}
					$nestedData[] 	= getDIPLStatusOutput($candidate_nostrification_status, $candidate_institute_name);
					
					$data[] 		= $nestedData;
				}
					
				$json_data = array(
					"draw"            => intval( $requestData['draw'] ),
					"recordsTotal"    => intval( $totalData ),
					"recordsFiltered" => intval( $totalFiltered ),
					"data"            => $data
				);
				echo json_encode($json_data);


		}
		else if($type == 4){
      $servername = $envConfig->DB_HOST;
      $username = $envConfig->DB_USER;
      $password = $envConfig->DB_PASSWORD;
      $dbname = $envConfig->DB_DATABASE;
			$columns = array();
			if($flag_show_id){
				array_push($columns, 'kandidat_id');
			}
			
			
			if($bar_id != 4){
				array_push($columns, 'candidate_fullname', 'days_on_status');
				array_push($columns, 'kandidat_status_prijave');
			}
			
			if($bar_id == 1){
				array_push($columns, 'main.candidate_fullname', 'days_on_status');
				array_push($columns, 'deadline', 'document_points/document_points_max');
			}
			else if($bar_id == 2){
				array_push($columns, 'candidate_fullname', 'days_on_status');
				array_push($columns, 'kandidat_latest_reserved_time', 'datum_termina');
			}
			else if($bar_id == 3){
				array_push($columns, 'candidate_fullname', 'days_on_status');
				array_push($columns, 'kandidat_viza_vrijedi_do');
			}
			else if($bar_id == 4){
				array_push($columns, 'candidate_fullname', 'days_on_status');
			}

			$conn = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());
			$conn->query('set character_set_client=utf8mb4');
			$conn->query('set character_set_connection=utf8mb4');
			$conn->query('set character_set_results=utf8mb4');
			$conn->query('set character_set_server=utf8mb4');
			/* Database connection end */
			$requestData = $_REQUEST;
			$query_nd_condition 	= "";
			$query_add_to_select	= "";
			if($bar_id == 1){
				$query_nd_condition 	= " kan.kandidat_status_prijave IN (12,21) ";
			}
			else if($bar_id == 2){
				$query_nd_condition 	= " kan.kandidat_status_prijave IN (15,18) ";
				$query_add_to_select 	= ", kan.datum_termina";
			}
			else if($bar_id == 3){
				$query_nd_condition 	= " 
					(kan.kandidat_status_prijave = 27 
					OR
						(
							kan.kandidat_status_prijave = 24
							AND	kan.kandidat_id IN (
								SELECT sqvi.vi_cand_id
								FROM idk_pp_visa_incomplete sqvi
								WHERE 
									sqvi.vi_status = 1
								AND	sqvi.vi_nalog_id IN (".$nalog_id.")
								AND (
										sqvi.vi_type = 1
									OR(
											sqvi.vi_type = 2
										AND sqvi.vi_complaint = 0
									)
								)
							)
						)
					)
				";
				$query_add_to_select 	= ", kan.kandidat_viza_vrijedi_do";
			}
			else if($bar_id == 4){
				$query_nd_condition 	= " kan.kandidat_status_prijave IN (4,10)";
			}

			$sql = "";

			if($bar_id != 1){
				$sql = "
					SELECT
						kan.kandidat_id, CONCAT(kan.kandidat_ime, ' ', kan.kandidat_prezime) as candidate_fullname, kan.kandidat_check, kan.kandidat_status_prijave, kan.kandidat_latest_reserved_time, DATEDIFF(now(), kan.kandidat_latest_reserved_time) as days_on_status ".$query_add_to_select."
					FROM
						idk_kandidati kan
					WHERE $query_nd_condition
					AND kan.kandidat_ppa_partner_id IN (".$partner_id.")

				";
				// var_dump($sql);
			}
			else{
				$sql = "
					SELECT *,
					((main.document_null + main.document_uploaded + main.document_checked + main.document_waiting_original + main.document_sent + main.document_have_original + main.document_ready + main.document_failed_check + main.document_rejected)*6) as document_points_max,
    				(main.document_null*0 + main.document_uploaded*1 + main.document_checked*2 + main.document_waiting_original*3 + main.document_sent*4 + main.document_have_original*5 + main.document_ready*6 + main.document_failed_check*0 + main.document_rejected*0) as document_points
					FROM(
						SELECT
							kan.kandidat_id, 
							CONCAT(TRIM(kan.kandidat_ime), ' ', TRIM(kan.kandidat_prezime)) as candidate_fullname,
							kan.kandidat_check,
							kan.kandidat_status_prijave,
							kan.kandidat_latest_reserved_time,
							DATEDIFF(now(), kan.kandidat_latest_reserved_time) as days_on_status,
							vi.vi_deadline_date_candidate as deadline,
							SUM(
								CASE 
									WHEN doc.doc_status IS NULL AND crd.crd_done_by = 1
									THEN 1
									ELSE 0
								END
							) as document_null,
							SUM(
								CASE 
									WHEN doc.doc_status = 3 AND crd.crd_done_by = 1
									THEN 1
									ELSE 0
								END
							) as document_uploaded,
							SUM(
								CASE 
									WHEN doc.doc_status = 6 AND crd.crd_done_by = 1
									THEN 1
									ELSE 0
								END
							) as document_checked,
							SUM(
								CASE 
									WHEN doc.doc_status = 9 AND crd.crd_done_by = 1
									THEN 1
									ELSE 0
								END
							) as document_waiting_original,
							SUM(
								CASE 
									WHEN doc.doc_status = 12 AND crd.crd_done_by = 1
									THEN 1
									ELSE 0
								END
							) as document_sent,
							SUM(
								CASE 
									WHEN doc.doc_status = 15 AND crd.crd_done_by = 1
									THEN 1
									ELSE 0
								END
							) as document_have_original,
							SUM(
								CASE 
									WHEN doc.doc_status = 18 AND crd.crd_done_by = 1
									THEN 1
									ELSE 0
								END
							) as document_ready,
							SUM(
								CASE 
									WHEN doc.doc_status = 21 AND crd.crd_done_by = 1
									THEN 1
									ELSE 0
								END
							) as document_failed_check,
							SUM(
								CASE 
									WHEN doc.doc_status = 24 AND crd.crd_done_by = 1
									THEN 1
									ELSE 0
								END
							) as document_rejected
						FROM (
							SELECT 
								sqkan.kandidat_id,
								sqkan.kandidat_ime,
								sqkan.kandidat_prezime,
								sqkan.kandidat_check,
								sqkan.kandidat_status_prijave,
								sqkan.kandidat_latest_reserved_time,
								sqkan.kandidat_nalog_id,
								sqkan.kandidat_nacin_odlaska
							FROM 
								idk_kandidati sqkan
							WHERE 
								sqkan.kandidat_ppa_partner_id IN (".$partner_id.")
							AND	
								sqkan.kandidat_status_prijave IN(21,24)
						)kan
						JOIN(
							SELECT 
								sqcrd.crd_id, sqcrd.crd_cand_id, sqcrd.crd_done_by
							FROM 
								idk_pp_cand_required_documents sqcrd
							WHERE 
								sqcrd.crd_nalog_id IN (".$nalog_id.")
							AND sqcrd.crd_status = 1
						)crd
						ON
							kan.kandidat_id = crd.crd_cand_id
						LEFT JOIN(
							SELECT 
								sqdoc.doc_id,
								sqdoc.doc_candidate_id, 
								sqdoc.doc_nalog_id, 
								sqdoc.doc_status,
								sqdoc.doc_nrd_id,
								sqdoc.doc_crd_id
							FROM 
								idk_pp_documents sqdoc
							WHERE 
								sqdoc.doc_status != 27
							
						)doc
						ON
							crd.crd_id = doc.doc_crd_id AND doc.doc_candidate_id = kan.kandidat_id 
						JOIN (
							SELECT sqvi.vi_cand_id, sqvi.vi_deadline_date_candidate
							FROM idk_pp_visa_incomplete sqvi
							WHERE 
								sqvi.vi_status = 1
							AND	sqvi.vi_nalog_id IN (".$nalog_id.")
							AND (
									sqvi.vi_type = 1
								OR(
										sqvi.vi_type = 2
									AND sqvi.vi_complaint = 1
								)
							)
						) vi
						ON
							vi.vi_cand_id = kan.kandidat_id
						GROUP BY (kan.kandidat_id)		
						
						UNION 
				
						SELECT
							kan.kandidat_id, 
							CONCAT(TRIM(kan.kandidat_ime), ' ', TRIM(kan.kandidat_prezime)) as candidate_fullname,
							kan.kandidat_check,
							kan.kandidat_status_prijave,
							kan.kandidat_latest_reserved_time,
							DATEDIFF(now(), kan.kandidat_latest_reserved_time) as days_on_status,
							NULL as deadline,
							CASE
								WHEN kan.kandidat_nacin_odlaska = 2 OR (kan.kandidat_nacin_odlaska != 2 AND (nd.full_recognition = 1 OR nd.full_recognition = 2))
								THEN
									SUM(
										CASE 
											WHEN doc.doc_status IS NULL AND nrd.nrd_done_by = 1 AND nrd.nrd_west_balkan = 1
											THEN 1
											ELSE 0
										END
									)
								WHEN kan.kandidat_nacin_odlaska = 3
									THEN
										SUM(
											CASE 
												WHEN doc.doc_status IS NULL AND nrd.nrd_done_by = 1 AND nrd.nrd_work_experience = 1
												THEN 1
												ELSE 0
											END
										) 
								WHEN kan.kandidat_nacin_odlaska = 0
									THEN
										SUM(
											CASE 
												WHEN doc.doc_status IS NULL AND nrd.nrd_done_by = 1 AND nrd.nrd_skilled_candidates = 1
												THEN 1
												ELSE 0
												END
										) 
							END as document_null,
							SUM(
								CASE 
									WHEN doc.doc_status = 3 AND nrd.nrd_done_by = 1
									THEN 1
									ELSE 0
								END
							) as document_uploaded,
							SUM(
								CASE 
									WHEN doc.doc_status = 6 AND nrd.nrd_done_by = 1
									THEN 1
									ELSE 0
								END
							) as document_checked,
							SUM(
								CASE 
									WHEN doc.doc_status = 9 AND nrd.nrd_done_by = 1
									THEN 1
									ELSE 0
								END
							) as document_waiting_original,
							SUM(
								CASE 
									WHEN doc.doc_status = 12 AND nrd.nrd_done_by = 1
									THEN 1
									ELSE 0
								END
							) as document_sent,
							SUM(
								CASE 
									WHEN doc.doc_status = 15 AND nrd.nrd_done_by = 1
									THEN 1
									ELSE 0
								END
							) as document_have_original,
							SUM(
								CASE 
									WHEN doc.doc_status = 18 AND nrd.nrd_done_by = 1
									THEN 1
									ELSE 0
								END
							) as document_ready,
							SUM(
								CASE 
									WHEN doc.doc_status = 21 AND nrd.nrd_done_by = 1
									THEN 1
									ELSE 0
								END
							) as document_failed_check,
							SUM(
								CASE 
									WHEN doc.doc_status = 24 AND nrd.nrd_done_by = 1
									THEN 1
									ELSE 0
								END
							) as document_rejected
						FROM (
							SELECT 
								sqkan.kandidat_id,
								sqkan.kandidat_ime,
								sqkan.kandidat_prezime,
								sqkan.kandidat_check,
								sqkan.kandidat_status_prijave,
								sqkan.kandidat_latest_reserved_time,
								sqkan.kandidat_nalog_id,
								sqkan.kandidat_nacin_odlaska
							FROM 
								idk_kandidati sqkan
							WHERE 
								sqkan.kandidat_ppa_partner_id IN (".$partner_id.")
							AND	sqkan.kandidat_status_prijave IN (12)
						)kan
						JOIN(
							SELECT 
								sqnrd.nrd_id, sqnrd.nrd_nalog_id, sqnrd.nrd_done_by, sqnrd.nrd_west_balkan, sqnrd.nrd_skilled_candidates, sqnrd.nrd_work_experience
							FROM 
								idk_pp_nalog_required_documents sqnrd
							INNER JOIN idk_kandidati sqnrdkan ON sqnrd.nrd_nalog_id = sqnrdkan.kandidat_nalog_id AND sqnrdkan.kandidat_status_prijave IN (12) AND sqnrdkan.kandidat_ppa_partner_id IN (".$partner_id.")
							LEFT JOIN(
								SELECT 
									full_recognition,
									id_cand_job
								FROM 
									idk_nostrifikovane_diplome
							) sqnrdnd
							ON sqnrdkan.kandidat_id = sqnrdnd.id_cand_job
							WHERE 
								sqnrd.nrd_nalog_id IN (".$nalog_id.")
								AND
								CASE
									WHEN sqnrdkan.kandidat_nacin_odlaska = 2 OR (sqnrdkan.kandidat_nacin_odlaska != 2 AND (sqnrdnd.full_recognition = 1 OR sqnrdnd.full_recognition = 2))
										THEN
											sqnrd.nrd_west_balkan = 1
									WHEN sqnrdkan.kandidat_nacin_odlaska = 3
										THEN
											sqnrd.nrd_work_experience = 1
									WHEN sqnrdkan.kandidat_nacin_odlaska = 0
										THEN 
											sqnrd.nrd_skilled_candidates = 1
								END
								AND sqnrd.nrd_status = 1
								GROUP BY sqnrd.nrd_id
						)nrd
						ON
							kan.kandidat_nalog_id = nrd.nrd_nalog_id
						LEFT JOIN(
							SELECT 
								sqdoc.doc_id,
								sqdoc.doc_candidate_id, 
								sqdoc.doc_nalog_id, 
								sqdoc.doc_status,
								sqdoc.doc_nrd_id
							FROM 
								idk_pp_documents sqdoc
							WHERE 
								sqdoc.doc_status != 27
							
						)doc
						ON
							nrd.nrd_id = doc.doc_nrd_id AND doc.doc_candidate_id = kan.kandidat_id  
						LEFT JOIN(
							SELECT 
								sq_nd.full_recognition,
								sq_nd.id_cand_job
							FROM 
								idk_nostrifikovane_diplome sq_nd
						) nd
						ON 
							kan.kandidat_id = nd.id_cand_job
						GROUP BY (kan.kandidat_id)
					) main
				";
			}
			if( !empty($requestData['search']['value']) ) {
				if($bar_id != 1){
					$sql	   .= "AND CONCAT(TRIM(kan.kandidat_ime), ' ', TRIM(kan.kandidat_prezime)) LIKE '%".$requestData['search']['value']."%' ";
				}
				else{
					$sql	   .= " WHERE main.candidate_fullname LIKE '%".$requestData['search']['value']."%'";
					
				}
			}
			$query 			= mysqli_query($conn, $sql) or die("serversidedata.php: get employees");
			$totalFiltered 	= mysqli_num_rows($query);
			
			if($requestData['length'] == -1){
				$sql		   .= "ORDER BY ". $columns[$requestData['order'][0]['column']]."   ";
			}else{
				$sql		    .= "ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
			}
			
			$query = mysqli_query($conn, $sql) or die("serversidedata.php: error ocured");
			$data = array();
			
			while($row = mysqli_fetch_array($query)){

				$naziv_ustanove_nd = "";

				$candidate_id		 				= $row['kandidat_id'];
				$candidate_fullname					= $row['candidate_fullname'];
				$candidate_check 					= $row['kandidat_check'];
				$candidate_status 					= $row['kandidat_status_prijave'];
				$days_on_status 					= $row['days_on_status'];
				// var_dump($days_on_status);
				$datum_termina 						= $row['datum_termina'];
				$candidat_latest_reserved_time		= $row['kandidat_latest_reserved_time'];
				$candidat_visa_expiration_date		= $row['kandidat_viza_vrijedi_do'];
				
				$document_deadline					= $row['deadline'];
				$document_required_points 			= $row['document_points_max'];
				$document_candidate_points 			= $row['document_points'];
				$document_candidate_percentage 		= 0;
				$documents_not_uploaded_percentage 	= "";

				if($document_candidate_points != 0){
					$document_candidate_percentage 	= $document_candidate_points * 100 / $document_required_points;
				}
				else{
					if($document_required_points == 0){
						$document_candidate_percentage = 100;
					}
					else{
						$documents_not_uploaded_percentage = "0%";
					}
				}
				if(is_null($datum_termina)){
					$datum_termina = $txtArray['Nije poznato'][$languageUser];
				}
				else{
					$datum_termina = date("d.m.Y", strtotime($datum_termina));
				}
				$progress_bar_border_left 	= "";
				$progress_bar_border_right 	= "";
				if($document_candidate_percentage == 100){
					$progress_bar_border_right = "border-top-right-radius:0.5rem;border-bottom-right-radius:0.5rem;";
				}
				else if($documents_not_uploaded_percentage == "0%"){
					$progress_bar_border_left = "border-top-left-radius:0.5rem;border-bottom-left-radius:0.5rem;";
				}
				$nestedData 	= array();
				if($flag_show_id){
					$nestedData[] 	= '<p class="text-center" style = "margin:auto;"><a>'.$candidate_id.'</a></p>';						
				}
				$nestedData[] 	= '<p class="text-center" style = "margin:auto;"><a target="_blank" href="'.getSiteUrlr().'profile?bar_id='.$bar_id.'&type='.$type.'&kandidat_id='.$candidate_check.'&n='.$overseeing_nalog.'">'.$candidate_fullname.'</a></p>';
				$nestedData[] 	= '<p class="text-center" style = "margin:auto;"><b>'.$days_on_status.'</b></p>';
				if($bar_id != 4){
					$nestedData[] 	= getStatusPrijaveOutput($candidate_status);
				}
				if($bar_id == 1){
					if($candidate_status == 12 OR is_null($document_deadline))
						$nestedData[] = '<p style = "margin:auto;" class="text-center"><b>N/A</b></p>';
					if(!is_null($document_deadline) AND ($candidate_status == 21 OR $candidate_status == 24))
						$nestedData[] = '<p style = "margin:auto;" class="text-center">'.date(('d.m.Y'),strtotime($document_deadline)).'</p>';
					$nestedData[] = '
						<div style = "margin:auto;" class="progress">
							<div class="progress-bar" role="progressbar" style="'.$progress_bar_border_right.'background-color: #388923;border-bottom-left-radius: 0.5rem;border-top-left-radius: 0.5rem;font-size:14px; font-weight: bold; width: '.$document_candidate_percentage.'%" aria-valuenow="'.$document_candidate_points.'" aria-valuemin="0" >'.intval($document_candidate_percentage).'%</div>
							<div class="progress-bar" role="progressbar" style="'.$progress_bar_border_left.'border-top-right-radius: 0.5rem;border-bottom-right-radius: 0.5rem;background-color: #dfdfdf;font-size:14px; font-weight: bold; color:#ff4c4c;width: '.(100 - $document_candidate_percentage).'%" aria-valuenow="'.($document_required_points - $document_candidate_points).'" aria-valuemin="0">'.$documents_not_uploaded_percentage.'</div>
						</div>
					';
				}
				else if($bar_id == 2){
					$nestedData[] 	= '<p style = "margin:auto;" class="text-center">'.date("d.m.Y", strtotime($candidat_latest_reserved_time)).'</p>';
					$nestedData[] 	= '<p style = "margin:auto;" class="text-center">'.$datum_termina.'</p>';
				}
				else if($bar_id == 3){
					if(is_null($candidat_visa_expiration_date)){
						$nestedData[] 	= '<p style = "margin:auto;" class="text-center"><b>N/A</b></p>';
					}
					else{
						$nestedData[] 	= '<p style = "margin:auto;" class="text-center">'.date("d.m.Y", strtotime($candidat_visa_expiration_date)).'</p>';
					}
				}
				$data[] 		= $nestedData;
			}
				
			$json_data = array(
				"draw"            => intval( $requestData['draw'] ),
				"recordsTotal"    => intval( $totalData ),
				"recordsFiltered" => intval( $totalFiltered ),
				"data"            => $data
			);
			echo json_encode($json_data);
		}
	break;
}
?>
