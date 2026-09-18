<!DOCTYPE html>
<html>


	<?php 
		include('includes/functions.php');
		include('includes/head.php');  
		include('lang/bs.php');
		if(isset($_GET['id'])) {
			$kandidat_id = $_GET['id'];
		}else{
		}
	?>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?php getTitle(); ?></title>
<body>
	<div id="content_public">
	
		<div class="container">
			<div class="row">
				<div class="col-xs-12 text-center">
					<img class="img-responsive" src="<?php getSiteURL(); ?>images/Jobstep_logo_new.png" alt="" style="max-width:200px;">
				</div>
				<div class="col-xs-12">
					<br />
				</div>
			</div>
			<style>
				.col-xs-12 > .img-responsive{
					max-width: 90px !important;
					margin-top: 10px !important;
				}
				.alert > h3{
					margin-top: 0px !important;
					font-size: 19px !important;
				}
				.alert > h5{
					font-size: 15px !important;
					border-left: none !important; 
					margin-bottom: 0px !important;
				}
				.col-md-4 > iframe{
					margin-bottom: 10px;
				}
				
			</style>
			<div class="row">
				<div class="col-md-6 col-md-offset-3 idk_margin_top10">
					<div style = "background-color: #ffffff; color: #6097a0; text-align: center;" class="alert material-alert material-alert_success">
						<h3><?php echo $txt_reg_zahvala; ?>
						<input type="hidden" value="<?php echo $kandidat_id; ?>" >
						</h3>
						<h5><?php echo "Jobstep Team će Vas kontaktirati ubrzo za sve detalje."; ?>
						</h5>
					</div>
				</div>
			</div>
			<?php 
			$query_check = $db->prepare("SELECT * FROM idk_project_kandidati
										WHERE pk_projectid = :pk_projectid AND pk_kandidatid = :pk_kandidatid");
			$query_check->execute(array(
							':pk_projectid' => 1375,
							':pk_kandidatid' => $kandidat_id));
			
			if($kandidat_id != 0){
				if($query_check->rowCount() == 0){
				
					$query_project = $db->prepare("
									INSERT INTO idk_project_kandidati
										(pk_projectid, pk_kandidatid)
									VALUES
										(:pk_projectid, :pk_kandidatid)");

					$query_project->execute(array(
									':pk_projectid' => 1375,
									':pk_kandidatid' => $kandidat_id));
				}
			}
			
			?>
			
			
			
			<footer><?php getCopyright(); ?></footer>
		</div>
	</div>
</body>
			
</html>