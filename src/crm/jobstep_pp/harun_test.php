<?php 
include("includes/function.php");
include("includes/language/language.php"); 
$languageUser = getLanguageForUser($userId);
$page = $_REQUEST['page'];

	switch($page){
		case "export":
			$nalog_id 		= NULL;
			$menu 			= NULL;
			$action_flag 	= NULL;
			$partner_id 	= NULL;
			$user_type 		= NULL;
			if(isset($_REQUEST["nalog_id"]))
				$nalog_id 		= $_REQUEST["nalog_id"];
			if(isset($_REQUEST["bar_id"]))
				$bar_id 		= $_REQUEST["bar_id"];
			if(isset($_REQUEST["type"]))
				$type			= $_REQUEST["type"];
			if(isset($_REQUEST["appointments"]))
				$appointment_ids= $_REQUEST["appointments"];
			if(isset($_REQUEST["partners"]))
				$partner_ids	= $_REQUEST["partners"];

			$overseeing_nalog = $nalog_id;
			if($nalog_id == 222){
				$nalog_id = '217,218,219,220,222';
			}
			
			if($type == 1){
				$uslov_appointment = "";
				
				if($appointment_ids != null){
				$appointment_ids = implode(',', $appointment_ids);
				$uslov_appointment .= "AND pap.pap_id IN (".$appointment_ids.") ";
				}
				
				if($partner_ids != null){
					$partner_ids = implode(',', $partner_ids);
					$uslov_appointment .= "AND sq_kan.kandidat_ppa_partner_id IN (".$partner_ids.") ";
				}
				
				if($bar_id == 0){
				$search_like = "Obra";
				}
				
				if($bar_id == 2){
					$search_like = "Casting";
				}
				
				if($bar_id == 3){
					$search_like = "Intervju";
					$add_to_select = ",  pca.pca_appointment_id, pap.pap_date, pap.pap_city, pca.pca_time, CONCAT(pap.pap_date, ' ', pca.pca_time) as sortable_appointment";
					$add_to_join = "
						LEFT JOIN(
							SELECT sq_pap.pap_date, sq_pap.pap_city, sq_pap.pap_id
							FROM idk_pp_appointments sq_pap
							WHERE sq_pap.pap_nalog_id IN (".$nalog_id.")
						) pap
						ON pca.pca_appointment_id  = pap.pap_id
					";
				}
				
				if($bar_id == 4){
					// $search_like = "Prihvacen";
					$search_like = "Ugovor";
				}
				
				if($bar_id == 5){
					// $search_like = "Prihvacen";
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
					WHERE pro.project_name LIKE ('%".$search_like."%')
					AND pro.project_nalogid IN (".$nalog_id.")
					AND kan.kandidat_status != 3
				");
				
				$query_get_candidate_ids -> execute();
				$candidate_ids = array();
				while($row_get_candidate_ids = $query_get_candidate_ids->fetch()){
					array_push($candidate_ids, $row_get_candidate_ids['kandidat_id']);
				}
				$candidate_ids = implode(',',$candidate_ids);
				
				if($candidate_ids != ''){
					$sql = "				
						SELECT sq_kan.kandidat_full_name, sq_kan.kandidat_vozacka_kategorija, sq_kan.kandidat_id, kj.kj_slusanje, sq_ke.ke_naziv, sq_kan.kandidat_check,
						sq_ke.ke_naziv_de, sq_kan.kandidat_pp_razlog_odbijanja, sq_ke.ke_vrsta_obrazovanja, sq_ke.ss_naziv_de, sq_ke.ss_naziv, kj.kj_id, sq_kan.kandidat_ppa_partner_id,  pca.pca_avg_rating".$add_to_select."
						FROM (
							SELECT  CONCAT (kan.kandidat_ime, ' ', kan.kandidat_prezime) AS kandidat_full_name, kan.kandidat_pp_razlog_odbijanja, kan.kandidat_vozacka_kategorija, kan.kandidat_id, kan.kandidat_ppa_partner_id, kan.kandidat_check
							FROM idk_kandidati kan
							WHERE kan.kandidat_id IN (".$candidate_ids.")
						) sq_kan
						LEFT JOIN (
							SELECT sq_kj.kj_id, sq_kj.kj_slusanje, sq_kj.kj_kandidatid
							FROM(
								SELECT ssq_kj.kj_id, ssq_kj.kj_slusanje, ssq_kj.kj_kandidatid
								FROM idk_kandidat_jezici ssq_kj
								WHERE ssq_kj.kj_kandidatid IN (".$candidate_ids.")
								AND ssq_kj.kj_naziv = 'Njemacki'
								ORDER BY ssq_kj.kj_slusanje DESC
							) sq_kj
							GROUP BY sq_kj.kj_kandidatid
						) kj
						ON kj.kj_kandidatid = sq_kan.kandidat_id
						LEFT JOIN (
							SELECT ke.ke_kandidat_id, ke.ke_naziv, ke.ke_naziv_de, ke.ke_vrsta_obrazovanja, ke.ss_naziv, ke.ss_naziv_de
							FROM (
								SELECT ssq_ke.ke_kandidat_id, ssq_ke.ke_naziv, ssq_ke.ke_naziv_de, sq_skole.skola_tip_obrazovanja AS ke_vrsta_obrazovanja, ssq_sm.ss_naziv, ssq_sm.ss_naziv_de
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
							WHERE sq_pca.pca_kandidat_id IN (".$candidate_ids.")
						) pca
						ON pca.pca_kandidat_id = sq_kan.kandidat_id
						".$add_to_join."
						WHERE sq_kan.kandidat_id IS NOT NULL
						".$uslov_appointment."		
					";
					
					$get_candidate_table = $db->prepare($sql);
					$get_candidate_table->execute();
						
					if($bar_id == 3){
					?>
						<div class="" style="height:0px;overflow:hidden;">
							<table class="table" id="INTERVIEWTABLE_project" style="100%;" class="display">
								<thead>
									<tr>
										<th style="width:30px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">#</th>
										<th style="width:70px;height:30px;text-align:center;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Name</th>
										<th style="width:250px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Sprache</th>
										<th style="width:100px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Ausbildung</th>
										<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Berufsbezeichung</th>
										<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Termin</th>
										<th style="width:150px;height:30px;text-align:left;padding:15px 15px;background:rgb(146,208,80);border-right:1px solid #ccc;">Bewertung</th>
									</tr>
								</thead>
								<tbody>
								<?php
									$candidate_count = 1;
									while($result_candidate_table_interview = $get_candidate_table->fetch()){
										$kandidat_id		 			= $result_candidate_table_interview['kandidat_id'];
										$kandidat_full_name 			= $result_candidate_table_interview['kandidat_full_name'];
										$kj_slusanje 					= $result_candidate_table_interview['kj_slusanje'];
										$kandidat_vozacka_kategorija 	= $result_candidate_table_interview['kandidat_vozacka_kategorija'];
										$ke_naziv 						= $result_candidate_table_interview['ke_naziv'];
										$ke_naziv_de 					= $result_candidate_table_interview['ke_naziv_de'];
										$ke_vrsta_obrazovanja 			= $result_candidate_table_interview['ke_vrsta_obrazovanja'];
										$ss_naziv 						= $result_candidate_table_interview['ss_naziv'];
										$ss_naziv_de 					= $result_candidate_table_interview['ss_naziv_de'];
										$pca_avg_rating 				= $result_candidate_table_interview['pca_avg_rating'];
										$kandidat_ppa_partner_id 		= $result_candidate_table_interview['kandidat_ppa_partner_id'];
										$kandidat_check 				= $result_candidate_table_interview['kandidat_check'];
										$kandidat_pp_razlog_odbijanja	= $result_candidate_table_interview['kandidat_pp_razlog_odbijanja'];
										$flag_action_buttons_show 		= false;
										
								?>
									<tr>
										<td style="width:30px; text-align:center;padding:15px 15px;"><?php echo $candidate_count; ?></td>
										<td style="width:70px; text-align:center;padding:15px 15px;"><?php echo $kandidat_full_name; ?></td>
										<td style="width:250px; text-align:left;padding:15px 15px;" class="text-success"><?php echo $kj_slusanje; ?></td>
										<td style="width:100px; text-align:left;padding:15px 15px;"><?php echo $ke_vrsta_obrazovanja; ?></td>
										<td style="width:150px; text-align:left;padding:15px 15px;"><?php echo $ke_naziv_de; ?></td>
										<td style="width:150px; text-align:center;padding:15px 15px;"><?php echo $pap_city.', '.date('d.m.y H:i', strtotime($pap_date.' '.$pca_time)); ?></td>
										<td style="width:150px; text-align:center;padding:15px 15px;"><?php echo $pca_avg_rating; ?></td>
									</tr>
								<?php
									}
								?>
								</tbody>
							</table>
						</div>
					<?php
					}
				}
			}
		break;
	}
