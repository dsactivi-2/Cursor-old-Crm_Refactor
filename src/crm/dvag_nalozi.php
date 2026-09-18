<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getEmployeeStatus = explode( ',' , getEmployeeStatus());


	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: dvag_nalozi?page=list");
	}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Nalozi DVAG | <?php getTitle(); ?></title>

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
					<h1><i class="fa fa-file-text-o idk_color_green" aria-hidden="true"></i> Nalozi svih kompanija DVAG maklera</h1>
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
													{ "width": "5%" },
													{ "width": "25%" },
													{ "width": "25%" },
													{ "width": "25%" },
													{ "width": "20%" },
												]
										});
									} );
								</script>
								<table id="idk_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th>ID</th>
											<th>Naziv naloga</th>
											<th>Makler</th>
											<th>Kompanija</th>
											<th>Kreirano</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
															SELECT
                                                                nalog_id,idk_nalozi.nalog_naziv,idk_jobstep_partners.jp_imeprezime,idk_companies.company_name,idk_nalozi.nalog_kreirano,js_partner_id,company_id
                                                            FROM
                                                                idk_nalozi
                                                            JOIN idk_companies ON idk_nalozi.kompanija_id = idk_companies.company_id
                                                            JOIN idk_jobstep_partners ON idk_companies.js_partner_id = idk_jobstep_partners.jp_id
                                                            WHERE idk_jobstep_partners.jp_partner_company = 3
                                                                ");

											$query->execute();

											while($row = $query->fetch()){

												$nalog_id = $row['nalog_id'];
                                                $nalog_naziv = $row['nalog_naziv'];
                                                $jp_imeprezime = $row['jp_imeprezime'];
                                                $company_name = $row['company_name'];
                                                $company_id = $row['company_id'];
                                                $nalog_kreirano = $row['nalog_kreirano'];
                                                $js_partner_id = $row['js_partner_id'];
										?>
										<tr>
											<td><a href="<?php getSiteURL(); ?>nalozi?page=open&id=<?php echo $nalog_id; ?>"><?php echo $nalog_id; ?></a></td>
											<td><a href="<?php getSiteURL(); ?>nalozi?page=open&id=<?php echo $nalog_id; ?>"><?php echo $nalog_naziv; ?></a></td>
											<td><a href="<?php getSiteURL(); ?>partners?page=open_dvag&id=<?php echo $js_partner_id; ?>"><?php echo $jp_imeprezime; ?></a></td>
											<td><a href="<?php getSiteURL(); ?>companies?page=open&id=<?php echo $company_id; ?>"><?php echo $company_name; ?></a></td>
											<td><?php echo $nalog_kreirano; ?></td>
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