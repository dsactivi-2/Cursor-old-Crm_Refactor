<?php 

	include("includes/connect.php");
	$novi_racun_naziv = getenv("vrsta_remindera");
	// $novi_racun_naziv = "fgdf";
	$query = $db->prepare("INSERT INTO idk_email_text 
						(email_value,  email_title, email_txt)	
						VALUES	
						(:email_value,:email_title,:email_txt)	
						");	
	$query->execute(array(	
				':email_value' => $novi_racun_naziv,
				':email_title' => "dfgdf",
				':email_txt' => "dfsdfsdf"
				));
	echo $novi_racun_naziv." done\n";
?>