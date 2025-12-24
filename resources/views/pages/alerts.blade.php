@if ($message = Session::get('primary'))
<div class="alert alert-custom alert-primary" role="alert">
    <div class="alert-icon">
        <i class="flaticon-warning"></i>
    </div>
    <div class="alert-text">{{ $message }}</div>
    <div class="alert-close">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true"><i class="ki ki-close"></i></span>
        </button>
    </div>
</div>
@endif
@if ($message = Session::get('secondary'))
<div class="alert alert-custom alert-secondary" role="alert">
    <div class="alert-icon">
        <i class="flaticon-questions-circular-button"></i>
    </div>
    <div class="alert-text">{{ $message }}</div>
    <div class="alert-close">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true"><i class="ki ki-close"></i></span>
        </button>
    </div>
</div>
@endif
@if ($message = Session::get('success'))
<div class="alert alert-custom alert-success" role="alert">
    <div class="alert-icon">
        <i class="flaticon-warning"></i>
    </div>
    <div class="alert-text">{{ $message }}</div>
    <div class="alert-close">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true"><i class="ki ki-close"></i></span>
        </button>
    </div>
</div>
@endif
@if ($message = Session::get('danger'))
<div class="alert alert-custom alert-danger" role="alert">
    <div class="alert-icon">
        <i class="flaticon-questions-circular-button"></i>
    </div>
    <div class="alert-text">{!! $message !!}</div>
    <div class="alert-close">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true"><i class="ki ki-close"></i></span>
        </button>
    </div>
</div>
@endif
@if ($message = Session::get('warning'))
<div class="alert alert-custom alert-warning" role="alert">
    <div class="alert-icon">
        <i class="flaticon-warning"></i>
    </div>
    <div class="alert-text">{{ $message }}</div>
    <div class="alert-close">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true"><i class="ki ki-close"></i></span>
        </button>
    </div>
</div>
@endif
@if ($message = Session::get('info'))
<div class="alert alert-custom alert-info" role="alert">
    <div class="alert-icon">
        <i class="flaticon-questions-circular-button"></i>
    </div>
    <div class="alert-text">{{ $message }}</div>
    <div class="alert-close">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true"><i class="ki ki-close"></i></span>
        </button>
    </div>
</div>
@endif
@if ($message = Session::get('white'))
<div class="alert alert-custom alert-white" role="alert">
    <div class="alert-icon">
        <i class="flaticon-warning"></i>
    </div>
    <div class="alert-text">{{ $message }}</div>
    <div class="alert-close">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true"><i class="ki ki-close"></i></span>
        </button>
    </div>
</div>
@endif
@if ($message = Session::get('dark'))
<div class="alert alert-custom alert-dark" role="alert">
    <div class="alert-icon">
        <i class="flaticon-questions-circular-button"></i>
    </div>
    <div class="alert-text">{{ $message }}</div>
    <div class="alert-close">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true"><i class="ki ki-close"></i></span>
        </button>
    </div>
</div>
@endif
@if ($errors->any())
    @foreach($errors->all() as $error)
    <div class="alert alert-custom alert-danger" role="alert">
        <div class="alert-icon">
            <i class="flaticon-questions-circular-button"></i>
        </div>
        <div class="alert-text">{{ucwords($error)}}</div>
        <div class="alert-close">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="ki ki-close"></i></span>
            </button>
        </div>
    </div>
    @endforeach
@endif
