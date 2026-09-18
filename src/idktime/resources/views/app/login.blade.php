@extends('app.layouts.login')

@section('content')
	<div class="container">

		<div class="row">
			<div class="col-md-12 d-flex justify-content-center align-items-center">
				<img class="mr-4" src="{{ asset('assets/images/logo/idk-logo.png') }}" alt="">
			</div>
		</div>

		<div class="row mt-4 mb-3">
			<div class="col-md-12">
				<div class="form-wrapper bg-white mx-auto">
					
					@include('app.partials.success')
					@include('app.partials.error')

					<h3>Prijavi se</h3>

					<form method="POST" action="{{ route('login') }}" class="clearfix mt-4">
						
						@csrf
						
						<div class="form-group">
							<label for="email" class="col-form-label mb-2 block">E-mail</label>

							<input id="email" class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="E-mail" autofocus>

							@error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
						</div>

						<div class="form-group">
							<label for="password" class="col-form-label mb-2 block">Lozinka</label>

							<input id="password" class="form-control @error('password') is-invalid @enderror" type="password" name="password" value="" required autocomplete="current-password" placeholder="Lozinka" autofocus>

							@error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
						</div>

						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" name="remember" id="remember">
							<label class="custom-control-label" for="remember">Zapamti me</label>
						</div>

						<div class="form-group float-right mt-4">
							<button class="btn custom-button" type="submit">Prijavi se</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12 text-center">
				<p class="text-muted">
					<i class="far fa-copyright"></i>2019 Sva prava pridržana
				</p>
			</div>
		</div>
	</div>
@endsection