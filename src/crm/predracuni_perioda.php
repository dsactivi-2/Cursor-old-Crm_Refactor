<?php
	
	include("includes/functions.php");
	include("includes/common.php");

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php getTitle(); ?></title>

	<?php include('includes/head.php'); 
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
			<div class="row">
				<div class="col-xs-12">
					<h1><i class="fa fa-cogs idk_color_green" aria-hidden="true"></i> Lista predračuna</h1>
				</div>
				<div class="col-xs-12">
					<hr/>
				</div>
			</div>
			<?php
				$employee_id = $_REQUEST['employee_id'];
				$period_od = $_REQUEST['period_od'];
				$period_do = $_REQUEST['period_do'];
				$tip = $_REQUEST['tip']; // 1 - Prodano ovaj mjesec bez uplacenih, 
										 // 2 - Uplaceno i prodano ovaj mjesec ispisano bez neuplacenih, 
										 // 3 - Uplaceno i prodano ovaj mjesec, sve ispisano, 
										 // 4 - Uplaceno ovaj mjesec kad god da je kreirano, sve ispisano,
				
				$uslov_employee = 'AND pred.pr_zaposlenik = '.$employee_id;
				if($tip == 1){
					$uslov_period = "AND pred.pr_datum_kreiranja BETWEEN '".$period_od."' AND '".$period_do."'
									 AND pred.pr_uplaceno = 0";
									 
					$text_naslov = "<b>Izdani predračuni</b> u periodu od <b>".date("d.m.Y",strtotime($period_od))."</b> do <b>".date("d.m.Y",strtotime($period_do))."</b>";
				}
				else if($tip == 2){
					$uslov_period = "AND pred.pr_datum_kreiranja BETWEEN '".$period_od."' AND '".$period_do."' 
									 AND pred.pr_datum_uplate BETWEEN  '".$period_od."' AND '".$period_do."'
									 AND pred.pr_uplaceno = 1"; 
									 
					$text_naslov = "<b>Uplaćeni</b> predračuni u periodu od <b>".date("d.m.Y",strtotime($period_od))."</b> do <b>".date("d.m.Y",strtotime($period_do))."</b>";
				}
				else if($tip == 3){
					$uslov_period = "AND (pred.pr_datum_kreiranja BETWEEN '".$period_od."' AND '".$period_do."'
									 OR pred.pr_datum_uplate BETWEEN '".$period_od."' AND '".$period_do."')
									 ";
									 
					$text_naslov = "<b>Izdani i uplaćeni</b> predračuni u periodu od <b>".date("d.m.Y",strtotime($period_od))."</b> do <b>".date("d.m.Y",strtotime($period_do))."</b>";
				}
				else if($tip == 4){
					$uslov_period = "AND pred.pr_datum_uplate BETWEEN  '".$period_od."' AND '".$period_do."'";
					$text_naslov = "<b>Uplaćeni</b> predračuni u periodu od <b>".date("d.m.Y",strtotime($period_od))."</b> do <b>".date("d.m.Y",strtotime($period_do))."</b>";
				}
			?>
			<div class="content_box" style="min-height: 100px;">
				<div class="row">
					<div class="col-lg-12">
						<div class="col-lg-10">
							<h1><?php echo $text_naslov;?></h1>
							<hr>
						</div>
						<br>
						<?php
							$query_get_predracune = $db->prepare('
								SELECT pred.pr_zaposlenik, pred.pr_kandidat_id, kan.vrsta_ugovora_nd_kandidata, pred.pr_datum_kreiranja, 
										pred.pr_broj_predracuna, pred.pr_file, pred.pr_status, 
										kan.ime_nd_kandidata, kan.prezime_nd_kandidata,
										pred.pr_datum_uplate
								FROM idk_predracuni pred
								JOIN idk_nd_kandidata kan
								ON kan.id_broj_nd_kandidata = pred.pr_kandidat_id
								WHERE
									pred.pr_rata = 1 
								AND pred.pr_status is not NULL 
								AND pred.pr_vrsta_predracuna = 1
								AND pred.pr_id IN(
									SELECT MAX(pr.pr_id) 
									FROM idk_predracuni pr 
									WHERE pr.pr_rata = 1 
									GROUP BY pr.pr_kandidat_id
								) 
								'.$uslov_period.'
								'.$uslov_employee.'
							');
							$query_get_predracune -> execute();
							// var_dump($query_get_predracune);
							// exit();
						?>
						
						<table id="tabela_predracuni" class="table-striped" style="width:100%">
							<thead>
								<tr>
									<th>Ime i prezime kandidata</th>
									<th class="text-center">Naziv predračuna</th>
									<th class="text-center" >Datum kreiranja ugovora</th>
									<th class="text-center" >Status</th>
									<th>Vrsta ugovora</th>
									
								</tr>
							</thead>
							<tbody>

							<?php

								while ($row_file = $query_get_predracune->fetch()){
									
									$id_zaposlenik=$row_file['pr_zaposlenik'];
									$id_kan=$row_file['pr_kandidat_id'];
									$status_predr = $row_file['pr_status'];
									$pr_file = $row_file['pr_file'];
									$ime = $row_file['ime_nd_kandidata'];
									$prezime = $row_file['prezime_nd_kandidata'];
									$br_predracuna = $row_file['pr_broj_predracuna'];
									$datum_kreiranja = substr($row_file['pr_datum_kreiranja'],0,10);
									$vrsta_ugovora_kandidata_ispis_otvoren = $row_file['vrsta_ugovora_nd_kandidata'];
									$datum_uplate = $row_file['pr_datum_uplate'];
									$datum_kreiranja = date("d.m.Y",strtotime($datum_kreiranja));
									$datum_uplate = date("d.m.Y",strtotime($datum_uplate));
									?>
										
											<tr>
												<td><a target="_blank" href="<?php getSiteURL(); ?>nostrifikacija_diploma?page=otvori_ND_kandidata&id=<?php echo $id_kan;?>"><?php echo $ime." ".$prezime;?></a></td>
												<td class="text-center"><?php echo $br_predracuna;?></td>
												<td class="text-center" data-order="<?php echo date("Y-m-d", strtotime($datum_kreiranja));?>"><?php echo $datum_kreiranja;?></td>
												
											<?php
											
												if($status_predr == 0){
													$status_show = '<td class="text-center" data-order="a"><span class="label label-warning material-label material-label_warning main-container__column ">Arhiviran</span></td>';
												}else if($status_predr == 1){
													$status_show = '<td class="text-center" data-order="b"><span class="label label-info material-label material-label_info main-container__column">Poslan</span></td>';
												}else if($status_predr == 2){
													$status_show = '<td class="text-center" data-order="'.date("Y-m-d", strtotime($datum_uplate)).'"><span class="label label-success material-label material-label_success main-container__column">Uplaćen na: '.date("d.m.Y", strtotime($datum_uplate)).'</span></td>';
												}else if($status_predr == 3){
													$status_show = '<td class="text-center" data-order="c"><span class="label label-danger material-label material-label_danger main-container__column">In Caso 1</span></td>';
												}else if($status_predr == 4){
													$status_show = '<td class="text-center" data-order="d"><span class="label label-danger material-label material-label_danger main-container__column">In Caso 2</span></td>';
												}else if($status_predr == 5){
													$status_show = '<td class="text-center" data-order="e"><span class="label label-danger material-label material-label_danger main-container__column">In Caso 3</span></td>';
												}

												echo $status_show;
												
											
												if($vrsta_ugovora_kandidata_ispis_otvoren == 1){
													echo '<td>Ugovor bez popusta na 2 rate!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 2){
													echo '<td>Ugovor sa popustom na 2 rate!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 3){
													echo '<td>Ugovor bez popusta na 5 rata!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 4){
													echo '<td>Ugovor sa popustom na 5 rata!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 5){
													echo '<td>Ugovor bez popusta na 3 rate!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 6){
													echo '<td>Ugovor sa popustom na 3 rate!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 7){
													echo '<td>Ugovor bez popusta na 4 rate!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 8){ 
													echo '<td>Ugovor sa popustom na 4 rate!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 9){ 
													echo '<td>Ugovor bez popusta na 1 ratu!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 10){ 
													echo '<td>Ugovor sa popustom na 1 ratu!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 11){ 
													echo '<td>Mikrofin ugovor bez popusta!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 12){ 
													echo '<td>Mikrofin ugovor sa popustom!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 21){ 
													echo '<td>Ugovor sa 20% popusta na 1 ratu!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 22){ 
													echo '<td>Ugovor sa 20% popusta na 2 rate!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 23){ 
													echo '<td>Ugovor sa 20% popusta na 3 rate!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 24){ 
													echo '<td>Ugovor sa 20% popusta na 4 rate!</td>';
												}
												else if($vrsta_ugovora_kandidata_ispis_otvoren == 25){ 
													echo '<td>Ugovor sa 20% popusta na 5 rata!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 71){ 
													echo '<td>Ugovor za struke na 1 ratu sa 30% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 72){ 
													echo '<td>Ugovor za struke na 2 rate sa 30% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 73){ 
													echo '<td>Ugovor za struke na 3 rate sa 30% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 74){ 
													echo '<td>Ugovor za struke na 4 rate sa 30% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 75){ 
													echo '<td>Ugovor za struke na 5 rata sa 30% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 51){ 
													echo '<td>Ugovor za struke na 1 ratu sa 50% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 52){ 
													echo '<td>Ugovor za struke na 2 rate sa 50% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 53){ 
													echo '<td>Ugovor za struke na 3 rate sa 50% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 54){ 
													echo '<td>Ugovor za struke na 4 rate sa 50% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 55){ 
													echo '<td>Ugovor za struke na 5 rata sa 50% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 99){ 
													echo '<td>Ugovor za struke na 1 ratu sa 100% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 61){ 
													echo '<td>Ugovor za struke na 1 ratu sa 20% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 62){ 
													echo '<td>Ugovor za struke na 2 rate sa 20% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 63){ 
													echo '<td>Ugovor za struke na 3 rate sa 20% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 64){ 
													echo '<td>Ugovor za struke na 4 rate sa 20% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 65){ 
													echo '<td>Ugovor za struke na 5 rata sa 20% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 41){ 
													echo '<td>Ugovor za struke na 1 ratu sa 70% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 42){ 
													echo '<td>Ugovor za struke na 2 rate sa 70% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 43){ 
													echo '<td>Ugovor za struke na 3 rate sa 70% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 44){ 
													echo '<td>Ugovor za struke na 4 rate sa 70% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 45){ 
													echo '<td>Ugovor za struke na 5 rata sa 70% popusta!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 82){ 
													echo '<td>Ugovor sa popustom 10% na 2 rate!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 83){ 
													echo '<td>Ugovor sa popustom 10% na 3 rate!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 84){ 
													echo '<td>Ugovor sa popustom 10% na 4 rate!</td>';
												}else if($vrsta_ugovora_kandidata_ispis_otvoren == 85){ 
													echo '<td>Ugovor sa popustom 10% na 5 rata!</td>';
												}
												else{
													echo '<td></td>';
												}

										?>
										</tr>
										<?php
									}
									?>
											
								</tbody>
							</table>
						<script>
							$(document).ready(function() {
								$('#tabela_predracuni').DataTable({
									responsive: true,
									"order": [[ 4, "desc" ]],
									"bAutoWidth": true,
									"pageLength": 10,
								});
							});	
						</script>
					</div>
				</div>
			</div>	
		</div>
	</div>
</body>
</html>
