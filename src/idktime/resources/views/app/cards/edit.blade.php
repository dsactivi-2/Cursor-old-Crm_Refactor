@extends('app.layouts.master')

@section('title', 'Uredi RFID karticu')

@section('css')
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap-select/bootstrap-select.min.css') }}">
@endsection

@section('content')
	
	<div class="container pb-4">
		<div class="app-header">
			<div class="row d-flex align-items-center">
				<div class="col-md-6 col-sm-12 app-header__title">
					<h3>Uredi RFID karticu</h3>
				</div>	

				<div class="col-md-6 col-sm-12 app-header__add-btn">
					<a class="btn custom-button" href="{{ route('cards.index') }}">
						<i class="fas fa-chevron-left"></i>
					</a>
				</div>
			</div>
		</div>

		<div class="app-content form-content shadow">
			<form method="POST" action="{{ route('cards.update', $card) }}">

				@csrf
				@method('PUT')

                <div class="form-group row">
                	<label for="user" class="col-md-3 col-form-label">Vlasnik kartice</label>
                	<div class="col-md-6">
						<select class="selectpicker show-tick" name="user_id" id="user" data-width="100%" title="Odaberi vlasnika kartice" data-size="6" data-live-search="true" required>
							@foreach($users as $user)
								@if ($user->id == $card->user_id)
									<option value="{{ $user->id }}" data-subtext="Broj kartica ({{$user->cards_count }})" selected>{{ $user->name }}</option>
								@else
									<option value="{{ $user->id }}" data-subtext="Broj kartica ({{$user->cards_count }})">{{ $user->name }}</option>
								@endif
							@endforeach
						</select>
                	</div>
                </div>
				
				<div class="form-group row">
				    <label for="uuid" class="col-md-3 col-form-label">Identifikacijski broj</label>
				    <div class="col-md-6">
				        <input type="text" class="form-control @error('uuid') is-invalid @enderror" id="uuid" name="uuid" placeholder="Identifikacijski broj kartice" value="{{ $card->uuid }}" required>
				        @error('uuid')
				            <span class="invalid-feedback" role="alert">
				                <strong>{{ $message }}</strong>
				            </span>
				        @enderror
				    </div>
				</div>


                <div class="form-group row">
                	<label for="role-selectpicker" class="col-md-3 col-form-label">Status kartice</label>
                	<div class="col-md-6">
                		@foreach ($card_statuses as $card_status)
						<div class="custom-control custom-radio custom-control-inline 
							@if($card_status->id == 1)
								green-radio
							@else
								red-radio
							@endif
							">
							<input type="radio" id="card_status_{{ $card_status->id }}" name="card_status_id" class="custom-control-input" @if ($card_status->id == $card->card_status_id) checked @endif value="{{ $card_status->id }}">
							<label class="custom-control-label" for="card_status_{{ $card_status->id }}">{{ $card_status->name }}</label>
						</div>
                		@endforeach
                	</div>
                </div>

                 <div class="form-group btn-form-group text-right">
				    <button type="submit" class="btn custom-button" id="">
						Uredi RFID karticu
					</button>
				</div>
			</form>
		</div>
	</div>
@endsection

@section('js')
	<script type="text/javascript" src="{{ asset('assets/js/bootstrap-select/bootstrap-select.min.js') }}"></script>
@endsection