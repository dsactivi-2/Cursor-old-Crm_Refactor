<?php 
	include("includes/function.php");
	include("includes/connect.php");
	
	$user_key = $_REQUEST['token'];
	$languageUser = getLanguageForUserByToken($user_key);
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
							<input style = "display:none" id = "user_key" value = "<?php echo $user_key; ?>">
							<div id = "password_recovery">
								<p style = "font-style: normal;font-weight: bold;font-size: 28px;line-height: 33px;">JOBSTEP</p>
								<p style = "font-style: normal;font-weight: bold;font-size: 28px;line-height: 33px;"><?php echo $txtArray["Unesite Vašu novu lozinku"][$languageUser]; ?></p>
								
								<label style = "font-style: normal; font-weight: 600; font-size: 15px; line-height: 18px;" for = "input_password"><?php echo $txtArray["Nova lozinka:"][$languageUser]; ?></label>
								
								<div class = "login_input_group">
									<i class="fa fa-lock" aria-hidden="true"></i>
									<input type = "password" class = "form-control js_input" id = "input_password" name = "input_password" minlength="8">
								</div>
								
								<label style = "font-style: normal; font-weight: 600; font-size: 15px; line-height: 18px;" for = "input_password_confirmation"><?php echo $txtArray["Ponovite lozinku:"][$languageUser]; ?></label>
								<div class = "login_input_group">
									<i class="fa fa-lock" aria-hidden="true"></i>
									<input type = "password" class = "form-control js_input" id = "input_password_confirmation" name = "input_password_confirmation" minlength="8">
								</div>
								
								<div class = "mt-4" style="text-align:center;">
									<button id = "btn_create_new_password" class = "login_button"><?php echo $txtArray["Potvrdi"][$languageUser]; ?></button>
								</div>
								
								<div class = "mt-4" style = "text-align:center; display:none;" id = "msg_passwords_dont_match">
									<p class = "text_negative"><?php echo $txtArray["Lozinke se ne poklapaju"][$languageUser]; ?></p>
								</div>
							</div>
							<div id = "retrieve_password_section" style = "display:none">
								<p class = "text_positive text-center"> 
									<i class="fa fa-check" style = "color:#3C857D;font-size:30px;" aria-hidden="true"></i><br><br>
									<?php echo $txtArray["Uspješno ste postavili novu lozinku. Klikom na dugme ispod se možete vratiti na log in page"][$languageUser]; ?>
								</p>
								<div class = "mt-4" style="text-align:center;">
									<button id = "btn_back_to_login" class = "login_button"><?php echo $txtArray["Prijava"][$languageUser]; ?></button>
								</div>
							</div>
						</div>
						<div class = "col-md-8 text-center">
							<img src = "images/login_photo1.svg" class="img-fluid">
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<script>
			$('#btn_create_new_password').on('click',function(){
				var user_key 					= $('#user_key').val();
				var input_password 				= $('#input_password').val();
				var input_password_confirmation = $('#input_password_confirmation').val();
				if(input_password == ""){
					$('#input_password').effect('highlight');
					$('#input_password').parent().effect('bounce');
					$('#input_password').effect('highlight');
				}
				else if(input_password_confirmation == ""){
					$('#input_password_confirmation').effect('highlight');
					$('#input_password_confirmation').parent().effect('bounce');
					$('#input_password_confirmation').effect('highlight');
				}
				else if (input_password != input_password_confirmation){
					msg_passwords_dont_match
					$('#msg_passwords_dont_match').show('clip',function(){
						$('#input_password').effect('highlight');
					});
					$('#input_password_confirmation').effect('highlight');
					$('#msg_passwords_dont_match').effect('bounce');
				}
				else if (input_password == input_password_confirmation){
					$.ajax({
						url: 'ajax.php?action=create_new_password',
						type: 'POST',
						dataType: 'html',
						data: {
							'input_password' 				: input_password,
							'input_password_confirmation' 	: input_password_confirmation,
							'user_key' 						: user_key
						},
						success: function(response) {
							$("#password_recovery").hide('slide',600, function(){
								$("#retrieve_password_section").show('slide', 600);
							});
						},
						error: function (xhr, ajaxOptions, thrownError) {
							alert(xhr.status);
							alert(thrownError);
						}
					});
					
				}
			})
			$('#btn_back_to_login').on('click', function(){
				window.open("/login.php", "_self");
			})
		</script>
		<?php
			include("includes/footer.php"); 
		?>

	</body>
</html>
<?php 

?>