@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary text-white header-elements-sm-inline">
						<h5 class="card-title text-white">Ubah Perintah Pembelian</h5>
					</div>
                    <form action="{{ route('PurchaseOrder.update', $dataPurchaseOrder->id) }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            @method('PUT')
                            <div class="row">
								<div class="col-md-6">
									<fieldset>
										<legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi Penjual/ Vendor </h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
                                        <div class="form-group">
                                            <label>Kode Pembelian :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="hidden" value="load" id="mode" />
                                                    <input type="text" class="form-control bg-slate-600 border-slate-600 border-1" placeholder="Auto Generated" name="kode_item" id="kode_item" value="{{strtoupper($dataPurchaseOrder->no_po)}}" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Vendor :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <select class="form-control select2 req" id="supplier" name="supplier">
                                                        <option label="Label"></option>
                                                        @foreach($dataSupplier as $supplier)
                                                        <option value="{{$supplier->id}}">{{strtoupper($supplier->nama_supplier)}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih vendor terlebih dahulu!</span>
                                            </div>
                                        </div>

										<div class="form-group">
                                            <label>Alamat Kirim :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="hidden" class=" form-control req" name="id_alamat" id="id_alamat">
                                                    <textarea class="form-control" name="alamat" id="alamat" style="resize:none;" placeholder="Silahkan Klik Tombol Pilih Alamat" readonly></textarea>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-primary" id="btnAlamat" data-toggle="modal" data-target="#modal_list_alamat">Pilih Alamat</button>
                                                    </div>
                                                </div>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih alamat kirim terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Tanggal Pembelian :</label>
                                            <div class="form-group divTgl form-group-feedback form-group-feedback-right">
                                                <input type="hidden" class="form-control tglValue req" name="tanggal_po" id="tanggal_po">
                                                <input type="text" class="form-control pickerTgl" placeholder="Pilih Tanggal" name="tanggal_po_picker" id="tanggal_po_picker" readonly>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal pembelian terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Tanggal Permintaan Penerimaan Barang :</label>
                                            <div class="form-group divTgl form-group-feedback form-group-feedback-right">
                                                <input type="hidden" class="form-control tglValue req" name="tanggal_req" id="tanggal_req">
                                                <input type="text" class="form-control pickerTgl" placeholder="Pilih Tanggal" name="tanggal_req_picker" id="tanggal_req_picker" readonly>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal permintaan terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Batas Waktu Penerimaan Barang :</label>
                                            <div class="form-group divTgl form-group-feedback form-group-feedback-right">
                                                <input type="hidden" class="form-control tglValue req" name="tanggal_deadline" id="tanggal_deadline">
                                                <input type="text" class="form-control pickerTgl" placeholder="Pilih Tanggal" name="tanggal_deadline_picker" id="tanggal_deadline_picker" readonly>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal batas waktu terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-lg-6 mb-5">
                                                <label>Metode Pembayaran :</label>
                                                <div>
                                                    <div class="radio-inline">
                                                        <label class="radio">
                                                            <input type="radio" id="cash" value="cash" name="metode_bayar" {{ $dataPurchaseOrder->metode_pembayaran === "cash" ? "checked" : "" }} />
                                                            <span></span>Cash/Tunai
                                                        </label>
                                                        <label class="radio">
                                                            <input type="radio" id="credit" value="credit" name="metode_bayar" {{ $dataPurchaseOrder->metode_pembayaran === "credit" ? "checked" : "" }} />
                                                            <span></span>Kredit
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6" id="durasiJT" @if ($dataPurchaseOrder->metode_pembayaran == "cash") style="display:none;" @endif>
    											<label>Durasi Jatuh Tempo (Hari) :</label>
    											<div>
    												<input type="text" class="form-control" maxlength="4" onkeypress="return validasiAngka(event);" name="durasi_jt" id="durasi_jt" value="{{$dataPurchaseOrder->durasi_jt}}">
                                                    <span class="form-text text-danger" id="errDurasi" style="display:none;">*Durasi JT Tidak dapat dibawah 1 Hari!</span>
                                                </div>
    										</div>
                                        </div>

                                        <div class="form-group">
                                            <label>Pajak Penjualan :</label>
                                            <div class="no-gutters">
                                                <div class="radio-inline">
                                                    <label class="radio">
                                                    <input type="radio" id="statPpn_y" name="status_ppn" value="Y" {{ $dataPurchaseOrder->flag_ppn === "Y" ? "checked" : "" }} />
                                                    <span></span>PPn Excl.</label>
                                                    <label class="radio">
                                                    <input type="radio" id="statPpn_i" name="status_ppn" value="I" {{ $dataPurchaseOrder->flag_ppn === "I" ? "checked" : "" }} />
                                                    <span></span>PPn Incl.</label>
                                                    <label class="radio">
                                                    <input type="radio" id="statPpn_n" name="status_ppn" value="N" {{ $dataPurchaseOrder->flag_ppn === "N" ? "checked" : "" }} />
                                                    <span></span>Non PPn</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-lg-6 mb-5">
                                                <label>Diskon :</label>
                                                <div>
                                                    <div class="radio-inline">
                                                        <label class="radio">
                                                            <input type="radio" id="percentage" value="P" name="jenis_diskon" {{ $dataPurchaseOrder->jenis_diskon === "P" ? "checked" : "" }} />
                                                            <span></span>Persentase
                                                        </label>
                                                        <label class="radio">
                                                            <input type="radio" id="nominal" value="N" name="jenis_diskon" {{ $dataPurchaseOrder->jenis_diskon === "N" ? "checked" : "" }} />
                                                            <span></span>Nominal
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-lg-6" id="discPercent" @if ($dataPurchaseOrder->jenis_diskon == "N") style="display:none;" @endif>
    											<label id="txtDiskonP">Persentase (%):</label>
    											<div>
    												<input type="text" class="form-control discount" onkeypress="return validasiAngka(event);" name="disc_percent" id="disc_percent" value="{{$dataPurchaseOrder->persentase_diskon}}">
                                                </div>
    										</div>
                                            <div class="col-lg-6" id="discNominal" @if ($dataPurchaseOrder->jenis_diskon == "P") style="display:none;" @endif>
    											<label id="txtDiskonN">Nominal (Rp) :</label>
    											<div>
    												<input type="text" class="form-control discount" onkeypress="return validasiAngka(event);" name="disc_nominal" id="disc_nominal" value="{{$dataPurchaseOrder->nominal_diskon}}">
                                                </div>
    										</div>
    									</div>

                                        <div class="form-group">
                                            <label>Syarat & Ketentuan :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <textarea class="form-control elastic" id="tnc" name="tnc" rows="3" placeholder="Ketik Syarat & Ketentuan Pembelian Disini atau gunakan Template pada tombol Template">@foreach($dataTerms as $terms){{$terms->terms_and_cond}}@endforeach</textarea>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-primary" id="btnTemplate" data-toggle="modal" data-target="#modal_list_terms">Template</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

									</fieldset>
								</div>

								<div class="col-md-6">
									<fieldset>
					                	<legend class="text-muted"><h6><i class="fab la-buffer"></i> Rincian Pembelian Barang</h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
										<div class="form-group">
                                            <label>Nama barang :</label>
                                            <div class="input-group">
                                                    <select class="form-control select2 detailItem" id="product" name="product">
                                                        <option label="Label"></option>
                                                    </select>

                                                    <div class="input-group-append" data-toggle="tooltip"  title="Tambah koneksi barang" data-placement="top">
                                                        <label class="input-group-text btn btn-primary btn-icon" for="product" id="btnProduct" data-toggle="modal" data-target="#modal_list_product">
                                                            <i class="flaticon2-plus"></i>
                                                        </label>
                                                    </div>
                                                <span class="form-text text-danger errItem" style="display:none;">*Harap pilih item terlebih dahulu!</span>
                                            </div>
                                            <span class="text-success" id="txtStok"></span> <span id="btnProductHistory"></span>
                                        </div>

										<div class="form-group">
											<label>Jumlah Pembelian Barang :</label>
											<div class="input-group">
        										<div class="col-8 pl-0">
												<input type="text" id="qtyOrderMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right">
                                                    <input type="hidden" id="qtyOrder" class="form-control text-right detailItem numericVal">
        											<span class="form-text text-danger errItem" style="display:none;">*Harap masukkan Jumlah order item terlebih dahulu!</span>
                                                    <span class="form-text text-danger errItemNumeric" style="display:none;">*Jumlah Barang tidak dapat dibawah atau 0!</span>
                                                </div>
        										<div class="col-4 pr-0">
        											<select class="form-control select2 detailUnit" id="productUnit" name="productUnit">
                                                        <option label="Label"></option>
                                                    </select>
                                                    <span class="form-text text-danger errUnit" style="display:none;">*Harap pilih satuan item terlebih dahulu!</span>
        										</div>
											</div>
										</div>

                                        <div class="form-group row mb-0">
                                            <div class="form-group hargaBeliItem col-xl-6 col-sm-6 col-xs-12">
                                                <label>Harga Beli Standard :</label>
                                                <div class="input-group input-group-solid">
                                                    <input type="number" id="harga_beli_item" class="form-control text-right hrg" readonly>
                                                    <span class="input-group-append">
                                                        <button type="button" class="btn btn-primary btn-icon btnHarga" data-popup="tooltip" title="Gunakan">
                                                            <i class="la la-check"></i>
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="form-group hargaBeliItem col-xl-6 col-sm-6 col-xs-12">
                                                <label>Harga Beli Terakhir :</label>
                                                <div class="input-group input-group-solid">
                                                    <input type="number" id="harga_beli_last" class="form-control text-right hrg" readonly>
                                                    <span class="input-group-append">
                                                        <button type="button" class="btn btn-primary btn-icon btnHarga" data-popup="tooltip" title="Gunakan">
                                                            <i class="la la-check"></i>
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
										</div>

										<div class="form-group">
											<label class="font-weight-semibold">Harga Beli :</label>
											<input type="text" id="harga_beli_mask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right">
											<input type="hidden" id="harga_beli" min="0" class="form-control text-right detailItem priceVal" autocomplete="off">
                                            <span class="form-text text-danger errItemPrice" style="display:none;">*Harga Barang tidak dapat dibawah 0!</span>
                                            <span class="form-text text-danger errItem" style="display:none;">*Harap masukkan harga beli item terlebih dahulu!</span>
										</div>

                                        <div class="form-group row">
											<label class="col-lg-3 col-form-label"></label>
											<div class="col-lg-9">
												<button type="button" class="btn btn-primary font-weight-bold" id="btnAddItem">Tambah List Pembelian</button>
											</div>
										</div>

									</fieldset>
								</div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset>
                                        <legend class="text-muted"><h6><i class="la la-clipboard-list"></i> List Pembelian Barang </h6></legend>
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
										<label class="col-lg-3 col-form-label">Total Order Qty</label>
										<div class="col-lg-9">
											<input type="text"  value="0" id="qtyTtlMask" class="form-control text-center" readonly>
											<input type="hidden" id="qtyTtl" name="qtyTtl" class="form-control text-right" readonly>
										</div>
									</div>
								</div>
							</div>

                            <br>
							<br>
							<div class="row">
								<div class="col-md-6">

								</div>

								<div class="col-md-6">

									<div class="form-group row">
										<label class="col-lg-3 col-form-label">Total Dpp</label>
										<div class="col-lg-9">
											<input type="text" value="0" id="dppMask" class="form-control text-right" readonly>
											<input type="hidden" id="dpp" name="dpp" class="form-control text-right" readonly>
										</div>
									</div>

									<div class="form-group row">
										<label class="col-lg-3 col-form-label">Diskon</label>
										<div class="col-lg-9">
											<input type="text" value="0" id="discNominalMask" class="form-control text-right" readonly>
											<input type="hidden" id="discNominal" name="discNominal" class="form-control text-right" readonly>
										</div>
									</div>

									<div class="form-group row">
										<label class="col-lg-3 col-form-label">Total PPn</label>
										<div class="col-lg-9">
											<input type="text" value="0" id="ppnMask" class="form-control text-right" readonly>
											<input type="hidden" id="ppn" name="ppn" class="form-control text-right" readonly>
										</div>
									</div>

									<div class="form-group row">
										<label class="col-lg-3 col-form-label">Grand Total</label>
										<div class="col-lg-9">
											<input type="text" value="0" id="gtMask" class="form-control text-right" readonly>
											<input type="hidden" id="gt" name="gt" class="form-control text-right" readonly>
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
                                <button type="button" style="display: none;" id="btnModalEditItem" data-toggle="modal" data-backdrop="static" data-target="#modal_form_edit_item"></button>
                                <button type="submit" class="btn btn-light-primary font-weight-bold mr-2"> Simpan <i class="flaticon-paper-plane-1"></i></button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal form list alamat -->
				<div id="modal_list_alamat" class="modal fade">
				    <div class="modal-dialog modal-lg">
					    <div class="modal-content">
						    <div class="modal-header bg-primary">

							    <h5 class="modal-title">List Alamat Vendor</h5>
						    </div>
						    <div class="modal-body">
							    <form >
								    <table class="datatable-bordered datatable-head-custom ml-4" id="list_alamat" width="100%">
									    <thead>
										    <tr>
											    <th align="center" style="text-align:center;display:none;">ID</th>
												<th align="center" style="text-align:center;">Alamat</th>
												{{-- <th align="center" style="text-align:center;">Jenis Alamat</th>
												<th align="center" style="text-align:center;">PIC</th>
												<th align="center" style="text-align:center;">No. Telp PIC</th> --}}
												<th align="center" style="text-align:center;">Aksi</th>
										    </tr>
									    </thead>
									    <tbody>

									    </tbody>
								    </table>
							    </form>

						    </div>

						    <div class="modal-footer">
							    <button type="button" class="btn btn-link" data-dismiss="modal">Tutup</button>
						    </div>
					    </div>
				    </div>
			    </div>
                <!-- /form list alamat -->

                <!-- Modal form list terms -->
				<div id="modal_list_terms" class="modal fade">
				    <div class="modal-dialog modal-lg">
					    <div class="modal-content">
						    <div class="modal-header bg-primary">

							    <h5 class="modal-title">List Template Terms</h5>
						    </div>
						    <div class="modal-body">
							    <form >
								    <table class="datatable-bordered datatable-head-custom ml-4" id="list_terms" width="100%">
									    <thead>
										    <tr>
											    <th align="center" style="text-align:center;display:none;">ID</th>
												<th align="center" style="text-align:center;">Nama Terms</th>
												<th align="center" style="text-align:center;">Aksi</th>
										    </tr>
									    </thead>
									    <tbody>

									    </tbody>
								    </table>
							    </form>

						    </div>

						    <div class="modal-footer">
							    <button type="button" class="btn btn-link" data-dismiss="modal">Tutup</button>
						    </div>
					    </div>
				    </div>
			    </div>
                <!-- /form list terms -->

                <!-- Modal form list barang -->
				<div id="modal_list_product" class="modal fade">
				    <div class="modal-dialog modal-lg">
					    <div class="modal-content">
						    <div class="modal-header bg-primary">

							    <h5 class="modal-title">List Barang</h5>
						    </div>
						    <div class="modal-body">
                                <div class="mb-7">
                                    <div class="row align-items-center">
                                        <div class="col-lg-9 col-xl-8">
                                            <div class="row align-items-center">
                                                <div class="col-md-6 my-2 my-md-0">
                                                    <div class="input-icon">
                                                        <input type="text" class="form-control" placeholder="Search..." id="list_product_search_query"/>
                                                        <span>
                                                            <i class="flaticon2-search-1 text-muted"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Search Form-->
                                <!--end: Search Form-->
                                <!--begin: Datatable-->

                                <div class="datatable datatable-bordered datatable-head-custom" id="list_product"></div>

						    </div>

						    <div class="modal-footer">
							    <button type="button" class="btn btn-link" data-dismiss="modal">Tutup</button>
						    </div>
					    </div>
				    </div>
			    </div>
                <!-- /form list barang -->

                <!-- Horizontal form edit item-->
				<div id="modal_form_edit_item" class="modal fade">
				    <div class="modal-dialog modal-lg">
					    <div class="modal-content">
						    <div class="modal-header bg-primary">
							    <h5 class="modal-title text-white">Ubah Harga Item</h5>
						    </div>
						    <div class="modal-body">
							    <form >
							        <div class="row">
                                        <div class="col-md-12">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label>Nama Barang :</label>
                                                    <div class="form-group form-group-feedback form-group-feedback-right">
                                                        <div class="input-group">
                                                            <input type='hidden' class='form-control' id='idRowEdit' />
                                                            <input type='hidden' class='form-control' id='idItemEdit' />
                                                            <input type='hidden' class='form-control' id='idSatuanEdit' />
                                                            <input type="text" class="form-control" id="namaBarangEdit" readonly>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label>Jumlah Pembelian Barang :</label>
                                                    <div class="input-group">
                                                        <div class="col-8 pl-0">
                                                            <input type="text" id="qtyRowEditMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right">
                                                            <input type="hidden" id="qtyRowEdit" class="form-control text-right inputEdit numericValEdit">
                                                            <span class="form-text text-danger errItemEdit" style="display:none;">*Harap masukkan Jumlah order item terlebih dahulu!</span>
                                                            <span class="form-text text-danger errItemNumericEdit" style="display:none;">*Jumlah Barang tidak dapat dibawah atau 0!</span>
                                                        </div>
                                                        <div class="col-4 pr-0">
                                                            <select class="form-control select2" id="productUnitEdit" name="productUnitEdit" style="width: 100%">
                                                                <option label="Label"></option>
                                                            </select>
                                                            <span class="form-text text-danger errUnit" style="display:none;">*Harap pilih satuan item terlebih dahulu!</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="font-weight-semibold">Harga Beli :</label>
                                                    <input type="text" id="hargaBaruMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right">
                                                    <input type="hidden" id="hargaBaru" min="0" class="form-control text-right inputEdit priceValEdit" autocomplete="off">
                                                    <span class="form-text text-danger errItemPriceEdit" style="display:none;">*Harga Barang tidak dapat dibawah 0!</span>
                                                    <span class="form-text text-danger errItemEdit" style="display:none;">*Harap masukkan harga beli item terlebih dahulu!</span>
                                                </div>

                                            </fieldset>
                                        </div>
                                    </div>
							    </form>
						    </div>

						    <div class="modal-footer">
							    <button type="button" class="btn btn-primary" id="btnEditItem" data-dismiss="modal">Simpan</button>
							    <button type="button"class="btn btn-light me-3" data-dismiss="modal">batal</button>
						    </div>
					    </div>
				    </div>
			    </div>
				<!-- /horizontal form edit item -->

                <!-- Modal form history barang -->
				<div id="modal_history_product" class="modal fade">
				    <div class="modal-dialog modal-xl">
					    <div class="modal-content">
						    <div class="modal-header bg-primary">

							    <h5 class="modal-title text-white">Riwayat Barang</h5>
						    </div>
						    <div class="modal-body">
                                <div class="mb-7">
                                    <div class="row align-items-center">
                                        <div class="col-lg-6">
                                            <div class="row align-items-center">
                                                <div class="col-md-4 my-2 my-md-0">
                                                    <div class="input-icon">
                                                        <input type="text" class="form-control" placeholder="Search..." id="list_riwayat_search_query"/>
                                                        <span>
                                                            <i class="flaticon2-search-1 text-muted"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Search Form-->
                                <!--end: Search Form-->
                                <!--begin: Datatable-->

                                <div class="datatable datatable-bordered datatable-head-custom" id="list_riwayat"></div>

						    </div>

						    <div class="modal-footer">
							    <button type="button" class="btn btn-link" data-dismiss="modal">Tutup</button>
						    </div>
					    </div>
				    </div>
			    </div>
                <!-- /form history barang -->

			</div>
			<!-- /content area -->
@endsection
@section('scripts')
    <script type="text/javascript">

        $(document).ready(function () {
            $('#supplier').select2({
                allowClear: true,
                placeholder: "Pilih Vendor"
            });

            $('#product').select2({
                allowClear: true,
                placeholder: "Pilih Barang"
            });

            $('#productUnit').select2({
                allowClear: true,
                placeholder: "Pilih satuan..."
            });

            $('#tanggal_po_picker, #tanggal_req_picker, #tanggal_deadline_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                autoclose : true,
                format : "dd MM yyyy",
            });

            $("#qtyOrderMask").autoNumeric('init');
            $("#harga_beli_mask").autoNumeric('init');

            $("#qtyRowEditMask").autoNumeric('init');
            $("#qtyRowEditMask").on('change', function() {
                $("#qtyRowEdit").val($("#qtyRowEditMask").autoNumeric("get"));
            });

            $("#hargaBaruMask").autoNumeric('init');
            $("#hargaBaruMask").on('change', function() {
                $("#hargaBaru").val($("#hargaBaruMask").autoNumeric("get"));
            });

            $('#productUnitEdit').select2({
                allowClear: true,
                placeholder: "Pilih satuan...",
                dropdownParent: $('#modal_form_edit_item')
            });
        });

        $("#qtyOrderMask").on('change', function() {
            $("#qtyOrder").val($("#qtyOrderMask").autoNumeric("get"));
        });

        $("#harga_beli_mask").on('change', function() {
            $("#harga_beli").val($("#harga_beli_mask").autoNumeric("get"));
        });

        $(".pickerTgl").on('change', function() {
            var reqDate = new Date($("#tanggal_req_picker").data('datepicker').getFormattedDate('yyyy-mm-dd'));
            var poDate = new Date($("#tanggal_po_picker").data('datepicker').getFormattedDate('yyyy-mm-dd'));
            var deadLineDate = new Date($("#tanggal_deadline_picker").data('datepicker').getFormattedDate('yyyy-mm-dd'));

            var selisih1 = Math.floor((Date.UTC(reqDate.getFullYear(), reqDate.getMonth(), reqDate.getDate()) - Date.UTC(poDate.getFullYear(), poDate.getMonth(), poDate.getDate()) ) /(1000 * 60 * 60 * 24));
            var selisih2 = Math.floor((Date.UTC(deadLineDate.getFullYear(), deadLineDate.getMonth(), deadLineDate.getDate()) - Date.UTC(reqDate.getFullYear(), reqDate.getMonth(), reqDate.getDate()) ) /(1000 * 60 * 60 * 24));
            $(this).closest(".divTgl").find(".tglValue").val($(this).data('datepicker').getFormattedDate('yyyy-mm-dd'));

            if ($("#tanggal_req").val() != "") {
                if (reqDate < poDate) {
                    Swal.fire(
                        "Error!",
                        "Tanggal Request tidak boleh dibawah dari tanggal PO!.",
                        "warning"
                    )
                    $("#tanggal_req").val("");
                    $("#tanggal_req_picker").val("");
                    $("#tanggal_deadline_picker").val("");
                    $("#tanggal_deadline").val("");
                }
            }

            if ($("#tanggal_deadline").val() != "") {
                if (deadLineDate < reqDate) {
                    Swal.fire(
                        "Error!",
                        "Tanggal Deadline tidak boleh dibawah dari tanggal Request dan PO!.",
                        "warning"
                    )
                    $("#tanggal_deadline_picker").val("");
                    $("#tanggal_deadline").val("");
                }
            }
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
                text: "Apakah anda ingin membatalkan perubahan pembelian barang?",
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
                        url: "/PurchaseOrder/RestoreDetail",
                        method: 'POST',
                        data: {
                           idPo: '{{$dataPurchaseOrder->id}}'
                        },
                        success: function(result){
                            window.location.href = "{{ url('/PurchaseOrder') }}";
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

                    if ($('input[name=metode_bayar]:checked').val() == "credit") {
                        if ($("#durasi_jt").val() < 1) {
                            $("#errDurasi").show();
                            count = parseInt(count) + 1;
                        }
                        else {
                            $("#errDurasi").hide();
                        }
                    }

                    if(parseInt(dataCount) < 1) {
                        Swal.fire(
                            "Gagal!",
                            "Harap Tambahkan Minimum 1 Item Pembelian!.",
                            "warning"
                        )
                        count = parseInt(count) + 1;
                    }

                    if (count == 0) {
                        $("#supplier").attr('disabled', false);
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

        $("#supplier").on("change", function() {
            //getListProduct
            getSupplierProduct($(this).val());

            //getDefaultAddress
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/PurchaseOrder/GetDefaultAddress",
                method: 'POST',
                data: {
                    id_alamat: "{{$dataPurchaseOrder->id_alamat}}",
                },
                success: function(result){
                    if (result.length > 0) {
                        $("#id_alamat").val(result[0].id);
                        $("#alamat").val(ucwords(result[0].alamat_pt));
                    }
                }
            });

            //getSupplierAddress
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/PurchaseOrder/GetSupplierAddress",
                method: 'POST',
                data: {
                    id_supplier: $(this).val(),
                },
                success: function(result){
                    if (result.length > 0) {
                        $('#list_alamat tbody').empty();
                        if (result.length > 0) {
                            for (var i = 0; i < result.length;i++) {
                                var idAlamat = result[i].id;
                                var alamat = result[i].alamat_pt;
                                //var jenisAlamat = result[i].jenis_alamat;
                                //var pic = result[i].pic_alamat;
                                //var tlpPic = result[i].telp_pic;
                                var data="<tr>";
                                    data +="<td style='text-align:center;display:none;'>"+idAlamat+"</td>";
                                    data +="<td style='text-align:left;word-wrap:break-word;min-width:160px;max-width:160px;'>"+ucwords(alamat)+"</td>";
                                    // data +="<td style='text-align:center;'>"+jenisAlamat+"</td>";
                                    // data +="<td style='text-align:center;'>"+ucwords(pic)+"</td>";
                                    // data +="<td style='text-align:center;'>"+tlpPic+"</td>";
                                    data +="<td style='text-align:center;'><button type='button' data-dismiss='modal' class='btn btn-primary btn-icon select'>Pilih</button></td>";
                                    data +="</tr>";
                                    $("#list_alamat").append(data);
                            }
                        }
                    }
                }
            });

            if ($("#mode").val() == "edit") {
                //Hapus Daftar penjualan
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/PurchaseOrder/ResetDetail",
                    method: 'POST',
                    data: {
                        idPO: '{{$dataPurchaseOrder->id}}',
                    },
                    success: function(result){
                        var datatable = $('#list_item').KTDatatable();
                            datatable.setDataSourceParam('idPurchaseOrder', '{{$dataPurchaseOrder->id}}');
                            datatable.setDataSourceParam('mode', 'edit');
                            datatable.reload();
                            footerDataForm('{{$dataPurchaseOrder->id}}');
                    }
                });

                //getPrevious Tax & Payment Method
                getSupplierTaxPayment($(this).val());
            }
        });

        function getSupplierTaxPayment(idSupplier) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/PurchaseOrder/GetSupplierPreviousOrder",
                method: 'POST',
                data: {
                    id_supplier: idSupplier,
                },
                success: function(result){
                    if (result != null) {
                        var jenisPPn = result.flag_ppn;
                        var metodePembayaran = result.metode_pembayaran;
                        var durasiJT = result.durasi_jt;

                        if (jenisPPn == "N") {
                            $("#statPpn_n").trigger('click');
                        }
                        else if (jenisPPn == "Y") {
                            $("#statPpn_y").trigger('click');
                        }
                        else if (jenisPPn == "I") {
                            $("#statPpn_i").trigger('click');
                        }

                        if (metodePembayaran == "cash") {
                            $("#cash").trigger('click');
                        }
                        else if (metodePembayaran == "credit") {
                            $("#credit").trigger('click');
                            $("#durasi_jt").val(durasiJT);
                        }
                    }
                }
            });
        }

        function getSupplierTaxPayment(idSupplier) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/PurchaseOrder/GetSupplierPreviousOrder",
                method: 'POST',
                data: {
                    id_supplier: idSupplier,
                },
                success: function(result){
                    if (result != null) {
                        var jenisPPn = result.flag_ppn;
                        var metodePembayaran = result.metode_pembayaran;
                        var durasiJT = result.durasi_jt;

                        if (jenisPPn == "N") {
                            $("#statPpn_n").trigger('click');
                        }
                        else if (jenisPPn == "Y") {
                            $("#statPpn_y").trigger('click');
                        }
                        else if (jenisPPn == "I") {
                            $("#statPpn_i").trigger('click');
                        }

                        if (metodePembayaran == "cash") {
                            $("#cash").trigger('click');
                        }
                        else if (metodePembayaran == "credit") {
                            $("#credit").trigger('click');
                            $("#durasi_jt").val(durasiJT);
                        }
                    }
                }
            });
        }

        $("#product").on("change", function() {
            //getdataItem
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/PurchaseOrder/GetSatuan",
                method: 'POST',
                data: {
                    idProduct: $(this).val(),
                },
                success: function(result){
                    $('#productUnit').find('option:not(:first)').remove();
                    if (result.length > 0) {
                        for (var i = 0; i < result.length;i++) {
                            $("#productUnit").append($('<option>', {
                                value:result[i].id,
                                text:result[i].kode_satuan.toUpperCase() + ' - ' + result[i].nama_satuan.toUpperCase()
                            }));
                        }
                    }
                }
            });
        });

        $("#productUnit").on("change", function() {
            //getdataItem
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/PurchaseOrder/GetDataItem",
                method: 'POST',
                data: {
                    id_product: $("#product option:selected").val(),
                    id_satuan: $(this).val(),
                    id_supplier: $("#supplier option:selected").val()
                },
                success: function(result){
                    if (result.length > 0) {
                        $("#harga_beli_last").val(result[0].harga_beli_last);
                        $("#harga_beli_item").val(result[0].harga_beli);
                        var stokItem = result[0].stok_item;
	                  	var satuan = result[0].nama_satuan;
                          $("#txtStok").html("Stok barang saat ini : "+parseFloat(stokItem).toLocaleString('id-ID', { maximumFractionDigits: 0})+" "+satuan);
                        $("#btnProductHistory").html('<a href="#" class="font-size-sm font-weight-bold text-danger text-right text-hover-muted" id="btnProductHistory" data-toggle="modal" data-target="#modal_history_product">[Lihat Riwayat Barang]</a>');
                    }
                    else {
                        $("#txtStok").html("");
                        $("#btnProductHistory").html("");
                    }
                }
            });

        });

        function getSupplierProduct(idSupplier) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/PurchaseOrder/GetProductBySupplier",
                method: 'POST',
                data: {
                    id_supplier: idSupplier,
                },
                success: function(result){
                    $('#product').find('option:not(:first)').remove();
                    if (result.length > 0) {
                        for (var i = 0; i < result.length;i++) {
                            var kodeItem = "";
                            if (result[i].value_spesifikasi != null) {
                                kodeItem = '('+result[i].value_spesifikasi+')'+result[i].kode_item.toUpperCase();
                            }
                            else {
                                kodeItem = result[i].kode_item.toUpperCase();
                            }
                            $("#product").append($('<option>', {
                                value:result[i].id,
                                text:kodeItem+' - '+result[i].nama_item
                            }));
                        }
                    }
                }
            });
        }

        $("#list_alamat").on('click', '.select', function() {
			var id = $(this).parents('tr:first').find('td:first').text();
			var alamat = $(this).parents('tr:first').find('td:eq(1)').text();
			$("#id_alamat").val(id);
			$("#alamat").val(ucwords(alamat));
        });

        $("#list_product").on('click', 'table .addToList', function() {
            var idItem = $(this).val();
            var datatable = $('#list_product').KTDatatable();
            var namaItem = datatable.getRecord(idItem).getColumn('nama_item').getValue();

            console.log(namaItem);
            var kd = $(this).parents('tr:first').find('td:first').text();
            var idSupp = $("#supplier option:selected").val();
	        var nmSupp = $("#supplier option:selected").html();
            Swal.fire({
                title: "Tambahkan Data?",
                text: "Apakah anda ingin menambah barang ini pada supplier" + namaItem +" ?",
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
                        url: "/PurchaseOrder/AddSupllierProduct",
                        method: 'POST',
                        data: {
                            id_item: idItem,
                            id_supplier: idSupp
                        },
                        success: function(result){
                            Swal.fire(
                                "Berhasil!",
                                "Barang Berhasil ditambahkan ke supplier " + nmSupp + "!",
                                "success"
                            )
                            getSupplierProduct(idSupp);
                            var datatable = $('#list_product').KTDatatable();
                                datatable.setDataSourceParam('id_supplier', idSupp);
                                datatable.reload();
                        }
                    });
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
        });

        $(document).ready(function() {

            var datatable = $('#list_product').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/PurchaseOrder/GetProduct',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                        }
                    },
                    pageSize: 10,
                    serverPaging: true,
                    serverFiltering: false,
                    serverSorting: true,
                    saveState: false
                },

                layout: {
                    scroll: true,
                    height: 'auto',
                    footer: false
                },

                sortable: true,

                filterable: true,

                pagination: true,

                search: {
                    input: $('#list_product_search_query')
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
                        field: 'kode_item',
                        title: 'Kode',
                        width: 'auto',
                        autoHide: false,
                        textAlign: 'center',
                        template: function(row) {
                            if(row.value_spesifikasi != null) {
                                return '('+row.value_spesifikasi+')'+row.kode_item.toUpperCase();
                            }
                            else {
                                return row.kode_item.toUpperCase();
                            }
                        },
                    },
                    {
                        field: 'nama_item',
                        title: 'Nama',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            return ucwords(row.nama_item);
                        },
                    },
                    {
                        field: 'nama_merk',
                        title: 'Merk',
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            return ucwords(row.nama_merk);
                        },
                    },
                    {
                        field: 'nama_kategori',
                        title: 'Kategori',
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            return ucwords(row.nama_kategori);
                        },
                    },
                    {
                        field: 'Actions',
                        title: 'Aksi',
                        sortable: false,
                        width: 110,
                        overflow: 'visible',
                        autoHide: false,
                        textAlign: 'center',
                        template: function(row) {
                            return "<button type='button' class='btn btn-primary btn-icon addToList' data-popup='tooltip' title='Tambah' value='" + row.id +"'><i class='flaticon2-plus'></i></button>";
                        },
                    }
                ],
            });
        });

        $("#btnProduct").on("click", function() {
            var datatable = $('#list_product').KTDatatable();
                datatable.setDataSourceParam('id_supplier', $("#supplier option:selected").val());
                datatable.reload();
        });

        $(document).ready(function() {

            var datatable = $('#list_item').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/PurchaseOrder/GetDetail',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data : {
                                idPurchaseOrder : '{{$dataPurchaseOrder->id}}',
                                mode:'edit'
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
                    scroll: true,
                    height: 'auto',
                    width: 'auto',
                    footer: false
                },

                sortable: false,

                filterable: false,

                pagination: false,

                rows: {
                    autoHide:false
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
                        title: 'Item',
                        autoHide: false,
                        width: 'auto',
                        textAlign: 'left',
                        template: function(row) {
                            if(row.value_spesifikasi != null) {
                                return '('+row.value_spesifikasi+')'+row.kode_item.toUpperCase() + ' - ' + row.nama_item.toUpperCase();
                            }
                            else {
                                return row.kode_item.toUpperCase() + ' - ' + row.nama_item.toUpperCase();
                            }
                        },
                    },
                    {
                        field: 'value4',
                        title: 'Jumlah',
                        textAlign: 'right',
                        width: 'auto',
                        type: 'number',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.value4).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'nama_satuan',
                        title: 'Satuan',
                        width: 'auto',
                        textAlign: 'left',
                        autoHide: false,
                        template: function(row) {
                            return row.nama_satuan.toUpperCase();
                        },
                    },
                    {
                        field: 'value6',
                        title: 'Harga Beli',
                        textAlign: 'right',
                        width: 'auto',
                        type: 'number',
                        autoHide: false,
                        template: function(row) {
                            var jenisPPn = $('input[name=status_ppn]:checked').val();
                            var persenPPNInclude = (100 + parseFloat("{{$taxSettings->ppn_percentage}}")) / 100;
                            if (jenisPPn == "I") {
                                var hargaMask = parseFloat(row.value6) / parseFloat(persenPPNInclude);
                            }
                            else {
                                var hargaMask = parseFloat(row.value6);
                            }
                            return parseFloat(hargaMask).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'subtotal',
                        title: 'Subtotal Item',
                        width: 'auto',
                        textAlign: 'right',
                        type: 'number',
                        autoHide: false,
                        template: function(row) {
                            var jenisPPn = $('input[name=status_ppn]:checked').val();
                            var persenPPNInclude = (100 + parseFloat("{{$taxSettings->ppn_percentage}}")) / 100;
                            if (jenisPPn == "I") {
                                var subtotalMask = parseFloat(row.subtotal) / parseFloat(persenPPNInclude);
                            }
                            else {
                                var subtotalMask = parseFloat(row.subtotal);
                            }
                            return parseFloat(subtotalMask).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'Actions',
                        title: 'Aksi',
                        sortable: false,
                        width: 110,
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

        $(document).ready(function() {

            var datatable = $('#list_riwayat').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/PurchaseOrder/GetProductHistory',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                        }
                    },
                    pageSize: 20,
                    serverPaging: true,
                    serverFiltering: false,
                    serverSorting: true,
                    saveState: false
                },

                layout: {
                    scroll: true,
                    height: 'auto',
                    footer: false
                },

                sortable: true,

                filterable: true,

                pagination: true,

                search: {
                    input: $('#list_riwayat_search_query')
                },

                rows: {
                    autoHide: false
                },

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
                        field: 'tanggal_sj',
                        title: 'Tanggal Penerimaan',
                        width: 'auto',
                        textAlign: 'left',
                        autoHide:false,
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
                        field: 'kode_penerimaan',
                        title: 'No. Surat Jalan',
                        width: 'auto',
                        textAlign: 'left',
                        autoHide:false,
                        template: function(row) {
                            var txtTgl = "";
                            if (row.kode_penerimaan != null) {
                                txtTgl += row.kode_penerimaan.toUpperCase();
                            }
                            if (row.no_po != null) {
                                txtTgl += "<br>";
                                txtTgl += "<span class='label label-md label-outline-primary label-inline mt-1'>SO : " + row.no_po.toUpperCase() + "</span>";
                            }
                            if (row.kode_invoice != null) {
                                txtTgl += "<br>";
                                txtTgl += "<span class='label label-md label-outline-primary label-inline mt-1'>INV : " + row.kode_invoice.toUpperCase() + "</span>";
                            }
                            return txtTgl;
                        },
                    },
                    {
                        field: 'nama_supplier',
                        title: 'Nama Supplier',
                        width: 'auto',
                        textAlign: 'left',
                        autoHide:false,
                        template: function(row) {
                            var txt = "";
                            txt += '<span class="font-weight-bold">'+ucwords(row.nama_supplier)+'</span>';
                            return txt;
                        },
                    },
                    {
                        field: 'qty_item',
                        title: 'Qty',
                        width: 'auto',
                        textAlign: 'right',
                        autoHide:false,
                        template: function(row) {
                            if (row.qty_item != null) {
                                return parseFloat(row.qty_item).toLocaleString('id-ID', { maximumFractionDigits: 2});
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'nama_satuan',
                        title: 'Satuan',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide:false,
                        template: function(row) {
                            return ucwords(row.nama_satuan);
                        },
                    },
                    {
                        field: 'harga_beli',
                        title: 'Harga Satuan',
                        textAlign: 'right',
                        width: 'auto',
                        autoHide:false,
                        template: function(row) {
                            return parseFloat(row.harga_beli).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                ],
            });
        });

        $("#btnProductHistory").on("click", function() {
            var datatable = $('#list_riwayat').KTDatatable();
                datatable.setDataSourceParam('id_product', $("#product option:selected").val());
                datatable.reload();
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

            $(".detailUnit").each(function(){
				if($(this).val() == "" || $(this).children("option:selected").val() == ""){
				   	$(this).closest('.form-group, input-group').find('.errUnit').show();
				  	errCount = errCount + 1;
				}
				else {
					$(this).closest('.form-group, input-group').find('.errUnit').hide();
				}
			});

            $(".numericVal").each(function() {
                if(parseFloat($(this).val()) < 1){
				   	$(this).closest('.form-group, input-group').find('.errItemNumeric').show();
				  	errCount = errCount + 1;
				}
				else {
					$(this).closest('.form-group, input-group').find('.errItemNumeric').hide();
				}
            });

            $(".priceVal").each(function() {
                if(parseFloat($(this).val()) < 0){
				   	$(this).closest('.form-group, input-group').find('.errItemPrice').show();
				  	errCount = errCount + 1;
				}
				else {
					$(this).closest('.form-group, input-group').find('.errItemPrice').hide();
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
                            url: "/PurchaseOrder/StoreDetail",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idItem : $("#product option:selected").val(),
                                idSatuan : $("#productUnit option:selected").val(),
                                idPo : "{{$dataPurchaseOrder->id}}",
                                qtyOrder : $("#qtyOrder").val(),
                                hargaBeli : $("#harga_beli").val(),
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Item Berhasil ditambahkan!.",
                                        "success"
                                    )
                                    $("#product").val("").trigger('change'),
                                    $("#productUnit").val("").trigger('change'),
                                    $("#qtyOrder").val("");
                                    $("#qtyOrderMask").val("");
                                    $("#harga_beli_mask").val("");
                                    $("#harga_beli_item").val("");
                                    $("#harga_beli_last").val("");
                                    $("#satuan_item").val("");
                                    $("#qtyOrderMask").val("");
                                    $("#harga_beli").val("");
                                    $("#harga_beli_mask").val("");
                                    $("#harga_beli_item").val("");
                                    $("#harga_beli_last").val("");
                                    var datatable = $('#list_item').KTDatatable();
                                        datatable.setDataSourceParam('idPurchaseOrder', '{{$dataPurchaseOrder->id}}');
                                        datatable.setDataSourceParam('mode', 'edit');
                                        datatable.reload();
                                    footerDataForm('{{$dataPurchaseOrder->id}}');
                                }
                                else if (result == "failDuplicate") {
                                    Swal.fire(
                                        "Gagal!",
                                        "Item ini sudah tersedia pada List Pembelian Barang !",
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
            var idItem = "";
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/PurchaseOrder/EditDetail",
                method: 'POST',
                data: {
                    idDetail: id,
                    mode:'edit'
                },
                async:false,
                success: function(result){
                    if (result.length > 0) {
                        var qty = parseFloat(result[0].value4);
                        var hargaBeli = parseFloat(result[0].value6);
                        var qtyFixed = qty.toString().replace(".", ",");
                        var hargaBeliFixed = hargaBeli.toString().replace(".", ",");
                        $("#idRowEdit").val(result[0].id);
                        $("#idItemEdit").val(result[0].value2);
                        idItem = result[0].value2;
                        $("#idSatuanEdit").val(result[0].value3);
                        $("#namaBarangEdit").val(result[0].kode_item.toUpperCase() + ' - ' + result[0].nama_item.toUpperCase());
                        $("#namaSatuanEdit").val(result[0].nama_satuan.toUpperCase());
                        $("#qtyRowEditMask").val(qty).trigger('change');
                        $("#hargaBaruMask").val(hargaBeli).trigger('change');

                        $("#btnModalEditItem").trigger('click');
                    }
                }
            });
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/PurchaseOrder/GetSatuan",
                method: 'POST',
                data: {
                    idProduct: idItem
                },
                success: function(result2){
                    $('#productUnitEdit').find('option:not(:first)').remove();
                    if (result2.length > 0) {
                        for (var i = 0; i < result2.length;i++) {
                            $("#productUnitEdit").append($('<option>', {
                                value:result2[i].id,
                                text:result2[i].kode_satuan.toUpperCase() + ' - ' + result2[i].nama_satuan.toUpperCase()
                            }));
                        }
                        $("#productUnitEdit").val($("#idSatuanEdit").val());
                    }
                    
                    $("#productUnitEdit").trigger('change');
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
                        url: "/PurchaseOrder/DeleteDetail",
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
                        datatable.setDataSourceParam('idPurchaseOrder', '{{$dataPurchaseOrder->id}}');
                        datatable.setDataSourceParam('mode', 'edit');
                        datatable.reload();
                        footerDataForm('{{$dataPurchaseOrder->id}}');
                        if (datatable.getTotalRows() < 1) {
                            $("#supplier").attr('disabled', false);
                        }
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

	    $(document).on("click", "#btnEditItem", function(e) {
            var errCount = 0;
            var errPrice = 0;
            var errVal = 0;

            var idRow = $("#idRowEdit").val();
            var idItem = $("#idItemEdit").val();
            var idSatuan = $("#productUnitEdit option:selected").val();
	     	var qty = $("#qtyRowEdit").val();
	     	var hargaBaru = $("#hargaBaru").val();

             $(".inputEdit").each(function(){
                if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                    $(this).closest('.form-group, input-group').find('.errItemEdit').show();
                    errCount = parseInt(errCount) + 1;
                }
                else {
                    $(this).closest('.form-group, input-group').find('.errItemEdit').hide();
                }
            });

            $(".inputUnitEdit").each(function(){
				if($(this).val() == "" || $(this).children("option:selected").val() == ""){
				   	$(this).closest('.form-group, input-group').find('.errUnitEdit').show();
				  	errCount = errCount + 1;
				}
				else {
					$(this).closest('.form-group, input-group').find('.errUnitEdit').hide();
				}
			});

            $(".numericValEdit").each(function() {
                if(parseFloat($(this).val()) < 1){
                    $(this).closest('.form-group, input-group').find('.errItemNumericEdit').show();
				  	errVal = errVal + 1;
				}
                else {
                    $(this).closest('.form-group, input-group').find('.errItemEdit').hide();
                }
            });

            $(".priceValEdit").each(function() {
                if(parseFloat($(this).val()) < 0){
                    $(this).closest('.form-group, input-group').find('.errItemPriceEdit').hide();
				  	errPrice = errPrice + 1;
				}
                else {
                    $(this).closest('.form-group, input-group').find('.errItemPriceEdit').hide();
                }
            });

            if(errVal != 0) {
                Swal.fire(
                    "Gagal!",
                    "Jumlah Barang tidak dapat kurang dari 1 !",
                    "warning"
                )
            }
            else if(errPrice != 0) {
                Swal.fire(
                    "Gagal!",
                    "Harga Barang tidak dapat kurang dari 0 !",
                    "warning"
                )
            }

            else if (errCount == 0) {
                Swal.fire({
                    title: "Ubah Data Item?",
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
                            url: "/PurchaseOrder/UpdateDetail",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idItem : idItem,
                                idSatuan : idSatuan,
                                idPo : "{{$dataPurchaseOrder->id}}",
                                idDetail : idRow,
                                qtyOrder : qty,
                                hargaBeli : hargaBaru,
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Item Berhasil diubah!.",
                                        "success"
                                    )
                                    $("#closeEdit").trigger('click');
                                    var datatable = $('#list_item').KTDatatable();
                                        datatable.setDataSourceParam('idPurchaseOrder', '{{$dataPurchaseOrder->id}}');
                                        datatable.setDataSourceParam('mode', 'edit');
                                        datatable.reload();
                                        footerDataForm('{{$dataPurchaseOrder->id}}');
                                }
                                else if (result == "failDuplicate") {
                                    Swal.fire(
                                        "Gagal!",
                                        "Item ini sudah tersedia pada List Pembelian Barang !",
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
            else {
                Swal.fire(
                    "Gagal!",
                    "Terdapat kolom kosong, harap mengisi kolom kosong terlebih dahulu !",
                    "warning"
                )
                e.preventDefault();
            }
	    });

        $('input[name=status_ppn]').on('change', function() {
		    var datatable = $('#list_item').KTDatatable();
                datatable.setDataSourceParam('idPurchaseOrder', '{{$dataPurchaseOrder->id}}');
                datatable.setDataSourceParam('mode', 'edit');
                datatable.reload();
                footerDataForm('{{$dataPurchaseOrder->id}}');
		});

        $('input[name=metode_bayar]').on('change', function() {
			var val = $(this).val();
			if (val == "credit") {
			    $("#durasiJT").show();
			}
			else {
				$("#durasi_jt").val(0);
                $("#durasiJT").hide();
			}
		});

        $(".btnHarga").on("click", function() {
	    	var harga = $(this).closest("div.hargaBeliItem").find(".hrg").val();
		    $("#harga_beli_mask").val(parseFloat(harga).trigger('change').toLocaleString('id-ID', { maximumFractionDigits: 2})).trigger("change");
	    });

        $(document).ready(function() {
            //getTemplateTerms
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/PurchaseOrder/GetListTerms",
                method: 'POST',
                data: {
                    target: "pembelian",
                },
                success: function(result){
                    if (result.length > 0) {
                        $('#list_terms tbody').empty();
                        if (result.length > 0) {
                            for (var i = 0; i < result.length;i++) {
                                var idTemplate = result[i].id;
                                var nama = result[i].nama_template;
                                var data="<tr>";
                                    data +="<td style='text-align:center;display:none;'>"+idTemplate+"</td>";
                                    data +="<td style='text-align:left;word-wrap:break-word;min-width:160px;max-width:160px;'>"+nama+"</td>";
                                    data +="<td style='text-align:center;'><button type='button' data-dismiss='modal' class='btn btn-primary btn-icon selectTerms'>Pilih</button></td>";
                                    data +="</tr>";
                                    $("#list_terms").append(data);
                            }
                        }
                    }
                }
            });
        });

        $("#list_terms").on('click', '.selectTerms', function() {
			var id = $(this).parents('tr:first').find('td:first').text();
			$.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/PurchaseOrder/GetTerms",
                method: 'POST',
                data: {
                    idTemplate: id,
                },
                success: function(result){
                    if (result.length > 0) {
                        var dataTemplate = "";
                        for (var i = 0; i < result.length;i++) {
                            dataTemplate += result[i].terms_and_condition;
                            counter = result.length - 1;
                            if (i != counter) {
                                dataTemplate += "\n";
                            }
                        }
                        $("#tnc").val(dataTemplate);
                    }
                }
            });
        });

        function footerDataForm(idPo) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/PurchaseOrder/GetDataFooter",
                method: 'POST',
                data: {
                    idPo: idPo,
                    mode: 'edit'
                },
                success: function(result){
                    if (result != "" && result != "null") {
                        var subtotal = result.subtotal;
                        var qtyOrder = result.qtyOrder;
                        var persenPPNInclude = (100 + parseFloat("{{$taxSettings->ppn_percentage}}")) / 100;
                        var persenPPNExclude = parseFloat("{{$taxSettings->ppn_percentage}}") / 100;
                        var jenisDisc = $('input[name=jenis_diskon]:checked').val();

                        if (qtyOrder != null || qtyOrder == "") {
                            var qtyFixed = qtyOrder.toString().replace(".", ",");
                        }
                        if (subtotal != null || subtotal == "") {
                            var subtotalFixed = subtotal.toString().replace(".", ",");
                        }

                        var jenisPPn = $('input[name=status_ppn]:checked').val();

                        $("#qtyTtl").val(qtyOrder);
                        $("#qtyTtlMask").val(parseFloat(qtyOrder).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}))

                        if (jenisPPn == "I") {
                            subtotalFixed = parseFloat(subtotalFixed) / parseFloat(persenPPNInclude);
                        }

                        var subtotalMask = parseFloat(subtotalFixed).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        $("#dpp").val(subtotalFixed);
                        $("#dppMask").val(subtotalMask);

                        var persenDiskon = 0;
                        var diskonNominal = 0;

                        if(jenisDisc == "P") {
                            persenDiskon = $("#disc_percent").val();
                            diskonNominal = parseFloat(subtotalFixed) * (parseFloat(persenDiskon) / 100);
                        }
                        else if(jenisDisc == "N") {
                            diskonNominal = $("#disc_nominal").val();
                        }

                        if (diskonNominal == 0 || diskonNominal == "") {
                            diskonNominal = 0;
                            $("#discNominalMask").val(parseFloat("0").toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                        }
                        else {
                            $("#discNominalMask").val(parseFloat(diskonNominal).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                        }
                        console.log(diskonNominal);

                        if (jenisPPn != "N") {
                            var ppn = (parseFloat(subtotalFixed) - parseFloat(diskonNominal)) * parseFloat(persenPPNExclude);
                            $("#ppn").val(ppn);
                            $("#ppnMask").val(parseFloat(ppn).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                        }
                        else {
                            var ppn = 0;
                            $("#ppn").val(ppn);
                            $("#ppnMask").val(parseFloat(ppn).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                        }

                        var grandTotal = (parseFloat(subtotalFixed) - parseFloat(diskonNominal)) + parseFloat(ppn);
                        $("#gt").val(Math.ceil(grandTotal));
                        $("#gtMask").val(parseFloat(Math.ceil(grandTotal)).toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}));

                    }
                    else {
                        $("#qtyTtl").val(0);
                        $("#qtyTtlMask").val(0);
                        $("#dpp").val(0);
                        $("#dppMask").val(0);
                        $("#discNominal").val(0);
                        $("#discNominalMask").val(0)
                        $("#ppn").val(0);
                        $("#ppnMask").val(0);
                        $("#gt").val(0);
                        $("#gtMask").val(0);
                    }
                }
            });
        }

        $(".discount").on("change", function() {
            footerDataForm('{{$dataPurchaseOrder->id}}');
        });
        
        $('input[name=jenis_diskon]').on('change', function() {
			var val = $(this).val();
			if (val == "P") {
			    $("#discNominal").hide();
                $("#discPercent").show();
                $("#disc_percent").val(0);
			}
			else {
                $("#discNominal").show();
                $("#discPercent").hide();
				$("#disc_nominal").val(0);
			}
		});

        $(document).ready(function () {
            $("#supplier").val("{{$dataPurchaseOrder->id_supplier}}").trigger('change');
            //$("#supplier").attr('disabled', true);

            $("#tanggal_po_picker").datepicker("setDate", new Date("{{$dataPurchaseOrder->tanggal_po}}"));
            $("#tanggal_req_picker").datepicker("setDate", new Date("{{$dataPurchaseOrder->tanggal_request}}"));
            $("#tanggal_deadline_picker").datepicker("setDate", new Date("{{$dataPurchaseOrder->tanggal_deadline}}"));
            footerDataForm('{{$dataPurchaseOrder->id}}');
            $("#mode").val("edit");
        });
    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
