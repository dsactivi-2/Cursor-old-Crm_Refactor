<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getUserStatus = getUserStatus();

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: products?page=list");
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
					<h1><i class="fas fa-barcode idk_color_green"></i> Proizvodi</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>products?page=add" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></a>
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
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novi proizvod.</div>';
									}elseif($mess == 2){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili profil proizvoda.</div>';
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
											<th>Proizvođač</th>
											<th class="text-center">Pregleda</th>
											<th class="text-center">Jezici</th>
											<th class="text-center">Status</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
															SELECT product_id, product_brand, product_count, product_lang_name, product_lang_url, product_status
															FROM idk_products
															LEFT JOIN idk_products_lang ON idk_products.product_id = idk_products_lang.product_lang_productid
															WHERE product_status != :product_status
															GROUP BY product_id");

											$query->execute(array(':product_status' => 0));

											while($row = $query->fetch()){

												$product_id = $row['product_id'];
												$product_brand = $row['product_brand'];
												$product_count = $row['product_count'];
												$product_lang_name = $row['product_lang_name'];
												$product_lang_url = $row['product_lang_url'];

												if($row['product_status'] == 1){
													$product_status = "Objavljen";
												}elseif($row['product_status'] == 2){
													$product_status = "U izradi";
												}elseif($row['product_status'] == 3){
													$product_status = "Na čekanju";
												}elseif($row['product_status'] == 0){
													$product_status = "Arhiviran";
												}
										?>
										<tr>
											<td class="text-center"><?php echo $product_id; ?></td>
											<td><a href="<?php getSiteUrlFront(); ?><?php echo $product_lang_url; ?>" target="_blank"><?php echo $product_lang_name; ?></a></td>
											<td><?php echo $product_brand; ?></td>
											<td class="text-center"><?php echo $product_count; ?></td>
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

														echo '<li class="list-inline-item"><a href="' . getSiteUrlr() . 'products?page=edit&id=' . $product_id . '&lang=' . $lang_id . '">' . $lang_code . '</a></li>';

													}

													echo '</ul>';
												?>
											</td>
											<td class="text-center"><?php echo $product_status; ?></td>
											<td class="text-center">
												<div class="btn-group material-btn-group">
													<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
													<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
														<li><a href="<?php getSiteUrlFront(); ?><?php echo $product_lang_url; ?>" target="_blank" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
														<li><a href="<?php getSiteURL(); ?>products?page=edit&id=<?php echo $product_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi</a></li>
														<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>products?page=archive&id=<?php echo $product_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
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
														<p>Jeste li sigurni da želite arhivirati profil proizvoda?</p>
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
					<h1><i class="fas fa-barcode idk_color_green"></i> Dodaj novi proizvod</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>products?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
								<form id="idk_form" action="<?php getSiteURL(); ?>do?form=add_product" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<div class="form-group">
										<div class="col-sm-12">
											<input class="form-control idk_form_control_large" type="text" name="product_lang_name" id="product_lang_name" placeholder="Naziv proizvoda" required>
										</div>
									</div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
        										<div class="col-sm-12">
        											<input class="form-control" type="text" name="product_number" id="product_number" placeholder="Šifra proizvoda">
        										</div>
        									</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
        										<div class="col-sm-12">
													<script>
													  $( function() {
													    var availableTags = [
															<?php

																$select_query = $db->prepare("
																					SELECT product_brand
																					FROM idk_products
																					GROUP BY product_brand");

																$select_query->execute();

																while($select_row = $select_query->fetch()) {
																	echo '"' . $select_row['product_brand'] . '",';
																}

															 ?>
													    ];
													    $( "#product_brand" ).autocomplete({
													      source: availableTags
													    });
													  } );
													  </script>
        											<input class="form-control" type="text" name="product_brand" id="product_brand" placeholder="Proizvođač">
        										</div>
        									</div>
                                        </div>
                                    </div>
									<div class="form-group">
										<div class="col-sm-12">
											<textarea class="form-control materail-input material-textarea" name="product_lang_desc" id="product_lang_desc" placeholder="Opis proizvoda ..." rows="8"></textarea>
                                            <script>
                                                $('#product_lang_desc').trumbowyg({
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
                            				<div class="panel-heading material-accordion__heading" id="acc_headingOne">
                            					<h4 class="panel-title">
                            						<a data-toggle="collapse" data-parent="#accordion" href="#acc_collapseOne" class="collapsed material-accordion__title">Fotogalerija</a>
                            					</h4>
                            				</div>
                            				<div id="acc_collapseOne" class="panel-collapse collapse material-accordion__collapse">
                            					<div class="panel-body">
													<div class="fileinput fileinput-new" data-provides="fileinput">
														<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberite fotografije</span><span class="fileinput-exists">Promijenite</span><input name="pphotos_file[]" id="pphotos_file" multiple="" type="file" /></span>
														<span class="fileinput-filenumber"></span>
														<ul class="list-inline" id="result" />

														<script>
															window.onload = function(){
																if(window.File && window.FileList && window.FileReader){
																	var filesInput = document.getElementById("pphotos_file");
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
                            				<div class="panel-heading material-accordion__heading" id="acc_headingDocs">
                            					<h4 class="panel-title">
                            						<a data-toggle="collapse" data-parent="#accordion" href="#acc_collapseDocs" class="collapsed material-accordion__title">Dokumenti</a>
                            					</h4>
                            				</div>
                            				<div id="acc_collapseDocs" class="panel-collapse collapse material-accordion__collapse">
                            					<div class="panel-body">
													<div class="row">
														<div class="col-md-6">
															<input class="form-control" type="text" name="pdocs_lang_name" placeholder="Naziv dokumenta">
														</div>
														<div class="col-md-6">
															<div class="fileinput fileinput-new" data-provides="fileinput">
																<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberite dokument</span><span class="fileinput-exists">Promijenite</span><input name="pdocs_lang_file" id="pdocs_lang_file" type="file" /></span>
																<span class="fileinput-filenumber"></span>
																<span class="fileinput-filename"></span>
  																<a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">&times;</a>
															</div>
														</div>
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
                										<label for="product_seo_btn" class="col-sm-3 control-label">Ručno podešavanje:</label>
                										<div class="col-sm-9">
                											<div class="main-container__column materail-switch materail-switch_success">
                												<input class="materail-switch__element" name="product_seo" value="1" type="checkbox" id="product_seo_btn">
                												<label class="materail-switch__label" for="product_seo_btn"></label>
                											</div>
                										</div>
                									</div>
                									<script>
                										jQuery(document).ready(function($) {
                											//reset
                											$("#product_seo_btn").prop("checked", false);
                											$("#product_seo_btn").click(function () {

                												if ($("#product_seo_btn").is(":checked")) {

                													//checked
                													$("#product_seo").removeClass("hidden").fadeOut(0).fadeIn(1000);

                												} else {
                													//unchecked
                													$("#product_seo").addClass("hidden");
                												}

                											})

                										});
                									</script>
                									<div class="hidden" id="product_seo">
                                                        <div class="form-group">
                                                            <label for="product_lang_seoname" class="col-sm-3 control-label">SEO naziv proizvoda:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" type="text" name="product_lang_seoname" id="product_lang_seoname" placeholder="SEO naziv proizvoda">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="product_lang_url" class="col-sm-3 control-label">Slug (URL):</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" type="text" name="product_lang_url" id="product_lang_url" placeholder="moj-proizvod-slug">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="product_lang_seodesc" class="col-sm-3 control-label">SEO opis proizvoda:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" type="text" name="product_lang_seodesc" id="product_lang_seodesc" placeholder="SEO opis proizvoda">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="product_lang_seokeywords" class="col-sm-3 control-label">SEO ključne riječi proizvoda:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" type="text" name="product_lang_seokeywords" id="product_lang_seokeywords" placeholder="riječ, riječ, riječ">
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
                                            <label for="product_status" class="control-label">Status:</label>
                                            <select class="form-control" id="product_status" name="product_status">
                                                <option value="1">Objavljen</option>
                                                <option value="2">U izradi</option>
                                                <option value="3">Na čekanju</option>
                                                <option value="0">Arhiviran</option>
                                            </select>
                                        </div>
                                        <br>
										<div class="form-group">
											<label for="productcat_id" class="control-label">Kategorija:</label><br>
											<select class="selectpicker form-control" id="productcat_id" name="productcat_id[]" multiple>
												<?php getCatOption(); ?>
											</select>
											<script>
												$('.selectpicker').selectpicker('render');
											</script>
										</div>
                                        <br>
                                        <div class="form-group">
    										<div class="">
                                                <label for="product_lang_img" class="control-label">Fotografija:</label><br>
    											<div class="fileinput fileinput-new" data-provides="fileinput">
    												<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;"></div>
    												<div>
    													<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografiju</span><span class="fileinput-exists">Promijeni</span><input type="file" name="product_lang_img" id="product_lang_img"></span>
    													<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
    													<script>
    														$(function (){
    															$('#product_lang_img').change(function (){

    																var ext = $('#product_lang_img').val().split('.').pop().toLowerCase();

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
    										<label for="product_featured" class="col-sm-5 control-label">Izdvojeno:</label>
    										<div class="col-sm-7">
    											<div class="main-container__column materail-switch materail-switch_success">
    												<input class="materail-switch__element" name="product_featured" value="1" type="checkbox" id="product_featured">
    												<label class="materail-switch__label" for="product_featured"></label>
    											</div>
    										</div>
    									</div>
                                        <br>
                                        <div class="form-group row">
    										<label for="product_onsale" class="col-sm-5 control-label">Akcija:</label>
    										<div class="col-sm-7">
    											<div class="main-container__column materail-switch materail-switch_success">
    												<input class="materail-switch__element" name="product_onsale" value="1" type="checkbox" id="product_onsale">
    												<label class="materail-switch__label" for="product_onsale"></label>
    											</div>
    										</div>
    									</div>
                                        <br>
                                        <div class="form-group row">
    										<label for="product_comment" class="col-sm-5 control-label">Komentari:</label>
    										<div class="col-sm-7">
    											<div class="main-container__column materail-switch materail-switch_success">
    												<input class="materail-switch__element" name="product_comment" value="1" type="checkbox" id="product_comment">
    												<label class="materail-switch__label" for="product_comment"></label>
    											</div>
    										</div>
    									</div>
                                        <br>
                                        <div class="form-group row">
    										<label for="product_share" class="col-sm-5 control-label">Društvene mreže:</label>
    										<div class="col-sm-7">
    											<div class="main-container__column materail-switch materail-switch_success">
    												<input class="materail-switch__element" name="product_share" value="1" type="checkbox" id="product_share" checked>
    												<label class="materail-switch__label" for="product_share"></label>
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

						Global $product_id;
						$product_id = $_GET['id'];

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
										SELECT product_number, product_brand, product_featured, product_onsale, product_comment, product_share, product_status,
												product_lang_name, product_lang_url, product_lang_desc, product_lang_img, product_lang_seoname, product_lang_seodesc, product_lang_seokeywords
										FROM idk_products
										INNER JOIN idk_products_lang ON idk_products.product_id = idk_products_lang.product_lang_productid
										WHERE product_id = :product_id AND product_lang_langid = :product_lang_langid");

						$query->execute(array(
									':product_id' => $product_id,
									':product_lang_langid' => $lang_id));

						$row = $query->fetch();

							$product_number = $row['product_number'];
							$product_brand = $row['product_brand'];
							$product_featured = $row['product_featured'];
							$product_onsale = $row['product_onsale'];
							$product_comment = $row['product_comment'];
							$product_share = $row['product_share'];
							$product_status = $row['product_status'];
							$product_lang_name = $row['product_lang_name'];
							$product_lang_url = $row['product_lang_url'];
							$product_lang_url_string = get_string_between($row['product_lang_url'], '/', '/');
							$product_lang_desc = $row['product_lang_desc'];
							$product_lang_seoname = $row['product_lang_seoname'];
							$product_lang_seodesc = $row['product_lang_seodesc'];
							$product_lang_seokeywords = $row['product_lang_seokeywords'];

							if($row['product_lang_img'] == NULL){
								$product_lang_img = "none.jpg";
								$product_lang_img_input = NULL;
							}else{
								$product_lang_img = $row['product_lang_img'];
								$product_lang_img_input = $row['product_lang_img'];
							}

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fas fa-barcode idk_color_green"></i> Uredi proizvod</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>products?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
								<form id="idk_form" action="<?php getSiteURL(); ?>do?form=edit_product" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
									<input type="hidden" name="product_lang_langid" value="<?php echo $lang_id; ?>" />
									<div class="form-group">
										<div class="col-sm-12">
											<input class="form-control idk_form_control_large" type="text" name="product_lang_name" value="<?php echo $product_lang_name; ?>" id="product_lang_name" placeholder="Naziv proizvoda" required>
											<br>
											<p>URL: <a href="<?php getSiteUrlFront(); ?><?php echo $product_lang_url; ?>" target="_blank"><?php getSiteUrlFront(); ?><?php echo $product_lang_url; ?></a></p>
										</div>
									</div>
									<div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
        										<div class="col-sm-12">
        											<input class="form-control" type="text" name="product_number" value="<?php echo $product_number; ?>" id="product_number" placeholder="Šifra proizvoda">
        										</div>
        									</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
        										<div class="col-sm-12">
													<script>
													  $( function() {
													    var availableTags = [
															<?php

																$select_query = $db->prepare("
																					SELECT product_brand
																					FROM idk_products
																					GROUP BY product_brand");

																$select_query->execute();

																while($select_row = $select_query->fetch()) {
																	echo '"' . $select_row['product_brand'] . '",';
																}

															 ?>
													    ];
													    $( "#product_brand" ).autocomplete({
													      source: availableTags
													    });
													  } );
													  </script>
        											<input class="form-control" type="text" name="product_brand" value="<?php echo $product_brand; ?>" id="product_brand" placeholder="Proizvođač">
        										</div>
        									</div>
                                        </div>
                                    </div>
									<div class="form-group">
										<div class="col-sm-12">
											<textarea class="form-control materail-input material-textarea" name="product_lang_desc" id="product_lang_desc" placeholder="Opis proizvoda ..." rows="8"><?php echo $product_lang_desc; ?></textarea>
                                            <script>
                                                $('#product_lang_desc').trumbowyg({
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
											<div class="panel-heading material-accordion__heading" id="acc_headingFour">
                            					<h4 class="panel-title">
                            						<a class="collapsed material-accordion__title" data-toggle="collapse" data-parent="#accordion" href="#acc_collapseFour">Fotogalerija</a>
                            					</h4>
                            				</div>
                            				<div id="acc_collapseFour" class="panel-collapse collapse material-accordion__collapse">
                            					<div class="panel-body">
													<div class="fileinput fileinput-new" data-provides="fileinput">
														<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberite fotografije</span><span class="fileinput-exists">Promijenite</span><input name="pphotos_file[]" id="pphotos_file" multiple="" type="file" /></span>
														<span class="fileinput-filenumber"></span>
														<ul class="list-inline">
														<hr />
														<?php
															$gallery_query = $db->prepare("
																					SELECT pphotos_id, pphotos_file
																					FROM idk_product_photos
																					WHERE pphotos_productid = :pphotos_productid");

															$gallery_query->execute(array(
																				':pphotos_productid' => $product_id));

															while($gallery = $gallery_query->fetch()){

																$pphotos_id = $gallery['pphotos_id'];
																$pphotos_file = $gallery['pphotos_file'];
														?>
															<li id="<?php echo $pphotos_id; ?>" class="idk_img_holder">
																<img class='idk_thumbnail thumbnail' src="<?php getSiteUrlFront(); ?>files/product-photos/thumbs/<?php echo $pphotos_file; ?>" />
																<a onclick="removeImg(<?php echo $pphotos_id; ?>)" href="do?form=del_product_photo&id=<?php echo $pphotos_id; ?>"><i class="idk_img_remove fa fa-times text-danger fa-lg" data-toggle="tooltip" data-placement="top" title="Obriši"></i></a>
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
																	var filesInput = document.getElementById("pphotos_file");
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
                            				<div class="panel-heading material-accordion__heading" id="acc_headingDocs">
                            					<h4 class="panel-title">
                            						<a data-toggle="collapse" data-parent="#accordion" href="#acc_collapseDocs" class="collapsed material-accordion__title">Dokumenti</a>
                            					</h4>
                            				</div>
                            				<div id="acc_collapseDocs" class="panel-collapse collapse material-accordion__collapse">
                            					<div class="panel-body">
													<p><strong>Dodaj novi dokument:</strong></p>
													<div class="row">
														<div class="col-md-6">
															<input class="form-control" type="text" name="pdocs_lang_name" placeholder="Naziv dokumenta">
														</div>
														<div class="col-md-6">
															<div class="fileinput fileinput-new" data-provides="fileinput">
																<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberite dokument</span><span class="fileinput-exists">Promijenite</span><input name="pdocs_lang_file" id="pdocs_lang_file" type="file" /></span>
																<span class="fileinput-filenumber"></span>
																<span class="fileinput-filename"></span>
  																<a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">&times;</a>
															</div>
														</div>
													</div>
													<hr>
													<p><strong>Trenutni dokumenti:</strong></p>
													<table class="table table-striped">
														<thead>
															<tr>
																<th scope="col">Naziv</th>
																<th scope="col">Dokument</th>
																<th class="text-center" scope="col">Obriši</th>
															</tr>
														</thead>
														<tbody>
															<?php
			                                                    $query_docs = $db->prepare("
			                                                                    SELECT pdocs_lang_name, pdocs_lang_file, pdocs_lang_id
			                                                                    FROM idk_product_docs
			                                                                    INNER JOIN idk_product_docs_lang ON idk_product_docs.pdocs_id = idk_product_docs_lang.pdocs_lang_pdocsid
			                                                                    WHERE pdocs_lang_langid = :pdocs_lang_langid AND pdocs_productid = :pdocs_productid
			                                                                    GROUP BY pdocs_id");

			                                                    $query_docs->execute(array(
			                                                                    ':pdocs_lang_langid' => $lang_id,
			                                                                    ':pdocs_productid' => $product_id));

																if($query_docs->rowCount() != 0){



				                                                    while($row_docs = $query_docs->fetch()){

				                                                        $pdocs_lang_id = $row_docs['pdocs_lang_id'];
				                                                        $pdocs_lang_name = $row_docs['pdocs_lang_name'];
				                                                        $pdocs_lang_file = $row_docs['pdocs_lang_file'];


			                                                ?>
															<tr>
																<td><?php echo $pdocs_lang_name; ?></td>
																<td><a href="<?php getSiteUrlFront(); ?>files/product-documents/<?php echo $pdocs_lang_file; ?>" target="_blank">Otvori</a></td>
																<td class="text-center"><a href="#" data="<?php getSiteURL(); ?>products?page=del_docs&id=<?php echo $pdocs_lang_id; ?>" data-toggle="modal" data-target="#doc_delModal" class="doc_del btn material-btn material-btn-icon-danger material-btn_danger main-container__column"><i class="far fa-trash-alt"></i></a></td>
															</tr>
															<?php
																	}
																}else{
																	echo '<tr>
																		<td colspan="3" class="text-center">Trenutno nema dodanih dokumenata!</td>
																	</tr>';
																}
															?>
															<script>
																$(".doc_del").click(function () {
																	var addressValue = $(this).attr("data");
																	document.getElementById("del_doc_link").href = addressValue;
																});
															</script>
															<!-- Modal -->
															<div class="modal material-modal material-modal_danger fade" id="doc_delModal">
																<div class="modal-dialog">
																	<div class="modal-content material-modal__content">
																		<div class="modal-header material-modal__header">
																			<span class="close material-modal__close" data-dismiss="modal">&times;</span>
																			<h4 class="modal-title material-modal__title">Brisanje</h4>
																		</div>
																		<div class="modal-body material-modal__body">
																			<p>Jeste li sigurni da želite obrisati dokument?</p>
																		</div>
																		<div class="modal-footer material-modal__footer">
																			<span class="btn material-btn material-btn" data-dismiss="modal">Zatvori</span>
																			<a id="del_doc_link" href=""><span class="btn btn-primary material-btn material-btn_danger">OBRIŠI</span></a>
																		</div>
																	</div>
																</div>
															</div>
														</tbody>
													</table>
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
                										<label for="product_seo_btn" class="col-sm-3 control-label">Ručno podešavanje:</label>
                										<div class="col-sm-9">
                											<div class="main-container__column materail-switch materail-switch_success">
                												<input class="materail-switch__element" name="product_seo" value="1" type="checkbox" id="product_seo_btn">
                												<label class="materail-switch__label" for="product_seo_btn"></label>
                											</div>
                										</div>
                									</div>
                									<script>
                										jQuery(document).ready(function($) {
                											//reset
                											$("#product_seo_btn").prop("checked", false);
                											$("#product_seo_btn").click(function () {

                												if ($("#product_seo_btn").is(":checked")) {

                													//checked
                													$("#product_seo").removeClass("hidden").fadeOut(0).fadeIn(1000);

                												} else {
                													//unchecked
                													$("#product_seo").addClass("hidden");
                												}

                											})

                										});
                									</script>
                									<div class="hidden" id="product_seo">
                                                        <div class="form-group">
                                                            <label for="product_lang_seoname" class="col-sm-3 control-label">SEO naziv proizvoda:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" type="text" name="product_lang_seoname" value="<?php echo $product_lang_seoname; ?>" id="product_lang_seoname" placeholder="SEO naziv proizvoda">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="product_lang_url" class="col-sm-3 control-label">Slug (URL):</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" type="text" name="product_lang_url" value="<?php echo $product_lang_url_string; ?>" id="product_lang_url" placeholder="moj-proizvod-slug">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="product_lang_seodesc" class="col-sm-3 control-label">SEO opis proizvoda:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" type="text" name="product_lang_seodesc" value="<?php echo $product_lang_seodesc; ?>" id="product_lang_seodesc" placeholder="SEO opis proizvoda">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="product_lang_seokeywords" class="col-sm-3 control-label">SEO ključne riječi proizvoda:</label>
                                                            <div class="col-sm-7">
                                                                <input class="form-control" type="text" name="product_lang_seokeywords" value="<?php echo $product_lang_seokeywords; ?>" id="product_lang_seokeywords" placeholder="riječ, riječ, riječ">
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
                                            <label for="product_status" class="control-label">Status:</label>
                                            <select class="form-control" id="product_status" name="product_status">
                                                <option value="1" <?php if($product_status == "1"){ echo "selected"; } ?>>Objavljen</option>
                                                <option value="2" <?php if($product_status == "2"){ echo "selected"; } ?>>U izradi</option>
                                                <option value="3" <?php if($product_status == "3"){ echo "selected"; } ?>>Na čekanju</option>
                                                <option value="0" <?php if($product_status == "0"){ echo "selected"; } ?>>Arhiviran</option>
                                            </select>
                                        </div>
                                        <br>
										<div class="form-group">
											<label for="productcat_id" class="control-label">Kategorija:</label><br>
											<select class="selectpicker form-control" id="productcat_id" name="productcat_id[]" multiple>
												<?php getCatOptionEdit(); ?>
											</select>
											<script>
												$('.selectpicker').selectpicker('render');
											</script>
										</div>
                                        <br>
                                        <div class="form-group">
                                            <label for="product_lang_img" class="control-label">Fotografija:</label><br>
											<div class="fileinput fileinput-new" data-provides="fileinput">
												<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;">
													<img src="<?php getSiteUrlFront(); ?>files/products/thumbs/<?php echo $product_lang_img; ?>">
												</div>
												<input type="hidden" name="product_lang_img_input" value="<?php echo $product_lang_img_input; ?>" />
												<div>
													<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografiju</span><span class="fileinput-exists">Promijeni</span><input type="file" name="product_lang_img" id="product_lang_img"></span>
													<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
													<script>
														$(function (){
															$('#product_lang_img').change(function (){

																var ext = $('#product_lang_img').val().split('.').pop().toLowerCase();

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
    										<label for="product_featured" class="col-sm-5 control-label">Izdvojeno:</label>
    										<div class="col-sm-7">
    											<div class="main-container__column materail-switch materail-switch_success">
    												<input class="materail-switch__element" name="product_featured" value="1" type="checkbox" id="product_featured" <?php if($product_featured == NULL){ echo ""; }else{ echo "checked"; } ?>>
    												<label class="materail-switch__label" for="product_featured"></label>
    											</div>
    										</div>
    									</div>
                                        <br>
										<div class="form-group row">
    										<label for="product_onsale" class="col-sm-5 control-label">Akcija:</label>
    										<div class="col-sm-7">
    											<div class="main-container__column materail-switch materail-switch_success">
    												<input class="materail-switch__element" name="product_onsale" value="1" type="checkbox" id="product_onsale" <?php if($product_onsale == NULL){ echo ""; }else{ echo "checked"; } ?>>
    												<label class="materail-switch__label" for="product_onsale"></label>
    											</div>
    										</div>
    									</div>
                                        <br>
                                        <div class="form-group row">
    										<label for="product_comment" class="col-sm-5 control-label">Komentari:</label>
    										<div class="col-sm-7">
    											<div class="main-container__column materail-switch materail-switch_success">
    												<input class="materail-switch__element" name="product_comment" value="1" type="checkbox" id="product_comment" <?php if($product_comment == NULL){ echo ""; }else{ echo "checked"; } ?>>
    												<label class="materail-switch__label" for="product_comment"></label>
    											</div>
    										</div>
    									</div>
                                        <br>
                                        <div class="form-group row">
    										<label for="product_share" class="col-sm-5 control-label">Društvene mreže:</label>
    										<div class="col-sm-7">
    											<div class="main-container__column materail-switch materail-switch_success">
    												<input class="materail-switch__element" name="product_share" value="1" type="checkbox" id="product_share" <?php if($product_share == NULL){ echo ""; }else{ echo "checked"; } ?>>
    												<label class="materail-switch__label" for="product_share"></label>
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

						$product_id = $_GET['id'];

						//Get
						$query_select = $db->prepare("
												SELECT product_lang_name
												FROM idk_products_lang
												WHERE product_lang_productid = :product_lang_productid");

						$query_select->execute(array(
											':product_lang_productid' => $product_id));

						$row_select = $query_select->fetch();

						$product_lang_name = $row_select['product_lang_name'];

						//Save
						$query = $db->prepare("
										UPDATE idk_products
										SET product_status = :product_status
										WHERE product_id = :product_id");

						$query->execute(array(
									':product_status' => 0,
									':product_id' => $product_id));

						//Add to LOGS
						$log_desc = "Arhivirao profil proizvoda: " . $content_lang_name . "";
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


						header("Location: " . getSiteURLr() . "products?page=list&mess=3");

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

				case "del_docs":
					if($getUserStatus  == 1){

						$pdocs_lang_id = $_GET['id'];

						$query_select = $db->prepare("
										 SELECT pdocs_lang_name, pdocs_lang_langid, pdocs_productid
										 FROM idk_product_docs
										 INNER JOIN idk_product_docs_lang ON idk_product_docs.pdocs_id = idk_product_docs_lang.pdocs_lang_pdocsid
										 WHERE pdocs_lang_id = :pdocs_lang_id");

						 $query_select->execute(array(
 											':pdocs_lang_id' => $pdocs_lang_id));

						$row_select = $query_select->fetch();

						$pdocs_lang_name = $row_select['pdocs_lang_name'];
						$pdocs_lang_langid = $row_select['pdocs_lang_langid'];
						$pdocs_productid = $row_select['pdocs_productid'];

						//Delete
						$del_query = $db->prepare("
												DELETE FROM idk_product_docs_lang
												WHERE pdocs_lang_id = :pdocs_lang_id");

						$del_query->execute(array(
											':pdocs_lang_id' => $pdocs_lang_id));

						//Add to LOGS
						$log_desc = "Obrisao dokument proizvoda: " . $pdocs_lang_name . "";
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


						header("Location: " . getSiteURLr() . "products?page=edit&id=$pdocs_productid&lang=$pdocs_lang_langid");

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
