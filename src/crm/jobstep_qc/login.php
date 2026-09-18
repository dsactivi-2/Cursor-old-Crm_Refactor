<?php 
	include("includes/functions.php");
	
	$isLoggedIn = isLoggedIn();
	
	if($isLoggedIn == 0){
?>
<!doctype html>
<html lang="en" class="h-100">
	<head>
		<?php
			include("includes/head.php"); 
		?>
	</head>
	<style>
		body{
			background-color: #6197a0;
		}
	</style>
	<body class = "h-100">
		<main class="container justify-content-center d-flex h-100">
			<div class = "row align-items-center w-100">
				<div class = "col-sm-8 offset-sm-2">
					
					<div class="card mb-3 shadow-lg rounded225">
						<div class="card-header whiteBgColor">
							<figure class="figure">
								<img src="<?php echo getSiteUrl(); ?>jobstep_qc/images/ea3.png" class="figure-img img-fluid rounded-circle" alt="...">
							</figure>
						</div>
						<div class="card-body">
							<?php 
								//Poruke
								$isInvalidEmail = "";
								$isInvalidPassword = "";
								if(isset($_GET["mess"])){
							?>
							<script>
								setTimeout(
									function(){
										$(".ah").hide();
									}, 5000
								);
							</script>
							<?php 
									$mess = $_GET["mess"];
									if($mess == 1){
										$isInvalidEmail = "is-invalid";
										echo '
											<div class="time alert alert-danger text-center ah" role="alert">
												Provjerite ispravnost Vaše email adrese!
											</div>
										';
									}else if($mess == 2){
										$isInvalidPassword = "is-invalid";
										echo '
											<div class="time alert alert-danger text-center ah" role="alert">
												Provjerite ispravnost Vaše lozinke!
											</div>
										';
									}else if($mess == 3){
										$isInvalidEmail = "is-invalid";
										$isInvalidPassword = "is-invalid";
										echo '
											<div class="time alert alert-danger text-center ah" role="alert">
												Provjerite ispravnost Vaše email adrese i lozinke!
											</div>
										';
									}else if($mess == 4){
										echo '
											<div class="time alert alert-danger text-center ah" role="alert">
												Korisnički profil je deaktiviran!
											</div>
										';
									}else if($mess == 5){
										echo '
											<div class="time alert alert-info text-center ah" role="alert">
												Uspješno ste se odjavili!
											</div>
										';
									}else{
										echo '
											<div class="time alert alert-danger text-center ah" role="alert">
												Nedefinisana poruka!
											</div>
										';
									}
								}
							?>
							<form action="<?php getSiteUrl(); ?>jobstep_qc/do.php?page=logIn" method="post" role="form">
								<div class="form-floating mb-2">
									<input type="email" class="form-control <?php echo $isInvalidEmail;?> rounded225" id="inputEmail" name="inputEmail" autocomplete = "off" placeholder = "ime_prezime" required>
									<label for="inputEmail" class="">Email adresa</label>
								</div>
								<div class="form-floating mb-2 ">
									<input type="password" class="form-control <?php echo $isInvalidPassword;?> rounded225" id="inputPassword" name="inputPassword" autocomplete = "off" placeholder = "Password" required>
									<label for="inputPassword" class="">Lozinka</label>
								</div>
								<div class="mb-2 form-check">
									<input type="checkbox" class="form-check-input" id="inputCheck" name="inputCheck">
									<label class="form-check-label" for="exampleCheck1">Zapamti me</label>
								</div>
									<button type="submit" class="btn btn-light jobStepBtnColor w-100 rounded225">Ulaz</button>
							</form>
						</div>
						<div class="card-footer whiteBgColor text-black-50 text-center">
							©<?php echo date("Y");?> Sva prava pridržana - Jobstep IT Solutions
						</div>
					</div>
				</div>
			</div>
		</main>
		
	</body>
</html>
<?php 
	}else{
		header("Location:".getSiteUrlr()."/jobstep_qc/index.php");
	}
?>