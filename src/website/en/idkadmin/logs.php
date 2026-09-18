<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getUserStatus = getUserStatus();

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: logs?page=list");
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
				<div class="col-xs-12">
					<h1><i class="fa fa-file-text-o idk_color_green" aria-hidden="true"></i> Pregled LOG: <?php getUserFullname(); ?></h1>
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
													{"width": "0%", "bVisible": false},
													{ "width": "20%" },
													{ "width": "70%" }
												]
										});
									} );
								</script>
								<table id="idk_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th></th>
											<th class="text-center">Datum i vrijeme</th>
											<th>Log opis</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
															SELECT log_id, log_desc, log_date
															FROM idk_logs
                                                            WHERE log_userid = :log_userid");

											$query->execute(array(
                                                        ':log_userid' => $logged_user_id));

											while($row = $query->fetch()){

												$log_id = $row['log_id'];
                                                $log_date = date('d.m.Y. - H:i', strtotime($row['log_date']));
												$log_desc = $row['log_desc'];
										?>
										<tr>
											<td><?php echo $log_id; ?></td>
                                            <td class="text-center"><?php echo $log_date; ?></td>
											<td><?php echo $log_desc; ?></td>
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

				case "all":

		?>
			<div class="row">
				<div class="col-xs-12">
					<h1><i class="fa fa-file-text-o idk_color_green" aria-hidden="true"></i> LOG svih korisnika</h1>
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
													{"width": "0%", "bVisible": false},
													{ "width": "15%" },
													{ "width": "15%" },
													{ "width": "76%" }
												]
										});
									} );
								</script>
								<table id="idk_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th></th>
											<th class="text-center">Datum i vrijeme</th>
											<th>Korisnik</th>
											<th>Log opis</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
															SELECT log_id, log_desc, log_date, user_fullname
															FROM idk_logs
															INNER JOIN idk_users ON idk_logs.log_userid = idk_users.user_id
															ORDER BY log_id DESC");

											$query->execute();

											while($row = $query->fetch()){

												$log_id = $row['log_id'];
                                                $log_date = date('d.m.Y. - H:i', strtotime($row['log_date']));
												$user_fullname = $row['user_fullname'];
												$log_desc = $row['log_desc'];
										?>
										<tr>
											<td><?php echo $log_id; ?></td>
                                            <td class="text-center"><?php echo $log_date; ?></td>
											<td><?php echo $user_fullname; ?></td>
											<td><?php echo $log_desc; ?></td>
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
