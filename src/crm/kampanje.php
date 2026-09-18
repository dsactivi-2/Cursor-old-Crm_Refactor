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
	<title>Kampanje | <?php getTitle(); ?></title>

	<?php include('includes/head.php'); 
	if (in_array($getUserIp, $getIpWhiteList)){
	?>
	<style>
		.campaign-category, .campaign-category-sm{
			font-weight: bold;
			background-color: #ebe2e2;
			padding: 10px;
			border-radius: 20px;
			padding-right: 15px;
			padding-left: 15px;
			border: 2px;
			border-style: solid;
			font-size: 2rem;
			text-decoration:none;
		}
		.campaign-category-sm{
			font-weight: bold;
			background-color: #ebe2e2;
			padding: 5px;
			border-radius: 20px;
			padding-right: 10px;
			padding-left: 10px;
			border: 2px;
			border-style: solid;
			font-size: 1.5rem;
			text-decoration:none;
		}
		.campaign-category:hover{
			cursor:pointer;
			text-decoration:none;
			color: black;
			border-color: black;
		}
		.campaign-category-sm:hover{
			cursor:pointer;
			text-decoration:none;
			color: black;
			border-color: black;
			z-index:1000;
		}
		.campaign-A{
			color: #dd0f0f;
			border-color: #dd0f0f;
		}
		.campaign-B{
			color: #0f23dd;
			border-color: #0f23dd;
		}
		.campaign-C{
			color: #0fdd0f;
			border-color: #0fdd0f;
		}
	</style>
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
					<h1><i class="fa fa-retweet idk_color_green" aria-hidden="true"></i> Kampanje</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					 <?php
					 	$approved_employees = [222, 25, 234];
					 	if(in_array($logged_employee_id, $approved_employees)){?>
						<a href="/facebook_leads.php?page=upload_leads" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-file-o" aria-hidden="true"></i> <span>Import</span></a>
                    <?php }
						if((in_array( "7" , $employee_status)) OR (in_array( "1" , $employee_status))){
					?>
						<a href="/statistikaIzvora.php" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-file-o" aria-hidden="true"></i> <span>Statistika</span></a>
					<?php
						}
					?>
					<a href="<?php getSiteURL(); ?>kampanje?page=add" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></a>
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
										echo '<div class="alert material-alert material-alert_success">Uspješno ste kreirali novu kampanju.</div>';
									}elseif($mess == 2){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili kampanju.</div>';
									}
								?>
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_table').DataTable({

											responsive: true,

											"order": [[ 0, "desc" ]],

											 "bAutoWidth": false,

											"aoColumns": [
													{ "width": "3%" },
													{ "width": "10%" },
													{ "width": "23%" },
													{ "width": "5%" },
													{ "width": "5%" },
													{ "width": "5%" },
													{ "width": "3%" },
													{ "width": "26%" },
													{ "width": "5%" },
													{ "width": "8%" },
													{ "width": "7%", "bSortable": false },
													{ "width": "0%", "visible": false, }
												]
										});
									} );
								</script>
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
											<th>Naziv</th>
											<th>Poruka kampanje</th>
											<th class="text-center">Pregleda</th>
											<th class="text-center">Prijava</th>
											<th class="text-center">Ponovnih Prijava</th>
											<th class="text-center">Država</th>
											<th class="text-center">Link prijave</th>
											<th class="text-center">Kategorija</th>
											<th class="text-center">Datum kreiranja</th>
											<th></th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
															SELECT kd_category, kd_id, kd_naziv, kd_skraceni_naziv, kd_poruka_kampanje, kd_jezik_forme, kd_drzava, kd_broj_pregleda, kd_broj_prijavljenih, kd_datum_kreiranja
															FROM idk_kampanje_dipl
															WHERE kd_status = 1
															ORDER BY kd_naziv DESC
															");

											$query->execute();
											$i=1;
											while($row = $query->fetch()){

												$kd_category = $row['kd_category'];
												$kd_id = $row['kd_id'];
												$kd_naziv = $row['kd_naziv'];
												$kd_skraceni_naziv = $row['kd_skraceni_naziv'];
												$kd_poruka_kampanje = $row['kd_poruka_kampanje'];
												$kd_jezik_forme = $row['kd_jezik_forme'];
												$kd_drzava = $row['kd_drzava'];
												$kd_broj_pregleda = $row['kd_broj_pregleda'];
												$kd_broj_prijavljenih = '<span class="label label-succes material-label material-label_success main-container__column">'.$row['kd_broj_prijavljenih'].'</span>';
												
												$kd_datum_kreiranja = $row['kd_datum_kreiranja'];
												$datum_ispis = date('d-m-Y', strtotime($kd_datum_kreiranja));
												
												$href_link_prijave = "https://join.job-step.com/nostrification/".$kd_id."/".$kd_jezik_forme;
												$span_link_prijave = '<span style="margin-right:0.5rem" class="label cursor label-success material-label material-label_success main-container__column">';
												
												
												// OD 09.04.2021 KORISTI SE DRUGA FORMA ZA DIPL KAMPANJE 
												// if( date(strtotime($kd_datum_kreiranja)) > date(strtotime("09-04-2021"))){ 
													
												// 		$href_link_prijave = "https://job-step.net/usluga/priznavanje-diplome/3/".$kd_id."/".$kd_jezik_forme;
												// }

										?>
										<tr>
											<td class="text-center"><?php echo $kd_id; ?></td>
											<td title="<?php echo $kd_naziv; ?>"><a href="<?php getSiteURL(); ?>kampanje?page=open&id=<?php echo $kd_id; ?>"><?php echo $kd_skraceni_naziv; ?></a></td>
											<td><?php echo $kd_poruka_kampanje; ?></td>
											<td class="text-center"><span class="label label-warning material-label material-label_warning main-container__column text-center"><?php echo $kd_broj_pregleda; ?></span></td>
											<td class="text-center"><a href="<?php getSiteURL(); ?>kampanje?page=statistike&id=<?php echo $kd_id; ?>"><?php echo $kd_broj_prijavljenih; ?></a></td>
											<td class="text-center"><a href="<?php getSiteURL(); ?>kampanje?page=statistike_ponovnih_prijava&id=<?php echo $kd_id; ?>"><span class="label label-danger material-label material-label_danger main-container__column text-center"><?php echo countKandidatiPonovnePrijave($kd_id); ?></span></a></td>
											<td class="text-center"><?php echo $kd_drzava; ?></td>
											<td class="text-left"><a href="<?php echo $href_link_prijave; ?>" target="_BLANK" ><?php echo $span_link_prijave; ?><i class="fa fa-external-link" aria-hidden="true"></i></span><?php echo $href_link_prijave; ?></a></td>
											<td style = "text-align:center;">
												<a id = "current_campaign_<?php echo $kd_id; ?>" category = "<?php echo $kd_category;?>" campaign_id = "<?php echo $kd_id; ?>" class = "campaign-category campaign-<?php echo $kd_category;?>"><?php echo $kd_category; ?></a>
												<div id = "pick_campaign_<?php echo $kd_id; ?>" style = "display:none;">
													<a class = "campaign-category-sm campaign-A" campaign_id = "<?php echo $kd_id; ?>" category = "A">A</a>
													<a class = "campaign-category-sm campaign-B" campaign_id = "<?php echo $kd_id; ?>" category = "B">B</a>
													<a class = "campaign-category-sm campaign-C" campaign_id = "<?php echo $kd_id; ?>" category = "C">C</a>
												</div>
											</td>
											<td class="text-center"><span class="label label-default material-label material-label_default main-container__column"><?php echo $datum_ispis; ?> </span></td>
											<td class="text-center">
												<div class="btn-group material-btn-group">
													<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
													<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
														
														<li><a href="<?php getSiteURL(); ?>kampanje?page=open&id=<?php echo $kd_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
														<li><a href="<?php getSiteURL(); ?>kampanje?page=edit&id=<?php echo $kd_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi</a></li>
														<?php if(in_array("1",$employee_status)){ ?>
														<li><a href="<?php getSiteURL(); ?>kampanje?page=statistike&id=<?php echo $kd_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Statistike</a></li>
														<?php } ?>
														<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>kampanje?page=archive&id=<?php echo $kd_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>

													</ul>
												</div>
											</td>
											<td></td>
										</tr>
										<?php } ?>
										<script>
											$('.campaign-category').on('click', function(){
												var kd_id = $(this).attr('campaign_id');
												$('#current_campaign_'+kd_id).fadeOut(200,function(){
													$('#pick_campaign_'+kd_id).fadeIn(200);
												});
											});
											$('.campaign-category-sm').on('click', function(){
												var kd_id = $(this).attr('campaign_id');
												var category = $(this).attr('category');
												$('#pick_campaign_'+kd_id).fadeOut(200, function(){
													$.ajax({
														url: 'ajax_data.php?page=update_campaign_category',
														type: 'POST',
														dataType: 'html',
														data:{
															'kd_id'		: kd_id,
															'category'	: category

														},
														success : function (response){
															$('#current_campaign_'+kd_id).empty().append(category);
															$('#current_campaign_'+kd_id).attr('category', category);
															$('#current_campaign_'+kd_id).removeClass('campaign-A');
															$('#current_campaign_'+kd_id).removeClass('campaign-B');
															$('#current_campaign_'+kd_id).removeClass('campaign-C');
															$('#current_campaign_'+kd_id).addClass('campaign-'+category);
															$('#current_campaign_'+kd_id).fadeIn(200,function(){
																$('#current_campaign_'+kd_id).effect('highlight');
															});
														},
														error: function (xhr, ajaxOptions, thrownError) {
															alert(xhr.status);
															alert(thrownError);
														}
													});
												});
												
												
											});
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
														<p>Jeste li sigurni da želite arhivirati zaposlenika?</p>
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
					if((in_array( "7" , $employee_status)) OR (in_array( "1" , $employee_status))){
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-retweet idk_color_green" aria-hidden="true"></i> Kreiraj novu kampanju</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>kampanje?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-md-offset-1 col-md-8">
								<form action="<?php getSiteURL(); ?>do.php?form=kreiraj_kampanju" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<div class="form-group">
										<label for="kd_naziv" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kd_naziv" id="kd_naziv" placeholder="Google Search DIPL keywords BiH" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kd_skraceni_naziv" class="col-sm-3 control-label"><span class="text-danger">*</span> Skraceni naziv:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kd_skraceni_naziv" id="kd_skraceni_naziv" placeholder="FB BIH AD1" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kd_poruka_kampanje" class="col-sm-3 control-label"><span class="text-danger">*</span> Poruka kampanje:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kd_poruka_kampanje" id="kd_poruka_kampanje" placeholder="Upkros koroni u Njemacku" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kd_drzava" class="col-sm-3 control-label"><span class="text-danger">*</span> Drzava:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="kd_drzava" data-live-search="true" name="kd_drzava" required>
												<option value="BiH">BiH</option>
												<option value="Srbija">Srbija</option>
												<option value="Njemacka">Njemačka</option>
												<option value="N/A">NEDEFINISANO</option>
											</select>
										</div>
									</div>									
									
									
									<div class="form-group">
										<label for="kd_" class="col-sm-3 control-label">Slike kampanje:</label>
										<div class="col-sm-9">
											<div class="fileinput fileinput-new" data-provides="fileinput">
												<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;"></div>
												<div>
													<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografije</span><span class="fileinput-exists">Promijeni</span><input type="file" name="kampanja_image[]" multiple="" required></span>
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
									
									<br />
									<div class="form-group">
										<div class="col-sm-offset-2 col-sm-10 text-right">
											<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span>Kreiraj</span></button>
											<br /><small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
										</div>
									</div>
								</form>
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

				case "edit":
					if((in_array( "7" , $employee_status)) OR (in_array( "1" , $employee_status))){

						$kd_id = $_GET['id'];

						$query = $db->prepare("
										SELECT *
										FROM idk_kampanje_dipl
										WHERE kd_id = :kd_id
										");

						$query->execute(array(
							":kd_id" => $kd_id
						));
						$row = $query->fetch();

							$kd_id = $row['kd_id'];
							$kd_naziv = $row['kd_naziv'];
							$kd_skraceni_naziv = $row['kd_skraceni_naziv'];
							$kd_drzava = $row['kd_drzava'];
							$kd_poruka_kampanje = $row['kd_poruka_kampanje'];
							$kd_slike = $row['kd_slike'];
							$slike = explode(",", $kd_slike);



		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-retweet idk_color_green" aria-hidden="true"></i> Uredi kampanju</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>kampanje?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-md-offset-1 col-md-8">
								<form action="<?php getSiteURL(); ?>do.php?form=edit_kampanja" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
								<input type="hidden" name="kd_id" value="<?php echo $kd_id ?>">
									<div class="form-group">
										<label for="kd_naziv" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kd_naziv" id="kd_naziv" value="<?php echo $kd_naziv; ?>" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kd_skraceni_naziv" class="col-sm-3 control-label"><span class="text-danger">*</span> Skraćeni naziv:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kd_skraceni_naziv" id="kd_skraceni_naziv" value="<?php echo $kd_skraceni_naziv; ?>" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kd_poruka_kampanje" class="col-sm-3 control-label"><span class="text-danger">*</span> Poruka kampanje:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kd_poruka_kampanje" id="kd_poruka_kampanje" value="<?php echo $kd_poruka_kampanje; ?>" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kd_drzava" class="col-sm-3 control-label"><span class="text-danger">*</span> Država:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="kd_drzava" data-live-search="true" name="kd_drzava" required>
												<option <?php if($kd_drzava == "BiH"){echo "selected";} ?> value="BiH">BiH</option>
												<option <?php if($kd_drzava == "Srbija"){echo "selected";} ?> value="Srbija">Srbija</option>
												<option <?php if($kd_drzava == "Njemacka"){echo "selected";} ?> value="Njemacka">Njemačka</option>
											</select>
										</div>
									</div>
									
									<div class="form-group">
										<label for="unesene_slike" class="col-sm-3 control-label">Slike kampanje:</label>
										<input type="hidden" name="kd_slike" id="kd_slike" value="<?php echo $kd_slike; ?>" required>
												
										<div class="row col-sm-9" >
											<?php foreach($slike as $slika_path){ ?>
											<div class="col-md-4" style="box-shadow: 1 2px 5px 0 rgba(0, 0, 0, 0.298039); height:200px;">
												<img class="" style=" max-width: 100%; height: 100%; padding:5px;" src="<?php getSiteUrl(); ?>files/kampanje/<?php echo $slika_path; ?>">
											</div>
											<?php } ?>
											
										</div>
										
									</div>
									<div class="form-group">
										<label for="idk_urlimg_prijave" class="col-sm-3 control-label">Dodaj novu sliku:</label>
										
										<div class="col-sm-9">
											<div class="fileinput fileinput-new" data-provides="fileinput">
												<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;"></div>
												<div>
													<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografije</span><span class="fileinput-exists">Promijeni</span><input type="file" name="kampanja_image[]" multiple="" ></span>
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
									
									<br />
									<div class="form-group">
										<div class="col-sm-offset-2 col-sm-10 text-right">
											<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span>Uredi</span></button>
											<br /><small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
										</div>
									</div>
								</form>
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
				
				case "statistike":
					if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status))){

						// $lg_id = $_GET['id'];

						// $query = $db->prepare("
										// SELECT lg_id, lg_link_prijave, lg_url, lg_desc, lg_nalogid, lg_language, lg_troskovi
										// FROM idk_link_generator
										// WHERE lg_id = :lg_id
										// ");

						// $query->execute(array(
							// ":lg_id" => $lg_id
						// ));
						// $row = $query->fetch();

						// $lg_id = $row['lg_id'];
						// $lg_link_prijave = $row['lg_link_prijave'];
						// $lg_url = $row['lg_url'];
						// $lg_desc = $row['lg_desc'];
						// $lg_nalogid = $row['lg_nalogid'];
						// $lg_language = $row['lg_language'];
						// $lg_troskovi = $row['lg_troskovi'];
						
						
						$kampanja_id = $_GET['id'];

							$query = $db->prepare("
											SELECT *
											FROM idk_kampanje_dipl
											WHERE kd_id = :kd_id");

							$query->execute(array(
										':kd_id' => $kampanja_id));

							$row = $query->fetch();

							$kd_naziv = $row['kd_naziv'];
							$kd_skraceni_naziv = $row['kd_skraceni_naziv'];
							$kd_poruka_kampanje = $row['kd_poruka_kampanje'];
							$kd_jezik_forme = $row['kd_jezik_forme'];
							$kd_drzava = $row['kd_drzava'];
							$kd_slike = $row['kd_slike'];
							$kd_datum_kreiranja = date('d.m.Y.', strtotime($row['kd_datum_kreiranja']));
							
							$slike = explode(",", $kd_slike);
						



		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-retweet idk_color_green" aria-hidden="true"></i> Statistike za <?php echo $kd_naziv; ?></h1>
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
										"aoColumns": [
												{ "width": "5%" },
												{ "width": "17.5%" },
												{ "width": "15%" },
												{ "width": "17.5%" },
												{ "width": "15%" },
												{ "width": "10%" },
												{ "width": "10%" },
											]
									});
								});
							</script>
							<table id="link_stats_candidates" class="display" cellspacing="0" width="100%">
								<thead>
									<tr>
										<th>#</th>
										<th>Ime i prezime</th>
										<th>Kontakt</th>
										<th>E-mail</th>
										<th class="text-center">Status</th>
										<th class="text-center">Vrijeme kreiranja</th>
										<th class="text-center">Zadužen</th>
									</tr>
								</thead>
								<tbody>
								<?php 
									//SVI DIPL
									$query_DIPL = $db->prepare("
												SELECT id_broj_nd_kandidata, ime_nd_kandidata, prezime_nd_kandidata, mobilni_nd_kandidata, email_nd_kandidata, vrijeme_kreiranja_nd_kandidata, dodao_zaposlenik_nd_kandidata, status_nd_kandidata, pstatus_nd_kandidata, povijest_nd_kandidata, povijest_vrsta_nd_kandidata, zaduzen_zaposlenik_nd_kandidata, partner_nd_status
												FROM idk_nd_kandidata
												WHERE kampanja_id = :kampanja_id");
												
									$query_DIPL->execute(array(
													':kampanja_id' => $kampanja_id));
									// MOGUCA ISPLATA
									while($row_dipl = $query_DIPL->fetch()){
										
										$id_broj_nd_kandidata = $row_dipl["id_broj_nd_kandidata"]; 
										$ime_nd_kandidata_ispis = $row_dipl["ime_nd_kandidata"]; 
										$prezime_nd_kandidata_ispis = $row_dipl["prezime_nd_kandidata"];
										$mobilni_nd_kandidata_ispis = $row_dipl['mobilni_nd_kandidata'];
										$email_nd_kandidata_ispis = $row_dipl['email_nd_kandidata'];
										$vrijeme_kreiranja_nd_kandidata_ispis = date('d.m.Y H:i', strtotime($row_dipl['vrijeme_kreiranja_nd_kandidata']));
										$dodao_zaposlenik_nd_kandidata_ispis = getZaposlenikimeR($row_dipl['dodao_zaposlenik_nd_kandidata']);
										$zaduzen_zaposlenik_nd_kandidata_ispis = getZaposlenikimeR($row_dipl['zaduzen_zaposlenik_nd_kandidata']);
										$status_nd_kandidata_ispis = $row_dipl['status_nd_kandidata'];
										$pstatus_nd_kandidata_ispis = $row_dipl['pstatus_nd_kandidata'];
										$povijest_nd_kandidata_ispis = $row_dipl['povijest_nd_kandidata'];
										$povijest_vrsta_nd_kandidata_ispis = $row_dipl['povijest_vrsta_nd_kandidata'];		
					
										?>
										<tr>
											<td class="text-center"><?php echo $id_broj_nd_kandidata; ?></td>
											<td><a href="<?php getSiteUrl(); ?>nostrifikacija_diploma?page=otvori_ND_kandidata&id=<?php echo $id_broj_nd_kandidata; ?>"><?php echo $ime_nd_kandidata_ispis." ".$prezime_nd_kandidata_ispis; ?></a></td>
											<td><?php echo $mobilni_nd_kandidata_ispis; ?></td>
											<td style = "word-break: break-all;"><?php echo $email_nd_kandidata_ispis; ?></td>
											<td class="text-center" style = "word-break: break-all;"><?php echo getStatusDIPLKandidatR( $status_nd_kandidata_ispis, $pstatus_nd_kandidata_ispis  ); ?></td>
											<td class="text-center" data-order="<?php echo $row_dipl['vrijeme_kreiranja_nd_kandidata']; ?>" ><?php echo $vrijeme_kreiranja_nd_kandidata_ispis; ?></td>
											<td class="text-center"><?php echo $zaduzen_zaposlenik_nd_kandidata_ispis; ?></td> 
										</tr>
									<?php } ?>
									
								</tbody>
							</table>
							<?php// $balans = $ukupna_suma - $lg_troskovi; ?>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12" style="margin-top:2rem">
					<div class="content_box">
						<h3 style="margin-bottom:3rem">Statistike kandidata vezanih za ovu kampanju: </h2>
						<hr>
						<div class="row statistike-kampanje">
						
							<div class="chart-div" style="min-width:105rem; margin-right:1rem" >
								<canvas class="bg-big-stone-800" id="plot"></canvas>
								<?php 

									$q_getcountstatusi=$db->prepare('
										select 
										sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=1 THEN 1 else 0 END) AS statLead,
										sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=6 THEN 1 else 0 END) AS Nk1,
										sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=2 THEN 1 else 0 END) AS Nk3,
										sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=3 THEN 1 else 0 END) AS Zld,
										sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=4 THEN 1 else 0 END) AS Nzld,
										sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=5 THEN 1 else 0 END) AS Uobld,
										sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=7 THEN 1 else 0 END) AS NL1,
										sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=8 THEN 1 else 0 END) AS NL2,
										sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=9 THEN 1 else 0 END) AS TZ,
										sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=10 THEN 1 else 0 END) AS TOs,
										sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=11 THEN 1 else 0 END) AS LNL,
										sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=12 THEN 1 else 0 END) AS LNZ,
										sum(case when status_nd_kandidata=2 THEN 1 else 0 END) AS Prdok,
										sum(case when status_nd_kandidata=3 THEN 1 else 0 END) AS Pospos,
										sum(case when status_nd_kandidata=4 THEN 1 else 0 END) AS Obrada,
										sum(case when status_nd_kandidata=5 THEN 1 else 0 END) AS Dopdok,
										sum(case when status_nd_kandidata=6 THEN 1 else 0 END) AS Zavrs,
										sum(case when status_nd_kandidata=7 THEN 1 else 0 END) AS Arhiva
									FROM 
										idk_nd_kandidata
										WHERE kampanja_id = :kampanja_id'
										);
									$q_getcountstatusi->execute(array(
										':kampanja_id' => $kampanja_id
									));
									$rowStatistika = $q_getcountstatusi->fetch();
																
									$uk_br_st11_ispis2=$rowStatistika['statLead'];
									$uk_br_st16_ispis2=$rowStatistika['Nk1'];
									$uk_br_st12_ispis2=$rowStatistika['Nk3'];
									$uk_br_st13_ispis2=$rowStatistika['Zld'];
									$uk_br_st14_ispis2=$rowStatistika['Nzld'];
									$uk_br_st15_ispis2=$rowStatistika['Uobld'];
									$uk_br_st17_ispis2=$rowStatistika['NL1'];
									$uk_br_st18_ispis2=$rowStatistika['NL2'];
									$uk_br_st19_ispis2=$rowStatistika['TZ'];
									$uk_br_st20_ispis2=$rowStatistika['TOs'];
									$uk_br_st21_ispis2=$rowStatistika['LNL'];
									$uk_br_st22_ispis2=$rowStatistika['LNZ'];
									$uk_br_st2_ispis2=$rowStatistika['Prdok'];
									$uk_br_st3_ispis2=$rowStatistika['Pospos'];
									$uk_br_st4_ispis2=$rowStatistika['Obrada'];
									$uk_br_st5_ispis2=$rowStatistika['Dopdok'];
									$uk_br_st6_ispis2=$rowStatistika['Zavrs'];
									$uk_br_st7_ispis2=$rowStatistika['Arhiva'];
									$ukupan_br_ispis2=$uk_br_st11_ispis2 + $uk_br_st12_ispis2 + $uk_br_st13_ispis2 + $uk_br_st14_ispis2 + $uk_br_st15_ispis2 + $uk_br_st16_ispis2 + $uk_br_st17_ispis2 + $uk_br_st18_ispis2 + $uk_br_st19_ispis2 + $uk_br_st20_ispis2 + $uk_br_st21_ispis2 + $uk_br_st22_ispis2 + $uk_br_st2_ispis2 + $uk_br_st3_ispis2 + $uk_br_st4_ispis2 + $uk_br_st5_ispis2 + $uk_br_st6_ispis2 + $uk_br_st7_ispis2;

									//Racunanje postotka
									$proc_st11_ispis2 = number_format((($uk_br_st11_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st12_ispis2 = number_format((($uk_br_st12_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st16_ispis2 = number_format((($uk_br_st16_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st13_ispis2 = number_format((($uk_br_st13_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st14_ispis2 = number_format((($uk_br_st14_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st15_ispis2 = number_format((($uk_br_st15_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st17_ispis2 = number_format((($uk_br_st17_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st18_ispis2 = number_format((($uk_br_st18_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st19_ispis2 = number_format((($uk_br_st19_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st20_ispis2 = number_format((($uk_br_st20_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st21_ispis2 = number_format((($uk_br_st21_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st22_ispis2 = number_format((($uk_br_st22_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st2_ispis2 = number_format((($uk_br_st2_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st3_ispis2 = number_format((($uk_br_st3_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st4_ispis2 = number_format((($uk_br_st4_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st5_ispis2 = number_format((($uk_br_st5_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st6_ispis2 = number_format((($uk_br_st6_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st7_ispis2 = number_format((($uk_br_st7_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									
								?>
								<script>
								  $.getScript( "https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.1.1/chart.min.js", function() {
									var ctx = document.getElementById('plot').getContext('2d');
									const data = {
									  labels: [
										'Lead (<?php echo $uk_br_st11_ispis2; ?>) - <?php echo $proc_st11_ispis2; ?>%',
										'Neuspješan Kontakt 1 (<?php echo $uk_br_st16_ispis2; ?>) - <?php echo $proc_st16_ispis2; ?>%',
										'Neuspješan Kontakt 3 (<?php echo $uk_br_st12_ispis2; ?>) - <?php echo $proc_st12_ispis2; ?>%',
										'Zainteresiran Lead (<?php echo $uk_br_st13_ispis2; ?>) - <?php echo $proc_st13_ispis2; ?>%',
										'Nezainteresiran Lead (<?php echo $uk_br_st14_ispis2; ?>) - <?php echo $proc_st14_ispis2; ?>%',
										'U obradi Lead (<?php echo $uk_br_st15_ispis2; ?>) - <?php echo $proc_st15_ispis2; ?>%',
										'Neuspješan Lead 1 (<?php echo $uk_br_st17_ispis2; ?>) - <?php echo $proc_st17_ispis2; ?>%',
										'Neuspješan Lead 2 (<?php echo $uk_br_st18_ispis2; ?>) - <?php echo $proc_st18_ispis2; ?>%',
										'Termin Zainteresiran (<?php echo $uk_br_st19_ispis2; ?>) - <?php echo $proc_st19_ispis2; ?>%',
										'Termin Ostali (<?php echo $uk_br_st20_ispis2; ?>) - <?php echo $proc_st20_ispis2; ?>%',
										'Lead NL (<?php echo $uk_br_st21_ispis2; ?>) - <?php echo $proc_st21_ispis2; ?>%',
										'Lead NZ (<?php echo $uk_br_st22_ispis2; ?>) - <?php echo $proc_st22_ispis2; ?>%',
										'Prikupljanje dokumentacije (<?php echo $uk_br_st2_ispis2; ?>) - <?php echo $proc_st2_ispis2; ?>%',
										'Poslana pošta (<?php echo $uk_br_st3_ispis2; ?>) - <?php echo $proc_st3_ispis2; ?>%',
										'U obradi (<?php echo $uk_br_st4_ispis2; ?>) - <?php echo $proc_st4_ispis2; ?>%',
										'Dopuna dokumentacije (<?php echo $uk_br_st5_ispis2; ?>) - <?php echo $proc_st5_ispis2; ?>%',
										'Završen (<?php echo $uk_br_st6_ispis2; ?>) - <?php echo $proc_st6_ispis2; ?>%',
										'Arhiviran (<?php echo $uk_br_st7_ispis2; ?>) - <?php echo $proc_st7_ispis2; ?>%'
									],
									  datasets: [{
										data: [
											<?php echo $uk_br_st11_ispis2; ?>,
											<?php echo $uk_br_st16_ispis2; ?>,
											<?php echo $uk_br_st12_ispis2; ?>,
											<?php echo $uk_br_st13_ispis2; ?>,
											<?php echo $uk_br_st14_ispis2; ?>,
											<?php echo $uk_br_st15_ispis2; ?>,
											<?php echo $uk_br_st17_ispis2; ?>,
											<?php echo $uk_br_st18_ispis2; ?>,
											<?php echo $uk_br_st19_ispis2; ?>,
											<?php echo $uk_br_st20_ispis2; ?>,
											<?php echo $uk_br_st21_ispis2; ?>,
											<?php echo $uk_br_st22_ispis2; ?>,
											<?php echo $uk_br_st2_ispis2; ?>,
											<?php echo $uk_br_st3_ispis2; ?>,
											<?php echo $uk_br_st4_ispis2; ?>,
											<?php echo $uk_br_st5_ispis2; ?>,
											<?php echo $uk_br_st6_ispis2; ?>,
											<?php echo $uk_br_st7_ispis2; ?>
										],
										backgroundColor: [
											'rgb(131, 144, 152)',
											'rgb(0, 250, 251)',
											'rgb(0, 251, 83)',
											'rgb(14, 105, 115)',
											'rgb(191, 33, 75)',
											'rgb(199, 156, 255)',
											'rgb(183, 182, 249)',
											'rgb(207, 95, 250)',
											'rgb(117, 34, 99)',
											'rgb(204, 177, 122)',
											'rgb(128, 123, 70)',
											'rgb(214, 76, 10)',
											'rgb(242, 228, 46)',
											'rgb(64, 146, 217)',
											'rgb(139, 218, 242)',
											'rgb(242, 161, 46)',
											'rgb(104, 195, 104)',
											'rgb(243, 65, 60)'
										],
										hoverOffset: 4
									  }]
									};
									var myChart = new Chart(ctx, {
										type: 'pie',
										data: data,
										options: {
											responsive: true,
											maintainAspectRatio: false,
											plugins: {	
												legend: {
													position: 'right',
													align: 'center'
												},
												title: {
													display: true,
													position: 'top',
													align: 'end',
													text: 'Ukupno: (<?php echo $ukupan_br_ispis2; ?>)'
												},
											},
											animation: {
												animateScale: true,
												animateRotate: true
											}
										}
									});
								});
								</script>
							</div>
							<div class="uspjesno-izdanih-predracuna">
								<h1>Uspjesno izdanih predracuna (uplata): </h1>
								<?php 
									$ukupan_br_ispis2 = (
										$uk_br_st2_ispis2=$rowStatistika['Prdok'] +
										$uk_br_st3_ispis2=$rowStatistika['Pospos'] +
										$uk_br_st4_ispis2=$rowStatistika['Obrada'] +
										$uk_br_st5_ispis2=$rowStatistika['Dopdok'] +
										$uk_br_st6_ispis2=$rowStatistika['Zavrs']
										);
										
								?>
								<h1><b><?php echo $ukupan_br_ispis2; ?></b> </h1>
							</div>
							
						<style>
							.statistike-kampanje{
								display: flex;
								width:100%;
							}
							.uspjesno-izdanih-predracuna{
								display:flex;
								align-items: center;
							}
						</style>
						</div>
						<hr>
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
	case "statistike_ponovnih_prijava":
					if((in_array( "2" , $employee_status)) OR (in_array( "3" , $employee_status)) OR (in_array( "7" , $employee_status)) OR (in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status))){

						// $lg_id = $_GET['id'];

						// $query = $db->prepare("
										// SELECT lg_id, lg_link_prijave, lg_url, lg_desc, lg_nalogid, lg_language, lg_troskovi
										// FROM idk_link_generator
										// WHERE lg_id = :lg_id
										// ");

						// $query->execute(array(
							// ":lg_id" => $lg_id
						// ));
						// $row = $query->fetch();

						// $lg_id = $row['lg_id'];
						// $lg_link_prijave = $row['lg_link_prijave'];
						// $lg_url = $row['lg_url'];
						// $lg_desc = $row['lg_desc'];
						// $lg_nalogid = $row['lg_nalogid'];
						// $lg_language = $row['lg_language'];
						// $lg_troskovi = $row['lg_troskovi'];
						
						
						$kampanja_id = $_GET['id'];

							$query = $db->prepare("
											SELECT *
											FROM idk_kampanje_dipl
											WHERE kd_id = :kd_id");

							$query->execute(array(
										':kd_id' => $kampanja_id));

							$row = $query->fetch();

							$kd_naziv = $row['kd_naziv'];
							$kd_skraceni_naziv = $row['kd_skraceni_naziv'];
							$kd_poruka_kampanje = $row['kd_poruka_kampanje'];
							$kd_jezik_forme = $row['kd_jezik_forme'];
							$kd_drzava = $row['kd_drzava'];
							$kd_slike = $row['kd_slike'];
							$kd_datum_kreiranja = date('d.m.Y.', strtotime($row['kd_datum_kreiranja']));
							
							$slike = explode(",", $kd_slike);




		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-retweet idk_color_green" aria-hidden="true"></i> Statistike za ponovne kandidate kampanje: <?php echo $kd_naziv; ?></h1>
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
										"aoColumns": [
												{ "width": "5%", "bSortable": false },
												{ "width": "17.5%" },
												{ "width": "15%" },
												{ "width": "17.5%" },
												{ "width": "15%" },
												{ "width": "10%" },
												{ "width": "10%" },
											]
									});
								});
							</script>
							<table id="link_stats_candidates" class="display" cellspacing="0" width="100%">
								<thead>
									<tr>
										<th>#</th>
										<th>Ime i prezime</th>
										<th>Kontakt</th>
										<th>E-mail</th>
										<th class="text-center">Status</th>
										<th class="text-center">Vrijeme kreiranja</th>
										<th class="text-center">Zadužen</th>
									</tr>
								</thead>
								<tbody>
								<?php 
									//SVI DIPL
									$query_DIPL = $db->prepare("
												SELECT DISTINCT id_broj_nd_kandidata, ime_nd_kandidata, prezime_nd_kandidata, mobilni_nd_kandidata, email_nd_kandidata, vrijeme_kreiranja_nd_kandidata, dodao_zaposlenik_nd_kandidata, status_nd_kandidata, pstatus_nd_kandidata, povijest_nd_kandidata, povijest_vrsta_nd_kandidata, zaduzen_zaposlenik_nd_kandidata, partner_nd_status
												FROM idk_nd_kandidata
												INNER JOIN idk_ponovne_prijave ON idk_nd_kandidata.id_broj_nd_kandidata = idk_ponovne_prijave.id_kandidata
												WHERE kampanja_pp = :kampanja_pp");
												
									$query_DIPL->execute(array(
													':kampanja_pp' => $kampanja_id));
									// MOGUCA ISPLATA
									while($row_dipl = $query_DIPL->fetch()){
										
										$id_broj_nd_kandidata = $row_dipl["id_broj_nd_kandidata"]; 
										$ime_nd_kandidata_ispis = $row_dipl["ime_nd_kandidata"]; 
										$prezime_nd_kandidata_ispis = $row_dipl["prezime_nd_kandidata"];
										$mobilni_nd_kandidata_ispis = $row_dipl['mobilni_nd_kandidata'];
										$email_nd_kandidata_ispis = $row_dipl['email_nd_kandidata'];
										$vrijeme_kreiranja_nd_kandidata_ispis = date('d.m.Y H:i', strtotime($row_dipl['vrijeme_kreiranja_nd_kandidata']));
										$dodao_zaposlenik_nd_kandidata_ispis = getZaposlenikimeR($row_dipl['dodao_zaposlenik_nd_kandidata']);
										$zaduzen_zaposlenik_nd_kandidata_ispis = getZaposlenikimeR($row_dipl['zaduzen_zaposlenik_nd_kandidata']);
										$status_nd_kandidata_ispis = $row_dipl['status_nd_kandidata'];
										$pstatus_nd_kandidata_ispis = $row_dipl['pstatus_nd_kandidata'];
										$povijest_nd_kandidata_ispis = $row_dipl['povijest_nd_kandidata'];
										$povijest_vrsta_nd_kandidata_ispis = $row_dipl['povijest_vrsta_nd_kandidata'];		
										
								
										?>
										<tr>
											<td class="text-center"><?php echo $id_broj_nd_kandidata; ?></td>
											<td><a href="<?php getSiteUrl(); ?>nostrifikacija_diploma?page=otvori_ND_kandidata&id=<?php echo $id_broj_nd_kandidata; ?>"><?php echo $ime_nd_kandidata_ispis." ".$prezime_nd_kandidata_ispis; ?></a></td>
											<td><?php echo $mobilni_nd_kandidata_ispis; ?></td>
											<td style = "word-break: break-all;"><?php echo $email_nd_kandidata_ispis; ?></td>
											<td class="text-center" style = "word-break: break-all;"><?php echo getStatusDIPLKandidatR($status_nd_kandidata_ispis, $pstatus_nd_kandidata_ispis); ?></td> 
											<td class="text-center"><?php echo $vrijeme_kreiranja_nd_kandidata_ispis; ?></td>
											<td class="text-center"><?php echo $zaduzen_zaposlenik_nd_kandidata_ispis; ?></td> 
										</tr>
									<?php } ?>
									
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12" style="margin-top:2rem">
					<div class="content_box">
						<h3 style="margin-bottom:3rem">Statistike kandidata vezanih za ovu kampanju: </h2>
						<hr>
						<div class="row statistike-kampanje">
						
							<div class="chart-div" style="min-width:105rem; margin-right:1rem" >
								<canvas class="bg-big-stone-800" id="plot"></canvas>
								<?php 

									$q_getcountstatusi=$db->prepare('
										select id_broj_nd_kandidata,
										sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=1 THEN 1 else 0 END) AS statLead,
										sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=6 THEN 1 else 0 END) AS Nk1,
										sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=2 THEN 1 else 0 END) AS Nk3,
										sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=3 THEN 1 else 0 END) AS Zld,
										sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=4 THEN 1 else 0 END) AS Nzld,
										sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=5 THEN 1 else 0 END) AS Uobld,
										sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=7 THEN 1 else 0 END) AS NL1,
										sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=8 THEN 1 else 0 END) AS NL2,
										sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=9 THEN 1 else 0 END) AS TZ,
										sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=10 THEN 1 else 0 END) AS TOs,
										sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=11 THEN 1 else 0 END) AS LNL,
										sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=12 THEN 1 else 0 END) AS LNZ,
										sum(case when status_nd_kandidata=2 THEN 1 else 0 END) AS Prdok,
										sum(case when status_nd_kandidata=3 THEN 1 else 0 END) AS Pospos,
										sum(case when status_nd_kandidata=4 THEN 1 else 0 END) AS Obrada,
										sum(case when status_nd_kandidata=5 THEN 1 else 0 END) AS Dopdok,
										sum(case when status_nd_kandidata=6 THEN 1 else 0 END) AS Zavrs,
										sum(case when status_nd_kandidata=7 THEN 1 else 0 END) AS Arhiva
									FROM 
										idk_nd_kandidata
									INNER JOIN idk_ponovne_prijave ON idk_nd_kandidata.id_broj_nd_kandidata = idk_ponovne_prijave.id_kandidata
									WHERE kampanja_pp = :kampanja_pp'
										);
									$q_getcountstatusi->execute(array(
										':kampanja_pp' => $kampanja_id
									));
									$rowStatistika = $q_getcountstatusi->fetch();
																
									$uk_br_st11_ispis2=$rowStatistika['statLead'];
									$uk_br_st16_ispis2=$rowStatistika['Nk1'];
									$uk_br_st12_ispis2=$rowStatistika['Nk3'];
									$uk_br_st13_ispis2=$rowStatistika['Zld'];
									$uk_br_st14_ispis2=$rowStatistika['Nzld'];
									$uk_br_st15_ispis2=$rowStatistika['Uobld'];
									$uk_br_st17_ispis2=$rowStatistika['NL1'];
									$uk_br_st18_ispis2=$rowStatistika['NL2'];
									$uk_br_st19_ispis2=$rowStatistika['TZ'];
									$uk_br_st20_ispis2=$rowStatistika['TOs'];
									$uk_br_st21_ispis2=$rowStatistika['LNL'];
									$uk_br_st22_ispis2=$rowStatistika['LNZ'];
									$uk_br_st2_ispis2=$rowStatistika['Prdok'];
									$uk_br_st3_ispis2=$rowStatistika['Pospos'];
									$uk_br_st4_ispis2=$rowStatistika['Obrada'];
									$uk_br_st5_ispis2=$rowStatistika['Dopdok'];
									$uk_br_st6_ispis2=$rowStatistika['Zavrs'];
									$uk_br_st7_ispis2=$rowStatistika['Arhiva'];
									$ukupan_br_ispis2=$uk_br_st11_ispis2 + $uk_br_st12_ispis2 + $uk_br_st13_ispis2 + $uk_br_st14_ispis2 + $uk_br_st15_ispis2 + $uk_br_st16_ispis2 + $uk_br_st17_ispis2 + $uk_br_st18_ispis2 + $uk_br_st19_ispis2 + $uk_br_st20_ispis2 + $uk_br_st21_ispis2 + $uk_br_st22_ispis2 + $uk_br_st2_ispis2 + $uk_br_st3_ispis2 + $uk_br_st4_ispis2 + $uk_br_st5_ispis2 + $uk_br_st6_ispis2 + $uk_br_st7_ispis2;

									//Racunanje postotka
									$proc_st11_ispis2 = number_format((($uk_br_st11_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st12_ispis2 = number_format((($uk_br_st12_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st16_ispis2 = number_format((($uk_br_st16_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st13_ispis2 = number_format((($uk_br_st13_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st14_ispis2 = number_format((($uk_br_st14_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st15_ispis2 = number_format((($uk_br_st15_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st17_ispis2 = number_format((($uk_br_st17_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st18_ispis2 = number_format((($uk_br_st18_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st19_ispis2 = number_format((($uk_br_st19_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st20_ispis2 = number_format((($uk_br_st20_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st21_ispis2 = number_format((($uk_br_st21_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st22_ispis2 = number_format((($uk_br_st22_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st2_ispis2 = number_format((($uk_br_st2_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st3_ispis2 = number_format((($uk_br_st3_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st4_ispis2 = number_format((($uk_br_st4_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st5_ispis2 = number_format((($uk_br_st5_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st6_ispis2 = number_format((($uk_br_st6_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									$proc_st7_ispis2 = number_format((($uk_br_st7_ispis2 / $ukupan_br_ispis2)*100), 2, ',', '');
									
								?>
								<script>
								  $.getScript( "https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.1.1/chart.min.js", function() {
									var ctx = document.getElementById('plot').getContext('2d');
									const data = {
									  labels: [
										'Lead (<?php echo $uk_br_st11_ispis2; ?>) - <?php echo $proc_st11_ispis2; ?>%',
										'Neuspješan Kontakt 1 (<?php echo $uk_br_st16_ispis2; ?>) - <?php echo $proc_st16_ispis2; ?>%',
										'Neuspješan Kontakt 3 (<?php echo $uk_br_st12_ispis2; ?>) - <?php echo $proc_st12_ispis2; ?>%',
										'Zainteresiran Lead (<?php echo $uk_br_st13_ispis2; ?>) - <?php echo $proc_st13_ispis2; ?>%',
										'Nezainteresiran Lead (<?php echo $uk_br_st14_ispis2; ?>) - <?php echo $proc_st14_ispis2; ?>%',
										'U obradi Lead (<?php echo $uk_br_st15_ispis2; ?>) - <?php echo $proc_st15_ispis2; ?>%',
										'Neuspješan Lead 1 (<?php echo $uk_br_st17_ispis2; ?>) - <?php echo $proc_st17_ispis2; ?>%',
										'Neuspješan Lead 2 (<?php echo $uk_br_st18_ispis2; ?>) - <?php echo $proc_st18_ispis2; ?>%',
										'Termin Zainteresiran (<?php echo $uk_br_st19_ispis2; ?>) - <?php echo $proc_st19_ispis2; ?>%',
										'Termin Ostali (<?php echo $uk_br_st20_ispis2; ?>) - <?php echo $proc_st20_ispis2; ?>%',
										'Lead NL (<?php echo $uk_br_st21_ispis2; ?>) - <?php echo $proc_st21_ispis2; ?>%',
										'Lead NZ (<?php echo $uk_br_st22_ispis2; ?>) - <?php echo $proc_st22_ispis2; ?>%',
										'Prikupljanje dokumentacije (<?php echo $uk_br_st2_ispis2; ?>) - <?php echo $proc_st2_ispis2; ?>%',
										'Poslana pošta (<?php echo $uk_br_st3_ispis2; ?>) - <?php echo $proc_st3_ispis2; ?>%',
										'U obradi (<?php echo $uk_br_st4_ispis2; ?>) - <?php echo $proc_st4_ispis2; ?>%',
										'Dopuna dokumentacije (<?php echo $uk_br_st5_ispis2; ?>) - <?php echo $proc_st5_ispis2; ?>%',
										'Završen (<?php echo $uk_br_st6_ispis2; ?>) - <?php echo $proc_st6_ispis2; ?>%',
										'Arhiviran (<?php echo $uk_br_st7_ispis2; ?>) - <?php echo $proc_st7_ispis2; ?>%'
									],
									  datasets: [{
										data: [
											<?php echo $uk_br_st11_ispis2; ?>,
											<?php echo $uk_br_st16_ispis2; ?>,
											<?php echo $uk_br_st12_ispis2; ?>,
											<?php echo $uk_br_st13_ispis2; ?>,
											<?php echo $uk_br_st14_ispis2; ?>,
											<?php echo $uk_br_st15_ispis2; ?>,
											<?php echo $uk_br_st17_ispis2; ?>,
											<?php echo $uk_br_st18_ispis2; ?>,
											<?php echo $uk_br_st19_ispis2; ?>,
											<?php echo $uk_br_st20_ispis2; ?>,
											<?php echo $uk_br_st21_ispis2; ?>,
											<?php echo $uk_br_st22_ispis2; ?>,
											<?php echo $uk_br_st2_ispis2; ?>,
											<?php echo $uk_br_st3_ispis2; ?>,
											<?php echo $uk_br_st4_ispis2; ?>,
											<?php echo $uk_br_st5_ispis2; ?>,
											<?php echo $uk_br_st6_ispis2; ?>,
											<?php echo $uk_br_st7_ispis2; ?>
										],
										backgroundColor: [
											'rgb(131, 144, 152)',
											'rgb(0, 250, 251)',
											'rgb(0, 251, 83)',
											'rgb(14, 105, 115)',
											'rgb(191, 33, 75)',
											'rgb(199, 156, 255)',
											'rgb(183, 182, 249)',
											'rgb(207, 95, 250)',
											'rgb(117, 34, 99)',
											'rgb(204, 177, 122)',
											'rgb(128, 123, 70)',
											'rgb(214, 76, 10)',
											'rgb(242, 228, 46)',
											'rgb(64, 146, 217)',
											'rgb(139, 218, 242)',
											'rgb(242, 161, 46)',
											'rgb(104, 195, 104)',
											'rgb(243, 65, 60)'
										],
										hoverOffset: 4
									  }]
									};
									var myChart = new Chart(ctx, {
										type: 'pie',
										data: data,
										options: {
											responsive: true,
											maintainAspectRatio: false,
											plugins: {	
												legend: {
													position: 'right',
													align: 'center'
												},
												title: {
													display: true,
													position: 'top',
													align: 'end',
													text: 'Ukupno: (<?php echo $ukupan_br_ispis2; ?>)'
												},
											},
											animation: {
												animateScale: true,
												animateRotate: true
											}
										}
									});
								});
								</script>
							</div>
							<div class="uspjesno-izdanih-predracuna">
								<h1>Uspjesno izdanih predracuna (uplata): </h1>
								<?php 
									$ukupan_br_ispis2 = (
										$uk_br_st2_ispis2=$rowStatistika['Prdok'] +
										$uk_br_st3_ispis2=$rowStatistika['Pospos'] +
										$uk_br_st4_ispis2=$rowStatistika['Obrada'] +
										$uk_br_st5_ispis2=$rowStatistika['Dopdok'] +
										$uk_br_st6_ispis2=$rowStatistika['Zavrs']
										);
										
								?>
								<h1><b><?php echo $ukupan_br_ispis2; ?></b> </h1>
							</div>
							
						<style>
							.statistike-kampanje{
								display: flex;
								width:100%;
							}
							.uspjesno-izdanih-predracuna{
								display:flex;
								align-items: center;
							}
						</style>
						</div>
						<hr>
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

					$kampanja_id = $_GET['id'];

					$query = $db->prepare("
									SELECT *
									FROM idk_kampanje_dipl
									WHERE kd_id = :kd_id");

					$query->execute(array(
								':kd_id' => $kampanja_id));

					$row = $query->fetch();

					$kd_naziv = $row['kd_naziv'];
					$kd_skraceni_naziv = $row['kd_skraceni_naziv'];
					$kd_poruka_kampanje = $row['kd_poruka_kampanje'];
					$kd_jezik_forme = $row['kd_jezik_forme'];
					$kd_drzava = $row['kd_drzava'];
					$kd_slike = $row['kd_slike'];
					$kd_datum_kreiranja = date('d.m.Y.', strtotime($row['kd_datum_kreiranja']));
					
					$slike = explode(",", $kd_slike);
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1> <?php echo $kd_naziv; ?></h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>kampanje?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">

						<div class="container-fluid">
						
						<hr />
							<div class="row">
								<h4>Info o kampanji: </h4>
								<div class="col-md-6">
									<div class="row">
										<strong class="col-sm-4 text-right">Datum kreiranja:</strong>
										<div class="col-sm-8"><?php echo $kd_datum_kreiranja; ?></div>
									</div>
									<div class="row">
										<strong class="col-sm-4 text-right">Skraceni naziv:</strong>
										<div class="col-sm-8"><?php echo $kd_skraceni_naziv; ?></div>
									</div>
									<div class="row">
										<strong class="col-sm-4 text-right">Drzava:</strong>
										<div class="col-sm-8"><?php echo $kd_drzava; ?></div>
									</div>
									
								</div>
								<div class="col-md-6">
									<div class="row">
										<strong class="col-sm-4 text-right">Poruka kampanje:</strong>
										<div class="col-sm-8"><?php echo $kd_poruka_kampanje; ?></div>
									</div>
								</div>
							</div>
							
						<hr />
						<div class="row">
							<h4>Troškovi kampanje </h4>
							
							<div class="row text-center" style="min-height: 5rem; padding:5rem" >
								<table style="max-width: 100%; min-width: 25%;" border="3" cellspacing="3">
									<tr class="text-center">
										<th>Datum početka</th>
										<th>Datum kraja</th>
										<th>Budžet</th>
									</tr>
									<tr class="text-center">
									<?php  // foreach($slike as $slika_path){ ?>
									<td>
										27.04.2021
									</td>
									<td>
										01.05.2021
									</td>
									<td>
										50 evra
									</td>
									<?php // } ?>
									</tr>
								</table>
							</div>
						</div>
						<hr />
						<br>
						<h4>Slike vezane za kampanju: </h4>
							<div class="row text-center" style="min-height: 5rem; padding:5rem" >
								<table style="max-width: 100%; min-width: 25%;" border="3" cellspacing="3">
								
									<tr>
									<?php foreach($slike as $slika_path){ ?>
									<td>
									<!-- <div class="col-md-4" style="box-shadow: 1 2px 5px 0 rgba(0, 0, 0, 0.298039);"> -->
										<img style="height : 15rem; width : 15rem" src="<?php getSiteUrl(); ?>files/kampanje/<?php echo $slika_path; ?>">
									
									</td>
									<?php } ?>
									</tr>
								</table>
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