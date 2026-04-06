@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h6 class="card-title text-white">Mutasi Detail Stok Barang</h6>
					</div>
                    <form action="#" class="form-horizontal" id="form_add" method="POST" enctype="multipart/form-data">
					    <div class="card-body">
                            {{ csrf_field() }}
                            <div class="row">
								<div class="col-md-6">
									<fieldset>
                                        <legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi </h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Gudang :</label>
                                            <div class="col-lg-9">
                                                <select class="form-control select2 req" id="idIndex" name="idIndex">
                                                    <option value="all">All</option>
                                                    @foreach($listIndex as $index)
                                                    <option value="{{$index['id']}}">{{strtoupper($index['nama_index'])}}</option>
                                                    @endforeach
                                                </select>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih jenis periode terlebih dahulu!</span>
                                            </div>
                                        </div>
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
                                                <input type="text" class="form-control" id="tanggal_picker" name="tanggal_picker" >
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
                                                <input type="text" class="form-control" id="tahun_picker" name="tahun_picker" autocomplete="off" >
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

								<div class="col-md-6">
									<fieldset>
					                	<legend class="text-muted"><h6><i class="fab la-buffer"></i> Data Barang</h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
                                        <div class="row">
                                            <div class="col-3">
                                                <div class="form-group">
                                                    <label class="col-lg-12 col-form-label">Kode Barang : </label>
                                                    <div class="col-lg-12">
                                                        <input type="text" class="form-control" name="kode_item" id="kode_item" value="{{ $dataProduct->value_spesifikasi != null ? '('.$dataProduct->value_spesifikasi.')' : "" }}{{strtoupper($dataProduct->kode_item)}}" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-9">
                                                <div class="form-group">
                                                    <label class="col-lg-6 col-form-label">Nama Barang</label>
                                                    <div class="col-lg-12">
                                                        <input type="text" class="form-control" name="nama_item" id="nama_item" value="{{ucwords($dataProduct->nama_item)}}" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row">
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label class="col-lg-12 col-form-label">Kategori : </label>
                                                    <div class="col-lg-12">
                                                        <input type="text" class="form-control" name="nama_kategori" id="nama_kategori" value="{{ucwords($dataProduct->nama_kategori)}}" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label class="col-lg-12 col-form-label">Merk : </label>
                                                    <div class="col-lg-12">
                                                        <input type="text" class="form-control" name="nama_merk" id="nama_merk" value="{{ucwords($dataProduct->nama_merk)}}" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label class="col-lg-12 col-form-label">Satuan : </label>
                                                    <div class="col-lg-12">
                                                        <input type="text" class="form-control" name="nama_satuan" id="nama_satuan" value="{{ucwords($dataSatuan->nama_satuan)}}" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        {{-- <div class="row">
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <label class="col-lg-12 col-form-label">Qty per dus : </label>
                                                    <div class="col-lg-12">
                                                        <input type="text" class="form-control" name="qty_per_dus" id="qty_per_dus" value="{{number_format(($dataProduct->qty_per_dus),0,",",".")}}" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                        </div> --}}

									</fieldset>
								</div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset>

                                        <div class="datatable datatable-bordered datatable-head-custom" id="list_item"></div>

                                    </fieldset>
                                </div>
                            </div>

                            <br>


                        </div>

                        <div class="card-footer bg-white d-sm-flex justify-content-sm-between align-items-sm-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-light-danger font-weight-bold mr-2" id="cancel">Keluar <i class="flaticon2-cancel icon-sm"></i></button>
                            </div>
                        </div>
                    </form>
                </div>

			</div>
			<!-- /content area -->
@endsection
@section('scripts')
    <script type="text/javascript">

        $(document).ready(function () {

            $('#idIndex').select2({
                allowClear: true,
                placeholder: "Silahkan Pilih Gudang Disini"
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

        $("#cancel").on('click', function(e) {
            Swal.fire({
                title: "Batal?",
                text: "Apakah anda ingin kembali ke daftar stok?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    window.location.href = "{{ url('/Stock') }}";
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
	    });

        $(document).ready(function() {

            var datatable = $('#list_item').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Stock/GetStockDetailPerItem',
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

                columns: [
                    {
                        field: 'id',
                        title: '#',
                        sortable: false,
                        width: 00,
                        type: 'number',
                        selector: false,
                        textAlign: 'center',
                        visible:false,
                    },
                    {
                        field: 'tgl_transaksi',
                        title: 'Tanggal',
                        autoHide: false,
                        width: '150',
                        textAlign: 'left',
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bold">'+formatDate(row.tgl_transaksi)+'</span>';
                                return txt;
                        },
                    },
                    {
                        field: 'customer_vendor',
                        title: 'Sumber Transaksi',
                        autoHide: false,
                        width: 'auto',
                        textAlign: 'left',
                        template: function(row) {
                            if(row.transaksi == 'out'){
                                if (row.customer_vendor != null) {
                                    var txt = "";
                                    txt += '<span class="font-weight-bold">'+row.customer_vendor.toUpperCase()+'</span>';
                                    txt += '<br /><span class="label label-md label-outline-danger label-inline mt-1 mr-1">' + row.kode_transaksi.toUpperCase() + '</span>';
                                    return txt;
                                }
                                else {
                                    var txt = "";
                                    txt += '<span class="font-weight-bold text-danger">Adjustment</span>';
                                    txt += '<br /><span class="label label-md label-outline-danger label-inline mt-1 mr-1">' + row.kode_transaksi.toUpperCase() + '</span>';
                                    return txt;
                                }
                            }
                            else{
                                if (row.customer_vendor != null) {
                                    var txt = "";
                                    txt += '<span class="font-weight-bold">'+row.customer_vendor.toUpperCase()+'</span>';
                                    txt += '<br /><span class="label label-md label-outline-primary label-inline mt-1 mr-1">' + row.kode_transaksi.toUpperCase() + '</span>';
                                    return txt;
                                }
                                else {
                                    var txt = "";
                                    txt += '<span class="font-weight-bold text-muted">Adjustment</span>';
                                    txt += '<br /><span class="label label-md label-outline-primary label-inline mt-1 mr-1">' + row.kode_transaksi.toUpperCase() + '</span>';
                                    return txt;
                                }
                            }
                        },
                    },
                    {
                        field: 'jenis_transaksi',
                        title: 'Transaksi',
                        width: 'auto',
                        textAlign: 'left',
                        autoHide: false,
                        template: function(row) {
                            if(row.transaksi == 'out'){
                                var txt = "";
                                txt += '<span class="label label-md label-dot label-danger mr-2"></span><span class="font-weight-bold text-danger">'+row.jenis_transaksi.replace('_', ' ').toUpperCase();+'</span>';
                                txt += '<br /><span class="label label-md label-outline-danger label-inline mt-1 mr-1">Keluar</span>';
                                return txt;
                            }
                            else{
                                var txt = "";
                                txt += '<span class="label label-md label-dot label-primary mr-2"></span><span class="font-weight-bold text-primary">'+row.jenis_transaksi.replace('_', ' ').toUpperCase();+'</span>';
                                txt += '<br /><span class="label label-md label-outline-primary label-inline mt-1 mr-1">Masuk</span>';
                                return txt;
                            }
                        },
                    },
                    {
                        field: 'qty_item',
                        title: 'Jumlah',
                        type: 'number',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.qty_item).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
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
                        text: "Apakah data sudah sesuai?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Ya",
                        cancelButtonText: "Tidak",
                        reverseButtons: false
                    }).then(function(result) {
                        if(result.value) {
                            var datatable = $('#list_item').KTDatatable();
                                datatable.setDataSourceParam('idProduct', '{{$dataProduct->id}}');
                                datatable.setDataSourceParam('idSatuan', '{{$dataSatuan->id}}');
                                datatable.setDataSourceParam('idIndex', $("#idIndex option:selected").val());
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



    </script>
@endsection
