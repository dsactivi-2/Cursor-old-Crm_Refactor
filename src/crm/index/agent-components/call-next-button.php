<?php
	
	/**************************************
	*
	** CREATED 30.08.2021 -- 
	** CALL NEXT CANDIDATE BUTTON
	*
	*
	** Last update - Ismail Suljic - Date: 
	*
	*/
?>
<style>
	.phonecaller{
		padding:1rem;
		background-color: #68c368;
		color:#fff;
		height: 23rem;
		width:100%;
		border: 1px solid #5ba75b;
		border-radius: 10px;
		display: flex;
		align-items: center;
		justify-content: center;
		box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);
	}
	
	.phonecaller:hover{
		box-shadow: 9px 15px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);
	}
	
	
</style>
<span class="phonecaller">POZOVI SLJEDEĆEG KANDIDATA</span>


<script>
	$( document ). ready(function () {
		$('.phonecaller').on('click', function() {
			$.ajax({
				url: '/ajax_data.php?page=generateNextCandidate',
				type: 'GET',
				dataType: 'json',
				success: function(data) {
					if(data == 'NULL'){ 
						alert('Nemate dostupnih kandidata'); 
					}
					else{
						window.location.href = "/nostrifikacija_diploma?page=otvori_ND_kandidata&id=" + data;
					}
				},
				error: function (xhr, ajaxOptions, thrownError) {
					alert(xhr.status);
					alert(thrownError);
				}
			});
		});
	});
</script>