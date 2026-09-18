<?php

	/**************************************
	*
	** CREATED 30.08.2021 -- 
	** SYSTEM FOR MANUAL STATUS SWITCHING - FOR AGENTS
	*
	*
	** Last update - Ismail Suljic - Date: 
	*
	*/

?>

<style>
	.my-1{
		margin-top : 1rem;
		margin-bottom : 1rem;
	}
	.my-2{
		margin-top : 2rem;
		margin-bottom : 2rem;
	}
</style>
<div style="position:relative" class="container-fluid">
	<div class="row">
		<div class="col-sm-4">
			<h1><i class="fa fa-angle-double-right idk_color_green" aria-hidden="true"></i> Dobrodošli <?php getEmployeeFullname(); ?></h1>
		</div>
		<div class="col-sm-8 text-right idk_margin_top10">

		</div>
		<?php
		if(in_array($logged_employee_id,[20,412,294,222,75,134,67,354]))
		{
			?>
			<a style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);" href="/dashboardNaloga/companies.php" class="btn btn-primary btn-lg">Idi na dashboard naloga  <i class="fa fa-arrow-right"></i></a>
			<?php
		}
		?>
	</div>
</div>
<div class="container-fluid">
	<div class="row">
		<div class="col-xs-12">
			<hr style="width:100%">
		</div>
	</div>
</div>
