<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus = explode( ',' , getEmployeeStatus());

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: nalozi?page=list");
	}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Nalozi | <?php getTitle(); ?></title>

	<?php include('includes/head.php'); 
	if (in_array($getUserIp, $getIpWhiteList)){
	?>
	<!-- CK Editor ---------------------------------------------------------------------------------------->
	<script src="<?php getSiteURL(); ?>ckeditor/ckeditor.js" async></script>
	<script src="/components/Nalog/IdeNaGlossu.js" type="module"></script>
	<script src="/components/Nalog/nalogSmjerKriterij.js" type="module"></script>
	<script src="/components/Nalog/nalogSmjeroviKriterij.js" type="module"></script>

	<!-- HTML to MD -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/showdown/1.9.0/showdown.min.js"></script>
	<!-- MD to HTML -->
	<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
	<script src="/components/Nalog/PoslodavacTraziJezik.js" type="module"></script>
	<script src="/components/Nalog/PoslodavacKoristiPP.js" type="module"></script>


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

				case "list":
		?>
			<div class="row">
				<div class="col-xs-4">
					<h1><i class="fa fa-file-o idk_color_green" aria-hidden="true"></i> Nalozi</h1>
				</div>
				<div class="col-xs-4 text-right">
					<button id="export_naloga" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-file-excel-o" aria-hidden="true"></i> <span>Export</span></button>
				</div>
				<script>
				$(document).ready(function() {
					$('#export_naloga').click(function() {
						$('#export_naloga_div').load('export_excel.php?prozor=export_naloga_excel');
					
						return false;
					});
				});				
				</script>	
				<div id="export_naloga_div"></div>
				<div class="col-xs-4 text-right">
					<select id="table-filter" class="selectpicker" data-live-search="true">
						<option value="">Svi statusi</option>
						<option>Potpis</option>
						<option>Čeka se uplata</option>
						<option>Marketing</option>
						<option>Prijave u toku</option>
						<option>Obrada prijava</option>
						<option>Nalog kod poslodavca</option>
						<option>Casting</option>
						<option>Na čekanju</option>
						<option>Kandidati u odlasku</option>
						<option>Završeno (nenaplaćeno)</option>
					</select>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-xs-12">
								<?php
									if(isset($_GET['mess'])) {
										$mess = $_GET['mess'];
									}else{
										$mess = 0;
									}

									if($mess == 1){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novu kompaniju.</div>';
									}elseif($mess == 3){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali profil kompanije.</div>';
									}
								?>
								<script type="text/javascript">
									$(document).ready(function() {
										var table = $('#idk_table').DataTable({
											responsive: true,
											"order": [[ 0, "desc" ]],
											 "bAutoWidth": false,
											"aoColumns": [
													{ "width": "5%" },
													{ "width": "20%" },
													{ "width": "25%" },
													{ "width": "10%" },
													{ "width": "10%" },
													{ "width": "8%" },
													{ "width": "10%" },
													{ "width": "12%", "bSortable": false }
												]
										});

										$('#table-filter').on('change', function(){
											table.search(this.value).draw();   
										});
									} );
								</script>
								<table id="idk_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th class="text-center">Broj</th>
											<th class="text-center">Naziv</th>
											<th class="text-center">Kompanija</th>
											<th class="text-center">Projekt menadžer</th>
											<th class="text-center">Status</th>
											<th class="text-center">Vrsta</th>
											<th class="text-center">Kreirano</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php
											if($logged_employee_id == 63){ //SABINA da ne vidi JOBSTEP naloge
												$nalog_uslov = " AND kompanija_id != 5 ";
											}else{
												$nalog_uslov = "";
											}
											$query = $db->prepare("
                                                    SELECT nalog_id, kompanija_id, nalog_broj, nalog_naziv, nalog_opis, nalog_kreirano, nalog_status, nalog_ugovor, comp.company_name, employee_id, nalog_financije
                                                    FROM idk_nalozi
                                                    INNER JOIN idk_companies comp ON kompanija_id = comp.company_id
                                                    WHERE nalog_status NOT IN (8,12) $nalog_uslov");

											$query->execute();

											while($row = $query->fetch()){

                                                $nalog_id = $row['nalog_id'];
												$nalog_broj = $row['nalog_broj'];
												$kompanija_id = $row['kompanija_id'];
												$nalog_naziv = $row['nalog_naziv'];
												$nalog_ugovor = $row['nalog_ugovor'];
                                                $kompanija = $row['company_name'];
                                                $nalog_status = $row['nalog_status'];
                                                $employee_id = $row['employee_id'];
                                                $nalog_financije = $row['nalog_financije'];
                                                $nalog_kreirano = date('d.m.Y.', strtotime($row['nalog_kreirano']));
												
												$query_pm = $db->prepare("
																SELECT employee_id, employee_firstname, employee_lastname
																FROM idk_employees
																WHERE employee_id = :employee_id");

												$query_pm->execute(array(':employee_id' => $employee_id));

												$row_pm = $query_pm->fetch();

												$employee_firstname = $row_pm['employee_firstname'];
												$employee_lastname = $row_pm['employee_lastname'];
												
                                                if($nalog_status == 1){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-primary">Potpis</span>';
                                                }elseif($nalog_status == 2){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-warning">Čeka se uplata</span>';
                                                }elseif($nalog_status == 3){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-primary">Marketing</span>';
                                                }elseif($nalog_status == 4){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-warning">Prijave u toku</span>';
                                                }elseif($nalog_status == 5){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-primary">Obrada prijava</span>';
                                                }elseif($nalog_status == 6){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-warning">Nalog kod poslodavca</span>';
                                                }elseif($nalog_status == 7){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-primary">Casting</span>';
                                                }elseif($nalog_status == 8){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-success">Završeno</span>';
                                                }elseif($nalog_status == 9){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-danger">Na čekanju</span>';
                                                }elseif($nalog_status == 10){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-success">Kandidati u odlasku</span>';
                                                }elseif($nalog_status == 11){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-info">Završeno (nenaplaćeno)</span>';
                                                }elseif($nalog_status == 12){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-danger">Arhiviran<span>';
                                                }
                                                
										?>
										<tr>
											<td class="text-center"><?php echo $nalog_broj; ?></td>
											<td class="text-center"><?php echo $nalog_naziv; ?></td>
											<td class="text-center"><a href="<?php getSiteURL(); ?>companies?page=open&id=<?php echo $kompanija_id; ?>"><?php echo $kompanija; ?></a></td>
											<td class="text-center"><?php echo $employee_firstname." ".$employee_lastname; ?></td>
											<td class="text-center" id="nalog_status_cell<?php echo $nalog_id?>"><?php echo $nalog_status_txt; ?></td>
											<td class="text-center"><?php echo $nalog_ugovor; ?></td>
											<td class="text-center"><?php echo $nalog_kreirano; ?></td>
											<td class="text-center d-inline">
												<a href="<?php getSiteURL(); ?>nalozi?page=open&id=<?php echo $nalog_id; ?>" class="btn material-btn material-btn_success main-container__column"><i class="fa fa-info-circle" aria-hidden="true"></i></a>
												<?php
												if(in_array($logged_employee_id, array(43,49,67))){
													?>
														<a href="<?php getSiteURL(); ?>skole?page=skola_nalog&id=<?php echo $nalog_id; ?>" class="btn material-btn material-btn_success main-container__column"><i class="fa fa-book" aria-hidden="true"></i></a>
													<?php
												}
												if(in_array($logged_employee_id, array(134, 67, 213, 202, 207))){
												?>
												<a href="#" class="btn material-btn material-btn_danger main-container__column arhiviraj_nalog_button" id="arhiviraj_nalog_button<?php echo $nalog_id; ?>" nalog_id="<?php echo $nalog_id; ?>" data-toggle="modal" data-target="#modal_nalog_potvrdi_arhiviranje"><i class="fa fa-trash" aria-hidden="true"></i></a>
												<?php
												}
												?>
											</td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal material-modal material-modal_primary fade text-left" id="modal_nalog_potvrdi_arhiviranje">
				<div class="modal-dialog ">
					<div class="modal-content material-modal__content">
						<div class="modal-header material-modal__header">
							<button class="close material-modal__close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title material-modal__title"><b>Jeste li sigurni da želite arhivirati ovaj nalog?</b><h4>
						</div>
						<div class="modal-body material-modal__body text-center">
							<input type="hidden" id="clicked_nalog_id">
							<button id="arhiviraj_nalog_button_confirm" class="btn material-btn material-btn_info" style="margin-right:20px;">
								<i class="fa fa-trash" style="font-size: xxx-large;" aria-hidden="true"></i><br>
								<span style="font-size:15px;">DA</span>
							</button>
							<button id="arhiviraj_nalog_button_cancel" class="btn material-btn material-btn_info" style="margin-left:20px;">
								<i class="fa fa-times" style="font-size: xxx-large;" aria-hidden="true"></i><br>
								<span style="font-size:15px;">NE</span>
							</button>
						</div>
					</div>
				</div>
			</div>
			<script>
				$('.arhiviraj_nalog_button').on('click', function () {
					$('#clicked_nalog_id').val($(this).attr('nalog_id'));
					
				});
				
				$('#arhiviraj_nalog_button_cancel').on('click', function () {
					$('#clicked_nalog_id').empty();
					$("#modal_nalog_potvrdi_arhiviranje").modal('hide');
				});
				$('#arhiviraj_nalog_button_confirm').on('click', function () {
					var nalog_id = $('#clicked_nalog_id').val();	
					$.ajax({
						url: 'ajax_data.php?page=arhiviraj_nalog',
						type: 'POST',
						data: {	
							'nalog_id' :nalog_id
						},
						dataType: 'html',
						success: function(text) {
							$("#modal_nalog_potvrdi_arhiviranje").modal('hide');
							$("#arhiviraj_nalog_button"+nalog_id).fadeOut();
							$('#nalog_status_cell'+nalog_id).fadeOut(500, function(){
								$('#nalog_status_cell'+nalog_id).empty().append('<span class="label label-danger"style="font-size:13px;">UPRAVO ARHIVIRAN<span>').fadeIn();
							});
							
						},
						error: function (xhr, ajaxOptions, thrownError) {
							alert(xhr.status);
							alert(thrownError);
						}
					});
				});
			</script>
		<?php
				break;

				case "list_finish":
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-file-o idk_color_green" aria-hidden="true"></i> Nalozi</h1>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-xs-12">
								<?php
									if(isset($_GET['mess'])) {
										$mess = $_GET['mess'];
									}else{
										$mess = 0;
									}

									if($mess == 1){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novu kompaniju.</div>';
									}elseif($mess == 3){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali profil kompanije.</div>';
									}
								?>
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_table').DataTable({

											responsive: true,

											"order": [[ 1, "asc" ]],

											 "bAutoWidth": false,

											"aoColumns": [
													{ "width": "5%", "bSortable": false },
													{ "width": "10%" },
													{ "width": "20%" },
													{ "width": "25%" },
													{ "width": "15%" },
													{ "width": "15%" },
													{ "width": "10%", "bSortable": false }
												]
										});
									} );
								</script>
								<table id="idk_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th class="text-center"></th>
											<th class="text-center">Broj</th>
											<th class="text-center">Naziv</th>
											<th class="text-center">Kompanija</th>
											<th class="text-center">Status</th>
											<th class="text-center">Kreirano</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
                                                    SELECT nalog_id, kompanija_id, nalog_broj, nalog_naziv, nalog_opis, nalog_kreirano, nalog_status, comp.company_name
                                                    FROM idk_nalozi
                                                    INNER JOIN idk_companies comp ON kompanija_id = comp.company_id
                                                    WHERE nalog_status = :nalog_status");

											$query->execute(array(':nalog_status' => 8));

											while($row = $query->fetch()){

                                                $nalog_id = $row['nalog_id'];
												$nalog_broj = $row['nalog_broj'];
												$kompanija_id = $row['kompanija_id'];
												$nalog_naziv = $row['nalog_naziv'];
                                                $kompanija = $row['company_name'];
                                                $nalog_status = $row['nalog_status'];
                                                $nalog_kreirano = date('d.m.Y.', strtotime($row['nalog_kreirano']));

                                                if($nalog_status == 1){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-primary">Potpis</span>';
                                                }elseif($nalog_status == 2){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-warning">Čeka se uplata</span>';
                                                }elseif($nalog_status == 3){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-primary">Marketing</span>';
                                                }elseif($nalog_status == 4){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-warning">Prijave u toku</span>';
                                                }elseif($nalog_status == 5){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-primary">Obrada prijava</span>';
                                                }elseif($nalog_status == 6){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-warning">Nalog kod poslodavca</span>';
                                                }elseif($nalog_status == 7){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-primary">Casting</span>';
                                                }elseif($nalog_status == 8){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-success">Završeno</span>';
                                                }elseif($nalog_status == 9){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-danger">Na čekanju</span>';
                                                }elseif($nalog_status == 10){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-success">Kandidati u odlasku</span>';
                                                }elseif($nalog_status == 11){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-info">Završeno (nenaplaćeno)</span>';
                                                }elseif($nalog_status == 12){
                                                    $nalog_status_txt = '<span id="nalog_status'.$nalog_id.'" class="label label-danger">Arhiviran<span>';
                                                }
										?>
										<tr>
											<td class="text-center"><?php echo $nalog_id; ?></td>
											<td class="text-center"><?php echo $nalog_broj; ?></td>
											<td class="text-center"><?php echo $nalog_naziv; ?></td>
											<td class="text-center"><a href="<?php getSiteURL(); ?>companies?page=open&id=<?php echo $kompanija_id; ?>"><?php echo $kompanija; ?></a></td>
											<td class="text-center" id="nalog_status_cell<?php echo $nalog_id?>"><?php echo $nalog_status_txt; ?></td>
											<td class="text-center"><?php echo $nalog_kreirano; ?></td>
											<td class="text-center">
											<a href="<?php getSiteURL(); ?>nalozi?page=open&id=<?php echo $nalog_id; ?>" class="btn material-btn material-btn_success main-container__column"><i class="fa fa-info-circle" aria-hidden="true"></i></a>
											<?php
											if(in_array($logged_employee_id, array(134, 67, 213, 202, 207 ))){
											?>
											<a href="#" class="btn material-btn material-btn_danger main-container__column arhiviraj_nalog_button" id="arhiviraj_nalog_button<?php echo $nalog_id; ?>" nalog_id="<?php echo $nalog_id; ?>" data-toggle="modal" data-target="#modal_nalog_potvrdi_arhiviranje"><i class="fa fa-trash" aria-hidden="true"></i></a>
											<?php
											}
											?>
											</td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal material-modal material-modal_primary fade text-left" id="modal_nalog_potvrdi_arhiviranje">
				<div class="modal-dialog ">
					<div class="modal-content material-modal__content">
						<div class="modal-header material-modal__header">
							<button class="close material-modal__close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title material-modal__title"><b>Jeste li sigurni da želite arhivirati ovaj nalog?</b><h4>
						</div>
						<div class="modal-body material-modal__body text-center">
							<input type="hidden" id="clicked_nalog_id">
							<button id="arhiviraj_nalog_button_confirm" class="btn material-btn material-btn_info" style="margin-right:20px;">
								<i class="fa fa-trash" style="font-size: xxx-large;" aria-hidden="true"></i><br>
								<span style="font-size:15px;">DA</span>
							</button>
							<button id="arhiviraj_nalog_button_cancel" class="btn material-btn material-btn_info" style="margin-left:20px;">
								<i class="fa fa-times" style="font-size: xxx-large;" aria-hidden="true"></i><br>
								<span style="font-size:15px;">NE</span>
							</button>
						</div>
					</div>
				</div>
			</div>
			<script>
				$('.arhiviraj_nalog_button').on('click', function () {
					$('#clicked_nalog_id').val($(this).attr('nalog_id'));
					
				});
				
				$('#arhiviraj_nalog_button_cancel').on('click', function () {
					$('#clicked_nalog_id').empty();
					$("#modal_nalog_potvrdi_arhiviranje").modal('hide');
				});
				$('#arhiviraj_nalog_button_confirm').on('click', function () {
					var nalog_id = $('#clicked_nalog_id').val();	
					$.ajax({
						url: 'ajax_data.php?page=arhiviraj_nalog',
						type: 'POST',
						data: {	
							'nalog_id' :nalog_id
						},
						dataType: 'html',
						success: function(text) {
							$("#modal_nalog_potvrdi_arhiviranje").modal('hide');
							$("#arhiviraj_nalog_button"+nalog_id).fadeOut();
							$('#nalog_status_cell'+nalog_id).fadeOut(500, function(){
								$('#nalog_status_cell'+nalog_id).empty().append('<span class="label label-danger"style="font-size:13px;">UPRAVO ARHIVIRAN<span>').fadeIn();
							});
							
						},
						error: function (xhr, ajaxOptions, thrownError) {
							alert(xhr.status);
							alert(thrownError);
						}
					});
				});
			</script>
		<?php
				break;


				case "open":

                    $nalog_id = $_GET['id'];
                    
                    $query = $db->prepare("
                            SELECT nalog_id, kompanija_id, nalog_broj, nalog_naziv, nalog_opis, nalog_kreirano, nalog_status, nalog_marketing_menadzer, comp.company_name, nalog_ugovor, nalog_potrebno_kandidata, nalog_provizija, nalog_broj_rata, employee_id,nalog_partner_active, nalog_partner_provizija, nalog_slanje_na_glosu, pristup_poslodavcima, nalog_financije, nalog_provizija_po_plati, nalog_placa_nostrifikaciju, nalog_nacin_nostrifikacije, nalog_provizija_nostrifikacija, nalog_dospijece, nalog_smjerovi_kriterij, nalog_poslodavac_trazi_jezik, nalog_poslodavac_koristi_pp
                            FROM idk_nalozi
                            INNER JOIN idk_companies comp ON kompanija_id = comp.company_id
                            WHERE nalog_id = :nalog_id");

                    $query->execute(array(':nalog_id' => $nalog_id));

                    $row = $query->fetch();

                    $nalog_id = $row['nalog_id'];
                    $nalog_broj = $row['nalog_broj'];
                    $kompanija_id = $row['kompanija_id'];
                    $nalog_naziv = $row['nalog_naziv'];
                    $kompanija = $row['company_name'];
                    $nalog_status = $row['nalog_status'];
                    $nalog_kreirano = date('d.m.Y.', strtotime($row['nalog_kreirano']));
                    $nalog_opis = $row['nalog_opis'];
                    $nalog_marketing_menadzer = $row['nalog_marketing_menadzer'];
                    $nalog_ugovor = $row['nalog_ugovor'];
                    $nalog_potrebno_kandidata = $row['nalog_potrebno_kandidata'];
                    $nalog_provizija = $row['nalog_provizija'];
                    $nalog_broj_rata = $row['nalog_broj_rata'];
                    $nalog_partner_active = $row['nalog_partner_active'];
                    $nalog_partner_provizija = $row['nalog_partner_provizija'];
                    $nalog_slanje_na_glosu = $row['nalog_slanje_na_glosu'];
                    $employee_id = $row['employee_id'];
                    $nalog_pristup = $row['pristup_poslodavcima'];
					$nacin_placanja = $row['nalog_financije'];
					$nalog_provizija_po_plati = $row['nalog_provizija_po_plati'];
					$nalog_placa_nostrifikaciju = $row['nalog_placa_nostrifikaciju'];
					$nalog_nacin_nostrifikacije = $row['nalog_nacin_nostrifikacije'];
					$nalog_provizija_nostrifikacija = $row['nalog_provizija_nostrifikacija'];
					$nalog_dospijece = $row['nalog_dospijece'];
					$nalog_smjerovi_kriterij = $row['nalog_smjerovi_kriterij'];
					$nalog_poslodavac_trazi_jezik = $row['nalog_poslodavac_trazi_jezik'];
					$nalog_poslodavac_koristi_pp = $row['nalog_poslodavac_koristi_pp'];

                    if($nalog_status == 1){
                        $nalog_status_txt = '<span class="label label-primary">Potpis</span>';
                    }elseif($nalog_status == 2){
                        $nalog_status_txt = '<span class="label label-warning">Čeka se uplata</span>';
                    }elseif($nalog_status == 3){
                        $nalog_status_txt = '<span class="label label-primary">Marketing</span>';
                    }elseif($nalog_status == 4){
                        $nalog_status_txt = '<span class="label label-warning">Prijave u toku</span>';
                    }elseif($nalog_status == 5){
                        $nalog_status_txt = '<span class="label label-primary">Obrada prijava</span>';
                    }elseif($nalog_status == 6){
                        $nalog_status_txt = '<span class="label label-warning">Nalog kod poslodavca</span>';
                    }elseif($nalog_status == 7){
                        $nalog_status_txt = '<span class="label label-primary">Casting</span>';
                    }elseif($nalog_status == 8){
                        $nalog_status_txt = '<span class="label label-success">Završeno</span>';
                    }elseif($nalog_status == 9){
                        $nalog_status_txt = '<span class="label label-danger">Na čekanju</span>';
                    }elseif($nalog_status == 10){
                        $nalog_status_txt = '<span class="label label-success">Kandidati u odlasku</span>';
                    }elseif($nalog_status == 11){
						$nalog_status_txt = '<span class="label label-info">Završeno (nenaplaćeno)</span>';
					}elseif($nalog_status == 12){
						$nalog_status_txt = '<span class="label label-danger">Arhiviran</span>';
					}
					
					$query_marketing = $db->prepare("
									SELECT employee_id, employee_firstname, employee_lastname
									FROM idk_employees
									WHERE employee_id = :employee_id");

                    $query_marketing->execute(array(':employee_id' => $nalog_marketing_menadzer));

                    $row_marketing = $query_marketing->fetch();

                    $employee_firstname = $row_marketing['employee_firstname'];
                    $employee_lastname = $row_marketing['employee_lastname'];
					
					$query_responsible_employee = $db->prepare("
									SELECT employee_firstname, employee_lastname
									FROM idk_employees
									WHERE employee_id = :employee_id");

                    $query_responsible_employee->execute(array(
									':employee_id' => $employee_id));

                    $row_responsible_employee = $query_responsible_employee->fetch();

                    $responsible_name = $row_responsible_employee['employee_firstname'];
                    $responsible_lastname = $row_responsible_employee['employee_lastname'];
		    ?>
			<div class="row">
				<div class="col-xs-8">
						<h1><i class="fa fa-file-o idk_color_green" aria-hidden="true"></i> <?php echo $nalog_naziv; ?> | <i class="fa fa-briefcase idk_color_green" aria-hidden="true"></i> <?php echo $kompanija; ?></h1>
					</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>nalozi?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-xs-12">
								<?php
									if(isset($_GET['mess'])) {
										$mess = $_GET['mess'];
									}else{
										$mess = 0;
									}

									if($mess == 1){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste dodali dokument.</div>
										<script>$(function() { $('[href="#documents"]').tab('show'); });</script>
									<?php
									}
									elseif($mess == 2){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste obrisali dokument.</div>
										<script>$(function() { $('[href="#documents"]').tab('show'); });</script>
									<?php
									}
									elseif($mess == 3){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste dodali bilješku.</div>
										<script>$(function() { $('[href="#notes"]').tab('show'); });</script>
									<?php
									}
									elseif($mess == 4){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste obrisali bilješku.</div>
										<script>$(function() { $('[href="#notes"]').tab('show'); });</script>
									<?php
									}
									elseif($mess == 5){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste odradili izmjene na nalogu.</div>
										<script>$(function() { $('[href="#info"]').tab('show'); });</script>
									<?php
									}elseif($mess == 6){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste kreirali projekat za nalog.</div>
										<script>$(function() { $('[href="#projects"]').tab('show'); });</script>
									<?php
									}elseif($mess == 7){ ?>
										<div class="alert material-alert material-alert_success">Uspješno ste skinuli projekat sa naloga.</div>
										<script>$(function() { $('[href="#projects"]').tab('show'); });</script>
									<?php
									}elseif($mess == 8){ ?>
										<div class="alert material-alert material-alert_danger">Unijeli ste manji broj rata, molimo da obrišete ratu koju želite ukloniti.</div>
										<script>$(function() { $('[href="#finances"]').tab('show'); });</script>
									<?php
									}elseif($mess == 9){ ?>
										<div class="alert material-alert material-alert_success">Uspiješno ste obrisali ratu.</div>
										<script>$(function() { $('[href="#finances"]').tab('show'); });</script>
									<?php
									}elseif($mess == 10){ ?>
										<div class="alert material-alert material-alert_success">Uspiješno ste unijeli sve rate.</div>
										<script>$(function() { $('[href="#finances"]').tab('show'); });</script>
									<?php
									}elseif($mess == 11){ ?>
										<div class="alert material-alert material-alert_success">Uspiješno ste uredili ratu.</div>
										<script>$(function() { $('[href="#finances"]').tab('show'); });</script>
									<?php
									}elseif($mess == 12){ ?>
										<div class="alert material-alert material-alert_success">Uspiješno ste uredili financijske informacije naloga.</div>
										<script>$(function() { $('[href="#finances"]').tab('show'); });</script>
									<?php
									}
									if(isset($_GET['tab'])){
										$tab=$_GET['tab'];
									}else{
										$tab="info";
									}
								?>
								
							</div>
						</div>
						<div id="myTabs" class="panel-group material-tabs-group">
							<ul class="nav nav-tabs material-tabs material-tabs_primary">
								<li class="<?php if($tab=="info"){echo "active";} ?>"><a href="#info" class="material-tabs__tab-link" data-toggle="tab">Informacije</a></li>
								<li class="<?php if($tab=="opis_partnerapp"){echo "active";} ?>"><a href="#opis_partnerapp" class="material-tabs__tab-link" data-toggle="tab">Partner App</a></li>	
								<li class="<?php if($tab=="notes"){echo "active";} ?>"><a href="#notes" class="material-tabs__tab-link" data-toggle="tab">Bilješke</a></li>
								<li class="<?php if($tab=="documents"){echo "active";} ?>"><a href="#documents" class="material-tabs__tab-link" data-toggle="tab">Dokumenti</a></li>
								<li class="<?php if($tab=="projects"){echo "active";} ?>"><a href="#projects" class="material-tabs__tab-link" data-toggle="tab">Projekti</a></li>
								<li class="<?php if($tab=="linkgen"){echo "active";} ?>"><a href="#linkgen" class="material-tabs__tab-link" data-toggle="tab">Linkovi</a></li>
								<?php if((in_array( "9" , $employee_status)) OR (in_array( "9" , $employee_supervizor))){	?>
								<li class="<?php if($tab=="finances"){echo "active";} ?>"><a href="#finances" class="material-tabs__tab-link" data-toggle="tab">Finansije</a></li>
								<?php } ?>
								<?php if($logged_employee_id == 6677){	?>
								<li class="<?php if($tab=="troskovi"){echo "active";} ?>"><a href="#troskovi" class="material-tabs__tab-link" data-toggle="tab">Troškovi linkova</a></li>
								<?php } ?>
								<li class="<?php if($tab=="kriteriji"){echo "active";} ?>"><a href="#kriteriji" class="material-tabs__tab-link" data-toggle="tab">Kriteriji</a></li>
								<li class="<?php if($tab=="vizadokumenti"){echo "active";} ?>"><a href="#vizadokumenti" class="material-tabs__tab-link" data-toggle="tab">Dokumenti za vizu</a></li>
								<?php if($logged_employee_id == 67 OR $logged_employee_id == 87){ ?>
								<li class="<?php if($tab=="trazi_kandidate"){echo "active";} ?>"><a href="#trazi_kandidate" class="material-tabs__tab-link" data-toggle="tab">Traži kandidate</a></li>
								<?php } ?>
								<li class="<?php if($tab=="kandidati_u_odlasku"){echo "active";} ?>"><a href="#kandidati_u_odlasku" class="material-tabs__tab-link" data-toggle="tab">Kandidati u odlasku</a></li>
								<?php if(in_array( "1" , $employee_status) OR in_array( "2" , $employee_status) OR in_array( "2" , $employee_supervizor)){ ?>
								<li class="<?php if($tab=="jobstep_pp_settings"){echo "active";} ?>"><a href="#jobstep_pp_settings" class="material-tabs__tab-link" data-toggle="tab">JobSoft postavke</a></li>
								<?php } ?>
								<?php if((in_array( "1" , $employee_status)) OR (in_array( "2" , $employee_status))){ ?>
								<li class="<?php if($tab=="termini_casting"){echo "active";} ?>"><a href="#termini_casting" class="material-tabs__tab-link" data-toggle="tab">Termini castinga</a></li>
								<?php } ?>
								<?php if(in_array( "1" , $employee_status) OR in_array( "2" , $employee_status) OR in_array( "18" , $employee_supervizor)){ ?>
								<li class="<?php if($tab=="smjerovi_naloga"){echo "active";} ?>"><a href="#smjerovi_naloga" class="material-tabs__tab-link" data-toggle="tab">Smjerovi naloga</a></li>
								<?php } ?>
							</ul>
							<div class="tab-content materail-tabs-content">
								<div class="tab-pane fade <?php if($tab=="info"){echo "active in";} ?>" id="info">
									<div class="row idk_employee_info">
										<div class="col-md-6">
											<div class="row">
												<div class="col-sm-9">
													<h5>Informacije o nalogu</h5>
												</div>
												<div class="col-sm-3 text-right">
													<a href="nalozi?page=edit&id=<?php echo $nalog_id; ?>" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-pencil" aria-hidden="true"></i> <span></span></a>
												</div>
											</div>

                                            <div class="row">
												<strong class="col-sm-4 text-right">Broj naloga:</strong>
												<div class="col-sm-8"><?php echo $nalog_broj; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Naziv:</strong>
												<div class="col-sm-8"><?php echo $nalog_naziv; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Opis naloga:</strong>
												<div class="col-sm-8"><?php echo $nalog_opis; ?></div>
											</div>
											<?php 
												$resultPartnerForNalog = getJSPartnerForNalogArrayR($nalog_id);
												if ($resultPartnerForNalog["status"] == 1) {
													?>
														<div class="row">
															<strong class="col-sm-4 text-right">Partner:</strong>
															<div class="col-sm-8">
																<span class="label label-success material-label material-label_success main-container__column">
																	<?php 
																		echo $resultPartnerForNalog["partner_name"];
																	?> 
																</span>
															</div>
														</div>
													<?php 
												}
												unset($resultPartnerForNalog); 
											?>											
											<div class="row">
												<strong class="col-sm-4 text-right">Provizija za partnere:</strong>
												<div class="col-sm-8"><?php echo $nalog_partner_provizija; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Kompanija:</strong>
												<div class="col-sm-8"><a href="<?php getSiteURL(); ?>companies?page=open&id=<?php echo $kompanija_id; ?>"><?php echo $kompanija; ?></a></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Kreirano:</strong>
												<div class="col-sm-8"><?php echo $nalog_kreirano; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Trenutni status:</strong>
												<div class="col-sm-8"><?php echo $nalog_status_txt; ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Projekt menadžer:</strong>
												<div class="col-sm-8"><?php if($employee_id == 0){echo "Nije definisan";}else{echo $responsible_name . " " . $responsible_lastname;} ?></div>
											</div>
											<div class="row">
												<strong class="col-sm-4 text-right">Marketing menadžer:</strong>
												<div class="col-sm-8"><?php if($nalog_marketing_menadzer == 0){echo "Nije definisan";}else{echo $employee_firstname . " " . $employee_lastname;} ?></div>
											</div>
											<poslodavac-trazi-jezik
												nalogId="<?php echo $nalog_id; ?>"
												poslodavacTraziJezikNew="<?php echo $nalog_poslodavac_trazi_jezik; ?>"
												poslodavacTraziJezikOld="<?php echo $nalog_poslodavac_trazi_jezik; ?>"
											>
											</poslodavac-trazi-jezik>
											<poslodavac-koristi-pp
												nalogId="<?php echo $nalog_id; ?>"
												poslodavacKoristiPPNew="<?php echo $nalog_poslodavac_koristi_pp; ?>"
												poslodavacKoristiPPOld="<?php echo $nalog_poslodavac_koristi_pp; ?>"
											>
											</poslodavac-koristi-pp>
										<?php if($logged_employee_id == 75 OR $logged_employee_id == 67 OR $logged_employee_id == 134 OR $logged_employee_id == 222 OR $logged_employee_id == 173 OR $logged_employee_id == 79){?>
											 <div class="row" style="height: 40px;">
												<strong class="col-sm-4 text-right" style="margin-top: 7px;">Omogući pristup poslodavcu:</strong>
												<div class="col-sm-8" style="margin-top: 7px;"><?php getProgressPp($nalog_id); ?></div>
												<!-- <div class="col-sm-4"><a href="<?php /*getSiteURL();*/ ?>pristup_poslodavcu?page=input&id=<?php /* echo $nalog_id;*/ ?>" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive" style="margin-left: 150px; margin-top: 3px;"><i class="fa fa-pencil" aria-hidden="true"></i><span><?php /*ppButton($nalog_id);*/ ?></span></a></div> -->
											</div>
										<?php } ?>
										<?php if(in_array( "1" , $employee_status) OR in_array("2", $employee_status)): ?>
											<!--
											<div class="row">
												<strong class="col-sm-4 text-right">Slanje kandidata na Glossu</strong>
												<div class="col-sm-8">
													<ide-na-glossu
														nalogId="<?php echo $nalog_id ?>"
														ideNaGlossu="<?php echo $nalog_slanje_na_glosu ?>"
														>
													</ide-na-glossu>
												</div>
											</div>
											-->
										<?php endif; ?>
											
											<!-- 
												Offers and benefits of order START 
											-->
												<?php 
													include('offers_and_benefits_of_order.php'); 
												?>
											<!-- 
												Offers and benefits of order END  
											-->
											<div class="row">
													<br/>
													<hr/>
													<br/>
												<div class="ccol-xs-12 col-sm-12 col-md-6">
													<div id="canvas-holder" style="width:100%">
														<canvas id="chart-area"></canvas>
													</div>
													<?php
														$status_na_provjeri = 0;
														$status_u_obradi = 1;
														$status_obradjen = 2;
														$status_arhiviran = 3;
														
														$query_projects = $db->prepare("
																		SELECT project_id
																		FROM idk_projects
																		WHERE project_nalogid = :project_nalogid
																		");

														$query_projects->execute(array(
															'project_nalogid' => $nalog_id));
														
														$procjet_ids = [];
														while($row_pro = $query_projects->fetch()){

															$project_id = $row_pro['project_id'];	
															$procjet_ids[] = $project_id;												
														}
														
														$procjet_ids_string = implode(",", $procjet_ids);
														
													?>
													<script>
														window.chartColors = {
															red: 'rgb(243, 65, 60)',
															orange: 'rgb(255, 159, 64)',
															yellow: 'rgb(255, 205, 86)',
															green: 'rgb(102, 213, 102)',
															blue: 'rgb(54, 162, 235)',
															purple: 'rgb(153, 102, 255)',
															grey: 'rgb(201, 203, 207)'
														};	
													
														var randomScalingFactor = function() {
															return Math.round(Math.random() * 100);
														};
												
														var config = {
															type: 'doughnut',
															data: {
																datasets: [{
																	data: [
																		<?php getEmployeesPerNalog($status_arhiviran, $procjet_ids_string); ?>,
																		<?php getEmployeesPerNalog($status_na_provjeri, $procjet_ids_string); ?>,
																		<?php getEmployeesPerNalog($status_u_obradi, $procjet_ids_string); ?>,
																		<?php getEmployeesPerNalog($status_obradjen, $procjet_ids_string); ?>,
																	],
																	backgroundColor: [
																		window.chartColors.red,
																		window.chartColors.orange,
																		window.chartColors.blue,
																		window.chartColors.green
																	],
																	label: 'Dataset 1'
																}],
																labels: [
																	'Arhiviran (<?php getEmployeesPerNalog($status_arhiviran, $procjet_ids_string); ?>)',
																	'Na provjeri (<?php getEmployeesPerNalog($status_na_provjeri, $procjet_ids_string); ?>)',
																	'U obradi (<?php getEmployeesPerNalog($status_u_obradi, $procjet_ids_string); ?>)',
																	'Obrađen (<?php getEmployeesPerNalog($status_obradjen, $procjet_ids_string); ?>)'
																]
															},
															options: {
																responsive: true,
																legend: {
																	position: 'left',
																},
																title: {
																	display: true,
																	text: 'Statistika kandidata za <?php echo $nalog_naziv; ?>'
																},
																animation: {
																	animateScale: true,
																	animateRotate: true
																}
															}
														};
												
														window.onload = function() {
															var ctx = document.getElementById('chart-area').getContext('2d');
															window.myDoughnut = new Chart(ctx, config);
														};
													</script>
												</div>
											</div>											
										</div>
										<div class="col-md-6">
											<div class="row">
												<div class="col-sm-9">
													<h5>Log statusa</h5>
												</div>
											</div>
											<div class="table-responsive">
												<table class="table table-striped">
													<tbody>
														<?php
															$query_logs = $db->prepare("
																				SELECT n_log_id, n_log_nalogid, n_log_employeeid, n_log_status, n_log_vrijeme, emp.employee_firstname, emp.employee_lastname
                                                                                FROM idk_nalozi_log
                                                                                INNER JOIN idk_employees emp ON n_log_employeeid = emp.	employee_id
																				WHERE n_log_nalogid = :n_log_nalogid
																				ORDER BY n_log_id DESC");

															$query_logs->execute(array(
                                                                    ':n_log_nalogid' => $nalog_id));
                                                                    
															while($row_logs = $query_logs->fetch()){

                                                                $log_id = $row_logs['n_log_id'];
																$uposlenik_ime = $row_logs['employee_firstname'];
																$uposlenik_prezime = $row_logs['employee_lastname'];
																$nalog_status = $row_logs['n_log_status'];
                                                                $log_kreirano = date('d.m.Y. H:i', strtotime($row_logs['n_log_vrijeme']));

                                                                if($nalog_status == 1){
                                                                    $nalog_status_txt = '<span class="label label-primary">Potpis</span>';
                                                                }elseif($nalog_status == 2){
                                                                    $nalog_status_txt = '<span class="label label-warning">Čeka se uplata</span>';
                                                                }elseif($nalog_status == 3){
                                                                    $nalog_status_txt = '<span class="label label-primary">Marketing</span>';
                                                                }elseif($nalog_status == 4){
                                                                    $nalog_status_txt = '<span class="label label-warning">Prijave u toku</span>';
                                                                }elseif($nalog_status == 5){
                                                                    $nalog_status_txt = '<span class="label label-primary">Obrada prijava</span>';
                                                                }elseif($nalog_status == 6){
                                                                    $nalog_status_txt = '<span class="label label-warning">Nalog kod poslodavca</span>';
                                                                }elseif($nalog_status == 7){
                                                                    $nalog_status_txt = '<span class="label label-primary">Casting</span>';
                                                                }elseif($nalog_status == 8){
                                                                    $nalog_status_txt = '<span class="label label-success">Završeno</span>';
                                                                }elseif($nalog_status == 9){
                                                                    $nalog_status_txt = '<span class="label label-danger">Na čekanju</span>';
                                                                }elseif($nalog_status == 10){
                                                                    $nalog_status_txt = '<span class="label label-success">Kandidati u odlasku</span>';
                                                                }elseif($nalog_status == 11){
                                                                    $nalog_status_txt = '<span class="label label-info">Završeno (nenaplaćeno)</span>';
                                                                }elseif($nalog_status == 12){
																	$nalog_status_txt = '<span class="label label-danger">Arhiviran</span>';
																}
																
														?>
														<tr>
															<td class="text-center"><?php echo $uposlenik_ime, ' ', $uposlenik_prezime;  ?></td>
															<td class="text-center"><?php echo $nalog_status_txt; ?></td>
															<td class="text-right"><?php echo $log_kreirano; ?></td>
														</tr>
														<?php } ?>
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane fade <?php if($tab=="notes"){echo "active in";} ?>" id="notes">
									<ul class="list-inline text-right">
										<li><a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#noteModal"><i class="fa fa-sticky-note-o" aria-hidden="true"></i> <span>Dodaj bilješku</span></a></li>
										<!-- Modal add note -->
										<div class="modal material-modal material-modal_primary fade text-left" id="noteModal">
											<div class="modal-dialog ">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Dodaj bilješku</h4>
													</div>
													<div class="modal-body material-modal__body">
														<form action="<?php getSiteURL(); ?>do.php?form=add_nalog_note" method="post" role="form" class="form-horizontal">
															<input type="hidden" name="note_dataid" value="<?php echo $nalog_id; ?>" />
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="form-group materail-input-block materail-input-block_success">
																		<textarea class="form-control materail-input material-textarea" name="note_txt" placeholder="Bilješka" rows="6" required></textarea>
																		<span class="materail-input-block__line"></span>
																	</div>
																</div>
															</div>
													</div>
													<div class="modal-footer material-modal__footer">
														<ul class="list-inline">
															<li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
															<li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Dodaj</button></li>
														</ul>
														</form>
													</div>
												</div>
											</div>
										</div>
										<!-- Modal add note end -->
									</ul>
									<hr>
									<div class="panel-group material-accordion material-accordion_primary" id="accordion1">
										<?php

											$year_query = $db->prepare("
																SELECT YEAR (note_datetime) AS note_datetime_year
																FROM idk_notes
																WHERE note_dataid = :note_dataid AND note_group = :note_group
																GROUP BY YEAR (note_datetime)
																ORDER BY YEAR (note_datetime) DESC");

											$year_query->execute(array(
															':note_dataid' => $nalog_id,
															':note_group' => 4));

											while($year_row = $year_query->fetch()){

												$note_datetime_year = $year_row['note_datetime_year'];

												if($note_datetime_year == date('Y')){
													$idk_notes_in = "in";
												}else{
													$idk_notes_in = "";
												}

										?>
										<div class="panel panel-default material-accordion__panel material-accordion__panel">
											<div class="panel-heading material-accordion__heading">
												<h4 class="panel-title">
													<a class="material-accordion__title" data-toggle="collapse" data-parent="#accordion1" href="#<?php echo $note_datetime_year; ?>"><?php echo $note_datetime_year; ?></a>
												</h4>
											</div>
											<div id="<?php echo $note_datetime_year; ?>" class="panel-collapse <?php echo $idk_notes_in; ?> collapse material-accordion__collapse">
												<div class="panel-body">
													<?php
														$notes_query = $db->prepare("
																		SELECT note_id, note_datetime, note_txt, employee_firstname, employee_lastname
																		FROM idk_notes
																		INNER JOIN idk_employees ON idk_notes.note_employeeid = idk_employees.employee_id
																		WHERE YEAR (note_datetime) = :note_datetime_year AND note_dataid = :note_dataid AND note_group = :note_group
																		ORDER BY note_datetime DESC");

														$notes_query->execute(array(
																		':note_datetime_year' => $note_datetime_year,
																		':note_group' => 4,
																		':note_dataid' => $nalog_id));

														while($notes_row = $notes_query->fetch()){

															$note_date = date('d.m.Y.', strtotime($notes_row['note_datetime']));
															$note_time = date('H:i', strtotime($notes_row['note_datetime']));
															$note_id = $notes_row['note_id'];
															$note_txt = $notes_row['note_txt'];
															$employee_firstname = $notes_row['employee_firstname'];
															$employee_lastname = $notes_row['employee_lastname'];

													?>
													<div class="row">
														<div class="col-sm-3">
															<p><i class="fa fa-calendar text-primary" aria-hidden="true"></i> <?php echo $note_date; ?> | <i class="fa fa-clock-o text-primary" aria-hidden="true"></i> <?php echo $note_time; ?></p>
															<p><i class="fa fa-user text-primary" aria-hidden="true"></i> <?php echo $employee_firstname; ?> <?php echo $employee_lastname; ?></p>
														</div>
														<div class="col-sm-7">
															<p><?php echo $note_txt; ?></p>
														</div>
														<div class="col-sm-2 text-right">
															<a href="#" data="<?php getSiteURL(); ?>nalozi?page=del_note&id=<?php echo $note_id; ?>" data-toggle="modal" data-target="#deleteNoteModal" class="delete_note btn material-btn material-btn_danger main-container__column"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i></a>
															<script>
																$(".delete_note").click(function () {
																	var addressValue = $(this).attr("data");
																	document.getElementById("delete_note_link").href = addressValue;
																});
															</script>
															<!-- Modal -->
															<div class="modal material-modal material-modal_danger fade text-left" id="deleteNoteModal">
																<div class="modal-dialog">
																	<div class="modal-content material-modal__content">
																		<div class="modal-header material-modal__header">
																			<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																			<h4 class="modal-title material-modal__title">Brisanje</h4>
																		</div>
																		<div class="modal-body material-modal__body">
																			<p>Jeste li sigurni da želite obrisati bilješku?</p>
																		</div>
																		<div class="modal-footer material-modal__footer">
																			<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
																			<a id="delete_note_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<hr />
													<?php } ?>
												</div>
											</div>
										</div>
										<?php } ?>
									</div>
								</div>
								<div class="tab-pane fade <?php if($tab=="documents"){echo "active in";} ?>" id="documents">
									<ul class="list-inline text-right">
										<li><a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#docModal"><i class="fa fa-file-text-o" aria-hidden="true"></i> <span>Dodaj dokument</span></a></li>
										<!-- Modal add document -->
										<div class="modal material-modal material-modal_primary fade text-left" id="docModal">
											<div class="modal-dialog ">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Dodaj dokument</h4>
													</div>
													<div class="modal-body material-modal__body">
														<form action="<?php getSiteURL(); ?>do.php?form=add_nalog_doc" enctype="multipart/form-data" method="post" role="form" class="form-horizontal">
															<div id="idk_alert_size" class="row hidden">
																<div class="col-sm-12">
																	<div class="alert material-alert material-alert_danger">Greška: Dokument koji pokuštavate dodati je veći od dozvoljene veličine.</div>
																</div>
															</div>
															<div id="idk_alert_ext" class="row hidden">
																<div class="col-sm-12">
																	<div class="alert material-alert material-alert_danger">Greška: Format dokumenta kojeg pokušavate dodati nije dozvoljen.</div>
																</div>
															</div>
															<input type="hidden" name="document_dataid" value="<?php echo $nalog_id; ?>" />
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="form-group materail-input-block materail-input-block_success">
																		<input type="text" class="form-control materail-input" name="document_name" id="document_name" placeholder="Naziv dokumenta" required>
																		<span class="materail-input-block__line"></span>
																	</div>
																</div>
															</div>
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="form-group materail-input-block materail-input-block_success">
																		<input type="text" class="form-control materail-input" name="document_desc" id="document_desc" placeholder="Opis dokumenta">
																		<span class="materail-input-block__line"></span>
																	</div>
																</div>
															</div>
															<div class="form-group">
																<div class="col-md-offset-2 col-sm-8">
																	<div class="fileinput fileinput-new" data-provides="fileinput">
																		<span class="btn btn-default btn-file">
																			<span class="fileinput-new">Izaberi dokument</span>
																			<span class="fileinput-exists">Promijeni</span>
																			<input type="file" name="document_file" id="document_file" required required>
																		</span>
																		<i class="fa fa-info-circle fa-lg idk_margin_left10" data-toggle="tooltip" data-placement="right" title="Napomena: Dokument ne smije biti veći od 20MB! - Dozvoljeni formati: jpg, jpeg, pdf, doc, docx, xls, xlsx, txt, ppt, pptx, png" aria-hidden="true"></i>
																		<span class="fileinput-filename"></span>
																		<a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">×</a>
																		<script>
																			$(function (){
																				$('#document_file').change(function (){

																					var ext = $('#document_file').val().split('.').pop().toLowerCase();

																					if($.inArray(ext, ['jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png']) == -1) {
																						$('#idk_alert_ext').removeClass('hidden');
																						this.value = null;
																					}else{
																						$('#idk_alert_ext').addClass('hidden');
																					}

																					var f = this.files[0];

																					if (f.size > 20388608 || f.fileSize > 20388608){
																						$('#idk_alert_size').removeClass('hidden');
																						this.value = null;
																					}else{
																						$('#idk_alert_size').addClass('hidden');
																					}


																				})
																			});
																		</script>
																	</div>
																</div>
															</div>
													</div>
													<div class="modal-footer material-modal__footer">
															<ul class="list-inline">
																<li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
																<li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Dodaj</button></li>
															</ul>
														</form>
													</div>
												</div>
											</div>
										</div>
										<!-- Modal add document end -->
									</ul>
									<hr>
									<script type="text/javascript">
										$(document).ready(function() {
											$('#idk_table_documents').DataTable({

												"order": [[ 0, "desc" ]],

												 "bAutoWidth": false,

												"aoColumns": [
														{ "width": "10%" },
														{ "width": "40%" },
														{ "width": "40%" },
														{ "width": "5%", "bSortable": false },
														{ "width": "5%", "bSortable": false }
													]
											});
										} );
									</script>
									<table id="idk_table_documents" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th>Datum</th>
												<th>Naziv</th>
												<th>Opis</th>
												<th>Preuzimanje</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<?php
												$query_doc = $db->prepare("
																SELECT document_id, document_name, document_desc, document_file, document_icon, document_datetime
																FROM idk_documents
																WHERE document_group = :document_group AND document_dataid = :document_dataid");

												$query_doc->execute(array(
															':document_group' => 4,
															':document_dataid' => $nalog_id));

												while($row_doc = $query_doc->fetch()){

													$document_id = $row_doc['document_id'];
													$document_name = $row_doc['document_name'];
													$document_desc = $row_doc['document_desc'];
													$document_file = $row_doc['document_file'];
													$document_datetime = date('d.m.Y.', strtotime($row_doc['document_datetime']));

													if($row_doc['document_icon'] == "jpg"){
														$document_icon = '<i class="fa fa-file-image-o fa-lg" aria-hidden="true"></i>';
													}elseif($row_doc['document_icon'] == "pdf"){
														$document_icon = '<i class="fa fa-file-pdf-o fa-lg" aria-hidden="true"></i>';
													}elseif($row_doc['document_icon'] == "doc" OR $row_doc['document_icon'] == "docx"){
														$document_icon = '<i class="fa fa-file-word-o fa-lg" aria-hidden="true"></i>';
													}elseif($row_doc['document_icon'] == "xls" OR $row_doc['document_icon'] == "xlsx"){
														$document_icon = '<i class="fa fa-file-excel-o fa-lg" aria-hidden="true"></i>';
													}elseif($row_doc['document_icon'] == "txt"){
														$document_icon = '<i class="fa fa-file-text-o fa-lg" aria-hidden="true"></i>';
													}elseif($row_doc['document_icon'] == "ppt" OR $row_doc['document_icon'] == "pptx"){
														$document_icon = '<i class="fa fa-file-powerpoint-o fa-lg" aria-hidden="true"></i>';
													}else{
														$document_icon = '<i class="fa fa-file-o fa-lg" aria-hidden="true"></i>';
													}
											?>
											<tr>
												<td class="text-center"><?php echo $document_datetime; ?></td>
												<td><?php echo $document_name; ?></td>
												<td><?php echo $document_desc; ?></td>
												<td class="text-center"><a href="<?php getSiteURL(); ?>files/files/nalozi/<?php echo $document_file; ?>" class="btn material-btn material-btn_success main-container__column" target="_BLANK"><?php echo $document_icon; ?></a></td>
												<td class="text-center"><a href="#" data="<?php getSiteURL(); ?>nalozi?page=del_doc&id=<?php echo $document_id; ?>" data-toggle="modal" data-target="#deleteDocModal" class="delete_doc btn material-btn material-btn_danger main-container__column"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i></a></td>
											</tr>
											<?php } ?>
											<script>
												$(".delete_doc").click(function () {
													var addressValue = $(this).attr("data");
													document.getElementById("delete_doc_link").href = addressValue;
												});
											</script>
											<!-- Modal -->
											<div class="modal material-modal material-modal_danger fade" id="deleteDocModal">
												<div class="modal-dialog">
													<div class="modal-content material-modal__content">
														<div class="modal-header material-modal__header">
															<button class="close material-modal__close" data-dismiss="modal">&times;</button>
															<h4 class="modal-title material-modal__title">Brisanje</h4>
														</div>
														<div class="modal-body material-modal__body">
															<p>Jeste li sigurni da želite obrisati dokument?</p>
														</div>
														<div class="modal-footer material-modal__footer">
															<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
															<a id="delete_doc_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
														</div>
													</div>
												</div>
											</div>
										</tbody>
									</table>
								</div>
								<script>
								$(document).ready(function(){
									
//var timeOut = 0;
//$(".reorder-process-list").mousedown(function () {
//    clearTimeout(timeOut);
//    timeOut = setTimeout(function () {
//        $(".reorder-process-list").sortable({ tolerance: 'pointer' });
//		$('.reorder_link').html('<i class="fa fa-floppy-o" aria-hidden="true"></i> Sacuvaj');
//		$('.reorder_link').attr("id","save_reorder");
//		$('.proc_link').attr("href","javascript:void(0);");
//		$('.proc_link').css("cursor","move");	
//    }, 1000);
//});	
//$("#save_reorder").click(function( e ){
//		//$(this).html('').prepend('<img src="images/refresh-animated.gif"/>');
//		$(".reorder-process-list").sortable('destroy');
//		$("#reorder-helper").html( "Izmjenjujem ordere. Molimo Vas ne izlazite sa stranice dok se ne zavrsi" ).removeClass('light_box').addClass('notice notice_error');
//
//		var h = [];
//		$(".list-group-items").each(function() {  h.push($(this).attr('id').substr(9));  });
//		//alert(h);
//		
//		$.ajax({
//			type: "POST",
//			url: "<?php getSiteURL(); ?>nalozi?page=reorderProjects",
//			data: {ids: " " + h + "", type: "process"},
//			success: function(data){
//				window.location.reload();
//			}
//		}); 
//		return false;
//	
//	e.preventDefault();     
//});				
									
									$('.reorder_link').on('click',function(){
										$(".reorder-process-list").sortable({ tolerance: 'pointer' });
										$('.reorder_link').html('<i class="fa fa-floppy-o" aria-hidden="true"></i> Sacuvaj');
										$('.reorder_link').attr("id","save_reorder");
										$('#reorder-helper').slideDown('slow');
										$('.proc_link').attr("href","javascript:void(0);");
										$('.proc_link').css("cursor","move");
										$("#save_reorder").click(function( e ){
												//$(this).html('').prepend('<img src="images/refresh-animated.gif"/>');
												$(".reorder-process-list").sortable('destroy');
												$("#reorder-helper").html( "Izmjenjujem ordere. Molimo Vas ne izlazite sa stranice dok se ne zavrsi" ).removeClass('light_box').addClass('notice notice_error');
									
												var h = [];
												$(".list-group-items").each(function() {  h.push($(this).attr('id').substr(9));  });
												//alert(h);
												
												$.ajax({
													type: "POST",
													url: "<?php getSiteURL(); ?>nalozi?page=reorderProjects",
													data: {ids: " " + h + "", type: "process"},
													success: function(data){
														window.location.reload();
													}
												}); 
												return false;
											
											e.preventDefault();     
										});
									});
								});
								</script>								
								<div class="tab-pane fade <?php if($tab=="projects"){echo "active in";} ?>" id="projects">
									<div class="row">
										<div class="col-xs-12 text-right idk_margin_top10">
											<?php
												$query_get_candidate_count_from_queue = $db -> prepare('
													SELECT count(q.queue_id) as queue_count
													FROM idk_project_candidate_queue q
													JOIN idk_projects pr
													ON pr.project_id = q.queue_project_id
													WHERE pr.project_nalogid = :nalog_id
													AND q.queue_is_assigned = 0
												');
												$query_get_candidate_count_from_queue -> execute(array(':nalog_id' => $nalog_id));
												$row_get_candidate_count_from_queue = $query_get_candidate_count_from_queue -> fetch();
												$queue_count = $row_get_candidate_count_from_queue['queue_count'];
												$queue_link = "#";
												if($queue_count != "#"){
													$queue_link = getSiteUrlr().'queue_candidates_list?nalog_id='.$nalog_id;
													
												}
											?>
											<a href="<?php echo $queue_link;?>" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa" style = "font-weight: bolder; font-size: 17px;" aria-hidden="true"><?php echo $queue_count;?></i> <span>Kandidata u redu</span></a>
											<?php if($logged_employee_id == 63 or $logged_employee_id == 67){ ?>
												<a href="#" id="obicni_export" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-file-o" aria-hidden="true"></i> Export svih kandidata</a>
											<?php } ?>
											<a href="javascript:void(0);" class="btn outlined mleft_no reorder_link btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive" id="save_reorder"><i class="fa fa-refresh" aria-hidden="true"></i> Izmjeni raspored</a>						
			
											<a href="<?php getSiteURL(); ?>projects?page=add&id=<?php echo $nalog_id; ?>" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Kreiraj projekat</span></a>
											
										</div>
										<div class="col-xs-12">
											<hr />
										</div>
									</div>
									
									<div class="row">
										<div class="col-md-12">
											<div class="content_box">
												<div class="row">
													<div class="col-xs-12">
														<?php
															if(isset($_GET['mess'])) {
																$mess = $_GET['mess'];
															}else{
																$mess = 0;
															}

															if($mess == 1){
																echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novi projekat.</div>';
															}elseif($mess == 3){
																echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili profil projekta.</div>';
															}elseif($mess == 4){
																echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali projekat.</div>';
															}
														?>
														<script type="text/javascript">
															$(document).ready(function() {
																$('#idk_table').DataTable({

																	responsive: true,

																	"order": [[ 0, "desc" ]],

																	"bAutoWidth": false,

																	"aoColumns": [
																			{ "width": "2%" },
																			{ "width": "36%" },
																			{ "width": "7%" },
																			{ "width": "10%" },
																			{ "width": "10%" },
																			{ "width": "10%" },
																			{ "width": "5%", "bSortable": false }
																		]
																});
															} );
														</script>
														<table id="idk_table" class="display" cellspacing="0" width="100%">
															<thead>
																<tr>
																	<th>ID</th>
																	<th>Naziv</th>
																	<th>Kandidata</th>
																	<th>Datum termina</th>
																	<th>Status projekta</th>
																	<th class="text-center">Kreirano</th>
																	<th></th>
																</tr>
															</thead>
															<tbody class="reorder-process-list">
																<?php
																	$idoviKandidataUProjektu = array();
																	$query = $db->prepare("
																					SELECT project_id, project_name, project_datetime, project_status, project_nalogid, project_order, project_datumtermina, project_datumterminado
																					FROM idk_projects
																					WHERE project_nalogid = :project_nalogid
																					ORDER BY project_order, project_id ASC
																					");

																	$query->execute(array(
																		'project_nalogid' => $nalog_id));
																	
																	$count = 1;
																	while($row = $query->fetch()){

																		$projectIds = array();
																		$project_id = $row['project_id'];
																		$project_order = $row['project_order'];
																		$project_name = $row['project_name'];
																		$project_datetime = '<span class="label label-success">'.date('d.m.Y. - H:i', strtotime($row['project_datetime'])).'</span>';
																		$project_status = $row['project_status'];
																		$project_datumtermina = $row['project_datumtermina'];
																		$project_datumterminado = $row['project_datumterminado'];
																		if($project_datumterminado == null){
																			$datumterminado_f = "NN";
																		}else{
																			$datumterminado_f = date("F Y", strtotime($project_datumterminado));
																		}
																		
																		if($project_datumtermina != NULL){
																			$project_mjesec_termina = '<span class="label label-success">'.date("F Y", strtotime($project_datumtermina)).' do '.$datumterminado_f.'</span>';
																		}else{
																			$project_mjesec_termina = '<span class="label label-warning">Nije definisan</span>';
																		}
																		
																		$brojKandidataUProjektu = getNumberOfCandidatesProject($project_id);
																		$projectIds = getIdsOfCandidatesProject($project_id);
																		if(!empty($projectIds))
																			array_push($idoviKandidataUProjektu, $projectIds);

																		if($project_status == 1){
																			$project_status_txt = '<span class="label label-primary">U toku</span>';
																		}elseif($project_status == 0){
																			$project_status_txt = '<span class="label label-success">Završeno</span>';
																		}
																		if($nalog_pristup == 1 AND strpos($project_name, ' - Casting')){
																			$casting_link = getSiteUrlr()."casting?page=open&id=".$project_id;
																		}else{
																			$casting_link = getSiteUrlr()."projects?page=open&id=".$project_id;
																		}
																		
																?>
																<tr id="image_li_<?php echo $project_id; ?>" class="list-group-items">
																<div style="float:none;" class="proc_link">
																	<td><?php echo $count++; ?></td>
																	<td><a href="<?php echo $casting_link; ?>"><?php echo $project_name; ?></a></td>
																	<td class="text-center"><?php echo $brojKandidataUProjektu; ?> </td>
																	<td class="text-center"><?php echo $project_mjesec_termina; ?> </td>
																	<td class="text-center"><?php echo $project_status_txt; ?></td>
																	<td class="text-center"><?php echo $project_datetime; ?></td>
																	<td class="text-center">
																		<div class="btn-group material-btn-group">
																			<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
																			<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
																				<li><a href="<?php getSiteURL(); ?>projects?page=open&id=<?php echo $project_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
																				<li><a href="<?php getSiteURL(); ?>projects?page=edit&id=<?php echo $project_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi</a></li>
																				<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>nalozi?page=project_disconnect&id=<?php echo $project_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Ukloni</a></li>
																			</ul>
																		</div>
																	</td>
																</div>
																</tr>
																<?php } ?>
																
																<script>
																	$(".archive").click(function () {
																		var addressValue = $(this).attr("data");
																		document.getElementById("archive_link").href = addressValue;
																	});
																	$('#obicni_export').on('click', function(e){
																
																		var passedArray = <?php echo json_encode($idoviKandidataUProjektu); ?>; 
																		console.log(passedArray);
																		// var selectedIds = $('#candidate_ids').find(".checkbox").map(function(){return $(this).val(); }).get();
																		
																		$('#exportajax').load('<?php getSiteUrl(); ?>export_excel.php?prozor=export_kandidata_u_projektu&selectedis='+passedArray+'');
																		
																		// return false;
								
																		e.preventDefault();
																	});
																</script>
																<div id="exportajax"></div>	
																<!-- Modal -->
																<div class="modal material-modal material-modal_danger fade" id="archiveModal">
																	<div class="modal-dialog">
																		<div class="modal-content material-modal__content">
																			<div class="modal-header material-modal__header">
																				<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																				<h4 class="modal-title material-modal__title">Ukloni projekat sa naloga</h4>
																			</div>
																			<div class="modal-body material-modal__body">
																				<p>Jeste li sigurni da želite ukloniti projekat sa naloga?</p>
																			</div>
																			<div class="modal-footer material-modal__footer">
																				<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
																				<a id="archive_link" href=""><button class="btn btn-primary material-btn material-btn_danger">UKLONI</button></a>
																			</div>
																		</div>
																	</div>
																</div>
															</tbody>
														</table>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane fade <?php if($tab=="linkgen"){echo "active in";} ?>" id="linkgen">
									<div class="row">
										<div class="col-md-12">
											<div class="content_box">
												<div class="row">
													<div class="col-xs-12">
														<?php
															if(isset($_GET['mess'])) {
																$mess = $_GET['mess'];
															}else{
																$mess = 0;
															}

															if($mess == 1){
																echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novi projekat.</div>';
															}elseif($mess == 3){
																echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili profil projekta.</div>';
															}elseif($mess == 4){
																echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali projekat.</div>';
															}
														?>
														<script type="text/javascript">
															$(document).ready(function() {
																$('#idk_tablelimnkgen').DataTable({
							
																	responsive: true,
							
																	"order": [[ 0, "desc" ]],
							
																	"bAutoWidth": false,
							
																	"aoColumns": [
																			{ "width": "5%", "bSortable": false },
																			{ "width": "23%" },
																			{ "width": "15%" },
																			{ "width": "5%" },
																			{ "width": "5%" },
																			{ "width": "5%" },
																			{ "width": "10%" },
																			{ "width": "25%" },
																			{ "width": "7%", "bSortable": false }
																		]
																});
															} );
														</script>
														<script>
														function copyToClipboard(element) {
															var $temp = $("<input>");
															$("body").append($temp);
															$temp.val($(element).text()).select();
															document.execCommand("copy");
															$temp.remove();
														}
														</script>
														<table id="idk_tablelimnkgen" class="display" cellspacing="0" width="100%">
															<thead>
																<tr>
																	<th></th>
																	<th>Url</th>
																	<th>Opis</th>
																	<th class="text-center">Prijava</th>
																	<th>Jezik prijave</th>
																	<th>Link prijave</th>
																	<th>Registracija url</th>
																	<th>Vrijeme / Dodao</th>
																	<th></th>
																</tr>
															</thead>
															<tbody>
																<?php
																	$nalogidLinkovi = $_GET['id'];
																	
																	$query = $db->prepare("
																					SELECT lg_id, lg_link_prijave, lg_url, lg_desc, lg_datetime, employee_firstname, employee_lastname, lg_language
																					FROM idk_link_generator
																					INNER JOIN idk_employees ON idk_link_generator.lg_employeeid = idk_employees.employee_id
																					WHERE lg_status = 0 AND lg_nalogid = $nalogidLinkovi
																					ORDER BY lg_id DESC
																					");
							
																	$query->execute();
																	$i=1;
																	while($row = $query->fetch()){
							
																		$lg_id = $row['lg_id'];
																		$lg_link_prijave = $row['lg_link_prijave'];
																		$lg_url = $row['lg_url'];
																		$lg_desc = $row['lg_desc'];
																		$lg_datetime = $row['lg_datetime'];
																		$employee_firstname = $row['employee_firstname'];
																		$employee_lastname = $row['employee_lastname'];
																		$lg_language = $row['lg_language'];
																		$lg_datetimef = date('d.m.Y H:i', strtotime($lg_datetime));
							
																		// BROJ PRIJAVLJENIH
																		$query_check_numbers = $db->prepare("
																						SELECT kandidat_id
																						FROM idk_kandidati
																						WHERE kandidat_visitedurl = :kandidat_visitedurl AND kandidat_status != 3
																						");
							
																		$query_check_numbers->execute(array(
																			":kandidat_visitedurl" => $lg_id
																		));
																		$check = $query_check_numbers->rowCount();
							
																		if($check > 0){
																			$check_txt = '<span class="label label-success material-label material-label_success main-container__column">'.$check.'</span>';
																		}else{
																			$check_txt = '<span class="label label-warning material-label material-label_warning main-container__column">'.$check.'</span>';
																		}
																		
																		if($lg_link_prijave == null OR $lg_link_prijave == ""){
																			$span_link_prijave = '<span class="label cursor label-danger material-label material-label_danger main-container__column">';
																			$href_link_prijave = "#";
																			$target_blank = "";
																		}else{
																			$span_link_prijave = '<span class="label cursor label-success material-label material-label_success main-container__column">';
																			$href_link_prijave = $lg_link_prijave;
																			$target_blank = 'target="_BLANK"';
																		}
							
																?>
																<tr>
																	<td class="text-center"><?php echo $lg_id; ?></td>
																	<td><?php echo $lg_url; ?></td>
																	<td><?php echo $lg_desc; ?></td>
																	<td class="text-center"><a href="<?php getSiteUrl(); ?>link_generator.php?page=show_list&id=<?php echo $lg_id; ?>"><?php echo $check_txt; ?></a></td>
																	<td class="text-center"><?php echo strtoupper($lg_language); ?></td>
																	<td class="text-center"><a href="<?php echo $href_link_prijave; ?>" <?php echo $target_blank; ?> ><?php echo $span_link_prijave; ?><i class="fa fa-external-link" aria-hidden="true"></i></span></a></td>
																	<td>
																		<span style="overflow:hidden;width:0px;height:0px;opacity:0;" id="p<?php echo $lg_id; ?>" class="label label-default material-label main-container__column"><?php getSiteUrl(); ?>registracija/<?php echo $lg_id; ?>/korak1</span>
																		<span data-toggle="tooltip" data-placement="top" title="KOPIRAJ URL" aria-hidden="true" onclick="copyToClipboard('#p<?php echo $lg_id; ?>')" class="label cursor label-success material-label material-label_success main-container__column">
																			<i class="fa fa-clipboard" aria-hidden="true"></i>
																		</span>
																	</td>
																	<td><span class="label label-success material-label material-label_success main-container__column"><?php echo $lg_datetimef; ?> <?php echo $employee_firstname; ?> <?php echo $employee_lastname; ?></span></td>
																	<td class="text-center">
																		<div class="btn-group material-btn-group">
																			<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
																			<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
							
																				<li><a href="<?php getSiteURL(); ?>link_generator?page=edit&id=<?php echo $lg_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi</a></li>
							
																				<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>link_generator?page=archive&id=<?php echo $lg_id; ?>" data-toggle="modal" data-target="#archiveLinkModal" class="material-dropdown-menu__link archive"><i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj</a></li>
							
																			</ul>
																		</div>
																	</td>
																</tr>
																<?php } ?>
																<script>
																	$(".archive").click(function () {
																		var addressValue = $(this).attr("data");
																		document.getElementById("archive_link_nalog").href = addressValue;
																	});
																</script>
																<!-- Modal -->
																<div class="modal material-modal material-modal_danger fade"  id="archiveLinkModal">
																	<div class="modal-dialog">
																		<div class="modal-content material-modal__content">
																			<div class="modal-header material-modal__header">
																				<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																				<h4 class="modal-title material-modal__title">Arhiviranje</h4>
																			</div>
																			<div class="modal-body material-modal__body">
																				<p>Jeste li sigurni da želite arhivirati link?</p>
																			</div>
																			<div class="modal-footer material-modal__footer">
																				<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
																				<a id="archive_link_nalog" href=""><button class="btn btn-primary material-btn material-btn_danger">ARHIVIRAJ</button></a>
																			</div>
																		</div>
																	</div>
																</div>
															</tbody>
														</table>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<?php if(in_array( "1" , $employee_status)){	?>
								<div class="tab-pane fade <?php if($tab=="troskovi"){echo "active in";} ?>" id="troskovi">
									<?php if($logged_employee_id == 6677) {	?>
									<div class="row">
										<div class="col-md-12">
											<div class="content_box">
												<div class="row">
													<div class="col-xs-12">
														
														<script type="text/javascript">
															$(document).ready(function() {
																$('#idk_tablelimnkgen2').DataTable({
							
																	responsive: true,
							
																	"order": [[ 0, "desc" ]],
							
																	"bAutoWidth": false,
							
																	"aoColumns": [
																			{ "width": "5%" },
																			{ "width": "20%" },
																			{ "width": "19%" },
																			{ "width": "8%" },
																			{ "width": "8%" },
																			{ "width": "8%" },
																			{ "width": "8%" },
																			{ "width": "8%" },
																			{ "width": "8%" },
																			{ "width": "8%" }
																		]
																});
															} );
														</script>
														<script>
														function copyToClipboard(element) {
															var $temp = $("<input>");
															$("body").append($temp);
															$temp.val($(element).text()).select();
															document.execCommand("copy");
															$temp.remove();
														}
														</script>
														<table id="idk_tablelimnkgen2" class="display" cellspacing="0" width="100%">
															<thead>
																<tr>
																	<th class="text-center">ID</th>
																	<th>Url</th>
																	<th>Opis</th>
																	<th class="text-center">Prijava</th>
																	<th class="text-center">Na provjeri</th>
																	<th class="text-center">U obradi</th>
																	<th class="text-center">Obrađen</th>
																	<th class="text-center">Arhiviran</th>
																	<th class="text-center">Troskovi</th>
																	<th class="text-center">Provizija</th>
																</tr>
															</thead>
															<tbody>
																<?php
																	$nalogidLinkovi = $_GET['id'];
																	
																	$query = $db->prepare("
																					SELECT lg_id, lg_link_prijave, lg_url, lg_desc, lg_datetime, employee_firstname, employee_lastname, lg_troskovi
																					FROM idk_link_generator
																					INNER JOIN idk_employees ON idk_link_generator.lg_employeeid = idk_employees.employee_id
																					WHERE lg_status = 0 AND lg_nalogid = $nalogidLinkovi
																					ORDER BY lg_id DESC
																					");
							
																	$query->execute();
																	$i=1;
																	$ukupna_zarada = 0;
																	$ukupni_trosak = 0;
																	while($row = $query->fetch()){
							
																		$lg_id = $row['lg_id'];
																		$lg_link_prijave = $row['lg_link_prijave'];
																		$lg_url = $row['lg_url'];
																		$lg_desc = $row['lg_desc'];
																		$lg_datetime = $row['lg_datetime'];
																		$lg_troskovi = $row['lg_troskovi'];
																		$employee_firstname = $row['employee_firstname'];
																		$employee_lastname = $row['employee_lastname'];
																		$lg_datetimef = date('d.m.Y H:i', strtotime($lg_datetime));
																		
																		if(strlen($lg_desc) > 40){
																			$lg_desc = substr($lg_desc, 0, 40)."...";
																		}
																		
																		$br_naprovjeri = 0;
																		$br_uobradi = 0;
																		$br_obraden = 0;
																		$br_arhiviran = 0;
							
																		// BROJ PRIJAVLJENIH
																		$query_check_numbers = $db->prepare("
																						SELECT kandidat_status, COUNT(kandidat_status) as broj FROM idk_kandidati WHERE kandidat_visitedurl = :kandidat_visitedurl GROUP BY kandidat_status
																						");
							
																		$query_check_numbers->execute(array(
																			":kandidat_visitedurl" => $lg_id
																		));
																		while($check = $query_check_numbers->fetch()){
																			$kandidat_status = $check['kandidat_status'];
																			$br_kan = $check['broj'];
																			switch ($kandidat_status){
																				case 0: $br_naprovjeri+=$br_kan; break;
																				case 1: $br_uobradi+=$br_kan; break;
																				case 2: $br_obraden+=$br_kan; break;
																				case 3: $br_arhiviran+=$br_kan; break;
																				case 4: $br_uobradi+=$br_kan; break;
																				case 5: $br_uobradi+=$br_kan; break;
																				case 6: $br_uobradi+=$br_kan; break;
																				case 7: $br_uobradi+=$br_kan; break;
																			}
																		}
																		$ukupno_prijava = $br_naprovjeri+$br_uobradi+$br_obraden+$br_arhiviran;
																		if($ukupno_prijava == 0)
																			$ukupno_prijava_d = 1;
																		else
																			$ukupno_prijava_d = $ukupno_prijava;
							
																		if($check > 0){
																			$check_txt = '<span class="label label-success material-label material-label_success main-container__column">'.$check.'</span>';
																		}else{
																			$check_txt = '<span class="label label-warning material-label material-label_warning main-container__column">'.$check.'</span>';
																		}
																		
																		//fja za broj kandidata u financijama po linku
																		$br_kand = getBrKandFinc($lg_id);
																		$link_uk_prov = $br_kand*$nalog_provizija;
																		$ukupna_zarada += $link_uk_prov;
																		$ukupni_trosak += $lg_troskovi;
																		$profit = $ukupna_zarada - $ukupni_trosak;
							
																?>
																<tr>
																	<td class="text-center"><?php echo $lg_id; ?></td>
																	<td><a href="<?php getSiteUrl(); ?>link_generator.php?page=show_list&id=<?php echo $lg_id; ?>"><?php echo $lg_url; ?></a></td>
																	<td class="text-center" ><?php echo $lg_desc; ?></td>
																	<td class="text-center">
																		
																			<div class="progress" style="position:relative;">
																				<span style="position: absolute; font-weight: 800; left: 0; right: 0; color: white;"><?php echo $ukupno_prijava; ?></span>
																				<div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="99" aria-valuemin="0" aria-valuemax="100" style="width: 100%; ">
																				</div>
																			</div>
																		
																	</td>
																	<td class="text-center">
																		<div class="progress" style="position:relative;">
																			<span style="position: absolute; font-weight: 800; left: 0; right: 0;"><?php echo $br_naprovjeri; ?></span>
																			<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="<?php echo ($br_naprovjeri/$ukupno_prijava_d)*100; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo ($br_naprovjeri/$ukupno_prijava_d)*100; ?>%;">
																			</div>
																		</div>
																	</td>
																	<td class="text-center" style="position:relative;">
																		<div class="progress">
																			<span style="position: absolute; font-weight: 800; left: 0; right: 0;"><?php echo $br_uobradi; ?></span>
																			<div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="<?php echo ($br_uobradi/$ukupno_prijava_d)*100; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo ($br_uobradi/$ukupno_prijava_d)*100; ?>%;">
																			</div>
																		</div>
																	</td>
																	<td class="text-center" style="position:relative;">
																		<div class="progress">
																			<span style="position: absolute; font-weight: 800; left: 0; right: 0;"><?php echo $br_obraden; ?></span>
																			<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?php echo ($br_obraden/$ukupno_prijava_d)*100; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo ($br_obraden/$ukupno_prijava_d)*100; ?>%;">
																			</div>
																		</div>
																	</td>
																	<td class="text-center" style="position:relative;">
																		<div class="progress">
																			<span style="position: absolute; font-weight: 800; left: 0; right: 0;"><?php echo $br_arhiviran; ?></span>
																			<div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="<?php echo ($br_arhiviran/$ukupno_prijava_d)*100; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo ($br_arhiviran/$ukupno_prijava_d)*100; ?>%;">
																			</div>
																		</div>
																	</td>
																	<td class="text-right"><?php echo number_format($lg_troskovi, 2, ',', ''); ?></td>
																	<td class="text-right"><?php echo number_format($link_uk_prov, 2, ',', ''); ?></td>
																	
																</tr>
																<?php } ?>
																
															</tbody>
														</table>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12"  style = "margin-top: 10px;">
											<div class="content_box" style = "min-height: 1px;">
												<div class="row">
													<div class="col-md-4">
														<div class="content_box" style = "min-height: 1px;">
															<div class = "kartica_statistike text-center angry-animate" style = "background: linear-gradient(180deg, rgba(104,195,104,1) 0%, rgba(104,195,104,0.9) 35%, rgba(104,195,104,0.7) 100%);">
																<div class = "kartica_statistike_header">
																	<p>
																		Provizija
																	</p>
																</div>
																<div class = "kartica_statistike_body">
																	<p>
																		<?php echo number_format($ukupna_zarada, 2, ',', ' '); ?> KM
																	</p>
																</div>
															</div>
														</div>
													</div>
													<div class="col-md-4">
														<div class="content_box" style = "min-height: 1px;">
															<div class = "kartica_statistike text-center angry-animate" style = "background: linear-gradient(180deg, rgba(243,65,60,1) 0%, rgba(243,65,60,0.9) 35%, rgba(243,65,60,0.7) 100%);">
																<div class = "kartica_statistike_header">
																	<p>
																		Troškovi
																	</p>
																</div>
																<div class = "kartica_statistike_body">
																	<p>
																		<?php echo number_format($ukupni_trosak, 2, ',', ' '); ?> KM
																	</p>
																</div>
															</div>
														</div>
													</div>
													<div class="col-md-4">
														<div class="content_box" style = "min-height: 1px;">
															<div class = "kartica_statistike text-center angry-animate" style = "background: linear-gradient(180deg, rgba(64,146,217,1) 0%, rgba(64,146,217,0.9) 35%, rgba(64,146,217,0.7) 100%);">
																<div class = "kartica_statistike_header">
																	<p>
																		Balans
																	</p>
																</div>
																<div class = "kartica_statistike_body">
																	<p>
																	
																		<?php 
																		if(isset($profit))
																			echo number_format($profit, 2, ',', ' '); 
																		else echo 'nedefinisano ';
																		?> KM
																	</p>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<?php } else { ?>
										<p>IN WORK</p>
									<?php } ?>
								</div>
								
								<?php } ?>
								<?php if((in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status))  OR $logged_employee_id == $employee_id){	
								
									if($nacin_placanja == 3){
										$naslov_provizija = "Provizija po plati kandidata:";
										$provizija = $nalog_provizija_po_plati;
									} else {
										$naslov_provizija = "Provizija:";
										$provizija = $nalog_provizija;
									}
								?>
								<div class="tab-pane fade <?php if($tab=="finances"){echo "active in";} ?>" id="finances">
									<div class="row idk_employee_info">
										<div class="col-md-6">
											<div class="row">
												<div class="col-sm-9">
													<h5>Informacije o financijama</h5>
												</div>
												<!-- <div class="col-sm-3 text-right">
													<a href="nalozi?page=edit_finances&id=<?php //echo $nalog_id; ?>" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-pencil" aria-hidden="true"></i> <span></span></a>
												</div> -->
											</div>

                                            <div class="row">
												<strong class="col-sm-6 text-right">Vrsta ugovora:</strong>
												<div class="col-sm-6"><?php if($nalog_ugovor == "")echo "Nije definisano";echo $nalog_ugovor; ?></div>
											</div>
											
											<div class="row">
												<strong class="col-sm-6 text-right">Broj potrebnih kandidata:</strong>
												<div class="col-sm-6"><?php if($nalog_potrebno_kandidata == "")echo "Nije definisano";echo $nalog_potrebno_kandidata; ?></div>
											</div>
											
											<div class="row">
												<strong class="col-sm-6 text-right"><?php echo $naslov_provizija;?></strong>
												<div class="col-sm-6"><?php if($provizija == "")echo "Nije definisano";echo $provizija; ?></div>
											</div>
											
											<div class="row">
												<strong class="col-sm-6 text-right">Broj rata:</strong>
												<div class="col-sm-6"><?php if($nalog_broj_rata == "")echo "Nije definisano";echo $nalog_broj_rata; ?></div>
											</div>

											<div class="row">
												<strong class="col-sm-6 text-right">Broj dana za dospijeće plaćanja:</strong>
												<div class="col-sm-6"><?php if($nalog_dospijece == "")echo "Nije definisano";echo $nalog_dospijece; ?></div>
											</div>
											<hr>
											<div class="row">
												<strong class="col-sm-6 text-right">Poslodavac plaća nostrifikaciju:</strong>
												<div class="col-sm-6"><?php echo ($nalog_placa_nostrifikaciju == 1) ? "Da" : "Ne"; ?></div>
											</div>
											<?php
											if($nalog_placa_nostrifikaciju == 1){?>
												<div class="row">
													<strong class="col-sm-6 text-right">Iznos nostrifikacije po kandidatu:</strong>
													<div class="col-sm-6"><?php echo ($nalog_provizija_nostrifikacija == "") ? "Nedefinisano" : $nalog_provizija_nostrifikacija . "Є"; ?></div>
												</div>
											<?php
											}
											?>
										</div>
									</div>
									</br>
									<div class="alert material-alert" id="procenat_check"></div>
									<div class="row">
										<div class="col-md-6"><h3>Rate za posredovanje</h3></div>
										<div class="col-md-6"><h3>Rate za nostrifikaciju</h3></div>
									</div>
									<hr>
									<div class="row">
										<div class="col-md-5">
										<?php
											if($nacin_placanja == 2){
												$query_rate = $db->prepare("
																	SELECT nf_type, nf_broj_rate, nf_iznos, nf_datum_aktiviranja 
																	FROM idk_nalog_financije
																	WHERE nf_nalog_id = :nalog_id AND nf_type != 4");
																
												$query_rate->execute(array(
																	':nalog_id' => $nalog_id));

												$rate = [];
												while($row_rate = $query_rate->fetch()){
													$rata = [];
													$rata['nf_type'] = $row_rate['nf_type'];
													$rata['nf_broj_rate'] = $row_rate['nf_broj_rate'];
													$rata['nf_iznos'] = $row_rate['nf_iznos'];
													$rata['nf_datum_aktiviranja'] = $row_rate['nf_datum_aktiviranja'];
													$rate[] = $rata;
												}

												foreach($rate as $rata){
													if($rata['nf_type'] == 2){
														$naslov = "Avans";
													}
													elseif($rata['nf_type'] == 3){
														$naslov = $rata["nf_broj_rate"].". rata";
													}
													$iznos = round($rata['nf_iznos'], 2);
													?>
													<div class="row idk_employee_info" style="border-bottom: 1px solid gainsboro;border-top: 1px solid gainsboro;background: #f9f9f9;padding-top: 20px;padding-bottom: 20px;margin-top: 30px;">
														<div class="col-md-6">
															<div class="row">
																<div class="col-sm-12">
																	<h5><strong><?php echo $naslov; ?></strong></h5>
																</div>
															</div>
															<div class="row">
																<strong class="col-sm-6 text-right">Iznos: </strong>
																<div class="col-sm-6"><?php echo ($rata['nf_iznos'] == "") ? "Nije definisano" : $iznos . "Є"; ?></div>
															</div>
															<div class="row">
																<strong class="col-sm-6 text-right">Datum placanja: </strong>
																<div class="col-sm-6"><?php echo ($rata['nf_datum_aktiviranja'] == "") ? "Nije definisano" : $rata['nf_datum_aktiviranja']; ?></div>
															</div>
														</div>
													</div>
												<?php }
											}
										?>
										<?php
										if($nalog_broj_rata != 0 && in_array($nacin_placanja, [1, 3])){
											$query_rate = $db->prepare("
																SELECT nr_id, nr_nalog, nr_rata, nr_procenat, nr_vrijeme_placanja, nr_datum, nr_mjeseci_nakon
																FROM idk_nalozi_rate
																WHERE nr_nalog = :nalog_id");
															
											$query_rate->execute(array(
																':nalog_id' => $nalog_id));
											
											$i=0;
											$suma_procenat=0;
											$ukupni_broj_rata = $query_rate->rowCount();
											while($row_rate = $query_rate->fetch()){
												$nr_id[] = $row_rate['nr_id'];
												$nr_nalog[] = $row_rate['nr_nalog'];
												$nr_rata[] = $row_rate['nr_rata'];
												$nr_procenat[] = $row_rate['nr_procenat'];
												$nr_vrijeme_placanja[] = $row_rate['nr_vrijeme_placanja'];
												$nr_vrijeme_placanja_trenutni = $row_rate['nr_vrijeme_placanja'];
												$nr_mjeseci_nakon[] = $row_rate['nr_mjeseci_nakon'];
												
												if($row_rate['nr_vrijeme_placanja'] == "odmah"){
													$vrijeme[] = "Odmah";
													$nr_datum[] = date("Y-m", strtotime($row_rate['nr_datum']));
												}
												else if($row_rate['nr_vrijeme_placanja'] == "ugovor"){
													$vrijeme[] = "Ugovor";
													$nr_datum[] = date("d.m.Y", strtotime($row_rate['nr_datum']));
												}
												else if($row_rate['nr_vrijeme_placanja'] == "dobio vizu"){
													$vrijeme[] = "Dobio vizu";
													$nr_datum[] = date("d.m.Y", strtotime($row_rate['nr_datum']));
												}
												else if($row_rate['nr_vrijeme_placanja'] == "pocetak rada"){
													$vrijeme[] = "Početak rada";
													$nr_datum[] = date("d.m.Y", strtotime($row_rate['nr_datum']));
												}
												else if($row_rate['nr_vrijeme_placanja'] == "mjeseci nakon"){
													$vrijeme[] = "Nakon početka rada";
													$nr_datum[] = date("d.m.Y", strtotime($row_rate['nr_datum']));
												}
												$suma_procenat = $suma_procenat + $nr_procenat[$i];

												if($nalog_provizija_po_plati != NULL){
													$naslov = "Provizija po plati";
													// $nr_procenat[$i] = $nalog_provizija_po_plati;
												}else{
													$naslov = "Procenat";
												}	
												?>
												<div class="row idk_employee_info" style="border-bottom: 1px solid gainsboro;border-top: 1px solid gainsboro;background: #f9f9f9;padding-top: 20px;padding-bottom: 20px;margin-top: 30px;">
													<div class="col-md-12">
														<div class="row">
															<div class="col-sm-12">
																<h5><strong><?php echo $nr_rata[$i]; ?>. rata:</strong></h5>
															</div>
														</div>
														<?php if($nalog_provizija_po_plati !== NULL){ ?>
														<div class="row">
															<strong class="col-sm-6 text-right">Provizija po plati: </strong>
															<div class="col-sm-6"><?php echo $nalog_provizija_po_plati; ?></div>
														</div>
														<?php } ?>
														<div class="row">
															<strong class="col-sm-6 text-right">Procenat: </strong>
															<div class="col-sm-6"><?php echo ($nr_procenat[$i] == "") ? "Nije definisano" : $nr_procenat[$i]; ?></div>
														</div>
														<div class="row">
															<strong class="col-sm-6 text-right">Vrijeme plaćanja: </strong>
															<div class="col-sm-6"><?php echo ($nr_vrijeme_placanja[$i] == "") ? "Nije definisano" : $vrijeme[$i]; ?></div>
														</div>
														<?php if($nr_vrijeme_placanja[$i] == "odmah"){ ?>
														<div class="row">
															<strong class="col-sm-6 text-right">Datum plaćanja: </strong>
															<div class="col-sm-6"><?php echo ($nr_datum[$i] == "") ? "Nije definisano" : $nr_datum[$i]; ?></div>
														</div>
														<?php } ?>
														<?php if(is_null($nr_mjeseci_nakon[$i]) == false){ ?>
														<div class="row">
															<strong class="col-sm-6 text-right">Mjeseci nakon početka rada: </strong>
															<div class="col-sm-6"><?php echo ($nr_mjeseci_nakon[$i] == "") ? "Nije definisano" : $nr_mjeseci_nakon[$i]; ?></div>
														</div>
														<?php } ?>
														
														<!-- <div class="row">
															<div class="col-xs-12">
																<?php //if($logged_employee_id == 63 or $logged_employee_id == 67) { ?>
																<button class="btn material-btn material-btn-icon-danger material-btn_danger main-container__column pull-right" data-toggle="modal" data-target="#brisanje_rata<?php //echo $nr_id[$i]; ?>" style="padding-right: 0px!important;">
																	<i class="fa fa-times" style="margin-right:0px" aria-hidden="true"></i>
																</button><?php // } ?>
																<button class="btn material-btn material-btn-icon-success material-btn_success main-container__column pull-right" data-toggle="modal" data-target="#rata<?php //echo $nr_id[$i]; ?>" style="padding-right: 0px!important;">
																	<i class="fa fa-pencil" style="margin-right:0px" aria-hidden="true"></i>
																</button>
															</div>
														</div> -->
														
													</div>
													<!-- <div class="col-md-6">
														Neki tekst
													</div> -->
												</div>

													<!-- Modal rate -->
													<div class="modal material-modal material-modal_success fade" id="rata<?php echo $nr_id[$i]; ?>">
														<div class="modal-dialog">
															<div class="modal-content material-modal__content">
																<div class="modal-header material-modal__header">
																	<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																	<h4 class="modal-title material-modal__title">Uredite ratu <?php echo $nr_rata[$i]; ?></h4>
																</div>
																<div class="modal-body material-modal__body">
																	<div class="row">	
																		<div class="col-xs-12">	
																			<form action="<?php getSiteURL(); ?>do.php?form=edit_rate" method="post" http-equiv="Content-type" enctype="multipart/form-data"; charset=utf-8" class="form-horizontal">
																				<input type="hidden" value="<?php echo $nr_id[$i]; ?>" name="nr_id">
																				<input type="hidden" value="<?php echo $nr_nalog[$i]; ?>" name="nr_nalog">
																				<input type="hidden" value="<?php echo $nr_rata[$i]; ?>" name="nr_rata">
																				<input type="hidden" value="<?php echo $nalog_broj_rata; ?>" name="broj_rata">
																				<div class="form-group">
																					<label for="project_plannedhours" class="col-sm-3 control-label"> Procenat:</label>
																					<div class="col-sm-9">
																						<div class="materail-input-block materail-input-block_success">
																							<input class="form-control materail-input" type="text" name="nr_procenat" id="nr_procenat<?php echo $nr_id[$i];?>" value="<?php echo $nr_procenat[$i]; ?>">
																							<input type="hidden" class="form-control materail-input" type="text" name="nr_procenat_stari" value="<?php echo $nr_procenat[$i]; ?>">
																							<span class="materail-input-block__line"></span>
																						</div>
																					</div>
																				</div>
																				<div class="form-group">
																					<label for="project_plannedhours" class="col-sm-3 control-label"> Vrijeme plaćanja: </label>
																					<div class="col-sm-9">
																						<div class="materail-input-block materail-input-block_success">
																							<select class="form-control materail-input materail-input-custom" id="nr_vrijeme_placanja<?php echo $nr_id[$i];?>" name="nr_vrijeme_placanja" required>
																								<?php if($nr_vrijeme_placanja[$i] === "odmah"){?>
																									<option value="odmah">Odmah</option>
																								<?php }else if($nr_vrijeme_placanja[$i] === "" and $nr_rata[$i] == 1){?>
																									<option value="odmah">Odmah</option>
																									<option value="ugovor">Ugovor</option>
																									<option value="pocetak rada">Početak rada</option>
																									<option value="mjeseci nakon">Nakon početka rada</option>
																								<?php }else if($i == 0 ){?>
																									<option value="odmah">Odmah</option>
																									<option value="<?php echo $nr_vrijeme_placanja[$i]; ?>" selected><?php echo ucfirst($nr_vrijeme_placanja[$i]); ?></option>
																								<?php }else if($nr_vrijeme_placanja[$i-1] === "odmah"){?>
																									<option value="ugovor">Ugovor</option>
																									<option value="pocetak rada" <?php if($nr_vrijeme_placanja_trenutni == "pocetak rada"){echo "selected";}?> >Početak rada</option>
																									<option value="mjeseci nakon" <?php if($nr_vrijeme_placanja_trenutni == "mjeseci nakon"){echo "selected";}?> >Nakon početka rada</option>
																								<?php } else if($nr_vrijeme_placanja[$i-1] === "ugovor"){?>
																									<option value="pocetak rada" <?php if($nr_vrijeme_placanja_trenutni == "pocetak rada"){echo "selected";}?> >Početak rada</option>
																									<option value="mjeseci nakon" <?php if($nr_vrijeme_placanja_trenutni == "mjeseci nakon"){echo "selected";}?> >Nakon početka rada</option>
																								<?php } else if($nr_vrijeme_placanja[$i-1] === "pocetak rada"){?>
																									<option value="mjeseci nakon">Nakon početka rada</option>
																								<?php } else if($nr_vrijeme_placanja[$i-1] === "mjeseci nakon"){?>
																									<option value="mjeseci nakon">Nakon početka rada</option>
																								<?php }
																								if(isset($nr_vrijeme_placanja[$i+1])){
																									if($nr_vrijeme_placanja[$i+1] === "mjeseci nakon"){?> 
																										<option value="mjeseci nakon" <?php if($nr_vrijeme_placanja_trenutni == "mjeseci nakon"){echo "selected";}?> >Nakon početka rada</option> 
																								<?php } }?>
																								
																							</select>
																							<span class="materail-input-block__line"></span>
																						</div>
																					</div>
																				</div>
																				<script>
																					//DATUM PLAĆANJA AKO JE ODABRANO ODMAH KAO VRIJEME PLAĆANJA KOD RATE
																					$(document).ready(function(){
																						$("#datum_placanja<?php echo $nr_id[$i];?>").hide();
																						var vrijeme_placanja = $("#nr_vrijeme_placanja<?php echo $nr_id[$i];?>").val();
																						if(vrijeme_placanja == "odmah"){
																							$("#datum_placanja<?php echo $nr_id[$i];?>").show();
																							$('.ui-datepicker-calendar').hide();
																							$("#nr_datum<?php echo $nr_id[$i];?>").datepicker({
																								dateFormat: 'yy-mm',
																								changeMonth: true,
																								changeYear: true,
																								closeText : "Potvrdi",
																								yearRange: '2010:2055',
																								showButtonPanel: true,
																								onClose: function(dateText, inst) {
																									var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
																									var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
																									$(this).val($.datepicker.formatDate('yy-mm', new Date(year, month, 1)));
																								}
																							});
																							$(".monthPicker").focus(function () {
																								$(".ui-datepicker-calendar").addClass('display_none');
																								$(".ui-datepicker-calendar").hide();
																								$("#ui-datepicker-div").position({
																									my: "center top",
																									at: "center bottom",
																									of: $(this)
																								});
																							});
																						}
																						if(vrijeme_placanja == "mjeseci nakon"){
																							$("#show_mjesec_nakon<?php echo $nr_id[$i];?>").show();
																						}else
																							$("#show_mjesec_nakon<?php echo $nr_id[$i];?>").hide();
																					});
																					
																					//KAD SE PROMIJENI VRIJEME PLAĆANJA PRIKAZ DATUMA PLAĆANJA
																					$(document).ready(function(){
																						$("#nr_vrijeme_placanja<?php echo $nr_id[$i];?>").change(function(){
																							var vrijeme_placanja = $("#nr_vrijeme_placanja<?php echo $nr_id[$i];?>").val();
																							
																							if(vrijeme_placanja == "odmah"){
																								$("#datum_placanja<?php echo $nr_id[$i];?>").show();
																							}
																							else
																								$("#datum_placanja<?php echo $nr_id[$i];?>").hide();
																						});
																					});
																					
																					
																					$("#nr_vrijeme_placanja<?php echo $nr_id[$i];?>").change(function(){
																						var vrijeme_placanja2 = $('option:selected',this).attr('value');
																						
																						if(vrijeme_placanja2 == "mjeseci nakon"){
																							$("#show_mjesec_nakon<?php echo $nr_id[$i];?>").show();
																							
																						}
																						else {
																							$("#show_mjesec_nakon<?php echo $nr_id[$i];?>").hide();
																							
																						}
																					});
																					
																				</script>
																				<div class="form-group" id="datum_placanja<?php echo $nr_id[$i];?>">
																					<label for="project_plannedhours" class="col-sm-3 control-label"> Datum plaćanja:</label>
																					<div class="col-sm-9">
																						<div class="materail-input-block materail-input-block_success">
																							<input class="form-control materail-input monthPicker" type="text" name="nr_datum" id="nr_datum<?php echo $nr_id[$i];?>" value="<?php echo $nr_datum[$i]; ?>">
																							<span class="materail-input-block__line"></span>
																						</div>
																					</div>
																				</div>
																				
																				<div class="form-group" id="show_mjesec_nakon<?php echo $nr_id[$i];?>">
																					<label for="project_plannedhours" class="col-sm-3 control-label"> Broj mjeseci nakon:</label>
																					<div class="col-sm-9">
																						<div class="materail-input-block materail-input-block_success">
																							<input class="form-control materail-input" type="text" name="nr_mjeseci_nakon" id="nr_mjeseci_nakon<?php echo $nr_id[$i];?>" value="<?php echo $nr_mjeseci_nakon[$i]; ?>">
																							<span class="materail-input-block__line"></span>
																						</div>
																					</div>
																				</div>
																				
																		</div>
																	</div>
																</div>
																<div class="modal-footer material-modal__footer">
																	<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
																	<button class="btn btn-primary material-btn material-btn_success">SPREMI</button>
																	</form>
																</div>
															</div>
														</div>
													</div>
													
													<!-- Modal obrisi ratu -->
													<div class="modal material-modal material-modal_danger fade" id="brisanje_rata<?php echo $nr_id[$i]; ?>">
														<div class="modal-dialog">
															<div class="modal-content material-modal__content">
																<div class="modal-header material-modal__header">
																	<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																	<h4 class="modal-title material-modal__title">Brisanje rate <?php echo $nr_rata[$i]; ?></h4>
																</div>
																<div class="modal-body material-modal__body">
																	<div class="row">	
																		<div class="col-xs-12">	
																			<form action="<?php getSiteURL(); ?>do.php?form=delete_rate" method="post" http-equiv="Content-type" enctype="multipart/form-data"; charset=utf-8" class="form-horizontal">
																				<input type="hidden" value="<?php echo $nr_id[$i]; ?>" name="nr_id">
																				<input type="hidden" value="<?php echo $nr_nalog[$i]; ?>" name="nr_nalog">
																				<input type="hidden" value="<?php echo $nr_rata[$i]; ?>" name="nr_rata">
																				<input type="hidden" value="<?php echo $nr_vrijeme_placanja[$i]; ?>" name="nr_vrijeme_placanja">
																				<input type="hidden" value="<?php echo $nr_procenat[$i]; ?>" name="nr_procenat">
																				<input type="hidden" value="<?php echo $nalog_broj_rata; ?>" name="nalog_broj_rata">
																				</br>
																				<p style="text-align: center"> Da li sigurno želite obrisati ratu broj <?php echo $nr_rata[$i]; ?>?</p>
																		</div>
																	</div>
																</div>
																<div class="modal-footer material-modal__footer">
																	<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
																	<button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button>
																	</form>
																</div>
															</div>
														</div>
													</div>
													
											
												<?php $i++;
											} 
										}else{
											?>
											<?php  
										} ?>
										</div>
										<div class="col-md-5 col-md-offset-1">
											<?php
												if($nalog_placa_nostrifikaciju == 1 && $nalog_nacin_nostrifikacije == 2){
													$query_dipl_rate = $db->prepare("SELECT nf_datum_aktiviranja, nf_iznos FROM idk_nalog_financije WHERE nf_nalog_id = :nalog_id AND nf_type = 4");	
													$query_dipl_rate->execute(array(
														':nalog_id' => $nalog_id
													));
													$dipl_rate = [];
													while($row_dipl_rate = $query_dipl_rate->fetch()){
														$dipl_rata = [];
														$dipl_rata['nf_datum_aktiviranja'] = $row_dipl_rate['nf_datum_aktiviranja'];
														$dipl_rata['nf_iznos'] = $row_dipl_rate['nf_iznos'];
														$dipl_rate[] = $dipl_rata;
													}

													$broj_dipl_rate = 0;

													foreach($dipl_rate as $rata){
														$broj_dipl_rate++;
														$iznos_dipl = round($rata['nf_iznos'], 2);
													?>
													<div class="row idk_employee_info" style="border-bottom: 1px solid gainsboro;border-top: 1px solid gainsboro;background: #f9f9f9;padding-top: 20px;padding-bottom: 20px;margin-top: 30px;">
														<div class="col-md-6">
															<div class="row">
																<div class="col-sm-12">
																	<h5><strong><?php echo $broj_dipl_rate; ?> .rata za nostrifikaciju</strong></h5>
																</div>
															</div>
															<div class="row">
																<strong class="col-sm-6 text-right">Iznos: </strong>
																<div class="col-sm-6"><?php echo ($rata['nf_iznos'] == "") ? "Nije definisano" : $iznos_dipl . "Є"; ?></div>
															</div>
															<div class="row">
																<strong class="col-sm-6 text-right">Datum placanja: </strong>
																<div class="col-sm-6"><?php echo ($rata['nf_datum_aktiviranja'] == "") ? "Nije definisano" : $rata['nf_datum_aktiviranja']; ?></div>
															</div>
														</div>
													</div>
													<?php
													}
												}
												
												if($nalog_placa_nostrifikaciju == 1 && $nalog_nacin_nostrifikacije == 1){?>
													<div class="row idk_employee_info" style="border-bottom: 1px solid gainsboro;border-top: 1px solid gainsboro;background: #f9f9f9;padding-top: 20px;padding-bottom: 20px;margin-top: 30px;">
														<div class="col-md-6">
															<div class="row">
																<div class="col-sm-12">
																	<h5><strong>Po kandidatu</strong></h5>
																</div>
															</div>
															<div class="row">
																<strong class="col-sm-3 text-right">Iznos: </strong>
																<div class="col-sm-6"><?php echo ($nalog_provizija_nostrifikacija == "") ? "Nije definisano" : $nalog_provizija_nostrifikacija . "Є"; ?></div>
															</div>
														</div>
													</div>
												<?php }
											?>
										</div>
									</div>
								</div>
								<?php }?>
								
								<div class="tab-pane fade <?php if($tab=="kriteriji"){echo "active in";} ?>" id="kriteriji">
									<div class="row">
										<div class="col-lg-12" id="profile_box">
											<?php
											$query_profili = $db->prepare("SELECT *
																FROM idk_nalog_profil
																INNER JOIN idk_profil_kriterij ON idk_nalog_profil.id = idk_profil_kriterij.profil_id
																WHERE nalog_id = :nalog_id AND status = 1 ORDER BY prioritet");
															
											$query_profili->execute(array(':nalog_id' => $nalog_id));
																						
											$profili = $query_profili->fetchAll(PDO::FETCH_ASSOC);
											?>
											<div class="row">
												<div class="col-xs-12">
													<div class="text-center col-xs-11" style = "font-weight: bold; font-size: 18px;"><i class="fa fa-question-circle-o" style = "margin-right: 10px;" aria-hidden="true"></i>Profili za nalog</div>
												</div>
											</div>
											<style>
												label {
													padding-bottom: 5px;
													margin-bottom: 0px; 
													}
													.idk_radio_buttons{
														padding-top:0px;
														margin-top:5px;
													}
											</style>
											<div class="row">
												<div class="col-md-12">
													<div class="panel panel-default">
														<div class="panel-body">
															<div class="content_box content_questions">
																<div class="row">
																	<div class="col-md-12">
																		<button data-toggle="modal" data-target="#addNalogProfil" style="float:right;" class="btn btn-primary material-btn material-btn_success"><i class="fa fa-check-square-o" aria-hidden="true"></i> Dodaj novi profil</button>
																	</div>
																	<!-- ADD MODAL -->
																	<div class="modal material-modal material-modal_success fade text-left" id="addNalogProfil">
																		<div class="modal-dialog ">
																			<div class="modal-content material-modal__content">
																				<div class="modal-header material-modal__header">
																					<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																					<h4 class="modal-title material-modal__title">Dodaj profil</h4>
																				</div>																								
																				<form action="<?php getSiteURL(); ?>do?form=add_nalog_profile" enctype="multipart/form-data" method="post" role="form" class="form-horizontal">
																					<div class="modal-body material-modal__body text-center">
																						<input type="hidden" name="nalog_id" value="<?php echo $nalog_id;?>" />
																						<div class="form-group">
																							<label for="naziv_profila" class="col-sm-4 control-label"><span class="text-danger">*</span> Naziv profila:</label>
																							<div class="col-sm-8">
																								<input class="form-control materail-input" type="text" name="naziv_profila" id="naziv_profila" value="Profil <?php echo getDefaultProfileName($nalog_id);?>" placeholder="Unesite naziv profila" required>
																							</div>
																						</div>

																						<div class="form-group">
																							<label for="prioritet_profila" class="col-sm-4 control-label"><span class="text-danger">*</span> Prioritet profila:</label>
																							<div class="col-sm-8">
																								<input class="form-control materail-input" type="number" min="1" name="prioritet_profila" id="prioritet_profila" placeholder="Unesite prioritet profila" required>
																							</div>
																						</div>
																						
																						<!-- SMJEROVI NALOGA -->
																						<div class="form-group col-xs-12" style="display:flex; align-items: center;">
																							<div class="col-xs-4" style="text-align:right;">Smjerovi naloga:</div>
																							<div class="col-xs-2 materail-input-block materail-input-block_success idk_radio_buttons kriterij_smjerovi_naloga_da">
																								<label class="main-container__column material-radio-group material-radio-group_success" for="kriterij_smjerovi_naloga_da">
																									<input type="radio" name="kriterij_smjerovi_naloga" id="kriterij_smjerovi_naloga_da" class="material-radiobox" value="1" />
																									<span class="material-radio-group__element material-radio-group__check-radio"></span>
																									<span class="material-radio-group__element material-radio-group__caption">DA</span>
																								</label>
																							</div>
																							<div class="col-xs-2 materail-input-block materail-input-block_danger idk_radio_buttons kriterij_smjerovi_naloga_ne">
																								<label class="main-container__column material-radio-group material-radio-group_danger" for="kriterij_smjerovi_naloga_ne">
																									<input type="radio" name="kriterij_smjerovi_naloga" id="kriterij_smjerovi_naloga_ne" class="material-radiobox" value="0" checked />
																									<span class="material-radio-group__element material-radio-group__check-radio"></span>
																									<span class="material-radio-group__element material-radio-group__caption">NE</span>
																								</label>
																							</div>
																							<div class="col-xs-3">
																								<?php if(getNalogSmjeroviCntR($nalog_id) != 0){ ?>
																									<span style="font-size: 10px; font-style: italic;">Smjerovi unešeni</span>
																								<?php } ?>
																								<a href="<?php getSiteURL(); ?>nalozi?page=open&id=<?php echo $nalog_id; ?>&tab=smjerovi_naloga" style="display:none; font-size: 10px; " class="goto_smjerovi_naloga btn-primary material-btn material-btn_success"><i class="fa fa-plus" aria-hidden="true"></i> Unesi smjerove</a>
																							</div>
																						</div>
																						<script>
																							$('.kriterij_smjerovi_naloga_da').click(function(){
																								$.ajax({
																									type: "POST",
																									url: 'ajax_data.php?page=check_nalog_smjerovi_settings',
																									data: {
																										nalog_id: "<?php echo $nalog_id; ?>"
																									},
																									success: function(data){
																										if(data == 0){
																											$('.goto_smjerovi_naloga').css('display', 'inline-block');
																											$('.add_nalog_profile_btn').css('display', 'none');
																										}
																									}
																								}); 
																							});
																							$('.kriterij_smjerovi_naloga_ne').click(function(){
																								$('.goto_smjerovi_naloga').css('display', 'none');
																								$('.add_nalog_profile_btn').css('display', 'inline-block');
																							});
																						</script>

																						<!-- VOZACKA DOZVOLA -->
																						<div class="form-group col-md-12">
																							<div class="col-xs-4" style="padding-top:5px; text-align:right;">Vozačka dozvola:</div>
																							<div class="col-xs-2 materail-input-block materail-input-block_success idk_radio_buttons kriterij_vozacka_dozvola_da">
																								<label class="main-container__column material-radio-group material-radio-group_success" for="kriterij_vozacka_dozvola_da">
																									<input type="radio" name="kriterij_vozacka_dozvola" id="kriterij_vozacka_dozvola_da" class="material-radiobox" value="1" />
																									<span class="material-radio-group__element material-radio-group__check-radio"></span>
																									<span class="material-radio-group__element material-radio-group__caption">DA</span>
																								</label>
																							</div>
																							<div class="col-xs-2 materail-input-block materail-input-block_danger idk_radio_buttons kriterij_vozacka_dozvola_ne">
																								<label class="main-container__column material-radio-group material-radio-group_danger" for="kriterij_vozacka_dozvola_ne">
																									<input type="radio" name="kriterij_vozacka_dozvola" id="kriterij_vozacka_dozvola_ne" class="material-radiobox" value="0" checked />
																									<span class="material-radio-group__element material-radio-group__check-radio"></span>
																									<span class="material-radio-group__element material-radio-group__caption">NE</span>
																								</label>
																							</div>
																						</div>
																						
																						<div class="form-group col-md-12 kriterij_kategorije_vozacke" style="display: none">
																							<div class="col-xs-4" style="padding-top:5px; text-align:right;">Kategorija vozačke dozvole:</div>
																							<div class="col-md-6 col-xs-6" style="padding-left:0px;">
																								<select class="selectpicker" multiple id="kriterij_vozacka_kat" name="kriterij_vozacka_kat[]"  style="width: 75%;" >	
																									<option value="B" selected>B</option>
																									<option value="C1">C1</option>
																									<option value="C">C</option>
																									<option value="BE">BE</option>
																									<option value="C1E">C1E</option>
																									<option value="CE">CE</option>
																								</select>
																							</div>
																						</div>
																						<script>
																							$('.kriterij_vozacka_dozvola_da').click(function(){
																								$('.kriterij_kategorije_vozacke').css('display', 'block');
																								$('#kriterij_vozacka_kat').attr('required', 'true');
																							});
																							$('.kriterij_vozacka_dozvola_ne').click(function(){
																								$('.kriterij_kategorije_vozacke').css('display', 'none');
																								$("#kriterij_vozacka_kat").val(null).selectpicker('refresh');
																								$('#kriterij_vozacka_kat').removeAttr('required');

																							});		
																						</script>
																						
																						<!-- RADNO ISKUSTVO U STRUCI -->
																						<div class="form-group col-md-12">
																							<div class="col-xs-4" style="padding-top:5px; text-align:right;">Radno iskustvo u struci:</div>
																							<div class="col-xs-2 materail-input-block materail-input-block_success idk_radio_buttons">
																								<label class="main-container__column material-radio-group material-radio-group_success" for="kriterij_iskustvo_struka_da">
																									<input type="radio" name="kriterij_iskustvo_struka" id="kriterij_iskustvo_struka_da" class="material-radiobox" value="1" />
																									<span class="material-radio-group__element material-radio-group__check-radio"></span>
																									<span class="material-radio-group__element material-radio-group__caption">DA</span>
																								</label>
																							</div>
																							<div class="col-xs-2 materail-input-block materail-input-block_danger idk_radio_buttons">
																								<label class="main-container__column material-radio-group material-radio-group_danger" for="kriterij_iskustvo_struka_ne">
																									<input type="radio" name="kriterij_iskustvo_struka" id="kriterij_iskustvo_struka_ne" class="material-radiobox" value="0" checked />
																									<span class="material-radio-group__element material-radio-group__check-radio"></span>
																									<span class="material-radio-group__element material-radio-group__caption">NE</span>
																								</label>
																							</div>
																						</div>

																						<!-- RADNO ISKUSTVO TRAJANJE -->
																						<div class="form-group col-md-12" id="kriterij_iskustvo_trajanje_group" style="display:none">
																							<div class="col-xs-4" style="padding-top:5px; text-align:right;">Radno iskustvo u struci u posljednjih 5 godina:</div>
																							<div class="col-md-6 col-xs-6" style="padding-left:0px;">
																								<select class="selectpicker kriterij_iskustvo_trajanje" id="kriterij_iskustvo_trajanje" name="kriterij_iskustvo_trajanje">
																									<option value="0">Ne treba</option>
																									<option value="1">Više od 0</option>
																									<option value="2">1 godina i više</option>
																									<option value="3">2 godine i više</option>
																									<option value="4">3 godine i više</option>
																									<option value="5">4 godine i više</option>
																									<option value="6">5 godina</option>
																								</select>
																							</div>
																						</div>
																						<script>
																							$(document).ready(function () {
																								
																								$('#kriterij_iskustvo_struka_da').click(function() {
																									if($('#kriterij_iskustvo_struka_da').is(':checked')) { 
																										$('#kriterij_iskustvo_trajanje_group').show();
																										$("#kriterij_iskustvo_trajanje").prop('required',true);
																									}
																								});
																								$('#kriterij_iskustvo_struka_ne').click(function() {
																									if($('#kriterij_iskustvo_struka_ne').is(':checked')) { 
																										$('#kriterij_iskustvo_trajanje_group').hide();
																										$("#kriterij_iskustvo_trajanje").prop('required',false);
																									}
																								});
																							});
																						</script>

																						<!-- STAROST KANDIDATA -->
																						<div class="form-group col-md-12">
																							<div class="col-xs-4" style="padding-top:5px; text-align:right;">Starost kandidata:</div>
																							<div class="col-xs-2 materail-input-block materail-input-block_success idk_radio_buttons">
																								<label class="main-container__column material-radio-group material-radio-group_success" for="kriterij_starost_kandidata_da">
																									<input type="radio" name="kriterij_starost_kandidata" id="kriterij_starost_kandidata_da" class="material-radiobox" value="1" />
																									<span class="material-radio-group__element material-radio-group__check-radio"></span>
																									<span class="material-radio-group__element material-radio-group__caption">DA</span>
																								</label>
																							</div>
																							<div class="col-xs-2 materail-input-block materail-input-block_danger idk_radio_buttons">
																								<label class="main-container__column material-radio-group material-radio-group_danger" for="kriterij_starost_kandidata_ne">
																									<input type="radio" name="kriterij_starost_kandidata" id="kriterij_starost_kandidata_ne" class="material-radiobox" value="0" checked />
																									<span class="material-radio-group__element material-radio-group__check-radio"></span>
																									<span class="material-radio-group__element material-radio-group__caption">NE</span>
																								</label>
																							</div>
																						</div>																

																						<div class="form-group col-md-12 kriterij_starost_kandidata_group" style="display: none;">
																							<div class="col-xs-4" style="padding-top:5px; text-align:right;">Kandidat mora imati između:</div>
																							<label class="col-xs-6 main-container__column material-radio-group material-radio-group_danger" for="kriterij_starost_kandidata">
																								<input type="number" id="kriterij_starost_od" name="kriterij_starost_od" min="18" max="99" value="18">
																								<input type="number" id="kriterij_starost_do" name="kriterij_starost_do" min="18" max="99" value="44">
																							</label>
																						</div>
																						<script>
																							$('#kriterij_starost_kandidata_da').click(function(){
																								$('#kriterij_starost_od').prop('required',true);
																								$('#kriterij_starost_do').prop('required',true);
																								
																								$('.kriterij_starost_kandidata_group').css('display', 'block');
																							});
																							$('#kriterij_starost_kandidata_ne').click(function(){
																								$('#kriterij_starost_od').removeAttr('required');
																								$('#kriterij_starost_do').removeAttr('required');
																								
																								$('.kriterij_starost_kandidata_group').css('display', 'none');
																							});
																						</script>
																						
																						<!-- NJEMACKI JEZIK -->
																						<div class="form-group col-md-12">
																							<div class="col-xs-4" style="padding-top:5px; text-align:right;">Njemački jezik:</div>
																							<div class="col-xs-2 materail-input-block materail-input-block_success idk_radio_buttons kriterij_njemacki_jezik_da">
																								<label class="main-container__column material-radio-group material-radio-group_success" for="kriterij_njemacki_jezik_da">
																									<input type="radio" name="kriterij_njemacki_jezik" id="kriterij_njemacki_jezik_da" class="material-radiobox" value="1" />
																									<span class="material-radio-group__element material-radio-group__check-radio"></span>
																									<span class="material-radio-group__element material-radio-group__caption">DA</span>
																								</label>
																							</div>
																							<div class="col-xs-2 materail-input-block materail-input-block_danger idk_radio_buttons kriterij_njemacki_jezik_ne">
																								<label class="main-container__column material-radio-group material-radio-group_danger" for="kriterij_njemacki_jezik_ne">
																									<input type="radio" name="kriterij_njemacki_jezik" id="kriterij_njemacki_jezik_ne" class="material-radiobox" value="0" checked />
																									<span class="material-radio-group__element material-radio-group__check-radio"></span>
																									<span class="material-radio-group__element material-radio-group__caption">NE</span>
																								</label>
																							</div>
																						</div>

																						<!-- NIVO NJEMAČKOG JEZIKA -->
																						<div class="form-group col-md-12" id="kriterij_nivo_njemackog_jezika_group" style="display:none;">
																							<div class="col-xs-4" style="padding-top:5px; text-align:right;">Minimalno poznavanje njemačkog jezika:</div>
																							<div class="col-xs-6" style="padding-left:0px; ">
																								<select class="selectpicker" id="kriterij_nivo_njemackog_jezika" name="kriterij_nivo_njemackog_jezika" style="">
																									<option value="" disabled="disabled"></option>
																									<!-- <option value="BZ">Bez znanja</option> -->
																									<option value="A1">A1</option>
																									<option value="A2">A2</option>
																									<option value="B1">B1</option>
																									<option value="B2">B2</option>
																									<option value="C1">C1</option>
																									<option value="C2">C2</option>
																								</select>
																							</div>
																						</div>
																						<script>
																							$(document).ready(function () {
																								$('#kriterij_njemacki_jezik_da').click(function() {
																									if($('#kriterij_njemacki_jezik_da').is(':checked')) { 
																										$('#kriterij_nivo_njemackog_jezika_group').show();
																										$("#kriterij_nivo_njemackog_jezika").prop('required',true);
																									}
																								});
																								$('#kriterij_njemacki_jezik_ne').click(function() {
																									if($('#kriterij_njemacki_jezik_ne').is(':checked')) { 
																										$('#kriterij_nivo_njemackog_jezika_group').hide();
																										$("#kriterij_nivo_njemackog_jezika").prop('required',false);
																									}
																								});
																							});
																						</script>
																						
																						<!-- OSTALI JEZICI -->
																						<div class="form-group col-md-12">
																							<div class="col-xs-4" style="padding-top:5px; text-align:right;">Ostali jezici:</div>
																							<div class="col-xs-2 materail-input-block materail-input-block_success idk_radio_buttons">
																								<label class="main-container__column material-radio-group material-radio-group_success" for="kriterij_jezici_da">
																									<input type="radio" name="kriterij_jezici" id="kriterij_jezici_da" class="material-radiobox" value="1" />
																									<span class="material-radio-group__element material-radio-group__check-radio"></span>
																									<span class="material-radio-group__element material-radio-group__caption">DA</span>
																								</label>
																							</div>
																							<div class="col-xs-2 materail-input-block materail-input-block_danger idk_radio_buttons">
																								<label class="main-container__column material-radio-group material-radio-group_danger" for="kriterij_jezici_ne">
																									<input type="radio" name="kriterij_jezici" id="kriterij_jezici_ne" class="material-radiobox" value="0" checked />
																									<span class="material-radio-group__element material-radio-group__check-radio"></span>
																									<span class="material-radio-group__element material-radio-group__caption">NE</span>
																								</label>
																							</div>
																						</div>
																						<script>
																							$(document).ready(function () {
																								$('#kriterij_jezici_da').click(function() {
																									if($('#kriterij_jezici_da').is(':checked')) { 
																										$('#kriterij_ostali_jezici_group').show();
																									}
																								});
																								$('#kriterij_jezici_ne').click(function() {
																									if($('#kriterij_jezici_ne').is(':checked')) { 
																										$('#kriterij_ostali_jezici_group').hide();
																									}
																								});
																							});
																						</script>

																						<div id="kriterij_ostali_jezici_group" style="display:none;">
																							<!-- ENGLESKI JEZIK -->
																							<div class="form-group col-md-12">
																								<div class="col-xs-4" style="padding-top:5px; text-align:right;">Engleski jezik:</div>
																								<div class="col-xs-2 materail-input-block materail-input-block_success idk_radio_buttons kriterij_engleski_jezik_da">
																									<label class="main-container__column material-radio-group material-radio-group_success" for="kriterij_engleski_jezik_da">
																										<input type="radio" name="kriterij_engleski_jezik" id="kriterij_engleski_jezik_da" class="material-radiobox" value="1" />
																										<span class="material-radio-group__element material-radio-group__check-radio"></span>
																										<span class="material-radio-group__element material-radio-group__caption">DA</span>
																									</label>
																								</div>
																								<div class="col-xs-2 materail-input-block materail-input-block_danger idk_radio_buttons kriterij_engleski_jezik_ne">
																									<label class="main-container__column material-radio-group material-radio-group_danger" for="kriterij_engleski_jezik_ne">
																										<input type="radio" name="kriterij_engleski_jezik" id="kriterij_engleski_jezik_ne" class="material-radiobox" value="0" checked />
																										<span class="material-radio-group__element material-radio-group__check-radio"></span>
																										<span class="material-radio-group__element material-radio-group__caption">NE</span>
																									</label>
																								</div>
																							</div>

																							<!-- NIVO ENGLESKOG JEZIKA -->
																							<div class="form-group col-md-12" id="kriterij_nivo_engleskog_jezika_group" style="display:none;">
																								<div class="col-xs-4" style="padding-top:5px; text-align:right;">Minimalno poznavanje engleskog jezika:</div>
																								<div class="col-xs-6" style="padding-left:0px; ">
																									<select class="selectpicker" id="kriterij_nivo_engleskog_jezika" name="kriterij_nivo_engleskog_jezika" style="">
																										<option value="" disabled="disabled"></option>
																										<!-- <option value="BZ">Bez znanja</option> -->
																										<option value="A1">A1</option>
																										<option value="A2">A2</option>
																										<option value="B1">B1</option>
																										<option value="B2">B2</option>
																										<option value="C1">C1</option>
																										<option value="C2">C2</option>
																									</select>
																								</div>
																							</div>
																							<script>
																								$(document).ready(function () {
																									$('#kriterij_engleski_jezik_da').click(function() {
																										if($('#kriterij_engleski_jezik_da').is(':checked')) { 
																											$('#kriterij_nivo_engleskog_jezika_group').show();
																											$("#kriterij_nivo_engleskog_jezika").prop('required',true);
																										}
																									});
																									$('#kriterij_engleski_jezik_ne').click(function() {
																										if($('#kriterij_engleski_jezik_ne').is(':checked')) { 
																											$('#kriterij_nivo_engleskog_jezika_group').hide();
																											$("#kriterij_nivo_engleskog_jezika").prop('required',false);
																										}
																									});
																								});
																							</script>
																							<!-- ITALIJANSKI JEZIK -->
																							<div class="form-group col-md-12">
																								<div class="col-xs-4" style="padding-top:5px; text-align:right;">Italijanski jezik:</div>
																								<div class="col-xs-2 materail-input-block materail-input-block_success idk_radio_buttons kriterij_italijanski_jezik_da">
																									<label class="main-container__column material-radio-group material-radio-group_success" for="kriterij_italijanski_jezik_da">
																										<input type="radio" name="kriterij_italijanski_jezik" id="kriterij_italijanski_jezik_da" class="material-radiobox" value="1" />
																										<span class="material-radio-group__element material-radio-group__check-radio"></span>
																										<span class="material-radio-group__element material-radio-group__caption">DA</span>
																									</label>
																								</div>
																								<div class="col-xs-2 materail-input-block materail-input-block_danger idk_radio_buttons kriterij_italijanski_jezik_ne">
																									<label class="main-container__column material-radio-group material-radio-group_danger" for="kriterij_italijanski_jezik_ne">
																										<input type="radio" name="kriterij_italijanski_jezik" id="kriterij_italijanski_jezik_ne" class="material-radiobox" value="0" checked />
																										<span class="material-radio-group__element material-radio-group__check-radio"></span>
																										<span class="material-radio-group__element material-radio-group__caption">NE</span>
																									</label>
																								</div>
																							</div>

																							<!-- NIVO ITALIJANSKOG JEZIKA -->
																							<div class="form-group col-md-12" id="kriterij_nivo_italijanskog_jezika_group" style="display:none;">
																								<div class="col-xs-4" style="padding-top:5px; text-align:right;">Minimalno poznavanje engleskog jezika:</div>
																								<div class="col-xs-6" style="padding-left:0px; ">
																									<select class="selectpicker" id="kriterij_nivo_italijanskog_jezika" name="kriterij_nivo_italijanskog_jezika" style="">
																										<option value="" disabled="disabled"></option>
																										<!-- <option value="BZ">Bez znanja</option> -->
																										<option value="A1">A1</option>
																										<option value="A2">A2</option>
																										<option value="B1">B1</option>
																										<option value="B2">B2</option>
																										<option value="C1">C1</option>
																										<option value="C2">C2</option>
																									</select>
																								</div>
																							</div>
																							<script>
																								$(document).ready(function () {
																									$('#kriterij_italijanski_jezik_da').click(function() {
																										if($('#kriterij_italijanski_jezik_da').is(':checked')) { 
																											$('#kriterij_nivo_italijanskog_jezika_group').show();
																											$("#kriterij_nivo_italijanskog_jezika").prop('required',true);
																										}
																									});
																									$('#kriterij_italijanski_jezik_ne').click(function() {
																										if($('#kriterij_italijanski_jezik_ne').is(':checked')) { 
																											$('#kriterij_nivo_italijanskog_jezika_group').hide();
																											$("#kriterij_nivo_italijanskog_jezika").prop('required',false);
																										}
																									});
																								});
																							</script>
																							<!-- FRANCUSKI JEZIK -->
																							<div class="form-group col-md-12">
																								<div class="col-xs-4" style="padding-top:5px; text-align:right;">Francuski jezik:</div>
																								<div class="col-xs-2 materail-input-block materail-input-block_success idk_radio_buttons kriterij_francuski_jezik_da">
																									<label class="main-container__column material-radio-group material-radio-group_success" for="kriterij_francuski_jezik_da">
																										<input type="radio" name="kriterij_francuski_jezik" id="kriterij_francuski_jezik_da" class="material-radiobox" value="1" />
																										<span class="material-radio-group__element material-radio-group__check-radio"></span>
																										<span class="material-radio-group__element material-radio-group__caption">DA</span>
																									</label>
																								</div>
																								<div class="col-xs-2 materail-input-block materail-input-block_danger idk_radio_buttons kriterij_francuski_jezik_ne">
																									<label class="main-container__column material-radio-group material-radio-group_danger" for="kriterij_francuski_jezik_ne">
																										<input type="radio" name="kriterij_francuski_jezik" id="kriterij_francuski_jezik_ne" class="material-radiobox" value="0" checked />
																										<span class="material-radio-group__element material-radio-group__check-radio"></span>
																										<span class="material-radio-group__element material-radio-group__caption">NE</span>
																									</label>
																								</div>
																							</div>

																							<!-- NIVO FRANCUSKOG JEZIKA -->
																							<div class="form-group col-md-12" id="kriterij_nivo_francuskog_jezika_group" style="display:none;">
																								<div class="col-xs-4" style="padding-top:5px; text-align:right;">Minimalno poznavanje engleskog jezika:</div>
																								<div class="col-xs-6" style="padding-left:0px; ">
																									<select class="selectpicker" id="kriterij_nivo_francuskog_jezika" name="kriterij_nivo_francuskog_jezika" style="">
																										<option value="" disabled="disabled"></option>
																										<!-- <option value="BZ">Bez znanja</option> -->
																										<option value="A1">A1</option>
																										<option value="A2">A2</option>
																										<option value="B1">B1</option>
																										<option value="B2">B2</option>
																										<option value="C1">C1</option>
																										<option value="C2">C2</option>
																									</select>
																								</div>
																							</div>
																							<script>
																								$(document).ready(function () {
																									$('#kriterij_francuski_jezik_da').click(function() {
																										if($('#kriterij_francuski_jezik_da').is(':checked')) { 
																											$('#kriterij_nivo_francuskog_jezika_group').show();
																											$("#kriterij_nivo_francuskog_jezika").prop('required',true);
																										}
																									});
																									$('#kriterij_francuski_jezik_ne').click(function() {
																										if($('#kriterij_francuski_jezik_ne').is(':checked')) { 
																											$('#kriterij_nivo_francuskog_jezika_group').hide();
																											$("#kriterij_nivo_francuskog_jezika").prop('required',false);
																										}
																									});
																								});
																							</script>
																						</div>
																					</div>
																					<div class="modal-footer material-modal__footer">
																						<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
																						<button type="submit" class="add_nalog_profile_btn btn btn-primary material-btn material-btn_success" >Spremi</button>
																					</div>
																				</form>
																			</div>
																		</div>
																	</div>
																	
																	<?php
																	foreach($profili as $profil){
																		$vozacka_kategorija_niz = explode(',',$profil["kategorija_vozacke_dozvole"]);
																	?>
																		<div class="col-md-12 idk_margin_top10">
																			<div class="panel panel-default">
																				<div class="panel-heading" style="display:flex; justify-content: space-between;align-items: center;">
																					<div>
																						<p class="panel-title"><?php echo $profil["naziv"]; ?> - <span class="label label-info" style="padding:10px;s"><?php echo $profil["prioritet"]; ?></span></p>
																					</div>
																					<div>
																						<button class="delete_nalog_profile_btn btn btn-danger material-btn material-btn_danger" data-toggle="modal" data-target="#deleteNalogProfil<?php echo $profil["id"];?>"> Obriši</button>
																						<button class="edit_nalog_profile_btn btn btn-primary material-btn material-btn_primary" data-toggle="modal" data-target="#editNalogProfil<?php echo $profil["id"];?>"> Uredi</button>
																					</div>
																				</div>
																				<div class="panel-body">
																					<?php
																					if($profil["starost"] == 1){
																					?>
																						<b><p> Starost:</b> <?php echo  $profil['starost_minimum'].'-'. $profil['starost_maksimum'] ?></p>
																					<?php
																					}
																					?>
																					<?php 
																					if($profil["vozacka_dozvola"] == 1){
																					?>
																						<b><p> Vozačka dozvola:</b> <?php echo $profil['kategorija_vozacke_dozvole']; ?></p>
																					<?php
																					}
																					?>
																					<?php
																					if($profil["smjerovi_naloga"] == 1){
																					?>
																						<b><p> Smjerovi naloga:</b> DA</p>
																					<?php
																					}
																					?>
																					<?php 
																					if($profil["radno_iskustvo_struka"] == 1){
																					?>
																						<p><b>Radno iskustvo u struci:</b> DA - 
																							<?php
																								switch($profil["radno_iskustvo_struka_trajanje"]){
																									case "0":
																										echo "Ne treba u posljednjih 5 godina";
																									break;
																									case "1":
																										echo "Više od 0 godina";
																									break;
																									case "2":
																										echo "1 godina i više";
																									break;
																									case "3":
																										echo "2 godine i više";
																									break;
																									case "4":
																										echo "3 godine i više";
																									break;
																									case "5":
																										echo "4 godine i više";
																									break;
																									case "6":
																										echo "5 godina";
																									break;
																								}
																							?>
																						</p>
																					<?php
																					}
																					?>
																					<?php
																					if($profil["njemacki_jezik"] == 1){
																					?>
																					<b><p> Njemački jezik:</b> <?php echo $profil['nivo_njemackog_jezika']; ?></p>
																					<?php
																					}
																					?>
																					<?php
																					if($profil["znanje_drugog_jezika"] == 1){
																					?>
																					<b><p> Ostali jezici:</b> DA</p>
																						<?php
																						if($profil["engleski_jezik"] == 1){
																						?>
																							<b><p> Engleski jezik:</b> <?php echo $profil['nivo_engleskog_jezika']; ?></p>
																						<?php
																						}
																						?>
																						<?php
																						if($profil["francuski_jezik"] == 1){
																						?>
																							<b><p> Francuski jezik:</b> <?php echo $profil['nivo_francuskog_jezika']; ?></p>
																						<?php
																						}
																						?>
																						<?php
																						if($profil["italijanski_jezik"] == 1){
																						?>
																							<b><p> Italijanski jezik:</b> <?php echo $profil['nivo_italijanskog_jezika']; ?></p>
																						<?php
																						}
																						?>
																					<?php
																					}
																					?>
																				</div>
																			</div>
																		</div>
																		<!-- DELETE MODAL -->
																		<div class="modal material-modal material-modal_danger fade text-left" id="deleteNalogProfil<?php echo $profil["id"];?>">
																			<div class="modal-dialog ">
																				<div class="modal-content material-modal__content">
																					<div class="modal-header material-modal__header">
																						<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																						<h4 class="modal-title material-modal__title"><b>Jeste li sigurni da želite arhivirati ovaj profil?</b><h4>
																					</div>																								
																					<form action="<?php getSiteURL(); ?>do?form=delete_nalog_profile" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id = "delete_nalog_profile<?php echo $profil["id"];?>">
																						<div class="modal-body material-modal__body text-center">
																							<input type="hidden" name="profil_id" value="<?php echo $profil["profil_id"];?>" />
																							<input type="hidden" name="nalog_id" value="<?php echo $profil["nalog_id"];?>" />
																						</div>
																						<div class="modal-footer material-modal__footer">
																							<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
																							<button form="delete_nalog_profile<?php echo $profil["id"];?>" type="submit" class="btn btn-danger material-btn material-btn_danger" >Spremi</button>
																						</div>
																					</form>
																				</div>
																			</div>
																		</div>
																		<!-- EDIT MODAL -->
																		<div class="modal material-modal material-modal_primary fade text-left" id="editNalogProfil<?php echo $profil["id"];?>">
																			<div class="modal-dialog ">
																				<div class="modal-content material-modal__content">
																					<div class="modal-header material-modal__header">
																						<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																						<h4 class="modal-title material-modal__title"><b>Uredi profil</b><h4>
																					</div>																								
																					<form action="<?php getSiteURL(); ?>do?form=edit_nalog_profile" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id = "edit_nalog_profile<?php echo $profil["id"];?>">
																						<div class="modal-body material-modal__body text-center">
																							<input type="hidden" name="profil_id" value="<?php echo $profil["profil_id"];?>" />
																							<input type="hidden" name="nalog_id" value="<?php echo $profil["nalog_id"];?>" />
																							<div class="form-group">
																								<label for="naziv_profila" class="col-sm-4 control-label"><span class="text-danger">*</span> Naziv profila:</label>
																								<div class="col-sm-8">
																									<input class="form-control materail-input" type="text" name="naziv_profila" id="naziv_profila<?php echo $profil["id"]; ?>" placeholder="Unesite naziv profila" value="<?php echo $profil["naziv"];?>" required>
																								</div>
																							</div>

																							<div class="form-group">
																								<label for="prioritet_profila" class="col-sm-4 control-label"><span class="text-danger">*</span> Prioritet profila:</label>
																								<div class="col-sm-8">
																									<input class="form-control materail-input" type="number" min="1" name="prioritet_profila" id="prioritet_profila<?php echo $profil["id"]; ?>" placeholder="Unesite prioritet profila" value="<?php echo $profil["prioritet"];?>" required>
																								</div>
																							</div>
																							
																							<!-- SMJEROVI NALOGA -->
																							<div class="form-group col-xs-12" style="display:flex; align-items: center;">
																								<div class="col-xs-4" style="text-align:right;">Smjerovi naloga:</div>
																								<div class="col-xs-2 materail-input-block materail-input-block_success idk_radio_buttons kriterij_smjerovi_naloga_da<?php echo $profil["id"]; ?>">
																									<label class="main-container__column material-radio-group material-radio-group_success" for="kriterij_smjerovi_naloga_da<?php echo $profil["id"]; ?>">
																										<input type="radio" name="kriterij_smjerovi_naloga" id="kriterij_smjerovi_naloga_da<?php echo $profil["id"]; ?>" class="material-radiobox" value="1" <?php echo ($profil["smjerovi_naloga"]) ? 'checked' : ''; ?> />
																										<span class="material-radio-group__element material-radio-group__check-radio"></span>
																										<span class="material-radio-group__element material-radio-group__caption">DA</span>
																									</label>
																								</div>
																								<div class="col-xs-2 materail-input-block materail-input-block_danger idk_radio_buttons kriterij_smjerovi_naloga_ne<?php echo $profil["id"]; ?>">
																									<label class="main-container__column material-radio-group material-radio-group_danger" for="kriterij_smjerovi_naloga_ne<?php echo $profil["id"]; ?>">
																										<input type="radio" name="kriterij_smjerovi_naloga" id="kriterij_smjerovi_naloga_ne<?php echo $profil["id"]; ?>" class="material-radiobox" value="0" <?php echo (!$profil["smjerovi_naloga"]) ? 'checked' : ''; ?> />
																										<span class="material-radio-group__element material-radio-group__check-radio"></span>
																										<span class="material-radio-group__element material-radio-group__caption">NE</span>
																									</label>
																								</div>
																								<div class="col-xs-3">
																									<?php if(getNalogSmjeroviCntR($nalog_id) != 0){ ?>
																										<span style="font-size: 10px; font-style: italic;">Smjerovi unešeni</span>
																									<?php } ?>
																									<a href="<?php getSiteURL(); ?>nalozi?page=open&id=<?php echo $nalog_id; ?>&tab=smjerovi_naloga" style="display:none; font-size: 10px; " class="goto_smjerovi_naloga btn-primary material-btn material-btn_success"><i class="fa fa-plus" aria-hidden="true"></i> Unesi smjerove</a>
																								</div>
																							</div>
																							
																							<script>
																								$('.kriterij_smjerovi_naloga_da<?php echo $profil["id"]; ?>').click(function(){
																									$.ajax({
																										type: "POST",
																										url: 'ajax_data.php?page=check_nalog_smjerovi_settings',
																										data: {
																											nalog_id: "<?php echo $nalog_id; ?>"
																										},
																										success: function(data){
																											if(data == 0){
																												$('.goto_smjerovi_naloga').css('display', 'inline-block');
																												$('.edit_nalog_profile').css('display', 'none');
																											}
																										}
																									}); 
																								});
																								$('.kriterij_smjerovi_naloga_ne<?php echo $profil["id"]; ?>').click(function(){
																									$('.goto_smjerovi_naloga').css('display', 'none');
																									$('.edit_nalog_profile').css('display', 'inline-block');
																								});
																							</script>
																							<!-- VOZACKA DOZVOLA -->
																							<div class="form-group col-md-12">
																								<div class="col-xs-4" style="padding-top:5px; text-align:right;">Vozačka dozvola:</div>
																								<div class="col-xs-2 materail-input-block materail-input-block_success idk_radio_buttons kriterij_vozacka_dozvola_da<?php echo $profil["id"]; ?>">
																									<label class="main-container__column material-radio-group material-radio-group_success" for="kriterij_vozacka_dozvola_da_edit<?php echo $profil["id"]; ?>">
																										<input type="radio" name="kriterij_vozacka_dozvola" id="kriterij_vozacka_dozvola_da_edit<?php echo $profil["id"]; ?>" class="material-radiobox" value="1" <?php echo ($profil["vozacka_dozvola"]) ? 'checked' : ''; ?> />
																										<span class="material-radio-group__element material-radio-group__check-radio"></span>
																										<span class="material-radio-group__element material-radio-group__caption">DA</span>
																									</label>
																								</div>
																								<div class="col-xs-2 materail-input-block materail-input-block_danger idk_radio_buttons kriterij_vozacka_dozvola_ne<?php echo $profil["id"]; ?>">
																									<label class="main-container__column material-radio-group material-radio-group_danger" for="kriterij_vozacka_dozvola_ne<?php echo $profil["id"]; ?>">
																										<input type="radio" name="kriterij_vozacka_dozvola" id="kriterij_vozacka_dozvola_ne<?php echo $profil["id"]; ?>" class="material-radiobox" value="0" <?php echo (!$profil["vozacka_dozvola"]) ? 'checked' : ''; ?> />
																										<span class="material-radio-group__element material-radio-group__check-radio"></span>
																										<span class="material-radio-group__element material-radio-group__caption">NE</span>
																									</label>
																								</div>
																							</div>
																							<div class="form-group col-md-12 kriterij_kategorije_vozacke<?php echo $profil["id"]; ?>" <?php if(!$profil["vozacka_dozvola"]){ echo 'style="display: none"';}else{echo 'style="display: block"';} ?>>
																								<div class="col-xs-4" style="padding-top:5px; text-align:right;">Kategorija vozačke dozvole:</div>
																								<div class="col-xs-6" style="padding-left:0px;">
																									<select class="selectpicker" multiple id="kriterij_vozacka_kat<?php echo $profil["id"]; ?>" name="kriterij_vozacka_kat[]"  style="width: 75%;" >	
																										<option value="B" <?php if(in_array("B", $vozacka_kategorija_niz)){ echo "selected"; } ?>>B</option>
																										<option value="C1" <?php if(in_array("C1", $vozacka_kategorija_niz)){ echo "selected"; } ?>>C1</option>
																										<option value="C" <?php if(in_array("C", $vozacka_kategorija_niz)){ echo "selected"; } ?>>C</option>
																										<option value="BE" <?php if(in_array("BE", $vozacka_kategorija_niz)){ echo "selected"; } ?>>BE</option>
																										<option value="C1E" <?php if(in_array("C1E", $vozacka_kategorija_niz)){ echo "selected"; } ?>>C1E</option>
																										<option value="CE" <?php if(in_array("CE", $vozacka_kategorija_niz)){ echo "selected"; } ?>>CE</option>
																									</select>
																								</div>
																							</div>
																							<script>
																								$('.kriterij_vozacka_dozvola_da<?php echo $profil["id"]; ?>').click(function(){
																									$('.kriterij_kategorije_vozacke<?php echo $profil["id"]; ?>').css('display', 'block');
																									$('#kriterij_vozacka_kat<?php echo $profil["id"]; ?>').attr('required', 'true');
																								});
																								$('.kriterij_vozacka_dozvola_ne<?php echo $profil["id"]; ?>').click(function(){
																									$('.kriterij_kategorije_vozacke<?php echo $profil["id"]; ?>').css('display', 'none');
																									$("#kriterij_vozacka_kat<?php echo $profil["id"]; ?>t").val(null).selectpicker('refresh');
																									$('#kriterij_vozacka_kat<?php echo $profil["id"]; ?>').removeAttr('required');

																								});		
																							</script>
																							<!-- RADNO ISKUSTVO U STRUCI -->
																							<div class="form-group col-md-12">
																								<div class="col-xs-4" style="padding-top:5px; text-align:right;">Radno iskustvo u struci:</div>
																								<div class="col-xs-2 materail-input-block materail-input-block_success idk_radio_buttons">
																									<label class="main-container__column material-radio-group material-radio-group_success" for="kriterij_iskustvo_struka_da<?php echo $profil["id"]; ?>">
																										<input type="radio" name="kriterij_iskustvo_struka" id="kriterij_iskustvo_struka_da<?php echo $profil["id"]; ?>" class="material-radiobox" value="1" <?php echo ($profil["radno_iskustvo_struka"]) ? 'checked' : ''; ?> />
																										<span class="material-radio-group__element material-radio-group__check-radio"></span>
																										<span class="material-radio-group__element material-radio-group__caption">DA</span>
																									</label>
																								</div>
																								<div class="col-xs-2 materail-input-block materail-input-block_danger idk_radio_buttons">
																									<label class="main-container__column material-radio-group material-radio-group_danger" for="kriterij_iskustvo_struka_ne<?php echo $profil["id"]; ?>">
																										<input type="radio" name="kriterij_iskustvo_struka" id="kriterij_iskustvo_struka_ne<?php echo $profil["id"]; ?>" class="material-radiobox" value="0" <?php echo (!$profil["radno_iskustvo_struka"]) ? 'checked' : ''; ?> />
																										<span class="material-radio-group__element material-radio-group__check-radio"></span>
																										<span class="material-radio-group__element material-radio-group__caption">NE</span>
																									</label>
																								</div>
																							</div>

																							<!-- RADNO ISKUSTVO TRAJANJE -->
																							<div class="form-group col-md-12" id="kriterij_iskustvo_trajanje_group<?php echo $profil["id"]; ?>" <?php if(!$profil["radno_iskustvo_struka"]){ echo 'style="display: none"';}else{echo 'style="display: block"';} ?>>
																								<div class="col-xs-4" style="padding-top:5px; text-align:right;">Radno iskustvo u struci u posljednjih 5 godina:</div>
																								<div class="col-xs-6">
																									<select class="selectpicker kriterij_iskustvo_trajanje<?php echo $profil["id"]; ?>" id="kriterij_iskustvo_trajanje<?php echo $profil["id"]; ?>" name="kriterij_iskustvo_trajanje">
																										<option value="0" <?php echo ($profil["radno_iskustvo_struka_trajanje"] == 0) ? 'selected' : ''; ?>>Ne treba</option>
																										<option value="1" <?php echo ($profil["radno_iskustvo_struka_trajanje"] == 1) ? 'selected' : ''; ?>>Više od 0</option>
																										<option value="2" <?php echo ($profil["radno_iskustvo_struka_trajanje"] == 2) ? 'selected' : ''; ?>>1 godina i više</option>
																										<option value="3" <?php echo ($profil["radno_iskustvo_struka_trajanje"] == 3) ? 'selected' : ''; ?>>2 godine i više</option>
																										<option value="4" <?php echo ($profil["radno_iskustvo_struka_trajanje"] == 4) ? 'selected' : ''; ?>>3 godine i više</option>
																										<option value="5" <?php echo ($profil["radno_iskustvo_struka_trajanje"] == 5) ? 'selected' : ''; ?>>4 godine i više</option>
																										<option value="6" <?php echo ($profil["radno_iskustvo_struka_trajanje"] == 6) ? 'selected' : ''; ?>>5 godina i više</option>
																									</select>
																								</div>
																							</div>
																							<script>
																								$(document).ready(function () {
																									
																									$('#kriterij_iskustvo_struka_da<?php echo $profil["id"]; ?>').click(function() {
																										if($('#kriterij_iskustvo_struka_da<?php echo $profil["id"]; ?>').is(':checked')) { 
																											$('#kriterij_iskustvo_trajanje_group<?php echo $profil["id"]; ?>').show();
																											$("#kriterij_iskustvo_trajanje<?php echo $profil["id"]; ?>").prop('required',true);
																										}
																									});
																									$('#kriterij_iskustvo_struka_ne<?php echo $profil["id"]; ?>').click(function() {
																										if($('#kriterij_iskustvo_struka_ne<?php echo $profil["id"]; ?>').is(':checked')) { 
																											$('#kriterij_iskustvo_trajanje_group<?php echo $profil["id"]; ?>').hide();
																											$("#kriterij_iskustvo_trajanje<?php echo $profil["id"]; ?>").prop('required',false);
																										}
																									});
																								});
																							</script>

																							<!-- STAROST KANDIDATA -->
																							<div class="form-group col-md-12">
																								<div class="col-xs-4" style="padding-top:5px; text-align:right;">Starost kandidata:</div>
																								<div class="col-xs-2 materail-input-block materail-input-block_success idk_radio_buttons">
																									<label class="main-container__column material-radio-group material-radio-group_success" for="kriterij_starost_kandidata_da<?php echo $profil["id"]; ?>">
																										<input type="radio" name="kriterij_starost_kandidata" id="kriterij_starost_kandidata_da<?php echo $profil["id"]; ?>" class="material-radiobox" value="1" <?php echo ($profil["starost"]) ? 'checked' : ''; ?> />
																										<span class="material-radio-group__element material-radio-group__check-radio"></span>
																										<span class="material-radio-group__element material-radio-group__caption">DA</span>
																									</label>
																								</div>
																								<div class="col-xs-2 materail-input-block materail-input-block_danger idk_radio_buttons">
																									<label class="main-container__column material-radio-group material-radio-group_danger" for="kriterij_starost_kandidata_ne<?php echo $profil["id"]; ?>">
																										<input type="radio" name="kriterij_starost_kandidata" id="kriterij_starost_kandidata_ne<?php echo $profil["id"]; ?>" class="material-radiobox" value="0" <?php echo (!$profil["starost"]) ? 'checked' : ''; ?> />
																										<span class="material-radio-group__element material-radio-group__check-radio"></span>
																										<span class="material-radio-group__element material-radio-group__caption">NE</span>
																									</label>
																								</div>
																							</div>																

																							<div class="form-group col-md-12 kriterij_starost_kandidata_group<?php echo $profil["id"]; ?>" <?php if(!$profil["starost"]){ echo 'style="display: none"';}else{echo 'style="display: block"';} ?>>
																								<div class="col-xs-4" style="padding-top:5px; text-align:right;">Kandidat mora imati između:</div>
																								<label class="col-xs-6 main-container__column material-radio-group material-radio-group_danger" for="kriterij_starost_kandidata<?php echo $profil["id"]; ?>">
																									<input type="number" id="kriterij_starost_od<?php echo $profil["id"]; ?>" name="kriterij_starost_od" min="18" max="99" value="<?php echo ($profil["starost_minimum"]); ?>">
																									<input type="number" id="kriterij_starost_do<?php echo $profil["id"]; ?>" name="kriterij_starost_do" min="18" max="99" value="<?php echo ($profil["starost_maksimum"]); ?>">
																								</label>
																							</div>
																							<script>
																								$('#kriterij_starost_kandidata_da<?php echo $profil["id"]; ?>').click(function(){
																									$('#kriterij_starost_od<?php echo $profil["id"]; ?>').prop('required',true);
																									$('#kriterij_starost_do<?php echo $profil["id"]; ?>').prop('required',true);
																									
																									$('.kriterij_starost_kandidata_group<?php echo $profil["id"]; ?>').css('display', 'block');
																								});
																								$('#kriterij_starost_kandidata_ne<?php echo $profil["id"]; ?>').click(function(){
																									$('#kriterij_starost_od<?php echo $profil["id"]; ?>').removeAttr('required');
																									$('#kriterij_starost_do<?php echo $profil["id"]; ?>').removeAttr('required');
																									
																									$('.kriterij_starost_kandidata_group<?php echo $profil["id"]; ?>').css('display', 'none');
																								});
																							</script>
																							
																							<!-- NJEMACKI JEZIK -->
																							<div class="form-group col-md-12">
																								<div class="col-xs-4" style="padding-top:5px; text-align:right;">Njemački jezik:</div>
																								<div class="col-xs-2 materail-input-block materail-input-block_success idk_radio_buttons kriterij_njemacki_jezik_da<?php echo $profil["id"]; ?>">
																									<label class="main-container__column material-radio-group material-radio-group_success" for="kriterij_njemacki_jezik_da<?php echo $profil["id"]; ?>">
																										<input type="radio" name="kriterij_njemacki_jezik" id="kriterij_njemacki_jezik_da<?php echo $profil["id"]; ?>" class="material-radiobox" value="1" <?php echo ($profil["njemacki_jezik"]) ? 'checked' : ''; ?> />
																										<span class="material-radio-group__element material-radio-group__check-radio"></span>
																										<span class="material-radio-group__element material-radio-group__caption">DA</span>
																									</label>
																								</div>
																								<div class="col-xs-2 materail-input-block materail-input-block_danger idk_radio_buttons kriterij_njemacki_jezik_ne<?php echo $profil["id"]; ?>">
																									<label class="main-container__column material-radio-group material-radio-group_danger" for="kriterij_njemacki_jezik_ne<?php echo $profil["id"]; ?>">
																										<input type="radio" name="kriterij_njemacki_jezik" id="kriterij_njemacki_jezik_ne<?php echo $profil["id"]; ?>" class="material-radiobox" value="0" <?php echo (!$profil["njemacki_jezik"]) ? 'checked' : ''; ?> />
																										<span class="material-radio-group__element material-radio-group__check-radio"></span>
																										<span class="material-radio-group__element material-radio-group__caption">NE</span>
																									</label>
																								</div>
																							</div>
																							<!-- NIVO NJEMAČKOG JEZIKA -->
																							<div class="form-group col-md-12" id="kriterij_nivo_njemackog_jezika_group<?php echo $profil["id"]; ?>" <?php if(!$profil["njemacki_jezik"]){ echo 'style="display: none"';}else{echo 'style="display: block"';} ?>>
																								<div class="col-xs-4" style="padding-top:5px; text-align:right;">Minimalno poznavanje njemačkog jezika:</div>
																								<div class="col-xs-6">
																									<select class="selectpicker" id="kriterij_nivo_njemackog_jezika<?php echo $profil["id"]; ?>" name="kriterij_nivo_njemackog_jezika" style="">
																										<option value="" disabled="disabled"></option>
																										<!-- <option value="BZ">Bez znanja</option> -->
																										<option value="A1" <?php echo ($profil["nivo_njemackog_jezika"] == "A1") ? 'selected' : ''; ?>>A1</option>
																										<option value="A2" <?php echo ($profil["nivo_njemackog_jezika"] == "A2") ? 'selected' : ''; ?>>A2</option>
																										<option value="B1" <?php echo ($profil["nivo_njemackog_jezika"] == "B1") ? 'selected' : ''; ?>>B1</option>
																										<option value="B2" <?php echo ($profil["nivo_njemackog_jezika"] == "B2") ? 'selected' : ''; ?>>B2</option>
																										<option value="C1" <?php echo ($profil["nivo_njemackog_jezika"] == "C1") ? 'selected' : ''; ?>>C1</option>
																										<option value="C2" <?php echo ($profil["nivo_njemackog_jezika"] == "C2") ? 'selected' : ''; ?>>C2</option>
																									</select>
																								</div>
																							</div>
																							<script>
																								$(document).ready(function () {
																									$('#kriterij_njemacki_jezik_da<?php echo $profil["id"]; ?>').click(function() {
																										if($('#kriterij_njemacki_jezik_da<?php echo $profil["id"]; ?>').is(':checked')) { 
																											$('#kriterij_nivo_njemackog_jezika_group<?php echo $profil["id"]; ?>').show();
																											$("#kriterij_nivo_njemackog_jezika<?php echo $profil["id"]; ?>").prop('required',true);
																										}
																									});
																									$('#kriterij_njemacki_jezik_ne<?php echo $profil["id"]; ?>').click(function() {
																										if($('#kriterij_njemacki_jezik_ne<?php echo $profil["id"]; ?>').is(':checked')) { 
																											$('#kriterij_nivo_njemackog_jezika_group<?php echo $profil["id"]; ?>').hide();
																											$("#kriterij_nivo_njemackog_jezika<?php echo $profil["id"]; ?>").prop('required',false);
																										}
																									});
																								});
																							</script>
																							
																							<!-- OSTALI JEZICI -->
																							<div class="form-group col-md-12">
																								<div class="col-xs-4" style="padding-top:5px; text-align:right;">Ostali jezici:</div>
																								<div class="col-xs-2 materail-input-block materail-input-block_success idk_radio_buttons">
																									<label class="main-container__column material-radio-group material-radio-group_success" for="kriterij_jezici_da<?php echo $profil["id"]; ?>">
																										<input type="radio" name="kriterij_jezici" id="kriterij_jezici_da<?php echo $profil["id"]; ?>" class="material-radiobox" value="1" <?php echo ($profil["znanje_drugog_jezika"]) ? 'checked' : ''; ?> />
																										<span class="material-radio-group__element material-radio-group__check-radio"></span>
																										<span class="material-radio-group__element material-radio-group__caption">DA</span>
																									</label>
																								</div>
																								<div class="col-xs-2 materail-input-block materail-input-block_danger idk_radio_buttons">
																									<label class="main-container__column material-radio-group material-radio-group_danger" for="kriterij_jezici_ne<?php echo $profil["id"]; ?>">
																										<input type="radio" name="kriterij_jezici" id="kriterij_jezici_ne<?php echo $profil["id"]; ?>" class="material-radiobox" value="0" <?php echo (!$profil["znanje_drugog_jezika"]) ? 'checked' : ''; ?> />
																										<span class="material-radio-group__element material-radio-group__check-radio"></span>
																										<span class="material-radio-group__element material-radio-group__caption">NE</span>
																									</label>
																								</div>
																							</div>
																							<script>
																								$(document).ready(function () {
																									$('#kriterij_jezici_da<?php echo $profil["id"]; ?>').click(function() {
																										if($('#kriterij_jezici_da<?php echo $profil["id"]; ?>').is(':checked')) { 
																											$('#kriterij_ostali_jezici_group<?php echo $profil["id"]; ?>').show();
																										}
																									});
																									$('#kriterij_jezici_ne<?php echo $profil["id"]; ?>').click(function() {
																										if($('#kriterij_jezici_ne<?php echo $profil["id"]; ?>').is(':checked')) { 
																											$('#kriterij_ostali_jezici_group<?php echo $profil["id"]; ?>').hide();
																										}
																									});
																								});
																							</script>

																							<div id="kriterij_ostali_jezici_group<?php echo $profil["id"]; ?>" <?php if(!$profil["znanje_drugog_jezika"]){ echo 'style="display: none"';}else{echo 'style="display: block"';} ?>>
																								<!-- ENGLESKI JEZIK -->
																								<div class="form-group col-md-12">
																									<div class="col-xs-4" style="padding-top:5px; text-align:right;">Engleski jezik:</div>
																									<div class="col-xs-2 materail-input-block materail-input-block_success idk_radio_buttons kriterij_engleski_jezik_da<?php echo $profil["id"]; ?>">
																										<label class="main-container__column material-radio-group material-radio-group_success" for="kriterij_engleski_jezik_da<?php echo $profil["id"]; ?>">
																											<input type="radio" name="kriterij_engleski_jezik" id="kriterij_engleski_jezik_da<?php echo $profil["id"]; ?>" class="material-radiobox" value="1" <?php echo ($profil["engleski_jezik"]) ? 'checked' : ''; ?> />
																											<span class="material-radio-group__element material-radio-group__check-radio"></span>
																											<span class="material-radio-group__element material-radio-group__caption">DA</span>
																										</label>
																									</div>
																									<div class="col-xs-2 materail-input-block materail-input-block_danger idk_radio_buttons kriterij_engleski_jezik_ne<?php echo $profil["id"]; ?>">
																										<label class="main-container__column material-radio-group material-radio-group_danger" for="kriterij_engleski_jezik_ne<?php echo $profil["id"]; ?>">
																											<input type="radio" name="kriterij_engleski_jezik" id="kriterij_engleski_jezik_ne<?php echo $profil["id"]; ?>" class="material-radiobox" value="0" <?php echo (!$profil["engleski_jezik"]) ? 'checked' : ''; ?> />
																											<span class="material-radio-group__element material-radio-group__check-radio"></span>
																											<span class="material-radio-group__element material-radio-group__caption">NE</span>
																										</label>
																									</div>
																								</div>

																								<!-- NIVO ENGLESKOG JEZIKA -->
																								<div class="form-group col-md-12" id="kriterij_nivo_engleskog_jezika_group<?php echo $profil["id"]; ?>" <?php if(!$profil["engleski_jezik"]){ echo 'style="display: none"';}else{echo 'style="display: block"';} ?>>
																									<div class="col-xs-4" style="padding-top:5px; text-align:right;">Minimalno poznavanje engleskog jezika:</div>
																									<div class="col-xs-6" style="padding-left:0px; ">
																										<select class="selectpicker" id="kriterij_nivo_engleskog_jezika<?php echo $profil["id"]; ?>" name="kriterij_nivo_engleskog_jezika" style="">
																											<option value="" disabled="disabled"></option>
																											<!-- <option value="BZ">Bez znanja</option> -->
																											<option value="A1" <?php echo ($profil["nivo_engleskog_jezika"] == "A1") ? 'selected' : ''; ?>>A1</option>
																											<option value="A2" <?php echo ($profil["nivo_engleskog_jezika"] == "A3") ? 'selected' : ''; ?>>A2</option>
																											<option value="B1" <?php echo ($profil["nivo_engleskog_jezika"] == "B1") ? 'selected' : ''; ?>>B1</option>
																											<option value="B2" <?php echo ($profil["nivo_engleskog_jezika"] == "B2") ? 'selected' : ''; ?>>B2</option>
																											<option value="C1" <?php echo ($profil["nivo_engleskog_jezika"] == "C1") ? 'selected' : ''; ?>>C1</option>
																											<option value="C2" <?php echo ($profil["nivo_engleskog_jezika"] == "C2") ? 'selected' : ''; ?>>C2</option>
																										</select>
																									</div>
																								</div>
																								<script>
																									$(document).ready(function () {
																										$('#kriterij_engleski_jezik_da<?php echo $profil["id"]; ?>').click(function() {
																											if($('#kriterij_engleski_jezik_da<?php echo $profil["id"]; ?>').is(':checked')) { 
																												$('#kriterij_nivo_engleskog_jezika_group<?php echo $profil["id"]; ?>').show();
																												$("#kriterij_nivo_engleskog_jezika<?php echo $profil["id"]; ?>").prop('required',true);
																											}
																										});
																										$('#kriterij_engleski_jezik_ne<?php echo $profil["id"]; ?>').click(function() {
																											if($('#kriterij_engleski_jezik_ne<?php echo $profil["id"]; ?>').is(':checked')) { 
																												$('#kriterij_nivo_engleskog_jezika_group<?php echo $profil["id"]; ?>').hide();
																												$("#kriterij_nivo_engleskog_jezika<?php echo $profil["id"]; ?>").prop('required',false);
																											}
																										});
																									});
																								</script>
																								<!-- ITALIJANSKI JEZIK -->
																								<div class="form-group col-md-12">
																									<div class="col-xs-4" style="padding-top:5px; text-align:right;">Italijanski jezik:</div>
																									<div class="col-xs-2 materail-input-block materail-input-block_success idk_radio_buttons kriterij_italijanski_jezik_da<?php echo $profil["id"]; ?>">
																										<label class="main-container__column material-radio-group material-radio-group_success" for="kriterij_italijanski_jezik_da<?php echo $profil["id"]; ?>">
																											<input type="radio" name="kriterij_italijanski_jezik" id="kriterij_italijanski_jezik_da<?php echo $profil["id"]; ?>" class="material-radiobox" value="1" <?php echo ($profil["italijanski_jezik"]) ? 'checked' : ''; ?> />
																											<span class="material-radio-group__element material-radio-group__check-radio"></span>
																											<span class="material-radio-group__element material-radio-group__caption">DA</span>
																										</label>
																									</div>
																									<div class="col-xs-2 materail-input-block materail-input-block_danger idk_radio_buttons kriterij_italijanski_jezik_ne<?php echo $profil["id"]; ?>">
																										<label class="main-container__column material-radio-group material-radio-group_danger" for="kriterij_italijanski_jezik_ne<?php echo $profil["id"]; ?>">
																											<input type="radio" name="kriterij_italijanski_jezik" id="kriterij_italijanski_jezik_ne<?php echo $profil["id"]; ?>" class="material-radiobox" value="0" <?php echo (!$profil["italijanski_jezik"]) ? 'checked' : ''; ?> />
																											<span class="material-radio-group__element material-radio-group__check-radio"></span>
																											<span class="material-radio-group__element material-radio-group__caption">NE</span>
																										</label>
																									</div>
																								</div>

																								<!-- NIVO ITALIJANSKOG JEZIKA -->
																								<div class="form-group col-md-12" id="kriterij_nivo_italijanskog_jezika_group<?php echo $profil["id"]; ?>"  <?php if(!$profil["italijanski_jezik"]){ echo 'style="display: none"';}else{echo 'style="display: block"';} ?>>
																									<div class="col-xs-4" style="padding-top:5px; text-align:right;">Minimalno poznavanje engleskog jezika:</div>
																									<div class="col-xs-6" style="padding-left:0px; ">
																										<select class="selectpicker" id="kriterij_nivo_italijanskog_jezika<?php echo $profil["id"]; ?>" name="kriterij_nivo_italijanskog_jezika" style="">
																											<option value="" disabled="disabled"></option>
																											<!-- <option value="BZ">Bez znanja</option> -->
																											<option value="A1" <?php echo ($profil["nivo_italijanskog_jezika"] == "A1") ? 'selected' : ''; ?>>A1</option>
																											<option value="A2" <?php echo ($profil["nivo_italijanskog_jezika"] == "A2") ? 'selected' : ''; ?>>A2</option>
																											<option value="B1" <?php echo ($profil["nivo_italijanskog_jezika"] == "B1") ? 'selected' : ''; ?>>B1</option>
																											<option value="B2" <?php echo ($profil["nivo_italijanskog_jezika"] == "B2") ? 'selected' : ''; ?>>B2</option>
																											<option value="C1" <?php echo ($profil["nivo_italijanskog_jezika"] == "C1") ? 'selected' : ''; ?>>C1</option>
																											<option value="C2" <?php echo ($profil["nivo_italijanskog_jezika"] == "C2") ? 'selected' : ''; ?>>C2</option>
																										</select>
																									</div>
																								</div>
																								<script>
																									$(document).ready(function () {
																										$('#kriterij_italijanski_jezik_da<?php echo $profil["id"]; ?>').click(function() {
																											if($('#kriterij_italijanski_jezik_da<?php echo $profil["id"]; ?>').is(':checked')) { 
																												$('#kriterij_nivo_italijanskog_jezika_group<?php echo $profil["id"]; ?>').show();
																												$("#kriterij_nivo_italijanskog_jezika<?php echo $profil["id"]; ?>").prop('required',true);
																											}
																										});
																										$('#kriterij_italijanski_jezik_ne<?php echo $profil["id"]; ?>').click(function() {
																											if($('#kriterij_italijanski_jezik_ne<?php echo $profil["id"]; ?>').is(':checked')) { 
																												$('#kriterij_nivo_italijanskog_jezika_group<?php echo $profil["id"]; ?>').hide();
																												$("#kriterij_nivo_italijanskog_jezika<?php echo $profil["id"]; ?>").prop('required',false);
																											}
																										});
																									});
																								</script>
																								<!-- FRANCUSKI JEZIK -->
																								<div class="form-group col-md-12">
																									<div class="col-xs-4" style="padding-top:5px; text-align:right;">Francuski jezik:</div>
																									<div class="col-xs-2 materail-input-block materail-input-block_success idk_radio_buttons kriterij_francuski_jezik_da<?php echo $profil["id"]; ?>">
																										<label class="main-container__column material-radio-group material-radio-group_success" for="kriterij_francuski_jezik_da<?php echo $profil["id"]; ?>">
																											<input type="radio" name="kriterij_francuski_jezik" id="kriterij_francuski_jezik_da<?php echo $profil["id"]; ?>" class="material-radiobox" value="1" <?php echo ($profil["francuski_jezik"]) ? 'checked' : ''; ?> />
																											<span class="material-radio-group__element material-radio-group__check-radio"></span>
																											<span class="material-radio-group__element material-radio-group__caption">DA</span>
																										</label>
																									</div>
																									<div class="col-xs-2 materail-input-block materail-input-block_danger idk_radio_buttons kriterij_francuski_jezik_ne<?php echo $profil["id"]; ?>">
																										<label class="main-container__column material-radio-group material-radio-group_danger" for="kriterij_francuski_jezik_ne<?php echo $profil["id"]; ?>">
																											<input type="radio" name="kriterij_francuski_jezik" id="kriterij_francuski_jezik_ne<?php echo $profil["id"]; ?>" class="material-radiobox" value="0" <?php echo (!$profil["francuski_jezik"]) ? 'checked' : ''; ?> />
																											<span class="material-radio-group__element material-radio-group__check-radio"></span>
																											<span class="material-radio-group__element material-radio-group__caption">NE</span>
																										</label>
																									</div>
																								</div>

																								<!-- NIVO FRANCUSKOG JEZIKA -->
																								<div class="form-group col-md-12" id="kriterij_nivo_francuskog_jezika_group<?php echo $profil["id"]; ?>" <?php if(!$profil["francuski_jezik"]){ echo 'style="display: none"';}else{echo 'style="display: block"';} ?>>
																									<div class="col-xs-4" style="padding-top:5px; text-align:right;">Minimalno poznavanje engleskog jezika:</div>
																									<div class="col-xs-6" style="padding-left:0px; ">
																										<select class="selectpicker" id="kriterij_nivo_francuskog_jezika<?php echo $profil["id"]; ?>" name="kriterij_nivo_francuskog_jezika" style="">
																											<option value="" disabled="disabled"></option>
																											<!-- <option value="BZ">Bez znanja</option> -->
																											<option value="A1" <?php echo ($profil["nivo_francuskog_jezika"] == "A1") ? 'selected' : ''; ?>>A1</option>
																											<option value="A2" <?php echo ($profil["nivo_francuskog_jezika"] == "A2") ? 'selected' : ''; ?>>A2</option>
																											<option value="B1" <?php echo ($profil["nivo_francuskog_jezika"] == "B1") ? 'selected' : ''; ?>>B1</option>
																											<option value="B2" <?php echo ($profil["nivo_francuskog_jezika"] == "B2") ? 'selected' : ''; ?>>B2</option>
																											<option value="C1" <?php echo ($profil["nivo_francuskog_jezika"] == "C1") ? 'selected' : ''; ?>>C1</option>
																											<option value="C2" <?php echo ($profil["nivo_francuskog_jezika"] == "C2") ? 'selected' : ''; ?>>C2</option>
																										</select>
																									</div>
																								</div>
																								<script>
																									$(document).ready(function () {
																										$('#kriterij_francuski_jezik_da<?php echo $profil["id"]; ?>').click(function() {
																											if($('#kriterij_francuski_jezik_da<?php echo $profil["id"]; ?>').is(':checked')) { 
																												$('#kriterij_nivo_francuskog_jezika_group<?php echo $profil["id"]; ?>').show();
																												$("#kriterij_nivo_francuskog_jezika<?php echo $profil["id"]; ?>").prop('required',true);
																											}
																										});
																										$('#kriterij_francuski_jezik_ne<?php echo $profil["id"]; ?>').click(function() {
																											if($('#kriterij_francuski_jezik_ne<?php echo $profil["id"]; ?>').is(':checked')) { 
																												$('#kriterij_nivo_francuskog_jezika_group<?php echo $profil["id"]; ?>').hide();
																												$("#kriterij_nivo_francuskog_jezika<?php echo $profil["id"]; ?>").prop('required',false);
																											}
																										});
																									});
																								</script>
																							</div>
																						</div>
																						<div class="modal-footer material-modal__footer">
																							<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
																							<button form="edit_nalog_profile<?php echo $profil["id"];?>" type="submit" class="edit_nalog_profile btn btn-primary material-btn material-btn_primary" >Spremi</button>
																						</div>
																					</form>
																				</div>
																			</div>
																		</div>
																	<?php
																	}
																	?>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="tab-pane fade <?php if($tab=="vizadokumenti"){echo "active in";} ?>" id="vizadokumenti">
									<div class="row">
										<div class="col-lg-12" id="documents_box">
											<div class="row">
												<div class="col-xs-12">
													<div class="row">
														<div class="col-md-12">
															<div class="text-center" style = "font-weight: bold; font-size: 18px;"><i class="fa fa-files-o" style = "margin-right: 10px;" aria-hidden="true"></i>Dokumenti za vizu</div>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="panel panel-default">
														<div class="panel-body">
															<div class="content_box content_documents">
																<div class="row" style="margin-bottom: 15px;">
																	<div class="col-md-offset-1 col-md-10 text-center">
																		<span style="font-size: large;border-bottom: 1px solid #ccc;font-weight: 600;">Lista aktivnih dokumenata</span> 
																	</div>
																</div>
																<div class="row" style="margin-bottom: 20px;">
																	<div class="col-md-offset-1 col-md-10">
																		<div class="row">
																			<div class="col-lg-6 text-left">
																				<a href="" data-toggle="modal" data-target="#activateDoc" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive">
																					<i class="fa fa-plus" aria-hidden="true">
																					</i>
																					<span>
																						Uključi dokument
																					</span>
																				</a>
																				<div class="modal material-modal material-modal_success fade text-left" id="activateDoc">
																					<div class="modal-dialog modal-lg">
																						<div class="modal-content material-modal__content">
																							<div class="modal-header material-modal__header">
																								<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																								<h4 class="modal-title material-modal__title"><i style = "margin-right: 10px;" class="fa fa-plus" aria-hidden="true"></i>Uključi novi tip dokumenta</h4>
																							</div> 
																							<div class="modal-body material-modal__body">
																								<form action="<?php getSiteURL(); ?>do?form=activate_document_pp" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id = "form_activate_document_pp">
																									<input type="hidden" id="acd_nalog" name="acd_nalog" value="<?php echo $nalog_id; ?>">
																									<!--
																										acd - skraćenica od: activate document
																									-->

																									<div class="form-group">
																										<div class="col-md-offset-1 col-sm-10">
																											<div class="alert alert-warning text-center" role="alert">
																												Obratite pažnju prilikom određivanja zaduženosti za dokument u polju <strong>Zadužen</strong>!</br>
																												U pozadini sistema se kriju akcije koje se izvršavaju na osnovu postavljenog parametra i pogrešno postavljanje bi moglo dovesti do neželjenog toka procesa.
																											</div>
																										</div>
																									</div>
																									<div class="form-group">
																										<div class="col-md-offset-1 col-sm-10 text-center">
																											<label for="acd_doc_id" class="col-sm-4 control-label">
																												<span class="text-danger">
																													*
																												</span>
																												Dokument:
																											</label>
																											<div class="col-sm-8">
																												<div class="">
																													<select class="selectpicker" title = "Odaberite tip dokumenta koji želite aktivirati" data-actions-box="true" data-live-search = "true" id="acd_doc_id" name="acd_doc_id" required>
																														<?php 
																															$queryActivate = $db->prepare("
																																SELECT 
																																	dt.doc_type_id, dt.doc_type_name, dt.doc_type_name_de
																																FROM 
																																	idk_pp_document_types dt
																																WHERE
																																	dt.doc_type_id NOT IN (
																																		SELECT 
																																			nrd.nrd_type_id
																																		FROM 
																																			idk_pp_nalog_required_documents nrd
																																		WHERE 
																																			nrd.nrd_status = 1
																																			AND 
																																			nrd.nrd_nalog_id = :nalogId
																																	)
																															");

																															$queryActivate->execute(array(
																																":nalogId" => $nalog_id
																															));

																															while($rowActivate = $queryActivate->fetch()){
																																$doc_type_id_AC = $rowActivate["doc_type_id"];
																																$doc_type_name_AC = $rowActivate["doc_type_name"];
																																$doc_type_name_de_AC = $rowActivate["doc_type_name_de"];

																																echo '
																																	<option value="'.$doc_type_id_AC.'" data-subtext="Naziv DE: '.$doc_type_name_de_AC.'">'.$doc_type_name_AC.'</option>
																																';
																															}
																														?>
																													</select>
																												</div>
																											</div>
																										</div>
																									</div>
																									<div class="form-group">
																										<div class="col-md-offset-1 col-sm-10 text-center">
																											<label for="strucni_kadar_yn" class="col-sm-4 control-label">
																												<span class="text-danger">*</span>Potreban za Stručni kadar:
																											</label>
																											<div class="col-sm-8 text-left">
																												<label class="main-container__column material-radio-group material-radio-group_success" for="sk_treba">	
																													<input type="radio" name="sk_potreban" id="sk_treba" class="material-radiobox" value="1" checked>
																													<span class="material-radio-group__element material-radio-group__check-radio"></span>
																													<span class="material-radio-group__element material-radio-group__caption">DA</span>
																												</label>
																												<label class="main-container__column material-radio-group material-radio-group_danger" for="sk_ne_treba">	
																													<input type="radio" name="sk_potreban" id="sk_ne_treba" class="material-radiobox" value="0" >
																													<span class="material-radio-group__element material-radio-group__check-radio"></span>
																													<span class="material-radio-group__element material-radio-group__caption">NE</span>
																												</label>
																											</div>
																										</div>
																									</div>
																									<div class="form-group">
																										<div class="col-md-offset-1 col-sm-10 text-center">
																											<label for="west_balkan_yn" class="col-sm-4 control-label">
																												<span class="text-danger">*</span>Potreban za Zapadni Balkan:
																											</label>
																											<div class="col-sm-8 text-left">
																												<label class="main-container__column material-radio-group material-radio-group_success" for="zb_treba">	
																													<input type="radio" name="zb_potreban" id="zb_treba" class="material-radiobox" value="1">
																													<span class="material-radio-group__element material-radio-group__check-radio"></span>
																													<span class="material-radio-group__element material-radio-group__caption">DA</span>
																												</label>
																												<label class="main-container__column material-radio-group material-radio-group_danger" for="zb_ne_treba">	
																													<input type="radio" name="zb_potreban" id="zb_ne_treba" class="material-radiobox" value="0" checked>
																													<span class="material-radio-group__element material-radio-group__check-radio"></span>
																													<span class="material-radio-group__element material-radio-group__caption">NE</span>
																												</label>
																											</div>
																										</div>
																									</div>
																									<div class="form-group">
																										<div class="col-md-offset-1 col-sm-10 text-center">
																											<label for="work_experience_yn" class="col-sm-4 control-label">
																												<span class="text-danger">*</span>Potreban za Radno iskustvo:
																											</label>
																											<div class="col-sm-8 text-left">
																												<label class="main-container__column material-radio-group material-radio-group_success" for="ri_treba">	
																													<input type="radio" name="ri_potreban" id="ri_treba" class="material-radiobox" value="1">
																													<span class="material-radio-group__element material-radio-group__check-radio"></span>
																													<span class="material-radio-group__element material-radio-group__caption">DA</span>
																												</label>
																												<label class="main-container__column material-radio-group material-radio-group_danger" for="ri_ne_treba">	
																													<input type="radio" name="ri_potreban" id="ri_ne_treba" class="material-radiobox" value="0" checked>
																													<span class="material-radio-group__element material-radio-group__check-radio"></span>
																													<span class="material-radio-group__element material-radio-group__caption">NE</span>
																												</label>
																											</div>
																										</div>
																									</div>
																									<div class="form-group">
																										<div class="col-md-offset-1 col-sm-10 text-center">
																											<label for="acd_done_by" class="col-sm-4 control-label">
																												<span class="text-danger">
																													*
																												</span>
																												Zadužen:
																											</label>
																											<div class="col-sm-8">
																												<div class="">
																													<select class="selectpicker" title = "Postavite zaduženje za ovaj dokument" data-actions-box="true" id="acd_done_by" name="acd_done_by" required>
																														<option value="1">Poslodavac</option>
																														<option value="2">Kandidat</option>
																														<option value="3">JobStep</option>
																													</select>
																												</div>
																											</div>
																										</div>
																									</div>
																									<script>
																										$("#acd_done_by").change(function(){
																											if(parseInt($('#acd_done_by').val()) == 1){
																												$('.zaduzen_poslodavac_message').removeClass('hidden');
																											}else{
																												$('.zaduzen_poslodavac_message').addClass('hidden');
																											}
																										});
																									</script>
																									<div class="form-group hidden zaduzen_poslodavac_message">
																										<div class="col-md-offset-1 col-sm-10 text-center">
																											<div class="alert alert-danger" role="alert">
																												<img src="<?php getSiteUrlr(); ?>images/Germany.png" width="50">
																												<br>
																												<strong>U slučaju da je potrebno unijeti komentar, zaduženjem poslodavca za ovaj dokument, komentar se mora unijeti na njemačkom jeziku!</strong>
																											</div>
																										</div>
																									</div>
																									<div class="form-group">
																										<div class="col-md-offset-1 col-sm-10 text-center">
																											<label for="acd_comment" class="col-sm-4 control-label">
																												Komentar:
																											</label>
																											<div class="col-sm-8">
																												<div class="form-group materail-input-block materail-input-block_primary styleForTextArea">
																													<textarea class="form-control materail-input material-textarea" name="acd_comment" id="acd_comment" placeholder="Unesite komentar" rows="8"></textarea>
																													<!--<span class="materail-input-block__line"></span>-->
																												</div>
																											</div>
																										</div>
																									</div>
																									<div class="modal-footer material-modal__footer">
																										<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
																										<button class="btn btn-primary material-btn material-btn_success actDocSave"><i class="fa fa-check-square-o" aria-hidden="true"></i> Dodaj</button>
																									</div>
																								</form>
																								<script>
																									$( ".actDocSave" ).click(function() {
																										if($("#acd_done_by").val() != "" && $("#acd_doc_id").val() != "" && $("#acd_nalog").val() != ""){
																											$('.actDocSave').prop('disabled', true);
																											console.log("Popunjeno!");
																											$( "#form_activate_document_pp" ).submit();
																										}else{
																											console.log("Nije popunjeno!");
																										}
																									});
																								</script>
																							</div>
																						</div>
																					</div>
																				</div>
																			</div>
																			<div class="col-lg-6 text-right">
																				<a href="" data-toggle="modal" data-target="#archiveDoc" class="btn material-btn material-btn-icon-danger material-btn_danger main-container__column material-btn-icon-responsive">
																					<i class="fa fa-minus" aria-hidden="true"></i>
																					<span>
																						Isključi dokument
																					</span>
																				</a>
																				<div class="modal material-modal material-modal_danger fade text-left" id="archiveDoc">
																					<div class="modal-dialog modal-lg">
																						<div class="modal-content material-modal__content">
																							<div class="modal-header material-modal__header">
																								<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																								<h4 class="modal-title material-modal__title"><i style = "margin-right: 10px;" class="fa fa-plus" aria-hidden="true"></i>Isključi tip dokumenta</h4>
																							</div> 
																							<div class="modal-body material-modal__body">
																								<form action="<?php getSiteURL(); ?>do?form=archived_document_pp" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id = "form_archived_document_pp">
																									<input type="hidden" id="ard_nalog" name="ard_nalog" value="<?php echo $nalog_id; ?>">
																									<!--
																										ard - skraćenica od: archive document
																									-->
																									<div class="form-group">
																										<div class="col-md-offset-2 col-sm-8 text-center">
																											<label for="ard_nrd_id" class="col-sm-4 control-label">
																												<span class="text-danger">
																													*
																												</span>
																												Dokument:
																											</label>
																											<div class="col-sm-8">
																												<div class="">
																													<select class="selectpicker" title = "Odaberite tip dokumenta koji želite arhivirati" data-actions-box="true" data-live-search = "true" id="ard_nrd_id" name="ard_nrd_id[]" multiple required>
																														<?php 
																															$queryArchive = $db->prepare("
																																SELECT 
																																	dt.doc_type_name, dt.doc_type_name_de, nrd.nrd_done_by, nrd.nrd_id
																																FROM 
																																	idk_pp_document_types dt
																																JOIN 
																																	idk_pp_nalog_required_documents nrd
																																ON 
																																	dt.doc_type_id = nrd.nrd_type_id
																																WHERE  
																																	nrd.nrd_nalog_id = :nalogId
																																	AND 
																																	nrd.nrd_status = 1
																															");

																															$queryArchive->execute(array(
																																":nalogId" => $nalog_id
																															));

																															while($rowArchive = $queryArchive->fetch()){
																																$nrd_id_AR = $rowArchive["nrd_id"];
																																$doc_type_name_AR = $rowArchive["doc_type_name"];
																																$doc_type_name_de_AR = $rowArchive["doc_type_name_de"];
																																$nrd_done_by_AR_value = $rowArchive["nrd_done_by"];
																																if($nrd_done_by_AR_value == 1){
																																	$nrd_done_by_AR_text = "Poslodavac";
																																}else if($nrd_done_by_AR_value == 2){
																																	$nrd_done_by_AR_text = "JobStep";
																																}else{
																																	$nrd_done_by_AR_text = "Kandidat";
																																}

																																echo '
																																	<option value="'.$nrd_id_AR.'" data-subtext="Naziv DE: '.$doc_type_name_de_AR.' Dostavlja: '.$nrd_done_by_AR_text.'">'.$doc_type_name_AR.'</option>
																																';
																															}
																														?>
																													</select>
																												</div>
																											</div>
																										</div>
																									</div>
																									<div class="modal-footer material-modal__footer">
																										<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
																										<button class="btn btn-primary material-btn material-btn_danger deactDocSave"><i class="fa fa-check-square-o" aria-hidden="true"></i> Isključi</button>
																									</div>
																								</form>
																								<script>
																									$( ".deactDocSave" ).click(function() {
																										if($("#ard_nrd_id").val() != null && $("#ard_nalog").val() != ""){
																											$('.deactDocSave').prop('disabled', true);
																											console.log("Popunjeno!");
																											$( "#form_archived_document_pp" ).submit();
																										}else{
																											console.log("Nije popunjeno!");
																										}
																									});
																								</script>
																							</div>
																						</div>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
																<div class="row">
																	<div class="col-md-12">
																		<!-- Kategorija 1 - dokumenti procesa odlaska -->
																		<script type="text/javascript">
																			$(document).ready(function() {
																				$('#doc_category_1').DataTable({

																					responsive: true,

																					"order": [[ 0, "asc" ]],

																					"bAutoWidth": false,

																					"aoColumns": [
																							{ "width": "3%"},
																							{ "width": "21%" },
																							{ "width": "21%" },
																							{ "width": "10%" },
																							{ "width": "10%" },
																							{ "width": "10%" },
																							{ "width": "10%" },
																							{ "width": "5%" },
																							{ "width": "5%" },
																							{ "width": "5%" }
																						]
																				});
																			} );
																		</script>
																		<table id="doc_category_1" class="display" cellspacing="0" width="100%">
																			<thead>
																				<tr>
																					<th class="text-center">#</th>
																					<th class="text-center">Naziv <img src="<?php getSiteUrlr(); ?>images/BosniaHerzegowina.png" width="20"></th>
																					<th class="text-center">Naziv <img src="<?php getSiteUrlr(); ?>images/Germany.png" width="20"></th>
																					<th class="text-center">Dostavlja</th>
																					<th class="text-center">Stručni kadar</th>
																					<th class="text-center">Zapadni balkan</th>
																					<th class="text-center">Radno iskustvo</th>
																					<th class="text-center">Komentar</th>
																					<th class="text-center">Aktivirao</th>
																					<th class="text-center">Datum aktiviranja</th>
																				</tr>
																			</thead>
																			<tbody>
																				<?php 
																					$count_c1 = 0;
																					$query_category_1 = $db->prepare("
																						SELECT 
																							dt.doc_type_name,
																							dt.doc_type_name_de,
																							nrd.nrd_done_by, 
																							nrd.nrd_comment, 
																							emp.employee_firstname,
																							emp.employee_lastname,
																							nrd.nrd_date_activated,
																							nrd.nrd_skilled_candidates,
																							nrd.nrd_west_balkan,
																							nrd.nrd_work_experience,
																							nrd.nrd_id
																						FROM 
																							idk_pp_nalog_required_documents nrd
																						JOIN 
																							idk_pp_document_types dt
																						ON 
																							nrd.nrd_type_id = dt.doc_type_id
																						JOIN 
																							idk_employees emp
																						ON 
																							nrd.nrd_user_activated = emp.employee_id
																						WHERE 
																							nrd.nrd_status = 1
																							AND 
																							nrd.nrd_nalog_id = :nalodId
																					");
																					$query_category_1->execute(array(
																						":nalodId" => $nalog_id
																					));

																					while($row_category_1 = $query_category_1->fetch()){
																						$count_c1++;
																						$nrd_id = $row_category_1["nrd_id"];
																						$doc_type_name_c1 = $row_category_1["doc_type_name"];
																						$doc_type_name_de_c1 = $row_category_1["doc_type_name_de"];
																						$nrd_done_by_val_c1 = $row_category_1["nrd_done_by"];
																						
																						if($nrd_done_by_val_c1 == 1 ){
																							$nrd_done_by_text_c1 = '<span class="material-label material-label_success material-label_xs main-container__column text-center">Poslodavac</span>';
																						}else if($nrd_done_by_val_c1 == 2){
																							$nrd_done_by_text_c1 = '<span class="material-label material-label_warning material-label_xs main-container__column text-center">Kandidat</span>';
																						}else{
																							$nrd_done_by_text_c1 = '<span class="material-label material-label_danger material-label_xs main-container__column text-center">JobStep</span>';
																						}

																						$nrd_skilled_candidates = $row_category_1["nrd_skilled_candidates"];
																						if($nrd_skilled_candidates == 1){
																							$nrd_skilled_candidates_text = '<button data-toggle="modal" data-target="#modalChangeSK" data-nrd_id="'.$nrd_id.'" data-to_update="0" class="edit_skilled_candidates material-label material-label_success material-label_xs">Treba <i class="fa fa-edit" aria-hidden="true"></i></button>';
																						}else{
																							$nrd_skilled_candidates_text = '<button data-toggle="modal" data-target="#modalChangeSK" data-nrd_id="'.$nrd_id.'" data-to_update="1" class="edit_skilled_candidates material-label material-label_danger material-label_xs">Ne treba <i class="fa fa-edit" aria-hidden="true"></i></button>';
																						}

																						$nrd_west_balkan = $row_category_1["nrd_west_balkan"];
																						if($nrd_west_balkan == 1){
																							$nrd_west_balkan_text = '<button data-toggle="modal" data-target="#modalChangeZB" data-nrd_id="'.$nrd_id.'" class="sa_treba material-label material-label_success material-label_xs">Treba <i class="fa fa-edit" aria-hidden="true"></i></button>';
																						}else{
																							$nrd_west_balkan_text = '<button data-toggle="modal" data-target="#modalChangeZB" data-nrd_id="'.$nrd_id.'" class="sa_ne_treba material-label material-label_danger material-label_xs">Ne treba <i class="fa fa-edit" aria-hidden="true"></i></button>';
																						}

																						$nrd_work_experience = $row_category_1["nrd_work_experience"];
																						if($nrd_work_experience == 1){
																							$nrd_work_experience_text = '<button data-toggle="modal" data-target="#modalChangeRI" data-nrd_id="'.$nrd_id.'" class="ri_sa_treba material-label material-label_success material-label_xs">Treba <i class="fa fa-edit" aria-hidden="true"></i></button>';
																						}else{
																							$nrd_work_experience_text = '<button data-toggle="modal" data-target="#modalChangeRI" data-nrd_id="'.$nrd_id.'" class="ri_sa_ne_treba material-label material-label_danger material-label_xs">Ne treba <i class="fa fa-edit" aria-hidden="true"></i></button>';
																						}

																						$nrd_comment_val_c1 = $row_category_1["nrd_comment"];
																						if($nrd_comment_val_c1 != NULL){
																							$nrd_comment_text_c1 = '<i class="fa fa-comment fa-2x" style = "color: green;" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="" data-original-title="'.$nrd_comment_val_c1.'"></i>';
																						}else{
																							$nrd_comment_text_c1 = '<i class="fa fa-comment fa-2x" style = "color: red;" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="" data-original-title="Nije unešen komentar!"></i>';
																						}
																						$nrd_user_activated_c1 = $row_category_1["employee_firstname"]." ".$row_category_1["employee_lastname"];
																						$nrd_date_activated_c1 = date("d.m.Y H:i", strtotime($row_category_1["nrd_date_activated"]));

																						echo '
																							<tr>
																								<td class="text-center">'.$count_c1.'</td>
																								<td class="text-center">'.$doc_type_name_c1.'</td>
																								<td class="text-center">'.$doc_type_name_de_c1.'</td>
																								<td class="text-center" data-order="'.$nrd_done_by_val_c1.'">'.$nrd_done_by_text_c1.'</td>
																								<td class="text-center">'.$nrd_skilled_candidates_text.'</td>
																								<td class="text-center">'.$nrd_west_balkan_text.'</td>
																								<td class="text-center">'.$nrd_work_experience_text.'</td>
																								<td class="text-center">'.$nrd_comment_text_c1.'</td>
																								<td class="text-center">'.$nrd_user_activated_c1.'</td>
																								<td class="text-center" data-order="'.strtotime($nrd_date_activated_c1).'">'.$nrd_date_activated_c1.'</td>
																							</tr>
																						';
																					}
																				?>
																			</tbody>
																		</table>
																		<script>
																			$(".sa_treba").click(function(){
																				var nrd_id = $(this).data("nrd_id");
																				$("#nrd_id_zb", '#modalChangeZB').val(nrd_id);
																				var to_update = 0;
																				$("#to_update_zb", '#modalChangeZB').val(to_update);
																				document.getElementById('zb_text_notice').innerHTML = 'Potvrdom označavate da dokument <b>nije potreban</b> za Zapadno-balkanski način odlaska';
																			});
																			$(".sa_ne_treba").click(function(){
																				var nrd_id = $(this).data("nrd_id");
																				$("#nrd_id_zb", '#modalChangeZB').val(nrd_id);
																				var to_update = 1;
																				$("#to_update_zb", '#modalChangeZB').val(to_update);
																				document.getElementById('zb_text_notice').innerHTML = 'Potvrdom označavate da je dokument <b>potreban</b> za Zapadno-balkanski način odlaska!';
																			});
																		</script>
																		<!-- MODAL PROMJENA POTREBNOSTI DOKUMENTA ZA ZAPADNO-BALKANSKI SISTEM -->
																		<div class="modal material-modal material-modal_success fade" id="modalChangeZB">
																			<div class="modal-dialog">
																				<div class="modal-content material-modal__content">
																					<div class="modal-header material-modal__header">
																						<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																						<h4 class="modal-title material-modal__title">Promjena dokumenta za Zapadno-balkanski način odlaska</h4>
																					</div>
																					<div class="modal-body material-modal__body">
																					<form action="<?php getSiteURL(); ?>do?form=changeDocumentZB" method="POST">
																							<div class="form-group">
																								<div class="col-md-offset-2 col-sm-8" id="zb_text_notice">
																									
																								</div>
																							</div>
																						</div>
																						<input type="hidden" name="nrd_id_zb" id="nrd_id_zb">
																						<input type="hidden" name="to_update_zb" id="to_update_zb">
																						<input type="hidden" name="nalog_id_zb" id="nalog_id_zb" value=<?php echo $nalog_id; ?>>
																						<div class="modal-footer material-modal__footer">
																							<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
																							<button type="submit" class="btn btn-primary material-btn material-btn_success val_isk_da">Potvrdi</button>
																						</div>
																					</form>
																				</div>
																			</div>
																		</div>
																		<script>
																			$(".ri_sa_treba").click(function(){
																				var nrd_id = $(this).data("nrd_id");
																				$("#nrd_id_ri", "#modalChangeRI").val(nrd_id);
																				var to_update = 0;
																				$("#to_update_ri", "#modalChangeRI").val(to_update);
																				document.getElementById('ri_text_notice').innerHTML = 'Potvrdom označavate da dokument <b>nije potreban</b> za način odlaska Radno iskustvo!';
																			});
																			$(".ri_sa_ne_treba").click(function(){
																				var nrd_id = $(this).data("nrd_id");
																				$("#nrd_id_ri", "#modalChangeRI").val(nrd_id);
																				var to_update = 1;
																				$("#to_update_ri", "#modalChangeRI").val(to_update);
																				document.getElementById('ri_text_notice').innerHTML = 'Potvrdom označavate da je dokument <b>potreban</b> za način odlaska Radno iskustvo!';
																			});
																		</script>
																		<!-- MODAL PROMJENA POTREBNOSTI DOKUMENTA ZA SISTEM Radno iskustvo -->
																		<div class="modal material-modal material-modal_success fade" id="modalChangeRI">
																			<div class="modal-dialog">
																				<div class="modal-content material-modal__content">
																					<div class="modal-header material-modal__header">
																						<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																						<h4 class="modal-title material-modal__title">Promjena dokumenta za način odlaska Radno iskustvo</h4>
																					</div>
																					<div class="modal-body material-modal__body">
																					<form action="<?php getSiteURL(); ?>do?form=changeDocumentRI" method="POST">
																							<div class="form-group">
																								<div class="col-md-offset-2 col-sm-8" id="ri_text_notice">
																									
																								</div>
																							</div>
																						</div>
																						<input type="hidden" name="nrd_id_ri" id="nrd_id_ri">
																						<input type="hidden" name="to_update_ri" id="to_update_ri">
																						<input type="hidden" name="nalog_id_ri" id="nalog_id_ri" value=<?php echo $nalog_id; ?>>
																						<div class="modal-footer material-modal__footer">
																							<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
																							<button type="submit" class="btn btn-primary material-btn material-btn_success">Potvrdi</button>
																						</div>
																					</form>
																				</div>
																			</div>
																		</div>
																		<script>
																			$(".edit_skilled_candidates").click(function(){
																				var nrd_id = $(this).data("nrd_id");
																				var to_update = $(this).data("to_update");
																				$("#nrd_id_sk", "#modalChangeSK").val(nrd_id);
																				$("#to_update_sk", "#modalChangeSK").val(to_update);
																				$("#sk_text_notice", "#modalChangeSK").html("Potvrdom označavate da "+(to_update == 1 ? "je dokument <b> potreban" : "dokument <b>nije potreban" )+"</b> za način odlaska <b>Stručni kadar</b>!");
																			});
																		</script>
																		<!-- MODAL PROMJENA POTREBNOSTI DOKUMENTA ZA SISTEM Stručni kadar -->
																		<div class="modal material-modal material-modal_success fade" id="modalChangeSK">
																			<div class="modal-dialog">
																				<div class="modal-content material-modal__content">
																					<div class="modal-header material-modal__header">
																						<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																						<h4 class="modal-title material-modal__title">Promjena dokumenta za način odlaska Stručni kadar</h4>
																					</div>
																					<div class="modal-body material-modal__body">
																					<form action="<?php getSiteURL(); ?>do?form=changeDocumentSK" method="POST">
																							<div class="form-group">
																								<div class="col-md-offset-2 col-sm-8" id="sk_text_notice">
																									
																								</div>
																							</div>
																						</div>
																						<input type="hidden" name="nrd_id_sk" id="nrd_id_sk">
																						<input type="hidden" name="to_update_sk" id="to_update_sk">
																						<input type="hidden" name="nalog_id_sk" id="nalog_id_sk" value=<?php echo $nalog_id; ?>>
																						<div class="modal-footer material-modal__footer">
																							<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
																							<button type="submit" class="btn btn-primary material-btn material-btn_success">Potvrdi</button>
																						</div>
																					</form>
																				</div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								
								<div class="tab-pane fade <?php if($tab=="opis_partnerapp"){echo "active in";} ?>" id="opis_partnerapp">
									<?php
										$query_no = $db->prepare("
															SELECT no_lang,  no_nalogid,   no_nalognaziv, no_nalogopis
															FROM idk_nalozi_opis
															WHERE no_nalogid = :no_nalogid");
														
										$query_no->execute(array(
															':no_nalogid' => $nalog_id));
										
										$query_n_o = $query_no->rowCount();
																				
										$lang_bs = array();
										$lang_de = array();
										$lang_en = array();
										$lang_it = array();
										
										while($row = $query_no->fetch()){
											$query = [
												'no_nalognaziv' => $row['no_nalognaziv'],
												'no_nalogopis' => $row['no_nalogopis'],
											];
											
											if($row['no_lang'] == 'bs'){
												$lang_bs[] = $query;
											}elseif($row['no_lang'] == 'de'){
												$lang_de[] = $query;
											}elseif($row['no_lang'] == 'it'){
												$lang_it[] = $query;
											}elseif($row['no_lang'] == 'en'){
												$lang_en[] = $query;
											}elseif($row['no_lang'] == 'sr'){
												$lang_sr[] = $query;
											}
										}
										
										
										if(	(empty($lang_bs[0]['no_nalogopis'])) AND (empty($lang_de[0]['no_nalogopis'])) AND (empty($lang_it[0]['no_nalogopis'])) AND (empty($lang_en[0]['no_nalogopis']))	){
											$nodescription = 'text-danger';
										}else{
											$nodescription = 'text-warning';
										}
										
										
										$query_link_g = $db->prepare("
												SELECT *
												FROM idk_link_generator	
												WHERE  lg_partner_app = 1 AND lg_nalogid = :lg_nalogid AND (idk_urlimg_prijave IS NOT NULL OR idk_urlimg_prijave != 'none')
												ORDER BY lg_id DESC");
								 
										$query_link_g->execute(array(':lg_nalogid' => $nalog_id));
										
										$query_l_g = $query_link_g->rowCount();
										$row = $query_link_g->fetch();
										$lg_id = $row['lg_id'];
										$lg_link_prijave = $row['lg_link_prijave'];
										$idk_urlimg_prijave = $row['idk_urlimg_prijave'];
										$lg_url = $row['lg_url'];
										$lg_desc = $row['lg_desc'];
										$lg_nalogid = $row['lg_nalogid'];
										$lg_language = $row['lg_language'];
										
										if($query_l_g > 0){
											$link_form_action = getSiteUrlr()."do.php?form=generate_new_link_edit";
										}else{
											$link_form_action = getSiteUrlr()."do.php?form=generate_new_link";
										}	
										$partner_app_details_query = $db->prepare("SELECT
																			nalog_partner_app_location,
																			nalog_partner_app_until,
																			nalog_partner_app_salary
																		FROM
																			idk_nalozi
																		WHERE
																			nalog_id = :nalog_id");
										
										$partner_app_details_query->execute(array(':nalog_id'=>$nalog_id));

										$partner_app_details = $partner_app_details_query->fetch();

										$nalog_partner_app_location	= $partner_app_details["nalog_partner_app_location"];
										$nalog_partner_app_until 	= $partner_app_details["nalog_partner_app_until"];
										$nalog_partner_app_salary 	= $partner_app_details["nalog_partner_app_salary"];

										$partner_app_details_check = 0;

										if(isset($nalog_partner_app_location) AND isset($nalog_partner_app_until) AND isset($nalog_partner_app_salary)){
											$partner_app_details_check = 1;
										}


									?>
									<style>
										.text-warning{
											color:#ffc107!important;
										}
									</style>
									<!--**************************************************
											Postavke naloga za Partner APP
									*****************************************************-->									
									<div class="row">
										<div class="col-xs-8">
											<h1><i class="fa fa-tasks idk_color_green" aria-hidden="true"></i> Postavke naloga za Partner APP </h1>
										</div>
										<div class="col-xs-12">
											<hr />
										</div>
										<div class="col-md-offset-1 col-md-11">
											<?php if($nalog_partner_active == 1){ ?><p class="text-success"><i class="fa fa-check-circle" aria-hidden="true"></i> Nalog aktiviran. </p><?php }else{ ?><p class="text-danger"><i class="fa fa-ban" aria-hidden="true"></i> Potrebno aktivirati nalog.</p><?php } ?>
											
											<?php if(intval($query_l_g) == 0){ ?>
												<p class="text-danger"><i class="fa fa-ban" aria-hidden="true"></i> Potreban website link(job-step.net) u  Generator linkova.</p>
											<?php }else{ ?>
											
												<p class="text-success"><i class="fa fa-check-circle" aria-hidden="true"></i>  Website link dodan u  Generator linkova.</p>
											
												<?php if(($idk_urlimg_prijave == 'none')){ ?>
													<p  class="text-danger"> <i class="fa fa-ban" aria-hidden="true"></i> Potrebna slika/thumbnail naloga u Link generator.</p>
												<?php }else{ ?>
													<p class="text-success"><i class="fa fa-check-circle" aria-hidden="true"></i>  Slika/thumbnail je dodana u  Generator linkova.</p>
												<?php } ?>	
											
											<?php } ?>	
											
											<?php if($partner_app_details_check == 1){ ?><p class="text-success"><i class="fa fa-check-circle" aria-hidden="true"></i> Unešene dodatne informacije.</p><?php }else{ ?><p class="<?php echo $nodescription; ?>"><i class="fa fa-ban" aria-hidden="true"></i> Potrebno unijeti dodatne informacije za nalog.</p><?php } ?>
											<?php if(!empty($lang_de[0]['no_nalogopis'])){ ?><p class="text-success"><i class="fa fa-check-circle" aria-hidden="true"></i> Dodan opis naloga za njemački jezik.</p><?php }else{ ?><p class="<?php echo $nodescription; ?>"><i class="fa fa-ban" aria-hidden="true"></i> Potreban opis naloga za njemački jezik.</p><?php } ?>
											<?php if(!empty($lang_en[0]['no_nalogopis'])){ ?><p class="text-success"><i class="fa fa-check-circle" aria-hidden="true"></i> Dodan opis naloga za engleski jezik.</p><?php }else{ ?><p class="<?php echo $nodescription; ?>"><i class="fa fa-ban" aria-hidden="true"></i> Potreban opis naloga za engleski jezik.</p><?php } ?>
											<?php if(!empty($lang_bs[0]['no_nalogopis'])){ ?><p class="text-success"><i class="fa fa-check-circle" aria-hidden="true"></i> Dodan opis naloga za bosanski jezik.</p><?php }else{ ?><p style="color:#ffc107!important"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Potreban opis naloga za bosanski jezik.</p><?php } ?>
											<?php if(!empty($lang_sr[0]['no_nalogopis'])){ ?><p class="text-success"><i class="fa fa-check-circle" aria-hidden="true"></i> Dodan opis naloga za srpski jezik.</p><?php }else{ ?><p style="color:#ffc107!important"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Potreban opis naloga za srpski jezik.</p><?php } ?>
											
											<?php if(intval($nalog_partner_provizija) == 0){ ?>
											<a href = "<?php getSiteUrl(); ?>nalozi?page=edit&id=<?php echo $nalog_id; ?>"><p class="text-warning" style="color:#ffc107!important"> <i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Potrebno unijeti proviziju. Trenutna provizija: <?php echo $nalog_partner_provizija; ?>€</p></a>
											<?php }else{ ?>
											<p class="text-success"> <i class="fa fa-check-circle" aria-hidden="true"></i> Trenutna provizija: <?php echo $nalog_partner_provizija; ?>€</p>
											<?php } ?>

										</div>
										<?php if((in_array( "7" , $employee_status)) OR (in_array( "1" , $employee_status))){	?>
										<div class="col-xs-11 text-right">
											<form action="<?php getSiteURL(); ?>do.php?form=partner_nalog_active" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
												<input type="hidden" name="nalog_id" value="<?php echo $nalog_id; ?>" />
												<?php 
												if(($lang_de[0]['no_nalogopis'] ?? null) != null AND ($lang_en[0]['no_nalogopis'] ?? null) != null AND intval($query_l_g) != 0 AND $partner_app_details_check == 1){
												?>
													<button class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive" <?php if($nalog_partner_active == 1){ echo 'disabled'; } ?>>   <i class="fa fa-refresh" aria-hidden="true"></i> Aktiviraj partnerima</button>
												<?php
												}else{
												?>
													<button class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive" <?php if($nalog_partner_active == 1){ echo 'disabled'; } ?> disabled>   <i class="fa fa-refresh" aria-hidden="true"></i> Unesite obavezne parametre</button>
												<?php
												} 
												?>
											</form>
										</div>
										<?php } ?>
									</div>
									
									<!--**************************************************
											Postavke linka
									*****************************************************-->
									<div class="row">
										<div class="col-xs-8">
											<h1><i class="fa fa-tasks idk_color_green" aria-hidden="true"></i> Postavke linka </h1>
										</div>
										<div class="col-xs-12">
											<hr />
										</div>
										<div class="col-md-offset-1 col-md-11">
											<form action="<?php echo $link_form_action;?>" method="post" enctype="multipart/form-data" class="form-horizontal" role="form" id="link_forma">
											<input type="hidden" name="lg_id" value="<?php echo $lg_id ?>">
											<input type="hidden" name="poslano_sa_naloga" value="1" ?>
											<div class="form-group">
												<label for="lg_url" class="col-sm-2 control-label"><span class="text-danger">*</span> Naziv linka:</label>
												<div class="col-sm-9">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" placeholder="Link za prijavu preko Partner APP-a Nalog 999" name="lg_url" id="lg_url" value="<?php echo $lg_url; ?>" required>
														<span class="materail-input-block__line"></span>
													</div>
												</div>
											</div>
											<div class="form-group">
												<label for="lg_desc" class="col-sm-2 control-label"><span class="text-danger">*</span> Opis:</label>
												<div class="col-sm-9">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="lg_desc" id="lg_desc" value="<?php echo htmlspecialchars($lg_desc); ?>" required>
														<span class="materail-input-block__line"></span>
													</div>
												</div>
											</div>
											<div class="form-group">
												<label for="lg_nalogid" class="col-sm-2 control-label"><span class="text-danger">*</span> Nalog:</label>
												<div class="col-sm-9">
													<select class="selectpicker" id="lg_nalogid"  name="lg_nalogid" required>
														<option value="<?php echo $nalog_id;?>"><?php echo $nalog_broj."-".$nalog_naziv; ?></option>
													</select>
												</div>
											</div>										
											<div class="form-group">
												<label for="lg_desc" class="col-sm-2 control-label"><span class="text-danger">*</span> Link za:</label>
												<div class="col-sm-9">
													<select class="selectpicker" data-live-search="true" id="lg_types" name="lg_types[]" multiple required>
													<?php
														$query = $db->prepare("
																		SELECT kg_id, kg_title, kg_date
																		FROM idk_kandidati_grupe
																		WHERE kg_status = 0
																		ORDER BY kg_id DESC
																		");

														$query->execute();
														$i=1;
														while($row = $query->fetch()){

															$kg_id = $row['kg_id'];
															$kg_title = $row['kg_title'];
															$kg_date = $row['kg_date'];
															$kg_date_f = date('d.m.Y H:i', strtotime($kg_date));


														$query_selected = $db->prepare("
																		SELECT lr_groupid
																		FROM idk_link_generator_rel
																		WHERE lr_lgid = :lr_lgid
																		");

														$query_selected->execute(array(
															":lr_lgid" => $lg_id
														));

														$array = array();
														while($selectedd = $query_selected->fetch()){

															$lr_groupid = $selectedd['lr_groupid'];
															$array[] = $lr_groupid;
														}


													?>
													<option value="<?php echo $kg_id; ?>" <?php if(in_array($kg_id, $array)){echo "selected";} ?>><?php echo $kg_title; ?></option>
													<?php } ?>
													</select>
												</div>
											</div>
											<!-- <div class="form-group">
												<label for="lg_link_prijave" class="col-sm-2 control-label">Link prijave:</label>
												<div class="col-sm-9">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="lg_link_prijave" id="lg_link_prijave" value="<?php echo $lg_link_prijave; ?>" >
														<span class="materail-input-block__line"></span>
													</div>
												</div>
											</div> -->
											
											<div class="form-group">
												<label for="idk_urlimg_prijave" class="col-sm-2 control-label">Thumbnail slike naloga:</label>
												<div class="col-sm-2">
													<div class="fileinput fileinput-new" data-provides="fileinput">
														<img class="idk_logo1 img-responsive" title="Trenutna slika thumbnail-a!" style="width: 160px; height: 160px;" src="<?php getSiteUrl(); ?>files/partner_nalogs/<?php echo $idk_urlimg_prijave; ?>">
													</div>
												</div>
												<div class="col-sm-7">
													<div class="fileinput fileinput-new" data-provides="fileinput">
														<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;"></div>									
														<div>
															<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi fotografiju</span><span class="fileinput-exists">Promijeni</span><input type="file" name="idk_urlimg_prijave" id="idk_urlimg_prijave"></span>
															<input type="hidden" name="idk_urlimg_prijave_exists" value="<?php echo $idk_urlimg_prijave; ?>" id="idk_urlimg_prijave_exists">
															<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
															<script>
																$(function (){
																	$('#idk_urlimg_prijave').change(function (){

																		var ext = $('#idk_urlimg_prijave').val().split('.').pop().toLowerCase();

																		if($.inArray(ext, ['jpg', 'jpeg', 'png', '']) == -1) {
																			$('#idk_alert_ext').removeClass('hidden');
																			this.value = null;
																		}else{
																			$('#idk_alert_ext').addClass('hidden');
																		}

																		var f = this.files[0];

																		if (f.size > 20388600 || f.fileSize > 20388600){
																			$('#idk_alert_size').removeClass('hidden');
																			this.value = null;
																		}else{
																			$('#idk_alert_size').addClass('hidden');
																		}



																	})
																});
															</script>
														</div>
													</div>
												</div>	
											</div>

											<div class="form-group">
												<label class="col-sm-2"></label>
												<div class="col-sm-9">
													<div id="idk_alert_size" class="hidden">
														<div class="alert material-alert material-alert_danger">Greška: Fotografiju koju pokuštavate dodati je veća od dozvoljene veličine.</div>
													</div>
													<div id="idk_alert_ext" class="hidden">
														<div class="alert material-alert material-alert_danger">Greška: Format fotografije koju pokušavate dodati nije dozvoljen.</div>
													</div>
												</div>
											</div>		
											
											
											
											<div class="form-group">
												<label for="lg_language" class="col-sm-2 control-label"><span class="text-danger">*</span> Jezik forme za prijavu:</label>
												<div class="col-sm-9">
													<select class="selectpicker" id="lg_language" name="lg_language" required>
														<option value="1" <?php if($lg_language == "de") echo "selected"; ?> >Njemački</option>
														<option value="0" <?php if($lg_language == "bs") echo "selected"; ?> >Bosanski</option>
														<option value="3" <?php if($lg_language == "sr") echo "selected"; ?> >Srpski</option>
														<option value="2" <?php if($lg_language == "it") echo "selected"; ?> >Italijanski</option>
													</select>
												</div>
											</div>
											<br />
										</form>
											<div class="col-xs-11 text-right">
												<button form="link_forma" type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span>Spremi</span></button>
												<br /><small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>											
											</div>
										</div>
									</div>
									
									<div class="row">
										<div class="col-xs-8">
											<h1><i class="fa fa-tasks idk_color_green" aria-hidden="true"></i> Dodatne informacije </h1>
										</div>
										<div class="col-xs-12">
											<hr />
										</div>
										<div class="col-md-offset-1 col-md-8">
										</div>
										<div class="col-xs-11 text-right">
											<form action="<?php getSiteURL(); ?>do.php?form=uredi_dodatne_detalje_nalog" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
												<input type="hidden" name="nalog_id" id="nalog_id" value="<?php echo $nalog_id; ?>">
												<div class="form-group">
													<label for="nalog_partner_app_location" class="col-sm-2 control-label">Lokacija naloga:</label>
													<div class="col-sm-3">
														<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line">
															<input type="text" class="form-control materail-input" name="nalog_partner_app_location" placeholder="Berlin" id="nalog_partner_app_location"
															<?php if(isset($nalog_partner_app_location)){?> value="<?php echo $nalog_partner_app_location; ?>"> <?php } ?>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>
												<div class="form-group">
													<label for="nalog_partner_app_until" class="col-sm-2 control-label">Datum završetka naloga:</label>
													<div class="col-sm-3">
														<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line">
															<input type="text" class="form-control materail-input" name="nalog_partner_app_until" placeholder="2023-01-13" autocomplete="off" id="nalog_partner_app_until"
															<?php if(isset($nalog_partner_app_until)){?> value="<?php echo $nalog_partner_app_until; ?>"> <?php } ?>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
													<script>
														$(function() {
															initDateSelect();
														});
														function initDateSelect() {
															$("#nalog_partner_app_until").flatpickr({
																minDate: "2000-01-01"
															});
														}
													</script>
												</div>
												<div class="form-group">
													<label for="nalog_partner_app_salary" class="col-sm-2 control-label">Plata(od-do):</label>
													<div class="col-sm-3">
														<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line">
															<input type="text" class="form-control materail-input" name="nalog_partner_app_salary" placeholder="3000-4500" id="nalog_partner_app_salary"
															<?php if(isset($nalog_partner_app_salary)){?> value="<?php echo $nalog_partner_app_salary; ?>"> <?php } ?>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-sm-11 text-right">
														<button type="submit" class="btn material-btn material-btn-icon material-btn_success main-container__column"> <i class="fa fa-save" aria-hidden="true"></i>  </button>
													</div>
												</div>
											</form>
										</div>
									</div>
									
									<!--**************************************************
											Notifikacije za nalog
									*****************************************************-->	
									<div class="row">
										<div class="col-xs-8">
											<h1><i class="fa fa-tasks idk_color_green" aria-hidden="true"></i> Notifikacije za Partner APP </h1>
										</div>
										<div class="col-xs-12">
											<hr />
										</div>
										<div class="col-md-offset-1 col-md-8">
										<script type="text/javascript">
											$(document).ready(function() {
												$('#idk_pushnotifications').DataTable({

													"order": [[ 0, "desc" ]],

													 "bAutoWidth": false,

													"aoColumns": [
															{ "width": "10%" },
															{ "width": "45%" },
															{ "width": "45%" },
														]
												});
											} );
										</script>
										<table id="idk_pushnotifications" class="display" cellspacing="0" width="100%">
											<thead>
												<tr>
													<th>ID</th>
													<th class="text-center">Datum</th>
													<th class="text-center">Poslao</th>
												</tr>
											</thead>
										<tbody>
											<?php
												$query_pushnot = $db->prepare("
																SELECT pushnot_id, pushnot_datetime, pushnot_employeeid
																FROM idk_pushnotifications
																WHERE pushnot_nalogid = :pushnot_nalogid
																ORDER BY pushnot_id DESC");

												$query_pushnot->execute(array(':pushnot_nalogid' => $nalog_id));

												while($row_pushnot = $query_pushnot->fetch()){

													$pushnot_id = $row_pushnot['pushnot_id'];
													$pushnot_employeeid = $row_pushnot['pushnot_employeeid'];
													$pushnot_datetime = date('d.m.Y. H:i:s', strtotime($row_pushnot['pushnot_datetime']));
											?>
											<tr>
												<td class="text-center"><?php echo $pushnot_id; ?></td>
												<td class="text-center"><?php echo $pushnot_datetime; ?></td>
												<td class="text-center"><?php echo getZaposlenikimeR($pushnot_employeeid); ?></td>
											</tr>
											<?php } ?>
										</table>
										</div>
										<div class="col-xs-11 text-right">
											<form action="<?php getSiteURL(); ?>do.php?form=partner_notification" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
												<input type="hidden" name="nalog_id" value="<?php echo $nalog_id; ?>" />
												<button class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive">   <i class="fa fa-bell" aria-hidden="true"></i> Notifikacija partnerima</button>
											</form>
										</div>
									</div>

									<!--**************************************************
											Opis naloga za Partner APP
									*****************************************************-->
									<div class="row">
										<div class="col-xs-8">
											<h1><i class="fa fa-language idk_color_green" aria-hidden="true"></i> Opis naloga za Partner APP </h1>
										</div>
										<div class="col-xs-12">
											<hr />
										</div>
									</div>
									<style>
										.control-label{font-weight: bold!important;color: #333;}								
										.h4control-label{font-weight: bold!important;color: black;}								
										.textareacontrol-label{
										display: block;
										border: 1px solid #b6b6b6;
										-moz-box-shadow: 0 0 3px rgba(0,0,0,.15);
										-webkit-box-shadow: 0 0 3px rgba(0,0,0,.15);
										box-shadow: 0 0 3px rgba(0,0,0,.15);
										margin: 0;
										padding: 15px;
										border: 0;
										background: transparent;
										text-decoration: none;
										width: auto;
										height: auto;
										vertical-align: baseline;
										box-sizing: content-box;
										-moz-box-sizing: content-box;
										-webkit-box-sizing: content-box;
										position: static;
										-webkit-transition: none;
										-moz-transition: none;
										-ms-transition: none;
										transition: none;
										height: 200px;
										width: 100%;
										box-sizing: border-box;
									}								
									</style>
									<div class="row">
										<div class="col-md-12">
											<div class="content_box">
												<div class="row">
													<div class="col-md-offset-1 col-md-11">
													<div class="col-sm-12">
														<h4 class="h4control-label"><img src="<?php getSiteUrl(); ?>images/icon_flags/icon_flag_bh.png" width="25"> BOSANSKI JEZIK </h4>
														<br>
													</div>
													<!--************************************
															BOSANSKI
													**************************************-->
													<form action="do.php?form=dodaj_nalog_opis" method="post" role="form" class="form-horizontal">													
														<input type="hidden" value="<?php echo $nalog_id; ?>" name="no_nalogid">
														<input type="hidden" value="bs" name="no_lang">
														<div class="form-group">
															<label for="no_nalognaziv" class="col-sm-2 control-label">Nalog naziv:</label>
															<div class="col-sm-9">
																<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line">
																	<input type="text" class="form-control materail-input" name="no_nalognaziv" id="no_nalognaziv_bs"
																	<?php if(!empty($lang_bs[0]['no_nalognaziv'])){	?> value="<?php echo $lang_bs[0]['no_nalognaziv']; ?>"> <?php } ?>
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>
														<div class="form-group">

															<input type="hidden" class="form-control materail-input" name="no_nalogopis" id="no_nalogopis_bs">
															<label for="nalog_opis_bs_edit" class="col-sm-2 control-label">Nalog opis:</label>
															<div class="col-sm-9"> 
																<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line">
																	<textarea id="nalog_opis_bs_edit" name="nalog_opis_bs_edit" style="width: 400px;"></textarea>
																	<span class="materail-input-block__line"></span>
																</div>
															</div>

															<script>
															 function initializeCKEditor() {
																CKEDITOR.replace('nalog_opis_bs_edit', {
																	toolbar: [
																		{ name: 'lists', items: ['BulletedList', 'NumberedList'] },
																		{ name: 'styles', items: ['Format'] },
																	]
																});
																var htmlContent = marked.parse(`<?php echo $lang_bs[0]['no_nalogopis'] ?? ""; ?>`);
																CKEDITOR.instances.nalog_opis_bs_edit.setData(htmlContent);
															}
															$(document).ready(function () {

																if (typeof CKEDITOR === 'undefined') {
																	setTimeout(initializeCKEditor, 500);
																} else {
																	initializeCKEditor();
																}
																$("#save-button_bs").on("click", function () {
																	var htmlContent = CKEDITOR.instances.nalog_opis_bs_edit.getData();
																	var converter = new showdown.Converter();
																	var markdownContent = converter.makeMarkdown(htmlContent);
																	markdownContent = markdownContent.replaceAll(/<!--(.*?)-->/g, '');
																	document.getElementById('no_nalogopis_bs').value = markdownContent;	
																});

															});
														</script>
														</div>
														<div class="row">
															<div class="col-sm-11 text-right">
																<button type="submit" id="save-button_bs" class="btn material-btn material-btn-icon material-btn_success main-container__column"> <i class="fa fa-save" aria-hidden="true"></i>  </button>
															</div>
														</div>
													</form>
													<div class="col-sm-12">
														<h4 class="h4control-label"><img src="<?php getSiteUrl(); ?>images/icon_flags/icon_flag_de.png" width="25"> NJEMAČKI JEZIK </h4>
														<br>
													</div>													
													<!--************************************
																NJEMAČKI
													**************************************-->
													<form action="do.php?form=dodaj_nalog_opis" method="post" role="form" class="form-horizontal">													
														<input type="hidden" value="<?php echo $nalog_id; ?>" name="no_nalogid">
														<input type="hidden" value="de" name="no_lang">
														<div class="form-group">
															<label for="no_nalognaziv" class="col-sm-2 control-label">Nalog naziv:</label>
															<div class="col-sm-9">
																<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line">
																	<input type="text" class="form-control materail-input"  name="no_nalognaziv" id="no_nalognaziv_de"
																	<?php if(!empty($lang_de[0]['no_nalognaziv'])){	?> value="<?php echo $lang_de[0]['no_nalognaziv']; ?>"> <?php } ?>
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>
														<div class="form-group">
															<input type="hidden" class="form-control materail-input" name="no_nalogopis" id="no_nalogopis_de">
															<label for="nalog_opis_de_edit" class="col-sm-2 control-label">Nalog opis:</label>
															<div class="col-sm-9"> 
																<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line">
																	<textarea id="nalog_opis_de_edit" name="nalog_opis_de_edit" style="width: 400px;"></textarea>
																	<span class="materail-input-block__line"></span>
																</div>
															</div>

															<script>
															 function initializeCKEditorDe() {
																CKEDITOR.replace('nalog_opis_de_edit', {
																	toolbar: [
																		{ name: 'lists', items: ['BulletedList', 'NumberedList'] },
																		{ name: 'styles', items: ['Format'] },
																	]
																});
																var htmlContent = marked.parse(`<?php echo $lang_de[0]['no_nalogopis'] ?? ""; ?>`);

																CKEDITOR.instances.nalog_opis_de_edit.setData(htmlContent);
															}
															$(document).ready(function () {

																if (typeof CKEDITOR === 'undefined') {
																	setTimeout(initializeCKEditorDe, 500);
																} else {
																	initializeCKEditorDe();
																}
																$("#save-button_de").on("click", function () {
																	var htmlContent = CKEDITOR.instances.nalog_opis_de_edit.getData();
																	var converterDe = new showdown.Converter();
																	var markdownContent = converterDe.makeMarkdown(htmlContent);
																	markdownContent = markdownContent.replaceAll(/<!--(.*?)-->/g, '');
																	document.getElementById('no_nalogopis_de').value = markdownContent;	
																});

															});
														</script>
														</div>
														<div class="row">
															<div class="col-sm-11 text-right">
																<button type="submit" id="save-button_de" class="btn material-btn material-btn-icon material-btn_success main-container__column"> <i class="fa fa-save" aria-hidden="true"></i>  </button>
															</div>
														</div>
													</form>	
													<div class="col-sm-12">
														<h4 class="h4control-label"><img src="<?php getSiteUrl(); ?>images/icon_flags/icon_flag_uk.png" width="25"> ENGLESKI JEZIK </h4>
														<br>
													</div>													
													<!--************************************
																ENGLESKI
													**************************************-->
													<form action="do.php?form=dodaj_nalog_opis" method="post" role="form" class="form-horizontal">													
														<input type="hidden" value="<?php echo $nalog_id; ?>" name="no_nalogid">
														<input type="hidden" value="en" name="no_lang">
														<div class="form-group">
															<label for="no_nalognaziv" class="col-sm-2 control-label">Nalog naziv:</label>
															<div class="col-sm-9">
																<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line">
																	<input type="text" class="form-control materail-input"  name="no_nalognaziv" id="no_nalognaziv_en"
																	<?php if(!empty($lang_en[0]['no_nalognaziv'])){	?> value="<?php echo $lang_en[0]['no_nalognaziv']; ?>"> <?php } ?>
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>
														<div class="form-group">
															<input type="hidden" class="form-control materail-input" name="no_nalogopis" id="no_nalogopis_en">
															<label for="nalog_opis_en_edit" class="col-sm-2 control-label">Nalog opis:</label>
															<div class="col-sm-9"> 
																<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line">
																	<textarea id="nalog_opis_en_edit" name="nalog_opis_en_edit" style="width: 400px;"></textarea>
																	<span class="materail-input-block__line"></span>
																</div>
															</div>

															<script>
															 function initializeCKEditorEn() {
																CKEDITOR.replace('nalog_opis_en_edit', {
																	toolbar: [
																		{ name: 'lists', items: ['BulletedList', 'NumberedList'] },
																		{ name: 'styles', items: ['Format'] },
																	]
																});
																var htmlContent = marked.parse(`<?php echo $lang_en[0]['no_nalogopis'] ?? ""; ?>`);

																CKEDITOR.instances.nalog_opis_en_edit.setData(htmlContent);
															}
															$(document).ready(function () {

																if (typeof CKEDITOR === 'undefined') {
																	setTimeout(initializeCKEditorEn, 500);
																} else {
																	initializeCKEditorEn();
																}
																$("#save-button_en").on("click", function () {
																	var htmlContent = CKEDITOR.instances.nalog_opis_en_edit.getData();
																	var converterEn = new showdown.Converter();
																	var markdownContent = converterEn.makeMarkdown(htmlContent);
																	markdownContent = markdownContent.replaceAll(/<!--(.*?)-->/g, '');
																	document.getElementById('no_nalogopis_en').value = markdownContent;	
																});

															});
														</script>
														</div>
														<div class="row">
															<div class="col-sm-11 text-right">
																<button type="submit" id="save-button_en" class="btn material-btn material-btn-icon material-btn_success main-container__column"> <i class="fa fa-save" aria-hidden="true"></i>  </button>
															</div>
														</div>
													</form>	
													<div class="col-sm-12">
														<h4 class="h4control-label"><img src="<?php getSiteUrl(); ?>images/icon_flags/icon_flag_sr.png" width="25"> SRPSKI JEZIK </h4>
														<br>
													</div>													
													<!--************************************
																SRPSKI
													**************************************-->
													<form action="do.php?form=dodaj_nalog_opis" method="post" role="form" class="form-horizontal">													
														<input type="hidden" value="<?php echo $nalog_id; ?>" name="no_nalogid">
														<input type="hidden" value="sr" name="no_lang">
														<div class="form-group">
															<label for="no_nalognaziv" class="col-sm-2 control-label">Nalog naziv:</label>
															<div class="col-sm-9">
																<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line">
																	<input type="text" class="form-control materail-input" name="no_nalognaziv" id="no_nalognaziv_it"
																	<?php if(!empty($lang_sr[0]['no_nalognaziv'])){	?> value="<?php echo $lang_sr[0]['no_nalognaziv']; ?>"> <?php } ?>
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>
														<div class="form-group">
															<input type="hidden" class="form-control materail-input" name="no_nalogopis" id="no_nalogopis_it">
															<label for="nalog_opis_it_edit" class="col-sm-2 control-label">Nalog opis:</label>
															<div class="col-sm-9"> 
																<div class="form-group materail-input-block materail-input-block_primary materail-input_slide-line">
																	<textarea id="nalog_opis_it_edit" name="nalog_opis_it_edit" style="width: 400px;"></textarea>
																	<span class="materail-input-block__line"></span>
																</div>
															</div>

															<script>
															 function initializeCKEditorIt() {
																CKEDITOR.replace('nalog_opis_it_edit', {
																	toolbar: [
																		{ name: 'lists', items: ['BulletedList', 'NumberedList'] },
																		{ name: 'styles', items: ['Format'] },
																	]
																});
																
																var htmlContent = marked.parse(`<?php echo $lang_sr[0]['no_nalogopis'] ?? ""; ?>`);
																CKEDITOR.instances.nalog_opis_it_edit.setData(htmlContent);
															}
															$(document).ready(function () {

																if (typeof CKEDITOR === 'undefined') {
																	setTimeout(initializeCKEditorIt, 500);
																} else {
																	initializeCKEditorIt();
																}
																$("#save-button_it").on("click", function () {
																	var htmlContent = CKEDITOR.instances.nalog_opis_it_edit.getData();
																	var converterIt = new showdown.Converter();
																	var markdownContent = converterIt.makeMarkdown(htmlContent);
																	markdownContent = markdownContent.replaceAll(/<!--(.*?)-->/g, '');
																	document.getElementById('no_nalogopis_it').value = markdownContent;	
																});

															});
														</script>
														</div>
														<div class="row">
															<div class="col-sm-11 text-right">
																<button type="submit" id="save-button_it" class="btn material-btn material-btn-icon material-btn_success main-container__column"> <i class="fa fa-save" aria-hidden="true"></i>  </button>
															</div>
														</div>
													</form> 
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane fade <?php if($tab=="trazi_kandidate"){echo "active in";} ?>" id="trazi_kandidate">
									<?php if($logged_employee_id == 67 OR $logged_employee_id == 87) {	?>
									<div class="row">
										<div class="col-md-12">
											<div class="content_box">
											</div>
										</div>
									</div>
									<?php } else { ?>
										<p>IN WORK</p>
									<?php } ?>
								</div>
								<div class="tab-pane fade <?php if($tab=="kandidati_u_odlasku"){echo "active in";} ?>" id="kandidati_u_odlasku">
									<div class="row">
										<div class="col-xs-12">
											<hr />
										</div>
									</div>
									
									<div class="row">
										<div class="col-md-12">
											<div class="content_box">
												<div class="row">
													<div class="col-xs-12">
														
														<script type="text/javascript">
															$(document).ready(function() {
																$('#idk_table_kandidati_odlazak').DataTable({

																	responsive: true,

																	"order": [[ 0, "asc" ]],

																	"bAutoWidth": false,

																	"aoColumns": [
																			{ "width": "5%" },
																			{ "width": "50%" },
																			{ "width": "45%" }
																		]
																});
															} );
														</script>
														<table id="idk_table_kandidati_odlazak" class="display" cellspacing="0" width="100%">
															<thead>
																<tr>
																	<th></th>
																	<th>Status Prijave</th>
																	<th class="text-center">Broj Kandidata</th>
																</tr>
															</thead>
															<tbody class="reorder-process-list">
																<?php
																	$query_get_status_prijave = $db->prepare("
																											SELECT 
																												status_id,
																												status_naziv,
																												redoslijed_statusa
																											FROM
																												idk_kandidat_status_prijave
																											WHERE
																												redoslijed_statusa IN (4,5,6,7,8,10,11,12,13,14,15)
																											ORDER BY redoslijed_statusa ASC
																											");
																	$query_get_status_prijave->execute();
																	while($result_status_prijave = $query_get_status_prijave->fetch()){
																		$status_id 		= $result_status_prijave['status_id'];
																		$status_naziv 	= $result_status_prijave['status_naziv'];
																		$redoslijed 	= $result_status_prijave['redoslijed_statusa'];
																		if($status_id == 18){
																			$query_get_candidates = $db->prepare("SELECT 
																													kandidat_id 
																												FROM 
																													idk_kandidati 
																												WHERE 
																													kandidat_status_prijave IN (15, 18) 
																												AND 
																													kandidat_nalog_id = :nalog_id
																											");
																			$query_get_candidates->execute(array(
																				":nalog_id" => $nalog_id
																			));
																		} else {
																			$query_get_candidates = $db->prepare("SELECT 
																													kandidat_id 
																												FROM 
																													idk_kandidati 
																												WHERE 
																													kandidat_status_prijave = :status_prijave 
																												AND 
																													kandidat_nalog_id = :nalog_id
																											");
																			$query_get_candidates->execute(array(
																				":status_prijave" 	=> $status_id,
																				":nalog_id" 		=> $nalog_id
																			));
																		}	
																		$candidates_count = $query_get_candidates->rowCount();
																?>
																	<tr>
																		<td class="text-center" ></td>
																		<td><a href="<?php getSiteURL(); ?>nalozi.php?page=open_status_prijave&sid=<?php echo $status_id; ?>&nid=<?php echo $nalog_id; ?>"><?php echo $status_naziv; ?></a></td>
																<?php
																		if($candidates_count > 0){
																			echo '<td class="text-center"><span class="label label-success">'.$candidates_count.'</span></td>';
																		}else{
																			echo '<td class="text-center"><span class="label label-warning">'.$candidates_count.'</span></td>';
																		}
																?>
																	</tr>
																<?php
																	}
																?>
															</tbody>
														</table>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane fade <?php if($tab=="jobstep_pp_settings"){echo "active in";} ?>" id="jobstep_pp_settings">
									<div class="row">
										<div class="col-md-12">
											<div class="content_box">
												<?php 
													include('jobsoft_settings/group_setting.php'); 
												?>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane fade <?php if($tab=="termini"){echo "active in"; } ?>" id="termini_casting">
									<?php if((in_array( "1" , $employee_status)) OR (in_array( "2" , $employee_status))){ ?>
									<div class="row">

									</div>
									<script>
										$(document).ready(function() {
											$('#tabela_termina').DataTable({
												responsive: true,
												"bAutoWidth": false,
											});
										} );
										function getCastingInfo(){
											let pap_id = $(event.target).data("id");
											$.ajax({
												url: 'ajax.php?page=editCastingInfo',
												type: 'POST',
												data: {
													'pap_id':pap_id		
												},
												success: function(data){
													$("#castingInfoModalContent").html(data);		
												}
											});
										}
									</script>
									<div class="row">
										
										<table id="tabela_termina" >
											<thead>
												<td>Id</td>
												<td>Grad</td>
												<td>Lokacija</td>
												<td>Datum</td>
												<td>Termini</td>
												<td>Slanja poruka</td>
												<td>Uredi</td>
											</thead>
											<tbody>
												<?php
													$appointments_query=$db->prepare("SELECT pap_id, 
																							pap_date, 
																							pap_nalog_id, 
																							pap_city, 
																							pap_location_name, 
																							pap_google_maps_location, 
																							pap_first_sending_number_days, 
																							pap_second_sending_number_days, 
																							pap_first_send_enabled, 
																							pap_second_send_enabled, 
																							pap_group_id 
																						FROM 
																							idk_pp_appointments 
																						WHERE pap_nalog_id = :nalog_id");
													$appointments_query->execute(array(
														":nalog_id" => $nalog_id
													));
													$appointments_data=$appointments_query->fetchAll();

													foreach($appointments_data as $appointment){
														$pap_id								=$appointment['pap_id'];
														$pap_date							=$appointment['pap_date'];
														$pap_nalog_id						=$appointment['pap_nalog_id'];
														$pap_city							=$appointment['pap_city'];
														$pap_location_name					=$appointment['pap_location_name'];
														$pap_google_maps_location			=$appointment['pap_google_maps_location'];
														$pap_first_sending_number_days		=$appointment['pap_first_sending_number_days'];
														$pap_second_sending_number_days		=$appointment['pap_second_sending_number_days'];
														$pap_first_send_enabled				=$appointment['pap_first_send_enabled'];
														$pap_second_send_enabled			=$appointment['pap_second_send_enabled'];
														$pap_group_id						=$appointment['pap_group_id'];

														$appt_times = getAppointmentPredefinedTimes($pap_id);
												?>
														<tr>
															<td>
																<a href='<?php getSiteURL(); ?>casting_appointments?page=open&id=<?php echo $pap_id; ?>' ><?php echo $pap_id; ?> </a>
															</td>	
															<td>
																<?php echo $pap_city; ?> 
															</td>	
															<td>
																<?php echo $pap_location_name; ?> 
															</td>	
															<td>
																<?php echo $pap_date; ?> 
															</td>
															<td>
																<?php 

																foreach ($appt_times as $appt_time) {
																	echo '<span class="changeValue main-container__column material-label label material-label_info" pah_id="'.$appt_time["pah_id"].'" pah_time="'.$appt_time["pah_time"].'" data-toggle="modal" data-target="#changeAppointment">'.$appt_time["pah_time"].'</span>';
																}
																	echo '<span class="addValue main-container__column material-label label material-label_success" pap_id="'.$pap_id.'" data-toggle="modal" data-target="#addAppointment">Dodaj</span>';
																?>
															</td>		
															<td>
																<span class="main-container__column material-label label material-label_info">
																	<?php if($pap_first_send_enabled=="1"){ echo "Prvo slanje ".$pap_first_sending_number_days." dana prije ";}else{ echo "Prvo slanje ugašeno";} ?>
																</span>
																<span class="main-container__column material-label label material-label_info">
																	<?php if($pap_second_send_enabled=="1"){ echo "Drugo slanje ".$pap_second_sending_number_days." dana prije ";}else{ echo "Drugo slanje ugašeno";} ?>
																</span>
															</td>
															<td>
																<span class="idk_candidate_action_button idk_candidate_action_button_green"><i class="fa fa-outdent" onclick="getCastingInfo()" data-toggle="modal" data-target="#castingInfoModal" data-id="<?php echo $pap_id; ?>" aria-hidden="true"></i></span>
															</td>	
														</tr>
												<?php

													}


												?>
											</tbody>
										</table>
										
										<div class="modal material-modal material-modal_success fade text-left" id="castingInfoModal">
											<div class="modal-dialog ">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Detalji kastinga</h4>
													</div>
													<div class="modal-body material-modal__body">
														<div class="row">
															<div id="castingInfoModalContent" class="row castingInfoModalContent text-center">
															</div>	
														</div>
													</div>
												</div>
											</div>
										</div>

										<div class="modal material-modal material-modal_success fade text-left" id="changeAppointment">
											<div class="modal-dialog ">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Promjeni termin</h4>
													</div>
													<div class="modal-body material-modal__body text-center">
														<input type="hidden" id="termin_pah_id">

														<div class="form-group">
															<label for="kki_grupa" class="col-sm-4 control-label"><span class="text-danger">*</span> Nova vrijednost:</label>
															
															<div class="col-sm-8">
																<input class="form-control materail-input" type="text" name="nova_vrijednost" id="nova_vrijednost" placeholder="Unesite novu vrijednost">
															</div>
														</div>
													</div>
													<script>
														$("#nova_vrijednost").flatpickr({
															enableTime: true,
															timeFormat: "H:i:s",
															disableMobile: "true",
															time_24hr: true,
															noCalendar: true,
														});
													</script>
													<div class="modal-footer material-modal__footer">
														<button style="float:left;" type="submit" class="btn btn-primary material-btn material-btn_danger" id="button_obrisi_termin_potvrda">Obriši</button>
														<button class="btn material-btn material-btn" data-dismiss="modal" id="button_promjeni_termin_odustani">Odustani</button>
														<button type="submit" class="btn btn-primary material-btn material-btn_danger" id="button_promjeni_termin_potvrda">Potvrdi</button>
													</div>
												</div>
											</div>
										</div>
										<script>
											$('.changeValue').on('click', function () {
												$('#termin_pah_id').val($(this).attr('pah_id'));
												$('#nova_vrijednost').val($(this).attr('pah_time'));
											});

											$('#button_promjeni_termin_odustani').on('click', function () {
												$('#termin_pah_id').empty();
												$('#nova_vrijednost').val("");
											});

											$('#button_promjeni_termin_potvrda').on('click', function () {
												var termin_pah_id = $('#termin_pah_id').val();	
												var nova_vrijednost = $('#nova_vrijednost').val();
													$.ajax({
														url: 'ajax_data.php?page=check_candidates_for_appointment_time',
														type: 'POST',
														data: {	
															'termin_pah_id' :termin_pah_id
														},
														dataType: 'html',
														success: function(data) {
															if(data == 0){	
																if(nova_vrijednost){	
																	$.ajax({
																		url: 'ajax_data.php?page=change_appointment_time',
																		type: 'POST',
																		data: {	
																			'termin_pah_id' :termin_pah_id,
																			'nova_vrijednost' :nova_vrijednost
																		},
																		dataType: 'html',
																		success: function(text) {
																			console.log(text);
																			$("#changeAppointment").modal('hide');
																			window.location.reload();
																		},
																		error: function (xhr, ajaxOptions, thrownError) {
																			alert(xhr.status);
																			alert(thrownError);
																		}
																	});
																}else{
																	alert("Vrijeme ne može biti prazno.")
																}
															}else{
																alert("Ovaj termin ima vezanih kandidata.");

															}
														},
														error: function (xhr, ajaxOptions, thrownError) {
															alert(xhr.status);
															alert(thrownError);
														}
													});
											});

											$('#button_obrisi_termin_potvrda').on('click', function () {
												var termin_pah_id = $('#termin_pah_id').val();
												var nova_vrijednost = $('#nova_vrijednost').val();
												$.ajax({
														url: 'ajax_data.php?page=check_candidates_for_appointment_time',
														type: 'POST',
														data: {	
															'termin_pah_id' :termin_pah_id
														},
														dataType: 'html',
														success: function(data) {
															if(data == 0){	
																$.ajax({
																	url: 'ajax_data.php?page=delete_appointment_time',
																	type: 'POST',
																	data: {	
																		'termin_pah_id' :termin_pah_id
																	},
																	dataType: 'html',
																	success: function(text) {
																		console.log(text);
																		$("#changeAppointment").modal('hide');
																		window.location.reload();
																	},
																	error: function (xhr, ajaxOptions, thrownError) {
																		alert(xhr.status);
																		alert(thrownError);
																	}
																});
															}else{
																alert("Ovaj termin ima vezanih kandidata.");
															}
														},
														error: function (xhr, ajaxOptions, thrownError) {
															alert(xhr.status);
															alert(thrownError);
														}
													});
											});
										</script>
										
										<div class="modal material-modal material-modal_success fade text-left" id="addAppointment">
											<div class="modal-dialog ">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Dodaj termin</h4>
													</div>
													<div class="modal-body material-modal__body text-center">
														<input type="hidden" id="termin_pap_id">

														<div class="form-group">
															<label for="kki_grupa" class="col-sm-4 control-label"><span class="text-danger">*</span> Nova vrijednost:</label>
															
															<div class="col-sm-8">
																<input class="form-control materail-input" type="text" name="termin_dodavanje" id="termin_dodavanje" placeholder="Unesite novu vrijednost">
															</div>
														</div>
													</div>
													<script>
														$("#termin_dodavanje").flatpickr({
															enableTime: true,
															timeFormat: "H:i:s",
															disableMobile: "true",
															time_24hr: true,
															noCalendar: true,
														});
													</script>
													<div class="modal-footer material-modal__footer">
														<button class="btn material-btn material-btn" data-dismiss="modal" id="button_dodaj_termin_odustani">Odustani</button>
														<button type="submit" class="btn btn-primary material-btn material-btn_danger" id="button_dodaj_termin_potvrda">Potvrdi</button>
													</div>
												</div>
											</div>
										</div>
										<script>
											$('.addValue').on('click', function () {
												$('#termin_pap_id').val($(this).attr('pap_id'));
											});

											$('#button_dodaj_termin_odustani').on('click', function () {
												$('#termin_pap_id').empty();
												$('#termin_dodavanje').val("");
											});

											$('#button_dodaj_termin_potvrda').on('click', function () {
												var termin_pap_id = $('#termin_pap_id').val();	
												var nova_vrijednost = $('#termin_dodavanje').val();
												if(nova_vrijednost){	
													$.ajax({
														url: 'ajax_data.php?page=add_appointment_time',
														type: 'POST',
														data: {	
															'termin_pap_id' :termin_pap_id,
															'nova_vrijednost' :nova_vrijednost
														},
														dataType: 'html',
														success: function(text) {
															console.log(text);
															$("#addAppointment").modal('hide');
															window.location.reload();
														},
														error: function (xhr, ajaxOptions, thrownError) {
															alert(xhr.status);
															alert(thrownError);
														}
													});
												}else{
													alert("Vrijeme ne može biti prazno.")
												}
											});
										</script>
									</div>
									<?php } ?>
								</div>
								<div class="tab-pane fade <?php if($tab=="smjerovi_naloga"){echo "active in"; } ?>" id="smjerovi_naloga">
									<script>
										$(document).ready(function() {
											function getOrderVocationTable(){
												var order_id = $('#add_vocations').attr('order_id');
												$.ajax({
													url: 'ajax_data.php?page=getOrderVocationTable',
													type: 'POST',
													dataType: 'html',
													data: {
														'order_id' : order_id,
													},
													success: function(data) {
														$('#to_append_table').empty().append(data);
														var table = $('#vocation_table').DataTable({
															responsive: true,
															"order": [[ 0, "desc" ]],
															paging: false,
														});
														
														$('.remove_vocation').unbind().bind('click', function(){
															var vocation_id = $(this).attr('vocation_id');
															var order_id = $('#add_vocations').attr('order_id');
															var that = this;
															
															$.ajax({
																url: 'ajax_data.php?page=removeVocationFromOrder',
																type: 'POST',
																dataType: 'html',
																data: {
																	'vocation_id' : vocation_id,
																	'order_id' : order_id,
																},
																success: function(data) {
																	$(that).parent().empty().append('Obrisan');
																	console.log($(this));
																	console.log($(this).parent());
																	console.log($(this).parent().parent());
																},
																error: function (xhr, ajaxOptions, thrownError) {
																	alert(xhr.status);
																	alert(thrownError);
																}
															});
															
														});
													},
													error: function (xhr, ajaxOptions, thrownError) {
														alert(xhr.status);
														alert(thrownError);
													}
												});
											}

											$('#filter_vocation_groups').on('change', function(){
												$('#to_append_to_vocations').empty();
												var filter_vocation_groups = $('#filter_vocation_groups').val();

												$.ajax({
													url: 'ajax_data.php?page=getVocationsByVocationGroup',
													type: 'POST',
													dataType: 'html',
													data: {
														'filter_vocation_groups' : filter_vocation_groups
													},
													success: function(data) {
														$('#to_append_to_vocations').append(data);
														$('#filter_vocations').selectpicker('refresh');
													},
													error: function (xhr, ajaxOptions, thrownError) {
														alert(xhr.status);
														alert(thrownError);
													}
												});
											});
											$('#add_vocations').on('click', function(){
												var selected_vocations = $('#filter_vocations').val();
												var order_id = $(this).attr('order_id');

												$.ajax({
													url: 'ajax_data.php?page=addVocationToOrder',
													type: 'POST',
													dataType: 'html',
													data: {
														'selected_vocations' : selected_vocations,
														'order_id' : order_id,
													},
													success: function(data) {
														getOrderVocationTable();
														window.location.reload();
													},
													error: function (xhr, ajaxOptions, thrownError) {
														alert(xhr.status);
														alert(thrownError);
													}
												});
											});
											getOrderVocationTable()
										});
									</script>
									<style>
										.remove_vocation{
											background-color: #C95C48;
											color: white;
											font-size: 15px;
											padding-bottom: 2px;
											padding-top: 2px;
											padding-left: 6px;
											padding-right: 6px;
											border: 1px solid;
											border-radius: 7px;
										}
									</style>
									<div class = "row">
										<div class = "col-lg-4">
											<label for="filter_vocation_groups">Struke:</label><br>
											<select id="filter_vocation_groups" class="selectpicker" multiple data-live-search="true" data-actions-box="true">
												<?php
													$query_get_vocation_groups = $db -> prepare('
														SELECT ss.id_struke, ss.naziv_struke
														FROM idk_struke ss
													');
													$query_get_vocation_groups -> execute();
					
													while($row_get_vocation_groups = $query_get_vocation_groups -> fetch()){
														echo '<option value = "'.$row_get_vocation_groups["id_struke"].'">'.$row_get_vocation_groups["naziv_struke"].'</option>';
													}
												?>
												<option value = "0">Bez struke</option>
											</select> 
											<br><br>
											<label id="lable_vocations" for="filter_vocations">Smjerovi:</label><br>
											<div id="to_append_to_vocations"></div>
											<br><br><br><br>
											<button id="add_vocations" order_id = "<?php echo $nalog_id;?>" style="width:30%" class="btn btn-success">Prebaci</button>
										</div>
										<div class = "col-lg-8">
											<?php 
												if (getNalogSmjeroviCntR($nalog_id) != 0) {
													?>
														<div class="row">
															<div class="col-xs-12">
																<div class="row">
																	<div class="col-xs-4 text-right" style="padding-top: 10px;padding-bottom: 10px;">
																		Da li su unešeni smjerovi kriterij naloga? 
																	</div>
																	<div class="col-xs-8" style="padding-top: 3px; padding-bottom: 3px;">
																		<!-- <nalog-smjerovi-kriterij 
																			nalogId="<?php echo $nalog_id; ?>" 
																			nalogVr="<?php  echo $nalog_smjerovi_kriterij; ?>"
																		>	
																		</nalog-smjerovi-kriterij> -->
																		<div class="materail-input-block materail-input-block_success idk_radio_buttons">
																			<label class="main-container__column material-radio-group material-radio-group_success" for="nalog_smjerovi_da_confirmation">
																				<input type="radio" name="nalog_smjerovi_confirmation" id="nalog_smjerovi_da_confirmation" class="material-radiobox" value="1" <?php if(getNalogSmjeroviKriterij($nalog_id) != 0){echo "checked";}?> />
																				<span class="material-radio-group__element material-radio-group__check-radio"></span>
																				<span class="material-radio-group__element material-radio-group__caption">DA</span>
																			</label>
																		</div>
																		<div class="materail-input-block materail-input-block_danger idk_radio_buttons">
																			<label class="main-container__column material-radio-group material-radio-group_danger" for="nalog_smjerovi_ne_confirmation">
																				<input type="radio" name="nalog_smjerovi_confirmation" id="nalog_smjerovi_ne_confirmation" class="material-radiobox" value="0" <?php if(getNalogSmjeroviKriterij($nalog_id) == 0){echo "checked";}?> />
																				<span class="material-radio-group__element material-radio-group__check-radio"></span>
																				<span class="material-radio-group__element material-radio-group__caption">NE</span>
																			</label>
																		</div>
																		<script>
																			$('#nalog_smjerovi_da_confirmation').click(function(){
																				$.ajax({
																					type: "POST",
																					url: 'do.php?form=nalogSmjeroviKriterijEdit',
																					data: {
																						nalogId: "<?php echo $nalog_id; ?>",
																						nalogVr: 1
																					},
																				}); 
																			});
																			$('#nalog_smjerovi_ne_confirmation').click(function(){
																				$.ajax({
																					type: "POST",
																					url: 'do.php?form=nalogSmjeroviKriterijEdit',
																					data: {
																						nalogId: "<?php echo $nalog_id; ?>",
																						nalogVr: 0
																					},
																				}); 
																			});
																		</script>
																	</div>
																</div>
															</div>
														</div>
														<hr>
													<?php 
												}
											?>
											<div class="row">
												<div class="col-xs-12">
													<div id = "to_append_table"></div>
												</div>
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
				
				case "open_status_prijave":
					$status_id 	= $_GET['sid'];
					$nalog_id 	= $_GET['nid'];
					
					
					$get_status_name = $db->prepare("SELECT status_naziv FROM idk_kandidat_status_prijave WHERE status_id = :status_id");
					$get_status_name->execute(array( ":status_id" => $status_id ));
					$result_status_name = $get_status_name->fetch();
					$status_name = $result_status_name['status_naziv'];
					$get_nalog_name = $db->prepare("SELECT nalog_naziv FROM idk_nalozi WHERE nalog_id = :nalog_id");
					$get_nalog_name->execute(array( ":nalog_id" => $nalog_id ));
					$result_nalog_name = $get_nalog_name->fetch();
					$nalog_name = $result_nalog_name['nalog_naziv'];
				?>
					<div class="row">
						<div class="col-xs-8">
								<h1><?php echo $status_name; ?> | <i class="fa fa-file-o idk_color_green" aria-hidden="true"></i> <?php echo $nalog_name; ?></h1>
							</div>
						<div class="col-xs-4 text-right idk_margin_top10">
							<a href="<?php getSiteURL(); ?>nalozi?page=open&id=<?php echo $nalog_id; ?>" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
						</div>
						<div class="col-xs-12">
							<hr />
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="content_box">
								<div class="row">
									<div class="col-xs-12" id="table_wrapper">
										<script type="text/javascript">
											function checkDipl(){
												let kandidat_id = $(event.target).data("kandidat_id");
												$.ajax({
													url: 'ajax.php?page=provjeriDiplomu',
													type: 'POST',
													data: {
														'posao_id':kandidat_id		
													},
													success: function(data){
														$("#provjeraDiplomeContent").html(data);
														$("#fullRecognition").selectpicker('refresh');		
													}
												});
											}
											$(document).ready(function() {
												
												let nalog_id = <?php echo $nalog_id; ?>;
												let status_id = <?php echo $status_id; ?>;
												$.ajax({
													url: 'kandidati_odlazak_API.php',
													type: 'POST',
													data: {	
														'nalog_id'  :nalog_id,
														'status_id' :status_id
													},
													dataType: 'html',
													success: function(data) {
														$("#table_wrapper").html(data);
														$('#idk_table_status_prijave').DataTable({
															responsive: true,
															"order": [[ 0, "desc" ]]
														});
														$(".lista").click(function(){
															let kandidat_id = $(this).data("kandidat_id");
															let employee_id = "<?php echo $logged_employee_id; ?>";
															$("#lista_wrapper").html("<document-checklist kandidat_id=" + kandidat_id + " employee_id=" + employee_id + "></document-checklist>");
														});
														$(".lista_dopuna").click(function(){
															let kandidat_id = $(this).data("kandidat_id");
															let dopuna_id = $(this).data("dopuna_id");
															let employee_id = "<?php echo $logged_employee_id; ?>";
															$("#lista_wrapper_dopuna").html("<document-checklist kandidat_id=" + kandidat_id + " employee_id=" + employee_id + " dopuna=" + dopuna_id + "></document-checklist>");
														});
														$(".lista_odbijenica").click(function(){
															let kandidat_id = $(this).data("kandidat_id");
															let odbijenica_id = $(this).data("odbijenica_id");
															let employee_id = "<?php echo $logged_employee_id; ?>";
															$("#lista_wrapper_odbijenica").html("<document-checklist kandidat_id=" + kandidat_id + " employee_id=" + employee_id + " dopuna=" + odbijenica_id + "></document-checklist>");
														});
														

													},
													
													error: function (xhr, ajaxOptions, thrownError) {
														alert(xhr.status);
														alert(thrownError);
													}
												});
												
											});
											function uploadHandle(){
												$(".upload").click(function(){
													var kandidat_id = $(this).data("kandidat_id");
													var ppa_partner_id = $(this).data("ppa_partner_id");
													var pp_pozicija = $(this).data("pp_pozicija");
													var pp_lokacija = $(this).data("pp_lokacija");
													var pp_plata = $(this).data("pp_plata");
                                                    $("#kandidat_id").val(kandidat_id);
                                                    $("#ppa_partner_id").val(ppa_partner_id);
													$("#pp_pozicija").val(pp_pozicija);
													$("#pp_lokacija").val(pp_lokacija);
													$("#pp_plata").val(pp_plata);

													$("#date_receiving").flatpickr({
														dateFormat: "d.m.Y"
													});
														
													$.ajax({
														url: 'ajax_data.php?page=check_contract_sent',
														type: 'POST',
														data: {	
															'kandidat_id': kandidat_id
														},

														dataType: 'html',

														success: function (text) {

															if (text == "true") {
																
																$("#date_receiving_wrap").show();
																$("#date_receiving").required = true;

															} else if (text == "false") {
																
																$("#date_receiving").required = false;
																$("#date_receiving_wrap").hide();
																
															}

														},
														
														error: function (xhr, ajaxOptions, thrownError) {

															alert(xhr.status);
															alert(thrownError);

														}
													});

											    });
                                            }

                                            function odustaoHandle(){
												$(".odustao").click(function(){
													let kandidat_id = $(this).data("kandidat_id");
													$("#kandidat_id_odustao").val(kandidat_id);
												});
											}

											function poslanUgovorPostom(){
												$(".unesiDetaljePoste").click(function(){
													var kandidat_id = $(this).data("kandidat_id");
													var nalog_id = <?php echo $_GET['nid'];  ?>;
													$("#candidate_id").val(kandidat_id);
													$("#id_nalog").val(nalog_id);
													getData();

												});	
												
										}
										</script>
									</div>
								</div>
							</div>
						</div>
					</div>
					<style>
						.scrollBarHorizontal::-webkit-scrollbar {
							height:5px;
							margin-top: 10px;
						}
						.scrollBarHorizontal::-webkit-scrollbar-thumb {
							background: #1D84C0;
							border-radius: 5px;
						}
						.scrollBarHorizontal::-webkit-scrollbar-track {
							background-color: #e9ecef;
							border-radius: 5px;
						}
					</style>
					<!-- LISTA DOKUMENATA -->
					<div class="modal material-modal material-modal_success fade" id="modal_lista_dokumenata">
						<div class="modal-dialog modal-lg">
							<div class="modal-content material-modal__content">
								<div class="modal-header material-modal__header">
									<button class="close material-modal__close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title material-modal__title">
										<i class="fa fa-plus" aria-hidden="true" style = "margin-right: 10px;">
										</i>
										Lista dokumenata
									</h4>
								</div>
								<div class="modal-body material-modal__body">
									<div class = "row">
										<div class="col-lg-8 col-lg-offset-2 scrollBarHorizontal" id="lista_wrapper" style="overflow-x: scroll;">
										</div>
									</div>
								</div>
								<div class="modal-footer material-modal__footer" style = "text-align: center;">
									<button class="btn material-btn material-btn" data-dismiss="modal">
										Zatvori
									</button>
								</div>
							</div>
						</div>
					</div>
					

					<!-- Modal provjera diplome -->
					<div class="modal material-modal material-modal_success fade text-left" id="provjeri_dipl_kand">
						<div class="modal-dialog modal-lg">
							<div class="modal-content material-modal__content">
								<div class="modal-header material-modal__header">
									<button class="close material-modal__close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title material-modal__title">Unos nostrifikacije diplome</h4>
								</div>
								<div class="modal-body material-modal__body">
									<div class="row">
										<div id="provjeraDiplomeContent" class="row provjeraDiplomeContent text-center">
										</div>	
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- LISTA DOKUMENATA ZA DOPUNU-->
					<div class="modal material-modal material-modal_success fade" id="modal_lista_dokumenata_dopuna">
						<div class="modal-dialog modal-lg">
							<div class="modal-content material-modal__content">
								<div class="modal-header material-modal__header">
									<button class="close material-modal__close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title material-modal__title">
										<i class="fa fa-plus" aria-hidden="true" style = "margin-right: 10px;">
										</i>
										Lista dokumenata za dopunu
									</h4>
								</div>
								<div class="modal-body material-modal__body">
									<div class = "row">
										<div class="col-lg-8 col-lg-offset-2 scrollBarHorizontal" id="lista_wrapper_dopuna" style="overflow-x: scroll;">
										</div>
									</div>
								</div>
								<div class="modal-footer material-modal__footer" style = "text-align: center;">
									<button class="btn material-btn material-btn" data-dismiss="modal">
										Zatvori
									</button>
								</div>
							</div>
						</div>
					</div>
					<!-- LISTA DOKUMENATA ZA ODBIJENICU-->
					<div class="modal material-modal material-modal_success fade" id="modal_lista_dokumenata_odbijenica">
						<div class="modal-dialog modal-lg">
							<div class="modal-content material-modal__content">
								<div class="modal-header material-modal__header">
									<button class="close material-modal__close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title material-modal__title">
										<i class="fa fa-plus" aria-hidden="true" style = "margin-right: 10px;">
										</i>
										Lista dokumenata za odbijenicu
									</h4>
								</div>
								<div class="modal-body material-modal__body">
									<div class = "row">
										<div class="col-lg-8 col-lg-offset-2 scrollBarHorizontal" id="lista_wrapper_odbijenica" style="overflow-x: scroll;">
										</div>
									</div>
								</div>
								<div class="modal-footer material-modal__footer" style = "text-align: center;">
									<button class="btn material-btn material-btn" data-dismiss="modal">
										Zatvori
									</button>
								</div>
							</div>
						</div>
					</div>
					<!-- MODAL ZA UPLOAD UGOVORA -->
					<div class="modal material-modal material-modal_success fade" id="modal_kandidat_ugovor">
						<div class="modal-dialog modal-lg">
							<div class="modal-content material-modal__content">
								<div class="modal-header material-modal__header">
									<button class="close material-modal__close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title material-modal__title">
										<i class="fa fa-plus" aria-hidden="true" style = "margin-right: 10px;">
										</i>
										Upload ugovora
									</h4>
								</div>
								<div class="modal-body material-modal__body">
									<div class = "row">
										<div class="col-md-8 col-md-offset-2">
											<div class="form-horizontal" id="form_kandidat_ugovor">
												<input type="hidden" name="status_id" id="status_id" value="<?php echo $status_id; ?>">
												<input type="hidden" name="nalog_id" id="nalog_id" value="<?php echo $nalog_id; ?>">
												<input type="hidden" name="kandidat_id" id="kandidat_id">
												<input type="hidden" name="ppa_partner_id" id="ppa_partner_id">
												<input type="hidden" name="pp_pozicija" id="pp_pozicija">
												<input type="hidden" name="pp_lokacija" id="pp_lokacija">
												<input type="hidden" name="pp_plata" id="pp_plata">
												<label class="col-md-4 control-label" for="dokument_select">Vrsta dokumenta:</label>
												<div class="col-md-8">
													<select class="selectpicker" id="dokument_select" name="dokument_select" data-live-search="true" title="Odaberi...">
														<option value="1">Ugovor</option>
													</select>
												</div>
												<div id="dokument_select_alert_partner" class="row hidden" >
													<div class="col-md-offset-2 col-sm-8" style="margin-top: 20px !important;">
														<div class="alert material-alert material-alert_danger">Greška: Nije moguće izvršiti upload ugovora jer kandidat nije dodijeljen partneru preko aplikacije za pristup poslodavcima.</div>
													</div>
												</div>
												<div id="dokument_select_alert_contract_info" class="row hidden" >
													<div class="col-md-offset-2 col-sm-8" style="margin-top: 20px !important;">
														<div class="alert material-alert material-alert_danger">Greška: Nije moguće izvršiti upload ugovora jer kandidat nema unešene informacije o ugovoru (pozicija, plata, lokacija) preko aplikacije za pristup poslodavcima.</div>
													</div>
												</div>
												<div id="dokument_alert_size" class="row hidden" >
													<div class="col-md-offset-2 col-sm-8" style="margin-top: 20px !important;">
														<div class="alert material-alert material-alert_danger">Greška: Dokument koji pokuštavate dodati je veći od dozvoljene veličine.</div>
													</div>
												</div>
												<div id="dokument_alert_ext" class="row hidden" >
													<div class="col-md-offset-2 col-sm-8" style="margin-top: 20px !important;">
														<div class="alert material-alert material-alert_danger">Greška: Format dokumenta kojeg pokušavate dodati nije dozvoljen.</div>
													</div>
												</div>
												<div id="dokument_alert_len" class="row hidden">
													<div class="col-md-offset-2 col-sm-8" style="margin-top: 20px !important;">
														<div class="alert material-alert material-alert_danger">Greška: Dozvoljeno je dodati maximalno 1 dokument.</div>
													</div>
												</div>
												<div class="form-group" id="dokumentDiv" style="display: none;">
													<div class="col-md-offset-2 col-sm-8 text-center" style="margin-top: 20px;">
														<div class="fileinput fileinput-new" data-provides="fileinput">
															<span class="btn btn-default btn-file">
																<span class="fileinput-new"> 
																	Izaberi dokument
																</span>
																<span class="fileinput-exists">
																	Promijeni
																</span>
																<input type="file" name="kandidat_dokument" id="kandidat_dokument">
															</span>
															<br>
															<span style="padding-top: 10px; padding-left: 10px; padding-right: 10px; word-break: break-all;" id = "files-name">
															</span>
														<script>
															$("#dokument_select").change(function(){
																if("#dokument_select option:selected"){
																	$("#dokumentDiv").css("display", "");
																} else {
																	$("#dokumentDiv").css("display", "none");
																}

																if ($("#dokument_select").val() == 1 ) {
																	var ppa_partner_id = $("#ppa_partner_id").val(); dokument_select_alert_contract_info
																	var status_id = $('#status_id','#modal_kandidat_ugovor').val();
																	var pp_pozicija = $('#pp_pozicija','#modal_kandidat_ugovor').val();
																	var pp_lokacija = $('#pp_lokacija','#modal_kandidat_ugovor').val();
																	var pp_plata = $('#pp_plata','#modal_kandidat_ugovor').val();
																	if ( ppa_partner_id != "" ) {
																		$('#dokument_select_alert_partner').addClass('hidden');
																		// $("#kandidat_dokument").attr("disabled", false);
																		// if (status_id == 7) {
																		// 	if (pp_pozicija != "" && pp_lokacija != "" && pp_plata != "") {
																		// 		$('#dokument_select_alert_contract_info','#modal_kandidat_ugovor').addClass('hidden');
																		// 		$("#kandidat_dokument").attr("disabled", false);
																		// 	} else {
																		// 		$('#dokument_select_alert_contract_info','#modal_kandidat_ugovor').removeClass('hidden');
																		// 		$("#kandidat_dokument").attr("disabled", true);
																		// 	}
																		// }
																	}else {
																		$('#dokument_select_alert_partner').removeClass('hidden');
																		$("#kandidat_dokument").attr("disabled", true);
																	}
																} else {
																	$('#dokument_select_alert_partner').addClass('hidden');
																	$("#kandidat_dokument").attr("disabled", false);
																}
															});
															$("#kandidat_dokument").change(function(){
																$("#upload_btn").attr("disabled", false);
																var file = $("#kandidat_dokument")[0].files[0].name;
																$("#files-name").text(file);

																if(this.files[0].size > 20388608){
																	$('#dokument_alert_size').removeClass('hidden');
																	setTimeout(function(){
																		$('#dokument_alert_size').addClass('hidden');
																	}, 5000);
																	$("#kandidat_dokument").val(null);
																	$("#files-name").text("");
																	$("#upload_btn").attr("disabled", true);
																	return;
																}
																if(parseInt(this.files.lenght) > 1){
																	$('#dokument_alert_len').removeClass('hidden');
																	setTimeout(function(){
																		$('#dokument_alert_len').addClass('hidden');
																	}, 5000);
																	$("#kandidat_dokument").val(null);
																	$("#files-name").text("");
																	$("#upload_btn").attr("disabled", true);
																	return;
																}
																var ext = $("#kandidat_dokument")[0].files[0].name.split('.').pop().toLowerCase();
																if($.inArray(ext, ['jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png']) === -1){
																	$('#dokument_alert_ext').removeClass('hidden');
																	setTimeout(function(){
																		$('#dokument_alert_ext').addClass('hidden');
																	}, 5000);
																	$("#kandidat_dokument").val(null);
																	$("#files-name").text("");
																	$("#upload_btn").attr("disabled", true);
																	return;
																}
															});
														</script>
														</div>
													</div>
												</div>
												<!--
												<div class="form-group" id="glossa"> 
													<label for="glossa_kurs" class="col-sm-7 control-label" style="margin-top: 10px !important;">
														Da li će kandidat pohađati glossin kurs:
													</label>
													<div class="col-sm-4">
														<div class="main-container__column materail-switch materail-switch_primary" style="margin-top: 10px !important;">
															<input class="materail-switch__element" type="checkbox" id="glossa_kurs" name="glossa_kurs" value="DA" >
															<label class="materail-switch__label" for="glossa_kurs"></label>
														</div>
													</div>
												</div>	
												<script>
													$(document).ready(function(){
														let status_id = "<?php echo $status_id; ?>";
														if( status_id != 7){
															$("#glossa").css("display", "none");
														}

														fetch("/ajax_data.php?page=check_nalog_glossa&nalog_id=<?php echo $nalog_id; ?>")
														.then(response => response.text())
														.then(data => {
															if(data == 1){
																$("#glossa_kurs").prop("checked", true);
															}
														});	
													});
												</script>
												-->
                                                <div id="date_receiving_wrap" style="display: none;">
													<br><br><br>

													<label class="col-md-4 control-label" for="dokument_select">Datum zaprimanja:</label>
													
													<div class="col-md-8">
														<input class="form-control materail-input flatpickr-input" type="text" name="date_receiving" autocomplete="off" id="date_receiving">
																
														
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="modal-footer material-modal__footer" style = "text-align: center;">
									<button class="btn material-btn material-btn" data-dismiss="modal">
										Odustani
									</button>
									<button class="btn btn-primary material-btn material-btn_success" id="upload_btn" data-dismiss="modal" disabled>
										<i class="fa fa-check-square-o" aria-hidden="true">
										</i>
										Završi
									</button>
								</div>
							</div>
						</div>
					</div>
					<script>
						$("#modal_kandidat_ugovor").on('hide.bs.modal', function(e) {
							$("#kandidat_dokument").val(null);
							$("#upload_btn").attr("disabled", true);
							$("#files-name").text("");
							$("#dokumentDiv").css("display", "none");
							$("#dokument_select", '#modal_kandidat_ugovor').val(null).selectpicker('refresh');
							$('#dokument_select_alert_partner').addClass('hidden');
							$('#kandidat_status_prijave','#modal_kandidat_ugovor').removeAttr('value');
							$('#pp_pozicija','#modal_kandidat_ugovor').removeAttr('value');
							$('#pp_lokacija','#modal_kandidat_ugovor').removeAttr('value');
							$('#pp_plata','#modal_kandidat_ugovor').removeAttr('value');
							$('#dokument_select_alert_contract_info','#modal_kandidat_ugovor').addClass('hidden');
						});
						
						$("#upload_btn").on("click", function(e){

							// e.preventDefault();
							let kandidat_id = $("#kandidat_id").val();
							let nalog_id = $("#nalog_id").val();
							let status_id = $("#status_id").val();
							let document_type = $("#dokument_select").val();
							let dokument = $("#kandidat_dokument")[0].files[0];
							let fname = "";
							let lname = "";
							let email = "";
							let phone = "";
							let crm_id = "";

							let check = 0;

							let glossa_slanje = 0;
							
							/*
							if($("#glossa_kurs").is(":checked")){
								glossa_slanje = 3;
							}
							*/

							let form = new FormData();
							form.append("kandidat_id", kandidat_id);
							form.append("nalog_id", nalog_id);
							form.append("status_id", status_id);
							form.append("dokument_select", document_type);
							form.append("kandidat_dokument", dokument);
							form.append("glossa_slanje", glossa_slanje);

							if(status_id == 8){
								/* GASI SE ZA GLOSSU */
								/*
								fetch(`/ajax_data.php?page=glossa_slanje_info&kandidat_id=${kandidat_id}`)
								.then((res) => res.text())
								.then((data) => {
									if(data == 3){
										check = 1;
										fetch(`ajax_data.php?page=glossa_api_info&id=${kandidat_id}`)
										.then((res) => res.json())
										.then((data) => {
											fname = data.fname;
											lname = data.lname;
											email = data.email;
											phone = data.phone;
											crm_id = data.crm_id;
											
											if(phone === null){
												phone = "1111111111";
											}

											var myHeaders = new Headers();
											myHeaders.append("Authorization", "Bearer 8d81150a-8cd6-4a8a-9279-d4a4a45ccdea");
											myHeaders.append("Content-Type", "application/json");
											myHeaders.append("Cookie", "PH_HPXY_CHECK=s1");

											var raw = JSON.stringify({
												"name": fname,
												"lastname": lname,
												"email": email,
												"phone": phone,
												"crm_id": crm_id
											});

											var requestOptions = {
											method: 'POST',
											headers: myHeaders,
											body: raw,
											redirect: 'follow',
											};

											fetch("https://glossa-crm.com/api/Person/Create", requestOptions)
											.then(response => response.json())
											.then((result) => {
												let response_msg = result.message;
												let response_status = result.code;
												let glossaForm = new FormData();
												glossaForm.append("kandidat_id", kandidat_id);
												glossaForm.append("response_msg", response_msg);
												glossaForm.append("response_code", response_status);
												glossaForm.append("glossa_kurs", 1);
												fetch("do.php?form=insert_glossa_lead", {
													method: "POST",
													body: glossaForm
												})
												.then(response => response.text())
												.then((data) => {
													fetch("do.php?form=upload_document", {
														method: "POST",
														body: form 
													})
													.then(result => result.text())
													.then(() => {
														location.reload();
													})
												})
											})
											.catch(error => console.log('error', error));
										});
									} else {
										fetch("do.php?form=upload_document", {
											method: "POST",
											body: form 
										})
										.then(result => result.text())
										.then(() => {
											location.reload();
										})
									}
								});
								*/
								fetch("do.php?form=upload_document", {
									method: "POST",
									body: form 
								})
								.then(result => result.text())
								.then(() => {
									location.reload();
								})
							} else {
								fetch("do.php?form=upload_document", {
									method: "POST",
									body: form 
								})
								.then(result => result.text())
								.then(() => {
									location.reload();
								})
							}
						});
					</script>
                    <!-- MODAL ZA UNOS DETALJE SLANJA POSTE -->
					<div class="modal material-modal material-modal_success fade" id="modal_poslan_ugovor_postom">
						<div class="modal-dialog modal-lg">
							<form action="do.php?form=insert_contract_sent" method="post">
								<div class="modal-content material-modal__content">
									<div class="modal-header material-modal__header">
										<button class="close material-modal__close" data-dismiss="modal">&times;</button>
										<h4 class="modal-title material-modal__title">
											<i class="fa fa-envelope-o" aria-hidden="true" style = "margin-right: 10px;">
											</i>
											Detalji slanja pošte
										</h4>
									</div>
									<div class="modal-body material-modal__body">
										<div class = "row">
											<input type="hidden" name="cs_id" id="cs_id">
											<input type="hidden" name="candidate_id" id="candidate_id" >
											<input type="hidden" name="id_nalog" id="id_nalog" >
											<div class="form-group col-sm-10 col-md-offset-1 text-right unos_sent_date" >
												<label for="unos_sent_date" class="col-sm-3 control-label">Datum slanja:</label>
												<div class="col-sm-7">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" data-sent_date="send_date"  name="send_date" autocomplete="off" id="send_date" required >
														<span class="materail-input-block__line"></span>
													</div>
												</div>
												<script>
													$(function() {
														initDateSelect();
														
													});

													function initDateSelect() {
														$("#send_date").flatpickr({
															minDate: "2000-01-01",
														});
														$('#send_date').on('focus', ({ currentTarget }) => $(currentTarget).blur());
														$("#send_date").prop('readonly', false);
														
													}
													function getData()
													{
														
														let kandidat_id = $("#candidate_id").val();
														let nalog_id = $("#id_nalog").val();
														fetch(`ajax_data.php?page=get_contract_sent_details&cid=${kandidat_id}&nid=${nalog_id}`).then(res => res.json()).then((data) => {
															$("#cs_id").val(data.id_cs);
															$("#tracking_code").val(data.tracking_code);
															$("#link_tracking_code").val(data.tracking_link);
															$("#send_date").val(data.date);
															if(data.id_cs){
																$('#tracking_code').attr('disabled','disabled');
																$('#link_tracking_code').attr('disabled','disabled');
																$('#spremiDetaljePoste').attr('disabled','disabled');
																$('#send_date').attr('disabled','disabled');
																
															}else{
																$('#tracking_code').removeAttr('disabled');
																$('#link_tracking_code').removeAttr('disabled');
																$('#spremiDetaljePoste').removeAttr('disabled');
																$('#send_date').removeAttr('disabled');

															}
														})
													}
												</script>
											</div>	
												
											<div class="form-group col-md-offset-1 col-md-10">
												<label class="col-sm-3 control-label text-right">Tracking code:</label>
												<div class="col-sm-7">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="tracking_code" id="tracking_code" value="" required>
														<span class="materail-input-block__line"></span>
													</div>
												</div>
											</div>	

											<div class="form-group col-md-offset-1 col-md-10">
												<label class="col-sm-3 control-label text-right">Link za povjeru:</label>
												<div class="col-sm-7">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="link_tracking_code" id="link_tracking_code" value="" required>
														<span class="materail-input-block__line"></span>
													</div>
												</div>
											</div>		
										</div>
									</div>
									<div class="modal-footer material-modal__footer" style = "text-align: center;">
										<button id="spremiDetaljePoste" class="btn btn-primary material-btn material-btn_success" type="submit">
											Spremi
										</button>
										<button class="btn material-btn material-btn" data-dismiss="modal">
											Zatvori
										</button>
									</div>
								</div>
							</form>
						</div>
					</div>
					<!-- MODAL ZA ODUSTAJANJE KANDIDATA -->
					<div class="modal material-modal material-modal_success fade" id="modal_kandidat_odustao">
						<div class="modal-dialog modal-lg">
							<div class="modal-content material-modal__content">
								<div class="modal-header material-modal__header">
									<button class="close material-modal__close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title material-modal__title">
										<i class="fa fa-plus" aria-hidden="true" style = "margin-right: 10px;">
										</i>
										Odaberi razlog odustajanja kandidata
									</h4>
								</div>
								<div class="modal-body material-modal__body">
									<div class = "row">
										<div class="col-md-8 col-md-offset-2">
											<form action="<?php getSiteURL(); ?>nalozi?page=insert_reject_reason" method="post" http-equiv="Content-type" enctype="multipart/form-data"; charset = "utf-8" class="form-horizontal" id="form_kandidat_odustao">
											<input type="hidden" name="nalog_id" id="nalog_id" value="<?php echo $nalog_id; ?>">
											<input type="hidden" name="status_id" id="status_id" value="<?php echo $status_id; ?>">
											<input type="hidden" name="kandidat_id" id="kandidat_id_odustao"> 
												<label for="razlog_odustajanja" class="col-md-3 control-label"><strong>Razlozi</strong>:</label>
												<div class="col-md-9" id="select">
													<select class="selectpicker" id="razlog_odustajanja" name="razlog_odustajanja" data-live-search="true" title="Odaberi...">
													<?php
														$query_get_reasons = $db->prepare("SELECT rr_id, rr_name_bs FROM idk_reject_reasons WHERE rr_id != 1 AND rr_rejected_by = 1");
														$query_get_reasons->execute();
														while($result = $query_get_reasons->fetch()){
															$rr_id 		= $result['rr_id'];
															$rr_name_bs = $result['rr_name_bs'];
													?>
															<option value="<?php echo $rr_id; ?>"><?php echo $rr_name_bs; ?></option>
													<?php
														}
													?>
													</select>
												</div>
												<label for="razlog_opis" class="col-md-3 control-label"><strong>Opis</strong>:</label>
												<div class="col-md-9" id="opis">
													<textarea class="form-control materail-input material-textarea" name="razlog_opis" id="razlog_opis"></textarea>
												</div>
											</form>
										</div>
									</div>
								</div>
								<div class="modal-footer material-modal__footer" style = "text-align: center;">
									<button class="btn material-btn material-btn" data-dismiss="modal">
										Odustani
									</button>
									<button type="submit" class="btn btn-primary material-btn material-btn_success" form="form_kandidat_odustao">
										<i class="fa fa-check-square-o" aria-hidden="true">
										</i>
										Završi
									</button>
								</div>
							</div>
						</div>
					</div>
					<script>
						$("#razlog_odustajanja").on("change", function(){
							var rr_id = $(this).val();
							if(rr_id == 1){
								$("#opis").after('<div class="col-md-3" id="alert"></div><div class="col-md-9"><p class="text-danger">Odabirom "Ostalo" obavezno je ispuniti polje "Opis"!!!</p></div>');
								$("#razlog_opis").prop("required",true);
							}else{
								$("#alert").remove();
								$("#razlog_opis").removeProp("required");
							}
						});
					</script>
					<!-- MODAL UPLOAD DOKUMENTI RADNO ISKUSTVO START -->
						
						<script>
							/* 
								WE - work experience
							*/
							function setTitleWe(doc_name) {
								$('strong', '.modal-title', '#add_doc_we').text('Upload dokumenta '+ doc_name);
							};
							function setInitialValuesWe(thisRow) {
								let candidate_id = $(thisRow).data("candidate_id");
								let doc_name = $(thisRow).data("doc_name");
								let doc_type = $(thisRow).data("doc_type");

								$('#document_dataid', '#form_add_doc_we', '#add_doc_we').val(candidate_id);
								$('#document_name', '#form_add_doc_we', '#add_doc_we').val(doc_name);
							};
							function addDocWE(thisRow) {
								let candidate_id = $(thisRow).data("candidate_id");
								let doc_name = $(thisRow).data("doc_name");
								let doc_type = $(thisRow).data("doc_type");
								setTitleWe(doc_name);
								setInitialValuesWe(thisRow);
								$('#add_doc_we').modal('show');
							};
							function resetFormWe() {
								$('#document_dataid', '#form_add_doc_we', '#add_doc_we').val(null);
								$('#document_name', '#form_add_doc_we', '#add_doc_we').val(null);
								$('.close.fileinput-exists', '#form_add_doc_we', '#add_doc_we').trigger('click');
							}; 
						</script>
						<div class="modal material-modal material-modal_success fade text-left" id="add_doc_we">
							<div class="modal-dialog modal-lg">
								<div class="modal-content material-modal__content">
									<div class="modal-header material-modal__header">
										<button class="close material-modal__close" data-dismiss="modal">&times;</button>
										<h4 class="modal-title material-modal__title">
											<i style = "margin-right: 10px;" class="fa fa-info-circle" aria-hidden="true"></i>
											<strong>
												Upload dokument
											</strong>
										</h4>
									</div> 
									<div class="modal-body material-modal__body">
										<form action="<?php getSiteURL(); ?>do.php?form=add_kandidat_doc" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id="form_add_doc_we">
											<input type="hidden" id="document_dataid" name="document_dataid">
											<input type="hidden" id="document_name" name="document_name">
											<input type="hidden" name="request_location" value="nalozi_open_status_prijave">
											<input type="hidden" name="nalog_id" value="<?php echo $nalog_id; ?>">
											<div class="form-group">
												<div class="col-md-offset-2 col-sm-8">
													<label for="document_desc" class="col-sm-4 control-label">
														Opis dokumenta:
													</label>
													<div class="col-sm-8">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="document_desc" id="document_desc" placeholder="Unesite opis dokumenta (nije obavezno)">
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>
											</div>
											<div class="alert_size_we form-group hidden">
												<div class="col-md-offset-2 col-sm-8">
													<div class="alert material-alert material-alert_danger">Greška: Dokument koji pokuštavate dodati je veći od dozvoljene veličine.</div>
												</div>
											</div>
											<div class="alert_ext_we form-group hidden">
												<div class="col-md-offset-2 col-sm-8">
													<div class="alert material-alert material-alert_danger">Greška: Format dokumenta kojeg pokušavate dodati nije dozvoljen.</div>
												</div>
											</div>
											<div class="form-group">
												<div class="col-md-offset-2 col-sm-8 text-center">
													<div class="fileinput fileinput-new" data-provides="fileinput">
														<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi dokument</span><span class="fileinput-exists">Promijeni</span>
														<input type="file" name="document_file" id="document_file" required></span> <i class="fa fa-info-circle fa-lg idk_margin_left10" data-toggle="tooltip" data-placement="right" title="Napomena: Dokument ne smije biti veći od 20MB! - Dozvoljeni formati: jpg, jpeg, pdf, doc, docx, xls, xlsx, txt, ppt, pptx, png" aria-hidden="true"></i>
														<br>
														<span class="fileinput-filename"></span>
														<button class="close fileinput-exists" data-dismiss="fileinput"><i class="fa fa-times-circle" aria-hidden="true"></i></button>
														<script>
															$(function (){
																$('#document_file', '#form_add_doc_we', '#add_doc_we').change(function (){
																	if($('#document_file', '#form_add_doc_we', '#add_doc_we').val() !== ""){
																		var f = this.files[0];
																		if (f.size > 25388608 || f.fileSize > 25388608){
																			$('.alert_size_we','#form_add_doc_we', '#add_doc_we').removeClass('hidden');
																			setTimeout(function(){
																				$('.alert_size_we','#form_add_doc_we', '#add_doc_we').addClass('hidden');
																			}, 5000);
																			this.value = null;
																		}else{
																			$('.alert_size_we','#form_add_doc_we', '#add_doc_we').addClass('hidden');
																		}

																		var ext = $('#document_file').val().split('.').pop().toLowerCase();
																		if($.inArray(ext, ['jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png']) == -1) {
																			$('.alert_ext_we','#form_add_doc_we', '#add_doc_we').removeClass('hidden');
																			setTimeout(function(){
																				$('.alert_ext_we','#form_add_doc_we', '#add_doc_we').addClass('hidden');
																			}, 5000);
																			this.value = null;
																		}else{
																			$('.alert_ext_we','#form_add_doc_we', '#add_doc_we').addClass('hidden');
																		}
																	}
																});
															});
														</script>
													</div>
												</div>
											</div>
											<div class="modal-footer material-modal__footer">
												<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
												<button type="submit" class="btn btn-success material-btn material-btn_success" form="form_add_doc_we"><i class="fa fa-check-square-o" aria-hidden="true"></i> Spremi</button>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
						<script>
							$('#add_doc_we').on('hidden.bs.modal',resetFormWe);
						</script>
					<!-- MODAL UPLOAD DOKUMENTI RADNO ISKUSTVO END -->

				<?php	
				break;
				
				case "insert_reject_reason":
					$nalog_id 		= $_POST['nalog_id'];
					$status_id 		= $_POST['status_id'];
					$novi_status_id = 1;
					$kandidat_id 	= $_POST['kandidat_id'];
					$rr_id 			= $_POST['razlog_odustajanja'];
					$razlog_opis	= $_POST['razlog_opis'];
					$stari_projekt  = null;
					$izvor = 1;
					
					insert_reject_reason($nalog_id, $status_id, $novi_status_id, $kandidat_id, $rr_id, $razlog_opis, $stari_projekt, $izvor);
					
					header("Location: " . getSiteURLr() . "nalozi?page=open_status_prijave&sid=".$status_id."&nid=".$nalog_id); 
				break;

				case "edit":
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "7" , $getEmployeeStatus)) OR (in_array( "9" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){	

						$nalog_id = $_GET['id'];

						$query = $db->prepare("
										SELECT nalog_broj, nalog_naziv, nalog_opis, nalog_status, nalog_marketing_menadzer, nalog_financije, nalog_partner_provizija, nalog_dospijece, employee_id
										FROM idk_nalozi
										WHERE nalog_id = :nalog_id");

						$query->execute(array(
									':nalog_id' => $nalog_id));

						$row = $query->fetch();

						$nalog_naziv = $row['nalog_naziv'];
						$nalog_opis = $row['nalog_opis'];
						$nalog_broj = $row['nalog_broj'];
						$nalog_status = $row['nalog_status'];
						$nalog_marketing_menadzer = $row['nalog_marketing_menadzer'];
						$nalog_financije = $row['nalog_financije'];
						$nalog_partner_provizija = $row['nalog_partner_provizija'];				
						$nalog_dospijece = $row['nalog_dospijece'];
						$nalog_employee_id = $row['employee_id']; 
						$current_employee_status = getEmployeeStatusById($nalog_employee_id);
						$current_employee_name = getEmployeeFullnameById($nalog_employee_id);
						$employeeDeactivated = 0;
						if ($current_employee_status == "0"){
							$employeeDeactivated = 1;
						}
						
						$query_marketing = $db->prepare("
										SELECT employee_id, employee_firstname, employee_lastname
										FROM idk_employees
										WHERE employee_id = :employee_id");

						$query_marketing->execute(array(':employee_id' => $nalog_marketing_menadzer));

						$row_marketing = $query_marketing->fetch();

						$employee_firstname = $row_marketing['employee_firstname'];
						$employee_lastname = $row_marketing['employee_lastname'];
						
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-file-o idk_color_green" aria-hidden="true"></i> Uredi podatke o nalogu</h1>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-md-offset-1 col-md-8">
								<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=edit_nalog" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="nalog_id" value="<?php echo $nalog_id; ?>" />
                                    <div class="form-group">
										<label for="project_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv naloga:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="nalog_naziv" id="nalog_naziv" value="<?php echo $nalog_naziv; ?>" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
                                    <div class="form-group">
										<label for="project_plannedhours" class="col-sm-3 control-label">Broj naloga:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="nalog_broj" id="nalog_broj" value="<?php echo $nalog_broj; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="project_plannedhours" class="col-sm-3 control-label">Opis naloga:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="nalog_opis" id="nalog_opis" value="<?php echo $nalog_opis; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="project_plannedhours" class="col-sm-3 control-label">Provizija naloga za partnere(€):</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="nalog_partner_provizija" id="nalog_partner_provizija" value="<?php echo $nalog_partner_provizija; ?>" placeholder="Iznos u € (Partner APP)">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="nalog_dospijece" class="col-sm-3 control-label">Broj dana za dospijeće plaćanja:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="nalog_dospijece" id="nalog_dospijece" value="<?php echo $nalog_dospijece; ?>" placeholder="Broj dana">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="project_pmanagerid" class="col-sm-3 control-label"><span class="text-danger">*</span> Status naloga:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="nalog_status" name="nalog_status" data-live-search="true" required>
												<option value="1" <?php if($nalog_status == 1){echo "selected";} ?>>Potpis</option>
												<option value="2" <?php if($nalog_status == 2){echo "selected";} ?>>Čeka se uplata</option>
												<option value="3" <?php if($nalog_status == 3){echo "selected";} ?>>Marketing</option>
												<option value="4" <?php if($nalog_status == 4){echo "selected";} ?>>Prijave u toku</option>
												<option value="5" <?php if($nalog_status == 5){echo "selected";} ?>>Obrada prijava</option>
												<option value="6" <?php if($nalog_status == 6){echo "selected";} ?>>Nalog kod poslodavca</option>
												<option value="7" <?php if($nalog_status == 7){echo "selected";} ?>>Casting</option>
												<option value="9" <?php if($nalog_status == 9){echo "selected";} ?>>Na čekanju</option>
												<option value="10" <?php if($nalog_status == 10){echo "selected";} ?>>Kandidati u odlasku</option>
												<option value="11" <?php if($nalog_status == 11){echo "selected";} ?>>Završeno (nenaplaćeno)</option>
												<option value="8" <?php if($nalog_status == 8){echo "selected";} ?>>Završeno</option>
											</select>
											<span class="materail-input-block__line"></span>
										</div>
									</div>
									<?php 
										if ($employeeDeactivated == 1) {
										?>
											<div class="form-group" id="pmEmployeeDeactivatedMess">
												<div class="col-md-offset-2 col-sm-8">
													<div class="alert alert-danger text-center" style="margin-bottom: 0px;">
														Zaposlenik <strong><?php echo $current_employee_name; ?></strong> je deaktiviran!<br>
														Molimo, odaberite novog zaposlenika u polju <strong>Projekt menadžer</strong>.
													</div>
												</div>
											</div>
										<?php 
										}	
									?>
									<div class="form-group">
										<label for="nalog_project_menadzer" class="col-sm-3 control-label"><span class="text-danger">*</span> Projekt menadžer: </label>
										<div class="col-sm-9">
											<select class="selectpicker" id="nalog_project_menadzer" name="nalog_project_menadzer" title="Odaberite Projekt Menadžera" data-live-search="true" required>
												<?php 
													$query_project_manager_for_nalog = $db->prepare("
														SELECT 
															employee_id, 
															CONCAT(employee_firstname, ' ', employee_lastname) AS employee_full_name
														FROM 
															idk_employees 
														WHERE 
															(
																FIND_IN_SET(2, employee_status) > 0 
																AND 
																employee_status != 0
															)
															OR 
															employee_id = :employee_id
														ORDER BY 
															employee_id 
														ASC
													");
													$query_project_manager_for_nalog->execute(array(
														':employee_id' => $nalog_employee_id
													));
													if ($query_project_manager_for_nalog->rowCount() != 0) {
														while ($row_project_manager_for_nalog = $query_project_manager_for_nalog->fetch()) {
															echo '
																<option
																	'.(($nalog_employee_id == $row_project_manager_for_nalog["employee_id"]) ? "selected" : "").'
																	value = "'.$row_project_manager_for_nalog["employee_id"].'"
																>
																	'.$row_project_manager_for_nalog["employee_full_name"].'
																</option>
															';
														}
													}
												?>
											</select>
											<span class="materail-input-block__line"></span>
										</div>
									</div>
									<?php 
										if ($employeeDeactivated == 1) {
										?>
											<script>
												$('#nalog_project_menadzer', '#idk_form').change(function(){
													console.clear();
													var nalog_project_menadzer_new = $('#nalog_project_menadzer', '#idk_form').val();
													var nalog_project_menadzer_old = parseInt("<?php echo $nalog_employee_id; ?>"); 
													if (nalog_project_menadzer_new != nalog_project_menadzer_old) {
														if ($('#nalog_project_menadzer option[value="' + nalog_project_menadzer_old + '"]', '#idk_form').length > 0) {
															$('#nalog_project_menadzer option[value="' + nalog_project_menadzer_old + '"]', '#idk_form').remove();
															$('#nalog_project_menadzer', '#idk_form').selectpicker("refresh");
															if ($('#pmEmployeeDeactivatedMess', '#idk_form').length > 0) {
																$('#pmEmployeeDeactivatedMess', '#idk_form').remove();
															}
														}
													}
												});
											</script>
										<?php 
										}	
									?>
									<div class="form-group">
										<label for="nalog_marketing_menadzer" class="col-sm-3 control-label"><span class="text-danger">*</span> Marketing menadžer:</label>
										<div class="col-sm-9">
											<select class="selectpicker" id="nalog_marketing_menadzer" name="nalog_marketing_menadzer" required>
											<?php 
												$get_mark_employees = $db->prepare("SELECT employee_firstname, employee_lastname, employee_status, employee_id FROM idk_employees 
																		WHERE employee_odjel = 6 ");
												$get_mark_employees->execute();
												while($row_mark = $get_mark_employees->fetch()){
													$ime_prezime = $row_mark['employee_firstname']." ".$row_mark['employee_lastname'];
													$employee_status_marketing = $row_mark['employee_status'];
													$employee_id = $row_mark['employee_id'];
													?>
													<option value="<?php echo $employee_id; ?>" <?php if($nalog_marketing_menadzer == $employee_id){echo "selected";} if($employee_status_marketing == 0){echo "disabled";} ?>  ><?php echo $ime_prezime; ?></option>
												<?php
												}
												?>
											</select>
											<span class="materail-input-block__line"></span>
										</div>
									</div>
									<?php if((in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status)) OR (in_array("2", $employee_status))  OR ($logged_employee_id == $employee_id)){	?>
									<div class="form-group">
										<label for="nalog_financije" class="col-sm-3 control-label">Finansije:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success idk_radio_buttons">
												<label class="main-container__column material-radio-group material-radio-group_success" for="nalog_financije_da">
													<input type="radio" name="nalog_financije" id="nalog_financije_da" class="material-radiobox" value="1" <?php if($nalog_financije == "1"){echo "checked";} ?>  />
													<span class="material-radio-group__element material-radio-group__check-radio"></span>
													<span class="material-radio-group__element material-radio-group__caption">DA</span>
												</label>
											</div>
										
											<div class="materail-input-block materail-input-block_danger idk_radio_buttons">
												<label class="main-container__column material-radio-group material-radio-group_danger" for="nalog_financije_ne">
													<input type="radio" name="nalog_financije" id="nalog_financije_ne" class="material-radiobox" value="0" <?php if($nalog_financije == "0"){echo "checked";} ?> />
													<span class="material-radio-group__element material-radio-group__check-radio"></span>
													<span class="material-radio-group__element material-radio-group__caption">NE</span>
												</label>
											</div>
										</div>
									</div>
									<?php } ?>

									<br />
									<div class="form-group">
										<div class="col-sm-offset-2 col-sm-10 text-right">
											<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-save" aria-hidden="true"></i> <span>Snimi</span></button>
											<br /><small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
					}else{
						echo '
							<div class="alert material-alert material-alert_danger">
								<h4>NEMATE PRIVILEGIJE!</h4>
								<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
								<br />
								<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
							</div>
						';
					}

				break;

				case "del_doc":
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "7" , $getEmployeeStatus)) OR (in_array( "9" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){

						$document_id = $_GET['id'];

						//Get document name, dataid and delete document
						$doc_open_query = $db->prepare("
													SELECT document_name, document_file, document_dataid
													FROM idk_documents
													WHERE document_id = :document_id");

						$doc_open_query->execute(array(
												':document_id' => $document_id));

						$doc_open = $doc_open_query->fetch();

						$document_name = $doc_open['document_name'];
						$document_file = $doc_open['document_file'];
						$document_dataid = $doc_open['document_dataid'];

						unlink("files/files/nalozi/" . $document_file);

						//Add to LOGS
						$log_desc = "Obrisao dokument: " . $document_name . " ";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_employeeid, log_desc, log_date)
										VALUES
											(:log_employeeid, :log_desc, :log_date)");

						$log_query->execute(array(
										':log_employeeid' => $logged_employee_id,
										':log_desc' => $log_desc,
										':log_date' => $log_date));

						//Delete document from db
						$doc_del_query = $db->prepare("
													DELETE FROM idk_documents
													WHERE document_id = :document_id");

						$doc_del_query->execute(array(
											':document_id' => $document_id));

						header("Location: " . getSiteURLr() . "nalozi?page=open&id=$document_dataid&mess=2");

					}else{
						echo '
							<div class="alert material-alert material-alert_danger">
								<h4>NEMATE PRIVILEGIJE!</h4>
								<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
								<br />
								<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
							</div>
						';
					}
				break;

				case "del_note":
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "7" , $getEmployeeStatus)) OR (in_array( "9" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){

						$note_id = $_GET['id'];

						//Get note_txt and note_dataid
						$note_open_query = $db->prepare("
													SELECT note_txt, note_dataid
													FROM idk_notes
													WHERE note_id = :note_id");

						$note_open_query->execute(array(
												':note_id' => $note_id));

						$note_open = $note_open_query->fetch();

							$note_txt = $note_open['note_txt'];
							$note_dataid = $note_open['note_dataid'];

						//Add to LOGS
						$log_desc = "Obrisao bilješku: " .$note_txt. "";
						$log_type = "3";
						addToLogs($log_desc, $log_type); //Log za biljeske - $log_type = 3

						//Delete note from db
						$note_del_query = $db->prepare("
													DELETE FROM idk_notes
													WHERE note_id = :note_id");

						$note_del_query->execute(array(
											':note_id' => $note_id));

						header("Location: " . getSiteURLr() . "nalozi?page=open&id=$note_dataid&mess=4");

					}else{
						echo '
							<div class="alert material-alert material-alert_danger">
								<h4>NEMATE PRIVILEGIJE!</h4>
								<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
								<br />
								<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
							</div>
						';
					}
				break;

				case "archive":
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "7" , $getEmployeeStatus)) OR (in_array( "9" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){

						$company_id = $_GET['id'];

						//Get
						$query_select = $db->prepare("
												SELECT company_name
												FROM idk_companies
												WHERE company_id = :company_id");

						$query_select->execute(array(
											':company_id' => $company_id));

						$row_select = $query_select->fetch();

						$company_name = $row_select['company_name'];

						//Save
						$query = $db->prepare("
										UPDATE idk_companies
										SET company_status = :company_status
										WHERE company_id = :company_id");

						$query->execute(array(
									':company_status' => 0,
									':company_id' => $company_id));

						//Add to LOGS
						$log_desc = "Arhivirao profil kompanije: " . $company_name . "";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_employeeid, log_desc, log_date)
										VALUES
											(:log_employeeid, :log_desc, :log_date)");

						$log_query->execute(array(
										':log_employeeid' => $logged_employee_id,
										':log_desc' => $log_desc,
										':log_date' => $log_date));


						header("Location: " . getSiteURLr() . "nalozi?page=open&id=$note_dataid&mess=4");

					}else{
						echo '
							<div class="alert material-alert material-alert_danger">
								<h4>NEMATE PRIVILEGIJE!</h4>
								<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
								<br />
								<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
							</div>
						';
					}
				break;

				case "project_disconnect":
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "7" , $getEmployeeStatus)) OR (in_array( "9" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){

						$project_id = $_GET['id'];

						//Get
						$query_select = $db->prepare("
												SELECT project_nalogid
												FROM idk_projects
												WHERE project_id = :project_id");

						$query_select->execute(array(
											':project_id' => $project_id));

						$row_select = $query_select->fetch();

						$nalogid = $row_select['project_nalogid'];

						//Save
						$query = $db->prepare("
										UPDATE idk_projects
										SET project_nalogid = :project_nalogid
										WHERE project_id = :project_id");

						$query->execute(array(
									':project_nalogid' => 0,
									':project_id' => $project_id));


						header("Location: " . getSiteURLr() . "nalozi?page=open&id=$nalogid&mess=7");

					}else{
						echo '
							<div class="alert material-alert material-alert_danger">
								<h4>NEMATE PRIVILEGIJE!</h4>
								<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
								<br />
								<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
							</div>
						';
					}
				break;

				case "del_timeline":
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "7" , $getEmployeeStatus)) OR (in_array( "9" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){

						$timeline_id = $_GET['id'];

						//Get timeline_txt and timeline_dataid
						$timeline_open_query = $db->prepare("
													SELECT timeline_txt, timeline_dataid
													FROM idk_timeline
													WHERE timeline_id = :timeline_id");

						$timeline_open_query->execute(array(
												':timeline_id' => $timeline_id));

						$timeline_open = $timeline_open_query->fetch();

							$timeline_txt = strip_tags($timeline_open['timeline_txt']);
							$timeline_dataid = $timeline_open['timeline_dataid'];

						//Add to LOGS
						$log_desc = "Obrisao aktivnost: " . $timeline_txt . " ";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_employeeid, log_desc, log_date)
										VALUES
											(:log_employeeid, :log_desc, :log_date)");

						$log_query->execute(array(
										':log_employeeid' => $logged_employee_id,
										':log_desc' => $log_desc,
										':log_date' => $log_date));

						//Delete timeline from db
						$timeline_del_query = $db->prepare("
													DELETE FROM idk_timeline
													WHERE timeline_id = :timeline_id");

						$timeline_del_query->execute(array(
											':timeline_id' => $timeline_id));

						header("Location: " . getSiteURLr() . "companies?page=open&id=$timeline_dataid&mess=7");

					}else{
						echo '
							<div class="alert material-alert material-alert_danger">
								<h4>NEMATE PRIVILEGIJE!</h4>
								<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
								<br />
								<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
							</div>
						';
					}
				break;

				case "del_task":
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "7" , $getEmployeeStatus)) OR (in_array( "9" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){

						$task_id = $_GET['id'];

						//Get task_txt and task_dataid
						$task_open_query = $db->prepare("
													SELECT task_txt, task_dataid, task_emailnotifi, task_status
													FROM idk_tasks
													WHERE task_id = :task_id");

						$task_open_query->execute(array(
												':task_id' => $task_id));

						$task_open = $task_open_query->fetch();

							$task_txt = strip_tags($task_open['task_txt']);
							$task_dataid = $task_open['task_dataid'];
							$task_emailnotifi = $task_open['task_emailnotifi'];
							$task_status = $task_open['task_status'];

						//Add to LOGS
						$log_desc = "Obrisao zadatak: " . $task_txt . " ";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_employeeid, log_desc, log_date)
										VALUES
											(:log_employeeid, :log_desc, :log_date)");

						$log_query->execute(array(
										':log_employeeid' => $logged_employee_id,
										':log_desc' => $log_desc,
										':log_date' => $log_date));

						//Delete task from db
						$task_del_query = $db->prepare("
													DELETE FROM idk_tasks
													WHERE task_id = :task_id");

						$task_del_query->execute(array(
											':task_id' => $task_id));

						//Remove Trigger
						if($task_emailnotifi != 0 && $task_status == 1){
							$url_tag = "email_task_" . $task_id . "";
							removeTrigger($url_tag);
						}

						header("Location: " . getSiteURLr() . "companies?page=open&id=$task_dataid&mess=11");

					}else{
						echo '
							<div class="alert material-alert material-alert_danger">
								<h4>NEMATE PRIVILEGIJE!</h4>
								<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
								<br />
								<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
							</div>
						';
					}
				break;

				case "reorderProjects":
					
					$project_ids = $_POST["ids"];
					
					$idArray = explode(",",$project_ids);
					
					$count = 1;
					foreach ($idArray as $id){
							
						$query = $db->prepare("
										UPDATE idk_projects
										SET	project_order = :project_order
										WHERE project_id = :project_id");
							
						$query->execute(array(
								':project_order' => $count,
								':project_id' => $id
								));			
							
					
					$count ++;    
					
					}
					return TRUE;
				
				break;
				
				case "del_company_info":
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "7" , $getEmployeeStatus)) OR (in_array( "9" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){

						$comi_id = $_GET['id'];

						//Get comi_title, comi_data and comi_companyid
						$phone_open_query = $db->prepare("
													SELECT comi_title, comi_data, comi_companyid
													FROM idk_companies_info
													WHERE comi_id = :comi_id");

						$phone_open_query->execute(array(
												':comi_id' => $comi_id));

						$phone_open = $phone_open_query->fetch();

							$comi_title = $phone_open['comi_title'];
							$comi_data = $phone_open['comi_data'];
							$comi_companyid = $phone_open['comi_companyid'];

						//Add to LOGS
						$log_desc = "Obrisao kontakt informaciju: " . $comi_title . " - " . $comi_data . "";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_employeeid, log_desc, log_date)
										VALUES
											(:log_employeeid, :log_desc, :log_date)");

						$log_query->execute(array(
										':log_employeeid' => $logged_employee_id,
										':log_desc' => $log_desc,
										':log_date' => $log_date));

						//Delete phone from db
						$phone_del_query = $db->prepare("
													DELETE FROM idk_companies_info
													WHERE comi_id = :comi_id");

						$phone_del_query->execute(array(
											':comi_id' => $comi_id));

						header("Location: " . getSiteURLr() . "companies?page=open&id=$comi_companyid&mess=16");

					}else{
						echo '
							<div class="alert material-alert material-alert_danger">
								<h4>NEMATE PRIVILEGIJE!</h4>
								<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
								<br />
								<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
							</div>
						';
					}
				break;
				
				case "edit_finances":
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "7" , $getEmployeeStatus)) OR (in_array( "9" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){

						$nalog_id = $_GET['id'];

						$query = $db->prepare("
										SELECT nalog_ugovor, nalog_potrebno_kandidata, nalog_provizija, nalog_broj_rata, nalog_financije
										FROM idk_nalozi
										WHERE nalog_id = :nalog_id");

						$query->execute(array(
									':nalog_id' => $nalog_id));

						$row = $query->fetch();

						$nalog_ugovor = $row['nalog_ugovor'];
						$nalog_potrebno_kandidata = $row['nalog_potrebno_kandidata'];
						$nalog_provizija = $row['nalog_provizija'];
						$nalog_broj_rata = $row['nalog_broj_rata'];
						$nalog_nacin_placanja = $row['nalog_financije'];

						$placanje_query = $db->prepare('SELECT
															nf_placeno,
															kf_placeno
														FROM
															idk_nalozi
														LEFT JOIN idk_kandidat_financije ON idk_nalozi.nalog_id = idk_kandidat_financije.nalog_id
														LEFT JOIN idk_nalog_financije ON idk_nalozi.nalog_id = idk_nalog_financije.nf_nalog_id
														WHERE
															idk_nalozi.nalog_id = :nalog_id');
						$placanje_query->execute(array(
									':nalog_id' => $nalog_id));	

						while($result = $placanje_query->fetch()){
							if(in_array($result['nf_placeno'], [2,3]) || in_array($result['kf_placeno'], [2,3])){
								$nalog_financije = 1;
								break;
							}else{
								$nalog_financije = 0;
							}
						}


						$style_nacin_placanja = ($nalog_financije == 1) ? 'style="display: none;"' : 'required';
						
						if($nalog_broj_rata != 0){
							$query_rate = $db->prepare("
												SELECT nr_nalog, nr_rata, nr_procenat, nr_vrijeme_placanja, nr_datum, nr_mjeseci_nakon
												FROM idk_nalozi_rate
												WHERE nr_nalog = :nalog_id");
											
							$query_rate->execute(array(
												':nalog_id' => $nalog_id));
										
							while($row_rate = $query_rate->fetch()){
								$nr_nalog[] = $row_rate['nr_nalog'];
								$nr_rata[] = $row_rate['nr_rata'];
								$nr_procenat[] = $row_rate['nr_procenat'];
								if($row_rate['nr_vrijeme_placanja'] == "odmah")		$vrijeme[] = "Odmah";
								else if($row_rate['nr_vrijeme_placanja'] == "ugovor")	$vrijeme[] = "Ugovor";
								else if($row_rate['nr_vrijeme_placanja'] == "pocetak rada")	$vrijeme[] = "Početak rada";
								else if($row_rate['nr_vrijeme_placanja'] == "mjeseci nakon")	$vrijeme[] = "Nakon početka rada";
								$nr_vrijeme_placanja[] = $row_rate['nr_vrijeme_placanja'];
								$nr_datum[] = $row_rate['nr_datum'];
								$nr_mjeseci_nakon[] = $row_rate['nr_mjeseci_nakon'];
							}
							
							$odmah_array[0] = "ugovor";
							$odmah_array[1] = "pocetak rada";
							$odmah_array[2] = "mjeseci nakon";
							
							$ugovor_array[0] = "pocetak rada";
							$ugovor_array[1] = "mjeseci nakon";
						}
		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-file-o idk_color_green" aria-hidden="true"></i> Uredi financijske podatke o nalogu</h1>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-md-offset-1 col-md-8">
								<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=edit_nalog_finances" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="nalog_id" value="<?php echo $nalog_id; ?>" />
                                    <div class="form-group" <?php echo $style_nacin_placanja; ?>>
										<label for="vrsta_placanja" class="col-sm-3 control-label"><span class="text-danger">*</span> Vrsta plaćanja:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<select class="form-control materail-input materail-input-custom" name="vrsta_placanja" id="vrsta_placanja" title="Odaberite vrstu plaćanja..." >
													<option value="1" <?php echo ($nalog_nacin_placanja == 1) ? "selected" : "";?>>Standardni način</option>
													<option value="2" <?php echo ($nalog_nacin_placanja == 2) ? "selected" : "";?>>Mjesečne rate</option>
													<option value="3" <?php echo ($nalog_nacin_placanja == 3) ? "selected" : "";?>>Po plati kandidata</option>
												</select>
											</div>
										</div>
									</div>
                                    <div class="form-group">
										<label for="project_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Vrsta ugovora:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<select class="form-control materail-input materail-input-custom" name="nalog_ugovor" id="nalog_ugovor" required>
													<option value="<?php if($nalog_ugovor == "okvirni") echo "okvirni"; else echo "nalog" ?>"><?php if($nalog_ugovor == "okvirni") echo "Okvirni"; else echo "Nalog" ?></option>
													<option value="<?php if($nalog_ugovor == "okvirni") echo "nalog"; else echo "okvirni" ?>"><?php if($nalog_ugovor == "okvirni") echo "Nalog"; else echo "Okvirni" ?></option>
												</select>
											</div>
										</div>
									</div>
                                    <div class="form-group">
										<label for="project_plannedhours" class="col-sm-3 control-label"> Broj potrebnih kandidata:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="nalog_potrebno_kandidata" id="nalog_potrebno_kandidata" value="<?php echo $nalog_potrebno_kandidata; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group" id="provizija">
										<label for="project_plannedhours" class="col-sm-3 control-label"> Provizija po kandidatu:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="number" name="nalog_provizija" id="nalog_provizija" value="<?php echo $nalog_provizija; ?>" min="0" step="0.01">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group" id="koeficijent_plate" style="display: none;">
										<label for="project_plannedhours" class="col-sm-3 control-label"> Koeficijent plate:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="number" name="koeficijent" id="koeficijent"  min="0" step="0.01">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group" id="avans" style="display: none;">
										<label for="avans_choose" class="col-sm-3 control-label">Avans:</label>
										<div class="col-sm-9">
											<div class="main-container__column materail-switch materail-switch_primary">
												<input class="materail-switch__element" type="checkbox" id="switch_input1" name="avans_choose" value="1">
												<label class="materail-switch__label" for="switch_input1"></label>
											</div>
										</div>
									</div>
									<div class="form-group" id="avans_postotak" style="display: none;">
										<label for="project_plannedhours" class="col-sm-3 control-label"> Postotak avansa:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="number" name="postotak_avans" id="postotak_avans" min="0" step="0.01">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="project_plannedhours" class="col-sm-3 control-label"> Broj rata:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="nalog_broj_rata" id="nalog_broj_rata" value="<?php echo $nalog_broj_rata; ?>">
												<input type="hidden" name="nalog_broj_rata_stari" value="<?php echo $nalog_broj_rata; ?>">
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<br />
									<div class="form-group">
										<div class="col-sm-offset-2 col-sm-10 text-right">
											<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-save" aria-hidden="true"></i> <span>Snimi</span></button>
											<br /><small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			<script>
				$(document).ready(function(){
					let vrsta_placanja = $("#vrsta_placanja").val();


					if(vrsta_placanja == 2){
						$("#avans").css("display", "block");
					}

					if(vrsta_placanja == 3){
						$("#koeficijent_plate").css("display", "block");
					}

					$("#vrsta_placanja").change(function(){
						let vrsta_placanja = $(this).val();

						if(vrsta_placanja == 2){
							$('#avans').css("display", "block");
						} else {
							$('#avans').css("display", "none");
							$('#avans_postotak').css("display", "none");
						}

						if(vrsta_placanja == 3){
							$('#koeficijent_plate').css("display", "block");
						} else {
							$('#koeficijent_plate').css("display", "none");
						}
					});

					$("#switch_input1").change(function(){
						if($(this).is(":checked")){
							$("#avans_postotak").css("display", "block");
						}else{
							$("#avans_postotak").css("display", "none");
						}
					});
				});
			</script>
		<?php
					}else{
						echo '
							<div class="alert material-alert material-alert_danger">
								<h4>NEMATE PRIVILEGIJE!</h4>
								<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
								<br />
								<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
							</div>
						';
					}

				break;
				
				case "add_rate":
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "7" , $getEmployeeStatus)) OR (in_array( "9" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){
						$nalog_id = $_GET['id'];
						$nr_rata = $_GET['nr_rata'];
						$nalog_datum_potpisa = $_GET['datum_pu'];
						
						$nalog_query = $db->prepare("
									SELECT nalog_broj_rata, nalog_kreirano, nalog_financije
									FROM idk_nalozi
									WHERE nalog_id = :nalog_id");

						$nalog_query->execute(array(
									':nalog_id' => $nalog_id));

						$nalog_row = $nalog_query->fetch();
						
						$nalog_broj_rata = $nalog_row['nalog_broj_rata'];
						$nalog_kreirano = $nalog_row['nalog_kreirano'];
						$nalog_financije = $nalog_row['nalog_financije'];
						
						if($nr_rata > 1){
							$rata_query = $db->prepare("
										SELECT nr_vrijeme_placanja
										FROM idk_nalozi_rate
										WHERE nr_rata = :nr_rata AND nr_nalog = :nr_nalog");

							$rata_query->execute(array(
										':nr_rata' => $nr_rata-1,
										':nr_nalog' => $nalog_id));

							$rata_row = $rata_query->fetch();
							
							$prethodna_rata = $rata_row['nr_vrijeme_placanja'];
						}
						
						if($nr_rata == $nalog_broj_rata){
							$procenat_query = $db->prepare("
										SELECT nr_procenat
										FROM idk_nalozi_rate
										WHERE nr_nalog = :nr_nalog");

							$procenat_query->execute(array(
										':nr_nalog' => $nalog_id));

							$suma_procenat = 0;
							
							while($procenat_row = $procenat_query->fetch()){
								$suma_procenat = $suma_procenat + $procenat_row['nr_procenat'];
							}
							$procenat_zadnji = 100 - $suma_procenat;
						}
					?>
					
				<div class="row">
					<div class="col-xs-8">
						<h1><i class="fa fa-file-o idk_color_green" aria-hidden="true"></i> Unos rata za nalog</h1>
					</div>
					<div class="col-xs-12">
						<hr />
					</div>
				</div>
				<?php 
					if(isset($_GET['mess1'])) {
						$mess1 = $_GET['mess1'];

						$mess1_text = "";
						$mess1_type = "";

						if ($mess1 == 1) {
							$mess1_text = "Uspješno kreiran nalog i postavljene financije naloga za nostrifikaciju.";
							$mess1_type = "success";
						} else if ($mess1 == 100 OR $mess1 == 101 OR $mess1 == 102) {
							$mess1_text = "Na prethodnom koraku desio se problem kod postavljanja financija nostrifikacije za naloga.";
							$mess1_type = "danger";
						} else {
							$mess1_text = "Na prethodnom koraku desio se problem kod unosa neke od rata financija nostrifikacije za nalog.";
							$mess1_type = "warning";
						}

						?>
							<div class="row">
								<div class="col-md-offset-2 col-sm-8">
									<div class="alert material-alert material-alert_<?php echo $mess1_type; ?>"><?php echo $mess1_text; ?></div>
								</div>
							</div>
						<?php 
					}
				?>
				<div class="row">
					<div class="col-md-12">
						<div class="content_box">
							<div class="row">
								<div class="col-md-offset-1 col-md-8">
									<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=add_rate" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
										<input type="hidden" name="nalog_id" value="<?php echo $nalog_id; ?>" />
										<input type="hidden" name="nr_rata" value="<?php echo $nr_rata; ?>" />
										<input type="hidden" name="nalog_datum_potpisa" value="<?php echo $nalog_datum_potpisa; ?>" />
										<?php if($nr_rata == $nalog_broj_rata){?>
											<input type="hidden" name="nr_procenat" value="<?php echo $procenat_zadnji; ?>" />
										<?php } ?>
										<h5>Rata <?php echo $nr_rata?>.:</h5>
										<div class="form-group">
											<label for="project_plannedhours" class="col-sm-3 control-label"><span class="text-danger">*</span> Procenat:</label>
											<div class="col-sm-9">
												<div class="materail-input-block materail-input-block_success">
													<input class="form-control materail-input" type="number" name="nr_procenat" id="nr_procenat" placeholder="100%" required <?php if($nr_rata == $nalog_broj_rata){?> value="<?php echo $procenat_zadnji?>" disabled <?php } ?>>
													<span class="materail-input-block__line"></span>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label for="nr_vrijeme_placanja" class="col-sm-3 control-label"><span class="text-danger">*</span> Vrijeme plaćanja:</label>
											<div class="col-sm-9">
												<div class="materail-input-block materail-input-block_success">
													<select class="form-control materail-input materail-input-custom" id="nr_vrijeme_placanja" name="nr_vrijeme_placanja" required>
														<?php 
															if ($nalog_financije != 3) {
														?> 
															<?php if($nr_rata == 1){?>
																<option value="odmah">Odmah</option>
																<option value="ugovor">Ugovor</option>
																<option value="dobio vizu">Dobio vizu</option>
																<option value="pocetak rada">Početak rada</option>
																<option value="mjeseci nakon">Nakon početka rada</option>
															<?php }else if($prethodna_rata === "odmah"){?>
																<option value="ugovor">Ugovor</option>
																<option value="dobio vizu">Dobio vizu</option>
																<option value="pocetak rada">Početak rada</option>
																<option value="mjeseci nakon">Nakon početka rada</option>
															<?php } else if($prethodna_rata === "ugovor"){?>
																<option value="dobio vizu">Dobio vizu</option>
																<option value="pocetak rada">Početak rada</option>
																<option value="mjeseci nakon">Nakon početka rada</option>
															<?php } else if($prethodna_rata === "dobio vizu"){?>
																<option value="pocetak rada">Početak rada</option>
																<option value="mjeseci nakon">Nakon početka rada</option>
															<?php } else if($prethodna_rata === "pocetak rada"){?>
																<option value="mjeseci nakon">Nakon početka rada</option>
															<?php } else if($prethodna_rata === "mjeseci nakon"){?>
																<option value="mjeseci nakon">Nakon početka rada</option>	
															<?php } ?>
														<?php 
															} else {
														?>
															<?php if($nr_rata == 1){?>
																<option value="ugovor">Ugovor</option>
																<option value="dobio vizu">Dobio vizu</option>
																<option value="pocetak rada">Početak rada</option>
																<option value="mjeseci nakon">Nakon početka rada</option>
															<?php } else if($prethodna_rata === "ugovor"){?>
																<option value="dobio vizu">Dobio vizu</option>
																<option value="pocetak rada">Početak rada</option>
																<option value="mjeseci nakon">Nakon početka rada</option>
															<?php } else if($prethodna_rata === "dobio vizu"){?>
																<option value="pocetak rada">Početak rada</option>
																<option value="mjeseci nakon">Nakon početka rada</option>
															<?php } else if($prethodna_rata === "pocetak rada"){?>
																<option value="mjeseci nakon">Nakon početka rada</option>
															<?php } else if($prethodna_rata === "mjeseci nakon"){?>
																<option value="mjeseci nakon">Nakon početka rada</option>	
															<?php } ?>
														<?php 
															}
														?>
													</select>
													<span class="materail-input-block__line"></span>
												</div>
											</div>
										</div>
										
										<div class="form-group" id="show_mjesec_nakon_add">
											<label for="project_plannedhours" class="col-sm-3 control-label"><span class="text-danger">*</span> Broj mjeseci nakon početka rada:</label>
											<div class="col-sm-9">
												<div class="materail-input-block materail-input-block_success">
													<input class="form-control materail-input" type="text" name="nr_mjeseci_nakon" id="nr_mjeseci_nakon" placeholder="12">
													<span class="materail-input-block__line"></span>
												</div>
											</div>
										</div>
										<script>
											$(document).ready(function(){
												var vrijeme_placanja_add = $('option:selected',this).attr('value');
												if(vrijeme_placanja_add == "mjeseci nakon"){
														$("#show_mjesec_nakon_add").show();
														
													}
													else {
														$("#show_mjesec_nakon_add").hide();
														
													}
											});
												$("#nr_vrijeme_placanja").change(function(){
													var vrijeme_placanja_add = $('option:selected',this).attr('value');
													
													if(vrijeme_placanja_add == "mjeseci nakon"){
														$("#show_mjesec_nakon_add").show();
														
													}
													else {
														$("#show_mjesec_nakon_add").hide();
														
													}
												});
											
										
										</script>
										
										<br />
										<div class="form-group">
											<div class="col-sm-offset-2 col-sm-10 text-right">
												<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span>Spremi</span></button>
												<br /><small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
					<?php
					}else{
						echo '
							<div class="alert material-alert material-alert_danger">
								<h4>NEMATE PRIVILEGIJE!</h4>
								<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
								<br />
								<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
							</div>
						';
					}
				break;
				
				case "add_blokove_prijave":
				
					if((in_array( "2" , $getEmployeeStatus)) OR (in_array( "3" , $getEmployeeStatus)) OR (in_array( "4" , $getEmployeeStatus)) OR (in_array( "7" , $getEmployeeStatus)) OR (in_array( "9" , $getEmployeeStatus)) OR (in_array( "1" , $getEmployeeStatus))){
						$nalog_id = $_GET['id'];
						?>
						<style>
										label {
   											   padding-bottom: 5px;
   										       margin-bottom: 0px; 
										      }
											  .idk_radio_buttons{
												padding-top:0px;
												margin-top:5px;
											  }
									</style>
						<div class="row">
							<div class="col-xs-8">
								<h1><i class="fa fa-question-circle-o idk_color_green" aria-hidden="true"></i> Odabir kriterija - Profil 1</h1>
								
							</div>
							<div class="col-xs-12">
								<hr />
							</div>
						</div>
						<?php 
							if(isset($_GET['mess'])) {
								$mess = $_GET['mess'];

								$mess_text = "";
								$mess_type = "";

								if ($mess == 1) {
									$mess_text = "Uspješno kreiran nalog i postavljene financije naloga.";
									$mess_type = "success";
								} else if ($mess == 100 OR $mess == 101 OR $mess == 102) {
									$mess_text = "Na prethodnom koraku desio se problem kod postavljanja financija naloga.";
									$mess_type = "danger";
								} else if ($mess == 103) {
									$mess_text = "Na prethodnom koraku desio se problem kod unosa neke od rata za financije naloga.";
									$mess_type = "warning";
								} else if ($mess == 104) {
									$mess_text = "Na prethodnom koraku desio se problem kod unosa neke od rata za financije naloga ali je uredno izvršen unos avansa.";
									$mess_type = "warning";
								} else if ($mess == 105) {
									$mess_text = "Na prethodnom koraku desio se problem kod unosa neke od rata i kod unosa avansa za financije naloga.";
									$mess_type = "warning";
								} else {
									$mess_text = "Na prethodnom koraku desio se problem kod unosa avansa za financija naloga ali je uredno izvršen unos rata.";
									$mess_type = "warning";
								}

								?>
									<div class="row">
										<div class="col-md-offset-2 col-sm-8">
											<div class="alert material-alert material-alert_<?php echo $mess_type; ?>"><?php echo $mess_text; ?></div>
										</div>
									</div>
								<?php 
							}

							if(isset($_GET['mess1'])) {
								$mess1 = $_GET['mess1'];

								$mess1_text = "";
								$mess1_type = "";

								if ($mess1 == 1) {
									$mess1_text = "Uspješno kreiran nalog i postavljene financije naloga za nostrifikaciju.";
									$mess1_type = "success";
								} else if ($mess1 == 100 OR $mess1 == 101 OR $mess1 == 102) {
									$mess1_text = "Na prethodnom koraku desio se problem kod postavljanja financija nostrifikacije za naloga.";
									$mess1_type = "danger";
								} else {
									$mess1_text = "Na prethodnom koraku desio se problem kod unosa neke od rata financija nostrifikacije za nalog.";
									$mess1_type = "warning";
								}

								?>
									<div class="row">
										<div class="col-md-offset-2 col-sm-8">
											<div class="alert material-alert material-alert_<?php echo $mess1_type; ?>"><?php echo $mess1_text; ?></div>
										</div>
									</div>
								<?php 
							}
						?>
						<div class="row">
							<div class="col-md-12">
								<div class="content_box">
									<div class="row">
										<div class="col-md-offset-1 col-md-8">
											<form id="form_blokovi" action="<?php getSiteURL(); ?>do.php?form=add_blokove_prijave" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
												<input type="hidden" name="nalog_id" value="<?php echo $nalog_id; ?>" />
												
												<div class="form-group col-md-12">
													<div class="col-xs-6" style="padding-top:5px; text-align:right;">Vozačka dozvola:</div>
													<div class="materail-input-block materail-input-block_success idk_radio_buttons vozacka_da">
														<label class="main-container__column material-radio-group material-radio-group_success" for="nalog_vozacka_da">
															<input type="radio" name="nalog_vozacka" id="nalog_vozacka_da" class="material-radiobox" value="block" />
															<span class="material-radio-group__element material-radio-group__check-radio"></span>
															<span class="material-radio-group__element material-radio-group__caption">DA</span>
														</label>
													</div>
													<div class="materail-input-block materail-input-block_danger idk_radio_buttons vozacka_ne">
														<label class="main-container__column material-radio-group material-radio-group_danger" for="nalog_vozacka_ne">
															<input type="radio" name="nalog_vozacka" id="nalog_vozacka_ne" class="material-radiobox" value="none" checked />
															<span class="material-radio-group__element material-radio-group__check-radio"></span>
															<span class="material-radio-group__element material-radio-group__caption">NE</span>
														</label>
													</div>
												</div>
												
												<script>
													$(document).ready(function () {
														
														if( $('#nalog_vozacka_ne').is(':checked') ){
															$('.kategorije_da_ne').css('display', 'none');
															$('#nalog_vozacka_kat').removeAttr('required');
														}
														else{
															$('.kategorije_da_ne').css('display', 'block');
														}
															});
													$('.vozacka_da').click(function(){
														$('.kategorije_da_ne').css('display', 'block');
														
													});
													$('.vozacka_ne').click(function(){
														$('.kategorije_da_ne').css('display', 'none');
														$('.kategorije_vozacke').css('display', 'none');
														$('#nalog_kategorija_ne').prop("checked", true);
														$("#nalog_vozacka_kat").val(null).selectpicker('refresh');
														$('#nalog_vozacka_kat').removeAttr('required');
													});
												</script>
												
												<div class="form-group col-md-12 kategorije_da_ne" style="display: none">
													<div class="col-xs-6" style="padding-top:5px; text-align:right;">Kategorija vozačke dozvole:</div>
													<div class="materail-input-block materail-input-block_success idk_radio_buttons kategorije_v_da">
														<label class="main-container__column material-radio-group material-radio-group_success" for="nalog_kategorija_da">
															<input type="radio" name="nalog_kategorija" id="nalog_kategorija_da" class="material-radiobox" value="da" />
															<span class="material-radio-group__element material-radio-group__check-radio"></span>
															<span class="material-radio-group__element material-radio-group__caption">DA</span>
														</label>
													</div>
													<div class="materail-input-block materail-input-block_danger idk_radio_buttons kategorije_v_ne">
														<label class="main-container__column material-radio-group material-radio-group_danger" for="nalog_kategorija_ne">
															<input type="radio" name="nalog_kategorija" id="nalog_kategorija_ne" class="material-radiobox" value="ne" checked />
															<span class="material-radio-group__element material-radio-group__check-radio"></span>
															<span class="material-radio-group__element material-radio-group__caption">NE</span>
														</label>
													</div>
												</div>
												
												<script>
													$(document).ready(function () {
														
														if( $('#nalog_kategorija_ne').is(':checked') ){
															$('.kategorije_vozacke').css('display', 'none');
															$('#nalog_vozacka_kat').removeAttr('required');
														}
														else{
															$('.kategorije_vozacke').css('display', 'block');
															$('#nalog_vozacka_kat').attr('required', 'true');
														}
															});
													$('.kategorije_v_da').click(function(){
														$('.kategorije_vozacke').css('display', 'block');
														$('#nalog_vozacka_kat').attr('required', 'true');	 
													});
													$('.kategorije_v_ne').click(function(){
														$('#nalog_vozacka_kat').removeAttr('required');
														$('.kategorije_vozacke').css('display', 'none');
														$("#nalog_vozacka_kat").val(null).selectpicker('refresh');
														 
													});
												</script>
												
												<div class="form-group col-md-12 kategorije_vozacke" style="display: none">
													<div class="col-xs-6" style="padding-top:5px; text-align:right;">Kategorija vozačke dozvole:</div>
													<div class="col-md-6 col-xs-6" style="padding-left:0px;">
														<select class="selectpicker select_kategorije" multiple id="nalog_vozacka_kat" name="nalog_vozacka_kat[]" style="width: 75%;"    >
															
															
															<option value="B">B</option>
															<option value="C1">C1</option>
															<option value="C">C</option>
															<option value="BE">BE</option>
															<option value="B1E">B1E</option>
															<option value="CE">CE</option>
														</select>
													</div>
												</div>
												
												<!-- <div class="form-group col-md-12 ">
																<div class="col-xs-6" style="padding-top:5px; text-align:right;">Struka:</div>
																<div class="col-md-2 col-xs-6" style="padding-left:0px; ">
																	<select class="selectpicker" id="nbp_struka_id" multiple name="nbp_struka_id[]" style="">
																		<option value="" disabled></option>
																		<?php 
																		$query_struke = $db->prepare("SELECT id_struke, naziv_struke FROM idk_struke");
																		$query_struke->execute();
																		while($row_struke = $query_struke->fetch()){
																			?>
																			<option value="<?php echo $row_struke['id_struke']; ?>" ><?php echo $row_struke['naziv_struke']; ?></option>
																			<?php
																		}
																		?>
																		
																	</select>
																</div>
															</div> -->

												<!-- <div class="form-group col-md-12">
													<div class="col-xs-6" style="padding-top:5px; text-align:right;">Visoko obrazovanje:</div>
													<div class="materail-input-block materail-input-block_success idk_radio_buttons">
														<label class="main-container__column material-radio-group material-radio-group_success" for="nalog_visoko_obr_da">
															<input type="radio" name="nalog_visoko_obr" id="nalog_visoko_obr_da" class="material-radiobox" value="1" />
															<span class="material-radio-group__element material-radio-group__check-radio"></span>
															<span class="material-radio-group__element material-radio-group__caption">DA</span>
														</label>
													</div>
													<div class="materail-input-block materail-input-block_danger idk_radio_buttons">
														<label class="main-container__column material-radio-group material-radio-group_danger" for="nalog_visoko_obr_ne">
															<input type="radio" name="nalog_visoko_obr" id="nalog_visoko_obr_ne" class="material-radiobox" value="0" checked />
															<span class="material-radio-group__element material-radio-group__check-radio"></span>
															<span class="material-radio-group__element material-radio-group__caption">NE</span>
														</label>
													</div>
												</div> -->
												
												<!-- <div class="form-group col-md-12">
													<div class="col-xs-6" style="padding-top:5px; text-align:right;">Dodatna edukacija:</div>
													<div class="materail-input-block materail-input-block_success idk_radio_buttons">
														<label class="main-container__column material-radio-group material-radio-group_success" for="nalog_dodatno_obr_da">
															<input type="radio" name="nalog_dodatno_obr" id="nalog_dodatno_obr_da" class="material-radiobox" value="1" />
															<span class="material-radio-group__element material-radio-group__check-radio"></span>
															<span class="material-radio-group__element material-radio-group__caption">DA</span>
														</label>
													</div>
													<div class="materail-input-block materail-input-block_danger idk_radio_buttons">
														<label class="main-container__column material-radio-group material-radio-group_danger" for="nalog_dodatno_obr_ne">
															<input type="radio" name="nalog_dodatno_obr" id="nalog_dodatno_obr_ne" class="material-radiobox" value="0" checked />
															<span class="material-radio-group__element material-radio-group__check-radio"></span>
															<span class="material-radio-group__element material-radio-group__caption">NE</span>
														</label>
													</div>
												</div> -->
												
												<!-- <div class="form-group col-md-12">
													<div class="col-xs-6" style="padding-top:5px; text-align:right;">Radno iskustvo:</div>
													<div class="materail-input-block materail-input-block_success idk_radio_buttons">
														<label class="main-container__column material-radio-group material-radio-group_success" for="nalog_iskustvo_da">
															<input type="radio" name="nalog_iskustvo" id="nalog_iskustvo_da" class="material-radiobox" value="1" />
															<span class="material-radio-group__element material-radio-group__check-radio"></span>
															<span class="material-radio-group__element material-radio-group__caption">DA</span>
														</label>
													</div>
													<div class="materail-input-block materail-input-block_danger idk_radio_buttons">
														<label class="main-container__column material-radio-group material-radio-group_danger" for="nalog_iskustvo_ne">
															<input type="radio" name="nalog_iskustvo" id="nalog_iskustvo_ne" class="material-radiobox" value="0" checked />
															<span class="material-radio-group__element material-radio-group__check-radio"></span>
															<span class="material-radio-group__element material-radio-group__caption">NE</span>
														</label>
													</div>
												</div> -->

												<div class="form-group col-md-12">
													<div class="col-xs-6" style="padding-top:5px; text-align:right;">Radno iskustvo u struci:</div>
													<div class="materail-input-block materail-input-block_success idk_radio_buttons">
														<label class="main-container__column material-radio-group material-radio-group_success" for="nalog_iskustvo_struka_da">
															<input type="radio" name="nalog_iskustvo_struka" id="nalog_iskustvo_struka_da" class="material-radiobox" value="1"  />
															<span class="material-radio-group__element material-radio-group__check-radio"></span>
															<span class="material-radio-group__element material-radio-group__caption">DA</span>
														</label>
													</div>
													<div class="materail-input-block materail-input-block_danger idk_radio_buttons">
														<label class="main-container__column material-radio-group material-radio-group_danger" for="nalog_iskustvo_struka_ne">
															<input type="radio" name="nalog_iskustvo_struka" id="nalog_iskustvo_struka_ne" class="material-radiobox" value="0" checked />
															<span class="material-radio-group__element material-radio-group__check-radio"></span>
															<span class="material-radio-group__element material-radio-group__caption">NE</span>
														</label>
													</div>
												</div>

												<div class="form-group col-md-12" id="radno_iskustvo_trajanje_group" style="display:none;">
													<div class="col-xs-6" style="padding-top:5px; text-align:right;">Radno iskustvo u struci u posljednjih 5 godina:</div>
													<div class="col-md-6 col-xs-6" style="padding-left:0px;">
														<select class="selectpicker radno_iskustvo_trajanje" id="radno_iskustvo_trajanje" name="radno_iskustvo_trajanje">
															<option value="" disabled selected></option>
															<option value="0">Ne treba</option>
															<option value="1">Više od 0</option>
															<option value="2">1 godina i više</option>
															<option value="3">2 godine i više</option>
															<option value="4">3 godine i više</option>
															<option value="5">4 godine i više</option>
															<option value="6">5 godina i više</option>
														</select>
													</div>
												</div>
												<script>
													$(document).ready(function () {
														
														$('#nalog_iskustvo_struka_da').click(function() {
															if($('#nalog_iskustvo_struka_da').is(':checked')) { 
																$('#radno_iskustvo_trajanje_group').show();
																$("#radno_iskustvo_trajanje").prop('required',true);
															}
														});
														$('#nalog_iskustvo_struka_ne').click(function() {
															if($('#nalog_iskustvo_struka_ne').is(':checked')) { 
																$('#radno_iskustvo_trajanje_group').hide();
																$("#radno_iskustvo_trajanje").prop('required',false);
															}
														});
													});
												</script>
												<div class="form-group col-md-12">
													<div class="col-xs-6" style="padding-top:5px; text-align:right;">Starost kandidata:</div>
													<div class="materail-input-block materail-input-block_success idk_radio_buttons">
														<label class="main-container__column material-radio-group material-radio-group_success" for="nalog_starost_kandidata_da">
															<input type="radio" name="nalog_starost_kandidata" id="nalog_starost_kandidata_da" class="material-radiobox" value="1" />
															<span class="material-radio-group__element material-radio-group__check-radio"></span>
															<span class="material-radio-group__element material-radio-group__caption">DA</span>
														</label>
													</div>
													<div class="materail-input-block materail-input-block_danger idk_radio_buttons">
														<label class="main-container__column material-radio-group material-radio-group_danger" for="nalog_starost_kandidata_ne">
															<input type="radio" name="nalog_starost_kandidata" id="nalog_starost_kandidata_ne" class="material-radiobox" value="0" checked />
															<span class="material-radio-group__element material-radio-group__check-radio"></span>
															<span class="material-radio-group__element material-radio-group__caption">NE</span>
														</label>
													</div>
												</div>

												<script>

													$(document).ready(function () {
														if($('#nalog_starost_kandidata_da').is(':checked') ){
															$('#starost_od').prop('required',true);
															$('#starost_do').prop('required',true);
															
															
															$('.starost_range').css('display', 'block');
														}
														else{
															$('#starost_od').removeAttr('required');
															$('#starost_do').removeAttr('required');
															
															$('.starost_range').css('display', 'none');
														}
													});
													$('#nalog_starost_kandidata_da').click(function(){
														$('#starost_od').prop('required',true);
														$('#starost_do').prop('required',true);
														
														$('.starost_range').css('display', 'block');
													});
													$('#nalog_starost_kandidata_ne').click(function(){
														$('#starost_od').removeAttr('required');
														$('#starost_do').removeAttr('required');
														
														$('.starost_range').css('display', 'none');
													});
												</script>																				

												<div class="form-group col-md-12 starost_range" style="display: none;">
													<div class="col-xs-12" style="text-align: center;">
														<p style="padding-top: 5px;">
															Kandidat mora imati između: 			
															<input type="number" id="starost_od" name="starost_od" min="18" max="99" >
															i 
															<input type="number" id="starost_do" name="starost_do" min="18" max="99" >
															godina
															
															<!-- <p>
																<label class="col-xs-6" style="padding-top:5px; text-align:right;" for="amount">Razdoblje godina:</label>
																<input type="text" id="amount" readonly style="border:0; ">
															</p> -->
														</p>
																													
														<div class="col-md-6 col-md-offset-3" id="starost_od_do"></div>
													</div>
												</div>
												<script>

													$( "#starost_od" ).bind('keyup mouseup',function() {
														var starost_od= $("#starost_od").val();
														var starost_do= $("#starost_do").val();
														$("#starost_od").attr({"max": starost_do})
														$("#starost_do").attr({"min": starost_od})
														/* $( "#amount" ).val( "Od " + starost_od + " do " + starost_do+ " godina" ); */
														$("#starost_od_do").slider("values",[starost_od??18,starost_do??99])	
													});

													$( "#starost_do" ).bind('keyup mouseup',function() {
														var starost_od= $("#starost_od").val();
														var starost_do= $("#starost_do").val();
														$("#starost_do").attr({"min": starost_od})
														$("#starost_od").attr({"max": starost_do})
														/* $( "#amount" ).val( "Od " + starost_od + " do " + starost_do+ " godina" ); */
														$("#starost_od_do").slider("values",[starost_od??18,starost_do??99])	
													});
													$( function() {
														$( "#starost_od_do" ).slider({
														range: true,
														min: 18,
														max: 99,
														values: [18, 99],
														slide: function( event, ui ) {
															/* $( "#amount" ).val( "Od " + ui.values[ 0 ] + " do " + ui.values[ 1 ]+ " godina" ); */
															$("#starost_od").val(ui.values[0])
															$("#starost_do").val(ui.values[1])
															$("#starost_od").attr({"max": ui.values[1]})
															$("#starost_do").attr({"min": ui.values[0]})
														}
														});
														/* $( "#amount" ).val( "Od " + $( "#starost_od_do" ).slider( "values", 0 ) +
														" do " + $( "#starost_od_do" ).slider( "values", 1 )+ " godina" ); */
														var valueFrom = $("#starost_od_do").slider("values",0)
														var valueTo = $("#starost_od_do").slider("values",1)
														$("#starost_od").val(valueFrom);
														$("#starost_do").val(valueTo);
													} );
												</script>
												<div class="form-group col-md-12">
													<div class="col-xs-6" style="padding-top:5px; text-align:right;">Minimalno poznavanje njemačkog jezika:</div>
													<div class="col-md-6 col-xs-6" style="padding-left:0px;">
														<select class="selectpicker" id="min_njem_jez" name="min_njem_jez" style="width: 75%;" required>
															<option value="" selected disabled></option>
															<option value="BZ">Bez znanja</option>
															<option value="A1">A1</option>
															<option value="A2">A2</option>
															<option value="B1">B1</option>
															<option value="B2">B2</option>
															<option value="C1">C1</option>
															<option value="C2">C2</option>
														</select>
													</div>
												</div>

												<!-- OSTALI JEZICI -->
												<div class="form-group col-md-12">
													<div class="col-xs-6" style="padding-top:5px; text-align:right;">Ostali jezici:</div>
													<div class="materail-input-block materail-input-block_success idk_radio_buttons">
														<label class="main-container__column material-radio-group material-radio-group_success" for="nalog_jezici_da">
															<input type="radio" name="nalog_jezici" id="nalog_jezici_da" class="material-radiobox" value="1" />
															<span class="material-radio-group__element material-radio-group__check-radio"></span>
															<span class="material-radio-group__element material-radio-group__caption">DA</span>
														</label>
													</div>
													<div class="materail-input-block materail-input-block_danger idk_radio_buttons">
														<label class="main-container__column material-radio-group material-radio-group_danger" for="nalog_jezici_ne">
															<input type="radio" name="nalog_jezici" id="nalog_jezici_ne" class="material-radiobox" value="0" checked />
															<span class="material-radio-group__element material-radio-group__check-radio"></span>
															<span class="material-radio-group__element material-radio-group__caption">NE</span>
														</label>
													</div>
												</div>
												<script>
													$(document).ready(function () {
														$('#nalog_jezici_da').click(function() {
															if($('#nalog_jezici_da').is(':checked')) { 
																$('#kriterij_ostali_jezici_group').show();
															}
														});
														$('#nalog_jezici_ne').click(function() {
															if($('#nalog_jezici_ne').is(':checked')) { 
																$('#kriterij_ostali_jezici_group').hide();
															}
														});
													});
												</script>

												<div id="kriterij_ostali_jezici_group" style="display:none;">
													<!-- ENGLESKI JEZIK -->
													<div class="form-group col-md-12">
														<div class="col-xs-6" style="padding-top:5px; text-align:right;">Engleski jezik:</div>
														<div class="materail-input-block materail-input-block_success idk_radio_buttons kriterij_engleski_jezik_da">
															<label class="main-container__column material-radio-group material-radio-group_success" for="kriterij_engleski_jezik_da">
																<input type="radio" name="kriterij_engleski_jezik" id="kriterij_engleski_jezik_da" class="material-radiobox" value="1" />
																<span class="material-radio-group__element material-radio-group__check-radio"></span>
																<span class="material-radio-group__element material-radio-group__caption">DA</span>
															</label>
														</div>
														<div class="materail-input-block materail-input-block_danger idk_radio_buttons kriterij_engleski_jezik_ne">
															<label class="main-container__column material-radio-group material-radio-group_danger" for="kriterij_engleski_jezik_ne">
																<input type="radio" name="kriterij_engleski_jezik" id="kriterij_engleski_jezik_ne" class="material-radiobox" value="0" checked />
																<span class="material-radio-group__element material-radio-group__check-radio"></span>
																<span class="material-radio-group__element material-radio-group__caption">NE</span>
															</label>
														</div>
													</div>

													<!-- NIVO ENGLESKOG JEZIKA -->
													<div class="form-group col-md-12" id="kriterij_nivo_engleskog_jezika_group" style="display:none;">
														<div class="col-xs-6" style="padding-top:5px; text-align:right;">Minimalno poznavanje engleskog jezika:</div>
														<div class="col-xs-6" style="padding-left:0px; ">
															<select class="selectpicker" id="kriterij_nivo_engleskog_jezika" name="kriterij_nivo_engleskog_jezika" style="">
																<option value="" disabled="disabled"></option>
																<!-- <option value="BZ">Bez znanja</option> -->
																<option value="A1">A1</option>
																<option value="A2">A2</option>
																<option value="B1">B1</option>
																<option value="B2">B2</option>
																<option value="C1">C1</option>
																<option value="C2">C2</option>
															</select>
														</div>
													</div>
													<script>
														$(document).ready(function () {
															$('#kriterij_engleski_jezik_da').click(function() {
																if($('#kriterij_engleski_jezik_da').is(':checked')) { 
																	$('#kriterij_nivo_engleskog_jezika_group').show();
																	$("#kriterij_nivo_engleskog_jezika").prop('required',true);
																}
															});
															$('#kriterij_engleski_jezik_ne').click(function() {
																if($('#kriterij_engleski_jezik_ne').is(':checked')) { 
																	$('#kriterij_nivo_engleskog_jezika_group').hide();
																	$("#kriterij_nivo_engleskog_jezika").prop('required',false);
																}
															});
														});
													</script>
													<!-- ITALIJANSKI JEZIK -->
													<div class="form-group col-md-12">
														<div class="col-xs-6" style="padding-top:5px; text-align:right;">Italijanski jezik:</div>
														<div class="materail-input-block materail-input-block_success idk_radio_buttons kriterij_italijanski_jezik_da">
															<label class="main-container__column material-radio-group material-radio-group_success" for="kriterij_italijanski_jezik_da">
																<input type="radio" name="kriterij_italijanski_jezik" id="kriterij_italijanski_jezik_da" class="material-radiobox" value="1" />
																<span class="material-radio-group__element material-radio-group__check-radio"></span>
																<span class="material-radio-group__element material-radio-group__caption">DA</span>
															</label>
														</div>
														<div class="materail-input-block materail-input-block_danger idk_radio_buttons kriterij_italijanski_jezik_ne">
															<label class="main-container__column material-radio-group material-radio-group_danger" for="kriterij_italijanski_jezik_ne">
																<input type="radio" name="kriterij_italijanski_jezik" id="kriterij_italijanski_jezik_ne" class="material-radiobox" value="0" checked />
																<span class="material-radio-group__element material-radio-group__check-radio"></span>
																<span class="material-radio-group__element material-radio-group__caption">NE</span>
															</label>
														</div>
													</div>

													<!-- NIVO ITALIJANSKOG JEZIKA -->
													<div class="form-group col-md-12" id="kriterij_nivo_italijanskog_jezika_group" style="display:none;">
														<div class="col-xs-6" style="padding-top:5px; text-align:right;">Minimalno poznavanje engleskog jezika:</div>
														<div class="col-xs-6" style="padding-left:0px; ">
															<select class="selectpicker" id="kriterij_nivo_italijanskog_jezika" name="kriterij_nivo_italijanskog_jezika" style="">
																<option value="" disabled="disabled"></option>
																<!-- <option value="BZ">Bez znanja</option> -->
																<option value="A1">A1</option>
																<option value="A2">A2</option>
																<option value="B1">B1</option>
																<option value="B2">B2</option>
																<option value="C1">C1</option>
																<option value="C2">C2</option>
															</select>
														</div>
													</div>
													<script>
														$(document).ready(function () {
															$('#kriterij_italijanski_jezik_da').click(function() {
																if($('#kriterij_italijanski_jezik_da').is(':checked')) { 
																	$('#kriterij_nivo_italijanskog_jezika_group').show();
																	$("#kriterij_nivo_italijanskog_jezika").prop('required',true);
																}
															});
															$('#kriterij_italijanski_jezik_ne').click(function() {
																if($('#kriterij_italijanski_jezik_ne').is(':checked')) { 
																	$('#kriterij_nivo_italijanskog_jezika_group').hide();
																	$("#kriterij_nivo_italijanskog_jezika").prop('required',false);
																}
															});
														});
													</script>
													<!-- FRANCUSKI JEZIK -->
													<div class="form-group col-md-12">
														<div class="col-xs-6" style="padding-top:5px; text-align:right;">Francuski jezik:</div>
														<div class="materail-input-block materail-input-block_success idk_radio_buttons kriterij_francuski_jezik_da">
															<label class="main-container__column material-radio-group material-radio-group_success" for="kriterij_francuski_jezik_da">
																<input type="radio" name="kriterij_francuski_jezik" id="kriterij_francuski_jezik_da" class="material-radiobox" value="1" />
																<span class="material-radio-group__element material-radio-group__check-radio"></span>
																<span class="material-radio-group__element material-radio-group__caption">DA</span>
															</label>
														</div>
														<div class="materail-input-block materail-input-block_danger idk_radio_buttons kriterij_francuski_jezik_ne">
															<label class="main-container__column material-radio-group material-radio-group_danger" for="kriterij_francuski_jezik_ne">
																<input type="radio" name="kriterij_francuski_jezik" id="kriterij_francuski_jezik_ne" class="material-radiobox" value="0" checked />
																<span class="material-radio-group__element material-radio-group__check-radio"></span>
																<span class="material-radio-group__element material-radio-group__caption">NE</span>
															</label>
														</div>
													</div>

													<!-- NIVO FRANCUSKOG JEZIKA -->
													<div class="form-group col-md-12" id="kriterij_nivo_francuskog_jezika_group" style="display:none;">
														<div class="col-xs-6" style="padding-top:5px; text-align:right;">Minimalno poznavanje engleskog jezika:</div>
														<div class="col-xs-6" style="padding-left:0px; ">
															<select class="selectpicker" id="kriterij_nivo_francuskog_jezika" name="kriterij_nivo_francuskog_jezika" style="">
																<option value="" disabled="disabled"></option>
																<!-- <option value="BZ">Bez znanja</option> -->
																<option value="A1">A1</option>
																<option value="A2">A2</option>
																<option value="B1">B1</option>
																<option value="B2">B2</option>
																<option value="C1">C1</option>
																<option value="C2">C2</option>
															</select>
														</div>
													</div>
													<script>
														$(document).ready(function () {
															$('#kriterij_francuski_jezik_da').click(function() {
																if($('#kriterij_francuski_jezik_da').is(':checked')) { 
																	$('#kriterij_nivo_francuskog_jezika_group').show();
																	$("#kriterij_nivo_francuskog_jezika").prop('required',true);
																}
															});
															$('#kriterij_francuski_jezik_ne').click(function() {
																if($('#kriterij_francuski_jezik_ne').is(':checked')) { 
																	$('#kriterij_nivo_francuskog_jezika_group').hide();
																	$("#kriterij_nivo_francuskog_jezika").prop('required',false);
																}
															});
														});
													</script>
												</div>
												<!-- <div class="form-group col-md-12">
													<div class="col-xs-6" style="padding-top:5px; text-align:right;">Dokumenti:</div>
													<div class="materail-input-block materail-input-block_success idk_radio_buttons dokumenti_da">
														<label class="main-container__column material-radio-group material-radio-group_success" for="nalog_dokumenti_da">
															<input type="radio" name="nalog_dokumenti" id="nalog_dokumenti_da" class="material-radiobox" value="1" />
															<span class="material-radio-group__element material-radio-group__check-radio"></span>
															<span class="material-radio-group__element material-radio-group__caption">DA</span>
														</label>
													</div>
													<div class="materail-input-block materail-input-block_danger idk_radio_buttons dokumenti_ne">
														<label class="main-container__column material-radio-group material-radio-group_danger" for="nalog_dokumenti_ne">
															<input type="radio" name="nalog_dokumenti" id="nalog_dokumenti_ne" class="material-radiobox" value="0" checked />
															<span class="material-radio-group__element material-radio-group__check-radio"></span>
															<span class="material-radio-group__element material-radio-group__caption">NE</span>
														</label>
													</div>
												</div> -->
												<!-- <script>
													$('.dokumenti_da').click(function(){
														$('.vrste_dokumenata').css('display', 'block');
													});
													$('.dokumenti_ne').click(function(){
														$('.vrste_dokumenata').css('display', 'none');
														$('#doc_slika_ne').prop("checked", true);
														$('#doc_diploma_ne').prop("checked", true);
														$('#doc_pripravnicki_ne').prop("checked", true);
														$('#doc_strucni_ne').prop("checked", true);
														$('#doc_jezik_cert_ne').prop("checked", true);
													});
												</script> -->
												<!-- <div class="vrste_dokumenata" style="display: none">
													<div class="form-group col-md-12">
														<div class="col-xs-6" style="padding-top:5px; text-align:right;"><?php echo $txt_reg_fotografija; ?>:</div>
														<div class="materail-input-block materail-input-block_success idk_radio_buttons">
															<label class="main-container__column material-radio-group material-radio-group_success" for="doc_slika_da">
																<input type="radio" name="doc_slika" id="doc_slika_da" class="material-radiobox" value="1" />
																<span class="material-radio-group__element material-radio-group__check-radio"></span>
																<span class="material-radio-group__element material-radio-group__caption">DA</span>
															</label>
														</div>
														<div class="materail-input-block materail-input-block_danger idk_radio_buttons">
															<label class="main-container__column material-radio-group material-radio-group_danger" for="doc_slika_ne">
																<input type="radio" name="doc_slika" id="doc_slika_ne" class="material-radiobox" value="0" checked />
																<span class="material-radio-group__element material-radio-group__check-radio"></span>
																<span class="material-radio-group__element material-radio-group__caption">NE</span>
															</label>
														</div>
													</div>
													<div class="form-group col-md-12">
														<div class="col-xs-6" style="padding-top:5px; text-align:right;"><?php echo $txt_reg_koraksedam_diploma; ?>:</div>
														<div class="materail-input-block materail-input-block_success idk_radio_buttons">
															<label class="main-container__column material-radio-group material-radio-group_success" for="doc_diploma_da">
																<input type="radio" name="doc_diploma" id="doc_diploma_da" class="material-radiobox" value="1" />
																<span class="material-radio-group__element material-radio-group__check-radio"></span>
																<span class="material-radio-group__element material-radio-group__caption">DA</span>
															</label>
														</div>
														<div class="materail-input-block materail-input-block_danger idk_radio_buttons">
															<label class="main-container__column material-radio-group material-radio-group_danger" for="doc_diploma_ne">
																<input type="radio" name="doc_diploma" id="doc_diploma_ne" class="material-radiobox" value="0" checked />
																<span class="material-radio-group__element material-radio-group__check-radio"></span>
																<span class="material-radio-group__element material-radio-group__caption">NE</span>
															</label>
														</div>
													</div>
													<div class="form-group col-md-12">
														<div class="col-xs-6" style="padding-top:5px; text-align:right;"><?php echo $txt_reg_koraksedam_uvjerenje_pripravnicki; ?>:</div>
														<div class="materail-input-block materail-input-block_success idk_radio_buttons">
															<label class="main-container__column material-radio-group material-radio-group_success" for="doc_pripravnicki_da">
																<input type="radio" name="doc_pripravnicki" id="doc_pripravnicki_da" class="material-radiobox" value="1" />
																<span class="material-radio-group__element material-radio-group__check-radio"></span>
																<span class="material-radio-group__element material-radio-group__caption">DA</span>
															</label>
														</div>
														<div class="materail-input-block materail-input-block_danger idk_radio_buttons">
															<label class="main-container__column material-radio-group material-radio-group_danger" for="doc_pripravnicki_ne">
																<input type="radio" name="doc_pripravnicki" id="doc_pripravnicki_ne" class="material-radiobox" value="0" checked />
																<span class="material-radio-group__element material-radio-group__check-radio"></span>
																<span class="material-radio-group__element material-radio-group__caption">NE</span>
															</label>
														</div>
													</div>
													<div class="form-group col-md-12">
														<div class="col-xs-6" style="padding-top:5px; text-align:right;"><?php echo $txt_reg_koraksedam_uvjerenje_strucni; ?>:</div>
														<div class="materail-input-block materail-input-block_success idk_radio_buttons">
															<label class="main-container__column material-radio-group material-radio-group_success" for="doc_strucni_da">
																<input type="radio" name="doc_strucni" id="doc_strucni_da" class="material-radiobox" value="1" />
																<span class="material-radio-group__element material-radio-group__check-radio"></span>
																<span class="material-radio-group__element material-radio-group__caption">DA</span>
															</label>
														</div>
														<div class="materail-input-block materail-input-block_danger idk_radio_buttons">
															<label class="main-container__column material-radio-group material-radio-group_danger" for="doc_strucni_ne">
																<input type="radio" name="doc_strucni" id="doc_strucni_ne" class="material-radiobox" value="0" checked />
																<span class="material-radio-group__element material-radio-group__check-radio"></span>
																<span class="material-radio-group__element material-radio-group__caption">NE</span>
															</label>
														</div>
													</div>
													<div class="form-group col-md-12">
														<div class="col-xs-6" style="padding-top:5px; text-align:right;"><?php echo $txt_reg_koraksedam_certifikat_jezik; ?>:</div>
														<div class="materail-input-block materail-input-block_success idk_radio_buttons">
															<label class="main-container__column material-radio-group material-radio-group_success" for="doc_jezik_cert_da">
																<input type="radio" name="doc_jezik_cert" id="doc_jezik_cert_da" class="material-radiobox" value="1" />
																<span class="material-radio-group__element material-radio-group__check-radio"></span>
																<span class="material-radio-group__element material-radio-group__caption">DA</span>
															</label>
														</div>
														<div class="materail-input-block materail-input-block_danger idk_radio_buttons">
															<label class="main-container__column material-radio-group material-radio-group_danger" for="doc_jezik_cert_ne">
																<input type="radio" name="doc_jezik_cert" id="doc_jezik_cert_ne" class="material-radiobox" value="0" checked />
																<span class="material-radio-group__element material-radio-group__check-radio"></span>
																<span class="material-radio-group__element material-radio-group__caption">NE</span>
															</label>
														</div>
													</div>
												</div> -->
												
												<br />
												<div class="form-group">
													<div class="col-sm-offset-2 col-sm-6 text-right">
														<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span>Spremi</span></button>
														<br /><small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
													</div>
												</div>
												
											</form>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?php
					}else{
						echo '
							<div class="alert material-alert material-alert_danger">
								<h4>NEMATE PRIVILEGIJE!</h4>
								<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
								<br />
								<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
							</div>
						';
					}
				
				break;
				case "list_arhivirani_nalozi":
				
				?>
					<div class="row">
						<div class="col-xs-8">
							<h1><i class="fa fa-file-o idk_color_green" aria-hidden="true"></i> Nalozi</h1>
						</div>
						<div class="col-xs-12">
							<hr />
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="content_box">
								<div class="row">
									<div class="col-xs-12">
										<?php
											if(isset($_GET['mess'])) {
												$mess = $_GET['mess'];
											}else{
												$mess = 0;
											}

											if($mess == 1){
												echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novu kompaniju.</div>';
											}elseif($mess == 3){
												echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali profil kompanije.</div>';
											}
										?>
										<script type="text/javascript">
											$(document).ready(function() {
												$('#idk_table').DataTable({

													responsive: true,

													"order": [[ 1, "asc" ]],

													 "bAutoWidth": false,

													"aoColumns": [
															{ "width": "5%", "bSortable": false },
															{ "width": "10%" },
															{ "width": "20%" },
															{ "width": "25%" },
															{ "width": "15%" },
															{ "width": "15%" },
															{ "width": "10%", "bSortable": false }
														]
												});
											} );
										</script>
										<table id="idk_table" class="display" cellspacing="0" width="100%">
											<thead>
												<tr>
													<th class="text-center"></th>
													<th class="text-center">Broj</th>
													<th class="text-center">Naziv</th>
													<th class="text-center">Kompanija</th>
													<th class="text-center">Status</th>
													<th class="text-center">Kreirano</th>
													<th></th>
												</tr>
											</thead>
											<tbody>
												<?php
													$query = $db->prepare("
															SELECT nalog_id, kompanija_id, nalog_broj, nalog_naziv, nalog_opis, nalog_kreirano, nalog_status, comp.company_name
															FROM idk_nalozi
															INNER JOIN idk_companies comp ON kompanija_id = comp.company_id
															WHERE nalog_status = :nalog_status");

													$query->execute(array(':nalog_status' => 12));

													while($row = $query->fetch()){

														$nalog_id = $row['nalog_id'];
														$nalog_broj = $row['nalog_broj'];
														$kompanija_id = $row['kompanija_id'];
														$nalog_naziv = $row['nalog_naziv'];
														$kompanija = $row['company_name'];
														$nalog_status = $row['nalog_status'];
														$nalog_kreirano = date('d.m.Y.', strtotime($row['nalog_kreirano']));

														if($nalog_status == 1){
															$nalog_status_txt = '<span class="label label-primary">Potpis</span>';
														}elseif($nalog_status == 2){
															$nalog_status_txt = '<span class="label label-warning">Čeka se uplata</span>';
														}elseif($nalog_status == 3){
															$nalog_status_txt = '<span class="label label-primary">Marketing</span>';
														}elseif($nalog_status == 4){
															$nalog_status_txt = '<span class="label label-warning">Prijave u toku</span>';
														}elseif($nalog_status == 5){
															$nalog_status_txt = '<span class="label label-primary">Obrada prijava</span>';
														}elseif($nalog_status == 6){
															$nalog_status_txt = '<span class="label label-warning">Nalog kod poslodavca</span>';
														}elseif($nalog_status == 7){
															$nalog_status_txt = '<span class="label label-primary">Casting</span>';
														}elseif($nalog_status == 8){
															$nalog_status_txt = '<span class="label label-success">Završeno</span>';
														}elseif($nalog_status == 9){
															$nalog_status_txt = '<span class="label label-danger">Na čekanju</span>';
														}elseif($nalog_status == 12){
															$nalog_status_txt = '<span class="label label-danger">Arhiviran</span>';
														}
														
												?>
												<tr>
													<td class="text-center"><?php echo $nalog_id; ?></td>
													<td class="text-center"><?php echo $nalog_broj; ?></td>
													<td class="text-center"><?php echo $nalog_naziv; ?></td>
													<td class="text-center"><a href="<?php getSiteURL(); ?>companies?page=open&id=<?php echo $kompanija_id; ?>"><?php echo $kompanija; ?></a></td>
													<td class="text-center"><?php echo $nalog_status_txt; ?></td>
													<td class="text-center"><?php echo $nalog_kreirano; ?></td>
													<td class="text-center"><a href="<?php getSiteURL(); ?>nalozi?page=open&id=<?php echo $nalog_id; ?>" class="btn material-btn material-btn_success main-container__column"><i class="fa fa-info-circle" aria-hidden="true"></i></a></td>
												</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php
				
				break;

				case "predefinisani_dokumenti":
				?>
				<div class = "row">
					<div class = "col-xs-6 idk_color_green">
						<h1><i class="fa fa-file" aria-hidden="true" style = "margin-right: 10px;"></i> Predefinisani dokumenti naloga</h1>
					</div>
					<div class = "col-xs-6 text-right idk_margin_top10">
						<!--
							Dodavanje tipova dokumenata - START
						-->
						<a href="" data-toggle="modal" data-target="#addNewTypeDoc" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive">
							<i class="fa fa-plus" aria-hidden="true">
							</i>
							<span>
								Novi tip dokumenta
							</span>
						</a>
						<div class="modal material-modal material-modal_success fade text-left" id="addNewTypeDoc">
							<div class="modal-dialog modal-lg">
								<div class="modal-content material-modal__content">
									<div class="modal-header material-modal__header">
										<button class="close material-modal__close" data-dismiss="modal">&times;</button>
										<h4 class="modal-title material-modal__title"><i style = "margin-right: 10px;" class="fa fa-plus" aria-hidden="true"></i>Dodaj novi tip dokumenta</h4>
									</div> 
									<div class="modal-body material-modal__body">
										<form action="<?php getSiteURL(); ?>do?form=new_pp_doc_type" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id = "form_pp_doc_type">
											<!--
												dt - skraćenica od: document type
											-->
											<div class="form-group">
												<div class="col-md-offset-2 col-sm-8">
													<div class="alert alert-danger text-center" role="alert">
														<i class="fa fa-exclamation-triangle fa-3x" aria-hidden="true"></i>
														<br>
														<strong>
															Prije unosa novog tipa dokumenta, obavezno u tabeli provjerite da li je takav tip dokumenta već dodan.<br>
															Dupliranje istog tipa dokumenta je strogo zabranjeno!
														</strong>
													</div>
												</div>
											</div>
											<div class="form-group">
												<div class="col-md-offset-2 col-sm-8">
													<div class="alert alert-danger text-center" role="alert">
														<img src="<?php getSiteUrlr(); ?>images/Germany.png" width="100">
														<br>
														Unos naziva dokumenta na njemačkom jeziku je obavezan!</br></br>
														<strong>Strogo zabranjen unos neadekvatnih pojmova kako bi se time omogućio unos forme!</strong>
													</div>
												</div>
											</div>
											<div class="form-group">
												<div class="col-md-offset-2 col-sm-8">
													<label for="dt_name" class="col-sm-4 control-label">
														<span class="text-danger">
															*
														</span>
														Naziv <img src="<?php getSiteUrlr(); ?>images/BosniaHerzegowina.png" width="30">
													</label>
													<div class="col-sm-8">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="dt_name" id="dt_name" placeholder="Naziv tipa dokumenta" autocomplete="off" required>
															<span class="materail-input-block__line">
															</span>
														</div>
													</div>
												</div>
											</div>
											<div class="form-group">
												<div class="col-md-offset-2 col-sm-8">
													<label for="dt_name_de" class="col-sm-4 control-label">
														<span class="text-danger">
															*
														</span>
														Naziv <img src="<?php getSiteUrlr(); ?>images/Germany.png" width="30">
													</label>
													<div class="col-sm-8">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="dt_name_de" id="dt_name_de" placeholder="Njemački naziv tipa dokumenta" autocomplete="off" required>
															<span class="materail-input-block__line">
															</span>
														</div>
													</div>
												</div>
											</div>
											<div class="modal-footer material-modal__footer">
												<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
												<button class="btn btn-primary material-btn material-btn_success docTypeSave"><i class="fa fa-check-square-o" aria-hidden="true"></i> Dodaj</button>
											</div>
										</form>
										<script>
											$( ".docTypeSave" ).click(function() {
												if($("#dt_name").val() != "" && $("#dt_name_de").val() != ""){
													$('.docTypeSave').prop('disabled', true);
													console.log("Popunjeno!");
													$( "#form_pp_doc_type" ).submit();
												}else{
													console.log("Nije popunjeno!");
												}
											});
										</script>
									</div>
								</div>
							</div>
						</div>
						<!--
							Dodavanje tipova dokumenata - END
						-->
					</div>
				</div>
				<div class="row" style = "margin-top: 20px;">
					<div class="col-md-12">
						<div class="content_box">
							<!--
							<div class="row">
								<div class="col-xs-12">
									Prostor za neke funkcije
								</div>
							</div>
							-->
							<div class="row">
								<div class="col-xs-12">
									<script type="text/javascript">
										$(document).ready(function() {
											var table_s = $('#doc_types').DataTable({
		
												responsive: true,
												
												"order": [[ 0, "desc" ]],

												"bAutoWidth": false,

												"aoColumns": [
														{ "width": "5%"},
														{ "width": "30%" },
														{ "width": "30%" },
														{ "width": "17.5%" },
														{ "width": "17.5%" }
													],
											});
										});
									</script>
									<table id="doc_types" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th class="text-center">#</th>
												<th class="text-center">Naziv <img src="<?php getSiteUrlr(); ?>images/BosniaHerzegowina.png" width="20"></th>
												<th class="text-center">Naziv <img src="<?php getSiteUrlr(); ?>images/Germany.png" width="20"></th>
												<th class="text-center">Kreirao korisnik</th>
												<th class="text-center">Datum kreiranja</th>
											</tr>
										</thead>
										<tbody>
											<?php 
												$queryListTypes = $db->prepare("
													SELECT 
														*
													FROM 
														idk_pp_document_types
												");
												$queryListTypes->execute();
												if($queryListTypes->rowCount() != 0){
													while($rowListTypes = $queryListTypes->fetch()){
														$docTypeId 				= intval($rowListTypes["doc_type_id"]);
														$docTypeName 			= $rowListTypes["doc_type_name"];
														$docTypeNameDe 			= $rowListTypes["doc_type_name_de"];
														$docTypeEnteredUser 	= getZaposlenikimeR($rowListTypes["doc_type_entered_user"]);
														$docTypeEnteredDate 	= date("d.m.Y H:i:s", strtotime($rowListTypes["doc_type_date"]));

														echo '
															<tr>
																<td class="text-center">'.$docTypeId.'</td>
																<td class="text-center">'.$docTypeName.'</td>
																<td class="text-center">'.$docTypeNameDe.'</td>
																<td class="text-center">'.$docTypeEnteredUser.'</td>
																<td class="text-center" data-order="'.strtotime($docTypeEnteredDate).'">'.$docTypeEnteredDate.'</td>
															</tr>
														';
													}
												}
											?> 
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
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