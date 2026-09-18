<?php
    include("../includes/common.php");
    include("../includes/functions.php");
    include($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");

    Global $db;
    Global $logged_employee_id;
    $candidates = $_REQUEST['candidates_to_transfer'];
    $destination_order = $_REQUEST['filter_for_order'];
    
    $query_get_destination_project = $db -> prepare("
        SELECT p.project_id
        FROM idk_projects p
        WHERE p.project_nalogid = $destination_order
        AND p.project_name LIKE ('%Baza - odgovara za nalog%')
    ");
    $query_get_destination_project -> execute();
    $row_get_destination_project = $query_get_destination_project -> fetch();
    $destination_project = $row_get_destination_project['project_id'];


    $insert_statements = implode("','".$destination_project."','6','".$logged_employee_id."', '6', now(), NULL ); INSERT INTO idk_log_statusi_prijave (lsp_kandidat_id, lsp_projekt_id, lsp_status_prijave_id, lsp_employee_id, lsp_izvor, lsp_datetime, lsp_broj_dana) VALUES ('", explode(',',$candidates));
    $insert_statements = " INSERT INTO idk_log_statusi_prijave (lsp_kandidat_id, lsp_projekt_id, lsp_status_prijave_id, lsp_employee_id, lsp_izvor, lsp_datetime, lsp_broj_dana) VALUES ('".$insert_statements."','".$destination_project."','6','".$logged_employee_id."', '6', now(), NULL ); ";
    var_dump($insert_statements);
    $insert_lsp = $db -> prepare($insert_statements);
    $insert_lsp -> execute();

    
    ?>
