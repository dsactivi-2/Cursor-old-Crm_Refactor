<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus = explode( ',' , getEmployeeStatus());

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: tiketi?page=list");
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Tiketi | <?php getTitle(); ?></title>
		<?php 
			include('includes/head.php');
			if (in_array($getUserIp, $getIpWhiteList))
			{
		?>
	</head>

<body>
	<header>
		<?php 
			include('header.php'); 
		?>
	</header>
	<div id="sidebar">
		<?php 
			include('menu.php'); 
		?>
	</div>
	<div id="content">
		<div class="container-fluid">
			<?php
				switch ($page)
				{
					case "list":
			?>
				 <script>
                    $(document).ready(function(){
                        // get the tab from url
                        var hash = window.location.hash;
                        // if a hash is present (when you come to this page)
                        console.log(hash);
                        if (hash !='') {
                            // show the tab
                            $('.nav-tabs a[href="' + hash + '"]').tab('show');
                        }
                    });
                </script>
				<div class="row">
					<div class="col-xs-8">
						<h1><i class="fa fa-ticket idk_color_green" aria-hidden="true"></i> Tiketi</h1>
					</div>
					<div class="col-xs-4 text-right idk_margin_top10">
						<a href="<?php getSiteURL(); ?>tiketi?page=novi_tiket" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-send" aria-hidden="true"></i> <span>Pošalji tiket</span></a>
					</div>
					<div class="col-xs-12">
						<hr />
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-12">
						<div class="content_box">
							<?php
								if(isset($_GET['mess'])) {
									$mess = $_GET['mess'];
								}else{
									$mess = 0;
								}

								if($mess == 1){
									echo '<div class="alert material-alert material-alert_success">Uspješno ste poslali poruku.</div>';
								}elseif($mess == 2){
									echo '<div class="alert material-alert material-alert_success">Uspješno ste obrisali poruku.</div>';
								}
							?>
							<!-- Start CSS -->
							<style>
								.hitnost_izgled{
									float: right;
								}
								.opis_izgled{
									width: 100%;
									text-align: justify;
									background: white;
									padding: 10px;
								}
								.opis_izgled_k{
									width: 100%;
									text-align: justify;
									background: white;
									padding: 10px;
									background: rgba(104, 195, 104, 0.50);
									border-radius: 15px;
								}
								.razlog_reaktivacije_izgled{
									width: 100%;
									padding: 10px;
									resize: none;
									background: white;
								}
								.predmet_izgled{
									word-break: break-all;
								}
								.modal-dialog{
									width: 800px;
								}
								.modal-body{
									padding: 50px;
									padding-bottom: 10px;
								}
								.modal-footer{
									padding-right: 50px;
									padding-left: 50px;
								}
								#modal_informacije {
									font-size: 17px;
									color: black;
								}
								#modal_naslov {
									font-size: 20px;
									color: black;
								}
								#modal_linija {
									margin-top: 10px;
									margin-bottom: 10px;
									border-color: darkgrey;
								}
								.material-label{
									padding: 2px 5px;
								}
								.material-dropdown-menu .material-dropdown-menu__link{
									padding-left: 10px;
									padding-right: 10px;
									text-align: center;
								}
								
							</style>
							<!-- EndCSS -->
							<h3>Kratke upute za korištenje možete pogledati <a href="<?php getSiteURL(); ?>files/dokumenti_tiketi/ticketing_system.pdf" target="_BLANK">ovdje</a>.</h3><br>
							<div id="myTabs" class="panel-group material-tabs-group">
								<ul class="nav nav-tabs material-tabs material-tabs_success">
									<li style = "margin-left: 25px;"class="active"><a href="#novi_ticketi_list" class="material-tabs__tab-link" data-toggle="tab">Novi tiketi</a></li>
									<li><a href="#ticketi_u_toku_list" class="material-tabs__tab-link" data-toggle="tab">Tiketi u toku</a></li>
									<li><a href="#obradjeni_tiketi_list" class="material-tabs__tab-link" data-toggle="tab">Završeni tiketi</a></li>
									<li style = "float: right; margin-right: 25px;"><a href="#poslani_tiketi_list" class="material-tabs__tab-link" data-toggle="tab">Poslani tiketi</a></li>
								</ul>
								<div class="tab-content materail-tabs-content">
									<!--**************************************************************-->
									<!-- Novi tiketi start-->
									<!--**************************************************************-->
									<div class="tab-pane fade active in" id="novi_ticketi_list">
										<script type="text/javascript">
											$(document).ready( function () {
												$('#novi_ticketi_list_dt').DataTable( { 

													responsive: true,

													"order": [[ 7, "desc" ]],

													"aoColumns": [
															
															{"width": "5%"},
															{"width": "15%"},
															{"width": "10%"},
															{"width": "10%"},
															{"width": "15%"},
															{"width": "10%"},
															{"width": "15%"},
															{"width": "10%"},
															{"width": "10%"}
														]
												});
											} );
										</script>
										<table id="novi_ticketi_list_dt" class="display" cellspacing="0" width="100%">
											<thead>
												<tr>
													<th class="text-center">Broj</th>
													<th class="text-center">Predmet</th>
													<th class="text-center">Hitnost</th>
													<th class="text-center">Pošiljaoc odjel</th>
													<th class="text-center">Pošiljaoc</th>
													<th class="text-center">Primatelj odjel</th>
													<th class="text-center">Primatelj</th>
													<th class="text-center">Vrijeme kreiranja</th>
													<th class="text-center">Akcija</th>
												</tr>
											</thead>
											<tbody>
												<?php
													$novi_ticketi = $db->prepare("
																		SELECT *
																		FROM idk_ticketi
																		WHERE ticket_status = :ticket_status AND (ticket_primatelj = :ticket_primatelj OR ticket_primatelj_odjel = :ticket_primatelj_odjel)");

													$novi_ticketi->execute(array(
																		':ticket_status' => 1 ,
																		':ticket_primatelj' => $logged_employee_id,
																		':ticket_primatelj_odjel' => $logged_employee_department_id));

													while($novi_ticketi_row = $novi_ticketi->fetch()){
														//******************************************************************************************************
														$tiket_id = $novi_ticketi_row['ticket_id'];
														$tiket_status = $novi_ticketi_row['ticket_status'];
														$tiket_predmet = $novi_ticketi_row['ticket_predmet'];
														$tiket_opis = $novi_ticketi_row['ticket_opis'];
														$tiket_hitnost = $novi_ticketi_row['ticket_hitnost'];
														$tiket_posiljaoc = $novi_ticketi_row['ticket_posiljaoc'];
														$tiket_primatelj = $novi_ticketi_row['ticket_primatelj'];
														$tiket_primatelj_odjel = $novi_ticketi_row['ticket_primatelj_odjel'];
														$tiket_vrijeme_kreiranja = date('d.m.Y H:i', strtotime($novi_ticketi_row['ticket_vrijeme_kreiranja']));
														$tiket_vrijeme_prihvatanja = date('d.m.Y H:i', strtotime($novi_ticketi_row['ticket_vrijeme_prihvatanja']));
														$tiket_vrijeme_zavrsetka = date('d.m.Y H:i', strtotime($novi_ticketi_row['ticket_vrijeme_zavrsetka']));
														$tiket_dokument = $novi_ticketi_row['ticket_dokument'];
														//******************************************************************************************************
														
														//Provjera ekstenzije dokumenta
														if($tiket_dokument !== "1"){
															
															if (strpos($tiket_dokument, '.jpg') !== false OR strpos($tiket_dokument, '.png') !== false){
																$tiket_dokument_icon = '<i class="fa fa-file-image-o fa-3x" aria-hidden="true"></i>';
															}
															else if(strpos($tiket_dokument, '.pdf') !== false){
																$tiket_dokument_icon = '<i class="fa fa-file-pdf-o fa-3x" aria-hidden="true"></i>';
															}
															else if(strpos($tiket_dokument, '.doc') !== false OR strpos($tiket_dokument, '.docx') !== false){
																$tiket_dokument_icon = '<i class="fa fa-file-word-o fa-3x" aria-hidden="true"></i>';
															}
															else if(strpos($tiket_dokument, '.xls') !== false OR strpos($tiket_dokument, '.xlsx') !== false OR strpos($tiket_dokument, '.csv') !== false){
																$tiket_dokument_icon = '<i class="fa fa-file-excel-o fa-3x" aria-hidden="true"></i>';
															}
															else if(strpos($tiket_dokument, '.txt') !== false ){
																$tiket_dokument_icon = '<i class="fa fa-file-text-o fa-3x" aria-hidden="true"></i>';
															}
															else{
																$tiket_dokument_icon = '<i class="fa fa-file-powerpoint-o fa-3x" aria-hidden="true"></i>';
															}
															
															$tiket_dokument_download = '<a href="'.getSiteUrlr().'files/dokumenti_tiketi/'.$tiket_dokument.'" class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK">'.$tiket_dokument_icon.'</a>';
															
														}
														else{
															$tiket_dokument_download = 'Nije priložen dokument.';
														}
														
														//Prikaz hitnosti tiketa
														if($tiket_hitnost == 1){
															$tiket_hitnost_ispis = '<span class="label label-success material-label material-label_success main-container__column text-left">LOW</span>';
														}
														else if($tiket_hitnost == 2){
															$tiket_hitnost_ispis = '<span class="label label-warning material-label material-label_warning main-container__column text-left">MEDIUM</span>';
														}
														else if($tiket_hitnost == 3){
															$tiket_hitnost_ispis = '<span class="label label-danger material-label material-label_danger main-container__column text-left">HIGH</span>';
														}
														else{
															$tiket_hitnost_ispis = "Nije definisano";
														}
														
														//Prikaz odjela tiketa
														if($tiket_primatelj_odjel == 1){
															$tiket_primatelj_odjel_ispis = "UPRAVA";
														}
														else if($tiket_primatelj_odjel == 2){
															$tiket_primatelj_odjel_ispis = "FINANCIJE";
														}
														else if($tiket_primatelj_odjel == 3){
															$tiket_primatelj_odjel_ispis = "PRODAJA";
														}
														else if($tiket_primatelj_odjel == 4){
															$tiket_primatelj_odjel_ispis = "OBRADA";
														}
														else if($tiket_primatelj_odjel == 5){
															$tiket_primatelj_odjel_ispis = "SVE ZA VIZE";
														}
														else if($tiket_primatelj_odjel == 6){
															$tiket_primatelj_odjel_ispis = "MARKETING";
														}
														else if($tiket_primatelj_odjel == 7){
															$tiket_primatelj_odjel_ispis = "TEHIKA";
														}
														else if($tiket_primatelj_odjel == 8){
															$tiket_primatelj_odjel_ispis = "DEVELOPMENT";
														}
														else{
															$tiket_primatelj_odjel_ispis = "Nije definisano";
														}
														
														//Prikaz posiljaoca
														$tiket_posiljaoc_ispis = getZaposlenikimeR($tiket_posiljaoc);
														
														//Prikaz odjela posiljaoca
														$tiket_posiljaoc_odjel = getZaposlenikDepartmentR($tiket_posiljaoc);
														
														if($tiket_posiljaoc_odjel == 1){
															$tiket_posiljaoc_odjel_ispis = "UPRAVA";
														}
														else if($tiket_posiljaoc_odjel == 2){
															$tiket_posiljaoc_odjel_ispis = "FINANCIJE";
														}
														else if($tiket_posiljaoc_odjel == 3){
															$tiket_posiljaoc_odjel_ispis = "PRODAJA";
														}
														else if($tiket_posiljaoc_odjel == 4){
															$tiket_posiljaoc_odjel_ispis = "OBRADA";
														}
														else if($tiket_posiljaoc_odjel == 5){
															$tiket_posiljaoc_odjel_ispis = "SVE ZA VIZE";
														}
														else if($tiket_posiljaoc_odjel == 6){
															$tiket_posiljaoc_odjel_ispis = "MARKETING";
														}
														else if($tiket_posiljaoc_odjel == 7){
															$tiket_posiljaoc_odjel_ispis = "TEHIKA";
														}
														else if($tiket_posiljaoc_odjel == 8){
															$tiket_posiljaoc_odjel_ispis = "DEVELOPMENT";
														}
														else{
															$tiket_posiljaoc_odjel_ispis = "Nije definisano";
														}
														//Prikaz primatelja
														if($tiket_primatelj == 0){ 
															$tiket_primatelj_ispis = '<span class="label label-info material-label material-label_info main-container__column text-left">Odjel</span>';
														}
														else{
															$tiket_primatelj_ispis = getZaposlenikimeR($tiket_primatelj);
														}
												?>
												<tr>
													<td class="text-center"><?php echo $tiket_id; ?></td>
													<td class="text-center"><?php echo substr($tiket_predmet, 0, 15)."..." ?></td>
													<td class="text-center"><?php echo $tiket_hitnost_ispis; ?></td>
													<td class="text-center"><?php echo $tiket_posiljaoc_odjel_ispis; ?></td>
													<td class="text-center"><?php echo $tiket_posiljaoc_ispis; ?></td>
													<td class="text-center"><?php echo $tiket_primatelj_odjel_ispis; ?></td>
													<td class="text-center"><?php echo $tiket_primatelj_ispis; ?></td>
													<td class="text-center"><?php echo $tiket_vrijeme_kreiranja; ?></td>
													<td class="text-center">
														<div class="btn-group material-btn-group">
															<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_success idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
															<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_success idk_dropdown_table" role="menu">
																<li>
																	<a id = "otvori_novi_tiket_podaci" href="#" class="material-dropdown-menu__link" data-toggle="modal" data-target="#otvori_novi_tiket" 
																		data-address_t ="tiketi?page=prihvatanje_tiketa&id=<?php echo $tiket_id; ?>"
																		data-id_t ="<?php echo $tiket_id; ?>"
																		data-status_t ="<?php echo $tiket_status; ?>"
																		data-predmet_t = '<?php echo str_replace("'", "`", $tiket_predmet); ?>'
																		data-opis_t ='<?php echo str_replace("'", "`", $tiket_opis); ?>'
																		data-hitnost_t = '<?php echo $tiket_hitnost; ?>'
																		data-posiljaoc_t ="<?php echo $tiket_posiljaoc_ispis; ?>"
																		data-primatelj_t ='<?php echo $tiket_primatelj_ispis; ?>'
																		data-primatelj_odjel_t ="<?php echo $tiket_primatelj_odjel_ispis; ?>"
																		data-posiljaoc_odjel_t ="<?php echo $tiket_posiljaoc_odjel_ispis; ?>"
																		data-v_kreiranja_t ="<?php echo $tiket_vrijeme_kreiranja; ?>"
																		data-v_prihvatanja_t ="<?php echo $tiket_vrijeme_prihvatanja; ?>"
																		data-v_zavrsetka_t ="<?php echo $tiket_vrijeme_zavrsetka; ?>"
																		data-dokument_t = '<?php echo $tiket_dokument_download; ?>'
																		>
																		<i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori
																	</a>
																</li>
															</ul>
														</div>
													</td>
												</tr>
												<?php	}	?>
											</tbody>
										</table>
										
									</div>
									<!-- SCRIPT: OTVARANJE NOVOG TIKETA -->
									<script>
										$(document).on("click","#otvori_novi_tiket_podaci",function() {
											var address_t1 = $(this).data("address_t");
											document.getElementById("prihvatanje_tiketa_href").href = address_t1;
											var id_t1 = $(this).data("id_t");
											var status_t1 = $(this).data("status_t");
											var predmet_t1 = $(this).data("predmet_t");
											var opis_t1 = $(this).data("opis_t");  
											var hitnost_t1 = $(this).data("hitnost_t");
											var posiljaoc_t1 = $(this).data("posiljaoc_t");
											var primatelj_t1 = $(this).data("primatelj_t");
											var primatelj_odjel_t1 = $(this).data("primatelj_odjel_t");
											var posiljaoc_odjel_t1 = $(this).data("posiljaoc_odjel_t");
											var v_kreiranja_t1 = $(this).data("v_kreiranja_t");
											var v_prihvatanja_t1 = $(this).data("v_prihvatanja_t");
											var v_zavrsetka_t1 = $(this).data("v_zavrsetka_t");
											var dokument_t1 = $(this).data("dokument_t");
											$("#id_t11").html(id_t1);
											$("#status_t11").html(status_t1);
											$("#predmet_t11").html(predmet_t1);
											$("#opis_t11").html(opis_t1);
											$("#hitnost_t11").val(hitnost_t1).find("option[value=" + hitnost_t1 +"]").attr('selected', true);
											$("#hitnost_t11").selectpicker('refresh')
											$("#posiljaoc_t11").html(posiljaoc_t1);
											$("#primatelj_t11").html(primatelj_t1);
											$("#primatelj_odjel_t11").html(primatelj_odjel_t1);
											$("#posiljaoc_odjel_t11").html(primatelj_odjel_t1);
											$("#v_kreiranja_t11").html(v_kreiranja_t1);
											$("#v_prihvatanja_t11").html(v_prihvatanja_t1);
											$("#v_zavrsetka_t11").html(v_zavrsetka_t1);
											$("#dokument_t11").html(dokument_t1);
											
											$.ajax({
												url: 'ajax_data.php?page=ispis_komentara_n',
												type: 'POST',
												data: {'id_t11_comment_ispis':id_t1},
												dataType: 'html',
												success: function(data) {
													$("#forma_komentar_ispis_n").html(data);
												},
												error: function (xhr, ajaxOptions, thrownError) {
													alert(xhr.status);
													alert(thrownError);
												}
											});
											
										});
										$(document).on("change","#hitnost_t11",function() {
											var hitnost_t11_new = $("#hitnost_t11").val();
											var id_t11_new = $("#id_t11").text();
											
											$.ajax({
												url: 'tiketi.php?page=hitnost_edit',
												type: 'POST',
												data: {'hitnost_t11_new':hitnost_t11_new, 'id_t11_new':id_t11_new},
												dataType: 'html',
												success: function(data) {
													alert("Uspješno promijenjena hitnost");
												},
												error: function (xhr, ajaxOptions, thrownError) {
													alert(xhr.status);
													alert(thrownError);
												}
											});
											
										});
									</script>
									<!-- MODAL: PRIHVATI TIKET START-->
									<div class="modal material-modal material-modal_success fade" id="otvori_novi_tiket">
										<div class="modal-dialog">
											<div class="modal-content material-modal__content">
												
												<div class="modal-body material-modal__body">
													<div id = "modal_naslov" class="row">
														<div class="col-xs-12 text-center">
															<p><i class="fa fa-tag" aria-hidden="true"></i> Novi tiket info </p>
														</div>
													</div>
													<div id = "modal_informacije" class="row">
														<div class="col-xs-1 text-center">
															<p><i class="fa fa-id-card" aria-hidden="true"></i></p>
															<p><i class="fa fa-users" aria-hidden="true"></i></p>
															<p><i class="fa fa-user-circle-o" aria-hidden="true"></i></p>
															<p><i class="fa fa-users" aria-hidden="true"></i></p>
															<p><i class="fa fa-user-circle" aria-hidden="true"></i></p>
															<p><i class="fa fa-calendar" aria-hidden="true"></i></p>
														</div>
														<div class="col-xs-7 text-left">
															<p>Broj tiketa: <span id="id_t11"></span></p>
															<p>Odjel posiljaoca: <span id="posiljaoc_odjel_t11"></span></p>
															<p>Posiljaoc: <span id="posiljaoc_t11"></span></p>
															<p>Odjel primatelja: <span id="primatelj_odjel_t11"></span></p>
															<p>Primatelj: <span id="primatelj_t11"></span></p>
															<p>Datum kreiranja: <span id="v_kreiranja_t11"></span></p>
														</div>
														<!--<div class="col-sm-4 text-right">
															<p><span id="hitnost_t11"></span></p>
														</div>-->
													
														<div class="col-xs-4 text-right">
															<select class = "selectpicker" id="hitnost_t11">
																<option value="3" data-content = "<span class='label label-danger material-label material-label_danger main-container__column text-left'>HIGH</span>" >HIGH</option>
																<option value="2" data-content = "<span class='label label-warning material-label material-label_warning main-container__column text-left'>MEDIUM</span>">MEDIUM</option>
																<option value="1" data-content = "<span class='label label-success material-label material-label_success main-container__column text-left'>LOW</span>" >LOW</option> 
															</select>
														</div>
													</div>
													<hr id = "modal_linija">
													<div id = "modal_informacije" class="row">
														<div class="col-xs-1 text-center">
															<p><i class="fa fa-info" aria-hidden="true"></i></p>
														</div>
														<div class="col-xs-8 text-left">
															<p>Predmet: <span class = "predmet_izgled" id="predmet_t11"></span></p>
														</div>
														<div class="col-xs-1 text-center">
															<p><i class="fa fa-file" aria-hidden="true"></i></p>
														</div>
														<div class="col-xs-2 text-left">
															<p>Dokumenti</p>
														</div>
													</div>
													<div class="row">
														<div class="col-xs-9 text-center">
															<p class = "opis_izgled" id="opis_t11" ></p>
														</div>
														<div class="col-xs-3 text-center" style = "padding: 10px;">
															<p><span id = "dokument_t11"></span></p>
														</div>
													</div>
													<div id = "forma_komentar_ispis_n" class="row">
														
													</div>
												</div>
												<div class="modal-footer material-modal__footer">
													<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
													<a id="prihvatanje_tiketa_href" href="#"><button class="btn btn-primary material-btn material-btn_success">Prihvati</button></a>
												</div>
											</div>
										</div>
									</div>
									<!-- MODAL: PRIHVATI TIKET END-->
									<!--**************************************************************-->
									<!-- Tiketi u toku start-->
									<!--**************************************************************-->
									<div class="tab-pane fade" id="ticketi_u_toku_list">
										<script type="text/javascript">
											$(document).ready( function () {
												$('#ticketi_u_toku_list_dt').DataTable( {

													responsive: true,

													"order": [[ 7, "desc" ]],

													"aoColumns": [
															
															{"width": "5%"},
															{"width": "15%"},
															{"width": "10%"},
															{"width": "10%"},
															{"width": "10%"},
															{"width": "10%"},
															{"width": "15%"},
															{"width": "15%"},
															{"width": "10%"}
														]
												});
											} );
										</script>
										<table id="ticketi_u_toku_list_dt" class="display" cellspacing="0" width="100%">
											<thead>
												<tr>
													<th class="text-center">Broj</th>
													<th class="text-center">Predmet</th>
													<th class="text-center">Hitnost</th>
													<th class="text-center">Pošiljaoc odjel</th>
													<th class="text-center">Pošiljaoc</th>
													<th class="text-center">Prihvatio</th>
													<th class="text-center">Vrijeme kreiranja</th>
													<th class="text-center">Vrijeme prihvatanja</th>
													<th class="text-center">Akcija</th>
												</tr>
											</thead>
											<tbody>
												<?php
													$u_toku_ticketi = $db->prepare("
																		SELECT *
																		FROM idk_ticketi
																		WHERE ticket_status = :ticket_status AND ticket_primatelj = :ticket_primatelj");

													$u_toku_ticketi->execute(array(
																		':ticket_status' => 2,
																		':ticket_primatelj' => $logged_employee_id));

													while($u_toku_ticketi_row = $u_toku_ticketi->fetch()){
														//******************************************************************************************************
														$tiket_id = $u_toku_ticketi_row['ticket_id'];
														$tiket_status = $u_toku_ticketi_row['ticket_status'];
														$tiket_predmet = $u_toku_ticketi_row['ticket_predmet'];
														$tiket_opis = $u_toku_ticketi_row['ticket_opis'];
														$tiket_hitnost = $u_toku_ticketi_row['ticket_hitnost'];
														$tiket_posiljaoc = $u_toku_ticketi_row['ticket_posiljaoc'];
														$tiket_primatelj = $u_toku_ticketi_row['ticket_primatelj'];
														$tiket_primatelj_odjel = $u_toku_ticketi_row['ticket_primatelj_odjel'];
														$tiket_vrijeme_kreiranja = date('d.m.Y H:i', strtotime($u_toku_ticketi_row['ticket_vrijeme_kreiranja']));
														$tiket_vrijeme_prihvatanja = date('d.m.Y H:i', strtotime($u_toku_ticketi_row['ticket_vrijeme_prihvatanja']));
														$tiket_vrijeme_zavrsetka = date('d.m.Y H:i', strtotime($u_toku_ticketi_row['ticket_vrijeme_zavrsetka']));
														$tiket_dokument = $u_toku_ticketi_row['ticket_dokument'];
														//******************************************************************************************************
														
														//Provjera ekstenzije dokumenta
														if($tiket_dokument !== "1"){
															
															if (strpos($tiket_dokument, '.jpg') !== false OR strpos($tiket_dokument, '.png') !== false){
																$tiket_dokument_icon = '<i class="fa fa-file-image-o fa-3x" aria-hidden="true"></i>';
															}
															else if(strpos($tiket_dokument, '.pdf') !== false){
																$tiket_dokument_icon = '<i class="fa fa-file-pdf-o fa-3x" aria-hidden="true"></i>';
															}
															else if(strpos($tiket_dokument, '.doc') !== false OR strpos($tiket_dokument, '.docx') !== false){
																$tiket_dokument_icon = '<i class="fa fa-file-word-o fa-3x" aria-hidden="true"></i>';
															}
															else if(strpos($tiket_dokument, '.xls') !== false OR strpos($tiket_dokument, '.xlsx') !== false OR strpos($tiket_dokument, '.csv') !== false){
																$tiket_dokument_icon = '<i class="fa fa-file-excel-o fa-3x" aria-hidden="true"></i>';
															}
															else if(strpos($tiket_dokument, '.txt') !== false ){
																$tiket_dokument_icon = '<i class="fa fa-file-text-o fa-3x" aria-hidden="true"></i>';
															}
															else{
																$tiket_dokument_icon = '<i class="fa fa-file-powerpoint-o fa-3x" aria-hidden="true"></i>';
															}
															
															$tiket_dokument_download = '<a href="'.getSiteUrlr().'files/dokumenti_tiketi/'.$tiket_dokument.'" class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK">'.$tiket_dokument_icon.'</a>';
															
														}
														else{
															$tiket_dokument_download = 'Nije priložen dokument.';
														}
														
														//Prikaz hitnosti tiketa
														if($tiket_hitnost == 1){
															$tiket_hitnost_ispis = '<span class="label label-success material-label material-label_success main-container__column text-left">LOW</span>';
														}
														else if($tiket_hitnost == 2){
															$tiket_hitnost_ispis = '<span class="label label-warning material-label material-label_warning main-container__column text-left">MEDIUM</span>';
														}
														else if($tiket_hitnost == 3){
															$tiket_hitnost_ispis = '<span class="label label-danger material-label material-label_danger main-container__column text-left">HIGH</span>';
														}
														else{
															$tiket_hitnost_ispis = "Nije definisano";
														}
														
														//Prikaz odjela tiketa
														if($tiket_primatelj_odjel == 1){
															$tiket_primatelj_odjel_ispis = "UPRAVA";
														}
														else if($tiket_primatelj_odjel == 2){
															$tiket_primatelj_odjel_ispis = "FINANCIJE";
														}
														else if($tiket_primatelj_odjel == 3){
															$tiket_primatelj_odjel_ispis = "PRODAJA";
														}
														else if($tiket_primatelj_odjel == 4){
															$tiket_primatelj_odjel_ispis = "OBRADA";
														}
														else if($tiket_primatelj_odjel == 5){
															$tiket_primatelj_odjel_ispis = "SVE ZA VIZE";
														}
														else if($tiket_primatelj_odjel == 6){
															$tiket_primatelj_odjel_ispis = "MARKETING";
														}
														else if($tiket_primatelj_odjel == 7){
															$tiket_primatelj_odjel_ispis = "TEHIKA";
														}
														else if($tiket_primatelj_odjel == 8){
															$tiket_primatelj_odjel_ispis = "DEVELOPMENT";
														}
														else{
															$tiket_primatelj_odjel_ispis = "Nije definisano";
														}
														
														//Prikaz posiljaoca
														$tiket_posiljaoc_ispis = getZaposlenikimeR($tiket_posiljaoc);
														
														//Prikaz odjela posiljaoca
														$tiket_posiljaoc_odjel = getZaposlenikDepartmentR($tiket_posiljaoc);
														
														if($tiket_posiljaoc_odjel == 1){
															$tiket_posiljaoc_odjel_ispis = "UPRAVA";
														}
														else if($tiket_posiljaoc_odjel == 2){
															$tiket_posiljaoc_odjel_ispis = "FINANCIJE";
														}
														else if($tiket_posiljaoc_odjel == 3){
															$tiket_posiljaoc_odjel_ispis = "PRODAJA";
														}
														else if($tiket_posiljaoc_odjel == 4){
															$tiket_posiljaoc_odjel_ispis = "OBRADA";
														}
														else if($tiket_posiljaoc_odjel == 5){
															$tiket_posiljaoc_odjel_ispis = "SVE ZA VIZE";
														}
														else if($tiket_posiljaoc_odjel == 6){
															$tiket_posiljaoc_odjel_ispis = "MARKETING";
														}
														else if($tiket_posiljaoc_odjel == 7){
															$tiket_posiljaoc_odjel_ispis = "TEHIKA";
														}
														else if($tiket_posiljaoc_odjel == 8){
															$tiket_posiljaoc_odjel_ispis = "DEVELOPMENT";
														}
														else{
															$tiket_posiljaoc_odjel_ispis = "Nije definisano";
														}
														
														//Prikaz primatelja
														$tiket_primatelj_ispis = getZaposlenikimeR($tiket_primatelj);
														
												?>
												<tr>
													<td class="text-center"><?php echo $tiket_id; ?></td>
													<td class="text-center"><?php echo substr($tiket_predmet, 0, 15)."..."?></td>
													<td class="text-center"><?php echo $tiket_hitnost_ispis; ?></td>
													<td class="text-center"><?php echo $tiket_posiljaoc_odjel_ispis; ?></td>
													<td class="text-center"><?php echo $tiket_posiljaoc_ispis; ?></td>
													<td class="text-center"><?php echo $tiket_primatelj_ispis; ?></td>
													<td class="text-center"><?php echo $tiket_vrijeme_kreiranja; ?></td>
													<td class="text-center"><?php echo $tiket_vrijeme_prihvatanja; ?></td>
													<td class="text-center">
														<div class="btn-group material-btn-group">
															<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_success idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
															<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_success idk_dropdown_table" role="menu">
																<li>
																	<a id = "otvori_u_toku_tiket_podaci" href="#" class="material-dropdown-menu__link" data-toggle="modal" data-target="#otvori_u_toku_tiket" 
																		data-address_t ="tiketi?page=zavrsavanje_tiketa&id=<?php echo $tiket_id; ?>"
																		data-id_t ="<?php echo $tiket_id; ?>"
																		data-status_t ="<?php echo $tiket_status; ?>"
																		data-predmet_t ='<?php echo  str_replace("'", "`", $tiket_predmet); ?>'
																		data-opis_t ='<?php echo str_replace("'", "`", $tiket_opis); ?>'
																		data-hitnost_t ='<?php echo $tiket_hitnost_ispis; ?>'
																		data-posiljaoc_t ="<?php echo $tiket_posiljaoc_ispis; ?>"
																		data-primatelj_t ="<?php echo $tiket_primatelj_ispis; ?>"
																		data-posiljaoc_odjel_t ="<?php echo $tiket_posiljaoc_odjel_ispis; ?>"
																		data-primatelj_odjel_t ="<?php echo $tiket_primatelj_odjel_ispis; ?>"
																		data-v_kreiranja_t ="<?php echo $tiket_vrijeme_kreiranja; ?>"
																		data-v_prihvatanja_t ="<?php echo $tiket_vrijeme_prihvatanja; ?>"
																		data-v_zavrsetka_t ="<?php echo $tiket_vrijeme_zavrsetka; ?>"
																		data-dokument_t = '<?php echo $tiket_dokument_download; ?>'
																		>
																		<i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori
																	</a>
																</li>
															</ul>
														</div>
													</td>
												</tr>
												<?php	}	?>
											</tbody>
										</table>
										
									</div>
									<!-- SCRIPT: OTVARANJE U TOKU TIKETA -->
									<script>
										$(document).on("click","#otvori_u_toku_tiket_podaci",function() {
											var address_t1 = $(this).data("address_t");
											document.getElementById("zavrsavanje_tiketa_href").href = address_t1;
											var id_t1 = $(this).data("id_t");
											var status_t1 = $(this).data("status_t");
											var predmet_t1 = $(this).data("predmet_t");
											var opis_t1 = $(this).data("opis_t");
											var hitnost_t1 = $(this).data("hitnost_t");
											var posiljaoc_t1 = $(this).data("posiljaoc_t");
											var primatelj_t1 = $(this).data("primatelj_t");
											var posiljaoc_odjel_t1 = $(this).data("posiljaoc_odjel_t");
											var primatelj_odjel_t1 = $(this).data("primatelj_odjel_t");
											var v_kreiranja_t1 = $(this).data("v_kreiranja_t");
											var v_prihvatanja_t1 = $(this).data("v_prihvatanja_t");
											var v_zavrsetka_t1 = $(this).data("v_zavrsetka_t");
											var dokument_t1 = $(this).data("dokument_t");
											$("#id_t12").html(id_t1);
											$("#id_t12_comment_hidden").val(id_t1);
											$("#status_t12").html(status_t1);
											$("#predmet_t12").html(predmet_t1);
											$("#opis_t12").html(opis_t1);
											$("#hitnost_t12").html(hitnost_t1);
											$("#posiljaoc_t12").html(posiljaoc_t1);
											$("#primatelj_t12").html(primatelj_t1);
											$("#posiljaoc_odjel_t12").html(posiljaoc_odjel_t1);
											$("#primatelj_odjel_t12").html(primatelj_odjel_t1);
											$("#v_kreiranja_t12").html(v_kreiranja_t1);
											$("#v_prihvatanja_t12").html(v_prihvatanja_t1);
											$("#v_zavrsetka_t12").html(v_zavrsetka_t1);
											$("#dokument_t12").html(dokument_t1);
											
											$.ajax({
												url: 'ajax_data.php?page=ispis_komentara',
												type: 'POST',
												data: {'id_t12_comment_ispis':id_t1},
												dataType: 'html',
												success: function(data) {
													$("#forma_komentar_ispis").html(data);
												},
												error: function (xhr, ajaxOptions, thrownError) {
													alert(xhr.status);
													alert(thrownError);
												}
											});
										});
									</script>
									<!-- MODAL: ZAVRSI TIKET START-->
									<div class="modal material-modal material-modal_success fade" id="otvori_u_toku_tiket">
										<div class="modal-dialog">
											<div class="modal-content material-modal__content">
												
												<div class="modal-body material-modal__body">
													<div id = "modal_naslov" class="row">
														<div class="col-xs-12 text-center">
															<p><i class="fa fa-tag" aria-hidden="true"></i> U toku tiket info </p>
														</div>
													</div>
													<div id = "modal_informacije" class="row">
														<div class="col-xs-1 text-center">
															<p><i class="fa fa-id-card" aria-hidden="true"></i></p>
															<p><i class="fa fa-users" aria-hidden="true"></i></p>
															<p><i class="fa fa-user-circle-o" aria-hidden="true"></i></p>
															<p><i class="fa fa-calendar" aria-hidden="true"></i></p>
															<p><i class="fa fa-calendar-plus-o" aria-hidden="true"></i></p>
														</div>
														<div class="col-xs-7 text-left">
															<p>Broj tiketa: <span id="id_t12"></span></p>
															<p>Odjel posiljaoca: <span id="posiljaoc_odjel_t12"></span></p>
															<p>Pošiljaoc: <span id="posiljaoc_t12"></span></p>
															<p>Datum kreiranja: <span id="v_kreiranja_t12"></span></p>
															<p>Datum prihvatanja: <span id="v_prihvatanja_t12"></span></p>
														</div>
														<div class="col-xs-4 text-right">
															<p><span id="hitnost_t12"></span></p>
														</div>
													</div>
													<hr id = "modal_linija">
													<div id = "modal_informacije" class="row">
														<div class="col-xs-1 text-center">
															<p><i class="fa fa-info" aria-hidden="true"></i></p>
														</div>
														<div class="col-xs-8 text-left">
															<p>Predmet: <span class = "predmet_izgled" id="predmet_t12"></span></p>
														</div>
														<div class="col-xs-1 text-center">
															<p><i class="fa fa-file" aria-hidden="true"></i></p>
														</div>
														<div class="col-xs-2 text-left">
															<p>Dokumenti</p>
														</div>
													</div>
													<div class="row">
														<div class="col-xs-9 text-center">
															<p class = "opis_izgled" id="opis_t12"></p>
														</div>
														<div class="col-xs-3 text-center" style = "padding: 10px;">
															<p><span id = "dokument_t12"></span></p>
														</div>
													</div>
													
													<!-- Dodavanje komentara forma-->
													<button style = "margin: 10px;" class="btn material-btn material-btn" onclick="otvori_formu_komentar()"><i style = "padding-right: 10px;" class="fa fa-comment" aria-hidden="true"></i>Dodaj komentar</button>
													
													<script>
														function otvori_formu_komentar() {
															var x = document.getElementById("forma_komentar_otvori");
															if (x.style.display == "none") {
																x.style.display = "block";
															} 
															else {
																x.style.display = "none";
															}
														}
													</script>
													
													<div id = "forma_komentar_otvori" style = "display: none;">
														<form id="dodaj_komentar" action="<?php getSiteURL(); ?>tiketi.php?page=dodaj_komentar" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
															<input type = "hidden" name = "tiket_komentar_id_tiketa" id = "id_t12_comment_hidden">
															<div class="row">
																<hr id = "modal_linija">
																<div class="col-xs-8 text-center">
																	<textarea class = "razlog_reaktivacije_izgled" rows="5" cols="50" placeholder = "Napišite svoj komentar" name = "tiket_komentar_sadrzaj" required></textarea>
																</div>
																<div class="col-xs-4 text-center" style = "padding: 10px;">
																	<div class="fileinput fileinput-new" data-provides="fileinput">
																		<span class="btn btn-default btn-file">
																			<span class="fileinput-new"> 
																				Izaberi dokument
																			</span>
																			<span class="fileinput-exists">
																				Promijeni
																			</span>
																			<input type="file" name="tiket_komentar_dokument" id="tiket_komentar_dokument">
																		</span>
																		<i style="padding-top: 5px; padding-left: 10px; padding-right: 10px;" class="fa fa-question-circle fa-lg" data-toggle="tooltip" data-placement="right" title="Napomena: Dokument ne smije biti veći od 20MB! - Dozvoljeni formati: jpg, jpeg, pdf, doc, docx, xls, xlsx, txt, ppt, pptx, png, csv" aria-hidden="true">
																		</i> 
																		<br>
																		<span style="padding-top: 5px; padding-left: 10px; padding-right: 10px;" class="fileinput-filename">
																		</span>
																		<a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="padding-top: 5px; padding-left: 10px; opacity: 0.8; color: red;">
																			<i class="fa fa-times-circle" aria-hidden="true">
																			</i>
																		</a>
																		<script>
																			$(function (){
																				$('#tiket_komentar_dokument').change(function (){

																					if($('#tiket_komentar_dokument').val() !== ""){
																						var ext = $('#tiket_komentar_dokument').val().split('.').pop().toLowerCase();
																						
																						if($.inArray(ext, ['jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png', 'csv']) == -1) {
																							$('#idk_alert_ext').removeClass('hidden');
																							this.value = null;
																						}else{
																							$('#idk_alert_ext').addClass('hidden');
																						}

																						var f = this.files[0];

																						if (f.size > 20388608 || f.fileSize > 20388608){
																							$('#idk_alert_size').removeClass('hidden');
																							this.value = null;
																						}else{
																							$('#idk_alert_size').addClass('hidden');
																						}
																					}
																				})
																			});
																		</script>
																	</div>
																</div>
															</div>
															
															<div id="idk_alert_size" class="row hidden">
																<div class="col-sm-12">
																	<div class="alert material-alert material-alert_danger">Greška: Dokument koji pokuštavate dodati je veći od dozvoljene veličine.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
																</div>
															</div>
															<div id="idk_alert_ext" class="row hidden">
																<div class="col-sm-12">
																	<div class="alert material-alert material-alert_danger">Greška: Format dokumenta kojeg pokušavate dodati nije dozvoljen.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
																</div>
															</div>
														</form>
															<div class = "row">
																<div class="col-xs-12 text-right">
																	<button style = "margin: 10px; float: left;" type="submit" class="btn btn-primary material-btn material-btn_success" form="dodaj_komentar" ><i style = "padding-right: 10px;" class="fa fa-plus" aria-hidden="true"></i>Dodaj</button>
																</div>
															</div>
													</div>
													<div id = "forma_komentar_ispis" class="row">
														
													</div>
													
													<!-- Dodavanje komentara forma END-->
												</div>
												<div class="modal-footer material-modal__footer">
													<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
													<a id="zavrsavanje_tiketa_href" href="#"><button class="btn btn-primary material-btn material-btn_success">Završi</button></a>
												</div>
											</div>
										</div>
									</div>
									<!-- MODAL: ZAVRSI TIKET END-->
									<!--**************************************************************-->
									<!-- Obradjeni tiketi start-->
									<!--**************************************************************-->
									<div class="tab-pane fade" id="obradjeni_tiketi_list">
										<script type="text/javascript">
											$(document).ready( function () {
												$('#obradjeni_tiketi_list_dt').DataTable( {

													responsive: true,

													"order": [[ 8, "desc" ]],

													"aoColumns": [
															
															{"width": "5%"},
															{"width": "15%"},
															{"width": "10%"},
															{"width": "10%"},
															{"width": "10%"},
															{"width": "10%"},
															{"width": "10%"},
															{"width": "10%"},
															{"width": "10%"},
															{"width": "10%"}
														]
												});
											} );
										</script>
										<table id="obradjeni_tiketi_list_dt" class="display" cellspacing="0" width="100%">
											<thead>
												<tr>
													<th class="text-center">Broj</th>
													<th class="text-center">Predmet</th>
													<th class="text-center">Hitnost</th>
													<th class="text-center">Pošiljaoc odjel</th>
													<th class="text-center">Pošiljaoc</th>
													<th class="text-center">Obradio</th>
													<th class="text-center">Vrijeme kreiranja</th>
													<th class="text-center">Vrijeme prihvatanja</th>
													<th class="text-center">Vrijeme završetka</th>
													<th class="text-center">Akcija</th>
												</tr>
											</thead>
											<tbody>
												<?php
													$obradeni_tiketi = $db->prepare("
																					SELECT *
																					FROM idk_ticketi
																					WHERE ticket_status = :ticket_status AND ticket_primatelj = :ticket_primatelj");

													$obradeni_tiketi->execute(array(
																		':ticket_status' => 3,
																		':ticket_primatelj' => $logged_employee_id));

													while($obradeni_tiketi_row = $obradeni_tiketi->fetch()){
														//******************************************************************************************************
														$tiket_id = $obradeni_tiketi_row['ticket_id'];
														$tiket_status = $obradeni_tiketi_row['ticket_status'];
														$tiket_predmet = $obradeni_tiketi_row['ticket_predmet'];
														$tiket_opis = $obradeni_tiketi_row['ticket_opis'];
														$tiket_hitnost = $obradeni_tiketi_row['ticket_hitnost'];
														$tiket_posiljaoc = $obradeni_tiketi_row['ticket_posiljaoc'];
														$tiket_primatelj = $obradeni_tiketi_row['ticket_primatelj'];
														$tiket_primatelj_odjel = $obradeni_tiketi_row['ticket_primatelj_odjel'];
														$tiket_vrijeme_kreiranja = date('d.m.Y H:i', strtotime($obradeni_tiketi_row['ticket_vrijeme_kreiranja']));
														$tiket_vrijeme_prihvatanja = date('d.m.Y H:i', strtotime($obradeni_tiketi_row['ticket_vrijeme_prihvatanja']));
														$tiket_vrijeme_zavrsetka = date('d.m.Y H:i', strtotime($obradeni_tiketi_row['ticket_vrijeme_zavrsetka']));
														$tiket_dokument = $obradeni_tiketi_row['ticket_dokument'];
														//******************************************************************************************************
														
														//Provjera ekstenzije dokumenta
														if($tiket_dokument !== "1"){
															
															if (strpos($tiket_dokument, '.jpg') !== false OR strpos($tiket_dokument, '.png') !== false){
																$tiket_dokument_icon = '<i class="fa fa-file-image-o fa-3x" aria-hidden="true"></i>';
															}
															else if(strpos($tiket_dokument, '.pdf') !== false){
																$tiket_dokument_icon = '<i class="fa fa-file-pdf-o fa-3x" aria-hidden="true"></i>';
															}
															else if(strpos($tiket_dokument, '.doc') !== false OR strpos($tiket_dokument, '.docx') !== false){
																$tiket_dokument_icon = '<i class="fa fa-file-word-o fa-3x" aria-hidden="true"></i>';
															}
															else if(strpos($tiket_dokument, '.xls') !== false OR strpos($tiket_dokument, '.xlsx') !== false OR strpos($tiket_dokument, '.csv') !== false){
																$tiket_dokument_icon = '<i class="fa fa-file-excel-o fa-3x" aria-hidden="true"></i>';
															}
															else if(strpos($tiket_dokument, '.txt') !== false ){
																$tiket_dokument_icon = '<i class="fa fa-file-text-o fa-3x" aria-hidden="true"></i>';
															}
															else{
																$tiket_dokument_icon = '<i class="fa fa-file-powerpoint-o fa-3x" aria-hidden="true"></i>';
															}
															
															$tiket_dokument_download = '<a href="'.getSiteUrlr().'files/dokumenti_tiketi/'.$tiket_dokument.'" class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK">'.$tiket_dokument_icon.'</a>';
															
														}
														else{
															$tiket_dokument_download = 'Nije priložen dokument.';
														}
														
														//Prikaz hitnosti tiketa
														if($tiket_hitnost == 1){
															$tiket_hitnost_ispis = '<span class="label label-success material-label material-label_success main-container__column text-left">LOW</span>';
														}
														else if($tiket_hitnost == 2){
															$tiket_hitnost_ispis = '<span class="label label-warning material-label material-label_warning main-container__column text-left">MEDIUM</span>';
														}
														else if($tiket_hitnost == 3){
															$tiket_hitnost_ispis = '<span class="label label-danger material-label material-label_danger main-container__column text-left">HIGH</span>';
														}
														else{
															$tiket_hitnost_ispis = "Nije definisano";
														}
														
														//Prikaz odjela tiketa
														if($tiket_primatelj_odjel == 1){
															$tiket_primatelj_odjel_ispis = "UPRAVA";
														}
														else if($tiket_primatelj_odjel == 2){
															$tiket_primatelj_odjel_ispis = "FINANCIJE";
														}
														else if($tiket_primatelj_odjel == 3){
															$tiket_primatelj_odjel_ispis = "PRODAJA";
														}
														else if($tiket_primatelj_odjel == 4){
															$tiket_primatelj_odjel_ispis = "OBRADA";
														}
														else if($tiket_primatelj_odjel == 5){
															$tiket_primatelj_odjel_ispis = "SVE ZA VIZE";
														}
														else if($tiket_primatelj_odjel == 6){
															$tiket_primatelj_odjel_ispis = "MARKETING";
														}
														else if($tiket_primatelj_odjel == 7){
															$tiket_primatelj_odjel_ispis = "TEHIKA";
														}
														else if($tiket_primatelj_odjel == 8){
															$tiket_primatelj_odjel_ispis = "DEVELOPMENT";
														}
														else{
															$tiket_primatelj_odjel_ispis = "Nije definisano";
														}
														
														//Prikaz posiljaoca
														$tiket_posiljaoc_ispis = getZaposlenikimeR($tiket_posiljaoc);
														
														//Prikaz odjela posiljaoca
														$tiket_posiljaoc_odjel = getZaposlenikDepartmentR($tiket_posiljaoc);
														
														if($tiket_posiljaoc_odjel == 1){
															$tiket_posiljaoc_odjel_ispis = "UPRAVA";
														}
														else if($tiket_posiljaoc_odjel == 2){
															$tiket_posiljaoc_odjel_ispis = "FINANCIJE";
														}
														else if($tiket_posiljaoc_odjel == 3){
															$tiket_posiljaoc_odjel_ispis = "PRODAJA";
														}
														else if($tiket_posiljaoc_odjel == 4){
															$tiket_posiljaoc_odjel_ispis = "OBRADA";
														}
														else if($tiket_posiljaoc_odjel == 5){
															$tiket_posiljaoc_odjel_ispis = "SVE ZA VIZE";
														}
														else if($tiket_posiljaoc_odjel == 6){
															$tiket_posiljaoc_odjel_ispis = "MARKETING";
														}
														else if($tiket_posiljaoc_odjel == 7){
															$tiket_posiljaoc_odjel_ispis = "TEHIKA";
														}
														else if($tiket_posiljaoc_odjel == 8){
															$tiket_posiljaoc_odjel_ispis = "DEVELOPMENT";
														}
														else{
															$tiket_posiljaoc_odjel_ispis = "Nije definisano";
														}
														
														//Prikaz primatelja
														$tiket_primatelj_ispis = getZaposlenikimeR($tiket_primatelj);
												?>
												<tr>
													<td class="text-center"><?php echo $tiket_id; ?></td>
													<td class="text-center"><?php echo substr($tiket_predmet, 0, 15)."..." ?></td>
													<td class="text-center"><?php echo $tiket_hitnost_ispis; ?></td>
													<td class="text-center"><?php echo $tiket_posiljaoc_ispis; ?></td>
													<td class="text-center"><?php echo $tiket_posiljaoc_odjel_ispis; ?></td>
													<td class="text-center"><?php echo $tiket_primatelj_ispis; ?></td>
													<td class="text-center"><?php echo $tiket_vrijeme_kreiranja; ?></td>
													<td class="text-center"><?php echo $tiket_vrijeme_prihvatanja; ?></td>
													<td class="text-center"><?php echo $tiket_vrijeme_zavrsetka; ?></td>
													<td class="text-center">
														<div class="btn-group material-btn-group">
															<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_success idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
															<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_success idk_dropdown_table" role="menu">
																<li>
																	<a id = "otvori_obradeni_tiket_podaci" href="#" class="material-dropdown-menu__link" data-toggle="modal" data-target="#otvori_obradeni_tiket" 
																		data-address_t ="tiketi?page=vrati_tiket_u_toku&id=<?php echo $tiket_id; ?>"
																		data-id_t ="<?php echo $tiket_id; ?>"
																		data-status_t ="<?php echo $tiket_status; ?>"
																		data-predmet_t ='<?php echo str_replace("'", "`", $tiket_predmet); ?>'
																		data-opis_t ='<?php echo str_replace("'", "`", $tiket_opis); ?>'
																		data-hitnost_t ='<?php echo $tiket_hitnost_ispis; ?>'
																		data-posiljaoc_t ="<?php echo $tiket_posiljaoc_ispis; ?>"
																		data-posiljaoc_odjel_t ="<?php echo $tiket_posiljaoc_odjel_ispis; ?>"
																		data-primatelj_t ="<?php echo $tiket_primatelj_ispis; ?>"
																		data-primatelj_odjel_t ="<?php echo $tiket_primatelj_odjel_ispis; ?>"
																		data-v_kreiranja_t ="<?php echo $tiket_vrijeme_kreiranja; ?>"
																		data-v_prihvatanja_t ="<?php echo $tiket_vrijeme_prihvatanja; ?>"
																		data-v_zavrsetka_t ="<?php echo $tiket_vrijeme_zavrsetka; ?>"
																		data-dokument_t = '<?php echo $tiket_dokument_download; ?>'
																		>
																		<i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori
																	</a>
																</li>
															</ul>
														</div>
													</td>
												</tr>
												<?php	}	?>
											</tbody>
										</table>
									</div>
									<!-- SCRIPT: OTVARANJE ZAVRŠENIH TIKETA -->
									<script>
									$(document).on("click","#otvori_obradeni_tiket_podaci",function() {
										var address_t1 = $(this).data("address_t");
											document.getElementById("vrati_tiket_u_toku_href").href = address_t1;
											var id_t1 = $(this).data("id_t");
											var status_t1 = $(this).data("status_t");
											var predmet_t1 = $(this).data("predmet_t");
											var opis_t1 = $(this).data("opis_t");
											var hitnost_t1 = $(this).data("hitnost_t");
											var posiljaoc_t1 = $(this).data("posiljaoc_t");
											var primatelj_t1 = $(this).data("primatelj_t");
											var posiljaoc_odjel_t1 = $(this).data("posiljaoc_odjel_t");
											var primatelj_odjel_t1 = $(this).data("primatelj_odjel_t");
											var v_kreiranja_t1 = $(this).data("v_kreiranja_t");
											var v_prihvatanja_t1 = $(this).data("v_prihvatanja_t");
											var v_zavrsetka_t1 = $(this).data("v_zavrsetka_t");
											var dokument_t1 = $(this).data("dokument_t");
											$("#id_t13").html(id_t1);
											$("#status_t13").html(status_t1);
											$("#predmet_t13").html(predmet_t1);
											$("#opis_t13").html(opis_t1);
											$("#hitnost_t13").html(hitnost_t1);
											$("#posiljaoc_t13").html(posiljaoc_t1);
											$("#primatelj_t13").html(primatelj_t1);
											$("#posiljaoc_odjel_t13").html(posiljaoc_odjel_t1);
											$("#primatelj_odjel_t13").html(primatelj_odjel_t1);
											$("#v_kreiranja_t13").html(v_kreiranja_t1);
											$("#v_prihvatanja_t13").html(v_prihvatanja_t1);
											$("#v_zavrsetka_t13").html(v_zavrsetka_t1);
											$("#dokument_t13").html(dokument_t1);
											
											$.ajax({
												url: 'ajax_data.php?page=ispis_komentara_z',
												type: 'POST',
												data: {'id_t13_comment_ispis':id_t1},
												dataType: 'html',
												success: function(data) {
													$("#forma_komentar_ispis_z").html(data);
												},
												error: function (xhr, ajaxOptions, thrownError) {
													alert(xhr.status);
													alert(thrownError);
												}
											});
											
										});

									</script>
									<!-- MODAL: OBRAĐENI TIKET START-->
									<div class="modal material-modal material-modal_success fade" id="otvori_obradeni_tiket">
										<div class="modal-dialog">
											<div class="modal-content material-modal__content">
												
												<div class="modal-body material-modal__body">
													<div id = "modal_naslov" class="row">
														<div class="col-xs-12 text-center">
															<p><i class="fa fa-tag" aria-hidden="true"></i> Obrađeni tiket info </p>
														</div>
													</div>
													<div id = "modal_informacije" class="row">
														<div class="col-xs-1 text-center">
															<p><i class="fa fa-id-card" aria-hidden="true"></i></p>
															<p><i class="fa fa-users" aria-hidden="true"></i></p>
															<p><i class="fa fa-user-circle-o" aria-hidden="true"></i></p>
															<p><i class="fa fa-calendar-minus-o" aria-hidden="true"></i></p>
															<p><i class="fa fa-calendar-plus-o" aria-hidden="true"></i></p>
															<p><i class="fa fa-calendar-check-o" aria-hidden="true"></i></p>
														</div>
														<div class="col-xs-7 text-left">
															<p>Broj tiketa: <span id="id_t13"></span></p>
															<p>Pošiljaoc odjel: <span id="posiljaoc_odjel_t13"></span></p>
															<p>Pošiljaoc: <span id="posiljaoc_t13"></span></p>
															<p>Vrijeme kreiranja: <span id="v_kreiranja_t13"></span></p>
															<p>Vrijeme prihvatanja: <span id="v_prihvatanja_t13"></span></p>
															<p>Vrijeme završetka: <span id="v_zavrsetka_t13"></span></p>
														</div>
														<div class="col-xs-4 text-center">
															<p><span id="hitnost_t13"></span></p>
														</div>
													</div>
													<hr id = "modal_linija">
													<div id = "modal_informacije" class="row">
														<div class="col-xs-1 text-center">
															<p><i class="fa fa-info" aria-hidden="true"></i></p>
														</div>
														<div class="col-xs-8 text-left">
															<p>Predmet: <span class = "predmet_izgled" id="predmet_t13"></span></p>
														</div>
														<div class="col-xs-1 text-center">
															<p><i class="fa fa-info" aria-hidden="true"></i></p>
														</div>
														<div class="col-xs-2 text-left">
															<p>Dokumenti</p>
														</div>
													</div>
													<div class="row">
														<div class="col-xs-9 text-center">
															<p class = "opis_izgled" id="opis_t13"></p>
														</div>
														<div class="col-xs-3 text-center" style = "padding: 10px;">
															<p><span id = "dokument_t13"></span></p>
														</div>
													</div>
													<div id = "forma_komentar_ispis_z" class="row">
														
													</div>
												</div>
												<div class="modal-footer material-modal__footer">
													<a style = "float: left;" id="vrati_tiket_u_toku_href" href="#"><button class="btn btn-warning material-btn material-btn_warning"><i style = "padding-right: 10px;" class="fa fa-reply" aria-hidden="true"></i>Vrati tiket u obradu</button></a>
													<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
												</div>
											</div>
										</div>
									</div>
									<!--**************************************************************-->
									<!-- Poslani tiketi start-->
									<!--**************************************************************-->
									<div class="tab-pane fade" id="poslani_tiketi_list">
										<script type="text/javascript">
											$(document).ready( function () {
												$('#poslani_tiketi_list_dt').DataTable( {

													responsive: true,

													"order": [[ 0, "desc" ]],

													"aoColumns": [
															
															{"width": "5%"},
															{"width": "15%"},
															{"width": "10%"},
															{"width": "10%"},
															{"width": "10%"},
															{"width": "10%"},
															{"width": "10%"},
															{"width": "10%"},
															{"width": "10%"},
															{"width": "10%"}
														]
												});
											} );
										</script>
										<table id="poslani_tiketi_list_dt" class="display" cellspacing="0" width="100%">
											<thead>
												<tr>
													<th class="text-center">Broj</th>
													<th class="text-center">Predmet</th>
													<th class="text-center">Hitnost</th>
													<th class="text-center">Status</th>
													<th class="text-center">Primatelj odjel</th>
													<th class="text-center">Primatelj</th>
													<th class="text-center">Vrijeme kreiranja</th>
													<th class="text-center">Vrijeme prihvatanja</th>
													<th class="text-center">Vrijeme završetka</th>
													<th class="text-center">Akcija</th>
												</tr>
											</thead>
											<tbody>
												<?php
													$poslani_tiketi = $db->prepare("
																					SELECT *
																					FROM idk_ticketi
																					WHERE ticket_posiljaoc = :ticket_posiljaoc
																					ORDER BY ticket_id DESC");

													$poslani_tiketi->execute(array(
																		':ticket_posiljaoc' => $logged_employee_id));

													while($poslani_tiketi_row = $poslani_tiketi->fetch()){
														//******************************************************************************************************
														$tiket_id = $poslani_tiketi_row['ticket_id'];
														$tiket_status = $poslani_tiketi_row['ticket_status'];
														$tiket_predmet = $poslani_tiketi_row['ticket_predmet'];
														$tiket_opis = $poslani_tiketi_row['ticket_opis'];
														$tiket_hitnost = $poslani_tiketi_row['ticket_hitnost'];
														$tiket_posiljaoc = $poslani_tiketi_row['ticket_posiljaoc'];
														$tiket_primatelj = $poslani_tiketi_row['ticket_primatelj'];
														$tiket_primatelj_odjel = $poslani_tiketi_row['ticket_primatelj_odjel'];
														$tiket_vrijeme_kreiranja = date('d.m.Y H:i', strtotime($poslani_tiketi_row['ticket_vrijeme_kreiranja']));
														$tiket_vrijeme_prihvatanja_provjera = $poslani_tiketi_row['ticket_vrijeme_prihvatanja'];
														$tiket_vrijeme_zavrsetka_provjera = $poslani_tiketi_row['ticket_vrijeme_zavrsetka'];
														if($tiket_vrijeme_prihvatanja_provjera == NULL){
															$tiket_vrijeme_prihvatanja = '<span class="label label-primary material-label material-label_primary main-container__column text-left">Čekanje</span>';
														}
														else{
															$tiket_vrijeme_prihvatanja = date('d.m.Y H:i', strtotime($poslani_tiketi_row['ticket_vrijeme_prihvatanja']));
														}
														
														if($tiket_vrijeme_zavrsetka_provjera == NULL){
															$tiket_vrijeme_zavrsetka = '<span class="label label-primary material-label material-label_primary main-container__column text-left">Čekanje</span>';
														}
														else{
															$tiket_vrijeme_zavrsetka = date('d.m.Y H:i', strtotime($poslani_tiketi_row['ticket_vrijeme_zavrsetka']));
														}
														$tiket_dokument = $poslani_tiketi_row['ticket_dokument'];
														//******************************************************************************************************
														
														//Provjera ekstenzije dokumenta
														if($tiket_dokument !== "1"){
															
															if (strpos($tiket_dokument, '.jpg') !== false OR strpos($tiket_dokument, '.png') !== false){
																$tiket_dokument_icon = '<i class="fa fa-file-image-o fa-3x" aria-hidden="true"></i>';
															}
															else if(strpos($tiket_dokument, '.pdf') !== false){
																$tiket_dokument_icon = '<i class="fa fa-file-pdf-o fa-3x" aria-hidden="true"></i>';
															}
															else if(strpos($tiket_dokument, '.doc') !== false OR strpos($tiket_dokument, '.docx') !== false){
																$tiket_dokument_icon = '<i class="fa fa-file-word-o fa-3x" aria-hidden="true"></i>';
															}
															else if(strpos($tiket_dokument, '.xls') !== false OR strpos($tiket_dokument, '.xlsx') !== false OR strpos($tiket_dokument, '.csv') !== false){
																$tiket_dokument_icon = '<i class="fa fa-file-excel-o fa-3x" aria-hidden="true"></i>';
															}
															else if(strpos($tiket_dokument, '.txt') !== false ){
																$tiket_dokument_icon = '<i class="fa fa-file-text-o fa-3x" aria-hidden="true"></i>';
															}
															else{
																$tiket_dokument_icon = '<i class="fa fa-file-powerpoint-o fa-3x" aria-hidden="true"></i>';
															}
															
															$tiket_dokument_download = '<a href="'.getSiteUrlr().'files/dokumenti_tiketi/'.$tiket_dokument.'" class="btn material-btn material-btn_success main-container__column" style = "padding: 5px;" target="_BLANK">'.$tiket_dokument_icon.'</a>';
															
														}
														else{
															$tiket_dokument_download = 'Nije priložen dokument.';
														}
														
														//Prikaz hitnosti tiketa
														if($tiket_hitnost == 1){
															$tiket_hitnost_ispis = '<span class="label label-success material-label material-label_success main-container__column text-left">LOW</span>';
														}
														else if($tiket_hitnost == 2){
															$tiket_hitnost_ispis = '<span class="label label-warning material-label material-label_warning main-container__column text-left">MEDIUM</span>';
														}
														else if($tiket_hitnost == 3){
															$tiket_hitnost_ispis = '<span class="label label-danger material-label material-label_danger main-container__column text-left">HIGH</span>';
														}
														else{
															$tiket_hitnost_ispis = "Nije definisano";
														}
														
														//Prikaz odjela tiketa
														if($tiket_primatelj_odjel == 1){
															$tiket_primatelj_odjel_ispis = "UPRAVA";
														}
														else if($tiket_primatelj_odjel == 2){
															$tiket_primatelj_odjel_ispis = "FINANCIJE";
														}
														else if($tiket_primatelj_odjel == 3){
															$tiket_primatelj_odjel_ispis = "PRODAJA";
														}
														else if($tiket_primatelj_odjel == 4){
															$tiket_primatelj_odjel_ispis = "OBRADA";
														}
														else if($tiket_primatelj_odjel == 5){
															$tiket_primatelj_odjel_ispis = "SVE ZA VIZE";
														}
														else if($tiket_primatelj_odjel == 6){
															$tiket_primatelj_odjel_ispis = "MARKETING";
														}
														else if($tiket_primatelj_odjel == 7){
															$tiket_primatelj_odjel_ispis = "TEHIKA";
														}
														else if($tiket_primatelj_odjel == 8){
															$tiket_primatelj_odjel_ispis = "DEVELOPMENT";
														}
														else{
															$tiket_primatelj_odjel_ispis = "Nije definisano";
														}
														
														//Prikaz statusa tiketa
														if($tiket_status == 3){
															$tiket_status_ispis = '<span class="label label-success material-label material-label_success main-container__column text-left">ZAVRŠEN</span>';
														}
														else if($tiket_status == 2){
															$tiket_status_ispis = '<span class="label label-warning material-label material-label_warning main-container__column text-left">U OBRADI</span>';
														}
														else if($tiket_status == 1){
															$tiket_status_ispis = '<span class="label label-danger material-label material-label_danger main-container__column text-left">POSLAN</span>';
														}
														else{
															$tiket_status_ispis = "Nije definisano";
														}
														
														//Prikaz primatelja
														if($tiket_primatelj == 0){ 
															$tiket_primatelj_ispis = '<span class="label label-info material-label material-label_info main-container__column text-left">Odjel</span>';
														}
														else{
															$tiket_primatelj_ispis = getZaposlenikimeR($tiket_primatelj);
														}
														
												?>
												<tr>
													<td class="text-center"><?php echo $tiket_id; ?></td>
													<td class="text-center"><?php echo substr($tiket_predmet, 0, 15)."..." ?></td>
													<td class="text-center"><?php echo $tiket_hitnost_ispis; ?></td>
													<td class="text-center"><?php echo $tiket_status_ispis; ?></td>
													<td class="text-center"><?php echo $tiket_primatelj_odjel_ispis; ?></td>
													<td class="text-center"><?php echo $tiket_primatelj_ispis; ?></td>
													<td class="text-center"><?php echo $tiket_vrijeme_kreiranja; ?></td>
													<td class="text-center"><?php echo $tiket_vrijeme_prihvatanja; ?></td>
													<td class="text-center"><?php echo $tiket_vrijeme_zavrsetka; ?></td>
													<td class="text-center">
														<div class="btn-group material-btn-group">
															<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_success idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
															<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_success idk_dropdown_table" role="menu">
																<li>
																	<a id = "otvori_poslani_tiket_podaci" href="#" class="material-dropdown-menu__link" data-toggle="modal" data-target="#otvori_poslani_tiket" 
																		data-id_t ="<?php echo $tiket_id; ?>"
																		data-status_t ='<?php echo $tiket_status_ispis; ?>'
																		data-status_t_values ="<?php echo $tiket_status; ?>"
																		data-predmet_t ='<?php echo str_replace("'", "`", $tiket_predmet); ?>'
																		data-opis_t ='<?php echo str_replace("'", "`", $tiket_opis); ?>'
																		data-hitnost_t ='<?php echo $tiket_hitnost_ispis; ?>'
																		data-posiljaoc_t ="<?php echo $tiket_posiljaoc_ispis; ?>"
																		data-primatelj_t ='<?php echo $tiket_primatelj_ispis; ?>'
																		data-primatelj_odjel_t ="<?php echo $tiket_primatelj_odjel_ispis; ?>"
																		data-v_kreiranja_t ="<?php echo $tiket_vrijeme_kreiranja; ?>"
																		data-v_prihvatanja_t ='<?php echo $tiket_vrijeme_prihvatanja; ?>'
																		data-v_zavrsetka_t ='<?php echo $tiket_vrijeme_zavrsetka; ?>'
																		data-dokument_t = '<?php echo $tiket_dokument_download; ?>'
																		>
																		<i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori
																	</a>
																</li>
																
																<?php if ($tiket_status == 3) {?>
																	<li>
																		<a id = "otvori_reaktiviraj_tiket_podaci" class="material-dropdown-menu__link" data-toggle="modal" data-target="#otvori_reaktiviraj_tiket"
																			data-id_t ="<?php echo $tiket_id; ?>"
																			data-status_t ="<?php echo $tiket_status; ?>"
																			data-predmet_t ='<?php echo  str_replace("'", "`", $tiket_predmet); ?>'
																			data-opis_t ='<?php echo str_replace("'", "`", $tiket_opis); ?>'
																			data-hitnost_t ='<?php echo $tiket_hitnost_ispis; ?>'
																			data-posiljaoc_t ="<?php echo $tiket_posiljaoc_ispis; ?>"
																			data-primatelj_t ="<?php echo $tiket_primatelj_ispis; ?>"
																			data-posiljaoc_odjel_t ="<?php echo $tiket_posiljaoc_odjel_ispis; ?>"
																			data-primatelj_odjel_t ="<?php echo $tiket_primatelj_odjel_ispis; ?>"
																			data-v_kreiranja_t ="<?php echo $tiket_vrijeme_kreiranja; ?>"
																			data-v_prihvatanja_t ="<?php echo $tiket_vrijeme_prihvatanja; ?>"
																			data-v_zavrsetka_t ="<?php echo $tiket_vrijeme_zavrsetka; ?>"
																			data-dokument_t = '<?php echo $tiket_dokument_download; ?>'
																			>
																			<i class="fa fa-reply-all" aria-hidden="true"></i> Reaktivacija 
																		</a>
																	</li>
																<?php } ?>
															</ul>
														</div>
													</td>
												</tr>
												<?php	}	?>
											</tbody>
										</table>
										
									</div>
									<!-- SCRIPT: OTVARANJE POSLANOG TIKETA -->
									<script>
										$(document).on("click","#otvori_poslani_tiket_podaci",function() {
											var id_t1 = $(this).data("id_t");
											var status_t1 = $(this).data("status_t");
											var status_t1_values = $(this).data("status_t_values");
											var predmet_t1 = $(this).data("predmet_t");
											var opis_t1 = $(this).data("opis_t");
											var hitnost_t1 = $(this).data("hitnost_t");
											var posiljaoc_t1 = $(this).data("posiljaoc_t");
											var primatelj_t1 = $(this).data("primatelj_t");
											var primatelj_odjel_t1 = $(this).data("primatelj_odjel_t");
											var v_kreiranja_t1 = $(this).data("v_kreiranja_t");
											var v_prihvatanja_t1 = $(this).data("v_prihvatanja_t");
											var v_zavrsetka_t1 = $(this).data("v_zavrsetka_t");
											var dokument_t1 = $(this).data("dokument_t");
											$("#id_t14").html(id_t1);
											$("#id_t14_comment_hidden").val(id_t1);
											$("#status_t14").html(status_t1);
											$("#predmet_t14").html(predmet_t1);
											$("#opis_t14").html(opis_t1);
											$("#hitnost_t14").html(hitnost_t1);
											$("#posiljaoc_t14").html(posiljaoc_t1);
											$("#primatelj_t14").html(primatelj_t1);
											$("#primatelj_odjel_t14").html(primatelj_odjel_t1);
											$("#v_kreiranja_t14").html(v_kreiranja_t1);
											$("#v_prihvatanja_t14").html(v_prihvatanja_t1);
											$("#v_zavrsetka_t14").html(v_zavrsetka_t1);
											$("#dokument_t14").html(dokument_t1);
											
											if(status_t1_values == 2){
												$("#dugme_dodaj_komentar").css("display","block");
											}
											else{
												$("#dugme_dodaj_komentar").css("display","none");
											}
											
											$.ajax({
												url: 'ajax_data.php?page=ispis_komentara_p',
												type: 'POST',
												data: {'id_t14_comment_ispis_p':id_t1},
												dataType: 'html',
												success: function(data) {
													$("#forma_komentar_ispis_p").html(data);
												},
												error: function (xhr, ajaxOptions, thrownError) {
													alert(xhr.status);
													alert(thrownError);
												}
											});
											
										});
										$(document).on("click","#otvori_reaktiviraj_tiket_podaci",function() {
											var id_t1 = $(this).data("id_t");
											var status_t1 = $(this).data("status_t");
											var predmet_t1 = $(this).data("predmet_t");
											var opis_t1 = $(this).data("opis_t");
											var hitnost_t1 = $(this).data("hitnost_t");
											var posiljaoc_t1 = $(this).data("posiljaoc_t");
											var primatelj_t1 = $(this).data("primatelj_t");
											var posiljaoc_odjel_t1 = $(this).data("posiljaoc_odjel_t");
											var primatelj_odjel_t1 = $(this).data("primatelj_odjel_t");
											var v_kreiranja_t1 = $(this).data("v_kreiranja_t");
											var v_prihvatanja_t1 = $(this).data("v_prihvatanja_t");
											var v_zavrsetka_t1 = $(this).data("v_zavrsetka_t");
											var dokument_t1 = $(this).data("dokument_t");
											$("#id_t14_reactive").html(id_t1);
											$("#id_t14_reactive_hidden").val(id_t1);
											$("#status_t14_reactive").html(status_t1);
											$("#predmet_t14_reactive").html(predmet_t1);
											$("#opis_t14_reactive").html(opis_t1);
											$("#opis_t14_reactive_hidden").val(opis_t1);
											$("#hitnost_t14_reactive").html(hitnost_t1);
											$("#posiljaoc_t14_reactive").html(posiljaoc_t1);
											$("#primatelj_t14_reactive").html(primatelj_t1);
											$("#posiljaoc_odjel_t14_reactive").html(posiljaoc_odjel_t1);
											$("#primatelj_odjel_t14_reactive").html(primatelj_odjel_t1);
											$("#v_kreiranja_t14_reactive").html(v_kreiranja_t1);
											$("#v_prihvatanja_t14_reactive").html(v_prihvatanja_t1);
											$("#v_zavrsetka_t14_reactive").html(v_zavrsetka_t1);
											$("#dokument_t14_reactive").html(dokument_t1);
										});

									</script>
									<!-- MODAL: POSLANI TIKET START-->
									<div class="modal material-modal material-modal_success fade" id="otvori_poslani_tiket">
										<div class="modal-dialog">
											<div class="modal-content material-modal__content">
												
												<div class="modal-body material-modal__body">
													<div id = "modal_naslov" class="row">
														<div class="col-xs-12 text-center">
															<p><i class="fa fa-tag" aria-hidden="true"></i> Poslani tiket info </p>
														</div>
													</div>
													<div id = "modal_informacije" class="row">
														<div class="col-xs-1 text-center">
															<p><i class="fa fa-id-card" aria-hidden="true"></i></p>
															<p><i class="fa fa-users" aria-hidden="true"></i></p>
															<p><i class="fa fa-user-circle" aria-hidden="true"></i></p>
															<p><i class="fa fa-calendar-minus-o" aria-hidden="true"></i></p>
															<p><i class="fa fa-calendar-plus-o" aria-hidden="true"></i></p>
															<p><i class="fa fa-calendar-check-o" aria-hidden="true"></i></p>
														</div>
														<div class="col-xs-7 text-left">
															<p>Broj tiketa: <span id="id_t14"></span></p>
															<p>Odjel primatelja: <span id="primatelj_odjel_t14"></span></p>
															<p>Primatelj: <span id="primatelj_t14"></span></p>
															<p>Vrijeme kreiranja: <span id="v_kreiranja_t14"></span></p>
															<p>Vrijeme prihvatanja: <span id="v_prihvatanja_t14"></span></p>
															<p>Vrijeme završetka: <span id="v_zavrsetka_t14"></span></p>
														</div>
														<div class="col-xs-4 text-center">
															<p><span id="hitnost_t14"></span></p>
															<p><span id="status_t14"></span></p>
														</div>
													</div>
													<hr id = "modal_linija">
													<div id = "modal_informacije" class="row">
														<div class="col-xs-1 text-center">
															<p><i class="fa fa-info" aria-hidden="true"></i></p>
														</div>
														<div class="col-xs-8 text-left">
															<p>Predmet: <span class = "predmet_izgled" id="predmet_t14"></span></p>
														</div>
														<div class="col-xs-1 text-center">
															<p><i class="fa fa-file" aria-hidden="true"></i></p>
														</div>
														<div class="col-xs-2 text-left">
															<p>Dokumenti</p>
														</div>
													</div>
													<div class="row">
														<div class="col-xs-9 text-center">
															<p class = "opis_izgled" id="opis_t14"></p>
														</div>
														<div class="col-xs-3 text-center" style = "padding: 10px;">
															<p><span id = "dokument_t14"></span></p>
														</div>
													</div>
													<!-- Dodavanje komentara forma-->
													
													<button id = "dugme_dodaj_komentar" style = "margin: 10px; display: none;" class="btn material-btn material-btn" onclick="otvori_formu_komentar_p()"><i style = "padding-right: 10px;" class="fa fa-comment" aria-hidden="true"></i>Dodaj komentar</button>
													
													<script>
														function otvori_formu_komentar_p() {
															var x = document.getElementById("forma_komentar_otvori_p");
															if (x.style.display == "none") {
																x.style.display = "block";
															} 
															else {
																x.style.display = "none";
															}
														}
													</script>
													
													<div id = "forma_komentar_otvori_p" style = "display: none;">
														<form id="dodaj_komentar_p" action="<?php getSiteURL(); ?>tiketi.php?page=dodaj_komentar_p" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
															<input type = "hidden" name = "tiket_komentar_id_tiketa_p" id = "id_t14_comment_hidden">
															<div class="row">
																<hr id = "modal_linija">
																<div class="col-xs-8 text-center">
																	<textarea class = "razlog_reaktivacije_izgled" rows="5" cols="50" placeholder = "Napišite svoj komentar" name = "tiket_komentar_sadrzaj_p" required></textarea>
																</div>
																<div class="col-xs-4 text-center" style = "padding: 10px;">
																	<div class="fileinput fileinput-new" data-provides="fileinput">
																		<span class="btn btn-default btn-file">
																			<span class="fileinput-new"> 
																				Izaberi dokument
																			</span>
																			<span class="fileinput-exists">
																				Promijeni
																			</span>
																			<input type="file" name="tiket_komentar_dokument_p" id="tiket_komentar_dokument_p">
																		</span>
																		<i style="padding-top: 5px; padding-left: 10px; padding-right: 10px;" class="fa fa-question-circle fa-lg" data-toggle="tooltip" data-placement="right" title="Napomena: Dokument ne smije biti veći od 20MB! - Dozvoljeni formati: jpg, jpeg, pdf, doc, docx, xls, xlsx, txt, ppt, pptx, png, csv" aria-hidden="true">
																		</i> 
																		<br>
																		<span style="padding-top: 5px; padding-left: 10px; padding-right: 10px;" class="fileinput-filename">
																		</span>
																		<a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="padding-top: 5px; padding-left: 10px; opacity: 0.8; color: red;">
																			<i class="fa fa-times-circle" aria-hidden="true">
																			</i>
																		</a>
																		<script>
																			$(function (){
																				$('#tiket_komentar_dokument_p').change(function (){

																					if($('#tiket_komentar_dokument_p').val() !== ""){
																						var ext = $('#tiket_komentar_dokument_p').val().split('.').pop().toLowerCase();
																						
																						if($.inArray(ext, ['jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png', 'csv']) == -1) {
																							$('#idk_alert_ext').removeClass('hidden');
																							this.value = null;
																						}else{
																							$('#idk_alert_ext').addClass('hidden');
																						}

																						var f = this.files[0];

																						if (f.size > 20388608 || f.fileSize > 20388608){
																							$('#idk_alert_size').removeClass('hidden');
																							this.value = null;
																						}else{
																							$('#idk_alert_size').addClass('hidden');
																						}
																					}
																				})
																			});
																		</script>
																	</div>
																</div>
															</div>
															
															<div id="idk_alert_size" class="row hidden">
																<div class="col-sm-12">
																	<div class="alert material-alert material-alert_danger">Greška: Dokument koji pokuštavate dodati je veći od dozvoljene veličine.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
																</div>
															</div>
															<div id="idk_alert_ext" class="row hidden">
																<div class="col-sm-12">
																	<div class="alert material-alert material-alert_danger">Greška: Format dokumenta kojeg pokušavate dodati nije dozvoljen.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
																</div>
															</div>
														</form>
															<div class = "row">
																<div class="col-xs-12 text-right">
																	<button style = "margin: 10px; float: left;" type="submit" class="btn btn-primary material-btn material-btn_success" form="dodaj_komentar_p" ><i style = "padding-right: 10px;" class="fa fa-plus" aria-hidden="true"></i>Dodaj</button>
																</div>
															</div>
													</div>
													<div id = "forma_komentar_ispis_p" class="row">
														
													</div>
													
													<!-- Dodavanje komentara forma END-->
												</div>
												<div class="modal-footer material-modal__footer">
													<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
												</div>
											</div>
										</div>
									</div>
									<!-- MODAL: REKATIVIRAJ TIKET START-->
									<div class="modal material-modal material-modal_success fade" id="otvori_reaktiviraj_tiket">
										<div class="modal-dialog">
											<div class="modal-content material-modal__content">
												<div class="modal-body material-modal__body">
													<form id="reaktiviranje_tiketa" action="<?php getSiteURL(); ?>tiketi.php?page=reaktiviranje_tiketa" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
														<input type = "hidden" name = "tiket_id_reaktiviraj" id = "id_t14_reactive_hidden">
														<input type = "hidden" name = "tiket_stari_opis_reaktiviraj" id = "opis_t14_reactive_hidden">
														<div id = "modal_naslov" class="row">
															<div class="col-xs-12 text-center">
																<p><i class="fa fa-tag" aria-hidden="true"></i> Reaktivacija tiketa</p>
															</div>
														</div>
														<div id = "modal_informacije" class="row">
															<div class="col-xs-1 text-center">
																<p><i class="fa fa-id-card" aria-hidden="true"></i></p>
															</div>
															<div class="col-xs-7 text-left">
																<p>Broj tiketa: <span id = "id_t14_reactive"></span></p>
															</div>
															<div class="col-xs-4 text-center">
																<p><span></span></p>
																<p><span></span></p>
															</div>
														</div>
														<hr id = "modal_linija">
														<div id = "modal_informacije" class="row">
															<div class="col-xs-1 text-center">
																<p><i class="fa fa-info" aria-hidden="true"></i></p>
															</div>
															<div class="col-xs-7 text-left">
																<p>Predmet: <span id = "predmet_t14_reactive" class = "predmet_izgled"></span></p>
															</div>
															<div class="col-xs-1 text-center">
																<p><i class="fa fa-file" aria-hidden="true"></i></p>
															</div>
															<div class="col-xs-3 text-left">
																<p>Dokumenti</p>
															</div>
														</div>
														<div class="row">
															<div class="col-xs-8 text-center">
																<p id = "opis_t14_reactive" class = "opis_izgled" ></p>
															</div>
															<div class="col-xs-4 text-center" style = "padding: 10px;">
																<p><span id = "dokument_t14_reactive"></span></p>
															</div>
														</div>
														<hr id = "modal_linija">
														
														<div id = "modal_informacije" class="row">
															<div class="col-xs-1 text-center">
																<p><i class="fa fa-reply-all" aria-hidden="true"></i></p>
															</div>
															<div class="col-xs-7 text-left">
																<p>Razlog reaktivacije tiketa</p>
															</div>
															<div class="col-xs-1 text-center">
																<p><i class="fa fa-file" aria-hidden="true"></i></p>
															</div>
															<div class="col-xs-3 text-left">
																<p>Novi dokument </p>
															</div>
														</div>
														<div class="row">
															<div class="col-xs-8 text-center">
																<textarea class = "razlog_reaktivacije_izgled" rows="10" cols="50" placeholder = "Navedite razlog reaktivacije tiketa" name = "tiket_opis_reaktiviraj" required></textarea>
															</div>
															<div class="col-xs-4 text-center" style = "padding: 10px;">
																<div class="fileinput fileinput-new" data-provides="fileinput">
																	<span class="btn btn-default btn-file">
																		<span class="fileinput-new"> 
																			Izaberi dokument
																		</span>
																		<span class="fileinput-exists">
																			Promijeni
																		</span>
																		<input type="file" name="tiket_dokument_reaktiviraj" id="tiket_dokument_reaktiviraj">
																	</span>
																	<i style="padding-top: 5px; padding-left: 10px; padding-right: 10px;" class="fa fa-question-circle fa-lg" data-toggle="tooltip" data-placement="right" title="Napomena: Dokument ne smije biti veći od 20MB! - Dozvoljeni formati: jpg, jpeg, pdf, doc, docx, xls, xlsx, txt, ppt, pptx, png, csv" aria-hidden="true">
																	</i> 
																	<br>
																	<span style="padding-top: 5px; padding-left: 10px; padding-right: 10px;" class="fileinput-filename">
																	</span>
																	<a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="padding-top: 5px; padding-left: 10px; opacity: 0.8; color: red;">
																		<i class="fa fa-times-circle" aria-hidden="true">
																		</i>
																	</a>
																	<script>
																		$(function (){
																			$('#tiket_dokument_reaktiviraj').change(function (){

																				if($('#tiket_dokument_reaktiviraj').val() !== ""){
																					var ext = $('#tiket_dokument_reaktiviraj').val().split('.').pop().toLowerCase();
																					
																					if($.inArray(ext, ['jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png', 'csv']) == -1) {
																						$('#idk_alert_ext').removeClass('hidden');
																						this.value = null;
																					}else{
																						$('#idk_alert_ext').addClass('hidden');
																					}

																					var f = this.files[0];

																					if (f.size > 20388608 || f.fileSize > 20388608){
																						$('#idk_alert_size').removeClass('hidden');
																						this.value = null;
																					}else{
																						$('#idk_alert_size').addClass('hidden');
																					}
																				}
																			})
																		});
																	</script>
																</div>
															</div>
														</div>
														<div id="idk_alert_size" class="row hidden">
															<div class="col-sm-12">
																<div class="alert material-alert material-alert_danger">Greška: Dokument koji pokuštavate dodati je veći od dozvoljene veličine.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
															</div>
														</div>
														<div id="idk_alert_ext" class="row hidden">
															<div class="col-sm-12">
																<div class="alert material-alert material-alert_danger">Greška: Format dokumenta kojeg pokušavate dodati nije dozvoljen.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
															</div>
														</div>
													</form>
												</div>
												<div class="modal-footer material-modal__footer">
													<button type="submit" class="btn btn-primary material-btn material-btn_success" form="reaktiviranje_tiketa" >Reaktiviraj</button>
													<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
												</div>
											</div>
										</div>
									</div>
								</div>
								<!--TAB KRAJ-->
							</div>
							
						</div>
					</div>
				</div>
			<!-- OSTALO -->
			<?php
					break;
					
					case "novi_tiket":
			?>
			
			<div class="row">
				<div class="col-xs-8 mx-2">
					<h1>
						<i class="fa fa-send idk_color_green" aria-hidden="true">
						</i> 
						Pošalji tiket
					</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>tiketi?page=list" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive">
						<i class="fa fa-chevron-left" aria-hidden="true">
						</i> 
						<span>
							Povratak
						</span>
					</a>
				</div>
				<div class="col-xs-12">
					<hr/>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class = "col-md-8 col-md-offset-2">
								<div class = "col-xs-12 text-center">
									<form action="tiketi.php?page=dodaj_tiket" method="post" enctype="multipart/form-data" role="form" class="form-horizontal">
										<div class="form-group">
											<label for="tiket_predmet_posalji" class="col-xs-5 control-label">
												<span class="text-danger">
													*
												</span> 
												Predmet tiketa: 
											</label>
											<div class="col-xs-7">
												<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line">
													<input type="text" class="form-control materail-input" id="tiket_predmet_posalji" name="tiket_predmet_posalji" autocomplete="off"  placeholder="Predmet tiketa" required>
													<span class="materail-input-block__line"></span>
												</div>
											</div>
										</div>
										
										<div class="form-group">
											<label for="tiket_hitnost_posalji" class="col-xs-5 control-label">
												<span class="text-danger">
													*
												</span> 
												Važnost tiketa:
											</label>
											<div class="col-xs-7">
												<div class="form-group">
													<select class="selectpicker" id="tiket_hitnost_posalji" name="tiket_hitnost_posalji">
														<option value="3" style="background-color: #F3413C; color:#fff;text-align:center">HIGH</option>
														<option value="2" style="background-color: #f2a12e; color:#fff;text-align:center">MEDIUM</option>
														<option value="1" style="background-color: #68c368; color:#fff;text-align:center">LOW</option>
													</select>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label for="tiket_odjel_posalji" class="col-xs-5 control-label">
												<span class="text-danger">
													*
												</span> 
												Kojem odjelu je namjenjen tiket:
											</label>
											<div class="col-xs-7">
												<div class="form-group">
													<select class="selectpicker" id="tiket_odjel_posalji" name="tiket_odjel_posalji" onchange="uzmi_odjel_id()">
														<option value="1">UPRAVA</option>
														<option value="2">FINANCIJE</option>
														<option value="3">PRODAJA</option>
														<option value="4">OBRADA</option>
														<option value="5">SVE ZA VIZE</option>
														<option value="6">MARKETING</option>
														<option value="7">TEHNIKA</option>
														<option value="8">DEVELOPMENT</option>
													</select>
												</div>
											</div>
										</div>
										<script>
											function uzmi_odjel_id() {
												var tiket_odjel_p = document.getElementById("tiket_odjel_posalji").value;
												$.ajax({
													url: 'ajax_data.php?page=posalji_zaposlenike_odjela',
													type: 'POST',
													data: {'tiket_odjel_p':tiket_odjel_p},
													dataType: 'html',
													success: function(data) {
														$("#tiket_primatelj_posalji").html(data).selectpicker('refresh');

													},
													error: function (xhr, ajaxOptions, thrownError) {
														alert(xhr.status);
														alert(thrownError);
													}
												});
											}
										</script>
										<div class="form-group">
											<label for="tiket_primatelj_posalji" class="col-xs-5 control-label">
												<span class="text-danger">
													*
												</span>
												Uputite direktno tiket nekoj osobi iz navedenog odjela:
											</label>
											<div class="col-xs-7">
												<div class="form-group">
													<select id="tiket_primatelj_posalji" class="selectpicker" name="tiket_primatelj_posalji" >
														<option value="0" selected>Pošalji cijelom odjelu</option>
													</select>
												</div>
											</div>
										</div>
										
										<div class="form-group">
											<label for="tiket_opis_posalji" class="col-xs-5 control-label">
												<span class="text-danger">
													*
												</span>
												Opis problema:
											</label>
											<div class="col-xs-7">
												<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line">
													<textarea class="form-control materail-input material-textarea" name="tiket_opis_posalji" id="tiket_opis_posalji" placeholder="OPIS PROBLEMA" rows="6" required></textarea>
													<span class="materail-input-block__line"></span>
												</div>
											</div>
										</div>
										
										<div class="form-group">
											<label for="tiket_dokument_posalji" class="col-xs-5 control-label">
												Dokument:
											</label>
											<div class="col-xs-7">
												<div class="fileinput fileinput-new" data-provides="fileinput">
													<span class="btn btn-default btn-file">
														<span class="fileinput-new"> 
															Izaberi dokument
														</span>
														<span class="fileinput-exists">
															Promijeni
														</span>
														<input type="file" name="tiket_dokument_posalji" id="tiket_dokument_posalji">
													</span>
													<i style="padding-top: 5px; padding-left: 10px; padding-right: 10px;" class="fa fa-question-circle fa-lg" data-toggle="tooltip" data-placement="right" title="Napomena: Dokument ne smije biti veći od 20MB! - Dozvoljeni formati: jpg, jpeg, pdf, doc, docx, xls, xlsx, txt, ppt, pptx, png, csv" aria-hidden="true">
													</i>
													<span class="fileinput-filename">
													</span>
													<a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="padding-top: 5px; padding-left: 10px; opacity: 0.8; color: red;">
														<i class="fa fa-times-circle" aria-hidden="true">
														</i>
													</a>
													<script>
														$(function (){
															$('#tiket_dokument_posalji').change(function (){

																if($('#tiket_dokument_posalji').val() !== ""){
																	var ext = $('#tiket_dokument_posalji').val().split('.').pop().toLowerCase();
																	
																	if($.inArray(ext, ['jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png', 'csv']) == -1) {
																		$('#idk_alert_ext').removeClass('hidden');
																		this.value = null;
																	}else{
																		$('#idk_alert_ext').addClass('hidden');
																	}

																	var f = this.files[0];

																	if (f.size > 20388608 || f.fileSize > 20388608){
																		$('#idk_alert_size').removeClass('hidden');
																		this.value = null;
																	}else{
																		$('#idk_alert_size').addClass('hidden');
																	}
																}
															})
														});
													</script>
												</div>
											</div>
										</div>
										<div id="idk_alert_size" class="row hidden">
											<div class="col-sm-12">
												<div class="alert material-alert material-alert_danger">Greška: Dokument koji pokuštavate dodati je veći od dozvoljene veličine.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
											</div>
										</div>
										<div id="idk_alert_ext" class="row hidden">
											<div class="col-sm-12">
												<div class="alert material-alert material-alert_danger">Greška: Format dokumenta kojeg pokušavate dodati nije dozvoljen.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-xs-6 col-xs-offset-3 text-center">
												<ul class="list-inline">
													<li class="hidden">
														<i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success">
														</i>
													</li>
													<li>
														<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column">
															<i class="fa fa-send" aria-hidden="true">
															</i> 
															<span>
																Pošalji
															</span>
														</button>
													</li>
												</ul>
												<small>
													Sva polja označena sa 
													<span class="text-danger">
														*
													</span>  
													su obavezna!
												</small>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			
			<?php
					break;
					
					case "dodaj_tiket":
						
						$ticket_posiljaoc_novi = $logged_employee_id;
						$ticket_vrijeme_kreiranja_novi = date('Y-m-d H:i:s');
						
						$ticket_predmet_novi = $_POST['tiket_predmet_posalji'];
						$ticket_hitnost_novi = $_POST['tiket_hitnost_posalji'];
						$ticket_primatelj_odjel_novi = $_POST['tiket_odjel_posalji'];
						$ticket_primatelj_novi = $_POST['tiket_primatelj_posalji'];
						$ticket_opis_novi = $_POST['tiket_opis_posalji'];
						//Upload document
						$tiket_dokument_novi = $_FILES['tiket_dokument_posalji'];
						
						if(!empty($_FILES['tiket_dokument_posalji']['name'])){
							//File properties
							$file_name = $tiket_dokument_novi['name'];
							$file_tmp = $tiket_dokument_novi['tmp_name'];
							
							//File extension
							$file_ext = explode('.', $file_name);
							$file_ext = strtolower(end($file_ext));

							$allowed = array('jpg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png', 'csv');

							if(in_array($file_ext, $allowed)) {

								$file_name_new = uniqid() . '.' . $file_ext;
								$file_destination = "files/dokumenti_tiketi/" . $file_name_new;

								if(move_uploaded_file($file_tmp, $file_destination)){}
							}
						}
						else{
							$file_name_new = "1";
						}
						
						$dodaj_tiket_novi = $db->prepare("
											INSERT INTO idk_ticketi
												(ticket_status, ticket_predmet, ticket_opis, ticket_hitnost, ticket_posiljaoc, ticket_primatelj, ticket_primatelj_odjel, ticket_vrijeme_kreiranja, ticket_dokument)
											VALUES
												(:ticket_status, :ticket_predmet, :ticket_opis, :ticket_hitnost, :ticket_posiljaoc, :ticket_primatelj, :ticket_primatelj_odjel, :ticket_vrijeme_kreiranja, :ticket_dokument)");

						$dodaj_tiket_novi->execute(array(
									
											':ticket_status' => 1,
											':ticket_predmet' => $ticket_predmet_novi,
											':ticket_opis' => $ticket_opis_novi,
											':ticket_hitnost' => $ticket_hitnost_novi,
											':ticket_posiljaoc' => $ticket_posiljaoc_novi,
											':ticket_primatelj' => $ticket_primatelj_novi,
											':ticket_primatelj_odjel' => $ticket_primatelj_odjel_novi,
											':ticket_vrijeme_kreiranja' => $ticket_vrijeme_kreiranja_novi,
											':ticket_dokument' => $file_name_new));
											
						
						//Slanje maila
						//Get User Info
						if($ticket_primatelj_novi !== 0){
							$user_query = $db->prepare("
													SELECT employee_firstname, employee_lastname, employee_email
													FROM idk_employees
													WHERE employee_id = :employee_id AND employee_status NOT LIKE '0'");

							$user_query->execute(array(
											':employee_id' => $ticket_primatelj_novi));
							if ($user_query->rowCount() > 0) {
								$user = $user_query->fetch();

								$employee_firstname = $user['employee_firstname'];
								$employee_lastname = $user['employee_lastname'];
								$employee_email = $user['employee_email'];

								//Send email to user
								$mail_email = $employee_email;
								$mail_name = $employee_firstname . ' ' . $employee_lastname;
								$mail_subject = "Imate novi tiket - JobStep";
								$mail_url = "" . getSiteUrlr() . "/tiketi?page=list";
								$mail_body = "
												<p>Imate novi tiket pod nazivom: " .$ticket_predmet_novi. "</p>
												<p>Detalji poruke: " . $mail_url . "</p>
								";
								$mail_altbody = "
												<p>Imate novi tiket pod nazivom: " .$ticket_predmet_novi. "</p>
												<p>Detalji poruke: " . $mail_url . "</p>
								";

								sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);
							}
						}
						//Add to LOGS
						if($ticket_primatelj_novi == 0){
							//Prikaz odjela tiketa
							if($ticket_primatelj_odjel_novi == 1){
								$log_odjel = "UPRAVA";
							}
							else if($ticket_primatelj_odjel_novi == 2){
								$log_odjel = "FINANCIJE";
							}
							else if($ticket_primatelj_odjel_novi == 3){
								$log_odjel = "PRODAJA";
							}
							else if($ticket_primatelj_odjel_novi == 4){
								$log_odjel = "OBRADA";
							}
							else if($ticket_primatelj_odjel_novi == 5){
								$log_odjel = "SVE ZA VIZE";
							}
							else if($ticket_primatelj_odjel_novi == 6){
								$log_odjel = "MARKETING";
							}
							else if($ticket_primatelj_odjel_novi == 7){
								$log_odjel = "TEHIKA";
							}
							else if($ticket_primatelj_odjel_novi == 8){
								$log_odjel = "DEVELOPMENT";
							}
							else{
								$log_odjel = "Nije definisano";
							}
							$log_desc = "Korisnik je poslao tiket na odjel ".$log_odjel." pod nazivom >>".$ticket_predmet_novi."<<.";
						}
						else{
							$log_primatelj = getZaposlenikimeR($ticket_primatelj_novi);
							$log_desc = "Korisnik je poslao tiket korisniku ".$log_primatelj." pod nazivom >>".$ticket_predmet_novi."<<.";
						}
						
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_employeeid, log_desc, log_date)
										VALUES
											(:log_employeeid, :log_desc, :log_date)");

						$log_query->execute(array(
										':log_employeeid' => $logged_employee_id,
										':log_desc' => $log_desc,
										':log_date' => $log_date));
										
						
						
						
						
						header("Location: " . getSiteURL() . "tiketi?page=list");
						
					break;
					
					case "prihvatanje_tiketa":
					
						$tiket_id_otvoren_novi = $_GET["id"];
						$trenutno_vrijeme = date('Y-m-d H:i:s');

						$prihvatanje_tiketa_status = $db->prepare("
													UPDATE idk_ticketi
													SET ticket_status = :ticket_status, ticket_vrijeme_prihvatanja = :ticket_vrijeme_prihvatanja, ticket_primatelj = :ticket_primatelj
													WHERE ticket_id = $tiket_id_otvoren_novi
													");
						
						$prihvatanje_tiketa_status->execute(array(
							':ticket_status' => 2,
							':ticket_vrijeme_prihvatanja' => $trenutno_vrijeme,
							':ticket_primatelj' => $logged_employee_id));
						
						//Add to LOGS
						$log_desc = "Korisnik je prihvatio tiket pod brojem ".$tiket_id_otvoren_novi.".";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_employeeid, log_desc, log_date)
										VALUES
											(:log_employeeid, :log_desc, :log_date)");

						$log_query->execute(array(
										':log_employeeid' => $logged_employee_id,
										':log_desc' => $log_desc,
										':log_date' => $log_date));
					
					header("Location: " . getSiteURL() . "tiketi?page=list");
					
					break;
					
					case "zavrsavanje_tiketa":
						
						$tiket_id_otvoren_u_toku = $_GET["id"];
						$trenutno_vrijeme = date('Y-m-d H:i:s');
						
						$get_ticket_name = $db->prepare("
												SELECT ticket_predmet, ticket_posiljaoc
												FROM idk_ticketi
												WHERE ticket_id = :ticket_id
												");
						$get_ticket_name->execute(array(
							":ticket_id" => $tiket_id_otvoren_u_toku
						));
						$result_ticket_name = $get_ticket_name->fetch();
						$ticket_name = $result_ticket_name['ticket_predmet'];
						$tiket_posiljaoc = $result_ticket_name['ticket_posiljaoc'];
						
						$zavrsavanje_tiketa_status = $db->prepare("
													UPDATE idk_ticketi
													SET ticket_status = :ticket_status, ticket_vrijeme_zavrsetka = :ticket_vrijeme_zavrsetka
													WHERE ticket_id = $tiket_id_otvoren_u_toku
													");
						
						$zavrsavanje_tiketa_status->execute(array(
													':ticket_status' => 3,
													':ticket_vrijeme_zavrsetka' => $trenutno_vrijeme));
													
						if($tiket_posiljaoc != null){
							$user_query = $db->prepare("
													SELECT employee_firstname, employee_lastname, employee_email
													FROM idk_employees
													WHERE employee_id = :employee_id AND employee_status NOT LIKE '0'");

							$user_query->execute(array(
											':employee_id' => $tiket_posiljaoc));
							if ($user_query->rowCount() > 0) {
								$user = $user_query->fetch();

								$employee_firstname = $user['employee_firstname'];
								$employee_lastname = $user['employee_lastname'];
								$employee_email = $user['employee_email'];

								//Send email to user
								$mail_email = $employee_email;
								$mail_name = $employee_firstname . ' ' . $employee_lastname;
								$mail_subject = "Završeni tiket - JobStep";
								$mail_url = "<a href='" . getSiteUrlr() . "tiketi?page=list#poslani_tiketi_list'>" . getSiteUrlr() . "/tiketi?page=list#poslani_tiketi_list</a>";
								$mail_body = "
												<p>Završeni je tiket pod nazivom: " .$ticket_name. "</p>
												<p>Detalji poruke: " . $mail_url . "</p>
								";
								$mail_altbody = "
												<p>Završeni je tiket pod nazivom: " .$ticket_name. "</p>
												<p>Detalji poruke: " . $mail_url . "</p>
								";

								sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);
							}
						}
						
						//Add to LOGS
						$log_desc = "Korisnik je obradio tiket pod brojem ".$tiket_id_otvoren_u_toku.".";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_employeeid, log_desc, log_date)
										VALUES
											(:log_employeeid, :log_desc, :log_date)");

						$log_query->execute(array(
										':log_employeeid' => $logged_employee_id,
										':log_desc' => $log_desc,
										':log_date' => $log_date));
						
					header("Location: " . getSiteURL() . "tiketi?page=list");
					
					break;
					
					case "vrati_tiket_u_toku":
						
						$tiket_id_otvoren_u_toku_vrati = $_GET["id"];
						$trenutno_vrijeme = NULL;

						$vracanje_tiketa_u_toku_status = $db->prepare("
													UPDATE idk_ticketi
													SET ticket_status = :ticket_status, ticket_vrijeme_zavrsetka = :ticket_vrijeme_zavrsetka
													WHERE ticket_id = $tiket_id_otvoren_u_toku_vrati
													");
						
						$vracanje_tiketa_u_toku_status->execute(array(
													':ticket_status' => 2,
													':ticket_vrijeme_zavrsetka' => $trenutno_vrijeme));
						
						//Add to LOGS
						$log_desc = "Korisnik je vratio u obradu završeni tiket pod brojem ".$tiket_id_otvoren_u_toku.".";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_employeeid, log_desc, log_date)
										VALUES
											(:log_employeeid, :log_desc, :log_date)");

						$log_query->execute(array(
										':log_employeeid' => $logged_employee_id,
										':log_desc' => $log_desc,
										':log_date' => $log_date));
						
					header("Location: " . getSiteURL() . "tiketi?page=list");
					
					break;
					
					case "hitnost_edit":
						$ticket_id_edit_hitnost = $_POST['id_t11_new'];
						$ticket_hitnost_edit_hitnost = $_POST['hitnost_t11_new'];
						
						$tiket_hitnost_promjeni = $db->prepare("
													UPDATE idk_ticketi
													SET ticket_hitnost = :ticket_hitnost
													WHERE ticket_id = $ticket_id_edit_hitnost
													");
						
						$tiket_hitnost_promjeni->execute(array(
													':ticket_hitnost' => $ticket_hitnost_edit_hitnost));
						
						//Add to LOGS
						$log_desc = "Korisnik je primijenio hitnost tiketa pod brojem ".$ticket_id_edit_hitnost.".";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_employeeid, log_desc, log_date)
										VALUES
											(:log_employeeid, :log_desc, :log_date)");

						$log_query->execute(array(
										':log_employeeid' => $logged_employee_id,
										':log_desc' => $log_desc,
										':log_date' => $log_date));
						
					break;
					
					case "reaktiviranje_tiketa":
					
						$ticket_id_reaktivacija = $_POST['tiket_id_reaktiviraj'];
						$ticket_stari_opis_reaktivacija = $_POST['tiket_stari_opis_reaktiviraj'];
						$ticket_opis_reaktivacija = $_POST['tiket_opis_reaktiviraj'];
						$ticket_dokument_reaktivacija = $_FILES['tiket_dokument_reaktiviraj'];
						$ticket_opis_spojeno_reaktivacija = $ticket_stari_opis_reaktivacija . "<br/><br/> Razlog reaktivacije: <br/>" . $ticket_opis_reaktivacija;
						$ticket_vrijeme_kreiranja_reaktivacija = date('Y-m-d H:i:s');
						$ticket_vrijeme_prihvatanja_reaktivacija = NULL;
						$ticket_vrijeme_zavrsetka_reaktivacija = NULL;
						
						if(!empty($_FILES['tiket_dokument_reaktiviraj']['name'])){
							//Ako postoji novi dokument prilikom reaktivacije tiketa - unosi se mjesto staroga dokumenta
							
							//File properties
							$file_name = $ticket_dokument_reaktivacija['name'];
							$file_tmp = $ticket_dokument_reaktivacija['tmp_name'];
							
							//File extension
							$file_ext = explode('.', $file_name);
							$file_ext = strtolower(end($file_ext));

							$allowed = array('jpg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png', 'csv');

							if(in_array($file_ext, $allowed)) {

								$file_name_new = uniqid() . '.' . $file_ext;
								$file_destination = "files/dokumenti_tiketi/" . $file_name_new;

								if(move_uploaded_file($file_tmp, $file_destination)){}
							}
							
							$reaktivacija_tiketa_sa_dokumentom = $db->prepare("
																UPDATE idk_ticketi
																SET  ticket_status = :ticket_status, ticket_opis = :ticket_opis, ticket_vrijeme_kreiranja = :ticket_vrijeme_kreiranja, ticket_vrijeme_prihvatanja = :ticket_vrijeme_prihvatanja, ticket_vrijeme_zavrsetka = :ticket_vrijeme_zavrsetka, ticket_dokument = :ticket_dokument
																WHERE ticket_id = $ticket_id_reaktivacija
																");
							
							$reaktivacija_tiketa_sa_dokumentom->execute(array(
																':ticket_status' => 1,
																':ticket_opis' => $ticket_opis_spojeno_reaktivacija,
																':ticket_vrijeme_kreiranja' => $ticket_vrijeme_kreiranja_reaktivacija,
																':ticket_vrijeme_prihvatanja' => $ticket_vrijeme_prihvatanja_reaktivacija,
																':ticket_vrijeme_zavrsetka' => $ticket_vrijeme_zavrsetka_reaktivacija,
																':ticket_dokument' => $file_name_new));
							
							
						}
						else{
							//Ako nema novog dokumenta prilikom reaktivacije - ostaje stari dokument
							$reaktivacija_tiketa_bez_dokumentom = $db->prepare("
																UPDATE idk_ticketi
																SET ticket_status = :ticket_status, ticket_opis = :ticket_opis, ticket_vrijeme_kreiranja = :ticket_vrijeme_kreiranja, ticket_vrijeme_prihvatanja = :ticket_vrijeme_prihvatanja, ticket_vrijeme_zavrsetka = :ticket_vrijeme_zavrsetka
																WHERE ticket_id = $ticket_id_reaktivacija
																");
							
							$reaktivacija_tiketa_bez_dokumentom->execute(array(
																':ticket_status' => 1,
																':ticket_opis' => $ticket_opis_spojeno_reaktivacija,
																':ticket_vrijeme_kreiranja' => $ticket_vrijeme_kreiranja_reaktivacija,
																':ticket_vrijeme_prihvatanja' => $ticket_vrijeme_prihvatanja_reaktivacija,
																':ticket_vrijeme_zavrsetka' => $ticket_vrijeme_zavrsetka_reaktivacija));
						}
						
						//Add to LOGS
						$log_desc = "Korisnik je reaktivirao tiket pod brojem ".$ticket_id_reaktivacija.".";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_employeeid, log_desc, log_date)
										VALUES
											(:log_employeeid, :log_desc, :log_date)");

						$log_query->execute(array(
										':log_employeeid' => $logged_employee_id,
										':log_desc' => $log_desc,
										':log_date' => $log_date));
						
						header("Location: " . getSiteURL() . "tiketi?page=list");
						
					break;
					
					case "dodaj_komentar":
					
						$ticket_id_komentar = $_POST['tiket_komentar_id_tiketa'];
						$ticket_posiljaoc_komentar = $logged_employee_id;
						$ticket_sadrzaj_komentar = $_POST['tiket_komentar_sadrzaj'];
						$ticket_dokument_komentar = $_FILES['tiket_komentar_dokument'];
						$ticket_vrijeme_kreiranja_komentar = date('Y-m-d H:i:s');
						
						$get_ticket_name = $db->prepare("
												SELECT ticket_predmet, ticket_posiljaoc
												FROM idk_ticketi
												WHERE ticket_id = :ticket_id
												");
						$get_ticket_name->execute(array(
							":ticket_id" => $ticket_id_komentar
						));
						$result_ticket_name = $get_ticket_name->fetch();
						$ticket_name = $result_ticket_name['ticket_predmet'];
						$tiket_posiljaoc = $result_ticket_name['ticket_posiljaoc'];
						
						if(!empty($_FILES['tiket_komentar_dokument']['name'])){
							//File properties
							$file_name = $ticket_dokument_komentar['name'];
							$file_tmp = $ticket_dokument_komentar['tmp_name'];
							
							//File extension
							$file_ext = explode('.', $file_name);
							$file_ext = strtolower(end($file_ext));

							$allowed = array('jpg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png', 'csv');

							if(in_array($file_ext, $allowed)) {

								$file_name_new = uniqid() . '.' . $file_ext;
								$file_destination = "files/dokumenti_tiketi/" . $file_name_new;

								if(move_uploaded_file($file_tmp, $file_destination)){}
							}
						}
						else{
							$file_name_new = "1";
						}
						
						$dodaj_komentar_novi = $db->prepare("
											INSERT INTO idk_ticketi_komentari
												(ticket_k_id_tiketa, ticket_k_posiljaoc, ticket_k_sadrzaj, ticket_k_dokument, ticket_k_vrijeme_kreiranja)
											VALUES
												(:ticket_k_id_tiketa, :ticket_k_posiljaoc, :ticket_k_sadrzaj, :ticket_k_dokument, :ticket_k_vrijeme_kreiranja)");

						$dodaj_komentar_novi->execute(array(
									
											':ticket_k_id_tiketa' => $ticket_id_komentar,
											':ticket_k_posiljaoc' => $ticket_posiljaoc_komentar,
											':ticket_k_sadrzaj' => $ticket_sadrzaj_komentar,
											':ticket_k_dokument' => $file_name_new,
											':ticket_k_vrijeme_kreiranja' => $ticket_vrijeme_kreiranja_komentar));
						
						if($tiket_posiljaoc != null){
							$user_query = $db->prepare("
													SELECT employee_firstname, employee_lastname, employee_email
													FROM idk_employees
													WHERE employee_id = :employee_id  AND employee_status NOT LIKE '0'");

							$user_query->execute(array(
											':employee_id' => $tiket_posiljaoc));
							if ($user_query->rowCount() > 0) {
								$user = $user_query->fetch();

								$employee_firstname = $user['employee_firstname'];
								$employee_lastname = $user['employee_lastname'];
								$employee_email = $user['employee_email'];

								//Send email to user
								$mail_email = $employee_email;
								$mail_name = $employee_firstname . ' ' . $employee_lastname;
								$mail_subject = "Novi komentar na tiketu - JobStep";
								$mail_url = "<a href='" . getSiteUrlr() . "tiketi?page=list#poslani_tiketi_list'>" . getSiteUrlr() . "/tiketi?page=list#poslani_tiketi_list</a>";
								$mail_body = "
												<p>Imate novi komentar na tiketu pod nazivom: " .$ticket_name. "</p>
												<p>Detalji poruke: " . $mail_url . "</p>
								";
								$mail_altbody = "
												<p>Imate novi komentar na tiketu pod nazivom: " .$ticket_name. "</p>
												<p>Detalji poruke: " . $mail_url . "</p>
								";

								sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);
							}
						}
						
						header("Location: " . getSiteURL() . "tiketi?page=list");
						
					break;
					
					case "dodaj_komentar_p":
					
						$ticket_id_komentar = $_POST['tiket_komentar_id_tiketa_p'];
						$ticket_posiljaoc_komentar = $logged_employee_id;
						$ticket_sadrzaj_komentar = $_POST['tiket_komentar_sadrzaj_p'];
						$ticket_dokument_komentar = $_FILES['tiket_komentar_dokument_p'];
						$ticket_vrijeme_kreiranja_komentar = date('Y-m-d H:i:s');
						
						if(!empty($_FILES['tiket_komentar_dokument_p']['name'])){
							//File properties
							$file_name = $ticket_dokument_komentar['name'];
							$file_tmp = $ticket_dokument_komentar['tmp_name'];
							
							//File extension
							$file_ext = explode('.', $file_name);
							$file_ext = strtolower(end($file_ext));

							$allowed = array('jpg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png', 'csv');

							if(in_array($file_ext, $allowed)) {

								$file_name_new = uniqid() . '.' . $file_ext;
								$file_destination = "files/dokumenti_tiketi/" . $file_name_new;

								if(move_uploaded_file($file_tmp, $file_destination)){}
							}
						}
						else{
							$file_name_new = "1";
						}
						
						$dodaj_komentar_novi = $db->prepare("
											INSERT INTO idk_ticketi_komentari
												(ticket_k_id_tiketa, ticket_k_posiljaoc, ticket_k_sadrzaj, ticket_k_dokument, ticket_k_vrijeme_kreiranja)
											VALUES
												(:ticket_k_id_tiketa, :ticket_k_posiljaoc, :ticket_k_sadrzaj, :ticket_k_dokument, :ticket_k_vrijeme_kreiranja)");

						$dodaj_komentar_novi->execute(array(
									
											':ticket_k_id_tiketa' => $ticket_id_komentar,
											':ticket_k_posiljaoc' => $ticket_posiljaoc_komentar,
											':ticket_k_sadrzaj' => $ticket_sadrzaj_komentar,
											':ticket_k_dokument' => $file_name_new,
											':ticket_k_vrijeme_kreiranja' => $ticket_vrijeme_kreiranja_komentar));
						
						header("Location: " . getSiteURL() . "tiketi?page=list");
						
					break;
					
				}
			?>
			<footer><?php getCopyright(); ?></footer>
		</div>
	</div>
</body>
</html>
		<?php 
			}
			else
			{			
				echo '<br/>
					<div class="alert material-alert material-alert_danger">
						<h4>NEMATE PRIVILEGIJE!</h4>
						<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
						<br />
					</div>';
			} 
		?>