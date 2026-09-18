<?php
	include("../includes/common.php");
    include($_SERVER['DOCUMENT_ROOT']."/includes/connect.php");

    Global $db;
    $vocation_groups = $_REQUEST['filter_vocation_groups'];

    $query_get_vocations = $db -> prepare('
        SELECT main.* 
        FROM (
            SELECT ss.ss_naziv as naziv, ss.ss_id, CASE WHEN ss.ss_struka_id IS NULL THEN 0 ELSE ss.ss_struka_id END AS struka
            FROM idk_skole_smjerovi ss
            LEFT JOIN idk_struke s
            ON s.id_struke = ss.ss_id
        ) main
        JOIN idk_kandidat_edukacija ke
        ON ke.ke_smjer_id = main.ss_id
        WHERE main.struka IN ('.implode(',',$vocation_groups).')
        GROUP BY(main.naziv)
        ORDER BY main.naziv ASC
    ');
    // $query_get_vocations = $db -> prepare('
    //     SELECT * 
    //     FROM (
    //         SELECT ss.ss_naziv as naziv, ss.ss_id, CASE WHEN ss.ss_struka_id IS NULL THEN 0 ELSE ss.ss_struka_id END AS struka
    //         FROM idk_skole_smjerovi ss
    //         LEFT JOIN idk_struke s
    //         ON s.id_struke = ss.ss_id
    //         JOIN idk_kandidat_edukacija ke
    //         ON ke.ke_smjer_id = ss.ss_id
    //         GROUP BY (ss.ss_naziv)  
    //     ) main
    //     WHERE main.struka IN ('.implode(',',$vocation_groups).')
    //     ORDER BY main.naziv ASC
    // ');
    // var_dump($query_get_vocations);
    $query_get_vocations -> execute();

    echo '<select id="filter_vocations" class="selectpicker" multiple data-live-search="true" data-actions-box="true">';
    if(in_array(0,$vocation_groups))
        echo '<option value = "0">Nepoznati kandidati</option>';
    while($row_get_vocations = $query_get_vocations -> fetch()){
        echo '<option value = "'.$row_get_vocations["naziv"].'">'.$row_get_vocations["naziv"].'</option>';
    }
    echo '</select>';
?>