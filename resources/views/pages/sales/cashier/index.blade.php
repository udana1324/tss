@extends('layout.default')
@section('content')
	<!-- Content area -->
			<div class="content">
			@include('pages.alerts')
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
                        <div class="card-title">
                            <h3 class="card-label text-white">Daftar Penjualan Kasir</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            @if($hakAkses->add == "Y")
                            <button class="btn btn-primary btn-outline-white mr-2" onclick="window.location.href = '{{ url('Cashier/Add') }}';">
                                <i class="flaticon2-plus"></i>
                                Buat Baru
                            </button>
                            @endif
                            {{-- @if($hakAkses->export == "Y")
                            <button type="button" class="btn btn-success font-weight-bold mr-2" id="btnExport" onclick="ExportExcel('T','table_cashier','Penawaran','xlsx');"> Export <i class="fas fa-file-excel"></i></button>
                            @endif --}}
                            <!--end::Button-->
                        </div>
					</div>

					<div class="card-body">

                        <!--begin::Search Form-->
                        <div class="mb-7">
                            <div class="row align-items-center">
                                <div class="col-lg-12 col-xl-8">
                                    <div class="row align-items-center">
                                        <div class="col-md-3 my-2 my-md-0">
                                            <div class="align-items-center">
                                                <label style="display: inline-block;"></label>
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" placeholder="Search..." id="table_cashier_search_query"/>
                                                    <span>
                                                        <i class="flaticon2-search-1 text-muted"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 my-2 my-md-0">
                                            <div class=" align-items-center">
                                                <label class="mr-3 mb-0 d-none d-md-block">Periode :</label>
                                                <input type="text" class="form-control" id="bulan_picker" name="bulan_picker" autocomplete="off" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end: Search Form-->

                        <!--begin: Datatable-->

                        <div class="datatable datatable-bordered datatable-head-custom" id="table_cashier"></div>

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

            $('#table_cashier_search_status').select2({
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

            var datatable = $('#table_cashier').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Cashier/GetData',
                            method: 'GET',

                        }
                    },
                    pageSize: 10,
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
                    input: $('#table_cashier_search_query')
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
                        field: 'user_name',
                        title: 'Kasir',
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            return ucwords(row.user_name);
                        },
                    },
                    {
                        field: 'no_ref',
                        title: 'No. Ref.',
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            return row.no_ref.toUpperCase();
                        },
                    },
                    {
                        field: 'nama_customer',
                        title: 'Customer',
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            return ucwords(row.nama_customer);
                        },
                    },
                    {
                        field: 'tanggal_penjualan',
                        title: 'Tanggal Penjualan',
                        textAlign: 'center',
                        width: 'auto',
                        template: function(row) {
                            if (row.tanggal_penjualan != null) {
                                return formatDate(row.tanggal_penjualan);
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'jumlah_total_qty',
                        title: 'Qty Penjualan',
                        textAlign: 'right',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                txt += parseFloat(row.jumlah_total_qty).toLocaleString('id-ID', { maximumFractionDigits: 0});
                                // if (row.nominal_dp !=0) {
                                //     txt += '<span class="label label-md label-outline-warning label-inline mt-1">DP : '+parseFloat(row.nominal_dp).toLocaleString('id-ID', { maximumFractionDigits: 0})+'</span><br />';
                                // }

                                // if (row.flag_pembayaran == '2' && row.flag_tf == "1") {
                                //     txt += '<span class="label label-light-primary label-inline mt-1">Bayar Sebagian</span>';
                                // }
                                // else if (row.flag_pembayaran == '1' && row.flag_tf == "1") {
                                //     txt += '<span class="label label-light-success label-inline mt-1">Lunas</span>';
                                // }
                                // else {
                                //     txt += '<span class="label label-light-dark label-inline mt-1">Belum Bayar</span>';
                                // }
                                return txt;
                        },
                    },
                    {
                        field: 'nominal_total',
                        title: 'Total Penjualan (Rp)',
                        textAlign: 'right',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                txt += parseFloat(row.nominal_total).toLocaleString('id-ID', { maximumFractionDigits: 0});
                                return txt;
                        },
                    },
                    {
                        field: 'metode_pembayaran',
                        title: 'Metode Pembayaran',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            return ucwords(row.metode_pembayaran);
                        },
                    },
                    {
                        field: 'status_sales',
                        title: 'Status',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            var statusTxt = "";
                            if (row.flag_revisi == '1') {
                                statusTxt = ucwords(row.status_sales)+"-R";
                            }
                            else {
                                statusTxt = ucwords(row.status_sales);
                            }

                            statusTxt = statusTxt.replace("_", " ");

                            if (row.status_sales == "draft") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline">'+statusTxt+'</span>';
                            }
                            else if (row.status_sales == "posted") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-primary">'+statusTxt+'</span>';
                            }
                            else if (row.status_sales == "request_revisi") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-warning">'+statusTxt+'</span>';
                            }
                            else if (row.status_sales == "revisi") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-warning">'+statusTxt+'</span>';
                            }
                            else if (row.status_sales == "batal") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-danger">'+statusTxt+'</span>';
                            }
                            else if (row.status_sales == "full") {
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

                                if (row.status_sales == "posted") {
                                    txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('Cashier.Detail', 'idPricing')}}'><i class='nav-icon la la-eye'></i><span class='nav-text'>Detail</span></a></li>";
                                }
                                if (row.status_sales == "request revisi") {
                                    txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('Cashier.edit', 'idPricing')}}'><i class='nav-icon la la-edit'></i><span class='nav-text'>Ubah</span></a></li>";
                                }
                                if (akses.delete) {
                                    // txtAction += "<li class='nav-item'><a class='nav-link' href='#' onclick='deleteData("+row.id+");return false;''><i class='nav-icon la la-trash'></i><span class='nav-text'>Hapus</span></a></li>";
                                }
                                txtAction += '</ul>';
                                txtAction += '</div>';
                                txtAction += '</div>';
                                txtAction = txtAction.replaceAll('idPricing',row.id);
                                return txtAction;
                        },
                    }
                ],
            });

            $('#table_cashier_search_status').on('change', function() {
                datatable.search($(this).val(), 'status_sales');
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
                        url: "/ProductSpecialPricing/Delete",
                        method: 'POST',
                        data: {
                            idPurchaseOrder: id
                        },
                        success: function(result){
                            Swal.fire(
                                "Sukses!",
                                "Data Berhasil dihapus!.",
                                "success"
                            )
                        }
                    });
                    var datatable = $("#table_cashier").KTDatatable();
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
