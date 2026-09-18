@extends('app.layouts.master')

@section('title', 'Izvještaji')

@section('css')
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.css">
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap-select/bootstrap-select.min.css') }}">
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
					<ul class="list-unstyled pl-3 mb-0">
						<li>
							<strong>Status:</strong> <span id="status"></span>
						</li>
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
					<h3>Izvještaji</h3>
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

			<div class="row">
				<div class="col-md-12">
					<form action="{{ route('reports.generate') }}" method="POST">
						@csrf

						<input type="hidden" name="from" id="date-from">
						<input type="hidden" name="to" id="date-to">

						<div class="form-group row bg-transparent d-flex align-items-center pl-0 pr-0 pb-0">
							<label for="date" class="col-md-2 col-form-label text-dark pl-0">Datum</label>
						    <div class="col-md-6">
						        <input type="text" class="form-control @error('date') is-invalid @enderror" id="report-date" name="date" placeholder="Odaberite datum" value="{{ old('date') }}">
						        @error('date')
						            <span class="invalid-feedback" role="alert">
						                <strong>{{ $message }}</strong>
						            </span>
						        @enderror
						    </div>
						</div>

						<div class="form-group row bg-transparent d-flex align-items-center pl-0 pr-0 pb-0">
							<label for="date" class="col-md-2 col-form-label text-dark pl-0">Zaposlenici</label>
							<div class="col-md-6">
								<select class="selectpicker show-tick" id="role-selectpicker" name="employees[]" title="Odaberi zaposlenike" data-width="100%" data-size="6" data-live-search="true" multiple data-actions-box="true" data-selected-text-format="count > 6" multiple required>
									@foreach($employees as $employee)
										<option value="{{ $employee->id }}">{{ $employee->name }}</option>
									@endforeach
								</select>
						    </div>
						</div>

						<div class="form-group row bg-transparent pl-0 pr-0 pb-0">
							<div class="col-md-2"></div>
							<div class="col-md-6 pr-0">
						    	<button type="submit" class="btn custom-button" id="">
									Generiši izvještaj
								</button>
						    </div>
						</div>
					</form>
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
	</div>
@endsection

@section('js')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
	<script type="text/javascript" src="{{ asset('assets/js/bootstrap-select/bootstrap-select.min.js') }}"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.js"></script>
	<script src="{{ asset('assets/js/calendar/scheduler.js') }}"></script>
	<script src="{{ asset('assets/js/calendar/local.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/flatpickr/flatpickr.min.js') }}"></script>
	<script>
		var roleSelect = $('#role-selectpicker');
		var initialLocaleCode = 'bs';
		var eventModal = $('#event-modal');
		var arrivalTime = $('#arrival-time');
		var departureTime = $('#departure-time');
		var eventStatus = $('#status');
		var reportDate = $('#report-date');
		var dateFrom = $('#date-from');
		var dateTo = $('#date-to');

		roleSelect.selectpicker();

		reportDate.flatpickr({
            dateFormat: "d-m-Y",
            mode: "range",
            maxDate: "today",
            onChange: function(selectedDates, dateStr, instance){
				var from = selectedDates[0].getFullYear() + "-" + numeroAdosCaracteres(selectedDates[0].getMonth() + 1) + "-" + numeroAdosCaracteres(selectedDates[0].getDate());

				var to = selectedDates[1].getFullYear() + "-" + numeroAdosCaracteres(selectedDates[1].getMonth() + 1) + "-" + numeroAdosCaracteres(selectedDates[1].getDate());

				dateFrom.val(from);
				dateTo.val(to);
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
			eventLimit: false,
			contentHeight: "auto",
			timeFormat: 'H:m',
			selectHelper: true,
			minTime: "05:00:00",
			maxTime: "22:00:00",
			eventOverlap: false,
			resources: {
				url: '{{ route('reports.get-employees') }}',
				type: 'GET'
			},
			events: {
				url: '{{ route('reports.get-reports') }}',
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

		function numeroAdosCaracteres(fecha) {
		    if (fecha > 9)
		        return ""+fecha;
		    else
		        return "0"+fecha;
		}
	</script>
@endsection
