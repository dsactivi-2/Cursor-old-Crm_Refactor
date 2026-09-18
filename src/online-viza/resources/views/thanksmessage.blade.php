<style>
body{
	background: #649aa3 !important;
}
.py-4{
	position:absolute;
	width:100%;
}
footer{
	display:none !important;
}

.dugme_next {
    width: 100% !important;
}
.card-header{
	background: rgba(0, 128, 0, 0.5) !important;
    margin: 0.25rem 0.25rem 0rem 0.25rem !important;
    border-radius: 1.25rem 1.25rem 0rem 0rem !important;
	padding:0px !important;
}
.card-header h5{
	font-size: 1.25rem;
    font-weight: 400;
    margin-top: 0.5rem !important;
    color: white;
}

/*------Animation START------*/

	.zoomOut {
		-webkit-animation-name: zoomOut;
		animation-name: zoomOut;
		-webkit-animation-duration: 1s;
		animation-duration: 1s;
		-webkit-animation-fill-mode: both;
		animation-fill-mode: both;
	}
	@-webkit-keyframes zoomOut {
		0% {
			opacity: 1;
		}
	  
		50% {
			opacity: 0;
			-webkit-transform: scale3d(.3, .3, .3);
			transform: scale3d(.3, .3, .3);
		}
		100% {
			opacity: 0;
		}
	}
	@keyframes zoomOut {
		0% {
			opacity: 1;
		}
		50% {
			opacity: 0;
			-webkit-transform: scale3d(.3, .3, .3);
			transform: scale3d(.3, .3, .3);
		}
		100% {
			opacity: 0;
		}
	}
	
	.zoomIn {
		-webkit-animation-name: zoomIn;
		animation-name: zoomIn;
		-webkit-animation-duration: 1s;
		animation-duration: 1s;
		-webkit-animation-fill-mode: both;
		animation-fill-mode: both;
	}
	@-webkit-keyframes zoomIn {
		0% {
			opacity: 0;
			-webkit-transform: scale3d(.3, .3, .3);
			transform: scale3d(.3, .3, .3);
		}
		50% {
			opacity: 1;
		}
	}
	@keyframes zoomIn {
		0% {
			opacity: 0;
			-webkit-transform: scale3d(.3, .3, .3);
			transform: scale3d(.3, .3, .3);
		}
		50% {
			opacity: 1;
		}
	}
  
/*------Animation END------*/  

.lds-spinner {
	z-index:1;
	position: absolute;
	left: 0;
	right: 0;
	margin-left: auto;
	margin-right: auto;
  color: official;
  display: inline-block;
  width: 80px;
  height: 80px;
}
.lds-spinner div {
  transform-origin: 40px 40px;
  animation: lds-spinner 1.2s linear infinite;
}
.lds-spinner div:after {
  content: " ";
  display: block;
  position: absolute;
  top: 3px;
  left: 37px;
  width: 6px;
  height: 18px;
  border-radius: 20%;
  background: #fff;
}
.lds-spinner div:nth-child(1) {
  transform: rotate(0deg);
  animation-delay: -1.1s;
}
.lds-spinner div:nth-child(2) {
  transform: rotate(30deg);
  animation-delay: -1s;
}
.lds-spinner div:nth-child(3) {
  transform: rotate(60deg);
  animation-delay: -0.9s;
}
.lds-spinner div:nth-child(4) {
  transform: rotate(90deg);
  animation-delay: -0.8s;
}
.lds-spinner div:nth-child(5) {
  transform: rotate(120deg);
  animation-delay: -0.7s;
}
.lds-spinner div:nth-child(6) {
  transform: rotate(150deg);
  animation-delay: -0.6s;
}
.lds-spinner div:nth-child(7) {
  transform: rotate(180deg);
  animation-delay: -0.5s;
}
.lds-spinner div:nth-child(8) {
  transform: rotate(210deg);
  animation-delay: -0.4s;
}
.lds-spinner div:nth-child(9) {
  transform: rotate(240deg);
  animation-delay: -0.3s;
}
.lds-spinner div:nth-child(10) {
  transform: rotate(270deg);
  animation-delay: -0.2s;
}
.lds-spinner div:nth-child(11) {
  transform: rotate(300deg);
  animation-delay: -0.1s;
}
.lds-spinner div:nth-child(12) {
  transform: rotate(330deg);
  animation-delay: 0s;
}
@keyframes lds-spinner {
  0% {
    opacity: 1;
  }
  100% {
    opacity: 0;
  }
}

</style>
@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
			<div class = "row">
				<div id = "logo_margin" class="col-md-4 offset-md-4 text-center mt-1 mb-5">
					<img src = "images/LogoGreen.png" style = "width: 50%; background-color: #d5e7e9; border-radius: 50%;">
				</div>
			</div>
			<div class="lds-spinner" style="display:none; "><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
			<div class="card">
				<div class="card-header text-center">
					<h5>Naš tim će vas kontaktirati.</h5>
				</div>
				<?php if($osiguranje == 1){?>				
					<div class="alert message_error alert-danger text-center" role="alert" style="display:none;">
					  Klikom na ikonu galerije dodajte datoteku, a zatim kliknite dugme za dodavanje. 
					</div>
					<div class="card-body">
						<!-- PASOŠ -->
						<div class="okvir_pasosa" <?php if($return_passport == 1){echo "style='display:none;'";}?>>
							<div class="form-group text-center">
								<label for="pasos">
									<span style="font-size: 18px; color: rgb(0, 0, 0);">
										Da li imate sliku pasoša? 
									</span>
								</label>
							</div>
							<div id = "image_file_pasos_dodaj1" class="form-group text-center" style="display:block;">
								<button id = "dugme_ucitaj_pasos" class="btn dugme_next">Učitaj
								<i style = "margin-left: 10px" class="fas fa-plus-circle">
								</i>
								</button>
							</div>
							<script>
								$(document).ready(function(){
									$("#dugme_ucitaj_pasos").click(function(){
										$(this).addClass("zoomOut");
										$("#image_file_pasos").addClass("zoomIn");
										$("#image_file_pasos_dodaj").addClass("zoomIn");
										setTimeout(function() {
											prikaz_dugme_upload_pasos();
										}, 500);
									});
								});
								
								function prikaz_dugme_upload_pasos() {
									var x1 = document.getElementById("image_file_pasos");
									var x2 = document.getElementById("image_file_pasos_dodaj");
									var x3 = document.getElementById("image_file_pasos_dodaj1");
									if (x1.style.display == "none") {
										x1.style.display = "block";
									} 
									else {
										x1.style.display = "none";
									}
									if (x2.style.display == "none") {
										x2.style.display = "block";
									} 
									else {
										x2.style.display = "none";
									}
									if (x3.style.display == "block") {
										x3.style.display = "none";
									} 
								}
							</script>
							<div id = "image_file_pasos" class="form-group text-center" style="display:none;">
								<form>   
									@csrf
									<div class="image-upload" style="display:inline-block; margin-right:20px;display:none;">
									  <label for="pasos_cam">
										<i class="fas fa-camera fa-3x" style="
											color: rgba(0, 0, 0, 0.7);
											/* border: 1px solid black; */
											background: #c1e3e8;
											padding: 0.25rem 1.25rem;
											border-radius: 1.25rem;
										">
											<br>
											<span style = "font-size: initial;">
												Photo
											</span>
										</i>
									  </label>
									  <input id="pasos_cam" name="pasos_cam" class="file-input pasos" accept="image/*" capture="camera" type="file" style="display:none;" />
									</div>
									 
									<div class="file-upload" style="display:inline-block;margin-left:10px;">
									  <label for="pasos_file">
										<i class="fas fa-images fa-3x" style="
											color: rgba(0, 0, 0, 0.7);
											/* border: 1px solid black; */
											background: #c1e3e8;
											padding: 0.25rem 1.25rem;
											border-radius: 1.25rem;
										">
											<br>
											<span style = "font-size: initial;">
												Galery
											</span>
										</i>					
									  </label>
									  <input id="pasos_file" name="pasos_file" class="file-input pasos" type="file"  style="display:none;" /> 
									</div>
								</form>
							</div>
						</div>
						
						<script>
							$(document).ready(function() {
								//SAVE MESSAGE AND DISPLAY IT 
								$('#dugme_pasos').on("click", function() {
									if(document.getElementById('pasos_cam').files.length == 0){
										var file_data = $('#pasos_file').prop('files')[0];
									}else if(document.getElementById('pasos_file').files.length == 0){
										var file_data = $('#pasos_cam').prop('files')[0];
									}
										 
									var form_data = new FormData();                  
									form_data.append('file', file_data); 
									if(document.getElementById('pasos_file').files.length == 0){
										$(".message_error").css("display", "block");
									}else{
										$(".lds-spinner").css("display","inline-block");
										$(".message_error").css("display", "none");
										$.ajax({
										url: 'https://jobstep-app.com/online-viza/upload.php', // point to server-side PHP script 
										dataType: 'text',  // what to expect back from the PHP script, if anything
										cache: false,
										contentType: false,
										processData: false,
										data: form_data,                         
										type: 'post',
										success: function(php_script_response){
											$(".lds-spinner").css("display","none");
											console.log(php_script_response); // display response from the PHP script, if any
											window.location.href = "<?php echo URL::to('save_passport/"+php_script_response+"'); ?>";
										}
									 });
									}
								});	
							});
						</script>	
						<div id = "image_file_pasos_dodaj" class="form-group text-center" style="display:none;">
							<button type="submit" id="dugme_pasos" class="btn dugme_next" >Dodajte pasoš<i style = "margin-left: 10px" class="fas fa-save"></i></button>
						</div>
						<div class="alert alert-info alert_pasosa text-center" role="alert" <?php if($return_passport == 0){echo "style='display:none;'";}?>>
							<i class="fas fa-check-double" style="
								color: #58a568;
								font-size:24px;
								float:left;
							"></i>
							Dodali ste pasoš. 
							<a id="reopen_pasos">
								<i class="fas fa-trash" style="
									color: #e3342f;
									font-size:24px;
									float:right;
								"></i>
							</a>
						</div>
						<script>
							$(document).ready(function() {
								var upload_file;
								// AKO UNOSI FILE OČISTI CAM I KUPI ZA UPLOAD
								$( "#pasos_file" ).on( "change", function() {
									$("#pasos_cam").val('');
									upload_file = $("#pasos_file").val();
								});
								// AKO UNOSI CAM OČISTI FILE I KUPI ZA UPLOAD
								$( "#pasos_cam" ).on( "change", function() {
									$("#pasos_file").val('');
									upload_file = $("#pasos_cam").val();
								})
								
								$("#reopen_pasos").on( "click", function() {
									$(".okvir_pasosa").css("display","block");
									$(".alert_pasosa").css("display","none");
								});
							});
						</script>	
						<!-- UGOVOR -->
						
						<div class="okvir_ugovora" <?php if($return_contract == 1){echo "style='display:none;'";}?>>
							<div class="form-group text-center">
								<label for="ugovor">
									<span style="font-size: 18px; color: rgb(0, 0, 0);">
										Da li imate sliku ugovora?
									</span>
								</label>
							</div>
							
							<div id = "image_file_contract_dodaj1" class="form-group text-center" style="display:block;">
								<button id = "dugme_ucitaj_ugovor" class="btn dugme_next">Učitaj
								<i style = "margin-left: 10px" class="fas fa-plus-circle">
								</i>
								</button>
							</div>
							<script>
								$(document).ready(function(){
									$("#dugme_ucitaj_ugovor").click(function(){
										$(this).addClass("zoomOut");
										$("#image_file_contract").addClass("zoomIn");
										$("#image_file_contract_dodaj").addClass("zoomIn");
										setTimeout(function() {
											prikaz_dugme_upload_contract();
										}, 500);
									});
								});
								function prikaz_dugme_upload_contract() {
									var x1 = document.getElementById("image_file_contract");
									var x2 = document.getElementById("image_file_contract_dodaj");
									var x3 = document.getElementById("image_file_contract_dodaj1");
									if (x1.style.display == "none") {
										x1.style.display = "block";
									} 
									else {
										x1.style.display = "none";
									}
									if (x2.style.display == "none") {
										x2.style.display = "block";
									} 
									else {
										x2.style.display = "none";
									}
									if (x3.style.display == "block") {
										x3.style.display = "none";
									} 
								}
							</script>
							
							<div id = "image_file_contract" class="form-group text-center" style="display:none;">
								<form id="form5"> 
									@csrf
									<div class="image-upload" style="display:inline-block; margin-right:20px;display:none;">  
										<label for="ugovor_cam">
											<i class="fas fa-camera fa-3x" style="
												color: rgba(0, 0, 0, 0.7);
												/* border: 1px solid black; */
												background: #c1e3e8;
												padding: 0.25rem 1.25rem;
												border-radius: 1.25rem;
											">
												<br>
												<span style = "font-size: initial;">
													Photo
												</span>
											</i>					
										</label>
									  <input id="ugovor_cam" name="contract_cam" class="file-input ugovor" accept="image/*" capture="camera" type="file" style="display:none;" />
									</div>
									  
									<div class="file-upload" style="display:inline-block;margin-left:10px;">
										<label for="ugovor_file">
											<i class="fas fa-images fa-3x" style="
												color: rgba(0, 0, 0, 0.7);
												/* border: 1px solid black; */
												background: #c1e3e8;
												padding: 0.25rem 1.25rem;
												border-radius: 1.25rem;
											">
												<br>
												<span style = "font-size: initial;">
													Galery
												</span>
												<div class="col-12 img_holder" style="display:none;"><img src="" alt="" id="image" class="img"/></div>
											</i>			
										</label> 
									  <input id="ugovor_file" name="contract_file" class="file-input ugovor" type="file" value="" style="display:none;" />
									</div>
								</form>
								<script>
									$(document).ready(function() {
										//SAVE MESSAGE AND DISPLAY IT
										$('#dugme_ugovor').on("click", function() {
											if(document.getElementById('ugovor_cam').files.length == 0){
												var file_data = $('#ugovor_file').prop('files')[0];
											}else if(document.getElementById('ugovor_file').files.length == 0){
												var file_data = $('#ugovor_cam').prop('files')[0];
											}
											var form_data = new FormData();                  
											form_data.append('file', file_data);
											
											if(document.getElementById('ugovor_file').files.length == 0){
												$(".message_error").css("display", "block");
											}else{
												$(".lds-spinner").css("display","inline-block");
												$(".message_error").css("display", "none");
												$.ajax({
													url: 'https://jobstep-app.com/online-viza/upload.php', // point to server-side PHP script 
													dataType: 'text',  // what to expect back from the PHP script, if anything
													cache: false,
													contentType: false,
													processData: false,
													data: form_data,                         
													type: 'post',
													success: function(php_script_response){
														$(".lds-spinner").css("display","none");
														window.location.href = "<?php echo URL::to('save_contract/"+php_script_response+"'); ?>";
														
													}
												 });
											}
										});	
									});
								</script>	
							</div>
							<div id = "image_file_contract_dodaj" class="form-group text-center" style="display:none;">
								<button type="submit" id="dugme_ugovor" class="dugme_next btn" >Dodajte ugovor<i style = "margin-left: 10px" class="fas fa-save"></i></button>
							</div> 
						</div>
						<div class="alert alert-info alert_ugovor text-center" role="alert" <?php if($return_contract == 0){echo "style='display:none;'";}?> >
							<i class="fas fa-check-double" style="
								color: #58a568;
								font-size:24px;
								float:left;
							"></i>
							Dodali ste ugovor.
							<a id="reopen_ugovor">
								<i class="fas fa-trash" style="
									color: #e3342f;
									font-size:24px;
									float:right;
								"></i>
							</a>
						</div>
					</div>
					<?php }else{?>
					<div class="card-body">
						<div class="form-group text-center">
							<label for="opis">
								<span style="font-size: 18px; color: rgb(0, 0, 0);">
									Hvala na povjerenju! Naš tim će Vas kontaktirati!
								</span>
							</label>
						</div>
					</div>
					<?php }?>
					<script>
						$(document).ready(function() {
							var upload_file;
							// AKO UNOSI FILE OČISTI CAM I KUPI ZA UPLOAD
							$( "#file-input" ).on( "change", function() {
								$("#ugovor_cam").val('');
								upload_file = $("#file-input").val();
							});
							// AKO UNOSI CAM OČISTI FILE I KUPI ZA UPLOAD
							$( "#ugovor_cam" ).on( "change", function() {
								$("#file-input").val('');
								upload_file = $("#ugovor_cam").val();
							});
							
							$("#reopen_ugovor").on( "click", function() {
								$(".okvir_ugovora").css("display","block");
								$(".alert_ugovor").css("display","none");
							});
						});
					</script>
				</div>
			</div>
        </div>
    </div>       
</div> 

@endsection
