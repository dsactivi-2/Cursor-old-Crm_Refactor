<?php
	error_reporting(0);
	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus = getEmployeeStatus();

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: employees?page=list");
	}


			switch ($page){

				
				case "create_dodatno_pitanje":
				
					// header('Content-Type: application/json; charset=utf-8');
					
					$txt = isset($_POST['dp_tekst']) ? trim($_POST['dp_tekst']) : '';
					if ($txt === '') {
						echo json_encode(['ok' => 0, 'msg' => 'Prazan tekst.']);
						exit;
					}
					
					// Optional: prevent duplicates
					// $check = $db->prepare("SELECT dp_id FROM idk_dodatna_pitanja WHERE dp_tekst = ?");
					// $check->execute([$txt]);
					// if ($row = $check->fetch(PDO::FETCH_ASSOC)) { ... }
					
					$ins = $db->prepare("INSERT INTO idk_dodatna_pitanja (dp_tekst) VALUES (?)");
					$ins->execute([$txt]);
					
					$dp_id = (int)$db->lastInsertId();
					
					echo json_encode([
						'ok' => 1,
						'dp_id' => $dp_id,
						'dp_tekst' => $txt
					]);
					exit;
					

				break;

				case "get_Project_Nalog":
				
				$nalog = $_POST['nalog'];
				
				if($nalog != 0){
					$select_query = $db->prepare("
							SELECT project_id, project_name, project_status, project_nalogid
							FROM idk_projects
							WHERE project_name NOT LIKE ('%Intervju%')AND project_status != 0 AND project_nalogid =" .$nalog);

					$select_query->execute();
					$html = "";
					while($select_row = $select_query->fetch()) {
						$html.= "<option value='" . $select_row['project_id'] . "'>" . $select_row['project_name'] . "</option>";
					}
				}
				else{
					$select_query = $db->prepare("
							SELECT project_id, project_name, project_status, project_nalogid
							FROM idk_projects
							WHERE project_status != 0 AND project_nalogid =0 OR project_nalogid IS NULL");

					$select_query->execute();
					$html = "";
					while($select_row = $select_query->fetch()) {
						
						$html.= "<option value='" . $select_row['project_id'] . "'>" . $select_row['project_name'] . "</option>";
					}
				}
				echo $html;
				break;
				
				
				case "get_Kandidat_Zamjena":
					$nalog_id = $_POST['nalog_id'];
					$kf_zamjena_id = $_POST['kf_zamjena_id'];
					
					$query_nalog = $db->prepare("
									SELECT DISTINCT(kandidat_id)
									FROM idk_kandidat_financije
									WHERE nalog_id = :nalog_id AND kf_status = 1");
									
					$query_nalog->execute(array(
								':nalog_id' => $nalog_id));
								
					while($row_nalog = $query_nalog->fetch()){
					
						$kandidat_id = $row_nalog['kandidat_id'];
						
						$query_kandidati = $db->prepare("
										SELECT kandidat_ime, kandidat_prezime
										FROM idk_kandidati
										WHERE kandidat_id = :kandidat_id");
										
						$query_kandidati->execute(array(
									':kandidat_id' => $kandidat_id));
									
						$row = $query_kandidati->fetch();

						$kandidat_ime = $row['kandidat_ime'];
						$kandidat_prezime = $row['kandidat_prezime'];
						if($kf_zamjena_id == $kandidat_id){
							$html.= "<option value='" . $kandidat_id . "' selected>" . $kandidat_ime . " ".$kandidat_prezime."</option>";
						}
						else{
							$html.= "<option value='" . $kandidat_id . "'>" . $kandidat_ime . " ".$kandidat_prezime."</option>";
						}
					}
					echo $html;
				break;
				
				case "kandidat_placeno_da":
					$kf_id = $_POST['kf_id'];
					$placeno = $_POST['placeno'];
					$datum_placanja = $_POST['datum_placanja'];
					$kf_datum_placanja = date('Y-m-d', strtotime($datum_placanja));
					
					$financije_query = $db->prepare("
										UPDATE idk_kandidat_financije
										SET kf_placeno = 1, kf_datum_placanja = :kf_datum_placanja
										WHERE kf_id = :kf_id
										");
										
					$financije_query->execute(array(
									':kf_id' => $kf_id,
									':kf_datum_placanja' => $kf_datum_placanja));
					
				break;
				
				case "kandidat_placeno_ne":
					$kf_id = $_POST['kf_id'];
					$placeno = $_POST['placeno'];
					
					$financije_query = $db->prepare("
										UPDATE idk_kandidat_financije
										SET kf_placeno = 0
										WHERE kf_id = :kf_id
										");
										
					$financije_query->execute(array(
									':kf_id' => $kf_id));
					
				break;
				
				case "rata_odmah_placeno_da":
					$nalog_id = $_POST['nalog_id'];
					$placeno = $_POST['placeno'];
					
					$financije_query = $db->prepare("
										UPDATE idk_nalozi
										SET nalog_placena_prva_rata = 1
										WHERE nalog_id = :nalog_id
										");
										
					$financije_query->execute(array(
									':nalog_id' => $nalog_id));
					
				break;
				
				case "rata_odmah_placeno_ne":
					$nalog_id = $_POST['nalog_id'];
					$placeno = $_POST['placeno'];
					
					$financije_query = $db->prepare("
										UPDATE idk_nalozi
										SET nalog_placena_prva_rata = 0
										WHERE nalog_id = :nalog_id
										");
										
					$financije_query->execute(array(
									':nalog_id' => $nalog_id));
					
				break;
				
				case "kandidat_zavrsen":
					$kf_id = $_POST['kf_id'];
					
					$query_kandidati_financije = $db->prepare("
										SELECT kandidat_id, nalog_id
										FROM idk_kandidat_financije
										WHERE kf_id = :kf_id");
						
					$query_kandidati_financije->execute(array(
										':kf_id' => $kf_id
										));
					
					$kandidat_financije = $query_kandidati_financije->fetch();
					$kandidat_id = $kandidat_financije['kandidat_id'];
					$nalog_id = $kandidat_financije['nalog_id'];
					
					$financije_query = $db->prepare("
										UPDATE idk_kandidat_financije
										SET kf_status = 3
										WHERE nalog_id = :nalog_id AND kandidat_id = :kandidat_id");
										
					$financije_query->execute(array(
									':nalog_id' => $nalog_id,
									':kandidat_id' => $kandidat_id
									));
					
				break;
				
				case "posalji_zaposlenike_odjela":
		
					$odjel_zasposlenika = $_POST["tiket_odjel_p"];
					
					
					$query = $db->prepare("
									SELECT employee_id, employee_firstname, employee_lastname
									FROM idk_employees
									WHERE employee_odjel = $odjel_zasposlenika
									AND employee_status != 0
									");

					$query->execute();

					echo '<option value="0">Pošalji cijelom odjelu</option>';
					
					while($row = $query->fetch()){

						$zaposlenik_id = $row['employee_id'];
						$zaposlenik_ime = $row['employee_firstname'];
						$zaposlenik_prezime = $row['employee_lastname'];
						$zaposlenik_full = $zaposlenik_ime. ' ' .$zaposlenik_prezime;
						echo ' <option value = " ' . $zaposlenik_id. ' ">'. $zaposlenik_full . ' </option>';
					}
				
				break;
				
				case "ispis_komentara":
		
					$ticket_komentar_id_tiketa_ispis = $_POST["id_t12_comment_ispis"];
					$komentari_ispis = $db->prepare("
											SELECT *
											FROM idk_ticketi_komentari
											WHERE ticket_k_id_tiketa = :ticket_k_id_tiketa");

					$komentari_ispis->execute(array(
										':ticket_k_id_tiketa' => $ticket_komentar_id_tiketa_ispis));
										
					while($komentari_ispis_row = $komentari_ispis->fetch()){
						$ticket_komentar_posiljaoc = $komentari_ispis_row['ticket_k_posiljaoc'];
						$ticket_komentar_sadrzaj = $komentari_ispis_row['ticket_k_sadrzaj'];
						$ticket_komentar_dokument = $komentari_ispis_row['ticket_k_dokument'];
						$ticket_komentar_vrijeme_kreiranja = date('d.m.Y H:i', strtotime($komentari_ispis_row['ticket_k_vrijeme_kreiranja'])); 
						
						//Provjera ekstenzije dokumenta
						if($ticket_komentar_dokument !== "1"){
							
							if (strpos($ticket_komentar_dokument, '.jpg') !== false OR strpos($ticket_komentar_dokument, '.png') !== false){
								$tiket_dokument_icon = '<i class="fa fa-file-image-o fa-3x" aria-hidden="true"></i>';
							}
							else if(strpos($ticket_komentar_dokument, '.pdf') !== false){
								$tiket_dokument_icon = '<i class="fa fa-file-pdf-o fa-3x" aria-hidden="true"></i>';
							}
							else if(strpos($ticket_komentar_dokument, '.doc') !== false OR strpos($ticket_komentar_dokument, '.docx') !== false){
								$tiket_dokument_icon = '<i class="fa fa-file-word-o fa-3x" aria-hidden="true"></i>';
							}
							else if(strpos($ticket_komentar_dokument, '.xls') !== false OR strpos($ticket_komentar_dokument, '.xlsx') !== false OR strpos($ticket_komentar_dokument, '.csv') !== false){
								$tiket_dokument_icon = '<i class="fa fa-file-excel-o fa-3x" aria-hidden="true"></i>';
							}
							else if(strpos($ticket_komentar_dokument, '.txt') !== false ){
								$tiket_dokument_icon = '<i class="fa fa-file-text-o fa-3x" aria-hidden="true"></i>';
							}
							else{
								$tiket_dokument_icon = '<i class="fa fa-file-powerpoint-o fa-3x" aria-hidden="true"></i>';
							}
							
							$tiket_dokument_download = '<a href="'.getSiteUrlr().'files/dokumenti_tiketi/'.$ticket_komentar_dokument.'" class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK">'.$tiket_dokument_icon.'</a>';
							
						}
						else{
							$tiket_dokument_download = 'Nije priložen dokument.';
						}
						
						$ticket_komentar_posiljaoc_ispis = getZaposlenikimeR($ticket_komentar_posiljaoc);
						
						echo '
							<hr id = "modal_linija">
							<div class = "row">
								<div class="col-xs-1 text-center">
									<p><i class="fa fa-user-circle-o" aria-hidden="true"></i></p>
									<p><i class="fa fa-calendar" aria-hidden="true"></i></p>
								</div>
								<div class="col-xs-11 text-left">
									<p>Pošiljaoc: <span>'.$ticket_komentar_posiljaoc_ispis.'</span></p>
									<p>Datum kreiranja: <span>'.$ticket_komentar_vrijeme_kreiranja.'</span></p>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-1 text-center">
									<p><i class="fa fa-comment-o" aria-hidden="true"></i></p>
								</div>
								<div class="col-xs-8 text-left">
									<p>Komentar<span class = "predmet_izgled"></span></p>
								</div>
								<div class="col-xs-1 text-center">
									<p><i class="fa fa-file-o" aria-hidden="true"></i></p>
								</div>
								<div class="col-xs-2 text-left">
									<p>Dokumenti</p>
								</div>
							</div>
							<div class="row">
								<div id = "modal_informacije" class="col-xs-9 text-center">
									<p class = "opis_izgled_k" >'.$ticket_komentar_sadrzaj.'</p>
								</div>
								<div class="col-xs-3 text-center" style = "padding: 10px;">
									<p><span>'.$tiket_dokument_download.'</span></p>
								</div>
							</div>
						';
						
					}
					
				break;
				
				case "ispis_komentara_p":
		
					$ticket_komentar_id_tiketa_ispis = $_POST["id_t14_comment_ispis_p"];
					$komentari_ispis = $db->prepare("
											SELECT *
											FROM idk_ticketi_komentari
											WHERE ticket_k_id_tiketa = :ticket_k_id_tiketa");

					$komentari_ispis->execute(array(
										':ticket_k_id_tiketa' => $ticket_komentar_id_tiketa_ispis));
										
					while($komentari_ispis_row = $komentari_ispis->fetch()){
						$ticket_komentar_posiljaoc = $komentari_ispis_row['ticket_k_posiljaoc'];
						$ticket_komentar_sadrzaj = $komentari_ispis_row['ticket_k_sadrzaj'];
						$ticket_komentar_dokument = $komentari_ispis_row['ticket_k_dokument'];
						$ticket_komentar_vrijeme_kreiranja = date('d.m.Y H:i', strtotime($komentari_ispis_row['ticket_k_vrijeme_kreiranja'])); 
						
						//Provjera ekstenzije dokumenta
						if($ticket_komentar_dokument !== "1"){
							
							if (strpos($ticket_komentar_dokument, '.jpg') !== false OR strpos($ticket_komentar_dokument, '.png') !== false){
								$tiket_dokument_icon = '<i class="fa fa-file-image-o fa-3x" aria-hidden="true"></i>';
							}
							else if(strpos($ticket_komentar_dokument, '.pdf') !== false){
								$tiket_dokument_icon = '<i class="fa fa-file-pdf-o fa-3x" aria-hidden="true"></i>';
							}
							else if(strpos($ticket_komentar_dokument, '.doc') !== false OR strpos($ticket_komentar_dokument, '.docx') !== false){
								$tiket_dokument_icon = '<i class="fa fa-file-word-o fa-3x" aria-hidden="true"></i>';
							}
							else if(strpos($ticket_komentar_dokument, '.xls') !== false OR strpos($ticket_komentar_dokument, '.xlsx') !== false OR strpos($ticket_komentar_dokument, '.csv') !== false){
								$tiket_dokument_icon = '<i class="fa fa-file-excel-o fa-3x" aria-hidden="true"></i>';
							}
							else if(strpos($ticket_komentar_dokument, '.txt') !== false ){
								$tiket_dokument_icon = '<i class="fa fa-file-text-o fa-3x" aria-hidden="true"></i>';
							}
							else{
								$tiket_dokument_icon = '<i class="fa fa-file-powerpoint-o fa-3x" aria-hidden="true"></i>';
							}
							
							$tiket_dokument_download = '<a href="'.getSiteUrlr().'files/dokumenti_tiketi/'.$ticket_komentar_dokument.'" class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK">'.$tiket_dokument_icon.'</a>';
							
						}
						else{
							$tiket_dokument_download = 'Nije priložen dokument.';
						}
						
						$ticket_komentar_posiljaoc_ispis = getZaposlenikimeR($ticket_komentar_posiljaoc);
						
						echo '
							<hr id = "modal_linija">
							<div class = "row">
								<div class="col-xs-1 text-center">
									<p><i class="fa fa-user-circle-o" aria-hidden="true"></i></p>
									<p><i class="fa fa-calendar" aria-hidden="true"></i></p>
								</div>
								<div class="col-xs-11 text-left">
									<p>Pošiljaoc: <span>'.$ticket_komentar_posiljaoc_ispis.'</span></p>
									<p>Datum kreiranja: <span>'.$ticket_komentar_vrijeme_kreiranja.'</span></p>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-1 text-center">
									<p><i class="fa fa-comment-o" aria-hidden="true"></i></p>
								</div>
								<div class="col-xs-8 text-left">
									<p>Komentar<span class = "predmet_izgled"></span></p>
								</div>
								<div class="col-xs-1 text-center">
									<p><i class="fa fa-file-o" aria-hidden="true"></i></p>
								</div>
								<div class="col-xs-2 text-left">
									<p>Dokumenti</p>
								</div>
							</div>
							<div class="row">
								<div id = "modal_informacije" class="col-xs-9 text-center">
									<p class = "opis_izgled_k" >'.$ticket_komentar_sadrzaj.'</p>
								</div>
								<div class="col-xs-3 text-center" style = "padding: 10px;">
									<p><span>'.$tiket_dokument_download.'</span></p>
								</div>
							</div>
						';
						
					}
					
				break;
				
				case "ispis_komentara_n":
		
					$ticket_komentar_id_tiketa_ispis = $_POST["id_t11_comment_ispis"];
					$komentari_ispis = $db->prepare("
											SELECT *
											FROM idk_ticketi_komentari
											WHERE ticket_k_id_tiketa = :ticket_k_id_tiketa");

					$komentari_ispis->execute(array(
										':ticket_k_id_tiketa' => $ticket_komentar_id_tiketa_ispis));
										
					while($komentari_ispis_row = $komentari_ispis->fetch()){
						$ticket_komentar_posiljaoc = $komentari_ispis_row['ticket_k_posiljaoc'];
						$ticket_komentar_sadrzaj = $komentari_ispis_row['ticket_k_sadrzaj'];
						$ticket_komentar_dokument = $komentari_ispis_row['ticket_k_dokument'];
						$ticket_komentar_vrijeme_kreiranja = date('d.m.Y H:i', strtotime($komentari_ispis_row['ticket_k_vrijeme_kreiranja'])); 
						
						//Provjera ekstenzije dokumenta
						if($ticket_komentar_dokument !== "1"){
							
							if (strpos($ticket_komentar_dokument, '.jpg') !== false OR strpos($ticket_komentar_dokument, '.png') !== false){
								$tiket_dokument_icon = '<i class="fa fa-file-image-o fa-3x" aria-hidden="true"></i>';
							}
							else if(strpos($ticket_komentar_dokument, '.pdf') !== false){
								$tiket_dokument_icon = '<i class="fa fa-file-pdf-o fa-3x" aria-hidden="true"></i>';
							}
							else if(strpos($ticket_komentar_dokument, '.doc') !== false OR strpos($ticket_komentar_dokument, '.docx') !== false){
								$tiket_dokument_icon = '<i class="fa fa-file-word-o fa-3x" aria-hidden="true"></i>';
							}
							else if(strpos($ticket_komentar_dokument, '.xls') !== false OR strpos($ticket_komentar_dokument, '.xlsx') !== false OR strpos($ticket_komentar_dokument, '.csv') !== false){
								$tiket_dokument_icon = '<i class="fa fa-file-excel-o fa-3x" aria-hidden="true"></i>';
							}
							else if(strpos($ticket_komentar_dokument, '.txt') !== false ){
								$tiket_dokument_icon = '<i class="fa fa-file-text-o fa-3x" aria-hidden="true"></i>';
							}
							else{
								$tiket_dokument_icon = '<i class="fa fa-file-powerpoint-o fa-3x" aria-hidden="true"></i>';
							}
							
							$tiket_dokument_download = '<a href="'.getSiteUrlr().'files/dokumenti_tiketi/'.$ticket_komentar_dokument.'" class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK">'.$tiket_dokument_icon.'</a>';
							
						}
						else{
							$tiket_dokument_download = 'Nije priložen dokument.';
						}
						
						$ticket_komentar_posiljaoc_ispis = getZaposlenikimeR($ticket_komentar_posiljaoc);
						
						echo '
							<hr id = "modal_linija">
							<div class = "row">
								<div class="col-xs-1 text-center">
									<p><i class="fa fa-user-circle-o" aria-hidden="true"></i></p>
									<p><i class="fa fa-calendar" aria-hidden="true"></i></p>
								</div>
								<div class="col-xs-11 text-left">
									<p>Pošiljaoc: <span>'.$ticket_komentar_posiljaoc_ispis.'</span></p>
									<p>Datum kreiranja: <span>'.$ticket_komentar_vrijeme_kreiranja.'</span></p>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-1 text-center">
									<p><i class="fa fa-comment-o" aria-hidden="true"></i></p>
								</div>
								<div class="col-xs-8 text-left">
									<p>Komentar<span class = "predmet_izgled"></span></p>
								</div>
								<div class="col-xs-1 text-center">
									<p><i class="fa fa-file-o" aria-hidden="true"></i></p>
								</div>
								<div class="col-xs-2 text-left">
									<p>Dokumenti</p>
								</div>
							</div>
							<div class="row">
								<div id = "modal_informacije" class="col-xs-9 text-center">
									<p class = "opis_izgled_k" >'.$ticket_komentar_sadrzaj.'</p>
								</div>
								<div class="col-xs-3 text-center" style = "padding: 10px;">
									<p><span>'.$tiket_dokument_download.'</span></p>
								</div>
							</div>
						';
						
					}
					
				break;
				
				case "ispis_komentara_z":
		
					$ticket_komentar_id_tiketa_ispis = $_POST["id_t13_comment_ispis"];
					$komentari_ispis = $db->prepare("
											SELECT *
											FROM idk_ticketi_komentari
											WHERE ticket_k_id_tiketa = :ticket_k_id_tiketa");

					$komentari_ispis->execute(array(
										':ticket_k_id_tiketa' => $ticket_komentar_id_tiketa_ispis));
										
					while($komentari_ispis_row = $komentari_ispis->fetch()){
						$ticket_komentar_posiljaoc = $komentari_ispis_row['ticket_k_posiljaoc'];
						$ticket_komentar_sadrzaj = $komentari_ispis_row['ticket_k_sadrzaj'];
						$ticket_komentar_dokument = $komentari_ispis_row['ticket_k_dokument'];
						$ticket_komentar_vrijeme_kreiranja = date('d.m.Y H:i', strtotime($komentari_ispis_row['ticket_k_vrijeme_kreiranja'])); 
						
						//Provjera ekstenzije dokumenta
						if($ticket_komentar_dokument !== "1"){
							
							if (strpos($ticket_komentar_dokument, '.jpg') !== false OR strpos($ticket_komentar_dokument, '.png') !== false){
								$tiket_dokument_icon = '<i class="fa fa-file-image-o fa-3x" aria-hidden="true"></i>';
							}
							else if(strpos($ticket_komentar_dokument, '.pdf') !== false){
								$tiket_dokument_icon = '<i class="fa fa-file-pdf-o fa-3x" aria-hidden="true"></i>';
							}
							else if(strpos($ticket_komentar_dokument, '.doc') !== false OR strpos($ticket_komentar_dokument, '.docx') !== false){
								$tiket_dokument_icon = '<i class="fa fa-file-word-o fa-3x" aria-hidden="true"></i>';
							}
							else if(strpos($ticket_komentar_dokument, '.xls') !== false OR strpos($ticket_komentar_dokument, '.xlsx') !== false OR strpos($ticket_komentar_dokument, '.csv') !== false){
								$tiket_dokument_icon = '<i class="fa fa-file-excel-o fa-3x" aria-hidden="true"></i>';
							}
							else if(strpos($ticket_komentar_dokument, '.txt') !== false ){
								$tiket_dokument_icon = '<i class="fa fa-file-text-o fa-3x" aria-hidden="true"></i>';
							}
							else{
								$tiket_dokument_icon = '<i class="fa fa-file-powerpoint-o fa-3x" aria-hidden="true"></i>';
							}
							
							$tiket_dokument_download = '<a href="'.getSiteUrlr().'files/dokumenti_tiketi/'.$ticket_komentar_dokument.'" class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK">'.$tiket_dokument_icon.'</a>';
							
						}
						else{
							$tiket_dokument_download = 'Nije priložen dokument.';
						}
						
						$ticket_komentar_posiljaoc_ispis = getZaposlenikimeR($ticket_komentar_posiljaoc);
						
						echo '
							<hr id = "modal_linija">
							<div class = "row">
								<div class="col-xs-1 text-center">
									<p><i class="fa fa-user-circle-o" aria-hidden="true"></i></p>
									<p><i class="fa fa-calendar" aria-hidden="true"></i></p>
								</div>
								<div class="col-xs-11 text-left">
									<p>Pošiljaoc: <span>'.$ticket_komentar_posiljaoc_ispis.'</span></p>
									<p>Datum kreiranja: <span>'.$ticket_komentar_vrijeme_kreiranja.'</span></p>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-1 text-center">
									<p><i class="fa fa-comment-o" aria-hidden="true"></i></p>
								</div>
								<div class="col-xs-8 text-left">
									<p>Komentar<span class = "predmet_izgled"></span></p>
								</div>
								<div class="col-xs-1 text-center">
									<p><i class="fa fa-file-o" aria-hidden="true"></i></p>
								</div>
								<div class="col-xs-2 text-left">
									<p>Dokumenti</p>
								</div>
							</div>
							<div id = "modal_informacije" class="row">
								<div class="col-xs-9 text-center">
									<p class = "opis_izgled_k" >'.$ticket_komentar_sadrzaj.'</p>
								</div>
								<div class="col-xs-3 text-center" style = "padding: 10px;">
									<p><span>'.$tiket_dokument_download.'</span></p>
								</div>
							</div>
						';
						
					}
					
				break;
				
				// FUNKCIJE ZA MODUL NOSTRIFIKACIJA DIPLOMA START
				
				case "posalji_smjer_odabrane_skole":
		
					$skola_id_odabrano1 = $_POST["skola_id_odabrano"];
					
					$skole_smjer_ispis_select = $db->prepare("
													SELECT *
													FROM idk_skole_smjerovi
													WHERE ss_skola_id = :ss_skola_id
												");
					$skole_smjer_ispis_select->execute(array(
												':ss_skola_id' => $skola_id_odabrano1));
					
					while($row_skole_smjer_ispis_select = $skole_smjer_ispis_select->fetch()){
						$skola_smjer_id1 = $row_skole_smjer_ispis_select['ss_id'];
						$skola_smjer_naziv1 = $row_skole_smjer_ispis_select['ss_naziv'];
						echo '<option value = "'.$skola_smjer_id1.'">'.$skola_smjer_naziv1.'</option>';
					}
				break;
				
				case "posalji_smjer_odabrane_skole_ms":
		
					$skola_id_odabrano1 = $_POST["skola_id_odabrano"] ?? [];
					$skola_id_odabrano2 = "0";
					foreach ($skola_id_odabrano1 as $key => $value){
						$skola_id_odabrano2 = "".$skola_id_odabrano2.",".$value;
					}
					//echo "Podaci: ".$skola_id_odabrano2." ";
					$skole_smjer_ispis_select = $db->prepare("
													SELECT *
													FROM idk_skole_smjerovi
													WHERE ss_skola_id IN (".$skola_id_odabrano2.")
												");
					$skole_smjer_ispis_select->execute();
					
					while($row_skole_smjer_ispis_select = $skole_smjer_ispis_select->fetch()){ 
						$skola_smjer_id1 = $row_skole_smjer_ispis_select['ss_id'];
						$skola_naziv_id1 = getSkolaNDKanidataR($row_skole_smjer_ispis_select['ss_skola_id']);
						$skola_smjer_naziv1 = $row_skole_smjer_ispis_select['ss_naziv'];
						if(in_array($skola_smjer_id1,$_POST["struke_izabrane"] ?? []))
						echo '<option selected value = "'.$skola_smjer_id1.'" data-subtext="'.$skola_naziv_id1.'">'.$skola_smjer_naziv1.'</option>';
						else
						echo '<option value = "'.$skola_smjer_id1.'" data-subtext="'.$skola_naziv_id1.'">'.$skola_smjer_naziv1.'</option>';
					}
				break;
				
				case "posalji_zaposlenike_tima":
					if(in_array(139,$_POST['radnici_izabrani'] ?? []))
					echo '<option selected value="139">Skladište kandidata</option>';
					else
					echo '<option value="139">Skladište kandidata</option>';
					$team_id = $_POST["team_id_odabrano"] ?? [];
					$query = $db->prepare("
						SELECT employee_id, employee_firstname, employee_lastname, naziv_t
						FROM idk_employees
						JOIN idk_timovi
						ON idk_employees.employee_team = idk_timovi.id_t 
						WHERE employee_nostrifikacija_diploma IN (0,1) AND employee_team IN (".implode(", ", $team_id).") 
						ORDER BY employee_team ASC
					");
					$query->execute();
					
					while($row = $query->fetch()){ 
						$id_zap = $row['employee_id'];
						$team_zap = $row['naziv_t'];
						$podaci_zap = $row['employee_firstname'].' '.$row['employee_lastname'];
						if(in_array($id_zap,$_POST['radnici_izabrani'] ?? []))
						echo '<option selected value = "'.$id_zap.'" data-subtext="'.$team_zap.'">'.$podaci_zap.'</option>';
						else
						echo '<option value = "'.$id_zap.'" data-subtext="'.$team_zap.'">'.$podaci_zap.'</option>';
					}
					
				break;
				
				case "posalji_smjer_odabrane_skole_tip_dokumenta_nd":
		
					$skola_id_odabrano_1_SS = $_POST["skola_id_odabrano_ss"];
					
					$skole_smjer_nd_tip_ispis_select = $db->prepare("
															SELECT *
															FROM idk_nd_ustanove_skole_smjerovi
															WHERE idd_skole_ustanove_nd = :idd_skole_ustanove_nd
														");
					$skole_smjer_nd_tip_ispis_select->execute(array(
															':idd_skole_ustanove_nd' => $skola_id_odabrano_1_SS));
					
					while($row_skole_smjer_nd_tip_ispis_select = $skole_smjer_nd_tip_ispis_select->fetch()){
						$skola_smjer_id1_ss = $row_skole_smjer_nd_tip_ispis_select['id_skole_smjer_ustanove_nd'];
						$skola_smjer_naziv1_ss = getSkolaSmjerNDKanidataR($row_skole_smjer_nd_tip_ispis_select['ss_idd']);
						echo '<option value = "'.$skola_smjer_id1_ss.'">'.$skola_smjer_naziv1_ss.'</option>';
					}
				break;
				
				case "posalji_select_za_unos_smjera_ND_ustanove":
		
					$skola_id_hidden1x = $_POST["skola_id_hiddenx"];
					$skola_nd_id_hidden1x = $_POST["skola_nd_id_hiddenx"];
					
					$skole_smjer_ispis_select1 = $db->prepare("
													SELECT *
													FROM idk_skole_smjerovi
													WHERE ss_skola_id = :ss_skola_id
												"); 
					$skole_smjer_ispis_select1->execute(array(
												':ss_skola_id' => $skola_id_hidden1x));
												
					while($row_skole_smjer_ispis_select1 = $skole_smjer_ispis_select1->fetch()){
						$skola_smjer_id2 = $row_skole_smjer_ispis_select1['ss_id'];
						$skola_smjer_naziv2 = $row_skole_smjer_ispis_select1['ss_naziv'];
						
						$provjera_skole_smjerovi_ND_ustanove = $db->prepare("
													SELECT *
													FROM idk_nd_ustanove_skole_smjerovi
													WHERE idd_skole_ustanove_nd = :idd_skole_ustanove_nd AND ss_idd = :ss_idd
													");

						$provjera_skole_smjerovi_ND_ustanove->execute(array(
													':idd_skole_ustanove_nd' => $skola_nd_id_hidden1x,
													':ss_idd' => $skola_smjer_id2
													));
						
						if (($provjera_skole_smjerovi_ND_ustanove->rowCount()) == 0){
							echo '<option value="'.$skola_smjer_id2.'">'.$skola_smjer_naziv2.'</option>';
						}
					}
				break;
				
				case "posalji_forme_za_unos_kontakata_ND":
					$broj_kontakt_formi = $_POST['broj_kontakata_odabrano'];
					$getSiteUrl = getSiteUrlr();
					for ($x = 1; $x <= $broj_kontakt_formi; $x++) {
						echo '
							<div class = "row">
								<div class="col-md-offset-2 col-sm-8">
									<div id = "form_kontakt_ustanove_izgled" style = "box-shadow: 0 0 10px #cccccc; margin-top: 10px; border: 1px solid #cccccc; border-radius: 1.25rem; padding-top: 10px;">
										<div class = "row" style = "border-bottom: 1px solid #cccccc; margin: 10px 0px; padding-bottom: 14px; font-weight: bold;">
											<div class="col-md-offset-2 col-sm-8">
												Kontakt br. '.$x.'
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-12">
												<label for="ime_kontakata_nd_ustanove_new'.$x.'" class="col-sm-5 control-label">
													<span class="text-danger">
														*
													</span>
													Ime kontakta:
												</label>
												<div class="col-sm-7">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="ime_kontakata_nd_ustanove_new'.$x.'" id="ime_kontakata_nd_ustanove_new'.$x.'" autocomplete="off" placeholder="Unesite ime kontakta" required>
														<span class="materail-input-block__line">
														</span>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-12">
												<label for="prezime_kontakata_nd_ustanove_new'.$x.'" class="col-sm-5 control-label">
													<span class="text-danger">
														*
													</span>
													Prezime kontakta:
												</label>
												<div class="col-sm-7">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="prezime_kontakata_nd_ustanove_new'.$x.'" id="prezime_kontakata_nd_ustanove_new'.$x.'" autocomplete="off" placeholder="Unesite prezime kontakta" required>
														<span class="materail-input-block__line">
														</span>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-12">
												<label for="mail_kontakata_nd_ustanove_new'.$x.'" class="col-sm-5 control-label">
													<span class="text-danger">
														*
													</span>
													E-mail kontakta:
												</label>
												<div class="col-sm-7">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="mail_kontakata_nd_ustanove_new'.$x.'" id="mail_kontakata_nd_ustanove_new'.$x.'" autocomplete="off" placeholder="Unesite e-mail kontakta" required>
														<span class="materail-input-block__line">
														</span>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-12">
												<label for="telefon_kontakata_nd_ustanove_new'.$x.'" class="col-sm-5 control-label">
													<span class="text-danger">
														*
													</span>
													Telefon:
												</label>
												<div class="col-sm-7">
													<div class="">
														<input class="form-control materail-input" type="tel" name="telefon_kontakata_nd_ustanove_new'.$x.'" id="telefon_kontakata_nd_ustanove_new'.$x.'" required>
													</div>
												</div>
											</div>
										</div>
										<script>
										$( document ).ready(function($) {
											$.each($("#telefon_kontakata_nd_ustanove_new'.$x.'"),function(){
												var telInput = document.getElementById("telefon_kontakata_nd_ustanove_new'.$x.'");
												if ($(this).val().startsWith("+") || $(this).val() == "") {
													iti'.$x.' = window.intlTelInput(telInput, {
														utilsScript:"'.$getSiteUrl.'buildTelInput/js/utils.js",
														autoPlaceholder: "aggressive",
														initialCountry: "de",
														formatOnDisplay: true,
														preferredCountries: ["de","rs","hr","ba"],
														separateDialCode: true
													});
												}
											});
											//SPAJANJE COUNTRY CODA I TELEFONSKOG BROJA
											$("form").submit(function(event) {
												//event.preventDefault();
												$.each($("#telefon_kontakata_nd_ustanove_new'.$x.'"),function(){
													
													$("#telefon_kontakata_nd_ustanove_new'.$x.'").val(iti'.$x.'.getNumber()); 
													
												});
											});	
										});
										</script>
										<div class="form-group">
											<div class="col-sm-12">
												<label for="zaduzenje_kontakata_nd_ustanove_new'.$x.'" class="col-sm-5 control-label">
													<span class="text-danger">
														*
													</span>
													Zadužen/a za:
												</label>
												<div class="col-sm-7">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="zaduzenje_kontakata_nd_ustanove_new'.$x.'" id="zaduzenje_kontakata_nd_ustanove_new'.$x.'" autocomplete="off" placeholder="Unesite zaduženja kontakta ustanove" required>
														<span class="materail-input-block__line">
														</span>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						';
					}
				break;
				
				case "posalji_forme_za_unos_tipova_dokumenata_ND_u":
					$broj_tip_formi_u = $_POST['broj_tipova_dokumenata_odabrano_U'];
					for ($x = 1; $x <= $broj_tip_formi_u; $x++) {
						echo '
							<div class = "row">
								<div class="col-md-offset-2 col-sm-8">
									<div id = "form_kontakt_ustanove_izgled" style = "box-shadow: 0 0 10px #cccccc; margin-top: 10px; border: 1px solid #cccccc; border-radius: 1.25rem; padding-top: 10px;">
										<div class = "row" style = "border-bottom: 1px solid #cccccc; margin: 10px 0px; padding-bottom: 14px; font-weight: bold;">
											<div class="col-md-offset-2 col-sm-8">
												Tip br.'.$x.'
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-12">
												<label for="naziv_tipa_dokumenta_nd_ustanove_new_u'.$x.'" class="col-sm-4 control-label">
													<span class="text-danger">
														*
													</span>
													Naziv tipa:
												</label>
												<div class="col-sm-8">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="naziv_tipa_dokumenta_nd_ustanove_new_u'.$x.'" id="naziv_tipa_dokumenta_nd_ustanove_new_u'.$x.'" autocomplete="off" placeholder="Unesite naziv tipa dopkumenta">
														<span class="materail-input-block__line">
														</span>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-12">
												<label for="obaveznost_tipa_dokumenta_nd_ustanove_new_u'.$x.'" class="col-sm-4 control-label"><span class="text-danger">*</span>Obaveznost:</label>
												<div class="col-sm-4 text-center">
													<label class="main-container__column material-radio-group material-radio-group_success" for="obaveznost_doc_DA_u'.$x.'">
														<input type="radio" name="obaveznost_tipa_dokumenta_nd_ustanove_new_u'.$x.'" id="obaveznost_doc_DA_u'.$x.'" class="material-radiobox" value="1">
														<span class="material-radio-group__element material-radio-group__check-radio"></span>
														<span class="material-radio-group__element material-radio-group__caption">DA</span>
													</label>
												</div>
												<div class="col-sm-4 text-center">
													<label class="main-container__column material-radio-group material-radio-group_danger" for="obaveznost_doc_NE_u'.$x.'">
														<input type="radio" name="obaveznost_tipa_dokumenta_nd_ustanove_new_u'.$x.'" id="obaveznost_doc_NE_u'.$x.'" class="material-radiobox" value="0">
														<span class="material-radio-group__element material-radio-group__check-radio"></span>
														<span class="material-radio-group__element material-radio-group__caption">NE</span>
													</label>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-12">
												<label for="template_tipa_dokumenta_nd_ustanove_new_u'.$x.'" class="col-sm-4 control-label"><span class="text-danger">*</span>Template:</label>
												<div class="col-sm-4 text-center">
													<label class="main-container__column material-radio-group material-radio-group_success" for="template_doc_DA_u'.$x.'">
														<input type="radio" name="template_tipa_dokumenta_nd_ustanove_new_u'.$x.'" id="template_doc_DA_u'.$x.'" class="material-radiobox" value="1">
														<span class="material-radio-group__element material-radio-group__check-radio"></span>
														<span class="material-radio-group__element material-radio-group__caption">DA</span>
													</label>
												</div>
												<div class="col-sm-4 text-center">
													<label class="main-container__column material-radio-group material-radio-group_danger" for="template_doc_NE_u'.$x.'">
														<input type="radio" name="template_tipa_dokumenta_nd_ustanove_new_u'.$x.'" id="template_doc_NE_u'.$x.'" class="material-radiobox" value="0">
														<span class="material-radio-group__element material-radio-group__check-radio"></span>
														<span class="material-radio-group__element material-radio-group__caption">NE</span>
													</label>
												</div>
											</div>
										</div>
										<script>
											$(document).ready(function() {
												$("#obaveznost_doc_DA_u'.$x.'").prop( "checked", false );
												$("#obaveznost_doc_NE_u'.$x.'").prop( "checked", false );
												$("#template_doc_DA_u'.$x.'").prop( "checked", false );
												$("#template_doc_NE_u'.$x.'").prop( "checked", false );
											});
										</script>
									</div>
								</div>
							</div>
						';
					}
				break;
				
				case "posalji_forme_za_unos_tipova_dokumenata_ND_s":
					$broj_tip_formi_s = $_POST['broj_tipova_dokumenata_odabrano_S'];
					for ($x = 1; $x <= $broj_tip_formi_s; $x++) {
						echo '
							<div class = "row">
								<div class="col-md-offset-2 col-sm-8">
									<div id = "form_kontakt_ustanove_izgled" style = "box-shadow: 0 0 10px #cccccc; margin-top: 10px; border: 1px solid #cccccc; border-radius: 1.25rem; padding-top: 10px;">
										<div class = "row" style = "border-bottom: 1px solid #cccccc; margin: 10px 0px; padding-bottom: 14px; font-weight: bold;">
											<div class="col-md-offset-2 col-sm-8">
												Tip br.'.$x.'
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-12">
												<label for="naziv_tipa_dokumenta_nd_ustanove_new_s'.$x.'" class="col-sm-4 control-label">
													<span class="text-danger">
														*
													</span>
													Naziv tipa:
												</label>
												<div class="col-sm-8">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="naziv_tipa_dokumenta_nd_ustanove_new_s'.$x.'" id="naziv_tipa_dokumenta_nd_ustanove_new_s'.$x.'" autocomplete="off" placeholder="Unesite naziv tipa dopkumenta">
														<span class="materail-input-block__line">
														</span>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-12">
												<label for="obaveznost_tipa_dokumenta_nd_ustanove_new_s'.$x.'" class="col-sm-4 control-label"><span class="text-danger">*</span>Obaveznost:</label>
												<div class="col-sm-4 text-center">
													<label class="main-container__column material-radio-group material-radio-group_success" for="obaveznost_doc_DA_s'.$x.'">
														<input type="radio" name="obaveznost_tipa_dokumenta_nd_ustanove_new_s'.$x.'" id="obaveznost_doc_DA_s'.$x.'" class="material-radiobox" value="1">
														<span class="material-radio-group__element material-radio-group__check-radio"></span>
														<span class="material-radio-group__element material-radio-group__caption">DA</span>
													</label>
												</div>
												<div class="col-sm-4 text-center">
													<label class="main-container__column material-radio-group material-radio-group_danger" for="obaveznost_doc_NE_s'.$x.'">
														<input type="radio" name="obaveznost_tipa_dokumenta_nd_ustanove_new_s'.$x.'" id="obaveznost_doc_NE_s'.$x.'" class="material-radiobox" value="0">
														<span class="material-radio-group__element material-radio-group__check-radio"></span>
														<span class="material-radio-group__element material-radio-group__caption">NE</span>
													</label>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-12">
												<label for="template_tipa_dokumenta_nd_ustanove_new_s'.$x.'" class="col-sm-4 control-label"><span class="text-danger">*</span>Template:</label>
												<div class="col-sm-4 text-center">
													<label class="main-container__column material-radio-group material-radio-group_success" for="template_doc_DA_s'.$x.'">
														<input type="radio" name="template_tipa_dokumenta_nd_ustanove_new_s'.$x.'" id="template_doc_DA_s'.$x.'" class="material-radiobox" value="1">
														<span class="material-radio-group__element material-radio-group__check-radio"></span>
														<span class="material-radio-group__element material-radio-group__caption">DA</span>
													</label>
												</div>
												<div class="col-sm-4 text-center">
													<label class="main-container__column material-radio-group material-radio-group_danger" for="template_doc_NE_s'.$x.'">
														<input type="radio" name="template_tipa_dokumenta_nd_ustanove_new_s'.$x.'" id="template_doc_NE_s'.$x.'" class="material-radiobox" value="0">
														<span class="material-radio-group__element material-radio-group__check-radio"></span>
														<span class="material-radio-group__element material-radio-group__caption">NE</span>
													</label>
												</div>
											</div>
										</div>
										<script>
											$(document).ready(function() {
												$("#obaveznost_doc_DA_s'.$x.'").prop( "checked", false );
												$("#obaveznost_doc_NE_s'.$x.'").prop( "checked", false );
												$("#template_doc_DA_s'.$x.'").prop( "checked", false );
												$("#template_doc_NE_s'.$x.'").prop( "checked", false );
											});
										</script>
									</div>
								</div>
							</div>
						';
					}
				break;
				
				case "posalji_forme_za_unos_tipova_dokumenata_ND_ss":
					$broj_tip_formi_ss = $_POST['broj_tipova_dokumenata_odabrano_SS'];
					for ($x = 1; $x <= $broj_tip_formi_ss; $x++) {
						echo '
							<div class = "row">
								<div class="col-md-offset-2 col-sm-8">
									<div id = "form_kontakt_ustanove_izgled" style = "box-shadow: 0 0 10px #cccccc; margin-top: 10px; border: 1px solid #cccccc; border-radius: 1.25rem; padding-top: 10px;">
										<div class = "row" style = "border-bottom: 1px solid #cccccc; margin: 10px 0px; padding-bottom: 14px; font-weight: bold;">
											<div class="col-md-offset-2 col-sm-8">
												Tip br.'.$x.'
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-12">
												<label for="naziv_tipa_dokumenta_nd_ustanove_new_ss'.$x.'" class="col-sm-4 control-label">
													<span class="text-danger">
														*
													</span>
													Naziv tipa:
												</label>
												<div class="col-sm-8">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="naziv_tipa_dokumenta_nd_ustanove_new_ss'.$x.'" id="naziv_tipa_dokumenta_nd_ustanove_new_ss'.$x.'" autocomplete="off" placeholder="Unesite naziv tipa dopkumenta">
														<span class="materail-input-block__line">
														</span>
													</div>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-12">
												<label for="obaveznost_tipa_dokumenta_nd_ustanove_new_ss'.$x.'" class="col-sm-4 control-label"><span class="text-danger">*</span>Obaveznost:</label>
												<div class="col-sm-4 text-center">
													<label class="main-container__column material-radio-group material-radio-group_success" for="obaveznost_doc_DA_ss'.$x.'">
														<input type="radio" name="obaveznost_tipa_dokumenta_nd_ustanove_new_ss'.$x.'" id="obaveznost_doc_DA_ss'.$x.'" class="material-radiobox" value="1">
														<span class="material-radio-group__element material-radio-group__check-radio"></span>
														<span class="material-radio-group__element material-radio-group__caption">DA</span>
													</label>
												</div>
												<div class="col-sm-4 text-center">
													<label class="main-container__column material-radio-group material-radio-group_danger" for="obaveznost_doc_NE_ss'.$x.'">
														<input type="radio" name="obaveznost_tipa_dokumenta_nd_ustanove_new_ss'.$x.'" id="obaveznost_doc_NE_ss'.$x.'" class="material-radiobox" value="0">
														<span class="material-radio-group__element material-radio-group__check-radio"></span>
														<span class="material-radio-group__element material-radio-group__caption">NE</span>
													</label>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-sm-12">
												<label for="template_tipa_dokumenta_nd_ustanove_new_ss'.$x.'" class="col-sm-4 control-label"><span class="text-danger">*</span>Template:</label>
												<div class="col-sm-4 text-center">
													<label class="main-container__column material-radio-group material-radio-group_success" for="template_doc_DA_ss'.$x.'">
														<input type="radio" name="template_tipa_dokumenta_nd_ustanove_new_ss'.$x.'" id="template_doc_DA_ss'.$x.'" class="material-radiobox" value="1">
														<span class="material-radio-group__element material-radio-group__check-radio"></span>
														<span class="material-radio-group__element material-radio-group__caption">DA</span>
													</label>
												</div>
												<div class="col-sm-4 text-center">
													<label class="main-container__column material-radio-group material-radio-group_danger" for="template_doc_NE_ss'.$x.'">
														<input type="radio" name="template_tipa_dokumenta_nd_ustanove_new_ss'.$x.'" id="template_doc_NE_ss'.$x.'" class="material-radiobox" value="0">
														<span class="material-radio-group__element material-radio-group__check-radio"></span>
														<span class="material-radio-group__element material-radio-group__caption">NE</span>
													</label>
												</div>
											</div>
										</div>
										<script>
											$(document).ready(function() {
												$("#obaveznost_doc_DA_ss'.$x.'").prop( "checked", false );
												$("#obaveznost_doc_NE_ss'.$x.'").prop( "checked", false );
												$("#template_doc_DA_ss'.$x.'").prop( "checked", false );
												$("#template_doc_NE_ss'.$x.'").prop( "checked", false );
											});
										</script>
									</div>
								</div>
							</div>
						';
					}
				break;
				
				case "posalji_pregled_zaposlenika_DIPL":
					//111Adis222 12
					$id_zaposlenika_pregled1 = intval($_POST['id_zaposlenika_pregled']);
					if($id_zaposlenika_pregled1 != NULL){
						$zaposlenik_id = $id_zaposlenika_pregled1;
						//Info zaposlenik
						$zaposleni_ispis_1 = $db->prepare("
												SELECT employee_firstname, employee_lastname, employee_image
												FROM idk_employees
												WHERE employee_nostrifikacija_diploma IN (0,1) AND employee_id = :employee_id
												");
						$zaposleni_ispis_1->execute(array(
												':employee_id' => $zaposlenik_id
												));
						$zaposleni_ispis_1_row = $zaposleni_ispis_1->fetch();
						$ime_zap_ispis2 = $zaposleni_ispis_1_row['employee_firstname'];
						$prezime_zap_ispis2 = $zaposleni_ispis_1_row['employee_lastname'];
						$image_zap_ispis2 = $zaposleni_ispis_1_row['employee_image'];
						//Ukupan broj od tog zaposlenika
						$ukupan_br_ispis1 = $db->prepare("
												SELECT id_broj_nd_kandidata
												FROM idk_nd_kandidata
												WHERE zaduzen_zaposlenik_nd_kandidata = :zaduzen_zaposlenik_nd_kandidata
												");
						$ukupan_br_ispis1->execute(array(
												':zaduzen_zaposlenik_nd_kandidata' => $zaposlenik_id
												));
						$ukupan_br_ispis2 = $ukupan_br_ispis1->rowCount();
						//Ukupan broj od tog zaposlenika sa statusom NOVI
						$uk_br_st11_ispis1 = $db->prepare("
												SELECT id_broj_nd_kandidata
												FROM idk_nd_kandidata
												WHERE zaduzen_zaposlenik_nd_kandidata = :zaduzen_zaposlenik_nd_kandidata AND status_nd_kandidata = :status_nd_kandidata AND pstatus_nd_kandidata = :pstatus_nd_kandidata
												");
						$uk_br_st11_ispis1->execute(array(
												':zaduzen_zaposlenik_nd_kandidata' => $zaposlenik_id,
												':status_nd_kandidata' => 1,
												':pstatus_nd_kandidata' => 1
												));
						$uk_br_st11_ispis2 = $uk_br_st11_ispis1->rowCount();
						
						$uk_br_st12_ispis1 = $db->prepare("
												SELECT id_broj_nd_kandidata
												FROM idk_nd_kandidata
												WHERE zaduzen_zaposlenik_nd_kandidata = :zaduzen_zaposlenik_nd_kandidata AND status_nd_kandidata = :status_nd_kandidata AND pstatus_nd_kandidata = :pstatus_nd_kandidata
												");
						$uk_br_st12_ispis1->execute(array(
												':zaduzen_zaposlenik_nd_kandidata' => $zaposlenik_id,
												':status_nd_kandidata' => 1,
												':pstatus_nd_kandidata' => 2
												));
						$uk_br_st12_ispis2 = $uk_br_st12_ispis1->rowCount();
						
						$uk_br_st16_ispis1 = $db->prepare("
												SELECT id_broj_nd_kandidata
												FROM idk_nd_kandidata
												WHERE zaduzen_zaposlenik_nd_kandidata = :zaduzen_zaposlenik_nd_kandidata AND status_nd_kandidata = :status_nd_kandidata AND pstatus_nd_kandidata = :pstatus_nd_kandidata
												");
						$uk_br_st16_ispis1->execute(array(
												':zaduzen_zaposlenik_nd_kandidata' => $zaposlenik_id,
												':status_nd_kandidata' => 1,
												':pstatus_nd_kandidata' => 6
												));
						$uk_br_st16_ispis2 = $uk_br_st16_ispis1->rowCount();
						
						$uk_br_st13_ispis1 = $db->prepare("
												SELECT id_broj_nd_kandidata
												FROM idk_nd_kandidata
												WHERE zaduzen_zaposlenik_nd_kandidata = :zaduzen_zaposlenik_nd_kandidata AND status_nd_kandidata = :status_nd_kandidata AND pstatus_nd_kandidata = :pstatus_nd_kandidata
												");
						$uk_br_st13_ispis1->execute(array(
												':zaduzen_zaposlenik_nd_kandidata' => $zaposlenik_id,
												':status_nd_kandidata' => 1,
												':pstatus_nd_kandidata' => 3
												));
						$uk_br_st13_ispis2 = $uk_br_st13_ispis1->rowCount();
						
						$uk_br_st14_ispis1 = $db->prepare("
												SELECT id_broj_nd_kandidata
												FROM idk_nd_kandidata
												WHERE zaduzen_zaposlenik_nd_kandidata = :zaduzen_zaposlenik_nd_kandidata AND status_nd_kandidata = :status_nd_kandidata AND pstatus_nd_kandidata = :pstatus_nd_kandidata
												");
						$uk_br_st14_ispis1->execute(array(
												':zaduzen_zaposlenik_nd_kandidata' => $zaposlenik_id,
												':status_nd_kandidata' => 1,
												':pstatus_nd_kandidata' => 4
												));
						$uk_br_st14_ispis2 = $uk_br_st14_ispis1->rowCount();
						
						$uk_br_st15_ispis1 = $db->prepare("
												SELECT id_broj_nd_kandidata
												FROM idk_nd_kandidata
												WHERE zaduzen_zaposlenik_nd_kandidata = :zaduzen_zaposlenik_nd_kandidata AND status_nd_kandidata = :status_nd_kandidata AND pstatus_nd_kandidata = :pstatus_nd_kandidata
												");
						$uk_br_st15_ispis1->execute(array(
												':zaduzen_zaposlenik_nd_kandidata' => $zaposlenik_id,
												':status_nd_kandidata' => 1,
												':pstatus_nd_kandidata' => 5
												));
						$uk_br_st15_ispis2 = $uk_br_st15_ispis1->rowCount();
						//111Adis222 START
						$uk_br_st17_ispis1 = $db->prepare("
												SELECT id_broj_nd_kandidata
												FROM idk_nd_kandidata
												WHERE zaduzen_zaposlenik_nd_kandidata = :zaduzen_zaposlenik_nd_kandidata AND status_nd_kandidata = :status_nd_kandidata AND pstatus_nd_kandidata = :pstatus_nd_kandidata
												");
						$uk_br_st17_ispis1->execute(array(
												':zaduzen_zaposlenik_nd_kandidata' => $zaposlenik_id,
												':status_nd_kandidata' => 1,
												':pstatus_nd_kandidata' => 7
												));
						$uk_br_st17_ispis2 = $uk_br_st17_ispis1->rowCount();
						
						$uk_br_st18_ispis1 = $db->prepare("
												SELECT id_broj_nd_kandidata
												FROM idk_nd_kandidata
												WHERE zaduzen_zaposlenik_nd_kandidata = :zaduzen_zaposlenik_nd_kandidata AND status_nd_kandidata = :status_nd_kandidata AND pstatus_nd_kandidata = :pstatus_nd_kandidata
												");
						$uk_br_st18_ispis1->execute(array(
												':zaduzen_zaposlenik_nd_kandidata' => $zaposlenik_id,
												':status_nd_kandidata' => 1,
												':pstatus_nd_kandidata' => 8
												));
						$uk_br_st18_ispis2 = $uk_br_st18_ispis1->rowCount();
						
						$uk_br_st19_ispis1 = $db->prepare("
												SELECT id_broj_nd_kandidata
												FROM idk_nd_kandidata
												WHERE zaduzen_zaposlenik_nd_kandidata = :zaduzen_zaposlenik_nd_kandidata AND status_nd_kandidata = :status_nd_kandidata AND pstatus_nd_kandidata = :pstatus_nd_kandidata
												");
						$uk_br_st19_ispis1->execute(array(
												':zaduzen_zaposlenik_nd_kandidata' => $zaposlenik_id,
												':status_nd_kandidata' => 1,
												':pstatus_nd_kandidata' => 9
												));
						$uk_br_st19_ispis2 = $uk_br_st19_ispis1->rowCount();
						
						$uk_br_st20_ispis1 = $db->prepare("
												SELECT id_broj_nd_kandidata
												FROM idk_nd_kandidata
												WHERE zaduzen_zaposlenik_nd_kandidata = :zaduzen_zaposlenik_nd_kandidata AND status_nd_kandidata = :status_nd_kandidata AND pstatus_nd_kandidata = :pstatus_nd_kandidata
												");
						$uk_br_st20_ispis1->execute(array(
												':zaduzen_zaposlenik_nd_kandidata' => $zaposlenik_id,
												':status_nd_kandidata' => 1,
												':pstatus_nd_kandidata' => 10
												));
						$uk_br_st20_ispis2 = $uk_br_st20_ispis1->rowCount();
						
						$uk_br_st21_ispis1 = $db->prepare("
												SELECT id_broj_nd_kandidata
												FROM idk_nd_kandidata
												WHERE zaduzen_zaposlenik_nd_kandidata = :zaduzen_zaposlenik_nd_kandidata AND status_nd_kandidata = :status_nd_kandidata AND pstatus_nd_kandidata = :pstatus_nd_kandidata
												");
						$uk_br_st21_ispis1->execute(array(
												':zaduzen_zaposlenik_nd_kandidata' => $zaposlenik_id,
												':status_nd_kandidata' => 1,
												':pstatus_nd_kandidata' => 11
												));
						$uk_br_st21_ispis2 = $uk_br_st21_ispis1->rowCount();
						
						$uk_br_st22_ispis1 = $db->prepare("
												SELECT id_broj_nd_kandidata
												FROM idk_nd_kandidata
												WHERE zaduzen_zaposlenik_nd_kandidata = :zaduzen_zaposlenik_nd_kandidata AND status_nd_kandidata = :status_nd_kandidata AND pstatus_nd_kandidata = :pstatus_nd_kandidata
												");
						$uk_br_st22_ispis1->execute(array(
												':zaduzen_zaposlenik_nd_kandidata' => $zaposlenik_id,
												':status_nd_kandidata' => 1,
												':pstatus_nd_kandidata' => 12
												));
						$uk_br_st22_ispis2 = $uk_br_st22_ispis1->rowCount();
						//111Adis222 END 
						//Ukupan broj od tog zaposlenika sa statusom Prikupljanje dokumentacije
						$uk_br_st2_ispis1 = $db->prepare("
												SELECT id_broj_nd_kandidata
												FROM idk_nd_kandidata
												WHERE zaduzen_zaposlenik_nd_kandidata = :zaduzen_zaposlenik_nd_kandidata AND status_nd_kandidata = :status_nd_kandidata
												");
						$uk_br_st2_ispis1->execute(array(
												':zaduzen_zaposlenik_nd_kandidata' => $zaposlenik_id,
												':status_nd_kandidata' => 2
												));
						$uk_br_st2_ispis2 = $uk_br_st2_ispis1->rowCount();
						//Ukupan broj od tog zaposlenika sa statusom Poslana pošta
						$uk_br_st3_ispis1 = $db->prepare("
												SELECT id_broj_nd_kandidata
												FROM idk_nd_kandidata
												WHERE zaduzen_zaposlenik_nd_kandidata = :zaduzen_zaposlenik_nd_kandidata AND status_nd_kandidata = :status_nd_kandidata
												");
						$uk_br_st3_ispis1->execute(array(
												':zaduzen_zaposlenik_nd_kandidata' => $zaposlenik_id,
												':status_nd_kandidata' => 3
												));
						$uk_br_st3_ispis2 = $uk_br_st3_ispis1->rowCount();
						//Ukupan broj od tog zaposlenika sa statusom U obradi
						$uk_br_st4_ispis1 = $db->prepare("
												SELECT id_broj_nd_kandidata
												FROM idk_nd_kandidata
												WHERE zaduzen_zaposlenik_nd_kandidata = :zaduzen_zaposlenik_nd_kandidata AND status_nd_kandidata = :status_nd_kandidata
												");
						$uk_br_st4_ispis1->execute(array(
												':zaduzen_zaposlenik_nd_kandidata' => $zaposlenik_id,
												':status_nd_kandidata' => 4
												));
						$uk_br_st4_ispis2 = $uk_br_st4_ispis1->rowCount();
						//Ukupan broj od tog zaposlenika sa statusom Dopuna dokumenata
						$uk_br_st5_ispis1 = $db->prepare("
												SELECT id_broj_nd_kandidata
												FROM idk_nd_kandidata
												WHERE zaduzen_zaposlenik_nd_kandidata = :zaduzen_zaposlenik_nd_kandidata AND status_nd_kandidata = :status_nd_kandidata
												");
						$uk_br_st5_ispis1->execute(array(
												':zaduzen_zaposlenik_nd_kandidata' => $zaposlenik_id,
												':status_nd_kandidata' => 5
												));
						$uk_br_st5_ispis2 = $uk_br_st5_ispis1->rowCount();
						//Ukupan broj od tog zaposlenika sa statusom Zavrsen
						$uk_br_st6_ispis1 = $db->prepare("
												SELECT id_broj_nd_kandidata
												FROM idk_nd_kandidata
												WHERE zaduzen_zaposlenik_nd_kandidata = :zaduzen_zaposlenik_nd_kandidata AND status_nd_kandidata = :status_nd_kandidata
												");
						$uk_br_st6_ispis1->execute(array(
												':zaduzen_zaposlenik_nd_kandidata' => $zaposlenik_id,
												':status_nd_kandidata' => 6
												));
						$uk_br_st6_ispis2 = $uk_br_st6_ispis1->rowCount();
						//Ukupan broj od tog zaposlenika sa statusom Arhiviran
						$uk_br_st7_ispis1 = $db->prepare("
												SELECT id_broj_nd_kandidata
												FROM idk_nd_kandidata
												WHERE zaduzen_zaposlenik_nd_kandidata = :zaduzen_zaposlenik_nd_kandidata AND status_nd_kandidata = :status_nd_kandidata
												");
						$uk_br_st7_ispis1->execute(array(
												':zaduzen_zaposlenik_nd_kandidata' => $zaposlenik_id,
												':status_nd_kandidata' => 7
												));
						$uk_br_st7_ispis2 = $uk_br_st7_ispis1->rowCount();
						
						//Ukupan broj kandidata koje je sistem dodjelio tom zaposleniku
						$uk_br_sistem = $db->prepare("
												SELECT id_stat
												FROM idk_nd_menadzeri_statistike
												WHERE zaduzen_zaposlenik_id = :zaduzen_zaposlenik_id AND vrsta_aktivnosti = :vrsta_aktivnosti
												");
						$uk_br_sistem->execute(array(
												':zaduzen_zaposlenik_id' => $zaposlenik_id,
												':vrsta_aktivnosti' => 0
												));
						$uk_br_sistem2 = $uk_br_sistem->rowCount();
						//Ukupan broj kandidata koje je drugi korisnik dodjelio tom zaposleniku
						$uk_br_zaposleni = $db->prepare("
												SELECT id_stat
												FROM idk_nd_menadzeri_statistike
												WHERE zaduzen_zaposlenik_id = :zaduzen_zaposlenik_id AND vrsta_aktivnosti = :vrsta_aktivnosti
												");
						$uk_br_zaposleni->execute(array(
												':zaduzen_zaposlenik_id' => $zaposlenik_id,
												':vrsta_aktivnosti' => 1
												));
						$uk_br_zaposleni2 = $uk_br_zaposleni->rowCount();
						//Ukupan broj kandidata koje je zaposlenik prebacio drugom zaposleniku
						$uk_br_zaposleni_pr = $db->prepare("
												SELECT id_stat
												FROM idk_nd_menadzeri_statistike
												WHERE prethodni_zaposlenik_id = :prethodni_zaposlenik_id AND vrsta_aktivnosti = :vrsta_aktivnosti
												");
						$uk_br_zaposleni_pr->execute(array(
												':prethodni_zaposlenik_id' => $zaposlenik_id,
												':vrsta_aktivnosti' => 1
												));
						$uk_br_zaposleni_pr2 = $uk_br_zaposleni_pr->rowCount();
						
						echo
							'
								<style>
									.fadeInUp {
										-webkit-animation-name: fadeInUp;
										animation-name: fadeInUp;
										-webkit-animation-duration: 1s;
										animation-duration: 1s;
										-webkit-animation-fill-mode: both;
										animation-fill-mode: both;
									}
									@-webkit-keyframes fadeInUp {
										0% {
											opacity: 0;
											-webkit-transform: translate3d(0, 100%, 0);
											transform: translate3d(0, 100%, 0);
										}
										100% {
											opacity: 1;
											-webkit-transform: none;
											transform: none;
										}
									}
									@keyframes fadeInUp {
										0% {
											opacity: 0;
											-webkit-transform: translate3d(0, 100%, 0);
											transform: translate3d(0, 100%, 0);
										}
										100% {
											opacity: 1;
											-webkit-transform: none;
											transform: none;
										}
									} 
									#card_zaposlenik_DIPL{
										border: 1px solid #cccccc; margin: 10px 40px; padding: 20px; border-radius: 1.25rem; box-shadow: 0 0 10px #cccccc;
									}
									.fancybox img{ width: 80px !important; height: 80px !important; border: 3px solid #ffffff; float: none !important;
									}
								</style>
								<div id = "card_zaposlenik_DIPL" class="row fadeInUp">
									<div class = "col-xs-12">
										<div class="row" style = "padding: 10px; background-color: #6097a0; border-radius: 1.25rem 1.25rem 0 0;"> 
											<div class = "col-xs-12 text-center">
												<a class="fancybox" rel="group" href="'.getSiteUrlr().'files/employees/'.$image_zap_ispis2.'"><img class="idk_profile_img" src="'.getSiteUrlr().'files/employees/'.$image_zap_ispis2.'"></a>
											</div> 
											<div class = "col-xs-12 text-center" style = "font-weight: bold; font-size: x-large; color: white; word-break: break-all;"> 
												'.$ime_zap_ispis2.' '.$prezime_zap_ispis2.'
											</div>
										</div>
										<div class="row" style = "padding-top: 10px;">
											<div class = "col-xs-12">
												<div class="table-responsive">
													<table class="table table-striped" style = "margin-bottom: 0px;">
														<thead>
															<tr>
																<th class="text-center">Status</th>
																<th class="text-center">Broj kandidata</th>
															</tr>
														</thead>
														<tbody>
															<tr>
																<td class="text-center">'.getStatusDIPLKandidatR(1, 1).'</td>
																<td class="text-center" style = "font-weight: bold;">'.$uk_br_st11_ispis2.'</td>
															</tr>
															<tr>
																<td class="text-center">'.getStatusDIPLKandidatR(1, 6).'</td>
																<td class="text-center" style = "font-weight: bold;">'.$uk_br_st16_ispis2.'</td>
															</tr>
															<tr>
																<td class="text-center">'.getStatusDIPLKandidatR(1, 2).'</td>
																<td class="text-center" style = "font-weight: bold;">'.$uk_br_st12_ispis2.'</td>
															</tr>
															<tr>
																<td class="text-center">'.getStatusDIPLKandidatR(1, 3).'</td>
																<td class="text-center" style = "font-weight: bold;">'.$uk_br_st13_ispis2.'</td>
															</tr>
															<tr>
																<td class="text-center">'.getStatusDIPLKandidatR(1, 4).'</td>
																<td class="text-center" style = "font-weight: bold;">'.$uk_br_st14_ispis2.'</td>
															</tr>
															<tr>
																<td class="text-center">'.getStatusDIPLKandidatR(1, 5).'</td>
																<td class="text-center" style = "font-weight: bold;">'.$uk_br_st15_ispis2.'</td>
															</tr>
															<tr>
																<td class="text-center">'.getStatusDIPLKandidatR(1, 7).'</td>
																<td class="text-center" style = "font-weight: bold;">'.$uk_br_st17_ispis2.'</td>
															</tr>
															<tr>
																<td class="text-center">'.getStatusDIPLKandidatR(1, 8).'</td>
																<td class="text-center" style = "font-weight: bold;">'.$uk_br_st18_ispis2.'</td>
															</tr>
															<tr>
																<td class="text-center">'.getStatusDIPLKandidatR(1, 9).'</td>
																<td class="text-center" style = "font-weight: bold;">'.$uk_br_st19_ispis2.'</td>
															</tr>
															<tr>
																<td class="text-center">'.getStatusDIPLKandidatR(1, 10).'</td>
																<td class="text-center" style = "font-weight: bold;">'.$uk_br_st20_ispis2.'</td>
															</tr>
															<tr>
																<td class="text-center">'.getStatusDIPLKandidatR(1, 11).'</td>
																<td class="text-center" style = "font-weight: bold;">'.$uk_br_st21_ispis2.'</td>
															</tr>
															<tr>
																<td class="text-center">'.getStatusDIPLKandidatR(1, 12).'</td>
																<td class="text-center" style = "font-weight: bold;">'.$uk_br_st22_ispis2.'</td>
															</tr>
															<tr>
																<td class="text-center">'.getStatusDIPLKandidatR(2, 1).'</td>
																<td class="text-center" style = "font-weight: bold;">'.$uk_br_st2_ispis2.'</td>
															</tr>
															<tr>
																<td class="text-center">'.getStatusDIPLKandidatR(3, 1).'</td>
																<td class="text-center" style = "font-weight: bold;">'.$uk_br_st3_ispis2.'</td>
															</tr>
															<tr>
																<td class="text-center">'.getStatusDIPLKandidatR(4, 1).'</td>
																<td class="text-center" style = "font-weight: bold;">'.$uk_br_st4_ispis2.'</td>
															</tr>
															<tr>
																<td class="text-center">'.getStatusDIPLKandidatR(5, 1).'</td>
																<td class="text-center" style = "font-weight: bold;">'.$uk_br_st5_ispis2.'</td>
															</tr>
															<tr>
																<td class="text-center">'.getStatusDIPLKandidatR(6, 1).'</td>
																<td class="text-center" style = "font-weight: bold;">'.$uk_br_st6_ispis2.'</td>
															</tr>
															<tr>
																<td class="text-center">'.getStatusDIPLKandidatR(7, 1).'</td>
																<td class="text-center" style = "font-weight: bold;">'.$uk_br_st7_ispis2.'</td>
															</tr>
															<tr>
																<td class="text-left" colspan="2" style = "font-weight: bold;">Menadžera je zadužio:</td>
															</tr>
															<tr>
																<td class="text-center"><span style = "background-color: #005015; color: white" class="label label-default material-label material-label_default main-container__column text-left">Sistem</span></td>
																<td class="text-center" style = "font-weight: bold;">'.$uk_br_sistem2.'</td>
															</tr>
															<tr>
																<td class="text-center"><span style = "background-color: #005015; color: white" class="label label-default material-label material-label_default main-container__column text-left">Zaposlenik</span></td>
																<td class="text-center" style = "font-weight: bold;">'.$uk_br_zaposleni2.'</td>
															</tr>
															<tr>
																<td class="text-left" colspan="2" style = "font-weight: bold;">Menadžer je:</td>
															</tr>
															<tr>
																<td class="text-center"><span style = "background-color: #005015; color: white" class="label label-default material-label material-label_default main-container__column text-left">Prebacio zaposleniku</span></td>
																<td class="text-center" style = "font-weight: bold;">'.$uk_br_zaposleni_pr2.'</td>
															</tr>
															<tr style = "background-color: #6097a0; color: white;">
																<td class="text-right" style = "font-weight: bold;">Ukupan broj kandidata:</td>
																<td class="text-center" style = "font-weight: bold;">'.$ukupan_br_ispis2.'</td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>
									</div>
								</div>
							';
					}
				break;
				
				case "posalji_tipove_dokumenta_zanimanja":
					$skola_id_filter1 = $_POST['skola_id_filter'];
					$ustanova_id_filter1 = $_POST['ustanova_id_filter'];
					
					$brojac = 0;
					$tipovi_dokumenata_ispis_S = $db->prepare("
														SELECT *
														FROM idk_nd_ustanove_tip_dokumenta 
														WHERE idd_ustanove_nd = :idd_ustanove_nd AND idd_skole_ustanove_nd = :idd_skole_ustanove_nd AND idd_skole_smjer_ustanove_nd is null AND povezanost_dokumenta_ustanove_nd = :povezanost_dokumenta_ustanove_nd
													");
					$tipovi_dokumenata_ispis_S->execute(array(
													':idd_ustanove_nd' => $ustanova_id_filter1,
													':idd_skole_ustanove_nd' => $skola_id_filter1,
													':povezanost_dokumenta_ustanove_nd' => 2
													));
					if (($tipovi_dokumenata_ispis_S->rowCount()) != 0){
						while($tipovi_dokumenata_ispis_S_row = $tipovi_dokumenata_ispis_S->fetch()){
							$brojac++;
							$idtipadokumenta = $tipovi_dokumenata_ispis_S_row['id_tip_dokumenta_ustanove_nd'];
							$nazivtipadokumenta = $tipovi_dokumenata_ispis_S_row['naziv_tip_dokumenta_ustanove_nd'];
							$obaveznostdokumenta = $tipovi_dokumenata_ispis_S_row['obaveznost_dokumenta_ustanove_nd'];
							$templatedokumenta = $tipovi_dokumenata_ispis_S_row['template_dokumenta_ustanove_nd'];
							$templatenazivdokumenta = $tipovi_dokumenata_ispis_S_row['template_naziv_dokumenta_ustanove_nd'];
							$templatenazivdokumenta1 = $tipovi_dokumenata_ispis_S_row['template_naziv_dokumenta_ustanove_nd'];
							
							if($templatenazivdokumenta1 != NULL){
								if (strpos($templatenazivdokumenta1, '.jpg') !== false OR strpos($templatenazivdokumenta1, '.png') !== false){
									$templatenazivdokumenta1icon = '<i class="fa fa-file-image-o fa-3x" aria-hidden="true"></i>';
								}
								else if(strpos($templatenazivdokumenta1, '.pdf') !== false){
									$templatenazivdokumenta1icon = '<i class="fa fa-file-pdf-o fa-3x" aria-hidden="true"></i>';
								}
								else if(strpos($templatenazivdokumenta1, '.doc') !== false OR strpos($templatenazivdokumenta1, '.docx') !== false){
									$templatenazivdokumenta1icon = '<i class="fa fa-file-word-o fa-3x" aria-hidden="true"></i>';
								}
								else if(strpos($templatenazivdokumenta1, '.xls') !== false OR strpos($templatenazivdokumenta1, '.xlsx') !== false OR strpos($templatenazivdokumenta1, '.csv') !== false){
									$templatenazivdokumenta1icon = '<i class="fa fa-file-excel-o fa-3x" aria-hidden="true"></i>';
								}
								else if(strpos($templatenazivdokumenta1, '.txt') !== false ){
									$templatenazivdokumenta1icon = '<i class="fa fa-file-text-o fa-3x" aria-hidden="true"></i>';
								}
								else{
									$templatenazivdokumenta1icon = '<i class="fa fa-file-powerpoint-o fa-3x" aria-hidden="true"></i>';
								}
								
								$templatenazivdokumenta1download = '<a href="'.getSiteUrlr().'files/dokumenti_ND_ustanove/'.$templatenazivdokumenta1.'" class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK">'.$templatenazivdokumenta1icon.'</a>';
							}
							
							if($obaveznostdokumenta == 1){
								$obaveznostdokumentaprikaz = '<span style = "padding: 1px 16px;" class="material-label material-label_success main-container__column text-center">DA</span>';
							}else{
								$obaveznostdokumentaprikaz = '<span style = "padding: 1px 16px;" class="material-label material-label_warning main-container__column text-center">NE</span>';
							}
							
							if($templatedokumenta == 1){
								$templatedokumentaprikaz = '<span style = "padding: 1px 16px;" class="material-label material-label_success main-container__column text-center">DA</span>';
								if($templatenazivdokumenta == NULL){
									$templatenazivdokumentaprikaz = '<span style = "padding: 1px 16px;" class="material-label material-label_danger main-container__column text-center">Nije dodan template</span>';
								}else{
									$templatenazivdokumentaprikaz = $templatenazivdokumenta1download;
								}
							}else{
								$templatedokumentaprikaz = '<span style = "padding: 1px 16px;" class="material-label material-label_warning main-container__column text-center">NE</span>';
							}
							$brisanje_dokumenta_opcija = '<a href="#" data="'.getSiteUrlr().'nostrifikacija_diploma.php?page=brisanje_tipova_dokumenata_ND_ustanoveS&id='.$idtipadokumenta.'&ustanova_id='.$ustanova_id_filter1.'" data-toggle="modal" data-target="#deleteDocument_skola_ND" class="delete_document_skola btn material-btn material-btn_danger main-container__column" style = "padding: 2px 16px;" title = "Brisanje tipa dokumenta."><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i></a>';
							if($templatedokumenta == 1){
								echo '
								<div style = "border: 1px solid #cccccc; padding: 15px; border-radius: 1.25rem; background-color: #ffffff; margin-bottom: 10px; box-shadow: 0 0 15px #333333ad;">
									<div class = "row" style = "margin-bottom: 10px; font-weight: bold; border-bottom: 1px solid #cccccc; padding-bottom: 10px;">
										<div class = "col-xs-6 text-left">
											Tip br. '.$brojac.' 
										</div>
										<div class = "col-xs-6 text-right">
											'.$brisanje_dokumenta_opcija.'
										</div> 
									</div>
									<div class="row" style = "margin-top: 5px;">
										<div style = "font-weight: bold;" class = "col-xs-3 text-right">
											Naziv:
										</div>
										<div class = "col-xs-9 text-left">
											'.$nazivtipadokumenta.'
										</div>
									</div>
									<div class="row" style = "margin-top: 5px;">
										<div style = "font-weight: bold;" class = "col-xs-3 text-right">
											Obavezan:
										</div>
										<div class = "col-xs-9 text-left">
											'.$obaveznostdokumentaprikaz.'
										</div>
									</div>
									<div class="row" style = "margin-top: 5px;">
										<div style = "font-weight: bold;" class = "col-xs-3 text-right">
											Template:
										</div>
										<div class = "col-xs-9 text-left">
											'.$templatedokumentaprikaz.'
										</div>
									</div>
									<div class="row" style = "margin-top: 5px;">
										<div style = "font-weight: bold;" class = "col-xs-3 text-right">
											Preuzmi:
										</div>
										<div class = "col-xs-9 text-left">
											'.$templatenazivdokumentaprikaz.'
										</div>
									</div>
								</div>';
							}else{
								echo '
								<div style = "border: 1px solid #cccccc; padding: 15px; border-radius: 1.25rem; background-color: #ffffff; margin-bottom: 10px; box-shadow: 0 0 15px #333333ad;">
									<div class = "row" style = "margin-bottom: 10px; font-weight: bold; border-bottom: 1px solid #cccccc; padding-bottom: 10px;">
										<div class = "col-xs-6 text-left">
											Tip br. '.$brojac.' 
										</div>
										<div class = "col-xs-6 text-right">
											'.$brisanje_dokumenta_opcija.'
										</div> 
									</div>
									<div class="row" style = "margin-top: 5px;">
										<div style = "font-weight: bold;" class = "col-xs-3 text-right">
											Naziv:
										</div>
										<div class = "col-xs-9 text-left">
											'.$nazivtipadokumenta.'
										</div>
									</div>
									<div class="row" style = "margin-top: 5px;">
										<div style = "font-weight: bold;" class = "col-xs-3 text-right">
											Obavezan:
										</div>
										<div class = "col-xs-9 text-left">
											'.$obaveznostdokumentaprikaz.'
										</div>
									</div>
									<div class="row" style = "margin-top: 5px;">
										<div style = "font-weight: bold;" class = "col-xs-3 text-right">
											Template:
										</div>
										<div class = "col-xs-9 text-left">
											'.$templatedokumentaprikaz.'
										</div>
									</div>
								</div>';
							}
						}
						echo '<script>
								$(".delete_document_skola").click(function () {
									var addressValue = $(this).attr("data");
									document.getElementById("delete_document_skola_link").href = addressValue;
								});
							</script>
							<!-- Modal -->
							<div class="modal material-modal material-modal_danger fade text-left" id="deleteDocument_skola_ND">
								<div class="modal-dialog">
									<div class="modal-content material-modal__content">
										<div class="modal-header material-modal__header">
											<button class="close material-modal__close" data-dismiss="modal">&times;</button>
											<h4 class="modal-title material-modal__title">Brisanje</h4>
										</div>
										<div class="modal-body material-modal__body">
											<p>Jeste li sigurni da želite obrisati ovaj tip dokumenta?</p>
										</div>
										<div class="modal-footer material-modal__footer">
											<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
											<a id="delete_document_skola_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
										</div>
									</div>
								</div>
							</div>';
					}
					else{
						echo '
							<div class="row">
								<div class = "content_box" style = "min-height: 1px;">
									<div class="alert alert-danger" role="alert">Nema dodanih tipova dokumenata za ovo zanimanje!</div>
								</div>
							</div>
						';
					}
				break;
				
				case "posalji_tipove_dokumenta_smjer_zanimanja":
					$skola_id_filter1 = $_POST['skola_id_filter'];
					$skola_smjer_id_filter1 = $_POST['skola_smjer_id_filter'];
					$ustanova_id_filter1 = $_POST['ustanova_id_filterr'];
					
					$brojac = 0;
					$tipovi_dokumenata_ispis_SS = $db->prepare("
														SELECT *
														FROM idk_nd_ustanove_tip_dokumenta 
														WHERE idd_ustanove_nd = :idd_ustanove_nd AND idd_skole_ustanove_nd = :idd_skole_ustanove_nd AND idd_skole_smjer_ustanove_nd = :idd_skole_smjer_ustanove_nd AND povezanost_dokumenta_ustanove_nd = :povezanost_dokumenta_ustanove_nd
													");
					$tipovi_dokumenata_ispis_SS->execute(array(
													':idd_ustanove_nd' => $ustanova_id_filter1,
													':idd_skole_ustanove_nd' => $skola_id_filter1,
													':idd_skole_smjer_ustanove_nd' => $skola_smjer_id_filter1,
													':povezanost_dokumenta_ustanove_nd' => 3
													));
					if (($tipovi_dokumenata_ispis_SS->rowCount()) != 0){
						while($tipovi_dokumenata_ispis_SS_row = $tipovi_dokumenata_ispis_SS->fetch()){
							$brojac++;
							$idtipadokumenta = $tipovi_dokumenata_ispis_SS_row['id_tip_dokumenta_ustanove_nd'];
							$nazivtipadokumenta = $tipovi_dokumenata_ispis_SS_row['naziv_tip_dokumenta_ustanove_nd'];
							$obaveznostdokumenta = $tipovi_dokumenata_ispis_SS_row['obaveznost_dokumenta_ustanove_nd'];
							$templatedokumenta = $tipovi_dokumenata_ispis_SS_row['template_dokumenta_ustanove_nd'];
							$templatenazivdokumenta = $tipovi_dokumenata_ispis_SS_row['template_naziv_dokumenta_ustanove_nd'];
							$templatenazivdokumenta1 = $tipovi_dokumenata_ispis_SS_row['template_naziv_dokumenta_ustanove_nd'];
							
							if($templatenazivdokumenta1 != NULL){
								if (strpos($templatenazivdokumenta1, '.jpg') !== false OR strpos($templatenazivdokumenta1, '.png') !== false){
									$templatenazivdokumenta1icon = '<i class="fa fa-file-image-o fa-3x" aria-hidden="true"></i>';
								}
								else if(strpos($templatenazivdokumenta1, '.pdf') !== false){
									$templatenazivdokumenta1icon = '<i class="fa fa-file-pdf-o fa-3x" aria-hidden="true"></i>';
								}
								else if(strpos($templatenazivdokumenta1, '.doc') !== false OR strpos($templatenazivdokumenta1, '.docx') !== false){
									$templatenazivdokumenta1icon = '<i class="fa fa-file-word-o fa-3x" aria-hidden="true"></i>';
								}
								else if(strpos($templatenazivdokumenta1, '.xls') !== false OR strpos($templatenazivdokumenta1, '.xlsx') !== false OR strpos($templatenazivdokumenta1, '.csv') !== false){
									$templatenazivdokumenta1icon = '<i class="fa fa-file-excel-o fa-3x" aria-hidden="true"></i>';
								}
								else if(strpos($templatenazivdokumenta1, '.txt') !== false ){
									$templatenazivdokumenta1icon = '<i class="fa fa-file-text-o fa-3x" aria-hidden="true"></i>';
								}
								else{
									$templatenazivdokumenta1icon = '<i class="fa fa-file-powerpoint-o fa-3x" aria-hidden="true"></i>';
								}
								
								$templatenazivdokumenta1download = '<a href="'.getSiteUrlr().'files/dokumenti_ND_ustanove/'.$templatenazivdokumenta1.'" class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK">'.$templatenazivdokumenta1icon.'</a>';
							}
							
							if($obaveznostdokumenta == 1){
								$obaveznostdokumentaprikaz = '<span style = "padding: 1px 16px;" class="material-label material-label_success main-container__column text-center">DA</span>';
							}else{
								$obaveznostdokumentaprikaz = '<span style = "padding: 1px 16px;" class="material-label material-label_warning main-container__column text-center">NE</span>';
							}
							
							if($templatedokumenta == 1){
								$templatedokumentaprikaz = '<span style = "padding: 1px 16px;" class="material-label material-label_success main-container__column text-center">DA</span>';
								if($templatenazivdokumenta == NULL){
									$templatenazivdokumentaprikaz = '<span style = "padding: 1px 16px;" class="material-label material-label_danger main-container__column text-center">Nije dodan template</span>';
								}else{
									$templatenazivdokumentaprikaz = $templatenazivdokumenta1download;
								}
							}else{
								$templatedokumentaprikaz = '<span style = "padding: 1px 16px;" class="material-label material-label_warning main-container__column text-center">NE</span>';
							}
							$brisanje_dokumenta_opcija = '<a href="#" data="'.getSiteUrlr().'nostrifikacija_diploma.php?page=brisanje_tipova_dokumenata_ND_ustanoveSS&id='.$idtipadokumenta.'&ustanova_id='.$ustanova_id_filter1.'" data-toggle="modal" data-target="#deleteDocument_smjer_ND" class="delete_document_smjer btn material-btn material-btn_danger main-container__column" style = "padding: 2px 16px;" title = "Brisanje tipa dokumenta."><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i></a>';
							if($templatedokumenta == 1){
								echo '
								<div style = "border: 1px solid #cccccc; padding: 15px; border-radius: 1.25rem; background-color: #ffffff; margin-bottom: 10px; box-shadow: 0 0 15px #333333ad;">
									<div class = "row" style = "margin-bottom: 10px; font-weight: bold; border-bottom: 1px solid #cccccc; padding-bottom: 10px;">
										<div class = "col-xs-6 text-left">
											Tip br. '.$brojac.' 
										</div>
										<div class = "col-xs-6 text-right">
											'.$brisanje_dokumenta_opcija.'
										</div> 
									</div>
									<div class="row" style = "margin-top: 5px;">
										<div style = "font-weight: bold;" class = "col-xs-3 text-right">
											Naziv:
										</div>
										<div class = "col-xs-9 text-left">
											'.$nazivtipadokumenta.'
										</div>
									</div>
									<div class="row" style = "margin-top: 5px;">
										<div style = "font-weight: bold;" class = "col-xs-3 text-right">
											Obavezan:
										</div>
										<div class = "col-xs-9 text-left">
											'.$obaveznostdokumentaprikaz.'
										</div>
									</div>
									<div class="row" style = "margin-top: 5px;">
										<div style = "font-weight: bold;" class = "col-xs-3 text-right">
											Template:
										</div>
										<div class = "col-xs-9 text-left">
											'.$templatedokumentaprikaz.'
										</div>
									</div>
									<div class="row" style = "margin-top: 5px;">
										<div style = "font-weight: bold;" class = "col-xs-3 text-right">
											Preuzmi:
										</div>
										<div class = "col-xs-9 text-left">
											'.$templatenazivdokumentaprikaz.'
										</div>
									</div>
								</div>';
							}else{
								echo '
								<div style = "border: 1px solid #cccccc; padding: 15px; border-radius: 1.25rem; background-color: #ffffff; margin-bottom: 10px; box-shadow: 0 0 15px #333333ad;">
									<div class = "row" style = "margin-bottom: 10px; font-weight: bold; border-bottom: 1px solid #cccccc; padding-bottom: 10px;">
										<div class = "col-xs-6 text-left">
											Tip br. '.$brojac.' 
										</div>
										<div class = "col-xs-6 text-right">
											'.$brisanje_dokumenta_opcija.'
										</div> 
									</div>
									<div class="row" style = "margin-top: 5px;">
										<div style = "font-weight: bold;" class = "col-xs-3 text-right">
											Naziv:
										</div>
										<div class = "col-xs-9 text-left">
											'.$nazivtipadokumenta.'
										</div>
									</div>
									<div class="row" style = "margin-top: 5px;">
										<div style = "font-weight: bold;" class = "col-xs-3 text-right">
											Obavezan:
										</div>
										<div class = "col-xs-9 text-left">
											'.$obaveznostdokumentaprikaz.'
										</div>
									</div>
									<div class="row" style = "margin-top: 5px;">
										<div style = "font-weight: bold;" class = "col-xs-3 text-right">
											Template:
										</div>
										<div class = "col-xs-9 text-left">
											'.$templatedokumentaprikaz.'
										</div>
									</div>
								</div>';
							}
						}
						echo '<script>
								$(".delete_document_smjer").click(function () {
									var addressValue = $(this).attr("data");
									document.getElementById("delete_document_smjer_link").href = addressValue;
								});
							</script>
							<!-- Modal -->
							<div class="modal material-modal material-modal_danger fade text-left" id="deleteDocument_smjer_ND">
								<div class="modal-dialog">
									<div class="modal-content material-modal__content">
										<div class="modal-header material-modal__header">
											<button class="close material-modal__close" data-dismiss="modal">&times;</button>
											<h4 class="modal-title material-modal__title">Brisanje</h4>
										</div>
										<div class="modal-body material-modal__body">
											<p>Jeste li sigurni da želite obrisati ovaj tip dokumenta?</p>
										</div>
										<div class="modal-footer material-modal__footer">
											<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
											<a id="delete_document_smjer_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
										</div>
									</div>
								</div>
							</div>';
					}
					else{
						echo '
							<div class="row">
								<div class = "content_box" style = "min-height: 1px;">
									<div class="alert alert-danger" role="alert">Nema dodanih tipova dokumenata za ovo zanimanje!</div>
								</div>
							</div>
						';
					}
				break;
				
				case "posalji_tipove_dokumenta_skole_template_vrijednosti":
					
					$skola_id_template_s1 = $_POST['skola_id_template_s'];
					$id_ustanove_template_s1 = $_POST['id_ustanove_template_s'];
					
						$tipovi_dokumenata_ustanove_temp_S = $db->prepare("
															SELECT *
															FROM idk_nd_ustanove_tip_dokumenta
															WHERE idd_ustanove_nd = :idd_ustanove_nd AND idd_skole_ustanove_nd = :idd_skole_ustanove_nd AND idd_skole_smjer_ustanove_nd is null AND template_dokumenta_ustanove_nd = :template_dokumenta_ustanove_nd AND povezanost_dokumenta_ustanove_nd = :povezanost_dokumenta_ustanove_nd
														");
						$tipovi_dokumenata_ustanove_temp_S->execute(array(
														':idd_ustanove_nd' => $id_ustanove_template_s1,
														':idd_skole_ustanove_nd' => $skola_id_template_s1,
														':template_dokumenta_ustanove_nd' => 1,
														':povezanost_dokumenta_ustanove_nd' => 2
														));
						while($row_tipovi_dokumenata_ustanove_temp_S = $tipovi_dokumenata_ustanove_temp_S->fetch()){
							$id_tipa_S = $row_tipovi_dokumenata_ustanove_temp_S['id_tip_dokumenta_ustanove_nd'];
							$naziv_tipa_S = $row_tipovi_dokumenata_ustanove_temp_S['naziv_tip_dokumenta_ustanove_nd'];
							echo '<option value="'.$id_tipa_S.'">'.$naziv_tipa_S.'</option>';
						}
						
				break;
				
				case "posalji_smjerove_skole_template":
					$skola_id_template_ss11 = $_POST['skola_id_template_ss1'];
					
					$tipovi_dokumenata_ustanove_temp_SS = $db->prepare("
															SELECT *
															FROM idk_nd_ustanove_skole_smjerovi
															WHERE idd_skole_ustanove_nd = :idd_skole_ustanove_nd
														");
					$tipovi_dokumenata_ustanove_temp_SS->execute(array(
															':idd_skole_ustanove_nd' => $skola_id_template_ss11));
					
					while($row_tipovi_dokumenata_ustanove_temp_SS = $tipovi_dokumenata_ustanove_temp_SS->fetch()){
						$id_tipa_SS = $row_tipovi_dokumenata_ustanove_temp_SS['id_skole_smjer_ustanove_nd'];
						$naziv_tipa_SS = getSkolaSmjerNDKanidataR($row_tipovi_dokumenata_ustanove_temp_SS['ss_idd']);
						echo '<option value = "'.$id_tipa_SS.'">'.$naziv_tipa_SS.'</option>';
					}
				break;
				
				case "posalji_tipove_dokumenta_skola_smjer_template_vrijednosti":
					$skola_id_template_sss1 = $_POST['skola_id_template_ss'];
					$skola_smjer_id_template_sss1 = $_POST['skola_smjer_id_template_ss'];
					$id_ustanove_template_sss1 = $_POST['id_ustanove_template_ss'];
					
						$tipovi_dokumenata_ustanove_temp_SS = $db->prepare("
															SELECT *
															FROM idk_nd_ustanove_tip_dokumenta
															WHERE idd_ustanove_nd = :idd_ustanove_nd AND idd_skole_ustanove_nd = :idd_skole_ustanove_nd AND idd_skole_smjer_ustanove_nd = :idd_skole_smjer_ustanove_nd AND template_dokumenta_ustanove_nd = :template_dokumenta_ustanove_nd AND povezanost_dokumenta_ustanove_nd = :povezanost_dokumenta_ustanove_nd
														");
						$tipovi_dokumenata_ustanove_temp_SS->execute(array(
														':idd_ustanove_nd' => $id_ustanove_template_sss1,
														':idd_skole_ustanove_nd' => $skola_id_template_sss1,
														':idd_skole_smjer_ustanove_nd' => $skola_smjer_id_template_sss1,
														':template_dokumenta_ustanove_nd' => 1,
														':povezanost_dokumenta_ustanove_nd' => 3
														));
						while($row_tipovi_dokumenata_ustanove_temp_SS = $tipovi_dokumenata_ustanove_temp_SS->fetch()){
							$id_tipa_SS = $row_tipovi_dokumenata_ustanove_temp_SS['id_tip_dokumenta_ustanove_nd'];
							$naziv_tipa_SS = $row_tipovi_dokumenata_ustanove_temp_SS['naziv_tip_dokumenta_ustanove_nd'];
							echo '<option value="'.$id_tipa_SS.'">'.$naziv_tipa_SS.'</option>';
						}
				break;
				
				case "update_broj_zahtjeva_kand":
					$naziv_update_broj_zahtjeva_kondidata1 = $_POST['broj_zahtjeva_na_ustanovi_naziv1'];
					$id_kandidata_update_broj_zahtjeva_kondidata1 = $_POST['update_id_kandidata_broj_zahtjeva1'];
					
					$update_broj_zahtjeva_kan_x = $db->prepare("
												UPDATE idk_nd_kandidata
												SET broj_zahtjeva_na_ustanovi_nd_kandidata = :broj_zahtjeva_na_ustanovi_nd_kandidata
												WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
												");
					
					$update_broj_zahtjeva_kan_x->execute(array(
						':id_broj_nd_kandidata' => $id_kandidata_update_broj_zahtjeva_kondidata1,
						':broj_zahtjeva_na_ustanovi_nd_kandidata' => $naziv_update_broj_zahtjeva_kondidata1
					));
					
					$log_desc = "DIPL -> Dodan broj zahtjeva na ustanovi sa brojem [".$naziv_update_broj_zahtjeva_kondidata1."] za kandidata >>".$id_kandidata_update_broj_zahtjeva_kondidata1."<<.";
					
					$log_date = date('Y-m-d H:i:s');

					$log_query = $db->prepare("
									INSERT INTO idk_logs
										(log_employeeid, log_desc, log_date)
									VALUES
										(:log_employeeid, :log_desc, :log_date)");

					$log_query->execute(array(
									':log_employeeid' => $logged_employee_id,
									':log_desc' => $log_desc,
									':log_date' => $log_date));
					
				break;
				
				// FUNKCIJE ZA MODUL NOSTRIFIKACIJA DIPLOMA END
				case "sviKandidatiZaZadnjuRatu":
					$query = $db->prepare(" 
						SELECT id_broj_nd_kandidata, ime_nd_kandidata, prezime_nd_kandidata
						FROM idk_nd_kandidata
						INNER JOIN idk_predracuni ON idk_nd_kandidata.id_broj_nd_kandidata = idk_predracuni.pr_kandidat_id
						GROUP BY id_broj_nd_kandidata 
						");
					
					$query->execute();
					
					while($row = $query->fetch()){

						$id_broj_nd_kandidata = $row['id_broj_nd_kandidata'];
						if(checkUplacenostSvihRata($id_broj_nd_kandidata) == 1){
							$ime_nd_kandidata = $row['ime_nd_kandidata'];
							$prezime_nd_kandidata = $row['prezime_nd_kandidata']; 
							$kandidati =[
							'id_broj_nd_kandidata' => $id_broj_nd_kandidata,
							'ime_nd_kandidata' => $ime_nd_kandidata,
							'prezime_nd_kandidata' => $prezime_nd_kandidata
							];
							
							$data[] = $kandidati;
						}	
					}
					echo json_encode($data);
				break;
				
				case "getSelectDrzave":
					
					$agent_id = $_POST["agent_id"];
					
					if($agent_id<0){
						echo '<option selected disabled>Odaberi</option>';
						echo '<option value = "bih">BiH</option>';
						echo '<option value = "srb">Srbija</option>';
						echo '<option value = "de">Njemačka</option>';
						echo '<option value = "ostalo">Ostale</option>';
					}
					else{
						$get_drzave = $db->prepare("
														SELECT lt_drzava
														FROM idk_nd_limiti
														WHERE lt_emp_id = $agent_id
													");
						$get_drzave->execute();
						
						$row_drzave = $get_drzave->fetch();
						$drzave = explode(',', $row_drzave['lt_drzava']);
						echo '<option selected disabled>Odaberi</option>';
						
						foreach($drzave as $drzava){ 
							switch($drzava){
								case "bih":
									$drzava_full_naziv = "BiH";
								break;
								case "srb":
									$drzava_full_naziv = "Srbija";
								break;
								case "de":
									$drzava_full_naziv = "Njemačka";
								break;
								case "ostalo":
									$drzava_full_naziv = "Ostale";
								break;
								default:
									$drzava_full_naziv = "NN";
								break;
							}
							echo '<option value = "'.$drzava.'">'.$drzava_full_naziv.'</option>';
							// data-subtext="'.$drzava.'"
						}
					}
				break;
				
				case "getSelectStatusa":
					
					$drzava = $_POST["drzava_val"];
					$tim_id = $_POST["tim_id"];
					$novi_tim = $_POST["novi_tim"];
					
					switch($drzava){
						case "bih":
							$telefon = "+387";
						break;
						case "srb":
							$telefon = "+381";
						break;
						case "de":
							$telefon = "+49";
						break;
						case "ostalo":
							$telefon = "111";
						break;
						default:
							$telefon = "";
						break;
					}
					
					//echo '<option selected disabled>Odaberi</option>';
					if($tim_id == 0){
						if(getBrojKandidata(11, $telefon, null) > 0) echo '<option value = "11" data-subtext="Broj kandidata: '. getBrojKandidata(11, $telefon, null) .'">LEAD</option>';
					}else{
						if(getBrojKandidata(11, $telefon, $tim_id) > 0) echo '<option value = "11" data-subtext="Broj kandidata: '. getBrojKandidata(11, $telefon, $tim_id) .'">LEAD</option>';
						if(getBrojKandidata(16, $telefon, $tim_id) > 0) echo '<option value = "16" data-subtext="Broj kandidata: '. getBrojKandidata(16, $telefon, $tim_id) .'">Neuspješan Lead 1</option>';
						if(getBrojKandidata(12, $telefon, $tim_id) > 0) echo '<option value = "12" data-subtext="Broj kandidata: '. getBrojKandidata(12, $telefon, $tim_id) .'">Neuspješan Lead 3</option>';
						if(getBrojKandidata(13, $telefon, $tim_id) > 0) echo '<option value = "13" data-subtext="Broj kandidata: '. getBrojKandidata(13, $telefon, $tim_id) .'">Zainteresiran Lead</option>';
						// if(getBrojKandidata(14, $telefon, $tim_id) > 0) echo '<option value = "14" data-subtext="Broj kandidata: '. getBrojKandidata(14, $telefon, $tim_id) .'">Nezainteresiran Lead</option>';
						if(getBrojKandidata(15, $telefon, $tim_id) > 0) echo '<option value = "15" data-subtext="Broj kandidata: '. getBrojKandidata(15, $telefon, $tim_id) .'">U obradi Lead</option>';
						if(getBrojKandidata(17, $telefon, $tim_id) > 0) echo '<option value = "17" data-subtext="Broj kandidata: '. getBrojKandidata(17, $telefon, $tim_id) .'">Neuspješan Lead 1</option>';
						if(getBrojKandidata(18, $telefon, $tim_id) > 0) echo '<option value = "18" data-subtext="Broj kandidata: '. getBrojKandidata(18, $telefon, $tim_id) .'">Neuspješan Lead 2</option>';
						if(getBrojKandidata(19, $telefon, $tim_id) > 0) echo '<option value = "19" data-subtext="Broj kandidata: '. getBrojKandidata(19, $telefon, $tim_id) .'">Termin Zainteresiran</option>';
						if(getBrojKandidata(110, $telefon, $tim_id) > 0) echo '<option value = "110" data-subtext="Broj kandidata: '. getBrojKandidata(110, $telefon, $tim_id) .'">Termin Ostali</option>';
						if(getBrojKandidata(111, $telefon, $tim_id) > 0) echo '<option value = "111" data-subtext="Broj kandidata: '. getBrojKandidata(111, $telefon, $tim_id) .'">Lead NL</option>';
						if(getBrojKandidata(112, $telefon, $tim_id) > 0) echo '<option value = "112" data-subtext="Broj kandidata: '. getBrojKandidata(112, $telefon, $tim_id) .'">Lead NZ</option>';
						if(getBrojKandidata(2, $telefon, $tim_id) > 0 && $novi_tim >= 0) echo '<option value = "2" data-subtext="Broj kandidata: '. getBrojKandidata(2, $telefon, $tim_id) .'">Prikupljanje dokumentacije</option>';
						if(getBrojKandidata(3, $telefon, $tim_id) > 0 && $novi_tim >= 0) echo '<option value = "3" data-subtext="Broj kandidata: '. getBrojKandidata(3, $telefon, $tim_id) .'">Poslana pošta</option>';
						if(getBrojKandidata(4, $telefon, $tim_id) > 0 && $novi_tim >= 0) echo '<option value = "4" data-subtext="Broj kandidata: '. getBrojKandidata(4, $telefon, $tim_id) .'">U obradi</option>';
						if(getBrojKandidata(5, $telefon, $tim_id) > 0 && $novi_tim >= 0) echo '<option value = "5" data-subtext="Broj kandidata: '. getBrojKandidata(5, $telefon, $tim_id) .'">Dopuna dokumentacije</option>';
						if(getBrojKandidata(6, $telefon, $tim_id) > 0 && $novi_tim >= 0) echo '<option value = "6" data-subtext="Broj kandidata: '. getBrojKandidata(6, $telefon, $tim_id) .'">Završen</option>';
						if(getBrojKandidata(77, $telefon, $tim_id) > 0) echo '<option value = "7" data-subtext="Broj kandidata: '. getBrojKandidata(77, $telefon, $tim_id) .'">Arhiviran</option>';
						// data-subtext="'.$drzava.'"
					}
				break;
				
				case "getStatusiBox":
				
					if(isset($_REQUEST["status_val"]))
						$statusi = $_POST["status_val"];
					else $statusi = array();
					$tim_id = $_POST["tim_id"];
					$drzava = $_POST["drzava_val"];
					switch($drzava){
						case "bih":
							$telefon = "+387";
						break;
						case "srb":
							$telefon = "+381";
						break;
						case "de":
							$telefon = "+49";
						break;
						case "ostalo":
							$telefon = "111";
						break;
						default:
							$telefon = "";
						break;
					}
					switch($drzava){
							case "bih":
								$telefon_uslov = "AND kan.mobilni_nd_kandidata LIKE '+387%' ";
							break;
							case "srb":
								$telefon_uslov = "AND kan.mobilni_nd_kandidata LIKE '+381%' ";
							break;
							case "de":
								$telefon_uslov = "AND kan.mobilni_nd_kandidata LIKE '+49%'";
							break;
							case "ostalo":
								$telefon_uslov = "AND (kan.mobilni_nd_kandidata NOT LIKE '+387%' AND kan.mobilni_nd_kandidata NOT LIKE '+381%' AND kan.mobilni_nd_kandidata NOT LIKE '+49%') ";
							break;
							default:
								$telefon_uslov = "";
							break;
						}
					
					
					if(in_array(11, $statusi)){
						if($tim_id == 0){
							$max_broj_11 =  getBrojKandidata(11, $telefon, null);
						}else{
							$max_broj_11 =  getBrojKandidata(11, $telefon, $tim_id);
						}
						echo 
						'<div class="form-group">
							<div class="col-sm-10">
								<label for="status_lead_'.$tim_id.'" class="col-sm-5 control-label" style="padding-left: 0px;"> Lead
								</label>
								<div class="col-sm-2 text-right">
									<a class = "minus_dugme'.$tim_id.' btn material-btn btn-danger limit_broj"><i class="fa fa-minus" aria-hidden="true"></i></a>
								</div>
								<div class="col-sm-3">
									<div class="materail-input-block materail-input-block_success">
										<input style = "text-align: center; font-size: large; font-weight: bold;" class="form-control materail-input svi_inputi'.$tim_id.'" type="number" name="status_lead_'.$tim_id.'" id="status_lead_'.$tim_id.'" min = "0" max = "'.$max_broj_11.'" value="0" required>
									</div>
								</div>
								<div class="col-sm-2 text-left">
									<a class = "plus_dugme'.$tim_id.' btn material-btn btn-success limit_broj"><i class="fa fa-plus" aria-hidden="true"></i></a> 
								</div>
							</div>
						</div>
						';
					}
					if(in_array(16, $statusi)){
						$max_broj_16 =  getBrojKandidata(16, $telefon, $tim_id);
						echo 
						'<div class="form-group">
							<div class="col-sm-10">
								<label for="status_nk1_'.$tim_id.'" class="col-sm-5 control-label" style="padding-left: 0px;"> Neuspješan Kontakt 1
								</label>
								<div class="col-sm-2 text-right">
									<a class = "minus_dugme'.$tim_id.' btn material-btn btn-danger limit_broj"><i class="fa fa-minus" aria-hidden="true"></i></a>
								</div>
								<div class="col-sm-3">
									<div class="materail-input-block materail-input-block_success">
										<input style = "text-align: center; font-size: large; font-weight: bold;" class="form-control materail-input svi_inputi'.$tim_id.'" type="number" name="status_nk1_'.$tim_id.'" id="status_nk1_'.$tim_id.'" min = "0" max = "'.$max_broj_16.'" value="0" required>
									</div>
								</div>
								<div class="col-sm-2 text-left">
									<a class = "plus_dugme'.$tim_id.' btn material-btn btn-success limit_broj"><i class="fa fa-plus" aria-hidden="true"></i></a> 
								</div>
							</div>
						</div>
						';
					}
					if(in_array(17, $statusi)){
						$max_broj_17 =  getBrojKandidata(17, $telefon, $tim_id);
						echo 
						'<div class="form-group">
							<div class="col-sm-10">
								<label for="status_neus_lead1_'.$tim_id.'" class="col-sm-5 control-label" style="padding-left: 0px;"> Neuspješan Lead 1
								</label>
								<div class="col-sm-2 text-right">
									<a class = "minus_dugme'.$tim_id.' btn material-btn btn-danger limit_broj"><i class="fa fa-minus" aria-hidden="true"></i></a>
								</div>
								<div class="col-sm-3">
									<div class="materail-input-block materail-input-block_success">
										<input style = "text-align: center; font-size: large; font-weight: bold;" class="form-control materail-input svi_inputi'.$tim_id.'" type="number" name="status_neus_lead1_'.$tim_id.'" id="status_neus_lead1_'.$tim_id.'" min = "0" max = "'.$max_broj_17.'" value="0" required>
									</div>
								</div>
								<div class="col-sm-2 text-left">
									<a class = "plus_dugme'.$tim_id.' btn material-btn btn-success limit_broj"><i class="fa fa-plus" aria-hidden="true"></i></a> 
								</div>
							</div>
						</div>
						';
					}
					if(in_array(18, $statusi)){
						$max_broj_18 =  getBrojKandidata(18, $telefon, $tim_id);
						echo 
						'<div class="form-group">
							<div class="col-sm-10">
								<label for="status_neus_lead2_'.$tim_id.'" class="col-sm-5 control-label" style="padding-left: 0px;"> Neuspješan Lead 2
								</label>
								<div class="col-sm-2 text-right">
									<a class = "minus_dugme'.$tim_id.' btn material-btn btn-danger limit_broj"><i class="fa fa-minus" aria-hidden="true"></i></a>
								</div>
								<div class="col-sm-3">
									<div class="materail-input-block materail-input-block_success">
										<input style = "text-align: center; font-size: large; font-weight: bold;" class="form-control materail-input svi_inputi'.$tim_id.'" type="number" name="status_neus_lead2_'.$tim_id.'" id="status_neus_lead2_'.$tim_id.'" min = "0" max = "'.$max_broj_18.'" value="0" required>
									</div>
								</div>
								<div class="col-sm-2 text-left">
									<a class = "plus_dugme'.$tim_id.' btn material-btn btn-success limit_broj"><i class="fa fa-plus" aria-hidden="true"></i></a> 
								</div>
							</div>
						</div>
						';
					}
					if(in_array(19, $statusi)){
						$max_broj_19 =  getBrojKandidata(19, $telefon, $tim_id);
						echo 
						'<div class="form-group">
							<div class="col-sm-10">
								<label for="status_termin_znt_'.$tim_id.'" class="col-sm-5 control-label" style="padding-left: 0px;"> Termin Zainteresiran
								</label>
								<div class="col-sm-2 text-right">
									<a class = "minus_dugme'.$tim_id.' btn material-btn btn-danger limit_broj"><i class="fa fa-minus" aria-hidden="true"></i></a>
								</div>
								<div class="col-sm-3">
									<div class="materail-input-block materail-input-block_success">
										<input style = "text-align: center; font-size: large; font-weight: bold;" class="form-control materail-input svi_inputi'.$tim_id.'" type="number" name="status_termin_znt_'.$tim_id.'" id="status_termin_znt_'.$tim_id.'" min = "0" max = "'.$max_broj_19.'" value="0" required>
									</div>
								</div>
								<div class="col-sm-2 text-left">
									<a class = "plus_dugme'.$tim_id.' btn material-btn btn-success limit_broj"><i class="fa fa-plus" aria-hidden="true"></i></a> 
								</div>
							</div>
						</div>
						';
					}
					if(in_array(110, $statusi)){
						$max_broj_110 =  getBrojKandidata(110, $telefon, $tim_id);
						echo 
						'<div class="form-group">
							<div class="col-sm-10">
								<label for="status_termin_ost_'.$tim_id.'" class="col-sm-5 control-label" style="padding-left: 0px;"> Termin Ostali
								</label>
								<div class="col-sm-2 text-right">
									<a class = "minus_dugme'.$tim_id.' btn material-btn btn-danger limit_broj"><i class="fa fa-minus" aria-hidden="true"></i></a>
								</div>
								<div class="col-sm-3">
									<div class="materail-input-block materail-input-block_success">
										<input style = "text-align: center; font-size: large; font-weight: bold;" class="form-control materail-input svi_inputi'.$tim_id.'" type="number" name="status_termin_ost_'.$tim_id.'" id="status_termin_ost_'.$tim_id.'" min = "0" max = "'.$max_broj_110.'" value="0" required>
									</div>
								</div>
								<div class="col-sm-2 text-left">
									<a class = "plus_dugme'.$tim_id.' btn material-btn btn-success limit_broj"><i class="fa fa-plus" aria-hidden="true"></i></a> 
								</div>
							</div>
						</div>
						';
					}
					if(in_array(111, $statusi)){
						$max_broj_111 =  getBrojKandidata(111, $telefon, $tim_id);
						echo 
						'<div class="form-group">
							<div class="col-sm-10">
								<label for="status_lead_nl_'.$tim_id.'" class="col-sm-5 control-label" style="padding-left: 0px;"> Lead NL
								</label>
								<div class="col-sm-2 text-right">
									<a class = "minus_dugme'.$tim_id.' btn material-btn btn-danger limit_broj"><i class="fa fa-minus" aria-hidden="true"></i></a>
								</div>
								<div class="col-sm-3">
									<div class="materail-input-block materail-input-block_success">
										<input style = "text-align: center; font-size: large; font-weight: bold;" class="form-control materail-input svi_inputi'.$tim_id.'" type="number" name="status_lead_nl_'.$tim_id.'" id="status_lead_nl_'.$tim_id.'" min = "0" max = "'.$max_broj_111.'" value="0" required>
									</div>
								</div>
								<div class="col-sm-2 text-left">
									<a class = "plus_dugme'.$tim_id.' btn material-btn btn-success limit_broj"><i class="fa fa-plus" aria-hidden="true"></i></a> 
								</div>
							</div>
						</div>
						';
					}
					if(in_array(112, $statusi)){
						$max_broj_112 =  getBrojKandidata(112, $telefon, $tim_id);
						echo 
						'<div class="form-group">
							<div class="col-sm-10">
								<label for="status_lead_nz_'.$tim_id.'" class="col-sm-5 control-label" style="padding-left: 0px;"> Lead NZ
								</label>
								<div class="col-sm-2 text-right">
									<a class = "minus_dugme'.$tim_id.' btn material-btn btn-danger limit_broj"><i class="fa fa-minus" aria-hidden="true"></i></a>
								</div>
								<div class="col-sm-3">
									<div class="materail-input-block materail-input-block_success">
										<input style = "text-align: center; font-size: large; font-weight: bold;" class="form-control materail-input svi_inputi'.$tim_id.'" type="number" name="status_lead_nz_'.$tim_id.'" id="status_lead_nz_'.$tim_id.'" min = "0" max = "'.$max_broj_112.'" value="0" required>
									</div>
								</div>
								<div class="col-sm-2 text-left">
									<a class = "plus_dugme'.$tim_id.' btn material-btn btn-success limit_broj"><i class="fa fa-plus" aria-hidden="true"></i></a> 
								</div>
							</div>
						</div>
						';
					}
					
					if(in_array(12, $statusi)){
						$max_broj_12 =  getBrojKandidata(12, $telefon, $tim_id);
						echo 
						'<div class="form-group">
							<div class="col-sm-10">
								<label for="status_nk3_'.$tim_id.'" class="col-sm-5 control-label" style="padding-left: 0px;"> Neuspješan Kontakt 3
								</label>
								<div class="col-sm-2 text-right">
									<a class = "minus_dugme'.$tim_id.' btn material-btn btn-danger limit_broj"><i class="fa fa-minus" aria-hidden="true"></i></a>
								</div>
								<div class="col-sm-3">
									<div class="materail-input-block materail-input-block_success">
										<input style = "text-align: center; font-size: large; font-weight: bold;" class="form-control materail-input svi_inputi'.$tim_id.'" type="number" name="status_nk3_'.$tim_id.'" id="status_nk3_'.$tim_id.'" min = "0" max = "'.$max_broj_12.'" value="0" required>
									</div>
								</div>
								<div class="col-sm-2 text-left">
									<a class = "plus_dugme'.$tim_id.' btn material-btn btn-success limit_broj"><i class="fa fa-plus" aria-hidden="true"></i></a> 
								</div>
							</div>
						</div>
						';
					}
					if(in_array(13, $statusi)){
						$max_broj_13 =  getBrojKandidata(13, $telefon, $tim_id);
						echo 
						'<div class="form-group">
							<div class="col-sm-10">
								<label for="status_zaint_'.$tim_id.'" class="col-sm-5 control-label" style="padding-left: 0px;"> Zainteresiran Lead 
								</label>
								<div class="col-sm-2 text-right">
									<a class = "minus_dugme'.$tim_id.' btn material-btn btn-danger limit_broj"><i class="fa fa-minus" aria-hidden="true"></i></a>
								</div>
								<div class="col-sm-3">
									<div class="materail-input-block materail-input-block_success">
										<input style = "text-align: center; font-size: large; font-weight: bold;" class="form-control materail-input svi_inputi'.$tim_id.'" type="number" name="status_zaint_'.$tim_id.'" id="status_zaint_'.$tim_id.'" min = "0" max = "'.$max_broj_13.'" value="0" required>
									</div>
								</div>
								<div class="col-sm-2 text-left">
									<a class = "plus_dugme'.$tim_id.' btn material-btn btn-success limit_broj"><i class="fa fa-plus" aria-hidden="true"></i></a> 
								</div>
							</div>
						</div>
						';
						
					}
					if(in_array(14, $statusi)){
						$nezainteresirani_lead_razlozi_id = $_POST['nezainteresirani_lead_razlozi_id'];

						
						$query_get_biljeske_id = $db->prepare('
							
							SELECT max(sqbil.id_biljeska_nd) as biljeska_id, kan.id_broj_nd_kandidata
							FROM idk_nd_kandidata_biljeske sqbil
							JOIN idk_nd_kandidata kan
							ON kan.id_broj_nd_kandidata = sqbil.id_kandidata_biljeska_nd

							WHERE  
							kan.status_nd_kandidata = 1
							AND kan.pstatus_nd_kandidata = 4
							AND sqbil.tip_biljeska_nd = 3 
							AND sqbil.status_biljeska_nd = 2
							AND kan.tim_nd_kandidata = :tim_nd_kandidata
							'.$telefon_uslov.'
							AND kan.zaduzen_zaposlenik_nd_kandidata = 139
							GROUP BY kan.id_broj_nd_kandidata
						');
						$query_get_biljeske_id -> execute(array(':tim_nd_kandidata' => $tim_id));
						$biljeske_ids = array();
						$nezainteresiran_lead_sa_biljeskama_ids = array();
						while($row_get_biljeske_id = $query_get_biljeske_id -> fetch()){
							array_push($biljeske_ids, $row_get_biljeske_id['biljeska_id']);
							array_push($nezainteresiran_lead_sa_biljeskama_ids, $row_get_biljeske_id['id_broj_nd_kandidata']);
						}
						$biljeske_ids = implode(',',$biljeske_ids);

						if(intval($nezainteresirani_lead_razlozi_id) >= 0){
							$query_get_razlozi_odbijanja = $db->prepare('
								SELECT count(ro.id_ro)
								FROM idk_ro_usluge ro
								JOIN idk_nd_kandidata_biljeske bil
								ON bil.razlog_biljeska_nd = ro.id_ro
								JOIN idk_nd_kandidata kan
								ON kan.id_broj_nd_kandidata = bil.id_kandidata_biljeska_nd
								WHERE bil.id_biljeska_nd IN (
									'.$biljeske_ids.'
								)
								AND kan.status_nd_kandidata = 1
								AND kan.pstatus_nd_kandidata = 4
								AND kan.tim_nd_kandidata = :tim_id
								AND ro.id_ro = :nezainteresirani_lead_razlozi_id
								'.$telefon_uslov.'
							');
							$query_get_razlozi_odbijanja -> execute(array(
								'tim_id' => $tim_id,
								'nezainteresirani_lead_razlozi_id' => $nezainteresirani_lead_razlozi_id
							));
							
							$row_get_razlozi_odbijanja = $query_get_razlozi_odbijanja -> fetch();

							$razlog_cnt = $row_get_razlozi_odbijanja['count(ro.id_ro)'];
						}
						else{
							$query_get_all_nezainteresiran_lead_kandidati_ids = $db->prepare('
								SELECT kan.id_broj_nd_kandidata
								FROM idk_nd_kandidata kan
								WHERE kan.status_nd_kandidata = 1
								AND kan.pstatus_nd_kandidata = 4
								AND kan.tim_nd_kandidata = :tim_nd_kandidata
								AND kan.zaduzen_zaposlenik_nd_kandidata = 139
								'.$telefon_uslov.'
							');
							
							$query_get_all_nezainteresiran_lead_kandidati_ids -> execute(array(':tim_nd_kandidata' => $tim_id));
							$svi_nezainteresiran_lead_kandidati_ids = array();
							while($row_get_all_nezainteresiran_lead_kandidati_ids = $query_get_all_nezainteresiran_lead_kandidati_ids->fetch()){
								array_push($svi_nezainteresiran_lead_kandidati_ids, $row_get_all_nezainteresiran_lead_kandidati_ids['id_broj_nd_kandidata']);
							}
							$array_razlika_ids = array_diff($svi_nezainteresiran_lead_kandidati_ids, $nezainteresiran_lead_sa_biljeskama_ids);

							$array_razlika_ids = implode(',',$array_razlika_ids);
							
							$query_get_all_nezainteresiran_lead_ids_arhivirani_bez_biljeske = $db->prepare('
								SELECT count(id_broj_nd_kandidata)
								FROM idk_nd_kandidata
								WHERE id_broj_nd_kandidata IN ('.$array_razlika_ids.')
							');
							$query_get_all_nezainteresiran_lead_ids_arhivirani_bez_biljeske -> execute();
							$row_get_all_nezainteresiran_lead_ids_arhivirani_bez_biljeske = $query_get_all_nezainteresiran_lead_ids_arhivirani_bez_biljeske -> fetch();
							$razlog_cnt = $row_get_all_nezainteresiran_lead_ids_arhivirani_bez_biljeske['count(id_broj_nd_kandidata)'];

						}
						echo 
						'<div class="form-group">
							<div class="col-sm-10">
								<label for="status_nez_lead_'.$tim_id.'" class="col-sm-5 control-label" style="padding-left: 0px;"> Nezainteresiran Lead
								</label>
								<div class="col-sm-2 text-right">
									<a class = "minus_dugme'.$tim_id.' btn material-btn btn-danger limit_broj"><i class="fa fa-minus" aria-hidden="true"></i></a>
								</div>
								<div class="col-sm-3">
									<div class="materail-input-block materail-input-block_success">
										<input style = "text-align: center; font-size: large; font-weight: bold;" class="form-control materail-input svi_inputi'.$tim_id.'" type="number" name="status_nez_lead_'.$tim_id.'" id="status_nez_lead_'.$tim_id.'" min = "0" max = "'.$razlog_cnt.'" value="0" required>
									</div>
								</div>
								<div class="col-sm-2 text-left">
									<a class = "plus_dugme'.$tim_id.' btn material-btn btn-success limit_broj"><i class="fa fa-plus" aria-hidden="true"></i></a> 
								</div>
							</div>
						</div>
						';
	
					}
					if(in_array(15, $statusi)){
						$max_broj_15 =  getBrojKandidata(15, $telefon, $tim_id);
						echo 
						'<div class="form-group">
							<div class="col-sm-10">
								<label for="status_obr_lead'.$tim_id.'" class="col-sm-5 control-label" style="padding-left: 0px;"> U obradi Lead
								</label>
								<div class="col-sm-2 text-right">
									<a class = "minus_dugme'.$tim_id.' btn material-btn btn-danger limit_broj"><i class="fa fa-minus" aria-hidden="true"></i></a>
								</div>
								<div class="col-sm-3">
									<div class="materail-input-block materail-input-block_success">
										<input style = "text-align: center; font-size: large; font-weight: bold;" class="form-control materail-input svi_inputi'.$tim_id.'" type="number" name="status_obr_lead'.$tim_id.'" id="status_obr_lead'.$tim_id.'" min = "0" max = "'.$max_broj_15.'" value="0" required>
									</div>
								</div>
								<div class="col-sm-2 text-left">
									<a class = "plus_dugme'.$tim_id.' btn material-btn btn-success limit_broj"><i class="fa fa-plus" aria-hidden="true"></i></a> 
								</div>
							</div>
						</div>
						';
					}
					if(in_array(2, $statusi)){
						$max_broj_2 =  getBrojKandidata(2, $telefon, $tim_id);
						echo 
						'<div class="form-group">
							<div class="col-sm-10">
								<label for="status_prik_dok'.$tim_id.'" class="col-sm-5 control-label" style="padding-left: 0px;"> Prikupljanje dokumentacije
								</label>
								<div class="col-sm-2 text-right">
									<a class = "minus_dugme'.$tim_id.' btn material-btn btn-danger limit_broj"><i class="fa fa-minus" aria-hidden="true"></i></a>
								</div>
								<div class="col-sm-3">
									<div class="materail-input-block materail-input-block_success">
										<input style = "text-align: center; font-size: large; font-weight: bold;" class="form-control materail-input svi_inputi'.$tim_id.'" type="number" name="status_prik_dok'.$tim_id.'" id="status_prik_dok'.$tim_id.'" min = "0" max = "'.$max_broj_2.'" value="0" required>
									</div>
								</div>
								<div class="col-sm-2 text-left">
									<a class = "plus_dugme'.$tim_id.' btn material-btn btn-success limit_broj"><i class="fa fa-plus" aria-hidden="true"></i></a> 
								</div>
							</div>
						</div>
						';
					}
					if(in_array(3, $statusi)){
						$max_broj_3 =  getBrojKandidata(3, $telefon, $tim_id);
						echo 
						'<div class="form-group">
							<div class="col-sm-10">
								<label for="status_posl_posta'.$tim_id.'" class="col-sm-5 control-label" style="padding-left: 0px;"> Poslana pošta
								</label>
								<div class="col-sm-2 text-right">
									<a class = "minus_dugme'.$tim_id.' btn material-btn btn-danger limit_broj"><i class="fa fa-minus" aria-hidden="true"></i></a>
								</div>
								<div class="col-sm-3">
									<div class="materail-input-block materail-input-block_success">
										<input style = "text-align: center; font-size: large; font-weight: bold;" class="form-control materail-input svi_inputi'.$tim_id.'" type="number" name="status_posl_posta'.$tim_id.'" id="status_posl_posta'.$tim_id.'" min = "0" max = "'.$max_broj_3.'" value="0" required>
									</div>
								</div>
								<div class="col-sm-2 text-left">
									<a class = "plus_dugme'.$tim_id.' btn material-btn btn-success limit_broj"><i class="fa fa-plus" aria-hidden="true"></i></a> 
								</div>
							</div>
						</div>
						';
					}
					if(in_array(4, $statusi)){
						$max_broj_4 =  getBrojKandidata(4, $telefon, $tim_id);
						echo 
						'<div class="form-group">
							<div class="col-sm-10">
								<label for="status_uobradi'.$tim_id.'" class="col-sm-5 control-label" style="padding-left: 0px;"> U obradi
								</label>
								<div class="col-sm-2 text-right">
									<a class = "minus_dugme'.$tim_id.' btn material-btn btn-danger limit_broj"><i class="fa fa-minus" aria-hidden="true"></i></a>
								</div>
								<div class="col-sm-3">
									<div class="materail-input-block materail-input-block_success">
										<input style = "text-align: center; font-size: large; font-weight: bold;" class="form-control materail-input svi_inputi'.$tim_id.'" type="number" name="status_uobradi'.$tim_id.'" id="status_uobradi'.$tim_id.'" min = "0" max = "'.$max_broj_4.'" value="0" required>
									</div>
								</div>
								<div class="col-sm-2 text-left">
									<a class = "plus_dugme'.$tim_id.' btn material-btn btn-success limit_broj"><i class="fa fa-plus" aria-hidden="true"></i></a> 
								</div>
							</div>
						</div>
						';
					}
					if(in_array(5, $statusi)){
						$max_broj_5 =  getBrojKandidata(5, $telefon, $tim_id);
						echo 
						'<div class="form-group">
							<div class="col-sm-10">
								<label for="status_dop_dok'.$tim_id.'" class="col-sm-5 control-label" style="padding-left: 0px;"> Dopuna dokumentacije
								</label>
								<div class="col-sm-2 text-right">
									<a class = "minus_dugme'.$tim_id.' btn material-btn btn-danger limit_broj"><i class="fa fa-minus" aria-hidden="true"></i></a>
								</div>
								<div class="col-sm-3">
									<div class="materail-input-block materail-input-block_success">
										<input style = "text-align: center; font-size: large; font-weight: bold;" class="form-control materail-input svi_inputi'.$tim_id.'" type="number" name="status_dop_dok'.$tim_id.'" id="status_dop_dok'.$tim_id.'" min = "0" max = "'.$max_broj_5.'" value="0" required>
									</div>
								</div>
								<div class="col-sm-2 text-left">
									<a class = "plus_dugme'.$tim_id.' btn material-btn btn-success limit_broj"><i class="fa fa-plus" aria-hidden="true"></i></a> 
								</div>
							</div>
						</div>
						';
					}
					if(in_array(6, $statusi)){
						$max_broj_6 =  getBrojKandidata(6, $telefon, $tim_id);
						echo 
						'<div class="form-group">
							<div class="col-sm-10">
								<label for="status_zavrsen'.$tim_id.'" class="col-sm-5 control-label" style="padding-left: 0px;"> Završen
								</label>
								<div class="col-sm-2 text-right">
									<a class = "minus_dugme'.$tim_id.' btn material-btn btn-danger limit_broj"><i class="fa fa-minus" aria-hidden="true"></i></a>
								</div>
								<div class="col-sm-3">
									<div class="materail-input-block materail-input-block_success">
										<input style = "text-align: center; font-size: large; font-weight: bold;" class="form-control materail-input svi_inputi'.$tim_id.'" type="number" name="status_zavrsen'.$tim_id.'" id="status_zavrsen'.$tim_id.'" min = "0" max = "'.$max_broj_6.'" value="0" required>
									</div>
								</div>
								<div class="col-sm-2 text-left">
									<a class = "plus_dugme'.$tim_id.' btn material-btn btn-success limit_broj"><i class="fa fa-plus" aria-hidden="true"></i></a> 
								</div>
							</div>
						</div>
						';
					}
					if(in_array(7, $statusi)){
						$arhiva_razlozi_id = $_POST['arhiva_razlozi_id'];

						$query_get_max_biljeske = $db->prepare('
							SELECT max(bilj.id_biljeska_nd) as max_biljeska
							FROM idk_nd_kandidata_biljeske bilj
							JOIN idk_nd_kandidata kan
							ON kan.id_broj_nd_kandidata = bilj.id_kandidata_biljeska_nd
							WHERE kan.status_nd_kandidata = 7
							AND kan.zaduzen_zaposlenik_nd_kandidata = 139
							'.$telefon_uslov.'
							AND kan.tim_nd_kandidata = '.$tim_id.'
							GROUP BY(bilj.id_kandidata_biljeska_nd)
						');
						// var_dump($query_get_max_biljeske);
						$query_get_max_biljeske -> execute();
						$array_max_biljeske = array();
						while($row_get_max_biljeske = $query_get_max_biljeske->fetch()){
							array_push($array_max_biljeske, $row_get_max_biljeske['max_biljeska']);
						}
						// var_dump($array_max_biljeske);
						$string_max_biljeske = implode(',',$array_max_biljeske);
						$broj_leadova = count($array_max_biljeske);
						
						$query_get_3_2_biljeske = $db->prepare('
							SELECT count(bilj.id_biljeska_nd) as cnt
							FROM idk_nd_kandidata_biljeske bilj
							WHERE bilj.status_biljeska_nd = 2
							AND bilj.tip_biljeska_nd = 3
							AND bilj.razlog_biljeska_nd = '.$arhiva_razlozi_id.'
							AND bilj.id_biljeska_nd IN ('.$string_max_biljeske.')
						');
						// var_dump($query_get_3_2_biljeske);
						// exit();
						
						$query_get_3_2_biljeske -> execute();
						$row_get_3_2_biljeske = $query_get_3_2_biljeske->fetch();
						$razlog_cnt = $row_get_3_2_biljeske['cnt'];	
						echo 
						'<div class="form-group">
							<div class="col-sm-10">
								<label for="status_arhiv'.$tim_id.'" class="col-sm-5 control-label" style="padding-left: 0px;"> Arhiviran
								</label>
								<div class="col-sm-2 text-right">
									<a class = "minus_dugme'.$tim_id.' btn material-btn btn-danger limit_broj"><i class="fa fa-minus" aria-hidden="true"></i></a>
								</div>
								<div class="col-sm-3">
									<div class="materail-input-block materail-input-block_success">
										<input style = "text-align: center; font-size: large; font-weight: bold;" class="form-control materail-input svi_inputi'.$tim_id.'" type="number" name="status_arhiv'.$tim_id.'" id="status_arhiv'.$tim_id.'" min = "0" max = "'.$razlog_cnt.'" value="0" required>
									</div>
								</div>
								<div class="col-sm-2 text-left">
									<a class = "plus_dugme'.$tim_id.' btn material-btn btn-success limit_broj"><i class="fa fa-plus" aria-hidden="true"></i></a> 
								</div>
							</div>
						</div>
						';
						
					}

				break;
				case "getRazloziNezainteresiranLead":
					$tim_id = $_REQUEST['tim_id'];
					$drzava = $_REQUEST['drzava_val'];
					switch($drzava){
						case "bih":
							$telefon_uslov = "AND kan.mobilni_nd_kandidata LIKE '+387%' ";
						break;
						case "srb":
							$telefon_uslov = "AND kan.mobilni_nd_kandidata LIKE '+381%' ";
						break;
						case "de":
							$telefon_uslov = "AND kan.mobilni_nd_kandidata LIKE '+49%'";
						break;
						case "ostalo":
							$telefon_uslov = "AND (kan.mobilni_nd_kandidata NOT LIKE '+387%' AND kan.mobilni_nd_kandidata NOT LIKE '+381%' AND kan.mobilni_nd_kandidata NOT LIKE '+49%') ";
						break;
						default:
							$telefon_uslov = "";
						break;
					}
					echo '<option selected disabled>Odaberi</option>';
					$query_get_biljeske_id = $db->prepare('
							
						SELECT max(sqbil.id_biljeska_nd) as biljeska_id, kan.id_broj_nd_kandidata
						FROM idk_nd_kandidata_biljeske sqbil
						JOIN idk_nd_kandidata kan
						ON kan.id_broj_nd_kandidata = sqbil.id_kandidata_biljeska_nd

						WHERE  
						kan.status_nd_kandidata = 1
						AND kan.pstatus_nd_kandidata = 4
						AND sqbil.tip_biljeska_nd = 3 
						AND sqbil.status_biljeska_nd = 2
						AND kan.tim_nd_kandidata = :tim_nd_kandidata
						'.$telefon_uslov.'
						AND kan.zaduzen_zaposlenik_nd_kandidata = 139
						GROUP BY kan.id_broj_nd_kandidata
					');
					$query_get_biljeske_id -> execute(array(':tim_nd_kandidata' => $tim_id));
					$biljeske_ids = array();
					$nezainteresiran_lead_sa_biljeskama_ids = array();
					while($row_get_biljeske_id = $query_get_biljeske_id -> fetch()){
						array_push($biljeske_ids, $row_get_biljeske_id['biljeska_id']);
						array_push($nezainteresiran_lead_sa_biljeskama_ids, $row_get_biljeske_id['id_broj_nd_kandidata']);
					}
					$biljeske_ids = implode(',',$biljeske_ids);
					
					$query_get_all_nezainteresiran_lead_kandidati_ids = $db->prepare('
						SELECT kan.id_broj_nd_kandidata
						FROM idk_nd_kandidata kan
						WHERE kan.status_nd_kandidata = 1
						AND kan.pstatus_nd_kandidata = 4
						AND kan.tim_nd_kandidata = :tim_nd_kandidata
						AND kan.zaduzen_zaposlenik_nd_kandidata = 139
						'.$telefon_uslov.'
					');
					
					$query_get_all_nezainteresiran_lead_kandidati_ids -> execute(array(':tim_nd_kandidata' => $tim_id));
					$svi_nezainteresirani_lead_kandidati_ids = array();
					while($row_get_all_nezainteresiran_lead_kandidati_ids = $query_get_all_nezainteresiran_lead_kandidati_ids->fetch()){
						array_push($svi_nezainteresirani_lead_kandidati_ids, $row_get_all_nezainteresiran_lead_kandidati_ids['id_broj_nd_kandidata']);
					}
					$array_razlika_ids = array_diff($svi_nezainteresirani_lead_kandidati_ids, $nezainteresiran_lead_sa_biljeskama_ids);

					$array_razlika_ids = implode(',',$array_razlika_ids);
					$query_get_all_kandidati_ids_nezainteresirani_lead_bez_biljeske = $db->prepare('
						SELECT count(id_broj_nd_kandidata)
						FROM idk_nd_kandidata
						WHERE id_broj_nd_kandidata IN ('.$array_razlika_ids.')
					');
					$query_get_all_kandidati_ids_nezainteresirani_lead_bez_biljeske -> execute();
					$row_get_all_kandidati_ids_nezainteresiran_lead_bez_biljeske = $query_get_all_kandidati_ids_nezainteresirani_lead_bez_biljeske -> fetch();
					$cnt_kandidati_nezainteresiran_lead_bez_razloga = $row_get_all_kandidati_ids_nezainteresiran_lead_bez_biljeske['count(id_broj_nd_kandidata)'];

					$query_get_razlozi_odbijanja = $db->prepare("
						SELECT
							r.id_ro, 
							r.naziv_ro_bs, 
							r.status_ro, 
							r.ponovno_zvanje_ro, 
							r.br_dana_ro, 
							r.dodao_ro,
							(
								SELECT 
								COUNT(kan.id_broj_nd_kandidata) 
								FROM 
									idk_nd_kandidata kan
								JOIN 
									idk_nd_kandidata_biljeske bilj
								ON 
									kan.id_broj_nd_kandidata = bilj.id_kandidata_biljeska_nd 
								WHERE  
									bilj.razlog_biljeska_nd = r.id_ro
									AND kan.status_nd_kandidata = 1
									AND kan.pstatus_nd_kandidata = 4
									AND bilj.tip_biljeska_nd = 3 
									AND bilj.status_biljeska_nd = 2
									AND kan.tim_nd_kandidata = :tim_id
									AND kan.zaduzen_zaposlenik_nd_kandidata = 139
										".$telefon_uslov."
									AND 
									bilj.id_biljeska_nd IN(
										".$biljeske_ids."
									)
							)
							AS 
							razlog_cnt
						FROM 
							idk_ro_usluge r
					");
					echo '<option selected disabled>Odaberi</option>';
					
					$query_get_razlozi_odbijanja -> execute(array('tim_id' => $tim_id));
					while($row_get_razlozi_odbijanja = $query_get_razlozi_odbijanja -> fetch()){
						$razlog_naziv = $row_get_razlozi_odbijanja['naziv_ro_bs'];
						$razlog_id = $row_get_razlozi_odbijanja['id_ro'];
						$razlog_cnt = $row_get_razlozi_odbijanja['razlog_cnt'];							
						// $kandidat_full_name = $row_get_razlozi_odbijanja['ime_nd_kandidata']." ".$row_get_razlozi_odbijanja['prezime_nd_kandidata'];
						if($razlog_cnt != 0){
							echo '<option value = "'.$razlog_id.'" data-subtext = "Broj kandidata '.$razlog_cnt.'">'.$razlog_naziv.'</option>';							
						}
					}
					if(!is_null($cnt_kandidati_nezainteresiran_lead_bez_razloga)){
						echo '<option value="-1" data-subtext = "Broj kandidata'.$cnt_kandidati_nezainteresiran_lead_bez_razloga.'">Nepoznat razlog</option>';
					}
				break;
				
				case "getRazloziArhiva":
					$tim_id = $_REQUEST['tim_id'];
					$drzava = $_REQUEST['drzava_val'];
					switch($drzava){
						case "bih":
							$telefon_uslov = "AND kan.mobilni_nd_kandidata LIKE '+387%' ";
						break;
						case "srb":
							$telefon_uslov = "AND kan.mobilni_nd_kandidata LIKE '+381%' ";
						break;
						case "de":
							$telefon_uslov = "AND kan.mobilni_nd_kandidata LIKE '+49%'";
						break;
						case "ostalo":
							$telefon_uslov = "AND (kan.mobilni_nd_kandidata NOT LIKE '+387%' AND kan.mobilni_nd_kandidata NOT LIKE '+381%' AND kan.mobilni_nd_kandidata NOT LIKE '+49%') ";
						break;
						default:
							$telefon_uslov = "";
						break;
					}
						if($tim_id == null){
							$tim_uslov = "";
						}else{
							$tim_uslov = "AND tim_nd_kandidata = $tim_id";
						}
					$query_get_razlozi_odbijanja_arhiv = $db->prepare('
						SELECT id_ro
						FROM idk_ro_usluge
						WHERE br_dana_ro IS NULL
						AND status_ro != 1
					');
					$array_razlozi_odbijanja_arhiva = array();
					$query_get_razlozi_odbijanja_arhiv -> execute();
					while($row_get_razlozi_odbijanja_arhiv = $query_get_razlozi_odbijanja_arhiv->fetch()){
						array_push($array_razlozi_odbijanja_arhiva, $row_get_razlozi_odbijanja_arhiv['id_ro']);
					}
					$string_razlozi_odbijanja_arhiva = implode(',',$array_razlozi_odbijanja_arhiva);
					// var_dump($query_get_razlozi_odbijanja_arhiv);
					$query_get_max_biljeske = $db->prepare('
						SELECT max(bilj.id_biljeska_nd) as max_biljeska
						FROM idk_nd_kandidata_biljeske bilj
						JOIN idk_nd_kandidata kan
						ON kan.id_broj_nd_kandidata = bilj.id_kandidata_biljeska_nd
						WHERE kan.status_nd_kandidata = 7
						AND kan.zaduzen_zaposlenik_nd_kandidata = 139
						'.$telefon_uslov.'
						'.$tim_uslov.'
						GROUP BY(bilj.id_kandidata_biljeska_nd)
					');
					// var_dump($query_get_max_biljeske);
					$query_get_max_biljeske -> execute();
					$array_max_biljeske = array();
					while($row_get_max_biljeske = $query_get_max_biljeske->fetch()){
						array_push($array_max_biljeske, $row_get_max_biljeske['max_biljeska']);
					}
					// var_dump($array_max_biljeske);
					$string_max_biljeske = implode(',',$array_max_biljeske);
					$broj_leadova = count($array_max_biljeske);
					
					$query_get_3_2_biljeske = $db->prepare('
						SELECT count(bilj.id_biljeska_nd) as cnt
						FROM idk_nd_kandidata_biljeske bilj
						WHERE bilj.status_biljeska_nd = 2
						AND bilj.tip_biljeska_nd = 3
						AND bilj.razlog_biljeska_nd IN('.$string_razlozi_odbijanja_arhiva.')
						AND bilj.id_biljeska_nd IN ('.$string_max_biljeske.')
					');
					// var_dump($query_get_3_2_biljeske);
					// exit();
					
					$query_get_3_2_biljeske -> execute();
					$query_get_razlozi_odbijanja = $db->prepare("
						SELECT
							r.id_ro, 
							r.naziv_ro_bs, 
							r.status_ro, 
							r.ponovno_zvanje_ro, 
							r.br_dana_ro, 
							r.dodao_ro,
							(
								SELECT 
								COUNT(kan.id_broj_nd_kandidata) 
								FROM 
									idk_nd_kandidata kan
								JOIN 
									idk_nd_kandidata_biljeske bilj
								ON 
									kan.id_broj_nd_kandidata = bilj.id_kandidata_biljeska_nd 
								WHERE  
									bilj.razlog_biljeska_nd = r.id_ro
									AND bilj.tip_biljeska_nd = 3 
									AND bilj.status_biljeska_nd = 2
									AND 
									bilj.id_biljeska_nd IN(
										".$string_max_biljeske."
									)
									AND bilj.razlog_biljeska_nd IN(
										".$string_razlozi_odbijanja_arhiva."
									)
							)
							AS 
							razlog_cnt
						FROM 
							idk_ro_usluge r
					");
					//var_dump($query_get_razlozi_odbijanja);
					echo '<option selected disabled>Odaberi</option>';
					
					$query_get_razlozi_odbijanja -> execute(array('tim_id' => $tim_id));
					while($row_get_razlozi_odbijanja = $query_get_razlozi_odbijanja -> fetch()){
						$razlog_naziv = $row_get_razlozi_odbijanja['naziv_ro_bs'];
						$razlog_id = $row_get_razlozi_odbijanja['id_ro'];
						$razlog_cnt = $row_get_razlozi_odbijanja['razlog_cnt'];							
						// $kandidat_full_name = $row_get_razlozi_odbijanja['ime_nd_kandidata']." ".$row_get_razlozi_odbijanja['prezime_nd_kandidata'];
						if($razlog_cnt != 0){
							echo '<option value = "'.$razlog_id.'" data-subtext = "Broj kandidata '.$razlog_cnt.'">'.$razlog_naziv.'</option>';							
						}
					}
				break;
				// FUNKCIJE ZA MODUL NOSTRIFIKACIJA DIPLOMA END


				//case zaduzen za prebacivanje kandidata na skladište START ----------------------------------------------------
				case "prebaci_na_skladiste":

       

					$id=$_POST['emp_id'];                       //id agenta sa kojeg se prebacuju kandidati
					$qhelper=$_POST['qhelper'];                 //Niz ciji clanovi su stringovi na nacin: ("x,n"), gdje je x status kandidata koji se prebacuju, a n broj tih kandidata, 
					$i=0;                                       //npr qhelper[0]=("lead,5") qhelper[1]=("nk1,2")....
					$niz_kand=array();                          //niz u kojem ČE biti spremljeno n kandidata određenog statusa
					
					for($i=0;$i<count($qhelper);$i++){          //petlja koja prolazi kroz sve stringove niza qhelper
			
						$st_kol=explode(",",$qhelper[$i]);      //razdvajanje statusa od broja kandidata za prebacivanje
						//var_dump($st_kol);
			
						if($st_kol[0]=="lead"){                 //svi ifovi traže o kojem se to statusu radi te u varijable st i pst (status i podstatus), postoje iz razloga sto
							$st=1;                              //su statusi i podstatusi spremljeni numerično u bazi
							$pst=1;
						}
						else if($st_kol[0]=="nk1"){
							$st=1;
							$pst=6;
						}
						else if($st_kol[0]=="nk3"){
							$st=1;
							$pst=2;
						}
						else if($st_kol[0]=="zld"){
							$st=1;
							$pst=3;
						}
						else if($st_kol[0]=="nzld"){
							$st=1;
							$pst=4;
						}
						else if($st_kol[0]=="uobld"){
							$st=1;
							$pst=5;
						}
						else if($st_kol[0]=="prdok"){
							$st=2;
							$pst=NULL;
						}
						else if($st_kol[0]=="pospos"){
							$st=3;
							$pst=NULL;
						}
						else if($st_kol[0]=="obrada"){
							$st=4;
							$pst=NULL;
						}
						else if($st_kol[0]=="dopdok"){
							$st=5;
							$pst=NULL;
						}
						else if($st_kol[0]=="zavrs"){
							$st=6;
							$pst=NULL;
						}
						else if($st_kol[0]=="Neuld1"){
							$st=1;
							$pst=7;
						}
						else if($st_kol[0]=="Neuld2"){
							$st=1;
							$pst=8;
						}
						else if($st_kol[0]=="Terzai"){
							$st=1;
							$pst=9;
						}
						else if($st_kol[0]=="Terost"){
							$st=1;
							$pst=10;
						}
						else if($st_kol[0]=="LeadNL"){
							$st=1;
							$pst=11;
						}
						else if($st_kol[0]=="LeadNZ"){
							$st=1;
							$pst=12;
						}
						else if($st_kol[0]=="arhiva"){
							$st=7;
							$pst=NULL;
						}
						
						$qstatus[0]="status_nd_kandidata = ".$st;                       //qstatus je niz uz pomocu kojeg kreiramo string za prvi query
						if($pst!=NULL)
							$qstatus[1]="pstatus_nd_kandidata = ".$pst;
						
					   
						$qst=implode(" AND ", $qstatus);                                //qst je taj string
			
						//prvi kveri vadi n broj kandidata (st_kol[1] prethodno dobiven iz proslijedjenog niza stringova qhelper) za koje je trenutno zaduzen zaposlenik sa kojeg prebacujemo agente nekog 
						//nekog statusa kojeg smo prethodno odredili
						$q_niz_kand=$db->prepare('                                      
							SELECT id_broj_nd_kandidata
							FROM idk_nd_kandidata
							WHERE zaduzen_zaposlenik_nd_kandidata='.$id.' AND '.$qst.' ORDER BY id_broj_nd_kandidata LIMIT '.$st_kol[1]
						); 
						//var_dump($q_niz_kand);
			
						$q_niz_kand->execute();
						
						
						while($row_niz_kand=$q_niz_kand->fetch()){                      //za svakog tog kandidata radimo:
							array_push($niz_kand, $row_niz_kand['id_broj_nd_kandidata']);
							
			
							//query koji u tabeli idk_nd_menadzeri_statistike dodaje novi red koji govori koji kandidat je kad prebacen sa kojeg agenta na skladište (77-demo, 139-live)
							//ovaj bit ovog unosa je da se zna kojem je agentu kandidat prethodno bio zaduzen da se zna kojem skladistu pripada
							$q_men_stat=$db->prepare("
								INSERT INTO idk_nd_menadzeri_statistike (idd_broj_nd_kandidata, zaduzen_zaposlenik_id, prethodni_zaposlenik_id, vrsta_aktivnosti, vrijeme_aktivnosti)
								VALUES(".$niz_kand[count($niz_kand)-1].",139,".$id.",1,'".date('Y-m-d H:i:s')."')
							");
							
							//var_dump($q_men_stat);
			
							$q_men_stat->execute();
							
			
							//query koji prebacuje zaduzenog zaposlenika odredjenog kandidata na skladište (id 77 na demu, 139 na liveu)
							$q_zad_agent=$db->prepare('                                 
								UPDATE idk_nd_kandidata
								SET zaduzen_zaposlenik_nd_kandidata=139
								WHERE id_broj_nd_kandidata='.$niz_kand[count($niz_kand)-1]
							);
							//var_dump($q_zad_agent);
			
							$q_zad_agent->execute();
						}          
			
						$qstatus[1]="";
					}
					
					$log_kand=implode(",",$niz_kand); //kreiranje stringa od niza u kojem su spremljeni svi kandidati (12,25,92,11) za logove
					//var_dump($log_kand);
					$log_date = date('Y-m-d H:i:s');
					$log_desc =  $logged_employee_id." prebacuje kandidate(".$log_kand.") sa agenta ".$id." u skladište"; //description loga
			
					//query za unos loga, ulogovani employee taj i taj prebacuje kandidate (te,i, te) sa agenta tog i tog u skladiste
					$log_query = $db->prepare("
									INSERT INTO idk_logs
										(log_employeeid, log_desc, log_date)
									VALUES
										(".$logged_employee_id.", '".$log_desc."', '".$log_date."')"
									);
					$log_query->execute();
					//var_dump($log_query);
					
				break;
				//case zaduzen za prebacivanje kandidata sa agenta na skladiste END ----------------------------------
				case "getPartnerCandidates_dipl":
					$partnerid = $_REQUEST['partnerid'];
					$getSiteUrl = getSiteUrlr();
					$query_DIPL = $db->prepare("SELECT id_broj_nd_kandidata, ime_nd_kandidata, prezime_nd_kandidata, mobilni_nd_kandidata, email_nd_kandidata, vrijeme_kreiranja_nd_kandidata, dodao_zaposlenik_nd_kandidata, status_nd_kandidata, pstatus_nd_kandidata, povijest_nd_kandidata, povijest_vrsta_nd_kandidata, zaduzen_zaposlenik_nd_kandidata, kandidat_idd, partner_nd_status, idk_partner_uplate.jp_uplate_partnerid
										FROM idk_nd_kandidata 
										INNER JOIN idk_partner_uplate ON idk_nd_kandidata.id_broj_nd_kandidata = idk_partner_uplate.jp_uplate_kandidatid
										WHERE idk_partner_uplate.jp_uplate_status != 1 AND idk_partner_uplate.jp_uplate_vrsta = 2 AND idk_partner_uplate.jp_uplate_partnerid = ".$partnerid);
									
						// povijest_vrsta_nd_kandidata se gleda iz razloga što bi mogle biti kasnije promjene u vrsti nostrifikacije te da se ne mijenja svugdje 						
									
						$query_DIPL->execute();
						// MOGUCA ISPLATA
						$data = array();
						while($row_dipl = $query_DIPL->fetch()){
						$nestedData=array();
						$dipl_price = 25;
						$id_broj_nd_kandidata = $row_dipl["id_broj_nd_kandidata"]; 
						$ime_nd_kandidata_ispis = $row_dipl["ime_nd_kandidata"]; 
						$prezime_nd_kandidata_ispis = $row_dipl["prezime_nd_kandidata"];
						$mobilni_nd_kandidata_ispis = $row_dipl['mobilni_nd_kandidata'];
						$email_nd_kandidata_ispis = $row_dipl['email_nd_kandidata'];
						$vrijeme_kreiranja_nd_kandidata_ispis = date('d.m.Y H:i', strtotime($row_dipl['vrijeme_kreiranja_nd_kandidata']));
						$dodao_zaposlenik_nd_kandidata_ispis = getZaposlenikimeR($row_dipl['dodao_zaposlenik_nd_kandidata']);
						$zaduzen_zaposlenik_nd_kandidata_ispis = getZaposlenikimeR($row_dipl['zaduzen_zaposlenik_nd_kandidata']);
						$status_nd_kandidata_ispis = $row_dipl['status_nd_kandidata'];
						$pstatus_nd_kandidata_ispis = $row_dipl['pstatus_nd_kandidata'];
						$povijest = $row_dipl['povijest_nd_kandidata'];
						$povijest_vrsta = $row_dipl['povijest_vrsta_nd_kandidata'];		
						
						$kandidat_id = $row_dipl['id_broj_nd_kandidata'];
						$kandidat_partnerid = $row_dipl['jp_uplate_partnerid'];
						
						
						if($status_nd_kandidata_ispis == 1){
							if($pstatus_nd_kandidata_ispis == 1){
								$pstatus_nd_kandidata_ispis1 = 'Lead';
								$pstatus_nd_kandidata_style1 = 'background-color: #839098;';
							}
							else if($pstatus_nd_kandidata_ispis == 2){
								$pstatus_nd_kandidata_ispis1 = 'Neuspješan Kontakt 3';
								$pstatus_nd_kandidata_style1 = 'background-color: #00fb53;';
							}
							else if($pstatus_nd_kandidata_ispis == 3){
								$pstatus_nd_kandidata_ispis1 = 'Zainteresiran Lead';
								$pstatus_nd_kandidata_style1 = 'background-color: #0E6973;';
							}
							else if($pstatus_nd_kandidata_ispis == 4){
								$pstatus_nd_kandidata_ispis1 = 'Nezainteresiran Lead';
								$pstatus_nd_kandidata_style1 = 'background-color: #BF214B;';
							}
							else if($pstatus_nd_kandidata_ispis == 5){
								$pstatus_nd_kandidata_ispis1 = 'U obradi Lead';
								$pstatus_nd_kandidata_style1 = 'background-color: #c79cff;';
							}
							else if($pstatus_nd_kandidata_ispis == 6){
								$pstatus_nd_kandidata_ispis1 = 'Neuspješan Kontakt 1';
								$pstatus_nd_kandidata_style1 = 'background-color: #00fafb;';
							}
							$status_nd_kandidata_ispis1 = '<span style = "'.$pstatus_nd_kandidata_style1.' color: white" class="label label-default material-label material-label_default main-container__column text-left">'.$pstatus_nd_kandidata_ispis1.'</span>';
						}
						else if($status_nd_kandidata_ispis == 2){
							$status_nd_kandidata_ispis1 = '<span  style = "background-color: #f2e42e;" class="label label-default material-label material-label_default main-container__column text-left">Prikupljanje dokumentacije</span>';
						}
						else if($status_nd_kandidata_ispis == 3){ 
							$status_nd_kandidata_ispis1 = '<span class="label label-primary material-label material-label_primary main-container__column text-left">Poslana pošta</span>';
						}
						else if($status_nd_kandidata_ispis == 4){
							$status_nd_kandidata_ispis1 = '<span class="label label-info material-label material-label_info main-container__column text-left">U obradi</span>';
						}
						else if($status_nd_kandidata_ispis == 5){
							$status_nd_kandidata_ispis1 = '<span class="label label-warning material-label material-label_warning main-container__column text-left">Dopuna dokumentacije</span>';
						}
						else if($status_nd_kandidata_ispis == 6){
							$status_nd_kandidata_ispis1 = '<span class="label label-success material-label material-label_success main-container__column text-left">Završen</span>';
						}
						else if($status_nd_kandidata_ispis == 7){
							$status_nd_kandidata_ispis1 = '<span class="label label-danger material-label material-label_danger main-container__column text-left">Arhiviran</span>';
						}	
						$partner_nd_status = $row_dipl["partner_nd_status"];

						if($partner_nd_status == 1){
							$dipl_price = 20;
						}elseif($partner_nd_status == 2){
							$dipl_price = 22.5;
						}elseif($partner_nd_status == 3){
							$dipl_price = 25;
						}elseif($partner_nd_status == 4){
							$dipl_price = 25;
						}
						$partner_isplaceno_txt = '<input type="text" class="form-control jp_datum_uplate" placeholder="Datum" style="float:left;width:15rem;" required><i class="fa fa-check oznaci_kao_isplaceno" data-toggle="modal" data-target="#potvrdaUplate_modal" data-vrsta="2" data-provizija="'.$dipl_price.'" data-kandidatid="'.$id_broj_nd_kandidata.'" data-partnerid="'.$kandidat_partnerid.'" style="font-size:24px;color:#999999;cursor:pointer;" aria-hidden="true"></i>';																
						
						
						if($povijest == 0){
							$povijest_ispis = 'Ručna registracija-'.$dodao_zaposlenik_nd_kandidata_ispis.'';
							continue;
						}
						else if($povijest == 1){
							if($povijest_vrsta == 1){
								$povijest_vrsta_ispisx = 'SMS';
							}
							else if($povijest_vrsta == 2){
								$povijest_vrsta_ispisx = 'JobStep-Messenger';
							}
							else if($povijest_vrsta == 3){
								$povijest_vrsta_ispisx = 'CRM';
							}
							else if($povijest_vrsta == 4){
								$povijest_vrsta_ispisx = 'Viber';
							}
							$povijest_ispis = 'Kandidati-'.$povijest_vrsta_ispisx.'';
							}
						else if($povijest == 2){
							if($povijest_vrsta == 1){
								$povijest_vrsta_ispisx = 'APP';
							}
							else if($povijest_vrsta == 2){
								$povijest_vrsta_ispisx = 'WEB';
							}
							$povijest_ispis = 'Sve za vize-'.$povijest_vrsta_ispisx.'';
						}
						else if($povijest == 3){
							$povijest_ispis = 'JobStep-Partner-APP';
						}
						else if($povijest == 4){
							$povijest_ispis = 'JobStep-Web';
						}
						else if($povijest == 5){
							$povijest_ispis = ''.getKampanjeSkrNazivDIPLR($kampanja).'';
						}
					$nestedData[] = '<td class="text-center">'.$id_broj_nd_kandidata.'</td>';
					$nestedData[] = 	'<td class="text-center"><a href="'.$getSiteUrl.'nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$id_broj_nd_kandidata.'">'.$ime_nd_kandidata_ispis.' '.$prezime_nd_kandidata_ispis.'</a></td>';
					$nestedData[] = 	'<td class="text-center" style = "word-break: break-all;">'.$status_nd_kandidata_ispis1.'</td>';
					$nestedData[] = 	'<td class="text-center" style = "word-break: break-all;"><span class="label label-primary material-label material-label_primary main-container__column text-left">'.$povijest_ispis.'</span></td>';
					$nestedData[] = 	'<td class="text-center">'.$vrijeme_kreiranja_nd_kandidata_ispis.'</td>';
					$nestedData[] = 	'<td class = "text-center"><span class="label label-info material-label material-label_success material-label_xs main-container__column">'.$dipl_price.' €</span></td>';	
					$nestedData[] = 	'<td class="text-center">'.$partner_isplaceno_txt.'</td>'; 	
											
					$data[] = $nestedData;						
												
												
					}
					$data[]='<script>$(".jp_datum_uplate").flatpickr({
								dateFormat: "d.m.Y H:i:s",
								disableMobile: "true",
							});
							$(".oznaci_kao_isplaceno").on("click", function() {
							var kandidat_id = $(this).data("kandidatid");
							var partner_id = $(this).data("partnerid");
							var provizija = $(this).data("provizija");
							var vrsta = $(this).data("vrsta");
							var selektor = $(this);
							var datum =  $(this).parent().find("input").val();
							console.log("post varijabli");
							$(".potvrdaUplate_button").on( "click", function() {
								if ($(selektor).hasClass("oznaci_kao_isplaceno")) {
									$.ajax({
										url: "'.$getSiteUrl.'do.php?form=pay_partner",
										type: "POST",
										data: {"kandidat_id":kandidat_id, "partner_id":partner_id, "provizija":provizija,"datum":datum,"vrsta":vrsta},
										dataType: "html",
										success: function(data) {
											selektor.css({"color": "green", "cursor": "not-allowed"});
											selektor.removeClass("oznaci_kao_isplaceno");
											selektor.removeAttr("data-target");
											location.reload();
										}
									});
								};
							});
						});
							</script>';
					echo json_encode($data);
				break;
				//CASE ZADUZEN ZA PREBACIVANJE SVIH KANDIDATA JEDNOG AGENTA NA SKLADIŠTE ----------------------------------------------------------------------------------------START
				case "prebaci_sve_skladiste":
					$skladiste_id = 139;
					$agent_ids = $_POST['agent_ids'];
					foreach ($agent_ids as $agent_id){
						$query_get_prebacene = $db->prepare('
							SELECT id_broj_nd_kandidata
							FROM idk_nd_kandidata
							WHERE zaduzen_zaposlenik_nd_kandidata = :agent_id
						');
						$query_get_prebacene->execute(array(
							':agent_id' => $agent_id
						));
						$niz_kand = array();
						while($row_prebaceni = $query_get_prebacene->fetch()){
							var_dump($row_prebaceni['id_broj_nd_kandidata']);
							array_push($niz_kand, $row_prebaceni['id_broj_nd_kandidata']);
						}
						
						$query_prebaci = $db->prepare('
							UPDATE idk_nd_kandidata
							SET zaduzen_zaposlenik_nd_kandidata = :skladiste_id
							WHERE zaduzen_zaposlenik_nd_kandidata = :agent_id
						');
						
						$query_prebaci->execute(array(
							":agent_id" => $agent_id,
							":skladiste_id" => $skladiste_id
						));
						$log_kand=implode(",",$niz_kand); //kreiranje stringa od niza u kojem su spremljeni svi kandidati (12,25,92,11) za logove
						$log_date = date('Y-m-d H:i:s');
						$log_desc =  $logged_employee_id." prebacuje kandidate(".$log_kand.") sa agenta ".$agent_id." u skladište"; //description loga
				
						//query za unos loga, ulogovani employee taj i taj prebacuje kandidate (te,i, te) sa agenta tog i tog u skladiste
						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_employeeid, log_desc, log_date)
										VALUES
											(".$logged_employee_id.", '".$log_desc."', '".$log_date."')"
										);
						$log_query->execute();
							
					}


					
				break;
				//CASE ZADUZEN ZA PREBACIVANJE SVIH KANDIDATA JEDNOG AGENTA NA SKLADIŠTE ----------------------------------------------------------------------------------------END
			
				case "inkaso_po_agentu":
					
					$agent_id = $_REQUEST['agent_id'];
					$query_mjesec_0 = $db->prepare("
						SELECT 
						emp.employee_id, emp.employee_firstname, emp.employee_lastname,

						sum(case 
							when pred.pr_uplaceno = 1  
							AND bilj.status_biljeska_nd = 3 
							AND bilj.tip_biljeska_nd = 5 
							AND extract(year_month FROM pred.pr_datum_uplate) = extract(year_month FROM CURRENT_DATE()) 
							THEN 1 ELSE 0 END) as BU0,

						sum(case 
							when bilj.status_biljeska_nd = 3 
							AND bilj.tip_biljeska_nd NOT IN (3,1) 
							AND extract(year_month FROM bilj.vrijeme_dodavanja_biljeska_nd) = extract(year_month FROM CURRENT_DATE())
							THEN 1 ELSE 0 END) as BP0, 

						sum(case 
							when bilj.zadnja_inkaso_biljeska = 1 
							AND bilj.status_biljeska_nd = 3 AND bilj.tip_biljeska_nd = 5 
							AND extract(year_month FROM bilj.uplata_na_datum) BETWEEN extract(year_month FROM CURRENT_DATE()) AND extract(year_month FROM CURRENT_DATE()) 
							THEN 1 ELSE 0 END) as BZU0,
                        sum(case 
							when pred.pr_uplaceno = 1  
							AND bilj.status_biljeska_nd = 3 
							AND bilj.tip_biljeska_nd = 5 
							AND extract(year_month FROM pred.pr_datum_uplate) = extract(year_month FROM CURRENT_DATE() - INTERVAL 1 MONTH)
							THEN 1 ELSE 0 END) as BU1,

						sum(case 
							when bilj.status_biljeska_nd = 3 
							AND bilj.tip_biljeska_nd NOT IN (3,1) 
							AND extract(year_month FROM bilj.vrijeme_dodavanja_biljeska_nd) = extract(year_month FROM CURRENT_DATE() - INTERVAL 1 MONTH)
							THEN 1 ELSE 0 END) as BP1, 

						sum(case 
							when bilj.zadnja_inkaso_biljeska = 1 
							AND bilj.status_biljeska_nd = 3 AND bilj.tip_biljeska_nd = 5 
							AND extract(year_month FROM bilj.uplata_na_datum) = extract(year_month FROM CURRENT_DATE() - INTERVAL 1 MONTH)
							THEN 1 ELSE 0 END) as BZU1,
                        sum(case 
							when pred.pr_uplaceno = 1  
							AND bilj.status_biljeska_nd = 3 
							AND bilj.tip_biljeska_nd = 5 
							AND extract(year_month FROM pred.pr_datum_uplate) = extract(year_month FROM CURRENT_DATE() - INTERVAL 2 MONTH) 
							THEN 1 ELSE 0 END) as BU2,

						sum(case 
							when bilj.status_biljeska_nd = 3 
							AND bilj.tip_biljeska_nd NOT IN (3,1) 
							AND extract(year_month FROM bilj.vrijeme_dodavanja_biljeska_nd) = extract(year_month FROM CURRENT_DATE() - INTERVAL 2 MONTH)
							THEN 1 ELSE 0 END) as BP2, 

						sum(case 
							when bilj.zadnja_inkaso_biljeska = 1 
							AND bilj.status_biljeska_nd = 3 AND bilj.tip_biljeska_nd = 5 
							AND extract(year_month FROM bilj.uplata_na_datum) = extract(year_month FROM CURRENT_DATE() - INTERVAL 2 MONTH)
							THEN 1 ELSE 0 END) as BZU2

						from idk_predracuni pred 
						JOIN idk_nd_kandidata_biljeske bilj 
						ON pred.pr_id = bilj.predracun_id
						JOIN idk_employees emp
						ON bilj.dodao_zaposlenik_biljeska_nd = emp.employee_id
						WHERE emp.employee_id = ".$agent_id."
						
						GROUP BY (bilj.dodao_zaposlenik_biljeska_nd)
					"); 
					$query_mjesec_0 -> execute();
					
					while ($row_mjesec_0 = $query_mjesec_0->fetch()){
						$ime = $row_mjesec_0['employee_firstname'];
						$prezime = $row_mjesec_0['employee_lastname'];

						$mjesec0 = getPrevodMjesecStr(date('m'), date('Y'));
						$BU0 = $row_mjesec_0['BU0'];
						$BP0 = $row_mjesec_0['BP0'];
						$BZU0 = $row_mjesec_0['BZU0'];
						
						$mjesec1 = getPrevodMjesecStr(date('m', strtotime("-1 months")), date('Y', strtotime("-1 months")));
						$BU1 = $row_mjesec_0['BU1'];
						$BP1 = $row_mjesec_0['BP1'];
						$BZU1 = $row_mjesec_0['BZU1'];
						
						$mjesec2 = getPrevodMjesecStr(date('m', strtotime("-2 months")), date('Y', strtotime("-2 months")));
						$BU2 = $row_mjesec_0['BU2'];
						$BP2 = $row_mjesec_0['BP2'];
						$BZU2 = $row_mjesec_0['BZU2'];
						

					}
						$ukBU = $BU0 + $BU1 + $BU2;
						$ukBP = $BP0 + $BP1 + $BP2;
						$ukBZU = $BZU0 + $BZU1 + $BZU2;
						
						$new_data = array(
							"Mjesec" => array(
								'<td> <p class="text-center">'.$mjesec0.'</p></td>',
								'<td> <p class="text-center">'.$mjesec1.'</p></td>',
								'<td> <p class="text-center">'.$mjesec2.'</p></td>',
								'<td> <p class="text-center"><b>UKUPNO</b></p></td>',
							),
							"PKN" => array(
								getStyle4DataTableRows($BP0,$BU0),
								getStyle4DataTableRows($BP1,$BU1),
								getStyle4DataTableRows($BP2,$BU2),
								getStyle4DataTableRows($ukBP,$ukBU)
							),
							"PN" => array(
								getStyle4DataTableRows($BZU0,$BU0),
								getStyle4DataTableRows($BZU1,$BU1),
								getStyle4DataTableRows($BZU2,$BU2),
								getStyle4DataTableRows($ukBZU,$ukBU)
							),
							"CR" => array(
								getStyle4DataTableRows($BP0,$BZU0),
								getStyle4DataTableRows($BP1,$BZU1),
								getStyle4DataTableRows($BP2,$BZU2),
								getStyle4DataTableRows($ukBP,$ukBZU)
							)
						);
					
					$myJSON = json_encode($new_data);
					echo $myJSON;
							
					
				break;
				
				case "refresh_inkaso_odjel_stat":
					$selected =	$_REQUEST['selected'];
					$period = $_REQUEST['period'];

					$period = explode (" to ", $period);

					$datum_od = date("Y-m-d", strtotime($period[0]));
					$datum_do = date("Y-m-d", strtotime($period[1]));
					
					$uslov_period = "BETWEEN '".$datum_od."' AND '".$datum_do."'";
					$uslov_selected = "(".implode (",",$selected).")";
					
					$query_inkaso_stat = $db->prepare("
						SELECT  emp.employee_id, emp.employee_firstname, emp.employee_lastname,
								sum(case 
									when bilj.status_biljeska_nd = 3 
									AND bilj.tip_biljeska_nd NOT IN (3,1) 
									AND date(bilj.vrijeme_dodavanja_biljeska_nd) ".$uslov_period."
									THEN 1 ELSE 0 END) as BP,
								sum(case 
									when pred.pr_uplaceno = 1  
									AND bilj.status_biljeska_nd = 3 
									AND bilj.tip_biljeska_nd = 5 
									AND date(pred.pr_datum_uplate) ".$uslov_period."
									THEN 1 ELSE 0 END) as BU,
								sum(case 
									when pred.pr_uplaceno = 1  
									AND bilj.status_biljeska_nd = 3 
									AND bilj.tip_biljeska_nd = 5 
									AND date(pred.pr_datum_uplate) ".$uslov_period."
									AND date(bilj.vrijeme_dodavanja_biljeska_nd) ".$uslov_period."
									THEN 1 ELSE 0 END) as BO
									
									
								from idk_predracuni pred 
								JOIN idk_nd_kandidata_biljeske bilj 
								ON pred.pr_id = bilj.predracun_id
								JOIN idk_employees emp
								ON bilj.dodao_zaposlenik_biljeska_nd = emp.employee_id
								WHERE emp.employee_id IN ".$uslov_selected."
								GROUP BY (bilj.dodao_zaposlenik_biljeska_nd)
					");
					
					$query_inkaso_stat -> execute();
					$ukBP = 0;
					$ukBU = 0;
					$ukBO = 0;
					$nizName = array();
					$nizBP = array();
					$nizBU = array();
					$nizBO = array();
					while ($row_inkaso_stat = $query_inkaso_stat->fetch()){
						$employee_id = $row_inkaso_stat['employee_id'];
						$emp_name = $row_inkaso_stat['employee_firstname']." ".$row_inkaso_stat['employee_lastname'];
						$BP = $row_inkaso_stat['BP'];
						$BU = $row_inkaso_stat['BU'];
						$BO = $row_inkaso_stat['BO'];
						$ukBP = $ukBP + $BP;
						$ukBU = $ukBU + $BU;
						$ukBO = $ukBO + $BO;
						array_push($nizBP, '<div class="text-center"><a class="a_link" target="_blank" href = "benjo_test.php?page=inkaso_list_kandidate&date_od='.$datum_od.'&date_do='.$datum_do.'&employee_id='.$employee_id.'&mode=BP">'.$BP.'</a></div>');
						array_push($nizBU, '<div class="text-center"><a class="a_link" target="_blank" href = "benjo_test.php?page=inkaso_list_kandidate&date_od='.$datum_od.'&date_do='.$datum_do.'&employee_id='.$employee_id.'&mode=BU">'.$BU.'</a></div>');
						array_push($nizBO, '<div class="text-center"><a class="a_link" target="_blank" href = "benjo_test.php?page=inkaso_list_kandidate&date_od='.$datum_od.'&date_do='.$datum_do.'&employee_id='.$employee_id.'&mode=BO">'.$BO.'</a></div>');
						array_push($nizName, '<div class="text-center">'.$emp_name.'</div>');
					}
					
						array_push($nizBP, '<div class="text-center" style="color:#038103; background-color:#D2C9E4; border-style:solid; border-color:#038103; border-radius: 5px; border-width: 1px;"><a class="a_h_link" target="_blank" href = "benjo_test.php?page=inkaso_list_kandidate&date_od='.$datum_od.'&date_do='.$datum_do.'&employee_id=0&mode=BP"><b>'.$ukBP.'</b></a></div>');
						array_push($nizBU, '<div class="text-center" style="color:#038103; background-color:#D2C9E4; border-style:solid; border-color:#038103; border-radius: 5px; border-width: 1px;"><a class="a_h_link" target="_blank" href = "benjo_test.php?page=inkaso_list_kandidate&date_od='.$datum_od.'&date_do='.$datum_do.'&employee_id=0&mode=BU"><b>'.$ukBU.'</b></a></div>');
						array_push($nizBO, '<div class="text-center" style="color:#038103; background-color:#D2C9E4; border-style:solid; border-color:#038103; border-radius: 5px; border-width: 1px;"><a class="a_h_link" target="_blank" href = "benjo_test.php?page=inkaso_list_kandidate&date_od='.$datum_od.'&date_do='.$datum_do.'&employee_id=0&mode=BO"><b>'.$ukBO.'</b></a></div>');
						array_push($nizName, '<div class="text-center" style="color:#038103; background-color:#D2C9E4; border-style:solid; border-color:#038103; border-radius: 5px; border-width: 1px;"><b>UKUPNO</b></div>');
					$new_data = array(
							"Agent" => $nizName,
							"BP" => $nizBP,
							"BU" => $nizBU,
							"BO" => $nizBO
						);
					
					$myJSON = json_encode($new_data);
					echo $myJSON;
					
				break;
				
				case "refresh_list_kandidati":

					$ids = $_REQUEST['selected'];
					$dana = $_REQUEST['dana'];
					$tip = $_REQUEST['tip'];
					$ids = implode(",",$ids);
					if($tip == "PR"){
						$query_get_info=$db->prepare('
							SELECT pred.pr_zaposlenik, pred.pr_kandidat_id, kan.vrsta_ugovora_nd_kandidata, pred.pr_datum_kreiranja, 
							pred.pr_broj_predracuna, pred.pr_file, pred.pr_status, kan.ime_nd_kandidata AS ime, kan.prezime_nd_kandidata AS prezime, 
							pred.pr_datum_uplate, emp.employee_firstname, emp.employee_lastname 
							FROM idk_predracuni pred 
							JOIN idk_nd_kandidata kan 
							ON pred.pr_kandidat_id = kan.id_broj_nd_kandidata 
							JOIN idk_employees emp 
							ON emp.employee_id = pred.pr_zaposlenik 
							WHERE date(pr_datum_kreiranja) BETWEEN date(curdate() - interval '.$dana.' day) and date(curdate() - interval 1 day) 
							AND pr_rata = 1 
							AND pr_status != 0 
							AND pr_vrsta_predracuna = 1
							AND emp.employee_id IN ('.$ids.')
						');
					}
					else if($tip == "UP"){
						$query_get_info=$db->prepare('
							SELECT pred.pr_zaposlenik, pred.pr_kandidat_id, kan.vrsta_ugovora_nd_kandidata, pred.pr_datum_kreiranja, 
							pred.pr_broj_predracuna, pred.pr_file, pred.pr_status, kan.ime_nd_kandidata AS ime, kan.prezime_nd_kandidata AS prezime, 
							pred.pr_datum_uplate, emp.employee_firstname, emp.employee_lastname 
							FROM idk_predracuni pred 
							JOIN idk_nd_kandidata kan 
							ON pred.pr_kandidat_id = kan.id_broj_nd_kandidata 
							JOIN idk_employees emp 
							ON emp.employee_id = pred.pr_zaposlenik 
							WHERE date(pr_datum_uplate) BETWEEN date(curdate() - interval '.$dana.' day) and date(curdate() - interval 1 day) 
							AND pr_rata = 1 
							AND pr_status != 0 
							AND pr_vrsta_predracuna = 1
							AND pr_uplaceno = 1
							AND emp.employee_id IN ('.$ids.')
						');
					}
					
					$query_get_info -> execute();
					
					$nizKandidat = array();
					$nizAgent = array();
					$nizNazivPr = array();
					$nizDatumKr = array();
					$nizVrstaUg = array();
					$nizStatus = array();
					$nizDatumUplate = array();

					while($row_get_info = $query_get_info->fetch()){
									
						$id_zaposlenik=$row_get_info['pr_zaposlenik'];
						$id_kan=$row_get_info['pr_kandidat_id'];
						$status_predr = $row_get_info['pr_status'];
						$pr_file = $row_get_info['pr_file'];
						$ime_kandidata = $row_get_info['ime'];
						$prezime_kandidata = $row_get_info['prezime'];
						$ime_zaposlenika = $row_get_info['employee_firstname'];
						$prezime_zaposlenika = $row_get_info['employee_lastname'];
						$br_predracuna = $row_get_info['pr_broj_predracuna'];
						$datum_kreiranja = substr($row_get_info['pr_datum_kreiranja'],0,10);
						$vrsta_ugovora_kandidata_ispis_otvoren = $row_get_info['vrsta_ugovora_nd_kandidata'];
						$datum_uplate = $row_get_info['pr_datum_uplate'];
						$datum_kreiranja = date("d.m.Y",strtotime($datum_kreiranja));
						// $datum_uplate = date("d.m.Y",strtotime($datum_uplate));
						

						
						array_push($nizKandidat, $ime_kandidata.' '.$prezime_kandidata);
						array_push($nizAgent, $ime_zaposlenika.' '.$prezime_zaposlenika);
						array_push($nizNazivPr, '<div class="text-center">'.$br_predracuna.'</div>');
						array_push($nizDatumKr, '<div class="text-center">'.$datum_kreiranja.'</div>');
						
						if($status_predr == 0){
							$status_show = '<td class="text-center"><span class="label label-warning material-label material-label_warning main-container__column ">Arhiviran</span></td>';
						}else if($status_predr == 1){
							$status_show = '<td class="text-center"><span class="label label-info material-label material-label_info main-container__column">Poslan</span></td>';
						}else if($status_predr == 2){
							$status_show = '<td class="text-center"><span class="label label-success material-label material-label_success main-container__column">Uplaćen na: '.date("d.m.Y", strtotime($datum_uplate)).'</span></td>';
						}else if($status_predr == 3){
							$status_show = '<td class="text-center"><span class="label label-danger material-label material-label_danger main-container__column">In Caso 1</span></td>';
						}else if($status_predr == 4){
							$status_show = '<td class="text-center"><span class="label label-danger material-label material-label_danger main-container__column">In Caso 2</span></td>';
						}else if($status_predr == 5){
							$status_show = '<td class="text-center"><span class="label label-danger material-label material-label_danger main-container__column">In Caso 3</span></td>';
						}
						
						array_push($nizStatus, '<div class="text-center">'.$status_show.'</div>');

						if($vrsta_ugovora_kandidata_ispis_otvoren == 1){
							$vrsta_ugovora = '<td class="text-center">Ugovor bez popusta na 2 rate!</td>';
						}
						else if($vrsta_ugovora_kandidata_ispis_otvoren == 2){
							$vrsta_ugovora = '<td class="text-center">Ugovor sa popustom na 2 rate!</td>';
						}
						else if($vrsta_ugovora_kandidata_ispis_otvoren == 3){
							$vrsta_ugovora = '<td class="text-center">Ugovor bez popusta na 5 rata!</td>';
						}
						else if($vrsta_ugovora_kandidata_ispis_otvoren == 4){
							$vrsta_ugovora = '<td class="text-center">Ugovor sa popustom na 5 rata!</td>';
						}
						else if($vrsta_ugovora_kandidata_ispis_otvoren == 5){
							$vrsta_ugovora = '<td class="text-center">Ugovor bez popusta na 3 rate!</td>';
						}
						else if($vrsta_ugovora_kandidata_ispis_otvoren == 6){
							$vrsta_ugovora = '<td class="text-center">Ugovor sa popustom na 3 rate!</td>';
						}
						else if($vrsta_ugovora_kandidata_ispis_otvoren == 7){
							$vrsta_ugovora = '<td class="text-center">Ugovor bez popusta na 4 rate!</td>';
						}
						else if($vrsta_ugovora_kandidata_ispis_otvoren == 8){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor sa popustom na 4 rate!</td>';
						}
						else if($vrsta_ugovora_kandidata_ispis_otvoren == 9){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor bez popusta na 1 ratu!</td>';
						}
						else if($vrsta_ugovora_kandidata_ispis_otvoren == 10){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor sa popustom na 1 ratu!</td>';
						}
						else if($vrsta_ugovora_kandidata_ispis_otvoren == 11){ 
							$vrsta_ugovora = '<td class="text-center">Mikrofin ugovor bez popusta!</td>';
						}
						else if($vrsta_ugovora_kandidata_ispis_otvoren == 12){ 
							$vrsta_ugovora = '<td class="text-center">Mikrofin ugovor sa popustom!</td>';
						}
						else if($vrsta_ugovora_kandidata_ispis_otvoren == 21){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor sa 20% popusta na 1 ratu!</td>';
						}
						else if($vrsta_ugovora_kandidata_ispis_otvoren == 22){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor sa 20% popusta na 2 rate!</td>';
						}
						else if($vrsta_ugovora_kandidata_ispis_otvoren == 23){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor sa 20% popusta na 3 rate!</td>';
						}
						else if($vrsta_ugovora_kandidata_ispis_otvoren == 24){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor sa 20% popusta na 4 rate!</td>';
						}
						else if($vrsta_ugovora_kandidata_ispis_otvoren == 25){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor sa 20% popusta na 5 rata!</td>';
						}else if($vrsta_ugovora_kandidata_ispis_otvoren == 71){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor za struke na 1 ratu sa 30% popusta!</td>';
						}else if($vrsta_ugovora_kandidata_ispis_otvoren == 72){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor za struke na 2 rate sa 30% popusta!</td>';
						}else if($vrsta_ugovora_kandidata_ispis_otvoren == 73){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor za struke na 3 rate sa 30% popusta!</td>';
						}else if($vrsta_ugovora_kandidata_ispis_otvoren == 74){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor za struke na 4 rate sa 30% popusta!</td>';
						}else if($vrsta_ugovora_kandidata_ispis_otvoren == 75){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor za struke na 5 rata sa 30% popusta!</td>';
						}else if($vrsta_ugovora_kandidata_ispis_otvoren == 51){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor za struke na 1 ratu sa 50% popusta!</td>';
						}else if($vrsta_ugovora_kandidata_ispis_otvoren == 52){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor za struke na 2 rate sa 50% popusta!</td>';
						}else if($vrsta_ugovora_kandidata_ispis_otvoren == 53){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor za struke na 3 rate sa 50% popusta!</td>';
						}else if($vrsta_ugovora_kandidata_ispis_otvoren == 54){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor za struke na 4 rate sa 50% popusta!</td>';
						}else if($vrsta_ugovora_kandidata_ispis_otvoren == 55){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor za struke na 5 rata sa 50% popusta!</td>';
						}else if($vrsta_ugovora_kandidata_ispis_otvoren == 99){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor za struke na 1 ratu sa 100% popusta!</td>';
						}else if($vrsta_ugovora_kandidata_ispis_otvoren == 41){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor za struke na 1 ratu sa 70% popusta!</td>';
						}else if($vrsta_ugovora_kandidata_ispis_otvoren == 42){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor za struke na 2 rate sa 70% popusta!</td>';
						}else if($vrsta_ugovora_kandidata_ispis_otvoren == 43){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor za struke na 3 rate sa 70% popusta!</td>';
						}else if($vrsta_ugovora_kandidata_ispis_otvoren == 44){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor za struke na 4 rate sa 70% popusta!</td>';
						}else if($vrsta_ugovora_kandidata_ispis_otvoren == 45){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor za struke na 5 rata sa 70% popusta!</td>';
						}else if($vrsta_ugovora_kandidata_ispis_otvoren == 61){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor za struke na 1 ratu sa 20% popusta!</td>';
						}else if($vrsta_ugovora_kandidata_ispis_otvoren == 62){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor za struke na 2 rate sa 20% popusta!</td>';
						}else if($vrsta_ugovora_kandidata_ispis_otvoren == 63){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor za struke na 3 rate sa 20% popusta!</td>';
						}else if($vrsta_ugovora_kandidata_ispis_otvoren == 64){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor za struke na 4 rate sa 20% popusta!</td>';
						}else if($vrsta_ugovora_kandidata_ispis_otvoren == 65){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor za struke na 5 rata sa 20% popusta!</td>';
						}else if($vrsta_ugovora_kandidata_ispis_otvoren == 82){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor sa popustom 10% na 2 rate!</td>';
						}else if($vrsta_ugovora_kandidata_ispis_otvoren == 83){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor sa popustom 10% na 3 rate!</td>';
						}else if($vrsta_ugovora_kandidata_ispis_otvoren == 84){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor sa popustom 10% na 4 rate!</td>';
						}else if($vrsta_ugovora_kandidata_ispis_otvoren == 85){ 
							$vrsta_ugovora = '<td class="text-center">Ugovor sa popustom 10% na 5 rata!</td>';
						}

						array_push($nizVrstaUg, '<div class="text-center">'.$vrsta_ugovora.'</div>');
						array_push($nizDatumUplate, $datum_uplate);
					}
					$new_data = array(
						'kandidat' => $nizKandidat,
						'agent' => $nizAgent,
						'naziv_predracuna' => $nizNazivPr,
						'datum_kreiranja' => $nizDatumKr,
						'status' => $nizStatus,
						'vrsta_ugovora' => $nizVrstaUg,	
						'datum_uplate' => $nizDatumUplate
					);

					$myJSON = json_encode($new_data);

					echo $myJSON;
				break;
				
				case "updateStatusPP":
					if(isset($_POST["kandidat_id"])){
						$id_kan = $_POST["kandidat_id"];
						updateStatusPonovnaPrijavaDIPL($id_kan);
					}
				break;
				
				case "posaljiDocumentSaTemplate":
					$ustanovaID = intval($_POST["ustanovaID"]);
	
					if($ustanovaID != 0){
						$query = $db->prepare("
							SELECT id_tip_dokumenta_ustanove_nd, naziv_tip_dokumenta_ustanove_nd
							FROM idk_nd_ustanove_tip_dokumenta
							WHERE idd_ustanove_nd = ".$ustanovaID." AND template_naziv_dokumenta_ustanove_nd is not null AND template_dokumenta_ustanove_nd = 1
						");
						$query->execute();
						while($row = $query->fetch()){
							echo '<option value = "'.$row["id_tip_dokumenta_ustanove_nd"].'">'.$row["naziv_tip_dokumenta_ustanove_nd"].'</option>';
						}
					}
				break;
				
				case "posaljiDocumentSaTemplatePregled":
					$documentID = intval($_POST["documentID"]);
	
					if($documentID != 0){
						$query = $db->prepare("
							SELECT template_naziv_dokumenta_ustanove_nd
							FROM idk_nd_ustanove_tip_dokumenta
							WHERE id_tip_dokumenta_ustanove_nd = ".$documentID." AND template_naziv_dokumenta_ustanove_nd is not null AND template_dokumenta_ustanove_nd = 1
						");
						$query->execute();
						$row = $query->fetch();
						
						echo '
							<div class="form-group">
								<div class="col-md-offset-2 col-sm-8">
									<div class="alert alert-danger text-center" role="alert">
										<span style = "font-weight: bold;">Provjerite ispravnost dokumenta!</span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="col-md-offset-2 col-sm-8 text-center">
									<a href="'.getSiteUrlr().'files/dokumenti_ND_ustanove/'.$row["template_naziv_dokumenta_ustanove_nd"].'" class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK">
										<i class="fa fa-file-pdf-o fa-4x" aria-hidden="true"></i>
									</a>
								</div>
							</div>
						';
					}
				break;
				
				case "razloziOdbijanjaUsluge":
					//111Adis222 2 START
					$tipRazloga = $_POST["tipRazloga"];
					$uslov = "";
					if(isset($tipRazloga)){
						if($tipRazloga == 1){
							$uslov = " ponovno_zvanje_ro = 1 AND br_dana_ro is not null ";
						}else{
							$uslov = " ponovno_zvanje_ro = 0 AND br_dana_ro is null ";
						}
						
						if($uslov != ""){
							$query = $db->prepare("
								SELECT id_ro, naziv_ro_bs, ponovno_zvanje_ro, br_dana_ro
								FROM idk_ro_usluge
								WHERE tip_ro = 1 AND status_ro = 1 AND ".$uslov."
								ORDER BY id_ro ASC
							");
							$query->execute();
							while($row = $query->fetch()){
								$id_ro = $row["id_ro"];
								$naziv_ro_bs = $row["naziv_ro_bs"];
								if($row["ponovno_zvanje_ro"] == 1){
									$dana = "";
									if($row["br_dana_ro"] < 2){
										$dana = "dan.";
									}else{
										$dana = "dana.";
									}
									$ponovno_zvanje_ro = "Pozvati nakon ".$row["br_dana_ro"]." ".$dana;
								}else{
									$ponovno_zvanje_ro = "Kandidat ide u arhivu.";
								}
								
								echo '<option value = "'.$id_ro.'" data-subtext = "'.$ponovno_zvanje_ro.'">'.$naziv_ro_bs.'</option>';
							}
						}else{
							echo '<option value = "" disabled>Nije moguće dohvatiti razloge.</option>';
						}
					}else{
						echo '<option value = "" disabled>Nije moguće dohvatiti razloge.</option>';
					}
					//111Adis222 2 END
				break;
				
				/**
				*	PRIKAZI/POZOVI SLJEDECEG KANDIDATA AJAX
				*/
				case "generateNextCandidate":
					
					$kandidat_id = getNextCandidate();

					if($kandidat_id)
						echo $kandidat_id;
					else{
						echo 'Nema dostupnih kandidata'; 
					}
					
				break;
				/**
				*	PRIKAZI/POZOVI SLJEDECEG KANDIDATA SERVERCALL
				*/
				case "generateNextCandidateLink":
					
					$kandidat_id = getNextCandidate();

					if($kandidat_id)
						header("Location: /nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$kandidat_id);
					else
						header("Location: /");
					
				break;
				//case koji vraća tabelu fajlu modul_statistike.php u kojoj je ispisana statistika inkaso agenata po biljeskama START
				case "get_inkaso_statistika":
					$period = $_REQUEST['period'];
					$odabrani_agenti = $_REQUEST['odabrani_agenti'];
					// var_dump($odabrani_agenti);
					// exit();
					if(strpos($period, ' to ') !== false) {
						$period = explode (' to ', $period);
						$datum_od = date("Y-m-d",strtotime($period[0]));
						$datum_do = date("Y-m-d", strtotime($period[1].'+1 day'));						
					}
					else{
						$datum_od = date("Y-m-d",strtotime($period));
						$datum_do = date("Y-m-d", strtotime($period.'+1 day'));	
					}
					$odabrani_agenti = implode(',',$odabrani_agenti);
					$query_get_broj_kontaktiranih_predracuna = $db -> prepare("
						SELECT max(bilj.id_biljeska_nd) as max
						FROM idk_nd_kandidata_biljeske bilj
						JOIN idk_employees emp
						ON emp.employee_id = bilj.dodao_zaposlenik_biljeska_nd
						WHERE bilj.vrijeme_dodavanja_biljeska_nd BETWEEN ('".$datum_od."') AND '".$datum_do."'
						AND emp.employee_id IN (".$odabrani_agenti.")
						AND bilj.status_biljeska_nd = 3
						GROUP BY(bilj.predracun_id)
					
					");
					$query_get_broj_kontaktiranih_predracuna -> execute();
					$ukupni_broj_unikatnih_predracuna = $query_get_broj_kontaktiranih_predracuna->rowCount();
					
					$query_get_max_3_5_biljeske = $db->prepare("
						SELECT max(bilj.id_biljeska_nd) as max
						FROM idk_nd_kandidata_biljeske bilj
						WHERE bilj.vrijeme_dodavanja_biljeska_nd BETWEEN ('".$datum_od."') AND '".$datum_do."'
						AND bilj.status_biljeska_nd = 3
						AND bilj.tip_biljeska_nd = 5
						GROUP BY(bilj.predracun_id)
					");
					$query_get_max_3_5_biljeske -> execute();
					$array_max_3_5_biljeske = array();
					while($row_get_max_3_5_biljeske = $query_get_max_3_5_biljeske -> fetch()){
						array_push($array_max_3_5_biljeske, $row_get_max_3_5_biljeske['max']);
					}
					$string_max_3_5_biljeske = implode(',',$array_max_3_5_biljeske);
					$query_get_inkaso_stat = $db->prepare("
						SELECT 
						emp.employee_firstname, emp.employee_lastname, emp.employee_id,
						SUM(CASE WHEN  bilj.tip_biljeska_nd = 2 THEN 1 ELSE 0 END) AS cnt_ne_javlja_se,
						SUM(CASE WHEN  bilj.tip_biljeska_nd = 3 THEN 1 ELSE 0 END) AS cnt_pozovi_kasnije,
						SUM(CASE WHEN  bilj.tip_biljeska_nd = 5 THEN 1 ELSE 0 END) AS cnt_uplata_na_dan,
						SUM(CASE WHEN  bilj.tip_biljeska_nd = 6 THEN 1 ELSE 0 END) AS cnt_promjena_ugovora,
						SUM(CASE WHEN  bilj.tip_biljeska_nd = 4 THEN 1 ELSE 0 END) AS cnt_arhiviran,
						SUM(CASE WHEN (bilj.tip_biljeska_nd = 5 
										AND pred.pr_uplaceno = 1 
										AND pred.pr_status is not NULL 
										AND pred.pr_vrsta_predracuna = 1
										AND bilj.id_biljeska_nd IN (
												".$string_max_3_5_biljeske."
											)
										) 
									THEN 1 
									ELSE 0 
									END) AS cnt_uplate,
						COUNT(DISTINCT(bilj.predracun_id)) as cnt_prozvani_predracuni


						FROM idk_nd_kandidata_biljeske bilj
						JOIN idk_employees emp
						ON emp.employee_id = bilj.dodao_zaposlenik_biljeska_nd
						JOIN idk_predracuni pred
						ON pred.pr_id = bilj.predracun_id

						WHERE bilj.status_biljeska_nd = 3
						AND bilj.vrijeme_dodavanja_biljeska_nd BETWEEN '".$datum_od."' AND '".$datum_do."'
						AND emp.employee_id IN (".$odabrani_agenti.")

						GROUP BY(bilj.dodao_zaposlenik_biljeska_nd)
					");

					$query_get_inkaso_stat -> execute();
					?>
					<table id = "inkaso_statistika" class="table-striped" style="width:100%;text-align:center;">
						<thead>
							<tr>
								<th>Agent</th>
								<th>Prozvani predračuni</th>
								<th>Ukupni broj poziva</th>
								<th>Ne javlja se</th>
								<th>Pozovi kasnije</th>
								<th>Uplata na dan</th>
								<th>Promjena ugovora</th>
								<th>Arhivirani</th>
								<th>Broj uplata</th>
							</tr>
						</thead>
						<tbody>
					<?php
					$ukupno_poziva               = 0;
					$ukupno_ne_javlja_se         = 0;
					$ukupno_pozovi_kasnije       = 0;
					$ukupno_promjena_ugovora     = 0;
					$ukupno_arhiviran            = 0;
					$ukupno_uplate               = 0;
					$ukupno_uplate_na_dan        = 0;
					$ukupno_prozvanih_predracuna = 0;
					while($row_get_inkaso_stat = $query_get_inkaso_stat->fetch()){
						
						$cnt_pozivi¸                  = 0;
						$employee_fullname            = $row_get_inkaso_stat['employee_firstname'].' '.$row_get_inkaso_stat['employee_lastname'];
						$employee_id             	  = $row_get_inkaso_stat['employee_id'];
						$cnt_ne_javlja_se             = $row_get_inkaso_stat['cnt_ne_javlja_se'];
						$cnt_pozovi_kasnije           = $row_get_inkaso_stat['cnt_pozovi_kasnije'];
						$cnt_uplata_na_dan            = $row_get_inkaso_stat['cnt_uplata_na_dan'];
						$cnt_promjena_ugovora         = $row_get_inkaso_stat['cnt_promjena_ugovora'];
						$cnt_arhiviran                = $row_get_inkaso_stat['cnt_arhiviran'];
						$cnt_uplate                   = $row_get_inkaso_stat['cnt_uplate'];
						$cnt_prozvani_predracuni      = $row_get_inkaso_stat['cnt_prozvani_predracuni'];
						$cnt_pozivi                   = $cnt_ne_javlja_se + $cnt_pozovi_kasnije + $cnt_uplata_na_dan + $cnt_promjena_ugovora + $cnt_arhiviran;
						
						$ukupno_poziva               += $cnt_pozivi;
						$ukupno_ne_javlja_se         += $cnt_ne_javlja_se;
						$ukupno_pozovi_kasnije       += $cnt_pozovi_kasnije;
						$ukupno_promjena_ugovora     += $cnt_promjena_ugovora;
						$ukupno_arhiviran            += $cnt_arhiviran;
						$ukupno_uplate               += $cnt_uplate;
						$ukupno_uplate_na_dan        += $cnt_uplata_na_dan;
						$ukupno_prozvanih_predracuna += $cnt_prozvani_predracuni;
						?>
						<tr>
							<th><?php echo $employee_fullname;?></th>
							<td class="td_highlight" employee_id="<?php echo $employee_id; ?>" tip="1"><?php echo $cnt_prozvani_predracuni;?></td>
							<td class="td_highlight" employee_id="<?php echo $employee_id; ?>" tip="2"><?php echo $cnt_pozivi;?></td>
							<td class="td_highlight" employee_id="<?php echo $employee_id; ?>" tip="3"><?php echo $cnt_ne_javlja_se;?></td>
							<td class="td_highlight" employee_id="<?php echo $employee_id; ?>" tip="4"><?php echo $cnt_pozovi_kasnije;?></td>
							<td class="td_highlight" employee_id="<?php echo $employee_id; ?>" tip="5"><?php echo $cnt_uplata_na_dan;?></td>
							<td class="td_highlight" employee_id="<?php echo $employee_id; ?>" tip="6"><?php echo $cnt_promjena_ugovora;?></td>
							<td class="td_highlight" employee_id="<?php echo $employee_id; ?>" tip="7"><?php echo $cnt_arhiviran;?></td>
							<td class="td_highlight" employee_id="<?php echo $employee_id; ?>" tip="8"><?php echo $cnt_uplate;?></td>
						</tr>
						<?php
						
					}
					?>
					</tbody>
					<tfoot>
						<tr>
							<th></th>
							<th class="td_highlight" employee_id="0" tip="1" style="text-align:center;"><?php echo $ukupni_broj_unikatnih_predracuna." (".$ukupno_prozvanih_predracuna.")";?></th>
							<th class="td_highlight" employee_id="0" tip="2" style="text-align:center;"><?php echo $ukupno_poziva;?></th>
							<th class="td_highlight" employee_id="0" tip="3" style="text-align:center;"><?php echo $ukupno_ne_javlja_se;?></th>
							<th class="td_highlight" employee_id="0" tip="4" style="text-align:center;"><?php echo $ukupno_pozovi_kasnije;?></th>
							<th class="td_highlight" employee_id="0" tip="5" style="text-align:center;"><?php echo $ukupno_uplate_na_dan;?></th>
							<th class="td_highlight" employee_id="0" tip="6" style="text-align:center;"><?php echo $ukupno_promjena_ugovora;?></th>
							<th class="td_highlight" employee_id="0" tip="7" style="text-align:center;"><?php echo $ukupno_arhiviran;?></th>
							<th class="td_highlight" employee_id="0" tip="8" style="text-align:center;"><?php echo $ukupno_uplate;?></th>
						</tr>
					</tfoot>
				</table>
				<?php
				break; 
				case "inkasoProvjereTipKomunikacije":
					$idKandidat = intval($_POST["idKandidat"]);
					$idPredracun = intval($_POST["idPredracun"]);
					
					if(isset($idKandidat) AND isset($idPredracun)){
						$brojInfoPredracun = 0;
						$rataPredracun = 0;
						$statusPredracun = 0;
						$queryInfoPredracun = $db->prepare("
							SELECT 
								pr_rata,
								pr_status
							FROM 
								idk_predracuni
							WHERE 
								pr_id = :pr_id
								AND 
								pr_kandidat_id = :pr_kandidat_id
						");
						$queryInfoPredracun->execute(array(
							':pr_id' => $idPredracun,
							':pr_kandidat_id' => $idKandidat
						));
						
						$brojInfoPredracun = $queryInfoPredracun->rowCount();
						
						if($brojInfoPredracun == 1){
							$rowInfoPredracun = $queryInfoPredracun->fetch();
							//Osnovni podaci o predracunu START 
							$rataPredracun = intval($rowInfoPredracun["pr_rata"]);
							$statusPredracun = intval($rowInfoPredracun["pr_status"]);
							$brojPredracuna = getBrPredracunaNDKanR($idKandidat);
							$statusKandidat = getStatusValueDIPLKandidatR($idKandidat);
							//Osnovni podaci o predracunu END 
							
							$queryTip3 = $db->prepare("
								SELECT COUNT(id_biljeska_nd) as brojTip3
								FROM idk_nd_kandidata_biljeske
								WHERE status_biljeska_nd = 3 AND tip_biljeska_nd = 3 AND predracun_id = :predracun_id AND id_kandidata_biljeska_nd = :id_kandidata_biljeska_nd
							");
							$queryTip3->execute(array(
								':predracun_id' => $idPredracun,
								':id_kandidata_biljeska_nd' => $idKandidat
							));
							$rowTip3 = $queryTip3->fetch();
							$brojTip3 = intval($rowTip3["brojTip3"]);
							
							$queryTip4 = $db->prepare("
								SELECT COUNT(id_biljeska_nd) as brojTip4
								FROM idk_nd_kandidata_biljeske
								WHERE status_biljeska_nd = 3 AND tip_biljeska_nd = 3 AND predracun_id = :predracun_id AND id_kandidata_biljeska_nd = :id_kandidata_biljeska_nd
							");
							$queryTip4->execute(array(
								':predracun_id' => $idPredracun,
								':id_kandidata_biljeska_nd' => $idKandidat
							));
							$rowTip4 = $queryTip4->fetch();
							$brojTip4 = intval($rowTip4["brojTip4"]);
							
							$queryTip5 = $db->prepare("
								SELECT dodao_zaposlenik_biljeska_nd
								FROM idk_nd_kandidata_biljeske
								WHERE status_biljeska_nd = 3 AND tip_biljeska_nd = 5 AND predracun_id = :predracun_id AND id_kandidata_biljeska_nd = :id_kandidata_biljeska_nd 
							");
							$queryTip5->execute(array(
								':predracun_id' => $idPredracun,
								':id_kandidata_biljeska_nd' => $idKandidat
							));
							if($queryTip5->rowCount() != 0){
								$rowTip5 = $queryTip5->fetch();
								$brojTip5 = $rowTip5["dodao_zaposlenik_biljeska_nd"];
							}else{
								$brojTip5 = 0;
							}
							
							//Za ispis START
							if($brojTip3 == 0 AND $brojTip4 == 0 AND $brojTip5 == 0){
								echo '<option value = "2">Neuspješan poziv</option>';
							}
								echo '<option value = "3">Pozvati kasnije</option>';
								
							if((($brojPredracuna == 1) OR ($brojPredracuna != 0 AND $rataPredracun == 1)) AND ( $statusPredracun == 3 OR $statusPredracun == 4 OR $statusPredracun == 5) AND intval($statusKandidat["status"]) == 1 AND intval($statusKandidat["podstatus"]) == 5){
							
								echo '<option value = "4">Odustaje</option>';
							}
								echo '<option value = "5">Uplata</option>';
							if((($brojPredracuna == 1) OR ($brojPredracuna != 0 AND $rataPredracun == 1)) AND ( $statusPredracun == 3 OR $statusPredracun == 4 OR $statusPredracun == 5) AND intval($statusKandidat["status"]) == 1 AND (intval($statusKandidat["podstatus"]) == 4 OR intval($statusKandidat["podstatus"]) == 5)){
								echo '<option value = "6">Promjena ugovora</option>';
							}
							$employee_status = explode( ',' , getEmployeeStatus());
							if((in_array( "1" , $employee_status))){
								//Samo administratori imaju navedenu opciju "Odustaje nakon uplate prve rate"
								if($rataPredracun != 1 AND (intval($statusKandidat["status"]) == 2 OR intval($statusKandidat["status"]) == 7 OR intval($statusKandidat["status"]) == 6)){
									//Ako se radi o predračunu
									$provjeraPoslanaPonuda = 0;
									//U ovom dijelu potrebno je napraviti kada bude dostupna ponuda na sistemu
									//Za sada je postavljeno default vrijednost 0 koja kaze da nije poslano
									$queryUplaceneRateProvjera = $db->prepare("
										SELECT 
											COUNT(pr_id) AS brojPredracuna
										FROM 
											idk_predracuni
										WHERE 
											pr_kandidat_id = :pr_kandidat_id
											AND
											pr_vrsta_predracuna = 1
											AND 
											pr_status = 2
											AND 
											pr_uplaceno = 1
									");
									$queryUplaceneRateProvjera->execute(array(
										':pr_kandidat_id' => $idKandidat
									));
									$rowUplaceneRateProvjera = $queryUplaceneRateProvjera->fetch();
									$brojUplacenihProvjera = intval($rowUplaceneRateProvjera["brojPredracuna"]);
									//Ovaj dio provjera ako je izabran predracun koji je u inkasu
									//Da li postoji prije njega generisano predračuna koji su na statusu uplaćen
									//Ako postoji takvih i nije poslana ponuda prema kandidatu
									//Onda se nudi tip komunikacije "Odustaje nakon uplate prve rate" gdje ce se svi aktivni predračuni razliciti od uplacenih arhivirati
									//Nakon toga potrebno je izdati račun za rate koje su uplacene
									
									if($brojUplacenihProvjera != 0 AND $provjeraPoslanaPonuda == 0){
										echo '<option value = "7">Odustaje nakon uplate prve rate</option>';
									}
								}
							}
							//Za ispis END 
						}
					}
				break;
				//case koji vraća tabelu fajlu modul_statistike.php u kojoj je ispisana statistika inkaso agenata po biljeskama START
				
				case "arhiviraj_nalog":
	
					$nalog_id = $_POST['nalog_id'];
					
					$query_set_nalog = $db->prepare('
						UPDATE idk_nalozi
						SET nalog_status = 12
						WHERE nalog_id = :nalog_id
					');
					
					$query_set_nalog -> execute(array(':nalog_id'=>$nalog_id));
					
					$query_insert_nalog_log = $db->prepare('
						INSERT INTO idk_nalozi_log (n_log_nalogid, n_log_employeeid, n_log_status, n_log_vrijeme)
						VALUES (:nalog_id, :employee_id, 12, CURRENT_TIME())
					');
					
					$query_insert_nalog_log -> execute(array(
						':nalog_id' 	=> $nalog_id,
						':employee_id' 	=> $logged_employee_id
					));

				break;
				
				case "omoguciUgovoreZaKandidata":
					$kanId = intval($_POST["kanId"]);
					$drzavaId = intval($_POST["drzavaId"]);
					$rezultat = "";
					if($kanId != 0 AND $drzavaId != 0){
						$rezultat = getOmoguciTipoviUgovoraDIPLK($kanId, $drzavaId);
						echo $rezultat;
					}
				break;
				
				case "provjeraInformacijaUgovor":
					$idDrzava = intval($_POST["drzavaInput"]);
					$idKandidat = intval($_POST["kanId"]);
					if($idDrzava != 0 AND $idKandidat != 0){
						$kandidatInfoQuery = $db->prepare("
							SELECT 
								skola_nd_kandidata,
								skola_smjer_nd_kandidata,
								ime_nd_kandidata, 
								prezime_nd_kandidata, 
								ulica_nd_kandidata, 
								postanski_broj_nd_kandidata, 
								grad_nd_kandidata, 
								jmbg_nd_kandidata, 
								broj_licne_karte_nd_kandidata, 
								ulica_bor_nd_kandidata,
								postanski_broj_bor_nd_kandidata, 
								grad_bor_nd_kandidata, 
								izdao_licnu_nd_kandidata,
								mobilni_nd_kandidata,
								email_nd_kandidata
							FROM 
								idk_nd_kandidata
							WHERE 
								id_broj_nd_kandidata = :id_broj_nd_kandidata
						");
						$kandidatInfoQuery->execute(array(
							':id_broj_nd_kandidata' => $idKandidat
						));
						$kandidatProvjera = $kandidatInfoQuery->rowCount();
						if($kandidatProvjera != 0){
							$kandidatInfoRow = $kandidatInfoQuery->fetch();
							$imeKan = $kandidatInfoRow["ime_nd_kandidata"];
							$prezimeKan = $kandidatInfoRow["prezime_nd_kandidata"];
							$ulicaKan = $kandidatInfoRow["ulica_nd_kandidata"];
							$postanskiKan = $kandidatInfoRow["postanski_broj_nd_kandidata"];
							$gradKan = $kandidatInfoRow["grad_nd_kandidata"];
							$maticniKan = $kandidatInfoRow["jmbg_nd_kandidata"];
							$licnaKan = $kandidatInfoRow["broj_licne_karte_nd_kandidata"];
							$ulicaBoravkaKan = $kandidatInfoRow["ulica_bor_nd_kandidata"];
							$postanskiBoravkaKan = $kandidatInfoRow["postanski_broj_bor_nd_kandidata"];
							$gradBoravkaKan = $kandidatInfoRow["grad_bor_nd_kandidata"];
							$izdaoLicnuKan = $kandidatInfoRow["izdao_licnu_nd_kandidata"];
							$mobitelKan = $kandidatInfoRow["mobilni_nd_kandidata"];
							$emailKan = $kandidatInfoRow["email_nd_kandidata"];
							$skolaKan = intval($kandidatInfoRow["skola_nd_kandidata"]);
							$smjerKan = intval($kandidatInfoRow["skola_smjer_nd_kandidata"]);
							
							if($idDrzava == 1){
								if($imeKan != NULL AND $prezimeKan != NULL AND $ulicaKan != NULL AND $postanskiKan != NULL AND $gradKan != NULL AND $maticniKan != NULL AND $licnaKan != NULL AND $skolaKan != 0 AND $smjerKan != 0 AND $mobitelKan != NULL AND $emailKan != NULL){
									echo 1;
								}else{
									echo 0;
								}
							}else if($idDrzava == 2){
								if($imeKan != NULL AND $prezimeKan != NULL AND $ulicaKan != NULL AND $postanskiKan != NULL AND $gradKan != NULL AND $maticniKan != NULL AND $licnaKan != NULL AND $skolaKan != 0 AND $smjerKan != 0 AND $mobitelKan != NULL AND $emailKan != NULL AND $ulicaBoravkaKan != NULL AND $postanskiBoravkaKan != NULL AND $gradBoravkaKan != NULL AND $izdaoLicnuKan != NULL){
									echo 1;
								}else{
									echo 0;
								}
							}else{
								if($imeKan != NULL AND $prezimeKan != NULL AND $ulicaKan != NULL AND $postanskiKan != NULL AND $gradKan != NULL AND $maticniKan != NULL AND $licnaKan != NULL AND $skolaKan != 0 AND $smjerKan != 0 AND $mobitelKan != NULL AND $emailKan != NULL){
									echo 1;
								}else{
									echo 0;
								}
							}
							
						}else{
							echo 0;
						}
					}else{
						echo 0;
					}
					
				break;
				
				case "provjeraVrstaUgovora":
					$skolaZaProvjeruUg = intval($_POST["skolaZaProvjeruUg"]);
					$smjerZaProvjeruUg = intval($_POST["smjerZaProvjeruUg"]);
					$drzavaZaProvjeruUg = intval($_POST["drzavaZaProvjeruUg"]);
					$nivoJezikaZaProvjeruUg = intval($_POST["nivoJezikaZaProvjeruUg"]); // 0 - nije oznaceno, 1 - bez znanja, 2- A1, 3-A2, 4-B1, 5-B2, 6-C1, 7-C2
					$certifikatJezikZaProvjeruUg = intval($_POST["certifikatJezikZaProvjeruUg"]); // 1 - ima, 0 - nema
					
					$omogucenoZaposlenicima = array(11,32,33,43,48,49,83,88,67,75,69); //Ovdje su ID-ovi zaposlenika kojima su omogućeni ugovori od 30 % za nase kandidate
					$arrayOption = array(); //rezultat algoritma se sprema u ovu varijablu - return u obliku implode array
					$arrayOptionImplode = '';
					$opcijeVrijednost = '';
					$smjeroviOmoguceno = array();
					$get_smjerove_za_popust = $db->prepare("SELECT ss_id FROM idk_skole_smjerovi WHERE ss_struka_id IN (1,2)");
					$get_smjerove_za_popust->execute();
					while($row_smjerovi = $get_smjerove_za_popust->fetch()){
						array_push($smjeroviOmoguceno, $row_smjerovi['ss_id']);
					}
					//$smjeroviOmoguceno = array(49,245,253,264,278,293,601,614,620,656,673,724,775,9,16,18,19,20,58,61,243,260,273,306,310,334,361,383,404,408,412,445,479,489,500,522,546,559,562,570,583,586,595,602,604,627,630,636,643,653,665,687,703,717,726,729,736,754,758,760,769,770,789); //ovdje upisati smjerove koji ulaze u obzir EMIRRRNIZ
					
					//---------------------------------------Bez popusta START
					if($drzavaZaProvjeruUg == 1){
						//Ako je drzava kandidata BiH onda se omogućuje Mikrofin - inače ne
						$opcijeVrijednost = '
							<div class="panel panel-default">
								<div class="panel-heading">
									Ugovori bez popusta
								</div>
								<div class="panel-body">
									<ul class="list-group">
										<li class="list-group-item">1 rata</li>
										<li class="list-group-item">2 rate</li>
										<li class="list-group-item">3 rate</li>
										<li class="list-group-item">4 rate</li>
										<li class="list-group-item">5 rata</li>
										<li class="list-group-item">Mikrofin</li>
									</ul>
								</div>
							</div>
						';
					}else{
						$opcijeVrijednost = '
							<div class="panel panel-default">
								<div class="panel-heading">
									Ugovori bez popusta
								</div>
								<div class="panel-body">
									<ul class="list-group">
										<li class="list-group-item">1 rata</li>
										<li class="list-group-item">2 rate</li>
										<li class="list-group-item">3 rate</li>
										<li class="list-group-item">4 rate</li>
										<li class="list-group-item">5 rata</li>
									</ul>
								</div>
							</div>
						';
					}
					//---------------------------------------Bez popusta END
					array_push($arrayOption, $opcijeVrijednost);
					$opcijeVrijednost = '';
					//---------------------------------------30 % za nase kandidate START
					if(in_array($logged_employee_id, $omogucenoZaposlenicima)){
						//Ako je drzava kandidata BiH onda se omogućuje Mikrofin - inače ne
						if($drzavaKandidat == 1){
							$opcijeVrijednost = '
								<div class="panel panel-default">
									<div class="panel-heading">
										Ugovori sa popustom 30% za naše kandidate
									</div>
									<div class="panel-body">
										<ul class="list-group">
											<li class="list-group-item">1 rata</li>
											<li class="list-group-item">2 rate</li>
											<li class="list-group-item">3 rate</li>
											<li class="list-group-item">4 rate</li>
											<li class="list-group-item">5 rata</li>
											<li class="list-group-item">Mikrofin</li>
										</ul>
									</div>
								</div>
							';
						}else{
							$opcijeVrijednost = '
								<div class="panel panel-default">
									<div class="panel-heading">
										Ugovori sa popustom 30% za naše kandidate
									</div>
									<div class="panel-body">
										<ul class="list-group">
											<li class="list-group-item">1 rata</li>
											<li class="list-group-item">2 rate</li>
											<li class="list-group-item">3 rate</li>
											<li class="list-group-item">4 rate</li>
											<li class="list-group-item">5 rata</li>
										</ul>
									</div>
								</div>
							';
						}
						array_push($arrayOption, $opcijeVrijednost);
						$opcijeVrijednost = '';
					}
					//---------------------------------------30 % za nase kandidate END
						
					//---------------------------------------NOVI UGOVORI START
					$skolaKan = intval($skolaZaProvjeruUg);
					$smjerSkolaKan = intval($smjerZaProvjeruUg);
					$nivoJezikaKan = intval($nivoJezikaZaProvjeruUg); // 0 - nije oznaceno, 1 - bez znanja, 2- A1, 3-A2, 4-B1, 5-B2, 6-C1, 7-C2
					$certifikatKan = intval($certifikatJezikZaProvjeruUg); // 1 - ima, 0 - nema
					//Provjerava se da li kandidat ima određeni smjer - ako ima idi dalje, inače nista
					if(in_array($smjerSkolaKan, $smjeroviOmoguceno)){
						//provjerava se da li kandidat ima certifikat ili nema
						if($certifikatKan == 1){
							//provjeravaju se nivoi jezika 
							if($nivoJezikaKan == 2){
								//Ako je A1 onda su ugovori od "71" do "75" - 30 %
								$opcijeVrijednost = '
									<div class="panel panel-default">
										<div class="panel-heading">
											Ugovori sa popustom od 30% za kandidate sa A1 certifikatom
										</div>
										<div class="panel-body">
											<ul class="list-group">
												<li class="list-group-item">1 rata</li>
												<li class="list-group-item">2 rate</li>
												<li class="list-group-item">3 rate</li>
												<li class="list-group-item">4 rate</li>
												<li class="list-group-item">5 rata</li>
											</ul>
										</div>
									</div>
								';
							}else if($nivoJezikaKan == 3){
								//Ako je A2 onda su ugovori od "51" do "55" - 50 %
								$opcijeVrijednost = '
									<div class="panel panel-default">
										<div class="panel-heading">
											Ugovori sa popustom od 50% za kandidate sa A2 certifikatom
										</div>
										<div class="panel-body">
											<ul class="list-group">
												<li class="list-group-item">1 rata</li>
												<li class="list-group-item">2 rate</li>
												<li class="list-group-item">3 rate</li>
												<li class="list-group-item">4 rate</li>
												<li class="list-group-item">5 rata</li>
											</ul>
										</div>
									</div>
								';
							}else if($nivoJezikaKan == 4 OR $nivoJezikaKan == 5 OR $nivoJezikaKan == 6 OR $nivoJezikaKan == 7){
								
								if($logged_employee_id == 49){
									//Ako je B1,B2,C1,C2 i zaposlenik je Emina Cehic onda je ugovor "99" - 100 %
									$opcijeVrijednost = '
										<div class="panel panel-default">
											<div class="panel-heading">
												Ugovor sa popustom od 100% za kandidate sa B1,B2,C1,C2 certifikatom
											</div>
											<div class="panel-body">
												<ul class="list-group">
													<li class="list-group-item">1 rata</li>
												</ul>
											</div>
										</div>
									';
								}else{
									//Ako je B1,B2,C1,C2 onda su ugovori od "41" do "45" - 70 %
									$opcijeVrijednost = '
										<div class="panel panel-default">
											<div class="panel-heading">
												Ugovori sa popustom od 70% za kandidate sa B1,B2,C1,C2 certifikatom
											</div>
											<div class="panel-body">
												<ul class="list-group">
													<li class="list-group-item">1 rata</li>
													<li class="list-group-item">2 rate</li>
													<li class="list-group-item">3 rate</li>
													<li class="list-group-item">4 rate</li>
													<li class="list-group-item">5 rata</li>
												</ul>
											</div>
										</div>
									';
								}
							}else{
								$opcijeVrijednost = '';
							}
							
						}else{
							//Kandidat nema certifikata i dobija mogućnost 20% popusta
							$opcijeVrijednost = '
								<div class="panel panel-default">
									<div class="panel-heading">
										Ugovori sa popustom od 20% za kandidate bez certifikata
									</div>
									<div class="panel-body">
										<ul class="list-group">
											<li class="list-group-item">1 rata</li>
											<li class="list-group-item">2 rate</li>
											<li class="list-group-item">3 rate</li>
											<li class="list-group-item">4 rate</li>
											<li class="list-group-item">5 rata</li>
										</ul>
									</div>
								</div>
							';
						}
						array_push($arrayOption, $opcijeVrijednost);
						$opcijeVrijednost = '';
					}
					//---------------------------------------NOVI UGOVORI END 
					$arrayOptionImplode = implode("", $arrayOption);
					echo $arrayOptionImplode;
				break;
				
				case "get_financije_ch_predracuni":

					$filter_kreiran_predracun_period 	= $_REQUEST['filter_kreiran_predracun_period'];
					$filter_uplacen_predracun_period 	= $_REQUEST['filter_uplacen_predracun_period'];
					$filter_select_is_uplaceno 			= $_REQUEST['filter_select_is_uplaceno'];
					$filter_select_rate 				= $_REQUEST['filter_select_rate'];
					$filter_select_status_predracuna	= $_REQUEST['filter_select_status_predracuna'];
					$filter_select_drzavu 				= $_REQUEST['filter_select_drzavu'];
					
					$flag_vise_drzava 					= false;
					$uslov_vrijeme_kreiranja_predracuna = "";
					$uslov_vrijeme_uplate_predracuna 	= "";
					$uslov_is_uplacen 					= "";
					$uslov_rata 						= "";
					$uslov_status 						= "";
					$uslov_drzava						= "";

					//ubacen datum kreiranja uplatnice zbog filtera, da nakon filitrianja i odabira srbije ne ispise predracune sa novom uplatnicom(koja se treba gledati kao ostalo a ne srbija)
					//problem ostaje kada se odaberu srbija i bih i dalje ce ispisati predracune koje ne treba
					if(count($filter_select_drzavu) > 1){
						$flag_vise_drzava = true;
					}else{
						$flag_vise_drzava = false;
					}

					if($filter_select_drzavu != null){
						$uslov_drzava = "AND pr.pr_domaca_valuta IN (".implode(',',$filter_select_drzavu).")"; 
					}else{
						$uslov_drzava = "";
					}
					
					if(strpos($filter_kreiran_predracun_period, "to")){
						$tmp_period = array();
						$tmp_period = explode('to', $filter_kreiran_predracun_period);
						$filter_kreiran_predracun_datum_od = date("Y-m-d", strtotime($tmp_period[0]));
						$filter_kreiran_predracun_datum_do = date("Y-m-d", strtotime($tmp_period[1]));
						$uslov_vrijeme_kreiranja_predracuna = " AND pr.pr_datum_kreiranja BETWEEN '".$filter_kreiran_predracun_datum_od."' AND '".$filter_kreiran_predracun_datum_do."'";
					}
					else if($filter_kreiran_predracun_period != ""){
						$uslov_vrijeme_kreiranja_predracuna = " AND pr.pr_datum_kreiranja = ".date("d.m.Y",strtotime($filter_kreiran_predracun_period));
					}
					

					if(strpos($filter_uplacen_predracun_period, "to")){
						$tmp_period = array();
						$tmp_period = explode('to', $filter_uplacen_predracun_period);
						$filter_uplacen_predracun_datum_od = date("Y-m-d", strtotime($tmp_period[0]));
						$filter_uplacen_predracun_datum_do = date("Y-m-d", strtotime($tmp_period[1]));
						$uslov_vrijeme_uplate_predracuna = " AND pr.pr_datum_uplate BETWEEN '".$filter_uplacen_predracun_datum_od."' AND '".$filter_uplacen_predracun_datum_do."'";
					}
					else if($filter_uplacen_predracun_period != ""){
						$uslov_vrijeme_uplate_predracuna = " AND pr.pr_datum_uplate = ".date("d.m.Y",strtotime($filter_uplacen_predracun_period));
					}

					if(in_array('0',$filter_select_is_uplaceno) && !in_array('1',$filter_select_is_uplaceno)){
						$uslov_is_uplacen = "AND pr.pr_uplaceno = 0";
					}
					else if(!in_array('0',$filter_select_is_uplaceno) && in_array('1',$filter_select_is_uplaceno)){
						$uslov_is_uplacen = "AND pr.pr_uplaceno = 1";
					}
					
					$uslov_rata = "AND pr.pr_rata IN (".implode(',',$filter_select_rate).")";

					$uslov_status = "AND pr.pr_status IN (".implode(',',$filter_select_status_predracuna).")";
										
					$query_get_predracune = $db->prepare('
						SELECT pr.*, kan.ime_nd_kandidata, kan.prezime_nd_kandidata, kan.id_broj_nd_kandidata
						FROM idk_predracuni pr
						JOIN idk_nd_kandidata kan
						ON kan.id_broj_nd_kandidata = pr.pr_kandidat_id
						WHERE pr.pr_naplata_preko = 1
						AND pr.pr_file IS NOT NULL
						'.$uslov_vrijeme_kreiranja_predracuna.'
						'.$uslov_vrijeme_uplate_predracuna.'
						'.$uslov_is_uplacen.'
						'.$uslov_rata.'
						'.$uslov_status.'
						'.$uslov_drzava.'
					');
					// var_dump($query_get_predracune);
					// exit();
					$query_get_predracune -> execute();
					$brojac = 0;
					?>
					<style>
						.racun_file{
							font_size 	: 15px!important; 
							color 		: #5cb85c; 
							display 	: none; 
							margin 		: auto;
						}
						.racun_file:hover{
							color: rgb(80 255 0) !important;
							text-decoration: none !important;
							cursor: pointer;
						}
					</style>
					<table id="table_predracuni_ch" class="striped col-12">
						<thead>
							<th>#</th>
							<th>Naiv</th>
							<?php
							if($flag_vise_drzava){
								?><th>Drzava</th><?php								
							}
							?>
							<th>Broj</th>
							<th>Datum kreiranja</th>
							<th>Rata</th>
							<th>Vrijednost</th>
							<th>Status</th>
							<th>Uplaćeno</th>
							<th>Akcija</th>
						</thead>
						<tbody>
					<?php

						$total_uplaceno 		= 0;
						$total_neuplaceno 		= 0;
						$total_inakso_1			= 0;
						$total_inakso_2			= 0;
						$total_inakso_3			= 0;

					while($row_get_predracune = $query_get_predracune -> fetch()){
						
						$brojac 				= $brojac + 1;
						$pr_id 					= $row_get_predracune['pr_id'];
						$pr_broj_predracuna 	= $row_get_predracune['pr_broj_predracuna'];
						$pr_datum_kreiranja 	= $row_get_predracune['pr_datum_kreiranja'];
						$pr_vrsta_predracuna 	= $row_get_predracune['pr_vrsta_predracuna'];
						$pr_rata 				= $row_get_predracune['pr_rata'];
						$pr_status 				= $row_get_predracune['pr_status'];
						$pr_domaca_valuta 		= $row_get_predracune['pr_domaca_valuta'];
						$pr_vrijednost_EUR 		= $row_get_predracune['pr_vrijednost_EUR'];
						$pr_file 				= $row_get_predracune['pr_file'];
						$pr_file_de 			= $row_get_predracune['pr_file_de'];
						$pr_uplaceno 			= $row_get_predracune['pr_uplaceno'];
						$pr_datum_uplate 		= $row_get_predracune['pr_datum_uplate'];
						$pr_opis 				= $row_get_predracune['pr_opis'];
						$pr_stornirano 			= $row_get_predracune['pr_stornirano'];
						$pr_datum_storniranja 	= $row_get_predracune['pr_datum_storniranja'];
						$pr_vrsta_placanja 		= $row_get_predracune['pr_vrsta_placanja'];
						$kandidat_id	 		= $row_get_predracune['id_broj_nd_kandidata'];
						$kandidat_full_name		= $row_get_predracune['ime_nd_kandidata'].' '.$row_get_predracune['prezime_nd_kandidata'];
						
						$drzava_img				= "";
						$status_text 			= "";
						$vrijednost_text 		= "";
						$pr_uplaceno_text		= "";
						$akcija_text			= "";
						
						$kandidat_ime_ispis		= '<a href = "'.getSiteUrlr().'nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$kandidat_id.'">'.$kandidat_full_name.'</a>';
						if($pr_domaca_valuta == 'BAM'){
							$drzava_img = '<img src = "'.getSiteUrlr().'images/bs3d.png" width="25">';
						}
						else if($pr_domaca_valuta == 'RSD'){
							$drzava_img = '<img src = "'.getSiteUrlr().'images/sr3d.png" width="25">';
						}
						else{
							$drzava_img = '<img src = "'.getSiteUrlr().'images/globe3d.png" width="25">';
						}
						
						$rata_text 				= $pr_rata.'. Rata';
						
						if($pr_stornirano == 1){
							$vrijednost_text 	= '<a class="label label-success material-label material-label_danger main-container__column">-'.$pr_vrijednost_EUR.' €</a>';
						}
						else{
							$vrijednost_text 	= '<a class="label label-success material-label material-label_success main-container__column">'.$pr_vrijednost_EUR.' €</a>';
						}
						
						if($pr_status == 0){
							$status_text 		= '<span class="label label-warning material-label material-label_warning main-container__column">Arhiviran</span>';
						}else if($pr_status == 1){
							$status_text 		= '<span class="label label-info material-label material-label_info main-container__column">Poslan</span>';
						}else if($pr_status == 2){
							$status_text 		= '<span class="label label-success material-label material-label_success main-container__column">Uplaćen</span>';
						}else if($pr_status == 3){
							$status_text 		= '<span class="label label-danger material-label material-label_danger main-container__column">In Caso 1</span>';
							$total_inakso_1 = $total_inakso_1 + $pr_vrijednost_EUR;
						}else if($pr_status == 4){
							$status_text 		= '<span class="label label-danger material-label material-label_danger main-container__column">In Caso 2</span>';
							$total_inakso_2 	= $total_inakso_2 + $pr_vrijednost_EUR;
						}else if($pr_status == 5){
							$status_text 		= '<span class="label label-danger material-label material-label_danger main-container__column">In Caso 3</span>';
							$total_inakso_3 	= $total_inakso_3 + $pr_vrijednost_EUR;
						}
						if($pr_uplaceno == 0){
							$pr_uplaceno_text 	= '<span class="label label-warning material-label material-label_warning main-container__column">NE</span>';
							$total_neuplaceno 	= $total_neuplaceno + $pr_vrijednost_EUR;
							if($pr_status != 0){
								$akcija_text 		= '
									<div class="akcija d-inline">
										<input value = "nema_bf" type="text" class="form-control bf_input" name="bf_input" id="bf_input_'.$pr_id.'" placeholder="BF" style="padding:17px;border-radius:0;width:80px;display:none;">
										<input type="text" class="form-control datum_uplate_input" name="datum_uplate_input" id="datum_uplate_input_'.$pr_id.'" placeholder="Datum" style="padding:17px;border-radius:0;float:left;width:90%; width:110px;">
										<i class="fa fa-check oznaci_kao_isplaceno" 
											pr_id="'.$pr_id.'" 
											pr_kandidat_id="'.$kandidat_id.'" 
											pr_domaca_valuta="'.$pr_domaca_valuta.'"  
											style="color:white;background:#5cb85c;font-size:24px;display:flex;float:left;padding:6px;cursor:pointer;" 
											aria-hidden="true"
										>
										</i>
									</div>
									<table id = "download_racune_'.$pr_id.'" style = "width:100%; display:none;">
										<tr style = "background: rgb(218, 243, 255);">
											<td style="margin: 0px; padding-top: 0px; padding-bottom: 0px; padding-right: 6px; text-align: right!important;">
												<a id = "link_racun_'.$pr_id.'" href = "" target="_BLANK">
													'.$drzava_img.'
												</a>
											</td>
											<td  style="margin: 0px; padding-top: 0px; padding-bottom: 0px; padding-left: 6px; text-align: left!important;">
												<a id = "link_racun_de_'.$pr_id.'"href = "" target="_BLANK">
													<img src = "'.getSiteUrlr().'images/Germany.png" width="25">
												</a>
											</td>
										</tr>
									</table>
									<i id="uplaceno_msg_negative_'.$pr_id.'" style="font_size : 15px!important; color : red;     display : none; margin : auto;"><b> GREŠKA !!!</b></i>
									<div id = "loader_min_'.$pr_id.'" class = "lds-hourglass_min" style="display:none;">
								';								
							}
						}
						else{
							$pr_uplaceno_text 	= '<span class="label label-success material-label material-label_success main-container__column">DA</span>';
							$total_uplaceno 	= $total_uplaceno + $pr_vrijednost_EUR;
							$akcija_text 		= '';
						}

						?>
							<tr id="row_<?php echo $pr_id;?>">
								<td><?php echo $brojac; ?></td>
								<td><?php echo $kandidat_ime_ispis; ?></td>
								<?php
								if($flag_vise_drzava){
									?><td data-order="<?php echo $pr_domaca_valuta; ?>"><?php echo $drzava_img; ?></td><?php								
								}
								?>
								<td><?php echo $pr_broj_predracuna; ?></td>
								<td data-order = "<?php echo $pr_datum_kreiranja; ?>"><?php echo date('d.m.Y', strtotime($pr_datum_kreiranja)); ?></td>
								<td><?php echo $rata_text; ?></td>
								<td class = "predracun_download_cell">
									<b id="vrijednost_text_<?php echo $pr_id; ?>" class = "vrijednost_text" pr_id = "<?php echo $pr_id; ?>"><?php echo $vrijednost_text; ?></b>
									<table id = "predracun_download_<?php echo $pr_id?>" style = "width:100%;display:none;">
										<tr>
											<td style="margin: 0px; padding-top: 0px; padding-bottom: 0px; padding-right: 4px;">
												<a href = "<?php echo getSiteUrlr().'files/predracuni_dipl/'.$pr_file; ?>" target="_BLANK" style="float:right;">
													<?php echo $drzava_img; ?>
												</a>
											</td>
											<td style="margin: 0px; padding-top: 0px; padding-bottom: 0px; padding-left: 4px;">
												<a href = "<?php echo getSiteUrlr().'files/predracuni_dipl/'.$pr_file_de; ?>" target="_BLANK">
													<img src = "<?php echo getSiteUrlr(); ?>images/Germany.png" width="25">
												</a>
											</td>
											
											<td id = "predracun_cell_back_<?php echo $pr_id ?>" class = "predracun_cell_back" pr_id = "<?php echo $pr_id ?>" style="margin: 0px; padding-top: 0px; padding-bottom: 0px;">
												<i style = "text-align:center;" class="fa fa-undo idk_green" aria-hidden="true"></i>
											</td>
										</tr>
									</table>
								</td>
								<td><?php echo $status_text; ?></td>
								<td><?php echo $pr_uplaceno_text; ?></td>
								<td style="width:20%;"><?php echo $akcija_text; ?></td>
							</tr>
						<?php
					}
					$total = $total_uplaceno + $total_neuplaceno;
					?>	
						</tbody>
					</table>
					<div class="row" style="text-align:center;">
						<div class="card" data-toggle="tooltip" data-placement="top" title="Vrijednost predračuna koji su uplaćeni">
							<div class="rectangle income"></div>
							<div>
							  <div class="naslov">Uplaćeno</div>
							  <div class="balance">Vrijednost</div>
							  <div class="kurs">€</div>
							  <div class="amount"><?php echo $total_uplaceno;?></div>
							  <div class="siluet-1"></div>
							  <div class="siluet-2"></div>
							</div>
						</div>
						<div class="card"  data-toggle="tooltip" data-placement="top" title="Vrijednost predračuna koji još nisu uplaćeni">
							<div class="rectangle expense"></div>
							<div>
							  <div class="naslov">Neuplaćeno</div>
							  <div class="balance">Vrijednost</div>
							  <div class="kurs">€</div>
							  <div class="amount"><?php echo $total_neuplaceno;?></div>
							  <div class="siluet-1"></div>
							  <div class="siluet-2"></div>
							</div>
						</div>
						<div class="card" data-toggle="tooltip" data-placement="top" title="Ukupna vrijednost predračuna">
							<div class="rectangle info"></div>
							<div>
							  <div class="naslov">Total</div>
							  <div class="balance">Vrijednost</div>
							  <div class="kurs">€</div>
							  <div class="amount"><?php echo ($total_neuplaceno + $total_uplaceno);?></div>
							  <div class="siluet-1"></div>
							  <div class="siluet-2"></div>
							</div>
						</div>
					</div>
					<div class="row" style="text-align:center;">
						<div class="card" data-toggle="tooltip" data-placement="top" title="10 dana nije uplaćeno">
							<div class="rectangle expense"></div>
							<div>
							  <div class="naslov">In Caso 1</div>
							  <div class="balance">Vrijednost</div>
							  <div class="kurs">€</div>
							  <div class="amount"><?php echo number_format((float)$total_inakso_1, 2, '.', ',');?></div>
							  <div class="siluet-1"></div>
							  <div class="siluet-2"></div>
							</div>
						</div>
						<div class="card" data-toggle="tooltip" data-placement="top" title="40 dana nije uplaćeno">
							<div class="rectangle expense"></div>
							<div>
							  <div class="naslov">In Caso 2</div>
							  <div class="balance">Vrijednost</div>
							  <div class="kurs">€</div>
							  <div class="amount"><?php echo number_format((float)$total_inakso_2, 2, '.', ',');?></div>
							  <div class="siluet-1"></div>
							  <div class="siluet-2"></div>
							</div>
						</div>
						<div class="card" data-toggle="tooltip" data-placement="top" title="90 dana nije uplaćeno">
							<div class="rectangle expense"></div>
							<div>
							  <div class="naslov">In Caso 3</div>
							  <div class="balance">Vrijednost</div>
							  <div class="kurs">€</div>
							  <div class="amount"><?php echo number_format((float)$total_inakso_3, 2, '.', ',');?></div>
							  <div class="siluet-1"></div>
							  <div class="siluet-2"></div>
							</div>
						</div>
					</div>
					<script>
						$(".datum_uplate_input").flatpickr({
							dateFormat: "d.m.Y",
							disableMobile: "true"
						});
						$('.oznaci_kao_isplaceno').on('click',function(){
							var this_element = $(this);
							this_element.css('pointer-events','none');
							var predracun_id 			= $(this).attr('pr_id');
							var pr_kandidat_id 			= $(this).attr('pr_kandidat_id');
							var pr_domaca_valuta 		= $(this).attr('pr_domaca_valuta');
							var element_datum_uplate 	= $('#datum_uplate_input_'+predracun_id);
							var element_bf 				= $('#bf_input_'+predracun_id);
							var bf 						= element_bf.val();
							var datum_uplate 			= element_datum_uplate.val();
							var flag_greske_unosa 		= false;
							
							if(!datum_uplate){
								flag_greske_unosa 		= true;
								element_datum_uplate.effect('highlight');
								element_datum_uplate.effect('bounce');
								element_datum_uplate.effect('highlight');
								this_element.css('pointer-events','auto');
							}
							if(!bf){
								flag_greske_unosa 		= true;
								element_bf.effect('highlight');
								element_bf.effect('bounce');
								element_bf.effect('highlight');
								this_element.css('pointer-events','auto');
							}
							if(!flag_greske_unosa){
								this_element.css('pointer-events','none');
								element_datum_uplate.effect('fold',	400, function(){
									$('#loader_min_'+predracun_id).fadeIn(500);
									this_element.effect('fold', 300,function(){
										$.ajax({
											url: 'ajax_data.php?page=uplati_predracun_ch', 
											type: 'POST',
											data: {'pr_id':predracun_id, 'pr_kandidat_id': pr_kandidat_id, 'pr_datum_uplate': datum_uplate,'bf': bf},
											dataType: 'html',
											success: function(data) {
												$('#loader_min_'+predracun_id).fadeOut(300,function(){
													var links = data.split('|');
													$('#link_racun_'+predracun_id).attr('href', links[0]);
													$('#link_racun_de_'+predracun_id).attr('href', links[1]);
													$('#download_racune_'+predracun_id).effect('slide');	
													$('#row_'+predracun_id).css('background','#DAF3FF');
												});
												
											},
											error: function (xhr, ajaxOptions, thrownError) {
												$('#loader_min_'+predracun_id).fadeOut(300,function(){
													$('#uplaceno_msg_negative_'+predracun_id).effect('slide');
													$('#row_'+predracun_id).css('background','#ffa8a6');
												});
											}
										});
										
									});
								});
							}
						});
						$('.vrijednost_text').on('click',function(){
							var pr_id = $(this).attr('pr_id');
							$('#vrijednost_text_'+pr_id).fadeOut(500,function(){
								$('#predracun_download_'+pr_id).fadeIn(500);
							});
						});
						$('.predracun_cell_back').on('click',function(){
							var pr_id = $(this).attr('pr_id');
							$('#predracun_download_'+pr_id).fadeOut(500,function(){
								$('#vrijednost_text_'+pr_id).fadeIn(500);
							});
						});
					</script>
					<?php

				break;
				
				case "uplati_predracun_ch":
					$pr_id 				= $_POST['pr_id'];
					$pr_kandidat_id 	= $_POST['pr_kandidat_id'];
					$racun_bf			= $_POST['bf'];
					$pr_datum_uplate 	= date("Y-m-d", strtotime($_POST['pr_datum_uplate']));

					$query_get_info_predracun = $db->prepare('
						SELECT pr_naplata_preko, pr_domaca_valuta, pr_rata 
						FROM idk_predracuni
						WHERE pr_id = :pr_id
					');
					$query_get_info_predracun -> execute(array(':pr_id' => $pr_id));
					$row_get_info_predracun = $query_get_info_predracun -> fetch();
					
					$pr_naplata_preko 	= $row_get_info_predracun['pr_naplata_preko'];
					$pr_domaca_valuta 	= $row_get_info_predracun['pr_domaca_valuta'];
					$pr_rata 			= $row_get_info_predracun['pr_rata'];
					
					$query_update_predracun = $db->prepare('
						UPDATE idk_predracuni
						SET pr_uplaceno = :pr_uplaceno, pr_datum_uplate = :pr_datum_uplate, pr_status = :pr_status
						WHERE pr_id = :pr_id
					');
					
					$query_update_predracun -> execute(array(
						':pr_id' 			=> $pr_id,
						':pr_uplaceno' 		=> 1,
						':pr_datum_uplate' 	=> $pr_datum_uplate,
						':pr_status' 		=> 2
					));
					
					
					sendMailUplataPredracuna($pr_id);
					//Prebaci status na "PRIKUPLJANJE DOKUMENTACIJE" ako je u pitanju prva rata
					if($pr_rata == 1){
						sendMailViberCheckListDIPL($pr_id);	
						promjenaStatusaDIPLKandidat($pr_kandidat_id, 2, 1, 1);
						
						updateProjekcijeKandidat(1, $pr_kandidat_id);
					}
					//Insert logova
					$log_desc = "Označio da je naplaćena rata: " . $pr_id . " ";
					$log_date = date('Y-m-d H:i:s'); 

					$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date)
						VALUES
							(:log_employeeid, :log_desc, :log_date)
					");

					$log_query->execute(array(
						':log_employeeid' 	=> $logged_employee_id,
						':log_desc' 		=> $log_desc,
						':log_date' 		=> $log_date
					));
					
					//UPLATA OBRACUNA fja sa id-em predracuna
					uplatiObracune($pr_id);
					
					$uplata_rate_sati = date("H:i:s");
					$uplata_rate_vrijeme = $pr_datum_uplate.' '.$uplata_rate_sati;
					$query_insert_uplacenu_ratu = $db->prepare("
						INSERT INTO idk_nd_rate
							(id_nd_kan, br_r, vr_u_r)
						VALUES
							(:id_nd_kan, :br_r, :vr_u_r)"
					);

					$query_insert_uplacenu_ratu->execute(array(
						':id_nd_kan' => $pr_kandidat_id,
						':br_r' => $pr_rata,
						':vr_u_r' => $uplata_rate_vrijeme
					));
					
					$drzava = "";
					switch($pr_domaca_valuta){
						case 'BAM':
							$drzava = "BiH";
						break;
							
						case 'RSD':
							$drzava = "Srbija";
						break;
						
						default:
							$drzava = "Njemacka";
					}
					include("html_pdf_generator.php");
					generisiRacun($pr_id, $drzava);
					if($drzava != "Njemacka"){
						generisiRacun($pr_id, "Njemacka");
					}
					
					$query_get_last_racun_file = $db -> prepare('
						SELECT racun_file ,racun_file_de
						FROM idk_racuni
						WHERE racun_kandidat_id = :pr_kandidat_id
						AND racun_status = 1
						ORDER BY racun_id DESC
						LIMIT 1
					');
					
					$query_get_last_racun_file -> execute(array(':pr_kandidat_id' => $pr_kandidat_id));
					$row_get_last_racun_file = $query_get_last_racun_file -> fetch();
					$racun_file_za_send = $row_get_last_racun_file['racun_file_de'];
					sendMailRacun($racun_file_za_send);
					
					$link_racun_file = getSiteUrlr().'files/racuni_dipl/'.$row_get_last_racun_file['racun_file'].'|'.getSiteUrlr().'files/racuni_dipl/'.$row_get_last_racun_file['racun_file_de'];
					
					echo $link_racun_file;
				break;
				
				/**************************************************
				*		AJAX POZIVI ZA PRAĆENJE STATUSA AGENTA
				***************************************************/
				case "agentStatusTimeCheck":
				
					/**
					*	ako je naveden status - saznaj vrijeme provedeno na tom statusu
					*		ako nije - daj vremena za sve statuse
					**/
					if( isset( $_POST ) )
					{
						$requestedStatus = $_POST['status'];
						if($requestedStatus)
							echo json_encode (  getStatusTimeForLoggedAgent( $requestedStatus ) );
						else 
							echo json_encode (  getAllStatusTimesForLoggedAgent( ) );
						
					}
				break;
				
				case "getAgentCurrentStatus":
					// saznaj trenutno vrijeme agenta
					echo  json_encode( getCurrentStatus () );
					
				break;
				case "updateStatusStop":
					/**
					*	ZAUSTAVI BROJANJE ILI ZA SVE STATUSE ILI ZA SAMO ODREĐENI STATUS
					*		I POSTAVI STATUS IDLE KAO AKTIVAN
					**/
					
					if( isset( $_POST ) )
					{
						$requestedStatus = $_POST['status'];
						if( empty( $requestedStatus ) ) 
							$requestedStatus = null;
						echo json_encode (  stopAgentStatusTracking( $requestedStatus ) );
						setStatusToIdle();
					}
					
				break;
				
				case "startTimerForAgentStatus":
					/**
					*	POSTAVI SVE STATUSE KAO NEAKTIVNE
					*	AKTIVIRAJ BROJANJE ZA ODABRANI STATUS
					**/
					
					if( isset( $_POST ) )
					{
						$requestedStatus = $_POST['status'];
						if( empty( $requestedStatus ) ) 
							$requestedStatus = null;
						
						stopAgentStatusTracking( null ); // sve statuse postavi na nulu
						echo json_encode ( startTimeTracking( $requestedStatus ) );
						
					}
				break;
				/**************************************************
				*		AJAX POZIVI ZA PRAĆENJE STATUSA AGENTA
				*					**KRAJ**
				***************************************************/
				
				case "pregled_ugovora":
					$id_kand = $_POST["id_kand"];
					// $vrsta_ugovora = $_POST["vrsta_ugovora"];
					
					$sum_ispis = "";
					$ispis = "";
					
					function cmp($a, $b)
					{
						$a = date('Y-m-d H:i:s', strtotime($a));
						$b = date('Y-m-d H:i:s', strtotime($b));

						if ($a == $b) {
							return 0;
						}
						return ($a < $b) ? -1 : 1;
					}
					
					if(isset($id_kand)){
						// if(isset($vrsta_ugovora)){
							$query = $db->prepare("
								SELECT ug_vrsta, ug_file, ug_datum_slanja, ug_datum_otvaranja_linka, ug_datum_prihvatanja, ug_datum_odbijanja, ug_datum_arhiviranja
								FROM idk_nd_ugovori
								WHERE ug_kandidat_id = :ug_kandidat_id
								ORDER BY ug_id ASC
							");
							$query->execute(array(
								':ug_kandidat_id' => $id_kand
							));
							
							if($query->rowCount() != 0){
								while($row = $query->fetch()){
									$niz = array();
									
									if($row["ug_vrsta"] != NULL){
										$ug_vrstaR = getVrstaUgovoraDiplR($row["ug_vrsta"]);
										$ug_vrsta = '
											<tr>
												<td class="text-center" style = "vertical-align: middle;" colspan="3">
													<span class="label label-success material-label material-label_success main-container__column text-left">
														'.$ug_vrstaR.'
													</span>
												</td>
											</tr>
										';
									}else{
										$ug_vrsta = '
											<tr>
												<td class="text-center" style = "vertical-align: middle;" colspan="3">
													<span class="label label-danger material-label material-label_danger main-container__column text-left">
														Nije unešena informacija!
													</span>
												</td>
											</tr>
										';
									}
									if($row["ug_file"] != NULL){
									$ug_file = '<span class="label label-default material-label material-label_default main-container__column text-left">'.chop($row["ug_file"],".pdf").'</span>';
									}else{
										$ug_file = '<span class="label label-default material-label material-label_default main-container__column text-left">Nije generisan</span>';
									}
									$ug_datum_slanja = $row["ug_datum_slanja"];
									if($ug_datum_slanja != NULL){
										array_push($niz, $ug_datum_slanja);
									}
									$ug_datum_otvaranja_linka = $row["ug_datum_otvaranja_linka"];
									if($ug_datum_otvaranja_linka != NULL){
										array_push($niz, $ug_datum_otvaranja_linka);
									}
									$ug_datum_prihvatanja = $row["ug_datum_prihvatanja"];
									if($ug_datum_prihvatanja != NULL){
										array_push($niz, $ug_datum_prihvatanja);
									}
									$ug_datum_odbijanja = $row["ug_datum_odbijanja"];
									if($ug_datum_odbijanja != NULL){
										array_push($niz, $ug_datum_odbijanja);
									}
									$ug_datum_arhiviranja = $row["ug_datum_arhiviranja"];
									if($ug_datum_arhiviranja != NULL){
										array_push($niz, $ug_datum_arhiviranja);
									}
									$sum_ispis = $sum_ispis.''.$ug_vrsta;
									usort($niz, "cmp");
									$duzina_niza = count($niz);
									foreach ($niz as $value) {
										if($value == $ug_datum_slanja){
											$ispis = '
												<tr>
													<td class="text-center" style = "vertical-align: middle;" rowspan="'.$duzina_niza.'">'.$ug_file.'</td>
													<td class="text-center"><span class="label label-primary material-label material-label_primary main-container__column text-left">Poslan</span></td>
													<td class="text-center">'.date("d.m.Y H:i:s", strtotime($value)).'</td>
												</tr>
											';
										}else if($value == $ug_datum_otvaranja_linka){
											$ispis = '
												<tr>
													<td class="text-center"><span class="label label-info material-label material-label_info main-container__column text-left">Otvoren Link</span></td>
													<td class="text-center">'.date("d.m.Y H:i:s", strtotime($value)).'</td>
												</tr>
											';
										}else if($value == $ug_datum_prihvatanja){
											$ispis = '
												<tr>
													<td class="text-center"><span class="label label-success material-label material-label_success main-container__column text-left">Prihvaćen</span></td>
													<td class="text-center">'.date("d.m.Y H:i:s", strtotime($value)).'</td>
												</tr>
											';
										}else if($value == $ug_datum_odbijanja){
											$ispis = '
												<tr>
													<td class="text-center"><span class="label label-danger material-label material-label_danger main-container__column text-left">Odbijen</span></td>
													<td class="text-center">'.date("d.m.Y H:i:s", strtotime($value)).'</td>
												</tr>
											';
										}else{
											$ispis = '
												<tr>
													<td class="text-center"><span class="label label-danger material-label material-label_danger main-container__column text-left">Arhiviran</span></td>
													<td class="text-center">'.date("d.m.Y H:i:s", strtotime($value)).'</td>
												</tr>
											';
										}
										$sum_ispis = $sum_ispis.''.$ispis;
									}
								}
								
								echo '
									<div class = "row" style = "padding-left: 20px;">
										<div class = "col-xs-12">
											<div class="row">
												<div class="col-xs-12">
													<h5 style = "font-weight: bold;"><i class="fa fa-pencil-square-o" style = "margin-right: 10px;" aria-hidden="true"></i>Statusi ugovora</h5>
												</div>
											</div>
											<div class="table-responsive">
												<table class="table table-striped">
													<thead>
														<tr>
															<th class="text-center">Ugovor</th>
															<th class="text-center">Status</th>
															<th class="text-center">Datum</th>
														</tr>
													</thead>
													<tbody>
														'.$sum_ispis.'
													</tbody>
												</table>
											</div>
										</div>
									</div>
								';
								
							}else{
								echo '';
							}
						// }else{
							// echo '';
						// }
					}else{
						echo '
							<div class="row">
								<div class = "col-xs-12" style = "min-height: 1px;">
									<div class="alert alert-danger" role="alert">
										Problem sa kandidatom!
									</div>
								</div>
							</div>
						';
					}
				break;
				
				case "opcije_uplate":
					$id_kand = $_POST["id_kand"];
					$vrsta_ugovora = $_POST["vrsta_ugovora"];

					// $rata1 = array(9,21,10,71,51,61,99);
					// $rata2 = array(1,22,2,72,52,62);
					// $rata3 = array(5,23,6,73,53,63);
					// $rata4 = array(7,24,8,74,54,64);
					// $rata5 = array(3,25,4,75,55,65);
					// $rata6 = array(26);
					// $rata12 = array(27);
					// $rataMikrofin = array(11,12);
					
					$rata1 = array(9,21,10,41,51,61,71,81);
					$rata2 = array(1,22,2,42,52,62,72,82);
					$rata3 = array(5,23,6,43,53,63,73,83);
					$rata4 = array(7,24,8,44,54,64,74,84);
					$rata5 = array(3,25,4,45,55,65,75,85);
					$rata6 = array(26);
					$rata12 = array(27);
					$rataMikrofin = array(11,12);
					if(isset($id_kand) AND isset($vrsta_ugovora)){
						if(in_array($vrsta_ugovora, $rata1)){
							$broj_rata = 1;
						}else if(in_array($vrsta_ugovora, $rata2)){
							$broj_rata = 2;
						}else if(in_array($vrsta_ugovora, $rata3)){
							$broj_rata = 3;
						}else if(in_array($vrsta_ugovora, $rata4)){
							$broj_rata = 4;
						}else if(in_array($vrsta_ugovora, $rata5)){
							$broj_rata = 5;
						}else if(in_array($vrsta_ugovora, $rata6)){
							$broj_rata = 6;
						}else if(in_array($vrsta_ugovora, $rata12)){
							$broj_rata = 12;
						}else if(in_array($vrsta_ugovora, $rataMikrofin)){
							$broj_rata = 1;
						}else{
							$broj_rata = 0;
						}
						
						$query = $db->prepare("
							SELECT MAX(br_r) AS max_rata
							FROM idk_nd_rate
							WHERE id_nd_kan = :id_nd_kan
						");
						$query->execute(array(
							':id_nd_kan' => $id_kand
						));
						$row = $query->fetch();
						
						$max_rata = intval($row["max_rata"]);
						$max_rata_sum = $max_rata + 1;
						if($max_rata < $broj_rata){
							echo '<option value = "'.$max_rata_sum.'">Rata '.$max_rata_sum.'</option>';
						}else{
							echo '';
						}
					}else{
						echo '';
					}
				break;
				case "ispis_rata":
				
					$id_kand = $_POST["id_kand"];
					$vrsta_ugovora = $_POST["vrsta_ugovora"];
					//echo "Danas";
					//echo "".$id_kand." ".$vrsta_ugovora;
					$red = "";
					$suma_redova = "";
					$boja = "";
					$vrijeme_prve_rate = "";
					$vrijeme_uplate = "";
					$poruka1_rata = "";
					$poruka2_rata = "";
					$broj_rata = 0;
					$brojac = 0;
					$predvidjeno_brojac = 0;
					$provjera_prve_rate = 0;
					$provjera_rate = 0;
					
					// $rata1 = array(9,21,10,71,51,61,99);
					// $rata2 = array(1,22,2,72,52,62);
					// $rata3 = array(5,23,6,73,53,63);
					// $rata4 = array(7,24,8,74,54,64);
					// $rata5 = array(3,25,4,75,55,65);
					// $rata6 = array(26);
					// $rata12 = array(27);
					// $rataMikrofin = array(11,12);
					
					$rata1 = array(9,21,10,41,51,61,71,81);
					$rata2 = array(1,22,2,42,52,62,72,82);
					$rata3 = array(5,23,6,43,53,63,73,83);
					$rata4 = array(7,24,8,44,54,64,74,84);
					$rata5 = array(3,25,4,45,55,65,75,85);
					$rata6 = array(26);
					$rata12 = array(27);
					$rataMikrofin = array(11,12);
					
					if(isset($id_kand)){
						
						if(isset($vrsta_ugovora)){
							if(in_array($vrsta_ugovora, $rata1)){
								$broj_rata = 1;
							}else if(in_array($vrsta_ugovora, $rata2)){
								$broj_rata = 2;
							}else if(in_array($vrsta_ugovora, $rata3)){
								$broj_rata = 3;
							}else if(in_array($vrsta_ugovora, $rata4)){
								$broj_rata = 4;
							}else if(in_array($vrsta_ugovora, $rata5)){
								$broj_rata = 5;
							}else if(in_array($vrsta_ugovora, $rata6)){
								$broj_rata = 6;
							}else if(in_array($vrsta_ugovora, $rata12)){
								$broj_rata = 12;
							}else if(in_array($vrsta_ugovora, $rataMikrofin)){
								$broj_rata = 1;
							}else{
								$broj_rata = 0;
							}
							
							if($broj_rata != 0){
								
								$provjera_prve_rate = getUplacenaRataND($id_kand, 1);
								
								if($provjera_prve_rate != 0){
									
									$query = $db->prepare("
										SELECT vr_u_r
										FROM idk_nd_rate
										WHERE id_nd_kan = :id_nd_kan AND br_r = :br_r
									");
									$query->execute(array(
										':id_nd_kan' => $id_kand,
										':br_r' => 1
									));
									
									$row = $query->fetch();
									
									$vrijeme_prve_rate = date('d.m.Y', strtotime($row["vr_u_r"]));
									
									for ($brojac = 1; $brojac <= $broj_rata; $brojac++) {
										
										$provjera_rate = getUplacenaRataND($id_kand, $brojac);
										$predvidjeno_brojac = 0;
										if($provjera_rate != 0){
											$boja = "success";
											$query1 = $db->prepare("
												SELECT vr_u_r
												FROM idk_nd_rate
												WHERE id_nd_kan = :id_nd_kan AND br_r = :br_r
											");
											$query1->execute(array(
												':id_nd_kan' => $id_kand,
												':br_r' => $brojac
											));
											$row1 = $query1->fetch();
											$vrijeme_uplate = date('d.m.Y',strtotime($row1["vr_u_r"]));
											$poruka1_rata = '<span style="padding: 0px 16px;" class="material-label material-label_success main-container__column" title = "Uplaćena rata!"> DA </span>';
											$poruka2_rata = '<span style="padding: 0px 16px;" class="material-label material-label_success main-container__column" title = "Datum uplate."> '.$vrijeme_uplate.' </span>';
										}else{
											$boja = "danger";
											$predvidjeno_brojac = $brojac - 1;
											$vrijeme_uplate = date('d.m.Y',strtotime('+'.$predvidjeno_brojac.' month',strtotime($vrijeme_prve_rate)));
											$poruka1_rata = '<span style="padding: 0px 16px;" class="material-label material-label_danger main-container__column" title = "Nije uplaćena rata!"> NE </span>';
											$poruka2_rata = '<span style="padding: 0px 16px;" class="material-label material-label_danger main-container__column" title = "Predviđeno vrijeme uplate druge rate.">'.$vrijeme_uplate.'</span>';
										}
										
										$red = '
											<li class="list-group-item list-group-item-'.$boja.'">
												<div class = "row">
													<div class = "col-xs-4 text-center">
														Rata '.$brojac.'
													</div>
													<div class = "col-xs-4 text-center">
														'.$poruka1_rata.'
													</div>
													<div class = "col-xs-4 text-center">
														'.$poruka2_rata.'
													</div>
												</div>
											</li>
										';
										$suma_redova = $suma_redova.' '.$red;
										$provjera_rate = 0;
									}
								}else{
									for ($brojac = 1; $brojac <= $broj_rata; $brojac++) {
										$boja = "danger";
										$red = '
											<li class="list-group-item list-group-item-'.$boja.'">
												<div class = "row">
													<div class = "col-xs-4 text-center">
														Rata '.$brojac.'
													</div>
													<div class = "col-xs-4 text-center">
														<span style="padding: 0px 16px;" class="material-label material-label_danger main-container__column" title = "Nije plaćena rata!">NE</span>
													</div>
													<div class = "col-xs-4 text-center">
														<span style="padding: 0px 16px;" class="material-label material-label_danger main-container__column" title = "Nije plaćena rata!">NE</span>
													</div>
												</div>
											</li>
										';
										$suma_redova = $suma_redova.' '.$red;
									}
								}
								
								echo '
									<div class = "row">
										<div class = "col-xs-12">
											<ul class="list-group">
											'.$suma_redova.'
											</ul>
										</div>
									</div>
								';
								
								
							}else{
								echo '
									<div class="row">
										<div class = "col-xs-12" style = "min-height: 1px;">
											<div class="alert alert-danger" role="alert">
												Problem sa brojem rata!
											</div>
										</div>
									</div>
								';
							}
						}else{
							echo '
								<div class="row">
									<div class = "col-xs-12" style = "min-height: 1px;">
										<div class="alert alert-danger" role="alert">
											Nije određena vrsta ugovora!
										</div>
									</div>
								</div>
							';
						}
						
					}else{
						echo '
							<div class="row">
								<div class = "col-xs-12" style = "min-height: 1px;">
									<div class="alert alert-danger" role="alert">
										Problem sa kandidatom!
									</div>
								</div>
							</div>
						';
					}
				break;
				case "get_vazni_dokumenti":
					$kanId = intval($_POST["kan_id_lista"]);
					if($kanId != 0){
						$countStari = 0; //broj predracuna po starom nacinu 
						$countNovi = 0;
						$queryProvjeraNacinPlacanja = $db->prepare("
							SELECT 
								pr_naplata_preko AS naplata
							FROM 
								idk_predracuni 
							WHERE 
								pr_kandidat_id = :pr_kandidat_id AND pr_vrsta_predracuna = :pr_vrsta_predracuna
						");
						$queryProvjeraNacinPlacanja->execute(array(
							':pr_kandidat_id' => $kanId, 
							':pr_vrsta_predracuna' => 1
						));
						$brojProvjeraNacinPlacanja = $queryProvjeraNacinPlacanja->rowCount();
						if($brojProvjeraNacinPlacanja != 0){
							while($rowProvjeraNacinPlacanja = $queryProvjeraNacinPlacanja->fetch()){
								$naplata = intval($rowProvjeraNacinPlacanja["naplata"]);
								if($naplata == 0){
									$countStari++;
								}else{
									$countNovi++;
								}
							}
							
							if($countStari != 0){
								//STARI NACIN UPLATA PREKO 0 START
									//SU - skracenica stari ugovoru
									//Ugovori START ----------------------------------------------------------------------------------
										//SU - skracenica stari ugovoru
										$queryStariUgovori = $db->prepare("
											SELECT 
												id_dokument_nd,
												naziv_dokument_nd,
												naziv_dokument_ostali_nd,
												vrijeme_dodavanja_dokument_nd,
												dodao_zaposlenik_dokument_nd,
												tip_dokumenta_status
											FROM 
												idk_nd_kandidata_dokumenti
											WHERE
												id_kandidata_dokument_nd = :id_kandidata_dokument_nd 
												AND 
												status_dokument_nd is null 
												AND 
												naziv_dokument_ostali_nd LIKE 'ugovor' 
												AND 
												tip_dokumenta = 1
										");
										$queryStariUgovori->execute(array(
											':id_kandidata_dokument_nd' => $kanId,
										));
										$brojStariUgovori = $queryStariUgovori->rowCount();
										if($brojStariUgovori != 0){
											echo '
												<div class="panel panel-default">
													<div class="panel-heading text-center"><h3 class="panel-title" style = "font-weight: bold;">Ugovor/i</h3></div>
													<div class="panel-body">
											';
										}
										while($rowStariUgovori = $queryStariUgovori->fetch()){
											$cardIzgledSU = "";
											$idDokumentSU = $rowStariUgovori["id_dokument_nd"];
											$fileDokumentSU = '<a href="'.getSiteUrlr().'files/ugovori_uplatnice_dipl/'.$rowStariUgovori["naziv_dokument_nd"].'" class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK"><i class="fa fa-file-pdf-o fa-3x" aria-hidden="true"></i></a>';
											$nazivDokumentSU = $rowStariUgovori["naziv_dokument_ostali_nd"];
											$vrijemeDokumentSU = date('d.m.Y H:i', strtotime($rowStariUgovori["vrijeme_dodavanja_dokument_nd"]));
											$zaposlenikDokumentSU = getZaposlenikimeR($rowStariUgovori["dodao_zaposlenik_dokument_nd"]);
											$statusDokumentSU = intval($rowStariUgovori["tip_dokumenta_status"]);
											if($statusDokumentSU == 1){
												$statusDokumentSU = "Aktivan";
												$cardIzgledSU = "pan";
											}else{
												$statusDokumentSU = "Arhiviran";
												$cardIzgledSU = "pan1";
											}
											echo '
												<div class="row" style = "text-align: -webkit-center;">
													<div class = "content_box" style = "min-height: 1px; padding: 12px;">
														<div class="panel '.$cardIzgledSU.' panel-default pandef">
															<div class="panel-heading panhead">
																<h3 class="panel-title pantit">'.$nazivDokumentSU.' - '.$statusDokumentSU.' </h3>
															</div>
															<div class="panel-body panbod">
																<div class = "row">
																	<div class = "col-xs-8">
																		<div class = "row">
																			<div class = "col-xs-3 text-center">
																				<i class="fa fa-user" aria-hidden="true"></i>
																			</div>
																			<div class = "col-xs-9 text-left">
																				'.$zaposlenikDokumentSU.'
																			</div>
																		</div>
																		<div class = "row">
																			<div class = "col-xs-3 text-center">
																				<i class="fa fa-calendar-plus-o" aria-hidden="true"></i>
																			</div>
																			<div class = "col-xs-9 text-left">
																				'.$vrijemeDokumentSU.'
																			</div>
																		</div>
																	</div>
																	<div class = "col-xs-4">
																		'.$fileDokumentSU.'
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											';
										}
										if($brojStariUgovori != 0){
											echo '
													</div>
												</div>
											';
										}
									//Ugovori END ----------------------------------------------------------------------------------
									
									//Predracuni START ----------------------------------------------------------------------------------
										//SP skracenica stari predracuni
										$queryStariPredracuni = $db->prepare("
											SELECT 
												pr_naplata_preko, 
												pr_broj_predracuna, 
												pr_datum_kreiranja, 
												pr_zaposlenik, 
												pr_rata, 
												pr_file,
												pr_status,
												pr_domaca_valuta
											FROM idk_predracuni
											WHERE 
												pr_kandidat_id = :pr_kandidat_id 
												AND 
												pr_vrsta_predracuna = :pr_vrsta_predracuna 
												AND 
												pr_stornirano = :pr_stornirano
												AND 
												pr_naplata_preko = 0
										");
										$queryStariPredracuni->execute(array(
											':pr_kandidat_id' => $kanId,
											':pr_vrsta_predracuna' => 1,
											':pr_stornirano' => 0
										));
										$brojStariPredracuni = $queryStariPredracuni->rowCount();
										if($brojStariPredracuni != 0){
											echo '
												<div class="panel panel-default">
													<div class="panel-heading text-center"><h3 class="panel-title" style = "font-weight: bold;">Predračun/i</h3></div>
													<div class="panel-body">
											';
										}
										while($rowStariPredracuni = $queryStariPredracuni->fetch()){
											$cardIzgledSP = "";
											$brojSP = $rowStariPredracuni["pr_broj_predracuna"];
											$datumSP = date("d.m.Y H:i:s", strtotime($rowStariPredracuni["pr_datum_kreiranja"]));
											$zaposlenik_id = $rowStariPredracuni["pr_zaposlenik"];
											if($zaposlenik_id != 67){
												$zaposlenikSP = getZaposlenikimeR($rowStariPredracuni["pr_zaposlenik"]);
											}else{
												$zaposlenikSP = "Automatski";
											}
											$rataSP = intval($rowStariPredracuni["pr_rata"]);
											$fileSP = $rowStariPredracuni["pr_file"];
											$statusSP = intval($rowStariPredracuni["pr_status"]);
											$valutaSP = $rowStariPredracuni["pr_domaca_valuta"];
											if($statusSP != 0){
												$statusSP = "Aktivan";
												$cardIzgledSP = "pan";
											}else{
												$statusSP = "Arhiviran";
												$cardIzgledSP = "pan1";
											}
											if($valutaSP == "BAM"){
												$valutaSP = '<img src="images/bs3d.png" width="25">';
											}else if($valutaSP == "RSD"){
												$valutaSP = '<img src="images/sr3d.png" width="25">';
											}else{
												$valutaSP = '<img src="images/de3d.png" width="25">';
											}
											echo '
												<div class="row" style = "text-align: -webkit-center;">
													<div class = "content_box" style = "min-height: 1px; padding: 12px;">
														<div class="panel '.$cardIzgledSP.' panel-default pandef">
															<div class="panel-heading panhead">
																<h3 class="panel-title pantit">'.$statusSP.' predračun '.$brojSP.' RATA '.$rataSP.'</h3>
															</div>
															<div class="panel-body panbod">
																<div class = "row">
																	<div class = "col-xs-8">
																		<div class = "row">
																			<div class = "col-xs-3 text-center">
																				<i class="fa fa-user" aria-hidden="true"></i>
																			</div>
																			<div class = "col-xs-9 text-left">
																				'.$zaposlenikSP.'
																			</div>
																		</div>
																		<div class = "row">
																			<div class = "col-xs-3 text-center">
																				<i class="fa fa-calendar-plus-o" aria-hidden="true"></i>
																			</div>
																			<div class = "col-xs-9 text-left">
																				'.$datumSP.'
																			</div>
																		</div>
																	</div>
																	<div class = "col-xs-4">
																		<div class = "row">
																			<div class = "col-xs-12 text-center">
																				'.$valutaSP.'
																			</div>
																		</div>
																		<div class = "row" style = "margin-top: 10px;">
																			<div class = "col-xs-12 text-center">
																				<a href="'.getSiteUrlr().'files/predracuni_dipl/'.$fileSP.'" class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK"><i class="fa fa-file-pdf-o fa-3x" title = "Ugovor za kandidata!" aria-hidden="true"></i></a>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											';
										}
										if($brojStariPredracuni != 0){
											echo '
													</div>
												</div>
											';
										}
									//Predracuni END ----------------------------------------------------------------------------------
									
								//STARI NACIN UPLATA PREKO 0 END
							}
							if($countNovi != 0){
								//NOVI NACIN UPLATA PREKO 1 START
									//Ugovori START
										//NU Skraćenica novi ugovori
										$queryNoviUgovor = $db->prepare("
											SELECT 
												ug_id,
												ug_zaposlenik_id,
												ug_jezik,
												ug_file,
												ug_file_de,
												ug_datum_slanja,
												ug_status
											FROM 
												idk_nd_ugovori
											WHERE 
												ug_kandidat_id = :ug_kandidat_id
										");
										$queryNoviUgovor->execute(array(
											':ug_kandidat_id' => $kanId
										));
										$brojNoviUgovori = $queryNoviUgovor->rowCount();
										if($brojNoviUgovori != 0){
											echo '
												<div class="panel panel-default">
													<div class="panel-heading text-center"><h3 class="panel-title" style = "font-weight: bold;">Ugovor/i</h3></div>
													<div class="panel-body">
											';
										}
										while($rowNoviUgovor = $queryNoviUgovor->fetch()){
											$cardIzgledNU = "";
											$elemDow = "";
											$disDow = "";
											$idDokumentNU = intval($rowNoviUgovor["ug_id"]);
											$zaposlenikDokumentNU = getZaposlenikimeR($rowNoviUgovor["ug_zaposlenik_id"]);
											$jezikDokumentNU = $rowNoviUgovor["ug_jezik"];
											$fileDokumentNU = $rowNoviUgovor["ug_file"];
											$fileDEDokumentNU = $rowNoviUgovor["ug_file_de"];
											$datumDokumentNU = date('d.m.Y H:i', strtotime($rowNoviUgovor["ug_datum_slanja"]));
											$statusDokumentNU = intval($rowNoviUgovor["ug_status"]);
											if($statusDokumentNU == 0){
												if($fileDokumentNU != NULL){
													$elemDow = 'a';
													$disDow = '';
												}else{
													$elemDow = 'button';
													$disDow = 'disabled="disabled"';
												}
												$statusDokumentNU = 'Arhiviran';
												$cardIzgledNU = "pan1";
											}else if($statusDokumentNU == 3){
												$elemDow = 'button';
												$disDow = 'disabled="disabled"';
												$statusDokumentNU = 'Odbijen';
												$cardIzgledNU = "pan1";
											}else if($statusDokumentNU == 1){
												$elemDow = 'button';
												$disDow = 'disabled="disabled"';
												$statusDokumentNU = 'Poslan';
												$cardIzgledNU = "pan2";
											}else if($statusDokumentNU == 4){
												$elemDow = 'button';
												$disDow = 'disabled="disabled"';
												$statusDokumentNU = 'Otvoren link';
												$cardIzgledNU = "pan2";
											}else{
												$elemDow = 'a';
												$disDow = '';
												$statusDokumentNU = 'Prihvaćen';
												$cardIzgledNU = "pan";
											}
											
											if($jezikDokumentNU == "bs"){
												$jezikDokumentNU = '<img src="images/bs3d.png" width="25">';
											}else if($jezikDokumentNU == "sr"){
												$jezikDokumentNU = '<img src="images/sr3d.png" width="25">';
											}else{
												$jezikDokumentNU = '<img src="images/de3d.png" width="25">';
											}
											
											$dowFileDokumentNU = '<'.$elemDow.' href="'.getSiteUrlr().'tcpdf-main/ugovori/'.$fileDokumentNU.'" '.$disDow.' class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK"><i class="fa fa-file-pdf-o fa-3x" aria-hidden="true"></i></'.$elemDow.'>';
											$dowFileDEDokumentNU = '<'.$elemDow.' href="'.getSiteUrlr().'tcpdf-main/ugovori/'.$fileDEDokumentNU.'" '.$disDow.' class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK"><i class="fa fa-file-pdf-o fa-3x" aria-hidden="true"></i></'.$elemDow.'>';
											
											$ugovor_kartica = '';
											if($naplata == 1){
												$ugovor_kartica = '<div class = "col-xs-4">
																		<div class = "row">
																			<div class = "col-xs-6 text-center">
																				'.$jezikDokumentNU.'
																			</div>
																			<div class = "col-xs-6 text-center">
																				<img src="images/de3d.png" width="25">
																			</div>
																		</div>
																		<div class = "row" style = "margin-top: 10px;">
																			<div class = "col-xs-6 text-center">
																				'.$dowFileDokumentNU.'
																			</div>
																			<div class = "col-xs-6 text-center">
																				'.$dowFileDEDokumentNU.'
																			</div>
																		</div>
																		
																	</div>';
											}elseif($naplata == 2){
												$ugovor_kartica = '
													<div class="col-xs-4">	
														<div class = "row">
															<div class = "col-xs-12 text-center">
																'.$jezikDokumentNU.'
															</div>
														</div>
														<div class = "row" style = "margin-top: 10px;">
															<div class = "col-xs-12 text-center">
																'.$dowFileDokumentNU.'
															</div>
														</div>
													</div>
												';
											}
											
											
											echo '
												<div class="row" style = "text-align: -webkit-center;">
													<div class = "content_box" style = "min-height: 1px; padding: 12px;">
														<div class="panel '.$cardIzgledNU.' panel-default pandef">
															<div class="panel-heading panhead">
																<h3 class="panel-title pantit">Ugovor - '.$statusDokumentNU.' </h3>
															</div>
															<div class="panel-body panbod">
																<div class = "row">
																	<div class = "col-xs-8">
																		<div class = "row">
																			<div class = "col-xs-3 text-center">
																				<i class="fa fa-user" aria-hidden="true"></i>
																			</div>
																			<div class = "col-xs-9 text-left">
																				'.$zaposlenikDokumentNU.'
																			</div>
																		</div>
																		<div class = "row">
																			<div class = "col-xs-3 text-center">
																				<i class="fa fa-calendar-plus-o" aria-hidden="true"></i>
																			</div>
																			<div class = "col-xs-9 text-left">
																				'.$datumDokumentNU.'
																			</div>
																		</div>
																	</div>
																	'.$ugovor_kartica.'
																</div>
															</div>
														</div>
													</div>
												</div>
											';
										}
										if($brojNoviUgovori != 0){
											echo '
													</div>
												</div>
											';
										}
									//Ugovori END 
									
									//Predracuni START
										//NP skracenica novi predracuni
										$queryNoviPredracuni = $db->prepare("
											SELECT 
												pr_naplata_preko, 
												pr_broj_predracuna, 
												pr_datum_kreiranja, 
												pr_zaposlenik, 
												pr_rata, 
												pr_file,
												pr_file_de,
												pr_status,
												pr_domaca_valuta
											FROM idk_predracuni
											WHERE 
												pr_kandidat_id = :pr_kandidat_id 
												AND 
												pr_vrsta_predracuna = :pr_vrsta_predracuna 
												AND 
												pr_stornirano = :pr_stornirano
												AND 
												pr_naplata_preko IN (1,2)
										");
										$queryNoviPredracuni->execute(array(
											':pr_kandidat_id' => $kanId,
											':pr_vrsta_predracuna' => 1,
											':pr_stornirano' => 0
										));
										$brojNoviPredracuni = $queryNoviPredracuni->rowCount();
										if($brojNoviPredracuni != 0){
											echo '
												<div class="panel panel-default">
													<div class="panel-heading text-center"><h3 class="panel-title" style = "font-weight: bold;">Predračun/i</h3></div>
													<div class="panel-body">
											';
										}
										while($rowNoviPredracuni = $queryNoviPredracuni->fetch()){
											$cardIzgledNP = "";
											$brojNP = $rowNoviPredracuni["pr_broj_predracuna"];
											$datumNP = date("d.m.Y H:i:s", strtotime($rowNoviPredracuni["pr_datum_kreiranja"]));
											$zaposlenik_id = $rowNoviPredracuni["pr_zaposlenik"];
											if($zaposlenik_id != 67){
												$zaposlenikNP = getZaposlenikimeR($rowNoviPredracuni["pr_zaposlenik"]);
											}else{
												$zaposlenikNP = "Automatski";
											}
											$rataNP = intval($rowNoviPredracuni["pr_rata"]);
											$fileNP = $rowNoviPredracuni["pr_file"];
											$fileDENP = $rowNoviPredracuni["pr_file_de"];
											$statusNP = intval($rowNoviPredracuni["pr_status"]);
											$valutaNP = $rowNoviPredracuni["pr_domaca_valuta"];
											if($statusNP != 0){
												$statusNP = "Aktivan";
												$cardIzgledNP = "pan";
											}else{
												$statusNP = "Arhiviran";
												$cardIzgledNP = "pan1";
											}
											if($valutaNP == "BAM"){
												$valutaNP = '<img src="images/bs3d.png" width="25">';
											}else if($valutaNP == "RSD"){
												$valutaNP = '<img src="images/sr3d.png" width="25">';
											}else{
												$valutaNP = '<img src="images/de3d.png" width="25">';
											}
											$predracunKreiranNP = "";
											if($fileNP != NULL){
												$predracunKreiranNP = '<a href="'.getSiteUrlr().'files/predracuni_dipl/'.$fileNP.'" class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK"><i class="fa fa-file-pdf-o fa-3x" title = "Ugovor za kandidata!" aria-hidden="true"></i></a>';
											}else{
												$predracunKreiranNP = '<button href="'.getSiteUrlr().'files/predracuni_dipl/'.$fileNP.'" disabled="disabled" class="btn material-btn material-btn_warning main-container__column" style = "padding: 5px;" target="_BLANK"><i class="fa fa-file-pdf-o fa-3x" title = "Čeka se prihvatanje!" aria-hidden="true"></i></button>';
											}
											$divDowNP = "";
											if($fileDENP != NULL){
												$divDowNP = '
													<div class = "row">
														<div class = "col-xs-6 text-center">
															'.$valutaNP.'
														</div>
														<div class = "col-xs-6 text-center">
															<img src="images/de3d.png" width="25">
														</div>
													</div>
													<div class = "row" style = "margin-top: 10px;">
														<div class = "col-xs-6 text-center">
															<a href="'.getSiteUrlr().'files/predracuni_dipl/'.$fileNP.'" class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK"><i class="fa fa-file-pdf-o fa-3x" title = "Ugovor za kandidata!" aria-hidden="true"></i></a>
														</div>
														<div class = "col-xs-6 text-center">
															<a href="'.getSiteUrlr().'files/predracuni_dipl/'.$fileDENP.'" class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK"><i class="fa fa-file-pdf-o fa-3x" title = "Ugovor za kandidata!" aria-hidden="true"></i></a>
														</div>
													</div>
												';
											}else{
												$divDowNP = '
													<div class = "row">
														<div class = "col-xs-12 text-center">
															'.$valutaNP.'
														</div>
													</div>
													<div class = "row" style = "margin-top: 10px;">
														<div class = "col-xs-12 text-center">
															'.$predracunKreiranNP.'
														</div>
													</div>
												';
											}
											echo '
												<div class="row" style = "text-align: -webkit-center;">
													<div class = "content_box" style = "min-height: 1px; padding: 12px;">
														<div class="panel '.$cardIzgledNP.' panel-default pandef">
															<div class="panel-heading panhead">
																<h3 class="panel-title pantit">'.$statusNP.' predračun '.$brojNP.' RATA '.$rataNP.'</h3>
															</div>
															<div class="panel-body panbod">
																<div class = "row">
																	<div class = "col-xs-8">
																		<div class = "row">
																			<div class = "col-xs-3 text-center">
																				<i class="fa fa-user" aria-hidden="true"></i>
																			</div>
																			<div class = "col-xs-9 text-left">
																				'.$zaposlenikNP.'
																			</div>
																		</div>
																		<div class = "row">
																			<div class = "col-xs-3 text-center">
																				<i class="fa fa-calendar-plus-o" aria-hidden="true"></i>
																			</div>
																			<div class = "col-xs-9 text-left">
																				'.$datumNP.'
																			</div>
																		</div>
																	</div>
																	<div class = "col-xs-4">
																		'.$divDowNP.'
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											';
										}
										if($brojNoviPredracuni != 0){
											echo '
													</div>
												</div>
											';
										}
									//Predracuni END 
								//NOVI NACIN UPLATA PREKO 1 END
							}
							
							//Uplatnice START
								//SUP skracenica za stare uplatnice
								$queryStareUuplatnice = $db->prepare("
									SELECT 
										id_dokument_nd,
										naziv_dokument_nd,
										naziv_dokument_ostali_nd,
										vrijeme_dodavanja_dokument_nd,
										dodao_zaposlenik_dokument_nd,
										tip_dokumenta_status
									FROM 
										idk_nd_kandidata_dokumenti
                                    WHERE
										id_kandidata_dokument_nd = :id_kandidata_dokument_nd 
										AND 
										status_dokument_nd is null 
										AND 
										naziv_dokument_ostali_nd LIKE 'uplatnica' 
										AND 
										tip_dokumenta = 2
								");
								$queryStareUuplatnice->execute(array(
									':id_kandidata_dokument_nd' => $kanId
								));
								$brojStareUuplatnice = $queryStareUuplatnice->rowCount();
								if($brojStareUuplatnice != 0){
									echo '
										<div class="panel panel-default">
											<div class="panel-heading text-center"><h3 class="panel-title" style = "font-weight: bold;">Uplatnica/e</h3></div>
											<div class="panel-body">
									';
								}
								while($rowStareUuplatnice = $queryStareUuplatnice->fetch()){
									$cardIzgledSUP = "";
									$idDokumentSUP = $rowStareUuplatnice["id_dokument_nd"];
									$fileDokumentSUP = '<a href="'.getSiteUrlr().'files/ugovori_uplatnice_dipl/'.$rowStareUuplatnice["naziv_dokument_nd"].'" class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK"><i class="fa fa-file-pdf-o fa-3x" aria-hidden="true"></i></a>';
									$nazivDokumentSUP = $rowStareUuplatnice["naziv_dokument_ostali_nd"];
									$vrijemeDokumentSUP = date('d.m.Y H:i', strtotime($rowStareUuplatnice["vrijeme_dodavanja_dokument_nd"]));
									$zaposlenikDokumentSUP = getZaposlenikimeR($rowStareUuplatnice["dodao_zaposlenik_dokument_nd"]);
									$statusDokumentSUP = intval($rowStareUuplatnice["tip_dokumenta_status"]);
									if($statusDokumentSUP == 1){
										$statusDokumentSUP = "Aktivan";
										$cardIzgledSUP = "pan";
									}else{
										$statusDokumentSUP = "Arhiviran";
										$cardIzgledSUP = "pan1";
									}
									echo '
										<div class="row" style = "text-align: -webkit-center;">
											<div class = "content_box" style = "min-height: 1px; padding: 12px;">
												<div class="panel '.$cardIzgledSUP.' panel-default pandef">
													<div class="panel-heading panhead">
														<h3 class="panel-title pantit">'.$nazivDokumentSUP.' - '.$statusDokumentSUP.' </h3>
													</div>
													<div class="panel-body panbod">
														<div class = "row">
															<div class = "col-xs-8">
																<div class = "row">
																	<div class = "col-xs-3 text-center">
																		<i class="fa fa-user" aria-hidden="true"></i>
																	</div>
																	<div class = "col-xs-9 text-left">
																		'.$zaposlenikDokumentSUP.'
																	</div>
																</div>
																<div class = "row">
																	<div class = "col-xs-3 text-center">
																		<i class="fa fa-calendar-plus-o" aria-hidden="true"></i>
																	</div>
																	<div class = "col-xs-9 text-left">
																		'.$vrijemeDokumentSUP.'
																	</div>
																</div>
															</div>
															<div class = "col-xs-4">
																'.$fileDokumentSUP.'
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									';
								}
								if($brojStareUuplatnice != 0){
									echo '
											</div>
										</div>
									';
								}
							//Uplatnice END 
						}
						
						//Certifikati START
							//C skracenica za certifikate
							$queryCertifikati = $db->prepare("
								SELECT 
									id_dokument_nd,
									naziv_dokument_nd,
									naziv_dokument_ostali_nd,
									vrijeme_dodavanja_dokument_nd,
									dodao_zaposlenik_dokument_nd,
									tip_dokumenta_status
								FROM 
									idk_nd_kandidata_dokumenti
                                WHERE
									id_kandidata_dokument_nd = :id_kandidata_dokument_nd 
									AND 
									status_dokument_nd is null 
									AND 
									naziv_dokument_ostali_nd LIKE '%Certifikat%' 
									AND 
									tip_dokumenta = 3
							");
							$queryCertifikati->execute(array(
								':id_kandidata_dokument_nd' => $kanId,
							));
							$brojCertifikati = $queryCertifikati->rowCount();
							if($brojCertifikati != 0){
								echo '
									<div class="panel panel-default">
										<div class="panel-heading text-center"><h3 class="panel-title" style = "font-weight: bold;">Certifikat/i</h3></div>
										<div class="panel-body">
								';
							}
							while($rowCertifikati = $queryCertifikati->fetch()){
								$cardIzgledC = "";
								$idDokumentC = $rowCertifikati["id_dokument_nd"];
								$fileDokumentC = '<a href="'.getSiteUrlr().'files/certifikati_dipl/'.$rowCertifikati["naziv_dokument_nd"].'" class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK"><i class="fa fa-file-pdf-o fa-3x" aria-hidden="true"></i></a>';
								$nazivDokumentC = $rowCertifikati["naziv_dokument_ostali_nd"];
								$vrijemeDokumentC = date('d.m.Y H:i', strtotime($rowCertifikati["vrijeme_dodavanja_dokument_nd"]));
								$zaposlenikDokumentC = getZaposlenikimeR($rowCertifikati["dodao_zaposlenik_dokument_nd"]);
								$statusDokumentC = intval($rowCertifikati["tip_dokumenta_status"]);
								if($statusDokumentC == 1){
									$statusDokumentC = "Aktivan";
									$cardIzgledC = "pan";
								}else{
									$statusDokumentC = "Arhiviran";
									$cardIzgledC = "pan1";
								}
								echo '
									<div class="row" style = "text-align: -webkit-center;">
										<div class = "content_box" style = "min-height: 1px; padding: 12px;">
											<div class="panel '.$cardIzgledC.' panel-default pandef">
												<div class="panel-heading panhead">
													<h3 class="panel-title pantit">'.$nazivDokumentC.' - '.$statusDokumentC.' </h3>
												</div>
												<div class="panel-body panbod">
													<div class = "row">
														<div class = "col-xs-8">
															<div class = "row">
																<div class = "col-xs-3 text-center">
																	<i class="fa fa-user" aria-hidden="true"></i>
																</div>
																<div class = "col-xs-9 text-left">
																	'.$zaposlenikDokumentC.'
																</div>
															</div>
															<div class = "row">
																<div class = "col-xs-3 text-center">
																	<i class="fa fa-calendar-plus-o" aria-hidden="true"></i>
																</div>
																<div class = "col-xs-9 text-left">
																	'.$vrijemeDokumentC.'
																</div>
															</div>
														</div>
														<div class = "col-xs-4">
															'.$fileDokumentC.'
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								';
							}
							if($brojCertifikati != 0){
								echo '
										</div>
									</div>
								';
							}
						//Certifikati END 
						
						
					}else{
						//U slucaju da je u postu null vrijednost skripta ce izbaciti ovu poruku greske
						echo '
							<div class="row">
								<div class = "content_box" style = "min-height: 1px;">
									<div class="alert alert-danger" role="alert">GREŠKA!<br>Obratite se administratoru sistema!</div>
								</div>
							</div>
						';
					}
				break;
				
				case "smjer_skole":
					$naziv_skole = $_POST['skola_naziv'];
					if(isset($_POST['smjer_naziv']))
						$smjer_naziv_filter = $_POST['smjer_naziv'];
					else
						$smjer_naziv_filter = array();
					
					$skola_naziv = array();
					
					if(is_array($naziv_skole)){
						$skola_naziv = $naziv_skole;
					} else {
						array_push($skola_naziv, $naziv_skole);
					}
					
					$skole_id = array();
					for($i = 0; $i < count($skola_naziv); $i++){
						$query_skola = $db->prepare("SELECT skola_id FROM idk_skole WHERE skola_naziv IN ('".$skola_naziv[$i]."')");
						$query_skola->execute();
						while($row_skola = $query_skola->fetch()){
							$skola_id = $row_skola['skola_id'];
							$skole_id[] = $skola_id;
						}
					}
					$skole_id_result = array_unique($skole_id);
					$skole_idImp = implode(",", $skole_id_result);
					
					$query_skole_smjer = $db->prepare("
							SELECT ss_naziv,skola_naziv FROM idk_skole_smjerovi
							JOIN idk_skole
							ON idk_skole.skola_id = ss_skola_id
							WHERE ss_skola_id IN (".$skole_idImp.")
					");
					$query_skole_smjer->execute();
					
					while($row_skola_smjer = $query_skole_smjer->fetch()){
						$smjer_naziv = $row_skola_smjer['ss_naziv'];
						$skola_sub = $row_skola_smjer['skola_naziv'];
						if(in_array($smjer_naziv,$smjer_naziv_filter))
						echo '<option value="'.$smjer_naziv.'" class="smjerovi_all " data-subtext="'.$skola_sub.'" selected>'.$smjer_naziv.'</option>';
						else
						echo '<option value="'.$smjer_naziv.'" class="smjerovi_all " data-subtext="'.$skola_sub.'">'.$smjer_naziv.'</option>';
						
					}
				break;
			
				case "smjer_struke":
				
					$struka_id = [];
					if(isset($_POST["struka_id"]) && is_array($_POST["struka_id"]))
					{
						$struka_id = $_POST["struka_id"];
					}

					$smjer_naziv_filter = $_POST['smjer_naziv'] ?? [];
					$struka_idImp = implode(",", $struka_id);
					$query_struke_smjer = $db->prepare("
							SELECT ss_naziv FROM idk_skole_smjerovi WHERE ss_struka_id IN (".$struka_idImp.")
					");
					$query_struke_smjer->execute();
					
					while($row_struke_smjer = $query_struke_smjer->fetch()){
						$smjer_naziv = $row_struke_smjer['ss_naziv'];

						if(in_array($smjer_naziv,$smjer_naziv_filter))
							echo '<option value="'.$smjer_naziv.'" class="smjerovi_all" selected>'.$smjer_naziv.'</option>';
						else
							echo '<option value="'.$smjer_naziv.'" class="smjerovi_all">'.$smjer_naziv.'</option>';
					}
				break;
				
				case "get_financije_ch_racuni":
					
					$filter_period 			= $_POST['filter_period'];
					$filter_status 			= $_POST['filter_status'];
					$filter_select_drzavu 	= $_POST['filter_select_drzavu'];
					
					$uslov_datum_kreiranja 	= "";
					$uslov_status 			= "";
					$uslov_drzava 			= "";
					$flag_vise_drzava 		= false;
					
					if(strpos($filter_period, "to")){
						$tmp_period = array();
						$tmp_period = explode('to', $filter_period);
						$filter_kreiran_racun_datum_od = date("Y-m-d 00:00:00", strtotime($tmp_period[0]));
						$filter_kreiran_racun_datum_do = date("Y-m-d 23:59:59", strtotime($tmp_period[1]));
						$uslov_datum_kreiranja = " AND rc.racun_datum_kreiranja BETWEEN '".$filter_kreiran_racun_datum_od."' AND '".$filter_kreiran_racun_datum_do."'";
					}
					else if($filter_period != ""){
						$uslov_datum_kreiranja = " AND rc.racun_datum_kreiranja = ".date("d.m.Y",strtotime($filter_period));
					}
					if($filter_status != ""){
						$uslov_status = "AND rc.racun_status IN (".implode(',', $filter_status).")";
					}
					
					if(count($filter_select_drzavu) > 1){
						$flag_vise_drzava = true;
					}
					
					if($filter_select_drzavu != ""){
						$uslov_drzava = " AND rc.racun_domaca_valuta IN (".implode(',', $filter_select_drzavu).")";
					}
					
					$query_get_racune = $db -> prepare("
						SELECT kan.ime_nd_kandidata, kan.prezime_nd_kandidata, rc.racun_broj, rc.racun_datum_kreiranja, rc.racun_id,
						rc.racun_vrijednost_EUR, rc.racun_status, rc.racun_domaca_valuta, rc.racun_file, rc.racun_file_de, rc.racun_kandidat_id, rc.racun_stornirano
						FROM idk_racuni rc
						JOIN idk_nd_kandidata kan
						ON kan.id_broj_nd_kandidata = rc.racun_kandidat_id
						WHERE rc.racun_broj LIKE ('%CH%')
						".$uslov_datum_kreiranja."
						".$uslov_status."
						".$uslov_drzava."
					");
					
					// var_dump($query_get_racune);
					// exit();
					
					$query_get_racune -> execute();
					?>
					<table id = "table_racuni_ch" class="striped col-12">
						<thead>
							<th>#</th>
							<th>Ime kandidata</th>
							<?php 
							if($flag_vise_drzava){
								echo "<th>Država</th>";
							}
							?>
							<th>Broj</th>
							<th>Datum kreiranja</th>
							<th>Vrijednost</th>
							<th>Status</th>
							<th></th>
						</thead>
						<tbody>
						<?php
						$cnt = 0;
						while($row_get_racune = $query_get_racune -> fetch()){
							$cnt++;
							$kandidat_full_name 	= $row_get_racune['ime_nd_kandidata'].' '.$row_get_racune['prezime_nd_kandidata'];
							$racun_broj 			= $row_get_racune['racun_broj'];
							$racun_datum_kreiranja 	= date("d.m.Y", strtotime($row_get_racune['racun_datum_kreiranja']));
							$racun_vrijednost_EUR 	= $row_get_racune['racun_vrijednost_EUR'];
							$racun_status 			= $row_get_racune['racun_status'];
							$racun_domaca_valuta 	= $row_get_racune['racun_domaca_valuta'];
							$racun_kandidat_id 		= $row_get_racune['racun_kandidat_id'];
							$racun_stornirano 		= $row_get_racune['racun_stornirano'];
							$racun_file 			= $row_get_racune['racun_file'];
							$racun_file_de 			= $row_get_racune['racun_file_de'];
							$racun_id 				= $row_get_racune['racun_id'];
							
							$drzava_img 	= "";
							$status_text 	= "";

							if($racun_domaca_valuta == 'BAM'){
								$drzava_img = '<img src = "'.getSiteUrlr().'images/bs3d.png" width="25">';
							}
							else if($racun_domaca_valuta == 'RSD'){
								$drzava_img = '<img src = "'.getSiteUrlr().'images/sr3d.png" width="25">';
							}
							else{
								$drzava_img = '<img src = "'.getSiteUrlr().'images/globe3d.png" width="25">';
							}

							if($racun_status == 1){
								$status_text = '<span class="label label-info material-label material-label_info main-container__column">Aktivan</span>';
								$boja = 'success';
								$plusminus = '';
							}else if($racun_status == 2){
								$status_text = '<span class="label label-success material-label material-label_success main-container__column">Storno račun</span>';
								$boja = 'danger';
								$plusminus = '-';
							}
							$disabled_action = "";
							$boja_action = "";
							
							if($racun_stornirano == 0){
								$boja_action 		= "";
								$disabled_action 	= "storniraj_modal";
							}
							else if($racun_stornirano == 1){
								$boja_action 		= 'background-color:red;';
								$disabled_action 	= "";
							}
							echo '<tr>';
							echo '<td class="text-center">'.$cnt.'</td>';
							echo '<td class="text-center"><a href = "'.getSiteUrlr().'nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$racun_kandidat_id.'">'.$kandidat_full_name.'</a></td>';
							if($flag_vise_drzava){
								echo '<td class="text-center">'.$drzava_img.'</td>';								
							}
							echo '<td class="text-center">'.$racun_broj.'</td>';
							echo '<td class="text-center" data-order = "'.date("Y-m-d",strtotime($racun_datum_kreiranja)).'">'.$racun_datum_kreiranja.'</td>';
							echo '<td class="text-center"><a href = "'.getSiteUrlr().'files/racuni_dipl/'.$racun_file.'" target="_BLANK" class="label label-'.$boja.' material-label material-label_'.$boja.' main-container__column">'.$plusminus.' '.$racun_vrijednost_EUR.' &euro;</a></td>';
							echo '<td class="text-center">'.$status_text.'</td>';
							echo '
								<td class="text-center">
									<div class="btn-group material-btn-group">
										<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_success idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
										<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_success idk_dropdown_table" role="menu">
											<li><a href = "'.getSiteUrlr().'files/racuni_dipl/'.$racun_file.'" target="_BLANK" class="material-dropdown-menu__link" style="font-size:11px;"><i class="fa fa-download" aria-hidden="true"></i> Download</a></li>
											<li><a href = "'.getSiteUrlr().'files/racuni_dipl/'.$racun_file_de.'" target="_BLANK" class="material-dropdown-menu__link" style="font-size:11px;"><i class="fa fa-download" aria-hidden="true"></i> Download (DE)</a></li>
											<li><a href = "#" racun_id = "'.$racun_id.'" data-toggle="modal" data-target="#'.$disabled_action.'" class="storniraj_button material-dropdown-menu__link" style="font-size:11px;'.$boja_action.'" ><i class="fa fa-ban" aria-hidden="true" ></i> Storniraj račun</a></li>
										</ul>
									</div>
								</td>
							';
							echo '</tr>';
						}
						?>
						</tbody>
					</table>
					<script>
						$('#storniraj_racun_razlog').selectpicker();
						
						$('.storniraj_button').click(function () {
							var racun_id 	= $(this).attr('racun_id');
							$('#selected_racun_id').val(racun_id);
						});
						
						$('#storniraj_ask_confirmation').on('click', function(){
							var racun_id 	= $('#selected_racun_id').val();
							var razlog_id  	= $('#storniraj_racun_razlog').val();

							
							if(razlog_id == null){
								$('#select_razlog_label').effect('highlight');
								$('#select_razlog_label').effect('bounce');
								$('#select_razlog_label').effect('highlight');
							}
							else{
								$('#storniraj_ask_confirmation').prop('disabled',true);
								$('#storniraj_modal_close').prop('disabled',true);
								
								$.ajax({
									url: 'ajax_data.php?page=get_storniranje_racuna_confirmation',
									type: 'POST',
									dataType: 'html',
									data:{
										'racun_id' : racun_id,
										'razlog_id' : razlog_id
									},
									success: function(data) {
										$('#to_append_to_action_info').empty().append(data).show('fold',600,function(){
											$('#storniraj_modal_footer').effect('fold', 300);
										});
										$('#storniraj_confirmation_no').on('click',function(){
											$('#to_append_to_action_info').effect('fold',600, function(){
												$('#storniraj_ask_confirmation').prop('disabled',false);
												$('#storniraj_modal_close').prop('disabled',false);
												$('#storniraj_modal_footer').show('fold',300);
											});
										});
										$('#storniraj_confirmation_yes').on('click',function(){
											var dodatni_komentar  	= $('#storniraj_dodatni_komentar').val();
											// alert(dodatni_komentar);
											$.ajax({
												url: 'ajax_data.php?page=storniraj_racun',
												type: 'POST',
												dataType: 'html',
												data:{
													'racun_id' 			: racun_id,
													'razlog_id' 		: razlog_id,
													'dodatni_komentar' 	: dodatni_komentar
												},
												success: function(data) {
													$('#to_append_to_action_info').fadeOut(300, function(){
														$('#storniraj_ask_confirmation').prop('disabled',true);
														$('#storniraj_modal_close').prop('disabled',false);
														$('#storniraj_modal_footer').fadeIn(300);
														$('#msg_racun_storniran').show('fold',300);
													});
												},
												error: function (xhr, ajaxOptions, thrownError) {
													alert(xhr.status);
													alert(thrownError);
												}
											});
										});
									},
									error: function (xhr, ajaxOptions, thrownError) {
										alert(xhr.status);
										alert(thrownError);
									}
								});								
							}
						});
						$('#storniraj_modal').on('hidden.bs.modal', function () {
							if($('#storniraj_ask_confirmation').is(':disabled') && $('#to_append_to_action_info').css('display') == "none")
								getTableRacuni();
						});
					</script>
					<div class="modal material-modal material-modal_success fade" id="storniraj_modal" style="max-height:500px;">
						<div class="modal-dialog">
							<div class="modal-content material-modal__content">
								<div class="modal-header material-modal__header">
									<button class="close material-modal__close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title material-modal__title">Storniranje računa</h4>
								</div>
								<div class="modal-body material-modal__body">
									<input style="display:none" id = "selected_racun_id">
									<label class = "col-md-6" id = "select_razlog_label" for = "storniraj_racun_razlog">Odaberite razlog storniranja računa:</label>
									<select class="selectpicker col-md-6" id="storniraj_racun_razlog" data-live-search="true" data-actions-box="true">
										<option value = "Odaberite razlog" selected disabled hidden><b>Odaberite razlog</b></option>
										<option value = "1">Pogresni podaci</option>
										<option value = "2">Pogrešnoj osobi označena uplata</option>
										<option value = "3">Povrat uplate</option>
										<option value = "4" disabled>Pogrešan iznos uplate</option>
									</select>
									<div id = "to_append_to_action_info" style="display:none;margin-top:13px;">
									</div>
									<div class="alert alert-success text-center" role="alert" style="display:none;margin-top:8px;" id="msg_racun_storniran">
										RAČUN JE STORNIRAN
									</div>
								</div>
								<div class="modal-footer material-modal__footer" id="storniraj_modal_footer">
									<button id = "storniraj_modal_close" class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
									<button id = "storniraj_ask_confirmation" class="btn btn-success material-btn material-btn_success">STORNIRAJ</button>
								</div>
							</div>
						</div>
					</div>
					<?php
						
				break;
				
				case "download_predracuni_ch":
				
					$filter_kreiran_predracun_period 	= $_POST['filter_kreiran_predracun_period'];
					$filter_uplacen_predracun_period 	= $_POST['filter_uplacen_predracun_period'];
					$filter_select_is_uplaceno 			= $_POST['filter_select_is_uplaceno'];
					$filter_select_rate 				= $_POST['filter_select_rate'];
					$filter_select_status_predracuna	= $_POST['filter_select_status_predracuna'];
					$filter_select_drzavu 				= $_POST['filter_select_drzavu'];
					
					$flag_vise_drzava 					= false;
					$uslov_vrijeme_kreiranja_predracuna = "";
					$uslov_vrijeme_uplate_predracuna 	= "";
					$uslov_is_uplacen 					= "";
					$uslov_rata 						= "";
					$uslov_status 						= "";
					$uslov_drzava						= "";

					if(count($filter_select_drzavu) > 1){
						$flag_vise_drzava = true;
					}
					$uslov_drzava = "AND pr.pr_domaca_valuta IN (".implode(',',$filter_select_drzavu).")";
					
					if(strpos($filter_kreiran_predracun_period, "to")){
						$tmp_period = array();
						$tmp_period = explode('to', $filter_kreiran_predracun_period);
						$filter_kreiran_predracun_datum_od = date("Y-m-d", strtotime($tmp_period[0]));
						$filter_kreiran_predracun_datum_do = date("Y-m-d 23:59:59", strtotime($tmp_period[1]));
						$uslov_vrijeme_kreiranja_predracuna = " AND pr.pr_datum_kreiranja BETWEEN '".$filter_kreiran_predracun_datum_od."' AND '".$filter_kreiran_predracun_datum_do."'";
					}
					else if($filter_kreiran_predracun_period != ""){
						$uslov_vrijeme_kreiranja_predracuna = " AND pr.pr_datum_kreiranja = ".date("d.m.Y",strtotime($filter_kreiran_predracun_period));
					}
					

					if(strpos($filter_uplacen_predracun_period, "to")){
						$tmp_period = array();
						$tmp_period = explode('to', $filter_uplacen_predracun_period);
						$filter_uplacen_predracun_datum_od = date("Y-m-d", strtotime($tmp_period[0]));
						$filter_uplacen_predracun_datum_do = date("Y-m-d", strtotime($tmp_period[1]));
						$uslov_vrijeme_uplate_predracuna = " AND pr.pr_datum_uplate BETWEEN '".$filter_uplacen_predracun_datum_do."' AND '".$filter_uplacen_predracun_datum_do."'";
					}
					else if($filter_uplacen_predracun_period != ""){
						$uslov_vrijeme_uplate_predracuna = " AND pr.pr_datum_uplate = ".date("d.m.Y",strtotime($filter_uplacen_predracun_period));
					}

					if(in_array('0',$filter_select_is_uplaceno) && !in_array('1',$filter_select_is_uplaceno)){
						$uslov_is_uplacen = "AND pr.pr_uplaceno = 0";
					}
					else if(!in_array('0',$filter_select_is_uplaceno) && in_array('1',$filter_select_is_uplaceno)){
						$uslov_is_uplacen = "AND pr.pr_uplaceno = 1";
					}
					
					$uslov_rata = "AND pr.pr_rata IN (".implode(',',$filter_select_rate).")";

					$uslov_status = "AND pr.pr_status IN (".implode(',',$filter_select_status_predracuna).")";
										
					$query_get_predracune = $db->prepare('
						SELECT pr.*
						FROM idk_predracuni pr
						WHERE pr.pr_naplata_preko = 1
						AND pr.pr_file IS NOT NULL
						'.$uslov_vrijeme_kreiranja_predracuna.'
						'.$uslov_vrijeme_uplate_predracuna.'
						'.$uslov_is_uplacen.'
						'.$uslov_rata.'
						'.$uslov_status.'
						'.$uslov_drzava.'
					');
					$query_get_predracune -> execute();
					// var_dump($query_get_predracune);
					$zipname = 'Predracuni.zip';
					$zip = new ZipArchive;
					$zip->open($zipname, ZipArchive::OVERWRITE);
					while($row_get_predracune = $query_get_predracune->fetch()){
						$pr_file_de = $row_get_predracune['pr_file_de'];
						$pr_broj 	= $row_get_predracune['pr_broj_predracuna'];
						$zip->addFile('files/predracuni_dipl/'.$pr_file_de, 'Predracuni_'.date("d-m-y Hi").'/'.$pr_broj.'.pdf');
					}

					$zip->close();
					
					echo $zipname;
					
				break;
				case "download_racuni_ch":
					$filter_period 			= $_POST['filter_period'];
					$filter_status 			= $_POST['filter_status'];
					$filter_select_drzavu 	= $_POST['filter_select_drzavu'];
					
					$uslov_datum_kreiranja 	= "";
					$uslov_status 			= "";
					$uslov_drzava 			= "";
					$flag_vise_drzava 		= false;
					
					if(strpos($filter_period, "to")){
						$tmp_period = array();
						$tmp_period = explode('to', $filter_period);
						$filter_kreiran_racun_datum_od = date("Y-m-d", strtotime($tmp_period[0]));
						$filter_kreiran_racun_datum_do = date("Y-m-d 23:59:59", strtotime($tmp_period[1]));
						$uslov_datum_kreiranja = " AND rc.racun_datum_kreiranja BETWEEN '".$filter_kreiran_racun_datum_od."' AND '".$filter_kreiran_racun_datum_do."'";
					}
					else if($filter_period != ""){
						$uslov_datum_kreiranja = " AND rc.racun_datum_kreiranja = ".date("d.m.Y",strtotime($filter_period));
					}
					// var_dump($uslov_datum_kreiranja);
					// exit();
					if($filter_status != ""){
						$uslov_status = "AND rc.racun_status IN (".implode(',', $filter_status).")";
					}
					
					if(count($filter_select_drzavu) > 1){
						$flag_vise_drzava = true;
					}
					
					if($filter_select_drzavu != ""){
						$uslov_drzava = " AND rc.racun_domaca_valuta IN (".implode(',', $filter_select_drzavu).")";
					}
					
					$zipname = 'files/Racuni.zip';
					$zip = new ZipArchive;
					$zip->open($zipname, ZipArchive::OVERWRITE);
					
					$query_get_racune = $db -> prepare("
						SELECT rc.racun_file_de, rc.racun_broj, rc.racun_stornirano
						FROM idk_racuni rc
						WHERE rc.racun_broj LIKE ('%CH%')
						".$uslov_datum_kreiranja."
						".$uslov_status."
						".$uslov_drzava."
					");
					$query_get_racune -> execute();
					while($row_get_racune = $query_get_racune -> fetch()){
						$racun_file_de 	= $row_get_racune['racun_file_de'];
						$racun_broj 	= $row_get_racune['racun_broj'];
						$racun_stornirano 	= $row_get_racune['racun_stornirano'];
						if($racun_stornirano == 1)
							$racun_broj = $racun_broj."S";
						else
							$racun_broj = $racun_broj;
						$zip->addFile('files/racuni_dipl/'.$racun_file_de, 'Racuni_'.date("d-m-y Hi").'/'.$racun_broj.'.pdf');
					}
					
					$zip->close();
					
					// echo var_dump($query_get_racune);
					echo $zipname;
				break;
				
				
				case "get_storniranje_racuna_confirmation":
	
					$racun_id 		= $_REQUEST['racun_id'];
					$razlog_id 		= $_REQUEST['razlog_id'];
					
					$query_get_racuni_files = $db->prepare('
						SELECT racun_file, racun_file_de, racun_domaca_valuta
						FROM idk_racuni
						WHERE racun_id = :racun_id
					');
					$query_get_racuni_files -> execute(array(':racun_id' => $racun_id));
					$row_get_racuni_files = $query_get_racuni_files -> fetch();
					
					$racun_domaca_valuta 	= $row_get_racuni_files['racun_domaca_valuta'];
					$racun_file 			= $row_get_racuni_files['racun_file'];
					$racun_file_de 			= $row_get_racuni_files['racun_file_de'];
					
					$drzava_img 			= "";

					if($racun_domaca_valuta == 'BAM'){
						$drzava_img 		= '<img src = "'.getSiteUrlr().'images/bs3d.png" width="25">';
					}
					else if($racun_domaca_valuta == 'RSD'){
						$drzava_img 		= '<img src = "'.getSiteUrlr().'images/sr3d.png" width="25">';
					}
					else{
						$drzava_img 		= '<img src = "'.getSiteUrlr().'images/globe3d.png" width="25">';
					}
				
					$racuni_download_buttons = '
						<table style = "width:100%">
							<tr>
								<td width="50%" style="margin: 0px; padding-top: 0px; padding-bottom: 0px; padding-right: 4px;">
									<a href = "'.getSiteUrlr().'files/racuni_dipl/'.$racun_file.'" target="_BLANK" style = "float:right">
										'.$drzava_img.'
									</a>
								</td>
								<td width="50%" style="margin: 0px; padding-top: 0px; padding-bottom: 0px; padding-left: 4px;">
									<a href = "'.getSiteUrlr().'files/racuni_dipl/'.$racun_file_de.'" target="_BLANK">
										<img src = "'.getSiteUrlr().'images/Germany.png" width="25" style="float:left">
									</a>
								</td>
							</tr>
						</table>
					';
					?>
					<div class="alert alert-success text-center" role="alert">
						Svi traženi podaci su unešeni! Za spremanje podataka birajte opciju <span style = "font-weight: bold;">Dodaj</span>.
						<br><span style = "font-weight: bold; ">Napomena:</span> Potvrdom unosa provjerava se da li su traženi podaci unešeni. <br>Tačnost podataka je Vaša odgovornost.
						<?php echo $racuni_download_buttons;?>
					</div>
					
					<div class="row text-center" style="margin-top:10px;">
						<label for = "storniraj_dodatni_komentar" class="col-md-12"> Uneiste dodatni komentar:</label>
						<input id = "storniraj_dodatni_komentar" class="form-control materail-input col-md-12" type="text" placeholder="Unesite dodatni komentar"></input>
					</div>
					
					<div class = "row text-center" style="margin-top:10px;">
						<button class="btn material-btn material-btn material-btn_success" id = "storniraj_confirmation_yes">DA</button>
						<button class="btn btn-success material-btn" id  = "storniraj_confirmation_no">NE</button>
					</div>
					<?php
					
				break;
				
				case "storniraj_racun":
					$racun_id 			= $_REQUEST['racun_id'];
					$razlog_id 			= $_REQUEST['razlog_id'];
					$dodatni_komentar 	= $_REQUEST['dodatni_komentar'];

					$query_update_trenutni_racun = $db->prepare('
						UPDATE idk_racuni
						SET racun_stornirano = 1, racun_razlog_storniranja = :razlog_id, racun_komentar_storniranja = :dodatni_komentar, racun_datum_storniranja = NOW()
						WHERE racun_id = :racun_id
					');
					$query_update_trenutni_racun -> execute(array(
						'racun_id' 			=> $racun_id,
						'dodatni_komentar' 	=> $dodatni_komentar,
						'razlog_id' 		=> $razlog_id
					));
					
					$query_get_racun_drzava = $db->prepare('
						SELECT predracun_id, racun_domaca_valuta
						FROM idk_racuni
						WHERE racun_id = :racun_id
					');
					$query_get_racun_drzava -> execute(array('racun_id' => $racun_id));
					$row_get_racun_drzava = $query_get_racun_drzava -> fetch();
					
					$predracun_id 	= $row_get_racun_drzava['predracun_id'];
					$domaca_valuta 	= $row_get_racun_drzava['racun_domaca_valuta'];
					
					if($domaca_valuta == "BAM")
						$drzava = "BiH";
					else if($domaca_valuta == "RSD")
						$drzava = "Srbija";
					else
						$drzava = "Njemacka";
					
					include("html_pdf_generator.php");
					generisiRacun($predracun_id, $drzava, TRUE);
					if($drzava != "Njemacka"){
						generisiRacun($predracun_id, "Njemacka", TRUE);
					}
				
					vracanjePredracunaStorn($racun_id, $razlog_id);
				break;
				
				case "add_appointment":
					$kandidat_id 	= $_POST['kandidat_id'];
					$nalog_id 		= $_POST['nalog_id'];
					$appointment_id = $_POST['date'];
					// $time_hours 	= $_POST['hours'];
					// $time_minutes 	= $_POST['minutes'];
					$time_id 		= $_POST['time_id'];
					
					// $time_format 	= date('H:i', strtotime($time_hours.':'.$time_minutes));
					$time_format 			= date("H:i", strtotime(getTimeForAppointment($time_id)));
					addAppointmentForCandidate($kandidat_id, $appointment_id, $time_format, $time_id);
					
					//Add to LOGS
					$log_desc = "Ubacio kandidata " .$kandidat_id. " u termin za casting: ".$appointment_id."" ;
					$log_type = "0";
					addToLogs($log_desc, $log_type); //Log za biljeske - $log_type = 3
				break;
				
				case "list_appointments":
					$kandidat_id 	= $_POST['kandidat_id'];
					$nalog_id 		= $_POST['nalog_id'];
					
					$query_get_candidate_appointment = $db->prepare("
																	SELECT
																		pap_date,
																		pca_time,
																		pca_id,
																		pca_appointment_id,
																		l1.link_status as status_prvog, l2.link_status as status_drugog 
																	FROM
																		idk_pp_appointments
																	JOIN
																		idk_pp_cand_appts
																	ON
																		idk_pp_appointments.pap_id = idk_pp_cand_appts.pca_appointment_id
																	LEFT JOIN idk_appointment_invite_links l1 ON l1.counter_sent = 1 AND l1.candidate_id = :kandidat_id AND l1.interview_id = pca_id
																	LEFT JOIN idk_appointment_invite_links l2 ON l2.counter_sent = 2 AND l2.candidate_id = :kandidat_id AND l2.interview_id = pca_id
																	WHERE
																		pca_kandidat_id = :kandidat_id
																	AND
																		pap_nalog_id = :nalog_id
																	AND 
																		pca_status = 1
																	");
					$query_get_candidate_appointment->execute(array(
						":nalog_id" 	=> $nalog_id,
						":kandidat_id" 	=> $kandidat_id
					));
					$count_candidate_appointment = $query_get_candidate_appointment->rowCount();
					
					if($count_candidate_appointment == 0){
					?>
						<div class="row"><div class="col-sm-12"><span class="label label-danger material-label material-label_danger main-container__column">Nema termin</span></div></div>
					<?php
					} else {
						while($result_candidat_appointment = $query_get_candidate_appointment->fetch()){
							$datum 		= $result_candidat_appointment['pap_date'];
							$vrijeme 	= $result_candidat_appointment['pca_time'];
							$pca_id 	= $result_candidat_appointment['pca_id'];
							$appt_id 	= $result_candidat_appointment['pca_appointment_id'];
							$status_prvog 	= $result_candidat_appointment['status_prvog'];
							$status_drugog 	= $result_candidat_appointment['status_drugog'];
							$datum_format = date('d.m.Y', strtotime($datum));
							$vrijeme_format = date('H:i', strtotime($vrijeme));
							[$confirmation_link_name1, $confirmation_color1, $confirmation_link_name2, $confirmation_color2] = getCastingConfirmationOutputByStatus($status_prvog, $status_drugog);
							$casting_column4 = '
								<span class= "label label-'.$confirmation_color1.' material-label material-label_'.$confirmation_color1.' material-label_xs main-container__column" style="width: 95%">'.$confirmation_link_name1.'</span>
								<span class= "label label-'.$confirmation_color2.' material-label material-label_'.$confirmation_color2.' material-label_xs main-container__column" style="width: 95%">'.$confirmation_link_name2.'</span>
							';
						?>
							<div class="row" style="margin-bottom: 2px;" >
								<div class="col-sm-12">
									<span class="label label-success material-label material-label_success main-container__column text-right" style="width: 95%"><?php echo $datum_format . " " . $vrijeme_format; ?></span>
									<?php echo $casting_column4; ?>
								</div>
							<?php 
							if($count_candidate_appointment > 1 AND ($logged_employee_id == 67 OR $logged_employee_id == 222 OR $logged_employee_id == 174 OR $logged_employee_id == 49 OR $logged_employee_id == 190)){
								?>
								<div class="col-sm-2 link">
									<a href="#" class="label label-danger material-label material-label_danger main-container__column delete_appt" id="delete_appt" data-toggle="modal" data-target="#modalDeleteAppt"  data-pca_id="<?php echo $pca_id; ?>" data-appt_id="<?php echo $appt_id; ?>" data-pca_time="<?php echo $vrijeme_format; ?>"><i class="fa fa-times" aria-hidden="true"></i></a>
								</div>
								<?php
							}
							?>
							</div>
							<?php
						}
					}
				break;
				
				case "superadmin_table":
		
					$kompanija_id 	= $_POST['kompanija_id'];
					$nalog_id 		= $_POST['nalog_id'];
					$query_user_superadmin = $db->prepare("
															SELECT
																pu_id,
																pu_fname,
																pu_lname
															FROM
																idk_pp_users
															WHERE
																pu_company_id = :kompanija_id
															AND
															 pu_id NOT IN (SELECT pua_user_id FROM idk_pp_user_access WHERE pua_status = 1 AND pua_nalog_id = :nalog_id AND pua_type = 1 AND pua_user_id IN (SELECT pua_user_id FROM idk_pp_user_access WHERE pua_status = 1 AND pua_nalog_id = :nalog_id AND pua_type = 2)) 
														");
					$query_user_superadmin->execute(array(
						":kompanija_id" => $kompanija_id,
						":nalog_id" 	=> $nalog_id
					));
					$superadminCount = 0;
				?>
					<table id="idk_table" class="display" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th>ID</th>
								<th>Ime</th>
								<th>Prezime</th>
								<th>Postavi kao</th>
								<th>Postavi kao</th>
							</tr>
						</thead>
						<tbody>
						<?php
						while($result_user_superadmin = $query_user_superadmin->fetch()){
							$user_id 	= $result_user_superadmin['pu_id'];
							$user_fname = $result_user_superadmin['pu_fname'];
							$user_lname = $result_user_superadmin['pu_lname'];
						?>
							<tr>
								<td><?php echo $user_id; ?></td>
								<td><?php echo $user_fname; ?></td>
								<td><?php echo $user_lname; ?></td>
						<?php
								$check_superadmin = $db->prepare("SELECT pua_user_id FROM idk_pp_user_access WHERE pua_status = 1 AND pua_type = 1 AND pua_user_id = :user_id AND pua_nalog_id = :nalog_id");
								$check_superadmin->execute(array(
									"user_id" 	=> $user_id,
									"nalog_id" 	=> $nalog_id
								));
								$count_superadmin = $check_superadmin->rowCount();
								if ($count_superadmin > 0){
						?>
								<td></td>
						<?php			
								} else {
						?>
									<td><a href="#"  class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive superadmin" id="superadmin<?php echo $user_id; ?>" data-value="<?php echo $user_id; ?>"><i class="fa fa-plus" aria-hidden="true"></i> <span>Superadmin</span></a></td>
						<?php
								}
						
								$check_admin = $db->prepare("SELECT pua_user_id FROM idk_pp_user_access WHERE pua_status = 1 AND pua_type = 2 AND pua_user_id = :user_id AND pua_nalog_id = :nalog_id");
								$check_admin->execute(array(
									"user_id" 	=> $user_id,
									"nalog_id" 	=> $nalog_id
								));
								$count_admin = $check_admin->rowCount();
								if($count_admin > 0){
						?>
								<td></td>
						<?php
								} else {
						?>
								<td><a href="#"  class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive add-admin"  id="admin<?php echo $user_id; ?>" data-value="<?php echo $user_id; ?>"><i class="fa fa-plus" aria-hidden="true"></i> <span>Admin</span></a></td>
						<?php
								}
						?>
							</tr>
						<?php
							$superadminCount++;
						}
						?>
						</tbody>
					</table>
				<?php	
				break;
				
				case "admin_table":
					$kompanija_id = $_POST['kompanija_id'];
					
					$query_user_admin = $db->prepare("
														SELECT
															pu_id,
															pu_fname,
															pu_lname
														FROM
															idk_pp_users
														WHERE
															pu_company_id = :kompanija_id
														AND
															pu_id NOT IN (SELECT pua_user_id FROM idk_pp_user_access WHERE pua_type = 2 AND pua_status = 1)
													");
					$query_user_admin->execute(array(
						":kompanija_id" => $kompanija_id
					));
					?>
						<table id="idk_table2" class="display" cellspacing="0" width="100%">
							<thead>
								<tr>
									<td>ID</td>
									<td>Ime</td>
									<td>Prezime</td>
									<td></td>
								</tr>
							</thead>
							<tbody id="tbody">
							
					<?php
					
					while($result_user_admin = $query_user_admin->fetch()){
						$user_id 	= $result_user_admin['pu_id'];
						$user_fname = $result_user_admin['pu_fname'];
						$user_lname = $result_user_admin['pu_lname'];
					?>
						<tr>
							<td><?php echo $user_id; ?></td>
							<td><?php echo $user_fname; ?></td>
							<td><?php echo $user_lname; ?></td>
							
						</tr>
					<?php	
					}
					?>
							</tbody>
						</table>
					<?php
				break;
				
				case "form_company":
					$kompanija_id 	= $_POST['kompanija_id'];
					$nalog_id 		= $_POST['nalog_id'];
					$query_company = $db->prepare("
													SELECT
														company_name
													FROM
														idk_companies
													WHERE
														company_id = :kompanija_id
												");
					$query_company->execute(array(
						":kompanija_id" => $kompanija_id
					));
					$result_company = $query_company->fetch();
					$company_name 	= $result_company['company_name'];
					
					$user_ids_from_access = array();
					$query_get_user_from_user_access = $db->prepare("
																	SELECT
																		pu_id
																	FROM
																		idk_pp_users
																	WHERE
																		pu_company_id = :kompanija_id
																	");
					$query_get_user_from_user_access->execute(array(
						":kompanija_id" => $kompanija_id
					));
					while($result_user = $query_get_user_from_user_access->fetch()){
						$user_id 				= $result_user['pu_id'];
						$user_ids_from_access[] = $user_id;
					}
					// var_dump($user_ids_from_access);
					// exit();	
					if($user_ids_from_access == null){
						$user_id_uslov = 1;
					} else {
						$user_ids_from_accessImp = implode(",", $user_ids_from_access);
						$user_id_uslov = " pu_id IN (".$user_ids_from_accessImp.")";
					}
					$sql = "SELECT
								pu_id,
								pu_fname,
								pu_lname,
								pua_status,
								pua_type
							FROM
								idk_pp_users
							LEFT JOIN
								idk_pp_user_access
							ON
								idk_pp_users.pu_id = idk_pp_user_access.pua_user_id
							WHERE
								pu_company_id = :kompanija_id
							AND
								(pua_nalog_id = :nalog_id OR pua_nalog_id IS NULL )
							AND
								".$user_id_uslov."";
					
				//	echo $sql;
					$query_admin_company = $db->prepare($sql);
					$query_admin_company->execute(array(
						":kompanija_id" => $kompanija_id,
						":nalog_id" 	=> $nalog_id
					));
					?>
						<div id="form-grupacije" class="form-grupacije">
							<div class="row">
								<div class="col-sm-7">
									<h3 class="company-name"><?php echo $company_name; ?></h3>
								</div>
							</div>
							<div class="row" style="margin-top: 15px;">
								<div class="col-md-offset-1 col-md-10">
									<form action="#" methode="POST">
									<input type="hidden" value="<?php echo $kompanija_id; ?>" name="partner_id" id="partner_id">
										<div class="form-group">
											<label for="broj_kandidata" class="col-sm-5 control-label">
												<strong>Broj kandidata:</strong>
											</label>
											<div class="col-sm-3">
												<div class="materail-input-block materail-input-block_success">
													<input class="form-control materail-input" type="number" name="broj_kandidata" id="broj_kandidata" autocomplete="off">
													<span class="materail-input-block__line">
													</span>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label for="broj_kandidata" class="col-sm-2 control-label">
												<strong>Korisnici:</strong>
											</label>
											<div class="col-sm-10">
												<table id="idk_table3" class="display" cellspacing="0" width="100%">
													<thead>
														<tr>
															<th>ID</th>
															<th>Ime</th>
															<th>Prezime</th>
															<th></th>
														</tr>
													</thead>
													<tbody>
													<?php
														while($result_admin_company = $query_admin_company->fetch()){
															$user_id 		= $result_admin_company['pu_id'];
															$user_fname 	= $result_admin_company['pu_fname'];
															$user_lname 	= $result_admin_company['pu_lname'];
															$user_status 	= $result_admin_company['pua_status'];
															$user_type		= $result_admin_company['pua_type'];
													?>
														<tr>
															<td><?php echo $user_id; ?></td>
															<td><?php echo $user_fname; ?></td>
															<td><?php echo $user_lname; ?></td>
													<?php
															if($user_status == 1 AND $user_type == 2){
													?>
																<td><a href="#"  class="btn material-btn material-btn-icon-danger material-btn_danger main-container__column material-btn-icon-responsive remove_partner_admin" data-value="<?php echo $user_id; ?>"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> <span>Ukloni</span></a></td>
													<?php
															} else {
													?>
															<td><a href="#"  class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive add_partner_admin" data-value="<?php echo $user_id; ?>"><i class="fa fa-plus" aria-hidden="true"></i> <span>Admin</span></a></td>
													<?php
															}
													?>
														</tr>
													<?php
														}
													?>
													</tbody>
												</table>
											</div>
										</div>
										<div class="form-group" style="margin-top: 20px;">
											<div class="col-sm-3" style="margin-left: 170px;">
												<a href="#"  class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive" id="add_partner"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj kompaniju</span></a>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						<div class="modal material-modal material-modal_success fade" id="modal_add_company_admin">
							<div class="modal-dialog modal-lg">
								<div class="modal-content material-modal__content">
									<div class="modal-header material-modal__header">
										<button class="close material-modal__close" data-dismiss="modal">&times;</button>
										<h4 class="modal-title material-modal__title">
											<i class="fa fa-plus" aria-hidden="true" style = "margin-right: 10px;">
											</i>
											Dodaj korisnika za <?php echo $company_name; ?>
										</h4>
									</div>
									<div class="modal-body material-modal__body">
										<div class = "row">
											<div class="col-md-8 col-md-offset-2">
												<form action="<?php getSiteURL(); ?>pristup_poslodavcu?page=add_user" method="post" http-equiv="Content-type" enctype="multipart/form-data"; charset = "utf-8" class="form-horizontal" id="form_add_company_admin">
													<input type="hidden" name="kompanija_id" value="<?php echo $kompanija_id; ?>">
													<input type="hidden" name="nalog_id" value="<?php echo $nalog_id; ?>">
													<div class="form-group">
														<label for="user_fname" class="col-sm-5 control-label">
															<span class="text-danger">
																*
															</span>
															Ime korisnika:
														</label>
														<div class="col-sm-7">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="user_fname" id="user_fname" autocomplete="off" placeholder="Unesite ime user-a..." required>
																<span class="materail-input-block__line">
																</span>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label for="user_lname" class="col-sm-5 control-label">
															<span class="text-danger">
																*
															</span>
															Prezime korisnika:
														</label>
														<div class="col-sm-7">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="user_lname" id="user_lname" autocomplete="off" placeholder="Unesite prezime user-a..." required>
																<span class="materail-input-block__line">
																</span>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label for="user_email" class="col-sm-5 control-label">
															<span class="text-danger">
																*
															</span>
															Email korisnika:
														</label>
														<div class="col-sm-7">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="user_email" id="user_email" autocomplete="off" placeholder="name@example.com" required>
																<span class="materail-input-block__line">
																</span>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label for="user_password" class="col-sm-5 control-label">
															<span class="text-danger">
																*
															</span>
															Password korisnika:
														</label>
														<div class="col-sm-7">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="user_password" id="user_password" autocomplete="off" placeholder="Unesite password user-a..." required>
																<span class="materail-input-block__line">
																</span>
															</div>
														</div>
													</div>
													<div class="modal-footer material-modal__footer" style = "text-align: center;">
														<button class="btn material-btn material-btn" data-dismiss="modal">
															Odustani
														</button>
														<button type="submit" class="btn btn-primary material-btn material-btn_success" form="form_add_company_admin">
															<i class="fa fa-check-square-o" aria-hidden="true" style = "margin-right: 10px;">
															</i>
															Završi
														</button>
													</div>
												</form>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?php
				break;
				
				case "ajax_companies":
					$grupacija = $_POST['grupacija'];
					$query_companies = $db->prepare("
													SELECT
														company_id,
														company_name
													FROM
														idk_companies
												
												");
					$query_companies->execute();
					if($grupacija == "DA"){?>
						<option disabled selected>Odaberi...</option>
					<?php
						while($result_companies = $query_companies->fetch()){
							$company_name 	= $result_companies['company_name'];
							$company_id 	= $result_companies['company_id'];
						?>
						<option value="<?php echo $company_id; ?>"><?php echo $company_name; ?></option>
						<?php
						}
					}
				break;
				
				case "add_superadmin":
					$user_id 	= $_POST['user_id'];
					$nalog_id 	= $_POST['nalog_id'];
					
					superadminAccess($user_id, $nalog_id);
					updateNalogPp($nalog_id);
					
				break;
				
				case "add_admin":
					$nalog_id 		= $_POST['nalog_id'];
					$user_id 		= $_POST['user_id'];
					$kompanija_id 	= $_POST['kompanija_id'];
					
					$query_check_partner = $db->prepare("
														SELECT
															ppa_id
														FROM
															idk_pp_partners
														WHERE 
															ppa_company_id = :kompanija_id
														AND
															ppa_nalog_id = :nalog_id
													");
					$query_check_partner->execute(array(
						":kompanija_id" => $kompanija_id,
						":nalog_id" 	=> $nalog_id
					));
					
					$count_partners = $query_check_partner->rowCount();
					$result_partner = $query_check_partner->fetch();
					$ppa_id 		= $result_partner['ppa_id'];
					
					if($count_partners == 0){
						$query_insert_partner = $db->prepare("
															INSERT INTO
																idk_pp_partners
																(
																	ppa_nalog_id,
																	ppa_company_id,
																	ppa_status
																)
															VALUES
																(
																	:nalog_id,
																	:company_id,
																	:status
																)
														");
						$query_insert_partner->execute(array(
							":nalog_id" 	=> $nalog_id,
							":company_id" 	=> $kompanija_id,
							":status" 		=> 1
						));
						
						$query_check_partner = $db->prepare("
														SELECT
															ppa_id
														FROM
															idk_pp_partners
														WHERE 
															ppa_company_id = :kompanija_id
														AND
															ppa_nalog_id = :nalog_id
													");
						$query_check_partner->execute(array(
							":kompanija_id" => $kompanija_id,
							":nalog_id" 	=> $nalog_id
						));
						$result_partner = $query_check_partner->fetch();
						$ppa_id 		= $result_partner['ppa_id'];
					}
					adminAccess($user_id, $nalog_id, $ppa_id);
					updateNalogPp($nalog_id);
				break;
				
				case "remove_admin":
					$user_id 		= $_POST['user_id'];
					$nalog_id 		= $_POST['nalog_id'];
					$kompanija_id 	= $_POST['kompanija_id'];
					
					$query_check_partner = $db->prepare("
														SELECT
															ppa_id
														FROM
															idk_pp_partners
														WHERE 
															ppa_company_id = :kompanija_id
														AND
															ppa_nalog_id = :nalog_id
													");
					$query_check_partner->execute(array(
						":kompanija_id" => $kompanija_id,
						":nalog_id" 	=> $nalog_id
					));
					$result_partner = $query_check_partner->fetch();
					$ppa_id 		= $result_partner['ppa_id'];
					
					$query_remove_admin = $db->prepare("
														UPDATE
															idk_pp_user_access
														SET
															pua_status = 0
														WHERE
															pua_user_id = :user_id
														AND
															pua_nalog_id = :nalog_id
														AND
															pua_partner_id = :ppa_id
														AND
															pua_type = 2
													");
					$query_remove_admin->execute(array(
						":user_id" 	=> $user_id,
						":nalog_id" => $nalog_id,
						":ppa_id" 	=> $ppa_id
					));
					updateNalogPp($nalog_id);
				break;
				
				case "add_partner_admin":
					$user_id 		= $_POST['user_id'];
					$nalog_id 		= $_POST['nalog_id'];
					$partner_id 	= $_POST['partner_id'];
					
					$query_get_ppa_id = $db->prepare("
														SELECT
															ppa_id
														FROM
															idk_pp_partners
														WHERE
															ppa_company_id = :partner_id
														AND
															ppa_status = 1
														AND
															ppa_nalog_id = :nalog_id
													");
					$query_get_ppa_id->execute(array(
						":partner_id" => $partner_id,
						":nalog_id"	  => $nalog_id
					));
					$count_ppa_id = $query_get_ppa_id->rowCount();
					$result_ppa_id = $query_get_ppa_id->fetch();
					$ppa_id = $result_ppa_id['ppa_id'];
					
					if($count_ppa_id == 0){
						$query_insert_partner = $db->prepare("
															INSERT INTO
																idk_pp_partners
																(
																	ppa_nalog_id,
																	ppa_company_id
																)
															VALUES
																(
																	:nalog_id,
																	:partner_id
																)
															");
						$query_insert_partner->execute(array(
							":nalog_id" 	=> $nalog_id,
							":partner_id" 	=> $partner_id
						));
						$ppa_id = $query_insert_partner->lastInsertId();
					}
					adminAccess($user_id, $nalog_id, $ppa_id);
					updateNalogPp($nalog_id);
				break;
				
				case "remove_superadmin":
					$nalog_id 	= $_POST['nalog_id'];
					$user_id 	= $_POST['user_id'];
					
					$query_remove_superadmin = $db->prepare("
															UPDATE
																idk_pp_user_access
															SET
																pua_status = 0
															WHERE
																pua_user_id = :user_id
															AND
																pua_nalog_id = :nalog_id
														");
					$query_remove_superadmin->execute(array(
						":user_id" => $user_id,
						":nalog_id" => $nalog_id
					));
					updateNalogPp($nalog_id);
				break;
				
				case "list_superadmin":
					$nalog_id = $_POST['nalog_id'];
					$query_list_superadmin = $db->prepare("
															SELECT
																pu_id,
																pu_fname,
																pu_lname
															FROM
																idk_pp_users
															JOIN
																idk_pp_user_access
															ON
																idk_pp_users.pu_id = idk_pp_user_access.pua_user_id
															WHERE
																pua_type = 1
															AND
																pua_nalog_id = :nalog_id
															AND 
																pua_status = 1
														");
					$query_list_superadmin->execute(array(
						":nalog_id" => $nalog_id
					));
					?>
						<table id="idk_table_list_superadmin" class="display" cellspacing="0" width="100%">
							<thead>
								<tr>
									<th>ID</th>
									<th>Ime</th>
									<th>Prezime</th>
									<th></th>
								</tr>
							</thead>
							<tbody id="tbody_superadmin">
						
					<?php
					while($result_list_superadmin = $query_list_superadmin->fetch()){
						$user_id 	= $result_list_superadmin['pu_id'];
						$user_fname = $result_list_superadmin['pu_fname'];
						$user_lname = $result_list_superadmin['pu_lname'];
					?>
								<tr>
									<td><?php echo $user_id; ?></td>
									<td><?php echo $user_fname; ?></td>
									<td><?php echo $user_lname; ?></td>
									<td><a href="#"  class="btn material-btn material-btn-icon-danger material-btn_danger main-container__column material-btn-icon-responsive remove-superadmin" id="remove_superadmin" data-value="<?php echo $user_id; ?>"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> <span>Ukloni</span></a></td>
								</tr>
					<?php
					}
					?>
							</tbody>
						</table>
					<?php
				break;
				
				case "admin_list":
					$nalog_id 		= $_POST['nalog_id'];
					$kompanija_id 	= $_POST['kompanija_id'];
					
					$get_ppa_id = $db->prepare("SELECT ppa_id FROM idk_pp_partners WHERE ppa_nalog_id = :ppa_nalog_id AND ppa_company_id = :ppa_company_id");
					$get_ppa_id->execute(array(
								":ppa_nalog_id" => $nalog_id,
								":ppa_company_id" => $kompanija_id
					));
					$row_ppa_id = $get_ppa_id->fetch();
					$ppa_id = $row_ppa_id['ppa_id'];
					
					$query_list_admin = $db->prepare("
														SELECT
															pu_id,
															pu_fname,
															pu_lname
														FROM
															idk_pp_users
														JOIN
															idk_pp_user_access
														ON
															idk_pp_users.pu_id = idk_pp_user_access.pua_user_id
														WHERE
															pua_type = 2
														AND
															pua_nalog_id = :nalog_id
														AND
															pua_partner_id = :kompanija_id
														AND
															pua_status = 1
													");
					$query_list_admin->execute(array(
						":nalog_id" 	=> $nalog_id,
						":kompanija_id" => $ppa_id
					));
				?>
						<table id="idk_table_list_admin" class="display" cellspacing="0" width="100%">
							<thead>
								<tr>
									<th>ID</th>
									<th>Ime</th>
									<th>Prezime</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
				<?php
					while($result_list_admin = $query_list_admin->fetch()){
						$user_id 	= $result_list_admin['pu_id'];
						$user_fname = $result_list_admin['pu_fname'];
						$user_lname = $result_list_admin['pu_lname'];
				?>
							<tr>
								<td><?php echo $user_id; ?></td>
								<td><?php echo $user_fname; ?></td>
								<td><?php echo $user_lname; ?></td>
								<td><a href="#"  class="btn material-btn material-btn-icon-danger material-btn_danger main-container__column material-btn-icon-responsive remove-admin" data-value="<?php echo $user_id; ?>"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> <span>Ukloni</span></a></td>
							</tr>
				<?php
					}
				?>
							</tbody>
						</table>
				<?php
				break;
				
				case "list_partner":
					$nalog_id 		= $_POST['nalog_id'];
					$kompanija_id 	= $_POST['kompanija_id'];
					$query_get_partner = $db->prepare("
														SELECT
															company_name,
															company_id,
															ppa_id
														FROM
															idk_companies
														JOIN
															idk_pp_partners
														ON
															idk_companies.company_id = idk_pp_partners.ppa_company_id
														WHERE
															ppa_nalog_id = :nalog_id
														AND
															ppa_status = 1
														AND
															company_id != :kompanija_id
													");
					$query_get_partner->execute(array(
						":nalog_id" 	=> $nalog_id,
						":kompanija_id" => $kompanija_id
					));
					while($result_get_partner = $query_get_partner->fetch()){
						$company_name 	= $result_get_partner['company_name'];
						$company_id 	= $result_get_partner['company_id'];
						$ppa_id 	= $result_get_partner['ppa_id'];
						$query_check_admins = $db->prepare("
														SELECT
															pua_id
														FROM
															idk_pp_user_access
														WHERE
															pua_nalog_id = :nalog_id
														AND
															pua_partner_id = :kompanija_id
														AND
															pua_status = 1
														AND 
															pua_type = 2
													");
						$query_check_admins->execute(array(
							":nalog_id" 	=> $nalog_id,
							":kompanija_id" => $company_id
						));
						$count_admins = $query_check_admins->rowCount();
					?>
						<div class="panel-group material-accordion material-accordion_success" id="accordion<?php echo $company_id; ?>" style="margin-left: 100px;">
							<div class="panel panel-success material-accordion__panel material-accordion__panel">
								<div class="panel-heading material-accordion__heading">
									<h4 class="panel-title">
									<a class="material-accordion__title" style="margin-bottom:0.3rem" data-toggle="collapse" data-parent="#accordion<?php echo $company_id; ?>" href="#listaPartnera<?php echo $company_id; ?>"><span class="glyphicon glyphicon-briefcase" aria-hidden="true" style="margin-right: 10px;"></span><?php echo $company_name; ?></a>
									</h4>
								</div>
								<div id="listaPartnera<?php echo $company_id; ?>" class="panel-collapse collapse material-accordion__collapse">
									<div class="panel-body">
										<div class="col-md-offset-1 col-md-8">
											<div class="form-group">
												<div class="row">
												<?php
													if($count_admins > 0){
														echo '<div class="col-sm-8"><p class="text-danger">Da biste uklonili partnera morate ukloniti admine!!</p></div>';
													}
												?>
													<div class="col-sm-2" style="margin-left: 450px;">
														<a href="#"  class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive edit_partner" data-value="<?php echo $company_id; ?>" id="edit_partner"><i class="fa fa-pencil" aria-hidden="true"></i> <span>Edit</span></a>
													</div>
													<div class="col-sm-2">
														<a href="#" class="btn material-btn material-btn-icon-danger material-btn_danger main-container__column material-btn-icon-responsive" data-value="<?php echo $company_id; ?>" id="remove_partner" <?php if($count_admins > 0){ echo "disabled";}?>><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> <span>Ukloni</span></a>
													</div>
												</div>
											</div>
											<div class="from-group">
											<input type="hidden" id="partner_id" value="<?php echo $company_id; ?>">
											<input type="hidden" id="pravi_partner_id" value="<?php echo $ppa_id; ?>">
												<div class="row">
													<label for="list_partner_admin" class="col-sm-3 control-label"><strong>Admin lista:</strong></label>
													<div class="col-sm-9" id="list_partner_admin">
														<table class="table table-striped">
															<thead>
																<tr>
																	<th scope="col">ID</th>
																	<th scope="col">Ime</th>
																	<th scope="col">Prezime</th>
																	<th scope="col"></th>
																</tr>
															</thead>
															<tbody>
												<?php
													$query_get_partner_admin = $db->prepare("
																							SELECT
																								pu_id,
																								pu_fname,
																								pu_lname
																							FROM
																								idk_pp_users
																							JOIN
																								idk_pp_user_access
																							ON
																								idk_pp_user_access.pua_user_id = idk_pp_users.pu_id
																							WHERE
																								pua_nalog_id = :nalog_id
																							AND
																								pua_partner_id = :ppa_id
																							AND
																								pua_status = 1
																							AND
																								pua_type = 2
																						");
													$query_get_partner_admin->execute(array(
														":nalog_id" 	=> $nalog_id,
														":ppa_id" 	=> $ppa_id
													));
													while($result_get_partner_admin = $query_get_partner_admin->fetch()){
														$user_id 	= $result_get_partner_admin['pu_id'];
														$user_fname = $result_get_partner_admin['pu_fname'];
														$user_lname = $result_get_partner_admin['pu_lname'];
												?>
															<tr>
																<th scope="row"><?php echo $user_id; ?></th>
																<td><?php echo $user_fname; ?></td>
																<td><?php echo $user_lname; ?></td>
																<td><a href="#"  class="btn material-btn material-btn-icon-danger material-btn_danger main-container__column material-btn-icon-responsive remove_partner_admin" data-value="<?php echo $user_id; ?>"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> <span>Ukloni</span></a></td>
															</tr>
												<?php
													}
												?>
															</tbody>
														</table>
													</div>
												</div>
											</div>
											<div id="edit_container<?php echo $company_id; ?>">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?php
					}
				break;
				
				case "add_partner":
					$nalog_id 		= $_POST['nalog_id'];
					$broj_kandidata = $_POST['broj_kandidata'];
					$partner_id 	= $_POST['partner_id'];

					if($broj_kandidata == null){
						$broj_kandidata = 0;
					}

					$query_check_partner = $db->prepare("
														SELECT
															ppa_id
														FROM
															idk_pp_partners
														WHERE 
															ppa_company_id = :partner_id
														AND
															ppa_nalog_id = :nalog_id
													");
					$query_check_partner->execute(array(
						":partner_id" 	=> $partner_id,
						":nalog_id" 	=> $nalog_id
					));
					$count_partners = $query_check_partner->rowCount();
					if($count_partners > 0){
						$query_update_partner = $db->prepare("
															UPDATE
																idk_pp_partners
															SET
																ppa_number_of_candidates = :broj_kandidata,
																ppa_status = 1
															WHERE
																ppa_company_id = :partner_id
															AND
																ppa_nalog_id = :nalog_id
														");
						$query_update_partner->execute(array(
							":nalog_id" 		=> $nalog_id,
							":partner_id" 		=> $partner_id,
							":broj_kandidata" 	=> $broj_kandidata
						));
					} else {
						$query_insert_partner = $db->prepare("
															INSERT INTO
																idk_pp_partners
																(
																	ppa_nalog_id,
																	ppa_company_id,
																	ppa_number_of_candidates,
																	ppa_status
																)
															VALUES
																(
																	:nalog_id,
																	:partner_id,
																	:broj_kandidata,
																	:status
																)
														");
						$x = $query_insert_partner->execute(array(
							":nalog_id" 		=> $nalog_id,
							":partner_id" 		=> $partner_id,
							":broj_kandidata" 	=> $broj_kandidata,
							":status" 			=> 1
						));
						
					}
					updateNalogPartner($partner_id, $nalog_id);
				break;
				
				case "get_document_type_name":
					$idDoc = intval($_POST["idDoc"]);
					if($idDoc != 0){
						$result = "";
						$result = getDocumentTypeNameR($idDoc, 1);
						echo $result;
					}else{
						echo "undefined";
					}
				break;

				case "edit_partner":
					$kompanija_id 	= $_POST['partner_id'];
					$nalog_id 		= $_POST['nalog_id'];
					$query_company = $db->prepare("
													SELECT
														company_name,
														ppa_number_of_candidates
													FROM
														idk_companies
													JOIN
														idk_pp_partners
													ON
														idk_companies.company_id = idk_pp_partners.ppa_company_id
													WHERE
														company_id = :kompanija_id
													AND
														ppa_nalog_id = :nalog_id
												");
					$query_company->execute(array(
						":kompanija_id" => $kompanija_id,
						":nalog_id"		=> $nalog_id
					));
					$result_company = $query_company->fetch();
					$company_name 	= $result_company['company_name'];
					$candidate_number 	= $result_company['ppa_number_of_candidates'];
					$query_admin_company = $db->prepare("
															SELECT
																pu_id,
																pu_fname,
																pu_lname
															FROM
																idk_pp_users
															WHERE
																pu_company_id = :kompanija_id
														");
					$query_admin_company->execute(array(
						":kompanija_id" => $kompanija_id
					));
					?>
						<div id="edit-partner" class="edit-partner">
							<div class="row">
								<div class="col-sm-7">
									<h3 class="company-name"><?php echo $company_name; ?></h3>
								</div>
								<div class="col-sm-3" style="margin-top:18px;">
									<a href="" data-toggle="modal" data-target="#modal_add_company_admin<?php echo $kompanija_id; ?>" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj user-a</span></a>
								</div>
							</div>
							<div class="row" style="margin-top: 15px;">
								<div class="col-md-offset-1 col-md-12">
									<form action="#" methode="POST">
									<input type="hidden" value="<?php echo $kompanija_id; ?>" name="partner_id" id="partner_id_edit">
										<div class="form-group" style="height: 50px;">
											<label for="broj_kandidata" class="col-sm-3 control-label" style="margin-top: 15px;">
												<strong>Broj kandidata:</strong>
											</label>
											<div class="col-sm-3">
												<div class="materail-input-block materail-input-block_success">
													<input class="form-control materail-input" type="number" name="broj_kandidata" id="broj_kandidata_edit" autocomplete="off" value="<?php echo $candidate_number; ?>">
													<span class="materail-input-block__line">
													</span>
												</div>
											</div>
										</div>
										<div class="form-group" style="margin-right: 170px;">
											<label for="partner_admin" class="col-sm-2 control-label">
												<strong>Korisnici:</strong>
											</label>
											<div class="col-sm-10" id="partner_admin">
												<table id="idk_table4" class="display" cellspacing="0" width="100%">
													<thead>
														<tr>
															<th>ID</th>
															<th>Ime</th>
															<th>Prezime</th>
															<th></th>
														</tr>
													</thead>
													<tbody>
													<?php
														while($result_admin_company = $query_admin_company->fetch()){
															$user_id 		= $result_admin_company['pu_id'];
															$user_fname 	= $result_admin_company['pu_fname'];
															$user_lname 	= $result_admin_company['pu_lname'];
															
															$get_admin_status = $db->prepare("
																							SELECT
																								pua_status
																							FROM
																								idk_pp_user_access
																							WHERE
																								pua_user_id = :user_id
																							AND
																								pua_nalog_id = :nalog_id
																							AND
																								pua_type = 2
																							");
														$get_admin_status->execute(array(
															":user_id" 	=> $user_id,
															":nalog_id" => $nalog_id
														));
														$result_get_status = $get_admin_status->fetch();
														$admin_status = $result_get_status['pua_status'];
													?>
														<tr>
															<td><?php echo $user_id; ?></td>
															<td><?php echo $user_fname; ?></td>
															<td><?php echo $user_lname; ?></td>
													<?php
															if($admin_status == 1){
													?>
																<td><a href="#"  class="btn material-btn material-btn-icon-danger material-btn_danger main-container__column material-btn-icon-responsive remove_partner_admin" data-value="<?php echo $user_id; ?>"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> <span>Ukloni</span></a></td>
													<?php
															} else {
													?>
															<td><a href="#"  class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive add_partner_admin" data-value="<?php echo $user_id; ?>"><i class="fa fa-plus" aria-hidden="true"></i> <span>Admin</span></a></td>
													<?php
															}
													?>
														</tr>
													<?php
														}
													?>
													</tbody>
												</table>
											</div>
										</div>
										<div class="form-group" style="margin-top: 50px; height: 60px;">
											<div class="col-sm-3" style="margin-left: 240px; margin-top: 20px;">
												<a href="#"  class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive" id="save_edit"><i class="fa fa-plus" aria-hidden="true"></i> <span>Spremi</span></a>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						<div class="modal material-modal material-modal_success fade" id="modal_add_company_admin<?php echo $kompanija_id; ?>">
							<div class="modal-dialog modal-lg">
								<div class="modal-content material-modal__content">
									<div class="modal-header material-modal__header">
										<button class="close material-modal__close" data-dismiss="modal">&times;</button>
										<h4 class="modal-title material-modal__title">
											<i class="fa fa-plus" aria-hidden="true" style = "margin-right: 10px;">
											</i>
											Dodaj user-a za <?php echo $company_name; ?>
										</h4>
									</div>
									<div class="modal-body material-modal__body">
										<div class = "row">
											<div class="col-md-8 col-md-offset-2">
												<form action="<?php getSiteURL(); ?>pristup_poslodavcu?page=add_user" method="post" http-equiv="Content-type" enctype="multipart/form-data"; charset = "utf-8" class="form-horizontal" id="form_add_company_admin">
													<input type="hidden" name="kompanija_id" value="<?php echo $kompanija_id; ?>">
													<input type="hidden" value="<?php echo $nalog_id; ?>" name="nalog_id" id="nalog_id">
													<div class="form-group">
														<label for="user_fname" class="col-sm-5 control-label">
															<span class="text-danger">
																*
															</span>
															Ime user-a:
														</label>
														<div class="col-sm-7">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="user_fname" id="user_fname" autocomplete="off" placeholder="Unesite ime user-a..." required>
																<span class="materail-input-block__line">
																</span>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label for="user_lname" class="col-sm-5 control-label">
															<span class="text-danger">
																*
															</span>
															Prezime user-a:
														</label>
														<div class="col-sm-7">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="user_lname" id="user_lname" autocomplete="off" placeholder="Unesite prezime user-a..." required>
																<span class="materail-input-block__line">
																</span>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label for="user_email" class="col-sm-5 control-label">
															<span class="text-danger">
																*
															</span>
															Email user-a:
														</label>
														<div class="col-sm-7">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="user_email" id="user_email" autocomplete="off" placeholder="name@example.com" required>
																<span class="materail-input-block__line">
																</span>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label for="user_password" class="col-sm-5 control-label">
															<span class="text-danger">
																*
															</span>
															Password user-a:
														</label>
														<div class="col-sm-7">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="user_password" id="user_password" autocomplete="off" placeholder="Unesite password user-a..." required>
																<span class="materail-input-block__line">
																</span>
															</div>
														</div>
													</div>
													<div class="modal-footer material-modal__footer" style = "text-align: center;">
														<button class="btn material-btn material-btn" data-dismiss="modal">
															Odustani
														</button>
														<button type="submit" class="btn btn-primary material-btn material-btn_success" form="form_add_company_admin">
															<i class="fa fa-check-square-o" aria-hidden="true" style = "margin-right: 10px;">
															</i>
															Završi
														</button>
													</div>
												</form>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?php
				break;
				
				case "remove_partner":
					$partner_id = $_POST['partner_id'];
					$nalog_id 	= $_POST['nalog_id'];
					
					$query_remove_partner = $db->prepare("
														UPDATE
															idk_pp_partners
														SET
															ppa_status = 0
														WHERE
															ppa_company_id = :partner_id
														AND
															ppa_nalog_id = :nalog_id
														");
					$query_remove_partner->execute(array(
						":partner_id" 	=> $partner_id,
						":nalog_id" 	=> $nalog_id
					));
					updateNalogPartner($partner_id, $nalog_id);
				break;
				
				case "save_edit_partner":
					$partner_id 		= $_POST['partner_id'];
					$candidate_number 	= $_POST['candidate_number'];
					
					if($partner_id != NULL && $candidate_number != NULL)
					{
						$query_submit_edit = $db->prepare("
															UPDATE
																idk_pp_partners
															SET
																ppa_number_of_candidates = :candidate_number
															WHERE
																ppa_company_id = :partner_id
														");
						$query_submit_edit->execute(array(
							":candidate_number" => $candidate_number,
							":partner_id"		=> $partner_id
						));
					}
				break;
				
				case "choose_partner":
					$nalog_id = $_POST['nalog_id'];

					$query_check_partners = $db->prepare("
														SELECT
															partneri_pp
														FROM
															idk_nalozi
														WHERE
															nalog_id = :nalog_id
														");
					$query_check_partners->execute(array(
						":nalog_id" => $nalog_id
					));
					$result_check_partners = $query_check_partners->fetch();
					$partneri_pp = $result_check_partners['partneri_pp'];
					?>
						<label for="tip_grupacija" class="col-sm-3 control-label"><strong>Sa/bez partneri:</strong></label>
						<div class="col-sm-9">
							<div class="main-container__column materail-switch materail-switch_primary">
								<input class="materail-switch__element" type="checkbox" id="switch_input1" name="tip_grupacija" value="DA" <?php if($partneri_pp == 1){ echo "checked disabled"; }?>>
								<label class="materail-switch__label" for="switch_input1"></label>
							</div>
						</div>
					<?php
				break;
				
				case "update_campaign_category":
					$kd_id 		= $_REQUEST['kd_id'];
					$category 	= $_REQUEST['category'];
					
					$query_update_campaign_category = $db -> prepare('
						UPDATE idk_kampanje_dipl
						SET kd_category = :category
						WHERE kd_id = :kd_id
					');
					$query_update_campaign_category -> execute(array(
						':category' => $category,
						':kd_id' => $kd_id
					));
				break;

				case "add_termin":
					$broj_termina 	= $_POST["broj_termina"];
					$termini 		= $_POST["termini"];
					$gradovi 		= $_POST["gradovi"];
					$nalog_id 		= $_POST["nalog_id"];
					
					$start_date = date("Y-m-d", strtotime($termini[0]));
					$end_date	= date("Y-m-d", strtotime(end($termini)));

					$query_get_nalog_name = $db->prepare("SELECT nalog_naziv FROM idk_nalozi WHERE nalog_id = :nalog_id");
					$query_get_nalog_name->execute(array(
						":nalog_id" => $nalog_id
					));
					$result_nalog_name = $query_get_nalog_name->fetch();
					$nalog_name 	   = $result_nalog_name["nalog_naziv"];

					$query_add_group = $db->prepare("
										INSERT INTO 
											idk_pp_appointment_groups
											(
												ppaq_start_date,
												ppaq_end_date,
												papq_name,
												papq_name_de,
												papq_nalog_id
											)
										VALUES
											(
												:start_date,
												:end_date,
												:name,
												:name_de,
												:nalog_id
											)
										");
					$query_add_group->execute(array(
						":start_date" => $start_date,
						":end_date"   => $end_date,
						":name" 	  => $nalog_name,
						":name_de" 	  => $nalog_name,
						":nalog_id"   => $nalog_id
					));
					$group_id = $db->lastInsertId();
					$ctr = 0;

					while($ctr < count($termini)){
						$termin = date("Y-m-d", strtotime($termini[$ctr]));
						$grad = $gradovi[$ctr];

						$add_termin = $db->prepare("
											INSERT INTO 
												idk_pp_appointments 
												(
													pap_date, 
													pap_nalog_id, 
													pap_city, 
													pap_group_id
												)
											VALUES
												(
													:date,
													:nalog_id,
													:city,
													:group_id
												)
												");
						$add_termin->execute(array(
							":date" 	=> $termin,
							":nalog_id" => $nalog_id,
							":city" 	=> $grad,
							":group_id" => $group_id
						));
						$ctr++;
					}
				break;

				case "check_termin":
					$nalog_id = $_POST["nalog_id"];

					$query_get_termini = $db->prepare("
												SELECT 
													ppaq_id,
													pap_id,
													pap_date, 
													pap_city 
												FROM 
													idk_pp_appointments 
												JOIN 
													idk_pp_appointment_groups
												ON
													idk_pp_appointments.pap_group_id = idk_pp_appointment_groups.ppaq_id
												WHERE
													pap_nalog_id = :nalog_id
												AND
													ppaq_casting_cron_executed = 0
												AND
													ppaq_interview_cron_executed = 0
												");
					$query_get_termini->execute(array(
						":nalog_id" => $nalog_id
					));
					while($result_termin = $query_get_termini->fetch()){
						$date 		= date("d.m.Y", strtotime($result_termin["pap_date"]));
						$city 		= $result_termin["pap_city"];
						$id   		= $result_termin["pap_id"];
						$group_id 	= $result_termin["ppaq_id"];
						echo
							'
								<div class="col-sm-7" style="margin-bottom: 5px; text-align: right;">
									<span class="label label-success material-label material-label_success main-container__column text-right">' . $date . ' - ' . $city .'</span>
								</div>
								<div class="col-sm-1 link">
									<a href="" class="label label-danger material-label material-label_danger main-container__column delete_appt" id="delete_appt" data-appt_id="' . $id . '" data-appt_date="' . $date . '" data-group_id="' . $group_id . '"><i class="fa fa-times" aria-hidden="true"></i></a>
								</div>
								<div class="col-sm-1 link" style="margin-right: 50px;">
									<a href="" class="label label-success material-label material-label_success main-container__column edit_appt" id="edit_appt" data-appt_id="' . $id . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>
								</div>
							';
					}
				break;

				case "delete_termin":
					$appt_id 	= $_POST["appt_id"];
					$appt_date 	= date("Y-m-d", strtotime($_POST["appt_date"]));
					$group_id 	= $_POST["group_id"];

					$appointments = array();
					$get_group_appointments = $db->prepare("
														SELECT
															pap_date
														FROM
															idk_pp_appointments
														WHERE
															pap_group_id = :group_id
														ORDER BY pap_date ASC
													");
					$get_group_appointments->execute(array(
						":group_id" => $group_id
					));
					while($result = $get_group_appointments->fetch()){
						$appointments[] = $result["pap_date"];	
					}
					$start_date = $appointments[0];
					$end_date = end($appointments);

					if(count($appointments) > 1){
						if($appt_date == $start_date){
							//postavi drugi datum iz niza kao start_date u tabeli idk_pp_appointment_groups
							$second_date = $appointments[1];
							$update_start_date = $db->prepare("UPDATE idk_pp_appointment_groups SET ppaq_start_date = :second_date WHERE ppaq_id = :group_id");
							$update_start_date->execute(array(
								":second_date" => $second_date,
								":group_id"  => $group_id
							));
						}
						elseif($appt_date == $end_date){
							//postavi predzadnji datum iz niza kao end_date u tabeli idk_pp_appointment_groups
							$second_to_last_date = $appointments[count($appointments) - 2];
							$update_end_date = $db->prepare("UPDATE idk_pp_appointment_groups SET ppaq_end_date = :second_to_last_date WHERE ppaq_id = :group_id");
							$update_end_date->execute(array(
								":second_to_last_date" => $second_to_last_date,
								":group_id"  => $group_id
							));
						}
					} else {
						//s obzirom da grupa ima samo jedan datum, onda je potrebno obrisati i citavu grupu
						$delete_appt_group = $db->prepare("DELETE FROM idk_pp_appointment_group WHERE ppaq_id = :group_id");
						$delete_appt_group->execute(array(
							":group_id" => $group_id
						));
					}

					$delete_appt = $db->prepare("DELETE FROM idk_pp_appointments WHERE pap_id = :appt_id");
					$delete_appt->execute(array(
						":appt_id" => $appt_id
					));
					
					//Add to LOGS
					$log_desc = "Obrisao termin: " .$appt_date. " , id termina: ".$appt_id."" ;
					$log_type = "0";
					addToLogs($log_desc, $log_type); //Log za biljeske - $log_type = 3
				break;

				case "list_dipl_candidates":
					$kandidat_ime 	  = $_POST["kandidat_ime"];
					$kandidat_prezime = $_POST["kandidat_prezime"];
					$kandidat_id 	  = $_POST["kandidat_id"];
					$approved_pstatuses = [1,2,3,4,6,7,8,9,10,11,12];
					$approved_status_prijave = [1,2,3,5,4,7,8,9];

					if($_POST["kandidat_mobitel"] !== "")
					{
						$kandidat_mobitel = $_POST["kandidat_mobitel"];
						$mobitel_uslov = " OR mobilni_nd_kandidata LIKE '%$kandidat_mobitel%'"; 
					}
					else 
					{
						$mobitel_uslov = 1;
					}

					$check_dipl = "SELECT 
										povezan_na_dipl, 
										kandidat_dipl_id, 
										kandidat_status_prijave,
										concat(ime_nd_kandidata, ' ', prezime_nd_kandidata) as ime_prezime,
										status_nd_kandidata,
										vrijeme_kreiranja_nd_kandidata
									FROM 
										idk_kandidati 
									JOIN 
										idk_nd_kandidata
									ON
										idk_kandidati.kandidat_dipl_id = idk_nd_kandidata.id_broj_nd_kandidata 
									WHERE 
										kandidat_id = $kandidat_id";
					$stmt = $db->prepare($check_dipl);
					$stmt->execute();
					$check_dipl_result = $stmt->fetch();
					$kandidat_dipl_id = $check_dipl_result['kandidat_dipl_id'];
					$kandidat_status_prijave = $check_dipl_result['kandidat_status_prijave'];
					$kandidat_dipl_ime_prezime = $check_dipl_result['ime_prezime'];
					$kandidat_dipl_status = $check_dipl_result['status_nd_kandidata'];
					$kandidat_datum_ulaska_leada   = date("d.m.Y", strtotime($check_dipl_result["vrijeme_kreiranja_nd_kandidata"]));

					if($check_dipl_result['povezan_na_dipl'] == 0){
						$sql= "SELECT 
									id_broj_nd_kandidata, 
									ime_nd_kandidata, 
									prezime_nd_kandidata,
									status_nd_kandidata,
									pstatus_nd_kandidata,
									razlog_biljeska_nd,
									vrijeme_kreiranja_nd_kandidata 
								FROM 
									idk_nd_kandidata 
								LEFT JOIN
									(
										SELECT
											id_kandidata_biljeska_nd,
											razlog_biljeska_nd
										FROM
											idk_nd_kandidata_biljeske
										WHERE
											razlog_biljeska_nd = 32
									) as biljeska
								ON
									idk_nd_kandidata.id_broj_nd_kandidata = biljeska.id_kandidata_biljeska_nd
								WHERE 
									((ime_nd_kandidata LIKE '%$kandidat_ime%' AND prezime_nd_kandidata LIKE '%$kandidat_prezime%') $mobitel_uslov)
								AND
									id_broj_nd_kandidata 
								NOT IN
									(
										SELECT
											id_broj_nd_kandidata
										FROM
											idk_nd_kandidata
										JOIN idk_kandidati ON idk_nd_kandidata.id_broj_nd_kandidata = idk_kandidati.kandidat_dipl_id
									)
								";
						$search_candidate_in_dipl = $db->prepare($sql);
						$search_candidate_in_dipl->execute();
						$result = $search_candidate_in_dipl->fetchAll();
						if($result > 0){
							echo '<div class="form-group">
									<div class="col-sm-12">
										<div class="alert alert-danger text-center" role="alert">
											<i class="fa fa-exclamation-triangle fa-3x" aria-hidden="true"></i>
											<br>
											<strong>
												Obavezno prvo provjeriti ponuđene kandidate iz pretrage.<br>
												Samo ako ni jedan kandidat ne odgovara za povezivanje onda koristiti dugme "Kopiraj u DIPL"!
											</strong>
										</div>
									</div>
								</div>
								<table id="dipl_table">
									<thead>
										<tr>
											<th class="text-center">Ime i Prezime</th>
											<th class="text-center">Status</th>
											<th class="text-center">Datum ulaska lead-a</th>
											<th></th>
										</tr>
									</thead>
									<tbody>';
							foreach($result as $kandidat){
								$kandidat_id_nd 	  = $kandidat["id_broj_nd_kandidata"];
								$kandidat_ime_nd 	  = $kandidat["ime_nd_kandidata"];
								$kandidat_prezime_nd  = $kandidat["prezime_nd_kandidata"];
								$dipl_status		  = $kandidat["status_nd_kandidata"];
								$dipl_pstatus		  = $kandidat["pstatus_nd_kandidata"];
								$razlog				  = $kandidat["razlog_biljeska_nd"];
								$datum_ulaska_leada   = date("d.m.Y", strtotime($kandidat["vrijeme_kreiranja_nd_kandidata"]));

								if(($dipl_status == 1 && in_array($dipl_pstatus, $approved_pstatuses)) || $dipl_status == 7){
									$povezi_btn =  '<a 
														href = "#" 
														class = "btn material-btn material-btn-icon-success material-btn_success main-container__column povezi_btn" 
														data-toggle = "modal" 
														data-target = "#nostrifikovana_dipl"
														data-povezivanje = 1
														data-dipl_id =' . $kandidat_id_nd . '
													>
														<i class="fa fa-plus" aria-hidden="true"></i>
														Povezi
													</a>';
								} else {
									$povezi_btn =  '<a 
														href="/do.php?form=povezi_dipl&id='.$kandidat_id.'&nd_id='.$kandidat_id_nd.'&dipl_status='.$dipl_status.'" 
														class="btn material-btn material-btn-icon-success material-btn_success main-container__column" 
														style="text-align: right;"
													>
														<i class="fa fa-plus" aria-hidden="true"></i>
														Povezi
													</a>';
								}

								switch($dipl_status){
									case "1":
										$status = "Novi";
									break;
									case "2":
										$status = "Prikupljanje dokumentacije";
									break;
									case "3":
										$status = "Poslana posta";
									break;
									case "4":
										$status = "U obradi";
									break;
									case "5":
										$status = "Dopuna dokumentacije";
									break;
									case "6":
										$status = "Zavrsen";
									break;
									case "7":
										$status = "Arhiviran";
									break;
								}

								echo '<tr>
										<td class="text-center">
											<a href="/nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$kandidat_id_nd.'" target="_BLANK">'.$kandidat_ime_nd . ' ' . $kandidat_prezime_nd .'</a>
										</td>
										<td class="text-center">'
											. $status . 
										'</td>
										<td class="text-center">'
											. $datum_ulaska_leada . 
										'</td>
										<td class="text-center">'
											. $povezi_btn .										
										'</td>
									</tr>';
							}
							echo '	</tbody>
								</table>';
							echo '<div class="row" style="margin-top: 15px;">
									<div class="col-sm-3"></div>
									<div class="col-sm-4">
										<li>
											<a 
												href = "#"
												id = "copy_dipl" 
												class = "btn material-btn material-btn-icon-success material-btn_success main-container__column" 
												data-toggle = "modal" 
												data-target = "#diplModal"
											>
												<i 
													class = "fa fa-plus-square" 
													aria-hidden = "true"
												>
												</i> 
												<span>
													Kopiraj u DIPL
												</span>
											</a>
										</li>
									</div>
									<div class="col-sm-4"></div>
								</div>';
						}
					} else {
						switch($kandidat_dipl_status){
							case "1":
								$status = "Novi";
							break;
							case "2":
								$status = "Prikupljanje dokumentacije";
							break;
							case "3":
								$status = "Poslana posta";
							break;
							case "4":
								$status = "U obradi";
							break;
							case "5":
								$status = "Dopuna dokumentacije";
							break;
							case "6":
								$status = "Zavrsen";
							break;
							case "7":
								$status = "Arhiviran";
							break;
						}
						if(in_array($kandidat_status_prijave, $approved_status_prijave)){
							$odvezivanje_btn = ' 
									<a href="/do.php?form=odvezi_dipl_profil&kandidat_id='.$kandidat_id.'" class="btn material-btn material-btn-icon-danger material-btn_danger main-container__column" title = "Kandidat će se odvezati od profila koji se nalazi u DIPL modulu!" ><i class="fa fa-times" aria-hidden="true">
										</i> <span>Odveži</span>
									</a>
								';
						} else {
							$odvezivanje_btn = '';
						}

						echo '<div class="row">
								<div class="col-sm-2"></div>
								<div class="col-sm-8">
									<div class="alert alert-danger" role="alert">Kandidat povezan sa DIPL-om!</div>
								</div>
								<div class="col-sm-2"></div>
							  </div>
							  <table id="dipl_table">
								<thead>
									<tr>
										<th class="text-center">Ime i Prezime</th>
										<th class="text-center">Status</th>
										<th class="text-center">Datum ulaska lead-a</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="text-center">'.$kandidat_dipl_ime_prezime.'</td>
										<td class="text-center">'.$status.'</td>
										<td class="text-center">'.$kandidat_datum_ulaska_leada.'</td>
										<td class="text-center">'.$odvezivanje_btn.'</td>
									</tr>
								</tbody>
							  </table>
							  <div class="row" style="margin-top: 15px;">
								<div class="col-sm-3"></div>
								<div class="col-sm-8">
									<a href="/nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$kandidat_dipl_id.'" target="_BLANK" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column" title = "Kandidat se već nalazi u DIPL modulu!" ><i class="fa fa-chevron-right" aria-hidden="true">
										</i> <span>Otvori profil u DIPL-u</span>
									</a>
								</div>
							  </div>
						';
					}
					
				break;

				case "check_certificate":
					$kandidat_id = $_POST["kandidat_id"];

					$sql = "SELECT 
								cvl_id  
							FROM 
								idk_candidate_verified_languages 
							JOIN 
								idk_kandidat_jezici 
							ON 
								idk_candidate_verified_languages.cvl_id = idk_kandidat_jezici.kj_id
							WHERE
								kj_kandidatid = $kandidat_id
							AND
								cvl_certificate_expiration_date > NOW()
							AND
								cvl_active = 1";
					$stmt = $db->prepare($sql);
					$stmt->execute();
					$result = $stmt->fetch();
					if($result != NULL)
					{
						echo 1;
					}
				break;
				case "candidate_assessment":
					$candidate_id = $_POST["candidate_id"];

					include_once("jobstep_pp/includes/classes/candidatesProjection.php");
					$durationPerStatus = new durationPerStatus();

					$approved_statuses = [3, 7, 8, 9, 12, 15, 18, 21, 24, 27];
					$status_id 	       = getCandidateStatusPrijave($candidate_id);

					if(in_array($status_id, $approved_statuses))
					{
						$candidatesProjection = new candidatesProjection($durationPerStatus, $candidate_id);
						$candidate_date = $candidatesProjection -> getCandidateProjectionRows();
						$datum = date("d.m.Y", $candidate_date[0]["candidate_assessment"]);
						echo '
							<strong class="col-sm-4 text-right" style="margin-top: 7px;">Proračunati početak rada:</strong>
							<div class="col-sm-4" style="margin-top: 7px;">
								<span class="label label-info material-label material-label_info main-container__column">'.$datum.'</span>
							</div>
							';
													
					}
				break;

				case "get_contract_sent_details":
					$kandidat_id = $_GET["cid"];
					$nalog_id = $_GET["nid"];

					$sql = "SELECT id_cs, cs_tracking_code, cs_tracking_link, cs_sent_date FROM idk_pp_contract_sent WHERE cs_kandidat_id = $kandidat_id AND cs_nalog_id = $nalog_id";
					
					$stmt = $db->prepare($sql);
					$stmt->execute();

					$result = $stmt->fetch();

					echo json_encode([
						"id_cs" => $result["id_cs"],
						"tracking_code" => $result["cs_tracking_code"],
						"tracking_link" => $result["cs_tracking_link"],
						"date" => $result["cs_sent_date"]
 					]);
				break;

				case "check_contract_sent":

					if (checkContractSentR($_POST['kandidat_id']) == true) echo "true";
					else echo "false";

				break;

				case "insert_task":
					$tf_candidate_id 		= $_POST["tf_candidate_id"];
					$tf_nalog_id 			= $_POST["tf_nalog_id"];
					$tf_vrsta_id 			= $_POST["tf_vrsta_id"];
					$tf_project_id 			= $_POST["tf_projekt_id"];
					$tf_status_id 			= intval($_POST["tf_status_id"]);
					$tf_note_attachment 	= intval($_POST['tf_note_attachment']);
					$tf_category	 		= getTFCategoryFromVrstaId($tf_vrsta_id);
					$tf_dipl_id				= getDiplIdFromCandidate($tf_candidate_id);
					$file_names_s = NULL;
					$files_log_desc = "";
					
					if($tf_note_attachment == 1){
						echo "uslo u ima attachment<br/>";
						$allowed = array('jpg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png', 'csv');
						$file_names = array();
						
						$tf_nr_of_files = $_POST["tf_nr_of_files"];
						for($i = 0; $i < $tf_nr_of_files; $i++) {
							echo "uslo u petlju " . $i . " put<br/>";
							$tmpFilePath = $_FILES["file-$i"]['tmp_name'];
							
							if ($tmpFilePath != ""){
								echo "uslo u != prazno filepath<br/>";
								$filename = $_FILES["file-$i"]['name'];
								
								$file_ext = explode('.', $filename);
								$file_ext = strtolower(end($file_ext));
								if(in_array($file_ext, $allowed)) {
									$file_name_new = 'tfnote_' . uniqid() . '.' . $file_ext;
									if($tf_category == 3){
										$newFilePath = "files/prilozi_dipl/" . $file_name_new;
									}else{
										$newFilePath = "files/prilozi_kandidati/" . $file_name_new;
									}
									
									if(move_uploaded_file($tmpFilePath, $newFilePath)) {
										$file_names[] = $file_name_new;
									}
								}
							}
						}
						$file_names_s = implode(",", $file_names);
						$files_log_desc = ". Files: [".$file_names_s."]";
					}

					if(!empty($_POST["tf_call_appointment"])){
						$tf_call_appointment =  date("Y-m-d H:i:ss", strtotime($_POST["tf_call_appointment"]));
					}else{
						$tf_call_appointment = NULL;
					}
					
					$intervju 				= $_POST["intervju"] ?? null;
					$intervju_time 			= date("H:i", strtotime(getTimeForAppointment($intervju)));
					

					$appointment_id 		= $_POST["termin_group"] ?? null;
					if($appointment_id == null){
						
						$active_appt = intval(getActiveAppointmentForCandidate($tf_candidate_id));
						if($active_appt == null){
							$tf_casting_id = 0;
						}else{
							$tf_casting_id = intval(getAppointmentGroup($active_appt));
						}
						
					}else{
						$tf_casting_id = intval(getAppointmentGroup($appointment_id));
					}
					
					$tf_note 				= $_POST["tf_note"];
					$tf_important_note 		= $_POST["tf_important_note"];
					if($tf_important_note == "true"){
						$tf_important_note_value = 1;
					}else{
						$tf_important_note_value = 0;
					}
					$tf_status_prijave 		= getTFStatusPrijave($tf_status_id);
					$tfs_is_positive		= isTaskStatusRemovingFromTaskForce($tf_status_id);
					$tfs_has_interview		= hasTaskForceInterview($tf_status_id);
					$kand_status_prijave 	= getCandidateStatusPrijave($tf_candidate_id);
					$tf_brojac_neuspjela_komunikacija = NULL;

					if($tfs_has_interview == 1){
						addAppointmentForCandidate($tf_candidate_id, $appointment_id, $intervju_time, $intervju);
					}
					
					if($tfs_is_positive == 0){
						$tf_last_active_task = 0;
						updateLastActiveTaskForCandidate($tf_candidate_id, $tf_vrsta_id);
						// Ako se radi o castingu
						if($tf_vrsta_id == 1){
							updateTaskForceStatusForCandidate($tf_candidate_id, NULL);
						}
					}else{
						if($tf_status_id == 2){
							// pokupi broj neuspjelih komunikacija za zadnji TF status (mora biti 2 - to je u funkciji)
							$brojac_neuspjesnih_komunikacija = brojacNeuspjelaKomunikacija(getLastTFId($tf_candidate_id, $tf_nalog_id, $tf_vrsta_id));
							if($brojac_neuspjesnih_komunikacija > 9){
								// obzirom da je 10 puta prozvan treba da se desi isto kao i kod onih koji imaju tf_status_prijave 1 (Ne ispunjava uslove za nalog)
								// treba povuci projekte za taj nalog i obrisati iz tabele kandidati projekti sve gdje su ti projekti i ti kandidati
								// $project = $tf_nalog_id
								$project_list = getProjectListForNalog($tf_nalog_id);

								$delete_project_cand = $db->prepare("
													DELETE FROM idk_project_kandidati
													WHERE pk_projectid IN ($project_list) AND pk_kandidatid = $tf_candidate_id");

								$delete_project_cand->execute();

								updateKandidatStatusPrijave($tf_candidate_id, 1);
								addToLogsStatusPrijave(NULL, NULL, 1, $tf_candidate_id, 1);
								
								$tf_last_active_task = 0;
								updateLastActiveTaskForCandidate($tf_candidate_id, $tf_vrsta_id);
								updateTaskForceStatusForCandidate($tf_candidate_id, NULL);
							}else{
								$tf_last_active_task = 1;
								// increment neuspjele komunikacije, suprotno ce biti NULL
								$tf_brojac_neuspjela_komunikacija = $brojac_neuspjesnih_komunikacija + 1;
								updateLastActiveTaskForCandidate($tf_candidate_id, $tf_vrsta_id);
								updateTaskForceStatusForCandidate($tf_candidate_id, $tf_status_id);
							}
						}else{
							$tf_last_active_task = 1;
							updateLastActiveTaskForCandidate($tf_candidate_id, $tf_vrsta_id);
							$inbound_statuses = [24, 43, 44, 45, 46, 47, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 88, 89, 90, 91];
							if (!in_array($tf_status_id, $inbound_statuses)) {
								// Ako se radi o castingu
								if($tf_vrsta_id == 1){
									updateTaskForceStatusForCandidate($tf_candidate_id, $tf_status_id);
								}
							}
						}
					}
				
					$query = $db->prepare("
								INSERT INTO idk_task_force
									(tf_candidate_id, tf_agent_id, tf_nalog_id, tf_project_id, tf_casting_id, tf_status_id, tf_call_appointment, tf_note, tf_important_note, tf_last_active_task, tf_files, tf_brojac_neuspjela_komunikacija, tf_vrsta_id)
								VALUES
									(:tf_candidate_id, :tf_agent_id, :tf_nalog_id, :tf_project_id, :tf_casting_id, :tf_status_id, :tf_call_appointment, :tf_note, :tf_important_note, :tf_last_active_task, :tf_files, :tf_brojac_neuspjela_komunikacija, :tf_vrsta_id)");

					$query->execute(array(
									':tf_candidate_id' => $tf_candidate_id,
									':tf_agent_id' => $logged_employee_id,
									':tf_nalog_id' => $tf_nalog_id,
									':tf_project_id' => $tf_project_id,
									':tf_casting_id' => $tf_casting_id,
									':tf_vrsta_id' => $tf_vrsta_id,
									':tf_status_id' => $tf_status_id,
									':tf_call_appointment' => $tf_call_appointment,
									':tf_note' => $tf_note,
									':tf_important_note' => $tf_important_note_value,
									':tf_last_active_task' => $tf_last_active_task,
									':tf_files' => $file_names_s,
									':tf_brojac_neuspjela_komunikacija' => $tf_brojac_neuspjela_komunikacija));

					$izvor = 1; // CRM

					if($tf_status_prijave != NULL){
						if($tf_status_prijave == 1){
							// treba povuci projekte za taj nalog i obrisati iz tabele kandidati projekti sve gdje su ti projekti i ti kandidati
							// $project = $tf_nalog_id
							$nezainteresiran_projekt_id = getProjectIDForNalogByName("Nije zainteresiran", $tf_nalog_id);
							$project_list = getProjectListForNalog($tf_nalog_id);

							$delete_project_cand = $db->prepare("
													DELETE FROM idk_project_kandidati
													WHERE pk_projectid IN ($project_list) AND pk_kandidatid = $tf_candidate_id");

							$delete_project_cand->execute();

							$insert_into_project_kandidati = $db->prepare("
								INSERT INTO idk_project_kandidati
									(pk_projectid, pk_kandidatid)
								VALUES
									(:pk_projectid, :pk_kandidatid)
							");

							$insert_into_project_kandidati->execute(array(
								':pk_projectid' => $nezainteresiran_projekt_id,
								':pk_kandidatid' => $tf_candidate_id
							));

							updateKandidatStatusPrijave($tf_candidate_id, $tf_status_prijave);
							addToLogsStatusPrijave(NULL, $nezainteresiran_projekt_id, $tf_status_prijave, $tf_candidate_id, $izvor);

							//Ako je kandidat rezervisan za nekog agenta onda pri unosu negativnog statusa treba odrezervisati kandidata i agenta
							$agent_id_for_unreservation = disconnectAgentFromCandidate($tf_candidate_id);

							//Ako je kandidat bio na statusu Pristao ili Dolazi potrebno je prebaciti tf stat na Odustao
							$active_tsr_id = checkTFstatsPristaoDolaziForCandidate($tf_candidate_id, $tf_casting_id);
							if($active_tsr_id != null){
								updateTFStat($active_tsr_id, 3);
							}
							
							/*if($agent_id_for_unreservation != null){
								//treba unijeti u tabelu tf stats status "odustao" ako je kandidat u castingu i da nije vec u tf stats kao "nije dosao"
								if($kand_status_prijave == 3 AND (checkTFstatsForCandidate($tf_candidate_id, $tf_casting_id) == null)){
									insertTFStat($tf_candidate_id, $agent_id_for_unreservation, $tf_casting_id, $tf_nalog_id, 3);
								}
							}*/
							

						}elseif($tf_status_prijave == 3){
							if($kand_status_prijave != 3){
								// treba prebaciti kandidata na projekt kasting
								$projekt_casting_id = getCastingProjectForNalog($tf_nalog_id);
								$project_list = getProjectListForNalog($tf_nalog_id);

								$delete_project_cand = $db->prepare("
														DELETE FROM idk_project_kandidati
														WHERE pk_projectid IN ($project_list) AND pk_kandidatid = $tf_candidate_id");

								$delete_project_cand->execute();

								$query = $db->prepare("
											INSERT INTO idk_project_kandidati
												(pk_projectid, pk_kandidatid)
											VALUES
												(:pk_projectid, :pk_kandidatid)");

								$query->execute(array(
												':pk_projectid' => $projekt_casting_id,
												':pk_kandidatid' => $tf_candidate_id));

								updateKandidatStatusPrijave($tf_candidate_id, $tf_status_prijave);
								addToLogsStatusPrijave(NULL, $projekt_casting_id, $tf_status_prijave, $tf_candidate_id, $izvor);
							}

							//Da li je kandidat već bio u ovom castingu i na statusu nije dosao
							//ako jeste treba ga ažurirati na Pristao, ako nije onda ide insert
							$tsr_id = checkTFstatsForCandidate($tf_candidate_id, $tf_casting_id);
							if($tsr_id != null){
								updateTFStat($tsr_id, 4);
								// updateTFStatWithAgent($tsr_id, 4, $logged_employee_id);
							}else{
								insertTFStat($tf_candidate_id, $logged_employee_id, $tf_casting_id, $tf_nalog_id, 4);
							}
						}
					}

					if($tf_important_note == "true"){
						if($tf_category == 3){
							$dodavanje_biljeske_nd = $db->prepare("
								INSERT INTO idk_nd_kandidata_biljeske
									(id_kandidata_biljeska_nd, status_biljeska_nd, sadrzaj_biljeska_nd, vrijeme_dodavanja_biljeska_nd, vrijeme_grupa_biljeska_nd, dodao_zaposlenik_biljeska_nd, prilog_biljeska_nd)
								VALUES
									(:id_kandidata_biljeska_nd, :status_biljeska_nd, :sadrzaj_biljeska_nd, :vrijeme_dodavanja_biljeska_nd, :vrijeme_grupa_biljeska_nd, :dodao_zaposlenik_biljeska_nd, :prilog_biljeska_nd)
							");

							$dodavanje_biljeske_nd->execute(array(
								':id_kandidata_biljeska_nd' => $tf_dipl_id,
								':status_biljeska_nd' => 4,
								':sadrzaj_biljeska_nd' => $tf_note,
								':vrijeme_dodavanja_biljeska_nd' => date("Y-m-d H:i:s"),
								':vrijeme_grupa_biljeska_nd' => date("Y"),
								':dodao_zaposlenik_biljeska_nd' => $logged_employee_id,
								':prilog_biljeska_nd' => $file_names_s
							));

							$log_desc = "Dodao novu bilješku: " .$tf_note. " za kandidata: " .getCandidateFullnameR($tf_candidate_id).".";

							addToLogs($log_desc,3);
						}else{
							$query = $db->prepare("
								INSERT INTO idk_notes
									(note_txt, note_datetime, note_group, note_dataid, note_files, note_employeeid)
								VALUES
									(:note_txt, :note_datetime, :note_group, :note_dataid, :note_files, :note_employeeid)");

							$query->execute(array(
										':note_txt' => $tf_note,
										':note_datetime' => date('Y-m-d H:i:s'),
										':note_group' => 2,
										':note_dataid' => $tf_candidate_id,
										':note_files' => $file_names_s,
										':note_employeeid' => $logged_employee_id));
							
							$log_desc = "Dodao novu bilješku: " .$tf_note. " za kandidata: " .getCandidateFullnameR($tf_candidate_id).".";

							addToLogs($log_desc,3);
						}
					}

					// Rezervacija agenta za kandidata pri određenim statusima
					if($tf_status_id == 12 OR $tf_status_id == 14 OR $tf_status_id == 16 OR $tf_status_id == 18){
						$reserved_agent_id = getFullReservedAgentFromCandidate($tf_candidate_id);
						if($reserved_agent_id == null){
							connectAgentToCandidate($tf_candidate_id, $logged_employee_id);
						}
						//Update tf stats na Dolazi
						if($tf_status_id == 18){
							$tsr_id = checkTFstatsForCandidate($tf_candidate_id, $tf_casting_id);
							if($tsr_id != null){
								updateTFStat($tsr_id, 5);
								// updateTFStatWithAgent($tsr_id, 5, $logged_employee_id);
							}
						}
					}

					// Unos stats kod unosa statusa Nije dosao
					if($tf_status_id == 22){
						$last_agent_id = getFullReservedAgentFromCandidate($tf_candidate_id);
						// if($last_agent_id != null)
						// 	insertTFStat($tf_candidate_id, $last_agent_id, $tf_casting_id, $tf_nalog_id, 2);

						//Ako je kandidat bio na statusu Pristao ili Dolazi potrebno je prebaciti tf stat na Nije Dosao
						$active_tsr_id = checkTFstatsPristaoDolaziForCandidate($tf_candidate_id, $tf_casting_id);
						if($active_tsr_id != null){
							updateTFStat($active_tsr_id, 2);
						}
						
					}

					// Odustao kod posredovanja

					if($tf_status_id == 30 OR $tf_status_id == 34 OR $tf_status_id == 38 OR $tf_status_id == 42){
						$novi_status_id = 1;
						$razlog_opis	= null;
						$stari_projekt  = null;
						$izvor = 1;
						$tf_razlog_odustajanja = $_POST["tf_razlog_odustajanja"];

						insert_reject_reason($tf_nalog_id, $kand_status_prijave, $novi_status_id, $tf_candidate_id, $tf_razlog_odustajanja, $razlog_opis, $stari_projekt, $izvor);
					}

					// Posebne akcije za jezike

					// Prelazi na veci nivo
					if($tf_status_id == 108 OR $tf_status_id == 113 OR $tf_status_id == 118){
						$kj_naziv = "Njemački";
						$kj_slusanje = $_POST["prelazak_na_veci_nivo"];
						$prelazak_na_veci_nivo_podnivo = $_POST["prelazak_na_veci_nivo_podnivo"];	
						$language_ustanova = $_POST["language_ustanova"];	
						$prelazak_na_veci_nivo_datum_pocetka = $_POST["prelazak_na_veci_nivo_datum_pocetka"];	
						$prelazak_na_veci_nivo_datum_kraja = $_POST["prelazak_na_veci_nivo_datum_kraja"];
						if($prelazak_na_veci_nivo_podnivo == 3){
							$datum_pocetka_podnivo_1 = $prelazak_na_veci_nivo_datum_pocetka;
							$kraj_podnivo_1 = $prelazak_na_veci_nivo_datum_kraja;
							$datum_pocetka_podnivo_2 = null;
							$kraj_podnivo_2 = null;
						}else if($prelazak_na_veci_nivo_podnivo == 4){
							$datum_pocetka_podnivo_1 = null;
							$kraj_podnivo_1 = null;
							$datum_pocetka_podnivo_2 = $prelazak_na_veci_nivo_datum_pocetka;
							$kraj_podnivo_2 = $prelazak_na_veci_nivo_datum_kraja;
						}
						
						$log_date=date('Y-m-d H:i:s');

						// Provjera da li se unosi isti nivo jezika
						if(getActiveLanguage($tf_candidate_id) != $kj_slusanje){

							$query = $db->prepare("
											INSERT INTO idk_kandidat_jezici
												(kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid,kj_ustanova)
											VALUES
												(:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid, :kj_ustanova)");

							$query->execute(array(
										':kj_naziv' => $kj_naziv,
										':kj_slusanje' => $kj_slusanje,
										':kj_citanje' => $kj_slusanje,
										':kj_govorna_interakcija' => $kj_slusanje,
										':kj_govorna_produkcija' => $kj_slusanje,
										':kj_pisanje' => $kj_slusanje,
										':kj_kandidatid' => $tf_candidate_id,
										':kj_ustanova' => $language_ustanova));

							$new_lang_id = $db->lastInsertId();
							
							$query_inactive= $db->prepare("
								UPDATE idk_candidate_verified_languages join idk_kandidat_jezici on idk_candidate_verified_languages.cvl_id=idk_kandidat_jezici.kj_id 
								SET idk_candidate_verified_languages.cvl_active=0 
								WHERE idk_kandidat_jezici.kj_kandidatid=:kj_kandidatid AND idk_candidate_verified_languages.cvl_active=1 AND kj_naziv LIKE '%Njemacki%'");

							$query_inactive->execute(array(
								':kj_kandidatid'=>$tf_candidate_id
							));

							$query_new_insert =$db->prepare("
									INSERT INTO idk_candidate_verified_languages 
										(cvl_id,cvl_status,cvl_course1_started,cvl_course1_ended,cvl_course2_started,cvl_course2_ended,cvl_last_updated,cvl_active) 
									VALUES 
										(:cvl_id,:odabir_status_jezik,:cvl_course1_started,:cvl_course1_ended,:cvl_course2_started,:cvl_course2_ended,:log_date, :cvl_active)
							");

							$query_new_insert->execute(array(
								':cvl_id' 					=> $new_lang_id,
								':odabir_status_jezik' 		=> $prelazak_na_veci_nivo_podnivo,
								':cvl_course1_started' 		=> $datum_pocetka_podnivo_1,
								':cvl_course1_ended'		=> $kraj_podnivo_1,
								':cvl_course2_started' 		=> $datum_pocetka_podnivo_2,
								':cvl_course2_ended'		=> $kraj_podnivo_2,
								':log_date'=>$log_date,
								':cvl_active'=>1
							));
							
						}else{
							$new_lang_id = getActiveLanguageID($tf_candidate_id);
							if($prelazak_na_veci_nivo_podnivo == 3){
								$query_new_update =$db->prepare("
									UPDATE idk_candidate_verified_languages 
									SET cvl_status = :odabir_status_jezik, cvl_last_updated = :log_date, cvl_course1_started = :cvl_course1_started, cvl_course1_ended = :cvl_course1_ended
									WHERE cvl_id = :cvl_id AND cvl_active = :cvl_active
								");

								$query_new_update->execute(array(
									':cvl_id' => $new_lang_id,
									':odabir_status_jezik' => $prelazak_na_veci_nivo_podnivo,
									':cvl_course1_started' => $datum_pocetka_podnivo_1,
									':cvl_course1_ended' => $kraj_podnivo_1,
									':log_date'=>$log_date,
									':cvl_active'=>1
								));

							}else if($prelazak_na_veci_nivo_podnivo == 4){
								$query_new_update =$db->prepare("
									UPDATE idk_candidate_verified_languages 
									SET cvl_status = :odabir_status_jezik, cvl_last_updated = :log_date, cvl_course2_started= :cvl_course2_started,cvl_course2_ended = :cvl_course2_ended
									WHERE cvl_id = :cvl_id AND cvl_active = :cvl_active
								");

								$query_new_update->execute(array(
									':cvl_id' => $new_lang_id,
									':odabir_status_jezik' => $prelazak_na_veci_nivo_podnivo,
									':cvl_course2_started' => $datum_pocetka_podnivo_2,
									':cvl_course2_ended' => $kraj_podnivo_2,
									':log_date'=>$log_date,
									':cvl_active'=>1
								));
							}
						}

						logDateDifference($new_lang_id);

						insertCandidateLanguageLogs($tf_candidate_id, $new_lang_id, $prelazak_na_veci_nivo_podnivo);

						try{
							$result=prebaciNaPrikupljanjeDokumentacije($kj_kandidatid,1);  // 1 - CRM
						}catch (Exception $e){
							$result=$e->getMessage();
						}
						
						$kandidat_ime_prezime = getCandidateFullnameR($kj_kandidatid);
						$log_desc = "Uredio nivo jezika kandidatu: " . $kandidat_ime_prezime . " Status automatskog prebacivanja na prikupljanje dokumentacije: ".$result."";
						
						$log_type=11;
						addToLogs($log_desc, $log_type);

						updateCandidateProjectionAndInstallment($tf_candidate_id);
					}

					// Ceka datum polaganja
					if($tf_status_id == 123){
						$odabir_status_jezik = 5; // Ceka datum polaganja
						$kj_id = getActiveLanguageID($tf_candidate_id);
						$log_date=date('Y-m-d H:i:s');

						$query_new_update =$db->prepare("
								UPDATE idk_candidate_verified_languages 
								SET cvl_status = :odabir_status_jezik, cvl_last_updated = :log_date
								WHERE cvl_id = :cvl_id AND cvl_active = :cvl_active
						");

						$query_new_update->execute(array(
							':cvl_id' 					=> $kj_id,
							':odabir_status_jezik' 		=> $odabir_status_jezik,
							':log_date'=>$log_date,
							':cvl_active'=>1
						));
						
						logDateDifference($kj_id);

						insertCandidateLanguageLogs($tf_candidate_id, $kj_id, $odabir_status_jezik);

						try{
							$result=prebaciNaPrikupljanjeDokumentacije($kj_kandidatid,1);  // 1 - CRM
						}catch (Exception $e){
							$result=$e->getMessage();
						}						
						
						$kandidat_ime_prezime = getCandidateFullnameR($kj_kandidatid);
						$log_desc = "Uredio nivo jezika kandidatu: " . $kandidat_ime_prezime . " Status automatskog prebacivanja na prikupljanje dokumentacije: ".$result."";
						
						$log_type=11;
						addToLogs($log_desc, $log_type);

						updateCandidateProjectionAndInstallment($tf_candidate_id);
					}

					// Zakazao polaganje
					if($tf_status_id == 124 OR $tf_status_id == 130 OR $tf_status_id == 135){
						$taskforce_language_exam_date = $_POST["taskforce_language_exam_date"];
						$odabir_status_jezik = 6; // Ceka polaganje (ima termin)
						$kj_id = getActiveLanguageID($tf_candidate_id);
						$log_date=date('Y-m-d H:i:s');

						$query_new_update =$db->prepare("
								UPDATE idk_candidate_verified_languages 
								SET cvl_status = :odabir_status_jezik, cvl_last_updated = :log_date,cvl_exam_date = :cvl_exam_date
								WHERE cvl_id = :cvl_id AND cvl_active = :cvl_active
						");

						$query_new_update->execute(array(
							':cvl_id' 					=> $kj_id,
							':odabir_status_jezik' 		=> $odabir_status_jezik,
							':log_date'=>$log_date,
							':cvl_exam_date'=> $taskforce_language_exam_date,
							':cvl_active'=>1
						));
						
						logDateDifference($kj_id);

						insertCandidateLanguageLogs($tf_candidate_id, $kj_id, $odabir_status_jezik);

						try{
							$result=prebaciNaPrikupljanjeDokumentacije($kj_kandidatid,1);  // 1 - CRM
						}catch (Exception $e){
							$result=$e->getMessage();
						}	
						
						$kandidat_ime_prezime = getCandidateFullnameR($kj_kandidatid);
						$log_desc = "Uredio nivo jezika kandidatu: " . $kandidat_ime_prezime . " Status automatskog prebacivanja na prikupljanje dokumentacije: ".$result."";
						
						$log_type=11;
						addToLogs($log_desc, $log_type);

						updateCandidateProjectionAndInstallment($tf_candidate_id);
					}

					// Ceka rezultat
					if($tf_status_id == 128){
						$kj_id = getActiveLanguageID($tf_candidate_id);
						$log_date=date('Y-m-d H:i:s');
						$odabir_status_jezik = 7; // Ceka rezultat

						$query_new_update =$db->prepare("
								UPDATE idk_candidate_verified_languages 
								SET cvl_status = :odabir_status_jezik,cvl_last_updated = :log_date
								WHERE cvl_id = :cvl_id AND cvl_active = :cvl_active
						");

						$query_new_update->execute(array(
							':cvl_id' 					=> $kj_id,
							':odabir_status_jezik' 		=> $odabir_status_jezik,
							':log_date'=>$log_date,
							':cvl_active'=>1
						));

						logDateDifference($kj_id);

						insertCandidateLanguageLogs($tf_candidate_id, $kj_id, $odabir_status_jezik);

						try{
							$result=prebaciNaPrikupljanjeDokumentacije($kj_kandidatid,1);  // 1 - CRM
						}catch (Exception $e){
							$result=$e->getMessage();
						}

						$kandidat_ime_prezime = getCandidateFullnameR($kj_kandidatid);
						$log_desc = "Uredio nivo jezika kandidatu: " . $kandidat_ime_prezime . " Status automatskog prebacivanja na prikupljanje dokumentacije: ".$result."";
						
						$log_type=11;
						addToLogs($log_desc, $log_type);

						updateCandidateProjectionAndInstallment($tf_candidate_id);
					}
					
					// Polozio
					if($tf_status_id == 132){
						$kj_id = getActiveLanguageID($tf_candidate_id);
						$log_date=date('Y-m-d H:i:s');
						$timeOfUpload=date('Y-m-d H:i:s');
						$odabir_status_jezik = 8; // Ima certifikat

						$datum_k_certifikata=$_POST['taskforce_language_certificate_creation'];
						$datum_i_certifikata=$_POST['taskforce_language_certificate_expiration'];

						$allowed = array('jpg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png', 'csv');
						$file_names = array();
						
						$tf_nr_of_certificates = $_POST["tf_nr_of_certificates"];
						for($i = 0; $i < $tf_nr_of_certificates; $i++) {
							echo "uslo u petlju " . $i . " put<br/>";
							$tmpFilePath = $_FILES["file_certificate-$i"]['tmp_name'];
							
							if ($tmpFilePath != ""){
								echo "uslo u != prazno filepath<br/>";
								$filename = $_FILES["file_certificate-$i"]['name'];
								$file_ext = explode('.', $filename);
								$file_ext = strtolower(end($file_ext));
								if(in_array($file_ext, $allowed)) {
									$file_name_new = '' . uniqid() . '.' . $file_ext;
									$file_name_new_save = 'jobstep_pp/files/candidate_documents/' . $file_name_new;
									$newFilePath = "jobstep_pp/files/candidate_documents/" . $file_name_new;
									
									if(move_uploaded_file($tmpFilePath, $newFilePath)) {
										$file_names[] = $file_name_new_save;
									}
								}
							}
						}
						$file_names_s = implode(",", $file_names);

						$query_new_update =$db->prepare("
								UPDATE idk_candidate_verified_languages 
								SET cvl_status = :odabir_status_jezik,cvl_last_updated = :log_date, cvl_certificate_upload_date=:cvl_certificate_upload_date, cvl_certificate_path = :naziv_dokument_jezik_new, cvl_certficate_creation_date = :cvl_certficate_creation_date, cvl_certificate_expiration_date = :cvl_certificate_expiration_date
								WHERE cvl_id = :cvl_id AND cvl_active = :cvl_active
						");

						$query_new_update->execute(array(
							':cvl_id' 					=> $kj_id,
							':odabir_status_jezik' 		=> $odabir_status_jezik,
							':naziv_dokument_jezik_new' =>$file_names_s,
							':cvl_certificate_upload_date' =>$timeOfUpload,
							':cvl_certficate_creation_date'=>$datum_k_certifikata,
							':cvl_certificate_expiration_date'=>$datum_i_certifikata,
							':log_date'=>$log_date,
							':cvl_active'=>1
						));

						logDateDifference($kj_id);

						insertCandidateLanguageLogs($tf_candidate_id, $kj_id, $odabir_status_jezik);

						try{
							$result=prebaciNaPrikupljanjeDokumentacije($kj_kandidatid,1);  // 1 - CRM
						}catch (Exception $e){
							$result=$e->getMessage();
						}						
						
						$kandidat_ime_prezime = getCandidateFullnameR($kj_kandidatid);
						$log_desc = "Uredio nivo jezika kandidatu: " . $kandidat_ime_prezime . " Status automatskog prebacivanja na prikupljanje dokumentacije: ".$result."";
						
						$log_type=11;
						addToLogs($log_desc, $log_type);
						updateCandidateProjectionAndInstallment($tf_candidate_id);
					}

					// Pogresan broj
					if($tf_status_id == 147){
						updateCandidateWrongNumber($tf_candidate_id, 1);
						$log_desc = "Označio pogrešan broj za kandidata: " .getCandidateFullnameR($tf_candidate_id).".";
						addToLogs($log_desc,0);

						$pogresan_broj_projekt_id = getProjectIDForNalogByName("Pogrešan broj", $tf_nalog_id);
						$pogresan_broj_status_prijave = getStatusPrijaveByProjectId($pogresan_broj_projekt_id);
						$project_list = getProjectListForNalog($tf_nalog_id);

						$delete_project_cand = $db->prepare("
												DELETE FROM idk_project_kandidati
												WHERE pk_projectid IN ($project_list) AND pk_kandidatid = $tf_candidate_id");

						$delete_project_cand->execute();

						$insert_into_project_kandidati = $db->prepare("
							INSERT INTO idk_project_kandidati
								(pk_projectid, pk_kandidatid)
							VALUES
								(:pk_projectid, :pk_kandidatid)
						");

						$insert_into_project_kandidati->execute(array(
							':pk_projectid' => $pogresan_broj_projekt_id,
							':pk_kandidatid' => $tf_candidate_id
						));

						// Status prijave za projekt Pogrešan broj je U projektu NR
						$izvor = 1;
						updateKandidatStatusPrijave($tf_candidate_id, $pogresan_broj_status_prijave);
						addToLogsStatusPrijave(NULL, $pogresan_broj_projekt_id, $pogresan_broj_status_prijave, $tf_candidate_id, $izvor);

						//Ako je kandidat rezervisan za nekog agenta onda pri unosu negativnog statusa treba odrezervisati kandidata i agenta
						$agent_id_for_unreservation = disconnectAgentFromCandidate($tf_candidate_id);
					}

					// Nedostupan
					if($tf_status_id == 148){
						addUnavailableLog($tf_candidate_id, $tf_nalog_id);

						$nedostupan_projekt_id = getProjectIDForNalogByName("Nedostupan", $tf_nalog_id);
						$nedostupan_status_prijave = getStatusPrijaveByProjectId($nedostupan_projekt_id);
						$project_list = getProjectListForNalog($tf_nalog_id);

						$delete_project_cand = $db->prepare("
												DELETE FROM idk_project_kandidati
												WHERE pk_projectid IN ($project_list) AND pk_kandidatid = $tf_candidate_id");

						$delete_project_cand->execute();

						$insert_into_project_kandidati = $db->prepare("
							INSERT INTO idk_project_kandidati
								(pk_projectid, pk_kandidatid)
							VALUES
								(:pk_projectid, :pk_kandidatid)
						");

						$insert_into_project_kandidati->execute(array(
							':pk_projectid' => $nedostupan_projekt_id,
							':pk_kandidatid' => $tf_candidate_id
						));

						// Status prijave za projekt Nedostupan je U projektu NR
						$izvor = 1;
						updateKandidatStatusPrijave($tf_candidate_id, $nedostupan_status_prijave);
						addToLogsStatusPrijave(NULL, $nedostupan_projekt_id, $nedostupan_status_prijave, $tf_candidate_id, $izvor);

						//Ako je kandidat rezervisan za nekog agenta onda pri unosu negativnog statusa treba odrezervisati kandidata i agenta
						$agent_id_for_unreservation = disconnectAgentFromCandidate($tf_candidate_id);

						$log_desc = "Označio Nedostupan za kandidata: " .getCandidateFullnameR($tf_candidate_id)." za nalog $tf_nalog_id.";
						addToLogs($log_desc,0);
					}

					// Ne ispunjava uslove za nalog
					if($tf_status_id == 4){
						$neispunjava_uslove_projekt_id = getProjectIDForNalogByName("Ne ispunjava uslove za nalog", $tf_nalog_id);
						$neispunjava_uslove_status_prijave = 1;
						$project_list = getProjectListForNalog($tf_nalog_id);

						$delete_project_cand = $db->prepare("
												DELETE FROM idk_project_kandidati
												WHERE pk_projectid IN ($project_list) AND pk_kandidatid = $tf_candidate_id");

						$delete_project_cand->execute();

						$insert_into_project_kandidati = $db->prepare("
							INSERT INTO idk_project_kandidati
								(pk_projectid, pk_kandidatid)
							VALUES
								(:pk_projectid, :pk_kandidatid)
						");

						$insert_into_project_kandidati->execute(array(
							':pk_projectid' => $neispunjava_uslove_projekt_id,
							':pk_kandidatid' => $tf_candidate_id
						));

						// Status prijave za projekt Ne ispunjava uslove za nalog je U projektu NR
						$izvor = 1;
						updateKandidatStatusPrijave($tf_candidate_id, $neispunjava_uslove_status_prijave);
						addToLogsStatusPrijave(NULL, $neispunjava_uslove_projekt_id, $neispunjava_uslove_status_prijave, $tf_candidate_id, $izvor);

						//Ako je kandidat rezervisan za nekog agenta onda pri unosu negativnog statusa treba odrezervisati kandidata i agenta
						$agent_id_for_unreservation = disconnectAgentFromCandidate($tf_candidate_id);

						//Ako je kandidat bio na statusu Pristao ili Dolazi potrebno je prebaciti tf stat na Odustao
						$active_tsr_id = checkTFstatsPristaoDolaziForCandidate($tf_candidate_id, $tf_casting_id);
						if($active_tsr_id != null){
							updateTFStat($active_tsr_id, 3);
						}
						
						$log_desc = "Označio Ne ispunjava uslove za kandidata: " .getCandidateFullnameR($tf_candidate_id)." za nalog $tf_nalog_id.";
						addToLogs($log_desc,0);
					}

					unreserveAgent($logged_employee_id);
					unreserveCandidate($tf_candidate_id);
					
				break;

				case "unreserve_agent":
					unreserveAgent($logged_employee_id);
				break;

				case "check_candidate_reservation":
					$tf_candidate_id = $_POST["kandidat_id"];
					echo isCandidateFreeForCall($logged_employee_id, $tf_candidate_id);
				break;

				case "glossa_api_info":
					$kandidat_id = $_GET["id"];
					$data = [];

					$sql = "SELECT
								kandidat_ime,
								kandidat_prezime,
								kandidat_email,
								kandidat_mobitel,
								kandidat_check
							FROM
								idk_kandidati
							WHERE
								kandidat_id = $kandidat_id";

					$stmt = $db->prepare($sql);
					$stmt->execute();

					$result = $stmt->fetch();

					$data["fname"] 	= $result["kandidat_ime"];
					$data["lname"] 	= $result["kandidat_prezime"];
					$data["email"] 	= $result["kandidat_email"];
					$data["phone"] 	= $result["kandidat_mobitel"];
					$data["crm_id"] = $result["kandidat_check"];

					echo json_encode($data);
				break;

				case "getDateSecondMessage":
					$tf_nalog_id = $_POST["tf_nalog_id"];
					$tf_casting_id = $_POST["tf_casting_id"];
					$tf_call_appointment = $_POST["tf_call_appointment"] ?? null;

					$sql = "SELECT
								pap_date,
								pap_second_sending_number_days
							FROM
								idk_pp_appointments
							WHERE
								pap_id  = $tf_casting_id";
					$stmt = $db->prepare($sql);
					$stmt->execute();

					$result = $stmt->fetch();

					$broj_dana = $result["pap_second_sending_number_days"];

					$intervju = date("Y-m-d 09:00", strtotime($result["pap_date"] ."-$broj_dana days +1 days"));

					echo $intervju;
				break;

				case "getDateFirstMessage":
					$tf_nalog_id = $_POST["tf_nalog_id"];
					$tf_casting_id = $_POST["tf_casting_id"];
					$tf_call_appointment = $_POST["tf_call_appointment"] ?? null;

					$sql = "SELECT
								pap_date,
								pap_first_sending_number_days
							FROM
								idk_pp_appointments
							WHERE
								pap_id  = $tf_casting_id";
					$stmt = $db->prepare($sql);
					$stmt->execute();

					$result = $stmt->fetch();

					$broj_dana = $result["pap_first_sending_number_days"];

					$intervju = date("Y-m-d 09:00", strtotime($result["pap_date"] ."-$broj_dana days +1 days"));

					echo $intervju;
				break;

				case "cancel_reservation":

					$candidate_id = $_POST['candidate_id'];
					
					unreserveCandidate($candidate_id);
					$agent_id = disconnectAgentFromCandidate($candidate_id);

					$log_desc = "Zaposlenik odrezervisao kandidata: ". $candidate_id ." od agenta: ".$agent_id;
					$log_type = 0;
					addToLogs($log_desc, $log_type);
				
				break;

				case "change_reservation":

					$candidate_id = $_POST['candidate_id'];
					$new_agent_id = $_POST['new_agent_id'];

					unreserveCandidate($candidate_id);
					$old_agent_id = disconnectAgentFromCandidate($candidate_id);

					connectAgentToCandidate($candidate_id, $new_agent_id);

					$log_desc = "Zaposlenik odrezervisao kandidata: ". $candidate_id ." od agenta: ".$old_agent_id . " i dodijelio drugom agentu: " . $new_agent_id;
					$log_type = 0;
					addToLogs($log_desc, $log_type);
					echo getEmployeeFullnameById($new_agent_id);

				break;

				case "check_current_call":

					$candidate_id = $_POST['candidate_id'];
					$check_tf_call = $db->prepare("SELECT tf_kandidat_id FROM idk_tf_reservations WHERE tf_kandidat_id = :tf_kandidat_id");
					$check_tf_call->execute(array(':tf_kandidat_id' => $candidate_id));
					$is_there = $check_tf_call->rowCount();

					echo $is_there;

				break;

				case "check_task_finished":

					$task_id = $_POST['task_id'];
					$check_task_active = $db->prepare("SELECT tf_last_active_task FROM idk_task_force WHERE tf_id = :task_id");
					$check_task_active->execute(array(':task_id' => $task_id));
					$row_task_active = $check_task_active->fetch();
					echo $row_task_active['tf_last_active_task'];

				break;

				case "getVocationsByVocationGroup":
					$vocation_groups = $_REQUEST['filter_vocation_groups'];
				
					$add_where = "";
					if(in_array(0, $vocation_groups))
						$add_where = ' OR ss1.ss_struka_id IS NULL ';

					$query_get_vocations = $db -> prepare('
						SELECT main.ss_naziv, min_smjer_ids.min_ss_id
						FROM(
							SELECT ss1.ss_id, ss1.ss_naziv
							FROM idk_skole_smjerovi ss1
							WHERE ss1.ss_struka_id IN ('.implode(',',$vocation_groups).') '.$add_where.'
							GROUP BY (ss1.ss_naziv)
						) main
						JOIN (
							SELECT min(ss2.ss_id) as min_ss_id, ss2.ss_naziv
							FROM idk_skole_smjerovi ss2
							GROUP BY(ss2.ss_naziv)
						) min_smjer_ids
						ON main.ss_naziv = min_smjer_ids.ss_naziv

					');
					$query_get_vocations -> execute();
				
					echo '<select id="filter_vocations" class="selectpicker" multiple data-live-search="true" data-actions-box="true">';
					while($row_get_vocations = $query_get_vocations -> fetch()){
						echo '<option value = "'.$row_get_vocations["min_ss_id"].'">'.$row_get_vocations["ss_naziv"].'</option>';
					}
					echo '</select>';
				break;

				case "addVocationToOrder":
					$vocations = $_REQUEST['selected_vocations'];
					$order_id = $_REQUEST['order_id'];

					if($vocations AND $order_id){
						$query_get_current_vocations = $db -> prepare("
							SELECT smjer_id
							FROM idk_nalog_smjer
							WHERE nalog_id = $order_id
						");
						$query_get_current_vocations -> execute();
						$current_vocations = array();
						while($row = $query_get_current_vocations -> fetch()){
							array_push($current_vocations, $row['smjer_id']);
						}
	
						$insert_statements = "";
						foreach($vocations as $vocation)
							if(!in_array($vocation, $current_vocations))
								$insert_statements.= " INSERT INTO idk_nalog_smjer (nalog_id, smjer_id) VALUES ('".$order_id."','".$vocation."'); ";
						
	
						// 1 - refresh table, 0 - don't
						if($insert_statements){
							$query_insert = $db -> prepare($insert_statements);
							$query_insert -> execute();
						}
					}
				break;

				case "getOrderVocationTable":
					$order_id = $_REQUEST['order_id'];
					
					$query_get_current_vocations = $db -> prepare("
						SELECT id, ss_naziv, ss_naziv_de, ss_naziv_en, smjer_id, ss_skola_id
						FROM idk_nalog_smjer
						JOIN idk_skole_smjerovi 
						ON ss_id = smjer_id
						WHERE nalog_id = $order_id
					");
					
					$query_get_current_vocations -> execute();
					echo '
						<table id = "vocation_table" style = "width:100%">
							<thead>
								<th style = "text-align:center">ID smjera</th>
								<th style = "text-align:center">Naziv smjera</th>
								<th style = "text-align:center">Naziv smjera DE</th>
								<th style = "text-align:center">Naziv smjera EN</th>
								<th style = "text-align:center">Ukloni smjer</th>
							</thead>
							<tbody>
					';

					while($row = $query_get_current_vocations -> fetch()){
						
						if($row['ss_naziv_de'] == ""){
							$njemacki = "Uredi naziv";
						}else{
							$njemacki = $row['ss_naziv_de'];
						}
						if($row['ss_naziv_en'] == ""){
							$engleski = "Uredi naziv";
						}else{
							$engleski = $row['ss_naziv_en'];
						}
						
						echo '
							<tr>
								<td style = "text-align:center">'.$row['smjer_id'].'</td>
								<td>'.$row['ss_naziv'].'</td>
								<td><a href="'.getSiteURLR().'skole?page=edit_school_direction&ids='.$row['ss_skola_id'].'&idss='.$row['smjer_id'].'" target="_BLANK">'.$njemacki.'</a></td>
								<td><a href="'.getSiteURLR().'skole?page=edit_school_direction&ids='.$row['ss_skola_id'].'&idss='.$row['smjer_id'].'" target="_BLANK">'.$engleski.'</a></td>
								<td style = "text-align:center"> <button class = "remove_vocation" vocation_id = "'.$row['smjer_id'].'"><i class="fa fa-ban" aria-hidden="true"></i></button></td>
							</tr>
						';
					}
					echo '</tbody></table>';
				break;

				case "removeVocationFromOrder":
					$vocation_id = $_REQUEST['vocation_id'];
					$order_id = $_REQUEST['order_id'];

					$query_delete = $db -> prepare("
						DELETE FROM idk_nalog_smjer WHERE nalog_id = $order_id AND smjer_id = $vocation_id
					");

					$query_delete -> execute();

				break;

				case "glossa_info":
					$kandidat_id = $_POST['kandidat_id'];
					$statusi = array(4, 9, 10, 12, 15, 18, 21, 24, 27);
					$glossa_info = $db->prepare("SELECT kandidat_glossa, kandidat_status_prijave FROM idk_kandidati WHERE kandidat_id = :kandidat_id");
					$glossa_info->execute(array(':kandidat_id' => $kandidat_id));
					$row_glossa_info = $glossa_info->fetch();

					$info = $row_glossa_info['kandidat_glossa'];

					if(in_array($row_glossa_info['kandidat_status_prijave'], $statusi) && $info == 3)
					{
						$info = 1;
					}

					
					echo $info;
				break;

				case "editDepartureTypeForCandidate":
					$candidateId = $_POST["candidateId"] ?? null;
					$departureType = $_POST["departureType"] ?? null;
					$departureTypeOld = $_POST["departureTypeOld"] ?? null;
					$parallelAppliesWestBalkan = $_POST["parallelAppliesWestBalkan"] ?? null;
					$parallelAppliesWestBalkanOld = $_POST["parallelAppliesWestBalkanOld"] ?? null;

					if($candidateId == null || $departureType == null || $departureTypeOld == null || $parallelAppliesWestBalkan == null || $parallelAppliesWestBalkanOld == null) {
						http_response_code(500);
						die("Nisu proslijedjeni svi parametri");
					}

					if(($departureTypeOld == 0 OR $departureTypeOld == 3) AND $departureType == 2){
						$resultPrebacivanja = prebaciNaPrikupljanjeDokumentacijeZapadniBalkan($candidateId);
						$logAutomatskoPrebacivanje = "Status automatskog prebacivanja na prikupljanje dokumentacije (ZB): ".$resultPrebacivanja;
					}else{
						$logAutomatskoPrebacivanje = "";
					}

					if($departureType == 3){
						$resultPrebacivanja = prebaciNaPrikupljanjeDokumentacijeRadnoIskustvo($candidateId);
						$logAutomatskoPrebacivanje = "Status automatskog prebacivanja na prikupljanje dokumentacije (RI): ".$resultPrebacivanja;
					}else{
						$logAutomatskoPrebacivanje = "";
					}

					$queryUpdate = $db->prepare("
						UPDATE 
							idk_kandidati
						SET 
							kandidat_nacin_odlaska = :valueDeparture, 
							kandidat_paralelno_zb = :valueParallelApplies
						WHERE 
							kandidat_id = :candidateId
					");
					$queryUpdate->execute(array(
						':valueDeparture' => $departureType,
						':valueParallelApplies' => $parallelAppliesWestBalkan,
						':candidateId' => $candidateId
					));

					$log_desc = "Zaposlenik promijenio način odlaska na ". $departureType ." za kandidata (".$candidateId."). ".$logAutomatskoPrebacivanje."";
					$log_type = 0;
					addToLogs($log_desc, $log_type);

				break; 

				case 'jobs_for_candidate':
					$candidateIdJSC = $_POST["candidateIdJSC"] ?? null;
					if($candidateIdJSC == null ) {
						http_response_code(500);
						die("Problem sa parametrima pri učitavanju informacija Odgovarajući poslovi za kandidata! Kontaktirajte administratora sistema!");
					}
					?>
						<div class="row">
							<div class="col-xs-12">
								<?php 
									$nalogIdsJSC = array();

									$candidateFreeForTransfer = checkIsCandidateFreeForTransferToNalog($candidateIdJSC);

									$disabled = "";
									$title = "";
									if(!$candidateFreeForTransfer["status"]){
										$disabled = "disabled";
										$title = $candidateFreeForTransfer["message"];
									}

									$resultsAdequateJobs = checkAdequateJobs($candidateIdJSC); 
									foreach($resultsAdequateJobs AS $resultAdequateJobs) {
										if(in_array($resultAdequateJobs['nalog_id'], $nalogIdsJSC) === false) {
											array_push($nalogIdsJSC, $resultAdequateJobs['nalog_id']);
										}
										unset($resultAdequateJobs);
									}
									
									// print("<pre>".print_r($resultsAdequateJobs,true)."</pre>");

									$query = $db->prepare("
										SELECT 
											n.nalog_id,
											n.nalog_broj,  
											n.nalog_naziv, 
											c.company_id,
											c.company_name,
											n.offers_and_benefits_of_employers 
										FROM 
											idk_nalozi n
										JOIN 
											idk_companies c
										ON 
											n.kompanija_id = c.company_id
										WHERE 
											n.nalog_id IN (".((count($nalogIdsJSC) > 0) ? implode(",",$nalogIdsJSC) : 0).")
											/*AND
											n.offers_and_benefits_of_employers is not null*/
									");
									$query->execute();
									if ($query->rowCount() > 0 AND count($nalogIdsJSC) > 0) {
										$rows = $query->fetchAll(PDO::FETCH_ASSOC);
										$group_data = array();
										foreach ($rows as $index => $row) {
											$offers_and_benefits = json_decode($row['offers_and_benefits_of_employers'], true);
											$group_data[$index + 1] = array(
												'nalog_id' => $row['nalog_id'],
												'nalog_broj' => $row['nalog_broj'],
												'nalog_naziv' => $row['nalog_naziv'],
												'company_id' => $row['company_id'],
												'company_name' => $row['company_name'],
												'offers_and_benefits_of_employers' => $offers_and_benefits
											);
											unset($offers_and_benefits);
										}
										unset($rows);
										//print("<pre>".print_r($group_data,true)."</pre>");
										?>
											<table id="tableJFC" class="display" cellspacing="0" width = "100%">
												<thead>
													<tr>
														<th class="text-center">#</th>
														<th class="text-center">Nalog</th>
														<th class="text-center">Kompanija</th>
														<th class="text-center">Ponude i benefiti od poslodavca</th>
														<th class="text-center">Akcija</th>
													</tr>
												</thead>
												<tbody>
													<?php 
														foreach($group_data as $index_of_grouped_data => $row_of_grouped_data) {
															?>
																<tr>
																	<td class="text-center"><strong><?php echo $row_of_grouped_data['nalog_broj']; ?></strong></td>
																	<td class="text-center">
																		<a href="/nalozi?page=open&id=<?php echo $row_of_grouped_data['nalog_id']; ?>" target="_BLANK">
																			<strong><?php echo $row_of_grouped_data['nalog_naziv']; ?></strong>
																		</a>
																	</td>
																	<td class="text-center">
																		<a href="/companies?page=open&id=<?php echo $row_of_grouped_data['company_id']; ?>" target="_BLANK">
																			<strong><?php echo $row_of_grouped_data['company_name']; ?></strong>
																		</a>
																	</td>
																	
																	<?php 
																	if (count($row_of_grouped_data['offers_and_benefits_of_employers']) > 0 ){
																		?>
																		<td>
																			<ul class="list-group">
																				<li class="list-group-item">
																					<div class="row">
																						<strong class="col-lg-3 text-right">Država, grad:</strong>
																						<div class="col-lg-9"><?php echo $row_of_grouped_data['offers_and_benefits_of_employers']['countryCity']; ?></div>
																					</div>
																				</li>
																				<li class="list-group-item">
																					<div class="row">
																						<strong class="col-lg-3 text-right">Naziv pozicije:</strong>
																						<div class="col-lg-9"><?php echo $row_of_grouped_data['offers_and_benefits_of_employers']['position'] ?? '<span class="label label-danger">Potrebno urediti</span>'; ?></div>
																					</div>
																				</li>
																				<li class="list-group-item">
																					<div class="row">
																						<strong class="col-lg-3 text-right">Opis pozicije:</strong>
																						<div class="col-lg-9"><?php echo $row_of_grouped_data['offers_and_benefits_of_employers']['positionDesc'] ?? '<span class="label label-danger">Potrebno urediti</span>'; ?></div>
																					</div>
																				</li>
																				<li class="list-group-item">
																					<div class="row">
																						<strong class="col-lg-3 text-right">Zarada:</strong>
																						<div class="col-lg-9"><?php echo '<span class="label label-success">'.$row_of_grouped_data['offers_and_benefits_of_employers']['salary']['salary'].' '.$row_of_grouped_data['offers_and_benefits_of_employers']['salary']['salaryPeriod'] .' '.$row_of_grouped_data['offers_and_benefits_of_employers']['salary']['salaryType'].'</span>'; ?></div>
																					</div>
																				</li>
																				<li class="list-group-item">
																					<div class="row">
																						<strong class="col-lg-3 text-right">Bonus:</strong>
																						<div class="col-lg-9"><?php echo (($row_of_grouped_data['offers_and_benefits_of_employers']['bonus']['bonusQuestion'] == "da") ? (($row_of_grouped_data['offers_and_benefits_of_employers']['bonus']['bonusHasAmountQuestion'] == "da") ? '<span class="label label-success">'.$row_of_grouped_data['offers_and_benefits_of_employers']['bonus']['bonusAmount'].' '.$row_of_grouped_data['offers_and_benefits_of_employers']['bonus']['bonusPeriod'].' '.$row_of_grouped_data['offers_and_benefits_of_employers']['bonus']['bonusType'].'</span>' : '<span class="label label-warning">DA - nepoznat iznos</span>' ) : '<span class="label label-danger">NE</span>'); ?></div>
																					</div>
																				</li>
																				<li class="list-group-item">
																					<div class="row">
																						<strong class="col-lg-3 text-right">Smještaj:</strong>
																						<div class="col-lg-9"><?php echo (($row_of_grouped_data['offers_and_benefits_of_employers']['apartment'] != "da") ? (($row_of_grouped_data['offers_and_benefits_of_employers']['apartment'] != "ne") ? (($row_of_grouped_data['offers_and_benefits_of_employers']['apartment'] == "obezbjeđen_i_plaćen") ? '<span class="label label-success">Obezbjeđen i plaćen</span>' : '<span class="label label-warning">Obezbjeđen i odbija se od plate</span>') : '<span class="label label-danger">NE</span>') : '<span class="label label-danger">Potrebno urediti</span>'); ?></div>
																					</div>
																				</li>
																				<li class="list-group-item">
																					<div class="row">
																						<strong class="col-lg-3 text-right">Topli obrok:</strong>
																						<div class="col-lg-9"><?php echo (($row_of_grouped_data['offers_and_benefits_of_employers']['hotMeal']['hotMealQuestion'] == "da") ? (($row_of_grouped_data['offers_and_benefits_of_employers']['hotMeal']['hotMealHasAmountQuestion'] == "da") ? '<span class="label label-success">'.$row_of_grouped_data['offers_and_benefits_of_employers']['hotMeal']['hotMealAmount'].' '.$row_of_grouped_data['offers_and_benefits_of_employers']['hotMeal']['hotMealPeriod'].' '.$row_of_grouped_data['offers_and_benefits_of_employers']['hotMeal']['hotMealType'].'</span>' : '<span class="label label-warning">DA - nepoznat iznos</span>' ) : '<span class="label label-danger">NE</span>'); ?></div>
																					</div>
																				</li>
																				<li class="list-group-item">
																					<div class="row">
																						<strong class="col-lg-3 text-right">Dodatno:</strong>
																						<div class="col-lg-9"><?php echo (($row_of_grouped_data['offers_and_benefits_of_employers']['additionally'] != null) ? $row_of_grouped_data['offers_and_benefits_of_employers']['additionally'] : '<span class="label label-default">Nisu unešene dodatne informacije</span>'); ?></div>
																					</div>
																				</li>
																			</ul>
																		</td>
																		<?php 
																	}else{
																		?>
																			<td class="text-center">
																			<strong>Ponuda i benefiti nisu unešeni!</strong>
																	</td>
																		<?php 
																	} ?>
																	
																	<td class="text-center">
																		<button 
																			class="btn material-btn main-container__column" 
																			onclick="getAgentInCallWithCandidateJFC(this)"
																			data-candidate_id="<?php echo $candidateIdJSC; ?>"
																			data-nalog_id="<?php echo $row_of_grouped_data['nalog_id']; ?>"
																			title="<?php echo $title; ?>" 
																			<?php echo $disabled; ?>
																		>
																			<i class="fa fa-share-square-o" aria-hidden="true"></i> 
																			Prebaci
																		</button>
																	</td>
																</tr>
															<?php 
														}
													?>
												</tbody>
											</table>
										<?php 
										unset($group_data);
									} else {
										?>
											<div class="alert alert-warning text-center" role="alert">
												<i class="fa fa-info-circle fa-3x" aria-hidden="true"></i>
                                                <br>
												<br>
												<h4><strong>Za kandidata nije pronađen ni jedan odgovarajući posao!</strong></h4>
											</div>
										<?php
									}
									unset($nalogIdsJSC);
									unset($resultsAdequateJobs);
								?>
							</div>
						</div>
					<?php 
				break; 

				case "get_schools_for_component":
					$query = $db->prepare("
						SELECT 
							skola_id AS id, 
							skola_naziv AS name, 
							skola_naziv_de as name_de, 
							skola_tip_obrazovanja as type_school
						FROM 
							idk_skole
					");
					$query->execute();
					$result = $query->fetchAll(PDO::FETCH_CLASS);
					echo json_encode($result);

				break;

				case "get_direction_for_component":
					$skolaIdNew = $_POST["skolaIdNew"];
					$query = $db->prepare("
						SELECT 
							ss_id AS id, 
							ss_naziv AS name, 
							ss_naziv_de as name_de
						FROM 
							idk_skole_smjerovi
						WHERE 
							ss_skola_id = :ss_skola_id
					");
					$query->execute(array(
						":ss_skola_id" => $skolaIdNew
					));
					$result = $query->fetchAll(PDO::FETCH_CLASS);
					echo json_encode($result);

				break;

				case "get_institution_processing":
					$skolaIdNew = $_POST["skolaIdNew"];
					$smjerIdNew = $_POST["smjerIdNew"];
					$idUstanova = $_POST["idUstanova"];
					$query = $db->prepare("
						SELECT 
							(
								CASE 
									WHEN count(id_ustanove_nd) = 0 THEN 0
									ELSE 1
								END
							)
							AS countUstanova
						FROM 
							idk_nd_ustanove u
						JOIN 
							idk_nd_ustanove_skola us
						ON 
							u.id_ustanove_nd = us.idd_ustanove_nd
							AND 
							us.skola_idd = :skola_idd
						JOIN 
							idk_nd_ustanove_skole_smjerovi uss
						ON 
							uss.idd_skole_ustanove_nd = us.id_skole_ustanove_nd 
							AND 
							uss.ss_idd = :ss_idd
						WHERE 
							u.id_ustanove_nd = :id_ustanove_nd
					");
					$query->execute(array(
						":skola_idd" => $skolaIdNew,
						":ss_idd" => $smjerIdNew, 
						":id_ustanove_nd" => $idUstanova
					));
					$result = $query->fetch();
					echo json_encode($result["countUstanova"]);
				break;

				case "change_school_and_direction_for_component":
					$kandidatId = $_POST["kandidatId"];
					$skolaIdNew = $_POST["skolaIdNew"];
					$smjerIdNew = $_POST["smjerIdNew"];
					$skolaIdOld = $_POST["skolaIdOld"];
					$smjerIdOld = $_POST["smjerIdOld"];
					$idUstanova = $_POST["idUstanova"];
					$isProcessing = $_POST["isProcessing"];

					$query_update = $db->prepare("
						UPDATE
							idk_nd_kandidata
						SET
							skola_nd_kandidata = :skola_nd_kandidata,
							skola_smjer_nd_kandidata = :skola_smjer_nd_kandidata
						WHERE 
							id_broj_nd_kandidata = :id_broj_nd_kandidata
					");
					$query_update->execute(array(
						':id_broj_nd_kandidata' => $kandidatId,
						':skola_nd_kandidata' => $skolaIdNew,
						':skola_smjer_nd_kandidata' => $smjerIdNew
					));

					if ($isProcessing == 0){
						$checkSchool = $db->prepare("
							SELECT
								id_skole_ustanove_nd
							FROM 
								idk_nd_ustanove_skola
							WHERE 
								idd_ustanove_nd = :idd_ustanove_nd
								AND 
								skola_idd = :skola_idd
						");
						$checkSchool->execute(array(
							':idd_ustanove_nd' => $idUstanova,
							':skola_idd' => $skolaIdNew
						));
						if ($checkSchool->rowCount() == 1){
							$rowSchoolCheck = $checkSchool->fetch();
							$idSkoleUstanove = intval($rowSchoolCheck["id_skole_ustanove_nd"]); 
							$checkDirection = $db->prepare("
								SELECT
									id_skole_smjer_ustanove_nd 
								FROM 
									idk_nd_ustanove_skole_smjerovi
								WHERE 
									idd_skole_ustanove_nd = :idd_skole_ustanove_nd
									AND 
									ss_idd = :ss_idd
							");
							$checkDirection->execute(array(
								':idd_skole_ustanove_nd' => $idSkoleUstanove,
								':ss_idd' => $smjerIdNew
							));
							if ($checkDirection->rowCount() == 0){
								$insertDirection = $db->prepare("
									INSERT INTO idk_nd_ustanove_skole_smjerovi
									(
										idd_skole_ustanove_nd,
										ss_idd
									)
									VALUES
									(
										:idd_skole_ustanove_nd,
										:ss_idd
									)
								");
								$insertDirection->execute(array(
									':idd_skole_ustanove_nd' => $idSkoleUstanove, 
									':ss_idd' => $smjerIdNew
								));

								$insertDirectionId = $db->lastInsertId();
								$logDesc = "
									DIPL -> Edit škole/smjera kandidata -> Prilikom update-a škole i smjera za kandidata ID = [".$kandidatId."] u vrijednosti 
									['skola_nd_kandidata' => '".$skolaIdNew."', 'skola_smjer_nd_kandidata' => '".$smjerIdNew."'], ista skola i smjer je vezana 
									sa ustanovom ID = [".$idUstanova."] sa vrijednostima ['id_skole_ustanove_nd' => '".$idSkoleUstanove."', 'id_skole_smjer_ustanove_nd' => '".$insertDirectionId."']. 
									Veza je uspostavljena na osnovu već dodane vrijednosti 'id_skole_ustanove_nd' i novokreirane vrijednosti 'id_skole_smjer_ustanove_nd'.
								";
								addToLogs($logDesc, 0);
							} else {
								$rowDirectionCheck = $checkDirection->fetch();
								$idSmjerUstanove = intval($rowDirectionCheck["id_skole_smjer_ustanove_nd"]);

								$logDesc = "
									DIPL -> Edit škole/smjera kandidata -> Prilikom update-a škole i smjera za kandidata ID = [".$kandidatId."] u vrijednosti 
									['skola_nd_kandidata' => '".$skolaIdNew."', 'skola_smjer_nd_kandidata' => '".$smjerIdNew."'], ista skola i smjer je već povezana 
									sa ustanovom ID = [".$idUstanova."] sa vrijednostima ['id_skole_ustanove_nd' => '".$idSkoleUstanove."', 'id_skole_smjer_ustanove_nd' => '".$idSmjerUstanove."'].
								";
								addToLogs($logDesc, 0);
							}
						}else{
							$insertSchool = $db->prepare("
								INSERT INTO idk_nd_ustanove_skola
								(
									skola_idd,
									idd_ustanove_nd
								)
								VALUES
								(
									:skola_idd,
									:idd_ustanove_nd
								)
							");
							$insertSchool->execute(array(
								':skola_idd' => $skolaIdNew, 
								':idd_ustanove_nd' => $idUstanova
							));
							$insertSchoolId = $db->lastInsertId();

							$insertDirection = $db->prepare("
								INSERT INTO idk_nd_ustanove_skole_smjerovi
								(
									idd_skole_ustanove_nd,
									ss_idd
								)
								VALUES
								(
									:idd_skole_ustanove_nd,
									:ss_idd
								)
							");
							$insertDirection->execute(array(
								':idd_skole_ustanove_nd' => $insertSchoolId, 
								':ss_idd' => $smjerIdNew
							));
							$insertDirectionId = $db->lastInsertId();

							$logDesc = "
								DIPL -> Edit škole/smjera kandidata -> Prilikom update-a škole i smjera za kandidata ID = [".$kandidatId."] u vrijednosti 
								['skola_nd_kandidata' => '".$skolaIdNew."', 'skola_smjer_nd_kandidata' => '".$smjerIdNew."'], ista skola i smjer je vezana 
								sa ustanovom ID = [".$idUstanova."] sa vrijednostima ['id_skole_ustanove_nd' => '".$insertSchoolId."', 'id_skole_smjer_ustanove_nd' => '".$insertDirectionId."']. 
								Veza je uspostavljena na osnovu novokreirane vrijednosti 'id_skole_ustanove_nd' i novokreirane vrijednosti 'id_skole_smjer_ustanove_nd'.
							";
							addToLogs($logDesc, 0);
						}
					}

					/*
						Edukacije Profil posao Check
						*/
							$result = checkInsertUpdateCandidateDiplEducationInCandidateJobArrayR($kandidatId, $skolaIdNew, $smjerIdNew, $skolaIdOld, $smjerIdOld);
							$logDesc = $result["statusMessage"]. " Status code: " . $result["statusCode"];
							addToLogs($logDesc, 0);
							unset($result);
						/*
						Edukacije Profil posao Check
					*/

					$logDesc = "
						DIPL -> Edit škole/smjera kandidata -> Izvršen update škole i smjera za kandidata ID = [".$kandidatId."] u vrijednosti 
						['skola_nd_kandidata' => '".$skolaIdNew."', 'skola_smjer_nd_kandidata' => '".$smjerIdNew."'].
					";
					addToLogs($logDesc, 0);

					echo 1;

				break;

				case "editPoslodavacTraziJezik":
					$nalog_id = $_POST["nalog_id"] ?? null;
					$nalog_poslodavac_trazi_jezik = $_POST["nalog_poslodavac_trazi_jezik"] ?? null;
					
					if($nalog_id == null || $nalog_poslodavac_trazi_jezik == null) {
						http_response_code(500);
						die("Nisu proslijedjeni svi parametri");
					}

					$queryUpdate = $db->prepare("
						UPDATE idk_nalozi
						SET nalog_poslodavac_trazi_jezik = :nalog_poslodavac_trazi_jezik
						WHERE nalog_id = :nalog_id 
					");
					$queryUpdate->execute(array(
						':nalog_id' => $nalog_id, 
						':nalog_poslodavac_trazi_jezik' => $nalog_poslodavac_trazi_jezik
					));

					$log_desc = "Zaposlenik promijenio parametar kolona 'nalog_poslodavac_trazi_jezik' na vrijednost '". $nalog_poslodavac_trazi_jezik ."' za nalog ID = [".$nalog_id."].";
					$log_type = 0;
					addToLogs($log_desc, $log_type);
				break;

				case "editPoslodavacKoristiPP":
					$nalog_id = $_POST["nalog_id"] ?? null;
					$nalog_poslodavac_koristi_pp = $_POST["nalog_poslodavac_koristi_pp"] ?? null;
					
					if($nalog_id == null || $nalog_poslodavac_koristi_pp == null) {
						http_response_code(500);
						die("Nisu proslijedjeni svi parametri");
					}

					$queryUpdate = $db->prepare("
						UPDATE idk_nalozi
						SET nalog_poslodavac_koristi_pp = :nalog_poslodavac_koristi_pp
						WHERE nalog_id = :nalog_id 
					");
					$queryUpdate->execute(array(
						':nalog_id' => $nalog_id, 
						':nalog_poslodavac_koristi_pp' => $nalog_poslodavac_koristi_pp
					));

					$log_desc = "Zaposlenik promijenio parametar kolona 'nalog_poslodavac_koristi_pp' na vrijednost '". $nalog_poslodavac_koristi_pp ."' za nalog ID = [".$nalog_id."].";
					$log_type = 0;
					addToLogs($log_desc, $log_type);
				break;

				case "change_departure_type":
					/*
						Stari nacin prebacivanja za nacin posredovanja - vise se ne koristi case
						*/
					$candidate_id 	= $_POST['candidate_id'];
					$current_departure_type = $_POST['current_departure_type'];

					$set_to = 0;
					if($current_departure_type == 0){
						$set_to = 2;
						$resultPrebacivanja = prebaciNaPrikupljanjeDokumentacijeZapadniBalkan($candidate_id);
						$log_automatsko_prebacivanje = "Status automatskog prebacivanja na prikupljanje dokumentacije: ".$resultPrebacivanja;
					}else{
						$log_automatsko_prebacivanje = "";
					}
					
					$update_query = $db -> prepare("
						UPDATE idk_kandidati
						SET kandidat_nacin_odlaska = $set_to
						WHERE kandidat_id = $candidate_id
					");

					$log_desc = "Zaposlenik promijenio način odlaska na ". $set_to ." za kandidata (".$candidate_id."). ".$log_automatsko_prebacivanje."";
					$log_type = 0;
					addToLogs($log_desc, $log_type);

					$update_query -> execute();
					$updated_departure_type  = getCandidateDepartureType($candidate_id);
					echo '<span id = "departure_type_text" class="label label-success material-label material-label_success main-container__column">'.$updated_departure_type[1].'</span>';

					if($updated_departure_type[0] != 2){
						echo '
							<button id="change_departure_type" candidate_id = "'.$candidate_id.'" departure_type = "'.$updated_departure_type[0].'" class="pull-right material-btn_success main-container__column" style = "padding: 2px 10px; border: 0px; cursor: pointer; border-radius: 2px;" aria-hidden="true">
								<i class="fa fa-check" aria-hidden="true"></i> Promijeni na zapadno-balkanski sistem
							</button>
						';
					}
					else{
						echo '
							<button id="change_departure_type" candidate_id = "'.$candidate_id.'" departure_type = "'.$updated_departure_type[0].'" class="pull-right material-btn_danger main-container__column" style = "padding: 2px 10px; border: 0px; cursor: pointer; border-radius: 2px;" aria-hidden="true">
								<i class="fa fa-times" aria-hidden="true"></i> Promijeni
							</button>
						';
					}
				break;

				case "check_nalog_glossa":
					$nalog_id = $_GET['nalog_id'];
					$glossa_info = $db->prepare("SELECT nalog_slanje_na_glosu FROM idk_nalozi WHERE nalog_id = :nalog_id");
					$glossa_info->execute(array(':nalog_id' => $nalog_id));
					$row_glossa_info = $glossa_info->fetch();
					
					echo $row_glossa_info['nalog_slanje_na_glosu'];
				break;

				case "glossa_slanje_info":
					$kandidat_id = $_GET['kandidat_id'];

					$glossa_info = $db->prepare("SELECT kandidat_glossa, kandidat_status_prijave FROM idk_kandidati WHERE kandidat_id = :kandidat_id");
					$glossa_info->execute(array(':kandidat_id' => $kandidat_id));
					$row_glossa_info = $glossa_info->fetch();

					echo $row_glossa_info['kandidat_glossa'];
				break;

				case "deactivate_tf_nalog":

					$nalog_id = $_REQUEST['nalog_id'];
					$confirmation = $_REQUEST['confirmation'] ?? NULL;

					$json = getTaskForceInfoForNalog($nalog_id, 1);
                    $data = json_decode($json, true);
					
					if (empty($data) OR $confirmation != NULL) {
						// Uzmi trenutnu vrijednost nalog_prioriteta
						$stmt = $db->prepare("SELECT nalog_prioritet FROM idk_nalozi WHERE nalog_id = $nalog_id");
						$stmt -> execute();
						$row = $stmt->fetch();
						
						$currentValue = intval($row['nalog_prioritet']);

						// Pocetak transakcije
						$db->beginTransaction();

						try {
							// Update tog naloga na NULL
							$stmt = $db->prepare("UPDATE idk_nalozi SET nalog_prioritet = NULL WHERE nalog_id = $nalog_id");
							$stmt->execute();

							// Ako je trenutna vrijednost 1 onda sve buduce smanji za 1 osim onih koji su 1
							if ($currentValue === 1) {
								$stmt = $db->prepare("UPDATE idk_nalozi SET nalog_prioritet = nalog_prioritet - 1 WHERE nalog_prioritet IS NOT NULL AND nalog_prioritet > 1");
								$stmt->execute();
							}

							// Ako je veci od 1 onda smanji za 1 sve one poslije njega
							if ($currentValue > 1) {
								$stmt = $db->prepare("UPDATE idk_nalozi SET nalog_prioritet = nalog_prioritet - 1 WHERE nalog_prioritet IS NOT NULL AND nalog_prioritet > $currentValue AND nalog_id != $nalog_id");
								$stmt->execute();
							}

							if($confirmation != NULL){
								// OVDJE SVE OVE KANDIDATE STO JE NASAO POSTAVI NA LAST ACTIVE 0
								updateLastActiveTaskForceCandForNalog($nalog_id, 1);
							}

							$stmt = $db->prepare("DELETE FROM idk_tf_agent_nalog WHERE tfan_nalog = :tfan_nalog");
							$stmt->execute(array(
								":tfan_nalog" => $nalog_id
							));

							// Uradi transakcije
							$db->commit();

							echo -1;
						} catch (Exception $e) {
							$db->rollBack();
							throw $e;
						}
					}else{
						echo '<p><span class="label label-danger">Ne možete isključiti nalog jer ima kandidata:</span></p> ';
						echo ' <a  style="text-decoration:none" href="tf_nalog_list?page=candidates&nalog_id='.$nalog_id.'"><p><span class="label label-info">VIDI LISTU KANDIDATA</span></p></a>';
						
					}
				break;

				case "activate_tf_nalog":

					$nalog_id = $_REQUEST['nalog_id'];

					$select_max_tf_prioritet = $db->prepare("SELECT MAX(nalog_prioritet) as max_prioritet FROM idk_nalozi");
					$select_max_tf_prioritet -> execute();
					$row = $select_max_tf_prioritet->fetch();
					
					$new_tf_prioritet = intval($row['max_prioritet'])+1;

					$update = $db->prepare("UPDATE idk_nalozi SET nalog_prioritet = $new_tf_prioritet WHERE nalog_id = $nalog_id");
					$update -> execute();

				break;

				case "change_tf_prioritet":

					$nalog_id = $_REQUEST['nalog_id'];
					$nalog_prioritet = $_REQUEST['nalog_prioritet'];

					$update = $db->prepare("UPDATE idk_nalozi SET nalog_prioritet = $nalog_prioritet WHERE nalog_id = $nalog_id");
					$update -> execute();

				break;

				case "check_tf_nalog_candidates_on_status":

					$nalog_id = $_REQUEST['nalog_id'];
					// Ne smije biti nijedan na pristao ili dolazi
					$status = "16, 18";
					$response = checkIfTFHasUnfinishedCandidatesOnStatus($nalog_id, $status);
					echo intval($response);
				break;

				case "change_appointment_time":

					$pah_id = $_REQUEST['termin_pah_id'];
					$pah_time = date("H:i:s", strtotime($_REQUEST['nova_vrijednost']));
					$update = $db->prepare("UPDATE idk_pp_appointment_hours SET pah_time = '$pah_time' WHERE pah_id = $pah_id");
					$update -> execute();

				break;

				case "add_appointment_time":

					$pap_id = $_REQUEST['termin_pap_id'];
					$pah_time = date("H:i:s", strtotime($_REQUEST['nova_vrijednost']));

					$insert = $db->prepare("
									INSERT INTO idk_pp_appointment_hours
										(pah_time, pah_employee_id, pap_id)
									VALUES
										(:pah_time, :pah_employee_id, :pap_id)");

					$insert->execute(array(
									':pah_time' => $pah_time,
									':pah_employee_id' => $logged_employee_id,
									':pap_id' => $pap_id));

				break;

				case "delete_appointment_time":
					$pah_id = $_REQUEST['termin_pah_id'];

					$delete = $db->prepare("DELETE FROM idk_pp_appointment_hours WHERE pah_id = :pah_id");
					$delete->execute(array(":pah_id" => $pah_id));

				break;

				case "check_candidates_for_appointment_time":
					$pah_id = $_REQUEST['termin_pah_id'];

					$select = $db->prepare("SELECT pca_id FROM idk_pp_cand_appts WHERE pca_pah_id = :pca_pah_id");
					$select->execute(array(":pca_pah_id" => $pah_id));
					echo $select->rowCount();
				break;

				case "check_appointment_time_for_appointment":
					$pap_id = $_REQUEST['pap_id'];

					$select = $db->prepare("SELECT pah_id, pah_time FROM idk_pp_appointment_hours WHERE pap_id = :pap_id");
					$select->execute(array(":pap_id" => $pap_id));
					echo json_encode($select->fetchAll(PDO::FETCH_ASSOC));
				break;

				case "get_pregled_prijava":

					$filter_period_prijava 	= $_REQUEST['filter_period_prijava'];
					$danas = date("Y-m-d");


					// datum kreiranja
					if(strpos($filter_period_prijava, "to")){
						$tmp_period = array();
						$tmp_period = explode('to', $filter_period_prijava);
						$first_day = date("Y-m-d", strtotime($tmp_period[0]));
						$last_day = date("Y-m-d", strtotime($tmp_period[1]));
						
					}
					else if($filter_period_prijava != ""){
						$first_day = date("Y-m-d", strtotime($filter_period_prijava));
						$last_day = date("Y-m-d", strtotime($filter_period_prijava));
					}else{
						$first_day = date("Y-m-d", strtotime('- 14 days', strtotime($danas)));
						$last_day = $danas;
					}
					
					$sql_select = array();
					
					//DANI ZA KOLONE TABELE
					$dani_ispis_array = array();
					$dani_query_array = array();
					
					
					$datediff = strtotime($last_day) - strtotime($first_day);

					$broj_dana = round($datediff / (60 * 60 * 24));


					for($i=0;$i<=$broj_dana;$i++){
						array_push($dani_query_array, date("Y-m-d", strtotime('- '.$i.' days', strtotime($last_day) )));
						array_push($dani_ispis_array, date("d.m.Y", strtotime('- '.$i.' days', strtotime($last_day) )));
						array_push($sql_select, 
							'MAX(
								CASE WHEN glavni_query.dan_prijave = "'.  date("d.m.Y", strtotime('- '.$i.' days', strtotime($last_day) )) .'"  
									THEN glavni_query.broj_prijava ELSE 0 END 
							) AS "'.  date("d.m.Y", strtotime('- '.$i.' days', strtotime($last_day) )) .'"'
						);
					}
					// var_dump($dani_ispis_array);
					// var_dump($dani_query_array);

					$sql_select_implode = implode(",", $sql_select);

					$query_nalozi = $db->prepare("
							SELECT glavni_query.nalog_id, glavni_query.nalog_naziv, glavni_query.nalog_broj,
							".$sql_select_implode."
							
							FROM 
							(
								SELECT
							
									nal.nalog_id, nal.nalog_naziv, nal.nalog_broj, nal.nalog_status, DATE_FORMAT(lks_datetime, '%d.%m.%Y') AS dan_prijave, COUNT(lks_id) as broj_prijava
								FROM
									idk_log_kandidat_statusi
								JOIN(
									SELECT
										MAX(lks_id) AS max_log_id
									FROM
										idk_log_kandidat_statusi
									WHERE
										lks_kandidat_id != 0 AND lks_status_obrade IN(0, 9)
									GROUP BY
										lks_kandidat_id
								) max_log
								ON
									lks_id = max_log.max_log_id
								JOIN idk_link_generator lg ON
									lks_link_id = lg.lg_id AND lks_link_id IS NOT NULL AND lks_link_id != 1
								JOIN idk_nalozi nal ON
									lg.lg_nalogid = nal.nalog_id
								
								GROUP BY
									dan_prijave,
									nal.nalog_id  
								ORDER BY `dan_prijave` DESC
							) glavni_query
							WHERE glavni_query.nalog_status != 12
							GROUP BY glavni_query.nalog_id
							ORDER BY glavni_query.nalog_id DESC
							
					");

					$query_nalozi->execute();
					// var_dump($query_nalozi);
					?>
					<table id="table_prijave" class="display" cellspacing="0" width="100%">
						<thead>
							
							<th class="text-center">Nalog</th>
							<?php 
							foreach($dani_ispis_array as $dan_ispis){
								echo '<th class="text-right">'.date("d.m", strtotime($dan_ispis)).'</th>';
							}
							?>
							
						</thead>
						<tbody>
							<?php
							    $sums = array_fill_keys($dani_ispis_array, 0); // Initialize an array to store the sums for each date column
								$nalog_arrays = array_fill_keys($dani_ispis_array, []); // Initialize an array to store nalog_ids for each date column
								while($row = $query_nalozi->fetch()){
									
									$nalog_id = $row['nalog_id'];
									$nalog_broj = $row['nalog_broj'];
									$nalog_naziv = $row['nalog_naziv'];

									 // Initialize the sum for this row
									$rowSum = 0;

									foreach ($dani_ispis_array as $dan_ispis) {
										$value = $row[$dan_ispis];
										$rowSum += $value;

										// Update the sum for the date column
										$sums[$dan_ispis] += $value;

										// Add nalog_id to the array only if the value is not 0
										if ($value > 0) {
											$nalog_arrays[$dan_ispis][] = $nalog_id;
										}
									}
									?>
									<tr>
										<td class="text-center"><?php echo $nalog_broj." - ".$nalog_naziv; ?></td>
										<?php
											foreach($dani_ispis_array as $dan_ispis){

												echo '<td class="text-right"><a href="/pregledPrijava.php?page=candidates_list&nalog_id='.$nalog_id.'&dan='.$dan_ispis.'">'.$row[$dan_ispis].'</a></td>';
											}
										?>
									</tr>
									<?php 
								} ?>
						</tbody>
						<tfoot>
							<th class="text-center">Sum</th>
							<?php
							foreach ($dani_ispis_array as $dan_ispis) {
								echo '<th class="text-right">
										<form  target="_blank" action="pregledPrijava.php?page=candidates_list_total" method="POST">
											<input type="hidden" name="dan" value="'.$dan_ispis.'" />
											<input type="hidden" name="nalog_ids[]" value="' . implode(",", $nalog_arrays[$dan_ispis]) . '" />
											<button type="submit" style="  all: unset; cursor: pointer;">' . $sums[$dan_ispis] . '</button>
										</form>
									</th>';
							}
							?>
						</tfoot>
					</table>

					<?php

				break;
				case "get_financije_fakture":

					$filter_kreirana_faktura_period 	= $_REQUEST['filter_kreirana_faktura_period'];
					$filter_fakturisana_faktura_period 	= $_REQUEST['filter_fakturisana_faktura_period'];
					$filter_uplacena_faktura_period 	= $_REQUEST['filter_uplacena_faktura_period'];
					$filter_select_nalog_faktura		= $_REQUEST['filter_select_nalog_faktura'];
					$filter_select_status_rate_faktura 	= $_REQUEST['filter_select_status_rate_faktura'];
					$filter_select_firma_faktura	 	= $_REQUEST['filter_select_firma_faktura'];

					$uslov_vrijeme_kreiranja_fakture 	= "";
					$uslov_vrijeme_fakturisanja_fakture = "";
					$uslov_vrijeme_uplate_fakture 		= "";
					$uslov_nalog 						= "";
					$uslov_rate							= "";
					$uslov_firma						= "";
					
					// datum kreiranja
					if(strpos($filter_kreirana_faktura_period, "to")){
						$tmp_period = array();
						$tmp_period = explode('to', $filter_kreirana_faktura_period);
						$filter_kreirana_faktura_datum_od = date("Y-m-d", strtotime($tmp_period[0]));
						$filter_kreirana_faktura_datum_do = date("Y-m-d", strtotime($tmp_period[1]));
						$uslov_vrijeme_kreiranja_fakture = " AND fin.kf_datum_stvarni BETWEEN '".$filter_kreirana_faktura_datum_od."' AND '".$filter_kreirana_faktura_datum_do."'";
					}
					else if($filter_kreirana_faktura_period != ""){
						$uslov_vrijeme_kreiranja_fakture = " AND fin.kf_datum_stvarni = ".date("d.m.Y",strtotime($filter_kreirana_faktura_period));
					}
					
					// datum fakturisanja
					if(strpos($filter_fakturisana_faktura_period, "to")){
						$tmp_period = array();
						$tmp_period = explode('to', $filter_fakturisana_faktura_period);
						$filter_fakturisana_faktura_datum_od = date("Y-m-d", strtotime($tmp_period[0]));
						$filter_fakturisana_faktura_datum_do = date("Y-m-d", strtotime($tmp_period[1]));
						$uslov_vrijeme_fakturisanja_fakture = " AND fin.kf_datum_fakturisanja BETWEEN '".$filter_fakturisana_faktura_datum_od."' AND '".$filter_fakturisana_faktura_datum_do."'";
					}
					else if($filter_fakturisana_faktura_period != ""){
						$uslov_vrijeme_fakturisanja_fakture = " AND fin.kf_datum_fakturisanja = ".date("d.m.Y",strtotime($filter_fakturisana_faktura_period));
					}

					// datum uplate
					if(strpos($filter_uplacena_faktura_period, "to")){
						$tmp_period = array();
						$tmp_period = explode('to', $filter_uplacena_faktura_period);
						$filter_uplacena_faktura_datum_od = date("Y-m-d", strtotime($tmp_period[0]));
						$filter_uplacena_faktura_datum_do = date("Y-m-d", strtotime($tmp_period[1]));
						$uslov_vrijeme_uplate_fakture = " AND fin.kf_datum_placanja BETWEEN '".$filter_uplacena_faktura_datum_od."' AND '".$filter_uplacena_faktura_datum_do."'";
					}
					else if($filter_uplacena_faktura_period != ""){
						$uslov_vrijeme_uplate_fakture = " AND fin.kf_datum_placanja = ".date("d.m.Y",strtotime($filter_uplacena_faktura_period));
					}

					// nalog
					$uslov_nalog = "AND fin.nalog_id IN (".implode(',',$filter_select_nalog_faktura).")";

					// rate
					$uslov_rate = "AND fin.kf_placeno IN (".implode(',',$filter_select_status_rate_faktura).")";

					// rate
					$uslov_firma = "AND nal.nalog_firma_fakturisanja IN (".implode(',',$filter_select_firma_faktura).")";
										
					$query_get_fakture = $db->prepare('
						SELECT fin.*, kan.kandidat_ime, kan.kandidat_prezime, nal.nalog_firma_fakturisanja, nal.nalog_broj, nal.nalog_dospijece,
						CASE WHEN nal.nalog_firma_fakturisanja = 1 THEN kf_iznos * 1.19
            				WHEN nal.nalog_firma_fakturisanja = 2 THEN kf_iznos
       					END AS iznos
						FROM idk_kandidat_financije fin
						JOIN idk_kandidati kan
						ON kan.kandidat_id  = fin.kandidat_id
						JOIN idk_nalozi nal 
						ON fin.nalog_id = nal.nalog_id
						WHERE fin.kf_datum_stvarni IS NOT NULL AND fin.kf_placeno != 0 AND fin.kf_datum_stvarni <= CURRENT_DATE() AND fin.kf_status NOT IN (0,3) 
						AND (
                            fin.kf_type IN (0,1,2)
                            OR
                            (fin.kf_placeno IN (2,3) OR (fin.kf_placeno = 1 AND kan.kandidat_potvrden_pocetak_rada = 1))
                        )
						'.$uslov_vrijeme_kreiranja_fakture.'
						'.$uslov_vrijeme_fakturisanja_fakture.'
						'.$uslov_vrijeme_uplate_fakture.'
						'.$uslov_nalog.'
						'.$uslov_rate.'
						'.$uslov_firma.'
						GROUP BY fin.kf_id');

					$query_get_fakture -> execute();
					
					$brojac = 0;
					?>
					<table id="table_fakture" class="striped col-12">
						<thead>
							<th>#</th>
							<th>Kandidat</th>
							<th>Kompanija</th>
							<th>Nalog</th>
							<th>Firma</th>
							<th>Datum za fakturisanje</th>
							<th>Iznos (EUR)</th>
							<th>Rata</th>
							<th>Status</th>
							<th>Datum fakturisanja</th>
							<th>Datum plaćanja</th>
						</thead>
						<tbody>
					<?php

						$total_uplaceno 		= 0;
						$total_fakturisano 		= 0;
						$total_placeno			= 0;
						$total_fakturisati		= 0;
						
					while($row = $query_get_fakture -> fetch()){
						
						$brojac 				= $brojac + 1;
						$kf_id 					= $row['kf_id'];
						$nalog_id			 	= $row['nalog_id'];
						$firma_fakturisanja		= $row['nalog_firma_fakturisanja'];
						$nalog_broj				= $row['nalog_broj'];
						$nalog_dospijece		= $row['nalog_dospijece'];
						$company_name			= getCompanyNameByNalogId($row['nalog_id']);
						$nalog_name				= getNalogNameById($row['nalog_id']);
						$projekt_id			 	= $row['projekt_id'];
						$kandidat_id		 	= $row['kandidat_id'];
						$kf_datum 				= $row['kf_datum'];
						$kf_datum_stvarni 		= $row['kf_datum_stvarni'];
						$kf_iznos		 		= $row['iznos'];
						$kf_type		 		= $row['kf_type'];
						$kf_nalog_rata_id		= $row['kf_nalog_rata_id'];
						$kf_placeno 			= $row['kf_placeno'];
						$kf_datum_fakturisanja 	= $row['kf_datum_fakturisanja'];
						$kf_datum_placanja 		= $row['kf_datum_placanja'];
						$kf_status 				= $row['kf_status'];
						$kf_zamjena_id 			= $row['kf_zamjena_id'];
						$kandidat_full_name		= $row['kandidat_ime'].' '.$row['kandidat_prezime'];

						$status_text 			= "";
						$vrijednost_text 		= "";
						$pr_uplaceno_text		= "";
						
						$kandidat_ime_ispis		= '<a href = "'.getSiteUrlR().'/kandidati?page=open&id='.$kandidat_id.'" target="_blank">'.$kandidat_full_name.'</a>';
						$nalog_name_ispis		= '<a href = "/nalozi?page=open&id='.$nalog_id.'" target="_blank">'.$nalog_broj." - ".$nalog_name.'</a>';
						
						if($firma_fakturisanja == 1){
							$firma_fakturisanja_text = "DE-Jobstep GmbH";
						}else if($firma_fakturisanja ==2){
							$firma_fakturisanja_text = "CH-Jobstep Int GmbH";
						}else{
							$firma_fakturisanja_text = "-";
						}

						if(strlen($company_name) > 20){
							$company_name_text = substr($company_name, 0, 20)."...";
						}else{
							$company_name_text = $company_name;
						}

						if(strlen($nalog_name) > 20){
							$nalog_name_text = substr($nalog_name, 0, 20)."...";
						}else{
							$nalog_name_text = $nalog_name;
						}
						
						$rata_text 				= $kf_type.'. Rata';

						// NDF - Nista se ni ne prikazuje
						$kf_datum_placanja_novo_dugme = '';
						if($kf_placeno == 0){
							$status_text 		= '<span class="label label-warning material-label material-label_warning main-container__column">Očekivano</span>';
							$kf_datum_kreiranja_text = '';
							$kf_datum_fakturisanja_text = '';
							$kf_datum_fakturisanja_text = '';
						// Treba fakturisati - Prikazuje se datum kreiranja i umjesto datuma fakturisanja ima dugme za fakturisanje ako je vrijeme
						}else if($kf_placeno == 1){

							if(date("Y-m-d") >=  date('Y-m-d', strtotime($kf_datum_stvarni. "+$nalog_dospijece days"))){
								$prosao_rok_fakturisanja = '<i class="fa fa-clock-o" title="Kasnite sa fakturisanjem." style="color:white;cursor:pointer;" aria-hidden="true"></i>';
							}else{
								$prosao_rok_fakturisanja = '';
							}
							$status_text = '<span id="status_type_'.$kf_id.'" class="label label-danger material-label material-label_danger main-container__column">Treba fakturisati '.$prosao_rok_fakturisanja.'</span>';

							$total_fakturisati += $row['iznos'];
							$kf_datum_kreiranja_text = ''. date('d.m.Y', strtotime($kf_datum_stvarni)).'';
							
							if($kf_datum_stvarni <= date("Y-m-d")){
								$kf_datum_fakturisanja_text 		= '
									<div class="akcija d-inline">
										<input type="text" class="form-control datum_fakturisanja_input" name="datum_fakturisanja_input" id="datum_fakturisanja_input'.$kf_id.'" placeholder="Datum" style="padding:17px;border-radius:0;float:left;width:90%; width:110px;">
										<i class="fa fa-check oznaci_kao_fakturisano" 
											kf_id="'.$kf_id.'" 
											kf_iznos="'.$kf_iznos.'"
											kandidat_id="'.$kandidat_id.'"    
											style="color:white;background:#5cb85c;font-size:24px;display:flex;float:left;padding:6px;cursor:pointer;" 
											aria-hidden="true"
										>
										</i>
									</div>
									<div id = "loader_min_'.$kf_id.'" class = "lds-hourglass_min" style="display:none;"></div>
								';	
							}else{
								$kf_datum_fakturisanja_text = '';
							}
							$kf_datum_placanja_text = '';
							// Ovo je hidden ali se prikaze kad se uradi fakturisanje i stavi se u red za placanje
							$kf_datum_placanja_novo_dugme = '
								<div class="akcija d-inline">
									<input type="text" class="form-control datum_placanja_input" name="datum_placanja_input" id="datum_placanja_input'.$kf_id.'" placeholder="Datum" style="padding:17px;border-radius:0;float:left;width:90%; width:110px;">
									<i class="fa fa-check oznaci_kao_placeno" 
										kf_id="'.$kf_id.'"
										kf_iznos="'.$kf_iznos.'"
										kandidat_id="'.$kandidat_id.'"   
										nalog_id="'.$nalog_id.'"   
										style="color:white;background:#5cb85c;font-size:24px;display:flex;float:left;padding:6px;cursor:pointer;" 
										aria-hidden="true"
									>
									</i>
								</div>
								<div id = "loader_min_'.$kf_id.'" class = "lds-hourglass_min" style="display:none;"></div>
							';	
						// Fakturisano - Prikazuje se datum kreiranja i fakturisanja i umjesto datuma placanja ima dugme za placanje
						}else if($kf_placeno == 2){

							if(date("Y-m-d") >=  date('Y-m-d', strtotime($kf_datum_fakturisanja. "+$nalog_dospijece days"))){
								$prosao_rok_placanja = '<i class="fa fa-clock-o" title="Kasnite sa plaćanjem." style="color:white;cursor:pointer;" aria-hidden="true"></i>';
							}else{
								$prosao_rok_placanja = '';
							}

							$status_text 		= '<span id="status_type_'.$kf_id.'" class="label label-primary material-label material-label_primary main-container__column">Fakturisano '.$prosao_rok_placanja.'</span>';
							$total_fakturisano += $row['iznos'];
							$kf_datum_kreiranja_text = ''. date('d.m.Y', strtotime($kf_datum_stvarni)).'';
							$kf_datum_fakturisanja_text = ''. date('d.m.Y', strtotime($kf_datum_fakturisanja)).'';

							$kf_datum_placanja_text 		= '
								<div class="akcija d-inline">
									<input type="text" class="form-control datum_placanja_input" name="datum_placanja_input" id="datum_placanja_input'.$kf_id.'" placeholder="Datum" style="padding:17px;border-radius:0;float:left;width:90%; width:100px;">
									<i class="fa fa-check oznaci_kao_placeno" 
										kf_id="'.$kf_id.'"
										kf_iznos="'.$kf_iznos.'"  
										kandidat_id="'.$kandidat_id.'" 
										nalog_id="'.$nalog_id.'" 
										style="color:white;background:#5cb85c;font-size:24px;display:flex;float:left;padding:6px;cursor:pointer;" 
										aria-hidden="true"
									>
									</i>
								</div>
								<div id = "loader_min_'.$kf_id.'" class = "lds-hourglass_min" style="display:none;"></div>
							';	
						// Placeno - Prikazuje se datum kreiranja, fakturisanja i placanja
						}else if($kf_placeno == 3){
							$status_text 		= '<span class="label label-success material-label material-label_success main-container__column">Plaćeno</span>';
							$total_placeno	   += $row['iznos'];
							$kf_datum_kreiranja_text = ''. date('d.m.Y', strtotime($kf_datum_stvarni)).'';
							$kf_datum_fakturisanja_text = ''. date('d.m.Y', strtotime($kf_datum_fakturisanja)).'';
							$kf_datum_placanja_text = ''. date('d.m.Y', strtotime($kf_datum_placanja)).'';
						// Slucaj ako se nesto pogresi u procesu da se zna da ima i ta faktura
						}else{
							$status_text 		= '<span class="label label-info material-label material-label_info main-container__column">-</span>';
							$total_placeno += $row['iznos'];
							$kf_datum_kreiranja_text = '-';
							$kf_datum_fakturisanja_text = '-';
							$kf_datum_placanja_text = '-';
						}

						
						if($kf_type == 0){
							$rata_text 	= '<span class="label label-secondary material-label material-label_secondary main-container__column">Nostrifikacija</span>';
						}else if($kf_type == 1){
							$rata_text 	= '<span class="label label-secondary material-label material-label_secondary main-container__column">Ugovor</span>';
						}else if($kf_type == 2){
							$rata_text 	= '<span class="label label-secondary material-label material-label_secondary main-container__column">Dobio vizu</span>';
						}else if($kf_type == 3){
							$rata_text 	= '<span class="label label-secondary material-label material-label_secondary main-container__column">Početak rada</span>';
						}else{
							$rata_text 	= '<span class="label label-secondary material-label material-label_secondary main-container__column">'. getMonthsAfterByRateId($kf_nalog_rata_id) .' mj. nakon</span>';
						}

						?>
							<tr id="row_<?php echo $kf_id;?>">
								<td><?php echo $brojac; ?></td>
								<td><?php echo $kandidat_ime_ispis; ?></td>
								<td title="<?php echo $company_name;?>"><?php echo $company_name; ?></td>
								<td title="<?php echo $nalog_name;?>"><?php echo $nalog_name_ispis; ?></td>
								<td><?php echo $firma_fakturisanja_text; ?></td>
								<td data-order = "<?php echo $kf_datum_stvarni; ?>"><?php echo $kf_datum_kreiranja_text; ?></td>
								<td><?php echo number_format((float)$kf_iznos, 2, ',', '.'); ?></td>
								<td><?php echo $rata_text; ?></td>
								<td><?php echo $status_text; ?> </td>
								<td data-order = "<?php echo $kf_datum_fakturisanja; ?>"><div id="novi_datum_fakturisanja_<?php echo $kf_id;?>" style="display: none;"> </div><?php echo $kf_datum_fakturisanja_text; ?></td>
								<td data-order = "<?php echo $kf_datum_placanja; ?>">
									<div id="novi_datum_placanja_<?php echo $kf_id;?>" style="display: none;"> </div>
									<div id="novo_dugme_placanje_<?php echo $kf_id;?>" style="display:none;"><?php echo $kf_datum_placanja_novo_dugme;?></div>
									<?php echo $kf_datum_placanja_text; ?>
								</td>
							</tr>
						<?php
					}
					?>	
						</tbody>
					</table>
					<div class="row" style="text-align:center;">
						<div class="card" data-toggle="tooltip" data-placement="top" title="Potrebno fakturisati">
							<div class="rectangle expense"></div>
							<div>
							  <div class="naslov">Fakturisati</div>
							  <div class="balance">Vrijednost</div>
							  <div class="kurs">€</div>
							  <div class="amount fakturisati_amount" fakturisati_value="<?php echo $total_fakturisati; ?>"><?php echo number_format((float)$total_fakturisati, 2, '.', ','); ?></div>
							  <div class="siluet-1"></div>
							  <div class="siluet-2"></div>
							</div>
						</div>
						<div class="card fakturisano"  data-toggle="tooltip" data-placement="top" title="Fakturisano">
							<div class="rectangle info"></div>
							<div>
							  <div class="naslov">Fakturisano</div>
							  <div class="balance">Vrijednost</div>
							  <div class="kurs">€</div>
							  <div class="amount fakturisano_amount"><?php echo number_format((float)$total_fakturisano, 2, '.', ','); ?></div>
							  <div class="siluet-1"></div>
							  <div class="siluet-2"></div>
							</div>
						</div>
						<div class="card" data-toggle="tooltip" data-placement="top" title="Plaćeno">
							<div class="rectangle income"></div>
							<div>
							  <div class="naslov">Plaćeno</div>
							  <div class="balance">Vrijednost</div>
							  <div class="kurs">€</div>
							  <div class="amount placeno_amount"><?php echo number_format((float)$total_placeno, 2, '.', ',');?></div>
							  <div class="siluet-1"></div>
							  <div class="siluet-2"></div>
							</div>
						</div>
					</div>
					<script>
						$(".datum_fakturisanja_input").flatpickr({
							dateFormat: "d.m.Y",
							disableMobile: "true"
						});
						$('.oznaci_kao_fakturisano').on('click',function(){
							var this_element = $(this);
							this_element.css('pointer-events','none');
							var kf_id 						= $(this).attr('kf_id');
							var kandidat_id 				= $(this).attr('kandidat_id');
							var element_datum_fakturisanja 	= $('#datum_fakturisanja_input'+kf_id);
							var datum_fakturisanja 			= element_datum_fakturisanja.val();
							var flag_greske_unosa 			= false;

							if(!datum_fakturisanja){
								flag_greske_unosa 		= true;
								element_datum_fakturisanja.effect('highlight');
								element_datum_fakturisanja.effect('bounce');
								element_datum_fakturisanja.effect('highlight');
								this_element.css('pointer-events','auto');
							}


							if(!flag_greske_unosa){
								var kf_iznos	 				= parseFloat($(this).attr('kf_iznos').replace(/,/g, ''));
								var fakturisano_amount	 		= parseFloat($('.fakturisano_amount').html().replace(/,/g, ''));
								var fakturisati_amount	 		= parseFloat($('.fakturisati_amount').html().replace(/,/g, ''));
								var new_fakturisati_amount = fakturisati_amount-kf_iznos;
								var new_fakturisano_amount = fakturisano_amount+kf_iznos;
												
								$('.fakturisati_amount').html(parseFloat(new_fakturisati_amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
								$('.fakturisano_amount').html(parseFloat(new_fakturisano_amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));

								this_element.css('pointer-events','none');
								element_datum_fakturisanja.effect('fold',	400, function(){
									$('#loader_min_'+kf_id).fadeIn(500);
									this_element.effect('fold', 300,function(){
										$.ajax({
											url: 'ajax_data.php?page=fakturisi_ratu', 
											type: 'POST',
											data: {'kf_id':kf_id, 'kandidat_id':kandidat_id, 'kf_datum_fakturisanja': datum_fakturisanja},
											dataType: 'html',
											success: function(data) {
												$('#loader_min_'+kf_id).fadeOut(300,function(){
													$('#status_type_'+kf_id).text("Fakturisano");
													$('#status_type_'+kf_id).addClass('material-label_primary').removeClass('material-label_danger');
													$('#row_'+kf_id).css('background','#DAF3FF');
													$('#novi_datum_fakturisanja_'+kf_id).css('display','block');
													$('#novi_datum_fakturisanja_'+kf_id).text(datum_fakturisanja);
													$('#novo_dugme_placanje_'+kf_id).css('display','block');
												});
											},
											error: function (xhr, ajaxOptions, thrownError) {
												$('#loader_min_'+kf_id).fadeOut(300,function(){
													$('#row_'+kf_id).css('background','#ffa8a6');
												});
											}
										});
										
									});
								});
							}
						});

						$(".datum_placanja_input").flatpickr({
							dateFormat: "d.m.Y",
							disableMobile: "true"
						});
						$('.oznaci_kao_placeno').on('click',function(){
							var this_element = $(this);
							this_element.css('pointer-events','none');
							var kf_id 						= $(this).attr('kf_id');
							var kandidat_id 				= $(this).attr('kandidat_id');
							var nalog_id 					= $(this).attr('nalog_id');
							var element_datum_placanja 	= $('#datum_placanja_input'+kf_id);
							var datum_placanja 			= element_datum_placanja.val();
							var flag_greske_unosa 		= false;
							
							if(!datum_placanja){
								flag_greske_unosa 		= true;
								element_datum_placanja.effect('highlight');
								element_datum_placanja.effect('bounce');
								element_datum_placanja.effect('highlight');
								this_element.css('pointer-events','auto');
							}


							if(!flag_greske_unosa){
								var kf_iznos	 				= parseFloat($(this).attr('kf_iznos').replace(/,/g, ''));
								var fakturisano_amount	 		= parseFloat($('.fakturisano_amount').html().replace(/,/g, ''));
								var placeno_amount	 		= parseFloat($('.placeno_amount').html().replace(/,/g, ''));
								var new_placeno_amount = placeno_amount+kf_iznos;
								var new_fakturisano_amount = fakturisano_amount-kf_iznos;
													
								$('.fakturisano_amount').html(parseFloat(new_fakturisano_amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
								$('.placeno_amount').html(parseFloat(new_placeno_amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));

								this_element.css('pointer-events','none');
								element_datum_placanja.effect('fold',	400, function(){
									$('#loader_min_'+kf_id).fadeIn(500);
									this_element.effect('fold', 300,function(){
										$.ajax({
											url: 'ajax_data.php?page=plati_ratu', 
											type: 'POST',
											data: {'kf_id':kf_id, 'kandidat_id':kandidat_id, 'nalog_id': nalog_id, 'kf_datum_placanja': datum_placanja},
											dataType: 'html',
											success: function(data) {
												$('#loader_min_'+kf_id).fadeOut(300,function(){
													$('#status_type_'+kf_id).text("Plaćeno");
													$('#status_type_'+kf_id).addClass('material-label_success').removeClass('material-label_primary');
													$('#row_'+kf_id).css('background','#DAF3FF');
													$('#novi_datum_placanja_'+kf_id).css('display','block');
													$('#novi_datum_placanja_'+kf_id).text(datum_placanja);
													
													
												});

											},
											error: function (xhr, ajaxOptions, thrownError) {
												$('#loader_min_'+kf_id).fadeOut(300,function(){
													$('#row_'+kf_id).css('background','#ffa8a6');
												});
											}
										});
										
									});
								});
							}
						});
					</script>
					<?php

				break;

				case "get_company_orders":
					$companies = $_POST["companies"];
					if(!$companies){
						$companies = [];
					}

					$company_list = implode(',', $companies);

					$select_query = $db->prepare("
						SELECT nalog_id, nalog_naziv, company_id, company_name, nalog_broj
						FROM idk_companies
						JOIN idk_nalozi ON idk_companies.company_id = idk_nalozi.kompanija_id
						WHERE company_status = 1 AND nalog_status NOT IN (8,12) AND company_id IN ($company_list)
						ORDER BY nalog_broj DESC");

					$select_query->execute();

					$rows = $select_query->fetchAll();

					foreach($rows as $row){
						echo '<option selected value="'.$row["nalog_id"].'">'.$row["nalog_broj"]." - ".$row["nalog_naziv"].' ('.$row["company_name"].')</option>';
					}
				break;
				
				case "get_order_castings":
					$orders = $_POST["orders"];
					if(!$orders){
						$orders = [];
					}
					$order_list = implode(',', $orders);

					$select_query = $db->prepare("
						SELECT pap_id, pap_date, pap_city,nalog_naziv
						FROM idk_pp_appointments
						JOIN idk_nalozi ON idk_pp_appointments.pap_nalog_id = idk_nalozi.nalog_id
						JOIN idk_companies ON idk_companies.company_id = idk_nalozi.kompanija_id
						WHERE company_status = 1 AND nalog_status NOT IN (8,12) AND nalog_id IN ($order_list)
						ORDER BY pap_date DESC");

					$select_query->execute();

					$rows = $select_query->fetchAll();

					foreach($rows as $row){
						echo '<option selected value="'.$row["pap_id"].'">'.$row["pap_date"]." - ".$row["pap_city"].' ('.$row["nalog_naziv"].')</option>';
					}
				break;

				case "fakturisi_ratu":
					$kf_id 					= $_POST['kf_id'];
					$kandidat_id 			= $_POST['kandidat_id'];
					$kf_datum_fakturisanja 	= date("Y-m-d", strtotime($_POST['kf_datum_fakturisanja']));

					$query_update_predracun = $db->prepare('
						UPDATE idk_kandidat_financije
						SET kf_placeno = :kf_placeno, kf_datum_fakturisanja = :kf_datum_fakturisanja
						WHERE kf_id  = :kf_id 
					');
					
					$query_update_predracun -> execute(array(
						':kf_id' 			=> $kf_id,
						':kf_placeno' 		=> 2,
						':kf_datum_fakturisanja' 	=> $kf_datum_fakturisanja
					));

					//Add to LOGS
					$log_desc = "Zaposlenik ".getEmployeeFullnameById($logged_employee_id)." fakturisao ratu " .$kf_id. " za kandidata: " .$kandidat_id. " na datum " .$kf_datum_fakturisanja. ".";
					$log_type = "4";
					addToLogs($log_desc, $log_type); //Log za financije - $log_type = 3

				break;

				case "plati_ratu":
					$kf_id 					= $_POST['kf_id'];
					$kandidat_id 			= $_POST['kandidat_id'];
					$nalog_id 				= $_POST['nalog_id'];
					$kf_datum_placanja 		= date("Y-m-d", strtotime($_POST['kf_datum_placanja']));

					if(isMaxRata($kf_id, $kandidat_id)){
						// Ako je placanje != 2 (mjesecno)
						if(checkNalogPaymentType($nalog_id) != 2){
							$status_id = 4;
							$izvor = 1; // CRM
							$novi_projekt_id = getProjectIDForNalogByName("Završen" ,$nalog_id);

							$get_old_project_query = $db->prepare("SELECT  pr.project_id, pk.pk_id
												FROM idk_projects pr 
												JOIN (
													SELECT sqpk.pk_id, sqpk.pk_projectid, sqpk.pk_kandidatid
													FROM idk_project_kandidati sqpk
													WHERE sqpk.pk_kandidatid = :kandidat_id
												) pk
												ON pk.pk_projectid = pr.project_id
												WHERE pr.project_nalogid = :nalog_id
												AND pr.project_name LIKE '%Kandidati počeli sa radom%'");
							$get_old_project_query->execute(array(
								':kandidat_id'	=>	$kandidat_id,
								':nalog_id'		=>	$nalog_id
							));								
							$get_old_project = $get_old_project_query->fetch();		

							$old_project_id = $get_old_project["project_id"];
							$pk_id 		= $get_old_project["pk_id"];

							$delete_old_project = $db->prepare("
												DELETE FROM idk_project_kandidati
												WHERE pk_id=:pk_id
												");
							$delete_old_project->execute(array(
								':pk_id'	=> $pk_id
							));							
							
							$add_new_project = $db->prepare("
									INSERT INTO idk_project_kandidati
										(pk_projectid, pk_kandidatid)
									VALUES
										(:pk_projectid, :pk_kandidatid)");		
							$add_new_project->execute(array(
								':pk_projectid'		=>	$novi_projekt_id,
								':pk_kandidatid'	=>	$kandidat_id
							));

							updateKandidatStatusPrijave($kandidat_id, $status_id);

							addToLogsStatusPrijave(NULL, $novi_projekt_id, $status_id, $kandidat_id, $izvor);
							$log_desc3 = "Zaposlenik prebacio kandidata: ".$kandidat_id." iz projekta: ".$old_project_id." u ".$novi_projekt_id.".";
							addToLogs($log_desc3, 0);
						}
					}
					
					$query_update_predracun = $db->prepare('
						UPDATE idk_kandidat_financije
						SET kf_placeno = :kf_placeno, kf_datum_placanja = :kf_datum_placanja
						WHERE kf_id  = :kf_id 
					');
					
					$query_update_predracun -> execute(array(
						':kf_id' 			=> $kf_id,
						':kf_placeno' 		=> 3,
						':kf_datum_placanja' 	=> $kf_datum_placanja
					));

					//Add to LOGS
					$log_desc = "Zaposlenik ".getEmployeeFullnameById($logged_employee_id)." naplatio ratu " .$kf_id. " za kandidata: " .$kandidat_id. " na datum " .$kf_datum_placanja. ".";
					$log_type = "4";
					addToLogs($log_desc, $log_type); //Log za financije - $log_type = 3

				break;

				case "get_financije_fakture_naloga":

					$filter_kreirana_faktura_period 	= $_REQUEST['filter_kreirana_faktura_period'];
					$filter_fakturisana_faktura_period 	= $_REQUEST['filter_fakturisana_faktura_period'];
					$filter_uplacena_faktura_period 	= $_REQUEST['filter_uplacena_faktura_period'];
					$filter_select_nalog_faktura		= $_REQUEST['filter_select_nalog_faktura'];
					$filter_select_status_rate_faktura 	= $_REQUEST['filter_select_status_rate_faktura'];
					$filter_select_firma_faktura	 	= $_REQUEST['filter_select_firma_faktura'];

					$uslov_vrijeme_kreiranja_fakture 	= "";
					$uslov_vrijeme_fakturisanja_fakture = "";
					$uslov_vrijeme_uplate_fakture 		= "";
					$uslov_nalog 						= "";
					$uslov_rate							= "";
					$uslov_firma						= "";
					
					// datum kreiranja
					if(strpos($filter_kreirana_faktura_period, "to")){
						$tmp_period = array();
						$tmp_period = explode('to', $filter_kreirana_faktura_period);
						$filter_kreirana_faktura_datum_od = date("Y-m-d", strtotime($tmp_period[0]));
						$filter_kreirana_faktura_datum_do = date("Y-m-d", strtotime($tmp_period[1]));
						$uslov_vrijeme_kreiranja_fakture = " AND fin.nf_datum_aktiviranja BETWEEN '".$filter_kreirana_faktura_datum_od."' AND '".$filter_kreirana_faktura_datum_do."'";
					}
					else if($filter_kreirana_faktura_period != ""){
						$uslov_vrijeme_kreiranja_fakture = " AND fin.nf_datum_aktiviranja = ".date("d.m.Y",strtotime($filter_kreirana_faktura_period));
					}
					
					// datum fakturisanja
					if(strpos($filter_fakturisana_faktura_period, "to")){
						$tmp_period = array();
						$tmp_period = explode('to', $filter_fakturisana_faktura_period);
						$filter_fakturisana_faktura_datum_od = date("Y-m-d", strtotime($tmp_period[0]));
						$filter_fakturisana_faktura_datum_do = date("Y-m-d", strtotime($tmp_period[1]));
						$uslov_vrijeme_fakturisanja_fakture = " AND fin.nf_datum_fakturisanja BETWEEN '".$filter_fakturisana_faktura_datum_od."' AND '".$filter_fakturisana_faktura_datum_do."'";
					}
					else if($filter_fakturisana_faktura_period != ""){
						$uslov_vrijeme_fakturisanja_fakture = " AND fin.nf_datum_fakturisanja = ".date("d.m.Y",strtotime($filter_fakturisana_faktura_period));
					}

					// datum uplate
					if(strpos($filter_uplacena_faktura_period, "to")){
						$tmp_period = array();
						$tmp_period = explode('to', $filter_uplacena_faktura_period);
						$filter_uplacena_faktura_datum_od = date("Y-m-d", strtotime($tmp_period[0]));
						$filter_uplacena_faktura_datum_do = date("Y-m-d", strtotime($tmp_period[1]));
						$uslov_vrijeme_uplate_fakture = " AND fin.nf_datum_placanja BETWEEN '".$filter_uplacena_faktura_datum_od."' AND '".$filter_uplacena_faktura_datum_do."'";
					}
					else if($filter_uplacena_faktura_period != ""){
						$uslov_vrijeme_uplate_fakture = " AND fin.nf_datum_placanja = ".date("d.m.Y",strtotime($filter_uplacena_faktura_period));
					}

					// nalog
					$uslov_nalog = "AND fin.nf_nalog_id IN (".implode(',',$filter_select_nalog_faktura).")";

					// rate
					$uslov_rate = "AND fin.nf_placeno IN (".implode(',',$filter_select_status_rate_faktura).")";

					// rate
					$uslov_firma = "AND nal.nalog_firma_fakturisanja IN (".implode(',',$filter_select_firma_faktura).")";
										
					$query_get_fakture = $db->prepare('
						SELECT fin.*, nal.nalog_firma_fakturisanja, nal.nalog_broj, nal.nalog_dospijece,
						CASE WHEN nal.nalog_firma_fakturisanja = 1 THEN nf_iznos * 1.19
            				WHEN nal.nalog_firma_fakturisanja = 2 THEN nf_iznos
       					END AS iznos
						FROM idk_nalog_financije fin
						JOIN idk_nalozi nal 
						ON fin.nf_nalog_id = nal.nalog_id
						WHERE fin.nf_datum_aktiviranja IS NOT NULL AND fin.nf_placeno != 0 AND fin.nf_datum_aktiviranja <= CURRENT_DATE()
						'.$uslov_vrijeme_kreiranja_fakture.'
						'.$uslov_vrijeme_fakturisanja_fakture.'
						'.$uslov_vrijeme_uplate_fakture.'
						'.$uslov_nalog.'
						'.$uslov_rate.'
						'.$uslov_firma.'
						GROUP BY fin.nf_id');

					$query_get_fakture -> execute();
					
					$brojac = 0;
					?>
					<table id="table_fakture" class="striped col-12">
						<thead>
							<th>#</th>
							<th>Kompanija</th>
							<th>Nalog</th>
							<th>Firma</th>
							<th>Datum za fakturisanje</th>
							<th>Iznos (EUR)</th>
							<th>Rata</th>
							<th>Status</th>
							<th>Datum fakturisanja</th>
							<th>Datum plaćanja</th>
						</thead>
						<tbody>
					<?php

						$total_uplaceno 		= 0;
						$total_fakturisano 		= 0;
						$total_placeno			= 0;
						$total_fakturisati		= 0;

					while($row = $query_get_fakture -> fetch()){
						
						$brojac 				= $brojac + 1;
						$nf_id 					= $row['nf_id'];
						$nf_nalog_id			= $row['nf_nalog_id'];
						$firma_fakturisanja		= $row['nalog_firma_fakturisanja'];
						$nalog_broj		= $row['nalog_broj'];
						$company_name			= getCompanyNameByNalogId($row['nf_nalog_id']);
						$nalog_name				= getNalogNameById($row['nf_nalog_id']);
						$nf_datum_aktiviranja	= $row['nf_datum_aktiviranja'];
						$nf_type		 		= $row['nf_type'];
						$nf_broj_rate 			= $row['nf_broj_rate'];
						$nf_iznos 				= $row['iznos'];
						$nf_placeno		 		= $row['nf_placeno'];
						$nf_datum_fakturisanja	= $row['nf_datum_fakturisanja'];
						$nf_datum_placanja		= $row['nf_datum_placanja'];
						$nf_desc				= $row['nf_desc'];
						$nalog_dospijece		= $row['nalog_dospijece'];

						$status_text 			= "";
						$vrijednost_text 		= "";
						$pr_uplaceno_text		= "";

						if($nf_desc != NULL){
							$nalog_name_ispis = '<a href = "/nalozi?page=open&id='.$nf_nalog_id.'" target="_blank">'.$nf_desc.' - '.$nalog_broj.' - '.$nalog_name.'</a>';
						}else{
							$nalog_name_ispis = '<a href = "/nalozi?page=open&id='.$nf_nalog_id.'" target="_blank">'.$nalog_broj.' - '.$nalog_name.'</a>';
						}
												
						if($firma_fakturisanja == 1){
							$firma_fakturisanja_text = "DE-Jobstep GmbH";
						}else if($firma_fakturisanja ==2){
							$firma_fakturisanja_text = "CH-Jobstep Int GmbH";
						}else{
							$firma_fakturisanja_text = "-";
						}

						if(strlen($company_name) > 20){
							$company_name_text = substr($company_name, 0, 20)."...";
						}else{
							$company_name_text = $company_name;
						}

						if(strlen($nalog_name) > 20){
							$nalog_name_text = substr($nalog_name, 0, 20)."...";
						}else{
							$nalog_name_text = $nalog_name;
						}

						// NDF - Nista se ni ne prikazuje
						$nf_datum_placanja_novo_dugme = '';
						if($nf_placeno == 0){
							$status_text 		= '<span class="label label-warning material-label material-label_warning main-container__column">Očekivano</span>';
							$nf_datum_kreiranja_text = '';
							$nf_datum_fakturisanja_text = '';
							$nf_datum_placanja_text = '';
						// Treba fakturisati - Prikazuje se datum kreiranja i umjesto datuma fakturisanja ima dugme za fakturisanje ako je vrijeme
						}else if($nf_placeno == 1){
							if(date("Y-m-d") >=  date('Y-m-d', strtotime($nf_datum_aktiviranja. "+$nalog_dospijece days"))){
								$prosao_rok_fakturisanja = '<i class="fa fa-clock-o" title="Kasnite sa fakturisanjem." style="color:white;cursor:pointer;" aria-hidden="true"></i>';
							}else{
								$prosao_rok_fakturisanja = '';
							}
							$status_text 		= '<span id="status_type_'.$nf_id.'" class="label label-danger material-label material-label_danger main-container__column">Treba fakturisati '.$prosao_rok_fakturisanja.'</span>';
							$total_fakturisati += $row['iznos'];
							$nf_datum_kreiranja_text = ''. date('d.m.Y', strtotime($nf_datum_aktiviranja)).'';
							
							if($nf_datum_aktiviranja <= date("Y-m-d")){
								$nf_datum_fakturisanja_text 		= '
									<div class="akcija d-inline">
										<input type="text" class="form-control datum_fakturisanja_input" name="datum_fakturisanja_input" id="datum_fakturisanja_input'.$nf_id.'" placeholder="Datum" style="padding:17px;border-radius:0;float:left;width:90%; width:110px;">
										<i class="fa fa-check oznaci_kao_fakturisano" 
											nf_id="'.$nf_id.'"
											nf_iznos="'.$nf_iznos.'" 
											nf_nalog_id="'.$nf_nalog_id.'" 
											style="color:white;background:#5cb85c;font-size:24px;display:flex;float:left;padding:6px;cursor:pointer;" 
											aria-hidden="true"
										>
										</i>
									</div>
									<div id = "loader_min_'.$nf_id.'" class = "lds-hourglass_min" style="display:none;"></div>
								';	
							}else{
								$nf_datum_fakturisanja_text = '';
							}
							$nf_datum_placanja_text = '';
							// Ovo je hidden ali se prikaze kad se uradi fakturisanje i stavi se u red za placanje
							$nf_datum_placanja_novo_dugme = '
								<div class="akcija d-inline">
									<input type="text" class="form-control datum_placanja_input" name="datum_placanja_input" id="datum_placanja_input'.$nf_id.'" placeholder="Datum" style="padding:17px;border-radius:0;float:left;width:90%; width:110px;">
									<i class="fa fa-check oznaci_kao_placeno" 
										nf_id="'.$nf_id.'"
										nf_iznos="'.$nf_iznos.'"  
										nf_nalog_id="'.$nf_nalog_id.'"  
										style="color:white;background:#5cb85c;font-size:24px;display:flex;float:left;padding:6px;cursor:pointer;" 
										aria-hidden="true"
									>
									</i>
								</div>
								<div id = "loader_min_'.$nf_id.'" class = "lds-hourglass_min" style="display:none;"></div>
							';	
						// Fakturisano - Prikazuje se datum kreiranja i fakturisanja i umjesto datuma placanja ima dugme za placanje
						}else if($nf_placeno == 2){
							if(date("Y-m-d") >=  date('Y-m-d', strtotime($nf_datum_fakturisanja. "+$nalog_dospijece days"))){
								$prosao_rok_placanja = '<i class="fa fa-clock-o" title="Kasnite sa plaćanjem." style="color:white;cursor:pointer;" aria-hidden="true"></i>';
							}else{
								$prosao_rok_placanja = '';
							}
							$status_text 		= '<span id="status_type_'.$nf_id.'" class="label label-primary material-label material-label_primary main-container__column">Fakturisano '.$prosao_rok_placanja.'</span>';
							$total_fakturisano += $row['iznos'];
							$nf_datum_kreiranja_text = ''. date('d.m.Y', strtotime($nf_datum_aktiviranja)).'';
							$nf_datum_fakturisanja_text = ''. date('d.m.Y', strtotime($nf_datum_fakturisanja)).'';

							$nf_datum_placanja_text 		= '
								<div class="akcija d-inline">
									<input type="text" class="form-control datum_placanja_input" name="datum_placanja_input" id="datum_placanja_input'.$nf_id.'" placeholder="Datum" style="padding:17px;border-radius:0;float:left;width:90%; width:110px;">
									<i class="fa fa-check oznaci_kao_placeno" 
										nf_id="'.$nf_id.'"
										nf_iznos="'.$nf_iznos.'"  
										nf_nalog_id="'.$nf_nalog_id.'"  
										style="color:white;background:#5cb85c;font-size:24px;display:flex;float:left;padding:6px;cursor:pointer;" 
										aria-hidden="true"
									>
									</i>
								</div>
								<div id = "loader_min_'.$nf_id.'" class = "lds-hourglass_min" style="display:none;"></div>
							';	
						// Placeno - Prikazuje se datum kreiranja, fakturisanja i placanja
						}else if($nf_placeno == 3){
							$status_text 		= '<span class="label label-success material-label material-label_success main-container__column">Plaćeno</span>';
							$total_placeno	   += $row['iznos'];
							$nf_datum_kreiranja_text = ''. date('d.m.Y', strtotime($nf_datum_aktiviranja)).'';
							$nf_datum_fakturisanja_text = ''. date('d.m.Y', strtotime($nf_datum_fakturisanja)).'';
							$nf_datum_placanja_text = ''. date('d.m.Y', strtotime($nf_datum_placanja)).'';
						// Slucaj ako se nesto pogresi u procesu da se zna da ima i ta faktura
						}else{
							$status_text 		= '<span class="label label-info material-label material-label_info main-container__column">-</span>';
							$total_placeno += $row['iznos'];
							$nf_datum_kreiranja_text = '-';
							$nf_datum_fakturisanja_text = '-';
							$nf_datum_placanja_text = '-';
						}

						
						if($nf_type == 1){
							$rata_text 	= '<span class="label label-secondary material-label material-label_secondary main-container__column">Avans (standardni)</span>';
						}else if($nf_type == 2){
							$rata_text 	= '<span class="label label-secondary material-label material-label_secondary main-container__column">Avans (mjesečni) '.$nf_desc.'</span>';
						}else if($nf_type == 3){
							$rata_text 	= '<span class="label label-secondary material-label material-label_secondary main-container__column">Mjesečna rata ('.$nf_broj_rate.')</span>';
						}else if($nf_type == 4){
							$rata_text 	= '<span class="label label-secondary material-label material-label_secondary main-container__column">Nostrifikacija ('.$nf_broj_rate.')</span>';
						}

						?>
							<tr id="row_<?php echo $nf_id;?>">
								<td><?php echo $brojac; ?></td>
								<td title="<?php echo $company_name;?>"><?php echo $company_name; ?></td>
								<td title="<?php echo $nalog_name;?>"><?php echo $nalog_name_ispis; ?></td>
								<td><?php echo $firma_fakturisanja_text; ?></td>
								<td data-order = "<?php echo $nf_datum_aktiviranja; ?>"><?php echo $nf_datum_kreiranja_text; ?></td>
								<td><?php echo number_format((float)$nf_iznos, 2, '.', ','); ?></td>
								<td><?php echo $rata_text; ?></td>
								<td><?php echo $status_text; ?> </td>
								<td data-order = "<?php echo $nf_datum_fakturisanja; ?>"><div id="novi_datum_fakturisanja_<?php echo $nf_id;?>" style="display: none;"> </div><?php echo $nf_datum_fakturisanja_text; ?></td>
								<td data-order = "<?php echo $nf_datum_placanja; ?>">
									<div id="novi_datum_placanja_<?php echo $nf_id;?>" style="display: none;"> </div>
									<div id="novo_dugme_placanje_<?php echo $nf_id;?>" style="display:none;"> <?php echo $nf_datum_placanja_novo_dugme;?> </div>
									<div><?php echo $nf_datum_placanja_text; ?></div>
								</td>
							</tr>
						<?php
					}
					?>	
						</tbody>
					</table>
					<div class="row" style="text-align:center;">
						<div class="card" data-toggle="tooltip" data-placement="top" title="Potrebno fakturisati">
							<div class="rectangle expense"></div>
							<div>
							  <div class="naslov">Fakturisati</div>
							  <div class="balance">Vrijednost</div>
							  <div class="kurs">€</div>
							  <div class="amount fakturisati_amount"><?php echo number_format((float)$total_fakturisati, 2, '.', ','); ?></div>
							  <div class="siluet-1"></div>
							  <div class="siluet-2"></div>
							</div>
						</div>
						<div class="card"  data-toggle="tooltip" data-placement="top" title="Fakturisano">
							<div class="rectangle info"></div>
							<div>
							  <div class="naslov">Fakturisano</div>
							  <div class="balance">Vrijednost</div>
							  <div class="kurs">€</div>
							  <div class="amount fakturisano_amount"><?php echo number_format((float)$total_fakturisano, 2, '.', ','); ?></div>
							  <div class="siluet-1"></div>
							  <div class="siluet-2"></div>
							</div>
						</div>
						<div class="card" data-toggle="tooltip" data-placement="top" title="Plaćeno">
							<div class="rectangle income"></div>
							<div>
							  <div class="naslov">Plaćeno</div>
							  <div class="balance">Vrijednost</div>
							  <div class="kurs">€</div>
							  <div class="amount placeno_amount"><?php echo number_format((float)$total_placeno, 2, '.', ',');?></div>
							  <div class="siluet-1"></div>
							  <div class="siluet-2"></div>
							</div>
						</div>
					</div>
					<script>
						$(".datum_fakturisanja_input").flatpickr({
							dateFormat: "d.m.Y",
							disableMobile: "true"
						});
						$('.oznaci_kao_fakturisano').on('click',function(){
							var this_element = $(this);
							this_element.css('pointer-events','none');
							var nf_id 						= $(this).attr('nf_id');
							var nf_nalog_id 				= $(this).attr('nf_nalog_id');
							var element_datum_fakturisanja 	= $('#datum_fakturisanja_input'+nf_id);
							var datum_fakturisanja 			= element_datum_fakturisanja.val();
							var flag_greske_unosa 			= false;
							
							if(!datum_fakturisanja){
								flag_greske_unosa 		= true;
								element_datum_fakturisanja.effect('highlight');
								element_datum_fakturisanja.effect('bounce');
								element_datum_fakturisanja.effect('highlight');
								this_element.css('pointer-events','auto');
							}


							if(!flag_greske_unosa){
								var nf_iznos	 				= parseFloat($(this).attr('nf_iznos').replace(/,/g, ''));
								var fakturisano_amount	 		= parseFloat($('.fakturisano_amount').html().replace(/,/g, ''));
								var fakturisati_amount	 		= parseFloat($('.fakturisati_amount').html().replace(/,/g, ''));
								var new_fakturisati_amount = fakturisati_amount-nf_iznos;
								var new_fakturisano_amount = fakturisano_amount+nf_iznos;

								$('.fakturisati_amount').html(parseFloat(new_fakturisati_amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
								$('.fakturisano_amount').html(parseFloat(new_fakturisano_amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
								
								this_element.css('pointer-events','none');
								element_datum_fakturisanja.effect('fold',	400, function(){
									$('#loader_min_'+nf_id).fadeIn(500);
									this_element.effect('fold', 300,function(){
										$.ajax({
											url: 'ajax_data.php?page=fakturisi_ratu_nalog', 
											type: 'POST',
											data: {'nf_id':nf_id, 'nf_nalog_id': nf_nalog_id, 'nf_datum_fakturisanja': datum_fakturisanja},
											dataType: 'html',
											success: function(data) {
												$('#loader_min_'+nf_id).fadeOut(300,function(){
													$('#status_type_'+nf_id).text("Fakturisano");
													$('#status_type_'+nf_id).addClass('material-label_primary').removeClass('material-label_danger');
													$('#row_'+nf_id).css('background','#DAF3FF');
													$('#novi_datum_fakturisanja_'+nf_id).css('display','block');
													$('#novi_datum_fakturisanja_'+nf_id).text(datum_fakturisanja);
													$('#novo_dugme_placanje_'+nf_id).css('display','block');
												});

											},
											error: function (xhr, ajaxOptions, thrownError) {
												$('#loader_min_'+nf_id).fadeOut(300,function(){
													$('#row_'+nf_id).css('background','#ffa8a6');
												});
											}
										});
										
									});
								});
							}
						});

						$(".datum_placanja_input").flatpickr({
							dateFormat: "d.m.Y",
							disableMobile: "true"
						});
						$('.oznaci_kao_placeno').on('click',function(){
							var this_element = $(this);
							this_element.css('pointer-events','none');
							var nf_id 						= $(this).attr('nf_id');
							var nf_nalog_id 				= $(this).attr('nf_nalog_id');
							var element_datum_placanja 	= $('#datum_placanja_input'+nf_id);
							var datum_placanja 			= element_datum_placanja.val();
							var flag_greske_unosa 		= false;
							
							if(!datum_placanja){
								flag_greske_unosa 		= true;
								element_datum_placanja.effect('highlight');
								element_datum_placanja.effect('bounce');
								element_datum_placanja.effect('highlight');
								this_element.css('pointer-events','auto');
							}


							if(!flag_greske_unosa){
								var nf_iznos	 				= parseFloat($(this).attr('nf_iznos').replace(/,/g, ''));
								var fakturisati_amount	 		= parseFloat($('.fakturisati_amount').html().replace(/,/g, ''));
								var fakturisano_amount	 		= parseFloat($('.fakturisano_amount').html().replace(/,/g, ''));
								var placeno_amount	 		= parseFloat($('.placeno_amount').html().replace(/,/g, ''));
								var new_placeno_amount = placeno_amount+nf_iznos;
								var new_fakturisano_amount = fakturisano_amount-nf_iznos;
													
								$('.fakturisano_amount').html(parseFloat(new_fakturisano_amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
								$('.placeno_amount').html(parseFloat(new_placeno_amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));

								this_element.css('pointer-events','none');
								element_datum_placanja.effect('fold',	400, function(){
									$('#loader_min_'+nf_id).fadeIn(500);
									this_element.effect('fold', 300,function(){
										$.ajax({
											url: 'ajax_data.php?page=plati_ratu_nalog', 
											type: 'POST',
											data: {'nf_id':nf_id, 'nf_nalog_id': nf_nalog_id, 'nf_datum_placanja': datum_placanja},
											dataType: 'html',
											success: function(data) {
												$('#loader_min_'+nf_id).fadeOut(300,function(){
													$('#status_type_'+nf_id).text("Plaćeno");
													$('#status_type_'+nf_id).addClass('material-label_success').removeClass('material-label_primary');
													$('#row_'+nf_id).css('background','#DAF3FF');
													$('#novi_datum_placanja_'+nf_id).css('display','block');
													$('#novi_datum_placanja_'+nf_id).text(datum_placanja);
												});

											},
											error: function (xhr, ajaxOptions, thrownError) {
												$('#loader_min_'+nf_id).fadeOut(300,function(){
													$('#row_'+nf_id).css('background','#ffa8a6');
												});
											}
										});
										
									});
								});
							}
						});
					</script>
					<?php

				break;

				case "fakturisi_ratu_nalog":
					$nf_id 					= $_POST['nf_id'];
					$nf_nalog_id 			= $_POST['nf_nalog_id'];
					$nf_datum_fakturisanja 	= date("Y-m-d", strtotime($_POST['nf_datum_fakturisanja']));

					$query_update_predracun = $db->prepare('
						UPDATE idk_nalog_financije
						SET nf_placeno = :nf_placeno, nf_datum_fakturisanja = :nf_datum_fakturisanja
						WHERE nf_id  = :nf_id 
					');
					
					$query_update_predracun -> execute(array(
						':nf_id' 			=> $nf_id,
						':nf_placeno' 		=> 2,
						':nf_datum_fakturisanja' 	=> $nf_datum_fakturisanja
					));

					//Add to LOGS
					$log_desc = "Zaposlenik ".getEmployeeFullnameById($logged_employee_id)." fakturisao ratu " .$nf_id. " za nalog: " .$nf_nalog_id. " na datum " .$nf_datum_fakturisanja. ".";
					$log_type = "4";
					addToLogs($log_desc, $log_type); //Log za financije - $log_type = 3

				break;

				case "plati_ratu_nalog":
					$nf_id 					= $_POST['nf_id'];
					$nf_nalog_id 			= $_POST['nf_nalog_id'];
					$nf_datum_placanja 	= date("Y-m-d", strtotime($_POST['nf_datum_placanja']));

					$query_update_predracun = $db->prepare('
						UPDATE idk_nalog_financije
						SET nf_placeno = :nf_placeno, nf_datum_placanja = :nf_datum_placanja
						WHERE nf_id  = :nf_id 
					');
					
					$query_update_predracun -> execute(array(
						':nf_id' 			=> $nf_id,
						':nf_placeno' 		=> 3,
						':nf_datum_placanja' 	=> $nf_datum_placanja
					));

					//Add to LOGS
					$log_desc = "Zaposlenik ".getEmployeeFullnameById($logged_employee_id)." naplatio ratu " .$nf_id. " za nalog: " .$nf_nalog_id. " na datum " .$nf_datum_placanja. ".";
					$log_type = "4";
					addToLogs($log_desc, $log_type); //Log za financije - $log_type = 3

				break;

				case "get_financije_projekcija":

					$filter_kreirana_faktura_start 		= date("Y-m-d", strtotime($_REQUEST['filter_kreirana_faktura_start']));
					$filter_kreirana_faktura_end 		= date("Y-m-d", strtotime($_REQUEST['filter_kreirana_faktura_end']));
					$filter_select_kompanija_faktura	= $_REQUEST['filter_select_kompanija_faktura'];
					$filter_select_status_rate_faktura 	= $_REQUEST['filter_select_status_rate_faktura'];
					$filter_select_firma_faktura	 	= $_REQUEST['filter_select_firma_faktura'];

					$uslov_vrijeme_kreiranja_fakture 	= "";
					$uslov_kompanija					= "";
					$uslov_rate_kandidat				= "";
					$uslov_rate_nalog					= "";
					$uslov_firma						= "";
					

					if(date('Y-m') == date('Y-m', strtotime($filter_kreirana_faktura_start))){
						$uslov_vrijeme_kreiranja_fakture_kandidati = " AND ( CASE WHEN kf_placeno IN (0, 1) THEN DATE_ADD(kf_datum_stvarni, INTERVAL idk_nalozi.nalog_dospijece DAY) WHEN kf_placeno = 2 THEN DATE_ADD(kf_datum_fakturisanja, INTERVAL idk_nalozi.nalog_dospijece DAY) END ) <= '".$filter_kreirana_faktura_end."'";
						$uslov_vrijeme_kreiranja_fakture_nalozi = " AND ( CASE WHEN nf_placeno IN (0, 1) THEN DATE_ADD(nf_datum_aktiviranja, INTERVAL idk_nalozi.nalog_dospijece DAY) WHEN nf_placeno = 2 THEN DATE_ADD(nf_datum_fakturisanja, INTERVAL idk_nalozi.nalog_dospijece DAY) END ) <= '".$filter_kreirana_faktura_end."'";
					}else{
						$uslov_vrijeme_kreiranja_fakture_kandidati = " AND ( CASE WHEN kf_placeno IN (0, 1) THEN DATE_ADD(kf_datum_stvarni, INTERVAL idk_nalozi.nalog_dospijece DAY) WHEN kf_placeno = 2 THEN DATE_ADD(kf_datum_fakturisanja, INTERVAL idk_nalozi.nalog_dospijece DAY) END ) BETWEEN '".$filter_kreirana_faktura_start."' AND '".$filter_kreirana_faktura_end."'";
						$uslov_vrijeme_kreiranja_fakture_nalozi = " AND ( CASE WHEN nf_placeno IN (0, 1) THEN DATE_ADD(nf_datum_aktiviranja, INTERVAL idk_nalozi.nalog_dospijece DAY) WHEN nf_placeno = 2 THEN DATE_ADD(nf_datum_fakturisanja, INTERVAL idk_nalozi.nalog_dospijece DAY) END ) BETWEEN '".$filter_kreirana_faktura_start."' AND '".$filter_kreirana_faktura_end."'";
					}
					


					// nalog
					$uslov_kompanija = "AND idk_companies.company_id = $filter_select_kompanija_faktura";

					// firma
					$uslov_firma = "AND idk_nalozi.nalog_firma_fakturisanja IN (".implode(',',$filter_select_firma_faktura).")";
					
					// firma
					$uslov_rate_kandidat = " kf_placeno IN (".implode(',',$filter_select_status_rate_faktura).")";
					$uslov_rate_nalog = " nf_placeno IN (".implode(',',$filter_select_status_rate_faktura).")";

					$query_get_fakture = $db->prepare('
										SELECT company_id, company_name, kandidat, id_rate, status_rate, id_naloga, formatted_month, nalog_firma_fakturisanja, iznos, tip_rate, vrsta_querija, broj_rate_nalog, desc_rate, broj_rate_kandidat, nalog_broj, nalog_dospijece
										FROM (
											(SELECT company_id, idk_companies.company_name, "" AS kandidat, nf_id AS id_rate, nf_placeno as status_rate, idk_nalog_financije.nf_nalog_id as id_naloga, nalog_firma_fakturisanja, nalog_broj, nalog_dospijece,
											CASE 
												WHEN nf_placeno IN (0, 1) THEN DATE_ADD(nf_datum_aktiviranja, INTERVAL idk_nalozi.nalog_dospijece DAY)
												WHEN nf_placeno = 2 THEN DATE_ADD(nf_datum_fakturisanja, INTERVAL idk_nalozi.nalog_dospijece DAY)
											END 
											AS formatted_month, 
											CASE WHEN idk_nalozi.nalog_firma_fakturisanja = 1 THEN nf_iznos * 1.19
												WHEN idk_nalozi.nalog_firma_fakturisanja = 2 THEN nf_iznos
												ELSE nf_iznos
       										END AS iznos,
											nf_type AS tip_rate, 1 AS vrsta_querija, nf_broj_rate AS broj_rate_nalog, nf_desc AS desc_rate, "" AS broj_rate_kandidat
											FROM idk_nalog_financije
											INNER JOIN idk_nalozi ON idk_nalog_financije.nf_nalog_id = idk_nalozi.nalog_id
											INNER JOIN idk_companies ON idk_nalozi.kompanija_id = idk_companies.company_id
											WHERE
											'.$uslov_rate_nalog.'
											'.$uslov_vrijeme_kreiranja_fakture_nalozi.'
											'.$uslov_kompanija.'
											'.$uslov_firma.')
											UNION
											(SELECT company_id, idk_companies.company_name, idk_kandidat_financije.kandidat_id AS kandidat, kf_id AS id_rate, kf_placeno as status_rate, idk_kandidat_financije.nalog_id as id_naloga, nalog_firma_fakturisanja, nalog_broj, nalog_dospijece,
											CASE 
												WHEN kf_placeno IN (0, 1) THEN DATE_ADD(kf_datum_stvarni, INTERVAL idk_nalozi.nalog_dospijece DAY)
												WHEN kf_placeno = 2 THEN DATE_ADD(kf_datum_fakturisanja, INTERVAL idk_nalozi.nalog_dospijece DAY)
											END 
											AS formatted_month, 
											CASE WHEN idk_nalozi.nalog_firma_fakturisanja = 1 THEN kf_iznos * 1.19
												WHEN idk_nalozi.nalog_firma_fakturisanja = 2 THEN kf_iznos
												ELSE kf_iznos
       										END AS iznos,
											kf_type as tip_rate, 2 AS vrsta_querija, "" AS broj_rate_nalog, "" AS desc_rate, kf_nalog_rata_id AS broj_rate_kandidat
											FROM idk_kandidat_financije
											INNER JOIN idk_nalozi ON idk_kandidat_financije.nalog_id = idk_nalozi.nalog_id
											INNER JOIN idk_companies ON idk_nalozi.kompanija_id = idk_companies.company_id
											WHERE
											'.$uslov_rate_kandidat.'
											'.$uslov_vrijeme_kreiranja_fakture_kandidati.'
											'.$uslov_kompanija.'
											'.$uslov_firma.' AND idk_kandidat_financije.kf_status NOT IN (0,3))
										) AS subquery'
									);
									
					$query_get_fakture -> execute();
					
					$brojac = 0;
					?>
					<table id="table_fakture" class="striped col-12">
						<thead>
							<th>#</th>
							<th>Kompanija</th>
							<th>Nalog</th>
							<th>Kandidat</th>
							<th>Firma</th>
							<th class="text-center">Status</th>
							<th class="text-center">Tip rate</th>
							<th class="text-center">Očekivani datum uplate</th>
							<th class="text-right">Iznos (EUR)</th>
						</thead>
						<tbody>
					<?php

						$total_ndf		 		= 0;
						$total_fakturisati 		= 0;
						$total_fakturisano 		= 0;
						$total					= 0;

					while($row = $query_get_fakture -> fetch()){
						
						$brojac 				= $brojac + 1;
						$id_rate				= $row['id_rate'];
						$company_id				= $row['company_id'];
						$nalog_id			 	= $row['id_naloga'];
						$nalog_broj			 	= $row['nalog_broj'];
						$firma_fakturisanja		= $row['nalog_firma_fakturisanja'];
						$company_name			= $row['company_name'];
						$kandidat				= $row['kandidat'];
						$nalog_name				= getNalogNameById($row['id_naloga']);
						$datum 					= $row['formatted_month'];
						$iznos		 			= $row['iznos'];
						$status_rate		 	= $row['status_rate'];
						$tip_rate		 		= $row['tip_rate'];
						$desc_rate		 		= $row['desc_rate'];
						$broj_rate_nalog		= $row['broj_rate_nalog'];
						$broj_rate_kandidat		= $row['broj_rate_kandidat'];
						// Ovo kupi 1 ili 2 direkt iz querija jer imamo tip rate koja zavisi od toga je li od kand ili naloga
						$vrsta_querija		 	= $row['vrsta_querija'];
						$nalog_dospijece		= $row['nalog_dospijece'];
						
						if($kandidat != NULL){
							$kandidat_full_name		= getCandidateFullnameR($row['kandidat']);
							$kandidat_ime_ispis		= '<a href = "/kandidati?page=open&id='.$kandidat.'" target="_blank">'.$kandidat_full_name.'</a>';
						}else{
							$kandidat_ime_ispis = "/";
						}

						$status_text 			= "";
						
						$nalog_name_ispis		= '<a href = "/nalozi?page=open&id='.$nalog_id.'" target="_blank">'.$nalog_broj." - ".$nalog_name.'</a>';
												
						if($firma_fakturisanja == 1){
							$firma_fakturisanja_text = "DE-Jobstep GmbH";
						}else if($firma_fakturisanja ==2){
							$firma_fakturisanja_text = "CH-Jobstep Int GmbH";
						}else{
							$firma_fakturisanja_text = "-";
						}

						// NDF - Nista se ni ne prikazuje
						if($status_rate == 0){
							$status_text 		= '<span class="label label-danger material-label material-label_danger main-container__column">Očekivano</span>';
							$total_ndf		   += $row['iznos'];
							$kf_datum_kreiranja_text = ''. date('d.m.Y', strtotime($datum)).'';
						// Treba fakturisati - Prikazuje se datum kreiranja i umjesto datuma fakturisanja ima dugme za fakturisanje ako je vrijeme
						}else if($status_rate == 1){
							if(date("Y-m-d") >=  date('Y-m-d', strtotime($datum. "+$nalog_dospijece days"))){
								$prosao_rok_fakturisanja = '<i class="fa fa-clock-o" title="Kasnite sa fakturisanjem." style="color:white;cursor:pointer;" aria-hidden="true"></i>';
							}else{
								$prosao_rok_fakturisanja = '';
							}
							$status_text 		= '<span id="status_type_'.$kf_id.'" class="label label-info material-label material-label_info main-container__column">Treba fakturisati '.$prosao_rok_fakturisanja.'</span>';
							$total_fakturisati += $row['iznos'];
							$kf_datum_kreiranja_text = ''. date('d.m.Y', strtotime($datum)).'';
						}else if($status_rate == 2){
							if(date("Y-m-d") >=  date('Y-m-d', strtotime($datum. "+$nalog_dospijece days"))){
								$prosao_rok_placanja = '<i class="fa fa-clock-o" title="Kasnite sa plaćanjem." style="color:white;cursor:pointer;" aria-hidden="true"></i>';
							}else{
								$prosao_rok_placanja = '';
							}
							$status_text 		= '<span id="status_type_'.$kf_id.'" class="label label-primary material-label material-label_primary main-container__column">Fakturisano '.$prosao_rok_placanja.'</span>';
							$total_fakturisano += $row['iznos'];
							$kf_datum_kreiranja_text = ''. date('d.m.Y', strtotime($datum)).'';
						}

						if($vrsta_querija == 1){
							if($tip_rate == 1){
								$rata_text 	= '<span class="label label-secondary material-label material-label_secondary main-container__column">Avans (standardni)</span>';
							}else if($tip_rate == 2){
								$rata_text 	= '<span class="label label-secondary material-label material-label_secondary main-container__column">Avans (mjesečni)</span>';
							}else if($tip_rate == 3){
								$rata_text 	= '<span class="label label-secondary material-label material-label_secondary main-container__column">Mjesečna rata ('.$broj_rate_nalog.')</span>';
							}else if($tip_rate == 4){
								$rata_text 	= '<span class="label label-secondary material-label material-label_secondary main-container__column">Nostrifikacija ('.$broj_rate_nalog.')</span>';
							}else if($tip_rate == 5){
								$rata_text 	= '<span class="label label-secondary material-label material-label_secondary main-container__column" title="Očekivana uplata za svakog kandidata na potpisu ugovora">Potpis ugovora*</span>';
							}
						}else if($vrsta_querija == 2){
							if($tip_rate == 0){
								$rata_text 	= '<span class="label label-secondary material-label material-label_secondary main-container__column">Nostrifikacija</span>';
							}else if($tip_rate == 1){
								$rata_text 	= '<span class="label label-secondary material-label material-label_secondary main-container__column">Ugovor</span>';
							}else if($tip_rate == 2){
								$rata_text 	= '<span class="label label-secondary material-label material-label_secondary main-container__column">Dobio vizu</span>';
							}else if($tip_rate == 3){
								$rata_text 	= '<span class="label label-secondary material-label material-label_secondary main-container__column">Početak rada</span>';
							}else{
								$rata_text 	= '<span class="label label-secondary material-label material-label_secondary main-container__column">'. getMonthsAfterByRateId($broj_rate_kandidat) .' mj. nakon</span>';
							}
						}
						

						?>
							<tr>
								<td><?php echo $brojac; ?></td>
								<td title="<?php echo $company_name;?>"><?php echo $company_name; ?></td>
								<td title="<?php echo $nalog_name;?>"><?php if($desc_rate != NULL) {echo $desc_rate." - ".$nalog_name_ispis;}else{ echo $nalog_name_ispis;} ?></td>
								<td title="<?php echo $kandidat;?>"><?php echo $kandidat_ime_ispis; ?></td>
								<td><?php echo $firma_fakturisanja_text; ?></td>
								<td class="text-center"><?php echo $status_text; ?></td>
								<td class="text-center"><?php echo $rata_text; ?></td>
								<td class="text-center" data-order = "<?php echo $datum; ?>"><?php echo $kf_datum_kreiranja_text; ?></td>
								<td class="text-right"><?php echo number_format((float)$iznos, 2, '.', ','); ?></td>
							</tr>
						<?php
					}
					?>	
						</tbody>
					</table>
					<div class="row" style="text-align:center;">
						<div class="card" data-toggle="tooltip" data-placement="top" title="Potrebno fakturisati">
							<div class="rectangle expense"></div>
							<div>
							  <div class="naslov">Očekivano</div>
							  <div class="balance">Vrijednost</div>
							  <div class="kurs">€</div>
							  <div class="amount"><?php echo number_format((float)$total_ndf, 2, '.', ','); ?></div>
							  <div class="siluet-1"></div>
							  <div class="siluet-2"></div>
							</div>
						</div>
						<div class="card"  data-toggle="tooltip" data-placement="top" title="Fakturisano">
							<div class="rectangle info"></div>
							<div>
							  <div class="naslov">Fakturisati</div>
							  <div class="balance">Vrijednost</div>
							  <div class="kurs">€</div>
							  <div class="amount"><?php echo number_format((float)$total_fakturisati, 2, '.', ','); ?></div>
							  <div class="siluet-1"></div>
							  <div class="siluet-2"></div>
							</div>
						</div>
						<div class="card" data-toggle="tooltip" data-placement="top" title="Plaćeno">
							<div class="rectangle income"></div>
							<div>
							  <div class="naslov">Fakturisano</div>
							  <div class="balance">Vrijednost</div>
							  <div class="kurs">€</div>
							  <div class="amount"><?php echo number_format((float)($total_fakturisano), 2, '.', ',');?></div>
							  <div class="siluet-1"></div>
							  <div class="siluet-2"></div>
							</div>
						</div>
					</div>
					<?php

				break;

				case "get_agents_for_nalog":
					$nalog_id = $_POST["nalog_id"];

					$agent_rows = getTFAgentsForNalog($nalog_id);
					$agent_rows_count = count($agent_rows);

					if($agent_rows_count > 0){
						echo json_encode($agent_rows);
					}else{
						echo 0;
					}

				break;

				case "remove_agent_from_nalog":
					$nalog_id = $_POST["nalog_id"];
					$agent_id = $_POST["agent_id"];
					$vrsta_id = $_POST["vrsta_id"];

					$get_candidates = $db->prepare("SELECT
														kan.kandidat_id,
														kan.tf_reserved_agent,
														tf.tf_status_id
													FROM
														idk_kandidati kan
													JOIN idk_task_force tf ON
														kan.kandidat_id = tf.tf_candidate_id AND kan.tf_reserved_agent = tf.tf_agent_id AND tf.tf_vrsta_id = $vrsta_id AND tf.tf_last_active_task = 1
													WHERE
														tf.tf_nalog_id = $nalog_id AND kan.tf_reserved_agent = $agent_id
													GROUP BY
														kandidat_id
													ORDER BY
														`kan`.`kandidat_id` ASC;
													");

					$get_candidates->execute();

					$rows = $get_candidates->fetchAll(PDO::FETCH_ASSOC);

					$candidate_list = array();

					foreach($rows as $row){
						disconnectAgentFromCandidate($row["kandidat_id"]);
						$candidate_list[] = $row["kandidat_id"];
					}

					$delete_query = $db->prepare("DELETE FROM idk_tf_agent_nalog WHERE tfan_nalog = :tfan_nalog AND tfan_agent = :tfan_agent AND tfan_vrsta_id = :tfan_vrsta_id");
					$delete_query->execute(array(
						":tfan_nalog" => $nalog_id,
						":tfan_agent" => $agent_id,
						":tfan_vrsta_id" => $vrsta_id
					));

					$candidate_list_str = implode(",", $candidate_list);

					$log_desc = "Zaposlenik $logged_employee_id uklonio agenta $agent_id iz naloga $nalog_id. Odvezani kandidati: $candidate_list_str.";
					
					$log_date = date('Y-m-d H:i:s');

					$log_query = $db->prepare("
									INSERT INTO idk_logs
										(log_employeeid, log_desc, log_date)
									VALUES
										(:log_employeeid, :log_desc, :log_date)");

					$log_query->execute(array(
									':log_employeeid' => $logged_employee_id,
									':log_desc' => $log_desc,
									':log_date' => $log_date));

				break;

				case "remove_all_agents_from_nalog":
					$nalog_id = $_POST["nalog_id"];

					$delete_query = $db->prepare("DELETE FROM idk_tf_agent_nalog WHERE tfan_nalog = :tfan_nalog");
					$delete_query->execute(array(
						":tfan_nalog" => $nalog_id
					));

				break;

				case "add_agents_to_nalog":
					$nalog_id = $_POST["nalog_id"];
					$agent_ids = $_POST["agent_ids"];
					$vrsta_id = $_POST["vrsta_id"];

					foreach($agent_ids as $agent_id){
						$query = $db->prepare("
							INSERT INTO idk_tf_agent_nalog
								(tfan_nalog, tfan_agent, tfan_vrsta_id)
							VALUES
								(:tfan_nalog, :tfan_agent, :tfan_vrsta_id)
						");

						$query->execute(array(
							':tfan_nalog' 	=> $nalog_id,
							':tfan_agent' 	=> $agent_id,
							':tfan_vrsta_id' 	=> $vrsta_id
						));
					}

				break;
				
				case "get_tf_vrsta_option":

					$query = $db -> prepare('SELECT * FROM `idk_tf_vrste` WHERE category != 3');

					$query -> execute();

					$rows = $query->fetchAll(PDO::FETCH_ASSOC);

					echo json_encode($rows);
				break;

				case "get_reserved_agent_for_candidate":
					$candidate_id = $_POST["candidate_id"];

					$query = $db->prepare("SELECT tf_reserved_agent FROM idk_kandidati WHERE kandidat_id = $candidate_id");
					$query->execute();

					$row = $query->fetch();
					if($row['tf_reserved_agent'] != null){
						echo getEmployeeFullnameById($row['tf_reserved_agent']);
					}else{
						echo 'Nije rezervisan';
					}

				break;

				case "get_free_agents_for_nalog":
					$nalog_id = $_POST["nalog_id"];
					$vrsta_id = $_POST["vrsta_id"];

					$query = $db -> prepare('
						SELECT employee_id, CONCAT(employee_firstname," ",employee_lastname) as employee_fullname
						FROM idk_employees
						LEFT JOIN idk_tf_agent_nalog
						ON idk_employees.employee_id = idk_tf_agent_nalog.tfan_agent AND tfan_nalog = :nalog_id AND tfan_vrsta_id = :tfan_vrsta_id
						WHERE tfan_id IS NULL AND (employee_status != 0 AND FIND_IN_SET("18", employee_status)) 
						GROUP BY idk_employees.employee_id');

					$query -> execute(array(
						':nalog_id' => $nalog_id,
						':tfan_vrsta_id' => $vrsta_id
					));

					$rows = $query->fetchAll(PDO::FETCH_ASSOC);

					echo json_encode($rows);
				break;

				case "list_nalog_profil_pp":
					$nalog_id = $_POST['nalog_id'];

					$query = $db->prepare("SELECT
											pnp_id,
											pnp_info_id,
											pip_id,
											pip_name,
											pip_name_de
										FROM
											idk_pp_nalog_profil
										JOIN idk_pp_informacije_profil ON idk_pp_nalog_profil.pnp_info_id = idk_pp_informacije_profil.pip_id
										WHERE
											pnp_nalog_id = :pnp_nalog_id AND pip_status = 1"
										);

					$query->execute(array(
						":pnp_nalog_id" => $nalog_id
					));

					?>
						<table id="table_nalog_profil_pp" class="display" cellspacing="0" width="100%">
							<thead>
								<tr>
									<th>ID</th>
									<th>Naziv</th>
									<th>Ispis na njemačkom</th>
									<th></th>
								</tr>
							</thead>
							<tbody id="tbody_nalog_profil_pp">
						
					<?php
					while($row = $query->fetch()){
					?>
								<tr>
									<td><?php echo $row["pnp_id"]; ?></td>
									<td><?php echo $row["pip_name"]; ?></td>
									<td><?php echo $row["pip_name_de"]; ?></td>
									<td><a href="#" class="btn material-btn material-btn-icon-danger material-btn_danger main-container__column material-btn-icon-responsive remove-nalog_pp_info" id="remove_nalog_pp_info" data-value="<?php echo $row["pnp_id"]; ?>" data-pip_id = "<?php echo $row["pip_id"];?>"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> <span>Ukloni</span></a></td>
								</tr>
					<?php
					}
					?>
							</tbody>
						</table>
						<script>
							$('.remove-nalog_pp_info').on('click',function(){
								var pnp_id = $(this).data('value');
								var pip_id = $(this).data('pip_id');
								var nalog_id = <?php echo $nalog_id; ?>;
                
								$.ajax({
									url: 'ajax_data.php?page=remove_nalog_pp_info',
									type: 'POST',
									data: {
										'pnp_id': pnp_id,
										'nalog_id': nalog_id,
										'pip_id': pip_id
									},
									dataType: 'html',
									success: function(data){
										window.location.reload();
									},
									error: function (xhr, ajaxOptions, thrownError) {
										alert(xhr.status);
										alert(thrownError);
									}
								});
							});
						</script>
					<?php
				break;

				case "remove_nalog_pp_info":
					$nalog_id 	= $_POST['nalog_id'];
					$pnp_id 	= $_POST['pnp_id'];
					$pip_id 	= $_POST['pip_id'];
					
					$query = $db->prepare("DELETE FROM `idk_pp_nalog_profil` WHERE pnp_id = :pnp_id");
					$query->execute(array(
						":pnp_id" => $pnp_id
					));

					$log_desc = "Uklonjena informacija za prikaz na pp-u: [".$pip_id."] za nalog: [".$nalog_id."].";
					$log_type = "0";

					addToLogs($log_desc, $log_type);
				break;

				case "deactivate_tf_obrada":
					$employee_id 	= $_POST['employee_id'];

					$query = $db->prepare("DELETE FROM `idk_tf_agent_nalog` WHERE tfan_agent = :tfan_agent AND tfan_vrsta_id > 11 AND tfan_nalog = 0");
					$query->execute(array(
						":tfan_agent" => $employee_id
					));

					$log_desc = "Uklonjen agent iz TF obrade: [".$employee_id."].";
					$log_type = "0";

					addToLogs($log_desc, $log_type);
				break;

				case "get_tasks_for_agent":
					$employeeid = $_POST["employeeid"];

					$tasks_rows = getTFTasksForAgent($employeeid);
					$tasks_rows_count = count($tasks_rows);

					if($tasks_rows_count > 0){
						echo json_encode($tasks_rows);
					}else{
						echo 0;
					}

				break;

				case "remove_agent_from_obrada_task":
					$agent_id = $_POST["agent_id"];
					$vrsta_id = $_POST["vrsta_id"];

					$query = $db->prepare("DELETE FROM `idk_tf_agent_nalog` WHERE tfan_agent = :tfan_agent AND tfan_vrsta_id = :tfan_vrsta_id AND tfan_nalog = 0");
					$query->execute(array(
						":tfan_agent" => $agent_id,
						":tfan_vrsta_id" => $vrsta_id
					));
				break;

				case "get_tf_vrsta_obrada_option":
					$agent_id = $_POST["agent_id"];

					$query = $db -> prepare("SELECT * 
											FROM idk_tf_vrste
											LEFT JOIN idk_tf_agent_nalog ON idk_tf_vrste.id = idk_tf_agent_nalog.tfan_vrsta_id AND tfan_agent = $agent_id
											WHERE category = 3 AND idk_tf_agent_nalog.tfan_id IS NULL");

					$query -> execute();

					$rows = $query->fetchAll(PDO::FETCH_ASSOC);

					echo json_encode($rows);
				break;

				case "add_agent_to_obrada_tasks":
					$agent_id = $_POST["agent_id"];
					$vrsta_ids = $_POST["vrsta_ids"];

					foreach($vrsta_ids as $vrsta_id){
						$query = $db->prepare("
							INSERT INTO idk_tf_agent_nalog
								(tfan_nalog, tfan_agent, tfan_vrsta_id)
							VALUES
								(:tfan_nalog, :tfan_agent, :tfan_vrsta_id)
						");

						$query->execute(array(
							':tfan_nalog' 	=> 0,
							':tfan_agent' 	=> $agent_id,
							':tfan_vrsta_id' 	=> $vrsta_id
						));
					}
				break;
				
				case "get_agent_tf_stats": // Stari backend za listu broja taskova po agentu za vrstu taska: casting, posredovanje, obrada, inkaso

					$filter_select_vrste 	= $_REQUEST['filter_select_vrste'];
					if(!$filter_select_vrste){
						$filter_select_vrste = [];
					}
					$filter_select_vrste_imploded = implode(',',$filter_select_vrste);
					$filter_datum 			= $_REQUEST['filter_datum'];

					$uslov_vrijeme 	= "";
					$uslov_vrsta = "";
					
					if(strpos($filter_datum, "to")){
						$tmp_period = array();
						$tmp_period = explode('to', $filter_datum);
						$filter_datum_od = date("Y-m-d 00:00:00", strtotime($tmp_period[0]));
						$filter_datum_do = date("Y-m-d 23:59:59", strtotime($tmp_period[1]));
						$uslov_vrijeme = " AND tf.tf_doe BETWEEN '".$filter_datum_od."' AND '".$filter_datum_do."'";
					}
					else{
						$filter_datum_od = date("Y-m-d 00:00:00", strtotime($filter_datum));
						$filter_datum_do = date("Y-m-d 23:59:59", strtotime($filter_datum));
						$uslov_vrijeme = "AND tf.tf_doe BETWEEN '".$filter_datum_od."' AND '".$filter_datum_do."'";
					}

					$uslov_vrsta = "AND category IN (".implode(',',$filter_select_vrste).")";

					$query_summator = $db->prepare("SELECT
													category,
													SUM(CASE WHEN category = 1 THEN 1 ELSE 0 END) AS total_casting,
													SUM(CASE WHEN category = 2 THEN 1 ELSE 0 END) AS total_posredovanje,
													SUM(CASE WHEN category = 3 THEN 1 ELSE 0 END) AS total_obrada,
													SUM(CASE WHEN category = 4 THEN 1 ELSE 0 END) AS total_inkaso
												FROM
													idk_task_force tf
												INNER JOIN idk_tf_vrste ON tf.tf_vrsta_id = idk_tf_vrste.id
												INNER JOIN idk_employees emp ON tf.tf_agent_id = emp.employee_id
												WHERE
													FIND_IN_SET('18', employee_status)
													$uslov_vrijeme
													$uslov_vrsta");

					$query_summator -> execute();
					$row_summator = $query_summator -> fetch();
					$total_casting = $row_summator["total_casting"] ?? 0;
					$total_posredovanje = $row_summator["total_posredovanje"] ?? 0;
					$total_obrada = $row_summator["total_obrada"] ?? 0;
					$total_inkaso = $row_summator["total_inkaso"] ?? 0;

					$query_get = $db->prepare("SELECT tf_id, tf_agent_id, tf_candidate_id,tf_vrsta_id, employee_firstname, employee_lastname, category,
											SUM(
												CASE WHEN category = 1 THEN 1 ELSE 0 END
											) AS casting,
											SUM(
												CASE WHEN category = 2 THEN 1 ELSE 0 END
											) AS posredovanje,
											SUM(
												CASE WHEN category = 3 THEN 1 ELSE 0 END
											) AS obrada,
											SUM(
												CASE WHEN category = 4 THEN 1 ELSE 0 END
											) AS inkaso,
											SUM(1) AS total
											FROM
												idk_task_force tf
											INNER JOIN idk_employees emp ON tf.tf_agent_id = emp.employee_id
											INNER JOIN idk_tf_vrste ON tf.tf_vrsta_id = idk_tf_vrste.id
											WHERE
												FIND_IN_SET('18', employee_status)
												$uslov_vrijeme
												$uslov_vrsta
											GROUP BY
												tf_agent_id");

					$query_get -> execute();
					
					$brojac = 0;

					?>
					<table id="table" class="stripe col-12">
						<thead>
							<th>#</th>
							<th>Agent</th>
							<th>Casting</th>
							<th>Posredovanje</th>
							<th>Obrada</th>
							<th>Inkaso</th>
							<th>Suma</th>
						</thead>
						<tbody>
					<?php
					$getSiteUrl = getSiteUrlr();
					$total_sum = 0;
					while($row = $query_get -> fetch()){
						
						$brojac 				= $brojac + 1;
						$tf_agent_id 			= $row['tf_agent_id'];
						$employee_firstname 	= $row['employee_firstname'];
						$employee_lastname 		= $row['employee_lastname'];
						$category 				= $row['category'];

						if($row['casting'] != 0){
							$casting			= '<a href="'.$getSiteUrl.'tf_agent_stats?page=candidates&date='.$filter_datum.'&type=1&agent='.$tf_agent_id.'">'.$row['casting'].'</a>';
						}else{
							$casting			= $row['casting'];
						}

						if($row['posredovanje'] != 0){
							$posredovanje		= '<a href="'.$getSiteUrl.'tf_agent_stats?page=candidates&date='.$filter_datum.'&type=2&agent='.$tf_agent_id.'">'.$row['posredovanje'].'</a>';
						}else{
							$posredovanje		= $row['posredovanje'];
						}

						if($row['obrada'] != 0){
							$obrada				= '<a href="'.$getSiteUrl.'tf_agent_stats?page=candidates&date='.$filter_datum.'&type=3&agent='.$tf_agent_id.'">'.$row['obrada'].'</a>';
						}else{
							$obrada				= $row['obrada'];
						}

						if($row['inkaso'] != 0){
							$inkaso				='<a href="'.$getSiteUrl.'tf_agent_stats?page=candidates&date='.$filter_datum.'&type=4&agent='.$tf_agent_id.'">'.$row['inkaso'].'</a>';
						}else{
							$inkaso				= $row['inkaso'];
						}

						if($row['total'] != 0){
							$total				= '<a href="'.$getSiteUrl.'tf_agent_stats?page=candidates&date='.$filter_datum.'&type='.$filter_select_vrste_imploded.'&agent='.$tf_agent_id.'">'.$row['total'].'</a>';
						}else{
							$total				= $row['total'];
						}

						$total_sum = $total_sum + $row['total'];

						?>
							<tr>
								<td><?php echo $brojac; ?></td>
								<td><?php echo $employee_firstname." ".$employee_lastname; ?></td>
								<td><?php echo $casting; ?></td>
								<td><?php echo $posredovanje; ?></td>
								<td><?php echo $obrada; ?></td>
								<td><?php echo $inkaso; ?></td>
								<td><?php echo $total; ?></td>
							</tr>
						<?php
					}
					?>	
						</tbody>
					</table>
					<div class="row" style="text-align:center; display: flex; flex-direction: col;">
						<div class="card" data-toggle="tooltip" data-placement="top" title="Casting">
							<div class=" income"></div>
							<div>
							  <div class="naslov">Casting</div>
							  <div class="amount"><?php echo $total_casting; ?></div>
							</div>
						</div>
						<div class="card"  data-toggle="tooltip" data-placement="top" title="Posredovanje">
							<div class=" income"></div>
							<div>
							  <div class="naslov">Posredovanje</div>
							  <div class="amount"><?php echo $total_posredovanje; ?></div>
							</div>
						</div>
						<div class="card" data-toggle="tooltip" data-placement="top" title="Obrada">
							<div class=" income"></div>
							<div>
							  <div class="naslov">Obrada</div>
							  <div class="amount"><?php echo $total_obrada; ?></div>
							</div>
						</div>
						<div class="card" data-toggle="tooltip" data-placement="top" title="Inkaso">
							<div class=" income"></div>
							<div>
							  <div class="naslov">Inkaso</div>
							  <div class="amount"><?php echo $total_inkaso; ?></div>
							</div>
						</div>
						<div class="card" data-toggle="tooltip" data-placement="top" title="Total">
							<div class=" income"></div>
							<div>
							  <div class="naslov">Total</div>
							  <div class="amount"><?php echo $total_sum; ?></div>
							</div>
						</div>
					</div>
					<?php

				break;

				case "get_agent_tf_stats_casting": // Novi backend za listu broja taskova po agentu samo za casting

					$filter_datum 			= $_REQUEST['filter_datum'];

					$uslov_vrijeme 	= "";
					$uslov_vrsta = "";
					
					if(strpos($filter_datum, "to")){
						$tmp_period = array();
						$tmp_period = explode('to', $filter_datum);
						$filter_datum_od = date("Y-m-d 00:00:00", strtotime($tmp_period[0]));
						$filter_datum_do = date("Y-m-d 23:59:59", strtotime($tmp_period[1]));
						$uslov_vrijeme = " AND tf_doe BETWEEN '".$filter_datum_od."' AND '".$filter_datum_do."'";
					}
					else{
						$filter_datum_od = date("Y-m-d 00:00:00", strtotime($filter_datum));
						$filter_datum_do = date("Y-m-d 23:59:59", strtotime($filter_datum));
						$uslov_vrijeme = "AND tf_doe BETWEEN '".$filter_datum_od."' AND '".$filter_datum_do."'";
					}

					$uslov_vrsta = "AND tf_status_id IN (".implode(',',$filter_select_vrste).")";
					// echo $uslov_vrijeme;

					$sql_vrste = "";
					if (isset($_POST['filter_select_vrste'])) {
						foreach ($_POST['filter_select_vrste'] as $option) {
							list($status_id, $status_label) = explode('|', $option, 2);
							// echo "Status ID: $status_id, Label: $status_label<br>";
							$sql_vrste = $sql_vrste . ", SUM(CASE WHEN tf_status_id = " . $status_id . " THEN 1 ELSE 0 END) AS `" . $status_label . "`";
						}
					}
					
					$query_get = $db->prepare("SELECT 
													CONCAT(employee_firstname, ' ', employee_lastname) as 'Agent', COUNT(*) as 'Ukupno taskova' " . $sql_vrste . " , tf_agent_id as 'AgentID'
												FROM idk_task_force
												INNER JOIN idk_employees emp ON tf_agent_id = emp.employee_id
												WHERE FIND_IN_SET('18', employee_status)
												$uslov_vrijeme
												
												
												GROUP BY tf_agent_id;");

					$query_get -> execute();
					$results = $query_get->fetchAll(PDO::FETCH_ASSOC);
					$brojac = 0;

					if (count($results) > 0): ?>
						<table id="table" class="stripe">
							<thead>
								<tr>
									<?php 
									foreach (array_keys($results[0]) as $column){ 
										if(htmlspecialchars($column) === "AgentID") {

										}else{
											?>
											<th><?php echo  htmlspecialchars($column) ?></th>
											<?php 
										}
									} ?>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($results as $row){ ?>
									<tr>
										<?php foreach ($row as $key => $value){ ?>
											<?php if ($key === 'AgentID'){}
											elseif ($key === 'Ukupno taskova'){ ?>
												<td>
													<a href="<?php $getSiteUrl; ?>tf_agent_stats?page=candidates_list_for_agent&date=<?php echo $filter_datum; ?>&type=1&agent=<?php echo $row['AgentID']; ?>" target="_BLANK"><?php echo $value; ?></a>
													
												</td>
											<?php }else{ ?>
												<td><?= htmlspecialchars($value) ?></td>
											<?php }; ?>
										<?php } ?>
									</tr>
								<?php } ?>
							</tbody>
						</table>
						<!-- Pie Charts -->
						<div class="chart-grid">
							<?php foreach ($results as $i => $row): 
								$labels = [];
								$data = [];

								foreach ($row as $key => $value) {
									if ($key !== 'Agent' && $value > 0) {
										$labels[] = $key;
										$data[] = $value;
									}
								}
							?>
								<div class="chart-container">
									<h4><?= htmlspecialchars($row['Agent']) ?></h4>
									<canvas
										class="chart-canvas"
										id="chart_<?= $i ?>"
										width="300"
										height="300"
										data-labels='<?= json_encode($labels) ?>'
										data-values='<?= json_encode($data) ?>'>
									</canvas>
								</div>
							<?php endforeach; ?>
						</div>
					<?php else: ?>
						<p>Nema podataka za odabrani period.</p>
					<?php endif; 

				break;

				case "get_agent_tf_stats_candidates": // Stari backend za listu taskova za agenta

					$filter_select_vrste 	= $_REQUEST['filter_select_vrste'];
					$filter_datum 			= $_REQUEST['filter_datum'];
					$agent 					= $_REQUEST['agent'];

					$uslov_vrijeme 	= "";
					$uslov_vrsta = "";
					$uslov_agent = "";
					
					// datum kreiranja
					if(strpos($filter_datum, "to")){
						$tmp_period = array();
						$tmp_period = explode('to', $filter_datum);
						$filter_datum_od = date("Y-m-d 00:00:00", strtotime($tmp_period[0]));
						$filter_datum_do = date("Y-m-d 23:59:59", strtotime($tmp_period[1]));
						$uslov_vrijeme = " AND tf.tf_doe BETWEEN '".$filter_datum_od."' AND '".$filter_datum_do."'";
					}
					else{
						$filter_datum_od = date("Y-m-d 00:00:00", strtotime($filter_datum));
						$filter_datum_do = date("Y-m-d 23:59:59", strtotime($filter_datum));
						$uslov_vrijeme = "AND tf.tf_doe BETWEEN '".$filter_datum_od."' AND '".$filter_datum_do."'";
					}

					$uslov_vrsta = "AND category IN (".$filter_select_vrste.")";

					$uslov_agent = "AND tf_agent_id = $agent";

					$query_get = $db->prepare("SELECT tf_id, tf_agent_id, tf_candidate_id, ts.tfs_name, tf_doe, tf_vrsta_id, employee_firstname, employee_lastname, kandidat_ime, kandidat_prezime, category,
												CASE 
													WHEN category = 1 THEN 'Casting' 
													WHEN category = 2 THEN 'Posredovanje' 
													WHEN category = 3 THEN 'Obrada' 
													WHEN category = 4 THEN 'Inkaso'
												END AS vrsta_taska
											FROM
												idk_task_force tf
											INNER JOIN idk_kandidati kan ON tf.tf_candidate_id = kan.kandidat_id
											INNER JOIN idk_employees emp ON tf.tf_agent_id = emp.employee_id
											INNER JOIN idk_tf_vrste ON tf.tf_vrsta_id = idk_tf_vrste.id
											JOIN idk_tf_statusi ts ON ts.tfs_id = tf_status_id
											WHERE
												FIND_IN_SET('18', employee_status)
												$uslov_vrijeme
												$uslov_vrsta
												$uslov_agent
												");

					$query_get -> execute();
					
					$brojac = 0;
					?>
					<table id="table" class="striped col-12">
						<thead>
							<th>#</th>
							<th>Kandidat</th>
							<th>Agent</th>
							<th>Vrijeme poziva</th>
							<th>Task</th>
							<th>Vrsta taska</th>
						</thead>
						<tbody>
					<?php
					$getSiteUrl = getSiteUrlr();
					$total_casting = 0;
					$total_posredovanje = 0;
					$total_obrada = 0;
					$total_inkaso = 0;
					$total = 0;

					$total_neuspjesna = 0;
					$total_ne_isp_usl = 0;
					$total_nije_zaint = 0;
					$total_isp_ne_jav = 0;
					$total_ne_odg_ter = 0;

					$total_dopuna 	  = 0;
					$total_zainteresi = 0;
					$total_pristao 	  = 0;
					$total_dolazi 	  = 0;
					$total_dosao 	  = 0;

					$total_nije_dosao = 0;
					$total_inbound_po = 0;

					while($row = $query_get -> fetch()){
						
						$brojac 				= $brojac + 1;
						$tf_agent_id 			= $row['tf_agent_id'];
						$tf_candidate_id 		= $row['tf_candidate_id'];
						$employee_firstname 	= $row['employee_firstname'];
						$employee_lastname 		= $row['employee_lastname'];
						$employee_lastname 		= $row['employee_lastname'];
						$vrijeme_poziva 		= $row['tf_doe'];
						$tfs_name 				= $row['tfs_name'];
						$vrsta_taska 			= $row['vrsta_taska'];
						$kandidat_ime 			= $row['kandidat_ime'];
						$kandidat_prezime 		= $row['kandidat_prezime'];
						$category 				= $row['category'];
						$dipl_id				= getDiplIdFromCandidate($tf_candidate_id);
						
						if($category == 1){
							$total_casting = $total_casting + 1;
							$candidate_link = '/kandidati?page=open&id='.$tf_candidate_id.'';

							switch($tfs_name){
								case "Neuspješna komunikacija": 			$total_neuspjesna += 1; break;
								case "Ne ispunjava uslove za nalog": 		$total_ne_isp_usl += 1; break;
								case "Nije zainteresiran": 					$total_nije_zaint += 1; break;
								case "Ispunjava uslove - ne javlja se": 	$total_isp_ne_jav += 1; break;
								case "Ne odgovara mu termin za casting": 	$total_ne_odg_ter += 1; break;

								case "Dopuna": 								$total_dopuna += 1; break;
								case "Zainteresiran": 						$total_zainteresi += 1; break;
								case "Pristao":								$total_pristao += 1; break;
								case "Dolazi": 								$total_dolazi += 1; break;
								case "Došao": 								$total_dosao += 1; break;

								case "Nije došao": 							$total_nije_dosao += 1; break;
								case "Inbound poziv": 						$total_inbound_po += 1; break;
								
							}
						}else if($category == 2){
							$total_posredovanje = $total_posredovanje + 1;
							$candidate_link = '/kandidati?page=open&id='.$tf_candidate_id.'';
						}else if($category == 3){
							$total_obrada = $total_obrada + 1;
							$candidate_link = 'nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$dipl_id.'';
						}else if($category == 4){
							$total_inkaso = $total_inkaso + 1;
							$candidate_link = '/kandidati?page=open&id='.$tf_candidate_id.'';
						}

						$total = $total +1;
						
						?>
							<tr>
								<td><?php echo $brojac; ?></td>
								<td><a href="<?php echo $candidate_link;?>" target="_blank"><?php echo $kandidat_ime. " " .$kandidat_prezime ; ?></a></td>
								<td><?php echo $employee_firstname." ".$employee_lastname; ?></td>
								<td><?php echo $vrijeme_poziva; ?></td>
								<td><?php echo $tfs_name; ?></td>
								<td><?php echo $vrsta_taska; ?></td>
							</tr>
						<?php
					}
					?>	
						</tbody>
					</table>
					
					<div id="taskTypeChartWrapper" style="display: flex; gap: 30px; align-items: flex-start; max-width: 900px; margin: 20px auto;">
  
						<!-- Pie Chart Container -->
						<div style="flex: 0 0 300px; height: 300px;">
							<canvas id="taskTypeChart"></canvas>
						</div>

						<!-- Custom Legend / Summary -->
						<div id="taskTypeLegend" style="flex: 1;">
							<!-- legend will be inserted here -->
						</div>

					</div>
					<div class="row" style="text-align:center; display: flex; flex-direction: col;">
						<div class="card" data-toggle="tooltip" data-placement="top" title="Casting">
							<div class=" income"></div>
							<div>
							  <div class="naslov">Casting</div>
							  <div class="amount"><?php echo $total_casting; ?></div>
							</div>
						</div>
						<div class="card"  data-toggle="tooltip" data-placement="top" title="Posredovanje">
							<div class=" income"></div>
							<div>
							  <div class="naslov">Posredovanje</div>
							  <div class="amount"><?php echo $total_posredovanje; ?></div>
							</div>
						</div>
						<div class="card" data-toggle="tooltip" data-placement="top" title="Obrada">
							<div class=" income"></div>
							<div>
							  <div class="naslov">Obrada</div>
							  <div class="amount"><?php echo $total_obrada; ?></div>
							</div>
						</div>
						<div class="card" data-toggle="tooltip" data-placement="top" title="Inkaso">
							<div class=" income"></div>
							<div>
							  <div class="naslov">Inkaso</div>
							  <div class="amount"><?php echo $total_inkaso; ?></div>
							</div>
						</div>
						<div class="card" data-toggle="tooltip" data-placement="top" title="Total">
							<div class=" income"></div>
							<div>
							  <div class="naslov">Total</div>
							  <div class="amount"><?php echo $total; ?></div>
							</div>
						</div>
					</div>
					<?php

				break;

				case "get_agent_casting_tasks_stats": // Novi backend za listu casting taskova za agenta uz sumu kandidata po statusima 

					$filter_select_vrste 	= $_REQUEST['filter_select_vrste'];
					$filter_datum 			= $_REQUEST['filter_datum'];
					$agent 					= $_REQUEST['agent'];

					$uslov_vrijeme 	= "";
					$uslov_vrsta = "";
					$uslov_agent = "";
					
					// datum kreiranja
					if(strpos($filter_datum, "to")){
						$tmp_period = array();
						$tmp_period = explode('to', $filter_datum);
						$filter_datum_od = date("Y-m-d 00:00:00", strtotime($tmp_period[0]));
						$filter_datum_do = date("Y-m-d 23:59:59", strtotime($tmp_period[1]));
						$uslov_vrijeme = " AND tf.tf_doe BETWEEN '".$filter_datum_od."' AND '".$filter_datum_do."'";
					}
					else{
						$filter_datum_od = date("Y-m-d 00:00:00", strtotime($filter_datum));
						$filter_datum_do = date("Y-m-d 23:59:59", strtotime($filter_datum));
						$uslov_vrijeme = "AND tf.tf_doe BETWEEN '".$filter_datum_od."' AND '".$filter_datum_do."'";
					}

					$uslov_vrsta = "AND category IN (".$filter_select_vrste.")";

					$uslov_agent = "AND tf_agent_id = $agent";

					$query_get = $db->prepare("SELECT tf_id, tf_agent_id, tf_candidate_id, ts.tfs_name, tf_doe, tf_vrsta_id, employee_firstname, employee_lastname, kandidat_ime, kandidat_prezime, category,
												CASE 
													WHEN category = 1 THEN 'Casting' 
													WHEN category = 2 THEN 'Posredovanje' 
													WHEN category = 3 THEN 'Obrada' 
													WHEN category = 4 THEN 'Inkaso'
												END AS vrsta_taska
											FROM
												idk_task_force tf
											INNER JOIN idk_kandidati kan ON tf.tf_candidate_id = kan.kandidat_id
											INNER JOIN idk_employees emp ON tf.tf_agent_id = emp.employee_id
											INNER JOIN idk_tf_vrste ON tf.tf_vrsta_id = idk_tf_vrste.id
											JOIN idk_tf_statusi ts ON ts.tfs_id = tf_status_id
											WHERE
												FIND_IN_SET('18', employee_status)
												$uslov_vrijeme
												$uslov_vrsta
												$uslov_agent
												");

					$query_get -> execute();
					
					$brojac = 0;
					?>
					<table id="table" class="stripe col-12">
						<thead>
							<th>#</th>
							<th>Kandidat</th>
							<th>Agent</th>
							<th>Vrijeme poziva</th>
							<th>Task</th>
							<th>Vrsta taska</th>
						</thead>
						<tbody>
					<?php
					$getSiteUrl = getSiteUrlr();
					$total = 0;

					$total_neuspjesna = 0;
					$total_ne_isp_usl = 0;
					$total_nije_zaint = 0;
					$total_isp_ne_jav = 0;
					$total_ne_odg_ter = 0;

					$total_dopuna 	  = 0;
					$total_zainteresi = 0;
					$total_pristao 	  = 0;
					$total_dolazi 	  = 0;
					$total_dosao 	  = 0;

					$total_nije_dosao = 0;
					$total_inbound_po = 0;

					$candidates_ids = array();

					while($row = $query_get -> fetch()){
						
						$brojac 				= $brojac + 1;
						$tf_agent_id 			= $row['tf_agent_id'];
						$tf_candidate_id 		= $row['tf_candidate_id'];
						$employee_firstname 	= $row['employee_firstname'];
						$employee_lastname 		= $row['employee_lastname'];
						$employee_lastname 		= $row['employee_lastname'];
						$vrijeme_poziva 		= $row['tf_doe'];
						$tfs_name 				= $row['tfs_name'];
						$vrsta_taska 			= $row['vrsta_taska'];
						$kandidat_ime 			= $row['kandidat_ime'];
						$kandidat_prezime 		= $row['kandidat_prezime'];
						$category 				= $row['category'];
						$dipl_id				= getDiplIdFromCandidate($tf_candidate_id);
						array_push($candidates_ids, $tf_candidate_id);
						
						if($category == 1){
							$candidate_link = '/kandidati?page=open&id='.$tf_candidate_id.'';

							switch($tfs_name){
								case "Neuspješna komunikacija": 			$total_neuspjesna += 1; break;
								case "Ne ispunjava uslove za nalog": 		$total_ne_isp_usl += 1; break;
								case "Nije zainteresiran": 					$total_nije_zaint += 1; break;
								case "Ispunjava uslove - ne javlja se": 	$total_isp_ne_jav += 1; break;
								case "Ne odgovara mu termin za casting": 	$total_ne_odg_ter += 1; break;

								case "Dopuna": 								$total_dopuna += 1; break;
								case "Zainteresiran": 						$total_zainteresi += 1; break;
								case "Pristao":								$total_pristao += 1; break;
								case "Dolazi": 								$total_dolazi += 1; break;
								case "Došao": 								$total_dosao += 1; break;

								case "Nije došao": 							$total_nije_dosao += 1; break;
								case "Inbound poziv": 						$total_inbound_po += 1; break;
								
							}
						}

						$total = $total +1;
						
						?>
							<tr>
								<td><?php echo $brojac; ?></td>
								<td><a href="<?php echo $candidate_link;?>" target="_blank"><?php echo $kandidat_ime. " " .$kandidat_prezime ; ?></a></td>
								<td><?php echo $employee_firstname." ".$employee_lastname; ?></td>
								<td><?php echo $vrijeme_poziva; ?></td>
								<td><?php echo $tfs_name; ?></td>
								<td><?php echo $vrsta_taska; ?></td>
							</tr>
						<?php
					}
					?>	
						</tbody>
					</table>
					<div class="row" style="margin-top: 20px;">
						<div class="col-md-6">
							<div id="taskTypeChartWrapper" style="display: flex; gap: 30px; align-items: flex-start; max-width: 900px; margin: 20px auto;">
								<!-- Pie Chart Container -->
								<div style="flex: 0 0 300px; height: 300px;">
									<canvas id="taskTypeChart"></canvas>
								</div>
								<!-- Custom Legend / Summary -->
								<div id="taskTypeLegend" style="flex: 1;">
									<!-- legend will be inserted here -->
								</div>
							</div>
						</div>

						<?php 
						$candidates_ids = array_unique($candidates_ids);
						$candidates_ids_for_query = implode(", ", $candidates_ids);
						
						$sql_statusi_prijave_rz_a = "
							SELECT 
								s.status_naziv, s.redoslijed_statusa, s.status_id,
								COUNT(*) AS total
							FROM 
								idk_kandidati k
							JOIN 
								idk_kandidat_status_prijave s ON k.kandidat_status_prijave = s.status_id
							WHERE 
								k.kandidat_id IN ($candidates_ids_for_query) AND rezervisanje = 1 AND k.tf_reserved_agent = $agent
							GROUP BY 
								s.status_naziv
							ORDER BY 
								s.redoslijed_statusa DESC
						";
						
						$query_sp_rz_a = $db->prepare($sql_statusi_prijave_rz_a);
						$query_sp_rz_a -> execute();

						?>
						<div class="col-md-6">
							<div class="card-container">
								<div class="cardf secondary">
									Ukupno poziva<br><?php echo $query_get->rowCount(); ?>
								</div>
								<div class="cardf total">
									Ukupno kandidata<br><?php echo count($candidates_ids); ?>
								</div>
							</div>
							<div class="section-title">Rezervisani kandidati ovog agenta:</div>
							<div class="card-container">
								<?php
								while($row = $query_sp_rz_a -> fetch()){
									// echo $row['status_naziv'] . ': ' . $row['total'] . "<br>";
									echo 
									'<a href="/tf_agent_stats?page=candidate_per_sp_for_agent&agent='.$agent.'&type=1&date='.$filter_datum.'&sp='.$row['status_id'].'" target="_BLANK" class="cardf green">'.$row['status_naziv'].'<br><strong>'.$row['total'].'</strong></a>';
								}
								?>
							</div>

							<?php
							$sql_statusi_prijave_rz_o = "
								SELECT 
									s.status_naziv, s.redoslijed_statusa, s.status_id,
									COUNT(*) AS total
								FROM 
									idk_kandidati k
								JOIN 
									idk_kandidat_status_prijave s ON k.kandidat_status_prijave = s.status_id
								WHERE 
									k.kandidat_id IN ($candidates_ids_for_query) AND rezervisanje = 1 AND (k.tf_reserved_agent != $agent OR k.tf_reserved_agent is null)
								GROUP BY 
									s.status_naziv
								ORDER BY 
									s.redoslijed_statusa DESC
							";
					
							$query_sp_rz_o = $db->prepare($sql_statusi_prijave_rz_o);
							$query_sp_rz_o -> execute();
							?>
							
							<div class="section-title">Rezervisani kandidati drugih agenata:</div>
							<div class="card-container">
					
								<?php
								while($row = $query_sp_rz_o -> fetch()){
									echo '<a href="/tf_agent_stats?page=candidate_per_sp_for_agent&agent='.$agent.'&type=2&date='.$filter_datum.'&sp='.$row['status_id'].'" target="_BLANK" class="cardf blue">'.$row['status_naziv'].'<br><strong>'.$row['total'].'</strong></a>';
								} ?>

							</div>
							<?php

							$sql_statusi_prijave_nr = "
								SELECT 
									s.status_naziv, s.redoslijed_statusa,
									COUNT(*) AS total
								FROM 
									idk_kandidati k
								JOIN 
									idk_kandidat_status_prijave s ON k.kandidat_status_prijave = s.status_id
								WHERE 
									k.kandidat_id IN ($candidates_ids_for_query) AND rezervisanje = 0
								GROUP BY 
									s.status_naziv
								ORDER BY 
									s.redoslijed_statusa DESC
							";
							
							$query_sp_rz_nr = $db->prepare($sql_statusi_prijave_nr);
							$query_sp_rz_nr -> execute();

							?>
							
							<div class="section-title">Nerezervisani:</div>
							<div class="card-container">
								<?php
								while($row = $query_sp_rz_nr -> fetch()){
									echo '<div class="cardf gray">'.$row['status_naziv'].'<br><strong>'.$row['total'].'</strong></div>';
								} ?>
							</div>
						</div>
					</div>
					<?php 

				break;

				case "get_candidates_list_by_sp":

					$agent 	= $_REQUEST['agent'];
					$type 	= $_REQUEST['type'];
					$date 	= $_REQUEST['date'];
					$sp 	= $_REQUEST['sp'];

					// datum kreiranja
					if(strpos($date, "to")){
						$tmp_period = array();
						$tmp_period = explode('to', $date);
						$filter_datum_od = date("Y-m-d 00:00:00", strtotime($tmp_period[0]));
						$filter_datum_do = date("Y-m-d 23:59:59", strtotime($tmp_period[1]));
						$uslov_vrijeme = " AND tf.tf_doe BETWEEN '".$filter_datum_od."' AND '".$filter_datum_do."'";
					}
					else{
						$filter_datum_od = date("Y-m-d 00:00:00", strtotime($date));
						$filter_datum_do = date("Y-m-d 23:59:59", strtotime($date));
						$uslov_vrijeme = "AND tf.tf_doe BETWEEN '".$filter_datum_od."' AND '".$filter_datum_do."'";
					}

					$uslov_vrsta = "AND category IN (1)";
					$uslov_agent = "AND tf_agent_id = $agent";

					// prvo query za taskove u tom periodu za tog agenta, pa napraviti unique niz po IDu kandidata
					$candidates_ids = array();
					$query_get = $db->prepare("SELECT tf_candidate_id
											FROM
												idk_task_force tf
											INNER JOIN idk_kandidati kan ON tf.tf_candidate_id = kan.kandidat_id
											INNER JOIN idk_employees emp ON tf.tf_agent_id = emp.employee_id
											INNER JOIN idk_tf_vrste ON tf.tf_vrsta_id = idk_tf_vrste.id
											JOIN idk_tf_statusi ts ON ts.tfs_id = tf_status_id
											WHERE
												FIND_IN_SET('18', employee_status)
												$uslov_vrijeme
												$uslov_vrsta
												$uslov_agent
												GROUP BY tf_candidate_id"
											);

					$query_get -> execute();

					while($row = $query_get -> fetch()){
						array_push($candidates_ids, $row['tf_candidate_id']);
					}
					$candidates_ids_for_query = implode(", ", $candidates_ids);

					// var_dump($candidates_ids);

					// onda za taj niz pretraziti sve kandidatu na tom SP-u (ne)rezervisani za tog agenta

					if($type == 1){
						$sql_agent = " AND tf_reserved_agent = " . $agent;
					}else{
						$sql_agent = " AND (tf_reserved_agent != $agent OR tf_reserved_agent is null) ";
					}
					$sql_statusi_prijave_rz_a = "
						SELECT 
							kandidat_id, kandidat_ime, kandidat_prezime, tf_reserved_agent, e.employee_firstname, e.employee_lastname
						FROM 
							idk_kandidati
						LEFT JOIN idk_employees e ON tf_reserved_agent = e.employee_id
						WHERE 
							kandidat_id IN ($candidates_ids_for_query) AND kandidat_status_prijave = $sp $sql_agent
						
					";
					
					$query_sp_rz_a = $db->prepare($sql_statusi_prijave_rz_a);
					$query_sp_rz_a -> execute();

					?>
					<table id="table" class="stripe col-12" style="text-align: left;">
						<thead>
							<th style="text-align: left;">#</th>
							<th style="text-align: left;">Kandidat</th>
							<th style="text-align: left;">Agent</th>
						</thead>
						<tbody>
						<?php
						$brojac = 1;
						while($row = $query_sp_rz_a -> fetch()){
							$kandidat_id = $row['kandidat_id'];
							$kandidat_ime = $row['kandidat_ime']." ".$row['kandidat_prezime'];
							$candidate_link = '/kandidati?page=open&id='.$kandidat_id.'';
							if($row['tf_reserved_agent'] !== null){
								$agent_name = $row['employee_firstname']." ".$row['employee_lastname'];
							}else{
								$agent_name = "Nije rezervisan za nijednog agenta";
							}
							?>
							<tr>
								<td style="text-align: left;"><?php echo $brojac++; ?></td>
								<td style="text-align: left;"><a href="<?php echo $candidate_link;?>" target="_blank"><?php echo $kandidat_ime ; ?></a></td>
								<td style="text-align: left;"><?php echo $agent_name; ?></td>

							<?php
						} ?>
						</tbody>
					</table>
						<?php

				break;
				
				case "update_city_and_plz":
					$selected_city 	= $_POST['selected_city'];
					$candidate_id 	= $_POST['candidate_id'];
					$plz 			= $_POST['plz'];
					
					$query = $db -> prepare('
						UPDATE idk_kandidati
						SET kandidat_destinacija_postanski_broj = :plz, kandidat_destinacija_grad = :selected_city
						WHERE kandidat_id = :candidate_id
					');

					$query->execute(array(	
						':plz' => $plz,
						':candidate_id' => $candidate_id,
						':selected_city' => $selected_city
					));

					$log_desc = "Kandidatu ".$candidate_id." dodan grad ".$selected_city.", ".$plz;
					$log_type = "0";

					addToLogs($log_desc, $log_type);
				break;
				
				case "assign_seller_to_candidate_lead":
				
					$candidate_id = $_POST['candidate_id'];
					$seller_id = $_POST['seller_id'];

					$query_set_seller = $db->prepare("
						UPDATE idk_kandidati
						SET zaduzeni_makler_id = :seller_id, zaduzen_makleru_datum = now(), kandidat_partner_lead_status = 1
						WHERE kandidat_id = :candidate_id
					");
					$query_set_seller -> execute(array(
						':seller_id' => $seller_id, 
						':candidate_id' => $candidate_id
					));

					$query_get_seller_name = $db -> prepare("
						SELECT jp_imeprezime 
						FROM idk_jobstep_partners
						WHERE jp_id = :seller_id
					");
					$query_get_seller_name -> execute(array(':seller_id' => $seller_id));
					$result = $query_get_seller_name -> fetch();

					$log_desc = "Kandidatu [".$candidate_id."] dodijeljen makler [".$seller_id."]";
					
					$log_date = date('Y-m-d H:i:s');

					$log_query = $db->prepare("
									INSERT INTO idk_logs
										(log_employeeid, log_desc, log_date)
									VALUES
										(:log_employeeid, :log_desc, :log_date)");

					$log_query->execute(array(
									':log_employeeid' => $logged_employee_id,
									':log_desc' => $log_desc,
									':log_date' => $log_date));

					echo '<b style = "color:green">'.$result['jp_imeprezime'].'</b>';
				break;

				case "bulk_assign_candidates_to_seller":
					$selected_candidates = $_POST['selected_candidates'];

					$get_all_sellers = $db->prepare('
						SELECT jp_id, jp_received_last_lead, 
							CASE 
							WHEN jp_id < (SELECT jp_id FROM idk_jobstep_partners WHERE jp_received_last_lead = 1) THEN 1 else 0 END as manji 
						FROM idk_jobstep_partners 
						WHERE jp_makler_id IS NOT NULL 
						ORDER BY jp_received_last_lead ASC, manji, jp_id
					');
					$get_all_sellers -> execute();
					
					$sellers_order = [];
					while($all_sellers = $get_all_sellers -> fetch()){
						$seller['id'] = $all_sellers['jp_id'];
						$seller['is_last'] = $all_sellers['jp_received_last_lead'];
						$sellers_order[] = $seller;
					}

					$update_statements = "";
					$last_received = "";
					for($i = 0; $i<count($selected_candidates); $i++){
						echo 'agent '.$sellers_order[$i % count($sellers_order)]['id'].' -> '.$selected_candidates[$i].'<br>';
						$update_statements .= 'UPDATE idk_kandidati SET zaduzeni_makler_id = '.$sellers_order[$i % count($sellers_order)]['id'].' WHERE kandidat_id = '.$selected_candidates[$i].';';
						$last_received = $sellers_order[$i % count($sellers_order)]['id'];
					}


					$reset_selected_candidates = $db -> prepare('
						UPDATE idk_kandidati
						SET kandidat_partner_lead_status = 1, zaduzen_makleru_datum = now(), zaduzeni_makler_reminder_datum = NULL
						WHERE kandidat_id IN ('.implode(',', $selected_candidates).');
					');	
					$reset_selected_candidates -> execute();

					$assign_query = $db->prepare($update_statements);
					$assign_query -> execute();

					$reset_previous_last_received = $db -> prepare('
						UPDATE idk_jobstep_partners
						SET jp_received_last_lead = NULL
					');
					$reset_previous_last_received -> execute();
					
					$set_actual_last_received = $db -> prepare('
						UPDATE idk_jobstep_partners
						SET jp_received_last_lead = 1
						WHERE jp_id = :last_received
					');
					$set_actual_last_received -> execute(array(':last_received' => $last_received));
				break;

				case "open_plz_search":
					$search_term = $_POST['search_term'];
					$page = 1;
					$response = array();
					
					do{
						$data = getOpenPlzResponse($search_term, $page);
						$response = array_merge($response, $data);
						$page ++;
					}while(count($data) == 50);

					$grouped_cities = array();
					$grouped_data = array();
					foreach($response as $index){
						$city = $index['name'];
						$plz = $index['postalCode'];

						if(!in_array($city, $grouped_cities)){
							$grouped_cities[] = $city;
							$newData = [];
							$newData['name'] = $city;
							$newData['plz'][] = $plz;
							array_push($grouped_data, $newData);
						}
						else{
							$i = findCityIndex($grouped_data, $city);
							array_push($grouped_data[$i]['plz'], $plz);
						}
					}

					echo json_encode($grouped_data);
				break;

				case "get_company_orders_odlasci":
					$companies = $_POST["companies"];
					if(!$companies){
						$companies = [];
					}
					$company_list = implode(',', $companies);

					$select_query = $db->prepare("SELECT nalog_id, kompanija_id, nalog_broj, nalog_naziv, comp.company_name
						FROM idk_nalozi
						INNER JOIN idk_companies comp ON kompanija_id = comp.company_id
						JOIN idk_kandidati kan ON nalog_id = kan.kandidat_nalog_id
						WHERE nalog_status NOT IN (8,12) AND company_id IN ($company_list)
						GROUP BY nalog_id  
						ORDER BY `idk_nalozi`.`nalog_id` DESC"
					);

					$select_query->execute();

					$rows = $select_query->fetchAll();

					foreach($rows as $row){
						echo '<option selected value="'.$row["nalog_id"].'">'.$row["nalog_broj"]." - ".$row["nalog_naziv"].' ('.$row["company_name"].')</option>';
					}
				break;

				case "reset_password":
					$id = $_POST["id"];
					$email = $_POST["email"];
					$fullname = $_POST["fullname"];

					$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
					$password = '';

					for ($i = 0; $i < 8; $i++) {
						$password .= $characters[rand(0, strlen($characters) - 1)];
					}

					$hashed_password = md5($password);


					$update = $db -> prepare("
						UPDATE idk_jobstep_partners 
						SET jp_password = '$hashed_password' , jp_last_password_reset = now()
						WHERE jp_id = $id
						
					");
					
					$update -> execute();

					$mail_status = sendMailMaklerPassword($email, $password, $fullname);

					$log_desc = "Resetirana šifra za: $makler_email, mail status: $mail_status";
					$log_type = "0";
					addToLogs($log_desc, $log_type); 

					echo '<b style = "color:green">Šifra resetirana</b>';

				break;

				case "set_default_tutorial":
					$tutorial_id = $_POST['id'];
					$reset_default_tutorial = $db -> prepare('UPDATE idk_partner_tutorials SET is_default = 0 WHERE is_default = 1');
					$reset_default_tutorial -> execute();

					$set_default_tutorial = $db -> prepare('UPDATE idk_partner_tutorials SET is_default = 1 WHERE id = :tutorial_id');
					$set_default_tutorial -> execute(array(':tutorial_id' => $tutorial_id));


				break;

				case "get_partner_accounts_list":
					$selected_companies = $_POST['selected_companies'];
					
					?>
					<table id="accounts_list" class="display" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th>Ime i prezime</th>
								<th>E-mail</th>
								<th>Datum registracije</th>
								<th>Jezik</th>
								<th>Kompanija</th>
								<th>Makler ID</th>
								<th>Direkcija</th>
								<th>Zadnji reset</th>
								<th>Prvi login</th>
								<th>Zadnji login</th>
								<th>Zadnja aktivnost</th>
								<th>Representative</th>
								<th>Status računa</th>
								<th class="text-center">Akcije računa</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$get_accounts = $db -> prepare('
									SELECT jp.jp_latest_activity, jp.jp_representative_employee_id, jp.jp_ime, jp.jp_prezime, jp.jp_tutorial_sent, jp.jp_datum_deaktivacije, jp.jp_confirmedaccount, jp.jp_direction_number, jp.jp_first_login, jp.jp_latest_login, e.employee_firstname, e.employee_lastname, jp.jp_id, jp.jp_imeprezime, jp.jp_email, jp.jp_lang, pc.pc_name, jp.jp_makler_id, jp.jp_register_date, jp.jp_last_password_reset
									FROM  idk_jobstep_partners jp
									JOIN idk_partner_companies pc 
									on pc.pc_id = jp.jp_partner_company
									LEFT JOIN idk_employees e
									ON e.employee_id = jp.jp_representative_employee_id
									WHERE jp.jp_user_type = 1
									AND jp.jp_partner_company IN ('.implode(',',$selected_companies).')
								');
								$get_accounts -> execute();

								while($accounts = $get_accounts -> fetch()){

									$id = $accounts['jp_id'];
									$firstname = $accounts['jp_ime'];
									$lastname = $accounts['jp_prezime'];
									$full_name = $accounts['jp_imeprezime'];
									$email = $accounts['jp_email'];
									$company = $accounts['pc_name'];
									$makler_id = $accounts['jp_makler_id'];
									$broj_direkcije = $accounts['jp_direction_number'];
									$is_active = $accounts['jp_confirmedaccount'];
									$deactivation_date = $accounts['jp_datum_deaktivacije'];
									$tutorial_sent = $accounts['jp_tutorial_sent'];
									$representative_employee_id = $accounts['jp_representative_employee_id'];
									$latest_activity = $accounts['jp_latest_activity'];


									$language = $accounts['jp_lang'];
									if($language == 'de'){
										$language = "Njemački";
									}else if($language == "en"){
										$language = "Engleski";
									}

									$representative_employee = $accounts['employee_firstname'].' '.$accounts['employee_lastname'];

									$register_date = date('d.m.y H:i', strtotime($accounts['jp_register_date']));
									$deactivation_date_formated = date('d.m.y H:i', strtotime($deactivation_date));

									$first_login_date ='Nikada';
									$latest_loggin_date = 'Nikada';
									$latest_activity_text = 'Nikada';

									if(!is_null($accounts['jp_first_login'])){
											$first_login_date = date('d.m.y H:i', strtotime($accounts['jp_first_login']));
									}
									else if(date('Y-m-d',strtotime($accounts['jp_register_date'])) < date('Y-m-d', strtotime('2024-03-04'))){
										$first_login_date = 'N/A';
									}
								

									if(!is_null($accounts['jp_latest_login'])){
											$latest_loggin_date = date('d.m.y H:i', strtotime($accounts['jp_latest_login']));
									}
									else if(date('Y-m-d',strtotime($accounts['jp_register_date'])) < date('Y-m-d', strtotime('2024-03-04'))){
										$latest_loggin_date = 'N/A';
									}

									if(!is_null($latest_activity)){
											$latest_activity_text = date('d.m.y H:i', strtotime($latest_activity));
									}
									else if(date('Y-m-d',strtotime($accounts['jp_register_date'])) < date('Y-m-d', strtotime('2024-03-04'))){
										$latest_activity_text = 'N/A';
									}


									$last_password_reset = $accounts['jp_last_password_reset'];
									if(is_null($last_password_reset)){
										$last_password_reset = "Nikada";
									}
									else{
										$last_password_reset = date('d.m.y H:i', strtotime($accounts['jp_last_password_reset']));
									}

									$active_text = "Deaktiviraj";
									$active_icon = "times";
									$active_output = '<i style = "color:green" class="fa fa-check" aria-hidden="true"></i> Aktivan';
									
									if(!$is_active){
										if(is_null($deactivation_date)){
											$active_output = '<b style = "color:blue;">Pending...<b>';
										}else{
											$active_text = "Aktiviraj";
											$active_icon = "check";
											$active_output = '<b style = "color:red;">Deaktiviran: '.$deactivation_date_formated.'</b>';
										}
									}

									$tutorial_sent_text = '<a href="../partner-app/tutorials/'.$tutorial_sent.'" target="_blank">'.$tutorial_sent.'</a>';

									if(is_null($tutorial_sent)){
										$tutorial_sent_text = "<b>N/A</b>";
									}
									echo "<tr>";
									echo "<td>".$full_name."</td>";
									echo "<td>".$email."</td>";
									echo '<td data-order="'.$accounts['jp_register_date'].'">'.$register_date.'</td>';
									echo "<td>".$language."</td>";
									echo "<td>".$company."</td>";
									echo "<td>".$makler_id."</td>";
									echo "<td>".$broj_direkcije."</td>";
									echo '<td data-order="'.$accounts['jp_last_password_reset'].'">'.$last_password_reset.'</td>';
									echo '<td data-order="'.$accounts['jp_first_login'].'">'.$first_login_date.'</td>';
									echo '<td data-order="'.$accounts['jp_latest_login'].'">'.$latest_loggin_date.'</td>';
									echo '<td style = "text-align: center;" data-order="'.$latest_activity.'">'.$latest_activity_text.'</td>';
									echo "<td>".$representative_employee."</td>";
									echo '<td date-order="'.$deactivation_date.'"style = "text-align:center;">'.$active_output.'</td>';
									?>
									<td style = "text-align:center">
										<div class="btn-group material-btn-group">

											<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
											<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
												<li><a class="material-dropdown-menu__link password_reset" style = "cursor:pointer;" fullname = "<?php echo $full_name;?>" jp_id = "<?php echo $id;?>" jp_mail = "<?php echo $email;?>"><i class="fa fa-refresh" aria-hidden="true"></i> Reset</a></li>
												<?php 
													if($company != "DVAG"){
														?>
														<li><a class="material-dropdown-menu__link account_controll" style = "cursor:pointer;" jp_id = "<?php echo $id;?>" is_active = <?php echo $is_active;?>><i class="fa fa-<?php echo $active_icon; ?>" aria-hidden="true"></i> <?php echo $active_text; ?></a></li>
														<?php
													}
													?>
												<li><a class="material-dropdown-menu__link edit_makler" style = "cursor:pointer;"
													firstname = "<?php echo $firstname;?>"
													lastname = "<?php echo $lastname;?>"
													language = "<?php echo $accounts['jp_lang'];?>"
													makler_id = "<?php echo $makler_id;?>"
													broj_direkcije = "<?php echo $broj_direkcije;?>"
													representative_employee = "<?php echo $representative_employee_id;?>"
													actual_id = "<?php echo $id;?>"
												><i class="fa fa-pencil" aria-hidden="true"></i> Uredi</a></li>
											</ul>
										</div>
									</td>
									<?php
									echo "</tr>";
								}
							?>
						</tbody>
					</table>
					<?php
				break;

				case "account_controll":
				
					$id = $_POST['id'];
					$is_active = !$_POST['is_active'];

					$query_update = $db -> prepare("
						UPDATE idk_jobstep_partners
						SET jp_confirmedaccount = :is_active, jp_datum_deaktivacije = now()
						WHERE jp_id = :id
					");

					$query_update -> execute(array(
						':is_active' => number_format($is_active),
						':id' => $id
					));

					$log_desc = "Status partner računa: $id promijenjen na: $is_active";
					$log_type = "0";
					addToLogs($log_desc, $log_type); 
				break;
				
				case "is_duplicate_partner_app_email":
				
					$email = $_POST['email'];

					$query_check = $db -> prepare("
						SELECT count(jp_email) as duplicate_email
						FROM idk_jobstep_partners
						WHERE jp_email LIKE ('$email')
					");

					$query_check -> execute();

					$result = $query_check -> fetch();

					echo $result['duplicate_email'];
				break;

				case "set_default_representative_employee":
					$employee_id = $_POST['id'];
					$reset_default_tutorial = $db -> prepare('UPDATE idk_employees SET employee_makler_representative_status = 1 WHERE employee_makler_representative_status = 2');
					$reset_default_tutorial -> execute();

					$set_default_tutorial = $db -> prepare('UPDATE idk_employees SET employee_makler_representative_status = 2 WHERE employee_id = :employee_id');
					$set_default_tutorial -> execute(array(':employee_id' => $employee_id));
				break;

				case "unfreeze_candidate":
					$kandidat_id = $_POST['kandidat_id'];
					updateCandidateAvailability($kandidat_id, 0);

					$query_deactivate = $db->prepare("
						UPDATE idk_nedostupan_log
						SET	status = 0
						WHERE kandidat = :kandidat AND status = 1 AND brojac = 4
					");

					$query_deactivate->execute(array(
						':kandidat' => $kandidat_id
					));

					$log_desc = "Odmrznuo kandidata: " .getCandidateFullnameR($kandidat_id)." ID: [$kandidat_id].";
					addToLogs($log_desc,0);
				break;

				case "confirm_candidate_number":
					$kandidat_id = $_POST['kandidat_id'];
					updateCandidateWrongNumber($kandidat_id, 0);

					$log_desc = "Označio da je broj tačan za kandidata: " .getCandidateFullnameR($kandidat_id)." ID: [$kandidat_id].";
					addToLogs($log_desc,0);
				break;

				case "check_nalog_smjerovi_settings":
					$nalog_id = $_POST['nalog_id'];
					
					echo getNalogSmjeroviCntR($nalog_id);
				break;

				case "get_agent_incall_with_candidate":
					$kandidat_id = $_POST["kandidat_id"];
					
					echo getAgentReservedInCallWithCandidate($kandidat_id, $logged_employee_id);
				break;

				case "get_appointment_details_for_nalog":
					$nalog_id = $_POST["nalog_id"];

					$casting_id = getCastingIdForNalog($nalog_id);																
					$formatted_casting_ids = implode(', ', $casting_id);
					
					$query_get_appointment = $db->prepare("SELECT
															pap_date,
															idk_pp_appointments.pap_id,
															pap_city,
															date_sub(pap_date, INTERVAL pap_first_sending_number_days day) as ending_date,
															CASE
																WHEN DATE_FORMAT(date_sub(pap_date, INTERVAL pap_first_sending_number_days day), '%Y-%m-%d 09:00:00') < NOW() THEN 'getDateSecondMessage'
																ELSE 'getDateFirstMessage'
															END AS terminPozivaEndpoint 
														FROM
															idk_pp_appointments
														INNER JOIN idk_pp_appointment_hours ON idk_pp_appointments.pap_id = idk_pp_appointment_hours.pap_id
														LEFT JOIN idk_pp_cand_appts ON idk_pp_appointments.pap_id = idk_pp_cand_appts.pca_appointment_id
														WHERE
															pap_group_id IN ($formatted_casting_ids) AND pap_date > NOW() - INTERVAL 1 DAY
														GROUP BY idk_pp_appointments.pap_id
														");
					$query_get_appointment->execute();	
					$appointments = $query_get_appointment->fetchAll(PDO::FETCH_ASSOC);	
					$appointment_counter = $query_get_appointment->rowCount();

					$result = array();
					if($appointment_counter > 0){
						$result["tf_status"] = 16;
						$result["tf_status_naziv"] = "Pristao";
						$result["casting_appointments"] = $appointments;
					}else{
						$result["tf_status"] = 14;
						$result["tf_status_naziv"] = "Zainteresiran";
						$result["casting_appointments"] = [];
					}

					echo json_encode($result);

				break;
			}
        ?>