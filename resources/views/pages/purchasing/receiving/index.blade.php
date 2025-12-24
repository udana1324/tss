@extends('layout.default')
@section('content')
	<!-- Content area -->
			<div class="content">
			@include('pages.alerts')
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
                        <div class="card-title">
                            <h3 class="card-label text-white">Daftar Penerimaan Barang</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            @if($hakAkses->add == "Y")
                            <button class="btn btn-primary btn-outline-white mr-2" onclick="window.location.href = '{{ url('Receiving/Add') }}';">
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
                        <!--begin: Search Form-->
                        <!--begin::Search Form-->
                        <form action="{{ route('Receiving.Export') }}" class="form-horizontal" id="form_add" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="mb-7">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <div class="align-items-center mt-2">
                                            <label style="display: inline-block;"></label>
                                            <div class="input-icon">
                                                <input type="text" class="form-control" placeholder="Search..." id="table_rcv_search_query"/>
                                                <span>
                                                    <i class="flaticon2-search-1 text-muted"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="align-items-center">
                                            <label class="mr-3">Supplier :</label>
                                            <select class="form-control select2" id="table_rcv_search_supplier">
                                                <option value="">All</option>
                                                @foreach($dataSupplier as $rowSupplier)
                                                <option value="{{$rowSupplier->nama_supplier}}">{{ucwords($rowSupplier->nama_supplier)}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="align-items-center">
                                            <label class="mr-3">Status :</label>
                                            <select class="form-control select2" id="table_rcv_search_status">
                                                <option value="">All</option>
                                                @foreach($dataStatus as $rowStatus)
                                                <option value="{{$rowStatus->status_penerimaan}}">{{ucwords($rowStatus->status_penerimaan)}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="align-items-center">
                                            <label class="mr-3">Periode Penerimaan :</label>
                                            <input type="text" class="form-control" id="bulan_picker" name="bulan_picker" autocomplete="off" >
                                            <input type="hidden" class="form-control" id="bulan_picker_val" name="bulan_picker_val" readonly >
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Search Form-->
                            <!--end: Search Form-->
                            <!--begin: Datatable-->

                            <div class="datatable datatable-bordered datatable-head-custom" id="table_rcv"></div>
                        </form>
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
            $('#table_rcv_search_supplier').select2({
                allowClear: true
            });

            $('#table_rcv_search_status').select2({
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

            var datatable = $('#table_rcv').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Receiving/GetData',
                            method: 'GET',

                        }
                    },
                    pageSize: 25,
                    serverPaging: false,
                    serverFiltering: false,
                    serverSorting: false,
                    saveState: false
                },

                layout: {
                    scroll: true,
                    height:'auto',
                    footer: false
                },

                sortable: true,

                filterable: true,

                pagination: true,

                search: {
                    input: $('#table_rcv_search_query')
                },

                columns: [
                    {
                        field: 'id',
                        title: '#',
                        sortable: false,
                        width: 0,
                        type: 'number',
                        visible:false,
                    },
                    {
                        field: 'kode_penerimaan',
                        title: 'No. Penerimaan',
                        width: 'auto',
                        textAlign: 'left',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bold">'+row.kode_penerimaan.toUpperCase()+'</span>';
                                txt += "<br />";
                                txt += '<span class="label label-md label-outline-primary label-inline font-weight-bold">'+row.no_po.toUpperCase()+'</span>';
                                return txt;
                        },
                    },
                    {
                        field: 'nama_supplier',
                        width: 220,
                        title: 'Supplier',
                        textAlign: 'left',
                        autoHide: false,
                        template: function(row) {
                            return ucwords(row.nama_supplier);
                        },
                    },
                    {
                        field: 'no_sj_supplier',
                        title: 'No. SJ Supplier',
                        textAlign: 'left',
                        width: 'auto',
                        autoHide: true,
                        template: function(row) {
                            if (row.no_sj_supplier == null) {
                                return "-";
                            }
                            else {
                                return ucwords(row.no_sj_supplier);
                            }
                        },
                    },
                    {
                        field: 'jumlah_total_sj',
                        title: 'Jumlah',
                        textAlign: 'right',
                        width: 'auto',
                        autoHide: true,
                        template: function(row) {
                            return parseFloat(row.jumlah_total_sj).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'tanggal_sj',
                        title: 'Tanggal SJ',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: true,
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bold">'+formatDate(row.tanggal_sj)+'</span>';
                                txt += "<br />";
                                txt += '<span class="label label-md label-outline-success label-inline"><i class="flaticon-calendar-with-a-clock-time-tools mr-2 text-success"></i>'+formatDate(row.tanggal_terima)+'</span>';
                                return txt;
                        },
                    },
                    {
                        field: 'status_penerimaan',
                        title: 'Status',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: true,
                        template: function(row) {
                            var statusTxt = "";
                            if (row.flag_revisi == '1') {
                                statusTxt = ucwords(row.status_penerimaan)+"-R";
                            }
                            else {
                                statusTxt = ucwords(row.status_penerimaan);
                            }

                            if (row.status_penerimaan == "draft") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline">'+statusTxt+'</span>';
                            }
                            else if (row.status_penerimaan == "posted") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-primary">'+statusTxt+'</span>';
                            }
                            else if (row.status_penerimaan == "close") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-warning">'+statusTxt+'</span>';
                            }
                            else if (row.status_penerimaan == "batal") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-danger">'+statusTxt+'</span>';
                            }
                            else if (row.status_penerimaan == "full") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-success">'+statusTxt+'</span>';
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

                                txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('Receiving.Detail', 'idRcv')}}'><i class='nav-icon la la-eye'></i><span class='nav-text'>Detail</span></a></li>";
                                if (akses.edit == "Y" && row.status_penerimaan == "draft") {
                                    txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('Receiving.edit', 'idRcv')}}'><i class='nav-icon la la-edit'></i><span class='nav-text'>Ubah</span></a></li>";
                                }
                                if (akses.delete) {
                                    // txtAction += "<li class='nav-item'><a class='nav-link' href='#' onclick='deleteData("+row.id+");return false;''><i class='nav-icon la la-trash'></i><span class='nav-text'>Hapus</span></a></li>";
                                }
                                txtAction += '</ul>';
                                txtAction += '</div>';
                                txtAction += '</div>';
                                txtAction = txtAction.replaceAll('idRcv',row.id);
                                return txtAction;
                        },
                    }
                ],
            });

            $('#table_rcv_search_supplier').on('change', function() {
                datatable.search($(this).val(), 'nama_supplier');
            });

            $('#table_rcv_search_status').on('change', function() {
                datatable.search($(this).val(), 'status_penerimaan');
            });

            $("#bulan_picker").on('change', function() {
                var bulanDate = $("#bulan_picker").data('datepicker').getFormattedDate('yyyy-mm-dd');
                $("#bulan_picker_val").val(bulanDate);
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
                        url: "/Receiving/Delete",
                        method: 'POST',
                        data: {
                            idReceiving: id
                        },
                        success: function(result){
                            Swal.fire(
                                "Sukses!",
                                "Data Berhasil dihapus!.",
                                "success"
                            )
                        }
                    });
                    var datatable = $("#table_rcv").KTDatatable();
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
