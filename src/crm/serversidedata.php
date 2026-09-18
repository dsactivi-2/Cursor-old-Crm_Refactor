<?php
include("includes/functions.php");
require_once("includes/env.php");
$page = $_REQUEST['page'];
switch($page)
{
	case "lista_kandidata":

	/* Database connection start */
  // $servername = $envConfig->DB_HOST;
	// $username = $envConfig->DB_USER;
	// $password = $envConfig->DB_PASSWORD;
	// $dbname = $envConfig->DB_DATABASE;


	// $conn = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());
	// $conn->query('set character_set_client=utf8mb4');
	// $conn->query('set character_set_connection=utf8mb4');
	// $conn->query('set character_set_results=utf8mb4');
	// $conn->query('set character_set_server=utf8mb4');
	/* Database connection end */

	$requestData = $_REQUEST;
	$starost_od = $_REQUEST['starost_od'];
	$starost_do = $_REQUEST['starost_do'];
	$vozacka_dozvola = $_REQUEST['vozacka_dozvola'];
	$radno_iskustvo = $_REQUEST['radno_iskustvo'];
	$znanje_njemacki = $_REQUEST['znanje_njemacki'];
	$znanje_engleski = $_REQUEST['znanje_engleski'];
	$filter_grupeImp = $_REQUEST['filter_grupe'];
	$filter_statusImp = $_REQUEST['filter_status'];
	$filter_status_prijaveImp = $_REQUEST['filter_status_prijave'];
	$filter_drzavljanstvoImp = $_REQUEST['filter_drzavljanstvo'];
	$filter_boravakImp = $_REQUEST['filter_boravak'];
	$filter_skoleImp = $_REQUEST['filter_skole'];
	$filter_smjerImp = $_REQUEST['filter_smjer'];
	$filter_izvorImp = $_REQUEST['filter_izvor'];
	$filter_kategorija_vozackeImp = $_REQUEST['filter_kategorija_vozacke'];
	$filter_struke = $_REQUEST['filter_struke'];
	$filter_dipl_statusImp = $_REQUEST['filter_dipl_status'];
	$filter_vrsta_nostrifikacijeImp = $_REQUEST['filter_vrsta_nostrifikacije'];

	$filter_grupeExp = explode(",", $filter_grupeImp);
	$filter_statusExp = explode(",", $filter_statusImp);

	if($filter_dipl_statusImp != null){
		$filter_dipl_statusExp = explode(",", $filter_dipl_statusImp);
	} else {
		$filter_dipl_statusExp = null;
	}
	if($filter_vrsta_nostrifikacijeImp != null){
		$filter_vrsta_nostrifikacijeExp = explode(",", $filter_vrsta_nostrifikacijeImp);
	} else {
		$filter_vrsta_nostrifikacijeExp = null;
	}
	if($filter_status_prijaveImp != null){
		$filter_status_prijaveExp = explode(",", $filter_status_prijaveImp);
	} else {
		$filter_status_prijaveExp = null;
	}
	if($filter_skoleImp != null){
		$filter_skoleExp = explode(",", $filter_skoleImp);
	} else {
		$filter_skoleExp = null;
	}
	if($filter_smjerImp != null){
		$filter_smjerExp = explode(",", $filter_smjerImp);
	} else {
		$filter_smjerExp = null;
	}
	if($filter_struke != null){
		$filter_strukeExp = explode(",", $filter_struke);
	} else {
		$filter_strukeExp = null;
	}
	if($filter_drzavljanstvoImp == null){
		$filter_drzavljanstvoExp = null;
	} else {
		$filter_drzavljanstvoExp = explode(",", $filter_drzavljanstvoImp);
	}
	if($filter_kategorija_vozackeImp != null){
		$filter_kategorija_vozackeExp = explode(",", $filter_kategorija_vozackeImp);
	} else {
		$filter_kategorija_vozackeExp = null;
	}
	if($filter_izvorImp != null){
		$filter_izvorExp = explode(",", $filter_izvorImp);
	} else {
		$filter_izvorExp = array();
	}
	$columns = array(
		0 => 'kandidat_id',
		1 => 'kandidat_slika',
		3 => 'kandidat_ime',
		4 => 'kandidat_datetime',
		5 => 'kandidat_porijeklo',
		6 => 'cv_de',
		7 => 'nalog_naziv',
		8 => 'kg_title',
		9 => 'kandidat_status'
	);

	if(isset($_COOKIE['archive_status'])){
		$archivestatus = 1;
	}else{
		$archivestatus = 0;
	}
	//STAROST KANDIDATA
	if($starost_od != null && $starost_do != null){
		$starost_uslov = " (YEAR(NOW()) - YEAR(`kandidat_datumrodjenja`)) BETWEEN ".$starost_od." AND ".$starost_do."";
		
	} else {
		$starost_uslov = 1;
	}

	//VOZACKA DOZVOLA
	if($vozacka_dozvola == "DA"){
		$vozacka_uslov = " kandidat_vozacka_dozvola LIKE '%Da%'";
	} else {
		$vozacka_uslov = 1;
	}

	//RADNO ISKUSTVO
	if($radno_iskustvo == "DA"){
		$radno_iskustvo_uslov = " kandidat_id IN (SELECT kri_kandidat_id FROM `idk_kandidat_radno_iskustvo` GROUP BY kri_kandidat_id)";
	}else {
		$radno_iskustvo_uslov = 1;
	}

	//NIVO NJEMACKOG JEZIKA
	if($znanje_njemacki == "A1"){
		$njemacki_uslov = " kandidat_id IN (SELECT kj_kandidatid FROM `idk_kandidat_jezici` WHERE kj_naziv LIKE '%Njemacki%' AND (kj_slusanje LIKE 'A1'  OR kj_slusanje LIKE 'A2' OR kj_slusanje LIKE 'B1' OR kj_slusanje LIKE 'B1' OR kj_slusanje LIKE 'B2' OR kj_slusanje LIKE 'C1' OR kj_slusanje LIKE 'C2'))";
	}
	elseif($znanje_njemacki == "A2"){
		$njemacki_uslov = " kandidat_id IN (SELECT kj_kandidatid FROM `idk_kandidat_jezici` WHERE kj_naziv LIKE '%Njemacki%' AND (kj_slusanje LIKE 'A2'  OR kj_slusanje LIKE 'B1' OR kj_slusanje LIKE 'B2' OR kj_slusanje LIKE 'C1' OR kj_slusanje LIKE 'C2'))";
	}
	elseif($znanje_njemacki == "B1"){
		$njemacki_uslov = " kandidat_id IN (SELECT kj_kandidatid FROM `idk_kandidat_jezici` WHERE kj_naziv LIKE '%Njemacki%' AND (kj_slusanje LIKE 'B1'  OR kj_slusanje LIKE 'B2' OR kj_slusanje LIKE 'C1' OR kj_slusanje LIKE 'C2'))";
	}
	elseif($znanje_njemacki == "B2"){
		$njemacki_uslov = " kandidat_id IN (SELECT kj_kandidatid FROM `idk_kandidat_jezici` WHERE kj_naziv LIKE '%Njemacki%' AND (kj_slusanje LIKE 'B2'  OR kj_slusanje LIKE 'C1' OR kj_slusanje LIKE 'C2'))";
	}
	elseif($znanje_njemacki == "C1"){
		$njemacki_uslov = " kandidat_id IN (SELECT kj_kandidatid FROM `idk_kandidat_jezici` WHERE kj_naziv LIKE '%Njemacki%' AND (kj_slusanje LIKE 'C1'  OR kj_slusanje LIKE 'C2'))";
	}
	elseif($znanje_njemacki == "C2"){
		$njemacki_uslov = " kandidat_id IN (SELECT kj_kandidatid FROM `idk_kandidat_jezici` WHERE kj_naziv LIKE '%Njemacki%' AND (kj_slusanje LIKE 'C2'))";
	} 
	elseif($znanje_njemacki == "Bezznanja") {
		$njemacki_uslov = " kandidat_id IN (SELECT kj_kandidatid FROM `idk_kandidat_jezici` WHERE kj_naziv LIKE '%Njemacki%' AND (kj_slusanje LIKE 'BEZ ZNANJA'))";
	}
	elseif($znanje_njemacki == "nemainfo"){
		//$njemacki_uslov = " kandidat_id IN (SELECT kj_kandidatid FROM `idk_kandidat_jezici` WHERE kj_naziv LIKE '%Njemacki%' AND (kj_slusanje LIKE ''))";
		$njemacki_uslov = "
			kandidat_id IN (
				SELECT 
					kan1.kandidat_id
				FROM 
					idk_kandidati kan1 
				WHERE 
					kan1.kandidat_id NOT IN (
						SELECT 
							kan.kandidat_id
						FROM 
							idk_kandidati kan 
						JOIN
							idk_kandidat_jezici kj
						ON
							kan.kandidat_id = kj.kj_kandidatid 
						WHERE 
							(
								kj.kj_slusanje LIKE 'A1'  OR kj.kj_slusanje LIKE 'A2' OR kj.kj_slusanje LIKE 'B1' OR kj.kj_slusanje LIKE 'B2' OR kj.kj_slusanje LIKE  'C1' OR kj.kj_slusanje LIKE 'C2' OR kj.kj_slusanje LIKE 'BEZ ZNANJA'
							)
							AND 
							kj.kj_naziv LIKE '%Njemacki%'
					)
			)
		";
	}else {
		$njemacki_uslov = 1;
	}
	//Nivo Engleskog jezika
	if($znanje_engleski == "A1") {
		$engleski_uslov = " kandidat_id IN (SELECT kj_kandidatid FROM `idk_kandidat_jezici` WHERE kj_naziv LIKE '%Engleski%' AND (kj_slusanje LIKE 'A1'  OR kj_slusanje LIKE 'A2' OR kj_slusanje LIKE 'B1' OR kj_slusanje LIKE 'B1' OR kj_slusanje LIKE 'B2' OR kj_slusanje LIKE 'C1' OR kj_slusanje LIKE 'C2'))";
	}
	elseif($znanje_engleski == "A2"){
		$engleski_uslov = " kandidat_id IN (SELECT kj_kandidatid FROM `idk_kandidat_jezici` WHERE kj_naziv LIKE '%Engleski%' AND (kj_slusanje LIKE 'A2'  OR kj_slusanje LIKE 'B1' OR kj_slusanje LIKE 'B2' OR kj_slusanje LIKE 'C1' OR kj_slusanje LIKE 'C2'))";
	}
	elseif($znanje_engleski == "B1"){
		$engleski_uslov = " kandidat_id IN (SELECT kj_kandidatid FROM `idk_kandidat_jezici` WHERE kj_naziv LIKE '%Engleski%' AND (kj_slusanje LIKE 'B1'  OR kj_slusanje LIKE 'B2' OR kj_slusanje LIKE 'C1' OR kj_slusanje LIKE 'C2'))";
	}
	elseif($znanje_engleski == "B2"){
		$engleski_uslov = " kandidat_id IN (SELECT kj_kandidatid FROM `idk_kandidat_jezici` WHERE kj_naziv LIKE '%Engleski%' AND (kj_slusanje LIKE 'B2'  OR kj_slusanje LIKE 'C1' OR kj_slusanje LIKE 'C2'))";
	}
	elseif($znanje_engleski == "C1"){
		$engleski_uslov = " kandidat_id IN (SELECT kj_kandidatid FROM `idk_kandidat_jezici` WHERE kj_naziv LIKE '%Engleski%' AND (kj_slusanje LIKE 'C1'  OR kj_slusanje LIKE 'C2'))";
	}
	elseif($znanje_engleski == "C2"){
		$engleski_uslov = " kandidat_id IN (SELECT kj_kandidatid FROM `idk_kandidat_jezici` WHERE kj_naziv LIKE '%Engleski%' AND (kj_slusanje LIKE 'C2'))";
	} 
	elseif($znanje_engleski == "Bezznanja") {
		$engleski_uslov = " kandidat_id IN (SELECT kj_kandidatid FROM `idk_kandidat_jezici` WHERE kj_naziv LIKE '%Engleski%' AND (kj_slusanje LIKE 'BEZ ZNANJA'))";
	}
	elseif($znanje_engleski == "nemainfo"){
		//$engleski_uslov = " kandidat_id IN (SELECT kj_kandidatid FROM `idk_kandidat_jezici` WHERE kj_naziv LIKE '%Engleski%' AND (kj_slusanje LIKE ''))";
		$engleski_uslov = "
			kandidat_id IN (
				SELECT 
					kan2.kandidat_id
				FROM 
					idk_kandidati kan2 
				WHERE 
					kan2.kandidat_id NOT IN (
						SELECT 
							kan.kandidat_id
						FROM 
							idk_kandidati kan 
						JOIN
							idk_kandidat_jezici kj
						ON
							kan.kandidat_id = kj.kj_kandidatid 
						WHERE 
							(
								kj.kj_slusanje LIKE 'A1'  OR kj.kj_slusanje LIKE 'A2' OR kj.kj_slusanje LIKE 'B1' OR kj.kj_slusanje LIKE 'B2' OR kj.kj_slusanje LIKE  'C1' OR kj.kj_slusanje LIKE 'C2' OR kj.kj_slusanje LIKE 'BEZ ZNANJA'
							)
							AND 
							kj.kj_naziv LIKE '%Engleski%'
					)
			)
		";
	}else {
		$engleski_uslov = 1;
	}

	//KANDIDAT GROUP
	if($filter_grupeImp != null){
		$grupa_uslov = " kandidat_group IN (" . $filter_grupeImp . ")";
	} else {
		$grupa_uslov = 1;
	}

	//KANDIDAT STATUS
	if($filter_statusImp != null) {
		$status_uslov = " kandidat_status IN (" . $filter_statusImp . ")";
	} else {
		$status_uslov = 1;
	}

	//KANDIDAT STATUS PRIJAVE
	if($filter_status_prijaveImp != null) {
		if(in_array(0, $filter_status_prijaveExp))
		{
			$status_prijave_uslov = " (kandidat_status_prijave IN (" . $filter_status_prijaveImp . ") OR kandidat_status_prijave IS NULL)";
		}
		else
		{
			$status_prijave_uslov = " kandidat_status_prijave IN (" . $filter_status_prijaveImp . ")";
		}
	} else {
		$status_prijave_uslov = 1;
	}

	//KANDIDAT DRZAVLJANSTVO
	if(count($filter_drzavljanstvoExp) == 1){
		if($filter_drzavljanstvoExp[0] == "EU"){
			$drzavljanstvo_uslov = " kandidat_drzavljanstvo_vrsta LIKE 'EU%'";
		} else {
			$drzavljanstvo_uslov = " kandidat_drzavljanstvo_vrsta NOT LIKE 'EU%' OR kandidat_drzavljanstvo_vrsta IS NULL"; 
		}
	} else {
		$drzavljanstvo_uslov = " kandidat_id IS NOT null";
	}

	//KANDIDAT BORAVAK
	if($filter_boravakImp != null){
		$boravak_uslov = " boravak_eu LIKE '".$filter_boravakImp."'";
	} else {
		$boravak_uslov = 1;
	}

	//KANDIDAT SKOLA
	if($filter_skoleExp != null && $filter_smjerExp == null){
		$kandidat_id_skola = array();
		for($i = 0; $i < count($filter_skoleExp); $i++){
			$stmt = $db->prepare("SELECT ke_kandidat_id FROM idk_kandidat_edukacija WHERE ke_naziv LIKE '%".$filter_skoleExp[$i]."%'");
			$stmt->execute();
			$result = $stmt->fetchAll();
			
			foreach($result as $kandidat_skola){
				$id_kandidata = $kandidat_skola['ke_kandidat_id'];
				$kandidat_id_skola[] = $id_kandidata;
			}
		}
		$rezultat_skole = array_unique($kandidat_id_skola);
		$rezultat_skoleImp = implode(",", $rezultat_skole);
		$skole_uslov = " kandidat_id IN (" . $rezultat_skoleImp . ")";
	} else {
		$skole_uslov = 1;
	}

	//KANDIDAT SMJER
	if($filter_smjerExp != null){
		$kandidat_id_smjer = array();
		for($i = 0; $i < count($filter_smjerExp); $i++){
			$stmt2 = $db->prepare("SELECT ke_kandidat_id FROM idk_kandidat_edukacija WHERE ke_naziv_kvalifikacije LIKE '".$filter_smjerExp[$i]."'");
			$stmt2->execute();
			$result2 = $stmt2->fetchAll();
			
			foreach($result2 as $kandidat_smjer){
				$id_kandidata_smjer = $kandidat_smjer['ke_kandidat_id'];
				$kandidat_id_smjer[] = $id_kandidata_smjer;
			}
		}
		$rezultat_smjera = array_unique($kandidat_id_smjer);
		$rezultat_smjeraImp = implode(",", $rezultat_smjera);
		$smjer_uslov = " kandidat_id IN (" . $rezultat_smjeraImp . ")"; 
	} else {
		$smjer_uslov = 1;
	}

	//KANDIDAT STRUKA
	if($filter_strukeExp != NULL && $filter_smjerExp == NULL)
	{
		$smjerovi = [];
		$kandidati = [];
		$query_struke_smjer = $db->prepare("SELECT ss_naziv FROM idk_skole_smjerovi WHERE ss_struka_id IN (".$filter_struke.")");
		$query_struke_smjer->execute();

		while($row = $query_struke_smjer->fetch())
		{
			$smjerovi[] = $row['ss_naziv'];
		}

		for($i = 0; $i < count($smjerovi); $i++){
			$stmt2 = $db->prepare("SELECT ke_kandidat_id FROM idk_kandidat_edukacija WHERE ke_naziv_kvalifikacije LIKE '".$smjerovi[$i]."'");
			$stmt2->execute();
			$result2 = $stmt2->fetchAll();
			
			foreach($result2 as $kandidat_smjer){
				$id_kandidata_smjer = $kandidat_smjer['ke_kandidat_id'];
				$kandidati[] = $id_kandidata_smjer;
			}
		}
		$rezultat_struke = array_unique($kandidati);
		$rezultat_strukeImp = implode(",", $rezultat_struke);
		$struka_uslov = " kandidat_id IN (" . $rezultat_strukeImp . ")"; 
	}
	else
	{
		$struka_uslov = 1;
	}
	
	//KANDIDAT IZVOR
	if($filter_izvorExp > 0){
		if(in_array(6, $filter_izvorExp)){
			$filter_izvorExp[] = 7;
		}
	}
	$izvorImp = implode(",", $filter_izvorExp);
	if($izvorImp != null){
		$izvor_uslov = " kandidat_porijeklo IN (".$izvorImp.")";
	} else {
		$izvor_uslov = 1;
	}
	
	//KANDIDAT KATEGORIJA VOZACKE
	$niz_kategorija = array();
	if($filter_kategorija_vozackeExp != null){
		$najveci = null;
		if(in_array("B", $filter_kategorija_vozackeExp)){
			$najveci = "B";
		}
		else if(in_array("C1", $filter_kategorija_vozackeExp)){
			$najveci = "C1";
		}
		else if(in_array("C", $filter_kategorija_vozackeExp)){
			$najveci = "C";
		}
		else if(in_array("C1E", $filter_kategorija_vozackeExp)){
			$najveci = "C1E";
		}
		else if(in_array("CE", $filter_kategorija_vozackeExp)){
			$najveci = "CE";
		}
		if($najveci == "B"){
			$niz_kategorija[] = "CE";
			$niz_kategorija[] = "C1E";
			$niz_kategorija[] = "C";
			$niz_kategorija[] = "C1";
			$niz_kategorija[] = "B";
		}
		else if($najveci == "C1"){
			$niz_kategorija[] = "CE";
			$niz_kategorija[] = "C1E";
			$niz_kategorija[] = "C";
			$niz_kategorija[] = "C1";
		}
		else if($najveci == "C"){
			$niz_kategorija[] = "CE";
			$niz_kategorija[] = "C1E";
			$niz_kategorija[] = "C";
		}
		else if($najveci == "C1E"){
			$niz_kategorija[] = "CE";
			$niz_kategorija[] = "C1E";
		}
		else if($najveci == "CE"){
			$niz_kategorija[] = "CE";
		}
		
		if(in_array("BE", $filter_kategorija_vozackeExp)){
			$niz_kategorija[] = "BE";
		}
	}

	//KANDIDAT STATUS DIPL
	if($filter_dipl_statusImp != null) {
		if(in_array(0, $filter_dipl_statusExp)){
			$dipl_status_uslov = " (kandidat_dipl_id = 0 OR ndk.status_nd_kandidata IN ($filter_dipl_statusImp)) ";
        }else{
            $dipl_status_uslov = " ndk.status_nd_kandidata IN ($filter_dipl_statusImp) ";
        }
		$dipl_status_join = " LEFT JOIN idk_nd_kandidata ndk ON kandidat_dipl_id = ndk.id_broj_nd_kandidata ";
	} else {
		$dipl_status_uslov = 1;
		$dipl_status_join = '';
	}

	//VRSTA NOSTRIFIKACIJE
	if($filter_vrsta_nostrifikacijeImp != null){
		if(in_array('3', $filter_vrsta_nostrifikacijeExp)){
            $vrsta_nostrifikacije_uslov = " (nodi.full_recognition IS NULL OR nodi.full_recognition IN ($filter_vrsta_nostrifikacijeImp)) ";
        }else{
            $vrsta_nostrifikacije_uslov = " nodi.full_recognition IN ($filter_vrsta_nostrifikacijeImp) ";
        }
		$vrsta_nostrifikacije_join = " LEFT JOIN idk_nostrifikovane_diplome nodi ON kandidat_dipl_id = nodi.id_cand_dipl ";
	}else{
		$vrsta_nostrifikacije_uslov = 1;
		$vrsta_nostrifikacije_join = "";
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
		   kandidat_mobitel,
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
		   LEFT JOIN idk_link_generator idklg 
				   ON kandidat_visitedurl = idklg.lg_id 
		   LEFT JOIN idk_nalozi idnalog 
				   ON idklg.lg_nalogid = idnalog.nalog_id 			   
		   INNER JOIN idk_kandidat_status idks 
				   ON kandidat_status = idks.status_id
		   ". $dipl_status_join ."
		   ". $vrsta_nostrifikacije_join ."
		WHERE  kandidat_status != 3 
			   AND
			   ".$vozacka_uslov."
			   AND
			   ".$radno_iskustvo_uslov."
			   AND
			   ".$njemacki_uslov."
			   AND 
			   ".$engleski_uslov."
			   AND
			   ".$grupa_uslov."
			   AND
			   ".$status_uslov."
			   AND
			   ".$drzavljanstvo_uslov."
			   AND
			   ".$boravak_uslov."
			   AND
			   ".$skole_uslov."
			   AND
			   ".$smjer_uslov."
			   AND
			   ".$starost_uslov."
			   AND
			   ".$izvor_uslov."
			   AND
			   ".$struka_uslov."
			   AND
			   ".$status_prijave_uslov."
			   AND
			   ".$dipl_status_uslov."
			   AND
			   ".$vrsta_nostrifikacije_uslov."
		" ;
	$count_sql = "SELECT 
			count(kandidat_id) as broj
				FROM  idk_kandidati 
			INNER JOIN idk_kandidati_grupe idkg 
					ON kandidat_group = idkg.kg_id 
			LEFT JOIN idk_link_generator idklg 
					ON kandidat_visitedurl = idklg.lg_id 
			LEFT JOIN idk_nalozi idnalog 
					ON idklg.lg_nalogid = idnalog.nalog_id 			   
			INNER JOIN idk_kandidat_status idks 
					ON kandidat_status = idks.status_id
			" . $dipl_status_join . "
			" . $vrsta_nostrifikacije_join . "
			WHERE  
				kandidat_status != 3 
			AND	
				".$vozacka_uslov." 
			AND 
				".$radno_iskustvo_uslov." 
			AND 
				".$njemacki_uslov."
			AND
				".$engleski_uslov." 
			AND 
				".$grupa_uslov." 
			AND 
				".$status_uslov." 
			AND 
				".$drzavljanstvo_uslov." 
			AND 
				".$boravak_uslov." 
			AND 
				".$skole_uslov." 
			AND 
				".$smjer_uslov." 
			AND 
				".$starost_uslov." 
			AND 
				".$izvor_uslov."
			AND
				".$struka_uslov."
			AND
			   ".$status_prijave_uslov."
			AND
			   ".$dipl_status_uslov."
			AND
			   ".$vrsta_nostrifikacije_uslov."
			";
	} else {
		$sql = "SELECT 
					kandidat_id, 
					kandidat_ime, 
					kandidat_prezime, 
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
					idks.status_id, 
					idks.status_naziv 
				FROM 
					idk_kandidati 
				INNER JOIN 
					idk_kandidati_grupe idkg 
				ON 
					kandidat_group = idkg.kg_id 
				LEFT JOIN 
					idk_link_generator idklg 
				ON 
					kandidat_visitedurl = idklg.lg_id 
				INNER JOIN 
					idk_kandidat_status idks 
				ON 
					kandidat_status = idks.status_id 
				WHERE kandidat_status = 3" ;
		$count_sql = "SELECT 
					count(kandidat_id) as broj 
				FROM 
					idk_kandidati 
				INNER JOIN 
					idk_kandidati_grupe idkg 
				ON 
					kandidat_group = idkg.kg_id 
				LEFT JOIN 
					idk_link_generator idklg 
				ON 
					kandidat_visitedurl = idklg.lg_id 
				INNER JOIN 
					idk_kandidat_status idks 
				ON 
					kandidat_status = idks.status_id 
				WHERE kandidat_status = 3" ;
	}
	if($niz_kategorija != null){
		$sql .= " AND (";
		foreach($niz_kategorija as $kategorija_uslov){
			if($kategorija_uslov != $niz_kategorija[0]){
				$sql .= " OR ";
			}
			$sql .= " kandidat_vozacka_kategorija LIKE ('%".$kategorija_uslov."%')";
		}
		$sql .= ")";
		$count_sql .= " AND (";
		foreach($niz_kategorija as $kategorija_uslov){
			if($kategorija_uslov != $niz_kategorija[0]){
				$count_sql .= " OR ";
			}
			$count_sql .= " kandidat_vozacka_kategorija LIKE ('%".$kategorija_uslov."%')";
		}
		$count_sql .= ")";
	}
	//----------------------------------------------------------------
	
	if( !empty($requestData['search']['value']) ) {
		$sql.=" AND (CONCAT(kandidat_ime,' ',kandidat_prezime) LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR CONCAT(kandidat_ime,kandidat_prezime) LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR CONCAT(kandidat_prezime,' ',kandidat_ime) LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR CONCAT(kandidat_prezime,kandidat_ime) LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR kandidat_ime LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR kandidat_prezime LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR status_naziv LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR kandidat_email LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR idkg.kg_title LIKE '%".$requestData['search']['value']."%' ";
		$sql.=" OR kandidat_mobitel LIKE '%".$requestData['search']['value']."%' )";
	}
	// $query = mysqli_query($conn, $count_sql) or die(mysqli_error($conn));
	// $row = mysqli_fetch_array($query); 
	$stmt = $db->prepare($count_sql);
	$stmt->execute();
	$row = $stmt->fetch();
	$totalData = $row['broj'];
	$totalFiltered = $totalData;


	if($requestData['length'] != -1) {
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
	} else {
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir'];
	}
	// $query = mysqli_query($conn, $sql) or die("serversidedata.php: get employees");
	$stmt = $db->prepare($sql);
	$stmt->execute();
	$data = array();

	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach($rows as $row) {
		$kandidat_id = $row['kandidat_id'];
		$kandidat_ime = $row['kandidat_ime'];
		$kandidat_prezime = $row['kandidat_prezime'];
		$kandidat_datumrodjenja = $row['kandidat_datumrodjenja'];
		$kandidat_spol = $row['kandidat_spol'];
		$kandidat_jmbg = $row['kandidat_jmbg'];
		$kandidat_group = $row['kandidat_group'];
		$kandidat_email = $row['kandidat_email'];
		$kandidat_mobitel = $row['kandidat_mobitel'];
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
		$lg_nalogid = $row['lg_nalogid'];
		$nalog_naziv = $row['nalog_naziv'];
		
		//SLIKA KANDIDATA
		if($row['kandidat_slika'] == 'none'){
			$kandidat_slika = "nonekandidati.jpg";
		} else {
			$kandidat_slika = $row['kandidat_slika'];
		}
		
		$kandidat_datetime = date('d.m.Y H:i:s', strtotime($kandidat_datetime));
		
		//PORIJEKLO KANDIDATA
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
			case "8":
				$porijeklo_text = '<p class="text-center"><span class="label label-warning material-label material-label_warning material-label_xs main-container__column">Novi Partner APP</span></p>';
			break;
			case "0":
				$porijeklo_text = '<p class="text-center"><span class="label label-primary material-label material-label_primary material-label_xs main-container__column">Prijava na oglas</span></p>';
			break;
			
		}
		
		//CV KANDIDATA
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
		
		//PROFIL KANDIDATA
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
		
		//NALOG KANDIDATA
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
		
		//KANDIDAT MESSENGER 
		if($kandidat_status_messenger == 2){
			$bot = ' <i class="fa fa-commenting" style="color: <?php echo $style_bot?> font-size: 1.5em;" aria-hidden="true" title = "Kandidat se logirao na aplikaciju Messenger."></i>';
		}
		elseif($kandidat_status_messenger == 1){
			$bot = ' <i class="fa fa-commenting" style="color: <?php echo $style_bot?> font-size: 1.5em;" aria-hidden="true" title = "Kandidat se čeka za instalaciju Messengera."></i>';
		} else {
			$bot = "";
		}
		
		//STATUS KANDIDATA
		if($row['status_id'] == 0){
			$status_text = '<p class="text-center"><span class="label label-warning material-label material-label_warning material-label_xs main-container__column">'.$kandidat_status.$bot.'</span></p>';
		}elseif($row['status_id'] == 1){
			$status_text = '<p class="text-center"><span class="label label-primary material-label material-label_primary material-label_xs main-container__column">'.$kandidat_status.$bot.'</span></p>';
		}elseif($row['status_id'] == 2){
			$status_text = '<p class="text-center"><span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$kandidat_status.$bot.'</span></p>';
		}elseif($row['status_id'] == 3){
			$status_text = '<p class="text-center"><span class="label label-danger material-label material-label_danger material-label_xs main-container__column">'.$kandidat_status.$bot.'</span></p>';
		}elseif($row['status_id'] == 4){
			$status_text = '<p class="text-center"><span class="label label-info material-label material-label_info material-label_xs main-container__column">'.$kandidat_status.$bot.'</span></p>';
		}elseif($row['status_id'] == 5){
			$status_text = '<p class="text-center"><span class="label label-info material-label material-label_primary material-label_xs main-container__column">'.$kandidat_status.$bot.'</span></p>';
		}elseif($row['status_id'] == 6){
			$status_text = '<p class="text-center"><span class="label label-danger material-label material-label_danger material-label_xs main-container__column">'.$kandidat_status.'</span></p>';
		}elseif($row['status_id'] == 7){
			$status_text = '<p class="text-center"><span class="label label-danger material-label material-label_yellow material-label_xs main-container__column">'.$kandidat_status.'</span></p>';
		}elseif($row['status_id'] == 8){
			$status_text = '<p class="text-center"><span class="label label-danger material-label material-label_yellow material-label_xs main-container__column">'.$kandidat_status.'</span></p>';
		}
		
		// DUPLIKAT
		// $numberOfDuplicates = 0;
		
		// foreach($rows as $potentialDuplicate) {

		// 	if ($potentialDuplicate['kandidat_id'] != $kandidat_id) {

		// 		if ((replace_africates(trim($potentialDuplicate['kandidat_ime'])) == replace_africates(trim($kandidat_ime)) && replace_africates(trim($potentialDuplicate['kandidat_prezime'])) == replace_africates(trim($kandidat_prezime)) && ($potentialDuplicate['kandidat_mobitel'] == $kandidat_mobitel OR $potentialDuplicate['kandidat_datumrodjenja'] == $kandidat_datumrodjenja)) 
		// 			|| 
		// 			(replace_africates(trim($potentialDuplicate['kandidat_ime'])) == replace_africates(trim($kandidat_prezime)) && replace_africates(trim($potentialDuplicate['kandidat_prezime'])) == replace_africates(trim($kandidat_ime)) && ($potentialDuplicate['kandidat_mobitel'] == $kandidat_mobitel OR $potentialDuplicate['kandidat_datumrodjenja'] == $kandidat_datumrodjenja))) {
					
		// 			$numberOfDuplicates++;
		// 		}

		// 	}
			
		// }

		// if($numberOfDuplicates > 0){
		// 	$duplikat_text = '<p class="text-center"><span class="label label-danger material-label material-label_danger material-label_xs main-container__column">IMA DUPLIKAT</span></p>';
		// 	$doubleicon = '<i class="fa fa-user-times" style="color: #f3413c; font-size: 1.5em;" aria-hidden="true" title = "Postoji duplikat!"></i> ';
		// }else{
		// 	$duplikat_text = '<p class="text-center"><span class="label label-success material-label material-label_success material-label_xs main-container__column">NEMA DUPLIKAT</span></p>';
		// 	$doubleicon = "";
		// }
		
		$nestedData = array();
		
		$nestedData[] = '<p class="text-center">'.$kandidat_id.'</p>';
		$nestedData[] = '<p class="text-center"><a href="'.$getSiteUrl.'kandidati?page=open&id='.$kandidat_id.'"><img class="idk_profile_img" src="'.$getSiteUrl.'files/kandidati/'.$kandidat_slika.'"></a></p>';
		// $nestedData[] = '<p class="text-center">'.$doubleicon.'</p>';
		$nestedData[] = '<p class="text-left"><a href="'.$getSiteUrl.'kandidati?page=open&id='.$kandidat_id.'">'.$kandidat_ime.' '.$kandidat_prezime.'</a></p>';
		$nestedData[] = '<span class="date-sort-class">'.$kandidat_datetime.'</span>';
		$nestedData[] = $porijeklo_text;
		$nestedData[] = '<p class="text-center">'.$cv_txt_ba.' '.$cv_txt_de.'</p>';
		$nestedData[] = '<p class="text-left"><a href="'.$getSiteUrl.'nalozi?page=open&id='.$lg_nalogid.'" target="_BLANK" class="label label-'.$nalog_boja.' material-label material-label_'.$nalog_boja.' material-label_xs main-container__column" title="'.$nalog_puni_naziv.'">'. $lg_nalogid.' - '.$nalog_naziv.'</a></p>';
		$nestedData[] = '<p class="text-center"><span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$kg_title.'</span></p>';
		$nestedData[] = $status_text;
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
		$nestedData[] = $kandidat_mobitel;

		$data[] = $nestedData;
	}

	$json_data = array(
				"draw"            => intval( $requestData['draw'] ),
				"recordsTotal"    => intval( $totalData ),
				"recordsFiltered" => intval( $totalFiltered ),
				"data"            => $data
				);

	echo json_encode($json_data);
	//echo $sql;
	
	break;
	
	case "lista_link_generator":
	
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
			0 => 'lg_id',
			1 => 'lg_url',
			2 => 'lg_desc',
			3 => 'lg_link_prijave',
			4 => 'lg_datetime'
		);
		if( getEmployeeStatus() == 17){
			$employee_uslov = " AND employee_id = $logged_employee_id";
		} else {
			$employee_uslov = " AND 1";
		}
		
		$sql = "
			SELECT lg_id, lg_link_prijave, idk_urlimg_prijave, lg_url, lg_desc, lg_datetime, employee_firstname, employee_lastname, lg_broj_pregleda, lg_language
			FROM idk_link_generator
			INNER JOIN idk_employees ON idk_link_generator.lg_employeeid = idk_employees.employee_id
			WHERE lg_status = 0
			$employee_uslov
		";
		
		
		if( !empty($requestData['search']['value']) ) {
			
			$sql.=" AND ( lg_url LIKE '%".$requestData['search']['value']."%' ";
			$sql.=" OR lg_desc LIKE '%".$requestData['search']['value']."%' ";
			$sql.=" AND ( CONCAT(employee_firstname,' ',employee_lastname) LIKE '%".$requestData['search']['value']."%' ";
			$sql.=" OR CONCAT(employee_firstname,employee_lastname) LIKE '%".$requestData['search']['value']."%' ";
			$sql.=" OR CONCAT(employee_lastname,' ',employee_firstname) LIKE '%".$requestData['search']['value']."%' ";
			$sql.=" OR CONCAT(employee_lastname,employee_firstname) LIKE '%".$requestData['search']['value']."%' ";
		}

		$query = mysqli_query($conn, $sql) or die();
		$totalData = mysqli_num_rows($query);
		$totalFiltered = $totalData;
		
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		$query = mysqli_query($conn, $sql) or die();
		$data = array();
		while($row = mysqli_fetch_array($query)){
			
			$lg_id = $row['lg_id'];
			$lg_link_prijave = $row['lg_link_prijave'];
			$idk_urlimg_prijave = $row['idk_urlimg_prijave'];
			$lg_url = $row['lg_url'];
			$lg_desc = $row['lg_desc'];
			$lg_broj_pregleda = '<span class="label label-warning material-label material-label_warning main-container__column">'.$row['lg_broj_pregleda'].'</span>';
			$lg_datetime = $row['lg_datetime'];
			$employee_firstname = $row['employee_firstname'];
			$employee_lastname = $row['employee_lastname'];
			$lg_language = $row['lg_language'];
			$lg_datetimef = date('d.m.Y H:i', strtotime($lg_datetime));
			
		
			// BROJ PRIJAVLJENIH
			$query_check_numbers = $db->prepare("
							SELECT kandidat_id
							FROM idk_kandidati
							WHERE kandidat_visitedurl = :kandidat_visitedurl
							");

			$query_check_numbers->execute(array(
				":kandidat_visitedurl" => $lg_id
			));
			$check = $query_check_numbers->rowCount();

			if($check > 0){
				$check_txt = '<span class="label label-success material-label material-label_success main-container__column">'.$check.'</span>';
			}else{
				$check_txt = '<span class="label label-warning material-label material-label_warning main-container__column">'.$check.'</span>';
			}
			
			if($lg_link_prijave == null OR $lg_link_prijave == ""){
				$span_link_prijave = '<span class="label cursor label-danger material-label material-label_danger main-container__column">';
				$href_link_prijave = "#";
				$target_blank = "";
			}else{
				$span_link_prijave = '<span class="label cursor label-success material-label material-label_success main-container__column">';
				$href_link_prijave = $lg_link_prijave;
				$target_blank = 'target="_BLANK"';
			}

			if(empty($idk_urlimg_prijave) OR ($idk_urlimg_prijave == "none")){ $warning_success = 'warning'; }else{ $warning_success = 'success'; }	
			$getSiteUrl = getSiteURLr();
			$clipBoard = "'#p".$lg_id."'";
			$nestedData = array();
			
			$nestedData[] = '<p class="text-center">'.$lg_id.'</p>';
			$nestedData[] = '<p>'.$lg_url.'</p>';
			$nestedData[] = '<p>'.$lg_desc.'</p>';
			$nestedData[] = '<p class="text-center">'.$lg_broj_pregleda.'</p>';
			$nestedData[] = '<a href="'.$getSiteUrl.'link_generator.php?page=show_list&id='.$lg_id.'"><p class="text-center">'.$check_txt.'</p></a>';
			$nestedData[] = '<p class="text-center">'.strtoupper($lg_language).'</p>';
			$nestedData[] = '<a href="'.$href_link_prijave.'"'.$target_blank.'>'.$span_link_prijave.'<i class="fa fa-external-link" aria-hidden="true"></i></span></a>';
			$nestedData[] = '<span style="overflow:hidden;width:0px;height:0px;opacity:0;" id="p'.$lg_id.'" class="label label-default material-label main-container__column">https://join.job-step.com/job/'.$lg_id.'</span><span data-toggle="tooltip" data-placement="top" title="KOPIRAJ URL" aria-hidden="true" onclick="copyToClipboard('.$clipBoard.')" class="label cursor label-success material-label material-label_success main-container__column"><i class="fa fa-clipboard" aria-hidden="true"></i></span>';
			$nestedData[] = '<a href="'.$getSiteUrl.'files/partner_nalogs/'.$idk_urlimg_prijave.'" target="_blank"><span class="label label-success material-label material-label_'.$warning_success.' main-container__column"><i class="fa fa-clipboard" aria-hidden="true"></i></span></a>';
			$nestedData[] = '<span class="label label-success material-label material-label_success main-container__column">'.$lg_datetimef.' <br />'.$employee_firstname.' '.$employee_lastname.'</span>';
			$nestedData[] = '<div class="btn-group material-btn-group">
													 <button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
													 <ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
														
													 <li><a href="'.$getSiteUrl.'link_generator?page=edit&id='.$lg_id.'" class="material-dropdown-menu__link"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi</a></li>
														
													 <li><a href="'.$getSiteUrl.'link_generator?page=statistike&id='.$lg_id.'" class="material-dropdown-menu__link"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Statistike</a></li>
														
													 <li class="idk_dropdown_danger"><a href="#" data-link="'.$getSiteUrl.'link_generator?page=archive&id='.$lg_id.'" onclick="archiveLink(this)" data-toggle="modal" data-target="#archiveLinkModal" class="material-dropdown-menu__link archive"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>

													 </ul>
												 </div>';
				
			
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
	
	case "lista_projekti_kandidati":
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
			0 => 'kandidat_id',
			1 => 'kandidat_ime',
			2 => 'kandidat_group',
			3 => 'kandidat_status'
		);
		
		$sql = "
				SELECT 
					kandidat_id, 
					kandidat_ime, 
					kandidat_prezime, 
					kandidat_spol,
					kandidat_jmbg, 
					kandidat_status, 
					kandidat_slika, 
					kandidat_email, 
					kandidat_datetime, 
					kandidat_visitedurl, 
					kandidat_prijava_na, 
					kandidat_group, 
					cv_ba, 
					cv_de
				FROM 
					idk_kandidati
				INNER JOIN 
					idk_project_kandidati 
				ON 
					idk_kandidati.kandidat_id = idk_project_kandidati.pk_kandidatid
				WHERE 
					pk_projectid = :pk_projectid 
				";
	break;
	
	case "lista_kandidata_dn":

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

	$requestData 					= $_REQUEST;
	$starost_od 					= $_REQUEST['starost_od'];
	$starost_do 					= $_REQUEST['starost_do'];
	$vozacka_dozvola 				= $_REQUEST['vozacka_dozvola'];
	$radno_iskustvo 				= $_REQUEST['radno_iskustvo'];
	$znanje_njemacki 				= $_REQUEST['znanje_njemacki'];
	$filter_skoleImp 				= $_REQUEST['filter_skole'];
	$filter_smjerImp 				= $_REQUEST['filter_smjer'];
	$filter_kategorija_vozackeImp 	= $_REQUEST['filter_kategorija_vozacke'];


	if($filter_skoleImp != null){
		$filter_skoleExp = explode(",", $filter_skoleImp);
	} else {
		$filter_skoleExp = null;
	}
	if($filter_smjerImp != null){
		$filter_smjerExp = explode(",", $filter_smjerImp);
	} else {
		$filter_smjerExp = null;
	}
	
	if($filter_kategorija_vozackeImp != null){
		$filter_kategorija_vozackeExp = explode(",", $filter_kategorija_vozackeImp);
	} else {
		$filter_kategorija_vozackeExp = null;
	}
	$columns = array(
		0 => 'kandidat_id',
		1 => 'kandidat_ime',
		2 => 'kandidat_datetime',
		3 => 'kandidat_vozacka_kategorija'	
		// 4 => 'struka',		
		// 5 => 'jezik'
	);

	
	//STAROST KANDIDATA
	if($starost_od != null && $starost_do != null){
		$starost_uslov = " (YEAR(NOW()) - YEAR(`kandidat_datumrodjenja`)) BETWEEN ".$starost_od." AND ".$starost_do."";
		
	} else {
		$starost_uslov = 1;
	}

	//VOZACKA DOZVOLA
	if($vozacka_dozvola == "DA"){
		$vozacka_uslov = " kandidat_vozacka_dozvola LIKE '%Da%'";
	} else {
		$vozacka_uslov = 1;
	}

	//RADNO ISKUSTVO
	if($radno_iskustvo == "DA"){
		$radno_iskustvo_uslov = " kandidat_id IN (SELECT kri_kandidat_id FROM `idk_kandidat_radno_iskustvo` GROUP BY kri_kandidat_id)";
	}else {
		$radno_iskustvo_uslov = 1;
	}

	//NIVO NJEMACKOG JEZIKA
	if($znanje_njemacki == "A1"){
		$njemacki_uslov = " kandidat_id IN (SELECT kj_kandidatid FROM `idk_kandidat_jezici` WHERE kj_naziv LIKE '%Njemacki%' AND (kj_slusanje LIKE 'A1'  OR kj_slusanje LIKE 'A2' OR kj_slusanje LIKE 'B1' OR kj_slusanje LIKE 'B1' OR kj_slusanje LIKE 'B2' OR kj_slusanje LIKE 'C1' OR kj_slusanje LIKE 'C2'))";
	}
	elseif($znanje_njemacki == "A2"){
		$njemacki_uslov = " kandidat_id IN (SELECT kj_kandidatid FROM `idk_kandidat_jezici` WHERE kj_naziv LIKE '%Njemacki%' AND (kj_slusanje LIKE 'A2'  OR kj_slusanje LIKE 'B1' OR kj_slusanje LIKE 'B2' OR kj_slusanje LIKE 'C1' OR kj_slusanje LIKE 'C2'))";
	}
	elseif($znanje_njemacki == "B1"){
		$njemacki_uslov = " kandidat_id IN (SELECT kj_kandidatid FROM `idk_kandidat_jezici` WHERE kj_naziv LIKE '%Njemacki%' AND (kj_slusanje LIKE 'B1'  OR kj_slusanje LIKE 'B2' OR kj_slusanje LIKE 'C1' OR kj_slusanje LIKE 'C2'))";
	}
	elseif($znanje_njemacki == "B2"){
		$njemacki_uslov = " kandidat_id IN (SELECT kj_kandidatid FROM `idk_kandidat_jezici` WHERE kj_naziv LIKE '%Njemacki%' AND (kj_slusanje LIKE 'B2'  OR kj_slusanje LIKE 'C1' OR kj_slusanje LIKE 'C2'))";
	}
	elseif($znanje_njemacki == "C1"){
		$njemacki_uslov = " kandidat_id IN (SELECT kj_kandidatid FROM `idk_kandidat_jezici` WHERE kj_naziv LIKE '%Njemacki%' AND (kj_slusanje LIKE 'C1'  OR kj_slusanje LIKE 'C2'))";
	}
	elseif($znanje_njemacki == "C2"){
		$njemacki_uslov = " kandidat_id IN (SELECT kj_kandidatid FROM `idk_kandidat_jezici` WHERE kj_naziv LIKE '%Njemacki%' AND (kj_slusanje LIKE 'C2'))";
	} 
	elseif($znanje_njemacki == "Bezznanja") {
		$njemacki_uslov = " kandidat_id IN (SELECT kj_kandidatid FROM `idk_kandidat_jezici` WHERE kj_naziv LIKE '%Njemacki%' AND (kj_slusanje LIKE 'BEZ ZNANJA'))";
	}
	elseif($znanje_njemacki == "nemainfo"){
		$njemacki_uslov = " kandidat_id IN (SELECT kj_kandidatid FROM `idk_kandidat_jezici` WHERE kj_naziv LIKE '%Njemacki%' AND (kj_slusanje LIKE ''))";
	}else {
		$njemacki_uslov = 1;
	}


	//KANDIDAT SKOLA
	if($filter_skoleExp != null){
		$kandidat_id_skola = array();
		for($i = 0; $i < count($filter_skoleExp); $i++){
			$stmt = $db->prepare("SELECT ke_kandidat_id FROM idk_kandidat_edukacija WHERE ke_naziv LIKE '%".$filter_skoleExp[$i]."%'");
			$stmt->execute();
			$result = $stmt->fetchAll();
			
			foreach($result as $kandidat_skola){
				$id_kandidata 			= $kandidat_skola['ke_kandidat_id'];
				$kandidat_id_skola[] 	= $id_kandidata;
			}
		}
		$rezultat_skole = array_unique($kandidat_id_skola);
		$rezultat_skoleImp = implode(",", $rezultat_skole);
		$skole_uslov = " kandidat_id IN (" . $rezultat_skoleImp . ")";
	} else {
		$skole_uslov = 1;
	}

	//KANDIDAT SMJER
	if($filter_smjerExp != null){
		$kandidat_id_smjer = array();
		for($i = 0; $i < count($filter_smjerExp); $i++){
			$stmt2 = $db->prepare("SELECT ke_kandidat_id FROM idk_kandidat_edukacija WHERE ke_naziv_kvalifikacije LIKE '".$filter_smjerExp[$i]."'");
			$stmt2->execute();
			$result2 = $stmt2->fetchAll();
			
			foreach($result2 as $kandidat_smjer){
				$id_kandidata_smjer 	= $kandidat_smjer['ke_kandidat_id'];
				$kandidat_id_smjer[] 	= $id_kandidata_smjer;
			}
		}
		$rezultat_smjera 	= array_unique($kandidat_id_smjer);
		$rezultat_smjeraImp = implode(",", $rezultat_smjera);
		$smjer_uslov 		= " kandidat_id IN (" . $rezultat_smjeraImp . ")"; 
	} else {
		$smjer_uslov = 1;
	}
	
	
	//KANDIDAT KATEGORIJA VOZACKE
	$niz_kategorija = array();
	if($filter_kategorija_vozackeExp != null){
		$najveci = null;
		if(in_array("B", $filter_kategorija_vozackeExp)){
			$najveci = "B";
		}
		else if(in_array("C1", $filter_kategorija_vozackeExp)){
			$najveci = "C1";
		}
		else if(in_array("C", $filter_kategorija_vozackeExp)){
			$najveci = "C";
		}
		else if(in_array("C1E", $filter_kategorija_vozackeExp)){
			$najveci = "C1E";
		}
		else if(in_array("CE", $filter_kategorija_vozackeExp)){
			$najveci = "CE";
		}
		if($najveci == "B"){
			$niz_kategorija[] = "CE";
			$niz_kategorija[] = "C1E";
			$niz_kategorija[] = "C";
			$niz_kategorija[] = "C1";
			$niz_kategorija[] = "B";
		}
		else if($najveci == "C1"){
			$niz_kategorija[] = "CE";
			$niz_kategorija[] = "C1E";
			$niz_kategorija[] = "C";
			$niz_kategorija[] = "C1";
		}
		else if($najveci == "C"){
			$niz_kategorija[] = "CE";
			$niz_kategorija[] = "C1E";
			$niz_kategorija[] = "C";
		}
		else if($najveci == "C1E"){
			$niz_kategorija[] = "CE";
			$niz_kategorija[] = "C1E";
		}
		else if($najveci == "CE"){
			$niz_kategorija[] = "CE";
		}
		
		if(in_array("BE", $filter_kategorija_vozackeExp)){
			$niz_kategorija[] = "BE";
		}
	}
	$sql = "
		SELECT 
		kandidat_id, 
		   kandidat_ime, 
		   kandidat_prezime, 
		   kandidat_slika, 
		   kandidat_datetime,
		   kandidat_vozacka_kategorija
		FROM  idk_kandidati 
		WHERE  kandidat_status != 3 
			   AND
			   ".$vozacka_uslov."
			   AND
			   ".$radno_iskustvo_uslov."
			   AND
			   ".$njemacki_uslov."
			   AND
			   ".$skole_uslov."
			   AND
			   ".$smjer_uslov."
			   AND
			   ".$starost_uslov."
		" ;
		
	if($niz_kategorija != null){
		$sql .= " AND (";
		foreach($niz_kategorija as $kategorija_uslov){
			if($kategorija_uslov != $niz_kategorija[0]){
				$sql .= " OR ";
			}
			$sql .= " kandidat_vozacka_kategorija LIKE ('%".$kategorija_uslov."%')";
		}
		$sql .= ")";
	}
	//----------------------------------------------------------------
	

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
	}

	$query = mysqli_query($conn, $sql) or die();
	$totalData = mysqli_num_rows($query);
	$totalFiltered = $totalData;

	$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
	$query = mysqli_query($conn, $sql) or die();
	$data = array();
	
	while( $row=mysqli_fetch_array($query) ) {

		$kandidat_id 		= $row['kandidat_id'];
		$kandidat_ime 		= $row['kandidat_ime'];
		$kandidat_prezime 	= $row['kandidat_prezime'];
		$kandidat_datetime 	= $row['kandidat_datetime'];
		$kandidat_vozacka	= $row['kandidat_vozacka_kategorija'];
		$getSiteUrl 		= getSiteURLr();
		
		$query_get_lang = $db->prepare("SELECT kj_slusanje FROM idk_kandidat_jezici WHERE kj_kandidatid = :kandidat_id AND kj_naziv LIKE '%Njemacki%'");
		$query_get_lang->execute(array(
			":kandidat_id" => $kandidat_id
		));
		$result_lang = $query_get_lang->fetch();
		if($result_lang != null){
			$kandidat_jezik = $result_lang['kj_slusanje'];
		} else {
			$kandidat_jezik = "-";
		}
		if($kandidat_jezik == "Bez znanja"){
			$kandidat_jezik = "Ohne Kenntnisse";
		}
		$query_get_struka = $db->prepare("SELECT 
											ke_smjer_id, 
											naziv_struke_de 
										FROM idk_kandidat_edukacija 
										JOIN idk_skole_smjerovi 
										ON idk_kandidat_edukacija.ke_smjer_id = idk_skole_smjerovi.ss_id 
										JOIN idk_struke 
										ON idk_skole_smjerovi.ss_struka_id = idk_struke.id_struke
										WHERE ke_kandidat_id = :kandidat_id
										");
		$query_get_struka->execute(array(
			":kandidat_id" => $kandidat_id
		));
		$result_struka = $query_get_struka->fetch();
		if($result_struka != null){
			$kandidat_struka = $result_struka['naziv_struke_de'];
		} else {
			$kandidat_struka = "-";
		}

		
		if($kandidat_vozacka == null){
			$kandidat_vozacka = "-";
		}
		//SLIKA KANDIDATA
		if($row['kandidat_slika'] == 'none'){
			$kandidat_slika = "nonekandidati.jpg";
		} else {
			$kandidat_slika = $row['kandidat_slika'];
		}
		
		$kandidat_datetime = date('d.m.Y H:i:s', strtotime($kandidat_datetime));
		
		$kandidat_ime_f	= replace_africates($kandidat_ime);
		$kandidat_prezime_f	= replace_africates($kandidat_prezime);
		$kandidat_ime_inicijal 		= $kandidat_ime_f[0];
		$kandidat_prezime_inicijal 	= $kandidat_prezime_f[0];
		
		
		$nestedData = array();
		
		$nestedData[] = '<p class="text-center">'.$kandidat_id.'</p>';
		$nestedData[] = '<p class="text-center">'.$kandidat_ime_inicijal.'. '.$kandidat_prezime_inicijal.'.</p>';
		$nestedData[] = '<span class="date-sort-class text-center">'.$kandidat_datetime.'</span>';
		$nestedData[] = '<p class="text-center">'.$kandidat_vozacka.'</p>';
		$nestedData[] = '<p class="text-center">'.$kandidat_struka.'</p>';
		$nestedData[] = '<p class="text-center">'.$kandidat_jezik.'</p>';
		
		$data[] = $nestedData;
	}
// var_dump($data);
	$json_data = array(
				"draw"            => intval( $requestData['draw'] ),
				"recordsTotal"    => intval( $totalData ),
				"recordsFiltered" => intval( $totalFiltered ),
				"data"            => $data
				);

	echo json_encode($json_data);
	//echo $sql;
	// // var_dump($sql);
	break;

	case "tf_naslovnica_casting": 

		$columns = array(
			0 => 'kandidat_ime',
			1 => 'nalog_naziv',
			2 => 'vrsta_id',
			3 => 'tfs_name',
			4 => 'project_name',
			5 => 'tf_call_appointment',
			6 => 'employee_firstname',
			7 => 'fn_reserved'
		);

		/* TASK FORCE ZADACI IZ TABELE TASK-FORCE SVE VRSTE */

		$query = '
			(
				SELECT idk_kandidati.kandidat_id, idk_kandidati.kandidat_ime, idk_kandidati.kandidat_prezime, idk_nalozi.nalog_naziv, idk_nalozi.nalog_id, idk_tf_statusi.tfs_name, idk_projects.project_name, idk_projects.project_id, idk_task_force.tf_call_appointment, em1.employee_firstname, em1.employee_lastname, em2.employee_firstname as fn_reserved, em2.employee_lastname as ln_reserved, idk_kandidati.tf_reserved_agent, idk_task_force.tf_id, idk_tf_vrste.name AS tf_vrsta_name, idk_task_force.tf_vrsta_id as vrsta_id
				FROM `idk_task_force`
				
				INNER JOIN idk_kandidati  ON idk_task_force.tf_candidate_id = idk_kandidati.kandidat_id
				INNER JOIN idk_nalozi     ON idk_task_force.tf_nalog_id     = idk_nalozi.nalog_id
				INNER JOIN idk_tf_statusi ON idk_task_force.tf_status_id    = idk_tf_statusi.tfs_id
				INNER JOIN idk_projects   ON idk_task_force.tf_project_id   = idk_projects.project_id
				INNER JOIN idk_tf_vrste   ON idk_task_force.tf_vrsta_id     = idk_tf_vrste.id
				LEFT JOIN idk_employees em1 ON idk_task_force.tf_agent_id     = em1.employee_id
				LEFT JOIN idk_employees em2 ON idk_kandidati.tf_reserved_agent = em2.employee_id
				
				WHERE idk_task_force.tf_last_active_task = 1
				AND idk_nalozi.nalog_prioritet IS NOT NULL AND tf_vrsta_id = 1 AND idk_kandidati.kandidat_pogresan_broj = 0
				
				
		';

		if (!empty($_POST['search']['value'])) {
			
			$post_search_value = trim($_POST["search"]["value"]);
			
			$query .= ' AND (CONCAT(kandidat_ime, " ", kandidat_prezime) LIKE "%' 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, " ", kandidat_ime) LIKE "%' 	 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, kandidat_ime) LIKE "%' 	     	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     	  . $post_search_value . '%" ';
			$query .= 'OR kandidat_ime LIKE "%'         						 	  . $post_search_value . '%" ';
			$query .= 'OR kandidat_prezime LIKE "%'         					 	  . $post_search_value . '%" ';
			$query .= 'OR nalog_naziv LIKE "%'         							 	  . $post_search_value . '%" ';
			$query .= 'OR tfs_name LIKE "%' 		   							 	  . $post_search_value . '%" ';
			$query .= 'OR idk_tf_vrste.name LIKE "%' 		   						  . $post_search_value . '%" ';
			$query .= 'OR project_name LIKE "%'        							 	  . $post_search_value . '%" ';
			$query .= 'OR tf_call_appointment LIKE "%' 							 	  . $post_search_value . '%" ';
			$query .= 'OR kandidat_mobitel LIKE "%' 							 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(em1.employee_firstname, em1.employee_lastname) LIKE "%' 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(em1.employee_lastname, " ", em1.employee_firstname) LIKE "%' . $post_search_value . '%" ';
			$query .= 'OR CONCAT(em1.employee_lastname, em1.employee_firstname) LIKE "%' 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(em1.employee_firstname, em1.employee_lastname) LIKE "%' 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(em2.employee_firstname, em2.employee_lastname) LIKE "%' 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(em2.employee_lastname, " ", em2.employee_firstname) LIKE "%' . $post_search_value . '%" ';
			$query .= 'OR CONCAT(em2.employee_lastname, em2.employee_firstname) LIKE "%' 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(em2.employee_firstname, em2.employee_lastname) LIKE "%' 	  . $post_search_value . '%") ';
			
		}

		if (!isset($_POST["order"])) {

			$query .= '
				
				ORDER BY FIELD (idk_tf_statusi.tfs_name, "Pristao", "Zainteresiran", "Dopuna", "Neodlučan", "Neuspješna komunikacija", "Dolazi")
			';

		}

		/* TASK FORCE CASTING KANDIDATI KOJI TREBAJU DOCI NA POZIV A NISU U TASK FORCE */
		$query .= '
			)	
			
			UNION ALL
			
			(
				SELECT idk_kandidati.kandidat_id, idk_kandidati.kandidat_ime, idk_kandidati.kandidat_prezime, idk_nalozi.nalog_naziv, idk_nalozi.nalog_id, "" AS tfs_name, idk_projects.project_name, idk_projects.project_id, "" AS tf_call_appointment, "" AS employee_firstname, "" AS employee_lastname, "" AS fn_reserved, "" AS ln_reserved, idk_kandidati.tf_reserved_agent, "" AS tf_id, "Casting" AS tf_vrsta_name, 1 AS vrsta_id
				FROM idk_kandidati
				
				JOIN idk_project_kandidati ON idk_project_kandidati.pk_kandidatid = idk_kandidati.kandidat_id
				JOIN idk_projects ON idk_project_kandidati.pk_projectid = idk_projects.project_id
				JOIN idk_nalozi ON idk_nalozi.nalog_id = idk_projects.project_nalogid
				
				WHERE (idk_kandidati.kandidat_status_prijave IN (0,1,2,5,6) OR idk_kandidati.kandidat_status_prijave is null)
				AND idk_nalozi.nalog_prioritet IS NOT NULL AND idk_kandidati.kandidat_tf_status IS NULL
				AND (project_name LIKE "%BOT - %" OR project_name LIKE "%Prijave%" OR project_name LIKE "%Baza - odgovara za nalog%") AND idk_kandidati.kandidat_pogresan_broj = 0

		';
		
		if (!empty($_POST['search']['value'])) {
			
			$post_search_value = trim($_POST["search"]["value"]);
			
			$query .= ' AND (CONCAT(kandidat_ime, " ", kandidat_prezime) LIKE "%' . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, " ", kandidat_ime) LIKE "%' 	 . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, kandidat_ime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR kandidat_ime LIKE "%'         						 . $post_search_value . '%" ';
			$query .= 'OR kandidat_prezime LIKE "%'         					 . $post_search_value . '%" ';
			$query .= 'OR nalog_naziv LIKE "%'         							 . $post_search_value . '%" ';
			$query .= 'OR project_name LIKE "%'        							 . $post_search_value . '%" ';
			$query .= 'OR "Casting" LIKE "%'        							 . $post_search_value . '%" ';
			$query .= 'OR kandidat_mobitel LIKE "%' 							 . $post_search_value . '%") ';
			
		}

		if (!isset($_POST["order"])) {

			$query .= '
				
				ORDER BY idk_projects.project_id DESC
			';

		}

		$query .= ')';

		if (isset($_POST["order"])) {
			if ($_POST["order"][0]['column'] !== 0) {

				$query .= ' 
					ORDER BY '. $columns[$_POST["order"][0]["column"]]. ' '. $_POST["order"]["0"]["dir"] . ' 
				';

			}
		}

		if ($_POST["length"] != -1) {
			$query .= ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$query_prepared = $db->prepare($query);
		$query_prepared->execute();
		
		$rows = $query_prepared->fetchAll();
		$data = array();

		foreach ($rows as $row) {

			$sub_array[0] = '<a href="kandidati?page=open&id=' . $row["kandidat_id"] . '&projekt_id=' . $row["project_id"] . '&nalog_id=' . $row["nalog_id"] . '&vrsta_id='. $row["vrsta_id"]. '">' . $row["kandidat_ime"] . " " . $row["kandidat_prezime"] . '</a>';
			$sub_array[1] = $row['nalog_naziv'];
			$sub_array[2] = '<span data-order="' . $row['tf_vrsta_name'] . '">' . $row['tf_vrsta_name'] . '</span>';
			$sub_array[3] = $row['tfs_name'];
			$sub_array[4] = $row['project_name'];
			$sub_array[5] = $row['tf_call_appointment'];
			$sub_array[6] = $row['employee_firstname'] . " " . $row['employee_lastname'];
			$sub_array[7] = $row['fn_reserved'] . " " . $row['ln_reserved'];
			$sub_array[8] = $row['kandidat_id'];
			$sub_array[9] = $row['tf_reserved_agent'];
			$sub_array[10] = $row['tf_id'];

			
			$data[] = $sub_array;

		}

		$query = '
			(
				SELECT idk_kandidati.kandidat_id, idk_kandidati.kandidat_ime, idk_kandidati.kandidat_prezime, idk_nalozi.nalog_naziv, idk_nalozi.nalog_id, idk_tf_statusi.tfs_name, idk_projects.project_name, idk_projects.project_id, idk_task_force.tf_call_appointment, em1.employee_firstname, em1.employee_lastname, em2.employee_firstname as fn_reserved, em2.employee_lastname as ln_reserved, idk_kandidati.tf_reserved_agent, idk_task_force.tf_id, idk_tf_vrste.name AS tf_vrsta_name, idk_task_force.tf_vrsta_id AS vrsta_id
				FROM `idk_task_force`
				
				INNER JOIN idk_kandidati  ON idk_task_force.tf_candidate_id = idk_kandidati.kandidat_id
				INNER JOIN idk_nalozi     ON idk_task_force.tf_nalog_id     = idk_nalozi.nalog_id
				INNER JOIN idk_tf_statusi ON idk_task_force.tf_status_id    = idk_tf_statusi.tfs_id
				INNER JOIN idk_projects   ON idk_task_force.tf_project_id   = idk_projects.project_id
				INNER JOIN idk_tf_vrste   ON idk_task_force.tf_vrsta_id     = idk_tf_vrste.id
				LEFT JOIN idk_employees em1  ON idk_task_force.tf_agent_id     = em1.employee_id
				LEFT JOIN idk_employees em2 ON idk_kandidati.tf_reserved_agent = em2.employee_id
				
				WHERE idk_task_force.tf_last_active_task = 1
				AND idk_nalozi.nalog_prioritet IS NOT NULL AND tf_vrsta_id = 1 AND idk_kandidati.kandidat_pogresan_broj = 0
				
		';

		if (!empty($_POST['search']['value'])) {
			
			$post_search_value = trim($_POST["search"]["value"]);
			
			$query .= ' AND (CONCAT(kandidat_ime, " ", kandidat_prezime) LIKE "%' 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, " ", kandidat_ime) LIKE "%' 	 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, kandidat_ime) LIKE "%' 	     	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     	  . $post_search_value . '%" ';
			$query .= 'OR kandidat_ime LIKE "%'         						 	  . $post_search_value . '%" ';
			$query .= 'OR kandidat_prezime LIKE "%'         					 	  . $post_search_value . '%" ';
			$query .= 'OR nalog_naziv LIKE "%'         							 	  . $post_search_value . '%" ';
			$query .= 'OR tfs_name LIKE "%' 		   							 	  . $post_search_value . '%" ';
			$query .= 'OR idk_tf_vrste.name LIKE "%' 		   						  . $post_search_value . '%" ';
			$query .= 'OR project_name LIKE "%'        							 	  . $post_search_value . '%" ';
			$query .= 'OR tf_call_appointment LIKE "%' 							 	  . $post_search_value . '%" ';
			$query .= 'OR kandidat_mobitel LIKE "%' 							 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(em1.employee_firstname, em1.employee_lastname) LIKE "%' 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(em1.employee_lastname, " ", em1.employee_firstname) LIKE "%' . $post_search_value . '%" ';
			$query .= 'OR CONCAT(em1.employee_lastname, em1.employee_firstname) LIKE "%' 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(em1.employee_firstname, em1.employee_lastname) LIKE "%' 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(em2.employee_firstname, em2.employee_lastname) LIKE "%' 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(em2.employee_lastname, " ", em2.employee_firstname) LIKE "%' . $post_search_value . '%" ';
			$query .= 'OR CONCAT(em2.employee_lastname, em2.employee_firstname) LIKE "%' 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(em2.employee_firstname, em2.employee_lastname) LIKE "%' 	  . $post_search_value . '%") ';
			
		}

		$query .= '
			)	
			
			UNION ALL
			
			(
				SELECT idk_kandidati.kandidat_id, idk_kandidati.kandidat_ime, idk_kandidati.kandidat_prezime, idk_nalozi.nalog_naziv, idk_nalozi.nalog_id, "" AS tfs_name, idk_projects.project_name, idk_projects.project_id, "" AS tf_call_appointment, "" AS employee_firstname, "" AS employee_lastname, "" AS employee_firstname, "" AS employee_lastname, idk_kandidati.tf_reserved_agent, "" AS tf_id, "Casting" AS tf_vrsta_name, 1 AS vrsta_id
				FROM idk_kandidati
				
				JOIN idk_project_kandidati ON idk_project_kandidati.pk_kandidatid = idk_kandidati.kandidat_id
				JOIN idk_projects ON idk_project_kandidati.pk_projectid = idk_projects.project_id
				JOIN idk_nalozi ON idk_nalozi.nalog_id = idk_projects.project_nalogid
				
				WHERE (idk_kandidati.kandidat_status_prijave IN (0,1,2,5,6) OR idk_kandidati.kandidat_status_prijave is null)
				AND idk_nalozi.nalog_prioritet IS NOT NULL 
				AND idk_kandidati.kandidat_tf_status IS NULL
				AND (project_name LIKE "%BOT - %" OR project_name LIKE "%Prijave%" OR project_name LIKE "%Baza - odgovara za nalog%") AND idk_kandidati.kandidat_pogresan_broj = 0

		';

		if (!empty($_POST['search']['value'])) {
			
			$post_search_value = trim($_POST["search"]["value"]);
			
			$query .= ' AND (CONCAT(kandidat_ime, " ", kandidat_prezime) LIKE "%' . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, " ", kandidat_ime) LIKE "%' 	 . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, kandidat_ime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR kandidat_ime LIKE "%'         						 . $post_search_value . '%" ';
			$query .= 'OR kandidat_prezime LIKE "%'         					 . $post_search_value . '%" ';
			$query .= 'OR nalog_naziv LIKE "%'         							 . $post_search_value . '%" ';
			$query .= 'OR project_name LIKE "%'        							 . $post_search_value . '%" ';
			$query .= 'OR "Casting" LIKE "%'        							 . $post_search_value . '%" ';
			$query .= 'OR kandidat_mobitel LIKE "%' 							 . $post_search_value . '%") ';
			
		}

		$query .= ')';

		$query_prepared = $db->prepare($query);
		$query_prepared->execute();

		$recordsTotal  = $query_prepared->rowCount();
		$filtered_rows = $recordsTotal;
		
		$output = array(
			"draw" 			  => intval($_POST["draw"]),
			"recordsTotal"    => $recordsTotal,
			"recordsFiltered" => $filtered_rows,
			"data" 			  => $data
		);

		echo json_encode($output);

	break;

	case "tf_naslovnica_posredovanje": 

		$columns = array(
			0 => 'kandidat_ime',
			1 => 'nalog_naziv',
			2 => 'vrsta_id',
			3 => 'tfs_name',
			4 => 'project_name',
			5 => 'tf_call_appointment',
			6 => 'employee_firstname'
		);

		/* TASK FORCE ZADACI IZ TABELE TASK-FORCE SVE VRSTE */

		$query = '
			(
				SELECT idk_kandidati.kandidat_id, idk_kandidati.kandidat_ime, idk_kandidati.kandidat_prezime, idk_nalozi.nalog_naziv, idk_nalozi.nalog_id, idk_tf_statusi.tfs_name, idk_projects.project_name, idk_projects.project_id, idk_task_force.tf_call_appointment, idk_employees.employee_firstname, idk_employees.employee_lastname, idk_kandidati.tf_reserved_agent, idk_task_force.tf_id, idk_tf_vrste.name AS tf_vrsta_name, idk_task_force.tf_vrsta_id as vrsta_id
				FROM `idk_task_force`
				
				INNER JOIN idk_kandidati  ON idk_task_force.tf_candidate_id = idk_kandidati.kandidat_id
				INNER JOIN idk_nalozi     ON idk_task_force.tf_nalog_id     = idk_nalozi.nalog_id
				INNER JOIN idk_tf_statusi ON idk_task_force.tf_status_id    = idk_tf_statusi.tfs_id
				INNER JOIN idk_projects   ON idk_task_force.tf_project_id   = idk_projects.project_id
				INNER JOIN idk_tf_vrste   ON idk_task_force.tf_vrsta_id     = idk_tf_vrste.id AND idk_tf_vrste.category = 2
				LEFT JOIN idk_employees   ON idk_task_force.tf_agent_id     = idk_employees.employee_id
				
				WHERE idk_task_force.tf_last_active_task = 1
				AND idk_nalozi.nalog_prioritet IS NOT NULL AND tf_vrsta_id != 1 AND idk_kandidati.kandidat_pogresan_broj = 0
				
				
		';

		if (!empty($_POST['search']['value'])) {
			
			$post_search_value = trim($_POST["search"]["value"]);
			
			$query .= ' AND (CONCAT(kandidat_ime, " ", kandidat_prezime) LIKE "%' 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, " ", kandidat_ime) LIKE "%' 	 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, kandidat_ime) LIKE "%' 	     	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     	  . $post_search_value . '%" ';
			$query .= 'OR kandidat_ime LIKE "%'         						 	  . $post_search_value . '%" ';
			$query .= 'OR kandidat_prezime LIKE "%'         					 	  . $post_search_value . '%" ';
			$query .= 'OR nalog_naziv LIKE "%'         							 	  . $post_search_value . '%" ';
			$query .= 'OR tfs_name LIKE "%' 		   							 	  . $post_search_value . '%" ';
			$query .= 'OR idk_tf_vrste.name LIKE "%' 		   						  . $post_search_value . '%" ';
			$query .= 'OR project_name LIKE "%'        							 	  . $post_search_value . '%" ';
			$query .= 'OR tf_call_appointment LIKE "%' 							 	  . $post_search_value . '%" ';
			$query .= 'OR kandidat_mobitel LIKE "%' 							 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(employee_firstname, employee_lastname) LIKE "%' 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(employee_lastname, " ", employee_firstname) LIKE "%' . $post_search_value . '%" ';
			$query .= 'OR CONCAT(employee_lastname, employee_firstname) LIKE "%' 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(employee_firstname, employee_lastname) LIKE "%' 	  . $post_search_value . '%") ';
			
		}

		if (!isset($_POST["order"])) {

			$query .= '
				
				ORDER BY FIELD (idk_tf_statusi.tfs_name, "Pristao", "Zainteresiran", "Dopuna", "Neodlučan", "Neuspješna komunikacija", "Dolazi")
			';

		}

		/* TASK FORCE POSREDOVANJE KANDIDATI KOJI TREBAJU DOCI NA POZIV A NISU U TASK FORCE */

		$query .= '
			)	
			
			UNION ALL
			
			(
				SELECT
						idk_kandidati.kandidat_id, idk_kandidati.kandidat_ime, idk_kandidati.kandidat_prezime, idk_nalozi.nalog_naziv, idk_nalozi.nalog_id,
                        "" AS tfs_name, idk_projects.project_name, idk_projects.project_id, "" AS tf_call_appointment, "" AS employee_firstname, "" AS employee_lastname, idk_kandidati.tf_reserved_agent, "" AS tf_id,
						CASE 
						   WHEN kandidat_status_prijave = 5 THEN "Posredovanje-Odbijen" 
						   WHEN kandidat_status_prijave = 7 THEN "Posredovanje-Ceka ugovor"
						   WHEN kandidat_status_prijave = 8 AND DATE_FORMAT(lsp_datetime, "%Y-%M-%d") <= DATE(NOW() - INTERVAL 1 DAY) THEN "Posredovanje-Poslan ugovor"
						   WHEN kandidat_status_prijave = 8 AND DATE_FORMAT(lsp_datetime, "%Y-%M-%d") <= DATE(NOW() - INTERVAL 7 DAY) THEN "Posredovanje-Time Up za Potpisan ugovor" 
						   WHEN kandidat_status_prijave = 9 THEN "Posredovanje-Potpisan Ugovor"
						END AS tf_vrsta_name,
						CASE 
                            WHEN kandidat_status_prijave = 5 THEN 7 /* Posredovanje-Odbijen (Status prijave Odbijen)*/
            				WHEN kandidat_status_prijave = 7 THEN 8 /* Posredovanje-Ceka ugovor (Status prijave Ceka ugovor)*/
            				WHEN kandidat_status_prijave = 8 AND DATE_FORMAT(lsp_datetime, "%Y-%M-%d") <= DATE(NOW() - INTERVAL 1 DAY) THEN 9 /* Posredovanje-Poslan ugovor (Status prijave Poslan ugovor poslije 1 dan)*/
            				WHEN kandidat_status_prijave = 8 AND DATE_FORMAT(lsp_datetime, "%Y-%M-%d") <= DATE(NOW() - INTERVAL 7 DAY) THEN 10 /* Posredovanje-Time Up za Potpisan ugovor (Status prijave Poslan ugovor poslije 7 dana)*/
            				WHEN kandidat_status_prijave = 9 THEN 11 /* Posredovanje-Potpisan Ugovor (Status prijave Potpisan ugovor)*/
       					END AS vrsta_id	
				FROM
					`idk_kandidati`
				INNER JOIN `idk_log_statusi_prijave` ON `idk_kandidati`.`kandidat_id` = `idk_log_statusi_prijave`.`lsp_kandidat_id` AND idk_log_statusi_prijave.lsp_status_prijave_id IN (5,7,8,9) AND idk_log_statusi_prijave.lsp_broj_dana IS NULL
				INNER JOIN idk_projects ON idk_log_statusi_prijave.lsp_projekt_id = idk_projects.project_id
				INNER JOIN idk_nalozi ON idk_projects.project_nalogid = idk_nalozi.nalog_id
				LEFT JOIN idk_task_force ON idk_task_force.tf_candidate_id = `idk_kandidati`.kandidat_id AND
				(
					(kandidat_status_prijave = 5 AND idk_task_force.tf_vrsta_id = 7) OR
					(kandidat_status_prijave = 7 AND idk_task_force.tf_vrsta_id = 8) OR
					(kandidat_status_prijave = 8 AND DATE_FORMAT(lsp_datetime, "%Y-%M-%d") <= DATE(NOW() - INTERVAL 1 DAY) AND DATE_FORMAT(lsp_datetime, "%Y-%M-%d") > DATE(NOW() - INTERVAL 7 DAY) AND idk_task_force.tf_vrsta_id = 9) OR
					(kandidat_status_prijave = 8 AND DATE_FORMAT(lsp_datetime, "%Y-%M-%d") <= DATE(NOW() - INTERVAL 7 DAY) AND idk_task_force.tf_vrsta_id = 10) OR
					(kandidat_status_prijave = 9 AND idk_task_force.tf_vrsta_id = 11)
				)
				WHERE
					`kandidat_status_prijave` IN (5,7,8,9)
					AND DATE(`lsp_datetime`) <= 
											(CASE 
												WHEN kandidat_status_prijave = 5 THEN DATE(NOW() - INTERVAL 1 DAY)
												WHEN kandidat_status_prijave = 7 THEN DATE(NOW() - INTERVAL 1 DAY) 
												WHEN kandidat_status_prijave = 8 AND lsp_datetime <= DATE(NOW() - INTERVAL 1 DAY) THEN DATE(NOW() - INTERVAL 1 DAY) 
												WHEN kandidat_status_prijave = 8 AND lsp_datetime <= DATE(NOW() - INTERVAL 7 DAY) THEN DATE(NOW() - INTERVAL 7 DAY) 
												WHEN kandidat_status_prijave = 9 THEN DATE(NOW() - INTERVAL 1 DAY) 
											END)
					AND (kandidat_nalog_id IS NOT NULL)
					AND idk_task_force.tf_candidate_id IS NULL
					AND idk_nalozi.nalog_prioritet IS NOT NULL AND idk_kandidati.kandidat_tf_status IS NULL AND idk_kandidati.kandidat_pogresan_broj = 0
		';

		
		
		if (!empty($_POST['search']['value'])) {
			
			$post_search_value = trim($_POST["search"]["value"]);
			
			$query .= ' AND (CONCAT(kandidat_ime, " ", kandidat_prezime) LIKE "%' . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, " ", kandidat_ime) LIKE "%' 	 . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, kandidat_ime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR kandidat_ime LIKE "%'         						 . $post_search_value . '%" ';
			$query .= 'OR kandidat_prezime LIKE "%'         					 . $post_search_value . '%" ';
			$query .= 'OR nalog_naziv LIKE "%'         							 . $post_search_value . '%" ';
			$query .= 'OR project_name LIKE "%'        							 . $post_search_value . '%" ';
			$query .= 'OR CASE 
							WHEN kandidat_status_prijave = 5 THEN "Posredovanje-Odbijen" 
							WHEN kandidat_status_prijave = 7 THEN "Posredovanje-Ceka ugovor"
							WHEN kandidat_status_prijave = 8 AND lsp_datetime <= DATE(NOW() - INTERVAL 1 DAY) THEN "Posredovanje-Poslan ugovor"
							WHEN kandidat_status_prijave = 8 AND lsp_datetime <= DATE(NOW() - INTERVAL 7 DAY) THEN "Posredovanje-Time Up za Potpisan ugovor" 
							WHEN kandidat_status_prijave = 9 THEN "Posredovanje-Potpisan Ugovor"
						END LIKE "%'        							 		. $post_search_value . '%" ';
			$query .= 'OR kandidat_mobitel LIKE "%' 							. $post_search_value . '%") ';
			
		}

		if (!isset($_POST["order"])) {

			$query .= '
				
				ORDER BY idk_projects.project_id DESC
			';

		}

		/* TASK FORCE POSREDOVANJE VEZANO ZA JEZIK */

		$query .= '
			)	
			
			UNION ALL
			
			(
				SELECT
						idk_kandidati.kandidat_id, idk_kandidati.kandidat_ime, idk_kandidati.kandidat_prezime, idk_nalozi.nalog_naziv, idk_nalozi.nalog_id,
                        "" AS tfs_name, idk_projects.project_name, idk_projects.project_id, "" AS tf_call_appointment, "" AS employee_firstname, "" AS employee_lastname, idk_kandidati.tf_reserved_agent, "" AS tf_id,
						CASE 
						   WHEN kandidat_status_prijave = 9 AND kj_slusanje = "A1" AND cvl_status = 3 THEN "Prelazak na veći nivo-A1.1" 
						   WHEN kandidat_status_prijave = 9 AND kj_slusanje = "A1" AND cvl_status = 4 THEN "Prelazak na veći nivo-A1.2"
						   WHEN kandidat_status_prijave = 9 AND kj_slusanje = "A2" AND cvl_status = 3 THEN "Prelazak na veći nivo-A2.1"
						   WHEN kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 4 THEN "Završen kurs-A2.2"
						   WHEN kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 6 AND cvl_exam_date IS NOT NULL THEN "Provjera izlaska na ispit"
						   WHEN kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 7 AND cvl_exam_date IS NOT NULL THEN "Čeka rezultat"
						END AS tf_vrsta_name,
						CASE 
							WHEN kandidat_status_prijave = 9 AND kj_slusanje = "A1" AND cvl_status = 3 THEN 27 
							WHEN kandidat_status_prijave = 9 AND kj_slusanje = "A1" AND cvl_status = 4 THEN 28
							WHEN kandidat_status_prijave = 9 AND kj_slusanje = "A2" AND cvl_status = 3 THEN 29
							WHEN kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 4 THEN 30
							WHEN kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 6 AND cvl_exam_date IS NOT NULL THEN 31
							WHEN kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 7 AND cvl_exam_date IS NOT NULL THEN 32
       					END AS vrsta_id	
				FROM
					`idk_kandidati`
				INNER JOIN `idk_log_statusi_prijave` ON `idk_kandidati`.`kandidat_id` = `idk_log_statusi_prijave`.`lsp_kandidat_id` AND idk_log_statusi_prijave.lsp_status_prijave_id IN (9,12,15,18,21,24) AND idk_log_statusi_prijave.lsp_broj_dana IS NULL
				INNER JOIN idk_projects ON idk_log_statusi_prijave.lsp_projekt_id = idk_projects.project_id
				INNER JOIN idk_nalozi ON idk_projects.project_nalogid = idk_nalozi.nalog_id
				INNER JOIN idk_kandidat_jezici ON idk_kandidati.kandidat_id = idk_kandidat_jezici.kj_kandidatid AND kj_naziv = "Njemacki" AND kj_ustanova IN (1,2,3,4)
    			INNER JOIN idk_candidate_verified_languages ON idk_kandidat_jezici.kj_id = idk_candidate_verified_languages.cvl_id AND idk_candidate_verified_languages.cvl_active = 1
				LEFT JOIN idk_task_force ON idk_task_force.tf_candidate_id = `idk_kandidati`.kandidat_id AND
				(
					(kandidat_status_prijave = 9 AND kj_slusanje = "A1" AND cvl_status = 3 AND idk_task_force.tf_vrsta_id = 27) OR
					(kandidat_status_prijave = 9 AND kj_slusanje = "A1" AND cvl_status = 4 AND idk_task_force.tf_vrsta_id = 28) OR
					(kandidat_status_prijave = 9 AND kj_slusanje = "A2" AND cvl_status = 3 AND idk_task_force.tf_vrsta_id = 29) OR
					(kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 4 AND idk_task_force.tf_vrsta_id = 30) OR
					(kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 6 AND cvl_exam_date IS NOT NULL AND idk_task_force.tf_vrsta_id = 31) OR
					(kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 7 AND cvl_exam_date IS NOT NULL AND idk_task_force.tf_vrsta_id = 32)
				)
				WHERE
					`kandidat_status_prijave` IN (9,12,15,18,21,24)
					AND cvl_status IN (3,4,6,7)
					AND
					(CASE
						WHEN kandidat_status_prijave = 9 AND kj_slusanje = "A1" AND cvl_status = 3 THEN DATE(`cvl_course1_ended`) <= DATE(NOW() - INTERVAL 2 DAY)
						WHEN kandidat_status_prijave = 9 AND kj_slusanje = "A1" AND cvl_status = 4 THEN DATE(`cvl_course2_ended`) <= DATE(NOW() - INTERVAL 2 DAY)
						WHEN kandidat_status_prijave = 9 AND kj_slusanje = "A2" AND cvl_status = 3 THEN DATE(`cvl_course1_ended`) <= DATE(NOW() - INTERVAL 2 DAY)
						WHEN kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 4 THEN DATE(`cvl_course2_ended`) <= DATE(NOW() - INTERVAL 2 DAY)
						WHEN kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 6 AND cvl_exam_date IS NOT NULL THEN DATE(`cvl_exam_date`) <= DATE(NOW() - INTERVAL 1 DAY)
						WHEN kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 7 AND cvl_exam_date IS NOT NULL THEN DATE(`cvl_last_updated`) <= DATE(NOW() - INTERVAL 35 DAY)
					END)
					AND (kandidat_nalog_id IS NOT NULL)
					AND idk_task_force.tf_candidate_id IS NULL
					AND idk_nalozi.nalog_prioritet IS NOT NULL AND idk_kandidati.kandidat_tf_status IS NULL AND idk_kandidati.kandidat_pogresan_broj = 0
		';
		
		if (!empty($_POST['search']['value'])) {
			
			$post_search_value = trim($_POST["search"]["value"]);
			
			$query .= ' AND (CONCAT(kandidat_ime, " ", kandidat_prezime) LIKE "%' . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, " ", kandidat_ime) LIKE "%' 	 . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, kandidat_ime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR kandidat_ime LIKE "%'         						 . $post_search_value . '%" ';
			$query .= 'OR kandidat_prezime LIKE "%'         					 . $post_search_value . '%" ';
			$query .= 'OR nalog_naziv LIKE "%'         							 . $post_search_value . '%" ';
			$query .= 'OR project_name LIKE "%'        							 . $post_search_value . '%" ';
			$query .= 'OR CASE 
							WHEN kandidat_status_prijave = 9 AND kj_slusanje = "A1" AND cvl_status = 3 THEN "Prelazak na veći nivo-A1.1" 
							WHEN kandidat_status_prijave = 9 AND kj_slusanje = "A1" AND cvl_status = 4 THEN "Prelazak na veći nivo-A1.2"
							WHEN kandidat_status_prijave = 9 AND kj_slusanje = "A2" AND cvl_status = 3 THEN "Prelazak na veći nivo-A2.1"
							WHEN kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 4 THEN "Završen kurs-A2.2" 
							WHEN kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 6 AND cvl_exam_date IS NOT NULL THEN "Provjera izlaska na ispit"
							WHEN kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 7 AND cvl_exam_date IS NOT NULL THEN "Čeka rezultat"
						END LIKE "%'        							 		. $post_search_value . '%" ';
			$query .= 'OR kandidat_mobitel LIKE "%' 							. $post_search_value . '%") ';
			
		}

		if (!isset($_POST["order"])) {

			$query .= '
				
				ORDER BY idk_projects.project_id DESC
			';

		}

		/* TASK FORCE POSREDOVANJE APLICIRAJ ZA ZAPADNI BALKAN */

		$query .= '
			)	
			
			UNION ALL
			
			(
				SELECT
						idk_kandidati.kandidat_id, idk_kandidati.kandidat_ime, idk_kandidati.kandidat_prezime, idk_nalozi.nalog_naziv, idk_nalozi.nalog_id,
                        "" AS tfs_name, idk_projects.project_name, idk_projects.project_id, "" AS tf_call_appointment, "" AS employee_firstname, "" AS employee_lastname, idk_kandidati.tf_reserved_agent, "" AS tf_id,
						"Apliciraj za zapadni balkan" AS tf_vrsta_name,
						33 AS vrsta_id	
				FROM
					`idk_kandidati`
				INNER JOIN `idk_log_statusi_prijave` ON `idk_kandidati`.`kandidat_id` = `idk_log_statusi_prijave`.`lsp_kandidat_id` AND idk_log_statusi_prijave.lsp_status_prijave_id = 9 AND idk_log_statusi_prijave.lsp_broj_dana IS NULL
				INNER JOIN idk_projects ON idk_log_statusi_prijave.lsp_projekt_id = idk_projects.project_id
				INNER JOIN idk_nalozi ON idk_projects.project_nalogid = idk_nalozi.nalog_id
				LEFT JOIN idk_task_force ON idk_task_force.tf_candidate_id = `idk_kandidati`.kandidat_id AND idk_task_force.tf_vrsta_id = 33 AND (idk_task_force.tf_status_id = 139 OR (YEAR(idk_task_force.tf_doe) = YEAR(CURRENT_DATE) AND MONTH(idk_task_force.tf_doe) = MONTH(CURRENT_DATE))) AND idk_projects.project_nalogid = idk_task_force.tf_nalog_id
				WHERE
					`kandidat_status_prijave` = 9
					AND ((kandidat_nacin_odlaska = 2 AND datum_termina IS NULL) OR (kandidat_nacin_odlaska = 0 AND kandidat_paralelno_zb = 1))
					AND kandidat_drzavljanstvo_vrsta != "EU državljanin"
					AND (kandidat_nalog_id IS NOT NULL)
					AND idk_task_force.tf_candidate_id IS NULL
					AND idk_nalozi.nalog_prioritet IS NOT NULL AND idk_kandidati.kandidat_tf_status IS NULL AND idk_kandidati.kandidat_pogresan_broj = 0
		';
		
		if (!empty($_POST['search']['value'])) {
			
			$post_search_value = trim($_POST["search"]["value"]);
			
			$query .= ' AND (CONCAT(kandidat_ime, " ", kandidat_prezime) LIKE "%' . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, " ", kandidat_ime) LIKE "%' 	 . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, kandidat_ime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR kandidat_ime LIKE "%'         						 . $post_search_value . '%" ';
			$query .= 'OR kandidat_prezime LIKE "%'         					 . $post_search_value . '%" ';
			$query .= 'OR nalog_naziv LIKE "%'         							 . $post_search_value . '%" ';
			$query .= 'OR project_name LIKE "%'        							 . $post_search_value . '%" ';
			$query .= 'OR "Apliciraj za zapadni balkan" LIKE "%'        		. $post_search_value . '%" ';
			$query .= 'OR kandidat_mobitel LIKE "%' 							. $post_search_value . '%") ';
			
		}

		if (!isset($_POST["order"])) {

			$query .= '
				
				ORDER BY idk_projects.project_id DESC
			';

		}

		$query .= ')';

		if (isset($_POST["order"])) {
			if ($_POST["order"][0]['column'] !== 0) {

				$query .= ' 
					ORDER BY '. $columns[$_POST["order"][0]["column"]]. ' '. $_POST["order"]["0"]["dir"] . ' 
				';

			}
		}

		if ($_POST["length"] != -1) {
			$query .= ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$query_prepared = $db->prepare($query);
		$query_prepared->execute();
		
		$rows = $query_prepared->fetchAll();
		$data = array();

		foreach ($rows as $row) {

			$sub_array[0] = '<a href="kandidati?page=open&id=' . $row["kandidat_id"] . '&projekt_id=' . $row["project_id"] . '&nalog_id=' . $row["nalog_id"] . '&vrsta_id='. $row["vrsta_id"]. '">' . $row["kandidat_ime"] . " " . $row["kandidat_prezime"] . '</a>';
			$sub_array[1] = $row['nalog_naziv'];
			$sub_array[2] = '<span data-order="' . $row['tf_vrsta_name'] . '">' . $row['tf_vrsta_name'] . '</span>';
			$sub_array[3] = $row['tfs_name'];
			$sub_array[4] = $row['project_name'];
			$sub_array[5] = $row['tf_call_appointment'];
			$sub_array[6] = $row['employee_firstname'] . " " . $row['employee_lastname'];
			$sub_array[7] = $row['kandidat_id'];
			$sub_array[8] = $row['tf_reserved_agent'];
			$sub_array[9] = $row['tf_id'];

			
			$data[] = $sub_array;

		}

		$query = '
			(
				SELECT idk_kandidati.kandidat_id, idk_kandidati.kandidat_ime, idk_kandidati.kandidat_prezime, idk_nalozi.nalog_naziv, idk_nalozi.nalog_id, idk_tf_statusi.tfs_name, idk_projects.project_name, idk_projects.project_id, idk_task_force.tf_call_appointment, idk_employees.employee_firstname, idk_employees.employee_lastname, idk_kandidati.tf_reserved_agent, idk_task_force.tf_id, idk_tf_vrste.name AS tf_vrsta_name, idk_task_force.tf_vrsta_id AS vrsta_id
				FROM `idk_task_force`
				
				INNER JOIN idk_kandidati  ON idk_task_force.tf_candidate_id = idk_kandidati.kandidat_id
				INNER JOIN idk_nalozi     ON idk_task_force.tf_nalog_id     = idk_nalozi.nalog_id
				INNER JOIN idk_tf_statusi ON idk_task_force.tf_status_id    = idk_tf_statusi.tfs_id
				INNER JOIN idk_projects   ON idk_task_force.tf_project_id   = idk_projects.project_id
				INNER JOIN idk_tf_vrste   ON idk_task_force.tf_vrsta_id     = idk_tf_vrste.id AND idk_tf_vrste.category = 2
				LEFT JOIN idk_employees   ON idk_task_force.tf_agent_id     = idk_employees.employee_id
				
				WHERE idk_task_force.tf_last_active_task = 1
				AND idk_nalozi.nalog_prioritet IS NOT NULL AND tf_vrsta_id != 1 AND idk_kandidati.kandidat_pogresan_broj = 0
				
		';

		if (!empty($_POST['search']['value'])) {
			
			$post_search_value = trim($_POST["search"]["value"]);
			
			$query .= ' AND (CONCAT(kandidat_ime, " ", kandidat_prezime) LIKE "%' 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, " ", kandidat_ime) LIKE "%' 	 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, kandidat_ime) LIKE "%' 	     	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     	  . $post_search_value . '%" ';
			$query .= 'OR kandidat_ime LIKE "%'         						 	  . $post_search_value . '%" ';
			$query .= 'OR kandidat_prezime LIKE "%'         					 	  . $post_search_value . '%" ';
			$query .= 'OR nalog_naziv LIKE "%'         							 	  . $post_search_value . '%" ';
			$query .= 'OR tfs_name LIKE "%' 		   							 	  . $post_search_value . '%" ';
			$query .= 'OR idk_tf_vrste.name LIKE "%' 		   						  . $post_search_value . '%" ';
			$query .= 'OR project_name LIKE "%'        							 	  . $post_search_value . '%" ';
			$query .= 'OR tf_call_appointment LIKE "%' 							 	  . $post_search_value . '%" ';
			$query .= 'OR kandidat_mobitel LIKE "%' 							 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(employee_firstname, employee_lastname) LIKE "%' 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(employee_lastname, " ", employee_firstname) LIKE "%' . $post_search_value . '%" ';
			$query .= 'OR CONCAT(employee_lastname, employee_firstname) LIKE "%' 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(employee_firstname, employee_lastname) LIKE "%' 	  . $post_search_value . '%") ';
			
		}

		$query .= '
			)	
			
			UNION ALL
			
			(
				SELECT
						idk_kandidati.kandidat_id, idk_kandidati.kandidat_ime, idk_kandidati.kandidat_prezime, idk_nalozi.nalog_naziv, idk_nalozi.nalog_id,
                        "" AS tfs_name, idk_projects.project_name, idk_projects.project_id, "" AS tf_call_appointment, "" AS employee_firstname, "" AS employee_lastname, idk_kandidati.tf_reserved_agent, "" AS tf_id,
						CASE 
						   WHEN kandidat_status_prijave = 5 THEN "Posredovanje-Odbijen" 
						   WHEN kandidat_status_prijave = 7 THEN "Posredovanje-Ceka ugovor"
						   WHEN kandidat_status_prijave = 8 AND lsp_datetime <= DATE(NOW() - INTERVAL 1 DAY) THEN "Posredovanje-Poslan ugovor"
						   WHEN kandidat_status_prijave = 8 AND lsp_datetime <= DATE(NOW() - INTERVAL 7 DAY) THEN "Posredovanje-Time Up za Potpisan ugovor" 
						   WHEN kandidat_status_prijave = 9 THEN "Posredovanje-Potpisan Ugovor"
						END AS tf_vrsta_name,
						CASE 
                            WHEN kandidat_status_prijave = 5 THEN 7 /* Posredovanje-Odbijen (Status prijave Odbijen)*/
            				WHEN kandidat_status_prijave = 7 THEN 8 /* Posredovanje-Ceka ugovor (Status prijave Ceka ugovor)*/
            				WHEN kandidat_status_prijave = 8 AND lsp_datetime <= DATE(NOW() - INTERVAL 1 DAY) THEN 9 /* Posredovanje-Poslan ugovor (Status prijave Poslan ugovor poslije 1 dan)*/
            				WHEN kandidat_status_prijave = 8 AND lsp_datetime <= DATE(NOW() - INTERVAL 7 DAY) THEN 10 /* Posredovanje-Time Up za Potpisan ugovor (Status prijave Poslan ugovor poslije 7 dana)*/
            				WHEN kandidat_status_prijave = 9 THEN 11 /* Posredovanje-Potpisan Ugovor (Status prijave Potpisan ugovor)*/
       					END AS vrsta_id	
				FROM
					`idk_kandidati`
				INNER JOIN `idk_log_statusi_prijave` ON `idk_kandidati`.`kandidat_id` = `idk_log_statusi_prijave`.`lsp_kandidat_id` AND idk_log_statusi_prijave.lsp_status_prijave_id IN (5,7,8,9) AND idk_log_statusi_prijave.lsp_broj_dana IS NULL
				INNER JOIN idk_projects ON idk_log_statusi_prijave.lsp_projekt_id = idk_projects.project_id
				INNER JOIN idk_nalozi ON idk_projects.project_nalogid = idk_nalozi.nalog_id
				LEFT JOIN idk_task_force ON idk_task_force.tf_candidate_id = `idk_kandidati`.kandidat_id AND
				(
					(kandidat_status_prijave = 5 AND idk_task_force.tf_vrsta_id = 7) OR
					(kandidat_status_prijave = 7 AND idk_task_force.tf_vrsta_id = 8) OR
					(kandidat_status_prijave = 8 AND lsp_datetime <= DATE(NOW() - INTERVAL 1 DAY) AND lsp_datetime > DATE(NOW() - INTERVAL 7 DAY) AND idk_task_force.tf_vrsta_id = 9) OR					(kandidat_status_prijave = 8 AND lsp_datetime <= DATE(NOW() - INTERVAL 7 DAY) AND idk_task_force.tf_vrsta_id = 10) OR
					(kandidat_status_prijave = 9 AND idk_task_force.tf_vrsta_id = 11)
				)
				WHERE
					`kandidat_status_prijave` IN (5,7,8,9)
					AND DATE(`lsp_datetime`) <= 
											(CASE 
												WHEN kandidat_status_prijave = 5 THEN DATE(NOW() - INTERVAL 1 DAY)
												WHEN kandidat_status_prijave = 7 THEN DATE(NOW() - INTERVAL 1 DAY) 
												WHEN kandidat_status_prijave = 8 AND lsp_datetime <= DATE(NOW() - INTERVAL 1 DAY) THEN DATE(NOW() - INTERVAL 1 DAY) 
												WHEN kandidat_status_prijave = 8 AND lsp_datetime <= DATE(NOW() - INTERVAL 7 DAY) THEN DATE(NOW() - INTERVAL 7 DAY) 
												WHEN kandidat_status_prijave = 9 THEN DATE(NOW() - INTERVAL 1 DAY) 
											END)
					AND (kandidat_nalog_id IS NOT NULL)
					AND idk_task_force.tf_candidate_id IS NULL
					AND idk_nalozi.nalog_prioritet IS NOT NULL AND idk_kandidati.kandidat_tf_status IS NULL AND idk_kandidati.kandidat_pogresan_broj = 0
		';

		
		
		if (!empty($_POST['search']['value'])) {
			
			$post_search_value = trim($_POST["search"]["value"]);
			
			$query .= ' AND (CONCAT(kandidat_ime, " ", kandidat_prezime) LIKE "%' . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, " ", kandidat_ime) LIKE "%' 	 . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, kandidat_ime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR kandidat_ime LIKE "%'         						 . $post_search_value . '%" ';
			$query .= 'OR kandidat_prezime LIKE "%'         					 . $post_search_value . '%" ';
			$query .= 'OR nalog_naziv LIKE "%'         							 . $post_search_value . '%" ';
			$query .= 'OR project_name LIKE "%'        							 . $post_search_value . '%" ';
			$query .= 'OR CASE 
							WHEN kandidat_status_prijave = 5 THEN "Posredovanje-Odbijen" 
							WHEN kandidat_status_prijave = 7 THEN "Posredovanje-Ceka ugovor"
							WHEN kandidat_status_prijave = 8 AND lsp_datetime <= DATE(NOW() - INTERVAL 1 DAY) THEN "Posredovanje-Poslan ugovor"
							WHEN kandidat_status_prijave = 8 AND lsp_datetime <= DATE(NOW() - INTERVAL 7 DAY) THEN "Posredovanje-Time Up za Potpisan ugovor" 
							WHEN kandidat_status_prijave = 9 THEN "Posredovanje-Potpisan Ugovor"
						END LIKE "%'        							 		. $post_search_value . '%" ';
			$query .= 'OR kandidat_mobitel LIKE "%' 							. $post_search_value . '%") ';
			
		}

		/* TASK FORCE POSREDOVANJE VEZANO ZA JEZIK */

		$query .= '
			)	
			
			UNION ALL
			
			(
				SELECT
						idk_kandidati.kandidat_id, idk_kandidati.kandidat_ime, idk_kandidati.kandidat_prezime, idk_nalozi.nalog_naziv, idk_nalozi.nalog_id,
                        "" AS tfs_name, idk_projects.project_name, idk_projects.project_id, "" AS tf_call_appointment, "" AS employee_firstname, "" AS employee_lastname, idk_kandidati.tf_reserved_agent, "" AS tf_id,
						CASE 
						   WHEN kandidat_status_prijave = 9 AND kj_slusanje = "A1" AND cvl_status = 3 THEN "Prelazak na veći nivo-A1.1" 
						   WHEN kandidat_status_prijave = 9 AND kj_slusanje = "A1" AND cvl_status = 4 THEN "Prelazak na veći nivo-A1.2"
						   WHEN kandidat_status_prijave = 9 AND kj_slusanje = "A2" AND cvl_status = 3 THEN "Prelazak na veći nivo-A2.1"
						   WHEN kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 4 THEN "Završen kurs-A2.2"
						   WHEN kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 6 AND cvl_exam_date IS NOT NULL THEN "Provjera izlaska na ispit"
						   WHEN kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 7 AND cvl_exam_date IS NOT NULL THEN "Čeka rezultat"
						END AS tf_vrsta_name,
						CASE 
							WHEN kandidat_status_prijave = 9 AND kj_slusanje = "A1" AND cvl_status = 3 THEN 27 
							WHEN kandidat_status_prijave = 9 AND kj_slusanje = "A1" AND cvl_status = 4 THEN 28
							WHEN kandidat_status_prijave = 9 AND kj_slusanje = "A2" AND cvl_status = 3 THEN 29
							WHEN kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 4 THEN 30
							WHEN kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 6 AND cvl_exam_date IS NOT NULL THEN 31
							WHEN kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 7 AND cvl_exam_date IS NOT NULL THEN 32
       					END AS vrsta_id	
				FROM
					`idk_kandidati`
				INNER JOIN `idk_log_statusi_prijave` ON `idk_kandidati`.`kandidat_id` = `idk_log_statusi_prijave`.`lsp_kandidat_id` AND idk_log_statusi_prijave.lsp_status_prijave_id IN (9,12,15,18,21,24) AND idk_log_statusi_prijave.lsp_broj_dana IS NULL
				INNER JOIN idk_projects ON idk_log_statusi_prijave.lsp_projekt_id = idk_projects.project_id
				INNER JOIN idk_nalozi ON idk_projects.project_nalogid = idk_nalozi.nalog_id
				INNER JOIN idk_kandidat_jezici ON idk_kandidati.kandidat_id = idk_kandidat_jezici.kj_kandidatid AND kj_naziv = "Njemacki" AND kj_ustanova IN (1,2,3,4)
    			INNER JOIN idk_candidate_verified_languages ON idk_kandidat_jezici.kj_id = idk_candidate_verified_languages.cvl_id AND idk_candidate_verified_languages.cvl_active = 1
				LEFT JOIN idk_task_force ON idk_task_force.tf_candidate_id = `idk_kandidati`.kandidat_id AND
				(
					(kandidat_status_prijave = 9 AND kj_slusanje = "A1" AND cvl_status = 3 AND idk_task_force.tf_vrsta_id = 27) OR
					(kandidat_status_prijave = 9 AND kj_slusanje = "A1" AND cvl_status = 4 AND idk_task_force.tf_vrsta_id = 28) OR
					(kandidat_status_prijave = 9 AND kj_slusanje = "A2" AND cvl_status = 3 AND idk_task_force.tf_vrsta_id = 29) OR
					(kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 4 AND idk_task_force.tf_vrsta_id = 30) OR
					(kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 6 AND cvl_exam_date IS NOT NULL AND idk_task_force.tf_vrsta_id = 31) OR
					(kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 7 AND cvl_exam_date IS NOT NULL AND idk_task_force.tf_vrsta_id = 32)
				)
				WHERE
					`kandidat_status_prijave` IN (9,12,15,18,21,24)
					AND cvl_status IN (3,4,6,7)
					AND
					(CASE
						WHEN kandidat_status_prijave = 9 AND kj_slusanje = "A1" AND cvl_status = 3 THEN DATE(`cvl_course1_ended`) <= DATE(NOW() - INTERVAL 2 DAY)
						WHEN kandidat_status_prijave = 9 AND kj_slusanje = "A1" AND cvl_status = 4 THEN DATE(`cvl_course2_ended`) <= DATE(NOW() - INTERVAL 2 DAY)
						WHEN kandidat_status_prijave = 9 AND kj_slusanje = "A2" AND cvl_status = 3 THEN DATE(`cvl_course1_ended`) <= DATE(NOW() - INTERVAL 2 DAY)
						WHEN kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 4 THEN DATE(`cvl_course2_ended`) <= DATE(NOW() - INTERVAL 2 DAY)
						WHEN kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 6 AND cvl_exam_date IS NOT NULL THEN DATE(`cvl_exam_date`) <= DATE(NOW() - INTERVAL 1 DAY)
						WHEN kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 7 AND cvl_exam_date IS NOT NULL THEN DATE(`cvl_last_updated`) <= DATE(NOW() - INTERVAL 35 DAY)
					END)
					AND (kandidat_nalog_id IS NOT NULL)
					AND idk_task_force.tf_candidate_id IS NULL
					AND idk_nalozi.nalog_prioritet IS NOT NULL AND idk_kandidati.kandidat_tf_status IS NULL AND idk_kandidati.kandidat_pogresan_broj = 0
		';
		
		if (!empty($_POST['search']['value'])) {
			
			$post_search_value = trim($_POST["search"]["value"]);
			
			$query .= ' AND (CONCAT(kandidat_ime, " ", kandidat_prezime) LIKE "%' . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, " ", kandidat_ime) LIKE "%' 	 . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, kandidat_ime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR kandidat_ime LIKE "%'         						 . $post_search_value . '%" ';
			$query .= 'OR kandidat_prezime LIKE "%'         					 . $post_search_value . '%" ';
			$query .= 'OR nalog_naziv LIKE "%'         							 . $post_search_value . '%" ';
			$query .= 'OR project_name LIKE "%'        							 . $post_search_value . '%" ';
			$query .= 'OR CASE 
							WHEN kandidat_status_prijave = 9 AND kj_slusanje = "A1" AND cvl_status = 3 THEN "Prelazak na veći nivo-A1.1" 
							WHEN kandidat_status_prijave = 9 AND kj_slusanje = "A1" AND cvl_status = 4 THEN "Prelazak na veći nivo-A1.2"
							WHEN kandidat_status_prijave = 9 AND kj_slusanje = "A2" AND cvl_status = 3 THEN "Prelazak na veći nivo-A2.1"
							WHEN kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 4 THEN "Završen kurs-A2.2" 
							WHEN kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 6 AND cvl_exam_date IS NOT NULL THEN "Provjera izlaska na ispit"
							WHEN kandidat_status_prijave IN (9,12,15,18,21,24) AND kj_slusanje = "A2" AND cvl_status = 7 AND cvl_exam_date IS NOT NULL THEN "Čeka rezultat"
						END LIKE "%'        							 		. $post_search_value . '%" ';
			$query .= 'OR kandidat_mobitel LIKE "%' 							. $post_search_value . '%") ';
			
		}

		/* TASK FORCE POSREDOVANJE APLICIRAJ ZA ZAPADNI BALKAN */

		$query .= '
			)	
			
			UNION ALL
			
			(
				SELECT
						idk_kandidati.kandidat_id, idk_kandidati.kandidat_ime, idk_kandidati.kandidat_prezime, idk_nalozi.nalog_naziv, idk_nalozi.nalog_id,
                        "" AS tfs_name, idk_projects.project_name, idk_projects.project_id, "" AS tf_call_appointment, "" AS employee_firstname, "" AS employee_lastname, idk_kandidati.tf_reserved_agent, "" AS tf_id,
						"Apliciraj za zapadni balkan" AS tf_vrsta_name,
						33 AS vrsta_id	
				FROM
					`idk_kandidati`
				INNER JOIN `idk_log_statusi_prijave` ON `idk_kandidati`.`kandidat_id` = `idk_log_statusi_prijave`.`lsp_kandidat_id` AND idk_log_statusi_prijave.lsp_status_prijave_id = 9 AND idk_log_statusi_prijave.lsp_broj_dana IS NULL
				INNER JOIN idk_projects ON idk_log_statusi_prijave.lsp_projekt_id = idk_projects.project_id
				INNER JOIN idk_nalozi ON idk_projects.project_nalogid = idk_nalozi.nalog_id
				LEFT JOIN idk_task_force ON idk_task_force.tf_candidate_id = `idk_kandidati`.kandidat_id AND idk_task_force.tf_vrsta_id = 33 AND (idk_task_force.tf_status_id = 139 OR (YEAR(idk_task_force.tf_doe) = YEAR(CURRENT_DATE) AND MONTH(idk_task_force.tf_doe) = MONTH(CURRENT_DATE))) AND idk_projects.project_nalogid = idk_task_force.tf_nalog_id
				WHERE
					`kandidat_status_prijave` = 9
					AND ((kandidat_nacin_odlaska = 2 AND datum_termina IS NULL) OR (kandidat_nacin_odlaska = 0 AND kandidat_paralelno_zb = 1))
					AND kandidat_drzavljanstvo_vrsta != "EU državljanin"
					AND (kandidat_nalog_id IS NOT NULL)
					AND idk_task_force.tf_candidate_id IS NULL
					AND idk_nalozi.nalog_prioritet IS NOT NULL AND idk_kandidati.kandidat_tf_status IS NULL AND idk_kandidati.kandidat_pogresan_broj = 0
		';
		
		if (!empty($_POST['search']['value'])) {
			
			$post_search_value = trim($_POST["search"]["value"]);
			
			$query .= ' AND (CONCAT(kandidat_ime, " ", kandidat_prezime) LIKE "%' . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, " ", kandidat_ime) LIKE "%' 	 . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, kandidat_ime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR kandidat_ime LIKE "%'         						 . $post_search_value . '%" ';
			$query .= 'OR kandidat_prezime LIKE "%'         					 . $post_search_value . '%" ';
			$query .= 'OR nalog_naziv LIKE "%'         							 . $post_search_value . '%" ';
			$query .= 'OR project_name LIKE "%'        							 . $post_search_value . '%" ';
			$query .= 'OR "Apliciraj za zapadni balkan" LIKE "%'        		. $post_search_value . '%" ';
			$query .= 'OR kandidat_mobitel LIKE "%' 							. $post_search_value . '%") ';
			
		}

		$query .= ')';

		$query_prepared = $db->prepare($query);
		$query_prepared->execute();

		$recordsTotal  = $query_prepared->rowCount();
		$filtered_rows = $recordsTotal;
		
		$output = array(
			"draw" 			  => intval($_POST["draw"]),
			"recordsTotal"    => $recordsTotal,
			"recordsFiltered" => $filtered_rows,
			"data" 			  => $data
		);

		echo json_encode($output);

	break;

	case "tf_naslovnica_obrada": 

		$columns = array(
			0 => 'kandidat_ime',
			1 => 'nalog_naziv',
			2 => 'vrsta_id',
			3 => 'tfs_name',
			4 => 'project_name',
			5 => 'tf_call_appointment',
			6 => 'employee_firstname'
		);

		/* TASK FORCE ZADACI IZ TABELE TASK-FORCE ZA OBRADU KATEGORIJA VRSTE = 3 (OBRADA) */

		$query = '
			(
				SELECT idk_kandidati.kandidat_id, idk_kandidati.kandidat_dipl_id, idk_kandidati.kandidat_ime, idk_kandidati.kandidat_prezime, "" AS nalog_naziv, tf_nalog_id AS nalog_id, idk_tf_statusi.tfs_name, "" AS project_name, "" AS project_id, idk_task_force.tf_call_appointment, idk_employees.employee_firstname, idk_employees.employee_lastname, idk_kandidati.tf_reserved_agent, idk_task_force.tf_id, idk_tf_vrste.name AS tf_vrsta_name, idk_task_force.tf_vrsta_id as vrsta_id
				FROM `idk_task_force`
				
				INNER JOIN idk_kandidati  ON idk_task_force.tf_candidate_id = idk_kandidati.kandidat_id
				INNER JOIN idk_tf_statusi ON idk_task_force.tf_status_id    = idk_tf_statusi.tfs_id
				INNER JOIN idk_tf_vrste   ON idk_task_force.tf_vrsta_id     = idk_tf_vrste.id
				LEFT JOIN idk_employees   ON idk_task_force.tf_agent_id     = idk_employees.employee_id
				
				WHERE idk_task_force.tf_last_active_task = 1
				AND tf_vrsta_id != 1 AND idk_tf_vrste.category = 3 AND idk_kandidati.kandidat_pogresan_broj = 0
				
				
		';

		if (!empty($_POST['search']['value'])) {
			
			$post_search_value = trim($_POST["search"]["value"]);
			
			$query .= ' AND (CONCAT(kandidat_ime, " ", kandidat_prezime) LIKE "%' 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, " ", kandidat_ime) LIKE "%' 	 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, kandidat_ime) LIKE "%' 	     	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     	  . $post_search_value . '%" ';
			$query .= 'OR kandidat_ime LIKE "%'         						 	  . $post_search_value . '%" ';
			$query .= 'OR kandidat_prezime LIKE "%'         					 	  . $post_search_value . '%" ';
			$query .= 'OR tfs_name LIKE "%' 		   							 	  . $post_search_value . '%" ';
			$query .= 'OR idk_tf_vrste.name LIKE "%' 		   						  . $post_search_value . '%" ';
			$query .= 'OR tf_call_appointment LIKE "%' 							 	  . $post_search_value . '%" ';
			$query .= 'OR kandidat_mobitel LIKE "%' 							 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(employee_firstname, employee_lastname) LIKE "%' 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(employee_lastname, " ", employee_firstname) LIKE "%' . $post_search_value . '%" ';
			$query .= 'OR CONCAT(employee_lastname, employee_firstname) LIKE "%' 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(employee_firstname, employee_lastname) LIKE "%' 	  . $post_search_value . '%") ';
			
		}

		/* TASK FORCE OBRADA 13 */

		$query .= '
			)	
			
			UNION ALL
			
			(
				SELECT
						idk_kandidati.kandidat_id, idk_kandidati.kandidat_dipl_id, idk_kandidati.kandidat_ime, idk_kandidati.kandidat_prezime, "" AS nalog_naziv, "" AS nalog_id,
                        "" AS tfs_name, "" AS project_name, "" AS project_id, "" AS tf_call_appointment, "" AS employee_firstname, "" AS employee_lastname, idk_kandidati.tf_reserved_agent, "" AS tf_id,
						"Povezi sa ustanovom i posalji obavijest" AS tf_vrsta_name,
						13 AS vrsta_id	
				FROM
					`idk_kandidati`
              	INNER JOIN idk_nd_kandidata ON idk_kandidati.kandidat_dipl_id = idk_nd_kandidata.id_broj_nd_kandidata
				INNER JOIN `idk_nd_kandidata_status_log` ON `idk_nd_kandidata`.`id_broj_nd_kandidata` = `idk_nd_kandidata_status_log`.`idd_broj_nd_kandidata` AND idk_nd_kandidata_status_log.status_nd_kandidata IN (2,3,4,5,6) AND idk_nd_kandidata_status_log.pstatus_nd_kandidata IN (1,2,5,6,7) AND idk_nd_kandidata_status_log.broj_dana_statusa_nd_kandidata IS NULL
				LEFT JOIN idk_nd_ustanove ON idk_nd_kandidata.idd_ustanova_nd = idk_nd_ustanove.id_ustanove_nd
				LEFT JOIN idk_task_force ON idk_task_force.tf_candidate_id = `idk_kandidati`.kandidat_id AND idk_task_force.tf_vrsta_id = 13
				WHERE
					idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 1
					AND DATE(`vrijeme_promjene_statusa_nd_kandidata`) <= DATE(NOW() - INTERVAL 0 DAY)
					AND idk_task_force.tf_candidate_id IS NULL
                    AND idk_kandidati.kandidat_dipl_id IS NOT NULL
					AND idk_kandidati.kandidat_tf_status IS NULL AND idk_kandidati.kandidat_pogresan_broj = 0
		';

		if (!empty($_POST['search']['value'])) {
			
			$post_search_value = trim($_POST["search"]["value"]);
			
			$query .= ' AND (CONCAT(kandidat_ime, " ", kandidat_prezime) LIKE "%' . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, " ", kandidat_ime) LIKE "%' 	 . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, kandidat_ime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR kandidat_ime LIKE "%'         						 . $post_search_value . '%" ';
			$query .= 'OR kandidat_prezime LIKE "%'         					 . $post_search_value . '%" ';
			$query .= 'OR "Povezi sa ustanovom i posalji obavijest" LIKE "%'     . $post_search_value . '%" ';
			$query .= 'OR kandidat_mobitel LIKE "%' 							 . $post_search_value . '%") ';
			
		}

		if (!isset($_POST["order"])) {

			$query .= '
				
				ORDER BY idk_kandidati.kandidat_id DESC
			';

		}

		/* TASK FORCE OBRADA 14 */

		$query .= '
			)	
			
			UNION ALL
			
			(
				SELECT
						idk_kandidati.kandidat_id, idk_kandidati.kandidat_dipl_id, idk_kandidati.kandidat_ime, idk_kandidati.kandidat_prezime, "" AS nalog_naziv, "" AS nalog_id,
                        "" AS tfs_name, "" AS project_name, "" AS project_id, "" AS tf_call_appointment, "" AS employee_firstname, "" AS employee_lastname, idk_kandidati.tf_reserved_agent, "" AS tf_id,
						"Cekamo dokumentaciju" AS tf_vrsta_name,
						14 AS vrsta_id	
				FROM
					`idk_kandidati`
              	INNER JOIN idk_nd_kandidata ON idk_kandidati.kandidat_dipl_id = idk_nd_kandidata.id_broj_nd_kandidata
				INNER JOIN `idk_nd_kandidata_status_log` ON `idk_nd_kandidata`.`id_broj_nd_kandidata` = `idk_nd_kandidata_status_log`.`idd_broj_nd_kandidata` AND idk_nd_kandidata_status_log.status_nd_kandidata IN (2,3,4,5,6) AND idk_nd_kandidata_status_log.pstatus_nd_kandidata IN (1,2,5,6,7) AND idk_nd_kandidata_status_log.broj_dana_statusa_nd_kandidata IS NULL
				LEFT JOIN idk_nd_ustanove ON idk_nd_kandidata.idd_ustanova_nd = idk_nd_ustanove.id_ustanove_nd
				LEFT JOIN idk_task_force ON idk_task_force.tf_candidate_id = `idk_kandidati`.kandidat_id AND idk_task_force.tf_vrsta_id = 14
				JOIN idk_task_force tf2 ON tf2.tf_candidate_id = `idk_kandidati`.kandidat_id AND tf2.tf_vrsta_id = 13 AND tf2.tf_status_id = 50
				WHERE
					idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 1
					AND DATE(`vrijeme_promjene_statusa_nd_kandidata`) <= DATE(NOW() - INTERVAL 7 DAY)
					AND idk_task_force.tf_candidate_id IS NULL
                    AND idk_kandidati.kandidat_dipl_id IS NOT NULL
					AND idk_kandidati.kandidat_tf_status IS NULL AND idk_kandidati.kandidat_pogresan_broj = 0
		';

		if (!empty($_POST['search']['value'])) {
			
			$post_search_value = trim($_POST["search"]["value"]);
			
			$query .= ' AND (CONCAT(kandidat_ime, " ", kandidat_prezime) LIKE "%' . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, " ", kandidat_ime) LIKE "%' 	 . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, kandidat_ime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR kandidat_ime LIKE "%'         						 . $post_search_value . '%" ';
			$query .= 'OR kandidat_prezime LIKE "%'         					 . $post_search_value . '%" ';
			$query .= 'OR "Cekamo dokumentaciju" LIKE "%'                        . $post_search_value . '%" ';
			$query .= 'OR kandidat_mobitel LIKE "%' 							 . $post_search_value . '%") ';
			
		}

		if (!isset($_POST["order"])) {

			$query .= '
				
				ORDER BY idk_kandidati.kandidat_id DESC
			';

		}

		/* TASK FORCE OBRADA 19 */

		$query .= '
			)	
			
			UNION ALL
			
			(
				SELECT
						idk_kandidati.kandidat_id, idk_kandidati.kandidat_dipl_id, idk_kandidati.kandidat_ime, idk_kandidati.kandidat_prezime, "" AS nalog_naziv, "" AS nalog_id,
                        "" AS tfs_name, "" AS project_name, "" AS project_id, "" AS tf_call_appointment, "" AS employee_firstname, "" AS employee_lastname, idk_kandidati.tf_reserved_agent, "" AS tf_id,
						"Poslana posta" AS tf_vrsta_name,
						19 AS vrsta_id	
				FROM
					`idk_kandidati`
              	INNER JOIN idk_nd_kandidata ON idk_kandidati.kandidat_dipl_id = idk_nd_kandidata.id_broj_nd_kandidata
				INNER JOIN `idk_nd_kandidata_status_log` ON `idk_nd_kandidata`.`id_broj_nd_kandidata` = `idk_nd_kandidata_status_log`.`idd_broj_nd_kandidata` AND idk_nd_kandidata_status_log.status_nd_kandidata IN (2,3,4,5,6) AND idk_nd_kandidata_status_log.pstatus_nd_kandidata IN (1,2,5,6,7) AND idk_nd_kandidata_status_log.broj_dana_statusa_nd_kandidata IS NULL
				LEFT JOIN idk_nd_ustanove ON idk_nd_kandidata.idd_ustanova_nd = idk_nd_ustanove.id_ustanove_nd
				LEFT JOIN idk_task_force ON idk_task_force.tf_candidate_id = `idk_kandidati`.kandidat_id AND idk_task_force.tf_vrsta_id = 19
				WHERE
					idk_nd_kandidata.status_nd_kandidata = 3 AND idk_nd_kandidata.pstatus_nd_kandidata = 1
					AND DATE(`vrijeme_promjene_statusa_nd_kandidata`) <= DATE(NOW() - INTERVAL 0 DAY)
					AND idk_task_force.tf_candidate_id IS NULL
                    AND idk_kandidati.kandidat_dipl_id IS NOT NULL
					AND idk_kandidati.kandidat_tf_status IS NULL AND idk_kandidati.kandidat_pogresan_broj = 0
		';

		if (!empty($_POST['search']['value'])) {
			
			$post_search_value = trim($_POST["search"]["value"]);
			
			$query .= ' AND (CONCAT(kandidat_ime, " ", kandidat_prezime) LIKE "%' . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, " ", kandidat_ime) LIKE "%' 	 . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, kandidat_ime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR kandidat_ime LIKE "%'         						 . $post_search_value . '%" ';
			$query .= 'OR kandidat_prezime LIKE "%'         					 . $post_search_value . '%" ';
			$query .= 'OR "Poslana posta" LIKE "%'                               . $post_search_value . '%" ';
			$query .= 'OR kandidat_mobitel LIKE "%' 							. $post_search_value . '%") ';
			
		}

		if (!isset($_POST["order"])) {

			$query .= '
				
				ORDER BY idk_kandidati.kandidat_id DESC
			';

		}

		/* TASK FORCE OBRADA 20 */

		$query .= '
			)	
			
			UNION ALL
			
			(
				SELECT
						idk_kandidati.kandidat_id, idk_kandidati.kandidat_dipl_id, idk_kandidati.kandidat_ime, idk_kandidati.kandidat_prezime, "" AS nalog_naziv, "" AS nalog_id,
                        "" AS tfs_name, "" AS project_name, "" AS project_id, "" AS tf_call_appointment, "" AS employee_firstname, "" AS employee_lastname, idk_kandidati.tf_reserved_agent, "" AS tf_id,
						"Provjera da li je stigla dokumentacija" AS tf_vrsta_name,
						20 AS vrsta_id	
				FROM
					`idk_kandidati`
              	INNER JOIN idk_nd_kandidata ON idk_kandidati.kandidat_dipl_id = idk_nd_kandidata.id_broj_nd_kandidata
				INNER JOIN `idk_nd_kandidata_status_log` ON `idk_nd_kandidata`.`id_broj_nd_kandidata` = `idk_nd_kandidata_status_log`.`idd_broj_nd_kandidata` AND idk_nd_kandidata_status_log.status_nd_kandidata IN (2,3,4,5,6) AND idk_nd_kandidata_status_log.pstatus_nd_kandidata IN (1,2,5,6,7) AND idk_nd_kandidata_status_log.broj_dana_statusa_nd_kandidata IS NULL
				LEFT JOIN idk_nd_ustanove ON idk_nd_kandidata.idd_ustanova_nd = idk_nd_ustanove.id_ustanove_nd
				LEFT JOIN idk_task_force ON idk_task_force.tf_candidate_id = `idk_kandidati`.kandidat_id AND idk_task_force.tf_vrsta_id = 20
				WHERE
					idk_nd_kandidata.status_nd_kandidata = 3 AND idk_nd_kandidata.pstatus_nd_kandidata = 1
					AND DATE(`vrijeme_promjene_statusa_nd_kandidata`) <= DATE(NOW() - INTERVAL 10 DAY)
					AND idk_task_force.tf_candidate_id IS NULL
                    AND idk_kandidati.kandidat_dipl_id IS NOT NULL
					AND idk_kandidati.kandidat_tf_status IS NULL AND idk_kandidati.kandidat_pogresan_broj = 0
		';

		if (!empty($_POST['search']['value'])) {
			
			$post_search_value = trim($_POST["search"]["value"]);
			
			$query .= ' AND (CONCAT(kandidat_ime, " ", kandidat_prezime) LIKE "%' . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, " ", kandidat_ime) LIKE "%' 	 . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, kandidat_ime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR kandidat_ime LIKE "%'         						 . $post_search_value . '%" ';
			$query .= 'OR kandidat_prezime LIKE "%'         					 . $post_search_value . '%" ';
			$query .= 'OR "Provjera da li je stigla dokumentacija" LIKE "%'      . $post_search_value . '%" ';
			$query .= 'OR kandidat_mobitel LIKE "%' 							. $post_search_value . '%") ';
			
		}

		if (!isset($_POST["order"])) {

			$query .= '
				
				ORDER BY idk_kandidati.kandidat_id DESC
			';

		}

		/* TASK FORCE OBRADA 15,16,17,18,21,22,23,24,25,26*/

		$query .= '
			)	
			
			UNION ALL
			
			(
				SELECT
						idk_kandidati.kandidat_id, idk_kandidati.kandidat_dipl_id, idk_kandidati.kandidat_ime, idk_kandidati.kandidat_prezime, "" AS nalog_naziv, "" AS nalog_id,
                        "" AS tfs_name, "" AS project_name, "" AS project_id, "" AS tf_call_appointment, "" AS employee_firstname, "" AS employee_lastname, idk_kandidati.tf_reserved_agent, "" AS tf_id,
						CASE 
                           WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 THEN "Nepotpuna dokumentacija"
						   WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 4 THEN "Dokumentacija kompletirana"
                           WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 6 AND vrijeme_promjene_statusa_nd_kandidata <= DATE(NOW() - INTERVAL 7 DAY) THEN "Cekamo zahtjev"
						   WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 5 AND (idk_nd_kandidata.idd_ustanova_nd = 75 OR idk_nd_kandidata.idd_ustanova_nd = 69 OR idk_nd_ustanove.naziv_ustanove_nd LIKE "%Handwerkskammer%") THEN "Posalji zahtjev HWK/ZAV/KMK"
						   WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 5 AND idk_nd_kandidata.idd_ustanova_nd = 1 AND vrijeme_promjene_statusa_nd_kandidata <= DATE(NOW() - INTERVAL 3 DAY) THEN "Poslati postu"
						   WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 7 AND vrijeme_promjene_statusa_nd_kandidata <= DATE(NOW() - INTERVAL 3 DAY) THEN "Poslati postu"                
						   WHEN idk_nd_kandidata.status_nd_kandidata = 3 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 AND vrijeme_promjene_statusa_nd_kandidata <= DATE(NOW() - INTERVAL 45 DAY) THEN "Javiti se ustanovi"                
						   WHEN idk_nd_kandidata.status_nd_kandidata = 4 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 THEN "Cekamo taksu-dopunu"                
						   WHEN idk_nd_kandidata.status_nd_kandidata = 4 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 THEN "Cekamo uplatu takse-slanje dopune"                
						   WHEN idk_nd_kandidata.status_nd_kandidata = 5 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 THEN "Javiti se ustanovi (zavrsetak procesa)"                
						   WHEN idk_nd_kandidata.status_nd_kandidata = 6 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 AND vrijeme_promjene_statusa_nd_kandidata > "2023-11-20" THEN "Zavrsen proces"                
					   END AS tf_vrsta_name,
						CASE 
							WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 THEN 15
							WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 4 THEN 21
							WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 6 AND vrijeme_promjene_statusa_nd_kandidata <= DATE(NOW() - INTERVAL 7 DAY) THEN 17
							WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 5 AND (idk_nd_kandidata.idd_ustanova_nd = 75 OR idk_nd_kandidata.idd_ustanova_nd = 69 OR idk_nd_ustanove.naziv_ustanove_nd LIKE "%Handwerkskammer%") THEN 16
							WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 5 AND idk_nd_kandidata.idd_ustanova_nd = 1 AND vrijeme_promjene_statusa_nd_kandidata <= DATE(NOW() - INTERVAL 3 DAY) THEN 18
							WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 7 AND vrijeme_promjene_statusa_nd_kandidata <= DATE(NOW() - INTERVAL 3 DAY) THEN 18                
							WHEN idk_nd_kandidata.status_nd_kandidata = 3 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 AND vrijeme_promjene_statusa_nd_kandidata <= DATE(NOW() - INTERVAL 45 DAY) THEN 22                
							WHEN idk_nd_kandidata.status_nd_kandidata = 4 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 THEN 23              
						  	WHEN idk_nd_kandidata.status_nd_kandidata = 4 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 THEN 24             
						   	WHEN idk_nd_kandidata.status_nd_kandidata = 5 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 THEN 25               
						   	WHEN idk_nd_kandidata.status_nd_kandidata = 6 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 AND vrijeme_promjene_statusa_nd_kandidata > "2023-11-20" THEN 26   
						END AS vrsta_id	
				FROM
					`idk_kandidati`
              	INNER JOIN idk_nd_kandidata ON idk_kandidati.kandidat_dipl_id = idk_nd_kandidata.id_broj_nd_kandidata
				INNER JOIN `idk_nd_kandidata_status_log` ON `idk_nd_kandidata`.`id_broj_nd_kandidata` = `idk_nd_kandidata_status_log`.`idd_broj_nd_kandidata` AND idk_nd_kandidata_status_log.status_nd_kandidata IN (2,3,4,5,6) AND idk_nd_kandidata_status_log.pstatus_nd_kandidata IN (1,2,4,5,6,7) AND idk_nd_kandidata_status_log.broj_dana_statusa_nd_kandidata IS NULL
				LEFT JOIN idk_nd_ustanove ON idk_nd_kandidata.idd_ustanova_nd = idk_nd_ustanove.id_ustanove_nd
				LEFT JOIN idk_task_force ON 
				idk_task_force.tf_candidate_id = `idk_kandidati`.kandidat_id AND
				(
					(idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 AND idk_task_force.tf_vrsta_id = 15) OR
					(idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 4 AND idk_task_force.tf_vrsta_id = 21) OR
					(idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 6 AND vrijeme_promjene_statusa_nd_kandidata <= DATE(NOW() - INTERVAL 7 DAY) AND idk_task_force.tf_vrsta_id = 17) OR
					(idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 5 AND (idk_nd_kandidata.idd_ustanova_nd = 75 OR idk_nd_kandidata.idd_ustanova_nd = 69 OR idk_nd_ustanove.naziv_ustanove_nd LIKE "%Handwerkskammer%") AND idk_task_force.tf_vrsta_id = 16) OR
					(idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 5 AND idk_nd_kandidata.idd_ustanova_nd = 1 AND vrijeme_promjene_statusa_nd_kandidata <= DATE(NOW() - INTERVAL 3 DAY) AND idk_task_force.tf_vrsta_id = 18) OR
					(idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 7 AND vrijeme_promjene_statusa_nd_kandidata <= DATE(NOW() - INTERVAL 3 DAY) AND idk_task_force.tf_vrsta_id = 18) OR
					(idk_nd_kandidata.status_nd_kandidata = 3 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 AND vrijeme_promjene_statusa_nd_kandidata <= DATE(NOW() - INTERVAL 45 DAY) AND idk_task_force.tf_vrsta_id = 22) OR
					(idk_nd_kandidata.status_nd_kandidata = 4 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 AND idk_task_force.tf_vrsta_id = 23) OR
					(idk_nd_kandidata.status_nd_kandidata = 4 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 AND idk_task_force.tf_vrsta_id = 24) OR
					(idk_nd_kandidata.status_nd_kandidata = 5 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 AND idk_task_force.tf_vrsta_id = 25) OR
					(idk_nd_kandidata.status_nd_kandidata = 6 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 AND vrijeme_promjene_statusa_nd_kandidata > "2023-11-20" AND idk_task_force.tf_vrsta_id = 26)
				)
				WHERE
					idk_nd_kandidata.status_nd_kandidata IN (2,3,4,5,6) AND idk_nd_kandidata.pstatus_nd_kandidata IN (1,2,4,5,6,7)
					AND DATE(`vrijeme_promjene_statusa_nd_kandidata`) <= 
											(CASE 
												WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 THEN DATE(NOW())
												WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 4 THEN DATE(NOW())
												WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 6 THEN DATE(NOW() - INTERVAL 7 DAY)
												WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 5  AND (idk_nd_kandidata.idd_ustanova_nd = 75 OR idk_nd_kandidata.idd_ustanova_nd = 69 OR idk_nd_ustanove.naziv_ustanove_nd LIKE "%Handwerkskammer%") THEN DATE(NOW())
												WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 5 AND idk_nd_kandidata.idd_ustanova_nd = 1 THEN DATE(NOW() - INTERVAL 3 DAY)
												WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 7 THEN DATE(NOW() - INTERVAL 3 DAY)
												WHEN idk_nd_kandidata.status_nd_kandidata = 3 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 THEN DATE(NOW() - INTERVAL 45 DAY)
												WHEN idk_nd_kandidata.status_nd_kandidata = 4 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 THEN DATE(NOW() - INTERVAL 20 DAY)
												WHEN idk_nd_kandidata.status_nd_kandidata = 4 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 THEN DATE(NOW())
												WHEN idk_nd_kandidata.status_nd_kandidata = 5 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 THEN DATE(NOW() - INTERVAL 100 DAY)
												WHEN idk_nd_kandidata.status_nd_kandidata = 6 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 AND vrijeme_promjene_statusa_nd_kandidata > "2023-11-20" THEN DATE(NOW())
											END)
					AND idk_task_force.tf_candidate_id IS NULL
                    AND idk_kandidati.kandidat_dipl_id IS NOT NULL
					AND idk_kandidati.kandidat_tf_status IS NULL AND idk_kandidati.kandidat_pogresan_broj = 0
		';
	
		if (!empty($_POST['search']['value'])) {
			
			$post_search_value = trim($_POST["search"]["value"]);
			
			$query .= ' AND (CONCAT(kandidat_ime, " ", kandidat_prezime) LIKE "%' . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, " ", kandidat_ime) LIKE "%' 	 . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, kandidat_ime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR kandidat_ime LIKE "%'         						 . $post_search_value . '%" ';
			$query .= 'OR kandidat_prezime LIKE "%'         					 . $post_search_value . '%" ';
			$query .= 'OR (CASE 
								WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 THEN "Nepotpuna dokumentacija"
								WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 4 THEN "Dokumentacija kompletirana"
								WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 6 THEN "Cekamo zahtjev"
								WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 5  AND (idk_nd_kandidata.idd_ustanova_nd = 75 OR idk_nd_kandidata.idd_ustanova_nd = 69 OR idk_nd_ustanove.naziv_ustanove_nd LIKE "%Handwerkskammer%") THEN "Posalji zahtjev HWK/ZAV/KMK"
								WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 5 AND idk_nd_kandidata.idd_ustanova_nd = 1 THEN "Poslati postu"
								WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 7 THEN "Poslati postu"
								WHEN idk_nd_kandidata.status_nd_kandidata = 3 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 THEN "Javiti se ustanovi"
								WHEN idk_nd_kandidata.status_nd_kandidata = 4 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 THEN "Cekamo taksu-dopunu"
								WHEN idk_nd_kandidata.status_nd_kandidata = 4 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 THEN "Cekamo uplatu takse-slanje dopune"
								WHEN idk_nd_kandidata.status_nd_kandidata = 5 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 THEN "Javiti se ustanovi (zavrsetak procesa)"
								WHEN idk_nd_kandidata.status_nd_kandidata = 6 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 AND vrijeme_promjene_statusa_nd_kandidata > "2023-11-20" THEN "Zavrsen proces"
							END) LIKE "%'         					 			. $post_search_value . '%" ';
			$query .= 'OR kandidat_mobitel LIKE "%' 							. $post_search_value . '%") ';
			
		}

		if (!isset($_POST["order"])) {

			$query .= '
				
				ORDER BY idk_kandidati.kandidat_id DESC
			';

		}

		/* TASK FORCE OBRADA PREBACITI NA PRIKUPLJANJE 100% */

		$query .= '
			)	
			
			UNION ALL
			
			(
				SELECT
						idk_kandidati.kandidat_id, idk_kandidati.kandidat_dipl_id, idk_kandidati.kandidat_ime, idk_kandidati.kandidat_prezime, "" AS nalog_naziv, "" AS nalog_id,
                        "" AS tfs_name, "" AS project_name, "" AS project_id, "" AS tf_call_appointment, "" AS employee_firstname, "" AS employee_lastname, idk_kandidati.tf_reserved_agent, "" AS tf_id,
						"Prebaciti na prikupljanje 100%" AS tf_vrsta_name,
						12 AS vrsta_id	
				FROM
					`idk_kandidati`
              	INNER JOIN idk_nd_kandidata ON idk_kandidati.kandidat_dipl_id = idk_nd_kandidata.id_broj_nd_kandidata
				INNER JOIN `idk_nd_kandidata_status_log` ON `idk_nd_kandidata`.`id_broj_nd_kandidata` = `idk_nd_kandidata_status_log`.`idd_broj_nd_kandidata` AND idk_nd_kandidata_status_log.broj_dana_statusa_nd_kandidata IS NULL
				INNER JOIN idk_task_force ON idk_task_force.tf_candidate_id = `idk_kandidati`.kandidat_id AND idk_task_force.tf_vrsta_id = 11 AND idk_task_force.tf_status_id IN (40, 41)
				WHERE
					idk_kandidati.kandidat_status_prijave = 9
					AND idk_nd_kandidata.status_nd_kandidata IN (1,7)
					AND idk_kandidati.kandidat_nacin_odlaska = 0
					AND idk_kandidati.kandidat_drzavljanstvo_vrsta NOT LIKE  ("EU državljanin")
                    AND idk_kandidati.kandidat_dipl_id IS NOT NULL AND idk_kandidati.kandidat_pogresan_broj = 0
		';
		
		if (!empty($_POST['search']['value'])) {
			
			$post_search_value = trim($_POST["search"]["value"]);
			
			$query .= ' AND (CONCAT(kandidat_ime, " ", kandidat_prezime) LIKE "%' . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, " ", kandidat_ime) LIKE "%' 	 . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, kandidat_ime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR kandidat_ime LIKE "%'         						 . $post_search_value . '%" ';
			$query .= 'OR kandidat_prezime LIKE "%'         					 . $post_search_value . '%" ';
			$query .= 'OR "Prebaciti na prikupljanje 100%" LIKE "%'         	 . $post_search_value . '%" ';
			$query .= 'OR kandidat_mobitel LIKE "%' 							 . $post_search_value . '%") ';
			
		}

		if (!isset($_POST["order"])) {

			$query .= '
				
				ORDER BY idk_kandidati.kandidat_id DESC
			';

		}

		$query .= ')';

		if (isset($_POST["order"])) {
			if ($_POST["order"][0]['column'] !== 0) {

				$query .= ' 
					ORDER BY '. $columns[$_POST["order"][0]["column"]]. ' '. $_POST["order"]["0"]["dir"] . ' 
				';

			}
		}

		if ($_POST["length"] != -1) {
			$query .= ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$query_prepared = $db->prepare($query);
		$query_prepared->execute();
		
		$rows = $query_prepared->fetchAll();
		$data = array();

		foreach ($rows as $row) {

			$sub_array[0] = '<a href="nostrifikacija_diploma?page=otvori_ND_kandidata&id=' . $row["kandidat_dipl_id"] . '&vrsta_id='. $row["vrsta_id"]. '">' . $row["kandidat_ime"] . " " . $row["kandidat_prezime"] . '</a>';
			$sub_array[1] = $row['nalog_naziv'];
			$sub_array[2] = '<span data-order="' . $row['tf_vrsta_name'] . '">' . $row['tf_vrsta_name'] . '</span>';
			$sub_array[3] = $row['tfs_name'];
			$sub_array[4] = $row['project_name'];
			$sub_array[5] = $row['tf_call_appointment'];
			$sub_array[6] = $row['employee_firstname'] . " " . $row['employee_lastname'];
			$sub_array[7] = $row['kandidat_id'];
			$sub_array[8] = $row['tf_reserved_agent'];
			$sub_array[9] = $row['tf_id'];

			
			$data[] = $sub_array;

		}

		/* TASK FORCE ZADACI IZ TABELE TASK-FORCE ZA OBRADU KATEGORIJA VRSTE = 3 (OBRADA) COUNT */

		$query = '
			(
				SELECT idk_kandidati.kandidat_id, idk_kandidati.kandidat_dipl_id, idk_kandidati.kandidat_ime, idk_kandidati.kandidat_prezime, "" AS nalog_naziv, tf_nalog_id AS nalog_id, idk_tf_statusi.tfs_name, "" AS project_name, "" AS project_id, idk_task_force.tf_call_appointment, idk_employees.employee_firstname, idk_employees.employee_lastname, idk_kandidati.tf_reserved_agent, idk_task_force.tf_id, idk_tf_vrste.name AS tf_vrsta_name, idk_task_force.tf_vrsta_id as vrsta_id
				FROM `idk_task_force`
				
				INNER JOIN idk_kandidati  ON idk_task_force.tf_candidate_id = idk_kandidati.kandidat_id
				INNER JOIN idk_tf_statusi ON idk_task_force.tf_status_id    = idk_tf_statusi.tfs_id
				INNER JOIN idk_tf_vrste   ON idk_task_force.tf_vrsta_id     = idk_tf_vrste.id
				LEFT JOIN idk_employees   ON idk_task_force.tf_agent_id     = idk_employees.employee_id
				
				WHERE idk_task_force.tf_last_active_task = 1
				AND tf_vrsta_id != 1 AND idk_tf_vrste.category = 3 AND idk_kandidati.kandidat_pogresan_broj = 0
				 
		';

		if (!empty($_POST['search']['value'])) {
			
			$post_search_value = trim($_POST["search"]["value"]);
			
			$query .= ' AND (CONCAT(kandidat_ime, " ", kandidat_prezime) LIKE "%' 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, " ", kandidat_ime) LIKE "%' 	 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, kandidat_ime) LIKE "%' 	     	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     	  . $post_search_value . '%" ';
			$query .= 'OR kandidat_ime LIKE "%'         						 	  . $post_search_value . '%" ';
			$query .= 'OR kandidat_prezime LIKE "%'         					 	  . $post_search_value . '%" ';
			$query .= 'OR tfs_name LIKE "%' 		   							 	  . $post_search_value . '%" ';
			$query .= 'OR idk_tf_vrste.name LIKE "%' 		   						  . $post_search_value . '%" ';
			$query .= 'OR tf_call_appointment LIKE "%' 							 	  . $post_search_value . '%" ';
			$query .= 'OR kandidat_mobitel LIKE "%' 							 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(employee_firstname, employee_lastname) LIKE "%' 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(employee_lastname, " ", employee_firstname) LIKE "%' . $post_search_value . '%" ';
			$query .= 'OR CONCAT(employee_lastname, employee_firstname) LIKE "%' 	  . $post_search_value . '%" ';
			$query .= 'OR CONCAT(employee_firstname, employee_lastname) LIKE "%' 	  . $post_search_value . '%") ';
			
		}

		/* TASK FORCE OBRADA 13 COUNT */

		$query .= '
			)	
			
			UNION ALL
			
			(
				SELECT
						idk_kandidati.kandidat_id, idk_kandidati.kandidat_dipl_id, idk_kandidati.kandidat_ime, idk_kandidati.kandidat_prezime, "" AS nalog_naziv, "" AS nalog_id,
                        "" AS tfs_name, "" AS project_name, "" AS project_id, "" AS tf_call_appointment, "" AS employee_firstname, "" AS employee_lastname, idk_kandidati.tf_reserved_agent, "" AS tf_id,
						"Povezi sa ustanovom i posalji obavijest" AS tf_vrsta_name,
						13 AS vrsta_id	
				FROM
					`idk_kandidati`
              	INNER JOIN idk_nd_kandidata ON idk_kandidati.kandidat_dipl_id = idk_nd_kandidata.id_broj_nd_kandidata
				INNER JOIN `idk_nd_kandidata_status_log` ON `idk_nd_kandidata`.`id_broj_nd_kandidata` = `idk_nd_kandidata_status_log`.`idd_broj_nd_kandidata` AND idk_nd_kandidata_status_log.status_nd_kandidata IN (2,3,4,5,6) AND idk_nd_kandidata_status_log.pstatus_nd_kandidata IN (1,2,5,6,7) AND idk_nd_kandidata_status_log.broj_dana_statusa_nd_kandidata IS NULL
				LEFT JOIN idk_nd_ustanove ON idk_nd_kandidata.idd_ustanova_nd = idk_nd_ustanove.id_ustanove_nd
				LEFT JOIN idk_task_force ON idk_task_force.tf_candidate_id = `idk_kandidati`.kandidat_id AND idk_task_force.tf_vrsta_id = 13
				WHERE
					idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 1
					AND DATE(`vrijeme_promjene_statusa_nd_kandidata`) <= DATE(NOW() - INTERVAL 0 DAY)
					AND idk_task_force.tf_candidate_id IS NULL
                    AND idk_kandidati.kandidat_dipl_id IS NOT NULL
					AND idk_kandidati.kandidat_tf_status IS NULL AND idk_kandidati.kandidat_pogresan_broj = 0
		';

		if (!empty($_POST['search']['value'])) {
			
			$post_search_value = trim($_POST["search"]["value"]);
			
			$query .= ' AND (CONCAT(kandidat_ime, " ", kandidat_prezime) LIKE "%' . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, " ", kandidat_ime) LIKE "%' 	 . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, kandidat_ime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR kandidat_ime LIKE "%'         						 . $post_search_value . '%" ';
			$query .= 'OR kandidat_prezime LIKE "%'         					 . $post_search_value . '%" ';
			$query .= 'OR "Povezi sa ustanovom i posalji obavijest" LIKE "%'     . $post_search_value . '%" ';
			$query .= 'OR kandidat_mobitel LIKE "%' 							 . $post_search_value . '%") ';
			
		}

		/* TASK FORCE OBRADA 14 COUNT */

		$query .= '
			)	
			
			UNION ALL
			
			(
				SELECT
						idk_kandidati.kandidat_id, idk_kandidati.kandidat_dipl_id, idk_kandidati.kandidat_ime, idk_kandidati.kandidat_prezime, "" AS nalog_naziv, "" AS nalog_id,
                        "" AS tfs_name, "" AS project_name, "" AS project_id, "" AS tf_call_appointment, "" AS employee_firstname, "" AS employee_lastname, idk_kandidati.tf_reserved_agent, "" AS tf_id,
						"Cekamo dokumentaciju" AS tf_vrsta_name,
						14 AS vrsta_id	
				FROM
					`idk_kandidati`
              	INNER JOIN idk_nd_kandidata ON idk_kandidati.kandidat_dipl_id = idk_nd_kandidata.id_broj_nd_kandidata
				INNER JOIN `idk_nd_kandidata_status_log` ON `idk_nd_kandidata`.`id_broj_nd_kandidata` = `idk_nd_kandidata_status_log`.`idd_broj_nd_kandidata` AND idk_nd_kandidata_status_log.status_nd_kandidata IN (2,3,4,5,6) AND idk_nd_kandidata_status_log.pstatus_nd_kandidata IN (1,2,5,6,7) AND idk_nd_kandidata_status_log.broj_dana_statusa_nd_kandidata IS NULL
				LEFT JOIN idk_nd_ustanove ON idk_nd_kandidata.idd_ustanova_nd = idk_nd_ustanove.id_ustanove_nd
				LEFT JOIN idk_task_force ON idk_task_force.tf_candidate_id = `idk_kandidati`.kandidat_id AND idk_task_force.tf_vrsta_id = 14
				JOIN idk_task_force tf2 ON tf2.tf_candidate_id = `idk_kandidati`.kandidat_id AND tf2.tf_vrsta_id = 13 AND tf2.tf_status_id = 50
				WHERE
					idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 1
					AND DATE(`vrijeme_promjene_statusa_nd_kandidata`) <= DATE(NOW() - INTERVAL 7 DAY)
					AND idk_task_force.tf_candidate_id IS NULL
                    AND idk_kandidati.kandidat_dipl_id IS NOT NULL
					AND idk_kandidati.kandidat_tf_status IS NULL AND idk_kandidati.kandidat_pogresan_broj = 0
		';

		if (!empty($_POST['search']['value'])) {
			
			$post_search_value = trim($_POST["search"]["value"]);
			
			$query .= ' AND (CONCAT(kandidat_ime, " ", kandidat_prezime) LIKE "%' . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, " ", kandidat_ime) LIKE "%' 	 . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, kandidat_ime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR kandidat_ime LIKE "%'         						 . $post_search_value . '%" ';
			$query .= 'OR kandidat_prezime LIKE "%'         					 . $post_search_value . '%" ';
			$query .= 'OR "Cekamo dokumentaciju" LIKE "%'                        . $post_search_value . '%" ';
			$query .= 'OR kandidat_mobitel LIKE "%' 							 . $post_search_value . '%") ';
			
		}

		/* TASK FORCE OBRADA 19 COUNT */

		$query .= '
			)	
			
			UNION ALL
			
			(
				SELECT
						idk_kandidati.kandidat_id, idk_kandidati.kandidat_dipl_id, idk_kandidati.kandidat_ime, idk_kandidati.kandidat_prezime, "" AS nalog_naziv, "" AS nalog_id,
                        "" AS tfs_name, "" AS project_name, "" AS project_id, "" AS tf_call_appointment, "" AS employee_firstname, "" AS employee_lastname, idk_kandidati.tf_reserved_agent, "" AS tf_id,
						"Poslana posta" AS tf_vrsta_name,
						19 AS vrsta_id	
				FROM
					`idk_kandidati`
              	INNER JOIN idk_nd_kandidata ON idk_kandidati.kandidat_dipl_id = idk_nd_kandidata.id_broj_nd_kandidata
				INNER JOIN `idk_nd_kandidata_status_log` ON `idk_nd_kandidata`.`id_broj_nd_kandidata` = `idk_nd_kandidata_status_log`.`idd_broj_nd_kandidata` AND idk_nd_kandidata_status_log.status_nd_kandidata IN (2,3,4,5,6) AND idk_nd_kandidata_status_log.pstatus_nd_kandidata IN (1,2,5,6,7) AND idk_nd_kandidata_status_log.broj_dana_statusa_nd_kandidata IS NULL
				LEFT JOIN idk_nd_ustanove ON idk_nd_kandidata.idd_ustanova_nd = idk_nd_ustanove.id_ustanove_nd
				LEFT JOIN idk_task_force ON idk_task_force.tf_candidate_id = `idk_kandidati`.kandidat_id AND idk_task_force.tf_vrsta_id = 19
				WHERE
					idk_nd_kandidata.status_nd_kandidata = 3 AND idk_nd_kandidata.pstatus_nd_kandidata = 1
					AND DATE(`vrijeme_promjene_statusa_nd_kandidata`) <= DATE(NOW() - INTERVAL 0 DAY)
					AND idk_task_force.tf_candidate_id IS NULL
                    AND idk_kandidati.kandidat_dipl_id IS NOT NULL
					AND idk_kandidati.kandidat_tf_status IS NULL AND idk_kandidati.kandidat_pogresan_broj = 0
		';

		if (!empty($_POST['search']['value'])) {
			
			$post_search_value = trim($_POST["search"]["value"]);
			
			$query .= ' AND (CONCAT(kandidat_ime, " ", kandidat_prezime) LIKE "%' . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, " ", kandidat_ime) LIKE "%' 	 . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, kandidat_ime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR kandidat_ime LIKE "%'         						 . $post_search_value . '%" ';
			$query .= 'OR kandidat_prezime LIKE "%'         					 . $post_search_value . '%" ';
			$query .= 'OR "Poslana posta" LIKE "%'                               . $post_search_value . '%" ';
			$query .= 'OR kandidat_mobitel LIKE "%' 							. $post_search_value . '%") ';
			
		}

		/* TASK FORCE OBRADA 20 COUNT */

		$query .= '
			)	
			
			UNION ALL
			
			(
				SELECT
						idk_kandidati.kandidat_id, idk_kandidati.kandidat_dipl_id, idk_kandidati.kandidat_ime, idk_kandidati.kandidat_prezime, "" AS nalog_naziv, "" AS nalog_id,
                        "" AS tfs_name, "" AS project_name, "" AS project_id, "" AS tf_call_appointment, "" AS employee_firstname, "" AS employee_lastname, idk_kandidati.tf_reserved_agent, "" AS tf_id,
						"Provjera da li je stigla dokumentacija" AS tf_vrsta_name,
						20 AS vrsta_id	
				FROM
					`idk_kandidati`
              	INNER JOIN idk_nd_kandidata ON idk_kandidati.kandidat_dipl_id = idk_nd_kandidata.id_broj_nd_kandidata
				INNER JOIN `idk_nd_kandidata_status_log` ON `idk_nd_kandidata`.`id_broj_nd_kandidata` = `idk_nd_kandidata_status_log`.`idd_broj_nd_kandidata` AND idk_nd_kandidata_status_log.status_nd_kandidata IN (2,3,4,5,6) AND idk_nd_kandidata_status_log.pstatus_nd_kandidata IN (1,2,5,6,7) AND idk_nd_kandidata_status_log.broj_dana_statusa_nd_kandidata IS NULL
				LEFT JOIN idk_nd_ustanove ON idk_nd_kandidata.idd_ustanova_nd = idk_nd_ustanove.id_ustanove_nd
				LEFT JOIN idk_task_force ON idk_task_force.tf_candidate_id = `idk_kandidati`.kandidat_id AND idk_task_force.tf_vrsta_id = 20
				WHERE
					idk_nd_kandidata.status_nd_kandidata = 3 AND idk_nd_kandidata.pstatus_nd_kandidata = 1
					AND DATE(`vrijeme_promjene_statusa_nd_kandidata`) <= DATE(NOW() - INTERVAL 10 DAY)
					AND idk_task_force.tf_candidate_id IS NULL
                    AND idk_kandidati.kandidat_dipl_id IS NOT NULL
					AND idk_kandidati.kandidat_tf_status IS NULL AND idk_kandidati.kandidat_pogresan_broj = 0
		';

		if (!empty($_POST['search']['value'])) {
			
			$post_search_value = trim($_POST["search"]["value"]);
			
			$query .= ' AND (CONCAT(kandidat_ime, " ", kandidat_prezime) LIKE "%' . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, " ", kandidat_ime) LIKE "%' 	 . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, kandidat_ime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR kandidat_ime LIKE "%'         						 . $post_search_value . '%" ';
			$query .= 'OR kandidat_prezime LIKE "%'         					 . $post_search_value . '%" ';
			$query .= 'OR "Provjera da li je stigla dokumentacija" LIKE "%'      . $post_search_value . '%" ';
			$query .= 'OR kandidat_mobitel LIKE "%' 							. $post_search_value . '%") ';
			
		}

		/* TASK FORCE OBRADA 15,16,17,18,21,22,23,24,25,26 COUNT */

		$query .= '
			)	
			
			UNION ALL
			
			(
				SELECT
						idk_kandidati.kandidat_id, idk_kandidati.kandidat_dipl_id, idk_kandidati.kandidat_ime, idk_kandidati.kandidat_prezime, "" AS nalog_naziv, "" AS nalog_id,
                        "" AS tfs_name, "" AS project_name, "" AS project_id, "" AS tf_call_appointment, "" AS employee_firstname, "" AS employee_lastname, idk_kandidati.tf_reserved_agent, "" AS tf_id,
						CASE 
                           WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 THEN "Nepotpuna dokumentacija"
						   WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 4 THEN "Dokumentacija kompletirana"
                           WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 6 AND vrijeme_promjene_statusa_nd_kandidata <= DATE(NOW() - INTERVAL 7 DAY) THEN "Cekamo zahtjev"
						   WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 5 AND (idk_nd_kandidata.idd_ustanova_nd = 75 OR idk_nd_kandidata.idd_ustanova_nd = 69 OR idk_nd_ustanove.naziv_ustanove_nd LIKE "%Handwerkskammer%") THEN "Posalji zahtjev HWK/ZAV/KMK"
						   WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 5 AND idk_nd_kandidata.idd_ustanova_nd = 1 AND vrijeme_promjene_statusa_nd_kandidata <= DATE(NOW() - INTERVAL 3 DAY) THEN "Poslati postu"
						   WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 7 AND vrijeme_promjene_statusa_nd_kandidata <= DATE(NOW() - INTERVAL 3 DAY) THEN "Poslati postu"                
						   WHEN idk_nd_kandidata.status_nd_kandidata = 3 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 AND vrijeme_promjene_statusa_nd_kandidata <= DATE(NOW() - INTERVAL 45 DAY) THEN "Javiti se ustanovi"                
						   WHEN idk_nd_kandidata.status_nd_kandidata = 4 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 THEN "Cekamo taksu-dopunu"                
						   WHEN idk_nd_kandidata.status_nd_kandidata = 4 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 THEN "Cekamo uplatu takse-slanje dopune"                
						   WHEN idk_nd_kandidata.status_nd_kandidata = 5 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 THEN "Javiti se ustanovi (zavrsetak procesa)"                
						   WHEN idk_nd_kandidata.status_nd_kandidata = 6 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 AND vrijeme_promjene_statusa_nd_kandidata > "2023-11-20" THEN "Zavrsen proces"                
					   END AS tf_vrsta_name,
						CASE 
							WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 THEN 15
							WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 4 THEN 21
							WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 6 AND vrijeme_promjene_statusa_nd_kandidata <= DATE(NOW() - INTERVAL 7 DAY) THEN 17
							WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 5 AND (idk_nd_kandidata.idd_ustanova_nd = 75 OR idk_nd_kandidata.idd_ustanova_nd = 69 OR idk_nd_ustanove.naziv_ustanove_nd LIKE "%Handwerkskammer%") THEN 16
							WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 5 AND idk_nd_kandidata.idd_ustanova_nd = 1 AND vrijeme_promjene_statusa_nd_kandidata <= DATE(NOW() - INTERVAL 3 DAY) THEN 18
							WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 7 AND vrijeme_promjene_statusa_nd_kandidata <= DATE(NOW() - INTERVAL 3 DAY) THEN 18                
							WHEN idk_nd_kandidata.status_nd_kandidata = 3 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 AND vrijeme_promjene_statusa_nd_kandidata <= DATE(NOW() - INTERVAL 45 DAY) THEN 22                
							WHEN idk_nd_kandidata.status_nd_kandidata = 4 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 THEN 23              
						  	WHEN idk_nd_kandidata.status_nd_kandidata = 4 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 THEN 24             
						   	WHEN idk_nd_kandidata.status_nd_kandidata = 5 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 THEN 25               
						   	WHEN idk_nd_kandidata.status_nd_kandidata = 6 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 AND vrijeme_promjene_statusa_nd_kandidata > "2023-11-20" THEN 26   
						END AS vrsta_id	
				FROM
					`idk_kandidati`
              	INNER JOIN idk_nd_kandidata ON idk_kandidati.kandidat_dipl_id = idk_nd_kandidata.id_broj_nd_kandidata
				INNER JOIN `idk_nd_kandidata_status_log` ON `idk_nd_kandidata`.`id_broj_nd_kandidata` = `idk_nd_kandidata_status_log`.`idd_broj_nd_kandidata` AND idk_nd_kandidata_status_log.status_nd_kandidata IN (2,3,4,5,6) AND idk_nd_kandidata_status_log.pstatus_nd_kandidata IN (1,2,4,5,6,7) AND idk_nd_kandidata_status_log.broj_dana_statusa_nd_kandidata IS NULL
				LEFT JOIN idk_nd_ustanove ON idk_nd_kandidata.idd_ustanova_nd = idk_nd_ustanove.id_ustanove_nd
				LEFT JOIN idk_task_force ON 
				idk_task_force.tf_candidate_id = `idk_kandidati`.kandidat_id AND
				(
					(idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 AND idk_task_force.tf_vrsta_id = 15) OR
					(idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 4 AND idk_task_force.tf_vrsta_id = 21) OR
					(idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 6 AND vrijeme_promjene_statusa_nd_kandidata <= DATE(NOW() - INTERVAL 7 DAY) AND idk_task_force.tf_vrsta_id = 17) OR
					(idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 5 AND (idk_nd_kandidata.idd_ustanova_nd = 75 OR idk_nd_kandidata.idd_ustanova_nd = 69 OR idk_nd_ustanove.naziv_ustanove_nd LIKE "%Handwerkskammer%") AND idk_task_force.tf_vrsta_id = 16) OR
					(idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 5 AND idk_nd_kandidata.idd_ustanova_nd = 1 AND vrijeme_promjene_statusa_nd_kandidata <= DATE(NOW() - INTERVAL 3 DAY) AND idk_task_force.tf_vrsta_id = 18) OR
					(idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 7 AND vrijeme_promjene_statusa_nd_kandidata <= DATE(NOW() - INTERVAL 3 DAY) AND idk_task_force.tf_vrsta_id = 18) OR
					(idk_nd_kandidata.status_nd_kandidata = 3 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 AND vrijeme_promjene_statusa_nd_kandidata <= DATE(NOW() - INTERVAL 45 DAY) AND idk_task_force.tf_vrsta_id = 22) OR
					(idk_nd_kandidata.status_nd_kandidata = 4 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 AND idk_task_force.tf_vrsta_id = 23) OR
					(idk_nd_kandidata.status_nd_kandidata = 4 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 AND idk_task_force.tf_vrsta_id = 24) OR
					(idk_nd_kandidata.status_nd_kandidata = 5 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 AND idk_task_force.tf_vrsta_id = 25) OR
					(idk_nd_kandidata.status_nd_kandidata = 6 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 AND vrijeme_promjene_statusa_nd_kandidata > "2023-11-20" AND idk_task_force.tf_vrsta_id = 26)
				)
				WHERE
					idk_nd_kandidata.status_nd_kandidata IN (2,3,4,5,6) AND idk_nd_kandidata.pstatus_nd_kandidata IN (1,2,4,5,6,7)
					AND DATE(`vrijeme_promjene_statusa_nd_kandidata`) <= 
											(CASE 
												WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 THEN DATE(NOW())
												WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 4 THEN DATE(NOW())
												WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 6 THEN DATE(NOW() - INTERVAL 7 DAY)
												WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 5  AND (idk_nd_kandidata.idd_ustanova_nd = 75 OR idk_nd_kandidata.idd_ustanova_nd = 69 OR idk_nd_ustanove.naziv_ustanove_nd LIKE "%Handwerkskammer%") THEN DATE(NOW())
												WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 5 AND idk_nd_kandidata.idd_ustanova_nd = 1 THEN DATE(NOW() - INTERVAL 3 DAY)
												WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 7 THEN DATE(NOW() - INTERVAL 3 DAY)
												WHEN idk_nd_kandidata.status_nd_kandidata = 3 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 THEN DATE(NOW() - INTERVAL 45 DAY)
												WHEN idk_nd_kandidata.status_nd_kandidata = 4 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 THEN DATE(NOW() - INTERVAL 20 DAY)
												WHEN idk_nd_kandidata.status_nd_kandidata = 4 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 THEN DATE(NOW())
												WHEN idk_nd_kandidata.status_nd_kandidata = 5 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 THEN DATE(NOW() - INTERVAL 100 DAY)
												WHEN idk_nd_kandidata.status_nd_kandidata = 6 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 AND vrijeme_promjene_statusa_nd_kandidata > "2023-11-20" THEN DATE(NOW())
											END)
					AND idk_task_force.tf_candidate_id IS NULL
                    AND idk_kandidati.kandidat_dipl_id IS NOT NULL
					AND idk_kandidati.kandidat_tf_status IS NULL AND idk_kandidati.kandidat_pogresan_broj = 0
		';
	
		if (!empty($_POST['search']['value'])) {
			
			$post_search_value = trim($_POST["search"]["value"]);
			
			$query .= ' AND (CONCAT(kandidat_ime, " ", kandidat_prezime) LIKE "%' . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, " ", kandidat_ime) LIKE "%' 	 . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, kandidat_ime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR kandidat_ime LIKE "%'         						 . $post_search_value . '%" ';
			$query .= 'OR kandidat_prezime LIKE "%'         					 . $post_search_value . '%" ';
			$query .= 'OR (CASE 
								WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 THEN "Nepotpuna dokumentacija"
								WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 4 THEN "Dokumentacija kompletirana"
								WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 6 THEN "Cekamo zahtjev"
								WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 5  AND (idk_nd_kandidata.idd_ustanova_nd = 75 OR idk_nd_kandidata.idd_ustanova_nd = 69 OR idk_nd_ustanove.naziv_ustanove_nd LIKE "%Handwerkskammer%") THEN "Posalji zahtjev HWK/ZAV/KMK"
								WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 5 AND idk_nd_kandidata.idd_ustanova_nd = 1 THEN "Poslati postu"
								WHEN idk_nd_kandidata.status_nd_kandidata = 2 AND idk_nd_kandidata.pstatus_nd_kandidata = 7 THEN "Poslati postu"
								WHEN idk_nd_kandidata.status_nd_kandidata = 3 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 THEN "Javiti se ustanovi"
								WHEN idk_nd_kandidata.status_nd_kandidata = 4 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 THEN "Cekamo taksu-dopunu"
								WHEN idk_nd_kandidata.status_nd_kandidata = 4 AND idk_nd_kandidata.pstatus_nd_kandidata = 2 THEN "Cekamo uplatu takse-slanje dopune"
								WHEN idk_nd_kandidata.status_nd_kandidata = 5 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 THEN "Javiti se ustanovi (zavrsetak procesa)"
								WHEN idk_nd_kandidata.status_nd_kandidata = 6 AND idk_nd_kandidata.pstatus_nd_kandidata = 1 AND vrijeme_promjene_statusa_nd_kandidata > "2023-11-20" THEN "Zavrsen proces"
							END) LIKE "%'         					 			. $post_search_value . '%" ';
			$query .= 'OR kandidat_mobitel LIKE "%' 							. $post_search_value . '%") ';
			
		}

		/* TASK FORCE OBRADA PREBACITI NA PRIKUPLJANJE 100% - COUNT*/

		$query .= '
			)	
			
			UNION ALL
			
			(
				SELECT
						idk_kandidati.kandidat_id, idk_kandidati.kandidat_dipl_id, idk_kandidati.kandidat_ime, idk_kandidati.kandidat_prezime, "" AS nalog_naziv, "" AS nalog_id,
                        "" AS tfs_name, "" AS project_name, "" AS project_id, "" AS tf_call_appointment, "" AS employee_firstname, "" AS employee_lastname, idk_kandidati.tf_reserved_agent, "" AS tf_id,
						"Prebaciti na prikupljanje 100%" AS tf_vrsta_name,
						12 AS vrsta_id	
				FROM
					`idk_kandidati`
              	INNER JOIN idk_nd_kandidata ON idk_kandidati.kandidat_dipl_id = idk_nd_kandidata.id_broj_nd_kandidata
				INNER JOIN `idk_nd_kandidata_status_log` ON `idk_nd_kandidata`.`id_broj_nd_kandidata` = `idk_nd_kandidata_status_log`.`idd_broj_nd_kandidata` AND idk_nd_kandidata_status_log.broj_dana_statusa_nd_kandidata IS NULL
				INNER JOIN idk_task_force ON idk_task_force.tf_candidate_id = `idk_kandidati`.kandidat_id AND idk_task_force.tf_vrsta_id = 11 AND idk_task_force.tf_status_id IN (40, 41)
				WHERE
					idk_kandidati.kandidat_status_prijave = 9
					AND idk_nd_kandidata.status_nd_kandidata IN (1,7)
					AND idk_kandidati.kandidat_nacin_odlaska = 0
					AND idk_kandidati.kandidat_drzavljanstvo_vrsta NOT LIKE  ("EU državljanin")
                    AND idk_kandidati.kandidat_dipl_id IS NOT NULL AND idk_kandidati.kandidat_pogresan_broj = 0
		';

		if (!empty($_POST['search']['value'])) {
			
			$post_search_value = trim($_POST["search"]["value"]);
			
			$query .= ' AND (CONCAT(kandidat_ime, " ", kandidat_prezime) LIKE "%' . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, " ", kandidat_ime) LIKE "%' 	 . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_prezime, kandidat_ime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR CONCAT(kandidat_ime, kandidat_prezime) LIKE "%' 	     . $post_search_value . '%" ';
			$query .= 'OR kandidat_ime LIKE "%'         						 . $post_search_value . '%" ';
			$query .= 'OR kandidat_prezime LIKE "%'         					 . $post_search_value . '%" ';
			$query .= 'OR "Prebaciti na prikupljanje 100%" LIKE "%'         	 . $post_search_value . '%" ';
			$query .= 'OR kandidat_mobitel LIKE "%' 							 . $post_search_value . '%") ';
			
		}

		$query .= ')';

		$query_prepared = $db->prepare($query);
		$query_prepared->execute();
		$recordsTotal  = $query_prepared->rowCount();
		$filtered_rows = $recordsTotal;
		
		$output = array(
			"draw" 			  => intval($_POST["draw"]),
			"recordsTotal"    => $recordsTotal,
			"recordsFiltered" => $filtered_rows,
			"data" 			  => $data
		);

		echo json_encode($output);

	break;

	case "nalozi_np":

		$requestData = $_REQUEST;
		$getSiteUrl = getSiteURLr();
		$filter_select_kompanija = $_REQUEST['filter_select_kompanija'];
		$filter_select_nalog = $_REQUEST['filter_select_nalog'];
		$filter_select_casting = $_REQUEST['filter_select_casting'];
		$filter_select_status_prijave = $_REQUEST['filter_select_status_prijave'];
		$filter_select_dipl_status = $_REQUEST['filter_select_dipl_status'];
		$filter_select_znanje_jezika = $_REQUEST['filter_select_znanje_jezika'];

		$uslov_nalog = "kandidat_nalog_id IN (".implode(',',$filter_select_nalog).")";
		$uslov_casting = "pca_appointment_id IN (".implode(',',$filter_select_casting).")";
		$uslov_status_prijave = "kan.kandidat_status_prijave IN (".implode(',',$filter_select_status_prijave).")";

		$quoted_filter_select_znanje_jezika = array_map(function($value) {
			return "'" . $value . "'";
		}, $filter_select_znanje_jezika);

		if (in_array('Bez znanja', $filter_select_znanje_jezika)) {
			$uslov_jezik = "(kj.kj_slusanje IN (" . implode(',', $quoted_filter_select_znanje_jezika) . ") OR kj.kj_slusanje IS NULL)";
		} else {
			$uslov_jezik = "(kj.kj_slusanje IN (" . implode(',', $quoted_filter_select_znanje_jezika) . "))";
		}
		
		$statuses = array();
		$podstatuses = array();
		
		// pokupi niz statuse i podstatusa poslanih
		foreach ($filter_select_dipl_status as $item) {
			$values = explode('|', $item);
			$value1 = isset($values[0]) ? $values[0] : null;
			$value2 = isset($values[1]) ? $values[1] : null;
		
			if ($value1 !== null) {
				$statuses[] = $value1;
			}
		
			if ($value2 !== null) {
				$podstatuses[] = $value2;
			}
		}

		// ostavi samo unikate
		$statuses = array_unique($statuses);
		$podstatuses = array_unique($podstatuses);
		
		// napravi stringove za IN
		$statusesString = implode(', ', $statuses);
		$podstatusesString = implode(', ', $podstatuses);

		// ukloni 1
		$keyToRemove = array_search(1, $statuses);
		if ($keyToRemove !== false) {
			unset($statuses[$keyToRemove]);
		}

		// napravi status string bez 1
		$statusesExcluded1 = implode(', ', $statuses);

		// Uslov za dipl - poslana 0 (Nije u diplu)
		if (strpos($statusesString, '0') !== false) {
			if (strpos($statusesString, '1') !== false) {
				$uslov_dipl = "((kan.kandidat_dipl_id = 0) OR (idk_nd_kandidata.status_nd_kandidata IN ($statusesExcluded1) OR (idk_nd_kandidata.status_nd_kandidata=1 AND idk_nd_kandidata.pstatus_nd_kandidata IN ($podstatusesString))))";
			} else {
				$uslov_dipl = "(kan.kandidat_dipl_id = 0 OR idk_nd_kandidata.status_nd_kandidata IN ($statusesExcluded1))";
			}
		} else {
			if (strpos($statusesString, '1') !== false) {
				// ovo je ako se posalje samo 1, onda ovaj IN baca error pa se mora provjeriti da li je IN prazan
				if(!empty($statusesExcluded1)){
					$uslov_dipl = "((idk_nd_kandidata.status_nd_kandidata IN ($statusesExcluded1)) OR (idk_nd_kandidata.status_nd_kandidata=1 AND idk_nd_kandidata.pstatus_nd_kandidata IN ($podstatusesString)))";
				}else{
					$uslov_dipl = "(idk_nd_kandidata.status_nd_kandidata=1 AND idk_nd_kandidata.pstatus_nd_kandidata IN ($podstatusesString))";
				}
			} else {
				$uslov_dipl = "(idk_nd_kandidata.status_nd_kandidata IN ($statusesExcluded1))";
			}
		}
		
		$columns = array(
			0 => 'kandidat_id',
			1 => 'kandidat_ime',
			2 => 'kandidat_status_prijave',
			3 => 'lsp_datetime',
			4 => 'status_nd_kandidata',
			5 => 'vrijeme_promjene_statusa_nd_kandidata',
			6 => 'kj_slusanje',
			7 => 'status_jezika',
			8 => 'cvl_last_updated',
			9 => 'pocetak_kursa',
			10 => 'kraj_kursa',
			11 => 'kandidat_potencijalni_pocetak_rada',
			12 => 'kandidat_dogovoreni_pocetak_rada',
			13 => 'proracunati_pocetak_rada'
		);
		
		$sql = "SELECT 
					kan.kandidat_id, 
					kandidat_ime, 
					kandidat_prezime, 
					kandidat_status_prijave,
					status_naziv, 
					kandidat_dipl_id,
					idk_nd_kandidata.status_nd_kandidata,
					kandidat_potencijalni_pocetak_rada,
					kandidat_dogovoreni_pocetak_rada,
					idk_kandidat_projekcije.proracunati_pocetak_rada,
					lsp_datetime,
					vrijeme_promjene_statusa_nd_kandidata,
					kj.kj_slusanje,

					case WHEN cvl_status is null then 'Nije provjeren'
					else cvl_status
					end as status_jezika,

					case 
						when cvl_course2_started is null then cvl_course1_started
						else cvl_course2_started
					end as pocetak_kursa,

					case 
						when cvl_course2_ended is null then cvl_course1_ended
						else cvl_course2_ended
					end as kraj_kursa,

					cvl_last_updated
				FROM idk_kandidati kan
				INNER JOIN (
					SELECT pca_kandidat_id, MAX(pca_id) AS max_pca_id
					FROM idk_pp_cand_appts
					GROUP BY pca_kandidat_id
				) max_pca ON kan.kandidat_id = max_pca.pca_kandidat_id
				INNER JOIN idk_pp_cand_appts ON kan.kandidat_id = idk_pp_cand_appts.pca_kandidat_id AND idk_pp_cand_appts.pca_id = max_pca.max_pca_id
				INNER JOIN idk_kandidat_status_prijave ON kan.kandidat_status_prijave = idk_kandidat_status_prijave.status_id
				INNER JOIN idk_log_statusi_prijave ON kan.kandidat_id = idk_log_statusi_prijave.lsp_kandidat_id AND idk_log_statusi_prijave.lsp_broj_dana IS NULL
				LEFT JOIN idk_nd_kandidata ON kan.kandidat_dipl_id = idk_nd_kandidata.id_broj_nd_kandidata
				LEFT JOIN idk_nd_kandidata_status_log ON idk_nd_kandidata.id_broj_nd_kandidata = idk_nd_kandidata_status_log.idd_broj_nd_kandidata AND broj_dana_statusa_nd_kandidata IS NULL
				LEFT JOIN idk_kandidat_projekcije ON kan.kandidat_id = idk_kandidat_projekcije.kandidat_id
				LEFT JOIN (
                        SELECT sq_kj.kj_id, sq_kj.kj_slusanje, sq_kj.kj_kandidatid, cvl_status, cvl_course1_started, cvl_course2_started, cvl_course1_ended, cvl_course2_ended, cvl_last_updated
                        FROM idk_kandidat_jezici sq_kj
                        JOIN idk_candidate_verified_languages cvl
                        ON sq_kj.kj_id = cvl.cvl_id AND cvl.cvl_active = 1
                        WHERE sq_kj.kj_naziv = 'Njemački'
                    ) kj ON kan.kandidat_id = kj.kj_kandidatid 
				WHERE
					$uslov_nalog
					AND
			   		$uslov_status_prijave
					AND
					$uslov_dipl
					AND
					$uslov_jezik
					AND
					$uslov_casting
				";
		// var_dump($sql);
		$count_sql = "SELECT
					kan.kandidat_id, 
					kandidat_ime, 
					kandidat_prezime, 
					kandidat_status_prijave,
					status_naziv, 
					kandidat_dipl_id,
					idk_nd_kandidata.status_nd_kandidata,
					kandidat_potencijalni_pocetak_rada,
					kandidat_dogovoreni_pocetak_rada,
					idk_kandidat_projekcije.proracunati_pocetak_rada,
					lsp_datetime,
					vrijeme_promjene_statusa_nd_kandidata,
					kj.kj_slusanje,

					case when cvl_course1_started is null then 'Nije provjeren'
					else cvl_status
					end as status_jezika,

					case 
						when cvl_course2_started is null then cvl_course1_started
						else cvl_course2_started
					end as pocetak_kursa,

					case 
						when cvl_course2_ended is null then cvl_course1_ended
						else cvl_course2_ended
					end as kraj_kursa,

					cvl_last_updated
				FROM idk_kandidati kan
				INNER JOIN (
					SELECT pca_kandidat_id, MAX(pca_id) AS max_pca_id
					FROM idk_pp_cand_appts
					GROUP BY pca_kandidat_id
				) max_pca ON kan.kandidat_id = max_pca.pca_kandidat_id
				INNER JOIN idk_pp_cand_appts ON kan.kandidat_id = idk_pp_cand_appts.pca_kandidat_id AND idk_pp_cand_appts.pca_id = max_pca.max_pca_id			
				INNER JOIN idk_kandidat_status_prijave ON kan.kandidat_status_prijave = idk_kandidat_status_prijave.status_id
				INNER JOIN idk_log_statusi_prijave ON kan.kandidat_id = idk_log_statusi_prijave.lsp_kandidat_id AND idk_log_statusi_prijave.lsp_broj_dana IS NULL
				LEFT JOIN idk_nd_kandidata ON kan.kandidat_dipl_id = idk_nd_kandidata.id_broj_nd_kandidata
				LEFT JOIN idk_nd_kandidata_status_log ON idk_nd_kandidata.id_broj_nd_kandidata = idk_nd_kandidata_status_log.idd_broj_nd_kandidata AND broj_dana_statusa_nd_kandidata IS NULL
				LEFT JOIN idk_kandidat_projekcije ON kan.kandidat_id = idk_kandidat_projekcije.kandidat_id
				LEFT JOIN (
                        SELECT sq_kj.kj_id, sq_kj.kj_slusanje, sq_kj.kj_kandidatid, cvl_status, cvl_course1_started, cvl_course2_started, cvl_course1_ended, cvl_course2_ended, cvl_last_updated
                        FROM idk_kandidat_jezici sq_kj
                        JOIN idk_candidate_verified_languages cvl
                        ON sq_kj.kj_id = cvl.cvl_id AND cvl.cvl_active = 1
                        WHERE sq_kj.kj_naziv = 'Njemački'
					
                    ) kj ON kan.kandidat_id = kj.kj_kandidatid
				WHERE
					$uslov_nalog
					AND
			   		$uslov_status_prijave
					AND
					$uslov_dipl
					AND
					$uslov_jezik
					AND
					$uslov_casting
				";
		
		if( !empty($requestData['search']['value']) ) {
			$sql.=" AND (CONCAT(kandidat_ime,' ',kandidat_prezime) LIKE '%".$requestData['search']['value']."%' ";
			$sql.=" OR CONCAT(kandidat_ime,kandidat_prezime) LIKE '%".$requestData['search']['value']."%' ";
			$sql.=" OR CONCAT(kandidat_prezime,' ',kandidat_ime) LIKE '%".$requestData['search']['value']."%' ";
			$sql.=" OR CONCAT(kandidat_prezime,kandidat_ime) LIKE '%".$requestData['search']['value']."%' ";
			$sql.=" OR kandidat_ime LIKE '%".$requestData['search']['value']."%' ";
			$sql.=" OR kandidat_prezime LIKE '%".$requestData['search']['value']."%' ";
			$sql.=" OR status_naziv LIKE '%".$requestData['search']['value']."%') ";

			// Ovo je za count
			$count_sql.=" AND (CONCAT(kandidat_ime,' ',kandidat_prezime) LIKE '%".$requestData['search']['value']."%' ";
			$count_sql.=" OR CONCAT(kandidat_ime,kandidat_prezime) LIKE '%".$requestData['search']['value']."%' ";
			$count_sql.=" OR CONCAT(kandidat_prezime,' ',kandidat_ime) LIKE '%".$requestData['search']['value']."%' ";
			$count_sql.=" OR CONCAT(kandidat_prezime,kandidat_ime) LIKE '%".$requestData['search']['value']."%' ";
			$count_sql.=" OR kandidat_ime LIKE '%".$requestData['search']['value']."%' ";
			$count_sql.=" OR kandidat_prezime LIKE '%".$requestData['search']['value']."%' ";
			$count_sql.=" OR status_naziv LIKE '%".$requestData['search']['value']."%') ";
		}

		// GROUP BY
		$sql .= " GROUP BY kan.kandidat_id";
		$count_sql .= " GROUP BY kan.kandidat_id";
		// var_dump($sql);
		$stmt = $db->prepare($count_sql);
		$stmt->execute();
		$row = $stmt->fetch();
		$totalData = $stmt->rowCount();
		$totalFiltered = $totalData;
	
		if($requestData['length'] != -1) {
			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		} else {
			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir'];
		}

		$stmt = $db->prepare($sql);
		$stmt->execute();
		$data = array();
	
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach($rows as $row) {
			$kandidat_id = $row['kandidat_id'];
			$kandidat_dipl_id = $row['kandidat_dipl_id'];
			$kandidat_ime = $row['kandidat_ime'];
			$kandidat_prezime = $row['kandidat_prezime'];
			$kandidat_status_prijave = $row['kandidat_status_prijave'];
			$kandidat_status_prijave_naziv = $row['status_naziv'];
			$kandidat_potencijalni_pocetak_rada = $row['kandidat_potencijalni_pocetak_rada'];
			$status_nd_kandidata = $row['status_nd_kandidata'];
			$lsp_datetime = $row['lsp_datetime'];
			$vrijeme_promjene_statusa_nd_kandidata = $row['vrijeme_promjene_statusa_nd_kandidata'];
			$kj_slusanje = $row['kj_slusanje'];
			$status_jezika = $row['status_jezika'];
			$cvl_last_updated = $row['cvl_last_updated'];
			$pocetak_kursa = $row['pocetak_kursa'];
			$kraj_kursa = $row['kraj_kursa'];
			$proracunati_pocetak_rada = $row['proracunati_pocetak_rada'];
			$kandidat_dogovoreni_pocetak_rada = $row['kandidat_dogovoreni_pocetak_rada'];
			
			if($status_jezika == 1){
				$status_jezika_text = "Samoprocjena";
			}else if($status_jezika == 2){
				$status_jezika_text = "Samostalno uči";
			}else if($status_jezika == 3){
				$status_jezika_text = "Pohađa kurs podnivo 1";
			}else if($status_jezika == 4){
				$status_jezika_text = "Pohađa kurs podnivo 2";
			}else if($status_jezika == 5){
				$status_jezika_text = "Čeka datum polaganja";
			}else if($status_jezika == 6){
				$status_jezika_text = "Čeka polaganje(ima termin)";
			}else if($status_jezika == 7){
				$status_jezika_text = "Čeka se rezultat";
			}else if($status_jezika == 8){
				$status_jezika_text = "Ima certifikat";
			}else if($status_jezika == 9){
				$status_jezika_text = "Certifikat istekao";
			}else if($status_jezika == 10){
				$status_jezika_text = "Napreduje na veći nivo";
			}else if($status_jezika == 11){
				$status_jezika_text = "Nije položen";
			}else if($status_jezika == 12){
				$status_jezika_text = "Odustao";
			}else if($status_jezika == 13){
				$status_jezika_text = "Arhiva";
			}else if($status_jezika == 14){
				$status_jezika_text = "Motiv";
			}else{
				$status_jezika_text = "Nije provjeren";
			}

			$nestedData = array();

			$nestedData[] = $kandidat_id;
			$nestedData[] = '<p class="text-left"><a href="'.$getSiteUrl.'kandidati?page=open&id='.$kandidat_id.'">'.$kandidat_ime.' '.$kandidat_prezime.'</a></p>';
			$nestedData[] = $kandidat_status_prijave_naziv;
			$nestedData[] = daysPassedFromDateToToday($lsp_datetime);
			$nestedData[] = getStatusDIPLKandidatR(getStatusValueDIPLKandidatR($kandidat_dipl_id)["status"], getStatusValueDIPLKandidatR($kandidat_dipl_id)["podstatus"]);
			$nestedData[] =	daysPassedFromDateToToday($vrijeme_promjene_statusa_nd_kandidata);
			$nestedData[] = $kj_slusanje ?? "Nema";
			$nestedData[] = $status_jezika_text ?? "Nema";
			$nestedData[] = $cvl_last_updated ?? "Nema";
			$nestedData[] = $pocetak_kursa ?? "Nema";
			$nestedData[] = $kraj_kursa ?? "Nema";
			$nestedData[] = $kandidat_potencijalni_pocetak_rada ?? "Nema";
			$nestedData[] = $kandidat_dogovoreni_pocetak_rada ?? "Nema";
			$nestedData[] = $proracunati_pocetak_rada ?? "Nema";
	
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
}
?>
