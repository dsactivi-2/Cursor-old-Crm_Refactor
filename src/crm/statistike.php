<?php
	
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus = explode( ',' , getEmployeeStatus());
	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: index.php");
    }
    
    function getStartAndEndDate($week, $year) {
        $dto = new DateTime();
        $ret['week_start'] = $dto->setISODate($year, $week)->format('Y-m-d');
        $ret['week_end'] = $dto->modify('+6 days')->format('Y-m-d');
        return $ret;
    }

?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Statistike | <?php getTitle(); ?></title>

	<?php include('includes/head.php'); 
	if (in_array($getUserIp, $getIpWhiteList)){
	?>
</head>
<body>
	<header>
		<?php include('header.php'); ?>
	</header>
	<div id="sidebar">
		<?php include('menu.php'); ?>
	</div>
	<div id="content">
		<div class="container-fluid">
		<?php
		switch ($page){

			case "predracuni":
				?>
				<div class="row">
					<div class="col-xs-12">
						<h1><i class="fa fa-cogs idk_color_green" aria-hidden="true"></i> Lista predračuna</h1>
					</div>
					<div class="col-xs-12">
						<hr/>
					</div>
				</div>
				<div class="content_box" style="min-height: 100px;">
					<div class="row">
						<div class="col-md-12">
							
						<?php
								$id_men = $_GET['id_men'];
								$atr = $_GET['sed'];
								$year = $_GET['year'];
								$branch_id = intval($_GET['branch_id']);
								if($branch_id != 0){
									//echo "is set";
									//Aktivni Deaktivirani zaposlenici START
									$zapDiplA = array();		//Aktivni zaposlenici
									$zapDiplD = array();		//Deaktivirani zaposlenici
									$zapDiplO = array();		//Ostali zaposlenici
									array_push($zapDiplA, 0);
									array_push($zapDiplD, 0);
									array_push($zapDiplO, 0);
									$query1 = $db->prepare("
										SELECT stat.zaduzen_zaposlenik_id, emp.employee_status, emp.employee_supervizor
										FROM idk_nd_menadzeri_statistike stat
										INNER JOIN idk_employees emp
										ON stat.zaduzen_zaposlenik_id = emp.employee_id
										WHERE stat.vrsta_aktivnosti = 0 AND stat.prethodni_zaposlenik_id is null AND emp.employee_poslovnica = ".$branch_id."
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
									$uslov_poslovnica = "AND (pr_zaposlenik IN (".implode(",",$zapDiplA)."))";
									//Aktivni Deaktivirani zaposlenici END 
								}else{
									//echo "no set";
									$uslov_poslovnica = " ";
								}
								//---------------------------------------------------------------
								if($id_men>0){

									$get_menadzer = $db->prepare('
										SELECT employee_firstname, employee_lastname FROM idk_employees WHERE employee_id=:id_men
									');

									$get_menadzer->execute(array(
										
										':id_men'=>$id_men

									));
									
									$tmp = $get_menadzer->fetch();
									$ime_men = $tmp['employee_firstname'];
									$prezime_men = $tmp['employee_lastname'];
								}
									
								
								echo '<div class="col-lg-11">';
								if($atr != 0 && $atr != -1){
									$sed = getStartAndEndDate($atr,$year);
									$poc_sed = $sed['week_start'];
									$kraj_sed = $sed['week_end'];
									$uslov="BETWEEN '".$poc_sed."' AND '".$kraj_sed."'";
									if($id_men>0){
										$uslov_men ="AND pr_zaposlenik = ".$id_men;
										echo '<h1>Predracuni od <b>'.$ime_men.' '.$prezime_men.'</b> u periodu od: <b>'.date("d.m.Y", strtotime($poc_sed)).'</b> do <b>'.date("d.m.Y", strtotime($kraj_sed)).'</b></h1>';
									}
									else {
										$uslov_men="";
										echo '<h1>Predracuni <b>svih agenata</b> u periodu od: <b>'.date("d.m.Y", strtotime($poc_sed)).'</b> do <b>'.date("d.m.Y", strtotime($kraj_sed)).'</b></h1>';
									}

									
								}
								else if ($atr==0){

									$uslov ="BETWEEN '".date("Y-m-d 00:00:00")."' AND '".date("Y-m-d 23:59:59")."'";

									if($id_men>0){
										$uslov_men ="AND pr_zaposlenik = ".$id_men;
										echo "<h1>Predracuni od <b>".$ime_men." ".$prezime_men."</b> danas <b>(".date("d.m.Y").")</b></h1>";
									}
									else {
										$uslov_men="";
										echo  "<h1>Predracuni <b>svih agenata</b> danas <b>(".date("d.m.Y").")</b></h1>";
									}
								


								}

								else if ($atr==-1){
									$trenutni_datum = date("Y-m-d H:i:s");
									$week4 = date("W", strtotime("-3 Week", strtotime($trenutni_datum)));
									$poc_sed=getStartAndEndDate($week4, $year);
									$kraj_sed=date("Y-m-d 23:59:59");
									$uslov="BETWEEN '".$poc_sed['week_start']."' AND '".$kraj_sed."'";

									if($id_men>0){

										$uslov_men ="AND pr_zaposlenik = ".$id_men;
										echo "<h1>Predracuni od <b>".$ime_men." ".$prezime_men." </b>u periodu od: <b>".date("d.m.Y", strtotime($poc_sed['week_start']))." </b>do<b> ".date("d.m.Y", strtotime($kraj_sed))."</b></h1>";
									}

									else {

										$uslov_men="";
										echo "<h1>Predracuni <b>svih agenata</b> u periodu od: <b>".date("d.m.Y", strtotime($poc_sed['week_start']))." </b>do<b> ".date("d.m.Y", strtotime($kraj_sed))."</b></h1>";
									}
									
								}
														
								echo '<hr></div><div class="col-lg-1"><a href="#" id="testich" class="btn btn-primary material-btn material-btn_success"><i class="fa fa-file-o" aria-hidden="true"></i> Export</a></div>';
							
							?>
							<table id="tabela_predracuni" class="table-striped" style="width:100%">
								<thead>
									<tr>
										<th>Ime i prezime kandidata</th>
										<?php
										if($id_men<0)
											echo "<th>Ime i prezime agenta</th>";
										?>
										<th class="text-center">Naziv predračuna</th>
										<th class="text-center" >Datum kreiranja ugovora</th>
										<th class="text-center" >Status</th>
										<th class="text-center" >Status ugovora</th>
										<th>Vrsta ugovora</th>
										
									</tr>
								</thead>
								<tbody>

							<?php
								$get_uplaceni = $db->prepare("
									
									SELECT pred.pr_zaposlenik, pred.pr_kandidat_id, kan.vrsta_ugovora_nd_kandidata, pred.pr_datum_kreiranja, 
										pred.pr_broj_predracuna, pred.pr_file, pred.pr_status, 
										kan.ime_nd_kandidata AS ime, kan.prezime_nd_kandidata AS prezime,
										pred.pr_datum_uplate

									FROM idk_predracuni pred
									JOIN idk_nd_kandidata kan 
									ON pred.pr_kandidat_id = kan.id_broj_nd_kandidata
									JOIN idk_employees emp
									ON emp.employee_id = pred.pr_zaposlenik
									WHERE pred.pr_status is not NULL
									AND pred.pr_rata = 1
									".$uslov_men." ".$uslov_poslovnica."
									
									AND pred.pr_datum_kreiranja > '2021-07-01 00:00:00'
									AND pred.pr_datum_kreiranja ".$uslov."
									AND pred.pr_id IN(
										SELECT MAX(pr.pr_id) 
										FROM idk_predracuni pr 
										WHERE pr.pr_datum_kreiranja >= '2021-07-01 00:00:00' 
										AND pr.pr_rata = 1 
										GROUP BY pr.pr_kandidat_id
									)
									AND emp.employee_status != 0
									
									
								
								");

								$get_uplaceni->execute();

								$cnt=0;
								while ($row_file = $get_uplaceni->fetch()){
									$cnt++;
									$id_zaposlenik=$row_file['pr_zaposlenik'];
									$id_kan=$row_file['pr_kandidat_id'];
									$status_predr = $row_file['pr_status'];
									$pr_file = $row_file['pr_file'];
									$ime = $row_file['ime'];
									$prezime = $row_file['prezime'];
									$br_predracuna = $row_file['pr_broj_predracuna'];
									$datum_kreiranja = substr($row_file['pr_datum_kreiranja'],0,10);
									$vrsta_ugovora_kandidata_ispis_otvoren = $row_file['vrsta_ugovora_nd_kandidata'];
									$datum_uplate = $row_file['pr_datum_uplate'];
									$datum_kreiranja = date("d.m.Y",strtotime($datum_kreiranja));
									$datum_uplate = date("d.m.Y",strtotime($datum_uplate));
									?>
										
											<tr>
												<td><a target="_blank" href="<?php getSiteURL(); ?>nostrifikacija_diploma?page=otvori_ND_kandidata&id=<?php echo $id_kan;?>"><?php echo $ime." ".$prezime;?></a></td>
												<?php
													if($id_men<0){
														$get_menadzer = $db->prepare('
															SELECT employee_firstname, employee_lastname FROM idk_employees WHERE employee_id=:id_zaposlenik
														');

														$get_menadzer->execute(array(
															
															':id_zaposlenik'=>$id_zaposlenik

														));
										
														$tmp = $get_menadzer->fetch();
														$ime_men = $tmp['employee_firstname'];
														$prezime_men = $tmp['employee_lastname'];
														echo "<td>".$ime_men." ".$prezime_men."</td>";			
													}
												?>
												<td class="text-center"><?php echo $br_predracuna;?></td>
												<td class="text-center"><?php echo $datum_kreiranja;?></td>
												
											<?php
											
												if($status_predr == 0){
													$status_show = '<td class="text-center"><span class="label label-warning material-label material-label_warning main-container__column ">Arhiviran</span></td>';
												}else if($status_predr == 1){
													$status_show = '<td class="text-center"><span class="label label-info material-label material-label_info main-container__column">Poslan</span></td>';
												}else if($status_predr == 2){
													$status_show = '<td class="text-center"><span class="label label-success material-label material-label_success main-container__column">Uplaćen na: '.date("d.m.Y", strtotime($datum_uplate)).'</span></td>';
												}else if($status_predr == 3){
													$status_show = '<td class="text-center"><span class="label label-danger material-label material-label_danger main-container__column">In Caso 1</span></td>';
												}else if($status_predr == 4){
													$status_show = '<td class="text-center"><span class="label label-danger material-label material-label_danger main-container__column">In Caso 2</span></td>';
												}else if($status_predr == 5){
													$status_show = '<td class="text-center"><span class="label label-danger material-label material-label_danger main-container__column">In Caso 3</span></td>';
												}

												echo $status_show;
												
												if($pr_file != NULL){
													$status_ugovora_show = '<td class="text-center"><span class="label label-success material-label material-label_success main-container__column">Prihvaćen</span></td>';
												}else{
													$status_ugovora_show = '<td class="text-center"><span class="label label-danger material-label material-label_danger main-container__column">Neprihvaćen</span></td>';
												}
												
												echo $status_ugovora_show;

												if($vrsta_ugovora_kandidata_ispis_otvoren == 1){
													echo '<td>Ugovor bez popusta na 2 rate!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 2){
													echo '<td>Ugovor sa popustom na 2 rate!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 3){
													echo '<td>Ugovor bez popusta na 5 rata!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 4){
													echo '<td>Ugovor sa popustom na 5 rata!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 5){
													echo '<td>Ugovor bez popusta na 3 rate!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 6){
													echo '<td>Ugovor sa popustom na 3 rate!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 7){
													echo '<td>Ugovor bez popusta na 4 rate!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 8){ 
													echo '<td>Ugovor sa popustom na 4 rate!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 9){ 
													echo '<td>Ugovor bez popusta na 1 ratu!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 10){ 
													echo '<td>Ugovor sa popustom na 1 ratu!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 11){ 
													echo '<td>Mikrofin ugovor bez popusta!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 12){ 
													echo '<td>Mikrofin ugovor sa popustom!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 21){ 
													echo '<td>Ugovor sa 20% popusta na 1 ratu!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 22){ 
													echo '<td>Ugovor sa 20% popusta na 2 rate!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 23){ 
													echo '<td>Ugovor sa 20% popusta na 3 rate!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 24){ 
													echo '<td>Ugovor sa 20% popusta na 4 rate!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 25){ 
													echo '<td>Ugovor sa 20% popusta na 5 rata!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 71){ 
													echo '<td>Ugovor za struke na 1 ratu sa 30% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 72){ 
													echo '<td>Ugovor za struke na 2 rate sa 30% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 73){ 
													echo '<td>Ugovor za struke na 3 rate sa 30% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 74){ 
													echo '<td>Ugovor za struke na 4 rate sa 30% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 75){ 
													echo '<td>Ugovor za struke na 5 rata sa 30% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 51){ 
													echo '<td>Ugovor za struke na 1 ratu sa 50% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 52){ 
													echo '<td>Ugovor za struke na 2 rate sa 50% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 53){ 
													echo '<td>Ugovor za struke na 3 rate sa 50% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 54){ 
													echo '<td>Ugovor za struke na 4 rate sa 50% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 55){ 
													echo '<td>Ugovor za struke na 5 rata sa 50% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 99){ 
													echo '<td>Ugovor za struke na 1 ratu sa 100% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 61){ 
													echo '<td>Ugovor za struke na 1 ratu sa 20% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 62){ 
													echo '<td>Ugovor za struke na 2 rate sa 20% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 63){ 
													echo '<td>Ugovor za struke na 3 rate sa 20% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 64){ 
													echo '<td>Ugovor za struke na 4 rate sa 20% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 65){ 
													echo '<td>Ugovor za struke na 5 rata sa 20% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 41){ 
													echo '<td>Ugovor za struke na 1 ratu sa 70% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 42){ 
													echo '<td>Ugovor za struke na 2 rate sa 70% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 43){ 
													echo '<td>Ugovor za struke na 3 rate sa 70% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 44){ 
													echo '<td>Ugovor za struke na 4 rate sa 70% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 45){ 
													echo '<td>Ugovor za struke na 5 rata sa 70% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 82){ 
													echo '<td>Ugovor sa popustom 10% na 2 rate!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 83){ 
													echo '<td>Ugovor sa popustom 10% na 3 rate!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 84){ 
													echo '<td>Ugovor sa popustom 10% na 4 rate!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 85){ 
													echo '<td>Ugovor sa popustom 10% na 5 rata!</td>';
												}
												else{
													echo '<td></td>';
												}

										?>
										</tr>
										<?php
									}
									?>
											
										</tbody>
							</table>
							<input type ="hidden" id="q1" name="q1" value="<?php echo $uslov; ?>">
							<input type ="hidden" id="q2" name="q2" value="<?php echo $uslov_men; ?>">
							<input type ="hidden" id="id_men" name="id_men" value="<?php echo $id_men; ?>">
						</div>
					</div>
				</div>
				<?php
			break;
			
			case "prosjek_predracuna":
			//prosjek zadnjih  7 dana	7
			//prosjek zadnjih 30 dana	30
			//prosjek zadnjih 90 dana	90
			//prosjek zadnjih 6 mjeseci 180
			//prosjek zadnju godinu 	365
			
			$tip_statistike = $_GET['tip'];
			$query_prosjek_uplate=$db->prepare("
				SELECT pred.pr_zaposlenik, pred.pr_kandidat_id, kan.vrsta_ugovora_nd_kandidata, pred.pr_datum_kreiranja, 
				pred.pr_broj_predracuna, pred.pr_file, pred.pr_status, kan.ime_nd_kandidata AS ime, kan.prezime_nd_kandidata AS prezime, 
				pred.pr_datum_uplate, emp.employee_firstname, emp.employee_lastname 
				FROM idk_predracuni pred 
				JOIN idk_nd_kandidata kan 
				ON pred.pr_kandidat_id = kan.id_broj_nd_kandidata 
				JOIN idk_employees emp 
				ON emp.employee_id = pred.pr_zaposlenik 
				WHERE date(pred.pr_datum_kreiranja) BETWEEN date(curdate() - interval ".$tip_statistike." day) and date(curdate() - interval 1 day) 
				AND pred.pr_rata = 1 
				AND pred.pr_status is not NULL
				AND pred.pr_vrsta_predracuna = 1
				AND pred.pr_id IN(
					SELECT MAX(pr.pr_id) 
					FROM idk_predracuni pr 
					WHERE pr.pr_datum_kreiranja >= '2020-11-20 00:00:00' 
					AND pr.pr_rata = 1 
					GROUP BY pr.pr_kandidat_id
				)
			");
			$query_prosjek_uplate -> execute();
		?>
			<div class="row">
				<div class="col-xs-12">
					<h1><i class="fa fa-cogs idk_color_green" aria-hidden="true"></i> Lista predračuna</h1>
				</div>
				<div class="col-xs-12">
					<hr/>
				</div>
			</div>
			<div class="content_box" style="min-height: 100px;">
				<div class="row">
					<div class="col-lg-12">
						<div class="col-lg-10">
							<h1>Izdani predračuni u zadnjih <?php echo $tip_statistike; ?> dana (<?php echo date('d.m.Y', strtotime('-'.$tip_statistike.' days'));?> do <?php echo date('d.m.Y',strtotime('- 1 days'));?>)</h1>
							<hr>
						</div>
						<div class="col-lg-2">
							<a href="#" id="export_lista_predracuna" class="btn btn-primary material-btn material-btn_success"><i class="fa fa-file-o" aria-hidden="true"></i> Export</a>

						</div>
						
						
						
						
						<script>
								$(document).ready(function() { 
										var table = $('#tabela_prosjek_uplate').DataTable({
										// responsive: true,
										// "order": [[ 3, "desc" ]],
										// "ordering": true,
										 // "bAutoWidth": false,

										// "paging":   true,
										// "ordering": true,
										// "info":     true
										responsive: true,
										"order": [[ 2, "desc" ]],
										paging: true,
										pageLength: 10,
										"bAutoWidth": false,

										"aoColumns": [
												{ "width": "16%"},
												{ "width": "16%"},
												{ "width": "16%"},
												{ "width": "16%"},
												{ "width": "16%"},
												{ "width": "20%"}
											],
										columnDefs: [
											{ type: 'de_date', targets: 3 }
										],
										"search": {
											"regex": true,
										},
									});
								});
						</script>
						<div class="col-md-8 col-xs-12">
							<table id="tabela_prosjek_uplate" class="display">
								<thead>
									<tr>
										<th>Kandidat</th>
										<th>Agent</th>
										<th class="text-center">Naziv predračuna</th>
										<th class="text-center" >Datum kreiranja ugovora</th>
										<th class="text-center" >Status</th>
										<th>Vrsta ugovora</th>
									</tr>
								</thead>
								<tbody>
								<?php
								while($row_prosjek_uplate = $query_prosjek_uplate->fetch()){
									
									$id_zaposlenik=$row_prosjek_uplate['pr_zaposlenik'];
									$id_kan=$row_prosjek_uplate['pr_kandidat_id'];
									$status_predr = $row_prosjek_uplate['pr_status'];
									$pr_file = $row_prosjek_uplate['pr_file'];
									$ime_kandidata = $row_prosjek_uplate['ime'];
									$prezime_kandidata = $row_prosjek_uplate['prezime'];
									$ime_zaposlenika = $row_prosjek_uplate['employee_firstname'];
									$prezime_zaposlenika = $row_prosjek_uplate['employee_lastname'];
									$br_predracuna = $row_prosjek_uplate['pr_broj_predracuna'];
									$datum_kreiranja = substr($row_prosjek_uplate['pr_datum_kreiranja'],0,10);
									$vrsta_ugovora_kandidata_ispis_otvoren = $row_prosjek_uplate['vrsta_ugovora_nd_kandidata'];
									$datum_uplate = $row_prosjek_uplate['pr_datum_uplate'];
									$datum_kreiranja = date("d.m.Y",strtotime($datum_kreiranja));
									$datum_uplate = date("d.m.Y",strtotime($datum_uplate));
								
								?>
								
									<tr>
										<td><a target="_blank" href="<?php getSiteURL(); ?>nostrifikacija_diploma?page=otvori_ND_kandidata&id=<?php echo $id_kan;?>"><?php echo $ime_kandidata." ".$prezime_kandidata;?></a></td>
										<td><?php echo $ime_zaposlenika.' '.$prezime_zaposlenika;?></td>	
										<td class="text-center"><?php echo $br_predracuna;?></td>
										<td class="text-center"><?php echo $datum_kreiranja;?></td>

										
									<?php
									
										if($status_predr == 0){
											$status_show = '<td class="text-center"><span class="label label-warning material-label material-label_warning main-container__column ">Arhiviran</span></td>';
										}else if($status_predr == 1){
											$status_show = '<td class="text-center"><span class="label label-info material-label material-label_info main-container__column">Poslan</span></td>';
										}else if($status_predr == 2){
											$status_show = '<td class="text-center"><span class="label label-success material-label material-label_success main-container__column">Uplaćen na: '.date("d.m.Y", strtotime($datum_uplate)).'</span></td>';
										}else if($status_predr == 3){
											$status_show = '<td class="text-center"><span class="label label-danger material-label material-label_danger main-container__column">In Caso 1</span></td>';
										}else if($status_predr == 4){
											$status_show = '<td class="text-center"><span class="label label-danger material-label material-label_danger main-container__column">In Caso 2</span></td>';
										}else if($status_predr == 5){
											$status_show = '<td class="text-center"><span class="label label-danger material-label material-label_danger main-container__column">In Caso 3</span></td>';
										}

										echo $status_show;
									
										if($vrsta_ugovora_kandidata_ispis_otvoren == 1){
											echo '<td class="text-center">Ugovor bez popusta na 2 rate!</td>';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 2){
											echo '<td class="text-center">Ugovor sa popustom na 2 rate!</td>';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 3){
											echo '<td class="text-center">Ugovor bez popusta na 5 rata!</td>';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 4){
											echo '<td class="text-center">Ugovor sa popustom na 5 rata!</td>';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 5){
											echo '<td class="text-center">Ugovor bez popusta na 3 rate!</td>';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 6){
											echo '<td class="text-center">Ugovor sa popustom na 3 rate!</td>';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 7){
											echo '<td class="text-center">Ugovor bez popusta na 4 rate!</td>';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 8){ 
											echo '<td class="text-center">Ugovor sa popustom na 4 rate!</td>';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 9){ 
											echo '<td class="text-center">Ugovor bez popusta na 1 ratu!</td>';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 10){ 
											echo '<td class="text-center">Ugovor sa popustom na 1 ratu!</td>';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 11){ 
											echo '<td class="text-center">Mikrofin ugovor bez popusta!</td>';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 12){ 
											echo '<td class="text-center">Mikrofin ugovor sa popustom!</td>';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 21){ 
											echo '<td class="text-center">Ugovor sa 20% popusta na 1 ratu!</td>';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 22){ 
											echo '<td class="text-center">Ugovor sa 20% popusta na 2 rate!</td>';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 23){ 
											echo '<td class="text-center">Ugovor sa 20% popusta na 3 rate!</td>';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 24){ 
											echo '<td class="text-center">Ugovor sa 20% popusta na 4 rate!</td>';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 25){ 
											echo '<td class="text-center">Ugovor sa 20% popusta na 5 rata!</td>';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 71){ 
											echo '<td class="text-center">Ugovor za struke na 1 ratu sa 30% popusta!</td>';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 72){ 
											echo '<td class="text-center">Ugovor za struke na 2 rate sa 30% popusta!</td>';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 73){ 
											echo '<td class="text-center">Ugovor za struke na 3 rate sa 30% popusta!</td>';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 74){ 
											echo '<td class="text-center">Ugovor za struke na 4 rate sa 30% popusta!</td>';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 75){ 
											echo '<td class="text-center">Ugovor za struke na 5 rata sa 30% popusta!</td>';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 51){ 
											echo '<td class="text-center">Ugovor za struke na 1 ratu sa 50% popusta!</td>';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 52){ 
											echo '<td class="text-center">Ugovor za struke na 2 rate sa 50% popusta!</td>';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 53){ 
											echo '<td class="text-center">Ugovor za struke na 3 rate sa 50% popusta!</td>';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 54){ 
											echo '<td class="text-center">Ugovor za struke na 4 rate sa 50% popusta!</td>';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 55){ 
											echo '<td class="text-center">Ugovor za struke na 5 rata sa 50% popusta!</td>';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 99){ 
											echo '<td class="text-center">Ugovor za struke na 1 ratu sa 100% popusta!</td>';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 61){ 
											echo '<td class="text-center">Ugovor za struke na 1 ratu sa 20% popusta!</td>';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 62){ 
											echo '<td class="text-center">Ugovor za struke na 2 rate sa 20% popusta!</td>';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 63){ 
											echo '<td class="text-center">Ugovor za struke na 3 rate sa 20% popusta!</td>';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 64){ 
											echo '<td class="text-center">Ugovor za struke na 4 rate sa 20% popusta!</td>';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 65){ 
											echo '<td class="text-center">Ugovor za struke na 5 rata sa 20% popusta!</td>';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 41){ 
											echo '<td class="text-center">Ugovor za struke na 1 ratu sa 70% popusta!</td>';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 42){ 
											echo '<td class="text-center">Ugovor za struke na 2 rate sa 70% popusta!</td>';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 43){ 
											echo '<td class="text-center">Ugovor za struke na 3 rate sa 70% popusta!</td>';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 44){ 
											echo '<td class="text-center">Ugovor za struke na 4 rate sa 70% popusta!</td>';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 45){ 
											echo '<td class="text-center">Ugovor za struke na 5 rata sa 70% popusta!</td>';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 82){ 
											echo '<td class="text-center">Ugovor sa popustom 10% na 2 rate!</td>';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 83){ 
											echo '<td class="text-center">Ugovor sa popustom 10% na 3 rate!</td>';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 84){ 
											echo '<td class="text-center">Ugovor sa popustom 10% na 4 rate!</td>';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 85){ 
											echo '<td class="text-center">Ugovor sa popustom 10% na 5 rata!</td>';
										}
										else{
											echo '<td></td>';
										}

									?>
									</tr>
									<?php
								}
								?>
								
								</tbody>
								<tfoot>
								</tfoot>
							</table>
						</div>
						<div id="selected_agents" style="display: none;"></div>
						<input id="h_dana" type="hidden" value="<?php echo $tip_statistike; ?>">
						<div class="col-md-4 col-xs-12">
							<script>
							$(document).ready(function() { 
								var tabela_rang_lista = $('#rang_lista').DataTable({
									"searching":false,
									"bPaginate": false,
									"bLengthChange": false,
									"bFilter": false,
									"bInfo": false,
									responsive: true,
									"order": [[ 3, "desc" ]],
									paging: true,
									pageLength: 10,
									"bAutoWidth": false,

									'columnDefs': [
													{
													'targets': 0,
													'checkboxes': {
														'selectRow': true
														}
													}
										],

									"aoColumns": [
											{ "width": "6%"},
											{ "width": "30%"},
											{ "width": "47%"},
											{ "width": "17%"}
										]
								});
								
								$("#rang_lista").on('change',"input[type='checkbox']",function(e){	
																	
									$("#selected_agents .checkbox").remove();
									var rows_selected = tabela_rang_lista.column(0).checkboxes.selected();
									var tmp = [];
									// Iterate over all selected checkboxes
									$.each(rows_selected, function(index, rowId){
										if($.inArray(rowId, tmp)=== -1){
											tmp.push(rowId);
											$("#selected_agents").append(
												rowId
											);
										}
										
									});
																		
									var selectedIds = $('#selected_agents').find(".checkbox").map(function(){return $(this).val(); }).get();
									var dana = $("#h_dana").val();
									var	tip = "PR"
									
									$.ajax({
										url: 'ajax_data.php?page=refresh_list_kandidati',
										type: 'POST',
										data: {'selected':selectedIds, 'dana':dana, 'tip':tip},
										dataType: 'json',
										success: function(json) {
											
											var arr = json;
											var table = $('#tabela_prosjek_uplate').DataTable();
											
											table.clear().draw();
											table.destroy();
											
											var table = $('#tabela_prosjek_uplate').DataTable({

												responsive: true,

												"bAutoWidth": false
											})
											for(var i = 0; i < arr['kandidat'].length; i++){
												
												$('#tabela_prosjek_uplate').dataTable().fnAddData([
															   arr['kandidat'][i],
															   arr['agent'][i],
															   arr['naziv_predracuna'][i],
															   arr['datum_kreiranja'][i],
															   arr['status'][i],
															   arr['vrsta_ugovora'][i]
															  ]);
											}
										},
										error: function (xhr, ajaxOptions, thrownError) {
											alert(xhr.status);
											alert(thrownError);
										}
									});
									
									
		
								});
								$('#export_lista_predracuna').on("click",function(e) {
									$("#selected_agents .checkbox").remove();
									var tip_stat = $("#tip_stat_h").val();
									var rows_selected = tabela_rang_lista.column(0).checkboxes.selected();
									var tmp = [];
									$.each(rows_selected, function(index, rowId){
										if($.inArray(rowId, tmp)=== -1){
											tmp.push(rowId);
											$("#selected_agents").append(
												rowId
											);
										}
										
									});
									
									var selectedIds = $('#selected_agents').find(".checkbox").map(function(){return $(this).val(); }).get();
									var query = new URLSearchParams();
									query.append('prozor','export_lista_predracuna');
									query.append('tip_stat',tip_stat);
									query.append('selected',selectedIds);
									url = "export_excel.php?" + query.toString();

									$('#export_predracuni_perioda_div').load(url);
									
								
									return false;
									e.preventDefault();
								});
							});
							
							</script>
							<table id="rang_lista" class="display" style="border-left: 3px solid;padding-left: 35px;">
								<thead>
									<th><input type="checkbox" id="select_all" name="select_all"></th>
									<th class="text-center">Agent</th>
									<th class="text-center">Poslovnica</th>
									<th class="text-center">Izdanih predračuna</th>
								</thead>
								<tbody>
								<?php 
									$query_rang_lista = $db->prepare("
										SELECT count(emp.employee_id) as cnt, emp.employee_firstname, emp.employee_lastname, employee_id, pos.branch_name, pos.branch_state
										FROM idk_predracuni pred 
										JOIN idk_nd_kandidata kan 
										ON pred.pr_kandidat_id = kan.id_broj_nd_kandidata 
										JOIN idk_employees emp 
										ON emp.employee_id = pred.pr_zaposlenik
										JOIN idk_poslovnice pos
										ON pos.branch_id = emp.employee_poslovnica
										
										WHERE date(pr_datum_kreiranja) BETWEEN date(curdate() - interval ".$tip_statistike." day) and date(curdate() - interval 1 day) 
										AND pr_rata = 1 
										AND pr_status != 0 
										AND pr_vrsta_predracuna = 1 
										GROUP BY (emp.employee_id)
										
										UNION

										SELECT 0 as cnt, emp.employee_firstname, emp.employee_lastname, emp.employee_id, pos.branch_name, pos.branch_state
										FROM idk_employees emp
										JOIN idk_poslovnice pos
										ON pos.branch_id = emp.employee_poslovnica
										WHERE employee_status LIKE '%15%'
                                        AND emp.employee_id NOT IN (
                                        	SELECT  emp.employee_id
											FROM idk_predracuni pred 
											JOIN idk_nd_kandidata kan 
											ON pred.pr_kandidat_id = kan.id_broj_nd_kandidata 
											JOIN idk_employees emp 
											ON emp.employee_id = pred.pr_zaposlenik
											JOIN idk_poslovnice pos
											ON pos.branch_id = emp.employee_poslovnica
										
											WHERE date(pr_datum_kreiranja) BETWEEN date(curdate() - interval ".$tip_statistike." day) and date(curdate() - interval 1 day) 
											AND pr_rata = 1 
											AND pr_status != 0 
                                            AND pr_vrsta_predracuna = 1 
                                            GROUP BY (emp.employee_id)
                                        )
										ORDER BY employee_firstname desc





									");

									$query_rang_lista -> execute();
									$ukUpl=0;
									while ($row_rang_lista = $query_rang_lista -> fetch()){
										
										$agent = $row_rang_lista['employee_firstname'].' '.$row_rang_lista['employee_lastname'];
										$broj_uplata = $row_rang_lista['cnt'];
										$agent_id = $row_rang_lista['employee_id'];
										$agent_poslovnica = $row_rang_lista['branch_name'];
										$poslovnica_state = $row_rang_lista['branch_state'];
										
										if($poslovnica_state == "BiH"){
											$poslovnica_state = "images/bs3d.png";											
										}
										else if($poslovnica_state == "Njemačka"){
											$poslovnica_state = "images/de3d.png";
										}
										else if($poslovnica_state == "Srbija"){
											$poslovnica_state = "images/sr3d.png";
										}
										else{
											$poslovnica_state = "images/globe3d.png";
										}
										
										?>
										<tr>
											<td class="text-center"><input class="checkbox" type="checkbox" name="selectedrows[<?php echo $agent_id; ?>]" value="<?php echo $agent_id; ?> "></td>
											<td class="text-center"><?php echo $agent; ?></td>
											<td class="text-center"><img src="<?php echo $poslovnica_state; ?>" width="25"></td>
											<td class="text-center"><b><?php echo $broj_uplata; ?></b></td>
										</tr>
										
										<?php
										$ukUpl = $ukUpl + $broj_uplata;
									}
								?>
								</tbody>
								<tfoot>
										<td></td>
										<td></td>
										<td class="text-center"><b>UKUPNO:</b></td>
										<td class="text-center"><b><?php echo $ukUpl; ?></b></td>

								</tfoot>
							</table>
							<input type="hidden" id="tip_stat_h" value="<?php echo $tip_statistike; ?>">
						<div id="export_predracuni_perioda_div" ></div>
						</div>
					</div>
				</div>
			</div>	
			<?php
			break;
			
			case "prosjek_uplata":
			//prosjek zadnjih  7 dana	7
			//prosjek zadnjih 30 dana	30
			//prosjek zadnjih 90 dana	90
			//prosjek zadnjih 6 mjeseci 180
			//prosjek zadnju godinu 	365
			
			$tip_statistike = $_GET['tip'];
			$query_prosjek_uplate=$db->prepare('
				SELECT pred.pr_zaposlenik, pred.pr_kandidat_id, kan.vrsta_ugovora_nd_kandidata, pred.pr_datum_kreiranja, 
				pred.pr_broj_predracuna, pred.pr_file, pred.pr_status, kan.ime_nd_kandidata AS ime, kan.prezime_nd_kandidata AS prezime, 
				pred.pr_datum_uplate, emp.employee_firstname, emp.employee_lastname 
				FROM idk_predracuni pred 
				JOIN idk_nd_kandidata kan 
				ON pred.pr_kandidat_id = kan.id_broj_nd_kandidata 
				JOIN idk_employees emp 
				ON emp.employee_id = pred.pr_zaposlenik 
				WHERE date(pr_datum_uplate) BETWEEN date(curdate() - interval '.$tip_statistike.' day) and date(curdate() - interval 1 day) 
				AND pr_rata = 1 
				AND pr_status != 0 
				AND pr_vrsta_predracuna = 1
				AND pr_uplaceno = 1
			');
			$query_prosjek_uplate -> execute();
		?>
			<div class="row">
				<div class="col-xs-12">
					<h1><i class="fa fa-cogs idk_color_green" aria-hidden="true"></i> Lista Uplata</h1>
				</div>
				<div class="col-xs-12">
					<hr/>
				</div>
			</div>
			<div class="content_box" style="min-height: 100px;">
				<div class="row">
					<div class="col-lg-12">
						<div class="col-lg-10">
							<h1>Uplaćeni predračuni u zadnjih <?php echo $tip_statistike; ?> dana (<?php echo date('d.m.Y', strtotime('-'.$tip_statistike.' days'));?> do <?php echo date('d.m.Y',strtotime('- 1 days'));?>)</h1>
							<hr>
						</div>
						<div class="col-lg-2">
							<a href="#" id="export_lista_uplata" class="btn btn-primary material-btn material-btn_success"><i class="fa fa-file-o" aria-hidden="true"></i> Export</a>

						</div>
						<script>
								$(document).ready(function() { 
										var table = $('#tabela_prosjek_uplate').DataTable({
										// responsive: true,
										// "order": [[ 3, "desc" ]],
										// "ordering": true,
										 // "bAutoWidth": false,

										// "paging":   true,
										// "ordering": true,
										// "info":     true
										responsive: true,
										"order": [[ 2, "desc" ]],
										// paging: true,
										// pageLength: 10,
										"bAutoWidth": false,

										"aoColumns": [
												{ "width": "16%"},
												{ "width": "16%"},
												{ "width": "16%"},
												{ "width": "16%"},
												{ "width": "16%"},
												{ "width": "20%"}
											],
										columnDefs: [
											{ type: 'de_date', targets: 3 }
										]
									});
								});
						</script>
						<div class="col-md-8 col-xs-12">
							<table id="tabela_prosjek_uplate" class="display">
								<thead>
									<tr>
										<th>Kandidat</th>
										<th>Agent</th>
										<th class="text-center">Naziv predračuna</th>
										<th class="text-center" >Datum kreiranja ugovora</th>
										<th class="text-center" >Status</th>
										<th>Vrsta ugovora</th>
									</tr>
								</thead>
								<tbody>
								<?php
								while($row_prosjek_uplate = $query_prosjek_uplate->fetch()){
									
									$id_zaposlenik=$row_prosjek_uplate['pr_zaposlenik'];
									$id_kan=$row_prosjek_uplate['pr_kandidat_id'];
									$status_predr = $row_prosjek_uplate['pr_status'];
									$pr_file = $row_prosjek_uplate['pr_file'];
									$ime_kandidata = $row_prosjek_uplate['ime'];
									$prezime_kandidata = $row_prosjek_uplate['prezime'];
									$ime_zaposlenika = $row_prosjek_uplate['employee_firstname'];
									$prezime_zaposlenika = $row_prosjek_uplate['employee_lastname'];
									$br_predracuna = $row_prosjek_uplate['pr_broj_predracuna'];
									$datum_kreiranja = substr($row_prosjek_uplate['pr_datum_kreiranja'],0,10);
									$vrsta_ugovora_kandidata_ispis_otvoren = $row_prosjek_uplate['vrsta_ugovora_nd_kandidata'];
									$datum_uplate = $row_prosjek_uplate['pr_datum_uplate'];
									$datum_kreiranja = date("d.m.Y",strtotime($datum_kreiranja));
									// $datum_uplate = date("d.m.Y",strtotime($datum_uplate));
								
								?>
								
									<tr>
										<td><a target="_blank" href="<?php getSiteURL(); ?>nostrifikacija_diploma?page=otvori_ND_kandidata&id=<?php echo $id_kan;?>"><?php echo $ime_kandidata." ".$prezime_kandidata;?></a></td>
										<td><?php echo $ime_zaposlenika.' '.$prezime_zaposlenika;?></td>	
										<td class="text-center"><?php echo $br_predracuna;?></td>
										<td class="text-center"><?php echo $datum_kreiranja;?></td>

										
									<?php
									
										if($status_predr == 0){
											$status_show = '<td class="text-center"><span class="label label-warning material-label material-label_warning main-container__column ">Arhiviran</span></td>';
										}else if($status_predr == 1){
											$status_show = '<td class="text-center"><span class="label label-info material-label material-label_info main-container__column">Poslan</span></td>';
										}else if($status_predr == 2){
											// $status_show = '<td class="text-center" data-order="'.$datum_uplate.'"><span class="label label-success material-label material-label_success main-container__column">Uplaćen na: '.date("d.m.Y", strtotime($datum_uplate)).'</span></td>';
											$status_show = '<td class="text-center" data-order="'.$datum_uplate.'"><span class="label label-success material-label material-label_success main-container__column">Uplaćen na: '.date("d.m.Y", strtotime($datum_uplate)).'</span></td>';
										}else if($status_predr == 3){
											$status_show = '<td class="text-center"><span class="label label-danger material-label material-label_danger main-container__column">In Caso 1</span></td>';
										}else if($status_predr == 4){
											$status_show = '<td class="text-center"><span class="label label-danger material-label material-label_danger main-container__column">In Caso 2</span></td>';
										}else if($status_predr == 5){
											$status_show = '<td class="text-center"><span class="label label-danger material-label material-label_danger main-container__column">In Caso 3</span></td>';
										}

										echo $status_show;
										?>
										<td class="text-center">
										<?php
										if($vrsta_ugovora_kandidata_ispis_otvoren == 1){
											echo 'Ugovor bez popusta na 2 rate!';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 2){
											echo 'Ugovor sa popustom na 2 rate!';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 3){
											echo 'Ugovor bez popusta na 5 rata!';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 4){
											echo 'Ugovor sa popustom na 5 rata!';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 5){
											echo 'Ugovor bez popusta na 3 rate!';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 6){
											echo 'Ugovor sa popustom na 3 rate!';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 7){
											echo 'Ugovor bez popusta na 4 rate!';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 8){ 
											echo 'Ugovor sa popustom na 4 rate!';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 9){ 
											echo 'Ugovor bez popusta na 1 ratu!';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 10){ 
											echo 'Ugovor sa popustom na 1 ratu!';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 11){ 
											echo 'Mikrofin ugovor bez popusta!';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 12){ 
											echo 'Mikrofin ugovor sa popustom!';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 21){ 
											echo 'Ugovor sa 20% popusta na 1 ratu!';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 22){ 
											echo 'Ugovor sa 20% popusta na 2 rate!';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 23){ 
											echo 'Ugovor sa 20% popusta na 3 rate!';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 24){ 
											echo 'Ugovor sa 20% popusta na 4 rate!';
										}
										else if($vrsta_ugovora_kandidata_ispis_otvoren == 25){ 
											echo 'Ugovor sa 20% popusta na 5 rata!';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 71){ 
											echo 'Ugovor za struke na 1 ratu sa 30% popusta!';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 72){ 
											echo 'Ugovor za struke na 2 rate sa 30% popusta!';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 73){ 
											echo 'Ugovor za struke na 3 rate sa 30% popusta!';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 74){ 
											echo 'Ugovor za struke na 4 rate sa 30% popusta!';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 75){ 
											echo 'Ugovor za struke na 5 rata sa 30% popusta!';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 51){ 
											echo 'Ugovor za struke na 1 ratu sa 50% popusta!';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 52){ 
											echo 'Ugovor za struke na 2 rate sa 50% popusta!';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 53){ 
											echo 'Ugovor za struke na 3 rate sa 50% popusta!';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 54){ 
											echo 'Ugovor za struke na 4 rate sa 50% popusta!';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 55){ 
											echo 'Ugovor za struke na 5 rata sa 50% popusta!';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 99){ 
											echo 'Ugovor za struke na 1 ratu sa 100% popusta!';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 61){ 
											echo 'Ugovor za struke na 1 ratu sa 20% popusta!';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 62){ 
											echo 'Ugovor za struke na 2 rate sa 20% popusta!';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 63){ 
											echo 'Ugovor za struke na 3 rate sa 20% popusta!';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 64){ 
											echo 'Ugovor za struke na 4 rate sa 20% popusta!';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 65){ 
											echo 'Ugovor za struke na 5 rata sa 20% popusta!';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 41){ 
											echo 'Ugovor za struke na 1 ratu sa 70% popusta!';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 42){ 
											echo 'Ugovor za struke na 2 rate sa 70% popusta!';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 43){ 
											echo 'Ugovor za struke na 3 rate sa 70% popusta!';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 44){ 
											echo 'Ugovor za struke na 4 rate sa 70% popusta!';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 45){ 
											echo 'Ugovor za struke na 5 rata sa 70% popusta!';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 82){ 
											echo 'Ugovor sa popustom 10% na 2 rate!';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 83){ 
											echo 'Ugovor sa popustom 10% na 3 rate!';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 84){ 
											echo 'Ugovor sa popustom 10% na 4 rate!';
										}else if($vrsta_ugovora_kandidata_ispis_otvoren == 85){ 
											echo 'Ugovor sa popustom 10% na 5 rata!';
										}
										else{
											echo '';
										}
										?>
										</td>
										<?php

									?>
									</tr>
									<?php
								}
								?>
								
								</tbody>
								<tfoot>
								</tfoot>
							</table>
						</div>
						<div class="col-md-4 col-xs-12">
							<div id="selected_agents" style="display: none;"></div>
							<input id="h_dana" type="hidden" value="<?php echo $tip_statistike; ?>">
							<script>
							$(document).ready(function() { 
								var tabela_rang_lista = $('#rang_lista').DataTable({
									"searching":false,
									"bPaginate": false,
									"bLengthChange": false,
									"bFilter": false,
									"bInfo": false,
									responsive: true,
									"order": [[ 3, "desc" ]],
									paging: true,
									pageLength: 10,
									"bAutoWidth": false,

									'columnDefs': [
													{
													'targets': 0,
													'checkboxes': {
														'selectRow': true
														}
													}
										],

									"aoColumns": [
											{ "width": "6%"},
											{ "width": "30%"},
											{ "width": "47%"},
											{ "width": "17%"}
										]
								});
								
								$("#rang_lista").on('change',"input[type='checkbox']",function(e){	
																	
									$("#selected_agents .checkbox").remove();
									var rows_selected = tabela_rang_lista.column(0).checkboxes.selected();
									var tmp = [];
									// Iterate over all selected checkboxes
									$.each(rows_selected, function(index, rowId){
										if($.inArray(rowId, tmp)=== -1){
											tmp.push(rowId);
											$("#selected_agents").append(
												rowId
											);
										}
										
									});
																		
									var selectedIds = $('#selected_agents').find(".checkbox").map(function(){return $(this).val(); }).get();
									var dana = $("#h_dana").val();
									var	tip = "UP"
									
									$.ajax({
										url: 'ajax_data.php?page=refresh_list_kandidati',
										type: 'POST',
										data: {'selected':selectedIds, 'dana':dana, 'tip':tip},
										dataType: 'json',
										success: function(json) {
											
											var arr = json;
											var table = $('#tabela_prosjek_uplate').DataTable();
											
											table.clear().draw();
											table.destroy();
											
											var table = $('#tabela_prosjek_uplate').DataTable({

												responsive: true,

												"bAutoWidth": false,
												
											columnDefs: [
												{ type: 'de_date', targets: 3 },
												{ type: 'de_date', targets: 4 }
											]
											})
											for(var i = 0; i < arr['kandidat'].length; i++){
												
												$('#tabela_prosjek_uplate').dataTable().fnAddData([
															   arr['kandidat'][i],
															   arr['agent'][i],
															   arr['naziv_predracuna'][i],
															   arr['datum_kreiranja'][i],
															   arr['status'][i],
															   arr['vrsta_ugovora'][i]
															  ]);
											}

											// for(var i = 0; i < arr['datum_uplate'].length; i++){
												// $( "#tabela_prosjek_uplate td:eq( "+(4+(6*i))+" )" ).attr( "data-order", arr['datum_uplate'][i]);
												// console.log(4+(6*i));
												// console.log($("td:eq( "+(4+(6*i))+" )" ).html());
											// }

										},
										error: function (xhr, ajaxOptions, thrownError) {
											alert(xhr.status);
											alert(thrownError);
										}
									});
									
									
		
								});
								$('#export_lista_uplata').on("click",function(e) {
									$("#selected_agents .checkbox").remove();
									var tip_stat = $("#tip_stat_h").val();
									var rows_selected = tabela_rang_lista.column(0).checkboxes.selected();
									var tmp = [];
									$.each(rows_selected, function(index, rowId){
										if($.inArray(rowId, tmp)=== -1){
											tmp.push(rowId);
											$("#selected_agents").append(
												rowId
											);
										}
										
									});
									
									var selectedIds = $('#selected_agents').find(".checkbox").map(function(){return $(this).val(); }).get();
									var query = new URLSearchParams();
									query.append('prozor','export_lista_uplata');
									query.append('tip_stat',tip_stat);
									query.append('selected',selectedIds);
									url = "export_excel.php?" + query.toString();

									$('#export_predracuni_perioda_div').load(url);
									
								
									return false;
									e.preventDefault();
								});
							});
							
							</script>
							<table id="rang_lista" class="display" style="border-left: 3px solid;padding-left: 35px;">
								<thead>
									<th><input type="checkbox" id="select_all" name="select_all"></th>
									<th class="text-center">Agent</th>
									<th class="text-center">Poslovnica</th>
									<th class="text-center">Uplaćenih predračuna</th>
								</thead>
								<tbody>
								<?php 
									
									
									$query_rang_lista = $db->prepare("
										SELECT count(emp.employee_id) as cnt, emp.employee_firstname, emp.employee_lastname, employee_id, pos.branch_name, pos.branch_state
										FROM idk_predracuni pred 
										JOIN idk_nd_kandidata kan 
										ON pred.pr_kandidat_id = kan.id_broj_nd_kandidata 
										JOIN idk_employees emp 
										ON emp.employee_id = pred.pr_zaposlenik
										JOIN idk_poslovnice pos
										ON pos.branch_id = emp.employee_poslovnica
										
										WHERE date(pr_datum_uplate) BETWEEN date(curdate() - interval ".$tip_statistike." day) and date(curdate() - interval 1 day) 
										AND pr_rata = 1 
										AND pr_status != 0 
										AND pr_vrsta_predracuna = 1 
										AND pr_uplaceno = 1
										GROUP BY (emp.employee_id) 

										UNION
										
										SELECT 0 as cnt, emp.employee_firstname as employee_firstname, emp.employee_lastname, emp.employee_id, pos.branch_name, pos.branch_state
										FROM idk_employees emp
										JOIN idk_poslovnice pos
										ON pos.branch_id = emp.employee_poslovnica
										WHERE employee_status LIKE '%15%'
										
                                        AND emp.employee_id NOT IN (
                                        	SELECT  emp.employee_id
											FROM idk_predracuni pred 
											JOIN idk_nd_kandidata kan 
											ON pred.pr_kandidat_id = kan.id_broj_nd_kandidata 
											JOIN idk_employees emp 
											ON emp.employee_id = pred.pr_zaposlenik
											JOIN idk_poslovnice pos
											ON pos.branch_id = emp.employee_poslovnica
										
											WHERE date(pr_datum_uplate) BETWEEN date(curdate() - interval ".$tip_statistike." day) and date(curdate() - interval 1 day) 
											AND pr_rata = 1 
											AND pr_status != 0 
											AND pr_vrsta_predracuna = 1 
											AND pr_uplaceno = 1
											GROUP BY (emp.employee_id) 
                                        )

										ORDER BY cnt desc




									");
									
								
									
									$query_rang_lista -> execute();
									$ukUpl=0;
									while ($row_rang_lista = $query_rang_lista -> fetch()){
										
										$agent = $row_rang_lista['employee_firstname'].' '.$row_rang_lista['employee_lastname'];
										$broj_uplata = $row_rang_lista['cnt'];
										$agent_id = $row_rang_lista['employee_id'];
										$agent_poslovnica = $row_rang_lista['branch_name'];
										$poslovnica_state = $row_rang_lista['branch_state'];
										
										if($poslovnica_state == "BiH"){
											$poslovnica_state = "images/bs3d.png";											
										}
										else if($poslovnica_state == "Njemačka"){
											$poslovnica_state = "images/de3d.png";
										}
										else if($poslovnica_state == "Srbija"){
											$poslovnica_state = "images/sr3d.png";
										}
										else{
											$poslovnica_state = "images/globe3d.png";
										}
										?>
										<tr>
											<td class="text-center"><input class="checkbox" type="checkbox" name="selectedrows[<?php echo $agent_id; ?>]" value="<?php echo $agent_id; ?> "></td>
											<td class="text-center"><?php echo $agent; ?></td>
											<td class="text-center"><img src="<?php echo $poslovnica_state; ?>" width="25"></td>
											<td class="text-center"><b><?php echo $broj_uplata; ?></b></td>
										</tr>
										
										<?php
										$ukUpl = $ukUpl + $broj_uplata;
									}
								?>
								</tbody>
								<tfoot>
										<td></td>
										<td></td>
										<td class="text-center"><b>UKUPNO:</b></td>
										<td class="text-center"><b><?php echo $ukUpl; ?></b></td>
								</tfoot>
							</table>
							<input type="hidden" id="tip_stat_h" value="<?php echo $tip_statistike; ?>">
							<div id="export_predracuni_perioda_div" ></div>
						</div>
					</div>
				</div>
			</div>	
			<?php
			break;
		}
			?>
		</div>
	</div>
	<script>
		$(document).ready(function() {
			$('#testich').on("click",function(e) {
				var q1 = document.getElementById('q1').value;
				var q2 = document.getElementById('q2').value;
				var id_men = document.getElementById('id_men').value;

				var query = new URLSearchParams();
				query.append('prozor','export_predracuni_perioda');
				query.append('q1',q1);
			 	query.append('q2',q2);
				query.append('id_men',id_men);
				url = "export_excel.php?" + query.toString();
	
 	
	
			
				$('#export_predracuni_perioda_div').load(url);
				
			
				return false;
				e.preventDefault();
			});
		});				
	</script>	
	<div id="export_predracuni_perioda_div" ></div>

	<script type="text/javascript">
			jQuery.extend( jQuery.fn.dataTableExt.oSort, {
			"de_datetime-asc": function ( a, b ) {
				var x, y;
				if (jQuery.trim(a) !== '') {
					var deDatea = jQuery.trim(a).split(' ');
					var deTimea = deDatea[1].split(':');
					var deDatea2 = deDatea[0].split('.');
								if(typeof deTimea[2] != 'undefined') {
									x = (deDatea2[2] + deDatea2[1] + deDatea2[0] + deTimea[0] + deTimea[1] + deTimea[2]) * 1;
								} else {
									x = (deDatea2[2] + deDatea2[1] + deDatea2[0] + deTimea[0] + deTimea[1]) * 1;
								}
				} else {
					x = -Infinity; // = l'an 1000 ...
				}
		
				if (jQuery.trim(b) !== '') {
					var deDateb = jQuery.trim(b).split(' ');
					var deTimeb = deDateb[1].split(':');
					deDateb = deDateb[0].split('.');
								if(typeof deTimeb[2] != 'undefined') {
									y = (deDateb[2] + deDateb[1] + deDateb[0] + deTimeb[0] + deTimeb[1] + deTimeb[2]) * 1;
								} else {
									y = (deDateb[2] + deDateb[1] + deDateb[0] + deTimeb[0] + deTimeb[1]) * 1;
								}
				} else {
					y = -Infinity;
				}
				var z = ((x < y) ? -1 : ((x > y) ? 1 : 0));
				return z;
			},
		
			
			"de_date-asc": function ( a_tmp, b_tmp ) {
				
				if(a_tmp.startsWith("<div")){
					var a_tmp = a_tmp.substring(
						a_tmp.lastIndexOf(":") + 2, 
						a_tmp.lastIndexOf("</span>")
					);			
				
					var b_tmp = b_tmp.substring(
						b_tmp.lastIndexOf(":") + 2, 
						b_tmp.lastIndexOf("</span>")
					);
				}

				
				a = a_tmp;
				b = b_tmp;

				var x, y;
				if (jQuery.trim(a) !== '') {
					var deDatea = jQuery.trim(a).split('.');
					x = (deDatea[2] + deDatea[1] + deDatea[0]) * 1;
				} else {
					x = Infinity; // = l'an 1000 ...
				}
		
				if (jQuery.trim(b) !== '') {
					var deDateb = jQuery.trim(b).split('.');
					y = (deDateb[2] + deDateb[1] + deDateb[0]) * 1;
				} else {
					y = -Infinity;
				}
				var z = ((x < y) ? -1 : ((x > y) ? 1 : 0));
				return z;
			},
		
			"de_date-desc": function ( a_tmp, b_tmp ) {
				
				//POSEBAN SLUCAJ KOD ISPISA PREDRACUNA I RACUNA uplacenih NA x DATUM, vadi datum iz stringa 
				if(a_tmp.startsWith("<div")){
					var a_tmp = a_tmp.substring(
						a_tmp.lastIndexOf(":") + 2, 
						a_tmp.lastIndexOf("</span>")
					);			
				
					var b_tmp = b_tmp.substring(
						b_tmp.lastIndexOf(":") + 2, 
						b_tmp.lastIndexOf("</span>")
					);
				}

				
				a = a_tmp;
				b = b_tmp;

				//KRAJ
				var x, y;
				if (jQuery.trim(a) !== '') {
					var deDatea = jQuery.trim(a).split('.');
					x = (deDatea[2] + deDatea[1] + deDatea[0]) * 1;
				} else {
					x = -Infinity;
				}
		
				if (jQuery.trim(b) !== '') {
					var deDateb = jQuery.trim(b).split('.');
					y = (deDateb[2] + deDateb[1] + deDateb[0]) * 1;
				} else {
					y = Infinity;
				}
				var z = ((x < y) ? 1 : ((x > y) ? -1 : 0));
				return z;
			}
		} );
		$(document).ready(function() {
			$('#tabela_predracuni').DataTable({
				responsive: true,
				"order": [[ 4, "desc" ]],
				"bAutoWidth": true,
				"pageLength": 10,
				columnDefs: [
					{ type: 'de_date', targets: 3 }
				]
			});
		});	

		</script>
</body>
</html>
<?php }else{			
			echo '
				<br/>
				<div class="alert material-alert material-alert_danger">
					<h4>NEMATE PRIVILEGIJE!</h4>
					<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
					<br />
				</div>
';} ?>
