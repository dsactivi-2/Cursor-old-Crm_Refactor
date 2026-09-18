@extends('app.layouts.master')

@section('title', 'Podešavanja')

@section('content')
	<div class="container">
		<div class="app-header">
			<div class="row d-flex align-items-center">
				<div class="col-md-6 col-sm-12 app-header__title">
					<h3>Podešavanja</h3>
				</div>	

				<div class="col-md-6 col-sm-12 app-header__add-btn">
					<a class="btn custom-button" href="{{ route('settings.edit') }}">
						<i class="fas fa-pencil-alt"></i>
					</a>
				</div>
			</div>
		</div>

		<div class="app-content resource-content">
			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Email
				</div>

				<div class="col-md-6 resource-content-row__data">
					{{ $settings->email ?? '-' }}
				</div>
			</div>

			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Slika
				</div>

				<div class="col-md-6 resource-content-row__data">
					@if (!is_null($settings->image))
						<img src="{{ asset('storage/settings/' . $settings->image) }}">
					@else
						-
					@endif
				</div>
			</div>	

			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Država
				</div>

				<div class="col-md-6 resource-content-row__data">
					{{ $settings->country ?? '-' }}
				</div>
			</div>

			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Grad
				</div>

				<div class="col-md-6 resource-content-row__data">
					{{ $settings->city ?? '-' }}
				</div>
			</div>

			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Poštanski broj
				</div>

				<div class="col-md-6 resource-content-row__data">
					{{ $settings->postcode ?? '-' }}
				</div>
			</div>

			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Adresa
				</div>

				<div class="col-md-6 resource-content-row__data">
					{{ $settings->address ?? '-' }}
				</div>
			</div>

			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Fax
				</div>

				<div class="col-md-6 resource-content-row__data">
					{{ $settings->fax ?? '-' }}
				</div>
			</div>

			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Primarni broj telefona 
				</div>

				<div class="col-md-6 resource-content-row__data">
					{{ $settings->primary_phone ?? '-' }}
				</div>
			</div>

			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Sekundarni broj mobitela
				</div>

				<div class="col-md-6 resource-content-row__data">
					{{ $settings->secondary_phone ?? '-' }}
				</div>
			</div>	

			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Primarni broj mobitela
				</div>

				<div class="col-md-6 resource-content-row__data">
					{{ $settings->primary_mobile ?? '-' }}
				</div>
			</div>

			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Sekundarni broj mobitela
				</div>

				<div class="col-md-6 resource-content-row__data">
					{{ $settings->secondary_mobile ?? '-' }}
				</div>
			</div>

			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Facebook
				</div>

				<div class="col-md-6 resource-content-row__data">
					{{ $settings->facebook ?? '-' }}
				</div>
			</div>

			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Youtube
				</div>

				<div class="col-md-6 resource-content-row__data">
					{{ $settings->youtube ?? '-' }}
				</div>
			</div>

			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Twitter
				</div>

				<div class="col-md-6 resource-content-row__data">
					{{ $settings->twitter ?? '-' }}
				</div>
			</div>
		</div>
	</div>
@endsection