<?php
	/* PAGE koji je napravljen za unos linkova koji mogu biti od koristi drugim zaposlenicima.
	Korišteno malo na početku i zaboravljeno da postoji.
	Izbačeno iz menia iz sekcije Postavki 26.06.2023*/
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus = explode( ',' , getEmployeeStatus());

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: linkovi?page=list");
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Linkovi | <?php getTitle(); ?></title>

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

				case "list":
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-link idk_color_green" aria-hidden="true"></i> Linkovi</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive" data-toggle="modal" data-target="#newLinkModal"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj novi link</span></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			
			<!-- Modal new link add -->
			<div class="modal material-modal material-modal_success fade text-left" id="newLinkModal">
				<div class="modal-dialog ">
					<div class="modal-content material-modal__content">
						<div class="modal-header material-modal__header">
							<button class="close material-modal__close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title material-modal__title">Dodaj novi link</h4>
						</div>
						<div class="modal-body material-modal__body">
							<form id="add_new_link" action="<?php getSiteURL(); ?>linkovi.php?page=add_new_link" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
								<input type="hidden" name="employee_id" value="<?php echo $logged_employee_id; ?>">
								<div class="form-group">
									<label for="naziv_link" class="col-sm-4 control-label"><span class="text-danger">*</span> Naziv:</label>
									<div class="col-sm-8">
										<div class="materail-input-block materail-input-block_success">
											<input class="form-control materail-input" type="text" name="naziv_link" id="naziv_link" placeholder="Naziv" required>
											<span class="materail-input-block__line"></span>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="url_link" class="col-sm-4 control-label"><span class="text-danger">*</span> URL:</label>
									<div class="col-sm-8">
										<div class="materail-input-block materail-input-block_success">
											<input class="form-control materail-input" type="text" name="url_link" id="url_link" placeholder="URL" required>
											<span class="materail-input-block__line"></span>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="opis_link" class="col-sm-4 control-label"> Detaljni opis:</label>
									<div class="col-sm-8">
										<div class="materail-input-block materail-input-block_success">
											<textarea class="form-control materail-input material-textarea" name="opis_link" id="opis_link"></textarea>
											<span class="materail-input-block__line"></span>
										</div>
									</div>
								</div>
							</form>
						</div>
						<div class="modal-footer material-modal__footer">
							<button type="submit" class="btn btn-primary material-btn material-btn_primary" form="add_new_link" >Dodaj</button>
							<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
						</div>
					</div>
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
								echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali link.</div>';
							}elseif($mess == 2){
								echo '<div class="alert material-alert material-alert_success">Uspješno ste obrisali link.</div>';
							}elseif($mess == 3){
								echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili link.</div>';
							}
						?>
						<div id="myTabs" class="panel-group material-tabs-group">
							<ul class="nav nav-tabs material-tabs material-tabs_primary">
								<li class="active"><a href="#inbox" class="material-tabs__tab-link" data-toggle="tab">Spisak linkova</a></li>
							</ul>
							<div class="tab-content materail-tabs-content">
								<div class="tab-pane fade active in" id="inbox">
									<script type="text/javascript">
										$(document).ready( function () {
											$('#links_dt').DataTable( {

												responsive: true,

												"order": [[ 0, "desc" ]],

												"aoColumns": [
														{"width": "0%", "bVisible": false},
														{"width": "20%"},
														{"width": "45%"},
														{"width": "5%", "bSortable": false},
														{"width": "20%"},
														{"width": "10%"}
													]
											});
										} );
									</script>
									<table id="links_dt" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th></th>
												<th>Naziv</th>
												<th>Opis linka</th>
												<th class="text-center">URL</th>
												<th class="text-center">Zaposlenik</th>
												<th class="text-center">Akcija</th>
											</tr>
										</thead>
										<tbody>
											<?php
												
												$link_query = $db->prepare("
																		SELECT *, employee_firstname, employee_lastname
																		FROM idk_korisni_linkovi
																		JOIN idk_employees 
																		ON idk_korisni_linkovi.kl_employee_id = idk_employees.employee_id
																		WHERE kl_status = 0
																		");

												$link_query->execute();

												while($link_row = $link_query->fetch()){

													$kl_id = $link_row['kl_id'];
													$kl_naziv = $link_row['kl_naziv'];
													$kl_detaljni_opis = $link_row['kl_detaljni_opis'];
													$kl_link = $link_row['kl_link'];
													$kl_datetime = $link_row['kl_datetime'];
													$kl_employee_id = $link_row['kl_employee_id'];
													$kl_employee_ime = $link_row['employee_firstname']." ".$link_row['employee_lastname'];
													if(strpos($kl_link, 'http') !== false)
														$kl_link_f = $kl_link;
													else
														$kl_link_f = "//".$kl_link;
													
											?>
											<tr>
												<td><?php echo $kl_id; ?></td>
												<td><?php echo $kl_naziv; ?></td>
												<td><?php echo $kl_detaljni_opis; ?></td>
												<td class="text-center"><a href="<?php echo $kl_link_f; ?>" class="btn material-btn material-btn_success main-container__column" target="_blank"><i class="fa fa-link"></i></a></td>
												<td class="text-center"><?php echo $kl_employee_ime; ?></td>
												<td class="text-center"><?php if($logged_employee_id == $kl_employee_id) {
													?>
													<div class="btn-group material-btn-group">
														<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
														<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
															<li><a href="#" class="material-dropdown-menu__link edit_link" data="<?php getSiteURL(); ?>linkovi?page=edit_link" data-toggle="modal" data-target="#editModal" data-kl_id="<?php echo $kl_id;?>" data-kl_naziv="<?php echo $kl_naziv;?>" data-kl_detaljni_opis="<?php echo $kl_detaljni_opis;?>" data-kl_link="<?php echo $kl_link;?>"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Uredi</a></li>
															<li class="idk_dropdown_danger"><a href="#" class="material-dropdown-menu__link archive" data-linkname="<?php echo $kl_naziv; ?>" data="<?php getSiteURL(); ?>linkovi?page=archive&id=<?php echo $kl_id; ?>" data-toggle="modal" data-target="#archiveModal"><i class="fa fa-trash-o" aria-hidden="true"></i> OBRIŠI</a></li>
														</ul>
													</div>
													<?php
												} ?></td>
											</tr>
											<?php	}	?>
										</tbody>
										<script>
											$(".archive").click(function () {
												var addressValue = $(this).attr("data");
												var linkname = $(this).data("linkname");
												$('#nazivstavke').html(linkname);
											
												document.getElementById("archive_link").href = addressValue;
											});
										</script>
										<script>
											$(".edit_link").click(function () {
												var addressValue = $(this).attr("data");
												var kl_id = $(this).data("kl_id");
												var kl_naziv = $(this).data("kl_naziv");
												var kl_detaljni_opis = $(this).data("kl_detaljni_opis");
												var kl_link = $(this).data("kl_link");
												//$('#nazivstavke').html(linkname);
												$('#f_kl_id').val(kl_id);
												$('#f_naziv_link').val(kl_naziv);
												$('#f_kl_detaljni_opis').val(kl_detaljni_opis);
												$('#f_kl_link').val(kl_link);
											
												document.getElementById("form_edit_link").action = addressValue;
											});
										</script>
									</table>
									<!-- Modal za arhiviranje-->
									<div class="modal material-modal material-modal_danger fade" id="archiveModal">
										<div class="modal-dialog">
											<div class="modal-content material-modal__content">
												<div class="modal-header material-modal__header">
													<button class="close material-modal__close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title material-modal__title">Brisanje</h4>
												</div>
												<div class="modal-body material-modal__body">
													<p>Jeste li sigurni da želite izbrisati link: <span id="nazivstavke"></span>?</p>
												</div>
												<div class="modal-footer material-modal__footer">
													<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
													<a id="archive_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
												</div>
											</div>
										</div>
									</div>
									<!-- Modal za uredjivanje-->
									<div class="modal material-modal material-modal_primary fade" id="editModal">
										<div class="modal-dialog">
											<div class="modal-content material-modal__content">
												<div class="modal-header material-modal__header">
													<button class="close material-modal__close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title material-modal__title">Uređivanje</h4>
												</div>
												<div class="modal-body material-modal__body">
													<form id="form_edit_link" action="" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
														<input type="hidden" name="f_kl_id" id="f_kl_id">
														<div class="form-group">
															<label for="f_naziv_link" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
															<div class="col-sm-9">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="f_naziv_link" id="f_naziv_link" placeholder="Naziv" required>
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>
														<div class="form-group">
															<label for="f_kl_link" class="col-sm-3 control-label"><span class="text-danger">*</span> URL:</label>
															<div class="col-sm-9">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="f_kl_link" id="f_kl_link" placeholder="URL" required>
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>
														<div class="form-group">
															<label for="f_kl_detaljni_opis" class="col-sm-3 control-label"> Detaljni opis:</label>
															<div class="col-sm-9">
																<div class="materail-input-block materail-input-block_success">
																	<textarea class="form-control materail-input material-textarea" name="f_kl_detaljni_opis" id="f_kl_detaljni_opis"></textarea>
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>
													</form>
												</div>
												<div class="modal-footer material-modal__footer">
													<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
													<button form="form_edit_link" type="submit" class="btn btn-primary material-btn material-btn_primary">SPREMI</button>
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

				case "add_new_link":
					
					$employee_id = $_POST['employee_id'];
					$naziv_link = $_POST['naziv_link'];
					$url_link = $_POST['url_link'];
					$opis_link = $_POST['opis_link'];
					$datetime = date("Y-m-d H:i:s");
					
					//Add kontakt info to db
					$query = $db->prepare("
									INSERT INTO idk_korisni_linkovi
										(kl_naziv, kl_detaljni_opis, kl_link, kl_datetime, kl_employee_id)
									VALUES
										(:kl_naziv, :kl_detaljni_opis, :kl_link, :kl_datetime, :kl_employee_id)");

					$query->execute(array(
								':kl_naziv' => $naziv_link,
								':kl_detaljni_opis' => $opis_link,
								':kl_link' => $url_link,
								':kl_datetime' => $datetime,
								':kl_employee_id' => $employee_id));

					header("Location: linkovi?page=list&mess=1");
					
				break;

				
				case "archive":

					$link_id = $_GET['id'];

					//Update link
					$query_inbox = $db->prepare("
									UPDATE idk_korisni_linkovi
									SET kl_status = :kl_status
									WHERE kl_id = :kl_id");

					$query_inbox->execute(array(
								':kl_status' => 1,
								':kl_id' => $link_id));

					
					//Add to LOGS
					$log_desc = "Obrisao link: ".$link_id."!";
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


					header("Location: " . getSiteURLr() . "linkovi?page=list&mess=2");

				break;
				
				case "edit_link":
				
					$f_kl_id = $_POST['f_kl_id'];
					$naziv_link = $_POST['f_naziv_link'];
					$url_link = $_POST['f_kl_link'];
					$opis_link = $_POST['f_kl_detaljni_opis'];
					$datetime = date("Y-m-d H:i:s");

					//Update link
					$query_inbox = $db->prepare("
									UPDATE idk_korisni_linkovi
									SET kl_naziv = :kl_naziv, kl_detaljni_opis = :kl_detaljni_opis, kl_link = :kl_link
									WHERE kl_id = :kl_id");

					$query_inbox->execute(array(
								':kl_id' => $f_kl_id,
								':kl_naziv' => $naziv_link,
								':kl_detaljni_opis' => $opis_link,
								':kl_link' => $url_link));
								
					//Add to LOGS
					$log_desc = "Uredio link: ".$f_kl_id."!";
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


					header("Location: " . getSiteURLr() . "linkovi?page=list&mess=3");
				
				break;

			}
		?>
			<footer><?php getCopyright(); ?></footer>
		</div>
	</div>
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