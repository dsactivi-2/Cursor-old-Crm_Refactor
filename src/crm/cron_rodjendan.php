<?php

    include("includes/functions.php");
	
	$text_viber = "Sretan rođendan i sve najbolje želi Vam Jobstep tim!";
	$text_sms = "Sretan rođendan i sve najbolje želi  Vam Jobstep tim!";
	$link = "https://job-step.net/";
	$btn_text = "Jobstep Web";
	$denis = "38761210765";
	$ado = "38763453196";
	$danas = date('m-d');
	
    $query = $db->prepare("
                        SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_datumrodjenja, kandidat_mobitel
						FROM idk_kandidati
						WHERE kandidat_status != 3 AND DATE_FORMAT(kandidat_datumrodjenja, '%m-%d') = :danas
						AND (kandidat_mobitel LIKE '%+389%' OR kandidat_mobitel LIKE '%+386%' OR kandidat_mobitel LIKE '%+382%' OR kandidat_mobitel LIKE '%+385%' OR kandidat_mobitel LIKE '%+381%' OR kandidat_mobitel LIKE '%+387%') 
						ORDER BY kandidat_datumrodjenja
						");

    $query->execute(array(':danas' => $danas));
	
	$ct = 1;
	?> 
	<table>
	<?php
	$active_provider = getActiveProviderForSendingMessages(3);
	while($row = $query->fetch()){
		
		$kandidat_id = $row['kandidat_id'];
		$kandidat_ime = $row['kandidat_ime'];
		$kandidat_prezime = $row['kandidat_prezime'];
		$kandidat_datumrodjenja = $row['kandidat_datumrodjenja'];
		$kandidat_mobitel = $row['kandidat_mobitel'];
		
		?>
		<tr>
		<td><?php echo  $ct++; ?> </td>
		<td><?php echo  $kandidat_id; ?> </td>
		<td><?php echo  $kandidat_ime; ?> </td>
		<td><?php echo  $kandidat_prezime; ?> </td>
		<td><?php echo  $kandidat_datumrodjenja."     "; ?> </td>
		<td><?php echo  $kandidat_mobitel; ?> </td>
		</tr>
		<?php
		/*
		if($kandidat_mobitel == "+38761938892"){*/
			if ($active_provider == 1) { 
				$curl = curl_init();

				curl_setopt_array($curl, array(
				CURLOPT_URL => "https://ej8w3r.api.infobip.com/omni/1/advanced",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => "{ \"scenarioKey\":\"7A32331B2103607D2F890C04FEB34942\", \"destinations\":[ { \"to\":{ \"phoneNumber\":\"".$kandidat_mobitel."\" } } ], \"sms\":{ \"text\":\"".$text_sms."\" }, \"viber\":{ \"text\":\"".$text_viber."\", \"imageURL\":\"https://crm.job-step.com/images/viber_slika_rodj.jpg\", \"buttonText\":\"".$btn_text."\", \"buttonURL\":\"".$link."\" } }",
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
				echo "<br>".$kandidat_mobitel."<br>";
			} else {
				$phoneNumber = checkPhoneNumberForNTH($kandidat_mobitel);
				$params = array(
					"channels" => array(
						"VIBER",
						"SMS"
					),
					"destinations" => array(
						array(
							"phoneNumber" => $phoneNumber
						)
					),
					"viber" => array(
						"priority" => 1,
						"sender" => "Jobstep Int",
						"buttonCaption" => $btn_text,
						"buttonAction" => $link,
						"image" => "https://crm.job-step.com/images/viber_slika_rodj.jpg",
						"text" => $text_viber,
						"ttl" => 14440,
						"label" => "promotion"
					),
					"sms" => array(
						"priority" => 2,
						"sender" => "Jobstep Int",
						"text" => $text_sms
					)
				);
				$params_encode = json_encode($params);
				$response = sendMessageViaNTH($params_encode);
				echo $response;
				echo "<br>".$kandidat_mobitel."<br>";
			}
		/*}*/
	}
	
	?>
	
	</table>
	<?php
?>
