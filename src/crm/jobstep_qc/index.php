<?php 
	include("includes/functions.php");
	
	$isLoggedIn = isLoggedIn();
	
	if($isLoggedIn == 1){
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
		?>
		<main class="container justify-content-center d-flex h-100 my-2">
			<div class = "row align-items-center w-100">
				<div class = "col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
					<div class="card border-0 text-center">
						<div class="card-body">
							<h2 class="card-title">Hello, <?php echo getImeZaposlenika($logged_employee_id)."!";?></h2>
							<!--<p class="card-text">Tekst!</p>-->
						</div>
					</div>
				</div>
			</div>
		</main>
		<?php 
			include("includes/footer.php");
		?>
	</body>
</html>
<?php 
	}else{
		header("Location:".getSiteUrlr()."jobstep_qc/landing.php");
	}
?>