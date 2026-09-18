<div class="side-navigation">
	<div class="side-navigation__header desktop-header d-flex justify-content-center align-items-center">
		<img src="{{ asset('assets/images/logo/idk-logo-white.png') }}" alt="">
	</div>
	<div class="side-navigation__header d-none justify-content-center align-items-center mobile-header">
		<img src="{{ asset('assets/images/logo/idk-logo-white-small.png') }}" alt="">
	</div>

	<div class="side-navigation__body pt-4">
		<nav>
			<ul>
				<li class="{{ Request::is('dashboard') ? 'active' : '' }}">
					<a href="{{ route('dashboard') }}">
						<i class="fas fa-tachometer-alt"></i>
						<span>Naslovnica</span>
					</a>
				</li>
				@if (auth()->user()->user_role_id == 1)
				<li class="{{ Request::is('users', 'users/*') ? 'active' : '' }}">
					<a href="{{ route('users.index') }}">
						<i class="fas fa-users"></i>
						<span>Zaposlenici</span>
					</a>
				</li>
				<li class="{{ Request::is('departments', 'departments/*') ? 'active' : '' }}">
					<a href="{{ route('departments.index') }}">
						<i class="far fa-building"></i>
						<span>Odjeli</span>
					</a>
				</li>
				<li class="{{ Request::is('positions', 'positions/*') ? 'active' : '' }}">
					<a href="{{ route('positions.index') }}">
						<i class="fas fa-briefcase"></i>
						<span>Radne pozicije</span>
					</a>
				</li>
				<li class="{{ Request::is('devices', 'devices/*') ? 'active' : '' }}">
					<a href="{{ route('devices.index') }}">
						<i class="fas fa-tablet-alt"></i>
						<span>Uređaji</span>
					</a>
				</li>
				@endif
				@if (auth()->user()->user_role_id == 1 || auth()->user()->permissions()->where('slug' , 'izvjestaji')->first())
				<li class="{{ Request::is('reports') ? 'active' : '' }}">
					<a href="{{ route('reports.index') }}">
						<i class="far fa-file-alt"></i>
						<span>Izvještaji</span>
					</a>
				</li>
				@endif
				@if (auth()->user()->user_role_id == 1 || auth()->user()->permissions()->where('slug' , 'obavjestenja')->first())
				<li class="{{ Request::is('notifications') ? 'active' : '' }}">
					<a href="{{ route('notifications.index') }}">
						<i class="fas fa-bell"></i>
						<span>Obavještenja</span>
					</a>
				</li>
				@endif
				@if (auth()->user()->user_role_id == 1 || auth()->user()->permissions()->where('slug' , 'rfid-kartice')->first())
				<li class="{{ Request::is('cards', 'cards/*') ? 'active' : '' }}">
					<a href="{{ route('cards.index') }}">
						<i class="fas fa-id-card-alt"></i>
						<span>RFID Kartice</span>
					</a>
				</li>
				@endif
				@if (auth()->user()->user_role_id == 1 || auth()->user()->permissions()->where('slug' , 'podesavanja')->first())
				<li class="{{ Request::is('settings', 'settings/*') ? 'active' : '' }}">
					<a href="{{ route('settings.index') }}">
						<i class="fas fa-cogs"></i>
						<span>Podešavanja</span>
					</a>
				</li>
				@endif
			</ul>
		</nav>
	</div>
</div>
