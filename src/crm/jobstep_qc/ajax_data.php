<?php

include("includes/functions.php");
include("includes/connect.php");

$form="";

if(isset($_REQUEST["form"])) {
	$form = $_REQUEST["form"];
}

switch ($form)
{
	case "obrada_zahtjeva_sluzbeni_put":
		$zaposlenik_id = $logged_employee_id;
		$period_sluzbeni_put = $_REQUEST['period_sluzbeni_put'];
		$period_sluzbeni_put = explode(" to ", $period_sluzbeni_put);
		$datum_od = $period_sluzbeni_put[0];
		$datum_do = $period_sluzbeni_put[1];
		if(is_null($datum_do)){
			$datum_do = $datum_od;
		}
		$relacija_od = "'".$_REQUEST['relacija_od']."'";
		$relacija_do = "'".$_REQUEST['relacija_do']."'";
		$dnevnica_dana = 0;
		$sluzbeno_vozilo_dana = 0;
		$trajanje_rezervacije = 0;
		$tip_troska_smjestaja = 0;
		if($_REQUEST['check_dnevnica'] == "true"){
			$dnevnica_dana = $_REQUEST['dnevnica_dana'];
		}
		if($_REQUEST['check_sluzbeno_vozilo'] == "true"){
			$sluzbeno_vozilo_dana = $_REQUEST['sluzbeno_vozilo_dana'];	
		}
		if($_REQUEST['check_smjestaj'] == "true"){
			$trajanje_rezervacije = $_REQUEST['trajanje_rezervacije'];				
		}

		if($_REQUEST['check_gotovina'] == "true"){
			$tip_troska_smjestaja = "1";
		}
		else if($_REQUEST['check_racun'] == "true"){
			$tip_troska_smjestaja = "2";
		}
		
		$query_insert_sluzbeni_put = $db->prepare('
			INSERT INTO idk_hr_sluzbeni_put (datum_od, datum_do, relacija_od, relacija_do, dnevnica_dana, tip_troska_smjestaja, trajanje_rezervacije, sluzbeno_vozilo_dana, zaposlenik_id, datum_podnosenja)
			VALUES (:datum_od, :datum_do, :relacija_od, :relacija_do, :dnevnica_dana, :tip_troska_smjestaja, :trajanje_rezervacije, :sluzbeno_vozilo_dana, :zaposlenik_id, now())
		');
		$query_insert_sluzbeni_put -> execute(array(
			':datum_od' => $datum_od,
			':datum_do' => $datum_do,
			':relacija_od' => $relacija_od,
			':relacija_do' => $relacija_do,
			':dnevnica_dana' => $dnevnica_dana,
			':tip_troska_smjestaja' => $tip_troska_smjestaja,
			':trajanje_rezervacije' => $trajanje_rezervacije,
			':sluzbeno_vozilo_dana' => $sluzbeno_vozilo_dana,
			':zaposlenik_id' => $zaposlenik_id
		));
	break;
}
?>