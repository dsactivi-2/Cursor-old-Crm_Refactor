<?php

/**************************************
*
** CREATED 30.08.2021 -- 
** SYSTEM FOR MANUAL STATUS SWITCHING - FOR AGENTS
*
*
** Last update - Adis Toromanovic - Date: 29.07.2022. DESC: Stari nacin praćenja statistike gdje je broj predracuna 1 broj (bez razdvajanja prihvacenih i neprihvacenih)
*
*/

include('query-parametars.php');

?>

<style>
		table.dataTable tbody td {
			padding: 2px !important;
		} 
		.panel {
			margin-bottom: 0px !important;
		}
	</style>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12 col-xs-12">
			<div class="idk_box idk_box_shadow">
				<?php 
					function invertSign($value){
						return -$value;
					}
					//Na osnovu tabele idk_nd_menadzeri_statistike uzimam sve zaposlenike koji su radili na diplu
					//Zaposlenici su svrstani u nizove Aktivnih i Deaktivirani
					//$time_start = microtime(true);
					$zapDiplA = array();		//Aktivni zaposlenici
					$zapDiplD = array();		//Deaktivirani zaposlenici
					$zapDiplO = array();		//Ostali zaposlenici
					$query1 = $db->prepare("
						SELECT stat.zaduzen_zaposlenik_id, emp.employee_status, emp.employee_supervizor
						FROM idk_nd_menadzeri_statistike stat
						INNER JOIN idk_employees emp
						ON stat.zaduzen_zaposlenik_id = emp.employee_id
						WHERE stat.vrsta_aktivnosti = 0 AND stat.prethodni_zaposlenik_id is null
						GROUP BY stat.zaduzen_zaposlenik_id
						ORDER BY stat.zaduzen_zaposlenik_id ASC
					");
					$query1->execute();
					while($row1 = $query1->fetch()){
						if(in_array("0",explode(",",$row1["employee_status"]))){
							array_push($zapDiplD, intval($row1["zaduzen_zaposlenik_id"]));
						}else if((in_array("2",explode(",",$row1["employee_status"]))) OR (in_array("3",explode(",",$row1["employee_status"]))) OR (in_array("15",explode(",",$row1["employee_status"]))) OR (in_array("2",explode(",",$row1["employee_supervizor"]))) OR (in_array("3",explode(",",$row1["employee_supervizor"]))) OR (in_array("15",explode(",",$row1["employee_supervizor"])))){
							array_push($zapDiplA, intval($row1["zaduzen_zaposlenik_id"]));
						}else{
							array_push($zapDiplO, intval($row1["zaduzen_zaposlenik_id"]));
						}
					}
					// echo '<span style = "word-break: break-all;">Broj aktivnih zaposlenika: '.count($zapDiplA).'<br>Aktivni: '.implode(",", $zapDiplA).'</span>';
					// echo '<span style = "word-break: break-all;"><br><br>Broj aktivnih zaposlenika: '.count($zapDiplD).'<br>Deaktivirani: '.implode(",", $zapDiplD).'</span>';
					// $time_end = microtime(true);
					// $execution_time = ($time_end - $time_start)/60;
					// echo "<br/><br/>Vrijeme: ".$execution_time;
					
					//Na osnovu tabele idk_poslovnice povlacim sve poslovnice
				?>
				
				
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title span_style_1 text-center">
							Statistika kandidata po poslovnicama
						</h3>
					</div>
					<div class="panel-body">
						<div class="row" style = "margin-bottom: 20px;">
							<div class="col-md-12 col-xs-12 text-right">
								<?php 
									if(isset($_POST['selected_rows'])){
										$uslov_query = $_POST['selected_rows'];
										if($uslov_query == ""){
											$uslov_query = "0";
											$dugme_ispis = '<i class="fa fa-refresh" aria-hidden="true"></i> <span>Refresh</span>';
										}else{
											$dugme_ispis = '<i class="fa fa-reply-all" aria-hidden="true"></i> <span>Vrati sve</span>';
										}
									}else{
										$dugme_ispis = '<i class="fa fa-refresh" aria-hidden="true"></i> <span>Refresh</span>';
										$uslov_query = "0";
									}
								?>
								<form action="<?php getSiteURL(); ?>index.php" name="refresh_form" id="refresh_form" method="POST" enctype="multipart/form-data" class="form-horizontal">
									<input type="hidden" name = "selected_rows" id="selected_rows"></input>
									<button type="submit" value="Submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive refDataTable pull-right"><?php echo $dugme_ispis; ?></button>
								</form>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12 col-xs-12" style = "overflow-y: auto !important;">
								<script type="text/javascript">
									$(document).ready(function() {
										var table = $('#tableStat').DataTable({
											
											responsive: false,
											"order": [[ 3, "desc" ]],
											'columnDefs': [
												{
													'targets': 0,
													'checkboxes': {
														'selectRow': true
													}
												}
											],
											'select': {
												'style': 'multi'
											},
											"bPaginate": false,
											"bLengthChange": false,
											"bInfo": false,
											"bAutoWidth": false,
											"aoColumns": [
												{ "width": "5%", "bSortable": false },
												{ "width": "10%", "bSortable": true },
												{ "width": "5%", "bSortable": true },
												{ "width": "7.5%", "bSortable": true },
												{ "width": "7.5%", "bSortable": false },
												{ "width": "7.5%", "bSortable": false },
												{ "width": "5%", "bSortable": true },
												{ "width": "5%", "bSortable": true },
												{ "width": "5%", "bSortable": true },
												{ "width": "5%", "bSortable": true },
												{ "width": "5%", "bSortable": true },
												{ "width": "5%", "bSortable": true },
												{ "width": "27.5%", "bSortable": true }
											]
										});
										
										$('.refDataTable').on('click', function(){
											var rows_selected = table.column(0).checkboxes.selected();
											//Output form data to a console     
											$('#selected_rows').val(rows_selected.join(","));
											var selectedIds = $('#selected_rows').val();
											//alert(selectedIds);
										}); 
									});
								</script>
								<table id="tableStat" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th class="text-center">#</th>
											<th class="text-center">Naziv</th>
											<th class="text-center">Adresa</th>
											<th class="text-center" title = "Procenat krajnje naplate = Naplaćeno / Prozvano">PKN</th>
											<th class="text-center" title = "Procenat naplate = Naplaćeno / Izdano">PN</th>
											<th class="text-center" title = "Conversion rate = Izdano / Prozvano">CR</th>
											<th class="text-center" title = "<?php echo $title0;?>">DANAS</th>
											<th class="text-center" title = "<?php echo $title1;?>">KS <?php echo $week1; ?></th>
											<th class="text-center" title = "<?php echo $title2;?>">KS <?php echo $week2; ?></th>
											<th class="text-center" title = "<?php echo $title3;?>">KS <?php echo $week3; ?></th>
											<th class="text-center" title = "<?php echo $title4;?>">KS <?php echo $week4; ?></th>
											<th class="text-center" title = "<?php echo "Prošli mjesec (od ".date('01.m.Y H:i:s', strtotime("-1 months", strtotime(date("Y-m-d 00:00:00"))))." do ".date('t.m.Y H:i:s', strtotime("-1 months", strtotime(date("Y-m-d 23:59:59")))).")";?>">PM</th>
											<th class="text-center" title = "<?php echo $title_ukupno;?>">Ukupno</th> 
										</tr>
									</thead>
									<tbody>
									<?php 
										$sum_broj_prozvanih = 0;
										$sum_broj_izdanih = 0;
										$sum_broj_uplacenih = 0;
										
										$sum_pkn = 0;
										$sum_pn = 0;
										$sum_cr = 0;
										
										$sum_broj_izdanih_CD = 0;
										$sum_broj_izdanih_KS1 = 0;
										$sum_broj_izdanih_KS2 = 0;
										$sum_broj_izdanih_KS3 = 0;
										$sum_broj_izdanih_KS4 = 0;
										$sum_broj_izdanih_PM = 0;
										$sum_broj_izdanih_KS = 0;
										
										$sum_broj_uplacenih_CD = 0;
										$sum_broj_uplacenih_KS1 = 0;
										$sum_broj_uplacenih_KS2 = 0;
										$sum_broj_uplacenih_KS3 = 0;
										$sum_broj_uplacenih_KS4 = 0;
										$sum_broj_uplacenih_PM = 0;
										$sum_broj_uplacenih_KS = 0;
										
										$sum_uplacenih_CD = 0;
										$sum_uplacenih_KS1 = 0;
										$sum_uplacenih_KS2 = 0;
										$sum_uplacenih_KS3 = 0;
										$sum_uplacenih_KS4 = 0;
										$sum_uplacenih_PM = 0;
										$sum_uplacenih_KS = 0;
										//$uslov_poslovnica = "pos.branch_id = 4";
										$query3 = $db->prepare("
											SELECT *
											FROM idk_poslovnice pos
											WHERE pos.branch_id NOT IN (".$uslov_query.") AND ".$uslov_poslovnica."
											ORDER BY pos.branch_city ASC
										");
										$query3->execute();
										while($row3 = $query3->fetch()){
											$zapBranch = array();
											$query2 = $db->prepare("
												SELECT emp.employee_id
												FROM idk_employees emp
												WHERE emp.employee_poslovnica = ".intval($row3["branch_id"])." AND (emp.employee_id IN (".implode(",", $zapDiplA)."))
											");
											$query2->execute();
											while($row2 = $query2->fetch()){
												array_push($zapBranch, $row2["employee_id"]);
											}
											if(count($zapBranch) != 0){
											$broj_prozvanih = 0;
											$broj_izdanih = 0;
											$broj_uplacenih = 0;
											
											$pkn = 0;
											$pn = 0;
											$cr = 0;
											
											$broj_izdanih_CD = 0;
											$broj_izdanih_KS1 = 0;
											$broj_izdanih_KS2 = 0;
											$broj_izdanih_KS3 = 0;
											$broj_izdanih_KS4 = 0;
											$broj_izdanih_PM = 0;
											$ukupno_broj_izdanih_KS = 0;
											
											$broj_uplacenih_CD = 0;
											$broj_uplacenih_KS1 = 0;
											$broj_uplacenih_KS2 = 0;
											$broj_uplacenih_KS3 = 0;
											$broj_uplacenih_KS4 = 0;
											$broj_uplacenih_PM = 0;
											$ukupno_broj_uplacenih_PM = 0;
											
											$uplacenih_CD = 0;
											$uplacenih_KS1 = 0;
											$uplacenih_KS2 = 0;
											$uplacenih_KS3 = 0;
											$uplacenih_KS4 = 0;
											$uplacenih_PM = 0;
											$ukupno_uplacenih_PM = 0;
											
											//query za broj prozvanih kandidata 
											$query4 = $db->prepare("
												SELECT COUNT(DISTINCT log.idd_broj_nd_kandidata) as broj_prozvanih 
												FROM idk_nd_kandidata kan
												JOIN idk_nd_kandidata_status_log log 
												ON kan.id_broj_nd_kandidata = log.idd_broj_nd_kandidata
												WHERE(kan.status_nd_kandidata IN (2,3,4,5,6,7) OR (kan.status_nd_kandidata = 1 AND kan.pstatus_nd_kandidata IN (2,6,3,4,5,7,8,9,10,11,12)))
												AND ((log.status_nd_kandidata IN (1) AND log.pstatus_nd_kandidata IN (3,4,5,9,10,12)) OR log.status_nd_kandidata IN (2,3,4,5,6))
												AND (log.promjenio_zaposlenik_nd_kandidata IN (".implode(",",$zapBranch)."))
												AND log.vrijeme_promjene_statusa_nd_kandidata >= '2021-07-01 00:00:00'
											");
											$query4->execute();
											$row4 = $query4->fetch();
											$broj_prozvanih = intval($row4["broj_prozvanih"]); //Broj prozvanih kandidata
											$sum_broj_prozvanih = $sum_broj_prozvanih + $broj_prozvanih; //Suma broja prozvanih kandidata
											//query za broj izdanih predracuna
											$query5 = $db->prepare("
												SELECT COUNT(DISTINCT pred.pr_kandidat_id) as broj_izdanih 
												FROM idk_nd_kandidata kan
												JOIN idk_predracuni pred ON kan.id_broj_nd_kandidata = pred.pr_kandidat_id
												WHERE (kan.status_nd_kandidata IN(2,3,4,5,6,7) OR (kan.status_nd_kandidata = 1 AND kan.pstatus_nd_kandidata IN (2,6,3,4,5,7,8,9,10,11,12)))
												AND pred.pr_zaposlenik IN(".implode(",",$zapBranch).")
												AND pred.pr_rata = 1
												AND pred.pr_status is not null
												AND pred.pr_datum_kreiranja >= '2021-07-01 00:00:00'
											");
											$query5->execute();
											$row5 = $query5->fetch();
											$broj_izdanih = intval($row5["broj_izdanih"]); //Broj izdanih predracuna
											$sum_broj_izdanih = $sum_broj_izdanih + $broj_izdanih; //Suma broja izdanih predracuna
											//query za broj uplacenih predracuna
											$query6 = $db->prepare("
												SELECT COUNT(DISTINCT pred.pr_kandidat_id) as broj_uplacenih 
												FROM idk_nd_kandidata kan
												JOIN idk_predracuni pred ON kan.id_broj_nd_kandidata = pred.pr_kandidat_id
												WHERE (kan.status_nd_kandidata IN(2,3,4,5,6,7) OR (kan.status_nd_kandidata = 1 AND kan.pstatus_nd_kandidata IN (2,6,3,4,5,7,8,9,10,11,12)))
												AND pred.pr_zaposlenik IN(".implode(",",$zapBranch).")
												AND pred.pr_uplaceno = 1
												AND pred.pr_rata = 1
												AND pred.pr_datum_kreiranja >= '2021-07-01 00:00:00'
											");
											$query6->execute();
											$row6 = $query6->fetch();
											$broj_uplacenih = intval($row6["broj_uplacenih"]); //Broj uplacenih predracuna
											$sum_broj_uplacenih = $sum_broj_uplacenih + $broj_uplacenih; //Suma broja izdanih predracuna
											
											$pkn = ($broj_prozvanih == 0) ? number_format(0, 2, ',', '') : number_format(((($broj_uplacenih) / $broj_prozvanih)*100), 2, ',', ''); // procenat krajnje naplate
											$pn = ($broj_izdanih == 0) ? number_format(0, 2, ',', '') : number_format(((($broj_uplacenih) / $broj_izdanih)*100), 2, ',', ''); // procenat naplate
											$cr = ($broj_prozvanih == 0) ? number_format(0, 2, ',', '') : number_format(((($broj_izdanih) / $broj_prozvanih)*100), 2, ',', ''); // procenat conversion rate-a
											
											$query7 = $db->prepare("
												SELECT
													SUM(CASE WHEN pred.pr_datum_kreiranja BETWEEN '".$pocetak0."' AND '".$kraj0."' THEN 1 ELSE 0 END) AS broj_izdanih_CD,
													SUM(CASE WHEN pred.pr_datum_kreiranja BETWEEN '".$pocetak1."' AND '".$kraj1."' THEN 1 ELSE 0 END) AS broj_izdanih_KS1,
													SUM(CASE WHEN pred.pr_datum_kreiranja BETWEEN '".$pocetak2."' AND '".$kraj2."' THEN 1 ELSE 0 END) AS broj_izdanih_KS2,
													SUM(CASE WHEN pred.pr_datum_kreiranja BETWEEN '".$pocetak3."' AND '".$kraj3."' THEN 1 ELSE 0 END) AS broj_izdanih_KS3,
													SUM(CASE WHEN pred.pr_datum_kreiranja BETWEEN '".$pocetak4."' AND '".$kraj4."' THEN 1 ELSE 0 END) AS broj_izdanih_KS4,
													SUM(CASE WHEN pred.pr_datum_kreiranja BETWEEN '".date('Y-m-01 H:i:s', strtotime("-1 months", strtotime(date("Y-m-d 00:00:00"))))."' AND '".date('Y-m-t H:i:s', strtotime("-1 months", strtotime(date("Y-m-d 23:59:59"))))."' THEN 1 ELSE 0 END) AS broj_izdanih_PM,
													SUM(CASE WHEN pred.pr_uplaceno = 1 AND pred.pr_datum_kreiranja BETWEEN '".$pocetak0."' AND '".$kraj0."' AND pred.pr_datum_uplate = CURRENT_DATE THEN 1 ELSE 0 END) AS broj_uplacenih_CD,
													SUM(CASE WHEN pred.pr_uplaceno = 1 AND pred.pr_datum_kreiranja BETWEEN '".$pocetak1."' AND '".$kraj1."' AND pred.pr_datum_uplate BETWEEN '".$result1['week_start']."' AND '".$result1['week_end']."' THEN 1 ELSE 0 END) AS broj_uplacenih_KS1,
													SUM(CASE WHEN pred.pr_uplaceno = 1 AND pred.pr_datum_kreiranja BETWEEN '".$pocetak2."' AND '".$kraj2."' AND pred.pr_datum_uplate BETWEEN '".$result2['week_start']."' AND '".$result2['week_end']."' THEN 1 ELSE 0 END) AS broj_uplacenih_KS2,
													SUM(CASE WHEN pred.pr_uplaceno = 1 AND pred.pr_datum_kreiranja BETWEEN '".$pocetak3."' AND '".$kraj3."' AND pred.pr_datum_uplate BETWEEN '".$result3['week_start']."' AND '".$result3['week_end']."' THEN 1 ELSE 0 END) AS broj_uplacenih_KS3,
													SUM(CASE WHEN pred.pr_uplaceno = 1 AND pred.pr_datum_kreiranja BETWEEN '".$pocetak4."' AND '".$kraj4."' AND pred.pr_datum_uplate BETWEEN '".$result4['week_start']."' AND '".$result4['week_end']."' THEN 1 ELSE 0 END) AS broj_uplacenih_KS4,
													SUM(CASE WHEN pred.pr_uplaceno = 1 AND pred.pr_datum_kreiranja BETWEEN '".date('Y-m-01 H:i:s', strtotime("-1 months", strtotime(date("Y-m-d 00:00:00"))))."' AND '".date('Y-m-t H:i:s', strtotime("-1 months", strtotime(date("Y-m-d 23:59:59"))))."' AND pred.pr_datum_uplate BETWEEN '".date('Y-m-01', strtotime("-1 months", strtotime(date("Y-m-d 00:00:00"))))."' AND '".date('Y-m-t', strtotime("-1 months", strtotime(date("Y-m-d 23:59:59"))))."' THEN 1 ELSE 0 END) AS broj_uplacenih_PM,
													SUM(CASE WHEN pred.pr_uplaceno = 1 AND pred.pr_datum_uplate = CURRENT_DATE THEN 1 ELSE 0 END) AS uplacenih_CD,
													SUM(CASE WHEN pred.pr_uplaceno = 1 AND pred.pr_datum_uplate BETWEEN '".$result1['week_start']."' AND '".$result1['week_end']."' THEN 1 ELSE 0 END) AS uplacenih_KS1,
													SUM(CASE WHEN pred.pr_uplaceno = 1 AND pred.pr_datum_uplate BETWEEN '".$result2['week_start']."' AND '".$result2['week_end']."' THEN 1 ELSE 0 END) AS uplacenih_KS2,
													SUM(CASE WHEN pred.pr_uplaceno = 1 AND pred.pr_datum_uplate BETWEEN '".$result3['week_start']."' AND '".$result3['week_end']."' THEN 1 ELSE 0 END) AS uplacenih_KS3,
													SUM(CASE WHEN pred.pr_uplaceno = 1 AND pred.pr_datum_uplate BETWEEN '".$result4['week_start']."' AND '".$result4['week_end']."' THEN 1 ELSE 0 END) AS uplacenih_KS4,
													SUM(CASE WHEN pred.pr_uplaceno = 1 AND pred.pr_datum_uplate BETWEEN '".date('Y-m-01', strtotime("-1 months", strtotime(date("Y-m-d 00:00:00"))))."' AND '".date('Y-m-t', strtotime("-1 months", strtotime(date("Y-m-d 23:59:59"))))."' THEN 1 ELSE 0 END) AS uplacenih_PM
												FROM idk_predracuni pred
												JOIN idk_nd_kandidata kan 
												ON pred.pr_kandidat_id = kan.id_broj_nd_kandidata 
												WHERE pred.pr_zaposlenik IN(".implode(",",$zapBranch).") 
												AND pred.pr_rata = 1
												AND pred.pr_status is not null
												AND pred.pr_datum_kreiranja >= '2021-07-01 00:00:00'
												AND pred.pr_id IN(SELECT MAX(pr.pr_id) FROM idk_predracuni pr WHERE pr.pr_datum_kreiranja >= '2021-07-01 00:00:00' AND pr.pr_zaposlenik IN(".implode(",",$zapBranch).") AND pr.pr_rata = 1 GROUP BY pr.pr_kandidat_id)
											");
											$query7->execute();
											$row7 = $query7->fetch();
											
											$broj_izdanih_CD = is_null($row7['broj_izdanih_CD']) ? 0 : intval($row7['broj_izdanih_CD']);
											$broj_izdanih_KS1 = is_null($row7['broj_izdanih_KS1']) ? 0 : intval($row7['broj_izdanih_KS1']);
											$broj_izdanih_KS2 = is_null($row7['broj_izdanih_KS2']) ? 0 : intval($row7['broj_izdanih_KS2']);
											$broj_izdanih_KS3 = is_null($row7['broj_izdanih_KS3']) ? 0 : intval($row7['broj_izdanih_KS3']);
											$broj_izdanih_KS4 = is_null($row7['broj_izdanih_KS4']) ? 0 : intval($row7['broj_izdanih_KS4']);
											$broj_izdanih_PM = is_null($row7['broj_izdanih_PM']) ? 0 : intval($row7['broj_izdanih_PM']);
											$ukupno_broj_izdanih_KS = $broj_izdanih_KS1 + $broj_izdanih_KS2 + $broj_izdanih_KS3 + $broj_izdanih_KS4;
											
											$sum_broj_izdanih_CD = $sum_broj_izdanih_CD + $broj_izdanih_CD;
											$sum_broj_izdanih_KS1 = $sum_broj_izdanih_KS1 + $broj_izdanih_KS1;
											$sum_broj_izdanih_KS2 = $sum_broj_izdanih_KS2 + $broj_izdanih_KS2;
											$sum_broj_izdanih_KS3 = $sum_broj_izdanih_KS3 + $broj_izdanih_KS3;
											$sum_broj_izdanih_KS4 = $sum_broj_izdanih_KS4 + $broj_izdanih_KS4;
											$sum_broj_izdanih_PM = $sum_broj_izdanih_PM + $broj_izdanih_PM;
											$sum_broj_izdanih_KS = $sum_broj_izdanih_KS + $ukupno_broj_izdanih_KS;
											
											$broj_uplacenih_CD = is_null($row7['broj_uplacenih_CD']) ? 0 : intval($row7['broj_uplacenih_CD']);
											$broj_uplacenih_KS1 = is_null($row7['broj_uplacenih_KS1']) ? 0 : intval($row7['broj_uplacenih_KS1']);
											$broj_uplacenih_KS2 = is_null($row7['broj_uplacenih_KS2']) ? 0 : intval($row7['broj_uplacenih_KS2']);
											$broj_uplacenih_KS3 = is_null($row7['broj_uplacenih_KS3']) ? 0 : intval($row7['broj_uplacenih_KS3']);
											$broj_uplacenih_KS4 = is_null($row7['broj_uplacenih_KS4']) ? 0 : intval($row7['broj_uplacenih_KS4']);
											$broj_uplacenih_PM = is_null($row7['broj_uplacenih_PM']) ? 0 : intval($row7['broj_uplacenih_PM']);
											$ukupno_broj_uplacenih_PM = $broj_uplacenih_KS1 + $broj_uplacenih_KS2 + $broj_uplacenih_KS3 + $broj_uplacenih_KS4;
											
											$sum_broj_uplacenih_CD = $sum_broj_uplacenih_CD + $broj_uplacenih_CD;
											$sum_broj_uplacenih_KS1 = $sum_broj_uplacenih_KS1 + $broj_uplacenih_KS1;
											$sum_broj_uplacenih_KS2 = $sum_broj_uplacenih_KS2 + $broj_uplacenih_KS2;
											$sum_broj_uplacenih_KS3 = $sum_broj_uplacenih_KS3 + $broj_uplacenih_KS3;
											$sum_broj_uplacenih_KS4 = $sum_broj_uplacenih_KS4 + $broj_uplacenih_KS4;
											$sum_broj_uplacenih_PM = $sum_broj_uplacenih_PM + $broj_uplacenih_PM;
											$sum_broj_uplacenih_KS = $sum_broj_uplacenih_KS + $ukupno_broj_uplacenih_PM;
											
											$uplacenih_CD = is_null($row7['uplacenih_CD']) ? 0 : intval($row7['uplacenih_CD']);
											$uplacenih_KS1 = is_null($row7['uplacenih_KS1']) ? 0 : intval($row7['uplacenih_KS1']);
											$uplacenih_KS2 = is_null($row7['uplacenih_KS2']) ? 0 : intval($row7['uplacenih_KS2']);
											$uplacenih_KS3 = is_null($row7['uplacenih_KS3']) ? 0 : intval($row7['uplacenih_KS3']);
											$uplacenih_KS4 = is_null($row7['uplacenih_KS4']) ? 0 : intval($row7['uplacenih_KS4']);
											$uplacenih_PM = is_null($row7['uplacenih_PM']) ? 0 : intval($row7['uplacenih_PM']);
											$ukupno_uplacenih_PM = $uplacenih_KS1 + $uplacenih_KS2 + $uplacenih_KS3 + $uplacenih_KS4;
											
											$sum_uplacenih_CD = $sum_uplacenih_CD + $uplacenih_CD;
											$sum_uplacenih_KS1 = $sum_uplacenih_KS1 + $uplacenih_KS1;
											$sum_uplacenih_KS2 = $sum_uplacenih_KS2 + $uplacenih_KS2;
											$sum_uplacenih_KS3 = $sum_uplacenih_KS3 + $uplacenih_KS3;
											$sum_uplacenih_KS4 = $sum_uplacenih_KS4 + $uplacenih_KS4;
											$sum_uplacenih_PM = $sum_uplacenih_PM + $uplacenih_PM;
											$sum_uplacenih_KS = $sum_uplacenih_KS + $ukupno_uplacenih_PM;
											
											//if($row_on_off == 'on'){
									?>
										<tr>
											<td class="text-center"><?php echo intval($row3["branch_id"]);?></td>
											<td class="text-center"><a class="a_link" target="_blank" href = "modul_statistike?page=dash_stat&branch_id=<?php echo $row3["branch_id"];?>"><?php echo $row3["branch_name"];?></a></td>
											<td class="text-center"><?php echo " ".$row3["branch_city"].", ".$row3["branch_state"]." "; ?></td>
											<td class="text-center"><span style ="font-weight: bold;"><?php echo $pkn." %"; ?></span></td>
											<td class="text-center"><?php echo $broj_uplacenih."/".$broj_izdanih; ?><br><span style ="font-weight: bold;"><?php echo $pn." %"; ?></span></td>
											<td class="text-center"><?php echo $broj_izdanih."/".$broj_prozvanih; ?><br><span style ="font-weight: bold;"><?php echo $cr." %"; ?></span></td>
										<?php
											if (in_array(("9"), $employee_status) || in_array(("1"), $employee_status)){
										?>
											<td class="text-center" style = "border-left: 1px solid #111111;" ><a class="a_link" target="_blank" <?php if($broj_izdanih_CD != 0) echo 'href = "statistike.php?page=predracuni&branch_id='.intval($row3["branch_id"]).'&sed=0&id_men='. invertSign(1) .'&year='.$year1.'"'; ?>><?php echo getStyleColumn($broj_izdanih_CD, $broj_uplacenih_CD, $uplacenih_CD); ?></a></td>
											<td class="text-center"><a class="a_link" target="_blank" <?php if($broj_izdanih_KS1 != 0) echo 'href = "statistike.php?page=predracuni&branch_id='.intval($row3["branch_id"]).'&sed='.$week1.'&id_men='. invertSign(2) .'&year='.$year1.'"'; ?>><?php echo getStyleColumn($broj_izdanih_KS1, $broj_uplacenih_KS1, $uplacenih_KS1); ?></a></td>
											<td class="text-center"><a class="a_link" target="_blank" <?php if($broj_izdanih_KS2 != 0) echo 'href = "statistike.php?page=predracuni&branch_id='.intval($row3["branch_id"]).'&sed='.$week2.'&id_men='. invertSign(3) .'&year='.$year2.'"'; ?>><?php echo getStyleColumn($broj_izdanih_KS2, $broj_uplacenih_KS2, $uplacenih_KS2); ?></a></td>
											<td class="text-center"><a class="a_link" target="_blank" <?php if($broj_izdanih_KS3 != 0) echo 'href = "statistike.php?page=predracuni&branch_id='.intval($row3["branch_id"]).'&sed='.$week3.'&id_men='. invertSign(4) .'&year='.$year3.'"'; ?>><?php echo getStyleColumn($broj_izdanih_KS3, $broj_uplacenih_KS3, $uplacenih_KS3); ?></a></td>
											<td class="text-center"><a class="a_link" target="_blank" <?php if($broj_izdanih_KS4 != 0) echo 'href = "statistike.php?page=predracuni&branch_id='.intval($row3["branch_id"]).'&sed='.$week4.'&id_men='. invertSign(6) .'&year='.$year4.'"'; ?>><?php echo getStyleColumn($broj_izdanih_KS4, $broj_uplacenih_KS4, $uplacenih_KS4); ?></a></td>
											<td class="text-center"><?php echo getStyleColumn($broj_izdanih_PM, $broj_uplacenih_PM, $uplacenih_PM); ?></td>
											<td class="text-center"><a class="a_link" target="_blank" <?php if($ukupno_broj_izdanih_KS != 0) echo 'href = "statistike.php?page=predracuni&branch_id='.intval($row3["branch_id"]).'&sed=-1&id_men='. invertSign(6) .'&year='.$year5.'"'; ?>><?php echo getStyleColumn($ukupno_broj_izdanih_KS, $ukupno_broj_uplacenih_PM, $ukupno_uplacenih_PM); ?></a></td>
										<?php
											}else{
										?>
											<td class="text-center" style = "border-left: 1px solid #111111;"><?php echo getStyleColumn($broj_izdanih_CD, $broj_uplacenih_CD, $uplacenih_CD); ?></td>
											<td class="text-center"><?php echo getStyleColumn($broj_izdanih_KS1, $broj_uplacenih_KS1, $uplacenih_KS1); ?></td>
											<td class="text-center"><?php echo getStyleColumn($broj_izdanih_KS2, $broj_uplacenih_KS2, $uplacenih_KS2); ?></td>
											<td class="text-center"><?php echo getStyleColumn($broj_izdanih_KS3, $broj_uplacenih_KS3, $uplacenih_KS3); ?></td>
											<td class="text-center"><?php echo getStyleColumn($broj_izdanih_KS4, $broj_uplacenih_KS4, $uplacenih_KS4); ?></td>
											<td class="text-center"><?php echo getStyleColumn($broj_izdanih_PM, $broj_uplacenih_PM, $uplacenih_PM); ?></td>
											<td class="text-center"><?php echo getStyleColumn($ukupno_broj_izdanih_KS, $ukupno_broj_uplacenih_PM, $ukupno_uplacenih_PM); ?></td>
										<?php
											}
										?>
										</tr>
									<?php 
											}
										}
										
											$sum_pkn = ($sum_broj_prozvanih == 0) ? number_format(0, 2, ',', '') : number_format(((($sum_broj_uplacenih) / $sum_broj_prozvanih)*100), 2, ',', ''); // suma procenta krajnje naplate
											$sum_pn = ($sum_broj_izdanih == 0) ? number_format(0, 2, ',', '') : number_format(((($sum_broj_uplacenih) / $sum_broj_izdanih)*100), 2, ',', ''); // suma procenta naplate
											$sum_cr = ($sum_broj_prozvanih == 0) ? number_format(0, 2, ',', '') : number_format(((($sum_broj_izdanih) / $sum_broj_prozvanih)*100), 2, ',', ''); // suma procenta conversion rate-a
											
									?>
									</tbody>
									<tfoot>
										<tr>
											<th></th>
											<th></th>
											<th class="text-center" style ="font-weight: bold;">Ukupno</th>
											<th class="text-center"><?php echo $sum_broj_uplacenih."/".$sum_broj_prozvanih; ?><br><span style ="font-weight: bold;"><?php echo $sum_pkn." %"; ?></span></th>
											<th class="text-center"><?php echo $sum_broj_uplacenih."/".$sum_broj_izdanih; ?><br><span style ="font-weight: bold;"><?php echo $sum_pn." %"; ?></span></th>
											<th class="text-center"><?php echo $sum_broj_izdanih."/".$sum_broj_prozvanih; ?><br><span style ="font-weight: bold;"><?php echo $sum_cr." %"; ?></span></th>
										<?php
											if (in_array(("9"), $employee_status) || in_array(("1"), $employee_status)){
										?>
											<th class="text-center" style ="font-weight: bold;"><a class="a_link" target="_blank" <?php if($sum_broj_izdanih_CD != 0) echo 'href = "statistike.php?page=predracuni&sed=0&id_men='. invertSign(1) .'&year='.$year1.'"'; ?>><?php echo getStyleColumn($sum_broj_izdanih_CD, $sum_broj_uplacenih_CD, $sum_uplacenih_CD); ?></a></th>
											<th class="text-center" style ="font-weight: bold;"><a class="a_link" target="_blank" <?php if($sum_broj_izdanih_KS1 != 0) echo 'href = "statistike.php?page=predracuni&sed='.$week1.'&id_men='. invertSign(2) .'&year='.$year1.'"'; ?>><?php echo getStyleColumn($sum_broj_izdanih_KS1, $sum_broj_uplacenih_KS1, $sum_uplacenih_KS1); ?></a></th>
											<th class="text-center" style ="font-weight: bold;"><a class="a_link" target="_blank" <?php if($sum_broj_izdanih_KS2 != 0) echo 'href = "statistike.php?page=predracuni&sed='.$week2.'&id_men='. invertSign(3) .'&year='.$year2.'"'; ?>><?php echo getStyleColumn($sum_broj_izdanih_KS2, $sum_broj_uplacenih_KS2, $sum_uplacenih_KS2); ?></a></th>
											<th class="text-center" style ="font-weight: bold;"><a class="a_link" target="_blank" <?php if($sum_broj_izdanih_KS3 != 0) echo 'href = "statistike.php?page=predracuni&sed='.$week3.'&id_men='. invertSign(4) .'&year='.$year3.'"'; ?>><?php echo getStyleColumn($sum_broj_izdanih_KS3, $sum_broj_uplacenih_KS3, $sum_uplacenih_KS3); ?></a></th>
											<th class="text-center" style ="font-weight: bold;"><a class="a_link" target="_blank" <?php if($sum_broj_izdanih_KS4 != 0) echo 'href = "statistike.php?page=predracuni&sed='.$week4.'&id_men='. invertSign(6) .'&year='.$year4.'"'; ?>><?php echo getStyleColumn($sum_broj_izdanih_KS4, $sum_broj_uplacenih_KS4, $sum_uplacenih_KS4); ?></a></th>
											<th class="text-center" style ="font-weight: bold;"><?php echo getStyleColumn($sum_broj_izdanih_PM, $sum_broj_uplacenih_PM, $sum_uplacenih_PM); ?></a></th>
											<th class="text-center" style ="font-weight: bold;"><a class="a_link" target="_blank" <?php if($sum_broj_izdanih_KS != 0) echo 'href = "statistike.php?page=predracuni&sed=-1&id_men='. invertSign(6) .'&year='.$year5.'"'; ?>><?php echo getStyleColumn($sum_broj_izdanih_KS, $sum_broj_uplacenih_KS, $sum_uplacenih_KS); ?></a></th>
										<?php 
											}else{
										?>
											<th class="text-center" style ="font-weight: bold;"><?php echo getStyleColumn($sum_broj_izdanih_CD, $sum_broj_uplacenih_CD, $sum_uplacenih_CD); ?></th>
											<th class="text-center" style ="font-weight: bold;"><?php echo getStyleColumn($sum_broj_izdanih_KS1, $sum_broj_uplacenih_KS1, $sum_uplacenih_KS1); ?></th>
											<th class="text-center" style ="font-weight: bold;"><?php echo getStyleColumn($sum_broj_izdanih_KS2, $sum_broj_uplacenih_KS2, $sum_uplacenih_KS2); ?></th>
											<th class="text-center" style ="font-weight: bold;"><?php echo getStyleColumn($sum_broj_izdanih_KS3, $sum_broj_uplacenih_KS3, $sum_uplacenih_KS3); ?></th>
											<th class="text-center" style ="font-weight: bold;"><?php echo getStyleColumn($sum_broj_izdanih_KS4, $sum_broj_uplacenih_KS4, $sum_uplacenih_KS4); ?></th>
											<th class="text-center" style ="font-weight: bold;"><?php echo getStyleColumn($sum_broj_izdanih_PM, $sum_broj_uplacenih_PM, $sum_uplacenih_PM); ?></th>
											<th class="text-center" style ="font-weight: bold;"><?php echo getStyleColumn($sum_broj_izdanih_KS, $sum_broj_uplacenih_KS, $sum_uplacenih_KS); ?></th>
										
										<?php 
											}
										?>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>