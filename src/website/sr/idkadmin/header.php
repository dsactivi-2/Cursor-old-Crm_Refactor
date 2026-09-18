<div id="idk_loader"></div>
<div id="idk_logo">
	<a href="<?php getSiteURL(); ?>"><img class="idk_logo1 img-responsive" src="<?php getSiteURL(); ?>images/logo.png" /></a>
	<a href="<?php getSiteURL(); ?>"><img class="idk_logo2 img-responsive" src="<?php getSiteURL(); ?>images/logo_s.png" /></a>
</div>
<div id="idk_add_button">
	<div class="dropdown">
		<a href="#" class="dropdown-toggle" id="idk_add_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
			<i class="fa fa-plus fa-lg" aria-hidden="true"></i>
		</a>
		<ul class="dropdown-menu" aria-labelledby="idk_add_dropdown">
			<li data-toggle="tooltip" data-placement="right" title="Članak"><a href="posts?page=add"><i class="far fa-newspaper fa-lg" aria-hidden="true"></i></a></li>
			<li data-toggle="tooltip" data-placement="right" title="Sadržaj"><a href="content?page=add"><i class="far fa-copy fa-lg" aria-hidden="true"></i></a></li>
			<li data-toggle="tooltip" data-placement="right" title="Proizvod"><a href="products?page=add"><i class="fas fa-barcode fa-lg" aria-hidden="true"></i></a></li>
			<li data-toggle="tooltip" data-placement="right" title="Poruka"><a href="messages?page=new"><i class="fa fa-envelope-o fa-lg" aria-hidden="true"></i></a></li>
		</ul>
	</div>
</div>
<div id="idk_search_header">
	<form>
		<!--<input type="search" placeholder="&#xf002; Traži proizvode, narudžbe, kupce ..." required>-->
	</form>
</div>
<div id="idk_topbar">
	<ul>
		<!--<li class="dropdown idk_dropdown_static idk_search_hidden">
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
		</li>-->
		<li class="dropdown idk_dropdown_static">
			<a href="#" class="idk_header_user_link dropdown-toggle" id="idk_user_notifications" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
				<ul class="list-inline">
					<li class="idk_user_text_hidden">Trenutni jezik: SRPSKI</li>
				</ul>
			</a>
			<ul class="dropdown-menu dropdown-menu-right idk_dropdown_menu" aria-labelledby="idk_user_notifications">
				<li><a href="<?php echo $envConfig->WEBSITE_URL . "bs/idkadmin"; ?>">BOSANSKI</a></li>
				<li><a href="<?php echo $envConfig->WEBSITE_URL . "en/idkadmin"; ?>">ENGLESKI</a></li>
				<li><a href="<?php echo $envConfig->WEBSITE_URL . "de/idkadmin"; ?>">NJEMAČKI</a></li>
			</ul>
		</li>
		<li class="dropdown idk_dropdown_static">
			<a href="<?php getSiteUrlFront(); ?>" target="_blank">
				<i class="far fa-eye fa-lg" aria-hidden="true"></i>
			</a>
		</li>
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
				<div class="getNumberOfNotifications"></div>
			</a>
			<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="idk_notifications">
				<li class="idk_dropdown_header pull-left">Notifikacije</li>
				<li class="idk_dropdown_header pull-right"><span id="notifications_mark_read" data-id="<?php echo $logged_user_id; ?>">Označi sve kao pročitano</span></li>
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

				<div class="clearfix"></div>
				<div id="idk_notifications_scroll">
					<li>
						<ul class="idk_dropdown_menu getNotifications_20"></ul>
					</li>
				</div>
				<li class="idk_dropdown_footer"><a href="#">Vidi sve</a></li>
			</ul>
		</li>
		<li class="dropdown idk_dropdown_static">
			<a href="#" class="idk_header_user_link dropdown-toggle" id="idk_user_notifications" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
				<ul class="list-inline">
					<li><img src="<?php getSiteURL(); ?>files/users/<?php getUserImage(); ?>" class="idk_header_user_image" alt="User Image" /></li>
					<li class="idk_user_text_hidden"><?php getUserFullname(); ?></li>
				</ul>
			</a>
			<ul class="dropdown-menu dropdown-menu-right idk_dropdown_menu" aria-labelledby="idk_user_notifications">
				<li class="idk_user_header">
					<img src="<?php getSiteURL(); ?>files/users/<?php getUserImage(); ?>" class="img-circle" alt="User Image">
					<p><?php getUserFullname(); ?><br /><small><?php getUserStatusName(); ?></small></p>
				</li>
				<li><a href="<?php getSiteURL(); ?>employees?page=edit_profile"><i class="fa fa-edit" aria-hidden="true"></i> Uredi profil</a></li>
				<li><a href="<?php getSiteURL(); ?>logs?page=list"><i class="fa fa-file-text-o" aria-hidden="true"></i> Pregledaj LOG</a></li>
				<li><a href="<?php getSiteURL(); ?>do.php?form=logout"><i class="fa fa-lock" aria-hidden="true"></i> Log Out</a></li>
			</ul>
		</li>
	</ul>
</div>
<script>
$('.dropdown-menu').click(function(e) {
	e.stopPropagation();
});
</script>
