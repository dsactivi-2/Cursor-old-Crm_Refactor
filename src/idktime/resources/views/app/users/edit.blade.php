@extends('app.layouts.master')

@section('title', 'Uredi zaposlenika')

@section('css')
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap-select/bootstrap-select.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/flatpickr/flatpickr.min.css') }}">
@endsection

@section('content')
	
	<div class="container pb-4">
		<div class="app-header">
			<div class="row d-flex align-items-center">
				<div class="col-md-6 col-sm-12 app-header__title">
					<h3>Uredi zaposlenika - {{ $user->name }}</h3>
				</div>	

				<div class="col-md-6 col-sm-12 app-header__add-btn">
					<a class="btn custom-button" href="{{ route('users.index') }}">
						<i class="fas fa-chevron-left"></i>
					</a>
				</div>
			</div>
		</div>

		<div class="app-content form-content shadow">
			<form method="POST" action="{{ route('users.update', $user) }}" enctype='multipart/form-data'>
				
				@method('PUT')
				@csrf
				
				<div class="form-group row">
				    <label for="name" class="col-md-3 col-form-label">Ime i prezime *</label>
				    <div class="col-md-6">
				        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Ime i prezime zaposlenika" value="{{ $user->name }}" required>
				        @error('name')
				            <span class="invalid-feedback" role="alert">
				                <strong>{{ $message }}</strong>
				            </span>
				        @enderror
				    </div>
				</div>

				<div class="form-group row">
				    <label for="name" class="col-md-3 col-form-label">Email</label>
				    <div class="col-md-6">
				        <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="E-mail adresa" value="{{ $user->email }}">
				        @error('email')
				            <span class="invalid-feedback" role="alert">
				                <strong>{{ $message }}</strong>
				            </span>
				        @enderror
				    </div>
				</div>

				<div class="form-group row">
                	<label for="role-selectpicker" class="col-md-3 col-form-label">Rola *</label>
                	<div class="col-md-6">
						<select class="selectpicker show-tick" id="role-selectpicker" name="user_role_id" data-width="100%" title="Odaberi rolu zaposlenika" required>
							@foreach($user_roles as $user_role)
								@if ($user_role->id == $user->user_role_id)
									<option value="{{ $user_role->id }}" selected>{{ $user_role->name }}</option>
								@else
									<option value="{{ $user_role->id }}">{{ $user_role->name }}</option>
								@endif
							@endforeach
						</select>
                	</div>
                </div>

                <div class="form-group row" id="permissions-form-group">
                	<label for="permissions" class="col-md-3 col-form-label">Permisije</label>
                	<div class="col-md-8">
                		@foreach($permissions as $permission)
							<div class="custom-control custom-checkbox custom-control-inline permission-checkbox-parent mr-4">
								<input type="checkbox" id="permission_{{ $permission->id }}" name="permissions[]" class="permission-checkbox custom-control-input" value="{{ $permission->id }}"
								@if(in_array($permission->id, $user_related_permissions))
									checked
								@endif
								>
								<label class="custom-control-label" for="permission_{{ $permission->id }}">{{ $permission->name }}</label>
							</div>
                		@endforeach
                	</div>
                </div>

				<div class="form-group row" id="password-form-group">
				    <label for="name" class="col-md-3 col-form-label">Lozinka</label>
				    <div class="col-md-6">
				        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Lozinka" value="" aria-describedby="passwordHelp">
				        <small id="passwordHelp" class="form-text text-muted">Ukoliko navedeno polje ostavite prazno, lozinka neće biti promijenjena.</small>
				        @error('password')
				            <span class="invalid-feedback" role="alert">
				                <strong>{{ $message }}</strong>
				            </span>
				        @enderror
				    </div>
				</div>

				<div class="form-group row" id="password-confirmation-form-group">
				    <label for="name" class="col-md-3 col-form-label">Potvrdi lozinku</label>
				    <div class="col-md-6">
				        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" placeholder="Potvrdi lozinku" value="">
				        @error('password_confirmation')
				            <span class="invalid-feedback" role="alert">
				                <strong>{{ $message }}</strong>
				            </span>
				        @enderror
				    </div>
				</div>

				<div class="form-group row">
                	<label for="position" class="col-md-3 col-form-label">Pozicija</label>
                	<div class="col-md-6">
						<select class="selectpicker show-tick" name="position_id" data-width="100%" id="position" title="Odaberi poziciju zaposlenika">
							@foreach($positions as $position)
								@if ($position->id == $user->position_id)
									<option value="{{ $position->id }}" selected>{{ $position->name }}</option>
								@else
									<option value="{{ $position->id }}">{{ $position->name }}</option>
								@endif
							@endforeach
						</select>
                	</div>
                </div>

                <div class="form-group row">
                	<label for="department" class="col-md-3 col-form-label">Služba</label>
                	<div class="col-md-6">
						<select class="selectpicker show-tick" name="department_id" id="department" data-width="100%" title="Odaberi službu zaposlenika">
							@foreach($departments as $department)
								@if ($department->id == $user->department_id)
									<option value="{{ $department->id }}" selected>{{ $department->name }}</option>
								@else
									<option value="{{ $department->id }}">{{ $department->name }}</option>
								@endif
							@endforeach
						</select>
                	</div>
                </div>
				
                <div class="form-group row">
                	<label for="role-selectpicker" class="col-md-3 col-form-label">Status</label>
                	<div class="col-md-6">
                		@foreach ($user_statuses as $user_status)
						<div class="custom-control custom-radio custom-control-inline 
							@if($user_status->id == 1)
								green-radio
							@else
								red-radio
							@endif
							">
							<input type="radio" id="user_status_{{ $user_status->id }}" name="user_status_id" class="custom-control-input" @if ($user_status->id == $user->user_status_id) checked @endif value="{{ $user_status->id }}">
							<label class="custom-control-label" for="user_status_{{ $user_status->id }}">{{ $user_status->name }}</label>
						</div>
                		@endforeach
                	</div>
                </div>

				<div class="form-group row">
                	<label class="col-md-3 col-form-label align-items-start">Trenutna slika profila</label>
                	<div class="col-md-8">
                		@if (is_null($user->image))
                			-
                		@else
                		<div class="resource-image-container">
                			<img src="{{ asset('storage/users/' . $user->image . '') }}" class="img-fluid">
                		</div>
                		@endif
                	</div>
                </div>

				<div class="form-group row">
                	<label class="col-md-3 col-form-label">Slika profila</label>
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
                    <label for="category-select" class="col-md-3 col-form-label">Datum rođenja</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control @error('birthday') is-invalid @enderror" id="birthday" name="birthday" placeholder="Dodaj datum rođenja">
                        @error('birthday')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
				    <label for="phone" class="col-md-3 col-form-label">Broj telefona</label>
				    <div class="col-md-6">
				        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" placeholder="Broj telefona zaposlenika" value="{{ $user->phone }}">
				        @error('phone')
				            <span class="invalid-feedback" role="alert">
				                <strong>{{ $message }}</strong>
				            </span>
				        @enderror
				    </div>
				</div>

				<div class="form-group row">
				    <label for="country" class="col-md-3 col-form-label">Država</label>
				    <div class="col-md-6">
				        <input type="text" class="form-control @error('country') is-invalid @enderror" id="country" name="country" placeholder="Država" value="{{ $user->country }}">
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
				        <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" placeholder="Grad" value="{{ $user->city }}">
				        @error('city')
				            <span class="invalid-feedback" role="alert">
				                <strong>{{ $message }}</strong>
				            </span>
				        @enderror
				    </div>
				</div>

				<div class="form-group row">
				    <label for="address" class="col-md-3 col-form-label">Adresa</label>
				    <div class="col-md-6">
				        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" placeholder="Adresa stanovanja" value="{{ $user->address }}">
				        @error('address')
				            <span class="invalid-feedback" role="alert">
				                <strong>{{ $message }}</strong>
				            </span>
				        @enderror
				    </div>
				</div>

				<div class="form-group row">
				    <label for="jmbg" class="col-md-3 col-form-label">JMBG</label>
				    <div class="col-md-6">
				        <input type="text" class="form-control @error('jmbg') is-invalid @enderror" id="jmbg" name="jmbg" placeholder="JMBG" value="{{ $user->jmbg }}">
				        @error('jmbg')
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
	<script type="text/javascript" src="{{ asset('assets/js/bootstrap-select/bootstrap-select.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/flatpickr/flatpickr.min.js') }}"></script>
	<script type="text/javascript">
		var roleSelect = $('#role-selectpicker');

		var permissionCheckboxParent = $('.permission-checkbox-parent');
		var permissionCheckbox = $('.permission-checkbox');

		var persmissionSelectFormGroup = $('#permissions-form-group');
		var passwordInputFormGroup = $('#password-form-group');
		var passwordConfirmFormGroup = $('#password-confirmation-form-group');
		var birthday = $('#birthday');

		var fileInput = $('#image');
        var fileNameDisplayField = $('.file-name-display-field');

		var roleID = {{ $user->user_role_id }};

		if (roleID == 1){
			passwordInputFormGroup.find('input').attr('disabled', false);
			passwordConfirmFormGroup.find('input').attr('disabled', false);

			persmissionSelectFormGroup.slideDown('down');
			permissionCheckbox.prop('checked', true);
			permissionCheckboxParent.addClass('pointer-events-disable');
		} else if (roleID == 2) {
			passwordInputFormGroup.find('input').attr('disabled', false);
			passwordConfirmFormGroup.find('input').attr('disabled', false);

			persmissionSelectFormGroup.slideDown('down');
			permissionCheckboxParent.removeClass('pointer-events-disable');
		} else {
			passwordInputFormGroup.find('input').attr('required', false);
			passwordInputFormGroup.find('input').attr('disabled', true);

			passwordConfirmFormGroup.find('input').attr('required', false);
			passwordConfirmFormGroup.find('input').attr('disabled', true);

			permissionCheckbox.prop('checked', false);
			permissionCheckboxParent.addClass('pointer-events-disable');
		}

		roleSelect.selectpicker();

		@if (!is_null($user->birthday))
            birthday.flatpickr({
                dateFormat: "d-m-Y",
                defaultDate: ["{{ \Carbon\Carbon::parse($user->birthday)->format('d-m-Y') }}"],
            });
        @else
            birthday.flatpickr({
                dateFormat: "d-m-Y"
            });
         @endif

		roleSelect.on('change', function(){
			roleID = $(this).find('option:selected').val();
			
			if (roleID == 1){
				passwordInputFormGroup.slideDown('slow');
				passwordInputFormGroup.find('input').attr('disabled', false);

				passwordConfirmFormGroup.slideDown('slow');
				passwordConfirmFormGroup.find('input').attr('disabled', false);

				persmissionSelectFormGroup.slideDown('down');
				permissionCheckbox.prop('checked', true);
				permissionCheckboxParent.addClass('pointer-events-disable');
			} else if (roleID == 2) {
				passwordInputFormGroup.slideDown('slow');
				passwordInputFormGroup.find('input').attr('disabled', false);

				passwordConfirmFormGroup.slideDown('slow');
				passwordConfirmFormGroup.find('input').attr('disabled', false);

				persmissionSelectFormGroup.slideDown('down');
				permissionCheckbox.prop('checked', false);
				permissionCheckboxParent.removeClass('pointer-events-disable');
			} else {
				// passwordInputFormGroup.slideUp('slow');
				passwordInputFormGroup.find('input').attr('required', false);
				passwordInputFormGroup.find('input').attr('disabled', true);

				// passwordConfirmFormGroup.slideUp('slow');
				passwordConfirmFormGroup.find('input').attr('required', false);
				passwordConfirmFormGroup.find('input').attr('disabled', true);

				// persmissionSelectFormGroup.slideUp('slow');
				permissionCheckbox.prop('checked', false);
				permissionCheckboxParent.addClass('pointer-events-disable');
			}
		});

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