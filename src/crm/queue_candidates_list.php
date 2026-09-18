<?php
	// var_dump('radi dovle');
	// exit();
// ini_set('display_errors', 1); 
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus = explode( ',' , getEmployeeStatus());

?><!DOCTYPE html>
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

				<div class="row">
					<div class="col-xs-12">
						<?php
						$nalog_id = $_GET['nalog_id'];
						$query_get_company_nalog = $db -> prepare('
							SELECT c.company_name, n.nalog_naziv
							FROM idk_nalozi n
							JOIN idk_companies c
							ON c.company_id = n.kompanija_id
							WHERE n.nalog_id = :nalog_id
						');
						$query_get_company_nalog -> execute(array(':nalog_id' => $nalog_id));
						$row_get_company_nalog 	= $query_get_company_nalog -> fetch();
						
						$company_name 			= $row_get_company_nalog['company_name'];
						$nalog_name 			= $row_get_company_nalog['nalog_naziv'];
						?>
						<h1><i class="fa fa-file-o idk_color_green" aria-hidden="true"></i> <?php echo $nalog_name; ?> | <i class="fa fa-briefcase idk_color_green" aria-hidden="true"></i> <?php echo $company_name; ?></h1>
						
							
					</div>
					<div class="col-xs-12">
						<hr/>
					</div>
				</div>
				<div class="content_box" style="min-height: 100px;">
					<div class="row">
						<div class="col-lg-12">
							<div class="col-lg-10">
								<h1><i class="fa fa-clock-o idk_color_green" aria-hidden="true"></i> Lista kandidata iz reda čekanja</h1>
								<hr>
							</div>
						</div>
					</div>
					<table id = "table_queue_candidates" class = "table-striped">
						<thead>
							<th></th>
							<th>Ime i prezime</th>
							<th>Grupa</th>
							<th>Njemački</th>
							<th>Status</th>
						</thead>
						<tbody>
							<?php
							$query_kriterij_jezik = $db->prepare("SELECT nbp_njemacki_jezik FROM idk_nalozi_blokovi_prijave WHERE nbp_nalogid = $nalog_id");
							$query_kriterij_jezik->execute();
							if($query_kriterij_jezik->rowCount() > 0){
								$row_kriterij_jezik = $query_kriterij_jezik->fetch();
								$kriterij_jezik = $row_kriterij_jezik['nbp_njemacki_jezik'];
								switch($kriterij_jezik){
									case "A1":
										$njem_uslov = "C1,C2,B2,B1,A2,A1";
									break;
									case "A2":
										$njem_uslov = "C1,C2,B2,B1,A2";
									break;
									case "B1":
										$njem_uslov = "C1,C2,B2,B1";
									break;
									case "B2":
										$njem_uslov = "C1,C2,B2";
									break;
									case "C1":
										$njem_uslov = "C1,C2";
									break;
									case "C2":
										$njem_uslov = "C2";
									break;
									default:
										$njem_uslov = "";
								}
							}else{
								$njem_uslov = "C1,C2,B2,B1,A2,A1";
							}
							$njem_uslov_niz = explode(',',$njem_uslov);
							
							$query_get_table_queue_candidates = $db -> prepare("
								SELECT k.kandidat_id, k.kandidat_ime, k.kandidat_prezime, kj.kj_slusanje, k.kandidat_status_prijave, kg.kg_title, q.queue_entry_date, kj.kj_id
								FROM idk_kandidati k
								JOIN idk_project_candidate_queue q
								ON q.queue_candidate_id = k.kandidat_id
								JOIN idk_projects p
								ON p.project_id = q.queue_project_id
								JOIN (
									SELECT sqkj.kj_kandidatid, sqkj.kj_slusanje, sqkj.kj_id
									FROM idk_kandidat_jezici sqkj
                                    WHERE sqkj.kj_id IN (
                                        SELECT max(sq2kj.kj_id)
                                        FROM idk_kandidat_jezici sq2kj
                                        WHERE sq2kj.kj_naziv LIKE ('%Njemacki%')
                                        GROUP BY sq2kj.kj_kandidatid
                                    )
								) kj
								ON kj.kj_kandidatid = k.kandidat_id
								JOIN idk_kandidati_grupe kg
								ON kg.kg_id = k.kandidat_group
								WHERE p.project_nalogid = :nalog_id
								AND q.queue_is_assigned = 0
							");
							
							$query_get_table_queue_candidates -> execute(array(':nalog_id' => $nalog_id));

							while($row_get_table_queue_candidates = $query_get_table_queue_candidates->fetch()){
								
								$kandidat_prezime 	= $row_get_table_queue_candidates['kandidat_prezime'];
								$queue_entry_date 	= $row_get_table_queue_candidates['queue_entry_date'];
								$status_prijave_kandidata_ispis_logova 	= $row_get_table_queue_candidates['kandidat_status_prijave'];
								$kandidat_ime 		= $row_get_table_queue_candidates['kandidat_ime'];
								$kandidat_id 		= $row_get_table_queue_candidates['kandidat_id'];
								$kj_slusanje 		= $row_get_table_queue_candidates['kj_slusanje'];
								$kg_title 			= $row_get_table_queue_candidates['kg_title'];
								
								$njem_ispis = "";
								
								if($kj_slusanje == ""){
									$njem_ispis = "";
								}
								else if(in_array($kj_slusanje, $njem_uslov_niz)){
									$njem_ispis = '<span class="label label-success material-label material-label_success material-label_xs main-container__column">'.$kj_slusanje.'</span>';
								}else{
									$njem_ispis = '<span class="label label-danger material-label material-label_danger material-label_xs main-container__column">'.$kj_slusanje.'</span>';
								}
								
								if($status_prijave_kandidata_ispis_logova == 1){
									$status_prijave_kandidata_ispis_logova1 = '<span class="label label-default material-label material-label_default main-container__column text-left">Slobodan</span>';
								}
								else if($status_prijave_kandidata_ispis_logova == 2){
									$status_prijave_kandidata_ispis_logova1 = '<span class="label label-warning material-label material-label_warning main-container__column text-left">Projekt NR</span>';
								}
								else if($status_prijave_kandidata_ispis_logova == 3){ 
									$status_prijave_kandidata_ispis_logova1 = '<span class="label label-info material-label material-label_info main-container__column text-left">Casting</span>';
								}
								else if($status_prijave_kandidata_ispis_logova == 4){
									$status_prijave_kandidata_ispis_logova1 = '<span class="label label-success material-label material-label_success main-container__column text-left">Završen</span>';
								}
								else if($status_prijave_kandidata_ispis_logova == 5){
									$status_prijave_kandidata_ispis_logova1 = '<span class="label label-danger material-label material-label_danger main-container__column text-left">Odbijen</span>';
								}
								else if($status_prijave_kandidata_ispis_logova == 6){
									$status_prijave_kandidata_ispis_logova1 = '<span class="label label-default material-label material-label_default main-container__column text-left" style="background-color: #B60606; color: white;">U projektu RZ</span>';
								}
								else if($status_prijave_kandidata_ispis_logova == 7){
									$status_prijave_kandidata_ispis_logova1 = '<span class="label label-default material-label material-label_default main-container__column text-left" style="background-color: #E2E241; color: white;">Čeka ugovor</span>';
								}
								else if($status_prijave_kandidata_ispis_logova == 8){
									$status_prijave_kandidata_ispis_logova1 = '<span class="label label-primary material-label material-label_primary main-container__column text-left">Poslan ugovor</span>';
								}
								else if($status_prijave_kandidata_ispis_logova == 9){
									$status_prijave_kandidata_ispis_logova1 = '<span class="label label-danger material-label main-container__column text-left" style="background-color: #66FFB2; color: black;">Potpisan ugovor</span>';
								}
								else if($status_prijave_kandidata_ispis_logova == 10){
									$status_prijave_kandidata_ispis_logova1 = '<span class="label label-light material-label material-label_light main-container__column text-left" style="background-color: #33FF33; color: white;">Početak rada</span>';
								}
								else if($status_prijave_kandidata_ispis_logova == 12){
									$status_prijave_kandidata_ispis_logova1 = '<span class="label label-light material-label main-container__column text-left" style="background-color: #E9EFC0; color: black;">Prikupljanje dokumentacije</span>';
								}
								else if($status_prijave_kandidata_ispis_logova == 15){
									$status_prijave_kandidata_ispis_logova1 = '<span class="label label-light material-label main-container__column text-left" style="background-color: #B4E197; color: black;">Čeka termin</span>';
								}
								else if($status_prijave_kandidata_ispis_logova == 18){
									$status_prijave_kandidata_ispis_logova1 = '<span class="label label-light material-label main-container__column text-left" style="background-color: #83BD75; color: black;">Čeka vizu</span>';
								}
								else if($status_prijave_kandidata_ispis_logova == 27){
									$status_prijave_kandidata_ispis_logova1 = '<span class="label label-light material-label main-container__column text-left" style="background-color: #4E944F; color: black;">Dobio vizu</span>';
								}
								else if($status_prijave_kandidata_ispis_logova == 21){
									$status_prijave_kandidata_ispis_logova1 = '<span class="label label-light material-label main-container__column text-left" style="background-color: #FFC3C3; color: black;">Dopuna dokumenata</span>';
								}
								else if($status_prijave_kandidata_ispis_logova == 24){
									$status_prijave_kandidata_ispis_logova1 = '<span class="label label-light material-label main-container__column text-left" style="background-color: #FF8C8C; color: black;">Odbijena viza</span>';
								}
								
								?>
								<tr>
									<td><?php echo $kandidat_id;?></td>
									<td><a href="<?php getSiteURL(); ?>kandidati?page=open&id=<?php echo $kandidat_id; ?>"><?php echo $kandidat_ime.' '.$kandidat_prezime?></a></td>
									<td><?php echo $kg_title; ?></td>
									<td><?php echo $njem_ispis;?></td>
									<td><?php echo $status_prijave_kandidata_ispis_logova1;?></td>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
					<script>
					$(document).ready(function() { 
							var table = $('#table_queue_candidates').DataTable({
							responsive: true,
							"order": [[ 1, "desc" ]],
							paging: true,
							pageLength: 10,
							"bAutoWidth": false
						});
					});
					</script>
				</div>	
			</div>
		</div>
	</body>
</html>
