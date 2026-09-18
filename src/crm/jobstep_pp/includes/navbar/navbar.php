 <div class="container" id = "main-navbar">
	<header class="d-flex flex-wrap align-items-center justify-content-center py-3">
		<div class="d-flex align-items-center col-lg-2 mb-2 mb-md-0 text-decoration-none">
			<img src="<?php echo getLogoPathR(); ?>" alt="" width="100">
		</div>
		<?php 
			$casting_view_class 	= "";
			$departure_view_class 	= "";
			$forecast_view_class 	= "";
			$user_type = getUserTypeR($userId);
			if($user_type == 1){
				$forecast_view_class 	= "view_unselected";
				$casting_view_class 	= "view_selected";
				$departure_view_class 	= "view_unselected";
			}
			else{
				$forecast_view_class 	= "view_unselected unclickable";
				$casting_view_class 	= "view_unselected unclickable";
				$departure_view_class 	= "view_selected";
			}
			if(basename($_SERVER['PHP_SELF']) != "profile.php"){
				?>
					<ul class="nav col-12 col-lg-auto mb-2 justify-content-center mb-md-0">
						<li><div id = "forecast_view" class="nav-item px-4 <?php echo $forecast_view_class; ?>">Forecast</div></li>
						<li><div id = "casting_view" class="nav-item px-4 <?php echo $casting_view_class; ?>"><?php echo $txtArray['Dashboard kandidati'][$languageUser]; ?></div></li>
						<li><div id = "departure_view" class="nav-item px-4 <?php echo $departure_view_class; ?>"><?php echo $txtArray['Dashboard personal'][$languageUser]; ?></div></li>
					</ul>
				<?php 
			}
		?>
		<?php 
			$query_get_active_reminder_types = $db -> prepare('
				SELECT prt_id
				FROM idk_pp_reminder_types
				WHERE prt_user_type IN (1,2)
			');
			$query_get_active_reminder_types -> execute();
			$active_reminders = array();
			while($row_get_active_reminders = $query_get_active_reminder_types -> fetch()){
				array_push($active_reminders, $row_get_active_reminders['prt_id']);
			}
		?>
		<ul class="nav col-lg-<?php echo ((basename($_SERVER['PHP_SELF']) != "profile.php") ? "3" : "10"); ?> justify-content-end">
			<li class="nav-item me-2 ms-2" style = "position:relative;">
				<img id = "button_task_manager" src="images/check_icon.svg" style = "width: 44px;cursor:pointer;"/>
				<div style = "font-size: 14px;text-align: center;position: absolute;top: -4px;left: 70%;background-color: #e91c24;color: white;height: 20px;width: 20px;border-radius: 50%;"><?php echo count(getRemindersArrayR(implode(',', $active_reminders))["count"]); ?></div>
			</li>
			<li class="nav-item ms-2">
				<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#userModal" style = "box-shadow: none;">
					<i class="fa fa-user-circle-o fa-2x" aria-hidden="true"></i>
				</button>
			</li>
			<li class="nav-item">
				<button type="button" class="btn" id = "search_candidates" style = "box-shadow: none;">
					<i class="fa fa-search fa-2x" aria-hidden="true"></i>
				</button>
			</li>		
		</ul>

	</header>
  </div>
  <div class="modal fade" id="modalSearchCandidate" aria-hidden="true" aria-labelledby="modalSearchCandidate" tabindex="-1" style = "background-color: rgb(255 255 255 / 50%);">
	<div class="modal-dialog modal-dialog-centered" style = "<?php if (showCandidateIdR($userId)){echo 'max-width:1000px!important';}else{echo 'max-width:700px!important';} ?>">
		<div class="modal-content border-0">
			<div class="modal-header border-bottom-0 text-center">
				<h5 class="modal-title w-100" id="modalSearchCandidate"><?php echo $txtArray["Pretraga kandidata"][$languageUser]; ?></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body" style = "text-align: center;">
				<input id = "input_candidate_search" type = "text" placeholder = "<?php echo $txtArray["Unesite ime kandidata"][$languageUser]; ?>" class = "search_candidates_input"></input>
				<div id = "candidates_to_append_to" style = "display:none; overflow: scroll;max-height:500px;"></div>
				<br><div id = "search_loader" class="lds-ellipsis" style = "display:none;"><div></div><div></div><div></div><div></div></div>
			</div>
		</div>
	</div>
</div>
<script>
var currentRequest = null;
$(document).ready(function() {
	$("#input_candidate_search").unbind('keyup').bind('keyup',function(){
		var search_text = $(this).val();
		if(search_text != ''){
			$("#candidates_to_append_to").hide('blind', 300, function(){
				$('#search_loader').show('blind',300);
				currentRequest = $.ajax({       
					url: 'ajax.php?action=get_search_candidates',
					type: 'POST',
					dataType: 'html',
					data:{'search_text': search_text},
					beforeSend : function(){   
						if(currentRequest != null){
							currentRequest.abort();
						}
					},
					success: function(data){  
						$('#search_loader').hide('blind',300,function(){
							$('#candidates_to_append_to').empty().append(data).show('blind', 300);
						});
					}
				});
			});

		}
		else{
			$("#candidates_to_append_to").hide('blind', 300, function(){
				$(this).empty();
			});
		}
	});
});
</script>
