<?php if(getEmployeeStatus() == 0 ){
	$login_query = $db->prepare("
							SELECT employee_key
							FROM idk_employees
							WHERE employee_id = :employee_id");

	$login_query->execute(array(
					':employee_id' => $logged_employee_id));
	$row_q = $login_query->fetch();
	$employee_key = $row_q['employee_key'];
	setcookie('idk_session', $employee_key, time()-3600);
}
?>

<div id="idk_loader"></div>
<a6-spotlight></a6-spotlight>
<div id="idk_logo">
	<?php 	
		$getEmployeeStatus = explode( ',' , getEmployeeStatus());
		if(in_array("11",$getEmployeeStatus)){ ?>
			<a href="<?php getSiteURL(); ?>dak?page=list_for_dak"><img class="idk_logo1 img-responsive" src="<?php getSiteURL(); ?>images/<?php echo getSubdomainr(); ?>_logo.png" /></a>
			<a href="<?php getSiteURL(); ?>dak?page=list_for_dak"><img class="idk_logo2 img-responsive" src="<?php getSiteURL(); ?>images/<?php echo getSubdomainr(); ?>_logo_s.png" /></a>
		<?php }else{ ?>
    <?php
      $app_env = $envConfig->APP_ENV;
    
      // Set logo path for production
      $logo_path = "/images/novi_logo_meni.png";
      if($app_env != "production"){
        // If development environment set logo path for dev, else set logo path for staging
        if($app_env == 'dev'){
			$logo_path = "/images/novi_logo_meni_dev.png";
		}else{
			$logo_path = "/images/novi_logo_meni_staging.png";
		}
      }
	  
    ?>
      	<a href="/"><img class="idk_logo1 img-responsive" src="<?php echo $logo_path; ?>" style="height: 100%!important;" /></a>
		<a href="<?php getSiteURL(); ?>"><img class="idk_logo2 img-responsive" src="<?php getSiteURL(); ?>images/<?php echo getSubdomainr(); ?>_logo_s.png" /></a>
	<?php } ?>
</div>
<?php if(!in_array("11",$getEmployeeStatus)){ ?>
<div id="idk_add_button">
	<div class="dropdown">
		<a href="#" class="dropdown-toggle" id="idk_add_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
			<i class="fa fa-plus fa-lg" aria-hidden="true"></i>
		</a>
		<ul class="dropdown-menu" aria-labelledby="idk_add_dropdown">
			<li data-toggle="tooltip" data-placement="right" title="Kompanija"><a href="companies?page=add"><i class="fa fa-briefcase fa-lg" aria-hidden="true"></i></a></li>
			<li data-toggle="tooltip" data-placement="right" title="Kontakt"><a href=""><i class="fa fa-address-card-o fa-lg" aria-hidden="true"></i></a></li>
			<li data-toggle="tooltip" data-placement="right" title="Zaposlenik"><a href="employees?page=add"><i class="fa fa-user-plus fa-lg" aria-hidden="true"></i></a></li>
			<li data-toggle="tooltip" data-placement="right" title="Poruka"><a href="messages?page=new"><i class="fa fa-envelope-o fa-lg" aria-hidden="true"></i></a></li>
		</ul>
	</div>
</div>
<?php } ?>
<div id="idk_search_header">
	<form>
		<!--<input type="search" placeholder="&#xf002; Traži proizvode, narudžbe, kupce ..." required>-->
	</form>
</div>
<style>
	.alertx{
		height: 100%;
		margin-bottom: 0px;
		border: 1px solid transparent;
		border-radius: 0px;
		font-size: medium;
		font-weight: bold;
		padding: 0px 15px;
	}
	.alert-dangerx{
		color: #ffffff;
		background-color: #f3413c;
		border-color: #f3413c;
	}
	.blinking{
		animation:blinkingText 5s infinite;
	}
	@keyframes blinkingText{
		0%{
			background-color: #f3413c;
		}
		49%{
			background-color: #f3413c;
		}
		60%{
			background-color: transparent; color: #f3413c; border-color: transparent;
		}
		99%{
			background-color: transparent; color: #f3413c; border-color: transparent;
		}
		100%{
			background-color: #f3413c;
		}
	}
	.click_under:hover {
		text-decoration: none !important;
	}
	.notification_text{
		display: flex;
	}
	@media only screen and (max-width: 1654px) {
		.dipl_notif .notification_text{
			display: none;
		}
		.alertx{
			padding: 13px 15px;
		}
	}
	.dipl_notif{
		float: left;
		height: 50px;
		margin-left:1px;
	}
</style>
<?php
	if(in_array($logged_employee_id, array(158,92,43,212,90,369,231))){
		$uslovUstanova = "";
		if(in_array($logged_employee_id, array(92,43,212,369,231))){
			$uslovUstanova = "(pred.pr_domaca_valuta LIKE 'EUR' OR  pred.pr_domaca_valuta LIKE 'BAM')";
		}else if($logged_employee_id == 90){
			$uslovUstanova = "( pred.pr_domaca_valuta LIKE 'RSD' )";
		}else{
			$uslovUstanova = "(pred.pr_domaca_valuta LIKE 'EUR' OR  pred.pr_domaca_valuta LIKE 'BAM' OR pred.pr_domaca_valuta LIKE 'RSD')";
		}
		
		$dipl_notif = $db->prepare("
			SELECT
				id_broj_nd_kandidata
			FROM 
				idk_nd_kandidata kan
			INNER JOIN 
				idk_predracuni pred
			ON 
				kan.id_broj_nd_kandidata = pred.pr_kandidat_id
			WHERE 
				kan.status_nd_kandidata = 2 
				AND 
				kan.idd_ustanova_nd is null
				AND 
				pred.pr_rata = 1
				AND 
				pred.pr_status = 2
				AND 
				pred.pr_datum_uplate is not null
				AND 
				".$uslovUstanova."
		");
		$dipl_notif->execute();
		$broj_dipl_notif = $dipl_notif->rowCount();
		if($broj_dipl_notif != 0){
			$br_kand_ispis = $broj_dipl_notif;
			// $row_dipl_notif = $dipl_notif->fetch();
			// $id_kand = $row_dipl_notif["id_broj_nd_kandidata"];
?>
		<div class = "dipl_notif" title="Povezati sa ustanovom">
			<a class = "click_under" href="<?php getSiteUrl(); ?>odbijeni_ugovori_agenta.php?page=povezi_ustanovom">
				<div class="alert alertx alert-danger alert-dangerx text-center blinking" role="alert">
					<span class="notification_text" style = "font-size: small;">Povezati sa ustanovom</span></br>
					<?php echo $br_kand_ispis; ?>
				</div>
			</a>
		</div>
<?php
		}
	}
?>

<?php
	$employee_status = explode( ',' , getEmployeeStatus());
	if(in_array( "1" , $employee_status) OR in_array("2", $employee_status) OR in_array("3", $employee_status) OR in_array("15", $employee_status)){
		$uslov_odbijeni_ugovori = "";
		if(in_array("1" , $employee_status) AND ($logged_employee_id == 158 OR $logged_employee_id == 173)){
			$uslov_odbijeni_ugovori = "ug.ug_zaposlenik_id is not null";
		}else{
			$uslov_odbijeni_ugovori = "ug.ug_zaposlenik_id = ".$logged_employee_id."";
		}
		
		if($uslov_odbijeni_ugovori != ""){
			$query_get_br_otvoren_link = $db->prepare("
				SELECT 
					COUNT(ug.ug_id) AS brojUgovora
				FROM 
					idk_nd_ugovori ug
				JOIN 
					idk_nd_kandidata kan
				ON 
					kan.id_broj_nd_kandidata = ug.ug_kandidat_id
				WHERE 
					ug.ug_status = 4
					AND 
					HOUR(TIMEDIFF(:vrijeme, ug.ug_datum_otvaranja_linka)) >= 2
					AND 
					".$uslov_odbijeni_ugovori."
			");
			$query_get_br_otvoren_link->execute(array(
				':vrijeme' => date("Y-m-d H:i:s")
			));
			$row_get_br_otvoren_link = $query_get_br_otvoren_link->fetch();
			$cnt_otvoren_link = $row_get_br_otvoren_link["brojUgovora"];
			
			$query_get_br_odbijenih_ugovora = $db->prepare("
				SELECT
					count(ug.ug_id) as cnt
				FROM 
					idk_reminders rem
				JOIN 
					idk_nd_ugovori ug 
				ON 
					ug.ug_id = rem.reminder_foreign_key
				WHERE 
					rem.reminder_type = 2
					AND 
					rem.reminder_status = 1
					AND 
					".$uslov_odbijeni_ugovori."
			");
			$query_get_br_odbijenih_ugovora -> execute();
			$row_get_br_odbijenih_ugovora = $query_get_br_odbijenih_ugovora -> fetch();
			$cnt_odbijeni_ugovori = $row_get_br_odbijenih_ugovora["cnt"];
			
			if($cnt_odbijeni_ugovori != 0){
?>
				<div class="dipl_notif" title="Odbijenih ugovora">
					 <a class="click_under" href="<?php getSiteURL();?>odbijeni_ugovori_agenta.php?page=list"> 
						<div class="alert alertx alert-danger alert-dangerx text-center blinking">
							<span class="notification_text" style = "font-size: small;">Odbijenih ugovora</span> </br> <?php echo $cnt_odbijeni_ugovori;?>
						</div>
					</a>
				</div>
<?php	
			}
			
			if($cnt_otvoren_link != 0){
?>
				<div class="dipl_notif" title="Otvoren link">
					 <a class="click_under" href="<?php getSiteURL();?>odbijeni_ugovori_agenta.php?page=otvoren_link"> 
						<div class="alert alertx alert-danger alert-dangerx text-center blinking">
							<span class="notification_text" style = "font-size: small;">Otvoren link</span></br> <?php echo $cnt_otvoren_link;?>
						</div>
					</a>
				</div>
<?php
			}
		}
	}
	//Inbound poziv
	if(in_array($logged_employee_id, array(75,173,158,201))){
		$queryInbound = $db->prepare("
			SELECT 
				count(id_broj_nd_kandidata) AS brojInbound
			FROM 
				idk_nd_kandidata
			WHERE 
				zaduzen_zaposlenik_nd_kandidata = 139 
				AND 
				inbound_aktivan = 1
		");
		$queryInbound->execute();
		$rowInbound = $queryInbound->fetch();
		$brojInbound = intval($rowInbound["brojInbound"]);
		if($brojInbound != 0){
?>
			<div class="dipl_notif" title="Inbound Pozivi">
					<a class="click_under" target="_BLANK" href="<?php getSiteURL();?>dipl.php?page=iboundPoziviSkladiste"> 
					<div class="alert alertx alert-danger alert-dangerx text-center blinking">
						<span class="notification_text" style = "font-size: small;">Inbound Pozivi</span></br> <?php echo $brojInbound;?>
					</div>
				</a>
			</div>
<?php
		}
	}
?>

<!-- Aktivni reminderi od PM-a START -->
	<?php 
		if (in_array("2", $employee_status)) {
			$active_pp_reminder_user 				= getCountPPRemindersForEmployeArrayR(1); 
			$active_pp_reminder_controlling_user 	= getCountPPRemindersForEmployeArrayR(2);
			if ($active_pp_reminder_user['status'] != 1) {
				?>
					<div class="dipl_notif" title="Aktivni reminderi">
						<a class="click_under" target="_BLANK" href="<?php getSiteURL();?>dashboardRemindera/reminders_of_project_manager?page=list&type=1">
							<div class="alert alertx alert-danger alert-dangerx text-center blinking">
								<span class="notification_text" style = "font-size: small;">Aktivni reminderi</span> <?php echo $active_pp_reminder_user['count'];?>
							</div>
						</a>
					</div>
				<?php
			}
			if ($active_pp_reminder_controlling_user['status'] != 1) {
				?>
					<div class="dipl_notif" title="Aktivni controlling reminderi">
						<a class="click_under" target="_BLANK" href="<?php getSiteURL();?>dashboardRemindera/reminders_of_project_manager?page=list&type=2">
							<div class="alert alertx alert-danger alert-dangerx text-center blinking">
								<span class="notification_text" style = "font-size: small;">Aktivni controlling reminderi</span> <?php echo $active_pp_reminder_controlling_user['count'];?>
							</div>
						</a>
					</div>
				<?php
			}
			unset($active_pp_reminder_user);
			unset($active_pp_reminder_controlling_user);
		}
	?>
<!-- Aktivni reminderi od PM-a END -->
<!-- Termini za poziv FIRST CALL START -->
	<?php
		$row_calls = getCallAppointmentSalesForEmployee($logged_employee_id, 0);
		if($row_calls){
			$broj_poziva = count($row_calls);
			$link_client = getSiteUrlr()."sales?page=open&id=".$row_calls[0]["sr_client_id"];
			// var_dump($link_client);
			?>
				<div class="dipl_notif" title="Aktivni reminderi">
					<a class="click_under" target="_BLANK" href="<?php echo $link_client; ?> ">
						<div class="alert alertx alert-danger alert-dangerx text-center blinking">
							<span class="notification_text" style = "font-size: small;">First Call pozivi</span> <?php echo $broj_poziva;?>
						</div>
					</a>
				</div>
			<?php
		}
	?>
	
<!-- Termini za poziv FIRST CALL END -->

<!-- Termini za poziv Sales call START -->
<?php
		$row_sales = getCallAppointmentSalesForEmployee($logged_employee_id, 1);
		if($row_sales){
			$broj_poziva_sales = count($row_sales);
			$link_client_sales = getSiteUrlr()."sales?page=open&id=".$row_sales[0]["sr_client_id"];
			// var_dump($link_client);
			?>
				<div class="dipl_notif" title="Aktivni reminderi">
					<a class="click_under" target="_BLANK" href="<?php echo $link_client_sales; ?> ">
						<div class="alert alertx alert-danger alert-dangerx text-center blinking">
							<span class="notification_text" style = "font-size: small;">Sales pozivi</span> <?php echo $broj_poziva_sales;?>
						</div>
					</a>
				</div>
			<?php
		}
	?>
	
<!-- Termini za poziv Sales call END -->

<?php
	if($logged_employee_id == 207 && date('d.m') == "23.08")
		echo '<i style = "font-size:30px; margin-left:20px;">Sretan rođendan Farise <3</i><i>(Džaba što ti je rođendan, neće poruke nikakve danas ići)</i>';
?>

<div id="idk_topbar">
	<ul>
		<li class="dropdown idk_dropdown_static idk_search_hidden">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
				<i class="fa fa-search fa-lg" aria-hidden="true"></i>
			</a>
			<ul class="dropdown-menu dropdown-menu-right idk_dropdown_menu" aria-labelledby="idk_user_notifications">
				<div id="idk_search_header_dropdown">
					<form>
						<input type="search" placeholder="&#xf002; Traži ..." required>
					</form>
				</div>
			</ul>
		</li>
		<?php 
		if(!in_array("15",$getEmployeeStatus)){ 
			?>
			<?php 
			if(getEmployeeStatus() != 11 AND getEmployeeStatus() != 55){ 
				?>
				<li class="dropdown idk_dropdown_static">
					<a href="#" class="dropdown-toggle" id="idk_mail_notifications" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
						<i class="fa fa-envelope-o fa-lg" aria-hidden="true"></i>
						<div class="getNumberOfMessages"></div>
					</a>
					<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="idk_mail_notifications">
						<li class="idk_dropdown_header">Posljednje primljene poruke</li>
						<div id="idk_mail_notifications_scroll">
							<li>
								<ul class="idk_dropdown_menu getMessages_10"></ul>
							</li>
						</div>
						<li class="idk_dropdown_footer"><a href="<?php getSiteURL(); ?>messages?page=list">Vidi sve poruke</a></li>
					</ul>
				</li>
				<li class="dropdown idk_dropdown_static">
					<a href="#" class="dropdown-toggle" id="idk_notifications" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
						<i class="fa fa-bell-o fa-lg" aria-hidden="true"></i>
						<span <?php /*if(getUnseenPonovnePrijave() == 0) {*/ echo 'style="display: none;"'; /*}*/ ?> ></span>
					</a>
					<ul class="dropdown-menu dropdown-menu-right" >
						<li class="idk_dropdown_header">Ponovne prijave</li>
						<li class="idk_dropdown_header" style="display:none;"><span id="notifications_mark_read" data-id="<?php echo $logged_employee_id; ?>">Označi sve kao pročitano</span></li>
						<script>
							$('#notifications_mark_read').on('click', function(){
								var data_id = $(this).data('id');
								$.ajax({
									url: "do.php?form=notifications_mark_read",
									type: "POST",
									data: {id: data_id},
									dataType: "html",
									success: notifications_mark_read
								});
							});
						</script>
						<!--
						<div id="idk_notifications_scroll">
							<li>
								<ul class="idk_dropdown_menu getNotifications_20"></ul>
							</li>
						</div>
						-->
						<div id="idk_notifications_scrolll">
							<li>
								<ul class="idk_dropdown_menu">
									<?php
									$query_notf_prijave= $db->prepare("
													/*SELECT not_text, not_url, not_date, not_status, not_id
													FROM idk_notification
													WHERE not_type = 2 AND not_status = 0
													ORDER BY not_id DESC
													LIMIT 20*/
													");
									
									$query_notf_prijave->execute();
									
									while($ponovne_prijave = $query_notf_prijave->fetch()){
										
										$not_id_np = $ponovne_prijave['not_id'];
										$not_text_np = $ponovne_prijave['not_text'];
										$not_url_np = $ponovne_prijave['not_url'];
										$not_date_np = $ponovne_prijave['not_date'];
										$not_date_f_np = date('d.m.Y H:i', strtotime($not_date_np));
										$not_status_np = $ponovne_prijave['not_status'];
										
										if($not_status_np == 0){
											$class_for_new_message_np = "idk_new_notification";
										}else{
											$class_for_new_message_np = "";
										}
										
									?>
									<li style="list-style:none;">
										<a href="<?php /*getSiteUrl()notifications?page=list_ponovne_prijave*/; ?>" class="<?php echo $class_for_new_message_np; ?>">
											<h3><?php echo $not_text_np; ?></h3>
											<small class="pull-right" style="margin-top: -10px;"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $not_date_f_np; ?></small>
										</a>
									</li>
									<?php } ?>
								</ul>
							</li>
						</div>
						
						<li class="idk_dropdown_footer"><a href="<?php /*getSiteUrl()notifications?page=list_ponovne_prijave*/; ?>">Vidi sve nove prijave</a></li>
					</ul>
				</li>
				<?php 
			}
		} ?>
		<?php 
		if(!in_array("15",$getEmployeeStatus)){
			?>
			<?php 
			if(getEmployeeStatus() != 11 AND getEmployeeStatus() != 55){
				?>
				<li class="dropdown idk_dropdown_static">
					<a href="#" class="dropdown-toggle" id="idk_tasks_notifications" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
						<i class="fa fa-tasks fa-lg" aria-hidden="true"></i>
						<!-- <span><?php /*getUnredNotification()*/; ?></span> -->
					</a>
					<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="idk_tasks_notifications">
						<li class="idk_dropdown_header">Imate 0 novih prijava</li>
						<div id="idk_tasks_notifications_scroll">
							<li>
								<ul class="idk_dropdown_menu">
									<?php
									$query_notifications = $db->prepare("
													/*SELECT not_text, not_url, not_date, not_status
													FROM idk_notification
													WHERE not_type = 1
													ORDER BY not_id DESC
													LIMIT 20*/
													");
									
									$query_notifications->execute();
									
									while($notification = $query_notifications->fetch()){
										
										$not_text = $notification['not_text'];
										$not_url = $notification['not_url'];
										$not_date = $notification['not_date'];
										$not_date_f = date('d.m.Y H:i', strtotime($not_date));
										$not_status = $notification['not_status'];
										
										if($not_status == 0){
											$class_for_new_message = "idk_new_notification";
										}else{
											$class_for_new_message = "";
										}
										
									?>
									<li>
										<a href="<?php getSiteUrl(); ?><?php echo $not_url; ?>&update_not=1" class="<?php echo $class_for_new_message; ?>">
											<h3><?php echo $not_text; ?></h3>
											<small class="pull-right" style="margin-top: -10px;"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $not_date_f; ?></small>
										</a>
									</li>
									<?php } ?>
								</ul>
							</li>
						</div>
						<li class="idk_dropdown_footer"><a href="<?php /*getSiteUrl()notifications?page=list*/; ?>">Vidi sve</a></li>
					</ul>
				</li>
				<?php 
			}
		} ?>
		<?php if(getEmployeeStatus() != 55){ ?>
		<li class="dropdown idk_dropdown_static">
			<a href="#" class="idk_header_user_link dropdown-toggle" id="idk_user_notifications" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
				<ul class="list-inline">
					<li><img src="<?php getSiteURL(); ?>files/employees/<?php getEmployeeImage(); ?>" class="idk_header_user_image" alt="User Image" /></li>
					<li class="idk_user_text_hidden"><?php getEmployeeFullname(); ?></li>
				</ul>
			</a>
			<ul class="dropdown-menu dropdown-menu-right idk_dropdown_menu" aria-labelledby="idk_user_notifications">
				<li class="idk_user_header">
					<img src="<?php getSiteURL(); ?>files/employees/<?php getEmployeeImage(); ?>" class="img-circle" alt="User Image">
					<p><?php getEmployeeFullname(); ?><br /><small><?php getEmployeePosition(); ?></small></p>
				</li>
				<?php if(!in_array("15",$getEmployeeStatus)){ ?>
				<?php if(getEmployeeStatus() != 11){ ?>
				<li><a href="<?php getSiteURL(); ?>employees?page=open&id=<?php echo $logged_employee_id; ?>"><i class="fa fa-user" aria-hidden="true"></i> Moj profil</a></li>
				<li><a href="<?php getSiteURL(); ?>employees?page=edit_profile"><i class="fa fa-edit" aria-hidden="true"></i> Uredi profil</a></li>
				<li><a href="<?php getSiteURL(); ?>logs?page=list"><i class="fa fa-file-text-o" aria-hidden="true"></i> Pregledaj LOG</a></li>
				<?php } ?>
				<?php } ?>
				<li><a href="<?php getSiteURL(); ?>do.php?form=logout"><i class="fa fa-lock" aria-hidden="true"></i> Log Out</a></li>
			</ul>
		</li>
		<?php } ?>
	</ul>
</div>
<script>
$('.dropdown-menu').click(function(e) {
	e.stopPropagation();
});
</script>
