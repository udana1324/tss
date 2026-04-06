@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h6 class="card-title text-white">Ubah Penawaran</h6>
					</div>
                    <form action="{{ route('Quotation.update', $dataQuotation->id) }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            @method('PUT')
                            <div class="row">
								<div class="col-md-6">
									<fieldset>
                                        <legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi Pelanggan / Customer </h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
										<div class="form-group row d-none">
                                            <label class="col-lg-3 col-form-label">Kode Penawaran :</label>
                                            <div class="col-lg-9">
                                                <input type="hidden" value="LOAD" id="mode" />
                                                <input type="text" class="form-control bg-slate-600 border-slate-600 border-1" placeholder="Auto Generated" name="kode_item" id="kode_item" value="{{strtoupper($dataQuotation->no_quotation)}}" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Customer :</label>
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
                                            <label>Alamat Customer :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="hidden" class=" form-control req" name="id_alamat" id="id_alamat" value="{{$dataQuotation->id_alamat}}">
                                                    <textarea class="form-control" name="alamat" id="alamat" style="resize:none;" placeholder="Silahkan Klik Tombol Pilih Alamat" readonly>{{ucwords($dataAlamat->alamat_customer)}}</textarea>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-primary" id="btnAlamat" data-toggle="modal" data-target="#modal_list_alamat">Pilih Alamat</button>
                                                    </div>
                                                </div>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih alamat customer terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Tanggal Penawaran :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <input type="hidden" class="form-control req" name="tanggal_quo" id="tanggal_quo" value="{{$dataQuotation->tanggal_quotation}}">
                                                <input type="text" class="form-control" value="{{\Carbon\Carbon::parse($dataQuotation->tanggal_quotation)->format('d F Y')}}" placeholder="Pilih Tanggal" name="tanggal_quo_picker" id="tanggal_quo_picker" readonly>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal penawaran terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-lg-6 mb-5">
                                                <label>Metode Pembayaran :</label>
                                                <div>
                                                    <div class="radio-inline">
                                                        <label class="radio">
                                                            <input type="radio" id="cash" value="cash" name="metode_bayar" {{ $dataQuotation->metode_pembayaran === "cash" ? "checked" : "" }} />
                                                            <span></span>Cash/Tunai
                                                        </label>
                                                        <label class="radio">
                                                            <input type="radio" id="credit" value="credit" name="metode_bayar" {{ $dataQuotation->metode_pembayaran === "credit" ? "checked" : "" }} />
                                                            <span></span>Kredit
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6" id="durasiJT" @if ($dataQuotation->metode_pembayaran == "cash") style="display:none;" @endif>
    											<label>Durasi Jatuh Tempo (Hari) :</label>
    											<div>
    												<input type="text" class="form-control" maxlength="4" onkeypress="return validasiAngka(event);" name="durasi_jt" id="durasi_jt" value="{{$dataQuotation->durasi_jt}}">
                                                    <span class="form-text text-danger" id="errDurasi" style="display:none;">*Durasi JT Tidak dapat dibawah 1 Hari!</span>
                                                </div>
    										</div>
                                        </div>

                                        <div class="form-group">
                                            <label>Pajak Penjualan :</label>
                                            <div class="no-gutters">
                                                <div class="radio-inline">
                                                    <label class="radio">
                                                    <input type="radio" id="statPpn_n" name="status_ppn" value="N" {{ $dataQuotation->flag_ppn === "N" ? "checked" : "" }} />
                                                    <span></span>Non PPn</label>
                                                    <label class="radio">
                                                    <input type="radio" id="statPpn_y" name="status_ppn" value="Y" {{ $dataQuotation->flag_ppn === "Y" ? "checked" : "" }} />
                                                    <span></span>PPn Excl.</label>
                                                    <label class="radio">
                                                    <input type="radio" id="statPpn_i" name="status_ppn" value="I" {{ $dataQuotation->flag_ppn === "I" ? "checked" : "" }} />
                                                    <span></span>PPn Incl.</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Syarat & Ketentuan :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <textarea class="form-control elastic" id="tnc" name="tnc" rows="3" placeholder="Ketik Syarat & Ketentuan Penawaran Disini atau gunakan Template pada tombol Template">@foreach($dataTerms as $terms){{$terms->terms_and_cond}}@endforeach</textarea>
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
					                	<legend class="text-muted"><h6><i class="fab la-buffer"></i> Rincian Penawaran Barang</h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
										{{-- <div class="form-group">
                                            <label>Nama Barang :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="text" class="form-control detailItem" placeholder="Masukkan Nama Item" name="nama_item" id="nama_item">
        											<span class="form-text text-danger errItem" style="display:none;">*Harap input nama item terlebih dahulu!</span>
                                                    <select class="form-control select2 detailItem" id="product" name="product">
                                                        <option label="Label"></option>
                                                    </select>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-primary" id="btnProduct" data-toggle="modal" data-target="#modal_list_product"><i class="flaticon2-plus"></i></button>
                                                    </div>
                                                    <span class="form-text text-danger errItem" style="display:none;">*Harap pilih item terlebih dahulu!</span>
                                                </div>
                                            </div>
                                        </div> --}}

                                        <div class="form-group">
											<label>Nama Barang :</label>
											<div class="input-group">
        										<div class="col-8 pl-0">
                                                    <input type="text" class="form-control detailItem" placeholder="Masukkan Nama Item" name="nama_item" id="nama_item">
        											<span class="form-text text-danger errItem" style="display:none;">*Harap input nama item terlebih dahulu!</span>
                                                    {{-- <input type="text" id="qtyItem" onkeypress="return validasiDecimal(this,event);" class="form-control text-right detailItem numericVal">
        											<span class="form-text text-danger errItem" style="display:none;">*Harap masukkan Jumlah item terlebih dahulu!</span>
                                                    <span class="form-text text-danger errItemNumeric" style="display:none;">*Jumlah Barang tidak dapat dibawah atau 0!</span> --}}
        										</div>
        										<div class="col-4 pr-0">
        											<select class="form-control select2 detailUnit" id="productUnit" name="productUnit">
                                                        <option label="Label"></option>
                                                        @foreach($dataUnit as $unit)
                                                        <option value="{{$unit->id}}">{{strtoupper($unit->kode_satuan)}} - {{strtoupper($unit->nama_satuan)}}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="form-text text-danger errUnit" style="display:none;">*Harap pilih satuan item terlebih dahulu!</span>
        										</div>
											</div>
										</div>

										{{-- <div class="form-group">
											<label>Jumlah Penawaran Barang :</label>
											<div class="input-group">
        										<div class="col-8 pl-0">
        											<input type="text" id="qtyItem" onkeypress="return validasiDecimal(this,event);" class="form-control text-right detailItem">
        											<span class="form-text text-danger errItem" style="display:none;">*Harap masukkan Jumlah item terlebih dahulu!</span>
        										</div>
        										<div class="col-4 pr-0">
        											<select class="form-control select2 detailUnit" id="productUnit" name="productUnit">
                                                        <option label="Label"></option>
                                                    </select>
                                                    <span class="form-text text-danger errUnit" style="display:none;">*Harap pilih satuan item terlebih dahulu!</span>
        										</div>
											</div>
										</div>

										<div class="form-group hargaJualItem">
											<label>Harga Jual Standard :</label>
											<div class="input-group">
											    <input type="number" id="harga_jual_item" class="form-control text-right hrg" readonly>
												<span class="input-group-append">
												    <button type="button" class="btn btn-primary btn-icon btnHarga" data-popup="tooltip" title="Gunakan">
    												    <i class="la la-check"></i>
    												</button>
    											</span>
    										</div>
										</div>

										<div class="form-group hargaJualItem">
											<label>Harga Jual Terakhir :</label>
											<div class="input-group">
											    <input type="number" id="harga_jual_last" class="form-control text-right hrg" readonly>
												<span class="input-group-append">
    												<button type="button" class="btn btn-primary btn-icon btnHarga" data-popup="tooltip" title="Gunakan">
    												    <i class="la la-check"></i>
    												</button>
    											</span>
    										</div>
										</div> --}}

										<div class="form-group">
											<label class="font-weight-semibold">Harga Jual :</label>
											<input type="text" onkeypress="return validasiDecimal(this,event);" id="harga_jual" min="0" class="form-control text-right detailItem" autocomplete="off">
											<span class="form-text text-danger errItem" style="display:none;">*Harap masukkan harga jual item terlebih dahulu!</span>
										</div>

                                        <div class="form-group">
											<label class="font-weight-semibold">Keterangan Barang :</label>
											<input type="text" id="keterangan_item" class="form-control" autocomplete="off">
										</div>

                                        <div class="form-group row">
											<label class="col-lg-3 col-form-label"></label>
											<div class="col-lg-9">
												<button type="button" class="btn btn-primary font-weight-bold" id="btnAddItem">Tambah List Penawaran</button>
											</div>
										</div>

									</fieldset>
								</div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset>
                                        <legend class="text-muted"><h6><i class="la la-list"></i> List Penawaran Barang</h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>

                                        <div class="datatable datatable-bordered datatable-head-custom" id="list_item"></div>

                                    </fieldset>
                                </div>
                            </div>

                            <br>
							{{-- <div class="row">
								<div class="col-md-6">

								</div>
								<div class="col-md-6">
									<div class="form-group row">
										<label class="col-lg-3 col-form-label">Total Qty</label>
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
										<label class="col-lg-3 col-form-label">Nominal Penawaran</label>
										<div class="col-lg-9">
											<input type="text" value="0" id="dppMask" class="form-control text-right" readonly>
											<input type="hidden" id="dpp" name="dpp" class="form-control text-right" readonly>
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
							</div> --}}

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

                <!-- Modal form list alamat -->
				<div id="modal_list_alamat" class="modal fade">
				    <div class="modal-dialog modal-lg">
					    <div class="modal-content">
						    <div class="modal-header bg-primary">

							    <h5 class="modal-title">List Alamat Customer</h5>
						    </div>
						    <div class="modal-body">
							    <form >
								    <table class="datatable-bordered datatable-head-custom ml-4" id="list_alamat" width="100%">
									    <thead>
										    <tr>
											    <th align="center" style="text-align:center;display:none;">ID</th>
												<th align="center" style="text-align:center;">Alamat</th>
												<th align="center" style="text-align:center;">Jenis Alamat</th>
												<th align="center" style="text-align:center;">PIC</th>
												<th align="center" style="text-align:center;">No. Telp PIC</th>
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

							    <h5 class="modal-title">Ubah Harga Item</h5>
						    </div>
						    <div class="modal-body">
							    <form >
								    <table class="table display" id="list_edit_item" width="100%">
									    <thead>
										    <tr>
											    <th align="center" style="text-align:center;display: none;">Id</th>
											    <th align="center" style="text-align:center;">Item</th>
											    <th align="center" style="text-align:center;">Satuan</th>
											    {{-- <th align="center" style="text-align:center;">Jumlah</th> --}}
											    <th align="center" style="text-align:center;">Harga Jual</th>
                                                <th align="center" style="text-align:center;">Keterangan</th>
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
							    <button type="button" class="btn btn-link" data-dismiss="modal">Tutup</button>
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

        $(document).ready(function () {
            $('#customer').select2({
                allowClear: true,
                placeholder: "Silahkan Pilih Customer Disini"
            });

            $('#product').select2({
                allowClear: true,
                placeholder: "Silahkan Pilih Item Untuk Ditawarkan"
            });

            $('#productUnit').select2({
                allowClear: true,
                placeholder: "Pilih satuan..."
            });

            $('#tanggal_quo_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                autoclose : true,
                format : "dd MM yyyy",
            });


        });

        $("#tanggal_quo_picker").on('change', function() {
            $("#tanggal_quo").val($("#tanggal_quo_picker").data('datepicker').getFormattedDate('yyyy-mm-dd'));
        });

        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        $("#cancel").on('click', function(e) {
            Swal.fire({
                title: "Batal?",
                text: "Apakah anda ingin membatalkan perubahan penawaran barang?",
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
                        url: "/Quotation/RestoreDetail",
                        method: 'POST',
                        data: {
                            idQuotation : '{{$dataQuotation->id}}'
                        },
                        success: function(result){
                            window.location.href = "{{ url('/Quotation') }}";
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

                    if ($('input[name=metode_bayar]:checked').val() == "credit") {
                        if (parseInt($("#durasi_jt").val()) < 1) {
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
                            "Harap Tambahkan Minimum 1 Item Penawaran!.",
                            "warning"
                        )
                        count = parseInt(count) + 1;
                    }

                    if (count == 0) {
                        $("#customer").prop('disabled', false);
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

        $("#customer").on("change", function() {
            //getListProduct
            getCustomerProduct($(this).val());

            //getDefaultAddress
            if ($("#mode").val() == "EDIT") {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/Quotation/GetDefaultAddress",
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
                    url: "/Quotation/ResetDetail",
                    method: 'POST',
                    data: {
                        idQuotation: '{{$dataQuotation->id}}',
                    },
                    success: function(result){
                        var datatable = $('#list_item').KTDatatable();
                            datatable.setDataSourceParam('idQuotation', '{{$dataQuotation->id}}');
                            datatable.setDataSourceParam('mode', 'edit');
                            datatable.reload();
                            //footerDataForm('{{$dataQuotation->id}}');
                    }
                });
            }

            //getCustomerAddress
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/Quotation/GetCustomerAddress",
                method: 'POST',
                data: {
                    id_customer: $(this).val(),
                },
                success: function(result){
                    if (result.length > 0) {
                        $('#list_alamat tbody').empty();
                        if (result.length > 0) {
                            for (var i = 0; i < result.length;i++) {
                                var idAlamat = result[i].id;
                                var alamat = result[i].alamat_customer;
                                var jenisAlamat = result[i].jenis_alamat;
                                var pic = result[i].pic_alamat;
                                var tlpPic = result[i].telp_pic;
                                var data="<tr>";
                                    data +="<td style='text-align:center;display:none;'>"+idAlamat+"</td>";
                                    data +="<td style='text-align:left;word-wrap:break-word;min-width:160px;max-width:160px;'>"+ucwords(alamat)+"</td>";
                                    data +="<td style='text-align:center;'>"+jenisAlamat+"</td>";
                                    data +="<td style='text-align:center;'>"+ucwords(pic)+"</td>";
                                    data +="<td style='text-align:center;'>"+tlpPic+"</td>";
                                    data +="<td style='text-align:center;'><button type='button' data-dismiss='modal' class='btn btn-primary btn-icon select'>Pilih</button></td>";
                                    data +="</tr>";
                                    $("#list_alamat").append(data);
                            }
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
                url: "/Quotation/GetSatuan",
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
                url: "/Quotation/GetDataItem",
                method: 'POST',
                data: {
                    id_product: $("#product option:selected").val(),
                    id_satuan: $(this).val(),
                    id_customer: $("#customer option:selected").val()
                },
                success: function(result){
                    if (result.length > 0) {
                        $("#harga_jual_last").val(result[0].harga_jual_last);
                        $("#harga_jual_item").val(result[0].harga_jual);
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
                url: "/Quotation/GetProductByCustomer",
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

            var kd = $(this).parents('tr:first').find('td:first').text();
            var idCust = $("#customer option:selected").val();
	        var nmCust = $("#customer option:selected").html();
            Swal.fire({
                title: "Tambahkan Data?",
                text: "Apakah anda ingin menambah barang ini pada customer" + namaItem +" ?",
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
                        url: "/Quotation/AddCustomerProduct",
                        method: 'POST',
                        data: {
                            id_item: idItem,
                            id_customer: idCust
                        },
                        success: function(result){
                            Swal.fire(
                                "Berhasil!",
                                "Barang Berhasil ditambahkan ke customer " + nmCust + "!",
                                "success"
                            )
                            getCustomerProduct(idCust);
                            var datatable = $('#list_product').KTDatatable();
                                datatable.setDataSourceParam('id_customer', idCust);
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
                            url: '/Quotation/GetProduct',
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
                        field: 'kode_item',
                        title: 'Kode',
                        autoHide: false,
                        textAlign: 'center',
                        width: 'auto',
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
                        autoHide: false,
                        width: 'auto',
                        template: function(row) {
                            return ucwords(row.nama_item);
                        },
                    },
                    {
                        field: 'nama_merk',
                        title: 'Merk',
                        textAlign: 'center',
                        width: 'auto',
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
                datatable.setDataSourceParam('id_customer', $("#customer option:selected").val());
                datatable.reload();
        });

        $(document).ready(function() {

            var datatable = $('#list_item').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Quotation/GetDetail',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data : {
                                idQuotation : '{{$dataQuotation->id}}',
                                mode: 'edit'
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
                        field: 'value2',
                        title: 'Item',
                        autoHide: false,
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            return row.value2.toUpperCase();
                        },
                    },
                    {
                        field: 'nama_satuan',
                        title: 'Satuan',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            return row.nama_satuan.toUpperCase();
                        },
                    },
                    // {
                    //     field: 'value4',
                    //     title: 'Jumlah',
                    //     textAlign: 'center',
                    //     width: 'auto',
                    //     autoHide: false,
                    //     template: function(row) {
                    //         return parseFloat(row.value4).toLocaleString('id-ID', { maximumFractionDigits: 2});
                    //     },
                    // },
                    {
                        field: 'value5',
                        title: 'Harga Jual',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            var jenisPPn = $('input[name=status_ppn]:checked').val();
                            var persenPPNInclude = (100 + parseFloat("{{$taxSettings->ppn_percentage}}")) / 100;
                            if (jenisPPn == "I") {
                                var hargaMask = parseFloat(row.value5) / parseFloat(persenPPNInclude);
                            }
                            else {
                                var hargaMask = parseFloat(row.value5);
                            }
                            return parseFloat(hargaMask).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'value6',
                        title: 'Keterangan',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                            if (row.value6 != null) {
                                txt += row.value6;
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
                            url: "/Quotation/StoreDetail",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idItem : $("#nama_item").val(),
                                idSatuan : $("#productUnit option:selected").val(),
                                idQuotation : "{{$dataQuotation->id}}",
                                qtyItem : 0,
                                hargaJual : $("#harga_jual").val(),
                                keterangan : $("#keterangan_item").val(),
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Item Berhasil ditambahkan!.",
                                        "success"
                                    )
                                    $("#productUnit").val("").trigger('change'),
                                    // $("#qtyItem").val("");
                                    $("#harga_jual").val("");
                                    $("#nama_item").val("");
                                    // $("#harga_jual_item").val("");
                                    // $("#harga_jual_last").val("");
                                    $("#keterangan_item").val("");
                                    var datatable = $('#list_item').KTDatatable();
                                        datatable.setDataSourceParam('idQuotation', '{{$dataQuotation->id}}');
                                        datatable.setDataSourceParam('mode', 'edit');
                                        datatable.reload();
                                    // footerDataForm('{{$dataQuotation->id}}');
                                }
                                else if (result == "failDuplicate") {
                                    Swal.fire(
                                        "Gagal!",
                                        "Item ini sudah tersedia pada List Penawaran Barang !",
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
                url: "/Quotation/EditDetail",
                method: 'POST',
                data: {
                    idDetail: id,
                    mode:'edit'
                },
                success: function(result){
                    if (result.length > 0) {
                        var qty = parseFloat(result[0].value4);
                        var hargaJual = parseFloat(result[0].value5);
                        var qtyFixed = qty.toString().replace(".", ",");
                        var hargaJualFixed = hargaJual.toString().replace(".", ",");
                        var data = "<tr>";
                            data += "<td style='display:none;'><input type='text' class='form-control' id='idRowEdit' value='"+result[0].id+"' /></td>";
                            data += "<td style='display:none;'><input type='text' class='form-control' id='idSatuanEdit' value='"+result[0].value3+"' /></td>";
                            data += "<td><input type='text' class='form-control' id='idItemEdit' value='"+result[0].value2+"' /></td>";
                            data += "<td style='text-align:center;'>"+result[0].nama_satuan.toUpperCase()+"</td>";
                            // data += "<td style='text-align:center;'><input type='text' class='form-control inputEdit' id='qtyRowEdit' value='"+qtyFixed+"' onkeypress='return validasiDecimal(this,event);' /></td>";
                            data += "<td width='150px' style='text-align:center;'><input type='text' class='form-control inputEdit' onkeypress='return validasiDecimal(this,event);' id='hargaBaru' value='"+hargaJualFixed+"' /></td>";
                            data += "<td width='200px' style='text-align:center;'><input type='text' class='form-control' id='keteranganEdit' value='"+result[0].value6+"' /></td>";
                            data += "</tr>";
                            $('#detil_edit_item').append(data);
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
                        url: "/Quotation/DeleteDetail",
                        method: 'POST',
                        data: {
                            idDetail: id,
                            mode: $("#mode").val()
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
                        datatable.setDataSourceParam('idQuotation', '{{$dataQuotation->id}}');
                        datatable.setDataSourceParam('mode', 'edit');
                        datatable.reload();
                        //footerDataForm('{{$dataQuotation->id}}');
                        if (datatable.getTotalRows() < 1) {
                            $("#customer").attr('disabled', false);
                            $("#mode").val("EDIT");
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
                            url: "/Quotation/UpdateDetail",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idItem : idItem,
                                idSatuan : idSatuan,
                                idQuotation : "{{$dataQuotation->id}}",
                                idDetail : idRow,
                                qtyItem : 0,
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
                                        datatable.setDataSourceParam('idQuotation', '{{$dataQuotation->id}}');
                                        datatable.setDataSourceParam('mode', 'edit');
                                        datatable.reload();
                                        //footerDataForm('{{$dataQuotation->id}}');
                                }
                                else if (result == "failDuplicate") {
                                    Swal.fire(
                                        "Gagal!",
                                        "Item ini sudah tersedia pada List Penawaran Barang !",
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
            }
	    });

        $('input[name=status_ppn]').on('change', function() {
		    var datatable = $('#list_item').KTDatatable();
                datatable.setDataSourceParam('idQuotation', '{{$dataQuotation->id}}');
                datatable.setDataSourceParam('mode', 'edit');
                datatable.reload();
                //footerDataForm('{{$dataQuotation->id}}');
		});

        $(".btnHarga").on("click", function() {
	    	var harga = $(this).closest("div.hargaJualItem").find(".hrg").val();
	    	$("#harga_jual").val(harga);
	    });

        $(document).ready(function() {
            //getTemplateTerms
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/Quotation/GetListTerms",
                method: 'POST',
                data: {
                    target: "penawaran",
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
                url: "/Quotation/GetTerms",
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

        function footerDataForm(idQuotation) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/Quotation/GetDataFooter",
                method: 'POST',
                data: {
                    idQuotation: idQuotation,
                    mode: 'edit'
                },
                success: function(result){
                    if (result != "null") {
                        var subtotal = result.subtotal;
                        var qtyOrder = result.qtyItem;
                        var qtyFixed = qtyOrder;
                        var subtotalFixed = subtotal;
                        var jenisPPn = $('input[name=status_ppn]:checked').val();
                        var persenPPNInclude = (100 + parseFloat("{{$taxSettings->ppn_percentage}}")) / 100;
                        var persenPPNExclude = parseFloat("{{$taxSettings->ppn_percentage}}") / 100;

                        $("#qtyTtl").val(qtyFixed);
                        $("#qtyTtlMask").val(parseFloat(qtyFixed).toLocaleString('id-ID', { maximumFractionDigits: 2}))

                        if (jenisPPn == "I") {
                            subtotalFixed = parseFloat(subtotalFixed) / parseFloat(persenPPNInclude);
                        }

                        var subtotalMask = parseFloat(subtotalFixed).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        $("#dpp").val(subtotalFixed);
                        $("#dppMask").val(subtotalMask);

                        if (jenisPPn != "N") {
                            var ppn = parseFloat(subtotalFixed) * parseFloat(persenPPNExclude);
                            $("#ppn").val(ppn);
                            $("#ppnMask").val(parseFloat(ppn).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        }
                        else {
                            var ppn = 0;
                            $("#ppn").val(ppn);
                            $("#ppnMask").val(parseFloat(ppn).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        }

                        var grandTotal = parseFloat(subtotalFixed) + parseFloat(ppn);
                        $("#gt").val(Math.ceil(grandTotal));
                        $("#gtMask").val(parseFloat(Math.ceil(grandTotal)).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                    }
                    else {
                        $("#qtyTtl").val(0);
                        $("#qtyTtlMask").val(0);
                        $("#dpp").val(0);
                        $("#dppMask").val(0);
                        $("#ppn").val(0);
                        $("#ppnMask").val(0);
                        $("#gt").val(0);
                        $("#gtMask").val(0);
                    }
                }
            });
        }

        $(document).ready(function () {
            $("#customer").val("{{$dataQuotation->id_customer}}").trigger('change');
            //footerDataForm('{{$dataQuotation->id}}');
            $("#mode").val("EDIT");
        });
    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
