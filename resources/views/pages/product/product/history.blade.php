@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header card-header-tabs-line">
					    <div class="card-title">
                            <h4 class="card-label text-muted mt-2">RIWAYAT TRANSAKSI {{$dataProduct->nama_item}}</h4>
                        </div>
                        <div class="card-toolbar">
                            <ul class="nav nav-tabs nav-bold nav-tabs-line">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#tab_pane_1">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Pembelian</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab_pane_2" id="tab2">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Penjualan</span>
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
                                                        <input type="text" class="form-control" placeholder="Search..." id="list_pembelian_search_query"/>
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

                                <div class="datatable datatable-bordered datatable-head-custom" id="list_pembelian"></div>

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
                                                        <input type="text" class="form-control" placeholder="Search..." id="list_penjualan_search_query"/>
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

                                <div class="datatable datatable-bordered datatable-head-custom" id="list_penjualan"></div>

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
            window.location.href = "{{ url('/Product') }}";
	    });

        $(document).ready(function() {
            var hakAksesHargaBeli = "{{ $hakAksesHargaBeli != null }}";
            var groupPembelian = "{{ $userGroup === null ? null : $userGroup }}";
			var listPembelian = @json($dataPembelian);
            var datatable = $('#list_pembelian').KTDatatable({
                data: {
                    type: 'local',
                    source: listPembelian,
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
                    input: $('#list_pembelian_search_query')
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
                        field: 'tanggal_sj',
                        title: 'Tanggal',
                        textAlign: 'center',
                        width: 'auto',
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
                        field: 'kode_penerimaan',
                        title: 'No. Penerimaan',
                        width: '200',
                        textAlign: 'left',
                        autoHide:false,
                        template: function(row) {
                            var txtTgl = "";
                            if (row.kode_penerimaan != null) {
                                txtTgl += "<span class='font-weight-bold'>" + row.kode_penerimaan.toUpperCase() + "</span>";
                            }
                            if (row.no_po != null) {
                                txtTgl += "<br>";
                                txtTgl += "<span class='label label-md label-outline-primary label-inline mt-1'>PO : " + row.no_po.toUpperCase() + "</span>";
                            }
                            if (row.kode_invoice != null) {
                                txtTgl += "<br>";
                                txtTgl += "<span class='label label-md label-outline-primary label-inline mt-1'>INV : " + row.kode_invoice.toUpperCase() + "</span>";
                            }
                            return txtTgl;
                        },
                    },
                    {
                        field: 'nama_supplier',
                        title: 'Nama Vendor',
                        width: '240',
                        textAlign: 'left',
                        template: function(row) {
                            return row.nama_supplier.toUpperCase();
                        },
                    },
                    {
                        field: 'qty_item',
                        title: 'Jumlah',
                        textAlign: 'right',
                        width: '80',
                        template: function(row) {
                            if (row.qty_item != null) {
                                return parseFloat(row.qty_item).toLocaleString('id-ID', { maximumFractionDigits: 2});
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'nama_satuan',
                        title: 'Satuan',
                        width: '70',
                        textAlign: 'left',
                        template: function(row) {
                            return ucwords(row.nama_satuan);
                        },
                    },
                    {
                        field: 'harga_beli',
                        title: 'Harga Beli (Rp)',
                        textAlign: 'right',
                        width: 'auto',
                        autoHide:false,
                        template: function(row) {
                            if (hakAksesHargaBeli != null && groupPembelian != "penjualan") {
                                return parseFloat(row.harga_beli).toLocaleString('id-ID', { maximumFractionDigits: 2});
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                ],
            });
		});

        $(document).ready(function() {
            var hakAksesHargaJual = "{{ $hakAksesHargaJual != null }}";
            var groupPenjualan = "{{ $userGroup === null ? null : $userGroup }}";
			var listPenjualan = @json($dataPenjualan);
            var datatable = $('#list_penjualan').KTDatatable({
                data: {
                    type: 'local',
                    source: listPenjualan,
                    pageSize: 20,
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

                autoHide:false,

                search: {
                    input: $('#list_penjualan_search_query')
                },

                columns: [
                    {
                        field: 'id',
                        title: '#',
                        sortable: false,
                        width: 'auto',
                        type: 'number',
                        selector: false,
                        textAlign: 'center',
                        visible:false,
                        autoHide:false,
                    },
                    {
                        field: 'tanggal_sj',
                        title: 'Tanggal Pengiriman',
                        width: '195',
                        textAlign: 'center',
                        autoHide:false,
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
                        field: 'kode_pengiriman',
                        title: 'No. Surat Jalan',
                        width: '200',
                        textAlign: 'left',
                        autoHide:false,
                        template: function(row) {
                            var txtTgl = "";
                            if (row.kode_pengiriman != null) {
                                txtTgl += row.kode_pengiriman.toUpperCase();
                            }
                            if (row.no_so != null) {
                                txtTgl += "<br>";
                                txtTgl += "<span class='label label-md label-outline-primary label-inline mt-1'>SO : " + row.no_so.toUpperCase() + "</span>";
                            }
                            if (row.kode_invoice != null) {
                                txtTgl += "<br>";
                                txtTgl += "<span class='label label-md label-outline-primary label-inline mt-1'>INV : " + row.kode_invoice.toUpperCase() + "</span>";
                            }
                            return txtTgl;
                        },
                    },
                    {
                        field: 'nama_customer',
                        title: 'Nama Pelanggan',
                        width: '240',
                        textAlign: 'left',
                        autoHide:false,
                        template: function(row) {
                            return row.nama_customer.toUpperCase();
                        },
                    },
                    {
                        field: 'qty_item',
                        title: 'Jumlah',
                        width: '80',
                        textAlign: 'right',
                        autoHide:false,
                        template: function(row) {
                            if (row.qty_item != null) {
                                return parseFloat(row.qty_item).toLocaleString('id-ID', { maximumFractionDigits: 2});
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'nama_satuan',
                        title: 'Satuan',
                        textAlign: 'left',
                        width: '70',
                        autoHide:false,
                        template: function(row) {
                            return ucwords(row.nama_satuan);
                        },
                    },
                    {
                        field: 'harga_jual',
                        title: 'Harga Jual (Rp)',
                        textAlign: 'right',
                        width: 'auto',
                        autoHide:false,
                        template: function(row) {
                            if (hakAksesHargaJual != null && groupPenjualan != "pembelian") {
                                return parseFloat(row.harga_jual).toLocaleString('id-ID', { maximumFractionDigits: 2});
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                ],
            });
		});

        $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
            var list_penjualan = $("#list_penjualan").KTDatatable();
            list_penjualan.reload();
	    });
    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
