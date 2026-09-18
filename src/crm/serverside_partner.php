<?php
include("includes/functions.php");
require_once("includes/env.php");
$page = $_REQUEST['page'];
switch($page)
{

	case "list":
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

		$columns = array(
			0 => 'jp_id',
			1 => 'jp_imeprezime',
			2 => 'jp_brtelefona',
			3 => 'jp_email',
			4 => 'jp_position',
			5 => 'jp_register_date'
		);

		$sql = "SELECT jp_id, jp_imeprezime, jp_email, jp_brtelefona, jp_social_imgurl, jp_provider, jp_drzava, jp_position, jp_register_date
				FROM  idk_jobstep_partners
				WHERE jp_confirmedaccount = 1 AND jp_user_type != 1" ;

		$query=mysqli_query($conn, $sql) or die("serverside_partner.php: get partners");
		$totalData = mysqli_num_rows($query);
		$totalFiltered = $totalData;

		if( !empty($requestData['search']['value']) ) {
			
			$sql.=" AND CONCAT(jp_imeprezime) LIKE '%".$requestData['search']['value']."%' ";
			$sql.=" OR jp_email LIKE '%".$requestData['search']['value']."%' ";
			$sql.=" OR jp_brtelefona LIKE '%".$requestData['search']['value']."%' ";
		}

		$query=mysqli_query($conn, $sql) or die("serverside_partner.php: get partners");
		$totalFiltered = mysqli_num_rows($query);

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

		$query=mysqli_query($conn, $sql) or die("serverside_partner.php: get partners");

		$data = array();
		while( $row=mysqli_fetch_array($query) ) {
			$jp_id = $row['jp_id'];
			$jp_imeprezime = $row['jp_imeprezime'];
			$jp_email = $row['jp_email'];
			$jp_brtelefona = $row['jp_brtelefona'];
			$jp_provider = $row['jp_provider'];
			$jp_drzava = $row['jp_drzava'];
			$jp_register_date = date('H:i d.m.Y', strtotime($row['jp_register_date']));


			if(($row['jp_social_imgurl'] == NULL) OR ($row['jp_social_imgurl'] == '')){
			$jp_social_imgurl = getSiteUrlr()."files/kandidati/nonekandidati.jpg";
			}else{
			$jp_social_imgurl = $row['jp_social_imgurl'];
			}

			if($row['jp_position'] == 1){
			$jp_position = "Junior";
			}elseif($row['jp_position'] == 2){
			$jp_position = "Partner";
			}elseif($row['jp_position'] == 3){
			$jp_position = "Senior";
			}elseif($row['jp_position'] == 4){
			$jp_position = "Agency";
			}

			//BROJ PREGLEDA
			$query_pregled = $db->prepare("
			SELECT SUM(jpp_pregledi_count) as jpp_pregledi_count
			FROM idk_jobstep_partners_pregledi
			WHERE jpp_partnerid = :jpp_partnerid");

			$query_pregled->execute(array(
			':jpp_partnerid' => $jp_id));  
			$row = $query_pregled->fetch();
			$jpp_pregledi_count = intval($row['jpp_pregledi_count']);


			// PK
			$partner_query = $db->prepare("
			SELECT kandidat_id
			FROM idk_kandidati
			WHERE kandidat_partnerid = :kandidat_partnerid AND kandidat_id >= 11000");

			$partner_query->execute(array(':kandidat_partnerid' => $jp_id));
			$partner_counter = $partner_query->rowCount();



			//SVI DIPL
			$query_DIPL = $db->prepare("
			SELECT id_broj_nd_kandidata
			FROM idk_nd_kandidata
			WHERE kandidat_idd = :kandidat_idd AND povijest_nd_kandidata = :povijest_nd_kandidata");

			$query_DIPL->execute(array(
			':kandidat_idd' => $jp_id,
			':povijest_nd_kandidata' => 3));
			$query_DIPL_counter = $query_DIPL->rowCount();
			
			
			$nestedData=array();
				$nestedData[] = '<p class="text-center">'.$jp_id.'</p>';
				$nestedData[] = '<p><a href="'.$jp_id.'">'.$jp_imeprezime.'</a></p>';
				$nestedData[] = '<p><a href="tel: '.$jp_brtelefona.'">'.$jp_brtelefona.'</a></p>';
				if($jp_drzava == 'BA')
					$nestedData[] = '<p class="text-center"><img src="'.getSiteUrlr().'images/BosniaHerzegowina.png" width="25"></p>';
				elseif($jp_drzava == 'DE')
					$nestedData[] = '<p class="text-center"><img src="'.getSiteUrlr().'images/Germany.png" width="25"></p>';
				else $nestedData[] = '<p class="text-center">'.$jp_drzava.'</p>';


				$nestedData[] = '<p><a href="mailto: '.$jp_email.'">'.$jp_email.'</a></p>';
				$nestedData[] = '<p><span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$jp_position.'</span></p>';
				$nestedData[] = '<p><span class="label label-info material-label material-label_info material-label_xs main-container__column">'.$jp_register_date.'</span></p>';


				$nestedData[] = '<p class="text-center"><span class="label label-info material-label material-label_info material-label_xs main-container__column"> '.$jpp_pregledi_count.' </span></p>';
				$nestedData[] = '<p class="text-center"><span class="label label-info material-label material-label_info material-label_xs main-container__column"> '.$partner_counter.'</span></p>';
				$nestedData[] = '<p class="text-center"><span class="label label-info material-label material-label_info material-label_xs main-container__column"> '.$query_DIPL_counter.' </span></p>';

				$nestedData[] = '<p class="text-center">
				<div class="btn-group material-btn-group">
				<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
				<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
				<li><a href="'.$jp_id.'" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
				</ul>
				</div>
				</p>';



			$data[] = $nestedData;

		}



		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),
					"recordsTotal"    => intval( $totalData ),
					"recordsFiltered" => intval( $totalFiltered ),
					"data"            => $data
					);

		echo json_encode($json_data);
	break;

	case "list_dvag":
		$getSiteUrl = getSiteURLr();
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

		$columns = array(
			0 => 'jp_id',
			1 => 'jp_imeprezime',
			2 => 'jp_brtelefona',
			3 => 'jp_email',
			4 => 'jp_register_date',
			5 => 'broj_leadova',
			6 => 'broj_pregleda',
			7 => 'broj_preporucenih',
			8 => 'broj_kompanija'
		);

		$sql = "SELECT
					jp_id,
					jp_imeprezime,
					jp_email,
					jp_brtelefona,
					jp_social_imgurl,
					jp_provider,
					jp_drzava,
					jp_position,
					jp_register_date,
					jp_makler_id,
					COALESCE(leadovi.broj_leadova,0) as broj_leadova,
					COALESCE(pregledi.broj_pregleda,0) as broj_pregleda,
					COALESCE(preporuceni.broj_preporucenih,0) as broj_preporucenih,
					COALESCE(kompanije.broj_kompanija,0) as broj_kompanija
				FROM
					idk_jobstep_partners
				LEFT JOIN(
					SELECT zaduzeni_makler_id,
						COUNT(kandidat_id) AS broj_leadova
					FROM
						idk_kandidati
					GROUP BY
						zaduzeni_makler_id
				) AS leadovi
				ON
					jp_id = leadovi.zaduzeni_makler_id
				LEFT JOIN(
					SELECT jpp_partnerid,
						SUM(jpp_pregledi_count) AS broj_pregleda
					FROM
						idk_jobstep_partners_pregledi
					GROUP BY
						jpp_partnerid
				) AS pregledi
				ON
					jp_id = pregledi.jpp_partnerid
				LEFT JOIN(
					SELECT kandidat_partnerid,
						COUNT(kandidat_id) AS broj_preporucenih
					FROM
						idk_kandidati
					WHERE
						kandidat_nalog_id IS NOT NULL AND kandidat_status_prijave IS NOT NULL AND kandidat_status_prijave NOT IN(0, 1)
					GROUP BY
						kandidat_partnerid
				) AS preporuceni
				ON
					jp_id = preporuceni.kandidat_partnerid
				LEFT JOIN(
					SELECT js_partner_id,
						COUNT(company_id) AS broj_kompanija
					FROM
						idk_companies
					GROUP BY
						js_partner_id
				) AS kompanije
				ON
					jp_id = kompanije.js_partner_id
				WHERE
					jp_confirmedaccount = 1 AND jp_user_type = 1" 
		;

		$query=mysqli_query($conn, $sql) or die("serverside_partner.php: get partners");
		$totalData = mysqli_num_rows($query);
		$totalFiltered = $totalData;

		if( !empty($requestData['search']['value']) ) {
			
			$sql.=" AND (CONCAT(jp_imeprezime) LIKE '%".$requestData['search']['value']."%' ";
			$sql.=" OR jp_email LIKE '%".$requestData['search']['value']."%' ";
			$sql.=" OR jp_id LIKE '%".$requestData['search']['value']."%' ";
			$sql.=" OR jp_makler_id LIKE '%".$requestData['search']['value']."%' ";
			$sql.=" OR jp_brtelefona LIKE '%".$requestData['search']['value']."%' )";
		}

		$query=mysqli_query($conn, $sql) or die("serverside_partner.php: get partners");
		$totalFiltered = mysqli_num_rows($query);

		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

		$query=mysqli_query($conn, $sql) or die("serverside_partner.php: get partners");

		$data = array();
		while( $row=mysqli_fetch_array($query) ) {
			$jp_id = $row['jp_id'];
			$jp_makler_id = $row['jp_makler_id'];
			$jp_imeprezime = $row['jp_imeprezime'];
			$jp_email = $row['jp_email'];
			$jp_brtelefona = $row['jp_brtelefona'];
			$jp_provider = $row['jp_provider'];
			$jp_drzava = $row['jp_drzava'];
			$jp_register_date = date('H:i d.m.Y', strtotime($row['jp_register_date']));

			$jpp_leadovi_count = $row['broj_leadova'];
			$jpp_pregledi_count = $row['broj_pregleda'];
			$number_of_candidates_count = $row['broj_preporucenih'];
			$number_of_companies_count = $row['broj_kompanija'];


			if(($row['jp_social_imgurl'] == NULL) OR ($row['jp_social_imgurl'] == '')){
			$jp_social_imgurl = getSiteUrlr()."files/kandidati/nonekandidati.jpg";
			}else{
			$jp_social_imgurl = $row['jp_social_imgurl'];
			}

			//BROJ LEADOVA
			// $query_leadovi = $db->prepare("SELECT COUNT(kandidat_id) as broj_leadova FROM idk_kandidati WHERE idk_kandidati.zaduzeni_makler_id = :partner_id");

			// $query_leadovi->execute(array(':partner_id' => $jp_id));  

			// $row = $query_leadovi->fetch();
			// $jpp_leadovi_count = intval($row['broj_leadova']);
			
			//BROJ PREGLEDA
			// $query_pregled = $db->prepare("SELECT SUM(jpp_pregledi_count) as jpp_pregledi_count FROM idk_jobstep_partners_pregledi WHERE jpp_partnerid = :jpp_partnerid");

			// $query_pregled->execute(array(':jpp_partnerid' => $jp_id));  

			// $row = $query_pregled->fetch();
			// $jpp_pregledi_count = intval($row['jpp_pregledi_count']);


			// $query_candidate_counter = $db->prepare('SELECT COUNT(idk_kandidati.kandidat_id) as number_of_candidates FROM idk_kandidati WHERE idk_kandidati.kandidat_partnerid = :partner_id AND idk_kandidati.kandidat_nalog_id IS NOT NULL AND idk_kandidati.kandidat_status_prijave IS NOT NULL AND idk_kandidati.kandidat_status_prijave NOT IN (0,1)');

            // $query_candidate_counter->execute(array(
            //     ':partner_id' => $jp_id
            // ));

            // $number_of_candidates = $query_candidate_counter->fetch();
            // $number_of_candidates_count = $number_of_candidates['number_of_candidates'];

			// $query_company_counter = $db->prepare("SELECT COUNT(idk_companies.company_id) as number_of_companies FROM idk_companies WHERE idk_companies.js_partner_id = :partner_id");

			// $query_company_counter->execute(array(
			// 	':partner_id' => $jp_id
			// ));

			// $number_of_companies = $query_company_counter->fetch();
			// $number_of_companies_count = $number_of_companies['number_of_companies'];
				
			$nestedData=array();
			$nestedData[] = '<p class="text-center">'.$jp_id.'</p>';
			$nestedData[] = '<p class="text-center">'.$jp_imeprezime.'</p>';
			$nestedData[] = '<p class="text-center"><a href="tel: '.$jp_brtelefona.'">'.$jp_brtelefona.'</a></p>';


			$nestedData[] = '<p class="text-center"><a href="mailto: '.$jp_email.'">'.$jp_email.'</a></p>';
			$nestedData[] = '<p class="text-center"><span class="label label-info material-label material-label_info material-label_xs main-container__column">'.$jp_register_date.'</span></p>';

			$nestedData[] = '<p class="text-center"><span class="label label-info material-label material-label_info material-label_xs main-container__column"> '.$jpp_leadovi_count.' </span></p>';
			$nestedData[] = '<p class="text-center"><span class="label label-info material-label material-label_info material-label_xs main-container__column"> '.$jpp_pregledi_count.' </span></p>';
			$nestedData[] = '<p class="text-center"><span class="label label-info material-label material-label_info material-label_xs main-container__column"> '.$number_of_candidates_count.'</span></p>';
			$nestedData[] = '<p class="text-center"><span class="label label-info material-label material-label_info material-label_xs main-container__column"> '.$number_of_companies_count.' </span></p>';
			
			$nestedData[] = '<p class="text-center">
							<div class="btn-group material-btn-group">
							<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
							<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
							<li><a href="'.$getSiteUrl.'partners?page=open_dvag&id='.$jp_id.'" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
							</ul>
							</div>
							</p>';



			$data[] = $nestedData;

		}

		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),
					"recordsTotal"    => intval( $totalData ),
					"recordsFiltered" => intval( $totalFiltered ),
					"data"            => $data
					);

		echo json_encode($json_data);
	break;

	case "list_kandidati":
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
			$logirani_zaposlenik = intval($_REQUEST['logirani_zaposlenik']);
			
			
			$columns = array(
				0 => 'kandidat_id',
				1 => 'kandidat_ime',
				2 => 'kandidat_datetime',
				3 => 'status_naziv',
				4 => 'nalog_naziv',
				5 => 'jp_imeprezime'
			);
			
			$sql = "SELECT
						idk_kandidati.kandidat_ime,
						idk_nalozi.nalog_naziv,
						idk_nalozi.nalog_id,
						idk_kandidati.kandidat_prezime,
						idk_kandidat_status_prijave.status_naziv,
						kandidat_id,
						kandidat_partnerid,
						jp_id,
						jp_imeprezime,
						kandidat_datetime
					FROM
						idk_kandidati
					JOIN idk_jobstep_partners ON idk_kandidati.kandidat_partnerid = idk_jobstep_partners.jp_id
					LEFT JOIN idk_nalozi ON idk_kandidati.kandidat_nalog_id = idk_nalozi.nalog_id
					JOIN idk_kandidat_status_prijave ON idk_kandidati.kandidat_status_prijave = idk_kandidat_status_prijave.status_id
					WHERE
						idk_jobstep_partners.jp_partner_company = 3 
					";
			$query = mysqli_query($conn, $sql) or die();
			$totalData = mysqli_num_rows($query);
			$totalFiltered = $totalData;
			
			if( !empty($requestData['search']['value']) ) {
				
				$s_value = $requestData['search']['value'];
				$space_pos = strpos($s_value, ' ');
				
				
				$sql.=" AND ( CONCAT(kandidat_ime,' ',kandidat_prezime) LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR nalog_naziv LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR kandidat_id LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR nalog_id LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR kandidat_partnerid LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR jp_imeprezime LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR status_naziv LIKE '%".$requestData['search']['value']."%' ";
			}
			
			$query=mysqli_query($conn, $sql) or die();
			$totalFiltered = mysqli_num_rows($query);

			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
			$query = mysqli_query($conn, $sql) or die();
			$data = array(); //ovo sam dodao zbog erorr loga
			while( $row = mysqli_fetch_array($query) ) {

				$id = $row['kandidat_id'];
				$ime_prezime = $row['kandidat_ime']." ".$row['kandidat_prezime'];
				$kandidat_prijava = date('d.m.Y. H:i:s', strtotime($row['kandidat_datetime']));
				$status_naziv = $row['status_naziv'];
				$nalog_naziv = $row['nalog_naziv'];
				$nalog_id = $row['nalog_id'];
				$kandidat_partnerid = $row['kandidat_partnerid'];
				$jp_imeprezime=$row['jp_imeprezime'];
				$jp_id = $row['jp_id'];
				$getSiteUrl = getSiteURLr();

				
				$nData=array();
				
				$nData[] = '<a href="'.$getSiteUrl.'kandidati?page=open&id='.$id.'">'.$id.'</a>';
				$nData[] = '<a href="'.$getSiteUrl.'kandidati?page=open&id='.$id.'">'.$ime_prezime.'</a>';
				$nData[] = '<p class="text-center">'.$kandidat_prijava.'</p>';
				$nData[] = '<p class="text-center">'.$status_naziv.'</p>';
				$nData[] = '<a href="'.$getSiteUrl.'nalozi?page=open&id='.$nalog_id.'">'.$nalog_naziv.'</a>';
				$nData[] = '<a href="'.$getSiteUrl.'partners?page=open_dvag&id='.$jp_id.'">'.$jp_imeprezime.'</a>';
				
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
}

?>
