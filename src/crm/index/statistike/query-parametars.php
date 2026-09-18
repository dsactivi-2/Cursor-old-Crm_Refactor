<?php
	
/**************************************
*
** CREATED 27.08.2021 -- 
** VARIABLES FOR STATISTICS TABLES 
*
*
** Last update - Ismail Suljic - Date: 
*
*/
	

//$trenutni_datum = "2021-01-19 17:25:18";
//Sve je radjeno na nacin zbog prelaska u novu godinu da se ne poremete godine
$currentWeekNumber = date('W');
$trenutni_datum = date("Y-m-d H:i:s");
$currentDay = date("Y-m-d");
$week1 = date("W", strtotime($trenutni_datum));
$week2 = date("W", strtotime("-1 Week", strtotime($trenutni_datum)));
$week3 = date("W", strtotime("-2 Week", strtotime($trenutni_datum)));
$week4 = date("W", strtotime("-3 Week", strtotime($trenutni_datum)));
$week5 = date("W", strtotime("-4 Week", strtotime($trenutni_datum)));
$week6 = date("W", strtotime("-5 Week", strtotime($trenutni_datum)));
$year1 = date("Y", strtotime($trenutni_datum));
$year2 = date("Y", strtotime("-1 Week", strtotime($trenutni_datum)));
$year3 = date("Y", strtotime("-2 Week", strtotime($trenutni_datum)));
$year4 = date("Y", strtotime("-3 Week", strtotime($trenutni_datum)));
$year5 = date("Y", strtotime("-4 Week", strtotime($trenutni_datum)));
$year6 = date("Y", strtotime("-5 Week", strtotime($trenutni_datum)));

$result1 = getStartAndEndDate($week1, $year1);
$result2 = getStartAndEndDate($week2, $year2);
$result3 = getStartAndEndDate($week3, $year3);
$result4 = getStartAndEndDate($week4, $year4);
$result5 = getStartAndEndDate($week5, $year5);
$result6 = getStartAndEndDate($week6, $year6);
$pocetak0 = date("Y-m-d H:i:s", strtotime($currentDay." 00:00:00"));
$kraj0 = date("Y-m-d H:i:s", strtotime($currentDay." 23:59:59"));
$pocetak1 = date("Y-m-d H:i:s", strtotime($result1['week_start']." 00:00:00"));
$kraj1 = date("Y-m-d H:i:s", strtotime($result1['week_end']." 23:59:59"));
$pocetak2 = date("Y-m-d H:i:s", strtotime($result2['week_start']." 00:00:00"));
$kraj2 = date("Y-m-d H:i:s", strtotime($result2['week_end']." 23:59:59"));
$pocetak3 = date("Y-m-d H:i:s", strtotime($result3['week_start']." 00:00:00"));
$kraj3 = date("Y-m-d H:i:s", strtotime($result3['week_end']." 23:59:59"));
$pocetak4 = date("Y-m-d H:i:s", strtotime($result4['week_start']." 00:00:00"));
$kraj4 = date("Y-m-d H:i:s", strtotime($result4['week_end']." 23:59:59"));
$pocetak5 = date("Y-m-d H:i:s", strtotime($result5['week_start']." 00:00:00"));
$kraj5 = date("Y-m-d H:i:s", strtotime($result5['week_end']." 23:59:59"));
$pocetak6 = date("Y-m-d H:i:s", strtotime($result6['week_start']." 00:00:00"));
$kraj6 = date("Y-m-d H:i:s", strtotime($result6['week_end']." 23:59:59"));

$title6 = ' Period od '.date("d.m.Y H:i:s", strtotime($pocetak6)).' do '.date("d.m.Y H:i:s", strtotime($kraj6)).' ';
$title5 = ' Period od '.date("d.m.Y H:i:s", strtotime($pocetak5)).' do '.date("d.m.Y H:i:s", strtotime($kraj5)).' ';
$title4 = ' Period od '.date("d.m.Y H:i:s", strtotime($pocetak4)).' do '.date("d.m.Y H:i:s", strtotime($kraj4)).' ';
$title3 = ' Period od '.date("d.m.Y H:i:s", strtotime($pocetak3)).' do '.date("d.m.Y H:i:s", strtotime($kraj3)).' ';
$title2 = ' Period od '.date("d.m.Y H:i:s", strtotime($pocetak2)).' do '.date("d.m.Y H:i:s", strtotime($kraj2)).' ';
$title1 = ' Period od '.date("d.m.Y H:i:s", strtotime($pocetak1)).' do '.date("d.m.Y H:i:s", strtotime($kraj1)).' ';
$title0 = ' Period od '.date("d.m.Y H:i:s", strtotime($pocetak0)).' do '.date("d.m.Y H:i:s", strtotime($kraj0)).' ';

$title_ukupno = ' Period od '.date("d.m.Y H:i:s", strtotime($pocetak4)).' do '.date("d.m.Y H:i:s", strtotime($kraj1)).' ';

$team_i = getLoggedEmployeeTeam();
$employess_id_team = implode(", ", getIdOfEmployeeTeam($team_i));
if($team_i == 1){
	$uslov_poslovnica = "pos.branch_id is not null";
	$uslov_otvori = " employee_id is not null";
}else{
	$uslov_otvori = " employee_id IN (".$employess_id_team.")";
	$provjera_poslovnice = $db->prepare("SELECT employee_poslovnica FROM idk_employees WHERE employee_id = ".$logged_employee_id."");
	$provjera_poslovnice->execute();
	$poslovnica_zaposlenik = $provjera_poslovnice->fetch();
	
	$uslov_poslovnica = "pos.branch_id = ".intval($poslovnica_zaposlenik["employee_poslovnica"])."";
}


//STATISTIKA, PN sedmicna i mjesecna, PP sedmicna i mjesecna, graf START------------------------------------------------------------------------------------------------
if(in_array("1", $employee_status) OR in_array("9", $employee_status) OR in_array("69", $employee_status) OR ($logged_employee_id == 32)  OR ($logged_employee_id == 158)  OR ($logged_employee_id == 108)){
	//if(($logged_employee_id == 0)){
	$query_stat_predracuni = $db->prepare("
		SELECT 
		sum(case when pr_uplaceno=1 and pr_datum_uplate BETWEEN date(curdate() - interval 30 DAY) AND date(curdate()- interval 1 day)  then 1 else 0 end) as uplacen, 
		sum(case when date(pr_datum_kreiranja) BETWEEN date(curdate() - interval 30 DAY) AND date(curdate() - interval 1 day)  then 1 else 0 end) as izdan
		FROM idk_predracuni
		WHERE
		pr_rata=1 AND pr_status is not NULL AND pr_vrsta_predracuna = 1
		AND pr_id IN(SELECT MAX(pr.pr_id) FROM idk_predracuni pr WHERE pr.pr_rata = 1 GROUP BY pr.pr_kandidat_id)
	
	");
	$query_stat_predracuni->execute();
	$row_stat_predracuni=$query_stat_predracuni->fetch();
	$pn = round($row_stat_predracuni['uplacen']/(30-brVikenda(30)),2);
	$pp = round($row_stat_predracuni['izdan']/(30-brVikenda(30)),2);
	
	$query_stat_predracuni_sedmica = $db->prepare("
		SELECT 
		sum(case when pr_datum_uplate BETWEEN date(curdate() - interval 7 day) AND date(curdate() - interval 1 day) and pr_uplaceno=1 then 1 else 0 end) as uplacen, 
		sum(case when date(pr_datum_kreiranja) BETWEEN date(curdate() - interval 7 day) and date(curdate() - interval 1 day) then 1 else 0 end) as izdan
		FROM idk_predracuni
		WHERE pr_rata = 1 AND pr_status is not NULL AND pr_vrsta_predracuna = 1
		AND pr_id IN(SELECT MAX(pr.pr_id) FROM idk_predracuni pr WHERE pr.pr_rata = 1 GROUP BY pr.pr_kandidat_id)
	");
	$query_stat_predracuni_sedmica->execute();
	$row_stat_predracuni_sedmica=$query_stat_predracuni_sedmica->fetch();
	$pn_sed = round($row_stat_predracuni_sedmica['uplacen']/(7-brVikenda(7)),2);
	$pp_sed = round($row_stat_predracuni_sedmica['izdan']/(7-brVikenda(7)),2);
	
	$query_stat_predracuni_90dana = $db->prepare("
		SELECT 
		sum(case when pr_datum_uplate BETWEEN date(curdate() - interval 90 day) AND date(curdate() - interval 1 day) and pr_uplaceno=1 then 1 else 0 end) as uplacen, 
		sum(case when date(pr_datum_kreiranja) BETWEEN date(curdate() - interval 90 day) and date(curdate() - interval 1 day) then 1 else 0 end) as izdan
		FROM idk_predracuni
		WHERE pr_rata = 1 AND pr_status is not NULL AND pr_vrsta_predracuna = 1
		AND pr_id IN(SELECT MAX(pr.pr_id) FROM idk_predracuni pr WHERE pr.pr_rata = 1 GROUP BY pr.pr_kandidat_id)
	");
	$query_stat_predracuni_90dana->execute();
	$row_stat_predracuni_90dana=$query_stat_predracuni_90dana->fetch();
	$pn_90dana = round($row_stat_predracuni_90dana['uplacen']/(90-brVikenda(90)),2);
	$pp_90dana = round($row_stat_predracuni_90dana['izdan']/(90-brVikenda(90)),2);
	
	$query_stat_predracuni_6mjeseci = $db->prepare("
		SELECT 
		sum(case when pr_datum_uplate BETWEEN date(curdate() - interval 180 day) AND date(curdate() - interval 1 day) and pr_uplaceno=1 then 1 else 0 end) as uplacen, 
		sum(case when date(pr_datum_kreiranja) BETWEEN date(curdate() - interval 180 day) and date(curdate() - interval 1 day) then 1 else 0 end) as izdan
		FROM idk_predracuni
		WHERE pr_rata = 1 AND pr_status is not NULL AND pr_vrsta_predracuna = 1
		AND pr_id IN(SELECT MAX(pr.pr_id) FROM idk_predracuni pr WHERE pr.pr_rata = 1 GROUP BY pr.pr_kandidat_id)
	");
	$query_stat_predracuni_6mjeseci->execute();
	$row_stat_predracuni_6mjeseci=$query_stat_predracuni_6mjeseci->fetch();
	$pn_6mjeseci = round($row_stat_predracuni_6mjeseci['uplacen']/(180-brVikenda(180)),2);
	$pp_6mjeseci = round($row_stat_predracuni_6mjeseci['izdan']/(180-brVikenda(180)),2);
	
	$query_stat_predracuni_12mjeseci = $db->prepare("
		SELECT 
		sum(case when pr_datum_uplate BETWEEN date(curdate() - interval 365 day) AND date(curdate() - interval 1 day) and pr_uplaceno=1 then 1 else 0 end) as uplacen, 
		sum(case when date(pr_datum_kreiranja) BETWEEN date(curdate() - interval 365 day) and date(curdate() - interval 1 day) then 1 else 0 end) as izdan
		FROM idk_predracuni
		WHERE pr_rata = 1 AND pr_status is not NULL AND pr_vrsta_predracuna = 1
		AND pr_id IN(SELECT MAX(pr.pr_id) FROM idk_predracuni pr WHERE pr.pr_rata = 1 GROUP BY pr.pr_kandidat_id)
	");
	$query_stat_predracuni_12mjeseci->execute();
	$row_stat_predracuni_12mjeseci=$query_stat_predracuni_12mjeseci->fetch();
	$pn_12mjeseci = round($row_stat_predracuni_12mjeseci['uplacen']/(365-brVikenda(365)),2);
	$pp_12mjeseci = round($row_stat_predracuni_12mjeseci['izdan']/(365-brVikenda(365)),2);
}
?>