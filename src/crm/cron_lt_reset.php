<?php

include "includes/connect.php";
include "includes/functions.php";
Global $db;
// echo date("d-m-Y H:i:s");
// echo "<br>".date("d", strtotime('+13 hours'));

$q1="";
$q2="";
if(date("D")=="Fri")
    $q1=", lt_sedmicni_cnt=0";
if(date("d", strtotime('+7 hours'))=="01")
    $q2=", lt_mjesecni_cnt=0";




$query_reset=$db->prepare('

    UPDATE idk_nd_limiti
    SET lt_dnevni_cnt=0'.$q1.$q2
);

$query_reset->execute();


?>