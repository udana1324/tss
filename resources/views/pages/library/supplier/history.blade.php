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
                                        <span class="nav-text">Purchase Order</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab_pane_2" id="tab2">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Penerimaan</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab_pane_3" id="tab3">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Faktur</span>
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
                                                        <input type="text" class="form-control" placeholder="Search..." id="list_po_search_query"/>
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

                                <div class="datatable datatable-bordered datatable-head-custom" id="list_po"></div>

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
                                                        <input type="text" class="form-control" placeholder="Search..." id="list_rcv_search_query"/>
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

                                <div class="datatable datatable-bordered datatable-head-custom" id="list_rcv"></div>

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
            window.location.href = "{{ url('/Supplier') }}";
	    });

        $(document).ready(function() {
			var listPO = @json($dataPurchase);
            var datatable = $('#list_po').KTDatatable({
                data: {
                    type: 'local',
                    source: listPO,
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
                    input: $('#list_po_search_query')
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
                        field: 'no_po',
                        title: 'No. PO',
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            return row.no_po.toUpperCase();
                        },
                    },
                    {
                        field: 'nama_supplier',
                        title: 'Nama Supplier',
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            return row.nama_supplier.toUpperCase();
                        },
                    },
                    {
                        field: 'jumlah_total_po',
                        title: 'Jumlah',
                        textAlign: 'center',
                        width: 'auto',
                        template: function(row) {
                            return parseFloat(row.jumlah_total_po).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'tanggal_po',
                        title: 'Tanggal Pembelian',
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            if (row.tanggal_po != null) {
                                return formatDate(row.tanggal_po);
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'tanggal_deadline',
                        title: 'Tanggal Deadline',
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            if (row.tanggal_deadline != null) {
                                return formatDate(row.tanggal_deadline);
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'outstanding_po',
                        width: 'auto',
                        title: 'Outstanding',
                        textAlign: 'center',
                        template: function(row) {
                            return parseFloat(row.outstanding_po).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'nominal_po_ttl',
                        width: 'auto',
                        title: 'Total',
                        template: function(row) {
                            return parseFloat(row.nominal_po_ttl).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'status_po',
                        title: 'Status',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            if (row.flag_revisi == '1') {
                                return ucwords(row.status_po)+"-R";
                            }
                            else {
                                return ucwords(row.status_po);
                            }
                        },
                    },
                ],
            });
		});

        $(document).ready(function() {
			var listRcv = @json($dataReceiving);
            var datatable = $('#list_rcv').KTDatatable({
                data: {
                    type: 'local',
                    source: listRcv,
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
                    input: $('#list_rcv_search_query')
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
                        field: 'kode_penerimaan',
                        title: 'No. Penerimaan',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            return row.kode_penerimaan.toUpperCase();
                        },
                    },
                    {
                        field: 'no_po',
                        title: 'No. PO',
                        textAlign: 'center',
                        width: 'auto',
                        template: function(row) {
                            return row.no_po.toUpperCase();
                        },
                    },
                    {
                        field: 'nama_supplier',
                        width: 'auto',
                        title: 'Supplier',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            return ucwords(row.nama_supplier);
                        },
                    },
                    {
                        field: 'no_sj_supplier',
                        title: 'No. SJ Supplier',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            if (row.no_sj_supplier != null || row.no_sj_supplier != "") {
                                return ucwords(row.no_sj_supplier);
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'jumlah_total_sj',
                        title: 'Jumlah',
                        textAlign: 'center',
                        width: 'auto',
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
                        field: 'tanggal_terima',
                        title: 'Tanggal Terima',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            if (row.tanggal_terima != null) {
                                return formatDate(row.tanggal_terima);
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'status_penerimaan',
                        title: 'Status',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            if (row.flag_revisi == '1') {
                                return ucwords(row.status_penerimaan)+"(Revisi)";
                            }
                            else {
                                return ucwords(row.status_penerimaan);
                            }
                        },
                    },
                ],
            });
		});

        $(document).ready(function() {
			var listInvoice = @json($dataInvoice);
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
                        textAlign: 'center',
                        autoHide: false,
                        width: 'auto',
                        template: function(row) {
                            return row.kode_invoice.toUpperCase();
                        },
                    },
                    {
                        field: 'no_po',
                        title: 'No. PO',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            return row.no_po.toUpperCase();
                        },
                    },
                    {
                        field: 'nama_supplier',
                        title: 'Supplier',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            return ucwords(row.nama_supplier);
                        },
                    },
                    {
                        field: 'ttl_qty',
                        title: 'Total',
                        autoHide: false,
                        width: 50,
                        template: function(row) {
                            return parseFloat(row.ttl_qty).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'grand_total',
                        title: 'Grand Total',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.grand_total).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'tanggal_invoice',
                        title: 'Tanggal Invoice',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: false,
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
                        autoHide: false,
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
                        autoHide: false,
                        template: function(row) {
                            if (row.flag_revisi == '1') {
                                return ucwords(row.status_invoice)+"-R";
                            }
                            else {
                                return ucwords(row.status_invoice);
                            }
                        },
                    },
                ],
            });
		});

        $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
            var list_rcv = $("#list_rcv").KTDatatable();
            list_rcv.reload();
            var list_invoice = $("#list_invoice").KTDatatable();
            list_invoice.reload();
	    });

    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
