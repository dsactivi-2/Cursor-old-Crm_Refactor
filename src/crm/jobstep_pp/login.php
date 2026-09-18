<?php 
	include("includes/function.php");
	
	//$isLoggedIn = isLoggedInR();
	
	//if($isLoggedIn == 0){
		$languageUser = 1;
		include("includes/language/language.php");
?>
<!doctype html>
<html class="h-100">
	<head>
		<?php
			include("includes/head.php"); 
		?>
	</head>
	
	<body class = "d-flex flex-column h-100">
		<div class="container justify-content-center d-flex h-100 my-2 scrollbar-hidden overflow-auto">
			<div class = "row align-items-center w-100">
				<div class = "col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
					<div class = "row gx-5 gy-3 align-items-center">
						<div class = "col-md-4">
							<div id = "login_section">
								<p style = "font-style: normal;font-weight: bold;font-size: 28px;line-height: 33px;"><?php echo $txtArray["JobStep"][$languageUser]; ?></p>
								<p style = "font-style: normal;font-weight: bold;font-size: 28px;line-height: 33px;"><?php echo $txtArray["Dobrodošli na JobStep platformu!"][$languageUser]; ?></p>
								<label style = "font-style: normal; font-weight: 600; font-size: 15px; line-height: 18px;" for = "inputEmail"><?php echo $txtArray["Email:"][$languageUser]; ?></label>
								<div class="login_input_group">
									<span><i class="fa fa-user" aria-hidden="true"></i></span>
									<input type = "email" class = "form-control js_input" id = "input_email" name = "input_email">
								</div>
								<label style = "font-style: normal; font-weight: 600; font-size: 15px; line-height: 18px;" for = "inputEmail"><?php echo $txtArray["Lozinka:"][$languageUser]; ?></label>
								<div class = "login_input_group">
									<i class="fa fa-lock" aria-hidden="true"></i>
									<input type = "password" class = "form-control js_input" id = "input_password" name = "input_password">
								</div>
								<div class = "row mt-3">
									<div class = "col-12 ">
										<input type="checkbox" class="form-check-input js_input me-2" id="checkbox_remember_me" name="checkbox_remember_me">
										<label class="form-check-label" for="checkbox_remember_me" style = "font-style: normal; font-weight: 600; font-size: 15px; line-height: 18px;"> <?php echo $txtArray["Zapamti me"][$languageUser]; ?></label>
									</div>
								</div>
								<div class = "mt-5" style="text-align:center;">
									<button id = "btn_log_in" class = "login_button"><?php echo $txtArray["Prijavi se"][$languageUser]; ?></button>
								</div>
								<div class = "mt-3" style="text-align:center;">
									<a href = "#" id = "action_forgot_password" class = "forgot_password"><?php echo $txtArray["Zaboravili ste lozinku?"][$languageUser]; ?></a>
								</div>
								
								<div class = "mt-4" style = "text-align:center; display:none;" id = "msg_login_wrong_mail">
									<p class = "text_negative"><?php echo $txtArray["Pogrešan email"][$languageUser]; ?></p>
								</div>
								<div class = "mt-4" style = "text-align:center; display:none;" id = "msg_login_wrong_password">
									<p class = "text_negative"><?php echo $txtArray["Pogrešna lozinka"][$languageUser]; ?></p>
								</div>
								<div class = "mt-4" style = "text-align:center; display:none;" id = "msg_login_wrong_deactivate">
									<p class = "text_negative"><?php echo $txtArray["Korisnički profil je deaktiviran"][$languageUser]; ?></p>
								</div>
							</div>
							
							<div id = "forgot_password_section" style = "display:none;">
								<p style = "font-style: normal;font-weight: bold;font-size: 28px;line-height: 33px;"><?php echo $txtArray["JobStep"][$languageUser]; ?></p>
								<p style = "font-style: normal;font-weight: bold;font-size: 28px;line-height: 33px;"><?php echo $txtArray["Zaboravili ste lozinku?"][$languageUser]; ?></p>
								<p style = "font-style: normal;font-weight: 500;font-size: 16px;line-height: 19px;color: #8E97A3;"><?php echo $txtArray["Ne brinite! Svima se dešava. Unesite email adresu povezanu sa Vašim računom."][$languageUser]; ?></p>
								
								<label for = "input_email_forgot_password"><?php echo $txtArray["Email:"][$languageUser]; ?></label>
								<div class = "login_input_group">
									<i class="fa fa-envelope" aria-hidden="true"></i>
									<input type = "email" class = "form-control js_input" id = "input_email_forgot_password" name = "input_email_forgot_password">
								</div>
								<div class = "mt-4" style="text-align:center;">
									<button id = "btn_send_password_recovery_mail" class = "login_button"><?php echo $txtArray["Potvrdi"][$languageUser]; ?></button>
								</div>
								<div class = "mt-4" style = "text-align:center;display:none;" id = "msg_retrieve_password_wrong_mail">
									<p class = "text_negative"><?php echo $txtArray["Pogrešan email"][$languageUser]; ?></p>
								</div>
							</div>
							
							<div id = "retrieve_password_section" style = "display:none">
								<p class = "text_positive text-center"><?php echo $txtArray["Mail je poslan na Vašu adresu. Mail sadrži link na kojem možete da unesete Vašu novu šifru."][$languageUser]; ?><br><br>
									<i class="fa fa-check" style = "color:#3C857D;font-size:30px;" aria-hidden="true"></i>
								</p>
							</div>
							<script>
								$(document).ready(function() {
									$(document).on('keypress',function(e) {
										if(e.which == 13) {
											$('#btn_log_in').trigger('click');
										}
									});
																		
									$('#action_forgot_password').on('click', function(){
										$(document).on('keypress',function(e) {
											if(e.which == 13) {
												$('#btn_send_password_recovery_mail').trigger('click');
											}
										});
										$("#login_section").hide('slide',600, function(){
											$("#forgot_password_section").show('slide', 600);
										});
									});
									
									$('#btn_log_in').on('click', function(){
										var input_email 	= $('#input_email').val();
										var input_password 	= $('#input_password').val();
										var input_check 	= 0;
										if ($('#checkbox_remember_me').is(":checked")){
											input_check = 1;
										}
										var flag_empty	 	= false;
										
										if(input_email == ""){
											$('#input_email').effect('highlight');
											$('#input_email').parent().effect('bounce');
											$('#input_email').effect('highlight');
											flag_empty = true;
										}
										if(input_password == ""){
											$('#input_password').effect('highlight');
											$('#input_password').parent().effect('bounce');
											$('#input_password').effect('highlight');
											flag_empty = true;
										}
										
										if(!flag_empty){
											
											$.ajax({
												url: 'ajax.php?action=check_login_data',
												type: 'POST',
												dataType: 'html',
												data: {
													'input_email' 		: input_email,
													'input_password' 	: input_password,
													'input_check' 		: input_check
												},
												success: function(response) {
													if(response == 0){
														$('#msg_login_wrong_mail').show('clip');
														$('#input_email').parent().addClass('input_negative');
														$('#input_password').parent().addClass('input_negative');
														$('#input_email').effect('highlight');
														$('#input_password').effect('highlight');
														$('#msg_login_wrong_mail').effect('bounce');

													}
													
													else if(response == 1){
														$('#input_email').parent().removeClass('input_negative');
														$('#msg_login_wrong_mail').hide('clip', function(){
															$('#msg_login_wrong_password').show('clip',function(){
																$('#msg_login_wrong_password').effect('bounce');																
															});															
														});
														$('#input_password').parent().addClass('input_negative');
														$('#input_password').effect('highlight');
													}
													
													else if(response == 2){
														$('#input_email').parent().removeClass('input_negative');
														$('#input_password').parent().removeClass('input_negative');
														$('#msg_login_wrong_mail').hide('clip', function(){
															$('#msg_login_wrong_password').hide('clip');															
														});
														//dodati lokaciju
														window.location.href = '<?php getSiteUrl(); ?>'+'dashboard.php';
													}
													
													else if(response == 3){
														$('#input_email').parent().removeClass('input_negative');
														$('#input_password').parent().removeClass('input_negative');
														$('#msg_login_wrong_deactivate').show('clip',function(){
															$('#msg_login_wrong_deactivate').effect('bounce');
														});
													}
												},
												error: function (xhr, ajaxOptions, thrownError) {
													alert(xhr.status);
													alert(thrownError);
												}
											});
										}
									});
									$('#btn_send_password_recovery_mail').on('click', function(){
										var input_email = $('#input_email_forgot_password').val();
										if(input_email == ''){
											$('#input_email_forgot_password').effect('highlight');
											$('#input_email_forgot_password').parent().effect('bounce');
											$('#input_email_forgot_password').effect('highlight');
										}
										else{
											$.ajax({
												url: 'ajax.php?action=check_email',
												type: 'POST',
												dataType: 'html',
												data: {
													'input_email' : input_email
												},
												success: function(response) {
													if(response == 0){
														// $('#msg_retrieve_password_wrong_mail').css('visibility', 'visible');
														$('#input_email_forgot_password').parent().addClass('input_negative');
														// $('#input_email_forgot_password').effect('highlight');
														// $('#msg_retrieve_password_wrong_mail').effect('bounce');
														
														$('#msg_retrieve_password_wrong_mail').show('clip');
													}
													else if(response == 1){
														$.ajax({
															url: 'ajax.php?action=send_mail_password_recovery',
															type: 'POST',
															dataType: 'html',
															data: {
																'input_email' : input_email
															},
															success: function(response) {														
																$("#forgot_password_section").hide('slide',600, function(){
																	$("#retrieve_password_section").show('slide', 600);
																});
															},
															error: function (xhr, ajaxOptions, thrownError) {
																alert(xhr.status);
																alert(thrownError);
															}
														});
													}

												},
												error: function (xhr, ajaxOptions, thrownError) {
													alert(xhr.status);
													alert(thrownError);
												}
											});
										}
									});
								});
							</script>
							
						</div>
						<div class = "col-md-8 text-center">
							<img src = "images/login_photo1.svg" class="img-fluid">
						</div>
					</div>
				</div>
			</div>
		</div>
		<!--<main class=  "container justify-content-center d-flex h-100 my-2 scrollbar-hidden overflow-auto d-inline">
			<div class = "row d-flex align-items-center">
				<div class = "col-3" style = "width:30%!important">
					
					
					
					
				</div>
				<div class = "col-9"  style = "width:70%!important">
					<img src = "images/login_photo.png">
				</div>
			</div>
		</main>-->
		<?php
			include("includes/footer.php"); 
		?>
	</body>
</html>
<?php 
		unset($txtArray);
	// }else{
		// header("Location:".getSiteUrlr()."/dashboard.php");
	// }
?>