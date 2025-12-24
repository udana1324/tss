@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h6 class="card-title text-white">Laporan Penjualan</h6>
					</div>
                    <form action="{{ route('ReportSales.Export') }}" class="form-horizontal" id="form_add" method="POST" enctype="multipart/form-data">
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
                                                <select class="form-control select2" id="customer" name="customer">
                                                    <option value="">Semua Pelanggan</option>
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
                                <h2> <strong><p>Laporan Penjualan</p></strong></h2>
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
                                            <th class="text-center">Nomor Transaksi</th>
                                            <th class="text-center">Tanggal</th>
                                            <th class="text-center">Nama Perusahaan</th>
                                            <th class="text-center">Jumlah (Rp)</th>
                                            <th class="text-center">Pembayaran (Rp)</th>
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
                                <h4><strong>TOTAL PENJUALAN : <p style="display: inline-block;" id="ttlSale"></p></strong></h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 text-right">
                                <h5>Total Pembayaran : <p style="display: inline-block;" id="ttlBayar"></p></h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 text-right">
                                <h5>Total Piutang : <p style="display: inline-block;" id="ttlPiutang"></p></h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 text-right">
                                <h5>Total Faktur Penjualan : <p style="display: inline-block;" id="ttlFaktur"></p></h5>
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
                                url: "/ReportSales/GetSalesReport",
                                method: 'POST',
                                data: {
                                    jenis: $("#jenis").val(),
                                    grup: $("#grup").val(),
                                    customer: $("#customer").val(),
                                    jenisPeriode: $("#jenisPeriode").val(),
                                    tglStart: $("#tanggal_picker_start").val(),
                                    tglEnd: $("#tanggal_picker_end").val(),
                                    bulan: $("#bulan_picker_val").val(),
                                    tahun: $("#tahun_picker_val").val(),
                                },
                                success: function(result){
                                    if (result.length > 0) {
                                        $('#tblData tbody').empty();
                                        var jmlTtl = 0;
                                        var jmlByr = 0;
                                        if (result.length > 0) {
                                            for (var i = 0; i < result.length;i++) {
                                                var tglInv = result[i].tanggal_invoice;
                                                var noSo = result[i].no_so;
                                                var kdInv = result[i].kode_invoice;
                                                var jml = result[i].grand_total;
                                                var jmlBayar = result[i].sumPembayaran;
                                                var tglBayar = result[i].tanggal;
                                                var nmOutlet = result[i].nama_outlet;
                                                var namaCust = result[i].nama_customer;
                                                jmlTtl = parseFloat(jmlTtl) + parseFloat(jml);
                                                jmlByr = parseFloat(jmlByr) + parseFloat(jmlBayar);
                                                var data="<tr>";
                                                    data +="<td style='text-align:center;'>"+kdInv.toUpperCase()+"<br><span class='label label-md label-outline-primary label-inline'>" + noSo.toUpperCase() + "</span>"+"</td>";
                                                    data +="<td style='text-align:center;'>"+formatDate(tglInv)+"</td>";
                                                    if(nmOutlet != null){
                                                        data +="<td>"+namaCust+"<br>"+"<span class='label label-md label-outline-primary label-inline'>" + ucwords(nmOutlet) + "</span>"+"</td>";
                                                    }
                                                    else{
                                                        data +="<td>"+namaCust+"</td>";
                                                    }
                                                    data +="<td style='text-align:right;'>"+parseFloat(jml).toLocaleString('id-ID', { maximumFractionDigits: 2})+"</td>";
                                                    if(tglBayar != null){
                                                        data +="<td style='text-align:right;'>"+parseFloat(jmlBayar).toLocaleString('id-ID', { maximumFractionDigits: 2})+"<br><span class='label label-md label-outline-primary label-inline'>"+formatDate(tglBayar)+"</td>";
                                                    }
                                                    else{
                                                        data +="<td style='text-align:right;'>"+parseFloat(jmlBayar).toLocaleString('id-ID', { maximumFractionDigits: 2})+"</td>";
                                                    }
                                                    data +="</tr>";
                                                    $("#tblData").append(data);
                                            }
                                            var txt = "";
                                            if ($("#jenisPeriode").val() == "harian") {
                                                txt = formatDate($("#tanggal_picker_start").val()) + " - " + formatDate($("#tanggal_picker_end").val());
                                            }
                                            else if ($("#jenisPeriode").val() == "bulanan") {
                                                txt = $("#bulan_picker").data('datepicker').getFormattedDate('MM yyyy');
                                            }
                                            else {
                                                txt = $("#tahun_picker").data('datepicker').getFormattedDate('yyyy');
                                            }
                                            var jmlPiutang = parseFloat(jmlTtl) - parseFloat(jmlByr);
                                            $("#ttlSale").text(parseFloat(jmlTtl).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                                            $("#ttlBayar").text(parseFloat(jmlByr).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                                            $("#ttlPiutang").text(parseFloat(jmlPiutang).toLocaleString('id-ID', { maximumFractionDigits: 2}));
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
