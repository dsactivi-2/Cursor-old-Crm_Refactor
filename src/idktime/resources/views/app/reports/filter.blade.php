@extends('app.layouts.master')

@section('title', 'Izvještaji')

@section('css')
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.css">
	<link rel="stylesheet" href="{{ asset('assets/css/calendar/scheduler.css') }}">
@endsection

@section('content')

	<div class="container">

		<div class="app-header">
			<div class="row d-flex align-items-center">
				<div class="col-md-6 col-sm-12 app-header__title">
					<h3>Izvještaji - {{ $date }}</h3>
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
	<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.js"></script>
	<script src="{{ asset('assets/js/calendar/scheduler.js') }}"></script>
	<script src="{{ asset('assets/js/calendar/local.js') }}"></script>
	<script>
		var initialLocaleCode = 'bs';
		var date = '{{ $date }}'

		console.log(date);
		$('#calendar').fullCalendar({
			themeSystem: 'bootstrap4',
			defaultView: 'timeline',
			slotDuration: '00:10:00',
			allDaySlot: false,
			header: {
				left: '',
				center: 'title',
				right: ''
			},
			locale: initialLocaleCode,
			resourceLabelText: 'Zaposlenici',
			selectable: true,
			editable: true,
			eventLimit: false,
			contentHeight: "auto",
			timeFormat: 'H:m',
			selectHelper: true,
			minTime: "07:00:00",
			maxTime: "21:00:00",
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
			},
			dayClick: function(date, jsEvent, view, resource) {
			},
			eventDrop:function(event){
			},
			eventResize: function(event, delta, revertFunc) {
			},
			 schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source'
		});

		$("#calendar").fullCalendar( 'gotoDate', date );
		
	</script>
@endsection