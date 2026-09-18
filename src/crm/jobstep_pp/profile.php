<?php 
	include("includes/function.php");
	
	$isLoggedIn = isLoggedInR();
	
	if($isLoggedIn == 1){
		$languageUser = getLanguageForUser($userId);;
		include("includes/language/language.php");
?>
<!doctype html>
<html class="h-100">
	<head>
		<?php
			include("includes/head.php"); 
		?>
	</head>
	<body class = "d-flex flex-column h-100">
		<div id="page-cover"></div>
		<div class="lds-dual-ring_big" style = "display:none;"></div>
		<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 2000">
			<div id="notification_toast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
				<div class="toast-header">
					<strong class="me-auto" id = "toast_title"></strong>
					<button id = "dismiss_toast" type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
				</div>
				<div id = "toast_text" class="toast-body">

				</div>
			</div>
		</div>
		<task-manager style = "display:none;background-color:#EEEEF2;height:100vh"></task-manager>
		<?php
			include("includes/navbar/navbar.php"); 
		?>
		<div id = "main-container" class="container-fluid justify-content-center d-flex h-100 my-2 scrollbar-hidden overflow-auto">
			<div class = "row align-items-center w-100">
				<div class = "col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
					<div class="container">
					<?php
						include("includes/notifications/notifications.php");
						include("includes/user/user_modal.php");
						$kandidat_id = $_GET["kandidat_id"];
						$type = intval($_GET["type"]);
						$bar_id = intval($_GET["bar_id"]);
						$nalog_id = intval($_GET["n"]);
					?>
						<main class=  "my-2" id = "candidateProfile" style = "display: none;">
						</main>
						<main class=  "my-2" id = "candidateViewInterview" style = "display: none;">
						</main>
						<main class=  "my-2" id = "candidateInsertInterview" style = "display: none;">
						</main>
					
						<script>
							
							let translate 		= [];
							let user_language 	= [];

							function setTranslation(){
								$.ajax({
									url: 'ajax.php?action=get_translation',
									type: 'POST',
									dataType: 'json',
									data:{},
									success: function (response){
										translate = JSON.parse(response['translation']);
										user_language = JSON.parse(response['user_language']);
									},
									error: function (xhr, ajaxOptions, thrownError) {
										alert(xhr.status);
										alert(thrownError);
									}
								});
							}
							function getTranslation(word){
								// console.log(translate);
								return translate[word][user_language];
							}
							
							function handleTaskManagerOpenProfile(){
								$('#main-container').hide('fade', 300, function(){
									$(this).removeClass('d-flex');
									$('#main-navbar').hide('blind', 300, function(){
										$('task-manager').show('fade');
										document.querySelector("task-manager").requestUpdate();
										document.querySelector("task-manager").shadowRoot.querySelector("tm-menu").manualUpdate();
									});
								});
							}
							$(document).ready(function(){
								setTranslation();
								$('#button_task_manager').unbind('click').bind('click',handleTaskManagerOpenProfile);

								$('#search_candidates').unbind('click').bind('click', openSearchCandidateModal);

								$('#dismiss_toast').on('click', function(){
									$('#notification_toast').hide('clip', 600);
								});

								getLoaderBig();
								var readyKanId = "<?php echo $kandidat_id; ?>";
								var type = parseInt("<?php echo $type; ?>");
								var bar_id = parseInt("<?php echo $bar_id; ?>");
								var nalog_id = parseInt("<?php echo $nalog_id; ?>");
								$.ajax({
									url: 'ajax.php?action=candidateProfile',
									type: 'POST',
									data: {
										'kandidat_id':readyKanId,
										'type':type,
										'bar_id':bar_id,
										'nalog_id':nalog_id
									},
									dataType: 'html',
									success: function(data){
										$("#candidateProfile").html(data);
										$( "#candidateProfile" ).show( 'slide', 1000);
										$("#edit_candidate_partner_data").unbind('click').bind('click', handleEditCandidatePartnerData);
										removeLoader();
									},
									error: function (xhr, ajaxOptions, thrownError) {
										alert(xhr.status);
										alert(thrownError);
									}
								});
								// $( "#candidateInter" ).hide(function(){
									// $( "#candidateProfile" ).show( 'slide', 500);
								// });
							});
						</script>
					</div>
				</div>
			</div>
		</div>
		<?php
			include("includes/footer.php"); 
		?>
	</body>
</html>
<?php
		unset($txtArray);
	}else{
		header("Location:".getSiteUrlr()."landing.php");
	}
?>