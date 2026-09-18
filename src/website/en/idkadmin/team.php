<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getUserStatus = getUserStatus();

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: team?page=list");
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
					<h1><i class="fas fa-users idk_color_green"></i> Naš tim</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>team?page=add" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></a>
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
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novog člana tima.</div>';
									}elseif($mess == 2){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili člana tima.</div>';
									}elseif($mess == 3){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali člana tima.</div>';
									}
								?>
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_table').DataTable({

											responsive: true,

											"order": [[ 0, "desc" ]],

											 "bAutoWidth": false,

											"aoColumns": [
													{ "width": "5%" },
													{ "width": "30%" },
													{ "width": "25%" },
													{ "width": "20%" },
													{ "width": "10%" },
													{ "width": "10%", "bSortable": false }
												]
										});
									} );
								</script>
								<table id="idk_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th class="text-center">ID</th>
											<th>Ime i prezime</th>
											<th>Pozicija</th>
											<th class="text-center">Jezici</th>
											<th class="text-center">Status</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
															SELECT team_id, team_status, team_lang_fullname, team_lang_position
															FROM idk_team
															LEFT JOIN idk_team_lang ON idk_team.team_id = idk_team_lang.team_lang_teamid
															WHERE team_status != :team_status
															GROUP BY team_id");

											$query->execute(array(':team_status' => 0));

											while($row = $query->fetch()){

												$team_id = $row['team_id'];

												$team_status = $row['team_status'];
												if($row['team_status'] == 1){
													$team_status = "Objavljen";
												}elseif($row['team_status'] == 2){
													$team_status = "U izradi";
												}elseif($row['team_status'] == 3){
													$team_status = "Na čekanju";
												}elseif($row['team_status'] == 0){
													$team_status = "Arhiviran";
												}

												$team_lang_fullname = $row['team_lang_fullname'];
												$team_lang_position = $row['team_lang_position'];
										?>
										<tr>
											<td class="text-center"><?php echo $team_id; ?></td>
											<td><?php echo $team_lang_fullname; ?></td>
											<td><?php echo $team_lang_position; ?></td>
											<td class="text-center">
												<?php
													$lang_query = $db->prepare("
																		SELECT lang_id, lang_code
																		FROM idk_langs");

													$lang_query->execute();

													echo '<ul class="list-inline">';

													while($lang_row = $lang_query->fetch()) {

														$lang_id = $lang_row['lang_id'];
														$lang_code = $lang_row['lang_code'];

														echo '<li class="list-inline-item"><a href="' . getSiteUrlr() . 'team?page=edit&id=' . $team_id . '&lang=' . $lang_id . '">' . $lang_code . '</a></li>';

													}

													echo '</ul>';
												?>
											</td>
											<td class="text-center"><?php echo $team_status; ?></td>
											<td class="text-center">
												<div class="btn-group material-btn-group">
													<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
													<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
														<li><a href="<?php getSiteURL(); ?>team?page=edit&id=<?php echo $team_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi</a></li>
														<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>team?page=archive&id=<?php echo $team_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
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
														<p>Jeste li sigurni da želite arhivirati izjavu korsnika?</p>
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
					if($getUserStatus  == 1 OR $getUserStatus  == 2){

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fas fa-users idk_color_green"></i> Dodaj novog člana tima</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>team?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
							<div class="col-md-8">
								<form id="idk_form" action="<?php getSiteURL(); ?>do?form=add_team" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<div class="form-group">
										<div class="col-sm-12">
											<input class="form-control idk_form_control_large" type="text" name="team_lang_fullname" id="team_lang_fullname" placeholder="Ime i prezime" required>
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-12">
											<input class="form-control" type="text" name="team_lang_position" id="team_lang_position" placeholder="Pozicija">
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-12">
											<input class="form-control" type="email" name="team_email" id="team_email" placeholder="Email">
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-12">
											<input class="form-control" type="text" name="team_phone" id="team_phone" placeholder="Telefon">
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-12">
											<input class="form-control" type="text" name="team_social" id="team_social" placeholder="Linkedin profil">
										</div>
									</div>
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
                                <div class="col-md-4">
                                    <div class="idk_side_form">
                                        <div class="form-group">
                                            <label for="team_status" class="control-label">Status:</label>
                                            <select class="form-control" id="team_status" name="team_status">
                                                <option value="1">Objavljen</option>
                                                <option value="2">U izradi</option>
                                                <option value="3">Na čekanju</option>
                                                <option value="0">Arhiviran</option>
                                            </select>
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label for="team_sort" class="control-label">Pozicija:</label>
                                            <input class="form-control" type="number" name="team_sort" id="team_sort" placeholder="Pozicija" value="0" required>
                                        </div>
                                        <br>
                                        <div class="form-group">
    										<div class="">
                                                <label for="team_img" class="control-label">Fotografija:</label><br>
    											<div class="fileinput fileinput-new" data-provides="fileinput">
    												<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;"></div>
    												<div>
    													<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografiju</span><span class="fileinput-exists">Promijeni</span><input type="file" name="team_img" id="team_img"></span>
    													<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
    													<script>
    														$(function (){
    															$('#team_img').change(function (){

    																var ext = $('#team_img').val().split('.').pop().toLowerCase();

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

						$team_id = $_GET['id'];

						//Set language
						if(isset($_GET['lang'])){
							$lang_id = $_GET['lang'];
						}else{
							$query_lang = $db->prepare("
											SELECT lang_id
											FROM idk_langs
											WHERE lang_default = :lang_default");

							$query_lang->execute(array(
										':lang_default' => 1));

							$row_lang = $query_lang->fetch();

							$lang_id = $row_lang['lang_id'];
						}

						//Edit
						$query = $db->prepare("
										SELECT team_img, team_email, team_phone, team_social, team_sort, team_status,
												team_lang_fullname, team_lang_position
										FROM idk_team
										INNER JOIN idk_team_lang ON idk_team.team_id = idk_team_lang.team_lang_teamid
										WHERE team_id = :team_id AND team_lang_langid = :team_lang_langid");

						$query->execute(array(
									':team_id' => $team_id,
									':team_lang_langid' => $lang_id));

						$row = $query->fetch();

							$team_email = $row['team_email'];
							$team_phone = $row['team_phone'];
							$team_social = $row['team_social'];
							$team_sort = $row['team_sort'];
							$team_status = $row['team_status'];

							$team_lang_fullname = $row['team_lang_fullname'];
							$team_lang_position = $row['team_lang_position'];

							if($row['team_img'] == NULL){
								$team_img = "none.jpg";
								$team_img_input = NULL;
							}else{
								$team_img = $row['team_img'];
								$team_img_input = $row['team_img'];
							}

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fas fa-users idk_color_green"></i> Uredi člana tima</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>team?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
							<div class="col-md-8">
								<form id="idk_form" action="<?php getSiteURL(); ?>do?form=edit_team" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="team_id" value="<?php echo $team_id; ?>" />
									<input type="hidden" name="team_lang_langid" value="<?php echo $lang_id; ?>" />
                                    <div class="form-group">
										<div class="col-sm-12">
											<input class="form-control idk_form_control_large" type="text" name="team_lang_fullname" value="<?php echo $team_lang_fullname; ?>" id="team_lang_fullname" placeholder="Ime i prezime" required>
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-12">
											<input class="form-control" type="text" name="team_lang_position" value="<?php echo $team_lang_position; ?>" id="team_lang_position" placeholder="Pozicija">
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-12">
											<input class="form-control" type="email" name="team_email" value="<?php echo $team_email; ?>" id="team_email" placeholder="Email">
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-12">
											<input class="form-control" type="text" name="team_phone" value="<?php echo $team_phone; ?>" id="team_phone" placeholder="Telefon">
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-12">
											<input class="form-control" type="text" name="team_social" value="<?php echo $team_social; ?>" id="team_social" placeholder="Linkedin profil">
										</div>
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
                                <div class="col-md-4">
                                    <div class="idk_side_form">
                                        <div class="form-group">
                                            <p>
												<?php
													$query_lang_name = $db->prepare("
																				SELECT lang_language
																				FROM idk_langs
																				WHERE lang_id = :lang_id");

													$query_lang_name->execute(array(
																':lang_id' => $lang_id));

													$row_lang_name = $query_lang_name->fetch();

													echo 'Jezik: <b>' . $lang_language = $row_lang_name['lang_language'] . '</b>';
												?>
											</p>
                                        </div>
										<br>
                                        <div class="form-group">
                                            <label for="team_status" class="control-label">Status:</label>
                                            <select class="form-control" id="team_status" name="team_status">
                                                <option value="1" <?php if($team_status == "1"){ echo "selected"; } ?>>Objavljen</option>
                                                <option value="2" <?php if($team_status == "2"){ echo "selected"; } ?>>U izradi</option>
                                                <option value="3" <?php if($team_status == "3"){ echo "selected"; } ?>>Na čekanju</option>
                                                <option value="0" <?php if($team_status == "0"){ echo "selected"; } ?>>Arhiviran</option>
                                            </select>
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label for="team_sort" class="control-label">Pozicija:</label>
                                            <input class="form-control" type="number" name="team_sort" value="<?php echo $team_sort; ?>" id="team_sort" placeholder="Pozicija" value="0" required>
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label for="team_img" class="control-label">Fotografija:</label><br>
											<div class="fileinput fileinput-new" data-provides="fileinput">
												<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;">
													<img src="<?php getSiteUrlFront(); ?>files/team/thumbs/<?php echo $team_img; ?>">
												</div>
												<input type="hidden" name="team_img_input" value="<?php echo $team_img_input; ?>" />
												<div>
													<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografiju</span><span class="fileinput-exists">Promijeni</span><input type="file" name="team_img" id="team_img"></span>
													<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
													<script>
														$(function (){
															$('#team_img').change(function (){

																var ext = $('#team_img').val().split('.').pop().toLowerCase();

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
    									<div id="idk_alert_size" class="hidden"><div class="alert material-alert material-alert_danger">Greška: Fotografiju koju pokuštavate dodati je veća od dozvoljene veličine.</div></div>
    									<div id="idk_alert_ext" class="hidden"><div class="alert material-alert material-alert_danger">Greška: Format fotografije koju pokušavate dodati nije dozvoljen.</div></div>
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

						$team_id = $_GET['id'];

						//Get
						$query_select = $db->prepare("
												SELECT team_lang_fullname
												FROM idk_team_lang
												WHERE team_lang_teamid = :team_lang_teamid");

						$query_select->execute(array(
											':team_lang_teamid' => $team_id));

						$row_select = $query_select->fetch();

						$team_lang_fullname = $row_select['team_lang_fullname'];

						//Save
						$query = $db->prepare("
										UPDATE idk_team
										SET team_status = :team_status
										WHERE team_id = :team_id");

						$query->execute(array(
									':team_status' => 0,
									':team_id' => $team_id));

						//Add to LOGS
						$log_desc = "Arhivirao člana tima: " . $team_lang_fullname . "";
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


						header("Location: " . getSiteURLr() . "team?page=list&mess=3");

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
