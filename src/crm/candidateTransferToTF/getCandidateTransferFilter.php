<?php


include("../includes/common.php");
include("../includes/functions.php");
include($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");

Global $db;


function buildCandidateTransferQuery($filter_for_order, $filter_vocations, $filter_age_from, $filter_age_to, $filter_unknown_age, $filter_select_country, $filter_select_residence, $filter_visa, $filter_language_de, $filter_language_en, $filter_drivers_licence, $filter_work_experience, $filter_dipl_status, $filter_vrsta_nostrifikacije){


	$query_part_vocation = "";
	$query_part_unknown_vocation = "";
	$query_part_age = "";
	$query_part_country = "";
	$query_part_residence = "";
	$query_part_visa = "";
	$query_part_order = "";
	$query_part_language_de = "";
    $query_part_language_en = "";  
	$query_part_drivers_licence = "";
	$query_part_work_experience = "";
	$query_part_dipl_status = "";
	$query_part_vrsta_nostrifikacije = "";

	if(in_array('0', $filter_vocations)){
		$query_part_unknown_vocation = ' AND ((kan.exists_in_ke = 0 AND ke.ke_kandidat_id IS NULL) OR ke.ke_kandidat_id IS NOT NULL) ';
	}
	else{
		$query_part_unknown_vocation = ' AND ke.ke_naziv_kvalifikacije IS NOT NULL '; 
	}
		

    foreach($filter_vocations as &$vocation){
        $vocation =  str_replace(' ', '', $vocation);
    }
	$query_part_vocation = implode("') OR REPLACE(sqke.ke_naziv_kvalifikacije, ' ', '') LIKE ('", $filter_vocations);
	$query_part_vocation = " (REPLACE(sqke.ke_naziv_kvalifikacije, ' ', '') LIKE('".$query_part_vocation;
	$query_part_vocation = $query_part_vocation."'))";

	if($filter_unknown_age == "true")
		$query_part_age = " AND ((DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), sqkan.kandidat_datumrodjenja)),'%Y') +0 BETWEEN ".$filter_age_from." AND ".$filter_age_to." OR sqkan.kandidat_datumrodjenja IS NULL) OR sqkan.kandidat_datumrodjenja = date('1970-01-01')) ";
		
	else
		$query_part_age = " AND (DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), sqkan.kandidat_datumrodjenja)),'%Y') +0 BETWEEN ".$filter_age_from." AND ".$filter_age_to." AND sqkan.kandidat_datumrodjenja != date('1970-01-01')) ";
	
	
	if(count($filter_select_country) != 4 AND $filter_select_country !== ""){
		
        if(in_array('R', $filter_select_country) AND in_array('B', $filter_select_country) AND in_array('S', $filter_select_country)){
            $query_part_country = " AND sqkan.kandidat_mobitel NOT LIKE  ('+49%') ";
        }elseif(in_array('R', $filter_select_country) AND in_array('B', $filter_select_country) AND in_array('D', $filter_select_country)){
            $query_part_country = " AND sqkan.kandidat_mobitel NOT LIKE  ('+381%') ";
        }elseif(in_array('R', $filter_select_country) AND in_array('S', $filter_select_country) AND in_array('D', $filter_select_country)){
            $query_part_country = " AND sqkan.kandidat_mobitel NOT LIKE  ('+387%') ";
        }elseif(in_array('R', $filter_select_country) AND in_array('B', $filter_select_country)){
            $query_part_country = " AND sqkan.kandidat_mobitel NOT LIKE  ('+381%') AND sqkan.kandidat_mobitel NOT LIKE  ('+49%') ";
        }elseif(in_array('R', $filter_select_country) AND in_array('S', $filter_select_country)){
            $query_part_country = " AND sqkan.kandidat_mobitel NOT LIKE ('+387%') AND sqkan.kandidat_mobitel NOT LIKE  ('+49%') ";
        }elseif(in_array('R', $filter_select_country) AND in_array('D', $filter_select_country)){
            $query_part_country = " AND sqkan.kandidat_mobitel NOT LIKE ('+387%') AND sqkan.kandidat_mobitel NOT LIKE  ('+381%') ";
        }elseif(in_array('R', $filter_select_country)){
            $query_part_country = " AND sqkan.kandidat_mobitel NOT LIKE ('+387%') AND sqkan.kandidat_mobitel NOT LIKE  ('+381%') AND sqkan.kandidat_mobitel NOT LIKE  ('+49%') ";
        }

        elseif(in_array('B', $filter_select_country) AND in_array('S', $filter_select_country)  AND in_array('D', $filter_select_country)){
            $query_part_country = " AND (sqkan.kandidat_mobitel LIKE ('+387%') OR sqkan.kandidat_mobitel LIKE  ('+381%') OR sqkan.kandidat_mobitel LIKE  ('+49%')) ";
        }elseif(in_array('B', $filter_select_country) AND in_array('S', $filter_select_country)){
            $query_part_country = " AND (sqkan.kandidat_mobitel LIKE ('+387%') OR sqkan.kandidat_mobitel LIKE  ('+381%')) ";
        }elseif(in_array('B', $filter_select_country) AND in_array('D', $filter_select_country)){
            $query_part_country = " AND (sqkan.kandidat_mobitel LIKE ('+387%') OR sqkan.kandidat_mobitel LIKE  ('+49%')) ";
        }elseif(in_array('S', $filter_select_country) AND in_array('D', $filter_select_country)){
            $query_part_country = " AND (sqkan.kandidat_mobitel LIKE ('+381%') OR sqkan.kandidat_mobitel LIKE  ('+49%')) ";
        }elseif(in_array('B', $filter_select_country)){
            $query_part_country = "  AND sqkan.kandidat_mobitel LIKE ('+387%') ";
        }elseif(in_array('S', $filter_select_country)){
            $query_part_country = "  AND sqkan.kandidat_mobitel LIKE ('+381%') ";
        }elseif(in_array('D', $filter_select_country)){
            $query_part_country = "  AND sqkan.kandidat_mobitel LIKE ('+49%') ";
        }
        
	}

	if(count($filter_select_residence) != 3 AND $filter_select_residence !== ""){
		if(in_array('Unknown', $filter_select_residence)){
			$query_part_residence = " AND sqkan.kandidat_drzavljanstvo_vrsta NOT LIKE ('NON-EU državljanin') AND sqkan.kandidat_drzavljanstvo_vrsta NOT LIKE  ('EU državljanin') ";
		}
		else if(in_array('EU državljanin', $filter_select_residence) AND in_array('NON-EU državljanin', $filter_select_residence)){
			$query_part_residence = " AND (sqkan.kandidat_drzavljanstvo_vrsta LIKE ('EU državljanin') OR LIKE sqkan.kandidat_drzavljanstvo_vrsta ('NON-EU državljanin')) ";
		}
		else if(in_array('EU državljanin', $filter_select_residence)){
			$query_part_residence = "  AND sqkan.kandidat_drzavljanstvo_vrsta LIKE ('EU državljanin') ";
		}
		else if(in_array('NON-EU državljanin', $filter_select_residence)){
			$query_part_residence = "  AND sqkan.kandidat_drzavljanstvo_vrsta LIKE ('NON-EU državljanin') ";
		}
	}

	if($filter_visa == "true"){
		$query_part_visa = " AND (sqkan.kandidat_viza = 1 AND sqkan.kandidat_viza_vrijedi_do > now()) ";
	}
    
	if($filter_for_order != "")
		$query_part_order = "
			AND   sqkan.kandidat_id NOT IN (
				SELECT 
					pk.pk_kandidatid
				FROM 
					idk_project_kandidati pk
				JOIN 
					idk_projects p
				ON 
					p.project_id = pk.pk_projectid AND p.project_name NOT LIKE ('%Nedostupan%')
				WHERE 
					p.project_nalogid IN (".implode(",", $filter_for_order).")
                    OR p.project_name LIKE ('%poceli%')
			) 
		";

    if($filter_for_order != "")
		$query_nedostupan = "
			AND sqkan.kandidat_id NOT IN (
                SELECT kandidat 
                FROM idk_nedostupan_log 
                WHERE 
                    (brojac = 2 AND status = 1 AND DATE(NOW() - INTERVAL 30 DAY) < DATE(doe)) 
                    OR 
                    (status = 1 AND idk_nedostupan_log.nalog IN (".implode(",", $filter_for_order)."))
			) 
		";

    

    if($filter_language_de !== ""){
        if(in_array('-1', $filter_language_de)){
            $query_part_language_de = ' AND (languages_de.language_level_de IN ('.implode(',', $filter_language_de).') OR languages_de.language_level_de IS NULL) ';
        }
        else{
            $query_part_language_de = ' AND languages_de.language_level_de IN ('.implode(',', $filter_language_de).') ';
        }
    }

    if($filter_language_en !== ""){
        if(in_array('-1', $filter_language_en)){
            $query_part_language_en = ' AND (languages_en.language_level_en IN ('.implode(',', $filter_language_en).') OR languages_en.language_level_en IS NULL) ';
        }
        else{
            $query_part_language_en = ' AND languages_en.language_level_en IN ('.implode(',', $filter_language_en).') ';
        }
    }

    if($filter_drivers_licence !== ""){
        $query_part_drivers_licence = implode("%') OR sqkan.kandidat_vozacka_kategorija LIKE('%", $filter_drivers_licence);
        if(in_array('0', $filter_drivers_licence)){
            if(in_array('Nema', $filter_drivers_licence)){
                $query_part_drivers_licence = " AND (sqkan.kandidat_vozacka_kategorija IS NULL OR sqkan.kandidat_vozacka_kategorija LIKE ('%".$query_part_drivers_licence."%')) ";
            }else{
                $query_part_drivers_licence = " AND ((sqkan.kandidat_vozacka_kategorija IS NULL AND sqkan.kandidat_vozacka_dozvola IS NULL) OR sqkan.kandidat_vozacka_kategorija LIKE ('%".$query_part_drivers_licence."%')) ";
            }
        }
        else{
            if(in_array('Nema', $filter_drivers_licence)){
                $query_part_drivers_licence = " AND (sqkan.kandidat_vozacka_dozvola = 'Ne' OR sqkan.kandidat_vozacka_kategorija LIKE ('%".$query_part_drivers_licence."%')) ";
            }else{
                $query_part_drivers_licence = " AND (sqkan.kandidat_vozacka_kategorija LIKE ('%".$query_part_drivers_licence."%'))";
            }
        }
    }

    if($filter_dipl_status !== ""){
        $imp_filter_dipl_status = implode(', ', $filter_dipl_status);
        if(in_array('0', $filter_dipl_status)){
            $query_part_dipl_status = " AND (sqkan.kandidat_dipl_id = 0 OR ndk.status_nd_kandidata IN ($imp_filter_dipl_status)) ";
        }else{
            $query_part_dipl_status = " AND ndk.status_nd_kandidata IN ($imp_filter_dipl_status) ";
        }
    }

    if($filter_vrsta_nostrifikacije !== ""){
        $imp_filter_vrsta_nostrifikacije = implode(', ', $filter_vrsta_nostrifikacije);
        if(in_array('3', $filter_vrsta_nostrifikacije)){
            $query_part_vrsta_nostrifikacije = " AND (nodi.full_recognition IS NULL OR nodi.full_recognition IN ($imp_filter_vrsta_nostrifikacije)) ";
        }else{
            $query_part_vrsta_nostrifikacije = " AND nodi.full_recognition IN ($imp_filter_vrsta_nostrifikacije) ";
        }
    }

    if($filter_work_experience == "true"){
		$query_part_work_experience = " AND sqkan.kandidat_iskustvo_u_struci = 1 ";
	}

	$final_query = "
		SELECT * 
		FROM(
			SELECT 
				sqkan.kandidat_id, 
				sqkan.kandidat_ime, 
				sqkan.kandidat_prezime, 
				sqkan.kandidat_mobitel, 
				sqkan.kandidat_datumrodjenja, 
				sqkan.kandidat_drzavljanstvo_vrsta, 
				sqkan.kandidat_email, 
				sqkan.kandidat_viza,
				sqkan.kandidat_vozacka_dozvola,
				sqkan.kandidat_vozacka_kategorija,
				sqkan.kandidat_iskustvo_u_struci,
				sqkan.kandidat_dipl_id,
            	ndk.status_nd_kandidata,
            	ndk.pstatus_nd_kandidata,
                CASE 
                    WHEN nodi.full_recognition = 0 THEN 'DJELIMIČNO'
                    WHEN nodi.full_recognition = 1 THEN 'POTPUNO'
                    WHEN nodi.full_recognition = 2 THEN 'EVALUACIJA'
                    ELSE 'N/A'
                END AS vrsta_nostrifikacije,
				CASE WHEN sqkan.kandidat_id IN (SELECT ke_kandidat_id FROM idk_kandidat_edukacija) THEN 1 ELSE 0 END as exists_in_ke
			FROM 
				idk_kandidati sqkan
                LEFT JOIN idk_nd_kandidata ndk ON sqkan.kandidat_dipl_id = ndk.id_broj_nd_kandidata
                LEFT JOIN idk_nostrifikovane_diplome nodi ON ndk.id_broj_nd_kandidata = nodi.id_cand_dipl 
			WHERE 
				  sqkan.kandidat_status != 3 
			AND  (sqkan.kandidat_status_prijave IN(0, 1, 2, 5, 6, 24) OR sqkan.kandidat_status_prijave IS NULL)
            AND sqkan.kandidat_nalog_id IS NULL
            AND sqkan.kandidat_pogresan_broj = 0
            AND sqkan.kandidat_nedostupan = 0
				".$query_part_order."
				".$query_nedostupan."
				".$query_part_age."
				".$query_part_country."
				".$query_part_residence."
				".$query_part_visa."
				".$query_part_drivers_licence."
				".$query_part_work_experience."
                ".$query_part_dipl_status."
                ".$query_part_vrsta_nostrifikacije."
		) kan
		LEFT JOIN(
			SELECT 
				sqke.ke_kandidat_id, 
				sqke.ke_naziv_kvalifikacije
			FROM idk_kandidat_edukacija sqke
			WHERE ".$query_part_vocation."
			GROUP BY sqke.ke_kandidat_id
		) ke 
		ON (kan.kandidat_id = ke.ke_kandidat_id) 
        LEFT JOIN(
            SELECT  
                languages_set.kj_id,
                languages_set.kj_kandidatid,
                max(languages_set.language_level_de) as language_level_de
            FROM(
                SELECT 
                    sq1kj.kj_id,
                    sq1kj.kj_kandidatid,
                    CASE 
                    WHEN sq1kj.kj_slusanje = 'C2'
                    THEN 6
                    WHEN sq1kj.kj_slusanje = 'C1'
                    THEN 5
                    WHEN sq1kj.kj_slusanje = 'B2'
                    THEN 4
                    WHEN sq1kj.kj_slusanje = 'B1'
                    THEN 3
                    WHEN sq1kj.kj_slusanje = 'A2'
                    THEN 2
                    WHEN sq1kj.kj_slusanje = 'A1'
                    THEN 1
                    ELSE 0
                    END as language_level_de
                FROM 
                    idk_kandidat_jezici sq1kj
                WHERE 
                    sq1kj.kj_naziv LIKE ('Njemacki')
                    AND 
                    sq1kj.kj_kandidatid NOT IN (
                        SELECT 
                            kj1.kj_kandidatid
                        FROM 
                            idk_kandidat_jezici kj1 
                        JOIN 
                            idk_candidate_verified_languages cvl1
                        ON 
                            kj1.kj_id = cvl1.cvl_id 
                            AND 
                            cvl1.cvl_active = 1
                    )
                
                UNION 
                
                SELECT 
                    sq1kj.kj_id,
                    sq1kj.kj_kandidatid,
                    CASE 
                    WHEN sq1kj.kj_slusanje = 'C2'
                    THEN 6
                    WHEN sq1kj.kj_slusanje = 'C1'
                    THEN 5
                    WHEN sq1kj.kj_slusanje = 'B2'
                    THEN 4
                    WHEN sq1kj.kj_slusanje = 'B1'
                    THEN 3
                    WHEN sq1kj.kj_slusanje = 'A2'
                    THEN 2
                    WHEN sq1kj.kj_slusanje = 'A1'
                    THEN 1
                    ELSE 0
                    END as language_level_de
                FROM 
                    idk_kandidat_jezici sq1kj
                JOIN 
                    idk_candidate_verified_languages sq1cvl
                ON 
                    sq1kj.kj_id = sq1cvl.cvl_id 
                    AND 
                    sq1cvl.cvl_active = 1
                WHERE 
                    sq1kj.kj_naziv LIKE ('Njemacki')

            )languages_set
            GROUP BY languages_set.kj_kandidatid
        )languages_de
        ON languages_de.kj_kandidatid = kan.kandidat_id
        LEFT JOIN(
            SELECT  
                languages_set_en.kj_id,
                languages_set_en.kj_kandidatid,
                max(languages_set_en.language_level_en) as language_level_en
            FROM(
                SELECT 
                    sq1kjen.kj_id,
                    sq1kjen.kj_kandidatid,
                    CASE 
                    WHEN sq1kjen.kj_slusanje = 'C2'
                    THEN 6
                    WHEN sq1kjen.kj_slusanje = 'C1'
                    THEN 5
                    WHEN sq1kjen.kj_slusanje = 'B2'
                    THEN 4
                    WHEN sq1kjen.kj_slusanje = 'B1'
                    THEN 3
                    WHEN sq1kjen.kj_slusanje = 'A2'
                    THEN 2
                    WHEN sq1kjen.kj_slusanje = 'A1'
                    THEN 1
                    ELSE 0
                    END as language_level_en
                FROM 
                    idk_kandidat_jezici sq1kjen
                WHERE 
                    sq1kjen.kj_naziv LIKE ('Engleski')
            )languages_set_en
            GROUP BY languages_set_en.kj_kandidatid
        )languages_en
        ON languages_en.kj_kandidatid = kan.kandidat_id
        WHERE 1 
        ".$query_part_unknown_vocation."  
        ".$query_part_language_de."
        ".$query_part_language_en."
		
		GROUP BY(kan.kandidat_id)
	";

	return $final_query;
}

    $filter_for_order 			= $_REQUEST['filter_for_order'];
    $filter_ignore_orders 	    = $_REQUEST['filter_ignore_orders'];
    $filter_vocations 			= $_REQUEST['filter_vocations'];
    $filter_age_from 			= $_REQUEST['filter_age_from'];
    $filter_age_to 				= $_REQUEST['filter_age_to'];
    $filter_unknown_age 		= $_REQUEST['filter_unknown_age'];
    $filter_select_country 		= $_REQUEST['filter_select_country'];
    $filter_select_residence 	= $_REQUEST['filter_select_residence'];
    $filter_visa 				= $_REQUEST['filter_visa'];
    $filter_language_de 		= $_REQUEST['filter_language_de'];
    $filter_language_en         = $_REQUEST['filter_language_en'];
    $filter_drivers_licence     = $_REQUEST['filter_drivers_licence'];
    $filter_work_experience     = $_REQUEST['filter_work_experience'];
    $filter_only_order_vocations= $_REQUEST['filter_only_order_vocations'];
    $filter_dipl_status         = $_REQUEST['filter_dipl_status'];
    $filter_vrsta_nostrifikacije         = $_REQUEST['filter_vrsta_nostrifikacije'];

    
    if($filter_only_order_vocations == "true"){
        $filter_vocations = array();

        $query_get_order_vocations = $db -> prepare("
            SELECT ss.ss_naziv
            FROM idk_nalog_smjer ns
            JOIN idk_skole_smjerovi ss ON ss.ss_id = ns.smjer_id
            WHERE ns.nalog_id = $filter_for_order
        ");
        $query_get_order_vocations -> execute();
        while($row = $query_get_order_vocations -> fetch()){
            array_push($filter_vocations, $row['ss_naziv']);
        }
        if(!$filter_vocations){
            $return_table[0]['table'] = "error";
            $return_table[0]['error_text'] = "Odabrani nalog nema povezane smjerove";
            echo json_encode($return_table);
            exit();
        }
    }
    $joined_orders_to_ignore = array();
    array_push($joined_orders_to_ignore, $filter_for_order);
    if($filter_ignore_orders === ""){
        $filter_ignore_orders = "-1";
        array_push($joined_orders_to_ignore, $filter_ignore_orders);
    }
    else if(is_array($filter_ignore_orders)){
       $joined_orders_to_ignore = array_merge($joined_orders_to_ignore, $filter_ignore_orders);
    }
    else{
        array_push($joined_orders_to_ignore, $filter_ignore_orders);
    }

    $query = buildCandidateTransferQuery($joined_orders_to_ignore, $filter_vocations, $filter_age_from, $filter_age_to, $filter_unknown_age, $filter_select_country, $filter_select_residence, $filter_visa, $filter_language_de, $filter_language_en, $filter_drivers_licence, $filter_work_experience, $filter_dipl_status, $filter_vrsta_nostrifikacije);
    //var_dump($query);
    //exit();
    $query_get_candidates = $db->prepare($query);
    $query_get_candidates -> execute();
    if($query_get_candidates -> rowCount()){

        $i = 0;
        $return_table = array();
        $result_candidates = array();
        while($row = $query_get_candidates -> fetch()){
        
            $candidate_id = $row['kandidat_id'];
            $candidate_name = $row['kandidat_ime'];
            $candidate_lastname = $row['kandidat_prezime'];
            $vocation = $row['ke_naziv_kvalifikacije'];
            $dob = $row['kandidat_datumrodjenja'];
            $is_eu = $row['kandidat_drzavljanstvo_vrsta'];
            $has_visa = $row['kandidat_viza'];
            $language_level_de = $row['language_level_de'];
            $language_level_en = $row['language_level_en'];
            $drivers_licence_primary = $row['kandidat_vozacka_dozvola'];
            $drivers_licence = $row['kandidat_vozacka_kategorija'];
            $work_experience = $row['kandidat_iskustvo_u_struci'];
            $status_nd_kandidata = $row['status_nd_kandidata'];
            $pstatus_nd_kandidata = $row['pstatus_nd_kandidata'];
            $vrsta_nostrifikacije = $row['vrsta_nostrifikacije'];
    
            if(is_null($vocation)){
                $vocation = '<i style="font-size:10px">N/A</i>';
            }
    
            if($is_eu == "NON-EU državljanin"){
                $is_eu = 'NE';
            }
            else if(is_null($is_eu)){
                $is_eu = '<i style="font-size:10px">N/A</i>';
            }
            else if($is_eu = "EU državljanin"){
                $is_eu = 'DA';
            }
            
            if(is_null($has_visa)){
                $has_visa = '<i style="font-size:10px">N/A</i>';
            }
            else if($has_visa){
                $has_visa = 'DA';
            }
            else{
                $has_visa = 'NE';
            }
    
            if(is_null($work_experience)){
                $work_experience = '<i style="font-size:10px">N/A</i>';
            }
            else if($work_experience){
                $work_experience = 'DA';
            }
            else{
                $work_experience = 'NE';
            }
            
            if(is_null($drivers_licence)){
                if(is_null($drivers_licence_primary)){
                    $drivers_licence = '<i style="font-size:10px">N/A</i>';
                }else{
                    $drivers_licence = "Nema";
                }
            }
    
            if(is_null($language_level_de)){
                $language_level_de = '<i style="font-size:10px">N/A</i>';
            }
            else if($language_level_de == 0){
                $language_level_de = 'BZ';
            }
            else if($language_level_de == 1){
                $language_level_de = 'A1';
            }
            else if($language_level_de == 2){
                $language_level_de = 'A2';
            }
            else if($language_level_de == 3){
                $language_level_de = 'B1';
            }
            else if($language_level_de == 4){
                $language_level_de = 'B2';
            }
            else if($language_level_de == 5){
                $language_level_de = 'C1';
            }
            else if($language_level_de == 6){
                $language_level_de = 'C2';
            }

            if(is_null($language_level_en)){
                $language_level_en = '<i style="font-size:10px">N/A</i>';
            }
            else if($language_level_en == 0){
                $language_level_en = 'BZ';
            }
            else if($language_level_en == 1){
                $language_level_en = 'A1';
            }
            else if($language_level_en == 2){
                $language_level_en = 'A2';
            }
            else if($language_level_en == 3){
                $language_level_en = 'B1';
            }
            else if($language_level_en == 4){
                $language_level_en = 'B2';
            }
            else if($language_level_en == 5){
                $language_level_en = 'C1';
            }
            else if($language_level_en == 6){
                $language_level_en = 'C2';
            }
    
            if(is_null($dob)){
                $dob = '<i style="font-size:10px">N/A</i>';
            }
            else{
                $dob = date('d.m.Y', strtotime($dob));
            }
            $return_table[$i]['candidate_id'] = $candidate_id;
            $return_table[$i]['candidate_name'] = '<a href = "'.getSiteUrlr().'kandidati?page=open&id='.$candidate_id.'">'.$candidate_name.' '.$candidate_lastname.'</a>';
            $return_table[$i]['vocation'] = $vocation;
            $return_table[$i]['dob'] = $dob;
            $return_table[$i]['is_eu'] = $is_eu;
            $return_table[$i]['has_visa'] = $has_visa;
            $return_table[$i]['language_level_de'] = $language_level_de;
            $return_table[$i]['language_level_en'] = $language_level_en;
            $return_table[$i]['drivers_licence'] = $drivers_licence;
            $return_table[$i]['work_experience'] = $work_experience;
            $return_table[$i]['dipl_status'] = getStatusDIPLKandidatR($status_nd_kandidata, $pstatus_nd_kandidata);
            $return_table[$i]['vrsta_nostrifikacije'] = $vrsta_nostrifikacije;
            array_push($result_candidates, $candidate_id);
            $i++;
        }
        $return_table[0]['result_candidates'] = implode(',',$result_candidates);
        $return_table[0]['table'] = '
            <table id = "table_marketing" class="striped col-12" width="100%">
                <thead>
                    <th style = "text-align:center;">ID</th>
                    <th style = "text-align:center;">Ime</th>
                    <th style = "text-align:center;">Kvalif</th>
                    <th style = "text-align:center;">Dob</th>
                    <th style = "text-align:center;">EU</th>
                    <th style = "text-align:center;">Visa</th>
                    <th style = "text-align:center;">Njemački jezik</th>
                    <th style = "text-align:center;">Engleski jezik</th>
                    <th style = "text-align:center;">Vozacka</th>
                    <th style = "text-align:center;">Iskustvo</th>
                    <th style = "text-align:center;">DIPL status</th>
                    <th style = "text-align:center;">Vrsta nostrifikacije</th>
                </thead>
                <tbody>
                
                </tbody>
            </table>
        ';
        echo json_encode($return_table);
    } else {
        $return_table[0]['table'] = "error";
        $return_table[0]['error_text'] = "Nije pronađen niti jedan kandidat koji odgovara filteru";
        echo json_encode($return_table);
    }
    
?>