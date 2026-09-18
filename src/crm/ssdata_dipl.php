<?php 
	include("includes/functions.php");
	require_once("includes/env.php");
	$page = $_REQUEST["page"];
	switch($page)
	{
		case "lista_lead":
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
			$uslov_type = intval($_REQUEST['type_ispis']);
			$logirani_zaposlenik = intval($_REQUEST['logirani_zaposlenik']);
			$uslov_team = intval($_REQUEST['team_ispis']);
			$uslov_prioritet = intval($_REQUEST['prioritet']);
			$employess_id_team = implode(", ", getIdOfEmployeeTeam($uslov_team));
			
			//izuzetak zbog zahtjeva od Adila - zaposlenici iz team = 4 (ssc) sa permisijom supervizor PM trebaju vidjeti sve kandidate
			
			$supervizorStatus = explode( ',' , getEmployeeSupervizor());
			
			$flagException = 0;
			if($uslov_type == 10 AND $uslov_team == 4 AND (in_array("2", $supervizorStatus) OR in_array("3", $supervizorStatus))){
				$flagException = 1;
			}
			
			$columns = array(
				0 => 'id_broj_nd_kandidata',
				1 => 'ime_nd_kandidata',
				2 => 'mobilni_nd_kandidata',
				3 => 'zadnja_komunikacija',
				4 => 'status_nd_kandidata',
				5 => 'vrijeme_kreiranja_nd_kandidata',
				6 => 'povijest_nd_kandidata',
				7 => 'zaduzen_zaposlenik_nd_kandidata'
			);

			if($uslov_team == 1 OR $flagException == 1){
				$uslov_sql_zap = " zaduzen_zaposlenik_nd_kandidata is not null";
			}else{
				$uslov_sql_zap = " zaduzen_zaposlenik_nd_kandidata IN (".$employess_id_team.")";
			}
			
			if($uslov_type == 1){
				$uslov_ispis = "status_nd_kandidata = 1 AND pstatus_nd_kandidata = 1 AND zaduzen_zaposlenik_nd_kandidata = ".$logirani_zaposlenik." ";
			}else if($uslov_type == 2){
				$uslov_ispis = "status_nd_kandidata = 1 AND pstatus_nd_kandidata = 6 AND zaduzen_zaposlenik_nd_kandidata = ".$logirani_zaposlenik." ";
			}else if($uslov_type == 3){
				$uslov_ispis = "status_nd_kandidata = 1 AND pstatus_nd_kandidata = 2 AND zaduzen_zaposlenik_nd_kandidata = ".$logirani_zaposlenik." ";
			}else if($uslov_type == 4){
				$uslov_ispis = "status_nd_kandidata = 1 AND pstatus_nd_kandidata = 3 AND zaduzen_zaposlenik_nd_kandidata = ".$logirani_zaposlenik." ";
			}else if($uslov_type == 5){
				$uslov_ispis = "status_nd_kandidata = 1 AND pstatus_nd_kandidata = 4 AND zaduzen_zaposlenik_nd_kandidata = ".$logirani_zaposlenik." ";
			}else if($uslov_type == 6){
				$uslov_ispis = "status_nd_kandidata = 1 AND pstatus_nd_kandidata = 5 AND zaduzen_zaposlenik_nd_kandidata = ".$logirani_zaposlenik." ";
			}else if($uslov_type == 7){
				$uslov_ispis = "status_nd_kandidata IN (2,3,4,5) ";
			}else if($uslov_type == 8){
				$uslov_ispis = "status_nd_kandidata = 6 ";
			}else if($uslov_type == 9){
				$uslov_ispis = "status_nd_kandidata = 7 AND zaduzen_zaposlenik_nd_kandidata = ".$logirani_zaposlenik." ";
			}else if($uslov_type == 10){
				$uslov_ispis = "vrijeme_kreiranja_nd_kandidata is not null";
			}else if($uslov_type == 11){
				$uslov_ispis = "((status_nd_kandidata IN (1) AND pstatus_nd_kandidata IN (1,2,3,4,5,6,7,8,9,10,11,12)) OR (status_nd_kandidata IN (7))) AND status_pp = ".$uslov_prioritet." AND zaduzen_zaposlenik_nd_kandidata = ".$logirani_zaposlenik." ";
			}else if($uslov_type == 12){
				$uslov_ispis = "((status_nd_kandidata IN (1) AND pstatus_nd_kandidata IN (1,2,3,4,5,6,7,8,9,10,11,12)) OR (status_nd_kandidata IN (7))) AND status_pp IN (1,2) ";
			}else if($uslov_type == 13){
				if($uslov_prioritet == 1){
					$uslov_ispis1 = " zaduzen_zaposlenik_nd_kandidata = ".$logirani_zaposlenik." ";
				}else{
					$uslov_ispis1 = " zaduzen_zaposlenik_nd_kandidata is not null";
				}
				$uslov_ispis = " povijest_nd_kandidata = 3 AND kandidat_idd is not null AND status_nd_kandidata = 1 AND pstatus_nd_kandidata = 1 AND ".$uslov_ispis1." ";
			}else  if($uslov_type == 14){
				if($uslov_prioritet == 1){
					$uslov_ispis1 = " zaduzen_zaposlenik_nd_kandidata = ".$logirani_zaposlenik." ";
				}else{
					$uslov_ispis1 = " zaduzen_zaposlenik_nd_kandidata is not null";
				}
				$uslov_ispis = " povijest_nd_kandidata = 5 AND (kampanja_id = 100 OR kampanja_id = 157) AND status_nd_kandidata = 1 AND pstatus_nd_kandidata = 1 AND ".$uslov_ispis1." ";
			}else{
				//ovaj tip se koristi kod ispisa svih kandidata za odredenog zaposlenika na file: nostrifikacija_diploma.php na case "menadzer_pregled_kandidata"
				$uslov_ispis = "status_nd_kandidata is not null AND zaduzen_zaposlenik_nd_kandidata = ".$logirani_zaposlenik." ";
			}
			
			$sql = "SELECT 
						id_broj_nd_kandidata,
						ime_nd_kandidata,
						prezime_nd_kandidata,
						mobilni_nd_kandidata,
						email_nd_kandidata,
						status_nd_kandidata,
						pstatus_nd_kandidata,
						vrijeme_kreiranja_nd_kandidata,
						povijest_nd_kandidata,
						povijest_vrsta_nd_kandidata,
						kampanja_id,
						zadnja_komunikacija,
						dodao_zaposlenik_nd_kandidata,
						zaduzen_zaposlenik_nd_kandidata,
						status_pp
					FROM idk_nd_kandidata
					WHERE
						".$uslov_ispis." AND ".$uslov_sql_zap."
					";
			$query = mysqli_query($conn, $sql) or die();
			$totalData = mysqli_num_rows($query);
			$totalFiltered = $totalData;
			
			if( !empty($requestData['search']['value']) ) {
				
				$s_value = $requestData['search']['value'];
				$space_pos = strpos($s_value, ' ');
				
				if($space_pos){
					$f_name = substr($s_value,0,$space_pos);
					$l_name = substr($s_value,$space_pos+1);
					
					$query_idemp = $db->prepare("SELECT employee_id FROM idk_employees WHERE employee_firstname LIKE '%$f_name%' AND employee_lastname LIKE '%$l_name%'");
					$query_idemp->execute();
					$row_idemp = $query_idemp->fetch();
					$search_idemp = $row_idemp['employee_id'];
				}else{
					$search_idemp = "";
				}
				// var_dump($search_idemp);
				// exit();
				
				$sql.=" AND ( CONCAT(ime_nd_kandidata,' ',prezime_nd_kandidata) LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR CONCAT(ime_nd_kandidata,prezime_nd_kandidata) LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR CONCAT(prezime_nd_kandidata,' ',ime_nd_kandidata) LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR CONCAT(prezime_nd_kandidata,ime_nd_kandidata) LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR ime_nd_kandidata LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR prezime_nd_kandidata LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR status_nd_kandidata LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR email_nd_kandidata LIKE '%".$requestData['search']['value']."%' ";
				if($search_idemp != null and $search_idemp != "" ){$sql.=" OR zaduzen_zaposlenik_nd_kandidata = ".$search_idemp." ";}
				$sql.=" OR mobilni_nd_kandidata LIKE '%".$requestData['search']['value']."%' )";
			}
			
			$query=mysqli_query($conn, $sql) or die();
			$totalFiltered = mysqli_num_rows($query);

			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
			$query = mysqli_query($conn, $sql) or die();
			$data = array(); //ovo sam dodao zbog erorr loga
			while( $row = mysqli_fetch_array($query) ) {

				$id = $row['id_broj_nd_kandidata'];
				$ime = $row['ime_nd_kandidata'];
				$prezime = $row['prezime_nd_kandidata'];
				$mobilni = $row['mobilni_nd_kandidata'];
				$email = $row['email_nd_kandidata'];
				$status = $row['status_nd_kandidata'];
				$pstatus = $row['pstatus_nd_kandidata'];
				$vrijeme_kr = date('d.m.Y. H:i:s', strtotime($row['vrijeme_kreiranja_nd_kandidata']));
				$povijest = $row['povijest_nd_kandidata'];
				$povijest_vr = $row['povijest_vrsta_nd_kandidata'];
				$kampanja = $row['kampanja_id'];
				$datum_zad_kom = $row['zadnja_komunikacija'];
				//$ime_agenta = $row['ime_agenta'];
				$dodao = getZaposlenikimeR($row['dodao_zaposlenik_nd_kandidata']);
				$zaduzenx = getZaposlenikimeR($row['zaduzen_zaposlenik_nd_kandidata']);
				$status_ponovna_p = $row['status_pp'];
				//Prilagodba ispisa za status START
				if($uslov_type == 11 OR $uslov_type == 12){
					if($status_ponovna_p == 1 OR $status_ponovna_p == 2){
						$status_pp_ispis = 'P-'.$status_ponovna_p.' ';
					}else{
						$status_pp_ispis = 'P-'.$status_ponovna_p.' ';
					}
				}else{
					$status_pp_ispis = '';
				}
				
				// if($status == 1){
					// if($pstatus == 1){
						// $pstatus_style = 'background-color: #839098; color: white;';
						// $pstatus_ispis = 'Lead';
					// }
					// else if($pstatus == 2){
						// $pstatus_style = 'background-color: #00fb53; color: white;';
						// $pstatus_ispis = 'Neuspješan Kontakt 3';
					// }
					// else if($pstatus == 3){
						// $pstatus_style = 'background-color: #0E6973; color: white;';
						// $pstatus_ispis = 'Zainteresiran Lead';
					// }
					// else if($pstatus == 4){
						// $pstatus_style = 'background-color: #BF214B; color: white;';
						// $pstatus_ispis = 'Nezainteresiran Lead';
					// }
					// else if($pstatus == 5){
						// $pstatus_style = 'background-color: #c79cff; color: white;';
						// $pstatus_ispis = 'U obradi Lead';
					// }
					// else if($pstatus == 6){
						// $pstatus_style = 'background-color: #00fafb; color: white;';
						// $pstatus_ispis = 'Neuspješan Kontakt 1';
					// }
					// $status_ispis = '<span style = "'.$pstatus_style.'" class="label label-default material-label material-label_default main-container__column text-left">'.$status_pp_ispis.''.$pstatus_ispis.'</span>';
				// }
				// else if($status == 2){
					// $status_ispis = '<span  style = "background-color: #f2e42e;" class="label label-default material-label material-label_default main-container__column text-left">'.$status_pp_ispis.'Prikupljanje dokumentacije</span>';
				// }
				// else if($status == 3){ 
					// $status_ispis = '<span class="label label-primary material-label material-label_primary main-container__column text-left">'.$status_pp_ispis.'Poslana pošta</span>';
				// }
				// else if($status == 4){
					// $status_ispis = '<span class="label label-info material-label material-label_info main-container__column text-left">'.$status_pp_ispis.'U obradi</span>';
				// }
				// else if($status == 5){
					// $status_ispis = '<span class="label label-warning material-label material-label_warning main-container__column text-left">'.$status_pp_ispis.'Dopuna dokumentacije</span>';
				// }
				// else if($status == 6){
					// $status_ispis = '<span class="label label-success material-label material-label_success main-container__column text-left">'.$status_pp_ispis.'Završen</span>';
				// }
				// else if($status == 7){
					// $status_ispis = '<span class="label label-danger material-label material-label_danger main-container__column text-left">'.$status_pp_ispis.'Arhiviran</span>';
				// }
				$status_nd_kandidata_ispis1 = getStatusDIPLKandidatR($status, $pstatus);
				//Prilagodba ispisa za status START
				
				//Prilagodba ispisa za povijest START
				if($povijest == 0){
					$povijest_ispis = '<span class="label label-success material-label material-label_success main-container__column text-left" title = "Ručna registracija">'.$dodao.'</span>';
				}
				else if($povijest == 1){
					if($povijest_vr == 1){
						$povijest_vrsta_ispisx = 'SMS';
					}
					else if($povijest_vr == 2){
						$povijest_vrsta_ispisx = 'JobStep Messenger';
					}
					else if($povijest_vr == 3){
						$povijest_vrsta_ispisx = 'CRM';
					}
					else if($povijest_vr == 4){
						$povijest_vrsta_ispisx = 'Viber';
					}
					else if($povijest_vr == 5){
						$povijest_vrsta_ispisx = 'Viber - Stornirani';
					}
					$povijest_ispis = '<span class="label label-primary material-label material-label_primary main-container__column text-left">Kandidati-'.$povijest_vrsta_ispisx.'</span>';
				}
				else if($povijest == 2){
					if($povijest_vr == 1){
						$povijest_vrsta_ispisx = 'APP';
					}
					else if($povijest_vr == 2){
						$povijest_vrsta_ispisx = 'WEB';
					}
					$povijest_ispis = '<span class="label label-warning material-label material-label_warning main-container__column text-left">Sve za vize-'.$povijest_vrsta_ispisx.'</span>';
				}
				else if($povijest == 3){
					$povijest_ispis = '<span class="label label-info material-label material-label_info main-container__column text-left">JobStep Partner APP</span>';
				}
				else if($povijest == 4){
					$povijest_ispis = '<span class="label label-danger material-label material-label_danger main-container__column text-left">JobStep Web</span>';
				}
				else if($povijest == 5){
					$povijest_ispis = '<span class="label label-default material-label material-label_default main-container__column text-left" title = "'.getKampanjePuniNazivDIPLR($kampanja).'">'.getKampanjeSkrNazivDIPLR($kampanja).'</span>';
				}
				
				//Prilagodba ispisa za povijest END
				$getSiteUrl = getSiteURLr();
				
				$nData=array();
				
				$nData[] = '<p class="text-center">'.$id.'</p>';
				$nData[] = '<p class="text-center"><a href="'.$getSiteUrl.'nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$id.'">'.$ime.' '.$prezime.'</a>';
				$nData[] = '<p class="text-center">'.$mobilni.'</p>';
				$nData[] = '<p class="text-center">'.$datum_zad_kom.'</p>';
				$nData[] = '<p class="text-center">'.$status_nd_kandidata_ispis1.'</p>';
				$nData[] = '<p class="text-center">'.$vrijeme_kr.'</p>';
				$nData[] = '<p class="text-center">'.$povijest_ispis.'</p>';
				$nData[] = '<p class="text-center">'.$zaduzenx.'</p>';
				$nData[] = '<div class="btn-group material-btn-group">
								<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_success idk_btn_table" data-toggle="dropdown">
									<i class="fa fa-cogs fa-lg" aria-hidden="true">
									</i> 
									<span class="caret material-btn__caret">
									</span>
								</button>
								<ul style = "top:32px; left: -40px; min-width: 150px !important;" class="dropdown-menu material-dropdown-menu material-dropdown-menu_success idk_dropdown_table" role="menu">
									<li>
										<a href="'.$getSiteUrl.'nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$id.'" class="material-dropdown-menu__link">
											<i class="fa fa-folder-open-o" aria-hidden="true">
											</i> 
											Otvori
										</a>
									</li>
								</ul>
							</div>';
				
				$data[] = $nData;
			}
			
			$json_data = array(
						//"draw"            => intval( $requestData['draw'] ),
						"recordsTotal"    => intval( $totalData ),
						"recordsFiltered" => intval( $totalFiltered ),
						"data"            => $data
						);

			echo json_encode($json_data);
			
		break;
		
		case "lista_diplnp":
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
			$type = intval($_REQUEST['type_ssd']);
			$team = intval($_REQUEST['team_ssd']);
			$employess_id_team = implode(", ", getIdOfEmployeeTeam($team));
			$status = explode(',',$_REQUEST['status_ssd']);
			$vrsta_priznanja_exp = explode(',',$_REQUEST['vrsta_priznanja_ssd']);
			$vrsta_priznanja_imp = implode(", ", $vrsta_priznanja_exp);
			$f_u1x = implode(',', $status);

			// var_dump($_REQUEST['status_ssd']);
			// var_dump($status);
			// var_dump($f_u1x);
			$povijest = explode( ',', $_REQUEST['povijest_ssd']);
			$kampanja = explode( ',', $_REQUEST['kampanja_ssd']);
			$datumod = $_REQUEST['datumod_ssd'];
			$datumdo = $_REQUEST['datumdo_ssd'];
			$struke = explode( ',', $_REQUEST['struka_ssd']);
			$skola = explode( ',', $_REQUEST['skola_ssd']);
			$skola_smijer = explode( ',', $_REQUEST['skola_smijer_ssd']);
			$zaposlenik = explode( ',', $_REQUEST['zaposlenik_ssd']);
			$vrstaugovora = explode( ',', $_REQUEST['vrstaugovora_ssd']);
			$nivojezika = explode( ',', $_REQUEST['nivojezika_ssd']);
			$statusPrijave = explode(',',$_REQUEST['statusPrijave_ssd']);
			$drzava = explode( ',', $_REQUEST['drzava_ssd']);
			$selected_teams = $_REQUEST['selected_team_ssd'];
			$razlozi_odbijanja = (($_REQUEST['razlozi_odbijanja_ssd'] != "") ? explode( ',', $_REQUEST['razlozi_odbijanja_ssd']) : null);
			
			if($zaposlenik[0] == 139){
				$uslov_skladiste = "AND tim_nd_kandidata IN (".$selected_teams.")";
			}
			else {
				$uslov_skladiste = "";
			}
			// echo " tu".$uslov_skladiste."tu ";
			$columns = array(
				0 => 'id_broj_nd_kandidata',
				1 => 'ime_nd_kandidata',
				2 => 'mobilni_nd_kandidata',
				3 => 'zadnja_komunikacija',
				4 => 'status_nd_kandidata',
				5 => 'vrijeme_kreiranja_nd_kandidata',
				6 => 'povijest_nd_kandidata',
				7 => 'zaduzen_zaposlenik_nd_kandidata'
			);
			
			$supervizorStatus = explode( ',' , getEmployeeSupervizor());
			
			$flagException = 0;
			if($team == 4 AND (in_array("2", $supervizorStatus) OR in_array("3", $supervizorStatus))){
				$flagException = 1;
			}
			
			if($team == 1 OR $flagException == 1){
				$uslov_zaposleni_ispis = " zaduzen_zaposlenik_nd_kandidata is not null";
			}else{
				$uslov_zaposleni_ispis = " zaduzen_zaposlenik_nd_kandidata IN (".$employess_id_team.")";
			}
			
			if($type == 1){
				//if($columns[$requestData['order'][0]['column']] == 3) // AKO JE U PITANJU SORTIRANJE PO DATUMU ZADNJE KOMUNIKACIJE
				$sql = "SELECT id_broj_nd_kandidata, ime_nd_kandidata, prezime_nd_kandidata, mobilni_nd_kandidata, email_nd_kandidata, vrijeme_kreiranja_nd_kandidata, dodao_zaposlenik_nd_kandidata, zaduzen_zaposlenik_nd_kandidata, status_nd_kandidata, pstatus_nd_kandidata, povijest_nd_kandidata, povijest_vrsta_nd_kandidata, kampanja_id, zadnja_komunikacija FROM idk_nd_kandidata WHERE vrijeme_kreiranja_nd_kandidata is not null AND ".$uslov_zaposleni_ispis."";
			}
			else if($type == 2){
				
				//Ako je označen status prijave u filteru, dodaje se left join na kandidate
				if($_REQUEST['statusPrijave_ssd'] != "" AND $vrsta_priznanja_imp == "" AND $vrsta_priznanja_imp != 4){
					$glavni_query = "SELECT id_broj_nd_kandidata, ime_nd_kandidata, prezime_nd_kandidata, mobilni_nd_kandidata, email_nd_kandidata, vrijeme_kreiranja_nd_kandidata, dodao_zaposlenik_nd_kandidata, zaduzen_zaposlenik_nd_kandidata, status_nd_kandidata, pstatus_nd_kandidata, povijest_nd_kandidata, povijest_vrsta_nd_kandidata, kampanja_id, zadnja_komunikacija FROM idk_nd_kandidata LEFT JOIN idk_kandidati ON id_broj_nd_kandidata = kandidat_dipl_id  WHERE id_broj_nd_kandidata > 0 AND ".$uslov_zaposleni_ispis." ";
				} else if ($vrsta_priznanja_imp != "" AND $_REQUEST['statusPrijave_ssd'] == "" AND $vrsta_priznanja_imp != 4) {
					$glavni_query = "SELECT id_broj_nd_kandidata, ime_nd_kandidata, prezime_nd_kandidata, mobilni_nd_kandidata, email_nd_kandidata, vrijeme_kreiranja_nd_kandidata, dodao_zaposlenik_nd_kandidata, zaduzen_zaposlenik_nd_kandidata, status_nd_kandidata, pstatus_nd_kandidata, povijest_nd_kandidata, povijest_vrsta_nd_kandidata, kampanja_id, zadnja_komunikacija FROM idk_nd_kandidata INNER JOIN idk_nostrifikovane_diplome ON id_broj_nd_kandidata = id_cand_dipl WHERE id_broj_nd_kandidata > 0 AND ".$uslov_zaposleni_ispis." ";
				} else if ($vrsta_priznanja_imp != "" AND $_REQUEST['statusPrijave_ssd'] != "" AND $vrsta_priznanja_imp != 4) {
					$glavni_query = "SELECT id_broj_nd_kandidata, ime_nd_kandidata, prezime_nd_kandidata, mobilni_nd_kandidata, email_nd_kandidata, vrijeme_kreiranja_nd_kandidata, dodao_zaposlenik_nd_kandidata, zaduzen_zaposlenik_nd_kandidata, status_nd_kandidata, pstatus_nd_kandidata, povijest_nd_kandidata, povijest_vrsta_nd_kandidata, kampanja_id, zadnja_komunikacija FROM idk_nd_kandidata LEFT JOIN idk_kandidati ON id_broj_nd_kandidata = kandidat_dipl_id INNER JOIN idk_nostrifikovane_diplome ON id_broj_nd_kandidata = id_cand_dipl WHERE id_broj_nd_kandidata > 0 AND ".$uslov_zaposleni_ispis." ";
				} else{
					$glavni_query = "SELECT id_broj_nd_kandidata, ime_nd_kandidata, prezime_nd_kandidata, mobilni_nd_kandidata, email_nd_kandidata, vrijeme_kreiranja_nd_kandidata, dodao_zaposlenik_nd_kandidata, zaduzen_zaposlenik_nd_kandidata, status_nd_kandidata, pstatus_nd_kandidata, povijest_nd_kandidata, povijest_vrsta_nd_kandidata, kampanja_id, zadnja_komunikacija FROM idk_nd_kandidata WHERE id_broj_nd_kandidata > 0 AND ".$uslov_zaposleni_ispis." ";
				}

				//Subquery za radno iskustvo
				$razlozi_odbijanja_sq = "";
				if ($razlozi_odbijanja != null) {
					$razlozi_odbijanja_imp = implode(",", $razlozi_odbijanja);
					$razlozi_odbijanja_sq = "
						AND id_broj_nd_kandidata IN (
							SELECT 
								kb.id_kandidata_biljeska_nd
							FROM 
								idk_nd_kandidata_biljeske kb 
							JOIN 
								idk_nd_kandidata kan 
							ON 
								kb.id_kandidata_biljeska_nd = kan.id_broj_nd_kandidata
								AND 
								(
									(
										kan.status_nd_kandidata = 1
										AND 
										kan.pstatus_nd_kandidata = 4
									) 
									OR 
									(
										kan.status_nd_kandidata = 7
									)
								)
							JOIN 
								(
									SELECT 
										kb1.id_kandidata_biljeska_nd AS biljeskaKandidatId, 
										MAX(kb1.id_biljeska_nd) AS biljeskaId
									FROM 
										idk_nd_kandidata_biljeske kb1 
									WHERE  
										kb1.status_biljeska_nd = 2 
										AND 
										kb1.tip_biljeska_nd IN (3,15,13) 
									GROUP BY
										kb1.id_kandidata_biljeska_nd
								) AS kbmax
							ON 
								kb.id_biljeska_nd = kbmax.biljeskaId
							WHERE 
								kb.razlog_biljeska_nd IN (
									".$razlozi_odbijanja_imp."
								)
						)
					";
				}

				$glavni_query = $glavni_query." ".$razlozi_odbijanja_sq;
				
				if($_REQUEST['status_ssd'] != ""){
					$f_u1 = $status;
					$f_u1x = implode(',', $f_u1);
					if(in_array("11", $f_u1)){
						$ps11 = "1";
					}
					else{
						$ps11 = "0";
					}
					if(in_array("12", $f_u1)){
						$ps12 = "2";
					}
					else{
						$ps12 = "0";
					}
					if(in_array("13", $f_u1)){
						$ps13 = "3"; 
					}
					else{
						$ps13 = "0";
					}
					if(in_array("14", $f_u1)){
						$ps14 = "4"; 
					}
					else{
						$ps14 = "0";
					}
					if(in_array("15", $f_u1)){
						$ps15 = "5"; 
					}
					else{
						$ps15 = "0";
					}
					if(in_array("16", $f_u1)){
						$ps16 = "6"; 
					}
					else{
						$ps16 = "0";
					}
					//111Adis222 9 START
					if(in_array("17", $f_u1)){
						$ps17 = "7"; 
					}
					else{
						$ps17 = "0";
					}
					if(in_array("18", $f_u1)){
						$ps18 = "8"; 
					}
					else{
						$ps18 = "0";
					}
					if(in_array("19", $f_u1)){
						$ps19 = "9"; 
					}
					else{
						$ps19 = "0";
					}
					if(in_array("20", $f_u1)){
						$ps20 = "10"; 
					}
					else{
						$ps20 = "0";
					}
					if(in_array("21", $f_u1)){
						$ps21 = "11"; 
					}
					else{
						$ps21 = "0";
					}
					if(in_array("22", $f_u1)){
						$ps22 = "12"; 
					}
					else{
						$ps22 = "0";
					}
					if(in_array("221", $f_u1)){
						$ps221 = "1"; 
					}
					else{
						$ps221 = "0";
					}
					if(in_array("222", $f_u1)){
						$ps222 = "2"; 
					}
					else{
						$ps222 = "0";
					}
					if(in_array("223", $f_u1)){
						$ps223 = "3"; 
					}
					else{
						$ps223 = "0";
					}
					if(in_array("224", $f_u1)){
						$ps224 = "4"; 
					}
					else{
						$ps224 = "0";
					}
					if(in_array("225", $f_u1)){
						$ps225 = "5"; 
					}
					else{
						$ps225 = "0";
					}
					if(in_array("226", $f_u1)){
						$ps226 = "6"; 
					}
					else{
						$ps226 = "0";
					}
					if(in_array("227", $f_u1)){
						$ps227 = "7"; 
					}
					else{
						$ps227 = "0";
					}
					if(in_array("31", $f_u1)){
						$ps31 = "1"; 
					}
					else{
						$ps31 = "0";
					}
					if(in_array("32", $f_u1)){
						$ps32 = "2"; 
					}
					else{
						$ps32 = "0";
					}
					if(in_array("41", $f_u1)){
						$ps41 = "1"; 
					}
					else{
						$ps41 = "0";
					}
					if(in_array("42", $f_u1)){
						$ps42 = "2"; 
					}
					else{
						$ps42 = "0";
					}
					//111Adis222 9 END
					
					$uslov_1 = "AND 
									(
										status_nd_kandidata IN (".$f_u1x.") 
										OR (
											status_nd_kandidata = 1 
											AND pstatus_nd_kandidata IN(".$ps11.",".$ps12.",".$ps13.",".$ps14.",".$ps15.",".$ps16.",".$ps17.",".$ps18.",".$ps19.",".$ps20.",".$ps21.",".$ps22.")
										)
										OR (
											status_nd_kandidata = 2
											AND pstatus_nd_kandidata IN(".$ps221.",".$ps222.",".$ps223.",".$ps224.",".$ps225.",".$ps226.",".$ps227.")
										)
										OR (
											status_nd_kandidata = 3
											AND pstatus_nd_kandidata IN(".$ps31.",".$ps32.")
										)
										OR (
											status_nd_kandidata = 4
											AND pstatus_nd_kandidata IN(".$ps41.",".$ps42.")
										)
									)
									";
				}else{
					$uslov_1  = "";
				}

				$uslov_vrsta_priznanja = "";
				if ($vrsta_priznanja_imp != "" AND $vrsta_priznanja_imp != 4){
					$uslov_vrsta_priznanja = "
						AND 
							(
								".( (in_array(2, $vrsta_priznanja_exp)) ? ' full_recognition = 2 OR ' : ' full_recognition = 100 OR ')."
								".( (in_array(1, $vrsta_priznanja_exp)) ? ' full_recognition = 1 OR ' : ' full_recognition = 100 OR ')." 
								".( (in_array(0, $vrsta_priznanja_exp)) ? ' full_recognition = 0 OR ' : ' full_recognition = 100 OR ')."
								".( (in_array(3, $vrsta_priznanja_exp)) ? ' full_recognition is null ' : ' full_recognition = 100 ')."
							)
					";
				}

				if ($vrsta_priznanja_imp == 4) {
					$uslov_vrsta_priznanja = "
						AND 
							id_broj_nd_kandidata NOT IN (
								SELECT 
									id_cand_dipl
								FROM 
									idk_nostrifikovane_diplome
								INNER JOIN 
									idk_nd_kandidata
								ON 
									id_cand_dipl = id_broj_nd_kandidata
								WHERE 
									id_cand_dipl is not null
									AND 
									status_nd_kandidata = 6
							)
						AND 
							status_nd_kandidata = 6
					";
				}

				if($_REQUEST['povijest_ssd'] != ""){
					$f_u2 = $povijest;
					$f_u2x = implode(',', $f_u2);
					if(in_array("0", $f_u2)){
						$pu1 = "0";
						$puv1 = " is null";
					}
					else{
						$pu1 = "10";
						$puv1 = " = 10";
					}
					if(in_array("1", $f_u2)){
						$pu2 = "1";
						$puv2 = "1";
					}
					else{
						$pu2 = "10";
						$puv2 = "10";
					}
					if(in_array("2", $f_u2)){
						$pu3 = "1";
						$puv3 = "2";
					}
					else{
						$pu3 = "10";
						$puv3 = "10";
					}
					if(in_array("3", $f_u2)){
						$pu4 = "1";
						$puv4 = "3";
					}
					else{
						$pu4 = "10";
						$puv4 = "10";
					}
					if(in_array("4", $f_u2)){
						$pu5 = "1";
						$puv5 = "4";
					}
					else{
						$pu5 = "10";
						$puv5 = "10";
					}
					if(in_array("5", $f_u2)){
						$pu6 = "2";
						$puv6 = "1";
					}
					else{
						$pu6 = "10";
						$puv6 = "10";
					}
					if(in_array("6", $f_u2)){
						$pu7 = "2";
						$puv7 = "2";
					}
					else{
						$pu7 = "10";
						$puv7 = "10";
					}
					if(in_array("7", $f_u2)){
						$pu8 = "3";
						$puv8 = "1";
					}
					else{
						$pu8 = "10";
						$puv8 = "10";
					}
					if(in_array("8", $f_u2)){
						$pu9 = "4";
						$puv9 = "1";
					}
					else{
						$pu9 = "10";
						$puv9 = "10";
					}
					if(in_array("9", $f_u2)){
						$pu10 = "1";
						$puv10 = "5";
					}
					else{
						$pu10 = "10";
						$puv10 = "10";
					}
					if(in_array("10", $f_u2)){
						$pu11 = "5";
						$puv11 = "1";
						if($_REQUEST['kampanja_ssd'] != ""){
							$f_u8 = $kampanja;
							$f_u8x = implode(',', $f_u8);
							$uslov_8 = "AND kampanja_id IN (".$f_u8x.") ";
						}
						else{
							$uslov_8 = "";
						}
					}
					else{
						$pu11 = "10";
						$puv11 = "10";
						$uslov_8 = "";
					}
					$uslov_2 = "AND (
										(povijest_nd_kandidata = ".$pu1." AND povijest_vrsta_nd_kandidata".$puv1.") OR
										(povijest_nd_kandidata = ".$pu2." AND povijest_vrsta_nd_kandidata = ".$puv2.") OR
										(povijest_nd_kandidata = ".$pu3." AND povijest_vrsta_nd_kandidata = ".$puv3.") OR
										(povijest_nd_kandidata = ".$pu4." AND povijest_vrsta_nd_kandidata = ".$puv4.") OR
										(povijest_nd_kandidata = ".$pu5." AND povijest_vrsta_nd_kandidata = ".$puv5.") OR
										(povijest_nd_kandidata = ".$pu6." AND povijest_vrsta_nd_kandidata = ".$puv6.") OR
										(povijest_nd_kandidata = ".$pu7." AND povijest_vrsta_nd_kandidata = ".$puv7.") OR
										(povijest_nd_kandidata = ".$pu8." AND povijest_vrsta_nd_kandidata = ".$puv8.") OR
										(povijest_nd_kandidata = ".$pu9." AND povijest_vrsta_nd_kandidata = ".$puv9.") OR 
										(povijest_nd_kandidata = ".$pu10." AND povijest_vrsta_nd_kandidata = ".$puv10.") OR 
										(povijest_nd_kandidata = ".$pu11." AND povijest_vrsta_nd_kandidata = ".$puv11." ".$uslov_8.")
									)";
				}else{
					$uslov_2  = "";
				}
				if(($_REQUEST['datumod_ssd'] != "") AND ($_REQUEST['datumdo_ssd'] != "")){
					$datum_ulaskaod_filx = $datumod." 00:00:00";
					$datum_ulaskado_filx = $datumdo." 23:59:59";
					$datum_ulaska_od_filx = date("Y-m-d H:i:s", strtotime($datum_ulaskaod_filx));
					$datum_ulaska_do_filx = date("Y-m-d H:i:s", strtotime($datum_ulaskado_filx));
					$uslov_3 = "AND vrijeme_kreiranja_nd_kandidata BETWEEN '".$datum_ulaska_od_filx."' AND '".$datum_ulaska_do_filx."'  ";
				}else{
					$uslov_3 = "";
				}
				if($_REQUEST['struka_ssd'] != ""){
					$f_struke = implode(',', $struke);
					$uslov_struke = "AND skola_smjer_nd_kandidata IN (SELECT ss_id FROM idk_skole_smjerovi WHERE ss_struka_id IN (".$f_struke.")) ";
				}
				else{
					$uslov_struke = "";
				}
				if($_REQUEST['skola_ssd'] != ""){
					$f_u4 = $skola;
					$f_u4x = implode(',', $f_u4);
					$uslov_4 = "AND skola_nd_kandidata IN (".$f_u4x.") ";
				}
				else{
					$uslov_4 = "";
				}
				if($_REQUEST['skola_smijer_ssd'] != ""){
					$f_u5 = $skola_smijer;
					$f_u5x = implode(',', $f_u5);
					$uslov_5 = "AND skola_smjer_nd_kandidata IN (".$f_u5x.") ";
				}
				else{
					$uslov_5 = "";
				}
				if($_REQUEST['zaposlenik_ssd'] != ""){
					$f_u6 = $zaposlenik;
					$f_u6x = implode(',', $f_u6);
					$uslov_6 = "AND zaduzen_zaposlenik_nd_kandidata IN (".$f_u6x.") ";
				}
				else{
					$uslov_6 = "";
				}
				if($_REQUEST['vrstaugovora_ssd'] != ""){
					$f_u7 = $vrstaugovora;
					$f_u7x = implode(',', $f_u7);
					$uslov_7 = "AND vrsta_ugovora_nd_kandidata IN (".$f_u7x.") ";
				}
				else{
					$uslov_7 = "";
				}
				if($_REQUEST['drzava_ssd'] != ""){
					$f_u8 = $drzava;
					$f_u8x = implode(',', $f_u8);
					$pu_drzava = "";
					
					$pu_bih = "";
					$pu_srb = "";
					$pu_de = "";
					$pu_ost = "";
					
					if(in_array("387", $f_u8)){
						$pu_bih = "(mobilni_nd_kandidata LIKE '+387%')";
					}else{
						$pu_bih = "(mobilni_nd_kandidata = '0000')";
					} 
					if(in_array("381", $f_u8)){
						$pu_srb = "(mobilni_nd_kandidata LIKE '+381%')";
					}else{
						$pu_srb = "(mobilni_nd_kandidata = '0000')";
					} 
					if(in_array("49", $f_u8)){
						$pu_de = "(mobilni_nd_kandidata LIKE '+49%')";
					}else{
						$pu_de = "(mobilni_nd_kandidata = '0000')";
					} 
					if(in_array("ostalo", $f_u8)){
						$pu_ost = "((mobilni_nd_kandidata NOT LIKE '+387%' AND mobilni_nd_kandidata NOT LIKE '+381%' AND mobilni_nd_kandidata NOT LIKE '+49%') OR mobilni_nd_kandidata IS NULL )";
					}else{
						$pu_ost = "mobilni_nd_kandidata = '0000'";
					}
					$uslov_8 = "AND (
						".$pu_bih." OR 
						".$pu_srb." OR 
						".$pu_de." OR 
						".$pu_ost."
					)";
				}
				else{
					$uslov_8 = "";
				}
				
				if($_REQUEST['nivojezika_ssd'] != ""){
					$f_u9 = $nivojezika;
					$f_u9x = implode(',', $f_u9);
					$uslov_9 = "AND nivo_poznavanja_jezika IN (".$f_u9x.") ";
				}
				else{
					$uslov_9 = "";
				}
				if($_REQUEST['statusPrijave_ssd'] != ""){
					$f_u10 = $statusPrijave;
					$f_u10x = implode(',', $f_u10);
					$uslov_10 = "AND kandidat_status_prijave IN (".$f_u10x.") ";
				}
				else{
					$uslov_10 = "";
				}
				$puni_query = $glavni_query." ".$uslov_1." ".$uslov_2." ".$uslov_3." ".$uslov_struke." ".$uslov_4." ".$uslov_5." ".$uslov_6." ".$uslov_7." ".$uslov_8." ".$uslov_skladiste." ".$uslov_9." ".$uslov_10." ".$uslov_vrsta_priznanja;
				// echo " ".$puni_query." ";
				$sql = $puni_query;
			}

			// if($query) // will return true if succefull else it will return false
				// {
				// echo "radi";
				// }else{
					// echo "nista";
				// }
			
			$query = mysqli_query($conn, $sql) or die();
			$totalData = mysqli_num_rows($query);
			$totalFiltered = $totalData;
			
			if( !empty($requestData['search']['value']) ) {
	
				$sql.=" AND( CONCAT(ime_nd_kandidata,' ',prezime_nd_kandidata) LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR CONCAT(ime_nd_kandidata,prezime_nd_kandidata) LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR CONCAT(prezime_nd_kandidata,' ',ime_nd_kandidata) LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR CONCAT(prezime_nd_kandidata,ime_nd_kandidata) LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR ime_nd_kandidata LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR prezime_nd_kandidata LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR status_nd_kandidata LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR email_nd_kandidata LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR mobilni_nd_kandidata LIKE '%".$requestData['search']['value']."%' )";
			}
			
			$query=mysqli_query($conn, $sql) or die();
			$totalFiltered = mysqli_num_rows($query);
			
			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
			$query=mysqli_query($conn, $sql) or die();
			$data = array(); //ovo sam dodao zbog erorr loga
			while( $ispis_ND_kandidata_row = mysqli_fetch_array($query) ) {

				$id_nd_kandidata_ispis = $ispis_ND_kandidata_row['id_broj_nd_kandidata'];
				$ime_nd_kandidata_ispis = $ispis_ND_kandidata_row['ime_nd_kandidata'];
				$prezime_nd_kandidata_ispis = $ispis_ND_kandidata_row['prezime_nd_kandidata'];
				$mobilni_nd_kandidata_ispis = $ispis_ND_kandidata_row['mobilni_nd_kandidata'];
				$email_nd_kandidata_ispis = $ispis_ND_kandidata_row['email_nd_kandidata'];
				$vrijeme_kreiranja_nd_kandidata_ispis = date('d.m.Y H:i', strtotime($ispis_ND_kandidata_row['vrijeme_kreiranja_nd_kandidata']));
				$dodao_zaposlenik_nd_kandidata_ispis = getZaposlenikimeR($ispis_ND_kandidata_row['dodao_zaposlenik_nd_kandidata']);
				$zaduzen_zaposlenik_nd_kandidata_ispis = getZaposlenikimeR($ispis_ND_kandidata_row['zaduzen_zaposlenik_nd_kandidata']);
				$status_nd_kandidata_ispis = $ispis_ND_kandidata_row['status_nd_kandidata'];
				$pstatus_nd_kandidata_ispis = $ispis_ND_kandidata_row['pstatus_nd_kandidata'];
				$povijest_nd_kandidata_ispis = $ispis_ND_kandidata_row['povijest_nd_kandidata'];
				$povijest_vrsta_nd_kandidata_ispis = $ispis_ND_kandidata_row['povijest_vrsta_nd_kandidata'];
				$kampanja_id_nd_kandidata_ispis = $ispis_ND_kandidata_row['kampanja_id'];
				$datum_zad_kom = $ispis_ND_kandidata_row['zadnja_komunikacija'];
				//$datum_zad_kom = getSamoDatumZadnjaKom($id_nd_kandidata_ispis);
				
				if($povijest_nd_kandidata_ispis == 0){
					$kreirao_nd_kandidata = '<span class="label label-success material-label material-label_success main-container__column text-left" title = "Ručna registracija">'.$dodao_zaposlenik_nd_kandidata_ispis.'</span>';
				}
				else if($povijest_nd_kandidata_ispis == 1){
					if($povijest_vrsta_nd_kandidata_ispis == 1){
						$povijest_vrsta_ispisx = 'SMS';
					}
					else if($povijest_vrsta_nd_kandidata_ispis == 2){
						$povijest_vrsta_ispisx = 'JobStep Messenger';
					}
					else if($povijest_vrsta_nd_kandidata_ispis == 3){
						$povijest_vrsta_ispisx = 'CRM';
					}
					else if($povijest_vrsta_nd_kandidata_ispis == 4){
						$povijest_vrsta_ispisx = 'Viber';
					}
					else if($povijest_vrsta_nd_kandidata_ispis == 5){
						$povijest_vrsta_ispisx = 'Viber Stornirani';
					}
					$kreirao_nd_kandidata = '<span class="label label-primary material-label material-label_primary main-container__column text-left">Kandidati-'.$povijest_vrsta_ispisx.'</span>';
				}else if($povijest_nd_kandidata_ispis == 2){
					if($povijest_vrsta_nd_kandidata_ispis == 1){
						$povijest_vrsta_ispisx = 'APP';
					}
					else if($povijest_vrsta_nd_kandidata_ispis == 2){
						$povijest_vrsta_ispisx = 'WEB';
					}
					$kreirao_nd_kandidata = '<span class="label label-warning material-label material-label_warning main-container__column text-left">Sve za vize-'.$povijest_vrsta_ispisx.'</span>';
				}
				else if($povijest_nd_kandidata_ispis == 3){
					$kreirao_nd_kandidata = '<span class="label label-info material-label material-label_info main-container__column text-left">JobStep Partner APP</span>';
				}
				else if($povijest_nd_kandidata_ispis == 4){
					$kreirao_nd_kandidata = '<span class="label label-danger material-label material-label_danger main-container__column text-left">JobStep Web</span>';
				}
				else if($povijest_nd_kandidata_ispis == 5){
					/*$query_kamp = $db->prepare("SELECT kd_naziv, kd_skraceni_naziv FROM idk_kampanje_dipl WHERE kd_id = :kd_id");
					$query_kamp->execute(array(':kd_id' => $kampanja_id_nd_kandidata_ispis));
					$row_kamp = $query_kamp->fetch();
					$puni_naziv_kamp = $row_kamp["kd_naziv"];
					$sk_naziv_kamp = $row_kamp["kd_skraceni_naziv"];*/
					$kreirao_nd_kandidata = '<span class="label label-default material-label material-label_default main-container__column text-left" title = "'.getKampanjePuniNazivDIPLR($kampanja_id_nd_kandidata_ispis).'">'.getKampanjeSkrNazivDIPLR($kampanja_id_nd_kandidata_ispis).'</span>';
				}
				
				// if($status_nd_kandidata_ispis == 1){
					// if($pstatus_nd_kandidata_ispis == 1){
						// $pstatus_nd_kandidata_style1 = 'background-color: #839098; color: white;';
						// $pstatus_nd_kandidata_ispis1 = 'Lead';
					// }
					// else if($pstatus_nd_kandidata_ispis == 2){
						// $pstatus_nd_kandidata_style1 = 'background-color: #00fb53; color: white;';
						// $pstatus_nd_kandidata_ispis1 = 'Neuspješan Kontakt 3';
					// }
					// else if($pstatus_nd_kandidata_ispis == 3){
						// $pstatus_nd_kandidata_style1 = 'background-color: #0E6973; color: white;';
						// $pstatus_nd_kandidata_ispis1 = 'Zainteresiran Lead';
					// }
					// else if($pstatus_nd_kandidata_ispis == 4){
						// $pstatus_nd_kandidata_style1 = 'background-color: #BF214B; color: white;';
						// $pstatus_nd_kandidata_ispis1 = 'Nezainteresiran Lead';
					// }
					// else if($pstatus_nd_kandidata_ispis == 5){
						// $pstatus_nd_kandidata_style1 = 'background-color: #c79cff; color: white;';
						// $pstatus_nd_kandidata_ispis1 = 'U obradi Lead';
					// }
					// else if($pstatus_nd_kandidata_ispis == 6){
						// $pstatus_nd_kandidata_style1 = 'background-color: #00fbfa; color: white;';
						// $pstatus_nd_kandidata_ispis1 = 'Neuspješan Kontakt 1';
					// }
					// $status_nd_kandidata_ispis1 = '<span style = "'.$pstatus_nd_kandidata_style1.'" class="label label-default material-label material-label_default main-container__column text-left">'.$pstatus_nd_kandidata_ispis1.'</span>';
				// }
				// else if($status_nd_kandidata_ispis == 2){
					// $status_nd_kandidata_ispis1 = '<span  style = "background-color: #f2e42e;" class="label label-default material-label material-label_default main-container__column text-left">Prikupljanje dokumentacije</span>';
				// }
				// else if($status_nd_kandidata_ispis == 3){ 
					// $status_nd_kandidata_ispis1 = '<span class="label label-primary material-label material-label_primary main-container__column text-left">Poslana pošta</span>';
				// }
				// else if($status_nd_kandidata_ispis == 4){
					// $status_nd_kandidata_ispis1 = '<span class="label label-info material-label material-label_info main-container__column text-left">U obradi</span>';
				// }
				// else if($status_nd_kandidata_ispis == 5){
					// $status_nd_kandidata_ispis1 = '<span class="label label-warning material-label material-label_warning main-container__column text-left">Dopuna dokumentacije</span>';
				// }
				// else if($status_nd_kandidata_ispis == 6){
					// $status_nd_kandidata_ispis1 = '<span class="label label-success material-label material-label_success main-container__column text-left">Završen</span>';
				// }
				// else if($status_nd_kandidata_ispis == 7){
					// $status_nd_kandidata_ispis1 = '<span class="label label-danger material-label material-label_danger main-container__column text-left">Arhiviran</span>';
				// }
				$status_nd_kandidata_ispis1 = getStatusDIPLKandidatR($status_nd_kandidata_ispis, $pstatus_nd_kandidata_ispis);
				//Prilagodba ispisa za povijest END
				$getSiteUrl = getSiteURLr();
				
				$nData=array();
				
				$nData[] = '<p class="text-center">'.$id_nd_kandidata_ispis.'</p>';
				$nData[] = '<p class="text-center"><a href="'.$getSiteUrl.'nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$id_nd_kandidata_ispis.'">'.$ime_nd_kandidata_ispis.' '.$prezime_nd_kandidata_ispis.'</a>';
				$nData[] = '<p class="text-center">'.$mobilni_nd_kandidata_ispis.'</p>';
				$nData[] = '<p class="text-center">'.$datum_zad_kom.'</p>';
				$nData[] = '<p class="text-center">'.$status_nd_kandidata_ispis1.'</p>';
				$nData[] = '<p class="text-center">'.$vrijeme_kreiranja_nd_kandidata_ispis.'</p>';
				$nData[] = '<p class="text-center">'.$kreirao_nd_kandidata.'</p>';
				$nData[] = '<p class="text-center">'.$zaduzen_zaposlenik_nd_kandidata_ispis.'</p>';
				$nData[] = '<div class="text-center"><div class="btn-group material-btn-group">
								<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_success idk_btn_table" data-toggle="dropdown">
									<i class="fa fa-cogs fa-lg" aria-hidden="true">
									</i> 
									<span class="caret material-btn__caret">
									</span>
								</button>
								<ul style = "top:32px; left: -40px; min-width: 150px !important;" class="dropdown-menu material-dropdown-menu material-dropdown-menu_success idk_dropdown_table" role="menu">
									<li>
										<a href="'.$getSiteUrl.'nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$id_nd_kandidata_ispis.'" class="material-dropdown-menu__link">
											<i class="fa fa-folder-open-o" aria-hidden="true">
											</i> 
											Otvori
										</a>
									</li>
								</ul>
							</div></div>';
				
				$data[] = $nData;
			}
			
			$json_data = array(
						 "draw"            => intval( $requestData['draw'] ),
						"recordsTotal"    => intval( $totalData ),
						"recordsFiltered" => intval( $totalFiltered ),
						"data"            => $data
						);
			
			echo json_encode($json_data);
		break;
		
		case "lista_ugovora":
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
			$datum_ugovor = $_REQUEST['datum_ugovor'];
			if($_REQUEST['razlog_ugovor'] != ""){
				$razlog_ugovor = implode(',', $_REQUEST['razlog_ugovor']);
			}else{
				$razlog_ugovor = "";
			}
			if($_REQUEST['status_ugovor'] != NULL){
				$status_ugovor = implode(',', $_REQUEST['status_ugovor']);
			}else{
				$status_ugovor = implode(',',array(0,1,2,4));
			}
			
			$columns = array(
				0 => 'ug_id',
				1 => 'ime_nd_kandidata',
				2 => 'ug_status',
				3 => 'ug_datum_slanja',
				4 => 'ug_file',
				5 => 'ug_file_de',
				6 => 'ug_razlog_odbijanja',
				7 => 'ug_komentar'
			);
			
			if(strpos($datum_ugovor, 'to') !== false) {
				$split = explode(" to ",$datum_ugovor);
				$datum_od = date("Y-m-d H:i:s",strtotime($split[0]." 00:00:00"));
				$datum_do = date("Y-m-d H:i:s",strtotime($split[1]." 23:59:59"));
			}else{
				$datum_od = "2021-05-01 00:00:00";
				$datum_do = date("Y-m-d H:i:s");
			}
			
			$team_id = getTeamIdByEmployee($logged_employee_id);
			
			if($team_id == 1){
				$uslov_team = "";
			}
			else {
				$uslov_team = " AND kan.tim_nd_kandidata = $team_id ";
			}
			
			
			if(in_array("3",$_REQUEST['status_ugovor'])){
				$glavni_query = "
					SELECT u.ug_id, u.ug_kandidat_id, kan.ime_nd_kandidata, kan.prezime_nd_kandidata, u.ug_status, u.ug_datum_slanja, u.ug_file, u.ug_file_de, raz.naziv_ro_bs AS kolona7, u.ug_komentar
					FROM idk_nd_ugovori u
					INNER JOIN idk_nd_kandidata kan
					ON u.ug_kandidat_id = kan.id_broj_nd_kandidata
					LEFT JOIN idk_ro_usluge raz
					ON u.ug_razlog_odbijanja = raz.id_ro
					WHERE u.ug_status = 3 
				";
				$uslov_datum = "u.ug_datum_slanja BETWEEN '".$datum_od."' AND '".$datum_do."'";
				if($razlog_ugovor != ""){
					$uslov_razlog = "(u.ug_razlog_odbijanja IN (".$razlog_ugovor."))";
				}else{
					$uslov_razlog = "(u.ug_razlog_odbijanja is not null OR u.ug_razlog_odbijanja is null)";
				}
			}else{
				$glavni_query = "
					SELECT u.ug_id, u.ug_kandidat_id, kan.ime_nd_kandidata, kan.prezime_nd_kandidata, u.ug_status, u.ug_datum_slanja, u.ug_file, u.ug_file_de, u.ug_razlog_odbijanja AS kolona7, u.ug_komentar
					FROM idk_nd_ugovori u
					INNER JOIN idk_nd_kandidata kan
					ON u.ug_kandidat_id = kan.id_broj_nd_kandidata
					WHERE u.ug_status IN (".$status_ugovor.")  
				";
				$uslov_datum = "(u.ug_datum_slanja BETWEEN '".$datum_od."' AND '".$datum_do."')";
				$uslov_razlog = "u.ug_id is not null";
			}
			
			$sql = " 
				".$glavni_query." 
				AND ".$uslov_datum."
				AND ".$uslov_razlog.$uslov_team."
			";
			
			$query = mysqli_query($conn, $sql) or die();
			$totalData = mysqli_num_rows($query);
			$totalFiltered = $totalData;
			
			if( !empty($requestData['search']['value']) ) {
	
				$sql.=" AND( CONCAT(kan.ime_nd_kandidata,' ',kan.prezime_nd_kandidata) LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR CONCAT(kan.ime_nd_kandidata,kan.prezime_nd_kandidata) LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR CONCAT(kan.prezime_nd_kandidata,' ',kan.ime_nd_kandidata) LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR CONCAT(kan.prezime_nd_kandidata,kan.ime_nd_kandidata) LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR kan.ime_nd_kandidata LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR kan.prezime_nd_kandidata LIKE '%".$requestData['search']['value']."%')";
			}
			
			$query=mysqli_query($conn, $sql) or die();
			$totalFiltered = mysqli_num_rows($query);
			
			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']." ";
			$query=mysqli_query($conn, $sql) or die();
			$data = array(); //ovo sam dodao zbog erorr loga
			while( $row = mysqli_fetch_array($query) ) {
				$ug_id = $row["ug_id"];
				$ug_kandidat_id = $row["ug_kandidat_id"];
				$ime_nd_kandidata = $row["ime_nd_kandidata"]." ".$row["prezime_nd_kandidata"];
				if($row["ug_status"] == 0){
					$ug_status = '<span class="label label-danger material-label material-label_danger main-container__column text-left">Arhiviran</span>';
				}else if($row["ug_status"] == 1){
					$ug_status = '<span class="label label-primary material-label material-label_primary main-container__column text-left">Poslan</span>';
				}else if($row["ug_status"] == 4){
					$ug_status = '<span class="label label-info material-label material-label_info main-container__column text-left">Otvoren Link</span>';
				}else if($row["ug_status"] == 2){
					$ug_status = '<span class="label label-success material-label material-label_success main-container__column text-left">Prihvaćen</span>';
				}else{
					$ug_status = '<span class="label label-danger material-label material-label_danger main-container__column text-left">Odbijen</span>';
				}
				$ug_datum_slanja = date("d.m.Y H:i", strtotime($row["ug_datum_slanja"]));
				if($row["ug_file"] != NULL){
					$ug_file = '<a href="'.getSiteUrlr().'tcpdf-main/ugovori/'.$row["ug_file"].'" class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK"><i class="fa fa-file-pdf-o fa-2x" aria-hidden="true"></i></a>';
				}else{
					$ug_file = '<span class="label label-warning material-label material-label_warning main-container__column text-left">Nije kreiran</span>';
				}
				if($row["ug_file_de"] != NULL){
					$ug_file_de = '<a href="'.getSiteUrlr().'tcpdf-main/ugovori/'.$row["ug_file_de"].'" class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK"><i class="fa fa-file-pdf-o fa-2x" aria-hidden="true"></i></a>';
				}else{
					$ug_file_de = '<span class="label label-warning material-label material-label_warning main-container__column text-left">Nije kreiran</span>';
				}
				if( $row["ug_status"] == 3 ){
					if($row["kolona7"] != NULL){
						$kolona7 = '<span class="label label-default material-label material-label_default main-container__column text-left" title = "'.$row["kolona7"].'">'.substr($row["kolona7"], 0, 20).'</span>';
					}else{
						$kolona7 = '<span class="label label-danger material-label material-label_danger main-container__column text-left">Nije unešeno!</span>';
					}
				}else{
					$kolona7 = '<i class="fa fa-question-circle" aria-hidden="true" style = "color: red;" title = "Razlog je dostupan samo u slučaju da se ugovor nalazi pod statusom Odbijen!"></i>';
				}
				if( $row["ug_status"] == 3 ){
					if($row["ug_komentar"] != NULL  AND $row["ug_status"] == 3){
						$ug_komentar = '<span class="label label-default material-label material-label_default main-container__column text-left" title = "'.$row["ug_komentar"].'">'.substr($row["ug_komentar"], 0, 20).'</span>';
					}else{
						$ug_komentar = '<span class="label label-danger material-label material-label_danger main-container__column text-left">Nije unešeno!</span>';
					}
				}else{
					$ug_komentar = '<i class="fa fa-question-circle" aria-hidden="true" style = "color: red;" title = "Komentar je dostupan samo u slučaju da se ugovor nalazi pod statusom Odbijen!"></i>';
				}
				$getSiteUrl = getSiteURLr();
				
				$nData=array();
				
				$nData[] = '<p class="text-center">'.$ug_id.'</p>';
				$nData[] = '<p class="text-center"><a href="'.$getSiteUrl.'nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$ug_kandidat_id.'">'.$ime_nd_kandidata.'</a>';
				$nData[] = '<p class="text-center">'.$ug_status.'</p>';
				$nData[] = '<p class="text-center">'.$ug_datum_slanja.'</p>';
				$nData[] = '<p class="text-center">'.$ug_file.'</p>';
				$nData[] = '<p class="text-center">'.$ug_file_de.'</p>';
				$nData[] = '<p class="text-center">'.$kolona7.'</p>';
				$nData[] = '<p class="text-center">'.$ug_komentar.'</p>';
				$nData[] = '<div class="text-center"><div class="btn-group material-btn-group">
								<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_success idk_btn_table" data-toggle="dropdown">
									<i class="fa fa-cogs fa-lg" aria-hidden="true">
									</i> 
									<span class="caret material-btn__caret">
									</span>
								</button>
								<ul style = "top:32px; left: -40px; min-width: 150px !important;" class="dropdown-menu material-dropdown-menu material-dropdown-menu_success idk_dropdown_table" role="menu">
									<li>
										<a href="'.$getSiteUrl.'nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$ug_kandidat_id.'" class="material-dropdown-menu__link">
											<i class="fa fa-folder-open-o" aria-hidden="true">
											</i> 
											Otvori
										</a>
									</li>
								</ul>
							</div></div>';
				
				$data[] = $nData;
			}
			$json_data = array(
				"draw"            => intval( $requestData['draw'] ),
				"recordsTotal"    => intval( $totalData ),
				"recordsFiltered" => intval( $totalFiltered ),
				"data"            => $data
			);
			
			echo json_encode($json_data);
		break;
		
		case "lista_obrada":
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
			$drzavaImp = $_REQUEST['drzava_ssd'];
			$statusImp = $_REQUEST['status_ssd'];
			$strukaImp = $_REQUEST['struka_ssd'];
			$komunikacijaImp = $_REQUEST['komunikacija_ssd'];
			$status_da_ne_Imp = $_REQUEST['status_da_ne_ssd'];
			$status_broj_Imp = $_REQUEST['status_broj_ssd'];
			$kom_da_ne_Imp = $_REQUEST['kom_da_ne_ssd'];
			$kom_broj_Imp = $_REQUEST['kom_broj_ssd'];
			$drzavaExp = explode(",",$drzavaImp);
			$statusExp = explode(",",$statusImp);
			$strukaExp = explode(",",$strukaImp);
			$komunikacijaExp = explode(",",$komunikacijaImp);
			$status_da_ne_Exp = explode(",",$status_da_ne_Imp);
			$kom_da_ne_Exp = explode(",",$kom_da_ne_Imp);
			
			if(in_array("0", $komunikacijaExp)){
				$columns = array(
					0 => 'kan.id_broj_nd_kandidata',
					1 => 'kan.ime_nd_kandidata',
					3 => 'kan.mobilni_nd_kandidata',
					4 => 'kan.status_nd_kandidata',
					5 => 'slog.vrijeme_promjene_statusa_nd_kandidata',
					9 => 'kan.povijest_nd_kandidata'
				);
			}else{
				$columns = array(
					0 => 'kan.id_broj_nd_kandidata',
					1 => 'kan.ime_nd_kandidata',
					3 => 'kan.mobilni_nd_kandidata',
					4 => 'kan.status_nd_kandidata',
					5 => 'slog.vrijeme_promjene_statusa_nd_kandidata',
					7 => 'bilj.vrijeme_dodavanja_biljeska_nd',
					9 => 'kan.povijest_nd_kandidata'
				);
			}
			
			$trenutnoVrijeme = date("Y-m-d H:i:s");
			$uslovDrzava = "";
			$uslovStatus = "";
			$uslovKomunikacija = "";
			//---------------------------------------------------------------------------------------------------------
			if(count($drzavaExp) != 0){
				$poduslovDrzavaBih = "";
				$poduslovDrzavaSrb = "";
				$poduslovDrzavaDe = "";
				$poduslovDrzavaOst = "";
				if(in_array("387", $drzavaExp)){
					$poduslovDrzavaBih = "(kan.mobilni_nd_kandidata LIKE '+387%')";
				}else{
					$poduslovDrzavaBih = "(kan.mobilni_nd_kandidata = '0000')";
				} 
				if(in_array("381", $drzavaExp)){
					$poduslovDrzavaSrb = "(kan.mobilni_nd_kandidata LIKE '+381%')";
				}else{
					$poduslovDrzavaSrb = "(kan.mobilni_nd_kandidata = '0000')";
				} 
				if(in_array("49", $drzavaExp)){
					$poduslovDrzavaDe = "(kan.mobilni_nd_kandidata LIKE '+49%')";
				}else{
					$poduslovDrzavaDe = "(kan.mobilni_nd_kandidata = '0000')";
				} 
				if(in_array("ostalo", $drzavaExp)){
					$poduslovDrzavaOst = "((kan.mobilni_nd_kandidata NOT LIKE '+387%' AND kan.mobilni_nd_kandidata NOT LIKE '+381%' AND kan.mobilni_nd_kandidata NOT LIKE '+49%') OR kan.mobilni_nd_kandidata IS NULL )";
				}else{
					$poduslovDrzavaOst = "(kan.mobilni_nd_kandidata = '0000')";
				}
				$uslovDrzava = "
					(
						".$poduslovDrzavaBih." OR 
						".$poduslovDrzavaSrb." OR 
						".$poduslovDrzavaDe." OR 
						".$poduslovDrzavaOst."
					)
				";
			}else{
				$uslovDrzava = " kan.mobilni_nd_kandidata is not null "; //ovdje da je broj telefona razlicit od null
			}
			//---------------------------------------------------------------------------------------------------------
			if(in_array("221", $statusExp)){
				$ps221 = "1"; 
			}
			else{
				$ps221 = "0";
			}
			if(in_array("222", $statusExp)){
				$ps222 = "2"; 
			}
			else{
				$ps222 = "0";
			}
			if(in_array("223", $statusExp)){
				$ps223 = "3"; 
			}
			else{
				$ps223 = "0";
			}
			if(in_array("224", $statusExp)){
				$ps224 = "4"; 
			}
			else{
				$ps224 = "0";
			}
			if(in_array("225", $statusExp)){
				$ps225 = "5"; 
			}
			else{
				$ps225 = "0";
			}
			if(in_array("226", $statusExp)){
				$ps226 = "6"; 
			}
			else{
				$ps226 = "0";
			}
			if(in_array("227", $statusExp)){
				$ps227 = "7"; 
			}
			else{
				$ps227 = "0";
			}
			if(in_array("31", $statusExp)){
				$ps31 = "1"; 
			}
			else{
				$ps31 = "0";
			}
			if(in_array("32", $statusExp)){
				$ps32 = "2"; 
			}
			else{
				$ps32 = "0";
			}
			if(in_array("41", $statusExp)){
				$ps41 = "1"; 
			}
			else{
				$ps41 = "0";
			}
			if(in_array("42", $statusExp)){
				$ps42 = "2"; 
			}
			else{
				$ps42 = "0";
			}
			
			if(count($statusExp) != 0){
				$uslovStatus = "(
									kan.status_nd_kandidata IN (".$statusImp.")
									OR (
										kan.status_nd_kandidata = 2 
										AND kan.pstatus_nd_kandidata IN (".$ps221.",".$ps222.",".$ps223.",".$ps224.",".$ps225.",".$ps226.",".$ps227.")
									)
									OR (
										kan.status_nd_kandidata = 3 
										AND kan.pstatus_nd_kandidata IN (".$ps31.",".$ps32.") 
									)
									OR (
										kan.status_nd_kandidata = 4 
										AND kan.pstatus_nd_kandidata IN (".$ps41.",".$ps42.") 
									)
								) 
				";
			}else{
				$uslovStatus = " kan.status_nd_kandidata IN (2,3,4,5,6) ";
			}
			//---------------------------------------------------------------------------------------------------------
			if(in_array("0", $strukaExp)){
				$uslovStruka = " (kan.id_broj_nd_kandidata is not null) ";
			}else{
				$uslovStruka = " (kan.skola_smjer_nd_kandidata IN (SELECT smj.ss_id FROM idk_skole_smjerovi smj WHERE smj.ss_struka_id IN (".$strukaImp."))) ";
			}
			//---------------------------------------------------------------------------------------------------------
			if(in_array("1", $status_da_ne_Exp)){
				$uslovDanaStatus = " (DATEDIFF('".$trenutnoVrijeme."', slog.vrijeme_promjene_statusa_nd_kandidata) >= ".$status_broj_Imp.") ";
			}else{
				$uslovDanaStatus = " (kan.id_broj_nd_kandidata is not null) ";
			}
			//---------------------------------------------------------------------------------------------------------
			if(in_array("1", $kom_da_ne_Exp)){
				$uslovDanaKomunikacija = " (DATEDIFF('".$trenutnoVrijeme."', bilj.vrijeme_dodavanja_biljeska_nd) >= ".$kom_broj_Imp.") ";
			}else{
				$uslovDanaKomunikacija = " (kan.id_broj_nd_kandidata is not null) ";
			}
			//---------------------------------------------------------------------------------------------------------
			$sql = "";
			if(in_array("0", $komunikacijaExp)){
				$sql = "
					SELECT
						kan.id_broj_nd_kandidata AS idKandidata, 
						kan.ime_nd_kandidata AS imeKandidata, 
						kan.prezime_nd_kandidata AS prezimeKandidata, 
						kan.mobilni_nd_kandidata AS brojTelefonaKandidata,
						kan.status_nd_kandidata AS statusKandidata,
						kan.pstatus_nd_kandidata AS pstatusKandidata,
						slog.vrijeme_promjene_statusa_nd_kandidata AS vrijemeUlaskaUStatus,
						DATEDIFF('".$trenutnoVrijeme."', slog.vrijeme_promjene_statusa_nd_kandidata) AS brojDanaNaStatusu,
						null AS vrijemeZadnjeBiljeske,
						null AS brojDanaOdZadnjeBiljeske,
						kan.povijest_nd_kandidata AS povijestKandidata, 
						kan.povijest_vrsta_nd_kandidata AS povijestVrstaKandidata, 
						kan.kampanja_id AS kampanjaKandidata
					FROM 
						idk_nd_kandidata kan
					INNER JOIN
						idk_nd_kandidata_status_log slog
					ON 
						slog.idd_broj_nd_kandidata = kan.id_broj_nd_kandidata 
					AND 
						slog.id_log_status_nd_kandidata IN (
							SELECT 
								MAX(maxlog.id_log_status_nd_kandidata)
							FROM 
								idk_nd_kandidata_status_log maxlog
							WHERE 
								maxlog.broj_dana_statusa_nd_kandidata is null
							GROUP BY
								maxlog.idd_broj_nd_kandidata
						)
					WHERE 
						".$uslovStatus." 
						AND 
						".$uslovDrzava."
						AND 
						".$uslovDanaStatus."
						AND 
						".$uslovDanaKomunikacija." 
						AND 
						".$uslovStruka."
				";
			}else{
				$sql = "
					SELECT
						kan.id_broj_nd_kandidata AS idKandidata, 
						kan.ime_nd_kandidata AS imeKandidata, 
						kan.prezime_nd_kandidata AS prezimeKandidata, 
						kan.mobilni_nd_kandidata AS brojTelefonaKandidata,
						kan.status_nd_kandidata AS statusKandidata,
						kan.pstatus_nd_kandidata AS pstatusKandidata,
						slog.vrijeme_promjene_statusa_nd_kandidata AS vrijemeUlaskaUStatus,
						DATEDIFF('".$trenutnoVrijeme."', slog.vrijeme_promjene_statusa_nd_kandidata) AS brojDanaNaStatusu,
						bilj.vrijeme_dodavanja_biljeska_nd AS vrijemeZadnjeBiljeske,
						DATEDIFF('".$trenutnoVrijeme."', bilj.vrijeme_dodavanja_biljeska_nd) AS brojDanaOdZadnjeBiljeske,
						kan.povijest_nd_kandidata AS povijestKandidata, 
						kan.povijest_vrsta_nd_kandidata AS povijestVrstaKandidata, 
						kan.kampanja_id AS kampanjaKandidata
					FROM 
						idk_nd_kandidata kan
					INNER JOIN
						idk_nd_kandidata_status_log slog
					ON 
						slog.idd_broj_nd_kandidata = kan.id_broj_nd_kandidata 
					AND 
						slog.id_log_status_nd_kandidata IN (
							SELECT 
								MAX(maxlog.id_log_status_nd_kandidata)
							FROM 
								idk_nd_kandidata_status_log maxlog
							WHERE 
								maxlog.broj_dana_statusa_nd_kandidata is null
							GROUP BY
								maxlog.idd_broj_nd_kandidata
						)
					INNER JOIN 
						idk_nd_kandidata_biljeske bilj
					ON 
						kan.id_broj_nd_kandidata = bilj.id_kandidata_biljeska_nd 
					AND
						bilj.id_biljeska_nd IN (
							SELECT 
							MAX(maxbilj.id_biljeska_nd)
							FROM 
							idk_nd_kandidata_biljeske maxbilj
							WHERE
							maxbilj.status_biljeska_nd = 4
							GROUP BY
							maxbilj.id_kandidata_biljeska_nd
						)
					WHERE 
						".$uslovStatus." 
						AND 
						".$uslovDrzava."
						AND 
						".$uslovDanaStatus."
						AND 
						".$uslovDanaKomunikacija." 
						AND 
						".$uslovStruka."
				";
			}
			// var_dump($sql);
			//---------------------------------------------------------------------------------------------------------
			
			$query = mysqli_query($conn, $sql) or die();
			$totalData = mysqli_num_rows($query);
			$totalFiltered = $totalData;
			
			if( !empty($requestData['search']['value']) ) {
	
				$sql.=" AND( CONCAT(kan.ime_nd_kandidata,' ',kan.prezime_nd_kandidata) LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR CONCAT(kan.ime_nd_kandidata,kan.prezime_nd_kandidata) LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR CONCAT(kan.prezime_nd_kandidata,' ',kan.ime_nd_kandidata) LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR CONCAT(kan.prezime_nd_kandidata,kan.ime_nd_kandidata) LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR kan.ime_nd_kandidata LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR kan.prezime_nd_kandidata LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR email_nd_kandidata LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR mobilni_nd_kandidata LIKE '%".$requestData['search']['value']."%' )";
			}
			
			$query=mysqli_query($conn, $sql) or die();
			$totalFiltered = mysqli_num_rows($query);
			
			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
			$query=mysqli_query($conn, $sql) or die();
			$data = array(); //ovo sam dodao zbog erorr loga
			while( $row = mysqli_fetch_array($query) ) {
				$idKandidata = intval($row["idKandidata"]);
				$imeKandidata = $row["imeKandidata"];
				$prezimeKandidata = $row["prezimeKandidata"];
				$brojTelefonaKandidata = $row["brojTelefonaKandidata"];
				$statusKandidata = $row["statusKandidata"];
				$pstatusKandidata = $row["pstatusKandidata"];
				$vrijemeUlaskaUStatus = date("d.m.Y",strtotime($row["vrijemeUlaskaUStatus"]));
				$brojDanaNaStatusu = $row["brojDanaNaStatusu"];
				$vrijemeZadnjeBiljeske = $row["vrijemeZadnjeBiljeske"];
				$brojDanaOdZadnjeBiljeske = $row["brojDanaOdZadnjeBiljeske"];
				$povijestKandidata = $row["povijestKandidata"];
				$povijestVrstaKandidata = $row["povijestVrstaKandidata"];
				$kampanjaKandidata = $row["kampanjaKandidata"];
				$povijestKandidataIsipis = "";
				$povijestVrstaKandidataIsipis = "";
				$statusKandidataIspis = getStatusDIPLKandidatR($statusKandidata, $pstatusKandidata);
				$getSiteUrl = getSiteURLr();
				//---------------------------------------------------------------------------------------------------------
				if($vrijemeZadnjeBiljeske != NULL){
					$vrijemeZadnjeBiljeske = date("d.m.Y", strtotime($vrijemeZadnjeBiljeske));
				}else{
					$vrijemeZadnjeBiljeske = '<i class="fa fa-question-circle" aria-hidden="true" style = "color: red;" title = "Nije postavljen kriterij za zadnju komunikaciju!"></i>';
				}
				if($brojDanaOdZadnjeBiljeske == NULL){
					$brojDanaOdZadnjeBiljeske = '<i class="fa fa-question-circle" aria-hidden="true" style = "color: red;" title = "Nije postavljen kriterij za zadnju komunikaciju!"></i>';
				}
				//---------------------------------------------------------------------------------------------------------
				if(strpos(substr($brojTelefonaKandidata, 0, 4), "381")){
					$drzavaKandidata = '<img src="'.getSiteUrlr().'images/sr3d.png" width=20>';
				}else if(strpos(substr($brojTelefonaKandidata, 0, 4), "387")){
					$drzavaKandidata = '<img src="'.getSiteUrlr().'images/bs3d.png" width=20>';
				}else if(strpos(substr($brojTelefonaKandidata, 0, 3), "49")){
					$drzavaKandidata = '<img src="'.getSiteUrlr().'images/de3d.png" width=20>';
				}else{
					$drzavaKandidata = '<img src="'.getSiteUrlr().'images/globe3d.png" width=20>';
				}
				//---------------------------------------------------------------------------------------------------------
				if($povijestKandidata == 0){
					$povijestKandidataIsipis = '<span class="label label-success material-label material-label_success main-container__column text-left" title = "Ručna registracija">Ručna registracija</span>';
				}
				else if($povijestKandidata == 1){
					if($povijestVrstaKandidata == 1){
						$povijestVrstaKandidataIsipis = 'SMS';
					}
					else if($povijestVrstaKandidata == 2){
						$povijestVrstaKandidataIsipis = 'JobStep Messenger';
					}
					else if($povijestVrstaKandidata == 3){
						$povijestVrstaKandidataIsipis = 'CRM';
					}
					else if($povijestVrstaKandidata == 4){
						$povijestVrstaKandidataIsipis = 'Viber';
					}
					else if($povijestVrstaKandidata == 5){
						$povijestVrstaKandidataIsipis = 'Viber Stornirani';
					}
					$povijestKandidataIsipis = '<span class="label label-primary material-label material-label_primary main-container__column text-left">Kandidati-'.$povijestVrstaKandidataIsipis.'</span>';
				}else if($povijestKandidata == 2){
					if($povijestVrstaKandidata == 1){
						$povijestVrstaKandidataIsipis = 'APP';
					}
					else if($povijestVrstaKandidata == 2){
						$povijestVrstaKandidataIsipis = 'WEB';
					}
					$povijestKandidataIsipis = '<span class="label label-warning material-label material-label_warning main-container__column text-left">Sve za vize-'.$povijestVrstaKandidataIsipis.'</span>';
				}
				else if($povijestKandidata == 3){
					$povijestKandidataIsipis = '<span class="label label-info material-label material-label_info main-container__column text-left">JobStep Partner APP</span>';
				}
				else if($povijestKandidata == 4){
					$povijestKandidataIsipis = '<span class="label label-danger material-label material-label_danger main-container__column text-left">JobStep Web</span>';
				}
				else if($povijestKandidata == 5){
					$povijestKandidataIsipis = '<span class="label label-default material-label material-label_default main-container__column text-left" title = "'.getKampanjePuniNazivDIPLR($kampanjaKandidata).'">'.getKampanjeSkrNazivDIPLR($kampanjaKandidata).'</span>';
				}
				//---------------------------------------------------------------------------------------------------------
				
				$nData=array();
				
				$nData[] = '<p class="text-center">'.$idKandidata.'</p>';
				$nData[] = '<p class="text-center"><a href="'.$getSiteUrl.'nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$idKandidata.'">'.$imeKandidata.' '.$prezimeKandidata.'</a>';
				$nData[] = '<p class="text-center">'.$drzavaKandidata.'</p>';
				$nData[] = '<p class="text-center">'.$brojTelefonaKandidata.'</p>';
				$nData[] = '<p class="text-center">'.$statusKandidataIspis.'</p>';
				$nData[] = '<p class="text-center">'.$vrijemeUlaskaUStatus.'</p>';
				$nData[] = '<p class="text-center">'.$brojDanaNaStatusu.'</p>';
				$nData[] = '<p class="text-center">'.$vrijemeZadnjeBiljeske.'</p>';
				$nData[] = '<p class="text-center">'.$brojDanaOdZadnjeBiljeske.'</p>';
				$nData[] = '<p class="text-center">'.$povijestKandidataIsipis.'</p>';
				$nData[] = '<div class="text-center"><div class="btn-group material-btn-group">
								<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_success idk_btn_table" data-toggle="dropdown">
									<i class="fa fa-cogs fa-lg" aria-hidden="true">
									</i> 
									<span class="caret material-btn__caret">
									</span>
								</button>
								<ul style = "top:32px; left: -40px; min-width: 150px !important;" class="dropdown-menu material-dropdown-menu material-dropdown-menu_success idk_dropdown_table" role="menu">
									<li>
										<a href="'.$getSiteUrl.'nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$idKandidata.'" class="material-dropdown-menu__link">
											<i class="fa fa-folder-open-o" aria-hidden="true">
											</i> 
											Otvori
										</a>
									</li>
								</ul>
							</div></div>';
				
				$data[] = $nData;
			}
			$json_data = array(
						 "draw"            => intval( $requestData['draw'] ),
						"recordsTotal"    => intval( $totalData ),
						"recordsFiltered" => intval( $totalFiltered ),
						"data"            => $data
						);
			
			echo json_encode($json_data);
		break;
	}
?>
