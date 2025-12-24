@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header card-header-tabs-line">
					    <div class="card-title">
                            <h4 class="card-label text-muted mt-2">RIWAYAT TRANSAKSI {{$dataCustomer->nama_customer}}</h4>
                        </div>
					</div>

					<div class="card-body">
						<form action="" method="POST" id="form_add">
						{{ csrf_field() }}
						<div class="tab-content">
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

                            <!--begin: Datatable-->

                            <div class="datatable datatable-bordered datatable-head-custom" id="list_penjualan"></div>

                            <!--end: Datatable-->

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
                            if (row.no_po_customer != null || row.no_po_customer == "") {
                                txtTgl += "<br>";
                                txtTgl += "<span class='label label-md label-outline-primary label-inline mt-1'>PO Cust : " + row.no_po_customer.toUpperCase() + "</span>";
                            }
                            if (row.kode_invoice != null) {
                                txtTgl += "<br>";
                                txtTgl += "<span class='label label-md label-outline-primary label-inline mt-1'>INV : " + row.kode_invoice.toUpperCase() + "</span>";
                            }
                            return txtTgl;
                        },
                    },
                    {
                        field: 'nama_item',
                        title: 'Nama Barang',
                        width: '240',
                        textAlign: 'left',
                        autoHide:false,
                        template: function(row) {
                            return row.nama_item.toUpperCase();
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
                        title: 'Harga',
                        width: '80',
                        textAlign: 'right',
                        autoHide:false,
                        template: function(row) {
                            if (row.harga_jual != null) {
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

    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
