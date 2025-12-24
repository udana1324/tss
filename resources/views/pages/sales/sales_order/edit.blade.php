@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h6 class="card-title text-white">Ubah Pesanan Penjualan</h6>
					</div>
                    <form action="{{ route('SalesOrder.update', $dataSalesOrder->id) }}" class="form-horizontal" id="form_add" method="POST" enctype="multipart/form-data">
					    <div class="card-body">
                            {{ csrf_field() }}
                            @method('PUT')
                            <div class="row">
								<div class="col-md-6">
									<fieldset>
                                        <legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi Pelanggan / Customer </h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
                                        <div class="form-group">
											<label>Kode penjualan :</label>
											<div class="input-group">
                                                <input type="hidden" value="load" id="mode" />
                                                <input type="text" class="form-control form-control-solid" placeholder="Nomor akan dibuat otomatis oleh sistem" name="kode_so" id="kode_so" value="{{strtoupper($dataSalesOrder->no_so)}}" readonly>
											</div>
										</div>

                                        <div class="form-group">
                                            <label>Nama perusahaan :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <select class="form-control select2 req" id="customer" name="customer">
                                                        <option label="Label"></option>
                                                        @foreach($dataCustomer as $customer)
                                                        <option value="{{$customer->id}}">{{strtoupper($customer->nama_customer)}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih customer terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>No. PO pelanggan :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="po_customer" id="po_customer" value="{{$dataSalesOrder->no_po_customer}}">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-primary mr-2" id="btnPo" data-toggle="modal" data-target="#modal_upload_po">Upload PO <i class="flaticon2-upload icon-sm"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

										<div class="form-group">
                                            <label>Alamat Customer :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="hidden" class=" form-control req" name="id_alamat" id="id_alamat" value="{{$dataAlamat->id}}">
                                                    <textarea class="form-control" name="alamat" id="alamat" style="resize:none;" placeholder="Silahkan Klik Tombol Pilih Alamat" readonly>{{ucwords($dataAlamat->alamat_customer)}}</textarea>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-primary" id="btnAlamat" data-toggle="modal" data-target="#modal_list_alamat">Pilih Alamat</button>
                                                    </div>
                                                </div>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih alamat customer terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row mb-0">
                                            <div class="col-md-6">
                                                <label>Tanggal penjualan :</label>
                                                <div class="form-group divTgl ">
                                                    <input type="hidden" class="form-control tglValue req" name="tanggal_so" id="tanggal_so" >
                                                    <input type="text" class="form-control pickerTgl" placeholder="Pilih Tanggal" name="tanggal_so_picker" id="tanggal_so_picker" readonly>
                                                    <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal penjualan terlebih dahulu!</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Tanggal perkiraan kirim :</label>
                                                <div class="form-group divTgl form-group-feedback form-group-feedback-right">
                                                    <input type="hidden" class="form-control tglValue req" name="tanggal_req" id="tanggal_req">
                                                    <input type="text" class="form-control pickerTgl" placeholder="Pilih Tanggal" name="tanggal_req_picker" id="tanggal_req_picker" readonly>
                                                    <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal pengiriman terlebih dahulu!</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row mb-0">
                                            <div class="col-lg-6 mb-5">
                                                <label>Metode Pembayaran :</label>
                                                <div>
                                                    <div class="radio-inline">
                                                        <label class="radio">
                                                            <input type="radio" id="cash" value="cash" name="metode_bayar" {{ $dataSalesOrder->metode_pembayaran === "cash" ? "checked" : "" }} />
                                                            <span></span>Cash/Tunai
                                                        </label>
                                                        <label class="radio">
                                                            <input type="radio" id="credit" value="credit" name="metode_bayar" {{ $dataSalesOrder->metode_pembayaran === "credit" ? "checked" : "" }} />
                                                            <span></span>Kredit
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6" id="durasiJT" @if ($dataSalesOrder->metode_pembayaran == "cash") style="display:none;" @endif>
											<label>Durasi (Hari) :</label>
    											<div>
    												<input type="text" class="form-control" maxlength="4" onkeypress="return validasiAngka(event);" name="durasi_jt" id="durasi_jt" value="{{$dataSalesOrder->durasi_jt}}">
                                                    <span class="form-text text-danger" id="errDurasi" style="display:none;">*Durasi JT Tidak dapat dibawah 1 Hari!</span>
                                                </div>
    										</div>
                                        </div>

                                        <div class="form-group">
                                            <label>Pajak Pertambahan Nilai :</label>
                                            <div class="no-gutters">
                                                <div class="radio-inline">
                                                    <label class="radio">
                                                    <input type="radio" id="statPpn_n" name="status_ppn" value="N" {{ $dataSalesOrder->flag_ppn === "N" ? "checked" : "" }} />
                                                    <span></span>Non PPn</label>
                                                    <label class="radio">
                                                    <input type="radio" id="statPpn_y" name="status_ppn" value="Y" {{ $dataSalesOrder->flag_ppn === "Y" ? "checked" : "" }} />
                                                    <span></span>PPn Excl.</label>
                                                    <label class="radio">
                                                    <input type="radio" id="statPpn_i" name="status_ppn" value="I" {{ $dataSalesOrder->flag_ppn === "I" ? "checked" : "" }} />
                                                    <span></span>PPn Incl.</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-lg-6 mb-5">
                                                <label>Diskon :</label>
                                                <div>
                                                    <div class="radio-inline">
                                                        <label class="radio">
                                                            <input type="radio" id="percentage" value="P" name="jenis_diskon" {{ $dataSalesOrder->jenis_diskon === "P" ? "checked" : "" }} />
                                                            <span></span>Persentase
                                                        </label>
                                                        <label class="radio">
                                                            <input type="radio" id="nominal" value="N" name="jenis_diskon" {{ $dataSalesOrder->jenis_diskon === "N" ? "checked" : "" }} />
                                                            <span></span>Nominal
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-6" id="discPercent" @if ($dataSalesOrder->jenis_diskon == "N") style="display:none;" @endif>
                                                <label id="txtDiskonP">Persentase (%):</label>
                                                <div>
                                                    <input type="text" class="form-control discount" onkeypress="return validasiAngka(event);" name="disc_percent" id="disc_percent" value="{{$dataSalesOrder->persentase_diskon}}">
                                                </div>
                                            </div>
                                            <div class="col-lg-6" id="discNominalDiv" @if ($dataSalesOrder->jenis_diskon == "P") style="display:none;" @endif >
                                                <label id="txtDiskonN">Nominal (Rp) :</label>
                                                <div>
                                                    <input type="text" class="form-control discount" onkeypress="return validasiAngka(event);" name="disc_nominal" id="disc_nominal" value="{{$dataSalesOrder->nominal_diskon}}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Metode Pengiriman :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <div class="radio-inline">
                                                        <label class="radio">
                                                            <input type="radio" value="delivery" name="metode_kirim" id="metode_kirim_delivery" {{ $dataSalesOrder->metode_kirim === "delivery" ? "checked" : "" }} />
                                                            <span></span>Kirim
                                                        </label>
                                                        <label class="radio">
                                                            <input type="radio" value="pickup" name="metode_kirim" id="metode_kirim_pickup" {{ $dataSalesOrder->metode_kirim === "pickup" ? "checked" : "" }} />
                                                            <span></span>Ambil Sendiri
                                                        </label>
                                                        <label class="radio">
                                                            <input type="radio" value="ekspedisi" name="metode_kirim" id="metode_kirim_ekspedisi" {{ $dataSalesOrder->metode_kirim === "ekspedisi" ? "checked" : "" }} />
                                                            <span></span>Ekspedisi
                                                        </label>
                                                    </div>
                                                    <div class="input-group-append ml-5" id="blokEkspedisi" >
                                                        <select class="form-control select2 reqEkspedisi" id="ekspedisi" name="ekspedisi">
                                                            <option label="Label"></option>
                                                            @foreach($dataEkspedisi as $ekspedisi)
                                                            <option value="{{$ekspedisi->id}}">{{strtoupper($ekspedisi->nama_cabang)}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <span class="form-text text-danger errEkspedisi" style="display:none;">*Harap pilih ekspedisi terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Catatan :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <textarea class="form-control elastic" id="tnc" name="tnc" rows="3" placeholder="Ketik Syarat & Ketentuan Penjualan Disini atau gunakan Template pada tombol Template">@foreach($dataTerms as $terms){{ucwords($terms->terms_and_cond)}}@endforeach</textarea>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-primary" id="btnTemplate" data-toggle="modal" data-target="#modal_list_terms">Template</button>
                                                    </div>
                                                </div>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih alamat customer terlebih dahulu!</span>
                                            </div>
                                        </div>

									</fieldset>
								</div>

								<div class="col-md-6">
									<fieldset>
					                	<legend class="text-muted"><h6><i class="fab la-buffer"></i> Rincian Penjualan Barang</h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>

										<div class="form-group">
                                            <label class="col-lg-7">Nama barang :</label> <span id="btnProductHistory"></span>
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
                                        </div>

										<div class="form-group">
											<label>Jumlah Penjualan Barang :</label>
											<div class="input-group">
        										<div class="col-8 pl-0">
        											<input type="text" id="qtyItemMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right">
                                                    <input type="hidden" id="qtyItem" class="form-control text-right detailItem numericVal">
                                                    {{-- <input type="hidden" id="qtyPerDus" class="form-control text-right"> --}}
                                                    <span class="form-text text-danger errItem" style="display:none;">*Harap masukkan Jumlah penjualan item terlebih dahulu!</span>
                                                    <span class="form-text text-danger errItemNumeric" style="display:none;">*Jumlah Barang tidak dapat dibawah atau 0!</span>
        										</div>
        										<div class="col-4 pr-0">
												<select class="form-control select2 detailUnit" id="productUnit" name="productUnit">
                                                        <option label="Label"></option>
                                                    </select>
                                                    <span class="form-text text-success" id="txtStok"></span>
                                                    <input type="hidden" id="stokItem" class="form-control form-control-solid">
                                                    <input type="hidden" id="hargaBeli" class="form-control form-control-solid">
                                                    <span class="form-text text-danger errUnit" style="display:none;">*Harap pilih satuan item terlebih dahulu!</span>
        										</div>
											</div>
										</div>

										<div class="form-group row mb-0">
    										<div class="form-group hargaJualItem col-xl-4 col-sm-6 col-xs-12">
    											<label>Harga jual standard :</label>
    											<div class="input-group input-group-solid">
    											    <input type="number" id="harga_jual_item" class="form-control text-right hrg" readonly>
    												<span class="input-group-append">
    												    <button type="button" class="btn btn-primary btn-icon btnHarga" data-toggle="tooltip"  title="Gunakan harga jual standard" data-placement="top">
        												    <i class="la la-check"></i>
        												</button>
        											</span>
        										</div>
                                            </div>

                                            <div class="form-group hargaJualItem col-xl-4 col-sm-6 col-xs-12">
    											<label>Harga jual penawaran :</label>
    											<div class="input-group input-group-solid">
    											    <input type="number" id="harga_jual_offer" class="form-control text-right hrg" readonly>
    												<span class="input-group-append">
        												<button type="button" class="btn btn-primary btn-icon btnHarga" data-toggle="tooltip"  title="Gunakan harga penawaran" data-placement="top">
        												    <i class="la la-check"></i>
        												</button>
        											</span>
        										</div>
    										</div>

    										<div class="form-group hargaJualItem col-xl-4 col-sm-12">
    											<label>Harga jual terakhir :</label>
    											<div class="input-group input-group-solid">
    											    <input type="number" id="harga_jual_last" class="form-control text-right hrg" readonly>
    												<span class="input-group-append">
        												<button type="button" class="btn btn-primary btn-icon btnHarga" data-toggle="tooltip"  title="Gunakan harga jual terakhir" data-placement="top">
        												    <i class="la la-check"></i>
        												</button>
        											</span>
        										</div>
    										</div>
										</div>

										<div class="form-group">
											<label class="font-weight-semibold">Harga Jual :</label>
                                            <input type="text" id="harga_jual_mask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right">
											<input type="hidden" id="harga_jual" min="0" class="form-control text-right detailItem priceVal" autocomplete="off">
											<span class="form-text text-danger errItem" style="display:none;">*Harap masukkan harga jual item terlebih dahulu!</span>
                                            <span class="form-text text-danger errItemNumeric" style="display:none;">*Harga Jual tidak dapat dibawah atau 0!</span>
										</div>

                                        <div class="form-group">
											<label class="font-weight-semibold">Keterangan Barang :</label>
											<input type="text" id="keterangan_item" class="form-control" autocomplete="off">
										</div>

                                        <div class="form-group row">
											<label class="col-lg-3 col-form-label"></label>
											<div class="col-lg-9">
												<button type="button" class="btn btn-primary font-weight-bold" id="btnAddItem">Tambah Daftar Penjualan</button>
											</div>
										</div>

									</fieldset>
								</div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset>
                                        <legend class="text-muted"><h6><i class="la la-list"></i> Daftar Penjualan Barang</h6></legend>
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
										<label class="col-lg-3 col-form-label">Total Order</label>
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
                                <button type="button" style="display: none;" id="btnModalEditItem" data-toggle="modal" data-target="#modal_form_edit_item"></button>
                                <button type="submit" class="btn btn-light-primary font-weight-bold mr-2"> Simpan <i class="flaticon-paper-plane-1"></i></button>
                            </div>
                        </div>
                        <!-- Horizontal form upload po -->
                        <div id="modal_upload_po" class="modal fade">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">

                                        <h5 class="modal-title">Upload File PO</h5>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group row">
                                            <label class="col-xl-3 col-lg-3 col-form-label text-right">File PO</label>
                                            <div class="col-lg-9 col-xl-6">
                                                <div class="image-input image-input-outline" id="file_po">
                                                    <div class="image-input-wrapper" style="background-image: url({{asset('documents/sales_order/'.$dataSalesOrder->path_po)}})"></div>
                                                    <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Input File">
                                                        <i class="fa fa-pen icon-sm text-muted"></i>
                                                        <input type="file" name="file_po_customer" accept=".png, .jpg, .jpeg, .pdf" />
                                                        <input type="hidden" name="file_po_customer_remove" />
                                                    </label>
                                                    <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="Hapus File">
                                                        <i class="ki ki-bold-close icon-xs text-muted"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-link" data-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /horizontal form upload po -->
                    </form>
                </div>

                <!-- Modal form list alamat -->
				<div id="modal_list_alamat" class="modal fade">
				    <div class="modal-dialog modal-lg">
					    <div class="modal-content">
						    <div class="modal-header bg-primary">

							    <h5 class="modal-title">List Alamat Customer</h5>
						    </div>
						    <div class="modal-body">
							    <form >
								    <!--<table class="datatable-bordered datatable-head-custom ml-4" id="list_alamat" width="100%">-->
									   <!-- <thead>-->
										  <!--  <tr>-->
											 <!--   <th align="center" style="text-align:center;display:none;">ID</th>-->
												<!--<th align="center" style="text-align:center;">Alamat</th>-->
												<!--<th align="center" style="text-align:center;">Jenis Alamat</th>-->
												<!--<th align="center" style="text-align:center;">PIC</th>-->
												<!--<th align="center" style="text-align:center;">No. Telp PIC</th>-->
												<!--<th align="center" style="text-align:center;">Aksi</th>-->
										  <!--  </tr>-->
									   <!-- </thead>-->
									   <!-- <tbody>-->

									   <!-- </tbody>-->
								    <!--</table>-->
								    <div class="mb-7">
                                    <div class="row align-items-center">
                                        <div class="col-lg-10">
                                            <div class="row align-items-center">
                                                <div class="col-md-4 my-2 my-md-0">
                                                    <div class="align-items-center">
                                                        <label style="display: inline-block;"></label>
                                                        <div class="input-icon">
                                                            <input type="text" class="form-control" placeholder="Search..." id="list_alamat_search_query"/>
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
                                <!--end: Search Form-->
                                <!--begin: Datatable-->

                                <div class="datatable datatable-bordered datatable-head-custom" id="list_alamat"></div>
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
                                        <div class="col-lg-10">
                                            <div class="row align-items-center">
                                                <div class="col-md-4 my-2 my-md-0">
                                                    <div class="align-items-center">
                                                        <label style="display: inline-block;"></label>
                                                        <div class="input-icon">
                                                            <input type="text" class="form-control" placeholder="Search..." id="list_product_search_query"/>
                                                            <span>
                                                                <i class="flaticon2-search-1 text-muted"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 my-2 my-md-0">
                                                    <div class="align-items-center">
                                                        <label class="mr-3 mb-0 d-none d-md-block">Merk :</label>
                                                        <select class="form-control select2" id="list_product_search_merk">
                                                            <option value="">All</option>
                                                            @foreach($merk as $rowMerk)
                                                            <option value="{{$rowMerk->nama_merk}}">{{ucwords($rowMerk->nama_merk)}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 my-2 my-md-0">
                                                    <div class="align-items-center">
                                                        <label class="mr-3 mb-0 d-none d-md-block">Kategori :</label>
                                                        <select class="form-control select2" id="list_product_search_kategori">
                                                            <option value="">All</option>
                                                            @foreach($kategori as $rowKategori)
                                                            <option value="{{$rowKategori->nama_kategori}}">{{ucwords($rowKategori->nama_kategori)}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
				    <div class="modal-dialog modal-xl">
					    <div class="modal-content">
						    <div class="modal-header bg-primary">
							    <h5 class="modal-title text-white">Ubah Harga Item</h5>
						    </div>
						    <div class="modal-body">
							    <form >
							        <table class="table table-hover table-responsive-xl" id="list_edit_item" width="100%">
									    <thead class="thead-light">
										    <tr class="text-left">
											    <th class="d-none">ID</th>
												<th>Nama Barang</th>
												<th>Jumlah</th>
												<th>Satuan</th>
												<th>Harga Jual</th>
                                                <th>Keterangan</th>
										    </tr>
									    </thead>
									    <tbody id="detil_edit_item">

									    </tbody>
								    </table>
							    </form>
						    </div>

						    <div class="modal-footer">
							    <button type="button" class="btn btn-primary" id="btnEditItem" data-dismiss="modal">Simpan</button>
							    <button type="button"class="btn btn-light me-3" data-dismiss="modal">Batal</button>
						    </div>
					    </div>
				    </div>
			    </div>
				<!-- /horizontal form edit item -->

                <!-- Modal form history barang -->
				<div id="modal_history_product" class="modal fade">
				    <div class="modal-dialog modal-lg">
					    <div class="modal-content">
						    <div class="modal-header bg-primary">

							    <h5 class="modal-title">Riwayat Barang</h5>
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

            var imgItem = new KTImageInput('file_po');

            $('#customer').select2({
                allowClear: true,
                placeholder: "Silahkan pilih nama perusahaan"
            });

            $('#ekspedisi').select2({
                allowClear: true,
                placeholder: "Silahkan Pilih Ekspedisi Disini"
            });

            $('#product').select2({
                allowClear: true,
                placeholder: "Silahkan pilih nama barang untuk dijual"
            });

            $('#productUnit').select2({
                allowClear: true,
                placeholder: "Pilih satuan..."
            });

            $('#list_product_search_merk').select2({
                allowClear: true,
                placeholder: "Silahkan Pilih Merk Item"
            });

            $('#list_product_search_kategori').select2({
                allowClear: true,
                placeholder: "Silahkan Pilih Kategori Item"
            });

            $('#tanggal_so_picker, #tanggal_req_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                autoclose : true,
                format : "dd MM yyyy",
            });

            $("#qtyItemMask").autoNumeric('init');
            $("#harga_jual_mask").autoNumeric('init');

        });

        $("#qtyItemMask").on('change', function() {
            $("#qtyItem").val($("#qtyItemMask").autoNumeric("get"));
            var qty = $("#qtyItemMask").autoNumeric("get");
            // var qtyPerDus = $("#qtyPerDus").val();

            // if ((qtyPerDus != "" || qtyPerDus != null) && qtyPerDus > 0) {
            //     var qtyDus = parseInt(qty) / parseInt(qtyPerDus);
            //     var txt = qtyDus + " Dus x " + parseInt(qtyPerDus).toLocaleString('id-ID', { maximumFractionDigits: 0}) + " " + $("#satuan_item").val();

            //     $("#keterangan_item").val(txt);
            // }
        });

        function formatDate(strDate) {
            var arrMonth = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            var date = new Date(strDate);
            var day = date.getDate();
            var month = date.getMonth();
            var year = date.getFullYear();

            return day + ' ' + arrMonth[month] + ' ' + year;
        }

        $("#harga_jual_mask").on('change', function() {
            $("#harga_jual").val($("#harga_jual_mask").autoNumeric("get"));
        });

        $('input[name=metode_kirim]').on('change', function() {
			var val = $(this).val();
			if (val == "delivery") {
				$("#blokEkspedisi").hide();
			}
			else {
				$("#blokEkspedisi").show()
			}
		});

        $(".pickerTgl").on('change', function() {
            var reqDate = new Date($("#tanggal_req_picker").datepicker('getDate'));
            var soDate = new Date($("#tanggal_so_picker").datepicker('getDate'));
            var selisih = Math.floor((Date.UTC(reqDate.getFullYear(), reqDate.getMonth(), reqDate.getDate()) - Date.UTC(soDate.getFullYear(), soDate.getMonth(), soDate.getDate()) ) /(1000 * 60 * 60 * 24));

            $(this).closest(".divTgl").find(".tglValue").val($(this).data('datepicker').getFormattedDate('yyyy-mm-dd'));

            if ($("#tanggal_req").val() != "") {
                if (selisih < 0) {
                    Swal.fire(
                        "Error!",
                        "Tanggal pengiriman tidak boleh dibawah dari tanggal penjualan!.",
                        "warning"
                    )
                    $("#tanggal_req").val("");
                    $("#tanggal_req_picker").val("");
                }
            }
        });

        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        $("#cancel").on('click', function(e) {
            Swal.fire({
                title: "Batal?",
                text: "Apakah anda ingin membatalkan peubahan pesanan penjualan?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    window.location.href = "{{ url('/SalesOrder') }}";
                    // $.ajaxSetup({
                    //     headers: {
                    //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    //     }
                    // });
                    // $.ajax({
                    //     url: "/SalesOrder/RestoreDetail",
                    //     method: 'POST',
                    //     data: {
                    //         idSo: '{{$dataSalesOrder->id}}'
                    //     },
                    //     success: function(result){
                    //         window.location.href = "{{ url('/SalesOrder') }}";
                    //     }
                    // });
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

                    if($('input[name=metode_kirim]:checked').val() == "ekspedisi") {
                        if ($("#ekspedisi").val() == "") {
                            $("#errEkspedisi").show();
                            count = parseInt(count) + 1;
                        }
                        else {
                            $("#errEkspedisi").hide();
                        }
                    }

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
                            "Harap Tambahkan Minimum 1 Item Penjualan!.",
                            "warning"
                        )
                        count = parseInt(count) + 1;
                        e.preventDefault();
                    }

                    if (count == 0) {
                        $("html, body").animate({ scrollTop: 0 }, "slow");
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

        $("#customer").on("change", function() {
            //getListProduct
            getCustomerProduct($(this).val());

            //getDefaultAddress
            if ($("#mode").val() == "edit") {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/SalesOrder/GetDefaultAddress",
                    method: 'POST',
                    data: {
                        id_customer: $(this).val(),
                    },
                    success: function(result){
                        if (result.length > 0) {
                            $("#id_alamat").val(result[0].id);
                            $("#alamat").val(ucwords(result[0].alamat_customer));
                        }
                    }
                });

                //Hapus Daftar penjualan
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/SalesOrder/ResetDetail",
                    method: 'POST',
                    data: {
                        idSO: '{{$dataSalesOrder->id}}',
                    },
                    success: function(result){
                        var datatable = $('#list_item').KTDatatable();
                            datatable.setDataSourceParam('idSalesOrder', '{{$dataSalesOrder->id}}');
                            datatable.setDataSourceParam('mode', 'edit');
                            datatable.reload();
                            footerDataForm('{{$dataSalesOrder->id}}');
                    }
                });
            }

            // //getCustomerAddress
            // $.ajaxSetup({
            //     headers: {
            //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //     }
            // });
            // $.ajax({
            //     url: "/SalesOrder/GetCustomerAddress",
            //     method: 'POST',
            //     data: {
            //         id_customer: $(this).val(),
            //     },
            //     success: function(result){
            //         if (result.length > 0) {
            //             $('#list_alamat tbody').empty();
            //             if (result.length > 0) {
            //                 for (var i = 0; i < result.length;i++) {
            //                     var idAlamat = result[i].id;
            //                     var alamat = result[i].alamat_customer;
            //                     var jenisAlamat = result[i].jenis_alamat;
            //                     var pic = result[i].pic_alamat;
            //                     var tlpPic = result[i].telp_pic;
            //                     var data="<tr>";
            //                         data +="<td style='text-align:center;display:none;'>"+idAlamat+"</td>";
            //                         data +="<td style='text-align:left;word-wrap:break-word;min-width:160px;max-width:160px;'>"+ucwords(alamat)+"</td>";
            //                         data +="<td style='text-align:center;'>"+jenisAlamat+"</td>";
            //                         data +="<td style='text-align:center;'>"+ucwords(pic)+"</td>";
            //                         data +="<td style='text-align:center;'>"+tlpPic+"</td>";
            //                         data +="<td style='text-align:center;'><button type='button' data-dismiss='modal' class='btn btn-primary btn-icon select'>Pilih</button></td>";
            //                         data +="</tr>";
            //                         $("#list_alamat").append(data);
            //                 }
            //             }
            //         }
            //     }
            // });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/SalesOrder/GetCustomerPreviousOrder",
                method: 'POST',
                data: {
                    id_customer: $(this).val(),
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
        });

        $("#product").on("change", function() {
            //getdataItem
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/SalesOrder/GetSatuan",
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
            //getDefaultAddress
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/SalesOrder/GetDataItem",
                method: 'POST',
                data: {
                    id_product: $("#product option:selected").val(),
                    id_satuan: $(this).val(),
                    id_customer: $("#customer option:selected").val(),
                    id_alamat: $("#id_alamat").val(),
                },
                success: function(result){
                    if (result != null || result.length > 0) {
                        hargaQuotation = result.harga_jual_quotation == "" || result.harga_jual_quotation == null ? 0 : result.harga_jual_quotation;
                        hargaJualItem = result.harga_jual == "" || result.harga_jual == null ? 0 : result.harga_jual;
                        hargaLast = result.harga_jual_last == "" || result.harga_jual_last == null ? 0 : result.harga_jual_last;
                        hargaBeli = result.harga_beli_last == "" || result.harga_beli_last == null ? 0 : result.harga_beli_last;
                        $("#harga_jual_offer").val(hargaQuotation);
                        $("#harga_jual_last").val(hargaLast);
                        $("#harga_jual_item").val(hargaJualItem);
                        $("#hargaBeli").val(hargaBeli);
	                  	var stokItem = result.stok_item == "" || result.stok_item == null ? 0 : result.stok_item;

                        // $("#qtyPerDus").val(result.qty_per_dus);

	                    $("#txtStok").html("Stok barang saat ini : "+parseFloat(stokItem).toLocaleString('id-ID', { maximumFractionDigits: 0}));
                        $("#stokItem").val(stokItem);
                        $("#btnProductHistory").html('<a href="#" class="font-size-sm font-weight-bold text-danger text-right text-hover-muted" id="btnProductHistory" data-toggle="modal" data-target="#modal_history_product">[Lihat Riwayat Barang]</a>');
                    }
                    else {
                        $("#harga_jual_offer").val(0);
                        $("#harga_jual_last").val(0);
                        $("#harga_jual_item").val(0);
                        $("#txtStok").html("0");
                        $("#stokItem").val(0);
                        $("#hargaBeli").val(0);
                        $("#btnProductHistory").html("");
                    }
                }
            });
        });

        function getCustomerProduct(idCustomer) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/SalesOrder/GetProductByCustomer",
                method: 'POST',
                data: {
                    id_customer: idCustomer,
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

        $("#list_alamat").on('click', 'table .selectAlamat', function() {
			var id = $(this).parents('tr:first').find('td:first').text();
			var alamat = $(this).parents('tr:first').find('td:eq(1)').text();
			$("#id_alamat").val(id);
			$("#alamat").val(ucwords(alamat));
        });

        $("#list_product").on('click', 'table .addToList', function() {
            var idItem = $(this).val();
            var datatable = $('#list_product').KTDatatable();
            var namaItem = datatable.getRecord(idItem).getColumn('nama_item').getValue();
            var kd = $(this).parents('tr:first').find('td:first').text();
            var idCustomer = $("#customer option:selected").val();
	        var nmCustomer = $("#customer option:selected").html();
            Swal.fire({
                title: "Tambahkan Data?",
                text: "Apakah anda ingin menambah barang ini pada customer " + nmCustomer +" ?",
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
                        url: "/SalesOrder/AddCustomerProduct",
                        method: 'POST',
                        data: {
                            id_item: idItem,
                            id_customer: idCustomer
                        },
                        success: function(result){
                            Swal.fire(
                                "Berhasil!",
                                "Barang Berhasil ditambahkan ke customer " + nmCustomer + "!",
                                "success"
                            )
                            getCustomerProduct(idCustomer);
                            var datatable = $('#list_product').KTDatatable();
                                datatable.setDataSourceParam('id_customer', idCustomer);
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
                            url: '/SalesOrder/GetProduct',
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

                rows: {
                    autoHide:false
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
                        field: 'nama_merk',
                        title: 'Nama',
                        width: 'auto',
                        textAlign: 'left',
                        visible:false,
                        template: function(row) {
                            var txt = "";
                            txt += '<span class="label label-md label-outline-primary label-inline mr-2 mb-1 mt-1">' +row.nama_merk.toUpperCase()+ '</span>';

                            return txt;
                        },
                    },
                    {
                        field: 'nama_item',
                        title: 'Nama',
                        width: 'auto',
                        textAlign: 'left',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                            txt += '<span class="font-weight-bold">'+ucwords(row.nama_item)+'</span><br />';
                            if(row.value_spesifikasi != null) {
                                txt += '<span class="label label-md label-outline-primary label-inline mr-2 mb-1 mt-1">' +'('+row.value_spesifikasi+')'+row.kode_item.toUpperCase()+ '</span>';
                            }
                            else {
                                txt += '<span class="label label-md label-outline-primary label-inline mr-2 mb-1 mt-1">' +row.kode_item.toUpperCase()+ '</span>';
                            }
                            txt += '<span class="label label-md label-outline-primary label-inline mr-2 mb-1 mt-1">' +row.nama_merk.toUpperCase()+ '</span>';
                            txt += '<span class="label label-md label-outline-primary label-inline mr-2 mb-1 mt-1">' +row.nama_kategori.toUpperCase()+ '</span>';

                            return txt;
                        },
                    },
                    {
                        field: 'nama_kategori',
                        title: 'Nama',
                        width: 'auto',
                        textAlign: 'left',
                        visible:false,
                        template: function(row) {
                            var txt = "";
                            txt += '<span class="label label-md label-outline-primary label-inline mr-2 mb-1 mt-1">' +row.nama_kategori.toUpperCase()+ '</span>';

                            return txt;
                        },
                    },
                    {
                        field: 'Actions',
                        title: 'Aksi',
                        sortable: false,
                        width: 'auto',
                        overflow: 'visible',
                        autoHide: false,
                        textAlign: 'center',
                        template: function(row) {
                            return "<button type='button' class='btn btn-primary btn-icon addToList' data-popup='tooltip' title='Tambah' value='" + row.id +"'><i class='flaticon2-plus'></i></button>";
                        },
                    }
                ],
            });

            $('#list_product_search_merk').on('change', function() {
                datatable.search($(this).val(), 'nama_merk');
            });

            $('#list_product_search_kategori').on('change', function() {
                datatable.search($(this).val(), 'nama_kategori');
            });
        });

        $(document).ready(function() {

            var datatable = $('#list_riwayat').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/SalesOrder/GetProductHistory',
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
                        width: 20,
                        type: 'number',
                        selector: false,
                        textAlign: 'center',
                        visible:false,
                    },
                    {
                        field: 'tanggal_sj',
                        title: 'Tanggal Pengiriman',
                        width: 100,
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
                        field: 'kode_pengiriman',
                        title: 'No. Surat Jalan',
                        width: 150,
                        textAlign: 'left',
                        autoHide:false,
                        template: function(row) {
                            var txtTgl = "";
                            if (row.kode_pengiriman != null) {
                                txtTgl += row.kode_pengiriman.toUpperCase();
                            }
                            if (row.no_so != null) {
                                txtTgl += "<br>";
                                txtTgl += "<span class='label label-md label-outline-primary label-inline mt-1'>SO : " + row.no_so.toUpperCase() + "</span>";
                            }
                            if (row.kode_invoice != null) {
                                txtTgl += "<br>";
                                txtTgl += "<span class='label label-md label-outline-primary label-inline mt-1'>INV : " + row.kode_invoice.toUpperCase() + "</span>";
                            }
                            return txtTgl;
                        },
                    },
                    {
                        field: 'nama_customer',
                        title: 'Nama Pelanggan',
                        width: 235,
                        textAlign: 'left',
                        autoHide:false,
                        template: function(row) {
                            var txt = "";
                            txt += '<span class="font-weight-bold">'+ucwords(row.nama_customer)+'</span>';
                            txt += "<br />";
                            txt += '<span class="label label-md label-outline-primary label-inline mr-2 mb-1 mt-1">' + row.nama_outlet + '</span>';
                            txt += "<br />";

                            if (row.no_po_customer != null) {
                                txt += '<span class="font-weight-bold text-inline text-primary font-size-xs">No. PO : '+row.no_po_customer.toUpperCase()+'</span>';
                            }
                            else {
                                txt += '<span class="font-weight-bold text-inline text-primary font-size-xs">No. PO : - </span>';
                            }
                            return txt;
                        },
                    },
                    {
                        field: 'qty_item',
                        title: 'Qty',
                        width: 100,
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
                        field: 'harga_jual',
                        title: 'Harga Satuan',
                        textAlign: 'right',
                        width: 100,
                        autoHide:false,
                        template: function(row) {
                            return parseFloat(row.harga_jual).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                ],
            });
        });

        $("#btnProduct").on("click", function() {
            var datatable = $('#list_product').KTDatatable();
                datatable.setDataSourceParam('id_customer', $("#customer option:selected").val());
                datatable.reload();
        });

        $("#btnProductHistory").on("click", function() {
            var datatable = $('#list_riwayat').KTDatatable();
                datatable.setDataSourceParam('id_customer', $("#customer option:selected").val());
                datatable.setDataSourceParam('id_product', $("#product option:selected").val());
                datatable.setDataSourceParam('id_satuan', $("#productUnit option:selected").val());
                datatable.reload();
        });

        $("#btnAlamat").on("click", function() {
            var datatable = $('#list_alamat').KTDatatable();
                datatable.setDataSourceParam('id_customer', $("#customer option:selected").val());
                datatable.reload();
        });

        $(document).ready(function() {

            var datatable = $('#list_item').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/SalesOrder/GetDetail',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data : {
                                idSalesOrder : '{{$dataSalesOrder->id}}',
                                mode : "edit"
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
                        width: 0,
                        type: 'number',
                        selector: false,
                        textAlign: 'center',
                        visible:false,
                    },
                    {
                        field: 'kode_item',
                        title: 'Deskripsi Barang',
                        autoHide: false,
                        width: 300,
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
                        width: 75,
                        textAlign: 'right',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.value4).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'nama_satuan',
                        title: 'Satuan',
                        textAlign: 'center',
                        width: 80,
                        autoHide: false,
                        template: function(row) {
                            return row.nama_satuan.toUpperCase();
                        },
                    },
                    {
                        field: 'value7',
                        width: 100,
                        title: 'Harga Jual',
                        textAlign: 'right',
                        autoHide: false,
                        template: function(row) {
                            var jenisPPn = $('input[name=status_ppn]:checked').val();
                            var persenPPNInclude = (100 + parseFloat("{{$taxSettings->ppn_percentage}}")) / 100;
                            if (jenisPPn == "I") {
                                var hargaMask = parseFloat(row.value7) / parseFloat(persenPPNInclude);
                            }
                            else {
                                var hargaMask = parseFloat(row.value7);
                            }
                            return parseFloat(hargaMask).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'subtotal',
                        width: 'auto',
                        title: 'Subtotal Item',
                        textAlign: 'right',
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
                            return parseFloat(subtotalMask).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'value8',
                        title: 'Keterangan',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                            if (row.value8 != null) {
                                txt += row.value8;
                            }
                            else {
                                txt += "-";
                            }
                            return txt;
                        },
                    },
                    {
                        field: 'Actions',
                        title: 'Aksi',
                        sortable: false,
                        width: 85,
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

            if (parseFloat($("#stokItem").val()) < 1) {

                Swal.fire(
                    "Gagal!",
                    "Stok Tidak Tersedia atau 0 !",
                    "warning"
                );
                return false;
            }

            if (parseFloat($("#qtyItem").val()) > parseFloat($("#stokItem").val())) {

                Swal.fire(
                    "Gagal!",
                    "Jumlah Item Melebihi Stok Tersedia !",
                    "warning"
                );
                return false;
            }

            if (parseFloat($("#harga_jual").val()) < parseFloat($("#hargaBeli").val())) {

                Swal.fire(
                    "Gagal!",
                    "Harga Penjualan Dibawah Harga Pembelian Terakhir, harap periksa dengan bagian Pembelian!",
                    "warning"
                );
                return false;
            }

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
                            url: "/SalesOrder/StoreDetail",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idItem : $("#product option:selected").val(),
                                idSatuan : $("#productUnit option:selected").val(),
                                idSo : "{{$dataSalesOrder->id}}",
                                qtyItem : $("#qtyItem").val(),
                                hargaJual : $("#harga_jual").val(),
                                keterangan : $("#keterangan_item").val()
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Item Berhasil ditambahkan!.",
                                        "success"
                                    )
                                    $("#product").val("").trigger('change'),
                                    $("#qtyItem").val("");
                                    $("#qtyItemMask").val("");
                                    $("#harga_jual_offer").val(0);
                                    $("#harga_jual_last").val(0);
                                    $("#harga_jual_item").val(0);
                                    $("#harga_jual").val("");
                                    $("#harga_jual_mask").val("");
                                    $("#satuan_item").val("");
                                    $("#keterangan_item").val("");
                                    $("#txtStok").html("");
                                    $("#stokItem").val(0);
                                    $("#hargaBeli").val(0);
                                    var datatable = $('#list_item').KTDatatable();
                                        datatable.setDataSourceParam('idSalesOrder', '{{$dataSalesOrder->id}}');
                                        datatable.setDataSourceParam('mode', 'edit');
                                        datatable.reload();
                                    footerDataForm('{{$dataSalesOrder->id}}');
                                }
                                else if (result == "failDuplicate") {
                                    Swal.fire(
                                        "Gagal!",
                                        "Item ini sudah tersedia pada Daftar Penjualan Barang !",
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
                url: "/SalesOrder/EditDetail",
                method: 'POST',
                data: {
                    idDetail: id,
                    mode:"edit"
                },
                success: function(result){
                    if (result.length > 0) {
                        var qty = parseFloat(result[0].value4);
                        var hargaJual = parseFloat(result[0].value7);
                        var qtyFixed = qty.toString().replace(".", ",");
                        var hargaJualFixed = hargaJual.toString().replace(".", ",");
                        var kodeItem = "";
                        var keterangan = "";
                        if (result[0].value8 != null) {
                            keterangan = result[0].value8;
                        }

                        if (result[0].value_spesifikasi != null) {
                            kodeItem = '('+result[0].value_spesifikasi+')'+result[0].kode_item.toUpperCase();
                        }
                        else {
                            kodeItem = result[0].kode_item.toUpperCase();
                        }

                        var data = "<tr>";
                            data += "<td class='d-none'><input type='text' class='form-control' id='idRowEdit' value='"+result[0].id+"' /></td>";
                            data += "<td class='d-none'><input type='text' class='form-control' id='idItemEdit' value='"+result[0].value2+"' /></td>";
                            data += "<td class='d-none'><input type='text' class='form-control' id='idSatuanEdit' value='"+result[0].value3+"' /></td>";
                            data += "<td>"+ kodeItem + ' - ' + result[0].nama_item.toUpperCase()+"</td>";
                            data += "<td><input type='text' class='form-control inputEdit numericValEdit' id='qtyRowEditMask' autocomplete='off' data-a-dec=',' data-a-sep='.' value='"+qtyFixed+"' /><input type='hidden' class='form-control inputEdit numericValEdit' id='qtyRowEdit' value='"+qtyFixed+"' /></td>";
                            data += "<td><input type='text' class='form-control form-control-solid' readonly id='idItemEdit' value='"+result[0].nama_satuan.toUpperCase()+"' /></td>";
                            data += "<td><input type='text' class='form-control' autocomplete='off' data-a-dec=',' data-a-sep='.' id='hargaBaruMask' value='"+hargaJualFixed+"' /><input type='hidden' class='form-control inputEdit priceValEdit' onkeypress='return validasiDecimal(this,event);' id='hargaBaru' value='"+hargaJualFixed+"' /></td>";
                            data += "<td width='200px' style='text-align:center;'><input type='text' class='form-control' id='keteranganEdit' value='"+keterangan+"' /></td>";
                            data += "</tr>";
                            $('#detil_edit_item').append(data);

                            $("#qtyRowEditMask").autoNumeric('init');
                            $("#qtyRowEditMask").on('change', function() {
                                $("#qtyRowEdit").val($("#qtyRowEditMask").autoNumeric("get"));
                            });

                            $("#hargaBaruMask").autoNumeric('init');
                            $("#hargaBaruMask").on('change', function() {
                                $("#hargaBaru").val($("#hargaBaruMask").autoNumeric("get"));
                            });

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            url: "/SalesOrder/GetDataItem",
                            method: 'POST',
                            data: {
                                id_product: result[0].value2,
                                id_satuan: result[0].value3,
                                id_customer: $("#customer option:selected").val()
                            },
                            success: function(result){
                                if (result != null || result.length > 0) {
                                    $("#stokItem").val(result.stok_item);
                                }
                                else {
                                    $("#stokItem").val(0);
                                }
                            }
                        });

                        $("#btnModalEditItem").trigger('click');
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
                        url: "/SalesOrder/DeleteDetail",
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
                            var datatable = $('#list_item').KTDatatable();
                            datatable.setDataSourceParam('idSalesOrder', '{{$dataSalesOrder->id}}');
                            datatable.setDataSourceParam('mode', 'edit');
                            datatable.reload();
                            footerDataForm('{{$dataSalesOrder->id}}');
                        }
                    });

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

            var idRow = $("#idRowEdit").val();
            var idItem = $("#idItemEdit").val();
            var idSatuan = $("#idSatuanEdit").val();
	     	var qty = $("#qtyRowEdit").val();
	     	var hargaBaru = $("#hargaBaru").val();
             var keterangan = $("#keteranganEdit").val();

            $(".inputEdit").each(function(){
                if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                    errCount = parseInt(errCount) + 1;
                }
            });

            if (parseFloat($("#stokItem").val()) < 1) {

                Swal.fire(
                    "Gagal!",
                    "Stok Tidak Tersedia atau 0 !",
                    "warning"
                );
                return false;
            }

            if (parseFloat(qty) > parseFloat($("#stokItem").val())) {

                Swal.fire(
                    "Gagal!",
                    "Jumlah Item Melebihi Stok Tersedia !",
                    "warning"
                );
                return false;
            }

            if (errCount == 0) {
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
                            url: "/SalesOrder/UpdateDetail",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idItem : idItem,
                                idSatuan : idSatuan,
                                idSo : "{{$dataSalesOrder->id}}",
                                idDetail : idRow,
                                qtyItem : qty,
                                hargaJual : hargaBaru,
                                keterangan : keterangan,
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Item Berhasil diubah!.",
                                        "success"
                                    )
                                    var datatable = $('#list_item').KTDatatable();
                                        datatable.setDataSourceParam('idSalesOrder', '{{$dataSalesOrder->id}}');
                                        datatable.setDataSourceParam('mode', 'edit');
                                        datatable.reload();
                                        footerDataForm('{{$dataSalesOrder->id}}');
                                }
                                else if (result == "failDuplicate") {
                                    Swal.fire(
                                        "Gagal!",
                                        "Item ini sudah tersedia pada Daftar Penjualan Barang !",
                                        "warning"
                                    )
                                }
                            }
                        });
                    }
                    else if (result.dismiss === "cancel") {
                        console.log(result);
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
            }
	    });

        $('input[name=status_ppn]').on('change', function() {
		    var datatable = $('#list_item').KTDatatable();
                datatable.setDataSourceParam('idSalesOrder', '{{$dataSalesOrder->id}}');
                datatable.setDataSourceParam('mode', 'edit');
                datatable.setDataSourceParam('mode', 'edit');
                datatable.reload();
                footerDataForm('{{$dataSalesOrder->id}}');
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
	    	var harga = $(this).closest("div.hargaJualItem").find(".hrg").val();
	    	$("#harga_jual").val(harga);
            $("#harga_jual_mask").val(harga);
	    });

        $(document).ready(function() {
            //getTemplateTerms
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/SalesOrder/GetListTerms",
                method: 'POST',
                data: {
                    target: "penjualan",
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
                                    data +="<td style='text-align:left;word-wrap:break-word;min-width:160px;max-width:160px;'>"+ucwords(nama)+"</td>";
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
                url: "/SalesOrder/GetTerms",
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

        function footerDataForm(idSo) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/SalesOrder/GetDataFooter",
                method: 'POST',
                data: {
                    idSo: idSo,
                    mode: "edit"
                },
                success: function(result){
                    if (result != "null") {
                        var subtotal = result.subtotal;
                        var qtyItem = result.qtyItem;
                        var qtyFixed = qtyItem;
                        var subtotalFixed = subtotal;
                        var jenisPPn = $('input[name=status_ppn]:checked').val();
                        var persenPPNInclude = (100 + parseFloat("{{$taxSettings->ppn_percentage}}")) / 100;
                        var persenPPNExclude = parseFloat("{{$taxSettings->ppn_percentage}}") / 100;
                        var jenisDisc = $('input[name=jenis_diskon]:checked').val();

                        $("#qtyTtl").val(qtyFixed);
                        $("#qtyTtlMask").val(parseFloat(qtyFixed).toLocaleString('id-ID', { maximumFractionDigits: 2}))

                        if (jenisPPn == "I") {
                            subtotalFixed = parseFloat(subtotalFixed) / parseFloat(persenPPNInclude);
                        }

                        var subtotalMask = parseFloat(subtotalFixed).toLocaleString('id-ID', { maximumFractionDigits: 2});
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

                        $("#discNominalMask").val(parseFloat(diskonNominal).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                        if (jenisPPn != "N") {
                            var ppn = (parseFloat(subtotalFixed) - parseFloat(diskonNominal)) * parseFloat(persenPPNExclude);
                            $("#ppn").val(ppn);
                            $("#ppnMask").val(parseFloat(ppn).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        }
                        else {
                            var ppn = 0;
                            $("#ppn").val(ppn);
                            $("#ppnMask").val(parseFloat(ppn).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        }

                        var grandTotal = (parseFloat(subtotalFixed) - parseFloat(diskonNominal)) + parseFloat(ppn);
                        $("#gt").val(Math.ceil(grandTotal));
                        $("#gtMask").val(parseFloat(Math.ceil(grandTotal)).toLocaleString('id-ID', { maximumFractionDigits: 2}));

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

        $('input[name=jenis_diskon]').on('change', function() {
			var val = $(this).val();
			if (val == "P") {
			    $("#discNominalDiv").hide();
                $("#discPercent").show();
                $("#disc_percent").val(0);
			}
			else {
                $("#discNominalDiv").show();
                $("#discPercent").hide();
				$("#disc_nominal").val(0);
			}
            footerDataForm('{{$dataSalesOrder->id}}');
		});

        $(".discount").on("change", function() {
            footerDataForm('{{$dataSalesOrder->id}}');
        });

        $(document).ready(function() {

            var datatable = $('#list_alamat').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/SalesOrder/GetCustomerAddress',
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
                    input: $('#list_alamat_search_query')
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
                        field: 'alamat',
                        title: 'AlamatHidden',
                        width: 'auto',
                        textAlign: 'left',
                        autoHide: false,
                        visible:false,
                        template: function(row) {
                            var txt = "";
                            txt += '<span class="font-weight-bold">'+ucwords(row.alamat_customer)+'</span><br />';
                            return txt;
                        },
                    },
                    {
                        field: 'alamat_customer',
                        title: 'Alamat',
                        width: 'auto',
                        textAlign: 'left',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                            txt += '<span class="font-weight-bold">'+ucwords(row.alamat_customer)+'</span><br />';
                            if (row.nama_outlet == null) {
                                txt += '<span class="label label-md label-outline-primary label-inline mr-2 mb-1 mt-1">-</span>';
                            }
                            else {
                                txt += '<span class="label label-md label-outline-primary label-inline mr-2 mb-1 mt-1">' + ucwords(row.nama_outlet) + '</span>';
                            }
                            return txt;
                        },
                    },
                    {
                        field: 'kota',
                        title: 'Kota',
                        width: '150',
                        textAlign: 'left',
                        template: function(row) {
                            var txt = "";
                            txt += '<span class="font-weight-bold">' + ucwords(row.kota) + '</span>';

                            return txt;
                        },
                    },
                    {
                        field: 'pic_alamat',
                        title: 'PIC',
                        width: '80',
                        textAlign: 'left',
                        template: function(row) {
                            var txt = "";
                            txt += '<span class="font-weight-bold">' + ucwords(row.pic_alamat) + '</span>';

                            return txt;
                        },
                    },
                    {
                        field: 'telp_pic',
                        title: 'Telp PIC',
                        width: '100',
                        textAlign: 'left',
                        template: function(row) {
                            var txt = "";
                            txt += '<span class="font-weight-bold">' + ucwords(row.telp_pic) + '</span>';

                            return txt;
                        },
                    },
                    {
                        field: 'jenis_alamat',
                        title: 'Jenis Alamat',
                        width: '150',
                        textAlign: 'left',
                        template: function(row) {
                            var txt = "";
                            txt += '<span class="font-weight-bold">' + row.jenis_alamat.toUpperCase() + '</span>';

                            return txt;
                        },
                    },
                    {
                        field: 'Actions',
                        title: 'Aksi',
                        sortable: false,
                        width: 'auto',
                        overflow: 'visible',
                        autoHide: false,
                        textAlign: 'center',
                        template: function(row) {
                            return "<button type='button' data-dismiss='modal' class='btn btn-primary btn-icon selectAlamat' data-popup='tooltip' title='Pilih' value='" + row.id +"'>Pilih</button>";
                        },
                    }
                ],
            });
        });


        $(document).ready(function () {
            $("#customer").val("{{$dataSalesOrder->id_customer}}").trigger('change');
            var metodeBayar = "{{$dataSalesOrder->metode_pembayaran}}";
            if (metodeBayar == "cash") {
                $("#durasiJT").hide();
            }
            else {
                $("#durasiJT").show();
            }
            var metodeKirim = "{{$dataSalesOrder->metode_kirim}}";
            if (metodeKirim == "ekspedisi") {
                $("#ekspedisi").val("{{$dataSalesOrder->jenis_kirim}}").trigger('change');
            }
            else {
                $("#blokEkspedisi").hide();
            }
            footerDataForm('{{$dataSalesOrder->id}}');
            $("#tanggal_so_picker").datepicker("setDate", new Date("{{$dataSalesOrder->tanggal_so}}"));
            $("#tanggal_req_picker").datepicker("setDate", new Date("{{$dataSalesOrder->tanggal_request}}"));
            $("#mode").val("edit");
        });
    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
