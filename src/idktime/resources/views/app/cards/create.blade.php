@extends('app.layouts.master')

@section('title', 'Nova RFID kartica')

@section('css')
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap-select/bootstrap-select.min.css') }}">
@endsection

@section('content')
	
	<div class="container pb-4">
		<div class="app-header">
			<div class="row d-flex align-items-center">
				<div class="col-md-6 col-sm-12 app-header__title">
					<h3>Nova RFID kartica</h3>
				</div>	

				<div class="col-md-6 col-sm-12 app-header__add-btn">
					<a class="btn custom-button" href="{{ route('cards.index') }}">
						<i class="fas fa-chevron-left"></i>
					</a>
				</div>
			</div>
		</div>

		<div class="app-content form-content shadow">
			<form method="POST" action="{{ route('cards.store') }}">

				@csrf
				
                <div class="form-group row">
                	<label for="user" class="col-md-3 col-form-label">Vlasnik kartice</label>
                	<div class="col-md-6">
						<select class="selectpicker show-tick" name="user_id" id="user" data-width="100%" title="Odaberi vlasnika kartice" data-size="6" data-live-search="true" required>
							@foreach($users as $user)
								<option value="{{ $user->id }}" data-subtext="Broj kartica ({{$user->cards_count }})">{{ $user->name }}</option>
							@endforeach
						</select>
                	</div>
                </div>
                
				<div class="form-group row">
				    <label for="uuid" class="col-md-3 col-form-label">Identifikacijski broj</label>
				    <div class="col-md-6">
				        <input type="text" class="form-control @error('uuid') is-invalid @enderror" id="uuid" name="uuid" placeholder="Identifikacijski broj kartice" value="{{ old('uuid') }}" required>
				        @error('uuid')
				            <span class="invalid-feedback" role="alert">
				                <strong>{{ $message }}</strong>
				            </span>
				        @enderror
				    </div>
				</div>


                 <div class="form-group btn-form-group text-right">
				    <button type="submit" class="btn custom-button" id="">
						Dodaj RFID karticu
					</button>
				</div>
			</form>
		</div>
	</div>
@endsection

@section('js')
	<script type="text/javascript" src="{{ asset('assets/js/bootstrap-select/bootstrap-select.min.js') }}"></script>
@endsection