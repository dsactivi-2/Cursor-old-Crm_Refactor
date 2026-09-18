<?php
	include("includes/functions.php");
	
	$currentDate = date("Y-m-d");
	$plus2Date = date("Y-m-d",strtotime('+2 days',strtotime($currentDate)));
	$currentDay = date("D");
	
	if($currentDay != "Sat" AND $currentDay != "Sun"){
		$br = 0;
		$sumBr = 0;
		$sumRows = '';
		$oneRow = '';
		$txtMail = '';
		$query = $db->prepare("
			SELECT kan.id_broj_nd_kandidata, kan.ime_nd_kandidata, kan.prezime_nd_kandidata, emp.employee_firstname, emp.employee_lastname
			FROM idk_nd_kandidata kan
			INNER JOIN idk_nd_kandidata_biljeske bilj
			ON kan.id_broj_nd_kandidata = bilj.id_kandidata_biljeska_nd
			INNER JOIN idk_predracuni pred
			ON bilj.predracun_id = pred.pr_id
			INNER JOIN idk_employees emp
			ON bilj.dodao_zaposlenik_biljeska_nd = emp.employee_id
			WHERE bilj.status_biljeska_nd = 3 AND bilj.tip_biljeska_nd = 5 AND bilj.zadnja_inkaso_biljeska = 1 AND bilj.uplata_na_datum LIKE '%".$plus2Date."%' AND pred.pr_status IN(3,4,5)
			ORDER BY bilj.dodao_zaposlenik_biljeska_nd ASC
		");
		$query->execute(array());
		if(intval($query->rowCount()) != 0){
			while($row = $query->fetch()){
				$br++;
				$sumBr = $sumBr + 1;
				$id = $row['id_broj_nd_kandidata'];
				$ime = $row['ime_nd_kandidata'];
				$prezime = $row['prezime_nd_kandidata'];
				$dodao = $row['employee_firstname']. ' '.$row['employee_lastname'];
				$oneRow = '
					<tr>
						<td>'.$br.'</td>
						<td>'.$ime.' '.$prezime.'</td>
						<td>'.getSiteUrlr().'nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$id.'</td>
						<td>'.$dodao.'</td>
					</tr>
				';
				$sumRows = $sumRows.' '.$oneRow;
			}
			
			$txtMail = '
				<html>
					<head>
						<style>
							table, th, td { border: 1px solid black; border-collapse: collapse;}
							th, td {padding: 5px; text-align: center;}
							caption{ padding-bottom: 40px; font-weight: bold; font-size: x-large;}
						</style>
					</head>
					<body>
						<table style="width:100%">
							<caption>Inkaso<br>Spisak kandidata koji su rekli uplatiti na dan '.date("d.m.Y", strtotime($plus2Date)).'</caption>
							<tr>
								<th>Broj</th>
								<th>Ime i Prezime</th>
								<th>Link</th>
								<th>Zaposlenik</th>
							</tr>
							'.$sumRows.'
							<tr>
								<td style = "text-align: right;" colspan = "4">Kandidate navedene unutar tabele potrebno je prozvati 2 dana prije datuma koji je naznačen za uplatu. </td>
							</tr>
							<tr>
								<td style = "text-align: right;" colspan = "4">Broj kandidata: '.$sumBr.'</td>
							</tr>
						</table>
					</body>
				</html>
			';
			
			$queryEmpl = $db->prepare("
				SELECT emp.employee_email, emp.employee_firstname, emp.employee_lastname
				FROM idk_employees emp
				WHERE emp.employee_status LIKE '%14%'
			");
			$queryEmpl->execute();
			while($rowEmpl = $queryEmpl->fetch()){
				$fnameEmp = $rowEmpl["employee_firstname"];
				$lnameEmp = $rowEmpl["employee_lastname"];
				$emailEmp = $rowEmpl["employee_email"];
				
				$mail_email = "";
				$mail_name = "";
				$mail_subject = "";
				$mail_body = "";
				$mail_altbody = "";
				
				//Send email to user
				$mail_email = $emailEmp;
				$mail_name = $fnameEmp." ".$lnameEmp;
				$mail_subject = "INKASO - UPLATA NA DAN ".date("d.m.Y", strtotime($plus2Date))."";
				$mail_body = $txtMail;
				$mail_altbody = $txtMail;
				
				sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);
			}
		}
		
	}
	
?>