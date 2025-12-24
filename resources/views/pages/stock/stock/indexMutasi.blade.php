@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h6 class="card-title text-white">Mutasi Stok Barang</h6>
					</div>
                    <form action="#" class="form-horizontal" id="form_add" method="POST" enctype="multipart/form-data">
					    <div class="card-body">
                            {{ csrf_field() }}
                            <div class="row">
								<div class="col-md-6">
									<fieldset>
										<div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Jenis Periode :</label>
                                            <div class="col-lg-9">
                                                <select class="form-control select2 req" id="jenisPeriode" name="jenisPeriode">
                                                    <option label="Label"></option>
                                                    <option value="harian">Harian</option>
                                                    <option value="bulanan">Bulanan</option>
                                                    <option value="tahunan">Tahunan</option>
                                                </select>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih jenis periode terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row" id="divHarian" style="display: none;">
                                            <label class="col-lg-3 col-form-label">Tanggal :</label>
                                            <div class="col-lg-9">
                                                <input type="hidden" class="form-control" id="tanggal_picker_start" name="tanggal_picker_start" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}">
                                                <input type="hidden" class="form-control" id="tanggal_picker_end" name="tanggal_picker_end" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}">
                                                <input type="text" class="form-control" id="tanggal_picker" name="tanggal_picker" autocomplete="off">
                                                <span class="form-text text-danger errTanggal" style="display:none;">*Harap pilih tanggal terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row" id="divBulanan" style="display: none;">
                                            <label class="col-lg-3 col-form-label">Bulan :</label>
                                            <div class="col-lg-9">
                                                <input type="hidden" class="form-control" id="bulan_picker_val" name="bulan_picker_val" >
                                                <input type="text" class="form-control" id="bulan_picker" name="bulan_picker" autocomplete="off" >
                                                <span class="form-text text-danger errBulanan" style="display:none;">*Harap pilih bulan terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row" id="divTahunan" style="display: none;">
                                            <label class="col-lg-3 col-form-label">Tahun :</label>
                                            <div class="col-lg-9">
                                                <input type="hidden" class="form-control" id="tahun_picker_val" name="tahun_picker_val" >
                                                <input type="text" class="form-control" id="tahun_picker" name="tahun_picker" autocomplete="off">
                                                <span class="form-text text-danger errTahunan" style="display:none;">*Harap pilih tahun terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row">
											<div class="col-lg-12 text-center">
												<button type="button" class="btn btn-primary font-weight-bold" id="btnSearch"><i class="flaticon-search"></i>Cari</button>
											</div>
										</div>

									</fieldset>
								</div>

                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset>
                                         <!--begin::Search Form-->
                                        <div class="mb-7">
                                            <div class="row align-items-center">
                                                <div class="col-lg-12 col-xl-8">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-3 my-2 my-md-0">
                                                            <div class="input-icon">
                                                                <input type="text" class="form-control" placeholder="Search..." id="table_adjustment_search_query"/>
                                                                <span>
                                                                    <i class="flaticon2-search-1 text-muted"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        {{-- <div class="col-md-4 my-2 my-md-0">
                                                            <div class="d-flex align-items-center">
                                                                <label class="mr-3 mb-0 d-none d-md-block">Kode Supplier: </label>
                                                                <select class="form-control select2" id="table_stock_search_supplier">
                                                                    <option value="">All</option>
                                                                    @foreach($dataSpek as $rowSpek)
                                                                    <option value="{{$rowSpek->value_spesifikasi}}">{{ucwords($rowSpek->value_spesifikasi)}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div> --}}
                                                        <div class="col-md-5 my-2 my-md-0">
                                                            <div class="d-flex align-items-center">
                                                                <label class="mr-3 mb-0 d-none d-md-block">Barang</label>
                                                                <select class="form-control select2" id="table_stock_search_product">
                                                                    <option value="">Semua</option>
                                                                    @foreach($dataProduct as $product)
                                                                    <option value="{{$product->kode_item}}">{{ $product->value_spesifikasi != null ? '('.$product->value_spesifikasi.')' : "" }}{{strtoupper($product->kode_item." - ".$product->nama_item)}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        {{-- <div class="col-md-3 my-2 my-md-0">
                                                            <div class="d-flex align-items-center">
                                                                <label class="mr-3 mb-0 d-none d-md-block">Kategori </label>
                                                                <select class="form-control select2" id="table_stock_search_category">
                                                                    <option value="">All</option>
                                                                    @foreach($dataCategory as $rowCategory)
                                                                    <option value="{{$rowCategory->nama_kategori}}">{{ucwords($rowCategory->nama_kategori)}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 my-2 my-md-0">
                                                            <div class="d-flex align-items-center">
                                                                <label class="mr-3 mb-0 d-none d-md-block">Merk </label>
                                                                <select class="form-control select2" id="table_stock_search_brand">
                                                                    <option value="">All</option>
                                                                    @foreach($dataBrand as $rowBrand)
                                                                    <option value="{{$rowBrand->nama_merk}}">{{ucwords($rowBrand->nama_merk)}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div> --}}
                                                        {{-- <div class="col-md-3 my-2 my-md-0">
                                                            <div class="d-flex align-items-center">
                                                                <label class="mr-3 mb-0 d-none d-md-block">Status </label>
                                                                <select class="form-control select2" id="table_adjustment_search_jenis">
                                                                    <option value="">All</option>
                                                                    <option value="retur_purc">Retur Pembelian</option>
                                                                    <option value="retur_sale">Retur Penjualan</option>
                                                                    <option value="penambahan">Penambahan</option>
                                                                    <option value="pengurangan">Pengurangan</option>
                                                                </select>
                                                            </div>
                                                        </div> --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end: Search Form-->
                                        <div class="datatable datatable-bordered datatable-head-custom" id="list_item"></div>

                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

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
                                                <div class="col-md-4">
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

			</div>
			<!-- /content area -->
@endsection
@section('scripts')
    <script type="text/javascript">

        $(document).ready(function () {

            $('#table_stock_search_category').select2({
                allowClear: true
            });

            $('#table_stock_search_brand').select2({
                allowClear: true
            });

            // $('#table_stock_search_supplier').select2({
            //     allowClear: true
            // });

            $('#table_stock_search_product').select2({
                allowClear: true
            });

            $('#jenisPeriode').select2({
                allowClear: true,
                placeholder: "Silahkan Pilih Jenis Periode Disini"
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

            $("#jenisPeriode").on("change", function () {
                var jenis = $(this).val();
                if (jenis == "harian") {
                    $("#tanggal_picker").addClass("reqHarian");
                    $("#bulan_picker_val").removeClass("reqBulanan");
                    $("#tahun_picker_val").removeClass("reqTahunan");

                    $("#divHarian").show();
                    $("#divBulanan").hide();
                    $("#divTahunan").hide();
                }
                else if (jenis == "bulanan") {
                    $("#tanggal_picker").removeClass("reqHarian");
                    $("#bulan_picker_val").addClass("reqBulanan");
                    $("#tahun_picker_val").removeClass("reqTahunan");

                    $("#divHarian").hide();
                    $("#divBulanan").show();
                    $("#divTahunan").hide();
                }
                else if (jenis == "tahunan") {
                    $("#tanggal_picker").removeClass("reqHarian");
                    $("#bulan_picker_val").removeClass("reqBulanan");
                    $("#tahun_picker_val").addClass("reqTahunan");

                    $("#divHarian").hide();
                    $("#divBulanan").hide();
                    $("#divTahunan").show();
                }
            });
        });

        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        function formatDate(strDate) {
            var arrMonth = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            var date = new Date(strDate);
            var day = date.getDate();
            var month = date.getMonth();
            var year = date.getFullYear();

            return day + ' ' + arrMonth[month] + ' ' + year;
        }

        $(document).ready(function() {

            var datatable = $('#list_item').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Stock/GetStockTransaction',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                        }
                    },
                    pageSize: 100,
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

                pagination: false,

                search: {
                    input: $('#table_adjustment_search_query')
                },

                autoHide: false,

                columns: [
                    {
                        field: 'id',
                        title: '#',
                        sortable: false,
                        width: 0,
                        type: 'number',
                        selector: false,
                        textAlign: 'center',
                        visible:false,
                    },
                    {
                        field: 'nama_merk',
                        title: 'Nama Merk',
                        width: 'auto',
                        textAlign: 'left',
                        visible:false,
                    },
                    {
                        field: 'nama_kategori',
                        title: 'Nama Kategori',
                        width: 'auto',
                        textAlign: 'left',
                        visible:false,
                    },
                    {
                        field: 'kode_item',
                        title: 'Kode',
                        width: 'auto',
                        textAlign: 'left',
                        visible:false,
                    },
                    {
                        field: 'value_spesifikasi',
                        title: 'Kode',
                        width: 'auto',
                        textAlign: 'left',
                        visible:false,
                    },
                    {
                        field: 'nama_item',
                        title: 'Nama Item',
                        autoHide: false,
                        width: 300,
                        textAlign: 'left',
                        template: function(row) {
                            var txt = "";
                            var txtLink = "";
                                txt += '<span class="font-weight-bold">'+row.nama_item+'</span>';
                                if(row.value_spesifikasi != null) {
                                    txt += '<br /><span class="label label-md label-outline-primary label-inline mt-1 mr-1">' +'('+row.value_spesifikasi+')'+row.kode_item.toUpperCase()+ '</span>';
                                }
                                else {
                                    txt += '<br /><span class="label label-md label-outline-primary label-inline mt-1 mr-1">' +row.kode_item.toUpperCase()+ '</span>';
                                }
                                txt += '<span class="label label-md label-outline-primary label-inline mt-1 mr-1">' + row.nama_merk.toUpperCase() + '</span>';
                                txt += '<span class="label label-md label-outline-primary label-inline mt-1">' + row.nama_kategori + '</span>';
                                txtLink = "<a href='#' class='text-secondary text-hover-primary' data-toggle='modal' data-target='#modal_detail_lokasi' title='Detail Lokasi' onclick='viewDetail(" + row.id + ", " + row.id_satuan + ");return false;'>";
                                txtLink += txt;
                                txtLink += "</a>";

                                return txtLink;
                        },
                    },
                    {
                        field: 'jenis_sumber',
                        width: 100,
                        title: 'Sumber',
                        textAlign: 'center',
                        template: function(row) {
                            var statusTxt = "";

                            if (row.jenis_sumber == "1") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-primary">Penerimaan</span>';
                            }
                            else if (row.jenis_sumber == "2") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-success">Produksi</span>';
                            }
                            else if (row.jenis_sumber == "3") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-warning">Transfer Stok</span>';
                            }
                            else if (row.jenis_sumber == "4") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-warning">Konversi Stok</span>';
                            }
                            else if (row.jenis_sumber == "5") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-warning">Adjustment</span>';
                            }
                            else if (row.jenis_sumber == "6") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-danger">Retur</span>';
                            }

                            return statusTxt;
                        },
                        autoHide: false,
                    },
                    {
                        field: 'stok_awal',
                        title: 'Stok Awal',
                        textAlign: 'right',
                        width: 120,
                        type: 'number',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.stok_awal).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'stok_in',
                        title: 'Jumlah Masuk',
                        textAlign: 'right',
                        width: 120,
                        type: 'number',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.stok_in).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'stok_out',
                        title: 'Jumlah Keluar',
                        textAlign: 'right',
                        width: 120,
                        type: 'number',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.stok_out).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'stok_akhir',
                        title: 'Stok Saat Ini',
                        textAlign: 'right',
                        width: 120,
                        type: 'number',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.stok_akhir).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'nama_satuan',
                        title: 'Satuan',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            return "<span class='text-left'>"+ucwords(row.nama_satuan)+"</span>";
                        },
                    },
                ],
            });

            // $('#table_stock_search_brand').on('change', function() {
            //     datatable.search($(this).val(), 'nama_merk');
            // });

            // $('#table_stock_search_category').on('change', function() {
            //     datatable.search($(this).val(), 'nama_kategori');
            // });

            // $('#table_stock_search_supplier').on('change', function() {
            //     datatable.search($(this).val(), 'value_spesifikasi');
            // });

            $('#table_stock_search_product').on('change', function() {
                datatable.search($(this).val(), 'kode_item');
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
                        text: "Apakah data sudah sesuai?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Ya",
                        cancelButtonText: "Tidak",
                        reverseButtons: false
                    }).then(function(result) {
                        if(result.value) {
                            var datatable = $('#list_item').KTDatatable();
                                datatable.setDataSourceParam('idProduct', '');
                                datatable.setDataSourceParam('jenisPeriode', $("#jenisPeriode").val());
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

        $(document).ready(function() {

            var datatable = $('#list_item_lokasi').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Stock/GetDataPerSumber',
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
                        field: 'jenis_sumber',
                        width: 'auto',
                        title: 'Sumber',
                        textAlign: 'left',
                        template: function(row) {
                            var statusTxt = "";

                            if (row.jenis_sumber == "1") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-primary">Penerimaan</span>';
                            }
                            else if (row.jenis_sumber == "2") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-success">Produksi</span>';
                            }
                            else if (row.jenis_sumber == "3") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-warning">Transfer Stok</span>';
                            }
                            else if (row.jenis_sumber == "4") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-warning">Konversi Stok</span>';
                            }
                            else if (row.jenis_sumber == "5") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-warning">Adjustment</span>';
                            }
                            else if (row.jenis_sumber == "6") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-danger">Retur</span>';
                            }

                            return statusTxt;
                        },
                        autoHide: false,
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
                        field: 'stok_item',
                        width: 'auto',
                        title: 'Qty',
                        textAlign: 'center',
                        autoHide: true,
                        template: function(row) {
                            if (row.stok_item != null) {
                                return parseFloat(row.stok_item).toLocaleString('id-ID', { maximumFractionDigits: 2});
                            }
                            else {
                                return '0';
                            }
                        },
                    },
                ],
            });
        });

        function viewDetail(id, idSatuan) {

            var datatable = $('#list_item_lokasi').KTDatatable();
                datatable.setDataSourceParam('idProduct', id);
                datatable.setDataSourceParam('idSatuan', idSatuan);
                datatable.reload();

            $("#txtNamaLokasi").text($("#txt_"+id).text().toUpperCase());

        }

    </script>
@endsection
