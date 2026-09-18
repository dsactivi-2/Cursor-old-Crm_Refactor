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
	$archive_status = $_REQUEST['archive_status'];
	$drzava_kan_fil = $_REQUEST['drzava_kan_fil'];
	$starost_od_kan_fil = $_REQUEST['starost_od_kan_fil'];
	$starost_do_kan_fil = $_REQUEST['starost_do_kan_fil'];
	$vozacka_dozvola_kan_fil = $_REQUEST['vozacka_dozvola_kan_fil'];
	$radno_iskustvo_kan_fil = $_REQUEST['radno_iskustvo_kan_fil'];
	$znanje_njemacki_kan_fil = $_REQUEST['znanje_njemacki_kan_fil'];
	$grupe_kan_fil = $_REQUEST['grupe_kan_fil'];
	$drzavljanstvo_kan_fil = $_REQUEST['drzavljanstvo_kan_fil'];
	$termina_od_kan_fil = $_REQUEST['termina_od_kan_fil'];
	$termina_do_kan_fil = $_REQUEST['termina_do_kan_fil'];
	$boravak_kan_fil = $_REQUEST['boravak_kan_fil'];
	$skole_kan_fil = $_REQUEST['skole_kan_fil'];
	$smjerovi_kan_fil = $_REQUEST['smjerovi_kan_fil'];

	$columns = array(
		0 => 'kandidat_id',
		1 => 'kandidat_ime',
		2 => 'kandidat_prezime',
		3 => 'kandidat_adresa',
		4 => 'kandidat_grad',
		5 => 'kandidat_pbroj',
		6 => 'kandidat_drzava'
	);
	
	//***************************************
	//    *****  *****   ***   ****   *****
	//   *         *    *   *  *   *    *
	//    ****     *    *****  ****     *
	//        *    *    *   *  *   *    *
	//   *****     *    *   *  *   *    *
	//***************************************
	//-----------------USLOVI----------------
	//***************************************
	//$sql = "";
	//$glavniQuery = "";
	$getSiteUrl = getSiteURLr();
	//---------------------------------------
	
	$archiveUslov = "";
	if($archive_status == 0){
		$archiveUslov = "kandidat_status != 3";
	}else{
		$archiveUslov = "kandidat_status = 3";
	}
	
	//---------------------------------------
	
	// $glavniQuery = "
		// SELECT *
		// FROM idk_kandidati 
		// WHERE kandidat_id is not null
	// ";
	
	//---------------------------------------
	
	$sql = "
		SELECT *
		FROM idk_kandidati 
	";
	
	//---------------------------------------
	
	$query = mysqli_query($conn, $sql) or die();
	$totalData = mysqli_num_rows($query);
	$totalFiltered = $totalData;
	
	//---------------------------------------
	
	// if( !empty($requestData['search']['value']) ) {
		
		// $sql.=" AND CONCAT(kandidat_ime,' ',kandidat_prezime) LIKE '%".$requestData['search']['value']."%' ";
		// $sql.=" OR CONCAT(kandidat_ime,kandidat_prezime) LIKE '%".$requestData['search']['value']."%' ";
		// $sql.=" OR CONCAT(kandidat_prezime,' ',kandidat_ime) LIKE '%".$requestData['search']['value']."%' ";
		// $sql.=" OR CONCAT(kandidat_prezime,kandidat_ime) LIKE '%".$requestData['search']['value']."%' ";
		// $sql.=" OR kandidat_ime LIKE '%".$requestData['search']['value']."%' ";
		// $sql.=" OR kandidat_prezime LIKE '%".$requestData['search']['value']."%' ";
		// $sql.=" OR status_naziv LIKE '%".$requestData['search']['value']."%' ";
		// $sql.=" OR kandidat_email LIKE '%".$requestData['search']['value']."%' ";
		// $sql.=" OR kandidat_mobitel LIKE '%".$requestData['search']['value']."%' ";
		// if($requestData['search']['value'] == "partner"){
			// $sql.=" OR kandidat_porijeklo IN (6,7) ";
		// }
	// }
	
	//---------------------------------------
	
	// $query = mysqli_query($conn, $sql) or die();
	// $totalFiltered = mysqli_num_rows($query);
	
	//---------------------------------------
	
	$sql .= " ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']." ";
	
	//---------------------------------------
	
	$query = mysqli_query($conn, $sql) or die();
	$totalData = mysqli_num_rows($query);
	// $totalFiltered = $totalData;
	
	
	//***************************************
	//-----------------USLOVI----------------
	//***************************************
	//    *****  *   *  ****
	//    *      **  *  *   *
	//    ****   * * *  *   *
	//    *      *  **  *   *
	//    *****  *   *  ****
	//***************************************
	
	$data = array();

	while( $row = mysqli_fetch_array( $query ) ) {
		
		//---------------------------------------
		$kandidat_id = $row['kandidat_id'];
		$kandidat_ime = $row['kandidat_ime'];
		$kandidat_prezime = $row['kandidat_prezime'];
		$kandidat_datumrodjenja = $row['kandidat_adresa'];
		$kandidat_spol = $row['kandidat_grad'];
		$kandidat_jmbg = $row['kandidat_pbroj'];
		$kandidat_slika = $row['kandidat_drzava'];
		$kandidat_group = $row['kandidat_email'];
		$kandidat_email = $row['kandidat_mobitel'];
		
		
		$nestedData=array();

		$nestedData[] = '<p class="text-center">'.$kandidat_id.'</p>';
		$nestedData[] = '<p class="text-center">'.$kandidat_ime.'</p>';
		$nestedData[] = '<p class="text-center">'.$kandidat_prezime.'</p>';
		$nestedData[] = '<p class="text-center">'.$kandidat_datumrodjenja.'</p>';
		$nestedData[] = '<p class="text-center">'.$kandidat_spol.'</p>';
		$nestedData[] = '<p class="text-center">'.$kandidat_jmbg.'</p>';
		$nestedData[] = '<p class="text-center">'.$kandidat_slika.'</p>';
		$nestedData[] = '<p class="text-center">'.$kandidat_group.'</p>';
		$nestedData[] = '<p class="text-center">'.$kandidat_email.'</p>';
		$nestedData[] = '<p class="text-center">'.$kandidat_email.'</p>';
		
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
