<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus = getEmployeeStatus();

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: timovi?page=list");
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Timovi | 
		<?php 
			getTitle(); 
		?>
		</title>

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
		<!-- Content START -->
		<div id="content">
			<!--Container-fluid START -->
			<div class="container-fluid">
				<!--Switch START-->
				<?php
					switch($page)
					{
						case "list":
				?>
						<div class = "row">
							<div class = "col-xs-6 idk_color_green">
								<h1><i class="fa fa fa-users" aria-hidden="true" style = "margin-right: 10px;"></i> Timovi</h1>
							</div>
							<div class = "col-xs-6 text-right idk_margin_top10">
								<a href="" data-toggle="modal" data-target="#new_team" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive">
									<i class="fa fa-plus" aria-hidden="true">
									</i>
									<span>
										Dodaj Tim
									</span>
								</a>
							</div>
							<div class="modal material-modal material-modal_success fade" id="new_team">
								<div class="modal-dialog modal-lg">
									<div class="modal-content material-modal__content">
										<div class="modal-header material-modal__header">
											<button class="close material-modal__close" data-dismiss="modal">&times;</button>
											<h4 class="modal-title material-modal__title">
												<i class="fa fa-plus" aria-hidden="true" style = "margin-right: 10px;">
												</i>
												Registracija novog tima
											</h4>
										</div>
										<div class="modal-body material-modal__body">
											<div class = "row">
												<div class="col-md-8 col-md-offset-2">
													<form action="<?php getSiteURL(); ?>timovi?page=add_new" method="post" http-equiv="Content-type" enctype="multipart/form-data"; charset="UTF-8" class="form-horizontal" id="form_add_new">
														<div class="form-group">
															<label for="name_team" class="col-sm-5 control-label">
																<span class="text-danger">
																	*
																</span>
																Naziv tima:
															</label>
															<div class="col-sm-7">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="name_team" id="name_team" placeholder="Unesite naziv" autocomplete="off" required>
																	<span class="materail-input-block__line">
																	</span>
																</div>
															</div>
														</div>
														<div class="form-group">
															<label for="short_name_team" class="col-sm-5 control-label">
																<span class="text-danger">
																	*
																</span>
																Skraćeni naziv tima:
															</label>
															<div class="col-sm-7">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="short_name_team" id="short_name_team" placeholder="Unesite skraćeni naziv" maxlength="2" autocomplete="off" required>
																	<span class="materail-input-block__line">
																	</span>
																</div>
															</div>
														</div>
														<div class="form-group">
															<label for="color_team" class="col-sm-5 control-label">
																<span class="text-danger">
																	*
																</span>
																Boja tima:
															</label>
															<div class="col-sm-7">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="color" name="color_team" id="color_team" placeholder="Unesite boju" maxlength="2" autocomplete="off" required>
																	<span class="materail-input-block__line">
																	</span>
																</div>
															</div>
														</div>
														<div class="modal-footer material-modal__footer" style = "text-align: center;">
															<button class="btn material-btn material-btn" data-dismiss="modal">
																Odustani
															</button>
															<button type="submit" class="btn btn-primary material-btn material-btn_success" form="form_add_new">
																<i class="fa fa-check-square-o" aria-hidden="true" style = "margin-right: 10px;">
																</i>
																Završi
															</button>
														</div>
														<div class="row" style = "margin-top: 15px;">
															<div class="col-md-8 col-md-offset-2 text-center">
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
						</div>
						<div class="row" style = "margin-top: 20px;">
							<div class="col-md-12">
								<div class="content_box">
									<!--
									<div class="row">
										<div class="col-xs-12">
											Prostor za neke funkcije
										</div>
									</div>
									-->
									<div class="row">
										<div class="col-xs-12">
											<script type="text/javascript">
												$(document).ready(function() {
													var table_s = $('#table_List').DataTable({
				
														responsive: true,
														
														"order": [[ 0, "desc" ]],

														"bAutoWidth": false,

														"aoColumns": [
																{ "width": "5%"},
																{ "width": "30%" },
																{ "width": "10%" },
																{ "width": "20%" },
																{ "width": "20%" },
																{ "width": "15%" , "bSortable": false }
															],
													});
												});
											</script>
											<table id="table_List" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th class="text-center">#</th>
														<th class="text-center">Naziv tima</th>
														<th class="text-center">Oznaka tima</th>
														<th class="text-center">Datum registracije</th>
														<th class="text-center">Status</th>
														<th class="text-center">Akcija</th>
													</tr>
												</thead>
												<tbody>
													<?php
														$query_list = $db->prepare("SELECT * FROM idk_timovi ");
														$query_list->execute();
														while($row_list = $query_list->fetch()){
															$id_tima = $row_list["id_t"];
															$naziv_tima = $row_list["naziv_t"];
															$skraceni_naziv_tima = $row_list["skraceni_naziv_t"];
															$boja_tima = $row_list["boja_t"];
															$ikona_tima = '<span style = "background-color: '.$boja_tima.'; color:white; border-radius: 2.25rem;" class="label label-default material-label material-label_default main-container__column text-left">'.$skraceni_naziv_tima.'</span>';
															
															$datum_reg_tima = date('d.m.Y H:i', strtotime($row_list["datum_reg_t"]));
															if($row_list["status_t"] == 0){
																$status_tima = '<span class="label label-danger material-label material-label_danger main-container__column">Neaktivan</span>';
															}else{
																$status_tima = '<span class="label label-success material-label material-label_success main-container__column">Aktivan</span>';
															}
															
													?>
													<tr>
														<td class="text-center"><?php echo $id_tima; ?></td>
														<td class="text-center"><?php echo $naziv_tima; ?></td>
														<td class="text-center"><?php echo $ikona_tima; ?></td>
														<td class="text-center"><?php echo $datum_reg_tima; ?></td>
														<td class="text-center"><?php echo $status_tima; ?></td>
														<td class="text-center">
															<div class="btn-group material-btn-group">
																<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_success idk_btn_table" data-toggle="dropdown">
																	<i class="fa fa-cogs fa-lg" aria-hidden="true">
																	</i> 
																	<span class="caret material-btn__caret">
																	</span>
																</button>
																<ul style = "top:32px; left: -40px; min-width: 150px !important;" class="dropdown-menu material-dropdown-menu material-dropdown-menu_success idk_dropdown_table" role="menu">
																	
																	<li>
																		<a href="<?php getSiteURL(); ?>timovi?page=open_team&id=<?php echo $id_tima; ?>" class="material-dropdown-menu__link">
																			<i class="fa fa-folder-open-o" aria-hidden="true">
																			</i> 
																			Otvori
																		</a>
																	</li>
																	<li>
																		<a id = "edit_team" href="#" class="material-dropdown-menu__link" 
																			data-toggle="modal" 
																			data-target="#edit_action_team"
																			data-naziv_t ="<?php echo $naziv_tima; ?>"
																			data-id_t ="<?php echo $id_tima; ?>"
																			data-boja_t ="<?php echo $boja_tima; ?>"
																			data-sk_naziv_t ="<?php echo $skraceni_naziv_tima; ?>"
																			>
																			<i class="fa fa-pencil-square-o" aria-hidden="true">
																			</i> 
																			Edit
																		</a>
																	</li>
																	<li>
																		<?php 
																			if($row_list["status_t"] == 0){
																		?>
																		<a id = "action_team" href="#" class="material-dropdown-menu__link" 
																			data-toggle="modal" 
																			data-target="#open_action_team"
																			data-address_t ="<?php getSiteURL(); ?>timovi?page=action_ad&type=1&id=<?php echo $id_tima; ?>"
																			data-status_t ="<?php echo $row_list["status_t"]; ?>"
																			>
																			<i class="fa fa-check" aria-hidden="true">
																			</i> 
																			Aktiviraj Tim
																		</a>
																		<?php 
																			}else{
																		?>
																		<a id = "action_team" href="#" class="material-dropdown-menu__link" 
																			data-toggle="modal" 
																			data-target="#open_action_team"
																			data-address_t ="<?php getSiteURL(); ?>timovi?page=action_ad&type=0&id=<?php echo $id_tima; ?>"
																			data-status ="<?php echo $row_list["status_t"]; ?>"
																			>
																			<i class="fa fa-times" aria-hidden="true">
																			</i> 
																			Deaktiviraj Tim
																		</a>	
																		<?php
																			}
																		?>
																	</li>
																</ul>
															</div>
														</td>
													</tr>
													<?php 
														}
													?>
												</tbody>
											</table>
											
											<script>
												$(document).on("click","#edit_team",function() {
													var naziv_t = $(this).data("naziv_t");
													var id_t = $(this).data("id_t");
													var boja_t = $(this).data("boja_t");
													var sk_naziv_t = $(this).data("sk_naziv_t");
													$("#edit_name").val(naziv_t);
													$("#edit_id").val(id_t);
													$("#edit_color").val(boja_t);
													$("#edit_short_name").val(sk_naziv_t);
												});
												
												$(document).on("click","#action_team",function() {
													var address_t = $(this).data("address_t");
													var status_t = $(this).data("status_t");
													document.getElementById("action_team_submit").href = address_t;
													if(status_t == 0){
														$("#text_action").html("Jeste li sigurni da želite aktivirati tim?");
													}else{
														$("#text_action").html("Jeste li sigurni da želite deaktivirati tim?");
													}
												});
											</script>
											<div class="modal material-modal material-modal_success fade" id="edit_action_team">
												<div class="modal-dialog modal-lg">
													<div class="modal-content material-modal__content">
														<div class="modal-header material-modal__header">
															<button class="close material-modal__close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title material-modal__title">
																<i class="fa fa-pencil-square-o" aria-hidden="true" style = "margin-right: 10px;">
																</i>
																Edit
															</h4>
														</div>
														<div class="modal-body material-modal__body">
															<div class = "row">
																<div class="col-md-8 col-md-offset-2">
																	<form action="<?php getSiteURL(); ?>timovi?page=edit_team" method="post" http-equiv="Content-type" enctype="multipart/form-data"; charset="UTF-8" class="form-horizontal" id="form_edit_team">
																		<input type = "hidden" name = "edit_id" id = "edit_id">
																		<div class="form-group">
																			<label for="edit_name" class="col-sm-5 control-label">
																				<span class="text-danger">
																					*
																				</span>
																				Naziv tima:
																			</label>
																			<div class="col-sm-7">
																				<div class="materail-input-block materail-input-block_success">
																					<input class="form-control materail-input" type="text" name="edit_name" id="edit_name" placeholder="Unesite naziv" autocomplete="off" required>
																					<span class="materail-input-block__line">
																					</span>
																				</div>
																			</div>
																		</div>
																		<div class="form-group">
																			<label for="edit_short_name" class="col-sm-5 control-label">
																				<span class="text-danger">
																					*
																				</span>
																				Skraćeni naziv tima:
																			</label>
																			<div class="col-sm-7">
																				<div class="materail-input-block materail-input-block_success">
																					<input class="form-control materail-input" type="text" name="edit_short_name" id="edit_short_name" placeholder="Unesite skraćeni naziv" maxlength="2" autocomplete="off" required>
																					<span class="materail-input-block__line">
																					</span>
																				</div>
																			</div>
																		</div>
																		<div class="form-group">
																			<label for="edit_color" class="col-sm-5 control-label">
																				<span class="text-danger">
																					*
																				</span>
																				Boja tima:
																			</label>
																			<div class="col-sm-7">
																				<div class="materail-input-block materail-input-block_success">
																					<input class="form-control materail-input" type="color" name="edit_color" id="edit_color" placeholder="Unesite boju" maxlength="2" autocomplete="off" required>
																					<span class="materail-input-block__line">
																					</span>
																				</div>
																			</div>
																		</div>
																		<div class="modal-footer material-modal__footer" style = "text-align: center;">
																			<button class="btn material-btn material-btn" data-dismiss="modal">
																				Odustani
																			</button>
																			<button type="submit" class="btn btn-primary material-btn material-btn_success" form="form_edit_team">
																				<i class="fa fa-check-square-o" aria-hidden="true" style = "margin-right: 10px;">
																				</i>
																				Završi
																			</button>
																		</div>
																		<div class="row" style = "margin-top: 15px;">
																			<div class="col-md-8 col-md-offset-2 text-center">
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
											<div class="modal material-modal material-modal_danger fade" id="open_action_team">
												<div class="modal-dialog">
													<div class="modal-content material-modal__content">
														<div class="modal-header material-modal__header">
															<button class="close material-modal__close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title material-modal__title">
																Akcija
															</h4>
														</div>
														<div class="modal-body material-modal__body">
															<div class="row">
																<div class="col-xs-12 text-left">
																	<p id="text_action"></p>
																</div>
															</div>
														</div>
														<div class="modal-footer material-modal__footer">
															<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
															<a id="action_team_submit" href="#"><button class="btn btn-primary material-btn material-btn_success">Završi</button></a>
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
						
						case "open_team":
							if((in_array( "1" , $employee_status))){
								
								$id = $_GET["id"];
								
								$query_open = $db->prepare("SELECT * FROM idk_timovi WHERE id_t = :id_t");
								$query_open->execute(array(':id_t' => $id));
								$row_open = $query_open->fetch();
								$id_tima = $row_open["id_t"];
								$naziv_tima = $row_open["naziv_t"];
								$datum_reg_tima = date('d.m.Y H:i', strtotime($row_open["datum_reg_t"]));
								if($row_open["status_t"] == 0){
									$icon_tima = '<i class="fa fa-times" aria-hidden="true"></i>';
									$status_tima = '<span class="label label-danger material-label material-label_danger main-container__column">Neaktivan</span>';
								}else{
									$icon_tima = '<i class="fa fa-check" aria-hidden="true"></i>';
									$status_tima = '<span class="label label-success material-label material-label_success main-container__column">Aktivan</span>';
								}
				?>
								<div class = "row">
									<div class = "col-xs-6 idk_color_green">
										<h1><i class="fa fa fa-users" aria-hidden="true" style = "margin-right: 10px;"></i> Tim: <?php echo $naziv_tima; ?></h1>
									</div>
									<div class = "col-xs-6 text-right idk_margin_top10">
										<a href="<?php getSiteURL(); ?>timovi?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
									</div>
								</div>
								<div class="row" style = "margin-top: 20px;">
									<div class="col-md-12">
										<div class="content_box">
											<!--
											<div class="row">
												<div class="col-xs-12">
													Prostor za neke funkcije
												</div>
											</div>
											-->
											<div class="row">
												<div class="col-md-6">
													<div class="row">
														<div class="col-xs-12">
															<h5 style = "font-weight: bold;"><i class="fa fa-info-circle" style = "margin-right: 10px;" aria-hidden="true"></i>Osnovne informacije</h5>
														</div>
													</div>
													<div class="row" style = "padding-top: 10px;">
														<div class = "col-xs-1 text-center">
															<i class="fa fa-users" aria-hidden="true"></i>
														</div>
														<div style = "font-weight: bold;" class = "col-xs-3 text-right">
															Naziv tima:
														</div>
														<div class = "col-xs-8 text-left">
															<?php echo " ".$naziv_tima." ";?>
														</div>
													</div>
													<div class="row" style = "padding-top: 10px;">
														<div class = "col-xs-1 text-center">
															<i class="fa fa-calendar" aria-hidden="true"></i>
														</div>
														<div style = "font-weight: bold;" class = "col-xs-3 text-right">
															Datum kreiranja:
														</div>
														<div class = "col-xs-8 text-left">
															<?php echo " ".$datum_reg_tima." ";?>
														</div>
													</div>
													<div class="row" style = "padding-top: 10px;">
														<div class = "col-xs-1 text-center">
															<?php echo $icon_tima;?>
														</div>
														<div style = "font-weight: bold;" class = "col-xs-3 text-right">
															Status:
														</div>
														<div class = "col-xs-8 text-left">
															<?php echo " ".$status_tima." ";?>
														</div>
													</div>
												</div>
												<div class="col-md-6">
													<div class="row">
														<div class="col-xs-12">
															<h5 style = "font-weight: bold;"><i class="fa fa-users" style = "margin-right: 10px;" aria-hidden="true"></i>Članovi</h5>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								
				<?php
							
							}else{
								echo '
									<div class="alert material-alert material-alert_danger">
										<h4>NEMATE PRIVILEGIJE!</h4>
										<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
										<br />
										<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
									</div>
								';
							}
						break;
						
						//Pozadina CASE-ovi
						
						case "add_new":
						
							if((in_array( "1" , $employee_status))){
								
								$naziv = $_POST["name_team"];
								$snaziv = $_POST["short_name_team"];
								$boja = $_POST["color_team"];
								
								$query = $db->prepare("
												INSERT INTO idk_timovi
													(naziv_t, skraceni_naziv_t, boja_t, datum_reg_t, status_t)
												VALUES
													(:naziv_t, :skraceni_naziv_t, :boja_t, :datum_reg_t, :status_t)");

								$query->execute(array(
												':naziv_t' => $naziv,
												':skraceni_naziv_t' => $snaziv,
												':boja_t' => $boja,
												':datum_reg_t' => date('Y-m-d H:i:s'),
												':status_t' => 1
								));
								
								$log_desc = "TIMOVI -> Dodan novi tim pod nazivom >>".$naziv."<<";
								$log_date = date('Y-m-d H:i:s');

								$log_query = $db->prepare("
												INSERT INTO idk_logs
													(log_employeeid, log_desc, log_date)
												VALUES
													(:log_employeeid, :log_desc, :log_date)");

								$log_query->execute(array(
												':log_employeeid' => $logged_employee_id,
												':log_desc' => $log_desc,
												':log_date' => $log_date
								));
								
								header("Location: " . getSiteURLr() . "timovi?page=list");
							
							}else{
								echo '
									<div class="alert material-alert material-alert_danger">
										<h4>NEMATE PRIVILEGIJE!</h4>
										<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
										<br />
										<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
									</div>
								';
							}
						
						break;
						
						case "action_ad":
							//action_ad -> active deactive
							if((in_array( "1" , $employee_status))){
								
								$id = $_GET["id"];
								
								if($_GET["type"] == 0){
									$type = 0;
									$log_type = "Deaktiviran";
								}else{
									$type = 1;
									$log_type = "Aktiviran";
								}
								
								$query_p_s = $db->prepare("
									UPDATE idk_timovi
									SET status_t = :status_t
									WHERE id_t = :id_t
								");

								$query_p_s->execute(array(
											':id_t' => $id,
											':status_t' => $type
								));
								
								$log_desc = "TIMOVI -> ".$log_type." tim sa ID = [".$id."]";
								$log_date = date('Y-m-d H:i:s');

								$log_query = $db->prepare("
												INSERT INTO idk_logs
													(log_employeeid, log_desc, log_date)
												VALUES
													(:log_employeeid, :log_desc, :log_date)");

								$log_query->execute(array(
												':log_employeeid' => $logged_employee_id,
												':log_desc' => $log_desc,
												':log_date' => $log_date
								));
								
								header("Location: " . getSiteURLr() . "timovi?page=list");
							
							}else{
								echo '
									<div class="alert material-alert material-alert_danger">
										<h4>NEMATE PRIVILEGIJE!</h4>
										<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
										<br />
										<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
									</div>
								';
							}
						break;
						
						case "edit_team":
							if((in_array( "1" , $employee_status))){
								
								$id = $_POST["edit_id"];
								$naziv = $_POST["edit_name"];
								$snaziv = $_POST["edit_short_name"];
								$boja = $_POST["edit_color"];
								
								$query_update = $db->prepare("
									UPDATE idk_timovi
									SET naziv_t = :naziv_t, skraceni_naziv_t = :skraceni_naziv_t, boja_t = :boja_t 
									WHERE id_t = :id_t
								");

								$query_update->execute(array(
											':id_t' => $id,
											':naziv_t' => $naziv,
											':skraceni_naziv_t' => $snaziv,
											':boja_t' => $boja
								));
								
								$log_desc = "TIMOVI -> Uređen tim sa ID = [".$id."]";
								$log_date = date('Y-m-d H:i:s');

								$log_query = $db->prepare("
												INSERT INTO idk_logs
													(log_employeeid, log_desc, log_date)
												VALUES
													(:log_employeeid, :log_desc, :log_date)");

								$log_query->execute(array(
												':log_employeeid' => $logged_employee_id,
												':log_desc' => $log_desc,
												':log_date' => $log_date
								));
								
								header("Location: " . getSiteURLr() . "timovi?page=list");
							
							}else{
								echo '
									<div class="alert material-alert material-alert_danger">
										<h4>NEMATE PRIVILEGIJE!</h4>
										<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
										<br />
										<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
									</div>
								';
							}
						break;
					}
				?>
				<!--Switch END-->
				<footer><?php getCopyright(); ?></footer>
			</div>
			<!--Container-fluid END -->
		</div>
		<!-- Content END -->
	</body>
</html>
<?php 
			}
			else
			{			
				echo 
					'
						<br/>
						<div class="alert material-alert material-alert_danger">
							<h4>
								NEMATE PRIVILEGIJE!
							</h4>
							<p>
								Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.
							</p>
							<br />
						</div>
					';	
			} 
		?>