<?php
include("includes/functions.php");

//UZIMA SUTRASNJI DATUM
$date = date("Y-m-d", strtotime("+1 day"));
// $date = date("Y-m-d", strtotime("-1 day")); //for testing

//GET APPOINTMENTS AND NALOG_ID FOR TOMORROW
$get_appt = $db->prepare("SELECT pap_id, pap_nalog_id FROM idk_pp_appointments WHERE pap_date = :pap_date");
$get_appt->execute(array(":pap_date" => $date   ));

while($row_appt = $get_appt->fetch()){
    $pap_id =  $row_appt['pap_id'];
    $pap_nalog_id = $row_appt['pap_nalog_id'];
    $project_id = getProjectIDForNalogByName("- Casting", $pap_nalog_id);
    $interview_project_id = getProjectIDForNalogByName("- Intervju", $pap_nalog_id);

    $sql = "SELECT kandidat_id, kandidat_ime, kandidat_prezime, kri_id, ke_id, pca_appointment_id
            FROM idk_kandidati
            INNER JOIN idk_project_kandidati ON idk_kandidati.kandidat_id = idk_project_kandidati.pk_kandidatid 
            LEFT JOIN idk_kandidat_radno_iskustvo ON kandidat_id = kri_kandidat_id AND kri_prikaz_pp = 1
            LEFT JOIN idk_kandidat_edukacija ON kandidat_id = ke_kandidat_id AND ke_prikaz_pp = 1
            JOIN idk_pp_cand_appts ON kandidat_id = pca_kandidat_id AND pca_status = 1 AND pca_appointment_id = :pap_id
            WHERE pk_projectid = :pk_projectid GROUP BY pk_kandidatid
            ORDER BY kandidat_ime, kandidat_prezime";

    $query_get_kandidati = $db->prepare($sql);

    $query_get_kandidati->execute(array(
        ':pk_projectid' => $project_id,
        ':pap_id' => $pap_id
    ));
    $ct=1;
    $array_candidates_go = array();
    $array_candidates_didnt_go = array();
    while($row_kandidat = $query_get_kandidati->fetch()){
        $kandidat_id = $row_kandidat['kandidat_id'];
        $skola_na_ppu = $row_kandidat['ke_id'];
        $iskustvo_na_ppu = $row_kandidat['kri_id'];
        if($skola_na_ppu !== null AND $iskustvo_na_ppu !== null){
            // echo $ct++.". ";
            // echo $row_kandidat['kandidat_id']." - ".$skola_na_ppu." - ".$iskustvo_na_ppu." - moze ici";
            // echo '<br>';
            array_push($array_candidates_go, $kandidat_id);

            //mijenjane statusa prijave i premjestanje u projekat
            $upd_status_prijave = $db->prepare("UPDATE idk_kandidati SET kandidat_status_prijave = 3 WHERE kandidat_id = $kandidat_id");
            $upd_status_prijave->execute();
            
            $del_project = $db->prepare("DELETE FROM idk_project_kandidati WHERE pk_projectid = $project_id AND pk_kandidatid = $kandidat_id");
            $del_project->execute();

            $insert_project = $db->prepare("
                            INSERT INTO idk_project_kandidati
                                (pk_projectid, pk_kandidatid)
                            VALUES
                                (:pk_projectid, :pk_kandidatid)");

            $insert_project->execute(array(
                            ':pk_projectid' => $interview_project_id,
                            ':pk_kandidatid' => $kandidat_id
	        ));
            $status_id = 3;
            $izvor = 5;
            addToLogsStatusPrijave($project_id, $interview_project_id, $status_id, $kandidat_id, $izvor);
        }else{
            //NE IDE GO ONLINE
            array_push($array_candidates_didnt_go, $kandidat_id);
        }
        
    }
    // var_dump($skola_na_ppu);
    $log_desc = "Za nalog ".$pap_nalog_id.", termin ".$pap_id." automatski prebaceni kandidati na go online [".implode(", ", $array_candidates_go)."].Nisu prebačeni: [".implode(", ", $array_candidates_didnt_go)."]";
    $log_type = "0";
    addToLogs($log_desc, $log_type);
}

?>
