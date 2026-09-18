
<?php
    include("../includes/common.php");
    include("../includes/functions.php");
    include($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");

    Global $db;
    Global $logged_employee_id;
    $response = array();
    $candidates = $_REQUEST['candidates_to_transfer'];
    $destination_order = $_REQUEST['filter_for_order'];
    $flag_transfer = true;
    if(is_null($candidates)){
        $flag_transfer = false;
    }
    else{
        $candidates = explode(',', $candidates);
        if(!count($candidates)){
            $flag_transfer = false;
        }
    }
    $flag_update_sp = false;

    $query_get_destination_project = $db -> prepare("
        SELECT p.project_id
        FROM idk_projects p
        WHERE p.project_nalogid = $destination_order
        AND p.project_name LIKE ('%Baza - odgovara za nalog%')
    ");
    
    $query_get_destination_project -> execute();
    if($query_get_destination_project -> rowCount() != 0){
        if($flag_transfer){
            $row_get_destination_project = $query_get_destination_project -> fetch();
            $destination_project = $row_get_destination_project['project_id'];
            $query_insert_log = $db -> prepare("
                INSERT INTO idk_tf_transfered_candidates_logs (tcl_transfered_candidates, tcl_destination_project_id,  tcl_employee_id)
                VALUES ('".implode(',',$candidates)."', '$destination_project', '$logged_employee_id')
            ");
            $query_insert_log -> execute();
            
            $insert_statements = implode("'); INSERT INTO idk_project_kandidati (pk_projectid, pk_kandidatid) VALUES ('".$destination_project."','", $candidates);
            $insert_statements = " INSERT INTO idk_project_kandidati (pk_projectid, pk_kandidatid) VALUES ('".$destination_project."','".$insert_statements."'); ";

           
            $insert_candidates = $db -> prepare(
                $insert_statements
            );
        
            if(count($candidates) < 5000){
                if($insert_candidates -> execute()){

                    
                    $flag_update_sp = true;
                    $response[0]['response'] = "Kandidati uspješno prebačeni";
                    $response[0]['update'] = "1";

                }
                else{
                    $response[0]['response'] = "Napravite screenshot ovog prozora i javite se development timu. Prekoračen je limit transfera i potrebno je uprebaciti kandidate ručno.<br> <b>ID: ".$logged_employee_id."<br>DATETIME: ".date('Y-m-d H:i:s')."<br>COUNT: ".count($candidates)."</b>";
                    $response[0]['update'] = "0";

                }
            }
            else{
                $response[0]['response'] = "Napravite screenshot ovog prozora i javite se development timu. Prekoračen je limit transfera i potrebno je uprebaciti kandidate ručno.<br> <b>ID: ".$logged_employee_id."<br>DATETIME: ".date('Y-m-d H:i:s')."<br>COUNT: ".count($candidates)."</b>";
                $response[0]['update'] = "0";
            }
        }
        else{
            $response[0]['response'] = "GREŠKA!!! Kandidati koji se prebacuju su oni ispisani u tabeli nakon klika na dugme 'trazi'";
            $response[0]['update'] = "0";
        }
        
    }
    else{
        $response[0]['response'] = "GREŠKA!!! Nije moguće prebaciti kandidate. Odabrani nalog nema projekat naziva 'Baza - odgovara za nalog'";
        $response[0]['update'] = "0";
    }

    echo json_encode($response);
?>