<?php 
header("Access-Control-Allow-Origin: https://job-step.net");
include("includes/functions.php");
include("includes/head.php");
	
	if(!empty($_POST)){
		$ocjena = $_POST['rating-value'];
		$datum = date('Y-m-d H:i:s');
		if(in_array($ocjena,['1','2','3','4','5'])){
			$insert_query = $db->prepare('
								INSERT INTO idk_analitika
									(analitika_rating,analitika_datum)
								VALUES
									(:analitika_rating,:analitika_datum)');	
			$insert_query->execute(array(
				':analitika_rating' => $ocjena,
				':analitika_datum' => $datum
			));
		}
	}
	$stat_od = $_GET['stat_od'];
	if(!empty($stat_od)){
	if (strpos($stat_od, 'to') !== false) {
		$split = explode(" to ",$stat_od);
			$datum_od = $split[0];
			$datum_do = $split[1];
		}else{
			$datum_od = $stat_od;
			$datum_do = $stat_od;
		}
		$stat_od_f_query = date('Y-m-d 00:00:00', strtotime($datum_od));
		$stat_do_f_query = date('Y-m-d 23:00:00', strtotime($datum_do));
		
		$odlicni = getJbNetCustomerRating(5,$stat_od_f_query,$stat_do_f_query);
		$veoma_dobri = getJbNetCustomerRating(4,$stat_od_f_query,$stat_do_f_query);
		$solidni = getJbNetCustomerRating(3,$stat_od_f_query,$stat_do_f_query);
		$losi = getJbNetCustomerRating(2,$stat_od_f_query,$stat_do_f_query);
		$uzasni = getJbNetCustomerRating(1,$stat_od_f_query,$stat_do_f_query);
	}else{
		$odlicni = getJbNetCustomerRating(5,"","");
		$veoma_dobri = getJbNetCustomerRating(4,"","");
		$solidni = getJbNetCustomerRating(3,"","");
		$losi = getJbNetCustomerRating(2,"","");
		$uzasni = getJbNetCustomerRating(1,"","");
	}
	?>
	<div class="container-fluid">
		<form action="#" enctype="multipart/form-data" method="get" accept-charset="utf-8" role="form" class="form-horizontal">
			<div class="col-sm-3">
				<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line">
					<input type="text" class="form-control" name="stat_od" id="stat_od" placeholder="Datum" required>
					<span class="materail-input-block__line"></span>
				</div>
			</div>
			<script>
					$("#stat_od").flatpickr({
					mode: "range",
					dateFormat: "d.m.Y",
					disableMobile: "true"
				});
			</script>
			<div class="col-sm-3">
				<button type="submit" class="btn material-btn material-btn_success main-container__column"><i class="fa fa-search" aria-hidden="true"></i> Filter datuma</button>
			</div>
		</form>
	</div>
	<div class="container-fluid" style="background-color:#fff; padding:2rem;">
		<div class="row">
			<div class="col-md-offset-1 col-md-5">
				<div class="idk_time_box idk_box_shadow" style="min-height:60vh;">
						<h2></h2>
					<div id="canvas-holder" style="width:100%;">
						<canvas id="chart-area"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
		window.onload = function() {
		var windowsize = $(window).width();
		var position_legend = 'left';
		var position_graph = 'right';
		var aspectRatioForDesktop = true;
		var ctx = document.getElementById('chart-area').getContext('2d');
		if (windowsize < 768) {
			var position_legend = 'top';
			var position_graph = 'bottom';
			// ctx.height = 520;
			$('#chart-area').css('height', '520');
		}else{
			// ctx.height = 400;
			$('#chart-area').css('height', '350');
		}
		window.chartColors = {
			blue: 'rgb(54, 162, 235)',
			green: 'rgb(102, 213, 102)',
			yellow: 'rgb(255,255,0)',
			orange: 'rgb(255, 165, 0)',
			red: 'rgb(243, 65, 60)'
		};

		var randomScalingFactor = function() {
			return Math.round(Math.random() * 100);
		};
		window.myDoughnut = new Chart(ctx, {
				type: 'doughnut',
				data: {
				datasets: [{
				data: [
					<?php echo $odlicni; ?>,
					<?php echo $veoma_dobri; ?>,
					<?php echo $solidni; ?>,
					<?php echo $losi; ?>,
					<?php echo $uzasni; ?>
				],
				backgroundColor: [
					window.chartColors.blue,
					window.chartColors.green,
					window.chartColors.yellow,
					window.chartColors.orange,
					window.chartColors.red
				],
				label: 'Dataset'
				}],
				labels: [
					'Odlicni (<?php echo $odlicni; ?>)',
					'Veoma dobr (<?php echo $veoma_dobri; ?>)',
					'Solidni (<?php echo $solidni; ?>)',
					'Losi (<?php echo $losi; ?>)',
					'Nezadovoljavajući (<?php echo $uzasni; ?>)',
				]
				},
				options: {
					responsive: true,
					position: position_graph,
					maintainAspectRatio: false,
					legend: {
						position: position_legend,
						align: 'start'
					},
					title: {
						display: true,
						text: 'Ocjene korisnika Job-step.net/de stranica'
					},
					animation: {
						animateScale: true,
						animateRotate: true
					}
				}
			});
		};
		</script>
