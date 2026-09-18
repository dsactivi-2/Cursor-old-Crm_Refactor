<?php

    include("includes/functions.php");
	require 'mail/PHPMailerAutoload.php';
	
	/*$danas = date('2020-03-04');
	$danas = strtotime($danas);
	$danas = strtotime("+5 days", $danas);
	echo $datum = date("Y-m-d", $danas);*/
	$danas = date('Y-m-d');
	echo $datum = date("Y-m-d", strtotime("+5 days"));
	
    $query = $db->prepare("
                        SELECT *
                        FROM idk_kandidat_financije
                        WHERE kf_datum_stvarni = :kf_datum AND kf_status != 2");

    $query->execute(array(
					':kf_datum' => $datum
	));
	$row_number = $query->rowCount();
	if($row_number > 0){
		
		$link_export = "".getSiteURLR()."export_excel.php?prozor=export_mail_financije&datum=".$datum;
		$mail = new PHPMailer;

		try {
			
			$mail->setFrom('no-reply@wwtravel.net', 'World Wide Travel');     		// Add a recipient
			$mail->addAddress('e.bender@wwtravel.net');		// Add a recipient
			$mail->addAddress('s.sertovic@job-step.net');		// Add a recipient
			$mail->addAddress('dz.tadzic@job-step.net');		// Add a recipient
			$mail->addAddress('dz.komic@job-step.com');		// Add a recipient
			
			$mail->Subject ="Financije";
			$mail->Body    = "Link: ".$link_export;
			$mail->AltBody = "";

			if(!$mail->send()) {
				echo 'Message could not be sent.';
				echo 'Mailer Error: ' . $mail->ErrorInfo;
			}else{
				echo "Mail sent";
			}
		} catch (phpmailerException $e) {
			echo $e->errorMessage(); //Pretty error messages from PHPMailer
		}
	}

?>
