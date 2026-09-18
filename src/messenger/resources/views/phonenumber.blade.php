
@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">

                <div class="card-body">
					<style>
						.card {
							overflow: auto;
						}
					</style>
					<div class="message_field" id="message_field_id">
                        <div class="message zoomIn pozdrav">
                            <div class="message_bot">
                                <div class="content_message">
									Kako bismo ostali u kontaktu unesite svoj novi broj telefona! <?php /*echo $_COOKIE["mobile_id"]; */ ?> <br/>
                                </div>
                            </div>
                        </div>
						<form method="POST" enctype="multipart/form-data" id="form_docs" action="updateTel">
						@csrf
							<div class="message zoomIn" style="">
								<div class="message_user">
									<div class="content_message">
										<input style="width: 205px;" class="input_izgled" type="tel" name="mobitel" id="mobitel" placeholder = "" required>
										<br>
										<br>
										<button class="tipka_potvrdi add_phone" id="add_phone" type="submit">OK</button>
									</div>
								</div>
							</div>
						</form>
						<script>
							$( document ).ready(function($) {

								var telInput = document.querySelector("#mobitel");

								iti = window.intlTelInput(telInput, {
									utilsScript: "https://crm.job-step.com/buildTelInput/js/utils.js",
									autoPlaceholder: "aggressive",
									preferredCountries: ["de","ba","hr","rs"],
									formatOnDisplay: true,
									separateDialCode: true
								});

								$("form").submit(function(event) {
									$("#mobitel").val(iti.getNumber());
								});

							});
						</script>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
@endsection
