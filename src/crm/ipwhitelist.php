<?php
	include("includes/functions.php");

	$getEmployeeStatus = getEmployeeStatus();

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: ipwhitelist?page=login");
	}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php getTitle(); ?></title>

	<?php include('includes/head.php'); ?>

</head>
<body>
		<div class="container-fluid idk_margin_top10">
		<?php
			switch ($page){
				
				case "login":

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-list-ol idk_color_green" aria-hidden="true"></i> IP Whitelist</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box" style="position:relative;">

								<?php
									if(isset($_GET['mess'])) {
										$mess = $_GET['mess'];
									}else{
										$mess = 0;
									}

									if($mess == 1){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novog zaposlenika.</div>';
									}elseif($mess == 2){
										echo '<div class="alert material-alert material-alert_danger">Greška: Korisnik kojeg pokuštavate dodati već postoji u bazi podataka.</div>';
									}elseif($mess == 3){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili profil zaposlenika.</div>';
									}elseif($mess == 4){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali profil zaposlenika.</div>';
									}
								?>
								<style>
								.ipWhiteLIstKey{
									border: 1px solid #ccc;
									padding: 28px;
									font-size: 24px;
									border-radius: 0;
									display: inline-block;
									width: auto;
								}
								.ipShiteListSubmitButton{
									display: inline-block;
									font-size: 24px;
									padding: 11px 22px;
									border: 1px solid #379c37;
									background: #379c37;
									color: #fff;									
								}
								.ipLoginCOntainer{
									display: inline-block;
									position: absolute;
									top: 50%;
									left: 50%;
									transform: translate(-50%, -50%);
								}
								</style>
								<form id="idk_form" action="<?php getSiteURL(); ?>ipwhitelist?page=list" method="post" enctype="multipart/form-data"  class="form-horizontal" role="form">
										<div class="ipLoginCOntainer">
											<input class="form-control ipWhiteLIstKey" type="text" name="ipWhitelistKey" autocomplete="off" id="ipWhitelistKey" placeholder="Key" required>
											<button class="ipShiteListSubmitButton"><i class="fa fa-chevron-right" aria-hidden="true"></i></button>
										</div>
								</form>
						
					
					</div>
				</div>
			</div>
		<?php
				break;
				
				case "list":
				
				$ipWhitelistKey = $_POST['ipWhitelistKey'];
				$ipWhitelistKeyGet = $_GET['ipWhitelistKey'];
				
				if(!isset($_GET['ipWhitelistKey'])){
					$Key = $ipWhitelistKey;
				}else{
					$Key = $ipWhitelistKeyGet;
				}
				
				
				if($ipWhitelistKey == "0Be50KXTB0krhxiUObGKvtf2XBJxKppR" OR $ipWhitelistKeyGet == "0Be50KXTB0krhxiUObGKvtf2XBJxKppR"){

				
				$ip_blocker_status = getIpWhitelistStatusR();
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-list-ol idk_color_green" aria-hidden="true"></i> IP Whitelist </h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>ipwhitelist?page=add&ipWhitelistKey=<?php echo $Key; ?>" class="pull-right btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></a>
					
					<div class="main-container__column materail-switch materail-switch_success pull-right" style="margin-right: 25px;">
						<input class="materail-switch__element" type="checkbox" id="blocker_status" name="blocker_status" <?php if($ip_blocker_status == 1){echo "checked";}else{} ?>>
						<label class="materail-switch__label" for="blocker_status"></label>
					</div>			
				</div>
			<script>
				$( "#blocker_status" ).change(function() {
					
					if($(this).is(':checked')){
						var blocker_status = 1;
					}else{
						var blocker_status = 0;
					}
					
					$.ajax({
						url: 'ipwhitelist?page=ip_whitelist_status',  
						type: 'POST',    
						data: {"blocker_status": blocker_status, "ipWhitelistKey": "<?php echo $Key; ?>"},
						dataType: 'html',																													
						success: function(data) {
							//alert(data);
						}
					})	
							
				});
			</script>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-xs-12">
								<?php
									if(isset($_GET['mess'])) {
										$mess = $_GET['mess'];
									}else{
										$mess = 0;
									}

									if($mess == 1){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novu ip adresu.</div>';
									}elseif($mess == 2){
										echo '<div class="alert material-alert material-alert_danger">Uspješno ste blokirali ip adresu.</div>';
									}elseif($mess == 3){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili profil zaposlenika.</div>';
									}elseif($mess == 4){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali profil zaposlenika.</div>';
									}
								?>
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_table').DataTable({

											responsive: true,

											"order": [[ 0, "asc" ]],

											 "bAutoWidth": false,

											"aoColumns": [
													{ "width": "5%", "bSortable": false },
													{ "width": "15%" },
													{ "width": "15%" },
													{ "width": "40%" },
													{ "width": "15%" },
													{ "width": "10%", "bSortable": false }
												]
										});
									} );
								</script>
								
									<table id="idk_table" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th></th>
												<th class="text-center">IP adresa</th>
												<th class="text-center">Korisnik</th>
												<th>Opis</th>
												<th class="text-center">Datum</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<?php
												$query = $db->prepare("
																SELECT ipwl_id, ipwl_user, ipwl_ip, ipwl_added_by, ipwl_reason, ipwl_datetime
																FROM idk_ipwhitelist
																WHERE ipwl_status = 0
																ORDER BY ipwl_id DESC
																");

												$query->execute();
	
												$count = 1;
												while($row = $query->fetch()){

													$ipwl_id = $row['ipwl_id'];
													$ipwl_user = $row['ipwl_user'];
													$ipwl_ip = $row['ipwl_ip'];
													$ipwl_added_by = $row['ipwl_added_by'];
													$ipwl_reason = $row['ipwl_reason'];
													$ipwl_datetime = $row['ipwl_datetime'];
													$datum = date("H:i d.m.Y", strtotime($ipwl_datetime));

											?>
											<tr>
												<td class="text-center"><?php echo $count++; ?></td>
												<td class="text-center"><span class="label label-success material-label material-label_success main-container__column"><?php echo $ipwl_ip; ?></span></td>
												<td class="text-center"><span class="label label-success material-label material-label_success main-container__column"><?php echo $ipwl_user; ?></span></td>
												<td><?php echo $ipwl_reason; ?></td>
												<td class="text-center"><span class="label label-success material-label material-label_success main-container__column"><?php echo $datum; ?></span></td>
												<td class="text-center">
													<div class="btn-group material-btn-group">
														<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
														<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
															<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>ipwhitelist?page=block&ipid=<?php echo $ipwl_id; ?>&ipWhitelistKey=<?php echo $Key; ?>" data-toggle="modal" data-target="#archiveModal" data-ipvalue="<?php echo $ipwl_ip; ?>" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Blokiraj</a></li>
														</ul>
													</div>
												</td>
											</tr>
											<?php } ?>
											<script>
												$(".archive").click(function () {
													var addressValue = $(this).attr("data");
													var ipvalue = $(this).data("ipvalue");
													$("#ipAdressBlock").html(ipvalue);
													document.getElementById("archive_link").href = addressValue;
												});
											</script>
											<!-- Modal -->
											<div class="modal material-modal material-modal_danger fade" id="archiveModal">
												<div class="modal-dialog">
													<div class="modal-content material-modal__content">
														<div class="modal-header material-modal__header">
															<button class="close material-modal__close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title material-modal__title">Blokiranje</h4>
														</div>
														<div class="modal-body material-modal__body">
															<p>Jeste li sigurni da želite blokirati ip adresu <span id="ipAdressBlock"></span>?</p>
														</div>
														<div class="modal-footer material-modal__footer">
															<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
															<a id="archive_link" href=""><button class="btn btn-primary material-btn material-btn_danger">BLOKIRAJ</button></a>
														</div>
													</div>
												</div>
											</div>
										</tbody>
									</table>
							
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
				}else{
						echo '
							<br/>
							<div class="alert material-alert material-alert_danger">
								<h4>NEMATE PRIVILEGIJE!</h4>
								<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
								<br />
							</div>';
				}
				
				break;

				case "list_arhiva":

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-list-ol idk_color_green" aria-hidden="true"></i> Arhiva zaposlenika</h1>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-xs-12">
								<?php
									if(isset($_GET['mess'])) {
										$mess = $_GET['mess'];
									}else{
										$mess = 0;
									}

									if($mess == 1){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novog zaposlenika.</div>';
									}elseif($mess == 2){
										echo '<div class="alert material-alert material-alert_danger">Greška: Korisnik kojeg pokuštavate dodati već postoji u bazi podataka.</div>';
									}elseif($mess == 3){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili profil zaposlenika.</div>';
									}elseif($mess == 4){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali profil zaposlenika.</div>';
									}
								?>
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_table').DataTable({

											responsive: true,

											"order": [[ 1, "asc" ]],

											 "bAutoWidth": false,

											"aoColumns": [
													{ "width": "5%", "bSortable": false },
													{ "width": "30%" },
													{ "width": "15%" },
													{ "width": "25%" },
													{ "width": "15%" },
													{ "width": "10%", "bSortable": false }
												]
										});
									} );
								</script>
								<table id="idk_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th></th>
											<th>Ime i prezime</th>
											<th>Telefon</th>
											<th>E-mail</th>
											<th>Status</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
															SELECT employee_id, employee_firstname, employee_lastname, employee_email, employee_status, employee_image
															FROM idk_employees
															WHERE employee_status = 0");

											$query->execute();

											while($row = $query->fetch()){

												$ipwl_id = $row['employee_id'];
												$employee_firstname = $row['employee_firstname'];
												$employee_lastname = $row['employee_lastname'];
												$employee_email = $row['employee_email'];

												if($row['employee_image'] == "none"){
													$employee_image = "none.jpg";
												}else{
													$employee_image = $row['employee_image'];
												}

												if($row['employee_status'] == 0){
													$employee_status = "Arhiviran";
												}elseif($row['employee_status'] == 1){
													$employee_status = "Administrator";
												}elseif($row['employee_status'] == 2){
													$employee_status = "Super korisnik";
												}elseif($row['employee_status'] == 3){
													$employee_status = "Korisnik";
												}elseif($row['employee_status'] == 4){
													$employee_status = "Saradnik";
												}

												//Get primary phone
												$query_phone = $db->prepare("
																	SELECT ei_data
																	FROM idk_employees_info
																	WHERE ei_group = :ei_group AND ei_primary = :ei_primary AND ei_employeeid = :ei_employeeid");

												$query_phone->execute(array(
													':ei_group' => 1,
													':ei_primary' => 1,
													':ei_employeeid' => $ipwl_id));

												$row_phone = $query_phone->fetch();

												$employee_phone = $row_phone['ei_data'];
										?>
										<tr>
											<td class="text-center"><a href="<?php getSiteURL(); ?>ipwhitelist?page=open&id=<?php echo $ipwl_id; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/employees/<?php echo $employee_image; ?>"></a></td>
											<td><a href="<?php getSiteURL(); ?>ipwhitelist?page=open&id=<?php echo $ipwl_id; ?>"><?php echo $employee_firstname; ?> <?php echo $employee_lastname; ?></a></td>
											<td><a href="tel:<?php echo $employee_phone; ?>"><?php echo $employee_phone; ?></a></td>
											<td><a href="mailto:<?php echo $employee_email; ?>"><?php echo $employee_email; ?></a></td>
											<td><?php echo $employee_status; ?></td>
											<td class="text-center">
												<div class="btn-group material-btn-group">
													<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
													<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
														<li><a href="<?php getSiteURL(); ?>ipwhitelist?page=open&id=<?php echo $ipwl_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
														<li><a href="<?php getSiteURL(); ?>ipwhitelist?page=edit&id=<?php echo $ipwl_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi</a></li>
														<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>ipwhitelist?page=archive&id=<?php echo $ipwl_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
													</ul>
												</div>
											</td>
										</tr>
										<?php } ?>
										<script>
											$(".archive").click(function () {
												var addressValue = $(this).attr("data");
												document.getElementById("archive_link").href = addressValue;
											});
										</script>
										<!-- Modal -->
										<div class="modal material-modal material-modal_danger fade" id="archiveModal">
											<div class="modal-dialog">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Arhiviranje</h4>
													</div>
													<div class="modal-body material-modal__body">
														<p>Jeste li sigurni da želite arhivirati zaposlenika?</p>
													</div>
													<div class="modal-footer material-modal__footer">
														<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
														<a id="archive_link" href=""><button class="btn btn-primary material-btn material-btn_danger">ARHIVIRAJ</button></a>
													</div>
												</div>
											</div>
										</div>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
				break;

				case "add":
				
					$ipWhitelistKey = $_GET['ipWhitelistKey'];
					if($ipWhitelistKey == "0Be50KXTB0krhxiUObGKvtf2XBJxKppR"){
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-list-ol idk_color_green" aria-hidden="true"></i> Dodaj novu IP adresu</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>ipwhitelist?page=list&ipWhitelistKey=<?php echo $ipWhitelistKey; ?>" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-md-offset-1 col-md-8">
								<form id="idk_form" action="<?php getSiteURL(); ?>ipwhitelist.php?page=add_imaddress" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="ipWhitelistKey" value="<?php echo $ipWhitelistKey; ?>">
									<div class="form-group">
										<label for="ipwl_ip" class="col-sm-3 control-label"><span class="text-danger">*</span> IP Adresa:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="ipwl_ip" id="ipwl_ip" placeholder="IP Adresa" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="ipwl_user" class="col-sm-3 control-label"><span class="text-danger">*</span> Korisnik:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="ipwl_user" id="ipwl_user" placeholder="Korisnik" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="ipwl_reason" class="col-sm-3 control-label">Razlog:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="ipwl_reason" id="ipwl_reason" placeholder="Razlog">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<br />
									<div class="form-group">
										<div class="col-sm-offset-2 col-sm-10 text-right">
											<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></button>
											<br /><small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
										</div>
									</div>
								</form>
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

				case "edit":
					if($getEmployeeStatus == 1){

						$ipwl_id = $_GET['id'];

						$query = $db->prepare("
										SELECT employee_firstname, employee_lastname, employee_jmbg, employee_email, employee_color, employee_rfid, employee_position, employee_dob, employee_doe, employee_address, employee_city, employee_country, employee_info, employee_status, employee_image
										FROM idk_employees
										WHERE employee_id = :employee_id");

						$query->execute(array(
									':employee_id' => $ipwl_id));

						$row = $query->fetch();

							$employee_firstname = $row['employee_firstname'];
							$employee_lastname = $row['employee_lastname'];
							$employee_jmbg = $row['employee_jmbg'];
							$employee_position = $row['employee_position'];
							$employee_dob = date('d.m.Y.', strtotime($row['employee_dob']));
							$employee_doe = date('d.m.Y.', strtotime($row['employee_doe']));
							$employee_email = $row['employee_email'];
							$employee_color = $row['employee_color'];
							$employee_rfid = $row['employee_rfid'];
							$employee_address = $row['employee_address'];
							$employee_city = $row['employee_city'];
							$employee_country = $row['employee_country'];
							$employee_info = $row['employee_info'];
							$employee_status = $row['employee_status'];

							if($row['employee_image'] == "none"){
								$employee_image = "none.jpg";
							}else{
								$employee_image = $row['employee_image'];
							}

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-list-ol idk_color_green" aria-hidden="true"></i> Uredi profile zaposlenika</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php echo $_SERVER['HTTP_REFERER']; ?>" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-md-offset-1 col-md-8">
								<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=edit_employees" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="employee_id" value="<?php echo $ipwl_id; ?>" />
									<div class="form-group">
										<label for="employee_firstname" class="col-sm-3 control-label"><span class="text-danger">*</span> Ime:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_firstname" id="employee_firstname" value="<?php echo $employee_firstname; ?>" placeholder="Ime" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_lastname" class="col-sm-3 control-label"><span class="text-danger">*</span> Prezime:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_lastname" id="employee_lastname" value="<?php echo $employee_lastname; ?>" placeholder="Prezime" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_jmbg" class="col-sm-3 control-label">JMBG:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="number" name="employee_jmbg" id="employee_jmbg" value="<?php echo $employee_jmbg; ?>" placeholder="JMBG">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_email" class="col-sm-3 control-label"><span class="text-danger">*</span> Primarni email:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="email" name="employee_email" id="employee_email" value="<?php echo $employee_email; ?>" placeholder="Primarni email" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_password" class="col-sm-3 control-label"><span class="text-danger">*</span> Lozinka:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="password" name="employee_password" id="employee_password" placeholder="Lozinka" >
												<span class="materail-input-block__line"></span>
											</div>
											<small>Ukoliko želite promijeniti lozinku, unesite novu.</small>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_color" class="col-sm-3 control-label">Boja:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="color" name="employee_color" id="employee_color" value="<?php echo $employee_color; ?>" />
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_rfid" class="col-sm-3 control-label">RFID:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_rfid" id="employee_rfid" value="<?php echo $employee_rfid; ?>" placeholder="RFID">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_position" class="col-sm-3 control-label">Pozicija:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="employee_position" name="employee_position">
												<option value=""></option>
												<?php
													$select_query = $db->prepare("
																		SELECT otherdata_data
																		FROM idk_otherdata
																		WHERE otherdata_group = :otherdata_group");

													$select_query->execute(array(
																	':otherdata_group' => 1));

													while($select_row = $select_query->fetch()) {

														if($employee_position == $select_row['otherdata_data']){ $selected = "selected"; }else{ $selected = ""; }

														echo "<option value='" . $select_row['otherdata_data'] . "' " . $selected . ">" . $select_row['otherdata_data'] . "</option>";
													}
												?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_dob" class="col-sm-3 control-label">Datum rođenja:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" data-init-set="false" data-large-default="true" data-format="d.m.Y." data-large-mode="true" data-modal="true" type="text" name="employee_dob" id="employee_dob" value="<?php echo $employee_dob; ?>" placeholder="Datum rođenja">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<script>
											$( function() {	$( "#employee_dob" ).dateDropper();	} );
										</script>
									</div>
									<div class="form-group">
										<label for="employee_doe" class="col-sm-3 control-label">Datum zaposlenja:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" data-init-set="false" data-large-default="true" data-format="d.m.Y." data-large-mode="true" data-modal="true" type="text" name="employee_doe" id="employee_doe" value="<?php echo $employee_doe; ?>" placeholder="Datum zaposlenja">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<script>
											$( function() {	$( "#employee_doe" ).dateDropper();	} );
										</script>
									</div>
									<div class="form-group">
										<label for="employee_address" class="col-sm-3 control-label">Adresa: </label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_address" id="employee_address" value="<?php echo $employee_address; ?>" placeholder="Adresa">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_city" class="col-sm-3 control-label">Grad:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_city" id="employee_city" value="<?php echo $employee_city; ?>" placeholder="Grad">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_country" class="col-sm-3 control-label">Država:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_country" id="employee_country" value="<?php echo $employee_country; ?>" placeholder="Država">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_info" class="col-sm-3 control-label">Ostale informacije:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="employee_info" id="employee_info" value="<?php echo $employee_info; ?>" placeholder="Ostale informacije">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_status" class="col-sm-3 control-label"><span class="text-danger">*</span> Status:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="employee_status" name="employee_status" required>
												<option value=""></option>
												<option value="4" <?php if($employee_status == "4"){ echo "selected"; } ?>>Saradnik</option>
												<option value="3" <?php if($employee_status == "3"){ echo "selected"; } ?>>Korisnik</option>
												<option value="2" <?php if($employee_status == "2"){ echo "selected"; } ?>>Super korisnik</option>
												<option value="1" <?php if($employee_status == "1"){ echo "selected"; } ?>>Administrator</option>
												<option value="0" <?php if($employee_status == "0"){ echo "selected"; } ?>>Deaktiviran</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="employee_image" class="col-sm-3 control-label">Fotografija:</label>
										<div class="col-sm-9">
											<div class="fileinput fileinput-new" data-provides="fileinput">
												<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;">
													<img src="<?php getSiteURL(); ?>files/employees/<?php echo $employee_image; ?>">
												</div>
												<input type="hidden" name="employee_image_url" value="<?php echo $employee_image; ?>" />
												<div>
													<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografiju</span><span class="fileinput-exists">Promijeni</span><input type="file" name="employee_image" id="employee_image"></span>
													<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
													<script>
														$(function (){
															$('#employee_image').change(function (){

																var ext = $('#employee_image').val().split('.').pop().toLowerCase();

																if($.inArray(ext, ['jpg', 'jpeg', 'png']) == -1) {
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

															})
														});
													</script>
												</div>
											</div>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-3"></label>
										<div class="col-sm-9">
											<div id="idk_alert_size" class="hidden">
												<div class="alert material-alert material-alert_danger">Greška: Fotografiju koju pokuštavate dodati je veća od dozvoljene veličine.</div>
											</div>
											<div id="idk_alert_ext" class="hidden">
												<div class="alert material-alert material-alert_danger">Greška: Format fotografije koju pokušavate dodati nije dozvoljen.</div>
											</div>
										</div>
									</div>
									<br />
									<div class="form-group">
										<div class="col-sm-offset-2 col-sm-10 text-right">
											<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-save" aria-hidden="true"></i> <span>Snimi</span></button>
											<br /><small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
										</div>
									</div>
								</form>
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


				case "add_imaddress":

					$ipWhitelistKey = $_POST['ipWhitelistKey'];
					$ipwl_user = $_POST['ipwl_user'];
					$ipwl_ip = $_POST['ipwl_ip'];
					$ipwl_reason = $_POST['ipwl_reason'];

					if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
						$ipwl_added_by_user = $_SERVER['HTTP_CLIENT_IP'];
					} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
						$ipwl_added_by_user = $_SERVER['HTTP_X_FORWARDED_FOR'];
					} else {
						$ipwl_added_by_user = $_SERVER['REMOTE_ADDR'];
					}					
					
					$ipwl_datetime = date("Y-m-d H:i:s");
					
					if($ipWhitelistKey == "0Be50KXTB0krhxiUObGKvtf2XBJxKppR"){
						
						//Add to whitelist
						$log_desc = "Obrisao bilješku: " . $note_txt . " ";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
										INSERT INTO idk_ipwhitelist
											(ipwl_user, ipwl_ip, ipwl_added_by, ipwl_reason, ipwl_datetime)
										VALUES
											(:ipwl_user, :ipwl_ip, :ipwl_added_by, :ipwl_reason, :ipwl_datetime)");

						$log_query->execute(array(
										':ipwl_user' => $ipwl_user,
										':ipwl_ip' => $ipwl_ip,
										':ipwl_added_by' => $ipwl_added_by_user,
										':ipwl_reason' => $ipwl_reason,
										':ipwl_datetime' => $ipwl_datetime
										));
						
						header("Location: ipwhitelist?page=list&ipWhitelistKey=$ipWhitelistKey&mess=1");
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

				case "del_note":
					if($getEmployeeStatus == 1){

						$note_id = $_GET['id'];

						//Get note_txt and note_dataid
						$note_open_query = $db->prepare("
													SELECT note_txt, note_dataid
													FROM idk_notes
													WHERE note_id = :note_id");

						$note_open_query->execute(array(
												':note_id' => $note_id));

						$note_open = $note_open_query->fetch();

							$note_txt = $note_open['note_txt'];
							$note_dataid = $note_open['note_dataid'];

						//Add to LOGS
						$log_desc = "Obrisao bilješku: " . $note_txt . " ";
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

						//Delete document from db
						$doc_del_query = $db->prepare("
													DELETE FROM idk_notes
													WHERE note_id = :note_id");

						$doc_del_query->execute(array(
											':note_id' => $note_id));

						header("Location: " . getSiteURLr() . "ipwhitelist?page=open&id=$note_dataid&mess=4");

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

				case "ip_whitelist_status":
				
					$ipWhitelistKey = $_POST['ipWhitelistKey'];
					
					
					if($ipWhitelistKey == "0Be50KXTB0krhxiUObGKvtf2XBJxKppR"){
						
						if($_POST['blocker_status'] == 1){
							$block_status = 1;
						}else{
							$block_status = 0;
						}
						
						//Save
						$query = $db->prepare("
										UPDATE idk_settings
										SET settings_value = :settings_value
										WHERE settings_name = :settings_name");
	
						$query->execute(array(
									':settings_value' => $block_status,
									':settings_name' => "crm_ip_blocker"));
									
									
					}elsE{
						
					}
					
				
				
				break;
				
				case "block":
				
					$ipWhitelistKey = $_GET['ipWhitelistKey'];
					$ipwl_id = $_GET['ipid'];
				
					if($ipWhitelistKey == "0Be50KXTB0krhxiUObGKvtf2XBJxKppR"){
	
						//Save
						$query = $db->prepare("
										UPDATE idk_ipwhitelist
										SET ipwl_status = :ipwl_status
										WHERE ipwl_id = :ipwl_id");
	
						$query->execute(array(
									':ipwl_status' => 1,
									':ipwl_id' => $ipwl_id));
	
	
						header("Location: ipwhitelist?page=list&ipWhitelistKey=$ipWhitelistKey&mess=2");
	
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

				case "del_task":
					if($getEmployeeStatus == 1){

						$task_id = $_GET['id'];

						//Get task_txt and task_dataid
						$task_open_query = $db->prepare("
													SELECT task_txt, task_dataid, task_emailnotifi, task_status
													FROM idk_tasks
													WHERE task_id = :task_id");

						$task_open_query->execute(array(
												':task_id' => $task_id));

						$task_open = $task_open_query->fetch();

							$task_txt = strip_tags($task_open['task_txt']);
							$task_dataid = $task_open['task_dataid'];
							$task_emailnotifi = $task_open['task_emailnotifi'];
							$task_status = $task_open['task_status'];

						//Add to LOGS
						$log_desc = "Obrisao zadatak: " . $task_txt . " ";
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

						//Delete task from db
						$task_del_query = $db->prepare("
													DELETE FROM idk_tasks
													WHERE task_id = :task_id");

						$task_del_query->execute(array(
											':task_id' => $task_id));

						//Remove Trigger
						if($task_emailnotifi != 0 && $task_status == 1){
							$url_tag = "email_task_" . $task_id . "";
							removeTrigger($url_tag);
						}

						header("Location: " . getSiteURLr() . "ipwhitelist?page=open&id=$task_dataid&mess=11");

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

				case "del_employee_info":
					if($getEmployeeStatus == 1){

						$ei_id = $_GET['id'];

						//Get ei_title, ei_data and ei_employeeid
						$phone_open_query = $db->prepare("
													SELECT ei_title, ei_data, ei_employeeid
													FROM idk_employees_info
													WHERE ei_id = :ei_id");

						$phone_open_query->execute(array(
												':ei_id' => $ei_id));

						$phone_open = $phone_open_query->fetch();

							$ei_title = $phone_open['ei_title'];
							$ei_data = $phone_open['ei_data'];
							$ei_employeeid = $phone_open['ei_employeeid'];

						//Add to LOGS
						$log_desc = "Obrisao telefon: " . $ei_title . " - " . $ei_data . "";
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

						//Delete phone from db
						$phone_del_query = $db->prepare("
													DELETE FROM idk_employees_info
													WHERE ei_id = :ei_id");

						$phone_del_query->execute(array(
											':ei_id' => $ei_id));

						header("Location: " . getSiteURLr() . "ipwhitelist?page=open&id=$ei_employeeid&mess=16");

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
			<footer><?php getCopyright(); ?></footer>
		</div>

</body>
</html>
