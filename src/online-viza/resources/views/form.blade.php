<style>
body{
	background: #649aa3 !important;
}
.form-control-lg{
	/*border-radius: 0px !important;*/
}
textarea.form-control {
    /*border-radius: 0px !important;*/
}
.dugme_next {
    width: 100% !important;
}
.py-4{
	position:absolute;
	width:100%;
	margin-top:0px; 
}
footer{
	display:none!important;
}
</style>
@extends('layouts.app')

@section('content')
<?php
foreach ($kandidat_info as $kandidat_info){
	$name = $kandidat_info->name_dak_kandidat;
	$lastname = $kandidat_info->lastname_dak_kandidat;
	$email = $kandidat_info->email_dak_kandidat;
	$daypart = $kandidat_info->daypart_dak_kandidat;
	$comment = $kandidat_info->comment_dak_kandidat;
	$dipl = $kandidat_info->dipl_dak_kandidat;
	$osiguranje = $kandidat_info->osiguranje_dak_kandidat;
}
?>
<div class="container form-okvir" style="padding:20px;">
	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card">
			
				<div class="card-header text-center">
					<p class="h3 text-center">Osnovne informacije</p>
				</div> 
				<!-- START BODY CARD -->
				<div class="card-body">
					<form method="post" id="form3" action="/online-viza/save_info" role="form">
					@csrf
						<input type="hidden" name="hashed_codetel" value="<?php echo $hashed_codetel;?>"></input> 
						
						<div class="form-group">
						  <label for="name"><i style = "margin-right: 10px; margin-left: 10px;" class="fas fa-scroll"></i> Odaberite uslugu:</label>
						</div>
							<div class="form-group">
								<div class="checkbox-animated-inline" style="margin-left: 20px;">
									<input id="checkbox_animated_4" name="osiguranje" type="checkbox" <?php if($osiguranje == 1){echo "checked";}?>>
									<label for="checkbox_animated_4">
										<span class="check"></span>
										<span class="box"></span>
										  ZAHTJEV ZA VIZU
									</label>
								</div>
							</div>
							<div class="form-group">
								<div class="checkbox-animated-inline" style="margin-left: 20px;">
									<input id="checkbox_animated_5" name="dipl" type="checkbox" <?php if($dipl == 1){echo "checked";}?>>
									<label for="checkbox_animated_5">
										<span class="check"></span>
										<span class="box"></span>
										  NOSTRIFIKACIJA DIPLOME U NJEMAČKOJ
									</label>
								</div>
							</div>
						
						
						<div class="form-group">
						  <label for="name"><i style = "margin-right: 10px; margin-left: 10px;" class="fas fa-user"></i> Ime:</label>
						  <input type="text" class="form-control form-control-lg" name="name" id="name" placeholder="Unesite ime" value="<?php echo $name;?>" autocomplete="off" required>
						</div>
				
						<div class="form-group">
						  <label for="lastname"><i style = "margin-right: 10px; margin-left: 10px;" class="fas fa-user"></i> Prezime:</label>
						  <input type="text" class="form-control form-control-lg" name="lastname" id="lastname" placeholder="Unesite prezime" value="<?php echo $lastname;?>" autocomplete="off" required>
						</div>
						
						<div class="form-group">
						  <label for="email"><i style = "margin-right: 10px; margin-left: 10px;" class="fas fa-envelope"></i> E-mail adresa:</label>
						  <input type="email" class="form-control form-control-lg" name="email" id="email" aria-describedby="emailHelp" value="<?php echo $email;?>" placeholder="Unesite e-mail" required>
						  <small id="emailHelp" class="form-text text-muted">
							Nikada nećemo dijeliti vaš e-mail sa drugima! 
						  </small>
						</div>
						<!--
						<div class="form-group">
							 <label for="password"><i style = "margin-right: 10px; margin-left: 10px;" class="fas fa-calendar"></i> When to contact you:</label>
							 <input type="text" class="form-control form-control-lg" placeholder="Datum i vrijeme" name="call_date" id="call_date">
						</div>
						
						<script>
							$("#call_date").flatpickr({
								enableTime: false,
								time_24hr: true,
								minDate: "<?php echo date("Y-m-d"); ?>",
								dateFormat: "d.m.Y", 
							});
						</script>
						-->
						<div class="form-group">
						  <label for="role"><i style = "margin-right: 10px; margin-left: 10px;" class="fas fa-clock"></i> Period dana za poziv:</label>
						  <select class="form-control  form-control-lg custom-select" id="role" name="daypart">
							<option value="0" <?php if($daypart == 0) echo "selected"; ?> >Jutro</option>
							<option value="1" <?php if($daypart == 1) echo "selected"; ?> >Podne</option>
							<option value="2" <?php if($daypart == 2) echo "selected"; ?> >Predvečer</option> 
						  </select>
						</div>

						<div class="form-group">
						  <label for="comments"><i style = "margin-right: 10px; margin-left: 10px;" class="fas fa-comment"></i> Komentar (opcionalno):</label> 
						  <textarea name="comment" class="form-control" id="comments" rows="3"><?php echo $comment;?></textarea>
						</div>
						<!--
						<div class="form-group">
						<label for="comments"><i style = "margin-right: 10px; margin-left: 10px;" class="fas fa-file-image"></i> Do you have passport picture?  </label>
						</div>
						<div class="form-group">
							<div class="checkbox-animated-inline" style="margin-left: 20px;">
								<input id="checkbox_animated_4" type="checkbox">
								<label for="checkbox_animated_4">
									<span class="check"></span>
									<span class="box"></span>
									  DA
								</label>
							</div>
							<div class="checkbox-animated-inline">
								<input id="checkbox_animated_5" type="checkbox">
								<label for="checkbox_animated_5">
									<span class="check"></span>
									<span class="box"></span>
									  NE
								</label>
							</div>
						</div>
						<script>
						$(document).ready(function() {
							$("#checkbox_animated_4").on( "click", function() {
								$('.image_file').css('display','block');
								$('#checkbox_animated_5').prop('checked', false);
							});
							$("#checkbox_animated_5").on( "click", function() {
								$('.image_file').css('display','none');
								$('#checkbox_animated_4').prop('checked', false); 
							});
						});
						</script>
						<div class="form-group image_file" style="display:none;">
						  <input type="file" class="form-control-file" id="photo">
						</div>
						-->
						<div style = "text-align: center;" class="form-check">
							<h6>Nastavljanjem dalje prihvatate<br><a href="https://jobstep-app.com/online-viza/privacypolicy.html" target="_blank">pravila privatnosti</a></h6>
						</div>
						<div class="form-group text-center">
							<button type="submit" form="form3" class="btn dugme_next" >SAČUVAJ<i style = "margin-left: 10px" class="fas fa-save"></i></button>
						</div>
					</form>
				</div>
				<!-- END BOGY CARD-->
			</div> 
		</div> 
	</div>
</div>
@endsection
