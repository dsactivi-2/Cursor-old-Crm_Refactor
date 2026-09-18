<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getUserStatus = getUserStatus();

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: faq?page=list");
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
					<h1><i class="far fa-question-circle idk_color_green"></i> Njačešća pitanja</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>faq?page=add" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></a>
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
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novo najčešče pitanje.</div>';
									}elseif($mess == 2){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili najčešće pitanje.</div>';
									}elseif($mess == 3){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali najčešće pitanje.</div>';
									}
								?>
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_table').DataTable({

											responsive: true,

											"order": [[ 1, "asc" ]],

											 "bAutoWidth": false,

											"aoColumns": [
													{ "width": "5%" },
													{ "width": "65%" },
													{ "width": "20%", "bSortable": false },
													{ "width": "10%", "bSortable": false }
												]
										});
									} );
								</script>
								<table id="idk_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th class="text-center">ID</th>
											<th>Pitanje</th>
											<th>Jezici</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
															SELECT faq_id, faq_lang_question
															FROM idk_faq
															LEFT JOIN idk_faq_lang ON idk_faq.faq_id = idk_faq_lang.faq_lang_faqid
                                                            WHERE faq_status != :faq_status
															GROUP BY faq_id");

											$query->execute(array(
                                                    ':faq_status' => 0));

											while($row = $query->fetch()){

												$faq_id = $row['faq_id'];
												$faq_lang_question = $row['faq_lang_question'];
										?>
										<tr>
											<td class="text-center"><?php echo $faq_id; ?></td>
											<td><?php echo $faq_lang_question; ?></td>
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

														echo '<li class="list-inline-item"><a href="' . getSiteUrlr() . 'faq?page=edit&id=' . $faq_id . '&lang=' . $lang_id . '">' . $lang_code . '</a></li>';

													}

													echo '</ul>';
												?>
											</td>
											<td class="text-center">
												<div class="btn-group material-btn-group">
													<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
													<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
														<li><a href="<?php getSiteURL(); ?>faq?page=edit&id=<?php echo $faq_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi</a></li>
														<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>faq?page=archive&id=<?php echo $faq_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
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
														<p>Jeste li sigurni da želite arhivirati najčešće pitanje?</p>
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
					<h1><i class="far fa-question-circle idk_color_green"></i> Dodaj pitanje i odgovor</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>faq?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
								<form id="idk_form" action="<?php getSiteURL(); ?>do?form=add_faq" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<div class="form-group">
										<div class="col-sm-12">
                                            <label for="faq_lang_question">Pitanje:</label>
											<input class="form-control idk_form_control_large" type="text" name="faq_lang_question" id="faq_lang_question" required>
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-12">
                                            <label for="faq_lang_answer">Odgovor:</label>
											<textarea class="form-control materail-input material-textarea" name="faq_lang_answer" id="faq_lang_answer" rows="8" required></textarea>
                                            <script>
                                                $('#faq_lang_answer').trumbowyg({
                                                    lang: 'hr',
                                                    btnsDef: {
                                                        align: {
                                                            dropdown: ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                                                            ico: 'justifyLeft'
                                                        },
                                                        txt: {
                                                            dropdown: ['removeformat', 'superscript', 'subscript'],
                                                            ico: 'removeformat'
                                                        },
                                                    },
                                                    btns: [
                                                        ['historyUndo','historyRedo'],
                                                        ['strong', 'em', 'del', 'align', 'unorderedList', 'orderedList', 'formatting', 'fontsize', 'foreColor', 'backColor', 'horizontalRule'],
                                                        ['link', 'table', 'insertImage','noembed', 'txt'],
                                                        ['viewHTML'],
                                                        ['fullscreen']
                                                    ],
                                                    plugins: {
                                                       resizimg: {
                                                           minSize: 64,
                                                           step: 16,
                                                       }
                                                    }
                                                });
                                            </script>
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

						$faq_id = $_GET['id'];

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

						//Edit content
						$query = $db->prepare("
										SELECT faq_lang_question, faq_lang_answer
										FROM idk_faq
										INNER JOIN idk_faq_lang ON idk_faq.faq_id = idk_faq_lang.faq_lang_faqid
										WHERE faq_id = :faq_id AND faq_lang_langid = :faq_lang_langid");

						$query->execute(array(
									':faq_id' => $faq_id,
									':faq_lang_langid' => $lang_id));

						$row = $query->fetch();

							$faq_lang_question = $row['faq_lang_question'];
							$faq_lang_answer = $row['faq_lang_answer'];

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="far fa-question-circle idk_color_green"></i> Uredi pitanje</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>faq?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
								<form id="idk_form" action="<?php getSiteURL(); ?>do?form=edit_faq" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="faq_id" value="<?php echo $faq_id; ?>" />
									<input type="hidden" name="faq_lang_langid" value="<?php echo $lang_id; ?>" />
                                    <div class="form-group">
										<div class="col-sm-12">
                                            <label for="faq_lang_question">Pitanje:</label>
											<input class="form-control idk_form_control_large" type="text" name="faq_lang_question" id="faq_lang_question" value="<?php echo $faq_lang_question; ?>" required>
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-12">
                                            <label for="faq_lang_answer">Odgovor:</label>
											<textarea class="form-control materail-input material-textarea" name="faq_lang_answer" id="faq_lang_answer" rows="8" required><?php echo $faq_lang_answer; ?></textarea>
                                            <script>
                                                $('#faq_lang_answer').trumbowyg({
                                                    lang: 'hr',
                                                    btnsDef: {
                                                        align: {
                                                            dropdown: ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                                                            ico: 'justifyLeft'
                                                        },
                                                        txt: {
                                                            dropdown: ['removeformat', 'superscript', 'subscript'],
                                                            ico: 'removeformat'
                                                        },
                                                    },
                                                    btns: [
                                                        ['historyUndo','historyRedo'],
                                                        ['strong', 'em', 'del', 'align', 'unorderedList', 'orderedList', 'formatting', 'fontsize', 'foreColor', 'backColor', 'horizontalRule'],
                                                        ['link', 'table', 'insertImage','noembed', 'txt'],
                                                        ['viewHTML'],
                                                        ['fullscreen']
                                                    ],
                                                    plugins: {
                                                       resizimg: {
                                                           minSize: 64,
                                                           step: 16,
                                                       }
                                                    }
                                                });
                                            </script>
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

														echo '<li class="list-inline-item"><a href="' . getSiteUrlr() . 'faq?page=edit&id=' . $faq_id . '&lang=' . $lang_id . '">' . $lang_code . '</a></li>';

													}

													echo '</ul>';
												?>
                                            </div>
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

						$faq_id = $_GET['id'];

						//Get
						$query_select = $db->prepare("
												SELECT faq_lang_question
												FROM idk_faq_lang
												WHERE faq_lang_faqid = :faq_lang_faqid");

						$query_select->execute(array(
											':faq_lang_faqid' => $faq_id));

						$row_select = $query_select->fetch();

						$faq_lang_question = $row_select['faq_lang_question'];

						//Save
						$query = $db->prepare("
										UPDATE idk_faq
										SET faq_status = :faq_status
										WHERE faq_id = :faq_id");

						$query->execute(array(
									':faq_status' => 0,
									':faq_id' => $faq_id));

						//Add to LOGS
						$log_desc = "Arhivirao najčešće pitanje: " . $faq_lang_question . "";
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


						header("Location: " . getSiteURLr() . "faq?page=list&mess=3");

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
