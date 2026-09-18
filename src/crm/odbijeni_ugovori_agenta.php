<?php
include("includes/functions.php");
include("includes/common.php");

if(isset($_REQUEST["page"])) {
	$page = $_REQUEST["page"];
}else{
	exit();
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php getTitle(); ?></title>

	<?php include('includes/head.php'); ?>

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
				$trenutnoVrijeme = date("Y-m-d H:i:s");
				$uslov_odbijeni_ugovori = "";
				if(in_array("1" , $employee_status) AND ($logged_employee_id == 75 OR $logged_employee_id == 158)){
					$uslov_odbijeni_ugovori = "ug.ug_zaposlenik_id is not null";
					$naslov_odbijeni_ugovori = "Odbijeni ugovori za sve agente";
				}else{
					$uslov_odbijeni_ugovori = "ug.ug_zaposlenik_id = ".$logged_employee_id."";
					$naslov_odbijeni_ugovori = "Moji odbijeni ugovori";
				}
		?>
					<style>
						.width100{
							width: 100%;
						}
					</style>
					<div class = "row">
						<div class = "col-xs-12 idk_color_green">
							<h1>
								<i class="fa fa-times idk_color_green" aria-hidden="true"></i> <?php echo $naslov_odbijeni_ugovori; ?>
							</h1>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-md-12">
							<div class="content_box">	
								<?php
									$query_get_odbijeni_ugovori = $db -> prepare("
										SELECT 
											kan.id_broj_nd_kandidata, 
											kan.ime_nd_kandidata, 
											kan.prezime_nd_kandidata, 
											kan.status_nd_kandidata, 
											kan.pstatus_nd_kandidata, 
											ug.ug_razlog_odbijanja, 
											ug.ug_vrsta, 
											ro.naziv_ro_bs, 
											ug.ug_komentar, 
											ug.ug_datum_odbijanja, 
											ug.ug_id,
											DATEDIFF('".$trenutnoVrijeme."', ug.ug_datum_odbijanja) AS danaOdbijen
										FROM idk_reminders rem
										JOIN idk_nd_ugovori ug
										ON ug.ug_id = rem.reminder_foreign_key
										JOIN idk_nd_kandidata kan
										ON kan.id_broj_nd_kandidata = ug.ug_kandidat_id
										LEFT JOIN idk_ro_usluge ro
										ON ro.id_ro = ug.ug_razlog_odbijanja
										WHERE rem.reminder_status = 1
										AND ".$uslov_odbijeni_ugovori." 
										AND rem.reminder_type = 2
									");
									$query_get_odbijeni_ugovori -> execute();
								?>
								<script>
									$(document).ready(function() { 
										var table = $('#table_odbijeni_ugovori').DataTable({
											responsive: true,
											"bAutoWidth": false,
											"aoColumns": [
												{ "width": "5%"},
												{ "width": "15%" },
												{ "width": "10%", "bSortable": false },
												{ "width": "15%" },
												{ "width": "10%" },
												{ "width": "10%" },
												{ "width": "35%" }
											]
										});
									});
								</script>
								<table id="table_odbijeni_ugovori" class="striped text-center">
									<thead>
										<th class="text-center">ID</th>
										<th class="text-center">Ime i prezime</th>
										<th class="text-center">Status</th>
										<th class="text-center">Vrsta ugovora</th>
										<th class="text-center">Datum odbijanja</th>
										<th class="text-center">Dana</th>
										<th class="text-center">Razlog odbijanja</th>
									</thead>
									<tbody>
										<?php
											while($row_get_odbijeni_ugovori = $query_get_odbijeni_ugovori->fetch()){

												$kandidat_full_name 		= $row_get_odbijeni_ugovori['ime_nd_kandidata'].' '.$row_get_odbijeni_ugovori['prezime_nd_kandidata'];
												$naziv_ro_bs 				= $row_get_odbijeni_ugovori['naziv_ro_bs'];
												$ug_komentar 				= $row_get_odbijeni_ugovori['ug_komentar'];
												$ug_datum_odbijanja 		= $row_get_odbijeni_ugovori['ug_datum_odbijanja'];
												$ug_dana_odbijen 			= $row_get_odbijeni_ugovori['danaOdbijen'];
												$ug_razlog_odbijanja 		= $row_get_odbijeni_ugovori['ug_razlog_odbijanja'];
												$ug_vrsta 					= $row_get_odbijeni_ugovori['ug_vrsta'];
												$status_nd_kandidata 		= $row_get_odbijeni_ugovori['status_nd_kandidata'];
												$pstatus_nd_kandidata 		= $row_get_odbijeni_ugovori['pstatus_nd_kandidata'];
												$kandidat_id 				= $row_get_odbijeni_ugovori['id_broj_nd_kandidata'];
												if(is_null($ug_razlog_odbijanja))
													$razlog_odbijanja = 	"Nije naveden";
												else if($ug_razlog_odbijanja != 13){
													$razlog_odbijanja = 	$naziv_ro_bs;
												}else{
													$razlog_odbijanja = 	$ug_komentar;
												}
												
												if(!is_null($ug_vrsta)){
													$ug_vrsta_ispis = '<span class="width100 label label-success material-label material-label_success main-container__column text-center">'.getVrstaUgovoraDiplR($ug_vrsta).'</span>';
												}else{
													$ug_vrsta_ispis = '<span class="width100 label label-danger material-label material-label_danger main-container__column text-center">Nije naveden</span>';
												}
										?>
											<tr>
												<td data-order="<?php echo $kandidat_id; ?>"> 
													<?php echo $kandidat_id; ?> 
												</td>
												<td data-order="<?php echo $kandidat_full_name; ?>">
													<a href = "<?php getSiteURL(); ?>/nostrifikacija_diploma?page=otvori_ND_kandidata&id=<?php echo $kandidat_id;?>">
														<?php echo $kandidat_full_name; ?>
													</a>
												</td>
												<td>
													<?php echo getStatusDIPLKandidatR($status_nd_kandidata, $pstatus_nd_kandidata); ?>
												</td>
												<td data-order="<?php echo $ug_vrsta; ?>">
													<?php echo $ug_vrsta_ispis; ?>
												</td>
												<td data-order="<?php echo $ug_datum_odbijanja; ?>">
													<span class="width100 label label-warning material-label material-label_warning main-container__column text-center">
														<?php echo date('d.m.Y',strtotime($ug_datum_odbijanja)); ?>
													</span>
												</td>
												<td data-order="<?php echo $ug_dana_odbijen; ?>">
													<span class="width100 label label-warning material-label material-label_warning main-container__column text-center">
														<?php echo $ug_dana_odbijen; ?>
													</span>
												</td>
												<td data-order="<?php echo $razlog_odbijanja; ?>">
													<span class="width100 label label-default material-label material-label_default main-container__column text-left">
														<?php echo $razlog_odbijanja; ?>
													</span>
												</td>
											</tr>
										<?php
											}
										?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
		<?php
				break;
				
				case "otvoren_link":
				$trenutnoVrijeme = date("Y-m-d H:i:s");
				$uslov_otvoren_link = "";
				if(in_array("1" , $employee_status) AND ($logged_employee_id == 75 OR $logged_employee_id == 158 OR $logged_employee_id == 173)){
					$uslov_otvoren_link = "ug.ug_zaposlenik_id is not null";
					$naslov_otvoren_link = "Ugovori sa statusom Otvoren link duže od 2 sata";
				}else{
					$uslov_otvoren_link = "ug.ug_zaposlenik_id = ".$logged_employee_id."";
					$naslov_otvoren_link = "Moji ugovori sa statusom Otvoren link duže od 2 sata";
				}
		?>
					<style>
						.width100{
							width: 100%;
						}
					</style>
					<div class = "row">
						<div class = "col-xs-12 idk_color_green">
							<h1>
								<i class="fa fa-times idk_color_green" aria-hidden="true"></i> <?php echo $naslov_otvoren_link; ?>
							</h1>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-md-12">
							<div class="content_box">	
								<?php
									$query_get_otvoreni_ugovori = $db -> prepare("
										SELECT 
											kan.id_broj_nd_kandidata, 
											kan.ime_nd_kandidata, 
											kan.prezime_nd_kandidata, 
											kan.status_nd_kandidata, 
											kan.pstatus_nd_kandidata,
											ug.ug_vrsta, 
											ug.ug_status,
											ug.ug_datum_otvaranja_linka, 
											ug.ug_id,
											HOUR(TIMEDIFF('".$trenutnoVrijeme."', ug.ug_datum_otvaranja_linka)) AS satiOtvoren
										FROM 
											idk_nd_ugovori ug
										JOIN 
											idk_nd_kandidata kan
										ON 
											kan.id_broj_nd_kandidata = ug.ug_kandidat_id
										WHERE 
											ug.ug_status = 4
											AND 
											HOUR(TIMEDIFF('".$trenutnoVrijeme."', ug.ug_datum_otvaranja_linka)) >= 2
											AND 
											".$uslov_otvoren_link."
									");
									$query_get_otvoreni_ugovori -> execute();
								?>
								<script>
									$(document).ready(function() { 
										var table = $('#table_odbijeni_ugovori').DataTable({
											responsive: true,
											"bAutoWidth": false,
											"aoColumns": [
												{ "width": "5%"},
												{ "width": "25%" },
												{ "width": "15%", "bSortable": false },
												{ "width": "15%", "bSortable": false },
												{ "width": "20%" },
												{ "width": "10%" },
												{ "width": "10%" }
											]
										});
									});
								</script>
								<table id="table_odbijeni_ugovori" class="striped text-center">
									<thead>
										<th class="text-center">ID</th>
										<th class="text-center">Ime i prezime</th>
										<th class="text-center">Status</th>
										<th class="text-center">Status ugovora</th>
										<th class="text-center">Vrsta ugovora</th>
										<th class="text-center">Datum otvaranja</th>
										<th class="text-center">Sati otvoren</th>
									</thead>
									<tbody>
										<?php
											while($row_get_otvoreni_ugovori = $query_get_otvoreni_ugovori->fetch()){

												$kandidat_full_name 		= $row_get_otvoreni_ugovori['ime_nd_kandidata'].' '.$row_get_otvoreni_ugovori['prezime_nd_kandidata'];
												$ug_datum_otvaranja 		= $row_get_otvoreni_ugovori['ug_datum_otvaranja_linka'];
												$ug_sati_otvoren			= $row_get_otvoreni_ugovori['satiOtvoren'];
												$ug_vrsta 					= $row_get_otvoreni_ugovori['ug_vrsta'];
												$ug_status 					= $row_get_otvoreni_ugovori['ug_status'];
												$status_nd_kandidata 		= $row_get_otvoreni_ugovori['status_nd_kandidata'];
												$pstatus_nd_kandidata 		= $row_get_otvoreni_ugovori['pstatus_nd_kandidata'];
												$kandidat_id 				= $row_get_otvoreni_ugovori['id_broj_nd_kandidata'];
												if($ug_status == 0){
													$ug_status = '<span class="label label-danger material-label material-label_danger main-container__column text-left">Arhiviran</span>';
												}else if($ug_status == 1){
													$ug_status = '<span class="label label-primary material-label material-label_primary main-container__column text-left">Poslan</span>';
												}else if($ug_status == 4){
													$ug_status = '<span class="label label-info material-label material-label_info main-container__column text-left">Otvoren Link</span>';
												}else if($ug_status == 2){
													$ug_status = '<span class="label label-success material-label material-label_success main-container__column text-left">Prihvaćen</span>';
												}else{
													$ug_status = '<span class="label label-danger material-label material-label_danger main-container__column text-left">Odbijen</span>';
												}
												
												if(!is_null($ug_vrsta)){
													$ug_vrsta_ispis = '<span class="width100 label label-success material-label material-label_success main-container__column text-center">'.getVrstaUgovoraDiplR($ug_vrsta).'</span>';
												}else{
													$ug_vrsta_ispis = '<span class="width100 label label-danger material-label material-label_danger main-container__column text-center">Nije naveden</span>';
												}
										?>
											<tr>
												<td data-order="<?php echo $kandidat_id; ?>"> 
													<?php echo $kandidat_id; ?> 
												</td>
												<td data-order="<?php echo $kandidat_full_name; ?>">
													<a href = "<?php getSiteURL(); ?>/nostrifikacija_diploma?page=otvori_ND_kandidata&id=<?php echo $kandidat_id;?>">
														<?php echo $kandidat_full_name; ?>
													</a>
												</td>
												<td>
													<?php echo getStatusDIPLKandidatR($status_nd_kandidata, $pstatus_nd_kandidata); ?>
												</td>
												<td>
													<?php echo $ug_status; ?>
												</td>
												<td data-order="<?php echo $ug_vrsta; ?>">
													<?php echo $ug_vrsta_ispis; ?>
												</td>
												<td data-order="<?php echo $ug_datum_otvaranja; ?>">
													<span class="width100 label label-warning material-label material-label_warning main-container__column text-center">
														<?php echo date('d.m.Y',strtotime($ug_datum_otvaranja)); ?>
													</span>
												</td>
												<td data-order="<?php echo $ug_sati_otvoren; ?>">
													<span class="width100 label label-warning material-label material-label_warning main-container__column text-center">
														<?php echo $ug_sati_otvoren; ?>
													</span>
												</td>
											</tr>
										<?php
											}
										?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
		<?php
				break;
				
				case "povezi_ustanovom":
					
					if(in_array($logged_employee_id, array(158,92,43,212,90,369,231))){
						$uslovUstanova = "";
						$naslovUstanova = "";
						if(in_array($logged_employee_id, array(92,43,212,369,231))){
							$uslovUstanova = "(pred.pr_domaca_valuta LIKE 'EUR' OR  pred.pr_domaca_valuta LIKE 'BAM')";
							$naslovUstanova = "Poveži sa ustanovom - moja zaduženja";
						}else if($logged_employee_id == 90){
							$uslovUstanova = "( pred.pr_domaca_valuta LIKE 'RSD' )";
							$naslovUstanova = "Poveži sa ustanovom - moja zaduženja";
						}else{
							$uslovUstanova = "(pred.pr_domaca_valuta LIKE 'EUR' OR  pred.pr_domaca_valuta LIKE 'BAM' OR pred.pr_domaca_valuta LIKE 'RSD')";
							$naslovUstanova = "Poveži sa ustanovom - svi kandidati";
						}
		?>
					<style>
						.width100{
							width: 100%;
						}
					</style>
					<div class = "row">
						<div class = "col-xs-12 idk_color_green">
							<h1>
								<i class="fa fa-university idk_color_green" aria-hidden="true"></i> <?php echo $naslovUstanova; ?>
							</h1>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-md-12">
							<div class="content_box">
		<?php 
						$dipl_notif = $db->prepare("
							SELECT
								kan.id_broj_nd_kandidata,
								kan.ime_nd_kandidata, 
								kan.prezime_nd_kandidata, 
								kan.status_nd_kandidata, 
								kan.pstatus_nd_kandidata,
								kan.mobilni_nd_kandidata,
								kan.email_nd_kandidata,
								pred.pr_domaca_valuta
							FROM 
								idk_nd_kandidata kan
							INNER JOIN 
								idk_predracuni pred
							ON 
								kan.id_broj_nd_kandidata = pred.pr_kandidat_id
							WHERE 
								kan.status_nd_kandidata = 2 
								AND 
								kan.idd_ustanova_nd is null
								AND 
								pred.pr_rata = 1
								AND 
								pred.pr_status = 2
								AND 
								pred.pr_datum_uplate is not null
								AND 
								".$uslovUstanova."
							ORDER BY 
								kan.id_broj_nd_kandidata ASC
						");
						$dipl_notif->execute();
						$broj_dipl_notif = $dipl_notif->rowCount();
		?>
							<script>
								$(document).ready(function() { 
									var table = $('#table_povezi_ustanovom').DataTable({
										responsive: true,
										"bAutoWidth": false,
										"aoColumns": [
											{ "width": "5%"},
											{ "width": "25%" },
											{ "width": "20%", "bSortable": false },
											{ "width": "10%", "bSortable": false },
											{ "width": "20%" },
											{ "width": "20%" }
										]
									});
								});
							</script>
							<table id="table_povezi_ustanovom" class="striped text-center">
								<thead>
									<th class="text-center">ID</th>
									<th class="text-center">Ime i prezime</th>
									<th class="text-center">Status</th>
									<th class="text-center">Država</th>
									<th class="text-center">Kontakt broj</th>
									<th class="text-center">Kontakt Email</th>
								</thead>
								<tbody>
		<?php
									while($row_dipl_notif = $dipl_notif->fetch()){
										$kandidat_full_name 		= $row_dipl_notif['ime_nd_kandidata'].' '.$row_dipl_notif['prezime_nd_kandidata'];
										$status_nd_kandidata 		= $row_dipl_notif['status_nd_kandidata'];
										$pstatus_nd_kandidata 		= $row_dipl_notif['pstatus_nd_kandidata'];
										$kandidat_id 				= intval($row_dipl_notif['id_broj_nd_kandidata']);
										$kandidat_mobitel			= $row_dipl_notif['mobilni_nd_kandidata'];
										$kandidat_email				= $row_dipl_notif['email_nd_kandidata'];
										$predracun_valuta			= $row_dipl_notif['pr_domaca_valuta'];
										$kandidat_drzava 			= "";
										if($predracun_valuta == "RSD"){
											$kandidat_drzava = '<img src="'.getSiteUrlr().'images/sr3d.png" width=20>';
										}else if($predracun_valuta == "BAM"){
											$kandidat_drzava = '<img src="'.getSiteUrlr().'images/bs3d.png" width=20>';
										}else if($predracun_valuta == "EUR"){
											$kandidat_drzava = '<img src="'.getSiteUrlr().'images/de3d.png" width=20>';
										}else{
											$kandidat_drzava = '<img src="'.getSiteUrlr().'images/globe3d.png" width=20>';
										}
		?>
										<tr>
											<td data-order="<?php echo $kandidat_id; ?>"> 
												<?php echo $kandidat_id; ?> 
											</td>
											<td data-order="<?php echo $kandidat_full_name; ?>">
												<a href = "<?php getSiteURL(); ?>/nostrifikacija_diploma?page=otvori_ND_kandidata&id=<?php echo $kandidat_id;?>">
													<?php echo $kandidat_full_name; ?>
												</a>
											</td>
											<td>
												<?php echo getStatusDIPLKandidatR($status_nd_kandidata, $pstatus_nd_kandidata); ?>
											</td>
											<td>
												<?php echo $kandidat_drzava; ?>
											</td>
											<td data-order="<?php echo $kandidat_mobitel; ?>">
												<span class="width100 label label-default material-label material-label_default main-container__column text-center">
													<?php echo $kandidat_mobitel; ?>
												</span>
											</td>
											<td data-order="<?php echo $kandidat_email; ?>">
												<span class="width100 label label-default material-label material-label_default main-container__column text-center">
													<?php echo $kandidat_email; ?>
												</span>
											</td>
										</tr>
		<?php 
									}
		?>
								</tbody>
							</table>
		<?php 
					}else{
						echo '
							<br/>
							<div class="alert material-alert material-alert_danger">
								<h4>
									NEMATE PRIVILEGIJE!
								</h4>
								<p>
									Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.
								</p>
								<br />
							</div>
						';	
					}
		?>
							</div>
						</div>
					</div>
		<?php 			
				break;
			}
		?>
	</div>
</body>