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
$uslov_vozacka = $_REQUEST['uslov_vozacka'];
$nalog_id = $_REQUEST['nalog_id'];
$njem_uslov = $_REQUEST['njem_uslov'];
$njem_uslov_niz = explode(',',$njem_uslov);

$columns = array(
	0 => 'kandidat_id',
	1 => 'kandidat_slika',
	2 => 'kandidat_ime',
	3 => 'cv_de',
    4 => 'nalog_naziv',
    5 => 'kg_title',
    6 => 'kandidat_status'
);

$sql = "
	SELECT 
	kandidat_id, 
	kandidat_ime, 
	kandidat_prezime, 
	kandidat_status, 
	kandidat_slika, 
	kandidat_email, 
	kandidat_visitedurl, 
	kandidat_prijava_na, 
	kandidat_group, 
	kandidat_vozacka_dozvola,
	kandidat_status_messenger,
	idkg.kg_title,
	idklg.lg_id, 
	idklg.lg_url, 
	idklg.lg_nalogid,
	idnalog.nalog_id, 
	idnalog.nalog_naziv,
	idks.status_id,
	idks.status_naziv,
	ikj.kj_slusanje,
    (SELECT 
		CASE WHEN EXISTS ( SELECT bo_poslana FROM idk_bot_obavijesti WHERE bo_kandidat_id = kandidat_id AND bo_nalog_id = ".$nalog_id.")
        THEN 1
        ELSE 0
    END ) as poslana
	FROM  idk_kandidati 
	INNER JOIN idk_kandidati_grupe idkg 
		ON kandidat_group = idkg.kg_id 
	INNER JOIN idk_link_generator idklg 
		ON kandidat_visitedurl = idklg.lg_id 
	LEFT JOIN idk_nalozi idnalog 
		ON idklg.lg_nalogid = idnalog.nalog_id 
	INNER JOIN idk_kandidat_status idks 
		ON kandidat_status = idks.status_id 
	INNER JOIN idk_kandidat_jezici ikj
    	ON kandidat_id = ikj.kj_kandidatid
	WHERE  kandidat_status != 3  
	AND ikj.kj_naziv = 'Njemački'
	AND (kandidat_status_prijave != '4' OR kandidat_status_prijave is NULL) 
	AND kandidat_status_messenger = 2
	AND kandidat_status = 2
	".$uslovi." GROUP BY kandidat_id" ;
$query=mysqli_query($conn, $sql) or die();
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData; 

$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
$query=mysqli_query($conn, $sql) or die();

$data = array();
while( $row=mysqli_fetch_array($query) ) {

	$kandidat_id = $row['kandidat_id'];
	$kandidat_ime = $row['kandidat_ime'];
	$kandidat_prezime = $row['kandidat_prezime'];
	$kandidat_status = $row['kandidat_status'];
	$kandidat_email = $row['kandidat_email'];
	$kandidat_visitedurl = $row['kandidat_visitedurl'];
	$kandidat_prijava_na = $row['kandidat_prijava_na'];
	$kandidat_group = $row['kandidat_group'];
	$kandidat_vozacka_dozvola = $row['kandidat_vozacka_dozvola'];
	$kg_title = $row['kg_title'];
	$lg_id = $row['lg_id'];
	$lg_url = $row['lg_url'];
	$lg_nalogid = $row['lg_nalogid'];
	$nalog_naziv = $row['nalog_naziv'];
	$kandidat_status = $row['status_naziv'];
	$kandidat_status_messenger = $row['kandidat_status_messenger'];
	$njem_jezik = $row['kj_slusanje'];
	$poslana = $row['poslana'];
	$getSiteUrl = getSiteURLr();
	
	if($kandidat_vozacka_dozvola == "Da"){
		$vozacka_ispis = '<span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$kandidat_vozacka_dozvola.'</span>';
	}else{
		$vozacka_ispis = '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">'.$kandidat_vozacka_dozvola.'</span>';
	}
	
	if(in_array($njem_jezik, $njem_uslov_niz)){
		$njem_ispis = '<span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$njem_jezik.'</span>';
	}else{
		$njem_ispis = '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">'.$njem_jezik.'</span>';
	}
	
	if($kandidat_status_messenger == 2)
		$bot = ' <i class="fa fa-commenting" style="color: <?php echo $style_bot?> font-size: 1.5em;" aria-hidden="true" title = "Kandidat se logirao na aplikaciju Messenger."></i>';
	else if($kandidat_status_messenger == 1)
		$bot = ' <i class="fa fa-commenting" style="color: <?php echo $style_bot?> font-size: 1.5em;" aria-hidden="true" title = "Kandidat se čeka za instalaciju Messengera."></i>';
	else
		$bot = "";
	
	if($nalog_naziv != ""){
		if (strlen($nalog_naziv) > 45){
			$nalog_puni_naziv = $nalog_naziv;
			$nalog_naziv = substr($nalog_naziv, 0, 45);
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
		
	// SLIKA
	if($row['kandidat_slika'] == "none"){
		$kandidat_slika = "nonekandidati.jpg";
	}else{
		$kandidat_slika = $row['kandidat_slika'];
	}
	
	
	$kg_grupa = "grupa";
	$status = "status";
	$nestedData=array();
	
	$nestedData[] = '<p class="text-center"><input class="checkbox" type="checkbox" name="selectedrows_s['.$kandidat_id.']" value="'.$kandidat_id.'"></p>';
	$nestedData[] = '<p class="text-center">'.$kandidat_id.'</p>';
	$nestedData[] = '<p class="text-center"><a href="'.$getSiteUrl.'kandidati?page=open&id='.$kandidat_id.'"><img class="idk_profile_img" src="'.$getSiteUrl.'files/kandidati/'.$kandidat_slika.'"></a></p>';
	$nestedData[] = '<p class="text-left"><a href="'.$getSiteUrl.'kandidati?page=open&id='.$kandidat_id.'">'.$kandidat_ime.' '.$kandidat_prezime.'</a></p>';
	$nestedData[] = '<p class="text-left"><span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$lg_url.'</span></p>';
	$nestedData[] = '<p class="text-left"><a href="'.$getSiteUrl.'nalozi?page=open&id='.$lg_nalogid.'" target="_BLANK" class="label label-'.$nalog_boja.' material-label material-label_'.$nalog_boja.' material-label_xs main-container__column" title="'.$nalog_puni_naziv.'">'. $lg_nalogid.' - '.$nalog_naziv.'</a></p>';
	$nestedData[] = '<p class="text-center"><span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$kg_title.'</span></p>';
	$nestedData[] = '<p class="text-center">'.$njem_ispis.'</p>';
	$nestedData[] = '<p class="text-center">'.$vozacka_ispis.'</p>';
	$nestedData[] = '<p class="text-center">'.$poslana.'</p>';
	
	$data[] = $nestedData;
	//}
}

$json_data = array(
			"draw"            => intval( $requestData['draw'] ),
			"recordsTotal"    => intval( $totalData ),
			"recordsFiltered" => intval( $totalFiltered ),
			"data"            => $data
			);

echo json_encode($json_data);
