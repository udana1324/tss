@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				{{-- <div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h5 class="card-title text-white">Detail Invoice Penjualan</h5>
					</div>
                    <form action="{{ route('SalesInvoice.Posting', $dataInv->id) }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            <div class="row">
								<div class="col-md-6">
									<fieldset>
										<legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi Pembeli / Customer </h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
										<div class="form-group row d-none">
                                            <label class="col-lg-3 col-form-label">Kode Invoice :</label>
                                            <div class="col-lg-9">
                                                <input type="hidden" name="mode" id="mode" value="load" readonly>
                                                <input type="text" class="form-control bg-slate-600 border-slate-600 border-1" placeholder="Auto Generated" name="kode_invoice" id="kode_invoice" value="{{strtoupper($dataInv->kode_invoice)}}" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row d-none">
                                            <label class="col-lg-3 col-form-label">Nomor Tukar Faktur Invoice :</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control bg-slate-600 border-slate-600 border-1" placeholder="Auto Generated" name="tf" id="tf" value="{{strtoupper($dataInv->kode_tf) ?? '-'}}" readonly>
                                            </div>
                                        </div>

										<div class="form-group">
                                            <label>Customer :</label>
                                            <div>
                                                <input type="text" class="form-control" id="customer" name="customer" value="{{strtoupper($dataInv->nama_customer)}}" readonly />
                                            </div>
                                            <span class="form-text text-danger err" style="display:none;">*Harap pilih customer terlebih dahulu!</span>
                                        </div>

                                        <div class="form-group">
                                            <label>No. Sales Order :</label>
                                            <div>
                                                <input type="text" class="form-control" id="SalesOrder" name="SalesOrder" value="{{strtoupper($dataInv->no_so)}}" readonly />
                                            </div>
                                        </div>

										<div class="form-group">
                                            <label>Alamat Pelanggan :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <textarea class="form-control" name="alamat" id="alamat" style="resize:none;" readonly>{{ucwords($dataAlamat->alamat_customer)}}</textarea>
                                                </div>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih alamat customer terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Tanggal Invoice :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <input type="text" class="form-control" name="tanggal_inv_picker" id="tanggal_inv_picker" readonly>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal invoice terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Rekening Perusahaan :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <input type="text" class="form-control" id="companyAccount" name="companyAccount" value="{{strtoupper($dataInv->nama_bank).' - '.$dataInv->nomor_rekening.' - '.ucwords($dataInv->atas_nama)}}" readonly />
                                            </div>
                                        </div>

                                        <div class="form-group">
											<label>Tenor Tagihan :</label>
											<div class="input-group">
        										<div class="col-8 pl-0">
        											<input type="text" id="durasiJT" maxlength="3" name="durasiJT" value="{{$dataInv->durasi_jt}}" class="form-control" readonly />
        											<span class="form-text text-danger errItem" style="display:none;">*Harap masukkan durasi Tenor invoice terlebih dahulu!</span>
                                                </div>

        										<div class="col-4 pr-0">
        											<input type="text" id="hari" value="Hari" class="form-control" readonly>
        										</div>
											</div>
										</div>

                                        <div class="form-group">
                                            <label>Tanggal Jatuh Tempo :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <input type="text" class="form-control" name="tgl_jt_picker" id="tgl_jt_picker" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Pajak Penjualan :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <input type="hidden" class="form-control" name="stat_ppn" id="stat_ppn" value="{{$dataInv->flag_ppn}}" readonly>
                                                @if ($dataInv->flag_ppn == "I")
                                                <input type="text" class="form-control" name="stat_ppnMask" id="stat_ppnMask" value="PPn Incl." readonly>
                                                @elseif ($dataInv->flag_ppn == "Y")
                                                <input type="text" class="form-control" name="stat_ppnMask" id="stat_ppnMask" value="PPn Excl." readonly>
                                                @else
                                                <input type="text" class="form-control" name="stat_ppnMask" id="stat_ppnMask" value="Non PPn" readonly>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Metode Pembayaran :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <input type="text" class="form-control" name="metode_bayar" id="metode_bayar" value="{{strtoupper($dataInv->metode_pembayaran)}}" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Diskon (%):</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <input type="text" class="form-control" name="persen_diskon" id="persen_diskon" value="{{$dataInv->persentase_diskon}}" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Penggunaan Syarat Dan Ketentuan :</label>
                                            <div>
                                                <div class="radio-inline">
                                                    <label class="radio">
                                                        <input type="radio" id="termsSo" value="termsSo" name="terms_usage" {{ $dataInv->flag_terms_so == "0" ? "" : "checked" }} disabled="disabled" />
                                                        <span></span>Sales Order
                                                    </label>
                                                    <label class="radio">
                                                        <input type="radio" id="buatTerms" value="buatTerms" name="terms_usage" {{ $dataInv->flag_terms_so == "0" ? "checked" : "" }} disabled="disabled" />
                                                        <span></span>Buat Baru
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group" id="divTnc">
                                            <label>Syarat & Ketentuan :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <textarea class="form-control" id="tnc" name="tnc" rows="3" readonly>@foreach($dataTerms as $terms){{$terms->terms_and_cond}}@endforeach</textarea>
                                                    <div class="input-group-append">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

									</fieldset>
								</div>

								<div class="col-md-6">
									<fieldset>
					                	<legend class="text-muted"><h6><i class="fab la-buffer"></i> Rincian Invoice</h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>

                                        <div class="form-group divDp" style="display: {{ $dataInv->dp === "0" ? "none" : "block" }}">
                                            <label>Sisa Down Payment : </label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="hidden" class="form-control" name="sisaDp" id="sisaDp" value="{{$dataInv->sisa_dp}}" readonly>
                                                    <input type="text" class="form-control" onkeypress="return validasiDecimal(this,event);" name="sisaDpMask" id="sisaDpMask" value="{{number_format($dataInv->sisa_dp, 0, ',', '.')}}" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group divDp" style="display: {{ $dataInv->dp === "0" ? "none" : "block" }}">
                                            <label>Penggunaan Down Payment (DP) :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" onkeypress="return validasiDecimal(this,event);" name="dp" id="dp" value="{{number_format($dataInv->dp, 0, ',', '.')}}" readonly>
                                                </div>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih alamat customer terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Status Invoice :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="status_invoice" id="status_invoice" value="{{strtoupper($dataInv->status_invoice)}}" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Status Revisi :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="text" id="revisi" class="form-control" value="{{ $dataInv->flag_revisi == "1" ? "Revisi" : "Tidak" }}" readonly>
                                                </div>
                                            </div>
                                        </div>

									</fieldset>
								</div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset>
                                        <legend class="text-muted"><h6><i class="la la-list"></i> List Surat Jalan</h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>

                                        <div class="datatable datatable-bordered datatable-head-custom" id=""></div>

                                    </fieldset>
                                </div>
                            </div>

                            <br>
							<div class="row">
								<div class="col-md-6">

								</div>
								<div class="col-md-6">
									<div class="form-group row">
										<label class="col-lg-3 col-form-label">Total Penjualan</label>
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
                                <input type="hidden" id="submit_action" name="submit_action" class="form-control" readonly>
                                @if($dataInv->status_invoice == "draft")
                                    <button type="button" class="btn btn-secondary mt-2 mt-sm-0 btnSubmit" id="btn_edit" value="ubah">Ubah Invoice<i class="flaticon-edit ml-2"></i></button>
                                    @if($hakAkses->approve == "Y")
                                        <button type="button" class="btn btn-light-primary font-weight-bold mr-2 btnSubmit" id="btn_posting" value="posting"> Posting <i class="flaticon-paper-plane-1"></i></button>
                                    @endif

                                @elseif($dataInv->status_invoice == "posted")
                                    @if($hakAkses->approve == "Y")
                                        <button type="button" class="btn btn-warning mt-2 mt-sm-0 btnSubmit" id="btn_revisi" value="revisi">Revisi<i class="fas fa-file-signature ml-2"></i></button>
                                    @endif
                                    @if($hakAkses->print == "Y")
										<a type="button" class="btn btn-primary mt-2 mt-sm-0" href='{{route('SalesInvoice.Cetak', $dataInv->id)}}' target="_blank">Cetak<i class="fas fa-print ml-2"></i></a>
									@endif
                                @endif
                            </div>
                        </div>
                    </form>
                </div> --}}

                <!-- Modal form detail delivery -->
				<div id="modal_detail_delivery" class="modal fade">
				    <div class="modal-dialog modal-xl">
					    <div class="modal-content">
						    <div class="modal-header bg-primary">
							    <h5 class="modal-title text-white">Perincian barang</h5>
						    </div>
						    <div class="modal-body">
                                <div class="row align-items-center mb-7 ">
                                    <div class="col-lg-4">
                                        <div class="input-icon">
                                            <input type="text" class="form-control" placeholder="Search..." id="list_product_search_query"/>
                                            <span>
                                                <i class="flaticon2-search-1 text-muted"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Search Form-->
                                <!--end: Search Form-->
                                <!--begin: Datatable-->

                                <div class="datatable datatable-bordered datatable-head-custom" id="list_item_detail"></div>

						    </div>

						    <div class="modal-footer">
							    <button type="button" class="btn btn-link" data-dismiss="modal">Tutup</button>
						    </div>
					    </div>
				    </div>
			    </div>
                <!-- /form detail delivery -->

                <div class="card card-custom position-relative overflow-hidden">
                    <form action="{{ route('SalesInvoice.Posting', $dataInv->id) }}" class="form-horizontal" id="form_add" method="POST">
                        {{ csrf_field() }}
    					<!--begin::Invoice header-->
    					<div class="row justify-content-center py-4 px-4 py-md-18 px-md-0 bg-primary ribbon ribbon-top ribbon-ver">
                            @if ($dataInv->status_invoice == "draft")
                                <div class="ribbon-target ribbon-xl bg-warning" style="top: -2px; right: 50px;">{{strtoupper($dataInv->status_invoice)}}<br></div>
                            @elseif ($dataInv->status_invoice == "batal")
                                <div class="ribbon-target ribbon-xl bg-danger" style="top: -2px; right: 50px;">{{strtoupper($dataInv->status_invoice)}}<br></div>
                            @elseif ($dataInv->status_invoice == "posted")
                                <div class="ribbon-target ribbon-xl bg-success" style="top: -2px; right: 50px;">{{strtoupper($dataInv->status_invoice)}}<br></div>
                            @elseif ($dataInv->status_invoice == "revisi")
                                <div class="ribbon-target ribbon-xl bg-warning" style="top: -2px; right: 50px;">{{strtoupper($dataInv->status_invoice)}}<br></div>
                            @endif
						<div class="col-md-9">
    							<div class="d-flex justify-content-between align-items-md-center flex-column flex-md-row">
    								<div class="d-flex flex-column px-0 order-2 order-md-1">
    								    <span class="d-flex flex-column font-size-h5 font-weight-bold text-white">
    										<span>{{strtoupper($dataPreference->nama_pt ?? "-")}}</span>
    										<span>{{strtoupper($dataPreference->alamat ?? "-")}}</span>
    									</span>
    								</div>
    								<div class="order-1 order-md-2">
    								    <h1 class="display-3 font-weight-bolder text-white">FAKTUR PENJUALAN</h1><br>
        							    <span class="font-weight-bolder font-size-h3 text-white mr-5 mb-2">#{{strtoupper($dataInv->kode_invoice)}}</span>
        							    @if($dataInv->flag_revisi == "1")
        							    <span class="label label-xl label-warning label-pill label-inline mb-2">REVISI</span>
        							    @endif
        							    <br><h3 class="font-weight-bolder text-white">#{{strtoupper($dataInv->no_so)}}</h3>
    								</div>
    							</div>
    						</div>
    					</div>
    					<!--end::Invoice header-->
    					<div class="row justify-content-center py-4 px-4 py-md-15">
    						<div class="col-lg-10">
    							<!--begin::Invoice body-->
    							<div class="row pb-10">
    								<div class="col-md-3 border-right-md">
    									<!--begin::Invoice To-->
    									<div class="text-dark-50 font-size-lg font-weight-bold mb-3">Tagihan Untuk :</div>
    									<div class="font-size-lg font-weight-bold mb-10">
    									    <span class="font-weight-boldest">{{strtoupper($dataInv->nama_customer)}}</span>
    									    <br>{{ucwords($dataAlamat->alamat_customer)}}
    									</div>
    									<!--end::Invoice To-->

    									<!--begin::Tanggal Invoice-->
    									<div class="text-dark-50 font-size-lg font-weight-bold mb-3">Tanggal Invoice</div>
    									<div class="font-size-lg font-weight-bold mb-10">{{\Carbon\Carbon::parse($dataInv->tanggal_invoice)->format('d F Y')}}</div>
    									<!--end::Tanggal Invoice-->

    									@php
    									$tenor = '';
    									if($dataInv->metode_pembayaran == 'credit'){
    									    $tenor = ' '.$dataInv->durasi_jt.' Hari';
    									}
    									@endphp

    									<!--begin::Metode Pembayaran-->
    									<div class="text-dark-50 font-size-lg font-weight-bold mb-3">Metode Pembayaran</div>
    									<div class="font-size-lg font-weight-bold mb-10">{{strtoupper($dataInv->metode_pembayaran).$tenor}}</div>
    									<!--end::Metode Pembayaran-->

    									<!--begin::Tanggal Jatuh Tempo-->
    									<div class="text-dark-50 font-size-lg font-weight-bold mb-3">Tanggal Jatuh Tempo</div>
    									<div class="font-size-lg font-weight-bold">{{\Carbon\Carbon::parse($dataInv->tanggal_jt)->format('d F Y')}}</div>
    									<!--end::Tanggal Jatuh Tempo-->
    								</div>

    							    <div class="col-md-9">
                                        <div class="datatable datatable-bordered datatable-head-custom" id="list_item"></div>
                                    </div>
    							</div>
    							<!--end::Invoice body-->

    							<!--begin::Invoice footer-->
    							<div class="row">
    								<div class="col-md-5 border-top pt-7 pb-5 pb-md-9">
    									<div class="d-flex flex-column flex-md-row">
    										<div class="d-flex flex-column">
    											<div class="font-weight-bolder font-size-h6 mb-3">{{strtoupper($dataInv->nama_bank)}}</div>
    											<div class="d-flex justify-content-between font-size-lg mb-3">
    												<span class="font-weight-bold mr-15">Atas Nama :</span>
    												<span class="text-right">{{ucwords($dataInv->atas_nama)}}</span>
    											</div>
    											<div class="d-flex justify-content-between font-size-lg mb-3">
    												<span class="font-weight-bold mr-15">Nomor Rekening:</span>
    												<span class="text-right">{{$dataInv->nomor_rekening}}</span>
    											</div>

    											<div class="font-size-lg mt-15">
    												<span class="font-weight-boldest mr-15">Catatan Faktur Penjualan:</span><br>
    											    <span class="mr-15">@foreach($dataTerms as $terms){{$terms->terms_and_cond}}@endforeach</span>
    											</div>


    										</div>
    									</div>
    								</div>
    								<div class="col-md-7 pt-md-25">
    								    <div class="align-items-center justify-content-between max-w-350px position-relative ml-auto pb-5">
    										<div class="font-weight-boldest font-size-h5">Total Penjualan</div>
    										<div class="text-right d-flex flex-column">
    											<span class="font-weight-boldest font-size-h3 line-height-sm" id="qty_new"></span>
    										</div>
    									</div>

    									<div class="align-items-center justify-content-between max-w-350px position-relative ml-auto pb-5">
    										<div class="font-weight-boldest font-size-h5">Total DPP</div>
    										<div class="text-right d-flex flex-column">
    											<span class="font-weight-boldest font-size-h3 line-height-sm" id="dpp_nominal_new"></span>
    										</div>
    									</div>

    									<div class="align-items-center justify-content-between max-w-350px position-relative ml-auto pb-5">
    										<div class="font-weight-boldest font-size-h5">Diskon <span id="disc_percent_new"></span></div>
    										<div class="text-right d-flex flex-column">
    											<span class="font-weight-boldest font-size-h3 line-height-sm" id="disc_nominal_new"></span>
    										</div>
    									</div>

    									<div class="align-items-center justify-content-between max-w-350px position-relative ml-auto pb-5">
    										<div class="font-weight-boldest font-size-h5">Total PPN</div>
    										<div class="text-right d-flex flex-column">
    											<span class="font-weight-boldest font-size-h3 line-height-sm" id="ppn_nominal_new"></span>
    										</div>
    									</div>

    									<div class="bg-primary rounded d-flex align-items-center justify-content-between text-white max-w-350px position-relative ml-auto p-7">
    										<!--begin::Shape-->
    										<div class="position-absolute opacity-30 top-0 right-0">
    											<span class="svg-icon svg-icon-2x svg-logo-white svg-icon-flip">
                    							<!--begin::Svg Icon | path:/metronic/theme/html/demo1/dist/assets/media/svg/shapes/abstract-8.svg-->
                    							{{ Metronic::getSVG("media/svg/shapes/abstract-8.svg") }}
                    							<!--end::Svg Icon-->
    											</span>
    										</div>
    										<!--end::Shape-->
    										<div class="font-weight-boldest font-size-h5">Grand Total</div>
    										<div class="text-right d-flex flex-column">
    											<span class="font-weight-boldest font-size-h3 line-height-sm" id="gt_nominal_new"></span>
    											@php
    											if ($dataInv->flag_ppn == "I"){
    											    $txtPPN = 'Sudah termasuk PPn';
    											}
    											elseif ($dataInv->flag_ppn == "Y"){
    											    $txtPPN = 'Sudah termasuk PPn';
    											}
    											else{
    											    $txtPPN = 'Tanpa PPN';
    											}
    											@endphp
    											<span class="font-size-sm">{{$txtPPN}}</span>
    										</div>
    									</div>
    								</div>
    							</div>
    							<!--end::Invoice footer-->
    						</div>
    					</div>
    					<!-- begin: Invoice action-->
    					<div class="row justify-content-center border-top py-4 px-4 py-md-14 px-md-0">
    					    <div class="d-flex justify-content-between align-items-center col-10">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-light-danger font-weight-bold mr-2" id="cancel">Keluar <i class="flaticon2-cancel icon-sm"></i></button>
                                </div>

                                <div class="mt-2 mt-sm-0">
                                    <input type="hidden" id="submit_action" name="submit_action" class="form-control" readonly>
                                    @if($dataInv->status_invoice == "draft")
                                        <button type="button" class="btn btn-secondary mt-2 mt-sm-0 btnSubmit" id="btn_edit" value="ubah">Ubah Invoice<i class="flaticon-edit ml-2"></i></button>
                                        @if($hakAkses->approve == "Y")
                                            <button type="button" class="btn btn-light-primary font-weight-bold mr-2 btnSubmit" id="btn_posting" value="posting"> Posting <i class="flaticon-paper-plane-1"></i></button>
                                        @endif

                                    @elseif($dataInv->status_invoice == "posted")
                                        @if($hakAkses->approve == "Y")
                                            <button type="button" class="btn btn-danger mt-2 mt-sm-0 btnSubmit" id="btn_batal" value="batal">Batal<i class="fas fa-file-signature ml-2"></i></button>
                                        @endif
                                        @if($hakAkses->revisi == "Y")
                                            <button type="button" class="btn btn-warning mt-2 mt-sm-0 btnSubmit" id="btn_revisi" value="revisi">Revisi<i class="fas fa-file-signature ml-2"></i></button>
                                        @endif
                                        @if($hakAkses->print == "Y")
        									<a type="button" class="btn btn-primary mt-2 mt-sm-0" href='{{route('SalesInvoice.Cetak', $dataInv->id)}}' target="_blank">Cetak<i class="fas fa-print ml-2"></i></a>
        								@endif
                                    @endif
                                </div>
                            </div>
                        </div>
    					<!-- end: Invoice action-->
    				</form>
				</div>

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
            $("#tanggal_inv_picker").val(formatDate('{{$dataInv->tanggal_invoice}}'));
            $("#tgl_jt_picker").val(formatDate('{{$dataInv->tanggal_jt}}'));
            footerDataForm('{{$dataInv->id}}');
        });

        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        $("#cancel").on('click', function(e) {
            var txt = "";
            var titleTxt = "";
            var status = "{{$dataInv->status_invoice}}";
            if (status == "draft") {
                titleTxt = "Batal?";
                txt = "Apakah anda ingin membatalkan posting invoice penjualan?";
            }
            else {
                titleTxt = "Keluar?";
                txt = "Apakah anda ingin kembali ke halaman utama?";
            }

            Swal.fire({
                title: titleTxt,
                text: txt,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    window.location.href = "{{ url('/SalesInvoice') }}";
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
	    });

        $(".btnSubmit").on("click", function(e){
            var btn = $(this).val();
            $("#submit_action").val(btn);
            if (btn == "batal") {
                Swal.fire({
                    title: "Pembatalan Invoice Penjualan",
                    //text: "Apakah yakin ingin melakukan Pembatalan Invoice Penjualan?",
                    html: "Apakah yakin ingin melakukan Pembatalan Invoice Penjualan?" + "<br /><br />" +
                    '<button type="button" id="btn_batalINV" onclick="batalInvoice(' + "'batalINV'" + ')"  class="btn btn-warning mt-2 mt-sm-0">' + 'Batal Hanya Invoice' + '</button><br /><br />' +
                    '<button type="button" id="btn_batalSO"  onclick="batalInvoice(' + "'batalSO'" + ')" class="btn btn-danger mt-2 mt-sm-0">' + 'Batal hingga Sales Order' + '</button>',
                    icon: "warning",
                    showCancelButton: true,
                    showConfirmButton: false,
                    cancelButtonText: "Tidak",
                    reverseButtons: false
                });
            }
            else {
                Swal.fire({
                    title: ucwords(btn) + " Invoice Penjualan?",
                    text: "Apakah yakin ingin melakukan " + ucwords(btn) +" Invoice Penjualan?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya",
                    cancelButtonText: "Tidak",
                    reverseButtons: false
                }).then(function(result) {
                    if(result.value) {
                        e.preventDefault();
                        $("#form_add").off("submit").submit();
                    }
                    else if (result.dismiss === "cancel") {
                        $("html, body").animate({ scrollTop: 0 }, "slow");
                        e.preventDefault();
                    }
                });
            }
		});

        function batalInvoice(aksiBatal) {
            console.log(aksiBatal);
            $("#submit_action").val(aksiBatal);
            swal.clickConfirm();
            if (aksiBatal == "batalINV" || aksiBatal == "batalSO") {
                $("#form_add").off("submit").submit();
            }
        }

        $(document).ready(function() {

            var datatable = $('#list_item').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/SalesInvoice/GetDetail',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data: {
                                idInvoice: "{{$dataInv->id}}",
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
                        field: 'ViewDetail',
                        title: '',
                        sortable: false,
                        width: 50,
                        overflow: 'visible',
                        autoHide: false,
                        textAlign: 'center',
                        template: function(row) {
                            var txtAction = "<a href='#' class='btn btn-icon btn-light btn-sm' data-toggle='modal' data-target='#modal_detail_delivery' title='Detail' onclick='viewDetailItem("+row.id_sj+");return false;'>";
                                txtAction += "<i class='la la-search'></i>";
                                txtAction += "</a>";

                            return txtAction;
                        },
                    },
                    {
                        field: 'kode_pengiriman',
                        title: 'Kode Pengiriman',
                        autoHide: false,
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            var txtAction = "<p class='font-weight-bolder font-size-lg pt-2'>"+row.kode_pengiriman.toUpperCase()+"</p>";
                            return txtAction;
                        },
                    },
                    {
                        field: 'tanggal_sj',
                        title: 'Tanggal Surat Jalan',
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            if (row.tanggal_sj != null) {
                                var txtAction = "<p class='font-weight-bolder font-size-lg pt-2'>"+formatDate(row.tanggal_sj)+"</p>";
                            }
                            else {
                                var txtAction = "-";
                            }
                            return txtAction;
                        },
                    },
                    {
                        field: 'qty_sj',
                        title: 'Jumlah Pengiriman',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            var txtAction = "<p class='font-weight-bolder font-size-lg pt-2'>"+parseFloat(row.qty_sj).toLocaleString('id-ID', { maximumFractionDigits: 2})+"</p>";
                            return txtAction;
                        },
                    },
                    {
                        field: 'subtotal_sj',
                        title: 'Subtotal(Rp)',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            var jenisPPn = $("#stat_ppn").val();
                            var persenPPNInclude = (100 + parseFloat("{{$taxSettings->ppn_percentage}}")) / 100;
                            if (jenisPPn == "I") {
                                var subtotalMask = parseFloat(row.subtotal_sj) / parseFloat(persenPPNInclude);
                            }
                            else {
                                var subtotalMask = parseFloat(row.subtotal_sj);
                            }
                            return parseFloat(subtotalMask).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                ],
            });
        });

        $(document).ready(function() {

            var datatable = $('#list_item_detail').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/SalesInvoice/GetDetailDelivery',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
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
                        type: 'number',
                        selector: false,
                        textAlign: 'center',
                        visible:false,
                    },
                    {
                        field: 'kode_item',
                        title: 'Kode Item',
                        autoHide: false,
                        width: 70,
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
                        title: 'Nama Item',
                        autoHide: false,
                        width: 200,
                        textAlign: 'center',
                        template: function(row) {
                            return row.nama_item.toUpperCase();
                        },
                    },
                    {
                        field: 'qty_item',
                        title: 'Jumlah Kirim',
                        textAlign: 'right',
                        template: function(row) {
                            return parseFloat(row.qty_item).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'nama_satuan',
                        title: 'Satuan',
                        textAlign: 'left',
                        template: function(row) {
                            return row.nama_satuan.toUpperCase();
                        },
                    },
                    {
                        field: 'harga_jual',
                        title: 'Harga(Rp)',
                        textAlign: 'right',
                        template: function(row) {
                            return parseFloat(row.harga_jual).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'subtotal_sj',
                        title: 'Subtotal(Rp)',
                        textAlign: 'center',
                        template: function(row) {
                            return parseFloat(row.subtotal_sj).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                ],
            });
        });

        function viewDetailItem(id) {

            var datatable = $('#list_item_detail').KTDatatable();
                datatable.setDataSourceParam('idDelivery', id);
                datatable.setDataSourceParam('idSo', '{{$dataInv->id_so}}');
                datatable.reload();

        }

        function footerDataForm(idInv) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/SalesInvoice/GetDataFooter",
                method: 'POST',
                data: {
                    idInvoice: idInv,
                },
                success: function(result){
                    if (result != "null") {
                        var ttlQty = result.qtyInv;
                        var ttlQtyFixed = ttlQty.toString().replace(".", ",");
                        var subtotal = result.subtotalInv;
                        var subtotalFixed = subtotal.toString().replace(".", ",");
                        var jenisPPn = '{{$dataInv->flag_ppn}}';
                        var persenPPNInclude = (100 + parseFloat("{{$taxSettings->ppn_percentage}}")) / 100;
                        var persenPPNExclude = parseFloat("{{$taxSettings->ppn_percentage}}") / 100;
                        var jenisDisc = '{{$dataInv->jenis_diskon}}';

                        $("#qtyTtl").val(ttlQtyFixed);
                        $("#qtyTtlMask").val(parseFloat(ttlQtyFixed).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                        $("#qty_new").html(parseFloat(ttlQtyFixed).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                        if (jenisPPn == "I") {
                            subtotalFixed = parseFloat(subtotalFixed) / parseFloat(persenPPNInclude);
                        }

                        var subtotalMask = parseFloat(subtotalFixed).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        $("#dpp").val(subtotalFixed);
                        $("#dppMask").val(subtotalMask);

                        $("#dpp_nominal_new").html(parseFloat(subtotalFixed).toLocaleString('id-ID', { maximumFractionDigits: 2}));


                        var persenDiskon = 0;
                        var diskonNominal = 0;

                        if(jenisDisc == "P") {
                            persenDiskon = $("#value_diskon").val();
                            diskonNominal = parseFloat(subtotalFixed) * (parseFloat(persenDiskon) / 100);
                        }
                        else if(jenisDisc == "N") {
                            diskonNominal = $("#value_diskon").val();
                        }

                        var persenDiskon = 0;
                        var diskonNominal = 0;

                        if(jenisDisc == "P") {
                            persenDiskon = "{{$dataInv->persentase_diskon}}";
                            diskonNominal = parseFloat(subtotalFixed) * (parseFloat(persenDiskon) / 100);
                            $("#disc_percent_new").show();
                        }
                        else if(jenisDisc == "N") {
                            diskonNominal = "{{$dataInv->nominal_diskon}}";
                            $("#disc_percent_new").hide();
                        }

                        $("#discNominal").val(diskonNominal);
                        $("#discNominalMask").val(parseFloat(diskonNominal).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                        $("#disc_percent_new").html(parseFloat("{{$dataInv->persentase_diskon}}").toLocaleString('id-ID', { maximumFractionDigits: 2}) + "%");
                        $("#disc_nominal_new").html(parseFloat(diskonNominal).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                        if (jenisPPn != "N") {
                            var ppn = (parseFloat(subtotalFixed) - parseFloat(diskonNominal)) * parseFloat(persenPPNExclude);
                            $("#ppn").val(ppn);
                            $("#ppnMask").val(parseFloat(ppn).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                            $("#ppn_nominal_new").html(parseFloat(ppn).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        }
                        else {
                            var ppn = 0;
                            $("#ppn").val(ppn);
                            $("#ppnMask").val(parseFloat(ppn).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                            $("#ppn_nominal_new").html(parseFloat(ppn).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        }

                        var grandTotal = (parseFloat(subtotalFixed) - parseFloat(diskonNominal)) + parseFloat(ppn);
                        $("#gt").val(Math.ceil(grandTotal));
                        $("#gtMask").val(parseFloat(Math.ceil(grandTotal)).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                        $("#gt_nominal_new").html(parseFloat(Math.ceil(grandTotal)).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                    }
                    else {
                        $("#qtyTtl").val(0);
                        $("#qtyTtlMask").val(parseFloat(0).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                        $("#qty_new").html("0");
                        $("#disc_percent_new").html("0");
                        $("#disc_nominal_new").html("0");
                        $("#dpp_nominal_new").html("0");
                        $("#ppn_nominal_new").html("0");
                        $("#gt_nominal_new").html("0");
                    }
                }
            });
        }
    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
