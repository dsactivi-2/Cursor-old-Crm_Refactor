<?php 
	
/**************************************
*
** CREATED 27.08.2021 -- 
** GRAPH ZA PREDRAČUNE
*
*
** Last update - Ismail Suljic - Date: 
*
*/
	

?>


<div class="container-fluid">
	<div class = "row">
		<div class="col-md-12 col-xs-12">
			<div class="idk_box idk_box_shadow" style="min-height: 440px !important;">
				<?php
					$query_stat_izdani = $db->prepare("
						SELECT 
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 0 week))=yearweek(pr_datum_kreiranja) then 1 else 0 end) as w0,
							yearweek(DATE_SUB(CURRENT_DATE(), interval 0 week)) as wd0,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 1 week))=yearweek(pr_datum_kreiranja) then 1 else 0 end) as w1,
							yearweek(DATE_SUB(CURRENT_DATE(), interval 1 week)) as wd1,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 2 week))=yearweek(pr_datum_kreiranja) then 1 else 0 end) as w2,
							yearweek(DATE_SUB(CURRENT_DATE(), interval 2 week)) as wd2,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 3 week))=yearweek(pr_datum_kreiranja) then 1 else 0 end) as w3,
							yearweek(DATE_SUB(CURRENT_DATE(), interval 3 week))as wd3,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 4 week))=yearweek(pr_datum_kreiranja) then 1 else 0 end) as w4,
							yearweek(DATE_SUB(CURRENT_DATE(), interval 4 week))as wd4,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 5 week))=yearweek(pr_datum_kreiranja) then 1 else 0 end) as w5,
							yearweek(DATE_SUB(CURRENT_DATE(), interval 5 week))as wd5,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 6 week))=yearweek(pr_datum_kreiranja) then 1 else 0 end) as w6,
							yearweek(DATE_SUB(CURRENT_DATE(), interval 6 week))as wd6,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 7 week))=yearweek(pr_datum_kreiranja) then 1 else 0 end) as w7,
							yearweek(DATE_SUB(CURRENT_DATE(), interval 7 week))as wd7,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 8 week))=yearweek(pr_datum_kreiranja) then 1 else 0 end) as w8,
							yearweek(DATE_SUB(CURRENT_DATE(), interval 8 week))as wd8,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 9 week))=yearweek(pr_datum_kreiranja) then 1 else 0 end) as w9,
							yearweek(DATE_SUB(CURRENT_DATE(), interval 9 week))as wd9,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 10 week))=yearweek(pr_datum_kreiranja) then 1 else 0 end) as w10,
							yearweek(DATE_SUB(CURRENT_DATE(), interval 10 week))as wd10,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 11 week))=yearweek(pr_datum_kreiranja) then 1 else 0 end) as w11,
							yearweek(DATE_SUB(CURRENT_DATE(), interval 11 week))as wd11,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 12 week))=yearweek(pr_datum_kreiranja) then 1 else 0 end) as w12,
							yearweek(DATE_SUB(CURRENT_DATE(), interval 12 week))as wd12
						FROM idk_predracuni
						WHERE pr_rata = 1 AND pr_status is not null AND pr_vrsta_predracuna = 1
						AND pr_id IN(SELECT MAX(pr.pr_id) FROM idk_predracuni pr WHERE pr.pr_rata = 1 GROUP BY pr.pr_kandidat_id)
					");
					$query_stat_izdani->execute();
					$niz_kolicina_izdanih=array();
					$niz_kolicina_naplacenih=array();
					$niz_datumi=array();
					$niz_uplaceni_dat_kr=array();
					$row_kol_izdani = $query_stat_izdani->fetch();
					array_push($niz_kolicina_izdanih, $row_kol_izdani['w0']);
					array_push($niz_kolicina_izdanih, $row_kol_izdani['w1']);	
					array_push($niz_kolicina_izdanih, $row_kol_izdani['w2']);	
					array_push($niz_kolicina_izdanih, $row_kol_izdani['w3']);
					array_push($niz_kolicina_izdanih, $row_kol_izdani['w4']);	
					array_push($niz_kolicina_izdanih, $row_kol_izdani['w5']);
					array_push($niz_kolicina_izdanih, $row_kol_izdani['w6']);
					array_push($niz_kolicina_izdanih, $row_kol_izdani['w7']);	
					array_push($niz_kolicina_izdanih, $row_kol_izdani['w8']);
					array_push($niz_kolicina_izdanih, $row_kol_izdani['w9']);
					array_push($niz_kolicina_izdanih, $row_kol_izdani['w10']);	
					array_push($niz_kolicina_izdanih, $row_kol_izdani['w11']);	
					array_push($niz_kolicina_izdanih, $row_kol_izdani['w12']);

					array_push($niz_datumi, substr($row_kol_izdani['wd0'],-2));
					array_push($niz_datumi, substr($row_kol_izdani['wd1'],-2));
					array_push($niz_datumi, substr($row_kol_izdani['wd2'],-2));
					array_push($niz_datumi, substr($row_kol_izdani['wd3'],-2));
					array_push($niz_datumi, substr($row_kol_izdani['wd4'],-2));
					array_push($niz_datumi, substr($row_kol_izdani['wd5'],-2));
					array_push($niz_datumi, substr($row_kol_izdani['wd6'],-2));
					array_push($niz_datumi, substr($row_kol_izdani['wd7'],-2));
					array_push($niz_datumi, substr($row_kol_izdani['wd8'],-2));
					array_push($niz_datumi, substr($row_kol_izdani['wd9'],-2));
					array_push($niz_datumi, substr($row_kol_izdani['wd10'],-2));
					array_push($niz_datumi, substr($row_kol_izdani['wd11'],-2));
					array_push($niz_datumi, substr($row_kol_izdani['wd12'],-2));
					
					$query_stat_naplaceni = $db->prepare("
						SELECT 
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 0 week))=yearweek(pr_datum_uplate) then 1 else 0 end) as w0,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 1 week))=yearweek(pr_datum_uplate) then 1 else 0 end) as w1,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 2 week))=yearweek(pr_datum_uplate) then 1 else 0 end) as w2,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 3 week))=yearweek(pr_datum_uplate) then 1 else 0 end) as w3,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 4 week))=yearweek(pr_datum_uplate) then 1 else 0 end) as w4,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 5 week))=yearweek(pr_datum_uplate) then 1 else 0 end) as w5,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 6 week))=yearweek(pr_datum_uplate) then 1 else 0 end) as w6,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 7 week))=yearweek(pr_datum_uplate) then 1 else 0 end) as w7,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 8 week))=yearweek(pr_datum_uplate) then 1 else 0 end) as w8,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 9 week))=yearweek(pr_datum_uplate) then 1 else 0 end) as w9,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 10 week))=yearweek(pr_datum_uplate) then 1 else 0 end) as w10,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 11 week))=yearweek(pr_datum_uplate) then 1 else 0 end) as w11,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 12 week))=yearweek(pr_datum_uplate) then 1 else 0 end) as w12
						FROM idk_predracuni
						WHERE pr_rata = 1 AND pr_status != 0 AND pr_vrsta_predracuna = 1 AND pr_uplaceno = 1
					");
					$query_stat_naplaceni->execute();
					while($row_kol_naplaceni = $query_stat_naplaceni->fetch()){
						array_push($niz_kolicina_naplacenih, $row_kol_naplaceni['w0']);
						array_push($niz_kolicina_naplacenih, $row_kol_naplaceni['w1']);	
						array_push($niz_kolicina_naplacenih, $row_kol_naplaceni['w2']);	
						array_push($niz_kolicina_naplacenih, $row_kol_naplaceni['w3']);
						array_push($niz_kolicina_naplacenih, $row_kol_naplaceni['w4']);	
						array_push($niz_kolicina_naplacenih, $row_kol_naplaceni['w5']);
						array_push($niz_kolicina_naplacenih, $row_kol_naplaceni['w6']);
						array_push($niz_kolicina_naplacenih, $row_kol_naplaceni['w7']);	
						array_push($niz_kolicina_naplacenih, $row_kol_naplaceni['w8']);
						array_push($niz_kolicina_naplacenih, $row_kol_naplaceni['w9']);
						array_push($niz_kolicina_naplacenih, $row_kol_naplaceni['w10']);	
						array_push($niz_kolicina_naplacenih, $row_kol_naplaceni['w11']);	
						array_push($niz_kolicina_naplacenih, $row_kol_naplaceni['w12']);
						
					}
					//uplaceni po datumu kreiranja
					$query_uplaceni_kreiranja = $db->prepare("
						SELECT 
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 0 week))=yearweek(pr_datum_kreiranja) then 1 else 0 end) as w0,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 1 week))=yearweek(pr_datum_kreiranja) then 1 else 0 end) as w1,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 2 week))=yearweek(pr_datum_kreiranja) then 1 else 0 end) as w2,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 3 week))=yearweek(pr_datum_kreiranja) then 1 else 0 end) as w3,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 4 week))=yearweek(pr_datum_kreiranja) then 1 else 0 end) as w4,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 5 week))=yearweek(pr_datum_kreiranja) then 1 else 0 end) as w5,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 6 week))=yearweek(pr_datum_kreiranja) then 1 else 0 end) as w6,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 7 week))=yearweek(pr_datum_kreiranja) then 1 else 0 end) as w7,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 8 week))=yearweek(pr_datum_kreiranja) then 1 else 0 end) as w8,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 9 week))=yearweek(pr_datum_kreiranja) then 1 else 0 end) as w9,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 10 week))=yearweek(pr_datum_kreiranja) then 1 else 0 end) as w10,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 11 week))=yearweek(pr_datum_kreiranja) then 1 else 0 end) as w11,
							sum(case when yearweek(DATE_SUB(CURRENT_DATE(), interval 12 week))=yearweek(pr_datum_kreiranja) then 1 else 0 end) as w12
						FROM idk_predracuni
						WHERE pr_rata = 1 AND pr_status != 0 AND pr_vrsta_predracuna = 1 AND pr_uplaceno = 1
					");
					$query_uplaceni_kreiranja->execute();
					while($row_uplaceni_kreiranja = $query_uplaceni_kreiranja->fetch()){
						array_push($niz_uplaceni_dat_kr, $row_uplaceni_kreiranja['w0']);
						array_push($niz_uplaceni_dat_kr, $row_uplaceni_kreiranja['w1']);	
						array_push($niz_uplaceni_dat_kr, $row_uplaceni_kreiranja['w2']);	
						array_push($niz_uplaceni_dat_kr, $row_uplaceni_kreiranja['w3']);
						array_push($niz_uplaceni_dat_kr, $row_uplaceni_kreiranja['w4']);	
						array_push($niz_uplaceni_dat_kr, $row_uplaceni_kreiranja['w5']);
						array_push($niz_uplaceni_dat_kr, $row_uplaceni_kreiranja['w6']);
						array_push($niz_uplaceni_dat_kr, $row_uplaceni_kreiranja['w7']);	
						array_push($niz_uplaceni_dat_kr, $row_uplaceni_kreiranja['w8']);
						array_push($niz_uplaceni_dat_kr, $row_uplaceni_kreiranja['w9']);
						array_push($niz_uplaceni_dat_kr, $row_uplaceni_kreiranja['w10']);	
						array_push($niz_uplaceni_dat_kr, $row_uplaceni_kreiranja['w11']);	
						array_push($niz_uplaceni_dat_kr, $row_uplaceni_kreiranja['w12']);
						
					}
					$date0=adjustDate(date("Y-m-d"));
					$date1=adjustDate(date("Y-m-d", strtotime("-1 week")));
					$date2=adjustDate(date("Y-m-d", strtotime("-2 week")));
					$date3=adjustDate(date("Y-m-d", strtotime("-3 week")));
					$date4=adjustDate(date("Y-m-d", strtotime("-4 week")));
					$date5=adjustDate(date("Y-m-d", strtotime("-5 week")));
					$date6=adjustDate(date("Y-m-d", strtotime("-6 week")));
					$date7=adjustDate(date("Y-m-d", strtotime("-7 week")));
					$date8=adjustDate(date("Y-m-d", strtotime("-8 week")));
					$date9=adjustDate(date("Y-m-d", strtotime("-9 week")));
					$date10=adjustDate(date("Y-m-d", strtotime("-10 week")));
					$date11=adjustDate(date("Y-m-d", strtotime("-11 week")));
				?>
					
				<div id="chartContainer"></div>
				
				<script src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>
				<script src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>
				<script>
					var options = {
						exportEnabled: true,
						animationEnabled: true,
						title:{
							text: "Statistika predračuna"
						},
						subtitles: [{
							text: ""
						}],
						axisX: {
							title: "Vremenski period",
							interval: 1,
							intervalType: "month"
						},
						axisY: {
							title: "Količina",
							titleFontColor: "#333",
							lineColor: "#333",
							labelFontColor: "#333",
							tickColor: "#333"
						},
						axisY2: {
							title: "Procenat uplate",
							suffix: " %",
							titleFontColor: "#FF0000",
							lineColor: "#FF0000",
							labelFontColor: "#FF0000",
							tickColor: "#FF0000"
						},


						toolTip: {
							shared: true
						},
						legend: {
							cursor: "pointer",
							itemclick: toggleDataSeries
						},
						data: [
						{
							type: "splineArea",
							name: "Izdani Predračuni",
							showInLegend: true,
							xValueFormatString: "MMM DD YYYY",
							yValueFormatString: "#",
							color: "rgba(64, 146, 217)",
							dataPoints: [
								{ x: new Date(<?php echo $date11; ?>),  y: <?php echo $niz_kolicina_izdanih[11];?>, color: "#333", markerSize: 5, toolTipContent: "<?php echo 'KS-'.$niz_datumi[11];?><br>Izdani Predračuni :{y}"},
								{ x: new Date(<?php echo $date10; ?>), y: <?php echo $niz_kolicina_izdanih[10]; ?>, color: "#333", markerSize: 5, toolTipContent: "<?php echo 'KS-'.$niz_datumi[10];?><br>Izdani Predračuni :{y} <?php  echo rast($niz_kolicina_izdanih[11],$niz_kolicina_izdanih[10]);?>"},
								{ x: new Date(<?php echo $date9; ?>), y: <?php echo $niz_kolicina_izdanih[9]; ?>, color: "#333", markerSize: 5, toolTipContent: "<?php echo 'KS-'.$niz_datumi[9];?><br>Izdani Predračuni :{y} <?php  echo rast($niz_kolicina_izdanih[10],$niz_kolicina_izdanih[9]);?>"},
								{ x: new Date(<?php echo $date8; ?>),  y:<?php echo $niz_kolicina_izdanih[8]; ?>, color: "#333", markerSize: 5, toolTipContent: "<?php echo 'KS-'.$niz_datumi[8];?><br>Izdani Predračuni :{y} <?php  echo rast($niz_kolicina_izdanih[9],$niz_kolicina_izdanih[8]);?>"},
								{ x: new Date(<?php echo $date7; ?>),  y: <?php echo $niz_kolicina_izdanih[7]; ?>, color: "#333",  markerSize: 5, toolTipContent: "<?php echo 'KS-'.$niz_datumi[7];?><br>Izdani Predračuni :{y} <?php  echo rast($niz_kolicina_izdanih[8],$niz_kolicina_izdanih[7]);?>"},
								{ x: new Date(<?php echo $date6; ?>),  y: <?php echo $niz_kolicina_izdanih[6]; ?>, color: "#333",  markerSize: 5, toolTipContent: "<?php echo 'KS-'.$niz_datumi[6];?><br>Izdani Predračuni :{y} <?php  echo rast($niz_kolicina_izdanih[7],$niz_kolicina_izdanih[6]);?>"},
								{ x: new Date(<?php echo $date5; ?>), y: <?php echo $niz_kolicina_izdanih[5]; ?>, color: "#333", markerSize: 5, toolTipContent: "<?php echo 'KS-'.$niz_datumi[5];?><br>Izdani Predračuni :{y} <?php  echo rast($niz_kolicina_izdanih[6],$niz_kolicina_izdanih[5]);?>"},
								{ x: new Date(<?php echo $date4; ?>), y: <?php echo $niz_kolicina_izdanih[4]; ?>, color: "# 333", markerSize: 5, toolTipContent: "<?php echo 'KS-'.$niz_datumi[4];?><br>Izdani Predračuni :{y} <?php  echo rast($niz_kolicina_izdanih[5],$niz_kolicina_izdanih[4]);?>"},
								{ x: new Date(<?php echo $date3; ?>),  y: <?php echo $niz_kolicina_izdanih[3]; ?>, color: "#333", markerSize: 5, toolTipContent: "<?php echo 'KS-'.$niz_datumi[3];?><br>Izdani Predračuni :{y} <?php  echo rast($niz_kolicina_izdanih[4],$niz_kolicina_izdanih[3]);?>"},
								{ x: new Date(<?php echo $date2; ?>),  y: <?php echo $niz_kolicina_izdanih[2]; ?>, color: "#333", markerSize: 5, toolTipContent: "<?php echo 'KS-'.$niz_datumi[2];?><br>Izdani Predračuni :{y} <?php  echo rast($niz_kolicina_izdanih[3],$niz_kolicina_izdanih[2]);?>"},
								{ x: new Date(<?php echo $date1; ?>),  y: <?php echo $niz_kolicina_izdanih[1]; ?>, color: "#333", markerSize: 5, toolTipContent: "<?php echo 'KS-'.$niz_datumi[1];?><br>Izdani Predračuni :{y} <?php  echo rast($niz_kolicina_izdanih[2],$niz_kolicina_izdanih[1]);?>"},
								{ x: new Date(<?php echo $date0; ?>), y: <?php echo $niz_kolicina_izdanih[0]; ?>, color: "#333",  markerSize: 5, toolTipContent: "<?php echo 'KS-'.$niz_datumi[0];?><br>Izdani Predračuni :{y} <?php  echo rast($niz_kolicina_izdanih[1],$niz_kolicina_izdanih[0]);?>"}
							]
						},
						{
							type: "splineArea",
							name: "Uplaćeni Predračuni",
							showInLegend: true,
							xValueFormatString: "MMM DD YYYY",
							yValueFormatString: "#",
							color: "rgba(0, 255, 0,.7)",
							dataPoints: [
								{ x: new Date(<?php echo $date11; ?>), y: <?php echo $niz_kolicina_naplacenih[11]; ?>, color:"#333", markerSize: 5, toolTipContent: "Uplaćeni Predračuni: {y}"},
								{ x: new Date(<?php echo $date10; ?>), y: <?php echo $niz_kolicina_naplacenih[10]; ?>, color:"#333", markerSize: 5, toolTipContent: "Uplaćeni Predračuni: {y}  <?php  echo rast($niz_kolicina_naplacenih[11],$niz_kolicina_naplacenih[10]);?>"},
								{ x: new Date(<?php echo $date9; ?>), y: <?php echo $niz_kolicina_naplacenih[9]; ?>, color:"#333", markerSize: 5, toolTipContent: "Uplaćeni Predračuni: {y}  <?php  echo rast($niz_kolicina_naplacenih[10],$niz_kolicina_naplacenih[9]);?>"},
								{ x: new Date(<?php echo $date8; ?>), y: <?php echo $niz_kolicina_naplacenih[8]; ?>, color:"#333", markerSize: 5, toolTipContent: "Uplaćeni Predračuni: {y}  <?php  echo rast($niz_kolicina_naplacenih[9],$niz_kolicina_naplacenih[8]);?>"},
								{ x: new Date(<?php echo $date7; ?>), y: <?php echo $niz_kolicina_naplacenih[7]; ?>, color:"#333", markerSize: 5, toolTipContent: "Uplaćeni Predračuni: {y}  <?php  echo rast($niz_kolicina_naplacenih[8],$niz_kolicina_naplacenih[7]);?>"},
								{ x: new Date(<?php echo $date6; ?>), y: <?php echo $niz_kolicina_naplacenih[6]; ?> , color:"#333", markerSize: 5, toolTipContent: "Uplaćeni Predračuni: {y}  <?php  echo rast($niz_kolicina_naplacenih[7],$niz_kolicina_naplacenih[6]);?>"},
								{ x: new Date(<?php echo $date5; ?>), y: <?php echo $niz_kolicina_naplacenih[5]; ?>, color:"#333", markerSize: 5, toolTipContent: "Uplaćeni Predračuni: {y}  <?php  echo rast($niz_kolicina_naplacenih[6],$niz_kolicina_naplacenih[5]);?>"},
								{ x: new Date(<?php echo $date4; ?>), y: <?php echo $niz_kolicina_naplacenih[4]; ?>, color:"#333", markerSize: 5, toolTipContent: "Uplaćeni Predračuni: {y}  <?php  echo rast($niz_kolicina_naplacenih[5],$niz_kolicina_naplacenih[4]);?>"},
								{ x: new Date(<?php echo $date3; ?>), y: <?php echo $niz_kolicina_naplacenih[3]; ?>, color:"#333", markerSize: 5, toolTipContent: "Uplaćeni Predračuni: {y}  <?php  echo rast($niz_kolicina_naplacenih[4],$niz_kolicina_naplacenih[3]);?>"},
								{ x: new Date(<?php echo $date2; ?>),  y: <?php echo $niz_kolicina_naplacenih[2]; ?>, color:"#333", markerSize: 5, toolTipContent: "Uplaćeni Predračuni: {y}  <?php  echo rast($niz_kolicina_naplacenih[3],$niz_kolicina_naplacenih[2]);?>"},
								{ x: new Date(<?php echo $date1; ?>), y: <?php echo $niz_kolicina_naplacenih[1]; ?>, color:"#333", markerSize: 5, toolTipContent: "Uplaćeni Predračuni: {y}  <?php  echo rast($niz_kolicina_naplacenih[2],$niz_kolicina_naplacenih[1]);?>"},
								{ x: new Date(<?php echo $date0; ?>), y: <?php echo $niz_kolicina_naplacenih[0]; ?>, color:"#333", markerSize: 5, toolTipContent: "Uplaćeni Predračuni: {y}  <?php  echo rast($niz_kolicina_naplacenih[1],$niz_kolicina_naplacenih[0]);?>"}
							]
						},
						{
							type: "spline",
							name: "Postotak Uplaćenih Predračuna",
							showInLegend: true,
							xValueFormatString: "MMM DD YYYY",
							axisYType: "secondary",
							yValueFormatString: "#",
							color: "rgba(255, 0, 0, 0.9)",
							dataPoints: [
								{ x: new Date(<?php echo $date11; ?>), y: <?php echo getProcenat($niz_kolicina_izdanih[11],$niz_uplaceni_dat_kr[11]); ?>, color:"#333", markerSize: 1, toolTipContent: "Procenat uplate: <?php echo getProcenat($niz_kolicina_izdanih[11],$niz_uplaceni_dat_kr[11]).'%'; ?>"},
								{ x: new Date(<?php echo $date10; ?>), y: <?php echo getProcenat($niz_kolicina_izdanih[10],$niz_uplaceni_dat_kr[10]); ?>, color:"#333", markerSize: 1, toolTipContent: "Procenat uplate: <?php echo getProcenat($niz_kolicina_izdanih[10],$niz_uplaceni_dat_kr[10]).'%'; ?>"},
								{ x: new Date(<?php echo $date9; ?>), y: <?php echo getProcenat($niz_kolicina_izdanih[9],$niz_uplaceni_dat_kr[9]); ?>, color:"#333", markerSize: 1, toolTipContent:"Procenat uplate: <?php echo getProcenat($niz_kolicina_izdanih[9],$niz_uplaceni_dat_kr[9]).'%'; ?>"},
								{ x: new Date(<?php echo $date8; ?>), y: <?php echo getProcenat($niz_kolicina_izdanih[8],$niz_uplaceni_dat_kr[8]); ?>, color:"#333", markerSize: 1, toolTipContent:"Procenat uplate: <?php echo getProcenat($niz_kolicina_izdanih[8],$niz_uplaceni_dat_kr[8]).'%'; ?>"},
								{ x: new Date(<?php echo $date7; ?>), y: <?php echo getProcenat($niz_kolicina_izdanih[7],$niz_uplaceni_dat_kr[7]); ?>, color:"#333", markerSize: 1, toolTipContent:"Procenat uplate: <?php echo getProcenat($niz_kolicina_izdanih[7],$niz_uplaceni_dat_kr[7]).'%'; ?>"},
								{ x: new Date(<?php echo $date6; ?>), y: <?php echo getProcenat($niz_kolicina_izdanih[6],$niz_uplaceni_dat_kr[6]); ?>, color:"#333", markerSize: 1, toolTipContent:"Procenat uplate: <?php echo getProcenat($niz_kolicina_izdanih[6],$niz_uplaceni_dat_kr[6]).'%'; ?>"},
								{ x: new Date(<?php echo $date5; ?>), y: <?php echo getProcenat($niz_kolicina_izdanih[5],$niz_uplaceni_dat_kr[5]); ?>, color:"#333", markerSize: 1, toolTipContent:"Procenat uplate: <?php echo getProcenat($niz_kolicina_izdanih[5],$niz_uplaceni_dat_kr[5]).'%'; ?>"},
								{ x: new Date(<?php echo $date4; ?>), y: <?php echo getProcenat($niz_kolicina_izdanih[4],$niz_uplaceni_dat_kr[4]); ?>, color:"#333", markerSize: 1, toolTipContent:"Procenat uplate: <?php echo getProcenat($niz_kolicina_izdanih[4],$niz_uplaceni_dat_kr[4]).'%'; ?>"},
								{ x: new Date(<?php echo $date3; ?>), y: <?php echo getProcenat($niz_kolicina_izdanih[3],$niz_uplaceni_dat_kr[3]); ?>, color:"#333", markerSize: 1, toolTipContent:"Procenat uplate: <?php echo getProcenat($niz_kolicina_izdanih[3],$niz_uplaceni_dat_kr[3]).'%'; ?>"},
								{ x: new Date(<?php echo $date2; ?>), y: <?php echo getProcenat($niz_kolicina_izdanih[2],$niz_uplaceni_dat_kr[2]); ?>, color:"#333", markerSize: 1, toolTipContent:"Procenat uplate: <?php echo getProcenat($niz_kolicina_izdanih[2],$niz_uplaceni_dat_kr[2]).'%'; ?>"},
								{ x: new Date(<?php echo $date1; ?>), y: <?php echo getProcenat($niz_kolicina_izdanih[1],$niz_uplaceni_dat_kr[1]); ?>, color:"#333", markerSize: 1, toolTipContent:"Procenat uplate: <?php echo getProcenat($niz_kolicina_izdanih[1],$niz_uplaceni_dat_kr[1]).'%'; ?>"},
								{ x: new Date(<?php echo $date0; ?>), y: <?php echo getProcenat($niz_kolicina_izdanih[0],$niz_uplaceni_dat_kr[0]); ?>, color:"#333", markerSize: 1, toolTipContent:"Procenat uplate: <?php echo getProcenat($niz_kolicina_izdanih[0],$niz_uplaceni_dat_kr[0]).'%'; ?>"}
							]
						}
						]
					};
					$("#chartContainer").CanvasJSChart(options);

					function toggleDataSeries(e) {
						if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
							e.dataSeries.visible = false;
						} else {
							e.dataSeries.visible = true;
						}
						e.chart.render();
					}
				</script>
				<<script src="<?php getSiteURL(); ?>js/bootstrap.min.js"></script>						
				<script src="<?php getSiteURL(); ?>js/jquery.slimscroll.min.js"></script>
				<script src="<?php getSiteURL(); ?>js/jquery.matchHeight-min.js"></script>
				<script src="<?php getSiteURL(); ?>js/bootstrap-select.min.js"></script>
				<script src="<?php getSiteURL(); ?>js/jquery.dataTables.min.js"></script>
				<script src="<?php getSiteURL(); ?>js/dataTables.responsive.min.js"></script>
				<script src="<?php getSiteURL(); ?>js/jquery.fancybox.js"></script>
				<script src="<?php getSiteURL(); ?>js/trumbowyg.min.js"></script>
				<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
				<script src="<?php getSiteURL(); ?>js/jquery.dataTables.min.js"></script>
				<script type="text/javascript" src="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.11/js/dataTables.checkboxes.min.js"></script>
				
			</div>
		</div>
	</div>
</div>