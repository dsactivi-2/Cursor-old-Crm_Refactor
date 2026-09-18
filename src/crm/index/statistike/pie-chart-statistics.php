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
						
if(isset($_GET['date_one'])){
	$f_from = $_GET['date_one'];

	if (strpos($f_from, 'to') !== false) {
		$split = explode(" to ",$f_from);
		$datum_od = $split[0];
		$datum_do = $split[1];
	}else{
		$datum_od = $f_from;
		$datum_do = $f_from;
	}
	
	$date1_from = date('Y-m-d', strtotime($datum_od." 00:00:00"));
	$date1_to = date('Y-m-d', strtotime($datum_do." 23:59:59"));	
	
}else{
	$date1_from = date('Y-m-d', strtotime("1970-01-01 00:00:00"));
	$date1_to = date('Y-m-d H:i:s');
}
if(isset($_GET['date_two'])){
	$date2 = $_GET['date_two'];

	if (strpos($date2, 'to') !== false) {
		$split = explode(" to ",$date2);
		$datum_od2 = $split[0];
		$datum_do2 = $split[1];
	}else{
		$datum_od2 = $date2;
		$datum_do2 = $date2;
	}
	
	$date2_from = date('Y-m-d', strtotime($datum_od2." 00:00:00"));
	$date2_to = date('Y-m-d', strtotime($datum_do2." 23:59:59"));	
	
}else{
	$date2_from = date('Y-m-d', strtotime("1970-01-01 00:00:00"));
	$date2_to = date('Y-m-d H:i:s');
}

?>


				
<style>
	.mx-2{
		margin-left:1rem;
		margin-right:1rem;
	}
	#date_one{
		display: none;
	}
	#date_two{
		display: none;
	}
</style>


<div class="container-fluid">
	<div class="row">
		<div class="col-md-4 col-xs-12">
			<div class="idk_box idk_box_shadow">	
				<div class="flatpickr_one calendar_area">
					<input type="text" class="form-control" name="date_one" id="date_one" data-input ></input>
					<a class="input-button" title="toggle" data-toggle><i class="fa fa-calendar" style="font-size: 21px;" aria-hidden="true"></i></a>
				</div>
				
				<div id="canvas-holder" style="width:100%">
					<canvas id="chart-area"></canvas>
				</div>
			</div>
		</div>
		<div class="col-md-4 col-xs-12">
			<div class="idk_box idk_box_shadow">
				<div class="flatpickr_two calendar_area">
					<input type="text" class="form-control" name="date_two" id="date_two" data-input ></input>
					<a class="input-button" title="toggle" data-toggle><i class="fa fa-calendar" style="font-size: 21px;" aria-hidden="true"></i></a>
				</div>
				
				<div id="canvas-holder" style="width:100%">
					<canvas id="chart-area2"></canvas>
				</div>
			</div>
		</div>
		<div class="col-md-4 col-xs-12">
			<div class="idk_box idk_box_shadow">
				<div id="canvas-holder" style="width:100%">
					<canvas id="chart-area3"></canvas>
				</div>
			</div>
		</div>
		<script>
			$(".flatpickr_one").flatpickr({
				mode: "range",
				dateFormat: "d.m.Y",
				disableMobile: "true",
				onClose: function(selectedDates, dateStr, instance) {
					
					var from = (selectedDates[0].getDate()) + "." + (selectedDates[0].getMonth() + 1) + "." + selectedDates[0].getFullYear(); 
					var tooo = (selectedDates[1].getDate()) + "." + (selectedDates[1].getMonth() + 1) + "." + selectedDates[1].getFullYear(); 
					$("#date_one").val(from+tooo);
					location.href = "https://crm.job-step.com/index?date_one="+from+"+to+"+tooo;
					
				}
			});	
			$(".flatpickr_two").flatpickr({
				mode: "range",
				dateFormat: "d.m.Y",
				disableMobile: "true",
				onClose: function(selectedDates, dateStr, instance) {
					
					var from2 = (selectedDates[0].getDate()) + "." + (selectedDates[0].getMonth() + 1) + "." + selectedDates[0].getFullYear(); 
					var tooo2 = (selectedDates[1].getDate()) + "." + (selectedDates[1].getMonth() + 1) + "." + selectedDates[1].getFullYear(); 
					$("#date_two").val(from2+tooo2);
					location.href = "https://crm.job-step.com/index?date_two="+from2+"+to+"+tooo2;
					
				}
			});						
		</script>
		<?php
		$status_na_provjeri = 0;
		$status_u_obradi = 1;
		$status_obradjen = 2;
		$status_arhiviran = 3;
		$status_kontrola = 4;
		$status_dopuna = 5;
		$status_u_obradi_3 = 7;
		$status_u_dopuni_3 = 8;
		
		$mes_status_cekanje_instalacije = 1;
		$mes_status_odbio_instalirati = 3;
		
		$br_mes_npr = getCandidatesPerMessengerStatus($mes_status_cekanje_instalacije, $date2_from, $date2_to);
		$br_mes_odb = getCandidatesPerMessengerStatus($mes_status_odbio_instalirati, $date2_from, $date2_to);
		$br_mes_u_o_3 = getNezavrseni($status_u_obradi_3, $date2_from, $date2_to);
		$br_mes_n_d_3 = getNezavrseni($status_u_dopuni_3, $date2_from, $date2_to);
		$br_mes_u_o = getCandidatesPerStatusWithMessenger($status_u_obradi, $date2_from, $date2_to);
		$br_mes_dop = getCandidatesPerStatusWithMessenger($status_dopuna, $date2_from, $date2_to);
		$br_mes_kon = getCandidatesPerStatusWithMessenger($status_kontrola, $date2_from, $date2_to);
		$br_mes_obr = getCandidatesPerStatusWithMessenger($status_obradjen, $date2_from, $date2_to);
		$br_mes_instaliran = $br_mes_u_o_3 + $br_mes_n_d_3 + $br_mes_u_o + $br_mes_dop + $br_mes_kon + $br_mes_obr;
		$br_mes_total = $br_mes_npr + $br_mes_odb + $br_mes_instaliran;
		
		$procent_mes_u_o_3 = number_format((($br_mes_u_o_3 / $br_mes_instaliran)*100), 2, ',', '');
		$procent_mes_n_d_3 = number_format((($br_mes_n_d_3 / $br_mes_instaliran)*100), 2, ',', '');
		$procent_mes_npr = number_format((($br_mes_npr / $br_mes_total)*100), 2, ',', '');
		$procent_mes_odb = number_format((($br_mes_odb / $br_mes_total)*100), 2, ',', '');
		$procent_mes_u_o = number_format((($br_mes_u_o / $br_mes_instaliran)*100), 2, ',', '');
		$procent_mes_dop = number_format((($br_mes_dop / $br_mes_instaliran)*100), 2, ',', '');
		$procent_mes_kon = number_format((($br_mes_kon / $br_mes_instaliran)*100), 2, ',', '');
		$procent_mes_obr = number_format((($br_mes_obr / $br_mes_instaliran)*100), 2, ',', '');
		$procent_mes_instaliran = number_format((($br_mes_instaliran / $br_mes_total)*100), 2, ',', '');

		?>
		<script>
			window.onload = function() {
				var windowsize = $(window).width();
				var position_legend = 'left';
				var position_graph = 'right';
				var aspectRatioForDesktop = true;
				
				var ctx2 = document.getElementById('chart-area2').getContext('2d');
				//window.myDoughnut2 = new Chart(ctx2, config2);
				var ctx = document.getElementById('chart-area').getContext('2d');
				//window.myDoughnut = new Chart(ctx, config);
				var ctx3 = document.getElementById('chart-area3').getContext('2d');
				//window.myDoughnut2 = new Chart(ctx2, config2);
				if (windowsize < 768) {
					var position_legend = 'top';
					var position_graph = 'bottom';
					// ctx.height = 520;
					// ctx2.height = 520;
					$('#chart-area').css('height', '520');
					$('#chart-area2').css('height', '520');
					$('#chart-area3').css('height', '520');
				}else{
					// ctx.height = 400;
					// ctx2.height = 400;
					$('#chart-area').css('height', '350');
					$('#chart-area2').css('height', '350');
					$('#chart-area3').css('height', '350');
				}
				window.chartColors = {
					red: 'rgb(243, 65, 60)',
					orange: 'rgb(255, 159, 64)',
					yellow: 'rgb(255, 205, 86)',
					green: 'rgb(102, 213, 102)',
					blue: 'rgb(54, 162, 235)',
					purple: 'rgb(153, 102, 255)',
					grey: 'rgb(201, 203, 207)',
					light_blue: 'rgb(139,218,242)'
				};	
			
				var randomScalingFactor = function() {
					return Math.round(Math.random() * 100);
				};
		
				window.myDoughnut = new Chart(ctx, {
					type: 'doughnut',
					data: {
						datasets: [{
							data: [
								<?php echo getEmployeesPerStatusDate($status_arhiviran, $date1_from, $date1_to); ?>,
								<?php echo getEmployeesPerStatusDate($status_na_provjeri, $date1_from, $date1_to); ?>,
								<?php echo (getEmployeesPerStatusDate($status_u_obradi, $date1_from, $date1_to) + getEmployeesPerStatusDate($status_dopuna, $date1_from, $date1_to)); ?>,
								<?php echo getEmployeesPerStatusDate($status_obradjen, $date1_from, $date1_to); ?>,
								<?php echo getEmployeesPerStatusDate($status_kontrola, $date1_from, $date1_to); ?>,
							],
							backgroundColor: [
								window.chartColors.red,
								window.chartColors.orange,
								window.chartColors.blue,
								window.chartColors.green,
								window.chartColors.light_blue
							],
							label: 'Dataset 1'
						}],
						labels: [
							'Arhiviran (<?php echo getEmployeesPerStatusDate($status_arhiviran, $date1_from, $date1_to); ?>)',
							'Na provjeri (<?php echo getEmployeesPerStatusDate($status_na_provjeri, $date1_from, $date1_to); ?>)',
							'U obradi (<?php echo (getEmployeesPerStatusDate($status_u_obradi, $date1_from, $date1_to) + getEmployeesPerStatusDate($status_dopuna, $date1_from, $date1_to)) ; ?>)',
							'Obrađen (<?php echo getEmployeesPerStatusDate($status_obradjen, $date1_from, $date1_to); ?>)',
							'Kontrola (<?php echo getEmployeesPerStatusDate($status_kontrola, $date1_from, $date1_to); ?>)'
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
							text: 'Statistika svih kandidata'
						},
						animation: {
							animateScale: true,
							animateRotate: true
						}
					}
				});
				
				
				window.myDoughnut2 = new Chart(ctx2, {
					type: 'doughnut',
					data: {
						datasets: [{
							data: [
								<?php echo $br_mes_instaliran; ?>,
								<?php echo getCandidatesPerMessengerStatus($mes_status_cekanje_instalacije, $date2_from, $date2_to); ?>,
								<?php echo getCandidatesPerMessengerStatus($mes_status_odbio_instalirati, $date2_from, $date2_to); ?>
							],
							backgroundColor: [
								window.chartColors.green,
								window.chartColors.orange,
								window.chartColors.red
							],
							label: 'Dataset 1'
						}],
						labels: [
							'Instaliran(<?php echo $br_mes_instaliran; ?>) - <?php echo $procent_mes_instaliran; ?> %',
							'Na Čekanju(<?php echo getCandidatesPerMessengerStatus($mes_status_cekanje_instalacije, $date2_from, $date2_to);?>) - <?php echo $procent_mes_npr; ?> %',
							'Odbijen(<?php echo getCandidatesPerMessengerStatus($mes_status_odbio_instalirati, $date2_from, $date2_to);?>) - <?php echo $procent_mes_odb; ?> %'
						]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						position: position_graph,
						legend: {
							position: position_legend,
							align: 'start'
						},
						title: {
							display: true,
							text: 'Statistika instalacija za messenger - Total (<?php echo $br_mes_total;?>)'
						},
						animation: {
							animateScale: true,
							animateRotate: true
						}
					}
				});
				window.myDoughnut3 = new Chart(ctx3, {
					type: 'doughnut',
					data: {
						datasets: [{
							data: [
								<?php echo getCandidatesPerStatusWithMessenger($status_obradjen, $date2_from, $date2_to); ?>,
								<?php echo getNezavrseni($status_u_obradi_3, $date2_from, $date2_to); ?>,
								<?php echo getNezavrseni($status_u_dopuni_3, $date2_from, $date2_to); ?>,
								<?php echo getCandidatesPerStatusWithMessenger($status_u_obradi, $date2_from, $date2_to); ?>,
								<?php echo getCandidatesPerStatusWithMessenger($status_dopuna, $date2_from, $date2_to); ?>,
								<?php echo getCandidatesPerStatusWithMessenger($status_kontrola, $date2_from, $date2_to); ?>
							],
							backgroundColor: [
								window.chartColors.green,
								window.chartColors.yellow,
								window.chartColors.orange,
								window.chartColors.blue,
								window.chartColors.purple,
								window.chartColors.light_blue
							],
							label: 'Dataset 1'
						}],
						labels: [
							'Obrađen (<?php echo getCandidatesPerStatusWithMessenger($status_obradjen, $date2_from, $date2_to);?>) - <?php echo $procent_mes_obr; ?>%',
							'U obradi više od 3 dana (<?php echo getNezavrseni($status_u_obradi_3, $date2_from, $date2_to);?>) - <?php echo $procent_mes_u_o_3; ?>%',
							'Na dopuni više od 3 dana (<?php echo getNezavrseni($status_u_dopuni_3, $date2_from, $date2_to);?>) - <?php echo $procent_mes_n_d_3; ?>%',
							'U obradi (<?php echo getCandidatesPerStatusWithMessenger($status_u_obradi, $date2_from, $date2_to);?>) - <?php echo $procent_mes_u_o; ?>%',
							'Dopuna (<?php echo getCandidatesPerStatusWithMessenger($status_dopuna, $date2_from, $date2_to);?>) - <?php echo $procent_mes_dop; ?>%',
							'Kontrola (<?php echo getCandidatesPerStatusWithMessenger($status_kontrola, $date2_from, $date2_to);?>) - <?php echo $procent_mes_kon; ?>%'
						]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						position: position_graph,
						legend: {
							position: position_legend,
						},
						title: {
							display: true,
							text: 'Statistika obrade za messenger - Total (<?php echo $br_mes_instaliran;?>)'
						},
						animation: {
							animateScale: true,
							animateRotate: true
						}
					}
				});
			};
		</script>
	</div>
</div>