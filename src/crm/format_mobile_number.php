<head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<?php 
include("includes/functions.php");
include("includes/common.php");
$vrijeme = date("23-03-2021 00:01:00");
echo test123();
// echo date();
// echo "<br>";
// echo date("Y-m-d H:i:s");

/*
function test123(){
	Global $db;
	$query2 = $db->prepare("
		SELECT pr_datum_kreiranja
		FROM idk_predracuni
		WHERE 
		pr_kandidat_id = 21520
		AND pr_vrsta_predracuna = 1 
		AND pr_id = (
			SELECT MIN(pr_id)
			FROM idk_predracuni 
			WHERE 
			pr_kandidat_id = 21520
			AND pr_vrsta_predracuna = 1
		)
	");
	$query2->execute();
	if($query2->rowCount() != 0){
		$row2 = $query2->fetch();
		$trenutno_vrijeme = date("Y-m-d H:i:s");
		$datum_inkaso_predracuna = $row2["pr_datum_kreiranja"];
		$diff = strtotime($trenutno_vrijeme) - strtotime($datum_inkaso_predracuna);
		$days = floor($diff/86400);
		if($days > 21){
			$broj_inkaso_predracuna = 1;
		}else{
			$broj_inkaso_predracuna = 0;
		}
	}else{
		$broj_inkaso_predracuna = 0;
	}
	return $days;
}
echo date("D");
/*
$curl = curl_init();
/*
			$params = array(
				
				"scenarioKey" => "7A32331B2103607D2F890C04FEB34942",
				"destinations" => array(
					0 => array(
						"to" => array(
							"phoneNumber" => "38761938892",
							 "placeholders" => array(
								"ime" => "emir"
							) 
						)
					),
					1 => array(
						"to" => array(
							"phoneNumber" => "38762473740",
							"placeholders" => array(
								"ime" => "benjo"
							 )
						)
					)
				),
				"sms" => array(
					"text" => "testsss",
					),
				"viber" => array(
					"text" => "{{ime}}",
				)
			);

		$data = json_encode($params);
		var_dump($data);
		exit();
		
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://ej8w3r.api.infobip.com/omni/1/advanced",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => '{"scenarioKey":"7A32331B2103607D2F890C04FEB34942","destinations":[{"to":{"phoneNumber":"38761938892","placeholders":{"ime":"emir"}}},{"to":{"phoneNumber":"38762473740","placeholders":{"ime":"benjo"}}}],"sms":{"text":"testsss"},"viber":{"text":"ime"}}',
		  CURLOPT_HTTPHEADER => array(
			"accept: application/json",
			"authorization: Basic ZWJlbmRlcjE6RUJlbmRlcjY0MzI=",
			"content-type: application/json"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  echo "cURL Error #:" . $err;
		} else {
		  echo $response;
		}
		exit();
/*
Global $db;
$predracun_id = 927;
uplatiObracune(1129);
// ubaciObracune(927);
exit();
echo getInkasoKoef($predracun_id);
exit();
/*
	//get stari ink status
	$get_ink_status = $db->prepare("SELECT pr_stari_ink_status FROM idk_predracuni WHERE pr_id = :pr_id");
	$get_ink_status->execute(array( ':pr_id' => $predracun_id));
	$row_ink_status = $get_ink_status->fetch()){
	$stari_ink_status = $row_ink_status['pr_stari_ink_status'];
	switch($stari_ink_status){
		case 3: $koef = 0.1; break;
		case 4: $koef = 0.2; break;
		case 5: $koef = 0.3; break;
		default: 
			//Provjera da li je uplata nakon inkasa
			$get_ink_biljesku = $db->prepare("SELECT predracun_status FROM idk_nd_kandidata_biljeske WHERE predracun_id = :predracun_id AND status_biljeska_nd = 3 AND tip_biljeska_nd = 5");
			$get_ink_biljesku->execute(array( ':predracun_id' => $predracun_id));
			$row_ink_biljeska = $get_ink_biljesku->fetch()){
			$predracun_ink_status = $row_ink_biljeska['predracun_status'];
			switch($predracun_ink_status){
				case 3: $koef = 0.1; break;
				case 4: $koef = 0.2; break;
				case 5: $koef = 0.3; break;
				default: $koef = 0;
			}
		break;
	}
	
	return $koef;
	
*/
/*
$zaposlenici = array(119,75,121,123,127,117,128,105,114,136,120,135,130,122,131,132,129,108,109,88,49,83,32,33,11,48,92,110,111,138,104,43,90,91);
foreach($zaposlenici as $zaposlenik){
	$query_get = $db->prepare("SELECT employee_firstname, employee_lastname FROM idk_employees WHERE employee_id = $zaposlenik");
	$query_get->execute();
	$row_get = $query_get->fetch();
	echo $zaposlenik."--".$row_get['employee_firstname']." ".$row_get['employee_lastname']."<br/>";
	$query_insert = $db->prepare("
				INSERT INTO idk_nd_limiti
				(lt_emp_id)
				VALUES
				(:lt_emp_id)
				");
	$query_insert->execute(array(
				':lt_emp_id' => $zaposlenik
	));
}
*/
/*
$query_get = $db->prepare("SELECT id_broj_nd_kandidata, zaduzen_zaposlenik_nd_kandidata, mobilni_nd_kandidata FROM idk_nd_kandidata WHERE id_broj_nd_kandidata BETWEEN 16435 AND 16481");
$query_get->execute();
?> <table>
<?php
while($row_get = $query_get->fetch()){
	$id_kan = $row_get['id_broj_nd_kandidata'];
	$id_zaposlenika = $row_get['zaduzen_zaposlenik_nd_kandidata'];
	$mobilni_nd_kandidata = $row_get['mobilni_nd_kandidata'];
	if($id_zaposlenika == 139){
		$novi_query = $db->prepare("SELECT prethodni_zaposlenik_id FROM idk_nd_menadzeri_statistike WHERE idd_broj_nd_kandidata = $id_kan AND
		id_stat = (
			SELECT MAX(id_stat)
			FROM idk_nd_menadzeri_statistike
			WHERE 
			idd_broj_nd_kandidata = $id_kan
		)");
		$novi_query->execute();
		$novi_row = $novi_query->fetch();
		$id_prethodnog = $novi_row['prethodni_zaposlenik_id'];
		$tim_id = getTeamIdByEmployee($id_prethodnog);
		$skl = "skl";
	}else{
		$id_prethodnog = "nema_pret";
		$tim_id = getTeamIdByEmployee($id_zaposlenika);
		$skl= "zaduzen";
	}
	?><tr><td><?php echo $id_kan; ?></td><?php
	?><td><?php echo $id_zaposlenika; ?></td><?php
	?><td><?php echo "tel".$mobilni_nd_kandidata; ?></td><?php
	?><td><?php echo $id_prethodnog; ?></td><?php
	?><td><?php echo "tim_id".$tim_id."-".$skl; ?></td></tr><?php
	$query_update_tim = $db->prepare("
		UPDATE idk_nd_kandidata
		SET tim_nd_kandidata = :tim_nd_kandidata
		WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
	");

	$query_update_tim->execute(array(
		':id_broj_nd_kandidata' => $id_kan,
		':tim_nd_kandidata' => $tim_id
	));
}?> </table>
<?php
*/
//FORMATIRANJE BROJEVA
/*
SELECT kon.kki_id, kon.kki_grupa, kon.kki_naziv, kon.kki_podatak, kon.kki_kandidat_id, kan.kandidat_drzava
FROM idk_kandidat_kontakt_info kon
JOIN idk_kandidati kan
ON kon.kki_kandidat_id = kan.kandidat_id
WHERE kon.kki_naziv != 'E-mail' AND kan.kandidat_id BETWEEN 1001 AND 2000

// SELECT CONCAT("'", id, "'=>'", mail, "'") AS ConcatenatedString FROM firme WHERE stranica_id = 1

//=IF(LEFT(H2,2)="43",RIGHT(H2,LEN(H2)-2),RIGHT(H2,LEN(H2)-0)) za brisanje pozivnih
//=CONCATENATE("'",A2,"'=>'",I2,"'") na kraju za sve sabrat i u php metat

//$brojevi = array('1'=>'e.bender@job-step.net');
$brojevi = array('32954'=>'+491605785506','31941'=>'+491626017778');
foreach($brojevi as $id => $broj){
	$query_update = $db->prepare("
					UPDATE idk_kandidat_kontakt_info
					SET	kki_podatak = :kki_podatak
					WHERE kki_id = :kki_id");

	$query_update->execute(array(
				':kki_podatak' => $broj,
				':kki_id' => $id
				));
				
	$get_kandidat_id = $db->prepare("
				SELECT kki_kandidat_id
				FROM idk_kandidat_kontakt_info
				WHERE kki_id = :kki_id
	");
	
	$get_kandidat_id->execute(array(
			':kki_id' => $id
	));
	
	$row_kan = $get_kandidat_id->fetch();
	$kandidat_id = $row_kan['kki_kandidat_id'];
	
	$query_update_kandidati = $db->prepare("
					UPDATE idk_kandidati
					SET	kandidat_mobitel = :kandidat_mobitel
					WHERE kandidat_id = :kandidat_id");

	$query_update_kandidati->execute(array(
				':kandidat_mobitel' => $broj,
				':kandidat_id' => $kandidat_id
				));
}
*/

//SLANJE SMS-a ONIMA KOJI NISU INSTALIRALI MESSENGER
/*
$query_list = $db->prepare("
			SELECT k.kandidat_id, k.kandidat_ime, k.kandidat_prezime, k.kandidat_status, k.kandidat_status_messenger, k.kandidat_prijava_na, u.nalog_id, k.kandidat_mobitel, u.email, u.ponovno_slanje
				FROM idk_kandidati k 
				JOIN users u
				on k.kandidat_id = u.kandidat_id
				WHERE k.kandidat_id > 9831 and k.kandidat_status_messenger = 3 and k.kandidat_status != 3 AND u.nalog_id = 105
");

$query_list->execute();
$ct=1;
while($row_list = $query_list->fetch()){
	
	$kandidat_id = $row_list['kandidat_id'];
	$full_name = $row_list['kandidat_ime']." ".$row_list['kandidat_prezime'];
	$kandidat_mobitel = $row_list['kandidat_mobitel'];
	$email = $row_list['email'];
	$kandidat_status = $row_list['kandidat_status'];
	$ponovno_slanje = $row_list['ponovno_slanje'];
	$dupli = getDoubleExistence($row_list['kandidat_ime'], $row_list['kandidat_prezime']);
	$link = "https://jobstep-app.com/prijava/".$kandidat_id;
	if($dupli == 0){
		//sendSMSStariKandidatiINFOBIP($kandidat_id, $kandidat_mobitel, $link, $full_name);
		echo $ct++.". ".$ponovno_slanje."---".$kandidat_id."---".$full_name."---".$kandidat_mobitel."---".$email."<br/>";
	}
}


*/


//STATS MESSENGER
/*
$id_od = 9001;
$id_do = 9830;
$q_linkovi = $db->prepare("SELECT 
				COUNT(kandidat_id) as broj_pregleda
				FROM idk_kandidati
				WHERE kandidat_id BETWEEN $id_od AND $id_do AND kandidat_mobitel is not null AND kandidat_pogledao_link_prijave = 1
				");
$q_linkovi->execute();
$row_link = $q_linkovi->fetch();
$otvoren_link = $row_link["broj_pregleda"];
echo "Otvorili link iz SMS-a: ".$otvoren_link."<br/>";
// AND ponovno_slanje = 1
$q_instal = $db->prepare("SELECT COUNT(id) as broj_instal FROM users WHERE kandidat_id BETWEEN $id_od AND $id_do 
				");
$q_instal->execute();
$row_instal = $q_instal->fetch();
$instalirano = $row_instal["broj_instal"];
echo "Prijavilo se : ".$instalirano."<br/>";

$q_obrada = $db->prepare("
			SELECT count(k.kandidat_status) as broj, k.kandidat_status
			FROM users u 
			JOIN idk_kandidati k
			ON u.kandidat_id = k.kandidat_id
			WHERE k.kandidat_id BETWEEN $id_od AND $id_do AND k.kandidat_status_messenger  != 0
			GROUP BY k.kandidat_status 
");
$q_obrada->execute();
$presli_kontrolu = 0;
$inst_mes = 0;
while($row_obrada = $q_obrada->fetch()){
	$status = $row_obrada['kandidat_status'];
	$broj = $row_obrada['broj'];
	if($status == 1){
		$naziv_statusa = "U obradi";
		$inst_mes = $inst_mes - $broj;
	}
	else if($status == 2)
		$naziv_statusa = "Obrađen";
	else if($status == 4)
		$naziv_statusa = "Kontrola";
	else if($status == 5)
		$naziv_statusa = "Dopuna";
	echo $naziv_statusa.": ".$broj."<br/>";
	$inst_mes = $inst_mes + $broj;
	$ukupno = $ukupno + $broj;
}
echo "Ispunili podatke: ".$inst_mes."<br/>";
echo "Instalirali messenger: ".$ukupno;
*/

//STATS DIPL:
/*
$query_user = $db->prepare("
						SELECT dipl_obavijest, 
							COUNT(*) as total,
							sum(case when notifikacije = 1 then 1 else 0 end) as broj1,
							sum(case when notifikacije = 2 then 1 else 0 end) as broj2,
							sum(case when notifikacije = 3 then 1 else 0 end) as broj3,
							sum(case when notifikacije = 4 then 1 else 0 end) as broj4,
							sum(case when notifikacije = 5 then 1 else 0 end) as broj5,
							sum(case when notifikacije = 6 then 1 else 0 end) as broj6
						FROM users
						WHERE dipl_obavijest is not null
						GROUP BY dipl_obavijest
	");

$query_user->execute();
$trenutna_notf = 1;
?>
<table class="table-bordered">
<thead>
	<th>status</th>
	<th>obradjeni</th>
	<th>doradeni</th>
	<th>nezavrseni</th>
	<th>pon obr</th>
	<th>pon dor</th>
	<th>pon nez</th>
	<th>total</th>
</thead>
<?php
while($row_user = $query_user->fetch()){
	$dipl_obavijest = $row_user['dipl_obavijest'];
	$notifikacije = $row_user['notifikacije'];
	$broj1 = $row_user['broj1'];
	$broj2 = $row_user['broj2'];
	$broj3 = $row_user['broj3'];
	$broj4 = $row_user['broj4'];
	$broj5 = $row_user['broj5'];
	$broj6 = $row_user['broj6'];
	$total = $row_user['total'];
	switch ($dipl_obavijest){
		case 1: $status = "Nema nostrifikovanu diplomu "; break;
		case 2: $status = "Ima  nostrifikovanu diplomu "; break;
		case 3: $status = "prosla notif, ali nije jos otvorena "; break;
		case 4: $status = "nije prosla notif "; break;
		case 5: $status = "otvorio notif, nista nije kliknuo "; break;
	}
	?>
	<tr>
	<?php
	?>
		<td><?php echo $status; ?></td>
		<td align="right"><?php echo $broj1; ?></td>
		<td align="right"><?php echo $broj2; ?></td>
		<td align="right"><?php echo $broj3; ?></td>
		<td align="right"><?php echo $broj4; ?></td>
		<td align="right"><?php echo $broj5; ?></td>
		<td align="right"><?php echo $broj6; ?></td>
		<td align="right"><?php echo $total; ?></td>
		
		<?php
	?>
	</tr>
	<?php
} */
?>
 <!-- </table> -->

<?php
//FJE ZA SLANJE VIBER PORUKA NAKON PRIJAVE
/*
function viberPrijava($broj, $text_sms, $text_viber){
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://ej8w3r.api.infobip.com/omni/1/advanced",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => "{ \"scenarioKey\":\"7A32331B2103607D2F890C04FEB34942\", \"destinations\":[ { \"to\":{ \"phoneNumber\":\"".$broj."\" } } ], \"sms\":{ \"text\":\"".$text_sms."\" }, \"viber\":{ \"text\":\"".$text_viber."\" } }",
	  CURLOPT_HTTPHEADER => array(
		"accept: application/json",
		"authorization: Basic ZWJlbmRlcjE6RUJlbmRlcjY0MzI=",
		"content-type: application/json"
	  ),
	));
	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
	  echo "cURL Error #:" . $err;
	} else {
	  echo $response;
	}
	echo "<br>".$broj."<br>";
}
function viberPrijava2($broj, $text_sms, $text_viber, $btn_text, $link){
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://ej8w3r.api.infobip.com/omni/1/advanced",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => "{ \"scenarioKey\":\"7A32331B2103607D2F890C04FEB34942\", \"destinations\":[ { \"to\":{ \"phoneNumber\":\"".$broj."\" } } ], \"sms\":{ \"text\":\"".$text_sms."\" }, \"viber\":{ \"text\":\"".$text_viber."\", \"buttonText\":\"".$btn_text."\", \"buttonURL\":\"".$link."\" } }",
	  CURLOPT_HTTPHEADER => array(
		"accept: application/json",
		"authorization: Basic ZWJlbmRlcjE6RUJlbmRlcjY0MzI=",
		"content-type: application/json"
	  ),
	));
	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
	  echo "cURL Error #:" . $err;
	} else {
	  echo $response;
	}
	echo "<br>".$broj."<br>";
}

$kandidat_email = "emir123";
$sifra = "456789";
$text_1 = "JOBSTEP Agencija Vam se zahvaljuje na registraciji. U narednoj SMS poruci cete dobiti link putem kojeg mozete preuzeti nasu aplikaciju.";
$text_2 = "Aplikaciju preuzmite na sljedecem linku : https://jobstep-app.com/download ";
$text_3 = "Koristite Vase korisnicko ime: ".$kandidat_email." i PIN: ".$sifra." za prijavu u Jobstep Messenger. ";
$text_4 = "U slucaju dodatnih informacija oko preuzimanja i popunjavanja trazenih podataka, upute pogledajte na videu koji se nalazi na linku: http://bit.ly/2IgsRwN ";

$text_viber1 = 'JOBSTEP Agencija Vam se zahvaljuje na registraciji.';
$text_viber2 = 'Kako bismo ispunili sve neophodne podatke preuzmite Jobstep Messenger na slijedećem linku:';
$text_viber3 = 'Vaši pristupni podaci za Jobstep Messenger su slijedeći\nKORISNIČKO IME: '.$kandidat_email.'\nPIN: '.$sifra.'\n';
$text_viber4 = 'Uputstvo za korištenje aplikacije možete pronaći ovdje: ';

$btn_text1 = "PREUZMI";
$btn_text2 = "UPUTSTVO";
$link1 = "https://jobstep-app.com/download";
$link2 = "http://bit.ly/2IgsRwN";

$tel = "38761938892";
$tel_ado = "38763453196";
$tel_ama = "38762926573";
$tel_bez_vibera = "38763295064";
// viberPrijava($tel_bez_vibera, $text_1, $text_viber1);
// viberPrijava2($tel_bez_vibera, $text_2, $text_viber2, $btn_text1, $link1);
// viberPrijava($tel_bez_vibera, $text_3, $text_viber3);
// viberPrijava2($tel_bez_vibera, $text_4, $text_viber4, $btn_text2, $link2);
*/


//STATS DIPL SMS-ovi:
/*
SELECT 
sum(case when kandidat_poslan_dipl = 2 or kandidat_poslan_dipl = 21 or kandidat_poslan_dipl = 22 or kandidat_poslan_dipl = 23 then 1 else 0 end) as poslano,
sum(case when kandidat_poslan_dipl = 21 or kandidat_poslan_dipl = 22 or kandidat_poslan_dipl = 23 then 1 else 0 end) as pogledano,
sum(case when kandidat_poslan_dipl = 22 then 1 else 0 end) as ima_nodi,
sum(case when kandidat_poslan_dipl = 23 then 1 else 0 end) as nema_nodi,
sum(case when kandidat_poslan_dipl = 2 or kandidat_poslan_dipl = 21 or kandidat_poslan_dipl = 22 or kandidat_poslan_dipl = 23 then 1 else 0 end) as total
FROM idk_kandidati 
WHERE kandidat_poslan_dipl != 0
*/
$query_kan = $db->prepare("
						SELECT kandidat_poslan_dipl, 
							sum(case when kandidat_id BETWEEN 1 AND 1586 then 1 else 0 end) as prvopoc,
							sum(case when kandidat_id BETWEEN 9716 AND 11424 then 1 else 0 end) as prvokraj,
							sum(case when kandidat_id BETWEEN 1588 AND 2813 then 1 else 0 end) as drugopoc,
							sum(case when kandidat_id BETWEEN 8722 AND 9712 then 1 else 0 end) as drugokraj,
							sum(case when kandidat_id BETWEEN 2825 AND 3686 then 1 else 0 end) as trecepoc,
							sum(case when kandidat_id BETWEEN 7356 AND 8725 then 1 else 0 end) as trecekraj,
							sum(case when kandidat_id BETWEEN 3688 AND 4853 then 1 else 0 end) as cetvrto_I,
							sum(case when kandidat_id BETWEEN 4856 AND 6088 then 1 else 0 end) as cetvrto_II,
							sum(case when kandidat_id BETWEEN 6091 AND 7353 then 1 else 0 end) as cetvrto_III,
							count(*) as total
						FROM idk_kandidati WHERE kandidat_poslan_dipl NOT IN (0,1,11,13,3,4,5,52,53,51,6,61,62,63,7,71,72,73,8,81,82,83,9,91,92,93) GROUP BY kandidat_poslan_dipl
	");

$query_kan->execute();
?>
<table class="table-bordered" style=" width: 40%;">
<thead>
	<th class="text-center">dosao do</th>
	<th class="text-center">1. slanje (spocetka)</th>
	<th class="text-center">1. slanje (skraja)</th>
	<th class="text-center">2. slanje (spocetka)</th>
	<th class="text-center">2. slanje (skraja)</th>
	<th class="text-center">3. slanje (spocetka)</th>
	<th class="text-center">3. slanje (skraja)</th>
	<th class="text-center">4. slanje (I dio)</th>
	<th class="text-center">4. slanje (II dio)</th>
	<th class="text-center">4. slanje (III dio)</th>
	<th class="text-center">total</th>
</thead>
<?php
while($row_kan = $query_kan->fetch()){
	$prvopoc = $row_kan['prvopoc'];
	$prvokraj = $row_kan['prvokraj'];
	$drugopoc = $row_kan['drugopoc'];
	$drugokraj = $row_kan['drugokraj'];
	$trecepoc = $row_kan['trecepoc'];
	$trecekraj = $row_kan['trecekraj'];
	$cetvrto_I = $row_kan['cetvrto_I'];
	$cetvrto_II = $row_kan['cetvrto_II'];
	$cetvrto_III = $row_kan['cetvrto_III'];
	$total = $row_kan['total'];
	$kandidat_poslan_dipl = $row_kan['kandidat_poslan_dipl'];
	switch ($kandidat_poslan_dipl){
		case 2: $status = "poslan sms, dalje nista"; break;
		case 21: $status = "pogledao link, nista kliknuo"; break;
		case 22: $status = "ima nostrifikovanu diplomu"; break;
		case 23: $status = "nema nostrifikovanu diplomu"; break;
		
	}
	?>
	<tr>
	<?php
	?>
		<td><?php echo $status; ?></td>
		<td align="center"><?php echo $prvopoc; ?></td>
		<td align="center"><?php echo $prvokraj; ?></td>
		<td align="center"><?php echo $drugopoc; ?></td>
		<td align="center"><?php echo $drugokraj; ?></td>
		<td align="center"><?php echo $trecepoc; ?></td>
		<td align="center"><?php echo $trecekraj; ?></td>
		<td align="center"><?php echo $cetvrto_I; ?></td>
		<td align="center"><?php echo $cetvrto_II; ?></td>
		<td align="center"><?php echo $cetvrto_III; ?></td>
		<td align="center"><?php echo $total; ?></td>
		
		<?php
	?>
	</tr>
	<?php
}
?>

</table>
<h4>VIBER</h4>
<?php
$query_kan_v = $db->prepare("
						SELECT kandidat_poslan_dipl, 
							sum(case when kandidat_id BETWEEN 1 AND 1586 then 1 else 0 end) as prvopoc,
							sum(case when kandidat_id BETWEEN 9716 AND 11424 then 1 else 0 end) as prvokraj,
							sum(case when kandidat_id BETWEEN 1588 AND 2813 then 1 else 0 end) as drugopoc,
							sum(case when kandidat_id BETWEEN 8722 AND 9712 then 1 else 0 end) as drugokraj,
							sum(case when kandidat_id BETWEEN 2825 AND 3686 then 1 else 0 end) as trecepoc,
							sum(case when kandidat_id BETWEEN 7356 AND 8725 then 1 else 0 end) as trecekraj,
							count(*) as total
						FROM idk_kandidati WHERE kandidat_poslan_dipl NOT IN (0,1,11,13,3,4,2,21,22,23,6,61,62,63,7,71,72,73,8,81,82,83,9,91,92,93) GROUP BY kandidat_poslan_dipl
	");

$query_kan_v->execute();
?>
<table class="table-bordered" style=" width: 40%;">
<thead>
	<th class="text-center">dosao do</th>
	<th class="text-center">1. slanje (spocetka)</th>
	<th class="text-center">1. slanje (skraja)</th>
	<th class="text-center">2. slanje (spocetka)</th>
	<th class="text-center">2. slanje (skraja)</th>
	<th class="text-center">3. slanje (spocetka)</th>
	<th class="text-center">3. slanje (skraja)</th>
	<th class="text-center">total</th>
</thead>
<?php
while($row_kan = $query_kan_v->fetch()){
	$prvopoc = $row_kan['prvopoc'];
	$prvokraj = $row_kan['prvokraj'];
	$drugopoc = $row_kan['drugopoc'];
	$drugokraj = $row_kan['drugokraj'];
	$trecepoc = $row_kan['trecepoc'];
	$trecekraj = $row_kan['trecekraj'];
	$total = $row_kan['total'];
	$kandidat_poslan_dipl = $row_kan['kandidat_poslan_dipl'];
	switch ($kandidat_poslan_dipl){
		case 5: $status = "viber - poslan sms, dalje nista"; break;
		case 51: $status = "viber - pogledao link, nista kliknuo"; break;
		case 52: $status = "viber - ima nostrifikovanu diplomu"; break;
		case 53: $status = "viber - nema nostrifikovanu diplomu"; break;
	}
	?>
	<tr>
	<?php
	?>
		<td><?php echo $status; ?></td>
		<td align="center"><?php echo $prvopoc; ?></td>
		<td align="center"><?php echo $prvokraj; ?></td>
		<td align="center"><?php echo $drugopoc; ?></td>
		<td align="center"><?php echo $drugokraj; ?></td>
		<td align="center"><?php echo $trecepoc; ?></td>
		<td align="center"><?php echo $trecekraj; ?></td>
		<td align="center"><?php echo $total; ?></td>
		
		<?php
	?>
	</tr>
	<?php
}
?>

</table>
<hr>
<b>Kampanja peta - Normal sve, samo novi kandidati</b>
<?php
$query_kan_novi = $db->prepare("
						SELECT kandidat_poslan_dipl,
							sum(case when kandidat_mobitel LIKE '%+387%' then 1 else 0 end) as bih,
							sum(case when kandidat_mobitel LIKE '%+381%' then 1 else 0 end) as srb,
							count(*) as total_svi
						FROM idk_kandidati WHERE kandidat_poslan_dipl IN (5,51,52,53) AND kandidat_id > 17727 GROUP BY kandidat_poslan_dipl
	");
	
$query_kan_novi->execute();
?>
<table class="table-bordered" style=" width: 40%;">
<thead>
	<th class="text-center">dosao do</th>
	<th class="text-center">BiH</th>
	<th class="text-center">Srbija</th>
	<th class="text-center">total</th>
</thead>
<?php
$ukupno_novi = 0;
$ukupno_novi_bih = 0;
$ukupno_novi_srb = 0;
while($row_kan_novi = $query_kan_novi->fetch()){
	
	$bih_novi = $row_kan_novi['bih'];
	$srb_novi= $row_kan_novi['srb'];
	$total_svi_novi= $row_kan_novi['total_svi'];
	$kandidat_poslan_dipl_novi = $row_kan_novi['kandidat_poslan_dipl'];
	switch ($kandidat_poslan_dipl_novi){
		case 5: $status_novi = "poslan sms, dalje nista"; break;
		case 51: $status_novi= "pogledao link, nista kliknuo"; break;
		case 52: $status_novi= "Ne zeli saznati"; break;
		case 53: $status_novi= "Zeli saznati kako otici"; break;
	}
	?>
	<tr>
	<?php
	?>
		<td><?php echo $status_novi; ?></td>
		<td align="center"><?php echo $bih_novi; ?></td>
		<td align="center"><?php echo $srb_novi; ?></td>
		<td align="center"><?php echo $total_svi_novi; ?></td>
		
		<?php
		$ukupno_novi+=$total_svi_novi;
		$ukupno_novi_bih+=$bih_novi;
		$ukupno_novi_srb+=$srb_novi;
	?>
	</tr>
	<?php
}
?>
<tr>
	<td>Ukupno</td>
	<td align="center"><?php echo $ukupno_novi_bih; ?></td>
	<td align="center"><?php echo $ukupno_novi_srb; ?></td>
	<td align="center"><?php echo $ukupno_novi; ?></td>
</tr>
</table>
<hr>
<b>Kampanja cetvrta - Stornirani termini</b>
<?php
$query_kan_storn = $db->prepare("
						SELECT kandidat_poslan_dipl,
							sum(case when kandidat_mobitel LIKE '%+387%' then 1 else 0 end) as bih,
							sum(case when kandidat_mobitel LIKE '%+381%' then 1 else 0 end) as srb,
							count(*) as total_svi
						FROM idk_kandidati WHERE kandidat_poslan_dipl IN (9,91,92,93) GROUP BY kandidat_poslan_dipl
	");
	
$query_kan_storn->execute();
?>
<table class="table-bordered" style=" width: 40%;">
<thead>
	<th class="text-center">dosao do</th>
	<th class="text-center">BiH</th>
	<th class="text-center">Srbija</th>
	<th class="text-center">total</th>
</thead>
<?php
$ukupno_storn = 0;
$ukupno_storn_bih = 0;
$ukupno_storn_srb = 0;
while($row_kan_storn = $query_kan_storn->fetch()){
	
	$bih_storn = $row_kan_storn['bih'];
	$srb_storn = $row_kan_storn['srb'];
	$total_svi_storn = $row_kan_storn['total_svi'];
	$kandidat_poslan_dipl_storn = $row_kan_storn['kandidat_poslan_dipl'];
	switch ($kandidat_poslan_dipl_storn){
		case 9: $status_storn = "poslan sms, dalje nista"; break;
		case 91: $status_storn = "pogledao link, nista kliknuo"; break;
		case 92: $status_storn = "Ne zeli saznati"; break;
		case 93: $status_storn = "Zeli saznati kako otici"; break;
	}
	?>
	<tr>
	<?php
	?>
		<td><?php echo $status_storn; ?></td>
		<td align="center"><?php echo $bih_storn; ?></td>
		<td align="center"><?php echo $srb_storn; ?></td>
		<td align="center"><?php echo $total_svi_storn; ?></td>
		
		<?php
		$ukupno_storn+=$total_svi_storn;
		$ukupno_storn_bih+=$bih_storn;
		$ukupno_storn_srb+=$srb_storn;
	?>
	</tr>
	<?php
}
?>
<tr>
	<td>Ukupno</td>
	<td align="center"><?php echo $ukupno_storn_bih; ?></td>
	<td align="center"><?php echo $ukupno_storn_srb; ?></td>
	<td align="center"><?php echo $ukupno_storn; ?></td>
</tr>
</table>
<hr>
<b>Kampanja treca "ne treba B1" + Korona</b>
<?php
$query_kan_cor_tr = $db->prepare("
						SELECT kandidat_poslan_dipl,
							sum(case when kandidat_mobitel LIKE '%+387%' then 1 else 0 end) as bih,
							sum(case when kandidat_mobitel LIKE '%+381%' then 1 else 0 end) as srb,
							count(*) as total_svi
						FROM idk_kandidati WHERE kandidat_poslan_dipl IN (8,81,82,83) GROUP BY kandidat_poslan_dipl
	");
	
$query_kan_cor_tr->execute();
?>
<table class="table-bordered" style=" width: 40%;">
<thead>
	<th class="text-center">dosao do</th>
	<th class="text-center">BiH</th>
	<th class="text-center">Srbija</th>
	<th class="text-center">total</th>
</thead>
<?php
$ukupno_cor_tr = 0;
$ukupno_cor_bih_tr = 0;
$ukupno_cor_srb_tr = 0;
while($row_kan_cor_tr = $query_kan_cor_tr->fetch()){
	
	$bih_cor_tr = $row_kan_cor_tr['bih'];
	$srb_cor_tr = $row_kan_cor_tr['srb'];
	$total_svi_cor_tr = $row_kan_cor_tr['total_svi'];
	$kandidat_poslan_dipl_cor_tr = $row_kan_cor_tr['kandidat_poslan_dipl'];
	switch ($kandidat_poslan_dipl_cor_tr){
		case 8: $status_cor_tr = "poslan sms, dalje nista"; break;
		case 81: $status_cor_tr = "pogledao link, nista kliknuo"; break;
		case 82: $status_cor_tr = "ima nostrifikovanu diplomu"; break;
		case 83: $status_cor_tr = "nema nostrifikovanu diplomu"; break;
	}
	?>
	<tr>
	<?php
	?>
		<td><?php echo $status_cor_tr; ?></td>
		<td align="center"><?php echo $bih_cor_tr; ?></td>
		<td align="center"><?php echo $srb_cor_tr; ?></td>
		<td align="center"><?php echo $total_svi_cor_tr; ?></td>
		
		<?php
		$ukupno_cor_bih_tr+=$bih_cor_tr;
		$ukupno_cor_srb_tr+=$srb_cor_tr;
		$ukupno_cor_tr+=$total_svi_cor_tr;
	?>
	</tr>
	<?php
}
?>
<tr>
	<td>Ukupno</td>
	<td align="center"><?php echo $ukupno_cor_bih_tr; ?></td>
	<td align="center"><?php echo $ukupno_cor_srb_tr; ?></td>
	<td align="center"><?php echo $ukupno_cor_tr; ?></td>
</tr>
</table>
<hr>
<b>Kampanja druga "ne treba B1" + Korona</b>
<?php
$query_kan_cor = $db->prepare("
						SELECT kandidat_poslan_dipl,
							sum(case when kandidat_mobitel LIKE '%+387%' then 1 else 0 end) as bih,
							sum(case when kandidat_mobitel LIKE '%+381%' then 1 else 0 end) as srb,
							count(*) as total_svi
						FROM idk_kandidati WHERE kandidat_poslan_dipl IN (7,71,72,73) GROUP BY kandidat_poslan_dipl
	");
	
$query_kan_cor->execute();
?>
<table class="table-bordered" style=" width: 40%;">
<thead>
	<th class="text-center">dosao do</th>
	<th class="text-center">BiH</th>
	<th class="text-center">Srbija</th>
	<th class="text-center">total</th>
</thead>
<?php
$ukupno_cor = 0;
$ukupno_cor_bih = 0;
$ukupno_cor_srb = 0;
while($row_kan_cor = $query_kan_cor->fetch()){
	
	$bih_cor = $row_kan_cor['bih'];
	$srb_cor = $row_kan_cor['srb'];
	$total_svi_cor = $row_kan_cor['total_svi'];
	$kandidat_poslan_dipl_cor = $row_kan_cor['kandidat_poslan_dipl'];
	switch ($kandidat_poslan_dipl_cor){
		case 7: $status_cor = "poslan sms, dalje nista"; break;
		case 71: $status_cor = "pogledao link, nista kliknuo"; break;
		case 72: $status_cor = "ima nostrifikovanu diplomu"; break;
		case 73: $status_cor = "nema nostrifikovanu diplomu"; break;
	}
	?>
	<tr>
	<?php
	?>
		<td><?php echo $status_cor; ?></td>
		<td align="center"><?php echo $bih_cor; ?></td>
		<td align="center"><?php echo $srb_cor; ?></td>
		<td align="center"><?php echo $total_svi_cor; ?></td>
		
		<?php
		$ukupno_cor_bih+=$bih_cor;
		$ukupno_cor_srb+=$srb_cor;
		$ukupno_cor+=$total_svi_cor;
	?>
	</tr>
	<?php
}
?>
<tr>
	<td>Ukupno</td>
	<td align="center"><?php echo $ukupno_cor_bih; ?></td>
	<td align="center"><?php echo $ukupno_cor_srb; ?></td>
	<td align="center"><?php echo $ukupno_cor; ?></td>
</tr>
</table>
<hr>
<b>Kampanja prva "ne treba B1"</b>
<?php
$query_kan3 = $db->prepare("
						SELECT kandidat_poslan_dipl,
							
							count(*) as total_svi
						FROM idk_kandidati WHERE kandidat_poslan_dipl NOT IN (0,1,11,13,3,4,2,21,22,23,5,51,52,53,7,71,72,73,8,81,82,83,9,91,92,93) GROUP BY kandidat_poslan_dipl
	");
	
$query_kan3->execute();
?>
<table class="table-bordered" style=" width: 40%;">
<thead>
	<th class="text-center">dosao do</th>
	<th class="text-center">broj</th>
</thead>
<?php
$ukupno = 0;
while($row_kan3 = $query_kan3->fetch()){
	
	$total_svi = $row_kan3['total_svi'];
	$kandidat_poslan_dipl3 = $row_kan3['kandidat_poslan_dipl'];
	switch ($kandidat_poslan_dipl3){
		case 6: $status3 = "poslan sms, dalje nista"; break;
		case 61: $status3 = "pogledao link, nista kliknuo"; break;
		case 62: $status3 = "ima nostrifikovanu diplomu"; break;
		case 63: $status3 = "nema nostrifikovanu diplomu"; break;
	}
	?>
	<tr>
	<?php
	?>
		<td><?php echo $status3; ?></td>
		<td align="center"><?php echo $total_svi; ?></td>
		
		<?php
		$ukupno+=$total_svi;
	?>
	</tr>
	<?php
}
?>
<tr>
	<td>Ukupno</td>
	<td align="center"><?php echo $ukupno; ?></td>
</tr>
</table>
<hr>
<b>prvo slanje SMS</b>
<?php
$query_kan2 = $db->prepare("
						SELECT kandidat_poslan_dipl,
							sum(case when kandidat_id IN (48, 127, 135, 137, 165, 193, 266, 297, 317, 371, 376, 429, 430, 464, 467, 522, 533, 571, 705, 717, 727, 728, 759, 777, 794, 800, 801, 802, 810, 813, 814, 815, 823, 824, 834, 852, 870, 873, 893, 898, 905, 908, 931, 953, 965, 1008, 1010, 1016, 1032, 1068, 1084, 1141, 1198, 1202, 1209, 1389, 1399, 1404, 1410, 1432, 1492, 1510, 1521, 1546, 1567, 1581, 9716, 9725, 9728, 9749, 9750, 9770, 9799, 9807, 9820, 9826, 9827, 9839, 9898, 9908, 9930, 9954, 9968, 9993, 10066, 10082, 10087, 10108, 10133, 10144, 10177, 10198, 10216, 10252, 10264, 10282, 10289, 10311, 10369, 10381, 10422, 10429, 10440, 10449, 10460, 10475, 10522, 10529, 10560, 10565, 10569, 10583, 10593, 10607, 10625, 10662, 10679, 10713, 10714, 10715, 10716, 10731, 10739, 10750, 10752, 10777, 10797, 10801, 10832, 10843, 10877, 10881, 10919, 10932, 10949, 10951, 10979, 11029, 11041, 11057, 11065, 11080, 11156, 11159, 11168, 11200, 11232, 11245, 11251) then 1 else 0 end) as ponovo_pogl,
							sum(case when kandidat_id IN (65, 98, 104, 105, 113, 117, 123, 136, 139, 143, 144, 154, 167, 178, 179, 190, 191, 194, 195, 204, 213, 216, 220, 234, 235, 243, 245, 248, 251, 253, 256, 258, 264, 268, 269, 279, 293, 298, 300, 302, 305, 306, 307, 308, 309, 312, 313, 320, 321, 328, 335, 336, 338, 341, 344, 351, 359, 363, 367, 368, 369, 372, 375, 390, 399, 406, 408, 411, 412, 415, 416, 424, 439, 440, 442, 445, 447, 450, 454, 456, 460, 469, 472, 477, 481, 484, 485, 486, 487, 496, 500, 501, 503, 504, 506, 518, 520, 524, 525, 526, 527, 528, 529, 530, 531, 532, 542, 543, 552, 568, 569, 573, 577, 578, 579, 581, 585, 586, 587, 588, 589, 592, 593, 595, 596, 600, 601, 603, 606, 615, 668, 671, 688, 691, 692, 694, 695, 711, 712, 713, 715, 720, 722, 723, 724, 725, 726, 736, 737, 741, 742, 743, 744, 746, 748, 751, 752, 753, 754, 756, 758, 762, 763, 764, 766, 768, 775, 776, 778, 779, 785, 787, 788, 790, 796, 797, 799, 804, 805, 806, 807, 811, 816, 818, 820, 821, 827, 830, 831, 833, 836, 844, 845, 849, 853, 857, 858, 859, 862, 864, 867, 869, 872, 874, 879, 880, 883, 885, 886, 887, 889, 891, 892, 894, 895, 897, 899, 901, 906, 909, 912, 914, 915, 919, 921, 922, 923, 929, 933, 936, 940, 942, 946, 949, 950, 951, 954, 955, 956, 958, 962, 963, 974, 978, 982, 985, 989, 991, 995, 998, 1002, 1003, 1011, 1012, 1013, 1015, 1024, 1029, 1030, 1034, 1035, 1036, 1039, 1041, 1050, 1052, 1060, 1067, 1070, 1086, 1087, 1088, 1092, 1096, 1097, 1116, 1118, 1119, 1123, 1124, 1127, 1128, 1131, 1132, 1140, 1153, 1156, 1158, 1168, 1172, 1173, 1174, 1176, 1178, 1181, 1182, 1191, 1192, 1193, 1200, 1207, 1215, 1222, 1237, 1242, 1243, 1244, 1247, 1248, 1257, 1264, 1265, 1266, 1267, 1268, 1269, 1270, 1271, 1274, 1279, 1284, 1289, 1291, 1294, 1302, 1306, 1356, 1358, 1359, 1376, 1377, 1381, 1383, 1386, 1387, 1391, 1396, 1403, 1405, 1406, 1408, 1414, 1415, 1416, 1419, 1421, 1423, 1428, 1433, 1434, 1436, 1444, 1445, 1456, 1459, 1461, 1466, 1472, 1473, 1480, 1481, 1486, 1491, 1496, 1498, 1500, 1511, 1513, 1514, 1517, 1522, 1526, 1528, 1533, 1535, 1536, 1543, 1545, 1547, 1550, 1554, 1555, 1556, 1557, 1561, 1562, 1563, 1564, 1565, 1566, 1568, 1570, 1571, 1572, 1573, 1575, 1577, 1582, 1583, 1585, 9717, 9721, 9723, 9726, 9736, 9739, 9741, 9742, 9753, 9756, 9757, 9759, 9765, 9766, 9767, 9768, 9769, 9773, 9774, 9778, 9779, 9782, 9783, 9785, 9787, 9790, 9795, 9801, 9804, 9806, 9809, 9811, 9813, 9816, 9818, 9821, 9828, 9829, 9836, 9841, 9859, 9866, 9867, 9873, 9875, 9887, 9894, 9895, 9900, 9905, 9912, 9915, 9924, 9932, 9933, 9934, 9950, 9958, 9963, 9973, 9977, 9978, 9979, 9982, 9983, 9994, 9999, 10004, 10006, 10007, 10009, 10015, 10018, 10026, 10043, 10046, 10052, 10055, 10059, 10062, 10064, 10069, 10070, 10072, 10073, 10074, 10078, 10083, 10088, 10090, 10095, 10097, 10100, 10102, 10103, 10106, 10112, 10118, 10120, 10130, 10131, 10132, 10146, 10149, 10163, 10165, 10176, 10178, 10185, 10187, 10194, 10195, 10206, 10219, 10236, 10238, 10240, 10247, 10250, 10267, 10278, 10279, 10280, 10284, 10286, 10296, 10303, 10305, 10316, 10320, 10342, 10345, 10346, 10349, 10371, 10372, 10376, 10389, 10397, 10398, 10404, 10405, 10407, 10409, 10416, 10418, 10423, 10434, 10437, 10446, 10451, 10454, 10456, 10458, 10459, 10469, 10471, 10476, 10478, 10479, 10488, 10490, 10494, 10502, 10504, 10506, 10513, 10518, 10521, 10530, 10532, 10535, 10544, 10545, 10559, 10562, 10566, 10573, 10575, 10581, 10584, 10585, 10592, 10596, 10601, 10608, 10609, 10610, 10616, 10627, 10629, 10632, 10635, 10636, 10642, 10646, 10649, 10673, 10674, 10685, 10686, 10688, 10689, 10698, 10701, 10705, 10717, 10718, 10719, 10726, 10733, 10734, 10736, 10743, 10744, 10749, 10751, 10755, 10758, 10762, 10763, 10765, 10769, 10780, 10783, 10785, 10794, 10796, 10799, 10805, 10806, 10815, 10816, 10821, 10828, 10833, 10839, 10842, 10846, 10847, 10852, 10855, 10859, 10870, 10887, 10890, 10893, 10898, 10899, 10904, 10920, 10921, 10923, 10927, 10931, 10933, 10934, 10943, 10944, 10948, 10956, 10963, 10970, 10973, 10977, 10978, 11003, 11012, 11017, 11027, 11028, 11031, 11034, 11047, 11050, 11066, 11072, 11079, 11081, 11083, 11087, 11088, 11089, 11105, 11114, 11117, 11119, 11120, 11121, 11124, 11128, 11130, 11137, 11138, 11140, 11141, 11149, 11153, 11169, 11174, 11175, 11176, 11179, 11185, 11193, 11197, 11202, 11203, 11204, 11207, 11209, 11217, 11218, 11231, 11236, 11237, 11238, 11243, 11246, 11250, 11252, 11267, 11287, 11302, 11311, 11320, 11324, 11325) then 1 else 0 end) as ponovo_nisu,
							count(*) as total
						FROM idk_kandidati WHERE kandidat_poslan_dipl NOT IN (0,1,11,13,3,4,5,51,52,53,6,61,62,63,7,71,72,73,8,81,82,83,9,91,92,93) GROUP BY kandidat_poslan_dipl
	");

$query_kan2->execute();
?>
<table class="table-bordered" style=" width: 40%;">
<thead>
	<th class="text-center">dosao do</th>
	<th class="text-center">pogledali link</th>
	<th class="text-center">nisu otv sms</th>
	<th class="text-center">total</th>
</thead>
<?php
while($row_kan2 = $query_kan2->fetch()){
	$total2 = $row_kan2['total'];
	$ponovo_pogl = $row_kan2['ponovo_pogl'];
	$ponovo_nisu = $row_kan2['ponovo_nisu'];
	$kandidat_poslan_dipl2 = $row_kan2['kandidat_poslan_dipl'];
	switch ($kandidat_poslan_dipl2){
		case 2: $status2 = "poslan sms, dalje nista"; break;
		case 21: $status2 = "pogledao link, nista kliknuo"; break;
		case 22: $status2 = "ima nostrifikovanu diplomu"; break;
		case 23: $status2 = "nema nostrifikovanu diplomu"; break;
	}
	?>
	<tr>
	<?php
	?>
		<td><?php echo $status2; ?></td>
		<td><?php echo $ponovo_pogl; ?></td>
		<td><?php echo $ponovo_nisu; ?></td>
		<td align="center"><?php echo $ponovo_pogl + $ponovo_nisu; ?></td>
		
		<?php
	?>
	</tr>
	<?php
}
?>

</table>
<?php

//NEKAKVO ISPRAVLJANJE
/*
$brojac=1;
$stari_pogl_link = array(48, 127, 135, 137, 165, 193, 266, 297, 317, 371, 376, 429, 430, 464, 467, 522, 533, 571, 705, 717, 727, 728, 759, 777, 794, 800, 801, 802, 810, 813, 814, 815, 823, 824, 834, 852, 870, 873, 893, 898, 905, 908, 931, 953, 965, 1008, 1010, 1016, 1032, 1068, 1084, 1141, 1198, 1202, 1209, 1389, 1399, 1404, 1410, 1432, 1492, 1510, 1521, 1546, 1567, 1581, 9716, 9725, 9728, 9749, 9750, 9770, 9799, 9807, 9820, 9826, 9827, 9839, 9898, 9908, 9930, 9954, 9968, 9993, 10066, 10082, 10087, 10108, 10133, 10144, 10177, 10198, 10216, 10252, 10264, 10282, 10289, 10311, 10369, 10381, 10422, 10429, 10440, 10449, 10460, 10475, 10522, 10529, 10560, 10565, 10569, 10583, 10593, 10607, 10625, 10662, 10679, 10713, 10714, 10715, 10716, 10731, 10739, 10750, 10752, 10777, 10797, 10801, 10832, 10843, 10877, 10881, 10919, 10932, 10949, 10951, 10979, 11029, 11041, 11057, 11065, 11080, 11156, 11159, 11168, 11200, 11232, 11245, 11251, 11268);
$stari_nisu_otv_sms = array(65, 98, 104, 105, 113, 117, 123, 136, 139, 143, 144, 154, 167, 178, 179, 190, 191, 194, 195, 204, 213, 216, 220, 234, 235, 243, 245, 248, 251, 253, 256, 258, 264, 268, 269, 279, 293, 298, 300, 302, 305, 306, 307, 308, 309, 312, 313, 320, 321, 328, 335, 336, 338, 341, 344, 351, 359, 363, 367, 368, 369, 372, 375, 390, 399, 406, 408, 411, 412, 415, 416, 424, 439, 440, 442, 445, 447, 450, 454, 456, 460, 469, 472, 477, 481, 484, 485, 486, 487, 496, 500, 501, 503, 504, 506, 518, 520, 524, 525, 526, 527, 528, 529, 530, 531, 532, 542, 543, 552, 568, 569, 573, 577, 578, 579, 581, 585, 586, 587, 588, 589, 592, 593, 595, 596, 600, 601, 603, 606, 615, 668, 671, 688, 691, 692, 694, 695, 711, 712, 713, 715, 720, 722, 723, 724, 725, 726, 736, 737, 741, 742, 743, 744, 746, 748, 751, 752, 753, 754, 756, 758, 762, 763, 764, 766, 768, 775, 776, 778, 779, 785, 787, 788, 790, 796, 797, 799, 804, 805, 806, 807, 811, 816, 818, 820, 821, 827, 830, 831, 833, 836, 844, 845, 849, 853, 857, 858, 859, 862, 864, 867, 869, 872, 874, 879, 880, 883, 885, 886, 887, 889, 891, 892, 894, 895, 897, 899, 901, 906, 909, 912, 914, 915, 919, 921, 922, 923, 929, 933, 936, 940, 942, 946, 949, 950, 951, 954, 955, 956, 958, 962, 963, 974, 978, 982, 985, 989, 991, 995, 998, 1002, 1003, 1011, 1012, 1013, 1015, 1024, 1029, 1030, 1034, 1035, 1036, 1039, 1041, 1050, 1052, 1060, 1067, 1070, 1086, 1087, 1088, 1092, 1096, 1097, 1116, 1118, 1119, 1123, 1124, 1127, 1128, 1131, 1132, 1140, 1153, 1156, 1158, 1168, 1172, 1173, 1174, 1176, 1178, 1181, 1182, 1191, 1192, 1193, 1200, 1207, 1215, 1222, 1237, 1242, 1243, 1244, 1247, 1248, 1257, 1264, 1265, 1266, 1267, 1268, 1269, 1270, 1271, 1274, 1279, 1284, 1289, 1291, 1294, 1302, 1306, 1356, 1358, 1359, 1376, 1377, 1381, 1383, 1386, 1387, 1391, 1396, 1403, 1405, 1406, 1408, 1414, 1415, 1416, 1419, 1421, 1423, 1428, 1433, 1434, 1436, 1444, 1445, 1456, 1459, 1461, 1466, 1472, 1473, 1480, 1481, 1486, 1491, 1496, 1498, 1500, 1511, 1513, 1514, 1517, 1522, 1526, 1528, 1533, 1535, 1536, 1543, 1545, 1547, 1550, 1554, 1555, 1556, 1557, 1561, 1562, 1563, 1564, 1565, 1566, 1568, 1570, 1571, 1572, 1573, 1575, 1577, 1582, 1583, 1585, 9717, 9721, 9723, 9726, 9736, 9739, 9741, 9742, 9753, 9756, 9757, 9759, 9765, 9766, 9767, 9768, 9769, 9773, 9774, 9778, 9779, 9782, 9783, 9785, 9787, 9790, 9795, 9801, 9804, 9806, 9809, 9811, 9813, 9816, 9818, 9821, 9828, 9829, 9836, 9841, 9859, 9866, 9867, 9873, 9875, 9887, 9894, 9895, 9900, 9905, 9912, 9915, 9924, 9932, 9933, 9934, 9950, 9958, 9963, 9973, 9977, 9978, 9979, 9982, 9983, 9994, 9999, 10004, 10006, 10007, 10009, 10015, 10018, 10026, 10043, 10046, 10052, 10055, 10059, 10062, 10064, 10069, 10070, 10072, 10073, 10074, 10078, 10083, 10088, 10090, 10095, 10097, 10100, 10102, 10103, 10106, 10112, 10118, 10120, 10130, 10131, 10132, 10146, 10149, 10163, 10165, 10176, 10178, 10185, 10187, 10194, 10195, 10206, 10219, 10236, 10238, 10240, 10247, 10250, 10267, 10278, 10279, 10280, 10284, 10286, 10296, 10303, 10305, 10316, 10320, 10342, 10345, 10346, 10349, 10371, 10372, 10376, 10389, 10397, 10398, 10404, 10405, 10407, 10409, 10416, 10418, 10423, 10434, 10437, 10446, 10451, 10454, 10456, 10458, 10459, 10469, 10471, 10476, 10478, 10479, 10488, 10490, 10494, 10502, 10504, 10506, 10513, 10518, 10521, 10530, 10532, 10535, 10544, 10545, 10559, 10562, 10566, 10573, 10575, 10581, 10584, 10585, 10592, 10596, 10601, 10608, 10609, 10610, 10616, 10627, 10629, 10632, 10635, 10636, 10642, 10646, 10649, 10673, 10674, 10685, 10686, 10688, 10689, 10698, 10701, 10705, 10717, 10718, 10719, 10726, 10733, 10734, 10736, 10743, 10744, 10749, 10751, 10755, 10758, 10762, 10763, 10765, 10769, 10780, 10783, 10785, 10794, 10796, 10799, 10805, 10806, 10815, 10816, 10821, 10828, 10833, 10839, 10842, 10846, 10847, 10852, 10855, 10859, 10870, 10887, 10890, 10893, 10898, 10899, 10904, 10920, 10921, 10923, 10927, 10931, 10933, 10934, 10943, 10944, 10948, 10956, 10963, 10970, 10973, 10977, 10978, 11003, 11012, 11017, 11027, 11028, 11031, 11034, 11047, 11050, 11066, 11072, 11079, 11081, 11083, 11087, 11088, 11089, 11105, 11114, 11117, 11119, 11120, 11121, 11124, 11128, 11130, 11137, 11138, 11140, 11141, 11149, 11153, 11169, 11174, 11175, 11176, 11179, 11185, 11193, 11197, 11202, 11203, 11204, 11207, 11209, 11217, 11218, 11231, 11236, 11237, 11238, 11243, 11246, 11250, 11252, 11267, 11287, 11302, 11311, 11320, 11324, 11325);

$query_kan2 = $db->prepare("
						SELECT kandidat_id, kandidat_poslan_dipl
						FROM idk_kandidati 
						WHERE kandidat_poslan_dipl NOT IN (0,1,11,13,3,4) 
	");

$query_kan2->execute();

while($row_kan2 = $query_kan2->fetch()){
	$kandidat_id2 = $row_kan2['kandidat_id'];
	$kandidat_poslan_dipl2 = $row_kan2['kandidat_poslan_dipl'];
	
	if(in_array($kandidat_id2, $stari_nisu_otv_sms)){
		echo $brojac++.". poslan_".$kandidat_poslan_dipl2."<br>";
	}
}
*/
//DODAVANJE PROJEKATA ZA BOT ISPUNJAVA / NE ISPUNJAVA USLOVE 
/*
$query = $db->prepare("
	SELECT nalog_id, kompanija_id, employee_id, nalog_broj
	FROM idk_nalozi
	WHERE nalog_status != 8 and nalog_id > 173");

$query->execute();
while($row = $query->fetch()){
	$nalog_id = $row['nalog_id'];
	$project_companyid = $row['kompanija_id'];
	$employee_id = $row['employee_id'];
	$nalog_broj = $row['nalog_broj'];
	$project_datetime = date('Y-m-d H:i:s');
	
	$project_name = $nalog_broj." - BOT - ne ispunjava uslove";
	
	$query_insert_project = $db->prepare("
					INSERT INTO idk_projects
						(project_name, project_companyid, project_employeeid, project_pmanagerid, project_datetime, project_status, project_nalogid)
					VALUES
						(:project_name, :project_companyid, :project_employeeid, :project_pmanagerid, :project_datetime, :project_status, :project_nalogid)");

	$query_insert_project->execute(array(
				':project_name' => $project_name,
				':project_companyid' => $project_companyid,
				':project_employeeid' => $employee_id,
				':project_pmanagerid' => $employee_id,
				':project_datetime' => $project_datetime,
				':project_status' => 1,
				':project_nalogid' => $nalog_id
				));

	
	
	echo $nalog_id."</br>";
}
*/
?>