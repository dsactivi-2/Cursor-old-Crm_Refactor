<?php 
	include("includes/function.php");
	
	$isLoggedIn = isLoggedInR();
	
	if($isLoggedIn == 1){
		$languageUser = getLanguageForUser($userId);;
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
		<?php
			include("includes/navbar/navbar.php"); 
		?>
		<div class="container justify-content-center d-flex h-100 my-2 scrollbar-hidden overflow-auto">
			<div class = "row align-items-center w-100">
				<div class = "col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
				<?php
					include("includes/notifications/notifications.php");
					include("includes/user/user_modal.php");
					
				?>
					<main class=  "my-2" id = "listsTable" style = "display: none;">
						<div class = "row">
							<div class = "col-12">
								<div class = "row align-items-center">
									<div class = "col-1 text-center goToDash">
										<i class="fa fa-angle-left fa-3x" aria-hidden="true"></i>
									</div>
									<div class = "col-11 text-center">
										<p class = "text_color mb-0" style = "font-style: normal; font-weight: bold; font-size: 28px;">Naziv liste - nije fiksno</p>
									</div>
								</div>
								<script>
									$( ".goToDash" ).on( "click", function() {
										//Ovdje dodati kod za vracanje prethodnog
										alert("Uradi kod za vraćanje na dash!");
									});
								</script>
								<div class = "row my-3 align-items-center">
									<div class = "col-md-4 offset-md-4 text-center" >
										<p class = "mb-0">Dio za chart</p>
									</div>
								</div>
								<div class = "row mb-3">
									<div class = "col-md-12">
										<div class = "px-5 py-5" style = "box-shadow: 0px 8px 24px rgba(112, 144, 176, 0.15); border-radius: 30px; padding: 25px;">
											<div class = "row">
												<div class = "col-12" style="overflow-y: auto !important;">
													<!-- Tabela START -->
													<script>
														$(document).ready(function() {
															$('#listTable').DataTable();
														});
													</script>
													<table id="listTable" class="hover compact" cellspacing="0" style="width:100%">
														<thead>
															<tr>
																<th>Name</th>
																<th>Position</th>
																<th>Office</th>
																<th>Age</th>
																<th>Salary</th>
															</tr>
														</thead>
														<tbody>
															<tr>
																<td>Tiger Nixon</td>
																<td>System Architect</td>
																<td>Edinburgh</td>
																<td>61</td>
																<td>$320,800</td>
															</tr>
															<tr>
																<td>Cedric Kelly</td>
																<td>Senior Javascript Developer</td>
																<td>Edinburgh</td>
																<td>22</td>
																<td>$433,060</td>
															</tr>
															<tr>
																<td>Sonya Frost</td>
																<td>Software Engineer</td>
																<td>Edinburgh</td>
																<td>23</td>
																<td>$103,600</td>
															</tr>
															<tr>
																<td>Quinn Flynn</td>
																<td>Support Lead</td>
																<td>Edinburgh</td>
																<td>22</td>
																<td>$342,000</td>
															</tr>
															<tr>
																<td>Dai Rios</td>
																<td>Personnel Lead</td>
																<td>Edinburgh</td>
																<td>35</td>
																<td>$217,500</td>
															</tr>
															<tr>
																<td>Gavin Joyce</td>
																<td>Developer</td>
																<td>Edinburgh</td>
																<td>42</td>
																<td>$92,575</td>
															</tr>
															<tr>
																<td>Martena Mccray</td>
																<td>Post-Sales support</td>
																<td>Edinburgh</td>
																<td>46</td>
																<td>$324,050</td>
															</tr>
															<tr>
																<td>Jennifer Acosta</td>
																<td>Junior Javascript Developer</td>
																<td>Edinburgh</td>
																<td>43</td>
																<td>$75,650</td>
															</tr>
															<tr>
																<td>Shad Decker</td>
																<td>Regional Director</td>
																<td>Edinburgh</td>
																<td>51</td>
																<td>$183,000</td>
															</tr>
														</tbody>
														<tfoot>
															<tr>
																<th>Name</th>
																<th>Position</th>
																<th>Office</th>
																<th>Age</th>
																<th>Salary</th>
															</tr>
														</tfoot>
													</table>
													<!-- Tabela END -->
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</main>
					<script>
						$(document).ready(function(){
							$( "#listsTable" ).show( 'slide', 500);
							// $( "#candidateInter" ).hide(function(){
								// $( "#candidateProfile" ).show( 'slide', 500);
							// });
						});
					</script>
				</div>
			</div>
		</div>
		<?php
			include("includes/footer.php"); 
		?>
	</body>
</html>
<?php
		unset($txtArray);
	}else{
		header("Location:".getSiteUrlr()."landing.php");
	}
?>