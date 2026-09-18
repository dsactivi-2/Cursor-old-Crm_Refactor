@extends('app.layouts.master')

@section('title', 'Zaposlenici')

@section('content')

	<div class="modal fade custom-modal" id="single-resource-delete-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="exampleModalLabel">Brisanje zaposlenika</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					Jeste li sigurni da želite obrisati odabranog zaposlenika ?
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
					<h3>Zaposlenici</h3>
				</div>	

				<div class="col-md-6 col-sm-12 app-header__add-btn">
					<a class="btn custom-button" href="{{ route('users.create') }}">
						Dodaj zaposlenika
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
										{{-- <th>
											<div class="custom-control custom-checkbox select-all-checkbox-container">
					                            <input type="checkbox" class="custom-control-input" type="checkbox" id="select-all-checkboxes-btn">
					                            <label class="custom-control-label" for="select-all-checkboxes-btn"></label>
					                        </div>
										</th> --}}
										<th></th>
										<th class="text-center">
											Ime i prezime
										</th>
										<th class="text-center">
											Email
										</th>
										<th class="text-center">
											Rola
										</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@foreach ($users as $user)
									<tr>
										@if ($user->id != auth()->user()->id)
										<td></td>
						        			{{-- <td>
						        				 <div class="custom-control custom-checkbox">
						                            <input type="checkbox" class="custom-control-input resource-checkboxes" type="checkbox" name="resource_ids[]" id="resource_{{ $user->id }}" value="{{ $user->id }}">
						                            <label class="custom-control-label" for="resource_{{ $user->id }}"></label>
						                        </div>
						        			</td> --}}
						        		@else
						        			<td>
						        				<i class="fas fa-user"></i>
						        			</td>
						        		@endif
						        		<td class="text-center">
						        			{{ $user->name }}
						        		</td>
						        		<td class="text-center">
						        			{{ $user->email ?? '-' }}
						        		</td>
						        		<td class="text-center">
						        			{{ $user->userRole->name }}
						        		</td>
						        		<td class="text-right">
						        			<a class="action-icon action-icon-primary mr-2" href="{{ route('users.show', $user) }}">
						        				<i class="far fa-eye"></i>
						        			</a>
						        			<a class="action-icon action-icon-primary" href="{{ route('cards.create-to-user', $user) }}"  data-toggle="tooltip" data-placement="top" title="Dodaj RFID karticu">
						        				<i class="fas fa-id-card-alt"></i>
						        			</a>
						        			@if ($user->id != auth()->user()->id)
						        			<a class="action-icon action-icon-primary mr-2 ml-2" href="{{ route('users.edit', $user) }}">
						        				<i class="fas fa-pencil-alt"></i>
						        			</a>
						        			{{-- <button class="action-icon action-icon-danger single-resource-delete-btn" data-resource-id="{{ $user->id }}" data-toggle="modal" data-target="#single-resource-delete-modal" type="button">
												<i class="far fa-trash-alt"></i>
											</button> --}}
											@else
											<a class="action-icon action-icon-primary ml-2" href="{{ route('users.edit', $user) }}">
						        				<i class="fas fa-pencil-alt"></i>
						        			</a>
											@endif
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
		var resourceCheckboxes = $('.resource-checkboxes');
		var actionInput = $('#action-input');
		
		var selectAllCheckboxesBtn = $('#select-all-checkboxes-btn');
		var singleResourceDeleteBtn = $('.single-resource-delete-btn');
		var resourcesArchiveBtn = $('#resources-archive-button');
		var resourcesActivateBtn = $('#resources-activate-button');
		var resourceDeleteModalTriggerBtn = $('#resource-delete-button');
		var resourcesDeleteBtn = $('#resources-delete-btn');

		var singleResourceDeleteForm = $('#single-resource-delete-form');
		var updateManyResourcesForm = $('#update-many-resources-form');

		table.DataTable({
		 	'aaSorting': [],
		 	'columnDefs': [
				{ 
					'orderable': false, 
					'targets': 0 
				},{ 
					'orderable': false, 
					'targets': 4
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
            var url = '{{ route("users.destroy", ":resourceID") }}';

            url = url.replace(':resourceID', resourceID);
            singleResourceDeleteForm.attr('action', url);
		});

		selectAllCheckboxesBtn.on('click', function() {
			if ($(this).hasClass('clicked')){
				resourceCheckboxes.prop('checked', false);
				$(this).removeClass('clicked');
			} else {
				resourceCheckboxes.prop('checked', true);
				$(this).addClass('clicked');
			}
		});

		resourcesArchiveBtn.on('click', function() {
			actionInput.val(1);
			updateManyResourcesForm.submit();
		});

		resourcesActivateBtn.on('click', function() {
			actionInput.val(2);
			updateManyResourcesForm.submit();
		});

		resourceDeleteModalTriggerBtn.on('click', function() {
			actionInput.val(3);
		});

		resourcesDeleteBtn.on('click', function() {
			updateManyResourcesForm.submit();
		});

	</script>
@endsection