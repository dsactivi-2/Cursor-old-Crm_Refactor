<?php
include("includes/functions.php");
include("includes/common.php");

$getUserStatus = getUserStatus();

if (isset($_REQUEST["page"])) {
	$page = $_REQUEST["page"];
} else {
	header("Location: post-category?page=list");
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
			switch ($page) {

				case "list":
			?>
					<div class="row">
						<div class="col-xs-8">
							<h1><i class="fas fa-tasks idk_color_green"></i> Proizvodi kategorije</h1>
						</div>
						<div class="col-xs-4 text-right idk_margin_top10">
							<a href="<?php getSiteURL(); ?>product-category?page=add" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></a>
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
										if (isset($_GET['mess'])) {
											$mess = $_GET['mess'];
										} else {
											$mess = 0;
										}

										if ($mess == 1) {
											echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novu kategoriju.</div>';
										} elseif ($mess == 2) {
											echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili kategoriju.</div>';
										} elseif ($mess == 3) {
											echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali kategoriju.</div>';
										}
										?>
										<?php getCategories(); ?>
										<script>
											$(".obrisi").click(function() {
												var addressValue = $(this).attr("data");
												document.getElementById("obrisi_link").href = addressValue;
											});
										</script>
										<!-- Modal delete-->
										<div class="modal material-modal material-modal_danger fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="modalDeleteLabel" aria-hidden="true">
											<div class="modal-dialog">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</span><span class="sr-only">Zatvori</span></button>
														<h4 class="modal-title material-modal__title" id="modalDeleteLabel">Brisanje</h4>
													</div>
													<div class="modal-body material-modal__body">
														<p>Jeste li sigurni da želite obrisati kategoriju?</p>
														<p><strong>Napomena: Sve potkategorije će biti obrisane!</strong></p>
													</div>
													<div class="modal-footer material-modal__footer">
														<button type="button" class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
														<a id="obrisi_link" href=""><button type="button" class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
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

				case "add":
					if ($getUserStatus  == 1 or $getUserStatus  == 2) {

					?>
						<div class="row">
							<div class="col-xs-8">
								<h1><i class="fas fa-tasks idk_color_green"></i> Dodaj novu kategoriju za proizvode</h1>
							</div>
							<div class="col-xs-4 text-right idk_margin_top10">
								<a href="<?php getSiteURL(); ?>product-category?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
											<form id="idk_form" action="<?php getSiteURL(); ?>do?form=add_productcat" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
												<div class="form-group">
													<div class="col-sm-12">
														<input class="form-control idk_form_control_large" type="text" name="productcat_lang_name" id="productcat_lang_name" placeholder="Naziv kategorije" required>
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
													<label for="productcat_status" class="control-label">Status:</label>
													<select class="form-control" id="productcat_status" name="productcat_status">
														<option value="1">Objavljen</option>
														<option value="2">U izradi</option>
														<option value="3">Na čekanju</option>
														<option value="0">Arhiviran</option>
													</select>
												</div>
												<br>
												<div class="form-group">
													<label for="productcat_sub" class="control-label">Pripada kategoriji:</label>
													<select class="form-control" id="productcat_sub" name="productcat_sub">
														<option value="0">Samostalna</option>
														<?php
														// $query_cat = $db->prepare("
														//                 SELECT productcat_id, productcat_lang_name
														//                 FROM idk_productcat
														//                 INNER JOIN idk_productcat_lang ON idk_productcat.productcat_id = idk_productcat_lang.productcat_lang_productid
														//                 WHERE productcat_lang_langid = :productcat_lang_langid AND productcat_sub = :productcat_sub
														//                 GROUP BY productcat_id");

														// $query_cat->execute(array(
														//                 ':productcat_lang_langid' => 1,
														// 								':productcat_sub' => 0));

														//Show all categories, not just main ones
														$query_cat = $db->prepare("
                                                                    SELECT productcat_id, productcat_lang_name
                                                                    FROM idk_productcat
                                                                    INNER JOIN idk_productcat_lang ON idk_productcat.productcat_id = idk_productcat_lang.productcat_lang_productid
                                                                    WHERE productcat_lang_langid = :productcat_lang_langid
                                                                    GROUP BY productcat_id");

														$query_cat->execute(array(
															':productcat_lang_langid' => 1
														));

														while ($row_cat = $query_cat->fetch()) {

															$productcat_id = $row_cat['productcat_id'];
															$productcat_lang_name = $row_cat['productcat_lang_name'];
														?>
															<option value="<?php echo $productcat_id; ?>"><?php echo $productcat_lang_name; ?></option>
														<?php } ?>
													</select>
												</div>
												<br>
												<div class="form-group">
													<div class="">
														<label for="productcat_lang_img" class="control-label">Fotografija:</label><br>
														<div class="fileinput fileinput-new" data-provides="fileinput">
															<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;"></div>
															<div>
																<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografiju</span><span class="fileinput-exists">Promijeni</span><input type="file" name="productcat_lang_img" id="productcat_lang_img"></span>
																<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
																<script>
																	$(function() {
																		$('#productcat_lang_img').change(function() {

																			var ext = $('#productcat_lang_img').val().split('.').pop().toLowerCase();

																			if ($.inArray(ext, ['jpg', 'jpeg', 'png', '']) == -1) {
																				$('#idk_alert_ext').removeClass('hidden');
																				this.value = null;
																			} else {
																				$('#idk_alert_ext').addClass('hidden');
																			}

																			var f = this.files[0];

																			if (f.size > 20388600 || f.fileSize > 20388600) {
																				$('#idk_alert_size').removeClass('hidden');
																				this.value = null;
																			} else {
																				$('#idk_alert_size').addClass('hidden');
																			}

																		})
																	});
																</script>
															</div>
														</div>
													</div>
												</div>
												<div id="idk_alert_size" class="hidden">
													<div class="alert material-alert material-alert_danger">Greška: Fotografiju koju pokuštavate dodati je veća od dozvoljene veličine.</div>
												</div>
												<div id="idk_alert_ext" class="hidden">
													<div class="alert material-alert material-alert_danger">Greška: Format fotografije koju pokušavate dodati nije dozvoljen.</div>
												</div>
											</div>
										</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					<?php
					} else {
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
					if ($getUserStatus  == 1 or $getUserStatus  == 2) {

						$productcat_id = $_GET['id'];

						//Set language
						if (isset($_GET['lang'])) {
							$lang_id = $_GET['lang'];
						} else {
							$query_lang = $db->prepare("
											SELECT lang_id
											FROM idk_langs
											WHERE lang_default = :lang_default");

							$query_lang->execute(array(
								':lang_default' => 1
							));

							$row_lang = $query_lang->fetch();

							$lang_id = $row_lang['lang_id'];
						}

						//Edit content
						$query = $db->prepare("
										SELECT productcat_sub, productcat_status, productcat_lang_name, productcat_lang_img
										FROM idk_productcat
										INNER JOIN idk_productcat_lang ON idk_productcat.productcat_id = idk_productcat_lang.productcat_lang_productid
										WHERE productcat_id = :productcat_id AND productcat_lang_langid = :productcat_lang_langid");

						$query->execute(array(
							':productcat_id' => $productcat_id,
							':productcat_lang_langid' => $lang_id
						));

						$row = $query->fetch();

						$productcat_sub = $row['productcat_sub'];
						$productcat_status = $row['productcat_status'];
						$productcat_lang_name = $row['productcat_lang_name'];

						if ($row['productcat_lang_img'] == NULL) {
							$productcat_lang_img = "none.jpg";
							$productcat_lang_img_input = NULL;
						} else {
							$productcat_lang_img = $row['productcat_lang_img'];
							$productcat_lang_img_input = $row['productcat_lang_img'];
						}

					?>
						<div class="row">
							<div class="col-xs-8">
								<h1><i class="fas fa-tasks idk_color_green" aria-hidden="true"></i> Uredi kategoriju za proizvod</h1>
							</div>
							<div class="col-xs-4 text-right idk_margin_top10">
								<a href="<?php getSiteURL(); ?>product-category?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
											<form id="idk_form" action="<?php getSiteURL(); ?>do?form=edit_productcat" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
												<input type="hidden" name="productcat_id" value="<?php echo $productcat_id; ?>" />
												<input type="hidden" name="productcat_lang_langid" value="<?php echo $lang_id; ?>" />
												<div class="form-group">
													<div class="col-sm-12">
														<input class="form-control idk_form_control_large" type="text" name="productcat_lang_name" value="<?php echo $productcat_lang_name; ?>" id="productcat_lang_name" placeholder="Naziv kategorije" required>
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
												<div class="form-group row">
													<div class="col-xs-6">
														<?php
														$query_lang_name = $db->prepare("
																				SELECT lang_language
																				FROM idk_langs
																				WHERE lang_id = :lang_id");

														$query_lang_name->execute(array(
															':lang_id' => $lang_id
														));

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

														while ($lang_row = $lang_query->fetch()) {

															$lang_id = $lang_row['lang_id'];
															$lang_code = $lang_row['lang_code'];

															echo '<li class="list-inline-item"><a href="' . getSiteUrlr() . 'product-category?page=edit&id=' . $productcat_id . '&lang=' . $lang_id . '">' . $lang_code . '</a></li>';
														}

														echo '</ul>';
														?>
													</div>
												</div>
												<br>
												<div class="form-group">
													<label for="productcat_status" class="control-label">Status:</label>
													<select class="form-control" id="productcat_status" name="productcat_status">
														<option value="1" <?php if ($productcat_status == "1") {
																								echo "selected";
																							} ?>>Objavljen</option>
														<option value="2" <?php if ($productcat_status == "2") {
																								echo "selected";
																							} ?>>U izradi</option>
														<option value="3" <?php if ($productcat_status == "3") {
																								echo "selected";
																							} ?>>Na čekanju</option>
														<option value="0" <?php if ($productcat_status == "0") {
																								echo "selected";
																							} ?>>Arhiviran</option>
													</select>
												</div>
												<br>
												<div class="form-group">
													<label for="productcat_sub" class="control-label">Pripada kategoriji:</label>
													<select class="form-control" id="productcat_sub" name="productcat_sub">
														<option value="0">Samostalna</option>
														<?php
														$query_cat = $db->prepare("
                                                                    SELECT productcat_id, productcat_lang_name
                                                                    FROM idk_productcat
                                                                    INNER JOIN idk_productcat_lang ON idk_productcat.productcat_id = idk_productcat_lang.productcat_lang_productid
                                                                    WHERE productcat_lang_langid = :productcat_lang_langid AND productcat_sub = :productcat_sub
                                                                    GROUP BY productcat_id");

														$query_cat->execute(array(
															':productcat_lang_langid' => 1,
															':productcat_sub' => 0
														));

														while ($row_cat = $query_cat->fetch()) {

															$productcat_id = $row_cat['productcat_id'];
															$productcat_lang_name = $row_cat['productcat_lang_name'];

															if ($productcat_sub == $productcat_id) {
																$productcat_selected = "selected";
															} else {
																$productcat_selected = "";
															}
														?>
															<option value="<?php echo $productcat_id; ?>" <?php echo $productcat_selected; ?>><?php echo $productcat_lang_name; ?></option>
														<?php } ?>
													</select>
												</div>
												<br>
												<div class="form-group">
													<label for="productcat_lang_img" class="control-label">Fotografija:</label><br>
													<div class="fileinput fileinput-new" data-provides="fileinput">
														<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;">
															<img src="<?php getSiteUrlFront(); ?>files/product-cat/thumbs/<?php echo $productcat_lang_img; ?>">
														</div>
														<input type="hidden" name="productcat_lang_img_input" value="<?php echo $productcat_lang_img_input; ?>" />
														<div>
															<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografiju</span><span class="fileinput-exists">Promijeni</span><input type="file" name="productcat_lang_img" id="productcat_lang_img"></span>
															<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
															<script>
																$(function() {
																	$('#productcat_lang_img').change(function() {

																		var ext = $('#productcat_lang_img').val().split('.').pop().toLowerCase();

																		if ($.inArray(ext, ['jpg', 'jpeg', 'png', '']) == -1) {
																			$('#idk_alert_ext').removeClass('hidden');
																			this.value = null;
																		} else {
																			$('#idk_alert_ext').addClass('hidden');
																		}

																		var f = this.files[0];

																		if (f.size > 20388600 || f.fileSize > 20388600) {
																			$('#idk_alert_size').removeClass('hidden');
																			this.value = null;
																		} else {
																			$('#idk_alert_size').addClass('hidden');
																		}

																	})
																});
															</script>
														</div>
													</div>
												</div>
												<div id="idk_alert_size" class="hidden">
													<div class="alert material-alert material-alert_danger">Greška: Fotografiju koju pokuštavate dodati je veća od dozvoljene veličine.</div>
												</div>
												<div id="idk_alert_ext" class="hidden">
													<div class="alert material-alert material-alert_danger">Greška: Format fotografije koju pokušavate dodati nije dozvoljen.</div>
												</div>
												<br>
											</div>
										</div>
										</form>
									</div>
								</div>
							</div>
						</div>
			<?php
					} else {
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
					if ($getUserStatus  == 1) {

						$productcat_id = $_GET['id'];

						//Get
						$query_select = $db->prepare("
												SELECT productcat_lang_name
												FROM idk_productcat_lang
												WHERE productcat_lang_productid = :productcat_lang_productid");

						$query_select->execute(array(
							':productcat_lang_productid' => $productcat_id
						));

						$row_select = $query_select->fetch();

						$productcat_lang_name = $row_select['productcat_lang_name'];

						//Save
						$query = $db->prepare("
										UPDATE idk_productcat
										SET productcat_status = :productcat_status
										WHERE productcat_id = :productcat_id");

						$query->execute(array(
							':productcat_status' => 0,
							':productcat_id' => $productcat_id
						));

						//Add to LOGS
						$log_desc = "Arhivirao kategoriju za proizvode: " . $productcat_lang_name . "";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_userid, log_desc, log_date)
										VALUES
											(:log_userid, :log_desc, :log_date)");

						$log_query->execute(array(
							':log_userid' => $logged_user_id,
							':log_desc' => $log_desc,
							':log_date' => $log_date
						));


						header("Location: " . getSiteURLr() . "product-category?page=list&mess=3");
					} else {
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