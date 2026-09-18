<?php
	error_reporting(0);
	include("includes/functions.php");
	include("includes/common.php");
	$getEmployeeStatus = getEmployeeStatus();

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: employees?page=list");
	}

?>
		<div class="container-fluid">
		<?php
			switch ($page){

				case "edit_kandidat_kontakt":
				
					$id 		 = $_REQUEST['id'];
					$kandidat_id = $_REQUEST['kandidat_id'];
					
					$select_query = $db->prepare("
						SELECT kki_id, kki_grupa, kki_naziv, kki_podatak, kki_primary
						FROM idk_kandidat_kontakt_info
						WHERE kki_id = :kki_id
					");

					$select_query->execute(array(
						':kki_id' => $id
					));

					$select_row = $select_query->fetch();

					$kki_id 	  = $select_row['kki_id'];
					$kki_grupa_id = $select_row['kki_grupa'];

					if      ($kki_grupa_id == 1) $kki_grupa = "Telefon";
					else if ($kki_grupa_id == 2) $kki_grupa = "E-mail";
					else if ($kki_grupa_id == 3) $kki_grupa = "Web";
					else if ($kki_grupa_id == 4) $kki_grupa = "Messangeri";
					else {};

					$kki_naziv = $select_row['kki_naziv'];
					$kki_podatak = $select_row['kki_podatak'];
					$kki_primary = $select_row['kki_primary'];

					?>
					<form action="<?php getSiteURL(); ?>do.php?form=edit_kandidat_kontakt" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
						<input type="hidden" name="kandidat_id" value="<?php echo $kandidat_id; ?>">
						<input type="hidden" name="kki_id" value="<?php echo $kki_id; ?>">
						
						<div class="form-group">
							<label for="kki_grupa" class="col-sm-3 control-label"><span class="text-danger">*</span> Vrsta kontakta:</label>
							
							<div class="col-sm-9">
								<select class="selectpicker" id="kki_grupa1" name="kki_grupa">
									<option value=""></option>
									<option value="1" <?php if($kki_grupa_id == 1){echo "selected";}else{echo "disabled";} ?>>Telefon</option>
									<option value="2" <?php if($kki_grupa_id == 2){echo "selected";}else{echo "disabled";} ?>>E-mail</option>
									<option value="3" <?php if($kki_grupa_id == 3){echo "selected";}else{echo "disabled";} ?>>WEB</option>
									<option value="4" <?php if($kki_grupa_id == 4){echo "selected";}else{echo "disabled";} ?>>Messangeri</option>
								</select>
							</div>
						</div>
						
						<div id="telefon1">
							<div class="form-group">
								<label for="kki_naziv1" class="col-sm-3 control-label"><span class="text-danger">*</span> Tip telefona:</label>
								
								<div class="col-sm-9">
									<select class="selectpicker" id="kki_naziv1" name="kki_naziv">
										<option selected value="<?php echo $kki_naziv; ?>"><?php echo $kki_naziv; ?></option>
									
										<?php 
										if ($kki_naziv != "Fiksni" AND $kki_primary != 1) echo '<option value="Fiksni">Fiksni</option>';
										if ($kki_naziv != "Mobilni") echo '<option value="Mobilni">Mobilni</option>';
										?>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label for="kki_podatak1" class="col-sm-3 control-label"><span class="text-danger">*</span> Broj telefona:</label>
								
								<div class="col-sm-9">
									<input class="form-control materail-input" type="tel" name="kki_podatak1" id="kki_podatak1" value="<?php echo $kki_podatak; ?>">

									<div class="materail-input-block materail-input-block_success">

										<span class="materail-input-block__line"></span>
									</div>
								</div>
							</div>
							
							<div id="primary_phone1">
								<br>

								<div class="alert alert-warning">
									Postojeće kontakt informacije koje su označene kao primarne se prebacuju kao sekundarni kontakt ukoliko nove kontakt informacije označimo oznakom "Primarni".
								</div>
							</div>
						</div>

						<div id="email1">
							<div class="form-group">
								<label for="kki_naziv_email1" class="col-sm-3 control-label"><span class="text-danger">*</span> E-mail:</label>
								
								<div class="col-sm-9">
									<div class="materail-input-block materail-input-block_success">
										<input class="form-control materail-input" type="email" name="kki_naziv_email" id="kki_naziv_email1" value="<?php echo $kki_podatak; ?>">
										<span class="materail-input-block__line"></span>
									</div>
								</div>
							</div>

							<div id="primary_email1">
								<br>

								<div class="alert alert-warning">
									Postojeće kontakt informacije koje su označene kao primarne se prebacuju kao sekundarni kontakt ukoliko nove kontakt informacije označimo oznakom "Primarni".
								</div>
							</div>
						</div>
						
						<div id="web1">
							<div class="form-group">
								<label for="kki_naziv_web1" class="col-sm-3 control-label"><span class="text-danger">*</span> Web:</label>

								<div class="col-sm-9">
									<div class="materail-input-block materail-input-block_success">
										<input class="form-control materail-input" type="text" name="kki_naziv_web1" id="kki_naziv_web1" value="<?php echo $kki_podatak; ?>" >
										<span class="materail-input-block__line"></span>
									</div>
								</div>
							</div>
						</div>

						<div id="messangeri1">
							<div class="form-group">
								<label for="kki_naziv_messangeri1" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>

								<div class="col-sm-9">
									<div class="materail-input-block materail-input-block_success">
										<input class="form-control materail-input" type="text" name="kki_naziv_messangeri1" id="kki_naziv_messangeri1" value="<?php echo $kki_naziv; ?>" >
										<span class="materail-input-block__line"></span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="kki_podatak_messangeri" class="col-sm-3 control-label"><span class="text-danger">*</span> Kontakt:</label>
								<div class="col-sm-9">
									<div class="materail-input-block materail-input-block_success">
										<input class="form-control materail-input" type="text" name="kki_podatak_messangeri" id="kki_podatak_messangeri" value="<?php echo $kki_podatak; ?>" >
										<span class="materail-input-block__line"></span>
									</div>
								</div>
							</div>
						</div>

						<div class="modal-footer" style="padding-top: 15px; margin-top: 40px; margin-bottom: -15px;">
							<div class="form-group" style="margin-bottom: 0;">
								<div class="col-sm-offset-2 col-sm-10" style="padding-right: 0;">
									<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" style="margin-bottom: 0;"><i class="fa fa-plus" aria-hidden="true" style="border-radius: 2px;"></i> <span>Uredi</span></button>
								</div>
							</div>
						</div>
					</form>

					<script>
						$(document).ready(function () {

							$('.selectpicker').selectpicker({});
							$('#loaderContent > div > form > div.form-group > div > div > button').css('color', 'silver');

							<?php 
							if ($kki_grupa_id == 1) { 
								
								?>
								$('#telefon1').show();
								$('#email1').hide();
								$('#web1').hide();
								$('#messangeri1').hide();	

								$("#kki_naziv_email1").prop('disabled', true);
								<?php 

							} else if ($kki_grupa_id == 2) { 
								
								?>
								$('#telefon1').hide();
								$('#email1').show();
								$('#web1').hide();

								$('#messangeri1').hide();	

								$("#kki_naziv_email1").prop('disabled', false);
								<?php 

							} else if ($kki_grupa_id == 3) {
								
								?>
								$('#telefon1').hide();

								$('#email1').hide();

								$('#web1').show();

								$('#messangeri1').hide();	

								$("#kki_naziv_email1").prop('disabled', true);
								<?php 

							} else if ($kki_grupa_id == 4) {
								
								?>
								$('#telefon1').hide();

								$('#email1').hide();

								$('#web1').hide();

								$('#messangeri1').show();	

								$("#kki_naziv_email1").prop('disabled', true);
								<?php 

							}
							?>
							
							$('#kki_grupa1').on('change', function (e) {

								if (($('#kki_grupa1').selectpicker('val') == "1")) {

									$('#telefon1').slideDown();
									$('#email1').slideUp();
									$('#web1').slideUp();
									$('#messangeri1').slideUp();

								}

								if (($('#kki_grupa1').selectpicker('val') == "2")) {

									$('#telefon1').slideUp();
									$('#email1').slideDown();
									$('#web1').slideUp();
									$('#messangeri1').slideUp();

								}

								if (($('#kki_grupa1').selectpicker('val') == "3")) {

									$('#telefon').slideUp();
									$('#email1').slideUp();
									$('#web1').slideDown();
									$('#messangeri1').slideUp();

								}

								if(($('#kki_grupa1').selectpicker('val') == "4")) {

									$('#telefon1').slideUp();
									$('#email1').slideUp();
									$('#web1').slideUp();
									$('#messangeri1').slideDown();

								}
								
							});

							$('#kki_naziv1').on('change', function (e) {

								if      (document.querySelector("#kki_naziv1").value == 'Mobilni') document.querySelector("#primary_phone1").style.display = "block";
								else if (document.querySelector("#kki_naziv1").value == 'Fiksni') document.querySelector("#primary_phone1").style.display = "none";

							});

							document.querySelectorAll('#telefon1 > div:nth-child(2) > div > div > div').forEach((element) => {
								element.style.width = "100%";
							});

							var telInput = document.getElementById("kki_podatak1");
										
							if (telInput.value.startsWith("+") || telInput.value == '') {
								
								iti = window.intlTelInput(telInput, {
									utilsScript:'<?php getSiteUrl(); ?>buildTelInput/js/utils.js',
									autoPlaceholder: "aggressive",
									initialCountry: "ba",
									formatOnDisplay: true,
									preferredCountries: ["ba","rs","hr","de"],
									separateDialCode: true
								});
								
								$("form").submit(function(event) {
																		
									$("#kki_podatak1").val(iti.getNumber()); 

								});

								document.querySelector("#telefon1 > div:nth-child(2) > div > div.iti.iti--allow-dropdown.iti--separate-dial-code").style.width = "100%";
								
							}

						});
					</script>
					<?php

				break;
				
				case "edit_kandidat_edukacija":
				
				$id = $_REQUEST['id'];
				$kandidat_id = $_REQUEST['kandidat_id'];
				
				$select_query = $db->prepare("
									SELECT ke_id, ke_datumod, ke_datumdo, ke_naziv_kvalifikacije, ke_naziv, ke_grad, ke_opis, ke_drzava, ke_vrsta_obrazovanja, ke_prikaz_pp, ke_smjer_id, ke_aktuelno
									FROM idk_kandidat_edukacija
									WHERE ke_id = :ke_id");

				$select_query->execute(array(
								':ke_id' => $id));

				$select_row = $select_query->fetch();

					$ke_id = $select_row['ke_id'];
					$ke_datumod = $select_row['ke_datumod'];
					$ke_datumod_f = date('m-Y', strtotime($ke_datumod));
					$ke_datumdo = $select_row['ke_datumdo'];
					$ke_naziv_kvalifikacije = $select_row['ke_naziv_kvalifikacije'];
					$ke_naziv = $select_row['ke_naziv'];
					$ke_grad = $select_row['ke_grad'];
					$ke_drzava = $select_row['ke_drzava'];
					$ke_vrsta_obrazovanja = $select_row['ke_vrsta_obrazovanja'];
					$ke_opis = $select_row['ke_opis'];
					$ke_prikaz_pp = $select_row['ke_prikaz_pp'];
					$ke_smjer_id = $select_row['ke_smjer_id'];
					$ke_aktuelno = $select_row['ke_aktuelno'];
					
					if($ke_aktuelno != 1){
						$ke_datumdo_f = date('m-Y', strtotime($ke_datumdo));
					}else{
						$ke_datumdo_f = "Aktuelno";
					}	

		?>
		<form action="<?php getSiteURL(); ?>do.php?form=edit_kandidat_edukacija" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
			<input type="hidden" name="ke_kandidat_id" value="<?php echo $kandidat_id; ?>">
			<input type="hidden" name="ke_id" value="<?php echo $ke_id; ?>">
			<div class="form-group">
				<label for="ke_datumod" class="col-sm-4 control-label"><span class="text-danger">*</span> Datum od:</label>
				<div class="col-sm-8">
					<div class="materail-input-block materail-input-block_success">
						<input class="form-control materail-input" type="text" name="ke_datumod" id="ke_datumod1" class="monthPicker" value="<?php if($ke_datumod != null){ echo $ke_datumod_f; } ?>" required>
						<span class="materail-input-block__line"></span>
					</div>
				</div>
				<style>
				.ui-datepicker-calendar {
					display: none;
				}										
				</style>
				<script>
				$('.ui-datepicker-calendar').hide();
				$(document).ready(function(){
					$("#ke_datumod1").datepicker({ 
						dateFormat: 'mm.yy',
						changeMonth: true,
						changeYear: true,
						yearRange: '1940:<?php echo date('Y'); ?>',
						showButtonPanel: true,
						onClose: function(dateText, inst) {  
							var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val(); 
							var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val(); 
							$(this).val($.datepicker.formatDate('mm-yy', new Date(year, month, 1)));
						}
					});
					$(".monthPicker").focus(function () {
						$(".ui-datepicker-calendar").addClass('display_none');
						$(".ui-datepicker-calendar").hide();
						$("#ui-datepicker-div").position({
							my: "center top",
							at: "center bottom",
							of: $(this)
						});    
					});
					
				});

				</script>
			</div>
			<div class="form-group">
				<label for="ke_datumdo" class="col-sm-4 control-label"><span class="text-danger">*</span> Datum do:</label>
				<div class="col-sm-5">
					<div class="materail-input-block materail-input-block_success">
						<input class="form-control materail-input monthPicker" type="text" name="ke_datumdo" id="ke_datumdo1" <?php if($ke_aktuelno == 1 or $ke_datumdo == null){echo "disabled";}else{ echo "value='$ke_datumdo_f'";} ?> required>
						<span class="materail-input-block__line"></span>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="main-container__column material-checkbox-group material-checkbox-group_primary idk_margin_top10">
						<input type="checkbox" id="ke_datumdo_aktuelno" name="ke_datumdo_aktuelno" class="material-checkbox" <?php if($ke_aktuelno == 1){echo "checked";} ?>>
						<label class="material-checkbox-group__label" for="ke_datumdo_aktuelno">Aktuelno</label>
					</div>	
				</div>
				<script>
				$( document ).ready(function() {
					$('#ke_datumdo_aktuelno').click(function()
					{
						//If checkbox is checked then disable or enable input
						if ($(this).is(':checked'))
						{
							$("#ke_datumdo1").removeAttr("disabled"); 
							$("#ke_datumdo1").attr("disabled","disabled");
							$("#ke_datumdo1").val('');
						}
						//If checkbox is unchecked then disable or enable input
						else
						{
							$("#ke_datumdo1").removeAttr("disabled"); 
							$("#ke_datumdo1").val('');
						}
					});
				});
				</script>
				<script>
				$('.ui-datepicker-calendar').hide();
				$(document).ready(function(){
					$("#ke_datumdo1").datepicker({ 
						dateFormat: 'mm.yy',
						changeMonth: true,
						changeYear: true,
						yearRange: '1940:<?php echo date('Y'); ?>',
						showButtonPanel: true,
						onClose: function(dateText, inst) {  
							var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val(); 
							var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val(); 
							$(this).val($.datepicker.formatDate('mm-yy', new Date(year, month, 1)));
						}
					});
					$(".monthPicker").focus(function () {
						$(".ui-datepicker-calendar").addClass('display_none');
						$(".ui-datepicker-calendar").hide();
						$("#ui-datepicker-div").position({
							my: "center top",
							at: "center bottom",
							of: $(this)
						});    
					});
					
				});

				</script>
				<script>$('.selectpicker').selectpicker({});</script>
			</div>
			<div class="form-group">
				<label for="ke_grad" class="col-sm-4 control-label"><span class="text-danger">*</span> Grad:</label>
				<div class="col-sm-8">
					<div class="materail-input-block materail-input-block_success">
						<input class="form-control materail-input" type="text" name="ke_grad" id="ke_grad" value="<?php echo $ke_grad; ?>" >
						<span class="materail-input-block__line"></span>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="ke_drzava" class="col-sm-4 control-label"><span class="text-danger">*</span> Država:</label>
				<div class="col-sm-8">
					<select class="selectpicker" id="ke_drzava" name="ke_drzava">
						<?php 
						if(
							in_array(
								trim(strtolower($ke_drzava)), 
								array(
									'bih', 'bosna i hercegovina', 'bosna', 'bosna i herzegovina', 'republika srpska', 'b i h', 'bosnien und herzegowina',
									'hrvatska', 'kroatien', 'republika hrvatska',
									'njemačka', 'deutschland', 'nemačka',
									'srbija', 'republika srbija', 'serbia', 'r srbija', 'serbien',
									'crna gora', 'montenegro',
									'makedonija', 'macedonia', 'sjeverna makedonija',
									'albanija', 'austrija', 'bugarska', 'danska', 'italija', 'kosovo', 'mađarska', 'slovenija', 'švicarska')
							)
						){}else{
							?>
							<option value="<?php echo $ke_drzava; ?>" selected style="color: red;"><?php echo $ke_drzava." - Nije prevedeno!"; ?></option>
							<?php
							// IMPORTANT !!! Ako budu htjeli dodati novu državu potrebno ju je dodati ovdje, (treba varijacije imena ovdje uključiti i iznad i ispod ovog komentara)
							// DOdati i na kandidat.php kod komentara: "OVDJE DODATI NOVU DRZAVU kandidati.php" (treba varijacije imena ovdje uključiti)
							// Dodati i na insertu nove skole, kod komentara: "OVDJE DODATI NOVU DRZAVU insert"
							// Dodati i na PP prevodu kod komentara: "PP PREVOD DRŽAVE"
							// Dodati i na carglass exportu u query iznad koga je komentar: "DODATI U QUERYU ISPOD DRZAVU ZA PREVOD" (treba varijacije imena ovdje uključiti)
						}	?>
						
    					<option value="Bosna i Hercegovina" 
							<?php 
							if(
								trim(strtolower($ke_drzava)) == "bosna i hercegovina" 
								OR trim(strtolower($ke_drzava)) == "bih"
								OR trim(strtolower($ke_drzava)) == "bosna"
								OR trim(strtolower($ke_drzava)) == "bosna i herzegovina"
								OR trim(strtolower($ke_drzava)) == "republika srpska"
								OR trim(strtolower($ke_drzava)) == "b i h"
								OR trim(strtolower($ke_drzava)) == "bosnien und herzegowina"
							){echo "selected";} 
							?>>
							Bosna i Hercegovina
						</option>
    					<option value="Hrvatska" 
							<?php 
							if(
								trim(strtolower($ke_drzava)) == "hrvatska"
								OR trim(strtolower($ke_drzava)) == "kroatien"
								OR trim(strtolower($ke_drzava)) == "republika hrvatska"
							){echo "selected";} 
							?>>
							Hrvatska
						</option>
    					<option value="Njemačka" 
							<?php 
							if(
								trim(strtolower($ke_drzava)) == "njemačka"
								OR trim(strtolower($ke_drzava)) == "deutschland"
								OR trim(strtolower($ke_drzava)) == "nemačka"
							){echo "selected";} 
							?>>
							Njemačka
						</option>
    					<option value="Srbija" 
							<?php 
							if(
								trim(strtolower($ke_drzava)) == "srbija"
								OR trim(strtolower($ke_drzava)) == "republika srbija"
								OR trim(strtolower($ke_drzava)) == "serbia"
								OR trim(strtolower($ke_drzava)) == "r srbija"
								OR trim(strtolower($ke_drzava)) == "serbien"
							){echo "selected";} 
							?>>
							Srbija
						</option>
    					<option value="Albanija" <?php if(trim(strtolower($ke_drzava)) == "albanija"){echo "selected";} ?>>Albanija</option>
    					<option value="Austrija" <?php if(trim(strtolower($ke_drzava)) == "austrija"){echo "selected";} ?>>Austrija</option>
    					<option value="Bugarska" <?php if(trim(strtolower($ke_drzava)) == "bugarska"){echo "selected";} ?>>Bugarska</option>
    					<option value="Crna Gora" 
							<?php 
							if(
								trim(strtolower($ke_drzava)) == "crna gora"
								OR trim(strtolower($ke_drzava)) == "montenegro"
							){echo "selected";} 
							?>>
							Crna Gora
						</option>
    					<option value="Danska" <?php if(trim(strtolower($ke_drzava)) == "danska"){echo "selected";} ?>>Danska</option>
    					<option value="Italija" <?php if(trim(strtolower($ke_drzava)) == "italija"){echo "selected";} ?>>Italija</option>
    					<option value="Kosovo" <?php if(trim(strtolower($ke_drzava)) == "kosovo"){echo "selected";} ?>>Kosovo</option>
    					<option value="Mađarska" <?php if(trim(strtolower($ke_drzava)) == "mađarska"){echo "selected";} ?>>Mađarska</option>
    					<option value="Makedonija" 
							<?php 
							if(
								trim(strtolower($ke_drzava)) == "makedonija"
								OR trim(strtolower($ke_drzava)) == "macedonia"
								OR trim(strtolower($ke_drzava)) == "sjeverna makedonija"
							){echo "selected";} 
							?>>
							Makedonija
						</option>
    					<option value="Slovenija" <?php if(trim(strtolower($ke_drzava)) == "slovenija"){echo "selected";} ?>>Slovenija</option>
    					<option value="Švicarska" <?php if(trim(strtolower($ke_drzava)) == "švicarska"){echo "selected";} ?>>Švicarska</option>
    				</select>
				</div>
			</div>
			<div class="form-group">
				<label for="ke_vrsta" class="col-sm-4 control-label"><span class="text-danger">*</span> Vrsta obrazovanja:</label>
				<div class="col-sm-8">
					<select class="selectpicker" id="ke_vrsta" name="ke_vrsta">
						<option value=""></option>
    					<option value="srednje" <?php if($ke_vrsta_obrazovanja == "srednje"){echo "selected";} ?>>Srednje</option>
    					<option value="visoko" <?php if($ke_vrsta_obrazovanja == "visoko"){echo "selected";} ?>>Visoko</option>
    					<option value="ostalo" <?php if($ke_vrsta_obrazovanja == "ostalo"){echo "selected";} ?>>Ostalo/Dodatno</option>
    				</select>
				</div>
			</div>
			<div class="form-group">
				<label for="ke_opis" class="col-sm-4 control-label"><span class="text-danger"></span> Opis:</label>
				<div class="col-sm-8">
					<div class="materail-input-block materail-input-block_success">
						<textarea class="form-control materail-input material-textarea" name="ke_opis" id="ke_opis"><?php echo $ke_opis; ?></textarea>
						<span class="materail-input-block__line"></span>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="ke_prikaz_pp" class="col-sm-3 control-label">Prikaz PP:</label>
				<div class="col-sm-9">
					<div class="main-container__column materail-switch materail-switch_primary">
						<input class="materail-switch__element" type="checkbox" id="ke_prikaz_pp" name="ke_prikaz_pp" value="1" <?php if($ke_prikaz_pp == 1){ echo "checked"; } if($ke_smjer_id == null){ echo "disabled";} ?>>
						<label class="materail-switch__label" for="ke_prikaz_pp"></label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10 text-right">
					<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span>Uredi</span></button>
				</div>
			</div>
		</form>
		
		<?php
				break;		

				case "edit_kandidat_iskustvo":
				
				$id = $_REQUEST['id'];
				$kandidat_id = $_REQUEST['kandidat_id'];
				
				$select_query = $db->prepare("
									SELECT kri_id, kri_darum_od, kri_datum_do, kri_pozicija, kri_pozicija_de, kri_pozicija_en, kri_naziv, kri_grad, kri_opis_de, kri_prikaz_pp, kri_aktuelno
									FROM idk_kandidat_radno_iskustvo
									WHERE kri_id = :kri_id");

				$select_query->execute(array(
								':kri_id' => $id));

				$select_row = $select_query->fetch();

					$kri_id = $select_row['kri_id'];
					$kri_darum_od = $select_row['kri_darum_od'];
					$kri_darum_od_f = date('m-Y', strtotime($kri_darum_od));
					$kri_datum_do = $select_row['kri_datum_do'];
					$kri_datum_do_f = date('m-Y', strtotime($kri_datum_do));
					$kri_pozicija = $select_row['kri_pozicija'];
					$kri_pozicija_de = $select_row['kri_pozicija_de'];
					$kri_pozicija_en = $select_row['kri_pozicija_en'];
					$kri_naziv = $select_row['kri_naziv'];
					$kri_grad = $select_row['kri_grad'];
					$kri_opis = $select_row['kri_opis_de'];
					$kri_prikaz_pp = $select_row['kri_prikaz_pp'];
					$kri_aktuelno = $select_row['kri_aktuelno'];
					
					
					if($kri_aktuelno != 1){
						$kri_datum_do_f = date('m-Y', strtotime($kri_datum_do));
					}else{
						$kri_datum_do_f = "Aktuelno";
					}	

		?>
								<form action="<?php getSiteURL(); ?>do.php?form=edit_kandidat_iskustvo" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="kri_kandidat_id" value="<?php echo $kandidat_id; ?>">
									<input type="hidden" name="kri_id" value="<?php echo $kri_id; ?>">
									<div class="form-group">
										<label for="kri_darum_od" class="col-sm-3 control-label"><span class="text-danger">*</span> Datum od:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input monthPicker" type="text" name="kri_darum_od" id="kri_darum_od1" value="<?php echo $kri_darum_od_f; ?>" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<style>
										.ui-datepicker-calendar {
											display: none;
										}										
										</style>
										<script>
										$('.ui-datepicker-calendar').hide();
										$(document).ready(function(){
											$("#kri_darum_od1").datepicker({ 
												dateFormat: 'mm.yy',
												changeMonth: true,
												changeYear: true,
												yearRange: '1940:<?php echo date('Y'); ?>',
												showButtonPanel: true,
												onClose: function(dateText, inst) {  
													var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val(); 
													var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val(); 
													$(this).val($.datepicker.formatDate('mm-yy', new Date(year, month, 1)));
												}
											});
											$(".monthPicker").focus(function () {
												$(".ui-datepicker-calendar").addClass('display_none');
												$(".ui-datepicker-calendar").hide();
												$("#ui-datepicker-div").position({
													my: "center top",
													at: "center bottom",
													of: $(this)
												});    
											});
											
										});

										</script>
									</div>

									<div class="form-group">
										<label for="kri_datum_do2" class="col-sm-3 control-label"><span class="text-danger">*</span> Datum do:</label>
										<div class="col-sm-6">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input monthPicker" type="text" name="kri_datum_do" id="kri_datum_do2" <?php if($kri_aktuelno == 1 or $kri_datum_do == null){echo "disabled";}else{ echo "value='$kri_datum_do_f'";} ?>>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="main-container__column material-checkbox-group material-checkbox-group_primary idk_margin_top10">
												<input type="checkbox" name="kri_datum_do_aktuelno" id="kri_datum_do_aktuelno2" class="material-checkbox" <?php if($kri_aktuelno == 1 or $kri_datum_do == null){echo "checked";} ?>>
												<label class="material-checkbox-group__label" for="kri_datum_do_aktuelno2">Aktuelno</label>
											</div>	
										</div>
										<script>
										$('.ui-datepicker-calendar').hide();
										$(document).ready(function(){
											$("#kri_datum_do2").datepicker({ 
												dateFormat: 'mm.yy',
												changeMonth: true,
												changeYear: true,
												yearRange: '1940:<?php echo date('Y'); ?>',
												showButtonPanel: true,
												onClose: function(dateText, inst) {  
													var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val(); 
													var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val(); 
													$(this).val($.datepicker.formatDate('mm-yy', new Date(year, month, 1)));
												}
											});
											$(".monthPicker").focus(function () {
												$(".ui-datepicker-calendar").addClass('display_none');
												$(".ui-datepicker-calendar").hide();
												$("#ui-datepicker-div").position({
													my: "center top",
													at: "center bottom",
													of: $(this)
												});    
											});
											
										});

										</script>
										<script>
										$( document ).ready(function() {
											$('#kri_datum_do_aktuelno2').click(function()
											{
												//If checkbox is checked then disable or enable input
												if ($(this).is(':checked'))
												{
													$("#kri_datum_do2").removeAttr("disabled"); 
													$("#kri_datum_do2").attr("disabled","disabled");
													$("#kri_datum_do2").removeAttr("required"); 
													$("#kri_datum_do2").val('');
												}
												//If checkbox is unchecked then disable or enable input
												else
												{
													$("#kri_datum_do2").removeAttr("disabled");
													$("#kri_datum_do2").attr("required","required"); 
													$("#kri_datum_do2").val('');
												}
											});
										});
										</script>

									</div>
									<div class="form-group">
										<label for="kri_pozicija" class="col-sm-3 control-label"><span class="text-danger">*</span> Pozicija: <i class="fa fa-info-circle fa-lg idk_margin_left10" data-toggle="tooltip" data-placement="right" title="etc.  Medicinski tehničar, glavna medicinska sestra, trgovac ..." aria-hidden="true"></i></label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kri_pozicija" id="kri_pozicija" value="<?php echo $kri_pozicija; ?>" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kri_pozicija_de" class="col-sm-3 control-label"><span class="text-danger">*</span> Pozicija DE: </label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kri_pozicija_de" id="kri_pozicija_de" value="<?php echo $kri_pozicija_de; ?>" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kri_pozicija_en" class="col-sm-3 control-label"><span class="text-danger">*</span> Pozicija EN: </label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kri_pozicija_en" id="kri_pozicija_en" value="<?php echo $kri_pozicija_en; ?>" >
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kri_naziv" class="col-sm-3 control-label"><span class="text-danger"></span> Naziv poslodavca:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kri_naziv" id="kri_naziv" value="<?php echo $kri_naziv; ?>" >
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kri_grad" class="col-sm-3 control-label"><span class="text-danger"></span> Grad:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kri_grad" id="kri_grad" value="<?php echo $kri_grad; ?>" >
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kri_opis" class="col-sm-3 control-label"><span class="text-danger"></span> Opis (Njemački):</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<textarea class="form-control materail-input material-textarea" name="kri_opis_de" id="kri_opis_de"><?php echo $kri_opis_de; ?></textarea>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kri_prikaz_pp" class="col-sm-3 control-label">Prikaz PP:</label>
										<div class="col-sm-9">
											<div class="main-container__column materail-switch materail-switch_primary">
												<input class="materail-switch__element" type="checkbox" id="kri_prikaz_pp" name="kri_prikaz_pp" value="1" <?php if($kri_prikaz_pp == 1){ echo "checked"; } ?>>
												<label class="materail-switch__label" for="kri_prikaz_pp"></label>
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-offset-2 col-sm-10 text-right">
											<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span>Uredi</span></button>
										</div>
									</div>
								</form>
		
		<?php
				break;		

				case "edit_kandidat_vjestine":
				
				$id = $_REQUEST['id'];
				$kandidat_id = $_REQUEST['kandidat_id'];
				
				$select_query = $db->prepare("
									SELECT kv_id, kv_naziv, kv_grupa, kv_opis
									FROM idk_kandidat_vjestine
									WHERE kv_id = :kv_id");

				$select_query->execute(array(
								':kv_id' => $id));

				$select_row = $select_query->fetch();

					$kv_id = $select_row['kv_id'];
					$kv_naziv = $select_row['kv_naziv'];
					$kv_grupa = $select_row['kv_grupa'];
					if($kv_grupa == 1){
						$kv_grupa_naziv = "Osnovne vještine";
					}elseif($kv_grupa == 2){
						$kv_grupa_naziv = "Digitalne kompetencije";
					}elseif($kv_grupa == 3){
						$kv_grupa_naziv = "Dodatne informacije";
					}else{};
					$kv_opis = $select_row['kv_opis'];	

		?>
		<script>$('.selectpicker').selectpicker({});</script>
		<form action="<?php getSiteURL(); ?>do.php?form=edit_kandidat_vjestine" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
		<input type="hidden" name="kv_kandidat_id" value="<?php echo $kandidat_id; ?>">
		<input type="hidden" name="kv_id" value="<?php echo $kv_id; ?>">
		<div class="form-group">
			<label for="kv_grupa" class="col-sm-3 control-label"><span class="text-danger">*</span> Tip vještine:</label>
			<div class="col-sm-9">
                <select class="selectpicker" id="kv_grupa" name="kv_grupa">
                    <option value=""></option>
    				<option value="1" <?php if($kv_grupa == 1){echo "selected";}else{} ?>>Osnovne vještine</option>
    				<option value="2" <?php if($kv_grupa == 2){echo "selected";}else{} ?>>Digitalne kompetencije</option>
    				<option value="3" <?php if($kv_grupa == 3){echo "selected";}else{} ?>>Dodatne informacije</option>
    			</select>
			</div>
		</div>
		<div class="form-group">
			<label for="kv_naziv" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv: <i class="fa fa-info-circle fa-lg idk_margin_left10" data-toggle="tooltip" data-placement="right" title="etc. rad na microsoft alatima, poznavanje rada na računaru ..." aria-hidden="true"></i></label>
			<div class="col-sm-9">
				<div class="materail-input-block materail-input-block_success">
					<input class="form-control materail-input" type="text" name="kv_naziv" id="kv_naziv" value="<?php echo $kv_naziv; ?>" >
					<span class="materail-input-block__line"></span>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label for="kv_opis" class="col-sm-3 control-label"><span class="text-danger"></span> Opis:</label>
			<div class="col-sm-9">
				<div class="materail-input-block materail-input-block_success">
					<textarea class="form-control materail-input material-textarea" name="kv_opis" id="kv_opis"><?php echo $kv_opis; ?></textarea>
					<span class="materail-input-block__line"></span>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10 text-right">
				<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span>Uredi</span></button>
			</div>
		</div>
	</form>
		
		<?php
		/*		<?php
				break;	

				case "edit_kandidat_jezik":
				
				$id = $_REQUEST['id'];
				$kandidat_id = $_REQUEST['kandidat_id'];
				
				$select_query = $db->prepare("
									SELECT kj_id, kj_naziv, kj_slusanje, kj_citanje, kj_govorna_interakcija, kj_govorna_produkcija, kj_pisanje
									FROM idk_kandidat_jezici
									WHERE kj_id = :kj_id");

				$select_query->execute(array(
								':kj_id' => $id));

				$select_row = $select_query->fetch();

					$kj_id = $select_row['kj_id'];
					$kj_naziv = $select_row['kj_naziv'];
					$kj_slusanje = $select_row['kj_slusanje'];
					$kj_citanje = $select_row['kj_citanje'];
					$kj_govorna_interakcija = $select_row['kj_govorna_interakcija'];
					$kj_govorna_produkcija = $select_row['kj_govorna_produkcija'];
					$kj_pisanje = $select_row['kj_pisanje'];

		?>
			<script>$('.selectpicker').selectpicker({});</script>
			<form action="<?php getSiteURL(); ?>do.php?form=edit_kandidat_jezik" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
			<input type="hidden" name="kj_kandidatid" value="<?php echo $kandidat_id; ?>">
			<input type="hidden" name="kj_id" value="<?php echo $kj_id; ?>">
			<div class="form-group">
				<label for="kj_naziv" class="col-sm-3 control-label"><span class="text-danger">*</span> Jezik:</label>
				<div class="col-sm-9">
                    <select class="selectpicker" id="kj_naziv" name="kj_naziv">
                         <option value=""></option>
    					<option value="Engleski" <?php if($kj_naziv == "Engleski"){echo "selected";} ?>>Engleski</option>
    					<option value="Njemački" <?php if($kj_naziv == "Njemački"){echo "selected";} ?>>Njemački</option>
    					<option value="Talijanski" <?php if($kj_naziv == "Talijanski"){echo "selected";} ?>>Talijanski</option>
    					<option value="Francuski" <?php if($kj_naziv == "Francuski"){echo "selected";} ?>>Francuski</option>
    					<option value="Španjolski" <?php if($kj_naziv == "Španjolski"){echo "selected";} ?>>Španjolski</option>
    					<option value="Arapski" <?php if($kj_naziv == "Arapski"){echo "selected";} ?>>Arapski</option>
    					<option value="Danski" <?php if($kj_naziv == "Danski"){echo "selected";} ?>>Danski</option>
    				</select>
				</div>
			</div>
			<div class="form-group">
				<label for="kj_slusanje" class="col-sm-3 control-label"><span class="text-danger">*</span> Slušanje:</label>
				<div class="col-sm-9">
                                      <select class="selectpicker" id="kj_slusanje" name="kj_slusanje">
                                          <option value=""></option>
    					<option value="Bez znanja" <?php if($kj_slusanje == "Bez znanja"){echo "selected";} ?>>Bez znanja</option>
    					<option value="A1" <?php if($kj_slusanje == "A1"){echo "selected";} ?>>A1</option>
    					<option value="A2" <?php if($kj_slusanje == "A2"){echo "selected";} ?>>A2</option>
    					<option value="B1" <?php if($kj_slusanje == "B1"){echo "selected";} ?>>B1</option>
    					<option value="B2" <?php if($kj_slusanje == "B2"){echo "selected";} ?>>B2</option>
    					<option value="C1" <?php if($kj_slusanje == "C1"){echo "selected";} ?>>C1</option>
    					<option value="C2" <?php if($kj_slusanje == "C2"){echo "selected";} ?>>C2</option>
    				</select>
				</div>
			</div>
			<div class="form-group">
				<label for="kj_citanje" class="col-sm-3 control-label"><span class="text-danger">*</span> Čitanje:</label>
				<div class="col-sm-9">
                                      <select class="selectpicker" id="kj_citanje" name="kj_citanje">
                                          <option value=""></option>
    					<option value="Bez znanja" <?php if($kj_citanje == "Bez znanja"){echo "selected";} ?>>Bez znanja</option>
    					<option value="A1" <?php if($kj_citanje == "A1"){echo "selected";} ?>>A1</option>
    					<option value="A2" <?php if($kj_citanje == "A2"){echo "selected";} ?>>A2</option>
    					<option value="B1" <?php if($kj_citanje == "B1"){echo "selected";} ?>>B1</option>
    					<option value="B2" <?php if($kj_citanje == "B2"){echo "selected";} ?>>B2</option>
    					<option value="C1" <?php if($kj_citanje == "C1"){echo "selected";} ?>>C1</option>
    					<option value="C2" <?php if($kj_citanje == "C2"){echo "selected";} ?>>C2</option>
    				</select>
				</div>
			</div>
			<div class="form-group">
				<label for="kj_govorna_interakcija" class="col-sm-3 control-label"><span class="text-danger">*</span> Govorna interakcija:</label>
				<div class="col-sm-9">
                                      <select class="selectpicker" id="kj_govorna_interakcija" name="kj_govorna_interakcija">
                                          <option value=""></option>
    					<option value="Bez znanja" <?php if($kj_govorna_interakcija == "Bez znanja"){echo "selected";} ?>>Bez znanja</option>
    					<option value="A1" <?php if($kj_govorna_interakcija == "A1"){echo "selected";} ?>>A1</option>
    					<option value="A2" <?php if($kj_govorna_interakcija == "A2"){echo "selected";} ?>>A2</option>
    					<option value="B1" <?php if($kj_govorna_interakcija == "B1"){echo "selected";} ?>>B1</option>
    					<option value="B2" <?php if($kj_govorna_interakcija == "B2"){echo "selected";} ?>>B2</option>
    					<option value="C1" <?php if($kj_govorna_interakcija == "C1"){echo "selected";} ?>>C1</option>
    					<option value="C2" <?php if($kj_govorna_interakcija == "C2"){echo "selected";} ?>>C2</option>
    				</select>
				</div>
			</div>
			<div class="form-group">
				<label for="kj_govorna_produkcija" class="col-sm-3 control-label"><span class="text-danger">*</span> Govorna produkcija:</label>
				<div class="col-sm-9">
                                      <select class="selectpicker" id="kj_govorna_produkcija" name="kj_govorna_produkcija">
                                          <option value=""></option>
    					<option value="Bez znanja" <?php if($kj_govorna_produkcija == "Bez znanja"){echo "selected";} ?>>Bez znanja</option>
    					<option value="A1" <?php if($kj_govorna_produkcija == "A1"){echo "selected";} ?>>A1</option>
    					<option value="A2" <?php if($kj_govorna_produkcija == "A2"){echo "selected";} ?>>A2</option>
    					<option value="B1" <?php if($kj_govorna_produkcija == "B1"){echo "selected";} ?>>B1</option>
    					<option value="B2" <?php if($kj_govorna_produkcija == "B2"){echo "selected";} ?>>B2</option>
    					<option value="C1" <?php if($kj_govorna_produkcija == "C1"){echo "selected";} ?>>C1</option>
    					<option value="C2" <?php if($kj_govorna_produkcija == "C2"){echo "selected";} ?>>C2</option>
    				</select>
				</div>
			</div>
			<div class="form-group">
				<label for="kj_pisanje" class="col-sm-3 control-label"><span class="text-danger">*</span> Pisanje:</label>
				<div class="col-sm-9">
                    <select class="selectpicker" id="kj_pisanje" name="kj_pisanje">
                        <option value=""></option>
    					<option value="Bez znanja" <?php if($kj_pisanje == "Bez znanja"){echo "selected";} ?>>Bez znanja</option>
    					<option value="A1" <?php if($kj_pisanje == "A1"){echo "selected";} ?>>A1</option>
    					<option value="A2" <?php if($kj_pisanje == "A2"){echo "selected";} ?>>A2</option>
    					<option value="B1" <?php if($kj_pisanje == "B1"){echo "selected";} ?>>B1</option>
    					<option value="B2" <?php if($kj_pisanje == "B2"){echo "selected";} ?>>B2</option>
    					<option value="C1" <?php if($kj_pisanje == "C1"){echo "selected";} ?>>C1</option>
    					<option value="C2" <?php if($kj_pisanje == "C2"){echo "selected";}  ?>>C2</option>
    				</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10 text-right">
					<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span>Uredi</span></button>
				</div>
			</div>
		</form>  */
		?>
		
		<?php
				break;	

				case "provjeri_jezik_kandidata":
				
				$cvl_id = $_REQUEST['id'];
				$kandidat_id = $_REQUEST['kandidat_id'];
				$select_nivo=$db->prepare("
							SELECT kj_slusanje from idk_kandidat_jezici WHERE kj_id=:kj_id
				");
				$select_nivo->execute(array(':kj_id' => $cvl_id));
				$select_row_nivo=$select_nivo->fetch();
				$kj_slusanje=$select_row_nivo['kj_slusanje'];

				if (getActiveLanguage($kandidat_id) != null) {
					$select_query_jezici = $db->prepare("
						SELECT 
							kj.kj_id,
							kj.kj_kandidatid,
							kj.kj_slusanje,
							kj.kj_ustanova,
							cvl.cvl_status,
							cvl.cvl_course1_started,
							cvl.cvl_course1_ended, 
							cvl.cvl_course2_started,
							cvl.cvl_course2_ended, 
							cvl.cvl_exam_date, 
							cvl.cvl_certificate_path, 
							cvl.cvl_certficate_creation_date, 
							cvl.cvl_certificate_expiration_date,
							cll.cll_id, 
							cll.cll_status, 
							cll.cll_employee_id, 
							cll.cll_date
						FROM 
							idk_kandidat_jezici kj 
						JOIN 
							idk_candidate_verified_languages cvl 
						ON 
							kj.kj_id = cvl.cvl_id
						JOIN 
							idk_candidate_language_logs cll 
						ON 
							cvl.cvl_id = cll.cll_cvl_id
						WHERE 
							kj.kj_kandidatid = :kj_kandidatid
						ORDER BY 
							cll.cll_date, kj.kj_slusanje, cvl.cvl_id, cll.cll_id 
						ASC 
					");
					$select_query_jezici->execute(array(
						':kj_kandidatid' => $kandidat_id
					));
				} else {
					$select_query_jezici = $db->prepare("
						SELECT kj_id,kj_kandidatid,kj_slusanje,kj_ustanova,cll_status,cvl_status,cll_date,cvl_exam_date,cvl_course1_started,cvl_course1_ended,cvl_course2_started,cvl_course2_ended,cvl_certficate_creation_date,cvl_certificate_expiration_date,cll_id,cll_employee_id,
						CASE WHEN cvl_certificate_path is null then null
						else cvl_certificate_path
						end as cvl_certificate_path
						FROM  idk_candidate_verified_languages
						join idk_kandidat_jezici on kj_id=cvl_id 
						left join idk_candidate_language_logs on cvl_id=cll_cvl_id
						WHERE cvl_id = :cvl_id
					");
					$select_query_jezici->execute(array(
						':cvl_id' => $cvl_id
					));
				}
				
				$select_rows = $select_query_jezici->fetchAll();			
				if(isset($select_rows[0]['cvl_status']))
				{
					$cvl_status = $select_rows[0]['cvl_status'];
				}else{
					$cvl_status=1;
				}
				if(isset($select_rows[0]['cll_status'])){
					$cll_status=$select_rows[0]['cll_status'];
				}else{
					$cll_status=0;
				}
				
				$language_details_query = $db->prepare("SELECT kj_ustanova FROM idk_kandidat_jezici WHERE kj_id = :kj_id");
				$language_details_query->execute(array(':kj_id' => $cvl_id));
				$select_language = $language_details_query->fetch();
				$kj_ustanova = $select_language['kj_ustanova'];
				
		?>
				<script>$('.selectpicker').selectpicker({});</script>
				<form id="change_language_form" action="<?php getSiteURL(); ?>do.php?form=provjeri_jezik_kandidata" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
					<!-- Expander hitorija statusa START -->
						<div class="panel-group material-accordion material-accordion_success" id="historijaStatusa" style="<?php  if($cll_status>=1 and $cll_status<=13){ echo 'display: block;'; $odaberi_ustanovu="none";}else{ echo 'display: none;'; $odaberi_ustanovu="block";} ?>">
							<input type="hidden" value="<?php echo $cvl_id; ?>" name="cvl_id">
							<input type="hidden" value="<?php echo $kandidat_id; ?>" name="kandidat_id">
							<div class="panel panel-default material-accordion__panel "  >
								<div class="panel-heading material-accordion__heading">
									<h4 class="panel-title">
										<a class="material-accordion__title" data-toggle="collapse" data-parent="#historijaStatusa"  href="#histStat">Historija statusa </a>
									</h4>
								</div>
								<div id="histStat" class="panel-collapse collapse material-accordion__collapse">
									<div class="panel-body">
										<div class="row">
											<div class="col-sm-12">
												<table class="table table-hover">
													<thead>
														<tr>
															<th class="text-center">Nivo</th>
															<th class="text-center">Status</th>
															<th class="text-center">Datum prelaska na status</th>
															<th class="text-center">Ustanova</th>
															<th class="text-center">Korisnik</th>
														</tr>
													</thead>
													<?php 
														foreach($select_rows as $select_row) {

															$kj_id = $select_row['kj_id'];
															$kj_kandidatid = $select_row['kj_kandidatid'];
															$kj_slusanje_ispis = $select_row['kj_slusanje'];
															$kj_ustanova_id = $select_row["kj_ustanova"]; 
															switch($kj_ustanova_id) {
																case 0:
																	$kj_ustanova_ispis = "Samostalno";
																break;
																case 1: 
																	$kj_ustanova_ispis = "Glossa";
																break;
																case 2: 
																	$kj_ustanova_ispis = "Lingoda";
																break;
																case 3: 
																	$kj_ustanova_ispis = "CPE";
																break;
																case 4: 
																	$kj_ustanova_ispis = "ÖSD";
																break;
																default:
																	$kj_ustanova_ispis = "";
																break; 
															}
															$status = $select_row['cll_status'];
															$cvl_status = $select_row['cvl_status'];
															$cll_date = $select_row['cll_date'];
															$datum_ispita=$select_row['cvl_exam_date'];
															$datum_k_certifikata=$select_row['cvl_certficate_creation_date'];
															$datum_i_certifikata=$select_row['cvl_certificate_expiration_date'];
															$cvl_certificate_path=$select_row['cvl_certificate_path'];
															$cvl_course1_started=$select_row['cvl_course1_started'];
															$cvl_course1_ended = $select_row['cvl_course1_ended'];
															$cvl_course2_started=$select_row['cvl_course2_started'];
															$cvl_course2_ended = $select_row['cvl_course2_ended'];
															$cll_employee_id = $select_row['cll_employee_id'];															

															if($select_row['cll_employee_id']==0){
																$cll_employee_name="Cron";
															}else{
																$cll_employee_name= getEmployeeFullnameById($cll_employee_id);
															}
													?>
													<tbody>
														<td class="text-center"><?php echo $kj_slusanje_ispis; ?></td>
														<td class="text-center">   
														<?php 
															if($status==1) {
																if($kj_ustanova_id==0){
																	echo ' Samoprocjena';
																}else if($kj_ustanova_id==1){
																	echo ' Procjena Glose';
																}else if($kj_ustanova_id==2){
																	echo ' Procjena Lingoda';
																}else if($kj_ustanova_id==3){
																	echo ' Procjena CPE';
																}else if($kj_ustanova_id==4){
																	echo ' Procjena OSD';
																}
															}
															else if($status==2) {echo 'Samostalno uči';}
															else if($status==3) {echo 'Pohađa kurs podnivo 1';}
															else if($status==4) {echo 'Pohađa kurs podnivo 2';}
															else if($status==5) {echo 'Čeka datum polaganja';}
															else if($status==6) {echo 'Čeka polaganje(ima termin)';}
															else if($status==7) {echo 'Čeka se rezultat';}
															else if($status==8) {echo 'Ima certfikat';}
															else if($status==9) {echo 'Certifikat istekao';}
															else if($status==10) {echo 'Napreduje na veći nivo';}
															else if($status==11) {echo 'Nije položio';}
															else if($status==12) {echo 'Odustao';}
															else if($status==13) {echo 'Arhiva';}
															else {echo $status;}	
														?>
														</td>
														<td class="text-center"><?php echo $cll_date; ?></td>
														<td class="text-center"><?php echo $kj_ustanova_ispis; ?></td>
														<td class="text-center"><?php echo $cll_employee_name; ?></td>
													</tbody>		
													<?php
														}
													?>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					<!-- Expander hitorija statusa END -->
					<!-- Odaberi ustanovu START -->
						<div class="form-group izbor_ustanove" style="display: <?php echo $odaberi_ustanovu; ?>;">
							<label for="izbor_ustanove" class="col-sm-4 control-label">Odaberi način učenja:</label>
							<input type="hidden" name="trenutna_ustanova" id="trenutna_ustanova" value="<?php echo $kj_ustanova; ?>">
							<div class="col-sm-8">
								<select class="selectpicker" id="kj_ustanova" name="kj_ustanova">
									<option <?php if($kj_ustanova==0){echo 'selected';} ?> value="0">Samostalno</option>
									<option <?php if($kj_ustanova==1){echo 'selected';} ?> value="1">Glosa</option>
									<option <?php if($kj_ustanova==2){echo 'selected';} ?> value="2">Lingoda</option>
									<option <?php if($kj_ustanova==3){echo 'selected';} ?> value="3">CPE</option>
									<option <?php if($kj_ustanova==4){echo 'selected';} ?> value="4">OSD</option>
								</select>
							</div>
						</div>						
						<script>
							$(document).ready(function () {
								if($('#kj_ustanova').val()==0){
									$('.statusi_samostalno').css('display', 'block');
									$('.statusi_glosa').css('display', 'none');
								}else if($('#kj_ustanova').val()==1){
									$('.statusi_glosa').css('display', 'block');
									$('.statusi_samostalno').css('display', 'none');
								}
							})
							$("#kj_ustanova").on("change", function(){
								$('#odabir_status_jezik option[value="0"]').prop("selected", "selected").change();
								if($('#kj_ustanova').val()==0){
									$('#samoprocjena').prop("selected", "selected").change();
									$('.statusi_samostalno').css('display', 'block');
									$('.statusi_glosa').css('display', 'none');
									
								}else if($('#kj_ustanova').val()!=0){
									$('#procjena_glose').prop("selected", "selected").change();
									$('.statusi_glosa').css('display', 'block');
									$('.statusi_samostalno').css('display', 'none');
									
								}
							});
						</script>
					<!-- Odaberi ustanovu END -->
					<!-- Stausi jezika START -->
						<div class="statusi_jezika"> 	
							<!-- Lista statusa START -->
								<div class="form-group lista_statusa">
									<label for="odabir_status_jezik" class="col-sm-4 control-label">Trenutni status:</label>
									<div class="col-sm-8">
										<select class="selectpicker" id="odabir_status_jezik" name="odabir_status_jezik" <?php  if($cvl_status == 9 OR $cvl_status == 10 OR $cvl_status == 13){ echo 'disabled'; } ?>>
											<option value="0" disabled></option>
											<?php 
											if($kj_slusanje!="Bez znanja"){
											?>
												<!-- Statusi samostalno START -->
													<option value="1" class="statusi_samostalno" id="samoprocjena" <?php if($cvl_status == "1" and $kj_ustanova==0){echo "selected";} ?> >Samoprocjena</option>
													<option value="2" class="statusi_samostalno" <?php if($cvl_status == "2"){echo "selected";} ?> >Samostalno uči</option>
												<!-- Statusi samostalno END -->
												<!-- Statusi glosa START -->
													<option value="1" class="statusi_glosa" id="procjena_glose" <?php if($cvl_status == "1" and $kj_ustanova!=0){echo "selected";} ?> >Procjena Ustanove</option>
													<option value="3" class="statusi_glosa" <?php if($cvl_status == "3"){echo "selected";} ?> >Pohađa kurs podnivo 1</option>
													<option value="4" class="statusi_glosa" <?php if($cvl_status == "4"){echo "selected";} ?> >Pohađa kurs podnivo 2</option>
												<!-- Statusi glosa END -->
												<!-- Statusi zajednički START -->
													<option value="5" <?php if($cvl_status == "5"){echo "selected";} ?> >Čeka datum polaganja</option>
													<option value="6" <?php if($cvl_status == "6"){echo "selected";} ?> >Čeka polaganje(ima termin)</option>
													<option value="7" <?php if($cvl_status == "7"){echo "selected";} ?> >Čeka rezultat</option>
													<option value="8" <?php if($cvl_status == "8"){echo "selected";} ?> >Ima certfikat</option>
													<option value="9" <?php if($cvl_status == "9"){echo "selected";} ?> >Certifikat istekao</option>
													<option value="10" <?php if($cvl_status == "10"){echo "selected";} ?> >Napreduje na veći nivo</option>
													<option value="11" <?php if($cvl_status == "11"){echo "selected";} ?> >Nije položio</option>
													<option value="12" <?php if($cvl_status == "12"){echo "selected";} ?> >Odustao</option>
													<option value="13" <?php if($cvl_status == "13"){echo "selected";} ?> >Arhiva</option>
												<!-- Statusi zajednički END -->
											<?php 
											}else{
											?>
												<option value="1" <?php if($cvl_status == "1"){echo "selected";} ?> >Samoprocjena</option>
											<?php
											}
											?>
			
											
										</select>
									</div>
								</div>
								<script>
									$(document).ready(function () {
										if($('#odabir_status_jezik').val()==6 || $('#odabir_status_jezik').val()==7)
										{
											$('.ispit_datum').css('display', 'block');
											$( "#ispit_datum" ).prop( "required", true );
											$("#naziv_dokument_jezik_new").prop("required", false);
										}

										if($('#odabir_status_jezik').val()==8)
										{
											$('.upload_certifikata').css('display', 'block');
											$("#naziv_dokument_jezik_new").prop("required", true);
											//$('#razlozi_termin_select').attr('required', 'true');
										}

										if($('#odabir_status_jezik').val()==3)
										{
											$('.unos_datum_podnivo_1').css('display', 'block');
											$("#naziv_dokument_jezik_new").prop("required", false);
										}
										
										if($('#odabir_status_jezik').val()==4)
										{
											$('.unos_datum_podnivo_2').css('display', 'block');
											$("#naziv_dokument_jezik_new").prop("required", false);
										}
										
									});
									$('#odabir_status_jezik').on('change', function() {
										if($('#odabir_status_jezik').val()==6 || $('#odabir_status_jezik').val()==7)
										{
											$('.ispit_datum').css('display', 'block');
											$( "#ispit_datum" ).prop( "required", true );
										}else if($('#odabir_status_jezik').val()!=6 || $('#odabir_status_jezik').val()!=7){
											$('.ispit_datum').css('display', 'none');
											$( "#ispit_datum" ).prop( "required", false );
											$('#ispit_datum').flatpickr().clear();
										}

										if($('#odabir_status_jezik').val()==8)
										{
											$('.upload_certifikata').css('display', 'block');	
											$("#naziv_dokument_jezik_new").prop("required", true);
										}else if($('#odabir_status_jezik').val()!=8)
										{
											$('.upload_certifikata').css('display', 'none');	
											$("#naziv_dokument_jezik_new").prop("required", false);
										}

										if($('#odabir_status_jezik').val()==3)
										{
											$('.unos_datum_podnivo_1').css('display', 'block');
										}
										if($('#odabir_status_jezik').val()!=3)
										{
											$('.unos_datum_podnivo_1').css('display', 'none');
											
										}

										if($('#odabir_status_jezik').val()==4)
										{
											$('.unos_datum_podnivo_2').css('display', 'block');
											
											
										}
										if($('#odabir_status_jezik').val()!=4)
										{
											$('.unos_datum_podnivo_2').css('display', 'none');
											
										}

										if($('#odabir_status_jezik').val()==10)
										{
											$('.unos_nivoa_jezika').css('display', 'block');
										}
										if($('#odabir_status_jezik').val()!=10)
										{
											$('.unos_nivoa_jezika').css('display', 'none');
										}
									});
								</script>
							<!-- Lista statusa START -->
							<!-- Podnivo 1 START -->
								<!-- Datum pocetka podnivo 1 START -->	
									<div class="form-group col-sm-12 text-right unos_datum_podnivo_1" style="display: none;">
										<label for="unos_datum_podnivo_1" class="col-sm-4 control-label">Datum početka kursa:</label>
										<div class="col-sm-8">
											<div class="materail-input-block materail-input-block_success">
												<input type="hidden" name="">
												<input class="form-control materail-input" type="text"	<?php if(isset($cvl_course1_started)){ echo 'value="'.$cvl_course1_started.'"'; }?>  name="datum_pocetka_podnivo_1" autocomplete="off" id="datum_pocetka_podnivo_1"  >
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<script>
											$(function() {
												initDateSelect();
											});

											function initDateSelect() {
												$("#datum_pocetka_podnivo_1").flatpickr({
													minDate: "2000-01-01"
												});
											}

											function handleSubmitPodnivo1(event) {
												var startDate = $("#datum_pocetka_podnivo_1").val();
												var endDate = $("#kraj_podnivo_1").val();

												var startDateObj = new Date(startDate);
												var endDateObj = new Date(endDate);

												if (endDateObj < startDateObj) {
													alert("Krajnji datum mora biti veći od početnog (Nivo 1).");
													event.preventDefault();
												}
											}
											$(document).ready(function () {
												if($('#odabir_status_jezik').val()==3)
												{
													$("form").on("submit", handleSubmitPodnivo1);
												}
											});
											$('#odabir_status_jezik').on('change', function() {
												if($('#odabir_status_jezik').val()!=3){
													$("form").off("submit", handleSubmitPodnivo1);
												}else{
													$("form").on("submit", handleSubmitPodnivo1);
												}
											});

										</script>
									</div>
								<!-- Datum pocetka podnivo 1 END -->
								<!-- Datum kraja podnivo 1 START -->
									<div class="form-group col-sm-12 text-right unos_datum_podnivo_1" style="display: none;">
										<label for="unos_datum_podnivo_1" class="col-sm-4 control-label">Datum kraja kursa:</label>
										<div class="col-sm-8">
											<div class="materail-input-block materail-input-block_success">
												<input type="hidden" name="">
												<input class="form-control materail-input" type="text"	<?php if(isset($cvl_course1_ended)){ echo 'value="'.$cvl_course1_ended.'"'; }?>  name="kraj_podnivo_1" autocomplete="off" id="kraj_podnivo_1"  >
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<script>
											$(function() {
												initDateSelect();
											});

											function initDateSelect() {
												$("#kraj_podnivo_1").flatpickr({
													minDate: "2000-01-01"
												});
											}

											function handleSubmitPodnivo2(event) {
												var startDate = $("#datum_pocetka_podnivo_2").val();
												var endDate = $("#kraj_podnivo_2").val();

												var startDateObj = new Date(startDate);
												var endDateObj = new Date(endDate);

												if (endDateObj < startDateObj) {
													alert("Krajnji datum mora biti veći od početnog (Nivo 2).");
													event.preventDefault();
												}
											}
											
											$(document).ready(function () {
												if($('#odabir_status_jezik').val()==4)
												{
													$("form").on("submit", handleSubmitPodnivo2);
												}
											});
											$('#odabir_status_jezik').on('change', function() {
												if($('#odabir_status_jezik').val()!=4){
													$("form").off("submit", handleSubmitPodnivo2);
												}else{
													$("form").on("submit", handleSubmitPodnivo2);
												}
											});
										</script>
									</div>
								<!-- Datum kraja podnivo 1 END -->
							<!-- Podnivo 1 END -->
							<!-- Podnivo 2 START -->
								<!-- Datum pocetka podnivo 2 START -->
									<div class="form-group col-sm-12 text-right unos_datum_podnivo_2" style="display: none;">
										<label for="unos_datum_podnivo_2" class="col-sm-4 control-label">Datum početka kursa:</label>
										<div class="col-sm-8">
											<div class="materail-input-block materail-input-block_success">
												<input type="hidden" name="">
												<input class="form-control materail-input" type="text" <?php if(isset($cvl_course2_started)){ echo 'value="'.$cvl_course2_started.'"'; }?>  name="datum_pocetka_podnivo_2" autocomplete="off" id="datum_pocetka_podnivo_2"  >
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<script>
											$(function() {
												initDateSelect();
											});

											function initDateSelect() {
												$("#datum_pocetka_podnivo_2").flatpickr({
													minDate: "2000-01-01"
												});
											}
											
										</script>
									</div>
								<!-- Datum pocetka podnivo 2 END -->
								<!-- Datum kraja podnivo 2 START -->
									<div class="form-group col-sm-12 text-right unos_datum_podnivo_2" style="display: none;">
										<label for="unos_datum_podnivo_2" class="col-sm-4 control-label">Datum kraja kursa:</label>
										<div class="col-sm-8">
											<div class="materail-input-block materail-input-block_success">
												<input type="hidden" name="">
												<input class="form-control materail-input" type="text" <?php if(isset($cvl_course2_ended)){ echo 'value="'.$cvl_course2_ended.'"'; }?>  name="kraj_podnivo_2" autocomplete="off" id="kraj_podnivo_2"  >
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<script>
											$(function() {
												initDateSelect();
											});
											function initDateSelect() {
												$("#kraj_podnivo_2").flatpickr({
													minDate: "2000-01-01"
												});
											}
										</script>
									</div>
								<!-- Datum kraja podnivo 2 END -->
							<!-- Podnivo 2 END -->
							<!-- Certifikat jezika START -->
								<div class="form-group col-sm-12 upload_certifikata" style="display: none;">
									<!-- Upload certifikata START -->
										<div class="fileinput <?php if(isset($cvl_certificate_path)){ echo "fileinput-exists";}else{ echo "fileinput-new";} ?> " data-provides="fileinput">
											<span class="btn btn-default btn-file">
												<span class="fileinput-new"> 
													Izaberi dokument 
												</span>
												<span class="fileinput-exists">
													Promijeni
												</span>
												<input type="hidden" <?php if(isset($cvl_certificate_path)){ echo 'value="'.$cvl_certificate_path.'"'; }?> name="certificate_path" id="certificate_path">
												<input type="file" value="" name="naziv_dokument_jezik_new" id="naziv_dokument_jezik_new" >
											</span>
										</div>	
										<br>
										<?php 
											if(isset($cvl_certificate_path) and $cvl_certificate_path!=null){
												echo '<a target="_blank" href="'.$cvl_certificate_path.'" style="padding-top: 5px; padding-left: 10px; padding-right: 10px; word-break: break-all;" class="fileinput-filename fileinput-exists">Otvori </a>';
											}else{
												echo '<span style="padding-top: 5px; padding-left: 10px; padding-right: 10px; word-break: break-all;" class="fileinput-filename"> </span>';
											} 
										?>
										<br>
										<script> 
											$(function (){
												$('#naziv_dokument_jezik_new').change(function (){
													if($('#naziv_dokument_jezik_new').val() !== ""){
														var ext = $('#naziv_dokument_jezik_new').val().split('.').pop().toLowerCase();
														if($.inArray(ext, ['jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png']) == -1) {
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
									<!-- Upload certifikata END -->
									<!-- Datum izdavanja certifikata START -->
										<div class="form-group">
											<div class="col-sm-12 text-right datum_k_certifikata" style="display: block;">
											<label for="datum_k_certifikata" class="col-sm-4 control-label">Datum izdavanja certifikata: </label>
											<div class="col-sm-8">
												<div class="materail-input-block materail-input-block_success">
													<input type="hidden" name="">
													<input class="form-control materail-input" type="text" <?php if(isset($datum_k_certifikata)){ echo 'value="'.$datum_k_certifikata.'"'; }?>name="datum_k_certifikata" autocomplete="off" id="datum_k_certifikata"  >
													<span class="materail-input-block__line"></span>
												</div>
											</div>
											<script>
												$(function() {
													initDateSelect();
												});
												function initDateSelect() {
													$("#datum_k_certifikata").flatpickr({
														minDate: "2000-01-01"
													});
												}
											</script>
											</div>
										</div>
									<!-- Datum izdavanja certifikata END -->
									<!-- Datum isteka certifikata START -->
										<div class="form-group">
											<div class="col-sm-12 text-right datum_i_certifikata" style="display: block;">
											<label for="datum_i_certifikata" class="col-sm-4 control-label">Datum isteka certifikata: </label>
											<div class="col-sm-8">
												<div class="materail-input-block materail-input-block_success">
													<input type="hidden" name="">
													<input class="form-control materail-input" type="text"  <?php if(isset($datum_i_certifikata)){ echo 'value="'.$datum_i_certifikata.'"'; }?> name="datum_i_certifikata" autocomplete="off" id="datum_i_certifikata"  >
													<span class="materail-input-block__line"></span>
												</div>
											</div>
											<script>
												$(function() {
													initDateSelect();
												});
												function initDateSelect() {
													$("#datum_i_certifikata").flatpickr({
														minDate: "2000-01-01"
													});
												}
											</script>
											</div>
										</div>
									<!-- Datum isteka certifikata END -->
								</div>
							<!-- Certifikat jezika END -->
							<!-- Datum ispita START -->
								<div class="form-group col-sm-12 text-right ispit_datum" style="display: none;">
									<label for="ispit_datum" class="col-sm-4 control-label">Datum termina: </label>
									<div class="col-sm-8">
										<div class="materail-input-block materail-input-block_success">
											<input type="hidden">
											<input class="form-control materail-input" type="text" <?php if(isset($datum_ispita)){ echo 'value="'.$datum_ispita.'"'; }?>  name="ispit_datum" autocomplete="off" id="ispit_datum" >
											<span class="materail-input-block__line"></span>
										</div>
									</div>
									<script>
										$(function() {
											initDateSelect();
										});
							
										function initDateSelect() {
											$("#ispit_datum").flatpickr({
												minDate: "2000-01-01",
												allowInput:true
											});
										}
										
									</script>
								</div>
							<!-- Datum ispita END -->
							<!-- Unos nivoa jezika -->
								<div class="unos_nivoa_jezika" style="display: none;">
									<div class="form-group">
										<label for="prelazak_na_veci_nivo" class="col-sm-4 control-label">Nivo jezika: <!-- Slušanje --> </label>
										<div class="col-sm-8">
											<select class="selectpicker" id="prelazak_na_veci_nivo" name="prelazak_na_veci_nivo">
											<option value=""></option>
											<?php
												$languageLevels = ['A1', 'A2', 'B1', 'B2', 'C1', 'C2'];

												$activeLevel = getActiveLanguage($kandidat_id);
												foreach ($languageLevels as $level) {
													$disabled = ($level <= $activeLevel) ? 'disabled' : '';
													echo "<option value=\"$level\" $disabled>$level</option>";
												}
											?>
											</select>
										</div>
									</div>
									<div class="form-group col-sm-12 text-right prelazak_na_veci_nivo_datum_pocetka">
										<label for="prelazak_na_veci_nivo_datum_pocetka" class="col-sm-4 control-label">Datum početka kursa:</label>
										<div class="col-sm-8">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="prelazak_na_veci_nivo_datum_pocetka" autocomplete="off" id="prelazak_na_veci_nivo_datum_pocetka"  >
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<script>
											$(function() {
												initDateSelect();
											});

											function initDateSelect() {
												$("#prelazak_na_veci_nivo_datum_pocetka").flatpickr({
													minDate: "2000-01-01"
												});
											}
											
										</script>
									</div>
									<div class="form-group col-sm-12 text-right prelazak_na_veci_nivo_datum_kraja">
										<label for="prelazak_na_veci_nivo_datum_kraja" class="col-sm-4 control-label">Datum kraja kursa:</label>
										<div class="col-sm-8">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="prelazak_na_veci_nivo_datum_kraja" autocomplete="off" id="prelazak_na_veci_nivo_datum_kraja"  >
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<script>
											$(function() {
												initDateSelect();
											});

											function initDateSelect() {
												$("#prelazak_na_veci_nivo_datum_kraja").flatpickr({
													minDate: "2000-01-01"
												});
											}

											function handleSubmitPrelazakNaVeciNivo(event) {
												var startDate = $("#prelazak_na_veci_nivo_datum_pocetka").val();
												var endDate = $("#prelazak_na_veci_nivo_datum_kraja").val();

												var startDateObj = new Date(startDate);
												var endDateObj = new Date(endDate);

												if (endDateObj < startDateObj) {
													alert("Krajnji datum mora biti veći od početnog (Prelazak na veci nivo).");
													event.preventDefault();
												}
											}
											
											$(document).ready(function () {
												if($('#odabir_status_jezik').val()==10)
												{
													$("form").on("submit", handleSubmitPrelazakNaVeciNivo);
												}
											});
											$('#odabir_status_jezik').on('change', function() {
												if($('#odabir_status_jezik').val()!=10){
													$("form").off("submit", handleSubmitPrelazakNaVeciNivo);
												}else{
													$("form").on("submit", handleSubmitPrelazakNaVeciNivo);
												}
											});
											
										</script>
									</div>
								</div>
							<!-- Unos nivoa jezika END -->     
						</div>
					<!-- Stausi jezika END -->
					<!-- Button spremi START -->
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10 text-right">
								<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" <?php if($cvl_status == 9 OR $cvl_status == 10 OR $cvl_status == 13){ echo 'style="display: none;"'; } ?>>
									<i class="fa fa-plus" aria-hidden="true"></i> <span>Spremi</span>
								</button>
							</div>
						</div>
					<!-- Button spremi END -->
				</form>			

		<?php
				break;	

				case "edit_kandidat_vjestine":
				
				$id = $_REQUEST['id'];
				$kandidat_id = $_REQUEST['kandidat_id'];
				
				$select_query = $db->prepare("
									SELECT kv_id, kv_naziv, kv_grupa, kv_opis
									FROM idk_kandidat_vjestine
									WHERE kv_id = :kv_id");

				$select_query->execute(array(
								':kv_id' => $id));

				$select_row = $select_query->fetch();

					$kv_id = $select_row['kv_id'];
					$kv_naziv = $select_row['kv_naziv'];
					$kv_grupa = $select_row['kv_grupa'];
					if($kv_grupa == 1){
						$kv_grupa_naziv = "Osnovne vještine";
					}elseif($kv_grupa == 2){
						$kv_grupa_naziv = "Digitalne kompetencije";
					}elseif($kv_grupa == 3){
						$kv_grupa_naziv = "Dodatne informacije";
					}else{};
					$kv_opis = $select_row['kv_opis'];	

		?>
		<script>$('.selectpicker').selectpicker({});</script>
		<form action="<?php getSiteURL(); ?>do.php?form=edit_kandidat_vjestine" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
		<input type="hidden" name="kv_kandidat_id" value="<?php echo $kandidat_id; ?>">
		<input type="hidden" name="kv_id" value="<?php echo $kv_id; ?>">
		<div class="form-group">
			<label for="kv_grupa" class="col-sm-3 control-label"><span class="text-danger">*</span> Tip vještine:</label>
			<div class="col-sm-9">
                <select class="selectpicker" id="kv_grupa" name="kv_grupa">
                    <option value=""></option>
    				<option value="1" <?php if($kv_grupa == 1){echo "selected";}else{} ?>>Osnovne vještine</option>
    				<option value="2" <?php if($kv_grupa == 2){echo "selected";}else{} ?>>Digitalne kompetencije</option>
    				<option value="3" <?php if($kv_grupa == 3){echo "selected";}else{} ?>>Dodatne informacije</option>
    			</select>
			</div>
		</div>
		<div class="form-group">
			<label for="kv_naziv" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv: <i class="fa fa-info-circle fa-lg idk_margin_left10" data-toggle="tooltip" data-placement="right" title="etc. rad na microsoft alatima, poznavanje rada na računaru ..." aria-hidden="true"></i></label>
			<div class="col-sm-9">
				<div class="materail-input-block materail-input-block_success">
					<input class="form-control materail-input" type="text" name="kv_naziv" id="kv_naziv" value="<?php echo $kv_naziv; ?>" >
					<span class="materail-input-block__line"></span>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label for="kv_opis" class="col-sm-3 control-label"><span class="text-danger"></span> Opis:</label>
			<div class="col-sm-9">
				<div class="materail-input-block materail-input-block_success">
					<textarea class="form-control materail-input material-textarea" name="kv_opis" id="kv_opis"><?php echo $kv_opis; ?></textarea>
					<span class="materail-input-block__line"></span>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10 text-right">
				<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span>Uredi</span></button>
			</div>
		</div>
	</form>
		
		<?php
				break;	

				case "edit_kandidat":
				
				$id = $_REQUEST['id'];
				$kandidat_id = $_REQUEST['kandidat_id'];
				
					$query = $db->prepare("
									SELECT kandidat_ime, kandidat_prezime, kandidat_spol, kandidat_djevojackoprezime, kandidat_jmbg, kandidat_datumrodjenja, kandidat_mjestorodjenja, kandidat_drzavarodjenja, kandidat_drzavljanstvo, kandidat_bracnostanje, kandidat_adresa, kandidat_grad, kandidat_pbroj, kandidat_drzava, kandidat_email, kandidat_slika, kandidat_datetime,kandidat_check, kandidat_vozacka_dozvola, kandidat_vozacka_kategorija, kandidat_prijava_na, datum_termina, datum_aplikacije, kandidat_procjenatermina, kandidat_viza, kandidat_viza_vrijedi_do, kandidat_drzavljanstvo_vrsta, kandidat_datum_ugovora, kandidat_datum_pocetakrada, kandidat_broj_pasosa
									FROM idk_kandidati
									WHERE kandidat_id = :kandidat_id");

					$query->execute(array(
								':kandidat_id' => $kandidat_id));

					$row = $query->fetch();

						$kandidat_ime = $row['kandidat_ime'];
						$kandidat_prezime = $row['kandidat_prezime'];
						$kandidat_spol = $row['kandidat_spol'];
						$kandidat_check = $row['kandidat_check'];
						$kandidat_djevojackoprezime = $row['kandidat_djevojackoprezime'];
						$kandidat_jmbg = $row['kandidat_jmbg'];
						$kandidat_mjestorodjenja = $row['kandidat_mjestorodjenja'];
						$kandidat_drzavarodjenja = $row['kandidat_drzavarodjenja'];
						$kandidat_drzavljanstvo = $row['kandidat_drzavljanstvo'];
						$kandidat_drzavljanstvo_vrsta = $row['kandidat_drzavljanstvo_vrsta'];
						$kandidat_bracnostanje = $row['kandidat_bracnostanje'];
						$kandidat_adresa = $row['kandidat_adresa'];
						$kandidat_prijava_na = $row['kandidat_prijava_na'];
						$kandidat_grad = $row['kandidat_grad'];
						$kandidat_pbroj = $row['kandidat_pbroj'];
						$kandidat_drzava = $row['kandidat_drzava'];
						$kandidat_email = $row['kandidat_email'];
						$kandidat_drzava = $row['kandidat_drzava'];
						$kandidat_vozacka_dozvola = $row['kandidat_vozacka_dozvola'];
						$kandidat_vozacka_kategorija = $row['kandidat_vozacka_kategorija'];
						$kandidat_procjenatermina = $row['kandidat_procjenatermina'];
						$kandidat_viza = $row['kandidat_viza'];
						//$kandidat_viza_vrijedi_do = $row['kandidat_viza_vrijedi_do'];
						$kandidat_aplikacija_termin = $row['datum_aplikacije'];
						$kandidat_datum_termina = $row['datum_termina'];
						$kandidat_broj_pasosa = $row['kandidat_broj_pasosa'];
						
						$kategorija_vozackeExp = array();
						if($kandidat_vozacka_kategorija == null){
							if($kandidat_vozacka_dozvola == "Da"){
								$kategorija_vozackeExp = explode(",", "B");
							}else{
								$kategorija_vozackeExp = explode(",", "Nema");
							}
						}else{
							$kategorija_vozackeExp = explode(",", $kandidat_vozacka_kategorija);
						}
						
						//DA LI JE DOBIJEN TERMIN
						if($kandidat_datum_termina != NULL && $kandidat_procjenatermina != 1)
							$termin_da_ne = 1;
						else
							$termin_da_ne = 0;
						
						if($row['datum_aplikacije'] != NULL){
							$kandidat_aplikacija_termin = date('d.m.Y', strtotime($row['datum_aplikacije']));
							$apliciranje_da_ne = 1;
						}else{
							$kandidat_aplikacija_termin = NULL;
							$apliciranje_da_ne = 0;
						}
						
						if($row['datum_termina'] != NULL){
							$kandidat_datum_termina = date('d.m.Y', strtotime($row['datum_termina']));
						}else{
							$kandidat_datum_termina = NULL;
						}
						
						if($row['kandidat_viza_vrijedi_do'] != NULL){
							$kandidat_viza_vrijedi_do = date('d.m.Y', strtotime($row['kandidat_viza_vrijedi_do']));
						}else{
							$kandidat_viza_vrijedi_do = NULL;
						}
						
						// Datum potpisa ugovora
						if($row['kandidat_datum_ugovora'] != NULL){
							$kandidat_datum_ugovora = date('d.m.Y', strtotime($row['kandidat_datum_ugovora']));						
						}else{
							$kandidat_datum_ugovora = NULL;						
						}
						
						// Datum pocetka rada
						if($row['kandidat_datum_pocetakrada'] != NULL){
							$kandidat_datum_pocetakrada = date('d.m.Y', strtotime($row['kandidat_datum_pocetakrada']));						
						}else{
							$kandidat_datum_pocetakrada = NULL;						
						}	
						
						$kandidat_datumrodjenja = date('d.m.Y.', strtotime($row['kandidat_datumrodjenja']));
						$kandidat_datetime = date('d.m.Y H:i', strtotime($row['kandidat_datetime']));


						if($row['kandidat_slika'] == "none"){
							$kandidat_slika = "none.jpg";
						}else{
							$kandidat_slika = $row['kandidat_slika'];
						}

		?>
		<script>$('.selectpicker').selectpicker({});</script>
								<form action="<?php getSiteURL(); ?>do.php?form=edit_kandidat" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
								<input type="hidden" name="kandidat_id" value="<?php echo $kandidat_id; ?>">
									<div class="form-group">
										<label for="kandidat_prijava_na" class="col-sm-3 control-label"><span class="text-danger">*</span> Prijavljuje na:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kandidat_prijava_na" id="kandidat_prijava_na" value="<?php echo $kandidat_prijava_na; ?>" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>								
									<div class="form-group">
										<label for="kandidat_ime" class="col-sm-3 control-label"><span class="text-danger">*</span> Ime:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kandidat_ime" id="kandidat_ime" value="<?php echo $kandidat_ime; ?>" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kandidat_prezime" class="col-sm-3 control-label"><span class="text-danger">*</span> Prezime:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kandidat_prezime" id="kandidat_prezime" value="<?php echo $kandidat_prezime; ?>" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kandidat_mjestorodjenja" class="col-sm-3 control-label">Broj pasoša:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kandidat_broj_pasosa" id="kandidat_broj_pasosa" value="<?php echo $kandidat_broj_pasosa; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kandidat_spol" class="col-sm-3 control-label"> Spol:</label>
										<div class="col-sm-9">
                                            <select class="selectpicker" id="kandidat_spol" name="kandidat_spol">
                                                <option value=""></option>
    											<option value="Muško" <?php if($kandidat_spol == "Muško"){echo "selected";} ?>>Muško</option>
    											<option value="Žensko" <?php if($kandidat_spol == "Žensko"){echo "selected";} ?>>Žensko</option>
    										</select>
										</div>
									</div>
									<div id="kandidat_djevojackoprezime" class="form-group hidden">
										<label for="kandidat_djevojackoprezime" class="col-sm-3 control-label"> Djevojačko prezime:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kandidat_djevojackoprezime" id="kandidat_djevojackoprezime" value="<?php echo $kandidat_djevojackoprezime; ?>" />
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
                                    <script>
										<?php if($kandidat_spol == "Žensko"){ ?>
											$('#kandidat_djevojackoprezime').removeClass('hidden');
										<?php } ?>
                                        $('#kandidat_spol').on('change', function() {
                                            if (this.value == 'Žensko') $('#kandidat_djevojackoprezime').removeClass('hidden');
                                            if (this.value == 'Muško') $('#kandidat_djevojackoprezime').addClass('hidden');
                                            if (this.value == '') $('#kandidat_djevojackoprezime').addClass('hidden');
                                        });
                                    </script>
									<div class="form-group">
										<label for="kandidat_datumrodjenja" class="col-sm-3 control-label"><span class="text-danger">*</span> Datum rođenja:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kandidat_datumrodjenja" id="kandidat_datumrodjenja1" value="<?php echo $kandidat_datumrodjenja; ?>" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>										
										<script>
										$( function() {
											$( "#kandidat_datumrodjenja1" ).datepicker({
											changeMonth: true,
											changeYear: true,
												dateFormat: 'dd.mm.yy',
												yearRange: '1940:<?php echo date('Y'); ?>'
											});
										} );
											$("#kandidat_datumrodjenja1").focus(function () {
												$(".ui-datepicker-calendar").addClass('display_block');
												$(".ui-datepicker-calendar").show();   
											});
										</script>	
									</div>
									<div class="form-group">
										<label for="kandidat_mjestorodjenja" class="col-sm-3 control-label">Mjesto rođenja:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kandidat_mjestorodjenja" id="kandidat_mjestorodjenja" value="<?php echo $kandidat_mjestorodjenja; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kandidat_drzavarodjenja" class="col-sm-3 control-label">Država rođenja:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kandidat_drzavarodjenja" id="kandidat_drzavarodjenja" value="<?php echo $kandidat_drzavarodjenja; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kandidat_drzavljanstvo_vrsta" class="col-sm-3 control-label">Državljanstvo:</label>
										<div class="col-sm-9">
                                            <select class="selectpicker" id="kandidat_drzavljanstvo_vrsta" name="kandidat_drzavljanstvo_vrsta">
                                                <option value=""></option>
    											<option value="EU državljanin" <?php if($kandidat_drzavljanstvo_vrsta == "EU državljanin"){echo "selected";} ?>>EU državljanin</option>
    											<option value="NON-EU državljanin" <?php if($kandidat_drzavljanstvo_vrsta == "NON-EU državljanin"){echo "selected";} ?>>NON-EU državljanin</option>
    										</select>
										</div>
									</div>									
									<div class="form-group">
										<label for="kandidat_drzavljanstvo" class="col-sm-3 control-label"> Državljanstvo naziv:</label>
										<div class="col-sm-9">
											<!-- <div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kandidat_drzavljanstvo" id="kandidat_drzavljanstvo" value="<?php echo $kandidat_drzavljanstvo; ?>" >
												<span class="materail-input-block__line"></span>
											</div> -->
											<select class="selectpicker" id="kandidat_drzavljanstvo" name="kandidat_drzavljanstvo">
                                                <option value="">Odaberi</option>
    											<option value="Bosanskohercegovačko" <?php if($kandidat_drzavljanstvo == "Bosanskohercegovačko"){echo "selected";} ?>>Bosanskohercegovačko</option>
    											<option value="Srpsko" <?php if($kandidat_drzavljanstvo == "Srpsko"){echo "selected";} ?>>Srpsko</option>
    											<option value="Hrvatsko" <?php if($kandidat_drzavljanstvo == "Hrvatsko"){echo "selected";} ?>>Hrvatsko</option>
    											<option value="Njemačko" <?php if($kandidat_drzavljanstvo == "Njemačko"){echo "selected";} ?>>Njemačko</option>
    											<option value="Makedonsko" <?php if($kandidat_drzavljanstvo == "Makedonsko"){echo "selected";} ?>>Makedonsko</option>
    											<option value="Crnogorsko" <?php if($kandidat_drzavljanstvo == "Crnogorsko"){echo "selected";} ?>>Crnogorsko</option>
    										</select>
										</div>
									</div>
                                    <script>
                                        $('#kandidat_bracnostanje').on('change', function() {
                                            if (this.value == '') $('#kandidat_bracnostanjeod1').addClass('hidden');
                                            if (this.value == 'Neoženjen / Neudana') $('#kandidat_bracnostanjeod1').addClass('hidden');
                                            if (this.value == 'Oženjen / Udana') $('#kandidat_bracnostanjeod1').removeClass('hidden');
                                            if (this.value == 'Razveden / Razvedena') $('#kandidat_bracnostanjeod1').removeClass('hidden');
                                            if (this.value == 'Udovac / Udovica') $('#kandidat_bracnostanjeod1').removeClass('hidden');
                                        });
                                    </script>

									<div class="form-group">
										<label for="kandidat_adresa" class="col-sm-3 control-label"> Adresa:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kandidat_adresa" id="kandidat_adresa" value="<?php echo $kandidat_adresa; ?>" >
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
                                    <div class="form-group">
										<label for="kandidat_grad" class="col-sm-3 control-label"> Grad:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kandidat_grad" id="kandidat_grad" value="<?php echo $kandidat_grad; ?>" >
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
                                    <div class="form-group">
										<label for="kandidat_pbroj" class="col-sm-3 control-label"> Poštanski broj:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="number" name="kandidat_pbroj" id="kandidat_pbroj" value="<?php echo $kandidat_pbroj; ?>" >
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
                                    <div class="form-group">
										<label for="kandidat_drzava" class="col-sm-3 control-label"> Država:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kandidat_drzava" id="kandidat_drzava" value="<?php echo $kandidat_drzava; ?>" >
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
                                    <div class="form-group">
										<label for="kandidat_email" class="col-sm-3 control-label">Email:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="email" name="kandidat_email" id="kandidat_email" value="<?php echo $kandidat_email; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group kandidat_kategorija_vozacke_sh">
										<div class="col-sm-3">
										</div>
										<div class="col-sm-9">
											<div class = "kandidat_kategorija_vozacke_alert alert alert-danger" style = "margin-bottom: 5px;" role="alert">
												
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="kandidat_kategorija_vozacke" class="col-sm-3 control-label">Kategorija vozacke:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="kandidat_kategorija_vozacke" name="kandidat_kategorija_vozacke[]" data-actions-box="true" data-live-search="true" multiple>
												<option value="Nema" <?php if(in_array("Nema", $kategorija_vozackeExp)) echo "selected";?>>Nema</option>
												<option value="B" <?php if(in_array("B", $kategorija_vozackeExp)) echo "selected";?>>B</option>
												<option value="C1" <?php if(in_array("C1", $kategorija_vozackeExp)) echo "selected";?>>C1</option>
												<option value="C" <?php if(in_array("C", $kategorija_vozackeExp)) echo "selected";?>>C</option>
												<option value="BE" <?php if(in_array("BE", $kategorija_vozackeExp)) echo "selected";?>>BE</option>
												<option value="C1E" <?php if(in_array("C1E", $kategorija_vozackeExp)) echo "selected";?>>C1E</option>
												<option value="CE" <?php if(in_array("CE", $kategorija_vozackeExp)) echo "selected";?>>CE</option>
											</select>
										</div>
									</div>
									<div class="form-group kandidat_kategorija_vozacke_sh">
										<div class="col-sm-3">
										</div>
										<div class="col-sm-9">
											<div class = "kandidat_kategorija_vozacke_alert alert alert-danger" style = "margin-bottom: 5px;" role="alert">
												
											</div>
										</div>
									</div>
									<script>
										$(document).ready(function(){
											$('.kandidat_kategorija_vozacke_sh').hide();
										});
										$("#kandidat_kategorija_vozacke").change(function(){
											if (jQuery.inArray('Nema', $('#kandidat_kategorija_vozacke').val()) != -1){
												if( $('#kandidat_kategorija_vozacke').val().length > 1 ){
													
													$('#kandidat_kategorija_vozacke').val('Nema');
													$('#kandidat_kategorija_vozacke').selectpicker('refresh');
													$(".kandidat_kategorija_vozacke_alert").html('Morate prvo deselektirati opciju "Nema"!');
													$('.kandidat_kategorija_vozacke_sh').show();
													setTimeout(function(){
														$('.kandidat_kategorija_vozacke_sh').hide();
													}, 5000);
												}
											}
										});
										
										</script>
									
									<hr/>
									<!-- PITANJA ZA VIZU -->
									<!-- VIZA -->
									<div class="form-group" id="viza_viza">
										<label for="kandidat_viza" class="col-sm-3 control-label">Da li kandidat ima vizu?</label>
										<div class="col-sm-1">
											<label class="main-container__column material-radio-group material-radio-group_success" for="viza_da">
												<input type="radio" name="kandidat_viza" id="viza_da" class="material-radiobox" value="1" <?php if($kandidat_viza == 1){echo "checked";}?> />
												<span class="material-radio-group__element material-radio-group__check-radio"></span>
												<span class="material-radio-group__element material-radio-group__caption">Da</span>
											</label>
										</div>
										<div class="col-sm-1">
											<label class="main-container__column material-radio-group material-radio-group_danger" for="viza_ne">
												<input type="radio" name="kandidat_viza" id="viza_ne" class="material-radiobox" value="0" <?php if($kandidat_viza == 0){echo "checked";}?> />
												<span class="material-radio-group__element material-radio-group__check-radio"></span>
												<span class="material-radio-group__element material-radio-group__caption">Ne</span>
											</label>
										</div>
									</div>
									<div class="form-group" id="viza_datum_vrijedi_do">
										<label for="kandidat_viza_vrijedi_do" class="col-sm-3 control-label">Kad ističe viza?</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kandidat_viza_vrijedi_do" id="kandidat_viza_vrijedi_do" <?php echo 'value="'.$kandidat_viza_vrijedi_do	.'"'; ?> placeholder="Datum isteka vize" autocomplete="off">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<script>
										<?php $year_sixteen = date('Y') - 16; ?>
										$( function() {
											$( "#kandidat_viza_vrijedi_do" ).datepicker({
												changeMonth: true,
												changeYear: true,
												dateFormat: 'dd.mm.yy' ,
												yearRange: '2020:2050'
											});
										} );
										$("#kandidat_viza_vrijedi_do").focus(function () {
											$(".ui-datepicker-calendar").addClass('display_block');
											$(".ui-datepicker-calendar").show();   
										});
										</script>
									</div>
									
									<!-- TERMIN -->
									<div class="form-group" id="viza_termin">
										<label for="kandidat_email" class="col-sm-3 control-label">Da li kandidat ima termin za vizu?</label>
										<div class="col-sm-1">
											<label class="main-container__column material-radio-group material-radio-group_success" for="termin_daa">
												<input type="radio" name="kandidat_termin" id="termin_daa" class="material-radiobox" value="1" <?php if($termin_da_ne == 1){echo "checked";}else{} ?> />
												<span class="material-radio-group__element material-radio-group__check-radio"></span>
												<span class="material-radio-group__element material-radio-group__caption">Da</span>
											</label>
										</div>
										<div class="col-sm-1">
											<label class="main-container__column material-radio-group material-radio-group_danger" for="termin_nee">
												<input type="radio" name="kandidat_termin" id="termin_nee" class="material-radiobox" value="0" <?php if($termin_da_ne == 0){echo "checked";}else{} ?> />
												<span class="material-radio-group__element material-radio-group__check-radio"></span>
												<span class="material-radio-group__element material-radio-group__caption">Ne</span>
											</label>
										</div>
									</div>
									
									<div class="form-group" id="viza_datum_termina">
										<label for="kandidat_termin_date" class="col-sm-3 control-label">Datum termina:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kandidat_termin_date" id="kandidat_termin_date" <?php if($termin_da_ne == 1){ echo 'value="'.$kandidat_datum_termina.'"';}?> placeholder="Datum termina" autocomplete="off">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<script>
										<?php $year_sixteen = date('Y') - 16; ?>
										$( function() {
											$( "#kandidat_termin_date" ).datepicker({
												changeMonth: true,
												changeYear: true,
												dateFormat: 'dd.mm.yy' ,
												yearRange: '2018:2025'
											});
										} );
										$("#kandidat_termin_date").focus(function () {
											$(".ui-datepicker-calendar").addClass('display_block');
											$(".ui-datepicker-calendar").show();   
										});
										</script>
									</div>
									
									<!-- APLICIRANJE -->
									<div class="form-group" id="viza_apliciranje">
										<label for="kandidat_apliciranje" class="col-sm-3 control-label">Da li je kandidat aplicirao za termin za vizu?</label>
										<div class="col-sm-1">
											<label class="main-container__column material-radio-group material-radio-group_success" for="apliciranje_da">
												<input type="radio" name="kandidat_apliciranje" id="apliciranje_da" class="material-radiobox" value="1" <?php if($apliciranje_da_ne == 1){echo "checked"; }?> />
												<span class="material-radio-group__element material-radio-group__check-radio"></span>
												<span class="material-radio-group__element material-radio-group__caption">Da</span>
											</label>
										</div>
										<div class="col-sm-1">
											<label class="main-container__column material-radio-group material-radio-group_danger" for="apliciranje_ne">
												<input type="radio" name="kandidat_apliciranje" id="apliciranje_ne" class="material-radiobox" value="0" <?php if($apliciranje_da_ne == 0){echo "checked"; }?> />
												<span class="material-radio-group__element material-radio-group__check-radio"></span>
												<span class="material-radio-group__element material-radio-group__caption">Ne</span>
											</label>
										</div>
									</div>
									
									<div class="form-group" id="viza_datum_apliciranja">
										<label for="kandidat_termin_date_app" class="col-sm-3 control-label">Datum aplikacije</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="kandidat_termin_date_app" id="kandidat_termin_date_app" <?php if($apliciranje_da_ne == 1){ echo 'value="'.$kandidat_aplikacija_termin.'"';}?> placeholder="Datum aplikacije" autocomplete="off" >
												<span class="materail-input-block__line"></span>
											</div>
										</div>
										<script>
										<?php $year_sixteen = date('Y') - 16; ?>
										$( function() {
											$( "#kandidat_termin_date_app" ).datepicker({
												changeMonth: true,
												changeYear: true,
												dateFormat: 'dd.mm.yy' ,
												yearRange: '2017:2020'
											});
										} );
										$("#kandidat_termin_date_app").focus(function () {
											$(".ui-datepicker-calendar").addClass('display_block');
											$(".ui-datepicker-calendar").show();   
										});
										</script>
									</div>
									
									<script>
										$(document).ready(function() {
											
											$('#kandidat_drzavljanstvo_vrsta').on('change', function() {
												drz_vrsta = $(this).val();
												if(drz_vrsta == "EU državljanin"){
													$('#viza_viza').hide();
													$('#viza_datum_vrijedi_do').hide();
													$('#viza_termin').hide();
													$('#viza_datum_termina').hide();
													$('#viza_apliciranje').hide();
													$('#viza_datum_apliciranja').hide();
													$("#viza_da").prop("checked", false);
													$("#viza_ne").prop("checked", false);
													$("#termin_daa").prop("checked", false);
													$("#termin_nee").prop("checked", false);
													$("#apliciranje_da").prop("checked", false);
													$("#apliciranje_ne").prop("checked", false);
												}else{
													$('#viza_viza').show();
												}
											});
											
											drz_vrsta = $( "#kandidat_drzavljanstvo_vrsta" ).val();
											if(drz_vrsta == "EU državljanin"){
												$('#viza_viza').hide();
												$('#viza_datum_vrijedi_do').hide();
												$('#viza_termin').hide();
												$('#viza_datum_termina').hide();
												$('#viza_apliciranje').hide();
												$('#viza_datum_apliciranja').hide();
											}else{
												viza_da_ne = $('input[name="kandidat_viza"]:checked').val();
												termin_da_ne = $('input[name="kandidat_termin"]:checked').val();
												apliciranje_da_ne = $('input[name="kandidat_apliciranje"]:checked').val();
												
												if(viza_da_ne == 1){
													$('#viza_viza').show();
													$('#viza_datum_vrijedi_do').show();
													$('#viza_termin').hide();
													$('#viza_datum_termina').hide();
													$('#viza_apliciranje').hide();
													$('#viza_datum_apliciranja').hide();
												}else{
													if(termin_da_ne == 1){
														$('#viza_datum_vrijedi_do').hide();
														$('#viza_termin').show();
														$('#viza_datum_termina').show();
														$('#viza_apliciranje').hide();
														$('#viza_datum_apliciranja').hide();
													}else{
														$('#viza_datum_vrijedi_do').hide();
														$('#viza_datum_termina').hide();
														$('#viza_apliciranje').show();
														if(apliciranje_da_ne == 1)
															$('#viza_datum_apliciranja').show();
														else
															$('#viza_datum_apliciranja').hide();
													}
												}
											}
											
											$('#viza_da').click(function() {
												if($('#viza_da').is(':checked')) { 
													$('#viza_datum_vrijedi_do').show();
													$('#viza_termin').hide();
													$('#viza_datum_termina').hide();
													$('#viza_apliciranje').hide();
													$('#viza_datum_apliciranja').hide();
													$("#termin_daa").prop("checked", false);
													$("#termin_nee").prop("checked", false);
													$("#apliciranje_da").prop("checked", false);
													$("#apliciranje_ne").prop("checked", false);
												}
											});
											$('#viza_ne').click(function() {
												if($('#viza_ne').is(':checked')) { 
													$('#viza_datum_vrijedi_do').hide();
													$('#viza_termin').show();
													$("#termin_daa").prop("checked", false);
													$("#termin_nee").prop("checked", false);
													$("#apliciranje_da").prop("checked", false);
													$("#apliciranje_ne").prop("checked", false);
												}
											});
											$('#termin_daa').click(function() {
												if($('#termin_daa').is(':checked')) { 
													$('#viza_datum_termina').show();
													$('#viza_apliciranje').hide();
													$('#viza_datum_apliciranja').hide();
													$("#apliciranje_da").prop("checked", false);
													$("#apliciranje_ne").prop("checked", false);
												}
											});
											$('#termin_nee').click(function() {
												if($('#termin_nee').is(':checked')) { 
													$('#viza_datum_termina').hide();
													$('#viza_apliciranje').show();
													$("#apliciranje_da").prop("checked", false);
													$("#apliciranje_ne").prop("checked", false);
												}
											});
											
											$('#apliciranje_da').click(function() {
												if($('#apliciranje_da').is(':checked')) { 
													$('#viza_datum_apliciranja').show();
												}
											});
											$('#apliciranje_ne').click(function() {
												if($('#apliciranje_ne').is(':checked')) { 
													$('#viza_datum_apliciranja').hide();
												}
											});
											
										});
									</script>
									
									<hr/>
									<div class="form-group">
										<label for="kandidat_datum_ugovora" class="col-sm-3 control-label">Datum potpisa ugovora:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input type="hidden" name="kandidat_datum_ugovora_stari" value="<?php echo $kandidat_datum_ugovora; ?>">
												<input class="form-control materail-input" type="text" name="kandidat_datum_ugovora" autocomplete="off" id="kandidat_datum_ugovora" value="<?php echo $kandidat_datum_ugovora; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>	
									<div class="form-group">
										<label for="kandidat_datum_pocetakrada" class="col-sm-3 control-label">Datum početka rada:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input type="hidden" name="kandidat_datum_pocetakrada_stari" value="<?php echo $kandidat_datum_pocetakrada; ?>">
												<input class="form-control materail-input" type="text" name="kandidat_datum_pocetakrada" autocomplete="off" id="kandidat_datum_pocetakrada" value="<?php echo $kandidat_datum_pocetakrada; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>	
									<script>
										$( function() {
											$( "#kandidat_datum_ugovora" ).datepicker({
											changeMonth: true,
											changeYear: true,
												dateFormat: 'dd.mm.yy',
												yearRange: '2018:2030'
											});
										});
										$("#kandidat_datum_ugovora").focus(function () {
											$(".ui-datepicker-calendar").addClass('display_block');
											$(".ui-datepicker-calendar").show();   
										});
										
										$( function() {
											$( "#kandidat_datum_pocetakrada" ).datepicker({
											changeMonth: true,
											changeYear: true,
												dateFormat: 'dd.mm.yy',
												yearRange: '2018:2030'
											});
										});
										$("#kandidat_datum_pocetakrada").focus(function () {
											$(".ui-datepicker-calendar").addClass('display_block');
											$(".ui-datepicker-calendar").show();   
										});
										
										$( document ).ready(function() {
											$("#zamjena_kandidat_form").hide();
										});
										
										//PRIKAŽI BIRANJE NALOGA AKO JE POČETNA VRIJEDNOST DATUMA POTPISA UGOVORA POSTAVLJENA
										$( function() {
											if($("#kandidat_datum_ugovora" ).val() != ""){
												$("#projekt_picker").show();
												$('#nalog_id').attr('required', 'required');
											}
											else{
												$("#projekt_picker").hide();
												$("#zamjena_check").hide();
												$("#zamjena_kandidat_form").hide();
												$('#nalog_id').removeAttr('required');
											}
										});
										
										//PRIKAŽI BIRANJE NALOGA I PROVJERU ZAMJENE AKO SE PROMIJENI VRIJEDNOST DATUMA POTPISA UGOVORA
										$("#kandidat_datum_ugovora").change(function(){
											$("#projekt_picker").show();
											$("#zamjena_check").show();
											$('#nalog_id').attr('required', 'required');
										});
									</script>
									<?php 
										if(!is_null($kandidat_datum_ugovora)){
											$stari = 1;
											
											$kf_id 				= "";
											$nalog_id_baza 		= "";
											$projekt_id 		= "";
											$kf_datum 			= "";
											$kf_datum_stvarni 	= "";
											$kf_type 			= "";
											$kf_placeno 		= "";
											$kf_status 			= "";
											$kf_zamjena_id 		= "";
											
											$query_kandidat_financije = $db->prepare("
																SELECT kf_id, nalog_id, projekt_id, kf_datum, kf_datum_stvarni, kf_type, kf_placeno, kf_status, kf_zamjena_id
																FROM idk_kandidat_financije
																WHERE kandidat_id = :kandidat_id AND kf_type = 1 AND kf_status != 3
																");
			
											$query_kandidat_financije->execute(array(
															':kandidat_id' => $kandidat_id
															));
			
											$row_kandidat_financije = $query_kandidat_financije->fetch();
											
											$kf_id = $row_kandidat_financije['kf_id'];
											$nalog_id_baza = $row_kandidat_financije['nalog_id'];
											$projekt_id = $row_kandidat_financije['projekt_id'];
											$kf_datum = $row_kandidat_financije['kf_datum'];
											$kf_datum_stvarni = $row_kandidat_financije['kf_datum_stvarni'];
											$kf_type = $row_kandidat_financije['kf_type'];
											$kf_placeno = $row_kandidat_financije['kf_placeno'];
											$kf_status = $row_kandidat_financije['kf_status'];
											$kf_zamjena_id = $row_kandidat_financije['kf_zamjena_id'];
										}
										
										if(!is_null($kandidat_datum_pocetakrada)){
											$query_kandidat_financije = $db->prepare("
																SELECT kf_id
																FROM idk_kandidat_financije
																WHERE kandidat_id = :kandidat_id AND kf_type = 2 AND kf_status != 3
																");
			
											$query_kandidat_financije->execute(array(
															':kandidat_id' => $kandidat_id
															));
			
											$row_kandidat_financije = $query_kandidat_financije->fetch();
											
											$kf_id_pocetak_rada = $row_kandidat_financije['kf_id'];
										}
									?>
									<div class="form-group" id="projekt_picker">
										<label for="nalog_id" class="col-sm-3 control-label"><span class="text-danger">*</span> Nalog:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="nalog_id" name="nalog_id">
												<option <?php if($nalog_id_baza != "") echo "selected";?>></option>
												<?php
													//NAĐI PROJEKTE U KOJIMA JE KANDIDAT
													$query_project = $db->prepare("
														SELECT pk_projectid
														FROM idk_project_kandidati
														WHERE pk_kandidatid = :pk_kandidatid");
																	
													$query_project->execute(array(
																':pk_kandidatid' => $kandidat_id));
																
													while($row = $query_project->fetch()){
												
														$pk_projectid = $row['pk_projectid'];
														
													//NAĐI NALOG_ID ZA PROJEKTE U KOJIMA JE KANDIDAT
														$query_project_nalog = $db->prepare("
															SELECT project_nalogid
															FROM idk_projects
															WHERE project_id = :project_id");
																		
														$query_project_nalog->execute(array(
																	':project_id' => $pk_projectid));
																	
														$row_projekt = $query_project_nalog->fetch();
													
														$nalog_id = $row_projekt['project_nalogid'];
														
													//NAĐI NALOG BROJ I NAZIV ZA NALOG KOJI SADRŽAVA PROJEKT U KOJEMU JE KANDIDAT
														$query_nalog = $db->prepare("
															SELECT nalog_broj, nalog_naziv
															FROM idk_nalozi
															WHERE nalog_id = :nalog_id");
														
														$query_nalog->execute(array(
																	':nalog_id' => $nalog_id));
																	
														$row_nalog = $query_nalog->fetch();
					
														$nalog_broj = $row_nalog['nalog_broj'];
														$nalog_naziv = $row_nalog['nalog_naziv'];
												?>
												<option value="<?php echo $nalog_id;?>" <?php if($nalog_id_baza == $nalog_id) echo "selected";?>> <?php echo $nalog_broj." ".$nalog_naziv;?> </option>
												<?php } ?>
											</select>
											<input type="hidden" name="projekt_id" value="<?php echo $pk_projectid; ?>">
											<input type="hidden" name="kf_id" value="<?php echo $kf_id; ?>">
											<input type="hidden" name="kf_id_pocetak_rada" value="<?php echo $kf_id_pocetak_rada; ?>">
										</div>
									</div>
									<div class="form-group" id="zamjena_check">
										<label for="zamjena_check" class="col-sm-3 control-label"><span class="text-danger">*</span> Da li se ovaj kandidat mijenja?</label>
										<div class="col-sm-1">
											<label class="main-container__column material-radio-group material-radio-group_success" for="zamjena_check_da">
												<input type="radio" name="zamjena_check" id="zamjena_check_da" class="material-radiobox" value="Da"<?php if($kf_zamjena_id !="") echo "checked";?> />
												<span class="material-radio-group__element material-radio-group__check-radio"></span>
												<span class="material-radio-group__element material-radio-group__caption">Da</span>
											</label>
										</div>
										<div class="col-sm-1">
											<label class="main-container__column material-radio-group material-radio-group_danger" for="zamjena_check_ne">
												<input type="radio" name="zamjena_check" id="zamjena_check_ne" class="material-radiobox" value="Ne" <?php if($kf_zamjena_id =="") echo "checked";?> />
												<span class="material-radio-group__element material-radio-group__check-radio"></span>
												<span class="material-radio-group__element material-radio-group__caption">Ne</span>
											</label>
										</div>
									</div>
									<input type="hidden" id="kf_zamjena_id" value="<?php echo $kf_zamjena_id ?? ""; ?>">
									<script>
										
									
										$("#zamjena_check_da").click(function () {
											$("#zamjena_kandidat_form").show();
											var nalog_id = document.getElementById("nalog_id").value;
											var kf_zamjena_id = document.getElementById("kf_zamjena_id").value;
											$.ajax({
												url: 'ajax_data.php?page=get_Kandidat_Zamjena',
												type: 'POST',    
												data: {'nalog_id':nalog_id, 'kf_zamjena_id':kf_zamjena_id},
												dataType: 'html',
												success: function(data) {
													console.log(data);
													$("#zamjena_kandidat").html(data).selectpicker("refresh");
												}
											});
										});
										
										$("#zamjena_check_ne").click(function () {
											$("#zamjena_kandidat_form").hide();
										});
									
									</script>
									<div class="form-group" id="zamjena_kandidat_form">
										<label for="zamjena_kandidat" class="col-sm-3 control-label"><span class="text-danger">*</span> Kandidat koji ga mijenja:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="zamjena_kandidat" name="zamjena_kandidat">
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="kandidat_slika" class="col-sm-3 control-label">Fotografija:</label>
										<input type="hidden" name="kandidat_slika_current" value="<?php echo $kandidat_slika; ?>">
										<div class="col-sm-9">
											<div class="fileinput fileinput-new" data-provides="fileinput">
												<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;"><img src="<?php getSiteURL(); ?>files/kandidati/<?php echo $kandidat_slika; ?>"></div>
												<div>
													<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografiju</span><span class="fileinput-exists">Promijeni</span><input type="file" name="kandidat_slika" id="kandidat_slika"></span>
													<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
													<script>
														$(function (){
															$('#kandidat_slika').change(function (){

																var f = this.files[0];

																if (f.size > 20388608 || f.fileSize > 20388608){
																	$('#idk_alert_size').removeClass('hidden');
																	this.value = null;
																}else{
																	$('#idk_alert_size').addClass('hidden');
																}

																var ext = $('#kandidat_slika').val().split('.').pop().toLowerCase();

																if($.inArray(ext, ['jpg', 'jpeg', 'png']) == -1) {
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
											<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-arrow-right" aria-hidden="true"></i> <span>Uredi</span></button>
											<br /><small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
										</div>
									</div>
								</form>
		
		<?php
				break;		

				case "candidate_status":	
					error_reporting(0);
				
					$kandidat_id = $_REQUEST['id'];
					$validacija_prosla = getSveValidacije($kandidat_id);
				
					$query = $db->prepare("
									SELECT kandidat_status, kandidat_status_messenger
									FROM idk_kandidati
									WHERE kandidat_id = :kandidat_id");

					$query->execute(array(
								':kandidat_id' => $kandidat_id));

					$row = $query->fetch();

						$kandidat_status = $row['kandidat_status'];
						$kandidat_status_messenger = $row['kandidat_status_messenger'];
						
						if($kandidat_status_messenger == 1){
							$tipkaiskljucena1 = "disabled";
							$tipkaiskljucena2 = "disabled";
							$tipkaiskljucena5 = "disabled";
							$tipkaiskljucena3 = "disabled";
							$tipkaiskljucena4 = "disabled";
							$tipkaiskljucena6 = "disabled";
						}elseif($kandidat_status_messenger == 2){
							if($kandidat_status == 4){
								$tipkaiskljucena1 = "disabled";
								$tipkaiskljucena2 = "disabled";
								$tipkaiskljucena5 = "disabled";
								$tipkaiskljucena6 = "disabled";
								if($validacija_prosla == 1)
									$tipkaiskljucena3 = " ";
								else
									$tipkaiskljucena3 = "disabled";
								$tipkaiskljucena4 = " ";
							}else{
								$tipkaiskljucena1 = "disabled";
								$tipkaiskljucena2 = "disabled";
								$tipkaiskljucena5 = "disabled";
								$tipkaiskljucena3 = "disabled";
								$tipkaiskljucena4 = "disabled";
								$tipkaiskljucena6 = "disabled";
							}
						}elseif($kandidat_status_messenger == 5){
							$tipkaiskljucena1 = "disabled";
							$tipkaiskljucena2 = "";
							$tipkaiskljucena5 = "disabled";
							$tipkaiskljucena3 = "";
							$tipkaiskljucena4 = "";
							$tipkaiskljucena6 = "disabled";
						}else{
							$tipkaiskljucena1 = " ";
							$tipkaiskljucena2 = " ";
							$tipkaiskljucena5 = " ";
							$tipkaiskljucena3 = " ";
							$tipkaiskljucena4 = " ";
							$tipkaiskljucena6 = " ";
						}
						$buttontxt1 ="";
						$buttontxt2 ="";
						$buttontxt3 ="";
						$buttontxt4 ="";
						$buttontxt5 ="";
						$buttontxt6 ="";
						$buttontxt7 ="";
						if($kandidat_status == 0){
							$buttontxt1 = "material-btn_warning";
						}elseif($kandidat_status == 1){
							$buttontxt2 = "material-btn_primary";
						}elseif($kandidat_status == 2){
							$buttontxt3 = "material-btn_success";
						}elseif($kandidat_status == 3){
							$buttontxt4 = "material-btn_danger";
						}elseif($kandidat_status == 4){
							$buttontxt5 = "material-btn_info";
						}elseif($kandidat_status == 5){
							$buttontxt6 = "material-btn_primary";
						}elseif($kandidat_status == 7){
							$buttontxt7 = "material-btn_yellow";
						}elseif($kandidat_status == 8){
							$buttontxt7 = "material-btn_yellow";
						}
						
						if($kandidat_status_messenger == 0){
							$style_bot = "black;";
						}elseif($kandidat_status_messenger == 1){
							$style_bot = "grey;";
						}elseif($kandidat_status_messenger == 2){
							$style_bot = "green;";
						}elseif($kandidat_status_messenger == 3){
							$style_bot = "red;";
						}elseif($kandidat_status_messenger == 5){
							$style_bot = "orange;";
						}elseif($kandidat_status_messenger == 6){
							$style_bot = "orange;";
						}
		?>
			<div class="row">
				<div class="col-xs-12 text-right">
					<div class="btn-group main-container__column" role="group" aria-label="Basic example">
						<?php if($kandidat_status_messenger == 1) { ?>
							<i class="fa fa-paper-plane" style="color: <?php echo $style_bot?> font-size: 1.5em; margin-right: 10px;" aria-hidden="true" title = "Poslan SMS za instalaciju Messengera - Na čekanju!"></i>
						<?php } elseif($kandidat_status_messenger == 2) { ?>
							<i class="fa fa-commenting" style="color: <?php echo $style_bot?> font-size: 1.5em; margin-right: 10px;" aria-hidden="true" title = "Kandidat se logirao na aplikaciju Messeneger."></i>
						<?php } elseif($kandidat_status_messenger == 3) { ?>
							<i class="fa fa-commenting" style="color: <?php echo $style_bot?> font-size: 1.5em; margin-right: 10px;" aria-hidden="true" title = "Kandidat je odbio instalirati aplikaciju Messeneger - Ručna obrada!"></i>
						<?php } elseif($kandidat_status_messenger == 5) { ?>
							<i class="fa fa-commenting" style="color: <?php echo $style_bot?> font-size: 1.5em; margin-right: 10px;" aria-hidden="true" title = "Kandidat nije završio ispunjavanje podataka preko messengera - Ručna obrada!"></i>
						<?php } elseif($kandidat_status_messenger == 6) { ?>
							<i class="fa fa-commenting" style="color: <?php echo $style_bot?> font-size: 1.5em; margin-right: 10px;" aria-hidden="true" title = "Kandidat nije završio ispunjavanje podataka preko messengera - Ručna obrada!"></i>
						<?php } else { ?>
							<i class="fa fa-user" style="color: <?php echo $style_bot?> font-size: 1.5em; margin-right: 10px;" aria-hidden="true" title = "Ručna obrada!"></i>
						<?php } ?>
					</div>
					<div class="btn-group main-container__column" role="group" aria-label="Basic example">
						<?php if($kandidat_status_messenger != 5 AND $kandidat_status_messenger != 6){?>
						<button type="button" class="btn material-btn change_status_candidate <?php echo $buttontxt1; ?>" data-id="<?php echo $kandidat_id ?>" data-status="0" data-status_mes="<?php echo $kandidat_status_messenger; ?>" <?php echo $tipkaiskljucena1 ?>><i class="fa fa-spinner" aria-hidden="true"></i> Na provjeri</button>
						<?php }else{ ?>
						<button type="button" class="btn material-btn change_status_candidate <?php echo $buttontxt7; ?>" data-id="<?php echo $kandidat_id ?>" data-status="7" data-status_mes="<?php echo $kandidat_status_messenger; ?>" <?php echo "disabled" ?>><i class="fa fa-balance-scale" aria-hidden="true"></i> Nezavršen messenger</button>
						<?php } ?>
						
						<?php if($kandidat_status != 5) { ?>
						<button type="button" class="btn material-btn change_status_candidate <?php echo $buttontxt2; ?>" data-id="<?php echo $kandidat_id ?>" data-status="1" data-status_mes="<?php echo $kandidat_status_messenger; ?>" <?php echo $tipkaiskljucena2 ?>><i class="fa fa-spinner" aria-hidden="true"></i> U obradi</button>
						<?php }else{ ?>
							
						<button type="button" class="btn material-btn change_status_candidate <?php echo $buttontxt6; ?>" data-id="<?php echo $kandidat_id ?>" data-status="5" data-status_mes="<?php echo $kandidat_status_messenger; ?>" <?php echo $tipkaiskljucena6 ?>><i class="fa fa-spinner" aria-hidden="true"></i> Dopuna</button>
						
						<?php } ?>
						<?php if($kandidat_status_messenger == 1 OR $kandidat_status_messenger == 2){?>
						<button type="button" class="btn material-btn change_status_candidate <?php echo $buttontxt5; ?>" data-id="<?php echo $kandidat_id ?>" data-status="4" data-status_mes="<?php echo $kandidat_status_messenger; ?>" <?php echo $tipkaiskljucena5 ?>><i class="fa fa-balance-scale" aria-hidden="true"></i> Kontrola</button>
						<?php } ?>
						<button type="button" class="btn material-btn change_status_candidate <?php echo $buttontxt3; ?>" data-id="<?php echo $kandidat_id ?>" data-status="2" data-status_mes="<?php echo $kandidat_status_messenger; ?>" <?php echo $tipkaiskljucena3 ?>><i class="fa fa-check" aria-hidden="true"></i> Obrađen</button>
						<button type="button" class="btn material-btn change_status_candidate <?php echo $buttontxt4; ?>" data-id="<?php echo $kandidat_id ?>" data-status="3" data-status_mes="<?php echo $kandidat_status_messenger; ?>" <?php /*echo $tipkaiskljucena4*/ ?>><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviran</button>
					</div>
				</div>
			</div>
			<br/>
			<script>
			$(document).ready(function() {	
				$( ".change_status_candidate" ).click(function() {
			
					var id = $(this).data('id'); 
					var status_k = $(this).data('status'); 
					var status_mes = $(this).data('status_mes'); 
					
					$('#getCandidateStatus').html('');
					$('#loader').show();    
					
					$.ajax({
						url: 'ajax.php?page=candidate_status_edit',
						type: 'POST',
						data: {'id': id, 'status_k': status_k, 'status_mes': status_mes},
						dataType: 'html',
					})
					.done(function(data){
						var id = $('#getCandidateStatus').data('id');
						
						$('#getCandidateStatus').html('');
						$('#loader').show();    
						
						$.ajax({
							url: 'ajax.php?page=candidate_status',
							type: 'POST',
							data: {'id': id},
							dataType: 'html',
						})
						.done(function(data){
							//console.log(data); 
							$('#getCandidateStatus').html(''); 
							$('#getCandidateStatus').html(data);
							$('#loader').hide();
						})		
					})
					.fail(function(){
						$('#getCandidateStatus').html('<i class="glyphicon glyphicon-info-sign"></i> Something went wrong, Please try again...');
						$('#loader').hide();
					});
				
				});
			});			
			</script>
		<?php

				break;
				
				case "candidate_status_edit":	

				$kandidat_id = $_REQUEST['id'];
				$status_k = $_REQUEST['status_k'];
				$status_mes = $_REQUEST['status_mes'];
				$date = date('Y-m-d H:i:s');
				
				$query = $db->prepare("
								UPDATE idk_kandidati
								SET kandidat_status = :kandidat_status
								WHERE kandidat_id = :kandidat_id");

				$query->execute(array(
							':kandidat_status' => $status_k,
							':kandidat_id' => $kandidat_id));				
			
				//INSERT INTO LOG STATUSA
				$query_log_status = $db->prepare("
						INSERT INTO idk_log_kandidat_statusi
							(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime, lks_employee_id)
						VALUES
							(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime, :lks_employee_id)
				");
				
				$query_log_status->execute(array(
						':lks_kandidat_id' => $kandidat_id,
						':lks_status_obrade' => $status_k,
						':lks_status_messenger' => $status_mes,
						':lks_datetime' => $date,
						':lks_employee_id' => $logged_employee_id
				));
				
				//PROVJERA DA LI KANDIDAT ISPUNJAVA USLOVE ZA NALOG I SMJESTANJE U ODGOVARAJUCI PROJEKAT
				if($status_k == 2){
					
					checkCandidateInputs($kandidat_id);
				}

				break;	

				case "candidate_group":	
				
				$kandidat_id = $_POST['id'];
				
				$query = $db->prepare("
								SELECT kandidat_group
								FROM idk_kandidati
								WHERE kandidat_id = :kandidat_id
								");

				$query->execute(array(
							':kandidat_id' => $kandidat_id));

				$row = $query->fetch();

				$kandidat_group = $row['kandidat_group'];
						
						
				$query_kg_name = $db->prepare("
								SELECT kg_title
								FROM idk_kandidati_grupe
								WHERE kg_id = :kg_id
								");

				$query_kg_name->execute(array(
								':kg_id' => $kandidat_group));
							
				$row_kg_name = $query_kg_name->fetch();

				$kg_selected = $row_kg_name['kg_title'];
				
		?>
			<div class="row">
				<div class="col-xs-12 text-left">
					<select class="change_group_candidate selectpicker form-control materail-input materail-input-custom" data-live-search="true" style="display:block!important;">
						<option value="<?php echo $kandidat_group; ?>"><?php echo $kg_selected; ?></option>
						<?php
							$query = $db->prepare("
											SELECT kg_id, kg_title, kg_date
											FROM idk_kandidati_grupe
											WHERE kg_status = 0
											ORDER BY kg_id ASC
											");

							$query->execute();
							$i=1;
							while($row = $query->fetch()){

								$kg_id = $row['kg_id'];
								$kg_title = $row['kg_title'];
								
								if($kg_id == $kandidat_group){
									$buttonclass = "material-btn_primary";
								}else{
									$buttonclass = "";	
								}
								if($kg_id != $kandidat_group){
						?>
						<option value="<?php echo $kg_id; ?>" data-id="<?php echo $kandidat_id ?>" data-status="<?php echo $kg_id; ?>"><?php echo $kg_title; ?></option>
							<?php } } ?>
					</select>
				</div>
			</div>
			<br/>
			<script>
			$(document).ready(function() {
				$( ".change_group_candidate" ).change(function() {
					var id = $("option:selected", this).data('id'); 
					var status_k = $("option:selected", this).data('status'); 
					
					$.ajax({
						url: 'ajax.php?page=candidate_group_edit',
						type: 'POST',
						data: {'id': id, 'status_k': status_k},
						dataType: 'html',
					})
					
				
				});
			});			
			</script>
		<?php

				break;
				
				case "candidate_group_edit":	

				$kandidat_id = $_REQUEST['id'];
				$status_k = $_REQUEST['status_k'];
				
				$query = $db->prepare("
								UPDATE idk_kandidati
								SET kandidat_group = :kandidat_group
								WHERE kandidat_id = :kandidat_id");

				$query->execute(array(
							':kandidat_group' => $status_k,
							':kandidat_id' => $kandidat_id));				
				
				

				break;		

				case "edit_projekat_kandidat":	

				$kandidat_id = $_REQUEST['kandidat_id'];
				$projekat_id = $_REQUEST['projekat_id'];
				
				if($projekat_id == "nedefinisan"){
					$delete_from_projects = $db->prepare("
												DELETE FROM idk_project_kandidati
												WHERE pk_kandidatid = :pk_kandidatid");

					$delete_from_projects->execute(array(
										':pk_kandidatid' => $kandidat_id));
				}else{
					
					//IZBRISI IZ BAZE
					$delete_from_projects = $db->prepare("
												DELETE FROM idk_project_kandidati
												WHERE pk_kandidatid = :pk_kandidatid");

					$delete_from_projects->execute(array(
										':pk_kandidatid' => $kandidat_id));
					
					// UBACI U BAZU
					$insert_into_projects = $db->prepare("
									INSERT INTO idk_project_kandidati
										(pk_projectid, pk_kandidatid)
									VALUES
										(:pk_projectid, :pk_kandidatid)");

					$insert_into_projects->execute(array(
									':pk_projectid' => $projekat_id,
									':pk_kandidatid' => $kandidat_id
									));				
				}
				

				break;
				
				case "edit_projekat_status_p":	

					$kandidat_id = $_REQUEST['kandidat_id'];
					$status_id = $_REQUEST['status_id'];
					
					if($status_id == "nedefinisan"){
						$query = $db->prepare("
										UPDATE idk_kandidati
										SET	kandidat_status_prijave = :kandidat_status_prijave
										WHERE kandidat_id = :kandidat_id");

						$query->execute(array(
									':kandidat_status_prijave' => 0,
									':kandidat_id' => $kandidat_id));
					}else{
						
						$query = $db->prepare("
										UPDATE idk_kandidati
										SET	kandidat_status_prijave = :kandidat_status_prijave
										WHERE kandidat_id = :kandidat_id");

						$query->execute(array(
									':kandidat_status_prijave' => $status_id,
									':kandidat_id' => $kandidat_id));
					}
					// POSTAVLJANJE OBAVIJESTI O POTREBNOJ ISPLATI PARTNERU, UKOLIKO JE KANDIDAT PREPORUČEN
					if($status_id == 4){
						
						// PROVJERI DA LI JE PREPORUČEN
						$read_query = $db -> prepare("
											SELECT kandidat_partnerid
											FROM idk_kandidati
											WHERE kandidat_id = :kandidat_id");

						$read_query->execute(array(
									':kandidat_id' => $kandidat_id));
						
						$row = $read_query->fetch();
						
						$partner_id = $row['kandidat_partnerid'];
						
						if($partner_id != NULL){
							
							$insert_query = $db->prepare('
											INSERT INTO idk_partner_uplate
												(jp_uplate_partnerid, jp_uplate_kandidatid, jp_uplate_status)
											VALUES
												(:jp_uplate_partnerid, :jp_uplate_kandidatid, :jp_uplate_status)
										');
							$insert_query->execute(array(
									':jp_uplate_partnerid' => $partner_id,
									':jp_uplate_kandidatid' => $kandidat_id,
									':jp_uplate_status' => 2,
									));
						}
					}
					
					//Add to LOGS
					$log_desc = "Prebacio status prijave kandidata " . $kandidat_id . " na ".$status_id. ".";
					$log_type = "0";
					addToLogs($log_desc, $log_type);

				break;
				
				case "edit_zaposlen_kod":	

					$kandidat_id = $_POST['kandidat_id'];
					$kompanija_id = $_POST['kompanija_id'];
					
					
					$query = $db->prepare("
								UPDATE idk_kandidati
								SET	kandidat_zaposlen_kod = :kandidat_zaposlen_kod
								WHERE kandidat_id = :kandidat_id");

					$query->execute(array(
								':kandidat_zaposlen_kod' => $kompanija_id,
								':kandidat_id' => $kandidat_id));
					
					$log_desc = "Svrstao kandidata " . $kandidat_id . " u zaposlene kod kompanije: " . $kompanija_id. " ." ;
					$log_type = "0";
					addToLogs($log_desc, $log_type);

				break;
				
				case "get_Position_Desc":
				
				$pozicija = $_POST['pozicija'];
				$kri_id = $_POST['kri_id'];
				if($pozicija != 0){
					$query_pozicija = $db->prepare("
									SELECT kp_id, kp_ime, kp_ime_de, kp_opis
									FROM idk_kandidat_pozicija
									WHERE kp_id = :kp_id"
									);
	
					$query_pozicija->execute(array(
									':kp_id' => $pozicija));
	
					$row = $query_pozicija->fetch();
					echo $row['kp_opis'];
					exit();
				}
				else{
					$select_query_iskustvo = $db->prepare("
						SELECT kri_opis, kri_opis_de
						FROM idk_kandidat_radno_iskustvo
						WHERE kri_id = :kri_id
						");
	
					$select_query_iskustvo->execute(array(
									':kri_id' => $kri_id));
									
					$row = $select_query_iskustvo->fetch();
					echo $row['kri_opis_de'];
					exit();
				}
				break;
				
/****** VALIDACIJA KANDIDATA SA MESSENGERA ******/
				case "validacija_viza":
					
					$kandidat_id = $_POST['kandidat_id'];
					$employee_id = $_POST['employee_id'];
					$status = $_POST['status'];
					$dio_bloka = $_POST['dio_bloka'];
					$ro_id = $_POST['ro_id'];
					$date = date('Y-m-d H:i:s');
					
					$query_lang = $db->prepare("
									SELECT jezik FROM users 
									WHERE kandidat_id = :kandidat_id 
					");
					
					$query_lang->execute(array(
									'kandidat_id' => $kandidat_id
					));
					$rowLang = $query_lang->fetch();
					$lang = $rowLang['jezik'];
					if($lang == "de")
						$text = "Korriegieren Sie Ihre Daten";
					else
						$text = "Niste unijeli dobre podatke!";
					
					$query_check = $db->prepare("
									SELECT * FROM idk_validnosti_inputa 
									WHERE vi_kandidat_id = :vi_kandidat_id 
									AND vi_razlog_id in ( 
										SELECT ro_id 
										FROM idk_razlozi_odbijanja 
										WHERE ro_dio_bloka = :ro_dio_bloka) 
					");
					
					$query_check->execute(array(
									'vi_kandidat_id' => $kandidat_id,
									'ro_dio_bloka' => $dio_bloka
					));
					
					$br = $query_check->rowCount();
					$rowVal = $query_check->fetch();
					
					if($br == 0){
						$insert_val = $db->prepare("
										INSERT INTO idk_validnosti_inputa
											(vi_razlog_id, vi_kandidat_id, vi_status, vi_employee_id, vi_vrsta_podatka, vi_datetime)
										VALUES
											(:vi_razlog_id, :vi_kandidat_id, :vi_status, :vi_employee_id, :vi_vrsta_podatka, :vi_datetime)");

						$insert_val->execute(array(
										':vi_razlog_id' => $ro_id,
										':vi_kandidat_id' => $kandidat_id,
										':vi_status' => $status,
										':vi_employee_id' => $employee_id,
										':vi_vrsta_podatka' => $dio_bloka,
										':vi_datetime' => $date
										));	
					}else{
						$vi_id = $rowVal['vi_id'];
						//echo $status;
						$update_val = $db->prepare("
										UPDATE idk_validnosti_inputa 
										SET vi_status = :vi_status, vi_razlog_id = :vi_razlog_id
										WHERE vi_id = :vi_id
										");

						$update_val->execute(array(
										':vi_status' => $status,
										':vi_razlog_id' => $ro_id,
										':vi_id' => $vi_id
										));	
					}
					//POZIV FUNKCIJA ZA PROVJERU DA LI JE ZADNJA VALIDACIJA U PITANJU
					$ukupno_za_validaciju = getBrojUnesenihZaValidaciju($kandidat_id);
					$ukupno_na_validaciji = getBrojNaValidaciji($kandidat_id);
					$ukupno_na_ponovnoj_provjeri = getBrojNaProvjeri($kandidat_id);
					
					//DA LI SU SVI PROVJERENI
					$broj_neprovjerenih = $ukupno_za_validaciju - $ukupno_na_validaciji + $ukupno_na_ponovnoj_provjeri;
					if($broj_neprovjerenih == 0){
						
						//UPDATE KANDIDAT STATUS NA 5 (DOPUNA)
						$update_status = $db->prepare("
										UPDATE idk_kandidati 
										SET kandidat_status = :kandidat_status
										WHERE kandidat_id = :kandidat_id
										");

						$update_status->execute(array(
										':kandidat_status' => 5,
										':kandidat_id' => $kandidat_id
										));	
										
						//INSERT INTO LOG STATUSA
						$query_log_status = $db->prepare("
								INSERT INTO idk_log_kandidat_statusi
									(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime, lks_employee_id)
								VALUES
									(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime, :lks_employee_id)
						");
						
						$query_log_status->execute(array(
								':lks_kandidat_id' => $kandidat_id,
								':lks_status_obrade' => 5,
								':lks_status_messenger' => 2,
								':lks_datetime' => $date,
								':lks_employee_id' => $employee_id
						));
										
						//GET TOKEN USERA
						$query_token = $db->prepare("
										SELECT token, type FROM users
										WHERE kandidat_id = :id
						");
						
						$query_token->execute(array(
										'id' => $kandidat_id
						));
						
						$rowToken = $query_token->fetch();
						$token = base64_decode($rowToken['token']);
						$type = $rowToken['type'];
						if($type == 'android')
							send_bot_notification_android($token, $text);
						else if($type == 'ios')
							send_bot_notification_ios($token, $text);
					}
					
				break;
				case "validacija_viza_da":
					
					$kandidat_id = $_POST['kandidat_id'];
					$employee_id = $_POST['employee_id'];
					$status = $_POST['status'];
					$dio_bloka = $_POST['dio_bloka'];
					$date = date('Y-m-d H:i:s');
					
					$query_lang = $db->prepare("
									SELECT jezik FROM users 
									WHERE kandidat_id = :kandidat_id 
					");
					
					$query_lang->execute(array(
									'kandidat_id' => $kandidat_id
					));
					$rowLang = $query_lang->fetch();
					$lang = $rowLang['jezik'];
					if($lang == "de")
						$text = "Korriegieren Sie Ihre Daten";
					else
						$text = "Niste unijeli dobre podatke!";
					
					$query_check = $db->prepare("
									SELECT * FROM idk_validnosti_inputa 
									WHERE vi_kandidat_id = :vi_kandidat_id 
									AND vi_razlog_id in ( 
										SELECT ro_id 
										FROM idk_razlozi_odbijanja 
										WHERE ro_dio_bloka = :ro_dio_bloka) 
					");
					
					$query_check->execute(array(
									'vi_kandidat_id' => $kandidat_id,
									'ro_dio_bloka' => $dio_bloka
					));
					
					$br = $query_check->rowCount();
					$rowVal = $query_check->fetch();
					
					if($br == 0){
						$insert_val = $db->prepare("
										INSERT INTO idk_validnosti_inputa
											(vi_kandidat_id, vi_status, vi_employee_id, vi_vrsta_podatka, vi_datetime)
										VALUES
											(:vi_kandidat_id, :vi_status, :vi_employee_id, :vi_vrsta_podatka, :vi_datetime)");

						$insert_val->execute(array(
										':vi_kandidat_id' => $kandidat_id,
										':vi_status' => $status,
										':vi_employee_id' => $employee_id,
										':vi_vrsta_podatka' => $dio_bloka,
										':vi_datetime' => $date
										));	
						
					}else{
						$vi_id = $rowVal['vi_id'];
						//echo $status;
						$update_val = $db->prepare("
										UPDATE idk_validnosti_inputa 
										SET vi_status = :vi_status
										WHERE vi_id = :vi_id
										");

						$update_val->execute(array(
										':vi_status' => $status,
										':vi_id' => $vi_id
										));	
					}
					//POZIV FUNKCIJA ZA PROVJERU DA LI JE ZADNJA VALIDACIJA U PITANJU
					$ukupno_za_validaciju = getBrojUnesenihZaValidaciju($kandidat_id);
					$ukupno_na_validaciji = getBrojNaValidaciji($kandidat_id);
					$ukupno_na_ponovnoj_provjeri = getBrojNaProvjeri($kandidat_id);
					
					//DA LI SU SVI PROVJERENI
					$broj_neprovjerenih = $ukupno_za_validaciju - $ukupno_na_validaciji + $ukupno_na_ponovnoj_provjeri;
					if($broj_neprovjerenih == 0){
						
						//PROVJERA DA LI IMA NOVIH ZA KORIGOVATI
						$query_check_val = $db->prepare("
										SELECT * FROM idk_validnosti_inputa 
										WHERE vi_kandidat_id = :vi_kandidat_id
										AND vi_status = :vi_status
										
						");
						
						$query_check_val->execute(array(
										'vi_status' => 2,
										'vi_kandidat_id' => $kandidat_id
						));
						$br_neval = $query_check_val->rowCount();
						if($br_neval > 0){
						
							//UPDATE KANDIDAT STATUS NA 5 (DOPUNA)
							$update_status = $db->prepare("
											UPDATE idk_kandidati 
											SET kandidat_status = :kandidat_status
											WHERE kandidat_id = :kandidat_id
											");

							$update_status->execute(array(
											':kandidat_status' => 5,
											':kandidat_id' => $kandidat_id
											));	
							
							//INSERT INTO LOG STATUSA
							$query_log_status = $db->prepare("
									INSERT INTO idk_log_kandidat_statusi
										(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime, lks_employee_id)
									VALUES
										(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime, :lks_employee_id)
							");
							
							$query_log_status->execute(array(
									':lks_kandidat_id' => $kandidat_id,
									':lks_status_obrade' => 5,
									':lks_status_messenger' => 2,
									':lks_datetime' => $date,
									':lks_employee_id' => $employee_id
							));
							
							//GET TOKEN USERA
							$query_token = $db->prepare("
											SELECT token, type FROM users
											WHERE kandidat_id = :id
							");
							
							$query_token->execute(array(
											'id' => $kandidat_id
							));
							
							$rowToken = $query_token->fetch();
							$token = base64_decode($rowToken['token']);
							$type = $rowToken['type'];
							if($type == 'android')
								send_bot_notification_android($token, $text);
							else if($type == 'ios')
								send_bot_notification_ios($token, $text);
						}
					}
				break;
				case "validacija_sve":
					
					$kandidat_id = $_POST['kandidat_id'];
					$employee_id = $_POST['employee_id'];
					$status = $_POST['status'];
					$ro_id = $_POST['ro_id'];
					$podatak_id = $_POST['podatak_id'];
					$vrsta_podatka = $_POST['vrsta_podatka'];
					$date = date('Y-m-d H:i:s');
					
					$query_lang = $db->prepare("
									SELECT jezik FROM users 
									WHERE kandidat_id = :kandidat_id 
					");
					
					$query_lang->execute(array(
									'kandidat_id' => $kandidat_id
					));
					$rowLang = $query_lang->fetch();
					$lang = $rowLang['jezik'];
					if($lang == "de")
						$text = "Korriegieren Sie Ihre Daten";
					else
						$text = "Niste unijeli dobre podatke!";
					
					$query_check = $db->prepare("
									SELECT * FROM idk_validnosti_inputa 
									WHERE vi_kandidat_id = :vi_kandidat_id 
									AND vi_razlog_id = :vi_razlog_id
									AND vi_podatak_id = :vi_podatak_id
					");
					
					$query_check->execute(array(
									'vi_kandidat_id' => $kandidat_id,
									'vi_razlog_id' => $ro_id,
									'vi_podatak_id' => $podatak_id
					));
					
					
					$br = $query_check->rowCount();
					$rowVal = $query_check->fetch();
					
					if($br == 0){
					
						$insert_val = $db->prepare("
										INSERT INTO idk_validnosti_inputa
											(vi_razlog_id, vi_kandidat_id, vi_status, vi_employee_id, vi_podatak_id, vi_vrsta_podatka, vi_datetime)
										VALUES
											(:vi_razlog_id, :vi_kandidat_id, :vi_status, :vi_employee_id, :vi_podatak_id, :vi_vrsta_podatka, :vi_datetime)");

						$insert_val->execute(array(
										':vi_razlog_id' => $ro_id,
										':vi_kandidat_id' => $kandidat_id,
										':vi_status' => $status,
										':vi_employee_id' => $employee_id,
										':vi_podatak_id' => $podatak_id,
										':vi_vrsta_podatka' => $vrsta_podatka,
										':vi_datetime' => $date
										));	
					}else{
						$vi_id = $rowVal['vi_id'];
						//echo $status;
						$update_val = $db->prepare("
										UPDATE idk_validnosti_inputa 
										SET vi_status = :vi_status, vi_razlog_id = :vi_razlog_id
										WHERE vi_id = :vi_id
										");

						$update_val->execute(array(
										':vi_status' => $status,
										':vi_razlog_id' => $ro_id,
										':vi_id' => $vi_id
										));	
					}
					
					//POZIV FUNKCIJA ZA PROVJERU DA LI JE ZADNJA VALIDACIJA U PITANJU
					$ukupno_za_validaciju = getBrojUnesenihZaValidaciju($kandidat_id);
					$ukupno_na_validaciji = getBrojNaValidaciji($kandidat_id);
					$ukupno_na_ponovnoj_provjeri = getBrojNaProvjeri($kandidat_id);
					
					//DA LI SU SVI PROVJERENI
					$broj_neprovjerenih = $ukupno_za_validaciju - $ukupno_na_validaciji + $ukupno_na_ponovnoj_provjeri;
					if($broj_neprovjerenih == 0){
						
						//PROVJERA DA LI IMA NOVIH ZA KORIGOVATI
						$query_check_val = $db->prepare("
										SELECT * FROM idk_validnosti_inputa 
										WHERE vi_kandidat_id = :vi_kandidat_id
										AND vi_status = :vi_status
										
						");
						
						$query_check_val->execute(array(
										'vi_status' => 2,
										'vi_kandidat_id' => $kandidat_id
						));
						$br_neval = $query_check_val->rowCount();
						if($br_neval > 0){
							
							//UPDATE KANDIDAT STATUS NA 5 (DOPUNA)
							$update_status = $db->prepare("
											UPDATE idk_kandidati 
											SET kandidat_status = :kandidat_status
											WHERE kandidat_id = :kandidat_id
											");

							$update_status->execute(array(
											':kandidat_status' => 5,
											':kandidat_id' => $kandidat_id
											));	
							
							//INSERT INTO LOG STATUSA
							$query_log_status = $db->prepare("
									INSERT INTO idk_log_kandidat_statusi
										(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime, lks_employee_id)
									VALUES
										(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime, :lks_employee_id)
							");
							
							$query_log_status->execute(array(
									':lks_kandidat_id' => $kandidat_id,
									':lks_status_obrade' => 5,
									':lks_status_messenger' => 2,
									':lks_datetime' => $date,
									':lks_employee_id' => $employee_id
							));
							
							//GET TOKEN USERA
							$query_token = $db->prepare("
											SELECT token, type FROM users
											WHERE kandidat_id = :id
							");
							
							$query_token->execute(array(
											'id' => $kandidat_id
							));
							
							$rowToken = $query_token->fetch();
							$token = base64_decode($rowToken['token']);
							$type = $rowToken['type'];
							if($type == 'android')
								send_bot_notification_android($token, $text);
							else if($type == 'ios')
								send_bot_notification_ios($token, $text);
						}
					}
					
				break;
				case "validacija_sve_da":
					
					$kandidat_id = $_POST['kandidat_id'];
					$employee_id = $_POST['employee_id'];
					$status = $_POST['status'];
					$podatak_id = $_POST['podatak_id'];
					$vrsta_podatka = $_POST['vrsta_podatka'];
					$date = date('Y-m-d H:i:s');
					
					$query_lang = $db->prepare("
									SELECT jezik FROM users 
									WHERE kandidat_id = :kandidat_id 
					");
					
					$query_lang->execute(array(
									'kandidat_id' => $kandidat_id
					));
					$rowLang = $query_lang->fetch();
					$lang = $rowLang['jezik'];
					if($lang == "de")
						$text = "Korriegieren Sie Ihre Daten";
					else
						$text = "Niste unijeli dobre podatke!";
					
					$query_check = $db->prepare("
									SELECT * FROM idk_validnosti_inputa 
									WHERE vi_podatak_id = :vi_podatak_id 
									AND vi_kandidat_id = :vi_kandidat_id
									AND vi_vrsta_podatka = :vi_vrsta_podatka
					");
					
					$query_check->execute(array(
									'vi_podatak_id' => $podatak_id,
									'vi_kandidat_id' => $kandidat_id,
									'vi_vrsta_podatka' => $vrsta_podatka
					));
					
					$br = $query_check->rowCount();
					$rowVal = $query_check->fetch();
					//echo $br;
					//echo $rowVal['vi_id'];
					if($br == 0){
						$insert_val = $db->prepare("
										INSERT INTO idk_validnosti_inputa
											(vi_kandidat_id, vi_status, vi_employee_id, vi_podatak_id, vi_vrsta_podatka, vi_datetime)
										VALUES
											(:vi_kandidat_id, :vi_status, :vi_employee_id, :vi_podatak_id, :vi_vrsta_podatka, :vi_datetime)");

						$insert_val->execute(array(
										':vi_kandidat_id' => $kandidat_id,
										':vi_status' => $status,
										':vi_employee_id' => $employee_id,
										':vi_podatak_id' => $podatak_id,
										':vi_vrsta_podatka' => $vrsta_podatka,
										':vi_datetime' => $date
										));	
						
					}else{
						//$vi_id = $rowVal['vi_id'];
						//echo $status;
						$update_val = $db->prepare("
										UPDATE idk_validnosti_inputa 
										SET vi_status = :vi_status
										WHERE vi_podatak_id = :vi_podatak_id
										");

						$update_val->execute(array(
										':vi_status' => $status,
										':vi_podatak_id' => $podatak_id
										));	
					}
					
					//POZIV FUNKCIJA ZA PROVJERU DA LI JE ZADNJA VALIDACIJA U PITANJU
					$ukupno_za_validaciju = getBrojUnesenihZaValidaciju($kandidat_id);
					$ukupno_na_validaciji = getBrojNaValidaciji($kandidat_id);
					$ukupno_na_ponovnoj_provjeri = getBrojNaProvjeri($kandidat_id);
					
					//DA LI SU SVI PROVJERENI
					$broj_neprovjerenih = $ukupno_za_validaciju - $ukupno_na_validaciji + $ukupno_na_ponovnoj_provjeri;
					if($broj_neprovjerenih == 0){
						
						//PROVJERA DA LI IMA NOVIH ZA KORIGOVATI
						$query_check_val = $db->prepare("
										SELECT * FROM idk_validnosti_inputa 
										WHERE vi_kandidat_id = :vi_kandidat_id
										AND vi_status = :vi_status
										
						");
						
						$query_check_val->execute(array(
										'vi_status' => 2,
										'vi_kandidat_id' => $kandidat_id
						));
						$br_neval = $query_check_val->rowCount();
						if($br_neval > 0){
						
							//UPDATE KANDIDAT STATUS NA 5 (DOPUNA)
							$update_status = $db->prepare("
											UPDATE idk_kandidati 
											SET kandidat_status = :kandidat_status
											WHERE kandidat_id = :kandidat_id
											");

							$update_status->execute(array(
											':kandidat_status' => 5,
											':kandidat_id' => $kandidat_id
											));	
							
							//INSERT INTO LOG STATUSA
							$query_log_status = $db->prepare("
									INSERT INTO idk_log_kandidat_statusi
										(lks_kandidat_id, lks_status_obrade, lks_status_messenger, lks_datetime, lks_employee_id)
									VALUES
										(:lks_kandidat_id, :lks_status_obrade, :lks_status_messenger, :lks_datetime, :lks_employee_id)
							");
							
							$query_log_status->execute(array(
									':lks_kandidat_id' => $kandidat_id,
									':lks_status_obrade' => 5,
									':lks_status_messenger' => 2,
									':lks_datetime' => $date,
									':lks_employee_id' => $employee_id
							));
							
							//GET TOKEN USERA
							$query_token = $db->prepare("
											SELECT token, type FROM users
											WHERE kandidat_id = :id
							");
							
							$query_token->execute(array(
											'id' => $kandidat_id
							));
							
							$rowToken = $query_token->fetch();
							$token = base64_decode($rowToken['token']);
							$type = $rowToken['type'];
							if($type == 'android')
								send_bot_notification_android($token, $text);
							else if($type == 'ios')
								send_bot_notification_ios($token, $text);
						}
					}
				break;
				
				case "select_blok":
					
					$vrsta_podatka = $_POST['vrsta_podatka'];
					$query_razlozi = $db->prepare("
									SELECT ro_id, ro_naziv, ro_dio_bloka
									FROM idk_razlozi_odbijanja
									WHERE ro_blok = '".$vrsta_podatka."'");

					$query_razlozi->execute();
						
					?> 
					<select class="selectpicker" id="ro_select_blok" name="ro_select_blok" required>
					<?php
						
						while($rowR = $query_razlozi->fetch()){
							$ro_id = $rowR['ro_id'];
							$ro_naziv = $rowR['ro_naziv'];
					?>
						<option value="<?php echo $ro_id; ?>"><?php echo $ro_naziv; ?></option>
					<?php 
						}
					?>
					</select>
					<input type="hidden" name="val_vrsta_podatka" id="val_vrsta_podatka" value="<?php echo $vrsta_podatka; ?>" />
					<?php
				break;
				
				case "update_faktor":
					$values = $_POST['values'];
					$emp_ids = $_POST['emp_ids'];
					$emp_ids_rs = $_POST['emp_ids_rs'];
					$values_rs = $_POST['values_rs'];
					foreach($emp_ids as $key => $emp_id){
						echo $key."--".$emp_id."--".$values[$key]."--".$values_rs[$key]."<br>";
						$update_val = $db->prepare("
										UPDATE idk_employees 
										SET employee_faktor_provizije_bih = :employee_faktor_provizije_bih, employee_faktor_provizije_srb= :employee_faktor_provizije_srb
										WHERE employee_id = :employee_id
										");

						$update_val->execute(array(
										':employee_faktor_provizije_bih' => $values[$key],
										':employee_faktor_provizije_srb' => $values_rs[$key],
										':employee_id' => $emp_id
										));	
					}
				break;
				
				case "provjeriDiplomu":
					
					$idCanJob  = $_POST['posao_id'];
					$getCandDetails_query=$db->prepare("SELECT kandidat_dipl_id,kandidat_nalog_id FROM idk_kandidati where kandidat_id=:kandidat_id");
					$getCandDetails_query->execute(array(
						':kandidat_id' => $idCanJob
					));
					$getCandDetails = $getCandDetails_query->fetch();
					$candDiplId 	= $getCandDetails['kandidat_dipl_id'];
					$nalog_id		= $getCandDetails['kandidat_nalog_id'];
					$otherDocuments=getNostrificationDocumentInOtherDocuments($candDiplId, $idCanJob);
					if(count($otherDocuments["count"]) !=0){
					?>
						
						<table class="table"> 
						<tbody>
							<p class="potvrdi_dokument">Potvrdi odgovarajući ili unesi novi dokument</p>
							<?php  
								foreach($otherDocuments["count"] AS $countDocument){

									$candidateIdDipl   = $otherDocuments["candidateIdDipl"][$countDocument];
									$candidateIdJob    = $otherDocuments["candidateIdJob"][$countDocument];
									$documentId        = $otherDocuments["documentId"][$countDocument];
									$documentName      = $otherDocuments["documentName"][$countDocument];
									$documentFile      = $otherDocuments["documentFile"][$countDocument];
									
									?>
									<tr>
										<td class="text-center"><?php echo $documentName; ?></td>
										<td class="text-center"><a target="_blank" href="<?php echo $documentFile; ?>">Otvori dokument</a></td>
										<td class="text-center" style="min-width: 170px;">
											<div class="row">
												<span class="col-sm-12 nesto<?php echo $documentId; ?>">
													<a class="label label-warning material-label material-label_primary main-container__column potvrdi<?php echo $documentId; ?>">Potvrdi</a> 
												</span>
												<span class="col-sm-8 full_recognition<?php echo $documentId; ?>" style="display: none;">
													<div class="">
														<select class="selectpicker" id="fullRecognition<?php echo $documentId; ?>" title = "Odaberite opciju">
															<option value = "2">Evaluacija</option>
															<option value = "1">Potpuno priznata</option>
															<option value = "0">Djelimično priznata</option>
														</select>
													</div>
												</span>
												<span class="col-sm-4 potvrdi_da<?php echo $documentId; ?>" style="display: none;">
													<button class="btn btn-success material-btn material-btn_success" id = "potvrdi_da_button<?php echo $documentId; ?>" onclick="poveziNostr(this)" data-kandidat_dipl_id="<?php echo $candDiplId; ?>" data-kandidat_id="<?php echo $candidateIdJob; ?>" data-nalog_id="<?php echo $nalog_id; ?>" data-document_path="<?php echo $documentFile; ?>" type="button" title="Spremi"><i class="fa fa-check-circle" aria-hidden="true"></i></button> 
													<a class="btn btn-danger material-btn material-btn_danger potvrdi_ne<?php echo $documentId; ?>" title="Odustani" ><i class="fa fa-times-circle" aria-hidden="true"></i></a>
												</span>
											</div>
										</td>
										<script>
											$('.potvrdi<?php echo $documentId; ?>').on('click', function() {
												$('.potvrdi_da<?php echo $documentId; ?>').css('display', 'block');
												$('.full_recognition<?php echo $documentId; ?>').css('display', 'block');
												$('#fullRecognition<?php echo $documentId; ?>').val(null).selectpicker("refresh");
												$('.nesto<?php echo $documentId; ?>').css('display', 'none');
												$( "#potvrdi_da_button<?php echo $documentId; ?>" ).prop( "disabled", true );
											});	
											$('.potvrdi_ne<?php echo $documentId; ?>').on('click', function() {
												$('.potvrdi_da<?php echo $documentId; ?>').css('display', 'none');
												$('.full_recognition<?php echo $documentId; ?>').css('display', 'none');
												$('#fullRecognition<?php echo $documentId; ?>').val(null).selectpicker("refresh");
												$('.nesto<?php echo  $documentId; ?>').css('display', 'block');
												$( "#potvrdi_da_button<?php echo $documentId; ?>" ).prop( "disabled", true );
											});	

											$('#fullRecognition<?php echo $documentId; ?>').change(function() {
												let fullRecVal = $('#fullRecognition<?php echo $documentId; ?>').val();
												if ( fullRecVal == 2 || fullRecVal == 1 || fullRecVal == 0 ) {
													$( "#potvrdi_da_button<?php echo $documentId; ?>" ).data('full_recognition', fullRecVal);
													if ($("#potvrdi_da_button<?php echo $documentId; ?>").data("full_recognition") == 2 || $("#potvrdi_da_button<?php echo $documentId; ?>").data("full_recognition") == 1 || $("#potvrdi_da_button<?php echo $documentId; ?>").data("full_recognition") == 0) {
														$( "#potvrdi_da_button<?php echo $documentId; ?>" ).prop( "disabled", false );
													} else {
														$( "#potvrdi_da_button<?php echo $documentId; ?>" ).prop( "disabled", true );
													}
												}
											});
										</script>
										
										<input type="hidden" name="kandidat_id" value="<?php echo $idCanJob; ?>">
										<input type="hidden" name="kandidat_dipl_id" value="<?php echo $candDiplId; ?>">
										<input type="hidden" name="nalog_id" value="<?php echo $nalog_id; ?>">
										<input type="hidden" name="documentFullPath" value="<?php echo $documentFile; ?>">
									</tr>		
									<?php
								}
							?> 
							
							</tbody>
						</table>
						<script>
							function poveziNostr(thisRow){
								var kandidat_id			= $(thisRow).data("kandidat_id");
								var kandidat_dipl_id	= $(thisRow).data("kandidat_dipl_id");
								var nalog_id			= $(thisRow).data("nalog_id");
								var document_path		= $(thisRow).data("document_path");
								var full_recognition 	= $(thisRow).data("full_recognition");

								//alert(kandidat_id+" "+kandidat_dipl_id+" "+nalog_id+" "+document_path+" Data:"+full_recognition); 
								
								$.ajax({
									url: 'do.php?form=poveziNostrifikaciju',
									type: 'POST',
									dataType: 'html',
									data:{
										'kandidat_id': kandidat_id,
										'kandidat_dipl_id': kandidat_dipl_id,
										'nalog_id': nalog_id,
										'documentFullPath': document_path,
										'full_recognition': full_recognition,
									},
									success : function (){
										window.location.reload();
									},
									error: function (xhr, ajaxOptions, thrownError) {
										alert(xhr.status);
										alert(thrownError);
									}
								});
							}
						</script>
					<?php
					}else{
						echo "Kandida nema dokumenata za prikaz";	
					}
					unset($otherDocuments);
                   		?>
						<hr>
						
					<form action="<?php getSiteURL(); ?>do.php?form=dodajNostrifikaciju" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
						<div class="form-group" id = "fullRecognitionDiv" style="display: none;">
							<div class="col-md-offset-2 col-sm-8 text-center">
								<label for="fullRecognition" class="col-sm-5 control-label">
									<span class="text-danger">*</span>
									Vrsta priznanja:
								</label>
								<div class="col-sm-6">
									<div class="">
										<select class="selectpicker" id="fullRecognition" title = "Odaberite opciju" name="fullRecognition" required>
											<option value = "2">Evaluacija</option>
											<option value = "1">Potpuno priznata</option>
											<option value = "0">Djelimično priznata</option>
										</select>
									</div>
								</div>
								<div class="col-sm-1 text-right">
									<i class="fa fa-question-circle fa-2x" style="color:red;" aria-hidden="true" data-toggle="tooltip" data-placement="right" title="" data-original-title="Ovom opcijom se vrši označavanje da li je nostrifikovana diploma potpuno ili djelimično priznata!"></i>
								</div>
							</div>
						</div>
						<div class="row text-center">
							<div class="fileinput fileinput-new " data-provides="fileinput">
								<p class="fileinput-exists">Dokument učitan. Prije potvrde odredite vrstu priznanja!</p>
								<span class="btn btn-info btn-file">
									<span class="fileinput-new"> 
									Izaberi novi dokument 
									</span>
									<span class="fileinput-exists">
										Promijeni
									</span>
									<input type="hidden"  name="certificate_path" id="certificate_path">
									<input type="file"  name="naziv_dokument_new" id="naziv_dokument_new" >
								</span>
								<button class="btn btn-success fileinput-exists" type="submit">Potvrdi</button>
							</div>
							
								<input type="hidden" name="kandidat_id" value="<?php echo $idCanJob; ?>">
								<input type="hidden" name="kandidat_dipl_id" value="<?php echo $candDiplId; ?>">
								<input type="hidden" name="nalog_id" value="<?php echo $nalog_id; ?>">
							<script> 
								$(function (){
									$('#naziv_dokument_new').change(function (){
										if($('#naziv_dokument_new').val() !== ""){
											$('#fullRecognitionDiv').css('display', 'block');
											var ext = $('#naziv_dokument_new').val().split('.').pop().toLowerCase();
											if($.inArray(ext, ['jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png']) == -1) {
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
					</form>
					<?php
				break;	

				case "editCastingInfo":
					$pap_id=$_POST["pap_id"];
					$appointment_query=$db->prepare("SELECT pap_date, 
															pap_nalog_id, 
															pap_city, 
															pap_location_name, 
															pap_google_maps_location, 
															pap_first_sending_number_days, 
															pap_second_sending_number_days, 
															pap_first_send_enabled, 
															pap_second_send_enabled, 
															pap_group_id 
														FROM 
															idk_pp_appointments 
														WHERE pap_id = :pap_id");
					$appointment_query->execute(array(
						":pap_id" => $pap_id
					));
					$appointment_data=$appointment_query->fetch();

					$pap_date 							= $appointment_data['pap_date'];
					$pap_nalog_id						= $appointment_data['pap_nalog_id'];
					$pap_location_name					= $appointment_data['pap_location_name'];
					$pap_google_maps_location			= $appointment_data['pap_google_maps_location'];
					$pap_first_sending_number_days		= $appointment_data['pap_first_sending_number_days'];
					$pap_second_sending_number_days		= $appointment_data['pap_second_sending_number_days'];
					$pap_first_send_enabled				= $appointment_data['pap_first_send_enabled'];
					$pap_second_send_enabled			= $appointment_data['pap_second_send_enabled'];
					?>
						<form action="<?php getSiteURL(); ?>do.php?form=editCastingDetails" method="post">
							<input type="hidden" name="pap_id" id="pap_id" value="<?php echo $pap_id; ?>" >
							<input type="hidden" name="pap_nalog_id" id="pap_nalog_id" value="<?php echo $pap_nalog_id; ?>" >
							<div class="row col-md-10 col-md-offset-1">
								<div class="alert alert-danger text-center">
									<h3>Upozorenje</h3><br><br>
									U slučaju da polje <strong>Naziv/adresa lokacije</strong> nije poznato - isto ostavite prazno. <br>
									<small>U slučaju da se unese neka neispravna vrijednost u polje, kandidatima će ista biti ispisana na formi kojoj pristupaju putem SMS linka!</small>
								</div>
								<div class="form-group col-md-12">
									<div class="col-xs-4" style="padding-top:5px; text-align: left;">Naziv/adresa lokacije:</div>
									<div class="col-sm-8">
										<div class="form-group materail-input-block materail-input-block_success">
											<input type="text" class="form-control materail-input" name="pap_location_name" id="pap_location_name" value="<?php if(isset($pap_location_name)){ echo $pap_location_name;} ?>" placeholder="Lokacija - adresa">
											<span class="materail-input-block__line"></span>
										</div>
									</div>
								</div>
								<div class="alert alert-danger text-center" style = "margin-top: 80px;">
									<h3>Upozorenje</h3><br><br>
									U slučaju da polje <strong>Link lokacije</strong> nije poznato ili nije moguće generisati link jer nije poznata lokacija - isto ostavite prazno. <br>
									<small>U slučaju da se unese neki neispravan link u polje, kandidatima će biti dostupno dugme koje vodi na neispravan link na formi kojoj pristupaju putem SMS linka!</small>
								</div>
								<div class="form-group col-md-12">
									<div class="col-xs-4" style="padding-top:5px; text-align: left;">Link lokacije</div>
									<div class="col-sm-8">
										<div class="form-group materail-input-block materail-input-block_success">
											<input type="text" class="form-control materail-input" name="pap_google_maps_location" id="pap_google_maps_location" value="<?php if(isset($pap_google_maps_location)){ echo $pap_google_maps_location;} ?>" placeholder="Google maps link lokacije">
											<span class="materail-input-block__line"></span>
										</div>
									</div>
								</div>
								<div class="form-group col-md-12">
									<div class="col-xs-8" style="padding-top:5px; text-align:left;">Uključi slanje prve poruke:</div>
									<div class="materail-input-block materail-input-block_success idk_radio_buttons">
										<label class="main-container__column material-radio-group material-radio-group_success" for="pap_first_send_enabled_da">
											<input type="radio" name="pap_first_send_enabled" id="pap_first_send_enabled_da" class="material-radiobox" value="1" <?php if($pap_first_send_enabled=="1"){ echo "checked";} ?>/>
											<span class="material-radio-group__element material-radio-group__check-radio"></span>
											<span class="material-radio-group__element material-radio-group__caption">Da</span>
										</label>
									</div>
									<div class="materail-input-block materail-input-block_danger idk_radio_buttons">
										<label class="main-container__column material-radio-group material-radio-group_danger" for="pap_first_send_enabled_ne">
											<input type="radio" name="pap_first_send_enabled" id="pap_first_send_enabled_ne" class="material-radiobox" value="0" <?php if($pap_first_send_enabled=="0"){ echo "checked"; } ?> />
											<span class="material-radio-group__element material-radio-group__check-radio"></span>
											<span class="material-radio-group__element material-radio-group__caption">Ne</span>
										</label>
									</div>
								</div>

								<div class="form-group col-md-10 text-left first_sending_number_days" style="padding-left: 30px; display: block;">
									Šalji prvu poruku   			
									<input type="number" id="pap_first_sending_number_days" name="pap_first_sending_number_days" min="<?php if($pap_second_send_enabled=="1"){ echo intval($pap_second_sending_number_days)+1;}else{ echo "1";} ?>" max="" style="width: 60px; text-align: center;" value="<?php if($pap_first_sending_number_days!="0"){ echo $pap_first_sending_number_days; }else{ echo "7";} ?>"> 
									dana prije termina
								</div>
								<div class="form-group col-md-12">
									<div class="col-xs-8" style="padding-top:5px; text-align:left;">Uključi slanje druge poruke:</div>
									<div class="materail-input-block materail-input-block_success idk_radio_buttons">
										<label class="main-container__column material-radio-group material-radio-group_success" for="pap_second_send_enabled_da">
											<input type="radio" name="pap_second_send_enabled" id="pap_second_send_enabled_da" class="material-radiobox" value="1" <?php if($pap_second_send_enabled=="1"){ echo "checked";} ?>/>
											<span class="material-radio-group__element material-radio-group__check-radio"></span>
											<span class="material-radio-group__element material-radio-group__caption">Da</span>
										</label>
									</div>
									<div class="materail-input-block materail-input-block_danger idk_radio_buttons">
										<label class="main-container__column material-radio-group material-radio-group_danger" for="pap_second_send_enabled_ne">
											<input type="radio" name="pap_second_send_enabled" id="pap_second_send_enabled_ne" class="material-radiobox" value="0" <?php if($pap_second_send_enabled=="0"){ echo "checked"; } ?> />
											<span class="material-radio-group__element material-radio-group__check-radio"></span>
											<span class="material-radio-group__element material-radio-group__caption">Ne</span>
										</label>
									</div>
								</div>
								<div class="form-group col-md-10 text-left second_sending_number_days" style="padding-left: 30px; display: block;">
									Šalji drugu poruku   			
									<input type="number" id="pap_second_sending_number_days" name="pap_second_sending_number_days" min="1" max="<?php if($pap_first_send_enabled=="1"){ echo intval($pap_first_sending_number_days)-1;} ?>" style="width: 60px; text-align: center;" value="<?php if($pap_second_sending_number_days!="0"){ echo $pap_second_sending_number_days; }else{ echo "2";} ?>"> 
									dana prije termina
								</div>
							</div>
							<script>

								$(document).ready(function () {
									if(!$('#pap_first_send_enabled_da').is(':checked') ){
										$('.first_sending_number_days').css('display', 'none');
										$('#pap_first_sending_number_days').removeAttr("max");
										$('#pap_first_sending_number_days').removeAttr("min");
									}else{
									}
										
									if(!$('#pap_second_send_enabled_da').is(':checked') ){
										$('.second_sending_number_days').css('display', 'none');
										$("#pap_second_sending_number_days").removeAttr("min");
										$("#pap_second_sending_number_days").removeAttr("max");
									}else{
									}
								});
								$('#pap_first_send_enabled_da').click(function(){

									$("#pap_first_sending_number_days").val(7);
									$('.first_sending_number_days').css('display', 'block');
									let first_sending =parseInt($("#pap_first_sending_days").val());
									let second_sending=parseInt($("#pap_second_sending_number_days").val());

									$("#pap_first_sending_number_days").attr({"min":second_sending+2});									
									$("#pap_second_sending_number_days").attr({"max":first_sending-2});									
								
								});
								$('#pap_first_send_enabled_ne').click(function(){
									
									$("#pap_first_sending_number_days").removeAttr("min");
									$("#pap_first_sending_number_days").removeAttr("max");
									$("#pap_second_sending_number_days").removeAttr("max");
									$('.first_sending_number_days').css('display', 'none');
								});

								$('#pap_second_send_enabled_da').click(function(){
									
									$("#pap_second_sending_number_days").val(2);
									let first_sending =parseInt($("#pap_first_sending_days").val());
									let second_sending=parseInt($("#pap_second_sending_number_days").val());
									$('.second_sending_number_days').css('display', 'block');
									
									$("#pap_first_sending_number_days").attr({"min":second_sending+2});									
									$("#pap_second_sending_number_days").attr({"max":first_sending-2});									
									$("#pap_second_sending_number_days").attr({"min":1});									
								});
								$('#pap_second_send_enabled_ne').click(function(){
									$('.second_sending_number_days').css('display', 'none');
									$("#pap_second_sending_number_days").removeAttr("min");
									$("#pap_second_sending_number_days").removeAttr("max");
									$("#pap_first_sending_number_days").attr({"min": 1});
								});

								$("#pap_second_sending_number_days").bind('keyup mouseup',function() {
									var prvo_slanje		= parseInt($("#pap_first_sending_number_days").val());
									var drugo_slajnje	= parseInt($("#pap_second_sending_number_days").val());
									if($("#pap_first_send_enabled_da").is(':checked')){
										$("#pap_first_sending_number_days").attr({"min": drugo_slajnje+2})
									}
								});

								$("#pap_first_sending_number_days").bind('keyup mouseup',function() {
									var prvo_slanje		= parseInt($("#pap_first_sending_number_days").val());
									var drugo_slajnje	= parseInt($("#pap_second_sending_number_days").val());
									if($("#pap_second_send_enabled_da").is(':checked')){
										$("#pap_second_sending_number_days").attr({"max": prvo_slanje-2})
									}
								});
							</script>
							<div class="row col-md-10 col-md-offset-1">
								<?php
									$castinInfoButton=""; 
									if($pap_first_send_enabled=="1"){
										if(date('Y-m-d',strtotime($pap_date.' - '.$pap_first_sending_number_days.' days'))<=date('Y-m-d')){
											$castinInfoButton="disabled";
										}

									}else if($pap_second_send_enabled=="1"){
										if(date('Y-m-d',strtotime($pap_date.' - '.$pap_second_sending_number_days.' days'))<=date('Y-m-d')){
											$castinInfoButton="disabled";
										}
									}
								?>
								<hr>
								<button <?php echo $castinInfoButton; ?> type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-edit" aria-hidden="true"></i> <span>Spremi</span></button>
							</div>
						</form>
						<?php
					//var_dump($pap_first_send_enabled);
					//var_dump($appointment_data);

				break;	

				case "otvoriTangoTutorial":
				
					$tutorijal_id=$_POST['tutorial_id'];
					$getTangoUrl_query=$db->prepare("SELECT tutorijal_yt_url FROM idk_tutorijali where tutorijal_id=:tutorijal_id");
					$getTangoUrl_query->execute(array( ':tutorijal_id' => $tutorijal_id));
					$getTangoUrl=$getTangoUrl_query->fetch();
					$TangoUrl=$getTangoUrl['tutorijal_yt_url'];
				
					echo $TangoUrl;
				
				break;
				
				case "tutorialLog":
					if(isset($_POST['tutorial_id'])){

						$tutorial_id = $_POST['tutorial_id'];
						
						$get_tutorial_details = $db->prepare("SELECT kategorija_id,kategorija_naziv,tutorijal_opis FROM `idk_tutorijali` left join idk_kategorije on tutorijal_kategorija_id=kategorija_id  WHERE `tutorijal_id`=:tutorial_id");
						$get_tutorial_details -> execute(array(':tutorial_id'=>$tutorial_id));
						$tutorial_details = $get_tutorial_details->fetch();
						$tutorial_kat_ime = $tutorial_details['kategorija_naziv'];
						$tutorial_opis = $tutorial_details['tutorijal_opis'];
						$log_desc = "Otvorio tutorial(link): ".$tutorial_opis.". Kategorija: ".$tutorial_kat_ime.".";
						addToLogs($log_desc, 0); 
					}
					if(isset($_POST['tutorial_doc'])){

						$tutorial_doc = $_POST['tutorial_doc'];
						
						$get_tutorial_details = $db->prepare("SELECT tutorijal_id, kategorija_id,kategorija_naziv,tutorijal_opis FROM `idk_tutorijali` left join idk_kategorije on tutorijal_kategorija_id=kategorija_id  WHERE `tutorijal_doc` LIKE :tutorial_doc");
						$get_tutorial_details -> execute(array(':tutorial_doc'=>$tutorial_doc));
						$tutorial_details = $get_tutorial_details->fetch();
						$tutorial_kat_ime = $tutorial_details['kategorija_naziv'];
						$tutorial_opis = $tutorial_details['tutorijal_opis'];
						$log_desc = "Otvorio tutorial(dokument): ".$tutorial_opis.". Kategorija: ".$tutorial_kat_ime.".";
						addToLogs($log_desc, 0); 
					}


				break;
			}
				
	?>

		</div>

