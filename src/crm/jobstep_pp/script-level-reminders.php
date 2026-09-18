<?php
	include("includes/connect.php");

	$reminder_type 	= intval($_GET['reminder_type']);

    if($reminder_type && ($reminder_type >= 1 && $reminder_type <= 19)) {

        /*
            Reminder type info START
            */
                $query_reminder_type = $db->prepare("
                    SELECT 
                        prt_id, prt_has_documents
                    FROM 
                        idk_pp_reminder_types
                    WHERE 
                        prt_id = :prt_id
                "); 
                $query_reminder_type->execute(array(
                    ':prt_id' => $reminder_type
                ));

                if ($query_reminder_type->rowCount() == 1) {

                    $row_reminder_type      = $query_reminder_type->fetch();

                    $prt_id                 = intval($row_reminder_type["prt_id"]);
                    $prt_has_documents      = intval($row_reminder_type["prt_has_documents"]); 

                    echo "<b>Reminder Type:</b> ".$prt_id."<br>";
                    echo "<hr>";
                    echo "<br>";

                    if ($prt_has_documents == 0) {

                        /*
                            Kupe se sve aktivne postavke za prt_id koji nemaju dokumente START 
                            */
                                $query_reminder_settings = $db->prepare("
                                    SELECT 
                                        prs_id
                                    FROM 
                                        idk_pp_reminder_settings
                                    WHERE 
                                        prs_active = 1 
                                        AND 
                                        prs_reminder_type_id = :reminderType
                                ");
                                $query_reminder_settings->execute(array(
                                    ':reminderType' => $prt_id
                                ));

                                if ($query_reminder_settings->rowCount() != 0) {

                                    echo "<b>Done for reminder settings:</b> <br>";
                                    echo "<pre>";

                                    while ($row_reminder_settings = $query_reminder_settings->fetch()) {

                                        $prs_id = intval($row_reminder_settings["prs_id"]);

                                        echo "     <b>".$prs_id."</b><br>";
                                        /*
                                            Kupe se svi kandidati koji imaju više od jednog remindera za prs_id
                                            */
                                                $query_candidates = $db->prepare("
                                                    SELECT 
                                                        pr_candidate_id, 
                                                        count(pr_id) AS count_reminders
                                                    FROM 
                                                        idk_pp_reminders 
                                                    WHERE 
                                                        pr_reminder_setting_id = :pr_reminder_setting_id
                                                    GROUP BY 
                                                        pr_candidate_id
                                                ");
                                                $query_candidates->execute(array(
                                                    ':pr_reminder_setting_id' => $prs_id
                                                ));

                                                if ($query_candidates->rowCount() != 0){

                                                    $candidates_array = array();

                                                    while ($row_candidates = $query_candidates->fetch()){

                                                        $pr_candidate_id = $row_candidates["pr_candidate_id"]; 
                                                        $count_reminders = $row_candidates["count_reminders"];

                                                        if ($count_reminders > 1){
                                                            array_push($candidates_array, $pr_candidate_id); 

                                                            /*
                                                                Kupe se svi reminderi za pr_candidate_id i pr_reminder_setting_id START 
                                                                */

                                                                    $query_reminders_candidate = $db->prepare("
                                                                        SELECT 
                                                                            pr_id, pr_status
                                                                        FROM 
                                                                            idk_pp_reminders
                                                                        WHERE 
                                                                            pr_reminder_setting_id = :pr_reminder_setting_id
                                                                            AND 
                                                                            pr_candidate_id = :pr_candidate_id 
                                                                        ORDER BY 
                                                                            pr_id 
                                                                        ASC
                                                                    ");
                                                                    $query_reminders_candidate->execute(array(
                                                                        ':pr_reminder_setting_id' => $prs_id, 
                                                                        ':pr_candidate_id' => $pr_candidate_id
                                                                    ));

                                                                    if ($count_reminders == $query_reminders_candidate->rowCount()) {

                                                                        $level_update = 1;

                                                                        while ($row_reminders_candidate = $query_reminders_candidate->fetch()) {

                                                                            $pr_id = $row_reminders_candidate["pr_id"]; 
                                                                            $pr_status = $row_reminders_candidate["pr_status"];

                                                                            $update = $db->prepare("
                                                                                UPDATE 
                                                                                    idk_pp_reminders 
                                                                                SET 
                                                                                    pr_level = :pr_level
                                                                                WHERE 
                                                                                    pr_id = :pr_id
                                                                            ");
                                                                            $update->execute(array(
                                                                                ':pr_level' => $level_update,
                                                                                ':pr_id' => $pr_id
                                                                            ));

                                                                            if ( $pr_status == 3 ) {
                                                                                $level_update = 1;
                                                                            } else {
                                                                                $level_update++;
                                                                            }

                                                                        }

                                                                    }

                                                                /*
                                                                Kupe se svi reminderi za pr_candidate_id i pr_reminder_setting_id END 
                                                            */
                                                        }

                                                    }

                                                    $candidates_implode = implode(",", $candidates_array);

                                                    echo "<b>       Candidates: </b>".(($candidates_implode != "") ? "<span style='white-space: pre-wrap;white-space: -moz-pre-wrap; white-space: -pre-wrap;white-space: -o-pre-wrap;word-wrap: break-word;'>".$candidates_implode."</span>" : "There are no candidates with more reminders!")."</br>";

                                                    unset($candidates_array);

                                                } else {
                                                    echo "<b>       Notice:</b> No reminders were found for the setting!<br>";
                                                }

                                            /*
                                            Kupe se svi kandidati koji imaju više od jednog remindera za prs_id
                                        */

                                    }
                                    echo "</pre>";

                                } else {
                                    echo "<b>Notice:</b> The search query for reminder settings did not find any results!<br>";
                                }

                            /*
                            Kupe se sve aktivne postavke za prt_id koji nemaju dokumente START 
                        */

                    } else {
                        /*
                            Kupe se sve aktivne postavke za prt_id koji imaju dokumente START 
                            */
                                $query_reminder_settings = $db->prepare("
                                    SELECT 
                                        prs.prs_id, prd.prd_id
                                    FROM 
                                        idk_pp_reminder_settings prs
                                    JOIN 
                                        idk_pp_reminder_documents prd
                                    ON 
                                        prs.prs_id = prd.prd_prs_id 
                                    WHERE 
                                        prs.prs_active = 1 
                                        AND 
                                        prs.prs_reminder_type_id = :reminderType
                                        AND 
                                        prd.prd_active = 1
                                ");
                                $query_reminder_settings->execute(array(
                                    ':reminderType' => $prt_id
                                ));

                                if ($query_reminder_settings->rowCount() != 0) {

                                    echo "<b>Done for reminder settings [reminder setting, document_setting]:</b> <br>";
                                    echo "<pre>";

                                    while ($row_reminder_settings = $query_reminder_settings->fetch()) {

                                        $prs_id = intval($row_reminder_settings["prs_id"]);
                                        $prd_id = intval($row_reminder_settings["prd_id"]);

                                        echo "     <b>[".$prs_id.",".$prd_id."]</b><br>";
                                        /*
                                            Kupe se svi kandidati koji imaju više od jednog remindera za prd_id
                                            */
                                                $query_candidates = $db->prepare("
                                                    SELECT 
                                                        pr_candidate_id, 
                                                        count(pr_id) AS count_reminders
                                                    FROM 
                                                        idk_pp_reminders 
                                                    WHERE 
                                                        pr_reminder_document_id = :pr_reminder_document_id
                                                    GROUP BY 
                                                        pr_candidate_id
                                                ");
                                                $query_candidates->execute(array(
                                                    ':pr_reminder_document_id' => $prd_id
                                                ));

                                                if ($query_candidates->rowCount() != 0){

                                                    $candidates_array = array();

                                                    while ($row_candidates = $query_candidates->fetch()){

                                                        $pr_candidate_id = $row_candidates["pr_candidate_id"]; 
                                                        $count_reminders = $row_candidates["count_reminders"];

                                                        if ($count_reminders > 1){
                                                            array_push($candidates_array, $pr_candidate_id); 

                                                            /*
                                                                Kupe se svi reminderi za pr_candidate_id i pr_reminder_document_id START 
                                                                */

                                                                    $query_reminders_candidate = $db->prepare("
                                                                        SELECT 
                                                                            pr_id, pr_status
                                                                        FROM 
                                                                            idk_pp_reminders
                                                                        WHERE 
                                                                            pr_reminder_document_id = :pr_reminder_document_id
                                                                            AND 
                                                                            pr_candidate_id = :pr_candidate_id 
                                                                        ORDER BY 
                                                                            pr_id 
                                                                        ASC
                                                                    ");
                                                                    $query_reminders_candidate->execute(array(
                                                                        ':pr_reminder_document_id' => $prd_id, 
                                                                        ':pr_candidate_id' => $pr_candidate_id
                                                                    ));

                                                                    if ($count_reminders == $query_reminders_candidate->rowCount()) {

                                                                        $level_update = 1;

                                                                        while ($row_reminders_candidate = $query_reminders_candidate->fetch()) {

                                                                            $pr_id = $row_reminders_candidate["pr_id"]; 
                                                                            $pr_status = $row_reminders_candidate["pr_status"];

                                                                            $update = $db->prepare("
                                                                                UPDATE 
                                                                                    idk_pp_reminders 
                                                                                SET 
                                                                                    pr_level = :pr_level
                                                                                WHERE 
                                                                                    pr_id = :pr_id
                                                                            ");
                                                                            $update->execute(array(
                                                                                ':pr_level' => $level_update,
                                                                                ':pr_id' => $pr_id
                                                                            ));

                                                                            if ( $pr_status == 3 ) {
                                                                                $level_update = 1;
                                                                            } else {
                                                                                $level_update++;
                                                                            }

                                                                        }

                                                                    }

                                                                /*
                                                                Kupe se svi reminderi za pr_candidate_id i pr_reminder_document_id END 
                                                            */
                                                        }

                                                    }

                                                    $candidates_implode = implode(",", $candidates_array);

                                                    echo "<b>       Candidates: </b>".(($candidates_implode != "") ? "<span style='white-space: pre-wrap;white-space: -moz-pre-wrap; white-space: -pre-wrap;white-space: -o-pre-wrap;word-wrap: break-word;'>".$candidates_implode."</span>" : "There are no candidates with more reminders!")."</br>";

                                                    unset($candidates_array);

                                                } else {
                                                    echo "<b>       Notice:</b> No reminders were found for the setting!<br>";
                                                }

                                            /*
                                            Kupe se svi kandidati koji imaju više od jednog remindera za prd_id
                                        */

                                    }
                                    echo "</pre>";

                                } else {
                                    echo "<b>Notice:</b> The search query for reminder settings did not find any results!<br>";
                                }

                            /*
                            Kupe se sve aktivne postavke za prt_id koji nemaju dokumente START 
                        */
                    }

                } else {
                    echo "<b>Notice:</b> The query for type reminder did not find any results!<br>"; 
                }
            /*
            Reminder type info END
        */

    } else {
        echo "<b>Error:</b> Invalid reminder type: ". $reminder_type."<br>"; 
    }


?> 