<?php
include("includes/functions.php");
include("includes/head.php");
?>
<html>
	<body>
	<?php
		// $query_string_brojevi_za_slanje = '
			// SELECT kandidat_id as id, kandidat_mobitel as mobitel, kandidat_ime as ime, kandidat_prezime as prezime
			// FROM idk_kandidati
			// WHERE kandidat_id = 25059
		// ';
		$button_text = "Prijavi se!";
		$image_url 	= 'https://crm.job-step.com/images/2022_05_16_dipl.jpg';
		$link_url 	= 'https://job-step.net/usluga/priznavanje-diplome/3/162/bs';
		
		// BIH
		// $text_viber = 'ŽELIŠ RADITI U NJEMAČKOJ U SVOJOJ STRUCI? ŽELIŠ TERMIN ZA VIZU BEZ LUTRIJE?\n\nPOKRENI NOSTRIFIKACIJU DIPLOME I OSTVARI POPUST OD 20%!\n\nPročitaj i prijavi se!';
		// $text_sms 	= 'ZELIS RADITI U NJEMACKOJ U SVOJOJ STRUCI? ZELIS TERMIN ZA VIZU BEZ LUTRIJE?\n\nPOKRENI NOSTRIFIKACIJU DIPLOME I OSTVARI POPUST OD 20%!\n\nProcitaj i prijavi se!\n\n'.$link_url;
		// SRB
		// $text_viber = 'ŽELIŠ RADITI U NEMAČKOJ U SVOJOJ STRUCI? ŽELIŠ TERMIN ZA VIZU BEZ LUTRIJE?\n\nPOKRENI NOSTRIFIKACIJU DIPLOME I OSTVARI POPUST OD 20%!\n\nPročitaj i prijavi se!';
		// $text_sms 	= 'ZELIS RADITI U NEMACKOJ U SVOJOJ STRUCI? ZELIS TERMIN ZA VIZU BEZ LUTRIJE?\n\nPOKRENI NOSTRIFIKACIJU DIPLOME I OSTVARI POPUST OD 20%!\n\nProcitaj i prijavi se!\n\n'.$link_url;
	?>
		<div id="control_box" style="padding-left:50px; padding-right:25px; padding-top:10px;">
		
			<input id="string_query" 	name="string_query" type="hidden" value="<?php echo $query_string_brojevi_za_slanje; ?>"> 
			<input id="image_url" 		name="image_url" 	type="hidden" value="<?php echo $image_url; ?>"> 
			<input id="link_url" 		name="link_url" 	type="hidden" value="<?php echo $link_url; ?>"> 
			<input id="text_viber"		name="text_viber" 	type="hidden" value="<?php echo $text_viber; ?>"> 
			<input id="text_sms" 		name="text_sms" 	type="hidden" value="<?php echo $text_sms; ?>"> 
			<input id="button_text" 	name="button_text" 	type="hidden" value="<?php echo $button_text; ?>"> 
			<?php 
			// echo '<b>Query za slanje: </b>'.$query_string_brojevi_za_slanje; 
			?>
			<br><br>
			<br>
		
			<p id="text_ide_li_bulk"> Idu li bulk poruke? <br><p>
			<button id="ide_bulk" name="ide_bulk"><b>DA</b></button>
			<button id="ne_ide_bulk" name="ne_ide_bulk"><b>NE</b></button>
			
			<p id="text_ide_bulk" style="visibility:hidden;">Pošto su bulk poruke u pitanju, jedini query koji treba da se mijenja je onaj u fajlu ____________. Obrati pažnju na to da id, broj mobitela, ime i prezime imaju odgovarajuće alias (id, mobitel, ime, prezime)<br></p>
			<button style="visibility:hidden;" id="button_ispis_bulk" name="button_ispis">Ispis kandidata za slanje poruka u <b>BULKU</b></button>
			<button style="visibility:hidden;" id="button_posalji_bulk" name="button_posalji_bulk" data-toggle="modal" data-target="#modal_slanje_check_bulk" >Pošalji <b>BULK</b> poruke kandidatima</button>
			
			<p id="text_ne_ide_bulk" style="visibility:hidden;">Pošto bulk poruke nisu u pitanju, potrebno je obratiti pažnju na CASE "posalji_bez_bulk" u fajlu _______. Query unutar ovog fajla je povučen za getanje brojeva, ali tekstovi i link nisu zbog personalne poruke. Linkovi i tekstovi se generišu unutar case "posalji_bez_bulk": u fajlu _______. Obratiti pažnju i na to da  id, broj mobitela, ime i prezime querya za ispis imaju odgovarajuće alias (id, mobitel, ime, prezime).<br></p>
			<button style="visibility:hidden;" id="button_ispis_bez_bulk" name="button_ispis">Ispis kandidata za slanje poruka</button>
			<button style="visibility:hidden;" id="button_posalji_bez_bulk" name="button_posalji_bez_bulk" data-toggle="modal" data-target="#modal_slanje_check">Pošalji poruke kandidatima</button>

			<h1 id="text_slanje_u_toku" style="visibility:hidden;">SLANJE U TOKU</h1>

		<script>
			$(document).on("click","#ide_bulk",function() {
				$('#text_ide_li_bulk').css('visibility','hidden');
				$('#ide_bulk').css('visibility','hidden');
				$('#ne_ide_bulk').css('visibility','hidden');
				
				$('#text_ide_bulk').css('visibility','visible');
				$('#button_ispis_bulk').css('visibility','visible');
				$('#button_posalji_bulk').css('visibility','visible');
			});
			
			$(document).on("click","#ne_ide_bulk",function() {
				$('#text_ide_li_bulk').css('visibility','hidden');
				$('#ide_bulk').css('visibility','hidden');
				$('#ne_ide_bulk').css('visibility','hidden');
				
				$('#text_ne_ide_bulk').css('visibility','visible');
				$('#button_ispis_bez_bulk').css('visibility','visible');
				$('#button_posalji_bez_bulk').css('visibility','visible');
			});
			
			$(document).on("click","#button_ispis_bulk, #button_ispis_bez_bulk",function() {
				var query = $('#string_query').val();
						
				$.ajax({
					url: 'marketing_poruke_ajax.php?action=ispis',
					type: 'POST',
					data: {	
						'query':query
					},
					dataType: 'html',
					success: function(html) {
						$("#to_append_to").empty();
						$("#to_append_to").append(html);
					},
					error: function (xhr, ajaxOptions, thrownError) {
						alert(xhr.status);
						alert(thrownError);
					}
				});
			});
			
			
			
			$(document).on("click","#button_definitivno_posalji_bulk",function() {
				var query = $('#string_query').val();
				var image_url = $('#image_url').val();
				var link_url = $('#link_url').val();
				var text_viber = $('#text_viber').val();
				var text_sms = $('#text_sms').val();
				var button_text = $('#button_text').val();
				
				$('#text_ide_bulk').css('visibility','hidden');
				$('#button_ispis_bulk').css('visibility','hidden');
				$('#button_posalji_bulk').css('visibility','hidden');
				$('#text_slanje_u_toku').css('visibility','visible');
				$('#modal_slanje_check_bulk').modal('toggle');
				
				$.ajax({
					url: 'marketing_poruke_ajax.php?action=posalji_bulk',
					type: 'POST',
					data: {	
						'query':query,											
						'image_url':image_url,											
						'link_url':link_url,											
						'text_viber':text_viber,											
						'text_sms':text_sms,											
						'button_text':button_text											
					},
					dataType: 'html',
					success: function(html) {
						$("#to_append_to").empty();
						$("#to_append_to").append(html);
					},
					error: function (xhr, ajaxOptions, thrownError) {
						alert(xhr.status);
						alert(thrownError);
					}
				});
			});
			
			$(document).on("click","#button_definitivno_ne_posalji_bulk",function() {
				$('#modal_slanje_check_bulk').modal('toggle');

			});


			$(document).on("click","#button_definitivno_posalji",function() {
				var query = $('#string_query').val();
				var image_url = $('#image_url').val();
				var button_text = $('#button_text').val();
				
				$('#text_ne_ide_bulk').css('visibility','hidden');
				$('#button_ispis_bez_bulk').css('visibility','hidden');
				$('#button_posalji_bez_bulk').css('visibility','hidden');
				$('#text_slanje_u_toku').css('visibility','visible');
				$('#modal_slanje_check').modal('toggle');
				
				$.ajax({
					url: 'marketing_poruke_ajax.php?action=posalji_bez_bulk',
					type: 'POST',
					data: {	
						'query':query,											
						'image_url':image_url,											
						'button_text':button_text											
					},
					dataType: 'html',
					success: function(html) {
						$("#to_append_to").empty();
						$("#to_append_to").append(html);
					},
					error: function (xhr, ajaxOptions, thrownError) {
						alert(xhr.status);
						alert(thrownError);
					}
				});
			});
			
			$(document).on("click","#button_definitivno_ne_posalji",function() {
				$('#modal_slanje_check').modal('toggle');

			});
			
		</script>
			<div id="to_append_to">
			</div>
		</div>
		
		<div class="modal fade" id="modal_slanje_check_bulk">
			<div class="modal-dialog">
				<div class="modal-content material-modal__content">
					<div class="modal-header">
						<h4>JESI LI SIGURAN DA ŽELIŠ POSLATI PORUKE?<h4>
					</div>
					<div id="modal_body">
						<div class="text-center">
							
							<button id = "button_definitivno_posalji_bulk"><b>DA</b></button>
							<button id = "button_definitivno_ne_posalji_bulk"><b>NE</b></button>
							
						</div>
					</div>
				</div>
			</div>
		</div>
				
		<div class="modal fade" id="modal_slanje_check">
			<div class="modal-dialog">
				<div class="modal-content material-modal__content">
					<div class="modal-header">
						<h4>JESI LI SIGURAN DA ŽELIŠ POSLATI PORUKE?<h4>
					</div>
					<div id="modal_body">
						<div class="text-center">
							
							<button id = "button_definitivno_posalji"><b>DA</b></button>
							<button id = "button_definitivno_ne_posalji"><b>NE</b></button>
							
						</div>
					</div>
				</div>
			</div>
		</div>
		
		
	</body>
</html>