@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h6 class="card-title text-white">Laporan Penjualan Detail</h6>
					</div>
                    <form action="{{ route('ReportSalesDetail.Export') }}" class="form-horizontal" id="form_add" method="POST" enctype="multipart/form-data">
					    <div class="card-body">
                            {{ csrf_field() }}
                            <div class="row">
								<div class="col-md-6">
									<fieldset>

                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Jenis Laporan :</label>
                                            <div class="col-lg-9">
                                                <select class="form-control select2" id="jenis" name="jenis">
                                                    <option value="customer">Pelanggan</option>
                                                    <option value="grup">Grup</option>
                                                </select>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih customer terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row" id="divGrup" style="display:none;">
                                            <label class="col-lg-3 col-form-label">Grup :</label>
                                            <div class="col-lg-9">
                                                <select class="form-control select2" id="grup" name="grup" style="width: 100%;">
                                                    <option value="">Semua Grup</option>
                                                    @foreach($dataGroup as $group)
                                                    <option value="{{$group->id}}">{{strtoupper($group->nama_group)}}</option>
                                                    @endforeach
                                                </select>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih grup terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row" id="divPelanggan">
                                            <label class="col-lg-3 col-form-label">Pelanggan :</label>
                                            <div class="col-lg-9">
                                                <select class="form-control select2 req" id="customer" name="customer">
                                                    <option label="Label">All</option>
                                                    @foreach($dataCustomer as $customer)
                                                    <option value="{{$customer->id}}">{{strtoupper($customer->nama_customer)}}</option>
                                                    @endforeach
                                                </select>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih customer terlebih dahulu!</span>
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
                                                <input type="text" class="form-control" id="bulan_picker" name="bulan_picker" autocomplete="off">
                                                <span class="form-text text-danger errBulanan" style="display:none;">*Harap pilih bulan terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row" id="divTahunan" style="display: none;">
                                            <label class="col-lg-3 col-form-label">Tahun :</label>
                                            <div class="col-lg-9">
                                                <input type="hidden" class="form-control" id="tahun_picker_val" name="tahun_picker_val">
                                                <input type="text" class="form-control" id="tahun_picker" name="tahun_picker"  autocomplete="off">
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

                        </div>
                    </form>
                </div>
                <br>
                <br>
                <div class="card card-custom" style="display: none;" id="cardReport" >
					<div class="card-body">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <h2> <strong><p>Laporan Penjualan Detail</p></strong></h2>

                                <h3> <strong><p id="namaCustomer">Nama Customer</p></strong></h3>
                                <h4 style="display: inline-block;">Periode <p id="periodeTxt"></p></h4>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-right">
                                @if($hakAkses->export == "Y")
                                <button type="button" class="btn btn-success font-weight-bold mr-2" id="btnExport">Export <i class="fas fa-file-excel"></i></button>
                                @endif
                            </div>
                        </div>
                        <br>

                        <div class="row">
                            <div class="col-md-12">
                                <table id="tblData" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Tanggal</th>
                                            <th class="text-center">Nama Pelanggan</th>
                                            <th class="text-center">Nomor Transaksi</th>
                                            <th class="text-center">Nama Barang</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-center">Satuan</th>
                                            <th class="text-center">Harga</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <h5>Total Transaksi Penjualan : <p style="display: inline-block;" id="ttlFaktur"></p></h5>
                            </div>
                        </div>
                        <br>
                    </div>
                </div>
			</div>
			<!-- /content area -->
@endsection
@section('scripts')
    <script type="text/javascript">

        $(document).ready(function () {

            $('#customer').select2({
                allowClear: true,
            });

            $('#grup').select2({
                allowClear: true,
            });

            $('#jenis').select2({
                allowClear: true,
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

            $("#jenis").on('change', function() {
                if ($(this).val() == "customer") {
                    $("#divPelanggan").show();
                    $("#divGrup").hide();
                }
                else if ($(this).val() == "grup") {
                    $("#divPelanggan").hide();
                    $("#divGrup").show();
                }
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
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });
                            $.ajax({
                                url: "/ReportSalesDetail/GetSalesReportDetail",
                                method: 'POST',
                                data: {
                                    jenis: $("#jenis").val(),
                                    grup: $("#grup").val(),
                                    idCustomer: $("#customer").val(),
                                    jenisPeriode: $("#jenisPeriode").val(),
                                    tglStart: $("#tanggal_picker_start").val(),
                                    tglEnd: $("#tanggal_picker_end").val(),
                                    bulan: $("#bulan_picker_val").val(),
                                    tahun: $("#tahun_picker_val").val(),
                                },
                                success: function(result){
                                    if (result.length > 0) {
                                        $('#tblData tbody').empty();
                                        var jmlQty = 0;
                                        if (result.length > 0) {
                                            for (var i = 0; i < result.length;i++) {
                                                var noDo = result[i].kode_pengiriman;
                                                var noSo = result[i].no_so;
                                                var nmBrg = result[i].nama_item;
                                                var kdBrg = "";
                                                if(result[i].value_spesifikasi != null) {
                                                    kdBrg = '('+result[i].value_spesifikasi+')'+result[i].kode_item;
                                                }
                                                else {
                                                    kdBrg = result[i].kode_item;
                                                }
                                                var kodeInv = result[i].kode_invoice;
                                                var tgl = result[i].tanggal_invoice;
                                                var qty = result[i].qty_item;
                                                var satuan = result[i].nama_satuan;
                                                var kategori = result[i].nama_kategori;
                                                var harga = result[i].harga_jual;
                                                var namaCust = result[i].nama_customer;

                                                var data="<tr>";
                                                    data +="<td style='text-align:center;'>"+formatDate(tgl)+"</td>";
                                                    data +="<td style='text-align:center;'>"+namaCust+"</td>";
                                                    data +="<td style='text-align:center;'>"+noDo.toUpperCase()+"<br>"+"<span class='label label-primary label-inline label-round'>" + noSo.toUpperCase() + "</span>"+"<br>"+"<span class='label label-primary label-inline label-round'>" + kodeInv.toUpperCase() + "</span>"+"</td>";
                                                    data +="<td style='text-align:center;'>"+ucwords(nmBrg)+"<br>"+"<span class='label label-primary label-inline label-round'>" + kdBrg.toUpperCase() + "</span>"+"<br>"+"<span class='label label-primary label-inline label-round'>" + ucwords(kategori) + "</span>"+"</td>";
                                                    data +="<td style='text-align:right;'>"+parseFloat(qty).toLocaleString('id-ID', { maximumFractionDigits: 2})+"</td>";
                                                    data +="<td style='text-align:center;'>"+ucwords(satuan)+"</td>";
                                                    data +="<td style='text-align:right;'>"+parseFloat(harga).toLocaleString('id-ID', { maximumFractionDigits: 2})+"</td>";
                                                    data +="</tr>";
                                                    $("#tblData").append(data);
                                            }
                                            var txt = "";
                                            $("#namaCustomer").text($("#customer option:selected").html());
                                            if ($("#jenis").val() == "customer") {
                                                $("#namaCustomer").text($("#customer option:selected").html());
                                            }
                                            else {
                                                $("#namaCustomer").text($("#grup option:selected").html());
                                            }
                                            if ($("#jenisPeriode").val() == "harian") {
                                                txt = formatDate($("#tanggal_picker_start").val()) + " - " + formatDate($("#tanggal_picker_end").val());
                                            }
                                            else if ($("#jenisPeriode").val() == "bulanan") {
                                                txt = $("#bulan_picker").data('datepicker').getFormattedDate('MM yyyy');
                                            }
                                            else {
                                                txt = $("#tahun_picker").data('datepicker').getFormattedDate('yyyy');
                                            }

                                            $("#ttlFaktur").text(parseFloat(result.length).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                                            $("#periodeTxt").text(txt);
                                            $("#cardReport").show();
                                        }
                                    }
                                    else {
                                        Swal.fire(
                                            "Gagal!",
                                            "Data Tidak Ditemukan!",
                                            "warning"
                                        );
                                        $("#cardReport").hide();
                                    }
                                }
                            });
                        }
                        else if (result.dismiss === "cancel") {
                            e.preventDefault();
                        }
                    });
                }
            });
        });

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
