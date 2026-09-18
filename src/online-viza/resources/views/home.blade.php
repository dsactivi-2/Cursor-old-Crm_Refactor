<style>
.py-4{
	position:absolute;
	width:100%;
}

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
				<div class="col-md-8 offset-md-2 text-center mt-1 mb-5">
					<img src = "images/LogoGreen.png" style = "width: 50%; background-color: #d5e7e9; border-radius: 50%;">
				</div>
			</div>
			<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
			<div class="card">
				<div class="card-header text-center">
					<i class="fas fa-mobile-alt" style = "margin-right: 10px;">
					</i>
					Unesite vaš podatke
				</div>
				
				<div class="card-body">
					<form method="post" id="tel_form" action="/online-viza/save_phone" role="form" autocomplete="off">
					@csrf
						<div class="form-group">
							<div class="col-md-8 offset-md-2">
								<div class="">
									<input class="form-control materail-input" name="ime" id="kandidat_ime" placeholder="Ime" required>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-8 offset-md-2">
								<div class="">
									<input class="form-control materail-input" name="prezime" id="kandidat_prezime" placeholder="Prezime" required>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-8 offset-md-2">
								<div class="">
									<input class="form-control materail-input" type="tel" name="telefon" id="kandidat_tel" required>
								</div>
							</div>
						</div>
						<?php 
							if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
								$ip = $_SERVER['HTTP_CLIENT_IP'];
							} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
								$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
							} else {
								$ip = $_SERVER['REMOTE_ADDR'];
							}
							//echo $ip;
							
							$ipdat = @json_decode(file_get_contents( 
								"http://www.geoplugin.net/json.gp?ip=" . $ip)); 
							   
							$countryCode = strtolower($ipdat->geoplugin_countryCode); 
						?>
						<script>
							$( document ).ready(function($) {
								$.each($('input[type=tel]'),function(){
									var telInput = $(this);
									var telephone_country = '<?php echo $countryCode; ?>';
									if ($(this).val().startsWith("+") || $(this).val() == '') {
										$(telInput).intlTelInput({
											utilsScript:'https://intl-tel-input.com/node_modules/intl-tel-input/build/js/utils.js',
											autoPlaceholder: "aggressive",
											initialCountry: ""+telephone_country+"",
											formatOnDisplay: true,
											preferredCountries: ["ba","hr","de","it","rs"],
											separateDialCode: true
										});
									}
								});
								//SPAJANJE COUNTRY CODA I TELEFONSKOG BROJA
								$("form").submit(function(event) {
									//event.preventDefault();
									$.each($('input[type=tel]'),function(){
										var telInput = $(this);	
										var telType = telInput.data('type');	
										telInput.val(telInput.intlTelInput("getNumber"));  
									});
								});	
							});
						</script>
						<div class="form-group">
							<div class="col-md-8 offset-md-2 text-center">
								<div class="">
									<button type="submit" class="btn dugme_next" form="tel_form">NASTAVI <i style = "margin-left: 10px;" class="fas fa-arrow-right"></i></button>
								</div>
							</div>
						</div>
						
					</form>
				</div>
				<!---->
			</div>
        </div>
    </div>
	<script>
	$(document).ready(function() {
		$(".lds-spinner").css("display","none");
		$("input").focus(function() {
			$('footer').hide('slow');
		});
		$("input").blur(function(){
			$('footer').show('slow');
		});
	});
	</script>
	
</div>

@endsection
