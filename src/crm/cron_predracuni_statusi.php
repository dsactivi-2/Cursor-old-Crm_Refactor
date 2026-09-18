<?php
include("includes/functions.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$today = date("Y-m-d H:i:s");

$query = $db->prepare("
		SELECT *
		FROM idk_predracuni 
		WHERE pr_status != 2 AND pr_stornirano = 0");

$query->execute();
$brojac = 0;
while($row = $query->fetch()){ 
	$pr_id = $row['pr_id'];
	$pr_rata = $row['pr_rata'];
	$pr_kandidat_id = $row['pr_kandidat_id'];
	$pr_zaposlenik = $row['pr_zaposlenik'];
	$pr_status = $row['pr_status']; 
	$pr_datum_kreiranja = $row['pr_datum_kreiranja'];
	$in_caso1_date = date("Y-m-d H:i:s", strtotime($pr_datum_kreiranja."+21 days"));
	$in_caso2_date = date("Y-m-d H:i:s", strtotime($pr_datum_kreiranja."+40 days"));
	$in_caso3_date = date("Y-m-d H:i:s", strtotime($pr_datum_kreiranja."+90 days")); 
	
	//USLOV ZA IZBJEGAVANJE DA SE PREDRACUN ZA PRVU RATU PO NAPLATI CH PREBACI NA INKASO --START
	$pr_naplata_preko = $row["pr_naplata_preko"];

	if($pr_naplata_preko == 1 AND $pr_rata == 1){
		$queryUgovor = $db->prepare("
			SELECT 
				ug_status
			FROM 
				idk_nd_ugovori
			WHERE 
				ug_kandidat_id = :ug_kandidat_id
				AND 
				ug_id = (
					SELECT 
						MAX(ug_id)
					FROM 
						idk_nd_ugovori
					WHERE 
						ug_kandidat_id = :ug_kandidat_id
				)
		");
		$queryUgovor->execute(array(
			':ug_kandidat_id' => $pr_kandidat_id
		));
		$rowUgovor = $queryUgovor->fetch();
		$ug_status = $rowUgovor['ug_status'];
		if($ug_status != 2){
			continue;
		}
	}
	//USLOV ZA IZBJEGAVANJE DA SE PREDRACUN ZA PRVU RATU PO NAPLATI CH PREBACI NA INKASO --START
	
	// //Slanje maila starom menadzeru sa linkom kandidata njemu dodjeljenog
	// $zaposlenik_query = $db->prepare("
							// SELECT zaduzen_zaposlenik_nd_kandidata
							// FROM idk_nd_kandidata
							// WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata");

	// $zaposlenik_query->execute(array(
					// ':id_broj_nd_kandidata' => $pr_kandidat_id));

	// $zaposlenik = $zaposlenik_query->fetch();
	
	// $zaduzen_zaposlenik_nd_kandidata = $zaposlenik["zaduzen_zaposlenik_nd_kandidata"];
	
	// //Slanje maila starom menadzeru sa linkom kandidata njemu dodjeljenog
	// $user_query = $db->prepare("
							// SELECT employee_firstname, employee_lastname, employee_email
							// FROM idk_employees
							// WHERE employee_id = :employee_id");

	// $user_query->execute(array(
					// ':employee_id' => $zaduzen_zaposlenik_nd_kandidata));

	// $user = $user_query->fetch();

	// $employee_firstname = $user['employee_firstname'];
	// $employee_lastname = $user['employee_lastname']; 
	// $employee_email = $user['employee_email'];
	
	// echo '---';
	// echo $employee_email;

	// //Send email to user
	// $mail_email = $employee_email;
	// $mail_name = $employee_firstname . ' ' . $employee_lastname;
	// $mail_subject = "Dipl modul - Prošao period za uplatu";
	// $mail_url = "https://jobstep-app.com/nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$pr_kandidat_id." ";
	// $mail_body = "
					// <p>Vašem kandidatu ".$mail_url." je prošao period za uplatu. Molimo kontaktirajte ga.</p>
	// ";
	// $mail_altbody = "
					// <p>Vašem kandidatu je prošao period za uplatu. Molimo kontaktirajte ga.</p> 
	// ";
	
	
	//$slanje = 0; 
	if($in_caso1_date <= $today AND $pr_status == 1){
		// IDE IN CASO 1 UPDATE	
		$query1 = $db->prepare("
					UPDATE idk_predracuni
					SET	pr_status = :pr_status
					WHERE pr_id = :pr_id");

		$query1->execute(array(
				':pr_id' => $pr_id,
				':pr_status' => 3));
				echo '---';
				echo 'IN CASO 1';
				
		//$slanje = 1;
	}
	
	if($in_caso2_date <= $today AND $pr_status == 3){
		// IDE IN CASO 2 UPDATE
		$query2 = $db->prepare("
					UPDATE idk_predracuni
					SET	pr_status = :pr_status
					WHERE pr_id = :pr_id");

		$query2->execute(array(
				':pr_id' => $pr_id,
				':pr_status' => 4));
				echo '---';
				echo 'IN CASO 2';
				
		//$slanje = 1;
	}
	
	if($in_caso3_date <= $today AND $pr_status == 4){
		// IDE IN CASO 3 UPDATE
		$query3 = $db->prepare("
					UPDATE idk_predracuni 
					SET	pr_status = :pr_status
					WHERE pr_id = :pr_id");

		$query3->execute(array(
				':pr_id' => $pr_id,
				':pr_status' => 5));
		
		//$slanje = 1;
	}
	
	
	// if($slanje == 1){
		// $mail = new PHPMailer;
		// $mail->isSMTP();											// Set mailer to use SMTP

		// $mail->Host = 'smtp.strato.de;smtp.strato.de';				// Specify main and backup SMTP servers
		// $mail->SMTPAuth = true;										// Enable SMTP authentication
		// $mail->Username = 'no-reply@wwtravel.net';					// SMTP username
		// $mail->Password = 'fdsaSD43fds';							// SMTP password
		// $mail->SMTPSecure = 'ssl';									// Enable TLS encryption, `ssl` also accepted
		// $mail->Port = 465;											// TCP port to connect to
		// $mail->CharSet = 'UTF-8';
		 

		// $mail->setFrom('info@job-step.net');     		// Add a recipient
		// $mail->addAddress($mail_email);		// Add a recipient	// Add a recipient

		// $mail->Subject = $mail_subject;
		// $mail->Body    = $mail_body; 
		// $mail->AltBody = $mail_body; 

		// if(!$mail->send()) {
			// echo 'Message could not be sent.';
			// echo 'Mailer Error: ' . $mail->ErrorInfo;
		// }else{ 
			// echo "Poslato";
		// }
	// }
	
}

	
?>