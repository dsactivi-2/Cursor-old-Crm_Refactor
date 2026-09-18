<?php
	include("includes/functions.php");
	include("includes/common.php");
?>

	
		<?php 	
		
			
			/* KANDIDATI PO STRUKAMA
			$kandidati_id = array();
			$stmt = $db->prepare("SELECT ss_naziv, skola_naziv FROM idk_skole_smjerovi
								  JOIN idk_skole
								  ON idk_skole_smjerovi.ss_skola_id = idk_skole.skola_id
								  WHERE ss_struka_id = 2");
			$stmt->execute();
			$result = $stmt->fetchAll();
			
			foreach($result as $smjer){
				$naziv_smjera = $smjer['ss_naziv'];
				$naziv_skole = $smjer['skola_naziv'];
				
				$stmt2 = $db->prepare("SELECT ke_kandidat_id FROM idk_kandidat_edukacija WHERE ke_naziv_kvalifikacije LIKE '".$naziv_smjera."'");
				$stmt2->execute();
				$result2 = $stmt2->fetchAll();
				
				foreach($result2 as $kandidat){
					$kandidat_id = $kandidat['ke_kandidat_id'];
					$kadidati_id[] = $kandidat_id;
				}
			}
			$rezultat = array_unique($kadidati_id);
			
			foreach($rezultat as $kandidat){
				echo $kandidat . ",";
			}	*/
			
			
			
			
			/*KANDIDATI PO NALOGU 211 I 213*/
			// $kandidati211 = array();
			// $query = $db->prepare("SELECT pk_kandidatid 
									// FROM idk_project_kandidati 
									// JOIN idk_projects 
									// ON idk_project_kandidati.pk_projectid = idk_projects.project_id 
									// WHERE idk_projects.project_nalogid = 211");
			// $query->execute();
			// $query_result = $query->fetchAll();
			
			// foreach($query_result as $kandidat){
				// $kandidatID = $kandidat['pk_kandidatid'];
				// $kandidati211[] = $kandidatID;
			// }
			
			// $unique_id211 = array_unique($kandidati211);
			// foreach($unique_id211 as $br_telefona){
				// echo $br_telefona . ",";
			// }
			
			
			
			// $kandidati213 = array();
			// $query2 = $db->prepare("SELECT pk_kandidatid 
									// FROM idk_project_kandidati 
									// JOIN idk_projects 
									// ON idk_project_kandidati.pk_projectid = idk_projects.project_id 
									// WHERE idk_projects.project_nalogid = 213");
			// $query2->execute();
			// $query_result2 = $query2->fetchAll();
			
			// foreach($query_result2 as $kandidat213){
				// $kandidatID213 = $kandidat213['pk_kandidatid'];
				// $kandidati213[] = $kandidatID213;
			// }
			
			// $unique_id213 = array_unique($kandidati213);
			
			// foreach($unique_id213 as $br_telefona213){
				// echo $br_telefona213 . ",";
			// }
			
			
			
			
			
			/* 
			odem na nalog 211 i odem na projekte gdje je spisak 
			
			tabela id_project_kandidati ima projekt id i id kandidata
			tabela id_projects ima nalog id
			uzeti sve projekte vezane za nalog 211 i onda naci sve kandidate vezane sa tim projektima 
			
			*/
		?>


<?php
	if(isset($_REQUEST['page'])){
		$page = $_REQUEST['page'];
	}
	
	switch($page){
		case "upload_document":
	$kandidat_id 		= $_POST['kandidat_id'];
	$nalog_id 			= $_POST['nalog_id'];
	$stari_status_id 	= $_POST['status_id'];
	$vrsta_dokumenta	= $_POST['dokument_select'];
	$uploaded_document 	= $_FILES['kandidat_dokument'];
	$vrijeme 			= date("y-m-d_H:i");
	$ext 				= explode(".", $uploaded_document['name']);
	$ext 				= strtolower(end($ext));
	$file_name 			= "UG".$kandidat_id.$vrijeme.".".$ext;
	$izvor				= 1;

	
	if($vrsta_dokumenta == 1){
		$location = $_SERVER['DOCUMENT_ROOT']."/jobstep_pp/files/candidate_contracts/";
		move_uploaded_file($uploaded_document["tmp_name"], $location.$file_name);

		if($stari_status_id == 7){
			$vrsta_ugovora = 0;
			$novi_status_id = 8;
		}
		else{
			$vrsta_ugovora = 1;
			$novi_status_id = 9;
		}
		$query_get_partner = $db->prepare("
									SELECT 
										ppa_company_id 
									FROM 
										idk_pp_partners 
									JOIN 
										idk_kandidati 
									ON 
										idk_kandidati.kandidat_ppa_partner_id = idk_pp_partners.ppa_id 
									WHERE 
										kandidat_id = :kandidat_id
								");
		$query_get_partner->execute(array(
			":kandidat_id" => $kandidat_id
		));
		$result_partner = $query_get_partner->fetch();
		$partner_id 	= $result_partner['ppa_company_id'];
		
		$query_get_project = $db->prepare("
									SELECT 
										project_id 
									FROM 
										idk_projects
									JOIN
										idk_project_kandidati
									ON
										idk_projects.project_id = idk_project_kandidati.pk_projectid
									WHERE
										idk_project_kandidati.pk_kandidatid = :kandidat_id
									AND
										idk_projects.project_nalogid = :nalog_id;
								");
		$query_get_project->execute(array(
			":kandidat_id" 	=> $kandidat_id,
			":nalog_id"		=> $nalog_id
		));
		$result_project = $query_get_project->fetch();
		$project_id = $result_project['project_id'];
		
		
		$query_insert_contract = $db->prepare("
										INSERT INTO 
											idk_kandidati_contracts 
											(
												kc_file_name, 
												kc_candidate_id, 
												kc_source, 
												kc_user_id, 
												kc_nalog_id, 
												kc_partner_id, 
												kc_signed, 
												kc_visibility_status
											)
										VALUES
											(
												:file_name,
												:candidate_id,
												:source,
												:user_id,
												:nalog_id,
												:partner_id,
												:signed,
												:visibility_status
											)
										");
		$query_insert_contract->execute(array(
			":file_name" 			=> $file_name,
			":candidate_id" 		=> $kandidat_id,
			":source" 				=> 1,
			":user_id" 				=> $logged_employee_id,
			":nalog_id" 			=> $nalog_id,
			":partner_id" 			=> $partner_id,
			":signed" 				=> $vrsta_ugovora,
			":visibility_status" 	=> 2
		));
		
		$query_update_kandidat_sp = $db->prepare("
											UPDATE
												idk_kandidati
											SET
												kandidat_status_prijave = :status_prijave
											WHERE
												kandidat_id = :kandidat_id
										");
		$query_update_kandidat_sp->execute(array(
			":status_prijave" 	=> $novi_status_id,
			":kandidat_id" 		=> $kandidat_id
		));
		addToLogsStatusPrijave($project_id, $project_id, $novi_status_id, $kandidat_id, $izvor);
		
	} else {
		$location = $_SERVER['DOCUMENT_ROOT']."/jobstep_pp/files/candidate_documents/";
	}
	 
	header("Location: nalozi?page=open_status_prijave&sid=".$stari_status_id."&nid=".$nalog_id);
break;
		
		case "testip":
			echo  $_SERVER['HTTP_CLIENT_IP'];
			// echo  $_SERVER['HTTP_X_FORWARDED_FOR'];
			echo $_SERVER['REMOTE_ADDR'];
		break;
		case "kandidati":
			$stmt = $db->prepare("SELECT pk_kandidatid FROM idk_project_kandidati WHERE pk_projectid IN (
			1756, 1757, 1758, 1759, 1760, 1761, 1762, 1763, 1764, 1765, 1786, 1787, 1788, 1810, 1853, 1776, 1777, 1778, 1779, 1780, 1781, 1782, 1783, 1784, 1785, 1864, 1800, 1801, 1802, 1803, 1804, 1805, 1806, 1807, 1808, 1809, 1811, 1812, 1813, 1814, 1815, 1816, 1817, 1818, 1819, 1820, 1821, 1852, 1822, 1823, 1824, 1825, 1826, 1827, 1828, 1829, 1830, 1831, 1877, 1832, 1833, 1834, 1835, 1836, 1837, 1838, 1839, 1840, 1841, 1879, 1880, 1842, 1843, 1844, 1845, 1846, 1847, 1848, 1849, 1850, 1851, 1865, 1866, 1878, 1854, 1855, 1856, 1857, 1858, 1859, 1860, 1861, 1862, 1863, 1881)");
			$stmt->execute();
			$kandidat_id_project = array();
			while($result = $stmt->fetch()){
				$kandidat_id = $result['pk_kandidatid'];
				$kandidat_id_project[] = $kandidat_id;
			}
			$kandidat_id_project_unique = array_unique($kandidat_id_project);
			$kandidat_id_project_uniqueImp = implode(",", $kandidat_id_project_unique);
			echo $kandidat_id_project_uniqueImp;
		break;
		case "unos_1":
			$kandidat_id_ciscenje = array();
			$stmt_provjera = $db->prepare("SELECT id_kandidata_dipl FROM idk_nd_kandidati_ciscenje");
			$stmt_provjera->execute();
			$result_provjera = $stmt_provjera->fetchAll();
			foreach($result_provjera as $kandidat_ciscenje){
				$kandidat_ciscenje_id = $kandidat_ciscenje['id_kandidata_dipl'];
				$kandidat_id_ciscenje[] = $kandidat_ciscenje_id;
			}
			$kandidat_id_ciscenjeImp;
			if ($kandidat_id_ciscenje != null) {
				$kandidat_id_ciscenjeImp = implode(",", $kandidat_id_ciscenje);
				$uslov_kandidata = " id_broj_nd_kandidata NOT IN (".$kandidat_id_ciscenjeImp.")";
			} else {
				$uslov_kandidata = " id_broj_nd_kandidata is not null ";
			}
			
			echo $uslov_kandidata. " <br><br><br><br>";
			
			//KANDIDATI KOJI NEMAJU JEZIK A IMAJU SKOLU
			$stmt_jezik = $db->prepare("
										SELECT 
											ime_nd_kandidata, 
											prezime_nd_kandidata, 
											id_broj_nd_kandidata, 
											mobilni_nd_kandidata 
										FROM 
											idk_nd_kandidata 
										WHERE 
											nivo_poznavanja_jezika IN (0) 
										AND 
											skola_nd_kandidata IS NOT NULL 
										AND 
											(mobilni_nd_kandidata LIKE '+49%' OR mobilni_nd_kandidata LIKE '+387%')
										AND
											".$uslov_kandidata."
										LIMIT 400
									");
			$stmt_jezik->execute();
			$count = $stmt_jezik->rowCount();
			echo $count . "<br><br>";
			$result_jezik = $stmt_jezik->fetchAll();
			foreach($result_jezik as $kandidat_jezik){
				
				$kandidat_jezik_id = $kandidat_jezik['id_broj_nd_kandidata'];
				$kandidat_jezik_id_hash = md5($kandidat_jezik['id_broj_nd_kandidata']);
				$kandidat_ime_jezik = $kandidat_jezik['ime_nd_kandidata'];
				$kandidat_prezime_jezik = $kandidat_jezik['prezime_nd_kandidata'];
				$kandidat_broj_telefona_jezik = $kandidat_jezik['mobilni_nd_kandidata'];

				echo $kandidat_jezik_id . " - " . $kandidat_ime_jezik . " " . $kandidat_prezime_jezik . " - " . $kandidat_broj_telefona_jezik . "<br><br>";
				$unos_kandidata = $db->prepare("INSERT INTO idk_nd_kandidati_ciscenje (id_kandidata_dipl, ime_kandidata, prezime_kandidata, hash_id_dipl, broj_telefona, status_informacija) 
												VALUES (:id_kandidata_dipl, :ime_kandidata, :prezime_kandidata, :hash_id_dipl, :broj_telefona, :status_informacija)");
				$unos_kandidata->execute(array(
					":id_kandidata_dipl" => $kandidat_jezik_id,
					":ime_kandidata" => $kandidat_ime_jezik,
					":prezime_kandidata" => $kandidat_prezime_jezik,
					":hash_id_dipl" => $kandidat_jezik_id_hash,
					":broj_telefona" => $kandidat_broj_telefona_jezik,
					":status_informacija" => 1
				));
			}			
		
		break;

		case "unos_2":
			$kandidat_id_ciscenje = array();
			$stmt_provjera = $db->prepare("SELECT id_kandidata_dipl FROM idk_nd_kandidati_ciscenje");
			$stmt_provjera->execute();
			$result_provjera = $stmt_provjera->fetchAll();
			foreach($result_provjera as $kandidat_ciscenje){
				$kandidat_ciscenje_id = $kandidat_ciscenje['id_kandidata_dipl'];
				$kandidat_id_ciscenje[] = $kandidat_ciscenje_id;
			}
			$kandidat_id_ciscenjeImp;
			if ($kandidat_id_ciscenje != null) {
				$kandidat_id_ciscenjeImp = implode(",", $kandidat_id_ciscenje);
				$uslov_kandidata = " id_broj_nd_kandidata NOT IN (".$kandidat_id_ciscenjeImp.")";
			} else {
				$uslov_kandidata = " id_broj_nd_kandidata is not null ";
			}
			
			echo $uslov_kandidata. " <br><br><br><br>";
			
			//KANDIDATI KOJI IMAJU JEZIK A NEMAJU SKOLU
			$stmt_skola = $db->prepare("
										SELECT 
											ime_nd_kandidata, 
											prezime_nd_kandidata, 
											id_broj_nd_kandidata,
											mobilni_nd_kandidata
										FROM 
											idk_nd_kandidata 
										WHERE 
											nivo_poznavanja_jezika IN (1,2,3,4,5,6,7) 
										AND 
											skola_nd_kandidata IS NULL
										AND 
											(mobilni_nd_kandidata LIKE '+49%' OR mobilni_nd_kandidata LIKE '+387%')
										AND
											".$uslov_kandidata."
										LIMIT 400
									");
			$stmt_skola->execute();
			$count = $stmt_skola->rowCount();
			echo $count . "<br><br>";
			$result_skola = $stmt_skola->fetchAll();
			foreach($result_skola as $kandidat_skola){
				
				$kandidat_skola_id = $kandidat_skola['id_broj_nd_kandidata'];
				$kandidat_skola_id_hash = md5($kandidat_skola['id_broj_nd_kandidata']);
				$kandidat_ime_skola = $kandidat_skola['ime_nd_kandidata'];
				$kandidat_prezime_skola = $kandidat_skola['prezime_nd_kandidata'];
				$kandidat_broj_telefona_skola = $kandidat_skola['mobilni_nd_kandidata'];

				echo $kandidat_skola_id . " - " . $kandidat_ime_skola . " " . $kandidat_prezime_skola . " - " . $kandidat_broj_telefona_skola . "<br>";
				$unos_kandidata2 = $db->prepare("INSERT INTO idk_nd_kandidati_ciscenje (id_kandidata_dipl, ime_kandidata, prezime_kandidata, hash_id_dipl, broj_telefona, status_informacija) 
												VALUES (:id_kandidata_dipl, :ime_kandidata, :prezime_kandidata, :hash_id_dipl, :broj_telefona, :status_informacija)");
				$unos_kandidata2->execute(array(
					":id_kandidata_dipl" => $kandidat_skola_id,
					":ime_kandidata" => $kandidat_ime_skola,
					":prezime_kandidata" => $kandidat_prezime_skola,
					":hash_id_dipl" => $kandidat_skola_id_hash,
					":broj_telefona" => $kandidat_broj_telefona_skola,
					":status_informacija" => 2
				));
			}
		break;

		case "unos_3":
			$kandidat_id_ciscenje = array();
			$stmt_provjera = $db->prepare("SELECT id_kandidata_dipl FROM idk_nd_kandidati_ciscenje");
			$stmt_provjera->execute();
			$result_provjera = $stmt_provjera->fetchAll();
			foreach($result_provjera as $kandidat_ciscenje){
				$kandidat_ciscenje_id = $kandidat_ciscenje['id_kandidata_dipl'];
				$kandidat_id_ciscenje[] = $kandidat_ciscenje_id;
			}
			$kandidat_id_ciscenjeImp;
			if ($kandidat_id_ciscenje != null) {
				$kandidat_id_ciscenjeImp = implode(",", $kandidat_id_ciscenje);
				$uslov_kandidata = " id_broj_nd_kandidata NOT IN (".$kandidat_id_ciscenjeImp.")";
			} else {
				$uslov_kandidata = " id_broj_nd_kandidata is not null ";
			}
			
			echo $uslov_kandidata. " <br><br><br><br>";

			//KANDIDATI KOJI NEMAJU NI JEZIK NI SKOLU
			$count = 0;
			$stmt_skola_jezik = $db->prepare("
												SELECT 
													ime_nd_kandidata, 
													prezime_nd_kandidata, 
													id_broj_nd_kandidata,
													mobilni_nd_kandidata													
												FROM 
													idk_nd_kandidata 
												WHERE 
													nivo_poznavanja_jezika IN (0) 
												AND 
													skola_nd_kandidata IS NULL
												AND 
													(mobilni_nd_kandidata LIKE '+49%' OR mobilni_nd_kandidata LIKE '+387%')
												AND 
													".$uslov_kandidata."
												ORDER BY id_broj_nd_kandidata ASC 
												LIMIT 400
											");
			$stmt_skola_jezik->execute();
			$count_skola_jezik = $stmt_skola_jezik->rowCount();
			echo $count_skola_jezik . "<br><br><br><br>"; 
			$zadnji_id_kan = 0;
			$zastavica = 0; 
			while($result_skola_jezik = $stmt_skola_jezik->fetch()){
				$count++;
				$kandidat_skola_jezik_id = $result_skola_jezik['id_broj_nd_kandidata'];
				$kandidat_skola_jezik_id_hash = md5($result_skola_jezik['id_broj_nd_kandidata']);
				$kandidat_ime_skola_jezik = $result_skola_jezik['ime_nd_kandidata'];
				$kandidat_prezime_skola_jezik = $result_skola_jezik['prezime_nd_kandidata'];
				$kandidat_broj_skola_jezik = $result_skola_jezik['mobilni_nd_kandidata'];
				
				if($zadnji_id_kan == 0 ){
					$zastavica = 1;
				}else if($zadnji_id_kan != $kandidat_skola_jezik_id){
					$zastavica = 1;
				}else{
					$zastavica = 0;
					continue;
				}
				
				if($count <= $count_skola_jezik AND $zastavica == 1){
					echo $count . ": " .$kandidat_skola_jezik_id . " " . $kandidat_ime_skola_jezik . " " . $kandidat_prezime_skola_jezik . " " . $kandidat_broj_skola_jezik . " " .$zadnji_id_kan. "<br>";
					$unos_kandidata3 = $db->prepare("
						INSERT INTO idk_nd_kandidati_ciscenje 
							(
								id_kandidata_dipl, 
								ime_kandidata, 
								prezime_kandidata, 
								hash_id_dipl, 
								broj_telefona, 
								status_informacija
							) 
						VALUES 
							(
								:id_kandidata_dipl, 
								:ime_kandidata, 
								:prezime_kandidata, 
								:hash_id_dipl, 
								:broj_telefona, 
								:status_informacija
							)
					");
					$unos_kandidata3->execute(array(
						':id_kandidata_dipl' => $kandidat_skola_jezik_id,
						':ime_kandidata' => $kandidat_ime_skola_jezik,
						':prezime_kandidata' => $kandidat_prezime_skola_jezik,
						':hash_id_dipl' => $kandidat_skola_jezik_id_hash,
						':broj_telefona' => $kandidat_broj_skola_jezik,
						':status_informacija' => 3
					));
					$zadnji_id_kan = $kandidat_skola_jezik_id;
				}else{
					break;
				}
			}
			
			if($count == $count_skola_jezik){
				echo "<br><br><br><br>Zaustavljeno!";
				exit();
			}
	
		break;

		case "unos_4":
			$kandidat_id_ciscenje = array();
			$stmt_provjera = $db->prepare("SELECT id_kandidata_dipl FROM idk_nd_kandidati_ciscenje");
			$stmt_provjera->execute();
			$result_provjera = $stmt_provjera->fetchAll();
			foreach($result_provjera as $kandidat_ciscenje){
				$kandidat_ciscenje_id = $kandidat_ciscenje['id_kandidata_dipl'];
				$kandidat_id_ciscenje[] = $kandidat_ciscenje_id;
			}
			$kandidat_id_ciscenjeImp;
			if ($kandidat_id_ciscenje != null) {
				$kandidat_id_ciscenjeImp = implode(",", $kandidat_id_ciscenje);
				$uslov_kandidata = " id_broj_nd_kandidata NOT IN (".$kandidat_id_ciscenjeImp.")";
			} else {
				$uslov_kandidata = " id_broj_nd_kandidata is not null ";
			}
			
			echo $uslov_kandidata. " <br><br><br><br>";

			//KANDIDATI KOJI NEMAJU JEZIK A IMAJU SKOLU
			$stmt_jezik = $db->prepare("
										SELECT 
											ime_nd_kandidata, 
											prezime_nd_kandidata, 
											id_broj_nd_kandidata, 
											mobilni_nd_kandidata 
										FROM 
											idk_nd_kandidata 
										WHERE 
											nivo_poznavanja_jezika IN (0) 
										AND 
											skola_nd_kandidata IS NOT NULL 
										AND
											mobilni_nd_kandidata LIKE '+%'
										AND
											".$uslov_kandidata."
										LIMIT 400
									");
			$stmt_jezik->execute();
			$count = $stmt_jezik->rowCount();
			echo $count . "<br><br>";
			$result_jezik = $stmt_jezik->fetchAll();
			foreach($result_jezik as $kandidat_jezik){
				
				$kandidat_jezik_id = $kandidat_jezik['id_broj_nd_kandidata'];
				$kandidat_jezik_id_hash = md5($kandidat_jezik['id_broj_nd_kandidata']);
				$kandidat_ime_jezik = $kandidat_jezik['ime_nd_kandidata'];
				$kandidat_prezime_jezik = $kandidat_jezik['prezime_nd_kandidata'];
				$kandidat_broj_telefona_jezik = $kandidat_jezik['mobilni_nd_kandidata'];
				
				echo $kandidat_jezik_id . " - " . $kandidat_ime_jezik . " " . $kandidat_prezime_jezik . " - " . $kandidat_broj_telefona_jezik . "<br>";
				$unos_kandidata = $db->prepare("INSERT INTO idk_nd_kandidati_ciscenje (id_kandidata_dipl, ime_kandidata, prezime_kandidata, hash_id_dipl, broj_telefona, status_informacija) 
												VALUES (:id_kandidata_dipl, :ime_kandidata, :prezime_kandidata, :hash_id_dipl, :broj_telefona, :status_informacija)");
				$unos_kandidata->execute(array(
					":id_kandidata_dipl" => $kandidat_jezik_id,
					":ime_kandidata" => $kandidat_ime_jezik,
					":prezime_kandidata" => $kandidat_prezime_jezik,
					":hash_id_dipl" => $kandidat_jezik_id_hash,
					":broj_telefona" => $kandidat_broj_telefona_jezik,
					":status_informacija" => 1
				));
			}	
		break;

		case "unos_5":
			$kandidat_id_ciscenje = array();
			$stmt_provjera = $db->prepare("SELECT id_kandidata_dipl FROM idk_nd_kandidati_ciscenje");
			$stmt_provjera->execute();
			$result_provjera = $stmt_provjera->fetchAll();
			foreach($result_provjera as $kandidat_ciscenje){
				$kandidat_ciscenje_id = $kandidat_ciscenje['id_kandidata_dipl'];
				$kandidat_id_ciscenje[] = $kandidat_ciscenje_id;
			}
			$kandidat_id_ciscenjeImp;
			if ($kandidat_id_ciscenje != null) {
				$kandidat_id_ciscenjeImp = implode(",", $kandidat_id_ciscenje);
				$uslov_kandidata = " id_broj_nd_kandidata NOT IN (".$kandidat_id_ciscenjeImp.")";
			} else {
				$uslov_kandidata = " id_broj_nd_kandidata is not null ";
			}
			
			echo $uslov_kandidata. " <br><br><br><br>";

			//KANDIDATI KOJI IMAJU JEZIK A NEMAJU SKOLU
			$stmt_skola = $db->prepare("
										SELECT 
											ime_nd_kandidata, 
											prezime_nd_kandidata, 
											id_broj_nd_kandidata,
											mobilni_nd_kandidata
										FROM 
											idk_nd_kandidata 
										WHERE 
											nivo_poznavanja_jezika IN (1,2,3,4,5,6,7) 
										AND 
											skola_nd_kandidata IS NULL
										AND
											mobilni_nd_kandidata LIKE '+%'
										AND
											".$uslov_kandidata."
										LIMIT 400
									");
			$stmt_skola->execute();
			$count = $stmt_skola->rowCount();
			echo $count . "<br><br>";
			$result_skola = $stmt_skola->fetchAll();
			foreach($result_skola as $kandidat_skola){
				
				$kandidat_skola_id = $kandidat_skola['id_broj_nd_kandidata'];
				$kandidat_skola_id_hash = md5($kandidat_skola['id_broj_nd_kandidata']);
				$kandidat_ime_skola = $kandidat_skola['ime_nd_kandidata'];
				$kandidat_prezime_skola = $kandidat_skola['prezime_nd_kandidata'];
				$kandidat_broj_telefona_skola = $kandidat_skola['mobilni_nd_kandidata'];
				
				echo $kandidat_skola_id . " - " . $kandidat_ime_skola . " " . $kandidat_prezime_skola . " - " . $kandidat_broj_telefona_skola . "<br>";
				$unos_kandidata2 = $db->prepare("INSERT INTO idk_nd_kandidati_ciscenje (id_kandidata_dipl, ime_kandidata, prezime_kandidata, hash_id_dipl, broj_telefona, status_informacija) 
												VALUES (:id_kandidata_dipl, :ime_kandidata, :prezime_kandidata, :hash_id_dipl, :broj_telefona, :status_informacija)");
				$unos_kandidata2->execute(array(
					":id_kandidata_dipl" => $kandidat_skola_id,
					":ime_kandidata" => $kandidat_ime_skola,
					":prezime_kandidata" => $kandidat_prezime_skola,
					":hash_id_dipl" => $kandidat_skola_id_hash,
					":broj_telefona" => $kandidat_broj_telefona_skola,
					":status_informacija" => 2
				));
			}


		break;

		case "unos_6":
			$kandidat_id_ciscenje = array();
			$stmt_provjera = $db->prepare("SELECT id_kandidata_dipl FROM idk_nd_kandidati_ciscenje");
			$stmt_provjera->execute();
			$result_provjera = $stmt_provjera->fetchAll();
			foreach($result_provjera as $kandidat_ciscenje){
				$kandidat_ciscenje_id = $kandidat_ciscenje['id_kandidata_dipl'];
				$kandidat_id_ciscenje[] = $kandidat_ciscenje_id;
			}
			$kandidat_id_ciscenjeImp;
			if ($kandidat_id_ciscenje != null) {
				$kandidat_id_ciscenjeImp = implode(",", $kandidat_id_ciscenje);
				$uslov_kandidata = " id_broj_nd_kandidata NOT IN (".$kandidat_id_ciscenjeImp.")";
			} else {
				$uslov_kandidata = " id_broj_nd_kandidata is not null ";
			}
			
			echo $uslov_kandidata. " <br><br><br><br>";

			//KANDIDATI KOJI NEMAJU NI JEZIK NI SKOLU
			$count = 0;
			$stmt_skola_jezik = $db->prepare("
												SELECT 
													ime_nd_kandidata, 
													prezime_nd_kandidata, 
													id_broj_nd_kandidata,
													mobilni_nd_kandidata													
												FROM 
													idk_nd_kandidata 
												WHERE 
													nivo_poznavanja_jezika IN (0) 
												AND 
													skola_nd_kandidata IS NULL
												AND
													mobilni_nd_kandidata LIKE '+%' 
												AND
													".$uslov_kandidata."
												ORDER BY id_broj_nd_kandidata ASC 
												LIMIT 400
											");
			$stmt_skola_jezik->execute();
			$count_skola_jezik = $stmt_skola_jezik->rowCount();
			echo $count_skola_jezik . "<br><br><br><br>"; 
			$zadnji_id_kan = 0;
			$zastavica = 0; 
			while($result_skola_jezik = $stmt_skola_jezik->fetch()){
				$count++;
				$kandidat_skola_jezik_id = $result_skola_jezik['id_broj_nd_kandidata'];
				$kandidat_skola_jezik_id_hash = md5($result_skola_jezik['id_broj_nd_kandidata']);
				$kandidat_ime_skola_jezik = $result_skola_jezik['ime_nd_kandidata'];
				$kandidat_prezime_skola_jezik = $result_skola_jezik['prezime_nd_kandidata'];
				$kandidat_broj_skola_jezik = $result_skola_jezik['mobilni_nd_kandidata'];
				
				if($zadnji_id_kan == 0 ){
					$zastavica = 1;
				}else if($zadnji_id_kan != $kandidat_skola_jezik_id){
					$zastavica = 1;
				}else{
					$zastavica = 0;
					continue;
				}
				
				if($count <= $count_skola_jezik AND $zastavica == 1){
					echo $count . ": " .$kandidat_skola_jezik_id . " " . $kandidat_ime_skola_jezik . " " . $kandidat_prezime_skola_jezik . " " . $kandidat_broj_skola_jezik . " " .$zadnji_id_kan. "<br>";
					$unos_kandidata3 = $db->prepare("
						INSERT INTO idk_nd_kandidati_ciscenje 
							(
								id_kandidata_dipl, 
								ime_kandidata, 
								prezime_kandidata, 
								hash_id_dipl, 
								broj_telefona, 
								status_informacija
							) 
						VALUES 
							(
								:id_kandidata_dipl, 
								:ime_kandidata, 
								:prezime_kandidata, 
								:hash_id_dipl, 
								:broj_telefona, 
								:status_informacija
							)
					");
					$unos_kandidata3->execute(array(
						':id_kandidata_dipl' => $kandidat_skola_jezik_id,
						':ime_kandidata' => $kandidat_ime_skola_jezik,
						':prezime_kandidata' => $kandidat_prezime_skola_jezik,
						':hash_id_dipl' => $kandidat_skola_jezik_id_hash,
						':broj_telefona' => $kandidat_broj_skola_jezik,
						':status_informacija' => 3
					));
					$zadnji_id_kan = $kandidat_skola_jezik_id;
				}else{
					break;
				}
			}
			
			if($count == $count_skola_jezik){
				echo "<br><br><br><br>Zaustavljeno!";
				exit();
			}

		break;

		case "unos_7":
			$kandidat_id_ciscenje = array();
			$stmt_provjera = $db->prepare("SELECT id_kandidata_dipl FROM idk_nd_kandidati_ciscenje");
			$stmt_provjera->execute();
			$result_provjera = $stmt_provjera->fetchAll();
			foreach($result_provjera as $kandidat_ciscenje){
				$kandidat_ciscenje_id = $kandidat_ciscenje['id_kandidata_dipl'];
				$kandidat_id_ciscenje[] = $kandidat_ciscenje_id;
			}
			$kandidat_id_ciscenjeImp;
			if ($kandidat_id_ciscenje != null) {
				$kandidat_id_ciscenjeImp = implode(",", $kandidat_id_ciscenje);
				$uslov_kandidata = " id_broj_nd_kandidata NOT IN (".$kandidat_id_ciscenjeImp.")";
			} else {
				$uslov_kandidata = " id_broj_nd_kandidata is not null ";
			}
			
			echo $uslov_kandidata. " <br><br><br><br>";

			//KANDIDATI KOJI IMAJU SVE
			$stmt_jezik = $db->prepare("
										SELECT 
											ime_nd_kandidata, 
											prezime_nd_kandidata, 
											id_broj_nd_kandidata, 
											mobilni_nd_kandidata 
										FROM 
											idk_nd_kandidata 
										WHERE 
											nivo_poznavanja_jezika IN (1,2,3,4,5,6,7) 
										AND 
											skola_nd_kandidata IS NOT NULL 
										AND
											mobilni_nd_kandidata LIKE '+%'
										AND
											".$uslov_kandidata."
										LIMIT 400
									");
			$stmt_jezik->execute();
			$count = $stmt_jezik->rowCount();
			echo $count . "<br><br>";
			$result_jezik = $stmt_jezik->fetchAll();
			foreach($result_jezik as $kandidat_jezik){
				
				$kandidat_jezik_id = $kandidat_jezik['id_broj_nd_kandidata'];
				$kandidat_jezik_id_hash = md5($kandidat_jezik['id_broj_nd_kandidata']);
				$kandidat_ime_jezik = $kandidat_jezik['ime_nd_kandidata'];
				$kandidat_prezime_jezik = $kandidat_jezik['prezime_nd_kandidata'];
				$kandidat_broj_telefona_jezik = $kandidat_jezik['mobilni_nd_kandidata'];
				
				echo $kandidat_jezik_id . " - " . $kandidat_ime_jezik . " " . $kandidat_prezime_jezik . " - " . $kandidat_broj_telefona_jezik . "<br>";
				
				$unos_kandidata = $db->prepare("INSERT INTO idk_nd_kandidati_ciscenje (id_kandidata_dipl, ime_kandidata, prezime_kandidata, hash_id_dipl, broj_telefona, status_informacija) 
												VALUES (:id_kandidata_dipl, :ime_kandidata, :prezime_kandidata, :hash_id_dipl, :broj_telefona, :status_informacija)");
				$unos_kandidata->execute(array(
					":id_kandidata_dipl" => $kandidat_jezik_id,
					":ime_kandidata" => $kandidat_ime_jezik,
					":prezime_kandidata" => $kandidat_prezime_jezik,
					":hash_id_dipl" => $kandidat_jezik_id_hash,
					":broj_telefona" => $kandidat_broj_telefona_jezik,
					":status_informacija" => 0
				));
			}			

		break;

		case "duplikati":
			
			$query_broj_duplikata = $db->prepare("SELECT * FROM (SELECT
																	id_kandidata_posao,
																	COUNT(kandidat_ciscenje_id) AS broj
																FROM
																	idk_nd_kandidati_ciscenje
																WHERE
																	id_kandidata_posao IS NOT NULL 
																GROUP BY
																	id_kandidata_posao) as result
													 WHERE broj > 1
												");
			$query_broj_duplikata->execute();

					$br=1;
			while ($result_broj_duplikata = $query_broj_duplikata->fetch()){
				$id_kandidata_posao = $result_broj_duplikata['id_kandidata_posao'];
				$broj = $result_broj_duplikata['broj'];
				
					
					$query_id_kandidata_dipl = $db->prepare("
																SELECT 
																	id_kandidata_dipl
																FROM 
																	idk_nd_kandidati_ciscenje
																WHERE 
																	id_kandidata_posao = :id_kandidata_posao
															");
					$query_id_kandidata_dipl->execute(array(
						":id_kandidata_posao" => $id_kandidata_posao
					));
					$count = $query_id_kandidata_dipl->rowCount();
					$ctr = 1;
					while($result_id_kandidata_dipl = $query_id_kandidata_dipl->fetch()){
						$id_kandidata_dipl = $result_id_kandidata_dipl['id_kandidata_dipl'];
						 
						$query_status_nd_kandidata = $db->prepare("
																	SELECT
																		status_nd_kandidata
																	FROM 
																		idk_nd_kandidata
																	WHERE 
																		id_broj_nd_kandidata = :id_kandidata_dipl
																");
						$query_status_nd_kandidata->execute(array(
							":id_kandidata_dipl" => $id_kandidata_dipl
						));
						$result_status_nd_kandidata = $query_status_nd_kandidata->fetch();
						
						if($ctr == 1){
							$stari_status = $result_status_nd_kandidata['status_nd_kandidata'];
							$stari_id = $id_kandidata_dipl;
							echo $br++." nistaaa<br>";
						} else {
							$trenutni_status = $result_status_nd_kandidata['status_nd_kandidata'];
							$trenutni_id = $id_kandidata_dipl;
							if($trenutni_status == 7 AND $stari_status != 7){
								echo $br++." ".$ctr . " Kandidat id: " . $stari_id . " ima trenutni status: " . $trenutni_status . " i stari status: " . $stari_status . " Ovo je slucaj gdje se uzima stari id (".$stari_id.") za update!" . "<br>";
								echo "Arhiviram " . $trenutni_id . "<br>";
								
								$query_arhiva_trenutnog = $db->prepare("UPDATE idk_nd_kandidati_ciscenje SET arhiva = 1 WHERE id_kandidata_dipl = :id_kandidata_dipl");
								$query_arhiva_trenutnog->execute(array(
									":id_kandidata_dipl" => $trenutni_id
								));
							} else {
								echo $br++." ".$ctr . " Kandidat id: " . $trenutni_id . " ima trenutni status: " . $trenutni_status . " i stari status: " . $stari_status . " Ovo je slucaj gdje se uzima novi id (".$trenutni_id.") za update!" . "<br>";
								echo "Arhiviram " . $stari_id . "<br>";
								
								$query_arhiva_starog = $db->prepare("UPDATE idk_nd_kandidati_ciscenje SET arhiva = 1 WHERE id_kandidata_dipl = :id_kandidata_dipl");
								$query_arhiva_starog->execute(array(
									":id_kandidata_dipl" => $stari_id
								));
								$stari_status = $trenutni_status;
								$stari_id = $trenutni_id;
								
							}
						}
						$ctr++;
					}
			}
		break;
		case "provjera_jezika":
			
			//ID KANDIDIDATA ZA POSAO IZ TABELE idk_nd_kandidati_ciscenje
			$kandidat_id_posao = array();
			$stmt_kandidat_id_posao = $db->prepare("
													SELECT
														id_kandidata_posao
													FROM
														idk_nd_kandidati_ciscenje
													WHERE
														status_informacija IN (1,3) 
													AND 
														id_kandidata_posao IS NOT NULL
													AND 
														arhiva = 0
													AND 
														treba_prebaciti_jezik_u_dipl IS NULL
													AND 
														znanje_jezika_iz_idk_kan_jezici IS NULL
												");
			$stmt_kandidat_id_posao->execute();
			while($result_kandidat_id_posao = $stmt_kandidat_id_posao->fetch()){
				$id_kandidata = $result_kandidat_id_posao['id_kandidata_posao'];
				$kandidat_id_posao[] = $id_kandidata;
			}
			$kandidat_id_posaoImp = implode(",", $kandidat_id_posao);
			// echo count($kandidat_id_posao);
			// exit();
		
			
			//PROVJERA KOJI KANDIDATI IMAJU NJEMACKI KORISTECI ID KANDIDATA ZA POSAO IZ TABELE idk_nd_kandidati_ciscenje
			$stmt_kandidati_njemacki = $db->prepare("
													SELECT
														kj_naziv,
														kj_slusanje,
														kj_kandidatid
													FROM
														idk_kandidat_jezici
													WHERE
														kj_naziv LIKE 'Njemački'
													AND
														kj_kandidatid IN (".$kandidat_id_posaoImp.")
													LIMIT 300
												");
			$stmt_kandidati_njemacki->execute();
			$id = array();
			$count = $stmt_kandidati_njemacki->rowCount();
			//echo "Ukupan broj: " . $count . "<br><br><br><br>";
			while($result_kandidati_njemacki = $stmt_kandidati_njemacki->fetch()){
				$njemacki = $result_kandidati_njemacki['kj_naziv'];
				$kandidat_id = $result_kandidati_njemacki['kj_kandidatid'];
				$nivo_jezika = $result_kandidati_njemacki['kj_slusanje'];
				$id[] = $kandidat_id;
				echo $kandidat_id . " - " . $njemacki . " - " . $nivo_jezika . " Update kolone treba prebaciti u dipl na 1" . "<br>";
				
				$stmt_update = $db->prepare("UPDATE idk_nd_kandidati_ciscenje SET treba_prebaciti_jezik_u_dipl = 1, znanje_jezika_iz_idk_kan_jezici = :nivo_jezika 
											WHERE id_kandidata_posao = :id_kandidata_posao AND arhiva = 0 ");
				$stmt_update->execute(array(
					":nivo_jezika" => $nivo_jezika,
					":id_kandidata_posao" => $kandidat_id
				));
			}
			$unique_id = array_unique($id);
			echo count($unique_id) . "<br>";
			//echo count($id);
		break;
		
		case "provjera_skole":
		
			//ID KANDIDIDATA ZA POSAO IZ TABELE idk_nd_kandidati_ciscenje
			$kandidat_id_posao = array();
			$stmt_kandidat_id_posao = $db->prepare("
													SELECT
														id_kandidata_posao
													FROM
														idk_nd_kandidati_ciscenje
													WHERE
														status_informacija IN (2,3)
													AND 
														id_kandidata_posao IS NOT NULL
													AND 
														arhiva = 0
													AND 
														treba_prebaciti_skolu_smjer_u_dipl IS NULL
												");
			$stmt_kandidat_id_posao->execute();
			while($result_kandidat_id_posao = $stmt_kandidat_id_posao->fetch()){
				$id_kandidata = $result_kandidat_id_posao['id_kandidata_posao'];
				$kandidat_id_posao[] = $id_kandidata;
			}
			$kandidat_id_posaoImp = implode(",", $kandidat_id_posao);
			
			
			//PROVJERA KOJI KANDIDATI IMAJU SKOLU KORISTECI ID KANDIDATA ZA POSAO IZ TABELE idk_nd_kandidati_ciscenje
			?>
			<table>
				<thead>
					<tr>
						<th>ID kandidata</th>
						<th>Ime Skole</th>
						<th>ID Skole</th>
						<th>Ime Smjera</th>
						<th>ID Smjera</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$skole_kandidata = array();
					$stmt_kandidati_skola = $db->prepare("
															SELECT 
																ke_naziv,
																ke_naziv_kvalifikacije,
																ke_kandidat_id,
																ke_smjer_id,
																ke_skola_id
															FROM
																idk_kandidat_edukacija
															WHERE
																ke_kandidat_id IN (".$kandidat_id_posaoImp.")
															AND
																ke_smjer_id IS NOT NULL
															AND 
																ke_skola_id IS NOT NULL
															ORDER BY ke_skola_id
															LIMIT 200
															
														");
					$stmt_kandidati_skola->execute();
					$ukupno = $stmt_kandidati_skola->rowCount();
					echo "Ukupan broj: " . $ukupno . "<br><br><br><br>";
					while($result_kandidati_skola = $stmt_kandidati_skola->fetch()){
						$naziv_skole = $result_kandidati_skola['ke_naziv'];
						$naziv_smjera = $result_kandidati_skola['ke_naziv_kvalifikacije'];
						$id_kandidata = $result_kandidati_skola['ke_kandidat_id'];
						$id_skole = $result_kandidati_skola['ke_skola_id'];
						$id_smjera = $result_kandidati_skola['ke_smjer_id'];
						$skole_kandidata[] = $id_kandidata;
						
						$stmt_update_skole = $db->prepare("UPDATE idk_nd_kandidati_ciscenje SET treba_prebaciti_skolu_smjer_u_dipl = 1, skola = :skola, smjer = :smjer
															WHERE id_kandidata_posao = :id_kandidata_posao AND arhiva = 0");
						$stmt_update_skole->execute(array(
							":skola" => $id_skole,
							":smjer" => $id_smjera,
							":id_kandidata_posao" => $id_kandidata
						));
						?>
						<tr>
							<td><?php echo $id_kandidata;?></td>
							<td><?php echo $naziv_skole;?></td>
							<td><?php echo $id_skole;?></td>
							<td><?php echo $naziv_smjera;?></td>
							<td><?php echo $id_smjera;?></td>
						</tr>
					<?php	
					}
					?>
				</tbody>
			</table>
		<?php
		$skole_kandidata_unique = array_unique($skole_kandidata);
			echo count($skole_kandidata_unique);
		break;
		
		case "prebacivanje_dipl_jezik":
		
			$stmt_kandidati_jezik = $db->prepare("SELECT
														znanje_jezika_iz_idk_kan_jezici,
														id_kandidata_dipl
													FROM
														idk_nd_kandidati_ciscenje
													WHERE
														arhiva = 0
													AND
														treba_prebaciti_jezik_u_dipl = 1
													AND
														treba_prebaciti_skolu_smjer_u_dipl IS NULL
													AND 
														status_informacija IN (1,3)
													LIMIT 400");
			$stmt_kandidati_jezik->execute();
			$count = $stmt_kandidati_jezik->rowCount();
			echo "Ukupan broj: " . $count . "<br><br><br>";
			while($result_kandidati_jezik = $stmt_kandidati_jezik->fetch()){
				$id_kandidata_dipl = $result_kandidati_jezik['id_kandidata_dipl'];
				$nivo_jezika = $result_kandidati_jezik['znanje_jezika_iz_idk_kan_jezici'];
				
				switch($nivo_jezika){
					case "Bez znanja":
						$jezik_dipl = 1;
					break;
					
					case "A1":
						$jezik_dipl = 2;
					break;
					
					case "A2":
						$jezik_dipl = 3;
					break;
					
					case "B1":
						$jezik_dipl = 4;
					break;
					
					case "B2":
						$jezik_dipl = 5;
					break;
					
					case "C1":
						$jezik_dipl = 6;
					break;
					
					case "C2":
						$jezik_dipl = 7;
					break;
				}
				echo "Update statusa_informacija na 2 i treba_prebaciti_jezik_u_dipl na 2 u idk_nd_kandidati_ciscenje kod kandidata: " . $id_kandidata_dipl . "<br>";
				$stmt_update_ciscenje = $db->prepare("UPDATE idk_nd_kandidati_ciscenje SET status_informacija = 2, treba_prebaciti_jezik_u_dipl = 2 
													WHERE id_kandidata_dipl = :id_kandidata_dipl AND arhiva = 0");
				$stmt_update_ciscenje->execute(array(
					":id_kandidata_dipl" => $id_kandidata_dipl
				));
				echo "Update jezika u idk_nd_kandidata na: " . $jezik_dipl . " - " . $nivo_jezika . " " . " gdje id: " . $id_kandidata_dipl . "<br>";
				$stmt_update_dipl = $db->prepare("UPDATE idk_nd_kandidata SET nivo_poznavanja_jezika = :jezik_dipl WHERE id_broj_nd_kandidata = :id_kandidata_dipl");
				$stmt_update_dipl->execute(array(
					":jezik_dipl" => $jezik_dipl,
					":id_kandidata_dipl" => $id_kandidata_dipl
				));
			}
		break;
		
		case "prebacivanje_dipl_skola":

			$stmt_kandidati_skola = $db->prepare("SELECT
													id_kandidata_dipl,
													skola,
													smjer
												FROM
													idk_nd_kandidati_ciscenje
												WHERE
													arhiva = 0
												AND
													treba_prebaciti_jezik_u_dipl IS NULL
												AND 
													treba_prebaciti_skolu_smjer_u_dipl = 1
												AND
													status_informacija IN (2,3)
													LIMIT 100");
			$stmt_kandidati_skola->execute();
			$count = $stmt_kandidati_skola->rowCount();
			echo "Ukupan broj: " . $count . "<br><br><br>";
			while($result_kandidati_skola = $stmt_kandidati_skola->fetch()){
				$id_kandidata_dipl = $result_kandidati_skola['id_kandidata_dipl'];
				$id_skole = $result_kandidati_skola['skola'];
				$id_smjer = $result_kandidati_skola['smjer'];
				
				echo "Update statusa_informacija na 1 i treba_prebaciti_skolu_smjer_u_dipl na 2 u idk_nd_kandidati_ciscenje kod kandidata: " . $id_kandidata_dipl . "<br>";
				$stmt_update_ciscenje = $db->prepare("UPDATE idk_nd_kandidati_ciscenje SET status_informacija = 1, treba_prebaciti_skolu_smjer_u_dipl = 2
													WHERE id_kandidata_dipl = :id_kandidata_dipl AND arhiva = 0");
				$stmt_update_ciscenje->execute(array(
					":id_kandidata_dipl" => $id_kandidata_dipl
				));
				echo "Update skole u idk_nd_kandidata na: " . $id_skole . " i  smjera na: " . $id_smjer . " gdje id: " . $id_kandidata_dipl . "<br>";
				$stmt_update_dipl = $db->prepare("UPDATE idk_nd_kandidata SET skola_nd_kandidata = :skola_id, skola_smjer_nd_kandidata = :smjer_id
												WHERE id_broj_nd_kandidata = :id_kandidata_dipl");
				$stmt_update_dipl->execute(array(
					":skola_id" => $id_skole,
					":smjer_id" => $id_smjer,
					":id_kandidata_dipl" => $id_kandidata_dipl
				));
			}
		break;
		
		case "prebacivanje_dipl_skola_jezik":
			$stmt_kandidati_skola_jezik = $db->prepare("SELECT
															id_kandidata_dipl,
															znanje_jezika_iz_idk_kan_jezici,
															skola,
															smjer
														FROM
															idk_nd_kandidati_ciscenje
														WHERE
															arhiva = 0
														AND
															treba_prebaciti_jezik_u_dipl = 1
														AND
															treba_prebaciti_skolu_smjer_u_dipl = 1
														AND
															status_informacija = 3
															LIMIT 100");
			$stmt_kandidati_skola_jezik->execute();
			$count = $stmt_kandidati_skola_jezik->rowCount();
			echo "Ukupan broj: " . $count . "<br><br><br>";
			while($result_kandidati_skola_jezik = $stmt_kandidati_skola_jezik->fetch()){
				$id_kandidata_dipl = $result_kandidati_skola_jezik['id_kandidata_dipl'];
				$nivo_jezika = $result_kandidati_skola_jezik['znanje_jezika_iz_idk_kan_jezici'];
				$id_skole = $result_kandidati_skola_jezik['skola'];
				$id_smjer = $result_kandidati_skola_jezik['smjer'];
				
				switch($nivo_jezika){
					case "Bez znanja":
						$jezik_dipl = 1;
					break;
					
					case "A1":
						$jezik_dipl = 2;
					break;
					
					case "A2":
						$jezik_dipl = 3;
					break;
					
					case "B1":
						$jezik_dipl = 4;
					break;
					
					case "B2":
						$jezik_dipl = 5;
					break;
					
					case "C1":
						$jezik_dipl = 6;
					break;
					
					case "C2":
						$jezik_dipl = 7;
					break;
				}
				echo "Update status_informacija na 0, treba_prebaciti_jezik_u_dipl na 2, treba_prebaciti_skolu_smjer_u_dipl na 2 u idk_nd_kandidati_ciscenje gdje id_kandidata_dipl: " . $id_kandidata_dipl . "<br>";
				
				$stmt_update_ciscenje = $db->prepare("UPDATE idk_nd_kandidati_ciscenje SET treba_prebaciti_jezik_u_dipl = 2, treba_prebaciti_skolu_smjer_u_dipl = 2, status_informacija = 0
													WHERE id_kandidata_dipl = :id_kandidata_dipl AND arhiva = 0");
				$stmt_update_ciscenje->execute(array(
					":id_kandidata_dipl" => $id_kandidata_dipl
				));
				
				echo "Update jezika na: " . $jezik_dipl . " i skole na: " . $id_skole . " i smjera na: " . $id_smjer . " u idk_nd_kandidata gdje kandidat id: " . $id_kandidata_dipl . "<br>";
				
				$stmt_update_dipl = $db->prepare("UPDATE idk_nd_kandidata SET nivo_poznavanja_jezika = :nivo_jezika, skola_nd_kandidata = :skola_id, skola_smjer_nd_kandidata = :smjer_id
												WHERE id_broj_nd_kandidata = :id_kandidata_dipl");
				$stmt_update_dipl->execute(array(
					":nivo_jezika" => $jezik_dipl,
					":skola_id" => $id_skole,
					":smjer_id" => $id_smjer,
					":id_kandidata_dipl" => $id_kandidata_dipl
				));
			}
		break;
		
		case "provjera_jezika_dipl_1":
			$get_id_dipl = $db->prepare("SELECT 
											id_kandidata_dipl,
											nivo_poznavanja_jezika 
										FROM 
											idk_nd_kandidati_ciscenje
										JOIN
											idk_nd_kandidata
										ON
											idk_nd_kandidati_ciscenje.id_kandidata_dipl = idk_nd_kandidata.id_broj_nd_kandidata 
										WHERE 
											status_informacija = 1
										AND
											nivo_poznavanja_jezika NOT IN (0)
										AND
											arhiva = 0
										");
			$get_id_dipl->execute();
			$num = $get_id_dipl->rowCount();
			echo $num . "<br><br><br>";
			while($result = $get_id_dipl->fetch()){
				$id_kandidata_dipl = $result['id_kandidata_dipl'];
				echo $id_kandidata_dipl . " - update status_informacija na 0 - " . $result['nivo_poznavanja_jezika'] . "<br>";
				$update_status_inf = $db->prepare("UPDATE idk_nd_kandidati_ciscenje SET status_informacija = 0 WHERE id_kandidata_dipl = :id_kandidata_dipl");
				$update_status_inf->execute(array(
					":id_kandidata_dipl" => $id_kandidata_dipl
				));
			}

		break;

		case "provjera_jezika_dipl_3":
				$get_id_dipl = $db->prepare("SELECT 
											id_kandidata_dipl,
											nivo_poznavanja_jezika 
										FROM 
											idk_nd_kandidati_ciscenje
										JOIN
											idk_nd_kandidata
										ON
											idk_nd_kandidati_ciscenje.id_kandidata_dipl = idk_nd_kandidata.id_broj_nd_kandidata 
										WHERE 
											status_informacija = 3
										AND
											nivo_poznavanja_jezika NOT IN (0)
										AND
											arhiva = 0
										");
			$get_id_dipl->execute();
			$num = $get_id_dipl->rowCount();
			echo $num . "<br><br><br>";
			while($result = $get_id_dipl->fetch()){
				$id_kandidata_dipl = $result['id_kandidata_dipl'];
				echo $result['id_kandidata_dipl'] . " - update status_informacija na 2 - " . $result['nivo_poznavanja_jezika'] . "<br>";
				$update_status_inf = $db->prepare("UPDATE idk_nd_kandidati_ciscenje SET status_informacija = 2 WHERE id_kandidata_dipl = :id_kandidata_dipl");
				$update_status_inf->execute(array(
					":id_kandidata_dipl" => $id_kandidata_dipl
				));
			}		
		break;
				
		case "provjera_skole_dipl_2":
			$get_id_dipl = $db->prepare("SELECT 
											id_kandidata_dipl,
											skola_nd_kandidata,
											skola_smjer_nd_kandidata 
										FROM 
											idk_nd_kandidati_ciscenje
										JOIN
											idk_nd_kandidata
										ON
											idk_nd_kandidati_ciscenje.id_kandidata_dipl = idk_nd_kandidata.id_broj_nd_kandidata 
										WHERE 
											status_informacija = 2
										AND
											skola_nd_kandidata IS NOT NULL
										AND
											skola_smjer_nd_kandidata IS NOT NULL
										AND
											arhiva = 0
										LIMIT 300
										");
			$get_id_dipl->execute();
			$num = $get_id_dipl->rowCount();
			echo $num . "<br><br><br>";
			while($result = $get_id_dipl->fetch()){
				$id_kandidata_dipl = $result['id_kandidata_dipl'];
				echo $id_kandidata_dipl . " - update status_informacija na 0 - SKOLA - " . $result['skola_nd_kandidata'] . " - SMJER - " . $result['skola_smjer_nd_kandidata'] . "<br>";
				$update_status_inf = $db->prepare("UPDATE idk_nd_kandidati_ciscenje SET status_informacija = 0 WHERE id_kandidata_dipl = :id_kandidata_dipl");
				$update_status_inf->execute(array(
					":id_kandidata_dipl" => $id_kandidata_dipl
				));
			}		
		break;
		
		case "provjera_skole_dipl_3":
			$get_id_dipl = $db->prepare("SELECT 
											id_kandidata_dipl,
											skola_nd_kandidata,
											skola_smjer_nd_kandidata 
										FROM 
											idk_nd_kandidati_ciscenje
										JOIN
											idk_nd_kandidata
										ON
											idk_nd_kandidati_ciscenje.id_kandidata_dipl = idk_nd_kandidata.id_broj_nd_kandidata 
										WHERE 
											status_informacija = 3
										AND
											skola_nd_kandidata IS NOT NULL
										AND
											skola_smjer_nd_kandidata IS NOT NULL
										AND
											arhiva = 0
										LIMIT 300
										");
			$get_id_dipl->execute();
			$num = $get_id_dipl->rowCount();
			echo $num . "<br><br><br>";
			while($result = $get_id_dipl->fetch()){
				$id_kandidata_dipl = $result['id_kandidata_dipl'];
				echo $id_kandidata_dipl . " - update status_informacija na 1 - SKOLA - " . $result['skola_nd_kandidata'] . " - SMJER - " . $result['skola_smjer_nd_kandidata'] . "<br>";
				$update_status_inf = $db->prepare("UPDATE idk_nd_kandidati_ciscenje SET status_informacija = 1 WHERE id_kandidata_dipl = :id_kandidata_dipl");
				$update_status_inf->execute(array(
					":id_kandidata_dipl" => $id_kandidata_dipl
				));
			}	
		break;

		case "landing": ?>
			<html>
				<head>
				<script
				  src="https://code.jquery.com/jquery-3.6.0.js"
				  integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk="
				  crossorigin="anonymous">
				 </script>
					<title>Jobstep</title>
					<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
					<link rel="preconnect" href="https://fonts.googleapis.com">
					<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
					<link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
					<link rel="preconnect" href="https://fonts.googleapis.com">
					<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
					<link href="https://fonts.googleapis.com/css2?family=Inter:wght@800;900&display=swap" rel="stylesheet">
					<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1" />
					<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
					<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

					<style>
						body {
							background: #F7F9FF; 
							overflow-x: hidden;
						}
						.container {
							background: #F7F9FF;
							margin-top: 50px;
						}
						.naslov {
							font-family: 'Inter', sans-serif;
							color: #18191F;
							font-weight: 900;
							font-size: 60px;
						}
						.slika {
							padding: 0;
							margin-top: 45px;
						}
						.text-box {
							padding: 0px;
							position: static;
							width: 459px;
							height: 272px;
							left: 0px;
							top: 0px;
							margin-top: 100px;
						}
						.text {
							font-family: 'Inter', sans-serif;
							color: #8A8A8A;
						}
						.form-select {
							height: 60px;
							background: #FFFFFF;
							box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
							border-color: #FFFFFF;
							border-radius: 0;
						}
						.posalji {
							width:100%;
							height: 60px;
							background: #3A4053;
							box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
							font-family: Inter;
							font-style: normal;
							font-weight: bold;
							font-size: 18px;
							line-height: 20px;
							text-align: center;
							font-feature-settings: 'salt' on, 'liga' off;
							color: #FFFFFF;
							border-color: #3A4053;
						}
						
						.krug{
							visibility: hidden;
							width: 170px;
							height: 170px;
							background: #6097A0;
							border-radius: 50%;
							position: absolute;
							top: -75px;
							right: -75px;
						}
						.forma{
							display: flex;
							justify-content: center;
							align-items:center;
							
						}
						.forma-child{
							flex:1;
							margin-left: 5px;
							margin-right: 5px;
						}
					@media (max-width: 991px) {
						.forma {
							flex-direction: column;
						}
						.forma-child{
							width: 85vw;
							margin-bottom: 10px; 
						}
						.slika{
							position: absolute;
						}
						.img{
							width: 110vw;
							margin-top: 30px;
							margin-bottom: 30px;
						}
						.naslov {
							font-size: 30px;
						}
						.container {
							background: #F7F9FF;
							margin-top: 0;
						}
						.text-box {
							margin-top: 70px;
							padding: 7vw;
						}
						.krug {
							visibility: visible;
						}
					}

					</style>
				</head>
				<body>
					<div class="container">
						<div class="row">
							<div class="col text-box">
							  <h2 class="naslov">Želiš li raditi u struci u Njemačkoj?</h2>
							  <p class="text">Navedi tačne podatke o svom obrazovanju, jer na taj način osiguravaš da prvo tebe pozovemo kada se otvori konkurs koji odgovara tvojoj struci.</p>
							</div>
							<div class="col slika" id="slika">
							<img src="images/Group-elements.png" class="img" id="img"></img>
							</div>
						</div>
						<div class="forma" >
							<div class="forma-child">
								<label for="select">Završeno obrazovanje*:</label> <br>
									<select class="form-select" id="select" style="height: 60px !important; background: #FFFFFF !important; box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25); border-color: #FFFFFF; border-radius: 0">
										<option id="option" disabled selected>Odaberi...</option>
										<option>1</option>
										<option>2</option>
										<option>3</option>
									</select>
							</div>
							<div class="forma-child">
								<label for="select2">Zvanje/smjer*:</label> <br>
									<select class="form-select" id="select2">
										<option disabled selected>Odaberi...</option>
										<option>1</option>
										<option>2</option>
										<option>3</option>
									</select>
							</div>
							<div class="forma-child">
								<label for="select3">Nivo poznavanja jezika*:</label> <br>
									<select class="form-select" id="select3">
										<option disabled selected>Odaberi...</option>
										<option>1</option>
										<option>2</option>
										<option>3</option>
									</select>
							</div>
							<div class="forma-child">
							<br>
								<button class="posalji">POŠALJI</button>
							</div>
						</div>
					</div>
					<div class="krug"></div>
					<div id="img-container"></div>
				</body>

				<script>
				$(document).ready(function() {
				  $("#select").select2();
				});
					const imgContainer = document.getElementById("img-container");
					const img = document.getElementById("img");
					const slika = document.getElementById("slika");
					
					$( window ).resize(function() {
					  if($( window ).width() < 992){
						imgContainer.appendChild(img);
					  }
					  else{
						slika.appendChild(img);
					  }
					});
					
					if($( window ).width() < 992){
						imgContainer.appendChild(img);
					  } 
				</script>
			</html>
<?php
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
																pua_user_id
															FROM
																idk_pp_user_access
															JOIN
																idk_pp_users
															ON
																idk_pp_user_access.pua_user_id = idk_pp_users.pu_id
															WHERE
																pu_company_id = :kompanija_id
															AND
																pua_nalog_id != :nalog_id
															");
			$query_get_user_from_user_access->execute(array(
				":kompanija_id" => $kompanija_id,
				":nalog_id" 	=> $nalog_id
			));
			while($result_user = $query_get_user_from_user_access->fetch()){
				$user_id 				= $result_user['pua_user_id'];
				$user_ids_from_access[] = $user_id;
			}
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
						pua_status
					FROM
						idk_pp_users
					LEFT JOIN
						idk_pp_user_access
					ON
						idk_pp_users.pu_id = idk_pp_user_access.pua_user_id
					WHERE
						pu_company_id = :kompanija_id
					AND
						(pua_nalog_id = :nalog_id OR pua_nalog_id IS NULL OR ".$user_id_uslov.")";
			
			//echo $sql;
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
											?>
												<tr>
													<td><?php echo $user_id; ?></td>
													<td><?php echo $user_fname; ?></td>
													<td><?php echo $user_lname; ?></td>
											<?php
													if($user_status == 1){
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
											");
			$query_get_ppa_id->execute(array(
				":partner_id" => $partner_id
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
			}
			adminAccess($user_id, $nalog_id, $partner_id);
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
												<a href="#"  class="btn material-btn material-btn-icon-danger material-btn_danger main-container__column material-btn-icon-responsive" data-value="<?php echo $company_id; ?>" id="remove_partner" <?php if($count_admins > 0){ echo "disabled";}?>><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> <span>Ukloni</span></a>
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
				$query_insert_partner->execute(array(
					":nalog_id" 		=> $nalog_id,
					":partner_id" 		=> $partner_id,
					":broj_kandidata" 	=> $broj_kandidata,
					":status" 			=> 1
				));
			}
			updateNalogPartner($partner_id, $nalog_id);
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
										");
			$query_company->execute(array(
				":kompanija_id" => $kompanija_id
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
		
		case "add_appointment":
			$kandidat_id 	= $_POST['kandidat_id'];
			$nalog_id 		= $_POST['nalog_id'];
			$appointment_id = $_POST['date'];
			$time_hours 	= $_POST['hours'];
			$time_minutes 	= $_POST['minutes'];
			
			$time_format 	= date('H:i', strtotime($time_hours.':'.$time_minutes));
			
			$query_get_candidate_appointment = $db->prepare("
															SELECT
																pca_id
															FROM
																idk_pp_cand_appts
															WHERE
																pca_appointment_id = :appointment_id
															AND
																pca_kandidat_id = :kandidat_id
															");
			$query_get_candidate_appointment->execute(array(
				":appointment_id" 	=> $appointment_id,
				":kandidat_id" 		=> $kandidat_id
			));
			$count_candidate_appointment = $query_get_candidate_appointment->rowCount();
			
			if($count_candidate_appointment > 0){
				//UPDATE
				$query_update_candidate_appointment = $db->prepare("
																	UPDATE
																		idk_pp_cand_appts
																	SET
																		pca_time = :time
																	WHERE
																		pca_appointment_id = :appointment_id
																	AND
																		pca_kandidat_id = :kandidat_id
																");
				$query_update_candidate_appointment->execute(array(
					":time" 			=> $time_format,
					":appointment_id" 	=> $appointment_id,
					":kandidat_id" 		=> $kandidat_id
				));
			} else {
				//INSERT
				$query_insert_candidate_appointment = $db->prepare("
																	INSERT INTO
																		idk_pp_cand_appts
																		(
																			pca_appointment_id,
																			pca_kandidat_id,
																			pca_time
																		)
																	VALUES
																		(
																			:appointment_id,
																			:kandidat_id,
																			:time
																		)
																");
				$query_insert_candidate_appointment->execute(array(
					":appointment_id" 	=> $appointment_id,
					":kandidat_id" 		=> $kandidat_id,
					":time" 			=> $time_format
				));
			}
			
			$query_get_nalog_id = $db->prepare("
												SELECT
													pap_nalog_id
												FROM
													idk_pp_appointments
												WHERE
													pap_id = :pap_id
											");
			$query_get_nalog_id->execute(array(
				":pap_id" => $appointment_id
			));
			$result_nalog_id 	= $query_get_nalog_id->fetch();
			$nalog_id 			= $result_nalog_id['pap_nalog_id'];
			
			$query_update_kandidat = $db->prepare("
													UPDATE
														idk_kandidati
													SET
														kandidat_nalog_id = :nalog_id
													WHERE
														kandidat_id = :kandidat_id
												");
			$query_update_kandidat->execute(array(
				":nalog_id" 	=> $nalog_id,
				":kandidat_id" 	=> $kandidat_id
			));
		break;
		
		case "list_appointments":
			$kandidat_id 	= $_POST['kandidat_id'];
			$nalog_id 		= $_POST['nalog_id'];
			
			$query_get_candidate_appointment = $db->prepare("
															SELECT
																pap_date,
																pca_time
															FROM
																idk_pp_appointments
															JOIN
																idk_pp_cand_appts
															ON
																idk_pp_appointments.pap_id = idk_pp_cand_appts.pca_appointment_id
															WHERE
																pca_kandidat_id = :kandidat_id
															AND
																pap_nalog_id = :nalog_id
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
					$datum_format = date('d.m.Y', strtotime($datum));
					$vrijeme_format = date('H:i', strtotime($vrijeme));
				?>
					<div class="row"><div class="col-sm-12"><span class="label label-success material-label material-label_success main-container__column"><?php echo $datum_format . " " . $vrijeme_format; ?></span></div></div>
				<?php
				}
			}
		break;
		
		case "kandidati_edukacija":
			$kandidati = array();
			$get_kandidat_id = $db->prepare("SELECT pk_kandidatid FROM idk_project_kandidati WHERE pk_projectid = 1826 OR pk_projectid = 1836 OR pk_projectid = 1846 OR pk_projectid = 1858");
			$get_kandidat_id->execute();
			while($result_kandidat_id = $get_kandidat_id->fetch()){
				$kandidat_id = $result_kandidat_id['pk_kandidatid'];
				$kandidati[] = $kandidat_id;
			}
			$kandidatiImp = implode(",", $kandidati);
			$kandidati_edukacija = array();
			$sql = "SELECT ke_id FROM idk_kandidat_edukacija WHERE ke_smjer_id IS NULL AND ke_skola_id IS NULL AND ke_kandidat_id IN (".$kandidatiImp.")";
			$get_edukacija = $db->prepare($sql);
			$get_edukacija->execute();
			while($result_edukacija = $get_edukacija->fetch()){
				$kandidat_edukacija = $result_edukacija['ke_id'];
				$kandidati_edukacija[] = $kandidat_edukacija;
			}
			$kandidati_edukacija_unique = array_unique($kandidati_edukacija);
			$count = count($kandidati_edukacija_unique);
			echo $count;
		break;
		
		case "pw_generator":
		// $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		// $charactersLength = strlen($characters);
		// $randomString = '';
		// for ($i = 0; $i < 8; $i++) {
		// 	$randomString .= $characters[rand(0, $charactersLength - 1)];
		// }
		// echo $randomString."<br/>". md5($randomString);
		echo md5('!PartnerTest322');
		exit();
		break;

		case "ciscenje_castinga":
			//UZIMA DATUM KOJI JE BIO PRIJE 3 DANA ISKLJUCUJUCI VIKENDE
			$date = date("Y-m-d", strtotime("-3 weekday"));
			
			//UZMI NALOG ID IZ GRUPE DATUMA 
			$query_get_group = $db->prepare("SELECT papq_nalog_id, ppaq_id FROM idk_pp_appointment_groups WHERE ppaq_end_date <= :date AND ppaq_casting_cron_executed = 0");
			$query_get_group->execute(array(
				":date" => $date
			));
			while($result_group = $query_get_group->fetch()){
				$candidate_ids 	= array();
				$group_nalog 	= $result_group["papq_nalog_id"];
				$group_id 		= $result_group["ppaq_id"];
				//POKUPI SVE KANDIDATE IZ KASTINGA ZA TAJ NALOG
				$query_get_candidates = $db->prepare("
													SELECT
														pk_kandidatid,
														pk_projectid
													FROM
														idk_project_kandidati
													JOIN
														idk_projects
													ON
														idk_project_kandidati.pk_projectid = idk_projects.project_id
													JOIN
														idk_nalozi
													ON
														idk_projects.project_nalogid = idk_nalozi.nalog_id
													WHERE
														nalog_id = :group_nalog
													AND
														project_name LIKE '% - Casting%'
												");
				$query_get_candidates->execute(array(
					":group_nalog" => $group_nalog
				));
				//UZMI ID PROJEKTA PRIJAVE ZA TAJ NALOG
				$query_get_prijave = $db->prepare("SELECT project_id FROM idk_projects WHERE project_name LIKE '% - Prijave%' AND project_nalogid = :group_nalog");
				$query_get_prijave->execute(array(
					":group_nalog" => $group_nalog
				));
				$result_prijave = $query_get_prijave->fetch();
				$prijave_id 	= $result_prijave['project_id'];
				
				while($result_candidate = $query_get_candidates->fetch()){
					$candidate_id 		= $result_candidate['pk_kandidatid'];
					$casting_id			= $result_candidate['pk_projectid'];
					$candidate_ids[] 	= $candidate_id;
					
					echo $candidate_id . "- prebacuje se u projekt Prijave(".$prijave_id.") za nalog: " . $group_nalog . "<br>";
					
					//$delete_from_project = $db->prepare("DELETE FROM idk_project_kandidati WHERE pk_kandidatid = :kandidat_id AND pk_projectid = :project_id");
					//$delete_from_project->execute(array(
						//":kandidat_id" 	=> $candidate_id,
						//":project_id" 	=> $casting_id
					//));
					
					//$insert_into_prijave = $db->prepare("INSERT INTO idk_project_kandidati (pk_projectid, pk_kandidatid) VALUES (:project_id, :kandidat_id)");
					//$insert_into_prijave->execute(array(
						//":project_id" 	=> $prijave_id,
						//":kandidat_id" 	=> $candidate_id
					//));
					echo $candidate_id . "- postavlja se status prijave U Projektu NZ  <br>";
					
					//$set_status_prijave = $db->prepare("UPDATE idk_kandidati SET kandidat_status_prijave = :status_prijave WHERE kandidat_id = :kandidat_id");
					//$set_status_prijave->execute(array(
						//":status_prijave" 	=> 2,
						//":kandidat_id"		=> $candidate_id
					//));
					
					echo "addToLogsStatusPrijave() <br><br><br><br>";
					//addToLogsStatusPrijave($casting_id, $prijave_id, 2, $candidate_id, 5);
				}
				$candidate_ids_imp = implode(",", $candidate_ids);
				$log_desc = "Cron je prebacio kandidate: (".$candidate_ids_imp.") u projekt prijave (" . $prijave_id . ") za nalog : " . $group_nalog;
				$log_type = 0;
				//addToLogs($log_desc, $log_type);
				//$set_cron_executed = $db->prepare("UPDATE idk_pp_appointment_groups SET ppaq_casting_cron_executed = :executed WHERE ppaq_id = :ppaq_id");
				//$set_cron_executed->executed(array(
					//":ppaq_id" 	=> $group_id,
					//":executed" => 1
				//));
			}
		break;
		
		case "ciscenje_intervjua":
			//UZIMA DATUM KOJI JE BIO PRIJE 3 DANA ISKLJUCUJUCI VIKENDE
			$date = date("Y-m-d", strtotime("-3 weekday"));
			//UZMI NALOG ID IZ GRUPE DATUMA 
			$query_get_group = $db->prepare("SELECT 
												papq_nalog_id,
												ppaq_id
											FROM 
												idk_pp_appointment_groups 
											WHERE 
												ppaq_end_date <= :date 
											AND 
												ppaq_interview_cron_executed = 0
											");
			$query_get_group->execute(array(
				":date" => $date
			));
			while($result_group = $query_get_group->fetch()){
				$candidate_ids 	= array();
				$nalog_id 	= $result_group["papq_nalog_id"];
				$group_id	= $result_group["ppaq_id"];
				
				//POKUPI SVE KANDIDATE IZ INTERVJUA ZA TAJ NALOG
				$query_get_candidates = $db->prepare("
													SELECT
														pk_kandidatid,
														pk_projectid
													FROM
														idk_project_kandidati
													JOIN
														idk_projects
													ON
														idk_project_kandidati.pk_projectid = idk_projects.project_id
													JOIN
														idk_nalozi
													ON
														idk_projects.project_nalogid = idk_nalozi.nalog_id
													WHERE
														nalog_id = :group_nalog
													AND
														project_name LIKE '% - Intervju%'
													AND
														pk_kandidatid NOT IN (
																				SELECT 
																					pca_kandidat_id 
																				FROM 
																					idk_pp_cand_appts 
																				JOIN 
																					idk_pp_appointments 
																				ON
																					idk_pp_cand_appts.pca_appointment_id = idk_pp_appointments.pap_id
																				JOIN
                                                                                	idk_pp_appointments_questions
                                                                                ON
																					idk_pp_appointments.pap_id = idk_pp_appointments_questions.papq_appointment_id
																				JOIN
                                                                                	idk_pp_ratings
                                                                                ON
																					idk_pp_appointments_questions.papq_id = idk_pp_ratings.pra_appointment_question_id
																				WHERE
                                                                                	pap_nalog_id = :group_nalog
                                                                                AND
																					(pca_avg_rating IS NOT NULL OR pra_rating IS NOT NULL)
                                                                                GROUP BY pca_kandidat_id
																			)
												");
				$query_get_candidates->execute(array(
					":group_nalog" => $nalog_id
				));
				//UZMI ID PROJEKTA NIJE DOSAO NA RAZGOVOR ZA TAJ NALOG
				$query_get_nije_dosao = $db->prepare("SELECT project_id FROM idk_projects WHERE project_name LIKE '% - Nije došao na razgovor%' AND project_nalogid = :group_nalog");
				$query_get_nije_dosao->execute(array(
					":group_nalog" => $nalog_id
				));
				$result_nije_dosao = $query_get_nije_dosao->fetch();
				$nije_dosao_id 	= $result_nije_dosao['project_id'];
				
				while($result_candidate = $query_get_candidates->fetch()){
					$candidate_id = $result_candidate["pk_kandidatid"];
					$interview_id = $result_candidate["pk_projectid"];
					$candidate_ids[] 	= $candidate_id;					
					
					echo $candidate_id . " - " . $nalog_id . " OVAJ SE BRISE <br>";
					//PREBACI KANDIDATA IZ PROJEKTA INTERVJU U PROJEKT NIJE DOSAO
						// $delete_from_project = $db->prepare("DELETE FROM idk_project_kandidati WHERE pk_kandidatid = :kandidat_id AND pk_projectid = :project_id");
						// $delete_from_project->execute(array(
							// ":kandidat_id" 	=> $candidate_id,
							// ":project_id" 	=> $interview_id
						// ));
						// $insert_into_nije_dosao = $db->prepare("INSERT INTO idk_project_kandidati (pk_projectid, pk_kandidatid) VALUES (:project_id, :kandidat_id)");
						// $insert_into_nije_dosao->execute(array(
							// ":project_id" 	=> $nije_dosao_id,
							// ":kandidat_id" 	=> $candidate_id
						// ));
					//POSTAVI STATUS PRIJAVE NA U Projektu NZ
						// $set_status_prijave = $db->prepare("UPDATE idk_kandidati SET kandidat_status_prijave = :status_prijave WHERE kandidat_id = :kandidat_id");
						// $set_status_prijave->execute(array(
							// ":status_prijave" 	=> 2,
							// ":kandidat_id"		=> $candidate_id
						// ));
						// addToLogsStatusPrijave($interview_id, $nije_dosao_id, 2, $candidate_id, 5);
					
				}
				// $candidate_ids_imp = implode(",", $candidate_ids);
				// $log_desc = "Cron je prebacio kandidate: (".$candidate_ids_imp.") u projekt nije dosao (" . $nije_dosao_id . ") za nalog : " . $nalog_id;
				// $log_type = 0;
				// addToLogs($log_desc, $log_type);
				// $set_cron_executed = $db->prepare("UPDATE idk_pp_appointment_groups SET ppaq_interview_cron_executed = :executed WHERE ppaq_id = :ppaq_id");
				// $set_cron_executed->executed(array(
					// ":ppaq_id" 	=> $group_id,
					// ":executed" => 1
				// ));
			}
		break;
		
		case "phpinfo":
			echo phpinfo();
		break;
		case "hello_world":
			$query = $db->prepare("SELECT ime_nd_kandidata, prezime_nd_kandidata, mobilni_nd_kandidata FROM idk_nd_kandidata WHERE id_broj_nd_kandidata = 65433");
			$query->execute();
			$result = $query->fetch();
			var_dump($result['ime_nd_kandidata']);
		break;
		case "api":
		?>
			<script>

				fetch("http://localhost/glossaAPI/API.php", {
					method : 'POST',
					headers: {
						'Accept': 'application/json',
						'Content-Type': 'application/json'
					},
					body: JSON.stringify({token: "1"})
				});
			</script>
		<?php
		break;

		case "brojevi":

			$sql = "SELECT 
						kki_id
					FROM
						idk_kandidat_kontakt_info
					JOIN
						idk_kandidati
					ON
						kki_kandidat_id = kandidat_id
                    AND
						kandidat_mobitel != kki_podatak
					WHERE 
                    	kki_podatak RLIKE('^[+0-9]+$')
					AND
						kki_naziv = 'Mobilni'";

			$query = $db->prepare($sql);
			$query->execute();

			$brojevi = $query->fetchAll(PDO::FETCH_ASSOC);
			$brojevi_arr = [];

			foreach($brojevi as $broj)
			{
				$brojevi_arr[] = $broj['kki_id'];
			}


			$brojevi_imp = implode(",", $brojevi_arr);

			$sql = "UPDATE idk_kandidat_kontakt_info SET kki_primary = 0 WHERE kki_id IN (".$brojevi_imp.")";
			$query = $db->prepare($sql);
			$query->execute();
		break;

		case "email":
			$sql = "SELECT 
						kki_id
					FROM
						idk_kandidat_kontakt_info
					JOIN
						idk_kandidati
					ON
						kki_kandidat_id = kandidat_id
                    AND
						kandidat_email != kki_podatak
					WHERE 
                    	kki_podatak LIKE('%@%')
					AND
						kki_naziv = 'E-mail'";

			$query = $db->prepare($sql);
			$query->execute();

			$email = $query->fetchAll(PDO::FETCH_ASSOC);
			$email_arr = [];

			foreach($email as $mail)
			{
				$email_arr[] = $mail['kki_id'];
			}


			$email_imp = implode(",", $email_arr);

			$sql = "UPDATE idk_kandidat_kontakt_info SET kki_primary = 0 WHERE kki_id IN (".$email_imp.")";
			$query = $db->prepare($sql);
			$query->execute();
		break;
		
		case "null_mob":
			$sql = "SELECT kandidat_id FROM idk_kandidati WHERE kandidat_mobitel IS NULL";
			$query = $db->prepare($sql);
			$query->execute();
			$null_mob = $query->fetchAll(PDO::FETCH_ASSOC);
			$null_mob_arr = [];
			foreach($null_mob as $mob)
			{
				$null_mob_arr[] = $mob['kandidat_id'];
			}
			$null_mob_imp = implode(",", $null_mob_arr);
			$sql = "UPDATE idk_kandidat_kontakt_info SET kki_primary = 0 WHERE kki_naziv = 'Mobilni' AND kki_primary = 1 AND kki_kandidat_id IN (".$null_mob_imp.")";
			$query = $db->prepare($sql);
			$query->execute();
		break;

		case "null_email":
			$sql = "SELECT kandidat_id FROM idk_kandidati WHERE kandidat_email IS NULL";
			$query = $db->prepare($sql);
			$query->execute();
			$null_email = $query->fetchAll(PDO::FETCH_ASSOC);
			$null_email_arr = [];
			foreach($null_email as $mail)
			{
				$null_email_arr[] = $mail['kandidat_id'];
			}
			$null_email_imp = implode(",", $null_email_arr);
			$sql = "UPDATE idk_kandidat_kontakt_info SET kki_primary = 0 WHERE kki_naziv = 'E-mail' AND kki_kandidat_id IN (".$null_email_imp.")";
			$query = $db->prepare($sql);
			$query->execute();
		break;

		case "mob_duplikati":
			$sql = "SELECT
						*
					FROM
						(
						SELECT
							COUNT(kki_id) AS broj,
							kki_kandidat_id,
							kki_podatak,
							kki_id
						FROM
							idk_kandidat_kontakt_info
						WHERE
							kki_naziv = 'Mobilni' AND kki_kandidat_id != 0
						GROUP BY
							kki_podatak,
							kki_kandidat_id
					
					) AS kan
					WHERE
						kan.broj > 1";
			$query = $db->prepare($sql);
			$query->execute();
			$mob_duplikati = $query->fetchAll(PDO::FETCH_ASSOC);
			foreach($mob_duplikati as $duplikat)
			{
				$sql = "UPDATE 
							idk_kandidat_kontakt_info 
						SET 
							kki_primary = 0 
						WHERE 
							kki_id != :kki_id
						AND 
							kki_kandidat_id = :kki_kandidat_id
						AND
							kki_naziv = 'Mobilni'";
				$query = $db->prepare($sql);
				$query->execute([
					':kki_id' => $duplikat['kki_id'],
					':kki_kandidat_id' => $duplikat['kki_kandidat_id']
				]);
			}
		break;

		case "mail_duplikati":
			$sql = "SELECT 
						*
					FROM
						(
						SELECT
							COUNT(kki_id) AS broj,
							kki_kandidat_id,
							kki_podatak,
							kki_id
						FROM
							idk_kandidat_kontakt_info
						WHERE
							kki_naziv = 'E-mail' AND kki_kandidat_id != 0
						GROUP BY
							kki_podatak,
							kki_kandidat_id
					
					) AS kan
					WHERE
						kan.broj > 1;";
			$query = $db->prepare($sql);
			$query->execute();
			$mail_duplikati = $query->fetchAll(PDO::FETCH_ASSOC);
			foreach($mail_duplikati as $duplikat)
			{
				$sql = "UPDATE 
							idk_kandidat_kontakt_info 
						SET 
							kki_primary = 0 
						WHERE 
							kki_id != :kki_id
						AND 
							kki_kandidat_id = :kki_kandidat_id
						AND
							kki_naziv = 'E-mail'";
				$query = $db->prepare($sql);
				$query->execute([
					':kki_id' => $duplikat['kki_id'],
					':kki_kandidat_id' => $duplikat['kki_kandidat_id']
				]);
			}
		break;
		case "rate":
			include_once($_SERVER["DOCUMENT_ROOT"] . '/jobstep_pp/includes/classes/candidatesProjection.php');
			$durationPerStatus = new durationPerStatus();
			$candidatesProjection = new candidatesProjection($durationPerStatus, 43155);
			$candidate_date = $candidatesProjection -> getCandidateProjectionRows();
			$pocetak_rada_date = date("Y-m-d", $candidate_date[0]["candidate_assessment"]);
			$broj_mjeseci = 6;
			$date = date('Y-m-d', strtotime($pocetak_rada_date . " + $broj_mjeseci months"));

			echo $date;
		break;

		case "visak":
			// var_dump(getKandidatPrvaRata(4855));
			$rate = getNalogRate(289);
			$idk = 0;
			foreach($rate as $rata){
				if(in_array("pocetak rada", $rata)){
					$idk = 1;
					break;
				}
			}

			if($idk == 1){
				echo "ima";
			} else {
				echo "nema";
			}
		break;
	}
?>


