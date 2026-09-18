<?php 

    include("includes/functions.php");
    
    $logged_employee_id=0;
    $odabir_status_jezik=9;
    $cll_date=date('Y-m-d H:i:s');
    $query=$db->prepare("
     select cvl_id,kj_kandidatid from idk_candidate_verified_languages join idk_kandidat_jezici on cvl_id=kj_id where cvl_certificate_expiration_date<now() and cvl_status<9
    ");
    $query->execute();

    $select_rows=$query->fetchAll();

    foreach($select_rows as $select_row){
        $cvl_id=$select_row['cvl_id'];
        $kj_kandidatid=$select_row['kj_kandidatid'];
        $query_update=$db->prepare("
             update idk_candidate_verified_languages set cvl_status=9 where cvl_id=:cvl_id
            ");
        $query_update->execute(array(
            ':cvl_id'=>$cvl_id
        ));
        logDateDifference($cvl_id);
	    $query_log=$db->prepare("
			INSERT INTO idk_candidate_language_logs
				 (cll_candidate_id,cll_cvl_id,cll_status,cll_date,cll_employee_id) 
			VALUE 
				 (:cll_candidate_id,:cll_cvl_id,:cll_status,:cll_date,:cll_employee_id)
	    ");
	    $query_log->execute(array(
            ':cll_candidate_id'=>$kj_kandidatid,
            'cll_cvl_id'=>$cvl_id,
            ':cll_status'=>$odabir_status_jezik,
            ':cll_date'=>$cll_date,
            ':cll_employee_id'=>$logged_employee_id
	    ));    
    }







?>