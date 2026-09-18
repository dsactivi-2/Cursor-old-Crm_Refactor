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
					<h5>Promjeni šifru:</h5>
					<?php
						if(isset($_GET['mess'])) {
							$mess = $_GET['mess'];
						}else{
							$mess = 0;
						}

						if($mess == 1){
							echo '<div class="alert material-alert material-alert_success">Uspješno ste uputili zahtjev za promjenu šifre. Provjerite email.</div>';
						}elseif($mess == 2){
                            echo '<div class="alert material-alert material-alert_danger">Nešto nije uredu. Kontaktirajte administratora.</div>';
                        }
					?>
					<form action="<?php getSiteURL(); ?>do.php?form=user_change_password_request" method="post" role="form">
						<div class="form-group">
							<div class="form-group  materail-input-block materail-input-block_success materail-input_slide-line">
								<input type="email" name="login_email" id="login_email" class="form-control materail-input" placeholder="Email adresa" required>
								<span class="materail-input-block__line"></span>
							</div>
						</div>
						<div class="text-right">
							<a href="<?php getSiteURL(); ?>login" class="change-password-link text" style="margin-right: 10px;  text-decoration: none;">Nazad</a>
							<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-unlock-alt" aria-hidden="true"></i> <span>Promjeni</span></button>
						</div>
					</form>
				</div>
				<footer><?php getCopyright(); ?></footer>
			</div>
		</div>
	</div>
</body>
</html>