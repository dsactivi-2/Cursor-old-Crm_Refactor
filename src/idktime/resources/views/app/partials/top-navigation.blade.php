<div class="top-navigation">
	<div class="top-navigation__dropdown">
		<div class="dropdown dropleft d-inline">
			<button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<i class="far fa-bell notification-bell" style="font-size: 19px"></i>
			</button>
			<div class="dropdown-menu notification-dropdown" aria-labelledby="dropdownMenuButton">
				<div class="container-fluid header">
					<div class="row d-flex align-items-center">
						<div class="col-md-3">
							Notifikacije
						</div>
						<div class="col-md-9 text-right">
							<button type="button" id="read-all">Označi kao procitano</button>
						</div>
					</div>
				</div>

				<ul class="list-group notification-list-gruop">
				</ul>
			</div>
		</div>
		<div class="dropdown dropleft d-inline">
			<button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				{{ auth()->user()->name }}
			</button>
			<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
				<a class="dropdown-item" href="{{ route('my-profile') }}">Moj profil</a>
				<a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
					Odjava
                </a>

				<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
					@csrf
				</form>
			</div>
		</div>
	</div>
</div>
