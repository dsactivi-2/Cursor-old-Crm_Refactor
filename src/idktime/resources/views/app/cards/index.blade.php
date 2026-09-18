@extends('app.layouts.master')

@section('title', 'RFID Kartice')

@section('content')

	<div class="modal fade custom-modal" id="single-resource-activate-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="exampleModalLabel">Aktiviranje kartice</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					Jeste li sigurni da želite aktivirati odabranu karticu ?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Zatvori</button>
					<form action="" id="single-resource-activate-form" method="POST">
						@csrf
						<button type="submit" class="btn btn-primary">Aktiviraj</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade custom-modal" id="single-resource-cancel-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="exampleModalLabel">Otkazivanje kartice</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					Jeste li sigurni da želite otkazati odabranu karticu ?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Zatvori</button>
					<form action="" id="single-resource-cancel-form" method="POST">
						@csrf
						<button type="submit" class="btn btn-danger">Otkaži</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="container">
		
		<div class="app-header">
			<div class="row d-flex align-items-center">
				<div class="col-md-6 col-sm-12 app-header__title">
					<h3>RFID Kartice</h3>
				</div>	

				<div class="col-md-6 col-sm-12 app-header__add-btn">
					<a class="btn custom-button" href="{{ route('cards.create') }}">
						Dodaj RFID karticu
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
											Idnetifikacijski broj
										</th>
										<th class="text-center">
											Vlasnik kartice
										</th>
										<th class="text-center">
											Status kartice
										</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@foreach ($cards as $card)
									<tr>
						        		<td class="text-center">
						        			{{ $card->uuid }}
						        		</td>
						        		<td class="text-center">
						        			{{ $card->user->name }}
						        		</td>
						        		<td class="text-center">
						        			@if ($card->card_status_id == 1)
						        				<span class="badge badge-success">
						        					Aktivna
						        				</span>
						        			@else
						        				<div class="badge badge-danger">
						        					Otkazana
						        				</div>
						        			@endif
						        		</td>
						        		<td class="text-right">
						        			@if ($card->card_status_id == 1)
						        			<button class="action-icon action-icon-danger single-resource-cancel-btn" data-resource-id="{{ $card->id }}" data-toggle="modal" data-target="#single-resource-cancel-modal" type="button">
						        				<i class="fas fa-times"></i>
						        			</button>
						        			@else
						        			<button class="action-icon action-icon-success single-resource-activate-btn" data-resource-id="{{ $card->id }}" data-toggle="modal" data-target="#single-resource-activate-modal" type="button">
						        				<i class="fas fa-check"></i>
						        			</button>
						        			@endif
						        			<a class="action-icon action-icon-primary ml-2 mr-2" href="{{ route('cards.show', $card) }}">
						        				<i class="far fa-eye"></i>
						        			</a>
						        			<a class="action-icon action-icon-primary" href="{{ route('cards.edit', $card) }}">
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

		var singleResourceActivateForm = $('#single-resource-activate-form');
		var singleResourceCancelForm = $('#single-resource-cancel-form');

		table.DataTable({
		 	'aaSorting': [],
		 	'columnDefs': [
				{ 
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

		singleResourceActivateModalBtn.on('click', function() {
			var resourceID = $(this).attr('data-resource-id');
            var url = '{{ route("cards.activate", ":resourceID") }}';

            url = url.replace(':resourceID', resourceID);
            singleResourceActivateForm.attr('action', url);
		});

		singleResourceCancelModalBtn.on('click', function() {
			var resourceID = $(this).attr('data-resource-id');
            var url = '{{ route("cards.cancel", ":resourceID") }}';

            url = url.replace(':resourceID', resourceID);
            singleResourceCancelForm.attr('action', url);
		});
	</script>
@endsection