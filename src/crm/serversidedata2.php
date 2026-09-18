<?php
include("includes/functions.php");
require_once("includes/env.php");

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
$uslovi = $_REQUEST['uslovi'];

if(isset($_POST['filter_gender']))
{
	$filter = $_POST['filter_gender'];
	$filter_status_f = implode(',', $filter);
}

$columns = array(
	0 => 'kandidat_id',
	1 => 'kandidat_slika',
	2 => 'kandidat_ime',
	3 => 'cv_de',
    4 => 'nalog_naziv',
    5 => 'kg_title',
    6 => 'kandidat_status'
);

if(isset($_COOKIE['archive_status'])){
	$archivestatus = 1;
}else{
	$archivestatus = 0;
}

if($archivestatus == 0){
	$sql = "
	SELECT 
	kandidat_id, 
       kandidat_ime, 
       kandidat_prezime, 
	   kandidat_datumrodjenja,
       kandidat_spol, 
       kandidat_jmbg, 
       kandidat_status,
	   kandidat_status_messenger,
       kandidat_slika, 
       kandidat_email, 
       kandidat_datetime, 
       kandidat_visitedurl, 
       kandidat_prijava_na, 
       kandidat_group, 
	   kandidat_porijeklo, 
       cv_ba, 
       cv_de, 
       profile_ba, 
       profile_de, 
       idkg.kg_title, 
       idklg.lg_id, 
       idklg.lg_url, 
       idklg.lg_desc, 
       idklg.lg_datetime, 
       idklg.lg_nalogid, 
       idks.status_id, 
       idnalog.nalog_id, 
       idnalog.nalog_naziv, 
       idks.status_naziv 
		FROM  idk_kandidati 
       INNER JOIN idk_kandidati_grupe idkg 
               ON kandidat_group = idkg.kg_id 
       INNER JOIN idk_link_generator idklg 
               ON kandidat_visitedurl = idklg.lg_id 
       LEFT JOIN idk_nalozi idnalog 
               ON idklg.lg_nalogid = idnalog.nalog_id 			   
       INNER JOIN idk_kandidat_status idks 
               ON kandidat_status = idks.status_id 
	WHERE  kandidat_status != 3 
	" ;
}else{
	$sql = "SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_spol, kandidat_jmbg, kandidat_status, kandidat_status_messenger, kandidat_slika, kandidat_email, kandidat_datetime, kandidat_visitedurl, kandidat_prijava_na, kandidat_group, kandidat_porijeklo, cv_ba, cv_de, profile_ba, profile_de, idkg.kg_title, idklg.lg_id, idklg.lg_url, idklg.lg_desc, idklg.lg_datetime, idks.status_id, idks.status_naziv FROM idk_kandidati INNER JOIN idk_kandidati_grupe idkg ON kandidat_group = idkg.kg_id INNER JOIN idk_link_generator idklg ON kandidat_visitedurl = idklg.lg_id INNER JOIN idk_kandidat_status idks ON kandidat_status = idks.status_id WHERE kandidat_status = 3" ;
}


//$sql = "SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_spol, kandidat_jmbg, kandidat_status, kandidat_slika, kandidat_email, kandidat_datetime, kandidat_visitedurl, kandidat_prijava_na, kandidat_group, idkg.kg_title, idklg.lg_id, idklg.lg_url, idklg.lg_desc, idklg.lg_datetime FROM idk_kandidati INNER JOIN idk_kandidati_grupe idkg ON kandidat_group = idkg.kg_id INNER JOIN idk_link_generator idklg ON kandidat_visitedurl = idklg.lg_id  WHERE kandidat_status != 3";
$query=mysqli_query($conn, $sql) or die("serversidedata.php: get employees");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;



//$sql = "SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_spol, kandidat_jmbg, kandidat_status, kandidat_slika, kandidat_email, kandidat_datetime, kandidat_visitedurl, kandidat_prijava_na, kandidat_group, idkg.kg_title, idklg.lg_id, idklg.lg_url, idklg.lg_desc, idklg.lg_datetime FROM idk_kandidati INNER JOIN idk_kandidati_grupe idkg ON kandidat_group = idkg.kg_id INNER JOIN idk_link_generator idklg ON kandidat_visitedurl = idklg.lg_id";

if(isset($_POST['filter_gender']))
{
 $sql.=" AND kandidat_status IN (".$filter_status_f.") ";

}
if( !empty($requestData['search']['value']) ) {
	
	$sql.=" AND CONCAT(kandidat_ime,' ',kandidat_prezime) LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR CONCAT(kandidat_ime,kandidat_prezime) LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR CONCAT(kandidat_prezime,' ',kandidat_ime) LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR CONCAT(kandidat_prezime,kandidat_ime) LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR kandidat_ime LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR kandidat_prezime LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR status_naziv LIKE '%".$requestData['search']['value']."%' ";
    $sql.=" OR kandidat_email LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR idkg.kg_title LIKE '%".$requestData['search']['value']."%' ";
	$sql.=" OR kandidat_mobitel LIKE '%".$requestData['search']['value']."%' ";
	if($requestData['search']['value'] == "partner"){
		$sql.=" OR kandidat_porijeklo IN (6,7) ";
	}
}
$query=mysqli_query($conn, $sql) or die("serversidedata.php: get employees");
$totalFiltered = mysqli_num_rows($query);

$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";

$query = mysqli_query($conn, $sql) or die("serversidedata.php: get employees");

$data = array();
while( $row=mysqli_fetch_array($query) ) {

	$kandidat_id = $row['kandidat_id'];
	$kandidat_ime = $row['kandidat_ime'];
	$kandidat_prezime = $row['kandidat_prezime'];
	$kandidat_datumrodjenja = $row['kandidat_datumrodjenja'];
	$kandidat_spol = $row['kandidat_spol'];
	$kandidat_jmbg = $row['kandidat_jmbg'];
	$kandidat_group = $row['kandidat_group'];
	$kandidat_email = $row['kandidat_email'];
	$kandidat_datetime = $row['kandidat_datetime'];
	$kandidat_visitedurl = $row['kandidat_visitedurl'];
	$kandidat_prijava_na = $row['kandidat_prijava_na'];
	$kandidat_porijeklo = $row['kandidat_porijeklo'];
	$kg_title = $row['kg_title'];
	$lg_id = $row['lg_id'];
	$lg_url = $row['lg_url'];
	$lg_desc = $row['lg_desc'];
	$kandidat_status = $row['status_naziv'];
	$kandidat_status_messenger = $row['kandidat_status_messenger'];
	$getSiteUrl = getSiteURLr();
	$cv_ba = $row['cv_ba'];
	$cv_de = $row['cv_de'];
	
	$profile_ba = $row['profile_ba'];
	$profile_de = $row['profile_de'];
	
	if($kandidat_status_messenger == 2)
		$bot = ' <i class="fa fa-commenting" style="color: <?php echo $style_bot?> font-size: 1.5em;" aria-hidden="true" title = "Kandidat se logirao na aplikaciju Messenger."></i>';
	else if($kandidat_status_messenger == 1)
		$bot = ' <i class="fa fa-commenting" style="color: <?php echo $style_bot?> font-size: 1.5em;" aria-hidden="true" title = "Kandidat se čeka za instalaciju Messengera."></i>';
	else
		$bot = "";

	if($cv_ba == 0){
		$cv_txt_ba = '<img src="'.$getSiteUrl.'images/BosniaHerzegowinaDesaturated.png" width=25>';
		$cv_filepath = 'cv_ba?page=open&id='.$kandidat_id;
	}else{
		$cv_filepath = 'files/cv/ba/'.$kandidat_id.'-'.$kandidat_ime.'_'.$kandidat_prezime.'.pdf';
		$cv_txt_ba = '<a href=""><a href="'.$getSiteUrl.''.$cv_filepath.'" target="_BLANK"><img src="'.$getSiteUrl.'images/BosniaHerzegowina.png" width=25></a>';
	}

	if($cv_de == 0){
		$cv_txt_de = '<img src="'.$getSiteUrl.'images/GermanyDesaturated.png" width=25>';
		$cv_filepath_de = 'cv_de?page=open&id='.$kandidat_id;
	}else{
		$cv_filepath_de = 'files/cv/de/'.$kandidat_id.'-'.$kandidat_ime.'_'.$kandidat_prezime.'.pdf';
		$cv_txt_de = '<a href=""><a href="'.$getSiteUrl.''.$cv_filepath_de.'" target="_BLANK"><img src="'.$getSiteUrl.'images/Germany.png" width=25></a>';
	}

	if($profile_ba == 0){
		$profil_filepath = 'profil_ba?page=open&id='.$kandidat_id;
	}else{
		$profil_filepath = 'files/profile/ba/'.$kandidat_id.'-'.$kandidat_ime.'.pdf';
	}

	if($profile_de == 0){
		$profil_filepath_de = 'profil_de?page=open&id='.$kandidat_id;
	}else{
		$profil_filepath_de = 'files/profile/de/'.$kandidat_id.'-'.$kandidat_ime.'.pdf';
	}

	$kandidat_datetime = date('d.m.Y H:i:s', strtotime($kandidat_datetime));
	// $datetime = new DateTime($kandidat_datetime);
	// $kandidat_datetime = $datetime->format(DateTime::ISO8601);
	// SLIKA
	if($row['kandidat_slika'] == "none"){
		$kandidat_slika = "nonekandidati.jpg";
	}else{
		$kandidat_slika = $row['kandidat_slika'];
	}
	
	// DUPLIKAT
	$duplikat = getDoubleExistence($kandidat_ime, $kandidat_prezime, $kandidat_datumrodjenja); 
	if($duplikat == 1){
		$duplikat_text = '<p class="text-center"><span class="label label-danger material-label material-label_danger material-label_xs main-container__column">IMA DUPLIKAT</span></p>';
		$doubleicon = '<i class="fa fa-user-times" style="color: #f3413c; font-size: 1.5em;" aria-hidden="true" title = "Postoji duplikat!"></i> ';
		//$doubleicon = '<span class="text-right"><i class="fa fa-user-times" style="color: #f3413c; font-size: 1.5em;" aria-hidden="true" title = "Postoji duplikat!"></i></span> ';
	}else{
		$duplikat_text = '<p class="text-center"><span class="label label-success material-label material-label_success material-label_xs main-container__column">NEMA DUPLIKAT</span></p>';
		$doubleicon = "";
	}
	
	//PORIJEKLO
	switch($kandidat_porijeklo){
		case "1":
			$porijeklo_text = '<p class="text-center"><span class="label label-success material-label material-label_success material-label_xs main-container__column">DIPL</span></p>';
		break;
		case "2":
			$porijeklo_text = '<p class="text-center"><span class="label label-secondary material-label material-label_secondary material-label_xs main-container__column">DAK</span></p>';
		break;
		case "3":
			$porijeklo_text = '<p class="text-center"><span class="label label-info material-label material-label_info material-label_xs main-container__column">DIPL - Partner</span></p>';
		break;
		case "4":
			$porijeklo_text = '<p class="text-center"><span class="label label-info material-label material-label_info material-label_xs main-container__column">DIPL - SZV</span></p>';
		break;
		case "5":
			$porijeklo_text = '<p class="text-center"><span class="label label-info material-label material-label_info material-label_xs main-container__column">DIPL - WEB</span></p>';
		break;
		case "6":
			$porijeklo_text = '<p class="text-center"><span class="label label-info material-label material-label_info material-label_xs main-container__column">Partner</span></p>';
		break;
		case "7":
			$porijeklo_text = '<p class="text-center"><span class="label label-success material-label material-label_success material-label_xs main-container__column">Partner</span></p>';
		break;
		case "0":
			$porijeklo_text = '<p class="text-center"><span class="label label-primary material-label material-label_primary material-label_xs main-container__column">Prijava na oglas</span></p>';
		break;
		
	}
	
	
	// GET NALOG ID FROM LINK GENERATOR
	$linkgen_query = $db->prepare("
					SELECT lg_nalogid, nalog_naziv
					FROM idk_link_generator
					INNER JOIN idk_nalozi ON idk_link_generator.lg_nalogid = idk_nalozi.nalog_id
					WHERE lg_id = :lg_id
				  ");

	$linkgen_query->execute(array(':lg_id' => $kandidat_visitedurl));

	$rowLinkGen = $linkgen_query->fetch();

        $lg_nalogid = $rowLinkGen['lg_nalogid'];
        $nalog_naziv = $rowLinkGen['nalog_naziv'];
		if($nalog_naziv != ""){
			if (strlen($nalog_naziv) > 50){
				$nalog_puni_naziv = $nalog_naziv;
				$nalog_naziv = substr($nalog_naziv, 0, 50);
				$nalog_naziv = $nalog_naziv."...";
			}else{
				$nalog_puni_naziv = "";
				$nalog_naziv = $nalog_naziv;
			}
			$nalog_boja = "success";
		}else{
			$nalog_naziv = "Nije definisan";
			$nalog_boja = "warning";
			$nalog_puni_naziv = "";
		}		
		
	$nestedData=array();

	$nestedData[] = '<p class="text-center">'.$kandidat_id.'</p>';
	$nestedData[] = '<p class="text-center"><a href="'.$getSiteUrl.'kandidati?page=open&id='.$kandidat_id.'"><img class="idk_profile_img" src="'.$getSiteUrl.'files/kandidati/'.$kandidat_slika.'"></a></p>';
	$nestedData[] = '<p class="text-center">'.$doubleicon.'</p>';
	$nestedData[] = '<p class="text-left"><a href="'.$getSiteUrl.'kandidati?page=open&id='.$kandidat_id.'">'.$kandidat_ime.' '.$kandidat_prezime.'</a></p>';
	$nestedData[] =  '<span class="date-sort-class">'.$kandidat_datetime.'</span>';
	$nestedData[] = $porijeklo_text;
	$nestedData[] = '<p class="text-center">'.$cv_txt_ba.' '.$cv_txt_de.'</p>';
	$nestedData[] = '<p class="text-left"><a href="'.$getSiteUrl.'nalozi?page=open&id='.$lg_nalogid.'" target="_BLANK" class="label label-'.$nalog_boja.' material-label material-label_'.$nalog_boja.' material-label_xs main-container__column" title="'.$nalog_puni_naziv.'">'. $lg_nalogid.' - '.$nalog_naziv.'</a></p>';
	$nestedData[] = '<p class="text-center"><span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$kg_title.'</span></p>';
	if($row['status_id'] == 0){
		$nestedData[] = '<p class="text-center"><span class="label label-warning material-label material-label_warning material-label_xs main-container__column">'.$kandidat_status.$bot.'</span></p>';
	}elseif($row['status_id'] == 1){
		$nestedData[] = '<p class="text-center"><span class="label label-primary material-label material-label_primary material-label_xs main-container__column">'.$kandidat_status.$bot.'</span></p>';
	}elseif($row['status_id'] == 2){
		$nestedData[] = '<p class="text-center"><span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$kandidat_status.$bot.'</span></p>';
	}elseif($row['status_id'] == 3){
		$nestedData[] = '<p class="text-center"><span class="label label-danger material-label material-label_danger material-label_xs main-container__column">'.$kandidat_status.$bot.'</span></p>';
	}elseif($row['status_id'] == 4){
		$nestedData[] = '<p class="text-center"><span class="label label-info material-label material-label_info material-label_xs main-container__column">'.$kandidat_status.$bot.'</span></p>';
	}elseif($row['status_id'] == 5){
		$nestedData[] = '<p class="text-center"><span class="label label-info material-label material-label_primary material-label_xs main-container__column">'.$kandidat_status.$bot.'</span></p>';
	}elseif($row['status_id'] == 6){
		$nestedData[] = '<p class="text-center"><span class="label label-danger material-label material-label_danger material-label_xs main-container__column">'.$kandidat_status.'</span></p>';
	}elseif($row['status_id'] == 7){
		$nestedData[] = '<p class="text-center"><span class="label label-danger material-label material-label_yellow material-label_xs main-container__column">'.$kandidat_status.'</span></p>';
	}elseif($row['status_id'] == 8){
		$nestedData[] = '<p class="text-center"><span class="label label-danger material-label material-label_yellow material-label_xs main-container__column">'.$kandidat_status.'</span></p>';
	}
	$nestedData[] = '
		<div class="btn-group material-btn-group">
			<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
			<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
				<li><a href="'.$getSiteUrl.'kandidati?page=open&id='.$kandidat_id.'" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>

				<li><a href="#" class="material-dropdown-menu__link cvshow" data="'.$getSiteUrl.$cv_filepath.'" data-id="'.$getSiteUrl.$cv_filepath_de.'" data-toggle="modal" data-target="#cvModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> CV</a></li>


				<li><a href="#" class="material-dropdown-menu__link profilshow" data="'.$getSiteUrl.$profil_filepath.'" data-id="'.$getSiteUrl.$profil_filepath_de.'" data-toggle="modal" data-target="#profilModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Profil</a></li>


				<li class="idk_dropdown_danger"><a href="#" data="'.$getSiteUrl.'kandidati?page=archive&id='.$kandidat_id.'" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>


			</ul>
		</div>
	';



	$data[] = $nestedData;

}



$json_data = array(
			"draw"            => intval( $requestData['draw'] ),
			"recordsTotal"    => intval( $totalData ),
			"recordsFiltered" => intval( $totalFiltered ),
			"data"            => $data
			);

echo json_encode($json_data);


?>
