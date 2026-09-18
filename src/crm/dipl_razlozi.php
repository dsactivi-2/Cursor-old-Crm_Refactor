<?php
	include("includes/functions.php");
	include("includes/common.php");
	
	$getEmployeeStatus = getEmployeeStatus();

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: dipl_razlozi?page=listaRazloga&type=1");
	}
?>
<html>
	<head>

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>
			<?php 
				getTitle(); 
			?>
		</title>

		<?php 
			include('includes/head.php');
		?>
	</head>
	<body>
		<?php 
			if (in_array($getUserIp, $getIpWhiteList))
			{
		?>
		<header>
			<?php
				include('header.php');
			?>
		</header>
		<div id="sidebar">
			<?php
				include('menu.php');
			?>
		</div>
		
		<!-- Content START -->
		<div id="content">
			<!--Container-fluid START -->
			<div class="container-fluid">
			<?php 
				switch($page){
				case "listaRazloga":
				$type = intval($_GET["type"]);
				if($type != 0){
					if($type == 1){
						$uslovKandidatStatus = " (k.status_nd_kandidata = 1 AND k.pstatus_nd_kandidata = 4) ";
						$vodiUslov = " r.ponovno_zvanje_ro = 1 ";
						$naslovIspis = " Nezainteresirane";
					}else{
						$uslovKandidatStatus = " (k.status_nd_kandidata = 7) ";
						$vodiUslov = " r.ponovno_zvanje_ro = 0 ";
						$naslovIspis = " Arhivu";
					}
				}else{
					header("Location: dipl_razlozi?page=listaRazloga&type=1");
				}
				
			?>
				<div class="row" style = "margin-top: 20px;">
					<div class="col-md-12">
						<div class="content_box">
							<div class="row">
								<div class="col-xs-8">
									<h1><i class="fa fa-list-ul" style="margin-right: 15px; " aria-hidden="true"></i>Razlozi odbijanja - vodi u <?php echo $naslovIspis; ?></h1>
								</div>
								<div class="col-xs-4 text-right">
								</div>
							</div>
							<div class="row" style = "margin-top: 20px;">
								<div class="col-xs-12">
									<script type="text/javascript">
										$(document).ready(function() {
											var table_s = $('#table_List').DataTable({
		
												responsive: true,
												
												"order": [[ 0, "desc" ]],

												"bAutoWidth": false,

												"aoColumns": [
														{ "width": "5%"},
														{ "width": "25%" },
														{ "width": "10%" },
														{ "width": "15%" },
														{ "width": "10%" },
														{ "width": "10%" },
														{ "width": "15%" },
														{ "width": "10%" }
													],
											});
										});
									</script>
									<table id="table_List" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th class="text-center">#</th>
												<th class="text-center">Naziv razloga</th>
												<th class="text-center">Status</th>
												<th class="text-center">Vodi u</th>
												<th class="text-center">Poziv ponovo</th>
												<th class="text-center">Broj kandidata</th>
												<th class="text-center">Dodao</th>
												<th class="text-center">Akcija</th>
											</tr>
										</thead>
										<tbody>
									<?php 
										//Id biljeski
										$nizPomocni = array();
										$nizPomocniImplode = "";
										$queryPomocni = $db->prepare("
											SELECT 
												MAX(b.id_biljeska_nd) AS id_kan
											FROM 
												idk_nd_kandidata_biljeske b
											INNER JOIN 
												idk_nd_kandidata k
											ON
												b.id_kandidata_biljeska_nd = k.id_broj_nd_kandidata
											WHERE 
												".$uslovKandidatStatus."
												AND 
												b.tip_biljeska_nd = 3 
												AND 
												b.status_biljeska_nd = 2
												AND 
												b.razlog_biljeska_nd is not null
											GROUP BY b.id_kandidata_biljeska_nd
										");
										$queryPomocni->execute();
										while($rowPomocni = $queryPomocni->fetch()){
											array_push($nizPomocni, $rowPomocni["id_kan"]);
										}
										$nizPomocniImplode = implode(",",$nizPomocni);
										$broj_Kandidata = 0;
	
										$query = $db->prepare("
											SELECT
												r.id_ro, 
												r.naziv_ro_bs, 
												r.status_ro, 
												r.ponovno_zvanje_ro, 
												r.br_dana_ro, 
												r.dodao_ro,
												(
													SELECT 
													COUNT(k.id_broj_nd_kandidata) 
													FROM 
														idk_nd_kandidata k
													INNER JOIN 
														idk_nd_kandidata_biljeske bilj
													ON 
														k.id_broj_nd_kandidata = bilj.id_kandidata_biljeska_nd 
													WHERE 
														".$uslovKandidatStatus."
														AND 
														bilj.tip_biljeska_nd = 3 
														AND 
														bilj.status_biljeska_nd = 2
														AND 
														bilj.razlog_biljeska_nd = r.id_ro
														AND 
														bilj.id_biljeska_nd IN(
															".$nizPomocniImplode."
														)
												)
												AS 
												broj_kan
											FROM 
												idk_ro_usluge r
											WHERE 
												r.tip_ro = :tip_ro 
												AND
												r.status_ro != 3
												AND 
												".$vodiUslov."
										");
										$query->execute(array(
											':tip_ro' => 1
										));
										
										while($row = $query->fetch()){
											$id_ro = $row["id_ro"];
											$naziv_ro_bs = $row["naziv_ro_bs"];
											if($row["status_ro"] == 0){
												$status_ro = '<span class="label label-warning material-label material-label_warning main-container__column text-center">Na čekanju</span>';
											}else if($row["status_ro"] == 2){
												$status_ro = '<span class="label label-danger material-label material-label_danger main-container__column text-center">Odbijen</span>';
											}else if($row["status_ro"] == 3){
												$status_ro = '<span class="label label-default material-label material-label_default main-container__column text-center">Deaktiviran</span>';
											}else{
												$status_ro = '<span class="label label-success material-label material-label_success main-container__column text-center">Aktivan</span>';
											}
											if($row["ponovno_zvanje_ro"] == 0){
												$br_dana_ro = '<span class="label label-warning material-label material-label_warning main-container__column text-left">0</span>';;
												$ponovno_zvanje_ro = '<span class="label label-danger material-label material-label_danger main-container__column text-left">Arhiv</span>';
											}else{
												$br_dana_ro = '<span class="label label-primary material-label material-label_primary main-container__column text-left">'.$row["br_dana_ro"].'</span>';
												$ponovno_zvanje_ro = '<span style = "background-color: #BF214B; color: white;" class="label label-default material-label material-label_default main-container__column text-left">Nezinteresirane</span>';
											}
											$dodao_ro = getZaposlenikimeR($row["dodao_ro"]);
											$br_kann = intval($row["broj_kan"]);
											if($br_kann != 0){
												$broj_kan = '<a href = " '.getSiteUrlr().'dipl_razlozi?page=pregledKandidataPoRazlogu&type='.$type.'&idRazlog='.$id_ro.'" target="_BLANK"><span class="label label-default material-label material-label_default main-container__column text-center">'.intval($row["broj_kan"]).'</span></a>';
											}else{
												$broj_kan = '<span class="label label-default material-label material-label_default main-container__column text-center">'.intval($row["broj_kan"]).'</span>';
											}
											
											$broj_Kandidata = $broj_Kandidata + $br_kann;
											
									?>
										<tr>
											<td class="text-center"><?php echo $id_ro; ?></td>
											<td class="text-center"><?php echo $naziv_ro_bs; ?></td>
											<td class="text-center"><?php echo $status_ro; ?></td>
											<td class="text-center"><?php echo $ponovno_zvanje_ro; ?></td>
											<td class="text-center"><?php echo $br_dana_ro; ?></td>
											<td class="text-center"><?php echo $broj_kan; ?></td>
											<td class="text-center"><?php echo $dodao_ro; ?></td>
											<td class="text-center">
												<div class="btn-group material-btn-group">
													<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_success idk_btn_table" data-toggle="dropdown">
														<i class="fa fa-cogs fa-lg" aria-hidden="true">
														</i> 
														<span class="caret material-btn__caret">
														</span>
													</button>
													<ul style = "top:32px; left: -40px; min-width: 150px !important;" class="dropdown-menu material-dropdown-menu material-dropdown-menu_success idk_dropdown_table" role="menu">
														
														<li>
															<?php 
																if($br_kann != 0){
															?>
															<a href="<?php getSiteURL(); ?>dipl_razlozi?page=pregledKandidataPoRazlogu&type=<?php echo $type; ?>&idRazlog=<?php echo $id_ro; ?>" target="_BLANK" class="material-dropdown-menu__link">
																<i class="fa fa-folder-open-o" aria-hidden="true">
																</i> 
																Kandidati
															</a>
															<?php
																}
															?>
														</li>
														<li>
															<?php 
																if($row["status_ro"] != 3 AND $br_kann == 0 AND ($logged_employee_id == 67 OR $logged_employee_id == 75) ){
															?>
															<a id = "action_raz" href="#" class="material-dropdown-menu__link" 
																data-toggle="modal" 
																data-target="#open_action_raz"
																data-address_r ="<?php getSiteURL(); ?>dipl_razlozi?page=deaktiviraj&type=<?php echo $type; ?>&id_raz=<?php echo $id_ro; ?>"
																data-status_r ="<?php echo $row["status_ro"]; ?>"
																>
																<i class="fa fa-check" aria-hidden="true">
																</i> 
																Deaktiviraj
															</a>	
															<?php
																}
															?>
														</li>
														<li>
															<?php 
																if($row["status_ro"] == 1){
															?>
															<a id = "action_odbij" href="#" class="material-dropdown-menu__link" 
																data-toggle="modal" 
																data-target="#open_action_odbij"
																data-address_o ="<?php getSiteURL(); ?>dipl_razlozi?page=odbij&type=<?php echo $type; ?>&id_raz=<?php echo $id_ro; ?>"
																data-status_o ="<?php echo $row["status_ro"]; ?>"
																>
																<i class="fa fa-check" aria-hidden="true">
																</i> 
																Odbij
															</a>	
															<?php
																}
															?>
														</li>
														<li>
															<?php 
																if($row["status_ro"] == 2){
															?>
															<a id = "action_aktiviraj" href="#" class="material-dropdown-menu__link" 
																data-toggle="modal" 
																data-target="#open_action_aktiviraj"
																data-address_a ="<?php getSiteURL(); ?>dipl_razlozi?page=aktiviraj&type=<?php echo $type; ?>&id_raz=<?php echo $id_ro; ?>"
																data-status_a ="<?php echo $row["status_ro"]; ?>"
																>
																<i class="fa fa-check" aria-hidden="true">
																</i> 
																Aktiviraj
															</a>	
															<?php
																}
															?>
														</li>
													</ul>
												</div>
											</td>
										</tr>
										</tr>
									<?php 
										}
									?>
										</tbody>
									</table>
									<script>
										$(document).on("click","#action_raz",function() {
											var address_r = $(this).data("address_r");
											var status_r = $(this).data("status_r");
											//alert(address_r);
											
											document.getElementById("action_raz_submit").href = address_r;
											$("#text_action").html("Jeste li sigurni da želite deaktivirati razlog?");
											
										});
										$(document).on("click","#action_odbij",function() {
											var address_o = $(this).data("address_o");
											var status_o = $(this).data("status_o");
											//alert(address_r);
											
											document.getElementById("action_odb_submit").href = address_o;
											$("#text_action_o").html("Jeste li sigurni da želite odbiti razlog?");
											
										});
										$(document).on("click","#action_aktiviraj",function() {
											var address_a = $(this).data("address_a");
											var status_a = $(this).data("status_a");
											//alert(address_r);
											
											document.getElementById("action_akt_submit").href = address_a;
											$("#text_action_a").html("Jeste li sigurni da želite aktivirati razlog?");
											
										});
									</script>
									<div class="modal material-modal material-modal_danger fade" id="open_action_raz">
										<div class="modal-dialog">
											<div class="modal-content material-modal__content">
												<div class="modal-header material-modal__header">
													<button class="close material-modal__close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title material-modal__title">
														Akcija
													</h4>
												</div>
												<div class="modal-body material-modal__body">
													<div class="row">
														<div class="col-xs-12 text-left">
															<p id="text_action"></p>
														</div>
													</div>
												</div>
												<div class="modal-footer material-modal__footer">
													<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
													<a id="action_raz_submit" href="#"><button class="btn btn-primary material-btn material-btn_success">Završi</button></a>
												</div>
											</div>
										</div>
									</div>
									<div class="modal material-modal material-modal_danger fade" id="open_action_odbij">
										<div class="modal-dialog">
											<div class="modal-content material-modal__content">
												<div class="modal-header material-modal__header">
													<button class="close material-modal__close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title material-modal__title">
														Akcija
													</h4>
												</div>
												<div class="modal-body material-modal__body">
													<div class="row">
														<div class="col-xs-12 text-left">
															<p id="text_action_o"></p>
														</div>
													</div>
												</div>
												<div class="modal-footer material-modal__footer">
													<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
													<a id="action_odb_submit" href="#"><button class="btn btn-primary material-btn material-btn_success">Završi</button></a>
												</div>
											</div>
										</div>
									</div>
									<div class="modal material-modal material-modal_danger fade" id="open_action_aktiviraj">
										<div class="modal-dialog">
											<div class="modal-content material-modal__content">
												<div class="modal-header material-modal__header">
													<button class="close material-modal__close" data-dismiss="modal">&times;</button>
													<h4 class="modal-title material-modal__title">
														Akcija
													</h4>
												</div>
												<div class="modal-body material-modal__body">
													<div class="row">
														<div class="col-xs-12 text-left">
															<p id="text_action_a"></p>
														</div>
													</div>
												</div>
												<div class="modal-footer material-modal__footer">
													<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
													<a id="action_akt_submit" href="#"><button class="btn btn-primary material-btn material-btn_success">Završi</button></a>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									Broj kandidata ukupno: <span class="label label-danger material-label material-label_danger main-container__column text-center"><?php echo $broj_Kandidata; ?></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php 
				break;
				
				case "pregledKandidataPoRazlogu":
				$id = intval($_GET["idRazlog"]);
				$type = intval($_GET["type"]);
				if($type != 0){
					if($type == 1){
						$uslovKandidatStatus = "(k.status_nd_kandidata = 1 AND k.pstatus_nd_kandidata = 4)";
						$vodiUslov = "r.ponovno_zvanje_ro = 1";
					}else{
						$uslovKandidatStatus = "(k.status_nd_kandidata = 7)";
						$vodiUslov = "r.ponovno_zvanje_ro = 0";
					}
				}else{
					header("Location: dipl_razlozi?page=listaRazloga&type=1");
				}
			?>
			<div class="row" style = "margin-top: 20px;">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-xs-8">
								<?php 
									$queryRazlog = $db->prepare("
										SELECT
											r.id_ro, 
											r.naziv_ro_bs
										FROM 
											idk_ro_usluge r
										WHERE 
											r.id_ro = :id_ro
									");
									$queryRazlog->execute(array(
										':id_ro' => $id
									));
									$rowRazlog = $queryRazlog->fetch();
									echo '<h1>Pregled kandidata pod razlogom: "'.$rowRazlog["naziv_ro_bs"].'" </h1>';
								?>
							</div>
							<div class="col-xs-4 text-right">
							<?php
								if($logged_employee_id == 67 OR $logged_employee_id == 75){
							?>
								<a href="" data-toggle="modal" data-target="#prebRaz" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive">
									<i class="fa fa-retweet" aria-hidden="true"></i>
									<span>
										Prebaci
									</span>
								</a>
								<div class="modal material-modal material-modal_success fade text-left" id="prebRaz">
									<div class="modal-dialog modal-lg">
										<div class="modal-content material-modal__content">
											<div class="modal-header material-modal__header">
												<button class="close material-modal__close" data-dismiss="modal">&times;</button>
												<h4 class="modal-title material-modal__title"><i style = "margin-right: 10px;" class="fa fa-retweet" aria-hidden="true"></i>Prebaci</h4>
											</div> 
											<div class="modal-body material-modal__body">
												<form action="<?php getSiteURL(); ?>dipl_razlozi?page=promjenaRazloga" enctype="multipart/form-data" method="post" role="form" class="form-horizontal" id = "promjenaRazloga_form">
													<input type = "hidden" class = "" id = "idSaRazlogaPreb" name = "idSaRazlogaPreb" value="<?php echo $id; ?>">
													<input type = "hidden" class = "" id = "typeRazlog" name = "typeRazlog" value="<?php echo $type; ?>">
													<div class="form-group">
														<div class="col-md-offset-2 col-sm-8 text-center">
															<label for="idNaRazlogaPreb" class="col-sm-4 control-label">
																<span class="text-danger">
																	* 
																</span>
																Prebaci na:
															</label>
															<div class="col-sm-8">
																<div class="">
																	<select class="selectpicker validacija" title = "Odaberi" data-actions-box="true" data-live-search = "true" id="idNaRazlogaPreb" name="idNaRazlogaPreb" data-selected-text-format = "count > 2" required>
																		<?php 
																			$queryRazlozi = $db->prepare("
																				SELECT 
																					r.id_ro, 
																					r.naziv_ro_bs
																				FROM 
																					idk_ro_usluge r
																				WHERE 
																					r.tip_ro = 1
																					AND 
																					r.status_ro != 3
																					AND 
																					".$vodiUslov." 
																					AND 
																					r.id_ro != ".$id."
																			");
																			$queryRazlozi->execute();
																			while($rowRazlozi = $queryRazlozi->fetch()){
																				echo '<option value = "'.$rowRazlozi["id_ro"].'" data-subtext="'.$rowRazlozi["id_ro"].'">'.$rowRazlozi["naziv_ro_bs"].'</option>';
																			}
																		?>
																	</select>
																</div>
															</div>
														</div>
													</div>
													<div class="form-group">
														<div class="col-md-offset-2 col-sm-8 text-center">
															<label for="limitRazlogaPreb" class="col-sm-4 control-label">
																Limitiraj:
															</label>
															<div class="col-sm-8">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input validacija" type="number" name="limitRazlogaPreb" id="limitRazlogaPreb" placeholder="Broj" autocomplete="off">
																	<span class="materail-input-block__line">
																	</span>
																</div>
															</div>
														</div>
													</div>
													<div class="modal-footer material-modal__footer"> 
														<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
														<button type="submit" id = "omoguciDugme" class="btn btn-primary material-btn material-btn_success" form="promjenaRazloga_form"><i class="fa fa-search" aria-hidden="true"></i> Završi</button>
													</div>
													<script>
														$(document).ready(function(){
															document.getElementById("omoguciDugme").disabled = true;
															document.getElementById("omoguciDugme").title = "Opcija onemogućena!";
														});
														$(".validacija").change(function(){
															if ($('#limitRazlogaPreb').val() || $('#idNaRazlogaPreb').val()){
																document.getElementById("omoguciDugme").disabled = false;
																document.getElementById("omoguciDugme").title = "Opcija omogućena!";
															}else{
																document.getElementById("omoguciDugme").disabled = true;
																document.getElementById("omoguciDugme").title = "Opcija onemogućena!";
															}
														});
													</script>
												</form>
											</div>
										</div>
									</div>
								</div>
						<?php
							}
						?>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-xs-12">
								<script type="text/javascript">
									$(document).ready(function() {
										var table_s = $('#table_List').DataTable({
	
											responsive: true,
											
											"order": [[ 0, "desc" ]],

											"bAutoWidth": false,

											"aoColumns": [
													{ "width": "5%"},
													{ "width": "15%" },
													{ "width": "10%" },
													{ "width": "15%" },
													{ "width": "15%" },
													{ "width": "40%" }
												],
										});
									});
								</script>
								<table id="table_List" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th class="text-center">#</th>
											<th class="text-center">Ime i prezime</th>
											<th class="text-center">Broj telefona</th>
											<th class="text-center">Status</th>
											<th class="text-center">Datum biljske</th>
											<th class="text-center">Biljeska</th>
										</tr>
									</thead>
									<tbody>
								<?php 
									//Id biljeski
									$nizPomocni = array();
									$nizPomocniImplode = "";
									$queryPomocni = $db->prepare("
										SELECT 
											MAX(b.id_biljeska_nd) AS id_kan
										FROM 
											idk_nd_kandidata_biljeske b
										INNER JOIN 
											idk_nd_kandidata k
										ON
											b.id_kandidata_biljeska_nd = k.id_broj_nd_kandidata
										WHERE 
											".$uslovKandidatStatus."
											AND 
											b.tip_biljeska_nd = 3 
											AND 
											b.status_biljeska_nd = 2
											AND 
											b.razlog_biljeska_nd is not null
										GROUP BY b.id_kandidata_biljeska_nd
									");
									$queryPomocni->execute();
									while($rowPomocni = $queryPomocni->fetch()){
										array_push($nizPomocni, $rowPomocni["id_kan"]);
									}
									$nizPomocniImplode = implode(",",$nizPomocni);

									$query = $db->prepare("
										SELECT 
											k.id_broj_nd_kandidata,
											k.ime_nd_kandidata,
											k.prezime_nd_kandidata,
											k.mobilni_nd_kandidata,
											k.status_nd_kandidata,
											bilj.sadrzaj_biljeska_nd,
											bilj.vrijeme_dodavanja_biljeska_nd
										FROM 
											idk_nd_kandidata k
										INNER JOIN 
											idk_nd_kandidata_biljeske bilj
										ON 
											k.id_broj_nd_kandidata = bilj.id_kandidata_biljeska_nd 
										WHERE 
											".$uslovKandidatStatus."
											AND 
											bilj.tip_biljeska_nd = 3 
											AND 
											bilj.status_biljeska_nd = 2
											AND 
											bilj.razlog_biljeska_nd = :razIDD
											AND 
											bilj.id_biljeska_nd IN(
												".$nizPomocniImplode."
											)
									");
									$query->execute(array(
										':razIDD' => $id
									));
									
									while($row = $query->fetch()){
										$id_broj_nd_kandidata = $row["id_broj_nd_kandidata"];
										$ime_nd_kandidata = '<a href = " '.getSiteUrlr().'nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$id_broj_nd_kandidata.'" target="_BLANK"><span class="label label-default material-label material-label_default main-container__column text-center">'.$row["ime_nd_kandidata"]." ".$row["prezime_nd_kandidata"].'</span></a>';
										$mobilni_nd_kandidata = $row["mobilni_nd_kandidata"];
										if(intval($row["status_nd_kandidata"]) == 7){
											$status_nd_kandidata = '<span class="label label-danger material-label material-label_danger main-container__column text-left">Arhiv</span>';
										}else{
											$status_nd_kandidata = '<span style = "background-color: #BF214B; color: white;" class="label label-default material-label material-label_default main-container__column text-left">Nezinteresirane</span>';
										}
										$sadrzaj_biljeska_nd = substr($row["sadrzaj_biljeska_nd"], 0, 70)."...";
										$vrijeme_dodavanja_biljeska_nd = date("d.m.Y H:i", strtotime($row["vrijeme_dodavanja_biljeska_nd"]));
										
								?>
									<tr>
										<td class="text-center"><?php echo $id_broj_nd_kandidata; ?></td>
										<td class="text-center"><?php echo $ime_nd_kandidata; ?></td>
										<td class="text-center"><?php echo $mobilni_nd_kandidata; ?></td>
										<td class="text-center"><?php echo $status_nd_kandidata; ?></td>
										<td class="text-center"><?php echo $vrijeme_dodavanja_biljeska_nd; ?></td>
										<td class="text-center"><?php echo $sadrzaj_biljeska_nd; ?></td>
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
			<?php 
				break;
				case "promjenaRazloga":
					$idSaRazlogaPreb = intval($_POST["idSaRazlogaPreb"]);
					$idNaRazlogaPreb = intval($_POST["idNaRazlogaPreb"]);
					$limitRazlogaPreb = intval($_POST["limitRazlogaPreb"]);
					$nizIDBiljeski = array();
					$nizIDBiljeskiImp = "";
					$limitUslov = "";
					if($limitRazlogaPreb != 0){
						$limitUslov = " LIMIT ".$limitRazlogaPreb." ";
					}else{
						$limitUslov = "";
					}
					$type = intval($_POST["typeRazlog"]);
					if($type != 0){
						if($type == 1){
							$uslovKandidatStatus = "(k.status_nd_kandidata = 1 AND k.pstatus_nd_kandidata = 4)";
							$vodiUslov = "r.ponovno_zvanje_ro = 1";
						}else{
							$uslovKandidatStatus = "(k.status_nd_kandidata = 7)";
							$vodiUslov = "r.ponovno_zvanje_ro = 0";
						}
					}else{
						header("Location: dipl_razlozi?page=listaRazloga&type=1");
					}
					//--------------------------------------------------------------
					//Provjere
					// $promjenaStatusa = 0;
					// $queryPonovoZvatiSA = $db->prepare("
						// SELECT ponovno_zvanje_ro
						// FROM idk_ro_usluge
						// WHERE id_ro = ".$idSaRazlogaPreb."
					// ");
					// $queryPonovoZvatiSA->execute();
					// $rowPonovoZvatiSA = $queryPonovoZvatiSA->fetch();
					// $queryPonovoZvatiNA = $db->prepare("
						// SELECT ponovno_zvanje_ro
						// FROM idk_ro_usluge
						// WHERE id_ro = ".$idNaRazlogaPreb."
					// ");
					// $queryPonovoZvatiNA->execute();
					// $rowPonovoZvatiNA = $queryPonovoZvatiNA->fetch();
					
					// if($rowPonovoZvatiSA["ponovno_zvanje_ro"] == 0 AND $rowPonovoZvatiNA["ponovno_zvanje_ro"] == 0){
						// $promjenaStatusa = 1;
					// }else if($rowPonovoZvatiSA["ponovno_zvanje_ro"] == 0 AND $rowPonovoZvatiNA["ponovno_zvanje_ro"] == 1){
						// $promjenaStatusa = 2;
					// }else if($rowPonovoZvatiSA["ponovno_zvanje_ro"] == 1 AND $rowPonovoZvatiNA["ponovno_zvanje_ro"] == 0){
						// $promjenaStatusa = 3;
					// }else{
						// $promjenaStatusa = 4;
					// }
					//--------------------------------------------------------------
					$nizPomocni = array();
					$nizPomocniImplode = "";
					$queryPomocni = $db->prepare("
						SELECT 
							MAX(b.id_biljeska_nd) AS id_kan
						FROM 
							idk_nd_kandidata_biljeske b
						INNER JOIN 
							idk_nd_kandidata k
						ON
							b.id_kandidata_biljeska_nd = k.id_broj_nd_kandidata
						WHERE 
							".$uslovKandidatStatus."
							AND 
							b.tip_biljeska_nd = 3 
							AND 
							b.status_biljeska_nd = 2
							AND 
							b.razlog_biljeska_nd is not null
						GROUP BY b.id_kandidata_biljeska_nd
					");
					$queryPomocni->execute();
					while($rowPomocni = $queryPomocni->fetch()){
						array_push($nizPomocni, $rowPomocni["id_kan"]);
					}
					$nizPomocniImplode = implode(",",$nizPomocni);
					$query = $db->prepare("
						SELECT 
							k.id_broj_nd_kandidata,
							bilj.id_biljeska_nd,
							bilj.razlog_biljeska_nd
						FROM 
							idk_nd_kandidata k
						INNER JOIN 
							idk_nd_kandidata_biljeske bilj
						ON 
							k.id_broj_nd_kandidata = bilj.id_kandidata_biljeska_nd 
						WHERE 
							".$uslovKandidatStatus."
							AND 
							bilj.tip_biljeska_nd = 3 
							AND 
							bilj.status_biljeska_nd = 2
							AND 
							bilj.razlog_biljeska_nd = :razIDD
							AND 
							bilj.id_biljeska_nd IN(
								".$nizPomocniImplode."
							)
						ORDER BY kan.id_broj_nd_kandidata ASC 
						".$limitUslov."
					");
					$query->execute(array(
						':razIDD' => $idSaRazlogaPreb
					));
					
					while($row = $query->fetch()){
						$id_broj_nd_kandidata = intval($row["id_broj_nd_kandidata"]);
						$id_biljeska_nd = intval($row["id_biljeska_nd"]);
						$razlog_biljeska_nd = intval($row["razlog_biljeska_nd"]);
						echo "<br>ID KANDIDATA: ".$id_broj_nd_kandidata." ID BILJESKA: ".$id_biljeska_nd." ID RAZLOG TRENUTNI: ".$razlog_biljeska_nd."";
						array_push($nizIDBiljeski,$id_biljeska_nd);
						
					}
					if(count($nizIDBiljeski) != 0){
						$nizIDBiljeskiImp = implode(",",$nizIDBiljeski);
						echo "<br><br><br>Biljeske ID: ".$nizIDBiljeskiImp."";
						$queryUpdate = $db->prepare("
							UPDATE
								idk_nd_kandidata_biljeske
							SET 
								razlog_biljeska_nd = :razlog_biljeska_nd
							WHERE 
								id_biljeska_nd IN (".$nizIDBiljeskiImp.")
						");
						$queryUpdate->execute(array(
							':razlog_biljeska_nd' => $idNaRazlogaPreb
						));
					}
					
					header("Location: " . getSiteURLr() . "dipl_razlozi?page=pregledKandidataPoRazlogu&type=".$type."&idRazlog=".$idSaRazlogaPreb);
					echo "<br><br><br>ID SA: ".$idSaRazlogaPreb."<br>ID NA: ".$idNaRazlogaPreb."<br>LIMIT:".$limitRazlogaPreb."";
				break;
				
				case "deaktiviraj";
					$id_raz = $_GET["id_raz"];
					$type = $_GET["type"];
					if(isset($id_raz)){
						//var_dump($id_raz);
						$query = $db->prepare("
							UPDATE
								idk_ro_usluge
							SET
								status_ro = 3
							WHERE 
								id_ro = :id_ro
						");
						$query->execute(array(
							':id_ro' => $id_raz
						));
					}
					header("Location: " . getSiteURLr() . "dipl_razlozi?page=listaRazloga&type=".$type."");
				break;
				
				case "odbij";
					$id_raz = $_GET["id_raz"];
					$type = $_GET["type"];
					if(isset($id_raz)){
						//var_dump($id_raz);
						$query = $db->prepare("
							UPDATE
								idk_ro_usluge
							SET
								status_ro = 2
							WHERE 
								id_ro = :id_ro
						");
						$query->execute(array(
							':id_ro' => $id_raz
						));
					}
					header("Location: " . getSiteURLr() . "dipl_razlozi?page=listaRazloga&type=".$type."");
				break;
				
				case "aktiviraj";
					$id_raz = $_GET["id_raz"];
					$type = $_GET["type"];
					if(isset($id_raz)){
						//var_dump($id_raz);
						$query = $db->prepare("
							UPDATE
								idk_ro_usluge
							SET
								status_ro = 1
							WHERE 
								id_ro = :id_ro
						");
						$query->execute(array(
							':id_ro' => $id_raz
						));
					}
					header("Location: " . getSiteURLr() . "dipl_razlozi?page=listaRazloga&type=".$type."");
				break;
				}
			?>
			</div>
		</div>
		<footer><?php getCopyright(); ?></footer>
		<?php 
			}else
			{			
				echo 
				'
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
	</body>
</html>