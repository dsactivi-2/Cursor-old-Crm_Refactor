<?php
	include("includes/functions.php");
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php getTitle(); ?></title>
<meta name="facebook-domain-verification" content="8cerw3utni6tern5oshym5369ii05o" />
	<?php include('includes/head.php'); ?>

</head>
<body id="idk_login">
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 idk_margin_top50">
				<img class="img-responsive" src="<?php getSiteURL(); ?>images/<?php echo getSubdomainr(); ?>_logo_front.png" />
				<div class="idk_box idk_box_shadow">
					<h5>Promjena šifre</h5>
                    <?php

						if(isset($_GET['token'])) {
							$token = $_GET['token'];
                            $isTokenValid = isTokenCorrect($token);

                            if($isTokenValid){
                            ?>
                                <form action="<?php getSiteURL(); ?>do.php?form=change_password" method="post" role="form">
                                    <div class="form-group">
                                        <input type="hidden" value="<?php echo $token; ?>" name="token" />
                                        <div class="form-group materail-input-block materail-input-block_success materail-input_slide-line">
                                            <div class="col-sm-8">
                                                <div class="materail-input-block materail-input-block_success">
                                                    <input class="form-control materail-input" type="text" name="login_password_new" id="login_password_new" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{12,}" placeholder="Lozinka" disabled>
                                                    <input class="form-control materail-input" type="hidden" name="login_password" id="login_password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{12,}" placeholder="Lozinka" required>
                                                    <span class="materail-input-block__line"></span>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <a title="Generiraj novu šifru" id="generate_new_password" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive" style="padding-right: 0px!important;"><i class="fa fa-refresh" style="margin: 0;"></i></a>
                                            </div>
                                            <div class="col-sm-2">
                                                <a title="Kopiraj šifru" onClick='copy_password()' class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive" style="padding-right: 0px!important;"><i class="fa fa-clone" style="margin: 0;"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <a href="<?php getSiteURL(); ?>login.php" class="change-password-link text" style="margin-right: 10px; text-decoration: none;">Vrati se na login</a>
                                        <button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-unlock-alt" aria-hidden="true"></i> <span>PROMJENI</span></button>
                                    </div>
                                </form>
                                <script>
                                    $('#generate_new_password').click(function() {

                                        function generatePassword() {
                                        var length = 12;
                                        var characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_+=<>?';
                                        var password = '';

                                        while (password.length < length || !isPasswordValid(password)) {
                                            password = '';
                                            while (password.length < length) {
                                                var char = characters[Math.floor(Math.random() * characters.length)];
                                                password += char;
                                            }
                                        }

                                        return password;
                                        }

                                        function isPasswordValid(password) {
                                            var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()-_+=<>?]).{8,}$/;
                                            var uniqueCharacters = new Set(password.split('')).size >= 4;

                                            return regex.test(password) && uniqueCharacters;
                                        }

                                        var password = generatePassword();

                                        $('#login_password_new').val(password);
                                        $('#login_password').val(password);
                                        copyToClipboard(password);
                                        alert("Lozinka kopirana.");
                                    });

                                    function copyToClipboard(text) {
                                        navigator.clipboard.writeText(text);
                                    }


                                    function copy_password(){
                                        var copyText = document.getElementById("login_password");
                                        copyText.select();
                                        document.execCommand("copy");
                                        alert("Uspješno kopirana lozinka");
                                    }
                                </script>
                            <?php
                            }else{
                                echo '<div class="alert material-alert material-alert_success">Token nije validan.</div>';
                            }
						}else{
                            echo '<div class="alert material-alert material-alert_success">Token nije postavljen.</div>';
						}
					?>
				</div>
				<footer><?php getCopyright(); ?></footer>
			</div>
		</div>
	</div>
</body>
</html>
