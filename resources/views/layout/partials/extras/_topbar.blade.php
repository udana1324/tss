{{-- Topbar --}}
<div class="topbar">
    {{-- User --}}
    @if (config('layout.extras.user.display'))
        @if (config('layout.extras.user.layout') == 'offcanvas')
            <div class="dropdown" id="kt_quick_search_toggle">
				<!--begin::Toggle-->
				<div class="topbar-item" data-toggle="dropdown" data-offset="10px,0px" aria-expanded="false">
					<div class="mr-10 text-white">
						<h2>
						    {{env('APP_NAME')}}
						</h2>
					</div>
				</div>
				<!--end::Toggle-->
			</div>
        
            <div class="topbar-item">
                <div class="dropdown dropdown-inline" data-placement="left">
                    <a href="#" class="btn btn-icon w-auto btn-clean d-flex align-items-center btn-lg px-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">Selamat datang,</span>
                        <span class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3">{{ucwords(Auth::user()->user_name)}}</span>
                        <span class="symbol symbol-35 symbol-light-success">
                        <span class="symbol-label font-size-h5 font-weight-bold">{{strtoupper(substr(Auth::user()->user_name,0,1))}}</span>
                        </span>
                    </a>
                    <div class="dropdown-menu p-0 m-0 dropdown-menu-md dropdown-menu-right">
                        {{-- Navigation --}}
                        <ul class="navi navi-hover">
                            <li class="navi-item">
                            <a href="{{url('/Users')}}" class="navi-link">
                                    <span class="navi-icon"><i class="flaticon2-user"></i></span>
                                    <span class="navi-text">My Profile</span>
                                </a>
                            </li>
                            <li class="navi-separator mb-3"></li>
                            <li class="navi-item">
                                <a href="{{url('/main/logout')}}" class="navi-link">
                                    <span class="navi-icon"><i class="flaticon-logout"></i></span>
                                    <span class="navi-text">Logout</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        @else
            <div class="dropdown">
                <div class="dropdown dropdown-inline" data-placement="left">
                    <a href="#" class="btn btn-icon w-auto btn-clean d-flex align-items-center btn-lg px-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1">Selamat datang,</span>
                        <span class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3">{{ucwords(Auth::user()->user_name)}}</span>
                        <span class="symbol symbol-35 symbol-light-success">
                        <span class="symbol-label font-size-h5 font-weight-bold">{{strtoupper(substr(Auth::user()->user_name,0,1))}}</span>
                        </span>
                    </a>
                    <div class="dropdown-menu p-0 m-0 dropdown-menu-md dropdown-menu-right">
                        {{-- Navigation --}}
                        <ul class="navi navi-hover">
                            <li class="navi-header font-weight-bold">
                                Jump to:
                                <i class="flaticon2-information" data-toggle="tooltip" data-placement="right" title="Click to learn more..."></i>
                            </li>
                            <li class="navi-separator mb-3"></li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-icon"><i class="flaticon2-drop"></i></span>
                                    <span class="navi-text">Recent Orders</span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-icon"><i class="flaticon2-calendar-8"></i></span>
                                    <span class="navi-text">Support Cases</span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-icon"><i class="flaticon2-telegram-logo"></i></span>
                                    <span class="navi-text">Projects</span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-icon"><i class="flaticon2-new-email"></i></span>
                                    <span class="navi-text">Messages</span>
                                    <span class="navi-label">
                                        <span class="label label-success label-rounded">5</span>
                                    </span>
                                </a>
                            </li>
                            <li class="navi-separator mt-3"></li>
                            <li class="navi-footer">
                                <a class="btn btn-light-primary font-weight-bolder btn-sm" href="#">Upgrade plan</a>
                                <a class="btn btn-clean font-weight-bold btn-sm" href="#" data-toggle="tooltip" data-placement="right" title="Click to learn more...">Learn more</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
