
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
	<title>Notifications DVAG | <?php getTitle(); ?></title>

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

				case "form":

		?>
			<div class="row">
				<div class="col-xs-12">
					<h1><i class="fa fa-file-text-o idk_color_green" aria-hidden="true"></i> Forma za slanje notifikacija</h1>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-md-offset-1 col-md-8">
								<form id="idk_form" action="/dvag_notifications.php?page=send" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<div class="form-group">
										<label for="company_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Korisnici:</label>
										<div class="col-sm-9">
											<div class="custom-select">
                                                <select class="select_js" name="users[]" id="users" data-live-search="true" data-actions-box="true" required multiple>
                                                    <?php
                                                        $makleri_sql = $db->prepare("SELECT jp_id, jp_ime, jp_prezime, jp_fcmtoken 
																					 FROM idk_jobstep_partners 
																					 WHERE jp_user_type = 1 AND jp_fcmtoken IS NOT NULL");
                                                        $makleri_sql->execute();
                                                        while($result = $makleri_sql->fetch()){ 
															$should_be_selected = shouldSendPersonalNotification($result['jp_id'], 4);	
															if($should_be_selected == 1){
														?>
                                                            <option value="<?php echo $result['jp_fcmtoken']; ?>"><?php echo $result['jp_ime'] . " " . $result['jp_prezime']; ?></option>
                                                        <?php } }
                                                    ?>
                                                </select>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
                                    <script>
                                        $(document).ready(function(){
                                            $('.select_js').selectpicker();
                                        });
                                    </script>
									<div class="form-group"><div class="col-sm-3"></div><div class="col-sm-9"><hr></div></div>
									<div class="form-group">
										<label for="title_de" class="col-sm-3 control-label"><span class="text-danger">*</span> Naslov na njemačkom:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="title_de" id="title_de" placeholder="Naslov na njemačkom" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="content_de" class="col-sm-3 control-label"><span class="text-danger">*</span>Sadržaj na njemačkom:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="content" name="content_de" id="content_de" placeholder="Sadržaj na njemačkom" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									</br>
									<div class="form-group">
										<label for="title_en" class="col-sm-3 control-label"><span class="text-danger">*</span> Naslov na engleskom:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="title_en" id="title_en" placeholder="Naslov na engleskom" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="content_en" class="col-sm-3 control-label"><span class="text-danger">*</span>Sadržaj na engleskom:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="content" name="content_en" id="content_en" placeholder="Sadržaj na engleskom" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<br />
									<div class="form-group">
										<div class="col-sm-offset-2 col-sm-10 text-right">
											<ul class="list-inline">
												<li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
												<li>
													<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-plus" aria-hidden="true"></i> <span>Pošalji</span></button>
												</li>
											</ul>
											<small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
										</div>
									</div>
                                </form>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<hr />
							</div>
						</div>
						<div class="row">
							<div class="col-md-2">
							</div>
							<div class="col-md-8">
								<script>
									$(document).ready(function() {
										var table = $('#notification_logs').DataTable({
											responsive: true,
											"order": [[ 0, "desc" ]],
											 "bAutoWidth": false,
											"aoColumns": [
													{ "width": "5%" },
													{ "width": "10%" },
													{ "width": "10%" },
													{ "width": "25%" },
													{ "width": "10%" },
													{ "width": "25%" },
													{ "width": "10%" }
												]
										});
									} );
								</script>
								<table id="notification_logs">
									<thead>
										<tr>
											<th class="text-center">ID</th>
											<th class="text-center">Uposlenik</th>
											<th class="text-center">Naslov na njemačkom</th>
											<th class="text-center">Sadržaj na njemačkom</th>
											<th class="text-center">Naslov na engleskom</th>
											<th class="text-center">Sadržaj na engleskom</th>
											<th class="text-center">Datum</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$sql = "SELECT id, employee_firstname, employee_lastname, title_de, title_en, content_de, content_en, created_at FROM idk_partner_notification_logs JOIN idk_employees ON idk_partner_notification_logs.employee_id = idk_employees.employee_id ORDER BY created_at DESC";	
											$stmt = $db->prepare($sql);
											$stmt->execute();
											$result = $stmt->fetchAll();
											foreach($result as $row){ ?>
												<tr>
													<td class="text-center"><?php echo $row['id']; ?></td>
													<td class="text-center"><?php echo $row['employee_firstname'] . " " . $row['employee_lastname']; ?></td>
													<td class="text-center"><?php echo $row['title_de']; ?></td>
													<td class="text-center"><?php echo $row['content_de']; ?></td>
													<td class="text-center"><?php echo $row['title_en']; ?></td>
													<td class="text-center"><?php echo $row['content_en']; ?></td>
													<td class="text-center"><?php echo $row['created_at']; ?></td>
												</tr>
										<?php }
										?>
									</tbody>
								</table>
							</div>
							<div class="col-md-2">
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
				break;

                case "send":
                    $users = $_POST['users'];
                    $title_de = $_POST['title_de'];
                    $content_de = $_POST['content_de'];
                    $title_en = $_POST['title_en'];
                    $content_en = $_POST['content_en'];
					$action = "NAVIGATE_TO_APP";
					$type = 4;
					$payload = "{}"; 
					$title = '{"en": "'.$title_en.'", "de": "'.$title_de.'"}';
					$content = '{"en": "'.$content_en.'", "de": "'.$content_de.'"}';

					$add_log = $db->prepare("INSERT INTO idk_partner_notification_logs (employee_id, title_de, title_en, content_de, content_en) VALUES (:employee_id, :title_de, :title_en, :content_de, :content_en)");
					$add_log->execute(array(
						":employee_id" => $logged_employee_id,
						":title_de" => $title_de,
						":title_en" => $title_en,
						":content_de" => $content_de,
						":content_en" => $content_en
					));

					$de_users = [];
					$en_users = [];
					foreach($users as $user){
						$user_data = getMaklerInfoByOnesignal($user);
						createPartnerPersonalNotification($user_data['partner_id'], $type, $payload, $action, $title, $content);
						if($user_data['lang'] == "de"){
							$de_users[] = $user_data['onesignal'];
						}else{
							$en_users[] = $user_data['onesignal'];
						}
					}


					if(!empty($de_users)){
						sendMultipleNotifications($de_users, $title_de, $content_de);
					}
					if(!empty($en_users)){
						sendMultipleNotifications($en_users, $title_en, $content_en);
					}

                    header("Location: dvag_notifications?page=form");
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