@extends('app.layouts.master')

@section('title', 'Notifikacije')

@section('content')

	<div class="container">
		
		<div class="app-header">
			<div class="row d-flex align-items-center">
				<div class="col-md-6 col-sm-12 app-header__title">
					<h3>Zaposlenici</h3>
				</div>
			</div>
		</div>
		
		@if (session()->has('error') || session()->has('success'))
		<div class="app-notifications">
			@include('app.partials.error')
			@include('app.partials.success')
		</div>
		@endif

		<form action="{{ route('notifications.enable') }}" method="POST" id="enable-notification-form">
			@csrf
			<input type="hidden" name="employee_id" id="enable-notification-input">
		</form>

		<form action="{{ route('notifications.disable') }}" method="POST" id="disable-notification-form">
			@csrf
			<input type="hidden" name="employee_id" id="disable-notification-input">
		</form>

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
										Notifikacija
									</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								@foreach ($employees as $employee)
								<tr>
					        		<td class="text-center">
					        			{{ $employee->name }}
					        		</td>
					        		<td class="text-center">	
										@if ($employee->notification == 1)
											Notifikacija uključena
										@else
											Notifikacija isključena
										@endif	
					        		</td>
					        		<td class="text-right">
					        			<a class="action-icon action-icon-primary mr-2" href="{{ route('users.show', $employee) }}">
					        				<i class="far fa-eye"></i>
					        			</a>
					        			@if ($employee->notification == 1)
					        				<button class="action-icon action-icon-primary mr-2 disable-notification-button" data-toggle="tooltip" data-placement="top" title="Isključi notifikaciju" type="button" data-resource-id="{{ $employee->id }}">
						        				<i class="far fa-bell-slash"></i>
						        			</button>
						        		@else
						        			<button class="action-icon action-icon-primary mr-2 enable-notification-button" data-toggle="tooltip" data-placement="top" title="Uključi notifikaciju" type="button" data-resource-id="{{ $employee->id }}">
						        				<i class="far fa-bell"></i>
						        			</button>
					        			@endif
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
		var enableNotificationButton = $('.enable-notification-button');
		var enableNotiifcationForm = $('#enable-notification-form');
		var enableNotificationInput = $('#enable-notification-input');
		var disableNotiifcationForm = $('#disable-notification-form');
		var disableNotificationButton = $('.disable-notification-button');
		var disableNotificationInput = $('#disable-notification-input');

		enableNotificationButton.click(function(){

            var resourceID = $(this).attr('data-resource-id');
            enableNotificationInput.val(resourceID);
            enableNotiifcationForm.submit();

        });

        disableNotificationButton.click(function(){

            var resourceID = $(this).attr('data-resource-id');
            disableNotificationInput.val(resourceID);
            disableNotiifcationForm.submit();

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