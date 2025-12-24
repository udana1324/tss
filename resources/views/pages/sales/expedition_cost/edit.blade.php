@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h5 class="card-title text-white">Ubah Biaya Ekspedisi</h5>
					</div>
                    <form action="{{ route('ExpeditionCost.update', $dataCost->id) }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            @method('PUT')
                            <div class="row">
								<div class="col-md-6">
									<fieldset>
										<legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi Ekspedisi </h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
										<div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Kode Biaya :</label>
                                            <div class="col-lg-9">
                                                <input type="hidden" name="mode" id="mode" value="load" readonly>
                                                <input type="text" class="form-control bg-slate-600 border-slate-600 border-1" placeholder="Auto Generated" name="kode_biaya" id="kode_biaya" value="{{strtoupper($dataCost->no_biaya)}}" readonly>
                                            </div>
                                        </div>

										<div class="form-group">
                                            <label>Ekspedisi :</label>
                                            <div>
                                                <select class="form-control select2 req" id="branch" name="branch">
                                                    <option label="Label"></option>
                                                    @foreach($dataCabang as $ekspedisi)
                                                    <option value="{{$ekspedisi->id}}">{{strtoupper($ekspedisi->nama_cabang)}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <span class="form-text text-danger err" style="display:none;">*Harap pilih ekspedisi terlebih dahulu!</span>
                                        </div>

										<div class="form-group">
                                            <label>Alamat Ekspedisi :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="hidden" class=" form-control req" name="id_alamat" id="id_alamat">
                                                    <textarea class="form-control" name="alamat" id="alamat" style="resize:none;" readonly></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Tanggal Kirim Ekspedisi :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <input type="hidden" class="form-control req" name="tanggal" id="tanggal">
                                                <input type="text" class="form-control" name="tanggal_picker" id="tanggal_picker" readonly>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal kirim terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>No. Resi:</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <input type="text" class="form-control" name="resi" id="resi" value="{{strtoupper($dataCost->no_resi)}}">
                                            </div>
                                        </div>

									</fieldset>
								</div>

								<div class="col-md-6">
									<fieldset>
								        <legend class="text-muted"><h6><i class="fab la-buffer"></i> Rincian Biaya</h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
										<div class="form-group">
                                            <label>No. Surat Jalan :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <select class="form-control select2 detailItem" id="delivery" name="delivery">
                                                        <option label="Label"></option>
                                                    </select>
                                                    <span class="form-text text-danger errItem" style="display:none;">*Harap pilih surat jalan terlebih dahulu!</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row mb-0">
										    <div class="form-group hargaJualItem col-xl-4 col-sm-6 col-xs-12">
											    <label>Tanggal Surat Jalan :</label>
											    <div class="input-group input-group-solid">
											        <input type="text" id="tglSJ" class="form-control text-right" readonly>
											    </div>
                                            </div>

                                            <div class="form-group hargaJualItem col-xl-4 col-sm-6 col-xs-12">
                                                <label>Tanggal Kirim :</label>
                                                <div class="input-group input-group-solid">
                                                    <input type="text" id="tglKirimSJ" class="form-control text-right" readonly>
                                                </div>
										    </div>

										    <div class="form-group hargaJualItem col-xl-4 col-sm-12">
                                                <label>Jumlah Qty Surat Jalan :</label>
                                                <div class="input-group input-group-solid">
                                                    <input type="text" id="qtySJMask" class="form-control text-right" readonly>
                                                        <input type="hidden" id="qtySJ" class="form-control text-right" readonly>
                                                </div>
										    </div>
										</div>

                                        <div class="form-group">
											<label>Tarif per Kota :</label>
											<div class="input-group">
											    <div class="col-9 pl-0">
                                                    <select class="form-control select2" id="tarif_kota" name="tarif_kota">
                                                        <option label="Label"></option>
                                                    </select>
                                                </div>
											    <div class="col-3 pr-0">
                                                    <div class="input-group input-group-solid">
                                                        <input type="text" id="tarif_per_kota_mask" class="form-control form-control-solid" readonly>
                                                        <input type="hidden" id="tarif_per_kota" class="form-control form-control-solid" readonly>
                                                        <span class="input-group-append">
                                                            <button type="button" class="btn btn-primary btn-icon btnHarga" data-toggle="tooltip"  title="Gunakan Tarif" data-placement="top">
                                                                <i class="la la-check"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                </div>
											</div>
										</div>

                                        <div class="form-group row mb-0">
										    <div class="form-group col-xl-4">
                                                <label>Tujuan Kirim :</label>
                                                <div class="input-group">
                                                        <input type="text" id="kota_tujuan" class="form-control detailItem" autocomplete="off">
                                                        <span class="form-text text-danger errItem" style="display:none;">*Harap masukkan kota tujuan terlebih dahulu!</span>
                                                </div>
                                            </div>

                                            <div class="form-group col-xl-4">
											    <label>Nama Barang :</label>
											    <div class="input-group">
											        <input type="text" id="nama_resi" class="form-control detailItem" autocomplete="off" value="PAPERCUP">
                                                    <span class="form-text text-danger errItem" style="display:none;">*Harap masukkan berat/volume terlebih dahulu!</span>
                                                </div>
										    </div>

                                            <div class="form-group col-xl-4">
											    <label>Ditagihkan? :</label>
											    <div class="input-group">
											        <div class="checkbox-inline">
                                                        <label class="checkbox checkbox-lg">
                                                            <input type="checkbox" value="Y" id="flag_tagih" name="flag_tagih">
                                                            <span></span>Ya
                                                        </label>
                                                    </div>
                                                </div>
										    </div>
										</div>

                                        <div class="form-group row mb-0">
                                            <div class="form-group col-xl-3 col-sm-6 col-xs-12">
                                                <label>Qty Dus :</label>
                                                <div class="input-group">
                                                    <input type="text" id="qty_dus_mask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right">
                                                    <input type="hidden" id="qty_dus" min="0" class="form-control text-right detailItem numeric" autocomplete="off">
                                                    <span class="form-text text-danger errItem" style="display:none;">*Harap masukkan qty dus terlebih dahulu!</span>
                                                    <span class="form-text text-danger errItemNumeric" style="display:none;">*qty dus tidak dapat dibawah atau 0!</span>
                                                </div>
                                            </div>

                                            <div class="form-group col-xl-3 col-sm-6 col-xs-12">
											    <label>Berat/Vol :</label>
											    <div class="input-group">
											        <input type="text" id="berat_mask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right">
                                                    <input type="hidden" id="berat" min="0" class="form-control text-right detailItem numeric" autocomplete="off">
                                                    <span class="form-text text-danger errItem" style="display:none;">*Harap masukkan berat/volume terlebih dahulu!</span>
                                                    <span class="form-text text-danger errItemNumeric" style="display:none;">*Berat/Volume tidak dapat dibawah atau 0!</span>
											    </div>
										    </div>

                                            <div class="form-group col-xl-3 col-sm-12">
                                                <label>Tarif :</label>
                                                <div class="input-group">
                                                    <input type="text" id="tarif_mask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right">
                                                    <input type="hidden" id="tarif" min="0" class="form-control text-right detailItem numeric" autocomplete="off">
                                                    <span class="form-text text-danger errItem" style="display:none;">*Harap masukkan tarif terlebih dahulu!</span>
                                                    <span class="form-text text-danger errItemNumeric" style="display:none;">*Tarif tidak dapat dibawah atau 0!</span>
                                                </div>
                                            </div>

                                            <div class="form-group col-xl-3 col-sm-12">
                                                <label>Diskon (%) :</label>
                                                <div class="input-group">
                                                    <input type="text" id="discount_mask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right">
                                                    <input type="hidden" id="discount" min="0" class="form-control text-right" autocomplete="off">
                                                </div>
                                            </div>
										</div>

                                        <div class="form-group row mb-0">
                                            <div class="form-group col-xl-3 col-sm-6 col-xs-12">

                                            </div>

                                            <div class="form-group col-xl-3 col-sm-6 col-xs-12">

										    </div>

                                            <div class="form-group col-xl-3 col-sm-12">

                                            </div>

                                            <div class="form-group col-xl-3 col-sm-12">
                                                <label>Subtotal :</label>
                                                <div class="input-group">
                                                    <input type="text" id="subtotal_mask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right" readonly>
                                                    <input type="hidden" id="subtotal" min="0" class="form-control text-right detailItem numeric" autocomplete="off" readonly>
                                                    <span class="form-text text-danger errItem" style="display:none;">*Harap masukkan subtotal terlebih dahulu!</span>
                                                    <span class="form-text text-danger errItemNumeric" style="display:none;">*Subtotal tidak dapat dibawah atau 0!</span>
                                                </div>
                                            </div>
										</div>

                                        <div class="form-group row">
											<label class="col-lg-3 col-form-label"></label>
											<div class="col-lg-9">
												<button type="button" class="btn btn-primary font-weight-bold" id="btnAddItem">Tambah</button>
											</div>
										</div>

									</fieldset>
								</div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset>
                                        <legend class="text-muted"><h6><i class="la la-list"></i> List Biaya</h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>

                                        <div class="datatable datatable-bordered datatable-head-custom" id="list_item"></div>

                                    </fieldset>
                                </div>
                            </div>

							<br>
							<div class="row">
								<div class="col-md-6">

								</div>

								<div class="col-md-6">

									<div class="form-group row">
										<label class="col-lg-3 col-form-label">Total Jumlah Dus</label>
										<div class="col-lg-9">
											<input type="text" value="0" id="ttlQtyMask" class="form-control text-right" readonly>
											<input type="hidden" id="ttlQty" name="ttlQty" class="form-control text-right" readonly>
										</div>
									</div>

									<div class="form-group row">
										<label class="col-lg-3 col-form-label">Total Berat/Volume</label>
										<div class="col-lg-9">
											<input type="text" value="0" id="ttlBeratMask" class="form-control text-right" readonly>
											<input type="hidden" id="ttlBerat" name="ttlBerat" class="form-control text-right" readonly>
										</div>
									</div>

									<div class="form-group row">
										<label class="col-lg-3 col-form-label">Total Biaya</label>
										<div class="col-lg-9">
											<input type="text" value="0" id="ttlBiayaMask" class="form-control text-right" readonly>
											<input type="hidden" id="ttlBiaya" name="ttlBiaya" class="form-control text-right" readonly>
										</div>
									</div>

								</div>
							</div>

                        </div>

                        <div class="card-footer bg-white d-sm-flex justify-content-sm-between align-items-sm-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-light-danger font-weight-bold mr-2" id="cancel">Keluar <i class="flaticon2-cancel icon-sm"></i></button>
                            </div>

                            <div class="mt-2 mt-sm-0">
                                <button type="button" style="display: none;" id="btnModalEditItem" data-toggle="modal" data-target="#modal_form_edit_item"></button>
                                <button type="submit" class="btn btn-light-primary font-weight-bold mr-2"> Simpan <i class="flaticon-paper-plane-1"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Horizontal form edit item-->
				<div id="modal_form_edit_item" class="modal fade">
				    <div class="modal-dialog modal-lg">
					    <div class="modal-content">
						    <div class="modal-header bg-primary">

							    <h5 class="modal-title text-white">Ubah Harga Item</h5>
						    </div>
						    <div class="modal-body">
							    <div class="row">
                                    <div class="col-md-12">
                                        <fieldset>
                                            <legend class="text-muted"><h6><i class="fab la-buffer"></i> Rincian Biaya</h6></legend>
                                            <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                            <br>
                                            <div class="form-group">
                                                <label>No. Surat Jalan :</label>
                                                <div class="form-group form-group-feedback form-group-feedback-right">
                                                    <div class="input-group">
                                                        <input type="hidden" id="idDetail" class="form-control" readonly>
                                                        <input type="text" id="NoSJEdit" class="form-control" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row mb-0">
                                                <div class="form-group hargaJualItem col-xl-4 col-sm-6 col-xs-12">
                                                    <label>Tanggal Surat Jalan :</label>
                                                    <div class="input-group input-group-solid">
                                                        <input type="text" id="tglSJEdit" class="form-control text-right" readonly>
                                                    </div>
                                                </div>

                                                <div class="form-group hargaJualItem col-xl-4 col-sm-6 col-xs-12">
                                                    <label>Tanggal Kirim :</label>
                                                    <div class="input-group input-group-solid">
                                                        <input type="text" id="tglKirimSJEdit" class="form-control text-right" readonly>
                                                    </div>
                                                </div>

                                                <div class="form-group hargaJualItem col-xl-4 col-sm-12">
                                                    <label>Jumlah Qty Surat Jalan :</label>
                                                    <div class="input-group input-group-solid">
                                                        <input type="text" id="qtySJEditMask" class="form-control text-right" readonly>
                                                        <input type="hidden" id="qtySJEdit" class="form-control text-right" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row mb-0">
                                                <div class="form-group col-xl-4">
                                                    <label>Tujuan Kirim :</label>
                                                    <div class="input-group">
                                                        <input type="text" id="kota_tujuanEdit" class="form-control detailItemEdit" autocomplete="off">
                                                        <span class="form-text text-danger errItemEdit" style="display:none;">*Harap masukkan qty dus terlebih dahulu!</span>
                                                    </div>
                                                </div>

                                                <div class="form-group col-xl-4">
                                                    <label>Nama Barang :</label>
                                                    <div class="input-group">
                                                        <input type="text" id="nama_resiEdit" class="form-control detailItemEdit" autocomplete="off">
                                                        <span class="form-text text-danger errItemEdit" style="display:none;">*Harap masukkan berat/volume terlebih dahulu!</span>
                                                    </div>
                                                </div>

                                                <div class="form-group col-xl-4">
                                                    <label>Ditagihkan? :</label>
                                                    <div class="input-group">
                                                        <div class="checkbox-inline">
                                                            <label class="checkbox checkbox-lg">
                                                                <input type="checkbox" value="Y" id="flag_tagihEdit" name="flag_tagihEdit">
                                                                <span></span>Ya
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row mb-0">
                                                <div class="form-group col-xl-3 col-sm-6 col-xs-12">
                                                    <label>Qty Dus :</label>
                                                    <div class="input-group">
                                                        <input type="text" id="qty_dusEdit_mask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right">
                                                        <input type="hidden" id="qty_dusEdit" min="0" class="form-control text-right detailItemEdit numericEdit" autocomplete="off">
                                                        <span class="form-text text-danger errItemEdit" style="display:none;">*Harap masukkan qty dus terlebih dahulu!</span>
                                                        <span class="form-text text-danger errItemEditNumeric" style="display:none;">*qty dus tidak dapat dibawah atau 0!</span>
                                                    </div>
                                                </div>

                                                <div class="form-group col-xl-3 col-sm-6 col-xs-12">
                                                    <label>Berat/Vol :</label>
                                                    <div class="input-group">
                                                        <input type="text" id="beratEdit_mask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right">
                                                        <input type="hidden" id="beratEdit" min="0" class="form-control text-right detailItemEdit numericEdit" autocomplete="off">
                                                        <span class="form-text text-danger errItemEdit" style="display:none;">*Harap masukkan berat/volume terlebih dahulu!</span>
                                                        <span class="form-text text-danger errItemEditNumeric" style="display:none;">*Berat/Volume tidak dapat dibawah atau 0!</span>
                                                    </div>
                                                </div>

                                                <div class="form-group col-xl-3 col-sm-12">
                                                    <label>Tarif :</label>
                                                    <div class="input-group">
                                                        <input type="text" id="tarifEdit_mask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right">
                                                        <input type="hidden" id="tarifEdit" min="0" class="form-control text-right detailItemEdit numericEdit" autocomplete="off">
                                                        <span class="form-text text-danger errItemEdit" style="display:none;">*Harap masukkan tarif terlebih dahulu!</span>
                                                        <span class="form-text text-danger errItemEditNumeric" style="display:none;">*Tarif tidak dapat dibawah atau 0!</span>
                                                    </div>
                                                </div>

                                                <div class="form-group col-xl-3 col-sm-12">
                                                    <label>Diskon (%) :</label>
                                                    <div class="input-group">
                                                        <input type="text" id="discountEdit_mask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right">
                                                        <input type="hidden" id="discountEdit" min="0" class="form-control text-right" autocomplete="off">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row mb-0">
                                                <div class="form-group col-xl-3 col-sm-12">
                                                    <label>Subtotal :</label>
                                                    <div class="input-group">
                                                        <input type="text" id="subtotalEdit_mask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right">
                                                        <input type="hidden" id="subtotalEdit" min="0" class="form-control text-right detailItemEdit numericEdit" autocomplete="off">
                                                        <span class="form-text text-danger errItemEdit" style="display:none;">*Harap masukkan subtotal terlebih dahulu!</span>
                                                        <span class="form-text text-danger errItemEditNumeric" style="display:none;">*Subtotal tidak dapat dibawah atau 0!</span>
                                                    </div>
                                                </div>
                                            </div>

                                        </fieldset>
                                    </div>
                                </div>
						    </div>

						    <div class="modal-footer">
							    <button type="button" class="btn btn-primary" id="btnEditItem" data-dismiss="modal">Simpan</button>
							    <button type="button"class="btn btn-light me-3" data-dismiss="modal">batal</button>
						    </div>
					    </div>
				    </div>
			    </div>
				<!-- /horizontal form edit item -->
			</div>
			<!-- /content area -->
@endsection
@section('scripts')
    <script type="text/javascript">

        function formatDate(strDate) {
            var arrMonth = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            var date = new Date(strDate);
            var day = date.getDate();
            var month = date.getMonth();
            var year = date.getFullYear();

            return day + ' ' + arrMonth[month] + ' ' + year;
        }

        $(document).ready(function () {
            $('#branch').select2({
                allowClear: true,
                placeholder: "Pilih Ekspedisi"
            });

            $('#tarif_kota').select2({
                allowClear: true,
                placeholder: "Pilih Kota"
            });

            $('#delivery').select2({
                allowClear: true,
                placeholder: "Pilih Pengiriman Barang"
            });

            $('#tanggal_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                autoclose : true,
                format : "dd MM yyyy",
                locale: "id",
            });

            $("#qty_dus_mask").autoNumeric('init');
            $("#tarif_mask").autoNumeric('init');
            $("#berat_mask").autoNumeric('init');
            $("#discount_mask").autoNumeric('init');
            $("#subtotal_mask").autoNumeric('init');

            $("#qty_dusEdit_mask").autoNumeric('init');
            $("#tarifEdit_mask").autoNumeric('init');
            $("#beratEdit_mask").autoNumeric('init');
            $("#discountEdit_mask").autoNumeric('init');
            $("#subtotalEdit_mask").autoNumeric('init');

            $("#tanggal_picker").datepicker('setDate', new Date());

            $("#tanggal_picker").datepicker('setDate', new Date('{{$dataCost->tanggal_kirim}}'));
            $("#branch").val("{{$dataCost->id_cabang_ekspedisi}}").trigger("change");
            footerDataForm('{{$dataCost->id}}');
            $("#mode").val("edit");
        });

        $("#tanggal_picker").on('change', function() {
            $("#tanggal").val($("#tanggal_picker").data('datepicker').getFormattedDate('yyyy-mm-dd'));
        });

        $("#qty_dus_mask").on('change', function() {
            $("#qty_dus").val($("#qty_dus_mask").autoNumeric("get"));
        });

        $("#tarif_mask").on('change', function() {
            $("#tarif").val($("#tarif_mask").autoNumeric("get"));

            var tarif = $("#tarif_mask").autoNumeric("get");
            var berat = $("#berat").val();
            var subtotal = 0;
            if (berat != "" && tarif != "") {
                subtotal = parseFloat(berat) * parseFloat(tarif);
                $("#subtotal_mask").val(parseFloat(subtotal).toLocaleString('id-ID', { maximumFractionDigits: 2})).trigger("change");
            }
        });

        $("#berat_mask").on('change', function() {
            $("#berat").val($("#berat_mask").autoNumeric("get"));

            var berat = $("#berat_mask").autoNumeric("get");
            var tarif = $("#tarif").val();
            var subtotal = 0;
            if (berat != "" && tarif != "") {
                subtotal = parseFloat(berat) * parseFloat(tarif);
                $("#subtotal_mask").val(parseFloat(subtotal).toLocaleString('id-ID', { maximumFractionDigits: 2})).trigger("change");
            }
        });

        $("#discount_mask").on('change', function() {
            $("#discount").val($("#discount_mask").autoNumeric("get"));

            var berat = $("#berat").val();
            var tarif = $("#tarif").val();
            var discount = $("#discount_mask").autoNumeric("get");
            var subtotal = 0;
            if (berat != "" && tarif != "") {
                subtotal = parseFloat(berat) * parseFloat(tarif);
                $("#subtotal_mask").val(parseFloat(subtotal).toLocaleString('id-ID', { maximumFractionDigits: 2})).trigger("change");
            }

            if (discount != "" && discount != 0) {
                subtotal = parseFloat(subtotal) - (parseFloat(subtotal * (parseFloat(discount)/100)));
                $("#subtotal_mask").val(parseFloat(subtotal).toLocaleString('id-ID', { maximumFractionDigits: 2})).trigger("change");
            }
        });

        $("#subtotal_mask").on('change', function() {
            $("#subtotal").val($("#subtotal_mask").autoNumeric("get"));
        });

        $("#qty_dusEdit_mask").on('change', function() {
            $("#qty_dusEdit").val($("#qty_dusEdit_mask").autoNumeric("get"));
        });

        $("#tarifEdit_mask").on('change', function() {
            $("#tarifEdit").val($("#tarifEdit_mask").autoNumeric("get"));

            var tarif = $("#tarifEdit_mask").autoNumeric("get");
            var berat = $("#beratEdit").val();
            var subtotal = 0;
            if (berat != "" && tarif != "") {
                subtotal = parseFloat(berat) * parseFloat(tarif);
                $("#subtotalEdit_mask").val(parseFloat(subtotal).toLocaleString('id-ID', { maximumFractionDigits: 2})).trigger("change");
            }
        });

        $("#beratEdit_mask").on('change', function() {
            $("#beratEdit").val($("#beratEdit_mask").autoNumeric("get"));

            var berat = $("#beratEdit_mask").autoNumeric("get");
            var tarif = $("#tarifEdit").val();
            var subtotal = 0;
            if (berat != "" && tarif != "") {
                subtotal = parseFloat(berat) * parseFloat(tarif);
                $("#subtotalEdit_mask").val(parseFloat(subtotal).toLocaleString('id-ID', { maximumFractionDigits: 2})).trigger("change");
            }
        });

        $("#discountEdit_mask").on('change', function() {
            $("#discountEdit").val($("#discountEdit_mask").autoNumeric("get"));

            var berat = $("#beratEdit").val();
            var tarif = $("#tarifEdit").val();
            var discount = $("#discountEdit_mask").autoNumeric("get");
            var subtotal = 0;
            if (berat != "" && tarif != "") {
                subtotal = parseFloat(berat) * parseFloat(tarif);
                $("#subtotalEdit_mask").val(parseFloat(subtotal).toLocaleString('id-ID', { maximumFractionDigits: 2})).trigger("change");
            }

            if (discount != "" && discount != 0) {
                subtotal = parseFloat(subtotal) - (parseFloat(subtotal * (parseFloat(discount)/100)));
                $("#subtotalEdit_mask").val(parseFloat(subtotal).toLocaleString('id-ID', { maximumFractionDigits: 2})).trigger("change");
            }
        });

        $("#subtotalEdit_mask").on('change', function() {
            $("#subtotalEdit").val($("#subtotalEdit_mask").autoNumeric("get"));
        });

        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        $("#cancel").on('click', function(e) {
            Swal.fire({
                title: "Batal?",
                text: "Apakah anda ingin membatalkan perubahan biaya ekspedisi?",
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
                        url: "/ExpeditionCost/RestoreDetail",
                        method: 'POST',
                        data: {
                            idCost: '{{$dataCost->id}}'
                        },
                        success: function(result){
                            window.location.href = "{{ url('/ExpeditionCost') }}";
                        }
                    });
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
	    });

        $("#form_add").submit(function(e){
            e.preventDefault();
            var dataCount = $('#list_item >table >tbody >tr').length;
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

                    if(parseInt(dataCount) < 1) {
                        Swal.fire(
                            "Gagal!",
                            "Harap Tambahkan Minimum 1 Surat Jalan Pengiriman!.",
                            "warning"
                        )
                        count = parseInt(count) + 1;
                        e.preventDefault();
                    }

                    if (count == 0) {
                        $("#form_add").off("submit").submit();
                    }
                    else {
                        $("html, body").animate({ scrollTop: 0 }, "slow");
                        e.preventDefault();
                    }
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
		});

        $("#branch").on("change", function() {
            //getListProduct
            getDelivery($(this).val());

            //Hapus Daftar penjualan
            var mode = $("#mode").val();
            if (mode == "edit") {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/ExpeditionCost/ResetDetail",
                    method: 'POST',
                    data: {
                        idCost: '{{$dataCost->id}}',
                    },
                    success: function(result){
                        var datatable = $('#list_item').KTDatatable();
                            datatable.setDataSourceParam('idCost', '');
                            datatable.reload();
                            footerDataForm('{{$dataCost->id}}');
                    }
                });
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/ExpeditionCost/GetExpeditionAddress",
                method: 'POST',
                data: {
                    idBranch: $(this).val(),
                },
                success: function(result){
                    if (result != null) {
                        $("#id_alamat").val(result.id);
                        $("#alamat").val(ucwords(result.alamat_cabang));
                    }
                }
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/ExpeditionCost/GetTarif",
                method: 'POST',
                data: {
                    idBranch: $(this).val(),
                },
                success: function(result){
                    $('#tarif_kota').find('option:not(:first)').remove();
                    if (result.length > 0) {
                        for (var i = 0; i < result.length;i++) {
                            if (result[i].nama_kota != null) {
                                $("#tarif_kota").append($('<option>', {
                                    value:result[i].id,
                                    text:result[i].nama_kota.toUpperCase()
                                }));
                            }
                        }
                    }
                }
            });
        });

        $("#delivery").on("change", function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/ExpeditionCost/GetDataDelivery",
                method: 'POST',
                data: {
                    idDelivery: $(this).val(),
                },
                success: function(result){
                    if (result.length > 0) {
                        var qtySJ = result[0].jumlah_total_sj;
                        $("#qty_dus_mask").val(parseInt(Math.ceil(result[0].koli))).trigger('change');
                        $("#kota_tujuan").val(ucwords(result[0].kota));
                        $("#qtySJMask").val(parseFloat(qtySJ).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        $("#qtySJ").val(qtySJ);
                        $("#tglSJ").val(formatDate(result[0].tanggal_sj));
                        if (result[0].tanggal_kirim != null) {
                            $("#tglKirimSJ").val(formatDate(result[0].tanggal_kirim));
                        }
                        else {
                            $("#tglKirimSJ").val("-");
                        }

                    }
                    else {
                        $("#qty_dus_mask").val(0).trigger('change');
                        $("#kota_tujuan").val(ucwords(""));
                        $("#qtySJMask").val("-");
                        $("#qtySJ").val("-");
                        $("#tglSJ").val("-");
                        $("#tglKirimSJ").val("-");
                    }
                }
            });
        });

        $("#tarif_kota").on("change", function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/ExpeditionCost/GetNominalTarif",
                method: 'POST',
                data: {
                    idTarif: $(this).val(),
                },
                success: function(result){
                    if (result != null) {
                        var tarif = result.tarif;
                        $("#tarif_per_kota_mask").val(parseFloat(tarif).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        $("#tarif_per_kota").val(tarif);
                    }
                    else {
                        $("#tarif_per_kota_mask").val("-");
                        $("#tarif_per_kota").val(0);
                    }
                }
            });
        });

        $(".btnHarga").on("click", function() {
            $("#tarif_mask").val(parseFloat($("#tarif_per_kota").val())).trigger('change');
        });

        function getDelivery(idExpedition) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/ExpeditionCost/GetDelivery",
                method: 'POST',
                data: {
                    idBranch: idExpedition,
                },
                success: function(result){
                    $('#delivery').find('option:not(:first)').remove();
                    if (result.length > 0) {
                        for (var i = 0; i < result.length;i++) {
                            $("#delivery").append($('<option>', {
                                value:result[i].id,
                                text:result[i].kode_pengiriman.toUpperCase()
                            }));
                        }
                    }
                }
            });
        }

        $(document).ready(function() {

            var datatable = $('#list_item').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/ExpeditionCost/GetDetail',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data: {
                                idCost: "{{$dataCost->id}}",
                                mode: "edit"
                            },
                        }
                    },
                    pageSize: 100,
                    serverPaging: true,
                    serverFiltering: false,
                    serverSorting: true,
                    saveState: false
                },

                layout: {
                    scroll: false,
                    height: 'auto',
                    footer: false
                },

                sortable: false,

                filterable: false,

                pagination: false,

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
                        field: 'kode_pengiriman',
                        title: 'Kode Pengiriman',
                        autoHide: false,
                        textAlign: 'center',
                        width: 100,
                        template: function(row) {
                            return row.kode_pengiriman.toUpperCase();
                        },
                    },
                    {
                        field: 'txtKode',
                        title: 'Kode Pengiriman',
                        autoHide: false,
                        textAlign: 'center',
                        visible:false,
                        width: 20,
                        template: function(row) {
                            return row.kode_pengiriman.toUpperCase() + "<span id='txt_"+row.id_sj+"'>"+row.kode_pengiriman+"</span>";
                        },
                    },
                    {
                        field: 'nama_customer',
                        title: 'Nama Customer',
                        autoHide: false,
                        textAlign: 75,
                        width: 'auto',
                        template: function(row) {
                            return row.nama_customer.toUpperCase();
                        },
                    },
                    {
                        field: 'tanggal_sj',
                        title: 'Tanggal Surat Jalan',
                        textAlign: 'center',
                        width: 100,
                        template: function(row) {
                            if (row.tanggal_sj != null) {
                                return formatDate(row.tanggal_sj);
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'value3',
                        title: 'Barang',
                        autoHide: false,
                        textAlign: 'center',
                        width: 75,
                        template: function(row) {
                            return row.value3.toUpperCase();
                        },
                    },
                    {
                        field: 'value4',
                        title: 'Tujuan',
                        autoHide: false,
                        textAlign: 'center',
                        width: 75,
                        template: function(row) {
                            return row.value4.toUpperCase();
                        },
                    },
                    {
                        field: 'value5',
                        title: 'Tarif(Rp)',
                        textAlign: 'center',
                        autoHide: false,
                        width: 45,
                        template: function(row) {
                            return parseFloat(row.value5).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'value6',
                        title: 'Jumlah Dus',
                        textAlign: 'center',
                        width: 75,
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.value6).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'value7',
                        title: 'Berat/Vol',
                        textAlign: 'center',
                        width: 50,
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.value7).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'value8',
                        title: 'Diskon (%)',
                        textAlign: 'center',
                        width: 55,
                        autoHide: false,
                        template: function(row) {
                            if (row.value8 == null) {
                                return "-"
                            }
                            else {
                                return parseFloat(row.value8).toLocaleString('id-ID', { maximumFractionDigits: 2});
                            }

                        },
                    },
                    {
                        field: 'value10',
                        title: 'Subtotal',
                        textAlign: 'center',
                        width: 70,
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.value10).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'Actions',
                        title: 'Aksi',
                        sortable: false,
                        width: 70,
                        overflow: 'visible',
                        autoHide: false,
                        textAlign: 'center',
                        template: function(row) {
                            var txtAction = "<a href='#' class='btn btn-sm btn-clean btn-icon edit' title='Ubah' onclick='editDetailItem("+row.id+");return false;'>";
                                txtAction += "<i class='la la-edit'></i>";
                                txtAction += "</a>";
                                txtAction += "<a href='#' class='btn btn-sm btn-clean btn-icon' title='Hapus' onclick='deleteDetailItem("+row.id+");return false;'>";
                                txtAction += "<i class='la la-trash'></i>";
                                txtAction += "</a>";

                            return txtAction;
                        },
                    }
                ],
            });
        });

        $("#btnAddItem").on('click', function(e) {
			var errCount = 0;

			$(".detailItem").each(function(){
				if($(this).val() == "" || $(this).children("option:selected").val() == ""){
					$(this).closest('.form-group, input-group').find('.errItem').show();
					errCount = errCount + 1;
				}
				else {
					$(this).closest('.form-group, input-group').find('.errItem').hide();
				}
			});

            $(".numeric").each(function(){
				if(parseFloat($(this).val()) < 1){
					$(this).closest('.form-group, input-group').find('.errItemNumeric').show();
					errCount = errCount + 1;
				}
				else {
					$(this).closest('.form-group, input-group').find('.errItemNumeric').hide();
				}
			});

			if (errCount == 0) {
                Swal.fire({
                    title: "Tambah Item?",
                    text: "Apakah data item sudah sesuai?",
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
                            url: "/ExpeditionCost/StoreDetail",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idCost : "{{$dataCost->id}}",
                                idDlv : $("#delivery option:selected").val(),
                                nama : $("#nama_resi").val(),
                                kota : $("#kota_tujuan").val(),
                                tarif : $("#tarif").val(),
                                qty : $("#qty_dus").val(),
                                discount : $("#discount").val(),
                                berat : $("#berat").val(),
                                subtotal : $("#subtotal").val(),
                                flagTagih : $('input[name="flag_tagih"]:checked').val(),
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Item Berhasil ditambahkan!.",
                                        "success"
                                    )
                                    $("#delivery").val("").trigger('change');
                                    $("#tarif_kota").val("").trigger('change');
                                    $("#nama_resi").val("");
                                    $("#kota_tujuan").val("");
                                    $("#tarif_mask").val("").trigger('change');
                                    $("#qty_dus_mask").val("").trigger('change');
                                    $("#berat_mask").val("").trigger('change');
                                    $("#subtotal_mask").val("").trigger('change');
                                    $("#flag_tagih").prop('checked', false);
                                    var datatable = $('#list_item').KTDatatable();
                                        datatable.setDataSourceParam('idCost','');
                                        datatable.reload();
                                    footerDataForm('{{$dataCost->id}}');
                                }
                                else if (result == "failDuplicate") {
                                    Swal.fire(
                                        "Gagal!",
                                        "Surat Jalan ini sudah tersedia pada List Biaya !",
                                        "warning"
                                    )
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

        $("#btnEditItem").on('click', function(e) {
			var errCount = 0;

			$(".detailItemEdit").each(function(){
				if($(this).val() == "" || $(this).children("option:selected").val() == ""){
					$(this).closest('.form-group, input-group').find('.errItemEdit').show();
					errCount = errCount + 1;
				}
				else {
					$(this).closest('.form-group, input-group').find('.errItemEdit').hide();
				}
			});

            $(".numericEdit").each(function(){
				if(parseFloat($(this).val()) < 1){
					$(this).closest('.form-group, input-group').find('.errItemEditNumeric').show();
					errCount = errCount + 1;
				}
				else {
					$(this).closest('.form-group, input-group').find('.errItemEditNumeric').hide();
				}
			});

			if (errCount == 0) {
                Swal.fire({
                    title: "Update Item?",
                    text: "Apakah data item sudah sesuai?",
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
                            url: "/ExpeditionCost/UpdateDetail",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idDetail : $("#idDetail").val(),
                                idCost : "{{$dataCost->id}}",
                                nama : $("#nama_resiEdit").val(),
                                kota : $("#kota_tujuanEdit").val(),
                                tarif : $("#tarifEdit").val(),
                                qty : $("#qty_dusEdit").val(),
                                discount : $("#discountEdit").val(),
                                berat : $("#beratEdit").val(),
                                subtotal : $("#subtotalEdit").val(),
                                flagTagih : $('input[name="flag_tagihEdit"]:checked').val(),
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Item Berhasil diupdate!.",
                                        "success"
                                    )
                                    $("#idDetail").val("");
                                    $("#nama_resiEdit").val("");
                                    $("#kota_tujuanEdit").val("");
                                    $("#tarifEdit_mask").val("").trigger('change');
                                    $("#qty_dusEdit_mask").val("").trigger('change');
                                    $("#beratEdit_mask").val("").trigger('change');
                                    $("#subtotalEdit_mask").val("").trigger('change');
                                    $("#flag_tagihEdit").prop('checked', false);
                                    var datatable = $('#list_item').KTDatatable();
                                        datatable.setDataSourceParam('idCost','');
                                        datatable.reload();
                                    footerDataForm('{{$dataCost->id}}');
                                }
                                else if (result == "failDuplicate") {
                                    Swal.fire(
                                        "Gagal!",
                                        "Surat Jalan ini sudah tersedia pada List Biaya !",
                                        "warning"
                                    )
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

        function editDetailItem(id) {
            $("#detil_edit_item").empty();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/ExpeditionCost/EditDetail",
                method: 'POST',
                data: {
                    idDetail: id,
                    mode:"edit"
                },
                success: function(result){
                    if (result.length > 0) {
                        for (var i = 0; i < result.length;i++) {
                            $("#idDetail").val(result[i].id);
                            $("#NoSJEdit").val(result[i].kode_pengiriman.toUpperCase());
                            $("#tglSJEdit").val(formatDate(result[i].tanggal_sj));
                            if (result[i].tanggal_kirim == null) {
                                $("#tglKirimSJEdit").val(" - ");
                            }
                            else {
                                $("#tglKirimSJEdit").val(formatDate(result[i].tanggal_kirim));
                            }

                            $("#qtySJEditMask").val(parseFloat(result[i].jumlah_total_sj).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                            $("#nama_resiEdit").val(result[i].value3);
                            $("#kota_tujuanEdit").val(result[i].value4);
                            $("#tarifEdit_mask").val(parseFloat(result[i].value5).toLocaleString('id-ID', { maximumFractionDigits: 2})).trigger('change');
                            $("#qty_dusEdit_mask").val(parseFloat(result[i].value6).toLocaleString('id-ID', { maximumFractionDigits: 2})).trigger('change');
                            $("#beratEdit_mask").val(parseFloat(result[i].value7).toLocaleString('id-ID', { maximumFractionDigits: 2})).trigger('change');
                            if (result[i].value8 == null) {
                                $("#discountEdit_mask").val("").trigger('change');
                            }
                            else {
                                $("#discountEdit_mask").val(parseFloat(result[i].discount).toLocaleString('id-ID', { maximumFractionDigits: 2})).trigger('change');
                            }
                            $("#subtotalEdit_mask").val(parseFloat(result[i].value10).toLocaleString('id-ID', { maximumFractionDigits: 2})).trigger('change');
                            if (result[i].value9 == "Y") {
                                $("#flag_tagihEdit").prop('checked', true);
                            }
                            else {
                                $("#flag_tagihEdit").prop('checked', false);
                            }
                            $("#btnModalEditItem").trigger('click');
                        }
                    }
                }
            });
        }

        function deleteDetailItem(id) {
            Swal.fire({
                title: "Hapus?",
                text: "Apakah anda ingin menghapus data ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "/ExpeditionCost/DeleteDetail",
                        method: 'POST',
                        data: {
                            idDetail: id,
                            mode: "edit"
                        },
                        success: function(result){
                            Swal.fire(
                                "Sukses!",
                                "Data Berhasil dihapus!.",
                                "success"
                            )
                        }
                    });
                    var datatable = $('#list_item').KTDatatable();
                        datatable.setDataSourceParam('idCost','{{$dataCost->id}}');
                        datatable.setDataSourceParam('mode', 'edit');
                        datatable.reload();
                        footerDataForm('{{$dataCost->id}}');
                }
                else if (result.dismiss === "cancel") {
                    // Swal.fire(
                    //     "Cancelled",
                    //     "Your imaginary file is safe :)",
                    //     "error"
                    // )
                    e.preventDefault();
                }
            });
        }

        function footerDataForm(idCost) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/ExpeditionCost/GetDataFooter",
                method: 'POST',
                data: {
                    idCost: idCost,
                    mode:"edit"
                },
                success: function(result){
                    if (result != "null") {
                        var ttlQty = result.qtyCost;
                        var ttlBerat = result.beratCost;
                        var subtotal = result.subtotalCost;

                        $("#ttlQty").val(ttlQty);
                        $("#ttlQtyMask").val(parseFloat(ttlQty).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                        $("#ttlBerat").val(ttlBerat);
                        $("#ttlBeratMask").val(parseFloat(ttlBerat).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                        $("#ttlBiaya").val(subtotal);
                        $("#ttlBiayaMask").val(parseFloat(subtotal).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                    }
                    else {
                        $("#ttlQty").val(0);
                        $("#ttlQtyMask").val(parseFloat(0).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                        $("#ttlBerat").val(0);
                        $("#ttlBeratMask").val(parseFloat(0).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                        $("#ttlBiaya").val(0);
                        $("#ttlBiayaMask").val(parseFloat(0).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                    }
                }
            });
        }
	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
