<?php 
	include("includes/functions.php");
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
					<div class = "row mb-5">
						<div class="col-sm-12">
							<figure class="w-100 text-center">
								<img src="<?php echo getSiteUrl(); ?>jobstep_qc/images/ea3.png" class="figure-img img-fluid rounded-circle" alt="...">
							</figure>
						</div>
					</div>
					<div class = "row mb-5">
						<div class="col-sm-12">
							<a href = "<?php getSiteUrl(); ?>jobstep_qc/login.php" class="btn btn-light jobStepColor w-100 rounded225">Login</a>
						</div>
					</div>
					<div class = "row">
						<div class="col-sm-12">
							<div class="text-white-50 text-center">
								©<?php echo date("Y");?> Sva prava pridržana - Jobstep IT Solutions
							</div>
						</div>
					</div>
				</div>
			</div>
		</main>
	</body>
</html>