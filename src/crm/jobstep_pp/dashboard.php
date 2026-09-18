<?php 

	include("includes/function.php");
	// include("includes/connect.php");
	ini_set('display_errors', 0);
	ini_set('error_log', 'error_log');
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	
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
		<style>
		.star_table{
			color: #FDD878;
		}
		.nalozi_list_selected{
			border-radius: 10px;
			border-left: 2px solid #EA6676;
			border-top: 2px solid #EA6676;
			border-bottom: 2px solid #EA6676;
			padding: 5px;
		}
		.partners_list_selected{
			border-radius: 10px;
			border-left: 2px solid #388923;
			border-top: 2px solid #388923;
			border-bottom: 2px solid #388923;
			padding: 8px;
		}
		.circle-progress {
			width: 200px; height: auto;
		}
		.circle-progress-value {
			stroke-width: 6px;
			stroke: hsl(280, 90%, 50%);
			stroke-linecap: round;
		}
		.circle-progress-circle {
			stroke-width: 1px;
		}
		.circle-progress-text{
			stroke-width:0px!important;
		}
		.progress{
			height: auto!important;
			background-color: white;
			position: relative;
			margin: 0 auto;
		}
		text{
			font-size: 10px!important;
			font-weight: 100!important;
			width:30px!important;
		}
		.color_casting{
			stroke:#7AB6D9!important;
		}
		.color_interview{
			stroke:#FDD878!important;
		}
		.color_passed{
			stroke:#79B465!important;
		}
		.color_rejected{
			stroke:#EB8888!important;
		}
		.color_waiting_contract{
			stroke:#b3d3e5!important;
		}
		.color_contract_sent{
			stroke:#7ab6d9!important;
		}
		.color_contract_signed{
			stroke:#1d84c0!important;
		}
		.color_started_working{
			stroke:#79b465!important;
		}
		.color_nostrification_pending{
			stroke:#EB8888!important;
		}
		.color_nostrification_started{
			stroke:#1d84c0!important;
		}
		.color_nostrification_finished{
			stroke:#79b465!important;
		}
		.color_language_A0{
			stroke:#EB8888!important;
		}
		.color_language_A1{
			stroke:#7ab6d9!important;
		}
		.color_language_A2{
			stroke:#1d84c0!important;
		}
		.color_language_B1{
			stroke:#79b465!important;
		}
		.color_colecting_documents{
			stroke:#b3d3e5!important;
		}
		.color_waiting_visa{
			stroke:#7ab6d9!important;
		}
		.color_result_of_visa{
			stroke:#1d84c0!important;
		}

		.progress_bar_click:hover{
			cursor:pointer;
		}
		.table_text{
			font-style: normal;
			font-weight: normal;
			font-size: 16px;
			line-height: 19px;
			padding-left: 20px;
		}
		.table_nalog_icon_column{
			width: 6%;
			padding-top: 8px;
			padding-bottom: 8px;
			text-align: center;
			font-size:16px;
		}
		.table_nalog_icon_selected{
			background: #F8CED3;
			border-top-left-radius: 15px;
			border-bottom-left-radius: 15px;
		}
		.table_nalog_selected{
			background: #FCE9EB;
			border-top-right-radius: 15px;
			border-bottom-right-radius: 15px;
		}
		.table_nalog_icon{
			color: #EA6676;
		}

		.table_partner_icon_column{
			width: 6%;
			padding-top: 8px;
			padding-bottom: 8px;
			text-align: center;
			font-size:16px;
		}
		.table_partner_icon_selected{
			background: #8ecc7a;
			border-top-left-radius: 15px;
			border-bottom-left-radius: 15px;
		}
		.table_partner_selected{
			background: #c9ecc0;
			border-top-right-radius: 15px;
			border-bottom-right-radius: 15px;
		}
		.table_partner_icon{
			color: #388923;
		}
		.table_partner_my_company{
			background: #bff4e7;
			border-top-right-radius: 15px;
			border-bottom-right-radius: 15px;
		}
		.row_handle_partner_click:hover{
			cursor:pointer;
		}
		.row_handle_partner_click:hover > .table_partner_icon_column {
			background: #8ecc7a;
			border-top-left-radius: 15px;
			border-bottom-left-radius: 15px;
		}
		.row_handle_nalog_click:hover{
			cursor:pointer;
		}
		.row_handle_nalog_click:hover > .table_nalog_icon_column {
			background: #F8CED3;
			border-top-left-radius: 15px;
			border-bottom-left-radius: 15px;
		}
		.view_choice{
			font-size: 28px;
			line-height: 34px;
			padding: 15px;
			background: #EDEEFF;
			border-top-right-radius: 10px;
			border-bottom-left-radius: 10px;
			border-top-left-radius: 30px;
			border-bottom-right-radius: 30px;
			color:gray;
		}
		.view_choice_superadmin:hover{
			color:#EA6676;
		}
		.view_choice_superadmin_selected{
			color:#EA6676;
			border: 1px solid #EA6676!important;
		}
		.view_choice_admin:hover{
			color:#388923;
		}
		.view_choice_admin_selected{
			color:#388923;
			border: 1px solid #388923!important;
		}
		::-webkit-scrollbar {
			width: 4px;
		}
		::-webkit-scrollbar-track {
			background: white;
		}

		::-webkit-scrollbar-thumb {
			background: #18a0fb;
			border-radius:15px;
		}

		::-webkit-scrollbar-thumb:hover {
			background: #51afee;
		}

		</style>
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
		<div class="container-fluid justify-content-center d-flex h-100 my-2 scrollbar-hidden overflow-auto" id = "main-container">
		
			<div class = "row  w-100">
				<div class = "col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 col-xxl-12">
					<div class="container">
						<?php
							include("includes/notifications/notifications.php");
							include("includes/user/user_modal.php");
							
						?>
						<main class=  "mb-5">
							<div class = "row">
								<div class = "col-12 mt-4">
									
									<div id = "db_forecast_calendar" style = "display:none;" class = "forecast_box">
										<div class = "forecast_box_head"></div>
										<div id = "db_forecast_calendar_input" style = "margin-left: 63%;position: relative;margin-bottom:20px;"></div>
										<div id = "db_forecast_calendar_table" ></div>
									</div>
									<div id = "db_forecast_list" style = "display:none" class = "forecast_box">
										<div class = "forecast_box_head"></div>
										<div id = "db_forecast_list_table"></div>
									</div>
									<div id = "db_view_choice" 		style = "display:none"></div>
									<div id = "db_progress_bar" 	style = "display:none"></div>
									<div id = "db_list_candidates"	style = "display:none"></div>
									<div class = "row">
										<div id = "db_list_nalozi" 		style = "display:none" class="col-md-5 offset-1 mt-5"></div>
										<div id = "db_list_partners" 	style = "display:none" class="col-md-5 mt-5"></div>
									</div>
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
										$(document).ready(function() {
											getLoaderBig();
											setTranslation();
											$('#button_task_manager').unbind('click').bind('click',handleTaskManagerOpen);
											$('#search_candidates').unbind('click').bind('click', openSearchCandidateModal);
											


											$('#forecast_view').unbind('click').bind('click', handleForecastViewClick);
											$('#forecast_view').unbind('mouseover').bind('mouseover', handleForecastViewMouseover);
											$('#forecast_view').unbind('mouseleave').bind('mouseleave', handleForecastViewMouseleave);
											$('#casting_view').unbind('click').bind('click', handleCastingViewClick);
											$('#casting_view').unbind('mouseover').bind('mouseover', handleCastingViewMouseover);
											$('#casting_view').unbind('mouseleave').bind('mouseleave', handleCastingViewMouseleave);
											$('#departure_view').unbind('click').bind('click', handleDepartureViewClick);
											$('#departure_view').unbind('mouseover').bind('mouseover', handleDepartureViewMouseover);
											$('#departure_view').unbind('mouseleave').bind('mouseleave', handleDepartureViewMouseleave);
											$('#dismiss_toast').on('click', function(){
												$('#notification_toast').hide('clip', 600);
											});
											var nalog_id 	= 0;
											var type 		= 0;
											var bar_id 		= 0;
											var partner_id 	= 0;
											var pb_text 	= '';
											
											var get_view_choice = 
												$.ajax({
													url: 'ajax.php?action=get_view_choice',
													type: 'POST',
													dataType: 'html',
													error: function (xhr, ajaxOptions, thrownError) {
														alert(xhr.status);
														alert(thrownError);
													}
												});
											var  get_progress_bar = 
												$.ajax({
													url: 'ajax.php?action=get_progress_bar',
													type: 'POST',
													dataType: 'json',
													data:{
														'nalog_ids'		: nalog_id,
														'type' 			: type,
														'partner_ids'	: partner_id
													},
													error: function (xhr, ajaxOptions, thrownError) {
														alert(xhr.status);
														alert(thrownError);
													}
												});
											var get_list_nalozi = 
												$.ajax({
													url: 'ajax.php?action=get_list_nalozi',
													type: 'POST',
													dataType: 'html',
													data : {
														'type' : 0
													},
													error: function (xhr, ajaxOptions, thrownError) {
														alert(xhr.status);
														alert(thrownError);
													}
												});
											var get_list_partners = 
												$.ajax({
													url: 'ajax.php?action=get_list_partners',
													type: 'POST',
													dataType: 'html',
													data:{
														'nalog_ids' : 0
													},
													error: function (xhr, ajaxOptions, thrownError) {
														alert(xhr.status);
														alert(thrownError);
													}
												});
											
											$.when(get_view_choice, get_progress_bar, get_list_nalozi, get_list_partners).then(
											function(response_view_choice, response_progress_bar, response_list_nalozi, response_list_partners){
												$('#db_view_choice').append(response_view_choice[0]);
												$('#db_progress_bar').append(response_progress_bar[0]['code_to_append']);
												
												for(var i = 0; i<parseInt(response_progress_bar[0]['bar_type'].length); i++){
													if(!response_progress_bar[0]['returns_status_bar']){
														pb_text = response_progress_bar[0]['bar_text'][i] + ' ' +response_progress_bar[0]['bar_value'][i];
													}
													else{
														pb_text = response_progress_bar[0]['bar_text'][i];
													}
													$(response_progress_bar[0]['bar_type'][i]).circleProgress({
														animationDuration : 0,
														max: parseInt(response_progress_bar[0]['bar_max'][i]),
														value: parseInt(response_progress_bar[0]['bar_value'][i]),
														textFormat: function(value, max) {
															return pb_text;
														}
													});
													$(response_progress_bar[0]['bar_type'][i]).children().children('.circle-progress-value').addClass(response_progress_bar[0]['bar_style'][i]);
													if(response_progress_bar[0]['returns_status_bar']){
														$(response_progress_bar[0]['bar_type'][i]).children().children('text').attr('y',35);
													}
												}
												$('#applied_candidates').fadeOut(200).empty().append(response_progress_bar[0]['applied_candidates']).fadeIn();
												$('#db_list_nalozi').append(response_list_nalozi[0]);
												if(response_progress_bar[0]['returns_status_bar']){
													checkNaloziPartneriBox(1);
												}
												$('#view_choice_superadmin').on('click', handleSuperadminClick);
												$('#view_choice_admin').on('click', handleAdminClick);
												$('.progress_bar_click').on('click',handleProgressBarClick);
												$('.row_handle_nalog_click').bind('click', handleNalogClick);
												if($('#casting_view').hasClass('unclickable')){
													getCircleBarMenu(function(response_circle_bar_menu){
														$('#db_view_choice').fadeOut(200,function(){
															$(this).empty().append(response_circle_bar_menu).show('size', 400);
															$('#menu_bars_contract').addClass('circle_bar_menu_selected');
															$('#menu_bars_contract').unbind().bind('click', handleCircleBarMenuClick);
															$('#menu_bars_diploma_certificate').unbind().bind('click', handleCircleBarMenuClick);
															$('#menu_bars_visa').unbind().bind('click', handleCircleBarMenuClick);
															$('#db_progress_bar').show('blind',600, function(){
																removeLoader();
															});
														});
													});
												}
												$('#db_progress_bar').show('blind',600, function(){
													$('#db_list_nalozi').show('fade', 600, function(){
														// if(!response_progress_bar[0]['returns_status_bar']){
															$('#db_list_partners').append(response_list_partners[0]);
															$('.table_partner_icon_column').each(function(){
																$(this).addClass('table_partner_icon_selected');
																$(this).next().addClass('table_partner_selected');
															});
															$('.row_handle_partner_click').bind('click', handlePartnerClick);												
															$('#db_list_partners').show('slide',600);
														// }
														removeLoader();
													});
												});
											});
										});
									</script>
								</div>
							</div>
						</main>
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