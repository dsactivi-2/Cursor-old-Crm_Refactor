<?php
	include("includes/functions.php");
	include("includes/common.php");

	$getUserStatus = getUserStatus();

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: newsletter?page=list");
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
					<h1><i class="fa fa-envelope idk_color_green" aria-hidden="true"></i> Newsletter</h1>
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
								<?php
									if(isset($_GET['mess'])) {
										$mess = $_GET['mess'];
									}else{
										$mess = 0;
									}

									if($mess == 1){
										echo '<div class="alert material-alert material-alert_success">Uspješno ste obrisali email sa newslettera.</div>';
									}
								?>
								<script type="text/javascript">
									$(document).ready(function() {
										$('#idk_table').DataTable({

											responsive: true,

											"order": [[ 0, "desc" ]],

											 "bAutoWidth": false,

											"aoColumns": [
													{ "width": "0%", "bVisible": false },
													{ "width": "20%" },
													{ "width": "70%" },
													{ "width": "10%", "bSortable": false }
												]
										});
									} );
								</script>
								<table id="idk_table" class="display" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th></th>
											<th class="text-center">Datum i vrijeme</th>
											<th>Email</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										<?php
											$query = $db->prepare("
															SELECT newsletter_id, newsletter_email, newsletter_datetime
															FROM idk_newsletter
                                                            GROUP BY newsletter_email");

											$query->execute(array(
                                                        ':log_userid' => $logged_user_id));

											while($row = $query->fetch()){

												$newsletter_id = $row['newsletter_id'];
                                                $newsletter_datetime = date('d.m.Y. - H:i', strtotime($row['newsletter_datetime']));
												$newsletter_email = $row['newsletter_email'];
										?>
										<tr>
											<td><?php echo $newsletter_id; ?></td>
                                            <td class="text-center"><?php echo $newsletter_datetime; ?></td>
											<td><?php echo $newsletter_email; ?></td>
											<td class="text-center"><a href="#" data="<?php getSiteURL(); ?>newsletter?page=del&email=<?php echo $newsletter_email; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o fa-lg text-danger" aria-hidden="true"></i></td>
										</tr>
										<?php } ?>
										<script>
											$(".archive").click(function () {
												var addressValue = $(this).attr("data");
												document.getElementById("archive_link").href = addressValue;
											});
										</script>
										<!-- Modal -->
										<div class="modal material-modal material-modal_danger fade" id="archiveModal">
											<div class="modal-dialog">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Brisanje</h4>
													</div>
													<div class="modal-body material-modal__body">
														<p>Jeste li sigurni da želite obrisati email adresu?</p>
													</div>
													<div class="modal-footer material-modal__footer">
														<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
														<a id="archive_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
													</div>
												</div>
											</div>
										</div>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
				break;

				case "del":
					if($getUserStatus  == 1){

						$newsletter_email = $_GET['email'];

						//Delete
						$del_query = $db->prepare("
											DELETE FROM idk_newsletter
											WHERE newsletter_email = :newsletter_email");

						$del_query->execute(array(
											':newsletter_email' => $newsletter_email));

						//Add to LOGS
						$log_desc = "Obrisao email iz newslettera: " . $newsletter_email . "";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_userid, log_desc, log_date)
										VALUES
											(:log_userid, :log_desc, :log_date)");

						$log_query->execute(array(
										':log_userid' => $logged_user_id,
										':log_desc' => $log_desc,
										':log_date' => $log_date));


						header("Location: " . getSiteURLr() . "newsletter?page=list&mess=1");

					}else{
						echo '
							<div class="alert material-alert material-alert_danger">
								<h4>NEMATE PRIVILEGIJE!</h4>
								<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
								<br />
								<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
							</div>
						';
					}
				break;

			}
		?>
			<footer><?php getCopyright(); ?></footer>
		</div>
	</div>
</body>
</html>
