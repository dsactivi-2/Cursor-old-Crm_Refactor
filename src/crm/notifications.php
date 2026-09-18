<?php
	include("includes/functions.php");
	include("includes/common.php");
	$getEmployeeStatus = getEmployeeStatus();

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: employees?page=list");
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

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-bell-o idk_color_green" aria-hidden="true"></i> Notifikacije</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
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
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_table').DataTable({

											responsive: true,

											"order": [[ 0, "desc" ]],

											 "bAutoWidth": false,

											"aoColumns": [
													{ "width": "5%" },
													{ "width": "60%" },
													{ "width": "15%" },
													{ "width": "20%" }
												]
										});
									} );
								</script>
								<table id="idk_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th></th>
											<th>Tekst</th>
											<th>Datum</th>
											<th>Status</th>
										</tr>
									</thead>
									<tbody>
									<?php
									$query_notifications = $db->prepare("
													SELECT not_id, not_text, not_url, not_date, not_status
													FROM idk_notification
													WHERE not_type = 1
													ORDER BY not_id DESC
													");

									$query_notifications->execute();

									while($notification = $query_notifications->fetch()){

										$not_id = $notification['not_id'];
										$not_text = $notification['not_text'];
										$not_url = $notification['not_url'];
										$not_date = $notification['not_date'];
										$not_date_f = date('d.m.Y H:i', strtotime($not_date));
										$not_status = $notification['not_status'];

										if($not_status == 0){
											$notstats = '<span class="label label-warning material-label material-label_warning main-container__column">NIJE OTVORENO</span>';
											$class_for_new_message = "idk_new_notification";
										}else{
											$notstats = '<span class="label label-success material-label material-label_success main-container__column">OTVORENO</span>';
											$class_for_new_message = "";
										}

									?>
										<tr>
											<td><?php echo $not_id; ?></td>
											<td><a href="<?php getSiteUrl(); ?><?php echo $not_url; ?>&update_not=1"><?php echo $not_text; ?></a></td>
											<td><span class="label label-success material-label material-label_success main-container__column"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $not_date_f; ?></span></td>
											<td class="text-center"><?php echo $notstats; ?></td>
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
				
				case "list_ponovne_prijave":

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-bell-o idk_color_green" aria-hidden="true"></i> Ponovne prijave</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
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
								
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_table').DataTable({

											responsive: true,

											"order": [[ 0, "desc" ]],

											 "bAutoWidth": false,

											"aoColumns": [
													{ "width": "5%" },
													{ "width": "20%" },
													{ "width": "25%" },
													{ "width": "25%" },
													{ "width": "15%" },
													{ "width": "10%" }
												]
										});
									} );
								</script>
								<table id="idk_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th></th>
											<th>Tekst</th>
											<th>Link</th>
											<th>Nalog</th>
											<th>Datum</th>
											<th>Status</th>
										</tr>
									</thead>
									<tbody>
									<?php
									$query_notifications = $db->prepare("
													SELECT not_id, not_text, not_url, not_date, not_status, not_kandidatid
													FROM idk_notification
													WHERE not_type = 2
													ORDER BY not_id DESC
													");

									$query_notifications->execute();

									while($notification = $query_notifications->fetch()){

										$not_id = $notification['not_id'];
										$not_text = $notification['not_text'];
										$not_url = $notification['not_url'];
										$not_date = $notification['not_date'];
										$not_date_f = date('d.m.Y H:i', strtotime($not_date));
										$not_status = $notification['not_status'];
										$not_kandidatid = $notification['not_kandidatid'];
										
										$query_link = $db->prepare("
														SELECT lg_desc, kandidat_visitedurl, lg_nalogid
														FROM idk_kandidati
														JOIN idk_link_generator
														ON idk_link_generator.lg_id = idk_kandidati.kandidat_visitedurl
														WHERE kandidat_id = :kandidat_id
														");

										$query_link->execute(array(
														":kandidat_id" => $not_kandidatid
										));
										
										$row_link = $query_link->fetch();
										$lg_desc = $row_link['lg_desc'];
										$url_id = $row_link['kandidat_visitedurl'];
										$lg_nalogid = $row_link['lg_nalogid'];
										
										$get_nalog = $db->prepare("
														SELECT nalog_naziv, nalog_id
														FROM idk_nalozi
														WHERE nalog_id = :nalog_id
														");

										$get_nalog->execute(array(
														":nalog_id" => $lg_nalogid
										));
										$row_nalog = $get_nalog->fetch();
										$nalog_naziv = $row_nalog['nalog_naziv'];
										$nalog_id = $row_nalog['nalog_id'];

										if($not_status == 0){
											$notstats = '<span class="label label-warning material-label material-label_warning main-container__column">NIJE OTVORENO</span>';
											$class_for_new_message = "idk_new_notification";
										}else{
											$notstats = '<span class="label label-success material-label material-label_success main-container__column">OTVORENO</span>';
											$class_for_new_message = "";
										}

									?>
										<tr>
											<td><?php echo $not_id; ?></td>
											<td><a href="<?php getSiteUrl(); ?><?php echo $not_url; ?>&update_not=1&ntf_id=<?php echo $not_id; ?>"><?php echo $not_text; ?></a></td>
											<td><a href="<?php getSiteUrl(); ?>/link_generator.php?page=show_list&id=<?php echo $url_id; ?>"><?php echo $lg_desc; ?></a></td>
											<td><a href="<?php getSiteUrl(); ?>/nalozi?page=open&id=<?php echo $nalog_id; ?>"><?php echo $nalog_naziv; ?></a></td>
											<td><span class="label label-success material-label material-label_success main-container__column"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $not_date_f; ?></span></td>
											<td class="text-center"><?php echo $notstats; ?></td>
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
			}
		?>
			<footer><?php getCopyright(); ?></footer>
		</div>
	</div>
</body>
</html>
