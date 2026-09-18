@extends('app.layouts.master')

@section('title', 'Detalji kartice')

@section('content')
	
	<div class="container">
		<div class="app-header">
			<div class="row d-flex align-items-center">
				<div class="col-md-6 col-sm-12 app-header__title">
					<h3>Detalji kartice</h3>
				</div>	

				<div class="col-md-6 col-sm-12 app-header__add-btn">
					<a class="btn custom-button mr-2" href="{{ route('cards.edit', $card) }}">
						<i class="fas fa-pencil-alt"></i>
					</a>
					<a class="btn custom-button" href="{{ route('cards.index') }}">
						<i class="fas fa-chevron-left"></i>
					</a>
				</div>
			</div>
		</div>

		<div class="app-content resource-content">
			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					UUID Kartice
				</div>

				<div class="col-md-6 resource-content-row__data">
					{{ $card->uuid }}
				</div>
			</div>	

			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Vlasnik
				</div>

				<div class="col-md-6 resource-content-row__data">
					{{ $card->user->name }}
				</div>
			</div>	

			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Status
				</div>

				<div class="col-md-6 resource-content-row__data">
					@if($card->card_status_id == 1)
						<span class="green-dot dot"></span> 
						Aktivna
					@else
						<span class="red-dot dot"></span>
						Otkazana
					@endif
				</div>
			</div>	
		</div>

		<div class="app-header">
			<div class="row d-flex align-items-center">
				<div class="col-md-6 col-sm-12 app-header__title">
					<h3>Akcije</h3>
				</div>
			</div>
		</div>

		<div class="app-content">
			<div class="table-responsive bg-white shadow">
				<table id="resources" class="table " style="width: 100%">
					<thead>
						<tr>
							<th class="text-center">
								Akcija
							</th>
							<th class="text-center">
								Od
							</th>
							<th class="text-center">
								Do
							</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($card->timelogs as $timelog)
							<tr>
				        		<td class="text-center">
				        			{{ $timelog->action->name }}
				        		</td>
				        		<td class="text-center">
				        			{{ \Carbon\Carbon::parse($timelog->created_at)->format('H:i, d-m-Y') }}
				        		</td>
				        		<td class="text-center">
				        			@if (!is_null($timelog->updated_at))
				        			{{ \Carbon\Carbon::parse($timelog->updated_at)->format('H:i, d-m-Y') }}
				        			@else
				        			Izlazak sa posla
				        			@endif
				        		</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
@endsection

@section('js')
	<script>
		var table = $('#resources');

		table.DataTable({
		 	'aaSorting': [],
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