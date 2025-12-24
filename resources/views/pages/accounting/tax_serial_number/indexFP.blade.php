@extends('layout.default')
@section('content')
	<!-- Content area -->
			<div class="content">
			@include('pages.alerts')
				<!-- Basic initialization -->
				<div class="card card-custom">

                    <div class="card-header bg-primary header-elements-sm-inline">
                        <div class="card-title">
                            <h3 class="card-label text-white">Daftar Faktur Pajak</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            {{-- @if($hakAkses->add == "Y")
                            <button class="btn btn-primary btn-outline-white mr-2" onclick="window.location.href = '{{ url('SalesInvoice/Add') }}';">
                                <i class="flaticon2-plus"></i>
                                Buat Baru
                            </button>
                            @endif --}}
                            @if($hakAkses->export == "Y")
                            <button type="button" class="btn btn-success font-weight-bold mr-2 btnExport" id="btnExportXML" value="xml" > Export XML <i class="fas fa-file-code"></i></button>
                            <button type="button" class="btn btn-success font-weight-bold mr-2 btnExport" id="btnExportExcel" value="excel" > Export Excel <i class="fas fa-file-excel"></i></button>
                            @endif
                            <!--end::Button-->
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="" id="form_add" method="POST" enctype="multipart/form-data">
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
                                                        <input type="text" class="form-control" placeholder="Search..." id="table_inv_sale_search_query"/>
                                                        <span>
                                                            <i class="flaticon2-search-1 text-muted"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 my-2 my-md-0">
                                                <div class="align-items-center">
                                                    <label class="mr-3 mb-0 d-none d-md-block">Nama Pelanggan :</label>
                                                    <select class="form-control select2" id="table_inv_sale_search_customer">
                                                        <option value="">All</option>
                                                        @foreach($dataCustomer as $rowCustomer)
                                                        <option value="{{$rowCustomer->nama_customer}}">{{ucwords($rowCustomer->nama_customer)}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2 my-2 my-md-0">
                                                <div class="align-items-center">
                                                    <label class="mr-3 mb-0 d-none d-md-block">Status :</label>
                                                    <select class="form-control select2" id="table_inv_sale_search_status">
                                                        <option value="">All</option>
                                                        <option value="1">Sudah Export</option>
                                                        <option value="0">Belum Export</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3 my-2 my-md-0">
                                                <div class=" align-items-center">
                                                    <label class="mr-3 mb-0 d-none d-md-block">Periode Invoice :</label>
                                                    <input type="hidden" class="form-control" id="bulan_picker_val" name="bulan_picker_val" >
                                                    <input type="hidden" class="form-control" id="id_invoices" name="id_invoices" >
                                                    <input type="text" class="form-control" id="bulan_picker" name="bulan_picker" autocomplete="off" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end: Search Form-->

                            <!--begin: Datatable-->

                            <div class="datatable datatable-bordered datatable-head-custom" id="table_inv_sale"></div>

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
            $('#table_inv_sale_search_customer').select2({
                allowClear: true
            });

            $('#table_inv_sale_search_status').select2({
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
            var bulanDate = new Date($(this).datepicker('getDate'));

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

        $("#table_inv_sale").on("click", "table .checkAll", function(){
            $("#table_inv_sale input:checkbox").prop('checked', $(this).prop("checked"));
        });

		$(document).ready(function() {

            var datatable = $('#table_inv_sale').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/FakturPajak/GetData',
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
                    height: 'auto',
                    footer: false
                },

                sortable: true,

                filterable: true,

                pagination: true,

                search: {
                    input: $('#table_inv_sale_search_query')
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
                        field: 'checkbox',
                        sortable: false,
                        autoHide: false,
                        title: "<div class='checkbox-inline align-items-center'><label class='checkbox checkbox-lg'><input type='checkbox' id='checkAll' class='text-center checkAll'><span></span></label></div>",
                        textAlign: 'center',
                        width: 50,
                        template: function(row) {
                            var txtCheckbox = "<div class='checkbox-inline align-items-center'>";
                                txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                txtCheckbox += "<input type='checkbox' class='text-center bayarSekaligus' value='"+row.id+"'>";
                                txtCheckbox += "<span></span>";
                                txtCheckbox += "</label>";
                                txtCheckbox += "</div>";

                            if (row.flag_batal == 2) {
                                return "";
                            }
                            else if (row.flag_batal == 1) {
                                return "";
                            }
                            else if (row.pembetulan == 1 && row.id_parent != null) {
                                return txtCheckbox;
                            }
                            else if (row.pembetulan == 0 && row.id_parent == null) {
                                return txtCheckbox;
                            }
                            // if (row.flag_export == 1) {
                            //     return "";
                            // }
                            // else {
                            //     return txtCheckbox;
                            // }
                        },
                    },
                    {
                        field: 'nomor_faktur',
                        title: 'No. Faktur',
                        textAlign: 'left',
                        width: 180,
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bolder">'+row.nomor_faktur.toUpperCase()+'</span><br />';
                                txt += '<span class="label label-md label-outline-primary label-inline">No. Inv : '+row.kode_invoice.toUpperCase()+'</span><br />';
                                txt += '<span class="label label-md label-outline-primary label-inline">No. SO : '+row.no_so.toUpperCase()+'</span><br />';

                                return txt;
                        },
                    },
                    {
                        field: 'nama_customer',
                        title: 'Nama Pelanggan',
                        width: 180,
                        textAlign: 'left',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bold">'+row.nama_customer+'</span>';
                                txt += "<br />";
                                txt += '<span class="label label-md label-outline-primary label-inline mt-1">' + row.npwp_customer +'</span>';
                                return txt;
                        },
                    },
                    {
                        field: 'grand_total',
                        title: 'Grand Total',
                        textAlign: 'right',
                        width: 100,
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bold">'+parseFloat(row.grand_total).toLocaleString('id-ID', { maximumFractionDigits: 2})+'</span><br />';
                                return txt;
                        },
                    },
                    {
                        field: 'subtotal',
                        title: 'Subtotal',
                        textAlign: 'right',
                        width: 100,
                        autoHide: true,
                        template: function(row) {
                                return parseFloat(row.dpp).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },

                    {
                        field: 'ppn',
                        title: 'PPn',
                        textAlign: 'right',
                        width: 80,
                        autoHide: true,
                        template: function(row) {
                                return parseFloat(row.ppn).toLocaleString('id-ID', { maximumFractionDigits: 2})
                        },
                    },
                    {
                        field: 'ttl_qty',
                        title: 'Jumlah Barang',
                        textAlign: 'right',
                        width: 50,
                        autoHide: true,
                        template: function(row) {
                            return parseFloat(row.ttl_qty).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'tanggal_faktur',
                        title: 'Tanggal Faktur',
                        width: 75,
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            //row.durasi_jt
                            var txt = "";
                                txt += '<span class="font-weight-bold">'+formatDate(row.tanggal_faktur)+'</span>';
                                return txt;
                        },
                    },
                    {
                        field: 'flag_export',
                        title: 'Status',
                        textAlign: 'center',
                        width: 120,
                        autoHide: false,
                        template: function(row) {
                            var statusTxt = "";

                            if (row.flag_batal == 2) {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-warning">Diganti</span>';
                            }
                            else if (row.flag_batal == 1) {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-danger">Batal</span>';
                            }
                            else if (row.pembetulan == 1 && row.id_parent != null) {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-warning">Normal-Pengganti</span>';
                            }
                            else if (row.pembetulan == 0 && row.id_parent == null) {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-success">Normal</span>';
                            }

                            statusTxt += '<br />';
                            if (row.flag_export == "0") {
                                statusTxt += '<span class="label label-md font-weight-bold label-pill label-inline">Belum Export</span>';
                            }
                            else if (row.flag_export == "1") {
                                statusTxt += '<span class="label label-md font-weight-bold label-pill label-inline label-primary">Sudah Export</span>';
                            }

                            return statusTxt;
                        },
                    },
                    {
                        field: 'Actions',
                        title: 'Aksi',
                        sortable: false,
                        width: 50,
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

                                txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('FakturPajak.Detail', 'idInv')}}'><i class='nav-icon la la-eye'></i><span class='nav-text'>Detail</span></a></li>";
                                txtAction += '</ul>';
                                txtAction += '</div>';
                                txtAction += '</div>';
                                txtAction = txtAction.replaceAll('idInv',row.id);
                                return txtAction;
                        },
                    }
                ],
            });

            $('#table_inv_sale_search_customer').on('change', function() {
                datatable.search($(this).val(), 'nama_customer');
            });

            $('#table_inv_sale_search_status').on('change', function() {
                datatable.search($(this).val(), 'flag_export');
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
                        url: "/SalesInvoice/Delete",
                        method: 'POST',
                        data: {
                            idSalesInvoice: id
                        },
                        success: function(result){
                            Swal.fire(
                                "Sukses!",
                                "Data Berhasil dihapus!.",
                                "success"
                            )
                        }
                    });
                    var datatable = $("#table_inv_sale").KTDatatable();
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

        // $(document).ready(function () {
        //     $("#btnExport").on('click', function(e) {
        //         var errCount = 0;

        //         var invoiceIDs = $("#table_inv_sale input:checkbox:checked").map(function(){
        //                             return $(this).val();
        //                         }).get();

        //         if ($("#bulan_picker_val").val() == "" && invoiceIDs.length == 0) {
        //             Swal.fire(
        //                     "Export Faktur Pajak Gagal!",
        //                     "Silahkan pilih faktur pajak atau periode terlebih dahulu!",
        //                     "warning"
        //                 );
        //                 errCount = errCount + 1;
        //         }

        //         if (errCount == 0) {
        //             Swal.fire({
        //                 title: "Export Faktur Pajak?",
        //                 text: "Apakah periode sudah sesuai?",
        //                 icon: "warning",
        //                 showCancelButton: true,
        //                 confirmButtonText: "Ya",
        //                 cancelButtonText: "Tidak",
        //                 reverseButtons: false
        //             }).then(function(result) {
        //                 if(result.value) {
        //                     $("#id_invoices").val(invoiceIDs);
        //                     $("#form_add").off("submit").submit();
        //                     var datatable = $("#table_inv_sale").KTDatatable();
        //                         datatable.reload();
        //                 }
        //                 else if (result.dismiss === "cancel") {
        //                     e.preventDefault();
        //                 }
        //             });
        //         }
        //     });
        // });

        $(".btnExport").on("click", function(e){
            var btn = $(this).val();
            var errCount = 0;

            var invoiceIDs = $("#table_inv_sale input:checkbox:checked").map(function(){
                                return $(this).val();
                            }).get();

            if ($("#bulan_picker_val").val() == "" && invoiceIDs.length == 0) {
                Swal.fire(
                        "Export Faktur Pajak Gagal!",
                        "Silahkan pilih faktur pajak atau periode terlebih dahulu!",
                        "warning"
                    );
                    errCount = errCount + 1;
            }

            if (errCount == 0) {
                Swal.fire({
                    title: "Export Faktur Pajak dengan format " + ucwords(btn) + "?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya",
                    cancelButtonText: "Tidak",
                    reverseButtons: false
                }).then(function(result) {
                    if(result.value) {
                        e.preventDefault();
                        if (btn == "xml") {
                            $("#form_add").attr('action', "{{ route('FakturPajakXML.Export') }}");
                        }
                        else if (btn == "excel") {
                            $("#form_add").attr('action', "{{ route('FakturPajak.Export') }}");
                        }
                        $("#id_invoices").val(invoiceIDs);
                        $("#form_add").off("submit").submit();
                    }
                    else if (result.dismiss === "cancel") {
                        $("html, body").animate({ scrollTop: 0 }, "slow");
                        e.preventDefault();
                    }
                });
            }
		});

    </script>
@endsection
