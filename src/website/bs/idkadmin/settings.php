<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getUserStatus = getUserStatus();

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: settings?page=open");
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

				case "open":
					if($getUserStatus == 1){
		?>
			<div class="row">
				<div class="col-xs-12">
					<h1><i class="fa fa-cogs idk_color_green" aria-hidden="true"></i> Postavke</h1>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-sm-2">
								<a class="idk_none" href="<?php getSiteURL(); ?>logs?page=all">
									<div class="idk_settings_box matchHeight">
										<p>LOG svih korisnika</p>
									</div>
								</a>
							</div>
							<div class="col-sm-2">
								<a class="idk_none" href="<?php getSiteURL(); ?>settings?page=settings-contact">
									<div class="idk_settings_box matchHeight">
										<p>Uredi kontakt informacije</p>
									</div>
								</a>
							</div>
							<div class="col-sm-2">
								<a class="idk_none" href="<?php getSiteURL(); ?>settings?page=language">
									<div class="idk_settings_box matchHeight">
										<p>Uredi jezične informacije</p>
									</div>
								</a>
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

				case "language":
					if($getUserStatus == 1){

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fas fa-language idk_color_green"></i> Uredi jezične informacije</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>settings?page=open" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-sm-12">
								<?php
									if(isset($_GET['mess'])) {
										$mess = $_GET['mess'];
									}else{
										$mess = 0;
									}

									if($mess == 1){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste uredili jezične informacije.</div>
										<script>$(function() { $('[href="#tab1"]').tab('show'); });</script>
									<?php
									}elseif($mess == 2){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste uredili jezične informacije.</div>
										<script>$(function() { $('[href="#tab2"]').tab('show'); });</script>
									<?php
									}elseif($mess == 3){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste uredili jezične informacije.</div>
										<script>$(function() { $('[href="#tab3"]').tab('show'); });</script>
									<?php } ?>
								<div id="myTabs" class="panel-group material-tabs-group">
		                            <ul class="nav nav-tabs material-tabs material-tabs_primary">
		                                <li class="active"><a href="#tab1" class="material-tabs__tab-link" data-toggle="tab">Bosanski</a></li>
		                            </ul>
		                            <div class="tab-content materail-tabs-content">
		                                <div class="tab-pane fade active in" id="tab1">
											<?php
												$fn = "../langs/bs.php";
												$file = fopen($fn, "a+");
												$size = filesize($fn);

												$text = fread($file, $size);
												fclose($file);
											?>
											<form action="<?php getSiteURL(); ?>do?form=edit_lang_bs" method="post">
												<textarea class="form-control" name="text_lang_bs" rows="15" cols="80" required><?php echo $text; ?></textarea>
												<div class="text-right">
		                                            <hr>
													<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-save" aria-hidden="true"></i> <span>Snimi</span></button>
												</div>
											</form>
		                                </div>
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

				case "settings-contact":
					if($getUserStatus == 1){
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-address-card-o idk_color_green" aria-hidden="true"></i> Kontakt informacije</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>settings?page=open" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<h5>Dodaj novu kontakt informaciju <small>(Za dodavanje nove informacije kontaktirajte developera!)</small></h5>
						<div class="row">
							<div class="col-xs-12 text-center">
								<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=add_settings_contact" method="post" class="form-horizontal" role="form">
									<div class="form-group">
										<div class="col-sm-3">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="settings_form_variable" id="settings_form_variable" placeholder="Variabla" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="settings_form_lang_name" id="settings_form_lang_name" placeholder="Naziv" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="settings_form_lang_value" id="settings_form_lang_value" placeholder="Podatak" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<div class="col-sm-3 text-left">
											<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></button>
										</div>
									</div>
								</form>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-xs-12">
								<h5>Trenutne kontakt informacije</h5>
								<?php
									if(isset($_GET['mess'])) {
										$mess = $_GET['mess'];
									}else{
										$mess = 0;
									}

									if($mess == 1){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novu kontakt informaciju.</div>';
									}elseif($mess == 2){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili kontakt informaciju.</div>';
									}elseif($mess == 3){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste obrisali kontakt informaciju.</div>';
									}
								?>
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_table').DataTable({

											responsive: true,

											"order": [[ 1, "asc" ]],

											 "bAutoWidth": false,

											"aoColumns": [
													{ "width": "15%" },
													{ "width": "30%" },
													{ "width": "35%" },
													{ "width": "20%" }
												]
										});
									} );
								</script>
								<table id="idk_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>Variabla</th>
											<th>Naziv</th>
											<th>Podatak</th>
											<th class="text-center">Jezici</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
															SELECT settings_form_id, settings_form_variable, settings_form_lang_name, settings_form_lang_value
															FROM idk_settings_form
															LEFT JOIN idk_settings_form_lang ON idk_settings_form.settings_form_id = idk_settings_form_lang.settings_form_lang_settingsformid
															WHERE settings_form_type = :settings_form_type AND settings_form_group = :settings_form_group
															GROUP BY settings_form_id");

											$query->execute(array(
															':settings_form_type' => 1,
															':settings_form_group' => 1));

											while($row = $query->fetch()){

												$settings_form_id = $row['settings_form_id'];
												$settings_form_variable = $row['settings_form_variable'];
												$settings_form_lang_name = $row['settings_form_lang_name'];
												$settings_form_lang_value = $row['settings_form_lang_value'];
										?>
										<tr>
											<td><?php echo $settings_form_variable; ?></td>
											<td><?php echo $settings_form_lang_name; ?></td>
											<td><?php echo $settings_form_lang_value; ?></td>
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

														echo '<li class="list-inline-item"><a href="' . getSiteUrlr() . 'settings?page=settings-contact-edit&id=' . $settings_form_id . '&lang=' . $lang_id . '">' . $lang_code . '</a></li>';

													}

													echo '</ul>';
												?>
											</td>
										</tr>
										<?php } ?>
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
							<div class="alert material-alert material-alert_danger">
								<h4>NEMATE PRIVILEGIJE!</h4>
								<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
								<br />
								<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
							</div>
						';
					}
				break;

				case "settings-contact-edit":
					if($getUserStatus  == 1 OR $getUserStatus  == 2){

						$settings_form_id = $_GET['id'];

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
										SELECT settings_form_lang_name, settings_form_lang_value
										FROM idk_settings_form
										INNER JOIN idk_settings_form_lang ON idk_settings_form.settings_form_id = idk_settings_form_lang.settings_form_lang_settingsformid
										WHERE settings_form_id = :settings_form_id AND settings_form_lang_langid = :settings_form_lang_langid");

						$query->execute(array(
									':settings_form_id' => $settings_form_id,
									':settings_form_lang_langid' => $lang_id));

						$row = $query->fetch();

							$settings_form_lang_name = $row['settings_form_lang_name'];
							$settings_form_lang_value = $row['settings_form_lang_value'];

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="far fa-copy idk_color_green" aria-hidden="true"></i> Uredi kontakt informaciju</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>settings?page=settings-contact" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
								<form id="idk_form" action="<?php getSiteURL(); ?>do?form=edit_settings_contact" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="settings_form_id" value="<?php echo $settings_form_id; ?>" />
									<input type="hidden" name="settings_form_lang_langid" value="<?php echo $lang_id; ?>" />
									<div class="form-group">
										<div class="col-sm-12">
											<input class="form-control" type="text" name="settings_form_lang_name" value="<?php echo $settings_form_lang_name; ?>" id="settings_form_lang_name" placeholder="Naziv" required>
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-12">
											<input class="form-control" type="text" name="settings_form_lang_value" value="<?php echo $settings_form_lang_value; ?>" id="settings_form_lang_value" placeholder="Podatak" required>
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
								<div class="col-md-1"></div>
								<div class="col-md-4">
                                    <div class="idk_side_form">
										<div class="form-group row">
                                            <div class="col-xs-6">
                                                <?php
													$query_lang_name = $db->prepare("
																				SELECT lang_language
																				FROM idk_langs
																				WHERE lang_id = :lang_id");

													$query_lang_name->execute(array(
																':lang_id' => $lang_id));

													$row_lang_name = $query_lang_name->fetch();

													echo '<p>Jezik: <b>' . $row_lang_name['lang_language'] . '</b></p>';
												?>
                                            </div>
                                            <div class="col-xs-6 text-right">
                                                <?php
													$lang_query = $db->prepare("
																		SELECT lang_id, lang_code
																		FROM idk_langs");

													$lang_query->execute();

													echo '<ul class="list-inline">';

													while($lang_row = $lang_query->fetch()) {

														$lang_id = $lang_row['lang_id'];
														$lang_code = $lang_row['lang_code'];

														echo '<li class="list-inline-item"><a href="' . getSiteUrlr() . 'settings?page=settings-contact-edit&id=' . $settings_form_id . '&lang=' . $lang_id . '">' . $lang_code . '</a></li>';

													}

													echo '</ul>';
												?>
                                            </div>
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

			}
		?>
			<footer><?php getCopyright(); ?></footer>
		</div>
	</div>
</body>
</html>
