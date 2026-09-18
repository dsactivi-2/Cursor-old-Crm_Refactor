@extends('app.layouts.master')

@section('title', 'Detalji uređaja')

@section('content')
	
	<div class="modal fade custom-modal" id="single-resource-delete-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="exampleModalLabel">Brisanje uređaja</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					Da li želite obrisati uređaj {{ $device->name }} ?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Zatvori</button>
					<form action="{{ route('devices.destroy', $device) }}" method="POST">
						@csrf
						@method('DELETE')
						<button type="submit" class="btn custom-button custom-button-danger">Obriši</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="container">
		<div class="app-header">
			<div class="row d-flex align-items-center">
				<div class="col-md-6 col-sm-12 app-header__title">
					<h3>Detalji uređaja</h3>
				</div>	

				<div class="col-md-6 col-sm-12 app-header__add-btn">
					<button class="btn custom-button custom-button-danger mr-2" data-toggle="modal" data-target="#single-resource-delete-modal">
						<i class="far fa-trash-alt"></i>
					</button>
					<a class="btn custom-button mr-2" href="{{ route('devices.edit', $device) }}">
						<i class="fas fa-pencil-alt"></i>
					</a>
					<a class="btn custom-button" href="{{ route('devices.index') }}">
						<i class="fas fa-chevron-left"></i>
					</a>
				</div>
			</div>
		</div>

		@if (session()->has('error') || session()->has('success'))
		<div class="app-notifications">
			@include('partials.error')
			@include('partials.success')
		</div>
		@endif

		<div class="app-content resource-content">
			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Ime
				</div>

				<div class="col-md-6 resource-content-row__data">
					{{ $device->name }}
				</div>
			</div>	

			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Link
				</div>

				<div class="col-md-6 resource-content-row__data">
					{{ $device->link }}
				</div>
			</div>	
		</div>
	</div>
@endsection

@section('js')
	<script>
		var table = $('#resources');
		var resourceDetachTrigger = $('.resource-detach-btn');

		resourceDetachTrigger.click(function(){

            var url = $(this).attr('data-url');
            $('#resource-detach-form').attr('action', url);

        });
	</script>
@endsection