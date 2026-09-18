<?php
include("includes/functions.php");
include("includes/common.php");
include("pdf_generator.php");
include("html_pdf_generator.php");
$form="";

if(isset($_REQUEST["form"])) {
	$form = $_REQUEST["form"];
	if($logged_employee_id != 0){
		switch ($form)
		{
			case "add_new_razlog":
				$razlog_new = $_POST['razlog_new'];
				if(intval($_POST['zvati_opet_new']) == 1){
					$zvati_opet_new = 1;
					$br_dana_new = $_POST['br_dana_new'];
				}else{
					$zvati_opet_new = 0;
					$br_dana_new = NULL;
				}
				$insert = $db->prepare("
					INSERT INTO idk_ro_usluge
						(naziv_ro_bs, tip_ro, status_ro, ponovno_zvanje_ro, br_dana_ro, odobrio_ro, dodao_ro)
					VALUES
						(:naziv_ro_bs, :tip_ro, :status_ro, :ponovno_zvanje_ro, :br_dana_ro, :odobrio_ro, :dodao_ro)
				");

				$insert->execute(array(
					':naziv_ro_bs' => $razlog_new,
					':tip_ro' => 1,
					':status_ro' => 1,
					':ponovno_zvanje_ro' => $zvati_opet_new,
					':br_dana_ro' =>$br_dana_new,
					':odobrio_ro' =>$logged_employee_id,
					':dodao_ro' =>$logged_employee_id
				));
				$razlog_id = $db->lastInsertId();
				$log_desc = "DIPL -> Dodan novi razlog odbijanja usluge sa ID = [".$razlog_id."].";
				$log_date = date('Y-m-d H:i:s');

				$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)
				");

				$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date
				));
				header("Location: " . getSiteURLr() . "dipl?page=pregledDIPL&type=1");
			break;
			
			case "addInkasoRazlog":
				$razlogInkaso = $_POST['razlogInkaso'];
				if(intval($_POST['zvatiInkaso']) == 1){
					$zvatiInkaso = 1;
					$danaInkaso = intval($_POST['danaInkaso']);
				}else{
					$zvatiInkaso = 0;
					$danaInkaso = NULL;
				}
				$insert = $db->prepare("
					INSERT INTO idk_ro_usluge
						(naziv_ro_bs, tip_ro, status_ro, ponovno_zvanje_ro, br_dana_ro, odobrio_ro, dodao_ro)
					VALUES
						(:naziv_ro_bs, :tip_ro, :status_ro, :ponovno_zvanje_ro, :br_dana_ro, :odobrio_ro, :dodao_ro)
				");

				$insert->execute(array(
					':naziv_ro_bs' => $razlogInkaso,
					':tip_ro' => 4,
					':status_ro' => 1,
					':ponovno_zvanje_ro' => $zvatiInkaso,
					':br_dana_ro' =>$danaInkaso,
					':odobrio_ro' =>$logged_employee_id,
					':dodao_ro' =>$logged_employee_id
				));
				$razlogId = $db->lastInsertId();
				$log_desc = "DIPL -> Dodan novi razlog odbijanja usluge sa ID = [".$razlogId."].";
				$log_date = date('Y-m-d H:i:s');

				$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)
				");

				$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date
				));
				header("Location: " . getSiteURLr() . "dipl?page=pregledDIPL&type=1");
			break;
			
			case "add_new_razlog_ugovori":
				$naziv_RU = $_POST['naziv_RU'];
				$nazivSR_RU = $_POST['nazivSR_RU'];
				$nazivDE_RU = $_POST['nazivDE_RU'];
				
				$insert = $db->prepare("
					INSERT INTO idk_ro_usluge
						(naziv_ro_bs, naziv_ro_sr, naziv_ro_de, tip_ro, status_ro, dodao_ro)
					VALUES
						(:naziv_ro_bs, :naziv_ro_sr, :naziv_ro_de, :tip_ro, :status_ro, :dodao_ro)
				");

				$insert->execute(array(
					':naziv_ro_bs' => $naziv_RU,
					':naziv_ro_sr' => $nazivSR_RU,
					':naziv_ro_de' => $nazivDE_RU,
					':tip_ro' => 3,
					':status_ro' => 1,
					':dodao_ro' =>$logged_employee_id
				));
				$razlog_id = $db->lastInsertId();
				$log_desc = "DIPL -> Dodan novi razlog odbijanja ugovora sa ID = [".$razlog_id."].";
				$log_date = date('Y-m-d H:i:s');

				$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)
				");

				$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date
				));
				header("Location: " . getSiteURLr() . "dipl?page=pregledDIPL&type=1");
			break;
			
			case "add_new_ND_cand":
			
				$tip_forme = intval($_POST['tip_forme']);
				$ime_new_ND_cand1 = $_POST['ime_new_ND_cand'];
				$prezime_new_ND_cand1 = $_POST['prezime_new_ND_cand'];
				if($tip_forme == 0){
					$ulica_new_ND_cand1 = $_POST['ulica_new_ND_cand'];
					$postanski_broj_new_ND_cand1 = $_POST['postanski_broj_new_ND_cand'];
					$grad_new_ND_cand1 = $_POST['grad_new_ND_cand'];
				}else{
					$ulica_new_ND_cand1 = NULL;
					$postanski_broj_new_ND_cand1 = NULL;
					$grad_new_ND_cand1 = NULL;
				}
				$mobilni_new_ND_cand1 = $_POST['mobilni_new_ND_cand'];
				$email_new_ND_cand1 = $_POST['email_new_ND_cand'];
				if($tip_forme == 0){
					if(intval($_POST['srbija_new_ND_cand']) == 1){
						//ulica_new_ND_cand2 spada pod ulicu boravista - analogno tome sa cand2 vezano je za boraviste
						$ulica_new_ND_cand2 = $_POST['ulica_new_ND_cand1'];
						$postanski_broj_new_ND_cand2 = $_POST['postanski_broj_new_ND_cand1'];
						$grad_new_ND_cand2 = $_POST['grad_new_ND_cand1'];
						$nadlezni_org_new_ND_cand1 = $_POST['nadlezni_organ_new_ND_cand'];
					}else{
						$ulica_new_ND_cand2 = NULL;
						$postanski_broj_new_ND_cand2 = NULL;
						$grad_new_ND_cand2 = NULL;
						$nadlezni_org_new_ND_cand1 = NULL;
					}
					if (!empty($_POST['jmbg_new_ND_cand'])){
						$jmbg_new_ND_cand1 = $_POST['jmbg_new_ND_cand'];
					}else{
						$jmbg_new_ND_cand1 = NULL;
					}
					if (!empty($_POST['broj_licne_karte_new_ND_cand'])){
						$br_licne_karte_new_ND_cand1 = $_POST['broj_licne_karte_new_ND_cand'];
					}else{
						$br_licne_karte_new_ND_cand1 = NULL;
					}
					$vrsta_obrade_new_ND_cand1 = intval($_POST['vrsta_obrade_new_ND_cand']);
					$skola_new_ND_cand1 = $_POST['skola_new_ND_cand'];
					$skola_smjer_new_ND_cand1 = $_POST['skola_smjer_new_ND_cand'];
					$nivo_jezika_new_ND_cand1 = intval($_POST['nivo_jezika_new_ND_cand']);
				}else{
					$ulica_new_ND_cand2 = NULL;
					$postanski_broj_new_ND_cand2 = NULL;
					$grad_new_ND_cand2 = NULL;
					$nadlezni_org_new_ND_cand1 = NULL;
					$jmbg_new_ND_cand1 = NULL;
					$br_licne_karte_new_ND_cand1 = NULL;
					$vrsta_obrade_new_ND_cand1 = 0;
					$skola_new_ND_cand1 = NULL;
					$skola_smjer_new_ND_cand1 = NULL;
					$nivo_jezika_new_ND_cand1 = 0;
				}
				if($tip_forme == 0){
					$poslodavac_new_ND_cand1 = $_POST['poslodavac_new_ND_cand'];
					if($poslodavac_new_ND_cand1 == 0){
						$poslodavac_nas_new_ND_cand1 = 0;
						$poslodavac_naziv_nas_new_ND_cand1 = NULL;
						$poslodavac_naziv_new_ND_cand1 = NULL;
						$poslodavac_ulica_new_ND_cand1 = NULL;
						$poslodavac_postanski_broj_new_ND_cand1 = NULL;
						$poslodavac_grad_new_ND_cand1 = NULL;
						$poslodavac_regija_new_ND_cand1 = NULL;
						$poslodavac_drzava_new_ND_cand1 = NULL;
						$poslodavac_ime_new_ND_cand1 = NULL;
						$poslodavac_prezime_new_ND_cand1 = NULL;
						$poslodavac_mail_new_ND_cand1 = NULL;
						$poslodavac_kontakt_broj_new_ND_cand1 = NULL;
					}
					else{
						$poslodavac_nas_new_ND_cand1 = $_POST['poslodavac_nas_new_ND_cand'];
						if($poslodavac_nas_new_ND_cand1 == 0){
							$poslodavac_naziv_nas_new_ND_cand1 = NULL;
							$poslodavac_naziv_new_ND_cand1 = $_POST['poslodavac_naziv_new_ND_cand'];
							$poslodavac_ulica_new_ND_cand1 = $_POST['poslodavac_ulica_new_ND_cand'];
							$poslodavac_postanski_broj_new_ND_cand1 = $_POST['poslodavac_postanski_broj_new_ND_cand'];
							$poslodavac_grad_new_ND_cand1 = $_POST['poslodavac_grad_new_ND_cand'];
							$poslodavac_regija_new_ND_cand1 = $_POST['poslodavac_regija_new_ND_cand'];
							$poslodavac_drzava_new_ND_cand1 = $_POST['poslodavac_drzava_new_ND_cand'];
							$poslodavac_kont_new_ND_cand1 = $_POST['poslodavac_kont_da_ne_ND_cand'];
							if($poslodavac_kont_new_ND_cand1 == 1){
								$poslodavac_ime_new_ND_cand1 = $_POST['poslodavac_ime_new_ND_cand'];
								$poslodavac_prezime_new_ND_cand1 = $_POST['poslodavac_prezime_new_ND_cand'];
								$poslodavac_mail_new_ND_cand1 = $_POST['poslodavac_mail_new_ND_cand'];
								$poslodavac_kontakt_broj_new_ND_cand1 = $_POST['poslodavac_kontakt_new_ND_cand'];
							}
							else{
								$poslodavac_ime_new_ND_cand1 = NULL;
								$poslodavac_prezime_new_ND_cand1 = NULL;
								$poslodavac_mail_new_ND_cand1 = NULL;
								$poslodavac_kontakt_broj_new_ND_cand1 = NULL;
							}
						}
						else{
							$poslodavac_naziv_nas_new_ND_cand1 = $_POST['poslodavac_naziv_nas_new_ND_cand'];
							$poslodavac_naziv_new_ND_cand1 = NULL;
							$poslodavac_ulica_new_ND_cand1 = NULL;
							$poslodavac_postanski_broj_new_ND_cand1 = NULL;
							$poslodavac_grad_new_ND_cand1 = NULL;
							$poslodavac_regija_new_ND_cand1 = NULL;
							$poslodavac_drzava_new_ND_cand1 = NULL;
							$poslodavac_ime_new_ND_cand1 = NULL;
							$poslodavac_prezime_new_ND_cand1 = NULL;
							$poslodavac_mail_new_ND_cand1 = NULL;
							$poslodavac_kontakt_broj_new_ND_cand1 = NULL;
						}
					}
				}else{
					$poslodavac_new_ND_cand1 = NULL;
					$poslodavac_nas_new_ND_cand1 = NULL;
					$poslodavac_naziv_nas_new_ND_cand1 = NULL;
					$poslodavac_naziv_new_ND_cand1 = NULL;
					$poslodavac_ulica_new_ND_cand1 = NULL;
					$poslodavac_postanski_broj_new_ND_cand1 = NULL;
					$poslodavac_grad_new_ND_cand1 = NULL;
					$poslodavac_regija_new_ND_cand1 = NULL;
					$poslodavac_drzava_new_ND_cand1 = NULL;
					$poslodavac_ime_new_ND_cand1 = NULL;
					$poslodavac_prezime_new_ND_cand1 = NULL;
					$poslodavac_mail_new_ND_cand1 = NULL;
					$poslodavac_kontakt_broj_new_ND_cand1 = NULL;
				}
				$koment_new_ND_cand1 = $_POST['koment_new_ND_cand'];
				
				//Provjera da li se nalazi u DIPL VEC
				$provjera_dipl = $db->prepare("
										SELECT id_broj_nd_kandidata, zaduzen_zaposlenik_nd_kandidata
										FROM idk_nd_kandidata
										WHERE mobilni_nd_kandidata = :mobilni_nd_kandidata AND mobilni_nd_kandidata != ''
										");
				$provjera_dipl->execute(array(
										":mobilni_nd_kandidata" => $mobilni_new_ND_cand1
									));
				$dipl_da_ne = $provjera_dipl->rowCount();
				$provjera_dipl_row = $provjera_dipl->fetch();
				$kandidat_id_vec_u_dipl = $provjera_dipl_row['id_broj_nd_kandidata'];
				$zaduzen_id_vec_u_dipl = $provjera_dipl_row['zaduzen_zaposlenik_nd_kandidata'];
				
				if($dipl_da_ne == 0){
					
					$menadzer_new_ND_cand1 = $logged_employee_id;
					$team_menadzer_new_ND_cand1 = getTeamIdByEmployee($menadzer_new_ND_cand1);
					$new_ND_kandidat = $db->prepare("
						INSERT INTO idk_nd_kandidata
							(ime_nd_kandidata, prezime_nd_kandidata, ulica_nd_kandidata, postanski_broj_nd_kandidata, grad_nd_kandidata, ulica_bor_nd_kandidata, postanski_broj_bor_nd_kandidata, grad_bor_nd_kandidata, mobilni_nd_kandidata, email_nd_kandidata, izdao_licnu_nd_kandidata, jmbg_nd_kandidata, broj_licne_karte_nd_kandidata, skola_nd_kandidata, skola_smjer_nd_kandidata, poslodavac_nd_kandidata, poslodavac_nas_nd_kandidata, poslodavac_naziv_nas_nd_kandidata, poslodavac_naziv_nd_kandidata, poslodavac_ulica_nd_kandidata, poslodavac_postanski_broj_nd_kandidata, poslodavac_grad_nd_kandidata, poslodavac_regija_nd_kandidata, poslodavac_drzava_nd_kandidata, poslodavac_ime_nd_kandidata, poslodavac_prezime_nd_kandidata, poslodavac_mail_nd_kandidata, poslodavac_mobilni_nd_kandidata, komentar_nd_kandidata, vrijeme_kreiranja_nd_kandidata, dodao_zaposlenik_nd_kandidata, zaduzen_zaposlenik_nd_kandidata, vrsta_obrade, nivo_poznavanja_jezika, tim_nd_kandidata, status_nd_kandidata)
						VALUES
							(:ime_nd_kandidata, :prezime_nd_kandidata, :ulica_nd_kandidata, :postanski_broj_nd_kandidata, :grad_nd_kandidata, :ulica_bor_nd_kandidata, :postanski_broj_bor_nd_kandidata, :grad_bor_nd_kandidata, :mobilni_nd_kandidata, :email_nd_kandidata, :izdao_licnu_nd_kandidata, :jmbg_nd_kandidata, :broj_licne_karte_nd_kandidata, :skola_nd_kandidata, :skola_smjer_nd_kandidata, :poslodavac_nd_kandidata, :poslodavac_nas_nd_kandidata, :poslodavac_naziv_nas_nd_kandidata, :poslodavac_naziv_nd_kandidata, :poslodavac_ulica_nd_kandidata, :poslodavac_postanski_broj_nd_kandidata, :poslodavac_grad_nd_kandidata, :poslodavac_regija_nd_kandidata, :poslodavac_drzava_nd_kandidata, :poslodavac_ime_nd_kandidata, :poslodavac_prezime_nd_kandidata, :poslodavac_mail_nd_kandidata, :poslodavac_mobilni_nd_kandidata, :komentar_nd_kandidata, :vrijeme_kreiranja_nd_kandidata, :dodao_zaposlenik_nd_kandidata, :zaduzen_zaposlenik_nd_kandidata, :vrsta_obrade, :nivo_poznavanja_jezika, :tim_nd_kandidata, :status_nd_kandidata)
					");

					$new_ND_kandidat->execute(array(
						':ime_nd_kandidata' => $ime_new_ND_cand1,
						':prezime_nd_kandidata' => $prezime_new_ND_cand1,
						':ulica_nd_kandidata' => $ulica_new_ND_cand1,
						':postanski_broj_nd_kandidata' => $postanski_broj_new_ND_cand1,
						':grad_nd_kandidata' => $grad_new_ND_cand1,
						':ulica_bor_nd_kandidata' => $ulica_new_ND_cand2,
						':postanski_broj_bor_nd_kandidata' => $postanski_broj_new_ND_cand2,
						':grad_bor_nd_kandidata' => $grad_new_ND_cand2,
						':mobilni_nd_kandidata' => $mobilni_new_ND_cand1,
						':email_nd_kandidata' => $email_new_ND_cand1,
						':izdao_licnu_nd_kandidata' => $nadlezni_org_new_ND_cand1,
						':jmbg_nd_kandidata' => $jmbg_new_ND_cand1,
						':broj_licne_karte_nd_kandidata' => $br_licne_karte_new_ND_cand1,
						':skola_nd_kandidata' => $skola_new_ND_cand1,
						':skola_smjer_nd_kandidata' => $skola_smjer_new_ND_cand1,
						':poslodavac_nd_kandidata' => $poslodavac_new_ND_cand1,
						':poslodavac_nas_nd_kandidata' => $poslodavac_nas_new_ND_cand1,
						':poslodavac_naziv_nas_nd_kandidata' => $poslodavac_naziv_nas_new_ND_cand1,
						':poslodavac_naziv_nd_kandidata' => $poslodavac_naziv_new_ND_cand1,
						':poslodavac_ulica_nd_kandidata' => $poslodavac_ulica_new_ND_cand1,
						':poslodavac_postanski_broj_nd_kandidata' => $poslodavac_postanski_broj_new_ND_cand1,
						':poslodavac_grad_nd_kandidata' => $poslodavac_grad_new_ND_cand1,
						':poslodavac_regija_nd_kandidata' => $poslodavac_regija_new_ND_cand1,
						':poslodavac_drzava_nd_kandidata' => $poslodavac_drzava_new_ND_cand1,
						':poslodavac_ime_nd_kandidata' => $poslodavac_ime_new_ND_cand1,
						':poslodavac_prezime_nd_kandidata' => $poslodavac_prezime_new_ND_cand1,
						':poslodavac_mail_nd_kandidata' => $poslodavac_mail_new_ND_cand1,
						':poslodavac_mobilni_nd_kandidata' => $poslodavac_kontakt_broj_new_ND_cand1,
						':komentar_nd_kandidata' => $koment_new_ND_cand1,
						':vrijeme_kreiranja_nd_kandidata' => date('Y-m-d H:i:s'),
						':dodao_zaposlenik_nd_kandidata' => $logged_employee_id,
						':zaduzen_zaposlenik_nd_kandidata' => $menadzer_new_ND_cand1,
						':vrsta_obrade' => $vrsta_obrade_new_ND_cand1,
						':nivo_poznavanja_jezika' => $nivo_jezika_new_ND_cand1,
						':tim_nd_kandidata' => $team_menadzer_new_ND_cand1,
						':status_nd_kandidata' => 1
					));
					//Get last ID
					$kandidat_id_nd = $db->lastInsertId();
					
					//Update kompanije i prodaje START
					if($poslodavac_new_ND_cand1 == 1 AND $poslodavac_nas_new_ND_cand1 == 0 AND $tip_forme == 0){
						$tip_poslodavca_new_ND_cand1 = "Lead";
						//Prebacivanje u kompanije START
							
							$query_company = $db->prepare("
											INSERT INTO idk_companies
												(company_name, company_address, company_zipcode, company_city, company_state, company_country, company_contact_type, company_datetime, company_status, company_origin, company_reccomendation)
											VALUES
												(:company_name, :company_address, :company_zipcode, :company_city, :company_state, :company_country, :company_contact_type, :company_datetime, :company_status, :company_origin, :company_reccomendation)");

							$query_company->execute(array(
										':company_name' => $poslodavac_naziv_new_ND_cand1,
										':company_address' => $poslodavac_ulica_new_ND_cand1,
										':company_zipcode' => $poslodavac_postanski_broj_new_ND_cand1,
										':company_city' => $poslodavac_grad_new_ND_cand1,
										':company_state' => $poslodavac_regija_new_ND_cand1,
										':company_country' => $poslodavac_drzava_new_ND_cand1,
										':company_contact_type' => $tip_poslodavca_new_ND_cand1,
										':company_datetime' => date('Y-m-d H:i:s'),
										':company_status' => 1,
										':company_origin' => 1,
										':company_reccomendation' => $kandidat_id_nd
										));
										
							$companyid = $db->lastInsertId();
							
							//Ubacivanje u prodajni modul START
							$query_clients = $db->prepare("
														INSERT INTO idk_clients
															(
																client_name,
																client_country,
																client_region,
																client_city,
																client_address,
																client_pp,
																client_telephone,
																client_email,
																client_origin,
																client_recommendation,
																client_recommendation_company,
																client_fc_or_sales,
																client_fc_status,
																client_sales_status
															)
															VALUES
															(
																:client_name,
																:client_country,
																:client_region,
																:client_city,
																:client_address,
																:client_pp,
																:client_telephone,
																:client_email,
																:client_origin,
																:client_recommendation,
																:client_recommendation_company,
																:client_fc_or_sales,
																:client_fc_status,
																:client_sales_status
															)
															");
								$query_clients->execute(array(
												':client_name' => $poslodavac_naziv_new_ND_cand1,
												':client_country' => "DE",
												':client_region' => $poslodavac_regija_new_ND_cand1,
												':client_city' => $poslodavac_grad_new_ND_cand1,
												':client_address' => $poslodavac_ulica_new_ND_cand1,
												':client_pp' => $poslodavac_postanski_broj_new_ND_cand1,
												':client_telephone' => NULL,
												':client_email' => NULL,
												':client_origin' => 1,
												':client_recommendation' => $kandidat_id_nd,
												':client_recommendation_company' => $companyid,
												':client_fc_or_sales' => 0,
												':client_fc_status' => 0,
												':client_sales_status' => 0
												));
												
								$clientid = $db->lastInsertId();
								//$stats_desc1 = 'Zaposlenik '.getZaposlenikimeR($logged_employee_id).' je dodao klijenta '.$poslodavac_naziv_new_ND_cand1.'. ';
								$stats_desc2 = 'Zaposlenik '.getZaposlenikimeR($logged_employee_id).' je dodao klijenta preko DIPL modula '.$poslodavac_naziv_new_ND_cand1.'. ';
								//insertClientStats($clientid, 0, 1, $stats_desc1);
								insertClientStats($clientid, 0, 0, $stats_desc2);
							//Ubacivanje u prodajni modul END
							if($poslodavac_kont_new_ND_cand1 == 1){
								$query_contact = $db->prepare("
												INSERT INTO idk_contacts
													(contact_firstname, contact_lastname, contact_companyid, contact_clientid, contact_datetime, contact_status)
												VALUES
													(:contact_firstname, :contact_lastname, :contact_companyid, :contact_clientid, :contact_datetime, :contact_status)");

								$query_contact->execute(array(
											':contact_firstname' => $poslodavac_ime_new_ND_cand1,
											':contact_lastname' => $poslodavac_prezime_new_ND_cand1,
											':contact_companyid' => $companyid,
											':contact_clientid' => $clientid,
											':contact_datetime' => date('Y-m-d H:i:s'),
											':contact_status' => 1
											));
								
								$contactid = $db->lastInsertId();
								
								 //Add primary phone
								if (!empty($poslodavac_kontakt_broj_new_ND_cand1)) {

									$ci_group_t = 1;
									$ci_title_t = "Telefon";
									$ci_data_t = $poslodavac_kontakt_broj_new_ND_cand1;
									$ci_primary_t = 1;

									$query_phone = $db->prepare("
													INSERT INTO idk_contacts_info
														(ci_group, ci_title, ci_data, ci_primary, ci_contactid)
													VALUES
														(:ci_group, :ci_title, :ci_data, :ci_primary, :ci_contactid)");

									$query_phone->execute(array(
													':ci_group' => $ci_group_t,
													':ci_title' => $ci_title_t,
													':ci_data' => $ci_data_t,
													':ci_primary' => $ci_primary_t,
													':ci_contactid' => $contactid
													));
									$contact_telefon_id = $db->lastInsertId();
								}

								//Add primary email
								if (!empty($poslodavac_mail_new_ND_cand1)) {

									$ci_group = 2;
									$ci_title = "E-mail";
									$ci_data = $poslodavac_mail_new_ND_cand1;
									$ci_primary = 1;

									$query_email = $db->prepare("
													INSERT INTO idk_contacts_info
														(ci_group, ci_title, ci_data, ci_primary, ci_contactid)
													VALUES
														(:ci_group, :ci_title, :ci_data, :ci_primary, :ci_contactid)");

									$query_email->execute(array(
													':ci_group' => $ci_group,
													':ci_title' => $ci_title,
													':ci_data' => $ci_data,
													':ci_primary' => $ci_primary,
													':ci_contactid' => $contactid
													));
									$contact_email_id = $db->lastInsertId();
								}
								$kontakt_broj_email_company = " [".$contact_telefon_id.",".$contact_email_id."] ";
								$desc_contact = " Kontakt ID = [".$contactid."] i kontakt podacima ID = ".$kontakt_broj_email_company." ";
							}
							else{
								$desc_contact = " Kontakt nije dodan.";
							}
							//ADD TO LOGS START
							$log_date_com = date('Y-m-d H:i:s');
							$log_desc_com = "DIPL -> Dodana nova kompanija sa ID = [".$companyid."] i novi klijent sa ID = [".$clientid."]. ".$desc_contact." ";
							$log_query_com = $db->prepare("
											INSERT INTO idk_logs 
												(log_employeeid, log_desc, log_date)
											VALUES
												(:log_employeeid, :log_desc, :log_date)");

							$log_query_com->execute(array(
											':log_employeeid' => $logged_employee_id,
											':log_desc' => $log_desc_com,
											':log_date' => $log_date_com));
							//ADD TO LOGS END
						//Prebacivanje u kompanije END
					}
					//Update kompanije i prodaje END
					
					//Update statistike menadzera START
					$insert_statistike_menagera = $db->prepare("
								INSERT INTO idk_nd_menadzeri_statistike
									(idd_broj_nd_kandidata, zaduzen_zaposlenik_id, vrsta_aktivnosti, vrijeme_aktivnosti)
								VALUES
									(:idd_broj_nd_kandidata, :zaduzen_zaposlenik_id, :vrsta_aktivnosti, :vrijeme_aktivnosti)");

					$insert_statistike_menagera->execute(array(
								':idd_broj_nd_kandidata' => $kandidat_id_nd,
								':zaduzen_zaposlenik_id' => $menadzer_new_ND_cand1,
								':vrsta_aktivnosti' => 0,
								':vrijeme_aktivnosti' => date('Y-m-d H:i:s')
								));
					$new_ND_status = $db->prepare("
										INSERT INTO idk_nd_kandidata_status_log
										(
											idd_broj_nd_kandidata,
											status_nd_kandidata,
											vrijeme_promjene_statusa_nd_kandidata,
											promjenio_zaposlenik_nd_kandidata
										)
										VALUES
										(
											:idd_broj_nd_kandidata,
											:status_nd_kandidata,
											:vrijeme_promjene_statusa_nd_kandidata,
											:promjenio_zaposlenik_nd_kandidata
										)
					");
					$new_ND_status->execute(array(
									':idd_broj_nd_kandidata' => $kandidat_id_nd,
									':status_nd_kandidata' => 1,
									':vrijeme_promjene_statusa_nd_kandidata' => date('Y-m-d H:i:s'),
									':promjenio_zaposlenik_nd_kandidata' => $logged_employee_id
									));
					
					//DODAVANJE U TABELU IDK_KANDIDATI
					//Provjera da li se nalazi u kandidatima
					$provjera_kandidati = $db->prepare("
									SELECT kandidat_id
									FROM idk_kandidati
									WHERE kandidat_mobitel = :kandidat_mobitel AND kandidat_status != :kandidat_status
									");
					$provjera_kandidati->execute(array(
											":kandidat_mobitel" => $mobilni_new_ND_cand1,
											":kandidat_status" => 3
										));
					$kandidati_da_ne = $provjera_kandidati->rowCount();
					$provjera_kandidati_row = $provjera_kandidati->fetch();
					$kandidat_id_vec_u_kan = $provjera_kandidati_row['kandidat_id'];
					if($kandidati_da_ne == 0){
						$kandidat_check = md5(uniqid(rand(), true));
						
						//Add user to db
						$query_add_user = $db->prepare("
										INSERT INTO idk_kandidati
											(kandidat_check, kandidat_ime, kandidat_prezime, kandidat_email, kandidat_mobitel, kandidat_slika, kandidat_status, kandidat_status_messenger, kandidat_status_prijave, kandidat_datetime, kandidat_visitedurl, kandidat_prijava_na, kandidat_group, povezan_na_dipl, kandidat_porijeklo, kandidat_dipl_id )
										VALUES
											(:kandidat_check, :kandidat_ime, :kandidat_prezime, :kandidat_email, :kandidat_mobitel, :kandidat_slika, :kandidat_status, :kandidat_status_messenger, :kandidat_status_prijave, :kandidat_datetime, :kandidat_visitedurl, :kandidat_prijava_na, :kandidat_group, :povezan_na_dipl, :kandidat_porijeklo, :kandidat_dipl_id)");
				
						$query_add_user->execute(array(
									':kandidat_check' => $kandidat_check,
									':kandidat_ime' => $ime_new_ND_cand1,
									':kandidat_prezime' => $prezime_new_ND_cand1,
									':kandidat_email' => $email_new_ND_cand1,
									':kandidat_mobitel' => $mobilni_new_ND_cand1,
									':kandidat_slika' => "none",
									':kandidat_status' => 0,
									':kandidat_status_messenger' => 1,
									':kandidat_status_prijave' => 1,
									':kandidat_datetime' => date('Y-m-d H:i:s'),
									':kandidat_visitedurl' => 1,
									':kandidat_prijava_na' => "Ostalo",
									':kandidat_group' => 7,
									':povezan_na_dipl' => 1,
									':kandidat_porijeklo' => 1,
									':kandidat_dipl_id' => $kandidat_id_nd
									));
									
						$kandidat_id = $db->lastInsertId();
						//UPDATE ND_KANDIDATA STAVITI OVAJ ID 
						
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
								':lks_datetime' => date('Y-m-d H:i:s')
						));
						
						$nalog_query = $db->prepare("
												SELECT project_id
												FROM idk_projects
												WHERE (project_name LIKE '%Kandidati sa DIPL%') ");
					
						$nalog_query->execute();
					
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
						
						addToLogsStatusPrijave(NULL, $project_id, 1, $kandidat_id, 3);
										
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
									':kki_podatak' => $mobilni_new_ND_cand1,
									':kki_kandidat_id' => $kandidat_id));
						
						$kki_grupa_e = 2;
						$kki_naziv_e = "E-mail";
						
						//Add kontakt info to db
						$query_email = $db->prepare("
										INSERT INTO idk_kandidat_kontakt_info
											(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
										VALUES
											(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)");

						$query_email->execute(array(
									':kki_grupa' => $kki_grupa_e,
									':kki_naziv' => $kki_naziv_e,
									':kki_podatak' => $email_new_ND_cand1,
									':kki_kandidat_id' => $kandidat_id));
						
						//Add to table users (chatbot)
						$random_string = generateRandomString();
						$options = [
							'cost' => 10,
						];
						$random_password = password_hash($random_string, PASSWORD_BCRYPT, $options);
						
						$log_desc2 = "Kandidat: " . $ime_new_ND_cand1 . " " . $prezime_new_ND_cand1 . "(".$kandidat_id."). ; (".$random_string.")";
						$log_type2 = "5";
						addToLogs($log_desc2, $log_type2);
						
						$characters = '0123456789';
						$charactersLength = strlen($characters);
						$randomString = '';
						for ($i = 0; $i < 5; $i++) {
							$randomString .= $characters[rand(0, $charactersLength - 1)];
						}
						$bot_koriscnicko_ime = $ime_new_ND_cand1.$randomString;
						$kandidat_full_name = $ime_new_ND_cand1." ".$prezime_new_ND_cand1;
						$query_user = $db->prepare("
									INSERT INTO users
										(phone, name, nalog_id, email, password, kandidat_id)
									VALUES
										(:phone, :name, :nalog_id, :email, :password, :kandidat_id)");

						$query_user->execute(array(
									':phone' => $mobilni_new_ND_cand1,
									':name' => $kandidat_full_name,
									':nalog_id' => 44,
									':email' => $bot_koriscnicko_ime,
									':password' => $random_password,
									':kandidat_id' => $kandidat_id));
									
						//INFOBIP
						sendSmsToCandidateInfobip1($random_string, $mobilni_new_ND_cand1, "NN");
						sleep(1);  // Seconds
						sendSmsToCandidateInfobip2($random_string, $mobilni_new_ND_cand1, "NN");
						sleep(1);  // Seconds
						sendSmsToCandidateInfobip3($random_string, $mobilni_new_ND_cand1, $bot_koriscnicko_ime);
						sleep(1);  // Seconds
						sendSmsToCandidateInfobip4($random_string, $mobilni_new_ND_cand1, $bot_koriscnicko_ime);
						
						sendCandidateMessengerMail($random_string, $email_new_ND_cand1, $bot_koriscnicko_ime);
						
						//KRAJ DODAVANJA U TABELU KANDIDATI
					}
					else{
						$update_query_pov_dipl = $db->prepare("
								UPDATE idk_kandidati
								SET povezan_na_dipl = :povezan_na_dipl, kandidat_dipl_id = :kandidat_dipl_id
								WHERE kandidat_id = :kandidat_id
						");
						
						$update_query_pov_dipl->execute(array(
									':kandidat_id' => $kandidat_id_vec_u_kan,
									':povezan_na_dipl' => 1,
									':kandidat_dipl_id' => $kandidat_id_nd
									));
					}
					//ADD TO LOGS START
						$log_date = date('Y-m-d H:i:s');
						$log_desc = "DIPL -> Dodan novi kandidat sa ID = [".$kandidat_id_nd."] - Zadužen zaposlenik: ".$menadzer_new_ND_cand1." ";
						$log_query = $db->prepare("
										INSERT INTO idk_logs 
											(log_employeeid, log_desc, log_date)
										VALUES
											(:log_employeeid, :log_desc, :log_date)"); 

						$log_query->execute(array(
										':log_employeeid' => $logged_employee_id,
										':log_desc' => $log_desc,
										':log_date' => $log_date));
					//ADD TO LOGS END
					header("Location: " . getSiteURL() . "nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$kandidat_id_nd."");
				}
				else{
					//Provjera da li se nalazi u kandidatima
					$provjera_kandidati = $db->prepare("
									SELECT kandidat_id
									FROM idk_kandidati
									WHERE kandidat_mobitel = :kandidat_mobitel AND kandidat_status != :kandidat_status
									");
					$provjera_kandidati->execute(array(
											":kandidat_mobitel" => $mobilni_new_ND_cand1,
											":kandidat_status" => 3
										));
					$kandidati_da_ne = $provjera_kandidati->rowCount();
					$provjera_kandidati_row = $provjera_kandidati->fetch();
					$kandidat_id_vec_u_kan = $provjera_kandidati_row['kandidat_id'];
					if($kandidati_da_ne != 0){
						$update_query_pov_dipl = $db->prepare("
								UPDATE idk_kandidati
								SET povezan_na_dipl = :povezan_na_dipl, kandidat_dipl_id = :kandidat_dipl_id
								WHERE kandidat_id = :kandidat_id
						");
						
						$update_query_pov_dipl->execute(array(
									':kandidat_id' => $kandidat_id_vec_u_kan,
									':povezan_na_dipl' => 1,
									':kandidat_dipl_id' => $kandidat_id_vec_u_dipl
									));
					}
					/*
					//Slanje maila starom menadzeru sa linkom kandidata njemu dodjeljenog
					$user_query = $db->prepare("
											SELECT employee_firstname, employee_lastname, employee_email
											FROM idk_employees
											WHERE employee_id = :employee_id");

					$user_query->execute(array(
									':employee_id' => $zaduzen_id_vec_u_dipl));

					$user = $user_query->fetch();

					$employee_firstname = $user['employee_firstname'];
					$employee_lastname = $user['employee_lastname'];
					$employee_email = $user['employee_email'];

					//Send email to user
					$mail_email = $employee_email;
					$mail_name = $employee_firstname . ' ' . $employee_lastname;
					$mail_subject = "Dipl modul - Stari kandidat";
					$mail_url = "" . getSiteUrlr() . "/nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$kandidat_id_vec_u_dipl." ";
					$mail_body = "
									<p>Vašeg starog kandidata u navedenom linku, zaposlenik ".getZaposlenikimeR($logged_employee_id)." je pokušao/la dodati u sistem Ručnom registracijom.</p>
									<p>Detalje pogledajte na linku: " . $mail_url . "</p>
					";
					$mail_altbody = "
									<p>Vašeg starog kandidata u navedenom linku, zaposlenik ".$logged_employee_id." je pokušao/la dodati u sistem Ručnom registracijom.</p>
									<p>Detalje pogledajte na linku: " . $mail_url . "</p>
					";
					
					sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);*/
					header("Location: " . getSiteURL() . "nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$kandidat_id_vec_u_dipl."");
				}
			break;
			
			case "dodaj_inkaso_biljesku":
				//za biljeske tipa inkaso oznacene su sa 3
				$id_kand = intval($_POST['id_nd_kandidata_biljeska_nd_new3']);
				$tip_bilj = intval($_POST['tip_biljeska_nd_new3']);
				$razlog_bilj = NULL;
				$sadrzaj_bilj = $_POST['sadrzaj_biljeska_nd_new3'];
				$predracun_bilj = intval($_POST['predracun_biljeska_nd_new3']);
				$vrijeme_zvanja_bilj = NULL;
				$vrijeme_uplate_bilj = NULL;
				$drzava_bilj = NULL;
				$ugovor_bilj = NULL;
				// var_dump($id_kand. " ".$predracun_bilj." ".$tip_bilj);
				// exit();
				$status_pred = $db->prepare("
					SELECT pr_status, pr_naplata_preko, pr_zaposlenik
					FROM idk_predracuni
					WHERE pr_id = :pr_id
				");
				$status_pred->execute(array(':pr_id' => $predracun_bilj ));
				$row_status = $status_pred->fetch();
				$pred_status = intval($row_status["pr_status"]);
				$pr_naplata_preko = intval($row_status["pr_naplata_preko"]);
				$zaposlenik = intval($row_status["pr_zaposlenik"]); //ovo dodano
				if($tip_bilj != 1){
					if($tip_bilj == 7){
						$update_zadnja_inkaso = $db->prepare("
							UPDATE idk_nd_kandidata_biljeske
							SET zadnja_inkaso_biljeska = 0
							WHERE zadnja_inkaso_biljeska = 1 AND id_kandidata_biljeska_nd = :id_kandidata_biljeska_nd AND status_biljeska_nd = 3
						");
						$update_zadnja_inkaso->execute(array(
							':id_kandidata_biljeska_nd' => $id_kand
						));
					}else{
						$update_zadnja_inkaso = $db->prepare("
							UPDATE idk_nd_kandidata_biljeske
							SET zadnja_inkaso_biljeska = 0
							WHERE zadnja_inkaso_biljeska = 1 AND id_kandidata_biljeska_nd = :id_kandidata_biljeska_nd AND status_biljeska_nd = 3 AND predracun_id = :predracun_id
						");
						$update_zadnja_inkaso->execute(array(
							':id_kandidata_biljeska_nd' => $id_kand,
							':predracun_id' => $predracun_bilj
						));
					}
				}
				if($tip_bilj == 3){
					$zadnja_inkaso = 1;
					//Pozvati kasnije uplata_biljeska_nd_new3
					$vrijeme_zvanja_bilj = date("Y-m-d H:i:s", strtotime($_POST['kasnije_biljeska_nd_new3'].':00'));
				}else if($tip_bilj == 4){
					$zadnja_inkaso = 1;
					//Odustaje
					$razlog_bilj = $_POST['razlog_biljeska_nd_new3'];
					//Provjeravam funkcijom da li postoji vec izdat jedan predracun za tog kandidata - ako postoji - onda je to sigurno za prvu ratu i njega arhiviram
					if(getBrPredracunaNDKanR($id_kand) != 0){
						//Update vrsta ugovora
						$update_stari_ugovor = $db->prepare("
							UPDATE idk_nd_kandidata
							SET vrsta_ugovora_nd_kandidata = :vrsta_ugovora_nd_kandidata
							WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
						");
						$update_stari_ugovor->execute(array(
							':vrsta_ugovora_nd_kandidata' => NULL,
							':id_broj_nd_kandidata' => $id_kand
						));
						//Update starog predracuna - status arhiva
						$update_stari_predracun = $db->prepare("
							UPDATE idk_predracuni
							SET pr_status = :pr_status
							WHERE pr_kandidat_id = :pr_kandidat_id AND pr_status != 0
						");
						$update_stari_predracun->execute(array(
							':pr_status' => 0,
							':pr_kandidat_id' => $id_kand
						));
						//Update obracuna za stari predracun
						$update_obracune = $db->prepare("
							UPDATE idk_obracuni
							SET status_obracuna = :status_obracuna
							WHERE predracun_id = :predracun_id
						");
						$update_obracune->execute(array(
							':status_obracuna' => 2,
							':predracun_id' => $predracun_bilj
						));
						//Sad je potrebno staviti ugovor i uplatnicu iz dokumenata kao neaktivne dokumente
						
						if($pr_naplata_preko == 1 OR $pr_naplata_preko == 2){
							//update ugovora koji su preko CH
							$update_stari_ugovor = $db->prepare("
								UPDATE idk_nd_ugovori
								SET ug_status = :ug_status, ug_datum_arhiviranja = NOW()
								WHERE ug_kandidat_id = :ug_kandidat_id
							");
							$update_stari_ugovor->execute(array(
								':ug_status' => 0,
								':ug_kandidat_id' => $id_kand
							));
						}else{
							$update_stari_ugovor = $db->prepare("
								UPDATE idk_nd_kandidata_dokumenti
								SET tip_dokumenta_status = :tip_dokumenta_status
								WHERE id_kandidata_dokument_nd = :id_kandidata_dokument_nd AND tip_dokumenta = :tip_dokumenta AND tip_dokumenta_status != 0
							");
							$update_stari_ugovor->execute(array(
								':tip_dokumenta_status' => 0,
								':id_kandidata_dokument_nd' => $id_kand,
								':tip_dokumenta' => 1
							));
						}
						$update_stara_uplatnica = $db->prepare("
							UPDATE idk_nd_kandidata_dokumenti
							SET tip_dokumenta_status = :tip_dokumenta_status
							WHERE id_kandidata_dokument_nd = :id_kandidata_dokument_nd AND tip_dokumenta = :tip_dokumenta AND tip_dokumenta_status != 0
						");
						$update_stara_uplatnica->execute(array(
							':tip_dokumenta_status' => 0,
							':id_kandidata_dokument_nd' => $id_kand,
							':tip_dokumenta' => 2
						));
					}
					
					//Update proslog statusa END
					$sel_razlog = $db->prepare("
						SELECT ponovno_zvanje_ro, br_dana_ro
						FROM idk_ro_usluge
						WHERE id_ro = :id_ro"
					);

					$sel_razlog->execute(array(
						':id_ro' => $razlog_bilj
					));
					$sel_razlog_row = $sel_razlog->fetch();
					$p_z_kand = $sel_razlog_row['ponovno_zvanje_ro'];
					$z_n_kand = $sel_razlog_row['br_dana_ro'];
					if($p_z_kand == 1){	
						promjenaStatusaDIPLKandidat($id_kand, 1, 4, 1);
					}else{		
						promjenaStatusaDIPLKandidat($id_kand, 7, 0, 1);
					}
				}else if($tip_bilj == 5){
					$zadnja_inkaso = 1;
					//Uplata
					$vrijeme_uplate_bilj = date("Y-m-d H:i:s", strtotime($_POST['uplata_biljeska_nd_new3'].':00'));
				}else if($tip_bilj == 6){
					$zadnja_inkaso = 1;
					//Promjena ugovora drzava_biljeska_nd_new3  ugovor_biljeska_nd_new3
					$drzava_bilj = intval($_POST['drzava_biljeska_nd_new3']);
					$ugovor_bilj = intval($_POST['ugovor_biljeska_nd_new3']);
					
					//Algoritam promjene ugovora START
					
					$drzava_novi = $drzava_bilj;
					$ugovor_novi = $ugovor_bilj;
					//$zaposlenik = intval($_POST['zaposlenik_biljeska_nd_new3']); /* nema nidje ovoga */
					
					//Ovaj query je postavljen cisto iz razloga provjere odredjenih informacija 
					//NPR: 	
					//		na kojem se statusu nalazi zaposlenik
					//		Trenutna vrsta ugovora i sl
					$query_provjera = $db->prepare("
						SELECT status_nd_kandidata, pstatus_nd_kandidata, vrsta_ugovora_nd_kandidata
						FROM idk_nd_kandidata
						WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
					");
					$query_provjera->execute(array(
						':id_broj_nd_kandidata' => $id_kand
					));
					
					if($query_provjera->rowCount() != 0){
						$row_provjera = $query_provjera->fetch();
						$status = $row_provjera["status_nd_kandidata"]; //trenutni status
						$pstatus = $row_provjera["pstatus_nd_kandidata"]; //trenutni podstatus
						$vrstaugovora = $row_provjera["vrsta_ugovora_nd_kandidata"]; //trenutna vrsta ugovora (ako se bude biljezilo u logove sa koje vrste na koju je prebacio)
						//ako se vec nalazi na statusu U obradi Lead, onda je samo potrebno promjeniti ugovor, predracun i ostalo (bez promjene odredjenog statusa)
						//Update Starih Ugovora, Uplatnica, Predracuna START
						//-----------------------------------------------------------------
							//Provjeravam funkcijom da li postoji vec izdat jedan predracun za tog kandidata - ako postoji - onda je to sigurno za prvu ratu i njega arhiviram
							if(getBrPredracunaNDKanR($id_kand) != 0){
								//Update starog predracuna - status arhiva
								$update_stari_predracun = $db->prepare("
									UPDATE idk_predracuni
									SET pr_status = :pr_status
									WHERE pr_kandidat_id = :pr_kandidat_id AND pr_status != 0
								");
								$update_stari_predracun->execute(array(
									':pr_status' => 0,
									':pr_kandidat_id' => $id_kand
								));
								//Update obracuna za stari predracun
								$update_obracune = $db->prepare("
									UPDATE idk_obracuni
									SET status_obracuna = :status_obracuna
									WHERE predracun_id = :predracun_id
								");
								$update_obracune->execute(array(
									':status_obracuna' => 2,
									':predracun_id' => $predracun_bilj
								));
								//Sad je potrebno staviti ugovor i uplatnicu iz dokumenata kao neaktivne dokumente
								
								if($pr_naplata_preko == 1 OR $pr_naplata_preko == 2){
									//update ugovora koji su preko CH
									$update_stari_ugovor = $db->prepare("
										UPDATE idk_nd_ugovori
										SET ug_status = :ug_status, ug_datum_arhiviranja = NOW()
										WHERE ug_kandidat_id = :ug_kandidat_id
									");
									$update_stari_ugovor->execute(array(
										':ug_status' => 0,
										':ug_kandidat_id' => $id_kand
									));
								}else{
									$update_stari_ugovor = $db->prepare("
										UPDATE idk_nd_kandidata_dokumenti
										SET tip_dokumenta_status = :tip_dokumenta_status
										WHERE id_kandidata_dokument_nd = :id_kandidata_dokument_nd AND tip_dokumenta = :tip_dokumenta AND tip_dokumenta_status != 0
									");
									$update_stari_ugovor->execute(array(
										':tip_dokumenta_status' => 0,
										':id_kandidata_dokument_nd' => $id_kand,
										':tip_dokumenta' => 1
									));
								}
								
								$update_stara_uplatnica = $db->prepare("
									UPDATE idk_nd_kandidata_dokumenti
									SET tip_dokumenta_status = :tip_dokumenta_status
									WHERE id_kandidata_dokument_nd = :id_kandidata_dokument_nd AND tip_dokumenta = :tip_dokumenta AND tip_dokumenta_status != 0
								");
								$update_stara_uplatnica->execute(array(
									':tip_dokumenta_status' => 0,
									':id_kandidata_dokument_nd' => $id_kand,
									':tip_dokumenta' => 2
								));
							}
							
						//-----------------------------------------------------------------
						//Update Starih Ugovora, Uplatnica, Predracuna END
						
						
						//Dodavanje Novih START
						//-----------------------------------------------------------------
							if($drzava_novi == 1){
								$drzava = "BiH";
							}elseif($drzava_novi == 2){
								$drzava = "Srbija";
							}else{
								$drzava = "Njemacka";
							}
							$month = date('m');
							$day = date('d');
							$year = date('Y');
							$year_skr = date('y');
							
							
							
							//Radi se update za vrstu ugovora
							$vrsta_ugovora_x = $db->prepare("
								UPDATE idk_nd_kandidata
								SET vrsta_ugovora_nd_kandidata = :vrsta_ugovora_nd_kandidata
								WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
							");
							$vrsta_ugovora_x->execute(array( 
								':id_broj_nd_kandidata' => $id_kand,
								':vrsta_ugovora_nd_kandidata' => $ugovor_novi
							));
							//Aktivna firma za naplate
							// 1 - CH; 0 - stari nacin: BiH i SRB zasebno
							/*
							$get_naplata = $db->prepare("
										SELECT fk_naplata_preko 
										FROM idk_nd_naplata_preko 
										WHERE aktivno = 1
							");
							$get_naplata->execute();
							$row_naplata = $get_naplata->fetch();
							$naplata_preko = $row_naplata["fk_naplata_preko"];
							*/
							$naplata_preko = getNaplataPrekoDIPLR($drzava_novi);
							
							if($naplata_preko == 1){
								
								$brojac_predracuna = createBrojPredracuna("ch");
								$novi_predracun = "DIPLCH-".$brojac_predracuna."-".$year_skr;
								
								$iznos_rate = getIznosRate($ugovor_novi, "ch", "rata1");
								
								$ch = curl_init();
								// Disable SSL verification
								curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
								// Will return the response, if false it print the response
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								// Set the url
								$rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
								curl_setopt($ch, CURLOPT_URL,$rls);
								// Execute
								$result=curl_exec($ch);
								curl_close($ch);

								$data = json_decode($result, TRUE);
								$EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
								$EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
								$RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
								$RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
								
								$EUR = str_replace(',', '.', $EUR1);
								$RSD = str_replace(',', '.', $RSD1);
								
								$rata_eur = $iznos_rate / $EUR;
								$rata_rsd = 100*$iznos_rate / $RSD;
								
								$rata_bam_f = number_format((float)$iznos_rate, 2, '.', '');
								$rata_eur_f = number_format((float)$rata_eur, 2, '.', '');
								$rata_rsd_f = number_format((float)$rata_rsd, 2, '.', '');
								
								if($drzava_novi == 1){
									$slovo_drz = "B";
									$domaca_valuta = "BAM";
									$jezik_ugovora = "bs";
								}
								elseif($drzava_novi == 2){
									$slovo_drz = "S";
									$domaca_valuta = "RSD";
									$jezik_ugovora = "sr";
								}
								elseif($drzava_novi == 3){
									$slovo_drz = "D";
									$domaca_valuta = "EUR";
									$jezik_ugovora = "de";
								}
								
								$file_datum = date('YmdHis'); 
								$file_name = $file_datum."DIPL".$slovo_drz.$brojac_predracuna.".pdf";
								$insert_predracun = $db->prepare("	
											INSERT INTO idk_predracuni	
											(pr_broj_predracuna,  pr_naplata_preko, pr_kandidat_id, pr_zaposlenik, pr_vrsta_predracuna, pr_rata, pr_domaca_valuta, pr_vrijednost_BAM, pr_vrijednost_RSD, pr_vrijednost_EUR, pr_stari_ink_status)	
											VALUES	
											(:pr_broj_predracuna,:pr_naplata_preko,:pr_kandidat_id,:pr_zaposlenik,:pr_vrsta_predracuna,:pr_rata,:pr_domaca_valuta,:pr_vrijednost_BAM,:pr_vrijednost_RSD,:pr_vrijednost_EUR, :pr_stari_ink_status)	
											");	
								$insert_predracun->execute(array(	
											':pr_broj_predracuna' => $novi_predracun,	
											':pr_naplata_preko' => $naplata_preko,	
											':pr_kandidat_id' => $id_kand,	
											':pr_zaposlenik' => $zaposlenik,	
											':pr_vrsta_predracuna' => 1,	
											':pr_rata' => 1,	
											':pr_domaca_valuta' => $domaca_valuta,	
											':pr_vrijednost_BAM' => $rata_bam_f,	
											':pr_vrijednost_RSD' => $rata_rsd_f,	
											':pr_vrijednost_EUR' => $rata_eur_f,
											':pr_stari_ink_status' => $pred_status
											));

								//Get last ID
								$predracun_id = $db->lastInsertId();
								//Zakomentarisao jer se generise tek kod prihvatanja ugovora ADIS
								// if($drzava != "Njemacka"){
									// generisiPredracun($predracun_id, $drzava);
									// generisiPredracun($predracun_id, "Njemacka");
								// }else{
									// generisiPredracun($predracun_id, "Njemacka");
								// }
								
								//generisanje tokena
								$token = $file_datum.$id_kand;
								$token_h = hash("md5", $token);
								
								$insert_ugovor = $db->prepare("	
											INSERT INTO idk_nd_ugovori	
											(ug_kandidat_id,  ug_vrsta, ug_zaposlenik_id, ug_token, ug_jezik, ug_status, ug_datum_slanja)	
											VALUES	
											(:ug_kandidat_id, :ug_vrsta, :ug_zaposlenik_id,:ug_token,:ug_jezik,:ug_status,:ug_datum_slanja)	
											");	
								$insert_ugovor->execute(array(	
											':ug_kandidat_id' => $id_kand,
											':ug_vrsta' => $ugovor_novi,
											':ug_zaposlenik_id' => $zaposlenik,
											':ug_token' => $token_h,
											':ug_jezik' => $jezik_ugovora,
											':ug_status' => 1,
											':ug_datum_slanja' => date('Y-m-d H:i:s')	
											));
								$ugovor_link = getSiteUrlr()."ugovor/".$token_h."/".$jezik_ugovora;
								$poruka_text_viber 	= "";
								$poruka_text_sms 	= "";
								$poruka_button		= "";
								
								if($drzava == "Srbija"){
									createUplatnicaSRB($predracun_id);
									$poruka_text_viber	= "Vaš proces nostrifikacije u zajedničkoj saradnji je počeo! 🤗\n\n";
									$poruka_text_viber .= "Nakon što otvorite ugovor, sledite upute kako biste prihvatili isti. Možete također pogledati  predračun, te preuzeti uplatnicu.\n\n";
									$poruka_text_viber .= "Zahvaljujemo Vam se na poverenju! Tu smo za Vas i sve što Vas interesuje.\n\n";
									$poruka_text_viber .= "Srdačan pozdrav,\n\nJSI Team";

									$poruka_text_sms	= "Vas proces nostrifikacije u zajednickoj saradnji je poceo! 🤗\n\n";
									$poruka_text_sms   .= "Nakon sto otvorite ugovor, sledite upute kako biste prihvatili isti. Mozete takoder pogledati  predracun, te preuzeti uplatnicu.\n\n";
									$poruka_text_sms   .= "Zahvaljujemo Vam se na poverenju! Tu smo za Vas i sve sto Vas interesuje.\n\n";
									$poruka_text_sms   .= "Srdacan pozdrav,\n\nJSI Team\n\n".$ugovor_link;
																						
									$poruka_button 		=  "Ugovor";
								}elseif($drzava == "BiH"){
									createUplatnicaBIH($predracun_id);
									$poruka_text_viber	= "Vaš proces nostrifikacije u zajedničkoj saradnji je počeo! 🤗\n\n";
									$poruka_text_viber .= "Nakon što otvorite ugovor, slijedite upute kako biste prihvatili isti. Možete također pogledati  predračun, te preuzeti uplatnicu.\n\n";
									$poruka_text_viber .= "Zahvaljujemo Vam se na povjerenju, tu smo za Vas i sve što Vas interesuje.\n\n";
									$poruka_text_viber .= "Lijep pozdrav,\nJSI Team";
									
									$poruka_text_sms	= "Vas proces nostrifikacije u zajednickoj saradnji je poceo! 🤗\n\n";
									$poruka_text_sms   .= "Nakon sto otvorite ugovor, slijedite upute kako biste prihvatili isti. Mozete takoder pogledati  predracun, te preuzeti uplatnicu.\n\n";
									$poruka_text_sms   .= "Zahvaljujemo Vam se na povjerenju, tu smo za Vas i sve sto Vas interesuje.\n\n";
									$poruka_text_sms   .= "Lijep pozdrav,\nJSI Team\n\n".$ugovor_link;
									
									$poruka_button 		=  "Ugovor";
								}else{
									generisiInoUplatnicu($predracun_id);
									$poruka_text_viber	= "Ihr Anerkennungsverfahren in gegenseitiger Zusammenarbeit hat begonnen! 🤗\n\n";
									$poruka_text_viber .= "Nachdem Sie den Vertrag geöffnet haben, folgen Sie die Anweisungen, um den Vertrag zu akzeptieren. Sie können auch die Pro-forma-Rechnung durchlesen und den Einzahlungsschein herunterladen.\n\n";
									$poruka_text_viber .= "Wir bedanken uns für Ihr Vertrauen und stehen für alle Fragen gerne zur Verfügung.\n\n";
									$poruka_text_viber .= "Mit freundlichen Grüßen,\n\nJobstep International Team";
									
									$poruka_text_sms	= "Ihr Anerkennungsverfahren in gegenseitiger Zusammenarbeit hat begonnen! 🤗\n\n";
									$poruka_text_sms   .= "Nachdem Sie den Vertrag geöffnet haben, folgen Sie die Anweisungen, um den Vertrag zu akzeptieren. Sie können auch die Pro-forma-Rechnung durchlesen und den Einzahlungsschein herunterladen.\n\n";
									$poruka_text_sms   .= "Wir bedanken uns für Ihr Vertrauen und stehen für alle Fragen gerne zur Verfügung.\n\n";
									$poruka_text_sms   .= "Mit freundlichen Grüßen,\n\nJobstep International Team\n\n".$ugovor_link;
																						
									$poruka_button 		=  "Vertrag";
								}
								sendMailUgovorLink($id_kand, $ugovor_link);
								sendViberUgovorLink($id_kand, $poruka_text_viber, $poruka_text_sms, $ugovor_link, $poruka_button);

							}elseif($naplata_preko == 2){
								/*
									Mix način - digitalno / drzava domacin START 
									*/
										$brojac_predracuna = createBrojPredracuna($drzava);
										$iznos_rate = getIznosRate($ugovor_novi, $drzava, "rata1");

										/*
											Dio odradjen samo za Srbiju START 
											*/
												$slovo_drz = "S";
												$domaca_valuta = "RSD";
												$jezik_ugovora = "sr";
												$rata_rsd = $iznos_rate;
												
												$ch = curl_init();
												// Disable SSL verification
												curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
												// Will return the response, if false it print the response
												curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
												// Set the url
												$rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
												// var_dump($rls);
												// exit();
												curl_setopt($ch, CURLOPT_URL,$rls);
												curl_setopt($ch, CURLOPT_FAILONERROR, true); // Required for HTTP error codes to be reported via our call to curl_error($ch)
												// Execute
												$result=curl_exec($ch);
												
												curl_close($ch);

												$data = json_decode($result, TRUE);
												$EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
												$EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
												$RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
												$RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
												
												$EUR = str_replace(',', '.', $EUR1);
												$RSD = str_replace(',', '.', $RSD1);
												$rata_bam = ($rata_rsd / 100) * $RSD ;
												$rata_eur = $rata_bam / $EUR;
												
												$rata_bam_f = number_format((float)$rata_bam, 2, '.', '');
												$rata_eur_f = number_format((float)$rata_eur, 2, '.', '');
												$rata_rsd_f = number_format((float)$rata_rsd, 2, '.', '');
											/*
											Dio odradjen samo za Srbiju START 
										*/

										$file_datum = date('YmdHis'); 
										$file_name = $file_datum."DIPL".$slovo_drz.$brojac_predracuna.".pdf";
										$novi_predracun = "DIPL".$slovo_drz."-".$brojac_predracuna."-".$year_skr;

										/*
											Insert predracuna START 
											*/
												$insert_predracun = $db->prepare("	
													INSERT INTO idk_predracuni	
														(
															pr_broj_predracuna, 
															pr_naplata_preko, 
															pr_kandidat_id, 
															pr_zaposlenik, 
															pr_vrsta_predracuna, 
															pr_rata, 
															pr_domaca_valuta, 
															pr_vrijednost_BAM, 
															pr_vrijednost_RSD, 
															pr_vrijednost_EUR,
															pr_stari_ink_status
														)	
													VALUES	
														(
															:pr_broj_predracuna,
															:pr_naplata_preko,
															:pr_kandidat_id,
															:pr_zaposlenik,
															:pr_vrsta_predracuna,
															:pr_rata,
															:pr_domaca_valuta,
															:pr_vrijednost_BAM,
															:pr_vrijednost_RSD,
															:pr_vrijednost_EUR,
															:pr_stari_ink_status
														)	
												");	
												
												$insert_predracun->execute(array(	
													':pr_broj_predracuna' => $novi_predracun,
													':pr_naplata_preko' => $naplata_preko,
													':pr_kandidat_id' => $id_kand,
													':pr_zaposlenik' => $zaposlenik,
													':pr_vrsta_predracuna' => 1,
													':pr_rata' => 1,
													':pr_domaca_valuta' => $domaca_valuta,
													':pr_vrijednost_BAM' => $rata_bam_f,
													':pr_vrijednost_RSD' => $rata_rsd_f,
													':pr_vrijednost_EUR' => $rata_eur_f,
													':pr_stari_ink_status' => $pred_status
												));
												
												$predracun_id = $db->lastInsertId();
											/*
											Insert predracuna END 
										*/

										/*
											Insert ugovora START
											*/

												//KREIRANJE BROJA UGOVORA U FORMATU DIPLS-BROJ UGOVORA U MJESECU-MJESEC(dvocifren 01)/zadnje dvije cifre godine
												$ug_broj = createBrojUgovoraSrbija();
												$ug_full_broj = "DIPLS-".$ug_broj."-".date('m')."/".date('y');
												$token = $file_datum.$id_kand;
												$token_h = hash("md5", $token);
												
												$insert_ugovor = $db->prepare("	
													INSERT INTO idk_nd_ugovori	
														(
															ug_kandidat_id,
															ug_broj,
															ug_vrsta,
															ug_zaposlenik_id, 
															ug_token, 
															ug_jezik, 
															ug_status, 
															ug_datum_slanja
														)	
													VALUES	
														(
															:ug_kandidat_id,
															:ug_broj,
															:ug_vrsta,
															:ug_zaposlenik_id,
															:ug_token,
															:ug_jezik,
															:ug_status,
															:ug_datum_slanja
														)	
												");	
												$insert_ugovor->execute(array(	
													':ug_kandidat_id' => $id_kand,
													':ug_broj' => $ug_full_broj,
													':ug_vrsta' => $ugovor_novi,
													':ug_zaposlenik_id' => $zaposlenik,
													':ug_token' => $token_h,
													':ug_jezik' => $jezik_ugovora,
													':ug_status' => 1,
													':ug_datum_slanja' => date('Y-m-d H:i:s')	
												));
												$ugovor_link = getSiteUrlr()."ugovorNew/".$token_h."/".$jezik_ugovora;
											/*
											Insert ugovora END
										*/
										
										/*
											Slanje ugovora START
											*/
												// Provjeriti da li je bilo promjena na porukama sms i viber Adis 444
												$poruka_text_viber 	= "";
												$poruka_text_sms 	= "";
												$poruka_button		= "";

												createUplatnicaSRB($predracun_id);
												$poruka_text_viber	= "Vaš proces nostrifikacije/evaluacije u zajedničkoj saradnji je počeo! 🤗\n\n";
												$poruka_text_viber .= "Nakon što otvorite ugovor, sledite upute kako biste prihvatili isti. Možete također pogledati  predračun, te preuzeti uplatnicu.\n\n";
												$poruka_text_viber .= "Zahvaljujemo Vam se na poverenju! Tu smo za Vas i sve što Vas interesuje.\n\n";
												$poruka_text_viber .= "Srdačan pozdrav,\n\nJSI Team";

												$poruka_text_sms	= "Vas proces nostrifikacije/evaluacije u zajednickoj saradnji je poceo! 🤗\n\n";
												$poruka_text_sms   .= "Nakon sto otvorite ugovor, sledite upute kako biste prihvatili isti. Mozete takoder pogledati  predracun, te preuzeti uplatnicu.\n\n";
												$poruka_text_sms   .= "Zahvaljujemo Vam se na poverenju! Tu smo za Vas i sve sto Vas interesuje.\n\n";
												$poruka_text_sms   .= "Srdacan pozdrav,\n\nJSI Team\n\n".$ugovor_link;
																									
												$poruka_button 		=  "Ugovor";

												sendMailUgovorLink($id_kand, $ugovor_link, $naplata_preko);
												sendViberUgovorLink($id_kand, $poruka_text_viber, $poruka_text_sms, $ugovor_link, $poruka_button);	
											/*
											Slanje ugovora END
										*/
									/*
									Mix način - digitalno / drzava domacin END 
								*/
							}elseif($naplata_preko == 0){
								
								$brojac_predracuna = createBrojPredracuna($drzava);
								$iznos_rate = getIznosRate($ugovor_novi, $drzava, "rata1");
								
								if($drzava == "Srbija"){
									$slovo_drz = "S";
									$domaca_valuta = "RSD";
									$rata_rsd = $iznos_rate;
									
									$ch = curl_init();
									// Disable SSL verification
									curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
									// Will return the response, if false it print the response
									curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
									// Set the url
									$rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
									// var_dump($rls);
									// exit();
									curl_setopt($ch, CURLOPT_URL,$rls);
									curl_setopt($ch, CURLOPT_FAILONERROR, true); // Required for HTTP error codes to be reported via our call to curl_error($ch)
									// Execute
									$result=curl_exec($ch);
									
									curl_close($ch);

									$data = json_decode($result, TRUE);
									$EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
									$EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
									$RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
									$RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
									
									$EUR = str_replace(',', '.', $EUR1);
									$RSD = str_replace(',', '.', $RSD1);
									$rata_bam = ($rata_rsd / 100) * $RSD ;
									$rata_eur = $rata_bam / $EUR;
									
									$rata_bam_f = number_format((float)$rata_bam, 2, '.', '');
									$rata_eur_f = number_format((float)$rata_eur, 2, '.', '');
									$rata_rsd_f = number_format((float)$rata_rsd, 2, '.', '');
									
								}else{
									$slovo_drz = "B";
									$domaca_valuta = "BAM";
									$rata_bam = $iznos_rate;
									
									$ch = curl_init();
									// Disable SSL verification
									curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
									// Will return the response, if false it print the response
									curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
									// Set the url
									$rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
									curl_setopt($ch, CURLOPT_URL,$rls);
									// Execute
									$result=curl_exec($ch);
									curl_close($ch);

									$data = json_decode($result, TRUE);
									$EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
									$EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
									$RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
									$RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
									
									$EUR = str_replace(',', '.', $EUR1);
									$RSD = str_replace(',', '.', $RSD1);
									
									$rata_eur = $rata_bam / $EUR;
									$rata_rsd = 100*$rata_bam / $RSD;
									
									$rata_bam_f = number_format((float)$rata_bam, 2, '.', '');
									$rata_eur_f = number_format((float)$rata_eur, 2, '.', '');
									$rata_rsd_f = number_format((float)$rata_rsd, 2, '.', '');
									
								}
								$novi_predracun = "DIPL".$slovo_drz."-".$brojac_predracuna."-".$year_skr;
								$file_datum = date('YmdHis'); 
								$file_name = $file_datum."DIPL".$slovo_drz.$brojac_predracuna.".pdf";
								
								//AKO SE RADI O 100% POPUSTU ONDA OZNACITI PREDRACUN DA JE VEC UPLACEN
								if($ugovor_novi == 99){
									$pr_status = 2;
									$pr_uplaceno = 1;
									$pr_datum_uplate = date("Y-m-d H:i:s");
								}else{
									$pr_status = 1;
									$pr_uplaceno = 0;
									$pr_datum_uplate = null;
								}
								$insert_predracun = $db->prepare("	
											INSERT INTO idk_predracuni	
											(pr_broj_predracuna,  pr_naplata_preko, pr_kandidat_id, pr_zaposlenik, pr_vrsta_predracuna, pr_rata, pr_domaca_valuta, pr_vrijednost_BAM, pr_vrijednost_RSD, pr_vrijednost_EUR, pr_file, pr_stari_ink_status, pr_inkaso_agent, pr_status, pr_uplaceno, pr_datum_uplate)	
											VALUES	
											(:pr_broj_predracuna,:pr_naplata_preko,:pr_kandidat_id,:pr_zaposlenik,:pr_vrsta_predracuna,:pr_rata,:pr_domaca_valuta,:pr_vrijednost_BAM,:pr_vrijednost_RSD,:pr_vrijednost_EUR,:pr_file,:pr_stari_ink_status,:pr_inkaso_agent,:pr_status,:pr_uplaceno,:pr_datum_uplate)	
											");	
								$insert_predracun->execute(array(	
											':pr_broj_predracuna' => $novi_predracun,	
											':pr_naplata_preko' => $naplata_preko,	
											':pr_kandidat_id' => $id_kand,	
											':pr_zaposlenik' => $zaposlenik,	
											':pr_vrsta_predracuna' => 1,	
											':pr_rata' => 1,	
											':pr_domaca_valuta' => $domaca_valuta,	
											':pr_vrijednost_BAM' => $rata_bam_f,	
											':pr_vrijednost_RSD' => $rata_rsd_f,	
											':pr_vrijednost_EUR' => $rata_eur_f,	
											':pr_file' => $file_name,
											':pr_stari_ink_status' => $pred_status,
											':pr_inkaso_agent' => $logged_employee_id,
											':pr_status' => $pr_status,
											':pr_uplaceno' => $pr_uplaceno,
											':pr_datum_uplate' => $pr_datum_uplate
											));

								//Get last ID
								$predracun_id = $db->lastInsertId();
								
								if($drzava == "Srbija"){
									createPredracunSRB($predracun_id);
									$putanja_uplatnica = createUplatnicaSRB($predracun_id);
									$putanja_ugovor = createUgovorSRB($id_kand);
									//createInfoListSRB();
									if($ugovor_novi != 99){
										sendMailPredracunUgovorSRB($file_name, $putanja_ugovor, $putanja_uplatnica, $id_kand);
									}
								}
								else{
									createPredracunBIH($predracun_id);
									$putanja_uplatnica = createUplatnicaBIH($predracun_id);
									$putanja_ugovor = createUgovorBIH($id_kand);
									//fja za slanje maila za bih
									if($ugovor_novi == 11 OR $ugovor_novi == 12){
										sendMailPredracunUgovorBIH($file_name, $putanja_ugovor, "ne", $id_kand);
									}elseif($ugovor_novi == 99){
										
									}else{
										sendMailPredracunUgovorBIH($file_name, $putanja_ugovor, $putanja_uplatnica, $id_kand);
									}
								}
							}
							//KREIRANJE NOVIH OBRACUNA
							ubaciObracune($predracun_id);
						
							$log_desc = "DIPL -> INKASO PROMJENA UGOVORA za kandidata ID = [".$id_kand."] sa ugovora BROJ = [".$vrstaugovora."] na ugovor BROJ = [".$ugovor_novi."].";
							$log_date = date('Y-m-d H:i:s');

							$log_query = $db->prepare("
								INSERT INTO idk_logs
									(log_employeeid, log_desc, log_date)
								VALUES
									(:log_employeeid, :log_desc, :log_date)
							");

							$log_query->execute(array(
								':log_employeeid' => $logged_employee_id,
								':log_desc' => $log_desc,
								':log_date' => $log_date
							));
						//-----------------------------------------------------------------
						//Dodavanje Novih END
						
						if($status == 1 AND $pstatus != 5){
							$vrijeme_stari_status = $db->prepare("
								SELECT id_log_status_nd_kandidata, vrijeme_promjene_statusa_nd_kandidata
								FROM idk_nd_kandidata_status_log
								WHERE idd_broj_nd_kandidata = :idd_broj_nd_kandidata AND broj_dana_statusa_nd_kandidata is null
							");
							$vrijeme_stari_status->execute(array(
								':idd_broj_nd_kandidata' => $id_kand
							));
							$vrijeme_stari_status_row = $vrijeme_stari_status->fetch();
							$id_log_statusa = $vrijeme_stari_status_row['id_log_status_nd_kandidata'];
							$vrijeme_log_statusa = $vrijeme_stari_status_row['vrijeme_promjene_statusa_nd_kandidata'];
							
							$trenutno_vrijeme_statusne_promjene = date('Y-m-d H:i:s');
							
							$diff_novi = strtotime($trenutno_vrijeme_statusne_promjene) - strtotime($vrijeme_log_statusa);
							$day_novi = floor($diff_novi/86400);
							
							$update_vrijeme_stari = $db->prepare("
											UPDATE idk_nd_kandidata_status_log
											SET broj_dana_statusa_nd_kandidata = :broj_dana_statusa_nd_kandidata
											WHERE idd_broj_nd_kandidata = :idd_broj_nd_kandidata AND id_log_status_nd_kandidata = :id_log_status_nd_kandidata");

							$update_vrijeme_stari->execute(array(
										':idd_broj_nd_kandidata' => $id_kand,
										':id_log_status_nd_kandidata' => $id_log_statusa,
										':broj_dana_statusa_nd_kandidata' => $day_novi
										));
										
							$new_ND_status = $db->prepare("
												INSERT INTO idk_nd_kandidata_status_log
												(
													idd_broj_nd_kandidata,
													status_nd_kandidata,
													pstatus_nd_kandidata,
													vrijeme_promjene_statusa_nd_kandidata,
													promjenio_zaposlenik_nd_kandidata
												)
												VALUES
												(
													:idd_broj_nd_kandidata,
													:status_nd_kandidata,
													:pstatus_nd_kandidata,
													:vrijeme_promjene_statusa_nd_kandidata,
													:promjenio_zaposlenik_nd_kandidata
												)
							");
							
							$new_ND_status->execute(array(
											':idd_broj_nd_kandidata' => $id_kand,
											':status_nd_kandidata' => 1,
											':pstatus_nd_kandidata' => 5,
											':vrijeme_promjene_statusa_nd_kandidata' => date('Y-m-d H:i:s'),
											':promjenio_zaposlenik_nd_kandidata' => $zaposlenik
											));
							//Update proslog statusa END
							$query_p_s = $db->prepare("
								UPDATE idk_nd_kandidata
								SET pstatus_nd_kandidata = :pstatus_nd_kandidata
								WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata");

							$query_p_s->execute(array(
										':id_broj_nd_kandidata' => $id_kand,
										':pstatus_nd_kandidata' => 5
										));
							
							updateProjekcijeKandidat(1, $id_kand);
							
							if($ugovor_novi == 99){
								promjenaStatusaDIPLKandidat($id_kand, 2, 0, 1);
								//ovdje sam uocio da je u funkciji $idKandidat koji ne postoji nigdje u case-u i zbog toga postavljam ispravno $id_kand
								sendMailViberCheckListDIPL($predracun_id);
							}
						}
					}
					
					//Algoritam promjene ugovora END
					
				}else if($tip_bilj == 7){
					$zadnja_inkaso = 1;
					//Odustaje
					$razlog_bilj = 365;
					//Update vrsta ugovora
					$update_stari_ugovor = $db->prepare("
						UPDATE idk_nd_kandidata
						SET vrsta_ugovora_nd_kandidata = :vrsta_ugovora_nd_kandidata
						WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
					");
					$update_stari_ugovor->execute(array(
						':vrsta_ugovora_nd_kandidata' => NULL,
						':id_broj_nd_kandidata' => $id_kand
					));
					//Update starog predracuna - status arhiva
					$update_stari_predracun = $db->prepare("
						UPDATE 
							idk_predracuni
						SET 
							pr_status = :pr_status
						WHERE
							pr_kandidat_id = :pr_kandidat_id
							AND
							pr_vrsta_predracuna = 1
							AND 
							pr_status IN (1,3,4,5)
							AND 
							pr_uplaceno = 0
							AND 
							pr_izdan_racun = 0
					");
					$update_stari_predracun->execute(array(
						':pr_status' => 0,
						':pr_kandidat_id' => $id_kand
					));

					if($pr_naplata_preko == 1 OR $pr_naplata_preko == 2){
						//update ugovora koji su preko CH
						$update_stari_ugovor = $db->prepare("
							UPDATE idk_nd_ugovori
							SET ug_status = :ug_status, ug_datum_arhiviranja = NOW()
							WHERE ug_kandidat_id = :ug_kandidat_id
						");
						$update_stari_ugovor->execute(array(
							':ug_status' => 0,
							':ug_kandidat_id' => $id_kand
						));
					}else{
						$update_stari_ugovor = $db->prepare("
							UPDATE idk_nd_kandidata_dokumenti
							SET tip_dokumenta_status = :tip_dokumenta_status
							WHERE id_kandidata_dokument_nd = :id_kandidata_dokument_nd AND tip_dokumenta = :tip_dokumenta
						");
						$update_stari_ugovor->execute(array(
							':tip_dokumenta_status' => 0,
							':id_kandidata_dokument_nd' => $id_kand,
							':tip_dokumenta' => 1
						));
					}

					$update_stara_uplatnica = $db->prepare("
						UPDATE idk_nd_kandidata_dokumenti
						SET tip_dokumenta_status = :tip_dokumenta_status
						WHERE id_kandidata_dokument_nd = :id_kandidata_dokument_nd AND tip_dokumenta = :tip_dokumenta AND tip_dokumenta_status != 0
					");
					$update_stara_uplatnica->execute(array(
						':tip_dokumenta_status' => 0,
						':id_kandidata_dokument_nd' => $id_kand,
						':tip_dokumenta' => 2
					));
					$statusKandidatInk = getStatusValueDIPLKandidatR($id_kand);
					if($statusKandidatInk["status"] != 7){
						promjenaStatusaDIPLKandidat($id_kand, 7, 0, 1);
					}
					
				}else{
					if($tip_bilj == 1){
						$zadnja_inkaso = 0;
					}else{
						$zadnja_inkaso = 1;
					}
					//Tip komunikacije Ne javlja se i Ostalo
				}
				
				$insert_query = $db->prepare("
					INSERT INTO idk_nd_kandidata_biljeske
						(id_kandidata_biljeska_nd, status_biljeska_nd, tip_biljeska_nd, razlog_biljeska_nd, sadrzaj_biljeska_nd, vrijeme_dodavanja_biljeska_nd, vrijeme_grupa_biljeska_nd, dodao_zaposlenik_biljeska_nd, predracun_id, predracun_status, vrijeme_ponovnog_zvanja, uplata_na_datum, zadnja_inkaso_biljeska)
					VALUES
						(:id_kandidata_biljeska_nd, :status_biljeska_nd, :tip_biljeska_nd, :razlog_biljeska_nd, :sadrzaj_biljeska_nd, :vrijeme_dodavanja_biljeska_nd, :vrijeme_grupa_biljeska_nd, :dodao_zaposlenik_biljeska_nd, :predracun_id, :predracun_status, :vrijeme_ponovnog_zvanja, :uplata_na_datum, :zadnja_inkaso_biljeska)
				");

				$insert_query->execute(array(
					':id_kandidata_biljeska_nd' => $id_kand,
					':status_biljeska_nd' => 3,
					':tip_biljeska_nd' => $tip_bilj,
					':razlog_biljeska_nd' => $razlog_bilj,
					':sadrzaj_biljeska_nd' => $sadrzaj_bilj,
					':vrijeme_dodavanja_biljeska_nd' => date("Y-m-d H:i:s"),
					':vrijeme_grupa_biljeska_nd' => date("Y"),
					':dodao_zaposlenik_biljeska_nd' => $logged_employee_id,
					':predracun_id' => $predracun_bilj,
					':predracun_status' => $pred_status,
					':vrijeme_ponovnog_zvanja' => $vrijeme_zvanja_bilj,
					':uplata_na_datum' => $vrijeme_uplate_bilj,
					':zadnja_inkaso_biljeska' => $zadnja_inkaso
				));
				$biljeska_id_zadnja = $db->lastInsertId();
				
				$log_desc = "DIPL -> Dodana inkaso bilješka ID = [".$biljeska_id_zadnja."] za kandidata [".$id_kand."] sa sadrzajem [".substr($sadrzaj_bilj, 0, 50)."...].";
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
				
				header("Location: " . getSiteURLr() . "nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$id_kand); 
			break;
			
			case "edit_recognition_dipl":
				$er_kandidat_id = $_POST["er_kandidat_id"];
				$er_nost_id = $_POST["er_nost_id"];
				$er_type_vr = $_POST["er_type_vr"]; 

				$query = $db->prepare("
					UPDATE 
						idk_nostrifikovane_diplome
					SET 
						full_recognition = :full_recognition
					WHERE 
						id_cand_dipl = :id_cand_dipl
						AND 
						id_nd = :id_nd
				");
				$query->execute(array(
					":full_recognition" => $er_type_vr, 
					":id_cand_dipl" => $er_kandidat_id, 
					":id_nd" => $er_nost_id
				));

				$log_automatsko_prebacivanje = "";
				try{
					$resultPrebacivanja = prebaciNaPrikupljanjeDokumentacije($er_kandidat_id, 2);
				}catch (Exception $e){
					$resultPrebacivanja = $e->getMessage();
				}
				$log_automatsko_prebacivanje = "Status automatskog prebacivanja na prikupljanje dokumentacije: ".$resultPrebacivanja;

				$log_desc = "DIPL -> Uređena vrsta nostrifikacije diplome ID = [".$er_nost_id."] za kandidata [".$er_kandidat_id."] sa vrijednosti [".$er_type_vr."]. ".$log_automatsko_prebacivanje;
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
				
				header("Location: " . getSiteURLr() . "nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$er_kandidat_id);

			break; 

			case "insert_recognition_dipl":
				$ir_kandidat_id = $_POST["ir_kandidat_id"];
				$ir_type_vr = $_POST["ir_type_vr"];
				$ir_file_vr = $_FILES["ir_file_vr"];

				$nazivDokument = "Gleichwertigkeitsbescheid";

				//Spremanje dokumenta START
					//File properties
					$file_name = $ir_file_vr['name'];
					$file_tmp = $ir_file_vr['tmp_name'];
					
					//File extension
					$file_ext = explode('.', $file_name);
					$file_ext = strtolower(end($file_ext));
					$allowed = array('pdf');

					if(in_array($file_ext, $allowed)) {

						$file_name_new = uniqid() . '.' . $file_ext;
						$file_destination = "files/dokumenti_ND_kandidat/" . $file_name_new;

						if(move_uploaded_file($file_tmp, $file_destination)){}
					}
				//Spremanje dokumenta END

				//Unos dokumenta u ostale dokumente na DIPL-u START
					$insertOstaliDipl = $db->prepare("
						INSERT INTO idk_nd_kandidata_dokumenti
							(
								naziv_dokument_nd, 
								naziv_dokument_ostali_nd, 
								status_dokument_nd, 
								id_kandidata_dokument_nd, 
								vrijeme_dodavanja_dokument_nd, 
								dodao_zaposlenik_dokument_nd
							)
						VALUES
							(
								:naziv_dokument_nd, 
								:naziv_dokument_ostali_nd, 
								:status_dokument_nd, 
								:id_kandidata_dokument_nd, 
								:vrijeme_dodavanja_dokument_nd, 
								:dodao_zaposlenik_dokument_nd
							)
					");

					$insertOstaliDipl->execute(array(
						':naziv_dokument_nd' => $file_name_new,
						':naziv_dokument_ostali_nd' => $nazivDokument,
						':status_dokument_nd' => NULL,
						':id_kandidata_dokument_nd' => $ir_kandidat_id,
						':vrijeme_dodavanja_dokument_nd' => date("Y-m-d H:i:s"),
						':dodao_zaposlenik_dokument_nd' => $logged_employee_id
					));
				//Unos dokumenta u ostale dokumente na DIPL-u END
				//Unos Loga da je unesen dokument u Ostale dokumente START
					$log_desc1 = "DIPL -> Dodan dokument u ostale dokumente za kandidata [".$ir_kandidat_id."] sa nazivom [".$nazivDokument."] i file-om [".$file_name_new."].";
					$log_date1 = date('Y-m-d H:i:s'); 
					$log_query1 = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date)
						VALUES
							(:log_employeeid, :log_desc, :log_date)
					");

					$log_query1->execute(array(
						':log_employeeid' => $logged_employee_id,
						':log_desc' => $log_desc1,
						':log_date' => $log_date1
					));
				//Unos Loga da je unesen dokument u Ostale dokumente END

				//Unos u tabelu nostrifikovane diplome START
					//Prvo treba izvrsiti provjeru da li je kandidat povezan START 
						$candJobId = NULL;
						$checkKandidat = $db->prepare("
							SELECT 
								kandidat_id
							FROM 
								idk_kandidati
							WHERE 
								kandidat_dipl_id = :kandidat_dipl_id
						");
						$checkKandidat->execute(array(
							":kandidat_dipl_id" => $ir_kandidat_id
						));
						if($checkKandidat->rowCount() == 1){
							$rowCheckKandidat = $checkKandidat->fetch();
							$candJobId = intval($rowCheckKandidat["kandidat_id"]);
						}else{
							$candJobId = NULL;
						}
					//Prvo treba izvrsiti provjeru da li je kandidat povezan END 
					//Unos u tabelu nostrifikovanih diploma START
					$add_new_query=$db->prepare("
						INSERT INTO idk_nostrifikovane_diplome 
						(
							id_cand_dipl,
							id_cand_job, 
							file_nd, 
							upload_date_nd, 
							upload_employee_id, 
							full_recognition
						) 
						VALUES 
						(
							:id_cand_dipl, 
							:id_cand_job, 
							:file_nd, 
							:upload_date_nd, 
							:upload_employee_id, 
							:full_recognition
						)
					");
					$add_new_query->execute(array(
						':id_cand_dipl'			=> $ir_kandidat_id,
						':id_cand_job'  		=> $candJobId,
						':file_nd'				=> "/".$file_destination,
						':upload_date_nd'		=> date('Y-m-d H:i:s'),
						':upload_employee_id'	=> $logged_employee_id,
						':full_recognition' 	=> $ir_type_vr
					));
					//Unos u tabelu nostrifikovanih diploma END
				//Unos u tabelu nostrifikovane diplome END
				//Poziv funkcije START 
					$log_automatsko_prebacivanje = "";
					try{
						$resultPrebacivanja = prebaciNaPrikupljanjeDokumentacije($ir_kandidat_id, 2);
					}catch (Exception $e){
						$resultPrebacivanja = $e->getMessage();
					}
					$log_automatsko_prebacivanje = "Status automatskog prebacivanja na prikupljanje dokumentacije: ".$resultPrebacivanja;
				//Poziv funkcije END 
				//Unos Loga da je unesen dokument u nostrifikovane diplome START
					$log_desc2 = "DIPL -> Dodan dokument u tabelu za nostrifikovane diplome za DIPL kandidata [".$ir_kandidat_id."] sa putanjom ["."/".$file_destination."]. ".$log_automatsko_prebacivanje;
					$log_date2 = date('Y-m-d H:i:s'); 
					$log_query2 = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date)
						VALUES
							(:log_employeeid, :log_desc, :log_date)
					");

					$log_query2->execute(array(
						':log_employeeid' => $logged_employee_id,
						':log_desc' => $log_desc2,
						':log_date' => $log_date2
					));
				//Unos Loga da je unesen dokument u nostrifikovane diplome END
				
				header("Location: " . getSiteURLr() . "nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$ir_kandidat_id);
			break;

			case "promjena_ugovora_INKASO":
				$id_kand = $_POST["ug_kandidat_id_IN"];
				$drzava_novi = $_POST["ug_drzava_id_IN"];
				$ugovor_novi = $_POST["vrsta_ugovora_id_IN"];
				$zaposlenik = $_POST["zaposlenik_prosli_id_IN"];
				
				//Ovaj query je postavljen cisto iz razloga provjere odredjenih informacija 
				//NPR: 	
				//		na kojem se statusu nalazi zaposlenik
				//		Trenutna vrsta ugovora i sl
				$query_provjera = $db->prepare("
					SELECT status_nd_kandidata, pstatus_nd_kandidata, vrsta_ugovora_nd_kandidata
					FROM idk_nd_kandidata
					WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
				");
				$query_provjera->execute(array(
					':id_broj_nd_kandidata' => $id_kand
				));
				
				if($query_provjera->rowCount() != 0){
					$row_provjera = $query_provjera->fetch();
					$status = $row_provjera["status_nd_kandidata"]; //trenutni status
					$pstatus = $row_provjera["pstatus_nd_kandidata"]; //trenutni podstatus
					$vrstaugovora = $row_provjera["vrsta_ugovora_nd_kandidata"]; //trenutna vrsta ugovora (ako se bude biljezilo u logove sa koje vrste na koju je prebacio)
					//ako se vec nalazi na statusu U obradi Lead, onda je samo potrebno promjeniti ugovor, predracun i ostalo (bez promjene odredjenog statusa)
					
					//Update Starih Ugovora, Uplatnica, Predracuna START
					//-----------------------------------------------------------------
						//Provjeravam funkcijom da li postoji vec izdat jedan predracun za tog kandidata - ako postoji - onda je to sigurno za prvu ratu i njega arhiviram
						if(getBrPredracunaNDKanR($id_kand) == 1){
							
							$query_stari_pr_id = $db->prepare("
								SELECT pr_id
								FROM idk_predracuni
								WHERE pr_kandidat_id = :pr_kandidat_id AND pr_rata = :pr_rata AND pr_status != 0
							");
							$query_stari_pr_id->execute(array(
								':pr_kandidat_id' => $id_kand,
								':pr_rata' => 1
							));
							$row_stari_pr_id = $query_stari_pr_id->fetch();
							$stari_pr_id = $row_stari_pr_id["pr_id"];
							
							//Update obracuna za stari predracun
							$update_obracune = $db->prepare("
								UPDATE idk_obracuni
								SET status_obracuna = :status_obracuna
								WHERE predracun_id = :predracun_id
							");
							$update_obracune->execute(array(
								':status_obracuna' => 2,
								':predracun_id' => $stari_pr_id
							));
							
							
							//Update starog predracuna - status arhiva
							$update_stari_predracun = $db->prepare("
								UPDATE idk_predracuni
								SET pr_status = :pr_status
								WHERE pr_rata = :pr_rata AND pr_kandidat_id = :pr_kandidat_id AND pr_status != 0
							");
							$update_stari_predracun->execute(array(
								':pr_status' => 0,
								':pr_rata' => 1,
								':pr_kandidat_id' => $id_kand
							));
						
						
							//Sad je potrebno staviti ugovor i uplatnicu iz dokumenata kao neaktivne dokumente
							
							$update_stari_ugovor = $db->prepare("
								UPDATE idk_nd_kandidata_dokumenti
								SET tip_dokumenta_status = :tip_dokumenta_status
								WHERE id_kandidata_dokument_nd = :id_kandidata_dokument_nd AND tip_dokumenta = :tip_dokumenta AND tip_dokumenta_status != 0
							");
							$update_stari_ugovor->execute(array(
								':tip_dokumenta_status' => 0,
								':id_kandidata_dokument_nd' => $id_kand,
								':tip_dokumenta' => 1
							));
							$update_stara_uplatnica = $db->prepare("
								UPDATE idk_nd_kandidata_dokumenti
								SET tip_dokumenta_status = :tip_dokumenta_status
								WHERE id_kandidata_dokument_nd = :id_kandidata_dokument_nd AND tip_dokumenta = :tip_dokumenta AND tip_dokumenta_status != 0
							");
							$update_stara_uplatnica->execute(array(
								':tip_dokumenta_status' => 0,
								':id_kandidata_dokument_nd' => $id_kand,
								':tip_dokumenta' => 2
							));
						}
						
					//-----------------------------------------------------------------
					//Update Starih Ugovora, Uplatnica, Predracuna END
					
					
					//Dodavanje Novih START
					//-----------------------------------------------------------------
						if($drzava_novi == 1){
							$drzava = "BiH";
						}else{
							$drzava = "Srbija";
						}
						$month = date('m');
						$day = date('d');
						$year = date('Y');
						$year_skr = date('y');
						
						
						
						//Radi se update za vrstu ugovora
						$vrsta_ugovora_x = $db->prepare("
							UPDATE idk_nd_kandidata
							SET vrsta_ugovora_nd_kandidata = :vrsta_ugovora_nd_kandidata
							WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
						");
						$vrsta_ugovora_x->execute(array( 
							':id_broj_nd_kandidata' => $id_kand,
							':vrsta_ugovora_nd_kandidata' => $ugovor_novi
						));
						
						$brojac_predracuna = createBrojPredracuna($drzava);
						$iznos_rate = getIznosRate($ugovor_novi, $drzava, "rata1");
						
						if($drzava == "Srbija"){
							$slovo_drz = "S";
							$domaca_valuta = "RSD";
							$rata_rsd = $iznos_rate;
							
							$ch = curl_init();
							// Disable SSL verification
							curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
							// Will return the response, if false it print the response
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							// Set the url
							$rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
							// var_dump($rls);
							// exit();
							curl_setopt($ch, CURLOPT_URL,$rls);
							curl_setopt($ch, CURLOPT_FAILONERROR, true); // Required for HTTP error codes to be reported via our call to curl_error($ch)
							// Execute
							$result=curl_exec($ch);
							
							curl_close($ch);

							$data = json_decode($result, TRUE);
							$EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
							$EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
							$RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
							$RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
							
							$EUR = str_replace(',', '.', $EUR1);
							$RSD = str_replace(',', '.', $RSD1);
							$rata_bam = ($rata_rsd / 100) * $RSD ;
							$rata_eur = $rata_bam / $EUR;
							
							$rata_bam_f = number_format((float)$rata_bam, 2, '.', '');
							$rata_eur_f = number_format((float)$rata_eur, 2, '.', '');
							$rata_rsd_f = number_format((float)$rata_rsd, 2, '.', '');
							
						}else{
							$slovo_drz = "B";
							$domaca_valuta = "BAM";
							$rata_bam = $iznos_rate;
							
							$ch = curl_init();
							// Disable SSL verification
							curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
							// Will return the response, if false it print the response
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							// Set the url
							$rls="https://www.cbbh.ba/CurrencyExchange/GetJson?date=".$month."%2F".$day."%2F".$year."%2000%3A00%3A00";
							curl_setopt($ch, CURLOPT_URL,$rls);
							// Execute
							$result=curl_exec($ch);
							curl_close($ch);

							$data = json_decode($result, TRUE);
							$EUR1 = $data['CurrencyExchangeItems'][0]['Middle'];
							$EUR2 = $data['CurrencyExchangeItems'][0]['Units'];
							$RSD1 = $data['CurrencyExchangeItems'][15]['Middle'];
							$RSD2 = $data['CurrencyExchangeItems'][15]['Units'];
							
							$EUR = str_replace(',', '.', $EUR1);
							$RSD = str_replace(',', '.', $RSD1);
							
							$rata_eur = $rata_bam / $EUR;
							$rata_rsd = 100*$rata_bam / $RSD;
							
							$rata_bam_f = number_format((float)$rata_bam, 2, '.', '');
							$rata_eur_f = number_format((float)$rata_eur, 2, '.', '');
							$rata_rsd_f = number_format((float)$rata_rsd, 2, '.', '');
							
						}
						$novi_predracun = "DIPL".$slovo_drz."-".$brojac_predracuna."-".$year_skr;
						$file_datum = date('YmdHis'); 
						$file_name = $file_datum."DIPL".$slovo_drz.$brojac_predracuna.".pdf";
						$insert_predracun = $db->prepare("	
									INSERT INTO idk_predracuni	
									(pr_broj_predracuna,  pr_kandidat_id, pr_zaposlenik, pr_vrsta_predracuna, pr_rata, pr_domaca_valuta, pr_vrijednost_BAM, pr_vrijednost_RSD, pr_vrijednost_EUR, pr_file)	
									VALUES	
									(:pr_broj_predracuna,:pr_kandidat_id,:pr_zaposlenik,:pr_vrsta_predracuna,:pr_rata,:pr_domaca_valuta,:pr_vrijednost_BAM,	:pr_vrijednost_RSD,:pr_vrijednost_EUR,:pr_file)	
									");	
						$insert_predracun->execute(array(	
									':pr_broj_predracuna' => $novi_predracun,	
									':pr_kandidat_id' => $id_kand,	
									':pr_zaposlenik' => $zaposlenik,	
									':pr_vrsta_predracuna' => 1,	
									':pr_rata' => 1,	
									':pr_domaca_valuta' => $domaca_valuta,	
									':pr_vrijednost_BAM' => $rata_bam_f,	
									':pr_vrijednost_RSD' => $rata_rsd_f,	
									':pr_vrijednost_EUR' => $rata_eur_f,	
									':pr_file' => $file_name
									));

						//Get last ID
						$predracun_id = $db->lastInsertId();
						
						if($drzava == "Srbija"){
							createPredracunSRB($predracun_id);
							$putanja_uplatnica = createUplatnicaSRB($predracun_id);
							$putanja_ugovor = createUgovorSRB($id_kand);
							//createInfoListSRB();
							sendMailPredracunUgovorSRB($file_name, $putanja_ugovor, $putanja_uplatnica, $id_kand);
						}
						else{
							createPredracunBIH($predracun_id);
							$putanja_uplatnica = createUplatnicaBIH($predracun_id);
							$putanja_ugovor = createUgovorBIH($id_kand);
							//fja za slanje maila za bih
							if($ugovor_novi == 11 OR $ugovor_novi == 12){
								sendMailPredracunUgovorBIH($file_name, $putanja_ugovor, "ne", $id_kand);
								viberKandidateMikrofinData($id_kand);
							}else{
								sendMailPredracunUgovorBIH($file_name, $putanja_ugovor, $putanja_uplatnica, $id_kand);
							}
						}
						//KREIRANJE NOVIH OBRACUNA
						ubaciObracune($predracun_id);
					
						$log_desc = "DIPL -> INKASO PROMJENA UGOVORA za kandidata ID = [".$id_kand."] sa ugovora BROJ = [".$vrstaugovora."] na ugovor BROJ = [".$ugovor_novi."].";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
							INSERT INTO idk_logs
								(log_employeeid, log_desc, log_date)
							VALUES
								(:log_employeeid, :log_desc, :log_date)
						");

						$log_query->execute(array(
							':log_employeeid' => $logged_employee_id,
							':log_desc' => $log_desc,
							':log_date' => $log_date
						));
					//-----------------------------------------------------------------
					//Dodavanje Novih END
					
					if($status == 1 AND $pstatus != 5){
						$vrijeme_stari_status = $db->prepare("
							SELECT id_log_status_nd_kandidata, vrijeme_promjene_statusa_nd_kandidata
							FROM idk_nd_kandidata_status_log
							WHERE idd_broj_nd_kandidata = :idd_broj_nd_kandidata AND broj_dana_statusa_nd_kandidata is null
						");
						$vrijeme_stari_status->execute(array(
							':idd_broj_nd_kandidata' => $id_kand
						));
						$vrijeme_stari_status_row = $vrijeme_stari_status->fetch();
						$id_log_statusa = $vrijeme_stari_status_row['id_log_status_nd_kandidata'];
						$vrijeme_log_statusa = $vrijeme_stari_status_row['vrijeme_promjene_statusa_nd_kandidata'];
						
						$trenutno_vrijeme_statusne_promjene = date('Y-m-d H:i:s');
						
						$diff_novi = strtotime($trenutno_vrijeme_statusne_promjene) - strtotime($vrijeme_log_statusa);
						$day_novi = floor($diff_novi/86400);
						
						$update_vrijeme_stari = $db->prepare("
										UPDATE idk_nd_kandidata_status_log
										SET broj_dana_statusa_nd_kandidata = :broj_dana_statusa_nd_kandidata
										WHERE idd_broj_nd_kandidata = :idd_broj_nd_kandidata AND id_log_status_nd_kandidata = :id_log_status_nd_kandidata");

						$update_vrijeme_stari->execute(array(
									':idd_broj_nd_kandidata' => $id_kand,
									':id_log_status_nd_kandidata' => $id_log_statusa,
									':broj_dana_statusa_nd_kandidata' => $day_novi
									));
									
						$new_ND_status = $db->prepare("
											INSERT INTO idk_nd_kandidata_status_log
											(
												idd_broj_nd_kandidata,
												status_nd_kandidata,
												pstatus_nd_kandidata,
												vrijeme_promjene_statusa_nd_kandidata,
												promjenio_zaposlenik_nd_kandidata
											)
											VALUES
											(
												:idd_broj_nd_kandidata,
												:status_nd_kandidata,
												:pstatus_nd_kandidata,
												:vrijeme_promjene_statusa_nd_kandidata,
												:promjenio_zaposlenik_nd_kandidata
											)
						");
						
						$new_ND_status->execute(array(
										':idd_broj_nd_kandidata' => $id_kand,
										':status_nd_kandidata' => 1,
										':pstatus_nd_kandidata' => 5,
										':vrijeme_promjene_statusa_nd_kandidata' => date('Y-m-d H:i:s'),
										':promjenio_zaposlenik_nd_kandidata' => $zaposlenik
										));
						//Update proslog statusa END
						$query_p_s = $db->prepare("
							UPDATE idk_nd_kandidata
							SET pstatus_nd_kandidata = :pstatus_nd_kandidata
							WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata");

						$query_p_s->execute(array(
									':id_broj_nd_kandidata' => $id_kand,
									':pstatus_nd_kandidata' => 5
									));

						/*
							Poziv funkcije za update projekcija
							*/
								updateProjekcijeKandidat(1, $id_kand);
							/*
							Poziv funkcije za update projekcija
						*/
					}
				}
				header("Location: " . getSiteURLr() . "nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$id_kand);
			break;
			
			case "resend_ugovor":
				$id_kandidata = $_GET["id"];
				if(isset($id_kandidata)){
					$query1 = $db->prepare("SELECT vrsta_ugovora_nd_kandidata FROM idk_nd_kandidata WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata");
					$query1->execute(array(':id_broj_nd_kandidata' => $id_kandidata));
					$query2 = $db->prepare("SELECT pr_domaca_valuta, pr_file FROM idk_predracuni WHERE pr_kandidat_id = :pr_kandidat_id AND pr_rata = 1 AND pr_status != 0");
					$query2->execute(array(':pr_kandidat_id' => $id_kandidata));
					$query_ugovor = $db->prepare("SELECT naziv_dokument_nd FROM idk_nd_kandidata_dokumenti WHERE id_kandidata_dokument_nd = :id_kandidata_dokument_nd AND tip_dokumenta = 1 AND tip_dokumenta_status = 1");
					$query_ugovor->execute(array(':id_kandidata_dokument_nd' => $id_kandidata));
					$query_uplatnica = $db->prepare("SELECT naziv_dokument_nd FROM idk_nd_kandidata_dokumenti WHERE id_kandidata_dokument_nd = :id_kandidata_dokument_nd AND tip_dokumenta = 2 AND tip_dokumenta_status = 1");
					$query_uplatnica->execute(array(':id_kandidata_dokument_nd' => $id_kandidata));
					
					$row1 = $query1->fetch();
					$row2 = $query2->fetch();
					$row_ugovor = $query_ugovor->fetch();
					$row_uplatnica = $query_uplatnica->fetch();
					
					$vrsta_ugovora = $row1["vrsta_ugovora_nd_kandidata"];
					$drzava = $row2["pr_domaca_valuta"];
					$file_name = $row2["pr_file"];
					$putanja_ugovor = $row_ugovor["naziv_dokument_nd"];
					$putanja_uplatnica = $row_uplatnica["naziv_dokument_nd"];
					
					//echo " Vrsta ugovora: ".$vrsta_ugovora." Drzava: ".$drzava." Predracun: ".$file_name." Ugovor: ".$putanja_ugovor." Uplatnica: ".$putanja_uplatnica." <br>";
					
					if($drzava == "RSD"){
						sendMailPredracunUgovorSRB($file_name, $putanja_ugovor, $putanja_uplatnica, $id_kandidata);
					}
					else{
						if($vrsta_ugovora == 11 OR $vrsta_ugovora == 12){
							sendMailPredracunUgovorBIH($file_name, $putanja_ugovor, "ne", $id_kandidata);
							viberKandidateMikrofinData($id_kandidata);
						}else{
							sendMailPredracunUgovorBIH($file_name, $putanja_ugovor, $putanja_uplatnica, $id_kandidata);
						}
					}
					$log_desc = "DIPL -> RESEND DOKUMENTI za kandidata ID = [".$id_kandidata."].";
					$log_date = date('Y-m-d H:i:s');

					$log_query = $db->prepare("
						INSERT INTO idk_logs
							(log_employeeid, log_desc, log_date)
						VALUES
							(:log_employeeid, :log_desc, :log_date)
					");

					$log_query->execute(array(
						':log_employeeid' => $logged_employee_id,
						':log_desc' => $log_desc,
						':log_date' => $log_date
					));
					//echo "POSLANO!";
					header("Location: " . getSiteURLr() . "nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$id_kandidata."&mess=1");
				}else{
					header("Location: " . getSiteURLr() . "nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$id_kandidata."&mess=2");
				}
			break;
			
			case "prebaci_iz_skladista":
			
				$id_skladiste = 139; //ID skladišta
				$team = $_GET["team"]; // Team -> 0 - zajednički, 1,2,3 ostali
				$id_zaposlenika_new = intval($_POST["select_agent".$team]); //Id zaposlenika kojem se prebacuje
				
				
				if(isset($_POST["select_razlog_nezainteresiran_lead".$team])){
					$razlog_nezainteresiran_lead = $_POST["select_razlog_nezainteresiran_lead".$team];
				}else 
					$razlog_nezainteresiran_lead = "0";
				
				
				if(isset($_POST["select_razlog_arhiva".$team])){
					$razlog_arhiva = $_POST["select_razlog_arhiva".$team];
				}else 
					$razlog_arhiva = "0";
				

				$team_zaposlenika_new = getTeamIdByEmployee($id_zaposlenika_new);
				
				$drzava = $_POST["select_drzava".$team]; //Drzava: bih, srb, de, ostalo, NULL(ako bude potrebe za prebacivanjem ukupnih iz nekog statusa)
				$status = $_POST["select_statusi".$team] ; //Statusu koje POSTAS iz svog selecta
				
				$broj = array();
				// status = (11,16,12,13,14,15,2,3,4,5,6,7);
				//			 |  |  |  |  |  |  | | | | | |
				// broj =   (14,12,24,50,49,30,4,9,8,3,2,1);
				if(in_array(11, $status)){
					array_push($broj, $_POST["status_lead_".$team]);
				}
				if(in_array(16, $status)){
					array_push($broj, $_POST["status_nk1_".$team]);
				}
				if(in_array(12, $status)){
					array_push($broj, $_POST["status_nk3_".$team]);
				}
				if(in_array(13, $status)){
					array_push($broj, $_POST["status_zaint_".$team]);
				}
				if(in_array(14, $status)){
					array_push($broj, $_POST["status_nez_lead_".$team]);
				}
				if(in_array(15, $status)){
					array_push($broj, $_POST["status_obr_lead".$team]);
				}
				if(in_array(2, $status)){
					array_push($broj, $_POST["status_prik_dok".$team]);
				}
				if(in_array(3, $status)){
					array_push($broj, $_POST["status_posl_posta".$team]);
				}
				if(in_array(4, $status)){
					array_push($broj, $_POST["status_uobradi".$team]);
				}
				if(in_array(5, $status)){
					array_push($broj, $_POST["status_dop_dok".$team]);
				}
				if(in_array(6, $status)){
					array_push($broj, $_POST["status_zavrsen".$team]);
				}
				if(in_array(7, $status)){
					array_push($broj, $_POST["status_arhiv".$team]);
				}
				if(in_array(17, $status)){
					array_push($broj, $_POST["status_neus_lead1_".$team]);
				}
				if(in_array(18, $status)){
					array_push($broj, $_POST["status_neus_lead2_".$team]);
				}
				if(in_array(19, $status)){
					array_push($broj, $_POST["status_termin_znt_".$team]);
				}
				if(in_array(110, $status)){
					array_push($broj, $_POST["status_termin_ost_".$team]);
				}
				if(in_array(111, $status)){
					array_push($broj, $_POST["status_lead_nl_".$team]);
				}
				if(in_array(112, $status)){
					array_push($broj, $_POST["status_lead_nz_".$team]);
				}
				
				$uslov_drzava = "";
				$drzava_pozivni = "";
				if($drzava != NULL){
					if($drzava != "srb" AND $drzava != "bih" AND $drzava != "de"){
						$uslov_drzava = " ( kan.mobilni_nd_kandidata NOT LIKE '+381%' AND kan.mobilni_nd_kandidata NOT LIKE '+387%' AND kan.mobilni_nd_kandidata NOT LIKE '+49%' OR kan.mobilni_nd_kandidata is null ) ";
					}else{
						if($drzava == "bih"){
							$drzava_pozivni = "+387";
						}else if($drzava == "srb"){
							$drzava_pozivni = "+381";
						}else if($drzava == "de"){
							$drzava_pozivni = "+49";
						}
						$uslov_drzava = " kan.mobilni_nd_kandidata LIKE '".$drzava_pozivni."%' ";
					}
				}else{
					$uslov_drzava = " kan.id_broj_nd_kandidata != 0 ";
				}
				$razlozi_uslov_prvi = "";
				$razlozi_uslov_drugi = "";
				$nizKandidataLog = array();
				foreach($status AS $x => $val_status){
					$status_uslov = "";
					if($val_status == 11){
						$status_uslov = " kan.status_nd_kandidata = 1 AND kan.pstatus_nd_kandidata = 1 ";
					}else if($val_status == 12){
						$status_uslov = " kan.status_nd_kandidata = 1 AND kan.pstatus_nd_kandidata = 2 ";
					}else if($val_status == 13){
						$status_uslov = " kan.status_nd_kandidata = 1 AND kan.pstatus_nd_kandidata = 3 ";
					}else if($val_status == 14){
						$status_uslov = " kan.status_nd_kandidata = 1 AND kan.pstatus_nd_kandidata = 4 ";
						if(intval($razlog_nezainteresiran_lead)>=0){
							$query_get_biljeska_ids_uslov = $db->prepare("
								SELECT max(sqbil.id_biljeska_nd) 
								FROM idk_nd_kandidata_biljeske sqbil 
								JOIN idk_nd_kandidata kan 
								ON kan.id_broj_nd_kandidata = sqbil.id_kandidata_biljeska_nd 
								WHERE sqbil.tip_biljeska_nd = 3 
								AND sqbil.status_biljeska_nd = 2 
								AND ".$uslov_drzava."
								AND kan.status_nd_kandidata = 1 
								AND kan.pstatus_nd_kandidata = 4 
								AND kan.tim_nd_kandidata = :team
								AND kan.zaduzen_zaposlenik_nd_kandidata = 139 
								GROUP BY sqbil.id_kandidata_biljeska_nd
							");

							$query_get_biljeska_ids_uslov -> execute(array(
								':team' => $team
							));
							$biljeska_ids_uslov = array();
							while($row_get_biljeska_ids_uslov = $query_get_biljeska_ids_uslov->fetch()){
								array_push($biljeska_ids_uslov, $row_get_biljeska_ids_uslov['max(sqbil.id_biljeska_nd)']);
							}
							$biljeska_ids_uslov = implode(",",$biljeska_ids_uslov);
							$razlozi_uslov_prvi = "
								JOIN idk_nd_kandidata_biljeske bil
								ON kan.id_broj_nd_kandidata = bil.id_kandidata_biljeska_nd
								JOIN idk_ro_usluge ro
								ON ro.id_ro = bil.razlog_biljeska_nd
							";
							$razlozi_uslov_drugi = "
								AND bil.id_biljeska_nd IN (
										".$biljeska_ids_uslov."
									)
								AND ro.id_ro = ".$razlog_nezainteresiran_lead."
							";
						}
						else{
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
								AND '.$uslov_drzava.'
								AND kan.zaduzen_zaposlenik_nd_kandidata = 139
								GROUP BY kan.id_broj_nd_kandidata
							');
							$query_get_biljeske_id -> execute(array(':tim_nd_kandidata' => $team));
							$biljeske_ids = array();
							$nezainteresiran_lead_sa_biljeskama_ids = array();
							while($row_get_biljeske_id = $query_get_biljeske_id -> fetch()){
								array_push($nezainteresiran_lead_sa_biljeskama_ids, $row_get_biljeske_id['id_broj_nd_kandidata']);
							}
							$query_get_all_nezainteresiran_lead_kandidati_ids = $db->prepare('
								SELECT kan.id_broj_nd_kandidata
								FROM idk_nd_kandidata kan
								WHERE kan.status_nd_kandidata = 1
								AND kan.pstatus_nd_kandidata = 4
								AND kan.tim_nd_kandidata = :tim_nd_kandidata
								AND kan.zaduzen_zaposlenik_nd_kandidata = 139
								AND '.$uslov_drzava.'
							');
							
							$query_get_all_nezainteresiran_lead_kandidati_ids -> execute(array(':tim_nd_kandidata' => $team));
							$svi_nezainteresiran_lead_kandidati_ids = array();
							while($row_get_all_nezainteresiran_lead_kandidati_ids = $query_get_all_nezainteresiran_lead_kandidati_ids->fetch()){
								array_push($svi_nezainteresiran_lead_kandidati_ids, $row_get_all_nezainteresiran_lead_kandidati_ids['id_broj_nd_kandidata']);
							}
							$array_razlika_ids = array_diff($svi_nezainteresiran_lead_kandidati_ids, $nezainteresiran_lead_sa_biljeskama_ids);


							$array_razlika_ids = implode(',',$array_razlika_ids);
							$razlozi_uslov_drugi = "
								AND kan.id_broj_nd_kandidata IN (
										".$array_razlika_ids."
									)
								";
						}
					}else if($val_status == 15){
						$status_uslov = " kan.status_nd_kandidata = 1 AND kan.pstatus_nd_kandidata = 5 ";
					}else if($val_status == 16){
						$status_uslov = " kan.status_nd_kandidata = 1 AND kan.pstatus_nd_kandidata = 6 ";
					}else if($val_status == 17){
						$status_uslov = " kan.status_nd_kandidata = 1 AND kan.pstatus_nd_kandidata = 7 ";
					}else if($val_status == 18){
						$status_uslov = " kan.status_nd_kandidata = 1 AND kan.pstatus_nd_kandidata = 8 ";
					}else if($val_status == 19){
						$status_uslov = " kan.status_nd_kandidata = 1 AND kan.pstatus_nd_kandidata = 9 ";
					}else if($val_status == 110){
						$status_uslov = " kan.status_nd_kandidata = 1 AND kan.pstatus_nd_kandidata = 10 ";
					}else if($val_status == 111){
						$status_uslov = " kan.status_nd_kandidata = 1 AND kan.pstatus_nd_kandidata = 11 ";
					}else if($val_status == 112){
						$status_uslov = " kan.status_nd_kandidata = 1 AND kan.pstatus_nd_kandidata = 12 ";
					}else if($val_status == 2){
						$status_uslov = " kan.status_nd_kandidata = 2 ";
					}else if($val_status == 3){
						$status_uslov = " kan.status_nd_kandidata = 3 ";
					}else if($val_status == 4){
						$status_uslov = " kan.status_nd_kandidata = 4 ";
					}else if($val_status == 5){
						$status_uslov = " kan.status_nd_kandidata = 5 ";
					}else if($val_status == 6){
						$status_uslov = " kan.status_nd_kandidata = 6 ";
					}else if($val_status == 7){
						$status_uslov = " kan.status_nd_kandidata = 7 ";
						$query_get_max_biljeske = $db->prepare('
							SELECT max(bilj.id_biljeska_nd) as max_biljeska
							FROM idk_nd_kandidata_biljeske bilj
							JOIN idk_nd_kandidata kan
							ON kan.id_broj_nd_kandidata = bilj.id_kandidata_biljeska_nd
							WHERE kan.status_nd_kandidata = 7
							AND kan.zaduzen_zaposlenik_nd_kandidata = 139
							AND '.$uslov_drzava.'
							AND kan.tim_nd_kandidata = '.$team.'
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
							SELECT bilj.id_biljeska_nd
							FROM idk_nd_kandidata_biljeske bilj
							WHERE bilj.status_biljeska_nd = 2
							AND bilj.tip_biljeska_nd = 3
							AND bilj.razlog_biljeska_nd = '.$razlog_arhiva.'
							AND bilj.id_biljeska_nd IN ('.$string_max_biljeske.')
						');
						// var_dump($query_get_3_2_biljeske);
						// exit();
						
						$query_get_3_2_biljeske -> execute();
						$array_biljeske_kandidata_za_prebacivanje = array();
						while($row_get_3_2_biljeske = $query_get_3_2_biljeske->fetch()){
							array_push($array_biljeske_kandidata_za_prebacivanje, $row_get_3_2_biljeske['id_biljeska_nd']);
						}
						
						$biljeska_ids_uslov = implode(',',$array_biljeske_kandidata_za_prebacivanje);
							
						$razlozi_uslov_prvi = "
							JOIN idk_nd_kandidata_biljeske bil
							ON kan.id_broj_nd_kandidata = bil.id_kandidata_biljeska_nd
							JOIN idk_ro_usluge ro
							ON ro.id_ro = bil.razlog_biljeska_nd
						";
						$razlozi_uslov_drugi = "
							AND bil.id_biljeska_nd IN (
									".$biljeska_ids_uslov."
								)
							AND ro.id_ro = ".$razlog_arhiva."
						";
					
					}else{
						$status_uslov = " kan.id_broj_nd_kandidata is not null ";
					}
					$glavni_query = "";
					//prebacivanje iz skladišta jednog tima u skladište drugog START
					if($id_zaposlenika_new < 0){
						
						$id_novi_team = $id_zaposlenika_new * (-1);
						$niz_kand=array();
						$query_get_kandidate = $db->prepare('
							SELECT kan.id_broj_nd_kandidata 
							FROM idk_nd_kandidata as kan
							 '.$razlozi_uslov_prvi.' 
							WHERE '.$uslov_drzava.' AND '.$status_uslov.' AND kan.tim_nd_kandidata = '.$team.'
							 '.$razlozi_uslov_drugi.' 
							AND kan.zaduzen_zaposlenik_nd_kandidata = '.$id_skladiste.'
							ORDER BY kan.id_broj_nd_kandidata ASC
							LIMIT '.$broj[$x]
						);
						// var_dump($query_get_kandidate);
						// exit();
						$query_get_kandidate->execute();

						while($row_get_kandidate = $query_get_kandidate->fetch()){
							array_push($niz_kand,$row_get_kandidate['id_broj_nd_kandidata']);
							array_push($nizKandidataLog,$row_get_kandidate['id_broj_nd_kandidata']);
						}

						$uslov_kandidati=implode(",",$niz_kand);
						$query_set_kandidate = $db->prepare('
							UPDATE idk_nd_kandidata
							SET tim_nd_kandidata = '.$id_novi_team.'
							WHERE 
							tim_nd_kandidata = '.$team.'
							AND 
							id_broj_nd_kandidata IN ('.$uslov_kandidati.')
						');
						// var_dump($query_set_kandidate);
						// exit();
						$query_set_kandidate->execute();
						
						
					}
					//prebacivanje iz skladišta jednog tima u skladište drugog END
					else{
						$glavni_query = "
							SELECT kan.id_broj_nd_kandidata, kan.status_nd_kandidata, kan.pstatus_nd_kandidata
							FROM idk_nd_kandidata kan 
							".$razlozi_uslov_prvi."
							WHERE 
							".$uslov_drzava." 
							AND 
							".$status_uslov." 
							".$razlozi_uslov_drugi."
							AND 
							kan.zaduzen_zaposlenik_nd_kandidata = :zaposlenik 
							AND 
							kan.tim_nd_kandidata = ".$team." 
							ORDER BY kan.id_broj_nd_kandidata  ASC
							LIMIT ".$broj[$x]."
						";

						$query = $db->prepare("
							".$glavni_query."
						");
						// var_dump($glavni_query);
						// exit();
						
						$query->execute(array(':zaposlenik' => $id_skladiste));
						
						while($row = $query->fetch()){
							$id_broj_kandida = $row["id_broj_nd_kandidata"];
							if($row['status_nd_kandidata'] == 7){
								promjenaStatusaDIPLKandidat($id_broj_kandida, 1, 12, 0);
							}
							array_push($nizKandidataLog, $id_broj_kandida);
							//echo $id_broj_kandida."<br/>";
							//_______________ START Prema bazi inserti i update ______________
								$query_provjera = $db->prepare("SELECT zaduzen_zaposlenik_nd_kandidata
																FROM idk_nd_kandidata
																WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata");
								$query_provjera->execute(array(':id_broj_nd_kandidata' => $id_broj_kandida));
								$row_provjera = $query_provjera->fetch();
								$men_prov = $row_provjera["zaduzen_zaposlenik_nd_kandidata"];
								if(intval($men_prov) == intval($id_skladiste)){
									//Unos novog menadzera START
									$query_update_men = $db->prepare("
										UPDATE idk_nd_kandidata
										SET zaduzen_zaposlenik_nd_kandidata = :zaduzen_zaposlenik_nd_kandidata, tim_nd_kandidata = :tim_nd_kandidata
										WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
									");

									$query_update_men->execute(array(
										':id_broj_nd_kandidata' => $id_broj_kandida,
										':zaduzen_zaposlenik_nd_kandidata' => $id_zaposlenika_new,
										':tim_nd_kandidata' => $team_zaposlenika_new
									));
									//Unos novog menadzera END
									
									//Update statistike menadzera START
									$insert_stat_men = $db->prepare("
										INSERT INTO idk_nd_menadzeri_statistike
										(idd_broj_nd_kandidata, zaduzen_zaposlenik_id, prethodni_zaposlenik_id, vrsta_aktivnosti, vrijeme_aktivnosti)
										VALUES
										(:idd_broj_nd_kandidata, :zaduzen_zaposlenik_id, :prethodni_zaposlenik_id, :vrsta_aktivnosti, :vrijeme_aktivnosti)
									");

									$insert_stat_men->execute(array(
										':idd_broj_nd_kandidata' => $id_broj_kandida,
										':zaduzen_zaposlenik_id' => $id_zaposlenika_new,
										':prethodni_zaposlenik_id' => $id_skladiste,
										':vrsta_aktivnosti' => 1,
										':vrijeme_aktivnosti' => date('Y-m-d H:i:s')
									));
								}
							//_______________ END Prema bazi inserti i update   ______________
						}						
					}

				}
				//echo "<br> ".implode(", ",$nizKandidataLog);
				
				//unošenje logova za prebacivanje kandidata iz skladišta jednog tima u skladište drugog START
				if($id_zaposlenika_new<0){
					$log_kand=implode(",",$nizKandidataLog);
					$id_novi_team = $id_zaposlenika_new * (-1);
					$log_desc = "Prebacuje kandidate (".$log_kand.") iz skladišta tima ".$team." u skladište tima ".$id_novi_team;
					$log_date = date('y-m-d h:i:s');

					$log_query = $db->prepare("
						insert into idk_logs
							(log_employeeid, log_desc, log_date)
						values
							(".$logged_employee_id.", '".$log_desc."', '".$log_date."')
					");
					
					$log_query->execute();
					
				}
				//unošenje logova za prebacivanje kandidata iz skladišta jednog tima u skladište drugog END
				else{
					$log_kand=implode(",",$nizKandidataLog);	
					$log_desc = "dipl -> prebačen/i kandidat/i id = [".$log_kand."] iz skladišta zaposleniku id = [".$id_zaposlenika_new."]";
					// $log_desc = "dipl -> prebačen/i kandidat/i id = [".implode(", ",$nizkandidatalog)."] iz skladišta zaposleniku id = [".$id_zaposlenika_new."]";
					$log_date = date('y-m-d h:i:s');

					$log_query = $db->prepare("
						insert into idk_logs
							(log_employeeid, log_desc, log_date)
						values
							(:log_employeeid, :log_desc, :log_date)
					");

					$log_query->execute(array(
						':log_employeeid' => $logged_employee_id,
						':log_desc' => $log_desc,
						':log_date' => $log_date
					));
					
				}
				header("location: " . getsiteurlr() . "dipl_agenti?page=open_skladiste");
			break;
			
			case "dodaj_agenta":
				
				$id_zaposlenik = intval($_POST["select_agent"]);
				$drzave = implode(",", $_POST["select_drzava"]);
				$dnevni = intval($_POST["dnevni_limit"]);
				$sedmicni = intval($_POST["sedmicni_limit"]);
				$mjesecni = intval($_POST["mjesecni_limit"]);
				// var_dump($id_zaposlenik);
				// var_dump($drzave);
				// var_dump($dnevni);
				// var_dump($sedmicni);
				// var_dump($mjesecni);
				// exit();
				
				$query_insert = $db->prepare("
					INSERT INTO idk_nd_limiti
						(lt_emp_id, lt_drzava, lt_dnevni, lt_sedmicni, lt_mjesecni)
					VALUES
						(:lt_emp_id, :lt_drzava, :lt_dnevni, :lt_sedmicni, :lt_mjesecni)
				");
				
				$query_insert->execute(array(
					':lt_emp_id' => $id_zaposlenik,
					':lt_drzava' => $drzave,
					':lt_dnevni' => $dnevni,
					':lt_sedmicni' => $sedmicni,
					':lt_mjesecni' => $mjesecni
				));
				
				if(getZaposlenikDiplR($id_zaposlenik) != 1){
					$query_update = $db->prepare("
						UPDATE idk_employees
						SET employee_nostrifikacija_diploma = :employee_nostrifikacija_diploma
						WHERE employee_id = :employee_id
					");

					$query_update->execute(array(
						':employee_id' => $id_zaposlenik,
						':employee_nostrifikacija_diploma' => 1
					));
				}
				
				$log_desc = "DIPL -> Uključen zaposlenik ID = [".$id_zaposlenik."] u DIPL. ";
				$log_date = date('Y-m-d H:i:s');

				$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)
				");

				$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date
				));
				header("Location: " . getSiteURLr() . "dipl_agenti?page=list");
			break;
			
			case "aktiviraj_deaktiviraj":
				$id_zaposlenik = $_GET["id_zap"];
				$id_akcija = $_GET["action"];
				$akcija_log = "";
				$trenutno = 10;
				if($id_akcija == 0){
					$trenutno = 1;
					$akcija_log = " deakviran iz DIPL-a";
				}else{
					$trenutno = 0;
					$akcija_log = " aktiviran u DIPL";
				}
				//id_akcija ->0 dekativiraj ->1 aktiviraj
				if(getZaposlenikDiplR($id_zaposlenik) == $trenutno){
					$query_update = $db->prepare("
						UPDATE idk_employees
						SET employee_nostrifikacija_diploma = :employee_nostrifikacija_diploma
						WHERE employee_id = :employee_id
					");

					$query_update->execute(array(
						':employee_id' => $id_zaposlenik,
						':employee_nostrifikacija_diploma' => $id_akcija
					));
				}
				$log_desc = "DIPL -> Zaposlenik ID = [".$id_zaposlenik."] je ".$akcija_log.". ";
				$log_date = date('Y-m-d H:i:s');

				$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)
				");

				$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date
				));
				header("Location: " . getSiteURLr() . "dipl_agenti?page=list");
			break;
			
			case "aktiviraj_deaktiviraj_RU":
				$id_RO = $_GET["id_RO"];
				$action = $_GET["action"];
				$akcija_log = "";
				if($action == 0){
					$akcija_log = " isključen";
				}else{
					$akcija_log = " uključen";
				}
				//action ->0 iskljuci ->1 ukljuci
				$query_update = $db->prepare("
					UPDATE idk_ro_usluge
					SET status_ro = :status_ro
					WHERE id_ro = :id_ro
				");

				$query_update->execute(array(
					':id_ro' => $id_RO,
					':status_ro' => $action
				));
				$log_desc = "DIPL -> Razlog odbijanja ugovora ID = [".$id_RO."] je ".$akcija_log.". ";
				$log_date = date('Y-m-d H:i:s');

				$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)
				");

				$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date
				));
				header("Location: " . getSiteURLr() . "dipl?page=pregledDIPL&type=1");
			break; 
			
			case "add_preporuka":
				$t = intval($_POST["t"]);
				if($t == 1){
					$kamp = 100;
				}else{
					$kamp = 157;
				}
				$kandidat_ime = $_POST["ime_new"];
				$kandidat_prezime = $_POST["prezime_new"];
				$kandidat_broj_tel = $_POST["mobilni_new"];
				
				$provjera_dipl = $db->prepare("
					SELECT id_broj_nd_kandidata, zaduzen_zaposlenik_nd_kandidata, chat_slike
					FROM idk_nd_kandidata
					WHERE mobilni_nd_kandidata = :mobilni_nd_kandidata AND mobilni_nd_kandidata != ''
				");
				$provjera_dipl->execute(array(
					":mobilni_nd_kandidata" => $kandidat_broj_tel
				));
				$dipl_da_ne = $provjera_dipl->rowCount();
				$provjera_dipl_row = $provjera_dipl->fetch();
				$kandidat_id_vec_u_dipl = $provjera_dipl_row['id_broj_nd_kandidata'];
				$zaduzen_id_vec_u_dipl = $provjera_dipl_row['zaduzen_zaposlenik_nd_kandidata'];
				$chat_slike = $provjera_dipl_row['chat_slike'];
				if($chat_slike != NULL){
					$chat_slike_explode = explode(",",$chat_slike);
				}else{
					$chat_slike_explode = array();
				}

				if($dipl_da_ne != 0){ 
					if($t == 1){
						$file_names = array();

						if(count($chat_slike_explode) != 0){
							foreach($chat_slike_explode AS $value_chat_slike){
								array_push($file_names, $value_chat_slike);
							}
						}

						for($i = 0; $i < count($_FILES['chat_image']['name']); $i++) {
							$tmpFilePath = $_FILES['chat_image']['tmp_name'][$i];
							if ($tmpFilePath != ""){
								$filename = $_FILES['chat_image']['name'][$i];
								$file_ext = explode('.', $filename);
								$file_ext = strtolower(end($file_ext));
								$file_name_new = uniqid() . '.' . $file_ext;
								$newFilePath = "files/ND_facebook_instagram/" . $file_name_new;
								
								if(move_uploaded_file($tmpFilePath, $newFilePath)) {
									array_push($file_names,$file_name_new);
								}
							}
						}

						$file_names_s = implode(",", $file_names);

						$chat_sl = $db->prepare("
							UPDATE idk_nd_kandidata 
							SET chat_slike = :chat_slike
							WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
						");

						$chat_sl->execute(array(
							':chat_slike' => $file_names_s,
							':id_broj_nd_kandidata' => $kandidat_id_vec_u_dipl
						));
					}
				}
				if($dipl_da_ne == 0){
					//Ako se kandidat ne nalazi u diplu
					//Update broja prijavljenih preko kampanje
					$query_upd = $db->prepare("
						UPDATE idk_kampanje_dipl
						SET kd_broj_prijavljenih = kd_broj_prijavljenih + 1
						WHERE kd_id = :kd_id
					");

					$query_upd->execute(array(
						':kd_id' => $kamp
					));
					
					$menager = getDodjeliAgentuDIPLR($kandidat_broj_tel, $kamp);
					//REZULTAT ALGORITMA 
					$zadnji_menager = $menager['stari'];
					$novi_menager = $menager['novi'];
					$novi_menager_team = getTeamIdByEmployee($novi_menager);
					
					$ime_new_ND_cand1 = $kandidat_ime;
					$prezime_new_ND_cand1 = $kandidat_prezime;
					$email_new_ND_cand1 = NULL;
					$mobilni_new_ND_cand1 = $kandidat_broj_tel;
					
					//UPDATE kandidata u Dipl
					$new_ND_kandidat = $db->prepare("
						INSERT INTO idk_nd_kandidata
							(
								ime_nd_kandidata, 
								prezime_nd_kandidata,
								mobilni_nd_kandidata, 
								email_nd_kandidata, 
								vrijeme_kreiranja_nd_kandidata,
								dodao_zaposlenik_nd_kandidata,
								zaduzen_zaposlenik_nd_kandidata, 
								tim_nd_kandidata, 
								status_nd_kandidata,
								povijest_nd_kandidata,
								povijest_vrsta_nd_kandidata,
								kampanja_id
							)
						VALUES
							(
								:ime_nd_kandidata, 
								:prezime_nd_kandidata,
								:mobilni_nd_kandidata, 
								:email_nd_kandidata, 
								:vrijeme_kreiranja_nd_kandidata,
								:dodao_zaposlenik_nd_kandidata,
								:zaduzen_zaposlenik_nd_kandidata, 
								:tim_nd_kandidata, 
								:status_nd_kandidata,
								:povijest_nd_kandidata,
								:povijest_vrsta_nd_kandidata,
								:kampanja_id
					)");

					$new_ND_kandidat->execute(array(
						':ime_nd_kandidata' => $ime_new_ND_cand1,
						':prezime_nd_kandidata' => $prezime_new_ND_cand1,
						':mobilni_nd_kandidata' => $mobilni_new_ND_cand1,
						':email_nd_kandidata' => $email_new_ND_cand1,
						':vrijeme_kreiranja_nd_kandidata' => date('Y-m-d H:i:s'),
						':dodao_zaposlenik_nd_kandidata' => $logged_employee_id,
						':zaduzen_zaposlenik_nd_kandidata' => $novi_menager,
						':tim_nd_kandidata' => $novi_menager_team,
						':status_nd_kandidata' => 1,
						':povijest_nd_kandidata' => 5,
						':povijest_vrsta_nd_kandidata' => 1,
						':kampanja_id' => $kamp
					));
					//Get last ID
					
					$kandidat_id_nd = $db->lastInsertId();
					if($t == 1){
						$file_names = array();
						for($i = 0; $i < count($_FILES['chat_image']['name']); $i++) {
							$tmpFilePath = $_FILES['chat_image']['tmp_name'][$i];
							if ($tmpFilePath != ""){
								$filename = $_FILES['chat_image']['name'][$i];
								$file_ext = explode('.', $filename);
								$file_ext = strtolower(end($file_ext));
								$file_name_new = uniqid() . '.' . $file_ext;
								$newFilePath = "files/ND_facebook_instagram/" . $file_name_new;
								
								if(move_uploaded_file($tmpFilePath, $newFilePath)) {
									$file_names[] = $file_name_new;
								}
							}
						}
						$file_names_s = implode(",", $file_names);
						$chat_sl = $db->prepare("
							UPDATE idk_nd_kandidata 
							SET chat_slike = :chat_slike
							WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
						");

						$chat_sl->execute(array(
							':chat_slike' => $file_names_s,
							':id_broj_nd_kandidata' => $kandidat_id_nd
						));
					}
					//Update statistike menadzera START
					// vrsta_aktivnosti == 0 -> oznacava da je sistem automatski dodjelio tom menadzeru kandidata
					$insert_statistike_menagera = $db->prepare("
						INSERT INTO idk_nd_menadzeri_statistike
							(idd_broj_nd_kandidata, zaduzen_zaposlenik_id, vrsta_aktivnosti, vrijeme_aktivnosti)
						VALUES
							(:idd_broj_nd_kandidata, :zaduzen_zaposlenik_id, :vrsta_aktivnosti, :vrijeme_aktivnosti)
					");

					$insert_statistike_menagera->execute(array(
						':idd_broj_nd_kandidata' => $kandidat_id_nd,
						':zaduzen_zaposlenik_id' => $novi_menager,
						':vrsta_aktivnosti' => 0,
						':vrijeme_aktivnosti' => date('Y-m-d H:i:s')
					));
	
					//Update log statusa ND kandidata
					$new_ND_status = $db->prepare("
						INSERT INTO idk_nd_kandidata_status_log
						(
							idd_broj_nd_kandidata,
							status_nd_kandidata,
							vrijeme_promjene_statusa_nd_kandidata
						)
						VALUES
						(
							:idd_broj_nd_kandidata,
							:status_nd_kandidata,
							:vrijeme_promjene_statusa_nd_kandidata
						)
					");
					$new_ND_status->execute(array(
						':idd_broj_nd_kandidata' => $kandidat_id_nd,
						':status_nd_kandidata' => 1,
						':vrijeme_promjene_statusa_nd_kandidata' => date('Y-m-d H:i:s')
					));
	
					//ADD TO LOGS START
						$log_date = date('Y-m-d H:i:s');
						$log_desc = "DIPL -> Dodan novi kandidat sa ID = [".$kandidat_id_nd."] preko forme >facebook/instagram - inbound< kandidati - Kampanja ID = [".$kamp."] - Zadužen zaposlenik: ".$novi_menager." ";
						$log_query = $db->prepare("
							INSERT INTO idk_logs 
								(log_employeeid, log_desc, log_date)
							VALUES
								(:log_employeeid, :log_desc, :log_date)
						");

						$log_query->execute(array(
							':log_employeeid' => $logged_employee_id,
							':log_desc' => $log_desc,
							':log_date' => $log_date
						));
					//ADD TO LOGS END
	
					//Provjera da li se nalazi u kandidatima
					$provjera_kandidati = $db->prepare("
						SELECT kandidat_id
						FROM idk_kandidati
						WHERE kandidat_mobitel = :kandidat_mobitel AND kandidat_status != :kandidat_status
					");
					$provjera_kandidati->execute(array(
						":kandidat_mobitel" => $kandidat_broj_tel,
						":kandidat_status" => 3
					));
					$kandidati_da_ne = $provjera_kandidati->rowCount();
					$provjera_kandidati_row = $provjera_kandidati->fetch();
					$kandidat_id_vec_u_kan = $provjera_kandidati_row['kandidat_id'];
					if($kandidati_da_ne == 0){
						//DODAVANJE U TABELU IDK_KANDIDATI
						
						$kandidat_check = md5(uniqid(rand(), true));
		
						//Add user to db
						$query_add_user = $db->prepare("
							INSERT INTO idk_kandidati
								(kandidat_check, kandidat_ime, kandidat_prezime, kandidat_email, kandidat_mobitel, kandidat_slika, kandidat_status, kandidat_status_messenger, kandidat_status_prijave, kandidat_datetime, kandidat_visitedurl, kandidat_prijava_na, kandidat_group, povezan_na_dipl, kandidat_porijeklo, kandidat_dipl_id )
							VALUES
								(:kandidat_check, :kandidat_ime, :kandidat_prezime, :kandidat_email, :kandidat_mobitel, :kandidat_slika, :kandidat_status, :kandidat_status_messenger, :kandidat_status_prijave, :kandidat_datetime, :kandidat_visitedurl, :kandidat_prijava_na, :kandidat_group, :povezan_na_dipl, :kandidat_porijeklo, :kandidat_dipl_id)
						");
				
						$query_add_user->execute(array(
							':kandidat_check' => $kandidat_check,
							':kandidat_ime' => $ime_new_ND_cand1,
							':kandidat_prezime' => $prezime_new_ND_cand1,
							':kandidat_email' => $email_new_ND_cand1,
							':kandidat_mobitel' => $mobilni_new_ND_cand1,
							':kandidat_slika' => "none",
							':kandidat_status' => 0,
							':kandidat_status_messenger' => 1,
							':kandidat_status_prijave' => 1,
							':kandidat_datetime' => date('Y-m-d H:i:s'),
							':kandidat_visitedurl' => 1,
							':kandidat_prijava_na' => "Ostalo",
							':kandidat_group' => 7,
							':povezan_na_dipl' => 1,
							':kandidat_porijeklo' => 4,
							':kandidat_dipl_id' => $kandidat_id_nd
						));
						
						$kandidat_id = $db->lastInsertId();
						//UPDATE ND_KANDIDATA STAVITI OVAJ ID 
			
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
							':lks_datetime' => date('Y-m-d H:i:s')
						));
						
						$nalog_query = $db->prepare("
							SELECT project_id
							FROM idk_projects
							WHERE (project_name LIKE '%Kampanje sa DIPLa%') 
						");
					
						$nalog_query->execute();
					
						$nalogrow = $nalog_query->fetch();
						
						$project_id = $nalogrow['project_id'];
						$query_project = $db->prepare("
							INSERT INTO idk_project_kandidati
								(pk_projectid, pk_kandidatid)
							VALUES
								(:pk_projectid, :pk_kandidatid)
						");

						$query_project->execute(array(
							':pk_projectid' => $project_id,
							':pk_kandidatid' => $kandidat_id
						));
						
						addToLogsStatusPrijave(NULL, $project_id, 1, $kandidat_id, 3);
						
						$kki_grupa = 1;
						$kki_naziv = "Mobilni";

						//Add mobilni to db
						$query_mob = $db->prepare("
							INSERT INTO idk_kandidat_kontakt_info
								(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
							VALUES
								(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)
						");

						$query_mob->execute(array(
							':kki_grupa' => $kki_grupa,
							':kki_naziv' => $kki_naziv,
							':kki_podatak' => $mobilni_new_ND_cand1,
							':kki_kandidat_id' => $kandidat_id
						));
						
						$kki_grupa_e = 2;
						$kki_naziv_e = "E-mail";
						
						//Add kontakt info to db
						$query_email = $db->prepare("
							INSERT INTO idk_kandidat_kontakt_info
								(kki_grupa, kki_naziv, kki_podatak, kki_kandidat_id)
							VALUES
								(:kki_grupa, :kki_naziv, :kki_podatak, :kki_kandidat_id)
						");

						$query_email->execute(array(
							':kki_grupa' => $kki_grupa_e,
							':kki_naziv' => $kki_naziv_e,
							':kki_podatak' => $email_new_ND_cand1,
							':kki_kandidat_id' => $kandidat_id
						));
						
						//Add to table users (chatbot)
						$random_string = generateRandomString();
						$options = [
							'cost' => 10,
						];
						$random_password = password_hash($random_string, PASSWORD_BCRYPT, $options);
						
						$log_desc2 = "Kandidat: " . $ime_new_ND_cand1 . " " . $prezime_new_ND_cand1 . "(".$kandidat_id."). ; (".$random_string.")";
						$log_type2 = "5";
						addToLogs($log_desc2, $log_type2);
						
						$characters = '0123456789';
						$charactersLength = strlen($characters);
						$randomString = '';
						for ($i = 0; $i < 5; $i++) {
							$randomString .= $characters[rand(0, $charactersLength - 1)];
						}
						$bot_koriscnicko_ime = $ime_new_ND_cand1.$randomString;
						$kandidat_full_name = $ime_new_ND_cand1." ".$prezime_new_ND_cand1;
						$query_user = $db->prepare("
							INSERT INTO users
								(phone, name, nalog_id, email, password, kandidat_id)
							VALUES
								(:phone, :name, :nalog_id, :email, :password, :kandidat_id)
						");

						$query_user->execute(array(
							':phone' => $mobilni_new_ND_cand1,
							':name' => $kandidat_full_name,
							':nalog_id' => 44,
							':email' => $bot_koriscnicko_ime,
							':password' => $random_password,
							':kandidat_id' => $kandidat_id
						));
					
						/*//INFOBIP
						sendSmsToCandidateInfobip1($random_string, $mobilni_new_ND_cand1, "NN");
						//sleep(1);  // Seconds
						sendSmsToCandidateInfobip2($random_string, $mobilni_new_ND_cand1, "NN");
						//sleep(1);  // Seconds
						sendSmsToCandidateInfobip3($random_string, $mobilni_new_ND_cand1, $bot_koriscnicko_ime);
						//sleep(1);  // Seconds
						sendSmsToCandidateInfobip4($random_string, $mobilni_new_ND_cand1, $bot_koriscnicko_ime);
						*/
						$phone_f = str_replace("+", '', $mobilni_new_ND_cand1);
						
						$link_dload = getSiteUrlr()."download";
						$link_uputs = "https://bit.ly/3V177tF";
						$sms2 = $txt_sms2.$link_dload;
						$sms3 = $txt_sms3_1.$bot_koriscnicko_ime.$txt_sms3_2.$random_string.$txt_sms3_3;
						$viber3 = $txt_viber3_1.'\n'.$txt_viber3_2.$bot_koriscnicko_ime.'\n'.$txt_viber3_3.$random_string.'\n';
						$sms4 = $txt_sms4.$link_uputs;
						//INFOBIP
						//sendSmsToCandidateInfobip1($random_string, $mobile_phone);
						viberPrijava1($phone_f, $txt_sms1, $txt_viber1);
						sleep(1);  // Seconds
						//sendSmsToCandidateInfobip2($random_string, $mobile_phone);
						viberPrijava2($phone_f, $sms2, $txt_viber2, $txt_btn1, $link_dload);
						sleep(1);  // Seconds
						//sendSmsToCandidateInfobip3($random_string, $mobile_phone, $bot_koriscnicko_ime);
						viberPrijava1($phone_f, $sms3, $viber3);
						sleep(1);  // Seconds
						//sendSmsToCandidateInfobip4($random_string, $mobile_phone, $bot_koriscnicko_ime);
						viberPrijava2($phone_f, $sms4, $txt_viber4, $txt_btn2, $link_uputs);
						// sendCandidateMessengerMail($random_string, $email_new_ND_cand1, $bot_koriscnicko_ime);
						
						//KRAJ DODAVANJA U TABELU KANDIDATI
					}else{
						$update_query_pov_dipl = $db->prepare("
							UPDATE idk_kandidati
							SET povezan_na_dipl = :povezan_na_dipl, kandidat_dipl_id = :kandidat_dipl_id
							WHERE kandidat_id = :kandidat_id
						");
						
						$update_query_pov_dipl->execute(array(
							':kandidat_id' => $kandidat_id_vec_u_kan,
							':povezan_na_dipl' => 1,
							':kandidat_dipl_id' => $kandidat_id_nd
						));
					}
					header("Location: " . getSiteURL() . "dipl?page=preporuka_rezultat&id=".$kandidat_id_nd."&type=1");
				}else{
				//Provjera da li se nalazi u kandidatima
					$provjera_kandidati = $db->prepare("
						SELECT kandidat_id
						FROM idk_kandidati
						WHERE kandidat_mobitel = :kandidat_mobitel AND kandidat_status != :kandidat_status AND kandidat_mobitel != ''
					");
					$provjera_kandidati->execute(array(
						":kandidat_mobitel" => $kandidat_broj_tel,
						":kandidat_status" => 3
					));
					$kandidati_da_ne = $provjera_kandidati->rowCount();
					$provjera_kandidati_row = $provjera_kandidati->fetch();
					$kandidat_id_vec_u_kan = $provjera_kandidati_row['kandidat_id'];
					if($kandidati_da_ne != 0){
						$update_query_pov_dipl = $db->prepare("
							UPDATE idk_kandidati
							SET povezan_na_dipl = :povezan_na_dipl, kandidat_dipl_id = :kandidat_dipl_id
							WHERE kandidat_id = :kandidat_id
						");
						
						$update_query_pov_dipl->execute(array(
							':kandidat_id' => $kandidat_id_vec_u_kan,
							':povezan_na_dipl' => 1,
							':kandidat_dipl_id' => $kandidat_id_vec_u_dipl
						));
					}
					//Ako se kandidat nalazi u diplu
					$kandidat_email = null; 
					$desila_se_prijava = insertPonovnePrijaveDIPL($kandidat_id_vec_u_dipl, $kandidat_ime, $kandidat_prezime, $kandidat_broj_tel, $kandidat_email, 5, 1, $kamp);
					if($desila_se_prijava == 1){
						/*
						//Slanje maila starom menadzeru sa linkom kandidata njemu dodjeljenog
						$user_query = $db->prepare("
							SELECT employee_firstname, employee_lastname, employee_email
							FROM idk_employees
							WHERE employee_id = :employee_id
						");

						$user_query->execute(array(
							':employee_id' => $zaduzen_id_vec_u_dipl
						));

						$user = $user_query->fetch();

						$employee_firstname = $user['employee_firstname'];
						$employee_lastname = $user['employee_lastname'];
						$employee_email = $user['employee_email'];

						//Send email to user
						$mail_email = $employee_email;
						$mail_name = $employee_firstname . ' ' . $employee_lastname;
						$mail_subject = "Dipl modul - Stari kandidat";
						$mail_url = "" . getSiteUrlr() . "/nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$kandidat_id_vec_u_dipl." ";
						$mail_body = "
							<p>Vaš stari kandidat u navedenom linku, pokušao se ponovno prijaviti za nostrifikaciju diplome putem linka za kampanje.</p>
							<p>Detalje pogledajte na linku: " . $mail_url . "</p>
						";
						$mail_altbody = "
							<p>Vaš stari kandidat u navedenom linku, pokušao se ponovno prijaviti za nostrifikaciju diplome putem linka za kampanje.</p>
							<p>Detalje pogledajte na linku: " . $mail_url . "</p>
						";
						
						sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);
						*/
					}
					header("Location: " . getSiteURL() . "dipl?page=preporuka_rezultat&id=".$kandidat_id_vec_u_dipl."&type=0");
				}
				
				// var_dump("IME ".$ime." PREZIME ".$prezime." BROJ TELEFONA ".$telefon." IMAGE ".$slika);
				// exit();
			break;
			
			case "dodaj_biljesku_obrada":
				$id_kan_bo = intval($_POST['id_kan_bo']);
				$podsjetnik_kan_bo = intval($_POST['podsjetnik_kan_bo']);
				if($podsjetnik_kan_bo == 1){
					$podsjetnik_datum_kan_bo = date("Y-m-d H:i:s", strtotime($_POST['podsjetnik_datum_kan_bo'].":00"));
				}else{
					$podsjetnik_datum_kan_bo = NULL;
				}
				$prilog_kan_bo = intval($_POST['prilog_kan_bo']);
				$file_names_s = NULL;
				$allowed = array('jpg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png', 'csv');
				if($prilog_kan_bo == 1){
					$file_names = array();
					for($i = 0; $i < count($_FILES['prilog_file_kan_bo']['name']); $i++) {
						$tmpFilePath = $_FILES['prilog_file_kan_bo']['tmp_name'][$i];
						if ($tmpFilePath != ""){
							$filename = $_FILES['prilog_file_kan_bo']['name'][$i];
							$file_ext = explode('.', $filename);
							$file_ext = strtolower(end($file_ext));
							if(in_array($file_ext, $allowed)) {
								$file_name_new = uniqid() . '.' . $file_ext;
								$newFilePath = "files/prilozi_dipl/" . $file_name_new;
								
								if(move_uploaded_file($tmpFilePath, $newFilePath)) {
									$file_names[] = $file_name_new;
								}
							}
						}
					}
					$file_names_s = implode(",", $file_names);
					
				}else{
					$file_names_s = NULL;
				}
				$arhiviraj_remidere_bo = intval($_POST["arhiviraj_remidere_bo"]); 
				$arhiviraj_remidere_bo_buduce = intval($_POST["arhiviraj_remidere_bo_buduce"]); 
				$arhiviraj_remider_ids_bo_buduci = $_POST["arhiviraj_remider_ids_bo_buduci"] ?? null; 

				$sadrzaj_bo = $_POST['sadrzaj_bo'];
				
				$dodavanje_biljeske_nd = $db->prepare("
					INSERT INTO idk_nd_kandidata_biljeske
						(id_kandidata_biljeska_nd, status_biljeska_nd, sadrzaj_biljeska_nd, vrijeme_dodavanja_biljeska_nd, vrijeme_grupa_biljeska_nd, dodao_zaposlenik_biljeska_nd, vrijeme_ponovnog_zvanja, prilog_biljeska_nd)
					VALUES
						(:id_kandidata_biljeska_nd, :status_biljeska_nd, :sadrzaj_biljeska_nd, :vrijeme_dodavanja_biljeska_nd, :vrijeme_grupa_biljeska_nd, :dodao_zaposlenik_biljeska_nd, :vrijeme_ponovnog_zvanja, :prilog_biljeska_nd)
				");

				$dodavanje_biljeske_nd->execute(array(
					':id_kandidata_biljeska_nd' => $id_kan_bo,
					':status_biljeska_nd' => 4,
					':sadrzaj_biljeska_nd' => $sadrzaj_bo,
					':vrijeme_dodavanja_biljeska_nd' => date("Y-m-d H:i:s"),
					':vrijeme_grupa_biljeska_nd' => date("Y"),
					':dodao_zaposlenik_biljeska_nd' => $logged_employee_id,
					':vrijeme_ponovnog_zvanja' => $podsjetnik_datum_kan_bo,
					':prilog_biljeska_nd' => $file_names_s
				));
				$biljeska_id_zadnja = $db->lastInsertId();
				
				if ($arhiviraj_remidere_bo == 1) {
					updateReminderStatusDIPL($id_kan_bo);
				}
				
				if ($arhiviraj_remidere_bo_buduce == 1 AND count($arhiviraj_remider_ids_bo_buduci) > 0) {
					updateReminderStatusDIPLBuduce($id_kan_bo, $arhiviraj_remider_ids_bo_buduci);
				}
				
				if($podsjetnik_kan_bo == 1){
					insertReminderCandidateDIPL(1, $id_kan_bo, $podsjetnik_datum_kan_bo, $logged_employee_id, 1, $biljeska_id_zadnja);
				}
				
				$log_desc = "DIPL -> Dodana bilješka obrade ID = [".$biljeska_id_zadnja."] za kandidata [".$id_kan_bo."] sa sadrzajem [".substr($sadrzaj_bo, 0, 50)."...].";
				$log_date = date('Y-m-d H:i:s');

				$log_query = $db->prepare("
					INSERT INTO idk_logs
						(log_employeeid, log_desc, log_date)
					VALUES
						(:log_employeeid, :log_desc, :log_date)
				");

				$log_query->execute(array(
					':log_employeeid' => $logged_employee_id,
					':log_desc' => $log_desc,
					':log_date' => $log_date
				));
				
				header("Location: " . getSiteURLr() . "nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$id_kan_bo); 
				
			break;
			
			case "fakturisanje_kraj_godine":
				$id = intval($_POST["idKrajGodine"]);
				$bf = $_POST["bfKrajGodine"];
				
				$putanja_racuna = createRacunBIH($id, $bf);
				//fja za slanje maila za bih
				sendMailRacun($putanja_racuna);
				
				header("Location: " . getSiteURLr() . "nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$id."&mess=3"); 
			break;
			
			case "resendLink":
				$kanIDRL = $_POST["kanIDRL"];
				
				$queryResendPredracun = $db->prepare("
					SELECT 
						ug.ug_id, 
						ug.ug_status,
						ug.ug_token,
						ug.ug_jezik, 
						pred.pr_naplata_preko
						
					FROM 
						idk_nd_ugovori ug
					INNER JOIN 
						idk_predracuni pred 
					ON 
						ug.ug_kandidat_id = pred.pr_kandidat_id
					WHERE 
						ug.ug_kandidat_id = :kanID 
						AND 
						pred.pr_kandidat_id = :kanID 
						AND 
						pred.pr_vrsta_predracuna = 1
						AND 
						pred.pr_rata = 1
						AND 
						pred.pr_status != 0
						AND 
						pred.pr_uplaceno = 0
						AND 
						ug.ug_status NOT IN (0, 3)
				");
				$queryResendPredracun->execute(array(
					':kanID' => $kanIDRL
				));
				
				$cntResendPredracun = $queryResendPredracun->rowCount();
				
				if($cntResendPredracun != 0){
					$rowResendPredracun = $queryResendPredracun->fetch();
					$ug_id = intval($rowResendPredracun["ug_id"]);
					$ug_status = intval($rowResendPredracun["ug_status"]);
					$ug_token = $rowResendPredracun["ug_token"];
					$ug_jezik = $rowResendPredracun["ug_jezik"];
					$pr_naplata_preko = $rowResendPredracun["pr_naplata_preko"];
					
					if($ug_status == 1 OR $ug_status == 4){
						//Poslan i otvoren link
						if ($pr_naplata_preko == 2) {
							$ugovor_link = getSiteUrlr()."ugovorNew/".$ug_token."/".$ug_jezik;
							sendMailUgovorLink($kanIDRL, $ugovor_link, 2);
						} else {
							$ugovor_link = getSiteUrlr()."ugovor/".$ug_token."/".$ug_jezik;
							sendMailUgovorLink($kanIDRL, $ugovor_link);
						}
						
						echo "Poslan";
						$mess = 5;
					}else if ($ug_status == 2){
						//Prihvacen
						if($ug_jezik == "sr"){
							$poruka_text_mail	= "Uspešno ste potpisali ugovor! Možete ga preuzeti zajedno sa predračunom i uplatnicom klikom na: <a href='".$ugovor_link."'>LINK</a>";
							$subject_ugovor		= "Ugovor i ";
						}
						else if($ug_jezik == "bs"){
							$poruka_text_mail	= "Uspješno ste potpisali ugovor! Možete ga preuzeti zajedno sa predračunom i uplatnicom klikom na: <a href='".$ugovor_link."'>LINK</a>";
							$subject_ugovor		= "Ugovor i ";
						}
						else{				
							$poruka_text_mail	= "Sie haben den Vertrag unterschrieben und können ihn gemeinsam mit der Pro-forma-Rechnung und den Einzahlungsschein herunterladen, indem Sie auf die Schaltfläche unten klicken: <a href='".$ugovor_link."'>LINK</a>";
							$subject_ugovor		= "Vertrag und";
						}
						sendMailUgovorPredracun($kanIDRL, $poruka_text_mail, $ugovor_link, $subject_ugovor, $pr_naplata_preko);
						echo "Prihvacen";
						$mess = 5;
					}else{
						//Ne radi nista
						echo "Nista";
						$mess = 4;
					}
					header("Location: " . getSiteURLr() . "nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$kanIDRL."&mess=".$mess); 
				}else{
					header("Location: " . getSiteURLr() . "nostrifikacija_diploma?page=otvori_ND_kandidata&id=".$kanIDRL."&mess=4"); 
				}
				
			break;
			case "statistika_izvor_lead":
				// Spajamo tri query-ja u jedan
				// 1. queryPredracuni
				// 2. queryNezainteresirani
				// 3. queryArhiva

				$from = "2000-01-01";
				$to = "2050-01-01";
				if(isset($_REQUEST["from"]))
				{
					$from = $_REQUEST["from"];
				}
				if(isset($_REQUEST["to"]))
				{
					$to = $_REQUEST["to"];
				}
				$detaljno = isset($_REQUEST["detaljno"]);
				$kampanje = $_REQUEST["kampanje"] ?? null;
				$drzave = $_REQUEST["drzave"] ?? null;


				// Prvo uzeti sve moguće razloge zašto je kandidat arhiviran da od njih možemo
				// napraviti PIVOT table (redovi postaju kolone)

				$sqlZaRazlogeArhive = "
					SELECT DISTINCT `idk_ro_usluge`.`id_ro`,
						`idk_ro_usluge`.`naziv_ro_bs`
					FROM
						(
							SELECT
								*
							FROM
								`idk_nd_kandidata`
							WHERE
								`idk_nd_kandidata`.`povijest_nd_kandidata` = 5
								AND `idk_nd_kandidata`.`status_nd_kandidata` = 7
								AND `idk_nd_kandidata`.`vrijeme_kreiranja_nd_kandidata` BETWEEN :from AND :to
						) AS kandidatiKampanja
						INNER JOIN (
							SELECT
								id_biljeska_nd,
								`idk_nd_kandidata_biljeske`.`id_kandidata_biljeska_nd`,
								`idk_nd_kandidata_biljeske`.`razlog_biljeska_nd`
							FROM
								`idk_nd_kandidata_biljeske`
							WHERE
								`idk_nd_kandidata_biljeske`.`status_biljeska_nd` = 2 AND id_biljeska_nd IN (
										SELECT MAX(id_biljeska_nd)
										FROM
											`idk_nd_kandidata_biljeske`
										GROUP BY
											`idk_nd_kandidata_biljeske`.`id_kandidata_biljeska_nd`
								)
						) as biljeske on biljeske.id_kandidata_biljeska_nd = kandidatiKampanja.id_broj_nd_kandidata
						INNER JOIN `idk_ro_usluge` on `idk_ro_usluge`.`id_ro` = biljeske.razlog_biljeska_nd
						INNER JOIN `idk_kampanje_dipl` as kampanje ON kandidatiKampanja.kampanja_id = kampanje. `kd_id` "
						. ($drzave ?  "AND kampanje.kd_drzava in (" . implode(",",$drzave) .") " : "" )
						. ($kampanje ?  "AND kampanje.kd_id in (" . implode(",",$kampanje) .") " : "" ) . 
					"WHERE
						`idk_ro_usluge`.`status_ro` = 1
					ORDER BY id_ro
				";

				$queryZaRazlogeArhive = $db->prepare($sqlZaRazlogeArhive);
				$queryZaRazlogeArhive->execute([
					":from" => $from,
					":to" => $to
				]);
				$razloziArhiva = $queryZaRazlogeArhive->fetchAll(PDO::FETCH_CLASS);
				$casesZaArhivu = "";

				foreach($razloziArhiva as $razlog)
				{
					// Smaknuti znakove koji ne mogu biti ime kolone
					$razlog->naziv_ro_bs = str_replace([" ", "/"],		"_",	$razlog->naziv_ro_bs);
					$razlog->naziv_ro_bs = str_replace(["(", ")"],		"",		$razlog->naziv_ro_bs);
					$razlog->naziv_ro_bs = str_replace([".", ",", "-"],	"",		$razlog->naziv_ro_bs);
					//$razlog->naziv_ro_bs = str_replace(["ć", "č"],		"c",	$razlog->naziv_ro_bs);
					//$razlog->naziv_ro_bs = str_replace("š",				"s",	$razlog->naziv_ro_bs);
					//$razlog->naziv_ro_bs = str_replace("ž",				"z",	$razlog->naziv_ro_bs);
					//$razlog->naziv_ro_bs = str_replace("đ",				"d",	$razlog->naziv_ro_bs);

					//$razlog->naziv_ro_bs = str_replace(["Ć", "Č"],		"c",	$razlog->naziv_ro_bs);
					//$razlog->naziv_ro_bs = str_replace("Š",				"s",	$razlog->naziv_ro_bs);
					//$razlog->naziv_ro_bs = str_replace("Ž",				"z",	$razlog->naziv_ro_bs);
					//$razlog->naziv_ro_bs = str_replace("Đ",				"d",	$razlog->naziv_ro_bs);

				}

				$casesZaArhivu = 
				implode(",",
						array_map(function($obj){ return "SUM(CASE WHEN id_ro = {$obj->id_ro} THEN 1 ELSE 0 END) AS {$obj->naziv_ro_bs}"; }, $razloziArhiva)
				);

				$sqlZaRazlogeNezainteresiran = "
					SELECT
						DISTINCT `idk_ro_usluge`.`id_ro`,
						`idk_ro_usluge`.`naziv_ro_bs`
					FROM
						(
							SELECT
								*
							FROM
								`idk_nd_kandidata`
							WHERE
								`idk_nd_kandidata`.`povijest_nd_kandidata` = 5
								AND `idk_nd_kandidata`.`status_nd_kandidata` = 1
								AND `idk_nd_kandidata`.`pstatus_nd_kandidata`= 4
								AND `idk_nd_kandidata`.`vrijeme_kreiranja_nd_kandidata` BETWEEN :from AND :to
						) AS kandidatiKampanja
						INNER JOIN (
							SELECT
								id_biljeska_nd,
								`idk_nd_kandidata_biljeske`.`id_kandidata_biljeska_nd`,
								`idk_nd_kandidata_biljeske`.`razlog_biljeska_nd`
							FROM
								`idk_nd_kandidata_biljeske`
							WHERE
								`idk_nd_kandidata_biljeske`.`status_biljeska_nd` = 2 AND id_biljeska_nd IN (
										SELECT MAX(id_biljeska_nd)
										FROM
											`idk_nd_kandidata_biljeske`
										GROUP BY
											`idk_nd_kandidata_biljeske`.`id_kandidata_biljeska_nd`
									)
						) as biljeske on biljeske.id_kandidata_biljeska_nd = kandidatiKampanja.id_broj_nd_kandidata
						INNER JOIN `idk_ro_usluge` on `idk_ro_usluge`.`id_ro` = biljeske.razlog_biljeska_nd
						INNER JOIN `idk_kampanje_dipl` as kampanje ON kandidatiKampanja.kampanja_id = kampanje. `kd_id` "
						. ($drzave ?  "AND kampanje.kd_drzava in (" . implode(",",$drzave) .") " : "" ) 
						. ($kampanje ?  "AND kampanje.kd_id in (" . implode(",",$kampanje) .") " : "" ) . 
					" WHERE
						`idk_ro_usluge`.`status_ro` = 1
					ORDER BY id_ro;
				";

				$queryZaRazlogeNezainteresiran = $db->prepare($sqlZaRazlogeNezainteresiran);
				$queryZaRazlogeNezainteresiran->execute([
					":from" => $from,
					":to" => $to
				]);
				$razloziNezainteresiran = $queryZaRazlogeNezainteresiran->fetchAll(PDO::FETCH_CLASS);
				$casesZaNezainteresiran = "";

				foreach($razloziNezainteresiran as $razlog)
				{
					// Smaknuti znakove koji ne mogu biti ime kolone
					$razlog->naziv_ro_bs = str_replace([" ", "/"],		"_",	$razlog->naziv_ro_bs);
					$razlog->naziv_ro_bs = str_replace(["(", ")"],		"",		$razlog->naziv_ro_bs);
					$razlog->naziv_ro_bs = str_replace([".", ",", "-"],	"",		$razlog->naziv_ro_bs);
					//$razlog->naziv_ro_bs = str_replace(["ć", "č"],		"c",	$razlog->naziv_ro_bs);
					//$razlog->naziv_ro_bs = str_replace("š",				"s",	$razlog->naziv_ro_bs);
					//$razlog->naziv_ro_bs = str_replace("ž",				"z",	$razlog->naziv_ro_bs);
					//$razlog->naziv_ro_bs = str_replace("đ",				"d",	$razlog->naziv_ro_bs);

					//$razlog->naziv_ro_bs = str_replace(["Ć", "Č"],		"c",	$razlog->naziv_ro_bs);
					//$razlog->naziv_ro_bs = str_replace("Š",				"s",	$razlog->naziv_ro_bs);
					//$razlog->naziv_ro_bs = str_replace("Ž",				"z",	$razlog->naziv_ro_bs);
					//$razlog->naziv_ro_bs = str_replace("Đ",				"d",	$razlog->naziv_ro_bs);

				}

				$casesZaNezainteresiran = 
				implode(",",
						array_map(function($obj){ return "SUM(CASE WHEN id_ro = {$obj->id_ro} THEN 1 ELSE 0 END) AS {$obj->naziv_ro_bs}"; }, $razloziNezainteresiran)
				);


				$sqlZaArhivu = "
					SELECT
						kd_id,
						kd_naziv,
						COUNT(*) as arhivirani
						" . ($casesZaArhivu ? (',' . $casesZaArhivu) : "") . "
					FROM
						(
							SELECT
								*
							FROM
								`idk_nd_kandidata`
							WHERE
								`idk_nd_kandidata`.`povijest_nd_kandidata` = 5
								AND `idk_nd_kandidata`.`status_nd_kandidata` = 7
								AND `idk_nd_kandidata`.`vrijeme_kreiranja_nd_kandidata` BETWEEN :from AND :to
						) AS kandidatiKampanja
						INNER JOIN (
							SELECT
								id_biljeska_nd,
								`idk_nd_kandidata_biljeske`.`id_kandidata_biljeska_nd`,
								`idk_nd_kandidata_biljeske`.`razlog_biljeska_nd`
							FROM
								`idk_nd_kandidata_biljeske`
							WHERE
								`idk_nd_kandidata_biljeske`.`status_biljeska_nd` = 2 AND id_biljeska_nd IN (
										SELECT MAX(id_biljeska_nd)
										FROM
											`idk_nd_kandidata_biljeske`
										GROUP BY
											`idk_nd_kandidata_biljeske`.`id_kandidata_biljeska_nd`
								)
						) as biljeske on biljeske.id_kandidata_biljeska_nd = kandidatiKampanja.id_broj_nd_kandidata
						INNER JOIN `idk_ro_usluge` on `idk_ro_usluge`.`id_ro` = biljeske.razlog_biljeska_nd
						INNER JOIN `idk_kampanje_dipl` as kampanje ON kandidatiKampanja.kampanja_id = kampanje. `kd_id` "
						. ($drzave ?  "AND kampanje.kd_drzava in (" . implode(",",$drzave) .") " : "" ) 
						. ($kampanje ?  "AND kampanje.kd_id in (" . implode(",",$kampanje) .") " : "" ) . 
					" WHERE
						`idk_ro_usluge`.`status_ro` = 1
					GROUP BY
						kampanje.kd_id
				";
				$sqlZaNezainteresiran = "
					SELECT
						kd_id,
						kd_naziv,
						COUNT(*) as nezainteresirani
						" . ($casesZaNezainteresiran ? (',' . $casesZaNezainteresiran) : "") . "
					FROM
						(
							SELECT
								*
							FROM
								`idk_nd_kandidata`
							WHERE
								`idk_nd_kandidata`.`povijest_nd_kandidata` = 5
								AND `idk_nd_kandidata`.`status_nd_kandidata` = 1
								AND `idk_nd_kandidata`.`pstatus_nd_kandidata`= 4
								AND `idk_nd_kandidata`.`vrijeme_kreiranja_nd_kandidata` BETWEEN :from AND :to
						) AS kandidatiKampanja
						INNER JOIN (
							SELECT
								id_biljeska_nd,
								`idk_nd_kandidata_biljeske`.`id_kandidata_biljeska_nd`,
								`idk_nd_kandidata_biljeske`.`razlog_biljeska_nd`
							FROM
								`idk_nd_kandidata_biljeske`
							WHERE
								`idk_nd_kandidata_biljeske`.`status_biljeska_nd` = 2 AND id_biljeska_nd IN (
										SELECT MAX(id_biljeska_nd)
										FROM
											`idk_nd_kandidata_biljeske`
										GROUP BY
											`idk_nd_kandidata_biljeske`.`id_kandidata_biljeska_nd`
								)
						) as biljeske on biljeske.id_kandidata_biljeska_nd = kandidatiKampanja.id_broj_nd_kandidata
						INNER JOIN `idk_ro_usluge` on `idk_ro_usluge`.`id_ro` = biljeske.razlog_biljeska_nd
						INNER JOIN `idk_kampanje_dipl` as kampanje ON kandidatiKampanja.kampanja_id = kampanje. `kd_id` "
						. ($drzave ?  "AND kampanje.kd_drzava in (" . implode(",",$drzave) .") " : "" ) 
						. ($kampanje ?  "AND kampanje.kd_id in (" . implode(",",$kampanje) .") " : "" ) . 
					" WHERE
						`idk_ro_usluge`.`status_ro` = 1
					GROUP BY
						kampanje.kd_id
				";
				


				$sqlZaPredracune = "
				SELECT
					kd_id,
					kd_naziv,
					COUNT(*) as leadovi,
					COUNT(CASE WHEN pr_status != 0 THEN 1 ELSE null END) as izdani_predracuni,
					COUNT(CASE WHEN pr_status = 2 THEN 1 ELSE null END)  as uplaceni_predracuni,
					COUNT(CASE WHEN termin_kandidat_status = 1 AND termin_kandidat_pstatus IN (7,9,10) THEN 1 ELSE NULL END) as termini,
					COUNT(CASE WHEN termin_kandidat_status = 1 AND termin_kandidat_pstatus = 9 THEN 1 ELSE NULL END) as termin_zainteresiran,
					COUNT(CASE WHEN termin_kandidat_status = 1 AND termin_kandidat_pstatus = 10 THEN 1 ELSE NULL END) as termin_ostali,
					COUNT(CASE WHEN termin_kandidat_status = 1 AND termin_kandidat_pstatus = 7 THEN 1 ELSE NULL END) as termin_neuspjesan
				FROM(
						SELECT
							*
						FROM
							`idk_nd_kandidata`
						WHERE
							`idk_nd_kandidata`.`povijest_nd_kandidata` = 5
							AND idk_nd_kandidata.vrijeme_kreiranja_nd_kandidata BETWEEN :from AND :to
					) AS kandidatiKampanja
					INNER JOIN `idk_kampanje_dipl` as kampanje ON kandidatiKampanja.kampanja_id = kampanje. `kd_id` "
					. ($drzave ?  "AND kampanje.kd_drzava in (" . implode(",",$drzave) .") " : "" ) 
					. ($kampanje ?  "AND kampanje.kd_id in (" . implode(",",$kampanje) .") " : "" ) . 
					" LEFT JOIN `idk_nd_termini` ON `idk_nd_termini`.termin_kandidat_id = kandidatiKampanja.id_broj_nd_kandidata AND `idk_nd_termini`.termin_status = 1
					LEFT JOIN `idk_predracuni` as predracuni on predracuni. `pr_kandidat_id` = kandidatiKampanja. `id_broj_nd_kandidata`
					AND pr_rata = 1
					AND pr_status != 0
				GROUP BY
					kampanje.kd_id
				";




				$zavrsniSql = "
					SELECT 
						PREDRACUNI_OUT.kd_id AS ID, 
						PREDRACUNI_OUT.kd_naziv AS Naziv, 
						leadovi, 
						izdani_predracuni as izdani_predračuni, 
						uplaceni_predracuni as uplaćeni_predračuni, 
						termini, 
						termin_zainteresiran,
						termin_ostali,
						termin_neuspjesan,
						nezainteresirani
						" . ($detaljno && $casesZaNezainteresiran ? "," . implode(",",array_map(function($obj){ return "NEZAINTERESIRAN_OUT." . $obj->naziv_ro_bs . " as NEZ_" . $obj->naziv_ro_bs . "_" . $obj->id_ro ; }, $razloziNezainteresiran)) : "" ) . ",arhivirani
						" . ($detaljno && $casesZaArhivu ? "," . implode(",",array_map(function($obj){ return "ARHIVA_OUT." . $obj->naziv_ro_bs . " as ARH_" .  $obj->naziv_ro_bs . "_" . $obj->id_ro;}, $razloziArhiva)) : "") . "
					FROM 
						($sqlZaPredracune) as PREDRACUNI_OUT 
					LEFT JOIN
						($sqlZaNezainteresiran) as NEZAINTERESIRAN_OUT ON PREDRACUNI_OUT.kd_id = NEZAINTERESIRAN_OUT.kd_id
					LEFT JOIN 
						($sqlZaArhivu) as ARHIVA_OUT ON PREDRACUNI_OUT.kd_id = ARHIVA_OUT.kd_id
				"; 


				$query = $db->prepare($zavrsniSql);

				$query->execute([
					":from" => $from,
					":to" => $to,
				]);

				echo json_encode($query->fetchAll(PDO::FETCH_CLASS));
			break;
			case "getKampanje":
				$drzave = $_REQUEST["drzave"] ?? [];
				$sql = " SELECT kd_id, kd_naziv FROM `idk_kampanje_dipl`" .
					(isset($_REQUEST["drzave"]) ? (" WHERE kd_drzava IN ( " . implode(",",$drzave) . ")" ) : "");

				$query = $db->prepare($sql);
				$query->execute();
				echo json_encode($query->fetchAll(PDO::FETCH_CLASS));
			break;
		}
	}
}
