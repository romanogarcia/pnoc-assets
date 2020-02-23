          <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
            <div class="navbar-brand-wrapper d-flex justify-content-center">
               <div class="navbar-brand-inner-wrapper d-flex justify-content-between align-items-center w-100">  
                  <a class="navbar-brand brand-logo" href="{{route('home')}}"><img src="{{@asset('images/barcode-logo.gif')}}" alt="logo" style="height: 50px; width: 43px;" /></a>
                  <a class="navbar-brand brand-logo-mini" href="{{route('home')}}"><img src="{{@asset('images/pnoc.ico')}}" alt="logo" style="height: 33px;"/></a>
                  <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                  <span class="mdi mdi-sort-variant"></span>
                  </button>
               </div>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
               <ul class="navbar-nav navbar-nav-right">
                  <li class="nav-item nav-profile" title="Current Time/Date" style="cursor: default;">
                     <span style="color: #333;" id="top_nav-current_realtime_date"></span>
                  </li>
                  <li class="nav-item nav-profile dropdown">
                     <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                     <img src="{{Utility::get_current_user_photo()}}" alt="profile"/>
                     <span class="nav-profile-name">{{ auth()->user()->username }}</span>
                     </a>
                     <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                        <a class="dropdown-item" href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="mdi mdi-logout text-primary"></i>
                        Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                              @csrf
                        </form>
                     </div>
                  </li>
               </ul>
               <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
               <span class="mdi mdi-menu"></span>
               </button>
            </div>
          </nav>