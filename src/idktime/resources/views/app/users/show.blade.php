@extends('app.layouts.master')

@section('title', 'Detalji zaposlenika')

@section('css')
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.css">
	<link rel="stylesheet" href="{{ asset('assets/css/calendar/scheduler.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/flatpickr/flatpickr.min.css') }}">
@endsection


@section('content')

	<div class="modal fade custom-modal" id="event-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="exampleModalLabel">Detalji izvještaja</h4>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<ul class="list-unstyled pl-3">
						<li>
							<strong>Status:</strong> <span id="status"></span></li>
						<li>
							<strong>Vrijeme:</strong> 
							<span id="arrival-time"></span> 
							- 
							<span id="departure-time"></span>
						</li>
					</ul>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Zatvori</button>
				</div>
			</div>
		</div>
	</div>
	
	<div class="container">
		<div class="app-header">
			<div class="row d-flex align-items-center">
				<div class="col-md-6 col-sm-12 app-header__title">
					<h3>Detalji zaposlenika</h3>
				</div>	

				<div class="col-md-6 col-sm-12 app-header__add-btn">
				{{-- 	@if ($user->id != auth()->user()->id)
					<button class="btn custom-button custom-button-danger mr-2" data-toggle="modal" data-target="#single-resource-delete-modal">
						<i class="far fa-trash-alt"></i>
					</button>
					@endif --}}
					<a class="btn custom-button mr-2" href="{{ route('users.edit', $user) }}">
						<i class="fas fa-pencil-alt"></i>
					</a>
					<a class="btn custom-button" href="{{ route('users.index') }}">
						<i class="fas fa-chevron-left"></i>
					</a>
				</div>
			</div>
		</div>

		<div class="app-content resource-content">
			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Ime i prezime
				</div>

				<div class="col-md-6 resource-content-row__data">
					{{ $user->name }}
				</div>
			</div>	

			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Email
				</div>

				<div class="col-md-6 resource-content-row__data">
					{{ $user->email ?? '-' }}
				</div>
			</div>	

			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Rola
				</div>

				<div class="col-md-6 resource-content-row__data">
					{{ $user->userRole->name }}
				</div>
			</div>	

			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Status
				</div>

				<div class="col-md-6 resource-content-row__data">
					@if($user->user_status_id == 1)
						<span class="green-dot dot"></span> 
						Aktivan
					@else
						<span class="red-dot dot"></span>
						Arhiviran
					@endif
				</div>
			</div>	

			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Pozicija
				</div>

				<div class="col-md-6 resource-content-row__data">
					{{ $user->position->name ?? '-' }}
				</div>
			</div>	

			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Služba
				</div>

				<div class="col-md-6 resource-content-row__data">
					{{ $user->department->name ?? '-' }}
				</div>
			</div>	

			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Slika profila
				</div>

				<div class="col-md-6 resource-content-row__data">
					@if (!is_null($user->image))
						<img src="{{ asset('storage/users/' . $user->image) }}">
					@else
						-
					@endif
				</div>
			</div>	

			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Datum rođenja
				</div>

				<div class="col-md-6 resource-content-row__data">
					@if (!is_null($user->birthday))
					{{ \Carbon\Carbon::parse($user->birthday)->format('d-m-Y') ?? '-' }}
					@else
					-
					@endif
				</div>
			</div>	

			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Broj telefona
				</div>

				<div class="col-md-6 resource-content-row__data">
					{{ $user->phone ?? '-' }}
				</div>
			</div>

			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Država
				</div>

				<div class="col-md-6 resource-content-row__data">
					{{ $user->country ?? '-' }}
				</div>
			</div>

			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Grad
				</div>

				<div class="col-md-6 resource-content-row__data">
					{{ $user->city ?? '-' }}
				</div>
			</div>

			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Adresa
				</div>

				<div class="col-md-6 resource-content-row__data">
					{{ $user->address ?? '-' }}
				</div>
			</div>

			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					JMBG
				</div>

				<div class="col-md-6 resource-content-row__data">
					{{ $user->jmbg ?? '-' }}
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
			<div class="form-group row content-form-group">
                <label for="category-select" class="col-md-3 col-form-label">Datum izvleštaja</label>
                <div class="col-md-6">
                    <input type="text" class="form-control @error('date') is-invalid @enderror" id="date" name="date" placeholder="Odaberi datum">
                    @error('birthday')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

			<div class="row">
				<div class="col-md-12">
					<div class="calendar-wrapper bg-white shadow">
						<div id="calendar" class="shadow-sm bg-white pt-3 rounded mt-4"></div>
					</div>
				</div>
			</div>
		</div>

		<div class="app-header">
			<div class="row d-flex align-items-center">
				<div class="col-md-6 col-sm-12 app-header__title">
					<h3>Kartice</h3>
				</div>
			</div>
		</div>

		<div class="app-content resource-content">
			<div class="row resource-content-row">
				<div class="col-md-3 resource-content-row__label">
					Kartice
				</div>

				<div class="col-md-6 resource-content-row__data">
					@if (count($user->cards) > 0)
					<ul class="list-group">
					@foreach ($user->cards as $card)
						<li class="list-group-item d-flex justify-content-between align-items-center">
							<a href="{{ route('cards.edit', $card) }}">{{ $card->uuid }}</a>
							@if ($card->card_status_id == 1)
							<span class="badge badge-success badge-pill">Aktivna</span>
							@else
							<span class="badge badge-danger badge-pill">Arhivirana</span>
							@endif
						</li>
					@endforeach
					</ul>
					@else
					Korisnik nema karticu. <a href="{{ route('cards.create-to-user', $user) }}">Dodaj karticu.</a>
					@endif
				</div>
			</div>	
		</div>
	</div>
@endsection

@section('js')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.js"></script>
	<script src="{{ asset('assets/js/calendar/scheduler.js') }}"></script>
	<script src="{{ asset('assets/js/calendar/local.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/flatpickr/flatpickr.min.js') }}"></script>
	<script>
		var dateInput = $('#date');
		var initialLocaleCode = 'bs';
		var eventModal = $('#event-modal');
		var arrivalTime = $('#arrival-time');
		var departureTime = $('#departure-time');
		var eventStatus = $('#status');

		dateInput.flatpickr({
			onChange: function(selectedDates, dateStr, instance) {
		        $("#calendar").fullCalendar('gotoDate', dateStr );
		    },
        });

		$('#calendar').fullCalendar({
			themeSystem: 'bootstrap4',
			defaultView: 'timeline',
			slotDuration: '00:10:00',
			allDaySlot: false,
			header: {
				left: 'prev,next',
				center: 'title',
				right: 'timelineDay,timelineWeek,timelineMonth'
			},
			locale: initialLocaleCode,
			resourceLabelText: 'Zaposlenici',
			selectable: true,
			eventLimit: false,
			contentHeight: "auto",
			timeFormat: 'H:m',
			selectHelper: true,
			minTime: "07:00:00",
			maxTime: "21:00:00",
			eventOverlap: false,
			resources: {
				url: '{{ route('reports.get-employee', $user->id) }}',
				type: 'GET'
			},
			events: {
				url: '{{ route('reports.get-report', $user->id) }}',
				type: 'GET'
			},
			eventClick: function(event, jsEvent, view) {
				var eventStatusName = event.status_name;
				var eventStatusId = event.status_id;
				var departureStatus = event.departure_status;
				var arrival = $.fullCalendar.formatDate(event.start, "HH:mm");
				var departure = $.fullCalendar.formatDate(event.end, "HH:mm"); 

				if (departureStatus) {
					departureTime.text(departure);
				} else {
					if (eventStatusId == 1)
						departureTime.text('Trenutno na poslu');
					else if (eventStatusId == 2)
						departureTime.text('Trenutno na pauzi');
					else if (eventStatusId == 3)
						departureTime.text('Trenutno na terenu');
				}

				eventStatus.text(eventStatusName);
				arrivalTime.text(arrival);

				displayEventModal();
			},
			dayClick: function(date, jsEvent, view, resource) {
			},
			eventDrop:function(event){
			},
			eventResize: function(event, delta, revertFunc) {
			},
			 schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source'
		});

		function displayEventModal(){
			eventModal.modal('show');
		}
	</script>
@endsection