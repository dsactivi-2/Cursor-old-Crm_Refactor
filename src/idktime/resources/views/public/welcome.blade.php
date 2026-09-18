@extends('public.layouts.master')

@section('styles')
<style>
	
	@media only screen and (max-height: 600px) {
		#app main .welcome .welcome-top {
			padding: 10px 50px 0 !important;
		}

		#app main .welcome .welcome-middle {
			padding: 0px 50px !important;
		}
		
		#app main .welcome .welcome-bottom {
			padding: 0px 10px 20px !important;
		}
		
		#app main .welcome .welcome-top__time p:first-child {
			font-size: 30px !important;
		}
		
		#app main .welcome .welcome-top__time p:last-child {
			font-size: 20px !important;
			margin-bottom: 0px !important;
		}
	}
</style> 
@endsection

@section('content')
	<div class="welcome">
		<div class="welcome-top">
			<div class="welcome-top__title">
				<h1>Dobro došli</h1>
			</div>

			<div class="welcome-top__time">
				<!--<p class="time"></p> <br>-->
				<p>@switch(\Carbon\Carbon::now()->dayOfWeek)
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
					@endswitch,
					{{ \Carbon\Carbon::now()->format('j.n.Y') }}</p>
			</div>
		</div>

		<div class="welcome-middle">
			<div class="welcome-middle__user">
				<div class="welcome-middle__user--image">
					@if (!is_null($card->user->image))
					<img src="{{ asset('storage/users/' . $card->user->image) }}" alt="">
					@else
					<img src="{{ asset('assets/images/placeholders/user.png') }}" alt="">
					@endif
				</div>

				<div class="welcome-middle__user--data">
					<h3>{{ $card->user->name }}</h3>
					<h5>{{ $card->user->position->name ?? 'Nije dodijeljena radna pozicija' }}</h5>
				</div>
			</div>

			<div class="welcome-middle__time">
				<p>Vrijeme skeniranja</p>
				<p>{{ \Carbon\Carbon::now()->format('H:i') }}</p>
				<p>@switch(\Carbon\Carbon::now()->dayOfWeek)
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
					@endswitch,
					{{ \Carbon\Carbon::now()->format('j.n.Y') }}</p>
			</div>
		</div>
		
		<div class="welcome-bottom">
			@if (!is_null($latest_timelog))
				@if ($latest_timelog->action_id != 2 && $latest_timelog->action_id != 3 && $latest_timelog->action_id != 4)
				<div class="welcome-bottom__header">
					<p>Odaberite razlog</p>
				</div>
				@endif
			@endif

			<div class="welcome-bottom__form">
				<form action="{{ route('scan.action') }}" method="POST" id="action-form">
					@csrf
					
					<input type="hidden" name="card_id" value="{{ $card->id }}">

					@if (is_null($latest_timelog))
						<input type="hidden" name="action_id" value="1">
					@else
						@if ($latest_timelog->action_id == 2 || $latest_timelog->action_id == 3)
						<input type="hidden" name="action_id" value="5">
						@elseif ($latest_timelog->action_id == 4)
						<input type="hidden" name="action_id" value="1">
						@else
							@foreach ($actions as $action)
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="action_id" id="action_{{ $action->id }}" value="{{ $action->id }}">
									<label class="form-check-label" for="action_{{ $action->id }}">{{ $action->name }}</label>
								</div>
							@endforeach
						@endif
					@endif
				</form>
			</div>
		</div>
	</div>
@endsection


@section('js')
	<script>
		var actionForm = $('#action-form');
		var radios = $('.form-check-input');
		var latestTimelogActionStatus = {{ $timelog_action }}

		radios.on('click', function() {
			actionForm.submit();
		});

		if (latestTimelogActionStatus == 1) {
			setTimeout(function() {
				actionForm.submit();
			}, 3000);
		}

		function startTime() {
            var today = new Date();
            var h = today.getHours();
            var m = today.getMinutes();
            m = checkTime(m);
            document.querySelector('.time').innerHTML = h + ":" + m;
            var t = setTimeout(startTime, 500);
        }

        function checkTime(i) {
            if (i < 10) {i = "0" + i};
            return i;
        }

        startTime();
	</script>
@endsection