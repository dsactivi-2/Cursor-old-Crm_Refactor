function checkNaloziPartneriBox(do_option){
	// do_option - 1 ispisuje samo naloge
	// do_option - 2 ispisuje i naloge i partnere
	if(do_option == 1){
		$('#db_list_nalozi').removeClass('col-md-5');
		$('#db_list_nalozi').removeClass('offset-1');
		$('#db_list_nalozi').addClass('col-md-6');
		$('#db_list_nalozi').addClass('offset-3');
	}
	else if (do_option == 2){
		$('#db_list_nalozi').removeClass('col-md-6');
		$('#db_list_nalozi').removeClass('offset-3');
		$('#db_list_nalozi').addClass('col-md-5');
		$('#db_list_nalozi').addClass('offset-1');
	}
}
function getLoaderBig(){
	$("#page-cover").css('z-index', 1);
	 $("#page-cover").css("opacity",0.3).fadeIn(300, function () {            
		$('.lds-dual-ring_big').css({'z-index':9999}).fadeIn();;
	 });
}
function removeLoader(){
	$("#page-cover").fadeOut(200, function(){
		$(".lds-dual-ring_big").fadeOut(500);
		$("#page-cover").css("opacity",0.0);
		$("#page-cover").css('z-index', -1);
		
	})
}

function handleSuperadminClick(){
	if(!$(this).hasClass('view_choice_superadmin_selected')){
		$('#view_choice_superadmin').addClass('view_choice_superadmin_selected');
		$('#view_choice_admin').removeClass('view_choice_admin_selected');
		
		getLoaderBig();
		$('#db_list_nalozi').hide('fade', 350, function(){
			$('#db_progress_bar').hide('blind', 350, function(){
				var nalog_id 	= 0;
				var bar_id 	 	= 0;
				var partner_id 	= 0;
				var type 		= 1;
				
				var  get_progress_bar = 
					$.ajax({
						url: 'ajax.php?action=get_progress_bar',
						type: 'POST',
						dataType: 'json',
						data:{
							'nalog_ids' 	: nalog_id,
							'type' 			: type,
							'bar_id' 		: bar_id,
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
						data: {
							'type' : 1
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
						data: {
							'nalog_ids' : 0
						},
						error: function (xhr, ajaxOptions, thrownError) {
							alert(xhr.status);
							alert(thrownError);
						}
					});
				
				checkNaloziPartneriBox(2);
				$.when(get_progress_bar, get_list_nalozi, get_list_partners).then(
				function(response_progress_bar, response_list_nalozi, response_list_partners){
					$('#db_progress_bar').empty().append(response_progress_bar[0]['code_to_append']);
					$('#applied_candidates').empty().append(response_progress_bar[0]['applied_candidates']);
					for(var i = 0; i<parseInt(response_progress_bar[0]['bar_type'].length); i++){
						$(response_progress_bar[0]['bar_type'][i]).circleProgress({
							animationDuration : 0,
							max: parseInt(response_progress_bar[0]['bar_max'][i]),
							value: parseInt(response_progress_bar[0]['bar_value'][i]),
							textFormat: function(value, max) {
								return response_progress_bar[0]['bar_text'][i] + ' ' + response_progress_bar[0]['bar_value'][i];
							}
						});
						$(response_progress_bar[0]['bar_type'][i]).children().children('.circle-progress-value').addClass(response_progress_bar[0]['bar_style'][i]);
					}
					$('#db_list_nalozi').empty().append(response_list_nalozi[0]);
					$('#db_list_partners').empty().append(response_list_partners[0]);
					
					$('.progress_bar_click').on('click',handleProgressBarClick);
					$('.row_handle_nalog_click').unbind().bind('click', handleNalogClick);
					$('.row_handle_partner_click').unbind().bind('click', handlePartnerClick);
					
					$('#db_progress_bar').show('blind',600, function(){
						$('#db_list_nalozi').show('fade', 600, function(){
							$('#db_list_partners').show('slide',600);
							removeLoader();
						});
					});
				});
				
			});
		});
	}
}

function handleAdminClick(){
	if(!$(this).hasClass('view_choice_admin_selected')){
		$('#view_choice_superadmin').removeClass('view_choice_superadmin_selected');
		$('#view_choice_admin').addClass('view_choice_admin_selected');

		getLoaderBig();
		$('#db_list_partners').hide('slide', 250, function(){
			$('#db_list_nalozi').hide('fade', 250, function(){
				$('#db_progress_bar').hide('blind', 250, function(){
					var nalog_id 	= 0;
					var bar_id 	 	= 0;
					var partner_id 	= 0;
					var type 		= 2;
					
					var  get_progress_bar = 
						$.ajax({
							url: 'ajax.php?action=get_progress_bar',
							type: 'POST',
							dataType: 'json',
							data:{
								'nalog_ids' 	: nalog_id,
								'partner_ids'	: partner_id,
								'type'			: type
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
							data: {
								'type' : 2
							},
							error: function (xhr, ajaxOptions, thrownError) {
								alert(xhr.status);
								alert(thrownError);
							}
						});
					$.when(get_progress_bar, get_list_nalozi).then(
					function(response_progress_bar, response_list_nalozi){
						$('#db_progress_bar').empty().append(response_progress_bar[0]['code_to_append']);

						for(var i = 0; i<parseInt(response_progress_bar[0]['bar_type'].length); i++){
							$(response_progress_bar[0]['bar_type'][i]).circleProgress({
								animationDuration : 0,
								max: parseInt(response_progress_bar[0]['bar_max'][i]),
								value: parseInt(response_progress_bar[0]['bar_value'][i]),
								textFormat: function(value, max) {
									return response_progress_bar[0]['bar_text'][i];
								}
							});
							$(response_progress_bar[0]['bar_type'][i]).children().children('text').attr('y',35);
							$(response_progress_bar[0]['bar_type'][i]).children().children('.circle-progress-value').addClass(response_progress_bar[0]['bar_style'][i]);
						}
						$('#db_list_nalozi').empty().append(response_list_nalozi[0]);
						
						$('.progress_bar_click').on('click',handleProgressBarClick);
						$('.row_handle_nalog_click').unbind().bind('click', handleNalogClick);

						
						checkNaloziPartneriBox(1);
						$('#db_progress_bar').show('blind',600, function(){
							$('#db_list_nalozi').show('fade', 600);
							removeLoader();
						});
					});
					
				});
			});
		});	
	}
}
function handleProgressBarClick(){
	var type 		= $(this).attr('type');
	var bar_id 		= $(this).attr('bar_id');
	var nalog_id 	= $('.table_nalog_selected').parent().attr('nalog_id');
	var partner_id 	= $('.table_partner_selected').parent().attr('partner_id');
	if(typeof partner_id === 'undefined'){
		partner_id = $('.table_nalog_selected').parent().attr('partner_id');
	}
	// alert(type);
	// alert(partner_id);
	$('#db_list_partners').hide('slide', 250, function(){
		$('#db_list_nalozi').hide('slide', 250, function(){
			$('#db_progress_bar').hide('fade', 250, function(){
				$('#db_view_choice').hide('blind', 250, function(){
					getLoaderBig();
					$.ajax({
						url: 'ajax.php?action=get_list_candidates',
						type: 'POST',
						dataType: 'html',
						data:{
							'bar_id' 	: bar_id,
							'nalog_id'	: nalog_id,
							'type'		: type,
							'partner_id': partner_id	
						},
						success: function(response) {
							$('#db_list_candidates').empty().append(response).show('slide', function(){
								if(type == 1){
									$('#reject_candidate').unbind().bind('click', handleRejectCandidateClick);
									$('#hire_candidate').unbind().bind('click', handleHireCandidateClick);
									$('#assign_candidate_to_partner').unbind().bind('click', handleAssignCandidateToPartnerClick);									
								}
								else if(type == 2){
									$('#edit_candidate_partner_data').unbind('click').bind('click',handleEditCandidatePartnerData);
									$.ajax({
										url: 'benjo_test.php?action=set_modal_upload_contract',
										type: 'POST',
										dataType: 'html',
										// data:{
											// 'bar_id' 	: bar_id,
											// 'nalog_id'	: nalog_id,
											// 'type'		: type,
											// 'partner_id': partner_id	
										// },
										success: function(response) {
											$('#to_append_to_modal_upload_ugovor').empty().append(response);
										}
									});
								}
								$('#select_appointment').unbind('change');
								$('#select_partner').unbind('change');
								$('#btn_refresh_table').unbind('click');
								$('#btn_refresh_table').on('click', function(){
									getLoaderBig();
									var appointment_ids = $('#select_appointment').val();
									var partner_ids 	= $('#select_partner').val();
									$('#select_appointment').prop('disabled', true);
									$('#select_partner').prop('disabled', true);
									$('#table_list_from_projects').DataTable({
										destroy: true,
										responsive: true,
										dom: "Blfrtip",
										buttons: [ 
											'csvHtml5','excelHtml5'
										],
										"pageLength": 10,
										"processing": true,
										"serverSide": true,
										"order": [[ 0, "desc" ]],
										"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
										"ajax":{
											url :"serverside.php?page=list_from_project",
											type: "POST",
											data:{
												'bar_id' 			: bar_id,
												'partner_id' 		: partner_id,
												'nalog_id'			: nalog_id,
												'type'				: type,
												'appointment_ids'	: appointment_ids,
												'partner_ids'		: partner_ids
											},

											error: function(data){
												$(".list-grid-error").html(""); 
												$("#list-grid_processing").css("display","none");
											},
										}
									});
									removeLoader();
									$('#select_appointment').prop('disabled', false);
									$('#select_partner').prop('disabled', false);
									$('.goToDash').unbind('click').bind('click', handleBackToDashboard);
									$('.progress_bar_click').on('click',handleProgressBarClick);
								});
								$('#select_appointment, #select_partner').on('change', function(){
									getLoaderBig();
									var appointment_ids = $('#select_appointment').val();
									var partner_ids 	= $('#select_partner').val();
									console.log(1);
									$("#btn_expoprt_table").data('appointments', appointment_ids);
									$("#btn_expoprt_table").data('partners', partner_ids);
									$('#select_appointment').prop('disabled', true);
									$('#select_partner').prop('disabled', true);
									$('#table_list_from_projects').DataTable({
										destroy: true,
										responsive: true,
										dom: "Blfrtip",
										buttons: [ 
											'csvHtml5','excelHtml5'
										],
										"pageLength": 10,
										"processing": true,
										"serverSide": true,
										"order": [[ 0, "desc" ]],
										"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
										"ajax":{
											url :"serverside.php?page=list_from_project",
											type: "POST",
											data:{
												'bar_id' 			: bar_id,
												'partner_id' 		: partner_id,
												'nalog_id'			: nalog_id,
												'type'				: type,
												'appointment_ids'	: appointment_ids,
												'partner_ids'		: partner_ids
											},

											error: function(data){
												$(".list-grid-error").html(""); 
												$("#list-grid_processing").css("display","none");
											},
										}
									});
									removeLoader();
									$('#select_appointment').prop('disabled', false);
									$('#select_partner').prop('disabled', false);
									$('.goToDash').unbind('click').bind('click', handleBackToDashboard);
									$('.progress_bar_click').on('click',handleProgressBarClick);
								});
								$('#table_list_from_projects').DataTable({
									destroy: true,
									responsive: true,
									dom: "Blfrtip",
									buttons: [ 
										'csvHtml5','excelHtml5'
									],
									"pageLength": 10,
									"processing": true,
									"serverSide": true,
									"order": [[ 0, "desc" ]],
									"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
									"ajax":{
										url :"serverside.php?page=list_from_project",
										type: "POST",
										data:{
											'bar_id' 			: bar_id,
											'partner_id'		: partner_id,
											'nalog_id'			: nalog_id,
											'type'				: type,
											'appointment_ids' 	: null
										},
										error: function(data){
											$(".list-grid-error").html(""); 
											$("#list-grid_processing").css("display","none");
										},
									}
								});
								$('.goToDash').on('click', handleBackToDashboard);
								$('.progress_bar_click').on('click',handleProgressBarClick);
								
								removeLoader();

							});
						},
						error: function (xhr, ajaxOptions, thrownError) {
							alert(xhr.status);
							alert(thrownError);
						}
					});
				});
			});
		});
	});

}

function handleBackToDashboard(){
	back_to_type 	= $('.goToDash').attr('back_to_type');	
	user_type 		= $('.goToDash').attr('user_type');	
	nalog_id 		= $('.goToDash').attr('nalog_id');	
	
	if($('#view_choice_superadmin').hasClass('view_choice_superadmin_selected')){
		user_type = 1;
	}
	else if($('#view_choice_admin').hasClass('view_choice_admin_selected')){
		user_type = 2;
	}
	getLoaderBig();
	$.ajax({
		url: 'ajax.php?action=get_progress_bar',
		type: 'POST',
		dataType: 'json',
		data:{
			'nalog_ids'		: nalog_id,
			'type' 			: back_to_type,
			'partner_ids'	: 0
		},
		success: function (response_progress_bar){
			$('#applied_candidates').fadeOut(200).empty().append(response_progress_bar['applied_candidates']).fadeIn();
			for(var i = 0; i<parseInt(response_progress_bar['bar_type'].length); i++){
				$(response_progress_bar['bar_type'][i]).circleProgress({
					max: response_progress_bar['bar_max'][i],
					value: response_progress_bar['bar_value'][i],
					textFormat: function(value, max) {
						return response_progress_bar['bar_text'][i] + ' ' + response_progress_bar['bar_value'][i];
					}
				});
			}
		},
		error: function (xhr, ajaxOptions, thrownError) {
			alert(xhr.status);
			alert(thrownError);
		}
	});
	$('#db_list_candidates').hide('slide', function(){
		$('#db_view_choice').show('slide', 250, function(){
			$('#db_progress_bar').show('blind',250, function(){
				$('#db_list_nalozi').show('fade', 250, function(){
					removeLoader();
					if(back_to_type == 1 || user_type == 1){
						$('#db_list_partners').show('slide',250);
						$('#candidates_list_table_container').empty();
					}
				});
			});
		});
	});
}
function removeHighlightedPartners(){
	$(".row_handle_partner_click>td.table_text").removeClass("table_partner_selected");
	$(".row_handle_partner_click>td.table_partner_icon_column").removeClass("table_partner_icon_selected");				
}
function handleNalogClick(){
	var nalog_id		= $(this).attr('nalog_id');
	var partner_id 		= 3
	var type;
	var bar_id 			= 0;
	var continue_flag 	= true;
	
	if($('#db_list_partners').is(':visible')){
		type = 1
	}
	else{
		type = 2
	}
	var nalog_ids = [];
	$('.table_nalog_selected').each(function(){
		nalog_ids.push($(this).parent().attr('nalog_id'));					
	});
	var partner_ids = [];
	$('.table_nalog_selected').each(function(){
		partner_ids.push($(this).parent().attr('partner_id'));					
	});
	$('.row_handle_partner_click').unbind().bind('click', handlePartnerClick);
	if(!$(this).children('.table_text').hasClass('table_nalog_selected')){
		// removeHighlightedPartners();
		$(this).children('.table_text').addClass('table_nalog_selected');
		$(this).children('.table_nalog_icon_column').addClass('table_nalog_icon_selected');
		nalog_ids.push($(this).attr('nalog_id'));
		partner_ids.push($(this).attr('partner_id'));
	}
	else if(nalog_ids.length != 1) {
		$(this).children('.table_text').removeClass('table_nalog_selected');
		$(this).children('.table_nalog_icon_column').removeClass('table_nalog_icon_selected');
		nalog_ids.splice(nalog_ids.indexOf($(this).attr('nalog_id')),1);
		partner_ids.splice(partner_ids.indexOf($(this).attr('partner_id')),1);
	}
	else{
		if($('#box_partners').hasClass('partners_list_selected')){
			continue_flag = true;
			$('#box_nalozi').addClass('nalozi_list_selected');
			$('#box_partners').removeClass('partners_list_selected');
		}
		else{
			continue_flag = false;
		}
	}
	if(continue_flag){
		// alert('nastavio');
		getLoaderBig();

		if(nalog_ids.length != 1){
			$('.progress_bar_click').each(function(){
				$(this).unbind();
			});
		}
		if(partner_ids.length == 0){
			partner_ids = 0;
		}
		
		var progress_bar_type = 0;
		$('#db_progress_bar').children().children().each(function(){
			if($(this).attr('type') == 1 || $(this).attr('type') == 2){
				progress_bar_type = $(this).attr('type');
			}
		});
		// nalog_ids = ['123', '222'];
		var get_partner_list = 
			$.ajax({
				url: 'ajax.php?action=get_list_partners',
				type: 'POST',
				dataType: 'html',
				data:{
					'nalog_ids' 	: nalog_ids,
					'partner_ids' 	: partner_ids
				},
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
					'nalog_ids'		: nalog_ids,
					'type' 			: 1,
					'partner_ids'	: partner_ids
				},
				error: function (xhr, ajaxOptions, thrownError) {
					alert(xhr.status);
					alert(thrownError);
				}
			});
			
		$('#box_nalozi').addClass('nalozi_list_selected');
		$('#box_partners').removeClass('partners_list_selected');
		$.when(get_partner_list, get_progress_bar).then(
		function(response_partner_list, response_progress_bar){
			$('#db_list_partners').empty().append(response_partner_list[0]);
			$('.row_handle_partner_click').unbind().bind('click', handlePartnerClick);
			var progress_bar_text;
			if(progress_bar_type == type){
				// alert(response_progress_bar[0]['returns_status_bar']);
				$('#applied_candidates').fadeOut(200).empty().append(response_progress_bar[0]['applied_candidates']).fadeIn();
				for(var i = 0; i<parseInt(response_progress_bar[0]['bar_type'].length); i++){
					if(response_progress_bar[0]['returns_status_bar']){
						progress_bar_text = response_progress_bar[0]['bar_text'][i];
					}
					else{
						progress_bar_text = response_progress_bar[0]['bar_text'][i] + ' ' + response_progress_bar[0]['bar_value'][i];
					}
					$(response_progress_bar[0]['bar_type'][i]).circleProgress({
						max: response_progress_bar[0]['bar_max'][i],
						value: response_progress_bar[0]['bar_value'][i],
						textFormat: function(value, max) {
							return progress_bar_text;
						}
					});
				}
				if(nalog_ids.length == 1){
					$('.progress_bar_click').each(function(){
						$(this).unbind().bind('click', handleProgressBarClick);
					});
				}
			}
			else{
				$('#db_progress_bar').hide('fade', 350, function(){
					$('#db_progress_bar').empty().append(response_progress_bar[0]['code_to_append']).show('fade', 500);
					$('#applied_candidates').empty().append(response_progress_bar[0]['applied_candidates']);
					for(var i = 0; i<parseInt(response_progress_bar[0]['bar_type'].length); i++){
						$(response_progress_bar[0]['bar_type'][i]).circleProgress({
							animationDuration : 0,
							max: parseInt(response_progress_bar[0]['bar_max'][i]),
							value: parseInt(response_progress_bar[0]['bar_value'][i]),
							textFormat: function(value, max) {
								return response_progress_bar[0]['bar_text'][i] + ' ' + response_progress_bar[0]['bar_value'][i];
							}
						});
						
					$(response_progress_bar[0]['bar_type'][i]).children().children('.circle-progress-value').addClass(response_progress_bar[0]['bar_style'][i]);
					}
					if(nalog_ids.length == 1){
						$('.progress_bar_click').each(function(){
							$(this).unbind().bind('click', handleProgressBarClick);
						});
					}
				});
			}
			removeLoader();
		});
	}
}

function handlePartnerClick(){
	var partner_ids;
	var nalog_ids;
	var type 			= 2;
	var bar_id 			= 0;
	var continue_flag 	= true;
	var my_company_flag = false;
	
	var nalog_ids = [];
	$('.table_partner_selected').each(function(){
		nalog_ids.push($(this).parent().attr('nalog_id'));					
	});
	var partner_ids = [];
	$('.table_partner_selected').each(function(){
		partner_ids.push($(this).parent().attr('partner_id'));
	});
	
	var progress_bar_type = 0;
	$('#db_progress_bar').children().children().each(function(){
		if($(this).attr('type') == 1 || $(this).attr('type') == 2){
			progress_bar_type = $(this).attr('type');
		}
	});
	
	
	if(!$(this).children('.table_text').hasClass('table_partner_selected')){
		// alert('if 1');
		$(this).children('.table_text').addClass('table_partner_selected');
		$(this).children('.table_partner_icon_column ').addClass('table_partner_icon_selected');
		partner_ids.push($(this).attr('partner_id'));
		nalog_ids.push($(this).attr('nalog_id'));

	}
	else if(partner_ids.length != 1) {
		// alert('if 2');
		$(this).children('.table_text').removeClass('table_partner_selected');
		$(this).children('.table_partner_icon_column ').removeClass('table_partner_icon_selected');
		partner_ids.splice(partner_ids.indexOf($(this).attr('partner_id')),1);
		nalog_ids.splice(partner_ids.indexOf($(this).attr('nalog_id')),1);
	}
	else{
		// alert('if 3');
		if($('#box_nalozi').hasClass('nalozi_list_selected')){
			continue_flag = true;
			$('#box_partners').addClass('partners_list_selected');
			$('#box_nalozi').removeClass('nalozi_list_selected');
		}
		else if($('#box_partneri').hasClass('partners_list_selected')){
			continue_flag = true;
			$('#box_partners').removeClass('partners_list_selected');
			$('#box_nalozi').addClass('nalozi_list_selected');
		}
		else{
			continue_flag = false;
		}
	}
	$('.table_partner_selected').each(function(){
		if($(this).hasClass('table_partner_my_company')){
			my_company_flag = true;
		}
	});
	if(continue_flag){
		// alert('nastavio');
		getLoaderBig();
		$('#box_partners').addClass('partners_list_selected');
		$('#box_nalozi').removeClass('nalozi_list_selected');
		
		$.ajax({
			url: 'ajax.php?action=get_progress_bar',
			type: 'POST',
			dataType: 'json',
			data:{
				'type' 			: 2,
				'partner_ids'	: partner_ids,
				'nalog_ids'		: nalog_ids
			},
			success : function (response){
				if(progress_bar_type == type){
					for(var i = 0; i<parseInt(response['bar_type'].length); i++){
						$(response['bar_type'][i]).circleProgress({
							max: response['bar_max'][i],
							value: response['bar_value'][i],
							textFormat: function(value, max) {
								return response['bar_text'][i];
							}
						});
					}
					if(partner_ids.length != 1){
						$('.progress_bar_click').each(function(){
							$(this).unbind('click');
						});
					}
					else {
						if(my_company_flag){
							$('.progress_bar_click').each(function(){
								$(this).unbind('click').bind('click', handleProgressBarClick);
							});				
						}
					}
				}
				else{
					$('#db_progress_bar').hide('fade', 350, function(){
						$('#db_progress_bar').empty().append(response['code_to_append']);
						for(var i = 0; i<parseInt(response['bar_type'].length); i++){
							$(response['bar_type'][i]).circleProgress({
								animationDuration : 0,
								max: parseInt(response['bar_max'][i]),
								value: parseInt(response['bar_value'][i]),
								textFormat: function(value, max) {
									return response['bar_text'][i];
								}
							});
							$(response['bar_type'][i]).children().children('.circle-progress-value').addClass(response['bar_style'][i]);
							$(response['bar_type'][i]).children().children('text').attr('y',35);

						}
						$('#db_progress_bar').show('fade', 500, function(){
							
							if(partner_ids.length != 1){
								$('.progress_bar_click').each(function(){
									$(this).unbind('click');
								});
							}
							else {
								if(my_company_flag){
									$('.progress_bar_click').each(function(){
										$(this).unbind('click').bind('click', handleProgressBarClick);
									});				
								}
							}
						});
					});
				}	
				removeLoader();
			},

			error: function (xhr, ajaxOptions, thrownError) {
				alert(xhr.status);
				alert(thrownError);
			}
		});
	}
}

function handleRejectCandidateModalOpen(element){
	var nalog_id 		= $(element).attr('nalog_id');
	var candidate_id 	= $(element).attr('candidate_id');
	$("#modalOdbij").modal('show');
	$("#reject_candidate").attr('candidate_id', candidate_id);
	$("#reject_candidate").attr('nalog_id', nalog_id);
}
function handleHireCandidateModalOpen(element){
	var nalog_id 		= $(element).attr('nalog_id');
	var candidate_id 	= $(element).attr('candidate_id');
	$("#modalAccept").modal('show');
	$("#hire_candidate").attr('candidate_id', candidate_id);
	$("#hire_candidate").attr('nalog_id', nalog_id);
}
function handleAsignCandidateModalOpen(element){
	var nalog_id 		= $(element).attr('nalog_id');
	var candidate_id 	= $(element).attr('candidate_id');
	$("#modalPartner").modal('show');
	$('#assign_candidate_to_partner').attr('candidate_id', candidate_id)
	$('#assign_candidate_to_partner').attr('nalog_id', nalog_id)
}

function handleRejectCandidateClick(){
	var nalog_id = $(this).attr('nalog_id');
	var candidate_id = $(this).attr('candidate_id');
	var razlog_odbijanja = $('#razlog_odbijanja').val();
	if(razlog_odbijanja == ""){
		
		$('#razlog_odbijanja').effect('highlight');
		$('#razlog_odbijanja').effect('bounce');
	}else{
		
		$.ajax({
			url: 'ajax.php?action=reject_candidate',
			type: 'POST',
			dataType: 'html',
			data:{
				'kandidat_id'		: candidate_id,
				'nalog_id'			: nalog_id,
				'razlog_odbijanja'	: razlog_odbijanja
			},
			success : function (response){
				$("#modalOdbij").modal('hide');
				$('#child_reject_candidate_' + candidate_id).parent().parent().parent().parent().css('background-color','#FCE9EB');
				$('#child_reject_candidate_' + candidate_id).parent().parent().fadeOut(200, function(){
					$('#candidate_profile_link_' + candidate_id).on('click', function(e){
						e.preventDefault();
					});
				});			
			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert(xhr.status);
				alert(thrownError);
			}
		});
	}
}

function handleHireCandidateClick(){
	var nalog_id = $(this).attr('nalog_id');
	var candidate_id = $(this).attr('candidate_id');
	
	$.ajax({
		url: 'ajax.php?action=hire_candidate',
		type: 'POST',
		dataType: 'html',
		data:{
			'kandidat_id'	: candidate_id,
			'nalog_id'		: nalog_id
		},
		success : function (response){
			$("#modalAccept").modal('hide');
			$('#child_hire_candidate_' + candidate_id).parent().parent().parent().parent().css('background-color','#C9ECC0');
			$('#child_hire_candidate_' + candidate_id).parent().parent().fadeOut(200, function(){
				$('#candidate_profile_link_' + candidate_id).on('click', function(e){
					e.preventDefault();
				});
				$("#modalPartner").modal('show');
				$('#assign_candidate_to_partner').attr('candidate_id', candidate_id)
				$('#assign_candidate_to_partner').attr('nalog_id', nalog_id)
			});			
		},
		error: function (xhr, ajaxOptions, thrownError) {
			alert(xhr.status);
			alert(thrownError);
		}
	});
}

function handleAssignCandidateToPartnerClick(){
	var partnerIDVal 			= $('#partnerIDVal').val();
	var partnerLocation 		= $('#partnerLocation').val();
	var partnerLocationOstalo 	= $('#partnerLocationOstalo').val();
	var partnerPosition			= $('#partnerPosition').val();
	var partnerPositionOstalo 	= $('#partnerPositionOstalo').val();
	var partnerSalary 			= $('#partnerSalary').val();
	var candidate_id 			= $(this).attr('candidate_id');
	var nalog_id	 			= $(this).attr('nalog_id');
	
	if(partnerIDVal == ''){
		$('#partnerIDVal').parent().effect('bounce');
	}
	else{
		$.ajax({
			url: 'ajax.php?action=assign_candidate_to_partner',
			type: 'POST',
			dataType: 'html',
			data:{
				'partnerIDVal'			: partnerIDVal,
				'partnerLocation'		: partnerLocation,
				'partnerLocationOstalo'	: partnerLocationOstalo,
				'partnerPosition'		: partnerPosition,
				'partnerPositionOstalo'	: partnerPositionOstalo,
				'partnerSalary'			: partnerSalary,
				'candidate_id'			: candidate_id,
				'nalog_id'				: nalog_id
			},
			success : function (response){
				$("#modalPartner").modal('hide');
				$('#child_assign_partner' + candidate_id).parent().empty().append('<p class = "text-center"><i class="fa fa-check" style = "color: #1289C8;font-size: 20px!important;"aria-hidden="true"></i></p>');
			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert(xhr.status);
				alert(thrownError);
			}
		});		
	}
}
function handleEditCandidatePartnerDataModalOpen(element){
	var nalog_id 		= $(element).attr('nalog_id');
	var candidate_id 	= $(element).attr('candidate_id');
	var partner_id	 	= $(element).attr('partner_id');
	$("#modalEditCandidatePartnerData").modal('show');
	$("#edit_candidate_partner_data").attr('candidate_id', candidate_id);
	$("#edit_candidate_partner_data").attr('nalog_id', nalog_id);
	$("#edit_candidate_partner_data").attr('partner_id', partner_id);
}
function handleEditCandidatePartnerData(){
	var nalog_id 		= $(this).attr('nalog_id');
	var candidate_id 	= $(this).attr('candidate_id');
	
	var partnerIDVal 			= $('#partnerIDVal').val();
	var partnerLocation 		= $('#partnerLocation').val();
	var partnerLocationOstalo 	= $('#partnerLocationOstalo').val();
	var partnerPosition			= $('#partnerPosition').val();
	var partnerPositionOstalo 	= $('#partnerPositionOstalo').val();
	var partnerSalary 			= $('#partnerSalary').val();
	
	$.ajax({
		url: 'ajax.php?action=assign_candidate_to_partner',
		type: 'POST',
		dataType: 'html',
		data:{
			'partnerIDVal'			: partnerIDVal,
			'partnerLocation'		: partnerLocation,
			'partnerLocationOstalo'	: partnerLocationOstalo,
			'partnerPosition'		: partnerPosition,
			'partnerPositionOstalo'	: partnerPositionOstalo,
			'partnerSalary'			: partnerSalary,
			'candidate_id'			: candidate_id,
			'nalog_id'				: nalog_id
		},
		success : function (response){
			$("#modalEditCandidatePartnerData").modal('hide');
			$('#child_edit_candidate' + candidate_id).parent().empty().append('<p class = "text-center"><i class="fa fa-check" style = "color: #ffe569;font-size: 20px!important;"aria-hidden="true"></i></p>');
			$('#child_edit_candidate' + candidate_id).parent().parent().fadeOut(200);
		},
		error: function (xhr, ajaxOptions, thrownError) {
			alert(xhr.status);
			alert(thrownError);
		}
	});		
}

function handleUploadContractModalOpen(element){
	$("#modalUploadContract").modal('show');
	
}

