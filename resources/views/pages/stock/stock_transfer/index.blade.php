@extends('layout.default')
@section('content')
	<!-- Content area -->
			<div class="content">
			@include('pages.alerts')
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
                        <div class="card-title">
                            <h3 class="card-label text-white">Pemindahan Stok Barang</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            @if($hakAkses->add == "Y")
                            <button class="btn btn-primary btn-outline-white mr-2" onclick="window.location.href = '{{ url('StockTransfer/Add') }}';">
                                <i class="flaticon2-plus"></i>
                                Buat Pemindahan Stok
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
                                                <input type="text" class="form-control" placeholder="Search..." id="table_transfer_search_query"/>
                                                <span>
                                                    <i class="flaticon2-search-1 text-muted"></i>
                                                </span>
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-4 my-2 my-md-0">
                                            <div class="d-flex align-items-center">
                                                <label class="mr-3 mb-0 d-none d-md-block">Barang</label>
                                                <select class="form-control select2" id="table_stock_search_product">
                                                    <option value="">Semua</option>
                                                    @foreach($dataProduct as $product)
                                                    <option value="{{$product->id}}">{{ $product->value_spesifikasi != null ? '('.$product->value_spesifikasi.')' : "" }}{{strtoupper($product->kode_item." - ".$product->nama_item)}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div> --}}
                                        <div class="col-md-3 my-2 my-md-0">
                                            <div class="d-flex align-items-center">
                                                <label class="mr-3 mb-0 d-none d-md-block">Status </label>
                                                <select class="form-control select2" id="table_transfer_search_status">
                                                    <option value="">Semua</option>
                                                    @foreach($dataStatus as $status)
                                                    <option value="{{$status->status_transfer}}">{{ucwords($status->status_transfer)}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end: Search Form-->

                        <!--begin: Datatable-->

                        <div class="datatable datatable-bordered datatable-head-custom" id="table_transfer"></div>

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
            $('#table_transfer_search_category').select2({
                allowClear: true
            });

            // $('#table_stock_search_product').select2({
            //     allowClear: true
            // });

            $('#table_transfer_search_status').select2({
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

            var datatable = $('#table_transfer').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/StockTransfer/GetData',
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
                    input: $('#table_transfer_search_query')
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
                        field: 'kode_transaksi',
                        width: 'auto',
                        title: 'Kode Transaksi',
                        textAlign: 'left',
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="text-left">'+row.kode_transaksi.toUpperCase()+'</span>';
                                return txt;
                        }
                    },
                    {
                        field: 'tgl_transaksi',
                        title: 'Tanggal',
                        width: 'auto',
                        textAlign: 'center',
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
                        field: 'keterangan',
                        title: 'Keterangan',
                        textAlign: 'center',
                        autoHide: true,
                        template: function(row) {
                            if (row.keterangan != null) {
                                return "<span class='text-left'>"+row.keterangan+"</span>";
                            }
                            else {
                                return "<span class='text-left'>-</span>";
                            }
                        },
                    },
                    {
                        field: 'status_transfer',
                        title: 'Status',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            var statusTxt = "";
                            if (row.flag_revisi == '1') {
                                statusTxt = ucwords(row.status_transfer)+"-R";
                            }
                            else {
                                statusTxt = ucwords(row.status_transfer);
                            }

                            if (row.status_transfer == "draft") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline">'+statusTxt+'</span>';
                            }
                            else if (row.status_transfer == "posted") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-primary">'+statusTxt+'</span>';
                            }
                            else if (row.status_transfer == "batal") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-danger">'+statusTxt+'</span>';
                            }

                            return statusTxt;
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
                            var akses = @json($hakAkses);
                            var txtAction = '<div class="dropdown dropdown-inline">';
                                    txtAction += '<a href="#" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">';
                                    txtAction += '<i class="la la-cog"></i>';
                                    txtAction += '</a>';
                                    txtAction += '<div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">';
                                    txtAction += '<ul class="nav nav-hoverable flex-column">';

                                    txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('StockTransfer.Detail', 'idTrf')}}'><i class='nav-icon la la-eye'></i><span class='nav-text'>Detail</span></a></li>";
                                    if (akses.edit == "Y" && row.status_transfer == "draft") {
                                        txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('StockTransfer.edit', 'idTrf')}}'><i class='nav-icon la la-edit'></i><span class='nav-text'>Ubah</span></a></li>";
                                    }
                                    if (akses.delete) {
                                        // txtAction += "<li class='nav-item'><a class='nav-link' href='#' onclick='deleteData("+row.id+");return false;''><i class='nav-icon la la-trash'></i><span class='nav-text'>Hapus</span></a></li>";
                                    }
                                    txtAction += '</ul>';
                                    txtAction += '</div>';
                                    txtAction += '</div>';
                                    txtAction = txtAction.replaceAll('idTrf',row.id);
                            return txtAction;
                        },
                    }
                ],
            });

            // $('#table_stock_search_product').on('change', function() {
            //     datatable.search($(this).val(), 'id');
            // });

            // $('#table_transfer_search_category').on('change', function() {
            //     datatable.search($(this).val(), 'nama_kategori');
            // });

            $('#table_transfer_search_status').on('change', function() {
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
                        url: "/StockTransfer/Delete",
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
                    var datatable = $("#table_transfer").KTDatatable();
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
