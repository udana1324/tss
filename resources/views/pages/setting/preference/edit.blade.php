@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h5 class="card-title text-white">Ubah Preferensi</h5>
					</div>
                    <form action="{{ route('Preference.store') }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-6">
                                    <fieldset>
                                        <legend class="font-weight-semibold">Data Preferensi</legend>
                                        <div class="separator separator-solid separator-border-2 separator-dark"></div>
                                        <br>
                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Nama PT.</label>
                                            <div class="col-lg-9">
                                                <input type="hidden" value="update" name="metode">
                                            <input type="hidden" id="idData" name="idData" value="{{$Preference->id}}">
                                                <input type="text" class="form-control req" placeholder="Masukkan Nama PT" name="nama_pt" id="nama_pt" value="{{$Preference->nama_pt}}">
                                            <span class="form-text text-danger err" style="display:none;">*Harap masukkan Nama PT terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">No. Telp</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control req" placeholder="Masukkan No. Telp" name="telp" id="telp" value="{{$Preference->telp_pt}}">
                                            <span class="form-text text-danger err" style="display:none;">*Harap masukkan No. Telp PT terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Email</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control req" placeholder="Masukkan Email" name="email" id="email" value="{{$Preference->email_pt}}">
                                            <span class="form-text text-danger err" style="display:none;">*Harap masukkan Email PT terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Website</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control req" placeholder="Masukkan Website" name="web" id="web" value="{{$Preference->website_pt}}">
                                            <span class="form-text text-danger err" style="display:none;">*Harap masukkan Website PT terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">NPWP</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="npwp form-control req" maxlength="2" style="width:60px;display:inline-block;text-align:center;" onkeypress="return validasiangka(event);" name="npwp1" id="npwp1">
                                                            .
                                                <input type="text" class="npwp form-control req" maxlength="3" style="width:60px;display:inline-block;text-align:center;" onkeypress="return validasiangka(event);" name="npwp2" id="npwp2">
                                                            .
                                                <input type="text" class="npwp form-control req" maxlength="3" style="width:60px;display:inline-block;text-align:center;" onkeypress="return validasiangka(event);" name="npwp3" id="npwp3">
                                                            .
                                                <input type="text" class="npwp form-control req" maxlength="1" style="width:60px;display:inline-block;text-align:center;" onkeypress="return validasiangka(event);" name="npwp4" id="npwp4">
                                                            -
                                                <input type="text" class="npwp form-control req" maxlength="3" style="width:60px;display:inline-block;text-align:center;" onkeypress="return validasiangka(event);" name="npwp5" id="npwp5">
                                                            .
                                                <input type="text" class="npwp form-control req" maxlength="3" style="width:60px;display:inline-block;text-align:center;" onkeypress="return validasiangka(event);" name="npwp6" id="npwp6">
                                                <span class="form-text text-muted"> Contoh : 99.999.999.9-999.999</span>
                                                <span class="form-text text-danger err" style="display:none;">*Harap masukkan NPWP PT terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Rekening</label>
                                            <div class="col-lg-9">
                                                <select class="form-control select2 req" id="rekening" name="rekening">
                                                    <option label="Label"></option>
                                                    @foreach ($CompanyAccount as $dataCompanyAccount)
                                                    <option value="{{$dataCompanyAccount->id}}">{{strtoupper($dataCompanyAccount->nama_bank).' - '.$dataCompanyAccount->nomor_rekening.' - '.ucwords($dataCompanyAccount->atas_nama)}}</option>
                                                    @endforeach
                                                </select>
                                                <span class="form-text text-danger err" style="display:none;">*Harap Pilih No. Rekening PT terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Alamat</label>
                                            <div class="col-lg-9">
                                                <textarea maxlength="50" class="form-control req" id="alamat" name="alamat" cols="4" style="resize:none;height:100px;">{{$Preference->alamat_pt}}</textarea>
                                                <span class="form-text text-danger err" style="display:none;">*Harap masukkan Alamat PT terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Kelurahan</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control" placeholder="Masukkan Kelurahan" name="kelurahan" id="kelurahan" value="{{$Preference->kelurahan_pt}}">
                                                <span class="form-text text-danger err" style="display:none;">*Harap masukkan Kelurahan PT terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Kecamatan</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control req" placeholder="Masukkan Kecamatan" name="kecamatan" id="kecamatan" value="{{$Preference->kecamatan_pt}}">
                                                <span class="form-text text-danger err" style="display:none;">*Harap masukkan Kecamatan PT terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Kota</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control req" placeholder="Masukkan Kota" name="kota" id="kota" value="{{$Preference->kota_pt}}">
                                                <span class="form-text text-danger err" style="display:none;">*Harap masukkan Kota PT terlebih dahulu!</span>
                                            </div>
                                        </div>

                                    </fieldset>
                                </div>

                                <div class="col-md-6">
                                    <fieldset>
                                        <legend class="font-weight-semibold">Flag Dokumen Cetak </legend>
                                        <div class="separator separator-solid separator-border-2 separator-dark"></div>
                                        <br>
                                        <div class="form-group row">
											<label class="control-label col-lg-3">Penawaran</label>
											<div class="col-lg-9">
                                                <div class="checkbox-inline">
                                                    <label class="checkbox checkbox">
                                                        @if($Preference->flag_default == "Y")
                                                        <input type="checkbox" class="checkFlag" value="Y" id="Default" checked>
                                                        @else
                                                        <input type="checkbox" class="checkFlag" value="Y" id="Default">
                                                        @endif
                                                        <span></span>
                                                    </label>
                                                </div>
												<input type="hidden" id="flagDefault" name="flagDefault" class="form-control" value="{{$Preference->flag_default}}" readonly>
											</div>
                                        </div>

                                        <div class="form-group row">
											<label class="control-label col-lg-3">Penawaran</label>
											<div class="col-lg-9">
                                                <div class="checkbox-inline">
                                                    <label class="checkbox checkbox">
                                                        @if($Preference->flag_quo == "Y")
                                                        <input type="checkbox" class="checkFlag" value="Y" id="QUO" checked>
                                                        @else
                                                        <input type="checkbox" class="checkFlag" value="Y" id="QUO">
                                                        @endif
                                                        <span></span>
                                                    </label>
                                                </div>
												<input type="hidden" id="flagQUO" name="flagQUO" class="form-control" value="{{$Preference->flag_quo}}" readonly>
											</div>
                                        </div>

                                        <div class="form-group row">
											<label class="control-label col-lg-3">Penjualan</label>
											<div class="col-lg-9">
                                                <div class="checkbox-inline">
                                                    <label class="checkbox checkbox">
                                                        @if($Preference->flag_so == "Y")
                                                        <input type="checkbox" class="checkFlag" value="Y" id="SO" checked>
                                                        @else
                                                        <input type="checkbox" class="checkFlag" value="Y" id="SO">
                                                        @endif
                                                        <span></span>
                                                    </label>
                                                </div>
												<input type="hidden" id="flagSO" name="flagSO" class="form-control" value="{{$Preference->flag_so}}" readonly>
											</div>
										</div>

                                        <div class="form-group row">
											<label class="control-label col-lg-3">Pengiriman Barang</label>
											<div class="col-lg-9">
                                                <div class="checkbox-inline">
                                                    <label class="checkbox checkbox">
                                                        @if($Preference->flag_do == "Y")
                                                        <input type="checkbox" class="checkFlag" value="Y" id="DO" checked>
                                                        @else
                                                        <input type="checkbox" class="checkFlag" value="Y" id="DO">
                                                        @endif
                                                        <span></span>
                                                    </label>
                                                </div>
												<input type="hidden" id="flagDO" name="flagDO" class="form-control" value="{{$Preference->flag_do}}" readonly>
											</div>
                                        </div>

                                        <div class="form-group row">
											<label class="control-label col-lg-3">Invoice DP</label>
											<div class="col-lg-9">
                                                <div class="checkbox-inline">
                                                    <label class="checkbox checkbox">
                                                        @if($Preference->flag_inv_dp == "Y")
                                                        <input type="checkbox" class="checkFlag" value="Y" id="DP" checked>
                                                        @else
                                                        <input type="checkbox" class="checkFlag" value="Y" id="DP">
                                                        @endif
                                                        <span></span>
                                                    </label>
                                                </div>
												<input type="hidden" id="flagDP" name="flagDP" class="form-control" value="{{$Preference->flag_inv_dp}}" readonly>
											</div>
										</div>

                                        <div class="form-group row">
											<label class="control-label col-lg-3">Invoice Penjualan</label>
											<div class="col-lg-9">
                                                <div class="checkbox-inline">
                                                    <label class="checkbox checkbox">
                                                        @if($Preference->flag_inv_sale == "Y")
                                                        <input type="checkbox" class="checkFlag" value="Y" id="INVS" checked>
                                                        @else
                                                        <input type="checkbox" class="checkFlag" value="Y" id="INVS">
                                                        @endif
                                                        <span></span>
                                                    </label>
                                                </div>
												<input type="hidden" id="flagINVS" name="flagINVS" class="form-control" value="{{$Preference->flag_inv_sale}}" readonly>
											</div>
										</div>

                                        <div class="form-group row">
											<label class="control-label col-lg-3">Pembelian Barang</label>
											<div class="col-lg-9">
                                                <div class="checkbox-inline">
                                                    <label class="checkbox checkbox">
                                                        @if($Preference->flag_po == "Y")
                                                        <input type="checkbox" class="checkFlag" value="Y" id="PO" checked>
                                                        @else
                                                        <input type="checkbox" class="checkFlag" value="Y" id="PO">
                                                        @endif
                                                        <span></span>
                                                    </label>
                                                </div>
												<input type="hidden" id="flagPO" name="flagPO" class="form-control" value="{{$Preference->flag_po}}" readonly>
											</div>
										</div>

                                        <div class="form-group row">
											<label class="control-label col-lg-3">Penerimaan Barang</label>
											<div class="col-lg-9">
                                                <div class="checkbox-inline">
                                                    <label class="checkbox checkbox">
                                                        @if($Preference->flag_rcv == "Y")
                                                        <input type="checkbox" class="checkFlag" value="Y" id="RCV" checked>
                                                        @else
                                                        <input type="checkbox" class="checkFlag" value="Y" id="RCV">
                                                        @endif
                                                        <span></span>
                                                    </label>
                                                </div>
												<input type="hidden" id="flagRCV" name="flagRCV" class="form-control" value="{{$Preference->flag_rcv}}" readonly>
											</div>
										</div>

                                        <div class="form-group row">
											<label class="control-label col-lg-3">Invoice Pembelian</label>
											<div class="col-lg-9">
                                                <div class="checkbox-inline">
                                                    <label class="checkbox checkbox">
                                                        @if($Preference->flag_inv_purc == "Y")
                                                        <input type="checkbox" class="checkFlag" value="Y" id="INVP" checked>
                                                        @else
                                                        <input type="checkbox" class="checkFlag" value="Y" id="INVP">
                                                        @endif
                                                        <span></span>
                                                    </label>
                                                </div>
												<input type="hidden" id="flagINVP" name="flagINVP" class="form-control" value="{{$Preference->flag_inv_purc}}" readonly>
											</div>
										</div>

                                    </fieldset>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-white d-sm-flex justify-content-sm-between align-items-sm-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-light-danger font-weight-bold mr-2" id="cancel">Keluar <i class="flaticon2-cancel icon-sm"></i></button>
                            </div>

                            <div class="mt-2 mt-sm-0">
                                <button type="submit" class="btn btn-light-primary font-weight-bold mr-2" id="btnSubmit"> Simpan <i class="flaticon-paper-plane-1"></i></button>
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
            $('#rekening').select2({
                placeholder: "Pilih No. Rekening",
                allowClear: true
            });
        });

        $("#cancel").on('click', function(e) {
            Swal.fire({
                title: "Batal?",
                text: "Apakah anda ingin membatalkan perubahan data?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if (result.value) {
                    window.location.href = '{{ url("/Preference") }}';
                    // Swal.fire(
                    //     "Deleted!",
                    //     "Your file has been deleted.",
                    //     "success"
                    // )
                    // result.dismiss can be "cancel", "overlay",
                    // "close", and "timer"
                } else if (result.dismiss === "cancel") {
                    // Swal.fire(
                    //     "Cancelled",
                    //     "Your imaginary file is safe :)",
                    //     "error"
                    // )
                    e.preventDefault();
                }
            });
	    });

        function validasiangka(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }
            return true;
        }

        $(document).on('keyup', '.npwp', function() {
            txt1 = $("#npwp1").val();
            txt2 = $("#npwp2").val();
            txt3 = $("#npwp3").val();
            txt4 = $("#npwp4").val();
            txt5 = $("#npwp5").val();
            txt6 = $("#npwp6").val();
            count1 = txt1.length;
            count2 = txt2.length;
            count3 = txt3.length;
            count4 = txt4.length;
            count5 = txt5.length;

            if (txt2=="") {
            	if (count1 == 2) {
	            	$("#npwp2").focus();
	            }
            }
            else if (txt3=="") {
            	if (count2 == 3) {
	            	$("#npwp3").focus();
	            }
            }
             else if (txt4=="") {
            	if (count3 == 3) {
	            	$("#npwp4").focus();
	            }
            }
             else if (txt5=="") {
            	if (count4 == 1) {
	            	$("#npwp5").focus();
	            }
            }
             else if (txt6=="") {
            	if (count5 == 3) {
	            	$("#npwp6").focus();
	            }
            }
        });

        $(document).ready(function() {
            $("#Default").change(function() {
                if(this.checked) {
                    $("#flagDefault").val("Y");
                }
                else {
                    $("#flagDefault").val("N");
                }
            });

            $("#QUO").change(function() {
                if(this.checked) {
                    $("#flagQUO").val("Y");
                }
                else {
                    $("#flagQUO").val("N");
                }
            });

            $("#SO").change(function() {
                if(this.checked) {
                    $("#flagSO").val("Y");
                }
                else {
                    $("#flagSO").val("N");
                }
            });

            $("#DO").change(function() {
                if(this.checked) {
                    $("#flagDO").val("Y");
                }
                else {
                    $("#flagDO").val("N");
                }
            });

            $("#DP").change(function() {
                if(this.checked) {
                    $("#flagDP").val("Y");
                }
                else {
                    $("#flagDP").val("N");
                }
            });

            $("#INVS").change(function() {
                if(this.checked) {
                    $("#flagINVS").val("Y");
                }
                else {
                    $("#flagINVS").val("N");
                }
            });

            $("#PO").change(function() {
                if(this.checked) {
                    $("#flagPO").val("Y");
                }
                else {
                    $("#flagPO").val("N");
                }
            });

            $("#RCV").change(function() {
                if(this.checked) {
                    $("#flagRCV").val("Y");
                }
                else {
                    $("#flagRCV").val("N");
                }
            });

            $("#INVP").change(function() {
                if(this.checked) {
                    $("#flagINVP").val("Y");
                }
                else {
                    $("#flagINVP").val("N");
                }
            });
        });

        $(document).ready(function() {
            var npwp = "{{$Preference->npwp_pt}}";
            var npwp_split = npwp.split(".");
            var npwp1 = npwp_split[0];
            var npwp2 = npwp_split[1];
            var npwp3 = npwp_split[2];
            var npwp_4 = npwp_split[3];
            var npwp_4_5 = npwp_4.split("-");
            var npwp4 = npwp_4_5[0];
            var npwp5 = npwp_4_5[1];
            var npwp6 = npwp_split[4];

            $("#npwp1").val(npwp1);
            $("#npwp2").val(npwp2);
            $("#npwp3").val(npwp3);
            $("#npwp4").val(npwp4);
            $("#npwp5").val(npwp5);
            $("#npwp6").val(npwp6);

            $("#rekening").val("{{$Preference->rekening}}").trigger('change');
        });

        $("#form_add").submit(function(e){
            e.preventDefault();

            Swal.fire({
                title: "Update Data?",
                text: "Apakah perubahan data sudah sesuai?",
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

    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
