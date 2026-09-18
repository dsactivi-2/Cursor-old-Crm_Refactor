<?php
	include("includes/functions.php");
	include("includes/common.php");
$employee_status = explode( ',' , getEmployeeStatus());
$employee_supervizor = explode( ',' , getEmployeeSupervizor());
	$getEmployeeStatus = getEmployeeStatus();

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: tutorial?page=list");
	}

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title><?php getTitle(); ?></title>
		<?php 
			include('includes/head.php');
			if (in_array($getUserIp, $getIpWhiteList))
			{
		?>
	</head>

<body>
	<header>
		<?php 
			include('header.php'); 
		?>
	</header>
	<div id="sidebar">
		<?php 
			include('menu.php'); 
		?>
	</div>
	<div id="content">
		<div class="container-fluid">
			<?php
				switch ($page)
				{  
					case "list":
			?>
				<div class="row">
					<div class="col-xs-8">
						<h1><i class="fa fa-ticket idk_color_green" aria-hidden="true"></i> TUTORIJALI</h1>
					</div>
					<div class="col-xs-4 text-right idk_margin_top10">
						<a href="<?php getSiteURL(); ?>tutorial?page=novi_tutorial" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus fa-lg"></i> <span>Dodaj tutorijal</span></a>
						<a href="<?php getSiteURL(); ?>tutorial?page=nova_kategorija" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus fa-lg"></i> <span>Dodaj kategoriju</span></a>
					</div>
					<div class="col-xs-12">
						<hr />
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-12">
						<div class="content_box">
							<?php
								if(isset($_GET['mess'])) {
									$mess = $_GET['mess'];
								}else{
									$mess = 0;
								}

								if($mess == 1){
									echo '<div class="alert material-alert material-alert_success">Uspješno ste poslali poruku.</div>';
								}elseif($mess == 2){
									echo '<div class="alert material-alert material-alert_success">Uspješno ste obrisali poruku.</div>';
								}
				
							?>
							<!-- Start CSS -->
							<style>
								.content_box{
									background-color: #eee !important;
								}
								.lista__blokova{
									background-color: #fff;
									box-shadow: 3px 3px 15px 6px rgba(0, 0, 0, 0.1);
								}
								.list__item{
									height: 10vh;
									margin-bottom: 5px;
									margin-top: 5px;
									position:relative;
								}
								.list__head_item{
									height: 5vh;
									margin-bottom: 5px;
									margin-top: 5px;
									position:relative;
								}
								.list__head_item p{
									border-left: 2px solid #68c368;
									border-bottom: 2px solid #68c368;
									font-size:18px;
									font-weight:900;
									line-height:5vh;
									padding-left:15px;
								}
								.list__item p{
									border-left: 2px solid #68c368;
									font-size:18px;
									font-weight:300;
									line-height:10vh;
									padding-left:15px;
								}
								.list__item:hover, .list__item:hover p{
									box-shadow: 3px 3px 15px 6px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
									font-weight:900;
								}
								
								.sadrzaj__tutorijala{
									margin-top: 5px;
									background-color: #fff;
									box-shadow: 3px 3px 15px 6px rgba(0, 0, 0, 0.1);
									padding:10px;
								} 
								.modal-dialog{
									width: 800px;
								}
								.modal-body{
									padding: 50px;
									padding-bottom: 10px;
								}
								.modal-footer{
									padding-right: 50px;
									padding-left: 50px;
									text-align:center !important;
								}
								#modal_informacije {
									font-size: 17px;
									color: black;
								}
								#modal_naslov {
									font-size: 20px;
									color: black;
								}
								#modal_linija {
									margin-top: 10px;
									margin-bottom: 10px;
									border-color: darkgrey;
								}
								.material-label{
									padding: 2px 5px;
								}
								.material-dropdown-menu .material-dropdown-menu__link{
									padding-left: 10px;
									padding-right: 10px;
									text-align: center;
								}
								.tutorijal_show{
									border-bottom: 2px solid #68c368;
									border-left: 2px solid #68c368;
									padding:3px;
									height:auto;
									text-align: center;
									width:100%;
									padding-top:3%;
									display:block;
									margin-bottom:2rem;
								}
								.tutorijal_noshow{
									display:none;
								}
								.jobstep__shadow{
									box-shadow: 3px 3px 15px 6px rgba(0, 0, 0, 0.1);
								}
								.pb-5{
									padding-bottom: 3rem;
								}
								mt-2{
									margin-top: 2rem;
								}
								#test123:focus + #DIPL{
									display:block;
								}
								#welcome_div{
									height:50vh;
									line-height:40vh;
									font-size:3rem;
									font-weight:600; 
								}
								.panel-group{
									margin-bottom: 5px !important;
								}
								
								

							</style>
							<div class = "row" style = "background-color: #fff; padding:15px; ">
								<h2> ODABERITE MODUL TUTORIJALA: </h2>
								<hr>
								<div class = "col-sm-9 col-md-offset-1" style = "padding: 15px;">
									<div class = "row">
										<div class = "col-xs-12">
											<div class="panel-group material-accordion material-accordion_success" id="accordion1">
												<?php 
													$id_count = 5000;
													$kategorije_read = $db->prepare("
														SELECT kategorija_id,kategorija_naziv
														FROM idk_kategorije
														");
													$kategorije_read->execute();
													while($kategorije_row = $kategorije_read->fetch()){
														$kategorija_id = $kategorije_row["kategorija_id"];
														$kategorija_naziv = $kategorije_row["kategorija_naziv"];
												?>
													<div class="panel panel-default material-accordion__panel material-accordion__panel">
														<div class="panel-heading material-accordion__heading">
															<h4 class="panel-title">
															<a class="material-accordion__title" style="margin-bottom:0.3rem" data-toggle="collapse" data-parent="#accordion1" href="#<?php echo $kategorija_id; ?>"><?php echo strtoupper($kategorija_naziv); ?></a>
															</h4>
														</div>
														<div id="<?php echo $kategorija_id; ?>" class="panel-collapse collapse material-accordion__collapse">
															<div class="panel-body">
														<?php 
															$read_query = $db->prepare("
															SELECT *
															FROM idk_tutorijali
																WHERE tutorijal_kategorija_id = :tutorijal_kategorija_id");

															$read_query->execute(array(':tutorijal_kategorija_id' => $kategorija_id));
															
															$tutorialRows = $read_query->fetchAll(PDO::FETCH_ASSOC);
															if($kategorija_naziv === "PROCES ODLASKA"){

																array_unshift($tutorialRows,$tutorialRows[7]);
																unset($tutorialRows[8]);

															}
															foreach($tutorialRows as $row){
																$tutorijal_id = $row["tutorijal_id"];
																$tutorijal_kat_id = $row["tutorijal_kategorija_id"];
																$tutorijal_tg = $row["tutorijal_yt_url"];
																$tutorijal_doc = $row["tutorijal_doc"];
																$tutorijal_opis = $row["tutorijal_opis"];
																
														?> 
															<div class="panel-group material-accordion material-accordion_success" id="<?php echo $tutorijal_kat_id.$id_count; ?>">
																<div class="panel panel-default material-accordion__panel material-accordion__panel">
																	<div class="panel-heading material-accordion__heading">
																		<h4 class="panel-title">
																		<a class="material-accordion__title" style="margin-bottom:0.1rem" data-toggle="collapse" data-parent="#<?php echo $tutorijal_kat_id.$id_count; $id_count++; ?>" href="#<?php echo str_replace(" ","_",$tutorijal_opis); ?>"><?php echo strtoupper($tutorijal_opis); ?></a>
																		</h4>
																	</div>
																	<div id="<?php echo str_replace(" ","_",$tutorijal_opis); ?>" class="panel-collapse collapse material-accordion__collapse">
																			<div class="panel-body">
																				<div class="text-center">
																					<?php if((in_array( "1" , $employee_status)) OR (in_array( "1" , $employee_supervizor)) OR $logged_employee_id == 233 ) {	?>
																					<a style="text-align:center; display:inline-block"  href="#" data="<?php getSiteURL(); ?>tutorial?page=obrisi_tutorijal&id=<?php echo $tutorijal_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive">
																							<i class="fa fa-trash" aria-hidden="true">
																							</i> 
																							<span>
																								Obriši tutorijal
																							</span>
																					</a>
																					<?php } ?>
																				</div>

																				<div class="text-center">
																					<?php if((in_array( "1" , $employee_status)) OR (in_array( "1" , $employee_supervizor)) OR $logged_employee_id == 233 ) {	?>
																					<a style="text-align:center; display:inline-block"  href="<?php getSiteURL(); ?>tutorial?page=uredi_tutorijal&id=<?php echo $tutorijal_id; ?>" data="" class="archive btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive">

																							<i class="fa fa-edit" aria-hidden="true">
																							</i> 
																							<span>
																									Uredi tutorijal
																							</span>
																					</a>
																					<?php } ?>
																				</div>
																			<?php if(!empty($tutorijal_tg)){ ?>
																			<hr style="width: 70%">
																			<div class="text-center">
																				
																				<button style="text-align:center; display:inline-block"  data-tutorial_id="<?php echo $tutorijal_id; ?>" data-toggle="modal" data-target="#openTutorial" class="archive btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive tutorial_link otvori">
																						<i class="fa fa-book" aria-hidden="true">
																						</i> 
																						<span>
																							Otvori tutorial
																						</span>
																				</button>	
																			</div>
																			
																			<?php }?>
																			<hr>
																				<div class="text-center">
																				<?php 
																				$num_Doc = substr_count($tutorijal_doc," ");
																				$tutorijal_doc = explode(" ",$tutorijal_doc);
																				for($i = 0; $i<=$num_Doc; $i++){ ?> 
																					
																					<h4>DOKUMENT</h4>
																				<?php	
																					if(!empty($tutorijal_doc[$i])){
																						if (strpos($tutorijal_doc[$i], '.jpg') !== false OR strpos($tutorijal_doc[$i], '.png') !== false){
																							$doc_icon = '<i class="fa fa-file-image-o fa-3x" aria-hidden="true"></i>';
																						}
																					else if(strpos($tutorijal_doc[$i], '.pdf') !== false){
																						$doc_icon = '<i class="fa fa-file-pdf-o fa-3x" aria-hidden="true"></i>';
																					}
																					else if(strpos($tutorijal_doc[$i], '.doc') !== false OR strpos($tutorijal_doc[$i], '.docx') !== false){
																						$doc_icon = '<i class="fa fa-file-word-o fa-3x" aria-hidden="true"></i>';
																					}
																					else if(strpos($tutorijal_doc[$i], '.xls') !== false OR strpos($tutorijal_doc[$i], '.xlsx') !== false OR strpos($tutorijal_doc[$i], '.csv') !== false){
																						$doc_icon = '<i class="fa fa-file-excel-o fa-3x" aria-hidden="true"></i>';
																					}
																					else if(strpos($tutorijal_doc[$i], '.txt') !== false ){
																					$doc_icon = '<i class="fa fa-file-text-o fa-3x" aria-hidden="true"></i>';
																					}
																					else{
																						$doc_icon = '<i class="fa fa-file-powerpoint-o fa-3x" aria-hidden="true"></i>';
																					}
																					$download_doc = '<a href="'.getSiteUrlr().'files/dokumenti_tutorijal/'.$tutorijal_doc[$i].'" data-tutorial_doc="'.$tutorijal_doc[$i].'" class="btn material-btn material-btn_success main-container__column tutorial_doc" style = "padding: 5px;" target="_BLANK">'.$doc_icon.'</a>';
																					}
																					else{
																						$download_doc = 'Nije priložen dokument.';
																					} ?>
																					<p><?php echo $download_doc; ?></i></p> 
																				<?php } ?>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														<?php } ?>
														
														</div>
														</div>
													</div>
												<?php } ?>				
											</div>
											<?php
												if(isset($_GET['id'])){
													?>
														<script>
															$(document).ready(function() {
																$('#openTutorial').modal('show');
																
																let tutorial_id = <?php echo $_GET['id']; ?>;
																
																$.ajax({
																	url: 'ajax.php?page=otvoriTangoTutorial',
																	type: 'POST',
																	data: {
																		'tutorial_id':tutorial_id		
																	},
																	success: function(data){
																		$("#prikaz_tutoriala").html(data);
																	}
																})

																$.ajax({
																	url: 'ajax.php?page=tutorialLog',
																	type: 'POST',
																	data: {
																		'tutorial_id':tutorial_id
																	},
																	success: function(data){
																		
																	}
																})
															});
														</script>
													<?php		
												}							
											?>
											<script>
											$(".archive").click(function () {
												var addressValue = $(this).attr("data");
												document.getElementById("archive_link").href = addressValue;
											});
											$(".tutorial_doc").click(function (){
												let tutorijal_doc = $(this).data("tutorial_doc");
												
												
												$.ajax({
													url: 'ajax.php?page=tutorialLog',
													type: 'POST',
													data: {
														'tutorial_doc':tutorijal_doc
													},
													success: function(data){
														
													}
												})
											});
											$(".tutorial_link").click(function (){
												let tutorijal_id = $(this).data("tutorial_id");
												
												
												$.ajax({
													url: 'ajax.php?page=tutorialLog',
													type: 'POST',
													data: {
														'tutorial_id':tutorijal_id
													},
													success: function(data){
														
													}
												})
											});
											$(".otvori").click(function() {
												let tutorial_id = $(this).data("tutorial_id");
																							
												$.ajax({
													url: 'ajax.php?page=otvoriTangoTutorial',
													type: 'POST',
													data: {
														'tutorial_id':tutorial_id		
													},
													success: function(data){
														$("#prikaz_tutoriala").html(data);		
													}
												});
											});
											
											</script>

											<div class="modal material-modal material-modal_danger fade" id="archiveModal">
												<div class="modal-dialog">
													<div class="modal-content material-modal__content">
														<div class="modal-header material-modal__header">
															<button class="close material-modal__close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title material-modal__title">Brisanje tutorijala</h4>
														</div>
														<div class="modal-body material-modal__body">
															<p>Da li ste sigurni da želite obrisati tutorijal</p>
														</div>
														<div class="modal-footer material-modal__footer">
															<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
															<a id="archive_link" href=""><button class="btn btn-primary material-btn material-btn_danger">Izbriši</button></a>
														</div>
													</div>
												</div>
											</div>
											<div class="modal material-modal material-modal_success fade" id="openTutorial">
												<div class="modal-dialog" style="height: 820px; width: 90%;" >
													<div class="modal-content material-modal__content">
														<div class="modal-body material-modal__body" id="prikaz_tutoriala">
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
					case "novi_tutorial":
				?>
				<div class="row">
					<div class="col-xs-8 mx-2">
						<h1>
							<i class="fa fa-plus idk_color_green" aria-hidden="true">
							</i> 
							Kreiraj tutorijal
						</h1>
					</div>
					<div class="col-xs-4 text-right idk_margin_top10">
						<a href="<?php getSiteURL(); ?>tutorial?page=list" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive">
							<i class="fa fa-chevron-left" aria-hidden="true">
							</i> 
							<span>
								Povratak
							</span>
						</a>
					</div>
					<div class="col-xs-12">
						<hr/>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="content_box">
							<div class="row">
								<div class = "col-md-8 col-md-offset-2">
									<div class = "col-xs-12 text-center">
										<form action="tutorial.php?page=dodaj_tutorial" method="post" enctype="multipart/form-data" role="form" class="form-horizontal">
										<div class="form-group">
											<label for="tutorijal_opis" class="col-xs-5 control-label">
												<span class="text-danger">
													*
												</span> 
												Naziv podkategorije/tutorijala: 
											</label>
											<div class="col-xs-7">
												<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line">
													<input type="text" class="form-control materail-input" id="tutorijal_opis" name="tutorijal_opis" autocomplete="off"  placeholder="Kako koristiti ..." required>
													<span class="materail-input-block__line"></span>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label for="tutorijal_kategorija" class="col-xs-5 control-label">
												<span class="text-danger">
													*
												</span> 
											    <i style="padding-top: 5px; padding-left: 10px; padding-right: 10px;" class="fa fa-question-circle fa-lg" data-toggle="tooltip" data-placement="left" title='Napomena: Ako ne možete pronaći kategoriju za Vaš tutorijal, potrebno je istu kreirati' aria-hidden="true">
													</i> Kategorija tutorijala:
											</label> 
											<div class="col-xs-7">
												<div class="form-group">
													<select class="selectpicker" id="tutorijal_kategorija" name="tutorijal_kategorija">
											<?php
												$kategorije_read = $db->prepare("
																		SELECT kategorija_id,kategorija_naziv
																		FROM idk_kategorije
																		");
												$kategorije_read->execute();
												while($kategorije_row = $kategorije_read->fetch()){
													$kategorija_id = $kategorije_row["kategorija_id"];
													$kategorija_naziv = $kategorije_row["kategorija_naziv"];
											?>
														<option value="<?php echo $kategorija_id; ?>" style="text-align:center"><?php echo $kategorija_naziv; ?></option>
												<?php } ?>
													</select>
													
												</div>
											</div>
										</div>
										<div class="form-group">
											<label for="tutorial_tango" class="col-xs-5 control-label"> 
												Tango link:
											</label>
											<div class="col-xs-7">
												<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line">
													<input type="text" class="form-control materail-input" id="tutorial_tango" name="tutorial_tango" autocomplete="off"  placeholder="<iframe....">
													<span class="materail-input-block__line"></span>
												</div>
											</div>
										</div>
										<div class="form-group" style="text-align:left">
											<label for="tutorijal_doc" class="col-xs-5 control-label">
											
													<i style="padding-top: 5px; padding-left: 10px; padding-right: 10px;" class="fa fa-question-circle fa-lg" data-toggle="tooltip" data-placement="bottom" title="Napomena: Dokument ne smije biti veći od 20MB! - Dozvoljeni formati: jpg, jpeg, pdf, doc, docx, xls, xlsx, txt, ppt, pptx, png, csv" aria-hidden="true">
													</i>	Dokument:
													
											</label>
											<div class="col-xs-7">
												<div class="fileinput fileinput-new" data-provides="fileinput">
													<span class="fileinput-filename">
													</span>
													<span class="fileinput-exists">
															<a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="padding-top: 5px; padding-left: 10px; opacity: 0.8; color: red;">
														<i class="fa fa-times-circle" aria-hidden="true">
														</i>
													</a>
													</span>
													<span class="btn btn-default btn-file">
														<span class="fileinput-new"> 
															Izaberi dokument
														</span>
														<span class="fileinput-exists">
															Promijeni
														</span>
														<input type="file" name="tutorijal_doc" id="tutorijal_doc"/>
													</span>
													<script>
														$(function (){
															$('#tutorijal_doc').change(function (){
															if($('#tutorijal_doc').val() !== ""){
																var ext = $('#tutorijal_doc').val().split('.').pop().toLowerCase();
																if($.inArray(ext, ['jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png', 'csv']) == -1) {
																	$('#idk_alert_ext').removeClass('hidden');
																		this.value = null;
																}else{
																	$('#idk_alert_ext').addClass('hidden');
																}

																var f = this.files[0];

																if (f.size > 20388608 || f.fileSize > 20388608){
																	$('#idk_alert_size').removeClass('hidden');
																		this.value = null;
																}else{
																$('#idk_alert_size').addClass('hidden');
																}
															}
															})
														});
													</script>
												</div>
											</div>
										</div>
										<div id="idk_alert_size" class="row hidden">
											<div class="col-sm-12">
												<div class="alert material-alert material-alert_danger">Greška: Dokument koji pokuštavate dodati je veći od dozvoljene veličine.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
											</div>
										</div>
										<div id="idk_alert_ext" class="row hidden">
											<div class="col-sm-12">
												<div class="alert material-alert material-alert_danger">Greška: Format dokumenta kojeg pokušavate dodati nije dozvoljen.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-xs-6 col-xs-offset-3 text-center">
												<ul class="list-inline">
													<li class="hidden">
														<i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success">
														</i>
													</li>
													<li>
														<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column">
															<i class="fa fa-send" aria-hidden="true">
															</i> 
															<span>
																Spremi
															</span>
														</button>
													</li>
												</ul>
												<small>
													Sva polja označena sa 
													<span class="text-danger">
														*
													</span>  
													su obavezna!
												</small>
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
					case "uredi_tutorijal":
				?>
				<div class="row">
					<div class="col-xs-8 mx-2">
						<h1>
							<i class="fa fa-plus idk_color_green" aria-hidden="true">
							</i> 
							Uredi tutorijal
						</h1>
					</div>
					<div class="col-xs-4 text-right idk_margin_top10">
						<a href="<?php getSiteURL(); ?>tutorial?page=list" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive">
							<i class="fa fa-chevron-left" aria-hidden="true">
							</i> 
							<span>
								Povratak
							</span>
						</a>
					</div>
					<div class="col-xs-12">
						<hr/>
					</div>
				</div>
				<?php
				
						$tutorijal_id=$_GET['id'];
						$get_tutorial_details=$db->prepare("SELECT tutorijal_kategorija_id, tutorijal_yt_url, tutorijal_doc, tutorijal_opis FROM idk_tutorijali WHERE tutorijal_id=:tutorijal_id");
						$get_tutorial_details->execute(array(
							':tutorijal_id' => $tutorijal_id
						));
						$tutorial_details=$get_tutorial_details->fetch();
						$tutorijal_kategorija_id	= $tutorial_details['tutorijal_kategorija_id'];
						$tutorijal_url				= $tutorial_details['tutorijal_yt_url'];
						$tutorijal_doc				= $tutorial_details['tutorijal_doc'];
						$tutorijal_opis				= $tutorial_details['tutorijal_opis'];	


				?>
				<div class="row">
					<div class="col-md-12">
						<div class="content_box">
							<div class="row">
								<div class = "col-md-8 col-md-offset-2">
									<div class = "col-xs-12 text-center">
										<form action="tutorial.php?page=edit_tutorial" method="post" enctype="multipart/form-data" role="form" class="form-horizontal">
										<input type="hidden" name="tutorijal_id" value="<?php echo $tutorijal_id; ?>">
										<div class="form-group">
											<label for="tutorijal_opis" class="col-xs-5 control-label">
												<span class="text-danger">
													*
												</span> 
												Naziv podkategorije/tutorijala: 
											</label>
											<div class="col-xs-7">
												<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line">
													<input type="text" class="form-control materail-input" id="tutorijal_opis" name="tutorijal_opis" autocomplete="off"  placeholder="Kako koristiti ..." value="<?php echo $tutorijal_opis; ?>" required>
													<span class="materail-input-block__line"></span>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label for="tutorijal_kategorija" class="col-xs-5 control-label">
												<span class="text-danger">
													*
												</span> 
											    <i style="padding-top: 5px; padding-left: 10px; padding-right: 10px;" class="fa fa-question-circle fa-lg" data-toggle="tooltip" data-placement="left" title='Napomena: Ako ne možete pronaći kategoriju za Vaš tutorijal, potrebno je istu kreirati' aria-hidden="true">
													</i> Kategorija tutorijala:
											</label> 
											<div class="col-xs-7">
												<div class="form-group">
													<select class="selectpicker" id="tutorijal_kategorija" name="tutorijal_kategorija">
											<?php
												$kategorije_read = $db->prepare("
																		SELECT kategorija_id,kategorija_naziv
																		FROM idk_kategorije
																		");
												$kategorije_read->execute();
												while($kategorije_row = $kategorije_read->fetch()){
													$kategorija_id = $kategorije_row["kategorija_id"];
													$kategorija_naziv = $kategorije_row["kategorija_naziv"];
											?>
														<option value="<?php echo $kategorija_id; ?>" <?php if($tutorijal_kategorija_id == $kategorija_id){ echo "selected"; } ?>  style="text-align:center"><?php echo $kategorija_naziv; ?></option>
												<?php } ?>
													</select>
													
												</div>
											</div>
										</div>
										<div class="form-group">
											<label for="tutorial_tango" class="col-xs-5 control-label"> 
												Tango link:
											</label>
											<div class="col-xs-7">
												<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line">
													<input type="text" class="form-control materail-input" id="tutorial_tango" name="tutorial_tango" autocomplete="off" value='<?php echo $tutorijal_url; ?>' placeholder="<iframe....">
													<span class="materail-input-block__line"></span>
												</div>
											</div>
										</div>
										<div class="form-group" style="text-align:left">
											<label for="tutorijal_doc" class="col-xs-5 control-label">
											
													<i style="padding-top: 5px; padding-left: 10px; padding-right: 10px;" class="fa fa-question-circle fa-lg" data-toggle="tooltip" data-placement="bottom" title="Napomena: Dokument ne smije biti veći od 20MB! - Dozvoljeni formati: jpg, jpeg, pdf, doc, docx, xls, xlsx, txt, ppt, pptx, png, csv" aria-hidden="true">
													</i>	Dokument:
													
											</label>
											<div class="col-xs-7">
												<div class="fileinput  <?php if(isset($tutorijal_doc)){ echo "fileinput-exists";}else{ echo "fileinput-new";} ?> " data-provides="fileinput">
													<span class="fileinput-filename">
													</span>
													<span class="fileinput-exists">
															<a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="padding-top: 5px; padding-left: 10px; opacity: 0.8; color: red;">
														<i class="fa fa-times-circle" aria-hidden="true">
														</i>
													</a>
													</span>
													<span class="btn btn-default btn-file">
														<span class="fileinput-new"> 
															Izaberi dokument
														</span>
														<span class="fileinput-exists">
															Promijeni
														</span>
														
														<input type="hidden" <?php if(isset($tutorijal_doc)){ echo 'value="'.$tutorijal_doc.'"'; }?> name="tutorial_path" id="tutorial_path">
														<input type="file"  name="tutorijal_doc" id="tutorijal_doc"/>
													</span>
													<?php 
														if(isset($tutorijal_doc) and $tutorijal_doc!=null){
															echo '<a target="_blank" href="'.getSiteUrl().'files/dokumenti_tutorijal/'.$tutorijal_doc.'" style="padding-top: 5px; padding-left: 10px; padding-right: 10px; word-break: break-all;" class="fileinput-exists">Otvori trenutni</a>';
														}else{
															echo '<span style="padding-top: 5px; padding-left: 10px; padding-right: 10px; word-break: break-all;" class="fileinput-filename"> </span>';
														} 
													?>
													<script>
														$(function (){
															$('#tutorijal_doc').change(function (){
															if($('#tutorijal_doc').val() !== ""){
																var ext = $('#tutorijal_doc').val().split('.').pop().toLowerCase();
																if($.inArray(ext, ['jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png', 'csv']) == -1) {
																	$('#idk_alert_ext').removeClass('hidden');
																		this.value = null;
																}else{
																	$('#idk_alert_ext').addClass('hidden');
																}

																var f = this.files[0];

																if (f.size > 20388608 || f.fileSize > 20388608){
																	$('#idk_alert_size').removeClass('hidden');
																		this.value = null;
																}else{
																$('#idk_alert_size').addClass('hidden');
																}
															}
															})
														});
													</script>
												</div>
											</div>
										</div>
										<div id="idk_alert_size" class="row hidden">
											<div class="col-sm-12">
												<div class="alert material-alert material-alert_danger">Greška: Dokument koji pokuštavate dodati je veći od dozvoljene veličine.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
											</div>
										</div>
										<div id="idk_alert_ext" class="row hidden">
											<div class="col-sm-12">
												<div class="alert material-alert material-alert_danger">Greška: Format dokumenta kojeg pokušavate dodati nije dozvoljen.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-xs-6 col-xs-offset-3 text-center">
												<ul class="list-inline">
													<li class="hidden">
														<i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success">
														</i>
													</li>
													<li>
														<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column">
															<i class="fa fa-send" aria-hidden="true">
															</i> 
															<span>
																Spremi
															</span>
														</button>
													</li>
												</ul>
												<small>
													Sva polja označena sa 
													<span class="text-danger">
														*
													</span>  
													su obavezna!
												</small>
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
			case "nova_kategorija":
		?>
				<div class="row">
					<div class="col-xs-8 mx-2">
						<h1>
							<i class="fa fa-plus idk_color_green" aria-hidden="true">
							</i> 
							Kreiraj kategoriju za tutorijal
						</h1>
					</div>
					<div class="col-xs-4 text-right idk_margin_top10">
						<a href="<?php getSiteURL(); ?>tutorial?page=list" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive">
							<i class="fa fa-chevron-left" aria-hidden="true">
							</i> 
							<span>
								Povratak
							</span>
						</a>
					</div>
					<div class="col-xs-12">
						<hr/>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="content_box">
							<div class="row">
								<div class = "col-md-8 col-md-offset-2">
									<div class = "col-xs-12 text-center">
										<form action="tutorial.php?page=dodaj_kategoriju" method="post" enctype="multipart/form-data" role="form" class="form-horizontal">
										<div class="form-group">
											<label for="kategorija_naziv" class="col-xs-5 control-label">
												<span class="text-danger">
													*
												</span> 
												Naziv kategorije: 
											</label>
											<div class="col-xs-7">
												<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line">
													<input type="text" class="form-control materail-input" id="kategorija_naziv" name="kategorija_naziv" autocomplete="off"  placeholder="DIPL ili DAK, TIKETI..." required>
													<span class="materail-input-block__line"></span>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="col-xs-6 col-xs-offset-3 text-center">
												<ul class="list-inline">
													<li class="hidden">
														<i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success">
														</i>
													</li>
													<li>
														<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column">
															<i class="fa fa-send" aria-hidden="true">
															</i> 
															<span>
																Spremi
															</span>
														</button>
													</li>
												</ul>
												<small>
													Sva polja označena sa 
													<span class="text-danger">
														*
													</span>  
													su obavezna!
												</small>
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
			case "dodaj_kategoriju":
				$kategorija_naziv = $_POST['kategorija_naziv'];
				$insert_kategorija_query = $db->prepare("	
								INSERT INTO idk_kategorije
								(kategorija_naziv)
								VALUES
								(:kategorija_naziv)");
				$insert_kategorija_query->execute(array( 
							':kategorija_naziv' => $kategorija_naziv
							));
				header("Location: " . getSiteURL() . "tutorial?page=list");					
		break;	
			case "obrisi_tutorijal":
				$tut_id = $_GET['id'];
				$delete_query = $db->prepare("	
								DELETE FROM idk_tutorijali
								WHERE tutorijal_id = :tutorijal_id");
				$delete_query->execute(array( 
							':tutorijal_id' => $tut_id
							));
				header("Location: " . getSiteURL() . "tutorial?page=list");					
		break;	
		
		
			case "dodaj_tutorial":
				$tutorijal_opis = $_POST['tutorijal_opis'];
				$tutorijal_kategorija = $_POST['tutorijal_kategorija'];
				$tutorial_tango = $_POST['tutorial_tango'];	
				$tutorijal_doc = $_FILES['tutorijal_doc'];
				if(!empty($tutorial_tango)){
					
				$tutorial_tango = str_replace('height="100%"','height="820px"',$tutorial_tango);	
					
					
				}
				if(!empty($tutorijal_doc)){
					$file_name = $tutorijal_doc['name'];
					$file_tmp = $tutorijal_doc['tmp_name'];
			
					$file_ext = explode('.', $file_name);
					$file_ext = strtolower(end($file_ext));
					$allowed = array('jpg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png', 'csv');
					if(in_array($file_ext, $allowed)) {
						$file_name_new = uniqid() . '.' . $file_ext;
								$file_destination = "files/dokumenti_tutorijal/" . $file_name_new;
						if(move_uploaded_file($file_tmp, $file_destination)){}
					}
				}
				  
					$insert_query = $db->prepare("
									INSERT INTO idk_tutorijali
									(tutorijal_kategorija_id, tutorijal_yt_url, tutorijal_doc, tutorijal_opis)
									VALUES
									(:tutorijal_kategorija_id, :tutorijal_yt_url, :tutorijal_doc, :tutorijal_opis)");
					$insert_query->execute(array(
							':tutorijal_kategorija_id' => $tutorijal_kategorija,
							':tutorijal_yt_url' => $tutorial_tango,
							':tutorijal_opis' => $tutorijal_opis,
							':tutorijal_doc' => $file_name_new));
				header("Location: " . getSiteURL() . "tutorial?page=list");			
		break;
		
			case "edit_tutorial":
				$tutorijal_id=$_POST['tutorijal_id'];
				$tutorijal_opis = $_POST['tutorijal_opis'];
				$tutorijal_kategorija = $_POST['tutorijal_kategorija'];
				$tutorial_tango = $_POST['tutorial_tango'];	
				$tutorijal_doc=0;
				if(isset($_FILES['tutorijal_doc'])){
					$tutorijal_doc = $_FILES['tutorijal_doc'];
				}
				if(isset($_POST['tutorial_path'])){
					$file_name_new = $_POST['tutorial_path'];
				}


				if(!empty($tutorial_tango)){
					
				$tutorial_tango = str_replace('height="100%"','height="820px"',$tutorial_tango);	
					
					
				}
				if(!empty($tutorijal_doc)){
					$file_name = $tutorijal_doc['name'];
					$file_tmp = $tutorijal_doc['tmp_name'];
					
					$file_ext = explode('.', $file_name);
					$file_ext = strtolower(end($file_ext));
					$allowed = array('jpg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png', 'csv');
					if(in_array($file_ext, $allowed)) {
						$file_name_new = uniqid() . '.' . $file_ext;
								$file_destination = "files/dokumenti_tutorijal/" . $file_name_new;
						if(move_uploaded_file($file_tmp, $file_destination)){}
					}
				}
				  
					$update_query = $db->prepare("UPDATE
														idk_tutorijali
													SET
														tutorijal_kategorija_id=:tutorijal_kategorija_id,
														tutorijal_yt_url=:tutorijal_yt_url,
														tutorijal_doc=:tutorijal_doc,
														tutorijal_opis=:tutorijal_opis
														
													WHERE
														tutorijal_id = :tutorijal_id ");
					$update_query->execute(array(
							':tutorijal_kategorija_id' => $tutorijal_kategorija,
							':tutorijal_yt_url' => $tutorial_tango,
							':tutorijal_opis' => $tutorijal_opis,
							':tutorijal_doc' => $file_name_new,
							':tutorijal_id' => $tutorijal_id
							));
				header("Location: " . getSiteURL() . "tutorial?page=list");			
		break;
		} 
		?>
			<footer><?php getCopyright(); ?></footer>
		</div>
	</div>
</body>
</html>
		<?php 
			}
			else
			{			
				echo '<br/>
					<div class="alert material-alert material-alert_danger">
						<h4>NEMATE PRIVILEGIJE!</h4>
						<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
						<br />
					</div>';
			} 
		?>