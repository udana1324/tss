@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h5 class="card-title text-white">Detail Preferensi</h5>
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
                                                <input type="text" class="form-control req" placeholder="Masukkan Nama PT" name="nama_pt" id="nama_pt" value="{{$Preference->nama_pt}}" readonly>
                                            <span class="form-text text-danger err" style="display:none;">*Harap masukkan Nama PT terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">No. Telp</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control req" placeholder="Masukkan No. Telp" name="telp" id="telp" value="{{$Preference->telp_pt}}" readonly>
                                            <span class="form-text text-danger err" style="display:none;">*Harap masukkan No. Telp PT terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Email</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control req" placeholder="Masukkan Email" name="email" id="email" value="{{$Preference->email_pt}}" readonly>
                                            <span class="form-text text-danger err" style="display:none;">*Harap masukkan Email PT terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Website</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control req" placeholder="Masukkan Website" name="web" id="web" value="{{$Preference->website_pt}}" readonly>
                                            <span class="form-text text-danger err" style="display:none;">*Harap masukkan Website PT terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">NPWP</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="npwp form-control req" maxlength="2" style="width:60px;display:inline-block;text-align:center;" onkeypress="return validasiangka(event);" name="npwp1" id="npwp1" readonly>
                                                            .
                                                <input type="text" class="npwp form-control req" maxlength="3" style="width:60px;display:inline-block;text-align:center;" onkeypress="return validasiangka(event);" name="npwp2" id="npwp2" readonly>
                                                            .
                                                <input type="text" class="npwp form-control req" maxlength="3" style="width:60px;display:inline-block;text-align:center;" onkeypress="return validasiangka(event);" name="npwp3" id="npwp3" readonly>
                                                            .
                                                <input type="text" class="npwp form-control req" maxlength="1" style="width:60px;display:inline-block;text-align:center;" onkeypress="return validasiangka(event);" name="npwp4" id="npwp4" readonly>
                                                            -
                                                <input type="text" class="npwp form-control req" maxlength="3" style="width:60px;display:inline-block;text-align:center;" onkeypress="return validasiangka(event);" name="npwp5" id="npwp5" readonly>
                                                            .
                                                <input type="text" class="npwp form-control req" maxlength="3" style="width:60px;display:inline-block;text-align:center;" onkeypress="return validasiangka(event);" name="npwp6" id="npwp6" readonly>
                                                <span class="form-text text-muted"> Contoh : 99.999.999.9-999.999</span>
                                                <span class="form-text text-danger err" style="display:none;">*Harap masukkan NPWP PT terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Rekening</label>
                                            <div class="col-lg-9">
                                                <select class="form-control select2 req" data-placeholder="Pilih Rekening" data-fouc id="rekening" name="rekening" disabled>
                                                    <option></option>
                                                    @foreach ($CompanyAccount as $dataCompanyAccount)
                                                    <option value="{{$dataCompanyAccount->id}}" readonly>{{strtoupper($dataCompanyAccount->nama_bank).' - '.$dataCompanyAccount->nomor_rekening.' - '.ucwords($dataCompanyAccount->atas_nama)}}</option>
                                                    @endforeach
                                                </select>
                                                <span class="form-text text-danger err" style="display:none;">*Harap Pilih No. Rekening PT terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Alamat</label>
                                            <div class="col-lg-9">
                                                <textarea maxlength="50" class="form-control req" id="alamat" name="alamat" cols="4" style="resize:none;height:100px;" readonly>{{$Preference->alamat_pt}}</textarea>
                                                <span class="form-text text-danger err" style="display:none;">*Harap masukkan Alamat PT terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Kelurahan</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control" placeholder="Masukkan Kelurahan" name="kelurahan" id="kelurahan" value="{{$Preference->kelurahan_pt}}" readonly>
                                                <span class="form-text text-danger err" style="display:none;">*Harap masukkan Kelurahan PT terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Kecamatan</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control req" placeholder="Masukkan Kecamatan" name="kecamatan" id="kecamatan" value="{{$Preference->kecamatan_pt}}" readonly>
                                                <span class="form-text text-danger err" style="display:none;">*Harap masukkan Kecamatan PT terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Kota</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control req" placeholder="Masukkan Kota" name="kota" id="kota" value="{{$Preference->kota_pt}}" readonly>
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
											<label class="control-label col-lg-3">Default</label>
											<div class="col-lg-9">
                                                <div class="checkbox-inline">
                                                    <label class="checkbox checkbox">
                                                        @if($Preference->flag_default == "Y")
                                                        <input type="checkbox" class="checkFlag" value="Y" id="Default" checked disabled>
                                                        @else
                                                        <input type="checkbox" class="checkFlag" value="Y" id="Default" disabled>
                                                        @endif
                                                        <span></span>
                                                    </label>
                                                </div>
											</div>
                                        </div>

                                        <div class="form-group row">
											<label class="control-label col-lg-3">Penawaran</label>
											<div class="col-lg-9">
                                                <div class="checkbox-inline">
                                                    <label class="checkbox checkbox">
                                                        @if($Preference->flag_quo == "Y")
                                                        <input type="checkbox" class="checkFlag" value="Y" id="QUO" checked disabled>
                                                        @else
                                                        <input type="checkbox" class="checkFlag" value="Y" id="QUO" disabled>
                                                        @endif
                                                        <span></span>
                                                    </label>
                                                </div>
											</div>
                                        </div>

                                        <div class="form-group row">
											<label class="control-label col-lg-3">Penjualan</label>
											<div class="col-lg-9">
                                                <div class="checkbox-inline">
                                                    <label class="checkbox checkbox">
                                                        @if($Preference->flag_so == "Y")
                                                        <input type="checkbox" class="checkFlag" value="Y" id="SO" checked disabled>
                                                        @else
                                                        <input type="checkbox" class="checkFlag" value="Y" id="SO" disabled>
                                                        @endif
                                                        <span></span>
                                                    </label>
                                                </div>
											</div>
										</div>

                                        <div class="form-group row">
											<label class="control-label col-lg-3">Pengiriman Barang</label>
											<div class="col-lg-9">
                                                <div class="checkbox-inline">
                                                    <label class="checkbox checkbox">
                                                        @if($Preference->flag_do == "Y")
                                                        <input type="checkbox" class="checkFlag" value="Y" id="DO" checked disabled>
                                                        @else
                                                        <input type="checkbox" class="checkFlag" value="Y" id="DO" disabled>
                                                        @endif
                                                        <span></span>
                                                    </label>
                                                </div>
											</div>
                                        </div>

                                        <div class="form-group row">
											<label class="control-label col-lg-3">Invoice DP</label>
											<div class="col-lg-9">
                                                <div class="checkbox-inline">
                                                    <label class="checkbox checkbox">
                                                        @if($Preference->flag_inv_dp == "Y")
                                                        <input type="checkbox" class="checkFlag" value="Y" id="DP" checked disabled>
                                                        @else
                                                        <input type="checkbox" class="checkFlag" value="Y" id="DP" disabled>
                                                        @endif
                                                        <span></span>
                                                    </label>
                                                </div>
											</div>
										</div>

                                        <div class="form-group row">
											<label class="control-label col-lg-3">Invoice Penjualan</label>
											<div class="col-lg-9">
                                                <div class="checkbox-inline">
                                                    <label class="checkbox checkbox">
                                                        @if($Preference->flag_inv_sale == "Y")
                                                        <input type="checkbox" class="checkFlag" value="Y" id="INVS" checked disabled>
                                                        @else
                                                        <input type="checkbox" class="checkFlag" value="Y" id="INVS" disabled>
                                                        @endif
                                                        <span></span>
                                                    </label>
                                                </div>
											</div>
										</div>

                                        <div class="form-group row">
											<label class="control-label col-lg-3">Pembelian Barang</label>
											<div class="col-lg-9">
                                                <div class="checkbox-inline">
                                                    <label class="checkbox checkbox">
                                                        @if($Preference->flag_po == "Y")
                                                        <input type="checkbox" class="checkFlag" value="Y" id="PO" checked disabled>
                                                        @else
                                                        <input type="checkbox" class="checkFlag" value="Y" id="PO" disabled>
                                                        @endif
                                                        <span></span>
                                                    </label>
                                                </div>
											</div>
										</div>

                                        <div class="form-group row">
											<label class="control-label col-lg-3">Penerimaan Barang</label>
											<div class="col-lg-9">
                                                <div class="checkbox-inline">
                                                    <label class="checkbox checkbox">
                                                        @if($Preference->flag_rcv == "Y")
                                                        <input type="checkbox" class="checkFlag" value="Y" id="RCV" checked disabled>
                                                        @else
                                                        <input type="checkbox" class="checkFlag" value="Y" id="RCV" disabled>
                                                        @endif
                                                        <span></span>
                                                    </label>
                                                </div>
											</div>
										</div>

                                        <div class="form-group row">
											<label class="control-label col-lg-3">Invoice Pembelian</label>
											<div class="col-lg-9">
                                                <div class="checkbox-inline">
                                                    <label class="checkbox checkbox">
                                                        @if($Preference->flag_inv_purc == "Y")
                                                        <input type="checkbox" class="checkFlag" value="Y" id="INVP" checked disabled>
                                                        @else
                                                        <input type="checkbox" class="checkFlag" value="Y" id="INVP" disabled>
                                                        @endif
                                                        <span></span>
                                                    </label>
                                                </div>
											</div>
										</div>

                                    </fieldset>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-white d-sm-flex justify-content-sm-between align-items-sm-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-light-danger font-weight-bold mr-2" onclick="window.location.href = '{{ url('/Preference') }}';">Keluar <i class="flaticon2-cancel icon-sm"></i></button>
                            </div>

                            <div class="mt-2 mt-sm-0">

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

    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
