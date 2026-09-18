@extends('app.layouts.master')

@section('title', 'Kontrolna ploča')

@section('css')
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/bootstrap-select/bootstrap-select.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/calendario/calendar.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/calendario/custom_2.css') }}">
@endsection

@section('content')
	<div class="container-fluid">
		<div class="app-header">
			<div class="row d-flex align-items-center">
				<div class="col-lg-6 col-md-12 col-sm-12 app-header__title">
					<h4>Naslovnica</h4>
				</div>
			</div>
		</div>

		<div class="app-content">
			<div class="row">
				<div class="col-lg-4 col-md-12 mb-3">
					<div class="statistic-card bg-white rounded shadow-sm p-3 text-center">
						<div class="statistic-card__title">
							<h1 class="statistic-card__title--clock">
								{{ \Carbon\Carbon::now()->format('H:i') }}
							</h1>
						</div>
						<div class="statistic-card__day">
							<p>
							@switch(\Carbon\Carbon::now()->dayOfWeek)
							    @case(1)
							        Ponedjeljak
							        @break
							    @case(2)
							        Utorak
							        @break
							    @case(3)
							        Srijeda
							        @break
							    @case(4)
							        Četvrtak
							        @break
							    @case(5)
							        Petak
							        @break
							    @case(6)
							        Subota
							        @break
							    @case(7)
							        Nedjelja
							        @break
							@endswitch
							, {{ \Carbon\Carbon::now()->format('d.m.Y') }}
							</p>
						</div>
						<div class="statistic-card__forecast">
							<a class="weatherwidget-io" href="https://forecast7.com/hr/40d71n74d01/new-york/" data-label_1="Bihać" data-label_2="Vrijeme" data-mode="Current" data-days="3" data-theme="original" data-basecolor="#ffffff" data-textcolor="#000000" data-mooncolor="#000000" data-cloudcolor="#000000" data-cloudfill="rgba(255, 255, 255, 0.14)" ></a>
							<script>
								!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src='https://weatherwidget.io/js/widget.min.js';fjs.parentNode.insertBefore(js,fjs);}}(document,'script','weatherwidget-io-js');
							</script>
						</div>
					</div>
				</div>

				<div class="col-lg-4 col-md-12 mb-3">
					<div class="statistic-card bg-white rounded shadow-sm p-3">
						<div class="statistic-card__title">
							<h5 class="text-muted mb-0">Trenutno stanje</h5>
						</div>

						<div class="statistic-card__data">
							<ul>
								<li>
									Na poslu: <span class="text-primary">{{ $working }}</span>
								</li>
								<li>
									Na pauzi: <span class="text-warning">{{ $pause }}</span>
								</li>
								<li>
									Na terenu: <span class="text-info">{{ $on_the_field }}</span>
								</li>
								<li>
									Van posla: <span class="text-danger">{{ $finished }}</span>
								</li>
							</ul>
						</div>
					</div>
				</div>

				<div class="col-lg-4 col-md-12 mb-3">
					<div class="statistic-card bg-white rounded shadow-sm">
						<div class="custom-calendar-wrap">
							<div id="custom-inner" class="custom-inner">
								<div class="custom-header clearfix">
									<nav>
										<span id="custom-prev" class="custom-prev"></span>
										<span id="custom-next" class="custom-next"></span>
									</nav>
									<h2 id="custom-month" class="custom-month"></h2>
									<h3 id="custom-year" class="custom-year"></h3>
								</div>
								<div id="calendar" class="fc-calendar-container"></div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-lg-6 col-md-12 mb-3">
					<div class="statistic-card bg-white rounded shadow-sm p-3">
						<div class="statistic-card__option-title mb-4">
							<h5 class="title text-muted mb-0">Status zaposlenika</h5>
							<div class="options">
								<select class="selectpicker" id="user-work-status-selectpicker" data-width="100%">
  									<option value="1" data-content="<span class='badge badge-primary'>Na poslu</span>">Na poslu</option>
  									<option value="2" data-content="<span class='badge badge-warning'>Na pauzi</span>">Na pauzi</option>
  									<option value="3" data-content="<span class='badge badge-info'>Na terenu</span>">Na terenu</option>
  									<option value="4" data-content="<span class='badge badge-danger'>Van posla</span>">Van posla</option>
								</select>
							</div>
						</div>

						<div class="statistic-card__timelogs">
							@forelse ($timelogs as $timelog)
								<div class="timelog">
									{{-- <div class="image">
										@if (!is_null($timelog->user->image))
										<img src="{{ asset('storage/users/' . $timelog->user->image) }}" alt="">
										@else
										<img src="{{ asset('assets/images/placeholders/user.png') }}" alt="">
										@endif
									</div> --}}
									<div class="data">
										<h3>{{ $timelog->user->name ?? '-' }}</h3>
										<p>{{ $timelog->user->department->name ?? '-' }}</p>
									</div>
								</div>
							@empty
							<p class='text-muted mb-0 text-center'>Nisu pronađeni rezultati.</p>
							@endforelse
						</div>
					</div>
				</div>

				<div class="col-lg-6 col-md-12 mb-3">
					<div class="statistic-card bg-white rounded shadow-sm p-3">
						<div class="statistic-card-title">
							<h5 class="text-muted mb-0">Izvještaji</h5>
						</div>
						<div class="statistic-card__data">
							<ul class="list-group list-group-flush pl-0">

							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('js')
	<script type="text/javascript" src="{{ asset('assets/js/bootstrap-select/bootstrap-select.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/bootstrap-select/bootstrap-select.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/calendario/modernizr.custom.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/calendario/jquery.calendario.js') }}"></script>
	<script>
		var userWorkStatusSelectpicker = $('#user-work-status-selectpicker');
		var timelogsContainer = $('.statistic-card__timelogs');

		userWorkStatusSelectpicker.on('change', function() {
			var selectedOption = $(this).find(':selected');
			filterReports(selectedOption.val());
		});

		function filterReports(status){
			$.ajax({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                url: '{{ route('reports.filter') }}',
                type: 'POST',
                data: {'status': status},
                success: function(response){

                	var responseLength = response.length;
                	var timelogs = response;
                	console.log(timelogs);
                	timelogsContainer.empty();

                   	if (responseLength == 0) {
                   		timelogsContainer.append( "<p class='text-muted mb-0 text-center'>Nisu pronađeni rezultati.</p>" );
                   	} else {
                   		$.each( timelogs, function( key, timelog ) {
                   			if (timelog.user.image) {
                   				var image = `<img src="http://localhost:8000/storage/users/` + timelog.user.image + `" alt="">`;
                   			} else {
                   				var image = `<img src="http://localhost:8000/assets/images/placeholders/user.png" alt="">`;
                   			}

                   			if (timelog.user.department) {
                   				var departmentName = timelog.user.department.name;
                   			} else {
                   				var departmentName = '-';
                   			}

	                   		var timelog = `
								<div class="timelog">
									<div class="image">
										` + image + `
									</div>
									<div class="data">
										<h3>` + timelog.user.name + `</h3>
										<p>` + departmentName + `</p>
									</div>
								</div>
	                   		`;

	                   		timelogsContainer.append(timelog);
                   		});
                   	}
                }
            });
		}

		var transEndEventNames = {
			'WebkitTransition' : 'webkitTransitionEnd',
			'MozTransition' : 'transitionend',
			'OTransition' : 'oTransitionEnd',
			'msTransition' : 'MSTransitionEnd',
			'transition' : 'transitionend'
		},
		transEndEventName = transEndEventNames[ Modernizr.prefixed( 'transition' ) ],
		$wrapper = $( '#custom-inner' ),
		$calendar = $( '#calendar' ),
		cal = $calendar.calendario( {
			onDayClick : function( $el, $contentEl, dateProperties ) {

				var day, month;

				if (dateProperties.day < 10)
					day = ('0' + dateProperties.day).slice(-2);
				else
					day = dateProperties.day;


				if (dateProperties.month < 10)
					month = ('0' + dateProperties.month).slice(-2);
				else
					month = dateProperties.month;

				window.location.href = "/reports/" + dateProperties.year + "-" + month + "-" + day;

			},
			displayWeekAbbr : true
		} ),
		$month = $( '#custom-month' ).html( cal.getMonthName() ),
		$year = $( '#custom-year' ).html( cal.getYear() );

		$( '#custom-next' ).on( 'click', function() {
			cal.gotoNextMonth( updateMonthYear );
		} );
		$( '#custom-prev' ).on( 'click', function() {
			cal.gotoPreviousMonth( updateMonthYear );
		} );

		function updateMonthYear() {
			$month.html( cal.getMonthName() );
			$year.html( cal.getYear() );
		}

	</script>
@endsection
