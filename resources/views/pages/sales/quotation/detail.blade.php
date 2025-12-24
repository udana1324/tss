@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h6 class="card-title text-white">Detail Penawaran</h6>
					</div>
                    <form action="{{ route('Quotation.Posting', $dataQuotation->id) }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            <div class="row">
								<div class="col-md-6">
									<fieldset>
                                        <legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi Pelanggan / Customer </h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
										<div class="form-group row d-none">
                                            <label class="col-lg-3 col-form-label">Kode Penawaran :</label>
                                            <div class="col-lg-9">
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

                                        <div class="form-group">
                                            <label>Metode Pembayaran :</label>
                                            <div>
                                                <div class="radio-inline">
                                                    <label class="radio">
                                                        <input type="radio" id="cash" value="cash" disabled="disabled" name="metode_bayar" {{ $dataQuotation->metode_pembayaran === "cash" ? "checked" : "" }}  />
                                                        <span></span>Cash/Tunai
                                                    </label>
                                                    <label class="radio">
                                                        <input type="radio" id="credit" value="credit" disabled="disabled" name="metode_bayar" {{ $dataQuotation->metode_pembayaran === "credit" ? "checked" : "" }} />
                                                        <span></span>Kredit
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        @if($dataQuotation->metode_pembayaran == "credit")
                                        <div class="form-group" id="durasiJT">
											<label>Durasi Jatuh Tempo (Hari) :</label>
											<div>
												<input type="text" class="form-control" maxlength="4" onkeypress="return validasiAngka(event);" name="durasi_jt" id="durasi_jt" value="{{$dataQuotation->durasi_jt}}">
											</div>
										</div>
                                        @endif

                                        <div class="form-group">
                                            <label>Syarat & Ketentuan :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <textarea class="form-control elastic" id="tnc" name="tnc" rows="3" readonly>@foreach($dataTerms as $terms){{$terms->terms_and_cond}}@endforeach</textarea>
                                                    <div class="input-group-append">
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
										<div class="form-group">
                                            <label>Status Penawaran :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="status_quotation" id="status_quotation" value="{{strtoupper($dataQuotation->status_quotation)}}" readonly>
                                                </div>
                                            </div>
                                        </div>

										<div class="form-group">
											<label>Status Revisi Penawaran Barang :</label>
											<div class="input-group">
        										<div class="col-12 pl-0">
        											<input type="text" id="revisi" class="form-control" value="{{ $dataQuotation->flag_revisi == "1" ? "Revisi" : "Tidak" }}" readonly>
        											<span class="form-text text-danger errItem" style="display:none;">*Harap masukkan Jumlah order item terlebih dahulu!</span>
        										</div>
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

								</div>
							</div> --}}

                        </div>

                        <div class="card-footer bg-white d-sm-flex justify-content-sm-between align-items-sm-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-light-danger font-weight-bold mr-2" id="cancel">Keluar <i class="flaticon2-cancel icon-sm"></i></button>
                            </div>

                            <div class="mt-2 mt-sm-0">
                                <input type="hidden" id="submit_action" name="submit_action" class="form-control" readonly>
                                @if($dataQuotation->status_quotation == "draft")
                                    <button type="button" class="btn btn-secondary mt-2 mt-sm-0 btnSubmit" id="btn_edit" value="ubah">Ubah Penawaran<i class="flaticon-edit ml-2"></i></button>
                                    @if($hakAkses->approve == "Y")
                                        <button type="button" class="btn btn-light-primary font-weight-bold mr-2 btnSubmit" id="btn_posting" value="posting"> Posting <i class="flaticon-paper-plane-1"></i></button>
                                    @endif

                                @elseif($dataQuotation->status_quotation == "posted")
                                    @if($hakAkses->revisi == "Y")
                                        <button type="button" class="btn btn-warning mt-2 mt-sm-0 btnSubmit" id="btn_revisi" value="revisi">Revisi<i class="fas fa-file-signature ml-2"></i></button>
                                    @endif
                                    {{-- <button type="button" class="btn btn-success mt-2 mt-sm-0 btnSubmit" id="btn_so" value="buat_so">Buat Sales Order<i class="fas fa-file-medical ml-2"></i></button> --}}
                                @endif
                                @if($hakAkses->print == "Y")
									<a type="button" class="btn btn-primary mt-2 mt-sm-0" href='{{route('Quotation.Cetak', $dataQuotation->id)}}' target="_blank">Cetak<i class="fas fa-print ml-2"></i></a>
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
            $('#customer').select2({
                allowClear: true,
                placeholder: "Silahkan Pilih Customer Disini"
            });

            $('#product').select2({
                allowClear: true,
                placeholder: "Silahkan Pilih Item Untuk Ditawarkan"
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
                text: "Apakah anda ingin membatalkan posting penawaran barang?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    window.location.href = "{{ url('/Quotation') }}";
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
	    });

        $(".btnSubmit").on("click", function(e){
            var btn = $(this).val();
            $("#submit_action").val(btn);
            var txt = "";
            var txtTitle = "";
            if (btn == "buat_so") {
                txtTitle = "Buat SO";
                txt = "Apakah yakin ingin membuat SO berdasarkan Penawaran ini?";
            }
            else {
                txtTitle = ucwords(btn) + " Penawaran?";
                txt = "Apakah yakin ingin melakukan " + ucwords(btn) +" Penawaran?";
            }
            Swal.fire({
                title: txtTitle,
                text: txt,
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
                                idQuotation : '{{$dataQuotation->id}}'
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
                        field: 'id_item',
                        title: 'Item',
                        autoHide: false,
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            return row.id_item.toUpperCase();
                        },
                    },
                    {
                        field: 'nama_satuan',
                        title: 'Satuan',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            return row.nama_satuan.toUpperCase();
                        },
                    },
                    {
                        field: 'harga_jual',
                        title: 'Harga Jual',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.harga_jual).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'keterangan',
                        title: 'Keterangan',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                            if (row.keterangan != null) {
                                txt += row.keterangan;
                            }
                            else {
                                txt += "-";
                            }
                            return txt;
                        },
                    },
                ],
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
                    }
                }
            });
        }

        $(document).ready(function () {
            $("#customer").val("{{$dataQuotation->id_customer}}").trigger('change');
            $("#customer").attr('disabled', true);
            // footerDataForm('{{$dataQuotation->id}}');
        });
    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
