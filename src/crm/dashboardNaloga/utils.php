<?php
include($_SERVER["DOCUMENT_ROOT"] . "/includes/connect.php");

function getNazivNaloga($id)
{
    global $db;
    $query = $db->prepare("
            SELECT 
                nalog_naziv
            FROM 
                idk_nalozi 
            WHERE
                nalog_id = :nalog_id
        ");
    $query->execute([
        ':nalog_id' => $id
    ]);

    return $query->fetch()[0];
}

function getNazivStatusa($id)
{
    global $db;
    $query = $db->prepare("
            SELECT 
                status_naziv
            FROM 
                idk_kandidat_status_prijave 
            WHERE
                status_id = :status_id
        ");
    $query->execute([
        ':status_id' => $id
    ]);

    return $query->fetch()[0];
}