<?php 
	$employeeStatus = explode( "," , getEmployeeStatus());
	$employeeSupervizor = explode( "," , getEmployeeSupervizor());
?>

<nav class="navbar navbar-expand-md navbar-dark bg-dark mb-2 shadow jobStepBGColor">
	<div class="container-fluid">
		<a class="navbar-brand" href="#">
			<img class = "navbarLogo"src="<?php getSiteUrl(); ?>jobstep_qc/images/ea2.png">
		</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
			<!--<span class="navbar-toggler-icon"></span>-->
			<span class="material-icons">menu</span>
		</button>
		<div class="collapse navbar-collapse" id="navbarCollapse">
			<ul class="navbar-nav me-auto mb-2 mb-md-0">
				<li class="nav-item">
					<a class="nav-link" href="<?php getSiteUrl();?>jobstep_qc/index.php">Home</a>
				</li>
				<?php 
					if(in_array("16", $employeeStatus) OR in_array("16", $employeeSupervizor) OR in_array("1", $employeeStatus)){
				?>
				<li class="nav-item">
					<a class="nav-link" href="<?php getSiteUrl();?>jobstep_qc/documentProcessing.php">Dokumenti</a>
				</li>
				<?php 
					}
					if(in_array("2", $employeeStatus) OR in_array("2", $employeeSupervizor) OR in_array("3", $employeeStatus) OR in_array("3", $employeeSupervizor) OR in_array("1", $employeeStatus)){
				?>
				<li class="nav-item">
					<a class="nav-link" href="<?php getSiteUrl();?>jobstep_qc/profileCandidates.php">Kandidati posao</a>
				</li>
				<?php 
					}
				?>
				<!--<li class="nav-item fontBold">
					<a class="nav-link" href="<?php //getSiteUrl();?>/jobstep_qc/ljudski_resursi.php">Human Resources</a>
				</li>-->
				<li class="nav-item fontBold">
					<a class="nav-link" href="<?php getSiteUrl();?>jobstep_qc/do.php?page=logOut">Log Out</a>
				</li>
				<!-- <li class="nav-item dropdown">
					<a href="#" class="nav-link" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
						Settings <span class="material-icons ms-1">arrow_drop_down</span>
					</a>
					<ul class="dropdown-menu text-small" aria-labelledby="dropdownUser1">
						<li><a class="dropdown-item" href="#">New project...</a></li>
						<li><a class="dropdown-item" href="#">Settings</a></li>
						<li><a class="dropdown-item" href="#">Profile</a></li>
						<li><hr class="dropdown-divider"></li>
						<li><a class="dropdown-item" href="<?php //getSiteUrl();?>jobstep_qc/do.php?page=logOut">Log out</a></li>
					</ul>
				</li>-->
			</ul>
		</div>
	</div>
</nav>