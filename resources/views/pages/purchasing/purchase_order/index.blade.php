@extends('layout.default')
@section('content')
	<!-- Content area -->
			<div class="content">
			@include('pages.alerts')
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
                        <div class="card-title">
                            <h3 class="card-label text-white">Daftar Perintah Pembelian</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            @if($hakAkses->add == "Y")
                            <button class="btn btn-primary btn-outline-white mr-2" onclick="window.location.href = '{{ url('PurchaseOrder/Add') }}';">
                                <i class="flaticon2-plus"></i>
                                Buat Baru
                            </button>
                            @endif
                            @if($hakAkses->export == "Y")
                            <button type="button" class="btn btn-success font-weight-bold mr-2" id="btnExport" onclick="ExportExcel('T','table_po','Purchase Order','xlsx');"> Export <i class="fas fa-file-excel"></i></button>
                            @endif
                            <!--end::Button-->
                        </div>
					</div>

					<div class="card-body">

                        <!--begin::Search Form-->
                        <div class="mb-7">
                            <div class="row align-items-center">
                                <div class="col-md-3 mt-2">
                                    <div class="align-items-center">
                                        <label style="display: inline-block;"></label>
                                        <div class="input-icon">
                                            <input type="text" class="form-control" placeholder="Search..." id="table_po_search_query"/>
                                            <span>
                                                <i class="flaticon2-search-1 text-muted"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="align-items-center">
                                        <label class="mr-3">Vendor :</label>
                                        <select class="form-control select2" id="table_po_search_supplier">
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
                                        <select class="form-control select2" id="table_po_search_status">
                                            <option value="">All</option>
                                            @foreach($dataStatus as $rowStatus)
                                            <option value="{{$rowStatus->status_po}}">{{ucwords($rowStatus->status_po)}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class=" align-items-center">
                                        <label class="mr-3">Periode Pembelian :</label>
                                        <input type="text" class="form-control" id="bulan_picker" name="bulan_picker" autocomplete="off" >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end: Search Form-->

                        <!--begin: Datatable-->

                        <div class="datatable datatable-bordered datatable-head-custom" id="table_po"></div>

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
            $('#table_po_search_supplier').select2({
                allowClear: true
            });

            $('#table_po_search_status').select2({
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

            var datatable = $('#table_po').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/PurchaseOrder/GetData',
                            method: 'GET',

                        }
                    },
                    pageSize: 50,
                    serverPaging: true,
                    serverFiltering: false,
                    serverSorting: false,
                    saveState: false
                },

                layout: {
                    scroll: true,
                    height: "auto",
                    footer: false
                },

                sortable: true,

                filterable: true,

                pagination: true,

                search: {
                    input: $('#table_po_search_query')
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
                        field: 'no_po',
                        title: 'Nomor PO',
                        textAlign: 'left',
                        autoHide: false,
                        width: 'auto',
                        template: function(row) {
                            var txt = "";
                            txt += '<span class="font-weight-bold">'+row.no_po.toUpperCase()+'</span>';
                            if (row.outstanding_po != row.jumlah_total_po && row.outstanding_po != 0 && row.status_po != "close") {
                                txt += '<br><span class="label label-md label-dot label-danger mr-1"></span> <span class="font-weight-bold text-inline text-danger font-size-xs">DITERIMA SEBAGIAN</span>';
                            }
                            return txt;
                        },
                    },
                    {
                        field: 'nama_supplier',
                        title: 'Vendor',
                        textAlign: 'left',
                        autoHide: false,
                        width: 'auto',
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bold">'+row.nama_supplier+'</span>';
                                return txt;
                        },
                    },
                    {
                        field: 'jumlah_total_po',
                        title: 'Jumlah Barang',
                        width: 'auto',
                        textAlign: 'right',
                        autoHide: true,
                        template: function(row) {
                            return parseFloat(row.jumlah_total_po).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'outstanding_po',
                        title: 'Outstanding',
                        textAlign: 'right',
                        width: 'auto',
                        autoHide: true,
                        template: function(row) {
                            return parseFloat(row.outstanding_po).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'tanggal_po',
                        title: 'Tanggal Pembelian',
                        textAlign: 'right',
                        autoHide: false,
                        width: 'auto',
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bold">'+formatDate(row.tanggal_po)+'</span>';
                                txt += "<br />";
                                txt += '<span class="label label-md label-outline-warning label-inline"><i class="flaticon-calendar-with-a-clock-time-tools mr-2 text-warning"></i>'+formatDate(row.tanggal_deadline)+'</span>';
                                return txt;
                        },
                    },
                    {
                        field: 'nominal_po_ttl',
                        title: 'Total',
                        textAlign: 'right',
                        autoHide: false,
                        width: 'auto',
                        template: function(row) {
                            var txt = "";
                            txt += '<span class="font-weight-bold">'+parseFloat(row.nominal_po_ttl).toLocaleString('id-ID', { maximumFractionDigits: 2})+'</span>';
                            txt += "<br />";
                            if (parseFloat(row.nominal_dp) > 0) {
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
                        field: 'status_po',
                        title: 'Status',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            var statusTxt = "";
                            if (row.flag_revisi == '1') {
                                statusTxt = ucwords(row.status_po)+"-R";
                            }
                            else {
                                statusTxt = ucwords(row.status_po);
                            }

                            if (row.status_po == "draft") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline">'+statusTxt+'</span>';
                            }
                            else if (row.status_po == "posted") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-primary">'+statusTxt+'</span>';
                            }
                            else if (row.status_po == "close") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-warning">'+statusTxt+'</span>';
                            }
                            else if (row.status_po == "batal") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-danger">'+statusTxt+'</span>';
                            }
                            else if (row.status_po == "full") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-success">'+statusTxt+'</span>';
                            }

                            return statusTxt;

                        },
                    },
                    {
                        field: 'Actions',
                        title: 'Aksi',
                        sortable: false,
                        width: 80,
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

                                txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('PurchaseOrder.Detail', 'idPo')}}'><i class='nav-icon la la-eye'></i><span class='nav-text'>Detail</span></a></li>";
                                if (akses.edit == "Y" && row.status_po == "draft") {
                                    txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('PurchaseOrder.edit', 'idPo')}}'><i class='nav-icon la la-edit'></i><span class='nav-text'>Ubah</span></a></li>";
                                }
                                if (akses.delete) {
                                    // txtAction += "<li class='nav-item'><a class='nav-link' href='#' onclick='deleteData("+row.id+");return false;''><i class='nav-icon la la-trash'></i><span class='nav-text'>Hapus</span></a></li>";
                                }
                                txtAction += '</ul>';
                                txtAction += '</div>';
                                txtAction += '</div>';
                                txtAction = txtAction.replaceAll('idPo',row.id);
                                return txtAction;
                        },
                    }
                ],
            });

            $('#table_po_search_supplier').on('change', function() {
                datatable.search($(this).val(), 'nama_supplier');
            });

            $('#table_po_search_status').on('change', function() {
                datatable.search($(this).val(), 'status_po');
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
                        url: "/PurchaseOrder/Delete",
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
                    var datatable = $("#table_po").KTDatatable();
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
