@extends('app.layouts.master')

@section('title', 'Uređaji')

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
					Jeste li sigurni da želite obrisati odabranu uređaj ?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Zatvori</button>
					<form action="" id="single-resource-delete-form" method="POST">
						@csrf
						@method('DELETE')
						<button type="submit" class="btn btn-danger">Obriši</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="container">
		
		<div class="app-header">
			<div class="row d-flex align-items-center">
				<div class="col-md-6 col-sm-12 app-header__title">
					<h3>Uređaji</h3>
				</div>	

				<div class="col-md-6 col-sm-12 app-header__add-btn">
					<a class="btn custom-button" href="{{ route('devices.create') }}">
						Dodaj uređaj
					</a>
				</div>
			</div>
		</div>
		
		@if (session()->has('error') || session()->has('success'))
		<div class="app-notifications">
			@include('app.partials.error')
			@include('app.partials.success')
		</div>
		@endif

		<div class="app-content">
			<form method="POST" action="" id="update-many-resources-form">
				
				@csrf
				
				<input type="hidden" name="action" id="action-input">

				<div class="row">
					<div class="col-md-12">
						<div class="table-responsive bg-white shadow">
							<table id="resources" class="table " style="width: 100%">
								<thead>
									<tr>
										<th class="text-center">
											Ime
										</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@foreach ($devices as $device)
									<tr>
						        		<td class="text-center">
						        			{{ $device->name }}
						        		</td>
						        		<td class="text-right">
						        			<button class="action-icon action-icon-danger single-resource-delete-btn" data-resource-id="{{ $device->id }}" data-toggle="modal" data-target="#single-resource-delete-modal" type="button">
												<i class="far fa-trash-alt"></i>
											</button>
						        			<a class="action-icon action-icon-primary ml-2 mr-2" href="{{ route('devices.show', $device) }}">
						        				<i class="far fa-eye"></i>
						        			</a>
						        			<a class="action-icon action-icon-primary" href="{{ route('devices.edit', $device) }}">
						        				<i class="fas fa-pencil-alt"></i>
						        			</a>
						        		</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
@endsection

@section('js')
	<script>
		var table = $('#resources');

		var singleResourceActivateModalBtn = $('.single-resource-activate-btn');
		var singleResourceCancelModalBtn = $('.single-resource-cancel-btn');

		var singleResourceDeleteBtn = $('.single-resource-delete-btn');
		var singleResourceDeleteForm = $('#single-resource-delete-form');

		table.DataTable({
		 	'aaSorting': [],
		 	'columnDefs': [
				{ 
					'orderable': false, 
					'targets': 1
				}
			],
			language: {
               	searchPlaceholder: "{{ __('global.search') }}",
                search: "",
                "info": "Prikazujem _START_ do _END_ od _TOTAL_ unosa",
                "lengthMenu":     "{{ __('global.showentries') }}",
                "infoEmpty":      "Prikazujem 0 do 0 od 0 unosa",
                "infoFiltered":   "(filtrirano od _MAX_ ukupnih unosa)",
                "zeroRecords":    "{{ __('global.noresult') }}",
                "paginate": {
                    "next":       "{{ __('global.next') }}",
                    "previous":   "{{ __('global.previous') }}"
                },
            }
		 });

		singleResourceDeleteBtn.on('click', function() {
			var resourceID = $(this).attr('data-resource-id');
            var url = '{{ route("devices.destroy", ":resourceID") }}';

            url = url.replace(':resourceID', resourceID);
            singleResourceDeleteForm.attr('action', url);
		});
	</script>
@endsection