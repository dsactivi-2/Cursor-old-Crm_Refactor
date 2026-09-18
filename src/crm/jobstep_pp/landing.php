<?php 
	include("includes/function.php");
	
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
	<body class="d-flex flex-column h-100">
		<main class="container justify-content-center d-flex h-100 my-2 scrollbar-hidden overflow-auto">
			<div class = "row align-items-center w-100">
				<div class = "col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
					<div class = "row">
						<div class="col-md-4 offset-md-4">
							<div class = "row mb-5">
								<div class="col-sm-12">
									<figure class="w-100 text-center">
										<img src="images/login_photo1.svg" class="figure-img img-fluid" alt="...">
									</figure>
								</div>
							</div>
							<div class = "row mb-5">
								<div class="col-sm-12 text-center">
									<a href = "<?php getSiteUrl(); ?>login.php" class="login_button"><?php echo $txtArray["Prijava"][$languageUser]; ?></a>
								</div>
							</div>
							<div class = "row">
								<div class="col-sm-12">
									<div class="text-center text-muted">
										©<?php echo date("Y")." ".$txtArray["Sva prava pridržana - Jobstep IT Solutions"][$languageUser];?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
	</body>
</html>

<?php 
	unset($txtArray);
?>