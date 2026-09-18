<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus = explode( ',' , getEmployeeStatus());

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: provizije?page=postavke");
	}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php getTitle(); ?></title>

	<?php include('includes/head.php'); 
	if (in_array($getUserIp, $getIpWhiteList)){
	?>
</head>
<body>
	<header>
		<?php include('header.php'); ?>
	</header>
	<div id="sidebar">
		<?php include('menu.php'); ?>
	</div>
	<div id="content">
		<div class="container-fluid">
		<?php
		switch ($page){

			case "postavke":
		?>
			<div class="row">
				<div class="col-xs-12">
					<h1><i class="fa fa-cogs idk_color_green" aria-hidden="true"></i> Postavke provizija</h1>
				</div>
				<div class="col-xs-12">
					<hr/>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<?php 
						$tip_query = $db->prepare("
											SELECT tp_naziv, tp_iznos, tp_id, tp_iznos_rs
											FROM idk_tipovi_provizija
											WHERE tp_status = 1
											ORDER BY tp_tip ");

						$tip_query->execute();

						while($tip_row = $tip_query->fetch()){

							$tp_id = $tip_row['tp_id'];
							$tp_naziv = $tip_row['tp_naziv'];
							$tp_iznos = $tip_row['tp_iznos'];
							$tp_iznos_rs = $tip_row['tp_iznos_rs'];
							$tp_iznos =  number_format($tp_iznos, 2, ',', '.');
							$tp_iznos_rs =  number_format($tp_iznos_rs, 2, ',', '.');
					?>
					<div class="content_box" style="min-height: 100px;">
						<div class="row">
							<div class="col-xs-6">
								<h5><?php echo $tp_naziv;?></h5>
							</div>
							<div class="col-xs-6 text-right">
								<h4><?php echo $tp_iznos." KM / ".$tp_iznos_rs." RSD";?></h4>
							</div>
							<?php 
								$kat_query = $db->prepare("
													SELECT kp_naziv, kp_iznos, kp_faktorizacija, kp_id, kp_odjeli, kp_employee_id, kp_vrsta, kp_iznos_rs
													FROM idk_kategorije_provizija
													WHERE kp_status = 1 AND kp_tip_id = :kp_tip_id
													ORDER BY kp_id ");

								$kat_query->execute(array(
													":kp_tip_id" => $tp_id
								));

								while($kat_row = $kat_query->fetch()){

									$kp_id = $kat_row['kp_id'];
									$kp_naziv = $kat_row['kp_naziv'];
									$kp_iznos = $kat_row['kp_iznos'];
									$kp_iznos_rs = $kat_row['kp_iznos_rs'];
									$kp_faktorizacija = $kat_row['kp_faktorizacija'];
									$kp_vrsta = $kat_row['kp_vrsta'];
									$kp_employee_id = $kat_row['kp_employee_id'];
									$kp_odjeli = $kat_row['kp_odjeli'];
									$odjeli = explode(",", $kp_odjeli);
									$kp_iznos_f = number_format($kp_iznos, 2, ',', '.');
									$kp_iznos_rs_f = number_format($kp_iznos_rs, 2, ',', '.');
									
									if($kp_vrsta == 3){
										$getName = $db->prepare("
															SELECT CONCAT(employee_firstname, ' ', employee_lastname) AS full_name
															FROM idk_employees
															WHERE employee_id = :employee_id ");

										$getName->execute(array(
															":employee_id" => $kp_employee_id
										));
										$name_row = $getName->fetch();
										$full_name = $name_row["full_name"];
										$ontop_zaposlenik = " (".$full_name.")";
									}else{
										$ontop_zaposlenik = "";
									}
									
									//GET SUMU FAKTORA ZA TU KATEGORIJU
									$faktor_query = $db->prepare("
														SELECT SUM(employee_faktor_provizije_bih) as suma_bih, SUM(employee_faktor_provizije_srb) as suma_srb
														FROM idk_employees
														WHERE employee_odjel IN ($kp_odjeli) AND employee_status != 0 ");

									$faktor_query->execute();
									$faktor_row = $faktor_query->fetch();
									$suma_faktora_bih = $faktor_row['suma_bih'];
									$suma_faktora_srb = $faktor_row['suma_srb'];
									
									//SUMA FAKTORA ZA PROVIZIJE OD TIMOVA
									$faktor_tim_query = $db->prepare("
														SELECT SUM(employee_faktor_za_timove) as suma_tim
														FROM idk_employees
														WHERE employee_status != 0 ");

									$faktor_tim_query->execute();
									$faktor_tim_row = $faktor_tim_query->fetch();
									$suma_faktora_tim = $faktor_tim_row['suma_tim'];
									
							?>
							<div class="col-xs-12">
								<div class="panel-group material-accordion material-accordion_primary" id="accordion1">
									<div class="panel panel-default material-accordion__panel material-accordion__panel">
										<div class="panel-heading material-accordion__heading">
											<h4 class="panel-title">
												<div class="row" style="margin: 0;">
												<a class="material-accordion__title " style="padding: 15px 30px 35px 15px;" data-toggle="collapse" data-parent="#accordion1" href="#<?php echo "kat".$kp_id; ?>">
													<span class="col-xs-9"><?php echo $kp_naziv.$ontop_zaposlenik; ?></span>
												
													<span class="col-xs-3 text-right"><?php echo $kp_iznos_f." KM / ".$kp_iznos_rs_f." RSD"; ?></span>
												</a>
												</div>
											</h4>
										</div>
										<div id="<?php echo "kat".$kp_id; ?>" class="panel-collapse collapse material-accordion__collapse">
											<input type="hidden" name="suma_faktora_bih" id="suma<?php echo $kp_id; ?>" value="<?php echo $suma_faktora_bih; ?>">
											<input type="hidden" name="suma_faktora_srb" id="suma<?php echo $kp_id; ?>" value="<?php echo $suma_faktora_srb; ?>">
											<input type="hidden" name="suma_faktora_tim" id="suma<?php echo $kp_id; ?>" value="<?php echo $suma_faktora_tim; ?>">
											<input type="hidden" name="kp_iznos" id="kp_iznos<?php echo $kp_id; ?>" value="<?php echo $kp_iznos; ?>">
											<input type="hidden" name="kp_iznos_rs" id="kp_iznos_rs<?php echo $kp_id; ?>" value="<?php echo $kp_iznos_rs; ?>">
											<div class="panel-body">
												
											<?php if($kp_vrsta == 2){ ?>
												<table class="table-striped" style="width:100%">
													<thead>
													<th style="width: 10%">Odjel</th>
													<th style="width: 30%">Zaposlenik</th>
													<th style="width: 15%" class="text-right header_faktor_bh">Faktor BiH (<?php echo $suma_faktora_bih; ?>)</th>
													<th style="width: 15%" class="text-right">Iznos BiH</th>
													<th style="width: 15%" class="text-right header_faktor_rs">Faktor SRB (<?php echo $suma_faktora_srb; ?>)</th>
													<th style="width: 15%" class="text-right">Iznos SRB</th>
													</thead>
											<?php
													foreach($odjeli as $odjel_id){
														
														if($odjel_id == 1){
															$employee_odjel = "Uprava";
														}elseif($odjel_id == 2){
															$employee_odjel = "Financije";
														}elseif($odjel_id == 3){
															$employee_odjel = "Prodaja";
														}elseif($odjel_id == 4){
															$employee_odjel = "Obrada";
														}elseif($odjel_id == 5){
															$employee_odjel = "Sve za vizu";
														}elseif($odjel_id == 6){
															$employee_odjel = "Marketing";
														}elseif($odjel_id == 7){
															$employee_odjel = "Tehnika";
														}elseif($odjel_id == 8){
															$employee_odjel = "Development";
														}elseif($odjel_id == 9){
															$employee_odjel = "Ostalo";
														}else{
															$employee_odjel = "-";
														}
														
														$odjeli_query = $db->prepare("
																			SELECT employee_id, employee_firstname, employee_lastname, employee_faktor_provizije_bih, employee_faktor_provizije_srb
																			FROM idk_employees
																			WHERE employee_odjel = :employee_odjel AND employee_status != 0 AND employee_team = 1
																			ORDER BY employee_id ");

														$odjeli_query->execute(array(
																			":employee_odjel" => $odjel_id
														));

														$ct=1;
														
														while($odjeli_row = $odjeli_query->fetch()){

															$employee_id = $odjeli_row['employee_id'];
															$faktor_bih = $odjeli_row['employee_faktor_provizije_bih'];
															$faktor_srb = $odjeli_row['employee_faktor_provizije_srb'];
															$employee_firstname = $odjeli_row['employee_firstname'];
															$employee_lastname = $odjeli_row['employee_lastname'];
															$zaposlenik = $employee_firstname." ".$employee_lastname;
															
															$provizija_zaposlenika_bih = ($kp_iznos * $faktor_bih)/$suma_faktora_bih;
															$provizija_zaposlenika_srb = ($kp_iznos_rs * $faktor_srb)/$suma_faktora_srb;
															$prov_zaposl_bih_f = number_format($provizija_zaposlenika_bih, 2, ',', '.');
															$prov_zaposl_srb_f = number_format($provizija_zaposlenika_srb, 2, ',', '.');
													?>
														<tr  <?php if($ct == 1){echo 'style="border-top: 1px solid;"';}?> >
															<td>
																<?php if($ct == 1){echo $employee_odjel;}$ct++;?>
															</td>
															<td>
																<?php echo $zaposlenik; ?>
															</td>
															<td class="text-right">
																<input class="form-control materail-input" style="width: 30%; float: right;" type="number" name="bh_faktor" id="bh_faktor_<?php echo $employee_id; ?>" value="<?php echo $faktor_bih; ?>">
															</td>
															<td class="text-right iznos_provizije_bh bh_provizija_<?php echo $employee_id?>">
																<?php echo $prov_zaposl_bih_f." KM"; ?>
															</td>
															<td class="text-right">
																<input class="form-control materail-input" style="width: 30%; float: right;" type="number" name="rs_faktor" id="rs_faktor_<?php echo $employee_id; ?>" value="<?php echo $faktor_srb; ?>">
															</td>
															<td class="text-right iznos_provizije_rs rs_provizija_<?php echo $employee_id?>">
																<?php echo $prov_zaposl_srb_f." RSD"; ?>
															</td>
														
														
														<?php 
														}
														?>
														</tr>
														
														<?php
													}
													?>
												</table>
													<button class="btn btn-primary material-btn material-btn_success spremi">SPREMI</button>
													<?php
												}elseif($kp_vrsta == 4){
												?>
												<table class="table-striped" style="width:100%">
													<thead>
													<th style="width: 10%">Odjel</th>
													<th style="width: 30%">Zaposlenik</th>
													<th style="width: 15%" class="text-right ">Faktor BiH</th>
													<th style="width: 15%" class="text-right">Iznos BiH</th>
													<th style="width: 15%" class="text-right ">Faktor SRB</th>
													<th style="width: 15%" class="text-right">Iznos SRB</th>
													</thead>
											<?php
													$emp_query = $db->prepare("
																		SELECT employee_id, employee_firstname, employee_lastname, employee_faktor_za_timove, employee_odjel
																		FROM idk_employees
																		WHERE employee_status != 0 AND employee_team = 1 AND employee_faktor_za_timove > 0
																		ORDER BY employee_odjel,employee_id ");

													$emp_query->execute();
													
													while($emp_row = $emp_query->fetch()){

														$employee_id = $emp_row['employee_id'];
														$employee_faktor_za_timove = $emp_row['employee_faktor_za_timove'];
														$odjel_id = $emp_row['employee_odjel'];
														$employee_firstname = $emp_row['employee_firstname'];
														$employee_lastname = $emp_row['employee_lastname'];
														$zaposlenik = $employee_firstname." ".$employee_lastname;
														
														if($odjel_id == 1){
														$employee_odjel = "Uprava";
														}elseif($odjel_id == 2){
															$employee_odjel = "Financije";
														}elseif($odjel_id == 3){
															$employee_odjel = "Prodaja";
														}elseif($odjel_id == 4){
															$employee_odjel = "Obrada";
														}elseif($odjel_id == 5){
															$employee_odjel = "Sve za vizu";
														}elseif($odjel_id == 6){
															$employee_odjel = "Marketing";
														}elseif($odjel_id == 7){
															$employee_odjel = "Tehnika";
														}elseif($odjel_id == 8){
															$employee_odjel = "Development";
														}elseif($odjel_id == 9){
															$employee_odjel = "Ostalo";
														}else{
															$employee_odjel = "-";
														}
														
														$provizija_zaposlenika_bih_tim = ($kp_iznos * $employee_faktor_za_timove)/$suma_faktora_tim;
														$provizija_zaposlenika_srb_tim = ($kp_iznos_rs * $employee_faktor_za_timove)/$suma_faktora_tim;
														$prov_zaposl_bih_tim_f = number_format($provizija_zaposlenika_bih_tim, 2, ',', '.');
														$prov_zaposl_srb_tim_f = number_format($provizija_zaposlenika_srb_tim, 2, ',', '.');
													?>
														<tr>
															<td>
																<?php echo $employee_odjel;?>
															</td>
															<td>
																<?php echo $zaposlenik; ?>
															</td>
															<td class="text-right">
																<input class="form-control materail-input" style="width: 30%; float: right;" type="number" name="bh_faktor_tim" id="bh_faktor_tim_<?php echo $employee_id; ?>" value="<?php echo $employee_faktor_za_timove; ?>">
															</td>
															<td class="text-right iznos_provizije_bh bh_provizija_<?php echo $employee_id?>">
																<?php echo $prov_zaposl_bih_tim_f." KM"; ?>
															</td>
															<td class="text-right">
																<input class="form-control materail-input" style="width: 30%; float: right;" type="number" name="rs_faktor_tim" id="rs_faktor_tim_<?php echo $employee_id; ?>" value="<?php echo $employee_faktor_za_timove; ?>">
															</td>
															<td class="text-right iznos_provizije_rs rs_provizija_<?php echo $employee_id?>">
																<?php echo $prov_zaposl_srb_tim_f." RSD"; ?>
															</td>
														
														
														<?php 
														}
														?>
														</tr>
												</table>
												<?php } ?>
											</div>
										</div>
									</div>
								</div>
							</div>
							<?php
								}
							?>
						</div>
					</div>
					<hr/>
					<?php
						}
					?>
					<script>
						$(document).on('click', '.spremi', function () {
                                
								var kat_id = $(this).parent().parent().attr('id');
								var values = new Array();
								var values_rs = new Array();
								var emp_ids = new Array();
								var emp_ids_rs = new Array();
								
								$("#"+kat_id+" input[name='bh_faktor']").each(function( i ) {
									var value_inputa = $(this).val();
									var input_id = $(this).attr('id');
									var duzina = input_id.length;
									var emp_id = input_id.substring(10, duzina);
									values.push(value_inputa);
									emp_ids.push(emp_id);
									//console.log(emp_id+': '+value_inputa+' -- '+duzina);
								});
								$("#"+kat_id+" input[name='rs_faktor']").each(function( i ) {
									var value_inputa_rs = $(this).val();
									var input_id_rs = $(this).attr('id');
									var duzina = input_id_rs.length;
									var emp_id_rs = input_id_rs.substring(10, duzina);
									values_rs.push(value_inputa_rs);
									emp_ids_rs.push(emp_id_rs);
								});
								$.ajax({
									url: "<?php getSiteUrl();?>ajax.php?page=update_faktor",
									type: 'POST',
									data: {'emp_ids': emp_ids, 'values': values, 'emp_ids_rs': emp_ids_rs,'values_rs': values_rs},
									success: function(data) {
										window.location.reload();
									}
								});
								
                            });
						
						$('input[name="bh_faktor"]').focus(function(){
							stara_vrijednost = parseInt($(this).val());
							var id_inputa = $(this).attr('id');
							var duzina = id_inputa.length;
							var res = id_inputa.substring(10, duzina);
							
							var table1 = $(this).parent().parent().parent().parent();
							var divKat = $(this).parent().parent().parent().parent().parent().parent();
							var id_kat = divKat.attr('id');
							var suma = parseInt(divKat.find('input[name="suma_faktora_bih"]').val());
							var kp_iznos = parseInt(divKat.find('input[name="kp_iznos"]').val());
							//console.log(stara_vrijednost);
							$('input[id="'+id_inputa+'"]').change(function(){
								nova_vrijednost = parseInt($(this).val());
								nova_suma = parseInt(suma + nova_vrijednost - stara_vrijednost);
								table1.find('.header_faktor_bh').text('Faktor BiH ('+nova_suma+')');
								//set input value za sumu
								divKat.find('input[name="suma_faktora_bih"]').val(nova_suma);
								// console.log("nova vr: "+nova_vrijednost);
								// console.log("nova suma: "+nova_suma);
								
								nova_provizija = parseFloat((kp_iznos * nova_vrijednost)/nova_suma);
								nova_provizija_f = parseFloat(nova_provizija).toFixed(2)
								// console.log(nova_provizija_f);
								// console.log($(this).find('.bh_provizija_'+res+''));
								$(this).parent().parent().find('.bh_provizija_'+res+'').text(nova_provizija_f+' KM');
								
								$("#"+id_kat+" .iznos_provizije_bh").each(function( i ) {
									var faktor_each = $(this).parent().find('input[name="bh_faktor"]').val();
									//console.log(faktor_each);
									each_provizija = parseFloat((kp_iznos * faktor_each)/nova_suma);
									each_provizija_f = parseFloat(each_provizija).toFixed(2);
									$(this).text(each_provizija_f+' KM');
								});
							});
						});
						$('input[name="rs_faktor"]').focus(function(){
							stara_vrijednost = parseInt($(this).val());
							var id_inputa = $(this).attr('id');
							var duzina = id_inputa.length;
							var res = id_inputa.substring(10, duzina);
							
							var table = $(this).parent().parent().parent().parent();
							var divKat = $(this).parent().parent().parent().parent().parent().parent();
							var id_kat = divKat.attr('id');
							var suma = parseInt(divKat.find('input[name="suma_faktora_srb"]').val());
							var kp_iznos_rs = parseInt(divKat.find('input[name="kp_iznos_rs"]').val());
							//console.log(stara_vrijednost);
							$('input[id="'+id_inputa+'"]').change(function(){
								nova_vrijednost = parseInt($(this).val());
								nova_suma = parseInt(suma + nova_vrijednost - stara_vrijednost);
								table.find('.header_faktor_rs').text('Faktor SRB ('+nova_suma+')');
								//set input value za sumu
								divKat.find('input[name="suma_faktora_srb"]').val(nova_suma);
								// console.log("nova vr: "+nova_vrijednost);
								// console.log("nova suma: "+nova_suma);
								
								nova_provizija = parseFloat((kp_iznos_rs * nova_vrijednost)/nova_suma);
								nova_provizija_f = parseFloat(nova_provizija).toFixed(2)
								// console.log(nova_provizija_f);
								// console.log($(this).find('.rs_provizija_'+res+''));
								$(this).parent().parent().find('.rs_provizija_'+res+'').text(nova_provizija_f+' KM');
								
								$("#"+id_kat+" .iznos_provizije_rs").each(function( i ) {
									var faktor_each = $(this).parent().find('input[name="rs_faktor"]').val();
									//console.log(faktor_each);
									each_provizija = parseFloat((kp_iznos_rs * faktor_each)/nova_suma);
									each_provizija_f = parseFloat(each_provizija).toFixed(2);
									$(this).text(each_provizija_f+' RSD');
								});
							});
						});
					</script>
				</div>
			</div>
		<?php
			break;
			
			case "lista":
			
				if(isset($_REQUEST['team']))
					$team = $_REQUEST['team'];
				else
					$team = "1";
				
				$f_from = $_POST['f_from'];
				
				if (strpos($f_from, 'to') !== false) {
					$split = explode(" to ",$f_from);
					$datum_od = $split[0];
					$datum_do = $split[1];
					$isplata_txt = '<a href="#" id="isplata_export" class="btn btn-primary material-btn material-btn_success"><i class="fa fa-money" aria-hidden="true"></i> Isplati za sve označene <i class="fa fa-money" aria-hidden="true"></i></a>';
					//$isplata_txt = ''; // gornja linija komentarisana kako bi se trenutno onemogucila isplata Azri dok se rucno ne promijene pogresne provizije
				}else{
					$datum_od = "2021-01-01 00:00:00";
					$datum_do = date("Y-m-d H:i:s");
					$isplata_txt = '<a href="#" class="btn btn-danger material-btn material-btn_danger" disabled title="Potrebno je odabrati period za isplatu!"><i class="fa fa-money" aria-hidden="true"></i> Isplati za sve označene <i class="fa fa-money" aria-hidden="true"></i></a>';
				}
				
				$f_from_f = date('Y-m-d 00:00:00', strtotime($datum_od));
				$f_to_f = date('Y-m-d 23:59:59', strtotime($datum_do));	
				
				// FOR FLATPICKR
				$f_from_flat = date('d.m.Y', strtotime($datum_od));
				$f_to_flat = date('d.m.Y', strtotime($datum_do));	
					
			?>
				<div class="row">
					<div class="col-xs-12">
						<h1><i class="fa fa-tasks idk_color_green" aria-hidden="true"></i> Lista provizija</h1>
					</div>
					<div class="col-xs-12">
						<hr/>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="content_box">
							<div class="row">
								<form action="<?php getSiteUrl(); ?>provizije?page=lista" enctype="multipart/form-data" method="post" accept-charset="utf-8" role="form" class="form-horizontal">
								<div class="col-sm-3">
									<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line">
										<input type="text" class="form-control" name="f_from" id="f_from" placeholder="Datum"  required>
									</div>
								</div>
								<div class="col-sm-1">
									<button style="width:100%" class="btn btn-success">Traži</button>
								</div>
								<div class="col-sm-2">
									<a href="#" id="obicni_export" class="btn btn-primary material-btn material-btn_success"><i class="fa fa-file-o" aria-hidden="true"></i> Export</a>
								</div>
								<?php if($logged_employee_id == 67 OR $logged_employee_id == 50 OR $logged_employee_id == 208 OR $logged_employee_id == 223){ ?>
								<div class="col-sm-2">
									<?php echo $isplata_txt; ?>
								</div>
								<?php } ?>
								</form>
							</div>
							<script>
								$("#f_from").flatpickr({
									mode: "range",
									dateFormat: "d.m.Y",
									disableMobile: "true",
									defaultDate: ["<?php echo $f_from_flat;?>", "<?php echo $f_to_flat;?>"]
								});							
							</script>
							<div class="row">
								<div class="col-xs-12">
									<div id="employee_ids" style="display: none;"></div>
									<script type="text/javascript">
										$(document).ready(function() {
											var table = $('#idk_table').DataTable({

												responsive: true,
												'columnDefs': [
													{
													'targets': 0,
													'checkboxes': {
														'selectRow': true
													}
													}
												],
												'select': {
													'style': 'multi'
												},

												"order": [[ 1, "asc" ]],

												"bAutoWidth": false,
												
												"pageLength": 50,

												"aoColumns": [
														{ "width": "5%", "bSortable": false },
														{ "width": "5%", "bSortable": false },
														{ "width": "15%" },
														{ "width": "15%" },
														{ "width": "15%" },
														{ "width": "15%" },
														{ "width": "15%" },
														{ "width": "15%", "bSortable": false }
													]
											});
											$('#obicni_export').on('click', function(e){
																
												var rows_selected = table.column(0).checkboxes.selected();
												var f_from =  $(this).parent().parent().find('input[name="f_from"]').val();
												f_from = f_from.replace(/\s/g, '');
												// Iterate over all selected checkboxes
												$.each(rows_selected, function(index, rowId){
													$("#employee_ids").append(
														rowId
													);
												});
												
												var selectedIds = $('#employee_ids').find(".checkbox").map(function(){return $(this).val(); }).get();
												console.log(f_from);
												$('#exportajax').load('<?php getSiteUrl(); ?>export_excel.php?prozor=export_lista_provzija&selectedis='+selectedIds+'&f_from='+f_from+'');
												
												return false;
		
												e.preventDefault();
											}); 
											$('#isplata_export').on('click', function(e){
												$("#employee_ids .checkbox").remove();
												var rows_selected = table.column(0).checkboxes.selected();
												var f_from =  $(this).parent().parent().find('input[name="f_from"]').val();
												f_from = f_from.replace(/\s/g, '');
												// Iterate over all selected checkboxes
												$.each(rows_selected, function(index, rowId){
													$("#employee_ids").append(
														rowId
													);
												});
												
												var selectedIds = $('#employee_ids').find(".checkbox").map(function(){return $(this).val(); }).get();
												console.log(f_from);
												$('#exportajaxisplata').load('<?php getSiteUrl(); ?>export_excel.php?prozor=export_isplata_provizija&selectedis='+selectedIds+'&f_from='+f_from+'');
												
												return false;
		
												e.preventDefault();
											}); 	
										} );
									</script>
									<div id="exportajaxisplata"></div>	
									<div id="exportajax"></div>	
									<table id="idk_table" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th><input type="checkbox" id="select_all" name="select_all"></th>
												<th></th>
												<th>Ime i prezime</th>
												<th>Odjel</th>
												<th>Moguća uplata</th>
												<th>Za isplatu</th>
												<th>Isplaćeno</th>
												<th>Akcija</th>
											</tr>
										</thead>
										<tbody>
											<?php
												$query = $db->prepare("
																SELECT employee_id, employee_firstname, employee_lastname, employee_odjel, employee_image, employee_status, employee_poslovnica
																FROM idk_employees
																WHERE employee_team = $team");

												$query->execute();

												while($row = $query->fetch()){

													$employee_id = $row['employee_id'];
													$employee_firstname = $row['employee_firstname'];
													$employee_lastname = $row['employee_lastname'];
													$employee_status = $row['employee_status'];
													$employee_poslovnica = $row['employee_poslovnica'];

													if($row['employee_image'] == "none"){
														$employee_image = "none.jpg";
													}else{
														$employee_image = $row['employee_image'];
													}
													if($employee_status == 0){
														$style_arhiva = "background-color: #ff272787;";
													}else{
														$style_arhiva = "";
													}

													if($row['employee_odjel'] == 1){
														$employee_odjel = "Uprava";
													}elseif($row['employee_odjel'] == 2){
														$employee_odjel = "Financije";
													}elseif($row['employee_odjel'] == 3){
														$employee_odjel = "Prodaja";
													}elseif($row['employee_odjel'] == 4){
														$employee_odjel = "Obrada";
													}elseif($row['employee_odjel'] == 5){
														$employee_odjel = "Sve za vizu";
													}elseif($row['employee_odjel'] == 6){
														$employee_odjel = "Marketing";
													}elseif($row['employee_odjel'] == 7){
														$employee_odjel = "Tehnika";
													}elseif($row['employee_odjel'] == 8){
														$employee_odjel = "Development";
													}elseif($row['employee_odjel'] == 9){
														$employee_odjel = "Ostalo";
													}else{
														$employee_odjel = "-";
													}
													
													$get_provizije = $db->prepare("
																	SELECT
																	SUM(CASE WHEN status_predracuna = 0 AND status_obracuna = 0 AND vrijeme_kreiranja BETWEEN '$f_from_f' AND '$f_to_f' THEN iznos_obracuna_bam ELSE NULL END) as suma_bam_moguca,
																	SUM(CASE WHEN status_predracuna = 1 AND status_obracuna = 0 AND vrijeme_uplate BETWEEN '$f_from_f' AND '$f_to_f' THEN iznos_obracuna_bam ELSE NULL END) as suma_bam_konto,
																	SUM(CASE WHEN status_predracuna = 1 AND status_obracuna = 1 AND vrijeme_uplate BETWEEN '$f_from_f' AND '$f_to_f' THEN iznos_obracuna_bam ELSE NULL END) as suma_bam_isplaceno
																	FROM idk_obracuni
																	WHERE employee_id = :employee_id
													");
													$get_provizije->execute(array(
																	'employee_id' => $employee_id
													));
													
													$row_prov = $get_provizije->fetch();
													$suma_bam_moguca = $row_prov['suma_bam_moguca'];
													$suma_bam_konto = $row_prov['suma_bam_konto'];
													$suma_bam_isplaceno = $row_prov['suma_bam_isplaceno'];
													$suma_bam_moguca = number_format($suma_bam_moguca, 2, ',', '');
													$suma_bam_konto = number_format($suma_bam_konto, 2, ',', '');
													$suma_bam_isplaceno = number_format($suma_bam_isplaceno, 2, ',', '');
													if ($suma_bam_konto != "0,00" || $suma_bam_isplaceno != "0,00"){ 
												?>
													<tr style="<?php echo $style_arhiva; ?>">
														<td class="text-center"><input class="checkbox" type="checkbox" name="selectedrows[<?php echo $employee_id; ?>]" value="<?php echo $employee_id; ?>"></td>
														<td class="text-center" ><a href="<?php getSiteURL(); ?>provizije?page=zaposlenik&id=<?php echo $employee_id; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/employees/<?php echo $employee_image; ?>"></a></td>
														<td><a href="<?php getSiteURL(); ?>provizije?page=zaposlenik&id=<?php echo $employee_id; ?>"><?php echo $employee_firstname; ?> <?php echo $employee_lastname; ?></a></td>
														<td><?php echo $employee_odjel; ?></td>
														<td><a href="<?php getSiteURL(); ?>provizije?page=zaposlenik&id=<?php echo $employee_id; ?>#moguce"><?php echo $suma_bam_moguca; ?></a></td>
														<td><a href="<?php getSiteURL(); ?>provizije?page=zaposlenik&id=<?php echo $employee_id; ?>#konto"><?php echo $suma_bam_konto; ?></a></td>
														<td><a href="<?php getSiteURL(); ?>provizije?page=zaposlenik&id=<?php echo $employee_id; ?>#isplaceno"><?php echo $suma_bam_isplaceno; ?></a></td>
														<td class="text-center">
															Isplati
														</td>
													</tr>
													<?php 
													}
												} ?>
												
											</tbody>
										</table>

								</div>
							</div>
						</div>
					</div>
				</div>
				
			<?php
			break;
			
			case "zaposlenik":
				$employee_id = $_GET['id'];
				
				$f_from = $_POST['f_from'];
					
				if (strpos($f_from, 'to') !== false) {
					$split = explode(" to ",$f_from);
					$datum_od = $split[0];
					$datum_do = $split[1];
				}else{
					$datum_od = "2021-01-01 00:00:00";
					$datum_do = date("Y-m-d H:i:s");
				}
				
				$f_from_f = date('Y-m-d 00:00:00', strtotime($datum_od));
				$f_to_f = date('Y-m-d 23:59:59', strtotime($datum_do));	
				
				// FOR FLATPICKR
				$f_from_flat = date('d.m.Y', strtotime($datum_od));
				$f_to_flat = date('d.m.Y', strtotime($datum_do));	
				
				$query = $db->prepare("
								SELECT employee_firstname, employee_lastname, employee_image
								FROM idk_employees
								WHERE employee_id = :employee_id");

				$query->execute(array(
							':employee_id' => $employee_id));

				$row = $query->fetch();
				
				$employee_firstname = $row['employee_firstname'];
				$employee_lastname = $row['employee_lastname'];
				if($row['employee_image'] == "none"){
					$employee_image = "none.jpg";
				}else{
					$employee_image = $row['employee_image'];
				}
				?>
                <script>
                    $(document).ready(function(){
                        // get the tab from url
                        var hash = window.location.hash;
                        // if a hash is present (when you come to this page)
                        console.log(hash);
                        if (hash !='') {
                            // show the tab
                            $('.nav-tabs a[href="' + hash + '"]').tab('show');
                        }
                    });
                </script>
				<div class="row">
					<div class="col-xs-8">
						<h1><a class="fancybox" rel="group" href="<?php getSiteURL(); ?>files/employees/<?php echo $employee_image; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/employees/<?php echo $employee_image; ?>"></a> <?php echo $employee_firstname; ?> <?php echo $employee_lastname; ?></h1>
					</div>
					<div class="col-xs-4 text-right idk_margin_top10">
						<a href="<?php getSiteURL(); ?>provizije?page=lista" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
					</div>
					<div class="col-xs-12">
						<hr />
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="content_box">
						<div class="row">
							<form action="<?php getSiteUrl(); ?>provizije?page=zaposlenik&id=<?php echo $employee_id; ?>" enctype="multipart/form-data" method="post" accept-charset="utf-8" role="form" class="form-horizontal">
							<div class="col-sm-3">
								<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line">
									<input type="text" class="form-control" name="f_from" id="f_from" placeholder="Datum"  required>
								</div>
							</div>
							<div class="col-sm-1">
								<button style="width:100%" class="btn btn-success">Traži</button>
							</div>
							</form>
						</div>
						<script>
							$("#f_from").flatpickr({
								mode: "range",
								dateFormat: "d.m.Y",
								disableMobile: "true",
								defaultDate: ["<?php echo $f_from_flat;?>", "<?php echo $f_to_flat;?>"]
							});							
						</script>
							<div id="myTabs" class="panel-group material-tabs-group">
								<ul class="nav nav-tabs material-tabs material-tabs_primary">
									<li class="active"><a href="#info" class="material-tabs__tab-link" data-toggle="tab">Ukupno</a></li>
									<li><a href="#moguce" class="material-tabs__tab-link" data-toggle="tab">Moguća zarada</a></li>
									<li><a href="#konto" class="material-tabs__tab-link" data-toggle="tab">Za isplatu</a></li>
									<li><a href="#isplaceno" class="material-tabs__tab-link" data-toggle="tab">Isplaćeno</a></li>
								</ul>
								<div class="tab-content materail-tabs-content">
									<!-- UKUPNO -->
                                    <div class="tab-pane fade active in" id="info">
										<div class="row idk_employee_info">
											<script type="text/javascript">
												$(document).ready(function() {
													var table_ukupno = $('#idk_table2').DataTable({

														responsive: true,

														'columnDefs': [
															{
																'targets': 0,
																'checkboxes': {
																	'selectRow': true
																}
															}
														],
														'select': {
															'style': 'multi'
														},

														"order": [[ 4, "desc" ]],
														
														"bAutoWidth": false,
														
														"pageLength": 10,

														"aoColumns": [
																{ "width": "5%" },
																{ "width": "10%" },
																{ "width": "10%" },
																{ "width": "15%" },
																{ "width": "15%" },
																{ "width": "5%" },
																{ "width": "5%" },
																{ "width": "10%" },
																{ "width": "15%" },
																{ "width": "10%" }
															]
													});
												} );
											</script>
											<a href="#" id="export_ukupno" class="btn btn-primary material-btn material-btn_success" style="position: absolute; margin-left: 10%;"><i class="fa fa-file-o" aria-hidden="true"></i> Export</a>
											<table id="idk_table2" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th>ID</th>
														<th>Predračun</th>
														<th>Kandidat</th>
														<th class="text-center">Status predračuna</th>
														<th class="text-center">Status obračuna</th>
														<th class="text-center">Iznos (KM)</th>
														<th class="text-center">Rata</th>
														<th class="text-center">Tip provizije</th>
														<th>Agent</th>
														<th class="text-center">Tim</th>
													</tr>
												</thead>
												<tbody>
												<?php
												$query = $db->prepare("
																SELECT employee_id, predracun_id, pre.pr_broj_predracuna, status_predracuna, status_obracuna, iznos_obracuna_bam, pre.pr_zaposlenik, tip_provizije, rata, broj_rata, vrijeme_kreiranja, vrijeme_uplate, vrijeme_isplate, pr_kandidat_id
																FROM idk_obracuni
																JOIN idk_predracuni pre ON idk_obracuni.predracun_id = pre.pr_id
																WHERE employee_id = $employee_id AND 
																	((status_predracuna = 0 AND status_obracuna = 0 AND vrijeme_kreiranja BETWEEN '$f_from_f' AND '$f_to_f') OR
																	(status_predracuna = 1 AND status_obracuna = 0 AND vrijeme_uplate BETWEEN '$f_from_f' AND '$f_to_f') OR
																	(status_predracuna = 1 AND status_obracuna = 1 AND vrijeme_uplate BETWEEN '$f_from_f' AND '$f_to_f'))
																ORDER BY iznos_obracuna_bam DESC
																");

												$query->execute();
												$moguce = 0;
												$konto = 0;
												$isplaceno = 0;
												while($row = $query->fetch()){

													$employee_id = $row['employee_id'];
													$predracun_id = $row['predracun_id'];
													$status_predracuna = $row['status_predracuna'];
													$status_obracuna = $row['status_obracuna'];
													$tip_provizije = $row['tip_provizije'];
													$iznos_obr = $row['iznos_obracuna_bam'];
													$iznos_obracuna_bam = number_format($iznos_obr, 2, ',', '');
													$pr_broj_predracuna = $row['pr_broj_predracuna'];
													$agent_id = $row['pr_zaposlenik'];
													$vrijeme_kreiranja = $row['vrijeme_kreiranja'];
													$vrijeme_uplate = $row['vrijeme_uplate'];
													$vrijeme_isplate = $row['vrijeme_isplate'];
													$kandidat_name = getImePrezimeNDKanR($row['pr_kandidat_id']);
													$rata = $row['rata'];
													$broj_rata = $row['broj_rata'];
													$agent = getZaposlenikimeR($agent_id);
													$tim = getTeamNameByEmployeeId($agent_id);
													
													switch($tip_provizije){
														case 1:
															$tip_f = '<p class="text-center"><span class="label label-primary material-label material-label_primary material-label_xs main-container__column">NORMAL</span></p>';
														break;
														case 2:
															$tip_f = '<p class="text-center"><span class="label label-primary material-label material-label_primary material-label_xs main-container__column">ON TOP 1</span></p>';
														break;
														case 3:
															$tip_f = '<p class="text-center"><span class="label label-info material-label material-label_info material-label_xs main-container__column">Provizija od tima</span></p>';
														break;
														case 4:
															$tip_f = '<p class="text-center"><span class="label label-success material-label material-label_success material-label_xs main-container__column">ON TOP 72h</span></p>';
														break;
														case 5:
															$tip_f = '<p class="text-center"><span class="label label-warning material-label material-label_warning material-label_xs main-container__column">ON TOP 48h</span></p>';
														break;
														case 99:
															$tip_f = '<p class="text-center"><span class="label label-warning material-label material-label_warning material-label_xs main-container__column">INKASO</span></p>';
														break;
														case 98:
															$tip_f = '<p class="text-center"><span class="label label-warning material-label material-label_warning material-label_xs main-container__column">NORMAL-INKASO</span></p>';
														break;
														case 11:
															$tip_f = '<p class="text-center"><span class="label label-primary material-label material-label_primary material-label_xs main-container__column">ON TOP</span></p>';
														break;
														
													}
													if($status_predracuna == 0){
														$status_p = '<p class="text-center"><span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Nije uplaćen</span></p>';
														$status_o = '<p class="text-center"><span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Nije isplaćen</span></p>';
														$iznos_obracuna_f = '<p class="text-center"><span class="label label-danger material-label material-label_danger material-label_xs main-container__column">'.$iznos_obracuna_bam.'</span></p>';
														$moguce += $iznos_obr;
													}elseif($status_predracuna == 1){
														$status_p = '<p class="text-center"><span class="label label-success material-label material-label_success material-label_xs main-container__column">Uplaćen</span></p>';
														if($status_obracuna == 0){
															$status_o = '<p class="text-center"><span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Nije isplaćen</span></p>';
															$iznos_obracuna_f = '<p class="text-center"><span class="label label-info material-label material-label_info material-label_xs main-container__column">'.$iznos_obracuna_bam.'</span></p>';
															$konto += $iznos_obr;
														}elseif($status_obracuna == 1){
															$status_o = '<p class="text-center"><span class="label label-success material-label material-label_success material-label_xs main-container__column">Isplaćen</span></p>';
															$iznos_obracuna_f = '<p class="text-center"><span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$iznos_obracuna_bam.'</span></p>';
															$isplaceno += $iznos_obr;
														}
													}
													
													?>
													<tr>
														<td><?php echo $predracun_id; ?></td>
														<td title="<?php echo $vrijeme_kreiranja ; ?>" ><?php echo $pr_broj_predracuna; ?></td>
														<td><?php echo $kandidat_name; ?></td>
														<td title="<?php echo $vrijeme_uplate ; ?>" ><?php echo $status_p; ?></td>
														<td><?php echo $status_o; ?></td>
														<td><?php echo $iznos_obracuna_f; ?></td>
														<td class="text-center"><?php echo $rata."/".$broj_rata; ?></td>
														<td class="text-center"><?php echo $tip_f; ?></td>
														<td><?php echo $agent; ?></td>
														<td class="text-center" ><?php echo getIconTeam($agent_id); ?></td>
													</tr>
												<?php } ?>
												
												</tbody>
											</table>
										</div>
										<div class="row">
											<div class="card" data-toggle="tooltip" data-placement="top" title="Moguća zarada od napravljenih predračuna">
												<div class="rectangle expense"></div>
												<div>
												  <div class="naslov">Moguća zarada</div>
												  <div class="balance">Vrijednost</div>
												  <div class="kurs">KM</div>
												  <div class="amount"><?php echo number_format($moguce, 2, ',', '');?></div>
												  <div class="siluet-1"></div>
												  <div class="siluet-2"></div>
												  <img src="./Siluet.svg" alt="">
												</div>
											</div>
											<div class="card"  data-toggle="tooltip" data-placement="top" title="Zarada od uplaćenih predračuna">
												<div class="rectangle info"></div>
												<div>
												  <div class="naslov">Za isplatu</div>
												  <div class="balance">Vrijednost</div>
												  <div class="kurs">KM</div>
												  <div class="amount"><?php echo number_format($konto, 2, ',', '');?></div>
												  <div class="siluet-1"></div>
												  <div class="siluet-2"></div>
												  <img src="./Siluet.svg" alt="">
												</div>
											</div>
											<div class="card" data-toggle="tooltip" data-placement="top" title="Vrijednost isplaćenih obračuna">
												<div class="rectangle income"></div>
												<div>
												  <div class="naslov">Isplaćeno</div>
												  <div class="balance">Vrijednost</div>
												  <div class="kurs">KM</div>
												  <div class="amount"><?php echo number_format($isplaceno, 2, ',', '');?></div>
												  <div class="siluet-1"></div>
												  <div class="siluet-2"></div>
												  <img src="./Siluet.svg" alt="">
												</div>
											</div>
										</div>
										<script>
											$('#export_ukupno').on('click', function(e){
												
												var rows_selected = table_ukupno.column(0).checkboxes.selected();
												var f_from =  $(this).parent().parent().find('input[name="f_from"]').val();
												f_from = f_from.replace(/\s/g, '');
												// Iterate over all selected checkboxes
												$.each(rows_selected, function(index, rowId){
													$("#employee_ids").append(
														rowId
													);
												});
												
												var selectedIds = $('#employee_ids').find(".checkbox").map(function(){return $(this).val(); }).get();
												console.log(f_from);
												$('#exportajax').load('<?php getSiteUrl(); ?>export_excel.php?prozor=export_lista_provzija&selectedis='+selectedIds+'&f_from='+f_from+'');
												
												return false;
		
												e.preventDefault();
											}); 	
										</script>
									</div>
                                    <!-- MOGUCA ZARADA -->
                                    <div class="tab-pane fade" id="moguce">
										<div class="row idk_employee_info">
											<script type="text/javascript">
												$(document).ready(function() {
													$('#idk_table3').DataTable({

														responsive: true,

														"order": [[ 4, "desc" ]],
														
														"bAutoWidth": false,
														
														"pageLength": 10,

														"aoColumns": [
															{ "width": "5%" },
																{ "width": "10%" },
																{ "width": "10%" },
																{ "width": "15%" },
																{ "width": "15%" },
																{ "width": "5%" },
																{ "width": "5%" },
																{ "width": "10%" },
																{ "width": "15%" },
																{ "width": "10%" }
															]
													});
												} );
											</script>
											<table id="idk_table3" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th>ID</th>
														<th>Predračun</th>
														<th>Kandidat</th>
														<th class="text-center">Status predračuna</th>
														<th class="text-center">Status obračuna</th>
														<th class="text-center">Iznos (KM)</th>
														<th class="text-center">Rata</th>
														<th class="text-center">Tip provizije</th>
														<th>Agent</th>
														<th>Tim</th>
													</tr>
												</thead>
												<tbody>
												<?php
												$query = $db->prepare("
																SELECT employee_id, predracun_id, pre.pr_broj_predracuna, status_predracuna, status_obracuna, iznos_obracuna_bam, pre.pr_zaposlenik, tip_provizije, rata, broj_rata, vrijeme_kreiranja, vrijeme_uplate, vrijeme_isplate, pr_kandidat_id
																FROM idk_obracuni
																JOIN idk_predracuni pre ON idk_obracuni.predracun_id = pre.pr_id
																WHERE employee_id = $employee_id AND 
																	(status_predracuna = 0 AND status_obracuna = 0 AND vrijeme_kreiranja BETWEEN '$f_from_f' AND '$f_to_f')
																ORDER BY iznos_obracuna_bam DESC
																");

												$query->execute();
												$moguce = 0;
												$konto = 0;
												$isplaceno = 0;
												while($row = $query->fetch()){

													$employee_id = $row['employee_id'];
													$predracun_id = $row['predracun_id'];
													$status_predracuna = $row['status_predracuna'];
													$status_obracuna = $row['status_obracuna'];
													$tip_provizije = $row['tip_provizije'];
													$iznos_obr = $row['iznos_obracuna_bam'];
													$iznos_obracuna_bam = number_format($iznos_obr, 2, ',', '');
													$pr_broj_predracuna = $row['pr_broj_predracuna'];
													$agent_id = $row['pr_zaposlenik'];
													$vrijeme_kreiranja = $row['vrijeme_kreiranja'];
													$vrijeme_uplate = $row['vrijeme_uplate'];
													$vrijeme_isplate = $row['vrijeme_isplate'];
													$kandidat_name = getImePrezimeNDKanR($row['pr_kandidat_id']);
													$rata = $row['rata'];
													$broj_rata = $row['broj_rata'];
													$agent = getZaposlenikimeR($agent_id);
													$tim = getTeamNameByEmployeeId($agent_id);
													
													switch($tip_provizije){
														case 1:
															$tip_f = '<p class="text-center"><span class="label label-primary material-label material-label_primary material-label_xs main-container__column">NORMAL</span></p>';
														break;
														case 2:
															$tip_f = '<p class="text-center"><span class="label label-primary material-label material-label_primary material-label_xs main-container__column">ON TOP 1</span></p>';
														break;
														case 3:
															$tip_f = '<p class="text-center"><span class="label label-info material-label material-label_info material-label_xs main-container__column">Provizija od tima</span></p>';
														break;
														case 4:
															$tip_f = '<p class="text-center"><span class="label label-success material-label material-label_success material-label_xs main-container__column">ON TOP 72h</span></p>';
														break;
														case 5:
															$tip_f = '<p class="text-center"><span class="label label-warning material-label material-label_warning material-label_xs main-container__column">ON TOP 48h</span></p>';
														break;
														
													}
													$status_p = '<p class="text-center"><span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Nije uplaćen</span></p>';
													$status_o = '<p class="text-center"><span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Nije isplaćen</span></p>';
                                                    $iznos_obracuna_f = '<p class="text-center"><span class="label label-danger material-label material-label_danger material-label_xs main-container__column">'.$iznos_obracuna_bam.'</span></p>';
                                                    $moguce += $iznos_obr;
													
													
													?>
													<tr>
														<td><?php echo $predracun_id; ?></td>
														<td title="<?php echo $vrijeme_kreiranja ; ?>" ><?php echo $pr_broj_predracuna; ?></td>
														<td><?php echo $kandidat_name; ?></td>
														<td title="<?php echo $vrijeme_uplate ; ?>" ><?php echo $status_p; ?></td>
														<td><?php echo $status_o; ?></td>
														<td><?php echo $iznos_obracuna_f; ?></td>
														<td class="text-center"><?php echo $rata."/".$broj_rata; ?></td>
														<td class="text-center"><?php echo $tip_f; ?></td>
														<td><?php echo $agent; ?></td>
														<td class="text-center" ><?php echo getIconTeam($agent_id); ?></td>
													</tr>
												<?php } ?>
												
												</tbody>
											</table>
										</div>
										<div class="row">
											<div class="card" data-toggle="tooltip" data-placement="top" title="Moguća zarada od napravljenih predračuna">
												<div class="rectangle expense"></div>
												<div>
												  <div class="naslov">Moguća zarada</div>
												  <div class="balance">Vrijednost</div>
												  <div class="kurs">KM</div>
												  <div class="amount"><?php echo number_format($moguce, 2, ',', '');?></div>
												  <div class="siluet-1"></div>
												  <div class="siluet-2"></div>
												  <img src="./Siluet.svg" alt="">
												</div>
											</div>
										</div>
									</div>
                                    <!-- KONTO -->
                                    <div class="tab-pane fade" id="konto">
										<div class="row idk_employee_info">
											<script type="text/javascript">
												$(document).ready(function() {
													$('#idk_table4').DataTable({

														responsive: true,

														"order": [[ 4, "desc" ]],
														
														"bAutoWidth": false,
														
														"pageLength": 10,

														"aoColumns": [
															{ "width": "5%" },
																{ "width": "10%" },
																{ "width": "10%" },
																{ "width": "15%" },
																{ "width": "15%" },
																{ "width": "5%" },
																{ "width": "5%" },
																{ "width": "10%" },
																{ "width": "15%" },
																{ "width": "10%" }
															]
													});
												} );
											</script>
											<table id="idk_table4" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th>ID</th>
														<th>Predračun</th>
														<th>Kandidat</th>
														<th class="text-center">Status predračuna</th>
														<th class="text-center">Status obračuna</th>
														<th class="text-center">Iznos (KM)</th>
														<th class="text-center">Rata</th>
														<th class="text-center">Tip provizije</th>
														<th>Agent</th>
														<th>Tim</th>
													</tr>
												</thead>
												<tbody>
												<?php
												$query = $db->prepare("
																SELECT employee_id, predracun_id, pre.pr_broj_predracuna, status_predracuna, status_obracuna, iznos_obracuna_bam, pre.pr_zaposlenik, tip_provizije, rata, broj_rata, vrijeme_kreiranja, vrijeme_uplate, vrijeme_isplate, pr_kandidat_id
																FROM idk_obracuni
																JOIN idk_predracuni pre ON idk_obracuni.predracun_id = pre.pr_id
																WHERE employee_id = $employee_id AND 
																	(status_predracuna = 1 AND status_obracuna = 0 AND vrijeme_uplate BETWEEN '$f_from_f' AND '$f_to_f') 
																ORDER BY iznos_obracuna_bam DESC
																");

												$query->execute();
												$moguce = 0;
												$konto = 0;
												$isplaceno = 0;
												while($row = $query->fetch()){

													$employee_id = $row['employee_id'];
													$predracun_id = $row['predracun_id'];
													$status_predracuna = $row['status_predracuna'];
													$status_obracuna = $row['status_obracuna'];
													$tip_provizije = $row['tip_provizije'];
													$iznos_obr = $row['iznos_obracuna_bam'];
													$iznos_obracuna_bam = number_format($iznos_obr, 2, ',', '');
													$pr_broj_predracuna = $row['pr_broj_predracuna'];
													$agent_id = $row['pr_zaposlenik'];
													$vrijeme_kreiranja = $row['vrijeme_kreiranja'];
													$vrijeme_uplate = $row['vrijeme_uplate'];
													$vrijeme_isplate = $row['vrijeme_isplate'];
													$kandidat_name = getImePrezimeNDKanR($row['pr_kandidat_id']);
													$rata = $row['rata'];
													$broj_rata = $row['broj_rata'];
													$agent = getZaposlenikimeR($agent_id);
													$tim = getTeamNameByEmployeeId($agent_id);
													
													switch($tip_provizije){
														case 1:
															$tip_f = '<p class="text-center"><span class="label label-primary material-label material-label_primary material-label_xs main-container__column">NORMAL</span></p>';
														break;
														case 2:
															$tip_f = '<p class="text-center"><span class="label label-primary material-label material-label_primary material-label_xs main-container__column">ON TOP 1</span></p>';
														break;
														case 3:
															$tip_f = '<p class="text-center"><span class="label label-info material-label material-label_info material-label_xs main-container__column">Provizija od tima</span></p>';
														break;
														case 4:
															$tip_f = '<p class="text-center"><span class="label label-success material-label material-label_success material-label_xs main-container__column">ON TOP 72h</span></p>';
														break;
														case 5:
															$tip_f = '<p class="text-center"><span class="label label-warning material-label material-label_warning material-label_xs main-container__column">ON TOP 48h</span></p>';
														break;
														
													}
													$status_p = '<p class="text-center"><span class="label label-success material-label material-label_success material-label_xs main-container__column">Uplaćen</span></p>';
													$status_o = '<p class="text-center"><span class="label label-danger material-label material-label_danger material-label_xs main-container__column">Nije isplaćen</span></p>';
													$iznos_obracuna_f = '<p class="text-center"><span class="label label-info material-label material-label_info material-label_xs main-container__column">'.$iznos_obracuna_bam.'</span></p>';
													$konto += $iznos_obr;
													
													
													?>
													<tr>
														<td><?php echo $predracun_id; ?></td>
														<td title="<?php echo $vrijeme_kreiranja ; ?>" ><?php echo $pr_broj_predracuna; ?></td>
														<td><?php echo $kandidat_name; ?></td>
														<td title="<?php echo $vrijeme_uplate ; ?>" ><?php echo $status_p; ?></td>
														<td><?php echo $status_o; ?></td>
														<td><?php echo $iznos_obracuna_f; ?></td>
														<td class="text-center"><?php echo $rata."/".$broj_rata; ?></td>
														<td class="text-center"><?php echo $tip_f; ?></td>
														<td><?php echo $agent; ?></td>
														<td class="text-center" ><?php echo getIconTeam($agent_id); ?></td>
													</tr>
												<?php } ?>
												
												</tbody>
											</table>
										</div>
										<div class="row">
											<div class="card"  data-toggle="tooltip" data-placement="top" title="Zarada od uplaćenih predračuna">
												<div class="rectangle info"></div>
												<div>
												  <div class="naslov">Za isplatu</div>
												  <div class="balance">Vrijednost</div>
												  <div class="kurs">KM</div>
												  <div class="amount"><?php echo number_format($konto, 2, ',', '');?></div>
												  <div class="siluet-1"></div>
												  <div class="siluet-2"></div>
												  <img src="./Siluet.svg" alt="">
												</div>
											</div>
										</div>
									</div>
                                    <!-- ISPLACENO -->
                                    <div class="tab-pane fade" id="isplaceno">
										<div class="row idk_employee_info">
											<script type="text/javascript">
												$(document).ready(function() {
													$('#idk_table5').DataTable({

														responsive: true,

														"order": [[ 4, "desc" ]],
														
														"bAutoWidth": false,
														
														"pageLength": 10,

														"aoColumns": [
																{ "width": "5%" },
																{ "width": "10%" },
																{ "width": "10%" },
																{ "width": "15%" },
																{ "width": "15%" },
																{ "width": "5%" },
																{ "width": "5%" },
																{ "width": "10%" },
																{ "width": "15%" },
																{ "width": "10%" }
															]
													});
												} );
											</script>
											<table id="idk_table5" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th>ID</th>
														<th>Predračun</th>
														<th>Kandidat</th>
														<th class="text-center">Status predračuna</th>
														<th class="text-center">Status obračuna</th>
														<th class="text-center">Iznos (KM)</th>
														<th class="text-center">Rata</th>
														<th class="text-center">Tip provizije</th>
														<th>Agent</th>
														<th>Tim</th>
													</tr>
												</thead>
												<tbody>
												<?php
												$query = $db->prepare("
																SELECT employee_id, predracun_id, pre.pr_broj_predracuna, status_predracuna, status_obracuna, iznos_obracuna_bam, pre.pr_zaposlenik, tip_provizije, rata, broj_rata, vrijeme_kreiranja, vrijeme_uplate, vrijeme_isplate, pr_kandidat_id
																FROM idk_obracuni
																JOIN idk_predracuni pre ON idk_obracuni.predracun_id = pre.pr_id
																WHERE employee_id = $employee_id AND 
																	(status_predracuna = 1 AND status_obracuna = 1 AND vrijeme_uplate BETWEEN '$f_from_f' AND '$f_to_f')
																ORDER BY iznos_obracuna_bam DESC
																");

												$query->execute();
												$moguce = 0;
												$konto = 0;
												$isplaceno = 0;
												while($row = $query->fetch()){

													$employee_id = $row['employee_id'];
													$predracun_id = $row['predracun_id'];
													$status_predracuna = $row['status_predracuna'];
													$status_obracuna = $row['status_obracuna'];
													$tip_provizije = $row['tip_provizije'];
													$iznos_obr = $row['iznos_obracuna_bam'];
													$iznos_obracuna_bam = number_format($iznos_obr, 2, ',', '');
													$pr_broj_predracuna = $row['pr_broj_predracuna'];
													$agent_id = $row['pr_zaposlenik'];
													$vrijeme_kreiranja = $row['vrijeme_kreiranja'];
													$vrijeme_uplate = $row['vrijeme_uplate'];
													$vrijeme_isplate = $row['vrijeme_isplate'];
													$kandidat_name = getImePrezimeNDKanR($row['pr_kandidat_id']);
													$rata = $row['rata'];
													$broj_rata = $row['broj_rata'];
													$agent = getZaposlenikimeR($agent_id);
													$tim = getTeamNameByEmployeeId($agent_id);
													
													switch($tip_provizije){
														case 1:
															$tip_f = '<p class="text-center"><span class="label label-primary material-label material-label_primary material-label_xs main-container__column">NORMAL</span></p>';
														break;
														case 2:
															$tip_f = '<p class="text-center"><span class="label label-primary material-label material-label_primary material-label_xs main-container__column">ON TOP 1</span></p>';
														break;
														case 3:
															$tip_f = '<p class="text-center"><span class="label label-info material-label material-label_info material-label_xs main-container__column">Provizija od tima</span></p>';
														break;
														case 4:
															$tip_f = '<p class="text-center"><span class="label label-success material-label material-label_success material-label_xs main-container__column">ON TOP 72h</span></p>';
														break;
														case 5:
															$tip_f = '<p class="text-center"><span class="label label-warning material-label material-label_warning material-label_xs main-container__column">ON TOP 48h</span></p>';
														break;
														
													}
													$status_p = '<p class="text-center"><span class="label label-success material-label material-label_success material-label_xs main-container__column">Uplaćen</span></p>';
													$status_o = '<p class="text-center"><span class="label label-success material-label material-label_success material-label_xs main-container__column">Isplaćen</span></p>';
													$iznos_obracuna_f = '<p class="text-center"><span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$iznos_obracuna_bam.'</span></p>';
													$isplaceno += $iznos_obr;
													
													?>
													<tr>
														<td><?php echo $predracun_id; ?></td>
														<td title="<?php echo $vrijeme_kreiranja ; ?>" ><?php echo $pr_broj_predracuna; ?></td>
														<td><?php echo $kandidat_name; ?></td>
														<td title="<?php echo $vrijeme_uplate ; ?>" ><?php echo $status_p; ?></td>
														<td><?php echo $status_o; ?></td>
														<td><?php echo $iznos_obracuna_f; ?></td>
														<td class="text-center"><?php echo $rata."/".$broj_rata; ?></td>
														<td class="text-center"><?php echo $tip_f; ?></td>
														<td><?php echo $agent; ?></td>
														<td class="text-center" ><?php echo getIconTeam($agent_id); ?></td>
													</tr>
												<?php } ?>
												
												</tbody>
											</table>
										</div>
										<div class="row">
											<div class="card" data-toggle="tooltip" data-placement="top" title="Vrijednost isplaćenih obračuna">
												<div class="rectangle income"></div>
												<div>
												  <div class="naslov">Isplaćeno</div>
												  <div class="balance">Vrijednost</div>
												  <div class="kurs">KM</div>
												  <div class="amount"><?php echo number_format($isplaceno, 2, ',', '');?></div>
												  <div class="siluet-1"></div>
												  <div class="siluet-2"></div>
												  <img src="./Siluet.svg" alt="">
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<?php
			
			break;
			
			case "ispravi_provizije":
				$employee_ids = array(32, 33, 43, 48, 49, 50, 63, 67, 75, 79, 83, 85, 87, 88, 92, 109, 120, 121, 122, 127, 128, 130, 132, 134, 137, 142, 158, 159, 160, 162, 163, 164, 174, 175);
				
				foreach($employee_ids as $employee_id){
					/*$get_obracuni = $db->prepare("
									SELECT SUM(iznos_obracuna_bam) as suma
									FROM idk_obracuni
									WHERE status_predracuna = 1 AND status_obracuna = 1 AND vrijeme_uplate BETWEEN '2021-05-01 00:00:00' AND '2021:05-31 23:59:59' AND employee_id = $employee_id
					");
					$get_obracuni->execute();
					$row_obracuni = $get_obracuni->fetch();
					$iznos = number_format($row_obracuni['suma'], 2, ',', '');
					echo $employee_id." --- ".$iznos."<br/>";*/
					/*
					$update_obracuni = $db->prepare("
									UPDATE idk_obracuni
									SET status_obracuna = 0
									WHERE status_predracuna = 1 AND status_obracuna = 1 AND vrijeme_uplate BETWEEN '2021-05-01 00:00:00' AND '2021:05-31 23:59:59' AND employee_id = $employee_id
					");
					$update_obracuni->execute();
					*/
					//brisanje novih
					/*$delete_ostaci= $db->prepare("
									DELETE
									FROM idk_ostatci_obracuna
									WHERE oo_period_od = '2021-05-01 00:00:00' AND oo_period_do = '2021-05-31 23:59:59' AND oo_employee_id = $employee_id AND oo_status = 0
					");
					$delete_ostaci->execute();*/
					
					//update starih na neisplacen
					/*$update_ost = $db->prepare("
									UPDATE idk_ostatci_obracuna
									SET oo_status = 0
									WHERE oo_period_od = '2021-04-01 00:00:00' AND oo_period_do = '2021-04-30 23:59:59' AND oo_employee_id = $employee_id
					");
					$update_ost->execute();*/
				}
			break;
		}
		?>
			<footer><?php getCopyright(); ?></footer>
		</div>
	</div>
</body>
</html>
<?php }else{			
			echo '
				<br/>
				<div class="alert material-alert material-alert_danger">
					<h4>NEMATE PRIVILEGIJE!</h4>
					<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
					<br />
				</div>
';} ?>