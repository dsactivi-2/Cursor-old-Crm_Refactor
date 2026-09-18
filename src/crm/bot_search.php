<?php 
include("includes/functions.php");
include("includes/common.php");
Global $db;

$idevi = $_POST['idevi'];
$nalog_id = $_POST['nalog_id'];

var_dump($nalog_id);
var_dump($idevi);

foreach($idevi as $id){
	
	$query_check = $db->prepare("SELECT bo_poslana FROM idk_bot_obavijesti WHERE bo_kandidat_id = :bo_kandidat_id AND bo_nalog_id = :bo_nalog_id");
	
	$query_check->execute(array(':bo_kandidat_id' => $id,
								':bo_nalog_id' => $nalog_id));
	if($query_check->rowCount() == 0){
	
		$query_insert = $db->prepare("INSERT INTO idk_bot_obavijesti
											(bo_kandidat_id, bo_nalog_id, bo_poslana)
										VALUES
											(:bo_kandidat_id, :bo_nalog_id, :bo_poslana)");
		
		$query_insert->execute(array(
						':bo_kandidat_id' => $id,
						':bo_nalog_id' => $nalog_id,
						':bo_poslana' => 1));
						
		//GET TOKEN USERA
			$query_token = $db->prepare("
							SELECT token, type FROM users
							WHERE kandidat_id = :id
			");
			
			$query_token->execute(array(
							'id' => $id
			));
			
			$rowToken = $query_token->fetch();
			$token = base64_decode($rowToken['token']);
			//$token = base64_decode('ZDd6QUNJRTFlVzQ6QVBBOTFiRjhhTUJKdC1UZlJOeXpHd3RhZU5OMjY5Y2dWblduNkRwLUZEM1Iyb1pIUEd0ZjVCbXF5UE90Q2pBV1NMUWdxS0pfYkgyZWtVNGZnZnJjZS1nYUFnaHZGaFFpd3lRMmNPWXpSaHViYXlVMEktS2ZxQnFwa1V5MG9aaGhQT2lrb19FVmV5ZkM=');
			$type = $rowToken['type'];
			if($type == 'android'){
				echo  send_bot_notification_android($token, "Test");
			}
			else if($type == 'ios'){
                //send_bot_notification_ios($token, "TestI");
            }
		
	}
}
