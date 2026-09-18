<?php
	/* PAGE koji je napravljen za unos dokumenata koji mogu biti od koristi drugim zaposlenicima.
	Korišteno malo na početku i zaboravljeno da postoji.
	Izbačeno iz menia iz sekcije Postavki 26.06.2023*/
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus = explode( ',' , getEmployeeStatus());

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: dokumenti?page=list");
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Dokumenti | <?php getTitle(); ?></title>

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
					<h1><i class="fa fa-paperclip idk_color_green" aria-hidden="true"></i> Dokumenti</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive" data-toggle="modal" data-target="#newDocModal"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj novi dokument</span></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			
			<!-- Modal new doc add -->
			<div class="modal material-modal material-modal_success fade text-left" id="newDocModal">
				<div class="modal-dialog ">
					<div class="modal-content material-modal__content" style="height: 50vh;">
						<div class="modal-header material-modal__header">
							<button class="close material-modal__close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title material-modal__title">Dodaj novi dokument</h4>
						</div>
						<div class="modal-body material-modal__body">
							<form id="add_new_doc" action="<?php getSiteURL(); ?>dokumenti.php?page=add_new_doc" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
								
								<input type="hidden" name="employee_id" value="<?php echo $logged_employee_id; ?>">
								<div class="form-group">
									<label for="naziv_doc" class="col-sm-4 control-label"><span class="text-danger">*</span> Naziv:</label>
									<div class="col-sm-8">
										<div class="materail-input-block materail-input-block_success">
											<input class="form-control materail-input" type="text" name="naziv_doc" id="naziv_doc" placeholder="Naziv" required>
											<span class="materail-input-block__line"></span>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="document_file" class="col-sm-4 control-label"><span class="text-danger">*</span> Dokument:</label>
									<div class="col-sm-8">
										<div class="fileinput fileinput-new" data-provides="fileinput">
											<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi dokument</span><span class="fileinput-exists">Promijeni</span><input type="file" name="document_file" id="document_file" required required></span> <i class="fa fa-info-circle fa-lg idk_margin_left10" data-toggle="tooltip" data-placement="right" title="Napomena: Dokument ne smije biti veći od 20MB! - Dozvoljeni formati: jpg, jpeg, pdf, doc, docx, xls, xlsx, txt, ppt, pptx, png" aria-hidden="true"></i>
											<span class="fileinput-filename"></span>
											<a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">×</a>
											<script>
												$(function (){
													$('#document_file').change(function (){

														var ext = $('#document_file').val().split('.').pop().toLowerCase();

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


													})
												});
											</script>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="opis_doc" class="col-sm-4 control-label"> Detaljni opis:</label>
									<div class="col-sm-8">
										<div class="materail-input-block materail-input-block_success">
											<textarea class="form-control materail-input material-textarea" name="opis_doc" id="opis_doc"></textarea>
											<span class="materail-input-block__line"></span>
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
							<button type="submit" class="btn btn-primary material-btn material-btn_primary" form="add_new_doc" >Dodaj</button>
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
								echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali dokument.</div>';
							}elseif($mess == 2){
								echo '<div class="alert material-alert material-alert_success">Uspješno ste obrisali dokument.</div>';
							}elseif($mess == 3){
								echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili dokument.</div>';
							}
						?>
						<div id="myTabs" class="panel-group material-tabs-group">
							<ul class="nav nav-tabs material-tabs material-tabs_primary">
								<li class="active"><a href="#dokumenti" class="material-tabs__tab-link" data-toggle="tab">Spisak dokumenata</a></li>
							</ul>
							<div class="tab-content materail-tabs-content">
								<div class="tab-pane fade active in" id="dokumenti">
									<script type="text/javascript">
										$(document).ready( function () {
											$('#docs_dt').DataTable( {

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
									<table id="docs_dt" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th></th>
												<th>Naziv</th>
												<th>Opis dokumenta</th>
												<th class="text-center">Preuzimanje</th>
												<th class="text-center">Zaposlenik</th>
												<th class="text-center">Akcija</th>
											</tr>
										</thead>
										<tbody>
											<?php
												
												$link_query = $db->prepare("
																		SELECT *, employee_firstname, employee_lastname
																		FROM idk_korisni_dokumenti
																		JOIN idk_employees 
																		ON idk_korisni_dokumenti.kd_employee_id = idk_employees.employee_id
																		WHERE kd_status = 0
																		");

												$link_query->execute();

												while($link_row = $link_query->fetch()){

													$kd_id = $link_row['kd_id'];
													$kd_naziv = $link_row['kd_naziv'];
													$kd_detaljni_opis = $link_row['kd_detaljni_opis'];
													$kd_file = $link_row['kd_file'];
													$kd_datetime = $link_row['kd_datetime'];
													$kd_employee_id = $link_row['kd_employee_id'];
													$kd_employee_ime = $link_row['employee_firstname']." ".$link_row['employee_lastname'];
													
													if($link_row['kd_icon'] == "jpg" OR $link_row['kd_icon'] == "png"){
														$document_icon = '<i class="fa fa-file-image-o fa-lg" aria-hidden="true"></i>';
													}elseif($link_row['kd_icon'] == "pdf"){
														$document_icon = '<i class="fa fa-file-pdf-o fa-lg" aria-hidden="true"></i>';
													}elseif($link_row['kd_icon'] == "doc" OR $link_row['kd_icon'] == "docx"){
														$document_icon = '<i class="fa fa-file-word-o fa-lg" aria-hidden="true"></i>';
													}elseif($link_row['kd_icon'] == "xls" OR $link_row['kd_icon'] == "xlsx" OR $link_row['kd_icon'] == "csv"){
														$document_icon = '<i class="fa fa-file-excel-o fa-lg" aria-hidden="true"></i>';
													}elseif($link_row['kd_icon'] == "txt"){
														$document_icon = '<i class="fa fa-file-text-o fa-lg" aria-hidden="true"></i>';
													}elseif($link_row['kd_icon'] == "ppt" OR $link_row['kd_icon'] == "pptx"){
														$document_icon = '<i class="fa fa-file-powerpoint-o fa-lg" aria-hidden="true"></i>';
													}else{
														$document_icon = '<i class="fa fa-file-o fa-lg" aria-hidden="true"></i>';
													}
													
													
											?>
											<tr>
												<td><?php echo $kd_id; ?></td>
												<td><?php echo $kd_naziv; ?></td>
												<td><?php echo $kd_detaljni_opis; ?></td>
												<td class="text-center"><a href="<?php getSiteURL(); ?>files/dokumenti/<?php echo $kd_file; ?>" class="btn material-btn material-btn_success main-container__column" target="_BLANK"><?php echo $document_icon; ?></a></td>
												<td class="text-center"><?php echo $kd_employee_ime; ?></td>
												<td class="text-center"><?php if($logged_employee_id == $kd_employee_id) {
													?>
													<div class="btn-group material-btn-group">
														<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
														<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
															<li><a href="#" class="material-dropdown-menu__link edit_link" data="<?php getSiteURL(); ?>dokumenti?page=edit_doc" data-toggle="modal" data-target="#editModal" data-kd_id="<?php echo $kd_id;?>" data-kd_naziv="<?php echo $kd_naziv;?>" data-kd_detaljni_opis="<?php echo $kd_detaljni_opis;?>" data-kd_file="<?php echo $kd_file;?>"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Uredi</a></li>
															<li class="idk_dropdown_danger"><a href="#" class="material-dropdown-menu__link archive" data-docname="<?php echo $kd_naziv; ?>" data="<?php getSiteURL(); ?>dokumenti?page=archive&id=<?php echo $kd_id; ?>" data-toggle="modal" data-target="#archiveModal"><i class="fa fa-trash-o" aria-hidden="true"></i> OBRIŠI</a></li>
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
												var docname = $(this).data("docname");
												$('#nazivstavke').html(docname);
											
												document.getElementById("archive_link").href = addressValue;
											});
										</script>
										<script>
											$(".edit_link").click(function () {
												var addressValue = $(this).attr("data");
												var kd_id = $(this).data("kd_id");
												var kd_naziv = $(this).data("kd_naziv");
												var kd_detaljni_opis = $(this).data("kd_detaljni_opis");
												var kd_file = $(this).data("kd_file");
												//$('#nazivstavke').html(linkname);
												$('#f_kd_id').val(kd_id);
												$('#f_naziv_doc').val(kd_naziv);
												$('#f_kd_detaljni_opis').val(kd_detaljni_opis);
												$('#f_kd_file').val(kd_file);
											
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
													<p>Jeste li sigurni da želite izbrisati dokument: <span id="nazivstavke"></span>?</p>
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
														<input type="hidden" name="f_kd_id" id="f_kd_id">
														<div class="form-group">
															<label for="f_naziv_doc" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
															<div class="col-sm-9">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="f_naziv_doc" id="f_naziv_doc" placeholder="Naziv" required>
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>
														<div class="form-group">
															<label for="f_kd_detaljni_opis" class="col-sm-3 control-label"> Detaljni opis:</label>
															<div class="col-sm-9">
																<div class="materail-input-block materail-input-block_success">
																	<textarea class="form-control materail-input material-textarea" name="f_kd_detaljni_opis" id="f_kd_detaljni_opis"></textarea>
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

				case "add_new_doc":
					
					$employee_id = $_POST['employee_id'];
					$naziv_doc = $_POST['naziv_doc'];
					$opis_doc = $_POST['opis_doc'];
					$datetime = date("Y-m-d H:i:s");
					
					//Upload document
					$document_file = $_FILES['document_file'];

					//File properties
					$file_name = $document_file['name'];
					$file_tmp = $document_file['tmp_name'];
					
					//File extension
					$file_ext = explode('.', $file_name);
					$file_ext = strtolower(end($file_ext));

					$allowed = array('jpg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png', 'csv');

					if(in_array($file_ext, $allowed)) {

						$file_name_new = uniqid() . '.' . $file_ext;
						$file_destination = "files/dokumenti/" . $file_name_new;

						if(move_uploaded_file($file_tmp, $file_destination)){}
					}
					
					//Add kontakt info to db
					$query = $db->prepare("
									INSERT INTO idk_korisni_dokumenti
										(kd_naziv, kd_detaljni_opis, kd_file, kd_icon, kd_datetime, kd_employee_id)
									VALUES
										(:kd_naziv, :kd_detaljni_opis, :kd_file, :kd_icon, :kd_datetime, :kd_employee_id)");

					$query->execute(array(
								':kd_naziv' => $naziv_doc,
								':kd_detaljni_opis' => $opis_doc,
								':kd_file' => $file_name_new,
								':kd_icon' => $file_ext,
								':kd_datetime' => $datetime,
								':kd_employee_id' => $employee_id));

					header("Location: dokumenti?page=list&mess=1");
					
				break;

				
				case "archive":

					$doc_id = $_GET['id'];

					//Update link
					$query_inbox = $db->prepare("
									UPDATE idk_korisni_dokumenti
									SET kd_status = :kd_status
									WHERE kd_id = :kd_id");

					$query_inbox->execute(array(
								':kd_status' => 1,
								':kd_id' => $doc_id));

					
					//Add to LOGS
					$log_desc = "Obrisao dokument: ".$doc_id."!";
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


					header("Location: " . getSiteURLr() . "dokumenti?page=list&mess=2");

				break;
				
				case "edit_doc":
				
					$f_kd_id = $_POST['f_kd_id'];
					$naziv_doc = $_POST['f_naziv_doc'];
					$opis_doc = $_POST['f_kd_detaljni_opis'];
					$datetime = date("Y-m-d H:i:s");

					//Update link
					$query_inbox = $db->prepare("
									UPDATE idk_korisni_dokumenti
									SET kd_naziv = :kd_naziv, kd_detaljni_opis = :kd_detaljni_opis
									WHERE kd_id = :kd_id");

					$query_inbox->execute(array(
								':kd_id' => $f_kd_id,
								':kd_naziv' => $naziv_doc,
								':kd_detaljni_opis' => $opis_doc));
								
					//Add to LOGS
					$log_desc = "Uredio dokument: ".$f_kd_id."!";
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


					header("Location: " . getSiteURLr() . "dokumenti?page=list&mess=3");
				
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