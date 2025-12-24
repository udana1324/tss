@extends('layout.default')
@section('content')
	<!-- Content area -->
			<div class="content">
			@include('pages.alerts')
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
                        <div class="card-title">
                            <h3 class="card-label text-white">Daftar Pembayaran ke Supplier</h3>
                        </div>
					</div>

					<div class="card-body">

                        <!--begin::Search Form-->
                        <div class="mb-7">
                            <div class="row align-items-center">
                                <div class="col-lg-10">
                                    <div class="row align-items-center">
                                        <div class="col-md-3 my-2 my-md-0">
                                            <div class="align-items-center">
                                                <label style="display: inline-block;"></label>
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" placeholder="Search..." id="table_ap_search_query"/>
                                                <span>
                                                    <i class="flaticon2-search-1 text-muted"></i>
                                                </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 my-2 my-md-0">
                                            <div class="align-items-center">
                                                <label class="mr-3 mb-0 d-none d-md-block">Nama Vendor :</label>
                                                <select class="form-control select2" id="table_ap_search_supplier">
                                                    <option value="">All</option>
                                                    @foreach($dataSupplier as $rowSupplier)
                                                    <option value="{{$rowSupplier->nama_supplier}}">{{ucwords($rowSupplier->nama_supplier)}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 my-2 my-md-0">
                                            <div class="align-items-center">
                                                <label class="mr-3 mb-0 d-none d-md-block">Status </label>
                                                <select class="form-control select2" id="table_ap_search_status">
                                                    <option value="">All</option>
                                                    <option value="Lunas">Lunas</option>
                                                    <option value="Over Limit">Over Limit</option>
                                                    <option value="Hutang Lancar">Hutang Lancar</option>
                                                    <option value="Menunggak">Menunggak</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end: Search Form-->

                        <!--begin: Datatable-->

                        <div class="datatable datatable-bordered datatable-head-custom" id="table_ap"></div>

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
            $('#table_ap_search_supplier').select2({
                allowClear: true
            });

            $('#table_ap_search_status').select2({
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

            var datatable = $('#table_ap').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/AccountPayable/GetData',
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
                    input: $('#table_ap_search_query')
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
                        field: 'nama_supplier',
                        title: 'Nama Supplier',
                        width: 'auto',
                        autoHide: false,
                        textAlign: 'left',
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bold">'+ucwords(row.nama_supplier)+'</span>';
                                txt += "<br />";
                                txt += '<span class="label label-md label-outline-primary label-inline">'+row.kode_supplier.toUpperCase()+'</span>';
                                return txt;
                        },
                    },
                    {
                        field: 'TotalInvoice',
                        title: 'Jumlah Tagihan',
                        autoHide: false,
                        width: 'auto',
                        textAlign: 'right',
                        template: function(row) {
                            var txt = "";
                                txt += parseFloat(row.TotalInvoice).toLocaleString('id-ID', { maximumFractionDigits: 2})+' Faktur';
                                txt += "<br />";
                                if (row.TotalInvoiceJT != 0) {
                                    txt += '<span class="label label-md label-outline-primary label-inline">'+parseFloat(row.TotalInvoiceJT).toLocaleString('id-ID', { maximumFractionDigits: 2})+' Jatuh tempo</span>';
                                }
                                return txt;
                        },
                    },
                    {
                        field: 'TotalTagihan',
                        title: 'Total Tagihan (Rp)',
                        width: 'auto',
                        textAlign: 'right',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bold">'+parseFloat(row.TotalTagihan).toLocaleString('id-ID', { maximumFractionDigits: 2})+'</span>';
                                txt += "<br />";
                                if (row.TotalInvoiceJT != 0) {
                                    txt += '<span class="label label-outline-danger label-pill label-inline">JT : '+parseFloat(row.TotalTagihanJT).toLocaleString('id-ID', { maximumFractionDigits: 2})+'</span>';
                                }
                                return txt;
                        },
                    },
                    {
                        field: 'status',
                        title: 'Status',
                        textAlign: 'left',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            if (parseFloat(row.TotalTagihanJT) == 0 && parseFloat(row.TotalTagihan) == 0) {
                                return '<span class="label label-lg font-weight-bold label-light-success label-inline">LUNAS</span>';
                            }
                            else if (parseFloat(row.TotalTagihan) > 0) {
                                if (parseFloat(row.TotalTagihanJT) == 0) {
                                    return '<span class="label label-lg font-weight-bold label-light-primary label-inline">Hutang Lancar</span>';
                                }
                                else if (parseFloat(row.TotalTagihanJT) > 0) {
                                    return '<span class="label label-lg font-weight-bold label-light-warning label-inline">Menunggak</span>';
                                }
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
                            var txtAction = '<a href="{{route("AccountPayable.Detail", "idAp")}}" class="btn btn-outline-primary btn-sm mr-3"><i class="flaticon-eye"></i>Lihat</a>';
                                txtAction = txtAction.replaceAll('idAp',row.id);
                                return txtAction;
                        },
                    }
                ],
            });

            $('#table_ap_search_supplier').on('change', function() {
                datatable.search($(this).val(), 'nama_supplier');
            });

            $('#table_ap_search_status').on('change', function() {
                datatable.search($(this).val(), 'status');
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
                    var datatable = $("#table_ap").KTDatatable();
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
