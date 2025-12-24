@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h6 class="card-title text-white">Detail Pesanan Penjualan</h6>
					</div>
                    <form action="{{ route('SalesOrderInternal.Posting', $dataSalesOrderInternal->id) }}" class="form-horizontal" id="form_add" method="POST" enctype="multipart/form-data">
					    <div class="card-body">
                            {{ csrf_field() }}
                            <div class="row">
								<div class="col-md-6">
									<fieldset>
                                        <legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi Pembeli / Customer </h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
										<div class="form-group row">
                                            <label class="col-lg-4 col-form-label">Kode Penjualan :</label>
                                            <div class="col-lg-8">
                                                <input type="hidden" value="load" id="mode" />
                                                <label class="col-form-label">{{strtoupper($dataSalesOrderInternal->no_so)}}</label>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label">Customer :</label>
                                            <div class="col-lg-8">
                                                <label class="col-form-label">{{$dataCustomer->nama_customer}}</label>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label">PO Pelanggan :</label>
                                            <div class="col-lg-5">
                                                <label class="col-form-label">{{$dataSalesOrderInternal->no_po_customer}}</label>
                                            </div>
                                            <div class="col-lg-3 text-right" style="display: {{ $dataSalesOrderInternal->path_po === '' ? 'none' : 'block' }};">
                                                <button type="button" class="btn btn-primary mr-2" id="btnPo" data-toggle="modal" data-target="#modal_upload_po">Lihat PO <i class="flaticon2-upload icon-sm"></i></button>
                                            </div>
                                        </div>

										<div class="form-group row">
                                            <label class="col-lg-4 col-form-label">Alamat Customer:</label>
                                            <div class="col-lg-8">
                                                    <p class="col-form-label">{{ucwords($dataAlamat->alamat_customer)}}</p>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label">Tanggal Penjualan :</label>
                                            <div class="col-lg-8">
                                                <label class="col-form-label">{{\Carbon\Carbon::parse($dataSalesOrderInternal->tanggal_so)->format('d F Y')}}</label>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label">Tanggal Permintaan Pengiriman Barang :</label>
                                            <div class="col-lg-8">
                                                <label class="col-form-label">{{\Carbon\Carbon::parse($dataSalesOrderInternal->tanggal_request)->format('d F Y')}}</label>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label">Metode Pembayaran :</label>
                                            <div class="col-lg-8">
                                                <label class="col-form-label">{{ $dataSalesOrderInternal->metode_pembayaran === "cash" ? "Cash/Tunai" : "Kredit" }}</label>
                                            </div>
                                        </div>

                                        <div class="form-group row {{ $dataSalesOrderInternal->metode_pembayaran === "cash" ? "d-none" : "" }}">
                                            <label class="col-lg-4 col-form-label">Durasi Jatuh Tempo (Hari) :</label>
                                            <div class="col-lg-8">
                                                <label class="col-form-label">{{$dataSalesOrderInternal->durasi_jt}}</label>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label">Pajak Penjualan :</label>
                                            <div class="col-lg-8">
                                                @if ($dataSalesOrderInternal->flag_ppn == "N")
                                                <label class="col-form-label">Non PPn</label>
                                                @elseif ($dataSalesOrderInternal->flag_ppn == "Y")
                                                <label class="col-form-label">PPn Excl.</label>
                                                @else
                                                <label class="col-form-label">PPn Incl.</label>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-lg-6 mb-5">
                                                <label>Diskon :</label>
                                                <div>
                                                    <div class="radio-inline">
                                                        <label class="radio">
                                                            <input type="radio" id="percentage" disabled="disabled" value="P" name="jenis_diskon" {{ $dataSalesOrderInternal->jenis_diskon === "P" ? "checked" : "" }}  />
                                                            <span></span>Persentase
                                                        </label>
                                                        <label class="radio">
                                                            <input type="radio" id="nominal" disabled="disabled" value="N" name="jenis_diskon" {{ $dataSalesOrderInternal->jenis_diskon === "N" ? "checked" : "" }}  />
                                                            <span></span>Nominal
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-6" id="discPercent" @if ($dataSalesOrderInternal->jenis_diskon == "N") style="display:none;" @endif >
                                                <label id="txtDiskonP">Persentase (%):</label>
                                                <div>
                                                    <input type="text" class="form-control discount" onkeypress="return validasiAngka(event);" name="disc_percent" id="disc_percent" value="{{number_format($dataSalesOrderInternal->persentase_diskon, 2, ',', '.')}}" disabled>
                                                </div>
                                            </div>
                                            <div class="col-lg-6" id="discNominalDiv" @if ($dataSalesOrderInternal->jenis_diskon == "P") style="display:none;" @endif >
                                                <label id="txtDiskonN">Nominal (Rp) :</label>
                                                <div>
                                                    <input type="text" class="form-control discount" onkeypress="return validasiAngka(event);" name="disc_nominal" id="disc_nominal" value="{{number_format($dataSalesOrderInternal->nominal_diskon, 2, ',', '.')}}" disabled>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label">Metode Pengiriman :</label>
                                            <div class="col-lg-8">
                                                @if($dataSalesOrderInternal->metode_kirim == "delivery")
                                                <label class="col-form-label">Kirim</label>
                                                @elseif($dataSalesOrderInternal->metode_kirim == "pickup")
                                                <label class="col-form-label">Ambil Sendiri</label>
                                                @elseif($dataSalesOrderInternal->metode_kirim == "ekspedisi")
                                                <label class="col-form-label">Ekspedisi</label>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label">Catatan :</label>
                                            <div class="col-lg-8">
                                                @foreach($dataTerms as $terms)
                                                    <li><label class="col-form-label">{{ $terms->terms_and_cond }}</label></li>
                                                @endforeach
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
                                            <label>Down Payment (DP) :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" onkeypress="return validasiDecimal(this,event);" name="dp" id="dp" value="{{ $dataSalesOrderInternal->status_so === "draft" ? $dataSalesOrderInternal->nominal_dp : number_format($dataSalesOrderInternal->nominal_dp, 0, ',', '.') }}" {{ $dataSalesOrderInternal->status_so === "draft" ? "" : "readonly" }}>
                                                    <div class="input-group-append" style="display: {{ $dataSalesOrderInternal->status_so === 'draft' ? 'block' : 'none' }};">
                                                        <button type="button" value="30" class="btn btn-primary btn-icon btnDP border-white">30%</button>
                                                        <button type="button" value="50" class="btn btn-primary btn-icon btnDP border-white">50%</button>
                                                        <button type="button" value="75" class="btn btn-primary btn-icon btnDP border-white">75%</button>
                                                    </div>
                                                </div>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih alamat customer terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group" style="display: {{ $dataSalesOrderInternal->status_so === 'draft' ? 'none' : 'block' }};">
                                            <label>Sisa Down Payment :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" onkeypress="return validasiDecimal(this,event);" name="sisaDp" id="sisaDp" value="{{number_format($dataSalesOrderInternal->sisa_dp, 0, ',', '.')}}" {{ $dataSalesOrderInternal->status === "draft" ? "" : "readonly" }}>
                                                </div>
                                            </div>
                                        </div>

										<div class="form-group">
                                            <label>Status Penjualan :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="status_so" id="status_so" value="{{strtoupper($dataSalesOrderInternal->status_so)}}" readonly>
                                                </div>
                                            </div>
                                        </div>

										<div class="form-group">
											<label>Status Revisi Penjualan Barang :</label>
											<div class="input-group">
        										<div class="col-12 pl-0">
        											<input type="text" id="revisi" class="form-control" value="{{ $dataSalesOrderInternal->flag_revisi == "1" ? "Revisi" : "Tidak" }}" readonly>
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
                                <input type="hidden" id="submit_action" name="submit_action" class="form-control" readonly>
                                @if($dataSalesOrderInternal->status_so == "draft")
                                    <button type="button" class="btn btn-secondary btnSubmit" id="btn_edit" value="ubah">Ubah Pesanan<i class="flaticon-edit ml-2"></i></button>
                                    @if($hakAkses->approve == "Y")
                                    <button type="button" class="btn btn-light-primary font-weight-bold btnSubmit" id="btn_posting" value="posting"> Posting <i class="flaticon-paper-plane-1"></i></button>
                                    @endif
                                @elseif($dataSalesOrderInternal->status_so == "posted")
                                    <button type="button" class="btn btn-light-warning btnSubmit" id="btn_revisi" value="revisi">Revisi<i class="fas fa-file-signature ml-2"></i></button>
                                    <button type="button" class="btn btn-light-danger btnSubmit" id="btn_cancel" value="batal">Batal<i class="flaticon2-cancel ml-2"></i></button>
                                @endif
                                @if($hakAkses->print == "Y")
								    <a type="button" class="btn btn-light btn-text-primary btn-hover-text-primary font-weight-bold" href='{{route('SalesOrderInternal.Cetak', $dataSalesOrderInternal->id)}}' target="_blank">Cetak SO<i class="fas fa-print ml-2 text-primary"></i></a>
								    @if($dataSalesOrderInternal->status_so != "draft" || $dataSalesOrderInternal->status_so != "batal")
    								    @if($dataSalesOrderInternal->nominal_dp > 0)
                                        <a type="button" class="btn btn-outline-primary" href='{{route('SalesOrderInternal.CetakInvDP', $dataSalesOrderInternal->id)}}' target="_blank">Cetak Invoice DP<i class="fas fa-print ml-2"></i></a>
                                        @endif
                                        <a type="button" class="btn btn-primary" href='{{route('SalesOrderInternal.CetakInvPelunasan', $dataSalesOrderInternal->id)}}' target="_blank">Cetak Invoice Pelunasan<i class="fas fa-print ml-2"></i></a>
                                        <a type="button" class="btn btn-primary" href='{{route('SalesOrderInternal.CetakInvPerforma', $dataSalesOrderInternal->id)}}' target="_blank">Cetak Proforma Invoice<i class="fas fa-print ml-2"></i></a>
                                    @endif
								@endif
                            </div>
                        </div>
                        <!-- Horizontal form upload po -->
                        <div id="modal_upload_po" class="modal fade">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">

                                        <h5 class="modal-title">File PO</h5>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group row">
                                            <label class="col-xl-3 col-lg-3 col-form-label text-right">File PO</label>
                                            <div class="col-lg-9 col-xl-6">
                                                <div class="image-input image-input-outline" id="file_po">
                                                    <div class="image-input-wrapper" style="background-image: url({{asset('documents/sales_order/'.$dataSalesOrderInternal->path_po)}})"></div>

                                                    </label>
                                                </div>
                                                <span class="form-text text-muted">*Allowed file types: png, jpg, jpeg, pdf</span>
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

			</div>
			<!-- /content area -->
@endsection
@section('scripts')
    <script type="text/javascript">

        $(document).ready(function () {

            var imgItem = new KTImageInput('file_po');

        });

        $(".btnDP").on('click', function() {
			var gt = $("#gt").val();
			var persenDp = $(this).val();
			if (gt != "0" && gt != "") {
				var persentaseDp = (parseFloat(gt) * parseFloat(persenDp))/100;
				$("#dp").val(persentaseDp);
			}
		});

		$("#dp").on('change', function(e) {
			var nominal = parseFloat($(this).val());
			var gt = parseFloat($("#gt").val());
			var selisih = gt - nominal;
			if (nominal >= gt) {
				alert("Nominal Down Payment tidak dapat melebih Nominal Pesanan!!!")
				$(this).val(0);
			}
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

        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        $("#cancel").on('click', function(e) {
            Swal.fire({
                title: "Batal?",
                text: "Apakah anda ingin kembali ke daftar pesanan penjualan?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    window.location.href = "{{ url('/SalesOrderInternal') }}";
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
	    });

        $(".btnSubmit").on("click", function(e){
            var btn = $(this).val();
            $("#submit_action").val(btn);
            Swal.fire({
                title: ucwords(btn) + " Sales Order?",
                text: "Apakah yakin ingin melakukan " + ucwords(btn) +" Sales Order?",
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
                            url: '/SalesOrderInternal/GetDetail',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data : {
                                idSalesOrderInternal : '{{$dataSalesOrderInternal->id}}'
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
                        width: 410,
                        autoHide: false,
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
                        field: 'qty_item',
                        title: 'Jumlah',
                        textAlign: 'right',
                        autoHide: false,
                        width: 90,
                        template: function(row) {
                            return parseFloat(row.qty_item).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'nama_satuan',
                        title: 'Satuan',
                        textAlign: 'center',
                        width: 85,
                        autoHide: false,
                        template: function(row) {
                            return row.nama_satuan.toUpperCase();
                        },
                    },
                    {
                        field: 'qty_outstanding',
                        title: 'Outstanding',
                        width: 'auto',
                        textAlign: 'right',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.qty_outstanding).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'harga_jual',
                        title: 'Harga Jual',
                        textAlign: 'right',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            var jenisPPn = $('input[name=status_ppn]:checked').val();
                            var persenPPNInclude = (100 + parseFloat("{{$taxSettings->ppn_percentage}}")) / 100;
                            if (jenisPPn == "I") {
                                var hargaMask = parseFloat(row.harga_jual) / parseFloat(persenPPNInclude);
                            }
                            else {
                                var hargaMask = parseFloat(row.harga_jual);
                            }
                            return parseFloat(hargaMask).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'subtotal',
                        title: 'Subtotal Item',
                        textAlign: 'right',
                        width: 'auto',
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
                            url: "/SalesOrderInternal/StoreDetail",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idItem : $("#product option:selected").val(),
                                idSo : "{{$dataSalesOrderInternal->id}}",
                                qtyItem : $("#qtyItem").val(),
                                hargaJual : $("#harga_jual").val(),
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
                                    $("#harga_jual").val("");
                                    $("#satuan_item").val("");
                                    var datatable = $('#list_item').KTDatatable();
                                        datatable.setDataSourceParam('idSalesOrderInternal', '{{$dataSalesOrderInternal->id}}');
                                        datatable.reload();
                                    footerDataForm('{{$dataSalesOrderInternal->id}}');
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
                url: "/SalesOrderInternal/EditDetail",
                method: 'POST',
                data: {
                    idDetail: id,
                },
                success: function(result){
                    $('#product').find('option:not(:first)').remove();
                    if (result.length > 0) {
                        var qty = parseFloat(result[0].qty_item);
                        var hargaJual = parseFloat(result[0].harga_jual);
                        var qtyFixed = qty.toString().replace(".", ",");
                        var hargaJualFixed = hargaJual.toString().replace(".", ",");
                        var kodeItem = "";
                        if (result[0].value_spesifikasi != null) {
                            kodeItem = '('+result[0].value_spesifikasi+')'+result[0].kode_item.toUpperCase();
                        }
                        else {
                            kodeItem = result[0].kode_item.toUpperCase();
                        }
                        var data = "<tr>";
                            data += "<td style='display:none;'><input type='text' class='form-control' id='idRowEdit' value='"+result[0].id+"' /></td>";
                            data += "<td style='display:none;'><input type='text' class='form-control' id='idItemEdit' value='"+result[0].id_item+"' /></td>";
                            data += "<td style='text-align:center;'>"+ kodeItem + ' - ' + result[0].nama_item.toUpperCase()+"</td>";
                            data += "<td style='text-align:center;'>"+result[0].nama_satuan.toUpperCase()+"</td>";
                            data += "<td style='text-align:center;'><input type='text' class='form-control inputEdit' id='qtyRowEdit' value='"+qtyFixed+"' onkeypress='return validasiDecimal(this,event);' /></td>";
                            data += "<td width='150px' style='text-align:center;'><input type='text' class='form-control inputEdit' onkeypress='return validasiDecimal(this,event);' id='hargaBaru' value='"+hargaJualFixed+"' /></td>";
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
                        url: "/SalesOrderInternal/DeleteDetail",
                        method: 'POST',
                        data: {
                            idDetail: id
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
                        datatable.setDataSourceParam('idSalesOrderInternal', '{{$dataSalesOrderInternal->id}}');
                        datatable.reload();
                        footerDataForm('{{$dataSalesOrderInternal->id}}');
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
	     	var qty = $("#qtyRowEdit").val();
	     	var hargaBaru = $("#hargaBaru").val();

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
                            url: "/SalesOrderInternal/UpdateDetail",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idItem : idItem,
                                idSo : "{{$dataSalesOrderInternal->id}}",
                                idDetail : idRow,
                                qtyItem : qty,
                                hargaJual : hargaBaru,
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Item Berhasil diubah!.",
                                        "success"
                                    )
                                    var datatable = $('#list_item').KTDatatable();
                                        datatable.setDataSourceParam('idSalesOrderInternal', '{{$dataSalesOrderInternal->id}}');
                                        datatable.reload();
                                        footerDataForm('{{$dataSalesOrderInternal->id}}');
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
            else {
                Swal.fire(
                    "Gagal!",
                    "Terdapat kolom kosong, harap mengisi kolom kosong terlebih dahulu !",
                    "warning"
                )
            }
	    });


        function footerDataForm(idSo) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/SalesOrderInternal/GetDataFooter",
                method: 'POST',
                data: {
                    idSo: idSo,
                },
                success: function(result){
                    if (result != "null") {
                        var subtotal = result.subtotal;
                        var qtyItem = result.qtyItem;
                        var qtyFixed = qtyItem;
                        var subtotalFixed = subtotal;
                        var persenDiskon = $("#persen_diskon").text();
                        var jenisPPn = "{{$dataSalesOrderInternal->flag_ppn}}";
                        var persenPPNInclude = (100 + parseFloat("{{$taxSettings->ppn_percentage}}")) / 100;
                        var persenPPNExclude = parseFloat("{{$taxSettings->ppn_percentage}}") / 100;
                        var jenisDisc = "{{$dataSalesOrderInternal->jenis_diskon}}";

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
                            persenDiskon = "{{$dataSalesOrderInternal->persentase_diskon}}";
                            diskonNominal = parseFloat(subtotalFixed) * (parseFloat(persenDiskon) / 100);
                        }
                        else if(jenisDisc == "N") {
                            diskonNominal = "{{$dataSalesOrderInternal->nominal_diskon}}";
                        }

                        if (diskonNominal == 0) {
                            $("#discNominalMask").val("0");
                        }
                        else {
                            $("#discNominalMask").val(parseFloat(diskonNominal).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        }

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
                }
            });
        }

        $(document).ready(function () {
            var metodeKirim = "{{$dataSalesOrderInternal->metode_kirim}}";
            if (metodeKirim == "ekspedisi") {
                $("#ekspedisi").val("{{$dataSalesOrderInternal->jenis_kirim}}").trigger('change');
            }
            else {
                $("#blokEkspedisi").hide();
            }
            footerDataForm('{{$dataSalesOrderInternal->id}}');
            $("#mode").val("edit");
        });
    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
