<style>
.py-4{
	/* position: absolute; */
    /* width: 100%; */
    /* margin-top: 50%;*/
}

.pin{
	width: 50px !important;
	height: 50px !important;
	border-radius: 50% !important;
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
				<div class="col-md-4 offset-md-4 text-center mt-1 mb-5">
					<img src = "images/LogoGreen.png" style = "width: 50%; background-color: #d5e7e9; border-radius: 50%;">
				</div>
			</div>
			<div class="lds-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
            <div class="card">
                <div class="card-header text-center"><i style = "margin-right: 10px;" class="fas fa-key"></i> Unesite dobijeni KOD</div>
                <div class="card-body">
				<form method="post" id="tel_form" action="/online-viza/form" role="form">
					@csrf
					<input type="hidden" name="hashed_codetel" value="<?php echo $hashed_codetel; ?>">
					<div class="form-group">
						<div class="col-sm-8 offset-md-2 text-center">
							<input style = "text-align: center; letter-spacing: 5px;" class="form-control materail-input" type="number" name="phone_confirm" id="phone_confirm" placeholder = "● ● ● ● ● ●" required>
						</div>
					</div>
					<!-- style="display:none;"-->
					<div class="form-group">
						<div class="col-md-8 offset-md-2 text-center">
							<div class="">
								<button type="submit" style="display:none;" form="tel_form" class="btn dugme_next nastavak" >NASTAVI <i class="fas fa-arrow-right"></i></button>
							</div>
						</div>
					</div>
				</form>
				<form method="post" id="resend_pin" action="/online-viza/resend_pin" role="form">
					@csrf
					<input type="hidden" name="hashed_codetel" value="<?php echo $hashed_codetel; ?>">
					<div class="form-group">
						<div class="col-md-8 offset-md-2 text-center">
							<div class="pin_frame">
								Niste dobili kod ?  <button type="submit" form="resend_pin" class="btn dugme_next pin" ><i class="fas fa-arrow-right"></i></button>
							</div>
						</div>
					</div>
				</form>
                </div>
            </div>
        </div>
    </div>
	<script>
	$(document).ready(function() {
		$(".lds-spinner").css("display","none");
		$("input#phone_confirm").on( "keyup", function() {
			var confirm_code = $("#phone_confirm").val();
			var code_decrypt = {!! json_encode($code, JSON_HEX_TAG) !!};
			// alert(confirm_code);
			// alert(code_decrypt);
			if(confirm_code == code_decrypt){
				$('button.nastavak').css('display','inline-block');
				$('.pin_frame').css('display','none');
				$("button.nastavak").addClass("zoomIn");
			}else{
				$('button.nastavak').css('display','none');
				$('.pin_frame').css('display','inline-block');
				$(".pin_frame").addClass("zoomIn");
			}
		});
		
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