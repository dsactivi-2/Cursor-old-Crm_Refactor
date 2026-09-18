<?php 
	
/**************************************
*
** CREATED 27.08.2021 -- 
** AGENT PERSONAL STATISTICS COMPONENT
*
*
** Last update - Ismail Suljic - Date: 
*
*/
	

$employee_status = explode( ',' , getEmployeeStatus());

function getStartAndEndDate($week, $year) {
	$dto = new DateTime();
	$ret['week_start'] = $dto->setISODate($year, $week)->format('Y-m-d');
	$ret['week_end'] = $dto->modify('+6 days')->format('Y-m-d');
	return $ret;
}
function getStyleColumn($vr1, $vr2, $vr3){
	return '<span style ="margin-left: 10px; padding: 0px 5px;">'.$vr1.'/'.$vr2.'/'.$vr3.'</span>';
}
function getStyleColumn1($vr1, $vr2, $vr3, $vr4){
	if($vr1 == 0){
		$vr1st = '<span style = "padding: 0px 1px; font-weight: bold;">'.$vr1.'</span>';
	}else{
		$vr1st = '<span style = "padding: 0px 5px; background-color: red; color: white; font-weight: bold; border-radius: 15px;">'.$vr1.'</span>';
	}
	
	return '<span style ="padding: 0px 5px;">'.$vr1st.'/'.$vr2.'/'.$vr3.'/'.$vr4.'</span>';
}
?>