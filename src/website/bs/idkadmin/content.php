<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getUserStatus = getUserStatus();

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: content?page=list");
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
					<h1><i class="far fa-copy idk_color_green"></i> Sadržaj</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>content?page=add" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></a>
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
										echo '<div class="alert material-alert material-alert_success">Uspješno ste kreirali novi sadržaj.</div>';
									}elseif($mess == 2){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili sadržaj.</div>';
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
											<th>Naziv</th>
											<th>Pripada stranici</th>
											<th class="text-center">Posjete</th>
											<th class="text-center">Jezici</th>
											<th class="text-center">Status</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
															SELECT content_id, content_sub, content_status, content_count, content_lang_name, content_lang_url
															FROM idk_content
															LEFT JOIN idk_content_lang ON idk_content.content_id = idk_content_lang.content_lang_postid
															WHERE content_status != :content_status AND content_lang_langid = 1
															GROUP BY content_id");

											$query->execute(array(':content_status' => 0));

											while($row = $query->fetch()){

												$content_id = $row['content_id'];

												if($row['content_sub'] == 0){
													$content_sub = "Samostalna";
												}else{

													$content_sub = $row['content_sub'];

													$sub_query = $db->prepare("
																		SELECT content_lang_name
																		FROM idk_content
																		LEFT JOIN idk_content_lang ON idk_content.content_id = idk_content_lang.content_lang_postid
																		WHERE content_id = :content_id");

													$sub_query->execute(array(
																		':content_id' => $content_sub));

													$sub = $sub_query->fetch();

													$content_sub = $sub['content_lang_name'];
												}

												$content_status = $row['content_status'];
												if($row['content_status'] == 1){
													$content_status = "Objavljen";
												}elseif($row['content_status'] == 2){
													$content_status = "U izradi";
												}elseif($row['content_status'] == 3){
													$content_status = "Na čekanju";
												}elseif($row['content_status'] == 0){
													$content_status = "Arhiviran";
												}

												$content_count = $row['content_count'];
												$content_lang_name = $row['content_lang_name'];
												$content_lang_url = $row['content_lang_url'];
										?>
										<tr>
											<td class="text-center"><?php echo $content_id; ?></td>
											<td><a href="<?php getSiteUrlFront(); ?><?php echo $content_lang_url; ?>" target="_blank"><?php echo $content_lang_name; ?></a></td>
											<td><?php echo $content_sub; ?></td>
											<td class="text-center"><?php echo $content_count; ?></td>
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

														echo '<li class="list-inline-item"><a href="' . getSiteUrlr() . 'content?page=edit&id=' . $content_id . '&lang=' . $lang_id . '">' . $lang_code . '</a></li>';

													}

													echo '</ul>';
												?>
											</td>
											<td class="text-center"><?php echo $content_status; ?></td>
											<td class="text-center">
												<div class="btn-group material-btn-group">
													<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
													<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
														<li><a href="<?php getSiteUrlFront(); ?><?php echo $content_lang_url; ?>" target="_blank" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
														<li><a href="<?php getSiteURL(); ?>content?page=edit&id=<?php echo $content_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi</a></li>
														<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>content?page=archive&id=<?php echo $content_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
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
														<p>Jeste li sigurni da želite arhivirati sadržaj?</p>
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
					<h1><i class="far fa-copy idk_color_green"></i> Dodaj novi sadržaj</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>content?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
								<form id="idk_form" action="<?php getSiteURL(); ?>do?form=add_content" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<div class="form-group">
										<div class="col-sm-12">
											<input class="form-control idk_form_control_large" type="text" name="content_lang_name" id="content_lang_name" placeholder="Naziv" required>
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-12">
											<textarea class="form-control materail-input material-textarea" name="content_lang_content" id="content_lang_content" placeholder="Sadržaj ..." rows="8"></textarea>
                                            <script>
                                                $('#content_lang_content').trumbowyg({
                                                    lang: 'hr',
													semantic: {
												        'div': 'div' // Editor does nothing on div tags now
												    },
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
                            				<div class="panel-heading material-accordion__heading" id="acc_headingOne">
                            					<h4 class="panel-title">
                            						<a data-toggle="collapse" data-parent="#accordion" href="#acc_collapseOne" class="collapsed material-accordion__title">Navigacija</a>
                            					</h4>
                            				</div>
                            				<div id="acc_collapseOne" class="panel-collapse collapse material-accordion__collapse">
                            					<div class="panel-body">
                                                    <div class="form-group row">
                										<label for="content_nav" class="col-sm-3 control-label">Dodaj u navigaciju:</label>
                										<div class="col-sm-9">
                											<div class="main-container__column materail-switch materail-switch_success">
                												<input class="materail-switch__element" name="content_nav" value="on" type="checkbox" id="idk_menu_form_btn">
                												<label class="materail-switch__label" for="idk_menu_form_btn"></label>
                											</div>
                										</div>
                									</div>
                									<script>
                										jQuery(document).ready(function($) {
                											//reset
                											$("#idk_menu_form_btn").prop("checked", false);
                											$("#idk_menu_form_btn").click(function () {

                												if ($("#idk_menu_form_btn").is(":checked")) {

                													//checked
                													$("#idk_menu_form").removeClass("hidden").fadeOut(0).fadeIn(1000);

                												} else {
                													//unchecked
                													$("#idk_menu_form").addClass("hidden");
                												}

                											})

                										});
                									</script>
                									<div class="hidden" id="idk_menu_form">
                                                        <div class="form-group">
                                                            <label for="nav_lang_name" class="col-sm-3 control-label">Naziv navigacije:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" type="text" name="nav_lang_name" id="nav_lang_name" placeholder="Naziv navigacije">
																<script>
																	$(function() {
																		$("#content_lang_name").keyup(function() {
																			$('#nav_lang_name').val(this.value);
																		});
																	});
																</script>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="nav_sub" class="col-sm-3 control-label">Pripada navigaciji:</label>
                                                            <div class="col-sm-7">
																<select class="form-control" id="nav_sub" name="nav_sub" required>
			                                                        <option value="0">Samostalna</option>
			                                                        <?php
			                                                            $query_nav = $db->prepare("
			                                                                                    SELECT nav_id, nav_lang_name
			                                                                                    FROM idk_navigation
			                                                                                    INNER JOIN idk_navigation_lang ON idk_navigation.nav_id = idk_navigation_lang.nav_lang_navid
			                                                                                    WHERE nav_lang_langid = :nav_lang_langid
			                                                                                    GROUP BY nav_id");

			                                                            $query_nav->execute(array(
			                                                                                    ':nav_lang_langid' => 1));

			                                                            while($row_nav = $query_nav->fetch()){

			                                                                $nav_id = $row_nav['nav_id'];
			                                                                $nav_lang_name = $row_nav['nav_lang_name'];
			                                                        ?>
			                                                            <option value="<?php echo $nav_id; ?>"><?php echo $nav_lang_name; ?></option>
			                                                        <?php } ?>
			                                                    </select>
                                                            </div>
                                                        </div>
														<div class="form-group row">
		            										<label for="nav_target" class="col-sm-3 control-label">Novi prozor:</label>
		            										<div class="col-sm-9">
		            											<div class="main-container__column materail-switch materail-switch_success">
		            												<input class="materail-switch__element" name="nav_target" value="1" type="checkbox" id="nav_target">
		            												<label class="materail-switch__label" for="nav_target"></label>
		            											</div>
		            										</div>
		            									</div>
		                                                <div class="form-group row">
		            										<label for="nav_sort" class="col-sm-3 control-label">Pozicija:</label>
		            										<div class="col-sm-2">
		            											<input class="form-control" type="number" name="nav_sort" id="nav_sort" placeholder="Pozicija" value="0" required>
		            										</div>
		            									</div>
                                                        <br>
                									</div>
                            					</div>
                            				</div>
                            			</div>
										<div class="panel panel-default material-accordion__panel">
                            				<div class="panel-heading material-accordion__heading" id="acc_headingPhoto">
                            					<h4 class="panel-title">
                            						<a data-toggle="collapse" data-parent="#accordion" href="#acc_collapsePhoto" class="collapsed material-accordion__title">Fotogalerija</a>
                            					</h4>
                            				</div>
                            				<div id="acc_collapsePhoto" class="panel-collapse collapse material-accordion__collapse">
                            					<div class="panel-body">
													<div class="fileinput fileinput-new" data-provides="fileinput">
														<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberite fotografije</span><span class="fileinput-exists">Promijenite</span><input name="cphotos_file[]" id="cphotos_file" multiple="" type="file" /></span>
														<span class="fileinput-filenumber"></span>
														<ul class="list-inline" id="result" />

														<script>
															window.onload = function(){
																if(window.File && window.FileList && window.FileReader){
																	var filesInput = document.getElementById("cphotos_file");
																	filesInput.addEventListener("change", function(event){
																		var files = event.target.files;
																		var output = document.getElementById("result");
																		var ul = document.getElementById("result");
																		while (ul.hasChildNodes()) {
																			ul.removeChild(ul.firstChild);
																		}
																		for(var i = 0; i< files.length; i++)
																		{
																			var file = files[i];
																			if(!file.type.match('image'))
																				continue;
																			var picReader = new FileReader();
																			picReader.addEventListener("load",function(event){
																				var picFile = event.target;
																				var li = document.createElement("li");
																				li.innerHTML = "<img class='idk_thumbnail thumbnail' src='" + picFile.result + "' />";
																				output.insertBefore(li,null);
																			});
																			picReader.readAsDataURL(file);
																		}
																	});
																}else{
																	console.log("Your browser does not support File API");
																}
															}
														</script>
													</div>
                            					</div>
                            				</div>
                            			</div>
                            			<div class="panel panel-default material-accordion__panel">
                            				<div class="panel-heading material-accordion__heading" id="acc_headingTwo">
                            					<h4 class="panel-title">
                            						<a class="collapsed material-accordion__title" data-toggle="collapse" data-parent="#accordion" href="#acc_collapseTwo">SEO postavke</a>
                            					</h4>
                            				</div>
                            				<div id="acc_collapseTwo" class="panel-collapse collapse material-accordion__collapse">
                            					<div class="panel-body">
                                                    <div class="form-group row">
                										<label for="content_seo_btn" class="col-sm-3 control-label">Ručno podešavanje:</label>
                										<div class="col-sm-9">
                											<div class="main-container__column materail-switch materail-switch_success">
                												<input class="materail-switch__element" name="content_seo" value="1" type="checkbox" id="content_seo_btn">
                												<label class="materail-switch__label" for="content_seo_btn"></label>
                											</div>
                										</div>
                									</div>
                									<script>
                										jQuery(document).ready(function($) {
                											//reset
                											$("#content_seo_btn").prop("checked", false);
                											$("#content_seo_btn").click(function () {

                												if ($("#content_seo_btn").is(":checked")) {

                													//checked
                													$("#content_seo").removeClass("hidden").fadeOut(0).fadeIn(1000);

                												} else {
                													//unchecked
                													$("#content_seo").addClass("hidden");
                												}

                											})

                										});
                									</script>
                									<div class="hidden" id="content_seo">
                                                        <div class="form-group">
                                                            <label for="content_lang_seoname" class="col-sm-3 control-label">SEO naziv sadržaja:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" type="text" name="content_lang_seoname" id="content_lang_seoname" placeholder="SEO naziv sadržaja">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="content_lang_url" class="col-sm-3 control-label">Slug (URL):</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" type="text" name="content_lang_url" id="content_lang_url" placeholder="moj-sadrzaj-slug">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="content_lang_seodesc" class="col-sm-3 control-label">SEO opis sadržaja:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" type="text" name="content_lang_seodesc" id="content_lang_seodesc" placeholder="SEO opis sadržaja">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="content_lang_seokeywords" class="col-sm-3 control-label">SEO ključne riječi sadržaja:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" type="text" name="content_lang_seokeywords" id="content_lang_seokeywords" placeholder="riječ, riječ, riječ">
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
                                            <label for="content_status" class="control-label">Status:</label>
                                            <select class="form-control" id="content_status" name="content_status">
                                                <option value="1">Objavljen</option>
                                                <option value="2">U izradi</option>
                                                <option value="3">Na čekanju</option>
                                                <option value="0">Arhiviran</option>
                                            </select>
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label for="content_sub" class="control-label">Pripada stranici:</label>
                                            <select class="form-control" id="content_sub" name="content_sub">
                                                <option value="0">Samostalna</option>
                                                <?php
                        							$sub_query = $db->prepare("
                        												SELECT content_id, content_lang_name, content_sub
                        												FROM idk_content
                                                                        LEFT JOIN idk_content_lang ON idk_content.content_id = idk_content_lang.content_lang_postid
                        												WHERE content_sub = :content_sub AND content_lang_langid = :content_lang_langid
                        												ORDER BY content_lang_name ASC");

                        							$sub_query->execute(array(
                        											':content_sub' => 0,
                        											':content_lang_langid' => 1));

                        							while($sub = $sub_query->fetch()) {

                                                        $content_id = $sub['content_id'];

                        								echo "<option value='" . $sub['content_id'] . "'>" . $sub['content_lang_name'] . "</option>";

                                                        $sub_sub_query = $db->prepare("
                            												SELECT content_id, content_lang_name
                            												FROM idk_content
                                                                            LEFT JOIN idk_content_lang ON idk_content.content_id = idk_content_lang.content_lang_postid
                            												WHERE content_sub = :content_sub AND content_lang_langid = :content_lang_langid
                            												ORDER BY content_lang_name ASC");

                            							$sub_sub_query->execute(array(
                            											':content_sub' => $content_id,
                            											':content_lang_langid' => 1));

                            							while($sub_sub = $sub_sub_query->fetch()) {
                                                            echo "<option value='" . $sub_sub['content_id'] . "'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $sub_sub['content_lang_name'] . "</option>";
                                                        }
                        							}
                        						?>
                                            </select>
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label for="content_sort" class="control-label">Raspored:</label>
                                            <input class="form-control" type="number" name="content_sort" id="content_sort" placeholder="Automatski">
                                        </div>
                                        <br>
                                        <div class="form-group">
    										<div class="">
                                                <label for="content_lang_img" class="control-label">Fotografija:</label><br>
    											<div class="fileinput fileinput-new" data-provides="fileinput">
    												<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;"></div>
    												<div>
    													<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografiju</span><span class="fileinput-exists">Promijeni</span><input type="file" name="content_lang_img" id="content_lang_img"></span>
    													<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
    													<script>
    														$(function (){
    															$('#content_lang_img').change(function (){

    																var ext = $('#content_lang_img').val().split('.').pop().toLowerCase();

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
    										<label for="content_comment" class="col-sm-5 control-label">Komentari:</label>
    										<div class="col-sm-7">
    											<div class="main-container__column materail-switch materail-switch_success">
    												<input class="materail-switch__element" name="content_comment" value="1" type="checkbox" id="content_comment">
    												<label class="materail-switch__label" for="content_comment"></label>
    											</div>
    										</div>
    									</div>
                                        <br>
                                        <div class="form-group row">
    										<label for="content_share" class="col-sm-5 control-label">Društvene mreže:</label>
    										<div class="col-sm-7">
    											<div class="main-container__column materail-switch materail-switch_success">
    												<input class="materail-switch__element" name="content_share" value="1" type="checkbox" id="content_share" checked>
    												<label class="materail-switch__label" for="content_share"></label>
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

						$content_id = $_GET['id'];

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
										SELECT content_sub, content_sort, content_comment, content_share, content_status, content_datetime,
												content_lang_name, content_lang_url, content_lang_content, content_lang_img, content_lang_seoname, content_lang_seodesc, content_lang_seokeywords
										FROM idk_content
										INNER JOIN idk_content_lang ON idk_content.content_id = idk_content_lang.content_lang_postid
										WHERE content_id = :content_id AND content_lang_langid = :content_lang_langid");

						$query->execute(array(
									':content_id' => $content_id,
									':content_lang_langid' => $lang_id));

						$row = $query->fetch();

							$content_lang_name = $row['content_lang_name'];
							$content_lang_url = $row['content_lang_url'];
							$content_lang_url_string = get_string_between($row['content_lang_url'], '/', '/');
							$content_lang_content = $row['content_lang_content'];
							$content_lang_seoname = $row['content_lang_seoname'];
							$content_lang_seodesc = $row['content_lang_seodesc'];
							$content_lang_seokeywords = $row['content_lang_seokeywords'];

							$content_sub = $row['content_sub'];
							$content_sort = $row['content_sort'];
							$content_comment = $row['content_comment'];
							$content_share = $row['content_share'];
							$content_status = $row['content_status'];
							$content_datetime = $row['content_datetime'];

							//$contact_dob = date('d.m.Y.', strtotime($row['contact_dob']));

							if($row['content_lang_img'] == NULL){
								$content_lang_img = "none.jpg";
								$content_lang_img_input = NULL;
							}else{
								$content_lang_img = $row['content_lang_img'];
								$content_lang_img_input = $row['content_lang_img'];
							}

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="far fa-copy idk_color_green" aria-hidden="true"></i> Uredi sadržaj</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>content?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
								<form id="idk_form" action="<?php getSiteURL(); ?>do?form=edit_content" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="content_id" value="<?php echo $content_id; ?>" />
									<input type="hidden" name="content_lang_langid" value="<?php echo $lang_id; ?>" />
									<div class="form-group">
										<div class="col-sm-12">
											<input class="form-control idk_form_control_large" type="text" name="content_lang_name" value="<?php echo $content_lang_name; ?>" id="content_lang_name" placeholder="Naziv" required>
											<br>
											<p>URL: <a href="<?php getSiteUrlFront(); ?><?php echo $content_lang_url; ?>" target="_blank"><?php getSiteUrlFront(); ?><?php echo $content_lang_url; ?></a></p>
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-12">
											<textarea class="form-control materail-input material-textarea" name="content_lang_content" id="content_lang_content" placeholder="Sadržaj ..." rows="8"><?php echo $content_lang_content; ?></textarea>
                                            <script>
                                                $('#content_lang_content').trumbowyg({
                                                    lang: 'hr',
													semantic: {
												        'div': 'div' // Editor does nothing on div tags now
												    },
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
											<div class="panel-heading material-accordion__heading" id="acc_headingFour">
                            					<h4 class="panel-title">
                            						<a class="collapsed material-accordion__title" data-toggle="collapse" data-parent="#accordion" href="#acc_collapseFour">Fotogalerija</a>
                            					</h4>
                            				</div>
                            				<div id="acc_collapseFour" class="panel-collapse collapse material-accordion__collapse">
                            					<div class="panel-body">
													<div class="fileinput fileinput-new" data-provides="fileinput">
														<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberite fotografije</span><span class="fileinput-exists">Promijenite</span><input name="cphotos_file[]" id="cphotos_file" multiple="" type="file" /></span>
														<span class="fileinput-filenumber"></span>
														<ul class="list-inline">
														<hr />
														<?php
															$gallery_query = $db->prepare("
																					SELECT cphotos_id, cphotos_file
																					FROM idk_content_photos
																					WHERE cphotos_contentid = :cphotos_contentid");

															$gallery_query->execute(array(
																				':cphotos_contentid' => $content_id));

															while($gallery = $gallery_query->fetch()){

																$cphotos_id = $gallery['cphotos_id'];
																$cphotos_file = $gallery['cphotos_file'];
														?>
															<li id="<?php echo $cphotos_id; ?>" class="idk_img_holder">
																<img class='idk_thumbnail thumbnail' src="<?php getSiteUrlFront(); ?>files/content-photos/thumbs/<?php echo $cphotos_file; ?>" />
																<a onclick="removeImg(<?php echo $cphotos_id; ?>)" href="do?form=del_content_photo&id=<?php echo $cphotos_id; ?>"><i class="idk_img_remove fa fa-times text-danger fa-lg" data-toggle="tooltip" data-placement="top" title="Obriši"></i></a>
															</li>

														<?php } ?>
															<script>
																function removeImg(ng_id){
																	$("#" + ng_id).remove();
																}
															</script>
														</ul>
														<hr />
														<ul class="list-inline" id="result" />
														<script>
															window.onload = function(){
																if(window.File && window.FileList && window.FileReader){
																	var filesInput = document.getElementById("cphotos_file");
																	filesInput.addEventListener("change", function(event){
																		var files = event.target.files;
																		var output = document.getElementById("result");
																		var ul = document.getElementById("result");
																		while (ul.hasChildNodes()) {
																			ul.removeChild(ul.firstChild);
																		}
																		for(var i = 0; i< files.length; i++)
																		{
																			var file = files[i];
																			if(!file.type.match('image'))
																				continue;
																			var picReader = new FileReader();
																			picReader.addEventListener("load",function(event){
																				var picFile = event.target;
																				var li = document.createElement("li");
																				li.innerHTML = "<img class='idk_thumbnail thumbnail' src='" + picFile.result + "' />";
																				output.insertBefore(li,null);
																			});
																			picReader.readAsDataURL(file);
																		}
																	});
																}else{
																	console.log("Your browser does not support File API");
																}
															}
														</script>
													</div>
                            					</div>
                            				</div>
                            			</div>
                            			<div class="panel panel-default material-accordion__panel">
                            				<div class="panel-heading material-accordion__heading" id="acc_headingTwo">
                            					<h4 class="panel-title">
                            						<a class="collapsed material-accordion__title" data-toggle="collapse" data-parent="#accordion" href="#acc_collapseTwo">SEO postavke</a>
                            					</h4>
                            				</div>
											<div id="acc_collapseTwo" class="panel-collapse collapse material-accordion__collapse">
                            					<div class="panel-body">
                                                    <div class="form-group row">
                										<label for="content_seo_btn" class="col-sm-3 control-label">Ručno podešavanje:</label>
                										<div class="col-sm-9">
                											<div class="main-container__column materail-switch materail-switch_success">
                												<input class="materail-switch__element" name="content_seo" value="1" type="checkbox" id="content_seo_btn">
                												<label class="materail-switch__label" for="content_seo_btn"></label>
                											</div>
                										</div>
                									</div>
                									<script>
                										jQuery(document).ready(function($) {
                											//reset
                											$("#content_seo_btn").prop("checked", false);
                											$("#content_seo_btn").click(function () {

                												if ($("#content_seo_btn").is(":checked")) {

                													//checked
                													$("#content_seo").removeClass("hidden").fadeOut(0).fadeIn(1000);

                												} else {
                													//unchecked
                													$("#content_seo").addClass("hidden");
                												}

                											})

                										});
                									</script>
                									<div class="hidden" id="content_seo">
                                                        <div class="form-group">
                                                            <label for="content_lang_seoname" class="col-sm-3 control-label">SEO naziv sadržaja:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" type="text" name="content_lang_seoname" value="<?php echo $content_lang_seoname; ?>" id="content_lang_seoname" placeholder="SEO naziv sadržaja">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="content_lang_url" class="col-sm-3 control-label">Slug (URL):</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" type="text" name="content_lang_url" value="<?php echo $content_lang_url_string; ?>" id="content_lang_url" placeholder="moj-sadrzaj-slug">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="content_lang_seodesc" class="col-sm-3 control-label">SEO opis sadržaja:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" type="text" name="content_lang_seodesc" value="<?php echo $content_lang_seodesc; ?>" id="content_lang_seodesc" placeholder="SEO opis sadržaja">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="content_lang_seokeywords" class="col-sm-3 control-label">SEO ključne riječi sadržaja:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" type="text" name="content_lang_seokeywords" value="<?php echo $content_lang_seokeywords; ?>" id="content_lang_seokeywords" placeholder="riječ, riječ, riječ">
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
                                            <label for="content_status" class="control-label">Status:</label>
                                            <select class="form-control" id="content_status" name="content_status">
                                                <option value="1" <?php if($content_status == "1"){ echo "selected"; } ?>>Objavljen</option>
                                                <option value="2" <?php if($content_status == "2"){ echo "selected"; } ?>>U izradi</option>
                                                <option value="3" <?php if($content_status == "3"){ echo "selected"; } ?>>Na čekanju</option>
                                                <option value="0" <?php if($content_status == "0"){ echo "selected"; } ?>>Arhiviran</option>
                                            </select>
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label for="content_sub" class="control-label">Pripada stranici:</label>
                                            <select class="form-control" id="content_sub" name="content_sub">
                                                <option value="0">Samostalna</option>
                                                <?php
                        							$sub_query = $db->prepare("
                        												SELECT content_id, content_lang_name, content_sub
                        												FROM idk_content
                                                                        LEFT JOIN idk_content_lang ON idk_content.content_id = idk_content_lang.content_lang_postid
                        												WHERE content_sub = :content_sub AND content_lang_langid = :content_lang_langid
                        												ORDER BY content_lang_name ASC");

                        							$sub_query->execute(array(
                        											':content_sub' => 0,
                        											':content_lang_langid' => 1));

                        							while($sub = $sub_query->fetch()) {

														if($sub['content_id'] == $content_sub){
															$selected = "selected";
														}else{
															$selected = "";
														}

                                                        $content_id = $sub['content_id'];

                        								echo "<option value='" . $sub['content_id'] . "' ' . $selected . '>" . $sub['content_lang_name'] . "</option>";

                                                        $sub_sub_query = $db->prepare("
                            												SELECT content_id, content_lang_name
                            												FROM idk_content
                                                                            LEFT JOIN idk_content_lang ON idk_content.content_id = idk_content_lang.content_lang_postid
                            												WHERE content_sub = :content_sub AND content_lang_langid = :content_lang_langid
                            												ORDER BY content_lang_name ASC");

                            							$sub_sub_query->execute(array(
                            											':content_sub' => $content_id,
                            											':content_lang_langid' => 1));

                            							while($sub_sub = $sub_sub_query->fetch()) {
                                                            echo "<option value='" . $sub_sub['content_id'] . "'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $sub_sub['content_lang_name'] . "</option>";
                                                        }
                        							}
                        						?>
                                            </select>
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label for="content_sort" class="control-label">Raspored:</label>
                                            <input class="form-control" type="number" name="content_sort" value="<?php echo $content_sort; ?>" id="content_sort" placeholder="Automatski">
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label for="content_lang_img" class="control-label">Fotografija:</label><br>
											<div class="fileinput fileinput-new" data-provides="fileinput">
												<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;">
													<img src="<?php getSiteUrlFront(); ?>files/content/thumbs/<?php echo $content_lang_img; ?>">
												</div>
												<input type="hidden" name="content_lang_img_input" value="<?php echo $content_lang_img_input; ?>" />
												<div>
													<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografiju</span><span class="fileinput-exists">Promijeni</span><input type="file" name="content_lang_img" id="content_lang_img"></span>
													<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
													<script>
														$(function (){
															$('#content_lang_img').change(function (){

																var ext = $('#content_lang_img').val().split('.').pop().toLowerCase();

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
    										<label for="content_comment" class="col-sm-5 control-label">Komentari:</label>
    										<div class="col-sm-7">
    											<div class="main-container__column materail-switch materail-switch_success">
    												<input class="materail-switch__element" name="content_comment" value="1" type="checkbox" id="content_comment" <?php if($content_comment == NULL){ echo ""; }else{ echo "checked"; } ?>>
    												<label class="materail-switch__label" for="content_comment"></label>
    											</div>
    										</div>
    									</div>
                                        <br>
                                        <div class="form-group row">
    										<label for="content_share" class="col-sm-5 control-label">Društvene mreže:</label>
    										<div class="col-sm-7">
    											<div class="main-container__column materail-switch materail-switch_success">
    												<input class="materail-switch__element" name="content_share" value="1" type="checkbox" id="content_share" <?php if($content_share == NULL){ echo ""; }else{ echo "checked"; } ?>>
    												<label class="materail-switch__label" for="content_share"></label>
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

						$content_id = $_GET['id'];

						//Get
						$query_select = $db->prepare("
												SELECT content_lang_name
												FROM idk_content_lang
												WHERE content_lang_postid = :content_lang_postid");

						$query_select->execute(array(
											':content_lang_postid' => $content_id));

						$row_select = $query_select->fetch();

						$content_lang_name = $row_select['content_lang_name'];

						//Save
						$query = $db->prepare("
										UPDATE idk_content
										SET content_status = :content_status
										WHERE content_id = :content_id");

						$query->execute(array(
									':content_status' => 0,
									':content_id' => $content_id));

						//Add to LOGS
						$log_desc = "Arhivirao sadržaj: " . $content_lang_name . "";
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


						header("Location: " . getSiteURLr() . "content?page=list&mess=3");

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
