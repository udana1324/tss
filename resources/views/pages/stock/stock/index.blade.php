@extends('layout.default')
@section('content')
	<!-- Content area -->
			<div class="content">
			@include('pages.alerts')
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
                        <div class="card-title">
                            <h3 class="card-label text-white">Laporan Stok Barang</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->
                                @if($hakAkses->export == "Y")
                                <form action="{{ route('StockCard.Export') }}" class="form-horizontal" id="form_add" method="POST" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <button type="button" class="btn btn-success font-weight-bold mr-2" id="btnExport" >Export Kartu Stock <i class="fas fa-file-excel"></i></button>
                                </form>
                                @endif
                            <!--end::Button-->
                        </div>
					</div>

					<div class="card-body">
                        <!--begin::Search Form-->
                        <div class="mb-5 row align-items-center">
                            <div class="col-md-3">
                                <div class="input-icon mr-3 mt-8">
                                    <input type="text" class="form-control" placeholder="Search..." id="table_stock_search_query"/>
                                    <span>
                                        <i class="flaticon2-search-1 text-muted"></i>
                                    </span>
                                </div>
                            </div>
                            {{-- <div class="col-md-3">
                                <label class="mr-3 mb-2">Kode Supplier :</label>
                                <select class="form-control select2" id="table_stock_search_supplier">
                                    <option value="">All</option>
                                    @foreach($kodeSP as $kodeSupplier)
                                    <option value="{{$kodeSupplier->value_spesifikasi}}">{{ucwords($kodeSupplier->value_spesifikasi)}}</option>
                                    @endforeach
                                </select>
                            </div> --}}
                            <div class="col-md-3">
                                <label class="mr-3 mb-2">Lokasi :</label>
                                <select class="form-control select2" id="table_stock_search_index">
                                    <option value="">All</option>
                                    @foreach($listIndex as $index)
                                    <option value="{{$index['id']}}">{{strtoupper($index['nama_index'])}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="mb-2">Status Stock :</label>
                                <select class="form-control select2" id="table_stock_search_status">
                                    <option value="">All</option>
                                    <option value="Stok Menipis">Stok Menipis</option>
                                    <option value="Stok Minus">Stok Minus</option>
                                    <option value="Stok Melebihi Batas">Stok Melebihi Batas</option>
                                    <option value="Kosong">Kosong</option>
                                </select>
                            </div>
                        </div>
                        <!--end: Search Form-->

                        <!--begin: Datatable-->

                        <div class="datatable datatable-bordered datatable-head-custom" id="table_stock"></div>

                        <!--end: Datatable-->
                    </div>
				</div>
				<!-- /basic initialization -->
				<div id="modal_detail_lokasi" class="modal fade">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header bg-primary">

                                <h5 class="modal-title" id="txtNamaLokasi"></h5>
                            </div>
                            <div class="modal-body">
                                <div class="mb-7">
                                    <div class="row align-items-center">
                                        <div class="col-lg-12 col-xl-8">
                                            <div class="row align-items-center">
                                                <div class="col-md-4">
                                                    <div class="align-items-center">
                                                        <label style="display: inline-block;"></label>
                                                        <div class="input-icon">
                                                            <input type="text" class="form-control" placeholder="Search..." id="table_lokasi_search_query"/>
                                                            <span>
                                                                <i class="flaticon2-search-1 text-muted"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Search Form-->
                                <!--end: Search Form-->
                                <!--begin: Datatable-->

                                <div class="datatable datatable-bordered datatable-head-custom" id="list_item_lokasi"></div>

                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-link" data-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
			<!-- /content area -->
@endsection
@section('scripts')
	<script type="text/javascript">
		//$('div.alert').delay(5000).slideUp(300);
        $(document).ready(function () {
            $('#table_stock_search_supplier').select2({
                allowClear: true
            });

            $('#table_stock_search_index').select2({
                allowClear: true
            });

            $('#table_stock_search_status').select2({
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

            var datatable = $('#table_stock').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Stock/GetData',
                            method: 'GET',

                        }
                    },
                    pageSize: 100,
                    serverPaging: false,
                    serverFiltering: false,
                    serverSorting: false,
                },

                layout: {
                    scroll: true,
                    height: 'auto',
                    footer: false
                },

                sortable: true,

                filterable: true,

                pagination: true,

                rows: {
                    autoHide: false
                },

                search: {
                    input: $('#table_stock_search_query')
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
                        autoHide: false,
                    },
                    {
                        field: 'nama_item',
                        title: 'Nama Barang',
                        width: 350,
                        textAlign: 'left',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                if (row.stok_item != null) {
                                    txt = "<a href='#' class='text-secondary text-hover-primary' data-toggle='modal' data-target='#modal_detail_lokasi' title='Detail Lokasi' onclick='viewDetailLokasi(" + row.id + ", " + row.id_satuan + ");return false;'>";
                                    txt += '<span class="font-weight-bold">'+row.nama_item+'</span>';
                                    txt += "</a>";
                                }
                                else {
                                    txt += '<span class="font-weight-bold">'+row.nama_item+'</span>';
                                }
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
                        field: 'stok_item',
                        width: 'auto',
                        title: 'Jumlah Stok',
                        type: 'number',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.stok_item).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'nama_satuan',
                        title: 'Satuan',
                        width: 'auto',
                        autoHide: false,
                        textAlign: 'center',
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
                        field: 'Status',
                        title: 'Status Stok',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            if (parseFloat(row.stok_item) < 0) {
                                return '<span class="label label-rounded font-weight-bold label-lg label-light-danger label-inline">Stok Minus</span>';
                            }
                            else if(parseFloat(row.stok_item) == 0) {
                                return '<span class="label label-rounded font-weight-bold label-lg label-light-default label-inline">Kosong</span>';
                            }
                            else if (parseFloat(row.stok_item) <= parseFloat(row.stok_minimum)) {
                                return '<span class="label label-rounded font-weight-bold label-lg label-light-warning label-inline">Stok Menipis</span>';
                            }
                            else if (parseFloat(row.stok_item) > parseFloat(row.stok_maksimum)) {
                                return '<span class="label label-rounded font-weight-bold label-lg label-light-danger label-inline">Stok Melebihi Batas</span>';
                            }
                            else {
                                return '<span class="label label-rounded font-weight-bold label-lg label-light-primary label-inline">Normal</span>';
                            }
                        },
                    },
                    {
                        field: 'txtKode',
                        title: 'Txt Barang',
                        autoHide: true,
                        textAlign: 'center',
                        width: 50,
                        visible:false,
                        template: function(row) {
                            if(row.value_spesifikasi != null) {
                                return '('+row.value_spesifikasi+')'+row.kode_item.toUpperCase() + "<span id='txt_"+row.id+"'>("+row.value_spesifikasi+')'+row.kode_item.toUpperCase() + " - " +row.nama_item.toUpperCase()+"</span>";

                            }
                            else {
                                return row.kode_item.toUpperCase() + "<span id='txt_"+row.id+"'>"+row.kode_item.toUpperCase() + " - " +row.nama_item.toUpperCase()+"</span>";
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
                            var akses = @json($hakAkses);
                            var txtAction = '<div class="dropdown dropdown-inline">';
                                txtAction += '<a href="#" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">';
                                txtAction += '<i class="la la-cog"></i>';
                                txtAction += '</a>';
                                txtAction += '<div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">';
                                txtAction += '<ul class="nav nav-hoverable flex-column">';

                                txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('Stock.Detail', ['idProduct', 'idSatuan'])}}'><i class='nav-icon la la-eye'></i><span class='nav-text'>Detail</span></a></li>";
                                txtAction += '</ul>';
                                txtAction += '</div>';
                                txtAction += '</div>';
                                txtAction = txtAction.replaceAll('idProduct',row.id);
                                txtAction = txtAction.replaceAll('idSatuan',row.id_satuan);
                                return txtAction;
                        },
                    }
                ],
            });

            $('#table_stock_search_supplier').on('change', function() {
                datatable.search($(this).val(), 'value_spesifikasi');
            });

            $('#table_stock_search_status').on('change', function() {
                datatable.search($(this).val(), 'status_stok');
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
                        url: "/SalesOrder/Delete",
                        method: 'POST',
                        data: {
                            idSalesOrder: id
                        },
                        success: function(result){
                            Swal.fire(
                                "Sukses!",
                                "Data Berhasil dihapus!.",
                                "success"
                            )
                        }
                    });
                    var datatable = $("#table_stock").KTDatatable();
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

        $(document).ready(function () {
            $("#btnExport").on('click', function(e) {
                // var errCount = 0;

                // $(".req").each(function(){
                //     if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                //         $(this).closest('.form-group, input-group').find('.err').show();
                //         errCount = errCount + 1;
                //     }
                //     else {
                //         $(this).closest('.form-group, input-group').find('.err').hide();
                //     }
                // });

                // var jenis = $("#jenisPeriode").val();

                // if (jenis == "harian") {
                //     $(".reqHarian").each(function(){
                //         if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                //             $(this).closest('.form-group, input-group').find('.errTanggal').show();
                //             errCount = errCount + 1;
                //         }
                //         else {
                //             $(this).closest('.form-group, input-group').find('.errTanggal').hide();
                //         }
                //     });
                // }

                // if (jenis == "bulanan") {
                //     $(".reqBulanan").each(function(){
                //         if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                //             $(this).closest('.form-group, input-group').find('.errBulanan').show();
                //             errCount = errCount + 1;
                //         }
                //         else {
                //             $(this).closest('.form-group, input-group').find('.errBulanan').hide();
                //         }
                //     });
                // }

                // if (jenis == "tahunan") {
                //     $(".reqTahunan").each(function(){
                //         if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                //             $(this).closest('.form-group, input-group').find('.errTahunan').show();
                //             errCount = errCount + 1;
                //         }
                //         else {
                //             $(this).closest('.form-group, input-group').find('.errTahunan').hide();
                //         }
                //     });
                // }

                // if (errCount == 0) {
                //     Swal.fire({
                //         title: "Export Data?",
                //         text: "Apakah data sudah sesuai?",
                //         icon: "warning",
                //         showCancelButton: true,
                //         confirmButtonText: "Ya",
                //         cancelButtonText: "Tidak",
                //         reverseButtons: false
                //     }).then(function(result) {
                //         if(result.value) {
                //             $("#form_add").off("submit").submit();
                //         }
                //         else if (result.dismiss === "cancel") {
                //             e.preventDefault();
                //         }
                //     });
                // }
                //alert("ok");
                Swal.fire({
                    title: "Cetak Kartu Stok?",
                    text: "Apakah ingin mencetak kartu stok?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya",
                    cancelButtonText: "Tidak",
                    reverseButtons: false
                }).then(function(result) {
                    if(result.value) {
                        $("#form_add").off("submit").submit();
                    }
                    else if (result.dismiss === "cancel") {
                        e.preventDefault();
                    }
                });
            });
        });

        $(document).ready(function () {
            $("#table_stock_search_index").on('change', function(e) {
                var datatable = $('#table_stock').KTDatatable();
                    datatable.setDataSourceParam('idIndex', $("#table_stock_search_index").val());
                    datatable.reload();
            });
        });

        $(document).ready(function() {

            var datatable = $('#list_item_lokasi').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Stock/GetDataPerIndex',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },

                        }
                    },
                    pageSize: 100,
                    serverPaging: false,
                    serverFiltering: false,
                    serverSorting: false,
                    saveState: false
                },

                layout: {
                    scroll: false,
                    height: 'auto',
                    footer: false
                },

                sortable: true,

                filterable: true,

                pagination: false,

                search: {
                    input: $('#table_lokasi_search_query')
                },

                rows: {
                    autoHide: false
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
                        field: 'jenis_sumber',
                        width: 'auto',
                        title: 'Sumber',
                        textAlign: 'left',
                        template: function(row) {
                            var statusTxt = "";

                            if (row.jenis_sumber == "1") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-primary">Penerimaan</span>';
                            }
                            else if (row.jenis_sumber == "2") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-success">Produksi</span>';
                            }
                            else if (row.jenis_sumber == "3") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-warning">Transfer Stok</span>';
                            }
                            else if (row.jenis_sumber == "4") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-warning">Konversi Stok</span>';
                            }
                            else if (row.jenis_sumber == "5") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-warning">Adjustment</span>';
                            }
                            else if (row.jenis_sumber == "6") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-danger">Retur</span>';
                            }

                            return statusTxt;
                        },
                        autoHide: false,
                    },
                    {
                        field: 'id_index',
                        title: 'Lokasi',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            if (row.txt_index != null) {
                                return row.txt_index.toUpperCase();
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'stok_item',
                        width: 'auto',
                        title: 'Qty',
                        textAlign: 'center',
                        autoHide: true,
                        template: function(row) {
                            if (row.stok_item != null) {
                                return parseFloat(row.stok_item).toLocaleString('id-ID', { maximumFractionDigits: 2});
                            }
                            else {
                                return '0';
                            }
                        },
                    },
                ],
            });
        });

        function viewDetailLokasi(id, idSatuan) {

            var datatable = $('#list_item_lokasi').KTDatatable();
                datatable.setDataSourceParam('idProduct', id);
                datatable.setDataSourceParam('idSatuan', idSatuan);
                datatable.reload();

            $("#txtNamaLokasi").text($("#txt_"+id).text().toUpperCase());

        }

    </script>
@endsection
