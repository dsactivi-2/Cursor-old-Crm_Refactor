<?php
include("includes/connect.php");

			
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
						} else {
							$trenutni_status = $result_status_nd_kandidata['status_nd_kandidata'];
							$trenutni_id = $id_kandidata_dipl;
							if($trenutni_status == 7 AND $stari_status != 7){
								
								$query_arhiva_trenutnog = $db->prepare("UPDATE idk_nd_kandidati_ciscenje SET arhiva = 1 WHERE id_kandidata_dipl = :id_kandidata_dipl");
								$query_arhiva_trenutnog->execute(array(
									":id_kandidata_dipl" => $trenutni_id
								));
							} else {
								
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
?>