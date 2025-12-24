{{-- Extends layout --}}
@extends('layout.default')

{{-- Content --}}
@section('content')

    {{-- Dashboard 1 --}}

    <div class="row">
        @include('pages.alerts')
        <div class="col-lg-8">
            @if(Auth::user()->user_group == "super_admin" || Auth::user()->user_group == "admin" || Auth::user()->user_group == "penjualan")
                @include('pages.widgets._widget-2', ['class' => 'card-stretch gutter-b'])
            @endif
        </div>

        <div class="col-lg-4">
            @if(Auth::user()->user_group == "super_admin" || Auth::user()->user_group == "admin")
                @include('pages.widgets._widget-1', ['class' => 'card-stretch gutter-b'])
            @endif
        </div>

    </div>

    {{-- <div class="row">

        <div class="col-lg-8">
            @include('pages.widgets._widget-3', ['class' => 'card-stretch card-stretch-half gutter-b'])
            @include('pages.widgets._widget-4', ['class' => 'card-stretch card-stretch-half gutter-b'])
        </div>

        <div class="col-lg-8 order-1 order-xxl-1">
            @include('pages.widgets._widget-5', ['class' => 'card-stretch gutter-b'])
        </div>

        <div class="col-xxl-8 order-2 order-xxl-1">
            @include('pages.widgets._widget-6', ['class' => 'card-stretch gutter-b'])
        </div>

        <div class="col-lg-8 order-1 order-xxl-2">
            @include('pages.widgets._widget-7', ['class' => 'card-stretch gutter-b'])
        </div>

        <div class="col-lg-8 order-1 order-xxl-2">
            @include('pages.widgets._widget-8', ['class' => 'card-stretch gutter-b'])
        </div>

        <div class="col-lg-12 col-xxl-4 order-1 order-xxl-2">
            @include('pages.widgets._widget-9', ['class' => 'card-stretch gutter-b'])
        </div>

    </div> --}}

@endsection

{{-- Scripts Section --}}
@section('scripts')
    <script src="{{ asset('js/pages/widgets.js') }}" type="text/javascript"></script>
@endsection
