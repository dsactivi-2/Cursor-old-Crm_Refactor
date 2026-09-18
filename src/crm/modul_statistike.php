<?php
include("includes/functions.php");
include("includes/common.php");

$team_id = getLoggedEmployeeTeam();

if(isset($_REQUEST["page"])) {
	$page = $_REQUEST["page"];
}else{
	header("Location: modul_statistike?page=open");
}


?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Statistike | <?php getTitle(); ?></title>

	<?php include('includes/head.php'); ?>

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

				case "open":
				?>
				<style>
					.panel-danger {
						border-color: #f3413c;
					}
					.panel-danger>.panel-heading {
						/* color: #a94442; */
						background-color: #f3413c;
						border-color: #f3413c;
					}
					.panel-title {
						font-size: 20px;
						color: white;
					}
					.dugme{
						align-items: center;
						background: linear-gradient(-45deg, rgba(0,0,0,0.22), rgba(255,255,255,0.25));
						box-shadow: 12px 12px 16px 0 rgba(0, 0, 0, 0.25),
						-8px -8px 12px 0 rgba(255, 255, 255, 0.3);
						border-radius: 50px;
						justify-content: center;
						padding: 20px;
						width: 50%;
					}
					.broj_type{
						font-size: large;
						font-weight: bold;
						color: #686868;
					}
					.icon{
						font-size: xxx-large;
						color: #686868;
						margin-bottom: 10px;
					}
					

				</style>
				<div class="row">
					<div class="col-xs-12 text-left">
						<h1><i class="fa fa-university idk_color_green" style = "padding-right: 15px;" aria-hidden="true"></i> DIPL statistike </h1>
					</div>
				</div>
				<hr />
				<div class="row">
					<div class="col-md-12">
						<div class="content_box">
							<div class="row">
								<div class="col-xs-12">
									<div class="row">
										<div class="col-md-offset-2 col-md-8 text-center idk_margin_top20">
											<div class="panel panel-danger">
												<div class="panel-heading">
													<h3 class="panel-title text-center"><b><i class="fa fa-credit-card" style = "padding-right: 15px;" aria-hidden="true"></i> Inkaso</b></h3>
												</div>
												<div class="panel-body">
													<div class="row">
														<div style = "margin: 15px 0px;" class="col-md-4 col-xs-12 text-center">
															<a href="<?php getSiteURL(); ?>modul_statistike?page=inkaso_odjel_stat" target="_BLANK">
																<button class="btn btn material-btn material dugme">
																	<i class="icon fa fa-user-circle-o" aria-hidden="true"></i><br>
																	<span class="broj_type">Agenti</span>
																</button>
															</a>
														</div>
														<div style = "margin: 15px 0px;"  class="col-md-4 col-xs-12 text-center">
															<a href="<?php getSiteURL(); ?>modul_statistike?page=inkaso_kandidati&drzava=0" target="_BLANK">
																<button class="btn btn material-btn material dugme">
																	<i class="icon fa fa-calendar-o" aria-hidden="true"></i><br>
																	<span class="broj_type">Statusi</span>
																</button>
															</a>
														</div>
														<div style = "margin: 15px 0px;"  class="col-md-4 col-xs-12 text-center">
															<a href="<?php getSiteURL(); ?>modul_statistike?page=inkaso_pregled" target="_BLANK">
																<button class="btn btn material-btn material dugme">
																	<i class="icon fa fa-commenting-o" aria-hidden="true"></i><br>
																	<span class="broj_type">Komunikacije</span>
																</button>
															</a>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
				break;
				case "inkaso_odjel_stat":
					?>
					<style>
					.a_link{
						color: #000000 !important;
					}
					.a_link:hover{
						color: rgb(80 255 0) !important;
						text-decoration: none !important;
					}
					.a_h_link{
						color: #038103 !important;
					}
					.a_h_link:hover{
						color: rgb(80 255 0) !important;
						text-decoration: none !important;
					}
					.td_highlight:hover{
						color: white !important;
						cursor: pointer;
						background-color:black;
						font-weight:bold;
						border-radius: 25px;
					}
					.td_clicked{
						color: black !important;
						cursor: pointer;
						background-color: #a8a8a8;
						font-weight: bold;
						border-radius: 25px;
					}
					</style>
					<div class="row">
						<div class="col-xs-12">
							<h1><i class="fa fa-user-circle-o idk_color_green" aria-hidden="true"></i> Statistika inkaso agenti </h1>
						</div>
						<div class="col-xs-12">
							<hr/>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<div class="content_box">
								<div class="row">
									<div class="col-lg-3 col-sm-12" style = "float: left;"> 
										<label for="period">Vremenski period: </label>
										<input type="text" class="form-control" name="period" id="period" placeholder="Datum" style="padding:17px;border-radius:0;">
									</div>
									<div class="col-lg-6 col-sm-12" style ="float:left;">
										<label for="odabrani_agenti">Agent: </label>
										<select id="odabrani_agenti" class="selectpicker" name="odabrani_agenti" multiple>
										<?php
											$query_get_agente = $db -> prepare("
												SELECT employee_id, employee_firstname, employee_lastname
												FROM idk_employees
												WHERE employee_status like '%14%'
											");
											
											$query_get_agente -> execute();
											
											
										?>
										<?php
										while($row_get_agente = $query_get_agente -> fetch()){
											
											$id_agent = $row_get_agente['employee_id'];
											$ime = $row_get_agente['employee_firstname'];
											$prezime = $row_get_agente['employee_lastname'];
											echo '<option value = "'.$id_agent.'" selected>'.$ime.' '.$prezime.'</option>';
											
										}
										?>
										</select>
									</div>
								</div>
								<hr>
								<div class = "row">
									<div id="to_append_to">
									</div>
								</div>
							</div>  
						</div>
					</div>
					<script>
						function getTable(){
							var period = $('#period').val();
							var odabrani_agenti = $('#odabrani_agenti').val();
							$.ajax({
								url: 'ajax_data.php?page=get_inkaso_statistika',
								type: 'POST',
								data: {	
									'period':period, 
									'odabrani_agenti':odabrani_agenti
								},
								dataType: 'html',
								success: function(html) {
									$("#to_append_to").empty();
									$("#to_append_to").append(html);
									$('#inkaso_statistika').DataTable({
										responsive: true,
										"bAutoWidth": true,
										"pageLength": 10,
										bFilter: false, 
										bInfo: false,
										bPaginate: false,
										"columnDefs": [
										  { "width": "11%", "targets": 0 },
										  { "width": "12%", "targets": 1 },
										  { "width": "11%", "targets": 2 },
										  { "width": "11%", "targets": 3 },
										  { "width": "11%", "targets": 4 },
										  { "width": "11%", "targets": 5 },
										  { "width": "11%", "targets": 6 },
										  { "width": "11%", "targets": 7 },
										  { "width": "11%", "targets": 8 }
										],							        
									});
									$(".td_highlight").on('click',function(){
										var employee_id = $(this).attr('employee_id');
										var tip = $(this).attr('tip');
										var period = $('#period').val();
										$(this).addClass('td_clicked');
										window.open('modul_statistike?page=inkaso_list_kandidate&period='+period+'&employee_id='+employee_id+'&tip='+tip); 
									});
								},
								error: function (xhr, ajaxOptions, thrownError) {
									alert(xhr.status);
									alert(thrownError);
								}
							});
						}
						$(document).ready(function() {
							getTable();
						});
						$("#period").flatpickr({
							mode: "range",
							dateFormat: "d.m.Y",
							disableMobile: "true",
							minDate: "06.04.2021",
							defaultDate: ["06.04.2021", new Date()]
						});	
						$('#period, #odabrani_agenti').on('change', function(){
							getTable();
						});
						
					</script>
					<?php

				break;
				
				case "inkaso_list_kandidate":
					$employee_id = $_GET['employee_id'];
					$period 	 = $_GET['period'];
					$tip 		 = $_GET['tip'];		//tip ide od 1-8 u skladu sa clickable kolonama u inkaso odjel stat (ajax_data.php case: get_inkaso_statistika)
					if(strpos($period, ' to ') !== false) {
						$period = explode (' to ', $period);
						$datum_od = date("Y-m-d",strtotime($period[0]));
						$datum_do = date("Y-m-d", strtotime($period[1].'+1 day'));
					}
					else{
						$datum_od = date("Y-m-d",strtotime($period));
						$datum_do = date("Y-m-d", strtotime($period.'+1 day'));
					}
					
					$naslov_text          		= "";
					$naslov_text_employee		= "";
					$query_get_max_3_5_biljeske = "";
					if($employee_id != 0){
						$uslov_employee = " AND bilj.dodao_zaposlenik_biljeska_nd= ".$employee_id." AND emp.employee_status LIKE '%14%'";
						$naslov_text_employee = "zaposlenika ".getEmployeeFullnameById($employee_id)." u periodu od ".date("d.m.Y", strtotime($datum_od))." do ".date("d.m.Y", strtotime($datum_do));
					}
					else{
						$uslov_employee = " AND emp.employee_status LIKE '%14%'";
						$naslov_text_employee = "zaposlenika svih zaposlenika u periodu od ".date("d.m.Y", strtotime($datum_od))." do ".date("d.m.Y", strtotime($datum_do));
					}
					
					$uslov_period = " WHERE bilj.vrijeme_dodavanja_biljeska_nd BETWEEN '".$datum_od."' AND '".$datum_do."'";
					
					$query_get_inkaso_list_predracuna_komunikacija = "
						SELECT pred.pr_broj_predracuna, bilj.id_kandidata_biljeska_nd, pred.pr_datum_kreiranja, emp.employee_firstname, emp.employee_lastname,
						pred.pr_status, pred.pr_datum_uplate, pred.pr_vrsta_predracuna, pred.pr_rata, kan.id_broj_nd_kandidata, kan.ime_nd_kandidata, kan.prezime_nd_kandidata, kan.vrsta_ugovora_nd_kandidata
						FROM idk_nd_kandidata_biljeske bilj
						JOIN idk_predracuni pred
						ON pred.pr_id = bilj.predracun_id
						JOIN idk_nd_kandidata kan
						ON kan.id_broj_nd_kandidata = bilj.id_kandidata_biljeska_nd
						JOIN idk_employees emp
						ON emp.employee_id = bilj.dodao_zaposlenik_biljeska_nd
						
						".$uslov_period."
						".$uslov_employee."
						AND emp.employee_status != 0
					";
					
					if($tip == 2){// SVE KOMUNIKACIJE
						$uslov_tip = "AND status_biljeska_nd = 3";
						$query_get_inkaso_list_predracuna_komunikacija .= $uslov_tip;
						$naslov_text = "Pozivi ";
					}
					else if($tip == 3){ // NE JAVLJA SE
						$uslov_tip = " AND tip_biljeska_nd = 2 AND status_biljeska_nd = 3";
						$query_get_inkaso_list_predracuna_komunikacija .= $uslov_tip;
						$naslov_text = "Pozivi tipa ne javlja se ";
					}
					else if($tip == 4){ // POZOVI KASNIJE
						$uslov_tip = " AND tip_biljeska_nd = 3 AND status_biljeska_nd = 3";
						$query_get_inkaso_list_predracuna_komunikacija .= $uslov_tip;
						$naslov_text = "Pozivi tipa pozovi kasnije ";
					}
					else if($tip == 5){ // UPLATA NA DAN
						$uslov_tip = " AND tip_biljeska_nd = 5 AND status_biljeska_nd = 3";
						$query_get_inkaso_list_predracuna_komunikacija .= $uslov_tip;
						$naslov_text = "Pozivi tipa uplata na dan ";
					}
					else if($tip == 6){ // PROMJENA UGOVORA
						$uslov_tip = " AND tip_biljeska_nd = 6 AND status_biljeska_nd = 3";
						$query_get_inkaso_list_predracuna_komunikacija .= $uslov_tip;
						$naslov_text = "Promjenjeni ugovori ";
					}
					else if($tip == 7){ // ARHIVIRANI
						$uslov_tip = " AND tip_biljeska_nd = 4 AND status_biljeska_nd = 3";
						$query_get_inkaso_list_predracuna_komunikacija .= $uslov_tip;
						$naslov_text = "Arhivirani predračuni ";
					}
					else if($tip == 8){ // BROJ UPLATA
						$query_get_max_3_5_biljeske = $db->prepare("
							SELECT max(bilj.id_biljeska_nd) as max
							FROM idk_nd_kandidata_biljeske bilj
							WHERE bilj.status_biljeska_nd = 3
							AND bilj.tip_biljeska_nd = 5
							GROUP BY(bilj.predracun_id)
						");
						$query_get_max_3_5_biljeske -> execute();
						$array_max_3_5_biljeske = array();
						while($row_get_max_3_5_biljeske = $query_get_max_3_5_biljeske -> fetch()){
							array_push($array_max_3_5_biljeske, $row_get_max_3_5_biljeske['max']);
						}
						$string_max_3_5_biljeske = implode(',',$array_max_3_5_biljeske);
					
						$uslov_tip = " 
							AND tip_biljeska_nd = 5 
							AND status_biljeska_nd = 3 
							AND pred.pr_uplaceno = 1
							AND bilj.id_biljeska_nd IN (
								".$string_max_3_5_biljeske."
							)
						";
						$query_get_inkaso_list_predracuna_komunikacija .= $uslov_tip;
						$naslov_text = "Uplate ";
					}
					else if($tip == 1){ // STATUS PREDRAČUNA KOJI SU BILI ILI SU U INKASU
						$query_get_max_biljeske = $db->prepare("
							SELECT max(bilj.id_biljeska_nd) as max
							FROM idk_nd_kandidata_biljeske bilj
							JOIN idk_employees emp
							ON emp.employee_id = bilj.dodao_zaposlenik_biljeska_nd
							".$uslov_period."
							".$uslov_employee."
							AND bilj.status_biljeska_nd = 3
							GROUP BY(bilj.predracun_id)
						");
						$query_get_max_biljeske -> execute();
						$array_max_biljeske = array();
						while($row_get_max_biljeske = $query_get_max_biljeske->fetch()){
							array_push($array_max_biljeske, $row_get_max_biljeske['max']);
						}
						$uslov_max_biljeske = implode(',',$array_max_biljeske);

						$uslov_tip = " WHERE bilj.id_biljeska_nd IN (
										".$uslov_max_biljeske."
									 )";
									 
						$query_get_inkaso_list_predracuna_komunikacija = "
							SELECT pred.pr_broj_predracuna, bilj.id_kandidata_biljeska_nd, pred.pr_datum_kreiranja, emp.employee_firstname, emp.employee_lastname,
							pred.pr_status, pred.pr_datum_uplate, pred.pr_vrsta_predracuna, pred.pr_rata, kan.id_broj_nd_kandidata, kan.ime_nd_kandidata, kan.prezime_nd_kandidata, kan.vrsta_ugovora_nd_kandidata
							FROM idk_nd_kandidata_biljeske bilj
							JOIN idk_predracuni pred
							ON pred.pr_id = bilj.predracun_id
							JOIN idk_nd_kandidata kan
							ON kan.id_broj_nd_kandidata = bilj.id_kandidata_biljeska_nd
							JOIN idk_employees emp
							ON emp.employee_id = bilj.dodao_zaposlenik_biljeska_nd
							".$uslov_tip."
							AND emp.employee_status != 0
						";
						$naslov_text = "Prozvani predračuni ";
					}
					$query_get_inkaso_list_predracuna_komunikacija = $db->prepare($query_get_inkaso_list_predracuna_komunikacija);
					$query_get_inkaso_list_predracuna_komunikacija -> execute();
;
					

					?>
					<div class="row">
						<div class="col-xs-12">
							<h1><i class="fa fa-user-circle-o idk_color_green" aria-hidden="true"></i> Statistika inkaso agenti </h1>
						</div>
						<div class="col-xs-12">
							<hr/>
						</div>
					</div>
					<div class="content_box" style="min-height: 100px;">
						<div class="row">
							<div class="col-md-12">
								<h1><?php echo $naslov_text." ".$naslov_text_employee; ?></h1>
								<hr><br>
								<table id="tabela_inkaso_list_predracuni_komunikacije" class="table-striped" style="width:100%">
									<thead>
										<tr>
											<th>Ime i prezime kandidata</th>
											<?php
											if($employee_id == 0 && $tip != 1)
												echo "<th>Ime i prezime agenta</th>";
											?>
											<th class="text-center">Naziv predračuna</th>
											<th class="text-center" >Datum kreiranja ugovora</th>
											<th class="text-center" >Status</th>
											<th>Vrsta ugovora</th>
											<th>Rata</th>
										</tr>
									</thead>
								<tbody>

							<?php
								
								while ($row_get_inkaso_list_predracuna_komunikacija = $query_get_inkaso_list_predracuna_komunikacija->fetch()){
									$pr_broj_predracuna 		= $row_get_inkaso_list_predracuna_komunikacija['pr_broj_predracuna'];
									$pr_datum_kreiranja 		= $row_get_inkaso_list_predracuna_komunikacija['pr_datum_kreiranja'];
									$id_kandidata_biljeska_nd	= $row_get_inkaso_list_predracuna_komunikacija['id_kandidata_biljeska_nd'];
									$employee_firstname 		= $row_get_inkaso_list_predracuna_komunikacija['employee_firstname'];
									$employee_lastname 			= $row_get_inkaso_list_predracuna_komunikacija['employee_lastname'];
									$pr_status 					= $row_get_inkaso_list_predracuna_komunikacija['pr_status'];
									$pr_datum_uplate 			= $row_get_inkaso_list_predracuna_komunikacija['pr_datum_uplate'];
									$pr_vrsta_predracuna 		= $row_get_inkaso_list_predracuna_komunikacija['pr_vrsta_predracuna'];
									$vrsta_ugovora_nd_kandidata = $row_get_inkaso_list_predracuna_komunikacija['vrsta_ugovora_nd_kandidata'];
									$pr_rata 					= $row_get_inkaso_list_predracuna_komunikacija['pr_rata'];
									$ime_nd_kandidata 			= $row_get_inkaso_list_predracuna_komunikacija['ime_nd_kandidata'];
									$prezime_nd_kandidata 		= $row_get_inkaso_list_predracuna_komunikacija['prezime_nd_kandidata'];
									?>
											<tr>
												<td><a target="_blank" href="<?php getSiteURL(); ?>nostrifikacija_diploma?page=otvori_ND_kandidata&id=<?php echo $id_kandidata_biljeska_nd;?>"><?php echo $ime_nd_kandidata." ".$prezime_nd_kandidata;?></a></td>
												<?php
													if($employee_id == 0 && $tip != 1){

														echo "<td>".$employee_firstname." ".$employee_lastname."</td>";			
													}
												?>
												<td class="text-center"><?php echo $pr_broj_predracuna;?></td>
												<td class="text-center"><?php echo $pr_datum_kreiranja;?></td>
												
											<?php
											
												if($pr_status == 0){
													$status_show = '<td class="text-center"><span class="label label-warning material-label material-label_warning main-container__column ">Arhiviran</span></td>';
												}else if($pr_status == 1){
													$status_show = '<td class="text-center"><span class="label label-info material-label material-label_info main-container__column">Poslan</span></td>';
												}else if($pr_status == 2){
													$status_show = '<td class="text-center"><span class="label label-success material-label material-label_success main-container__column">Uplaćen na: '.date("d.m.Y", strtotime($pr_datum_uplate)).'</span></td>';
												}else if($pr_status == 3){
													$status_show = '<td class="text-center"><span class="label label-danger material-label material-label_danger main-container__column">In Caso 1</span></td>';
												}else if($pr_status == 4){
													$status_show = '<td class="text-center"><span class="label label-danger material-label material-label_danger main-container__column">In Caso 2</span></td>';
												}else if($pr_status == 5){
													$status_show = '<td class="text-center"><span class="label label-danger material-label material-label_danger main-container__column">In Caso 3</span></td>';
												}

												echo $status_show;
												
											
												if($vrsta_ugovora_nd_kandidata == 1){
													echo '<td class="text-center">Ugovor bez popusta na 2 rate!</td>';
												}
												else if($vrsta_ugovora_nd_kandidata == 2){
													echo '<td class="text-center">Ugovor sa popustom na 2 rate!</td>';
												}
												else if($vrsta_ugovora_nd_kandidata == 3){
													echo '<td class="text-center">Ugovor bez popusta na 5 rata!</td>';
												}
												else if($vrsta_ugovora_nd_kandidata == 4){
													echo '<td class="text-center">Ugovor sa popustom na 5 rata!</td>';
												}
												else if($vrsta_ugovora_nd_kandidata == 5){
													echo '<td class="text-center">Ugovor bez popusta na 3 rate!</td>';
												}
												else if($vrsta_ugovora_nd_kandidata == 6){
													echo '<td class="text-center">Ugovor sa popustom na 3 rate!</td>';
												}
												else if($vrsta_ugovora_nd_kandidata == 7){
													echo '<td class="text-center">Ugovor bez popusta na 4 rate!</td>';
												}
												else if($vrsta_ugovora_nd_kandidata == 8){ 
													echo '<td class="text-center">Ugovor sa popustom na 4 rate!</td>';
												}
												else if($vrsta_ugovora_nd_kandidata == 9){ 
													echo '<td class="text-center">Ugovor bez popusta na 1 ratu!</td>';
												}
												else if($vrsta_ugovora_nd_kandidata == 10){ 
													echo '<td class="text-center">Ugovor sa popustom na 1 ratu!</td>';
												}
												else if($vrsta_ugovora_nd_kandidata == 11){ 
													echo '<td class="text-center">Mikrofin ugovor bez popusta!</td>';
												}
												else if($vrsta_ugovora_nd_kandidata == 12){ 
													echo '<td class="text-center">Mikrofin ugovor sa popustom!</td>';
												}
												else if($vrsta_ugovora_nd_kandidata == 21){ 
													echo '<td class="text-center">Ugovor sa 20% popusta na 1 ratu!</td>';
												}
												else if($vrsta_ugovora_nd_kandidata == 22){ 
													echo '<td class="text-center">Ugovor sa 20% popusta na 2 rate!</td>';
												}
												else if($vrsta_ugovora_nd_kandidata == 23){ 
													echo '<td class="text-center">Ugovor sa 20% popusta na 3 rate!</td>';
												}
												else if($vrsta_ugovora_nd_kandidata == 24){ 
													echo '<td class="text-center">Ugovor sa 20% popusta na 4 rate!</td>';
												}
												else if($vrsta_ugovora_nd_kandidata == 25){ 
													echo '<td class="text-center">Ugovor sa 20% popusta na 5 rata!</td>';
												}
												else{
													echo '<td></td>';
												}

										?>
										<td><?php echo $pr_rata; ?></td>
										</tr>
										<?php
									}
									?>
											
										</tbody>
								</table>
								<script>
								$(document).ready(function() { 
										var table = $('#tabela_inkaso_list_predracuni_komunikacije').DataTable({
										responsive: true,
										paging: true,
										pageLength: 10,
										"bAutoWidth": false,
										"search": {
											"regex": true,
										},
									});
								});
						</script>
							</div>
						</div>
					</div>
					<?php
				break;
				
				case "inkaso_kandidati":
						$drzava = $_GET["drzava"];
						if($drzava == "0"){
							$naslov = "- Sve države";
							$uslov = "pred.pr_domaca_valuta is not null";
						}else if($drzava == "BIH"){
							$naslov = "- BiH";
							$uslov = "pred.pr_domaca_valuta LIKE 'BAM'";
						}else if($drzava == "SRB"){
							$naslov = "- SRB";
							$uslov = "pred.pr_domaca_valuta LIKE 'RSD'";
						}
					?>
						<style>
							.panel-danger {
								border-color: #68c368;
							}
							.panel-danger>.panel-heading {
								/* color: #a94442; */
								background-color: #68c368;
								border-color: #68c368;
							}
							.panel-title {
								font-size: 20px;
								color: white;
							}
						</style>
						<div class="row">
							<div class="col-xs-12">
								<h1><i class="fa fa-calendar-o idk_color_green" aria-hidden="true"></i> Inkaso statusi </h1>
							</div>
							<div class="col-xs-12">
								<hr/>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="content_box">
									<div class="row" style = "margin-bottom: 25px;">
										<div class="col-xs-6">
										</div>
										<div class="col-xs-6">
											<div class="row">
												<form action="<?php getSiteUrl(); ?>modul_statistike" enctype="multipart/form-data" method="get" accept-charset="utf-8" role="form" class="form-horizontal">
													<input type="hidden" name="page" value="inkaso_kandidati">
													<?php 
														if(isset($_GET["period"])){
													?>
													<input type="hidden" name="period" value="<?php echo $_GET["period"]; ?>">
													<?php 
														}
													?>
													<div class="col-sm-8">
														<label for="drzava"></label>
														<select id="drzava" class="selectpicker" name="drzava" title = "Odaberi državu">
															
															<option value = "0" <?php if($_GET["drzava"] == "0"){echo "selected";}else{echo "";} ?>>Sve</option>
															<option value = "BIH" <?php if($_GET["drzava"] == "BIH"){echo "selected";}else{echo "";} ?>>BiH</option>
															<option value = "SRB" <?php if($_GET["drzava"] == "SRB"){echo "selected";}else{echo "";} ?>>SRB</option>
														</select>
													</div>
													
													<div class="col-sm-4 idk_margin_top20">
														<button style="width:100%" class="btn btn-success">Traži</button>
													</div>
												</form>
											</div>
										</div>
									</div>
									<div class="row">
									<?php 
										
										
										if($drzava == "0" OR $drzava == "BIH" OR $drzava == "SRB"){
										
											$type = array("1", "2", "3", "ukupno");
											foreach ($type as $typee){
												if($typee == "ukupno"){
													$type_uslov = "pred.pr_status IN (3,4,5)";
												}else if($typee == "1"){
													$type_uslov = "pred.pr_status = 3";
												}else if($typee == "2"){
													$type_uslov = "pred.pr_status = 4";
												}else {
													$type_uslov = "pred.pr_status = 5";
												}
												$query = $db->prepare("
													SELECT
														SUM(CASE WHEN pred.pr_rata = 1  THEN 1 ELSE 0 END) AS R1,
														SUM(CASE WHEN pred.pr_rata = 2  THEN 1 ELSE 0 END) AS R2,
														SUM(CASE WHEN pred.pr_rata = 3  THEN 1 ELSE 0 END) AS R3,
														SUM(CASE WHEN pred.pr_rata = 4  THEN 1 ELSE 0 END) AS R4,
														SUM(CASE WHEN pred.pr_rata = 5  THEN 1 ELSE 0 END) AS R5,
														COUNT(*) as total
													FROM idk_predracuni pred
													WHERE ".$uslov." AND ".$type_uslov."
												");
												$query->execute();
												$row = $query->fetch();
												
												$rata1 = $row["R1"];
												$rata2 = $row["R2"];
												$rata3 = $row["R3"];
												$rata4 = $row["R4"];
												$rata5 = $row["R5"];
												$total = $row["total"];
												
												$proc_r1 = number_format((($rata1 / $total)*100), 2, ',', '');
												$proc_r2 = number_format((($rata2 / $total)*100), 2, ',', '');
												$proc_r3 = number_format((($rata3 / $total)*100), 2, ',', '');
												$proc_r4 = number_format((($rata4 / $total)*100), 2, ',', '');
												$proc_r5 = number_format((($rata5 / $total)*100), 2, ',', '');
												
												
										?>
										
											<div class = "col-md-6">
												<div class="panel panel-danger">
													<div class="panel-heading">
														<h3 class="panel-title text-center"><b>Inkaso <?php echo $typee; ?></b></h3>
													</div>
													<div class="panel-body">
														<div class = "row">
															<div class="col-xs-12">
																<div id="canvas-holder" style="width:100%">
																	<canvas id="ca1<?php echo $typee;?>"></canvas>
																</div>
															</div>
														</div>
														<script>
															var position_legend = 'left';
															var position_graph = 'right';
															var aspectRatioForDesktop = true;
															var ctx11 = document.getElementById("ca1"+"<?php echo $typee;?>").getContext('2d');
															var randomScalingFactor = function() {
																return Math.round(Math.random() * 100);
															};
															
															window.myDoughnut1 = new Chart(ctx11, {
																type: 'doughnut',
																data: {
																	datasets: [{
																		data: [
																			<?php echo $rata1; ?>,
																			<?php echo $rata2; ?>,
																			<?php echo $rata3; ?>,
																			<?php echo $rata4; ?>,
																			<?php echo $rata5; ?>
																		],
																		backgroundColor: [
																			'rgb(104, 195, 104)',
																			'rgb(64, 146, 217)',
																			'rgb(242, 228, 46)',
																			'rgb(199, 156, 255)',
																			'rgb(243, 65, 60)'
																		],
																		label: 'Dataset 1'
																	}],
																	labels: [
																		'R1 (<?php echo $rata1; ?>) - <?php echo $proc_r1; ?>%',
																		'R2 (<?php echo $rata2; ?>) - <?php echo $proc_r2; ?>%',
																		'R3 (<?php echo $rata3; ?>) - <?php echo $proc_r3; ?>%',
																		'R4 (<?php echo $rata4; ?>) - <?php echo $proc_r4; ?>%',
																		'R5 (<?php echo $rata5; ?>) - <?php echo $proc_r5; ?>%',
																	]
																},
																options: {
																	responsive: true,
																	maintainAspectRatio: false,
																	position: position_graph,
																	legend: {
																		position: position_legend,
																		align: 'start'
																	},
																	title: {
																		display: true,
																		text: 'Ukupno: (<?php echo $total; ?>)'
																	},
																	animation: {
																		animateScale: true,
																		animateRotate: true
																	}
																}
															});
														</script>
													</div>
												</div>
											</div>
										
										<?php 
											}
										
										}else{
											echo '
												<div class="alert material-alert material-alert_danger">
													<h4>Problem sa linkom!</h4>
													<p>Kontaktirajte administratora za pomoć.</p>
													<br />
													<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
												</div>
											';
										}
									?>
								</div>
							</div>
						</div>
					<?php
				
				break;
				
				case "inkaso_pregled":
					?>
						<div class="row">
							<div class="col-xs-12">
								<h1><i class="fa fa-commenting-o idk_color_green" aria-hidden="true"></i> Inkaso komunikacije </h1>
							</div>
							<div class="col-xs-12">
								<hr/>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="content_box">
									<div class="row">
										<div class = "col-xs-12 col-md-12 col-lg-12">
											<div class="row">
												<?php 
													$dr = array("BAM", "RSD");
													$icon = "";
													//Bez ovih predracuna START
													$bezID = array();
													$bezIDispis = "";
													$query0 = $db->prepare("
														SELECT pred.pr_id
														FROM idk_predracuni pred
														JOIN idk_nd_kandidata_biljeske bilj
														ON pred.pr_id = bilj.predracun_id
														WHERE pred.pr_vrsta_predracuna = 1 AND pred.pr_status IN (3,4,5) AND bilj.status_biljeska_nd = 3 AND bilj.tip_biljeska_nd IN (2,3,4,5,6) AND zadnja_inkaso_biljeska = 1
													");
													$query0->execute();
													if($query0->rowCount() != 0){
														while($row0 = $query0->fetch()) {
															//echo $row0["pr_id"] ."<br/>";
															array_push($bezID, $row0["pr_id"]);
														}
														$bezIDispis = implode(",", $bezID);
													}else{
														$bezIDispis = "0";
													}
													//Bez ovih predracuna END 
													
													//Petlja za drzave
													foreach ($dr as $drzava){
														
														//Tip 1
														$tip1R1 = 0;
														$tip1R2 = 0;
														$tip1R3 = 0;
														$tip1R4 = 0;
														$tip1R5 = 0;
														//Tip 2
														$tip2R1 = 0;
														$tip2R2 = 0;
														$tip2R3 = 0;
														$tip2R4 = 0;
														$tip2R5 = 0;
														//Tip 3
														$tip3R1 = 0;
														$tip3R2 = 0;
														$tip3R3 = 0;
														$tip3R4 = 0;
														$tip3R5 = 0;
														//Tip 4
														$tip4R1 = 0;
														$tip4R2 = 0;
														$tip4R3 = 0;
														$tip4R4 = 0;
														$tip4R5 = 0;
														//Tip 5
														$tip5R1 = 0;
														$tip5R2 = 0;
														$tip5R3 = 0;
														$tip5R4 = 0;
														$tip5R5 = 0;
														
														//Ukupno po tipovima
														$ukupnoTip1 = 0;
														$ukupnoTip2 = 0;
														$ukupnoTip3 = 0;
														$ukupnoTip4 = 0;
														$ukupnoTip5 = 0;
														$ukupnoTip = 0; 
														
														//Ukupno po ratama
														$ukupnoR1 = 0;
														$ukupnoR2 = 0;
														$ukupnoR3 = 0;
														$ukupnoR4 = 0;
														$ukupnoR5 = 0;
														$ukupnoR = 0;
														
														if($drzava == "BAM"){
															$icon = '<img src="'.getSiteUrlr().'images/BosniaHerzegowina.png" width="100">';
														}else{
															$icon = '<img src="'.getSiteUrlr().'images/Serbian.png" width="100">';
														}
														
														$query_1 = $db->prepare("
															SELECT
																sum(case when pred.pr_domaca_valuta = '".$drzava."' AND pred.pr_id is not null AND pred.pr_rata = 1 then 1 else 0 end) as tip1R1,
																sum(case when pred.pr_domaca_valuta = '".$drzava."' AND pred.pr_id is not null AND pred.pr_rata = 2 then 1 else 0 end) as tip1R2,
																sum(case when pred.pr_domaca_valuta = '".$drzava."' AND pred.pr_id is not null AND pred.pr_rata = 3 then 1 else 0 end) as tip1R3,
																sum(case when pred.pr_domaca_valuta = '".$drzava."' AND pred.pr_id is not null AND pred.pr_rata = 4 then 1 else 0 end) as tip1R4,
																sum(case when pred.pr_domaca_valuta = '".$drzava."' AND pred.pr_id is not null AND pred.pr_rata = 5 then 1 else 0 end) as tip1R5
															FROM idk_predracuni pred
															WHERE pred.pr_vrsta_predracuna = 1 AND  pred.pr_status IN (3,4,5) AND pr_uplaceno = 0 AND pred.pr_stari_ink_status is null AND pred.pr_id NOT IN (".$bezIDispis.")
														");
														$query_1->execute();
														
														$query_2 = $db->prepare("
															SELECT
																sum(case when pred.pr_domaca_valuta = '".$drzava."' AND bilj.tip_biljeska_nd = 2  AND pred.pr_rata = 1 then 1 else 0 end) as tip2R1,
																sum(case when pred.pr_domaca_valuta = '".$drzava."' AND bilj.tip_biljeska_nd = 2  AND pred.pr_rata = 2 then 1 else 0 end) as tip2R2,
																sum(case when pred.pr_domaca_valuta = '".$drzava."' AND bilj.tip_biljeska_nd = 2  AND pred.pr_rata = 3 then 1 else 0 end) as tip2R3,
																sum(case when pred.pr_domaca_valuta = '".$drzava."' AND bilj.tip_biljeska_nd = 2  AND pred.pr_rata = 4 then 1 else 0 end) as tip2R4,
																sum(case when pred.pr_domaca_valuta = '".$drzava."' AND bilj.tip_biljeska_nd = 2  AND pred.pr_rata = 5 then 1 else 0 end) as tip2R5,
																sum(case when pred.pr_domaca_valuta = '".$drzava."' AND bilj.tip_biljeska_nd = 3  AND pred.pr_rata = 1 then 1 else 0 end) as tip3R1,
																sum(case when pred.pr_domaca_valuta = '".$drzava."' AND bilj.tip_biljeska_nd = 3  AND pred.pr_rata = 2 then 1 else 0 end) as tip3R2,
																sum(case when pred.pr_domaca_valuta = '".$drzava."' AND bilj.tip_biljeska_nd = 3  AND pred.pr_rata = 3 then 1 else 0 end) as tip3R3,
																sum(case when pred.pr_domaca_valuta = '".$drzava."' AND bilj.tip_biljeska_nd = 3  AND pred.pr_rata = 4 then 1 else 0 end) as tip3R4,
																sum(case when pred.pr_domaca_valuta = '".$drzava."' AND bilj.tip_biljeska_nd = 3  AND pred.pr_rata = 5 then 1 else 0 end) as tip3R5,
																sum(case when pred.pr_domaca_valuta = '".$drzava."' AND bilj.tip_biljeska_nd = 5  AND pred.pr_rata = 1 then 1 else 0 end) as tip4R1,
																sum(case when pred.pr_domaca_valuta = '".$drzava."' AND bilj.tip_biljeska_nd = 5  AND pred.pr_rata = 2 then 1 else 0 end) as tip4R2,
																sum(case when pred.pr_domaca_valuta = '".$drzava."' AND bilj.tip_biljeska_nd = 5  AND pred.pr_rata = 3 then 1 else 0 end) as tip4R3,
																sum(case when pred.pr_domaca_valuta = '".$drzava."' AND bilj.tip_biljeska_nd = 5  AND pred.pr_rata = 4 then 1 else 0 end) as tip4R4,
																sum(case when pred.pr_domaca_valuta = '".$drzava."' AND bilj.tip_biljeska_nd = 5  AND pred.pr_rata = 5 then 1 else 0 end) as tip4R5
															FROM idk_predracuni pred
															JOIN idk_nd_kandidata_biljeske bilj
															ON pred.pr_id = bilj.predracun_id
															WHERE pred.pr_vrsta_predracuna = 1 AND pred.pr_status IN (3,4,5) AND pr_uplaceno = 0 AND  bilj.status_biljeska_nd = 3 AND bilj.zadnja_inkaso_biljeska = 1
														");
														$query_2->execute();
														
														$query_3 = $db->prepare("
															SELECT
																sum(case when pred.pr_domaca_valuta = '".$drzava."' AND pr_stari_ink_status is not null AND pred.pr_rata = 1 then 1 else 0 end) as tip5R1,
																sum(case when pred.pr_domaca_valuta = '".$drzava."' AND pr_stari_ink_status is not null AND pred.pr_rata = 2 then 1 else 0 end) as tip5R2,
																sum(case when pred.pr_domaca_valuta = '".$drzava."' AND pr_stari_ink_status is not null AND pred.pr_rata = 3 then 1 else 0 end) as tip5R3,
																sum(case when pred.pr_domaca_valuta = '".$drzava."' AND pr_stari_ink_status is not null AND pred.pr_rata = 4 then 1 else 0 end) as tip5R4,
																sum(case when pred.pr_domaca_valuta = '".$drzava."' AND pr_stari_ink_status is not null AND pred.pr_rata = 5 then 1 else 0 end) as tip5R5
															FROM idk_predracuni pred
															WHERE pred.pr_vrsta_predracuna = 1 AND pred.pr_status IN (1,3,4,5) AND pr_uplaceno = 0
														");
														$query_3->execute();
														
														$row1 = $query_1->fetch();
														$row2 = $query_2->fetch();
														$row3 = $query_3->fetch();
														
														//Tip 1
														$tip1R1 = intval($row1["tip1R1"]);
														$tip1R2 = intval($row1["tip1R2"]);
														$tip1R3 = intval($row1["tip1R3"]);
														$tip1R4 = intval($row1["tip1R4"]);
														$tip1R5 = intval($row1["tip1R5"]);
														$ukupnoTip1 = $tip1R1+$tip1R2+$tip1R3+$tip1R4+$tip1R5; 
														//Tip 2
														$tip2R1 = intval($row2["tip2R1"]);
														$tip2R2 = intval($row2["tip2R2"]);
														$tip2R3 = intval($row2["tip2R3"]);
														$tip2R4 = intval($row2["tip2R4"]);
														$tip2R5 = intval($row2["tip2R5"]);
														$ukupnoTip2 = $tip2R1+$tip2R2+$tip2R3+$tip2R4+$tip2R5; 
														//Tip 3
														$tip3R1 = intval($row2["tip3R1"]);
														$tip3R2 = intval($row2["tip3R2"]);
														$tip3R3 = intval($row2["tip3R3"]);
														$tip3R4 = intval($row2["tip3R4"]);
														$tip3R5 = intval($row2["tip3R5"]);
														$ukupnoTip3 = $tip3R1+$tip3R2+$tip3R3+$tip3R4+$tip3R5; 
														//Tip 4
														$tip4R1 = intval($row2["tip4R1"]);
														$tip4R2 = intval($row2["tip4R2"]);
														$tip4R3 = intval($row2["tip4R3"]);
														$tip4R4 = intval($row2["tip4R4"]);
														$tip4R5 = intval($row2["tip4R5"]);
														$ukupnoTip4 = $tip4R1+$tip4R2+$tip4R3+$tip4R4+$tip4R5; 
														//Tip 5
														$tip5R1 = intval($row3["tip5R1"]);
														$tip5R2 = intval($row3["tip5R2"]);
														$tip5R3 = intval($row3["tip5R3"]);
														$tip5R4 = intval($row3["tip5R4"]);
														$tip5R5 = intval($row3["tip5R5"]);
														$ukupnoTip5 = $tip5R1+$tip5R2+$tip5R3+$tip5R4+$tip5R5;
														
														//Ukupno po ratama
														$ukupnoR1 = $tip1R1+$tip2R1+$tip3R1+$tip4R1+$tip5R1;
														$ukupnoR2 = $tip1R2+$tip2R2+$tip3R2+$tip4R2+$tip5R2;
														$ukupnoR3 = $tip1R3+$tip2R3+$tip3R3+$tip4R3+$tip5R3;
														$ukupnoR4 = $tip1R4+$tip2R4+$tip3R4+$tip4R4+$tip5R4;
														$ukupnoR5 = $tip1R5+$tip2R5+$tip3R5+$tip4R5+$tip5R5;
														
														//Ukupno od ukupnog po tipu
														$ukupnoTip = $ukupnoTip1+$ukupnoTip2+$ukupnoTip3+$ukupnoTip4+$ukupnoTip5;
														//Ukupno od ukupnog po tipu
														$ukupnoR = $ukupnoR1+$ukupnoR2+$ukupnoR3+$ukupnoR4+$ukupnoR5;
												?>
												<div class = "col-md-6">
													<div class="panel panel-default">
														<div class="panel-heading">
															<h3 class="panel-title text-center"><?php echo $icon; ?></h3>
														</div>
														<div class="panel-body">
															<div class="row">
																<div class = "col-xs-12">
																	<script type="text/javascript">
																		$(document).ready(function() {
																			$('#inkaso_<?php echo $drzava; ?>').DataTable({

																				responsive: true,
																				"bAutoWidth": false,
																				"bPaginate": false,
																				"bLengthChange": false,
																				"order": false,

																				"aoColumns": 
																				[
																					{ "width": "25%", "bSortable": false },
																					{ "width": "12.5%", "bSortable": false },
																					{ "width": "12.5%", "bSortable": false },
																					{ "width": "12.5%", "bSortable": false },
																					{ "width": "12.5%", "bSortable": false },
																					{ "width": "12.5%", "bSortable": false },
																					{ "width": "12.5%", "bSortable": false },
																				]
																			});
																		});
																	</script>
																	<table id="inkaso_<?php echo $drzava; ?>" class="display" cellspacing="0" width="100%">
																		<thead>
																			<tr>
																				<th class="text-center">Komunikacija</th>
																				<th class="text-center">Rata 1</th>
																				<th class="text-center">Rata 2</th>
																				<th class="text-center">Rata 3</th>
																				<th class="text-center">Rata 4</th>
																				<th class="text-center">Rata 5</th>
																				<th class="text-center">Ukupno po komunikaciji</th>
																			</tr>
																		</thead>
																		<tbody>
																			<tr>
																				<th class="text-center">Novi</th>
																				<td class="text-center"><?php echo $tip1R1; ?></td>
																				<td class="text-center"><?php echo $tip1R2; ?></td>
																				<td class="text-center"><?php echo $tip1R3; ?></td>
																				<td class="text-center"><?php echo $tip1R4; ?></td>
																				<td class="text-center"><?php echo $tip1R5; ?></td>
																				<th class="text-center"><?php echo $ukupnoTip1; ?></th>
																			</tr>
																			<tr>
																				<th class="text-center">Ne javlja se</th>
																				<td class="text-center"><?php echo $tip2R1; ?></td>
																				<td class="text-center"><?php echo $tip2R2; ?></td>
																				<td class="text-center"><?php echo $tip2R3; ?></td>
																				<td class="text-center"><?php echo $tip2R4; ?></td>
																				<td class="text-center"><?php echo $tip2R5; ?></td>
																				<th class="text-center"><?php echo $ukupnoTip2; ?></th>
																			</tr>
																			<tr>
																				<th class="text-center">Pozvati kasnije</th>
																				<td class="text-center"><?php echo $tip3R1; ?></td>
																				<td class="text-center"><?php echo $tip3R2; ?></td>
																				<td class="text-center"><?php echo $tip3R3; ?></td>
																				<td class="text-center"><?php echo $tip3R4; ?></td>
																				<td class="text-center"><?php echo $tip3R5; ?></td>
																				<th class="text-center"><?php echo $ukupnoTip3; ?></th>
																			</tr>
																			<tr>
																				<th class="text-center">Uplata na dan</th>
																				<td class="text-center"><?php echo $tip4R1; ?></td>
																				<td class="text-center"><?php echo $tip4R2; ?></td>
																				<td class="text-center"><?php echo $tip4R3; ?></td>
																				<td class="text-center"><?php echo $tip4R4; ?></td>
																				<td class="text-center"><?php echo $tip4R5; ?></td>
																				<th class="text-center"><?php echo $ukupnoTip4; ?></th>
																			</tr>
																			<tr>
																				<th class="text-center">Promjena ugovora</th>
																				<td class="text-center"><?php echo $tip5R1; ?></td>
																				<td class="text-center"><?php echo $tip5R2; ?></td>
																				<td class="text-center"><?php echo $tip5R3; ?></td>
																				<td class="text-center"><?php echo $tip5R4; ?></td>
																				<td class="text-center"><?php echo $tip5R5; ?></td>
																				<th class="text-center"><?php echo $ukupnoTip5; ?></th>
																			</tr>
																		</tbody>
																		<tfoot>
																			<tr>
																				<th class="text-center">Ukupno po ratama</th>
																				<th class="text-center"><?php echo $ukupnoR1; ?></th>
																				<th class="text-center"><?php echo $ukupnoR2; ?></th>
																				<th class="text-center"><?php echo $ukupnoR3; ?></th>
																				<th class="text-center"><?php echo $ukupnoR4; ?></th>
																				<th class="text-center"><?php echo $ukupnoR5; ?></th>
																				<th class="text-center"><?php echo $ukupnoR." / ".$ukupnoTip; ?></th>
																			</tr>
																		</tfoot>
																	</table>
																</div>
															</div>
															<div class="row" style = "margin-top: 20px;">
																<div class = "col-xs-12">
																	<div class="panel panel-primary">
																		<div class="panel-heading">
																			<h3 class="panel-title text-center"><b>Statistika po ratama</b></h3>
																		</div>
																		<div class="panel-body">
																			<div id="canvas-holder" style="width:100%">
																				<canvas id="ca1<?php echo $drzava;?>"></canvas>
																			</div>
																			<script>
																				var position_legend = 'left';
																				var position_graph = 'right';
																				var aspectRatioForDesktop = true;
																				var ctx11 = document.getElementById("ca1"+"<?php echo $drzava;?>").getContext('2d');
																				var randomScalingFactor = function() {
																					return Math.round(Math.random() * 100);
																				};
																				
																				window.myDoughnut1 = new Chart(ctx11, {
																					type: 'doughnut',
																					data: {
																						datasets: [{
																							data: [
																								<?php echo $ukupnoR1; ?>,
																								<?php echo $ukupnoR2; ?>,
																								<?php echo $ukupnoR3; ?>,
																								<?php echo $ukupnoR4; ?>,
																								<?php echo $ukupnoR5; ?>
																							],
																							backgroundColor: [
																								'rgb(104, 195, 104)',
																								'rgb(64, 146, 217)',
																								'rgb(242, 228, 46)',
																								'rgb(199, 156, 255)',
																								'rgb(243, 65, 60)'
																							],
																							label: 'Dataset 1'
																						}],
																						labels: [
																							'Rata 1 (<?php echo $ukupnoR1; ?>) - <?php echo number_format((($ukupnoR1 / $ukupnoR)*100), 2, ',', ''); ?>%',
																							'Rata 2 (<?php echo $ukupnoR2; ?>) - <?php echo number_format((($ukupnoR2 / $ukupnoR)*100), 2, ',', ''); ?>%',
																							'Rata 3 (<?php echo $ukupnoR3; ?>) - <?php echo number_format((($ukupnoR3 / $ukupnoR)*100), 2, ',', ''); ?>%',
																							'Rata 4 (<?php echo $ukupnoR4; ?>) - <?php echo number_format((($ukupnoR4 / $ukupnoR)*100), 2, ',', ''); ?>%',
																							'Rata 5 (<?php echo $ukupnoR5; ?>) - <?php echo number_format((($ukupnoR5 / $ukupnoR)*100), 2, ',', ''); ?>%',
																						]
																					},
																					options: {
																						responsive: true,
																						maintainAspectRatio: false,
																						position: position_graph,
																						legend: {
																							position: position_legend,
																							align: 'start'
																						},
																						title: {
																							display: true,
																							text: 'Ukupno: (<?php echo $ukupnoR; ?>)'
																						},
																						animation: {
																							animateScale: true,
																							animateRotate: true
																						}
																					}
																				});
																			</script>
																		</div>
																	</div>
																</div>
															</div>
															
															<div class="row" style = "margin-top: 20px;">
																<div class = "col-xs-12">
																	<div class="panel panel-primary">
																		<div class="panel-heading">
																			<h3 class="panel-title text-center"><b>Statistika po komunikaciji</b></h3>
																		</div>
																		<div class="panel-body">
																			<div id="canvas-holder" style="width:100%">
																				<canvas id="ca2<?php echo $drzava;?>"></canvas>
																			</div>
																			<script>
																				var position_legend = 'left';
																				var position_graph = 'right';
																				var aspectRatioForDesktop = true;
																				var ctx12 = document.getElementById("ca2"+"<?php echo $drzava;?>").getContext('2d');
																				var randomScalingFactor = function() {
																					return Math.round(Math.random() * 100);
																				};
																				window.myDoughnut1 = new Chart(ctx12, {
																					type: 'doughnut',
																					data: {
																						datasets: [{
																							data: [
																								<?php echo $ukupnoTip1; ?>,
																								<?php echo $ukupnoTip2; ?>,
																								<?php echo $ukupnoTip3; ?>,
																								<?php echo $ukupnoTip4; ?>,
																								<?php echo $ukupnoTip5; ?>
																							],
																							backgroundColor: [
																								'rgb(104, 195, 104)',
																								'rgb(64, 146, 217)',
																								'rgb(242, 228, 46)',
																								'rgb(199, 156, 255)',
																								'rgb(243, 65, 60)'
																							],
																							label: 'Dataset 1'
																						}],
																						labels: [
																							'Novi (<?php echo $ukupnoTip1; ?>) - <?php echo number_format((($ukupnoTip1 / $ukupnoTip)*100), 2, ',', ''); ?>%',
																							'Ne javlja se (<?php echo $ukupnoTip2; ?>) - <?php echo number_format((($ukupnoTip2 / $ukupnoTip)*100), 2, ',', ''); ?>%',
																							'Pozvati kasnije (<?php echo $ukupnoTip3; ?>) - <?php echo number_format((($ukupnoTip3 / $ukupnoTip)*100), 2, ',', ''); ?>%',
																							'Uplata na dan (<?php echo $ukupnoTip4; ?>) - <?php echo number_format((($ukupnoTip4 / $ukupnoTip)*100), 2, ',', ''); ?>%',
																							'Promjena ugovora (<?php echo $ukupnoTip5; ?>) - <?php echo number_format((($ukupnoTip5 / $ukupnoTip)*100), 2, ',', ''); ?>%',
																						]
																					},
																					options: {
																						responsive: true,
																						maintainAspectRatio: false,
																						position: position_graph,
																						legend: {
																							position: position_legend,
																							align: 'start'
																						},
																						title: {
																							display: true,
																							text: 'Ukupno: (<?php echo $ukupnoTip; ?>)'
																						},
																						animation: {
																							animateScale: true,
																							animateRotate: true
																						}
																					}
																				});
																			</script>
																		</div>
																	</div>
																	
																</div>
															</div>
															
														</div>
													</div>
												</div>
												<?php 
													}
												?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?php
				break;
				
				case "dash_stat";
					$branch_id = intval($_GET["branch_id"]);
					
					//---------------------------------------------------------------
					//Ostalo potrebno za statistiku START
					function invertSign($value){
						return -$value;
					}
					//Funkcija koja odredjuje pocetni i krajnji datum za odredjenu sedmicu u godini. Funkciji se prosljedjuje sedmica i godina.
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
					//Ostalo potrebno za statistiku END 
					//---------------------------------------------------------------
					
					//---------------------------------------------------------------
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
					//Aktivni Deaktivirani zaposlenici END 
					//---------------------------------------------------------------
					
					$query2 = $db->prepare("
						SELECT *
						FROM idk_poslovnice pos
						WHERE pos.branch_id = ".$branch_id."
						ORDER BY pos.branch_city ASC;
					");
					$query2->execute();
					$row2 = $query2->fetch();
					
					if(isset($branch_id)){
					?>
						<style>
							.a_link{
								color: #000000 !important;
							}
							.a_link:hover{
								color: rgb(80 255 0) !important;
								text-decoration: none !important;
							}
						</style>
						<div class="row">
							<div class="col-xs-6">
								<h1><i class="fa fa-bar-chart idk_color_green" aria-hidden="true" style = "margin-right:15px;"></i> Statistika za poslovnicu <?php echo " ".$row2["branch_name"].", ".$row2["branch_city"].", ".$row2["branch_state"]." "; ?></h1>
							</div>
							<div class="col-xs-6">
								<?php 
									$uslov_query = $_POST['selected_rows'];
									if(isset($uslov_query)){
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
								<form action="<?php getSiteURL(); ?>modul_statistike?page=dash_stat&branch_id=<?php echo $branch_id; ?>" name="refresh_form" id="refresh_form" method="POST" enctype="multipart/form-data" class="form-horizontal">
									<input type="hidden" name = "selected_rows" id="selected_rows"></input>
									<button type="submit" value="Submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive refDataTable pull-right"><?php echo $dugme_ispis; ?></button>
								</form>
							</div>
							<div class="col-xs-12">
								<hr/>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="content_box">
									<div class="row">
										<div class = "col-xs-12 col-md-12 col-lg-12" style = "overflow-y: auto !important;">
											<script type="text/javascript">
												$(document).ready(function() {
													var table = $('#tableStat').DataTable({
														
														responsive: false,
														"order": [[ 1, "desc" ]],
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
														alert(selectedIds);
													}); 													
												});
											</script>
											<table id="tableStat" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th class="text-center">#</th>
														<th class="text-center">Menadzer</th>
														<th class="text-center">Tim</th>
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
													$sum_broj_prozvanih['on'] = 0;
													$sum_broj_izdanih['on'] = 0;
													$sum_broj_uplacenih['on'] = 0;
													$sum_broj_prozvanih['off'] = 0;
													$sum_broj_izdanih['off'] = 0;
													$sum_broj_uplacenih['off'] = 0;
													
													$sum_pkn['on'] = 0;
													$sum_pn['on'] = 0;
													$sum_cr['on'] = 0;
													$sum_pkn['off'] = 0;
													$sum_pn['off'] = 0;
													$sum_cr['off'] = 0;
													
													$sum_broj_neprihvacenih_CD['on'] = 0;
													$sum_broj_neprihvacenih_KS1['on'] = 0;
													$sum_broj_neprihvacenih_KS2['on'] = 0;
													$sum_broj_neprihvacenih_KS3['on'] = 0;
													$sum_broj_neprihvacenih_KS4['on'] = 0;
													$sum_broj_neprihvacenih_PM['on'] = 0;
													$sum_broj_neprihvacenih_KS['on'] = 0;
													$sum_broj_neprihvacenih_CD['off'] = 0;
													$sum_broj_neprihvacenih_KS1['off'] = 0;
													$sum_broj_neprihvacenih_KS2['off'] = 0;
													$sum_broj_neprihvacenih_KS3['off'] = 0;
													$sum_broj_neprihvacenih_KS4['off'] = 0;
													$sum_broj_neprihvacenih_PM['off'] = 0;
													$sum_broj_neprihvacenih_KS['off'] = 0;

													$sum_broj_izdanih_CD['on'] = 0;
													$sum_broj_izdanih_KS1['on'] = 0;
													$sum_broj_izdanih_KS2['on'] = 0;
													$sum_broj_izdanih_KS3['on'] = 0;
													$sum_broj_izdanih_KS4['on'] = 0;
													$sum_broj_izdanih_PM['on'] = 0;
													$sum_broj_izdanih_KS['on'] = 0;
													$sum_broj_izdanih_CD['off'] = 0;
													$sum_broj_izdanih_KS1['off'] = 0;
													$sum_broj_izdanih_KS2['off'] = 0;
													$sum_broj_izdanih_KS3['off'] = 0;
													$sum_broj_izdanih_KS4['off'] = 0;
													$sum_broj_izdanih_PM['off'] = 0;
													$sum_broj_izdanih_KS['off'] = 0;
													
													$sum_broj_uplacenih_CD['on'] = 0;
													$sum_broj_uplacenih_KS1['on'] = 0;
													$sum_broj_uplacenih_KS2['on'] = 0;
													$sum_broj_uplacenih_KS3['on'] = 0;
													$sum_broj_uplacenih_KS4['on'] = 0;
													$sum_broj_uplacenih_PM['on'] = 0;
													$sum_broj_uplacenih_KS['on'] = 0;
													$sum_broj_uplacenih_CD['off'] = 0;
													$sum_broj_uplacenih_KS1['off'] = 0;
													$sum_broj_uplacenih_KS2['off'] = 0;
													$sum_broj_uplacenih_KS3['off'] = 0;
													$sum_broj_uplacenih_KS4['off'] = 0;
													$sum_broj_uplacenih_PM['off'] = 0;
													$sum_broj_uplacenih_KS['off'] = 0;
													
													$sum_uplacenih_CD['on'] = 0;
													$sum_uplacenih_KS1['on'] = 0;
													$sum_uplacenih_KS2['on'] = 0;
													$sum_uplacenih_KS3['on'] = 0;
													$sum_uplacenih_KS4['on'] = 0;
													$sum_uplacenih_PM['on'] = 0;
													$sum_uplacenih_KS['on'] = 0;
													$sum_uplacenih_CD['off'] = 0;
													$sum_uplacenih_KS1['off'] = 0;
													$sum_uplacenih_KS2['off'] = 0;
													$sum_uplacenih_KS3['off'] = 0;
													$sum_uplacenih_KS4['off'] = 0;
													$sum_uplacenih_PM['off'] = 0;
													$sum_uplacenih_KS['off'] = 0;
														
													$query3 = $db->prepare("
														SELECT emp.employee_id, emp.employee_firstname, emp.employee_lastname, emp.employee_nostrifikacija_diploma
														FROM idk_employees emp
														WHERE emp.employee_poslovnica = ".$branch_id." AND (emp.employee_id IN (".implode(",", $zapDiplA).") OR emp.employee_id IN (".implode(",", $zapDiplD).")) AND emp.employee_id NOT IN (".$uslov_query.")
													");
													$query3->execute();
													while($row3 = $query3->fetch()){
														$row_color = "";
														$row_on_off = "";
														
														$broj_prozvanih = 0;
														$broj_izdanih = 0;
														$broj_uplacenih = 0;
														
														$pkn = 0;
														$pn = 0;
														$cr = 0;
														
														$broj_neprihvacenih_CD = 0;
														$broj_neprihvacenih_KS1 = 0;
														$broj_neprihvacenih_KS2 = 0;
														$broj_neprihvacenih_KS3 = 0;
														$broj_neprihvacenih_KS4 = 0;
														$broj_neprihvacenih_PM = 0;
														$ukupno_broj_neprihvacenih_KS = 0;

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
														
														if(in_array($row3["employee_id"], $zapDiplD)){
															$row_on_off = 'off';
															$row_color = "background-color: rgb(244 67 54); color: rgb(255 255 255); font-weight: bold;";
														}else{
															$row_on_off = 'on';
															if(intval($row3["employee_nostrifikacija_diploma"]) == 0){
																$row_color = "background-color: rgb(255 152 0); color: rgb(255 255 255); font-weight: bold;";
															}else{
																$row_color = "background-color: rgb(76 175 80); color: rgb(255 255 255); font-weight: bold;";
															}
															
														}
														
														//query za broj prozvanih kandidata 
														$query4 = $db->prepare("
															SELECT COUNT(DISTINCT log.idd_broj_nd_kandidata) as broj_prozvanih 
															FROM idk_nd_kandidata kan
															JOIN idk_nd_kandidata_status_log log 
															ON kan.id_broj_nd_kandidata = log.idd_broj_nd_kandidata
															WHERE(kan.status_nd_kandidata IN (2,3,4,5,6,7) OR (kan.status_nd_kandidata = 1 AND kan.pstatus_nd_kandidata IN (2,6,3,4,5,7,8,9,10,11,12)))
															AND ((log.status_nd_kandidata IN (1) AND log.pstatus_nd_kandidata IN (3,4,5,9,10,12)) OR log.status_nd_kandidata IN (2,3,4,5,6))
															AND log.promjenio_zaposlenik_nd_kandidata = ".intval($row3["employee_id"])."
															AND log.vrijeme_promjene_statusa_nd_kandidata >= '2021-07-01 00:00:00'
														");
														$query4->execute();
														$row4 = $query4->fetch();
														$broj_prozvanih = intval($row4["broj_prozvanih"]); //Broj prozvanih kandidata
														$sum_broj_prozvanih[$row_on_off] = $sum_broj_prozvanih[$row_on_off] + $broj_prozvanih; //Suma broja prozvanih kandidata
														//query za broj izdanih predracuna
														$query5 = $db->prepare("
															SELECT COUNT(DISTINCT pred.pr_kandidat_id) as broj_izdanih 
															FROM idk_nd_kandidata kan
															JOIN idk_predracuni pred ON kan.id_broj_nd_kandidata = pred.pr_kandidat_id
															WHERE (kan.status_nd_kandidata IN(2,3,4,5,6,7) OR (kan.status_nd_kandidata = 1 AND kan.pstatus_nd_kandidata IN (2,6,3,4,5,7,8,9,10,11,12)))
															AND pred.pr_zaposlenik = ".intval($row3["employee_id"])."
															AND pred.pr_rata = 1
															AND pred.pr_status is not null
															AND pred.pr_datum_kreiranja >= '2021-07-01 00:00:00'
															AND pred.pr_file is not null
														");
														$query5->execute();
														$row5 = $query5->fetch();
														$broj_izdanih = intval($row5["broj_izdanih"]); //Broj izdanih predracuna
														$sum_broj_izdanih[$row_on_off] = $sum_broj_izdanih[$row_on_off] + $broj_izdanih; //Suma broja izdanih predracuna
														//query za broj uplacenih predracuna
														$query6 = $db->prepare("
															SELECT COUNT(DISTINCT pred.pr_kandidat_id) as broj_uplacenih 
															FROM idk_nd_kandidata kan
															JOIN idk_predracuni pred ON kan.id_broj_nd_kandidata = pred.pr_kandidat_id
															WHERE (kan.status_nd_kandidata IN(2,3,4,5,6,7) OR (kan.status_nd_kandidata = 1 AND kan.pstatus_nd_kandidata IN (2,6,3,4,5,7,8,9,10,11,12)))
															AND pred.pr_zaposlenik = ".intval($row3["employee_id"])."
															AND pred.pr_uplaceno = 1
															AND pred.pr_rata = 1
															AND pred.pr_datum_kreiranja >= '2021-07-01 00:00:00'
														");
														$query6->execute();
														$row6 = $query6->fetch();
														$broj_uplacenih = intval($row6["broj_uplacenih"]); //Broj uplacenih predracuna
														$sum_broj_uplacenih[$row_on_off] = $sum_broj_uplacenih[$row_on_off] + $broj_uplacenih; //Suma broja izdanih predracuna
														
														$pkn = ($broj_prozvanih == 0) ? number_format(0, 2, ',', '') : number_format(((($broj_uplacenih) / $broj_prozvanih)*100), 2, ',', ''); // procenat krajnje naplate
														$pn = ($broj_izdanih == 0) ? number_format(0, 2, ',', '') : number_format(((($broj_uplacenih) / $broj_izdanih)*100), 2, ',', ''); // procenat naplate
														$cr = ($broj_prozvanih == 0) ? number_format(0, 2, ',', '') : number_format(((($broj_izdanih) / $broj_prozvanih)*100), 2, ',', ''); // procenat conversion rate-a
														
														$query7 = $db->prepare("
															SELECT
																SUM(CASE WHEN pred.pr_file is null AND pred.pr_datum_kreiranja BETWEEN '".$pocetak0."' AND '".$kraj0."' THEN 1 ELSE 0 END) AS broj_neprihvacenih_CD,
																SUM(CASE WHEN pred.pr_file is null AND pred.pr_datum_kreiranja BETWEEN '".$pocetak1."' AND '".$kraj1."' THEN 1 ELSE 0 END) AS broj_neprihvacenih_KS1,
																SUM(CASE WHEN pred.pr_file is null AND pred.pr_datum_kreiranja BETWEEN '".$pocetak2."' AND '".$kraj2."' THEN 1 ELSE 0 END) AS broj_neprihvacenih_KS2,
																SUM(CASE WHEN pred.pr_file is null AND pred.pr_datum_kreiranja BETWEEN '".$pocetak3."' AND '".$kraj3."' THEN 1 ELSE 0 END) AS broj_neprihvacenih_KS3,
																SUM(CASE WHEN pred.pr_file is null AND pred.pr_datum_kreiranja BETWEEN '".$pocetak4."' AND '".$kraj4."' THEN 1 ELSE 0 END) AS broj_neprihvacenih_KS4,
																SUM(CASE WHEN pred.pr_file is null AND pred.pr_datum_kreiranja BETWEEN '".date('Y-m-01 H:i:s', strtotime("-1 months", strtotime(date("Y-m-d 00:00:00"))))."' AND '".date('Y-m-t H:i:s', strtotime("-1 months", strtotime(date("Y-m-d 23:59:59"))))."' THEN 1 ELSE 0 END) AS broj_neprihvacenih_PM,
																SUM(CASE WHEN pred.pr_file is not null AND pred.pr_datum_kreiranja BETWEEN '".$pocetak0."' AND '".$kraj0."' THEN 1 ELSE 0 END) AS broj_izdanih_CD,
																SUM(CASE WHEN pred.pr_file is not null AND pred.pr_datum_kreiranja BETWEEN '".$pocetak1."' AND '".$kraj1."' THEN 1 ELSE 0 END) AS broj_izdanih_KS1,
																SUM(CASE WHEN pred.pr_file is not null AND pred.pr_datum_kreiranja BETWEEN '".$pocetak2."' AND '".$kraj2."' THEN 1 ELSE 0 END) AS broj_izdanih_KS2,
																SUM(CASE WHEN pred.pr_file is not null AND pred.pr_datum_kreiranja BETWEEN '".$pocetak3."' AND '".$kraj3."' THEN 1 ELSE 0 END) AS broj_izdanih_KS3,
																SUM(CASE WHEN pred.pr_file is not null AND pred.pr_datum_kreiranja BETWEEN '".$pocetak4."' AND '".$kraj4."' THEN 1 ELSE 0 END) AS broj_izdanih_KS4,
																SUM(CASE WHEN pred.pr_file is not null AND pred.pr_datum_kreiranja BETWEEN '".date('Y-m-01 H:i:s', strtotime("-1 months", strtotime(date("Y-m-d 00:00:00"))))."' AND '".date('Y-m-t H:i:s', strtotime("-1 months", strtotime(date("Y-m-d 23:59:59"))))."' THEN 1 ELSE 0 END) AS broj_izdanih_PM,
																SUM(CASE WHEN pred.pr_file is not null AND pred.pr_uplaceno = 1 AND pred.pr_datum_kreiranja BETWEEN '".$pocetak0."' AND '".$kraj0."' AND pred.pr_datum_uplate = CURRENT_DATE THEN 1 ELSE 0 END) AS broj_uplacenih_CD,
																SUM(CASE WHEN pred.pr_file is not null AND pred.pr_uplaceno = 1 AND pred.pr_datum_kreiranja BETWEEN '".$pocetak1."' AND '".$kraj1."' AND pred.pr_datum_uplate BETWEEN '".$result1['week_start']."' AND '".$result1['week_end']."' THEN 1 ELSE 0 END) AS broj_uplacenih_KS1,
																SUM(CASE WHEN pred.pr_file is not null AND pred.pr_uplaceno = 1 AND pred.pr_datum_kreiranja BETWEEN '".$pocetak2."' AND '".$kraj2."' AND pred.pr_datum_uplate BETWEEN '".$result2['week_start']."' AND '".$result2['week_end']."' THEN 1 ELSE 0 END) AS broj_uplacenih_KS2,
																SUM(CASE WHEN pred.pr_file is not null AND pred.pr_uplaceno = 1 AND pred.pr_datum_kreiranja BETWEEN '".$pocetak3."' AND '".$kraj3."' AND pred.pr_datum_uplate BETWEEN '".$result3['week_start']."' AND '".$result3['week_end']."' THEN 1 ELSE 0 END) AS broj_uplacenih_KS3,
																SUM(CASE WHEN pred.pr_file is not null AND pred.pr_uplaceno = 1 AND pred.pr_datum_kreiranja BETWEEN '".$pocetak4."' AND '".$kraj4."' AND pred.pr_datum_uplate BETWEEN '".$result4['week_start']."' AND '".$result4['week_end']."' THEN 1 ELSE 0 END) AS broj_uplacenih_KS4,
																SUM(CASE WHEN pred.pr_file is not null AND pred.pr_uplaceno = 1 AND pred.pr_datum_kreiranja BETWEEN '".date('Y-m-01 H:i:s', strtotime("-1 months", strtotime(date("Y-m-d 00:00:00"))))."' AND '".date('Y-m-t H:i:s', strtotime("-1 months", strtotime(date("Y-m-d 23:59:59"))))."'  AND pred.pr_datum_uplate BETWEEN '".date('Y-m-01', strtotime("-1 months", strtotime(date("Y-m-d 00:00:00"))))."' AND '".date('Y-m-t', strtotime("-1 months", strtotime(date("Y-m-d 23:59:59"))))."' THEN 1 ELSE 0 END) AS broj_uplacenih_PM,
																SUM(CASE WHEN pred.pr_file is not null AND pred.pr_uplaceno = 1 AND pred.pr_datum_uplate = CURRENT_DATE THEN 1 ELSE 0 END) AS uplacenih_CD,
																SUM(CASE WHEN pred.pr_file is not null AND pred.pr_uplaceno = 1 AND pred.pr_datum_uplate BETWEEN '".$result1['week_start']."' AND '".$result1['week_end']."' THEN 1 ELSE 0 END) AS uplacenih_KS1,
																SUM(CASE WHEN pred.pr_file is not null AND pred.pr_uplaceno = 1 AND pred.pr_datum_uplate BETWEEN '".$result2['week_start']."' AND '".$result2['week_end']."' THEN 1 ELSE 0 END) AS uplacenih_KS2,
																SUM(CASE WHEN pred.pr_file is not null AND pred.pr_uplaceno = 1 AND pred.pr_datum_uplate BETWEEN '".$result3['week_start']."' AND '".$result3['week_end']."' THEN 1 ELSE 0 END) AS uplacenih_KS3,
																SUM(CASE WHEN pred.pr_file is not null AND pred.pr_uplaceno = 1 AND pred.pr_datum_uplate BETWEEN '".$result4['week_start']."' AND '".$result4['week_end']."' THEN 1 ELSE 0 END) AS uplacenih_KS4,
																SUM(CASE WHEN pred.pr_file is not null AND pred.pr_uplaceno = 1 AND pred.pr_datum_uplate BETWEEN '".date('Y-m-01', strtotime("-1 months", strtotime(date("Y-m-d 00:00:00"))))."' AND '".date('Y-m-t', strtotime("-1 months", strtotime(date("Y-m-d 23:59:59"))))."' THEN 1 ELSE 0 END) AS uplacenih_PM
															FROM idk_predracuni pred
															JOIN idk_nd_kandidata kan 
															ON pred.pr_kandidat_id = kan.id_broj_nd_kandidata 
															WHERE pred.pr_zaposlenik = ".intval($row3["employee_id"])." 
															AND pred.pr_rata = 1
															AND pred.pr_status is not null
															AND pred.pr_datum_kreiranja >= '2021-07-01 00:00:00'
															AND pred.pr_id IN(SELECT MAX(pr.pr_id) FROM idk_predracuni pr WHERE pr.pr_datum_kreiranja >= '2021-07-01 00:00:00' AND pr.pr_zaposlenik = ".intval($row3["employee_id"])." AND pr.pr_rata = 1 GROUP BY pr.pr_kandidat_id)
														");
														$query7->execute();
														$row7 = $query7->fetch();
														
														$broj_neprihvacenih_CD = is_null($row7['broj_neprihvacenih_CD']) ? 0 : intval($row7['broj_neprihvacenih_CD']);
														$broj_neprihvacenih_KS1 = is_null($row7['broj_neprihvacenih_KS1']) ? 0 : intval($row7['broj_neprihvacenih_KS1']);
														$broj_neprihvacenih_KS2 = is_null($row7['broj_neprihvacenih_KS2']) ? 0 : intval($row7['broj_neprihvacenih_KS2']);
														$broj_neprihvacenih_KS3 = is_null($row7['broj_neprihvacenih_KS3']) ? 0 : intval($row7['broj_neprihvacenih_KS3']);
														$broj_neprihvacenih_KS4 = is_null($row7['broj_neprihvacenih_KS4']) ? 0 : intval($row7['broj_neprihvacenih_KS4']);
														$broj_neprihvacenih_PM = is_null($row7['broj_neprihvacenih_PM']) ? 0 : intval($row7['broj_neprihvacenih_PM']);
														$ukupno_broj_neprihvacenih_KS = $broj_neprihvacenih_KS1 + $broj_neprihvacenih_KS2 + $broj_neprihvacenih_KS3 + $broj_neprihvacenih_KS4;

														$sum_broj_neprihvacenih_CD[$row_on_off] = $sum_broj_neprihvacenih_CD[$row_on_off] + $broj_neprihvacenih_CD;
														$sum_broj_neprihvacenih_KS1[$row_on_off] = $sum_broj_neprihvacenih_KS1[$row_on_off] + $broj_neprihvacenih_KS1;
														$sum_broj_neprihvacenih_KS2[$row_on_off] = $sum_broj_neprihvacenih_KS2[$row_on_off] + $broj_neprihvacenih_KS2;
														$sum_broj_neprihvacenih_KS3[$row_on_off] = $sum_broj_neprihvacenih_KS3[$row_on_off] + $broj_neprihvacenih_KS3;
														$sum_broj_neprihvacenih_KS4[$row_on_off] = $sum_broj_neprihvacenih_KS4[$row_on_off] + $broj_neprihvacenih_KS4;
														$sum_broj_neprihvacenih_PM[$row_on_off] = $sum_broj_neprihvacenih_PM[$row_on_off] + $broj_neprihvacenih_PM;
														$sum_broj_neprihvacenih_KS[$row_on_off] = $sum_broj_neprihvacenih_KS[$row_on_off] + $ukupno_broj_neprihvacenih_KS;

														$broj_izdanih_CD = is_null($row7['broj_izdanih_CD']) ? 0 : intval($row7['broj_izdanih_CD']);
														$broj_izdanih_KS1 = is_null($row7['broj_izdanih_KS1']) ? 0 : intval($row7['broj_izdanih_KS1']);
														$broj_izdanih_KS2 = is_null($row7['broj_izdanih_KS2']) ? 0 : intval($row7['broj_izdanih_KS2']);
														$broj_izdanih_KS3 = is_null($row7['broj_izdanih_KS3']) ? 0 : intval($row7['broj_izdanih_KS3']);
														$broj_izdanih_KS4 = is_null($row7['broj_izdanih_KS4']) ? 0 : intval($row7['broj_izdanih_KS4']);
														$broj_izdanih_PM = is_null($row7['broj_izdanih_PM']) ? 0 : intval($row7['broj_izdanih_PM']);
														$ukupno_broj_izdanih_KS = $broj_izdanih_KS1 + $broj_izdanih_KS2 + $broj_izdanih_KS3 + $broj_izdanih_KS4;
														
														$sum_broj_izdanih_CD[$row_on_off] = $sum_broj_izdanih_CD[$row_on_off] + $broj_izdanih_CD;
														$sum_broj_izdanih_KS1[$row_on_off] = $sum_broj_izdanih_KS1[$row_on_off] + $broj_izdanih_KS1;
														$sum_broj_izdanih_KS2[$row_on_off] = $sum_broj_izdanih_KS2[$row_on_off] + $broj_izdanih_KS2;
														$sum_broj_izdanih_KS3[$row_on_off] = $sum_broj_izdanih_KS3[$row_on_off] + $broj_izdanih_KS3;
														$sum_broj_izdanih_KS4[$row_on_off] = $sum_broj_izdanih_KS4[$row_on_off] + $broj_izdanih_KS4;
														$sum_broj_izdanih_PM[$row_on_off] = $sum_broj_izdanih_PM[$row_on_off] + $broj_izdanih_PM;
														$sum_broj_izdanih_KS[$row_on_off] = $sum_broj_izdanih_KS[$row_on_off] + $ukupno_broj_izdanih_KS;
														
														$broj_uplacenih_CD = is_null($row7['broj_uplacenih_CD']) ? 0 : intval($row7['broj_uplacenih_CD']);
														$broj_uplacenih_KS1 = is_null($row7['broj_uplacenih_KS1']) ? 0 : intval($row7['broj_uplacenih_KS1']);
														$broj_uplacenih_KS2 = is_null($row7['broj_uplacenih_KS2']) ? 0 : intval($row7['broj_uplacenih_KS2']);
														$broj_uplacenih_KS3 = is_null($row7['broj_uplacenih_KS3']) ? 0 : intval($row7['broj_uplacenih_KS3']);
														$broj_uplacenih_KS4 = is_null($row7['broj_uplacenih_KS4']) ? 0 : intval($row7['broj_uplacenih_KS4']);
														$broj_uplacenih_PM = is_null($row7['broj_uplacenih_PM']) ? 0 : intval($row7['broj_uplacenih_PM']);
														$ukupno_broj_uplacenih_PM = $broj_uplacenih_KS1 + $broj_uplacenih_KS2 + $broj_uplacenih_KS3 + $broj_uplacenih_KS4;
														
														$sum_broj_uplacenih_CD[$row_on_off] = $sum_broj_uplacenih_CD[$row_on_off] + $broj_uplacenih_CD;
														$sum_broj_uplacenih_KS1[$row_on_off] = $sum_broj_uplacenih_KS1[$row_on_off] + $broj_uplacenih_KS1;
														$sum_broj_uplacenih_KS2[$row_on_off] = $sum_broj_uplacenih_KS2[$row_on_off] + $broj_uplacenih_KS2;
														$sum_broj_uplacenih_KS3[$row_on_off] = $sum_broj_uplacenih_KS3[$row_on_off] + $broj_uplacenih_KS3;
														$sum_broj_uplacenih_KS4[$row_on_off] = $sum_broj_uplacenih_KS4[$row_on_off] + $broj_uplacenih_KS4;
														$sum_broj_uplacenih_PM[$row_on_off] = $sum_broj_uplacenih_PM[$row_on_off] + $broj_uplacenih_PM;
														$sum_broj_uplacenih_KS[$row_on_off] = $sum_broj_uplacenih_KS[$row_on_off] + $ukupno_broj_uplacenih_PM;
														
														$uplacenih_CD = is_null($row7['uplacenih_CD']) ? 0 : intval($row7['uplacenih_CD']);
														$uplacenih_KS1 = is_null($row7['uplacenih_KS1']) ? 0 : intval($row7['uplacenih_KS1']);
														$uplacenih_KS2 = is_null($row7['uplacenih_KS2']) ? 0 : intval($row7['uplacenih_KS2']);
														$uplacenih_KS3 = is_null($row7['uplacenih_KS3']) ? 0 : intval($row7['uplacenih_KS3']);
														$uplacenih_KS4 = is_null($row7['uplacenih_KS4']) ? 0 : intval($row7['uplacenih_KS4']);
														$uplacenih_PM = is_null($row7['uplacenih_PM']) ? 0 : intval($row7['uplacenih_PM']);
														$ukupno_uplacenih_PM = $uplacenih_KS1 + $uplacenih_KS2 + $uplacenih_KS3 + $uplacenih_KS4;
														
														$sum_uplacenih_CD[$row_on_off] = $sum_uplacenih_CD[$row_on_off] + $uplacenih_CD;
														$sum_uplacenih_KS1[$row_on_off] = $sum_uplacenih_KS1[$row_on_off] + $uplacenih_KS1;
														$sum_uplacenih_KS2[$row_on_off] = $sum_uplacenih_KS2[$row_on_off] + $uplacenih_KS2;
														$sum_uplacenih_KS3[$row_on_off] = $sum_uplacenih_KS3[$row_on_off] + $uplacenih_KS3;
														$sum_uplacenih_KS4[$row_on_off] = $sum_uplacenih_KS4[$row_on_off] + $uplacenih_KS4;
														$sum_uplacenih_PM[$row_on_off] = $sum_uplacenih_PM[$row_on_off] + $uplacenih_PM;
														$sum_uplacenih_KS[$row_on_off] = $sum_uplacenih_KS[$row_on_off] + $ukupno_uplacenih_PM;
														
														if($row_on_off == 'on'){
												?>
													<tr>
														<td class="text-center"><?php echo intval($row3["employee_id"]); ?></td>
														<td class="text-center" style = "<?php echo $row_color; ?>"><?php echo $row3["employee_firstname"]." ".$row3["employee_lastname"];?></td>
														<td class="text-center"><?php echo getIconTeam(intval($row3["employee_id"])); ?></td>
														<td class="text-center"><span style ="font-weight: bold;"><?php echo $pkn." %"; ?></span></td>
														<td class="text-center"><?php echo $broj_uplacenih."/".$broj_izdanih; ?><br><span style ="font-weight: bold;"><?php echo $pn." %"; ?></span></td>
														<td class="text-center"><?php echo $broj_izdanih."/".$broj_prozvanih; ?><br><span style ="font-weight: bold;"><?php echo $cr." %"; ?></span></td>
														<?php 
															if (in_array(("9"), $employee_status) || in_array(("1"), $employee_status) || $logged_employee_id == '285' || $logged_employee_id == '238'){
														?>
														<td class="text-center" style = "border-left: 1px solid #111111;" ><a class="a_link" target="_blank" <?php if($broj_izdanih_CD != 0 OR $broj_neprihvacenih_CD != 0) echo 'href = "statistike.php?page=predracuni&sed=0&id_men='.intval($row3["employee_id"]).'&year='.$year1.'"'; ?>><?php echo getStyleColumn1($broj_neprihvacenih_CD, $broj_izdanih_CD, $broj_uplacenih_CD, $uplacenih_CD); ?></a></td>
														<td class="text-center"><a class="a_link" target="_blank" <?php if($broj_izdanih_KS1 != 0 OR $broj_neprihvacenih_KS1 != 0) echo 'href = "statistike.php?page=predracuni&sed='.$week1.'&id_men='.intval($row3["employee_id"]).'&year='.$year1.'"'; ?>><?php echo getStyleColumn1($broj_neprihvacenih_KS1, $broj_izdanih_KS1, $broj_uplacenih_KS1, $uplacenih_KS1); ?></a></td>
														<td class="text-center"><a class="a_link" target="_blank" <?php if($broj_izdanih_KS2 != 0 OR $broj_neprihvacenih_KS2 != 0) echo 'href = "statistike.php?page=predracuni&sed='.$week2.'&id_men='.intval($row3["employee_id"]).'&year='.$year2.'"'; ?>><?php echo getStyleColumn1($broj_neprihvacenih_KS2, $broj_izdanih_KS2, $broj_uplacenih_KS2, $uplacenih_KS2); ?></a></td>
														<td class="text-center"><a class="a_link" target="_blank" <?php if($broj_izdanih_KS3 != 0 OR $broj_neprihvacenih_KS3 != 0) echo 'href = "statistike.php?page=predracuni&sed='.$week3.'&id_men='.intval($row3["employee_id"]).'&year='.$year3.'"'; ?>><?php echo getStyleColumn1($broj_neprihvacenih_KS3, $broj_izdanih_KS3, $broj_uplacenih_KS3, $uplacenih_KS3); ?></a></td>
														<td class="text-center"><a class="a_link" target="_blank" <?php if($broj_izdanih_KS4 != 0 OR $broj_neprihvacenih_KS4 != 0) echo 'href = "statistike.php?page=predracuni&sed='.$week4.'&id_men='.intval($row3["employee_id"]).'&year='.$year4.'"'; ?>><?php echo getStyleColumn1($broj_neprihvacenih_KS4, $broj_izdanih_KS4, $broj_uplacenih_KS4, $uplacenih_KS4); ?></a></td>
														<td class="text-center"><?php echo getStyleColumn1($broj_neprihvacenih_PM, $broj_izdanih_PM, $broj_uplacenih_PM, $uplacenih_PM); ?></td>
														<td class="text-center"><a class="a_link" target="_blank" <?php if($ukupno_broj_izdanih_KS != 0 OR $ukupno_broj_neprihvacenih_KS != 0) echo 'href = "statistike.php?page=predracuni&sed=-1&id_men='.intval($row3["employee_id"]).'&year='.$year5.'"'; ?>><?php echo getStyleColumn1($ukupno_broj_neprihvacenih_KS, $ukupno_broj_izdanih_KS, $ukupno_broj_uplacenih_PM, $ukupno_uplacenih_PM); ?></a></td>
														<?php 
															}else{
														?>
														<td class="text-center"><?php echo getStyleColumn1($broj_neprihvacenih_CD, $broj_izdanih_CD, $broj_uplacenih_CD, $uplacenih_CD); ?></td>
														<td class="text-center"><?php echo getStyleColumn1($broj_neprihvacenih_KS1, $broj_izdanih_KS1, $broj_uplacenih_KS1, $uplacenih_KS1); ?></td>
														<td class="text-center"><?php echo getStyleColumn1($broj_neprihvacenih_KS2, $broj_izdanih_KS2, $broj_uplacenih_KS2, $uplacenih_KS2); ?></td>
														<td class="text-center"><?php echo getStyleColumn1($broj_neprihvacenih_KS3, $broj_izdanih_KS3, $broj_uplacenih_KS3, $uplacenih_KS3); ?></td>
														<td class="text-center"><?php echo getStyleColumn1($broj_neprihvacenih_KS4, $broj_izdanih_KS4, $broj_uplacenih_KS4, $uplacenih_KS4); ?></td>
														<td class="text-center"><?php echo getStyleColumn1($broj_neprihvacenih_PM, $broj_izdanih_PM, $broj_uplacenih_PM, $uplacenih_PM); ?></td>
														<td class="text-center"><?php echo getStyleColumn1($ukupno_broj_izdanih_KS, $ukupno_broj_izdanih_KS, $ukupno_broj_uplacenih_PM, $ukupno_uplacenih_PM); ?></td>
														<?php
															}
														?>
													</tr>
												<?php 
														}
													}
													
														$sum_pkn['on'] = ($sum_broj_prozvanih['on'] == 0) ? number_format(0, 2, ',', '') : number_format(((($sum_broj_uplacenih['on']) / $sum_broj_prozvanih['on'])*100), 2, ',', ''); // suma procenta krajnje naplate
														$sum_pn['on'] = ($sum_broj_izdanih['on'] == 0) ? number_format(0, 2, ',', '') : number_format(((($sum_broj_uplacenih['on']) / $sum_broj_izdanih['on'])*100), 2, ',', ''); // suma procenta naplate
														$sum_cr['on'] = ($sum_broj_prozvanih['on'] == 0) ? number_format(0, 2, ',', '') : number_format(((($sum_broj_izdanih['on']) / $sum_broj_prozvanih['on'])*100), 2, ',', ''); // suma procenta conversion rate-a
														$sum_pkn['off'] = ($sum_broj_prozvanih['off'] == 0) ? number_format(0, 2, ',', '') : number_format(((($sum_broj_uplacenih['off']) / $sum_broj_prozvanih['off'])*100), 2, ',', ''); // suma procenta krajnje naplate
														$sum_pn['off'] = ($sum_broj_izdanih['off'] == 0) ? number_format(0, 2, ',', '') : number_format(((($sum_broj_uplacenih['off']) / $sum_broj_izdanih['off'])*100), 2, ',', ''); // suma procenta naplate
														$sum_cr['off'] = ($sum_broj_prozvanih['off'] == 0) ? number_format(0, 2, ',', '') : number_format(((($sum_broj_izdanih['off']) / $sum_broj_prozvanih['off'])*100), 2, ',', ''); // suma procenta conversion rate-a
												?>
												</tbody>
												<tfoot>
													<tr>
														<th></th>
														<th></th>
														<th class="text-center" style ="font-weight: bold;">Ukupno (A)</th>
														<th class="text-center"><?php echo $sum_broj_uplacenih['on']."/".$sum_broj_prozvanih['on']; ?><br><span style ="font-weight: bold;"><?php echo $sum_pkn['on']." %"; ?></span></th>
														<th class="text-center"><?php echo $sum_broj_uplacenih['on']."/".$sum_broj_izdanih['on']; ?><br><span style ="font-weight: bold;"><?php echo $sum_pn['on']." %"; ?></span></th>
														<th class="text-center"><?php echo $sum_broj_izdanih['on']."/".$sum_broj_prozvanih['on']; ?><br><span style ="font-weight: bold;"><?php echo $sum_cr['on']." %"; ?></span></th>
													<?php
														if (in_array(("9"), $employee_status) || in_array(("1"), $employee_status) || $logged_employee_id == '285' || $logged_employee_id == '238'){
													?>
														<td class="text-center" style = "border-left: 1px solid #111111;" ><a class="a_link" target="_blank" <?php if($sum_broj_izdanih_CD['on'] != 0 OR $sum_broj_neprihvacenih_CD['on'] != 0) echo 'href = "statistike.php?page=predracuni&branch_id='.$branch_id.'&sed=0&id_men='. invertSign(1) .'&year='.$year1.'"'; ?>><?php echo getStyleColumn1($sum_broj_neprihvacenih_CD['on'], $sum_broj_izdanih_CD['on'], $sum_broj_uplacenih_CD['on'], $sum_uplacenih_CD['on']); ?></a></td>
														<td class="text-center"><a class="a_link" target="_blank" <?php if($sum_broj_izdanih_KS1['on'] != 0 OR $sum_broj_neprihvacenih_KS1['on'] != 0) echo 'href = "statistike.php?page=predracuni&branch_id='.$branch_id.'&sed='.$week1.'&id_men='. invertSign(2) .'&year='.$year1.'"'; ?>><?php echo getStyleColumn1($sum_broj_neprihvacenih_KS1['on'], $sum_broj_izdanih_KS1['on'], $sum_broj_uplacenih_KS1['on'], $sum_uplacenih_KS1['on']); ?></a></td>
														<td class="text-center"><a class="a_link" target="_blank" <?php if($sum_broj_izdanih_KS2['on'] != 0 OR $sum_broj_neprihvacenih_KS2['on'] != 0) echo 'href = "statistike.php?page=predracuni&branch_id='.$branch_id.'&sed='.$week2.'&id_men='. invertSign(3) .'&year='.$year2.'"'; ?>><?php echo getStyleColumn1($sum_broj_neprihvacenih_KS2['on'], $sum_broj_izdanih_KS2['on'], $sum_broj_uplacenih_KS2['on'], $sum_uplacenih_KS2['on']); ?></a></td>
														<td class="text-center"><a class="a_link" target="_blank" <?php if($sum_broj_izdanih_KS3['on'] != 0 OR $sum_broj_neprihvacenih_KS3['on'] != 0) echo 'href = "statistike.php?page=predracuni&branch_id='.$branch_id.'&sed='.$week3.'&id_men='. invertSign(4) .'&year='.$year3.'"'; ?>><?php echo getStyleColumn1($sum_broj_neprihvacenih_KS3['on'], $sum_broj_izdanih_KS3['on'], $sum_broj_uplacenih_KS3['on'], $sum_uplacenih_KS3['on']); ?></a></td>
														<td class="text-center"><a class="a_link" target="_blank" <?php if($sum_broj_izdanih_KS4['on'] != 0 OR $sum_broj_neprihvacenih_KS4['on'] != 0) echo 'href = "statistike.php?page=predracuni&branch_id='.$branch_id.'&sed='.$week4.'&id_men='. invertSign(6) .'&year='.$year4.'"'; ?>><?php echo getStyleColumn1($sum_broj_neprihvacenih_KS4['on'], $sum_broj_izdanih_KS4['on'], $sum_broj_uplacenih_KS4['on'], $sum_uplacenih_KS4['on']); ?></a></td>
														<td class="text-center"><?php echo getStyleColumn1($sum_broj_neprihvacenih_PM['on'], $sum_broj_izdanih_PM['on'], $sum_broj_uplacenih_PM['on'], $sum_uplacenih_PM['on']); ?></td>
														<td class="text-center"><a class="a_link" target="_blank" <?php if($sum_broj_izdanih_KS['on'] != 0 OR $sum_broj_neprihvacenih_KS['on'] != 0) echo 'href = "statistike.php?page=predracuni&branch_id='.$branch_id.'&sed=-1&id_men='. invertSign(6) .'&year='.$year5.'"'; ?>><?php echo getStyleColumn1($sum_broj_neprihvacenih_KS['on'], $sum_broj_izdanih_KS['on'], $sum_broj_uplacenih_KS['on'], $sum_uplacenih_KS['on']); ?></a></td>
													<?php
														}else{
													?>
														<th class="text-center" style ="font-weight: bold;"><?php echo getStyleColumn1($sum_broj_neprihvacenih_CD['on'], $sum_broj_izdanih_CD['on'], $sum_broj_uplacenih_CD['on'], $sum_uplacenih_CD['on']); ?></th>
														<th class="text-center" style ="font-weight: bold;"><?php echo getStyleColumn1($sum_broj_neprihvacenih_KS1['on'], $sum_broj_izdanih_KS1['on'], $sum_broj_uplacenih_KS1['on'], $sum_uplacenih_KS1['on']); ?></th>
														<th class="text-center" style ="font-weight: bold;"><?php echo getStyleColumn1($sum_broj_neprihvacenih_KS2['on'], $sum_broj_izdanih_KS2['on'], $sum_broj_uplacenih_KS2['on'], $sum_uplacenih_KS2['on']); ?></th>
														<th class="text-center" style ="font-weight: bold;"><?php echo getStyleColumn1($sum_broj_neprihvacenih_KS3['on'], $sum_broj_izdanih_KS3['on'], $sum_broj_uplacenih_KS3['on'], $sum_uplacenih_KS3['on']); ?></th>
														<th class="text-center" style ="font-weight: bold;"><?php echo getStyleColumn1($sum_broj_neprihvacenih_KS4['on'], $sum_broj_izdanih_KS4['on'], $sum_broj_uplacenih_KS4['on'], $sum_uplacenih_KS4['on']); ?></th>
														<th class="text-center" style ="font-weight: bold;"><?php echo getStyleColumn1($sum_broj_neprihvacenih_PM['on'], $sum_broj_izdanih_PM['on'], $sum_broj_uplacenih_PM['on'], $sum_uplacenih_PM['on']); ?></th>
														<th class="text-center" style ="font-weight: bold;"><?php echo getStyleColumn1($sum_broj_neprihvacenih_KS['on'], $sum_broj_izdanih_KS['on'], $sum_broj_uplacenih_KS['on'], $sum_uplacenih_KS['on']); ?></th>
													<?php
														}
													?>
													</tr>
													<tr>
														<th></th>
														<th></th>
														<th class="text-center" style ="font-weight: bold;">Ukupno (D)</th>
														<th class="text-center"><?php echo $sum_broj_uplacenih['off']."/".$sum_broj_prozvanih['off']; ?><br><span style ="font-weight: bold;"><?php echo $sum_pkn['off']." %"; ?></span></th>
														<th class="text-center"><?php echo $sum_broj_uplacenih['off']."/".$sum_broj_izdanih['off']; ?><br><span style ="font-weight: bold;"><?php echo $sum_pn['off']." %"; ?></span></th>
														<th class="text-center"><?php echo $sum_broj_izdanih['off']."/".$sum_broj_prozvanih['off']; ?><br><span style ="font-weight: bold;"><?php echo $sum_cr['off']." %"; ?></span></th>
														<th class="text-center" style ="font-weight: bold;"><?php echo getStyleColumn1($sum_broj_neprihvacenih_CD['off'], $sum_broj_izdanih_CD['off'], $sum_broj_uplacenih_CD['off'], $sum_uplacenih_CD['off']); ?></th>
														<th class="text-center" style ="font-weight: bold;"><?php echo getStyleColumn1($sum_broj_neprihvacenih_KS1['off'], $sum_broj_izdanih_KS1['off'], $sum_broj_uplacenih_KS1['off'], $sum_uplacenih_KS1['off']); ?></th>
														<th class="text-center" style ="font-weight: bold;"><?php echo getStyleColumn1($sum_broj_neprihvacenih_KS2['off'], $sum_broj_izdanih_KS2['off'], $sum_broj_uplacenih_KS2['off'], $sum_uplacenih_KS2['off']); ?></th>
														<th class="text-center" style ="font-weight: bold;"><?php echo getStyleColumn1($sum_broj_neprihvacenih_KS3['off'], $sum_broj_izdanih_KS3['off'], $sum_broj_uplacenih_KS3['off'], $sum_uplacenih_KS3['off']); ?></th>
														<th class="text-center" style ="font-weight: bold;"><?php echo getStyleColumn1($sum_broj_neprihvacenih_KS4['off'], $sum_broj_izdanih_KS4['off'], $sum_broj_uplacenih_KS4['off'], $sum_uplacenih_KS4['off']); ?></th>
														<th class="text-center" style ="font-weight: bold;"><?php echo getStyleColumn1($sum_broj_neprihvacenih_PM['off'], $sum_broj_izdanih_PM['off'], $sum_broj_uplacenih_PM['off'], $sum_uplacenih_PM['off']); ?></th>
														<th class="text-center" style ="font-weight: bold;"><?php echo getStyleColumn1($sum_broj_neprihvacenih_KS['off'], $sum_broj_izdanih_KS['off'], $sum_broj_uplacenih_KS['off'], $sum_uplacenih_KS['off']); ?></th>
													</tr>
												</tfoot>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?php
					}else{
						echo '
							<div class="alert material-alert material-alert_danger">
								<h4>Problem sa linkom!</h4>
								<p>Kontaktirajte administratora za pomoć.</p>
								<br />
								<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
							</div>
						';
					}
				break;
				

			}
		?>
	</div>
</body>