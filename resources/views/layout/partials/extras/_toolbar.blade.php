{{-- Sticky Toolbar --}}

<ul class="sticky-toolbar nav flex-column pl-2 pr-2 pt-3 pb-3 mt-4" name="stickyToolBar" id="stickyToolBar">
    <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary mb-2" name="closeSTB" id="closeSTB" data-toggle="tooltip" title="Tutup" data-placement="top">
        <i class="ki ki-bold-close icon-xs text-muted"></i>
    </span>
    {{-- Item --}}
    <li class="nav-item mb-2" data-toggle="tooltip" title="Penjualan" data-placement="left">
        <a class="btn btn-sm btn-icon btn-bg-light btn-icon-primary btn-hover-warning" href="{{url('/SalesOrder/Add')}}">
            <i class="fas fa-file-medical"></i>
        </a>
    </li>

    {{-- Item --}}
    <li class="nav-item mb-2" data-toggle="tooltip" title="Pengiriman" data-placement="left">
        <a class="btn btn-sm btn-icon btn-bg-light btn-icon-success btn-hover-warning" href="{{url('/Delivery/Add')}}">
            <i class="flaticon2-delivery-truck"></i>
        </a>
    </li>

    {{-- Item --}}
    <li class="nav-item mb-2" data-toggle="tooltip" title="Invoice Penjualan" data-placement="left">
        <a class="btn btn-sm btn-icon btn-bg-light btn-icon-warning btn-hover-warning" href="{{url('/SalesInvoice/Add')}}">
            <i class="fas fa-file-invoice"></i>
        </a>
    </li>

    {{-- Item --}}
    <li class="nav-item mb-2" data-toggle="tooltip" title="Tukar Faktur" data-placement="left">
        <a class="btn btn-sm btn-icon btn-bg-light btn-icon-danger btn-hover-warning" href="{{url('/SalesInvoiceCollection/Add')}}">
            <i class="flaticon2-calendar-5"></i>
        </a>
    </li>

    {{-- Item --}}
    <li class="nav-item mb-2" data-toggle="tooltip"  title="Purchase Order" data-placement="left">
        <a class="btn btn-sm btn-icon btn-bg-light btn-icon-primary btn-hover-primary" href="{{url('/PurchaseOrder/Add')}}">
            <i class="fas fa-cart-plus"></i>
        </a>
    </li>

    {{-- Item --}}
    <li class="nav-item mb-2" data-toggle="tooltip"  title="Penerimaan Barang" data-placement="right">
        <a class="btn btn-sm btn-icon btn-bg-light btn-icon-success btn-hover-success" href="{{url('/Receiving/Add')}}">
            <i class="flaticon-bag"></i>
        </a>
    </li>

    {{-- Item --}}
    <li class="nav-item" data-toggle="tooltip"  title="Invoice Pembelian" data-placement="right">
        <a class="btn btn-sm btn-icon btn-bg-light btn-icon-warning btn-hover-warning" href="{{url('/PurchaseInvoice/Add')}}">
            <i class="la la-file-invoice-dollar"></i>
        </a>
    </li>
</ul>
