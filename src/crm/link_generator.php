<?php
	include("includes/functions.php");
	include("includes/common.php");
	$getEmployeeStatus = explode( ',' , getEmployeeStatus());

	if(isset($_REQUEST["page"])) {
		if($_REQUEST["page"] == "=show_list"){
			$page = "show_list";
		}else{
			$page = $_REQUEST["page"];
		}
	}else{
		header("Location: employees?page=list");
	}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Generator linkova | <?php getTitle(); ?></title>

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
					<h1><i class="fa fa-retweet idk_color_green" aria-hidden="true"></i> Generator linkova</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>link_generator?page=add" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></a>
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
								<style>
									#idk_table p{
										margin: 0 0 0px !important;
									}
								</style>
								<?php
									if(isset($_GET['mess'])) {
										$mess = $_GET['mess'];
									}else{
										$mess = 0;
									}

									if($mess == 1){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste generisali novi link.</div>';
									}elseif($mess == 2){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili link.</div>';
									}elseif($mess == 3){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili profil zaposlenika.</div>';
									}elseif($mess == 4){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali generisani link.</div>';
									}
								?>
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_table').DataTable({
					
											responsive: true,
											"pageLength": 10,
											"processing": true,
											"serverSide": true,

											"order": [[ 0, "desc" ]],

											 "bAutoWidth": false,

											"aoColumns": [
													{ "width": "5%" },
													{ "width": "20%" },
													{ "width": "13%" },
													{ "width": "5%" },
													{ "width": "5%" },
													{ "width": "5%" },
													{ "width": "5%" },
													{ "width": "10%" },
													{ "width": "10%" },
													{ "width": "15%" },
													{ "width": "7%", "bSortable": false }
												],
											"ajax":{
												url :"serversidedata.php?page=lista_link_generator",
												type: "POST",
												error: function(data){
													$(".list-grid-error").html(""); 
													$("#list-grid_processing").css("display","none");
											
												},
											}
										});
									} );
								</script>
								<script>
									function archiveLink(thisRow){
										var addressValue = $(thisRow).data("link");
										
										document.getElementById("archive_link_gen").href = addressValue;
									}
								</script>
								<!-- Modal -->
								<div class="modal material-modal material-modal_danger fade" id="archiveLinkModal">
									<div class="modal-dialog">
										<div class="modal-content material-modal__content">
											<div class="modal-header material-modal__header">
												<button class="close material-modal__close" data-dismiss="modal">&times;</button>
												<h4 class="modal-title material-modal__title">Arhiviranje</h4>
											</div>
											<div class="modal-body material-modal__body">
												<p>Jeste li sigurni da želite arhivirati link?</p>
											</div>
											<div class="modal-footer material-modal__footer">
												<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
												<a id="archive_link_gen" href=""><button class="btn btn-primary material-btn material-btn_danger">ARHIVIRAJ</button></a>
											</div>
										</div>
									</div>
								</div>
								 <script>
									function copyToClipboard(element) {
										var $temp = $("<input>");
										$("body").append($temp);
										$temp.val($(element).text()).select();
										document.execCommand("copy");
										$temp.remove();
									}
								 </script>
								<table id="idk_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th></th>
											<th>Url</th>
											<th>Opis</th>
											<th class="text-center">Pregleda</th>
											<th class="text-center">Prijava</th>
											<th>Jezik prijave</th>
											<th>Link prijave</th>
											<th>Registracija url</th>
											<th>Thumbnail naloga</th>
											<th>Vrijeme / Dodao</th>
											<th></th>
										</tr>
									</thead>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
				break;
				case "add":
					if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status)) OR (in_array(17, $employee_status))){
						?>
						<div class="row">
							<div class="col-xs-8">
								<h1><i class="fa fa-retweet idk_color_green" aria-hidden="true"></i> Generiši novi link</h1>
							</div>
							<div class="col-xs-4 text-right idk_margin_top10">
								<a href="<?php getSiteURL(); ?>link_generator?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
							</div>
							<div class="col-xs-12">
								<hr />
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="content_box">
									<div class="row">
										<form action="<?php getSiteURL(); ?>do.php?form=generate_new_link" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
											<div class="col-md-offset-1 col-md-5">	
												<div class="form-group">
													<label for="lg_url" class="col-sm-3 control-label"><span class="text-danger">*</span> Website:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="lg_url" id="lg_url" placeholder="www.nazivstranice.com" required>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>
												<div class="form-group">
													<label for="lg_desc" class="col-sm-3 control-label"><span class="text-danger">*</span> Opis:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="lg_desc" id="lg_desc" placeholder="Desni banner 400x300px" required>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>
												<!--
												<div class="form-group">
													<label for="lg_project" class="col-sm-3 control-label"><span class="text-danger">*</span> Projekat:</label>
													<div class="col-sm-9">
														<select class="selectpicker" id="lg_project" data-live-search="true" name="lg_project[]" multiple>
															<option value="0">Bez projekta</option>
															<?php /*getProjectList();*/ ?>
														</select>
													</div>
												</div>
												-->
												<div class="form-group">
													<label for="lg_nalogid" class="col-sm-3 control-label"><span class="text-danger">*</span> Nalog:</label>
													<div class="col-sm-9">
														<select class="selectpicker" id="lg_nalogid" data-live-search="true" name="lg_nalogid" required>
															<option value="0">Bez naloga</option>
															<?php getNalogList(); ?>
														</select>
													</div>
												</div>									
												
												<br/>
												<div class="form-group">
													<label for="lg_desc" class="col-sm-3 control-label"><span class="text-danger">*</span> Link za:</label>
													<div class="col-sm-9">
														<select class="selectpicker" data-live-search="true" id="lg_types" name="lg_types[]" multiple required>
															<?php getGroupList(); ?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label for="lg_link_prijave" class="col-sm-3 control-label">Link prijave:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="lg_link_prijave" id="lg_link_prijave" placeholder="www.linkprijave.com" >
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>
												
												<div class="form-group">
													<label for="idk_urlimg_prijave" class="col-sm-3 control-label">Thumbnail slike naloga:</label>
													<div class="col-sm-9">
														<div class="fileinput fileinput-new" data-provides="fileinput">
															<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;"></div>
															<div>
																<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografiju</span><span class="fileinput-exists">Promijeni</span><input type="file" name="idk_urlimg_prijave" id="idk_urlimg_prijave"></span>
																<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
																<script>
																	$(function (){
																		$('#idk_urlimg_prijave').change(function (){

																			var ext = $('#idk_urlimg_prijave').val().split('.').pop().toLowerCase();

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

												<div class="form-group">
													<label class="col-sm-3"></label>
													<div class="col-sm-9">
														<div id="idk_alert_size" class="hidden">
															<div class="alert material-alert material-alert_danger">Greška: Fotografiju koju pokuštavate dodati je veća od dozvoljene veličine.</div>
														</div>
														<div id="idk_alert_ext" class="hidden">
															<div class="alert material-alert material-alert_danger">Greška: Format fotografije koju pokušavate dodati nije dozvoljen.</div>
														</div>
													</div>
												</div>	
												
												<div class="form-group">
													<label for="lg_language" class="col-sm-3 control-label"><span class="text-danger">*</span> Jezik forme za prijavu:</label>
													<div class="col-sm-9">
														<select class="selectpicker" id="lg_language" name="lg_language" required>
															<option value="0">Bosanski</option>
															<option value="3">Srpski</option>
															<option value="1">Njemački</option>
															<option value="2">Italijanski</option>
														</select>
													</div>
												</div>

												<div class="form-group">
													<label for="lg_naslov_prijave" class="col-sm-3 control-label">Naslov na prijavi:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="lg_naslov_prijave" id="lg_naslov_prijave" placeholder="Prijava" >
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="lg_tekst_prijave" class="col-sm-3 control-label">Tekst na prijavi:</label>
													<div class="col-sm-8">
														<div class="materail-input-block materail-input-block_success">
															<textarea class="form-control materail-input material-textarea" name="lg_tekst_prijave" id="lg_tekst_prijave"></textarea>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<br />
												<div class="form-group">
													<div class="col-sm-offset-2 col-sm-10 text-right">
														<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span>Generiši</span></button>
														<br /><small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
													</div>
												</div>
											</div>
											<div class="col-md-offset-1 col-md-5">
												<h5><b>Pitanja na prijavi</b></h5>
												<?php
													$stmt = $db->prepare("
														SELECT qff_id, qff_short_name, qff_question, qff_is_active,
															qff_default, qff_locked, qff_depends_on, qff_default_sort_order
														FROM idk_questions_for_form
														WHERE qff_is_active = 1
														ORDER BY qff_default_sort_order, qff_id
													");
													$stmt->execute();
													$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

													foreach ($questions as $q){
														$code   = $q['qff_short_name']; 
														$label  = $q['qff_question'];
														$locked = (int)$q['qff_locked'] === 1;
														$def    = (int)$q['qff_default'];   // 1=DA, 0=NE
														$dep    = $q['qff_depends_on'];     // references another qff_short_name or NULL
													  
														// re-fill from POST after validation error; otherwise use default
														$value  = isset($_POST['formq'][$code]) ? (int)$_POST['formq'][$code] : $def;
													  
														$id_da  = "form_{$code}_da";
														$id_ne  = "form_{$code}_ne";
														?>
														<div class="form-group col-xs-12 question-row"
															data-code="<?=htmlspecialchars($code)?>"
															<?= $dep ? 'data-depends-on="'.htmlspecialchars($dep).'"' : '' ?>
															style="display:flex; align-items:center;">
														<div class="col-xs-6" style="text-align:right;"><?=htmlspecialchars($label)?>:</div>

														<div class="col-xs-2 materail-input-block materail-input-block_success idk_radio_buttons">
															<label class="main-container__column material-radio-group material-radio-group_success" for="<?=$id_da?>">
															<input type="radio"
																	name="formq[<?=$code?>]"
																	id="<?=$id_da?>"
																	class="material-radiobox"
																	value="1"
																	<?= $value === 1 ? 'checked' : '' ?> />
															<span class="material-radio-group__element material-radio-group__check-radio"></span>
															<span class="material-radio-group__element material-radio-group__caption">DA</span>
															</label>
														</div>

														<div class="col-xs-2 materail-input-block materail-input-block_danger idk_radio_buttons">
															<label class="main-container__column material-radio-group material-radio-group_danger" for="<?=$id_ne?>">
															<input type="radio"
																	name="formq[<?=$code?>]"
																	id="<?=$id_ne?>"
																	class="material-radiobox"
																	value="0"
																	<?= $value === 0 ? 'checked' : '' ?>
																	<?= $locked ? 'disabled title="Obavezno polje"' : '' ?> />
															<span class="material-radio-group__element material-radio-group__check-radio"></span>
															<span class="material-radio-group__element material-radio-group__caption">NE</span>
															</label>
														</div>
														</div>
														<?php

													}
													
												?>
												
												<h4 style="margin-top:20px;"><b>Dodatna pitanja (spremaju se u bilješke): </b></h4>

												<?php
												$stmt = $db->prepare("
												SELECT dp_id, dp_tekst
												FROM idk_dodatna_pitanja
												ORDER BY dp_id DESC
												");
												$stmt->execute();
												$dodatna = $stmt->fetchAll(PDO::FETCH_ASSOC);

												if (!$dodatna) {
												echo '<div class="alert material-alert material-alert_info">Trenutno nema dodatnih pitanja. Klikni “Dodaj pitanje”.</div>';
												}

												foreach ($dodatna as $dp) {
												$dp_id = (int)$dp['dp_id'];
												$text  = $dp['dp_tekst'];

												// default NE (0) unless POST refill
												$value = isset($_POST['formdp'][$dp_id]) ? (int)$_POST['formdp'][$dp_id] : 0;

												$id_da = "dp_{$dp_id}_da";
												$id_ne = "dp_{$dp_id}_ne";
												?>
												<div class="form-group col-xs-12 dp-row" data-dp-id="<?=$dp_id?>" style="display:flex; align-items:center;">
													<div class="col-xs-6" style="text-align:right;"><?=htmlspecialchars($text)?>:</div>

													<div class="col-xs-2 materail-input-block materail-input-block_success idk_radio_buttons">
													<label class="main-container__column material-radio-group material-radio-group_success" for="<?=$id_da?>">
														<input type="radio"
															name="formdp[<?=$dp_id?>]"
															id="<?=$id_da?>"
															class="material-radiobox"
															value="1"
															<?= $value === 1 ? 'checked' : '' ?> />
														<span class="material-radio-group__element material-radio-group__check-radio"></span>
														<span class="material-radio-group__element material-radio-group__caption">DA</span>
													</label>
													</div>

													<div class="col-xs-2 materail-input-block materail-input-block_danger idk_radio_buttons">
													<label class="main-container__column material-radio-group material-radio-group_danger" for="<?=$id_ne?>">
														<input type="radio"
															name="formdp[<?=$dp_id?>]"
															id="<?=$id_ne?>"
															class="material-radiobox"
															value="0"
															<?= $value === 0 ? 'checked' : '' ?> />
														<span class="material-radio-group__element material-radio-group__check-radio"></span>
														<span class="material-radio-group__element material-radio-group__caption">NE</span>
													</label>
													</div>
												</div>
												<?php
												}
												?>

												<div class="form-group col-xs-12" style="margin-top:10px;">
													<button type="button" id="btn_add_dp"
														class="btn material-btn material-btn-icon-primary material-btn_primary">
														<i class="fa fa-plus" aria-hidden="true"></i> Dodaj pitanje
													</button>
												</div>

												<div id="dp_add_box" class="form-group col-xs-12 hidden" style="margin-top:10px;">
													<div class="col-xs-6 col-xs-offset-2">
														<input type="text" id="dp_new_text" class="form-control materail-input"
															placeholder="Unesi tekst pitanja..." />
													</div>
													<div class="col-xs-3">
														<button type="button" id="btn_save_dp"
														class="btn material-btn material-btn-icon-success material-btn_success">
														<i class="fa fa-check"></i> Dodaj
														</button>
													</div>
												</div>
												
												<script>
													function applyDependency(parentCode) {
														const parentVal = $('input[name="formq['+parentCode+']"]:checked').val();
														const children = $('.question-row[data-depends-on="'+parentCode+'"]');

														children.each(function() {
														const row = $(this);
														const da  = row.find('input[id$="_da"]');
														const ne  = row.find('input[id$="_ne"]');

														if (parentVal === '1') {
															// enable child radios; keep selection
															da.prop('disabled', false);
															ne.prop('disabled', false);
														} else {
															// force NE and lock DA
															ne.prop('checked', true);
															da.prop('checked', false).prop('disabled', true);
															ne.prop('disabled', false);
														}
														});
													}

													$(function() {
														// find all parent codes referenced by any child
														const parentCodes = new Set();
														$('.question-row[data-depends-on]').each(function(){
														parentCodes.add($(this).data('depends-on'));
														});

														parentCodes.forEach(function(code){
														$('input[name="formq['+code+']"]').on('change', function(){
															applyDependency(code);
														});
														applyDependency(code); // initial pass on load
														});
													});
													
												</script>
												<script>
													$(function () {

													$('#btn_add_dp').on('click', function () {
														$('#dp_add_box').removeClass('hidden');
														$('#dp_new_text').focus();
													});

													$('#btn_save_dp').on('click', function () {
														const txt = $('#dp_new_text').val().trim();
														if (!txt) return;

														$.ajax({
														url: '<?php getSiteURL(); ?>ajax_data.php?page=create_dodatno_pitanje',
														method: 'POST',
														dataType: 'json',
														data: { dp_tekst: txt },
														success: function (res) {
															if (!res || !res.ok) {
															alert(res && res.msg ? res.msg : 'Greška pri dodavanju pitanja.');
															return;
															}

															// append new row (DA pre-selected)
															const dp_id = res.dp_id;
															const safeText = $('<div>').text(res.dp_tekst).html();

															const html = `
															<div class="form-group col-xs-12 dp-row" data-dp-id="${dp_id}" style="display:flex; align-items:center;">
																<div class="col-xs-6" style="text-align:right;">${safeText}:</div>

																<div class="col-xs-2 materail-input-block materail-input-block_success idk_radio_buttons">
																<label class="main-container__column material-radio-group material-radio-group_success" for="dp_${dp_id}_da">
																	<input type="radio" name="formdp[${dp_id}]" id="dp_${dp_id}_da" class="material-radiobox" value="1" checked />
																	<span class="material-radio-group__element material-radio-group__check-radio"></span>
																	<span class="material-radio-group__element material-radio-group__caption">DA</span>
																</label>
																</div>

																<div class="col-xs-2 materail-input-block materail-input-block_danger idk_radio_buttons">
																<label class="main-container__column material-radio-group material-radio-group_danger" for="dp_${dp_id}_ne">
																	<input type="radio" name="formdp[${dp_id}]" id="dp_${dp_id}_ne" class="material-radiobox" value="0" />
																	<span class="material-radio-group__element material-radio-group__check-radio"></span>
																	<span class="material-radio-group__element material-radio-group__caption">NE</span>
																</label>
																</div>
															</div>
															`;

															// Insert new row ABOVE the add button (so it sits with other questions)
															$('#btn_add_dp').closest('.form-group').before(html);

															// reset input (keep box open so user can add again quickly)
															$('#dp_new_text').val('').focus();
														},
														error: function () {
															alert('Greška: AJAX nije uspio.');
														}
														});
													});

													// Enter key submits
													$('#dp_new_text').on('keypress', function(e){
														if (e.which === 13) $('#btn_save_dp').click();
													});

													});
												</script>
												
												
												
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
					if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status))){

						$lg_id = $_GET['id'];

						$query = $db->prepare("
										SELECT lg_id, lg_link_prijave, idk_urlimg_prijave, lg_url, lg_desc, lg_nalogid, lg_language, lg_questions_added, lg_naslov_na_formi, lg_tekst_na_formi
										FROM idk_link_generator
										WHERE lg_id = :lg_id
										");

						$query->execute(array(
							":lg_id" => $lg_id
						));
						$row = $query->fetch();

						$lg_id = $row['lg_id'];
						$lg_link_prijave = $row['lg_link_prijave'];
						$idk_urlimg_prijave = $row['idk_urlimg_prijave'];
						$lg_url = $row['lg_url'];
						$lg_desc = $row['lg_desc'];
						$lg_nalogid = $row['lg_nalogid'];
						$lg_language = $row['lg_language'];
						$lg_naslov_na_formi = $row['lg_naslov_na_formi'];
						$lg_tekst_na_formi = $row['lg_tekst_na_formi'];
						$lg_questions_added = $row['lg_questions_added'];
						?>
						<div class="row">
							<div class="col-xs-8">
								<h1><i class="fa fa-retweet idk_color_green" aria-hidden="true"></i> Uredi link</h1>
							</div>
							<div class="col-xs-4 text-right idk_margin_top10">
								<a href="<?php getSiteURL(); ?>link_generator?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
							</div>
							<div class="col-xs-12">
								<hr />
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="content_box">
									<div class="row">
										<form action="<?php getSiteURL(); ?>do.php?form=generate_new_link_edit" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
											<div class="col-md-offset-1 col-md-5">
												<input type="hidden" name="lg_id" value="<?php echo $lg_id ?>">
												<div class="form-group">
													<label for="lg_url" class="col-sm-3 control-label"><span class="text-danger">*</span> Website:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="lg_url" id="lg_url" value="<?php echo $lg_url; ?>" required>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>
												<div class="form-group">
													<label for="lg_desc" class="col-sm-3 control-label"><span class="text-danger">*</span> Opis:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="lg_desc" id="lg_desc" value="<?php echo htmlspecialchars($lg_desc); ?>" required>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>
												<div class="form-group">
													<label for="lg_nalogid" class="col-sm-3 control-label"><span class="text-danger">*</span> Nalog:</label>
													<div class="col-sm-9">
														<select class="selectpicker" id="lg_nalogid" data-live-search="true" name="lg_nalogid" required>
															<option value="0">Bez naloga</option>
															<?php getNalogListEdit($lg_nalogid); ?>
														</select>
													</div>
												</div>										
												<div class="form-group">
													<label for="lg_desc" class="col-sm-3 control-label"><span class="text-danger">*</span> Link za:</label>
													<div class="col-sm-9">
														<select class="selectpicker" data-live-search="true" id="lg_types" name="lg_types[]" multiple required>
														<?php
															$query = $db->prepare("
																			SELECT kg_id, kg_title, kg_date
																			FROM idk_kandidati_grupe
																			WHERE kg_status = 0
																			ORDER BY kg_id DESC
																			");

															$query->execute();
															$i=1;
															while($row = $query->fetch()){

																$kg_id = $row['kg_id'];
																$kg_title = $row['kg_title'];
																$kg_date = $row['kg_date'];
																$kg_date_f = date('d.m.Y H:i', strtotime($kg_date));


															$query_selected = $db->prepare("
																			SELECT lr_groupid
																			FROM idk_link_generator_rel
																			WHERE lr_lgid = :lr_lgid
																			");

															$query_selected->execute(array(
																":lr_lgid" => $lg_id
															));

															$array = array();
															while($selectedd = $query_selected->fetch()){

																$lr_groupid = $selectedd['lr_groupid'];
																$array[] = $lr_groupid;
															}


														?>
														<option value="<?php echo $kg_id; ?>" <?php if(in_array($kg_id, $array)){echo "selected";} ?>><?php echo $kg_title; ?></option>
														<?php } ?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label for="lg_link_prijave" class="col-sm-3 control-label">Link prijave:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="lg_link_prijave" id="lg_link_prijave" value="<?php echo $lg_link_prijave; ?>" >
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>
												
												<div class="form-group">
													<label for="idk_urlimg_prijave" class="col-sm-3 control-label">Thumbnail slike naloga:</label>
													<div class="col-sm-2">
														<div class="fileinput fileinput-new" data-provides="fileinput">
															<img class="idk_logo1 img-responsive" title="Trenutna slika thumbnail-a!" style="width: 160px; height: 160px;" src="<?php getSiteUrl(); ?>files/partner_nalogs/<?php echo $idk_urlimg_prijave; ?>">
														</div>
													</div>
													<div class="col-sm-7">
														<div class="fileinput fileinput-new" data-provides="fileinput">
															<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;"></div>									
															<div>
																<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografiju</span><span class="fileinput-exists">Promijeni</span><input type="file" name="idk_urlimg_prijave" id="idk_urlimg_prijave"></span>
																<input type="hidden" name="idk_urlimg_prijave_exists" value="<?php echo $idk_urlimg_prijave; ?>" id="idk_urlimg_prijave_exists">
																<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
																<script>
																	$(function (){
																		$('#idk_urlimg_prijave').change(function (){

																			var ext = $('#idk_urlimg_prijave').val().split('.').pop().toLowerCase();

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

												<div class="form-group">
													<label class="col-sm-3"></label>
													<div class="col-sm-9">
														<div id="idk_alert_size" class="hidden">
															<div class="alert material-alert material-alert_danger">Greška: Fotografiju koju pokuštavate dodati je veća od dozvoljene veličine.</div>
														</div>
														<div id="idk_alert_ext" class="hidden">
															<div class="alert material-alert material-alert_danger">Greška: Format fotografije koju pokušavate dodati nije dozvoljen.</div>
														</div>
													</div>
												</div>		
												
												<div class="form-group">
													<label for="lg_language" class="col-sm-3 control-label"><span class="text-danger">*</span> Jezik forme za prijavu:</label>
													<div class="col-sm-9">
														<select class="selectpicker" id="lg_language" name="lg_language" required>
															<option value="0" <?php if($lg_language == "bs") echo "selected"; ?> >Bosanski</option>
															<option value="3" <?php if($lg_language == "sr") echo "selected"; ?> >Srpski</option>
															<option value="1" <?php if($lg_language == "de") echo "selected"; ?> >Njemački</option>
															<option value="2" <?php if($lg_language == "it") echo "selected"; ?> >Italijanski</option>
														</select>
													</div>
												</div>

												<div class="form-group">
													<label for="lg_naslov_prijave" class="col-sm-3 control-label">Naslov na prijavi:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input"
															type="text"
															name="lg_naslov_prijave"
															id="lg_naslov_prijave"
															placeholder="Prijava"
															value="<?= htmlspecialchars($lg_naslov_na_formi ?? '', ENT_QUOTES, 'UTF-8'); ?>">
														<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="lg_tekst_prijave" class="col-sm-3 control-label">Tekst na prijavi:</label>
													<div class="col-sm-8">
														<div class="materail-input-block materail-input-block_success">
														<textarea class="form-control materail-input material-textarea"
															name="lg_tekst_prijave"
															id="lg_tekst_prijave"><?= htmlspecialchars($lg_tekst_na_formi ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
														<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<br />
												<div class="form-group">
													<div class="col-sm-offset-2 col-sm-10 text-right">
														<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span>Uredi</span></button>
														<br /><small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
													</div>
												</div>
											</div>
											<div class="col-md-offset-1 col-md-5">
												<h5><b>Pitanja na prijavi</b></h5>
												<?php 
													$stmt = $db->prepare("
														SELECT lq_question_id, lq_is_required
														FROM idk_link_questions
														WHERE lq_link_id = ?
													");
													$stmt->execute([$lg_id]);
													$link_questions = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); 
													// returns array[question_id] => is_required

													$stmt = $db->prepare("
														SELECT qff_id, qff_short_name, qff_question, qff_is_active,
															qff_default, qff_locked, qff_depends_on, qff_default_sort_order
														FROM idk_questions_for_form
														WHERE qff_is_active = 1
														ORDER BY qff_default_sort_order, qff_id
													");
													$stmt->execute();
													$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

													foreach ($questions as $q){
														$code   = $q['qff_short_name']; 
														$qid	= $q['qff_id'];
														$label  = $q['qff_question'];
														$locked = (int)$q['qff_locked'] === 1;
														$dep    = $q['qff_depends_on'];     // references another qff_short_name or NULL
													  
														// re-fill from POST after validation error; otherwise use default
														$value  = isset($link_questions[$qid]) ? (int)$link_questions[$qid] : (int)$q['qff_default'];
													  
														$id_da  = "form_{$code}_da";
														$id_ne  = "form_{$code}_ne";

														?>
														<div class="form-group col-xs-12 question-row"
															data-code="<?=htmlspecialchars($code)?>"
															<?= $dep ? 'data-depends-on="'.htmlspecialchars($dep).'"' : '' ?>
															style="display:flex; align-items:center;">
														<div class="col-xs-6" style="text-align:right;"><?=htmlspecialchars($label)?>:</div>

														<div class="col-xs-2 materail-input-block materail-input-block_success idk_radio_buttons">
															<label class="main-container__column material-radio-group material-radio-group_success" for="<?=$id_da?>">
															<input type="radio"
																	name="formq[<?=$code?>]"
																	id="<?=$id_da?>"
																	class="material-radiobox"
																	value="1"
																	<?= $value === 1 ? 'checked' : '' ?> />
															<span class="material-radio-group__element material-radio-group__check-radio"></span>
															<span class="material-radio-group__element material-radio-group__caption">DA</span>
															</label>
														</div>

														<div class="col-xs-2 materail-input-block materail-input-block_danger idk_radio_buttons">
															<label class="main-container__column material-radio-group material-radio-group_danger" for="<?=$id_ne?>">
															<input type="radio"
																	name="formq[<?=$code?>]"
																	id="<?=$id_ne?>"
																	class="material-radiobox"
																	value="0"
																	<?= $value === 0 ? 'checked' : '' ?>
																	<?= $locked ? 'disabled title="Obavezno polje"' : '' ?> />
															<span class="material-radio-group__element material-radio-group__check-radio"></span>
															<span class="material-radio-group__element material-radio-group__caption">NE</span>
															</label>
														</div>
														</div>
														<?php

													}
												?>
												
												<h4 style="margin-top:20px;"><b>Dodatna pitanja (spremaju se u bilješke): </b></h4>

												<?php
												// EDIT MODE prefill
												$selectedDp = [];
												$stmtSel = $db->prepare("
													SELECT ldp_dp_id
													FROM idk_link_dodatna_pitanja
													WHERE ldp_link_id = ?
												");
												$stmtSel->execute([(int)$lg_id]);
												foreach ($stmtSel->fetchAll(PDO::FETCH_ASSOC) as $r) {
													$selectedDp[(int)$r['ldp_dp_id']] = 1;
												}

												// Load all additional questions
												$stmt = $db->prepare("
													SELECT dp_id, dp_tekst
													FROM idk_dodatna_pitanja
													ORDER BY dp_id DESC
												");
												$stmt->execute();
												$dodatna = $stmt->fetchAll(PDO::FETCH_ASSOC);

												if (!$dodatna) {
													echo '<div class="alert material-alert material-alert_info">Trenutno nema dodatnih pitanja. Klikni “Dodaj pitanje”.</div>';
												}

												foreach ($dodatna as $dp) {
													$dp_id = (int)$dp['dp_id'];
													$text  = $dp['dp_tekst'];

													// THIS IS THE IMPORTANT EDIT CHANGE:
													if (isset($_POST['formdp'][$dp_id])) {
														$value = (int)$_POST['formdp'][$dp_id];
													} else {
														$value = isset($selectedDp[$dp_id]) ? 1 : 0;
													}

													$id_da = "dp_{$dp_id}_da";
													$id_ne = "dp_{$dp_id}_ne";
													?>
													<div class="form-group col-xs-12 dp-row" data-dp-id="<?=$dp_id?>" style="display:flex; align-items:center;">
														<div class="col-xs-6" style="text-align:right;"><?=htmlspecialchars($text)?>:</div>

														<div class="col-xs-2 materail-input-block materail-input-block_success idk_radio_buttons">
														<label class="main-container__column material-radio-group material-radio-group_success" for="<?=$id_da?>">
															<input type="radio"
																name="formdp[<?=$dp_id?>]"
																id="<?=$id_da?>"
																class="material-radiobox"
																value="1"
																<?= $value === 1 ? 'checked' : '' ?> />
															<span class="material-radio-group__element material-radio-group__check-radio"></span>
															<span class="material-radio-group__element material-radio-group__caption">DA</span>
														</label>
														</div>

														<div class="col-xs-2 materail-input-block materail-input-block_danger idk_radio_buttons">
														<label class="main-container__column material-radio-group material-radio-group_danger" for="<?=$id_ne?>">
															<input type="radio"
																name="formdp[<?=$dp_id?>]"
																id="<?=$id_ne?>"
																class="material-radiobox"
																value="0"
																<?= $value === 0 ? 'checked' : '' ?> />
															<span class="material-radio-group__element material-radio-group__check-radio"></span>
															<span class="material-radio-group__element material-radio-group__caption">NE</span>
														</label>
														</div>
													</div>
													<?php
												}
												?>

												<div class="form-group col-xs-12" style="margin-top:10px;">
													<button type="button" id="btn_add_dp"
														class="btn material-btn material-btn-icon-primary material-btn_primary">
														<i class="fa fa-plus" aria-hidden="true"></i> Dodaj pitanje
													</button>
												</div>

												<div id="dp_add_box" class="form-group col-xs-12 hidden" style="margin-top:10px;">
													<div class="col-xs-6 col-xs-offset-2">
														<input type="text" id="dp_new_text" class="form-control materail-input"
														placeholder="Unesi tekst pitanja..." />
													</div>
													<div class="col-xs-3">
														<button type="button" id="btn_save_dp"
															class="btn material-btn material-btn-icon-success material-btn_success">
															<i class="fa fa-check"></i> Dodaj
														</button>
													</div>
												</div>

												<script>
													$(function() {
														const parentCodes = new Set();
														$('.question-row[data-depends-on]').each(function(){
															parentCodes.add($(this).data('depends-on'));
														});

														function applyDependency(parentCode) {
															const parentVal = $('input[name="formq['+parentCode+']"]:checked').val();
															const children = $('.question-row[data-depends-on="'+parentCode+'"]');

															children.each(function() {
																const row = $(this);
																const da  = row.find('input[id$="_da"]');
																const ne  = row.find('input[id$="_ne"]');

																if (parentVal === '1') {
																	da.prop('disabled', false);
																	ne.prop('disabled', false);
																} else {
																	ne.prop('checked', true);
																	da.prop('checked', false).prop('disabled', true);
																	ne.prop('disabled', false);
																}
															});
														}

														parentCodes.forEach(function(code){
															$('input[name="formq['+code+']"]').on('change', function(){
																applyDependency(code);
															});
															applyDependency(code); // initial pass on load
														});
													});
												</script>
												<script>
													$(function () {

														$('#btn_add_dp').on('click', function () {
															$('#dp_add_box').removeClass('hidden');
															$('#dp_new_text').focus();
														});

														$('#btn_save_dp').on('click', function () {
															const txt = $('#dp_new_text').val().trim();
															if (!txt) return;

															$.ajax({
															url: '<?php getSiteURL(); ?>ajax_data.php?page=create_dodatno_pitanje',
															method: 'POST',
															dataType: 'json',
															data: { dp_tekst: txt },
															success: function (res) {
																if (!res || !res.ok) {
																alert(res && res.msg ? res.msg : 'Greška pri dodavanju pitanja.');
																return;
																}

																const dp_id = res.dp_id;
																const safeText = $('<div>').text(res.dp_tekst).html();

																const html = `
																<div class="form-group col-xs-12 dp-row" data-dp-id="${dp_id}" style="display:flex; align-items:center;">
																	<div class="col-xs-6" style="text-align:right;">${safeText}:</div>

																	<div class="col-xs-2 materail-input-block materail-input-block_success idk_radio_buttons">
																	<label class="main-container__column material-radio-group material-radio-group_success" for="dp_${dp_id}_da">
																		<input type="radio" name="formdp[${dp_id}]" id="dp_${dp_id}_da" class="material-radiobox" value="1" checked />
																		<span class="material-radio-group__element material-radio-group__check-radio"></span>
																		<span class="material-radio-group__element material-radio-group__caption">DA</span>
																	</label>
																	</div>

																	<div class="col-xs-2 materail-input-block materail-input-block_danger idk_radio_buttons">
																	<label class="main-container__column material-radio-group material-radio-group_danger" for="dp_${dp_id}_ne">
																		<input type="radio" name="formdp[${dp_id}]" id="dp_${dp_id}_ne" class="material-radiobox" value="0" />
																		<span class="material-radio-group__element material-radio-group__check-radio"></span>
																		<span class="material-radio-group__element material-radio-group__caption">NE</span>
																	</label>
																	</div>
																</div>
																`;

																$('#btn_add_dp').closest('.form-group').before(html);

																$('#dp_new_text').val('').focus();
															},
															error: function () {
																alert('Greška: AJAX nije uspio.');
															}
															});
														});

														$('#dp_new_text').on('keypress', function(e){
															if (e.which === 13) $('#btn_save_dp').click();
														});

													});
													</script>

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
				
				case "statistike":
					if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status))){

						$lg_id = $_GET['id'];

						$query = $db->prepare("
										SELECT lg_id, lg_link_prijave, lg_url, lg_desc, lg_nalogid, lg_language, lg_troskovi
										FROM idk_link_generator
										WHERE lg_id = :lg_id
										");

						$query->execute(array(
							":lg_id" => $lg_id
						));
						$row = $query->fetch();

						$lg_id = $row['lg_id'];
						$lg_link_prijave = $row['lg_link_prijave'];
						$lg_url = $row['lg_url'];
						$lg_desc = $row['lg_desc'];
						$lg_nalogid = $row['lg_nalogid'];
						$lg_language = $row['lg_language'];
						$lg_troskovi = $row['lg_troskovi'];



		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-retweet idk_color_green" aria-hidden="true"></i> Statistike za <?php echo $lg_url; ?></h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>link_generator?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<script type="text/javascript">
								$(document).ready(function() {
									var table = $('#link_stats_candidates').DataTable({
										responsive: true,
										"order": [[ 1, "desc" ]],
										 "bAutoWidth": false,
										/*'columnDefs': [
											{
											'targets': 0,
											'checkboxes': {
												'selectRow': true
											}
											}
										],
										'select': {
											'style': 'multi'
										},	*/										 
										"aoColumns": [
												//{ "width": "5%", "bSortable": false },
												// { "width": "5%" },
												{ "width": "5%", "bSortable": false },
												{ "width": "15%" },
												{ "width": "34%" },
												{ "width": "10%" },
												{ "width": "10%" },
												{ "width": "10%" },
												{ "width": "9%" },
												{ "width": "7%", "bSortable": false }
											]
									});
								});
							</script>
							<table id="link_stats_candidates" class="display" cellspacing="0" width="100%">
								<thead>
									<tr>
										<th></th>
										<th>Ime i prezime</th>
										<th>Projekat</th>
										<th>Status prijave</th>
										<th class="text-center">Datum prijave</th>
										<th class="text-center">Provizija</th>
										<th class="text-center">Status obrade</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
								<?php 
									$ukupna_suma = 0;
									$query = $db->prepare("
													SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_status, kandidat_status_prijave, kandidat_slika, kandidat_email, kandidat_datetime, kandidat_visitedurl, kandidat_prijava_na, kandidat_group
													FROM idk_kandidati
													WHERE kandidat_visitedurl = $lg_id
													ORDER BY kandidat_id ASC
													");

									$query->execute();
									while($row = $query->fetch()){

										$kandidat_id = $row['kandidat_id'];
										$kandidat_ime = $row['kandidat_ime'];
										$kandidat_prezime = $row['kandidat_prezime'];
										$kandidat_status = $row['kandidat_status'];
										$kandidat_slika = $row['kandidat_slika'];
										$kandidat_group = $row['kandidat_group'];
										$kandidat_email = $row['kandidat_email'];
										$kandidat_datetime = $row['kandidat_datetime'];
										$kandidat_visitedurl = $row['kandidat_visitedurl'];
										$kandidat_prijava_na = $row['kandidat_prijava_na'];
										$kandidat_status_prijave = $row['kandidat_status_prijave'];
										
										$kandidat_datetime_f = date("Y-m-d", strtotime($kandidat_datetime));
										
										if($row['kandidat_slika'] == "none"){
											$kandidat_slika = "none.jpg";
										}else{
											$kandidat_slika = $row['kandidat_slika'];
										}
										
										if($row['kandidat_status'] == 0){
											$kandidat_status = '<span class="label label-warning material-label material-label_warning material-label_xs main-container__column">Na provjeri</span>';
										}elseif($row['kandidat_status'] == 1){
											$kandidat_status = '<span class="label label-primary material-label material-label_primary material-label_xs main-container__column">U obradi</span>';
										}elseif($row['kandidat_status'] == 2){
											$kandidat_status = '<span class="label label-success material-label material-label_success material-label_xs main-container__column">Obrađen</span>';
										}elseif($row['kandidat_status'] == 3){
											$kandidat_status = '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Arhiviran</span>';
										}
										if($row['kandidat_status_prijave'] == 0){
											$kandidat_status_prijave = '<span class="label label-secondary material-label material-label_secondary material-label_xs main-container__column">Nedefinisan</span>';
										}elseif($row['kandidat_status_prijave'] == 1){
											$kandidat_status_prijave = '<span class="label label-warning material-label material-label_warning material-label_xs main-container__column">Slobodan</span>';
										}elseif($row['kandidat_status_prijave'] == 2){
											$kandidat_status_prijave = '<span class="label label-info material-label material-label_info material-label_xs main-container__column">U projektu</span>';
										}elseif($row['kandidat_status_prijave'] == 3){
											$kandidat_status_prijave = '<span class="label label-primary material-label material-label_primary material-label_xs main-container__column">Casting</span>';
										}elseif($row['kandidat_status_prijave'] == 4){
											$kandidat_status_prijave = '<span class="label label-success material-label material-label_success material-label_xs main-container__column">Zaposlen</span>';
										}elseif($row['kandidat_status_prijave'] == 5){
											$kandidat_status_prijave = '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Odbijen</span>';
										}
										
										$query_group = $db->prepare("
														SELECT kg_id, kg_title, kg_date
														FROM idk_kandidati_grupe
														WHERE kg_id = :kg_id
														ORDER BY kg_id ASC
														");

										$query_group->execute(array(
											":kg_id" => $kandidat_group
										));

										$group_title = $query_group->fetch();
										$kg_title = $group_title['kg_title'];
										
										$query_project = $db->prepare("
																	SELECT pk_projectid, pk_kandidatid, proj.project_name, proj.project_datetime, proj.project_status, proj.project_nalogid
																	FROM idk_project_kandidati
																	INNER JOIN idk_projects proj ON pk_projectid = proj.project_id
																	WHERE pk_kandidatid = :pk_kandidatid");
										$query_project->execute(array(
											'pk_kandidatid' => $kandidat_id));
										
										$row_projects = $query_project->fetch();
										$project_name = $row_projects['project_name'];
										$project_nalogid = $row_projects['project_nalogid'];
										if($project_name == null)
											$project_name = "nema";
										
										$query_nalog = $db->prepare("
																	SELECT nalog_naziv
																	FROM idk_nalozi
																	WHERE nalog_id = :nalog_id");
										$query_nalog->execute(array(
											'nalog_id' => $project_nalogid));
										
										$row_nalog = $query_nalog->fetch();
										$nalog_name = $row_nalog['nalog_naziv'];
										
										$query_kan_fin = $db->prepare("
																	SELECT nalog_id
																	FROM idk_kandidat_financije
																	WHERE kandidat_id = :kandidat_id AND kf_status != :kf_status");
										$query_kan_fin->execute(array(
											'kandidat_id' => $kandidat_id,
											'kf_status' => 2
											));
										
										$row_kan_fin = $query_kan_fin->fetch();
										if($query_kan_fin->rowCount() > 0){
											$nalog_kan_fin = $row_kan_fin['nalog_id'];
											$query_nalog_fin = $db->prepare("
																		SELECT nalog_financije, nalog_provizija
																		FROM idk_nalozi
																		WHERE nalog_id = :nalog_id");
											$query_nalog_fin->execute(array(
												'nalog_id' => $nalog_kan_fin));
											
											$row_nalog_fin = $query_nalog_fin->fetch();
											$nalog_financije = $row_nalog_fin['nalog_financije'];
											if($nalog_financije == 1)
												$nalog_provizija = $row_nalog_fin['nalog_provizija'];
											else
												$nalog_provizija = 0;
										}
										else
											$nalog_provizija = 0;
										
										$ukupna_suma += $nalog_provizija;
										
										?>
										<tr>
											<td class="text-center"> <?php echo $kandidat_id; ?> </td>											
											<td><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><?php echo $kandidat_ime; ?> <?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo $kandidat_prezime; ?><?php } ?></a></td>
											<td><?php echo $project_name; ?></td>
											<td><?php echo $kandidat_status_prijave; ?></td>
											<td class="text-center"><?php echo $kandidat_datetime_f; ?></td>
											<td class="text-center"><?php echo $nalog_provizija; ?></td>
											<td class="text-center"><?php echo $kandidat_status; ?></td>
											<td class="text-center">
												<div class="btn-group material-btn-group">
													<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
													<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
														<li><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
														<?php if(getEmployeeStatus() == 1){ ?>
														<li><a href="#" class="material-dropdown-menu__link cvshow" data="<?php getSiteURL(); ?>cv?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>cv2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#cvModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> CV</a></li>
														<?php }else{} ?>

														<li><a href="#" class="material-dropdown-menu__link profilshow" data="<?php getSiteURL(); ?>profil?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>profil2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#profilModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Profil</a></li>

														<?php if(getEmployeeStatus() == 1){ ?>
														<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>kandidati?page=archive&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
														<?php }else{} ?>

													</ul>
												</div>
											</td>
										</tr>
										<?php
									}
								?>
								</tbody>
							</table>
							<?php $balans = $ukupna_suma - $lg_troskovi; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12"  style = "margin-top: 10px;">
					<div class="content_box" style = "min-height: 1px;">
						<div class="row">
							<div class="col-md-4">
								<div class="content_box" style = "min-height: 1px;">
									<div class = "kartica_statistike text-center angry-animate" style = "background: linear-gradient(180deg, rgba(104,195,104,1) 0%, rgba(104,195,104,0.9) 35%, rgba(104,195,104,0.7) 100%);">
										<div class = "kartica_statistike_header">
											<p>
												Provizija
											</p>
										</div>
										<div class = "kartica_statistike_body">
											<p>
												<?php echo number_format($ukupna_suma, 2, ',', ' '); ?> KM
											</p>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="content_box" style = "min-height: 1px;">
									<div class = "kartica_statistike text-center angry-animate" style = "background: linear-gradient(180deg, rgba(243,65,60,1) 0%, rgba(243,65,60,0.9) 35%, rgba(243,65,60,0.7) 100%);">
										<div class = "kartica_statistike_header">
											<p>
												Troškovi
											</p>
										</div>
										<div class = "kartica_statistike_body">
											<p>
												<?php echo number_format($lg_troskovi, 2, ',', ' '); ?> KM
											</p>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="content_box" style = "min-height: 1px;">
									<div class = "kartica_statistike text-center angry-animate" style = "background: linear-gradient(180deg, rgba(64,146,217,1) 0%, rgba(64,146,217,0.9) 35%, rgba(64,146,217,0.7) 100%);">
										<div class = "kartica_statistike_header">
											<p>
												Balans
											</p>
										</div>
										<div class = "kartica_statistike_body">
											<p>
												<?php echo number_format($balans, 2, ',', ' '); ?> KM
											</p>
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

				case "show_list":
					if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status)) OR (in_array("17", $employee_status))){

					$kandidat_visitedurl = $_GET['id'];

					//$query = $db->prepare("
					//				SELECT kandidat_id, kandidat_ime, kandidat_prezime, kandidat_spol, kandidat_jmbg, kandidat_status, kandidat_slika, kandidat_email, kandidat_datetime, kandidat_visitedurl, kandidat_prijava_na, kandidat_group
					//				FROM idk_kandidati
					//				$upit $uslov_saradnik $uslov_pretrage_vozacka $uslov_pretrage_godine  $uslov_pretrage_statusi $uslov_pretrage_drzavljanstvo $and kandidat_visitedurl = $kandidat_visitedurl
					//				ORDER BY kandidat_id ASC
					//				");
                    //
					//$query->execute();
					//
					//
					//$row = $query->fetch());
                    //
					//	$kandidat_id = $row['kandidat_id'];

					if(isset($_COOKIE['archive_status'])){
						$archive_status = 1;
					}else{
						$archive_status = 0;
					}
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-user idk_color_green" aria-hidden="true"></i> Kandidati</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
				</div>
				<div class="col-xs-12 text-right">
				<!--
					<div id="canvas-holder" style="width:100px">
						<canvas id="chart-area2"></canvas>
					</div>
				-->
					<hr />
					<button type="submit" form="formForExport" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-file-excel-o" aria-hidden="true"></i> <span>Export selektovanog</span></button>
					<button data-toggle="modal" data-target="#filterKandidati" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-search" aria-hidden="true"></i> <span>FILTER</span></button>
					<?php if($archive_status == 0){ ?>
					<?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?>


					<a href="<?php getSiteURL(); ?>do.php?form=show_candidate_archive&enable=1">
						<button class="btn material-btn material-btn-icon-success material-btn main-container__column material-btn-icon-responsive"><i class="fa fa-times" aria-hidden="true"></i> ARHIVA</button>
					</a>
					<?php } ?>
					<?php }else{ ?>
					<?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?>

					<a href="<?php getSiteURL(); ?>do.php?form=show_candidate_archive&enable=0">
						<button class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-check" aria-hidden="true"></i> ARHIVA</button>
					</a>
					<?php } ?>
					<?php } ?>

					<hr />
				</div>
				<!-- Modal filter -->
				<div class="modal material-modal material-modal_success fade" id="filterKandidati">
					<div class="modal-dialog">
						<div class="modal-content material-modal__content">
							<div class="modal-header material-modal__header">
								<button class="close material-modal__close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title material-modal__title">Filter kandidata</h4>
							</div>
							<div class="modal-body material-modal__body">
								<form action="<?php getSiteURL(); ?>link_generator.php?page=show_list" method="get" target="_blank" http-equiv="Content-type" enctype="multipart/form-data"; charset=utf-8" class="form-horizontal">
								<input type="hidden" name="page" value="=show_list" >
								<input type="hidden" name="search" value="yes" >
								<input type="hidden" name="id" value="<?php echo $kandidat_visitedurl; ?>" >
								<div class="form-group">
									<label for="kki_naziv_web" class="col-sm-4 control-label">Starost:</label>
									<div class="col-sm-8">
										<div class="row">
											<div class="col-sm-4">
												<div class="materail-input-block materail-input-block_success">
													<input class="form-control materail-input" type="number" name="starost_od" id="starost_od" placeholder="od" value="16">
													<span class="materail-input-block__line"></span>
												</div>
											</div>
											<div class="col-sm-4">
												<div class="materail-input-block materail-input-block_success">
													<input class="form-control materail-input" type="number" name="starost_do" id="starost_do" placeholder="do" value="80">
													<span class="materail-input-block__line"></span>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="form-group">
									<label for="kki_naziv_web" class="col-sm-4 control-label">Vozačka dozvola:</label>
									<div class="col-sm-8">
										<div class="main-container__column materail-switch materail-switch_primary">
											<input class="materail-switch__element" type="checkbox" id="switch_input1" name="vozacka_dozvola" value="DA">
											<label class="materail-switch__label" for="switch_input1"></label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="kki_naziv_web" class="col-sm-4 control-label">Radno iskustvo:</label>
									<div class="col-sm-8">
										<div class="main-container__column materail-switch materail-switch_primary">
											<input class="materail-switch__element" type="checkbox" id="switch_input2" name="radno_iskustvo" value="DA">
											<label class="materail-switch__label" for="switch_input2"></label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="kki_naziv_web" class="col-sm-4 control-label">Znanje njemačkog:</label>
									<div class="col-sm-5">
                                        <select class="selectpicker" id="kj_znanje_njemacki" name="kj_znanje_njemacki">
    										<option value="Bezznanja" selected>Bez znanja</option>
    										<option value="A1">A1</option>
    										<option value="A2">A2</option>
    										<option value="B1">B1</option>
    										<option value="B2">B2</option>
    										<option value="C1">C1</option>
    										<option value="C2">C2</option>
    									</select>
									</div>
								</div>
								<div class="form-group">
									<label for="filter_grupe" class="col-sm-4 control-label">Grupe:</label>
									<div class="col-sm-5">
                                        <select class="selectpicker" id="filter_grupe" name="filter_grupe[]" multiple>
    										<?php getGroupList(); ?>
    									</select>
									</div>
								</div>
								<div class="form-group">
									<label for="filter_status" class="col-sm-4 control-label">Status:</label>
									<div class="col-sm-5">
                                        <select class="selectpicker" id="filter_status" name="filter_status[]" multiple>
    										<?php getStatusList(); ?>
    									</select>
									</div>
								</div>
								<div class="form-group">
									<label for="filter_drzavljanstvo" class="col-sm-4 control-label">Državljanstvo:</label>
									<div class="col-sm-5">
                                        <select class="selectpicker" id="filter_drzavljanstvo" name="filter_drzavljanstvo[]" multiple>
    										<option value="EU">EU</option>
    										<option value="NON-EU">NON-EU</option>
    									</select>
									</div>
								</div>

							</div>
							<div class="modal-footer material-modal__footer">
								<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
								<a id="archive_link" href=""><button class="btn btn-primary material-btn material-btn_success">TRAŽI</button></a>

								</form>
							</div>
						</div>
					</div>
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
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novog zaposlenika.</div>';
									}elseif($mess == 2){
										echo '<div class="alert material-alert material-alert_danger">Greška: Korisnik kojeg pokuštavate dodati već postoji u bazi podataka.</div>';
									}elseif($mess == 3){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili profil zaposlenika.</div>';
									}elseif($mess == 4){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali kandidata.</div>';
									}
								?>
								<script type="text/javascript">
									$(document).ready(function() {
									//	$('#checkbox_check_all_offer').change(function() {
									//		if (this.checked) {
									//				$('input[name="izaberi_povrsinu[]').prop('checked', true);
									//		}else{
									//			$('input[name="izaberi_povrsinu[]').prop('checked', false);
									//		}		
									//	});	
										var table = $('#idk_table').DataTable({
											responsive: true,
											"order": [[ 1, "desc" ]],
											 "bAutoWidth": false,
											'columnDefs': [
												{
												'targets': 0,
												'checkboxes': {
													'selectRow': true
												}
												}
											],
											'select': {
												'style': 'multi'
											},											 
											"aoColumns": [
													{ "width": "5%", "bSortable": false },
													{ "width": "5%" },
													{ "width": "5%", "bSortable": false },
													{ "width": "20%" },
													{ "width": "13%" },
													{ "width": "11%" },
													{ "width": "15%" },
													{ "width": "10%" },
													{ "width": "9%" },
													{ "width": "7%", "bSortable": false }
												]
										});
										
										
										
										// Handle form submission event 
										$('#formForExport').on('submit', function(e){
											e.preventDefault();
											var form = this;
											
											var rows_selected = table.column(0).checkboxes.selected();
										
											// Iterate over all selected checkboxes
											$.each(rows_selected, function(index, rowId){
												// Create a hidden element 
												$(form).append(
													$('<input>')
													.attr('type', 'hidden')
													.attr('name', 'id[]')
													.val(rowId)
												);
											});
										
											// FOR DEMONSTRATION ONLY
											// The code below is not needed in production
											
											// Output form data to a console     
											$('#selected_rows_kandidates').val(rows_selected.join(","));
											var selectedIds = $('#selected_rows_kandidates').val();
											var link_id = $('#link_id').val();
											
											$('#exportajax').load('<?php getSiteUrl(); ?>export_excel.php?prozor=export_kandidata_u_linku', {
												link_id : link_id,
												selectedis : selectedIds
											});
							
											return false;
											
											// Output form data to a console     
											//$('#example-console-form').text($(form).serialize());
											
											// Remove added elements
											$('input[name="id\[\]"]', form).remove();
											
											// Prevent actual form submission
											e.preventDefault();
										});   										
										
									});
								</script>
								<div id="exportajax"></div>	
								<input type="hidden" id="selected_rows_kandidates"></input>
								<input type="hidden" id="link_id" value="<?php echo $kandidat_visitedurl;?>" ></input>
								<!--<input type="text" id="selected_ids">-->
								<form id="formForExport" action="/path/to/your/script.php" method="POST">
								<table id="idk_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>
											<!--
												<div class="main-container__column material-checkbox-group material-checkbox-group_danger">
													<input type="checkbox" id="checkbox_check_all_offer" class="material-checkbox">
													<label class="material-checkbox-group__label" for="checkbox_check_all_offer"></label>
												</div>
											-->
											</th>
											<th></th>
											<th></th>
											<th>Ime i prezime</th>
											<th>URL</th>
											<th>Prijava za</th>
											<th>Datum prijave</th>
											<th class="text-center">Grupa</th>
											<th class="text-center">Status</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php
										if($archive_status == 0){
											$upit = "WHERE kandidat_status !=3";
										}else if($archive_status == 1){
											$upit = "";
										}

										if(getEmployeeStatus() == 4){
											$uslov_saradnik = "AND (kandidat_status = 1 OR  kandidat_status = 2)";
										}else{
											$uslov_saradnik = "";
										}


										// USLOVI ZA FILTER PRETRAGU
										if(isset($_GET['search'])){

											$starost_od = $_GET['starost_od'];
											$starost_do = $_GET['starost_do'];

											$filter_grupe = $_GET['filter_grupe'];
											$filter_grupe_f = implode(',', $filter_grupe);

											$filter_status = $_GET['filter_status'];
											$filter_status_f = implode(',', $filter_status);

											if($_GET['vozacka_dozvola'] == "DA"){
												$vozacka_dozvola = "Da";
											}else{
												$vozacka_dozvola = "Ne";
											}

											if($_GET['radno_iskustvo'] == "DA"){
												$radno_iskustvo = "DA";
											}else{
												$radno_iskustvo = "NE";
											}

											$filter_drzavljanstvo = $_GET['filter_drzavljanstvo'];
											$filter_drzavljanstvo_f = implode(',', $filter_drzavljanstvo);

											if(isset($_GET['filter_drzavljanstvo'])){
											if($filter_drzavljanstvo_f == "EU,NON-EU"){
												$drzavljanstvo = "'EU državljanin','NON-EU državljanin'";
											}else if($filter_drzavljanstvo_f == "NON-EU"){
												$drzavljanstvo = "'NON-EU državljanin'";
											}else if($filter_drzavljanstvo_f == "EU"){
												$drzavljanstvo = "'EU državljanin'";
											}
												$uslov_pretrage_drzavljanstvo = 'AND kandidat_drzavljanstvo_vrsta IN('.$drzavljanstvo.')';
											}else{
												$uslov_pretrage_drzavljanstvo = '';
											}

											if(isset($_GET['kj_znanje_njemacki'])){
											if($_GET['kj_znanje_njemacki'] == "Bezznanja"){
												$kj_znanje_njemacki = "BEZ ZNANJA";
											}else if($_GET['kj_znanje_njemacki'] == "A1"){
												$kj_znanje_njemacki = "'A1','A2','B1','B2','C1','C2'";
											}else if($_GET['kj_znanje_njemacki'] == "A2"){
												$kj_znanje_njemacki = "'A2','B1','B2','C1','C2'";
											}else if($_GET['kj_znanje_njemacki'] == "B1"){
												$kj_znanje_njemacki = "'B1','B2','C1','C2'";
											}else if($_GET['kj_znanje_njemacki'] == "B2"){
												$kj_znanje_njemacki = "'B2','C1','C2'";
											}else if($_GET['kj_znanje_njemacki'] == "C1"){
												$kj_znanje_njemacki = "'C1','C2'";
											}else if($_GET['kj_znanje_njemacki'] == "C2"){
												$kj_znanje_njemacki = "'C2'";
											}
											}else{
												$kj_znanje_njemacki = "";
											}

											if($upit !="" OR $uslov_saradnik !=""){$and = "AND"; $and_url = "AND";}else{$and = "WHERE"; $and_url = "AND";}

											if($vozacka_dozvola == "Da"){
												$uslov_pretrage_vozacka = ''.$and.' kandidat_vozacka_dozvola = "'.$vozacka_dozvola.'"';
											}else{
												$uslov_pretrage_vozacka = ''.$and.' (kandidat_vozacka_dozvola = "Da" OR kandidat_vozacka_dozvola = "Ne")';
											}

											$uslov_pretrage_godine = 'AND (YEAR(NOW()) - YEAR(`kandidat_datumrodjenja`)) BETWEEN '.$starost_od.' AND '.$starost_do.'';
											$uslov_pretrage_statusi = 'AND kandidat_group IN('.$filter_grupe_f.')';
											$uslov_pretrage_status = 'AND kandidat_status IN('.$filter_status_f.')';

										}else{
											$uslov_pretrage_vozacka = "";
											$uslov_pretrage_godine = "";
											$uslov_pretrage_statusi = "";
											$uslov_pretrage_status = "";
											$kj_znanje_njemacki = "";
											$uslov_pretrage_drzavljanstvo = "";
											if($upit !="" OR $uslov_saradnik !=""){$and = "AND";$and_url = "AND";}else{$and = "WHERE";$and_url = "WHERE";}
										}

										//echo $upit;
										//echo$uslov_saradnik;
										//echo$uslov_pretrage_vozacka;
										//echo$uslov_pretrage_godine;
										//echo$uslov_pretrage_statusi;
										//echo$uslov_pretrage_drzavljanstvo;
										//echo$and;
										//echo "kandidat_visitedurl = $kandidat_visitedurl";

											$query = $db->prepare("
															SELECT 
																kandidat_id, 
																kandidat_ime, 
																kandidat_prezime, 
																kandidat_spol, 
																kandidat_jmbg, 
																kandidat_status, 
																kandidat_slika, 
																kandidat_email, 
																kandidat_datetime, 
																kandidat_visitedurl, 
																kandidat_prijava_na, 
																kandidat_group,
															CASE
																WHEN kandidat_status_prijave IN (1,2) THEN 'Slobodan'
																WHEN kandidat_status_prijave = 5 THEN 'Odbijen'
																WHEN kandidat_status_prijave IN (3,6,7,8,9,10,12,15,18,21,24,27) THEN 'U procesu'
																WHEN kandidat_status_prijave = 4 THEN 'Zaposlen'
															END as status_prijave
															FROM idk_kandidati
															$upit $uslov_saradnik $uslov_pretrage_vozacka $uslov_pretrage_godine  $uslov_pretrage_statusi $uslov_pretrage_status $uslov_pretrage_drzavljanstvo $and_url kandidat_visitedurl = $kandidat_visitedurl
															ORDER BY kandidat_id ASC
															");

											$query->execute();

											$i = 1;
											while($row = $query->fetch()){

												$kandidat_id = $row['kandidat_id'];
												$kandidat_ime = $row['kandidat_ime'];
												$kandidat_prezime = $row['kandidat_prezime'];
												$kandidat_spol = $row['kandidat_spol'];
												$kandidat_jmbg = $row['kandidat_jmbg'];
												$kandidat_group = $row['kandidat_group'];
												$kandidat_email = $row['kandidat_email'];
												$kandidat_datetime = $row['kandidat_datetime'];
												$kandidat_visitedurl = $row['kandidat_visitedurl'];
												$kandidat_prijava_na = $row['kandidat_prijava_na'];
												$kandidat_status_prijave = $row["status_prijave"];


												// PROVJERA DA LI RADNIK IMA RADNO ISKUSTVO
												$check_work_experience = $db->prepare("
																	SELECT kri_id
																	FROM idk_kandidat_radno_iskustvo
																	WHERE kri_kandidat_id = :kri_kandidat_id");

												$check_work_experience->execute(array(
																':kri_kandidat_id' => $kandidat_id));

												$check_work = $check_work_experience->rowCount();
												// END PROVJERA DA LI RADNIK IMA RADNO ISKUSTVO


												// PROVJERA ZNANJA NJEMACKOG JEZIKA
												$check_german_knowlege = $db->prepare("
																	SELECT kj_id, kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje
																	FROM idk_kandidat_jezici
																	WHERE kj_kandidatid = :kj_kandidatid AND kj_naziv = :kj_naziv AND kj_slusanje IN ($kj_znanje_njemacki)");

												$check_german_knowlege->execute(array(
																':kj_kandidatid' => $kandidat_id,
																':kj_naziv' => "Njemački"
																));

												$check_german = $check_german_knowlege->rowCount();
												// PROVJERA ZNANJA NJEMACKOG JEZIKA


												$query_urls = $db->prepare("
																SELECT lg_id, lg_url, lg_desc, lg_datetime
																FROM idk_link_generator
																WHERE lg_id = $kandidat_visitedurl
																");

												$query_urls->execute();
												$url = $query_urls->fetch();

													$lg_id = $url['lg_id'];
													$lg_url = $url['lg_url'];
													$lg_desc = $url['lg_desc'];


												if($row['kandidat_slika'] == "none"){
													$kandidat_slika = "none.jpg";
												}else{
													$kandidat_slika = $row['kandidat_slika'];
												}
												if(getEmployeeStatus() == 17){
													switch($kandidat_status_prijave){
														case "Slobodan":
															$kandidat_status = '<span class="label label-warning material-label material-label_warning material-label_xs main-container__column">Slobodan</span>';
														break;
														case "Odbijen":
															$kandidat_status = '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Odbijen</span>';
														break;
														case "U procesu":
															$kandidat_status = '<span class="label label-primary material-label material-label_primary material-label_xs main-container__column">U procesu</span>';
														break;
														case "Zaposlen":
															$kandidat_status = '<span class="label label-success material-label material-label_success material-label_xs main-container__column">Zaposlen</span>';
														break;
													}
												}else{
													if($row['kandidat_status'] == 0){
														$kandidat_status = '<span class="label label-warning material-label material-label_warning material-label_xs main-container__column">Na provjeri</span>';
													}elseif($row['kandidat_status'] == 1){
														$kandidat_status = '<span class="label label-primary material-label material-label_primary material-label_xs main-container__column">U obradi</span>';
													}elseif($row['kandidat_status'] == 2){
														$kandidat_status = '<span class="label label-success material-label material-label_success material-label_xs main-container__column">Obrađen</span>';
													}elseif($row['kandidat_status'] == 3){
														$kandidat_status = '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Arhiviran</span>';
													}elseif($row['kandidat_status'] == 4){
														$kandidat_status = '<span class="label label-info material-label material-label_info material-label_xs main-container__column">Kontrola</span>';
													}elseif($row['kandidat_status'] == 5){
														$kandidat_status = '<span class="label label-info material-label material-label_primary material-label_xs main-container__column">Dopuna</span>';
													}elseif($row['kandidat_status'] == 6){
														$kandidat_status = '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Odbio Messenger</span>';
													}elseif($row['kandidat_status'] == 7){
														$kandidat_status = '<span class="label label-danger material-label material-label_yellow material-label_xs main-container__column">U obradi više od 3 dana</span>';
													}elseif($row['kandidat_status'] == 8){
														$kandidat_status = '<span class="label label-danger material-label material-label_yellow material-label_xs main-container__column">Na dopuni više od 3 dana</span>';
													}
												}
												$query_group = $db->prepare("
																SELECT kg_id, kg_title, kg_date
																FROM idk_kandidati_grupe
																WHERE kg_id = :kg_id
																ORDER BY kg_id ASC
																");

												$query_group->execute(array(
													":kg_id" => $kandidat_group
												));

												$griup = $query_group->fetch();
													$kg_title = $griup['kg_title'];


										?>
										<?php if(!isset($_GET['search'])){ ?>
										<tr>
											<td class="text-center">
											<?php echo $kandidat_id; ?>
											</td>
											<td class="text-center"><?php echo $kandidat_id; ?></td>
											<td class="text-center"><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/kandidati/<?php echo $kandidat_slika; ?>"></a></td>
											<td><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><?php echo $kandidat_ime; ?> <?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo $kandidat_prezime; ?><?php } ?></a></td>
											<td><?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo $lg_url; ?><?php } ?></td>
											<td><?php echo $kandidat_prijava_na; ?></td>
											<td><?php echo $kandidat_datetime; ?></td>
											<td class="text-center"><?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo '<span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$kg_title.'</span>'; ?><?php } ?></td>
											<td class="text-center"><?php echo $kandidat_status; ?></td>
											<td class="text-center">
												<div class="btn-group material-btn-group">
													<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
													<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
														<li><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
														<?php if(getEmployeeStatus() == 1){ ?>
														<li><a href="#" class="material-dropdown-menu__link cvshow" data="<?php getSiteURL(); ?>cv?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>cv2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#cvModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> CV</a></li>
														<?php }else{} ?>

														<li><a href="#" class="material-dropdown-menu__link profilshow" data="<?php getSiteURL(); ?>profil?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>profil2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#profilModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Profil</a></li>

														<?php if(getEmployeeStatus() == 1){ ?>
														<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>kandidati?page=archive&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
														<?php }else{} ?>

													</ul>
												</div>
											</td>
										</tr>
										<?php }else{ ?>
										<?php if($_GET['kj_znanje_njemacki'] != "Bezznanja"){ ?>
										<?php if($check_german > 0 AND $radno_iskustvo == "NE"){  ?>
										<tr>
											<td class="text-center">
											<?php echo $kandidat_id; ?>
											</td>
											<td class="text-center">
											<?php echo $kandidat_id; ?>
											</td>											
											<td class="text-center"><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/kandidati/<?php echo $kandidat_slika; ?>"></a></td>
											<td><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><?php echo $kandidat_ime; ?> <?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo $kandidat_prezime; ?><?php } ?></a></td>
											<td><?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo $lg_url; ?><?php } ?></td>
											<td><?php echo $kandidat_prijava_na; ?></td>
											<td class="text-center"><?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo '<span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$kg_title.'</span>'; ?><?php } ?></td>
											<td class="text-center"><?php echo $kandidat_status; ?></td>
											<td class="text-center">
												<div class="btn-group material-btn-group">
													<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
													<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
														<li><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
														<?php if(getEmployeeStatus() == 1){ ?>
														<li><a href="#" class="material-dropdown-menu__link cvshow" data="<?php getSiteURL(); ?>cv?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>cv2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#cvModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> CV</a></li>
														<?php }else{} ?>

														<li><a href="#" class="material-dropdown-menu__link profilshow" data="<?php getSiteURL(); ?>profil?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>profil2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#profilModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Profil</a></li>

														<?php if(getEmployeeStatus() == 1){ ?>
														<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>kandidati?page=archive&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
														<?php }else{} ?>

													</ul>
												</div>
											</td>
										</tr>
										<?php }else if($check_german > 0 AND $radno_iskustvo == "DA") { ?>
										<?php if($check_work > 0){ ?>
										<tr>
											<td class="text-center">
											<?php echo $kandidat_id; ?>
											</td>
											<td class="text-center">
											<?php echo $kandidat_id; ?>
											</td>											
											<td class="text-center"><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/kandidati/<?php echo $kandidat_slika; ?>"></a></td>
											<td><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><?php echo $kandidat_ime; ?> <?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo $kandidat_prezime; ?><?php } ?></a></td>
											<td><?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo $lg_url; ?><?php } ?></td>
											<td><?php echo $kandidat_prijava_na; ?></td>
											<td class="text-center"><?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo '<span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$kg_title.'</span>'; ?><?php } ?></td>
											<td class="text-center"><?php echo $kandidat_status; ?></td>
											<td class="text-center">
												<div class="btn-group material-btn-group">
													<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
													<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
														<li><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
														<?php if(getEmployeeStatus() == 1){ ?>
														<li><a href="#" class="material-dropdown-menu__link cvshow" data="<?php getSiteURL(); ?>cv?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>cv2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#cvModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> CV</a></li>
														<?php }else{} ?>

														<li><a href="#" class="material-dropdown-menu__link profilshow" data="<?php getSiteURL(); ?>profil?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>profil2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#profilModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Profil</a></li>

														<?php if(getEmployeeStatus() == 1){ ?>
														<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>kandidati?page=archive&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
														<?php }else{} ?>

													</ul>
												</div>
											</td>
										</tr>
										<?php }else{} ?>


										<?php }else{} ?>

										<?php }else{ ?>
										<?php if($radno_iskustvo == "NE"){  ?>
										<tr>
											<td class="text-center">
											<?php echo $kandidat_id; ?>
											</td>
											<td class="text-center">
											<?php echo $kandidat_id; ?>
											</td>											
											<td class="text-center"><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/kandidati/<?php echo $kandidat_slika; ?>"></a></td>
											<td><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><?php echo $kandidat_ime; ?> <?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo $kandidat_prezime; ?><?php } ?></a></td>
											<td><?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo $lg_url; ?><?php } ?></td>
											<td><?php echo $kandidat_prijava_na; ?></td>
											<td class="text-center"><?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo '<span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$kg_title.'</span>'; ?><?php } ?></td>
											<td class="text-center"><?php echo $kandidat_status; ?></td>
											<td class="text-center">
												<div class="btn-group material-btn-group">
													<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
													<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
														<li><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
														<?php if(getEmployeeStatus() == 1){ ?>
														<li><a href="#" class="material-dropdown-menu__link cvshow" data="<?php getSiteURL(); ?>cv?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>cv2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#cvModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> CV</a></li>
														<?php }else{} ?>

														<li><a href="#" class="material-dropdown-menu__link profilshow" data="<?php getSiteURL(); ?>profil?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>profil2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#profilModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Profil</a></li>

														<?php if(getEmployeeStatus() == 1){ ?>
														<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>kandidati?page=archive&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
														<?php }else{} ?>

													</ul>
												</div>
											</td>
										</tr>
										<?php }else if($radno_iskustvo == "DA") { ?>
										<?php if($check_work > 0){ ?>
										<tr>
											<td class="text-center">
											<?php echo $kandidat_id; ?>
											</td>
											<td class="text-center">
											<?php echo $kandidat_id; ?>
											</td>											
											<td class="text-center"><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/kandidati/<?php echo $kandidat_slika; ?>"></a></td>
											<td><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><?php echo $kandidat_ime; ?> <?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo $kandidat_prezime; ?><?php } ?></a></td>
											<td><?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo $lg_url; ?><?php } ?></td>
											<td><?php echo $kandidat_prijava_na; ?></td>
											<td class="text-center"><?php if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status))){ ?><?php echo '<span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$kg_title.'</span>'; ?><?php } ?></td>
											<td class="text-center"><?php echo $kandidat_status; ?></td>
											<td class="text-center">
												<div class="btn-group material-btn-group">
													<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
													<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
														<li><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
														<?php if(getEmployeeStatus() == 1){ ?>
														<li><a href="#" class="material-dropdown-menu__link cvshow" data="<?php getSiteURL(); ?>cv?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>cv2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#cvModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> CV</a></li>
														<?php }else{} ?>

														<li><a href="#" class="material-dropdown-menu__link profilshow" data="<?php getSiteURL(); ?>profil?page=open&id=<?php echo $kandidat_id; ?>" data-id="<?php getSiteURL(); ?>profil2?page=open&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#profilModal"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Profil</a></li>

														<?php if(getEmployeeStatus() == 1){ ?>
														<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>kandidati?page=archive&id=<?php echo $kandidat_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
														<?php }else{} ?>

													</ul>
												</div>
											</td>
										</tr>
										<?php }else{} ?>


										<?php }else{} ?>
										<?php } ?>

										<?php } ?>

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
														<p>Jeste li sigurni da želite arhivirati kandidata?</p>
													</div>
													<div class="modal-footer material-modal__footer">
														<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
														<a id="archive_link" href=""><button class="btn btn-primary material-btn material-btn_danger">ARHIVIRAJ</button></a>
													</div>
												</div>
											</div>
										</div>

										<script>
											$(".cvshow").click(function () {
												var addressValueBs = $(this).attr("data");
												document.getElementById("cv_bosanski").href = addressValueBs;

												var addressValueDe = $(this).data("id");
												document.getElementById("cv_njemacki").href = addressValueDe;
											});
											$(".profilshow").click(function () {
												var addressValueBs = $(this).attr("data");
												document.getElementById("profil_bosanski").href = addressValueBs;

												var addressValueDe = $(this).data("id");
												document.getElementById("profil_njemacki").href = addressValueDe;
											});
										</script>
										<!-- Modal CV -->
										<div class="modal material-modal material-modal_success fade" id="cvModal">
											<div class="modal-dialog">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Izaberi jezik CV-a</h4>
													</div>
													<div class="modal-body material-modal__body text-center">
														<a id="cv_bosanski" href="" target="_BLANK"><button class="btn btn-primary material-btn material-btn_success"><img src="<?php getSiteUrl(); ?>images/BosniaHerzegowina.png" width="50">&nbsp &nbsp BOSANSKI</button></a>

														<a id="cv_njemacki" href="" target="_BLANK"><button class="btn btn-primary material-btn material-btn_success"><img src="<?php getSiteUrl(); ?>images/Germany.png" width="50">&nbsp &nbsp NJEMAČKI</button></a>
													</div>
													<div class="modal-footer material-modal__footer">
														<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
													</div>
												</div>
											</div>
										</div>

										<!-- Modal profil -->
										<div class="modal material-modal material-modal_success fade" id="profilModal">
											<div class="modal-dialog">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Izaberi jezik Profila</h4>
													</div>
													<div class="modal-body material-modal__body text-center">
														<a id="profil_bosanski" href="" target="_BLANK"><button class="btn btn-primary material-btn material-btn_success"><img src="<?php getSiteUrl(); ?>images/BosniaHerzegowina.png" width="50">&nbsp &nbsp BOSANSKI</button></a>

														<a id="profil_njemacki" href="" target="_BLANK"><button class="btn btn-primary material-btn material-btn_success"><img src="<?php getSiteUrl(); ?>images/Germany.png" width="50">&nbsp &nbsp NJEMAČKI</button></a>
													</div>
													<div class="modal-footer material-modal__footer">
														<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
													</div>
												</div>
											</div>
										</div>
									</tbody>
								</table>
								</form>
							</div>
					<?php if(getEmployeeStatus() != 17){?>
							<!-- CHART DATA -->
							<div class="col-xs-12 col-sm-12 col-md-4">
								<br/>
								<hr/>
								<br/>
								<div id="canvas-holder" style="width:100%">
									<canvas id="chart-area"></canvas>
								</div>
								<?php
									$status_na_provjeri = 0;
									$status_u_obradi = 1;
									$status_obradjen = 2;
									$status_arhiviran = 3;
									$status_kontrola = 4;
									$status_dopuna = 5;
									$status_odbio_msngr = 6;
									$status_u_obradi_vise_od_3 = 7;
									$status_u_dopuni_vise_od_3 = 8;
									
									$arhivirani;
									$na_provjeri;
									$u_obradi;
									$obradjen;
									$kontrola;
									$dopuna;
									$odbio_msngr;
									$u_obradi_3;
									$u_dopuni_3;
									
									$red = "rgb(255,255,255)";
									$orange = "rgb(255,255,255)";
									$yellow = "rgb(255,255,255)";
									$green = "rgb(255,255,255)";
									$blue = "rgb(255,255,255)";
									$purple = "rgb(255,255,255)";
									$orange2 = "rgb(255,255,255)";
									$pink = "rgb(255,255,255)";
									$light_blue = "rgb(255,255,255)";
									
									$broj_na_provjeri = getEmployeesPerStatusAndUrlR($status_na_provjeri, $kandidat_visitedurl);
									if($broj_na_provjeri > 0){
										$na_provjeri = "Na Provjeri(". $broj_na_provjeri . ")";
										$orange = "rgb(255, 159, 64)";
									}
									
									$broj_u_obradi = getEmployeesPerStatusAndUrlR($status_u_obradi, $kandidat_visitedurl);
									if($broj_u_obradi > 0){
										$u_obradi = "U obradi(". $broj_u_obradi . ")";
										$blue = "rgb(54, 162, 235)";
										
									}
									
									$broj_obradjen = getEmployeesPerStatusAndUrlR($status_obradjen, $kandidat_visitedurl);
									if($broj_obradjen > 0){
										$obradjen = "Obrađen(". $broj_obradjen . ")";
										$green = "rgb(102, 213, 102)";
									}
									
									$broj_arhivirani = getEmployeesPerStatusAndUrlR($status_arhiviran, $kandidat_visitedurl);
									if($broj_arhivirani > 0){
										$arhivirani = "Arhiviran(". $broj_arhivirani . ")";
										$red = "rgb(243, 65, 60)";
										
									}
									
									$broj_kontrola = getEmployeesPerStatusAndUrlR($status_kontrola, $kandidat_visitedurl);
									if($broj_kontrola > 0){
										$kontrola = "Kontrola(". $broj_kontrola . ")";
										$light_blue = "rgb(148, 241, 252)";
									}
									
									$broj_dopuna = getEmployeesPerStatusAndUrlR($status_dopuna, $kandidat_visitedurl);
									if($broj_dopuna > 0){
										$dopuna = "Dopuna(". $broj_dopuna . ")";
										$purple = "rgb(153, 102, 255)";
									}
									
									$broj_odbio_msngr = getEmployeesPerStatusAndUrlR($status_odbio_msngr, $kandidat_visitedurl);
									if($broj_odbio_msngr > 0){
										$odbio_msngr = "Odbio Messenger(". $broj_odbio_msngr . ")";
										$orange2 = "rgb(255,68,11)";
									}
									
									$broj_u_obradi_3 = getEmployeesPerStatusAndUrlR($status_u_obradi_vise_od_3, $kandidat_visitedurl);
									if($broj_u_obradi_3 > 0){
										$u_obradi_3 = "U obradi vise od 3 dana(". $broj_u_obradi_3 . ")";
										$yellow = "rgb(255, 205, 86)";
										
									}
									
									$broj_u_dopuni_3 = getEmployeesPerStatusAndUrlR($status_u_dopuni_vise_od_3, $kandidat_visitedurl);
									if($broj_u_dopuni_3 > 0){
										$u_dopuni_3 = "U dopuni vise od 3 dana(". $broj_u_dopuni_3 . ")";
										$pink = "rgb(255, 0 ,255)";
									}
								?>
								<script>
									window.chartColors = {
										red: '<?php echo $red; ?>',
										orange: '<?php echo $orange; ?>',
										yellow: '<?php echo $yellow; ?>',
										green: '<?php echo $green; ?>',
										blue: '<?php echo $blue; ?>',
										purple: '<?php echo $purple; ?>',
										orange2: '<?php echo $orange2; ?>',
										pink: '<?php echo $pink; ?>',
										lightblue: '<?php echo $light_blue; ?>'
									};	
								
									var randomScalingFactor = function() {
										return Math.round(Math.random() * 100);
									};
							
									var config = {
										type: 'doughnut',
										data: {
											datasets: [{
												data: [
													<?php getEmployeesPerStatusAndUrl($status_arhiviran, $kandidat_visitedurl); ?>,
													<?php getEmployeesPerStatusAndUrl($status_odbio_msngr, $kandidat_visitedurl); ?>,
													<?php getEmployeesPerStatusAndUrl($status_na_provjeri, $kandidat_visitedurl); ?>,
													<?php getEmployeesPerStatusAndUrl($status_u_obradi_vise_od_3, $kandidat_visitedurl); ?>,
													<?php getEmployeesPerStatusAndUrl($status_u_dopuni_vise_od_3, $kandidat_visitedurl); ?>,
													<?php getEmployeesPerStatusAndUrl($status_dopuna, $kandidat_visitedurl); ?>,
													<?php getEmployeesPerStatusAndUrl($status_u_obradi, $kandidat_visitedurl); ?>,
													<?php getEmployeesPerStatusAndUrl($status_kontrola, $kandidat_visitedurl); ?>,
													<?php getEmployeesPerStatusAndUrl($status_obradjen, $kandidat_visitedurl); ?>
												],
												backgroundColor: [
													window.chartColors.red,
													window.chartColors.orange2,
													window.chartColors.orange,
													window.chartColors.yellow,
													window.chartColors.pink,
													window.chartColors.purple,
													window.chartColors.blue,
													window.chartColors.lightblue,
													window.chartColors.green
												],
												label: 'Dataset 1'
											}],
											labels: [
												  '<?php echo $arhivirani; ?>',
												  '<?php echo $odbio_msngr; ?>',
												  '<?php echo $na_provjeri; ?>',
												  '<?php echo $u_obradi_3; ?>',
												  '<?php echo $u_dopuni_3; ?>',
												  '<?php echo $dopuna; ?>',
												  '<?php echo $u_obradi; ?>',
												  '<?php echo $kontrola; ?>',
												  '<?php echo $obradjen; ?>'
											]
										},
										options: {
											responsive: true,
											legend: {
												position: 'left',
											},
											title: {
												display: true,
												text: 'Statistika kandidata za link'
											},
											animation: {
												animateScale: true,
												animateRotate: true
											}
										}
									};
									
									var config2 = {
										type: 'doughnut',
										data: {
											datasets: [{
												data: [
													<?php getEmployeesPerStatusAndUrl($status_arhiviran, $kandidat_visitedurl); ?>,
													<?php getEmployeesPerStatusAndUrl($status_na_provjeri, $kandidat_visitedurl); ?>,
													<?php getEmployeesPerStatusAndUrl($status_u_obradi, $kandidat_visitedurl); ?>,
													<?php getEmployeesPerStatusAndUrl($status_obradjen, $kandidat_visitedurl); ?>,
												],
												backgroundColor: [
													window.chartColors.red,
													window.chartColors.orange,
													window.chartColors.blue,
													window.chartColors.green
												],
												label: 'Dataset 1'
											}],
											labels: [
												'Arhiviran (<?php getEmployeesPerStatusAndUrl($status_arhiviran, $kandidat_visitedurl); ?>)',
												'Na provjeri (<?php getEmployeesPerStatusAndUrl($status_na_provjeri, $kandidat_visitedurl); ?>)',
												'U obradi (<?php getEmployeesPerStatusAndUrl($status_u_obradi, $kandidat_visitedurl); ?>)',
												'Obrađen (<?php getEmployeesPerStatusAndUrl($status_obradjen, $kandidat_visitedurl); ?>)'
											]
										},
										options: {
											responsive: true,
											legend: {
												display: false,
												position: 'left',
											},
											title: {
												display: false,
												text: 'Statistika kandidata za link'
											},
											animation: {
												animateScale: true,
												animateRotate: true
											}
										}
									};									
									
									window.onload = function() {
										var ctx = document.getElementById('chart-area').getContext('2d');
										window.myDoughnut = new Chart(ctx, config);
										
										var ctx2 = document.getElementById("chart-area2").getContext("2d");
										window.myDoughnut = new Chart(ctx2, config2);										
										
									};
									
									
								
								</script>
								<?php }?>
							</div>
							<!-- TF STATS -->
							<div class="col-xs-12 col-sm-12 col-md-4">
								<br/><hr/><br/>
								<p><b>Statistika poziva za casting</b></p>
								<?php
								$query_tf_stats = $db->prepare("
									SELECT if(ts.tfs_name is null,'Neprozvani',ts.tfs_name) as name, COUNT(if(tf_max.tf_candidate_id is null,1,0)) as count FROM `idk_kandidati` 
									LEFT JOIN (
										SELECT max(tf_id) as max_id, tf_candidate_id 
										FROM idk_task_force 
										WHERE tf_nalog_id = (SELECT lg_nalogid FROM idk_link_generator WHERE lg_id = :visited_url)
										GROUP BY tf_candidate_id
										) tf_max ON tf_max.tf_candidate_id = kandidat_id
									left JOIN idk_task_force tf 
									ON tf.tf_id = tf_max.max_id
									left JOIN idk_tf_statusi ts 
									ON ts.tfs_id = tf.tf_status_id
									WHERE  kandidat_visitedurl = :visited_url  
									GROUP BY tf.tf_status_id  
									ORDER BY tf.tf_status_id ASC;
								");
								$query_tf_stats->execute(array(
									':visited_url' => $kandidat_visitedurl
								));
								echo "<table>";
								while($row_tf_stats = $query_tf_stats->fetch()){
									echo "<tr>
											<td style='text-align: right'>".$row_tf_stats['count']."</td>
											<td style='width: 15px; text-align: center'> - </td>
											<td>".$row_tf_stats['name']."</td></tr>";
								}
								echo "</table>";
								?>
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

				case "open":

					$employee_id = $_GET['id'];

					$query = $db->prepare("
									SELECT employee_firstname, employee_lastname, employee_jmbg, employee_email, employee_position, employee_dob, employee_doe, employee_phone, employee_address, employee_city, employee_country, employee_info, employee_status, employee_image, employee_inote
									FROM idk_employees
									WHERE employee_id = :employee_id");

					$query->execute(array(
								':employee_id' => $employee_id));

					$row = $query->fetch();

						$employee_firstname = $row['employee_firstname'];
						$employee_lastname = $row['employee_lastname'];
						$employee_jmbg = $row['employee_jmbg'];
						$employee_position = $row['employee_position'];
						$employee_dob = date('d.m.Y.', strtotime($row['employee_dob']));
						$employee_doe = date('d.m.Y.', strtotime($row['employee_doe']));
						$employee_email = $row['employee_email'];
						$employee_phone = $row['employee_phone'];
						$employee_address = $row['employee_address'];
						$employee_city = $row['employee_city'];
						$employee_country = $row['employee_country'];
						$employee_info = $row['employee_info'];
						$employee_inote = $row['employee_inote'];

						if($row['employee_image'] == "none"){
							$employee_image = "none.jpg";
						}else{
							$employee_image = $row['employee_image'];
						}

						if($row['employee_status'] == 0){
							$employee_status = "Deaktiviran";
						}elseif($row['employee_status'] == 1){
							$employee_status = "Administrator";
						}elseif($row['employee_status'] == 2){
							$employee_status = "Super korisnik";
						}elseif($row['employee_status'] == 3){
							$employee_status = "Korisnik";
						}
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><a class="fancybox" rel="group" href="<?php getSiteURL(); ?>files/employees/<?php echo $employee_image; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/employees/<?php echo $employee_image; ?>"></a> <?php echo $employee_firstname; ?> <?php echo $employee_lastname; ?></h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>employees?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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

									if($mess == 1){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste dodali novi bilješku.</div>
										<script>$(function() { $('[href="#notes"]').tab('show'); });</script>
									<?php
									}elseif($mess == 2){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste dodali novi dokument.</div>
										<script>$(function() { $('[href="#documents"]').tab('show'); });</script>
									<?php
									}elseif($mess == 3){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste obrisali dokument.</div>
										<script>$(function() { $('[href="#documents"]').tab('show'); });</script>
									<?php
									}elseif($mess == 4){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste obrisali bilješku.</div>
										<script>$(function() { $('[href="#notes"]').tab('show'); });</script>
									<?php
									}elseif($mess == 5){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste snimili važne napomene.</div>
										<script>$(function() { $('[href="#important"]').tab('show'); });</script>
									<?php
									}
								?>
							</div>
						</div>


						<ul class="list-inline text-right">
							<li><a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#noteModal"><i class="fa fa-sticky-note-o" aria-hidden="true"></i> <span>Dodaj bilješku</span></a></li>
							<!-- Modal add note -->
							<div class="modal material-modal material-modal_primary fade text-left" id="noteModal">
								<div class="modal-dialog ">
									<div class="modal-content material-modal__content">
										<div class="modal-header material-modal__header">
											<button class="close material-modal__close" data-dismiss="modal">&times;</button>
											<h4 class="modal-title material-modal__title">Dodaj bilješku</h4>
										</div>
										<div class="modal-body material-modal__body">
											<form action="<?php getSiteURL(); ?>do.php?form=add_employee_note" method="post" role="form" class="form-horizontal">
												<input type="hidden" name="note_dataid" value="<?php echo $employee_id; ?>" />
												<div class="form-group">
													<div class="col-md-offset-2 col-sm-8">
														<div class="form-group materail-input-block materail-input-block_success">
															<textarea class="form-control materail-input material-textarea" name="note_txt" placeholder="Bilješka" rows="6" required></textarea>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>
										</div>
										<div class="modal-footer material-modal__footer">
												<button type="submit" class="btn btn-primary material-btn material-btn_primary">Dodaj</button>
											</form>
										</div>
									</div>
								</div>
							</div>
							<!-- Modal add note end -->
							<li><a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#docModal"><i class="fa fa-file-text-o" aria-hidden="true"></i> <span>Dodaj dokument</span></a></li>
							<!-- Modal add document -->
							<div class="modal material-modal material-modal_primary fade text-left" id="docModal">
								<div class="modal-dialog ">
									<div class="modal-content material-modal__content">
										<div class="modal-header material-modal__header">
											<button class="close material-modal__close" data-dismiss="modal">&times;</button>
											<h4 class="modal-title material-modal__title">Dodaj dokument</h4>
										</div>
										<div class="modal-body material-modal__body">
											<form action="<?php getSiteURL(); ?>do.php?form=add_employee_doc" enctype="multipart/form-data" method="post" role="form" class="form-horizontal">
												<div id="idk_alert_size" class="row hidden">
													<div class="col-sm-12">
														<div class="alert material-alert material-alert_danger">Greška: Dokument koji pokuštavate dodati je veći od dozvoljene veličine.</div>
													</div>
												</div>
												<div id="idk_alert_ext" class="row hidden">
													<div class="col-sm-12">
														<div class="alert material-alert material-alert_danger">Greška: Format dokumenta kojeg pokušavate dodati nije dozvoljen.</div>
													</div>
												</div>
												<input type="hidden" name="document_dataid" value="<?php echo $employee_id; ?>" />
												<div class="form-group">
													<div class="col-md-offset-2 col-sm-8">
														<div class="form-group materail-input-block materail-input-block_success">
															<input type="text" class="form-control materail-input" name="document_name" id="document_name" placeholder="Naziv dokumenta" required>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>
												<div class="form-group">
													<div class="col-md-offset-2 col-sm-8">
														<div class="form-group materail-input-block materail-input-block_success">
															<input type="text" class="form-control materail-input" name="document_desc" id="document_desc" placeholder="Opis dokumenta">
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>
												<div class="form-group">
													<div class="col-md-offset-2 col-sm-8">
														<div class="fileinput fileinput-new" data-provides="fileinput">
															<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi dokument</span><span class="fileinput-exists">Promijeni</span><input type="file" name="document_file" id="document_file" required required></span> <i class="fa fa-info-circle fa-lg idk_margin_left10" data-toggle="tooltip" data-placement="right" title="Napomena: Dokument ne smije biti veći od 20MB! - Dozvoljeni formati: jpg, jpeg, pdf, doc, docx, xls, xlsx, txt, ppt, pptx, png" aria-hidden="true"></i>
															<span class="fileinput-filename"></span>
															<a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">×</a>
															<script>
																$(function (){
																	$('#document_file').change(function (){

																		var f = this.files[0];

																		if (f.size > 20388608 || f.fileSize > 20388608){
																			$('#idk_alert_size').removeClass('hidden');
																			this.value = null;
																		}else{
																			$('#idk_alert_size').addClass('hidden');
																		}

																		var ext = $('#document_file').val().split('.').pop().toLowerCase();

																		if($.inArray(ext, ['jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png']) == -1) {
																			$('#idk_alert_ext').removeClass('hidden');
																			this.value = null;
																		}else{
																			$('#idk_alert_ext').addClass('hidden');
																		}
																	})
																});
															</script>
														</div>
													</div>
												</div>
										</div>
										<div class="modal-footer material-modal__footer">
												<button type="submit" class="btn btn-primary material-btn material-btn_primary">Dodaj</button>
											</form>
										</div>
									</div>
								</div>
							</div>
							<!-- Modal add document end -->
						</ul>
						<hr />
						<div id="myTabs" class="panel-group material-tabs-group">
							<ul class="nav nav-tabs material-tabs material-tabs_primary">
								<li class="active"><a href="#info" class="material-tabs__tab-link" data-toggle="tab">Informacije</a></li>
								<li><a href="#notes" class="material-tabs__tab-link" data-toggle="tab">Bilješke</a></li>
								<li><a href="#documents" class="material-tabs__tab-link" data-toggle="tab">Dokumenti</a></li>
								<li><a href="#important" class="material-tabs__tab-link" data-toggle="tab">Važno</a></li>
							</ul>
							<div class="tab-content materail-tabs-content">
								<div class="tab-pane fade active in" id="info">
									<div class="row idk_employee_info">
										<div class="col-md-6">
											<div class="row">
												<strong class="col-sm-4 text-right">Ime:</strong>
												<div class="col-sm-8"><?php echo $employee_firstname; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Prezime:</strong>
												<div class="col-sm-8"><?php echo $employee_lastname; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">JMBG:</strong>
												<div class="col-sm-8"><?php echo $employee_jmbg; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Email:</strong>
												<div class="col-sm-8"><a href="mailto:<?php echo $employee_email; ?>"><?php echo $employee_email; ?></a></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Pozicija:</strong>
												<div class="col-sm-8"><?php echo $employee_position; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Datum rođenja:</strong>
												<div class="col-sm-8"><?php echo $employee_dob; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Datum zaposlenja:</strong>
												<div class="col-sm-8"><?php echo $employee_doe; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Telefon:</strong>
												<div class="col-sm-8"><?php echo $employee_phone; ?></div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="row">
												<strong class="col-sm-4 text-right">Adresa:</strong>
												<div class="col-sm-8"><?php echo $employee_address; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Grad:</strong>
												<div class="col-sm-8"><?php echo $employee_city; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Država:</strong>
												<div class="col-sm-8"><?php echo $employee_country; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Status:</strong>
												<div class="col-sm-8"><?php echo $employee_status; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Ostale informacije:</strong>
												<div class="col-sm-8"><?php echo $employee_info; ?></div>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="notes">
									<div class="panel-group material-accordion material-accordion_primary" id="accordion1">
										<?php

											$year_query = $db->prepare("
																SELECT YEAR (note_datetime) AS note_datetime_year
																FROM idk_notes
																WHERE note_dataid = :note_dataid
																GROUP BY YEAR (note_datetime)
																ORDER BY YEAR (note_datetime) DESC");

											$year_query->execute(array(
															':note_dataid' => $employee_id));

											while($year_row = $year_query->fetch()){

												$note_datetime_year = $year_row['note_datetime_year'];

												if($note_datetime_year == date('Y')){
													$idk_notes_in = "in";
												}else{
													$idk_notes_in = "";
												}

										?>
										<div class="panel panel-default material-accordion__panel material-accordion__panel">
											<div class="panel-heading material-accordion__heading">
												<h4 class="panel-title">
													<a class="material-accordion__title" data-toggle="collapse" data-parent="#accordion1" href="#<?php echo $note_datetime_year; ?>"><?php echo $note_datetime_year; ?></a>
												</h4>
											</div>
											<div id="<?php echo $note_datetime_year; ?>" class="panel-collapse <?php echo $idk_notes_in; ?> collapse material-accordion__collapse">
												<div class="panel-body">
													<?php
														$notes_query = $db->prepare("
																		SELECT note_id, note_datetime, note_txt, employee_firstname, employee_lastname
																		FROM idk_notes
																		INNER JOIN idk_employees ON idk_notes.note_employeeid = idk_employees.employee_id
																		WHERE YEAR (note_datetime) = :note_datetime_year AND note_dataid = :note_dataid
																		ORDER BY note_datetime DESC");

														$notes_query->execute(array(
																		':note_datetime_year' => $note_datetime_year,
																		':note_dataid' => $employee_id));

														while($notes_row = $notes_query->fetch()){

															$note_date = date('d.m.Y.', strtotime($notes_row['note_datetime']));
															$note_time = date('H:i', strtotime($notes_row['note_datetime']));
															$note_id = $notes_row['note_id'];
															$note_txt = $notes_row['note_txt'];
															$employee_firstname = $notes_row['employee_firstname'];
															$employee_lastname = $notes_row['employee_lastname'];

													?>
													<div class="row">
														<div class="col-sm-3">
															<p><i class="fa fa-calendar text-primary" aria-hidden="true"></i> <?php echo $note_date; ?> | <i class="fa fa-clock-o text-primary" aria-hidden="true"></i> <?php echo $note_time; ?></p>
															<p><i class="fa fa-user text-primary" aria-hidden="true"></i> <?php echo $employee_firstname; ?> <?php echo $employee_lastname; ?></p>
														</div>
														<div class="col-sm-7">
															<p><?php echo $note_txt; ?></p>
														</div>
														<div class="col-sm-2 text-right">
															<a href="#" data="<?php getSiteURL(); ?>employees?page=del_note&id=<?php echo $note_id; ?>" data-toggle="modal" data-target="#deleteNoteModal" class="delete_note btn material-btn material-btn_danger main-container__column"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i></a>
															<script>
																$(".delete_note").click(function () {
																	var addressValue = $(this).attr("data");
																	document.getElementById("delete_note_link").href = addressValue;
																});
															</script>
															<!-- Modal -->
															<div class="modal material-modal material-modal_danger fade text-left" id="deleteNoteModal">
																<div class="modal-dialog">
																	<div class="modal-content material-modal__content">
																		<div class="modal-header material-modal__header">
																			<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																			<h4 class="modal-title material-modal__title">Brisanje</h4>
																		</div>
																		<div class="modal-body material-modal__body">
																			<p>Jeste li sigurni da želite obrisati bilješku?</p>
																		</div>
																		<div class="modal-footer material-modal__footer">
																			<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
																			<a id="delete_note_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<hr />
													<?php } ?>
												</div>
											</div>
										</div>
										<?php } ?>
									</div>
								</div>
								<div class="tab-pane fade" id="documents">
									<script type="text/javascript">
										$(document).ready(function() {
											$('#idk_table_documents').DataTable({

												responsive: true,

												"order": [[ 0, "asc" ]],

												 "bAutoWidth": false,

												"aoColumns": [
														{ "width": "10%" },
														{ "width": "40%" },
														{ "width": "40%" },
														{ "width": "5%", "bSortable": false },
														{ "width": "5%", "bSortable": false }
													]
											});
										} );
									</script>
									<table id="idk_table_documents" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th>Datum</th>
												<th>Naziv</th>
												<th>Opis</th>
												<th>Preuzimanje</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<?php
												$query_doc = $db->prepare("
																SELECT document_id, document_name, document_desc, document_file, document_icon, document_datetime
																FROM idk_documents
																WHERE document_group = :document_group AND document_dataid = :document_dataid");

												$query_doc->execute(array(
															':document_group' => 1,
															':document_dataid' => $employee_id));

												while($row_doc = $query_doc->fetch()){

													$document_id = $row_doc['document_id'];
													$document_name = $row_doc['document_name'];
													$document_desc = $row_doc['document_desc'];
													$document_file = $row_doc['document_file'];
													$document_datetime = date('d.m.Y.', strtotime($row_doc['document_datetime']));

													if($row_doc['document_icon'] == "jpg"){
														$document_icon = '<i class="fa fa-file-image-o fa-lg" aria-hidden="true"></i>';
													}elseif($row_doc['document_icon'] == "pdf"){
														$document_icon = '<i class="fa fa-file-pdf-o fa-lg" aria-hidden="true"></i>';
													}elseif($row_doc['document_icon'] == "doc" OR $row_doc['document_icon'] == "docx"){
														$document_icon = '<i class="fa fa-file-word-o fa-lg" aria-hidden="true"></i>';
													}elseif($row_doc['document_icon'] == "xls" OR $row_doc['document_icon'] == "xlsx"){
														$document_icon = '<i class="fa fa-file-excel-o fa-lg" aria-hidden="true"></i>';
													}elseif($row_doc['document_icon'] == "txt"){
														$document_icon = '<i class="fa fa-file-text-o fa-lg" aria-hidden="true"></i>';
													}elseif($row_doc['document_icon'] == "ppt" OR $row_doc['document_icon'] == "pptx"){
														$document_icon = '<i class="fa fa-file-powerpoint-o fa-lg" aria-hidden="true"></i>';
													}else{
														$document_icon = '<i class="fa fa-file-o fa-lg" aria-hidden="true"></i>';
													}
											?>
											<tr>
												<td class="text-center"><?php echo $document_datetime; ?></td>
												<td><?php echo $document_name; ?></td>
												<td><?php echo $document_desc; ?></td>
												<td class="text-center"><a href="<?php getSiteURL(); ?>files/employees/<?php echo $document_file; ?>" class="btn material-btn material-btn_success main-container__column" target="_BLANK"><?php echo $document_icon; ?></a></td>
												<td class="text-center"><a href="#" data="<?php getSiteURL(); ?>employees?page=del_doc&id=<?php echo $document_id; ?>" data-toggle="modal" data-target="#deleteDocModal" class="delete_doc btn material-btn material-btn_danger main-container__column"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i></a></td>
											</tr>
											<?php } ?>
											<script>
												$(".delete_doc").click(function () {
													var addressValue = $(this).attr("data");
													document.getElementById("delete_doc_link").href = addressValue;
												});
											</script>
											<!-- Modal -->
											<div class="modal material-modal material-modal_danger fade" id="deleteDocModal">
												<div class="modal-dialog">
													<div class="modal-content material-modal__content">
														<div class="modal-header material-modal__header">
															<button class="close material-modal__close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title material-modal__title">Brisanje</h4>
														</div>
														<div class="modal-body material-modal__body">
															<p>Jeste li sigurni da želite obrisati dokument?</p>
														</div>
														<div class="modal-footer material-modal__footer">
															<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
															<a id="delete_doc_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
														</div>
													</div>
												</div>
											</div>
										</tbody>
									</table>
								</div>
								<div class="tab-pane fade" id="important">
									<form action="<?php getSiteURL(); ?>do.php?form=save_employee_inote" method="post" role="form" class="form-horizontal">
										<input type="hidden" name="employee_id" value="<?php echo $employee_id; ?>" />
										<div class="form-group text-right">
											<div class="col-md-offset-1 col-sm-10">
												<div class="form-group materail-input-block materail-input-block_success">
													<textarea class="form-control materail-input material-textarea" name="employee_inote" placeholder="Važne bilješke" rows="8" required><?php echo $employee_inote; ?></textarea>
													<span class="materail-input-block__line"></span>
												</div>
												<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-save" aria-hidden="true"></i> <span>Snimi</span></button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
				break;

				case "del_doc":
					if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status))){

						$document_id = $_GET['id'];

						//Get document name, dataid and delete document
						$doc_open_query = $db->prepare("
													SELECT document_name, document_file, document_dataid
													FROM idk_documents
													WHERE document_id = :document_id");

						$doc_open_query->execute(array(
												':document_id' => $document_id));

						$doc_open = $doc_open_query->fetch();

							$document_name = $doc_open['document_name'];
							$document_file = $doc_open['document_file'];
							$document_dataid = $doc_open['document_dataid'];

							unlink("files/employees/" . $document_file);

						//Add to LOGS
						$log_desc = "Obrisao dokument: " . $document_name . " ";
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

						//Delete document from db
						$doc_del_query = $db->prepare("
													DELETE FROM idk_documents
													WHERE document_id = :document_id");

						$doc_del_query->execute(array(
											':document_id' => $document_id));

						header("Location: " . getSiteURLr() . "employees?page=open&id=$document_dataid&mess=3");

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

				case "del_note":
					if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status))){

						$note_id = $_GET['id'];

						//Get note_txt and note_dataid
						$note_open_query = $db->prepare("
													SELECT note_txt, note_dataid
													FROM idk_notes
													WHERE note_id = :note_id");

						$note_open_query->execute(array(
												':note_id' => $note_id));

						$note_open = $note_open_query->fetch();

							$note_txt = $note_open['note_txt'];
							$note_dataid = $note_open['note_dataid'];

						//Add to LOGS
						$log_desc = "Obrisao bilješku: " . $note_txt . " ";
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

						//Delete document from db
						$doc_del_query = $db->prepare("
													DELETE FROM idk_notes
													WHERE note_id = :note_id");

						$doc_del_query->execute(array(
											':note_id' => $note_id));

						header("Location: " . getSiteURLr() . "employees?page=open&id=$note_dataid&mess=4");

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
					if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status))){

						$lg_id = $_GET['id'];


						$query_get = $db->prepare("
										SELECT lg_id, lg_url, lg_desc, lg_datetime
										FROM idk_link_generator
										WHERE lg_id = :lg_id
										");

						$query_get->execute(array(
							":lg_id" => $lg_id
						));
						$row_get = $query_get->fetch();

							$lg_id = $row_get['lg_id'];
							$lg_url = $row_get['lg_url'];
							$lg_desc = $row_get['lg_desc'];

						//Save
						$query = $db->prepare("
										UPDATE idk_link_generator
										SET lg_status = :lg_status
										WHERE lg_id = :lg_id");

						$query->execute(array(
									':lg_status' => 1,
									':lg_id' => $lg_id));

						//Add to LOGS
						$log_desc = "Arhivirao generisan link: " . $lg_desc . "";
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


						header("Location: link_generator?page=list&mess=4");

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
<?php }else{			
			echo '
				<br/>
				<div class="alert material-alert material-alert_danger">
					<h4>NEMATE PRIVILEGIJE!</h4>
					<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
					<br />
				</div>
';} ?>