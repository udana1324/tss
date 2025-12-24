@extends('layout.default')
@section('content')
	<!-- Content area -->
			<div class="content">
			@include('pages.alerts')
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
                        <div class="card-title">
                            <h3 class="card-label text-white">Ringkasan Penjualan berdasarkan Barang</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            @if($hakAkses->export == "Y")
                            <button type="button" class="btn btn-success font-weight-bold mr-2" id="btnExport">Export <i class="fas fa-file-excel"></i></button>
                            @endif
                            <!--end::Button-->
                        </div>
					</div>

					<div class="card-body">
                        <form action="{{ route('RekapPenjualanBarang.Export') }}" class="form-horizontal" id="form_add" method="POST" enctype="multipart/form-data">
                        <!--begin::Search Form-->
                        {{ csrf_field() }}
                        <div class="mb-7">
                            <div class="row align-items-center mb-5">
                                <div class="col-lg-12 col-xl-8">
                                    <div class="row align-items-center">
                                        <div class="col-md-3 my-2 my-md-0">
                                            <div class="align-items-center">
                                                <label style="display: inline-block;"></label>
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" placeholder="Search..." id="table_brg_search_query"/>
                                                    <span>
                                                        <i class="flaticon2-search-1 text-muted"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 my-2 my-md-0">
                                            <div class="align-items-center">
                                                <label class="mr-3 mb-0 d-none d-md-block">Nama Barang :</label>
                                                <select class="form-control select2" id="table_brg_search_item">
                                                    <option value="">All</option>
                                                    @foreach($dataProduct as $rowProduct)
                                                    <option value="{{$rowProduct->kode_item}}">{{ $rowProduct->value_spesifikasi != null ? '('.$rowProduct->value_spesifikasi.')' : "" }}{{strtoupper($rowProduct->kode_item.' - '.$rowProduct->nama_item)}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 my-2 my-md-0">
                                            <div class="align-items-center">
                                                <label class="mr-3 mb-0 d-none d-md-block">Customer :</label>
                                                <select class="form-control select2" id="table_brg_search_cust" name="id_customer">
                                                    <option value="">All</option>
                                                    @foreach($dataCust as $rowCustomer)
                                                    <option value="{{$rowCustomer->id}}">{{strtoupper($rowCustomer->kode_customer.' - '.$rowCustomer->nama_customer)}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 my-2 my-md-0">
                                            <div class="align-items-center">
                                                <label class="mr-3 mb-0 d-none d-md-block">Satuan:</label>
                                                <select class="form-control select2" id="table_brg_search_status">
                                                    <option value="">All</option>
                                                    @foreach($dataSatuan as $rowSatuan)
                                                    <option value="{{$rowSatuan->nama_satuan}}">{{ucwords($rowSatuan->nama_satuan)}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row align-items-center">
                                <div class="col-lg-12 col-xl-8">
                                    <div class="row align-items-center">
                                        <div class="col-md-6 my-2 my-md-0">
                                            <div class="d-flex align-items-center">
                                                <label class="col-lg-4 col-form-label mr-0">Jenis Periode : </label>
                                                <select class="form-control select2" id="jenis_periode" name="jenis_periode">
                                                    <option value=""> </option>
                                                    <option value="harian">Harian</option>
                                                    <option value="bulanan">Bulanan</option>
                                                    <option value="tahunan">Tahunan</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 my-2 my-md-2">
                                            <div class="align-items-center" id="divHarian" style="display: none;">
                                                <label class="col-lg-4 col-form-label mr-0">Tanggal :</label>
                                                <input type="hidden" class="form-control" id="tanggal_picker_start" name="tanggal_picker_start" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}">
                                                <input type="hidden" class="form-control" id="tanggal_picker_end" name="tanggal_picker_end" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}">
                                                <input type="text" class="form-control" id="tanggal_picker" name="tanggal_picker" autocomplete="off" >
                                                <span class="form-text text-danger errTanggal" style="display:none;">*Harap pilih tanggal terlebih dahulu!</span>
                                            </div>

                                            <div class="align-items-center" id="divBulanan" style="display: none;">
                                                <label class="col-lg-4 col-form-label mr-0">Bulan :</label>
                                                <input type="hidden" class="form-control" id="bulan_picker_val" name="bulan_picker_val" >
                                                <input type="text" class="form-control" id="bulan_picker" name="bulan_picker" autocomplete="off" >
                                                <span class="form-text text-danger errBulanan" style="display:none;">*Harap pilih bulan terlebih dahulu!</span>
                                            </div>

                                            <div class="align-items-center" id="divTahunan" style="display: none;">
                                                <label class="col-lg-4 col-form-label mr-0">Tahun :</label>
                                                <input type="hidden" class="form-control" id="tahun_picker_val" name="tahun_picker_val" >
                                                <input type="text" class="form-control" id="tahun_picker" name="tahun_picker" autocomplete="off" >
                                                <span class="form-text text-danger errTahunan" style="display:none;">*Harap pilih tahun terlebih dahulu!</span>
                                            </div>
                                        </div>
                                        <div class="col-md-2 my-2 my-md-0">
                                            <div class="align-items-center">
                                                <button type="button" class="btn btn-primary font-weight-bold" id="btnSearch"><i class="flaticon-search"></i>Cari</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end: Search Form-->
                        </form>
                        <!--begin: Datatable-->

                        <div class="datatable datatable-bordered datatable-head-custom" style="overflow-y: hidden !important;" id="table_brg"></div>

                        <!--end: Datatable-->

                        <!-- Modal form detail sales -->
                        <div id="modal_detail_sales" class="modal fade">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">

                                        <h5 class="modal-title" id="txtNama"></h5>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-7">
                                            <div class="row align-items-center">
                                                <div class="col-lg-12 col-xl-8">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-4 my-2 my-md-0">
                                                            <div class="align-items-center">
                                                                <label style="display: inline-block;"></label>
                                                                <div class="input-icon">
                                                                    <input type="text" class="form-control" placeholder="Search..." id="table_detail_search_query"/>
                                                                    <span>
                                                                        <i class="flaticon2-search-1 text-muted"></i>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 my-2 my-md-0">
                                                            <div class="align-items-center">
                                                                <label class="mr-3 mb-0 d-md-block">Nama Customer :</label>
                                                                <div class="col-10 pl-0">
                                                                <select class="form-control select2"  id="table_detail_search_customer" style="width: 250px;">
                                                                    <option value="">All</option>
                                                                    @foreach($dataCust as $rowCustomer)
                                                                    <option value="{{$rowCustomer->nama_customer}}">{{strtoupper($rowCustomer->nama_customer)}}</option>
                                                                    @endforeach
                                                                </select>
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

                                        <div class="datatable datatable-bordered datatable-head-custom" id="list_item_detail"></div>

                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-link" data-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /form detail delivery -->

                        <!-- Modal form detail sales -->
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
                                                        <div class="col-md-4 my-2 my-md-0">
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
                        <!-- /form detail delivery -->
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
            $('#table_brg_search_cust').select2({
                allowClear: true
            });

            $('#table_detail_search_customer').select2({
                allowClear: true
            });

            $('#table_brg_search_item').select2({
                allowClear: true
            });

            $('#table_brg_search_status').select2({
                allowClear: true
            });

            $('#jenis_periode').select2({
                allowClear: true,
                placeholder: "Klik untuk pilih periode..."
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

            $('#bulan_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                autoclose : true,
                startView: "months",
                minViewMode: "months",
                format : "MM yyyy",
            });

            $("#bulan_picker").on('change', function() {
                var bulanDate = new Date($(this).datepicker('getDate'));

                $("#bulan_picker_val").val($("#bulan_picker").data('datepicker').getFormattedDate('yyyy-mm-dd'));

            });

            $('#tahun_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                autoclose : true,
                startView: "years",
                minViewMode: "years",
                format : "yyyy",
            });

            $("#tahun_picker").on('change', function() {
                var tahunDate = new Date($(this).datepicker('getDate'));

                $("#tahun_picker_val").val($("#tahun_picker").data('datepicker').getFormattedDate('yyyy-mm-dd'));

            });

            $("#jenis_periode").on("change", function () {
                var jenis = $(this).val();
                if (jenis == "harian") {
                    $("#tanggal_picker").addClass("reqHarian");
                    $("#bulan_picker_val").removeClass("reqBulanan");
                    $("#tahun_picker_val").removeClass("reqTahunan");

                    $("#divHarian").addClass("d-flex");
                    $("#divBulanan").removeClass("d-flex");
                    $("#divTahunan").removeClass("d-flex");
                    $("#divHarian").show();
                    $("#divBulanan").hide();
                    $("#divTahunan").hide();
                }
                else if (jenis == "bulanan") {
                    $("#tanggal_picker").removeClass("reqHarian");
                    $("#bulan_picker_val").addClass("reqBulanan");
                    $("#tahun_picker_val").removeClass("reqTahunan");

                    $("#divBulanan").addClass("d-flex");
                    $("#divHarian").removeClass("d-flex");
                    $("#divTahunan").removeClass("d-flex");
                    $("#divHarian").hide();
                    $("#divBulanan").show();
                    $("#divTahunan").hide();
                }
                else if (jenis == "tahunan") {
                    $("#tanggal_picker").removeClass("reqHarian");
                    $("#bulan_picker_val").removeClass("reqBulanan");
                    $("#tahun_picker_val").addClass("reqTahunan");

                    $("#divTahunan").addClass("d-flex");
                    $("#divHarian").removeClass("d-flex");
                    $("#divBulanan").removeClass("d-flex");
                    $("#divHarian").hide();
                    $("#divBulanan").hide();
                    $("#divTahunan").show();
                }
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

            var datatable = $('#table_brg').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/RekapPenjualanBarang/GetDetail',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                        }
                    },
                    pageSize: 50,
                    serverPaging: true,
                    serverFiltering: false,
                    serverSorting: false,
                    saveState: false
                },

                layout: {
                    scroll: false,
                    height: 'auto',
                    footer: false
                },

                rows: {
                    autoHide: false
                },

                sortable: true,

                filterable: true,

                pagination: true,

                search: {
                    input: $('#table_brg_search_query')
                },

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
                        field: 'kode_item',
                        title: 'Nama Barang',
                        width: 160,
                        textAlign: 'left',
                        autoHide: false,
                        template: function(row) {
                            if (parseInt(row.qty_item) == 0 || row.qty_item == null) {
                                if(row.value_spesifikasi != null) {
                                    return '('+row.value_spesifikasi+')'+row.kode_item.toUpperCase() + " - " +row.nama_item.toUpperCase();
                                }
                                else {
                                    return row.kode_item.toUpperCase() + " - " +row.nama_item.toUpperCase();
                                }
                            }
                            else {
                                var txtAction = "<a href='#' data-toggle='modal' data-target='#modal_detail_sales' title='Detail' onclick='viewDetailItem(" + row.id + ", "+  row.id_satuan + ");return false;'>";
                                    if(row.value_spesifikasi != null) {
                                        txtAction += '('+row.value_spesifikasi+')'+row.kode_item.toUpperCase() + " - " +row.nama_item.toUpperCase();
                                    }
                                    else {
                                        txtAction += row.kode_item.toUpperCase() + " - " +row.nama_item.toUpperCase();
                                    }
                                    txtAction += "</a>";
                                return txtAction;
                            }
                        },
                    },
                    {
                        field: 'txtKode',
                        title: 'Txt Barang',
                        autoHide: false,
                        textAlign: 'center',
                        width: 'auto',
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
                        field: 'nama_satuan',
                        title: 'Satuan',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            return ucwords(row.nama_satuan);
                        },
                    },
                    {
                        field: 'qty_item',
                        title: 'Dipesan',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: true,
                        template: function(row) {
                            if (row.qty_item != null) {
                                return parseFloat(row.qty_item).toLocaleString('id-ID', { maximumFractionDigits: 2});
                            }
                            else {
                                return '0';
                            }
                        },
                    },
                    {
                        field: 'qty_kirim',
                        title: 'Terkirim',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: true,
                        template: function(row) {
                            if (row.qty_item != null) {
                                var qty = parseFloat(row.qty_item) - parseFloat(row.qty_outstanding);
                                var txtAction = "<a href='#' data-toggle='modal' data-target='#modal_detail_lokasi' title='Detail Lokasi' onclick='viewDetailLokasi(" + row.id + ");return false;'>";
                                    txtAction += parseFloat(qty).toLocaleString('id-ID', { maximumFractionDigits: 2});
                                    txtAction += "</a>";
                                return txtAction;
                            }
                            else {
                                return '0';
                            }
                        },
                    },
                    {
                        field: 'qty_outstanding',
                        title: 'Kurang Kirim',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: true,
                        template: function(row) {
                            if (row.qty_outstanding != null) {
                                return parseFloat(row.qty_outstanding).toLocaleString('id-ID', { maximumFractionDigits: 2});
                            }
                            else {
                                return '0';
                            }
                        },
                    },
                ],
            });

            $('#table_brg_search_item').on('change', function() {
                datatable.search($(this).val(), 'kode_item');
            });

            $('#table_brg_search_status').on('change', function() {
                datatable.search($(this).val(), 'nama_satuan');
            });

        });

        $(document).ready(function() {

            var datatable = $('#list_item_detail').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/RekapPenjualanBarang/GetDetailBarang',
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
                    input: $('#table_detail_search_query')
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
                        field: 'nama_customer',
                        title: 'Customer',
                        autoHide: false,
                        width: 200,
                        textAlign: 'left',
                        template: function(row) {
                            if (row.nama_customer != null) {
                                return row.kode_customer.toUpperCase() + ' - ' + row.nama_customer.toUpperCase();
                            }
                        },
                    },
                    {
                        field: 'nama_satuan',
                        title: 'Satuan',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            return ucwords(row.nama_satuan);
                        },
                    },
                    {
                        field: 'qty_item',
                        width: 'auto',
                        title: 'Dipesan',
                        textAlign: 'center',
                        autoHide: true,
                        template: function(row) {
                            if (row.qty_item != null) {
                                return parseFloat(row.qty_item).toLocaleString('id-ID', { maximumFractionDigits: 2});
                            }
                            else {
                                return '0';
                            }
                        },
                    },
                    {
                        field: 'qty_kirim',
                        title: 'Terkirim',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: true,
                        template: function(row) {
                            if (row.qty_item != null) {
                                var qty = parseFloat(row.qty_item) - parseFloat(row.qty_outstanding);
                                return parseFloat(qty).toLocaleString('id-ID', { maximumFractionDigits: 2});
                            }
                            else {
                                return '0';
                            }
                        },
                    },
                    {
                        field: 'qty_outstanding',
                        title: 'Kurang Kirim',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: true,
                        template: function(row) {
                            if (row.qty_outstanding != null) {
                                return parseFloat(row.qty_outstanding).toLocaleString('id-ID', { maximumFractionDigits: 2});
                            }
                            else {
                                return '0';
                            }
                        },
                    },
                ],
            });

            $('#table_detail_search_customer').on('change', function() {
                datatable.search($(this).val(), 'nama_customer');
            });

        });

        $(document).ready(function() {

            var datatable = $('#list_item_lokasi').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/RekapPenjualanBarang/GetDetailLokasi',
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
                        field: 'qty_item',
                        width: 'auto',
                        title: 'Qty',
                        textAlign: 'center',
                        autoHide: true,
                        template: function(row) {
                            if (row.qty_item != null) {
                                return parseFloat(row.qty_item).toLocaleString('id-ID', { maximumFractionDigits: 2});
                            }
                            else {
                                return '0';
                            }
                        },
                    },
                ],
            });
        });

        $(document).ready(function () {
            $("#btnSearch").on('click', function(e) {
                var errCount = 0;

                $(".req").each(function(){
                    if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                        $(this).closest('.form-group, input-group').find('.err').show();
                        errCount = errCount + 1;
                    }
                    else {
                        $(this).closest('.form-group, input-group').find('.err').hide();
                    }
                });

                var jenis = $("#jenisPeriode").val();

                if (jenis == "harian") {
                    $(".reqHarian").each(function(){
                        if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                            $(this).closest('.form-group, input-group').find('.errTanggal').show();
                            errCount = errCount + 1;
                        }
                        else {
                            $(this).closest('.form-group, input-group').find('.errTanggal').hide();
                        }
                    });
                }

                if (jenis == "bulanan") {
                    $(".reqBulanan").each(function(){
                        if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                            $(this).closest('.form-group, input-group').find('.errBulanan').show();
                            errCount = errCount + 1;
                        }
                        else {
                            $(this).closest('.form-group, input-group').find('.errBulanan').hide();
                        }
                    });
                }

                if (jenis == "tahunan") {
                    $(".reqTahunan").each(function(){
                        if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                            $(this).closest('.form-group, input-group').find('.errTahunan').show();
                            errCount = errCount + 1;
                        }
                        else {
                            $(this).closest('.form-group, input-group').find('.errTahunan').hide();
                        }
                    });
                }

                if (errCount == 0) {
                    Swal.fire({
                        title: "Cari Data?",
                        text: "Apakah periode sudah sesuai?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Ya",
                        cancelButtonText: "Tidak",
                        reverseButtons: false
                    }).then(function(result) {
                        if(result.value) {
                            var datatable = $('#table_brg').KTDatatable();
                                datatable.setDataSourceParam('idCust', $("#table_brg_search_cust").val());
                                datatable.setDataSourceParam('jenisPeriode', $("#jenis_periode").val());
                                datatable.setDataSourceParam('tglStart', $("#tanggal_picker_start").val());
                                datatable.setDataSourceParam('tglEnd', $("#tanggal_picker_end").val());
                                datatable.setDataSourceParam('bulan', $("#bulan_picker_val").val());
                                datatable.setDataSourceParam('tahun', $("#tahun_picker_val").val());
                                datatable.reload();
                        }
                        else if (result.dismiss === "cancel") {
                            e.preventDefault();
                        }
                    });
                }
            });
        });

        function viewDetailItem(id, idSatuan) {

            var datatable = $('#list_item_detail').KTDatatable();
                datatable.setDataSourceParam('idProduct', id);
                datatable.setDataSourceParam('idSatuan', idSatuan);
                datatable.reload();

            $("#txtNama").text($("#txt_"+id).text().toUpperCase());

        }

        function viewDetailLokasi(id) {

            var datatable = $('#list_item_lokasi').KTDatatable();
                datatable.setDataSourceParam('idProduct', id);
                datatable.setDataSourceParam('idCust', $("#table_brg_search_cust").val());
                datatable.setDataSourceParam('jenisPeriode', $("#jenis_periode").val());
                datatable.setDataSourceParam('tglStart', $("#tanggal_picker_start").val());
                datatable.setDataSourceParam('tglEnd', $("#tanggal_picker_end").val());
                datatable.setDataSourceParam('bulan', $("#bulan_picker_val").val());
                datatable.setDataSourceParam('tahun', $("#tahun_picker_val").val());
                datatable.reload();

            $("#txtNamaLokasi").text($("#txt_"+id).text().toUpperCase());

        }

        $(document).ready(function () {
            $("#btnExport").on('click', function(e) {
                var errCount = 0;

                $(".req").each(function(){
                    if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                        $(this).closest('.form-group, input-group').find('.err').show();
                        errCount = errCount + 1;
                    }
                    else {
                        $(this).closest('.form-group, input-group').find('.err').hide();
                    }
                });

                var jenis = $("#jenisPeriode").val();

                if (jenis == "harian") {
                    $(".reqHarian").each(function(){
                        if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                            $(this).closest('.form-group, input-group').find('.errTanggal').show();
                            errCount = errCount + 1;
                        }
                        else {
                            $(this).closest('.form-group, input-group').find('.errTanggal').hide();
                        }
                    });
                }

                if (jenis == "bulanan") {
                    $(".reqBulanan").each(function(){
                        if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                            $(this).closest('.form-group, input-group').find('.errBulanan').show();
                            errCount = errCount + 1;
                        }
                        else {
                            $(this).closest('.form-group, input-group').find('.errBulanan').hide();
                        }
                    });
                }

                if (jenis == "tahunan") {
                    $(".reqTahunan").each(function(){
                        if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                            $(this).closest('.form-group, input-group').find('.errTahunan').show();
                            errCount = errCount + 1;
                        }
                        else {
                            $(this).closest('.form-group, input-group').find('.errTahunan').hide();
                        }
                    });
                }

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
