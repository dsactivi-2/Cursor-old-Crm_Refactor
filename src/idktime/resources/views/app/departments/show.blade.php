@extends('app.layouts.master')

@section('title', 'Detalji odjela')

@section('content')
	
	<div class="modal fade custom-modal" id="single-resource-delete-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="exampleModalLabel">Brisanje odjela</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					Da li želite obrisati službu {{ $department->name }} ?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Zatvori</button>
					<form action="{{ route('departments.destroy', $department) }}" method="POST">
						@csrf
						@method('DELETE')
						<button type="submit" class="btn custom-button custom-button-danger">Obriši</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="resource-detach-modal" tabindex="-1" role="dialog" aria-labelledby="resource-detach-modal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-scrollable" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="resource-detach-modal">Brisanje zaposlenika iz odjela</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					Jeste li sigurni da želite obrisati odabranog zaposlenika iz odjela ?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Zatvori</button>
					<form id="resource-detach-form" action="" method="POST">
						@csrf
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
					<h3>Detalji odjela</h3>
				</div>	

				<div class="col-md-6 col-sm-12 app-header__add-btn">
					<button class="btn custom-button custom-button-danger mr-2" data-toggle="modal" data-target="#single-resource-delete-modal">
						<i class="far fa-trash-alt"></i>
					</button>
					<a class="btn custom-button mr-2" href="{{ route('departments.edit', $department) }}">
						<i class="fas fa-pencil-alt"></i>
					</a>
					<a class="btn custom-button" href="{{ route('departments.index') }}">
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
					{{ $department->name }}
				</div>
			</div>	

			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Broj zaposlenih u odjelu
				</div>

				<div class="col-md-6 resource-content-row__data">
					{{ $department->users->count() }}
				</div>
			</div>	
		</div>

		<div class="app-header">
			<div class="row d-flex align-items-center">
				<div class="col-md-12 col-sm-12 app-header__title">
					<h3>Zaposleni u odjelu - {{ $department->name }}</h3>
				</div>
			</div>
		</div>

		<div class="app-content">
			<div class="row">
				<div class="col-md-12">
					<div class="table-responsive bg-white shadow">
						<table id="resources" class="table " style="width: 100%">
							<thead>
								<tr>
									<th class="text-center">
										Ime i prezime
									</th>
									<th class="text-center">
										Email
									</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								@foreach ($department->users as $user)
								<tr>
					        		<td class="text-center">
					        			{{ $user->name }}
					        		</td>
					        		<td class="text-center">
					        			{{ $user->email }}
					        		</td>
					        		<td class="text-right">
					        			<button class="action-icon action-icon-danger resource-detach-btn" data-url="{{ route('departments.detach',['department' => $department, 'user' => $user]) }}" data-toggle="modal" data-target="#resource-detach-modal" type="button">
											<i class="fas fa-unlink"></i>
										</button>
					        			<a class="action-icon action-icon-primary ml-2 mr-2" href="{{ route('users.show', $user) }}">
					        				<i class="far fa-eye"></i>
					        			</a>
										<a class="action-icon action-icon-primary" href="{{ route('users.edit', $user) }}">
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

		table.DataTable({
		 	'aaSorting': [],
		 	'columnDefs': [
				{ 
					'orderable': false, 
					'targets': 0 
				},{ 
					'orderable': false, 
					'targets': 2
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
	</script>
@endsection