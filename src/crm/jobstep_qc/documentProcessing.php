<?php 
	include("includes/functions.php");
	
	$isLoggedIn = isLoggedIn();
	
	if($isLoggedIn == 1){
		if(isset($_REQUEST["page"])) {
			$page = $_REQUEST["page"];
		}else{
			//$page = "searchCandidate";
			header("Location: ".getSiteUrlr()."jobstep_qc/documentProcessing.php?page=searchCandidate");
		}
?>
<!doctype html>
<html lang="en" class="h-100">
	<head>
		<?php
			include("includes/head.php"); 
		?>
	</head>
	<body class = "d-flex flex-column h-100">
		<?php 
			include("includes/navbar.php");
			
			switch($page){
				case "searchCandidate":
		?>
			<main class="container justify-content-center d-flex h-100 my-2">
				<div class = "row align-items-center w-100">
					<div class = "col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
						<div class="card text-center shadow rounded225">
							<div class="card-body">
								<h5 class="card-title">Pretraga kandidata</h5>
								<p class="card-text">Za pretragu kandidata i unos dokumenata, unesite ID kandidata!</p>
								<form action="<?php getSiteUrl(); ?>jobstep_qc/do.php?page=searchCandidate" method="post" role="form">
									<div class="form-floating mb-3">
										<input type="number" class="form-control rounded225" id="inputID" name="inputID" autocomplete = "off" placeholder = "858585" required>
										<label for="inputID" class="">ID kandidata</label>
									</div>
										<button type="submit" class="btn btn-light jobStepBtnColor w-100 rounded225">Pronađi</button>
								</form>
							</div>
						</div>
					</div>
				</div>
			</main>
		<?php
				break;
				
				case "resultSearch":
				
				$kandidatId = intval($_GET["id"]);
				$resVr = intval($_GET["res"]);
				$resText = "";
				if($resVr == 0){
					$resText = '
						<div class="alert alert-danger text-center" role="alert">
							Kandidat ne postoji u bazi podataka!
						</div>
						
						<a href = "'.getSiteUrlr().'jobstep_qc/documentProcessing.php?page=searchCandidate" class="btn btn-light jobStepBtnColor w-100 rounded225">Nova pretraga</a>
					';
				}elseif($resVr == 1){
					$resText = '
						<div class="alert alert-danger text-center" role="alert">
							Kandidat se nalazi u prodaji!
						</div>
						
						<a href = "'.getSiteUrlr().'jobstep_qc/documentProcessing.php?page=searchCandidate" class="btn btn-light jobStepBtnColor w-100 rounded225">Nova pretraga</a>
					';
				}elseif($resVr == 2){
					$resText = '
						<div class="alert alert-danger text-center" role="alert">
							Kandidat se nalazi u Arhivi!
						</div>
						
						<a href = "'.getSiteUrlr().'jobstep_qc/documentProcessing.php?page=searchCandidate" class="btn btn-light jobStepBtnColor w-100 rounded225">Nova pretraga</a>
					';
				}elseif($resVr == 3){
					$resText = '
						<div class="alert alert-danger text-center" role="alert">
							Kandidat nije povezan sa ustanovom!
						</div>
						<a href = "'.getSiteUrlr().'jobstep_qc/documentProcessing.php?page=searchCandidate" class="btn btn-light jobStepBtnColor w-100 rounded225">Pretraži ponovno</a>
					';
				}else{
					$queryInfo = $db->prepare("
						SELECT ime_nd_kandidata, prezime_nd_kandidata, mobilni_nd_kandidata, email_nd_kandidata, skola_nd_kandidata, skola_smjer_nd_kandidata, zaduzen_zaposlenik_nd_kandidata, status_nd_kandidata, pstatus_nd_kandidata, idd_ustanova_nd
						FROM idk_nd_kandidata
						WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
					");
					$queryInfo->execute(array(
						':id_broj_nd_kandidata' => $kandidatId
					));
					$rowInfo = $queryInfo->fetch();
					$kanIme = $rowInfo["ime_nd_kandidata"];
					$kanPrezime = $rowInfo["prezime_nd_kandidata"];
					$kanMobitel = $rowInfo["mobilni_nd_kandidata"];
					$kanEmail = $rowInfo["email_nd_kandidata"];
					$kanSkola = getSkolaKandidata($rowInfo["skola_nd_kandidata"]);
					$kanSmjer = getSkolaSmjerKandidata($rowInfo["skola_smjer_nd_kandidata"]);
					$kanZaposlenik = getImePrezimeZaposlenika($rowInfo["zaduzen_zaposlenik_nd_kandidata"]);
					$kanStatus = intval($rowInfo["status_nd_kandidata"]);
					$kanPodStatus = intval($rowInfo["pstatus_nd_kandidata"]);
					$kanUstanova = getUstanovaKandidata($rowInfo["idd_ustanova_nd"]);
					$resText = '
						<div class="alert alert-success" role="alert">
							Kandidat pronađen!
						</div>
						<div class = "row">
							<div class = "col-xs-12" style = "overflow-y: auto !important;">
								<table class="table table-sm">
									<tr>
										<td class = "text-left fw-bold">
											Ime i prezime
										</td>
										<td class = "text-left ">
											'.$kanIme.' '.$kanPrezime.'
										</td>
									</tr>
									<tr>
										<td class = "text-left fw-bold">
											Telefon
										</td>
										<td class = "text-left ">
											'.$kanMobitel.'
										</td>
									</tr>
									<tr>
										<td class = "text-left fw-bold">
											Email
										</td>
										<td class = "text-left ">
											'.$kanEmail.'
										</td>
									</tr>
									<tr>
										<td class = "text-left fw-bold">
											Škola
										</td>
										<td class = "text-left ">
											'.$kanSkola.'
										</td>
									</tr>
									<tr>
										<td class = "text-left fw-bold">
											Smjer
										</td>
										<td class = "text-left ">
											'.$kanSmjer.'
										</td>
									</tr>
									<tr>
										<td class = "text-left fw-bold">
											Zadužen zaposlenik
										</td>
										<td class = "text-left ">
											'.$kanZaposlenik.'
										</td>
									</tr>
									<tr>
										<td class = "text-left fw-bold">
											Ustanova
										</td>
										<td class = "text-left">
											'.$kanUstanova.'
										</td>
									</tr>
								</table>
							</div>
						</div>
						<p class="card-text">Da bi se pristupilo formi za dodavanje dokumenata, potvrdite tačnost dobijenog rezultata</p>
						<a href = "'.getSiteUrlr().'jobstep_qc/documentProcessing.php?page=listDocuments&id='.$kandidatId.'" class="btn btn-light jobStepBtnColor w-100 rounded225">Potvrdi</a>
					';
				}
				
		?>
			<main class="container justify-content-center d-flex h-100 my-2">
				<div class = "row align-items-center w-100">
					<div class = "col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
						<div class="card text-center shadow rounded225">
							<div class="card-body">
								<h5 class="card-title">Rezultat pretrage</h5>
								<?php echo $resText; ?>
							</div>
						</div>
					</div>
				</div>
			</main>
		<?php
				break;
				
				case "listDocuments":
					$kandidatId = intval($_GET["id"]);
					
					$queryInfo = $db->prepare("
						SELECT ime_nd_kandidata, prezime_nd_kandidata, mobilni_nd_kandidata, email_nd_kandidata, skola_nd_kandidata, skola_smjer_nd_kandidata, idd_ustanova_nd
						FROM idk_nd_kandidata
						WHERE id_broj_nd_kandidata = :id_broj_nd_kandidata
					");
					$queryInfo->execute(array(
						':id_broj_nd_kandidata' => $kandidatId
					));
					$rowInfo = $queryInfo->fetch();
					$kanIme = $rowInfo["ime_nd_kandidata"];
					$kanPrezime = $rowInfo["prezime_nd_kandidata"];
					$kanMobitel = $rowInfo["mobilni_nd_kandidata"];
					$kanEmail = $rowInfo["email_nd_kandidata"];
					$kanSkola = $rowInfo["skola_nd_kandidata"];
					$kanSmjer = $rowInfo["skola_smjer_nd_kandidata"];
					$kanUstanova = $rowInfo["idd_ustanova_nd"];
					
		?>
					<main class="container my-2">
						<div class = "row mb-3">
							<div class = "col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12 text-center">
								<a href = "<?php getSiteUrl();?>jobstep_qc/documentProcessing.php?page=searchCandidate" class="btn btn-light jobStepBtnColor w-75 rounded225">Nova pretraga</a>
							</div>
						</div>
						<div class = "row mb-3">
							<div class = "col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12 text-center">
								<p>
									<button class="btn btn-light jobStepBtnColor w-75 rounded225" type="button" data-bs-toggle="collapse" data-bs-target="#infoCand" aria-expanded="false" aria-controls="infoCand">
										Podaci o kandidatu
									</button>
								</p>
								<div class="collapse" id="infoCand">
									<div class="card card-body rounded225 text-center">
										<div class = "row">
											<div class = "col-xs-12" style = "overflow-y: auto !important;">
												<table class="table table-sm">
													<tr>
														<td class = "fw-bold w-50">
															Kandidat
														</td>
														<td class = "w-50">
															<?php echo $kanIme." ".$kanPrezime;?>
														</td>
													</tr>
													<tr>
														<td class = "fw-bold w-50">
															Telefon
														</td>
														<td class = "w-50">
															<?php echo $kanMobitel; ?>
														</td>
													</tr>
													<tr>
														<td class = "fw-bold w-50">
															Email
														</td>
														<td class = "w-50">
															<?php echo $kanEmail; ?>
														</td>
													</tr>
													<tr>
														<td class = "fw-bold w-50">
															Škola
														</td>
														<td class = "w-50">
															<?php echo getSkolaKandidata($kanSkola); ?>
														</td>
													</tr>
													<tr>
														<td class = "fw-bold w-50">
															Smjer
														</td>
														<td class = "w-50">
															<?php echo getSkolaSmjerKandidata($kanSmjer); ?>
														</td>
													</tr>
													<tr>
														<td class = "fw-bold w-50">
															Ustanova
														</td>
														<td class = "w-50">
															<?php echo getSkolaSmjerKandidata($kanUstanova); ?>
														</td>
													</tr>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class = "row">
							<div class = "col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
								<div class="card text-center rounded225">
									<div class="card-header fw-bold" style="border-radius: 2.25rem 2.25rem 0rem 0rem;">Lista dokumenata</div>
									<div class="card-body">
										<?php 
											$queryList = $db->prepare("
												SELECT 
												* 
												FROM 
												idk_nd_ustanove_tip_dokumenta
												WHERE 
												(
													idd_ustanove_nd = :idd_ustanove_nd 
													AND
													idd_skole_ustanove_nd is null
													AND
													idd_skole_smjer_ustanove_nd is null
												)
												OR
												(
													idd_ustanove_nd = :idd_ustanove_nd
													AND
													idd_skole_ustanove_nd
													IN
													(
														SELECT
														id_skole_ustanove_nd
														FROM
														idk_nd_ustanove_skola
														WHERE
														(
															idd_ustanove_nd = :idd_ustanove_nd
															AND
															skola_idd = :skola_idd
														)
													)
													AND 
													idd_skole_smjer_ustanove_nd is null
												)
												OR
												(
													idd_ustanove_nd = :idd_ustanove_nd
													AND 
													idd_skole_ustanove_nd
													IN
													(
														SELECT
														id_skole_ustanove_nd
														FROM
														idk_nd_ustanove_skola
														WHERE
														(
															idd_ustanove_nd = :idd_ustanove_nd
															AND
															skola_idd = :skola_idd
														)
													)
													AND 
													idd_skole_smjer_ustanove_nd
													IN
													(
														SELECT
														id_skole_smjer_ustanove_nd
														FROM
														idk_nd_ustanove_skole_smjerovi
														WHERE
														(
															ss_idd = :ss_idd
															AND
															idd_skole_ustanove_nd
															IN
															(
																SELECT
																id_skole_ustanove_nd
																FROM
																idk_nd_ustanove_skola
																WHERE
																(
																	idd_ustanove_nd = :idd_ustanove_nd
																	AND
																	skola_idd = :skola_idd
																)
															)
														)
													)
												)
											");
											$queryList->execute(array(
												':idd_ustanove_nd' => $kanUstanova,
												':skola_idd' => $kanSkola,
												':ss_idd' => $kanSmjer
											));
											
											if($queryList->rowCount() != 0){
										?>
											<div class="accordion" id="accordionListDoc">
										<?php 
												$countList = 0;
												while($rowList = $queryList->fetch()){
													$countList++;
													$idList = $rowList["id_tip_dokumenta_ustanove_nd"];
													$nazivList = $rowList["naziv_tip_dokumenta_ustanove_nd"];
													if(intval($rowList["obaveznost_dokumenta_ustanove_nd"]) == 1){
														$obavezanIconList = '<span class="material-icons">check_box</span>';
														$obavezanStyleList = 'text-success';
													}else{
														$obavezanIconList = '<span class="material-icons">indeterminate_check_box</span>';
														$obavezanStyleList = 'text-warning';
													}
													if(intval($rowList["template_dokumenta_ustanove_nd"]) == 1){
														$templateIconList = '<span class="material-icons">check_box</span>';
														$templateStyleList = 'text-success';
													}else{
														$templateIconList = '<span class="material-icons">indeterminate_check_box</span>';
														$templateStyleList = 'text-warning';
													}
													$templateNazivList = $rowList["template_naziv_dokumenta_ustanove_nd"];
													
													$queryCheck = $db->prepare("
														SELECT *
														FROM idk_nd_kandidata_dokumenti
														WHERE status_dokument_nd = :status_dokument_nd AND id_kandidata_dokument_nd = :id_kandidata_dokument_nd
													");
													$queryCheck->execute(array(
														':status_dokument_nd' => $idList,
														':id_kandidata_dokument_nd' => $kandidatId
													));
													
													$iconCheck = "";
													$borderCheck = "";
													$btnCheck = "";
													$btnColor = "";
													$flagCheck = "";
													$nazivDokumentaCheck = "";
													if($queryCheck->rowCount() != 0){
														$rowCheck = $queryCheck->fetch();
														if( strpos($rowCheck["naziv_dokument_nd"], '.jpg') !== false OR strpos($rowCheck["naziv_dokument_nd"], '.png') !== false OR strpos($rowCheck["naziv_dokument_nd"], '.jpeg') !== false){
															$nazivDokumentaCheck = "".getSiteUrlr()."files/dokumenti_ND_kandidat/".$rowCheck["naziv_dokument_nd"];
														}else{
															$nazivDokumentaCheck = "https://docs.google.com/viewer?url=".getSiteUrlr()."files/dokumenti_ND_kandidat/".$rowCheck["naziv_dokument_nd"];
														}
														$iconCheck = '<span class="material-icons">check_circle_outline</span>';
														$borderCheck = 'light';
														$btnCheck = 'light';
														$btnColor = 'style = "background-color: #E6FDFF; color: #3A4053;"';
														$flagCheck = 1;
													}else{
														$iconCheck = '<span class="material-icons">highlight_off</span>';
														$borderCheck = 'light';
														$btnCheck = 'light';
														$btnColor = 'style = "background-color: #3A4053; color: #F7F9FF;"';
														$flagCheck = 0;
														$nazivDokumentaCheck = "";
													}
										?>
											<div class="accordion-item">
												<h2 class="accordion-header" id="heading<?php echo $countList;?>">
													<button class="accordion-button collapsed" <?php echo $btnColor;?> type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $countList;?>" aria-expanded="true" aria-controls="collapse<?php echo $countList;?>">
														<?php echo $nazivList; ?>
													</button>
												</h2>
												<div id="collapse<?php echo $countList;?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $countList;?>" data-bs-parent="#accordionListDoc">
													<div class="accordion-body">
														<div class = "row">
															<div class = "col-xs-12" style = "overflow-y: auto !important;">
																<table class="table table-borderless">
																	<tr>
																		<td class = "fw-bold text-center w-50">
																			Obavezan
																		</td>
																		<td class = "text-center w-50 <?php echo $obavezanStyleList; ?>">
																			<?php echo $obavezanIconList; ?>
																		</td>
																	</tr>
																	<tr>
																		<td class = "fw-bold text-center w-50">
																			Template
																		</td>
																		<td class = "text-center w-50 <?php echo $templateStyleList; ?>">
																			<?php echo $templateIconList; ?>
																		</td>
																	</tr>
																</table>
															</div>
														</div>
														<div class = "row">
															<div class = "col-xs-12">
																<?php 
																	if($flagCheck == 0){
																?>
																<a class="btn btn-light rounded225 sendDataModal" data-id_doc = "<?php echo $idList; ?>" data-bs-toggle="modal" data-bs-target="#addDoc">
																	<span class="material-icons">add</span>
																</a>
																<?php 
																	}else{
																?>
																<a class="btn btn-light rounded225 sendDataModal" href="<?php echo $nazivDokumentaCheck; ?>" >
																	<span class="material-icons">description</span>
																</a>
																<?php
																	}
																?>
															</div>
														</div>
													</div>
												</div>
											</div>
										<?php 
												}
										?>
										</div>
										<script>
											$(document).on("click",".sendDataModal",function() {
												var idDoc = $(this).data("id_doc");
												$("#idSaveDoc").val(idDoc);
											});
										</script>
										<div class="modal fade" id="addDoc" tabindex="-1" aria-labelledby="addDocLabel" aria-hidden="true">
											<div class="modal-dialog modal-dialog-centered modal-sm">
												<div class="modal-content border-secondary border-5">
													<div class="modal-header">
														<h5 class="modal-title" id="addDocLabel">Dodaj dokument</h5>
														<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
													</div>
													<form action="<?php getSiteUrl();?>jobstep_qc/do.php?page=saveDoc&type=1" method="post" role="form" enctype="multipart/form-data">
														<div class="modal-body">
															<input type = "hidden" name = "idSaveDoc" id = "idSaveDoc" required>
															<input type = "hidden" name = "idKanSaveDoc" id = "idKanSaveDoc" value = "<?php echo $kandidatId; ?>" required>
															<div class="input-group my-5">
																<input type="file" class="form-control" name = "fileSaveDoc" id = "fileSaveDoc" aria-describedby="resetSaveDoc" aria-label="Upload" required>
																<button class="btn btn-danger" type="button" id="resetSaveDoc">Reset</button>
															</div>
															<div class = "row mt-3 alertSize visually-hidden">
																<div class = "col-xs-12">
																	<div class="alert alert-danger text-center" role="alert">
																		Dokument koji pokuštavate dodati je veći od dozvoljene veličine!
																	</div>
																</div>
															</div>
															<div class = "row mt-3 alertFormat visually-hidden">
																<div class = "col-xs-12">
																	<div class="alert alert-danger text-center" role="alert">
																		Format dokumenta kojeg pokušavate dodati nije dozvoljen!
																	</div>
																</div>
															</div>
														</div>
														<div class="modal-footer">
															<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Odustani</button>
															<button type="submit" class="btn btn-success">Završi</button>
														</div>
													</form>
												</div>
											</div>
										</div>
										<script>
											$(document).on("click","#resetSaveDoc",function() {
												var fileSaveDoc = $("#fileSaveDoc").val();
												if(fileSaveDoc !== ""){
													$("#fileSaveDoc").val(null);
												}
											});
											$(function (){
												$('#fileSaveDoc').change(function (){
													if($('#fileSaveDoc').val() !== ""){
														
														var extDoc = $('#fileSaveDoc').val().split('.').pop().toLowerCase();

														if($.inArray(extDoc, ['jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png']) == -1) {
															$('.alertFormat').removeClass('visually-hidden');
															this.value = null;
															setTimeout(function(){
																	$('.alertFormat').addClass('visually-hidden');
																}, 5000
															);
														}else{
															$('.alertFormat').addClass('visually-hidden');
														}
													}
													if($('#fileSaveDoc').val() !== ""){
														var sizeDoc = this.files[0];

														if(sizeDoc.size > 20388608 || sizeDoc.fileSize > 20388608){
															$('.alertSize').removeClass('visually-hidden');
															this.value = null;
															setTimeout(function(){
																	$('.alertSize').addClass('visually-hidden');
																}, 5000
															);
														}else{
															$('.alertSize').addClass('visually-hidden');
														}
													}
												})
											});
										</script>
										<?php 
											}else{
												echo '
													<div class="alert alert-danger text-center mb-0" role="alert">
														Na ustanovi nisu dodani tipovi dokumenata!
													</div>
												';
											}
										?>
									</div>
								</div>
							</div>
						</div>
						<div class = "row mt-3">
							<div class = "col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12 text-center">
								<a class="btn btn-light jobStepBtnColor w-75 rounded225 sendOtherDataModal" data-bs-toggle="modal" data-bs-target="#addOtherDoc">
									Dodaj ostale dokumente<span class="material-icons ms-3">add</span>
								</a>
							</div>
						</div>
						<div class="modal fade" id="addOtherDoc" tabindex="-1" aria-labelledby="addOtherDocLabel" aria-hidden="true">
							<div class="modal-dialog modal-dialog-centered modal-sm">
								<div class="modal-content border-secondary border-5">
									<div class="modal-header">
										<h5 class="modal-title" id="addOtherDocLabel">Dodaj ostali dokument</h5>
										<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
									</div>
									<form action="<?php getSiteUrl();?>jobstep_qc/do.php?page=saveDoc&type=2" method="post" role="form" enctype="multipart/form-data">
										<div class="modal-body">
											<input type = "hidden" name = "idKanSaveOtherDoc" id = "idKanSaveOtherDoc" value = "<?php echo $kandidatId; ?>">
											<div class="form-floating mb-3">
												<input type="text" class="form-control" name = "nameSaveOtherDoc" id = "nameSaveOtherDoc" placeholder="Unesite naziv" required>
												<label for="nameSaveOtherDoc">Naziv dokumenta</label>
											</div>
											<div class="input-group my-5">
												<input type="file" class="form-control" name = "fileSaveOtherDoc" id = "fileSaveOtherDoc" aria-describedby="resetSaveOtherDoc" aria-label="Upload" required>
												<button class="btn btn-danger" type="button" id="resetSaveOtherDoc">Reset</button>
											</div>
											<div class = "row mt-3 alertOtherSize visually-hidden">
												<div class = "col-xs-12">
													<div class="alert alert-danger text-center" role="alert">
														Dokument koji pokuštavate dodati je veći od dozvoljene veličine!
													</div>
												</div>
											</div>
											<div class = "row mt-3 alertOtherFormat visually-hidden">
												<div class = "col-xs-12">
													<div class="alert alert-danger text-center" role="alert">
														Format dokumenta kojeg pokušavate dodati nije dozvoljen!
													</div>
												</div>
											</div>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Odustani</button>
											<button type="submit" class="btn btn-success">Završi</button>
										</div>
									</form>
								</div>
							</div>
						</div>
						<script>
							$(document).on("click","#resetSaveOtherDoc",function() {
								var fileSaveOtherDoc = $("#fileSaveOtherDoc").val();
								if(fileSaveOtherDoc !== ""){
									$("#fileSaveOtherDoc").val(null);
								}
							});
							$(function (){
								$('#fileSaveOtherDoc').change(function (){
									if($('#fileSaveOtherDoc').val() !== ""){
										
										var extOtherDoc = $('#fileSaveOtherDoc').val().split('.').pop().toLowerCase();

										if($.inArray(extOtherDoc, ['jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png']) == -1) {
											$('.alertOtherFormat').removeClass('visually-hidden');
											this.value = null;
											setTimeout(function(){
													$('.alertOtherFormat').addClass('visually-hidden');
												}, 5000
											);
										}else{
											$('.alertOtherFormat').addClass('visually-hidden');
										}
									}
									if($('#fileSaveOtherDoc').val() !== ""){
										var sizeOtherDoc = this.files[0];

										if(sizeOtherDoc.size > 20388608 || sizeOtherDoc.fileSize > 20388608){
											$('.alertOtherSize').removeClass('visually-hidden');
											this.value = null;
											setTimeout(function(){
													$('.alertOtherSize').addClass('visually-hidden');
												}, 5000
											);
										}else{
											$('.alertOtherSize').addClass('visually-hidden');
										}
									}
								})
							});
						</script>
						<div class = "row">
							<div class = "col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
								<div class="card text-center rounded225 mt-3">
									<div class="card-header fw-bold " style="border-radius: 2.25rem 2.25rem 0rem 0rem;">Ostali dokumenti</div>
									<div class="card-body">
										<?php 
											$queryOthersList = $db->prepare("
												SELECT *
												FROM idk_nd_kandidata_dokumenti
												WHERE id_kandidata_dokument_nd = :id_kandidata_dokument_nd AND status_dokument_nd is null AND naziv_dokument_ostali_nd is not null AND tip_dokumenta is null
												ORDER BY vrijeme_dodavanja_dokument_nd DESC
											");
											$queryOthersList->execute(array(
												':id_kandidata_dokument_nd' => $kandidatId
											));
											if($queryOthersList->rowCount() != 0){
										?>
											<div class="accordion" id="accordionListOtherDoc">
										<?php 
												$countOthersList = 0;
												while($rowOthersList = $queryOthersList->fetch()){
													$countOthersList++;
													if( strpos($rowOthersList["naziv_dokument_nd"], '.jpg') !== false OR strpos($rowOthersList["naziv_dokument_nd"], '.png') !== false OR strpos($rowOthersList["naziv_dokument_nd"], '.jpeg') !== false){
														$nazivOthersList = "".getSiteUrlr()."files/dokumenti_ND_kandidat/".$rowOthersList["naziv_dokument_nd"];
													}else{
														$nazivOthersList = "https://docs.google.com/viewer?url=".getSiteUrlr()."files/dokumenti_ND_kandidat/".$rowCheck["naziv_dokument_nd"];
													}
													$nazivOstaliOthersList = $rowOthersList["naziv_dokument_ostali_nd"];
													$vrijemeOthersList = date("d.m.Y H:i",strtotime($rowOthersList["vrijeme_dodavanja_dokument_nd"]));
													$zaposlenikOthersList = getImePrezimeZaposlenika($rowOthersList["dodao_zaposlenik_dokument_nd"]);
										?>
												<div class="accordion-item">
													<h2 class="accordion-header" id="headingOther<?php echo $countOthersList;?>">
														<button class="accordion-button collapsed" style = "background-color: #E6FDFF; color: #3A4053;" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOther<?php echo $countOthersList;?>" aria-expanded="true" aria-controls="collapseOther<?php echo $countOthersList;?>">
															<?php echo $nazivOstaliOthersList;?>
														</button>
													</h2>
													<div id="collapseOther<?php echo $countOthersList;?>" class="accordion-collapse collapse" aria-labelledby="headingOther<?php echo $countOthersList;?>" data-bs-parent="#accordionListOtherDoc">
														<div class="accordion-body">
															<div class = "row">
																<div class = "col-xs-12" style = "overflow-y: auto !important;">
																	<table class="table table-borderless">
																		<tr>
																			<td class = "fw-bold text-center w-50">
																				Zaposlenik
																			</td>
																			<td class = "text-center w-50">
																				<?php echo $zaposlenikOthersList; ?>
																			</td>
																		</tr>
																		<tr>
																			<td class = "fw-bold text-center w-50">
																				Vrijeme dodavanja
																			</td>
																			<td class = "text-center w-50">
																				<?php echo $vrijemeOthersList; ?>
																			</td>
																		</tr>
																	</table>
																</div>
															</div>
															<div class = "row">
																<div class = "col-xs-12">
																	<a class="btn btn-light rounded225" href="<?php echo $nazivOthersList; ?>" target="_BLANK">
																		<span class="material-icons">description</span>
																	</a>
																</div>
															</div>
														</div>
													</div>
												</div>
										<?php 
												}
										?>
											</div>
										<?php 
											}else{
												echo '
													<div class="alert alert-danger text-center mb-0" role="alert">
														Nema dodanih ostalih dokumenata!
													</div>
												';
											}
										?>
									</div>
								</div>
							</div>
						</div>
					</main>
		<?php
				break;
			}
			
			include("includes/footer.php");
		?>
	</body>
</html>
<?php 
	}else{
		header("Location:".getSiteUrlr()."jobstep_qc/landing.php");
	}
?>