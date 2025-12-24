@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary text-white header-elements-sm-inline">
						<h5 class="card-title font-weight-semibold">Detail Nomor Seri Faktur Pajak</h5>
					</div>
                    <form action="{{ route('TaxSerialNumber.Posting', $dataSerialNumber->id) }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-6">
                                    <fieldset>
                                        <legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi Permohonan PKP / Pemberitahun DJP </h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Nomor Permohonan PKP :</label>
                                            <div class="col-sm-12">
                                                <label class="col-form-label">{{$dataSerialNumber->no_permohonan}} </label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Tanggal Permohonan :</label>
                                            <div class="col-sm-12 divTgl">
                                                <label class="col-form-label">{{\Carbon\Carbon::parse($dataSerialNumber->tanggal_permohonan)->format('d F Y')}} </label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Nomor Pemberitahuan DJP :</label>
                                            <div class="col-sm-12">
                                                <label class="col-form-label">{{$dataSerialNumber->no_pemberitahuan_djp}} </label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Tanggal Pemberitahuan :</label>
                                            <div class="col-sm-12 divTgl">
                                                <label class="col-form-label">{{\Carbon\Carbon::parse($dataSerialNumber->tanggal_pemberitahuan_djp)->format('d F Y')}} </label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Tahun Pajak :</label>
                                            <div class="col-sm-12">
                                                <label class="col-form-label">{{$dataSerialNumber->tahun_berlaku_seri}} </label>
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
                                                <label class="col-form-label">{{$dataSerialNumber->jumlah_no_seri}} </label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Nomor Seri Faktur Pajak Dari :</label>
                                            <div class="col-sm-12">
                                                <label class="col-form-label">{{$dataSerialNumber->nomor_seri_dari}} </label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Nomor Seri Faktur Pajak Sampai :</label>
                                            <div class="col-sm-12">
                                                <label class="col-form-label">{{$dataSerialNumber->nomor_seri_sampai}} </label>
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
                                <input type="hidden" id="submit_action" name="submit_action" class="form-control" readonly>
                                @if($dataSerialNumber->status == "draft")
                                    <button type="button" class="btn btn-secondary mt-2 mt-sm-0 btnSubmit" id="btn_edit" value="ubah">Ubah<i class="flaticon-edit ml-2"></i></button>
                                    @if($hakAkses->approve == "Y")
                                        <button type="button" class="btn btn-light-primary font-weight-bold mr-2 btnSubmit" id="btn_posting" value="posting"> Posting <i class="flaticon-paper-plane-1"></i></button>
                                    @endif

                                @elseif($dataSerialNumber->status == "posted")
                                    @if($exportedFP > 0)
                                    <button type="button" class="btn btn-success mt-2 mt-sm-0 btnSubmit" id="btn_close" value="tutup">Selesaikan Nomor Seri<i class="fas fa-file-upload ml-2"></i></button>
                                    @else
                                    <button type="button" class="btn btn-danger mt-2 mt-sm-0 btnSubmit" id="btn_cancel" value="batal">Batal<i class="flaticon2-cancel ml-2"></i></button>
                                    @endif
                                @endif
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

        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

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

        $(".btnSubmit").on("click", function(e){
            var btn = $(this).val();
            $("#submit_action").val(btn);
            Swal.fire({
                title: ucwords(btn) + " Nomor Seri Faktur Pajak?",
                text: "Apakah yakin ingin melakukan " + ucwords(btn) +" Nomor Seri Faktur Pajak?",
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
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                    e.preventDefault();
                }
            });
		});

        $(".sfp").on("change", function() {
            var seri = $(this).val();

            var regexFormatFP = new RegExp('^\d{3}[.]\d{2}[.]\d{8}$');

            if(!regexFormatFP.test(seri)) {
                $(this).closest('.form-group').find('.errFormat').show();
            }
            else {
                $(this).closest('.form-group').find('.errFormat').hide();
            }

        });

    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
