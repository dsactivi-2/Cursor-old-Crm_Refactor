<?php
	include("includes/functions.php");
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>
		<?php
			getTitle();
		?>
	</title>
<meta name="facebook-domain-verification" content="8cerw3utni6tern5oshym5369ii05o" />
	<?php include('includes/head.php'); ?>

</head>
<body id="idk_login">
	<?php
		$color_class = "success";
		$style="";
	?>
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 idk_margin_top50">
				<img class="img-responsive" src="<?php getSiteURL(); ?>images/<?php echo getSubdomainr(); ?>_logo_front.png" />
				<div class="idk_box idk_box_shadow" style="<?php echo $style;?>">
					<h5 style="<?php echo $style;?>">Ulaz za korisnike</h5>
					<?php
						if(isset($_GET['mess'])) {
							$mess = $_GET['mess'];
						}else{
							$mess = 0;
						}

						if($mess == 1){
							echo '<div class="alert material-alert material-alert_success">Uspješno ste se odjavili.</div>';
						}elseif($mess == 2){
							echo '<div class="alert material-alert material-alert_danger">Greška: Email ili lozinka nisu validni!</div>';
						}elseif($mess == 3){
							echo '<div class="alert material-alert material-alert_danger">Greška: Zatražite reset šifre!</div>';
						}elseif($mess == 4){
							echo '<div class="alert material-alert material-alert_success">Uspješno ste promjenili šifru.</div>';
						}elseif($mess == 5){
							echo '<div class="alert material-alert material-alert_danger">Greška: Nije moguće promjeniti šifru! </div>';
						}
					?>
					<form action="<?php getSiteURL(); ?>do.php?form=login" method="post" role="form">
						<div class="form-group">
							<div class="form-group  materail-input-block materail-input-block_<?php echo $color_class;?> materail-input_slide-line">
								<input type="email" name="login_email" id="login_email" class="form-control materail-input" placeholder="Email adresa" required>
								<span class="materail-input-block__line"></span>
							</div>
						</div>
						<div class="form-group">
							<div class="form-group  materail-input-block materail-input-block_<?php echo $color_class;?> materail-input_slide-line">
								<input type="password" name="login_password" class="form-control materail-input" placeholder="Lozinka" required>
								<span class="materail-input-block__line"></span>
							</div>
						</div>
						<div class="main-container__column material-checkbox-group material-checkbox-group_<?php echo $color_class;?>">
							<input type="checkbox" id="checkbox2" name="login_rm" name="checkbox" class="material-checkbox">
							<label class="material-checkbox-group__label" for="checkbox2">Zapamti me</label>
						</div>
						<div class="text-right">
							<a href="<?php getSiteURL(); ?>change_password_request.php" class="change-password-link text" style="margin-right: 10px;  text-decoration: none;">Promjeni lozinku</a>
							<button type="submit" class="btn material-btn material-btn-icon-<?php echo $color_class;?> material-btn_<?php echo $color_class;?> main-container__column"><i class="fa fa-unlock-alt" aria-hidden="true"></i> <span>ULAZ</span></button>
						</div>
					</form>
				</div>
				<footer>
					<?php getCopyright(); ?>
				</footer>
			</div>
		</div>
	</div>
</body>
</html>
