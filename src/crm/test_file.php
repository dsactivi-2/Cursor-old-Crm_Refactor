<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus  = explode( ',' , getEmployeeStatus());
	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: dipl_agenti?page=list");
	}
	function replaceTimLoga($log_desc){
			$log_desc = str_replace("tima 1","tima <b>JS</b>",$log_desc);
			$log_desc = str_replace("tima 2","tima <b>GC</b>",$log_desc);
			$log_desc = str_replace("tima 3","tima <b>WE</b>",$log_desc);
		return $log_desc;
	}
	
	function replaceIdLoga ($log_ids){
	
		$log_ids_niz = explode(",",$log_ids);
		foreach($log_ids_niz as &$id){
			$id = '<a class="a_link" target="_blank" href = "'.getSiteUrlr().'nostrifikacija_diploma?page=otvori_ND_kandidata&id='.$id.'">'.$id.'</a>';
		}
		if(strlen($log_ids_niz)!=1){
			$log_ids_links = implode(", ",$log_ids_niz);
	
		}
		return $log_ids_links;
	}
		
	function getStringBetween($string, $start, $end){
		$string = ' ' . $string;
		$ini = strpos($string, $start);
		if ($ini == 0) return '';
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		return substr($string, $ini, $len);
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
		<?php include('menu.php');  ?>
	</div>
	<style>
		/* Chrome, Safari, Edge, Opera */
		input::-webkit-outer-spin-button,
		input::-webkit-inner-spin-button {
		  -webkit-appearance: none;
		  margin: 0;
		}

		/* Firefox */
		input[type=number] {
		  -moz-appearance: textfield;
		}

		.a_link{
			color: #000000 !important;
		}
		.a_link:hover{
			color: rgb(80 255 0) !important;
			text-decoration: none !important;
		}
	</style>
	<div id="content">
		<div class="container-fluid">
		<?php
			switch ($page){

				case "list":

		?>
			<?php 
			$team = getLoggedEmployeeTeam();
			//if((in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status)) OR (!empty($employee_supervizor[0]))){
			if((in_array( "1" , $employee_status)) OR $logged_employee_id == 33 OR $logged_employee_id == 32)
				$uslov_team = "";
			else
				$uslov_team = " AND e.employee_team = ".$team." ";
			
			?>
			
			<div class="row">
				<div class="col-xs-12 text-left">
					<h1><i class="fa fa-users idk_color_green" aria-hidden="true"></i> DIPL agenti </h1>
				</div>
			</div>
			<hr />
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-xs-12">
								<div class="row">
									<div class="col-md-4">
								
									</div>
									<div class="col-md-8">
										<div class="row">
											<div class="text-center">
												<a href="<?php getSiteURL(); ?>dipl_agenti?page=logovi" style="float: right; margin-right: 8px;" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-eye" aria-hidden="true"></i> <span>Logovi</span></a>
											</div>
											<div class="text-center">
												<a href="<?php getSiteURL(); ?>dipl_agenti?page=open_skladiste" style="float: right; margin-right: 8px;" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-archive" aria-hidden="true"></i> <span>Skladište</span></a>
											</div>
											<div class="text-center">
												<a href="#" style="float: right; margin-right: 8px;" class="editLimitsAll btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive" data-toggle="modal" data-target="#editLimitsModal" ><i stlye="float: left;" class="fa fa-edit" aria-hidden="true"></i> <span>Uredi sve označene</span></a>
											</div>
											<div class="text-center">
												<a href="<?php getSiteURL(); ?>dipl_agenti?page=add_agent" style="float: right; margin-right: 8px;" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></a>
											</div>
										</div>
									</div>
								</div> 
							</div>
						</div>
						<hr />
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

											"aoColumns": [
													{ "width": "5%", "bSortable": false },
													{ "width": "5%", "bSortable": false },
													{ "width": "12%" },
													{ "width": "5%", },
													{ "width": "13%" },
													{ "width": "13%" },
													{ "width": "13%" },
													{ "width": "13%" },
													{ "width": "13%" },
													{ "width": "8%", "bSortable": false }
												]
										});
										
										$('#check_dnevni').on('click', function(){
											$("#display_dnevni").toggle();
										});
										$('#check_sedmicni').on('click', function(){
											$("#display_sedmicni").toggle();
										});
										$('#check_mjesecni').on('click', function(){
											$("#display_mjesecni").toggle();
										});
										
										$('.editLimitsAll').on('click', function(){
											$("#employee_ids .checkbox").remove();
											var rows_selected = table.column(0).checkboxes.selected();
											// Iterate over all selected checkboxes
											$.each(rows_selected, function(index, rowId){
												$("#employee_ids").append(
													rowId
												);
											});
											
											var selectedIds = $('#employee_ids').find(".checkbox").map(function(){return $(this).val(); }).get();
											var broj_agenata = selectedIds.length;
											
											if(broj_agenata < 1){
												//$(".material-modal__body").hide();
												alert("Niste odabrali nijednog agenta!");
												$('#editLimitsModal').modal('toggle');
												
											}else{
												$(".material-modal__body").show();
											}
											
											$("#bih_check").prop('checked', false);
											$("#srb_check").prop('checked', false);
											$("#de_check").prop('checked', false);
											$("#ostalo_check").prop('checked', false);
											$("#agent_id").val(selectedIds);
											$("#agent_name").html("Označeni agenti");
											$("#dnevni_limit").val(0);
											$("#sedmicni_limit").val(0);
											$("#mjesecni_limit").val(0);
											$("#samo_limiti").hide();
											$("#samo_drzave").hide();
											$("#uredi_limite").show();
											$("#uredi_drzave").show();
											$("#slovo_a").show();
											$("#prvi_tekst_l").show();
											$("#prvi_tekst_d").show();
											$("#drugi_tekst_l").hide();
											$("#drugi_tekst_d").hide();
											$("#edit_limit_Q").val("0");
											$("#edit_drzave_Q").val("0");
											$("#spremi_tekst").html("");
											$(".submit_btn").prop('title', 'Broj označenih agenata: '+broj_agenata);
											$(".submit_btn").prop('disabled', true);
											
											$("#check_dnevni").prop('checked', false);
											$("#check_sedmicni").prop('checked', false);
											$("#check_mjesecni").prop('checked', false);
											$("#display_dnevni").hide();
											$("#display_sedmicni").hide();
											$("#display_mjesecni").hide();
											$(".check_limits").show();
											
										}); 
										$('.uredi_samo_limite').on('click', function(){
											
											$("#samo_limiti").toggle();
											$("#drugi_tekst_l").toggle();
											$("#prvi_tekst_l").toggle();
											var edit_limit = $('input[name=edit_limit_Q').val();
											$('input[name=edit_limit_Q').val(edit_limit === "0" ? "1" : "0");
											var edit_DRZ = $('input[name=edit_drzave_Q').val();
											if(edit_DRZ == "1"){
												if($('input[name=edit_limit_Q').val() == "0"){
													$('#spremi_tekst').html("DRŽAVE");
													$(".submit_btn").removeClass('material-btn_primary');
													$(".submit_btn").addClass('material-btn_info');
													$(".uredi_samo_limite").addClass('material-btn_light');
													$(".uredi_samo_limite").removeClass('material-btn_info');
													$(".uredi_samo_limite").removeClass('material-btn_primary');
													$(".uredi_samo_drzave").removeClass('material-btn_light');
													$(".uredi_samo_drzave").addClass('material-btn_info');
													$(".uredi_samo_drzave").removeClass('material-btn_primary');
												}else{
													$('#spremi_tekst').html("SVE");
													$(".submit_btn").removeClass('material-btn_info');
													$(".submit_btn").addClass('material-btn_primary');
													$(".uredi_samo_limite").removeClass('material-btn_light');
													$(".uredi_samo_limite").removeClass('material-btn_info');
													$(".uredi_samo_limite").addClass('material-btn_primary');
													$(".uredi_samo_drzave").removeClass('material-btn_light');
													$(".uredi_samo_drzave").removeClass('material-btn_info');
													$(".uredi_samo_drzave").addClass('material-btn_primary');
												}
												$(".submit_btn").prop('disabled', false);
											}else{
												if($('input[name=edit_limit_Q').val() == "0"){
													$('#spremi_tekst').html("");
													$(".submit_btn").prop('disabled', true);
													$(".submit_btn").removeClass('material-btn_info');
													$(".submit_btn").addClass('material-btn_primary');
													$(".uredi_samo_limite").addClass('material-btn_light');
													$(".uredi_samo_limite").removeClass('material-btn_info');
													$(".uredi_samo_limite").removeClass('material-btn_primary');
													$(".uredi_samo_drzave").addClass('material-btn_light');
													$(".uredi_samo_drzave").removeClass('material-btn_info');
													$(".uredi_samo_drzave").removeClass('material-btn_primary');
												}else{
													$('#spremi_tekst').html("LIMITE");
													$(".submit_btn").prop('disabled', false);
													$(".submit_btn").removeClass('material-btn_primary');
													$(".submit_btn").addClass('material-btn_info');
													$(".uredi_samo_limite").removeClass('material-btn_light');
													$(".uredi_samo_limite").addClass('material-btn_info');
													$(".uredi_samo_limite").removeClass('material-btn_primary');
													$(".uredi_samo_drzave").addClass('material-btn_light');
													$(".uredi_samo_drzave").removeClass('material-btn_info');
													$(".uredi_samo_drzave").removeClass('material-btn_primary');
												}
											}
											
										}); 
										$('.uredi_samo_drzave').on('click', function(){
											
											$("#samo_drzave").toggle();
											$("#drugi_tekst_d").toggle();
											$("#prvi_tekst_d").toggle();
											var edit_drzave = $('input[name=edit_drzave_Q').val();
											$('input[name=edit_drzave_Q').val(edit_drzave === "0" ? "1" : "0");
											var edit_LIM = $('input[name=edit_limit_Q').val();
											if(edit_LIM == "1"){
												if($('input[name=edit_drzave_Q').val() == "0"){
													$('#spremi_tekst').html("LIMITE");
													$(".submit_btn").removeClass('material-btn_primary');
													$(".submit_btn").addClass('material-btn_info');
													$(".uredi_samo_limite").removeClass('material-btn_light');
													$(".uredi_samo_limite").addClass('material-btn_info');
													$(".uredi_samo_limite").removeClass('material-btn_primary');
													$(".uredi_samo_drzave").addClass('material-btn_light');
													$(".uredi_samo_drzave").removeClass('material-btn_info');
													$(".uredi_samo_drzave").removeClass('material-btn_primary');
												}else{
													$('#spremi_tekst').html("SVE");
													$(".submit_btn").removeClass('material-btn_info');
													$(".submit_btn").addClass('material-btn_primary');
													$(".uredi_samo_limite").removeClass('material-btn_light');
													$(".uredi_samo_limite").removeClass('material-btn_info');
													$(".uredi_samo_limite").addClass('material-btn_primary');
													$(".uredi_samo_drzave").removeClass('material-btn_light');
													$(".uredi_samo_drzave").removeClass('material-btn_info');
													$(".uredi_samo_drzave").addClass('material-btn_primary');
												}
												$(".submit_btn").prop('disabled', false);
											}else{
												if($('input[name=edit_drzave_Q').val() == "0"){
													$('#spremi_tekst').html("");
													$(".submit_btn").prop('disabled', true);
													$(".submit_btn").removeClass('material-btn_info');
													$(".submit_btn").addClass('material-btn_primary');
													$(".uredi_samo_limite").addClass('material-btn_light');
													$(".uredi_samo_limite").removeClass('material-btn_info');
													$(".uredi_samo_limite").removeClass('material-btn_primary');
													$(".uredi_samo_drzave").addClass('material-btn_light');
													$(".uredi_samo_drzave").removeClass('material-btn_info');
													$(".uredi_samo_drzave").removeClass('material-btn_primary');
												}else{
													$('#spremi_tekst').html("DRŽAVE");
													$(".submit_btn").prop('disabled', false);
													$(".submit_btn").removeClass('material-btn_primary');
													$(".submit_btn").addClass('material-btn_info');
													$(".uredi_samo_limite").addClass('material-btn_light');
													$(".uredi_samo_limite").removeClass('material-btn_info');
													$(".uredi_samo_limite").removeClass('material-btn_primary');
													$(".uredi_samo_drzave").removeClass('material-btn_light');
													$(".uredi_samo_drzave").addClass('material-btn_info');
													$(".uredi_samo_drzave").removeClass('material-btn_primary');
												}
											}
											
										}); 
									} );
								</script>
								<table id="idk_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th><input type="checkbox" id="select_all" name="select_all"></th>
											<th></th>
											<th>Ime i prezime</th>
											<th class="text-center">Tim</th>
											<th>Države</th>
											<th class="text-center">Dnevni</th>
											<th class="text-center">Sedmični</th>
											<th class="text-center">Mjesečni</th>
											<th class="text-center">Leadovi + NK1</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php
										$query = $db->prepare("
															SELECT e.employee_id, e.employee_firstname, e.employee_lastname, e.employee_status, e.employee_image, e.employee_nostrifikacija_diploma,
																	lt_drzava, lt_dnevni, lt_sedmicni, lt_mjesecni, lt_dnevni_cnt, lt_sedmicni_cnt, lt_mjesecni_cnt 
															FROM idk_nd_limiti 
															JOIN idk_employees e ON e.employee_id = lt_emp_id
															WHERE e.employee_nostrifikacija_diploma IN (0,1) AND e.employee_status != 0 $uslov_team ");

										$query->execute();

										while($row = $query->fetch()){

											$employee_id = $row['employee_id'];
											$employee_name = $row['employee_firstname']." ".$row['employee_lastname'];
											$employee_status = explode( ',' , $row['employee_status']); // LIVE
											$drzave_string = $row['lt_drzava'];
											$lt_drzava = explode( ',' , $row['lt_drzava']);
											
											$lt_drzava_img = "";
											if(in_array( "bih" , $lt_drzava)){
												$lt_drzava_img .= ' <img src="'.$getSiteUrl.'images/bs3d.png" width=25>';
											}
											if(in_array( "srb" , $lt_drzava)){
												$lt_drzava_img .= ' <img src="'.$getSiteUrl.'images/sr3d.png" width=25>';
											}
											if(in_array( "de" , $lt_drzava)){
												$lt_drzava_img .= ' <img src="'.$getSiteUrl.'images/de3d.png" width=25>';
											}
											if(in_array( "ostalo" , $lt_drzava)){
												$lt_drzava_img .= ' <img src="'.$getSiteUrl.'images/globe3d.png" title="Ostale zemlje" width=25>';
											}
											
											$query_leads = $db->prepare("
														SELECT id_broj_nd_kandidata
														FROM idk_nd_kandidata
														WHERE zaduzen_zaposlenik_nd_kandidata = :zaduzen_zaposlenik_nd_kandidata AND status_nd_kandidata = 1 AND (pstatus_nd_kandidata = 1 OR pstatus_nd_kandidata = 6)
											");
											$query_leads->execute(array(
													':zaduzen_zaposlenik_nd_kandidata' => $employee_id
											));
											$broj_leadova = $query_leads->rowCount();
												
											if($row['employee_image'] == "none"){
												$employee_image = "none.jpg";
											}else{
												$employee_image = $row['employee_image'];
											}
											
											if(intval($row['employee_nostrifikacija_diploma']) == 0){
												$employee_nost_style = "background-color: #f3413c80;";
												$employee_nost = 0;
											}else{
												$employee_nost_style = "background-color: #68c36880;";
												$employee_nost = 1;
											}
											
											$lt_drzava = $row['lt_drzava'];
											$lt_dnevni = $row['lt_dnevni'];
											$lt_sedmicni = $row['lt_sedmicni'];
											$lt_mjesecni = $row['lt_mjesecni'];
											$lt_dnevni_cnt = $row['lt_dnevni_cnt'];
											$lt_sedmicni_cnt = $row['lt_sedmicni_cnt'];
											$lt_mjesecni_cnt = $row['lt_mjesecni_cnt'];
											
											$dnevni_omjer = $lt_dnevni_cnt/$lt_dnevni;
											if($dnevni_omjer < 0.4){
												$stil_dnevni = "success";
											}elseif($dnevni_omjer >= 0.4 && $dnevni_omjer < 0.8){
												$stil_dnevni = "warning";
											}else{
												$stil_dnevni = "danger";
											}
											$sedmicni_omjer = $lt_sedmicni_cnt/$lt_sedmicni;
											if($sedmicni_omjer < 0.4){
												$stil_sedmicni = "success";
											}elseif($sedmicni_omjer >= 0.4 && $sedmicni_omjer < 0.8){
												$stil_sedmicni = "warning";
											}else{
												$stil_sedmicni = "danger";
											}
											$mjesecni_omjer = $lt_mjesecni_cnt/$lt_mjesecni;
											if($mjesecni_omjer < 0.4){
												$stil_mjesecni = "success";
											}elseif($mjesecni_omjer >= 0.4 && $mjesecni_omjer < 0.8){
												$stil_mjesecni = "warning";
											}else{
												$stil_mjesecni = "danger";
											}
											$leadovi_omjer = $broj_leadova/60;
											if($leadovi_omjer < 0.4){
												$stil_leadovi = "success";
											}elseif($leadovi_omjer >= 0.4 && $leadovi_omjer < 0.8){
												$stil_leadovi = "warning";
											}else{
												$stil_leadovi = "danger";
											}
											// var_dump($employee_image);
											// exit();
										?>
											<tr>
												<td class="text-center"><input class="checkbox" type="checkbox" name="selectedrows[<?php echo $employee_id; ?>]" value="<?php echo $employee_id; ?>"></td>
												<td class="text-center"><a href="<?php getSiteURL(); ?>dipl_agenti?page=open_agent&id=<?php echo $employee_id; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/employees/<?php echo $employee_image; ?>"></a></td>
												<td class="text-center" style = "<?php echo $employee_nost_style; ?>"><a href="<?php getSiteURL(); ?>dipl_agenti?page=open_agent&id=<?php echo $employee_id; ?>"><?php echo $employee_name; ?></a></td>
												<td class="text-center"><?php echo getIconTeam($employee_id); ?></td>
												<td><?php echo $lt_drzava_img; ?></td>
												<td class="text-center">
													<div class="progress" style="position:relative; margin-bottom: 0;">
														<span style="position: absolute; font-weight: 800; left: 0; right: 0;"><?php echo $lt_dnevni_cnt."/".$lt_dnevni; ?></span>
														<div class="progress-bar progress-bar-<?php echo $stil_dnevni?>" role="progressbar" aria-valuenow="<?php echo ($lt_dnevni_cnt/$lt_dnevni)*100; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo ($lt_dnevni_cnt/$lt_dnevni)*100; ?>%;">
														</div>
													</div>
												</td>
												<td class="text-center">
													<div class="progress" style="position:relative; margin-bottom: 0;">
														<span style="position: absolute; font-weight: 800; left: 0; right: 0;"><?php echo $lt_sedmicni_cnt."/".$lt_sedmicni; ?></span>
														<div class="progress-bar progress-bar-<?php echo $stil_sedmicni?>" role="progressbar" aria-valuenow="<?php echo ($lt_sedmicni_cnt/$lt_sedmicni)*100; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo ($lt_sedmicni_cnt/$lt_sedmicni)*100; ?>%;">
														</div>
													</div>
												</td>
												<td class="text-center">
													<div class="progress" style="position:relative; margin-bottom: 0;">
														<span style="position: absolute; font-weight: 800; left: 0; right: 0;"><?php echo $lt_mjesecni_cnt."/".$lt_mjesecni; ?></span>
														<div class="progress-bar progress-bar-<?php echo $stil_mjesecni?>" role="progressbar" aria-valuenow="<?php echo ($lt_mjesecni_cnt/$lt_mjesecni)*100; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo ($lt_mjesecni_cnt/$lt_mjesecni)*100; ?>%;">
														</div>
													</div>
												</td>
												<td class="text-center">
													<div class="progress" style="position:relative; margin-bottom: 0;">
														<span style="position: absolute; font-weight: 800; left: 0; right: 0;"><?php echo $broj_leadova."/60"; ?></span>
														<div class="progress-bar progress-bar-<?php echo $stil_leadovi?>" role="progressbar" aria-valuenow="<?php echo ($broj_leadova/60)*100; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo ($broj_leadova/60)*100; ?>%;">
														</div>
													</div>
												</td>
												<td class="text-center">
													<div class="btn-group material-btn-group">
														<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
														<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
															<li><a href="<?php getSiteURL(); ?>dipl_agenti?page=open_agent&id=<?php echo $employee_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
															<li><a href="#" class="material-dropdown-menu__link editLimits"
																	data-toggle="modal" 
																	data-target="#editLimitsModal" 
																	data-agent_id="<?php echo $employee_id; ?>"  
																	data-agent_name="<?php echo $employee_name; ?>" 
																	data-lt_dnevni="<?php echo $lt_dnevni; ?>" 
																	data-lt_sedmicni="<?php echo $lt_sedmicni; ?>" 
																	data-lt_mjesecni="<?php echo $lt_mjesecni; ?>" 
																	data-drzave_string="<?php echo $drzave_string; ?>" 
																	>
																	<i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi
																</a>
															</li>
															<li>
																<?php 
																	if($employee_nost == 0){
																?>
																<a id = "action_agent" href="#" class="material-dropdown-menu__link" 
																	data-toggle="modal" 
																	data-target="#open_action_agent"
																	data-address_ad ="<?php getSiteURL(); ?>do_dipl?form=aktiviraj_deaktiviraj&action=1&id_zap=<?php echo $employee_id; ?>"
																	data-status_ad ="<?php echo $employee_nost; ?>"
																	>
																	<i class="fa fa-check" aria-hidden="true">
																	</i> 
																	Aktiviraj
																</a>
																<?php 
																	}else{
																?>
																<a id = "action_agent" href="#" class="material-dropdown-menu__link" 
																	data-toggle="modal" 
																	data-target="#open_action_agent"
																	data-address_ad ="<?php getSiteURL(); ?>do_dipl?form=aktiviraj_deaktiviraj&action=0&id_zap=<?php echo $employee_id; ?>"
																	data-status_ad ="<?php echo $employee_nost; ?>"
																	>
																	<i class="fa fa-times" aria-hidden="true">
																	</i> 
																	Deaktiviraj
																</a>	
																<?php
																	}
																?>
															</li>
														</ul>
													</div>
												</td>
											</tr>
										<?php
										}
										?>
									</tbody>
								</table>
								<script>
									$(document).on("click","#action_agent",function() {
										var address_ad = $(this).data("address_ad");
										var status_ad = $(this).data("status_ad");
										document.getElementById("action_agent_submit").href = address_ad;
										if(status_ad == 0){
											$("#text_action_ad").html("Jeste li sigurni da želite aktivirati agenta u DIPL?");
											$("#header_action_ad").html("Aktiviranje agenta");
										}else{
											$("#text_action_ad").html("Jeste li sigurni da želite deaktivirati agenta iz DIPL-a?");
											$("#header_action_ad").html("Deaktiviranje agenta");
										}
									});
								</script>
								<!-- Modal za aktiviranje - deaktiviranje agenta -->
								<div class="modal material-modal material-modal_primary fade" id="open_action_agent">
									<div class="modal-dialog">
										<div class="modal-content material-modal__content">
											<div class="modal-header material-modal__header">
												<button class="close material-modal__close" data-dismiss="modal">&times;</button>
												<h4 class="modal-title material-modal__title">
													<p id="header_action_ad"></p>
												</h4>
											</div>
											<div class="modal-body material-modal__body">
												<div class="row">
													<div class="col-xs-12 text-left">
														<p id="text_action_ad"></p>
													</div>
												</div>
											</div>
											<div class="modal-footer material-modal__footer">
												<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
												<a id="action_agent_submit" href="#"><button class="btn btn-primary material-btn material-btn_primary">Završi</button></a>
											</div>
										</div>
									</div>
								</div>
								<!-- Modal -->
								<div class="modal material-modal material-modal_primary fade" id="editLimitsModal">
									<div class="modal-dialog">
										<div class="modal-content material-modal__content">
											<div class="modal-header material-modal__header">
												<button class="close material-modal__close" data-dismiss="modal">&times;</button>
												<h4 class="modal-title material-modal__title">Uređivanje agen<span id="slovo_a">a</span>ta</h4>
											</div>
											<form action="<?php getSiteURL(); ?>dipl_agenti?page=editLimits" method="post" http-equiv="Content-type" enctype="multipart/form-data" charset="UTF-8" class="form-horizontal" id="form_edit_limits">
												<div class="modal-body material-modal__body">	
													<h4 class="text-center"><span id="agent_name"></span></h4><hr/>
													<input type="hidden" name="agent_id[]" id="agent_id">
													<div id="uredi_limite" class="text-center " style="margin-bottom: 10px;">
														<button type="button" class="uredi_samo_limite btn material-btn material-btn_light material-btn-icon-primary material-btn-icon-responsive"><span id="prvi_tekst_l"><i class="fa fa-chevron-down" aria-hidden="true"></i>Uredi limite</span><span id="drugi_tekst_l"><i class="fa fa-chevron-up" aria-hidden="true"></i>Sakrij limite (neće se ažurirati)</span></button>
													</div>
													<input type="hidden" id="edit_limit_Q" name="edit_limit_Q">
													<div id="samo_limiti">
														<div class="form-group">
															<div class="col-md-offset-1 col-sm-10">
																<label for="dnevni_limit" class="col-sm-4 control-label" style="padding-left: 0px;">
																	Uredi dnevni limit:
																</label>
																<div class="col-sm-2 check_limits" style="margin-bottom: 15px;">
																	<div class="main-container__column materail-switch materail-switch_primary">
																		<input class="materail-switch__element" type="checkbox" id="check_dnevni" name="check_dnevni" >
																		<label class="materail-switch__label" for="check_dnevni"></label>
																	</div>
																</div>
																<div class="col-sm-6" style="margin-bottom: 15px;">
																	<div class = "row" id="display_dnevni">
																		<div class="col-sm-4 text-right">
																			<a id = "minus_dnevni" class="btn material-btn btn-danger limit_broj"><i class="fa fa-minus" aria-hidden="true"></i></a>
																		</div>
																		<div class="col-sm-6">
																			<div class="materail-input-block materail-input-block_success">
																				<input style = "text-align: center; font-size: large; font-weight: bold;" class="form-control materail-input" type="number" name="dnevni_limit" id="dnevni_limit" min = "0"  required>
																			</div>
																		</div>
																		<div class="col-sm-2 text-left">
																			<a id = "plus_dnevni" class="btn material-btn btn-success limit_broj"><i class="fa fa-plus" aria-hidden="true"></i></a> 
																		</div>
																	</div>
																</div>
															</div>
														</div>
														
														<div class="form-group">
															<div class="col-md-offset-1 col-sm-10">
																<label for="sedmicni_limit" class="col-sm-4 control-label" style="padding-left: 0px;">
																	Uredi sedmični limit:
																</label>
																<div class="col-sm-2 check_limits" style="margin-bottom: 15px;">
																	<div class="main-container__column materail-switch materail-switch_primary">
																		<input class="materail-switch__element" type="checkbox" id="check_sedmicni" name="check_sedmicni" >
																		<label class="materail-switch__label" for="check_sedmicni"></label>
																	</div>
																</div>
																<div class="col-sm-6" style="margin-bottom: 15px;">
																	<div class = "row" id="display_sedmicni">
																		<div class="col-sm-4 text-right">
																			<a id = "minus_sedmicni" class="btn material-btn btn-danger limit_broj"><i class="fa fa-minus" aria-hidden="true"></i></a>
																		</div>
																		<div class="col-sm-6">
																			<div class="materail-input-block materail-input-block_success">
																				<input style = "text-align: center; font-size: large; font-weight: bold;" class="form-control materail-input" type="number" name="sedmicni_limit" id="sedmicni_limit" min = "0" required>
																			</div>
																		</div>
																		<div class="col-sm-2 text-left">
																			<a id = "plus_sedmicni" class="btn material-btn btn-success limit_broj"><i class="fa fa-plus" aria-hidden="true"></i></a> 
																		</div>
																	</div>
																</div>
															</div>
														</div>
														<div class="form-group">
															<div class="col-md-offset-1 col-sm-10">
																<label for="mjesecni_limit" class="col-sm-4 control-label" style="padding-left: 0px;">
																	Uredi mjesečni limit:
																</label>
																<div class="col-sm-2 check_limits" style="margin-bottom: 15px;">
																	<div class="main-container__column materail-switch materail-switch_primary">
																		<input class="materail-switch__element" type="checkbox" id="check_mjesecni" name="check_mjesecni" >
																		<label class="materail-switch__label" for="check_mjesecni"></label>
																	</div>
																</div>
																<div class="col-sm-6" style="margin-bottom: 15px;">
																	<div class = "row" id="display_mjesecni">
																		<div class="col-sm-4 text-right">
																			<a id = "minus_mjesecni" class="btn material-btn btn-danger limit_broj"><i class="fa fa-minus" aria-hidden="true"></i></a>
																		</div>
																		<div class="col-sm-6">
																			<div class="materail-input-block materail-input-block_success">
																				<input style = "text-align: center; font-size: large; font-weight: bold;" class="form-control materail-input" type="number" name="mjesecni_limit" id="mjesecni_limit" min = "0" required>
																			</div>
																		</div>
																		<div class="col-sm-2 text-left">
																			<a id = "plus_mjesecni" class="btn material-btn btn-success limit_broj"><i class="fa fa-plus" aria-hidden="true"></i></a> 
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<hr/>
													<div id="uredi_drzave" class="text-center ">
														<button type="button" class="uredi_samo_drzave btn material-btn material-btn_light material-btn-icon-primary material-btn-icon-responsive"><span id="prvi_tekst_d"><i class="fa fa-chevron-down" aria-hidden="true"></i>Izmijeni države</span><span id="drugi_tekst_d"><i class="fa fa-chevron-up" aria-hidden="true"></i>Sakrij države (neće se ažurirati)</span></button>
													</div>
													<input type="hidden" id="edit_drzave_Q" name="edit_drzave_Q">
													<div id="samo_drzave">
														<div class="col-md-offset-1 col-sm-10 text-center">
															<div class="col-sm-12">
																<h4>Države koje agent zove:</h4>
															</div>
														</div>
														<div class="form-group" style="margin-bottom: 10px;">
															<div class="col-md-offset-1 col-sm-10">
																<label for="bih_check" class="col-sm-6 control-label">
																	BiH   <img src="<?php echo $getSiteUrl; ?>images/bs3d.png" width=25>
																</label>
																<div class="col-sm-6 ">
																	<div class="main-container__column materail-switch materail-switch_primary">
																		<input class="materail-switch__element" type="checkbox" id="bih_check" name="bih_check" >
																		<label class="materail-switch__label" for="bih_check"></label>
																	</div>
																</div>
															</div>
														</div>
														<div class="form-group" style="margin-bottom: 10px;">
															<div class="col-md-offset-1 col-sm-10">
																<label for="srb_check" class="col-sm-6 control-label">
																	Srbija   <img src="<?php echo $getSiteUrl; ?>images/sr3d.png" width=25>
																</label>
																<div class="col-sm-6 ">
																	<div class="main-container__column materail-switch materail-switch_primary">
																		<input class="materail-switch__element" type="checkbox" id="srb_check" name="srb_check" >
																		<label class="materail-switch__label" for="srb_check"></label>
																	</div>
																</div>
															</div>
														</div>
														<div class="form-group" style="margin-bottom: 10px;">
															<div class="col-md-offset-1 col-sm-10">
																<label for="de_check" class="col-sm-6 control-label">
																	Njemačka   <img src="<?php echo $getSiteUrl; ?>images/de3d.png" width=25>
																</label>
																<div class="col-sm-6 ">
																	<div class="main-container__column materail-switch materail-switch_primary">
																		<input class="materail-switch__element" type="checkbox" id="de_check" name="de_check" >
																		<label class="materail-switch__label" for="de_check"></label>
																	</div>
																</div>
															</div>
														</div>
														<div class="form-group" style="margin-bottom: 10px;">
															<div class="col-md-offset-1 col-sm-10">
																<label for="ostalo_check" class="col-sm-6 control-label">
																	Ostale   <img src="<?php echo $getSiteUrl; ?>images/globe3d.png" width=25>
																</label>
																<div class="col-sm-6 ">
																	<div class="main-container__column materail-switch materail-switch_primary">
																		<input class="materail-switch__element" type="checkbox" id="ostalo_check" name="ostalo_check" >
																		<label class="materail-switch__label" for="ostalo_check"></label>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="modal-footer material-modal__footer">
													<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
													<button class="btn btn-primary material-btn material-btn_primary submit_btn" type="submit" >SPREMI <span id="spremi_tekst" ></span></button>
												</div>
											</form>
											
											<script>
												$(document).on("click",".editLimits",function() {
													$(".submit_btn").prop('disabled', false);
													$("#samo_limiti").show();
													$("#samo_drzave").show();
													$("#uredi_limite").hide();
													$("#uredi_drzave").hide();
													$("#slovo_a").hide();
													$('input[name=edit_limit_Q').val("1");
													$('input[name=edit_drzave_Q').val("1");
													
													$("#display_dnevni").show();
													$("#display_sedmicni").show();
													$("#display_mjesecni").show();
													$(".check_limits").hide();
													
													$("#check_dnevni").prop('checked', true);
													$("#check_sedmicni").prop('checked', true);
													$("#check_mjesecni").prop('checked', true);
													
													$("#bih_check").prop('checked', false);
													$("#srb_check").prop('checked', false);
													$("#de_check").prop('checked', false);
													$("#ostalo_check").prop('checked', false);
													var agent_id = $(this).data("agent_id");
													var agent_name = $(this).data("agent_name");
													var lt_dnevni = $(this).data("lt_dnevni");
													var lt_sedmicni = $(this).data("lt_sedmicni");
													var lt_mjesecni = $(this).data("lt_mjesecni");
													var drzave_string = $(this).data("drzave_string");
													var drzave_array = drzave_string.split(",");
													if(jQuery.inArray("bih", drzave_array) !== -1){
														$("#bih_check").prop('checked', true);
													}
													if(jQuery.inArray("srb", drzave_array) !== -1){
														$("#srb_check").prop('checked', true);
													}
													if(jQuery.inArray("de", drzave_array) !== -1){
														$("#de_check").prop('checked', true);
													}
													if(jQuery.inArray("ostalo", drzave_array) !== -1){
														$("#ostalo_check").prop('checked', true);
													}
													$("#agent_id").val(agent_id);
													$("#agent_name").html(agent_name);
													$("#dnevni_limit").val(lt_dnevni);
													$("#sedmicni_limit").val(lt_sedmicni);
													$("#mjesecni_limit").val(lt_mjesecni);
												});
												$(document).on('click','#plus_dnevni',function(){
													$('#dnevni_limit').val(parseInt($('#dnevni_limit').val()) + 1 );
												});
												$(document).on('click','#minus_dnevni',function(){
													$('#dnevni_limit').val(parseInt($('#dnevni_limit').val()) - 1 );
													if ($('#dnevni_limit').val() <= 0) {
														$('#dnevni_limit').val(0);
													}
												});
												$(document).on('click','#plus_sedmicni',function(){
													$('#sedmicni_limit').val(parseInt($('#sedmicni_limit').val()) + 1 );
												});
												$(document).on('click','#minus_sedmicni',function(){
													$('#sedmicni_limit').val(parseInt($('#sedmicni_limit').val()) - 1 );
													if ($('#sedmicni_limit').val() <= 0) {
														$('#sedmicni_limit').val(0);
													}
												});
												$(document).on('click','#plus_mjesecni',function(){
													$('#mjesecni_limit').val(parseInt($('#mjesecni_limit').val()) + 1 );
												});
												$(document).on('click','#minus_mjesecni',function(){
													$('#mjesecni_limit').val(parseInt($('#mjesecni_limit').val()) - 1 );
													if ($('#mjesecni_limit').val() <= 0) {
														$('#mjesecni_limit').val(0);
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
		<?php
				break;
				
				case "open_skladiste":
					
					$employee_id = 139;
					$tim_id_logged = getLoggedEmployeeTeam();
					
					//if((in_array( "9" , $employee_status)) OR (in_array( "1" , $employee_status)) OR (!empty($employee_supervizor[0]))){
					if((in_array( "1" , $employee_status)) OR $logged_employee_id == 32 OR $logged_employee_id == 33)
						$uslov_team = "";
					else
						$uslov_team = " AND id_t = ".$tim_id_logged." ";
			
					
					?>
					<style>
				/* Chrome, Safari, Edge, Opera */
				input::-webkit-outer-spin-button,
				input::-webkit-inner-spin-button {
				  -webkit-appearance: none;
				  margin: 0;
				}

				/* Firefox */
				input[type=number] {
				  -moz-appearance: textfield;
				}

				
			</style>
					<div class="row">
						<div class="col-xs-8">
							<h1><i class="fa fa-users idk_color_green" aria-hidden="true"></i> Skladište </h1>
						</div>
						<div class="col-xs-4 text-right idk_margin_top10">
							<a href="<?php getSiteURL(); ?>dipl_agenti?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
						</div>
					</div>
					<hr />
					<div class="row">
						<div class="col-md-12">
							<div class="content_box">
								<div class="row">
									<div class="col-sm-12 col-md-12">
										<div class="panel-group material-accordion material-accordion_primary" id="accordion0">
											<div class="panel panel-default material-accordion__panel material-accordion__panel">
												<div class="panel-heading material-accordion__heading">
													<h4 class="panel-title">
														<div class="row" style="margin: 0;">
														<a class="material-accordion__title " style="padding: 15px 30px 35px 15px;" data-toggle="collapse" data-parent="#accordion0" href="#<?php echo "tim0"; ?>">
															<span class="col-xs-7"><?php echo "Skladište - svježi leadovi"; ?></span>
															<span class="col-xs-1"><?php echo "UK: ". getBrojKandidata(11, '000', null); ?></span>
															<span class="col-xs-1"><?php echo "BH: ". getBrojKandidata(11, '+387', null); ?></span>
															<span class="col-xs-1"><?php echo "SR: ".getBrojKandidata(11, '+381', null); ?></span>
															<span class="col-xs-1"><?php echo "DE: ".getBrojKandidata(11, '+49', null); ?></span>
															<span class="col-xs-1"><?php echo "OS: ".getBrojKandidata(11, '111', null); ?></span>
														</a>
														</div>
													</h4>
												</div>
												
												<div id="<?php echo "tim0"; ?>" class="panel-collapse collapse material-accordion__collapse">
													<div class="panel-body">
														<div class="col-sm-10 col-md-6 table-responsive" style="border: 1px solid #cccccc; margin: 15px 5px; padding: 20px; border-radius: 1.25rem; box-shadow: 0 0 10px #cccccc;">
															<table class="table table-striped">
																<thead>
																	<th class="text-center">Status</th>
																	<th class="text-center">Ukupno</th>
																	<th class="text-center">BiH</th>
																	<th class="text-center">SRB</th>
																	<th class="text-center">DEU</th>
																	<th class="text-center">Ostalo</th>
																</thead>
																<tbody>
																	<tr>
																		<td class="text-center"><span style = "background-color: #839098; color: white" class="label label-default material-label material-label_default main-container__column text-left">Lead</span></td>
																		<td class="text-center" style = "font-weight: bold;"><?php echo getBrojKandidata(11, '000', null); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(11, '+387', null); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(11, '+381', null); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(11, '+49', null); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(11, '111', null); ?></td>
																	</tr>
																</tbody>
															</table>
														</div>
														<div class="col-sm-10 col-md-10 col-lg-5" style="border: 1px solid #cccccc; margin: 15px 5px; padding: 20px; border-radius: 1.25rem; box-shadow: 0 0 10px #cccccc;">
															<h5>PREBACIVANJE KANDIDATA</h5>
															<form action="<?php getSiteURL(); ?>do_dipl?form=prebaci_iz_skladista&team=0" method="post" http-equiv="Content-type" enctype="multipart/form-data" charset="UTF-8" class="form-horizontal" id="form_prebaci_iz_skladista0">
																<div class="form-group">
																	<div class="col-md-offset-1 col-sm-10">
																		<label for="select_agent0" class="col-sm-4 control-label" style="padding-left: 0px;">
																			Agent:
																		</label>
																		<div class="col-sm-6" style="margin-bottom: 15px;">
																			<select class="selectpicker select_agent" id="select_agent0" name="select_agent0"  required>
																				<option selected disabled>Odaberi</option>
																				<?php 
																				if($logged_employee_id == 11 OR $logged_employee_id == 32 OR $logged_employee_id == 33 OR (in_array( "1" , $employee_status))){
																					$query_uslov = " employee_id is not null";
																				}else{
																					$query_uslov = " employee_id IN (".implode(", ", getIdOfEmployeeTeam($tim_id_logged))." )";
																				}
																				$query_agenti = $db->prepare("
																								SELECT employee_id, employee_firstname, employee_lastname
																								FROM idk_employees
																								WHERE ".$query_uslov." AND employee_nostrifikacija_diploma = 1
																				");
																				
																				$query_agenti->execute();
																				
																				while($row_agenti = $query_agenti->fetch()){
																					?>
																						<option value="<?php echo $row_agenti['employee_id']; ?>" data-subtext="<?php echo getTeamNameByEmployeeId($row_agenti['employee_id']); ?>" ><?php echo $row_agenti['employee_firstname']." ".$row_agenti['employee_lastname']; ?></option>
																					<?php
																				}
																				?>
																			</select>
																		</div>
																	</div>
																	<input type="hidden" id="select_statusi0" name="select_statusi0[]" value="11">
																	<div class="form-group style_drzava0">
																		<div class="col-md-offset-1 col-sm-10">
																			<label for="select_drzava0" class="col-sm-4 control-label" style="padding-left: 0px;">
																				Država:
																			</label>
																			<div class="col-sm-6" style="margin-bottom: 15px;">
																				<select class="selectpicker" id="select_drzava0" name="select_drzava0" required>
																				</select>
																			</div>
																		</div>
																	</div>
																	<div class="statusi_box0" >
																		
																	</div>
																	<div class="text-right button_prikaz0">
																		<button id="btn_prebaci0" class="btn btn-primary material-btn material-btn_primary submit_btn" type="submit" >PREBACI</span></button>
																		<p id = "loading_prebaci0" style = "display: inline; margin-left: 20px;"><i class="fa fa-spinner" aria-hidden="true"></i> Prebacivanje u toku...</p>
																	</div>
																</div>
															</form>
														</div>
													</div>
												</div>
												<script>
													$(document).ready(function() {
														$('.style_drzava0').hide();
														$('.style_statusi0').hide();
														$('.button_prikaz0').hide();
														$("#loading_prebaci0").hide();
													});
													$("#btn_prebaci0").click(function(){
														$("#btn_prebaci0").hide();
														$("#loading_prebaci0").show();
													});
													$("#select_agent0").change(function() {
														
														$('.style_drzava0').hide();
														$('.style_statusi0').hide();
														$('.statusi_box0').hide();
														
														var agent_id = $(this).val();
														// alert(agent_id);
														// var id_selecta = $(this).attr('id');
														// console.log(id_selecta);
														
														$.ajax({
															url: 'ajax_data.php?page=getSelectDrzave',
															type: 'POST',
															data: {'agent_id':agent_id},
															dataType: 'html',
															success: function(data) {
																$(".style_drzava0").show();
																$("#select_drzava0").html(data).selectpicker('refresh');
															},
															error: function (xhr, ajaxOptions, thrownError) {
																alert(xhr.status);
																alert(thrownError);
															}
														});
													});
													$("#select_drzava0").change(function() {
														
														$('.style_statusi0').hide();
														$('.statusi_box0').hide();
														var drzava_val = $(this).val();
														console.log(drzava_val);
														
														$.ajax({
															url: 'ajax_data.php?page=getStatusiBox',
															type: 'POST',
															data: {'status_val': [11], 'tim_id': 0, 'drzava_val':drzava_val,},
															dataType: 'html',
															success: function(data) {
																$(".statusi_box0").show();
																$(".statusi_box0").html(data);
															},
															error: function (xhr, ajaxOptions, thrownError) {
																alert(xhr.status);
																alert(thrownError);
															}
														});
													});
													$(document).on('click','.minus_dugme0',function(){
														var input_polje = $(this).parent().parent().find('input');
														var statusi_box = $(this).parent().parent().parent().parent();
														var suma_svih = 0;
														
														input_polje.val(parseInt(input_polje.val()) - 1 );
														if (input_polje.val() <= 0) {
															input_polje.val(0);
															
														}else{
															
														}
														statusi_box.find('input').each(function() {
															suma_svih += parseInt($(this).val());
														});
														console.log(suma_svih);
														if(suma_svih > 0){
															$('.button_prikaz0').show();
														}else{
															$('.button_prikaz0').hide();
														} 	
													});
													$(document).on('click','.plus_dugme0',function(){
														var input_polje = $(this).parent().parent().find('input');
														input_max = input_polje.attr('max');
														$('.button_prikaz0').show();
														console.log(input_polje.val());
														if(input_polje.val() == ""){
															input_polje.val(0);
															$('.button_prikaz0').hide();
														}
														input_polje.val(parseInt(input_polje.val()) + 1 );
														if (parseInt(input_polje.val()) > input_max ) {
															input_polje.val(input_max);
															$('.button_prikaz0').show();
														}
														if (parseInt(input_polje.val()) == 0) {
															input_polje.val(input_max);
															$('.button_prikaz0').hide();
														}
														
													});
													$(document).on('change','.svi_inputi0',function(){
														var input_polje = $(this);
														input_max = $(this).attr('max');
														console.log(input_polje.val());
														console.log(input_max);
														$('.button_prikaz0').show();
														if(input_polje.val() == ""){
															input_polje.val(0);
															$('.button_prikaz0').hide();
														}
														if (parseInt(input_polje.val()) > input_max || parseInt(input_polje.val()) <= 0) {
															input_polje.val(input_max);
															$('.button_prikaz0').show();
														}
													});
													
												</script>
											</div>
										</div>
									<?php 
										$tim_query = $db->prepare("
															SELECT naziv_t, id_t
															FROM idk_timovi
															WHERE status_t = 1 $uslov_team
															ORDER BY id_t ");

										$tim_query->execute();

										while($tim_row = $tim_query->fetch()){
											$id_t = $tim_row['id_t'];
											$naziv_t = $tim_row['naziv_t'];
									?>
										<div class="panel-group material-accordion material-accordion_primary" id="accordion<?php echo $id_t; ?>">
											<div class="panel panel-default material-accordion__panel material-accordion__panel">
												<div class="panel-heading material-accordion__heading">
													<h4 class="panel-title">
														<div class="row" style="margin: 0;">
														<a class="material-accordion__title " style="padding: 15px 30px 35px 15px;" data-toggle="collapse" data-parent="#accordion<?php echo $id_t; ?>" href="#<?php echo "tim".$id_t; ?>">
															<span class="col-xs-7"><?php echo "Skladište - ".$naziv_t; ?></span>
															<span class="col-xs-1"><?php echo "UK: ". getBrojKandidata(0, '000', $id_t); ?></span>
															<span class="col-xs-1"><?php echo "BH: ". getBrojKandidata(0, '+387', $id_t); ?></span>
															<span class="col-xs-1"><?php echo "SR: ".getBrojKandidata(0, '+381', $id_t); ?></span>
															<span class="col-xs-1"><?php echo "DE: ".getBrojKandidata(0, '+49', $id_t); ?></span>
															<span class="col-xs-1"><?php echo "OS: ".getBrojKandidata(0, '111', $id_t); ?></span>
														</a>
														</div>
													</h4>
												</div>
												<div id="<?php echo "tim".$id_t; ?>" class="panel-collapse collapse material-accordion__collapse">
													<div class="panel-body">
														<div class="col-sm-10 col-md-10 col-lg-6 table-responsive" style="border: 1px solid #cccccc; margin: 15px 5px; padding: 20px; border-radius: 1.25rem; box-shadow: 0 0 10px #cccccc;">
															<table class="table table-striped">
																<thead>
																	<th class="text-center">Status</th>
																	<th class="text-center">Ukupno</th>
																	<th class="text-center">BiH</th>
																	<th class="text-center">SRB</th>
																	<th class="text-center">DEU</th>
																	<th class="text-center">Ostalo</th>
																</thead>
																<tbody>
																	<tr>
																		<td class="text-center"><span style = "background-color: #839098; color: white" class="label label-default material-label material-label_default main-container__column text-left">Lead</span></td>
																		<td class="text-center" style = "font-weight: bold;"><?php echo getBrojKandidata(11, '000', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(11, '+387', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(11, '+381', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(11, '+49', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(11, '111', $id_t); ?></td>
																	</tr>
																	<tr>
																		<td class="text-center"><span style = "background-color: #00fafb; color: white" class="label label-default material-label material-label_default main-container__column text-left">Neuspješan Lead 1</span></td>
																		<td class="text-center" style = "font-weight: bold;"><?php echo getBrojKandidata(16, '000', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(16, '+387', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(16, '+381', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(16, '+49', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(16, '111', $id_t); ?></td>
																	</tr>
																	<tr>
																		<td class="text-center"><span style = "background-color: #00fb53; color: white" class="label label-default material-label material-label_default main-container__column text-left">Neuspješan Lead 3</span></td>
																		<td class="text-center" style = "font-weight: bold;"><?php echo getBrojKandidata(12, '000', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(12, '+387', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(12, '+381', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(12, '+49', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(12, '111', $id_t); ?></td>
																	</tr>
																	<tr>
																		<td class="text-center"><span style = "background-color: #0E6973; color: white" class="label label-default material-label material-label_default main-container__column text-left">Zainteresiran Lead</span></td>
																		<td class="text-center" style = "font-weight: bold;"><?php echo getBrojKandidata(13, '000', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(13, '+387', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(13, '+381', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(13, '+49', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(13, '111', $id_t); ?></td>
																	</tr>
																	<tr>
																		<td class="text-center"><span style = "background-color: #BF214B; color: white" class="label label-default material-label material-label_default main-container__column text-left">Nezainteresiran Lead</span></td>
																		<td class="text-center" style = "font-weight: bold;"><?php echo getBrojKandidata(14, '+000', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(14, '+387', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(14, '+381', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(14, '+49', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(14, '111', $id_t); ?></td>
																	</tr>
																	<tr>
																		<td class="text-center"><span style = "background-color: #c79cff; color: white" class="label label-default material-label material-label_default main-container__column text-left">U obradi Lead</span></td>
																		<td class="text-center" style = "font-weight: bold;"><?php echo getBrojKandidata(15, '000', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(15, '+387', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(15, '+381', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(15, '+49', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(15, '111', $id_t); ?></td>
																	</tr>
																	<tr>
																		<td class="text-center"><span  style = "background-color: #f2e42e;" class="label label-default material-label material-label_default main-container__column text-left">Prikupljanje dokumentacije</span></td>
																		<td class="text-center" style = "font-weight: bold;"><?php echo getBrojKandidata(2, '000', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(2, '+387', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(2, '+381', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(2, '+49', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(2, '111', $id_t); ?></td>
																	</tr>
																	<tr>
																		<td class="text-center"><span class="label label-primary material-label material-label_primary main-container__column text-left">Poslana pošta</span></td>
																		<td class="text-center" style = "font-weight: bold;"><?php echo getBrojKandidata(3, '000', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(3, '+387', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(3, '+381', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(3, '+49', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(3, '111', $id_t); ?></td>
																	</tr>
																	<tr>
																		<td class="text-center"><span class="label label-info material-label material-label_info main-container__column text-left">U obradi</span></td>
																		<td class="text-center" style = "font-weight: bold;"><?php echo getBrojKandidata(4, '000', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(4, '+387', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(4, '+381', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(4, '+49', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(4, '111', $id_t); ?></td>
																	</tr>
																	<tr>
																		<td class="text-center"><span class="label label-warning material-label material-label_warning main-container__column text-left">Dopuna dokumentacije</span></td>
																		<td class="text-center" style = "font-weight: bold;"><?php echo getBrojKandidata(5, '000', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(5, '+387', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(5, '+381', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(5, '+49', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(5, '111', $id_t); ?></td>
																	</tr>
																	<tr>
																		<td class="text-center"><span class="label label-success material-label material-label_success main-container__column text-left">Završen</span></td>
																		<td class="text-center" style = "font-weight: bold;"><?php echo getBrojKandidata(6, '000', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(6, '+387', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(6, '+381', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(6, '+49', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(6, '111', $id_t); ?></td>
																	</tr>
																	<tr>
																		<td class="text-center"><span class="label label-danger material-label material-label_danger main-container__column text-left">Arhiviran</span></td>
																		<td class="text-center" style = "font-weight: bold;"><?php echo getBrojKandidata(7, '000', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(7, '+387', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(7, '+381', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(7, '+49', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(7, '111', $id_t); ?></td>
																	</tr>
																	<tr style="border-top: 2px solid;">
																		<td class="text-center"><span class="label label-light material-label material-label_light main-container__column text-left">UKUPNO</span></td>
																		<td class="text-center" style = "font-weight: bold;"><?php echo getBrojKandidata(0, '000', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(0, '+387', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(0, '+381', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(0, '+49', $id_t); ?></td>
																		<td class="text-center"><?php echo getBrojKandidata(0, '111', $id_t); ?></td>
																	</tr>
																</tbody>
															</table>
														</div>
														<div class="col-sm-10 col-md-10 col-lg-5" style="border: 1px solid #cccccc; margin: 15px 5px; padding: 20px; border-radius: 1.25rem; box-shadow: 0 0 10px #cccccc;">
															<h5>PREBACIVANJE KANDIDATA</h5>
															<form action="<?php getSiteURL(); ?>do_dipl?form=prebaci_iz_skladista&team=<?php echo $id_t; ?>" method="post" http-equiv="Content-type" enctype="multipart/form-data" charset="UTF-8" class="form-horizontal" id="form_prebaci_iz_skladista<?php echo $id_t; ?>">
																<div class="form-group">
																	<div class="col-md-offset-1 col-sm-10">
																		<label for="select_agent<?php echo $id_t; ?>" class="col-sm-4 control-label" style="padding-left: 0px;">
																			Agent:
																		</label>
																		<div class="col-sm-6" style="margin-bottom: 15px;">
																			<select class="selectpicker select_agent" id="select_agent<?php echo $id_t; ?>" name="select_agent<?php echo $id_t; ?>"  required>
																				<option selected disabled>Odaberi</option>
																				<?php 
																				$query_agenti = $db->prepare("
																								SELECT employee_id, employee_firstname, employee_lastname
																								FROM idk_employees
																								WHERE employee_team = :employee_team AND employee_nostrifikacija_diploma = 1
																				");
																				
																				$query_agenti->execute(array(
																								":employee_team" => $id_t
																				));
																				
																				while($row_agenti = $query_agenti->fetch()){
																					?>
																						<option value="<?php echo $row_agenti['employee_id']; ?>" ><?php echo $row_agenti['employee_firstname']." ".$row_agenti['employee_lastname']; ?></option>
																					<?php
																				}
																				if($logged_employee_id == 11 OR $logged_employee_id == 32 OR $logged_employee_id == 33 OR (in_array( "1" , $employee_status))){
																					$query_tim = $db->prepare('
																						SELECT id_t, naziv_t, boja_t FROM idk_timovi WHERE status_t = 1 AND id_t != '.$id_t
																					);
																					
																					$query_tim->execute();
																					while($row_tim = $query_tim->fetch()){
																						?>
																						
																						<option style="color: <?php echo $row_tim['boja_t'];?>;  font-weight: bold;" value="<?php echo "-".$row_tim['id_t']; ?>" ><?php echo $row_tim['naziv_t']; ?></option>
																						<?php
																					}
																				}
																				
																				?>
																			</select>
																		</div>
																	</div>
																</div>
																<div class="form-group style_drzava<?php echo $id_t; ?>">
																	<div class="col-md-offset-1 col-sm-10">
																		<label for="select_drzava<?php echo $id_t; ?>" class="col-sm-4 control-label" style="padding-left: 0px;">
																			Država:
																		</label>
																		<div class="col-sm-6" style="margin-bottom: 15px;">
																			<select class="selectpicker" id="select_drzava<?php echo $id_t; ?>" name="select_drzava<?php echo $id_t; ?>" required>
																			</select>
																		</div>
																	</div>
																</div>
																<div class="form-group style_statusi<?php echo $id_t; ?>">
																	<div class="col-md-offset-1 col-sm-10">
																		<label for="select_statusi<?php echo $id_t; ?>" class="col-sm-4 control-label" style="padding-left: 0px;">
																			Statusi:
																		</label>
																		<div class="col-sm-6" style="margin-bottom: 15px;">
																			<select class="selectpicker" id="select_statusi<?php echo $id_t; ?>" name="select_statusi<?php echo $id_t; ?>[]" multiple required >
																			</select>
																		</div>
																	</div>
																</div>
																<div class="statusi_box<?php echo $id_t; ?>" >
																	
																</div>
																<div class="text-right button_prikaz<?php echo $id_t; ?>">
																	<button id="btn_prebaci<?php echo $id_t; ?>" class="btn btn-primary material-btn material-btn_primary submit_btn" type="submit" >PREBACI <span id="spremi_tekst" ></span></button>
																	<p id = "loading_prebaci<?php echo $id_t; ?>" style = "display: inline; margin-left: 20px;"><i class="fa fa-spinner" aria-hidden="true"></i> Prebacivanje u toku...</p>
																</div>
																<script>
																	$(document).ready(function() {
																		$('.style_drzava<?php echo $id_t; ?>').hide();
																		$('.style_statusi<?php echo $id_t; ?>').hide();
																		$('.button_prikaz<?php echo $id_t; ?>').hide();
																		$("#loading_prebaci<?php echo $id_t; ?>").hide();
																		
																	});
																	$("#btn_prebaci<?php echo $id_t; ?>").click(function(){
																		$("#btn_prebaci<?php echo $id_t; ?>").hide();
																		$("#loading_prebaci<?php echo $id_t; ?>").show();
																	});
																	$("#select_agent<?php echo $id_t; ?>").change(function() {
																		
																		$('.style_drzava<?php echo $id_t; ?>').hide();
																		$('.style_statusi<?php echo $id_t; ?>').hide();
																		$('.statusi_box<?php echo $id_t; ?>').hide();
																		
																		var agent_id = $(this).val();
																		// alert(agent_id);
																		// var id_selecta = $(this).attr('id');
																		// console.log(id_selecta);
																		
																		$.ajax({
																			url: 'ajax_data.php?page=getSelectDrzave',
																			type: 'POST',
																			data: {'agent_id':agent_id},
																			dataType: 'html',
																			success: function(data) {
																				$(".style_drzava<?php echo $id_t; ?>").show();
																				$("#select_drzava<?php echo $id_t; ?>").html(data).selectpicker('refresh');
																			},
																			error: function (xhr, ajaxOptions, thrownError) {
																				alert(xhr.status);
																				alert(thrownError);
																			}
																		});
																	});
																	$("#select_drzava<?php echo $id_t; ?>").change(function() {
																		
																		$('.style_statusi<?php echo $id_t; ?>').hide();
																		$('.statusi_box<?php echo $id_t; ?>').hide();
																		var novi_tim = parseInt($('#select_agent<?php echo $id_t; ?>').val());
																		var drzava_val = $(this).val();
																		//console.log(drzava_val);
																		
																		$.ajax({
																			url: 'ajax_data.php?page=getSelectStatusa',
																			type: 'POST',
																			data: {'drzava_val':drzava_val, 'tim_id': <?php echo $id_t?>, 'novi_tim':novi_tim},
																			dataType: 'html',
																			success: function(data) {
																				$(".style_statusi<?php echo $id_t; ?>").show();
																				$("#select_statusi<?php echo $id_t; ?>").html(data).selectpicker('refresh');
																			},
																			error: function (xhr, ajaxOptions, thrownError) {
																				alert(xhr.status);
																				alert(thrownError);
																			}
																		});
																	});
																	$("#select_statusi<?php echo $id_t; ?>").change(function() {
																		var status_val = $(this).val();
																		console.log(status_val);
																		//console.log(subtext);
																		var drzava_val = $(document).find('#select_drzava<?php echo $id_t?>').val();
																		//console.log(drzava_val);
																		
																		$.ajax({
																			url: 'ajax_data.php?page=getStatusiBox',
																			type: 'POST',
																			data: {'status_val':status_val, 'tim_id': <?php echo $id_t?>, 'drzava_val': drzava_val},
																			dataType: 'html',
																			success: function(data) {
																				$(".statusi_box<?php echo $id_t; ?>").show();
																				$(".statusi_box<?php echo $id_t; ?>").html(data);
																			},
																			error: function (xhr, ajaxOptions, thrownError) {
																				alert(xhr.status);
																				alert(thrownError);
																			}
																		});
																	});
																	$(document).on('click','.minus_dugme<?php echo $id_t; ?>',function(){
																		var input_polje = $(this).parent().parent().find('input');
																		var statusi_box = $(this).parent().parent().parent().parent();
																		var suma_svih = 0;
																		
																		input_polje.val(parseInt(input_polje.val()) - 1 );
																		if (input_polje.val() <= 0) {
																			input_polje.val(0);
																			
																		}else{
																			
																		}
																		statusi_box.find('input').each(function() {
																			suma_svih += parseInt($(this).val());
																		});
																		console.log(suma_svih);
																		if(suma_svih > 0){
																			$('.button_prikaz<?php echo $id_t; ?>').show();
																		}else{
																			$('.button_prikaz<?php echo $id_t; ?>').hide();
																		} 	
																	});
																	$(document).on('click','.plus_dugme<?php echo $id_t; ?>',function(){
																		var input_polje = $(this).parent().parent().find('input');
																		input_max = input_polje.attr('max');
																		$('.button_prikaz<?php echo $id_t; ?>').show();
																		//console.log(input_polje.val());
																		if(input_polje.val() == ""){
																			input_polje.val(0);
																			$('.button_prikaz<?php echo $id_t; ?>').hide();
																		}
																		input_polje.val(parseInt(input_polje.val()) + 1 );
																		if (parseInt(input_polje.val()) > input_max || parseInt(input_polje.val) <= 0) {
																			input_polje.val(input_max);
																			$('.button_prikaz<?php echo $id_t; ?>').show();
																		}
																	});
																	$(document).on('change','.svi_inputi<?php echo $id_t; ?>',function(){
																		var input_polje = $(this);
																		input_max = $(this).attr('max');
																		console.log(input_polje.val());
																		console.log(input_max);
																		$('.button_prikaz<?php echo $id_t; ?>').show();
																		if(input_polje.val() == ""){
																			input_polje.val(0);
																			$('.button_prikaz<?php echo $id_t; ?>').hide();
																		}
																		if (parseInt(input_polje.val()) > input_max || parseInt(input_polje.val()) <= 0) {
																			input_polje.val(input_max);
																			$('.button_prikaz<?php echo $id_t; ?>').show();
																		}
																	});
																</script>
															</form>
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
							</div>
						</div>
					</div>
					<?php
				break;
				
				case "add_agent":
				
					?>
					<div class="row">
						<div class="col-xs-10">
							<h1><i class="fa fa-users idk_color_green" aria-hidden="true"></i> Dodavanje DIPL agenta </h1>
						</div>
						<div class="col-xs-2 text-right idk_margin_top10">
							<a href="<?php getSiteURL(); ?>dipl_agenti?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
						</div>
						<div class="col-xs-12">
							<hr />
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="content_box">
								<div class="row">
									<div class="col-md-4 col-md-offset-4">
										<form action="<?php getSiteURL(); ?>do_dipl?form=dodaj_agenta" method="post" http-equiv="Content-type" enctype="multipart/form-data" charset="UTF-8" class="form-horizontal" id="form_prebaci_iz_skladista<?php echo $id_t; ?>">
											<div class="form-group" style = "margin-top: 25px;">
												<label for="select_agent" class="col-sm-4 control-label text-right" style="padding-left: 0px;">
													<span class="text-danger">*</span>
													Agent:
												</label>
												<div class="col-sm-8">
													<select class="selectpicker select_agent" id="select_agent" name="select_agent"  data-live-search="true" required>
														<option selected disabled>Odaberi</option>
														<?php 
														$tim_id_logged = getLoggedEmployeeTeam();
														if((in_array( "1" , $employee_status)) OR $logged_employee_id == 32 OR $logged_employee_id == 33){
															$uslov_team = "employee_team is not null";
														}
														else{
															$uslov_team = "employee_team = ".$tim_id_logged." ";
														}
														$query_agenti = $db->prepare("
																		SELECT employee_id, employee_firstname, employee_lastname
																		FROM idk_employees
																		WHERE ".$uslov_team." AND employee_nostrifikacija_diploma is NULL AND employee_status != 0 AND employee_id != 139 AND employee_status != 0
														");
														
														$query_agenti->execute();
														
														while($row_agenti = $query_agenti->fetch()){
															?>
																<option value="<?php echo $row_agenti['employee_id']; ?>" ><?php echo $row_agenti['employee_firstname']." ".$row_agenti['employee_lastname']; ?></option>
															<?php
														}
														?>
													</select>
												</div>
											</div>
											<div class="form-group" style = "margin-top: 25px;">
												<label for="select_drzava" class="col-sm-4 control-label text-right">
													<span class="text-danger">*</span>
													Države koje agent zove:
												</label>
												<div class="col-sm-8">
													<select class="selectpicker select_drzava" id="select_drzava" name="select_drzava[]" multiple required>
														<option value="bih">BiH</option>
														<option value="srb">Srbija</option>
														<option value="de">Njemačka</option>
														<option value="ostalo">Ostale</option>
														
													</select>
												</div>
											</div>
											<div class="form-group" style = "margin-top: 25px;">
												<label for="dnevni_limit" class="col-sm-4 control-label text-right">
													<span class="text-danger">*</span>
													Dnevni limit:
												</label>
												<div class="col-sm-8">
													<div class = "row" id="display_dnevni">
														<div class="col-sm-4 text-right">
															<a id = "minus_dnevni" class="btn material-btn btn-danger limit_broj"><i class="fa fa-minus" aria-hidden="true"></i></a>
														</div>
														<div class="col-sm-4">
															<div class="materail-input-block materail-input-block_success">
																<input style = "text-align: center; font-size: large; font-weight: bold;" class="form-control materail-input svi_inputi" type="number" name="dnevni_limit" id="dnevni_limit" min = "0" value=0 required>
															</div>
														</div>
														<div class="col-sm-4 text-left">
															<a id = "plus_dnevni" class="btn material-btn btn-success limit_broj"><i class="fa fa-plus" aria-hidden="true"></i></a> 
														</div>
													</div>
												</div>
											</div>
											<div class="form-group" style = "margin-top: 25px;">
												<label for="sedmicni_limit" class="col-sm-4 control-label text-right">
													<span class="text-danger">*</span>
													Sedmični limit:
												</label>
												<div class="col-sm-8">
													<div class = "row" id="display_dnevni">
														<div class="col-sm-4 text-right">
															<a id = "minus_sedmicni" class="btn material-btn btn-danger limit_broj"><i class="fa fa-minus" aria-hidden="true"></i></a>
														</div>
														<div class="col-sm-4">
															<div class="materail-input-block materail-input-block_success">
																<input style = "text-align: center; font-size: large; font-weight: bold;" class="form-control materail-input svi_inputi" type="number" name="sedmicni_limit" id="sedmicni_limit" min = "0" value=0 required>
															</div>
														</div>
														<div class="col-sm-4 text-left">
															<a id = "plus_sedmicni" class="btn material-btn btn-success limit_broj"><i class="fa fa-plus" aria-hidden="true"></i></a> 
														</div>
													</div>
												</div>
											</div>
											<div class="form-group" style = "margin-top: 25px;">
												<label for="mjesecni_limit" class="col-sm-4 control-label text-right">
													<span class="text-danger">*</span>
													Mjesečni limit:
												</label>
												<div class="col-sm-8">
													<div class = "row" id="display_dnevni">
														<div class="col-sm-4 text-right">
															<a id = "minus_mjesecni" class="btn material-btn btn-danger limit_broj"><i class="fa fa-minus" aria-hidden="true"></i></a>
														</div>
														<div class="col-sm-4">
															<div class="materail-input-block materail-input-block_success">
																<input style = "text-align: center; font-size: large; font-weight: bold;" class="form-control materail-input svi_inputi" type="number" name="mjesecni_limit" id="mjesecni_limit" min = "0" value=0 required>
															</div>
														</div>
														<div class="col-sm-4 text-left">
															<a id = "plus_mjesecni" class="btn material-btn btn-success limit_broj"><i class="fa fa-plus" aria-hidden="true"></i></a> 
														</div>
													</div>
												</div>
											</div>
											<div class="form-group" style = "margin-top: 50px;">
												<div class="col-sm-12 text-center">
													<button class="btn btn-primary material-btn material-btn_primary submit_btn" type="submit" >
														<i class="fa fa-plus" aria-hidden="true" style = "margin-right: 10px;">
														</i>
														Dodaj 
													</button>
												</div>
											</div>
											<div class="row" style = "margin-top: 15px;">
												<div class="col-sm-12 text-center">
													<small>
														Sva polja označena sa 
														<span class="text-danger">
															*
														</span>  
														su obavezna!
													</small>
												</div>
											</div>
											
											<script>
												$(document).on('click','#plus_dnevni',function(){
													$('#dnevni_limit').val(parseInt($('#dnevni_limit').val()) + 1 );
												});
												$(document).on('click','#minus_dnevni',function(){
													$('#dnevni_limit').val(parseInt($('#dnevni_limit').val()) - 1 );
													if ($('#dnevni_limit').val() <= 0) {
														$('#dnevni_limit').val(0);
													}
												});
												$(document).on('click','#plus_sedmicni',function(){
													$('#sedmicni_limit').val(parseInt($('#sedmicni_limit').val()) + 1 );
												});
												$(document).on('click','#minus_sedmicni',function(){
													$('#sedmicni_limit').val(parseInt($('#sedmicni_limit').val()) - 1 );
													if ($('#sedmicni_limit').val() <= 0) {
														$('#sedmicni_limit').val(0);
													}
												});
												$(document).on('click','#plus_mjesecni',function(){
													$('#mjesecni_limit').val(parseInt($('#mjesecni_limit').val()) + 1 );
												});
												$(document).on('click','#minus_mjesecni',function(){
													$('#mjesecni_limit').val(parseInt($('#mjesecni_limit').val()) - 1 );
													if ($('#mjesecni_limit').val() <= 0) {
														$('#mjesecni_limit').val(0);
													}
												});
												$(document).on('change','.svi_inputi',function(){
													var input_polje = $(this);
													//console.log(input_polje.val());
													if(input_polje.val() == ""){
														input_polje.val(0);
													}
												});
											</script>
											
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php
				
				break;
				case "editLimits":
				
					$ids = $_POST['agent_id'];
					$edit_limit_Q = $_POST['edit_limit_Q'];
					$edit_drzave_Q = $_POST['edit_drzave_Q'];
					$check_dnevni = $_POST['check_dnevni'];
					$check_sedmicni = $_POST['check_sedmicni'];
					$check_mjesecni = $_POST['check_mjesecni'];
					$n_dnevni = $_POST['dnevni_limit'];
					$n_sedmicni = $_POST['sedmicni_limit'];
					$n_mjesecni = $_POST['mjesecni_limit'];
					$bih_check = $_POST['bih_check'];
					$srb_check = $_POST['srb_check'];
					$de_check = $_POST['de_check'];
					$ostalo_check = $_POST['ostalo_check'];
					
					// var_dump($check_dnevni);echo "<br/>";
					// var_dump($check_sedmicni);echo "<br/>";
					// var_dump($check_mjesecni);echo "<br/>";
					// var_dump($edit_limit_Q);echo "<br/>";
					// var_dump($edit_drzave_Q);echo "<br/>";
					// exit();
					
					$q_dr="";
					$q_id="";
					$q_lt="";
					$br=0;
					$drzave_n=NULL;
					if($edit_drzave_Q == 1){

						if($bih_check=="on")
							$drzave_n[]="bih";

						if($srb_check=="on")
							$drzave_n[]="srb";

						if($de_check=="on")
							$drzave_n[]="de";

						if($ostalo_check=="on")
							$drzave_n[]="ostalo";
						
						$drzava=implode(",",$drzave_n);   
						$q_dr="lt_drzava = '".$drzava."'";
					}
					
					$id = explode(',',$ids[0]);
					$id = implode(",", $id);
					$q_id ="  lt_emp_id IN (".$id.")";
					
					if($edit_limit_Q != "0"){

						if($n_dnevni != NULL && $check_dnevni=="on")
							$q_lt=$q_lt."lt_dnevni= ".$n_dnevni.", ";
						
						
						if($n_sedmicni != NULL && $check_sedmicni=="on")
							$q_lt=$q_lt."lt_sedmicni= ".$n_sedmicni.", ";
						
					   
						if($n_mjesecni != NULL && $check_mjesecni=="on")
							$q_lt=$q_lt."lt_mjesecni= ".$n_mjesecni.", ";
						
						$q_lt=substr($q_lt,0,-2);
					}
					
					if($q_dr != "" && $q_lt != "")
						$q_dr=$q_dr.", ";    

					
					$query_set=$db->prepare("
						UPDATE idk_nd_limiti
						SET ".$q_dr.$q_lt."
						WHERE ".$q_id
					
					);
					// var_dump("UPDATE idk_nd_limiti
						// SET ".$q_dr.$q_lt."
						// WHERE ".$q_id);
					// exit();
					
					$query_set->execute();
					
					header("Location: " . getSiteURLr() . "dipl_agenti?page=list");
				break;
				
				case "open_agent":
					
					$id = $_GET['id'];
                    
					?>
					<div class="row">
						<div class="col-xs-8">
                            <?php
                                $q_getname=$db->prepare('
                                    SELECT employee_firstname, employee_lastname, employee_status, employee_image FROM idk_employees WHERE employee_id='.$id
                                );
                               
                                $q_getname->execute();
                                $tmp=$q_getname->fetch();
                                $ime=$tmp['employee_firstname'];
                                $prezime=$tmp['employee_lastname'];
								$emp_status=$tmp['employee_status'];
								$emp_img=$tmp['employee_image'];
                            
                            ?>
							<input type="hidden" id="emp_st" name="emp_st" value="<?php echo $emp_status; ?>">
							<input type="hidden" id="emp_id" name="emp_id" value="<?php echo $id; ?>">
							<h1><i class="fa fa-users idk_color_green" aria-hidden="true"></i><?php echo "  ".$ime." ".$prezime; ?></h1>
						</div>
						<div class="col-xs-4 text-right idk_margin_top10">
							<a href="<?php getSiteURL(); ?>dipl_agenti?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
						</div>
						
					</div>
					<hr />
					<div class="row">
						<div class="col-md-12">
							<div class="content_box">
								<div class="row">
									<div class="col-xs-12">
                                    <style>
									.fadeInUp {
										-webkit-animation-name: fadeInUp;
										animation-name: fadeInUp;
										-webkit-animation-duration: 1s;
										animation-duration: 1s;
										-webkit-animation-fill-mode: both;
										animation-fill-mode: both;
									}
									@-webkit-keyframes fadeInUp {
										0% {
											opacity: 0;
											-webkit-transform: translate3d(0, 100%, 0);
											transform: translate3d(0, 100%, 0);
										}
										100% {
											opacity: 1;
											-webkit-transform: none;
											transform: none;
										}
									}
									@keyframes fadeInUp {
										0% {
											opacity: 0;
											-webkit-transform: translate3d(0, 100%, 0);
											transform: translate3d(0, 100%, 0);
										}
										100% {
											opacity: 1;
											-webkit-transform: none;
											transform: none;
										}
									} 
									#card_zaposlenik_DIPL{
										border: 1px solid #cccccc; margin: 10px 40px; padding: 20px; border-radius: 1.25rem; box-shadow: 0 0 10px #cccccc;
									}
									.fancybox img{ width: 80px !important; height: 80px !important; border: 3px solid #ffffff; float: none !important;
									}

									.plus_minus_prebaci{
										border-radius: 50% !important;
									
										margin-right: 0px !important;
										margin-left: 0px !important;
									
									}
									/*kod koji bi mogao zeznuti nesto drugo na stranic*/
									input::-webkit-outer-spin-button,
									input::-webkit-inner-spin-button {
										-webkit-appearance: none;
										margin: 0;
									}

									/* Firefox */
									input[type=number] {
										-moz-appearance: textfield;
									}
								</style>
										<div id = "card_zaposlenik_DIPL" class="row fadeInUp">
											<div class = "col-xs-12">
												<div class="row" style = "padding: 10px; background-color: #6097a0; border-radius: 1.25rem 1.25rem 0 0;"> 
													<div class = "col-xs-12 text-center">
														<a class="fancybox" rel="group" href="<?php echo getSiteUrlr().'files/employees/'.$emp_img; ?>"><img class="idk_profile_img" src="<?php echo getSiteUrlr().'files/employees/'.$emp_img; ?>"></a>
													</div> 
													<div class = "col-xs-12 text-center" style = "font-weight: bold; font-size: x-large; color: white; word-break: break-all;"> 
														<?php echo $ime." ".$prezime;?>
													</div>
												</div>
												<div class="row" style = "padding-top: 10px;">
													<div class = "col-xs-12">
														<div class="table-responsive">
														<?php
														$q_getcountstatusi=$db->prepare('
														select 
														sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=1 AND zaduzen_zaposlenik_nd_kandidata = '.$id.' THEN 1 else 0 END) AS statLead,
														sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=6 AND zaduzen_zaposlenik_nd_kandidata = '.$id.' THEN 1 else 0 END) AS Nk1,
														sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=2 AND zaduzen_zaposlenik_nd_kandidata = '.$id.' THEN 1 else 0 END) AS Nk3,
														sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=3 AND zaduzen_zaposlenik_nd_kandidata = '.$id.' THEN 1 else 0 END) AS Zld,
														sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=4 AND zaduzen_zaposlenik_nd_kandidata = '.$id.' THEN 1 else 0 END) AS Nzld,
														sum(case when status_nd_kandidata=1 AND pstatus_nd_kandidata=5 AND zaduzen_zaposlenik_nd_kandidata = '.$id.' THEN 1 else 0 END) AS Uobld,
														sum(case when status_nd_kandidata=2 AND zaduzen_zaposlenik_nd_kandidata = '.$id.' THEN 1 else 0 END) AS Prdok,
														sum(case when status_nd_kandidata=3 AND zaduzen_zaposlenik_nd_kandidata = '.$id.' THEN 1 else 0 END) AS Pospos,
														sum(case when status_nd_kandidata=4 AND zaduzen_zaposlenik_nd_kandidata = '.$id.' THEN 1 else 0 END) AS Obrada,
														sum(case when status_nd_kandidata=5 AND zaduzen_zaposlenik_nd_kandidata = '.$id.' THEN 1 else 0 END) AS Dopdok,
														sum(case when status_nd_kandidata=6 AND zaduzen_zaposlenik_nd_kandidata = '.$id.' THEN 1 else 0 END) AS Zavrs,
														sum(case when status_nd_kandidata=7 AND zaduzen_zaposlenik_nd_kandidata = '.$id.' THEN 1 else 0 END) AS Arhiva
														FROM idk_nd_kandidata
														');

														$q_getcountstatusi->execute();
														$row_cntst=$q_getcountstatusi->fetch();

														$lead=$row_cntst['statLead'];
														$nk1=$row_cntst['Nk1'];
														$nk3=$row_cntst['Nk3'];
														$zld=$row_cntst['Zld'];
														$nzld=$row_cntst['Nzld'];
														$uobld=$row_cntst['Uobld'];
														$prdok=$row_cntst['Prdok'];
														$pospos=$row_cntst['Pospos'];
														$obrada=$row_cntst['Obrada'];
														$dopdok=$row_cntst['Dopdok'];
														$zavrs=$row_cntst['Zavrs'];
														$arhiva=$row_cntst['Arhiva'];
														
																	
														?>
                                                        <div class="row ">
                                                            <div class="col-md-offset-2 col-sm-8 text-cente">
                                                                <div id = "mess_success_can" class="alert alert-success text-center" role="alert">
                                                                    Uspjesno prebaceno!
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row ">
                                                            <div class="col-md-offset-2 col-sm-8 text-cente">
                                                                <div id = "mess_fail_can" class="alert alert-danger text-center" role="alert">
                                                                    Unesite broj kandidata
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <script>
                                                        
                                                            $(document).ready(function () {
                                                                $('#mess_success_can').hide();
                                                            });
                                                            $(document).ready(function () {
                                                                $('#mess_fail_can').hide();
                                                            });
                                                            
                                                        </script>
															<table class="table table-striped" style = "margin-bottom: 0px;">
																<thead>
																	<tr>
																		<th class="text-center" style="width: 30%;">Status</th>	
																		<th class="text-center" style="width: 30%;">Broj kandidata</th>	
																		<th class="text-center" style="width: 15%;"></th>	
																		<th class="text-right" id="btn_prebaci" style="display: none; width:25%;"><a href="#" class="btn text-right material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><span class="text-right"  ><i class="fa fa-briefcase" aria-hidden="true"></i> Prebaci kandidate na skladište</span></a></th>
																	</tr>
																</thead>
																<tbody>

																	<tr>
																		<td class="text-center"><span style = "background-color: #839098; color: white" class="label label-default material-label material-label_default main-container__column text-left">Lead</span></td>
																		<td class="text-center max_st" style = "font-weight: bold;" id="td_lead"><?php echo $lead;?></td>
																		<td class="text-center">
																			<div class="main-container__column materail-switch materail-switch_primary">
																				<input class="materail-switch__element"  type="checkbox" id="check_lead" name="check_lead" >
																				<label class="materail-switch__label" for="check_lead"></label>
																			</div>
																		</td>
																		<td class="text-center">
																			<div id="uredi_lead" style="display: none;">
																				<div class="col-sm-2 text-right">
																					<a id = "minus_lead" class="plus_minus_prebaci btn material-btn btn-danger" ><i class="fa fa-minus" aria-hidden="true"></i></a>
																				</div>
																				<div class="col-sm-5 col-sm-offset-1 text-center">
																					<div class="materail-input-block materail-input-block_success">
																						<input value="0" style = "text-align: center; font-weight: bold;border-radius:15px; border-bottom: 2px solid #4092d9;" class="form-control materail-input br_kand" type="number" name="input_lead" id="input_lead" min = "0" required>
																					</div>
																				</div>
																				<div class="col-sm-2 text-left">
																					<a id = "plus_lead" class="plus_minus_prebaci btn material-btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i></a> 
																				</div>
																			</div>
																		</td>
																		
																	</tr>
																	<tr>
																		<td class="text-center"><span style = "background-color: #00fafb; color: white" class="label label-default material-label material-label_default main-container__column text-left">Neuspješan Lead 1</span></td>
																		<td class="text-center max_st" style = "font-weight: bold;" id="td_nk1"><?php echo $nk1;?></td>
																		<td>
																			<div class="main-container__column materail-switch materail-switch_primary">
																				<input class="materail-switch__element" type="checkbox" id="check_nk1" name="check_nk1" >
																				<label class="materail-switch__label" for="check_nk1"></label>
																			</div>
																		</td>
																		<td>
																			<div id="uredi_nk1" style="display: none;">
																				<div class="col-sm-2 text-right">
																					<a id = "minus_nk1" class="plus_minus_prebaci btn material-btn btn-danger" ><i class="fa fa-minus" aria-hidden="true"></i></a>
																				</div>
																				<div class="col-sm-5 col-sm-offset-1 text-center">
																					<div class="materail-input-block materail-input-block_success">
																						<input value="0" name="input_nk1" id="input_nk1" style = "text-align: center; font-weight: bold;border-radius:15px; border-bottom: 2px solid #4092d9;" class="form-control materail-input br_kand" type="number" min = "0" required>
																					</div>
																				</div>
																				<div class="col-sm-2 text-left">
																					<a id = "plus_nk1" class="plus_minus_prebaci btn material-btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i></a> 
																				</div>
																			</div>
																		</td>
																	</tr>
																	<tr>
																		<td class="text-center"><span style = "background-color: #00fb53; color: white" class="label label-default material-label material-label_default main-container__column text-left">Neuspješan Lead 3</span></td>
																		<td class="text-center max_st" style = "font-weight: bold;" id="td_nk3"><?php echo $nk3;?></td>
																		<td>
																			<div class="main-container__column materail-switch materail-switch_primary">
																				<input class="materail-switch__element" type="checkbox" id="check_nk3" name="check_nk3" >
																				<label class="materail-switch__label" for="check_nk3"></label>
																			</div>
																		</td>
																		<td>
																			<div id="uredi_nk3" style="display: none;">
																				<div class="col-sm-2 text-right">
																					<a id = "minus_nk3" class="plus_minus_prebaci btn material-btn btn-danger" ><i class="fa fa-minus" aria-hidden="true"></i></a>
																				</div>
																				<div class="col-sm-5 col-sm-offset-1 text-center">
																					<div class="materail-input-block materail-input-block_success">
																						<input value="0" name="input_nk3" id="input_nk3" style = "text-align: center; font-weight: bold;border-radius:15px; border-bottom: 2px solid #4092d9;" class="form-control materail-input br_kand" type="number" min = "0" required>
																					</div>
																				</div>
																				<div class="col-sm-2 text-left">
																					<a id = "plus_nk3" class="plus_minus_prebaci btn material-btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i></a> 
																				</div>
																			</div>
																		</td>
																	</tr>
																	<tr>
																		<td class="text-center"><span style = "background-color: #0E6973; color: white" class="label label-default material-label material-label_default main-container__column text-left">Zainteresiran Lead</span></td>
																		<td class="text-center max_st" style = "font-weight: bold;" id="td_zld"><?php echo $zld;?></td>
																		<td>
																			<div class="main-container__column materail-switch materail-switch_primary">
																				<input class="materail-switch__element" type="checkbox" id="check_zld" name="check_zld" >
																				<label class="materail-switch__label" for="check_zld"></label>
																			</div>
																		</td>
																		<td>
																			<div id="uredi_zld" style="display: none;">
																				<div class="col-sm-2 text-right">
																					<a id = "minus_zld" class="plus_minus_prebaci btn material-btn btn-danger" ><i class="fa fa-minus" aria-hidden="true"></i></a>
																				</div>
																				<div class="col-sm-5 col-sm-offset-1 text-center">
																					<div class="materail-input-block materail-input-block_success">
																						<input value="0" name="input_zld" id="input_zld" style = "text-align: center; font-weight: bold;border-radius:15px; border-bottom: 2px solid #4092d9;" class="form-control materail-input br_kand" type="number" min = "0" required>
																					</div>
																				</div>
																				<div class="col-sm-2 text-left">
																					<a id = "plus_zld" class="plus_minus_prebaci btn material-btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i></a> 
																				</div>
																			</div>
																		</td>
																	</tr>
																	<tr>
																		<td class="text-center"><span style = "background-color: #BF214B; color: white" class="label label-default material-label material-label_default main-container__column text-left">Nezainteresiran Lead</span></td>
																		<td class="text-center max_st" style = "font-weight: bold;" id="td_nzld"><?php echo $nzld;?></td>
																		<td>
																			<div class="main-container__column materail-switch materail-switch_primary">
																				<input class="materail-switch__element" type="checkbox" id="check_nzld" name="check_nzld" >
																				<label class="materail-switch__label" for="check_nzld"></label>
																			</div>
																		</td>
																		<td>
																			<div id="uredi_nzld" style="display: none;">
																				<div class="col-sm-2 text-right">
																					<a id = "minus_nzld" class="plus_minus_prebaci btn material-btn btn-danger" ><i class="fa fa-minus" aria-hidden="true"></i></a>
																				</div>
																				<div class="col-sm-5 col-sm-offset-1 text-center">
																					<div class="materail-input-block materail-input-block_success">
																						<input value="0" name="input_nzld" id="input_nzld" style = "text-align: center; font-weight: bold;border-radius:15px; border-bottom: 2px solid #4092d9;" class="form-control materail-input br_kand" type="number" min = "0" required>
																					</div>
																				</div>
																				<div class="col-sm-2 text-left">
																					<a id = "plus_nzld" class="plus_minus_prebaci btn material-btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i></a> 
																				</div>
																			</div>
																		</td>
																	</tr>
																	<?php if($emp_status==0){
																	?>

																
																		<tr>
																			<td class="text-center"><span style = "background-color: #c79cff; color: white" class="label label-default material-label material-label_default main-container__column text-left">U obradi Lead</span></td>
																			<td class="text-center max_st" style = "font-weight: bold;" id="td_uobld"><?php echo $uobld;?></td>
																			<td>
																				<div class="main-container__column materail-switch materail-switch_primary">
																					<input class="materail-switch__element" type="checkbox" id="check_uobld" name="check_uobld" >
																					<label class="materail-switch__label" for="check_uobld"></label>
																				</div>
																			</td>
																			<td>
																				<div id="uredi_uobld" style="display: none;">
																					<div class="col-sm-2 text-right">
																						<a id = "minus_uobld" class="plus_minus_prebaci btn material-btn btn-danger" ><i class="fa fa-minus" aria-hidden="true"></i></a>
																					</div>
																					<div class="col-sm-5 col-sm-offset-1 text-center">
																						<div class="materail-input-block materail-input-block_success">
																							<input value="0" name="input_uobld" id="input_uobld" style = "text-align: center; font-weight: bold;border-radius:15px; border-bottom: 2px solid #4092d9;" class="form-control materail-input br_kand" type="number" min = "0" required>
																						</div>
																					</div>
																					<div class="col-sm-2 text-left">
																						<a id = "plus_uobld" class="plus_minus_prebaci btn material-btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i></a> 
																					</div>
																				</div>
																			</td>
																		</tr>
																		<tr>
																			<td class="text-center"><span  style = "background-color: #f2e42e;" class="label label-default material-label material-label_default main-container__column text-left">Prikupljanje dokumentacije</span></td>
																			<td class="text-center max_st" style = "font-weight: bold;" id="td_prdok"><?php echo $prdok;?></td>
																			<td>
																				<div class="main-container__column materail-switch materail-switch_primary">
																					<input class="materail-switch__element" type="checkbox" id="check_prdok" name="check_prdok">
																					<label class="materail-switch__label" for="check_prdok"></label>
																				</div>
																			</td>
																			<td>
																				<div id="uredi_prdok" style="display: none;">
																					<div class="col-sm-2 text-right">
																						<a id = "minus_prdok" class="plus_minus_prebaci btn material-btn btn-danger" ><i class="fa fa-minus" aria-hidden="true"></i></a>
																					</div>
																					<div class="col-sm-5 col-sm-offset-1 text-center">
																						<div class="materail-input-block materail-input-block_success">
																							<input value="0" name="input_prdok" id="input_prdok" style = "text-align: center; font-weight: bold;border-radius:15px; border-bottom: 2px solid #4092d9;" class="form-control materail-input br_kand" type="number" min = "0" required>
																						</div>
																					</div>
																					<div class="col-sm-2 text-left">
																						<a id = "plus_prdok" class="plus_minus_prebaci btn material-btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i></a> 
																					</div>
																				</div>
																			</td>
																		</tr>
																		<tr>
																			<td class="text-center"><span class="label label-primary material-label material-label_primary main-container__column text-left">Poslana pošta</span></td>
																			<td class="text-center max_st" style = "font-weight: bold;" id="td_pospos"><?php echo $pospos;?></td>
																			<td>
																				<div class="main-container__column materail-switch materail-switch_primary">
																					<input class="materail-switch__element" type="checkbox" id="check_pospos" name="check_pospos">
																					<label class="materail-switch__label" for="check_pospos"></label>
																				</div>
																			</td>
																			<td>
																				<div id="uredi_pospos" style="display: none;">
																					<div class="col-sm-2 text-right">
																						<a id = "minus_pospos" class="plus_minus_prebaci btn material-btn btn-danger" ><i class="fa fa-minus" aria-hidden="true"></i></a>
																					</div>
																					<div class="col-sm-5 col-sm-offset-1 text-center">
																						<div class="materail-input-block materail-input-block_success">
																							<input value="0" name="input_pospos" id="input_pospos" style = "text-align: center; font-weight: bold;border-radius:15px; border-bottom: 2px solid #4092d9;" class="form-control materail-input br_kand" type="number" min = "0" required>
																						</div>
																					</div>
																					<div class="col-sm-2 text-left">
																						<a id = "plus_pospos" class="plus_minus_prebaci btn material-btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i></a> 
																					</div>
																				</div>
																			</td>
																		</tr>
																		<tr>
																			<td class="text-center"><span class="label label-info material-label material-label_info main-container__column text-left">U obradi</span></td>
																			<td class="text-center max_st" style = "font-weight: bold;" id="td_obrada"><?php echo $obrada;?></td>
																			<td>
																				<div class="main-container__column materail-switch materail-switch_primary">
																					<input class="materail-switch__element" type="checkbox" id="check_obrada" name="check_obrada" >
																					<label class="materail-switch__label" for="check_obrada"></label>
																				</div>
																			</td>
																			<td>
																				<div id="uredi_obrada" style="display: none;">
																					<div class="col-sm-2 text-right">
																						<a id = "minus_obrada" class="plus_minus_prebaci btn material-btn btn-danger" ><i class="fa fa-minus" aria-hidden="true"></i></a>
																					</div>
																					<div class="col-sm-5 col-sm-offset-1 text-center">
																						<div class="materail-input-block materail-input-block_success">
																							<input value="0" name="input_obrada" id="input_obrada" style = "text-align: center; font-weight: bold;border-radius:15px; border-bottom: 2px solid #4092d9;" class="form-control materail-input br_kand" type="number" min = "0" required>
																						</div>
																					</div>
																					<div class="col-sm-2 text-left">
																						<a id = "plus_obrada" class="plus_minus_prebaci btn material-btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i></a> 
																					</div>
																				</div>
																			</td>
																		</tr>
																		<tr>
																			<td class="text-center"><span class="label label-warning material-label material-label_warning main-container__column text-left">Dopuna dokumentacije</span></td>
																			<td class="text-center max_st" style = "font-weight: bold;" id="td_dopdok"><?php echo $dopdok;?></td>
																			<td>
																				<div class="main-container__column materail-switch materail-switch_primary">
																					<input class="materail-switch__element" type="checkbox" id="check_dopdok" name="check_dopdok" >
																					<label class="materail-switch__label" for="check_dopdok"></label>
																				</div>
																			</td>
																			<td>
																				<div id="uredi_dopdok" style="display: none;">
																					<div class="col-sm-2 text-right">
																						<a id = "minus_dopdok" class="plus_minus_prebaci btn material-btn btn-danger" ><i class="fa fa-minus" aria-hidden="true"></i></a>
																					</div>
																					<div class="col-sm-5 col-sm-offset-1 text-center">
																						<div class="materail-input-block materail-input-block_success">
																							<input value="0" name="input_dopdok" id="input_dopdok" style = "text-align: center; font-weight: bold;border-radius:15px; border-bottom: 2px solid #4092d9;" class="form-control materail-input br_kand" type="number" min = "0" required>
																						</div>
																					</div>
																					<div class="col-sm-2 text-left">
																						<a id = "plus_dopdok" class="plus_minus_prebaci btn material-btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i></a> 
																					</div>
																				</div>
																			</td>
																		</tr>
																		<tr>
																			<td class="text-center"><span class="label label-success material-label material-label_success main-container__column text-left">Završen</span></td>
																			<td class="text-center max_st" style = "font-weight: bold;" id="td_zavrs"><?php echo $zavrs;?></td>
																			<td>
																				<div class="main-container__column materail-switch materail-switch_primary">
																					<input class="materail-switch__element" type="checkbox" id="check_zavrs" name="check_zavrs">
																					<label class="materail-switch__label" for="check_zavrs"></label>
																				</div>
																			</td>
																			<td>
																				<div id="uredi_zavrs" style="display: none;">
																					<div class="col-sm-2 text-right">
																						<a id = "minus_zavrs" class="plus_minus_prebaci btn material-btn btn-danger" ><i class="fa fa-minus" aria-hidden="true"></i></a>
																					</div>
																					<div class="col-sm-5 col-sm-offset-1 text-center">
																						<div class="materail-input-block materail-input-block_success">
																							<input value="0" name="input_zavrs" id="input_zavrs" style = "text-align: center; font-weight: bold;border-radius:15px; border-bottom: 2px solid #4092d9;" class="form-control materail-input br_kand" type="number" min = "0" required>
																						</div>
																					</div>
																					<div class="col-sm-2 text-left">
																						<a id = "plus_zavrs" class="plus_minus_prebaci btn material-btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i></a> 
																					</div>
																				</div>
																			</td>
																		</tr>
																	<?php
																	}
																	?>
																	<tr>
																		<td class="text-center"><span class="label label-danger material-label material-label_danger main-container__column text-left">Arhiviran</span></td>
																		<td class="text-center max_st" style = "font-weight: bold;" id="td_arhiva"><?php echo $arhiva;?></td>
																		<td>
																			<div class="main-container__column materail-switch materail-switch_primary">
																				<input class="materail-switch__element" type="checkbox" id="check_arhiva" name="check_arhiva" >
																				<label class="materail-switch__label" for="check_arhiva"></label>
																			</div>
																		</td>
																		<td>
																			<div id="uredi_arhiva" style="display: none;">
																				<div class="col-sm-2 text-right">
																					<a id = "minus_arhiva" class="plus_minus_prebaci btn material-btn btn-danger" ><i class="fa fa-minus" aria-hidden="true"></i></a>
																				</div>
																				<div class="col-sm-5 col-sm-offset-1 text-center">
																					<div class="materail-input-block materail-input-block_success">
																						<input value="0" name="input_arhiva" id="input_arhiva" style = "text-align: center; font-weight: bold;border-radius:15px; border-bottom: 2px solid #4092d9;" class="form-control materail-input br_kand" type="number" min = "0" required>
																					</div>
																				</div>
																				<div class="col-sm-2 text-left">
																					<a id = "plus_arhiva" class="plus_minus_prebaci btn material-btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i></a> 
																				</div>
																			</div>
																		</td>
																	</tr>
																</tbody>
															</table>
															<script>

                                                                $(document).on('change','.br_kand',function(){
                                                                    var input_polje = $(this);
                                                                    var max_broj = parseInt(input_polje.parent().parent().parent().parent().parent().find('.max_st').text());
                                                                    
                                                                    if(input_polje.val()=="" || input_polje.val()<0){
                                                                        input_polje.val(0);
                                                                    }
                                                                    else if(input_polje.val()>max_broj){
                                                                        input_polje.val(max_broj);
                                                                    }
                                                                    
                                                                });
																function count_checks(){
																	var status=parseInt(document.getElementById("emp_st").value);
																	
																	if(status==0){
																		var checks=[
																			document.getElementById("check_lead").checked,
																			document.getElementById("check_nk1").checked,
																			document.getElementById("check_nk3").checked,
																			document.getElementById("check_zld").checked,
																			document.getElementById("check_uobld").checked,
																			document.getElementById("check_prdok").checked,
																			document.getElementById("check_pospos").checked,
																			document.getElementById("check_obrada").checked,
																			document.getElementById("check_dopdok").checked,
																			document.getElementById("check_zavrs").checked,
																			document.getElementById("check_arhiva").checked,
																			document.getElementById("check_nzld").checked
																		]
																	
																		var i=0;
																		var cnt=0;
																		for (i;i<12;i++){
																			if (checks[i]==true)
																				cnt++;
																		}

																		return cnt;
																		}
																	else{
																		var checks=[
																			document.getElementById("check_lead").checked,
																			document.getElementById("check_nk1").checked,
																			document.getElementById("check_nk3").checked,
																			document.getElementById("check_zld").checked,
																			document.getElementById("check_arhiva").checked,
																			document.getElementById("check_nzld").checked
																		]
																		
																		var i=0;
																		var cnt=0;
																		for (i;i<6;i++){
																			if (checks[i]==true)
																				cnt++;
																		}

																		return cnt;

																	}
																	
																}

																function inc(input,limit){
																	var i=parseInt(document.getElementById(input).value);
																	var lt=parseInt(document.getElementById(limit).textContent);
																	
																	if(i<lt)
																		i=i+1;
																	document.getElementById(input).value=parseInt(i);
																}
																function dec(input){
																	var i=parseInt(document.getElementById(input).value);
																	i=i-1;
																	if(i<0)
																		i=0;
																	document.getElementById(input).value=parseInt(i);
																}
																
																$(document).on("click","#plus_lead",function(){
																	inc("input_lead","td_lead");
																});
																$(document).on("click","#plus_nk1",function(){
																	inc("input_nk1","td_nk1");
																});
																$(document).on("click","#plus_nk3",function(){
																	inc("input_nk3","td_nk3");
																});
																$(document).on("click","#plus_zld",function(){
																	inc("input_zld","td_zld");
																});
																$(document).on("click","#plus_nzld",function(){
																	inc("input_nzld","td_nzld");
																});
																$(document).on("click","#plus_uobld",function(){
																	inc("input_uobld","td_uobld");
																});
																$(document).on("click","#plus_prdok",function(){
																	inc("input_prdok","td_prdok");
																});
																$(document).on("click","#plus_pospos",function(){
																	inc("input_pospos","td_pospos");
																});
																$(document).on("click","#plus_obrada",function(){
																	inc("input_obrada","td_obrada");
																});
																$(document).on("click","#plus_dopdok",function(){
																	inc("input_dopdok","td_dopdok");
																});
																$(document).on("click","#plus_zavrs",function(){
																	inc("input_zavrs","td_zavrs");
																});
																$(document).on("click","#plus_arhiva",function(){
																	inc("input_arhiva","td_arhiva");
																});
																
																$(document).on("click","#minus_lead",function(){
																	dec("input_lead");
																});
																$(document).on("click","#minus_nk1",function(){
																	dec("input_nk1");
																});
																$(document).on("click","#minus_nk3",function(){
																	dec("input_nk3");
																});
																$(document).on("click","#minus_zld",function(){
																	dec("input_zld");
																});
																$(document).on("click","#minus_nzld",function(){
																	dec("input_nzld");
																});
																$(document).on("click","#minus_uobld",function(){
																	dec("input_uobld");
																});
																$(document).on("click","#minus_prdok",function(){
																	dec("input_prdok");
																});
																$(document).on("click","#minus_pospos",function(){
																	dec("input_pospos");
																});
																$(document).on("click","#minus_obrada",function(){
																	dec("input_obrada");
																});
																$(document).on("click","#minus_dopdok",function(){
																	dec("input_dopdok");
																});
																$(document).on("click","#minus_zavrs",function(){
																	dec("input_zavrs");
																});
																$(document).on("click","#minus_arhiva",function(){
																	dec("input_arhiva");
																});

																$(document).on("click","#check_lead",function() {
																	count_checks();
																	var x = document.getElementById("uredi_lead");
																	var y = document.getElementById("btn_prebaci");
																	if(x.style.display=="none"){
																		x.style.display="block";
																		y.style.display="block";
																	}
																		  
																	else{
																		x.style.display="none";
																		if(count_checks()==0)
																			y.style.display="none";
																	}
																});
																$(document).on("click","#check_nk1",function() {			
																	var x = document.getElementById("uredi_nk1");
																	var y = document.getElementById("btn_prebaci");
                                                                    
																	if(x.style.display=="none"){
																		x.style.display="block";
                                                                        y.style.display="block";
																	}
																		  
																	else{
																		x.style.display="none";
																		if(count_checks()==0)
																			y.style.display="none";
																	}
                                                                                                                                    
																});
																$(document).on("click","#check_nk3",function() {			
																	var x = document.getElementById("uredi_nk3");
																	var y = document.getElementById("btn_prebaci");
																	if(x.style.display=="none"){
																		x.style.display="block";
																		y.style.display="block";
																	}
																		  
																	else{
																		x.style.display="none";
																		if(count_checks()==0)
																			y.style.display="none";
																	}
																});
																$(document).on("click","#check_zld",function() {			
																	var x = document.getElementById("uredi_zld");
																	var y = document.getElementById("btn_prebaci");
																	if(x.style.display=="none"){
																		x.style.display="block";
																		y.style.display="block";
																	}
																		  
																	else{
																		x.style.display="none";
																		if(count_checks()==0)
																			y.style.display="none";
																	}
																});
																$(document).on("click","#check_nzld",function() {			
																	var x = document.getElementById("uredi_nzld");
																	var y = document.getElementById("btn_prebaci");
																	if(x.style.display=="none"){
																		x.style.display="block";
																		y.style.display="block";
																	}
																		  
																	else{
																		x.style.display="none";
																		if(count_checks()==0)
																			y.style.display="none";
																	}
																});
																$(document).on("click","#check_uobld",function() {			
																	var x = document.getElementById("uredi_uobld");
																	var y = document.getElementById("btn_prebaci");
																	if(x.style.display=="none"){
																		x.style.display="block";
																		y.style.display="block";
																	}
																		  
																	else{
																		x.style.display="none";
																		if(count_checks()==0)
																			y.style.display="none";
																	}
																});
																$(document).on("click","#check_prdok",function() {			
																	var x = document.getElementById("uredi_prdok");
																	var y = document.getElementById("btn_prebaci");
																	if(x.style.display=="none"){
																		x.style.display="block";
																		y.style.display="block";
																	}
																		  
																	else{
																		x.style.display="none";
																		if(count_checks()==0)
																			y.style.display="none";
																	}
																});
																$(document).on("click","#check_pospos",function() {			
																	var x = document.getElementById("uredi_pospos");
																	var y = document.getElementById("btn_prebaci");
																	if(x.style.display=="none"){
																		x.style.display="block";
																		y.style.display="block";
																	}
																		  
																	else{
																		x.style.display="none";
																		if(count_checks()==0)
																			y.style.display="none";
																	}
																});
																$(document).on("click","#check_obrada",function() {			
																	var x = document.getElementById("uredi_obrada");
																	var y = document.getElementById("btn_prebaci");
																	if(x.style.display=="none"){
																		x.style.display="block";
																		y.style.display="block";
																	}
																		  
																	else{
																		x.style.display="none";
																		if(count_checks()==0)
																			y.style.display="none";
																	}
																});
																$(document).on("click","#check_dopdok",function() {			
																	var x = document.getElementById("uredi_dopdok");
																	var y = document.getElementById("btn_prebaci");
																	if(x.style.display=="none"){
																		x.style.display="block";
																		y.style.display="block";
																	}
																		  
																	else{
																		x.style.display="none";
																		if(count_checks()==0)
																			y.style.display="none";
																	}
																});
																$(document).on("click","#check_zavrs",function() {			
																	var x = document.getElementById("uredi_zavrs");
																	var y = document.getElementById("btn_prebaci");
																	if(x.style.display=="none"){
																		x.style.display="block";
																		y.style.display="block";
																	}
																		  
																	else{
																		x.style.display="none";
																		if(count_checks()==0)
																			y.style.display="none";
																	}
																});
																$(document).on("click","#check_arhiva",function() {			
																	var x = document.getElementById("uredi_arhiva");
																	var y = document.getElementById("btn_prebaci");
																	if(x.style.display=="none"){
																		x.style.display="block";
																		y.style.display="block";
																	}
																		  
																	else{
																		x.style.display="none";
																		if(count_checks()==0)
																			y.style.display="none";
																	}
																});
                                                                function check_nule(){

                                                                    
                                                                    
                                                                }
																$(document).on("click","#btn_prebaci",function(){

                                                                    
                                                                    document.getElementById("btn_prebaci").style.visibility = "hidden";
																	var id = document.getElementById("emp_id").value;
																	var st = parseInt(document.getElementById("emp_st").value);
																	var i=0;
																	var qhelper=[];
																	if(document.getElementById("check_lead").checked){
																		qhelper[i]="lead,"+document.getElementById("input_lead").value;
																		i=i+1;
																	}
																	if(document.getElementById("check_nk1").checked){
																		qhelper[i]="nk1,"+document.getElementById("input_nk1").value;
																		i=i+1;
																	}
																	if(document.getElementById("check_nk3").checked){
																		qhelper[i]="nk3,"+document.getElementById("input_nk3").value;
																		i=i+1;
																	}
																	if(document.getElementById("check_zld").checked){
																		qhelper[i]="zld,"+document.getElementById("input_zld").value;
																		i=i+1;
																	}
																	if(document.getElementById("check_nzld").checked){
																		qhelper[i]="nzld,"+document.getElementById("input_nzld").value;
																		i=i+1;
																	}
																	if(document.getElementById("check_arhiva").checked){
																		qhelper[i]="arhiva,"+document.getElementById("input_arhiva").value;
																		i=i+1;
																	}
                                                                  
																	if(st==0){
																		if(document.getElementById("check_uobld").checked){
																		qhelper[i]="uobld,"+document.getElementById("input_uobld").value;
																		i=i+1;
																		}
																		if(document.getElementById("check_prdok").checked){
																			qhelper[i]="prdok,"+document.getElementById("input_prdok").value;
																			i=i+1;
																		}
																		if(document.getElementById("check_pospos").checked){
																			qhelper[i]="pospos,"+document.getElementById("input_pospos").value;
																			i=i+1;
																		}
																		if(document.getElementById("check_obrada").checked){
																			qhelper[i]="obrada,"+document.getElementById("input_obrada").value;
																			i=i+1;
																		}
																		if(document.getElementById("check_dopdok").checked){
																			qhelper[i]="dopdok,"+document.getElementById("input_dopdok").value;
																			i=i+1;
																		}
																		if(document.getElementById("check_zavrs").checked){
																			qhelper[i]="zavrs,"+document.getElementById("input_zavrs").value;
																			i=i+1;
																		}
																	}
                                                                    foundnula=false;
                                                                    for (j=0; j<qhelper.length; j++){
                                                                        if (qhelper[j].includes(",0"))
                                                                            foundnula=true;
                                                                    }
                                                                    if(foundnula==true){
                                                                        document.getElementById("btn_prebaci").style.visibility = "visible";
                                                                        $('#mess_fail_can').show();
                                                                        setTimeout(function(){
                                                                                $('#mess_fail_can').hide();
                                                                            }, 3000);
                                                                    }
                                                                    else{
																		console.log(qhelper);
																		
																		$.ajax({ url: 'ajax_data.php?page=prebaci_na_skladiste',
																			data: {'qhelper': qhelper,'emp_id': id},
																			type: 'post',
																			success: function(result) {
																				$('#mess_success_can').show();
																				setTimeout(function(){
																					$('#mess_success_can').hide();
																					window.location.reload();
																				}, 1000);
																																															
																			}   
																		});  

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
						</div>
					</div>
				<?php
				case "logovi":
				?>
<!--TABELA LOGOVI PREBACIVANJA KANDIDATA SA SKLADIŠTE JEDNOG TIMA NA SKLADIŠTE DRUGOG___________________________________________________________________________START -->

					<h1> <i class="fa fa-eye idk_color_green" aria-hidden="true"></i> Logovi prebacivanja kandidata:</h1>
					<hr>
					<div class="row">
						<div class="col-md-12">
							<div class="content_box">
								<div class="panel-group material-accordion material-accordion_success" id="accordion1" style="margin-bottom: 5px;">
									<script type="text/javascript">
		
										$(document).ready(function() { 
											var table = $('#skl_u_skl').DataTable({

												responsive: true,
												"order": [[ 2, "desc" ]],

												 "bAutoWidth": false,

												"aoColumns": [
														{ "width": "15%"},
														{ "width": "75%" },
														{ "width": "10%"}
													]
											});
										});
									</script>
									
									<div class="panel panel-default material-accordion__panel material-accordion__panel" style="margin-bottom: 5px;">
										<div class="panel-heading material-accordion__heading">
											<h4 class="panel-title">
												<a class="material-accordion__title" data-toggle="collapse" data-parent="#accordion1" href="#skl_skl">
												Iz SKLADIŠTA  u drugo  SKLADIŠTE
												<span style="float:right;">
													<i style="width: 20px;" class="fa fa-archive" aria-hidden="true"></i> <i style="width: 20px;" class="fa fa-arrow-right" aria-hidden="true"></i> <i style="width: 20px;" class="fa fa-archive" aria-hidden="true"></i>
												</span>
												</a>
											</h4>
										</div>
										<div id="skl_skl" class="panel-collapse collapse material-accordion__collapse panel-body">
											<table id="skl_u_skl" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th class="text-center">Zaposlenik</th>
														<th class="text-center">Opis loga</th>
														<th class="text-center">Datum</th>
														<th></th>
													</tr>
												</thead>
												<tbody>
													<?php
													$query_skl_skl = $db->prepare("
														SELECT log_employeeid, log_desc, date(log_date) as log_date FROM `idk_logs` WHERE `log_desc` LIKE '%skladište tima%' ORDER BY `log_date` ASC
													");
													
													$query_skl_skl->execute();
													while($row_skl_skl = $query_skl_skl->fetch()){
														
														$log_desc = replaceTimLoga($row_skl_skl['log_desc']);
														$log_ids = substr($log_desc,strpos($log_desc,"(")+1,strpos($log_desc,")")-strpos($log_desc,"(")-1);
														$log_ids_links = replaceIdLoga($log_ids);
														$log_desc = str_replace("kandidate (".$log_ids,"kandidate (".$log_ids_links,$log_desc);
														

													?>
														<tr>
															<td class="text-center"><?php echo getZaposlenikimeR($row_skl_skl['log_employeeid']);?></td>
															<td class="text-center"><?php echo $log_desc;?></td>
															<td class="text-center"><?php echo $row_skl_skl['log_date'];?></td>
															
														</tr>
													<?php
													}
													?>
												</tbody>
											</table>
										</div>
									</div>
					<?php					
	//TABELA LOGOVI PREBACIVANJA KANDIDATA SA SKLADIŠTE JEDNOG TIMA NA SKLADIŠTE DRUGOG___________________________________________________________________________END 
					?>
					
					
					
					
	<!--TABELA LOGOVI PREBACIVANJA KANDIDATA IZ SKLADIŠTA NEKOM ZAPOSLENIKU_______________________________________________________________________________________START -->

									<script type="text/javascript">
		
										$(document).ready(function() { 
											var table = $('#skl_u_zap').DataTable({

												responsive: true,
												"order": [[ 2, "desc" ]],

												 "bAutoWidth": false,

												"aoColumns": [
														{ "width": "15%"},
														{ "width": "75%" },
														{ "width": "10%"}
													]
											});
										});
									</script>
									
									<div class="panel panel-default material-accordion__panel material-accordion__panel" style="margin-bottom: 5px;">
										<div class="panel-heading material-accordion__heading">
											<h4 class="panel-title">
												<a class="material-accordion__title" data-toggle="collapse" data-parent="#accordion1" href="#skl_zap">
												Iz SKLADIŠTA nekom AGENTU
												<span style="float:right;">
													<i style="width: 20px;" class="fa fa-archive" aria-hidden="true"></i> <i style="width: 20px;" class="fa fa-arrow-right" aria-hidden="true"></i> <i style="width: 20px;" class="fa fa-user" aria-hidden="true"></i>
												</span>
												</a>
											</h4>
										</div>
										<div id="skl_zap" class="panel-collapse collapse material-accordion__collapse panel-body">
											<table id="skl_u_zap" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th class="text-center">Zaposlenik</th>
														<th class="text-center">Opis loga</th>
														<th class="text-center">Datum</th>
														<th></th>
													</tr>
												</thead>
												<tbody>
													<?php
													$query_skl_zap = $db->prepare("
														SELECT log_employeeid, log_desc, date(log_date) as log_date FROM `idk_logs` WHERE `log_desc` LIKE '%iz skladišta zaposleniku%' ORDER BY `log_date` ASC
													");
													
													$query_skl_zap->execute();
													while($row_skl_zap = $query_skl_zap->fetch()){
														$log_desc = substr($row_skl_zap['log_desc'],8);

														$log_ids = substr($log_desc,strpos($log_desc,"[")+1,strpos($log_desc,"]")-strpos($log_desc,"[")-1);
														$log_ids_links = replaceIdLoga($log_ids);
														$log_desc = str_replace($log_ids,$log_ids_links,$log_desc);
														$log_fullname = substr($log_desc,strpos($log_desc,"[",strpos($log_desc,"[")+1)+1,strpos($log_desc,"]",strpos($log_desc,"]")+1)-strpos($log_desc,"[",strpos($log_desc,"[")+1)-1);
														$log_desc = str_replace("t/i ID = [".$log_ids,"t/i ID = [".$log_ids_links,$log_desc);
														$log_desc = str_replace("niku ID = [".$log_fullname."]","niku <b>".getZaposlenikimeR($log_fullname)."</b>",$log_desc);
														$log_desc = str_replace("niku id = [".$log_fullname."]","niku <b>".getZaposlenikimeR($log_fullname)."</b>",$log_desc);
														
														$log_desc = str_replace("id = []","<i><b>id =*nije poznato* </b></i>",$log_desc);
														$log_desc = str_replace("ID = []","<i><b>id =*nije poznato* </b></i>",$log_desc);
														

													?>
													
														<tr>
															<td class="text-center"><?php echo getZaposlenikimeR($row_skl_zap['log_employeeid']);?></td>
															<td class="text-center"><?php echo $log_desc;?></td>
															<td class="text-center"><?php echo $row_skl_zap['log_date'];?></td>
															
														</tr>
													<?php

													}
													?>
												</tbody>
											</table>
										</div>
									</div>
								
					<?php
	//TABELA LOGOVI PREBACIVANJA KANDIDATA IZ SKLADIŠTA NEKOM ZAPOSLENIKU_______________________________________________________________________________________END		
	?>





	<!--TABELA LOGOVI PREBACIVANJA KANDIDATA JEDNOG ZAPOSLENIKA DRUGOM_______________________________________________________________________________________START -->

									<script type="text/javascript">
		
										$(document).ready(function() { 
											var table = $('#zap_u_zap').DataTable({

												responsive: true,
												"order": [[ 2, "desc" ]],

												 "bAutoWidth": false,

												"aoColumns": [
														{ "width": "15%"},
														{ "width": "75%" },
														{ "width": "10%"}
													]
											});
										});
									</script>
									<div class="panel panel-default material-accordion__panel material-accordion__panel" style="margin-bottom: 5px;">
										<div class="panel-heading material-accordion__heading">
											<h4 class="panel-title">
												<a class="material-accordion__title" data-toggle="collapse" data-parent="#accordion1" href="#zap_zap">
													Sa AGENTA nekom drugom AGENTU
													<span style="float:right;">
														<i style="width: 20px;" class="fa fa-user" aria-hidden="true"></i> <i style="width: 20px;" class="fa fa-arrow-right" aria-hidden="true"></i> <i style="width: 20px;" class="fa fa-user" aria-hidden="true"></i>
													</span>
												</a>
												
											</h4>
										</div>
										<div id="zap_zap" class="panel-collapse collapse material-accordion__collapse panel-body">
											<table id="zap_u_zap" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th class="text-center">Zaposlenik</th>
														<th class="text-center">Opis loga</th>
														<th class="text-center">Datum</th>
														<th></th>
													</tr>
												</thead>
												<tbody>
													<?php
													$query_zap_zap = $db->prepare("
														SELECT log_employeeid, log_desc, date(log_date) as log_date FROM `idk_logs` WHERE `log_desc` LIKE '%prebacio zaduženje nostrifikacije diplome%' ORDER BY `log_date` ASC
													");
													
													$query_zap_zap->execute();
													while($row_zap_zap = $query_zap_zap->fetch()){
														$log_desc = substr($row_zap_zap['log_desc'],8);
														
														$id_zap1 = getStringBetween($log_desc,"nik ID = [","] preb");
														$id_zap2 = getStringBetween($log_desc,"nika ID = [","] na");
														$id_zap3 = getStringBetween($log_desc,"na zaposlenika ID = [","] za");
														$id_kan = getStringBetween($log_desc,"kandidata ID = [","].");
														
														$log_desc=str_replace("nik ID = [".$id_zap1."]", "nik: <b>".getZaposlenikimeR($id_zap1)."</b>",$log_desc);
														$log_desc=str_replace("nika ID = [".$id_zap2."]", "nika: <b>".getZaposlenikimeR($id_zap2)."</b>",$log_desc);
														$log_desc=str_replace("na zaposlenika ID = [".$id_zap3."] za", "na zaposlenika: <b>".getZaposlenikimeR($id_zap3)."</b>",$log_desc);
														$log_desc=str_replace("kandidata ID = [".$id_kan."].", "kandidata ID = [".replaceIdLoga($id_kan)."]",$log_desc);
														
														// $log_desc = str_replace("[]","<i><b>*nije poznato*</b></i>",$log_desc);
														// $log_desc = str_replace("()","<i><b>*nije poznato*</b><i>",$log_desc);
														
													?>
													
														<tr>
															<td class="text-center"><?php echo getZaposlenikimeR($row_zap_zap['log_employeeid']);?></td>
															<td class="text-center"><?php echo $log_desc;?></td>
															<td class="text-center"><?php echo $row_zap_zap['log_date'];?></td>
															
														</tr>
													<?php

													}
													?>
												</tbody>
											</table>
										</div>
									</div>
								
					<?php
	//TABELA LOGOVI PREBACIVANJA KANDIDATA JEDNOG ZAPOSLENIKA DRUGOM_______________________________________________________________________________________________END	
	?>




	<!--TABELA LOGOVI PREBACIVANJA KANDIDATA SA NEKOG ZAPOSLENIKA U SKLADIŠTE_______________________________________________________________________________________START -->

									<script type="text/javascript">
		
										$(document).ready(function() { 
											var table = $('#zap_u_skl').DataTable({

												responsive: true,
												"order": [[ 2, "desc" ]],

												 "bAutoWidth": false,

												"aoColumns": [
														{ "width": "15%"},
														{ "width": "75%" },
														{ "width": "10%"}
													]
											});
										});
									</script>
									<div class="panel panel-default material-accordion__panel material-accordion__panel" style="margin-bottom: 5px;">
										<div class="panel-heading material-accordion__heading">
											<h4 class="panel-title">
												<a class="material-accordion__title" data-toggle="collapse" data-parent="#accordion1" href="#zap_skl">	
													Sa AGENTA na SKLADIŠTE
													<span style="float:right;">
														<i style="width: 20px;" class="fa fa-user" aria-hidden="true"></i> <i style="width: 20px;" class="fa fa-arrow-right" aria-hidden="true"></i> <i style="width: 20px;" class="fa fa-archive" aria-hidden="true"></i>
													</span>
												</a>
											</h4>
										</div>
										<div id="zap_skl" class="panel-collapse collapse material-accordion__collapse panel-body">
											<table id="zap_u_skl" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th class="text-center">Zaposlenik</th>
														<th class="text-center">Opis loga</th>
														<th class="text-center">Datum</th>
														<th></th>
													</tr>
												</thead>
												<tbody>
													<?php
													$query_zap_skl = $db->prepare("
														SELECT log_employeeid, log_desc, date(log_date) as log_date FROM idk_logs WHERE log_desc LIKE '%prebacuje kandidate%' AND log_desc LIKE '%sa agenta%'
														ORDER BY `idk_logs`.`log_desc`  DESC
													");
													
													$query_zap_skl->execute();
													while($row_zap_skl = $query_zap_skl->fetch()){
																									
														$log_desc = $row_zap_skl['log_desc'];
														$log_desc = getZaposlenikimeR($row_zap_skl['log_employeeid']).$log_desc;
														$log_desc = str_replace(getStringBetween($log_desc, getZaposlenikimeR($row_zap_skl['log_employeeid']), " preb"), "", $log_desc);
														
														$log_ids = getStringBetween($log_desc, "kandidate(",") sa");
														
														if($log_ids != ""){
															$log_ids_link = replaceidLoga($log_ids);
															$log_desc = str_replace("kandidate(".$log_ids.") sa", "kandidate (".$log_ids_link.") sa", $log_desc);
														}
														
														else {
															$log_desc = str_replace("[]","<i><b>*nije poznato*</b></i>",$log_desc);
															$log_desc = str_replace("()","<i><b>*nije poznato*</b></i>",$log_desc);
														}
														
														
														$id_agenta = getStringBetween($log_desc, "sa agenta ", " u s");
														$log_desc = str_replace("sa agenta ".$id_agenta." u s","sa agenta: <b>".getZaposlenikimeR($id_agenta)."</b> u s", $log_desc);
														
													?>
													
														<tr>
															<td class="text-center"><?php echo getZaposlenikimeR($row_zap_skl['log_employeeid']);?></td>
															<td class="text-center"><?php echo $log_desc;?></td>
															<td class="text-center"><?php echo $row_zap_skl['log_date'];?></td>
															
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
				<?php
//TABELA LOGOVI PREBACIVANJA KANDIDATA SA NEKOG ZAPOSLENIKA U SKLADIŠTE_______________________________________________________________________________________END			
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