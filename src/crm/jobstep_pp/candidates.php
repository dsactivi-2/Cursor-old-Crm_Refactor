<?php 
	include("includes/function.php");
	
	$isLoggedIn = isLoggedInR();
	
	if($isLoggedIn == 1){
		if(isset($_REQUEST["userType"])){
			$user_type = intval($_REQUEST["userType"]); 
			// vrijednosti su: 
			//					1 - superadmin;
			//					2 - admin;
		}
		if(isset($_REQUEST["actionFlag"])){
			$action_flag = intval($_REQUEST["actionFlag"]);
			// vrijednosti su: 
			//					1 - dozvoljene akcije;
			//					0 - nisu dozvoljene akcije;
		}
		if(isset($_REQUEST["menu"])){
			$menu = intval($_REQUEST["menu"]); 
			// ako je: 
			//			userType -> 1 
			//			vrijednosti su: 
			//							1 - obradjen; 
			//							2 - casting; 
			//			userType -> 2 
			//			vrijednosti su:
			//							1 - Čeka ugovor; 
			//							2 - Poslan ugovor kandidatu; 
			//							3 - Potpisan ugovor; 
			//							4 - Početak rada; 
			//							5 - Završen;
		}
		if(isset($_REQUEST["nalogID"])){
			$nalog_id = intval($_REQUEST["nalogID"]);
			// vrijednost naloga 
		}
		if(isset($_REQUEST["partnerID"])){
			$partner_id = intval($_REQUEST["partnerID"]);
			//vrijednosti su: 
			//					0 - radi se o pristupu za superAdmina i partnerId tada nije bitan niti ga imamo
			//					N - radi se o pristupu gdje je bitan partnerId (N > 0)
		}
		
		//echo  $user_type. " ". $action_flag . " " . $menu . " " .$nalog_id. " " .$partner_id; 
		//Na osnovu dobijenih vrijednosti praviti odredjene akcije
		
		//NALOG DOSTA KANDIDATA U KASTINGU
		// $action_flag 	= 1;
		// $user_type 		= 1;
		// $nalog_id 		= 216;
		// $menu 			= 2;

		//NALOG SA VOZACKOM DOZVOLOM I RADNIM ISKUSTVOM U KASTINGU
		$action_flag 	= 1;
		$user_type 		= 1;
		$nalog_id 		= 213;
		$menu 			= 2;

		//NALOG 
?>
<!doctype html>
<html class="h-100">
	<head>
		<?php
			include("includes/head.php"); 
		?>
		
	</head>
	<body class="d-flex flex-column h-100">
		<?php
			include("includes/navbar.php"); 
		?>
		<main class="container justify-content-center d-flex h-100 my-2 scrollbar-hidden overflow-auto">
			<div class = "row align-items-center w-100">
				<div class = "col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
					<?php
						include("includes/canvas.php"); 
					?>
					
					<!-- Button trigger modal -->
					<button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
						Kandidati
					</button>
					
					<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" style="display: none;" aria-modal="true">
						<div class="modal-dialog modal-fullscreen">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title h4" id="exampleModalLabel">Pregled profila kandidata</h5>
									<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
								</div>
								<div class="modal-body bg-dark">
									<div class = "justify-content-center d-flex h-100">
										<div class = "row align-items-center w-100">
											<div class = "col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
												<div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
													<div class="carousel-inner text-center">
														<?php 
															$niz = array(
																"kandidat1","kandidat2","kandidat3","kandidat4","kandidat5"
															);
															foreach ($niz as $key => $value) {
																$active = "";
																if($key == 0){
																	$active  = "active";
																}
																echo '
																	<div class="carousel-item '.$active.'">
																		<div class="row">
																			<div class="col-md-6 offset-md-3">
																				<div class="card mb-3">
																					<div class="row g-0">
																						<div class="col-md-4">
																							<img src="https://crm.job-step.com/jobstep_qc/images/ea3.png" class="img-fluid rounded-start" alt="...">
																						</div>
																						<div class="col-md-8">
																							<div class="card-body">
																								<h5 class="card-title">'.$value.'</h5>
																								<p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
																								<p class="card-text"><small class="text-muted">Last updated 3 mins ago '.$value.'</small></p>
																							</div>
																						</div>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																	
																		
																';
															}
														?>
													</div>
													<button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
														<span class="carousel-control-prev-icon" aria-hidden="true"></span>
														<span class="visually-hidden">Previous</span>
													</button>
													<button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
														<span class="carousel-control-next-icon" aria-hidden="true"></span>
														<span class="visually-hidden">Next</span>
													</button>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
								</div>
							</div>
						</div>
					</div>
					<hr>
					<div class="accordion" id="accordionExample">
						<div class="accordion-item">
							<h2 class="accordion-header" id="headingOne">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
									Filter kandidata
								</button>
							</h2>
							<div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
								<div class="accordion-body">
								
									<input id = "action_flag" 	value = "<?php echo $action_flag; ?>" 	style = "disply:none">
									<input id = "partner_id"	value = "<?php echo $partner_id; ?>" 	style = "disply:none">
									<input id = "user_type" 	value = "<?php echo $user_type; ?>" 	style = "disply:none">
									<input id = "nalog_id" 		value = "<?php echo $nalog_id; ?>" 		style = "disply:none">
									<input id = "menu" 			value = "<?php echo $menu; ?>" 			style = "disply:none">
								
									<div class="col-md-4 offset-md-4">
										<form class = "text-center">
											<div class="mb-3">
												<div class="form-group">
													<select class="selectpicker form-control" id = "statusCan" data-live-search="true" multiple data-selected-text-format="count" title="Odaberite status...">
														<option>Mustard</option>
														<option>Ketchup</option>
														<option>Relish</option>
														<option>Onions</option>
													</select>
												</div>
											</div>
											<div class="mb-3">
												<div class="form-group">
													<input type="text" class="form-control" id="rangeDateCan" placeholder="Unesite datum od do...">
												</div>
											</div>
											<script>
												$("#rangeDateCan").flatpickr({
													mode: "range",
													minDate: "today",
													dateFormat: "Y-m-d" 
												});
											</script>
											<button type="submit" class="btn btn-primary">Traži</button>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
					<hr>
					<div class = "row">
						<div class="col-md-4 offset-md-4">
						<canvas id="myChart" style="height:40vh; width:80vw"></canvas>
						</div>
					</div>
					<hr>
					<?php
					/*
					[0] - Da li je vozacka trazena na linku 	[block (jeste) / none (nije)]
					[1] - Koja se vozacka kategorija traži 		[vozacka kategorija / NULL]
					[2] - Koji nivo njemačkog jezika se traži 	[BZ do C2]
					[3] - Da li se traži radno iskustvo 		[1 (traži) / 0 (ne traži)]
					[4] - Da li se traži visoko obrazovanje 	[1 (traži) / 0 (ne trazi)]
					*/
					$kriterij_nalog = array();
					$kriterij_nalog = getNalogKriterij($nalog_id);
					$text_candidates_name_short 			= "Ime kandiadta";
					$text_candidates_poznavanje_jezika 		= "Poznavanje jezika";
					$text_candidates_visoko_obrazvovanje 	= "Visoko obrazovanje";
					$text_candidates_edukacija 				= "Edukacija";
					$text_candidates_vozacka_dozvola 		= "Vozačka dozvola";
					$text_candidates_radno_iskustvo 		= "Radno iskustvo";
					?>
					<table id="table_list_from_projects" class="display" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th class="text-center"><?php echo $text_candidates_name_short; ?></th>
								<th class="text-center"><?php echo $text_candidates_poznavanje_jezika; ?></th>
								<?php if($kriterij_nalog[4] == 1){ ?>
									<th class="text-center"><?php echo $text_candidates_visoko_obrazvovanje; ?></th>									
								<?php } ?>
								<th class="text-center"><?php echo $text_candidates_edukacija; ?></th>
								<?php if($kriterij_nalog[0] == "block"){ ?>
									<th class="text-center"><?php echo $text_candidates_vozacka_dozvola; ?></th>									
								<?php } ?>
								<?php if($kriterij_nalog[3] == 1){ ?>
									<th class="text-center"><?php echo $text_candidates_radno_iskustvo; ?></th>									
								<?php } ?>
								
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
					<script>
						$("#rangeDateCan").flatpickr({
							mode: "range",
							minDate: "today",
							dateFormat: "Y-m-d" 
						});

						const ctx = document.getElementById('myChart').getContext('2d');
						const myChart = new Chart(ctx, {
							type: 'pie',
							data: {
								labels: [
									'Red',
									'Blue',
									'Yellow'
								],
								datasets: [{
									label: 'My First Dataset',
									data: [300, 50, 100],
									backgroundColor: [
									'rgb(255, 99, 132)',
									'rgb(54, 162, 235)',
									'rgb(255, 205, 86)'
									],
									hoverOffset: 4
								}]
							},
							
						});
						
						$(document).ready(function() {
							var menu 			= $('#menu').val();
							var nalog_id 		= $('#nalog_id').val();
							var user_type		= $('#user_type').val();
							var partner_id		= $('#partner_id').val();
							var action_flag		= $('#action_flag').val();
							
							$('#table_list_from_projects').DataTable({
								responsive: true,
								"pageLength": 10,
								"processing": true,
								"serverSide": true,
								"order": [[ 0, "desc" ]],
								"ajax":{
									url :"serverside.php?page=list_from_project",
									type: "POST",
									data: {
										// NOTICE -> DODATI VRIJEDNSTI IZ FILTERA
										'menu' 			: menu,
										'nalog_id' 		: nalog_id,
										'user_type' 	: user_type,
										'partner_id' 	: partner_id,
										'action_flag' 	: action_flag
									},
									error: function(data){
										// $(".list-grid-error").html(""); 
										// $("#list-grid_processing").css("display","none");
									},
								}
							});	
						});
					</script>
					<hr>
					
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
		header("Location:".getSiteUrlr()."landing.php");
	}
?>