<?php

	require_once 'gt/gtranslate.php';
	include("includes/functions.php");
	include("includes/common.php");
	

			$nalogs_arr = array();
			// $product_item = array(
				// 'nalog_id' => '',
				// 'lg_language' => '',
				// 'kompanija_id' => '',
				// 'nalog_broj' => '',
				// 'nalog_naziv' => '',
				// 'nalog_opis' => '',		
				// 'lg_link_prijave' => '',
				// 'idk_urlimg_prijave' => '',
				// 'nalog_partner_provizija' => '',
			// );

			$nalogs_arr["data"] = array();
			//array_push($nalogs_arr["data"], $product_item);
			
			//lg_language = '" . $infodata['lang'] ."' AND
			$query = $db->prepare("
					SELECT nalog_id, kompanija_id, nalog_broj, nalog_naziv, nalog_opis,lg_language, nalog_kreirano, nalog_status, nalog_marketing_menadzer, lg_link_prijave, idk_urlimg_prijave, nalog_partner_provizija, no_nalognaziv, no_nalogopis, lg_id
					FROM idk_nalozi	
					INNER JOIN idk_link_generator ON idk_nalozi.nalog_id = idk_link_generator.lg_nalogid
					INNER JOIN 	idk_nalozi_opis ON idk_nalozi.nalog_id = idk_nalozi_opis.no_nalogid
					WHERE  lg_link_prijave LIKE '%https://job-step.net/konkurs/%' AND nalog_partner_active = 1 AND idk_nalozi_opis.no_lang = 'bs'  AND (idk_urlimg_prijave IS NOT NULL AND idk_urlimg_prijave != 'none')
					GROUP BY nalog_id
					ORDER BY idk_link_generator.lg_id DESC");
     
			$query->execute();
            //$gt = new gtranslate();
			
			$i = 0;
			
			while($row = $query->fetch()){
				if(!empty($row['no_nalogopis'])){ // PROVJERA ZA PREVOD 
					//empty slots for nostrifikacija diplome in app
					if ($i%3 == 0 AND $i != 0) {
						$product_item = array('nalog_id' => '','lg_language' => '','kompanija_id' => '','nalog_broj' => '','nalog_naziv' => '','nalog_opis' => '','lg_link_prijave' => '','idk_urlimg_prijave' => '','nalog_partner_provizija' => '');
						array_push($nalogs_arr["data"], $product_item);
						echo $i;
					}
				
					$product_item = array(
						'nalog_id' => $row['nalog_id'],
						'lg_language' => $row['lg_language'],
						'kompanija_id' => $row['kompanija_id'],
						'nalog_broj' => $row['nalog_broj'],
						'nalog_naziv' => $row['no_nalognaziv'],
						'nalog_opis' => $row['no_nalogopis'],					
						'lg_link_prijave' => $row['lg_link_prijave'],
						'idk_urlimg_prijave' => $row['idk_urlimg_prijave'],
						'nalog_partner_provizija' => round(intval($row['nalog_partner_provizija']) , 2 ),
					);
			  
					array_push($nalogs_arr["data"], $product_item);
					
					
					$i++;
				}
			}
						http_response_code(200);
			
			// show products data in json format
			echo '<pre>';
			print_r($nalogs_arr);  
			echo '</pre>';

?>