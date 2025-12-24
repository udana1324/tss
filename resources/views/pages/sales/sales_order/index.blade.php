@extends('layout.default')
@section('content')
	<!-- Content area -->
			<div class="content">
			@include('pages.alerts')
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
                        <div class="card-title">
                            <h3 class="card-label text-white">Daftar Penjualan (Sales Order)</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            @if($hakAkses->add == "Y")
                            <button class="btn btn-primary btn-outline-white mr-2" onclick="window.location.href = '{{ url('SalesOrder/Add') }}';">
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
                        <form action="{{ route('SalesOrder.Export') }}" class="form-horizontal" id="form_add" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <!--begin::Search Form-->
                        <div class="mb-7">
                            <div class="row align-items-center">
                                <div class="col-lg-10">
                                    <div class="row align-items-center">
                                        <div class="col-md-3 my-2 my-md-0">
                                            <div class="align-items-center">
                                                <label style="display: inline-block;"></label>
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" placeholder="Search..." id="table_so_search_query"/>
                                                    <span>
                                                        <i class="flaticon2-search-1 text-muted"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 my-2 my-md-0">
                                            <div class="align-items-center">
                                                <label class="mr-3 mb-0 d-none d-md-block">Nama Pelanggan :</label>
                                                <select class="form-control select2" id="table_so_search_customer" name="id_customer">
                                                    <option value="">All</option>
                                                    @foreach($dataCustomer as $rowCustomer)
                                                    <option value="{{$rowCustomer->id}}">{{ucwords($rowCustomer->nama_customer)}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2 my-2 my-md-0">
                                            <div class="align-items-center">
                                                <label class="mr-3 mb-0 d-none d-md-block">Status :</label>
                                                <select class="form-control select2" id="table_so_search_status" name="status_so">
                                                    <option value="">All</option>
                                                    @foreach($dataStatus as $rowStatus)
                                                    <option value="{{$rowStatus->status_so}}">{{ucwords($rowStatus->status_so)}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 my-2 my-md-0">
                                            <div class=" align-items-center">
                                                <label class="mr-3 mb-0 d-none d-md-block">Periode Penjualan :</label>
                                                <input type="hidden" class="form-control" id="bulan_picker_val" name="bulan_picker_val" >
                                                <input type="text" class="form-control" id="bulan_picker" name="bulan_picker" autocomplete="off" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end: Search Form-->

                        <!--begin: Datatable-->

                        <div class="datatable datatable-bordered datatable-head-custom" style="overflow-y: hidden !important;" id="table_so"></div>

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
            $('#table_so_search_customer').select2({
                allowClear: true
            });

            $('#table_so_search_status').select2({
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
        });

        $("#bulan_picker").on('change', function() {

            $("#bulan_picker_val").val($("#bulan_picker").data('datepicker').getFormattedDate('yyyy-mm-dd'));

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

            var datatable = $('#table_so').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/SalesOrder/GetData',
                            method: 'GET',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
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
                    width: 'auto',
                    footer: false
                },

                sortable: true,

                filterable: true,

                pagination: true,

                search: {
                    input: $('#table_so_search_query')
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
                    },
                    {
                        field: 'id_customer',
                        title: '#',
                        sortable: false,
                        width: 'auto',
                        selector: false,
                        textAlign: 'center',
                        visible:false,
                    },
                    {
                        field: 'no_so',
                        title: 'Nomor SO',
                        width: 'auto',
                        textAlign: 'left',
                        autoHide: false,
                        template: function(row) {
                        var txt = "";
                            txt += '<span class="font-weight-bold">'+row.no_so.toUpperCase()+'</span>';
                            if (row.outstanding_so != row.jumlah_total_so && row.outstanding_so != 0 && row.status_so != "close") {
                                txt += '<br><span class="label label-md label-dot label-danger mr-1"></span> <span class="font-weight-bold text-inline text-danger font-size-xs">TERKIRIM SEBAGIAN</span>';
                            }
                            return txt;
                        },
                    },
                    {
                        field: 'nama_customer',
                        width: '200',
                        title: 'Pelanggan',
                        textAlign: 'left',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                            txt += '<span class="font-weight-bold">'+ucwords(row.nama_customer)+'</span>';
                            txt += "<br />";
                            txt += '<span class="label label-md label-outline-primary label-inline mr-2 mb-1 mt-1">' + row.nama_outlet + '</span>';
                            txt += "<br />";

                            if (row.no_po_customer != null) {
                                txt += '<span class="font-weight-bold text-inline text-primary font-size-xs">No. PO : '+row.no_po_customer.toUpperCase()+'</span>';
                            }
                            else {
                                txt += '<span class="font-weight-bold text-inline text-primary font-size-xs">No. PO : - </span>';
                            }
                            return txt;
                        },
                    },
                    {
                        field: 'tanggal_so',
                        title: 'Tanggal Penjualan',
                        textAlign: 'center',
                        width: '126',
                        autoHide: false,
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
                        field: 'jumlah_total_so',
                        title: 'Jumlah',
                        textAlign: 'right',
                        autoHide: true,
                        template: function(row) {
                            return parseFloat(row.jumlah_total_so).toLocaleString('id-ID', { maximumFractionDigits: 2});
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
                        textAlign: 'right',
                        width: '215',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                            txt += parseFloat(row.nominal_so_ttl).toLocaleString('id-ID', { maximumFractionDigits: 2});
                            txt += "<br />";
                            if (row.nominal_dp !=0) {
                                txt += '<span class="label label-md label-outline-warning label-inline mt-1">DP : '+parseFloat(row.nominal_dp).toLocaleString('id-ID', { maximumFractionDigits: 2})+'</span><br />';
                            }
                            if (row.metode_pembayaran == 'cash') {
                                txt += '<span class="label label-md label-outline-success label-inline mt-1">TUNAI</span>';
                            }
                            else {
                                txt += '<span class="label label-md label-outline-warning label-inline mt-1">Kredit '+row.durasi_jt+' Hari</span>';
                            }
                            return txt;
                        },
                    },
                    {
                        field: 'status_so',
                        title: 'Status',
                        textAlign: 'center',
                        width: '170',
                        autoHide: false,
                        template: function(row) {
                            var statusTxt = "";
                            if (row.flag_revisi == '1') {
                                statusTxt = ucwords(row.status_so)+"-R";
                            }
                            else {
                                statusTxt = ucwords(row.status_so);
                            }

                            if (row.status_so == "draft") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline">'+statusTxt+'</span>';
                            }
                            else if (row.status_so == "posted") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-primary">'+statusTxt+'</span>';
                            }
                            else if (row.status_so == "close") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-warning">'+statusTxt+'</span>';
                            }
                            else if (row.status_so == "batal") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-danger">'+statusTxt+'</span>';
                            }
                            else if (row.status_so == "full") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-success">'+statusTxt+'</span>';
                            }
                            return statusTxt;
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

                                txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('SalesOrder.Detail', 'idSo')}}'><i class='nav-icon la la-eye'></i><span class='nav-text'>Detail</span></a></li>";
                                if (akses.edit == "Y" && row.status_so == "draft") {
                                    txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('SalesOrder.edit', 'idSo')}}'><i class='nav-icon la la-edit'></i><span class='nav-text'>Ubah</span></a></li>";
                                }
                                if (akses.delete) {
                                    // txtAction += "<li class='nav-item'><a class='nav-link' href='#' onclick='deleteData("+row.id+");return false;''><i class='nav-icon la la-trash'></i><span class='nav-text'>Hapus</span></a></li>";
                                }
                                txtAction += '</ul>';
                                txtAction += '</div>';
                                txtAction += '</div>';
                                txtAction = txtAction.replaceAll('idSo',row.id);
                                return txtAction;
                        },
                    }
                ],
            });

            $('#table_so_search_customer').on('change', function() {
                datatable.search($(this).val(), 'id_customer');
            });

            $('#table_so_search_status').on('change', function() {
                datatable.search($(this).val(), 'status_so');
            });

            $("#bulan_picker").on('change', function() {
                var bulanDate = $("#bulan_picker").data('datepicker').getFormattedDate('yyyy-mm-dd');
                datatable.setDataSourceParam('periode', bulanDate);
                datatable.reload();
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
                    var datatable = $("#table_so").KTDatatable();
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
