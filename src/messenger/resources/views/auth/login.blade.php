@extends('layouts.app')

@section('content')
<script>
$(document).ready(function() {
	$("input").focus(function() {
		$('footer').hide('slow');
	});


	$("input").blur(function(){
		$('footer').show('slow');
	});
});
</script>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right"><i class="fa fa-envelope-o" aria-hidden="true"></i>{{ __('hometext.korIme') }} </label>

                            <div class="col-md-6">
                                <input id="email" type="text" class="form-control @error('email') is-invalid @enderror login_input" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder = "">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right"><i class="fa fa-key" aria-hidden="true"></i>{{ __('Pin') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror login_input" name="password" required autocomplete="current-password"  placeholder = "">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row mb-1">
                            <div class="col-md-8 offset-md-2 text-center">
                                <button type="submit" class="btn btn-primary login_potvrda">
                                    {{ __('Login') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<footer>
	<p>{{ config('app.name') }} - All right reserved - 2019 <br><a href="https://crm.job-step.com/messenger/privacypolicy.html" target="_blank" style="color: rgb(100, 154, 163); text-decoration: none; font-weight: bold;">Privacy Policy</a></p>
</footer>
@endsection
