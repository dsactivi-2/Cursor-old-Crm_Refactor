<?php
	include("../includes/functions.php");
	include("../includes/common.php");
    Global $db;
    $candidates = $_REQUEST['candidates'];

    $query_get_candidates = $db -> prepare('
        SELECT kan.kandidat_id, kan.kandidat_ime, kan.kandidat_prezime, kan.kandidat_mobitel, kan.kandidat_email
        FROM idk_kandidati kan
        WHERE kan.kandidat_id IN ('.$candidates.')
    ');

    $query_get_candidates -> execute();

    echo '<table>';

    $candidate_id = "";
    $candidate_name = "";
    $candidate_lastname = "";
    $candidate_phone = "";
    $candidate_email = "";

    while($row = $query_get_candidates -> fetch()){
        $candidate_id = $row['kandidat_id'];
        $candidate_name = $row['kandidat_ime'];
        $candidate_lastname = $row['kandidat_prezime'];
        $candidate_phone = $row['kandidat_mobitel'];
        $candidate_email = $row['kandidat_email'];

        echo '
            <tr>
                <td>'.$candidate_id.'</td>
                <td>'.$candidate_name.'</td>
                <td>'.$candidate_lastname.'</td>
                <td>'.$candidate_phone.'</td>
                <td>'.$candidate_email.'</td>
        ';
    }

    echo '</table>';
