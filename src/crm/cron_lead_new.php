<?php

    include("includes/functions.php");
    include("includes/head.php");
	
	$datum_kraj = date("Y-m-d H:i:s");
	$datum_poct = date('Y-m-d H:i:s', strtotime($datum." - 2 hours"));
	//echo $datum_poct."<br>".$datum_kraj."<br>";
	
	$brojevi = array('38761938892', '38763453196', '38761210765', '38762926573');
	//$brojevi = array("38761938892");
	//$broj = "38763453196"; //ado-poslovni
	//$broj = "387603497316"; //ado-privatni
	$text_sms = "U posljednja dva sata nije bilo novih leadova.";
	$text_viber = "U posljednja dva sata nije bilo novih leadova!!!";
	
    $query = $db->prepare("
						SELECT id_broj_nd_kandidata FROM idk_nd_kandidata WHERE vrijeme_kreiranja_nd_kandidata BETWEEN :datum_poct AND :datum_kraj
						AND kampanja_id IS NOT NULL");

    $query->execute(array(
					':datum_poct' => $datum_poct,
					':datum_kraj' => $datum_kraj
	));
	
	
	$row_number = $query->rowCount();
	if($row_number == 0){
		foreach($brojevi as $broj){
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
	}else{
		
	}
?>
