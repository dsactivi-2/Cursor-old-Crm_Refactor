@extends('app.layouts.master')

@section('title', 'Novi odjel')

@section('content')
	
	<div class="container pb-4">
		<div class="app-header">
			<div class="row d-flex align-items-center">
				<div class="col-md-6 col-sm-12 app-header__title">
					<h3>Novi Odjel</h3>
				</div>	

				<div class="col-md-6 col-sm-12 app-header__add-btn">
					<a class="btn custom-button" href="{{ route('departments.index') }}">
						<i class="fas fa-chevron-left"></i>
					</a>
				</div>
			</div>
		</div>

		<div class="app-content form-content shadow">
			<form method="POST" action="{{ route('departments.store') }}">

				@csrf
				
				<div class="form-group row">
				    <label for="name" class="col-md-3 col-form-label">Ime *</label>
				    <div class="col-md-6">
				        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Ime službe" value="{{ old('name') }}" required>
				        @error('name')
				            <span class="invalid-feedback" role="alert">
				                <strong>{{ $message }}</strong>
				            </span>
				        @enderror
				    </div>
				</div>
          
                 <div class="form-group btn-form-group text-right">
				    <button type="submit" class="btn custom-button" id="">
						Dodaj odjel
					</button>
				</div>
			</form>
		</div>
	</div>
@endsection