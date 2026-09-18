<?php 
	include("includes/functions.php");
	
	$isLoggedIn = isLoggedIn();
	
	if($isLoggedIn == 1){
		if(isset($_REQUEST["page"])) {
			$page = $_REQUEST["page"];
		}else{
			//$page = "searchCandidate";
			header("Location: ".getSiteUrlr()."jobstep_qc/profileCandidates.php?page=searchCandidateJob");
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
				case "searchCandidateJob":
		?>
			<main class="container justify-content-center d-flex h-100 my-2">
				<div class = "row align-items-center w-100">
					<div class = "col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
						<div class="card text-center shadow rounded225">
							<div class="card-body">
								<h5 class="card-title">Pretraga kandidata za poslove</h5>
								<p class="card-text">Za pretragu kandidata i editovanje određenih informacija, unesite ID kandidata!</p>
								<form action="<?php getSiteUrl(); ?>jobstep_qc/do.php?page=searchCandidateJob" method="post" role="form">
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
				
				case "resultSearchJob":
				
				$kandidatId = intval($_GET["id"]);
				$resVr = intval($_GET["res"]);
				$resText = "";
				if($resVr == 1){
					$queryInfo = $db->prepare("
						SELECT kandidat_ime, kandidat_prezime, kandidat_mobitel, kandidat_email, kandidat_slika
						FROM idk_kandidati
						WHERE kandidat_id = :kandidat_id
					");
					$queryInfo->execute(array(
						':kandidat_id' => $kandidatId
					));
					$rowInfo = $queryInfo->fetch();
					$kanIme = $rowInfo["kandidat_ime"];
					$kanPrezime = $rowInfo["kandidat_prezime"];
					$kanMobitel = $rowInfo["kandidat_mobitel"];
					$kanEmail = $rowInfo["kandidat_email"];
					$kanSlika = $rowInfo["kandidat_slika"];
					if($kanSlika == "none" OR $kanSlika == "none.jpg"){
						$kanSlika = "jobstep_pp/images/noImage.svg";
					}else{
						$kanSlika = "files/kandidati/".$kanSlika;
					}
					$resText = '
						<div class="alert alert-success" role="alert">
							Kandidat pronađen!
						</div>
						<div class = "row">
							<div class = "col-xs-12 mb-4" style = "overflow-y: auto !important;">
								<div class = "row">
									<div class = "col-xs-12 align-items-center">
										<img src="'.getSiteUrlr().$kanSlika.'" class="rounded-3 mx-auto d-block shadow-sm" style = "width:100px; height: 100px;" alt="...">
									</div>
								</div>
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
								</table>
							</div>
						</div>
						<p class="card-text">Da bi se pristupilo formi za editovanje podataka, potvrdite tačnost dobijenog rezultata</p>
						<a href = "'.getSiteUrlr().'jobstep_qc/profileCandidates.php?page=editCandidatInfo&id='.$kandidatId.'" class="btn btn-light jobStepBtnColor w-100 rounded225">Potvrdi</a>
					';
				}else{
					$resText = '
						<div class="alert alert-danger text-center" role="alert">
							Kandidat ne postoji u bazi podataka!
						</div>
						
						<a href = "'.getSiteUrlr().'jobstep_qc/profileCandidates.php?page=searchCandidateJob" class="btn btn-light jobStepBtnColor w-100 rounded225">Nova pretraga</a>
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
				
				case "editCandidatInfo":
					$kandidatId = intval($_GET["id"]);
					if($kandidatId != 0){
						$queryInfo = $db->prepare("
							SELECT kandidat_ime, kandidat_prezime, kandidat_mobitel, kandidat_email, kandidat_slika, kandidat_nalog_id, kandidat_ppa_partner_id
							FROM idk_kandidati
							WHERE kandidat_id = :kandidat_id
						");
						$queryInfo->execute(array(
							':kandidat_id' => $kandidatId
						));
						$rowInfo = $queryInfo->fetch();
						$kanIme = $rowInfo["kandidat_ime"];
						$kanPrezime = $rowInfo["kandidat_prezime"];
						$kanMobitel = $rowInfo["kandidat_mobitel"];
						$kanEmail = $rowInfo["kandidat_email"];
						$kanSlika = $rowInfo["kandidat_slika"];
						if($kanSlika == "none" OR $kanSlika == "none.jpg"){
							$kanSlika = "jobstep_pp/images/noImage.svg";
						}else{
							$kanSlika = "files/kandidati/".$kanSlika;
						}
						if(intval($rowInfo["kandidat_nalog_id"]) != 0){
							$kanNalog = getNazivNalogaR($rowInfo["kandidat_nalog_id"]);
							$kanCompany = getCompanyNameR(getCompanyIdFromNalogR($rowInfo["kandidat_nalog_id"]));
						}else{
							$kanNalog = "Nema naloga";
							$kanCompany = "Nema kompanije";
						}
						if(intval($rowInfo["kandidat_ppa_partner_id"]) != 0){
							$kanPartner = getCompanyNameR(getCompanyForPartnerIdR($rowInfo["kandidat_ppa_partner_id"])); 
						}else{
							$kanPartner = "Nema partnera";
						}
						
		?>
						<main class="container my-2 scrollbar-hidden">
							<div class = "row mb-3">
								<div class = "col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12 text-center">
									<a href = "<?php getSiteUrl();?>jobstep_qc/profileCandidates.php?page=searchCandidateJob" class="btn btn-light jobStepBtnColor w-75 rounded225">Nova pretraga</a>
								</div>
							</div>
							<div class = "row mb-3">
								<div class = "col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12 text-center">
									<div class="card text-center rounded225">
										<div class="card-header fw-bold" style="border-radius: 2.25rem 2.25rem 0rem 0rem;">Podaci o kandidatu</div>
										<div class="card-body">
											<div class = "row">
												<div class = "col-xs-12" style = "overflow-y: auto !important;">
													<div class = "row">
														<div class = "col-xs-12 align-items-center">
															<img src="<?php getSiteUrl(); echo $kanSlika;?>" class="rounded-3 mx-auto d-block shadow-sm" style = "width:100px; height: 100px;" alt="...">
														</div>
													</div>
													<table class="table table-sm my-5">
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
																Nalog
															</td>
															<td class = "w-50">
																<?php echo $kanNalog; ?>
															</td>
														</tr>
														<tr>
															<td class = "fw-bold w-50">
																Kompanija
															</td>
															<td class = "w-50">
																<?php echo $kanCompany; ?>
															</td>
														</tr>
														<tr>
															<td class = "fw-bold w-50">
																Partner
															</td>
															<td class = "w-50">
																<?php echo $kanPartner; ?>
															</td>
														</tr>
													</table>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class = "row mt-3">
								<div class = "col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12 text-center">
									<a class="btn btn-light jobStepBtnColor w-75 rounded225 sendOtherDataModal" data-bs-toggle="modal" data-bs-target="#addPictureForCandidat">
										Promijeni fotografiju<span class="material-icons ms-3">image</span>
									</a>
								</div>
							</div>
							<div class="modal fade" id="addPictureForCandidat" tabindex="-1" aria-labelledby="addPictureForCandidatLabel" aria-hidden="true">
								<div class="modal-dialog modal-dialog-centered">
									<div class="modal-content border-secondary border-5">
										<div class="modal-header">
											<h5 class="modal-title" id="addPictureForCandidatLabel">Promjena fotografije</h5>
											<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
										</div>
										<form id = "submitForm" action="<?php getSiteUrl();?>jobstep_qc/do.php?page=editPictureForCandidat" method="post" role="form" enctype="multipart/form-data">
											<div class="modal-body">
												<input type = "hidden" name = "idKanSaveEditPicture" id = "idKanSaveEditPicture" value = "<?php echo $kandidatId; ?>">
												<div class="input-group my-5">
													<input type="file" class="form-control" name = "fileSaveEditPicture" id = "fileSaveEditPicture" aria-describedby="resetSaveEditPicture" aria-label="Upload" required>
													<button class="btn btn-danger" type="button" id="resetSaveEditPicture">Reset</button>
												</div>
												<div class = "row mt-3 alertOtherSize visually-hidden">
													<div class = "col-xs-12">
														<div class="alert alert-danger text-center" role="alert">
															Fotografija koju pokuštavate dodati je veća od dozvoljene veličine!
														</div>
													</div>
												</div>
												<div class = "row mt-3 alertOtherFormat visually-hidden">
													<div class = "col-xs-12">
														<div class="alert alert-danger text-center" role="alert">
															Format fotografije koju pokušavate dodati nije dozvoljen!<br>
															Koristite format ".png" ili ".jpg" 
														</div>
													</div>
												</div>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Odustani</button>
												<button type="submit" id = "submitDisabled" class="btn btn-success">Završi</button>
											</div>
										</form>
									</div>
								</div>
							</div>
							<script>
								$(document).on("click","#resetSaveEditPicture",function() {
									var fileSaveEditPicture = $("#fileSaveEditPicture").val();
									if(fileSaveEditPicture !== ""){
										$("#fileSaveEditPicture").val(null); 
									}
								});
								$(function (){
									$('#fileSaveEditPicture').change(function (){
										if($('#fileSaveEditPicture').val() !== ""){
											
											var extOtherDoc = $('#fileSaveEditPicture').val().split('.').pop().toLowerCase();

											if($.inArray(extOtherDoc, ['jpg', 'png']) == -1) {
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
										if($('#fileSaveEditPicture').val() !== ""){
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
								$(document).on("click","#submitDisabled",function() {
									var fileSaveEditPicture = $("#fileSaveEditPicture").val();
									if(fileSaveEditPicture !== ""){
										$('#submitDisabled').attr('disabled',true);
										$( "#submitForm" ).submit();
									}
								});
							</script>
							<div class = "row mt-3 mb-3">
								<div class = "col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12 text-center">
									<a class="btn btn-light jobStepBtnColor w-75 rounded225 sendOtherDataModal" data-bs-toggle="modal" data-bs-target="#addContractForCandidat">
										Dodaj ugovor<span class="material-icons ms-3">file_upload</span>
									</a>
								</div>
							</div>
							<div class="modal fade" id="addContractForCandidat" tabindex="-1" aria-labelledby="addContractForCandidatLabel" aria-hidden="true">
								<div class="modal-dialog modal-dialog-centered">
									<div class="modal-content border-secondary border-5">
										<div class="modal-header">
											<h5 class="modal-title" id="addContractForCandidatLabel">Promjena fotografije</h5>
											<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
										</div>
										<?php 
											$canNalog = getNalogForCandidatR($kandidatId);
											$canStatusPrijave = getCandidateStatusPrijave($kandidatId);
											if($canNalog != 0 AND in_array($canStatusPrijave, array(7,8))){
										?>
										<form id = "submitFormACFC" action="<?php getSiteUrl();?>jobstep_qc/do.php?page=addContractForCandidat" method="post" role="form" enctype="multipart/form-data">
											<div class="modal-body">
												<!-- 
													ACFC - skraćenica od "Add Contract For Candidat"
												-->
												<input type = "hidden" name = "idCanACFC" id = "idCanACFC" value = "<?php echo $kandidatId; ?>">
												<input type = "hidden" name = "canNalogACFC" id = "canNalogACFC" value = "<?php echo $canNalog; ?>">
												<input type = "hidden" name = "canStatusPrijaveACFC" id = "canStatusPrijaveACFC" value = "<?php echo $canStatusPrijave; ?>">
												<select class="selectpicker form-control" id = "typeACFC" name = "typeACFC" title = "Odaberite tip dokumenta" required>
													<?php 
														if(in_array($canStatusPrijave, array(7))){
													?>
													<option value = "1" data-subtext="Potpisan ugovor od strane poslodavca">Nepotpisan</option>
													<option value = "2" data-subtext="Potpisan ugovor od strane poslodavca i kandidata">Potpisan</option>
													<?php 
														}else if(in_array($canStatusPrijave, array(8))){
													?>
													<option value = "2" data-subtext="Potpisan ugovor od strane poslodavca i kandidata">Potpisan</option>
													<?php 
														}else{

														}
													?>
												</select>
												<div class="input-group my-5">
													<input type="file" class="form-control" name = "fileACFC" id = "fileACFC" aria-describedby="resetACFC" aria-label="Upload" required>
													<button class="btn btn-danger" type="button" id="resetACFC">Reset</button>
												</div>
												<div class = "row mt-3 alertOtherSizeACFC visually-hidden">
													<div class = "col-xs-12">
														<div class="alert alert-danger text-center" role="alert">
															Dokument koji pokuštavate dodati je veći od dozvoljene veličine!
														</div>
													</div>
												</div>
												<div class = "row mt-3 alertOtherFormatACFC visually-hidden">
													<div class = "col-xs-12">
														<div class="alert alert-danger text-center" role="alert">
															Format dokumenta koji pokušavate dodati nije dozvoljen!<br>
															Koristite format ".pdf", ".doc" ili ".docx" 
														</div>
													</div>
												</div>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Odustani</button>
												<button type="submit" id = "submitDisabledACFC" class="btn btn-success">Završi</button>
											</div>
										</form>
										<?php 
											}else{
										?>
											<div class = "row mx-5 my-5">
												<div class = "col-xs-12">
													<div class="alert alert-danger text-center" role="alert">
														Kandidat nije rezervisan ni u jednom nalogu ili se ne nalazi na određenom statusu gdje nije moguće dodati ugovor!
													</div>
												</div>
											</div>
										<?php 
											}
										?>
									</div>
								</div>
							</div>
							<script>
								$(document).on("click","#resetACFC",function() {
									var fileACFC = $("#fileACFC").val();
									if(fileACFC !== ""){
										$("#fileACFC").val(null); 
									}
								});
								$(function (){
									$('#fileACFC').change(function (){
										if($('#fileACFC').val() !== ""){
											
											var extOtherDocACFC = $('#fileACFC').val().split('.').pop().toLowerCase();

											if($.inArray(extOtherDocACFC, ['pdf','doc','docx']) == -1) {
												$('.alertOtherFormatACFC').removeClass('visually-hidden');
												this.value = null;
												setTimeout(function(){
														$('.alertOtherFormatACFC').addClass('visually-hidden');
													}, 5000
												);
											}else{
												$('.alertOtherFormatACFC').addClass('visually-hidden');
											}
										}
										if($('#fileACFC').val() !== ""){
											var sizeOtherDocACFC = this.files[0];

											if(sizeOtherDocACFC.size > 5242880 || sizeOtherDocACFC.fileSize > 5242880){
												$('.alertOtherSizeACFC').removeClass('visually-hidden');
												this.value = null;
												setTimeout(function(){
														$('.alertOtherSizeACFC').addClass('visually-hidden');
													}, 5000
												);
											}else{
												$('.alertOtherSizeACFC').addClass('visually-hidden');
											}
										}
									})
								});
								$(document).on("click","#submitDisabledACFC",function() {
									var fileACFC = $("#fileACFC").val();
									if(fileACFC !== ""){
										$('#submitDisabledACFC').attr('disabled',true);
										$( "#submitFormACFC" ).submit();
									}
								});
							</script>
							<div class = "row mb-3">
								<div class = "col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12 text-center">
									<div class="card text-center rounded225">
										<div class="card-header fw-bold" style="border-radius: 2.25rem 2.25rem 0rem 0rem;">Ugovori</div>
										<div class="card-body">
											<div class="row">
												<div class="col-xs-12">
													<div class="alert alert-warning text-center" role="alert">
														Informacije o dodanim dokumentima su informativnog karaktera! <br>
														Sadržaj dokumenata pogledajte preko CRM-a.
													</div>
												</div>
											</div>
											<div class = "row">
												<div class = "col-xs-12" style = "overflow-y: auto !important;">
													<?php 
														$canNalogForList = getNalogForCandidatR($kandidatId);
														$canPartnerForList = getPartnerForCandidatR($kandidatId);
														$queryListContractForCandidat = $db->prepare("
															SELECT 
																kc_id, kc_file_name, kc_user_id, kc_source, kc_upload_time, kc_signed, kc_visibility_status
															FROM 
																idk_kandidati_contracts
															WHERE 
																kc_candidate_id = :kc_candidate_id
																AND 
																kc_nalog_id = :kc_nalog_id
																AND 
																kc_partner_id = :kc_partner_id
																AND 
																kc_visibility_status = 2
														");
														$queryListContractForCandidat->execute(array(
															':kc_candidate_id' => $kandidatId,
															':kc_nalog_id' => $canNalogForList,
															':kc_partner_id' => $canPartnerForList
														));
														if($queryListContractForCandidat->rowCount() != 0){

													?>
													<table class="table table-sm my-2">
													<thead>
														<tr>
															<th scope="col">Potpisan</th>
															<th scope="col">Vrijeme dodavanja</th>
															<th scope="col">Dodao korisnik</th>
														</tr>
													</thead>
														<?php 
															while($rowListContractForCandidat = $queryListContractForCandidat->fetch()){
																$kc_source = $rowListContractForCandidat["kc_source"];
																$kc_upload_time = date("d.m.Y H:i:s", strtotime($rowListContractForCandidat["kc_upload_time"]));
																if($kc_source == 1){
																	$kc_user_id = getImePrezimeZaposlenika($rowListContractForCandidat["kc_user_id"]);
																}else{
																	$kc_user_id = getFirstAndLastNameUserPPR($rowListContractForCandidat["kc_user_id"]);
																}
																$kc_signed = $rowListContractForCandidat["kc_signed"];
																if($kc_signed == 0){
																	$kc_signed = '<span class="material-icons" style="color:red;">cancel</span>';
																}else{
																	$kc_signed = '<span class="material-icons" style="color:green;">check_circle</span>';
																}
																
														?>
														<tr>
															<td class = "fw-bold">
																<?php  echo $kc_signed; ?> 
															</td>
															<td class = "">
																<?php echo $kc_upload_time;?>
															</td>
															<td class = "">
																<?php echo $kc_user_id; ?>
															</td>
														</tr>
														<?php 
															}
														?>
													</table>
													<?php 
														}else{
													?> 
													<div class="alert alert-danger text-center" role="alert">
														Kandidat nema dodanih dokumenata!
													</div>
													<?php
														}
													?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</main>
		<?php 
					}else{
						
					}
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