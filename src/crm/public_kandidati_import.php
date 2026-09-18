<?php
header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST');

header("Access-Control-Allow-Headers: X-Requested-With");
	include("includes/functions.php");
	
	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: registracija/korak1");
	}
	
	switch ($page){

		case "getLanguageForLink":
			$link_id = $_POST['link_id'];
			$get_lang = $db->prepare("SELECT lg_language FROM idk_link_generator WHERE lg_id = $link_id");
			$get_lang->execute();
			$row_lang = $get_lang->fetch();

			echo $row_lang['lg_language'];
		
		break;

		case "getQuestionsForForm":
			$link_id = $_POST['link_id'];
			// fetch configured questions for this link (if any)
			$stmt = $db->prepare("
				SELECT lq.lq_question_id, lq.lq_is_required, q.qff_short_name
				FROM idk_link_questions lq
				JOIN idk_questions_for_form q ON q.qff_id = lq.lq_question_id
				WHERE lq.lq_link_id = ?
			");
			$stmt->execute([$link_id]);
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

			// If none found, load defaults from questions_for_form
			if (empty($rows)) {
				$stmt = $db->prepare("
					SELECT qff_id AS lq_question_id, qff_default AS lq_is_required, qff_short_name
					FROM idk_questions_for_form
					WHERE qff_is_active = 1
				");
				$stmt->execute();
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			}

			// Map short_name => 0/1
			$questions_map = [];
			foreach ($rows as $r) {
				$short = $r['qff_short_name'];
				$questions_map[$short] = (int)$r['lq_is_required'];
			}

			$query = $db->prepare("
				SELECT id
				FROM idk_nalog_smjer ns
				JOIN idk_link_generator lg ON lg.lg_nalogid = ns.nalog_id
				WHERE lg.lg_id = :link_id
			");
			$query->execute([':link_id' => $link_id]);

			$nalog_mode = $query->rowCount() > 0 ? 1 : 0;

			// --- FORM TEXT DATA ---
			$stmt = $db->prepare("
				SELECT lg_naslov_na_formi, lg_tekst_na_formi
				FROM idk_link_generator
				WHERE lg_id = :link_id
				LIMIT 1
			");
			$stmt->execute([':link_id' => $link_id]);
			$linkData = $stmt->fetch(PDO::FETCH_ASSOC);

			// --- ADDITIONAL QUESTIONS (DODATNA PITANJA) ---
			$stmt = $db->prepare("
				SELECT dp.dp_id, dp.dp_tekst
				FROM idk_link_dodatna_pitanja ldp
				JOIN idk_dodatna_pitanja dp ON dp.dp_id = ldp.ldp_dp_id
				WHERE ldp.ldp_link_id = ?
				ORDER BY dp.dp_id
			");
			$stmt->execute([(int)$link_id]);
			$dodatna_pitanja = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
			// Build response with keys your frontend expects.  
			// If a question doesn't exist in DB map, default to 0 (hidden) except for some you want true-by-default.
			$response = [
				'questions'        => $questions_map,
				'nalog_mode'       => $nalog_mode,
				'naslov'           => $linkData['lg_naslov_na_formi'] ?? '',
				'tekst'            => $linkData['lg_tekst_na_formi'] ?? '',
				'dodatna_pitanja'  => $dodatna_pitanja
			];

			echo json_encode($response);
			exit;
		
		break;
		
		case "getNalogOpis":
			$link_id = $_POST['link_id'];
			$lang = $_POST['lang'];
			$get_nalog_opis = $db->prepare(
				"SELECT no_nalogopis FROM idk_nalozi_opis JOIN idk_link_generator lg ON no_nalogid = lg.lg_nalogid AND (lg.lg_partner_app = 1 OR lg.lg_nalogid = 44) WHERE lg.lg_id = $link_id AND no_lang = '$lang' "
			);
			$get_nalog_opis->execute();
			$row_nalog_opis = $get_nalog_opis->fetch();

			echo $row_nalog_opis['no_nalogopis'];
		
		break;

		case "getNalogTitle":
			$link_id = $_POST['link_id'];
			$lang = $_POST['lang'];
			$get_nalog_opis = $db->prepare(
				"SELECT no_nalognaziv FROM idk_nalozi_opis JOIN idk_link_generator lg ON no_nalogid = lg.lg_nalogid AND lg.lg_partner_app = 1 WHERE lg.lg_id = $link_id AND no_lang = '$lang' "
			);
			$get_nalog_opis->execute();
			$row_nalog_opis = $get_nalog_opis->fetch();

			echo $row_nalog_opis['no_nalognaziv'];
		
		break;

		case "getNalogLocation":
			$link_id = $_POST['link_id'];
			$lang = $_POST['lang'];
			$get_nalog_opis = $db->prepare(
				"SELECT nalog_partner_app_location FROM idk_nalozi JOIN idk_link_generator lg ON idk_nalozi.nalog_id = lg.lg_nalogid AND lg.lg_partner_app = 1 WHERE lg.lg_id = $link_id"
			);
			$get_nalog_opis->execute();
			$row_nalog_opis = $get_nalog_opis->fetch();

			echo $row_nalog_opis['nalog_partner_app_location'];

		break;
		
		case "getNalogImage":
			$link_id = $_POST['link_id'];
			$lang = $_POST['lang'];
			$get_nalog_opis = $db->prepare(
				"SELECT idk_urlimg_prijave FROM idk_nalozi JOIN idk_link_generator lg ON idk_nalozi.nalog_id = lg.lg_nalogid AND lg.lg_partner_app = 1 WHERE lg.lg_id = $link_id"
			);
			$get_nalog_opis->execute();
			$row_nalog_opis = $get_nalog_opis->fetch();

			echo $row_nalog_opis['idk_urlimg_prijave'];
		
		break;
		
		case "return_skole":

			$query_skole = $db->prepare("
					SELECT skola_id, skola_tip_obrazovanja, skola_naziv FROM idk_skole WHERE skola_tip_obrazovanja = 'srednje' ORDER BY skola_naziv
			");
			$query_skole->execute();
			$select_skole_txt = "";
			while($row_skola = $query_skole->fetch()){
				$skola_tip = $row_skola['skola_tip_obrazovanja'];
				$skola_id = $row_skola['skola_id'];
				$skola_naziv = $row_skola['skola_naziv'];
				$select_skole_txt = $select_skole_txt.'<option value="'.$skola_id.'" data-skola_id="'.$skola_id.'">'.$skola_naziv.'</option>';
			}
			$select_skole_txt = $select_skole_txt.'<option value="ostalo">Ostalo</option>';
			echo $select_skole_txt;
		break;

		case "return_smjerove":

			$query_skole_smjer = $db->prepare("
					SELECT ss_id, ss_skola_id, ss_naziv FROM idk_skole_smjerovi ORDER BY ss_naziv
			");
			$query_skole_smjer->execute();
			$select_smjer_txt = "";
			while($row_smjer = $query_skole_smjer->fetch()){
				$smjer_id = $row_smjer['ss_id'];
				$ss_naziv = $row_smjer['ss_naziv'];
				$ss_skola_id = $row_smjer['ss_skola_id'];
				$select_smjer_txt = $select_smjer_txt.'<option value="'.$smjer_id.'" class="hidden smjerovi_all opt_'.$ss_skola_id.'">'.$ss_naziv.'</option>';
			}
			echo $select_smjer_txt;
		
		break;
		
		case "return_smjerove_for_school_level":

			if(!isset($_REQUEST['urlid'])){
				http_response_code(400);
				die("Missing parameters!");
			}

			if(!isset($_REQUEST['nivo_obrazovanja'])){
				http_response_code(400);
				die("Missing parameters!");
			}

			if(!isset($_REQUEST['lang'])){
				http_response_code(400);
				die("Missing parameters!");
			}

			$urlid = $_REQUEST['urlid'];
			$nivo_obrazovanja = $_REQUEST['nivo_obrazovanja'];
			$lang = $_REQUEST['lang'];

			if($nivo_obrazovanja == 1){
				$skola_tip_obrazovanja = "visoko";
			}else{
				$skola_tip_obrazovanja = "srednje";
			}

			if($lang == "de"){
				$txt_ostalo = "Sonstiges";
				$txt_odaberi = "Wählen";
			}elseif($lang == "en"){
				$txt_ostalo = "Other";
				$txt_odaberi = "Select";
			}else{
				$txt_ostalo = "Ostalo";
				$txt_odaberi = "Odaberi";
			}

			$query_skole_smjer = $db->prepare("SELECT 
												smjer_id,
												idk_skole_smjerovi.ss_naziv,
												idk_skole_smjerovi.ss_naziv_de,
												idk_skole_smjerovi.ss_naziv_en,
												skola_tip_obrazovanja
											FROM
												idk_nalog_smjer
											JOIN idk_link_generator ON idk_nalog_smjer.nalog_id = idk_link_generator.lg_nalogid
											JOIN idk_skole_smjerovi ON idk_nalog_smjer.smjer_id = idk_skole_smjerovi.ss_id
											JOIN idk_skole ON idk_skole_smjerovi.ss_skola_id = idk_skole.skola_id
											WHERE
												lg_id = :urlid AND skola_tip_obrazovanja = '$skola_tip_obrazovanja' 
												ORDER BY
												CASE
													WHEN :lang = 'bs' THEN idk_skole_smjerovi.ss_naziv
													WHEN :lang = 'de' THEN idk_skole_smjerovi.ss_naziv_de
													WHEN :lang = 'en' THEN idk_skole_smjerovi.ss_naziv_en
													ELSE idk_skole_smjerovi.ss_naziv_de 
												END");
			$query_skole_smjer->execute([
				":urlid" => $urlid,
				":lang" => $lang
			]);
			
			$select_smjer_txt = '<option value="" selected>'.$txt_odaberi.'...</option>';
			while($row_smjer = $query_skole_smjer->fetch()){
				$smjer_id = $row_smjer['smjer_id'];
				if($lang == "de"){
					$naziv_smjera = $row_smjer['ss_naziv_de'];
				}elseif($lang == "en"){
					$naziv_smjera = $row_smjer['ss_naziv_en'];
				}else{ 
					$naziv_smjera = $row_smjer['ss_naziv'];
				}
				$select_smjer_txt = $select_smjer_txt.'<option value="'.$smjer_id.'">'.$naziv_smjera.'</option>';
			}

			$select_smjer_txt = $select_smjer_txt.'<option value="ostalo">'.$txt_ostalo.'</option>';

			echo $select_smjer_txt;
		break;

		case "return_smjerove_by_skola_id":
			if(!isset($_REQUEST['skola_id'])){
				http_response_code(400);
				die("Missing parameters!");
			}

			$skola_id = $_REQUEST['skola_id'];
			$query_skole_smjer = $db->prepare("
					SELECT ss_id, ss_skola_id, ss_naziv FROM idk_skole_smjerovi WHERE ss_skola_id = :ss_skola_id ORDER BY ss_naziv
			");
			$query_skole_smjer->execute([
				":ss_skola_id" => $skola_id
			]);

			$select_smjer_txt = "";
			while($row_smjer = $query_skole_smjer->fetch()){
				$smjer_id = $row_smjer['ss_id'];
				$ss_naziv = $row_smjer['ss_naziv'];
				$ss_skola_id = $row_smjer['ss_skola_id'];
				$select_smjer_txt = $select_smjer_txt.'<option value="'.$smjer_id.'" class="smjerovi_all opt_'.$ss_skola_id.'">'.$ss_naziv.'</option>';
			}
			echo $select_smjer_txt;
		
		break;

		case "return_all_groups_select":
			$query_job_type = $db->prepare("
							SELECT kg_id, kg_title
							FROM idk_kandidati_grupe
							");

			$query_job_type->execute();
			$select_txt = "";
			while($job_type = $query_job_type->fetch()){

				$kg_id = $job_type['kg_id'];
				$kg_title = $job_type['kg_title'];
				$select_txt = $select_txt.'<option value="'.$kg_id.'">'.$kg_title.'</option>';
			}
			echo $select_txt;

		break;
		case "return_groups_select":
			$link_id = $_POST['link_id'];
			$query_job_type = $db->prepare("
						SELECT lr_groupid, kg_title
						FROM idk_link_generator_rel
						INNER JOIN idk_kandidati_grupe ON idk_link_generator_rel.lr_groupid = idk_kandidati_grupe.kg_id
						WHERE lr_lgid = :lr_lgid
						");

			$query_job_type->execute(array(
			":lr_lgid" => $link_id
			));
			$select_txt = "";
			while($job_type = $query_job_type->fetch()){

				$lr_groupid = $job_type['lr_groupid'];
				$kg_title = $job_type['kg_title'];
				$select_txt = $select_txt.'<option value="'.$lr_groupid.'">'.$kg_title.'</option>';
				
			}
			echo $select_txt;

		break;

		case "check_nalog_smjer":
			$link_id = $_POST['link_id'];
			$query = $db->prepare("
				SELECT id FROM idk_nalog_smjer 
				JOIN idk_link_generator ON lg_nalogid = nalog_id
				WHERE lg_id = :link_id
			");
			$query->execute(array(':link_id' => $link_id));

			echo $query->rowCount();
		
		break;

		case "import_google_forma":

			include("lang/bs.php");
			//predefinisani parametri vezani za link
			$urlid = $_REQUEST['urlid'];
			$kandidat_group = $_REQUEST['kandidat_prijava_na'];
			$partner_token = $_REQUEST['token'];
			$lg_nalogid = $_REQUEST['nalog_id'];

			//parametri sa forme (excel file-a)
			$datum_prijave = $_REQUEST['datum_prijave'];
			$kandidat_ime = $_REQUEST['kandidat_ime'];
			$kandidat_prezime = $_REQUEST['kandidat_prezime'];
			$mobile_phone = $_REQUEST['kki_phone'];
			$dan_rodjenja = $_REQUEST["datum_dan"];
			$mjesec_rodjenja = $_REQUEST["datum_mjesec"];
			$godina_rodjenja = $_REQUEST["datum_godina"];
			$post_nivo_jezika = $_REQUEST['nivo_jezika'];
			if(isset($_REQUEST['kandidat_email'])){
				$kandidat_email = $_REQUEST['kandidat_email'];
			}else{
				$kandidat_email = null;
			}
			if(isset($_REQUEST['kategorija_vozacke'])){
				$vozacka = $_REQUEST['kategorija_vozacke'];
				if (in_array('Ne', $vozacka)){
					$kandidat_vozacka_dozvola = "Ne";
					$kandidat_vozacka_kategorija = null;
				}else{
					$kandidat_vozacka_dozvola = "Da";
					$kandidat_vozacka_kategorija = implode(',',$vozacka);
				}

			}else{
				$kandidat_vozacka_dozvola = null;
				$kandidat_vozacka_kategorija = null;
			}
			// addToLogs(implode(",", $kandidat_vozacka_kategorija), "0");

			//kreiranje inputa za bazu na osnovu parametara sa forme
			$kandidat_full_name = $kandidat_ime." ".$kandidat_prezime;
			$kandidat_datumrodjenja = date('Y-m-d', strtotime($godina_rodjenja."-".$mjesec_rodjenja."-".$dan_rodjenja));
			$kandidat_datetime = date('Y-m-d H:i:s', strtotime($datum_prijave));

			//napravi username po imenu, prezimenu i godini
			$ime_korime = strtolower($kandidat_ime);
			$prezime_korime = strtolower($kandidat_prezime);
			$imeprezime = $ime_korime.''.$prezime_korime;
			$search = array("ć", "č", "ž", "š", "đ", " ");
			$replacement = array("c", "c", "z", "s", "dj", "");
			$imeprezime_korime = str_replace($search, $replacement, $imeprezime);

			//kupi se naziv grupe
			$group_title_query = $db->prepare("
							SELECT kg_id, kg_title
							FROM idk_kandidati_grupe
							WHERE kg_id = :kg_id
							");
	
			$group_title_query->execute(array(
				":kg_id" => $kandidat_group
			));
			$row_kg_title = $group_title_query->fetch();
			$kandidat_prijava_na = $row_kg_title['kg_title'];
			
			$kandidat_check = md5(uniqid(rand(), true));
			
			// Provjera da li postoji korisnik sa istim imenom i prezimenom u zadnjih sat vremena
			$check_user = $db->prepare("
									SELECT kandidat_ime, kandidat_id, kandidat_status_messenger, kandidat_check, kandidat_visitedurl, kandidat_status
									FROM idk_kandidati
									WHERE kandidat_ime = :kandidat_ime AND kandidat_prezime = :kandidat_prezime AND kandidat_mobitel = :kandidat_mobitel AND kandidat_status != 3");
		
			$check_user->execute(array(
							':kandidat_ime' => $kandidat_ime,
							':kandidat_prezime' => $kandidat_prezime,
							':kandidat_mobitel' => $mobile_phone
							));
		
			$number_of_rows_user = $check_user->rowCount();

			if($number_of_rows_user == 0){
				
				$random_token = rand(100, 999);
				$kandidat_korisnickoime_uf = $imeprezime_korime.''.$random_token;
				$kandidat_korisnickoime = str_replace(' ', '', strtolower($kandidat_korisnickoime_uf));
				$kandidat_password = MD5($kandidat_korisnickoime);
				
				$kandidat_status = 0;
				$kandidat_status_messenger = 1;
				$kandidat_slika_final = "none";
				
				//ADD PARTNER DATA	
				if($partner_token != null){
					$partner_query = $db->prepare("
									SELECT jp_id, jp_fcmtoken, jp_position, jp_imeprezime
									FROM idk_jobstep_partners
									WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token
									");
			
						$partner_query->execute(array(
							":jp_mailconfirmation_token" => $partner_token
					));
			
					$row_partner = $partner_query->fetch();
					$partner_id = $row_partner['jp_id'];
					$partner_position = $row_partner['jp_position'];
					$jp_fcmtoken = $row_partner['jp_fcmtoken'];
					$partner_ime = $row_partner['jp_imeprezime'];
					$log_poruka = "Novi kandidat ".$kandidat_ime." je prijavljen preko vašeg linka.";
					send_notification_partnerapp($jp_fcmtoken, $partner_ime, $log_poruka);
					$kandidat_porijeklo = 6;
				}else{
					$partner_id = null;
					$partner_position = null;
					$kandidat_porijeklo = 0;
				}
				
				//Add user to db
				$query = $db->prepare("
								INSERT INTO idk_kandidati
									( kandidat_check, kandidat_ime, kandidat_prezime, kandidat_email, kandidat_mobitel, kandidat_password, kandidat_slika, kandidat_status, kandidat_status_messenger, kandidat_datetime, kandidat_korisnickoime, kandidat_datumrodjenja, kandidat_visitedurl, kandidat_prijava_na, kandidat_group, kandidat_partnerid, kandidat_partner_status, kandidat_porijeklo, kandidat_vozacka_dozvola, kandidat_vozacka_kategorija, kandidat_status_prijave)
								VALUES
									(:kandidat_check,:kandidat_ime,:kandidat_prezime,:kandidat_email,:kandidat_mobitel,:kandidat_password,:kandidat_slika,:kandidat_status,:kandidat_status_messenger,:kandidat_datetime,:kandidat_korisnickoime,:kandidat_datumrodjenja,:kandidat_visitedurl,:kandidat_prijava_na,:kandidat_group,:kandidat_partnerid,:kandidat_partner_status,:kandidat_porijeklo,:kandidat_vozacka_dozvola,:kandidat_vozacka_kategorija,:kandidat_status_prijave)");
		
				$query->execute(array(
							':kandidat_check' => $kandidat_check,
							':kandidat_ime' => $kandidat_ime,
							':kandidat_prezime' => $kandidat_prezime,
							':kandidat_email' => $kandidat_email,
							':kandidat_mobitel' => $mobile_phone,
							':kandidat_password' => $kandidat_password,
							':kandidat_slika' => 'none',
							':kandidat_status' => 0,
							':kandidat_status_messenger' => 1,
							':kandidat_datetime' => $kandidat_datetime,
							':kandidat_korisnickoime' => $kandidat_korisnickoime,
							':kandidat_datumrodjenja' => $kandidat_datumrodjenja,
							':kandidat_visitedurl' => $urlid,
							':kandidat_prijava_na' => $kandidat_prijava_na,
							':kandidat_group' => $kandidat_group,
							':kandidat_partnerid' => $partner_id,
							':kandidat_partner_status' => $partner_position,
							':kandidat_porijeklo' => $kandidat_porijeklo,
							':kandidat_vozacka_dozvola' => $kandidat_vozacka_dozvola,
							':kandidat_vozacka_kategorija' => $kandidat_vozacka_kategorija,
							':kandidat_status_prijave' => 2
							));
							
				$kandidat_id = $db->lastInsertId();
				
				$query_log_status = $db->prepare("
						INSERT INTO idk_log_kandidat_statusi
							(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime, lks_link_id)
						VALUES
							(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime, :lks_link_id)
				");
				
				$query_log_status->execute(array(
						':lks_kandidat_id' => $kandidat_id,
						':lks_status_obrade' => 0,
						':lks_status_messenger' => 1,
						':lks_datetime' => $kandidat_datetime,
						':lks_link_id' => $urlid
				));

				//INSERT BILJESKI
				
				if(isset($_REQUEST['biljeske'])){
					$note_txt_cert = $_REQUEST['biljeske'];
					
					$note_datetime = date('Y-m-d H:i:s');
					$query_biljeske = $db->prepare("
									INSERT INTO idk_notes
										(note_txt, note_datetime, note_group, note_dataid, note_employeeid)
									VALUES
										(:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");

					$query_biljeske->execute(array(
								':note_txt' => $note_txt_cert,
								':note_datetime' => date('Y-m-d H:i:s'),
								':note_group' => 2,
								':note_dataid' => $kandidat_id,
								':note_employeeid' => 67));
				}
				
				//insert jezika
				if($post_nivo_jezika == "Bez poznavanja")
					$kj_znanje_njemacki = "Bez znanja";
				else
					$kj_znanje_njemacki = $post_nivo_jezika;
				
				$kj_naziv_njemacki = "Njemački";
				// Add language knowlege
				$query_njem = $db->prepare("
								INSERT INTO idk_kandidat_jezici
									(kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid)
								VALUES
									(:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid)");
		
				$query_njem->execute(array(
							':kj_naziv' => $kj_naziv_njemacki,
							':kj_slusanje' => $kj_znanje_njemacki,
							':kj_citanje' => $kj_znanje_njemacki,
							':kj_govorna_interakcija' => $kj_znanje_njemacki,
							':kj_govorna_produkcija' => $kj_znanje_njemacki,
							':kj_pisanje' => $kj_znanje_njemacki,
							':kj_kandidatid' => $kandidat_id
				));

				//VEZANJE KANDIDATA ZA PROJEKT
				if($lg_nalogid != 0){
					$nalog_query = $db->prepare("
											SELECT project_id
											FROM idk_projects
											WHERE project_nalogid = :project_nalogid AND (project_name LIKE '%Prijave%' OR project_name LIKE '%Prijava%') ");
				
					$nalog_query->execute(array(
									':project_nalogid' => $lg_nalogid));
				
					$nalogrow = $nalog_query->fetch();
					if(!$nalogrow){
						//sta raditi ako nema projekta "PRIJAVA"
						//IDU U NOVE PROJEKTI ZA VIBER NALOG
					}else{
						
						$project_id = $nalogrow['project_id'];	
						
						$query_project = $db->prepare("
										INSERT INTO idk_project_kandidati
											(pk_projectid, pk_kandidatid)
										VALUES
											(:pk_projectid, :pk_kandidatid)");
			
						$query_project->execute(array(
										':pk_projectid' => $project_id,
										':pk_kandidatid' => $kandidat_id
						));
						addToLogsStatusPrijave(NULL, $project_id, 2, $kandidat_id, 3);
					}
				}
				
				//Add mobilni to db
				$query_mob = $db->prepare("
								INSERT INTO idk_kandidat_kontakt_info
									(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
								VALUES
									(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

				$query_mob->execute(array(
							':kki_grupa' => 1,
							':kki_naziv' => 'Mobilni',
							':kki_podatak' => $mobile_phone,
							':kki_kandidat_id' => $kandidat_id
				));

				//Add to table users (chatbot)
				$random_string = generateRandomString();
				$options = [
					'cost' => 10,
				];
				$random_password = password_hash($random_string, PASSWORD_BCRYPT, $options);
				
				//Add to logs candidate IP	
				$date_time_ip = date("F j, Y, g:i T");
				if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
					$ip = $_SERVER['HTTP_CLIENT_IP'];
				} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
					$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				} else {
					$ip = $_SERVER['REMOTE_ADDR'];
				}
				$log_desc = "Kandidat: " . $kandidat_ime . " " . $kandidat_prezime . "(".$kandidat_id."). ".$date_time_ip."; IP: ".$ip." (".$random_string.")";
				$log_type = "5";
				addToLogs($log_desc, $log_type);
				
				$characters = '0123456789';
				$charactersLength = strlen($characters);
				$randomString = '';
				for ($i = 0; $i < 5; $i++) {
					$randomString .= $characters[rand(0, $charactersLength - 1)];
				}
				$bot_koriscnicko_ime = $kandidat_ime.$randomString;
				
				$query_user = $db->prepare("
							INSERT INTO users
								(phone, name, nalog_id, email, password, kandidat_id)
							VALUES
								(:phone, :name, :nalog_id, :email, :password, :kandidat_id)");

				$query_user->execute(array(
							':phone' => $mobile_phone,
							':name' => $kandidat_full_name,
							':nalog_id' => $lg_nalogid,
							':email' => $bot_koriscnicko_ime,
							':password' => $random_password,
							':kandidat_id' => $kandidat_id));
				
				
				$phone_f = str_replace("+", '', $mobile_phone);
				
				$link_dload = "https://crm.job-step.com/download";
				$link_uputs = "https://bit.ly/3V177tF";

				$kandidat_full_name = getCandidateFullnameR($kandidat_id);

				$to_send_viber_poruka_1 = $kandidat_full_name.$text_viber_new_prva_poruka; 
				$to_send_sms_poruka_1 	= $kandidat_full_name.$text_sms_new_prva_poruka; 

				$to_send_viber_poruka_2 = $text_viber_new_druga_poruka;
				$to_send_sms_poruka_2 	= $text_sms_new_druga_poruka." ".$link_dload;

				$to_send_viber_poruka_3 = $text_viber_new_treca_poruka_1.'\n\n'.$text_viber_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_viber_new_treca_poruka_3.$random_string;
				$to_send_sms_poruka_3 	= $text_sms_new_treca_poruka_1.'\n\n'.$text_sms_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_sms_new_treca_poruka_3.$random_string;
				// var_dump($to_send_viber_poruka_3);
				// var_dump($to_send_sms_poruka_3);
				// exit();
				viberPrijava1($phone_f, $to_send_sms_poruka_1, $to_send_viber_poruka_1);
				sleep(1);
				viberPrijava2($phone_f, $to_send_sms_poruka_2, $to_send_viber_poruka_2, $text_btn_new_druga_poruka, $link_dload);
				sleep(1);
				viberPrijava1($phone_f, $to_send_sms_poruka_3, $to_send_viber_poruka_3);
				sleep(1);

				$to_send_viber_poruka_4 = $text_viber_new_cetvrta_poruka;
				$to_send_sms_poruka_4 	= $text_sms_new_cetvrta_poruka." ".$link_uputs;
				
				viberPrijava2($phone_f, $to_send_sms_poruka_4, $to_send_viber_poruka_4, $text_btn_new_cetvrta_poruka, $link_uputs);
				
				if($kandidat_email != null)
					sendCandidateMessengerMail($random_string, $kandidat_email, $bot_koriscnicko_ime);
				checkCandidateInputs($kandidat_id);
				
				notifForOnlneRegister($kandidat_id, $kandidat_check);

			}else{
				$rows_user = $check_user->fetch();
				$kandidat_id_c = $rows_user['kandidat_id'];
				$kandidat_status_messenger = $rows_user['kandidat_status_messenger'];
				$kandidat_check_exists = $rows_user['kandidat_check'];
				$stari_visitedurl = $rows_user['kandidat_visitedurl'];
				$kandidat_status = $rows_user['kandidat_status'];
				
				$kandidat_slika_final = "none";
				
				$check_messenger = $db->prepare("
										SELECT id, email, phone
										FROM users
										WHERE kandidat_id = :kandidat_id ");
			
				$check_messenger->execute(array(
								':kandidat_id' => $kandidat_id_c
								));
			
				$nr_of_rows_mess = $check_messenger->rowCount();
				if($nr_of_rows_mess == 0){
					$kandidat_status_messenger = 1;
				}else{}

				//ADD PARTNER DATA
				if($partner_token != null){
					$partner_query = $db->prepare("
									SELECT jp_id, jp_fcmtoken, jp_position, jp_imeprezime
									FROM idk_jobstep_partners
									WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token
									");
			
						$partner_query->execute(array(
							":jp_mailconfirmation_token" => $partner_token
					));
			
					$row_partner = $partner_query->fetch();
					$partner_id = $row_partner['jp_id'];
					$partner_position = $row_partner['jp_position'];
					$jp_fcmtoken = $row_partner['jp_fcmtoken'];
					$partner_ime = $row_partner['jp_imeprezime'];
					$log_poruka = "Novi kandidat ".$kandidat_ime." je prijavljen preko vašeg linka.";
					send_notification_partnerapp($jp_fcmtoken, $partner_ime, $log_poruka);
					$kandidat_porijeklo = 7;
				}else{
					$partner_id = null;
					$partner_position = null;
					$kandidat_porijeklo = 0;
				}

				//Update user
				$update_user = $db->prepare("
								UPDATE idk_kandidati
								SET kandidat_status_messenger = :kandidat_status_messenger, kandidat_datumrodjenja = :kandidat_datumrodjenja, kandidat_visitedurl = :kandidat_visitedurl, kandidat_prijava_na = :kandidat_prijava_na, kandidat_partnerid = :kandidat_partnerid, kandidat_partner_status = :kandidat_partner_status, kandidat_porijeklo = :kandidat_porijeklo
								WHERE kandidat_id = :kandidat_id
								");
		
				$update_user->execute(array(
							':kandidat_id' => $kandidat_id_c,
							':kandidat_status_messenger' => $kandidat_status_messenger,
							':kandidat_datumrodjenja' => $kandidat_datumrodjenja,
							':kandidat_visitedurl' => $urlid,
							':kandidat_prijava_na' => $kandidat_prijava_na,
							':kandidat_partnerid' => $partner_id,
							':kandidat_partner_status' => $partner_position,
							':kandidat_porijeklo' => $kandidat_porijeklo
				));

				//INSERT/UPDATE JEZIKA
				if(isset($_POST['nivo_jezika'])){
					$kj_naziv_njemacki = "Njemački";
					$kj_znanje_njemacki = $_POST['nivo_jezika'];
					$query_check = $db->prepare("SELECT
													kj_id
												FROM
													idk_kandidat_jezici
												WHERE
													kj_naziv = :kj_naziv
												AND
													kj_kandidatid = :kandidat_id
												");
					$query_check->execute(array(
						":kandidat_id" => $kandidat_id_c,
						":kj_naziv" => $kj_naziv_njemacki
					));
					$count_jezik = $query_check->rowCount();
					$result_query_check = $query_check->fetch();
					if($count_jezik > 0){
						$kj_id = $result_query_check['kj_id'];
						//UPDATE
						$query_njem = $db->prepare("
							UPDATE idk_kandidat_jezici
							SET kj_naziv = :kj_naziv, kj_citanje = :kj_citanje, kj_slusanje = :kj_slusanje, kj_citanje = :kj_citanje, kj_govorna_interakcija = :kj_govorna_interakcija, kj_govorna_produkcija = :kj_govorna_produkcija, kj_pisanje = :kj_pisanje
							WHERE kj_kandidatid = :kj_kandidatid
							");
						$query_njem->execute(array(
									':kj_naziv' => $kj_naziv_njemacki,
									':kj_slusanje' => $kj_znanje_njemacki,
									':kj_citanje' => $kj_znanje_njemacki,
									':kj_govorna_interakcija' => $kj_znanje_njemacki,
									':kj_govorna_produkcija' => $kj_znanje_njemacki,
									':kj_pisanje' => $kj_znanje_njemacki,
									':kj_kandidatid' => $kandidat_id_c));		
					} else {
						//INSERT
						
						// Add language knowlege
						$query_njem = $db->prepare("
										INSERT INTO idk_kandidat_jezici
											(kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid)
										VALUES
											(:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid)");
				
						$query_njem->execute(array(
									':kj_naziv' => $kj_naziv_njemacki,
									':kj_slusanje' => $kj_znanje_njemacki,
									':kj_citanje' => $kj_znanje_njemacki,
									':kj_govorna_interakcija' => $kj_znanje_njemacki,
									':kj_govorna_produkcija' => $kj_znanje_njemacki,
									':kj_pisanje' => $kj_znanje_njemacki,
									':kj_kandidatid' => $kandidat_id_c));
					}
				}
					
				//INSERT BILJESKI
				
				if(isset($_REQUEST['biljeske'])){
					$note_txt_cert = $_REQUEST['biljeske'];
					
					$query_biljeske = $db->prepare("
									INSERT INTO idk_notes
										(note_txt, note_datetime, note_group, note_dataid, note_employeeid)
									VALUES
										(:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");
					
					$query_biljeske->execute(array(
								':note_txt' => $note_txt_cert,
								':note_datetime' => date('Y-m-d H:i:s'),
								':note_group' => 2,
								':note_dataid' => $kandidat_id_c,
								':note_employeeid' => 67));
				}

				//INSERT INTO KANDIDAT LOG STATUSI		
					$query_log_status = $db->prepare("
							INSERT INTO idk_log_kandidat_statusi
								(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime, lks_link_id)
							VALUES
								(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime, :lks_link_id)
					");
					
					$query_log_status->execute(array(
							':lks_kandidat_id' => $kandidat_id_c,
							':lks_status_obrade' => 9,
							':lks_status_messenger' => 0,
							':lks_datetime' => $kandidat_datetime,
							':lks_link_id' => $urlid
					));
				//INSERT INTO KANDIDAT LOG STATUSI

				//INSERT INTO PROJEKAT
							
					$rezervisanFlag = 0;
					if($lg_nalogid != 0){
								
						$nalog_query = $db->prepare("
												SELECT project_id
												FROM idk_projects
												WHERE project_nalogid = :project_nalogid AND (project_name LIKE '%Prijave%' OR project_name LIKE '%Prijava%') ");
					
						$nalog_query->execute(array(
										':project_nalogid' => $lg_nalogid));
					
						$nalogrow = $nalog_query->fetch();
						if(!$nalogrow){
							//sta raditi ako nema projekta "PRIJAVA"
							//IDU U NOVE PROJEKTI ZA VIBER NALOG
							
						}else{
						
							$project_id = $nalogrow['project_id'];	
							$check_project = $db->prepare("
													SELECT pk_projectid
													FROM idk_project_kandidati
													WHERE pk_projectid = :pk_projectid AND pk_kandidatid = :pk_kandidatid");
						
							$check_project->execute(array(
												':pk_projectid' => $project_id,
												':pk_kandidatid' => $kandidat_id_c
							));
							if($check_project->rowCount() == 0){
								
								//Provjera rezervisanosti START
								
								$queryPrijavaNalog = $db->prepare("
									SELECT 
										kandidat_nalog_id, kandidat_status_prijave
									FROM 
										idk_kandidati
									WHERE 
										kandidat_id = :kandidat_id
								");
								$queryPrijavaNalog->execute(array(
									':kandidat_id' => $kandidat_id_c
								));
								$rowPrijavaNalog = $queryPrijavaNalog->fetch();
								$nalogIdPN = intval($rowPrijavaNalog["kandidat_nalog_id"]);
								$statusPrijavePN = intval($rowPrijavaNalog["kandidat_status_prijave"]);
								
								$queryProjekti = $db->prepare("
									SELECT 
										count(pk.pk_id) AS brojac
									FROM 
										idk_project_kandidati pk
									JOIN 
										idk_projects p
									ON 
										pk.pk_projectid = p.project_id
									WHERE 
										pk.pk_kandidatid = :pk_kandidatid
										AND 
										(
											project_name LIKE '%Intervju%' 
											OR 
											project_name LIKE '%Obrađeno%' 
											OR 
											project_name LIKE '%Završeni kandidati%' 
											OR 
											project_name LIKE '%BOT - ispunjava uslove%' 
											OR 
											project_name LIKE '%Baza - odgovara za nalog%'
											OR 
											project_name LIKE '%Casting%' 
											OR 
											project_name LIKE '%Ugovor%'
										)
										AND p.project_nalogid != 44
								");
								$queryProjekti->execute(array(
									':pk_kandidatid' => $kandidat_id_c
								));
								$rowProjekti = $queryProjekti->fetch();
								$brojacUProjektuP = intval($rowProjekti["brojac"]);
								
								if($nalogIdPN != 0 OR $brojacUProjektuP != 0 OR $statusPrijavePN == 4){
									$rezervisanFlag = 1;
								}
								
								if($rezervisanFlag == 0){
									$query_project = $db->prepare("
													INSERT INTO idk_project_kandidati
														(pk_projectid, pk_kandidatid)
													VALUES
														(:pk_projectid, :pk_kandidatid)");
						
									$query_project->execute(array(
													':pk_projectid' => $project_id,
													':pk_kandidatid' => $kandidat_id_c));
									addToLogsStatusPrijave(NULL, $project_id, 2, $kandidat_id_c, 3);
								}else{
									pushToProjectCandidateQueue($kandidat_id_c, $project_id);
								}
								//Provjera rezervisanosti END 
							}else{
								$rezervisanFlag = 0;
							}
						}
					}
				//INSERT INTO PROJEKAT

				//Add to table users (chatbot)
					$random_string = generateRandomString();
					$options = [
						'cost' => 10,
					];
					$random_password = password_hash($random_string, PASSWORD_BCRYPT, $options);
					
					//Add to logs candidate IP	
					$date_time_ip = date("F j, Y, g:i T");
					if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
						$ip = $_SERVER['HTTP_CLIENT_IP'];
					} elseif (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
						$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
					} else {
						$ip = $_SERVER['REMOTE_ADDR'];
					}
					$log_desc = "Kandidat se ponovo prijavio: " . $kandidat_ime . " " . $kandidat_prezime . ". ".$date_time_ip."; IP: ".$ip." (".$random_string.").";
					$log_type = "5";
					addToLogs($log_desc, $log_type);

					if($nr_of_rows_mess == 0){
						$characters = '0123456789';
						$charactersLength = strlen($characters);
						$randomString = '';
						for ($i = 0; $i < 5; $i++) {
							$randomString .= $characters[rand(0, $charactersLength - 1)];
						}
						$bot_koriscnicko_ime = $kandidat_ime.$randomString;
						
						$query_user = $db->prepare("
									INSERT INTO users
										(phone, name, nalog_id, email, password, kandidat_id)
									VALUES
										(:phone, :name, :nalog_id, :email, :password, :kandidat_id)");

						$query_user->execute(array(
									':phone' => $mobile_phone,
									':name' => $kandidat_full_name,
									':nalog_id' => $lg_nalogid,
									':email' => $bot_koriscnicko_ime,
									':password' => $random_password,
									':kandidat_id' => $kandidat_id_c));
						$phone_f = str_replace("+", '00', $mobile_phone);
						//INFOBIP
						$link_dload = "https://crm.job-step.com/download";
						$link_uputs = "https://bit.ly/3V177tF";
						$kandidat_full_name = getCandidateFullnameR($kandidat_id_c);

						$to_send_viber_poruka_1 = $kandidat_full_name.$text_viber_new_prva_poruka; 
						$to_send_sms_poruka_1 	= $kandidat_full_name.$text_sms_new_prva_poruka; 

						$to_send_viber_poruka_2 = $text_viber_new_druga_poruka;
						$to_send_sms_poruka_2 	= $text_sms_new_druga_poruka." ".$link_dload;

						$to_send_viber_poruka_3 = $text_viber_new_treca_poruka_1.'\n\n'.$text_viber_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_viber_new_treca_poruka_3.$random_string;
						$to_send_sms_poruka_3 	= $text_sms_new_treca_poruka_1.'\n\n'.$text_sms_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_sms_new_treca_poruka_3.$random_string;


						viberPrijava1($phone_f, $to_send_sms_poruka_1, $to_send_viber_poruka_1);
						sleep(1);
						viberPrijava2($phone_f, $to_send_sms_poruka_2, $to_send_viber_poruka_2, $text_btn_new_druga_poruka, $link_dload);
						sleep(1);
						viberPrijava1($phone_f, $to_send_sms_poruka_3, $to_send_viber_poruka_3);
						sleep(1);

						if($lg_language == "bs"){
							$to_send_viber_poruka_4 = $text_viber_new_cetvrta_poruka;
							$to_send_sms_poruka_4 	= $text_sms_new_cetvrta_poruka." ".$link_uputs;
							
							viberPrijava2($phone_f, $to_send_sms_poruka_4, $to_send_viber_poruka_4, $text_btn_new_cetvrta_poruka, $link_uputs);
						}
						if($rezervisanFlag == 0){
							checkCandidateInputs($kandidat_id_c);
						}
						if($kandidat_email != null)
							sendCandidateMessengerMail($random_string, $kandidat_email, $bot_koriscnicko_ime);
						
						
					}else{
						//AKO JE KANDIDAT VEC NA BOTU PROVJERITI STATUS
						if($kandidat_status == 2 OR $kandidat_status == 4 OR $kandidat_status == 5){
							//AKO JE OBRADJEN, NA KONTROLI ILI NA DOPUNI ONDA VEZATI GA ZA NALOG I STAVITI U ODGOVARAJUCI PROJEKAT
							if($rezervisanFlag == 0){
								$update_user = $db->prepare("
											UPDATE users
											SET nalog_id = :nalog_id
											WHERE kandidat_id = :kandidat_id
										");

								$update_user->execute(array(
											':nalog_id' => $lg_nalogid,
											':kandidat_id' => $kandidat_id_c));
							
								checkCandidateInputs($kandidat_id_c);
							}
						}else{
							//AKO NIJE NISTA RADIO NA BOTU ONDA VEZATI GA ZA NALOG I POSLATI PODATKE PONOVO
							
							if($rezervisanFlag == 0){
								
								$characters = '0123456789';
								$charactersLength = strlen($characters);
								
								$randomString = '';
								for ($i = 0; $i < 5; $i++) {
									$randomString .= $characters[rand(0, $charactersLength - 1)];
								}
								$bot_koriscnicko_ime = $kandidat_ime.$randomString;
								
								$row_messenger = $check_messenger->fetch();
								$user_id = $row_messenger['id'];
								$mobile_phone = $row_messenger['phone'];
								
								$update_user = $db->prepare("
											UPDATE users
											SET nalog_id = :nalog_id, password = :password, email = :email
											WHERE kandidat_id = :kandidat_id
										");

								$update_user->execute(array(
											':nalog_id' => $lg_nalogid,
											':password' => $random_password,
											':email' => $bot_koriscnicko_ime,
											':kandidat_id' => $kandidat_id_c));
								
								$phone_f = str_replace("+", '', $mobile_phone);
								
								$link_dload = "https://crm.job-step.com/download";
								$link_uputs = "https://bit.ly/3V177tF";
								$kandidat_full_name = getCandidateFullnameR($kandidat_id_c);

								$to_send_viber_poruka_1 = $kandidat_full_name.$text_viber_new_prva_poruka; 
								$to_send_sms_poruka_1 	= $kandidat_full_name.$text_sms_new_prva_poruka; 

								$to_send_viber_poruka_2 = $text_viber_new_druga_poruka;
								$to_send_sms_poruka_2 	= $text_sms_new_druga_poruka." ".$link_dload;

								$to_send_viber_poruka_3 = $text_viber_new_treca_poruka_1.'\n\n'.$text_viber_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_viber_new_treca_poruka_3.$random_string;
								$to_send_sms_poruka_3 	= $text_sms_new_treca_poruka_1.'\n\n'.$text_sms_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_sms_new_treca_poruka_3.$random_string;
								// var_dump($to_send_viber_poruka_3);
								// var_dump($to_send_sms_poruka_3);
								// exit();
								viberPrijava1($phone_f, $to_send_sms_poruka_1, $to_send_viber_poruka_1);
								sleep(1);
								viberPrijava2($phone_f, $to_send_sms_poruka_2, $to_send_viber_poruka_2, $text_btn_new_druga_poruka, $link_dload);
								sleep(1);
								viberPrijava1($phone_f, $to_send_sms_poruka_3, $to_send_viber_poruka_3);
								sleep(1);

								if($lg_language == "bs"){
									$to_send_viber_poruka_4 = $text_viber_new_cetvrta_poruka;
									$to_send_sms_poruka_4 	= $text_sms_new_cetvrta_poruka." ".$link_uputs;
									
									viberPrijava2($phone_f, $to_send_sms_poruka_4, $to_send_viber_poruka_4, $text_btn_new_cetvrta_poruka, $link_uputs);
								}
								
								checkCandidateInputs($kandidat_id_c);
								
								if($kandidat_email != null)
									sendCandidateMessengerMail($random_string, $kandidat_email, $bot_koriscnicko_ime);
							}
						}
					}
				//Add to table users (chatbot)
			}


		break;

		case "import_fb_leads_add_kandidat_new":
			
			include("lang/bs.php");
			$kandidat_ime = $_REQUEST['kandidat_ime'];
			$kandidat_prezime = $_REQUEST['kandidat_prezime'];
			$urlid = $_REQUEST['urlid'];
			$lg_language = $_REQUEST['lg_language'];
			$mobile_phone = $_REQUEST['kki_phone'];
			$kandidat_group = $_REQUEST['kandidat_prijava_na'];
			$kandidat_email = $_REQUEST['kandidat_email'];
			$datum_prijave = $_REQUEST['datum_prijave'];
			$kandidat_grad = $_REQUEST['kandidat_grad'];

			$kandidat_full_name = $kandidat_ime." ".$kandidat_prezime;

			$check_query2 = $db->prepare("
									SELECT lg_id, lg_url, lg_nalogid
									FROM idk_link_generator
									WHERE lg_id = :lg_id");
		
			$check_query2->execute(array(
							':lg_id' => $urlid));
		
			$rowlg = $check_query2->fetch();
				
			$lg_nalogid = $rowlg['lg_nalogid'];

			//napravi username po imenu, prezimenu i godini
			$ime_korime = strtolower($kandidat_ime);
			$prezime_korime = strtolower($kandidat_prezime);
			$imeprezime = $ime_korime.''.$prezime_korime;
			$search = array("ć", "č", "ž", "š", "đ", " ");
			$replacement = array("c", "c", "z", "s", "dj", "");
			$imeprezime_korime = str_replace($search, $replacement, $imeprezime);

			$group_title_query = $db->prepare("
							SELECT kg_id, kg_title
							FROM idk_kandidati_grupe
							WHERE kg_id = :kg_id
							");
	
			$group_title_query->execute(array(
				":kg_id" => $kandidat_group
			));
			$row_kg_title = $group_title_query->fetch();
			$kandidat_prijava_na = $row_kg_title['kg_title'];
			
			$kandidat_datetime = date('Y-m-d H:i:s', strtotime($datum_prijave));
			$kandidat_check = md5(uniqid(rand(), true));
			
			// Provjera da li postoji korisnik sa istim imenom i prezimenom u zadnjih sat vremena
			$check_user = $db->prepare("
									SELECT kandidat_ime, kandidat_id, kandidat_status_messenger, kandidat_check, kandidat_visitedurl, kandidat_status
									FROM idk_kandidati
									WHERE kandidat_ime = :kandidat_ime AND kandidat_prezime = :kandidat_prezime AND kandidat_mobitel = :kandidat_mobitel AND kandidat_status != 3");
		
			$check_user->execute(array(
							':kandidat_ime' => $kandidat_ime,
							':kandidat_prezime' => $kandidat_prezime,
							':kandidat_mobitel' => $mobile_phone
							));
		
			$number_of_rows_user = $check_user->rowCount();

			if($number_of_rows_user == 0){
				
				$random_token = rand(100, 999);
				$kandidat_korisnickoime_uf = $imeprezime_korime.''.$random_token;
				$kandidat_korisnickoime = str_replace(' ', '', strtolower($kandidat_korisnickoime_uf));
				$kandidat_password = MD5($kandidat_korisnickoime);
				
				//Add user to db
				$query = $db->prepare("
							INSERT INTO idk_kandidati
								( kandidat_check, kandidat_ime, kandidat_prezime, kandidat_email, kandidat_mobitel, kandidat_password, kandidat_slika, kandidat_status, kandidat_status_messenger, kandidat_datetime, kandidat_korisnickoime, kandidat_visitedurl, kandidat_prijava_na, kandidat_group, kandidat_porijeklo, kandidat_grad)
							VALUES
								(:kandidat_check,:kandidat_ime,:kandidat_prezime,:kandidat_email,:kandidat_mobitel,:kandidat_password,:kandidat_slika,:kandidat_status,:kandidat_status_messenger,:kandidat_datetime,:kandidat_korisnickoime,:kandidat_visitedurl,:kandidat_prijava_na,:kandidat_group,:kandidat_porijeklo,:kandidat_grad)");

				$query->execute(array(
						':kandidat_check' => $kandidat_check,
						':kandidat_ime' => $kandidat_ime,
						':kandidat_prezime' => $kandidat_prezime,
						':kandidat_email' => $kandidat_email,
						':kandidat_mobitel' => $mobile_phone,
						':kandidat_password' => $kandidat_password,
						':kandidat_slika' => "none",
						':kandidat_status' => 0,
						':kandidat_status_messenger' => 1,
						':kandidat_datetime' => $kandidat_datetime,
						':kandidat_korisnickoime' => $kandidat_korisnickoime,
						':kandidat_visitedurl' => $urlid,
						':kandidat_prijava_na' => $kandidat_prijava_na,
						':kandidat_group' => $kandidat_group,
						':kandidat_porijeklo' => 0,
						':kandidat_grad' => $kandidat_grad
						));
						
				$kandidat_id = $db->lastInsertId();
				
				$query_log_status = $db->prepare("
						INSERT INTO idk_log_kandidat_statusi
							(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime)
						VALUES
							(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime)
				");
				
				$query_log_status->execute(array(
						':lks_kandidat_id' => $kandidat_id,
						':lks_status_obrade' => 0,
						':lks_status_messenger' => 1,
						':lks_datetime' => $kandidat_datetime
				));

				//VEZANJE KANDIDATA ZA PROJEKT
				if($lg_nalogid != 0){
					$nalog_query = $db->prepare("
											SELECT project_id
											FROM idk_projects
											WHERE project_nalogid = :project_nalogid AND (project_name LIKE '%Prijave%' OR project_name LIKE '%Prijava%') ");
				
					$nalog_query->execute(array(
									':project_nalogid' => $lg_nalogid));
				
					$nalogrow = $nalog_query->fetch();
					if(!$nalogrow){
						//sta raditi ako nema projekta "PRIJAVA"
						//IDU U NOVE PROJEKTI ZA VIBER NALOG
					}else{
						
						$project_id = $nalogrow['project_id'];	
						
						$query_project = $db->prepare("
										INSERT INTO idk_project_kandidati
											(pk_projectid, pk_kandidatid)
										VALUES
											(:pk_projectid, :pk_kandidatid)");
			
						$query_project->execute(array(
										':pk_projectid' => $project_id,
										':pk_kandidatid' => $kandidat_id));	
					}
				}

				//Add mobilni to db
				$query_mob = $db->prepare("
								INSERT INTO idk_kandidat_kontakt_info
									(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
								VALUES
									(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

				$query_mob->execute(array(
							':kki_grupa' => 1,
							':kki_naziv' => "Mobilni",
							':kki_podatak' => $mobile_phone,
							':kki_kandidat_id' => $kandidat_id));
				
				//Add kontakt info to db
				$query_email = $db->prepare("
								INSERT INTO idk_kandidat_kontakt_info
									(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
								VALUES
									(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

				$query_email->execute(array(
							':kki_grupa' => 2,
							':kki_naziv' => "E-mail",
							':kki_podatak' => $kandidat_email,
							':kki_kandidat_id' => $kandidat_id));
				
				//Add to table users (chatbot)
				$random_string = generateRandomString();
				$options = [
					'cost' => 10,
				];
				$random_password = password_hash($random_string, PASSWORD_BCRYPT, $options);
				
				//Add to logs candidate IP	
				$date_time_ip = date("F j, Y, g:i T");
				if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
					$ip = $_SERVER['HTTP_CLIENT_IP'];
				} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
					$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				} else {
					$ip = $_SERVER['REMOTE_ADDR'];
				}
				$log_desc = "Kandidat: " . $kandidat_ime . " " . $kandidat_prezime . "(".$kandidat_id."). ".$date_time_ip."; IP: ".$ip." (".$random_string.")";
				$log_type = "5";
				addToLogs($log_desc, $log_type);

				$characters = '0123456789';
				$charactersLength = strlen($characters);
				$randomString = '';
				for ($i = 0; $i < 5; $i++) {
					$randomString .= $characters[rand(0, $charactersLength - 1)];
				}
				$bot_koriscnicko_ime = str_replace(' ','',$kandidat_ime).$randomString;
				
				$query_user = $db->prepare("
							INSERT INTO users
								(phone, name, nalog_id, email, password, kandidat_id)
							VALUES
								(:phone, :name, :nalog_id, :email, :password, :kandidat_id)");

				$query_user->execute(array(
							':phone' => $mobile_phone,
							':name' => $kandidat_full_name,
							':nalog_id' => $lg_nalogid,
							':email' => $bot_koriscnicko_ime,
							':password' => $random_password,
							':kandidat_id' => $kandidat_id));
				
				
				$phone_f = str_replace("+", '', $mobile_phone);
				
				$link_dload = "https://crm.job-step.com/download";
				$link_uputs = "https://bit.ly/3V177tF";

				$kandidat_full_name = getCandidateFullnameR($kandidat_id);

				$to_send_viber_poruka_1 = $kandidat_full_name.$text_viber_new_prva_poruka; 
				$to_send_sms_poruka_1 	= $kandidat_full_name.$text_sms_new_prva_poruka; 

				$to_send_viber_poruka_2 = $text_viber_new_druga_poruka;
				$to_send_sms_poruka_2 	= $text_sms_new_druga_poruka." ".$link_dload;

				$to_send_viber_poruka_3 = $text_viber_new_treca_poruka_1.'\n\n'.$text_viber_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_viber_new_treca_poruka_3.$random_string;
				$to_send_sms_poruka_3 	= $text_sms_new_treca_poruka_1.'\n\n'.$text_sms_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_sms_new_treca_poruka_3.$random_string;
				
				viberPrijava1($phone_f, $to_send_sms_poruka_1, $to_send_viber_poruka_1);
				sleep(1);
				viberPrijava2($phone_f, $to_send_sms_poruka_2, $to_send_viber_poruka_2, $text_btn_new_druga_poruka, $link_dload);
				sleep(1);
				viberPrijava1($phone_f, $to_send_sms_poruka_3, $to_send_viber_poruka_3);
				sleep(1);

				if($lg_language == "bs"){
					$to_send_viber_poruka_4 = $text_viber_new_cetvrta_poruka;
					$to_send_sms_poruka_4 	= $text_sms_new_cetvrta_poruka." ".$link_uputs;
					
					viberPrijava2($phone_f, $to_send_sms_poruka_4, $to_send_viber_poruka_4, $text_btn_new_cetvrta_poruka, $link_uputs);
				}
				if($kandidat_email != null)
					sendCandidateMessengerMail($random_string, $kandidat_email, $bot_koriscnicko_ime);
				checkCandidateInputs($kandidat_id);
				
				notifForOnlneRegister($kandidat_id, $kandidat_check);

			}else{
				$rows_user = $check_user->fetch();
				$kandidat_id_c = $rows_user['kandidat_id'];
				$kandidat_status_messenger = $rows_user['kandidat_status_messenger'];
				$kandidat_check_exists = $rows_user['kandidat_check'];
				$stari_visitedurl = $rows_user['kandidat_visitedurl'];
				$kandidat_status = $rows_user['kandidat_status'];
				
				$kandidat_slika_final = "none";
				
				$check_messenger = $db->prepare("
										SELECT id, email, phone
										FROM users
										WHERE kandidat_id = :kandidat_id ");
			
				$check_messenger->execute(array(
								':kandidat_id' => $kandidat_id_c
								));
			
				$nr_of_rows_mess = $check_messenger->rowCount();
				if($nr_of_rows_mess == 0){
					$kandidat_status_messenger = 1;
				}else{}

				//Update user
				$update_user = $db->prepare("
								UPDATE idk_kandidati
								SET kandidat_email = :kandidat_email, kandidat_status_messenger = :kandidat_status_messenger, kandidat_visitedurl = :kandidat_visitedurl, kandidat_prijava_na = :kandidat_prijava_na, kandidat_porijeklo = :kandidat_porijeklo
								WHERE kandidat_id = :kandidat_id
								");
		
				$update_user->execute(array(
							':kandidat_id' => $kandidat_id_c,
							':kandidat_email' => $kandidat_email,
							':kandidat_status_messenger' => $kandidat_status_messenger,
							':kandidat_visitedurl' => $urlid,
							':kandidat_prijava_na' => $kandidat_prijava_na,
							':kandidat_porijeklo' => 0
							));
				
				//VEZANJE KANDIDATA ZA PROJEKT
				if($lg_nalogid != 0){
					$nalog_query = $db->prepare("
											SELECT project_id
											FROM idk_projects
											WHERE project_nalogid = :project_nalogid AND (project_name LIKE '%Prijave%' OR project_name LIKE '%Prijava%') ");
				
					$nalog_query->execute(array(
									':project_nalogid' => $lg_nalogid));
				
					$nalogrow = $nalog_query->fetch();
					if(!$nalogrow){
						//sta raditi ako nema projekta "PRIJAVA"
						//IDU U NOVE PROJEKTI ZA VIBER NALOG
					}else{
					
						$project_id = $nalogrow['project_id'];	
						$check_project = $db->prepare("
												SELECT pk_projectid
												FROM idk_project_kandidati
												WHERE pk_projectid = :pk_projectid AND pk_kandidatid = :pk_kandidatid");
					
						$check_project->execute(array(
											':pk_projectid' => $project_id,
											':pk_kandidatid' => $kandidat_id_c
						));
						if($check_project->rowCount() == 0){
							
							$query_project = $db->prepare("
											INSERT INTO idk_project_kandidati
												(pk_projectid, pk_kandidatid)
											VALUES
												(:pk_projectid, :pk_kandidatid)");
				
							$query_project->execute(array(
											':pk_projectid' => $project_id,
											':pk_kandidatid' => $kandidat_id_c));
						}
					}
				}

				//Add to table users (chatbot)
				$random_string = generateRandomString();
				$options = [
					'cost' => 10,
				];
				$random_password = password_hash($random_string, PASSWORD_BCRYPT, $options);
				
				//Add to logs candidate IP	
				$date_time_ip = date("F j, Y, g:i T");
				if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
					$ip = $_SERVER['HTTP_CLIENT_IP'];
				} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
					$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				} else {
					$ip = $_SERVER['REMOTE_ADDR'];
				}
				$log_desc = "Kandidat se ponovo prijavio: " . $kandidat_ime . " " . $kandidat_prezime . ". ".$date_time_ip."; IP: ".$ip." (".$random_string."). Stari visited url: ".$stari_visitedurl."";
				$log_type = "5";
				addToLogs($log_desc, $log_type);
				
				//JEZICI, BOT NALOG=0...SAMO BOT
				
				if($nr_of_rows_mess == 0){
					$characters = '0123456789';
					$charactersLength = strlen($characters);
					$randomString = '';
					for ($i = 0; $i < 5; $i++) {
						$randomString .= $characters[rand(0, $charactersLength - 1)];
					}
					$bot_koriscnicko_ime = str_replace(' ','',$kandidat_ime).$randomString;
					
					$query_user = $db->prepare("
								INSERT INTO users
									(phone, name, nalog_id, email, password, kandidat_id)
								VALUES
									(:phone, :name, :nalog_id, :email, :password, :kandidat_id)");

					$query_user->execute(array(
								':phone' => $mobile_phone,
								':name' => $kandidat_full_name,
								':nalog_id' => $lg_nalogid,
								':email' => $bot_koriscnicko_ime,
								':password' => $random_password,
								':kandidat_id' => $kandidat_id_c));
					$phone_f = str_replace("+", '00', $mobile_phone);
					//INFOBIP
					$link_dload = "https://crm.job-step.com/download";
					$link_uputs = "https://bit.ly/3V177tF";
					$kandidat_full_name = getCandidateFullnameR($kandidat_id_c);

					$to_send_viber_poruka_1 = $kandidat_full_name.$text_viber_new_prva_poruka; 
					$to_send_sms_poruka_1 	= $kandidat_full_name.$text_sms_new_prva_poruka; 

					$to_send_viber_poruka_2 = $text_viber_new_druga_poruka;
					$to_send_sms_poruka_2 	= $text_sms_new_druga_poruka." ".$link_dload;

					$to_send_viber_poruka_3 = $text_viber_new_treca_poruka_1.'\n\n'.$text_viber_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_viber_new_treca_poruka_3.$random_string;
					$to_send_sms_poruka_3 	= $text_sms_new_treca_poruka_1.'\n\n'.$text_sms_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_sms_new_treca_poruka_3.$random_string;


					viberPrijava1($phone_f, $to_send_sms_poruka_1, $to_send_viber_poruka_1);
					sleep(1);
					viberPrijava2($phone_f, $to_send_sms_poruka_2, $to_send_viber_poruka_2, $text_btn_new_druga_poruka, $link_dload);
					sleep(1);
					viberPrijava1($phone_f, $to_send_sms_poruka_3, $to_send_viber_poruka_3);
					sleep(1);

					if($lg_language == "bs"){
						$to_send_viber_poruka_4 = $text_viber_new_cetvrta_poruka;
						$to_send_sms_poruka_4 	= $text_sms_new_cetvrta_poruka." ".$link_uputs;
						
						viberPrijava2($phone_f, $to_send_sms_poruka_4, $to_send_viber_poruka_4, $text_btn_new_cetvrta_poruka, $link_uputs);
					}
					checkCandidateInputs($kandidat_id_c);
					if($kandidat_email != null)
						sendCandidateMessengerMail($random_string, $kandidat_email, $bot_koriscnicko_ime);
					
					
				}else{
					//AKO JE KANDIDAT VEC NA BOTU PROVJERITI STATUS
					if($kandidat_status == 2 OR $kandidat_status == 4 OR $kandidat_status == 5){
						//AKO JE OBRADJEN, NA KONTROLI ILI NA DOPUNI ONDA VEZATI GA ZA NALOG I STAVITI U ODGOVARAJUCI PROJEKAT
						$update_user = $db->prepare("
									UPDATE users
									SET nalog_id = :nalog_id
									WHERE kandidat_id = :kandidat_id
								");

						$update_user->execute(array(
									':nalog_id' => $lg_nalogid,
									':kandidat_id' => $kandidat_id_c));
									
						checkCandidateInputs($kandidat_id_c);
					}else{
						//AKO NIJE NISTA RADIO NA BOTU ONDA VEZATI GA ZA NALOG I POSLATI PODATKE PONOVO
						
						$characters = '0123456789';
						$charactersLength = strlen($characters);
						
						$randomString = '';
						for ($i = 0; $i < 5; $i++) {
							$randomString .= $characters[rand(0, $charactersLength - 1)];
						}
						$bot_koriscnicko_ime = str_replace(' ','',$kandidat_ime).$randomString;
						
						$row_messenger = $check_messenger->fetch();
						$user_id = $row_messenger['id'];
						$mobile_phone = $row_messenger['phone'];
						
						$update_user = $db->prepare("
									UPDATE users
									SET nalog_id = :nalog_id, password = :password, email = :email
									WHERE kandidat_id = :kandidat_id
								");

						$update_user->execute(array(
									':nalog_id' => $lg_nalogid,
									':password' => $random_password,
									':email' => $bot_koriscnicko_ime,
									':kandidat_id' => $kandidat_id_c));
						
						$phone_f = str_replace("+", '', $mobile_phone);
						
						$link_dload = "https://crm.job-step.com/download";
						$link_uputs = "https://bit.ly/3V177tF";
						$kandidat_full_name = getCandidateFullnameR($kandidat_id_c);

						$to_send_viber_poruka_1 = $kandidat_full_name.$text_viber_new_prva_poruka; 
						$to_send_sms_poruka_1 	= $kandidat_full_name.$text_sms_new_prva_poruka; 

						$to_send_viber_poruka_2 = $text_viber_new_druga_poruka;
						$to_send_sms_poruka_2 	= $text_sms_new_druga_poruka." ".$link_dload;

						$to_send_viber_poruka_3 = $text_viber_new_treca_poruka_1.'\n\n'.$text_viber_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_viber_new_treca_poruka_3.$random_string;
						$to_send_sms_poruka_3 	= $text_sms_new_treca_poruka_1.'\n\n'.$text_sms_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_sms_new_treca_poruka_3.$random_string;
						// var_dump($to_send_viber_poruka_3);
						// var_dump($to_send_sms_poruka_3);
						// exit();
						viberPrijava1($phone_f, $to_send_sms_poruka_1, $to_send_viber_poruka_1);
						sleep(1);
						viberPrijava2($phone_f, $to_send_sms_poruka_2, $to_send_viber_poruka_2, $text_btn_new_druga_poruka, $link_dload);
						sleep(1);
						viberPrijava1($phone_f, $to_send_sms_poruka_3, $to_send_viber_poruka_3);
						sleep(1);

						if($lg_language == "bs"){
							$to_send_viber_poruka_4 = $text_viber_new_cetvrta_poruka;
							$to_send_sms_poruka_4 	= $text_sms_new_cetvrta_poruka." ".$link_uputs;
							
							viberPrijava2($phone_f, $to_send_sms_poruka_4, $to_send_viber_poruka_4, $text_btn_new_cetvrta_poruka, $link_uputs);
						}
						checkCandidateInputs($kandidat_id_c);
						if($kandidat_email != null)
							sendCandidateMessengerMail($random_string, $kandidat_email, $bot_koriscnicko_ime);
					}
				}
				
				//NOTIFIKACIJE
				notifForPonovnaPrijava($kandidat_id_c, $kandidat_check_exists);
				//header("Location: registracija/thank_you/$kandidat_id_c/$kandidat_check_exists");
				//header("Location: registracija/thank_you/$kandidat_id_c/$kandidat_check_exists/$lg_language");
			}

		break;
		
		case "import_add_kandidat_new":
			
			$kandidat_ime = $_REQUEST['kandidat_ime'];
			$kandidat_prezime = $_REQUEST['kandidat_prezime'];
			$urlid = $_REQUEST['urlid'];
			$lg_language = $_REQUEST['lg_language'];
			$dan_rodjenja = $_REQUEST["datum_dan"];
			$mjesec_rodjenja = $_REQUEST["datum_mjesec"];
			$godina_rodjenja = $_REQUEST["datum_godina"];
			$mobile_phone = $_REQUEST['kki_phone'];
			$partner_token = $_REQUEST['token'];
			$kandidat_viza_da_ne = $_REQUEST['kandidat_viza'];
			$kandidat_group = $_REQUEST['kandidat_prijava_na'];
			$struka = $_REQUEST['struka'];
			$bil_struka = $_REQUEST['bil_struka'];
			
			if(isset($_REQUEST['kandidat_email'])){
				$kandidat_email = $_REQUEST['kandidat_email'];
			}else{
				$kandidat_email = null;
			}
			if(isset($_REQUEST['kategorija_vozacke'])){
				$kandidat_vozacka_dozvola = "Da";
				$kandidat_vozacka_kategorija = $_REQUEST['kategorija_vozacke'];
			}else{
				$kandidat_vozacka_dozvola = null;
				$kandidat_vozacka_kategorija = null;
			}

			if($lg_language == "de")
				include("lang/de.php");
			else
				include("lang/bs.php");
			
			$kandidat_full_name = $kandidat_ime." ".$kandidat_prezime;
			
			$kandidat_datumrodjenja = date('Y-m-d', strtotime($godina_rodjenja."-".$mjesec_rodjenja."-".$dan_rodjenja));

			//Postavljanje defulatnog urlid = 1 kad nema linka i uzimanje id naloga
			$check_query2 = $db->prepare("
									SELECT lg_id, lg_url, lg_nalogid
									FROM idk_link_generator
									WHERE lg_id = :lg_id");
		
			$check_query2->execute(array(
							':lg_id' => $urlid));
		
			$num = $check_query2->rowCount();
			$rowlg = $check_query2->fetch();
				
			$lg_nalogid = $rowlg['lg_nalogid'];
			
			if($num > 0){
				$url_id = $_REQUEST['urlid'];
			}else{
				$url_id = 1;
			}
			
			//napravi username po imenu, prezimenu i godini
			$ime_korime = strtolower($kandidat_ime);
			$prezime_korime = strtolower($kandidat_prezime);
			$imeprezime = $ime_korime.''.$prezime_korime;
			$search = array("ć", "č", "ž", "š", "đ");
			$replacement = array("c", "c", "z", "s", "dj");
			$imeprezime_korime = str_replace($search, $replacement, $imeprezime);
		
			
			if($kandidat_viza_da_ne == "DA"){
				$kandidat_viza = 1;	
			}else{
				$kandidat_viza = 0;
			}
			
			// Provjera da li postoji korisnik sa istim imenom i prezimenom u zadnjih sat vremena
			$granicno_vrijeme = date( "Y-m-d H:i:s",  strtotime("- 1 hour" ));
			$check_user = $db->prepare("
									SELECT kandidat_ime, kandidat_id, kandidat_status_messenger, kandidat_check, kandidat_visitedurl, kandidat_status
									FROM idk_kandidati
									WHERE kandidat_ime = :kandidat_ime AND kandidat_prezime = :kandidat_prezime AND kandidat_mobitel = :kandidat_mobitel AND kandidat_status != 3");
		
			$check_user->execute(array(
							':kandidat_ime' => $kandidat_ime,
							':kandidat_prezime' => $kandidat_prezime,
							':kandidat_mobitel' => $mobile_phone
							));
		
			$number_of_rows_user = $check_user->rowCount();
			
			
			$group_title_query = $db->prepare("
							SELECT kg_id, kg_title
							FROM idk_kandidati_grupe
							WHERE kg_id = :kg_id
							");
	
			$group_title_query->execute(array(
				":kg_id" => $kandidat_group
			));
			$row_kg_title = $group_title_query->fetch();
			$kandidat_prijava_na = $row_kg_title['kg_title'];
			
			$kandidat_datetime = date('Y-m-d H:i:s');
			$kandidat_check = md5(uniqid(rand(), true));
			
			if($number_of_rows_user == 0){
				
				$random_token = rand(100, 999);
				$kandidat_korisnickoime_uf = $imeprezime_korime.''.$random_token;
				$kandidat_korisnickoime = str_replace(' ', '', strtolower($kandidat_korisnickoime_uf));
				$kandidat_password = MD5($kandidat_korisnickoime);
				
				$kandidat_status = 0;
				
				if($lg_language == "bs" or $lg_language == "de")
					$kandidat_status_messenger = 1;
				else
					$kandidat_status_messenger = 4;
				
				$kandidat_slika_final = "none";
				
				//ADD PARTNER DATA
					
				if($partner_token != null){
					$partner_query = $db->prepare("
									SELECT jp_id, jp_fcmtoken, jp_position, jp_imeprezime
									FROM idk_jobstep_partners
									WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token
									");
			
						$partner_query->execute(array(
							":jp_mailconfirmation_token" => $partner_token
					));
			
					$row_partner = $partner_query->fetch();
					$partner_id = $row_partner['jp_id'];
					$partner_position = $row_partner['jp_position'];
					$jp_fcmtoken = $row_partner['jp_fcmtoken'];
					$partner_ime = $row_partner['jp_imeprezime'];
					$log_poruka = "Novi kandidat ".$kandidat_ime." je prijavljen preko vašeg linka.";
					send_notification_partnerapp($jp_fcmtoken, $partner_ime, $log_poruka);
					$kandidat_porijeklo = 6;
				}else{
					$partner_id = null;
					$partner_position = null;
					$kandidat_porijeklo = 0;
				}
				
				//Add user to db
				$query = $db->prepare("
								INSERT INTO idk_kandidati
									( kandidat_check, kandidat_ime, kandidat_prezime, kandidat_email, kandidat_mobitel, kandidat_password, kandidat_slika, kandidat_status, kandidat_status_messenger, kandidat_datetime, kandidat_korisnickoime, kandidat_datumrodjenja, kandidat_visitedurl, kandidat_prijava_na, kandidat_group, kandidat_viza, kandidat_partnerid, kandidat_partner_status, kandidat_porijeklo, struka_sa_prijave, kandidat_vozacka_dozvola, kandidat_vozacka_kategorija)
								VALUES
									(:kandidat_check,:kandidat_ime,:kandidat_prezime,:kandidat_email,:kandidat_mobitel,:kandidat_password,:kandidat_slika,:kandidat_status,:kandidat_status_messenger,:kandidat_datetime,:kandidat_korisnickoime,:kandidat_datumrodjenja,:kandidat_visitedurl,:kandidat_prijava_na,:kandidat_group,:kandidat_viza,:kandidat_partnerid,:kandidat_partner_status,:kandidat_porijeklo,:struka_sa_prijave,:kandidat_vozacka_dozvola,:kandidat_vozacka_kategorija)");
		
				$query->execute(array(
							':kandidat_check' => $kandidat_check,
							':kandidat_ime' => $kandidat_ime,
							':kandidat_prezime' => $kandidat_prezime,
							':kandidat_email' => $kandidat_email,
							':kandidat_mobitel' => $mobile_phone,
							':kandidat_password' => $kandidat_password,
							':kandidat_slika' => $kandidat_slika_final,
							':kandidat_status' => $kandidat_status,
							':kandidat_status_messenger' => $kandidat_status_messenger,
							':kandidat_datetime' => $kandidat_datetime,
							':kandidat_korisnickoime' => $kandidat_korisnickoime,
							':kandidat_datumrodjenja' => $kandidat_datumrodjenja,
							':kandidat_visitedurl' => $url_id,
							':kandidat_prijava_na' => $kandidat_prijava_na,
							':kandidat_group' => $kandidat_group,
							':kandidat_viza' => $kandidat_viza,
							':kandidat_partnerid' => $partner_id,
							':kandidat_partner_status' => $partner_position,
							':kandidat_porijeklo' => $kandidat_porijeklo,
							':struka_sa_prijave' => $struka,
							':kandidat_vozacka_dozvola' => $kandidat_vozacka_dozvola,
							':kandidat_vozacka_kategorija' => $kandidat_vozacka_kategorija
							));
							
				$kandidat_id = $db->lastInsertId();
				
				//INSERT BILJESKI
				
				// $bil_certifikat = $_REQUEST['bil_certifikat'];
				// $bil_struka = $_REQUEST['bil_struka'];
				// $bil_srednja_skola = $_REQUEST['bil_srednja_skola'];
				$bil_iskustvo = $_REQUEST['bil_iskustvo'];
				
				if($struka == 2){
					$note_group = 2;
					$note_dataid = $kandidat_id;
					$note_datetime = date('Y-m-d H:i:s');
					
					$note_txt_cert = "Struka (sa prijave): ".$bil_struka;
					
					$query_biljeske = $db->prepare("
									INSERT INTO idk_notes
										(note_txt, note_datetime, note_group, note_dataid, note_employeeid)
									VALUES
										(:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");

					$query_biljeske->execute(array(
								':note_txt' => $note_txt_cert,
								':note_datetime' => $note_datetime,
								':note_group' => $note_group,
								':note_dataid' => $note_dataid,
								':note_employeeid' => 67));
								
					$note_txt_cert_isk = "Radno iskustvo u struci: ".$bil_iskustvo.".";
					$query_biljeske_isk = $db->prepare("
									INSERT INTO idk_notes
										(note_txt, note_datetime, note_group, note_dataid, note_employeeid)
									VALUES
										(:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");

					$query_biljeske_isk->execute(array(
								':note_txt' => $note_txt_cert_isk,
								':note_datetime' => $note_datetime,
								':note_group' => $note_group,
								':note_dataid' => $note_dataid,
								':note_employeeid' => 67));
				}
				
				$query_log_status = $db->prepare("
						INSERT INTO idk_log_kandidat_statusi
							(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime)
						VALUES
							(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime)
				");
				
				$query_log_status->execute(array(
						':lks_kandidat_id' => $kandidat_id,
						':lks_status_obrade' => $kandidat_status,
						':lks_status_messenger' => $kandidat_status_messenger,
						':lks_datetime' => $kandidat_datetime
				));

				$post_nivo_jezika = $_REQUEST['nivo_jezika'];
				
				if($post_nivo_jezika == "Bez poznavanja")
					$kj_znanje_njemacki = "Bez znanja";
				else
					$kj_znanje_njemacki = $post_nivo_jezika;
				
				$kj_naziv_njemacki = "Njemački";
				// Add language knowlege
				$query_njem = $db->prepare("
								INSERT INTO idk_kandidat_jezici
									(kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid)
								VALUES
									(:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid)");
		
				$query_njem->execute(array(
							':kj_naziv' => $kj_naziv_njemacki,
							':kj_slusanje' => $kj_znanje_njemacki,
							':kj_citanje' => $kj_znanje_njemacki,
							':kj_govorna_interakcija' => $kj_znanje_njemacki,
							':kj_govorna_produkcija' => $kj_znanje_njemacki,
							':kj_pisanje' => $kj_znanje_njemacki,
							':kj_kandidatid' => $kandidat_id));
								
				
				//VEZANJE KANDIDATA ZA PROJEKT
				if($lg_nalogid != 0){
					$nalog_query = $db->prepare("
											SELECT project_id
											FROM idk_projects
											WHERE project_nalogid = :project_nalogid AND (project_name LIKE '%Prijave%' OR project_name LIKE '%Prijava%') ");
				
					$nalog_query->execute(array(
									':project_nalogid' => $lg_nalogid));
				
					$nalogrow = $nalog_query->fetch();
					if(!$nalogrow){
						//sta raditi ako nema projekta "PRIJAVA"
						//IDU U NOVE PROJEKTI ZA VIBER NALOG
					}else{
						
						$project_id = $nalogrow['project_id'];	
						
						$query_project = $db->prepare("
										INSERT INTO idk_project_kandidati
											(pk_projectid, pk_kandidatid)
										VALUES
											(:pk_projectid, :pk_kandidatid)");
			
						$query_project->execute(array(
										':pk_projectid' => $project_id,
										':pk_kandidatid' => $kandidat_id));	
					}
				}
				
				$kki_grupa = 1;
				$kki_naziv = "Mobilni";

				//Add mobilni to db
				$query_mob = $db->prepare("
								INSERT INTO idk_kandidat_kontakt_info
									(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
								VALUES
									(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

				$query_mob->execute(array(
							':kki_grupa' => $kki_grupa,
							':kki_naziv' => $kki_naziv,
							':kki_podatak' => $mobile_phone,
							':kki_kandidat_id' => $kandidat_id));
				
				$kki_grupa_e = 2;
				$kki_naziv_e = "E-mail";
				if($kandidat_email != null){
					$kki_podatak_e = $kandidat_email;

					//Add kontakt info to db
					$query_email = $db->prepare("
									INSERT INTO idk_kandidat_kontakt_info
										(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
									VALUES
										(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

					$query_email->execute(array(
								':kki_grupa' => $kki_grupa_e,
								':kki_naziv' => $kki_naziv_e,
								':kki_podatak' => $kki_podatak_e,
								':kki_kandidat_id' => $kandidat_id));
				}
				//Add to table users (chatbot)
				$random_string = generateRandomString();
				$options = [
					'cost' => 10,
				];
				$random_password = password_hash($random_string, PASSWORD_BCRYPT, $options);
				
				//Add to logs candidate IP	
				$date_time_ip = date("F j, Y, g:i T");
				if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
					$ip = $_SERVER['HTTP_CLIENT_IP'];
				} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
					$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				} else {
					$ip = $_SERVER['REMOTE_ADDR'];
				}
				$log_desc = "Kandidat: " . $kandidat_ime . " " . $kandidat_prezime . "(".$kandidat_id."). ".$date_time_ip."; IP: ".$ip." (".$random_string.")";
				$log_type = "5";
				addToLogs($log_desc, $log_type);
				
				if($urlid == 0){
					header("Location: registracija/korak2/$kandidat_id/$kandidat_check");
				}else{
					
					$characters = '0123456789';
					$charactersLength = strlen($characters);
					$randomString = '';
					for ($i = 0; $i < 5; $i++) {
						$randomString .= $characters[rand(0, $charactersLength - 1)];
					}
					$bot_koriscnicko_ime = $kandidat_ime.$randomString;
					
					$query_user = $db->prepare("
								INSERT INTO users
									(phone, name, nalog_id, email, password, kandidat_id)
								VALUES
									(:phone, :name, :nalog_id, :email, :password, :kandidat_id)");

					$query_user->execute(array(
								':phone' => $mobile_phone,
								':name' => $kandidat_full_name,
								':nalog_id' => $lg_nalogid,
								':email' => $bot_koriscnicko_ime,
								':password' => $random_password,
								':kandidat_id' => $kandidat_id));
					
					
					$phone_f = str_replace("+", '', $mobile_phone);
					
					$link_dload = "https://crm.job-step.com/download";
					$link_uputs = "https://bit.ly/3V177tF";

					$kandidat_full_name = getCandidateFullnameR($kandidat_id);

					$to_send_viber_poruka_1 = $kandidat_full_name.$text_viber_new_prva_poruka; 
					$to_send_sms_poruka_1 	= $kandidat_full_name.$text_sms_new_prva_poruka; 

					$to_send_viber_poruka_2 = $text_viber_new_druga_poruka;
					$to_send_sms_poruka_2 	= $text_sms_new_druga_poruka." ".$link_dload;

					$to_send_viber_poruka_3 = $text_viber_new_treca_poruka_1.'\n\n'.$text_viber_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_viber_new_treca_poruka_3.$random_string;
					$to_send_sms_poruka_3 	= $text_sms_new_treca_poruka_1.'\n\n'.$text_sms_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_sms_new_treca_poruka_3.$random_string;
					// var_dump($to_send_viber_poruka_3);
					// var_dump($to_send_sms_poruka_3);
					// exit();
					viberPrijava1($phone_f, $to_send_sms_poruka_1, $to_send_viber_poruka_1);
					sleep(1);
					viberPrijava2($phone_f, $to_send_sms_poruka_2, $to_send_viber_poruka_2, $text_btn_new_druga_poruka, $link_dload);
					sleep(1);
					viberPrijava1($phone_f, $to_send_sms_poruka_3, $to_send_viber_poruka_3);
					sleep(1);

					if($lg_language == "bs"){
						$to_send_viber_poruka_4 = $text_viber_new_cetvrta_poruka;
						$to_send_sms_poruka_4 	= $text_sms_new_cetvrta_poruka." ".$link_uputs;
						
						viberPrijava2($phone_f, $to_send_sms_poruka_4, $to_send_viber_poruka_4, $text_btn_new_cetvrta_poruka, $link_uputs);
					}
					if($kandidat_email != null)
						sendCandidateMessengerMail($random_string, $kandidat_email, $bot_koriscnicko_ime);
					checkCandidateInputs($kandidat_id);
					//header("Location: registracija/thank_you/$kandidat_id/$kandidat_check/$lg_language");
				}
			
				notifForOnlneRegister($kandidat_id, $kandidat_check);
				
			}else{
				
				$rows_user = $check_user->fetch();
				$kandidat_id_c = $rows_user['kandidat_id'];
				$kandidat_status_messenger = $rows_user['kandidat_status_messenger'];
				$kandidat_check_exists = $rows_user['kandidat_check'];
				$stari_visitedurl = $rows_user['kandidat_visitedurl'];
				$kandidat_status = $rows_user['kandidat_status'];
				
				$kandidat_slika_final = "none";
				
				$check_messenger = $db->prepare("
										SELECT id, email, phone
										FROM users
										WHERE kandidat_id = :kandidat_id ");
			
				$check_messenger->execute(array(
								':kandidat_id' => $kandidat_id_c
								));
			
				$nr_of_rows_mess = $check_messenger->rowCount();
				if($nr_of_rows_mess == 0){
					$kandidat_status_messenger = 1;
				}else{}
				
				//ADD PARTNER DATA
					
				if($partner_token != null){
					$partner_query = $db->prepare("
									SELECT jp_id, jp_fcmtoken, jp_position, jp_imeprezime
									FROM idk_jobstep_partners
									WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token
									");
			
						$partner_query->execute(array(
							":jp_mailconfirmation_token" => $partner_token
					));
			
					$row_partner = $partner_query->fetch();
					$partner_id = $row_partner['jp_id'];
					$partner_position = $row_partner['jp_position'];
					$jp_fcmtoken = $row_partner['jp_fcmtoken'];
					$partner_ime = $row_partner['jp_imeprezime'];
					$log_poruka = "Novi kandidat ".$kandidat_ime." je prijavljen preko vašeg linka.";
					send_notification_partnerapp($jp_fcmtoken, $partner_ime, $log_poruka);
					$kandidat_porijeklo = 7;
				}else{
					$partner_id = null;
					$partner_position = null;
					$kandidat_porijeklo = 0;
				}
				
				//Update user
				$update_user = $db->prepare("
								UPDATE idk_kandidati
								SET kandidat_email = :kandidat_email, kandidat_status_messenger = :kandidat_status_messenger, kandidat_datumrodjenja = :kandidat_datumrodjenja, kandidat_viza = :kandidat_viza, kandidat_visitedurl = :kandidat_visitedurl, kandidat_prijava_na = :kandidat_prijava_na, kandidat_partnerid = :kandidat_partnerid, kandidat_partner_status = :kandidat_partner_status, kandidat_porijeklo = :kandidat_porijeklo, struka_sa_prijave = :struka_sa_prijave, kandidat_vozacka_dozvola = :kandidat_vozacka_dozvola, kandidat_vozacka_kategorija = :kandidat_vozacka_kategorija
								WHERE kandidat_id = :kandidat_id
								");
		
				$update_user->execute(array(
							':kandidat_id' => $kandidat_id_c,
							':kandidat_email' => $kandidat_email,
							':kandidat_status_messenger' => $kandidat_status_messenger,
							':kandidat_datumrodjenja' => $kandidat_datumrodjenja,
							':kandidat_viza' => $kandidat_viza,
							':kandidat_visitedurl' => $url_id,
							':kandidat_prijava_na' => $kandidat_prijava_na,
							':kandidat_partnerid' => $partner_id,
							':kandidat_partner_status' => $partner_position,
							':kandidat_porijeklo' => $kandidat_porijeklo,
							':struka_sa_prijave' => $struka,
							':kandidat_vozacka_dozvola' => $kandidat_vozacka_dozvola,
							':kandidat_vozacka_kategorija' => $kandidat_vozacka_kategorija
							));
				
				//INSERT BILJESKI
				
				// $bil_certifikat = $_REQUEST['bil_certifikat'];
				// $bil_struka = $_REQUEST['bil_struka'];
				// $bil_srednja_skola = $_REQUEST['bil_srednja_skola'];
				$bil_iskustvo = $_REQUEST['bil_iskustvo'];
				
				if($struka == 2){
					$note_group = 2;
					$note_dataid = $kandidat_id_c;
					$note_datetime = date('Y-m-d H:i:s');
					
					$note_txt_cert = "Struka (sa prijave): ".$bil_struka;
					
					$query_biljeske = $db->prepare("
									INSERT INTO idk_notes
										(note_txt, note_datetime, note_group, note_dataid, note_employeeid)
									VALUES
										(:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");
					
					$query_biljeske->execute(array(
								':note_txt' => $note_txt_cert,
								':note_datetime' => $note_datetime,
								':note_group' => $note_group,
								':note_dataid' => $note_dataid,
								':note_employeeid' => 67));
								
					$note_txt_cert_isk = "Radno iskustvo u struci: ".$bil_iskustvo.".";
					$query_biljeske_isk = $db->prepare("
									INSERT INTO idk_notes
										(note_txt, note_datetime, note_group, note_dataid, note_employeeid)
									VALUES
										(:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");

					$query_biljeske_isk->execute(array(
								':note_txt' => $note_txt_cert_isk,
								':note_datetime' => $note_datetime,
								':note_group' => $note_group,
								':note_dataid' => $note_dataid,
								':note_employeeid' => 67));
				}
				//INSERT INTO KANDIDAT LOG STATUSI
				
				$query_log_status = $db->prepare("
						INSERT INTO idk_log_kandidat_statusi
							(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime)
						VALUES
							(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime)
				");
				
				$query_log_status->execute(array(
						':lks_kandidat_id' => $kandidat_id_c,
						':lks_status_obrade' => 9,
						':lks_status_messenger' => 0,
						':lks_datetime' => $kandidat_datetime
				));
				
				$post_nivo_jezika = $_REQUEST['nivo_jezika'];
				
				if($post_nivo_jezika == "Bez poznavanja")
					$kj_znanje_njemacki = "Bez znanja";
				else
					$kj_znanje_njemacki = $post_nivo_jezika;
				
				//INSERT/UPDATE JEZIKA
				$kj_naziv_njemacki = "Njemački";
				$query_check = $db->prepare("SELECT
												kj_id
											FROM
												idk_kandidat_jezici
											WHERE
												kj_naziv = :kj_naziv
											AND
												kj_kandidatid = :kandidat_id
											");
				$query_check->execute(array(
					":kandidat_id" => $kandidat_id_c,
					":kj_naziv" => $kj_naziv_njemacki
				));
				$count_jezik = $query_check->rowCount();
				$result_query_check = $query_check->fetch();
				if($count_jezik > 0){
					$kj_id = $result_query_check['kj_id'];
					//UPDATE
					$query_njem = $db->prepare("
						UPDATE idk_kandidat_jezici
						SET kj_naziv = :kj_naziv, kj_citanje = :kj_citanje, kj_slusanje = :kj_slusanje, kj_citanje = :kj_citanje, kj_govorna_interakcija = :kj_govorna_interakcija, kj_govorna_produkcija = :kj_govorna_produkcija, kj_pisanje = :kj_pisanje
						WHERE kj_kandidatid = :kj_kandidatid
						");
					$query_njem->execute(array(
								':kj_naziv' => $kj_naziv_njemacki,
								':kj_slusanje' => $kj_znanje_njemacki,
								':kj_citanje' => $kj_znanje_njemacki,
								':kj_govorna_interakcija' => $kj_znanje_njemacki,
								':kj_govorna_produkcija' => $kj_znanje_njemacki,
								':kj_pisanje' => $kj_znanje_njemacki,
								':kj_kandidatid' => $kandidat_id_c));		
				}else{
						//INSERT
					
					// Add language knowlege
					$query_njem = $db->prepare("
									INSERT INTO idk_kandidat_jezici
										(kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid)
									VALUES
										(:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid)");
			
					$query_njem->execute(array(
								':kj_naziv' => $kj_naziv_njemacki,
								':kj_slusanje' => $kj_znanje_njemacki,
								':kj_citanje' => $kj_znanje_njemacki,
								':kj_govorna_interakcija' => $kj_znanje_njemacki,
								':kj_govorna_produkcija' => $kj_znanje_njemacki,
								':kj_pisanje' => $kj_znanje_njemacki,
								':kj_kandidatid' => $kandidat_id_c));
				}
				
				// Add language knowlege
				$query_njem = $db->prepare("
								UPDATE idk_kandidat_jezici
								SET kj_naziv = :kj_naziv, kj_citanje = :kj_citanje, kj_slusanje = :kj_slusanje, kj_citanje = :kj_citanje, kj_govorna_interakcija = :kj_govorna_interakcija, kj_govorna_produkcija = :kj_govorna_produkcija, kj_pisanje = :kj_pisanje
								WHERE kj_kandidatid = :kj_kandidatid
								");
		
				$query_njem->execute(array(
							':kj_naziv' => $kj_naziv_njemacki,
							':kj_slusanje' => $kj_znanje_njemacki,
							':kj_citanje' => $kj_znanje_njemacki,
							':kj_govorna_interakcija' => $kj_znanje_njemacki,
							':kj_govorna_produkcija' => $kj_znanje_njemacki,
							':kj_pisanje' => $kj_znanje_njemacki,
							':kj_kandidatid' => $kandidat_id_c));				
				
				//VEZANJE KANDIDATA ZA PROJEKT
				if($lg_nalogid != 0){
					$nalog_query = $db->prepare("
											SELECT project_id
											FROM idk_projects
											WHERE project_nalogid = :project_nalogid AND (project_name LIKE '%Prijave%' OR project_name LIKE '%Prijava%') ");
				
					$nalog_query->execute(array(
									':project_nalogid' => $lg_nalogid));
				
					$nalogrow = $nalog_query->fetch();
					if(!$nalogrow){
						//sta raditi ako nema projekta "PRIJAVA"
						//IDU U NOVE PROJEKTI ZA VIBER NALOG
					}else{
					
						$project_id = $nalogrow['project_id'];	
						$check_project = $db->prepare("
												SELECT pk_projectid
												FROM idk_project_kandidati
												WHERE pk_projectid = :pk_projectid AND pk_kandidatid = :pk_kandidatid");
					
						$check_project->execute(array(
											':pk_projectid' => $project_id,
											':pk_kandidatid' => $kandidat_id_c
						));
						if($check_project->rowCount() == 0){
							
							$query_project = $db->prepare("
											INSERT INTO idk_project_kandidati
												(pk_projectid, pk_kandidatid)
											VALUES
												(:pk_projectid, :pk_kandidatid)");
				
							$query_project->execute(array(
											':pk_projectid' => $project_id,
											':pk_kandidatid' => $kandidat_id_c));
						}
					}
				}
				
				//Add to table users (chatbot)
				$random_string = generateRandomString();
				$options = [
					'cost' => 10,
				];
				$random_password = password_hash($random_string, PASSWORD_BCRYPT, $options);
				
				//Add to logs candidate IP	
				$date_time_ip = date("F j, Y, g:i T");
				if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
					$ip = $_SERVER['HTTP_CLIENT_IP'];
				} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
					$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				} else {
					$ip = $_SERVER['REMOTE_ADDR'];
				}
				$log_desc = "Kandidat se ponovo prijavio: " . $kandidat_ime . " " . $kandidat_prezime . ". ".$date_time_ip."; IP: ".$ip." (".$random_string."). Stari visited url: ".$stari_visitedurl."";
				$log_type = "5";
				addToLogs($log_desc, $log_type);
				
				//JEZICI, BOT NALOG=0...SAMO BOT
				
				if($nr_of_rows_mess == 0){
					$characters = '0123456789';
					$charactersLength = strlen($characters);
					$randomString = '';
					for ($i = 0; $i < 5; $i++) {
						$randomString .= $characters[rand(0, $charactersLength - 1)];
					}
					$bot_koriscnicko_ime = $kandidat_ime.$randomString;
					
					$query_user = $db->prepare("
								INSERT INTO users
									(phone, name, nalog_id, email, password, kandidat_id)
								VALUES
									(:phone, :name, :nalog_id, :email, :password, :kandidat_id)");

					$query_user->execute(array(
								':phone' => $mobile_phone,
								':name' => $kandidat_full_name,
								':nalog_id' => $lg_nalogid,
								':email' => $bot_koriscnicko_ime,
								':password' => $random_password,
								':kandidat_id' => $kandidat_id_c));
					$phone_f = str_replace("+", '00', $mobile_phone);
					//INFOBIP
					$link_dload = "https://crm.job-step.com/download";
					$link_uputs = "https://bit.ly/3V177tF";
					$kandidat_full_name = getCandidateFullnameR($kandidat_id_c);

					$to_send_viber_poruka_1 = $kandidat_full_name.$text_viber_new_prva_poruka; 
					$to_send_sms_poruka_1 	= $kandidat_full_name.$text_sms_new_prva_poruka; 

					$to_send_viber_poruka_2 = $text_viber_new_druga_poruka;
					$to_send_sms_poruka_2 	= $text_sms_new_druga_poruka." ".$link_dload;

					$to_send_viber_poruka_3 = $text_viber_new_treca_poruka_1.'\n\n'.$text_viber_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_viber_new_treca_poruka_3.$random_string;
					$to_send_sms_poruka_3 	= $text_sms_new_treca_poruka_1.'\n\n'.$text_sms_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_sms_new_treca_poruka_3.$random_string;


					viberPrijava1($phone_f, $to_send_sms_poruka_1, $to_send_viber_poruka_1);
					sleep(1);
					viberPrijava2($phone_f, $to_send_sms_poruka_2, $to_send_viber_poruka_2, $text_btn_new_druga_poruka, $link_dload);
					sleep(1);
					viberPrijava1($phone_f, $to_send_sms_poruka_3, $to_send_viber_poruka_3);
					sleep(1);

					if($lg_language == "bs"){
						$to_send_viber_poruka_4 = $text_viber_new_cetvrta_poruka;
						$to_send_sms_poruka_4 	= $text_sms_new_cetvrta_poruka." ".$link_uputs;
						
						viberPrijava2($phone_f, $to_send_sms_poruka_4, $to_send_viber_poruka_4, $text_btn_new_cetvrta_poruka, $link_uputs);
					}
					checkCandidateInputs($kandidat_id_c);
					if($kandidat_email != null)
						sendCandidateMessengerMail($random_string, $kandidat_email, $bot_koriscnicko_ime);
					
					
				}else{
					//AKO JE KANDIDAT VEC NA BOTU PROVJERITI STATUS
					if($kandidat_status == 2 OR $kandidat_status == 4 OR $kandidat_status == 5){
						//AKO JE OBRADJEN, NA KONTROLI ILI NA DOPUNI ONDA VEZATI GA ZA NALOG I STAVITI U ODGOVARAJUCI PROJEKAT
						$update_user = $db->prepare("
									UPDATE users
									SET nalog_id = :nalog_id
									WHERE kandidat_id = :kandidat_id
								");

						$update_user->execute(array(
									':nalog_id' => $lg_nalogid,
									':kandidat_id' => $kandidat_id_c));
									
						checkCandidateInputs($kandidat_id_c);
					}else{
						//AKO NIJE NISTA RADIO NA BOTU ONDA VEZATI GA ZA NALOG I POSLATI PODATKE PONOVO
						
						$characters = '0123456789';
						$charactersLength = strlen($characters);
						
						$randomString = '';
						for ($i = 0; $i < 5; $i++) {
							$randomString .= $characters[rand(0, $charactersLength - 1)];
						}
						$bot_koriscnicko_ime = $kandidat_ime.$randomString;
						
						$row_messenger = $check_messenger->fetch();
						$user_id = $row_messenger['id'];
						$mobile_phone = $row_messenger['phone'];
						
						$update_user = $db->prepare("
									UPDATE users
									SET nalog_id = :nalog_id, password = :password, email = :email
									WHERE kandidat_id = :kandidat_id
								");

						$update_user->execute(array(
									':nalog_id' => $lg_nalogid,
									':password' => $random_password,
									':email' => $bot_koriscnicko_ime,
									':kandidat_id' => $kandidat_id_c));
						
						$phone_f = str_replace("+", '', $mobile_phone);
						
						$link_dload = "https://crm.job-step.com/download";
						$link_uputs = "https://bit.ly/3V177tF";
						$kandidat_full_name = getCandidateFullnameR($kandidat_id_c);

						$to_send_viber_poruka_1 = $kandidat_full_name.$text_viber_new_prva_poruka; 
						$to_send_sms_poruka_1 	= $kandidat_full_name.$text_sms_new_prva_poruka; 

						$to_send_viber_poruka_2 = $text_viber_new_druga_poruka;
						$to_send_sms_poruka_2 	= $text_sms_new_druga_poruka." ".$link_dload;

						$to_send_viber_poruka_3 = $text_viber_new_treca_poruka_1.'\n\n'.$text_viber_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_viber_new_treca_poruka_3.$random_string;
						$to_send_sms_poruka_3 	= $text_sms_new_treca_poruka_1.'\n\n'.$text_sms_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_sms_new_treca_poruka_3.$random_string;
						// var_dump($to_send_viber_poruka_3);
						// var_dump($to_send_sms_poruka_3);
						// exit();
						viberPrijava1($phone_f, $to_send_sms_poruka_1, $to_send_viber_poruka_1);
						sleep(1);
						viberPrijava2($phone_f, $to_send_sms_poruka_2, $to_send_viber_poruka_2, $text_btn_new_druga_poruka, $link_dload);
						sleep(1);
						viberPrijava1($phone_f, $to_send_sms_poruka_3, $to_send_viber_poruka_3);
						sleep(1);

						if($lg_language == "bs"){
							$to_send_viber_poruka_4 = $text_viber_new_cetvrta_poruka;
							$to_send_sms_poruka_4 	= $text_sms_new_cetvrta_poruka." ".$link_uputs;
							
							viberPrijava2($phone_f, $to_send_sms_poruka_4, $to_send_viber_poruka_4, $text_btn_new_cetvrta_poruka, $link_uputs);
						}
						checkCandidateInputs($kandidat_id_c);
						if($kandidat_email != null)
							sendCandidateMessengerMail($random_string, $kandidat_email, $bot_koriscnicko_ime);
					}
				}
				
				//NOTIFIKACIJE
				notifForPonovnaPrijava($kandidat_id_c, $kandidat_check_exists);
				//header("Location: registracija/thank_you/$kandidat_id_c/$kandidat_check_exists");
				//header("Location: registracija/thank_you/$kandidat_id_c/$kandidat_check_exists/$lg_language");
			}
			
		break;

		case "add_kandidat_new_import_lilium":
			/*  // Build POST request:
			$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
			$recaptcha_secret = '6LfSU_cUAAAAAAZolZBCR5u5zfIZfl2roL3irH8_';
			$recaptcha_response = $_POST['recaptcha_response'];
			// Make and decode POST request:
			$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
			$recaptcha = json_decode($recaptcha);
			
			// Take action based on the score returned:
			if ($recaptcha->score >= 0.4) { 
				*/
				include("lang/bs.php");
				$kandidat_ime = $_POST['kandidat_ime'];
				$kandidat_prezime = $_POST['kandidat_prezime'];
				$urlid = $_POST['urlid'];
				// if($urlid == 1000){
					// $kj_znanje_njemacki = $_POST['nivo_jezika'];
					// var_dump($kj_znanje_njemacki);
					// exit();
				// }
				
				$lg_language = $_POST['lg_language'];
				//$kandidat_email = $_POST['kandidat_email'];
				$dan_rodjenja = $_POST["datum_dan"];
				$mjesec_rodjenja = $_POST["datum_mjesec"];
				$godina_rodjenja = $_POST["datum_godina"];
				$mobile_phone = $_POST['kki_phone'];
				$kandidat_drzavljanstvo_vrsta = $_POST['kandidat_drzavljanstvo_vrsta'];
				$partner_token = $_POST['token'];
				
				if(isset($_REQUEST['kandidat_email'])){
					$kandidat_email = $_REQUEST['kandidat_email'];
				}else{
					$kandidat_email = null;
				}
				
				if($lg_language == "de")
					include("lang/de.php");
				else
					include("lang/bs.php");
				
				$kandidat_full_name = $kandidat_ime." ".$kandidat_prezime;
				
				$kandidat_datumrodjenja = date('Y-m-d', strtotime($godina_rodjenja."-".$mjesec_rodjenja."-".$dan_rodjenja));
				
				//Postavljanje defulatnog urlid = 1 kad nema linka i uzimanje id naloga
				$check_query2 = $db->prepare("
										SELECT lg_id, lg_url, lg_nalogid
										FROM idk_link_generator
										WHERE lg_id = :lg_id");
			
				$check_query2->execute(array(
								':lg_id' => $urlid));
			
				$num = $check_query2->rowCount();
				$rowlg = $check_query2->fetch();
					
				$lg_nalogid = $rowlg['lg_nalogid'];
				
				if($num > 0){
					$url_id = $_POST['urlid'];
				}else{
					$url_id = 1;
				}
				
				//napravi username po imenu, prezimenu i godini
				$ime_korime = strtolower($kandidat_ime);
				$prezime_korime = strtolower($kandidat_prezime);
				$imeprezime = $ime_korime.''.$prezime_korime;
				$search = array("ć", "č", "ž", "š", "đ");
				$replacement = array("c", "c", "z", "s", "dj");
				$imeprezime_korime = str_replace($search, $replacement, $imeprezime);
			
				//VIZA I TERMIN
				if(isset($_POST['kandidat_viza']))
					$kandidat_viza_da_ne = $_POST['kandidat_viza'];
				else
					$kandidat_viza_da_ne ="";
				if(isset($_POST['kandidat_termin']))
					$kandidat_termin_da_ne = $_POST['kandidat_termin'];
				else
					$kandidat_termin_da_ne ="";
				if(isset($_POST['kandidat_apliciranje']))
					$kandidat_apliciranje_da_ne = $_POST['kandidat_apliciranje'];
				else
					$kandidat_apliciranje_da_ne ="";

				if(isset($_POST['kandidat_vozacka_dozvola'])){
					$kandidat_vozacka_dozvola = $_POST['kandidat_vozacka_dozvola'];
					if($kandidat_vozacka_dozvola == "Da"){
						$kandidat_vozacka_kategorija = "B";
					}else{
						$kandidat_vozacka_kategorija = null;
					}
				}else{
					$kandidat_vozacka_dozvola = null;
					$kandidat_vozacka_kategorija = null;
				}
				
				if(isset($_POST['kandidat_iskustvo_u_struci']))
					$kandidat_iskustvo_u_struci = $_POST['kandidat_iskustvo_u_struci'];
				else
					$kandidat_iskustvo_u_struci = null;
				
				if($kandidat_viza_da_ne == 1){
					$kandidat_viza = 1;
					$kandidat_viza_vrijedi_do = $_POST['kandidat_viza_vrijedi_do'];
					$kandidat_viza_vrijedi_do = date("Y-m-d", strtotime($kandidat_viza_vrijedi_do));
					$kandidat_datum_termina = null;
					$kandidat_datum_aplikacije = null;
					$kandidat_procjenatermina = 0;
				}else{
					$kandidat_viza = 0;
					$kandidat_viza_vrijedi_do = null;
					if($kandidat_termin_da_ne == 1){
						$kandidat_datum_termina = $_POST['kandidat_termin_date'];
						$kandidat_datum_termina = date("Y-m-d", strtotime($kandidat_datum_termina));
						$kandidat_datum_aplikacije = null;
						$kandidat_procjenatermina = 0;
					}else{
						if($kandidat_apliciranje_da_ne == 1){
							$kandidat_datum_aplikacije =  $_POST['kandidat_termin_date_app'];
							$kandidat_datum_aplikacije = date("Y-m-d", strtotime($kandidat_datum_aplikacije));
							$kandidat_procjenatermina = 1;
							$kandidat_datum_termina = date('Y-m-d', strtotime("+20 months", strtotime($kandidat_datum_aplikacije)));
						}else{
							$kandidat_procjenatermina = 0;
							$kandidat_datum_termina = null;
							$kandidat_datum_aplikacije = null;
						}
					}
				}
				
				if(isset($_POST['termin_carglass'])){
					$termin_za_vizu_carglass = $_POST['termin_carglass'];
				}else{
					$termin_za_vizu_carglass = null;
				}
				if(isset($_POST['grad_za_razgovor'])){
					$grad_za_razgovor = $_POST['grad_za_razgovor'];
				}else{
					$grad_za_razgovor = null;
				}

				//BORAVAK I SKOLE
				if($kandidat_drzavljanstvo_vrsta == "EU državljanin"){
					$eu_drzava = "NN";
				}else{
					$eu_drzava = "NE";
				}

				$skola_id_post = $_POST['skola_naziv'];
				if($skola_id_post != "ostalo"){
					$smjer_id_post = $_POST['smjer_naziv'];
					
					if($smjer_id_post == 0){
						$skola_naziv = "nema";
					}else{
						// var_dump($smjer_id_post);
						$query_skola_naziv = $db->prepare("SELECT skola_naziv FROM idk_skole WHERE skola_id = $skola_id_post");
						$query_skola_naziv->execute();
						$row_skola_naziv = $query_skola_naziv->fetch();
						$skola_naziv = $row_skola_naziv['skola_naziv'];
						
						$query_smjer_naziv = $db->prepare("SELECT ss_naziv FROM idk_skole_smjerovi WHERE ss_id = $smjer_id_post");
						$query_smjer_naziv->execute();
						$row_smjer_naziv = $query_smjer_naziv->fetch();
						$smjer_naziv = $row_smjer_naziv['ss_naziv'];
					}
					/*
					if(in_array($urlid, array(914,916,918,920,940))){
						
						$smjerovi_enpal = array(15,569,600,961,974,1069,2556,20,408,630,665,754,789,813,843,844,2190,2590,778,995);
						$smjerovi_solution = array(893,935,949,780,840,842,2748,383,1000,1716,25,26,861,448,295,305,9,845,508,29,47,262,585,641,654,2571,1932,1803,1605,30,685,831,497);
						$smjerovi_zajednicki = array(60,479,653,756,785,958,960,2215,260,404,412,489,595,602,2112,2890,729,928,863,927,978,787,830,590,612,1641,660,666,243,758,339,647,14,573,999,2547,2329,790,44,59,2715,2778,2022);

						if(in_array($smjer_id_post, $smjerovi_enpal)){
							
							switch($urlid){
								case 914: $urlid = 924; $url_id = 924;
								break;
								case 916: $urlid = 928; $url_id = 928;
								break;
								case 918: $urlid = 936; $url_id = 936;
								break;
								case 920: $urlid = 932; $url_id = 932;
								break;
								case 940: $urlid = 944; $url_id = 944;
								break;
							}
							$lg_nalogid = 254;
						}elseif(in_array($smjer_id_post, $smjerovi_solution)){
							switch($urlid){
								case 914: $urlid = 922; $url_id = 922;
								break;
								case 916: $urlid = 926; $url_id = 926;
								break;
								case 918: $urlid = 934; $url_id = 934;
								break;
								case 920: $urlid = 930; $url_id = 930;
								break;
								case 940: $urlid = 942; $url_id = 942;
								break;
							}
							$lg_nalogid = 262;
						}
					}
					*/

				}else{
					$skola_naziv = $_POST['skola_naziv_ru'];
					$smjer_naziv = $_POST['smjer_naziv_ru'];
					$skola_id_post = null;
					$smjer_id_post = null;
				}
				
				// exit();
				
				// Provjera da li postoji korisnik sa istim imenom i prezimenom u zadnjih sat vremena
				$granicno_vrijeme = date( "Y-m-d H:i:s",  strtotime("- 1 hour" ));
				$check_user = $db->prepare("
										SELECT kandidat_ime, kandidat_slika, kandidat_id, kandidat_status_messenger, kandidat_check, kandidat_visitedurl, kandidat_status, kandidat_vozacka_kategorija
										FROM idk_kandidati
										WHERE kandidat_ime = :kandidat_ime AND kandidat_prezime = :kandidat_prezime AND kandidat_mobitel = :kandidat_mobitel AND kandidat_status != 3");
			
				$check_user->execute(array(
								':kandidat_ime' => $kandidat_ime,
								':kandidat_prezime' => $kandidat_prezime,
								':kandidat_mobitel' => $mobile_phone
								));
			
				$number_of_rows_user = $check_user->rowCount();
				$rows_user = $check_user->fetch();
				$check_slika = $rows_user['kandidat_slika'];
				$kandidat_id_c = $rows_user['kandidat_id'];
				$kandidat_status_messenger = $rows_user['kandidat_status_messenger'];
				$kandidat_check_exists = $rows_user['kandidat_check'];
				$stari_visitedurl = $rows_user['kandidat_visitedurl'];
				$kandidat_status = $rows_user['kandidat_status'];
				
				$kandidat_group = $_POST['kandidat_prijava_na'];
				$group_title_query = $db->prepare("
								SELECT kg_id, kg_title
								FROM idk_kandidati_grupe
								WHERE kg_id = :kg_id
								");
		
				$group_title_query->execute(array(
					":kg_id" => $kandidat_group
				));
				$row_kg_title = $group_title_query->fetch();
				$kandidat_prijava_na = $row_kg_title['kg_title'];
				
				if(isset($_POST['datum_prijave'])){
					$kandidat_datetime = $_POST['datum_prijave'];
				}else{
					$kandidat_datetime = date('Y-m-d H:i:s');
				}
				$kandidat_check = md5(uniqid(rand(), true));
				
				if($number_of_rows_user == 0){
					
					// nisu ulazile duple prijave zbog korisnickog imena koji je kombinacija imenaprezimenadatumarodjenja, pa sam dodao
					//jos jedan token od 3 broja na to da moze ulaziti
					$random_token = rand(100, 999);
					$kandidat_korisnickoime_uf = $imeprezime_korime.''.$random_token;
					$kandidat_korisnickoime = str_replace(' ', '', strtolower($kandidat_korisnickoime_uf));
					$kandidat_password = MD5($kandidat_korisnickoime);
					
					//izbaceno provjeravanje da li ima kandidata sa ovakvim $kandidat_korisnickoime
					
					// $kandidat_mjestorodjenja = $_POST['kandidat_mjestorodjenja'];
					// $kandidat_drzavarodjenja = $_POST['kandidat_drzavarodjenja'];
					$kandidat_visitedurl = $_POST['kandidat_visitedurl'];
					
					/*
					$kandidat_datum_termina = $_POST['kandidat_termin_date'];
					$kandidat_datum_aplikacije = $_POST['kandidat_termin_date_app'];
					$datum_aplikacije = date("Y-m-d", strtotime($kandidat_datum_aplikacije));
					*/
					
					$kandidat_status = 0;
					
					if($lg_language == "bs" or $lg_language == "de")
						$kandidat_status_messenger = 1;
					else
						$kandidat_status_messenger = 4;
				
					//Upload and save kandidat_slika
					/*
					if($_FILES['kandidat_slika'] !== null){
						if($_FILES['kandidat_slika']['size'] !== 0) {
							$kandidat_slika = $_FILES['kandidat_slika'];
				
							//File properties
							$file_name = $kandidat_slika['name'];
							$file_tmp = $kandidat_slika['tmp_name'];
							$file_size = $kandidat_slika['size'];
							$file_error = $kandidat_slika['error'];
				
							//File extension
							$file_ext = explode('.', $file_name);
							$file_ext = strtolower(end($file_ext));
				
							$allowed = array('jpg', 'png');
				
							if(in_array($file_ext, $allowed)) {
				
								$kandidat_slika_final = uniqid() . '.' . $file_ext;
								$file_destination = 'files/kandidati/' . $kandidat_slika_final;
				
								if(move_uploaded_file($file_tmp, $file_destination)) {
				
									$path_to_image_directory = "files/kandidati/";
									$final_width_of_image = 660;
									ini_set('memory_limit', '-1');
									if(preg_match('/[.](jpg)$/', $kandidat_slika_final)) {
										$im = imagecreatefromjpeg($path_to_image_directory . $kandidat_slika_final);
									} else if (preg_match('/[.](png)$/', $kandidat_slika_final)) {
										$im = imagecreatefrompng($path_to_image_directory . $kandidat_slika_final);
									}
				
									$ox = imagesx($im);
									$oy = imagesy($im);
									$nx = $final_width_of_image;
									$ny = floor($oy * ($final_width_of_image / $ox));
									$nm = imagecreatetruecolor($nx, $ny);
				
									imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
									imagejpeg($nm, $path_to_image_directory . $kandidat_slika_final);
				
								}
							}else{
								$kandidat_slika_final = "none";
							}
						}else{
							$kandidat_slika_final = "none";
						}
					}else*/
						$kandidat_slika_final = "none";
					
					//ADD PARTNER DATA
						
					if($partner_token != null){
						$partner_query = $db->prepare("
										SELECT jp_id, jp_fcmtoken, jp_position, jp_imeprezime
										FROM idk_jobstep_partners
										WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token
										");
				
							$partner_query->execute(array(
								":jp_mailconfirmation_token" => $partner_token
						));
				
						$row_partner = $partner_query->fetch();
						$partner_id = $row_partner['jp_id'];
						$partner_position = $row_partner['jp_position'];
						$jp_fcmtoken = $row_partner['jp_fcmtoken'];
						$partner_ime = $row_partner['jp_imeprezime'];
						$log_poruka = "Novi kandidat ".$kandidat_ime." je prijavljen preko vašeg linka.";
						send_notification_partnerapp($jp_fcmtoken, $partner_ime, $log_poruka);
						$kandidat_porijeklo = 6;
					}else{
						$partner_id = null;
						$partner_position = null;
						$kandidat_porijeklo = 0;
					}
					
					//Add user to db
					$query = $db->prepare("
									INSERT INTO idk_kandidati
										( kandidat_check, kandidat_ime, kandidat_prezime, kandidat_drzavljanstvo_vrsta, kandidat_mobitel, kandidat_password, kandidat_slika, kandidat_status, kandidat_status_messenger, kandidat_datetime, kandidat_korisnickoime, kandidat_datumrodjenja, kandidat_visitedurl, kandidat_prijava_na, datum_termina, datum_aplikacije, kandidat_group, kandidat_procjenatermina, kandidat_viza, kandidat_viza_vrijedi_do, kandidat_status_prijave, kandidat_partnerid, kandidat_partner_status, kandidat_porijeklo, kandidat_iskustvo_u_struci, kandidat_termin_za_vizu, kandidat_vozacka_dozvola, kandidat_vozacka_kategorija)
									VALUES
										(:kandidat_check,:kandidat_ime,:kandidat_prezime,:kandidat_drzavljanstvo_vrsta,:kandidat_mobitel, :kandidat_password, :kandidat_slika, :kandidat_status, :kandidat_status_messenger, :kandidat_datetime, :kandidat_korisnickoime, :kandidat_datumrodjenja, :kandidat_visitedurl, :kandidat_prijava_na, :datum_termina, :datum_aplikacije, :kandidat_group, :kandidat_procjenatermina, :kandidat_viza, :kandidat_viza_vrijedi_do, :kandidat_status_prijave, :kandidat_partnerid, :kandidat_partner_status, :kandidat_porijeklo, :kandidat_iskustvo_u_struci, :kandidat_termin_za_vizu, :kandidat_vozacka_dozvola, :kandidat_vozacka_kategorija)");
			
					$query->execute(array(
								':kandidat_check' => $kandidat_check,
								':kandidat_ime' => $kandidat_ime,
								':kandidat_prezime' => $kandidat_prezime,
								':kandidat_drzavljanstvo_vrsta' => $kandidat_drzavljanstvo_vrsta,
								':kandidat_mobitel' => $mobile_phone,
								':kandidat_password' => $kandidat_password,
								':kandidat_slika' => $kandidat_slika_final,
								':kandidat_status' => $kandidat_status,
								':kandidat_status_messenger' => $kandidat_status_messenger,
								':kandidat_datetime' => $kandidat_datetime,
								':kandidat_korisnickoime' => $kandidat_korisnickoime,
								':kandidat_datumrodjenja' => $kandidat_datumrodjenja,
								':kandidat_visitedurl' => $url_id,
								':kandidat_prijava_na' => $kandidat_prijava_na,
								':datum_termina' => $kandidat_datum_termina,
								':datum_aplikacije' => $kandidat_datum_aplikacije,
								':kandidat_group' => $kandidat_group,
								':kandidat_procjenatermina' => $kandidat_procjenatermina,
								':kandidat_viza' => $kandidat_viza,
								':kandidat_viza_vrijedi_do' => $kandidat_viza_vrijedi_do,
								':kandidat_status_prijave' => 2,
								':kandidat_partnerid' => $partner_id,
								':kandidat_partner_status' => $partner_position,
								':kandidat_porijeklo' => $kandidat_porijeklo,
								':kandidat_iskustvo_u_struci' => $kandidat_iskustvo_u_struci,
								':kandidat_termin_za_vizu' => $termin_za_vizu_carglass,
								':kandidat_vozacka_dozvola' => $kandidat_vozacka_dozvola,
								':kandidat_vozacka_kategorija' => $kandidat_vozacka_kategorija
								));
								
					$kandidat_id = $db->lastInsertId();
					
					$query_log_status = $db->prepare("
							INSERT INTO idk_log_kandidat_statusi
								(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime)
							VALUES
								(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime)
					");
					
					$query_log_status->execute(array(
							':lks_kandidat_id' => $kandidat_id,
							':lks_status_obrade' => $kandidat_status,
							':lks_status_messenger' => $kandidat_status_messenger,
							':lks_datetime' => $kandidat_datetime
					));

					if($grad_za_razgovor != null){
						$note_group = 2;
						$note_dataid = $kandidat_id;
						$note_datetime = date('Y-m-d H:i:s');
						
						$note_txt_cert = "Izabrani grad za razgovor: ".$grad_za_razgovor;
						
						$query_biljeske = $db->prepare("
										INSERT INTO idk_notes
											(note_txt, note_datetime, note_group, note_dataid, note_employeeid)
										VALUES
											(:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");
	
						$query_biljeske->execute(array(
									':note_txt' => $note_txt_cert,
									':note_datetime' => $note_datetime,
									':note_group' => $note_group,
									':note_dataid' => $note_dataid,
									':note_employeeid' => 67));
					}

					if(isset($_POST['nivo_jezika'])){
						
						$kj_naziv_njemacki = "Njemački";
						$kj_znanje_njemacki = $_POST['nivo_jezika'];
						// Add language knowlege
						$query_njem = $db->prepare("
										INSERT INTO idk_kandidat_jezici
											(kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid)
										VALUES
											(:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid)");
				
						$query_njem->execute(array(
									':kj_naziv' => $kj_naziv_njemacki,
									':kj_slusanje' => $kj_znanje_njemacki,
									':kj_citanje' => $kj_znanje_njemacki,
									':kj_govorna_interakcija' => $kj_znanje_njemacki,
									':kj_govorna_produkcija' => $kj_znanje_njemacki,
									':kj_pisanje' => $kj_znanje_njemacki,
									':kj_kandidatid' => $kandidat_id));
					}
					
					if($urlid == 446){
						
						$get_project = $db->prepare("
												SELECT project_id
												FROM idk_projects
												WHERE project_nalogid = 117 AND (project_name LIKE '%$kandidat_prijava_na%') ");
					
						$get_project->execute();
					
						$gp_row = $get_project->fetch();
						$projectid = $gp_row['project_id'];
						
						$check_project = $db->prepare("
												SELECT pk_projectid
												FROM idk_project_kandidati
												WHERE pk_projectid = :pk_projectid AND pk_kandidatid = :pk_kandidatid");
					
						$check_project->execute(array(
											':pk_projectid' => $projectid,
											':pk_kandidatid' => $kandidat_id
						));
						if($check_project->rowCount() == 0){
							
							$query_project = $db->prepare("
											INSERT INTO idk_project_kandidati
												(pk_projectid, pk_kandidatid)
											VALUES
												(:pk_projectid, :pk_kandidatid)");
				
							$query_project->execute(array(
											':pk_projectid' => $projectid,
											':pk_kandidatid' => $kandidat_id));
						}
					}
					if($lg_nalogid != 0){
						if($kandidat_drzavljanstvo_vrsta == "EU državljanin"){
							$kandidat_drzavljanstvo_vrsta_txt = "EU";
							
							//IF USER IS EU - Transfer him into EU kandidati project within the order
							$nalog_query = $db->prepare("
													SELECT project_id
													FROM idk_projects
													WHERE project_nalogid = :project_nalogid AND project_eukandidati = 1");
						
							$nalog_query->execute(array(
											':project_nalogid' => $lg_nalogid));
						
							$nalogrow = $nalog_query->fetch();
							
							$project_id = $nalogrow['project_id'];
							
							$query_project = $db->prepare("
											INSERT INTO idk_project_kandidati
												(pk_projectid, pk_kandidatid)
											VALUES
												(:pk_projectid, :pk_kandidatid)");

							$query_project->execute(array(
											':pk_projectid' => $project_id,
											':pk_kandidatid' => $kandidat_id));						
							
						}else{
							$kandidat_drzavljanstvo_vrsta_txt = "NONEU";
							$mjesectermina = date("m",strtotime($kandidat_datum_termina));
							$godinatermina = date("Y",strtotime($kandidat_datum_termina));
							
							//Provjeri da li postoji mjesec termina unutar projekata
							$check_termin_month = $db->prepare("
													SELECT project_id, project_name
													FROM idk_projects
													WHERE project_nalogid = :project_nalogid AND $mjesectermina BETWEEN MONTH(project_datumtermina) AND MONTH(project_datumterminado) AND YEAR(project_datumtermina) = $godinatermina");
						
							$check_termin_month->execute(array(
											':project_nalogid' => $lg_nalogid));
						
							$terminmonthcount = $check_termin_month->rowcount();						
							$terminmonth = $check_termin_month->fetch();	
							$project_id_termin = $terminmonth['project_id'];					
							$project_name = $terminmonth['project_name'];						
							
							if($terminmonthcount == 0){
								
								//IF USER IS NON EU - Transfer him into Prijave project within the order
								$nalog_query = $db->prepare("
														SELECT project_id
														FROM idk_projects
														WHERE project_nalogid = :project_nalogid AND (project_name LIKE '%Prijave%' OR project_name LIKE '%Prijava%') ");
							
								$nalog_query->execute(array(
												':project_nalogid' => $lg_nalogid));
							
								$nalogrow = $nalog_query->fetch();
								if(!$nalogrow){
									//sta raditi ako nema projekta "PRIJAVA"
									//IDU U NOVE PROJEKTI ZA VIBER NALOG
								}else{
									
									$project_id = $nalogrow['project_id'];	
									
									$query_project = $db->prepare("
													INSERT INTO idk_project_kandidati
														(pk_projectid, pk_kandidatid)
													VALUES
														(:pk_projectid, :pk_kandidatid)");
						
									$query_project->execute(array(
													':pk_projectid' => $project_id,
													':pk_kandidatid' => $kandidat_id));	
									
									addToLogsStatusPrijave(NULL, $project_id, 2, $kandidat_id, 3);
								}
								
							}else{
								$query_project = $db->prepare("
												INSERT INTO idk_project_kandidati
													(pk_projectid, pk_kandidatid)
												VALUES
													(:pk_projectid, :pk_kandidatid)");
		
								$query_project->execute(array(
												':pk_projectid' => $project_id_termin,
												':pk_kandidatid' => $kandidat_id));	
							}
						}
					}
					
					//INSERT SKOLE
					if($skola_naziv != "nema" AND $skola_naziv != null){
						$insert_skole = $db->prepare("
										INSERT INTO idk_kandidat_edukacija
											(ke_naziv_kvalifikacije, ke_naziv, ke_vrsta_obrazovanja, ke_kandidat_id, ke_skola_id, ke_smjer_id)
										VALUES
											(:ke_naziv_kvalifikacije,:ke_naziv,:ke_vrsta_obrazovanja,:ke_kandidat_id,:ke_skola_id,:ke_smjer_id)");
				
						$insert_skole->execute(array(
									':ke_naziv_kvalifikacije' => $smjer_naziv,
									':ke_naziv' => $skola_naziv,
									':ke_vrsta_obrazovanja' => "srednje",
									':ke_skola_id' => $skola_id_post,
									':ke_smjer_id' => $smjer_id_post,
									':ke_kandidat_id' => $kandidat_id));
					}
					
					$kki_grupa = 1;
					$kki_naziv = "Mobilni";

					//Add mobilni to db
					$query_mob = $db->prepare("
									INSERT INTO idk_kandidat_kontakt_info
										(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
									VALUES
										(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

					$query_mob->execute(array(
								':kki_grupa' => $kki_grupa,
								':kki_naziv' => $kki_naziv,
								':kki_podatak' => $mobile_phone,
								':kki_kandidat_id' => $kandidat_id));
					
					$kki_grupa_e = 2;
					$kki_naziv_e = "E-mail";
					if($kandidat_email != null){
						$kki_podatak_e = $_POST['kandidat_email'];

						//Add kontakt info to db
						$query_email = $db->prepare("
										INSERT INTO idk_kandidat_kontakt_info
											(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
										VALUES
											(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

						$query_email->execute(array(
									':kki_grupa' => $kki_grupa_e,
									':kki_naziv' => $kki_naziv_e,
									':kki_podatak' => $kki_podatak_e,
									':kki_kandidat_id' => $kandidat_id));
					}
					
					//Add to table users (chatbot)
					$random_string = generateRandomString();
					$options = [
						'cost' => 10,
					];
					$random_password = password_hash($random_string, PASSWORD_BCRYPT, $options);
					
					//Add to logs candidate IP	
					$date_time_ip = date("F j, Y, g:i T");
					if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
						$ip = $_SERVER['HTTP_CLIENT_IP'];
					} elseif (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
						$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
					} else {
						$ip = $_SERVER['REMOTE_ADDR'];
					}
					$log_desc = "Kandidat: " . $kandidat_ime . " " . $kandidat_prezime . "(".$kandidat_id."). ".$date_time_ip."; IP: ".$ip." (".$random_string.")";
					$log_type = "5";
					addToLogs($log_desc, $log_type);
					
					// DE i IT kandidate slati na korak 2
					if($lg_language == "it"){
						
					}else{
						
						if($urlid == 0){
							
						}else{
							
							$characters = '0123456789';
							$charactersLength = strlen($characters);
							$randomString = '';
							for ($i = 0; $i < 5; $i++) {
								$randomString .= $characters[rand(0, $charactersLength - 1)];
							}
							$bot_koriscnicko_ime = $kandidat_ime.$randomString;
							
							$query_user = $db->prepare("
										INSERT INTO users
											(phone, name, nalog_id, email, password, kandidat_id)
										VALUES
											(:phone, :name, :nalog_id, :email, :password, :kandidat_id)");

							$query_user->execute(array(
										':phone' => $mobile_phone,
										':name' => $kandidat_full_name,
										':nalog_id' => $lg_nalogid,
										':email' => $bot_koriscnicko_ime,
										':password' => $random_password,
										':kandidat_id' => $kandidat_id));
							
							
							$phone_f = str_replace("+", '', $mobile_phone);
							/*
							sendSmsToCandidate1($random_string, $phone_f, $kandidat_prijava_na);
							sleep(1);  // Seconds
							sendSmsToCandidate2($random_string, $phone_f, $kandidat_prijava_na);
							sleep(1);  // Seconds
							sendSmsToCandidate3($random_string, $phone_f, $kandidat_email);
							sleep(1);  // Seconds
							sendSmsToCandidate4($random_string, $phone_f, $kandidat_email);
							*/
							
							
							$link_dload = "https://crm.job-step.com/download";
							$link_uputs = "https://bit.ly/3V177tF";

							$kandidat_full_name = getCandidateFullnameR($kandidat_id);

							$to_send_viber_poruka_1 = $kandidat_full_name.$text_viber_new_prva_poruka; 
							$to_send_sms_poruka_1 	= $kandidat_full_name.$text_sms_new_prva_poruka; 

							$to_send_viber_poruka_2 = $text_viber_new_druga_poruka;
							$to_send_sms_poruka_2 	= $text_sms_new_druga_poruka." ".$link_dload;

							$to_send_viber_poruka_3 = $text_viber_new_treca_poruka_1.'\n\n'.$text_viber_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_viber_new_treca_poruka_3.$random_string;
							$to_send_sms_poruka_3 	= $text_sms_new_treca_poruka_1.'\n\n'.$text_sms_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_sms_new_treca_poruka_3.$random_string;
							// var_dump($to_send_viber_poruka_3);
							// var_dump($to_send_sms_poruka_3);
							// exit();
							viberPrijava1($phone_f, $to_send_sms_poruka_1, $to_send_viber_poruka_1);
							sleep(1);
							viberPrijava2($phone_f, $to_send_sms_poruka_2, $to_send_viber_poruka_2, $text_btn_new_druga_poruka, $link_dload);
							sleep(1);
							viberPrijava1($phone_f, $to_send_sms_poruka_3, $to_send_viber_poruka_3);
							sleep(1);

							if($lg_language == "bs"){
								$to_send_viber_poruka_4 = $text_viber_new_cetvrta_poruka;
								$to_send_sms_poruka_4 	= $text_sms_new_cetvrta_poruka." ".$link_uputs;
								
								viberPrijava2($phone_f, $to_send_sms_poruka_4, $to_send_viber_poruka_4, $text_btn_new_cetvrta_poruka, $link_uputs);
							}
							if($kandidat_email != null)
								sendCandidateMessengerMail($random_string, $kandidat_email, $bot_koriscnicko_ime);
							checkCandidateInputs($kandidat_id);
							if($urlid == 530){
								
							}else{
								
							}
						}
					}
					notifForOnlneRegister($kandidat_id, $kandidat_check);
					
				}else{
					$kandidat_group = $_POST['kandidat_prijava_na'];
					$group_title_query = $db->prepare("
									SELECT kg_id, kg_title
									FROM idk_kandidati_grupe
									WHERE kg_id = :kg_id
									");
			
					$group_title_query->execute(array(
						":kg_id" => $kandidat_group
					));
					$row_kg_title = $group_title_query->fetch();
					$kandidat_prijava_na = $row_kg_title['kg_title'];
					$kandidat_vozacka_kategorija_c = $rows_user['kandidat_vozacka_kategorija'];
					
					//Ako kandidat vec ima unesenu kategoriju vozacke onda za update ide to sto ima uneseno
					//Ako nema onda kupimo sa prijave, a ako je na prijavi null i ovdje ce upasti null
					//ako je na prijavi ista uneseno onda ce biti uneseno samo B za kategoriju

					if($kandidat_vozacka_kategorija_c != null){
						$kandidat_vozacka_dozvola_for_update = "Da";
						$kandidat_vozacka_kategorija_for_update = $kandidat_vozacka_kategorija_c;
					}else{
						$kandidat_vozacka_dozvola_for_update = $kandidat_vozacka_dozvola;
						$kandidat_vozacka_kategorija_for_update = $kandidat_vozacka_kategorija;
					}
					/*if($check_slika == "none" or $check_slika == "none.jpg"){
						//Upload and save kandidat_slika
						if($_FILES['kandidat_slika'] !== null){
							if($_FILES['kandidat_slika']['size'] !== 0) {
								$kandidat_slika = $_FILES['kandidat_slika'];
					
								//File properties
								$file_name = $kandidat_slika['name'];
								$file_tmp = $kandidat_slika['tmp_name'];
								$file_size = $kandidat_slika['size'];
								$file_error = $kandidat_slika['error'];
					
								//File extension
								$file_ext = explode('.', $file_name);
								$file_ext = strtolower(end($file_ext));
					
								$allowed = array('jpg', 'png');
					
								if(in_array($file_ext, $allowed)) {
					
									$kandidat_slika_final = uniqid() . '.' . $file_ext;
									$file_destination = 'files/kandidati/' . $kandidat_slika_final;
					
									if(move_uploaded_file($file_tmp, $file_destination)) {
					
										$path_to_image_directory = "files/kandidati/";
										$final_width_of_image = 660;
					
										if(preg_match('/[.](jpg)$/', $kandidat_slika_final)) {
											$im = imagecreatefromjpeg($path_to_image_directory . $kandidat_slika_final);
										} else if (preg_match('/[.](png)$/', $kandidat_slika_final)) {
											$im = imagecreatefrompng($path_to_image_directory . $kandidat_slika_final);
										}
					
										$ox = imagesx($im);
										$oy = imagesy($im);
										$nx = $final_width_of_image;
										$ny = floor($oy * ($final_width_of_image / $ox));
										$nm = imagecreatetruecolor($nx, $ny);
					
										imagecopyresized($nm, $im, 0,0,0,0,$nx,$ny,$ox,$oy);
										imagejpeg($nm, $path_to_image_directory . $kandidat_slika_final);
					
									}
								}
							}else{
								$kandidat_slika_final = "none";
							}
						}else
							$kandidat_slika_final = "none";
					}else
					*/
						$kandidat_slika_final = "none";
					
					// var_dump($kandidat_id_c);
					// var_dump($kandidat_group);
					// exit();
					
					$check_messenger = $db->prepare("
											SELECT id, email, phone
											FROM users
											WHERE kandidat_id = :kandidat_id ");
				
					$check_messenger->execute(array(
									':kandidat_id' => $kandidat_id_c
									));
				
					$nr_of_rows_mess = $check_messenger->rowCount();
					if($nr_of_rows_mess == 0){
						$kandidat_status_messenger = 1;
					}else{
						
					}
					
					//ADD PARTNER DATA
						
					if($partner_token != null){
						$partner_query = $db->prepare("
										SELECT jp_id, jp_fcmtoken, jp_position, jp_imeprezime
										FROM idk_jobstep_partners
										WHERE jp_mailconfirmation_token = :jp_mailconfirmation_token
										");
				
							$partner_query->execute(array(
								":jp_mailconfirmation_token" => $partner_token
						));
				
						$row_partner = $partner_query->fetch();
						$partner_id = $row_partner['jp_id'];
						$partner_position = $row_partner['jp_position'];
						$jp_fcmtoken = $row_partner['jp_fcmtoken'];
						$partner_ime = $row_partner['jp_imeprezime'];
						$log_poruka = "Novi kandidat ".$kandidat_ime." je prijavljen preko vašeg linka.";
						send_notification_partnerapp($jp_fcmtoken, $partner_ime, $log_poruka);
						$kandidat_porijeklo = 7;
					}else{
						$partner_id = null;
						$partner_position = null;
						$kandidat_porijeklo = 0;
					}
					
					//Update user
					$update_user = $db->prepare("
									UPDATE idk_kandidati
									SET kandidat_drzavljanstvo_vrsta = :kandidat_drzavljanstvo_vrsta, kandidat_mobitel= :kandidat_mobitel, kandidat_slika = :kandidat_slika, kandidat_status_messenger = :kandidat_status_messenger, kandidat_datumrodjenja = :kandidat_datumrodjenja, datum_termina = :datum_termina, datum_aplikacije = :datum_aplikacije, kandidat_procjenatermina = :kandidat_procjenatermina, kandidat_viza = :kandidat_viza, kandidat_viza_vrijedi_do = :kandidat_viza_vrijedi_do, boravak_eu = :boravak_eu, kandidat_visitedurl = :kandidat_visitedurl, kandidat_prijava_na = :kandidat_prijava_na, kandidat_partnerid = :kandidat_partnerid, kandidat_partner_status = :kandidat_partner_status, kandidat_porijeklo = :kandidat_porijeklo, kandidat_iskustvo_u_struci = :kandidat_iskustvo_u_struci, kandidat_termin_za_vizu = :kandidat_termin_za_vizu, kandidat_vozacka_dozvola = :kandidat_vozacka_dozvola, kandidat_vozacka_kategorija = :kandidat_vozacka_kategorija
									WHERE kandidat_id = :kandidat_id
									");
			
					$update_user->execute(array(
								':kandidat_id' => $kandidat_id_c,
								':kandidat_drzavljanstvo_vrsta' => $kandidat_drzavljanstvo_vrsta,
								':kandidat_mobitel' => $mobile_phone,
								':kandidat_slika' => $kandidat_slika_final,
								':kandidat_status_messenger' => $kandidat_status_messenger,
								':kandidat_datumrodjenja' => $kandidat_datumrodjenja,
								':datum_termina' => $kandidat_datum_termina,
								':datum_aplikacije' => $kandidat_datum_aplikacije,
								':kandidat_procjenatermina' => $kandidat_procjenatermina,
								':kandidat_viza' => $kandidat_viza,
								':kandidat_viza_vrijedi_do' => $kandidat_viza_vrijedi_do,
								':boravak_eu' => $eu_drzava,
								':kandidat_visitedurl' => $url_id,
								':kandidat_prijava_na' => $kandidat_prijava_na,
								':kandidat_partnerid' => $partner_id,
								':kandidat_partner_status' => $partner_position,
								':kandidat_porijeklo' => $kandidat_porijeklo,
								':kandidat_iskustvo_u_struci' => $kandidat_iskustvo_u_struci,
								':kandidat_termin_za_vizu' => $termin_za_vizu_carglass,
								':kandidat_vozacka_dozvola' => $kandidat_vozacka_dozvola_for_update,
								':kandidat_vozacka_kategorija' => $kandidat_vozacka_kategorija_for_update
								));
					
					if($grad_za_razgovor != null){
						$note_group = 2;
						$note_dataid = $kandidat_id_c;
						$note_datetime = date('Y-m-d H:i:s');
						
						$note_txt_cert = "Izabrani grad za razgovor: ".$grad_za_razgovor;
						
						$query_biljeske = $db->prepare("
										INSERT INTO idk_notes
											(note_txt, note_datetime, note_group, note_dataid, note_employeeid)
										VALUES
											(:note_txt, :note_datetime, :note_group, :note_dataid, :note_employeeid)");
	
						$query_biljeske->execute(array(
									':note_txt' => $note_txt_cert,
									':note_datetime' => $note_datetime,
									':note_group' => $note_group,
									':note_dataid' => $note_dataid,
									':note_employeeid' => 67));
					}
					//INSERT/UPDATE JEZIKA
					if(isset($_POST['nivo_jezika'])){
						$kj_naziv_njemacki = "Njemački";
						$kj_znanje_njemacki = $_POST['nivo_jezika'];
						$query_check = $db->prepare("SELECT
														kj_id
													FROM
														idk_kandidat_jezici
													WHERE
														kj_naziv = :kj_naziv
													AND
														kj_kandidatid = :kandidat_id
													");
						$query_check->execute(array(
							":kandidat_id" => $kandidat_id_c,
							":kj_naziv" => $kj_naziv_njemacki
						));
						$count_jezik = $query_check->rowCount();
						$result_query_check = $query_check->fetch();
						if($count_jezik > 0){
							$kj_id = $result_query_check['kj_id'];
							//UPDATE
							$query_njem = $db->prepare("
								UPDATE idk_kandidat_jezici
								SET kj_naziv = :kj_naziv, kj_citanje = :kj_citanje, kj_slusanje = :kj_slusanje, kj_citanje = :kj_citanje, kj_govorna_interakcija = :kj_govorna_interakcija, kj_govorna_produkcija = :kj_govorna_produkcija, kj_pisanje = :kj_pisanje
								WHERE kj_kandidatid = :kj_kandidatid
								");
							$query_njem->execute(array(
										':kj_naziv' => $kj_naziv_njemacki,
										':kj_slusanje' => $kj_znanje_njemacki,
										':kj_citanje' => $kj_znanje_njemacki,
										':kj_govorna_interakcija' => $kj_znanje_njemacki,
										':kj_govorna_produkcija' => $kj_znanje_njemacki,
										':kj_pisanje' => $kj_znanje_njemacki,
										':kj_kandidatid' => $kandidat_id_c));		
						} else {
								//INSERT
							
							// Add language knowlege
							$query_njem = $db->prepare("
											INSERT INTO idk_kandidat_jezici
												(kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid)
											VALUES
												(:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid)");
					
							$query_njem->execute(array(
										':kj_naziv' => $kj_naziv_njemacki,
										':kj_slusanje' => $kj_znanje_njemacki,
										':kj_citanje' => $kj_znanje_njemacki,
										':kj_govorna_interakcija' => $kj_znanje_njemacki,
										':kj_govorna_produkcija' => $kj_znanje_njemacki,
										':kj_pisanje' => $kj_znanje_njemacki,
										':kj_kandidatid' => $kandidat_id_c));
						}
					}
					//INSERT INTO KANDIDAT LOG STATUSI
					
					$query_log_status = $db->prepare("
							INSERT INTO idk_log_kandidat_statusi
								(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime)
							VALUES
								(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime)
					");
					
					$query_log_status->execute(array(
							':lks_kandidat_id' => $kandidat_id_c,
							':lks_status_obrade' => 9,
							':lks_status_messenger' => 0,
							':lks_datetime' => $kandidat_datetime
					));
					
					if($urlid == 446){
						
						$get_project = $db->prepare("
												SELECT project_id
												FROM idk_projects
												WHERE project_nalogid = 117 AND (project_name LIKE '%$kandidat_prijava_na%') ");
					
						$get_project->execute();
					
						$gp_row = $get_project->fetch();
						$projectid = $gp_row['project_id'];
						
						$check_project = $db->prepare("
												SELECT pk_projectid
												FROM idk_project_kandidati
												WHERE pk_projectid = :pk_projectid AND pk_kandidatid = :pk_kandidatid");
					
						$check_project->execute(array(
											':pk_projectid' => $projectid,
											':pk_kandidatid' => $kandidat_id_c
						));
						if($check_project->rowCount() == 0){
							
							$query_project = $db->prepare("
											INSERT INTO idk_project_kandidati
												(pk_projectid, pk_kandidatid)
											VALUES
												(:pk_projectid, :pk_kandidatid)");
				
							$query_project->execute(array(
											':pk_projectid' => $projectid,
											':pk_kandidatid' => $kandidat_id_c));
						}
					}
					
					//INSERT INTO PROJEKAT
					
					$rezervisanFlag = 0;
					if($lg_nalogid != 0){
						$nalog_query = $db->prepare("
												SELECT project_id
												FROM idk_projects
												WHERE project_nalogid = :project_nalogid AND (project_name LIKE '%Prijave%' OR project_name LIKE '%Prijava%') ");
					
						$nalog_query->execute(array(
										':project_nalogid' => $lg_nalogid));
					
						$nalogrow = $nalog_query->fetch();
						if(!$nalogrow){
							//sta raditi ako nema projekta "PRIJAVA"
							//IDU U NOVE PROJEKTI ZA VIBER NALOG
							
						}else{
						
							$project_id = $nalogrow['project_id'];	
							$check_project = $db->prepare("
													SELECT pk_projectid
													FROM idk_project_kandidati
													WHERE pk_projectid = :pk_projectid AND pk_kandidatid = :pk_kandidatid");
						
							$check_project->execute(array(
												':pk_projectid' => $project_id,
												':pk_kandidatid' => $kandidat_id_c
							));
							if($check_project->rowCount() == 0){
								
								//Provjera rezervisanosti START
								
								$queryPrijavaNalog = $db->prepare("
									SELECT 
										kandidat_nalog_id, kandidat_status_prijave
									FROM 
										idk_kandidati
									WHERE 
										kandidat_id = :kandidat_id
								");
								$queryPrijavaNalog->execute(array(
									':kandidat_id' => $kandidat_id_c
								));
								$rowPrijavaNalog = $queryPrijavaNalog->fetch();
								$nalogIdPN = intval($rowPrijavaNalog["kandidat_nalog_id"]);
								$statusPrijavePN = intval($rowPrijavaNalog["kandidat_status_prijave"]);
								
								$queryProjekti = $db->prepare("
									SELECT 
										count(pk.pk_id) AS brojac
									FROM 
										idk_project_kandidati pk
									JOIN 
										idk_projects p
									ON 
										pk.pk_projectid = p.project_id
									WHERE 
										pk.pk_kandidatid = :pk_kandidatid
										AND 
										(
											project_name LIKE '%Intervju%' 
											OR 
											project_name LIKE '%Obrađeno%' 
											OR 
											project_name LIKE '%Završeni kandidati%' 
											OR 
											project_name LIKE '%BOT - ispunjava uslove%' 
											OR 
											project_name LIKE '%Baza - odgovara za nalog%'
											OR 
											project_name LIKE '%Casting%' 
											OR 
											project_name LIKE '%Ugovor%'
										)
										AND p.project_nalogid != 44
								");
								$queryProjekti->execute(array(
									':pk_kandidatid' => $kandidat_id_c
								));
								$rowProjekti = $queryProjekti->fetch();
								$brojacUProjektuP = intval($rowProjekti["brojac"]);
								
								if($nalogIdPN != 0 OR $brojacUProjektuP != 0 OR $statusPrijavePN == 4){
									$rezervisanFlag = 1;
								}
								
								if($rezervisanFlag == 0){
									$query_project = $db->prepare("
													INSERT INTO idk_project_kandidati
														(pk_projectid, pk_kandidatid)
													VALUES
														(:pk_projectid, :pk_kandidatid)");
						
									$query_project->execute(array(
													':pk_projectid' => $project_id,
													':pk_kandidatid' => $kandidat_id_c));
									addToLogsStatusPrijave(NULL, $project_id, 2, $kandidat_id_c, 3);
								}else{
									pushToProjectCandidateQueue($kandidat_id_c, $project_id);
								}
								//Provjera rezervisanosti END 
							}
						}
					}
					
					//INSERT SKOLE
					if($skola_naziv != "nema" AND $skola_naziv != null){
						$insert_skole = $db->prepare("
										INSERT INTO idk_kandidat_edukacija
											(ke_naziv_kvalifikacije, ke_naziv, ke_vrsta_obrazovanja, ke_kandidat_id, ke_skola_id, ke_smjer_id)
										VALUES
											(:ke_naziv_kvalifikacije, :ke_naziv, :ke_vrsta_obrazovanja, :ke_kandidat_id, :ke_skola_id, :ke_smjer_id)");
				
						$insert_skole->execute(array(
									':ke_naziv_kvalifikacije' => $smjer_naziv,
									':ke_naziv' => $skola_naziv,
									':ke_vrsta_obrazovanja' => "srednje",
									':ke_skola_id' => $skola_id_post,
									':ke_smjer_id' => $smjer_id_post,
									':ke_kandidat_id' => $kandidat_id_c));
					}
					
					//Add to table users (chatbot)
					$random_string = generateRandomString();
					$options = [
						'cost' => 10,
					];
					$random_password = password_hash($random_string, PASSWORD_BCRYPT, $options);
					
					//Add to logs candidate IP	
					$date_time_ip = date("F j, Y, g:i T");
					if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
						$ip = $_SERVER['HTTP_CLIENT_IP'];
					} elseif (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
						$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
					} else {
						$ip = $_SERVER['REMOTE_ADDR'];
					}
					$log_desc = "Kandidat se ponovo prijavio: " . $kandidat_ime . " " . $kandidat_prezime . ". ".$date_time_ip."; IP: ".$ip." (".$random_string.").";
					$log_type = "5";
					addToLogs($log_desc, $log_type);
					
					//JEZICI, BOT NALOG=0...SAMO BOT
					
					if($nr_of_rows_mess == 0){
						$characters = '0123456789';
						$charactersLength = strlen($characters);
						$randomString = '';
						for ($i = 0; $i < 5; $i++) {
							$randomString .= $characters[rand(0, $charactersLength - 1)];
						}
						$bot_koriscnicko_ime = $kandidat_ime.$randomString;
						
						$query_user = $db->prepare("
									INSERT INTO users
										(phone, name, nalog_id, email, password, kandidat_id)
									VALUES
										(:phone, :name, :nalog_id, :email, :password, :kandidat_id)");

						$query_user->execute(array(
									':phone' => $mobile_phone,
									':name' => $kandidat_full_name,
									':nalog_id' => $lg_nalogid,
									':email' => $bot_koriscnicko_ime,
									':password' => $random_password,
									':kandidat_id' => $kandidat_id_c));
						$phone_f = str_replace("+", '00', $mobile_phone);
						//INFOBIP
						$link_dload = "https://crm.job-step.com/download";
						$link_uputs = "https://bit.ly/3V177tF";
						$kandidat_full_name = getCandidateFullnameR($kandidat_id_c);

						$to_send_viber_poruka_1 = $kandidat_full_name.$text_viber_new_prva_poruka; 
						$to_send_sms_poruka_1 	= $kandidat_full_name.$text_sms_new_prva_poruka; 

						$to_send_viber_poruka_2 = $text_viber_new_druga_poruka;
						$to_send_sms_poruka_2 	= $text_sms_new_druga_poruka." ".$link_dload;

						$to_send_viber_poruka_3 = $text_viber_new_treca_poruka_1.'\n\n'.$text_viber_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_viber_new_treca_poruka_3.$random_string;
						$to_send_sms_poruka_3 	= $text_sms_new_treca_poruka_1.'\n\n'.$text_sms_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_sms_new_treca_poruka_3.$random_string;


						viberPrijava1($phone_f, $to_send_sms_poruka_1, $to_send_viber_poruka_1);
						sleep(1);
						viberPrijava2($phone_f, $to_send_sms_poruka_2, $to_send_viber_poruka_2, $text_btn_new_druga_poruka, $link_dload);
						sleep(1);
						viberPrijava1($phone_f, $to_send_sms_poruka_3, $to_send_viber_poruka_3);
						sleep(1);

						if($lg_language == "bs"){
							$to_send_viber_poruka_4 = $text_viber_new_cetvrta_poruka;
							$to_send_sms_poruka_4 	= $text_sms_new_cetvrta_poruka." ".$link_uputs;
							
							viberPrijava2($phone_f, $to_send_sms_poruka_4, $to_send_viber_poruka_4, $text_btn_new_cetvrta_poruka, $link_uputs);
						}
						if($rezervisanFlag == 0){
							checkCandidateInputs($kandidat_id_c);
						}
						if($kandidat_email != null)
							sendCandidateMessengerMail($random_string, $kandidat_email, $bot_koriscnicko_ime);
						
						
					}else{
						//AKO JE KANDIDAT VEC NA BOTU PROVJERITI STATUS
						if($kandidat_status == 2 OR $kandidat_status == 4 OR $kandidat_status == 5){
							//AKO JE OBRADJEN, NA KONTROLI ILI NA DOPUNI ONDA VEZATI GA ZA NALOG I STAVITI U ODGOVARAJUCI PROJEKAT
							if($rezervisanFlag == 0){
								$update_user = $db->prepare("
											UPDATE users
											SET nalog_id = :nalog_id
											WHERE kandidat_id = :kandidat_id
										");

								$update_user->execute(array(
											':nalog_id' => $lg_nalogid,
											':kandidat_id' => $kandidat_id_c));
							
								checkCandidateInputs($kandidat_id_c);
							}
						}else{
							//AKO NIJE NISTA RADIO NA BOTU ONDA VEZATI GA ZA NALOG I POSLATI PODATKE PONOVO
							
							
							if($rezervisanFlag == 0){
								
								$characters = '0123456789';
								$charactersLength = strlen($characters);
								
								$randomString = '';
								for ($i = 0; $i < 5; $i++) {
									$randomString .= $characters[rand(0, $charactersLength - 1)];
								}
								$bot_koriscnicko_ime = $kandidat_ime.$randomString;
								
								$row_messenger = $check_messenger->fetch();
								$user_id = $row_messenger['id'];
								$mobile_phone = $row_messenger['phone'];
								
								$update_user = $db->prepare("
											UPDATE users
											SET nalog_id = :nalog_id, password = :password, email = :email
											WHERE kandidat_id = :kandidat_id
										");

								$update_user->execute(array(
											':nalog_id' => $lg_nalogid,
											':password' => $random_password,
											':email' => $bot_koriscnicko_ime,
											':kandidat_id' => $kandidat_id_c));
								
								$phone_f = str_replace("+", '', $mobile_phone);
								
								$link_dload = "https://crm.job-step.com/download";
								$link_uputs = "https://bit.ly/3V177tF";
								$kandidat_full_name = getCandidateFullnameR($kandidat_id_c);

								$to_send_viber_poruka_1 = $kandidat_full_name.$text_viber_new_prva_poruka; 
								$to_send_sms_poruka_1 	= $kandidat_full_name.$text_sms_new_prva_poruka; 

								$to_send_viber_poruka_2 = $text_viber_new_druga_poruka;
								$to_send_sms_poruka_2 	= $text_sms_new_druga_poruka." ".$link_dload;

								$to_send_viber_poruka_3 = $text_viber_new_treca_poruka_1.'\n\n'.$text_viber_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_viber_new_treca_poruka_3.$random_string;
								$to_send_sms_poruka_3 	= $text_sms_new_treca_poruka_1.'\n\n'.$text_sms_new_treca_poruka_2.$bot_koriscnicko_ime.'\n'.$text_sms_new_treca_poruka_3.$random_string;
								// var_dump($to_send_viber_poruka_3);
								// var_dump($to_send_sms_poruka_3);
								// exit();
								viberPrijava1($phone_f, $to_send_sms_poruka_1, $to_send_viber_poruka_1);
								sleep(1);
								viberPrijava2($phone_f, $to_send_sms_poruka_2, $to_send_viber_poruka_2, $text_btn_new_druga_poruka, $link_dload);
								sleep(1);
								viberPrijava1($phone_f, $to_send_sms_poruka_3, $to_send_viber_poruka_3);
								sleep(1);

								if($lg_language == "bs"){
									$to_send_viber_poruka_4 = $text_viber_new_cetvrta_poruka;
									$to_send_sms_poruka_4 	= $text_sms_new_cetvrta_poruka." ".$link_uputs;
									
									viberPrijava2($phone_f, $to_send_sms_poruka_4, $to_send_viber_poruka_4, $text_btn_new_cetvrta_poruka, $link_uputs);
								}
								
								checkCandidateInputs($kandidat_id_c);
								
								if($kandidat_email != null)
									sendCandidateMessengerMail($random_string, $kandidat_email, $bot_koriscnicko_ime);
							}
						}
					}
					
					//SKOLE
					
					//NOTIFIKACIJE
					notifForPonovnaPrijava($kandidat_id_c, $kandidat_check_exists);
					//header("Location: registracija/thank_you/$kandidat_id_c/$kandidat_check_exists");
					if($urlid == 530){
						
					}else{
						
					}
					
				}
			/*}else{
				echo "Greška! Molimo Vas pokusajte kasnije.";
			}*/
			
			
		break;

		case "add_kandidat_from_viberChatBot_manual":

			$kandidat_ime = trim($_POST['kandidat_ime']);
			$kandidat_prezime = trim($_POST['kandidat_prezime']);
			$url_id = $_POST['urlid'];
			$lg_language = $_POST['lg_language'];
			$mobile_phone = $_POST['kki_phone'];
			$kandidat_group = $_POST['kandidat_prijava_na'];
			
			$kandidat_full_name = $kandidat_ime." ".$kandidat_prezime;

			$kandidat_datetime = $_POST['datum_prijave'];
			$kandidat_check = md5(uniqid(rand(), true));

			//uzimanje naziva grupe
				$group_title_query = $db->prepare("
								SELECT kg_id, kg_title
								FROM idk_kandidati_grupe
								WHERE kg_id = :kg_id
								");
		
				$group_title_query->execute(array(
					":kg_id" => $kandidat_group
				));
				$row_kg_title = $group_title_query->fetch();
				$kandidat_prijava_na_txt = $row_kg_title['kg_title'];
			//uzimanje naziva grupe

			//uzimanje id naloga
				$check_query2 = $db->prepare("
							SELECT lg_nalogid
							FROM idk_link_generator
							WHERE lg_id = :lg_id");

				$check_query2->execute(array(
					':lg_id' => $url_id));

				$num = $check_query2->rowCount();
				$rowlg = $check_query2->fetch();

				$lg_nalogid = $rowlg['lg_nalogid'];
			//uzimanje id naloga

			//GETANJE SKOLE SA FORME - ISTO CODE TREBA I ZA NORMALNU I PONOVNU PRIJAVU
				if(isset($_POST['smjer_naziv']) && $_POST['smjer_naziv'] != "" && $_POST['smjer_naziv'] != "Ostalo"){
							
					//unos rucne skole za defaultnu formu
					$smjer_naziv = $_POST["smjer_naziv"];
					
					//provjeri da li smjer vec postoji
					$query_smjer_naziv = $db->prepare("SELECT 
														ss_id,
														skola_id,
														ss_naziv,
														skola_naziv
													FROM 
														idk_skole_smjerovi 
													JOIN 
														idk_skole 
													ON 
														idk_skole_smjerovi.ss_skola_id = idk_skole.skola_id 
													WHERE 
														ss_naziv = '$smjer_naziv'
													");
					$query_smjer_naziv->execute();
					$count = $query_smjer_naziv->rowCount();
					$row_smjer_naziv = $query_smjer_naziv->fetch();
					if($count > 0){
						$smjer_id_post = $row_smjer_naziv['ss_id'];
						$smjer_naziv = $row_smjer_naziv['ss_naziv'];
						$skola_id_post = $row_smjer_naziv['skola_id'];
						$skola_naziv = $row_smjer_naziv['skola_naziv'];
					} else {
						$smjer_id_post = null;
						$smjer_naziv = $_POST["smjer_naziv_ru"];
						$skola_id_post = null;
						$skola_naziv = "Nepoznato";
					}
				}else{
					$skola_naziv = "nema";
				}
			//GETANJE SKOLE SA FORME - ISTO CODE TREBA I ZA NORMALNU I PONOVNU PRIJAVU
			
			// Provjera da li postoji korisnik u bazi
			$check_user = $db->prepare("
									SELECT kandidat_ime, kandidat_id, kandidat_status_messenger, kandidat_status
									FROM idk_kandidati
									WHERE TRIM(kandidat_ime) = :kandidat_ime AND TRIM(kandidat_prezime) = :kandidat_prezime AND (kandidat_mobitel = :kandidat_mobitel) AND kandidat_status != 3");
		
			$check_user->execute(array(
							':kandidat_ime' => $kandidat_ime,
							':kandidat_prezime' => $kandidat_prezime,
							':kandidat_mobitel' => $mobile_phone
							));
		
			$number_of_rows_user = $check_user->rowCount();
			
			// Provjera da li postoji korisnik u bazi
			
			//NOVA PRIJAVA
			if($number_of_rows_user == 0){
				
				//Add user to db
					$query = $db->prepare("
									INSERT INTO idk_kandidati
										( kandidat_check, kandidat_ime, kandidat_prezime, kandidat_mobitel, kandidat_slika, kandidat_status, kandidat_status_messenger, kandidat_datetime, kandidat_visitedurl, kandidat_prijava_na, kandidat_group, kandidat_status_prijave, kandidat_porijeklo)
									VALUES
										(:kandidat_check,:kandidat_ime,:kandidat_prezime,:kandidat_mobitel,:kandidat_slika,:kandidat_status,:kandidat_status_messenger,:kandidat_datetime,:kandidat_visitedurl,:kandidat_prijava_na,:kandidat_group,:kandidat_status_prijave,:kandidat_porijeklo)");

					$query->execute(array(
								':kandidat_check' => $kandidat_check,
								':kandidat_ime' => $kandidat_ime,
								':kandidat_prezime' => $kandidat_prezime,
								':kandidat_mobitel' => $mobile_phone,
								':kandidat_slika' => "none",
								':kandidat_status' => 0,
								':kandidat_status_messenger' => 0,
								':kandidat_datetime' => $kandidat_datetime,
								':kandidat_visitedurl' => $url_id,
								':kandidat_prijava_na' => $kandidat_prijava_na_txt,
								':kandidat_group' => $kandidat_group,
								':kandidat_status_prijave' => 2,
								':kandidat_porijeklo' => 0
								));
						
					$kandidat_id = $db->lastInsertId();
				
				//Add user to db
				
				// INSERT LOG STATUSA
					$query_log_status = $db->prepare("
							INSERT INTO idk_log_kandidat_statusi
								(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime, lks_link_id)
							VALUES
								(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime, :lks_link_id)
					");
					
					$query_log_status->execute(array(
							':lks_kandidat_id' => $kandidat_id,
							':lks_status_obrade' => 0,
							':lks_status_messenger' => 0,
							':lks_datetime' => $kandidat_datetime,
							':lks_link_id' => $url_id
					));
				// INSERT LOG STATUSA

				//INSERT JEZIKA
					if(isset($_POST['nivo_jezika'])){
										
						$kj_naziv_njemacki = "Njemački";
						$kj_znanje_njemacki = $_POST['nivo_jezika'];
						// Add language knowlege
						$query_njem = $db->prepare("
										INSERT INTO idk_kandidat_jezici
											(kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid)
										VALUES
											(:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid)");
				
						$query_njem->execute(array(
									':kj_naziv' => $kj_naziv_njemacki,
									':kj_slusanje' => $kj_znanje_njemacki,
									':kj_citanje' => $kj_znanje_njemacki,
									':kj_govorna_interakcija' => $kj_znanje_njemacki,
									':kj_govorna_produkcija' => $kj_znanje_njemacki,
									':kj_pisanje' => $kj_znanje_njemacki,
									':kj_kandidatid' => $kandidat_id));
					}
				//INSERT JEZIKA

				//Transfer candidate into Prijave project within the order
				if($lg_nalogid != 0){
					$nalog_query = $db->prepare("
									SELECT project_id
									FROM idk_projects
									WHERE project_nalogid = :project_nalogid AND (project_name LIKE '%Prijave%' OR project_name LIKE '%Prijava%') ");

					$nalog_query->execute(array(
							':project_nalogid' => $lg_nalogid));

					$nalogrow = $nalog_query->fetch();
					if(!$nalogrow){
						
					}else{

						$project_id = $nalogrow['project_id'];	

						$query_project = $db->prepare("
									INSERT INTO idk_project_kandidati
										(pk_projectid, pk_kandidatid)
									VALUES
										(:pk_projectid, :pk_kandidatid)");

						$query_project->execute(array(
									':pk_projectid' => $project_id,
									':pk_kandidatid' => $kandidat_id));	

						addToLogsStatusPrijave(NULL, $project_id, 2, $kandidat_id, 3);
					}
				}
				//Transfer candidate into Prijave project within the order

				//INSERT TELEFONA
					$kki_grupa = 1;
					$kki_naziv = "Mobilni";

					//Add mobilni to db
					$query_mob = $db->prepare("
									INSERT INTO idk_kandidat_kontakt_info
										(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
									VALUES
										(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

					$query_mob->execute(array(
								':kki_grupa' => $kki_grupa,
								':kki_naziv' => $kki_naziv,
								':kki_podatak' => $mobile_phone,
								':kki_kandidat_id' => $kandidat_id));
					
				//INSERT TELEFONA

				//INSERT SKOLE
				
					if($skola_naziv != "nema" AND $skola_naziv != null){
						$insert_skole = $db->prepare("
										INSERT INTO idk_kandidat_edukacija
											(ke_naziv_kvalifikacije, ke_naziv, ke_vrsta_obrazovanja, ke_kandidat_id, ke_skola_id, ke_smjer_id)
										VALUES
											(:ke_naziv_kvalifikacije,:ke_naziv,:ke_vrsta_obrazovanja,:ke_kandidat_id,:ke_skola_id,:ke_smjer_id)");
				
						$insert_skole->execute(array(
									':ke_naziv_kvalifikacije' => $smjer_naziv,
									':ke_naziv' => $skola_naziv,
									':ke_vrsta_obrazovanja' => "srednje",
									':ke_skola_id' => $skola_id_post,
									':ke_smjer_id' => $smjer_id_post,
									':ke_kandidat_id' => $kandidat_id));
					}
				//INSERT SKOLE

				$log_desc = "Kandidat: " . $kandidat_ime . " " . $kandidat_prezime . "(".$kandidat_id.") se prijavio putem viber chat bota - ručni unos. ";
				$log_type = "5";
				addToLogs($log_desc, $log_type);
			
			}else{
				$rows_user = $check_user->fetch();
				$kandidat_id_c = $rows_user['kandidat_id'];
				$kandidat_status_messenger = $rows_user['kandidat_status_messenger'];
				$kandidat_status = $rows_user['kandidat_status'];
				
				//Update user
					$update_user = $db->prepare("
									UPDATE idk_kandidati
									SET kandidat_mobitel= :kandidat_mobitel, kandidat_visitedurl = :kandidat_visitedurl, kandidat_prijava_na = :kandidat_prijava_na, kandidat_group = :kandidat_group
									WHERE kandidat_id = :kandidat_id
									");

					$update_user->execute(array(
								':kandidat_id' => $kandidat_id_c,
								':kandidat_mobitel' => $mobile_phone,
								':kandidat_visitedurl' => $url_id,
								':kandidat_group' => $kandidat_group,
								':kandidat_prijava_na' => $kandidat_prijava_na_txt
								));
				//Update user

				//INSERT INTO KANDIDAT LOG STATUSI
					$query_log_status = $db->prepare("
							INSERT INTO idk_log_kandidat_statusi
								(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime, lks_link_id)
							VALUES
								(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime, :lks_link_id)
					");
					
					$query_log_status->execute(array(
							':lks_kandidat_id' => $kandidat_id_c,
							':lks_status_obrade' => 9,
							':lks_status_messenger' => 0,
							':lks_datetime' => $kandidat_datetime,
							':lks_link_id' => $url_id
					));
				//INSERT INTO KANDIDAT LOG STATUSI

				//INSERT/UPDATE JEZIKA
					if(isset($_POST['nivo_jezika'])){
						$kj_naziv_njemacki = "Njemački";
						$kj_znanje_njemacki = $_POST['nivo_jezika'];
						$query_check = $db->prepare("SELECT
														kj_id
													FROM
														idk_kandidat_jezici
													WHERE
														kj_naziv = :kj_naziv
													AND
														kj_kandidatid = :kandidat_id
													");
						$query_check->execute(array(
							":kandidat_id" => $kandidat_id_c,
							":kj_naziv" => $kj_naziv_njemacki
						));
						$count_jezik = $query_check->rowCount();
						$result_query_check = $query_check->fetch();
						if($count_jezik > 0){
							$kj_id = $result_query_check['kj_id'];
							//UPDATE
							$query_njem = $db->prepare("
								UPDATE idk_kandidat_jezici
								SET kj_naziv = :kj_naziv, kj_citanje = :kj_citanje, kj_slusanje = :kj_slusanje, kj_citanje = :kj_citanje, kj_govorna_interakcija = :kj_govorna_interakcija, kj_govorna_produkcija = :kj_govorna_produkcija, kj_pisanje = :kj_pisanje
								WHERE kj_id = :kj_id
								");
							$query_njem->execute(array(
										':kj_naziv' => $kj_naziv_njemacki,
										':kj_slusanje' => $kj_znanje_njemacki,
										':kj_citanje' => $kj_znanje_njemacki,
										':kj_govorna_interakcija' => $kj_znanje_njemacki,
										':kj_govorna_produkcija' => $kj_znanje_njemacki,
										':kj_pisanje' => $kj_znanje_njemacki,
										':kj_id' => $kj_id));		
						} else {
							//INSERT
							// Add language knowlege
							$query_njem = $db->prepare("
											INSERT INTO idk_kandidat_jezici
												(kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje, kj_kandidatid)
											VALUES
												(:kj_naziv, :kj_slusanje, :kj_citanje, :kj_govorna_interakcija, :kj_govorna_produkcija, :kj_pisanje, :kj_kandidatid)");
					
							$query_njem->execute(array(
										':kj_naziv' => $kj_naziv_njemacki,
										':kj_slusanje' => $kj_znanje_njemacki,
										':kj_citanje' => $kj_znanje_njemacki,
										':kj_govorna_interakcija' => $kj_znanje_njemacki,
										':kj_govorna_produkcija' => $kj_znanje_njemacki,
										':kj_pisanje' => $kj_znanje_njemacki,
										':kj_kandidatid' => $kandidat_id_c));
						}
					}
				//INSERT/UPDATE JEZIKA

				//VEZANJE ZA PROJEKAT
					$rezervisanFlag = 0;
					if($lg_nalogid != 0){
						$nalog_query = $db->prepare("
										SELECT project_id
										FROM idk_projects
										WHERE project_nalogid = :project_nalogid AND (project_name LIKE '%Prijave%' OR project_name LIKE '%Prijava%') ");

						$nalog_query->execute(array(
								':project_nalogid' => $lg_nalogid));

						$nalogrow = $nalog_query->fetch();
						if(!$nalogrow){
						}else{

							$project_id = $nalogrow['project_id'];	
							$check_project = $db->prepare("
												SELECT pk_projectid
												FROM idk_project_kandidati
												WHERE pk_projectid = :pk_projectid AND pk_kandidatid = :pk_kandidatid");

							$check_project->execute(array(
											':pk_projectid' => $project_id,
											':pk_kandidatid' => $kandidat_id_c
							));
							if($check_project->rowCount() == 0){
								
								//Provjera rezervisanosti START				
									$queryPrijavaNalog = $db->prepare("
										SELECT 
											kandidat_nalog_id, kandidat_status_prijave
										FROM 
											idk_kandidati
										WHERE 
											kandidat_id = :kandidat_id
									");
									$queryPrijavaNalog->execute(array(
										':kandidat_id' => $kandidat_id_c
									));
									$rowPrijavaNalog = $queryPrijavaNalog->fetch();
									$nalogIdPN = intval($rowPrijavaNalog["kandidat_nalog_id"]);
									$statusPrijavePN = intval($rowPrijavaNalog["kandidat_status_prijave"]);
									
									$queryProjekti = $db->prepare("
										SELECT 
											count(pk.pk_id) AS brojac
										FROM 
											idk_project_kandidati pk
										JOIN 
											idk_projects p
										ON 
											pk.pk_projectid = p.project_id
										WHERE 
											pk.pk_kandidatid = :pk_kandidatid
											AND 
											(
												project_name LIKE '%Intervju%' 
												OR 
												project_name LIKE '%Obrađeno%' 
												OR 
												project_name LIKE '%Završeni kandidati%' 
												OR 
												project_name LIKE '%BOT - ispunjava uslove%'
												OR 
												project_name LIKE '%Baza - odgovara za nalog%' 
												OR 
												project_name LIKE '%Casting%' 
												OR 
												project_name LIKE '%Ugovor%'
											)
											AND p.project_nalogid != 44
									");
									$queryProjekti->execute(array(
										':pk_kandidatid' => $kandidat_id_c
									));
									$rowProjekti = $queryProjekti->fetch();
									$brojacUProjektuP = intval($rowProjekti["brojac"]);
									
									if($nalogIdPN != 0 OR $brojacUProjektuP != 0 OR $statusPrijavePN == 4){
										$rezervisanFlag = 1;
									}

									if($rezervisanFlag == 0){
										$query_project = $db->prepare("
														INSERT INTO idk_project_kandidati
															(pk_projectid, pk_kandidatid)
														VALUES
															(:pk_projectid, :pk_kandidatid)");
							
										$query_project->execute(array(
														':pk_projectid' => $project_id,
														':pk_kandidatid' => $kandidat_id_c));
										addToLogsStatusPrijave(NULL, $project_id, 2, $kandidat_id_c, 3);
									}else{
										pushToProjectCandidateQueue($kandidat_id_c, $project_id, $partner_id);
									}

								//Provjera rezervisanosti END
							}
						}
					}
				//VEZANJE ZA PROJEKAT

				//INSERT SKOLE
					if($skola_naziv != "nema" AND $skola_naziv != null){
						$insert_skole = $db->prepare("
										INSERT INTO idk_kandidat_edukacija
											(ke_naziv_kvalifikacije, ke_naziv, ke_vrsta_obrazovanja, ke_kandidat_id, ke_skola_id, ke_smjer_id)
										VALUES
											(:ke_naziv_kvalifikacije, :ke_naziv, :ke_vrsta_obrazovanja, :ke_kandidat_id, :ke_skola_id, :ke_smjer_id)");
				
						$insert_skole->execute(array(
									':ke_naziv_kvalifikacije' => $smjer_naziv,
									':ke_naziv' => $skola_naziv,
									':ke_vrsta_obrazovanja' => "srednje",
									':ke_skola_id' => $skola_id_post,
									':ke_smjer_id' => $smjer_id_post,
									':ke_kandidat_id' => $kandidat_id_c));
					}
				//INSERT SKOLE

				$log_desc = "Kandidat se ponovo prijavio: " . $kandidat_ime . " " . $kandidat_prezime . "(".$kandidat_id_c.") putem viber chat bota - ručni unos. ";
				$log_type = "5";
				addToLogs($log_desc, $log_type);
				
			}

		break;

}
?>
