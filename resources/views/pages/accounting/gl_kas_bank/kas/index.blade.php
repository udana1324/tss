@extends('layout.default')
@section('content')
	<!-- Content area -->
			<div class="content">
			@include('pages.alerts')
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
                        <div class="card-title">
                            <h3 class="card-label text-white">Entri Kas</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            @if($hakAkses->add == "Y")
                            <button class="btn btn-primary btn-outline-white mr-2" onclick="window.location.href = '{{ url('GLKasBank/Kas/Add') }}';">
                                <i class="flaticon2-plus"></i>
                                Buat Baru
                            </button>
                            @endif
                            @if($hakAkses->export == "Y")
                            <button type="button" class="btn btn-success font-weight-bold mr-2" id="btnExport" > Export <i class="fas fa-file-excel"></i></button>
                            @endif
                            <!--end::Button-->
                        </div>
					</div>

					<div class="card-body">
                        <form action="{{ route('GLKasBank.Export') }}" id="form_add" method="POST" enctype="multipart/form-data">
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
                                                        <input type="text" class="form-control" placeholder="Search..." id="table_kasbank_search_query"/>
                                                        <span>
                                                            <i class="flaticon2-search-1 text-muted"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- <div class="col-md-4 my-2 my-md-0">
                                                <div class="align-items-center">
                                                    <label class="mr-3 mb-0 d-none d-md-block">Nama Pelanggan :</label>
                                                    <select class="form-control select2" id="table_kasbank_search_customer">
                                                        <option value="">All</option>
                                                        @foreach($dataCustomer as $rowCustomer)
                                                        <option value="{{$rowCustomer->nama_customer}}">{{ucwords($rowCustomer->nama_customer)}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div> --}}
                                            <div class="col-md-2 my-2 my-md-0">
                                                <div class="align-items-center">
                                                    <label class="mr-3 mb-0 d-none d-md-block">Status :</label>
                                                    <select class="form-control select2" id="table_kasbank_search_status">
                                                        <option value="">All</option>
                                                        @foreach($dataStatus as $rowStatus)
                                                        <option value="{{$rowStatus->status}}">{{ucwords($rowStatus->status)}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3 my-2 my-md-0">
                                                <div class=" align-items-center">
                                                    <label class="mr-3 mb-0 d-none d-md-block">Periode :</label>
                                                    <input type="hidden" class="form-control" id="bulan_picker_val" name="bulan_picker_val" >
                                                    <input type="hidden" class="form-control" id="jenis" name="jenis" value="kas" >
                                                    <input type="text" class="form-control" id="bulan_picker" name="bulan_picker" autocomplete="off" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end: Search Form-->

                            <!--begin: Datatable-->

                            <div class="datatable datatable-bordered datatable-head-custom" style="overflow-y: hidden !important;" id="table_kasbank"></div>

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
            $('#table_kasbank_search_customer').select2({
                allowClear: true
            });

            $('#table_kasbank_search_status').select2({
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

            var datatable = $('#table_kasbank').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/GLKasBank/GetData',
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
                    input: $('#table_kasbank_search_query')
                },

                rows: {
                    autoHide: false
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
                        field: 'tahun',
                        title: 'Tahun',
                        width: 100,
                        textAlign: 'left',
                        template: function(row) {
                            if (row.tanggal_transaksi != null) {
                                var tahun = new Date(row.tanggal_transaksi);
                                return tahun.getFullYear();
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'bulan',
                        width: 100,
                        title: 'Bulan',
                        textAlign: 'left',
                        template: function(row) {
                            if (row.tanggal_transaksi != null) {
                                var bulan = new Date(row.tanggal_transaksi);
                                return bulan.toLocaleString('id-ID', {month: 'long'});
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'nomor_kas_bank',
                        title: 'Nomor Bukti',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            if (row.nomor_kas_bank != null) {
                                return row.nomor_kas_bank.toUpperCase();
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'tanggal_transaksi',
                        title: 'Tanggal',
                        textAlign: 'right',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                txt += formatDate(row.tanggal_transaksi);

                                return txt;
                        },
                    },
                    {
                        field: 'jenis_transaksi',
                        title: 'Jenis Transaksi',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                            if (row.jenis_transaksi == "1") {
                                txt += "Masuk";
                            }
                            else {
                                txt += "Keluar";
                            }

                            return txt;
                        },
                    },
                    {
                        field: 'nominal_transaksi',
                        title: 'Nominal (Rp)',
                        textAlign: 'right',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                            txt += parseFloat(row.nominal_transaksi).toLocaleString('id-ID', { maximumFractionDigits: 2});

                            return txt;
                        },
                    },
                    {
                        field: 'status',
                        title: 'Status',
                        textAlign: 'center',
                        width: '80',
                        autoHide: false,
                        template: function(row) {
                            var statusTxt = "";
                            if (row.flag_revisi == '1') {
                                statusTxt = ucwords(row.status)+"-R";
                            }
                            else {
                                statusTxt = ucwords(row.status);
                            }

                            if (row.status == "draft") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline">'+statusTxt+'</span>';
                            }
                            else if (row.status == "posted") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-primary">'+statusTxt+'</span>';
                            }
                            else if (row.status == "batal") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-danger">'+statusTxt+'</span>';
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

                                txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('GLKasBank.Detail', 'idKasBank')}}'><i class='nav-icon la la-eye'></i><span class='nav-text'>Detail</span></a></li>";
                                if (akses.edit == "Y" && row.status == "draft") {
                                    txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('GLKasBank.edit', 'idKasBank')}}'><i class='nav-icon la la-edit'></i><span class='nav-text'>Ubah</span></a></li>";
                                }
                                if (akses.delete) {
                                    // txtAction += "<li class='nav-item'><a class='nav-link' href='#' onclick='deleteData("+row.id+");return false;''><i class='nav-icon la la-trash'></i><span class='nav-text'>Hapus</span></a></li>";
                                }
                                txtAction += '</ul>';
                                txtAction += '</div>';
                                txtAction += '</div>';
                                txtAction = txtAction.replaceAll('idKasBank',row.id);
                                return txtAction;
                        },
                    }
                ],
            });

            datatable.setDataSourceParam('jenis', 'kas');
            datatable.reload();

            // $('#table_kasbank_search_customer').on('change', function() {
            //     datatable.search($(this).val(), 'nama_customer');
            // });

            $('#table_kasbank_search_status').on('change', function() {
                datatable.search($(this).val(), 'status');
            });

            $("#bulan_picker").on('change', function() {
                var bulanDate = $("#bulan_picker").data('datepicker').getFormattedDate('yyyy-mm-dd');

                $("#bulan_picker_val").val($("#bulan_picker").data('datepicker').getFormattedDate('yyyy-mm-dd'));
                datatable.setDataSourceParam('jenis', 'kas');
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
                        url: "/GLKasBank/Delete",
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
                    var datatable = $("#table_kasbank").KTDatatable();
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

                // var invoiceIDs = $("#table_inv_sale input:checkbox:checked").map(function(){
                //                     return $(this).val();
                //                 }).get();

                // if ($("#bulan_picker_val").val() == "" && invoiceIDs.length == 0) {
                if ($("#bulan_picker_val").val() == "") {
                    Swal.fire(
                            "Export Gagal!",
                            "Silahkan pilih periode terlebih dahulu!",
                            "warning"
                        );
                        errCount = errCount + 1;
                }

                if (errCount == 0) {
                    Swal.fire({
                        title: "Export Entri Kas?",
                        text: "Apakah periode sudah sesuai?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Ya",
                        cancelButtonText: "Tidak",
                        reverseButtons: false
                    }).then(function(result) {
                        if(result.value) {
                            // $("#id_invoices").val(invoiceIDs);
                            $("#form_add").off("submit").submit();
                            // var datatable = $("#table_inv_sale").KTDatatable();
                            //     datatable.reload();
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
