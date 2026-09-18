<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus = explode( ',' , getEmployeeStatus());

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	} else {
		echo 0;
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title><?php getTitle(); ?></title>

		<?php include('includes/head.php'); 
		if (in_array($getUserIp, $getIpWhiteList)){
		?>
		<!-- CK Editor ---------------------------------------------------------------------------------------->
		<script src="<?php getSiteURL(); ?>ckeditor/ckeditor.js" async></script>
		<script src="pristup_poslodavcu_functions.js"></script>
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
				switch($page){
					
					case "input":
		
					$nalog_id = $_GET['id'];
					$query_kompanija_id = $db->prepare("
														SELECT 
															kompanija_id,
															partneri_pp
														FROM
															idk_nalozi
														WHERE
															nalog_id = :nalog_id
													");
					$query_kompanija_id->execute(array(
						":nalog_id" => $nalog_id
					));
					$result_kompanija_id = $query_kompanija_id->fetch();
					$kompanija_id 	= $result_kompanija_id['kompanija_id'];
					$partneri_pp 	= $result_kompanija_id['partneri_pp'];
				
					$query_check_users = $db->prepare("
															SELECT
																pu_id
															FROM
																idk_pp_users
															WHERE
																pu_company_id = :kompanija_id
														");
					$query_check_users->execute(array(
						":kompanija_id" => $kompanija_id
					));
					$users = $query_check_users->rowCount();
					
					$query_get_admin = $db->prepare("
													SELECT
														pua_user_id
													FROM
														idk_pp_user_access
													WHERE
														pua_nalog_id = :nalog_id
													AND
														pua_partner_id = :kompanija_id
													AND
														pua_status = 1
												");
					$query_get_admin->execute(array(
						":nalog_id" => $nalog_id,
						":kompanija_id" => $kompanija_id
					));
					while($result_get_admin = $query_get_admin->fetch()){
						$admin_id = "admin" . $result_get_admin['pua_user_id'];
			?>
						<script>
							$(document).ready(function(){
								$("#<?php echo $admin_id; ?>").hide();
							});
						</script>
			<?php
					}
			?>		
					<style>
						.form-grupacije{
							border: 1px 
							solid #cccccc; 
							padding: 15px; 
							border-radius: 1.25rem; 
							background-color: #ffffff; 
							margin-bottom: 10px; 
							box-shadow: 0 0 15px #333333ad;
							margin-top: 75px;
							margin-left: 210px;
							width: 790px;
						}
						.company-name{
							text-align: center;
						}
						.edit-partner{
							border: 1px 
							solid #cccccc; 
							padding: 15px; 
							border-radius: 1.25rem; 
							background-color: #ffffff; 
							margin-bottom: 10px; 
							box-shadow: 0 0 15px #333333ad;
							margin-top: 50px;
							width: 790px;
						}
					</style>
					<div class="row">
						<div class="col-xs-8">
							<h1><i class="fa fa-users idk_color_green" aria-hidden="true"></i> Omogući pristup poslodavcu </h1>
						</div>
						<div class="col-xs-4 text-right idk_margin_top10">
							<a href="<?php getSiteURL(); ?>nalozi?page=open&id=<?php echo $nalog_id?>"  class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
						</div>
						<div class="col-xs-12">
							<hr />
						</div>
					</div>
					
					<!-- Modal ADD NEW USER START -->
					<div class="modal material-modal material-modal_success fade" id="modal_add_new_user">
						<div class="modal-dialog modal-lg">
							<div class="modal-content material-modal__content">
								<div class="modal-header material-modal__header">
									<button class="close material-modal__close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title material-modal__title">
										<i class="fa fa-plus" aria-hidden="true" style = "margin-right: 10px;">
										</i>
										Dodaj korisnika
									</h4>
								</div>
								<div class="modal-body material-modal__body">
									<div class = "row">
										<div class="col-md-8 col-md-offset-2">
											<form action="<?php getSiteURL(); ?>pristup_poslodavcu?page=add_user" method="post" http-equiv="Content-type" enctype="multipart/form-data"; charset = "utf-8" class="form-horizontal" id="form_add_new_user">
												<input type="hidden" name="nalog_id" value="<?php echo $nalog_id; ?>">
												<input type="hidden" name="kompanija_id" value="<?php echo $kompanija_id; ?>">
												<div class="form-group">
													<label for="user_fname" class="col-sm-5 control-label">
														<span class="text-danger">
															*
														</span>
														Ime korisnika:
													</label>
													<div class="col-sm-7">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="user_fname" id="user_fname" autocomplete="off" placeholder="Unesite ime user-a..." required>
															<span class="materail-input-block__line">
															</span>
														</div>
													</div>
												</div>
												<div class="form-group">
													<label for="user_lname" class="col-sm-5 control-label">
														<span class="text-danger">
															*
														</span>
														Prezime korisnika:
													</label>
													<div class="col-sm-7">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="user_lname" id="user_lname" autocomplete="off" placeholder="Unesite prezime user-a..." required>
															<span class="materail-input-block__line">
															</span>
														</div>
													</div>
												</div>
												<div class="form-group">
													<label for="user_email" class="col-sm-5 control-label">
														<span class="text-danger">
															*
														</span>
														Email korisnika:
													</label>
													<div class="col-sm-7">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="user_email" id="user_email" autocomplete="off" placeholder="name@example.com" required>
															<span class="materail-input-block__line">
															</span>
														</div>
													</div>
												</div>
												<div class="form-group">
													<label for="user_password" class="col-sm-5 control-label">
														<span class="text-danger">
															*
														</span>
														Password korisnika:
													</label>
													<div class="col-sm-7">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="user_password" id="user_password" autocomplete="off" placeholder="Unesite password user-a..." required>
															<span class="materail-input-block__line">
															</span>
														</div>
													</div>
												</div>
												<div class="modal-footer material-modal__footer" style = "text-align: center;">
													<button class="btn material-btn material-btn" data-dismiss="modal">
														Odustani
													</button>
													<button type="submit" class="btn btn-primary material-btn material-btn_success" form="form_add_new_user">
														<i class="fa fa-check-square-o" aria-hidden="true" style = "margin-right: 10px;">
														</i>
														Završi
													</button>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- Modal ADD NEW USER END -->

					<!-- Modal ADD NEW INFORMATION START -->
					<div class="modal material-modal material-modal_success fade" id="modal_add_new_information">
						<div class="modal-dialog modal-lg">
							<div class="modal-content material-modal__content">
								<div class="modal-header material-modal__header">
									<button class="close material-modal__close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title material-modal__title">
										<i class="fa fa-plus" aria-hidden="true" style = "margin-right: 10px;">
										</i>
										Dodaj informaciju za prikaz
									</h4>
								</div>
								<div class="modal-body material-modal__body">
									<div class = "row">
										<div class="col-md-8 col-md-offset-2">
											<form action="<?php getSiteURL(); ?>pristup_poslodavcu?page=add_information" method="post" http-equiv="Content-type" enctype="multipart/form-data"; charset = "utf-8" class="form-horizontal" id="form_add_new_information">
												<input type="hidden" name="nalog_id" value="<?php echo $nalog_id; ?>">
												<div class="form-group">
													<div class="col-sm-12">
														<label for="pp_information">Informacija za prikaz:</label><br>
														<select id="pp_information" name="pp_information[]" class="selectpicker" multiple data-live-search="true" data-actions-box="true">
															<?php
																$query_info = $db -> prepare("
																SELECT
																	pi.pip_id,
																	pi.pip_name,
																	pi.pip_name_de,
																	pi.pip_status
																FROM
																	idk_pp_informacije_profil pi
																LEFT JOIN
																	idk_pp_nalog_profil np ON pi.pip_id = np.pnp_info_id AND pnp_nalog_id = $nalog_id
																WHERE
																	np.pnp_info_id IS NULL
																	AND pip_status = 1
																");
																$query_info -> execute();
								
																while($row_info = $query_info -> fetch()){
																	echo '<option value = "'.$row_info["pip_id"].'">'.$row_info["pip_name"].'</option>';
																}
															?>
														</select> 
													</div>
												</div>
												<div class="modal-footer material-modal__footer" style = "text-align: center;">
													<button class="btn material-btn material-btn" data-dismiss="modal">
														Odustani
													</button>
													<button type="submit" class="btn btn-primary material-btn material-btn_success" form="form_add_new_information">
														<i class="fa fa-check-square-o" aria-hidden="true" style = "margin-right: 10px;">
														</i>
														Završi
													</button>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- Modal ADD NEW INFORMATION END -->
					
					
					<div class="row">
						<div class="col-md-12">
							<div class="content_box">
								<div class="row">
									<div class="panel-group material-tabs-group">
										<ul class="nav nav-tabs material-tabs material-tabs_primary">
											<li class="active"><a href="#pristup" class="material-tabs__tab-link" data-toggle="tab">Postavke pristupa</a></li>
											<li><a href="#termini" class="material-tabs__tab-link" data-toggle="tab">Termini</a></li>	
											<li><a href="#profil" class="material-tabs__tab-link" data-toggle="tab">Profil</a></li>	
										</ul>
										<div id="myTabs" class="tab-content materail-tabs-content">
											<div class="tab-pane fade active in" id="pristup">
												<div class="row">
													<div class="col-md-offset-1 col-md-8">
														<form id="idk_form" action="<?php getSiteURL(); ?>pristup_poslodavcu?page=add" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
														<?php
															if($users == 0){
														?>
															<div class="form-group" style="margin-left: 300px !important;">
																<div class="col-sm-9">
																	<p class="text-danger">Kompanija nema postojećih korisnika, molimo unesite novog korisnika!</p>
																</div>
															</div>
														<?php
															}
														?>
															<div class="form-group">
																<div class="row">
																	<div class="col-sm-6">
																		<a href="" data-toggle="modal" data-target="#modal_add_new_user" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj korisnika</span></a>
																	</div>
																</div>
															</div>
															<div class="form-group">
																<div class="row">
																	<label for="superadmin" class="col-sm-2 control-label"><strong>Lista korisnika:</strong></label>
																	<div class="col-sm-8">
																		<div id="superadmin_table">
																		</div>
																		<!--- SUPERADMIN TABLE START--->
																		<script type="text/javascript">
																			$(document).ready(function() {
																				var kompanija_id = '<?php echo $kompanija_id; ?>';
																				var nalog_id = '<?php echo $nalog_id; ?>';
																				$.ajax({
																					url: 'ajax_data.php?page=superadmin_table',
																					type: 'POST',
																					data: {
																						'kompanija_id':kompanija_id,
																						'nalog_id':nalog_id
																					},
																					dataType: 'html',
																					success: function(data) {
																						$("#superadmin_table").html(data);
																						$('#idk_table').DataTable({
																							responsive: true,
																							searching: false,
																							paging: false,
																							"order": [[ 0, "asc" ]],

																							 "bAutoWidth": false

																						});
																						addSuperadmin(nalog_id, kompanija_id);
																						addAdmin(nalog_id, kompanija_id);
																					},
																					error: function (xhr, ajaxOptions, thrownError) {
																						alert(xhr.status);
																						alert(thrownError);
																					}
																				});
																			});
																		</script>
																		<!--- SUPERADMIN TABLE END--->
																	</div>
																</div>
															</div>
															<div class="form-group">
																<label for="broj_kandidata" class="col-sm-5 control-label">
																	<strong>Broj kandidata:</strong>
																</label>
																<div class="col-sm-3">
																	<div class="materail-input-block materail-input-block_success">
																		<input class="form-control materail-input" type="number" name="broj_kandidata" id="broj_kandidata" autocomplete="off">
																		<span class="materail-input-block__line">
																		</span>
																	</div>
																</div>
															</div>
															<div class="form-group" id="tip-grupacija">
																<script>
																	$(document).ready(function(){
																		var nalog_id = "<?php echo $nalog_id; ?>";
																		var kompanija_id = "<?php echo $kompanija_id; ?>";
																		$.ajax({
																			url: 'ajax_data.php?page=choose_partner',
																			type: 'POST',
																			data: {'nalog_id':nalog_id},
																			dataType: 'html',
																			success: function(data){
																				$("#tip-grupacija").html(data);
																				switchInputChecked(nalog_id, kompanija_id);
																				switchInputClicked(nalog_id, kompanija_id);
																			},
																			error: function (xhr, ajaxOptions, thrownError) {
																				alert(xhr.status);
																				alert(thrownError);
																			}
																		});
																	});
																</script>
															</div>
														</form>
													</div>
												</div>
											</div>
											<div class="tab-pane fade" id="termini">
												<div class="row">
													<form id="idk_form" action="#" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
														<div class="col-md-offset-1 col-md-8">
															<div class="form-group" id="input_broj_termina">
																<label for="broj_termina" class="col-sm-5 control-label">
																	<strong>Broj termina:</strong>
																</label>
																<div class="col-sm-3">
																	<div class="materail-input-block materail-input-block_success">
																		<input class="form-control materail-input" type="number" name="broj_termina" id="broj_termina" autocomplete="off">
																		<span class="materail-input-block__line">
																		</span>
																	</div>
																</div>
															</div>
															<div class="form-group" id="termin_input">
																<script>
																	$(document).ready(function(){
																		let nalog_id = "<?php echo $nalog_id; ?>";
																		$.ajax({
																			url: 'ajax_data.php?page=check_termin',
																			type: 'POST',
																			data: {
																				'nalog_id'     : nalog_id
																			},
																			success: function(data){
																				if(!$.trim(data)){
																					addTermin();
																				}else{
																					$("#input_broj_termina").empty();
																					$("#termin_input").empty();
																					$("#termin_input").html(data);
																					deleteAppt();
																				}
																			},
																			error: function (xhr, ajaxOptions, thrownError) {
																				alert(xhr.status);
																				alert(thrownError);
																			}
																		});
																	});
																</script>
															</div>
														</div>
													</form>
												</div>
											</div>
											<div class="tab-pane fade" id="profil">
												<div class="row">
													<div class="col-md-offset-1 col-md-8">
														<form id="idk_form" action="<?php getSiteURL(); ?>pristup_poslodavcu?page=add" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
														<?php
															if(!getNalogPPProfilSettings($nalog_id)){
														?>
															<div class="form-group" style="margin-left: 300px !important;">
																<div class="col-sm-9">
																	<p class="text-danger">Nalog nema postojećih postavki!</p>
																</div>
															</div>
														<?php
															}
														?>
															<div class="form-group">
																<div class="row">
																	<div class="col-sm-6">
																		<a href="" data-toggle="modal" data-target="#modal_add_new_information" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj nove informacije za prikaz</span></a>
																	</div>
																</div>
															</div>
															<div class="form-group">
																<div class="row">
																	<label for="superadmin" class="col-sm-2 control-label"><strong>Lista informacija:</strong></label>
																	<div class="col-sm-8">
																		<div id="list_pp_profil">
																		
																		</div>
																		<script>
																	//ISPIS SUPERADMINA U LISTI POSTAVKI PRILIKOM OTVARANJA STRANICE
																	$(document).ready(function(){
																		var nalog_id = '<?php echo $nalog_id; ?>';
																		var kompanija_id = '<?php echo $kompanija_id; ?>';
																		$.ajax({
																			url: 'ajax_data.php?page=list_nalog_profil_pp',
																			type: 'POST',
																			data: {'nalog_id':nalog_id},
																			dataType: 'html',
																			success: function(data) {
																				$("#list_pp_profil").html(data);
																				$('#table_nalog_profil_pp').DataTable({
																					responsive: true,
																					searching: false,
																					paging: false,
																					"order": [[ 0, "asc" ]],

																					 "bAutoWidth": false,

																					"aoColumns": [
																							{ "width": "5%" },
																							{ "width": "25%" },
																							{ "width": "15%" },
																							{ "width": "10%", "bSortable": false }
																						]
																				});
																			},
																			error: function (xhr, ajaxOptions, thrownError) {
																				alert(xhr.status);
																				alert(thrownError);
																			}
																		});
																	});

																	
																</script>
																	</div>
																</div>
															</div>
														</form>
													</div>
												</div>
											</div>
										</div>
									</div>
								<div class="row">
									<div class="panel-group material-accordion material-accordion_success" id="accordion2">
										<div class="panel panel-success material-accordion__panel material-accordion__panel">
											<div class="panel-heading material-accordion__heading">
												<h4 class="panel-title">
												<a class="material-accordion__title" style="margin-bottom:0.3rem" data-toggle="collapse" data-parent="#accordion2" href="#listaPostavki"><span class="glyphicon glyphicon-list" aria-hidden="true" style="margin-right: 10px;"></span>Lista Pristupa</a>
												</h4>
											</div>
											<div id="listaPostavki" class="panel-collapse material-accordion__collapse">
												<div class="panel-body">
													<div class="col-md-offset-1 col-md-8">
														<div class="form-group">
															<div class="row">
																<label for="superadmin" class="col-sm-3 control-label"><strong>Superadmin lista:</strong></label>
																<div class="col-sm-9" id="list_superadmin">
																<script>
																	//ISPIS SUPERADMINA U LISTI POSTAVKI PRILIKOM OTVARANJA STRANICE
																	$(document).ready(function(){
																		var nalog_id = '<?php echo $nalog_id; ?>';
																		var kompanija_id = '<?php echo $kompanija_id; ?>';
																		$.ajax({
																			url: 'ajax_data.php?page=list_superadmin',
																			type: 'POST',
																			data: {'nalog_id':nalog_id},
																			dataType: 'html',
																			success: function(data) {
																				$("#list_superadmin").html(data);
																				$('#idk_table_list_superadmin').DataTable({
																					responsive: true,
																					searching: false,
																					paging: false,
																					"order": [[ 0, "asc" ]],

																					 "bAutoWidth": false,

																					"aoColumns": [
																							{ "width": "5%" },
																							{ "width": "25%" },
																							{ "width": "15%" },
																							{ "width": "10%", "bSortable": false }
																						]
																				});
																				removeSuperadmin(nalog_id, kompanija_id);
																			},
																			error: function (xhr, ajaxOptions, thrownError) {
																				alert(xhr.status);
																				alert(thrownError);
																			}
																		});
																	});
																</script>
																</div>
															</div>
														</div>
														<hr>
														<div class="from-group">
															<div class="row" id="admin-container">
																<label for="list_admin" class="col-sm-3 control-label"><strong>Admin lista:</strong></label>
																<div class="col-sm-9" id="list_admin">
																<script>
																	$(document).ready(function(){
																		var kompanija_id = '<?php echo $kompanija_id; ?>';
																		var nalog_id = '<?php echo $nalog_id; ?>';
																		$.ajax({
																			url: 'ajax_data.php?page=admin_list',
																			type: 'POST',
																			data: {
																				'kompanija_id':kompanija_id,
																				'nalog_id':nalog_id
																			},
																			dataType: 'html',
																			success: function(data){
																				$("#list_admin").html(data);
																				$('#idk_table_list_admin').DataTable({
																					responsive: true,
																					searching: false,
																					paging: false,
																					"order": [[ 0, "asc" ]],

																					 "bAutoWidth": false,

																					"aoColumns": [
																							{ "width": "5%" },
																							{ "width": "25%" },
																							{ "width": "15%" },
																							{ "width": "10%", "bSortable": false }
																						]
																				});
																				removeAdmin(nalog_id, kompanija_id);
																			},
																			error: function (xhr, ajaxOptions, thrownError) {
																				alert(xhr.status);
																				alert(thrownError);
																			}
																		});
																	});
																</script>
																</div>
															</div>
														</div>
														<hr>
														<div id="partner_container">
															<script>
																$(document).ready(function(){
																	var nalog_id 		= "<?php echo $nalog_id; ?>";
																	var kompanija_id 	= "<?php echo $kompanija_id; ?>"
																	$.ajax({
																		url: 'ajax_data.php?page=list_partner',
																		type: 'POST',
																		data: {
																			'nalog_id'		:nalog_id,
																			'kompanija_id' 	:kompanija_id
																		},
																		dataType: 'html',
																		success: function(data) {
																			$("#partner_container").html(data);
																		
																			removePartnerAdmin2(nalog_id, kompanija_id);
																			editPartner(nalog_id, kompanija_id);
																			removePartner(nalog_id, kompanija_id);
																		},
																		error: function (xhr, ajaxOptions, thrownError) {
																			alert(xhr.status);
																			alert(thrownError);
																		}
																	});
																});
															</script>
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
					
					case "add_user":
						
						$user_fname = $_POST['user_fname'];
						$user_lname = $_POST['user_lname'];
						$user_email = $_POST['user_email'];
						$user_password = $_POST['user_password'];
						$nalog_id = $_POST['nalog_id'];
						$kompanija_id = $_POST['kompanija_id'];
						
						$user_hash_pw = md5($user_password);
						$str = date("YmdHis").rand().date("siHdmY");
						$user_key = md5($str);
						
						$query_add_user = $db->prepare("
															INSERT INTO
																idk_pp_users
																(
																	pu_fname,
																	pu_lname,
																	pu_email,
																	pu_password,
																	pu_key,
																	pu_company_id
																)
															VALUES
																(
																	:pu_fname,
																	:pu_lname,
																	:pu_email,
																	:pu_password,
																	:pu_key,
																	:pu_company_id
																)
														");
						$query_add_user->execute(array(
							":pu_fname" => $user_fname,
							":pu_lname" => $user_lname,
							":pu_email" => $user_email,
							":pu_password" => $user_hash_pw,
							":pu_key" => $user_key,
							":pu_company_id" => $kompanija_id
						));
						
						header("Location: pristup_poslodavcu?page=input&id=$nalog_id");
						
					break;

					case "add_information":
						
						$nalog_id = $_POST['nalog_id'];
						$pp_information = $_POST['pp_information'];

						$query = $db->prepare("INSERT INTO
															idk_pp_nalog_profil
															(
																pnp_nalog_id,
																pnp_info_id
															)
														VALUES
															(
																:pnp_nalog_id,
																:pnp_info_id
															)
														");

						foreach ($pp_information as $info) {
							$query->execute(array(
								":pnp_nalog_id" => $nalog_id,
								":pnp_info_id" => $info
							));

							$log_desc = "Dodana informacija za prikaz na pp-u: [".$info."] za nalog: [".$nalog_id."].";
							$log_type = "0";
							addToLogs($log_desc, $log_type);
						}

						header("Location: pristup_poslodavcu?page=input&id=$nalog_id");
						
					break;
					
					case "add_termin_group":
						$nalog_id 	= $_POST['nalog_id'];
						$start_date = $_POST['start_date'];
						$end_date 	= $_POST['end_date'];
						$group_name = $_POST['group_name'];
						
						echo $nalog_id . " - " . $start_date . " - " . $end_date . " - " . $group_name;
					break;
				}
			?>
			</div>
		</div>
	</body>
</html>
<?php 
}else{			
		echo '
			<br/>
			<div class="alert material-alert material-alert_danger">
				<h4>NEMATE PRIVILEGIJE!</h4>
				<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
				<br />
			</div>
';} ?>