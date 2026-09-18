<?php

    include("includes/functions.php");
    include("includes/head.php");
	require 'mail/PHPMailerAutoload.php';
	
	$datum = date("Y-m-d");
	$juce_poct = date('Y-m-d 00:00:00', strtotime($datum." - 1 days"));
	$juce_kraj = date('Y-m-d 23:59:59', strtotime($datum." - 1 days"));
	// $juce_poct = date('2020-12-14 00:00:00');
	// $juce_kraj = date('2020-12-17 00:00:00');
	$datum_2 = date("Y-m-d H:i:s");
	// $datum_2 = date("2020-12-17 08:00:00");
	
	$datum_subject = date("d.m.Y", strtotime($datum." - 1 days"));
	
	$datum_za_slati = strtotime($datum_2);
	
    $query = $db->prepare("
						SELECT pr_broj_predracuna, pr_file, kan.ime_nd_kandidata, kan.prezime_nd_kandidata, kan.grad_nd_kandidata, kan.ulica_nd_kandidata, kan.postanski_broj_nd_kandidata, kan.jmbg_nd_kandidata
                        FROM idk_predracuni pre
						JOIN idk_nd_kandidata kan
						ON pre.pr_kandidat_id = kan.id_broj_nd_kandidata
                        WHERE (pr_datum_kreiranja BETWEEN :juce_poct AND :juce_kraj)
                        AND pr_domaca_valuta = 'BAM'
                        AND (kan.vrsta_ugovora_nd_kandidata = 11 OR kan.vrsta_ugovora_nd_kandidata = 12)");

    $query->execute(array(
					':juce_poct' => $juce_poct,
					':juce_kraj' => $juce_kraj
	));
	
	
	$row_number = $query->rowCount();
	if($row_number > 0){	
	
		$link_export = "".getSiteURLR()."predracuni/".$datum_za_slati;
		//$link_export = "".getSiteURLR()."export_excel.php?prozor=export_mail_financije&datum=".$datum;
		$mail = new PHPMailer;
		$mail->isSMTP();

		try {
			$mail->Host = 'smtp.gmail.com';                 // Specify main and backup SMTP servers
			$mail->SMTPAuth = true;                         // Enable SMTP authentication
			$mail->Username = 'support@job-step.com';        // SMTP username
			$mail->Password = 'eooc nnxo aylp lqkh';          // SMTP password
			$mail->SMTPSecure = 'ssl';                      // Enable TLS encryption, `ssl` also accepted
			$mail->Port = 465;                              // TCP port to connect to
			$mail->CharSet = 'UTF-8';

			//Recipients
			$mail->setFrom('support@job-step.com', 'JobStep');
			$mail->addAddress('a.salkicevic@job-step.com', 'support@job-step.com');		// Add a recipient
			//$mail->addAddress('d.selmanovic@job-step.net');		// Add a recipient
			//$mail->addAddress('operateri@mikrofin.com');		// Add a recipient
			
			$mail->Subject ="Predracuni za dan ".$datum_subject;
			$mail->Body    = "Link: ".$link_export;
			$mail->AltBody = "";

			if(!$mail->send()) {
				echo 'Message could not be sent.';
				echo 'Mailer Error: ' . $mail->ErrorInfo;
				
				$razlog = $mail->ErrorInfo;
				//ADD TO LOGS START
				$log_date = date('Y-m-d H:i:s');
				$log_desc = "DIPL -> Nije poslana lista mikfrofinu. Razlog: ".$razlog;
				$log_query = $db->prepare("
								INSERT INTO idk_logs
									(log_employeeid, log_desc, log_date)
								VALUES
									(:log_employeeid, :log_desc, :log_date)");

				$log_query->execute(array(
								':log_employeeid' => 0,
								':log_desc' => $log_desc,
								':log_date' => $log_date));
				//ADD TO LOGS END
			}else{
				echo "Mail sent";
				//ADD TO LOGS START
				$log_date = date('Y-m-d H:i:s');
				$log_desc = "DIPL -> Poslana lista mikrofinu.";
				$log_query = $db->prepare("
								INSERT INTO idk_logs
									(log_employeeid, log_desc, log_date)
								VALUES
									(:log_employeeid, :log_desc, :log_date)");

				$log_query->execute(array(
								':log_employeeid' => 0,
								':log_desc' => $log_desc,
								':log_date' => $log_date));
				//ADD TO LOGS END
			}
		} catch (phpmailerException $e) {
			echo $e->errorMessage(); //Pretty error messages from PHPMailer
		}
	}

?>
