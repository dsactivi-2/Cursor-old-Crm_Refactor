@extends('app.layouts.master')

@section('title', 'Uredi Podešavanja')

@section('content')
	
	<div class="container">
		<div class="app-header">
			<div class="row d-flex align-items-center">
				<div class="col-md-6 col-sm-12 app-header__title">
					<h3>Uredi Podešavanja</h3>
				</div>	

				<div class="col-md-6 col-sm-12 app-header__add-btn">
					<a class="btn custom-button" href="{{ route('settings.index') }}">
						<i class="fas fa-chevron-left"></i>
					</a>
				</div>
			</div>
		</div>
			
		<div class="app-content form-content">
			<form method="POST" action="{{ route('settings.update') }}" enctype='multipart/form-data'>
				
				@method('PUT')
				@csrf
				
				<div class="form-group row">
				    <label for="email" class="col-md-3 col-form-label">Email</label>
				    <div class="col-md-6">
				        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Email" value="{{ $settings->email }}">
				        @error('email')
				            <span class="invalid-feedback" role="alert">
				                <strong>{{ $message }}</strong>
				            </span>
				        @enderror
				    </div>
				</div>

				<div class="form-group row">
                	<label class="col-md-3 col-form-label align-items-start">Trenutna slika</label>
                	<div class="col-md-8">
                		@if (is_null($settings->image))
                			-
                		@else
                		<div class="resource-image-container">
                			<img src="{{ asset('storage/settings/' . $settings->image . '') }}" class="img-fluid">
                		</div>
                		@endif
                	</div>
                </div>

				<div class="form-group row">
                	<label class="col-md-3 col-form-label">Slika</label>
                	<div class="col-md-8">
                		<div class="row">
                			<div class="col-md-12 mt-3">
                				<label for="image" class="btn btn-primary text-white">
                        		 	Odaberi sliku
                                    <input type='file' id="image" class="d-none" name="image" />
                                </label>
                                <span class="ml-4 file-name-display-field">Slika nije odabrana</span>
                                 @error('image')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                			</div>
                		</div>
                	</div>
                </div>

				<div class="form-group row">
				    <label for="country" class="col-md-3 col-form-label">Država</label>
				    <div class="col-md-6">
				        <input type="text" class="form-control @error('country') is-invalid @enderror" id="country" name="country" placeholder="Država" value="{{ $settings->country }}">
				        @error('country')
				            <span class="invalid-feedback" role="alert">
				                <strong>{{ $message }}</strong>
				            </span>
				        @enderror
				    </div>
				</div>

				<div class="form-group row">
				    <label for="city" class="col-md-3 col-form-label">Grad</label>
				    <div class="col-md-6">
				        <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" placeholder="Grad" value="{{ $settings->city }}">
				        @error('city')
				            <span class="invalid-feedback" role="alert">
				                <strong>{{ $message }}</strong>
				            </span>
				        @enderror
				    </div>
				</div>

				<div class="form-group row">
				    <label for="postcode" class="col-md-3 col-form-label">Poštanski broj</label>
				    <div class="col-md-6">
				        <input type="text" class="form-control @error('postcode') is-invalid @enderror" id="postcode" name="postcode" placeholder="Poštanski broj" value="{{ $settings->postcode }}">
				        @error('postcode')
				            <span class="invalid-feedback" role="alert">
				                <strong>{{ $message }}</strong>
				            </span>
				        @enderror
				    </div>
				</div>

				<div class="form-group row">
				    <label for="address" class="col-md-3 col-form-label">Adresa</label>
				    <div class="col-md-6">
				        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" placeholder="Adresa" value="{{ $settings->address }}">
				        @error('address')
				            <span class="invalid-feedback" role="alert">
				                <strong>{{ $message }}</strong>
				            </span>
				        @enderror
				    </div>
				</div>

				<div class="form-group row">
				    <label for="fax" class="col-md-3 col-form-label">Fax</label>
				    <div class="col-md-6">
				        <input type="text" class="form-control @error('fax') is-invalid @enderror" id="fax" name="fax" placeholder="Fax" value="{{ $settings->fax }}">
				        @error('fax')
				            <span class="invalid-feedback" role="alert">
				                <strong>{{ $message }}</strong>
				            </span>
				        @enderror
				    </div>
				</div>

				<div class="form-group row">
				    <label for="primary_phone" class="col-md-3 col-form-label">Primarni broj telefona</label>
				    <div class="col-md-6">
				        <input type="text" class="form-control @error('primary_phone') is-invalid @enderror" id="primary_phone" name="primary_phone" placeholder="Primarni broj telefona" value="{{ $settings->primary_phone }}">
				        @error('primary_phone')
				            <span class="invalid-feedback" role="alert">
				                <strong>{{ $message }}</strong>
				            </span>
				        @enderror
				    </div>
				</div>

				<div class="form-group row">
				    <label for="secondary_phone" class="col-md-3 col-form-label">Sekundarni broj telefona</label>
				    <div class="col-md-6">
				        <input type="text" class="form-control @error('secondary_phone') is-invalid @enderror" id="secondary_phone" name="secondary_phone" placeholder="Sekundarni broj telefona" value="{{ $settings->secondary_phone }}">
				        @error('secondary_phone')
				            <span class="invalid-feedback" role="alert">
				                <strong>{{ $message }}</strong>
				            </span>
				        @enderror
				    </div>
				</div>
				
				<div class="form-group row">
				    <label for="primary_mobile" class="col-md-3 col-form-label">Primarni broj mobitela</label>
				    <div class="col-md-6">
				        <input type="text" class="form-control @error('primary_mobile') is-invalid @enderror" id="primary_mobile" name="primary_mobile" placeholder="Primarni broj mobitela" value="{{ $settings->primary_mobile }}">
				        @error('primary_mobile')
				            <span class="invalid-feedback" role="alert">
				                <strong>{{ $message }}</strong>
				            </span>
				        @enderror
				    </div>
				</div>

				<div class="form-group row">
				    <label for="secondary_phone" class="col-md-3 col-form-label">Sekundarni broj mobitela</label>
				    <div class="col-md-6">
				        <input type="text" class="form-control @error('secondary_mobile') is-invalid @enderror" id="secondary_mobile" name="secondary_mobile" placeholder="Sekundarni broj mobitela" value="{{ $settings->secondary_phone }}">
				        @error('secondary_mobile')
				            <span class="invalid-feedback" role="alert">
				                <strong>{{ $message }}</strong>
				            </span>
				        @enderror
				    </div>
				</div>

				<div class="form-group row">
				    <label for="facebook" class="col-md-3 col-form-label">Facebook</label>
				    <div class="col-md-6">
				        <input type="text" class="form-control @error('facebook') is-invalid @enderror" id="facebook" name="facebook" placeholder="Link facebook stranice" value="{{ $settings->facebook }}">
				        @error('facebook')
				            <span class="invalid-feedback" role="alert">
				                <strong>{{ $message }}</strong>
				            </span>
				        @enderror
				    </div>
				</div>
				
				<div class="form-group row">
				    <label for="youtube" class="col-md-3 col-form-label">Youtube</label>
				    <div class="col-md-6">
				        <input type="text" class="form-control @error('youtube') is-invalid @enderror" id="youtube" name="youtube" placeholder="Link youtube kanala" value="{{ $settings->youtube }}">
				        @error('youtube')
				            <span class="invalid-feedback" role="alert">
				                <strong>{{ $message }}</strong>
				            </span>
				        @enderror
				    </div>
				</div>

				<div class="form-group row">
				    <label for="twitter" class="col-md-3 col-form-label">Twitter</label>
				    <div class="col-md-6">
				        <input type="text" class="form-control @error('twitter') is-invalid @enderror" id="twitter" name="twitter" placeholder="Link twitter stranice" value="{{ $settings->twitter }}">
				        @error('twitter')
				            <span class="invalid-feedback" role="alert">
				                <strong>{{ $message }}</strong>
				            </span>
				        @enderror
				    </div>
				</div>
				
                <div class="form-group btn-form-group text-right">
				    <button type="submit" class="btn custom-button" id="">
						Spremi izmjene
					</button>
				</div>

			</form>
		</div>
	</div>
@endsection

@section('js')
	<script type="text/javascript">
		var fileInput = $('#image');
        var fileNameDisplayField = $('.file-name-display-field');

		fileInput.change(function() {
      		var fileName = document.getElementById("image").files[0].name;
      		var fileSize =  document.getElementById("image").files[0].size / 1024;

      		if (fileSize > 3048){
      			$.notify({
                    message: "Image size is larger than 3MB." 
                },{
                    type: 'danger',
                    animate: {
                        enter: 'animated fadeInRight',
                        exit: 'animated fadeOutRight'
                    },
                    placement: {
                        from: "bottom",
                        align: "right"
                    },
                });
                fileInput.val('');
      		} else {
				fileNameDisplayField.html(fileName);
      		}
  		});

	</script>
@endsection

