<?php
include('includes/functions.php');
ini_set('max_execution_time', 0); //300 seconds = 5 minutes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

Global $vicidial_db;

try {
	$vicidial_db = new PDO('mysql:dbname=asterisk;host=185.164.35.44;','cron','1234');
    $vicidial_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
	exit();
}

/*************************************************
				FUNCTIONS
**************************************************/

function getAgentName($agent_username){
	
	Global $vicidial_db;
	
	$vici_query_agent = $vicidial_db->prepare("SELECT full_name
											FROM `vicidial_users`
											WHERE `user` LIKE '$agent_username'");
	$vici_query_agent->execute();
	
	$query_results = $vici_query_agent->fetch();
	$agent_name = $query_results['full_name'];
	
	return $agent_name;
}






if(isset($_GET['akcija'])){
	
	$server_ip = "185.164.35.44";
	
	
	$akcija = $_GET['akcija'];
	session_start();
	switch($akcija){ 
		case 'call_candidate':

		if(!isset($_SESSION['IN_CALL'])){  // PROVJERI DA LI JE AGENT PREKINUO PROŠLI POZIV
			$zaposlenik_id = $_POST['zaposlenik_id'];
			$kontakt_telefon = $_POST['kandidat_tel']; 
			
			//open connection za CURL
			$ch = curl_init();
			$read_query = $db->prepare("SELECT employee_viciuser, employee_vicipass
										   FROM idk_employees
										   WHERE employee_id = :employee_id");
			$read_query->execute(array(
						':employee_id' => $zaposlenik_id
			));
			
			$row = $read_query->fetch();
			
			$user = $row['employee_viciuser'];
			$password = $row['employee_vicipass'];
			
			if(empty($user) || empty($password)){
				echo "Nemate podešene korisničke podatke za vicidial na Jobstep CRM-u";
				exit();
			}
			// $user = "agent002";
			// $password = "User6432";
			
			$pozivni = substr(trim($kontakt_telefon," "),1,2);
			if( $pozivni == "38" ){ // POTENCIJALNI PROBLEM --
				
				$pozivni = substr(trim($kontakt_telefon," "),1,3);
				$mobilni = substr(trim($kontakt_telefon," "),4);
			}
			else{
				
				$mobilni = substr(trim($kontakt_telefon," "),3);
				
			}

			$url = "http://$server_ip/agc/api.php?";
			$params = array(
			  'source'=>'CRM',
			  'user'=> $user,
			  'pass'=> $password,
			  'agent_user' => $user,
			  'function'=> 'external_dial',
			  'value'=> $mobilni,
			  'phone_code'=> $pozivni,
			  'search'=> "NO",
			  'preview'=> 'N0',
			  'focus'=> 'YES',
			);




			$url = "http://$server_ip/agc/api.php?";
			$data = http_build_query($params);
			/*****************************************************/
			/***********IZBRISATI SLJEDEĆE DVIJE LINIJE NA LIVE SISTEMU***************/
			/*****************************************************/
			// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

			/*****************************************************/



			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, count($params));
			curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);


			//execute post
			$result = curl_exec($ch);
			// PROVJERA USPJEŠNOSTI POZIVA ODNOSNO ISPIS GREŠAKA
			// PORUKE SE ZASNIVAJU NA VICIDIAL DOKUMENTACIJI 
			// ERROR: external_dial not valid - 7275551212|1|YES|6666
			// ERROR: no user found - 6666
			// ERROR: agent_user is not logged in - 6666
			// ERROR: agent_user is not allowed to place manual dial calls - 6666
			// ERROR: caller_id_number from group_alias is not valid - 6666|TESTING|123
			// ERROR: group_alias is not valid - 6666|TESTING
			// ERROR: outbound_cid is not allowed on this system - 6666|3125551212|DISABLED
			// ERROR: vtiger callback activity does not exist in vtiger system - 12345   ---- OVO NE POSTOJI UNUTAR NAŠEG SISTEMA
			// ERROR: phone_number is already in this agents manual dial queue - 6666|7275551211
			// ERROR: lead_id is not valid - 6666|1234567
			// ERROR: phone number is not valid - 6666||1234567|
			// ERROR: phone number lead_id search not found - 6666|7275551212|1234567|
			// SUCCESS: external_dial function set - 7275551212|6666|1|YES|NO|YES|123456|1232020456|9|TESTING|7275551211|	

				if(strpos($result,'external_dial not valid')){
					echo 'Greška: Nedovoljni podaci za pokretanja poziva - kontaktirajte administratora';
					exit();
				}
				else if(strpos($result,'no user found')){
					echo 'Greška: Pogrešni pristupni podaci za telefoniju - kontaktirajte administratora';
					exit();
				}
				else if(strpos($result,'agent_user is not logged in')){
					echo 'Greška: Niste logirani u vicidial i zoiper | pokušajte ponovo';
					exit();
				}
				else if(strpos($result,'agent_user is not allowed to place manual')){
					echo 'Greška: Pogrešno postavljen profil u vicidial sistemu - kontaktirajte administratora (Allow manual dial campaign)';
					exit();
				}
				else if(strpos($result,'caller_id_number from group_alias is not valid')){
					echo 'Greška: vicidial sistem - caller_id_number from group_alias is not valid';
					exit();
				}
				else if(strpos($result,'outbound_cid is not allowed on this system')){
					echo 'Greška: vicidial sistem - outbound_cid is not allowed on this system';
					exit();
				}
				else if(strpos($result,'phone_number is already in this agents manual dial queue')){
					echo 'Greška: s kandidatom se već obavlja poziv';
					exit();
				}
				else if(strpos($result,'lead_id is not valid')){
					echo 'Greška: vicidal sistem - lead id is not valid';
					exit();
				}
				else if(strpos($result,'phone number is not valid')){
					echo 'Greška: broj telefona nije u redu';
					exit();
				}
				else if(strpos($result,'phone number lead_id search not found')){
					echo 'Greška: vicidial sistem - phone number lead_id search not found';
					exit();
				}else if(strpos($result,'external_dial function set')){
					echo 'NE ZABORAVITE PREKINUTI POZIV NAKON ZAVRŠETKA RAZOGOVORA';
					// setcookie("IN_CALL", "employe:".$zaposlenik_id, time() + 86000);
					$_SESSION['IN_CALL'] = true;
					exit();
				}
				else {
					echo $result; exit(); 
				}
				
				curl_multi_close();
		}else{
			echo "NISTE PREKINULI PRETHODNI POZIV";
		}
		break;
		case 'abort_call':
			
			//open connection za CURL
			$ch = curl_init();
			$zaposlenik_id = $_POST['zaposlenik_id'];
			$read_query = $db->prepare("SELECT employee_viciuser, employee_vicipass
										   FROM idk_employees
										   WHERE employee_id = :employee_id");
			$read_query->execute(array(
						':employee_id' => $zaposlenik_id
			));
			$row = $read_query->fetch();
			
			$user = $row['employee_viciuser'];
			$password = $row['employee_vicipass'];
			if(empty($user) || empty($password)){
				echo "Nemate podešene korisničke podatke za vicidial na Jobstep CRM-u";
				exit();
			}
			
			
			$url = "http://$server_ip/agc/api.php?";
				$params_prekid = array(
				  'source'=>'CRM',
				  'user'=> $user,
				  'pass'=> $password,
				  'agent_user' => $user,
				  'function'=> 'external_hangup',
				  'value'=> "1"
				);
 
				$params_status = array(
				  'source'=>'CRM',
				  'user'=> $user,
				  'pass'=> $password,
				  'agent_user' => $user,
				  'function'=> 'external_status',
				  'value'=> "N"
				);

				$data1 = http_build_query($params_prekid);
				$data2 = http_build_query($params_status);
				/*****************************************************/
				/*****************************************************/
				// POVRATNE INFORMACIJE O VICIDIAL POZIVU | ERROR MESSAGES
				
				// ERROR: external_hangup not valid - 1|6666
				// ERROR: no user found - 6666
				// ERROR: agent_user is not logged in - 6666
				// SUCCESS: external_hangup function set - 1|6666
				// ERROR: external_status not valid - A|6666
				// ERROR: no user found - 6666
				// ERROR: agent_user is not logged in - 6666
				// SUCCESS: external_status function set - A|6666

				// SLANJE FUNKCIJE ZA PREKID POZIVA
				curl_setopt($ch,CURLOPT_URL, $url); 
				curl_setopt($ch,CURLOPT_POST, count($params_prekid));
				curl_setopt($ch,CURLOPT_POSTFIELDS, $data1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);


				//execute post
				$result = curl_exec($ch);
				curl_close($ch);
				$ch = curl_init();
				
				if(strpos($result,'external_hangup not valid')){
					echo 'Greška: vicidial sistem - external_hangup not valid - kontaktirajte administratora';
					exit();
				}
				if(strpos($result,'no user found')){
					echo 'Greška: pogrešni podaci za vicidial sistem - kontaktirajte administratora';
					exit();
				}
				if(strpos($result,'agent_user is not logged in')){
					echo 'Greška: izlogirani ste iz vicidiala - kontaktirajte administratora';
					exit();
				}
				// SLANJE FUNKCIJE ZA POSTAVLJANJE STATUSA -- OBAVEZNO ZA VICIDIAL MANUAL POZIV
				curl_setopt($ch,CURLOPT_URL, $url);
				curl_setopt($ch,CURLOPT_POST, count($params_status));
				curl_setopt($ch,CURLOPT_POSTFIELDS, $data2);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

				$result = curl_exec($ch);
				
				if(strpos($result,'external_status not valid')){
					echo 'Greška: vicidial sistem - external_status not valid - kontaktirajte administratora';
					exit();
				}
				if(strpos($result,'no user found')){
					echo 'Greška: pogrešni podaci za vicidial sistem - kontaktirajte administratora';
					exit();
				}
				if(strpos($result,'agent_user is not logged in')){
					echo 'Greška: izlogirani ste iz vicidiala - kontaktirajte administratora';
					exit();
				}else if(strpos($result,'function set')){
					echo 'POZIV USPJEŠNO PREKINUT';
					// setcookie("IN_CALL", "employe:".$zaposlenik_id, time() - 586000);
					unset($_SESSION["IN_CALL"]);
					exit();
				}
				else {
					echo $result; exit(); 
				}
				curl_multi_close();
			break;
			case 'getRecordingsLocation':
			
				/***
				*
				*	Ovaj case kupi lokacije snimaka razgovora s određenim kandidatom (njegovim tel. brojem kao parametrom pretrage) 
					koristeći vicidial non_agent_api i minimalnom komunikacijom vicidial bazom
					Api se koristi zbog standardnog povratnog formata i radi izbjegavanja više kverija nad vicidial bazom
					***
					POVRATNA INFORMACIJA API-JA JE STRING KOJI SADRŽI DATUM RAZGOVORA, PARAMETRE PRETRAGE I URL DO LOKACIJE SNIMKA
				*
				*
				***/
				
				$url = "http://$server_ip/vicidial/non_agent_api.php?";
				
				$zaposlenik_id = $_POST['zaposlenik_id'];
				$kontakt_telefon = $_POST['kandidat_tel'];
				
				$vici_login = getZaposlenikVicidialParams($zaposlenik_id);
				// $vici_login = getZaposlenikVicidialParams(87);
				
				$user = $vici_login[0]; $password = $vici_login[1];
				if(empty($user) || empty($password)){
					echo "Nemate podešene korisničke podatke za vicidial na Jobstep CRM-u";
					exit();
				}
				
				
				$pozivni = substr(trim($kontakt_telefon," "),1,2);
				// $kontakt_telefon = '+387603358470';
				if( $pozivni == "38" ){ // POTENCIJALNI PROBLEM --
					$pozivni = substr(trim($kontakt_telefon," "),1,3);
					$mobilni = substr(trim($kontakt_telefon," "),4);
				}
				else{
					$mobilni = substr(trim($kontakt_telefon," "),3);
				}
			
				
				$vici_query = $vicidial_db->prepare("SELECT lead_id,user
											FROM `vicidial_list`
											WHERE `phone_number` LIKE CONVERT( _utf8 '$mobilni' 
											USING latin1 )
											COLLATE latin1_swedish_ci"); // VICIDIAL QUERY, SINTAKSA KOPIRANA IZ VICI SOURCE KODA
				$vici_query->execute();
				
				$vici_recordings_location = array(); // Moguće više različitih razgovora istim brojem (više lead_id-va)
				$datumi_razgovora = array(); // Koliko razgovora / toliko datuma
				$error_log = array(); // Niz s ID-ovima leadova u vicidial sistemu
				$agent_pozivatelj = array();
				while($row = $vici_query->fetch()){
					$lead_id = $row['lead_id'];
					$callagent = $row['user'];
					
					
					//otvori cURL
					$ch = curl_init();
				
					$params = array(
					  'source'=>'CRM',
					  'user'=> $user,
					  'pass'=> $password,
					  'stage' => 'csv',
					  'function'=> 'recording_lookup',
					  'lead_id'=> $lead_id
					);
					
					$data = http_build_query($params);
					
					curl_setopt($ch,CURLOPT_URL, $url); 
					curl_setopt($ch,CURLOPT_POST, count($params));
					curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					
					$result = curl_exec($ch);
					// var_dump($result);
					if(strpos($result,'NO RECORDINGS FOUND')){
						array_push($error_log, "Nema snimka za ovaj lead id : ".$lead_id);
					}
					if(strpos($result,'NOT HAVE PERMISSION')){ // FATAL ERROR
						echo 'Greška: nemate pristup vicidial snimcima razgovora - kontaktirajte administratora';
						exit();
					}
					if(strpos($result,'INVALID SEARCH')){  // FATAL ERROR
						echo 'Greška: CRM GREŠKA - kontaktirajte administratora';
						exit();
					}
					else if(strpos($result,'http')){
						$broj_snimaka = substr_count($result,'http');
						
						for($i = 1; $i<=$broj_snimaka; $i++){
							
							/*** U SLJEDEĆIM LINIJIAMA KODA SE UPOTREBLJAVA MAGIČNA OPCIJA DINAMIČKOG KREIRANJA VARIJABLNIH VARIJABLI | 
							*** VIŠE INFORMACIJA NA https://www.php.net/manual/en/language.types.string.php#language.types.string.parsing.complex
							***/
							$prijasnji_index = $i-1;
							if($prijasnji_index == 0){
								
								${"kraj_$i-agent"} = strpos($result,',',20);
								${"kraj_$i-linka"} = strpos($result,'.mp3');
								${"pocetak_$i-linka"} = strpos($result,'http'); 
								${"duzina_$i-linka"} = ${"kraj_$i-linka"} + 4 - ${"pocetak_$i-linka"}; /* DOBAVLJANJE POZICIJE POČETNIH I KRAJNJIH SLOVA TE DUŽINE LINKA 
																									   ** UNUTAR STRINGA KOJEG JE VRATIO API 
																									   */
								
								${"konacan_$i-link"}= substr ($result, ${"pocetak_$i-linka"}, ${"duzina_$i-linka"});   
								${"konacan_$i-datum"} = substr($result,0,19);   // DATUM IMA DUŽINU 19 ZNAKOVA I NA POČETKU JE STRINGA
								$formated_date = date('d.m.Y H:i:s',strtotime(${"konacan_$i-datum"})); // FORMAT ZBOG ISPISA
								
								${"konacan_$i-agent"} = substr($result,20,${"kraj_$i-agent"}-20);  
								$agent_name = getAgentName(${"konacan_$i-agent"});
								$agent_name = preg_replace("/[^a-zA-Z0-9 ]+/", "", $agent_name);
								array_push($agent_pozivatelj, $agent_name);  
								
								array_push($vici_recordings_location, ${"konacan_$i-link"}); // NIZ KOJI SADRŽI LINKOVE SNIMAKA
								array_push($datumi_razgovora, $formated_date); // NIZ KOJI SADRŽI DATUME ODGOVARAJUĆIH SNIMAKA
							}
							else{
								${"pocetak_$i-linka"} = strpos($result,'http', ${"pocetak_$prijasnji_index-linka"}+${"duzina_$prijasnji_index-linka"});
								${"kraj_$i-linka"} = strpos($result,'.mp3',${"kraj_$prijasnji_index-linka"}+4);
								${"duzina_$i-linka"} = ${"kraj_$i-linka"} + 4 - ${"pocetak_$i-linka"};
								${"kraj_$i-agent"} = strpos($result,',',${"kraj_$prijasnji_index-linka"}+25);
																									   /* DOBAVLJANJE POZICIJE POČETNIH I KRAJNJIH SLOVA TE DUŽINE LINKA 
																									   ** UNUTAR STRINGA KOJEG JE VRATIO API 
																									   *** NA OSNOVU POZICIJE ********PROŠLOG LINKA**********!
																									   */
								
								${"konacan_$i-link"} = substr ($result,${"pocetak_$i-linka"},${"duzina_$i-linka"});
								${"konacan_$i-datum"} = substr($result,${"kraj_$prijasnji_index-linka"}+4,19);
								$formated_date = date('d.m.Y H:i:s',strtotime(${"konacan_$i-datum"}));
								
								${"konacan_$i-agent"} = substr($result,${"kraj_$prijasnji_index-linka"}+25,${"kraj_$i-agent"}-25-${"kraj_$prijasnji_index-linka"});
								
								$agent_name = getAgentName(${"konacan_$i-agent"});
								$agent_name = preg_replace("/[^a-zA-Z0-9 ]+/", "", $agent_name);
								array_push($agent_pozivatelj, $agent_name);  
								
								array_push($vici_recordings_location, ${"konacan_$i-link"});
								array_push($datumi_razgovora, $formated_date);
							}
							 
						}
					}
					curl_close($ch);
				}
				if(empty($vici_recordings_location)){
					echo "NEMA SNIMAKA RAZGOVORA ZA OVAJ BROJ TELEFONA <br> LOG GREŠAKA:<br>";
					print_r($error_log);
					exit();
				}else{
					// var_dump($agent_pozivatelj);
					foreach($vici_recordings_location as $index => $link_snimka){
						echo '
							<p><a style="margin-right:2rem" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" href="'.$link_snimka.'" target="_blank"><i class="fa fa-music" aria-hidden="true"></i><span> SNIMAK - '.($index+1).'</span></a><span> DATUM RAZGOVORA: <b>'.$datumi_razgovora[$index].'</b>- Agent: <b>'.$agent_pozivatelj[$index].'</b></span></p> 
						'; 
					}
				} 
				
			break;
	}
}

?>