<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus = explode( ',' , getEmployeeStatus());


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
	<title>Logs | <?php getTitle(); ?></title>

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

				case "list":

		?>
			<div class="row">
				<div class="col-xs-12">
					<h1><i class="fa fa-file-text-o idk_color_green" aria-hidden="true"></i> Pregled LOG: <?php getEmployeeFullname(); ?> (zadnje 2 sedmice)</h1>
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
                                                            WHERE log_employeeid = :log_employeeid
															WHERE log_date >= DATE_ADD(CURDATE(),INTERVAL -7 DAY)");

											$query->execute(array(
                                                        ':log_employeeid' => $logged_employee_id));

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

				case "list_all":

		?>
			<div class="row">
				<div class="col-xs-12">
					<h1><i class="fa fa-file-text-o idk_color_green" aria-hidden="true"></i> Log svih korisnika (zadnje 2 sedmice)</h1>
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
								<style>
									table.dataTable td {
										word-break: break-word;
									}
								</style>
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_table').DataTable({

											responsive: true,

											"order": [[ 0, "desc" ]],

											 "bAutoWidth": false,

											"aoColumns": [
													{"width": "0%", "bVisible": false},
													{ "width": "20%" },
													{ "width": "60%" },
													{ "width": "10%" }
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
											<th>Zaposlenik</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
															SELECT log_id, log_desc, log_date, 
															CASE 
																WHEN log_type = 10 THEN idk_pp_users.pu_fname 
																ELSE employee_firstname 
																END as user_firstname,
															CASE 
																WHEN log_type = 10 THEN idk_pp_users.pu_lname
																ELSE employee_lastname
																END as user_lastname
															FROM idk_logs
															INNER JOIN idk_employees ON idk_logs.log_employeeid = idk_employees.employee_id
															LEFT JOIN idk_pp_users ON idk_logs.log_employeeid = idk_pp_users.pu_id
															WHERE log_date >= DATE_ADD(CURDATE(),INTERVAL -7 DAY)");

											$query->execute();

											while($row = $query->fetch()){

												$log_id = $row['log_id'];
                                                $log_date = date('d.m.Y. - H:i', strtotime($row['log_date']));
												$log_desc = $row['log_desc'];
												$ime = $row['user_firstname'];
												$prezime = $row['user_lastname'];
										?>
										<tr>
											<td><?php echo $log_id; ?></td>
                                            <td class="text-center"><?php echo $log_date; ?></td>
											<td><?php echo $log_desc; ?></td>
											<td><?php echo $ime; ?> <?php echo $prezime; ?></td>
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
<?php }else{			
			echo '
				<br/>
				<div class="alert material-alert material-alert_danger">
					<h4>NEMATE PRIVILEGIJE!</h4>
					<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
					<br />
				</div>
';} ?>