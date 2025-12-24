@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header card-header-tabs-line">
                        <div class="card-toolbar">
                            <ul class="nav nav-tabs nav-bold nav-tabs-line">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#tab_pane_1" id="tab1">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Data Barang</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab_pane_2" id="tab2">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Detail Barang</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab_pane_3" id="tab3">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Spesifikasi Barang</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab_pane_4" id="tab4">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Gambar Barang</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab_pane_5" id="tab5">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Pelanggan</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab_pane_6" id="tab6">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Supplier</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
					</div>

					<div class="card-body">
						<form action="{{ route('Product.store') }}" method="POST" id="form_add" enctype="multipart/form-data" autocomplete="off">
						{{ csrf_field() }}

						<div class="tab-content">
							<div class="tab-pane fade show active" id="tab_pane_1" role="tabpanel" aria-labelledby="tab_pane_1">
								<div class="row">
									<div class="col-md-6">
										<fieldset>
											<legend class="font-weight-semibold"></legend>
											<div class="form-group row">
											    <div class="col-lg-3">
                                                    <label>Kode Barang</label>
                                                </div>
                                                <div class="col-lg-9">
                                                    <input type="text" class="form-control form-control-solid " placeholder="Nomor akan dibuat otomatis oleh sistem" name="kode_item" id="kode_item" readonly>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-lg-3">
                                                    <label>Kategori Barang</label><label class="text-danger" data-toggle="tooltip"  title="Harus di isi" data-placement="top">**</label>
                                                </div>
                                                <div class="col-lg-9">
                                                    <select class="form-control select2 req" id="kategori_item" name="kategori_item">
                                                        <option label="Label"></option>
                                                        @foreach($dataCategory as $category)
                                                        <option value="{{$category->id}}">{{strtoupper($category->kode_kategori)}} - {{strtoupper($category->nama_kategori)}}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="form-text text-danger err" style="display:none;">*Harap pilih kategori barang terlebih dahulu!</span>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-lg-3">
                                                    <label>Merk Barang</label><label class="text-danger" data-toggle="tooltip"  title="Harus di isi" data-placement="top">**</label>
                                                </div>
                                                <div class="col-lg-9">
                                                    <select class="form-control select2 req" data-placeholder="Pilih Merk Barang" id="merk_item" name="merk_item">
                                                        <option label="Label"></option>
                                                        @foreach($dataBrand as $brand)
                                                        <option value="{{$brand->id}}">{{strtoupper($brand->nama_merk)}} - {{strtoupper($brand->keterangan_merk)}}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="form-text text-danger err" style="display:none;">*Harap pilih merk barang terlebih dahulu!</span>
                                                </div>
                                            </div>

                                            {{-- <div class="form-group row">
                                                <div class="col-lg-3">
                                                    <label>Satuan Barang</label><label class="text-danger" data-toggle="tooltip"  title="Harus di isi" data-placement="top">**</label>
                                                </div>
                                                <div class="col-lg-9">
                                                    <select class="form-control select2 req" id="satuan_item" name="satuan_item" data-width="100%">
                                                        <option label="Label"></option>
                                                        @foreach($dataUnit as $unit)
                                                        <option value="{{$unit->id}}">{{strtoupper($unit->kode_satuan)}} - {{strtoupper($unit->nama_satuan)}}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="form-text text-danger err" style="display:none;">*Harap pilih satuan barang terlebih dahulu!</span>
                                                </div>
                                            </div> --}}

                                            {{-- <div class="form-group row">
                                                <div class="col-lg-3">
                                                    <label>Stok Awal Barang</label>
                                                </div>
                                                <div class="col-lg-9">
                                                    <input type="text" id="stokAwalMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right" placeholder="Masukkan Stok Awal Barang">
        											<input type="hidden" id="stokAwal" name="stok_awal" class="form-control text-right">
                                                </div>
                                            </div> --}}
										</fieldset>
									</div>

									<div class="col-md-6">
										<fieldset>
											<legend class="font-weight-semibold"></legend>

											<div class="form-group row">
                                                <div class="col-lg-3">
                                                    <label>Nama Barang</label><label class="text-danger" data-toggle="tooltip"  title="Harus di isi" data-placement="top">**</label>
                                                </div>
                                                <div class="col-lg-9">
                                                    <input type="text" class="form-control req" placeholder="Masukkan Nama Barang" name="nama_item" id="nama_item">
                                                    <span class="form-text text-danger err" style="display:none;">*Harap isi nama item terlebih dahulu!</span>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-lg-3">
                                                    <label>Jenis Barang</label><label class="text-danger" data-toggle="tooltip"  title="Harus di isi" data-placement="top">**</label>
                                                </div>
                                                <div class="col-lg-9">
                                                    <select class="form-control select2 req" id="jenis_item" name="jenis_item">
                                                        <option label="Label"></option>
                                                        <option value="standard">Standard</option>
                                                        <option value="custom">Custom</option>
                                                        <option value="cetak">Tambahan (tidak tampil qty & harga)</option>
                                                    </select>
                                                    <span class="form-text text-danger err" style="display:none;">*Harap pilih Jenis Item terlebih dahulu!</span>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label">Keterangan Item</label>
                                                <div class="col-lg-9">
                                                    <textarea class="form-control" name="keterangan_item_txt" id="keterangan_item_txt" style="resize:none;"></textarea>
                                                </div>
                                            </div>

										</fieldset>
									</div>
								</div>
							</div>

                            <div class="tab-pane fade" id="tab_pane_2" role="tabpanel" aria-labelledby="tab_pane_2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <fieldset>
                                            <legend class="font-weight-semibold"></legend>

                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label">Satuan Item</label>
                                                <div class="col-lg-9">
                                                    <select class="form-control select2 reqSatuan" id="satuan_item" name="satuan_item" data-width="100%">
                                                        <option label="Label"></option>
                                                        @foreach($dataUnit as $unit)
                                                        <option value="{{$unit->id}}">{{strtoupper($unit->kode_satuan)}} - {{strtoupper($unit->nama_satuan)}}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="form-text text-danger errSatuan" style="display:none;">*Harap pilih Satuan item terlebih dahulu!</span>
                                                </div>
                                            </div>

                                            <div class="form-group row mb-0">
                                                <label class="col-lg-2 offset-lg-1 text-center col-form-label label-inline">Panjang<br>(mm)</label>
                                                <label class="col-lg-2 offset-lg-2 text-center col-form-label label-inline">Lebar<br>(mm)</label>
                                                <label class="col-lg-2 offset-lg-2 text-center col-form-label label-inline">Tinggi<br>(mm)</label>
                                            </div>

                                            <div class="form-group row">
                                                <input type="text" id="panjang_itemMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control col-lg-2 text-right offset-lg-1 pltbItem" value="0" >
        										<input type="hidden" name="panjang_item" id="panjang_item" value="0" class="form-control text-right">
                                                <input type="text" id="lebar_itemMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control col-lg-2 text-right offset-lg-2 pltbItem" value="0" >
        										<input type="hidden" name="lebar_item" id="lebar_item" value="0" class="form-control text-right">
                                                <input type="text" id="tinggi_itemMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control col-lg-2 text-right offset-lg-2 pltbItem" value="0" >
        										<input type="hidden" name="tinggi_item" id="tinggi_item" value="0" class="form-control text-right">
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label">Berat Barang (Gr)</label>
                                                <div class="col-lg-9">
                                                    <input type="text" id="berat_itemMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right" value="0" >
        										    <input type="hidden" name="berat_item" id="berat_item" value="0" class="form-control text-right">
                                                    <span class="form-text text-danger errTab2" style="display:none;">*Harap masukkan berat item terlebih dahulu!</span>
                                                </div>
                                            </div>

                                            <div class="example mb-10">
												<div class="example-preview">
													<h2>Dimensi Packing (Dus)</h2>
													<div class="form-group row mt-3">
													    <div class="col-4">
													        <label class="label-inline">Panjang Dus (mm)</label>
													        <input type="text" id="panjang_dusMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control pltbItem" value="0" >
											                <input type="hidden" name="panjang_dus" id="panjang_dus" value="0" class="form-control text-right">
												        </div>
                                                        <div class="col-4">
													        <label class="label-inline">Lebar Dus (mm)</label>
													        <input type="text" id="lebar_dusMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control pltbItem" value="0" >
											                <input type="hidden" name="lebar_dus" id="lebar_dus" value="0" class="form-control text-right">
												        </div>
												        <div class="col-4">
													        <label class="label-inline">Tinggi Dus (mm)</label>
													        <input type="text" id="tinggi_dusMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control pltbItem" value="0" >
										                	<input type="hidden" name="tinggi_dus" id="tinggi_dus" value="0" class="form-control text-right">
												        </div>
                                                    </div>
                                                    <div class="form-group row mt-5">
                                                        <div class="col-lg-3 col-form-label">
                                                            <label>Berat Packing / Dus (Gr)</label>
                                                        </div>
                                                        <div class="col-lg-9">
                                                            <input type="text" id="berat_dusMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control pltbItem" value="0" >
										                   	<input type="hidden" name="berat_dus" id="berat_dus" value="0" class="form-control text-right">
                                                            <span class="form-text text-danger errTab2" style="display:none;">*Harap masukkan berat item terlebih dahulu!</span>
                                                        </div>
                                                    </div>
                                                    {{-- <div class="form-group row mt-5">
                                                        <div class="col-lg-3 col-form-label">
                                                            <label>Qty Isi per Dus</label>
                                                        </div>
                                                        <div class="col-lg-9">
                                                            <input type="text" id="qty_per_dusMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control pltbItem" value="0" >
											                <input type="hidden" name="qty_per_dus" id="qty_per_dus" value="0" class="form-control text-right">
                                                            <span class="form-text text-danger errTab2" style="display:none;">*Harap masukkan berat item terlebih dahulu!</span>
                                                        </div>
                                                    </div> --}}
												</div>
											</div>

                                        </fieldset>
                                    </div>
                                    <div class="col-md-6">
                                        <fieldset>
                                            <legend class="font-weight-semibold"></legend>

                                            {{-- <div class="form-group row">
                                                <label class="col-lg-3 col-form-label">Stok Awal Item</label>
                                                <div class="col-lg-9">
                                                    <input type="text" id="stokAwalMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right" placeholder="Masukkan Stok Awal Item">
        											<input type="hidden" id="stokAwal" name="stok_awal" class="form-control text-right">
                                                </div>
                                            </div> --}}

                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label">Stok Minimum Item</label>
                                                <div class="col-lg-9">
                                                    <input type="text" id="stok_minimumMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right" placeholder="Masukkan Stok Minimum Item">
        											<input type="hidden" name="stok_minimum" id="stok_minimum" class="form-control text-right stok reqSatuan">
                                                    <span class="form-text text-danger errSatuan" style="display:none;">*Harap masukkan stok minimum terlebih dahulu!</span>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label">Stok Maksimum Item</label>
                                                <div class="col-lg-9">
                                                    <input type="text" id="stok_maksimumMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right" placeholder="Masukkan Stok Maksimum Item">
        											<input type="hidden" name="stok_maksimum" id="stok_maksimum" class="form-control text-right stok reqSatuan">
                                                    <span class="form-text text-danger errSatuan" style="display:none;">*Harap masukkan stok maksimum terlebih dahulu!</span>
                                                </div>
                                            </div>

                                            <div class="form-group row {{ $hakAksesHargaBeli === null ? 'd-none' : '' }}">
                                                <label class="col-lg-3 col-form-label">Harga Beli Item</label>
                                                <div class="col-lg-9">
                                                    <input type="text" id="harga_beliMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right" value="0" placeholder="Masukkan Harga Beli Item">
        											<input type="hidden" name="harga_beli" id="harga_beli" value="0" class="form-control text-right reqHarga">
                                                    <span class="form-text text-danger errHarga" style="display:none;">*Harap isi harga beli terlebih dahulu!</span>
                                                </div>
                                            </div>

                                            <div class="form-group row {{ $hakAksesHargaJual === null ? 'd-none' : '' }}">
                                                <label class="col-lg-3 col-form-label">Harga Jual Item</label>
                                                <div class="col-lg-9">
                                                    <input type="text" id="harga_jualMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right" value="0" placeholder="Masukkan Harga Jual Item">
        											<input type="hidden" name="harga_jual" id="harga_jual" value="0" class="form-control text-right reqHarga">
                                                    <span class="form-text text-danger errHarga" style="display:none;">*Harap isi harga jual terlebih dahulu!</span>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label"></label>
                                                <div class="col-lg-9">
                                                    <button type="button" class="btn btn sm btn-primary font-weight-bold" id="btnAddItem">Tambah Satuan</button>
                                                </div>
                                            </div>

                                        </fieldset>
                                    </div>
                                </div>

                                <!--begin::Search Form-->
                                <div class="mb-7">
                                    <div class="row align-items-center">
                                        <div class="col-lg-6">
                                            <div class="row align-items-center">
                                                <div class="col-md-4 my-2 my-md-0">
                                                    <div class="input-icon">
                                                        <input type="text" class="form-control" placeholder="Search..." id="table_satuan_search_query"/>
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
                                <!--begin: Datatable-->

                                <div class="datatable datatable-bordered datatable-head-custom" id="table_satuan"></div>

                                <!--end: Datatable-->

                                <div class="row">
                                    <div class="col-md-12">
                                        <fieldset>
                                            <legend class="font-weight-semibold"></legend>

                                            <div class="form-group row">
                                                <div class="col-lg-12 text-center">
                                                    <span class="form-text text-danger errTbl" id="errTbl" style="display:none;">*Harap tambahkan Minimum 1 Satuan Produk terlebih dahulu!</span>
                                                </div>
                                            </div>

                                        </fieldset>
                                    </div>
                                </div>

                            </div>

                            <div class="tab-pane fade" id="tab_pane_3" role="tabpanel" aria-labelledby="tab_pane_3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <fieldset>
                                            <legend class="font-weight-semibold"></legend>
                                            <!--begin: Datatable-->

                                            <div class="datatable datatable-bordered datatable-head-custom" id="table_spek"></div>

                                            <!--end: Datatable-->

                                        </fieldset>
                                    </div>
                                    <div class="col-md-6">
                                        <fieldset>
                                            <legend class="font-weight-semibold"></legend>

                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label">Spesifikasi Item</label>
                                                <div class="col-lg-9">
                                                    <select class="form-control select2 reqSpek" id="spesifikasi" name="spesifikasi" data-width="100%">
                                                        <option label="Label"></option>
                                                        @foreach($dataSpek as $spek)
                                                        <option value="{{$spek->id}}">{{strtoupper($spek->nama_spesifikasi)}}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="form-text text-danger errSpec" style="display:none;">*Harap pilih spesifikasi item terlebih dahulu!</span>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label">Nilai Spesifikasi</label>
                                                <div class="col-lg-9">
												<input type="text" name="nilai_spek" id="nilai_spek" class="form-control reqSpek">
                                                    <span class="form-text text-danger errSpec" style="display:none;">*Harap masukkan nilai spesifikasi terlebih dahulu!</span>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label"></label>
                                                <div class="col-lg-9">
                                                    <button type="button" class="btn btn sm btn-primary font-weight-bold" id="btnAddSpec">Tambah Spesifikasi</button>
                                                </div>
                                            </div>

                                        </fieldset>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="tab_pane_4" role="tabpanel" aria-labelledby="tab_pane_4">
                                <div class="form-group row">
                                    <label class="col-xl-3 col-lg-3 col-form-label text-right">Gambar Barang</label>
                                    <div class="col-lg-9 col-xl-6">
                                        <div class="image-input image-input-outline" id="image_product">
                                            <div class="image-input-wrapper" style="background-image: url({{asset('images/img-preview.jpg')}})"></div>
                                            <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Input Gambar">
                                                <i class="fa fa-pen icon-sm text-muted"></i>
                                                <input type="file" name="img_item" accept=".png, .jpg, .jpeg" />
                                                <input type="hidden" name="img_item_remove" />
                                            </label>
                                            <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="Hapus Gambar">
                                                <i class="ki ki-bold-close icon-xs text-muted"></i>
                                            </span>
                                        </div>
                                        <span class="form-text text-muted">Allowed file types: png, jpg, jpeg.</span>
                                    </div>
                                </div>
							</div>

                            <div class="tab-pane fade" id="tab_pane_5" role="tabpanel" aria-labelledby="tab_pane_5">
								<!--begin: Search Form-->
                                <!--begin::Search Form-->
                                <div class="mb-7">
                                    <div class="row align-items-center">
                                        <div class="col-lg-6">
                                            <div class="row align-items-center">
                                                <div class="col-md-4 my-2 my-md-0">
                                                    <div class="input-icon">
                                                        <input type="text" class="form-control" placeholder="Search..." id="list_item_cust_search_query"/>
                                                        <span>
                                                            <i class="flaticon2-search-1 text-muted"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 align-items-right" align="right">
                                            <div class="row align-items-right">
                                                <div class="col-md-11 my-md-0 align-items-right">
                                                    <button type="button" class="btn btn-primary font-weight-bold mr-2" data-toggle="modal" data-target="#modal_list_cust"> Tambah Customer</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Search Form-->
                                <!--end: Search Form-->
                                <!--begin: Datatable-->

                                <div class="datatable datatable-bordered datatable-head-custom" id="list_item_cust"></div>

                                <!--end: Datatable-->

								<div class="row">
									<div class="col-md-12">
										<fieldset>
											<legend class="font-weight-semibold"></legend>

											<div class="form-group row">
												<div class="col-lg-12 text-center">
													<span class="form-text text-danger errTbl" id="errTbl" style="display:none;">*Harap tambahkan Minimum 1 Alamat terlebih dahulu!</span>
												</div>
											</div>

										</fieldset>
									</div>
								</div>
							</div>

                            <div class="tab-pane fade" id="tab_pane_6" role="tabpanel" aria-labelledby="tab_pane_6">
								<!--begin: Search Form-->
                                <!--begin::Search Form-->
                                <div class="mb-7">
                                    <div class="row align-items-center">
                                        <div class="col-lg-6">
                                            <div class="row align-items-center">
                                                <div class="col-md-4 my-2 my-md-0">
                                                    <div class="input-icon">
                                                        <input type="text" class="form-control" placeholder="Search..." id="list_item_supp_search_query"/>
                                                        <span>
                                                            <i class="flaticon2-search-1 text-muted"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 align-items-right" align="right">
                                            <div class="row align-items-right">
                                                <div class="col-md-11 my-md-0 align-items-right">
                                                    <button type="button" class="btn btn-primary font-weight-bold mr-2" data-toggle="modal" data-target="#modal_list_supp"> Tambah Supplier</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Search Form-->
                                <!--end: Search Form-->
                                <!--begin: Datatable-->

                                <div class="datatable datatable-bordered datatable-head-custom" id="list_item_supp"></div>

                                <!--end: Datatable-->

								<div class="row">
									<div class="col-md-12">
										<fieldset>
											<legend class="font-weight-semibold"></legend>

											<div class="form-group row">
												<div class="col-lg-12 text-center">
													<span class="form-text text-danger errTbl" id="errTbl" style="display:none;">*Harap tambahkan Minimum 1 Alamat terlebih dahulu!</span>
												</div>
											</div>

										</fieldset>
									</div>
								</div>
							</div>
						</div>

						<div class="card-footer bg-white d-sm-flex justify-content-sm-between align-items-sm-center">
							<div class="btn-group">
                                <button type="button" style="display: none;" id="btnModalEditSpec" data-toggle="modal" data-target="#modal_form_edit_spec"></button>
                                <button type="button" class="btn btn-light-danger font-weight-bold mr-2" id="cancel">Keluar <i class="flaticon2-cancel icon-sm"></i></button>
                            </div>

                            <div class="mt-2 mt-sm-0">
                                <button type="submit" class="btn btn-light-primary font-weight-bold mr-2"> Simpan <i class="flaticon-paper-plane-1"></i></button>
                            </div>
						</div>
						</form>
					</div>
                    <!-- Horizontal form edit item-->
                    <div id="modal_form_edit_item" class="modal fade">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">

                                    <h5 class="modal-title">Ubah Detail Satuan Item</h5>
                                </div>
                                <div class="modal-body">
                                    <form >
                                        <div class="row">
                                            <div class="col-md-6">
                                                <fieldset>
                                                    <legend class="font-weight-semibold"></legend>

                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">Satuan Item</label>
                                                        <div class="col-lg-9">
                                                            <input type="hidden" id="id_detail" class="form-control text-right">
                                                            <input type="hidden" id="id_satuanEdit" class="form-control text-right">
                                                            <input type="text" id="nama_satuanEdit" class="form-control"  readonly>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row mb-0">
                                                        <label class="col-lg-2 offset-lg-1 text-center col-form-label label-inline">Panjang<br>(mm)</label>
                                                        <label class="col-lg-2 offset-lg-2 text-center col-form-label label-inline">Lebar<br>(mm)</label>
                                                        <label class="col-lg-2 offset-lg-2 text-center col-form-label label-inline">Tinggi<br>(mm)</label>
                                                    </div>

                                                    <div class="form-group row">
                                                        <input type="text" id="panjang_itemEditMaskEdit" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control col-lg-2 text-right offset-lg-1 pltbItem" value="0" >
                                                        <input type="hidden" name="panjang_itemEdit" id="panjang_itemEdit" value="0" class="form-control text-right">
                                                        <input type="text" id="lebar_itemEditMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control col-lg-2 text-right offset-lg-2 pltbItem" value="0" >
                                                        <input type="hidden" name="lebar_itemEdit" id="lebar_itemEdit" value="0" class="form-control text-right">
                                                        <input type="text" id="tinggi_itemEditMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control col-lg-2 text-right offset-lg-2 pltbItem" value="0" >
                                                        <input type="hidden" name="tinggi_itemEdit" id="tinggi_itemEdit" value="0" class="form-control text-right">
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">Berat Barang (Gr)</label>
                                                        <div class="col-lg-9">
                                                            <input type="text" id="berat_itemEditMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right" value="0" >
                                                            <input type="hidden" name="berat_itemEdit" id="berat_itemEdit" value="0" class="form-control text-right">
                                                            <span class="form-text text-danger errTab2" style="display:none;">*Harap masukkan berat item terlebih dahulu!</span>
                                                        </div>
                                                    </div>

                                                    <div class="example mb-10">
                                                        <div class="example-preview">
                                                            <h2>Dimensi Packing (Dus)</h2>
                                                            <div class="form-group row mt-3">
                                                                <div class="col-4">
                                                                    <label class="label-inline">Panjang Dus (mm)</label>
                                                                    <input type="text" id="panjang_dusEditMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control pltbItem" value="0" >
                                                                    <input type="hidden" name="panjang_dusEdit" id="panjang_dusEdit" value="0" class="form-control text-right">
                                                                </div>
                                                                <div class="col-4">
                                                                    <label class="label-inline">Lebar Dus (mm)</label>
                                                                    <input type="text" id="lebar_dusEditMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control pltbItem" value="0" >
                                                                    <input type="hidden" name="lebar_dusEdit" id="lebar_dusEdit" value="0" class="form-control text-right">
                                                                </div>
                                                                <div class="col-4">
                                                                    <label class="label-inline">Tinggi Dus (mm)</label>
                                                                    <input type="text" id="tinggi_dusEditMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control pltbItem" value="0" >
                                                                    <input type="hidden" name="tinggi_dusEdit" id="tinggi_dusEdit" value="0" class="form-control text-right">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row mt-5">
                                                                <div class="col-lg-3 col-form-label">
                                                                    <label>Berat Packing / Dus (Gr)</label>
                                                                </div>
                                                                <div class="col-lg-9">
                                                                    <input type="text" id="berat_dusEditMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control pltbItem" value="0" >
                                                                       <input type="hidden" name="berat_dusEdit" id="berat_dusEdit" value="0" class="form-control text-right">
                                                                    <span class="form-text text-danger errTab2" style="display:none;">*Harap masukkan berat item terlebih dahulu!</span>
                                                                </div>
                                                            </div>
                                                            {{-- <div class="form-group row mt-5">
                                                                <div class="col-lg-3 col-form-label">
                                                                    <label>Qty Isi per Dus</label>
                                                                </div>
                                                                <div class="col-lg-9">
                                                                    <input type="text" id="qty_per_dusEditMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control pltbItem" value="0" >
                                                                    <input type="hidden" name="qty_per_dusEdit" id="qty_per_dusEdit" value="0" class="form-control text-right">
                                                                    <span class="form-text text-danger errTab2" style="display:none;">*Harap masukkan berat item terlebih dahulu!</span>
                                                                </div>
                                                            </div> --}}
                                                        </div>
                                                    </div>

                                                </fieldset>
                                            </div>
                                            <div class="col-md-6">
                                                <fieldset>
                                                    <legend class="font-weight-semibold"></legend>

                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">Stok Minimum Item</label>
                                                        <div class="col-lg-9">
                                                            <input type="text" id="stok_minimumEditMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right" placeholder="Masukkan Stok Minimum Item">
                                                            <input type="hidden" name="stok_minimumEdit" id="stok_minimumEdit" class="form-control text-right stok reqSatuanEdit">
                                                            <span class="form-text text-danger errSatuanEdit" style="display:none;">*Harap masukkan stok minimum terlebih dahulu!</span>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">Stok Maksimum Item</label>
                                                        <div class="col-lg-9">
                                                            <input type="text" id="stok_maksimumEditMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right" placeholder="Masukkan Stok Maksimum Item">
                                                            <input type="hidden" name="stok_maksimumEdit" id="stok_maksimumEdit" class="form-control text-right stok reqSatuanEdit">
                                                            <span class="form-text text-danger errSatuanEdit" style="display:none;">*Harap masukkan stok maksimum terlebih dahulu!</span>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row {{ $hakAksesHargaBeli === null ? 'd-none' : '' }}">
                                                        <label class="col-lg-3 col-form-label">Harga Beli Item</label>
                                                        <div class="col-lg-9">
                                                            <input type="text" id="harga_beliEditMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right" value="0" placeholder="Masukkan Harga Beli Item">
                                                            <input type="hidden" name="harga_beliEdit" id="harga_beliEdit" value="0" class="form-control text-right reqHarga">
                                                            <span class="form-text text-danger errHarga" style="display:none;">*Harap isi harga beli terlebih dahulu!</span>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row {{ $hakAksesHargaJual === null ? 'd-none' : '' }}">
                                                        <label class="col-lg-3 col-form-label">Harga Jual Item</label>
                                                        <div class="col-lg-9">
                                                            <input type="text" id="harga_jualEditMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right" value="0" placeholder="Masukkan Harga Jual Item">
                                                            <input type="hidden" name="harga_jualEdit" id="harga_jualEdit" value="0" class="form-control text-right reqHarga">
                                                            <span class="form-text text-danger errHarga" style="display:none;">*Harap isi harga jual terlebih dahulu!</span>
                                                        </div>
                                                    </div>

                                                </fieldset>
                                            </div>
                                        </div>
                                    </form>

                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" id="btnEditItem" data-dismiss="modal">Simpan</button>
                                    <button type="button" class="btn btn-link" data-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /horizontal form edit item -->

                    <!-- Horizontal form edit item-->
                    <div id="modal_form_edit_spec" class="modal fade">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">

                                    <h5 class="modal-title">Ubah Spesifikasi Item</h5>
                                </div>
                                <div class="modal-body">
                                    <form >
                                        <table class="table display" id="list_edit_item" width="100%">
                                            <thead>
                                                <tr>
                                                    <th align="center" style="text-align:center;display: none;">Id</th>
                                                    <th align="center" style="text-align:center;">Spesifikasi</th>
                                                    <th align="center" style="text-align:center;">Nilai</th>
                                                </tr>
                                            </thead>
                                            <tbody id="detil_edit_spec">

                                            </tbody>
                                        </table>
                                    </form>

                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" id="btnEditSpec" data-dismiss="modal">Simpan</button>
                                    <button type="button" class="btn btn-link" data-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /horizontal form edit item -->

                    <!-- Modal form list customer -->
                    <div id="modal_list_cust" class="modal fade">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">

                                    <h5 class="modal-title text-white">Daftar Customer</h5>
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
                                                                <input type="text" class="form-control" placeholder="Search..." id="list_cust_search_query"/>
                                                                <span>
                                                                    <i class="flaticon2-search-1 text-muted"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 my-2 my-md-0">
                                                        <div class="align-items-center">
                                                            <label class="mr-3 mb-0 d-none d-md-block">Kategori :</label>
                                                            <select class="form-control select2" id="list_cust_search_kategori">
                                                                <option value="">All</option>
                                                                @foreach($kategoriCustomer as $rowKategori)
                                                                <option value="{{$rowKategori->id}}">{{ucwords($rowKategori->nama_kategori)}}</option>
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

                                    <div class="datatable datatable-bordered datatable-head-custom" id="list_cust"></div>

                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-link" data-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /form list customer -->

                    <!-- Modal form list supplier -->
                    <div id="modal_list_supp" class="modal fade">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">

                                    <h5 class="modal-title text-white">Daftar Supplier</h5>
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
                                                                <input type="text" class="form-control" placeholder="Search..." id="list_supp_search_query"/>
                                                                <span>
                                                                    <i class="flaticon2-search-1 text-muted"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 my-2 my-md-0">
                                                        <div class="align-items-center">
                                                            <label class="mr-3 mb-0 d-none d-md-block">Kategori :</label>
                                                            <select class="form-control select2" id="list_supp_search_kategori">
                                                                <option value="">All</option>
                                                                @foreach($kategoriSupplier as $rowKategoriS)
                                                                <option value="{{$rowKategoriS->id}}">{{ucwords($rowKategoriS->nama_kategori)}}</option>
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

                                    <div class="datatable datatable-bordered datatable-head-custom" id="list_supp"></div>

                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-link" data-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /form list barang -->
				</div>
			<!-- /content area -->
@endsection
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            var imgItem = new KTImageInput('image_product');

            $('#kategori_item').select2({
                placeholder: "Pilih Kategori Barang",
                allowClear: true
            });

            $('#jenis_item').select2({
                placeholder: "Pilih Jenis Barang",
                allowClear: true
            });

            $('#merk_item').select2({
                placeholder: "Pilih Merk Barang",
                allowClear: true
            });

            $('#satuan_item').select2({
                placeholder: "Pilih Satuan Barang",
                allowClear: true
            });

            $('#spesifikasi').select2({
                placeholder: "Pilih Spesifikasi",
                allowClear: true
            });

            $('#list_cust_search_kategori').select2({
                allowClear: true,
                placeholder: "Silahkan pilih kategori customer"
            });

            $('#list_supp_search_kategori').select2({
                allowClear: true,
                placeholder: "Silahkan pilih kategori supplier"
            });

            $("#stokAwalMask").autoNumeric('init');
            $("#harga_beliMask").autoNumeric('init');
            $("#harga_jualMask").autoNumeric('init');
            $("#stok_minimumMask").autoNumeric('init');
            $("#stok_maksimumMask").autoNumeric('init');
            $("#panjang_itemMask").autoNumeric('init');
            $("#lebar_itemMask").autoNumeric('init');
            $("#tinggi_itemMask").autoNumeric('init');
            $("#berat_itemMask").autoNumeric('init');
            $("#panjang_dusMask").autoNumeric('init');
            $("#lebar_dusMask").autoNumeric('init');
            $("#tinggi_dusMask").autoNumeric('init');
            $("#berat_dusMask").autoNumeric('init');
            // $("#qty_per_dusMask").autoNumeric('init');

            $("#harga_beliEditMask").autoNumeric('init');
            $("#harga_jualEditMask").autoNumeric('init');
            $("#stok_minimumEditMask").autoNumeric('init');
            $("#stok_maksimumEditMask").autoNumeric('init');
            $("#panjang_itemEditMask").autoNumeric('init');
            $("#lebar_itemEditMask").autoNumeric('init');
            $("#tinggi_itemEditMask").autoNumeric('init');
            $("#berat_itemEditMask").autoNumeric('init');

            $("#panjang_dusEditMask").autoNumeric('init');
            $("#lebar_dusEditMask").autoNumeric('init');
            $("#tinggi_dusEditMask").autoNumeric('init');
            $("#berat_dusEditMask").autoNumeric('init');
            // $("#qty_per_dusEditMask").autoNumeric('init');
        });

        $("#stokAwalMask").on('change', function() {
            $("#stokAwal").val($("#stokAwalMask").autoNumeric("get"));
        });

        $("#harga_beliMask").on('change', function() {
            $("#harga_beli").val($("#harga_beliMask").autoNumeric("get"));
        });

        $("#harga_jualMask").on('change', function() {
            $("#harga_jual").val($("#harga_jualMask").autoNumeric("get"));
        });

        $("#stok_minimumMask").on('change', function() {
            $("#stok_minimum").val($("#stok_minimumMask").autoNumeric("get"));
        });

        $("#stok_maksimumMask").on('change', function() {
            $("#stok_maksimum").val($("#stok_maksimumMask").autoNumeric("get"));
        });

        $("#panjang_itemMask").on('change', function() {
            $("#panjang_item").val($("#panjang_itemMask").autoNumeric("get"));
        });

        $("#lebar_itemMask").on('change', function() {
            $("#lebar_item").val($("#lebar_itemMask").autoNumeric("get"));
        });

        $("#tinggi_itemMask").on('change', function() {
            $("#tinggi_item").val($("#tinggi_itemMask").autoNumeric("get"));
        });

        $("#berat_itemMask").on('change', function() {
            $("#berat_item").val($("#berat_itemMask").autoNumeric("get"));
        });

        $("#panjang_dusMask").on('change', function() {
            $("#panjang_dus").val($("#panjang_dusMask").autoNumeric("get"));
        });

        $("#lebar_dusMask").on('change', function() {
            $("#lebar_dus").val($("#lebar_dusMask").autoNumeric("get"));
        });

        $("#tinggi_dusMask").on('change', function() {
            $("#tinggi_dus").val($("#tinggi_dusMask").autoNumeric("get"));
        });

        $("#berat_dusMask").on('change', function() {
            $("#berat_dus").val($("#berat_dusMask").autoNumeric("get"));
        });

        // $("#qty_per_dusMask").on('change', function() {
        //     $("#qty_per_dus").val($("#qty_per_dusMask").autoNumeric("get"));
        // });

        $("#panjang_dusEditMask").on('change', function() {
            $("#panjang_dusEdit").val($("#panjang_dusEditMask").autoNumeric("get"));
        });

        $("#lebar_dusEditMask").on('change', function() {
            $("#lebar_dusEdit").val($("#lebar_dusEditMask").autoNumeric("get"));
        });

        $("#tinggi_dusEditMask").on('change', function() {
            $("#tinggi_dusEdit").val($("#tinggi_dusEditMask").autoNumeric("get"));
        });

        $("#berat_dusEditMask").on('change', function() {
            $("#berat_dusEdit").val($("#berat_dusEditMask").autoNumeric("get"));
        });

        // $("#qty_per_dusEditMask").on('change', function() {
        //     $("#qty_per_dusEdit").val($("#qty_per_dusEditMask").autoNumeric("get"));
        // });

        $("#harga_beliEditMask").on('change', function() {
            $("#harga_beliEdit").val($("#harga_beliEditMask").autoNumeric("get"));
        });

        $("#harga_jualEditMask").on('change', function() {
            $("#harga_jualEdit").val($("#harga_jualEditMask").autoNumeric("get"));
        });

        $("#stok_minimumEditMask").on('change', function() {
            $("#stok_minimumEdit").val($("#stok_minimumEditMask").autoNumeric("get"));
        });

        $("#stok_maksimumEditMask").on('change', function() {
            $("#stok_maksimumEdit").val($("#stok_maksimumEditMask").autoNumeric("get"));
        });

        $("#panjang_itemEditMask").on('change', function() {
            $("#panjang_itemEdit").val($("#panjang_itemEditMask").autoNumeric("get"));
        });

        $("#lebar_itemEditMask").on('change', function() {
            $("#lebar_itemEdit").val($("#lebar_itemEditMask").autoNumeric("get"));
        });

        $("#tinggi_itemEditMask").on('change', function() {
            $("#tinggi_itemEdit").val($("#tinggi_itemEditMask").autoNumeric("get"));
        });

        $("#berat_itemEditMask").on('change', function() {
            $("#berat_itemEdit").val($("#berat_itemEditMask").autoNumeric("get"));
        });

		function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

		$("#cancel").on('click', function(e) {
            Swal.fire({
                title: "Batal?",
                text: "Apakah anda ingin membatalkan penambahan Barang?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    window.location.href = "{{ url('/Product') }}";
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
	    });

		function validasiangka(evt) {
          var charCode = (evt.which) ? evt.which : event.keyCode
           if (charCode > 31 && (charCode < 48 || charCode > 57))

            return false;
          return true;
        }

        $(document).ready(function(){
			$(".stok").change(function(){
			    if($(this).val() < 0){
			      alert("Jumlah Stok Tidak Dapat Kurang dari 0!");
			      $(this).val(0);
			  	}
                else {
                    if (parseFloat($("#stok_minimum").val()) > parseFloat($("#stok_maksimum").val())) {
                        alert("Jumlah Stok Maksimum Tidak Dapat Kurang dari Stok Minimum!");
                        $("#stok_maksimum").val("");
                    }
                }
			});
		});

        $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
            var tabName = e.target.id;
            var prevTab = e.relatedTarget.id;
            switch (tabName) {

                case "tab1" : {
                    // if (prevTab == "tab2") {
                    //     $(".reqTab2").each(function(){
                    //         if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                    //             $(this).closest('.form-group').find('.errTab2').show();
                    //             e.preventDefault();
                    //         }
                    //         else {
                    //             $(this).closest('.form-group').find('.errTab2').hide();
                    //         }
                    //     });
                    // }
                    break;
                }

                case "tab2" : {
                    $(".req").each(function(){
                        if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                            $(this).closest('.form-group').find('.err').show();
                            e.preventDefault();
                        }
                        else {
                            $(this).closest('.form-group').find('.err').hide();
                        }
                    });
                    break;
                }

                case "tab3" : {
                    if (prevTab == "tab1") {
                        $(".req").each(function(){
                            if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                                $(this).closest('.form-group').find('.err').show();
                                e.preventDefault();
                            }
                            else {
                                $(this).closest('.form-group').find('.err').hide();
                            }
                        });
                    }
                    $(".reqTab2").each(function(){
                        if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                            $(this).closest('.form-group').find('.errTab2').show();
                            e.preventDefault();
                        }
                        else {
                            $(this).closest('.form-group').find('.errTab2').hide();
                        }
                    });
                    break;
                }

                case "tab4" : {
                    if (prevTab == "tab1") {
                        $(".req").each(function(){
                            if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                                $(this).closest('.form-group').find('.err').show();
                                e.preventDefault();
                            }
                            else {
                                $(this).closest('.form-group').find('.err').hide();
                            }
                        });
                    }
                    $(".reqTab2").each(function(){
                        if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                            $(this).closest('.form-group').find('.errTab2').show();
                            e.preventDefault();
                        }
                        else {
                            $(this).closest('.form-group').find('.errTab2').hide();
                        }
                    });
                    break;
                }

                case "tab4" : {
                    if (prevTab == "tab1") {
                        $(".req").each(function(){
                            if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                                $(this).closest('.form-group').find('.err').show();
                                e.preventDefault();
                            }
                            else {
                                $(this).closest('.form-group').find('.err').hide();
                            }
                        });
                    }
                    $(".reqTab2").each(function(){
                        if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                            $(this).closest('.form-group').find('.errTab2').show();
                            e.preventDefault();
                        }
                        else {
                            $(this).closest('.form-group').find('.errTab2').hide();
                        }
                    });
                    break;
                }
            }
	    });

        $("#form_add").submit(function(e){
            e.preventDefault();
            var datatable = $('#table_satuan').KTDatatable();
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
                            $(".reqTab2").each(function(){
                                if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                                    $(this).closest('.form-group').find('.errTab2').show();
                                    $("#tab2").trigger('click');
                                    count = parseInt(count) + 1;
                                }
                                else {
                                    $(this).closest('.form-group').find('.errTab2').hide();

                                }
                            });
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
                    // Swal.fire(
                    //     "Cancelled",
                    //     "Your imaginary file is safe :)",
                    //     "error"
                    // )
                    e.preventDefault();
                }
            });
		});

        $(document).ready(function() {
            var datatable = $('#table_satuan').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Product/GetDetailSatuan',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            idProduct : ''
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
                    input: $('#table_satuan_search_query')
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
                        field: 'nama_satuan',
                        title: 'Satuan',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            return ucwords(row.nama_satuan + ' - ' + row.kode_satuan);
                        },
                    },
                    {
                        field: 'panjang_item',
                        title: 'Panjang',
                        width: 'auto',
                    },
                    {
                        field: 'lebar_item',
                        title: 'Lebar',
                        width: 'auto',
                    },
                    {
                        field: 'tinggi_item',
                        title: 'Tinggi',
                        width: 'auto',
                    },
                    {
                        field: 'berat_item',
                        title: 'Berat',
                        width: 'auto',
                    },
                    {
                        field: 'panjang_dus',
                        title: 'Panjang Dus',
                        width: 'auto',
                    },
                    {
                        field: 'lebar_dus',
                        title: 'Lebar Dus',
                        width: 'auto',
                    },
                    {
                        field: 'tinggi_dus',
                        title: 'Tinggi Dus',
                        width: 'auto',
                    },
                    {
                        field: 'berat_dus',
                        title: 'Berat Dus',
                        width: 'auto',
                    },
                    // {
                    //     field: 'qty_per_dus',
                    //     title: 'Qty per Dus',
                    //     width: 'auto',
                    // },
                    {
                        field: 'stok_minimum',
                        title: 'Stok Min',
                        width: 'auto',
                        autoHide: false,
                    },
                    {
                        field: 'stok_maksimum',
                        title: 'Stok Max',
                        width: 'auto',
                        autoHide: false,
                    },
                    {
                        field: 'harga_beli',
                        title: 'Harga Beli',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.harga_beli).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'harga_jual',
                        title: 'Harga Jual',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.harga_jual).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'default',
                        title: 'Satuan Dasar',
                        textAlign: 'center',
                        width: 'auto',
                        overflow: 'visible',
                        autoHide:false,
                        template: function(row) {
                            var txtCheckbox = "<div class='radio-list align-items-center'>";
                                txtCheckbox += "<label class='radio radio-lg'>";
                                if (row.default == "Y")
                                    txtCheckbox += "<input type='radio' class='text-center' onchange='setDefault("+row.id+");' value='"+row.id+"' name='satuanDefault' checked>";
                                else {
                                    txtCheckbox += "<input type='radio' class='text-center' onchange='setDefault("+row.id+");' value='"+row.id+"' name='satuanDefault'>";
                                }
                                txtCheckbox += "<span></span>";
                                txtCheckbox += "</label>";
                                txtCheckbox += "</div>";
                            return txtCheckbox;
                        },
                    },
                    {
                        field: 'flag_monitor',
                        title: 'Monitor?',
                        width: 'auto',
                        autoHide:false,
                        template: function(row) {
                            if (row.flag_monitor == "1") {
                                return "<div class='checkbox-inline align-items-center'><label class='checkbox checkbox-lg'><input type='checkbox' id='checkMonitor' onchange='setMonitor("+row.id+");' value='"+row.id+"' class='text-center checkMonitor' checked><span></span></label></div>";
                            }
                            else {
                                return "<div class='checkbox-inline align-items-center'><label class='checkbox checkbox-lg'><input type='checkbox' id='checkMonitor' onchange='setMonitor("+row.id+");' value='"+row.id+"' class='text-center checkMonitor'><span></span></label></div>";
                            }

                        },
                    },
                    {
                        field: 'actions',
                        title: 'Aksi',
                        width: 'auto',
                        textAlign: 'center',
                        overflow: 'visible',
                        autoHide:false,
                        template: function(row) {
                            var txtAction = "<a href='#' class='btn btn-sm btn-clean btn-icon edit' title='Ubah' onclick='editSatuan("+row.id+");return false;'>";
                                txtAction += "<i class='la la-edit'></i>";
                                txtAction += "</a>";
                                txtAction += "<a href='#' class='btn btn-sm btn-clean btn-icon' title='Hapus' onclick='deleteDetail("+row.id+");return false;'>";
                                txtAction += "<i class='la la-trash'></i>";
                                txtAction += "</a>";

                            return txtAction;
                        },
                    },
                ],
            });

            var hrgBeli = "{{json_encode($hakAksesHargaBeli)}}";
            var hrgJual = "{{json_encode($hakAksesHargaJual)}}";
            if (hrgBeli != null) {
                datatable.columns('harga_beli').visible(true);
            }
            else {
                datatable.columns('harga_beli').visible(false);
            }
            if (hrgJual != null) {
                datatable.columns('harga_jual').visible(true);
            }
            else {
                datatable.columns('harga_jual').visible(false);
            }
		});

        $("#btnAddItem").on('click', function(e) {
			var errCount = 0;

			$(".reqSatuan").each(function(){
				if($(this).val() == "" || $(this).children("option:selected").val() == ""){
				   	$(this).closest('.form-group').find('.errSatuan').show();
				  	errCount = errCount + 1;
				}
				else {
					$(this).closest('.form-group').find('.errSatuan').hide();
				}
			});

			if (errCount == 0) {
                Swal.fire({
                    title: "Tambah Satuan untuk Item?",
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
                            url: "/Product/StoreDetail",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idProduct : '',
                                id_satuan : $("#satuan_item").val(),
                                panjang : $("#panjang_item").val(),
                                lebar : $("#lebar_item").val(),
                                tinggi : $("#tinggi_item").val(),
                                berat : $("#berat_item").val(),
                                harga_beli : $("#harga_beli").val(),
                                harga_jual : $("#harga_jual").val(),
                                stok_min : $("#stok_minimum").val(),
                                stok_max : $("#stok_maksimum").val(),
                                panjangDus : $("#panjang_dus").val(),
                                lebarDus : $("#lebar_dus").val(),
                                tinggiDus : $("#tinggi_dus").val(),
                                beratDus : $("#berat_dus").val(),
                                // qtyPerDus : $("#qty_per_dus").val(),
                                mode : 'tambah'
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Data Berhasil disimpan!.",
                                        "success"
                                    )
                                    $("#satuan_item").val("").trigger("change");
                                    $("#panjang_itemMask").val(0).trigger("change");
                                    $("#lebar_itemMask").val(0).trigger("change");
                                    $("#tinggi_itemMask").val(0).trigger("change");
                                    $("#berat_itemMask").val(0).trigger("change");
                                    $("#harga_beliMask").val(0).trigger("change");
                                    $("#harga_jualMask").val(0).trigger("change");
                                    $("#stok_minimumMask").val(0).trigger("change");
                                    $("#stok_maksimumMask").val(0).trigger("change");
                                    $("#panjang_dusMask").val(0).trigger("change");
                                    $("#lebar_dusMask").val(0).trigger("change");
                                    $("#tinggi_dusMask").val(0).trigger("change");
                                    $("#berat_dusMask").val(0).trigger("change");
                                    // $("#qty_per_dusMask").val(0).trigger("change");
                                    var datatable = $('#table_satuan').KTDatatable();
                                        datatable.setDataSourceParam('idProduct', '');
                                        datatable.reload();
                                }
                                else if (result == "failDuplicate") {
                                    Swal.fire(
                                        "Gagal!",
                                        "Satuan ini sudah tersedia pada Item !",
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

        function editSatuan(id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/Product/EditDetail",
                method: 'POST',
                data: {
                    idDetail: id
                },
                success: function(result){
                    if (result != "") {
                        $("#nama_satuanEdit").val(result.kode_satuan.toUpperCase() + ' - ' + result.nama_satuan.toUpperCase());
                        $("#id_detail").val(result.id);
                        $("#id_satuanEdit").val(result.id_satuan);
                        $("#panjang_itemEditMask").val(result.panjang_item).trigger("change");
                        $("#lebar_itemEditMask").val(result.lebar_item).trigger("change");
                        $("#tinggi_itemEditMask").val(result.tinggi_item).trigger("change");
                        $("#berat_itemEditMask").val(result.berat_item).trigger("change");
                        $("#harga_beliEditMask").val(result.harga_beli).trigger("change");
                        $("#harga_jualEditMask").val(result.harga_jual).trigger("change");
                        $("#stok_minimumEditMask").val(result.stok_minimum).trigger("change");
                        $("#stok_maksimumEditMask").val(result.stok_maksimum).trigger("change");
                        $("#panjang_dusEditMask").val(result.panjang_dus).trigger("change");
                        $("#lebar_dusEditMask").val(result.lebar_dus).trigger("change");
                        $("#tinggi_dusEditMask").val(result.tinggi_dus).trigger("change");
                        $("#berat_dusEditMask").val(result.berat_dus).trigger("change");
                        // $("#qty_per_dusEditMask").val(result.qty_per_dus).trigger("change");

                        $('#modal_form_edit_item').modal('show');
                    }

                }
            });
        }

        function deleteDetail(id) {
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
                        url: "/Product/DeleteDetail",
                        method: 'POST',
                        data: {
                            idDetail: id
                        },
                        success: function(result){
                            if (result == "success") {
                                Swal.fire(
                                    "Sukses!",
                                    "Data Berhasil dihapus!.",
                                    "success"
                                )
                            }
                            else if (result == "failDefault") {
                                Swal.fire(
                                    "Gagal!",
                                    "Satuan ini tidak dapat dihapus karena diset sebagai satuan dasar !",
                                    "warning"
                                )
                            }
                        }
                    });
                    var datatable = $("#table_satuan").KTDatatable();
                    datatable.reload();
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

        $(document).ready(function() {
            var datatable = $('#table_spek').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Product/GetDetailSpec',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            idProduct : ''
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
                    input: $('#table_satuan_search_query')
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
                        field: 'nama_spesifikasi',
                        title: 'Spesifikasi',
                        width: 'auto',
                        template: function(row) {
                            return ucwords(row.nama_spesifikasi);
                        },
                    },
                    {
                        field: 'value_spesifikasi',
                        title: 'Nilai',
                        width: 'auto',
                        autoHide:false,
                    },
                    {
                        field: 'actions',
                        title: 'Aksi',
                        width: 'auto',
                        textAlign: 'center',
                        overflow: 'visible',
                        autoHide:false,
                        template: function(row) {
                            var txtAction = "<a href='#' class='btn btn-sm btn-clean btn-icon edit' title='Ubah' onclick='editSpek("+row.id+");return false;'>";
                                txtAction += "<i class='la la-edit'></i>";
                                txtAction += "</a>";
                                txtAction += "<a href='#' class='btn btn-sm btn-clean btn-icon' title='Hapus' onclick='deleteSpek("+row.id+");return false;'>";
                                txtAction += "<i class='la la-trash'></i>";
                                txtAction += "</a>";

                            return txtAction;
                        },
                    },
                ],
            });
		});

        $("#btnAddSpec").on('click', function(e) {
			var errCount = 0;

			$(".reqSpec").each(function(){
				if($(this).val() == "" || $(this).children("option:selected").val() == ""){
					$(this).closest('.form-group').find('.errSpec').show();
					errCount = errCount + 1;
				}
				else {
					$(this).closest('.form-group').find('.errSpec').hide();
				}
			});

			if (errCount == 0) {
                Swal.fire({
                    title: "Tambah Spesifikasi untuk Item?",
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
                            url: "/Product/StoreSpec",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idProduct : '',
                                id_spesifikasi : $("#spesifikasi option:selected").val(),
                                nilai_spesifikasi : $("#nilai_spek").val(),
                                mode : 'tambah'
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Data Berhasil disimpan!.",
                                        "success"
                                    )
                                    $("#spesifikasi").val("").trigger("change");
                                    $("#nilai_spek").val("");
                                    var datatable = $('#table_spek').KTDatatable();
                                        datatable.setDataSourceParam('idProduct', '');
                                        datatable.reload();
                                }
                                else if (result == "failDuplicate") {
                                    Swal.fire(
                                        "Gagal!",
                                        "Spesifikasi ini sudah tersedia pada Item !",
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

        function editSpek(id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/Product/EditSpec",
                method: 'POST',
                data: {
                    idDetail: id
                },
                success: function(result){
                    if (result != "") {
                        var data = "<tr>";
                            data += "<td style='display:none;'><input type='text' class='form-control' id='idRowEdit' value='"+result.id+"' /></td>";
                            data += "<td style='display:none;'><input type='text' class='form-control' id='idItemEdit' value='"+result.id_item+"' /></td>";
                            data += "<td style='display:none;'><input type='text' class='form-control' id='idSpecEdit' value='"+result.id_spesifikasi+"' /></td>";
                            data += "<td style='text-align:center;'>"+ucwords(result.nama_spesifikasi)+"</td>";
                            data += "<td style='text-align:center;'><input type='text' class='form-control inputEdit' id='nilai_spek_edit' autocomplete='off' value='"+result.value_spesifikasi+"' /></td>";
                            data += "</tr>";
                            $('#detil_edit_spec').append(data);
                        $("#btnModalEditSpec").trigger('click');
                    }
                }
            });
        }

        $(document).on("click", "#btnEditSpec", function(e) {
            var errCount = 0;

            var idRow = $("#idRowEdit").val();
            var idItem = $("#idItemEdit").val();
            var idSpec = $("#idSpecEdit").val();
		    var nilai = $("#nilai_spek_edit").val();

            $(".inputEdit").each(function(){
                if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                    errCount = parseInt(errCount) + 1;
                }
            });

			if (errCount == 0) {
                Swal.fire({
                    title: "Update Spesifikasi Item?",
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
                            url: "/Product/UpdateSpec",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idDetail: idRow,
                                id_spesifikasi : idSpec,
                                idProduct : '',
                                nilai_spesifikasi : nilai
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Data Berhasil disimpan!.",
                                        "success"
                                    )
                                    var datatable = $('#table_spek').KTDatatable();
                                        datatable.setDataSourceParam('idProduct', '');
                                        datatable.reload();
                                }
                                else if (result == "failDuplicate") {
                                    Swal.fire(
                                        "Gagal!",
                                        "Spesifikasi ini sudah tersedia pada Item !",
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

        $(document).on("click", "#btnEditItem", function(e) {
            var errCount = 0;

			$(".reqSatuanEdit").each(function(){
				if($(this).val() == "" || $(this).children("option:selected").val() == ""){
				   	$(this).closest('.form-group').find('.errSatuanEdit').show();
				  	errCount = errCount + 1;
				}
				else {
					$(this).closest('.form-group').find('.errSatuanEdit').hide();
				}
			});

			if (errCount == 0) {
                Swal.fire({
                    title: "Update Satuan Item?",
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
                            url: "/Product/UpdateDetail",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idDetail: $("#id_detail").val(),
                                id_satuan : $("#id_satuanEdit").val(),
                                idProduct : '',
                                panjang : $("#panjang_itemEdit").val(),
                                lebar : $("#lebar_itemEdit").val(),
                                tinggi : $("#tinggi_itemEdit").val(),
                                berat : $("#berat_itemEdit").val(),
                                harga_beli : $("#harga_beliEdit").val(),
                                harga_jual : $("#harga_jualEdit").val(),
                                stok_min : $("#stok_minimumEdit").val(),
                                stok_max : $("#stok_maksimumEdit").val(),
                                panjangDus : $("#panjang_dusEdit").val(),
                                lebarDus : $("#lebar_dusEdit").val(),
                                tinggiDus : $("#tinggi_dusEdit").val(),
                                beratDus : $("#berat_dusEdit").val(),
                                // qtyPerDus : $("#qty_per_dusEdit").val(),
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Data Berhasil disimpan!.",
                                        "success"
                                    )
                                    $("#nama_satuanEdit").val("");
                                    $("#id_detail").val("");
                                    $("#id_satuanEdit").val("");
                                    $("#panjang_itemEditMask").val(0).trigger("change");
                                    $("#lebar_itemEditMask").val(0).trigger("change");
                                    $("#tinggi_itemEditMask").val(0).trigger("change");
                                    $("#berat_itemEditMask").val(0).trigger("change");
                                    $("#harga_beliEditMask").val(0).trigger("change");
                                    $("#harga_jualEditMask").val(0).trigger("change");
                                    $("#stok_minimumEditMask").val(0).trigger("change");
                                    $("#stok_maksimumEditMask").val(0).trigger("change");
                                    $("#panjang_dusEditMask").val(0).trigger("change");
                                    $("#lebar_dusEditMask").val(0).trigger("change");
                                    $("#tinggi_dusEditMask").val(0).trigger("change");
                                    $("#berat_dusEditMask").val(0).trigger("change");
                                    // $("#qty_per_dusEditMask").val(0).trigger("change");
                                    var datatable = $('#table_satuan').KTDatatable();
                                        datatable.setDataSourceParam('idProduct', '');
                                        datatable.reload();
                                }
                                else if (result == "failDuplicate") {
                                    Swal.fire(
                                        "Gagal!",
                                        "Satuan ini sudah tersedia pada Item !",
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

        function deleteSpek(id) {
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
                        url: "/Product/DeleteSpec",
                        method: 'POST',
                        data: {
                            idDetail: id
                        },
                        success: function(result){
                            if (result == "success") {
                                Swal.fire(
                                    "Sukses!",
                                    "Data Berhasil dihapus!.",
                                    "success"
                                )
                            }
                        }
                    });
                    var datatable = $("#table_spek").KTDatatable();
                    datatable.reload();
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

        $(document).ready(function() {
            var datatable = $('#list_item_cust').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Product/GetDataItem',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data : {
                                id: "",
                                module: 'customer'
                            },
                        }
                    },
                    pageSize: 25,
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

                pagination: true,

                search: {
                    input: $('#list_item_cust_search_query')
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
                        visible: false,
                    },
                    {
                        field: 'kode_customer',
                        title: 'Kode Customer',
                        width: 100,
                        autoHide: false,
                        textAlign: 'left',
                        template: function(row) {
                            return row.kode_customer.toUpperCase();
                        },
                    },
                    {
                        field: 'nama_customer',
                        title: 'Nama Customer',
                        width: 500,
                        autoHide: false,
                        textAlign: 'left',
                        template: function(row) {
                            return ucwords(row.nama_customer);
                        },
                    },
                    {
                        field: 'nama_kategori',
                        title: 'Nama Kategori',
                        width: 170,
                        autoHide: false,
                        textAlign: 'left',
                        template: function(row) {
                            return ucwords(row.nama_kategori);
                        },
                    },
                    {
                        field: 'actions',
                        title: 'Aksi',
                        textAlign: 'center',
                        overflow: 'visible',
                        width: 'auto',
                        autoHide:false,
                        template: function(row) {
                            var modul = "customer";
                            var txtAction = "<a href='#' class='btn btn-sm btn-clean btn-icon' title='Hapus' onclick=deleteCustOrSupp("+row.id+",'"+modul+"');return false;>";
                                txtAction += "<i class='la la-trash'></i>";
                                txtAction += "</a>";

                            return txtAction;
                        },
                    },
                ],
            });
		});

        $(document).ready(function() {
            var datatable = $('#list_item_supp').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Product/GetDataItem',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data : {
                                id: "",
                                module: 'supplier'
                            },
                        }
                    },
                    pageSize: 25,
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

                pagination: true,

                search: {
                    input: $('#list_item_supp_search_query')
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
                        visible: false,
                    },
                    {
                        field: 'kode_supplier',
                        title: 'Kode Supplier',
                        width: 100,
                        autoHide: false,
                        textAlign: 'left',
                        template: function(row) {
                            return row.kode_supplier.toUpperCase();
                        },
                    },
                    {
                        field: 'nama_supplier',
                        title: 'Nama Supplier',
                        width: 500,
                        autoHide: false,
                        textAlign: 'left',
                        template: function(row) {
                            return ucwords(row.nama_supplier);
                        },
                    },
                    {
                        field: 'nama_kategori',
                        title: 'Nama Kategori',
                        width: 170,
                        autoHide: false,
                        textAlign: 'left',
                        template: function(row) {
                            return ucwords(row.nama_kategori);
                        },
                    },
                    {
                        field: 'actions',
                        title: 'Aksi',
                        textAlign: 'center',
                        overflow: 'visible',
                        width: 'auto',
                        autoHide:false,
                        template: function(row) {
                            var modul = "supplier";
                            var txtAction = "<a href='#' class='btn btn-sm btn-clean btn-icon' title='Hapus' onclick=deleteCustOrSupp("+row.id+",'"+modul+"');return false;>";
                                txtAction += "<i class='la la-trash'></i>";
                                txtAction += "</a>";

                            return txtAction;
                        },
                    },
                ],
            });
		});

        $("#list_cust").on('click', 'table .addCust', function() {
            var idCust = $(this).val();

            Swal.fire({
                title: "Tambahkan Data?",
                text: "Apakah anda ingin menambah customer ini ?",
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
                        url: "/Product/AddCustomerOrSupplier",
                        method: 'POST',
                        data: {
                            id_item: '',
                            cust_supp: idCust,
                            module: 'customer'
                        },
                        success: function(result){
                            Swal.fire(
                                "Berhasil!",
                                "Customer Berhasil ditambahkan!",
                                "success"
                            )

                            var datatable = $('#list_cust').KTDatatable();
                                datatable.setDataSourceParam('id', '');
                                datatable.setDataSourceParam('module', 'customer');
                                datatable.reload();
                            var datatable2 = $('#list_item_cust').KTDatatable();
                                datatable2.reload();
                        }
                    });
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
        });

        $("#list_supp").on('click', 'table .addSupp', function() {
            var idSupp = $(this).val();

            Swal.fire({
                title: "Tambahkan Data?",
                text: "Apakah anda ingin menambah supplier ini ?",
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
                        url: "/Product/AddCustomerOrSupplier",
                        method: 'POST',
                        data: {
                            id_item: '',
                            cust_supp: idSupp,
                            module: 'supplier'
                        },
                        success: function(result){
                            Swal.fire(
                                "Berhasil!",
                                "Supplier Berhasil ditambahkan!",
                                "success"
                            )

                            var datatable = $('#list_supp').KTDatatable();
                                datatable.setDataSourceParam('id', '');
                                datatable.setDataSourceParam('module', 'supplier');
                                datatable.reload();
                            var datatable2 = $('#list_item_supp').KTDatatable();
                                datatable2.reload();
                        }
                    });
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
        });

        $(document).ready(function() {

            var datatable = $('#list_cust').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Product/GetSuppAndCust',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data : {
                                id: "",
                                module: 'customer'
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
                    input: $('#list_cust_search_query')
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
                        field: 'nama_customer',
                        title: 'Customer',
                        width: 'auto',
                        textAlign: 'left',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                            txt += '<span class="font-weight-bold">'+ucwords(row.nama_customer)+'</span><br />';
                            txt += '<span class="label label-md label-outline-primary label-inline mr-2 mb-1 mt-1">' +row.nama_kategori.toUpperCase()+ '</span>';

                            return txt;
                        },
                    },
                    {
                        field: 'kategori_customer',
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
                            return "<button type='button' class='btn btn-primary btn-icon addCust' data-popup='tooltip' title='Tambah' value='" + row.id +"' ><i class='flaticon2-plus'></i></button>";
                        },
                    }
                ],
            });

            $('#list_cust_search_kategori').on('change', function() {
                datatable.search($(this).val(), 'kategori_customer');
            });
        });

        $(document).ready(function() {

            var datatable = $('#list_supp').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Product/GetSuppAndCust',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data : {
                                id: "",
                                module: 'supplier'
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
                    input: $('#list_supp_search_query')
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
                        field: 'nama_supplier',
                        title: 'Supplier',
                        width: 'auto',
                        textAlign: 'left',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                            txt += '<span class="font-weight-bold">'+ucwords(row.nama_supplier)+'</span><br />';
                            txt += '<span class="label label-md label-outline-primary label-inline mr-2 mb-1 mt-1">' +row.nama_kategori.toUpperCase()+ '</span>';

                            return txt;
                        },
                    },
                    {
                        field: 'kategori_supplier',
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
                            return "<button type='button' class='btn btn-primary btn-icon addSupp' data-popup='tooltip' title='Tambah' value='" + row.id +"' ><i class='flaticon2-plus'></i></button>";
                        },
                    }
                ],
            });

            $('#list_supp_search_kategori').on('change', function() {
                datatable.search($(this).val(), 'kategori_supplier');
            });
        });

        function deleteCustOrSupp(id, mod) {
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
                        url: "/Product/DeleteCustOrSupp",
                        method: 'POST',
                        data: {
                            idDetail: id,
                            module: mod,
                        },
                        success: function(result){
                            Swal.fire(
                                "Sukses!",
                                "Data Berhasil dihapus!.",
                                "success"
                            )
                        }
                    });
                    if (mod == "customer") {
                        var datatable = $("#list_item_cust").KTDatatable();
                        datatable.reload();
                    }
                    else if (mod == "supplier") {
                        var datatable = $("#list_item_supp").KTDatatable();
                        datatable.reload();
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

        $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
            var datatableCust = $("#list_item_cust").KTDatatable();
                datatableCust.reload();

            var datatableSupp = $("#list_item_supp").KTDatatable();
                datatableSupp.reload();
	    });

        $("#modal_list_cust").on('show.bs.modal', function(e) {
	        var datatablItem = $("#list_cust").KTDatatable();
            datatablItem.reload();
	    });

        $("#modal_list_supp").on('show.bs.modal', function(e) {
	        var datatablItem = $("#list_supp").KTDatatable();
            datatablItem.reload();
	    });

        function setDefault(id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/Product/SetDefault",
                method: 'POST',
                data: {
                    idDetail: id
                },
                success: function(result){
                    Swal.fire(
                        "Sukses!",
                        "Set Satuan Dasar Berhasil!.",
                        "success"
                    )
                }
            });
            var datatable = $("#table_satuan").KTDatatable();
            datatable.reload();
        }

        function setMonitor(id) {
            var flag = 0;
            if ($("#checkMonitor").prop("checked") == true) {
			    flag = 1;
	        }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/Product/SetMonitor",
                method: 'POST',
                data: {
                    idDetail: id,
                    flag: flag
                },
                success: function(result){
                    Swal.fire(
                        "Sukses!",
                        "Produk Berhasil! ditambahkan ke Dashboard.",
                        "success"
                    )
                }
            });
            var datatable = $("#table_satuan").KTDatatable();
            datatable.reload();
        }

    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
