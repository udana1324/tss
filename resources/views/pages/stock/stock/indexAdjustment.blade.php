@extends('layout.default')
@section('content')
	<!-- Content area -->
			<div class="content">
			@include('pages.alerts')
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
                        <div class="card-title">
                            <h3 class="card-label text-white">Adjustment Stok Barang</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            @if($hakAkses->add == "Y")
                            <button class="btn btn-primary btn-outline-white mr-2" onclick="window.location.href = '{{ url('Stock/Adjustment/Add') }}';">
                                <i class="flaticon2-plus"></i>
                                Buat Adjustment
                            </button>
                            @endif
                            <!--end::Button-->
                        </div>
					</div>

					<div class="card-body">

                        <!--begin::Search Form-->
                        <div class="mb-7">
                            <div class="row align-items-center">
                                <div class="col-lg-9 col-xl-8">
                                    <div class="row align-items-center">
                                        <div class="col-md-3 my-2 my-md-0">
                                            <div class="input-icon">
                                                <input type="text" class="form-control" placeholder="Search..." id="table_adjustment_search_query"/>
                                                <span>
                                                    <i class="flaticon2-search-1 text-muted"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 my-2 my-md-0">
                                            <div class="d-flex align-items-center">
                                                <label class="mr-3 mb-0 d-none d-md-block">Kategori </label>
                                                <select class="form-control select2" id="table_stock_search_category">
                                                    <option value="">All</option>
                                                    @foreach($dataCategory as $rowCategory)
                                                    <option value="{{$rowCategory->nama_kategori}}">{{ucwords($rowCategory->nama_kategori)}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 my-2 my-md-0">
                                            <div class="d-flex align-items-center">
                                                <label class="mr-3 mb-0 d-none d-md-block">Merk </label>
                                                <select class="form-control select2" id="table_stock_search_brand">
                                                    <option value="">All</option>
                                                    @foreach($dataBrand as $rowBrand)
                                                    <option value="{{$rowBrand->nama_merk}}">{{ucwords($rowBrand->nama_merk)}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 my-2 my-md-0">
                                            <div class="d-flex align-items-center">
                                                <label class="mr-3 mb-0 d-none d-md-block">Status </label>
                                                <select class="form-control select2" id="table_adjustment_search_jenis">
                                                    <option value="">All</option>
                                                    <option value="retur_purc">Retur Pembelian</option>
                                                    <option value="retur_sale">Retur Penjualan</option>
                                                    <option value="penambahan">Penambahan</option>
                                                    <option value="pengurangan">Pengurangan</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end: Search Form-->

                        <!--begin: Datatable-->

                        <div class="datatable datatable-bordered datatable-head-custom" id="table_adjustment"></div>

                        <!--end: Datatable-->
                    </div>
				</div>
				<!-- /basic initialization -->
			</div>
			<!-- /content area -->
@endsection
@section('scripts')
	<script type="text/javascript">
		//$('div.alert').delay(5000).slideUp(300);
        $(document).ready(function () {
            $('#table_stock_search_category').select2({
                allowClear: true
            });

            $('#table_stock_search_brand').select2({
                allowClear: true
            });

            $('#table_adjustment_search_jenis').select2({
                allowClear: true
            });
        });

        function formatDate(strDate) {
            var arrMonth = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            var date = new Date(strDate);
            var day = date.getDate();
            var month = date.getMonth();
            var year = date.getFullYear();

            return day + ' ' + arrMonth[month] + ' ' + year;
        }

		function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

		$(document).ready(function() {

            var datatable = $('#table_adjustment').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Stock/GetDataAdjustment',
                            method: 'GET',

                        }
                    },
                    pageSize: 25,
                    serverPaging: true,
                    serverFiltering: false,
                    serverSorting: false,
                    saveState: false
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
                    input: $('#table_adjustment_search_query')
                },

                columns: [
                    {
                        field: 'id',
                        title: '#',
                        sortable: false,
                        width: 0,
                        type: 'number',
                        selector: false,
                        textAlign: 'center',
                        visible:false,
                    },
                    {
                        field: 'nama_item',
                        width: 'auto',
                        title: 'Nama Barang',
                        textAlign: 'left',
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bold">'+row.nama_item+'</span>';
                                txt += '<br /><span class="label label-md label-outline-primary label-inline mt-1 mr-1">' + row.kode_transaksi.toUpperCase() + '</span>';
                                if(row.value_spesifikasi != null) {
                                    txt += '<br /><span class="label label-md label-outline-primary label-inline mt-1 mr-1">' +'('+row.value_spesifikasi+')'+row.kode_item.toUpperCase()+ '</span>';
                                }
                                else {
                                    txt += '<br /><span class="label label-md label-outline-primary label-inline mt-1 mr-1">' +row.kode_item.toUpperCase()+ '</span>';
                                }
                                txt += '<span class="label label-md label-outline-primary label-inline mt-1 mr-1">' + row.nama_merk.toUpperCase() + '</span>';
                                txt += '<span class="label label-md label-outline-primary label-inline mt-1">' + row.nama_kategori + '</span>';
                                return txt;
                        }
                    },
                    {
                        field: 'txt_index',
                        title: 'Gudang',
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            if (row.txt_index != null) {
                                return row.txt_index;
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'qty_item',
                        title: 'Jumlah',
                        type: 'number',
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            return parseFloat(row.qty_item).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'nama_satuan',
                        title: 'Satuan',
                        textAlign: 'center',
                        width: 'auto',
                        template: function(row) {
                            if (row.nama_satuan != null) {
                                return row.nama_satuan.toUpperCase();
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'tgl_transaksi',
                        title: 'Tanggal',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            if (row.tgl_transaksi != null) {
                                return formatDate(row.tgl_transaksi);
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'jenis_adjustment',
                        title: 'Jenis Penyesuaian',
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            if (row.jenis_adjustment == "penambahan") {
                                return '<span class="label label-success label-dot mr-2"></span><span class="font-weight-bold text-success">Penambahan</span>';
                            }
                            else if (row.jenis_adjustment == "pengurangan") {
                                return '<span class="label label-danger label-dot mr-2"></span><span class="font-weight-bold text-danger"><span class="label label-danger label-dot mr-2"></span><span class="font-weight-bold text-danger">Pengurangan</span></span>';
                            }
                            else if (row.jenis_adjustment == "retur_purc") {
                                return '<span class="label label-success label-dot mr-2"></span><span class="font-weight-bold text-danger"><span class="label label-success label-dot mr-2"></span><span class="font-weight-bold text-danger">Retur Purchase</span></span>';
                            }
                            else if (row.jenis_adjustment == "retur_sale") {
                                return '<span class="label label-danger label-dot mr-2"></span><span class="font-weight-bold text-danger"><span class="label label-danger label-dot mr-2"></span><span class="font-weight-bold text-danger">Retur Sales</span>s</span>';
                            }
                        },
                    },
                    {
                        field: 'keterangan',
                        title: 'Keterangan',
                        textAlign: 'center',
                        autoHide: true,
                        template: function(row) {
                            if (row.keterangan != null) {
                                return "<span class='text-left'>"+row.keterangan+"</span>";
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'Actions',
                        title: 'Aksi',
                        sortable: false,
                        width: 110,
                        overflow: 'visible',
                        autoHide: false,
                        textAlign: 'center',
                        template: function(row) {
                            var txtAction = "<a href='#' class='btn btn-sm btn-clean btn-icon' title='Hapus' onclick='deleteData("+row.id+");return false;'>";
                                txtAction += "<i class='la la-trash'></i>";
                                txtAction += "</a>";

                            return txtAction;
                        },
                    }
                ],
            });

            $('#table_stock_search_brand').on('change', function() {
                datatable.search($(this).val(), 'nama_merk');
            });

            $('#table_stock_search_category').on('change', function() {
                datatable.search($(this).val(), 'nama_kategori');
            });

            $('#table_adjustment_search_jenis').on('change', function() {
                datatable.search($(this).val(), 'jenis_adjustment');
            });
        });

        function deleteData(id) {
            Swal.fire({
                title: "Hapus?",
                text: "Apakah anda ingin menghapus data ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "/Stock/DeleteAdjustment",
                        method: 'POST',
                        data: {
                            idDetail: id
                        },
                        success: function(result){
                            Swal.fire(
                                "Sukses!",
                                "Data Berhasil dihapus!.",
                                "success"
                            )
                        }
                    });
                    var datatable = $("#table_adjustment").KTDatatable();
                    datatable.reload();
                }
                else if (result.dismiss === "cancel") {
                    // Swal.fire(
                    //     "Cancelled",
                    //     "Your imaginary file is safe :)",
                    //     "error"
                    // )
                    e.preventDefault();
                }
            });
        }

    </script>
@endsection
