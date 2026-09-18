<?php
	include("includes/functions.php");
	
	exit();
	//Cron job Dipl
	
	// 1. Prvi dio je urađen u svrhu obavjesti menadzera koliko ima novih kandidata za koje je zadužen u protekla 24h
	// START
	
		$vr_trenutno = date("Y-m-d H:i:s");
		$vr_nazad_24h = date("Y-m-d H:i:s", strtotime("-24 hours"));
		
		//echo "Trenutno vrijeme: ".$vr_trenutno."<br> Vrijeme nazad 24 h: ".$vr_nazad_24h." ";
		
		$user_query1 = $db->prepare("
								SELECT employee_id, employee_firstname, employee_lastname, employee_email
								FROM idk_employees
								WHERE employee_nostrifikacija_diploma = :employee_nostrifikacija_diploma
								ORDER BY employee_id ASC
								");

		$user_query1->execute(array(
								':employee_nostrifikacija_diploma' => 1
								));

		while($user1 = $user_query1->fetch()){
			$employee_id1 = $user1['employee_id'];
			$employee_firstname1 = $user1['employee_firstname'];
			$employee_lastname1 = $user1['employee_lastname'];
			$employee_email1 = $user1['employee_email'];
			
			$query1 = $db->prepare("
									SELECT id_broj_nd_kandidata, ime_nd_kandidata, prezime_nd_kandidata
									FROM idk_nd_kandidata
									WHERE (vrijeme_kreiranja_nd_kandidata BETWEEN :vr_nazad_24h AND :vr_trenutno) AND zaduzen_zaposlenik_nd_kandidata = :zaduzen_zaposlenik_nd_kandidata AND status_nd_kandidata = :status_nd_kandidata AND pstatus_nd_kandidata = :pstatus_nd_kandidata
									ORDER BY vrijeme_kreiranja_nd_kandidata ASC
			");
			$query1->execute(array(
									':vr_trenutno' => $vr_trenutno,
									':vr_nazad_24h' => $vr_nazad_24h,
									':zaduzen_zaposlenik_nd_kandidata' => $employee_id1,
									':status_nd_kandidata' => 1,
									':pstatus_nd_kandidata' => 1
			));
			$txt_mail1 = "";
			$kan_red1 = "";
			$txt_mail1x = "";
			$broj1 = $query1->rowCount();
			if($broj1 != 0){
				while($row1 = $query1->fetch()){
					$id1 = $row1['id_broj_nd_kandidata'];
					$ime1 = $row1['ime_nd_kandidata'];
					$prezime1 = $row1['prezime_nd_kandidata'];
					$kan_red1 = ' <tr><td>'.$ime1.' '.$prezime1.'</td><td> '. getSiteUrlr() .'nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$id1.' </td></tr>';
					$txt_mail1 = $txt_mail1." ".$kan_red1." ";
				}
				$txt_mail1x='
								<html>
									<head>
										<style>
										table, th, td {
										  border: 1px solid black;
										  border-collapse: collapse;
										}
										th, td {
										  padding: 5px;
										  text-align: center;
										}
										caption{
											padding-bottom: 40px;
											font-weight: bold;
											font-size: x-large;
										}
										</style>
									</head>
									<body>
										<table style="width:100%">
											<caption>Spisak novih kandidata za koje ste zaduženi u protekla 24 sata</caption>
											<tr>
												<th>Ime i Prezime</th>
												<th>Link</th>
											</tr>
											'.$txt_mail1.'
											<tr>
												<td style = "text-align: right;" colspan = "2">Kandidati u periodu od '.date("d.m.Y H:i:s", strtotime($vr_nazad_24h)).' do '.date("d.m.Y H:i:s", strtotime($vr_trenutno)).'</td>
											</tr>
											<tr>
												<td style = "text-align: right;" colspan = "2">Broj kandidata: '.$broj1.'</td>
											</tr>
										</table>
									</body>
								</html>
							';
				//echo $txt_mail1x;
				//Send email to user
				$mail_email = $employee_email1;
				$mail_name = $employee_firstname1." ".$employee_lastname1;
				$mail_subject = "Dipl modul - Novi kandidati";
				$mail_body = $txt_mail1x;
				$mail_altbody = $txt_mail1x;
				
				sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);
			}
		}
		
	// END
	
	// 2. Drugi dio je urađen u svrhu obavijesti menadzera o kandidatima koji su oznaceni kao nezainteresirani, te ih je kao takve nakon odredenim perioda potrebno ponovo zvati sto je definisano pod odredjenim razlozima 
	// START
		
		$user_query2 = $db->prepare("
								SELECT employee_id, employee_firstname, employee_lastname, employee_email
								FROM idk_employees
								WHERE employee_nostrifikacija_diploma IN (0,1)
								ORDER BY employee_id ASC
								");

		$user_query2->execute();

		while($user2 = $user_query2->fetch()){
			$employee_id2 = $user2['employee_id'];
			$employee_firstname2 = $user2['employee_firstname'];
			$employee_lastname2 = $user2['employee_lastname'];
			$employee_email2 = $user2['employee_email'];
			
			$query2 = $db->prepare("
									SELECT id_broj_nd_kandidata, ime_nd_kandidata, prezime_nd_kandidata
									FROM idk_nd_kandidata
									WHERE zaduzen_zaposlenik_nd_kandidata = :zaduzen_zaposlenik_nd_kandidata AND status_nd_kandidata = :status_nd_kandidata AND pstatus_nd_kandidata = :pstatus_nd_kandidata
			");
			$query2->execute(array(
									':zaduzen_zaposlenik_nd_kandidata' => $employee_id2,
									':status_nd_kandidata' => 1,
									':pstatus_nd_kandidata' => 4
			));
			$txt_mail2 = "";
			$kan_red2 = "";
			$txt_mail2x = "";
			$broj2 = $query2->rowCount();
			$broj2x = 0;
			if($broj2 != 0){
				while($row2 = $query2->fetch()){
					$id2 = $row2['id_broj_nd_kandidata'];
					$ime2 = $row2['ime_nd_kandidata'];
					$prezime2 = $row2['prezime_nd_kandidata'];
					
					$query21 = $db->prepare("
											SELECT razlog_biljeska_nd, vrijeme_dodavanja_biljeska_nd
											FROM idk_nd_kandidata_biljeske
											WHERE id_biljeska_nd = (SELECT 
																	MAX(id_biljeska_nd) 
																	FROM idk_nd_kandidata_biljeske 
																	WHERE id_kandidata_biljeska_nd = :id_kandidata_biljeska_nd AND status_biljeska_nd = :status_biljeska_nd AND tip_biljeska_nd = :tip_biljeska_nd AND razlog_biljeska_nd is not null) 
					");
					$query21->execute(array(
											':id_kandidata_biljeska_nd' => $id2,
											':status_biljeska_nd' => 2,
											':tip_biljeska_nd' => 3
					));
					$row21 = $query21->fetch();
					$razlog_id = $row21['razlog_biljeska_nd'];
					$vrijeme_razlog = $row21['vrijeme_dodavanja_biljeska_nd'];
					
					$diff_novi = strtotime($vr_trenutno) - strtotime($vrijeme_razlog);
					$day_novi = intval(floor($diff_novi/86400));
					
					$query211 = $db->prepare("
											SELECT br_dana_ro
											FROM idk_ro_usluge
											WHERE id_ro = :id_ro
					");
					$query211->execute(array(
											':id_ro' => $razlog_id
					));
					$row211 = $query211->fetch();
					$br_dana = intval($row211['br_dana_ro']);
					
					if($day_novi > $br_dana){
						$broj2x++;
						$kan_red2 = ' <tr><td>'.$ime2.' '.$prezime2.'</td><td> '. getSiteUrlr() .'nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$id2.' </td></tr>';
						$txt_mail2 = $txt_mail2." ".$kan_red2." ";
					}
				}
				if($broj2x != 0){
					$txt_mail2x='
									<html>
										<head>
											<style>
											table, th, td {
											  border: 1px solid black;
											  border-collapse: collapse;
											}
											th, td {
											  padding: 5px;
											  text-align: center;
											}
											caption{
												padding-bottom: 40px;
												font-weight: bold;
												font-size: x-large;
											}
											</style>
										</head>
										<body>
											<table style="width:100%">
												<caption>Spisak nezainteresiranih kandidata koje je potrebno ponovno prozvati</caption>
												<tr>
													<th>Ime i Prezime</th>
													<th>Link</th>
												</tr>
												'.$txt_mail2.'
												<tr>
													<td style = "text-align: right;" colspan = "2">Broj kandidata: '.$broj2x.'</td>
												</tr>
											</table>
										</body>
									</html>
								';
					//echo $txt_mail2x;
					//Send email to user
					$mail_email2 = $employee_email2;
					$mail_name2 = $employee_firstname2." ".$employee_lastname2;
					$mail_subject2 = "Dipl modul - Nezainteresirani kandidati";
					$mail_body2 = $txt_mail2x;
					$mail_altbody2 = $txt_mail2x;
					
					sendEmail($mail_email2, $mail_name2, $mail_subject2, $mail_body2, $mail_altbody2);
				}
			}
		}
		
	//END
	// 3. Trecci dio je urađen u svrhu obavijesti menadzera o kandidatima koji su oznaceni kao neuspješnima nakon tri poziva, te ih je kao takve potrebno ponovo zvati nakon 15 dana
	// START
		$user_query3 = $db->prepare("
								SELECT employee_id, employee_firstname, employee_lastname, employee_email
								FROM idk_employees
								WHERE employee_nostrifikacija_diploma = :employee_nostrifikacija_diploma
								ORDER BY employee_id ASC
								");

		$user_query3->execute(array(
								':employee_nostrifikacija_diploma' => 1
								));

		while($user3 = $user_query3->fetch()){
			$employee_id3 = $user3['employee_id'];
			$employee_firstname3 = $user3['employee_firstname'];
			$employee_lastname3 = $user3['employee_lastname'];
			$employee_email3 = $user3['employee_email'];
			
			$query3 = $db->prepare("
									SELECT id_broj_nd_kandidata, ime_nd_kandidata, prezime_nd_kandidata
									FROM idk_nd_kandidata
									WHERE zaduzen_zaposlenik_nd_kandidata = :zaduzen_zaposlenik_nd_kandidata AND status_nd_kandidata = :status_nd_kandidata AND pstatus_nd_kandidata = :pstatus_nd_kandidata
			");
			$query3->execute(array(
									':zaduzen_zaposlenik_nd_kandidata' => $employee_id3,
									':status_nd_kandidata' => 1,
									':pstatus_nd_kandidata' => 2
			));
			$txt_mail3 = "";
			$kan_red3 = "";
			$txt_mail3x = "";
			$broj3 = $query3->rowCount();
			$broj3x = 0;
			if($broj3 != 0){
				while($row3 = $query3->fetch()){
					$id3 = $row3['id_broj_nd_kandidata'];
					$ime3 = $row3['ime_nd_kandidata'];
					$prezime3 = $row3['prezime_nd_kandidata'];
					
					$query31 = $db->prepare("
											SELECT vrijeme_promjene_statusa_nd_kandidata
											FROM idk_nd_kandidata_status_log
											WHERE idd_broj_nd_kandidata = :idd_broj_nd_kandidata AND status_nd_kandidata = :status_nd_kandidata AND pstatus_nd_kandidata = :pstatus_nd_kandidata AND broj_dana_statusa_nd_kandidata is null 
					");
					$query31->execute(array(
											':idd_broj_nd_kandidata' => $id3,
											':status_nd_kandidata' => 1,
											':pstatus_nd_kandidata' => 2
					));
					$row31 = $query31->fetch();
					$vrijeme_status3 = $row31['vrijeme_promjene_statusa_nd_kandidata'];
					
					$diff_novi3 = strtotime($vr_trenutno) - strtotime($vrijeme_status3);
					$day_novi3 = intval(floor($diff_novi3/86400));
					//Provjerava se da li se kandidat nalazi na tom statusu vise od 15 dana
					//Ako nalazi - dolazi u spisku menadzeru za ponovo prozivanje
					if($day_novi3 >= 15){
						$broj3x++;
						$kan_red3 = ' <tr><td>'.$ime3.' '.$prezime3.'</td><td> '. getSiteUrlr() .'nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$id3.' </td></tr>';
						$txt_mail3 = $txt_mail3." ".$kan_red3." ";
					}
				}
				if($broj3x != 0){
					$txt_mail3x='
									<html>
										<head>
											<style>
											table, th, td {
											  border: 1px solid black;
											  border-collapse: collapse;
											}
											th, td {
											  padding: 5px;
											  text-align: center;
											}
											caption{
												padding-bottom: 40px;
												font-weight: bold;
												font-size: x-large;
											}
											</style>
										</head>
										<body>
											<table style="width:100%">
												<caption>Spisak kandidata pod statusom Neuspješan Kontakt 3</caption>
												<tr>
													<th>Ime i Prezime</th>
													<th>Link</th>
												</tr>
												'.$txt_mail3.'
												<tr>
													<td style = "text-align: right;" colspan = "2">Broj kandidata: '.$broj3x.'</td>
												</tr>
											</table>
										</body>
									</html>
								';
					//echo $txt_mail3x;
					//Send email to user
					$mail_email3 = $employee_email3;
					$mail_name3 = $employee_firstname3." ".$employee_lastname3;
					$mail_subject3 = "Dipl modul - Neuspješan kontakt 3";
					$mail_body3 = $txt_mail3x;
					$mail_altbody3 = $txt_mail3x;
					
					sendEmail($mail_email3, $mail_name3, $mail_subject3, $mail_body3, $mail_altbody3);
				}
			}
		}
	//END
	
	// 4. Cetvrti dio je urađen u svrhu obavijesti menadzera o kandidatima koji su oznaceni kao neuspješnima nakon jedan poziv, te ih je kao takve potrebno ponovo zvati nakon 1 dan
	// START
		$user_query4 = $db->prepare("
								SELECT employee_id, employee_firstname, employee_lastname, employee_email
								FROM idk_employees
								WHERE employee_nostrifikacija_diploma = :employee_nostrifikacija_diploma
								ORDER BY employee_id ASC
								");

		$user_query4->execute(array(
								':employee_nostrifikacija_diploma' => 1
								));

		while($user4 = $user_query4->fetch()){
			$employee_id4 = $user4['employee_id'];
			$employee_firstname4 = $user4['employee_firstname'];
			$employee_lastname4 = $user4['employee_lastname'];
			$employee_email4 = $user4['employee_email'];
			
			$query4 = $db->prepare("
									SELECT id_broj_nd_kandidata, ime_nd_kandidata, prezime_nd_kandidata
									FROM idk_nd_kandidata
									WHERE zaduzen_zaposlenik_nd_kandidata = :zaduzen_zaposlenik_nd_kandidata AND status_nd_kandidata = :status_nd_kandidata AND pstatus_nd_kandidata = :pstatus_nd_kandidata
			");
			$query4->execute(array(
									':zaduzen_zaposlenik_nd_kandidata' => $employee_id4,
									':status_nd_kandidata' => 1,
									':pstatus_nd_kandidata' => 6
			));
			$txt_mail4 = "";
			$kan_red4 = "";
			$txt_mail4x = "";
			$broj4 = $query4->rowCount();
			$broj4x = 0;
			if($broj4 != 0){
				while($row4 = $query4->fetch()){
					$id4 = $row4['id_broj_nd_kandidata'];
					$ime4 = $row4['ime_nd_kandidata'];
					$prezime4 = $row4['prezime_nd_kandidata'];
					
					$query41 = $db->prepare("
											SELECT vrijeme_promjene_statusa_nd_kandidata
											FROM idk_nd_kandidata_status_log
											WHERE idd_broj_nd_kandidata = :idd_broj_nd_kandidata AND status_nd_kandidata = :status_nd_kandidata AND pstatus_nd_kandidata = :pstatus_nd_kandidata AND broj_dana_statusa_nd_kandidata is null 
					");
					$query41->execute(array(
											':idd_broj_nd_kandidata' => $id4,
											':status_nd_kandidata' => 1,
											':pstatus_nd_kandidata' => 6
					));
					$row41 = $query41->fetch();
					$vrijeme_status4 = $row41['vrijeme_promjene_statusa_nd_kandidata'];
					
					$diff_novi4 = strtotime($vr_trenutno) - strtotime($vrijeme_status4);
					$day_novi4 = intval(floor($diff_novi4/86400));
					//Provjerava se da li se kandidat nalazi na tom statusu vise od 15 dana
					//Ako nalazi - dolazi u spisku menadzeru za ponovo prozivanje
					if($day_novi4 >= 1){
						$broj4x++;
						$kan_red4 = ' <tr><td>'.$ime4.' '.$prezime4.'</td><td> '. getSiteUrlr() .'nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$id4.' </td></tr>';
						$txt_mail4 = $txt_mail4." ".$kan_red4." ";
					}
				}
				if($broj4x != 0){
					$txt_mail4x='
									<html>
										<head>
											<style>
											table, th, td {
											  border: 1px solid black;
											  border-collapse: collapse;
											}
											th, td {
											  padding: 5px;
											  text-align: center;
											}
											caption{
												padding-bottom: 40px;
												font-weight: bold;
												font-size: x-large;
											}
											</style>
										</head>
										<body>
											<table style="width:100%">
												<caption>Spisak kandidata pod statusom Neuspješan Kontakt 1</caption>
												<tr>
													<th>Ime i Prezime</th>
													<th>Link</th>
												</tr>
												'.$txt_mail4.'
												<tr>
													<td style = "text-align: right;" colspan = "2">Broj kandidata: '.$broj4x.'</td>
												</tr>
											</table>
										</body>
									</html>
								';
					//echo $txt_mail4x;
					//Send email to user
					$mail_email4 = $employee_email4;
					$mail_name4 = $employee_firstname4." ".$employee_lastname4;
					$mail_subject4 = "Dipl modul - Neuspješan Kontakt 1";
					$mail_body4 = $txt_mail4x;
					$mail_altbody4 = $txt_mail4x;
					
					sendEmail($mail_email4, $mail_name4, $mail_subject4, $mail_body4, $mail_altbody4);
				}
			}
		}
	//END
	
	// 5. Peti dio je urađen u svrhu obavijesti menadzera o kandidatima koji su oznaceni kao zainteresirani, te ih je kao takve potrebno ponovo zvati nakon 5 dan
	// START
		$user_query5 = $db->prepare("
								SELECT employee_id, employee_firstname, employee_lastname, employee_email
								FROM idk_employees
								WHERE employee_nostrifikacija_diploma = :employee_nostrifikacija_diploma
								ORDER BY employee_id ASC
								");

		$user_query5->execute(array(
								':employee_nostrifikacija_diploma' => 1
								));

		while($user5 = $user_query5->fetch()){
			$employee_id5 = $user5['employee_id'];
			$employee_firstname5 = $user5['employee_firstname'];
			$employee_lastname5 = $user5['employee_lastname'];
			$employee_email5 = $user5['employee_email'];
			
			$query5 = $db->prepare("
									SELECT id_broj_nd_kandidata, ime_nd_kandidata, prezime_nd_kandidata
									FROM idk_nd_kandidata
									WHERE zaduzen_zaposlenik_nd_kandidata = :zaduzen_zaposlenik_nd_kandidata AND status_nd_kandidata = :status_nd_kandidata AND pstatus_nd_kandidata = :pstatus_nd_kandidata
			");
			$query5->execute(array(
									':zaduzen_zaposlenik_nd_kandidata' => $employee_id5,
									':status_nd_kandidata' => 1,
									':pstatus_nd_kandidata' => 3
			));
			$txt_mail5 = "";
			$kan_red5 = "";
			$txt_mail5x = "";
			$broj5 = $query5->rowCount();
			$broj5x = 0;
			if($broj5 != 0){
				while($row5 = $query5->fetch()){
					$id5 = $row5['id_broj_nd_kandidata'];
					$ime5 = $row5['ime_nd_kandidata'];
					$prezime5 = $row5['prezime_nd_kandidata'];
					
					$query51 = $db->prepare("
											SELECT vrijeme_promjene_statusa_nd_kandidata	
											FROM idk_nd_kandidata_status_log
											WHERE idd_broj_nd_kandidata = :idd_broj_nd_kandidata AND status_nd_kandidata = :status_nd_kandidata AND pstatus_nd_kandidata = :pstatus_nd_kandidata AND broj_dana_statusa_nd_kandidata is null 
					");
					$query51->execute(array(
											':idd_broj_nd_kandidata' => $id5,
											':status_nd_kandidata' => 1,
											':pstatus_nd_kandidata' => 3
					));
					$row51 = $query51->fetch();
					$vrijeme_status5 = $row51['vrijeme_promjene_statusa_nd_kandidata'];
					
					$diff_novi5 = strtotime($vr_trenutno) - strtotime($vrijeme_status5);
					$day_novi5 = intval(floor($diff_novi5/86400));
					//Provjerava se da li se kandidat nalazi na tom statusu vise od 15 dana
					//Ako nalazi - dolazi u spisku menadzeru za ponovo prozivanje
					if($day_novi5 >= 5){
						$broj5x++;
						$kan_red5 = ' <tr><td>'.$ime5.' '.$prezime5.'</td><td> '. getSiteUrlr() .'nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$id5.' </td></tr>';
						$txt_mail5 = $txt_mail5." ".$kan_red5." ";
					}
				}
				if($broj5x != 0){
					$txt_mail5x='
									<html>
										<head>
											<style>
											table, th, td {
											  border: 1px solid black;
											  border-collapse: collapse;
											}
											th, td {
											  padding: 5px;
											  text-align: center;
											}
											caption{
												padding-bottom: 40px;
												font-weight: bold;
												font-size: x-large;
											}
											</style>
										</head>
										<body>
											<table style="width:100%">
												<caption>Spisak Zainteresiranih kandidata koje je potrebno prozvati</caption>
												<tr>
													<th>Ime i Prezime</th>
													<th>Link</th>
												</tr>
												'.$txt_mail5.'
												<tr>
													<td style = "text-align: right;" colspan = "2">Broj kandidata: '.$broj5x.'</td>
												</tr>
											</table>
										</body>
									</html>
								';
					//echo $txt_mail5x;
					//Send email to user
					$mail_email5 = $employee_email5;
					$mail_name5 = $employee_firstname5." ".$employee_lastname5;
					$mail_subject5 = "Dipl modul - Zainteresirani Kandidati";
					$mail_body5 = $txt_mail5x;
					$mail_altbody5 = $txt_mail5x;
					
					sendEmail($mail_email5, $mail_name5, $mail_subject5, $mail_body5, $mail_altbody5);
				}
			}
		}
	//END
?>
