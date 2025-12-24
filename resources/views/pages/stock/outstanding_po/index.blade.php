@extends('layout.default')
@section('content')
	<!-- Content area -->
			<div class="content">
			@include('pages.alerts')
				<!-- Basic initialization -->
                <form action="{{ route('OutstandingPO.Export') }}" class="form-horizontal" id="form_add" method="POST" enctype="multipart/form-data">
				<div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
                        <div class="card-title">
                            <h3 class="card-label text-white">Outstanding Purchase Order</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            @if($hakAkses->export == "Y")

                                    {{ csrf_field() }}
                                    <button type="button" class="btn btn-success font-weight-bold mr-2" id="btnExport" >Export Outstanding PO <i class="fas fa-file-excel"></i></button>

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
                                            <div class="align-items-center">
                                                <label style="display: inline-block;"></label>
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" placeholder="Search..." id="table_outs_po_search_query"/>
                                                    <span>
                                                        <i class="flaticon2-search-1 text-muted"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 my-2 my-md-0">
                                            <div class="align-items-center">
                                                <label class="mr-3 mb-0 d-none d-md-block">Supplier: </label>
                                                <select class="form-control select2" id="table_outs_po_search_supplier" name="id_supplier">
                                                    <option value="">All</option>
                                                    @foreach($dataSupplier as $rowSupplier)
                                                    <option value="{{$rowSupplier->nama_supplier}}">{{ucwords($rowSupplier->nama_supplier)}}</option>
                                                    @endforeach
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

                        <div class="datatable datatable-bordered datatable-head-custom" id="table_outs_po"></div>

                        <!--end: Datatable-->
                    </div>
				</div>
                </form>
				<!-- /basic initialization -->
			</div>
			<!-- /content area -->
@endsection
@section('scripts')
	<script type="text/javascript">
		//$('div.alert').delay(5000).slideUp(300);
        $(document).ready(function () {
            $('#table_outs_po_search_supplier').select2({
                allowClear: true,
                placeholder: "Pilih Supplier..."
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

		$(document).ready(function() {

            var datatable = $('#table_outs_po').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/OutstandingPO/GetDataOutstandingPO',
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
                    height: "auto",
                    footer: false
                },

                sortable: true,

                filterable: true,

                pagination: true,

                search: {
                    input: $('#table_outs_po_search_query')
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
                        field: 'tanggal_po',
                        title: 'Tanggal PO',
                        width: 'auto',
                        textAlign: 'left',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                            //var dt = new Date();
                            //var time = "Tahun " + dt.getYear() + " Bulan " + dt.getMonth() + " Tanggal " + dt.getDate() + "<br>";

                                txt += '<span class="font-weight-bold">'+formatDate(row.tanggal_po)+'</span>';
                                txt += "<br />";

                                //if(time > row.tanggal_deadline){
                                //    txt += '<span class="label label-md label-dot label-danger mr-2"></span> <span class="font-weight-bold text-inline text-danger font-size-xs"> DEADLINE : ' + formatDate(row.tanggal_deadline) + '</span>';
                                //}
                                return txt;
                        },
                    },
                    {
                        field: 'nama_supplier',
                        title: 'idSupp',
                        width: 20,
                        textAlign: 'center',
                        visible:false,
                    },
                    {
                        field: 'no_po',
                        width: 'auto',
                        title: 'Nomor Transaksi',
                        autoHide: false,
                        textAlign: 'left',
                        template: function(row) {
                            var txt = "";
                                txt += "<a class='text-secondary text-hover-primary' href='{{route('PurchaseOrder.Detail', 'idPo')}}' target='_blank'>";
                                txt += '<span class="font-weight-bold">'+row.no_po.toUpperCase()+'</span>';
                                txt += "</a>";
                                txt += "<br />";
                                txt += '<span class="label label-md label-outline-primary label-inline mt-1">' + row.nama_supplier + '</span>';
                                txt = txt.replaceAll('idPo',row.id);
                                return txt;
                        },
                    },
                    {
                        field: 'nama_item',
                        title: 'Nama Barang',
                        autoHide: false,
                        autoHide: false,
                        width: 'auto',
                        textAlign: 'left',
                        template: function(row) {
                            return ucwords(row.nama_item);
                        }
                    },
                    {
                        field: 'outstanding_qty',
                        title: 'Jumlah Outstanding',
                        autoHide: false,
                        autoHide: false,
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            return parseFloat(row.outstanding_qty).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'nama_satuan',
                        width: 'auto',
                        title: 'Satuan',
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
                        field: 'harga_beli',
                        title: 'Harga',
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            return parseFloat(row.harga_beli).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                ],
            });

            $('#table_outs_po_search_supplier').on('change', function() {
                datatable.search($(this).val(), 'nama_supplier');
            });

            $("#bulan_picker").on('change', function() {
                var bulanDate = $("#bulan_picker").data('datepicker').getFormattedDate('yyyy-mm-dd');
                datatable.setDataSourceParam('periode', bulanDate);
                datatable.reload();
            });

        });

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
                    title: "Cetak Outstanding?",
                    text: "Apakah ingin mencetak Outstanding?",
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

    </script>
@endsection
