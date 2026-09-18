<?php
	include("includes/functions.php");
	include("includes/common.php");

	if(isset($_REQUEST["page"])) {
		$page = $_REQUEST["page"];
	}else{
		header("Location: messages?page=list");
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
					<h1><i class="fa fa-envelope idk_color_green" aria-hidden="true"></i> Poruke</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>messages?page=new" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Nova poruka</span></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<?php
							if(isset($_GET['mess'])) {
								$mess = $_GET['mess'];
							}else{
								$mess = 0;
							}

							if($mess == 1){
								echo '<div class="alert material-alert material-alert_success">Uspješno ste poslali poruku.</div>';
							}elseif($mess == 2){
								echo '<div class="alert material-alert material-alert_success">Uspješno ste obrisali poruku.</div>';
							}
						?>
						<div id="myTabs" class="panel-group material-tabs-group">
							<ul class="nav nav-tabs material-tabs material-tabs_primary">
								<li class="active"><a href="#inbox" class="material-tabs__tab-link" data-toggle="tab">Primljene poruke</a></li>
								<li><a href="#sentmail" class="material-tabs__tab-link" data-toggle="tab">Poslane poruke</a></li>
							</ul>
							<div class="tab-content materail-tabs-content">
								<div class="tab-pane fade active in" id="inbox">
									<script type="text/javascript">
										$(document).ready( function () {
											$('#inbox_dt').DataTable( {

												responsive: true,

												"order": [[ 0, "desc" ]],

												"aoColumns": [
														{"width": "0%", "bVisible": false},
														{"width": "20%"},
														{"width": "55%"},
														{"width": "15%", "bSortable": false},
														{"width": "10%", "bSortable": false}
													]
											});
										} );
									</script>
									<table id="inbox_dt" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th></th>
												<th>Od</th>
												<th>Naslov</th>
												<th class="text-center">Primljeno</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<?php
												$inbox_query = $db->prepare("
																		SELECT message_id, message_subject, message_datetime, user_fullname, mu_status
																		FROM idk_messages
																		INNER JOIN idk_messages_users ON idk_messages.message_id = idk_messages_users.mu_messageid
																		INNER JOIN idk_users ON idk_messages.message_sentid = idk_users.user_id
																		WHERE mu_userid = :mu_userid AND mu_status != :mu_status");

												$inbox_query->execute(array(
																':mu_userid' => $logged_user_id,
																':mu_status' => 2));

												while($inbox_row = $inbox_query->fetch()){

													$message_id = $inbox_row['message_id'];
													$user_fullname = $inbox_row['user_fullname'];
													$message_subject = $inbox_row['message_subject'];
													$message_datetime = $inbox_row['message_datetime'];
													$message_datetime_f = date('d.m.Y H:i', strtotime($inbox_row['message_datetime']));
													$mu_status = $inbox_row['mu_status'];

													if($mu_status == 0){
														$mess_status_style = "font-weight: bold;";
													}else{
														$mess_status_style = "";
													}
											?>
											<tr>
												<td><?php echo $message_id; ?></td>
												<td onclick="document.location = 'messages?page=open&id=<?php echo $message_id; ?>';" style="<?php echo $mess_status_style; ?> cursor: pointer;"><?php echo $user_fullname; ?></td>
												<td onclick="document.location = 'messages?page=open&id=<?php echo $message_id; ?>';" style="<?php echo $mess_status_style; ?> cursor: pointer;" ><?php echo $message_subject; ?></td>
												<td onclick="document.location = 'messages?page=open&id=<?php echo $message_id; ?>';" style="<?php echo $mess_status_style; ?> cursor: pointer;"  class="text-center"><time class="timeago" datetime="<?php echo $message_datetime; ?>" data-toggle="tooltip" data-placement="top" title="<?php echo $message_datetime_f; ?>"></time></td>
												<td class="text-center">
													<div class="btn-group material-btn-group">
														<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
														<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
															<li><a href="<?php getSiteURL(); ?>messages?page=open&id=<?php echo $message_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
															<li><a href="<?php getSiteURL(); ?>messages?page=reply&id=<?php echo $message_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-reply" aria-hidden="true"></i> Odgovori</a></li>
															<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>messages?page=archive&id=<?php echo $message_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Obriši</a></li>
														</ul>
													</div>
												</td>
											</tr>
											<?php	}	?>
										</tbody>
									</table>
								</div>
								<div class="tab-pane fade" id="sentmail">
									<script type="text/javascript">
										$(document).ready( function () {
											$('#sentmail_dt').DataTable( {

												"order": [[ 0, "desc" ]],

												"aoColumns": [
														{"width": "0%", "bVisible": false},
														{"width": "20%"},
														{"width": "55%"},
														{"width": "15%", "bSortable": false},
														{"width": "10%", "bSortable": false}
													]
											});
										});
									</script>
									<table id="sentmail_dt" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th></th>
												<th>Za</th>
												<th>Naslov</th>
												<th class="text-center">Poslano</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<?php
												$inbox_query = $db->prepare("
																		SELECT message_id, message_subject, message_datetime, user_fullname, mu_status
																		FROM idk_messages
																		INNER JOIN idk_messages_users ON idk_messages.message_id = idk_messages_users.mu_messageid
																		INNER JOIN idk_users ON idk_messages_users.mu_userid = idk_users.user_id
																		WHERE message_sentid = :message_sentid AND message_status = :message_status");

												$inbox_query->execute(array(
																':message_sentid' => $logged_user_id,
																':message_status' => 1));

												while($inbox_row = $inbox_query->fetch()){

													$message_id = $inbox_row['message_id'];
													$user_fullname = $inbox_row['user_fullname'];
													$message_subject = $inbox_row['message_subject'];
													$message_datetime = $inbox_row['message_datetime'];
													$message_datetime_f = date('d.m.Y H:i', strtotime($inbox_row['message_datetime']));
											?>
											<tr>
												<td><?php echo $message_id; ?></td>
												<td onclick="document.location = 'messages?page=open&id=<?php echo $message_id; ?>';" style="cursor: pointer;"><?php echo $user_fullname; ?></td>
												<td onclick="document.location = 'messages?page=open&id=<?php echo $message_id; ?>';" style="cursor: pointer;" ><?php echo $message_subject; ?></td>
												<td onclick="document.location = 'messages?page=open&id=<?php echo $message_id; ?>';" style="cursor: pointer;"  class="text-center"><time class="timeago" datetime="<?php echo $message_datetime; ?>" data-toggle="tooltip" data-placement="top" title="<?php echo $message_datetime_f; ?>"></time></td>
												<td class="text-center">
													<div class="btn-group material-btn-group">
														<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
														<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
															<li><a href="<?php getSiteURL(); ?>messages?page=open&id=<?php echo $message_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori</a></li>
															<li><a href="<?php getSiteURL(); ?>messages?page=reply&id=<?php echo $message_id; ?>" class="material-dropdown-menu__link"><i class="fa fa-reply" aria-hidden="true"></i> Odgovori</a></li>
															<li class="idk_dropdown_danger"><a href="#" data="<?php getSiteURL(); ?>messages?page=archive&id=<?php echo $message_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link"><i class="fa fa-trash-o" aria-hidden="true"></i> Obriši</a></li>
														</ul>
													</div>
												</td>
											</tr>
											<?php	}	?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
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
							<p>Jeste li sigurni da želite obrisati poruku?</p>
						</div>
						<div class="modal-footer material-modal__footer">
							<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
							<a id="archive_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
						</div>
					</div>
				</div>
			</div>
		<?php
				break;

				case "new":
		?>
		<div class="row">
			  <div class="col-xs-8">
				  <h1><i class="fa fa-send idk_color_green" aria-hidden="true"></i> Pošalji poruku</h1>
			  </div>
			  <div class="col-xs-4 text-right idk_margin_top10">
				  <a href="<?php getSiteURL(); ?>messages?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
			  </div>
			  <div class="col-xs-12">
				  <hr />
			  </div>
	  	</div>
	  	<div class="row">
			  <div class="col-md-12">
				  <div class="content_box">
					  <div class="row">
						  <div class="col-md-10">
							  <form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=send_message" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
								  <input type="hidden" name="message_sentid" value="<?php echo $logged_user_id; ?>">
								  <div class="form-group">
									  <label for="mu_userid" class="col-sm-3 control-label"><span class="text-danger">*</span> Prima:</label>
									  <div class="col-sm-9">
										  <div class="materail-input-block materail-input-block_success">
											  <select class="js-basic-multiple form-control" multiple="multiple" id="mu_userid" name="mu_userid[]" required>
					  							<?php
					  								$users_query = $db->prepare("
					  													SELECT user_id, user_fullname
					  													FROM idk_users
																		WHERE user_status != :user_status AND user_id != :user_id
					  													ORDER BY user_fullname ASC");

					  								$users_query->execute(array(
																	':user_status' => 0,
																	':user_id' => $logged_user_id));

					  								while($users = $users_query->fetch()) {

					  									echo "<option value='" . $users['user_id'] . "'>" . $users['user_fullname'] . "</option>";
					  								}
					  							?>
					  						</select>
					  						<script type="text/javascript">
					  							$(".js-basic-multiple").select2();
					  						</script>
											  <span class="materail-input-block__line"></span>
										  </div>
									  </div>
								  </div>
								  <div class="form-group">
									  <label for="message_subject" class="col-sm-3 control-label"><span class="text-danger">*</span> Naslov:</label>
									  <div class="col-sm-9">
										  <div class="materail-input-block materail-input-block_success">
											  <input class="form-control materail-input" type="text" name="message_subject" id="message_subject" required>
											  <span class="materail-input-block__line"></span>
										  </div>
									  </div>
								  </div>
								  <div class="form-group">
									  <div class="col-md-offset-2 col-sm-10">
										  <div class="form-group materail-input-block materail-input-block_success">
											  <textarea id="message_text" class="form-control materail-input material-textarea" name="message_text" placeholder="Poruka ..." rows="8" required></textarea>
											  <span class="materail-input-block__line"></span>
										  </div>
									  </div>
								  </div>
								  <script>
									  $('#message_text').trumbowyg({
										  lang: 'hr',
										  btns: [
											  ['undo', 'redo'],
											  ['formatting'],
											  ['strong', 'em', 'del'],
											  ['link'],
											  ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
											  ['unorderedList', 'orderedList'],
											  ['horizontalRule'],
											  ['fullscreen']
										  ]
									  });
								  </script>
								  <br />
								  <div class="form-group">
									  <div class="col-sm-offset-2 col-sm-10 text-right">
										  <ul class="list-inline">
											  <li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
											  <li>
												  <button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-send" aria-hidden="true"></i> <span>Pošalji</span></button>
											  </li>
										  </ul>
										  <small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
									  </div>
								  </div>
							  </form>
						  </div>
					  </div>
				  </div>
			  </div>
		  </div>
		<?php
				break;

				case "open":

				$message_id = $_GET['id'];

				//Check if message exist for logged employee
				$query_check = $db->prepare("
										SELECT mu_id
										FROM idk_messages_users
										WHERE mu_messageid = :mu_messageid AND mu_userid = :mu_userid");

				$query_check->execute(array(
									':mu_messageid' => $message_id,
									':mu_userid' => $logged_user_id));

				$number_of_messages = $query_check->rowCount();

				//Check if message exist for logged employee sentmail
				$query_check_sent = $db->prepare("
											SELECT message_id
											FROM idk_messages
											WHERE message_id = :message_id AND message_sentid = :message_sentid");

				$query_check_sent->execute(array(
										':message_id' => $message_id,
										':message_sentid' => $logged_user_id));

				$number_of_sent_messages = $query_check_sent->rowCount();

				if($number_of_messages > 0 OR $number_of_sent_messages > 0){

					//Mark message as ready for logged employee
					$query_update = $db->prepare("
									UPDATE idk_messages_users
									SET mu_status = :mu_status
									WHERE mu_messageid = :mu_messageid AND mu_userid = :mu_userid");

					$query_update->execute(array(
								':mu_status' => 1,
								':mu_messageid' => $message_id,
								':mu_userid' => $logged_user_id));

					//Get message data
					$query = $db->prepare("
									SELECT message_id, message_subject, message_text, message_datetime, user_fullname, user_image, mu_status
									FROM idk_messages
									INNER JOIN idk_messages_users ON idk_messages.message_id = idk_messages_users.mu_messageid
									INNER JOIN idk_users ON idk_messages.message_sentid = idk_users.user_id
									WHERE message_id = :message_id");

					$query->execute(array(
									':message_id' => $message_id));

					$row = $query->fetch();

						$message_subject = $row['message_subject'];
						$message_text = $row['message_text'];
						$user_fullname = $row['user_fullname'];
						if($row['user_image'] == NULL){
							$user_image = "none.jpg";
						}else{
							$user_image = $row['user_image'];
						}
						$message_date = date('d.m.Y.', strtotime($row['message_datetime']));
						$message_time = date('H:i', strtotime($row['message_datetime']));

		?>
		<div class="row">
			 <div class="col-xs-8">
				 <h1><i class="fa fa-envelope idk_color_green" aria-hidden="true"></i> <?php echo $message_subject; ?></h1>
			 </div>
			 <div class="col-xs-4 text-right idk_margin_top10">
				 <a href="<?php getSiteURL(); ?>messages?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
			 </div>
			 <div class="col-xs-12">
				 <hr />
			 </div>
	   </div>
	   <div class="row">
			 <div class="col-md-12">
				 <div class="content_box">
					 <div class="row">
						 <div class="col-md-offset-1 col-md-10">
							 <div class="row">
							 	<div class="col-sm-8">
									<ul class="list-inline">
										<li><a class="fancybox" rel="group" href="<?php getSiteURL(); ?>files/users/<?php echo $user_image; ?>"><img class="idk_profile_img" src="<?php getSiteURL(); ?>files/users/<?php echo $user_image; ?>"></a></li>
										<li><?php echo $user_fullname; ?></li>
										<li>|</li>
										<li><i class="fa fa-calendar fa-lg" aria-hidden="true"></i></li>
										<li><?php echo $message_date; ?></li>
										<li>|</li>
										<li><i class="fa fa-clock-o fa-lg" aria-hidden="true"></i></li>
										<li><?php echo $message_time; ?></li>
									</ul>
							 	</div>
								<div class="col-sm-4 text-right">
									<ul class="list-inline idk_margin_top5">
										<li><a href="<?php getSiteURL(); ?>messages?page=reply&id=<?php echo $message_id; ?>" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" ><i class="fa fa-reply" aria-hidden="true"></i> <span></span></a></li>
										<li><a href="#" data="<?php getSiteURL(); ?>messages?page=archive&id=<?php echo $message_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive btn material-btn material-btn-icon-danger material-btn_danger main-container__column"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
									</ul>
							 	</div>
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
												<p>Jeste li sigurni da želite obrisati poruku?</p>
											</div>
											<div class="modal-footer material-modal__footer">
												<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
												<a id="archive_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
											</div>
										</div>
									</div>
								</div>
							 </div>
							<hr>
							<div class="row">
							   <div class="col-sm-12">
								   <?php echo $message_text; ?>
							   </div>
							</div>
						 </div>
					 </div>
				 </div>
			 </div>
		 </div>
		<?php
				}else{
					echo '
						<div class="alert material-alert material-alert_danger">
							<h4>PORUKA NIJE DOSTUPNA!</h4>
							<p>Poruku koju pokušavate otvoriti nije dostupna. Kontaktirajte administratora za pomoć.</p>
							<br />
							<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
						</div>
					';
				}

				break;

				case "reply":

					$message_id = $_GET['id'];

					//Check if message exist for logged employee
					$query_check = $db->prepare("
											SELECT mu_id
											FROM idk_messages_users
											WHERE mu_messageid = :mu_messageid AND mu_userid = :mu_userid");

					$query_check->execute(array(
										':mu_messageid' => $message_id,
										':mu_userid' => $logged_user_id));

					$number_of_messages = $query_check->rowCount();

					//Check if message exist for logged employee sentmail
					$query_check_sent = $db->prepare("
												SELECT message_id
												FROM idk_messages
												WHERE message_id = :message_id AND message_sentid = :message_sentid");

					$query_check_sent->execute(array(
											':message_id' => $message_id,
											':message_sentid' => $logged_user_id));

					$number_of_sent_messages = $query_check_sent->rowCount();

					if($number_of_messages > 0 OR $number_of_sent_messages > 0){

						//Get message data
						$query = $db->prepare("
										SELECT message_id, message_subject, message_text, message_sentid
										FROM idk_messages
										WHERE message_id = :message_id");

						$query->execute(array(
										':message_id' => $message_id));

						$row = $query->fetch();

							$message_sentid = $row['message_sentid'];
							$message_subject = $row['message_subject'];
							$message_text = $row['message_text'];

		?>
			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-reply idk_color_green" aria-hidden="true"></i> Odgovori na poruku</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
					<a href="<?php getSiteURL(); ?>messages?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div class="row">
							<div class="col-md-10">
								<form id="idk_form" action="<?php getSiteURL(); ?>do.php?form=send_message" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
									<input type="hidden" name="message_sentid" value="<?php echo $logged_user_id; ?>">
									<div class="form-group">
										<label for="mu_userid" class="col-sm-3 control-label"><span class="text-danger">*</span> Prima:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<select class="js-basic-multiple form-control" multiple="multiple" id="mu_userid" name="mu_userid[]" required>
												  <?php
													  $users_query = $db->prepare("
																		  SELECT user_id, user_fullname
																		  FROM idk_users
																		  WHERE user_status != :user_status AND user_id != :user_id
																		  ORDER BY user_fullname ASC");

													  $users_query->execute(array(
																	  ':user_status' => 0,
																	  ':user_id' => $logged_user_id));

													  while($users = $users_query->fetch()) {

														  if($users['user_id'] == $message_sentid){
					  										$user_select = "selected";
					  									}else{
					  										$user_select = "";
					  									}

														  echo "<option value='" . $users['user_id'] . "' " . $user_select . ">" . $users['user_fullname'] . "</option>";
													  }
												  ?>
											  </select>
											  <script type="text/javascript">
												  $(".js-basic-multiple").select2();
											  </script>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="message_subject" class="col-sm-3 control-label"><span class="text-danger">*</span> Naslov:</label>
										<div class="col-sm-9">
											<div class="materail-input-block materail-input-block_success">
												<input class="form-control materail-input" type="text" name="message_subject" id="message_subject" value="Re: <?php echo $message_subject; ?>" required>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="col-md-offset-2 col-sm-10">
											<div class="form-group materail-input-block materail-input-block_success">
												<textarea id="message_text" class="form-control materail-input material-textarea" name="message_text" placeholder="Poruka ..." rows="8" required><p>&nbsp;</p><p>&nbsp;</p><blockquote><?php echo $message_text; ?></blockquote></textarea>
												<span class="materail-input-block__line"></span>
											</div>
										</div>
									</div>
									<script>

										$('#message_text').trumbowyg({
											lang: 'hr',
											btns: [
												['undo', 'redo'],
												['formatting'],
												['strong', 'em', 'del'],
												['link'],
												['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
												['unorderedList', 'orderedList'],
												['horizontalRule'],
												['fullscreen']
											]
										});

										$(document).ready(function() { $(".trumbowyg-editor").focus(); });
									</script>
									<br />
									<div class="form-group">
										<div class="col-sm-offset-2 col-sm-10 text-right">
											<ul class="list-inline">
												<li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
												<li>
													<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-send" aria-hidden="true"></i> <span>Pošalji</span></button>
												</li>
											</ul>
											<small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php
					}else{
						echo '
							<div class="alert material-alert material-alert_danger">
								<h4>PORUKA NIJE DOSTUPNA!</h4>
								<p>Poruku koju pokušavate otvoriti nije dostupna. Kontaktirajte administratora za pomoć.</p>
								<br />
								<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
							</div>
						';
					}

				break;

				case "archive":

						$message_id = $_GET['id'];

						//Update inbox
						$query_inbox = $db->prepare("
										UPDATE idk_messages_users
										SET mu_status = :mu_status
										WHERE mu_messageid = :mu_messageid AND mu_userid = :mu_userid");

						$query_inbox->execute(array(
									':mu_status' => 2,
									':mu_messageid' => $message_id,
									':mu_userid' => $logged_user_id));

						//Update sent
						$query_sent = $db->prepare("
										UPDATE idk_messages
										SET message_status = :message_status
										WHERE message_id = :message_id AND message_sentid = :message_sentid");

						$query_sent->execute(array(
									':message_status' => 2,
									':message_id' => $message_id,
									':message_sentid' => $logged_user_id));

						//Add to LOGS
						$log_desc = "Obrisao poruku!";
						$log_date = date('Y-m-d H:i:s');

						$log_query = $db->prepare("
										INSERT INTO idk_logs
											(log_employeeid, log_desc, log_date)
										VALUES
											(:log_employeeid, :log_desc, :log_date)");

						$log_query->execute(array(
										':log_employeeid' => $logged_employee_id,
										':log_desc' => $log_desc,
										':log_date' => $log_date));


						header("Location: " . getSiteURLr() . "messages?page=list&mess=2");

				break;

			}
		?>
			<footer><?php getCopyright(); ?></footer>
		</div>
	</div>
</body>
</html>
