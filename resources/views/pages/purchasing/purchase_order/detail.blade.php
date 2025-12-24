@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary text-white header-elements-sm-inline">
						<h5 class="card-title text-white">Detail Pembelian</h5>
					</div>
                    <form action="{{ route('PurchaseOrder.Posting', $dataPurchaseOrder->id) }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}

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
                                                    <input type="text" class="form-control bg-slate-600 border-slate-600 border-1" placeholder="Auto Generated" name="kode_item" id="kode_item" value="{{strtoupper($dataPurchaseOrder->no_po)}}" disabled>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Vendor :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <select class="form-control select2 req" id="supplier" name="supplier" disabled>
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
                                                        {{-- <button type="button" class="btn btn-primary" id="btnAlamat" data-toggle="modal" data-target="#modal_list_alamat">Pilih Alamat</button> --}}
                                                    </div>
                                                </div>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih alamat kirim terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Tanggal Pembelian :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <input type="text" class="form-control req" name="tanggal_po" id="tanggal_po" value="{{\Carbon\Carbon::parse($dataPurchaseOrder->tanggal_po)->format('d F Y')}}"  readonly>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal pembelian terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Tanggal Permintaan Penerimaan Barang :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <input type="text" class="form-control req" name="tanggal_req" id="tanggal_req" value="{{\Carbon\Carbon::parse($dataPurchaseOrder->tanggal_request)->format('d F Y')}}" readonly>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal pembelian terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Batas Waktu Penerimaan Barang :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <input type="text" class="form-control req" name="tanggal_deadline" id="tanggal_deadline" value="{{\Carbon\Carbon::parse($dataPurchaseOrder->tanggal_deadline)->format('d F Y')}}" readonly>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal pembelian terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-lg-6 mb-5">
                                                <label>Metode Pembayaran :</label>
                                                <div>
                                                    <div class="radio-inline">
                                                        <label class="radio">
                                                            <input type="radio" id="cash" disabled="disabled" value="cash" name="metode_bayar" {{ $dataPurchaseOrder->metode_pembayaran === "cash" ? "checked" : "" }} readonly />
                                                            <span></span>Cash/Tunai
                                                        </label>
                                                        <label class="radio">
                                                            <input type="radio" id="credit" disabled="disabled" value="credit" name="metode_bayar" {{ $dataPurchaseOrder->metode_pembayaran === "credit" ? "checked" : "" }} readonly />
                                                            <span></span>Kredit
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6" id="durasiJT" @if ($dataPurchaseOrder->metode_pembayaran == "cash") style="display:none;" @endif>
    											<label>Durasi (Hari) :</label>
    											<div>
    												<input type="text" class="form-control" maxlength="4" onkeypress="return validasiAngka(event);" name="durasi_jt" id="durasi_jt" value="{{$dataPurchaseOrder->durasi_jt}}" readonly>
    											</div>
    										</div>
                                        </div>

                                        <div class="form-group">
                                            <label>Pajak Penjualan :</label>
                                            <div class="no-gutters">
                                                <div class="radio-inline">
                                                    <label class="radio">
                                                    <input type="radio" id="statPpn_y" disabled="disabled" name="status_ppn" value="Y" {{ $dataPurchaseOrder->flag_ppn === "Y" ? "checked" : "" }} readonly />
                                                    <span></span>PPn Excl.</label>
                                                    <label class="radio">
                                                    <input type="radio" id="statPpn_i" disabled="disabled" name="status_ppn" value="I" {{ $dataPurchaseOrder->flag_ppn === "I" ? "checked" : "" }} readonly />
                                                    <span></span>PPn Incl.</label>
                                                    <label class="radio">
                                                    <input type="radio" id="statPpn_n" disabled="disabled" name="status_ppn" value="N" {{ $dataPurchaseOrder->flag_ppn === "N" ? "checked" : "" }} readonly />
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
                                                            <input type="radio" id="percentage" disabled="disabled" value="P" name="jenis_diskon" {{ $dataPurchaseOrder->jenis_diskon === "P" ? "checked" : "" }} />
                                                            <span></span>Persentase
                                                        </label>
                                                        <label class="radio">
                                                            <input type="radio" id="nominal" disabled="disabled" value="N" name="jenis_diskon" {{ $dataPurchaseOrder->jenis_diskon === "N" ? "checked" : "" }} />
                                                            <span></span>Nominal
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-lg-6" id="discPercent" @if ($dataPurchaseOrder->jenis_diskon == "N") style="display:none;" @endif>
    											<label id="txtDiskonP">Persentase (%):</label>
    											<div>
    												<input type="text" class="form-control discount" onkeypress="return validasiAngka(event);" name="disc_percent" id="disc_percent" value="{{$dataPurchaseOrder->persentase_diskon}}" readonly>
                                                </div>
    										</div>
                                            <div class="col-lg-6" id="discNominal" @if ($dataPurchaseOrder->jenis_diskon == "P") style="display:none;" @endif>
    											<label id="txtDiskonN">Nominal (Rp) :</label>
    											<div>
    												<input type="text" class="form-control discount" onkeypress="return validasiAngka(event);" name="disc_nominal" id="disc_nominal" value="{{$dataPurchaseOrder->nominal_diskon}}" readonly>
                                                </div>
    										</div>
    									</div>

                                        <div class="form-group">
                                            <label>Syarat & Ketentuan :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <textarea class="form-control elastic" id="tnc" name="tnc" rows="4" placeholder="Ketik Syarat & Ketentuan Pembelian Disini atau gunakan Template pada tombol Template" readonly>@foreach($dataTerms as $terms){{$terms->terms_and_cond}}@endforeach</textarea>
                                                    <div class="input-group-append">
                                                        {{-- <button type="button" class="btn btn-primary" id="btnTemplate" data-toggle="modal" data-target="#modal_list_terms">Template</button> --}}
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
                                            <label>Down Payment (DP) :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" onkeypress="return validasiDecimal(this,event);" name="dp" id="dp" value="{{ $dataPurchaseOrder->status_po === "draft" ? $dataPurchaseOrder->nominal_dp : number_format($dataPurchaseOrder->nominal_dp, 0, ',', '.') }}" {{ $dataPurchaseOrder->status_po === "draft" ? "" : "readonly" }}>
                                                    <div class="input-group-append" style="display: {{ $dataPurchaseOrder->status_po === 'draft' ? 'block' : 'none' }};">
                                                        <button type="button" value="30" class="btn btn-primary btn-icon btnDP border-white">30%</button>
                                                        <button type="button" value="50" class="btn btn-primary btn-icon btnDP border-white">50%</button>
                                                        <button type="button" value="75" class="btn btn-primary btn-icon btnDP border-white">75%</button>
                                                    </div>
                                                </div>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih alamat customer terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group" style="display: {{ $dataPurchaseOrder->status_po === 'draft' ? 'none' : 'block' }};">
                                            <label>Sisa Down Payment :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" onkeypress="return validasiDecimal(this,event);" name="sisaDp" id="sisaDp" value="{{number_format($dataPurchaseOrder->sisa_dp, 0, ',', '.')}}" {{ $dataPurchaseOrder->status === "draft" ? "" : "readonly" }}>
                                                </div>
                                            </div>
                                        </div>

										<div class="form-group">
                                            <label>Status Pembelian :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="status_po" id="status_po" value="{{strtoupper($dataPurchaseOrder->status_po)}}" readonly>
                                                </div>
                                            </div>
                                        </div>

										<div class="form-group">
											<label>Status Revisi Pembelian Barang :</label>
											<div class="input-group">
        										<div class="col-12 pl-0">
        											<input type="text" id="revisi" class="form-control" value="{{ $dataPurchaseOrder->flag_revisi == "1" ? "Revisi" : "Tidak" }}" readonly>
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
                                        <legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Rincian Pembelian Barang </h6></legend>
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
                                @if($dataPurchaseOrder->status_po == "draft")
                                    <button type="button" class="btn btn-secondary mt-2 mt-sm-0 btnSubmit" id="btn_edit" value="ubah">Ubah Pembelian<i class="flaticon-edit ml-2"></i></button>
                                    @if($hakAkses->approve == "Y")
                                        <button type="button" class="btn btn-light-primary font-weight-bold mr-2 btnSubmit" id="btn_posting" value="posting"> Posting <i class="flaticon-paper-plane-1"></i></button>
                                    @endif

                                @elseif($dataPurchaseOrder->status_po == "posted")
                                    @if($hakAkses->approve == "Y")
                                        @if($rcvCount == 0)
                                        <button type="button" class="btn btn-danger mt-2 mt-sm-0 btnSubmit" id="btn_cancel" value="batal">Batal<i class="flaticon2-cancel ml-2"></i></button>
                                        @endif
                                        @if($rcvCount > 0)
                                        <button type="button" class="btn btn-success mt-2 mt-sm-0 btnSubmit" id="btn_close" value="tutup">Tutup PO<i class="fas fa-file-upload ml-2"></i></button>
                                        @endif
                                    @endif
                                    @if($hakAkses->revisi == "Y")
                                    <button type="button" class="btn btn-warning mt-2 mt-sm-0 btnSubmit" id="btn_revisi" value="revisi">Revisi<i class="fas fa-file-signature ml-2"></i></button>
                                    @endif
                                @endif
                                @if($hakAkses->print == "Y")
									<a type="button" class="btn btn-primary mt-2 mt-sm-0" href='{{route('PurchaseOrder.Cetak', $dataPurchaseOrder->id)}}' target="_blank">Cetak<i class="fas fa-print ml-2"></i></a>
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
            $('#supplier').select2({
                allowClear: true,
                placeholder: "Pilih Vendor"
            });

            $('#product').select2({
                allowClear: true,
                placeholder: "Pilih Barang"
            });
        });


        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        $("#cancel").on('click', function(e) {
            Swal.fire({
                title: "Batal?",
                text: "Anda telah melakukan perubahan, yakin ingin keluar ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    window.location.href = "{{ url('/PurchaseOrder') }}";
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
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

        $(".btnSubmit").on("click", function(e){
            var btn = $(this).val();
            $("#submit_action").val(btn);
            Swal.fire({
                title: ucwords(btn) + " Purchase Order?",
                text: "Apakah yakin ingin melakukan " + ucwords(btn) +" Purchase Order?",
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
                                // var jenisAlamat = result[i].jenis_alamat;
                                // var pic = result[i].pic_alamat;
                                // var tlpPic = result[i].telp_pic;
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
        });

        $("#product").on("change", function() {
            //getDefaultAddress
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/PurchaseOrder/GetDataItem",
                method: 'POST',
                data: {
                    id_product: $(this).val(),
                },
                success: function(result){
                    if (result.length > 0) {
                        $("#harga_beli_item").val(result[0].harga_beli);
                        $("#satuan_item").val(ucwords(result[0].nama_satuan));
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
                            $("#product").append($('<option>', {
                                value:result[i].id,
                                text:result[i].nama_item
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
                                idPurchaseOrder : '{{$dataPurchaseOrder->id}}'
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
                        width: 20,
                        type: 'number',
                        selector: false,
                        textAlign: 'center',
                        visible:false,
                    },
                    {
                        field: 'kode_item',
                        title: 'Nama Barang',
                        autoHide: false,
                        width: 250,
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
                        field: 'qty_order',
                        title: 'Jumlah',
                        width: 'auto',
                        textAlign: 'right',
                        template: function(row) {
                            return parseFloat(row.qty_order).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'outstanding_qty',
                        title: 'Outstanding',
                        width: 'auto',
                        textAlign: 'right',
                        template: function(row) {
                            return parseFloat(row.outstanding_qty).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'nama_satuan',
                        title: 'Satuan',
                        textAlign: 'left',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            return row.nama_satuan.toUpperCase();
                        },
                    },
                    {
                        field: 'harga_beli',
                        title: 'Harga Beli (Rp)',
                        textAlign: 'right',
                        width: 'auto',
                        template: function(row) {
                            var jenisPPn = $('input[name=status_ppn]:checked').val();
                            var persenPPNInclude = (100 + parseFloat("{{$taxSettings->ppn_percentage}}")) / 100;
                            if (jenisPPn == "I") {
                                var hargaMask = parseFloat(row.harga_beli) / parseFloat(persenPPNInclude);
                            }
                            else {
                                var hargaMask = parseFloat(row.harga_beli);
                            }
                            return parseFloat(hargaMask).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'subtotal',
                        title: 'Harga Total (Rp)',
                        width: 'auto',
                        textAlign: 'right',
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
                ],
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
                },
                success: function(result){
                    if (result != "") {
                        var subtotal = result.subtotal;
                        var qtyOrder = result.qtyOrder;
                        var qtyFixed = qtyOrder.toString().replace(".", ",");
                        var subtotalFixed = subtotal.toString().replace(".", ",");
                        var jenisPPn = $('input[name=status_ppn]:checked').val();
                        var persenPPNInclude = (100 + parseFloat("{{$taxSettings->ppn_percentage}}")) / 100;
                        var persenPPNExclude = parseFloat("{{$taxSettings->ppn_percentage}}") / 100;
                        var jenisDisc = "{{$dataPurchaseOrder->jenis_diskon}}";

                        $("#qtyTtl").val(qtyOrder);
                        $("#qtyTtlMask").val(parseFloat(qtyOrder).toLocaleString('id-ID', { maximumFractionDigits: 2}))

                        if (jenisPPn == "I") {
                            subtotalFixed = parseFloat(subtotalFixed) / parseFloat(persenPPNInclude);
                        }

                        var subtotalMask = parseFloat(subtotalFixed).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        $("#dpp").val(subtotalFixed);
                        $("#dppMask").val(subtotalMask);

                        var persenDiskon = 0;
                        var diskonNominal = 0;

                        if(jenisDisc == "P") {
                            persenDiskon = "{{$dataPurchaseOrder->persentase_diskon}}";
                            diskonNominal = parseFloat(subtotalFixed) * (parseFloat(persenDiskon) / 100);
                        }
                        else if(jenisDisc == "N") {
                            diskonNominal = "{{$dataPurchaseOrder->nominal_diskon}}";
                        }

                        if (diskonNominal == 0 || diskonNominal == "") {
                            diskonNominal = 0;
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

        $("#persen_diskon").on("change", function() {
            footerDataForm('{{$dataPurchaseOrder->id}}');
        });

        $(document).ready(function () {
            $("#supplier").val("{{$dataPurchaseOrder->id_supplier}}").trigger('change');
            footerDataForm('{{$dataPurchaseOrder->id}}');
        });
    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
