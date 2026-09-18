<?php

    include("../includes/common.php");
    include("../includes/functions.php");
    include($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");

    Global $db;
    Global $logged_employee_id;
    $candidates = $_REQUEST['candidates_to_transfer'];
    $destination_order = $_REQUEST['filter_for_order'];

    $update_sp = $db -> prepare('
        UPDATE 
            idk_kandidati
        SET 
            kandidat_status_prijave = 6,
            kandidat_nalog_id = :kandidat_nalog_id, 
            kandidat_latest_reserved_time = :kandidat_latest_reserved_time
        WHERE 
            kandidat_id IN ('.$candidates.')
    ');
    $update_sp -> execute(array(
        ':kandidat_nalog_id' => $destination_order, 
		':kandidat_latest_reserved_time' => date("Y-m-d H:i:s")
    ));


    $update_lsp = $db -> prepare("
        UPDATE idk_log_statusi_prijave
        SET lsp_broj_dana =  DATEDIFF(now(), lsp_datetime)
        WHERE lsp_broj_dana IS NULL 
        AND lsp_kandidat_id IN (".$candidates.")
    ");
    $update_lsp -> execute();

?>