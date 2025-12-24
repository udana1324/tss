@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary text-white header-elements-sm-inline">
						<h5 class="card-title font-weight-semibold">Tambah Nomor Seri Faktur Pajak</h5>
					</div>
                    <form action="{{ route('TaxSerialNumber.update', $dataSerialNumber->id) }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <fieldset>
                                        <legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi Permohonan PKP / Pemberitahun DJP </h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Nomor Permohonan PKP :</label>
                                            <div class="col-sm-12">
                                                <input type="text" placeholder="Masukkan No. Permohonan" id="no_permohonan" name="no_permohonan" class="form-control req" value="{{$dataSerialNumber->no_permohonan}}">
                                                <span class="form-text text-danger err" style="display:none;">*Harap Masukkan nomor permohonan terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Tanggal Permohonan :</label>
                                            <div class="col-sm-12 divTgl">
                                                <input type="hidden" class="form-control tglValue req" name="tanggal_permohonan" id="tanggal_permohonan" >
                                                <input type="text" class="form-control pickerTgl" placeholder="Pilih Tanggal" name="tanggal_permohonan_picker" id="tanggal_permohonan_picker" readonly>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal permohonan terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Nomor Pemberitahuan DJP :</label>
                                            <div class="col-sm-12">
                                                <input type="text" placeholder="Masukkan Nomor Pemberitahuan" id="no_pemberitahuan" name="no_pemberitahuan" class="form-control req" value="{{$dataSerialNumber->no_pemberitahuan_djp}}">
                                                <span class="form-text text-danger err" style="display:none;">*Harap Masukkan nomor pemberitahuan terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Tanggal Pemberitahuan :</label>
                                            <div class="col-sm-12 divTgl">
                                                <input type="hidden" class="form-control tglValue req" name="tanggal_pemberitahuan" id="tanggal_pemberitahuan" >
                                                <input type="text" class="form-control pickerTgl" placeholder="Pilih Tanggal" name="tanggal_pemberitahuan_picker" id="tanggal_pemberitahuan_picker" readonly>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal pemberitahuan terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Tahun Pajak :</label>
                                            <div class="col-sm-12">
                                                <input type="text" maxlength="4" onkeypress="return validasiAngka(event);" placeholder="Masukkan Tahun Pajak" id="tahun_pajak" name="tahun_pajak" class="form-control req" value="{{$dataSerialNumber->tahun_berlaku_seri}}">
                                                <span class="form-text text-danger err" style="display:none;">*Harap Masukkan Deskripsi Bank terlebih dahulu!</span>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>

                                <div class="col-md-6">
                                    <fieldset>
                                        <legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi Nomor Seri Faktur Pajak </h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Jumlah Nomor Seri Faktur Pajak :</label>
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control req" maxlength="4" onkeypress="return validasiAngka(event);" name="jumlah_seri" id="jumlah_seri" value="{{$dataSerialNumber->jumlah_no_seri}}" readonly>
                                                <span class="form-text text-danger err" style="display:none;">*Harap Masukkan nomor permohonan terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Nomor Seri Faktur Pajak Dari :</label>
                                            <div class="col-sm-12">
                                                <input type="text" placeholder="Masukkan Nomor Awal Seri Faktur Pajak" id="seri_awal" name="seri_awal" class="form-control sfp req" value="{{$dataSerialNumber->nomor_seri_dari}}" autocomplete="off">
                                                <span class="form-text text-danger err" style="display:none;">*Harap Masukkan nomor seri faktur pajak awal terlebih dahulu!</span>
                                                <span class="form-text text-danger errFormat" style="display:none;">*format nomor seri faktur pajak salah, harap masukkan format dengan benar!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Nomor Seri Faktur Pajak Sampai :</label>
                                            <div class="col-sm-12">
                                                <input type="text" placeholder="Masukkan Nomor Akhir Seri Faktur Pajak" id="seri_akhir" name="seri_akhir" class="form-control sfp req" value="{{$dataSerialNumber->nomor_seri_sampai}}" autocomplete="off">
                                                <span class="form-text text-danger err" style="display:none;">*Harap Masukkan nomor seri faktur pajak akhir terlebih dahulu!</span>
                                                <span class="form-text text-danger errFormat" style="display:none;">*format nomor seri faktur pajak salah, harap masukkan format dengan benar!</span>
                                            </div>
                                        </div>

                                    </fieldset>
                                </div>

                            </div>
                        </div>

                        <div class="card-footer bg-white d-sm-flex justify-content-sm-between align-items-sm-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-light-danger font-weight-bold mr-2" onclick="window.location.href = '{{ url('/TaxSerialNumber') }}';">Keluar <i class="flaticon2-cancel icon-sm"></i></button>
                            </div>

                            <div class="mt-2 mt-sm-0">
                                <button type="submit" class="btn btn-light-primary font-weight-bold mr-2"> Simpan <i class="flaticon-paper-plane-1"></i></button>
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
            $('#tanggal_permohonan_picker, #tanggal_pemberitahuan_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                autoclose : true,
                "setDate": new Date(),
                format : "dd MM yyyy",
            });
        });

        $(".pickerTgl").on('change', function() {
            var pemberitahuanDate = new Date($("#tanggal_pemberitahuan_picker").datepicker('getDate'));
            var permohonanDate = new Date($("#tanggal_permohonan_picker").datepicker('getDate'));
            var selisih = Math.floor((Date.UTC(pemberitahuanDate.getFullYear(), pemberitahuanDate.getMonth(), pemberitahuanDate.getDate()) - Date.UTC(permohonanDate.getFullYear(), permohonanDate.getMonth(), permohonanDate.getDate()) ) /(1000 * 60 * 60 * 24));

            $(this).closest(".divTgl").find(".tglValue").val($(this).data('datepicker').getFormattedDate('yyyy-mm-dd'));

            if ($("#tanggal_pemberitahuan").val() != "") {
                if (selisih < 0) {
                    Swal.fire(
                        "Error!",
                        "Tanggal Pemberitahuan tidak boleh dibawah dari tanggal permohonan!.",
                        "warning"
                    )
                    $("#tanggal_pemberitahuan").val("");
                    $("#tanggal_pemberitahuan_picker").val("");
                }
            }
        });

        function validasiangka(evt) {
          var charCode = (evt.which) ? evt.which : event.keyCode
           	if (charCode > 31 && (charCode < 48 || charCode > 57)) {

	            return false;
	          return true;
	      	}
	      	else if (evt.which == 46 || evt.keyCode == 46) {
				e.preventDefault();
			}
			else if (evt.which == 45 || evt.keyCode == 45) {
				e.preventDefault();
			}
			else if (evt.which == 44 || evt.keyCode == 44) {
				e.preventDefault();
			}
			else if (evt.which == 43 || evt.keyCode == 43) {
				e.preventDefault();
			}
        }

        $("#form_add").submit(function(e){
            e.preventDefault();

            Swal.fire({
                title: "Simpan Data?",
                text: "Apakah data sudah sesuai?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    var count = 0;
                    $(".req").each(function(){
                        if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                            $(this).closest('.form-group').find('.err').show();
                            count = parseInt(count) + 1;
                        }
                        else {
                            $(this).closest('.form-group').find('.err').hide();
                        }
                    });

                    $(".sfp").each(function(){
                        var seri = $(this).val();

                        if(seri != "") {
                            var regexFormatFP = new RegExp('^\\d{3}\\.\\d{2}\\.\\d{8}$');

                            if(!regexFormatFP.test(seri)) {
                                $(this).closest('.form-group').find('.errFormat').show();
                                count = parseInt(count) + 1;
                            }
                            else {
                                $(this).closest('.form-group').find('.errFormat').hide();
                            }
                        }
                    });

                    if (count == 0) {
                        $("#form_add").off("submit").submit();
                    }
                    else {
                        e.preventDefault();
                    }

                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
		});

        $(".sfp").on("change", function() {
            var seri = $(this).val();

            if(seri != "") {
                var regexFormatFP = new RegExp('^\\d{3}\\.\\d{2}\\.\\d{8}$');
                if(!regexFormatFP.test(seri)) {
                    $(this).closest('.form-group').find('.errFormat').show();
                }
                else {
                    $(this).closest('.form-group').find('.errFormat').hide();
                    $(this).closest('.form-group').find('.errFormat').hide();
                    var seriAwal = $("#seri_awal").val().split('.')[2];
                    var seriAkhir = $("#seri_akhir").val().split('.')[2];
                    var jml = 0;

                    if ($("#seri_awal").val() != "" && $("#seri_akhir").val() != "") {
                        jml = (parseInt(seriAkhir) - parseInt(seriAwal)) + 1;
                        $("#jumlah_seri").val(jml);
                    }
                }
            }
            else {
                $(this).closest('.form-group').find('.errFormat').hide();
            }

        });

        $(document).ready(function () {
            $("#tanggal_permohonan_picker").datepicker("setDate", new Date("{{$dataSerialNumber->tanggal_permohonan}}"));
            $("#tanggal_pemberitahuan_picker").datepicker("setDate", new Date("{{$dataSerialNumber->tanggal_pemberitahuan_djp}}"));
        });

    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
