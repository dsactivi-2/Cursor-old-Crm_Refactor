<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getUserStatus = getUserStatus();

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: jobs?page=list");
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
					<h1><i class="far fa-newspaper idk_color_green"></i> Poslovi</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>jobs?page=add" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></a>
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
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novi posao.</div>';
									}elseif($mess == 2){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili posao.</div>';
									}elseif($mess == 3){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali posao.</div>';
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
													{ "width": "35%" },
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
											<th>Lokacija</th>
											<th class="text-center">Jezici</th>
											<th class="text-center">Status</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
															SELECT job_id, job_status, job_lang_title, jlocation_lang_name, job_lang_url
															FROM idk_jobs
															LEFT JOIN idk_jobs_lang ON idk_jobs.job_id = idk_jobs_lang.job_lang_jobid
															LEFT JOIN idk_jobs_locations ON idk_jobs.job_locationid = idk_jobs_locations.jlocation_id
															LEFT JOIN idk_jobs_location_langs ON idk_jobs_locations.jlocation_id = idk_jobs_location_langs.jlocation_lang_locationid
															WHERE job_status != :job_status AND idk_jobs_lang.job_lang_langid = 1
															GROUP BY job_id");

											$query->execute(array(':job_status' => 0));

											while($row = $query->fetch()){

												$job_id = $row['job_id'];

												$job_status = $row['job_status'];
												if($row['job_status'] == 1){
													$job_status = "Objavljen";
												}elseif($row['job_status'] == 2){
													$job_status = "U izradi";
												}elseif($row['job_status'] == 3){
													$job_status = "Na čekanju";
												}elseif($row['job_status'] == 0){
													$job_status = "Arhiviran";
												}

												$job_lang_title = $row['job_lang_title'];
												$jlocation_lang_name = $row['jlocation_lang_name'];
												$job_lang_url = $row['job_lang_url'];
										?>
										<tr>
											<td class="text-center"><?php echo $job_id; ?></td>
											<td><a href="<?php getSiteUrlFront(); ?><?php echo $job_lang_url; ?>" target="_blank"><?php echo $job_lang_title; ?></a></td>
											<td><?php echo $jlocation_lang_name; ?></td>
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

														echo '<li class="list-inline-item"><a href="' . getSiteUrlr() . 'jobs?page=edit&id=' . $job_id . '&lang=' . $lang_id . '">' . $lang_code . '</a></li>';

													}

													echo '</ul>';
												?>
											</td>
											<td class="text-center"><?php echo $job_status; ?></td>
											<td class="text-center">
												<div class="btn-group material-btn-group">
													<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
													<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
														<li><a href="<?php getSiteUrlFront(); ?><?php echo $job_lang_url; ?>" target="_blank" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
														<li><a href="<?php getSiteURL(); ?>jobs?page=edit&id=<?php echo $job_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi</a></li>
														<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>jobs?page=archive&id=<?php echo $job_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
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
														<p>Jeste li sigurni da želite arhivirati posao?</p>
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
					<h1><i class="far fa-newspaper idk_color_green"></i> Dodaj novi posao</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>jobs?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
								<form id="idk_form" action="<?php getSiteURL(); ?>do?form=add_job" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<div class="form-group">
										<div class="col-sm-12">
											<input class="form-control idk_form_control_large" type="text" name="job_lang_title" id="job_lang_title" placeholder="Naslov" required>
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-12">
											<textarea class="form-control materail-input material-textarea" name="job_lang_content" id="job_lang_content" placeholder="Informacije ..." rows="8"></textarea>
                                            <script>
                                                $('#job_lang_content').trumbowyg({
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
                                <div class="col-md-4">
                                    <div class="idk_side_form">
                                        <div class="form-group">
                                            <label for="job_status" class="control-label">Status:</label>
                                            <select class="form-control" id="job_status" name="job_status">
                                                <option value="1">Objavljen</option>
                                                <option value="2">U izradi</option>
                                                <option value="3">Na čekanju</option>
                                                <option value="0">Arhiviran</option>
                                            </select>
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label for="job_typeid" class="control-label">Zanimanje:</label>
                                            <select class="form-control" id="job_typeid" name="job_typeid" required>
                                                <option value="">----- Izaberite zanimanje -----</option>
                                                <?php
                        							$sub_query = $db->prepare("
                        												SELECT jtype_id, jtype_lang_name
                        												FROM idk_jobs_type
                                                                        LEFT JOIN idk_jobs_type_lang ON idk_jobs_type.jtype_id = idk_jobs_type_lang.jtype_lang_jtypeid
                        												WHERE jtype_status != :jtype_status AND idk_jobs_type_lang.jtype_lang_langid = 1
																		GROUP BY jtype_id
                        												ORDER BY jtype_lang_name ASC");

                        							$sub_query->execute(array(
                        											':jtype_status' => 0));

                        							while($sub = $sub_query->fetch()) {

                        								echo "<option value='" . $sub['jtype_id'] . "'>" . $sub['jtype_lang_name'] . "</option>";

                        							}
                        						?>
                                            </select>
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label for="job_locationid" class="control-label">Lokacija:</label>
                                            <select class="form-control" id="job_locationid" name="job_locationid">
                                                <option value="">----- Izaberite lokaciju -----</option>
                                                <?php
                        							$sub1_query = $db->prepare("
                        												SELECT jlocation_id, jlocation_lang_name
                        												FROM idk_jobs_locations
                                                                        LEFT JOIN idk_jobs_location_langs ON idk_jobs_locations.jlocation_id = idk_jobs_location_langs.jlocation_lang_locationid
                        												WHERE jlocation_status != :jlocation_status AND idk_jobs_location_langs.jlocation_lang_langid = 1
																		GROUP BY jlocation_id
                        												ORDER BY jlocation_lang_name ASC");

                        							$sub1_query->execute(array(
                        											':jlocation_status' => 0));

                        							while($sub1 = $sub1_query->fetch()) {

                        								echo "<option value='" . $sub1['jlocation_id'] . "'>" . $sub1['jlocation_lang_name'] . "</option>";

                        							}
                        						?>
                                            </select>
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label for="job_lang_salary" class="control-label">Plata:</label>
                                            <input class="form-control" type="text" name="job_lang_salary" id="job_lang_salary">
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label for="job_lang_accommodation" class="control-label">Smještaj:</label>
                                            <input class="form-control" type="text" name="job_lang_accommodation" id="job_lang_accommodation">
                                        </div>
                                        <div class="form-group">
                                            <label for="job_app_form" class="control-label">Link prijavne forme:</label>
                                            <input class="form-control" type="text" name="job_app_form" id="job_app_form">
                                        </div>
                                        <br>
                                        <div class="form-group">
    										<div class="">
                                                <label for="job_lang_img" class="control-label">Fotografija:</label><br>
    											<div class="fileinput fileinput-new" data-provides="fileinput">
    												<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;"></div>
    												<div>
    													<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografiju</span><span class="fileinput-exists">Promijeni</span><input type="file" name="job_lang_img" id="job_lang_img"></span>
    													<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
    													<script>
    														$(function (){
    															$('#job_lang_img').change(function (){

    																var ext = $('#job_lang_img').val().split('.').pop().toLowerCase();

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

						$job_id = $_GET['id'];

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
										SELECT job_typeid, job_locationid, job_status, job_lang_title, job_lang_img, job_lang_content, job_lang_salary, job_lang_accommodation, job_lang_url, job_application_form_link	
										FROM idk_jobs
										INNER JOIN idk_jobs_lang ON idk_jobs.job_id = idk_jobs_lang.job_lang_jobid
										WHERE job_id = :job_id AND job_lang_langid = :job_lang_langid");

						$query->execute(array(
									':job_id' => $job_id,
									':job_lang_langid' => $lang_id));

						$row = $query->fetch();

							$job_typeid = $row['job_typeid'];
							$job_locationid = $row['job_locationid'];
							$job_status = $row['job_status'];
							$job_lang_title = $row['job_lang_title'];
							$job_lang_content = $row['job_lang_content'];
							$job_lang_salary = $row['job_lang_salary'];
							$job_lang_accommodation = $row['job_lang_accommodation'];
							$job_lang_url = $row['job_lang_url'];
							$job_application_form_link	 = $row['job_application_form_link'];

							if($row['job_lang_img'] == NULL){
								$job_lang_img = "none.jpg";
								$job_lang_img_input = NULL;
							}else{
								$job_lang_img = $row['job_lang_img'];
								$job_lang_img_input = $row['job_lang_img'];
							}

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="far fa-newspaper idk_color_green" aria-hidden="true"></i> Uredi posao</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>jobs?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
								<form id="idk_form" action="<?php getSiteURL(); ?>do?form=edit_job" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="job_id" value="<?php echo $job_id; ?>" />
									<input type="hidden" name="job_lang_langid" value="<?php echo $lang_id; ?>" />
									<div class="form-group">
										<div class="col-sm-12">
											<input class="form-control idk_form_control_large" type="text" name="job_lang_title" value="<?php echo $job_lang_title; ?>" id="job_lang_title" required>
											<br>
											<p>URL: <a href="<?php getSiteUrlFront(); ?><?php echo $job_lang_url; ?>" target="_blank"><?php getSiteUrlFront(); ?><?php echo $job_lang_url; ?></a></p>
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-12">
											<textarea class="form-control materail-input material-textarea" name="job_lang_content" id="job_lang_content" rows="8"><?php echo $job_lang_content; ?></textarea>
                                            <script>
                                                $('#job_lang_content').trumbowyg({
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
                                            <label for="job_status" class="control-label">Status:</label>
                                            <select class="form-control" id="job_status" name="job_status">
                                                <option value="1" <?php if($job_status == "1"){ echo "selected"; } ?>>Objavljen</option>
                                                <option value="2" <?php if($job_status == "2"){ echo "selected"; } ?>>U izradi</option>
                                                <option value="3" <?php if($job_status == "3"){ echo "selected"; } ?>>Na čekanju</option>
                                                <option value="0" <?php if($job_status == "0"){ echo "selected"; } ?>>Arhiviran</option>
                                            </select>
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label for="job_typeid" class="control-label">Zanimanje:</label>
                                            <select class="form-control" id="job_typeid" name="job_typeid" required>
                                                <option value="">----- Izaberite zanjmanje -----</option>
                                                <?php
                        							$sub_query = $db->prepare("
                        												SELECT jtype_id, jtype_lang_name
                        												FROM idk_jobs_type
                                                                        LEFT JOIN idk_jobs_type_lang ON idk_jobs_type.jtype_id = idk_jobs_type_lang.jtype_lang_jtypeid
                        												WHERE jtype_status != :jtype_status
																		GROUP BY jtype_id
                        												ORDER BY jtype_lang_name ASC");

                        							$sub_query->execute(array(
                        											':jtype_status' => 0));

                        							while($sub = $sub_query->fetch()) {

                                                        if($sub['jtype_id'] == $job_typeid){
															$selected = "selected";
														}else{
															$selected = "";
														}

                        								echo "<option value='" . $sub['jtype_id'] . "' ' . $selected . '>" . $sub['jtype_lang_name'] . "</option>";

                        							}
                        						?>
                                            </select>
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label for="job_locationid" class="control-label">Lokacija:</label>
                                            <select class="form-control" id="job_locationid" name="job_locationid">
                                                <option value="">----- Izaberite lokaciju -----</option>
                                                <?php
                        							$sub1_query = $db->prepare("
                        												SELECT jlocation_id, jlocation_lang_name
                        												FROM idk_jobs_locations
                                                                        LEFT JOIN idk_jobs_location_langs ON idk_jobs_locations.jlocation_id = idk_jobs_location_langs.jlocation_lang_locationid
                        												WHERE jlocation_status != :jlocation_status
																		GROUP BY jlocation_id
                        												ORDER BY jlocation_lang_name ASC");

                        							$sub1_query->execute(array(
                        											':jlocation_status' => 0));

                        							while($sub1 = $sub1_query->fetch()) {

                                                        if($sub1['jlocation_id'] == $job_locationid){
															$selected = "selected";
														}else{
															$selected = "";
														}

                        								echo "<option value='" . $sub1['jlocation_id'] . "' ' . $selected . '>" . $sub1['jlocation_lang_name'] . "</option>";

                        							}
                        						?>
                                            </select>
                                        </div>
                                        <br>
										<div class="form-group">
                                            <label for="job_lang_salary" class="control-label">Plata:</label>
                                            <input class="form-control" type="text" name="job_lang_salary" id="job_lang_salary" value="<?php echo $job_lang_salary; ?>">
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label for="job_lang_accommodation" class="control-label">Smještaj:</label>
                                            <input class="form-control" type="text" name="job_lang_accommodation" id="job_lang_accommodation" value="<?php echo $job_lang_accommodation; ?>">
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label for="job_app_form" class="control-label">Link prijavne forme:</label>
                                            <input class="form-control" type="text" name="job_app_form" id="job_app_form" value="<?php echo $job_application_form_link; ?>">
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label for="job_lang_img" class="control-label">Fotografija:</label><br>
											<div class="fileinput fileinput-new" data-provides="fileinput">
												<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;">
													<img src="<?php getSiteUrlFront(); ?>files/jobs/thumbs/<?php echo $job_lang_img; ?>">
												</div>
												<input type="hidden" name="job_lang_img_input" value="<?php echo $job_lang_img_input; ?>" />
												<div>
													<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografiju</span><span class="fileinput-exists">Promijeni</span><input type="file" name="job_lang_img" id="job_lang_img"></span>
													<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
													<script>
														$(function (){
															$('#job_lang_img').change(function (){

																var ext = $('#job_lang_img').val().split('.').pop().toLowerCase();

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

						$job_id = $_GET['id'];

						//Get
						$query_select = $db->prepare("
												SELECT job_lang_title
												FROM idk_jobs_lang
												WHERE job_lang_jobid = :job_lang_jobid");

						$query_select->execute(array(
											':job_lang_jobid' => $job_id));

						$row_select = $query_select->fetch();

						$job_lang_title = $row_select['job_lang_title'];

						//Save
						$query = $db->prepare("
										UPDATE idk_jobs
										SET job_status = :job_status
										WHERE job_id = :job_id");

						$query->execute(array(
									':job_status' => 0,
									':job_id' => $job_id));

						//Add to LOGS
						$log_desc = "Arhivirao posao: " . $job_lang_title . "";
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


						header("Location: " . getSiteURLr() . "jobs?page=list&mess=3");

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
