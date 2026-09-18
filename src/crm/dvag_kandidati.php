<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus = explode( ',' , getEmployeeStatus());


	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: dvag_kandidati?page=list");
	}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Kandidati DVAG | <?php getTitle(); ?></title>

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
					<h1><i class="fa fa-file-text-o idk_color_green" aria-hidden="true"></i> Kandidati DVAG maklera</h1>
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
												"pageLength": 10,
												"processing": true,
												"serverSide": true,
												"order": [[ 0, "desc" ]],
												"aoColumns": [
														null,
														null,
														null,
														null,
														null,
														null
														
													],
												"ajax":{
													url :"<?php echo getSiteUrlr(); ?>serverside_partner.php?page=list_kandidati",
													type: "POST",
													error: function(data){
														$(".list-grid-error").html(""); 
														$("#list-grid_processing").css("display","none");
												
													},
												}		
										});
									} );
								</script>
								<table id="idk_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>ID</th>
											<th>Ime i prezime kandidata</th>
											<th>Datum prijave</th>
											<th>Status prijave</th>
											<th>Nalog</th>
											<th>Makler</th>
										</tr>
									</thead>
									
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