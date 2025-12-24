{{-- Nav --}}
<div class="navi navi-spacer-x-0 pt-5">
    {{-- Item --}}
    <a href="{{ url('/Users') }}" class="navi-item px-8">
        <div class="navi-link">
            <div class="navi-icon mr-2">
                <i class="flaticon2-calendar-3 text-success"></i>
            </div>
            <div class="navi-text">
                <div class="font-weight-bold">
                    My Profile
                </div>
                {{-- <div class="text-muted">
                    Account settings and more
                    <span class="label label-light-danger label-inline font-weight-bold">update</span>
                </div> --}}
            </div>
        </div>
    </a>

    {{-- Footer --}}
    <div class="navi-separator"></div>
    <div class="navi-footer  px-8 py-5">
        <a href="{{ url('/main/logout') }}" class="btn btn-light-primary font-weight-bold">Log Out</a>
    </div>
</div>
