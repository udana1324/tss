@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header card-header-tabs-line">
                        <div class="card-toolbar">
                            <ul class="nav nav-tabs nav-bold nav-tabs-line">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#tab_pane_1">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Sales Order</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab_pane_2" id="tab2">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Delivery</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab_pane_3" id="tab3">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Invoice</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
					</div>

					<div class="card-body">
						<form action="" method="POST" id="form_add">
						{{ csrf_field() }}
						<div class="tab-content">
							<div class="tab-pane fade show active" id="tab_pane_1" role="tabpanel" aria-labelledby="tab_pane_1">
								<!--begin: Search Form-->
                                <!--begin::Search Form-->
                                <div class="mb-7">
                                    <div class="row align-items-center">
                                        <div class="col-lg-6">
                                            <div class="row align-items-center">
                                                <div class="col-md-4 my-2 my-md-0">
                                                    <div class="input-icon">
                                                        <input type="text" class="form-control" placeholder="Search..." id="list_so_search_query"/>
                                                        <span>
                                                            <i class="flaticon2-search-1 text-muted"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Search Form-->
                                <!--end: Search Form-->
                                <!--begin: Datatable-->

                                <div class="datatable datatable-bordered datatable-head-custom" id="list_so"></div>

                                <!--end: Datatable-->
							</div>

							<div class="tab-pane fade" id="tab_pane_2" role="tabpanel" aria-labelledby="tab_pane_2">
								<!--begin: Search Form-->
                                <!--begin::Search Form-->
                                <div class="mb-7">
                                    <div class="row align-items-center">
                                        <div class="col-lg-6">
                                            <div class="row align-items-center">
                                                <div class="col-md-4 my-2 my-md-0">
                                                    <div class="input-icon">
                                                        <input type="text" class="form-control" placeholder="Search..." id="list_do_search_query"/>
                                                        <span>
                                                            <i class="flaticon2-search-1 text-muted"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Search Form-->
                                <!--end: Search Form-->
                                <!--begin: Datatable-->

                                <div class="datatable datatable-bordered datatable-head-custom" id="list_do"></div>

                                <!--end: Datatable-->
                            </div>

                            <div class="tab-pane fade" id="tab_pane_3" role="tabpanel" aria-labelledby="tab_pane_3">
								<!--begin: Search Form-->
                                <!--begin::Search Form-->
                                <div class="mb-7">
                                    <div class="row align-items-center">
                                        <div class="col-lg-6">
                                            <div class="row align-items-center">
                                                <div class="col-md-4 my-2 my-md-0">
                                                    <div class="input-icon">
                                                        <input type="text" class="form-control" placeholder="Search..." id="list_invoice_search_query"/>
                                                        <span>
                                                            <i class="flaticon2-search-1 text-muted"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Search Form-->
                                <!--end: Search Form-->
                                <!--begin: Datatable-->

                                <div class="datatable datatable-bordered datatable-head-custom" id="list_invoice"></div>

                                <!--end: Datatable-->
							</div>
						</div>

						<div class="card-footer bg-white d-sm-flex justify-content-sm-between align-items-sm-center">
							<div class="btn-group">
                                <button type="button" class="btn btn-light-danger font-weight-bold mr-2" id="cancel">Keluar <i class="flaticon2-cancel icon-sm"></i></button>
                            </div>
						</div>
						</form>
					</div>
				</div>
			<!-- /content area -->
@endsection
@section('scripts')
    <script type="text/javascript">

		function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        function formatDate(strDate) {
            var arrMonth = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            var date = new Date(strDate);
            var day = date.getDate();
            var month = date.getMonth();
            var year = date.getFullYear();

            return day + ' ' + arrMonth[month] + ' ' + year;
        }

		$("#cancel").on('click', function(e) {
            window.location.href = "{{ url('/Customer') }}";
	    });

        $(document).ready(function() {
			var listSO = @json($dataSalesOrder);
            var datatable = $('#list_so').KTDatatable({
                data: {
                    type: 'local',
                    source: listSO,
                    pageSize: 10,
                    saveState : false,
                },

                layout: {
                    scroll: true,
                    height: 'auto',
                    footer: false
                },

                sortable: true,

                filterable: true,

                pagination: true,

                search: {
                    input: $('#list_so_search_query')
                },

                columns: [
                    {
                        field: 'id',
                        title: '#',
                        sortable: false,
                        width: 20,
                        type: 'number',
                        selector: false,
                        textAlign: 'center',
                        visible:false,
                    },
                    {
                        field: 'no_so',
                        title: '#Pesanan',
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            return row.no_so.toUpperCase();
                        },
                    },
                    {
                        field: 'nama_customer',
                        title: 'Pelanggan',
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            return ucwords(row.nama_customer);
                        },
                    },
                    {
                        field: 'no_po_customer',
                        title: '#PO Pelanggan',
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            if (row.no_po_customer != null) {
                                return row.no_po_customer.toUpperCase();
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'jumlah_total_so',
                        title: 'Jumlah',
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            return parseFloat(row.jumlah_total_so).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'tanggal_so',
                        title: 'Tanggal Penjualan',
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            if (row.tanggal_so != null) {
                                return formatDate(row.tanggal_so);
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'outstanding_so',
                        title: 'Outstanding',
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            return parseFloat(row.outstanding_so).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'nominal_so_ttl',
                        title: 'Total (Rp)',
                        autoHide: false,
                        width: 'auto',
                        template: function(row) {
                            return parseFloat(row.nominal_so_ttl).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'status_so',
                        title: 'Status',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            if (row.flag_revisi == '1') {
                                return ucwords(row.status_so)+"-R";
                            }
                            else {
                                return ucwords(row.status_so);
                            }
                        },
                    },
                ],
            });
		});

        $(document).ready(function() {
			var listDo = @json($dataDelivery);
            var datatable = $('#list_do').KTDatatable({
                data: {
                    type: 'local',
                    source: listDo,
                    pageSize: 10,
                    saveState : false,
                },

                layout: {
                    scroll: true,
                    height: 'auto',
                    footer: false
                },

                sortable: true,

                filterable: true,

                pagination: true,

                search: {
                    input: $('#list_do_search_query')
                },

                columns: [
                    {
                        field: 'id',
                        title: '#',
                        sortable: false,
                        width: 20,
                        type: 'number',
                        selector: false,
                        textAlign: 'center',
                        visible:false,
                    },
                    {
                        field: 'kode_pengiriman',
                        title: 'No. Pengiriman',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            return row.kode_pengiriman.toUpperCase();
                        },
                    },
                    {
                        field: 'no_so',
                        title: '#Pesanan',
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            return row.no_so.toUpperCase();
                        },
                    },
                    {
                        field: 'nama_customer',
                        title: 'Customer',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            return ucwords(row.nama_customer);
                        },
                    },
                    {
                        field: 'no_sj_manual',
                        title: 'No. SJ Manual',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            if (row.no_sj_manual != null) {
                                return formatDate(row.no_sj_manual);
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'jumlah_total_sj',
                        title: 'Jumlah',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.jumlah_total_sj).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'tanggal_sj',
                        title: 'Tanggal SJ',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            if (row.tanggal_sj != null) {
                                return formatDate(row.tanggal_sj);
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'tanggal_kirim',
                        title: 'Tanggal Kirim',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            if (row.tanggal_kirim != null) {
                                return formatDate(row.tanggal_kirim);
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'status_pengiriman',
                        title: 'Status',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            if (row.flag_revisi == '1') {
                                return ucwords(row.status_pengiriman)+"(Revisi)";
                            }
                            else {
                                return ucwords(row.status_pengiriman);
                            }
                        },
                    },
                    {
                        field: 'flag_terkirim',
                        title: 'Status Terkirim',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            if (row.status_pengiriman == 'posted' && row.flag_terkirim == '1') {
                                return "Terkirim";
                            }
                            else if (row.status_pengiriman == 'posted' && row.flag_terkirim == '0') {
                                return "Dalam Proses Pengiriman";
                            }
                            else {
                                return "Dalam Proses Approval";
                            }
                        },
                    },
                ],
            });
		});

        $(document).ready(function() {
			var listInvoice = @json($dataSalesInvoice);
            var datatable = $('#list_invoice').KTDatatable({
                data: {
                    type: 'local',
                    source: listInvoice,
                    pageSize: 10,
                    saveState : false,
                },

                layout: {
                    scroll: true,
                    height: 'auto',
                    footer: false
                },

                sortable: true,

                filterable: true,

                pagination: true,

                search: {
                    input: $('#list_invoice_search_query')
                },

                columns: [
                    {
                        field: 'id',
                        title: '#',
                        sortable: false,
                        width: 20,
                        type: 'number',
                        selector: false,
                        textAlign: 'center',
                        visible:false,
                    },
                    {
                        field: 'kode_invoice',
                        title: 'No. Invoice',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            return row.kode_invoice.toUpperCase();
                        },
                    },
                    {
                        field: 'no_so',
                        title: 'No. SO',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            return row.no_so.toUpperCase();
                        },
                    },
                    {
                        field: 'nama_customer',
                        title: 'Customer',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            return ucwords(row.nama_customer);
                        },
                    },
                    {
                        field: 'ttl_qty',
                        title: 'Total',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.ttl_qty).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'dpp',
                        title: 'DPP',
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            return parseFloat(row.dpp).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'ppn',
                        title: 'PPN',
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            return parseFloat(row.ppn).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'grand_total',
                        title: 'Grand Total',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.grand_total).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'tanggal_invoice',
                        title: 'Tanggal Invoice',
                        width: 'auto',
                        textAlign: 'center',
                        //autoHide: false,
                        template: function(row) {
                            if (row.tanggal_invoice != null) {
                                return formatDate(row.tanggal_invoice);
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'tanggal_jt',
                        title: 'Jatuh Tempo',
                        textAlign: 'center',
                        width: 'auto',
                        //autoHide: false,
                        template: function(row) {
                            if (row.tanggal_jt != null) {
                                return formatDate(row.tanggal_jt);
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'status_invoice',
                        title: 'Status',
                        width: 'auto',
                        textAlign: 'center',
                        //autoHide: false,
                        template: function(row) {
                            if (row.flag_revisi == '1') {
                                return ucwords(row.status_invoice)+"-R";
                            }
                            else {
                                return ucwords(row.status_invoice);
                            }
                        },
                    },
                    {
                        field: 'flag_tf',
                        title: 'Status Tukar Faktur',
                        width: 'auto',
                        textAlign: 'center',
                        //autoHide: false,
                        template: function(row) {
                            if (row.flag_tf == '1') {
                                return "Sudah Tukar Faktur";
                            }
                            else {
                                return "Belum Tukar Faktur";
                            }
                        },
                    },
                    {
                        field: 'flag_pembayaran',
                        title: 'Status Pembayaran',
                        width: 'auto',
                        textAlign: 'center',
                        //autoHide: false,
                        template: function(row) {
                            if (row.flag_tf == '1') {
                                return "Bayar Sebagian";
                            }
                            else if (row.flag_pembayaran == '2') {
                                return "Lunas";
                            }
                            else {
                                return "Belum Ada Pembayaran";
                            }
                        },
                    },
                ],
            });
		});

        $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
            var list_do = $("#list_do").KTDatatable();
            list_do.reload();
            var list_invoice = $("#list_invoice").KTDatatable();
            list_invoice.reload();
	    });
    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
