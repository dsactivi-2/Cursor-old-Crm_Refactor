<?php
include("includes/connect.php");

    $skola_id = $_POST['skola_id'];
    $query_skola_smjer = $db->prepare("
                SELECT ss_naziv, ss_id FROM idk_skole_smjerovi WHERE ss_skola_id IN (".$skola_id.")
        ");
    $query_skola_smjer->execute();
    while($row_skola_smjer = $query_skola_smjer->fetch()){
        $smjer_naziv = $row_skola_smjer['ss_naziv'];
        $smjer_id = $row_skola_smjer['ss_id'];
        
        echo '<option value="'.$smjer_id.'">'.$smjer_naziv.'</option>';
        
    }
?>