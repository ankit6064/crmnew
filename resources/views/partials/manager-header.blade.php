  <!--topbar-->
  <div class="topbar">
			<div class="logo">
				<img src="{{ asset('images/logo.png') }}" alt="Revvelocity Logo">
			</div>
		<div class="profile-container">
			<div class="profile-box" id="profileToggle">
				<img src="{{ asset('images/dummyiconimage.png') }}" alt="User Photo" class="profile-img">
				<span class="profile-name">{{ Auth::user()->first_name.' '.Auth::user()->last_name }}</span>
				<span class="dropdown-arrow"><i class="fas fa-chevron-right"></i></span>
			</div>
		<ul class="profile-dropdown" id="profileMenu" style="display: none;">
		<li><a href="#">Edit Profile</a></li>
			<li>
			<a class="dropdown-item" href="javascript:void(0)"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>			
					</li>
		</ul>
		</div>
	</div>
	<!--topbar-->