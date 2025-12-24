@extends('layout.default')
@section('content')
	<!-- Content area -->
			<div class="content">
			@include('pages.alerts')
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
                        <div class="card-title">
                            <h3 class="card-label text-white">Daftar Invoice Pembelian</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            @if($hakAkses->add == "Y")
                            <button class="btn btn-primary btn-outline-white mr-2" onclick="window.location.href = '{{ url('PurchaseInvoice/Add') }}';">
                                <i class="flaticon2-plus"></i>
                                Buat Baru
                            </button>
                            @endif
                            @if($hakAkses->export == "Y")
                            <button type="button" class="btn btn-success font-weight-bold mr-2" id="btnExport"> Export <i class="fas fa-file-excel"></i></button>
                            @endif
                            <!--end::Button-->
                        </div>
					</div>

					<div class="card-body">
                        <form action="{{ route('PurchaseInvoice.Export') }}" class="form-horizontal" id="form_add" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <!--begin::Search Form-->
                        <div class="mb-7">
                            <div class="row align-items-center">
                                <div class="col-md-3 mt-2">
                                    <div class="align-items-center">
                                        <label style="display: inline-block;"></label>
                                        <div class="input-icon">
                                            <input type="text" class="form-control" placeholder="Search..." id="table_inv_search_query"/>
                                            <span>
                                                <i class="flaticon2-search-1 text-muted"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="align-items-center">
                                        <label class="mr-3">Vendor :</label>
                                        <select class="form-control select2" id="table_inv_search_supplier" name="id_supplier">
                                            <option value="">All</option>
                                            @foreach($dataSupplier as $rowSupplier)
                                            <option value="{{$rowSupplier->nama_supplier}}">{{ucwords($rowSupplier->nama_supplier)}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class=" align-items-center">
                                        <label>Status Pembayaran :</label>
                                        <select class="form-control select2" id="table_inv_search_status_bayar">
                                            <option value="">All</option>
                                            <option value="1">Lunas</option>
                                            <option value="2">Bayar Sebagian</option>
                                            <option value="3">Belum Bayar</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="align-items-center">
                                        <label class="mr-3">Status :</label>
                                        <select class="form-control select2" id="table_inv_search_status">
                                            <option value="">All</option>
                                            @foreach($dataStatus as $rowStatus)
                                            <option value="{{$rowStatus->status_invoice}}">{{ucwords($rowStatus->status_invoice)}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="align-items-center">
                                        <label class="mr-3">Periode Invoice :</label>
                                        <input type="text" class="form-control" id="bulan_picker" name="bulan_picker" autocomplete="off" >
                                        <input type="hidden" class="form-control" id="bulan_picker_val" name="bulan_picker_val" autocomplete="off" >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end: Search Form-->

                        <!--begin: Datatable-->

                        <div class="datatable datatable-bordered datatable-head-custom" id="table_inv"></div>

                        <!--end: Datatable-->
                        </form>
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
            $('#table_inv_search_supplier').select2({
                allowClear: true
            });

            $('#table_inv_search_status').select2({
                allowClear: true
            });

            $('#table_inv_search_status_bayar').select2({
                allowClear: true
            });

            $('#bulan_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                autoclose : true,
                startView: "months",
                minViewMode: "months",
                format : "MM yyyy",
                clearBtn: true,
            });

            $("#tanggal_picker").daterangepicker({
                locale : {
                    format : 'DD MMM YYYY',
                    cancelLabel: 'Clear'
                }
            },
            function(start, end, label) {
                $("#tanggal_picker_start").val(start.format('YYYY-MM-DD'));
                $("#tanggal_picker_end").val(end.format('YYYY-MM-DD'));
            });

            $('#tanggal_picker').on('cancel.daterangepicker', function(ev, picker) {
                $('#tanggal_picker_start').val('');
                $('#tanggal_picker_end').val('');
                $('#tanggal_picker').val('');
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

            var datatable = $('#table_inv').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/PurchaseInvoice/GetData',
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
                    input: $('#table_inv_search_query')
                },

                // rows: {
                //     autoHide:false
                // },

                columns: [
                    {
                        field: 'id',
                        title: '#',
                        sortable: false,
                        type: 'number',
                        selector: false,
                        textAlign: 'center',
                        visible:false,
                    },
                    {
                        field: 'kode_invoice',
                        title: 'No. Invoice',
                        textAlign: 'left',
                        width: 199,
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bolder">'+row.kode_invoice.toUpperCase()+'</span><br />';
                                txt += '<span class="label label-md label-outline-primary label-inline">'+row.no_po.toUpperCase()+'</span><br />';
                                return txt;
                        },
                    },
                    {
                        field: 'nama_supplier',
                        title: 'Supplier',
                        width: 241,
                        textAlign: 'left',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bold">'+row.nama_supplier+'</span>';
                                return txt;
                        },
                    },
                    {
                        field: 'grand_total',
                        title: 'Grand Total (Rp)',
                        textAlign: 'right',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bold">'+parseFloat(row.grand_total).toLocaleString('id-ID', { maximumFractionDigits: 2})+'</span><br />';

                                if (row.flag_pembayaran == '2') {
                                    txt += '<span class="label label-light-primary label-inline mt-1">Bayar Sebagian</span>';
                                }
                                else if (row.flag_pembayaran == '1') {
                                    txt += '<span class="label label-light-success label-inline mt-1">Lunas</span>';
                                }
                                else {
                                    txt += '<span class="label label-light-dark label-inline mt-1">Belum Bayar</span>';
                                }

                                return txt;
                        },
                    },
                    {
                        field: 'ttl_qty',
                        title: 'Jumlah Barang',
                        textAlign: 'right',
                        autoHide: true,
                        template: function(row) {
                            return parseFloat(row.ttl_qty).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'flag_pembayaran_filter',
                        title: 'Flag Pembayaran Filter',
                        textAlign: 'right',
                        width: 'auto',
                        visible:false,
                    },
                    {
                        field: 'dpp',
                        title: 'DPP (Rp)',
                        textAlign: 'right',
                        autoHide: true,
                        template: function(row) {
                            return parseFloat(row.dpp).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'ppn',
                        title: 'PPN (Rp)',
                        textAlign: 'right',
                        autoHide: true,
                        template: function(row) {
                            return parseFloat(row.ppn).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'tanggal_invoice',
                        title: 'Tanggal Invoice',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bold">'+formatDate(row.tanggal_invoice)+'</span>';
                                txt += "<br />";
                                if (row.tanggal_invoice != row.tanggal_jt) {
                                    txt += '<span class="label label-md label-outline-warning label-inline"><i class="flaticon-calendar-with-a-clock-time-tools mr-2 text-warning"></i>'+formatDate(row.tanggal_jt)+'</span>';
                                }
                                else {
                                    txt += '<span class="label label-md label-outline-success label-inline">TUNAI</span>';
                                }
                                txt += "<br />";
                                return txt;


                            if (row.tanggal_invoice != null) {
                                return formatDate(row.tanggal_invoice);
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'status_invoice',
                        title: 'Status',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            var statusTxt = "";
                            if (row.flag_revisi == '1') {
                                statusTxt = ucwords(row.status_invoice)+"-R";
                            }
                            else {
                                statusTxt = ucwords(row.status_invoice);
                            }

                            if (row.status_invoice == "draft") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline">'+statusTxt+'</span>';
                            }
                            else if (row.status_invoice == "posted") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-primary">'+statusTxt+'</span>';
                            }
                            else if (row.status_invoice == "close") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-warning">'+statusTxt+'</span>';
                            }
                            else if (row.status_invoice == "batal") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-danger">'+statusTxt+'</span>';
                            }
                            else if (row.status_invoice == "full") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-success">'+statusTxt+'</span>';
                            }

                            return statusTxt;
                        },
                    },
                    {
                        field: 'flag_tf',
                        title: 'Status Tukar Faktur',
                        textAlign: 'center',
                        width: 'auto',
                        //autoHide: false,
                        template: function(row) {
                            if (row.flag_tf == '1') {
                                return '<span class="label label-md font-weight-bold label-pill label-inline label-success">Sudah TF</span>';
                            }
                            else {
                                return '<span class="label label-md font-weight-bold label-pill label-inline label-primary">Belum TF</span>';
                            }
                        },
                    },
                    {
                        field: 'Actions',
                        title: 'Aksi',
                        sortable: false,
                        width: 'auto',
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

                                txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('PurchaseInvoice.Detail', 'idInv')}}'><i class='nav-icon la la-eye'></i><span class='nav-text'>Detail</span></a></li>";
                                if (akses.edit == "Y" && row.status_invoice == "draft") {
                                    txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('PurchaseInvoice.edit', 'idInv')}}'><i class='nav-icon la la-edit'></i><span class='nav-text'>Ubah</span></a></li>";
                                }
                                if (akses.delete) {
                                    // txtAction += "<li class='nav-item'><a class='nav-link' href='#' onclick='deleteData("+row.id+");return false;''><i class='nav-icon la la-trash'></i><span class='nav-text'>Hapus</span></a></li>";
                                }
                                txtAction += '</ul>';
                                txtAction += '</div>';
                                txtAction += '</div>';
                                txtAction = txtAction.replaceAll('idInv',row.id);
                                return txtAction;
                        },
                    }
                ],
            });

            $('#table_inv_search_supplier').on('change', function() {
                datatable.search($(this).val(), 'nama_supplier');
            });

            $('#table_inv_search_status').on('change', function() {
                datatable.search($(this).val(), 'status_invoice');
            });

            $("#bulan_picker").on('change', function() {
                var bulanDate = $("#bulan_picker").data('datepicker').getFormattedDate('yyyy-mm-dd');
                $("#bulan_picker_val").val(bulanDate);
                datatable.setDataSourceParam('periode', bulanDate);
                datatable.reload();
            });

            $('#table_inv_search_status_bayar').on('change', function() {
                datatable.search($(this).val(), 'flag_pembayaran_filter');
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
                        url: "/PurchaseInvoice/Delete",
                        method: 'POST',
                        data: {
                            idPurchaseInvoice: id
                        },
                        success: function(result){
                            Swal.fire(
                                "Sukses!",
                                "Data Berhasil dihapus!.",
                                "success"
                            )
                        }
                    });
                    var datatable = $("#table_inv").KTDatatable();
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
                var errCount = 0;

                if (errCount == 0) {
                    Swal.fire({
                        title: "Export Data?",
                        text: "Apakah data sudah sesuai?",
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
                }
            });
        });

    </script>
@endsection
