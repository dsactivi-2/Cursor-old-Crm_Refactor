@extends('public.layouts.master')

@section('content')
	<div class="scan">
		<div class="scan-top" style="padding: 40px 60px 20px;">
			<div class="scan-top__title">
				<h1>Dobro došli</h1>
			</div>

			<div class="scan-top__time">
				<!--<p class="time"></p> <br>-->
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
					@endswitch,
					{{ \Carbon\Carbon::now()->format('j.n.Y') }}
				</p>
			</div>
		</div>

		<div class="scan-middle">
			<form action="{{ route('scan.scan') }}" method="POST">
				
				@csrf
				
				<div class="form-group">
					<input id="rfid" class="form-control @if (session()->has('error')) is-invalid @endif" type="text" name="rfid" value="{{ old('rfid') }}" placeholder="RFID kartica" required autofocus>
					
					@if (session()->has('error'))
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ session()->get('error') }}</strong>
                        </span>
                    @endif
				</div>

			</form>
		</div>

		<div class="scan-bottom">
			<div class="scan-bottom__logo">
				<p>Powered by</p>
				<img src="{{ asset('assets/images/logo/idk-logo-white.png') }}" alt="">
			</div>

			<div class="scan-bottom__scan">
				<p>Molimo skenirajte vašu kartice ovdje</p>
				<i class="fas fa-angle-double-right"></i>
			</div>
		</div>
	</div>
@endsection

@section('js')
	<script>
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
        
        $('#rfid').bind('focusout', function(e) {
	        $(this).focus();
	    });
	    
        startTime();
	</script>
@endsection
