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
                                        <span class="nav-text">Informasi Total Tagihan</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab_pane_2" id="tab2">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Tagihan Outstanding</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab_pane_3" id="tab3">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Tagihan Lunas</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
					</div>
                    <form action="#" class="form-horizontal" id="form_add" method="POST" enctype="multipart/form-data">
					    <div class="card-body">
                            {{ csrf_field() }}
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="tab_pane_1" role="tabpanel" aria-labelledby="tab_pane_1">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <fieldset>
                                                <legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi Pembeli / Customer </h6></legend>
                                                <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                                <br>

                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">Customer :</label>
                                                    <div class="col-lg-9">
                                                        <input type="text" class="form-control" id="customer" value="{{ucwords($dataCustomer->nama_customer)}}" readonly>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">Alamat :</label>
                                                    <div class="col-lg-9">
                                                        <textarea class="form-control" id="alamat" style="resize:none;" readonly>{{ucwords($dataAlamat)}}</textarea>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">Limit Hutang :</label>
                                                    <div class="col-lg-9">
                                                        <input type="text" class="form-control" id="limit" value="{{number_format($dataCustomer->limit_customer ?? 0)}}" readonly>
                                                    </div>
                                                </div>

                                            </fieldset>
                                        </div>
                                        <div class="col-md-6">
                                            <fieldset>
                                                <legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi Tagihan Pembeli / Customer </h6></legend>
                                                <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                                <br>

                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">Total Invoice :</label>
                                                    <div class="col-lg-9">
                                                        <input type="text" class="form-control" id="ttlInv" readonly>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">Total Invoice Jatuh Tempo :</label>
                                                    <div class="col-lg-9">
                                                        <input type="text" class="form-control" id="ttlInvJT" readonly>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">Total Tagihan :</label>
                                                    <div class="col-lg-9">
                                                        <input type="text" class="form-control" id="ttlTagihan" readonly>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-lg-3 col-form-label">Total Tagihan Jatuh Tempo :</label>
                                                    <div class="col-lg-9">
                                                        <input type="text" class="form-control" id="ttlTagihanJT" readonly>
                                                    </div>
                                                </div>

                                            </fieldset>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab_pane_2" role="tabpanel" aria-labelledby="tab_pane_2">
                                    <div class="mb-7">
                                        <div class="row align-items-center">
                                            <div class="col-lg-12 col-xl-8">
                                                <div class="row align-items-center">
                                                    <div class="col-md-3 my-2 my-md-0">
                                                        <div class="align-items-center">
                                                            <label style="display: inline-block;"></label>
                                                            <div class="input-icon">
                                                                <input type="text" class="form-control" placeholder="Search..." id="list_item_search_query"/>
                                                                <span>
                                                                    <i class="flaticon2-search-1 text-muted"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 my-2 my-md-0">
                                                        <div class="align-items-center">
                                                            <label class="mr-3 mb-0 d-none d-md-block">Status:</label>
                                                            <select class="form-control select2" id="list_item_search_status" width="100%">
                                                                <option value="">All</option>
                                                                <option value="0">Belum Bayar</option>
                                                                <option value="2">Bayar Sebagian</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 my-2 my-md-0">
                                                        <div class=" align-items-center">
                                                            <label class="mr-3 mb-0 d-none d-md-block">Periode :</label>
                                                            <input type="text" class="form-control" id="bulan_picker" name="bulan_picker" autocomplete="off" >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 my-2 my-md-0">
                                                        <div class=" align-items-center">
                                                            <label class="mr-3 mb-0 d-none d-md-block" style="color:white;">Bayar Sekaligus</label>
                                                            <button type="button" id="btnMass" class="btn btn-success border-white">Bayar Sekaligus</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="datatable datatable-bordered datatable-head-custom" id="list_item"></div>
                                </div>

                                <div class="tab-pane fade" id="tab_pane_3" role="tabpanel" aria-labelledby="tab_pane_3">
                                    <div class="mb-7">
                                        <div class="row align-items-center">
                                            <div class="col-lg-12 col-xl-8">
                                                <div class="row align-items-center">
                                                    <div class="col-md-3 my-2 my-md-0">
                                                        <div class="align-items-center">
                                                            <label style="display: inline-block;"></label>
                                                            <div class="input-icon">
                                                                <input type="text" class="form-control" placeholder="Search..." id="list_item_lunas_search_query"/>
                                                                <span>
                                                                    <i class="flaticon2-search-1 text-muted"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 my-2 my-md-0">
                                                        <div class=" align-items-center">
                                                            <label class="mr-3 mb-0 d-none d-md-block">Periode :</label>
                                                            <input type="text" class="form-control" id="bulan_picker_lunas" name="bulan_picker_lunas" autocomplete="off" >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="datatable datatable-bordered datatable-head-custom" id="list_item_lunas"></div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-white d-sm-flex justify-content-sm-between align-items-sm-center">
                            <div class="btn-group">
                                <button type="button" style="display: none;" id="btnModalPayment" data-toggle="modal" data-target="#modal_payment"></button>
                                <button type="button" style="display: none;" id="btnModalPaymentMass" data-toggle="modal" data-backdrop="static" data-target="#modal_payment_mass"></button>
                                <button type="button" style="display: none;" id="btnModalCost" data-toggle="modal" data-target="#modal_potongan"></button>
                                <button type="button" style="display: none;" class="btn btn-primary" id="btnCost" data-toggle="modal" data-target="#modal_list_potongan"></button>
                                <button type="button" style="display: none;" class="btn btn-primary" id="btnHistory" data-toggle="modal" data-target="#modal_list_payment"></button>
                                <button type="button" class="btn btn-light-danger font-weight-bold mr-2" id="cancel">Keluar <i class="flaticon2-cancel icon-sm"></i></button>
                            </div>
                        </div>
                        <!-- Horizontal form pembayaran -->
                        <div id="modal_payment" class="modal fade">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">

                                        <h5 id="txtBayar" class="modal-title text-white">Input Pembayaran</h5>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <fieldset>
                                                    <legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi Pembayaran </h6></legend>
                                                    <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                                    <br>
                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">No. Faktur :</label>
                                                        <div class="col-lg-7">
                                                            <input type="hidden" class="form-control" name="id_invoice" id="id_invoice" readonly>
                                                            <input type="text" class="form-control bg-slate-600 border-slate-600 border-1" placeholder="Auto Generated" name="kode_invoice" id="kode_invoice" readonly>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <button type="button" class="btn btn-primary mt-2 mt-sm-0" onclick="cetakInv();" ><i class="fas fa-print ml-2"></i></button>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">Tanggal Faktur :</label>
                                                        <div class="col-lg-9">
                                                            <input type="text" class="form-control" placeholder="Tanggal Faktur" name="tanggal_invoice" id="tanggal_invoice" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">Tanggal Jatuh Tempo :</label>
                                                        <div class="col-lg-9">
                                                            <input type="text" class="form-control" placeholder="Tanggal JT Faktur" name="tanggal_jt" id="tanggal_jt" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">Jenis Pembayaran :</label>
                                                        <div class="col-lg-9">
                                                            <select class="form-control select2 req" id="jenis_bayar" name="jenis_bayar" style="width:100%;">
                                                                <option label="Label"></option>
                                                                <option value="transfer">Transfer</option>
                                                                <option value="giro">Giro</option>
                                                                <option value="cheque">Cheque</option>
                                                                <option value="cash">Cash</option>
                                                            </select>
                                                            <span class="form-text text-danger err" style="display:none;">*Harap pilih jenis pembayaran terlebih dahulu!</span>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row" id="rowJTGiro" style="display: none;">
                                                        <label class="col-lg-3 col-form-label">Tanggal JT Giro :</label>
                                                        <div class="col-lg-9">
                                                            <input type="hidden" class="form-control reqJT" name="tanggal_jt_giro" id="tanggal_jt_giro">
                                                            <input type="text" class="form-control" placeholder="Pilih Tanggal" name="tanggal_jt_giro_picker" id="tanggal_jt_giro_picker" readonly>
                                                            <span class="form-text text-danger" id="errJT" style="display:none;">*Harap pilih tanggal JT Giro terlebih dahulu!</span>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row" id="rowRekening">
                                                        <label class="col-lg-3 col-form-label">Rekening Pembayaran :</label>
                                                        <div class="col-lg-9">
                                                            <select class="form-control select2" id="rekening" name="rekening" style="width: 100%;">
                                                                <option label="Label"></option>
                                                                @foreach($dataRekening as $rekening)
                                                                <option value="{{$rekening->id}}">{{strtoupper($rekening->nama_bank)}} - {{strtoupper($rekening->nomor_rekening)}} A/N {{strtoupper($rekening->atas_nama)}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="form-text text-danger" id="errRekening" style="display:none;">*Harap pilih rekening terlebih dahulu!</span>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">Tanggal Pembayaran :</label>
                                                        <div class="col-lg-9">
                                                            <input type="hidden" class="form-control req" name="tanggal_bayar" id="tanggal_bayar" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}">
                                                            <input type="text" class="form-control" placeholder="Pilih Tanggal" name="tanggal_bayar_picker" id="tanggal_bayar_picker" readonly>
                                                            <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal penjualan terlebih dahulu!</span>
                                                        </div>
                                                    </div>

                                                </fieldset>
                                            </div>

                                            <div class="col-md-6">
                                                <fieldset>
                                                    <legend class="text-muted"><h6><i class="fab la-buffer"></i> Rincian Pembayaran</h6></legend>
                                                    <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                                    <br>

                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">Tagihan Rp :</label>
                                                        <div class="col-lg-9">
                                                            <input type="hidden" id="nominal_invoice" onkeypress="return validasiDecimal(this,event);" class="form-control text-right" readonly>
                                                            <input type="text" id="nominal_invoice_mask" onkeypress="return validasiDecimal(this,event);" class="form-control text-right" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">Total Bayar Rp :</label>
                                                        <div class="col-lg-6">
                                                            <input type="text" id="nominal_bayar_mask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right inputNominal">
                                                            <input type="hidden" id="nominal_bayar" autocomplete="off" onkeypress="return validasiDecimal(this,event);" class="form-control text-right">
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <button type="button" value="100" id="btnFull" class="btn btn-primary border-white">Bayar Penuh</button>
                                                        </div>
                                                        <span class="form-text text-danger" id="errNominal" style="display:none;">*Harap masukkan nominal pembayaran terlebih dahulu!</span>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">Potongan Tagihan Rp :</label>
                                                        <div class="col-lg-9">
                                                            <input type="text" id="potongan_tagihan" autocomplete="off" onkeypress="return validasiDecimal(this,event);" class="form-control text-right inputNominal">
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">Sisa Tagihan RP :</label>
                                                        <div class="col-lg-9">
                                                            <input type="hidden" id="sisa_tagihan" class="form-control text-right" readonly>
                                                            <input type="text" id="sisa_tagihan_mask" class="form-control text-right" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">Keterangan :</label>
                                                        <div class="col-lg-9">
                                                            <textarea class="form-control elastic" id="keterangan" name="keterangan" rows="3" placeholder="Ketik Keterangan Pembayaran Disini "></textarea>
                                                            <span class="form-text text-danger err" style="display:none;">*Harap pilih alamat customer terlebih dahulu!</span>
                                                        </div>
                                                    </div>

                                                </fieldset>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" id="btnPayment">Simpan</button>
                                        <button type="button" class="btn btn-link" id="closeModalPayment" data-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /horizontal form pembayaran -->

                        <!-- Horizontal form pembayaran massal -->
                        <div id="modal_payment_mass" class="modal fade">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">

                                        <h5 id="txtBayar" class="modal-title text-white">Input Pembayaran</h5>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <fieldset>
                                                    <legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi Pembayaran </h6></legend>
                                                    <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                                    <br>

                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">Jenis Pembayaran :</label>
                                                        <div class="col-lg-9">
                                                            <select class="form-control select2 reqMass" id="jenis_bayar_mass" name="jenis_bayar_mass" style="width:100%;">
                                                                <option label="Label"></option>
                                                                <option value="transfer">Transfer</option>
                                                                <option value="giro">Giro</option>
                                                                <option value="cheque">Cheque</option>
                                                                <option value="cash">Cash</option>
                                                            </select>
                                                            <span class="form-text text-danger errMass" style="display:none;">*Harap pilih jenis pembayaran terlebih dahulu!</span>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row" id="rowJTGiroMass" style="display: none;">
                                                        <label class="col-lg-3 col-form-label">Tanggal JT Giro :</label>
                                                        <div class="col-lg-9">
                                                            <input type="hidden" class="form-control" name="tanggal_jt_giro_mass" id="tanggal_jt_giro_mass" >
                                                            <input type="text" class="form-control" placeholder="Pilih Tanggal" name="tanggal_jt_giro_mass_picker" id="tanggal_jt_giro_mass_picker" readonly>
                                                            <span class="form-text text-danger" id="errJTMass" style="display:none;">*Harap pilih tanggal JT Giro terlebih dahulu!</span>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row" id="rowRekeningMass" style="display: none;">
                                                        <label class="col-lg-3 col-form-label">Rekening Pembayaran :</label>
                                                        <div class="col-lg-9">
                                                            <select class="form-control select2" id="rekening_mass" name="rekening_mass" style="width: 100%;">
                                                                <option label="Label"></option>
                                                                @foreach($dataRekening as $rekening)
                                                                <option value="{{$rekening->id}}">{{strtoupper($rekening->nama_bank)}} - {{strtoupper($rekening->nomor_rekening)}} A/N {{strtoupper($rekening->atas_nama)}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="form-text text-danger" id="errRekeningMass" style="display:none;">*Harap pilih rekening terlebih dahulu!</span>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">Tanggal Pembayaran :</label>
                                                        <div class="col-lg-9">
                                                            <input type="hidden" class="form-control reqMass" name="tanggal_bayar_mass" id="tanggal_bayar_mass" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}">
                                                            <input type="text" class="form-control" placeholder="Pilih Tanggal" name="tanggal_bayar_mass_picker" id="tanggal_bayar_mass_picker" readonly>
                                                            <span class="form-text text-danger errMass" style="display:none;">*Harap pilih tanggal pembayaran terlebih dahulu!</span>
                                                        </div>
                                                    </div>

                                                </fieldset>
                                            </div>

                                            <div class="col-md-6">
                                                <fieldset>
                                                    <legend class="text-muted"><h6><i class="fab la-buffer"></i> Rincian Pembayaran</h6></legend>
                                                    <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                                    <br>

                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">Total Tagihan(Rp):</label>
                                                        <div class="col-lg-9">
                                                            <input type="hidden" id="nominal_invoice_mass" onkeypress="return validasiDecimal(this,event);" class="form-control text-right" readonly>
                                                            <input type="text" id="nominal_invoice_mass_mask" onkeypress="return validasiDecimal(this,event);" class="form-control text-right" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">Total Bayar(Rp):</label>
                                                        <div class="col-lg-6">
                                                            <input type="text" id="nominal_bayar_mass_mask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right inputNominalMass">
                                                            <input type="hidden" id="nominal_bayar_mass" autocomplete="off" onkeypress="return validasiDecimal(this,event);" class="form-control text-right ">
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <button type="button" id="btnAlocate" class="btn btn-primary border-white">Alokasi Pembayaran</button>
                                                        </div>
                                                        <span class="form-text text-danger" id="errNominalMass" style="display:none;">*Harap lakukan alokasi nominal pembayaran terlebih dahulu!</span>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">Sisa Tagihan(Rp):</label>
                                                        <div class="col-lg-9">
                                                            <input type="hidden" id="sisa_tagihan_mass" class="form-control text-right" readonly>
                                                            <input type="text" id="sisa_tagihan_mass_mask" class="form-control text-right" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">Keterangan :</label>
                                                        <div class="col-lg-9">
                                                            <textarea class="form-control elastic" id="keterangan_mass" name="keterangan_mass" rows="3" placeholder="Ketik Keterangan Pembayaran Disini "></textarea>
                                                        </div>
                                                    </div>

                                                </fieldset>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <legend class="text-muted ml-5"><h6><i class="la la-list"></i> Perincian Tagihan</h6></legend>
                                            <div class="col-md-12">
                                                <div class="datatable datatable-bordered datatable-head-custom" id="list_item_mass"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" id="btnPaymentMass">Simpan</button>
                                        <button type="button" class="btn btn-link" id="closeModalPaymentMass" data-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /horizontal form pembayaran massal -->

                        <!-- Horizontal form potongan -->
                        <div id="modal_potongan" class="modal fade">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">

                                        <h5 id="txtBayar" class="modal-title text-white">Input Potongan</h5>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <fieldset>
                                                    <legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Data Potongan Pembayaran </h6></legend>
                                                    <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                                    <br>
                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">No. Faktur :</label>
                                                        <div class="col-lg-9">
                                                            <input type="hidden" class="form-control" name="id_invoice_pot" id="id_invoice_pot" readonly>
                                                            <input type="text" class="form-control bg-slate-600 border-slate-600 border-1" placeholder="Auto Generated" name="kode_invoice_pot" id="kode_invoice_pot" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">Tanggal Faktur :</label>
                                                        <div class="col-lg-9">
                                                            <input type="text" class="form-control" placeholder="Tanggal Faktur" name="tanggal_invoice_pot" id="tanggal_invoice_pot" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">Tanggal Jatuh Tempo :</label>
                                                        <div class="col-lg-9">
                                                            <input type="text" class="form-control" placeholder="Tanggal JT Faktur" name="tanggal_jt_pot" id="tanggal_jt_pot" readonly>
                                                        </div>
                                                    </div>

                                                </fieldset>
                                            </div>

                                            <div class="col-md-6">
                                                <fieldset>
                                                    <legend class="text-muted"><h6><i class="fab la-buffer"></i> Rincian Potongan</h6></legend>
                                                    <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                                    <br>

                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">Sisa Potongan Rp :</label>
                                                        <div class="col-lg-9">
                                                            <input type="hidden" id="nominal_potongan" onkeypress="return validasiDecimal(this,event);" class="form-control text-right" readonly>
                                                            <input type="text" id="nominal_potongan_mask" onkeypress="return validasiDecimal(this,event);" class="form-control text-right" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">Nominal Potongan Rp :</label>
                                                        <div class="col-lg-9">
                                                            <input type="text" id="nominal_input_pot" autocomplete="off" onkeypress="return validasiDecimal(this,event);" class="form-control text-right inputNominalPot">
                                                            <span class="form-text text-danger" id="errNominalPot" style="display:none;">*Harap masukkan nominal potongan terlebih dahulu!</span>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">Sisa Potongan Rp :</label>
                                                        <div class="col-lg-9">
                                                            <input type="hidden" id="sisa_potongan" class="form-control text-right" readonly>
                                                            <input type="text" id="sisa_potongan_mask" class="form-control text-right" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label">Keterangan :</label>
                                                        <div class="col-lg-9">
                                                            <textarea class="form-control elastic" id="keterangan_pot" name="keterangan_pot" rows="3" placeholder="Ketik Keterangan Potongan Disini "></textarea>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label class="col-lg-3 col-form-label"></label>
                                                        <div class="col-lg-9">
                                                            <button type="button" class="btn btn-primary font-weight-bold" id="btnAddPot">Simpan Potongan</button>
                                                        </div>
                                                    </div>

                                                </fieldset>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-link" id="closeModalPotongan" data-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /horizontal form potongan -->

                        <!-- Modal form list alamat -->
                        <div id="modal_list_potongan" class="modal fade">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">

                                        <h5 class="modal-title">List Potongan Invoice</h5>
                                    </div>
                                    <div class="modal-body">
                                        <form >
                                            <table class="datatable-bordered datatable-head-custom ml-4" id="list_cost_invoice" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th align="center" style="text-align:center;display:none;">ID</th>
                                                        <th align="center" style="text-align:center;">Nominal</th>
                                                        <th align="center" style="text-align:center;">Keterangan</th>
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
                        <!-- Modal form list alamat -->
                        <div id="modal_list_payment" class="modal fade">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">

                                        <h5 class="modal-title">List Pembayaran Invoice</h5>
                                    </div>
                                    <div class="modal-body">
                                        <form >
                                            <table class="datatable-bordered datatable-head-custom ml-4" id="list_payment_invoice" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th align="center" style="text-align:center;display:none;">ID</th>
                                                        <th align="center" style="text-align:center;">Kode AR</th>
                                                        <th align="center" style="text-align:center;">Tanggal Bayar</th>
                                                        <th align="center" style="text-align:center;">Metode Bayar</th>
                                                        <th align="center" style="text-align:center;">Rekening</th>
                                                        <th align="center" style="text-align:center;" class="gr">JT Giro</th>
                                                        <th align="center" style="text-align:center;">Nominal</th>
                                                        <th align="center" style="text-align:center;">Keterangan</th>
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
                    </form>
                </div>

			</div>
			<!-- /content area -->
@endsection
@section('scripts')
    <script type="text/javascript">

        $(document).ready(function() {

            $('#jenis_bayar').select2({
                allowClear: true,
                placeholder: "Pilih Jenis Pembayaran"
            });

            $('#rekening').select2({
                allowClear: true,
                placeholder: "Pilih Rekening Pembayaran"
            });

            $('#tanggal_bayar_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "top left",
                autoclose : true,
                "setDate": new Date(),
                format : "dd MM yyyy",
            });

            $('#tanggal_jt_giro_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "top left",
                autoclose : true,
                format : "dd MM yyyy",
            });

            $('#list_item_search_status').select2({
                allowClear: true,
                width:'100%',
            });

            $('#bulan_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                autoclose : true,
                startView: "months",
                minViewMode: "months",
                format : "MM yyyy",
                clearBtn: true,
            });

            $('#bulan_picker_lunas').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                autoclose : true,
                startView: "months",
                minViewMode: "months",
                format : "MM yyyy",
                clearBtn: true,
            });

            $('#jenis_bayar_mass').select2({
                allowClear: true,
                placeholder: "Pilih Jenis Pembayaran"
            });

            $('#rekening_mass').select2({
                allowClear: true,
                placeholder: "Pilih Rekening Pembayaran"
            });

            $('#tanggal_bayar_mass_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "top left",
                autoclose : true,
                "setDate": new Date(),
                format : "dd MM yyyy",
            });

            $('#tanggal_jt_giro_mass_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "top left",
                autoclose : true,
                format : "dd MM yyyy",
            });

            $("#nominal_bayar_mask").autoNumeric('init');
            $("#nominal_bayar_mass_mask").autoNumeric('init');

            $("#tanggal_bayar_picker").datepicker('setDate', new Date());
            $("#tanggal_bayar_mass_picker").datepicker('setDate', new Date());
            getDataTagihan();
        });

        $("#nominal_bayar_mask").on('change', function() {
            $("#nominal_bayar").val($("#nominal_bayar_mask").autoNumeric("get"));
        });

        $("#nominal_bayar_mass_mask").on('change', function() {
            $("#nominal_bayar_mass").val($("#nominal_bayar_mass_mask").autoNumeric("get"));
        });

        $("#tanggal_bayar_picker").on('change', function() {
            $("#tanggal_bayar").val($("#tanggal_bayar_picker").data('datepicker').getFormattedDate('yyyy-mm-dd'));
        });

        $("#tanggal_bayar_mass_picker").on('change', function() {
            $("#tanggal_bayar_mass").val($("#tanggal_bayar_mass_picker").data('datepicker').getFormattedDate('yyyy-mm-dd'));
        });

        $("#tanggal_jt_giro_picker").on('change', function() {
            $("#tanggal_jt_giro").val($("#tanggal_jt_giro_picker").data('datepicker').getFormattedDate('yyyy-mm-dd'));
        });

        $("#tanggal_jt_giro_mass_picker").on('change', function() {
            $("#tanggal_jt_giro_mass").val($("#tanggal_jt_giro_mass_picker").data('datepicker').getFormattedDate('yyyy-mm-dd'));
        });

        $("#jenis_bayar").on('change', function() {
            var jenisByr = $("#jenis_bayar").val();

            if (jenisByr == "cash" || jenisByr == "") {
                $("#rowRekening").hide();
                $("#rowJTGiro").hide();
            }
            else {
                $("#rowRekening").show();
                if (jenisByr == "giro") {
                    $("#rowJTGiro").show();
                }
                else {
                    $("#rowJTGiro").hide();
                }
            }


        });

        $("#jenis_bayar_mass").on('change', function() {
            var jenisByr = $("#jenis_bayar_mass").val();

            if (jenisByr == "cash"  || jenisByr == "") {
                $("#rekening_mass").val("").trigger("change");
                $("#rowRekeningMass").hide();
            }
            else {
                $("#rowRekeningMass").show();
                if (jenisByr == "giro") {
                    $("#rowJTGiroMass").show();
                }
                else {
                    $("#rowJTGiroMass").hide();
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
                text: "Apakah anda ingin kembali?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    window.location.href = "{{ url('/AccountReceiveable') }}";
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
	    });

        $(document).ready(function() {

            var datatable = $('#list_item').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/AccountReceiveable/GetDetail',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data : {
                                idCustomer : '{{$dataCustomer->id}}',
                            },
                        }
                    },
                    pageSize: 20,
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
                    input: $('#list_item_search_query')
                },

                autoHide : false,

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
                        field: 'periode_invoice',
                        title: 'Periode Invoice',
                        autoHide: false,
                        textAlign: 'left',
                        width: 'auto',
                        visible:false,

                    },
                    {
                        field: 'checkbox',
                        sortable: false,
                        autoHide: false,
                        title: "<div class='checkbox-inline align-items-center'><label class='checkbox checkbox-lg'><input type='checkbox' id='checkAll' class='text-center checkAll'><span></span></label></div>",
                        textAlign: 'center',
                        width: '50',
                        template: function(row) {
                            var txtCheckbox = "<div class='checkbox-inline align-items-center'>";
                                txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                txtCheckbox += "<input type='checkbox' class='text-center bayarSekaligus' value='"+row.id+"'>";
                                txtCheckbox += "<span></span>";
                                txtCheckbox += "</label>";
                                txtCheckbox += "</div>";
                            if (row.flag_pembayaran == 1) {
                                return "";
                            }
                            else {
                                return txtCheckbox;
                            }
                        },
                    },
                    {
                        field: 'tanggal_invoice',
                        title: 'Tanggal Invoice',
                        width: 'auto',
                        autoHide: false,
                        textAlign: 'left',
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bold">'+formatDate(row.tanggal_invoice)+'</span>';
                                txt += "<br />";
                                if (row.tanggal_invoice != row.tanggal_jt) {
                                    txt += '<span class="label label-md label-outline-warning label-inline"><i class="flaticon-calendar-with-a-clock-time-tools mr-2 text-warning"></i>'+formatDate(row.tanggal_jt)+'</span>';
                                }
                                else {
                                    txt += '<span class="label label-md label-outline-success label-inline">TUNAI</span>';
                                }
                            return txt;
                        },
                    },
                    {
                        field: 'kode_invoice',
                        title: 'No. Faktur',
                        width: 'auto',
                        textAlign: 'left',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bolder">'+row.kode_invoice.toUpperCase()+'</span><br />';
                                txt += '<span class="label label-md label-outline-primary label-inline mt-1">' + row.nama_outlet.toUpperCase() +'</span>';
                            return txt;
                        },
                    },
                    {
                        field: 'grand_total',
                        title: 'Tagihan (Rp)',
                        width: 'auto',
                        textAlign: 'left',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bold">'+parseFloat(row.grand_total).toLocaleString('id-ID', { maximumFractionDigits: 2})+'</span><br />';
                                txt += '<span class="label label-md label-outline-primary label-inline mt-1">Bayar : Rp '+parseFloat(row.nominal_bayar).toLocaleString('id-ID', { maximumFractionDigits: 2})+'</span><br />';
                                txt += '<span class="label label-md label-outline-warning label-inline mt-1">Sisa : Rp '+parseFloat(row.sisa_tagihan).toLocaleString('id-ID', { maximumFractionDigits: 2})+'</span><br />';
                                return txt;
                        },
                    },
                    // {
                    //     field: 'jenis_pembayaran',
                    //     title: 'Metode Pembayaran',
                    //     autoHide: false,
                    //     textAlign: 'center',
                    //     template: function(row) {
                    //         return ucwords(row.jenis_pembayaran);
                    //     },
                    // },
                    {
                        field: 'flag_pembayaran',
                        title: 'Status',
                        autoHide: false,
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            var txt = "";
                                if (row.flag_pembayaran == '2') {
                                    txt += '<span class="label label-light-primary label-inline mt-1">Bayar Sebagian</span>';
                                }
                                else if (row.flag_pembayaran == '1') {
                                    txt += '<span class="label label-light-success label-inline mt-1">Lunas</span>';
                                }
                                else {
                                    txt += '<span class="label label-light-dark label-inline mt-1">Belum Bayar</span>';
                                }
                                return txt;
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
                            var txtAction = "";
                                if (row.flag_pembayaran != "1") {
                                    txtAction += "<a href='#' class='btn btn-icon btn-outline-success btn-xs mr-2' data-toggle='tooltip' title='Input Pembayaran' onclick='payment("+row.id+");return false;'>";
                                    txtAction += "<i class='flaticon-edit'></i>";
                                    txtAction += "</a>";
                                }

                                if (row.sumPotongan > "0" && row.sisa_tagihan != "0") {
                                    txtAction += "<a href='#' class='btn btn-icon btn-outline-warning btn-xs' data-toggle='tooltip' title='Input Potongan' onclick='inputCost("+row.id+");return false;'>";
                                    txtAction += "<i class='fas fa-cut'></i>";
                                    txtAction += "</a>";
                                }
                                else if (row.sumPotongan > "0") {
                                    txtAction += "<a href='#' class='btn btn-icon btn-outline-warning btn-xs' data-toggle='tooltip' title='Data Potongan' onclick='ListCost("+row.id+");return false;'>";
                                    txtAction += "<i class='fas fa-cut'></i>";
                                    txtAction += "</a>";
                                }

                                if (row.flag_pembayaran != "0") {
                                    txtAction += "<a href='#' class='btn btn-icon btn-outline-primary btn-xs' data-toggle='tooltip' title='Lihat' onclick='ListPayment("+row.id+");return false;'>";
                                    txtAction += "<i class='la la-eye'></i>";
                                    txtAction += "</a>";
                                }

                            return txtAction;
                        },
                    }
                ],
            });

            $('#list_item_search_status').on('change', function() {
                datatable.search($(this).val(), 'flag_pembayaran');
            });

            $("#bulan_picker").on('change', function() {
                var bulanDate = $("#bulan_picker").data('datepicker').getFormattedDate('yyyy-mm');
                datatable.search(bulanDate, 'periode_invoice');
            });
        });

        $(document).ready(function() {

            var datatable = $('#list_item_lunas').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/AccountReceiveable/GetDetailLunas',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data : {
                                idCustomer : '{{$dataCustomer->id}}',
                            },
                        }
                    },
                    pageSize: 20,
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
                    input: $('#list_item_lunas_search_query')
                },

                autoHide : false,

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
                        field: 'periode_invoice',
                        title: 'Periode Invoice',
                        autoHide: false,
                        textAlign: 'left',
                        width: 'auto',
                        visible:false,

                    },
                    {
                        field: 'tanggal_invoice',
                        title: 'Tanggal Invoice',
                        width: 200,
                        autoHide: false,
                        textAlign: 'left',
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bold">'+formatDate(row.tanggal_invoice)+'</span>';
                                txt += "<br />";
                                if (row.tanggal_invoice != row.tanggal_jt) {
                                    txt += '<span class="label label-md label-outline-warning label-inline"><i class="flaticon-calendar-with-a-clock-time-tools mr-2 text-warning"></i>JT : '+formatDate(row.tanggal_jt)+'</span>';
                                }
                                // else {
                                //     txt += '<span class="label label-md label-outline-success label-inline">TUNAI</span>';
                                // }
                                // txt += '<br /><span class="label label-md label-outline-success label-inline"><i class="flaticon-calendar-with-a-clock-time-tools mr-2 text-success"></i>Bayar : '+formatDate(row.tanggal)+'</span>';
                            return txt;
                        },
                    },
                    {
                        field: 'kode_invoice',
                        title: 'No. Faktur',
                        width: 300,
                        textAlign: 'left',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bolder">'+row.kode_invoice.toUpperCase()+'</span><br />';
                                txt += '<span class="label label-md label-outline-primary label-inline mt-1">PO:' + row.no_po_customer+'</span><br />';
                                txt += '<span class="label label-md label-outline-primary label-inline mt-1">' + row.nama_outlet.toUpperCase() +'</span>';
                            return txt;
                        },
                    },
                    {
                        field: 'grand_total',
                        title: 'Tagihan (Rp)',
                        width: 'auto',
                        textAlign: 'left',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bold">'+parseFloat(row.grand_total).toLocaleString('id-ID', { maximumFractionDigits: 2})+'</span><br />';
                                return txt;
                        },
                    },
                    // {
                    //     field: 'jenis_pembayaran',
                    //     title: 'Metode Pembayaran',
                    //     autoHide: false,
                    //     textAlign: 'center',
                    //     width:100,
                    //     template: function(row) {
                    //         return ucwords(row.jenis_pembayaran);
                    //     },
                    // },
                    {
                        field: 'Actions',
                        title: 'Aksi',
                        sortable: false,
                        width: 110,
                        overflow: 'visible',
                        autoHide: false,
                        textAlign: 'center',
                        template: function(row) {
                            var txtAction = "";
                                txtAction += "<a href='#' class='btn btn-icon btn-outline-primary btn-xs' data-toggle='tooltip' title='Lihat' onclick='ListPayment("+row.id+");return false;'>";
                                    txtAction += "<i class='la la-eye'></i>";
                                    txtAction += "</a>";

                            return txtAction;
                        },
                    }
                ],
            });

            $("#bulan_picker_lunas").on('change', function() {
                var bulanDate = $("#bulan_picker_lunas").data('datepicker').getFormattedDate('yyyy-mm');
                datatable.search(bulanDate, 'periode_invoice');
            });
        });

        $(document).ready(function() {

            var datatable = $('#list_item_mass').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/AccountReceiveable/GetDetailMass',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                        }
                    },
                    pageSize: 100,
                    serverPaging: true,
                    serverFiltering: false,
                    serverSorting: false,
                    saveState: false
                },

                layout: {
                    scroll: false,
                    height: 'auto',
                    footer: false
                },

                sortable: true,

                filterable: true,

                pagination: true,

                search: {
                    input: $('#list_item_mass_search_query')
                },

                autoHide : false,

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
                        field: 'value3',
                        title: 'Tanggal Invoice',
                        autoHide: false,
                        width: 'auto',
                        textAlign: 'left',
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bold">'+formatDate(row.value3)+'</span>';
                                txt += "<br />";
                                if (row.value3 != row.value4) {
                                    txt += '<span class="label label-md label-outline-warning label-inline"><i class="flaticon-calendar-with-a-clock-time-tools mr-2 text-warning"></i>'+formatDate(row.value4)+'</span>';
                                }
                                else {
                                    txt += '<span class="label label-md label-outline-success label-inline">TUNAI</span>';
                                }
                            return txt;
                        },
                    },
                    {
                        field: 'value5',
                        title: 'No. Faktur',
                        textAlign: 'left',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bolder">'+row.value5.toUpperCase()+'</span>';
                            return txt;
                        },
                    },
                    {
                        field: 'value6',
                        title: 'Tagihan (Rp)',
                        textAlign: 'left',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bold">'+parseFloat(row.value6).toLocaleString('id-ID', { maximumFractionDigits: 2})+'</span><br />';
                                txt += '<span class="label label-md label-outline-primary label-inline mt-1">Bayar : Rp '+parseFloat(row.value7).toLocaleString('id-ID', { maximumFractionDigits: 2})+'</span><br />';
                                txt += '<span class="label label-md label-outline-warning label-inline mt-1">Sisa : Rp '+parseFloat(row.value8).toLocaleString('id-ID', { maximumFractionDigits: 2})+'</span><br />';
                                return txt;
                        },
                    },
                    {
                        field: 'value9',
                        title: 'Pembayaran (Rp)',
                        textAlign: 'left',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bold">'+parseFloat(row.value9).toLocaleString('id-ID', { maximumFractionDigits: 2})+'</span>';
                                return txt;
                        },
                    },
                ],
            });
        });

        function payment(id) {
            clear();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/AccountReceiveable/GetInvoiceData",
                method: 'POST',
                data: {
                    idInvoice: id,
                },
                success: function(result){
                    if (result != null) {
                        $("#id_invoice").val(result.id);
                        $("#kode_invoice").val(result.kode_invoice.toUpperCase());
                        $("#tanggal_invoice").val(formatDate(result.tanggal_invoice));
                        $("#tanggal_jt").val(formatDate(result.tanggal_jt));
                        $("#nominal_invoice").val(result.sisa_tagihan);
                        $("#nominal_invoice_mask").val(parseFloat(result.sisa_tagihan).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        $("#btnModalPayment").trigger('click');
                    }
                }
            });
        }

        function inputCost(id) {
            $("#sisa_potongan").val(0);
            $("#sisa_potongan_mask").val(0);
            $("#nominal_input_pot").val(0);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/AccountReceiveable/GetCostData",
                method: 'POST',
                data: {
                    idInvoice: id,
                },
                success: function(result){
                    if (result.length > 0) {
                        $("#id_invoice_pot").val(result[0].id);
                        $("#kode_invoice_pot").val(result[0].kode_invoice.toUpperCase());
                        $("#tanggal_invoice_pot").val(formatDate(result[0].tanggal_invoice));
                        $("#tanggal_jt_pot").val(formatDate(result[0].tanggal_jt));
                        $("#nominal_potongan").val(result[0].sisa_potongan);
                        $("#nominal_potongan_mask").val(parseFloat(result[0].sisa_potongan).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        $("#btnModalCost").trigger('click');
                    }
                }
            });
        }

        function ListCost(id) {
            $('#list_cost_invoice tbody').empty();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/AccountReceiveable/GetListCost",
                method: 'POST',
                data: {
                    idInvoice: id,
                },
                success: function(result){
                    if (result.length > 0) {
                        for (var i = 0; i < result.length;i++) {
                            var nominal = parseFloat(result[i].nominal).toLocaleString('id-ID', { maximumFractionDigits: 2});
                            var keteranganCost = result[i].keterangan;
                            var data="<tr>";
                                data +="<td style='text-align:center;'>"+nominal+"</td>";
                                data +="<td style='text-align:left;word-wrap:break-word;min-width:160px;max-width:160px;'>"+ucwords(keteranganCost)+"</td>";
                                data +="</tr>";
                            $("#list_cost_invoice").append(data);
                        }
                        $("#btnCost").trigger('click');
                    }
                }
            });
        }

        function ListPayment(id) {
            $('#list_payment_invoice tbody').empty();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/AccountReceiveable/GetPaymentData",
                method: 'POST',
                data: {
                    idInvoice: id,
                },
                success: function(result){
                    if (result.length > 0) {
                        var cekGiro = cekPembayaranGiro(result);
                        if (cekGiro == "Y") {
                            $(".gr").show();
                        }
                        else {
                            $(".gr").hide();
                        }
                        for (var i = 0; i < result.length;i++) {
                            var nominal = parseFloat(result[i].nominal_bayar).toLocaleString('id-ID', { maximumFractionDigits: 2});
                            var data="<tr>";
                                data +="<td style='display:none;' class='idAr'>"+result[i].id+"</td>";
                                data +="<td style='display:none;' class='idInv'>"+id+"</td>";
                                data +="<td style='text-align:center;' class='KodeAr'>"+result[i].kode_ar.toUpperCase()+"</td>";
                                data +="<td style='text-align:center;'>"+formatDate(result[i].tanggal)+"</td>";
                                data +="<td style='text-align:center;'>"+ucwords(result[i].jenis_pembayaran)+"</td>";
                                data +="<td style='text-align:center;'>"+result[i].rekening.toUpperCase()+"</td>";

                                if (cekGiro == "Y") {
                                    if (result[i].jenis_pembayaran == "giro") {
                                        data +="<td style='text-align:center;'>"+formatDate(result[i].tanggal_jt_giro)+"</td>";
                                    }
                                    else {
                                        data +="<td style='text-align:center;'>-</td>";
                                    }
                                }

                                data +="<td style='text-align:center;'>"+nominal+"</td>";
                                data +="<td style='text-align:left;'>"+result[i].keterangan+"</td>";
                                data += '<td style="text-align:center;"><a class="nav-link CancelPayment" href="#"><i class="nav-icon la la-trash"></i></a></td>';
                                data +="</tr>";
                            $("#list_payment_invoice").append(data);
                        }
                        $("#btnHistory").trigger('click');
                    }
                }
            });
        }

        function cekPembayaranGiro(arr) {
            for (var j=0; j<arr.length; j++) {
                if (arr[j].jenis_pembayaran.match("giro")) return "Y";
            }
            return "N";
        }

        function clear() {
            $("#id_invoice").val("");
            $("#kode_invoice").val("");
            $("#tanggal_invoice").val("");
            $("#tanggal_jt").val("");
            $("#jenis_bayar").val("").trigger("change");
            $("#rekening").val("").trigger("change");
            $("#nominal_invoice").val(0);
            $("#nominal_invoice_mask").val(0);
            $("#nominal_bayar_mask").val(0).trigger("change");
            $("#potongan_tagihan").val(0);
            $("#sisa_tagihan").val(0);
            $("#sisa_tagihan_mask").val(0);
            $("#keterangan").val("");
        }

        $("#btnFull").on("click", function() {
            $("#nominal_bayar").val($("#nominal_invoice").val());
            $("#nominal_bayar_mask").val($("#nominal_invoice_mask").val()).trigger('change');
        });

        $("#btnPayment").on('click', function(e) {
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

            var jenisBayar = $("#jenis_bayar").val();

            if (jenisBayar != "cash" && jenisBayar != "") {
                if ($("#rekening").val() == "") {
                    $('#errRekening').show();
				  	errCount = errCount + 1;
                }
                else {
					$('#errRekening').hide();
				}

                if (jenisBayar == "giro" && jenisBayar != "") {
                    if ($("#tanggal_jt_giro").val() == "") {
                        console.log("test");
                        $('#errJT').show();
                        errCount = errCount + 1;
                    }
                    else {
                        $('#errJT').hide();
                    }
                }
            }

            if ($("#nominal_bayar").val() == "0" || $("#nominal_bayar").val() == "") {
                $('#errNominal').show();
				  	errCount = errCount + 1;
            }
            else {
				$('#errNominal').hide();
			}

			if (errCount == 0) {
                Swal.fire({
                    title: "Input Pembayaran Invoice?",
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
                            url: "/AccountReceiveable/StoreDetail",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idCustomer : "{{$dataCustomer->id}}",
                                Rekening : $("#rekening").val(),
                                JenisPembayaran : $("#jenis_bayar").val(),
                                Tanggal : $("#tanggal_bayar").val(),
                                TanggalJTGiro : $("#tanggal_jt_giro").val(),
                                Keterangan : $("#keterangan").val(),
                                Potongan : $("#potongan_tagihan").val(),
                                Nominal : $("#nominal_bayar").val(),
                                Sisa : $("#sisa_tagihan").val(),
                                idInvoice : $("#id_invoice").val(),
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Pembayaran Berhasil Diinput!.",
                                        "success"
                                    )
                                    clear();
                                    var datatable = $('#list_item').KTDatatable();
                                        datatable.setDataSourceParam('idCustomer', '{{$dataCustomer->id}}');
                                        datatable.reload();
                                    $("#closeModalPayment").trigger('click');
                                    getDataTagihan();
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

        $(".inputNominal").on("change", function() {
            var tagihan = parseFloat($("#nominal_invoice").val());
            var bayar = parseFloat($("#nominal_bayar").val());
            var potongan = parseFloat($("#potongan_tagihan").val());
            var sisa = 0;

            if (bayar > tagihan) {
                Swal.fire(
                    "Peringatan!",
                    "Nominal Pembayaran Melebihi Tagihan!",
                    "warning"
                )
                $("#nominal_bayar_mask").val(0);
                $("#nominal_bayar").val(0);
            }
            else {
                sisa = (parseFloat(tagihan) - parseFloat(bayar));
            }


            if (potongan > 0) {
                if (potongan > sisa) {
                    Swal.fire(
                        "Peringatan!",
                        "Nominal Potongan Melebihi Sisa Tagihan!",
                        "warning"
                    )
                    $("#potongan_tagihan").val(0);
                }
                else {
                    sisa = sisa - potongan;
                }
            }

            $("#sisa_tagihan").val(sisa);
            $("#sisa_tagihan_mask").val(parseFloat(sisa).toLocaleString('id-ID', { maximumFractionDigits: 2}));
        });

        $("#btnAddPot").on('click', function(e) {
			var errCount = 0;

			if($("#nominal_input_pot").val() == "0" || $("#nominal_input_pot").val() == "") {
                Swal.fire(
                    "Peringatan!",
                    "Nominal Potongan Melebihi Sisa Potongan!",
                    "warning"
                )
                errCount = errCount + 1;
            }

			if (errCount == 0) {
                Swal.fire({
                    title: "Simpan Data Potongan?",
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
                            url: "/AccountReceiveable/StoreCost",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idInvoice : $("#id_invoice_pot").val(),
                                potongan : $("#nominal_input_pot").val(),
                                keterangan : $("#keterangan_pot").val(),
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Potongan Berhasil disimpan!.",
                                        "success"
                                    )
                                    $("#id_invoice_pot").val("");
                                    $("#nominal_input_pot").val("");
                                    $("#keterangan_pot").val("");
                                    var datatable = $('#list_item').KTDatatable();
                                        datatable.setDataSourceParam('idCustomer', '{{$dataCustomer->id}}');
                                        datatable.reload();
                                        $("#closeModalCost").trigger('click');
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

        $(".inputNominalPot").on("change", function() {
            var sisaPotongan = parseFloat($("#nominal_potongan").val());
            var potongan = parseFloat($("#nominal_input_pot").val());
            var sisa = 0;

            if (potongan > sisaPotongan) {
                Swal.fire(
                    "Peringatan!",
                    "Nominal Potongan Melebihi Sisa Potongan!",
                    "warning"
                )
                $("#nominal_input_pot").val(0);
            }
            else {
                sisa = (parseFloat(sisaPotongan) - parseFloat(potongan));
            }

            $("#sisa_potongan").val(sisa);
            $("#sisa_potongan_mask").val(parseFloat(sisa).toLocaleString('id-ID', { maximumFractionDigits: 2}));
        });

        function getDataTagihan(idCustomer) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/AccountReceiveable/GetTagihanByCustomer",
                method: 'POST',
                data: {
                    id_customer: '{{$dataCustomer->id}}',
                },
                success: function(result){
                    if (result != null) {
                        $("#ttlInv").val(parseFloat(result.TotalInvoice ?? 0).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        $("#ttlInvJT").val(parseFloat(result.TotalInvoiceJT ?? 0).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        $("#ttlTagihan").val(parseFloat(result.TotalTagihan ?? 0).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        $("#ttlTagihanJT").val(parseFloat(result.TotalTagihanJT ?? 0).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        $("#limit").val(parseFloat(result.limit_customer ?? 0).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                    }
                }
            });
        }

        function cetakInv() {
            var idInv = $("#id_invoice").val();
            window.open('/SalesInvoice/Cetak/' + idInv, '_blank');

        }

        $("#list_item").on("click", "table .checkAll", function(){
            $("#list_item input:checkbox").prop('checked', $(this).prop("checked"));
        });

        $("#btnMass").on('click', function(e) {
            var check = $('.bayarSekaligus:checkbox:checked').length;
            if (check < 1) {
                Swal.fire(
                    "Peringatan!",
                    "Harap pilih invoice yang akan dibayarkan!",
                    "warning"
                )
            }
            else {
                $("#btnModalPaymentMass").trigger('click');
                var invoiceIDs = $("#list_item input:checkbox:checked").map(function(){
                                return $(this).val();
                            }).get();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/AccountReceiveable/SetDataMass",
                    method: 'POST',
                    data: {
                        invoices: invoiceIDs,
                        idCustomer: '{{$dataCustomer->id}}',
                    },
                    success: function(result){
                        if (result != "") {
                            var datatable = $('#list_item_mass').KTDatatable();
                                datatable.setDataSourceParam('idCustomer', '{{$dataCustomer->id}}');
                                datatable.setDataSourceParam('invoices', invoiceIDs);
                                datatable.reload();
                            footerDataMass(invoiceIDs);
                        }
                    }
                });
            }
		});

        $("#btnAlocate").on('click', function(e) {
            e.preventDefault();
            var check = $('#nominal_bayar_mass').val();
            if (check < 1) {
                Swal.fire(
                    "Peringatan!",
                    "Harap input nominal pembayaran!",
                    "warning"
                )
            }
            else {
                var tagihan = parseFloat($("#nominal_invoice_mass").val());
                var sisa = parseFloat($("#sisa_tagihan_mass").val());
                var bayar = parseFloat($("#nominal_bayar_mass").val());
                var potongan = parseFloat($("#potongan_tagihan_mass").val());

                if (bayar > tagihan) {
                    Swal.fire(
                        "Peringatan!",
                        "Nominal Pembayaran Melebihi Tagihan!",
                        "warning"
                    )
                    $("#nominal_bayar_mass_mask").val(0);
                    $("#nominal_bayar_mass").val(0);
                }
                else if (bayar > sisa) {
                    Swal.fire(
                        "Peringatan!",
                        "Nominal Pembayaran Melebihi Sisa Tagihan!",
                        "warning"
                    )
                    $("#nominal_bayar_mass_mask").val(0);
                    $("#nominal_bayar_mass").val(0);
                }
                else {
                    var invoiceIDs = $("#list_item input:checkbox:checked").map(function(){
                                    return $(this).val();
                                }).get();

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "/AccountReceiveable/AlocatePayment",
                        method: 'POST',
                        data: {
                            invoices: invoiceIDs,
                            idCustomer: '{{$dataCustomer->id}}',
                            nominal: $('#nominal_bayar_mass').val()
                        },
                        success: function(result){
                            if (result != "") {
                                var datatable = $('#list_item_mass').KTDatatable();
                                    datatable.setDataSourceParam('idCustomer', '{{$dataCustomer->id}}');
                                    datatable.setDataSourceParam('invoices', invoiceIDs);
                                    datatable.reload();
                                footerDataMass(invoiceIDs);
                            }
                        }
                    });
                }
            }
		});

        function footerDataMass(idInv) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/AccountReceiveable/GetDataMass",
                method: 'POST',
                data: {
                    idInvoice: idInv,
                    idCustomer: '{{$dataCustomer->id}}',
                },
                success: function(result){
                    if (result != "null") {
                        var jmlFaktur = result.jml_faktur;
                        var nominal = result.total_invoice;
                        var sisa = result.total_tagihan;


                        $("#nominal_invoice_mass").val(nominal);
                        $("#nominal_invoice_mass_mask").val(parseFloat(nominal).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                        $("#sisa_tagihan_mass").val(sisa);
                        $("#sisa_tagihan_mass_mask").val(parseFloat(sisa).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                    }
                    else {
                        $("#nominal_invoice_mass").val(0);
                        $("#nominal_invoice_mass_mask").val(parseFloat(0).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                        $("#sisa_tagihan_mass").val(0);
                        $("#sisa_tagihan_mass_mask").val(parseFloat(0).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                    }
                }
            });
        }

        $("#btnPaymentMass").on('click', function(e) {
			var errCount = 0;

			$(".reqMass").each(function(){
				if($(this).val() == "" || $(this).children("option:selected").val() == ""){
				   	$(this).closest('.form-group, input-group').find('.errMass').show();
				  	errCount = errCount + 1;
				}
				else {
					$(this).closest('.form-group, input-group').find('.errMass').hide();
				}
			});

            var jenisBayar = $("#jenis_bayar_mass").val();
            if (jenisBayar != "cash" && jenisBayar != "") {
                if ($("#rekening_mass").val() == "") {
                    $('#errRekeningMass').show();
				  	errCount = errCount + 1;
                }
                else {
					$('#errRekeningMass').hide();
				}

                if (jenisBayar == "giro" && jenisBayar != "") {
                    if ($("#tanggal_jt_giro_mass").val() == "") {
                        $('#errJTMass').show();
                        errCount = errCount + 1;
                    }
                    else {
                        $('#errJTMass').hide();
                    }
                }
            }

			if (errCount == 0) {
                Swal.fire({
                    title: "Submit Pembayaran Invoice?",
                    text: "Apakah data sudah sesuai?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya",
                    cancelButtonText: "Tidak",
                    reverseButtons: false
                }).then(function(result) {
                    if(result.value) {
                        var invoiceIDs = $("#list_item input:checkbox:checked").map(function(){
                                    return $(this).val();
                                }).get();

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            url: "/AccountReceiveable/StoreDetailMass",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idCustomer : "{{$dataCustomer->id}}",
                                Rekening : $("#rekening_mass").val(),
                                JenisPembayaran : $("#jenis_bayar_mass").val(),
                                Tanggal : $("#tanggal_bayar_mass").val(),
                                TanggalJTGiro : $("#tanggal_jt_giro_mass").val(),
                                Keterangan : $("#keterangan_mass").val(),
                                Nominal : $("#nominal_bayar_mass").val(),
                                Sisa : $("#sisa_tagihan_mass").val(),
                                idInvoice : invoiceIDs,
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Pembayaran Berhasil Diinput!.",
                                        "success"
                                    )
                                    clear();
                                    var datatable = $('#list_item').KTDatatable();
                                        datatable.setDataSourceParam('idCustomer', '{{$dataCustomer->id}}');
                                        datatable.reload();
                                    $("#closeModalPaymentMass").trigger('click');
                                    getDataTagihan();
                                    $("#jenis_bayar_mass").val("").trigger("change");
                                    $("#rekening_mass").val("").trigger("change");
                                    $("#nominal_bayar_mass").val(0);
                                    $("#nominal_bayar_mass_mask").val(0);
                                }
                                else if (result == "failAlocate") {
                                    Swal.fire(
                                        "Peringatan!",
                                        "Harap alokasi pembayaran terlebih dahulu!",
                                        "warning"
                                    )
                                }
                                else if (result == "failOverPayment") {
                                    Swal.fire(
                                        "Peringatan!",
                                        "Pembayaran melebihi sisa tagihan!",
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

        $("#list_payment_invoice").on('click', '.CancelPayment', function(e) {
			var idAr = $(this).closest('tr').children('td.idAr').text();
            var idInv = $(this).closest('tr').children('td.idInv').text();
            const noAr = $(this).closest('tr').children('td.KodeAr').text();

			if (idAr != "" && idInv != "") {
                Swal.fire({
                    title: "Batal Pembayaran Invoice ?",
                    text: "Apakah yakin ingin membatalkan Pembayaran Invoice No. : " + noAr + " ?",
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
                            url: "/AccountReceiveable/CancelPayment",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idCustomer : "{{$dataCustomer->id}}",
                                idAr : idAr,
                                idInv : idInv,

                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Pembayaran Berhasil Dibatalkan!.",
                                        "success"
                                    )
                                    clear();
                                    var datatable = $('#list_item').KTDatatable();
                                        datatable.setDataSourceParam('idCustomer', '{{$dataCustomer->id}}');
                                        datatable.reload();
                                    var datatable2 = $('#list_item_lunas').KTDatatable();
                                        datatable2.setDataSourceParam('idCustomer', '{{$dataCustomer->id}}');
                                        datatable2.reload();
                                    getDataTagihan();
                                    ListPayment(idInv);
                                }
                                else if (result == "failNotFound") {
                                    Swal.fire(
                                        "Peringatan!",
                                        "Data Pembayaran tidak dapat ditemukan!",
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

        $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
            var activeTabID = $(e.target).attr('id');
            if (activeTabID == "tab2") {
                var datatablItem = $("#list_item").KTDatatable();
                datatablItem.reload();
            }

            if (activeTabID == "tab3") {
                var datatableLunas = $("#list_item_lunas").KTDatatable();
                datatableLunas.reload();
            }
	    });

    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
