<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getUserStatus = getUserStatus();

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: posts?page=list");
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
					<h1><i class="far fa-newspaper idk_color_green"></i> Novosti</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>posts?page=add" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></a>
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
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novi post.</div>';
									}elseif($mess == 2){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili post.</div>';
									}elseif($mess == 3){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali post.</div>';
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
													{ "width": "25%" },
													{ "width": "15%" },
													{ "width": "15%" },
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
											<th>Naslov</th>
											<th>Kategorija</th>
											<th class="text-center">Posjete</th>
											<th class="text-center">Jezici</th>
											<th class="text-center">Status</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
															SELECT post_id, post_lang_title, postcat_lang_name, post_count, post_lang_url, post_status
															FROM idk_posts
															LEFT JOIN idk_posts_lang ON idk_posts.post_id = idk_posts_lang.post_lang_postid
															LEFT JOIN idk_postcat ON idk_posts.post_catid = idk_postcat.postcat_id
															LEFT JOIN idk_postcat_lang ON idk_postcat.postcat_id = idk_postcat_lang.postcat_lang_postcatid
															WHERE post_status != :post_status
															GROUP BY post_id");

											$query->execute(array(':post_status' => 0));

											while($row = $query->fetch()){

												$post_id = $row['post_id'];

												$post_status = $row['post_status'];
												if($row['post_status'] == 1){
													$post_status = "Objavljen";
												}elseif($row['post_status'] == 2){
													$post_status = "U izradi";
												}elseif($row['post_status'] == 3){
													$post_status = "Na čekanju";
												}elseif($row['post_status'] == 0){
													$post_status = "Arhiviran";
												}

												$post_lang_title = $row['post_lang_title'];
												$postcat_lang_name = $row['postcat_lang_name'];
												$post_count = $row['post_count'];
												$post_lang_url = $row['post_lang_url'];
										?>
										<tr>
											<td class="text-center"><?php echo $post_id; ?></td>
											<td><a href="<?php getSiteUrlFront(); ?><?php echo $post_lang_url; ?>" target="_blank"><?php echo $post_lang_title; ?></a></td>
											<td><?php echo $postcat_lang_name; ?></td>
											<td class="text-center"><?php echo $post_count; ?></td>
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

														echo '<li class="list-inline-item"><a href="' . getSiteUrlr() . 'posts?page=edit&id=' . $post_id . '&lang=' . $lang_id . '">' . $lang_code . '</a></li>';

													}

													echo '</ul>';
												?>
											</td>
											<td class="text-center"><?php echo $post_status; ?></td>
											<td class="text-center">
												<div class="btn-group material-btn-group">
													<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
													<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
														<li><a href="<?php getSiteUrlFront(); ?><?php echo $post_lang_url; ?>" target="_blank" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
														<li><a href="<?php getSiteURL(); ?>posts?page=edit&id=<?php echo $post_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi</a></li>
														<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>posts?page=archive&id=<?php echo $post_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
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
														<p>Jeste li sigurni da želite arhivirati članak?</p>
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
					<h1><i class="far fa-newspaper idk_color_green"></i> Dodaj novi članak</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>posts?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
								<form id="idk_form" action="<?php getSiteURL(); ?>do?form=add_post" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<div class="form-group">
										<div class="col-sm-12">
											<input class="form-control idk_form_control_large" type="text" name="post_lang_title" id="post_lang_title" placeholder="Naslov" required>
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-12">
											<textarea class="form-control materail-input material-textarea" name="post_lang_content" id="post_lang_content" placeholder="Članak ..." rows="8"></textarea>
                                            <script>
                                                $('#post_lang_content').trumbowyg({
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
                                    <div class="material-accordion">
                            			<div class="panel panel-default material-accordion__panel">
                            				<div class="panel-heading material-accordion__heading" id="acc_headingTwo">
                            					<h4 class="panel-title">
                            						<a class="collapsed material-accordion__title" data-toggle="collapse" data-parent="#accordion" href="#acc_collapseTwo">SEO postavke</a>
                            					</h4>
                            				</div>
                            				<div id="acc_collapseTwo" class="panel-collapse collapse material-accordion__collapse">
                            					<div class="panel-body">
                                                    <div class="form-group row">
                										<label for="post_seo_btn" class="col-sm-3 control-label">Ručno podešavanje:</label>
                										<div class="col-sm-9">
                											<div class="main-container__column materail-switch materail-switch_success">
                												<input class="materail-switch__element" name="post_seo" value="1" type="checkbox" id="post_seo_btn">
                												<label class="materail-switch__label" for="post_seo_btn"></label>
                											</div>
                										</div>
                									</div>
                									<script>
                										jQuery(document).ready(function($) {
                											//reset
                											$("#post_seo_btn").prop("checked", false);
                											$("#post_seo_btn").click(function () {

                												if ($("#post_seo_btn").is(":checked")) {

                													//checked
                													$("#post_seo").removeClass("hidden").fadeOut(0).fadeIn(1000);

                												} else {
                													//unchecked
                													$("#post_seo").addClass("hidden");
                												}

                											})

                										});
                									</script>
                									<div class="hidden" id="post_seo">
                                                        <div class="form-group">
                                                            <label for="post_lang_seoname" class="col-sm-3 control-label">SEO naslov:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" type="text" name="post_lang_seoname" id="post_lang_seoname" placeholder="SEO naslov">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="post_lang_url" class="col-sm-3 control-label">Slug (URL):</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" type="text" name="post_lang_url" id="post_lang_url" placeholder="moj-clanak-slug">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="post_lang_seodesc" class="col-sm-3 control-label">SEO opis:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" type="text" name="post_lang_seodesc" id="post_lang_seodesc" placeholder="SEO opis">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="post_lang_seokeywords" class="col-sm-3 control-label">SEO ključne riječi:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" type="text" name="post_lang_seokeywords" id="post_lang_seokeywords" placeholder="riječ, riječ, riječ">
                                                            </div>
                                                        </div>
                                                        <br>
                									</div>
                            					</div>
                            				</div>
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
                                            <label for="post_status" class="control-label">Status:</label>
                                            <select class="form-control" id="post_status" name="post_status">
                                                <option value="1">Objavljen</option>
                                                <option value="2">U izradi</option>
                                                <option value="3">Na čekanju</option>
                                                <option value="0">Arhiviran</option>
                                            </select>
                                        </div>
										<br>
										<div class="form-group">
                                            <label for="post_author" class="control-label">Autor:</label>
                                            <input class="form-control" type="text" name="post_author" id="post_author" value="<?php getUserFullname(); ?>">
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label for="post_catid" class="control-label">Kategorija:</label>
                                            <select class="form-control" id="post_catid" name="post_catid">
                                                <?php
                        							$sub_query = $db->prepare("
                        												SELECT postcat_id, postcat_lang_name
                        												FROM idk_postcat
                                                                        LEFT JOIN idk_postcat_lang ON idk_postcat.postcat_id = idk_postcat_lang.postcat_lang_postcatid
                        												WHERE postcat_status != :postcat_status
																		GROUP BY postcat_id
                        												ORDER BY postcat_lang_name ASC");

                        							$sub_query->execute(array(
                        											':postcat_status' => 0));

                        							while($sub = $sub_query->fetch()) {

                        								echo "<option value='" . $sub['postcat_id'] . "'>" . $sub['postcat_lang_name'] . "</option>";

                        							}
                        						?>
                                            </select>
                                        </div>
                                        <br>
                                        <div class="form-group">
    										<div class="">
                                                <label for="post_lang_img" class="control-label">Fotografija:</label><br>
    											<div class="fileinput fileinput-new" data-provides="fileinput">
    												<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;"></div>
    												<div>
    													<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografiju</span><span class="fileinput-exists">Promijeni</span><input type="file" name="post_lang_img" id="post_lang_img"></span>
    													<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
    													<script>
    														$(function (){
    															$('#post_lang_img').change(function (){

    																var ext = $('#post_lang_img').val().split('.').pop().toLowerCase();

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
                                        <br>
                                        <div class="form-group row">
    										<label for="post_featured" class="col-sm-5 control-label">Izdvojeno:</label>
    										<div class="col-sm-7">
    											<div class="main-container__column materail-switch materail-switch_success">
    												<input class="materail-switch__element" name="post_featured" value="1" type="checkbox" id="post_featured">
    												<label class="materail-switch__label" for="post_featured"></label>
    											</div>
    										</div>
    									</div>
                                        <br>
                                        <div class="form-group row">
    										<label for="post_comment" class="col-sm-5 control-label">Komentari:</label>
    										<div class="col-sm-7">
    											<div class="main-container__column materail-switch materail-switch_success">
    												<input class="materail-switch__element" name="post_comment" value="1" type="checkbox" id="post_comment">
    												<label class="materail-switch__label" for="post_comment"></label>
    											</div>
    										</div>
    									</div>
                                        <br>
                                        <div class="form-group row">
    										<label for="post_share" class="col-sm-5 control-label">Društvene mreže:</label>
    										<div class="col-sm-7">
    											<div class="main-container__column materail-switch materail-switch_success">
    												<input class="materail-switch__element" name="post_share" value="1" type="checkbox" id="post_share" checked>
    												<label class="materail-switch__label" for="post_share"></label>
    											</div>
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

				case "edit":
					if($getUserStatus  == 1 OR $getUserStatus  == 2){

						$post_id = $_GET['id'];

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
										SELECT post_catid, post_author, post_featured, post_comment, post_share, post_status,
												post_lang_title, post_lang_url, post_lang_content, post_lang_img, post_lang_seoname, post_lang_seodesc, post_lang_seokeywords
										FROM idk_posts
										INNER JOIN idk_posts_lang ON idk_posts.post_id = idk_posts_lang.post_lang_postid
										WHERE post_id = :post_id AND post_lang_langid = :post_lang_langid");

						$query->execute(array(
									':post_id' => $post_id,
									':post_lang_langid' => $lang_id));

						$row = $query->fetch();

							$post_lang_title = $row['post_lang_title'];
							$post_lang_url = $row['post_lang_url'];
							$post_lang_url_string = get_string_between($row['post_lang_url'], '/', '/');
							$post_lang_content = $row['post_lang_content'];
							$post_lang_seoname = $row['post_lang_seoname'];
							$post_lang_seodesc = $row['post_lang_seodesc'];
							$post_lang_seokeywords = $row['post_lang_seokeywords'];

							$post_catid = $row['post_catid'];
							$post_author = $row['post_author'];
							$post_featured = $row['post_featured'];
							$post_comment = $row['post_comment'];
							$post_share = $row['post_share'];
							$post_status = $row['post_status'];

							if($row['post_lang_img'] == NULL){
								$post_lang_img = "none.jpg";
								$post_lang_img_input = NULL;
							}else{
								$post_lang_img = $row['post_lang_img'];
								$post_lang_img_input = $row['post_lang_img'];
							}

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="far fa-newspaper idk_color_green" aria-hidden="true"></i> Uredi članak</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>posts?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
								<form id="idk_form" action="<?php getSiteURL(); ?>do?form=edit_post" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="post_id" value="<?php echo $post_id; ?>" />
									<input type="hidden" name="post_lang_langid" value="<?php echo $lang_id; ?>" />
									<div class="form-group">
										<div class="col-sm-12">
											<input class="form-control idk_form_control_large" type="text" name="post_lang_title" value="<?php echo $post_lang_title; ?>" id="post_lang_title" required>
											<br>
											<p>URL: <a href="<?php getSiteUrlFront(); ?><?php echo $post_lang_url; ?>" target="_blank"><?php getSiteUrlFront(); ?><?php echo $post_lang_url; ?></a></p>
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-12">
											<textarea class="form-control materail-input material-textarea" name="post_lang_content" id="post_lang_content" rows="8"><?php echo $post_lang_content; ?></textarea>
                                            <script>
                                                $('#post_lang_content').trumbowyg({
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
                                    <div class="material-accordion">
                            			<div class="panel panel-default material-accordion__panel">
                            				<div class="panel-heading material-accordion__heading" id="acc_headingTwo">
                            					<h4 class="panel-title">
                            						<a class="collapsed material-accordion__title" data-toggle="collapse" data-parent="#accordion" href="#acc_collapseTwo">SEO postavke</a>
                            					</h4>
                            				</div>
											<div id="acc_collapseTwo" class="panel-collapse collapse material-accordion__collapse">
                            					<div class="panel-body">
                                                    <div class="form-group row">
                										<label for="post_seo_btn" class="col-sm-3 control-label">Ručno podešavanje:</label>
                										<div class="col-sm-9">
                											<div class="main-container__column materail-switch materail-switch_success">
                												<input class="materail-switch__element" name="post_seo" value="1" type="checkbox" id="post_seo_btn">
                												<label class="materail-switch__label" for="post_seo_btn"></label>
                											</div>
                										</div>
                									</div>
                									<script>
                										jQuery(document).ready(function($) {
                											//reset
                											$("#post_seo_btn").prop("checked", false);
                											$("#post_seo_btn").click(function () {

                												if ($("#post_seo_btn").is(":checked")) {

                													//checked
                													$("#post_seo").removeClass("hidden").fadeOut(0).fadeIn(1000);

                												} else {
                													//unchecked
                													$("#post_seo").addClass("hidden");
                												}

                											})

                										});
                									</script>
                									<div class="hidden" id="post_seo">
                                                        <div class="form-group">
                                                            <label for="post_lang_seoname" class="col-sm-3 control-label">SEO naslov:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" type="text" name="post_lang_seoname" value="<?php echo $post_lang_seoname; ?>" id="post_lang_seoname">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="post_lang_url" class="col-sm-3 control-label">Slug (URL):</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" type="text" name="post_lang_url" value="<?php echo $post_lang_url_string; ?>" id="post_lang_url">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="post_lang_seodesc" class="col-sm-3 control-label">SEO opis:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" type="text" name="post_lang_seodesc" value="<?php echo $post_lang_seodesc; ?>" id="post_lang_seodesc">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="post_lang_seokeywords" class="col-sm-3 control-label">SEO ključne riječi:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" type="text" name="post_lang_seokeywords" value="<?php echo $post_lang_seokeywords; ?>" id="post_lang_seokeywords">
                                                            </div>
                                                        </div>
                                                        <br>
                									</div>
                            					</div>
                            				</div>
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
                                            <label for="post_status" class="control-label">Status:</label>
                                            <select class="form-control" id="post_status" name="post_status">
                                                <option value="1" <?php if($post_status == "1"){ echo "selected"; } ?>>Objavljen</option>
                                                <option value="2" <?php if($post_status == "2"){ echo "selected"; } ?>>U izradi</option>
                                                <option value="3" <?php if($post_status == "3"){ echo "selected"; } ?>>Na čekanju</option>
                                                <option value="0" <?php if($post_status == "0"){ echo "selected"; } ?>>Arhiviran</option>
                                            </select>
                                        </div>
                                        <br>
										<div class="form-group">
                                            <label for="post_author" class="control-label">Autor:</label>
                                            <input class="form-control" type="text" name="post_author" id="post_author" value="<?php echo $post_author; ?>">
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label for="post_catid" class="control-label">Kategorija:</label>
                                            <select class="form-control" id="post_catid" name="post_catid">
                                                <?php
                        							$sub_query = $db->prepare("
                        												SELECT postcat_id, postcat_lang_name
                        												FROM idk_postcat
                                                                        LEFT JOIN idk_postcat_lang ON idk_postcat.postcat_id = idk_postcat_lang.postcat_lang_postcatid
                        												WHERE postcat_status != :postcat_status
																		GROUP BY postcat_id
                        												ORDER BY postcat_lang_name ASC");

                        							$sub_query->execute(array(
                        											':postcat_status' => 0));

                        							while($sub = $sub_query->fetch()) {

														if($sub['postcat_id'] == $post_catid){
															$selected = "selected";
														}else{
															$selected = "";
														}

                        								echo "<option value='" . $sub['postcat_id'] . "' ' . $selected . '>" . $sub['postcat_lang_name'] . "</option>";

                        							}
                        						?>
                                            </select>
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label for="post_lang_img" class="control-label">Fotografija:</label><br>
											<div class="fileinput fileinput-new" data-provides="fileinput">
												<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;">
													<img src="<?php getSiteUrlFront(); ?>files/posts/thumbs/<?php echo $post_lang_img; ?>">
												</div>
												<input type="hidden" name="post_lang_img_input" value="<?php echo $post_lang_img_input; ?>" />
												<div>
													<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografiju</span><span class="fileinput-exists">Promijeni</span><input type="file" name="post_lang_img" id="post_lang_img"></span>
													<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
													<script>
														$(function (){
															$('#post_lang_img').change(function (){

																var ext = $('#post_lang_img').val().split('.').pop().toLowerCase();

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
                                        <br>
                                        <div class="form-group row">
    										<label for="post_featured" class="col-sm-5 control-label">Izdvojeno:</label>
    										<div class="col-sm-7">
    											<div class="main-container__column materail-switch materail-switch_success">
    												<input class="materail-switch__element" name="post_featured" value="1" type="checkbox" id="post_featured" <?php if($post_featured == NULL){ echo ""; }else{ echo "checked"; } ?>>
    												<label class="materail-switch__label" for="post_featured"></label>
    											</div>
    										</div>
    									</div>
                                        <br>
                                        <div class="form-group row">
    										<label for="post_comment" class="col-sm-5 control-label">Komentari:</label>
    										<div class="col-sm-7">
    											<div class="main-container__column materail-switch materail-switch_success">
    												<input class="materail-switch__element" name="post_comment" value="1" type="checkbox" id="post_comment" <?php if($post_comment == NULL){ echo ""; }else{ echo "checked"; } ?>>
    												<label class="materail-switch__label" for="post_comment"></label>
    											</div>
    										</div>
    									</div>
                                        <br>
                                        <div class="form-group row">
    										<label for="post_share" class="col-sm-5 control-label">Društvene mreže:</label>
    										<div class="col-sm-7">
    											<div class="main-container__column materail-switch materail-switch_success">
    												<input class="materail-switch__element" name="post_share" value="1" type="checkbox" id="post_share" <?php if($post_share == NULL){ echo ""; }else{ echo "checked"; } ?>>
    												<label class="materail-switch__label" for="post_share"></label>
    											</div>
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

				case "archive":
					if($getUserStatus  == 1){

						$post_id = $_GET['id'];

						//Get
						$query_select = $db->prepare("
												SELECT post_lang_title
												FROM idk_posts_lang
												WHERE post_lang_postid = :post_lang_postid");

						$query_select->execute(array(
											':post_lang_postid' => $post_id));

						$row_select = $query_select->fetch();

						$post_lang_title = $row_select['post_lang_title'];

						//Save
						$query = $db->prepare("
										UPDATE idk_posts
										SET post_status = :post_status
										WHERE post_id = :post_id");

						$query->execute(array(
									':post_status' => 0,
									':post_id' => $post_id));

						//Add to LOGS
						$log_desc = "Arhivirao članak: " . $post_lang_title . "";
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


						header("Location: " . getSiteURLr() . "posts?page=list&mess=3");

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
