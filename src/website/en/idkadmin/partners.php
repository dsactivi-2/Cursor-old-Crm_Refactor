<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getUserStatus = getUserStatus();

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: partners?page=list");
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
					<h1><i class="far fa-handshake idk_color_green"></i> Partneri</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>partners?page=add" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></a>
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
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novog partnera.</div>';
									}elseif($mess == 2){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili profil partnera.</div>';
									}elseif($mess == 3){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste obrisali prodil partnera.</div>';
									}
								?>
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_table').DataTable({

											responsive: true,

											"order": [[ 1, "asc" ]],

											 "bAutoWidth": false,

											"aoColumns": [
													{ "width": "80%" },
													{ "width": "10%" },
                                                    { "width": "10%", "bSortable": false }
												]
										});
									} );
								</script>
								<table id="idk_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>Naziv</th>
											<th class="text-center">Raspored</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
															SELECT partner_id, partner_name, partner_sort
															FROM idk_partners");

											$query->execute();

											while($row = $query->fetch()){

												$partner_id = $row['partner_id'];
												$partner_name = $row['partner_name'];
												$partner_sort = $row['partner_sort'];
										?>
										<tr>
											<td><?php echo $partner_name; ?></td>
											<td class="text-center"><?php echo $partner_sort; ?></td>
											<td class="text-center">
												<div class="btn-group material-btn-group">
													<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
													<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
														<li><a href="<?php getSiteURL(); ?>partners?page=edit&id=<?php echo $partner_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi</a></li>
														<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>partners?page=archive&id=<?php echo $partner_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Obriši</a></li>
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
														<h4 class="modal-title material-modal__title">Brisanje</h4>
													</div>
													<div class="modal-body material-modal__body">
														<p>Jeste li sigurni da želite obrisati profil partnera?</p>
													</div>
													<div class="modal-footer material-modal__footer">
														<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
														<a id="archive_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
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
					if($getUserStatus  == 1 OR $getUserStatus  == 2){

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="far fa-handshake idk_color_green"></i> Dodaj novog partnera</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>partners?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
                        <br>
						<div class="row">
							<div class="col-md-1"></div>
							<div class="col-md-6">
								<form id="idk_form" action="<?php getSiteURL(); ?>do?form=add_partner" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
                                    <div class="form-group">
                                        <label for="partner_name" class="control-label">Naziv:</label>
                                        <input class="form-control" type="text" name="partner_name" id="partner_name" placeholder="Naziv">
                                    </div>
                                    <div class="form-group">
                                        <div class="">
                                            <label for="partner_logo" class="control-label">Logo:</label><br>
                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;"></div>
                                                <div>
                                                    <span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi logo</span><span class="fileinput-exists">Promijeni</span><input type="file" name="partner_logo" id="partner_logo"></span>
                                                    <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
                                                    <script>
                                                        $(function (){
                                                            $('#partner_logo').change(function (){

                                                                var ext = $('#partner_logo').val().split('.').pop().toLowerCase();

                                                                if($.inArray(ext, ['jpg', 'jpeg', 'png', '']) == -1) {
                                                                    $('#idk_alert_ext').removeClass('hidden');
                                                                    this.value = null;
                                                                }else{
                                                                    $('#idk_alert_ext').addClass('hidden');
                                                                }

                                                                var f = this.files[0];

                                                                if (f.size > 20388600 || f.fileSize > 20388600){
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
                                    <div id="idk_alert_size" class="hidden"><div class="alert material-alert material-alert_danger">Greška: Fotografiju koju pokuštavate dodati je veća od dozvoljene veličine.</div></div>
                                    <div id="idk_alert_ext" class="hidden"><div class="alert material-alert material-alert_danger">Greška: Format fotografije koju pokušavate dodati nije dozvoljen.</div></div>
                                    <div class="form-group">
                                        <label for="partner_link" class="control-label">Link:</label>
                                        <input class="form-control" type="text" name="partner_link" id="partner_link" placeholder="Link">
                                    </div>
                                    <div class="form-group">
                                        <label for="partner_sort" class="control-label">Raspored:</label>
                                        <input class="form-control" type="number" name="partner_sort" id="partner_sort" placeholder="Raspored">
                                    </div>
                                    <br>
                                    <hr>
									<div class="form-group">
										<div class="col-sm-12 text-right">
											<ul class="list-inline">
												<li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
												<li>
													<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></button>
												</li>
											</ul>
										</div>
									</div>
                                </div>
							</form>
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
					if($getUserStatus  == 1 OR $getUserStatus  == 2){

						$partner_id = $_GET['id'];

						//Edit content
						$query = $db->prepare("
										SELECT partner_name, partner_logo, partner_link, partner_sort
										FROM idk_partners
										WHERE partner_id = :partner_id");

						$query->execute(array(
									':partner_id' => $partner_id));

						$row = $query->fetch();

							$partner_name = $row['partner_name'];
							$partner_logo = $row['partner_logo'];
							$partner_link = $row['partner_link'];
							$partner_sort = $row['partner_sort'];

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="far fa-handshake idk_color_green"></i> Uredi profil partnera</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>partners?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
                        <br>
						<div class="row">
							<div class="col-md-1"></div>
							<div class="col-md-6">
								<form id="idk_form" action="<?php getSiteURL(); ?>do?form=edit_partner" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="partner_id" value="<?php echo $partner_id; ?>" />
                                    <div class="form-group">
                                        <label for="partner_name" class="control-label">Naziv:</label>
                                        <input class="form-control" type="text" name="partner_name" id="partner_name" value="<?php echo $partner_name; ?>">
                                    </div>
                                    <div class="form-group">
                                        <div class="">
                                            <label for="partner_logo" class="control-label">Logo:</label><br>
                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;">
                                                    <img src="<?php getSiteUrlFront(); ?>files/partners/<?php echo $partner_logo; ?>">
                                                </div>
                                                <input type="hidden" name="partner_logo_input" value="<?php echo $partner_logo; ?>" />
                                                <div>
                                                    <span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi logo</span><span class="fileinput-exists">Promijeni</span><input type="file" name="partner_logo" id="partner_logo"></span>
                                                    <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
                                                    <script>
                                                        $(function (){
                                                            $('#partner_logo').change(function (){

                                                                var ext = $('#partner_logo').val().split('.').pop().toLowerCase();

                                                                if($.inArray(ext, ['jpg', 'jpeg', 'png', '']) == -1) {
                                                                    $('#idk_alert_ext').removeClass('hidden');
                                                                    this.value = null;
                                                                }else{
                                                                    $('#idk_alert_ext').addClass('hidden');
                                                                }

                                                                var f = this.files[0];

                                                                if (f.size > 20388600 || f.fileSize > 20388600){
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
                                    <div id="idk_alert_size" class="hidden"><div class="alert material-alert material-alert_danger">Greška: Fotografiju koju pokuštavate dodati je veća od dozvoljene veličine.</div></div>
                                    <div id="idk_alert_ext" class="hidden"><div class="alert material-alert material-alert_danger">Greška: Format fotografije koju pokušavate dodati nije dozvoljen.</div></div>
                                    <div class="form-group">
                                        <label for="partner_link" class="control-label">Link:</label>
                                        <input class="form-control" type="text" name="partner_link" id="partner_link" value="<?php echo $partner_link; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="partner_sort" class="control-label">Raspored:</label>
                                        <input class="form-control" type="number" name="partner_sort" id="partner_sort" value="<?php echo $partner_sort; ?>">
                                    </div>
                                    <hr>
									<div class="form-group">
										<div class="col-sm-12 text-right">
											<ul class="list-inline">
												<li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
												<li>
													<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-save" aria-hidden="true"></i> <span>Snimi</span></button>
												</li>
											</ul>
										</div>
									</div>
                                </div>
							</form>
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

				case "archive":
					if($getUserStatus  == 1){

						$partner_id = $_GET['id'];

						//Get
						$query_select = $db->prepare("
												SELECT partner_name
												FROM idk_partners
												WHERE partner_id = :partner_id");

						$query_select->execute(array(
											':partner_id' => $partner_id));

						$row_select = $query_select->fetch();

						$partner_name = $row_select['partner_name'];

						//Delete
                        $slide_del_query = $db->prepare("
    												DELETE FROM idk_partners
    												WHERE partner_id = :partner_id");

    					$slide_del_query->execute(array(
    										':partner_id' => $partner_id));

						//Add to LOGS
						$log_desc = "Obrisao profil partnera: " . $partner_name . "";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_userid, log_desc, log_date)
										VALUES
											(:log_userid, :log_desc, :log_date)");

						$log_query->execute(array(
										':log_userid' => $logged_user_id,
										':log_desc' => $log_desc,
										':log_date' => $log_date));


						header("Location: " . getSiteURLr() . "partners?page=list&mess=3");

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
	</div>
</body>
</html>
