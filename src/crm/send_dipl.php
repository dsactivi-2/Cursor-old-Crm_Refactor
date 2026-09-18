<?php

    include("includes/functions.php");
	
	$danas = date('Y-m-d H:i:s');
	$datum1 = date("Y-m-d H:i:s", strtotime("-4 days"));
	$datum2 = date("Y-m-d H:i:s", strtotime("-1 days"));
	?>
	<table>
	<th>kan_id</th>
	<th>ime</th>
	<th>prezime</th>
	<th>mail</th>
	<th>mobitel</th>
	<th>status obrade</th>
	<th>status mess</th>
	<th>kandidat_poslan_sms</th>
	<?php
	/*SELECT kg_id, kg_title, COUNT(kan.kandidat_group) FROM idk_kandidati_grupe kg JOIN idk_kandidati kan ON kg.kg_id = kan.kandidat_group 
WHERE kan.kandidat_status != 3 AND kan.kandidat_mobitel LIKE '%+381%' 
AND (kan.kandidat_status_prijave  = 1 or kan.kandidat_status_prijave = 2 or kan.kandidat_status_prijave = 3 or kan.kandidat_status_prijave = 5 or kan.kandidat_status_prijave is null) 
AND kan.kandidat_poslan_dipl IN (0,1,11,13,2,21,22,23,5,51,52,53)
AND kan.kandidat_group IN (3,6,7,11,22,23,25,26,33,34,36,37,39,40,41,44,45,46,47,52)
AND kan.povezan_na_dipl = 0 AND kan.kandidat_datumrodjenja > '1974-12-31' 
GROUP BY kg_id*/
	
	$testni_brojevi = array('+38761938892');
	
	$br = 1;
	$notifikacija_prosla = "";
	$dipl_obavijest = "";
	$notifikacije = "";
	$idevi = array();
	
	
	//klasicni nacin slanja za dipl
	$query = $db->prepare("
                        SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_group, kandidat_email, kandidat_mobitel, kandidat_status, kandidat_status_messenger, kandidat_datumrodjenja, kandidat_poslan_sms, kandidat_status_prijave, kandidat_poslan_dipl, kandidat_porijeklo
                        FROM idk_kandidati 
                        WHERE kandidat_status != 3 	
							AND kandidat_id BETWEEN 18744 AND 24684
							AND (kandidat_status_prijave  = 1 or kandidat_status_prijave = 2 or kandidat_status_prijave = 3 or kandidat_status_prijave = 5 or kandidat_status_prijave is null) 
							AND povezan_na_dipl = 0
							AND kandidat_group NOT IN (2,23,48,26)
							AND kandidat_mobitel LIKE '%+387%'
							AND kandidat_datumrodjenja > '1974-12-31' order by kandidat_id
							"); 
                        
						
						//svi normalni uslovi
						/*
							AND kandidat_id BETWEEN 17728 AND 25600
						AND (kandidat_status_prijave  = 1 or kandidat_status_prijave = 2 or kandidat_status_prijave = 3 or kandidat_status_prijave = 5 or kandidat_status_prijave is null) 
							AND povezan_na_dipl = 0
							AND kandidat_poslan_dipl IN (0,1,11,13,2,21,22,23,5,51,52,53)
							AND kandidat_group IN (3,6,7,11,22,23,25,26,33,34,36,37,39,40,41,44,45,46,47,52)
							AND kandidat_mobitel LIKE '%+381%'
							AND kandidat_datumrodjenja > '1974-12-31' order by kandidat_id
						*/
						//grupe za BiH
						//  AND kandidat_group IN (3,4,5,6,7,11,14,15,16,22,23,25,26,33,34,36,37,40,44,45,46,47,52)
						
						//grupe za SRB
						// AND kandidat_group IN (3,6,7,11,22,23,25,26,33,34,36,37,39,40,41,44,45,46,47,52)
						
						//ZA ONE VEC NA DIPLU
							// AND nd.status_nd_kandidata = 1
							// AND nd.pstatus_nd_kandidata IN (2,3,4,6)
							// kan
							// JOIN idk_nd_kandidata nd ON kan.kandidat_id = nd.kandidat_idd
						
						//STARI: 
							// AND kandidat_id BETWEEN 7090 AND 7353	
							// AND (kandidat_status_prijave  = 4 or kandidat_status_prijave = 2 or kandidat_status_prijave = 3 or kandidat_status_prijave = 5 or kandidat_status_prijave is null) 
							// AND povezan_na_dipl != 1 
							// AND kandidat_poslan_dipl = 0
							// AND kandidat_group IN (48,47,46,45,44,40,39,37,36,34,33,31,26,25,23,22,20,15,14,13,12,11,10,6,5,4,3,7)
							// AND kandidat_datumrodjenja > '1974-12-31' order by kandidat_id
						
						// stari dobri: WHERE kandidat_status != 3 AND kandidat_poslan_dipl = 0 AND povezan_na_dipl != 1"); //AND kandidat_id BETWEEN 1 AND 1000 -  AND kandidat_id = 11268
						// stavljati k_status = X
						// AND kandidat_id BETWEEN 1850 AND 2126
						// AND kandidat_id BETWEEN 2129 AND 2466
						// AND kandidat_id BETWEEN 2468 AND 2813
						
						// AND kandidat_id BETWEEN 8722 AND 9036
						// AND kandidat_id BETWEEN 9039 AND 9279
						// AND kandidat_id BETWEEN 9281 AND 9471
						// AND kandidat_id BETWEEN 9473 AND 9712

    
    $query->execute();
	
	
	
		//$row=$get_kan_info->fetch();
	while($row=$query->fetch()){
		$kandidat_id = $row['kandidat_id'];
		$kandidat_ime = $row['kandidat_ime'];
		$kandidat_prezime = $row['kandidat_prezime'];
		$kandidat_email = $row['kandidat_email'];
		$kandidat_mobitel = $row['kandidat_mobitel'];
		$kandidat_status = $row['kandidat_status'];
		$kandidat_status_messenger = $row['kandidat_status_messenger'];
		$kandidat_datumrodjenja = $row['kandidat_datumrodjenja'];
		$kandidat_poslan_sms = $row['kandidat_poslan_sms'];
		$kandidat_status_prijave = $row['kandidat_status_prijave'];
		$kandidat_group = $row['kandidat_group'];
		$kandidat_poslan_dipl = $row['kandidat_poslan_dipl'];
		$kandidat_porijeklo = $row['kandidat_porijeklo'];
		//$pstatus_nd_kandidata = $row['pstatus_nd_kandidata'];
		$type = "nema";
		$kandidat_full_name = $kandidat_ime." ".$kandidat_prezime;
		//var_dump($kandidat_poslan_sms);
		if($kandidat_poslan_sms === null){
			$kandidat_poslan_sms = "poslan_null";
		}
		switch($kandidat_status){
			case 0: $status_obrade = "na provjeri"; break;
			case 1: $status_obrade = "u obradi"; break;
			case 2: $status_obrade = "obraden"; break;
			case 4: $status_obrade = "kontrola"; break;
			case 5: $status_obrade = "dopuna"; break;
			case 6: $status_obrade = "odbio_mes_obrada"; break;
			case 7: $status_obrade = "obrada>3"; break;
			case 8: $status_obrade = "dopuna>3"; break;
		}
		switch($kandidat_status_messenger){
			case 0: $status_messenger = "stari kandidati"; break;
			case 1: $status_messenger = "na cekanju"; break;
			case 2: $status_messenger = "logovan"; break;
			case 3: $status_messenger = "odbio messenger"; break;
			case 4: $status_messenger = "cetvorka"; break;
			case 5: $status_messenger = "nije zavrsio obradu3"; break;
			case 6: $status_messenger = "nije zavrsio dopunu3"; break;
		}
		
		//USLOV PRVI: KANDIDAT IMA INSTALIRAN MESSENGER
		//if($kandidat_status_messenger == 5 or $kandidat_status_messenger == 6){
			//USLOV PRVI.2 : SLATI SAMO OBRADJENIMA (ONI U KONTROLI, DOPUNI I OBRADI CE DOBIJATI KAD PREDJU U OBRADJENE), stavljeno u prvi query
			/*
			$query_user = $db->prepare("
								SELECT id, token, type, dipl_obavijest, notifikacije
								FROM users
								WHERE kandidat_id = $kandidat_id AND notifikacije = 3 AND (dipl_obavijest = 3 or dipl_obavijest = 5)");

			$query_user->execute();
			if($query_user->rowCount() > 0){
				$row_user = $query_user->fetch();
				$na_botu = "jes_na_botu";
				$user_id = $row_user['id'];
				$token = base64_decode($row_user['token']);
				$type = $row_user['type'];
				$dipl_obavijest = $row_user['dipl_obavijest'];
				$notifikacije = $row_user['notifikacije'];
			}else{
				$na_botu = "nije_na_botu";
				$user_id = "nema_user_id";
				$token = "nema_token";
				$type = "nema_type";
			}
			//USLOV DRUGI : SAMO ONI SA ANDROID APPOM
			if($type == "android"){
				/*$result = send_bot_notification_android($token, "Termin u Njemačkoj ambasadi za 10 dana!");
				$sms_data_object = json_decode($result, true);
				$not_dipl_prosla = $sms_data_object['success'];
				
				//USLOV TRECI: DA LI JE PROSLA NOTIFIKACIJA
				if($not_dipl_prosla == 1){
					
					//UPDATE dipl_obavijesti u user tabeli da je poslana notf -> 3
					$upd_poslan_dipl = 11;
					$upd_dipl_obavijest = 3;
					$notifikacija_prosla = "jes_prosla";
					?>
					
					<?php
					
					//UPDATE idk_kandidati za novu kolono poslan_dipl = 1 (notif), 2(sms)
				}else{
					//SMS3 za one kojima nije aplikacija prosla
					
					//Jedan od ova 2 updejta da notf nije poslana je suvisan, za sad idu oba radi svih mogucih deninih zelja da mi bude lakse odraditi
					$upd_poslan_dipl = 13;
					$upd_dipl_obavijest = 4;
					$notifikacija_prosla = "nije_prosla";
				}
				//UPDATE idk_kandidati za novu kolonu kandidat_poslan_dipl = 1 (notif), 2(sms), 3 notf nije prosla
				$update_query = $db->prepare("
							UPDATE idk_kandidati
							SET kandidat_poslan_dipl = :kandidat_poslan_dipl
							WHERE kandidat_id = :kandidat_id
				");
				
				$update_query->execute(array(
							'kandidat_id' => $kandidat_id,
							'kandidat_poslan_dipl' => $upd_poslan_dipl
							));
				//UPDATE dipl_obavijesti u user tabeli da je poslana notf -> 3 ili da nije prosla ->4
				$users_prosla = $db->prepare("
							UPDATE users
							SET dipl_obavijest = :dipl_obavijest, notifikacije = :notifikacije
							WHERE id = :id
				");
				
				$users_prosla->execute(array(
							'id' => $user_id,
							'dipl_obavijest' => $upd_dipl_obavijest,
							'notifikacije' => 6
							));
				
				?>
				<tr>
					<td><?php echo $br++;?></td>
					<td><?php echo $kandidat_id;?></td>
					<td><?php echo $kandidat_ime;?></td>
					<td><?php echo $kandidat_prezime;?></td>
					<td><?php echo "dipl_o_".$dipl_obavijest;?></td>
					<td><?php echo $notifikacije;?></td>
					<td><?php echo $kandidat_datumrodjenja;?></td>
					<td><?php echo $status_messenger;?></td>
					<td><?php echo $type;?></td>
					<td><?php echo $notifikacija_prosla;?></td>
				</tr>
				<?php
			}else{
				//za one sa ios-om slati SMS2
				/*
				$update_query = $db->prepare("
							UPDATE idk_kandidati
							SET kandidat_poslan_dipl = :kandidat_poslan_dipl
							WHERE kandidat_id = :kandidat_id
				");
				
				$update_query->execute(array(
							'kandidat_id' => $kandidat_id,
							'kandidat_poslan_dipl' => 4
							));
			}
			*/
			//bio else za kand_status, nece nista ici sad, sve dok ne predju u obradjene -> fju tamo napraviti za slanje dipl notifikacije
			
		//}else if($kandidat_status_messenger == 0 or $kandidat_status_messenger == 3){
			
			$result = substr($kandidat_mobitel, 0, 4);
			
			//if($kandidat_mobitel != "" and $result == '+387'){
			//SMS1 ide svim ostalim kandidatima
			
				//fja za slanje SMS-a
				//$link = "/dipl_info/".$kandidat_id;
				//link za drugi tip:
				//$link = "/info/".$kandidat_id;
				
				//link za resend
				// $link = "/rinfo/".$kandidat_id;
				
				//link za VIBER normal slanje
				$link = "nostrifikacija_info_bs/".$kandidat_id;
				//link za Srbiju
				$linksrb = "nostrifikacija_info_rs/".$kandidat_id;
				
				/*$kandidat_full_name = str_replace("đ","dj", $kandidat_full_name);
				$kandidat_full_name = str_replace("Đ","DJ", $kandidat_full_name);
				$kandidat_full_name = str_replace("č","c", $kandidat_full_name);
				$kandidat_full_name = str_replace("Č","C", $kandidat_full_name);
				$kandidat_full_name = str_replace("ć","c", $kandidat_full_name);
				$kandidat_full_name = str_replace("Ć","C", $kandidat_full_name);
				$kandidat_full_name = str_replace("š","s", $kandidat_full_name);
				$kandidat_full_name = str_replace("Š","S", $kandidat_full_name);
				$kandidat_full_name = str_replace("ž","z", $kandidat_full_name);
				$kandidat_full_name = str_replace("Ž","Z", $kandidat_full_name);*/
				
				//sendSMSdiplINFOBIP($kandidat_id, $kandidat_mobitel, $link, $kandidat_full_name);
				
				//VIBERRRRRRRRR
				//sendVIBERdiplINFOBIP($kandidat_id, $kandidat_mobitel, $link, $kandidat_full_name);
				//za srbiju
				//sendVIBERdiplINFOBIPsrbija($kandidat_id, $kandidat_mobitel, $linksrb, $kandidat_full_name);
				
				//array_push($idevi, $kandidat_id);
				?>
				<tr>
					<td><?php echo $br++;?></td>
					<td><?php echo $kandidat_id;?></td>
					<td><?php echo $kandidat_ime;?></td>
					<td><?php echo $kandidat_prezime;?></td>
					<td><?php echo $kandidat_datumrodjenja;?></td>
					<td><?php echo $kandidat_mobitel;?></td>
					<td><?php echo $status_obrade;?></td>
					<td><?php echo "Poslan_dipl-".$kandidat_porijeklo."--";?></td>
					<td><?php echo "Grupa".$kandidat_group."..";?></td>
				</tr>
				<?php
			//}
		//}
	}
    ?>
	</table>
	<?php
	/*
	foreach($idevi as $ide) {
		echo $ide.", ";
	}*/
	
	/**************
	 , , 4221, 4238, 4256, 4270, 4279, 4305, 4324, 4351, 4379, 4406, 4413, 4420, 4437, 4445, 4479, 4490, 4501, 4504, 4513, 4568, 4591, 4654, 4704, 4707, 4711, 4800, 4853, 4867, 4898, 4925, 4928, 4952, 5005, 5016, 5094, 5105, 5126, 5173, 5179, 5217, 5218, 5250, 5299, 5300, 5322, 5327, 5333, 5375, 5378, 5387, 5389, 5400, 5407, 5412, 5424, 5441, 5455, 5582, 5583, 5590, 5610, 5672, 5687, 5690, 5707, 5731, 5735, 5742, 5811, 5840, 5847, 5860, 5893, 5943, 5948, 5968, 5985, 5992, 5996, 6024, 6029, 6091, 6102, 6121, 6138, 6140, 6155, 6177, 6196, 6205, 6234, 6236, 6238, 6258, 6279, 6285, 6299, 6312, 6316, 6320, 6385, 6402, 6406, 6440, 6482, 6492, 6503, 6523, 6554, 6581, 6582, 6594, 6600, 6611, 6614, 6634, 6658, 6678, 6687, 6723, 6751, 6776, 6787, 6824, 6828, 6859, 6864, 6907, 6931, 6943, 6966, 7027, 7030, 7054, 7106, 7114, 7117, 7125, 7138, 7152, 7172, 7175, 7205, 7231, 7234, 7260, 7265, 7290, 7335
	**************/

?>
