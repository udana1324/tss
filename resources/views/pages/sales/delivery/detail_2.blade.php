@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h5 class="card-title text-white">Detail Pengiriman</h5>
					</div>
                    <form action="{{ route('Delivery.Posting', $dataDlv->id) }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            <div class="row">
								<div class="col-md-6">
									<fieldset>
										<legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi Pembeli / Customer </h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
										<div class="form-group row d-none">
                                            <label class="col-lg-3 col-form-label">Kode Pengiriman :</label>
                                            <div class="col-lg-9">
                                                <input type="hidden" value="load" id="mode" />
                                                <input type="text" class="form-control bg-slate-600 border-slate-600 border-1" placeholder="Auto Generated" name="kode_delivery" id="kode_delivery" value="{{strtoupper($dataDlv->kode_pengiriman)}}" readonly>
                                            </div>
                                        </div>

										<div class="form-group">
                                            <label>Customer :</label>
                                            <div>
                                                <input type="text" class="form-control" value="{{ucwords($dataDlv->nama_customer)}}" id="customer" name="customer"  />
                                            </div>
                                            <span class="form-text text-danger err" style="display:none;">*Harap pilih customer terlebih dahulu!</span>
                                        </div>

                                        <div class="form-group">
                                            <label>No. Sales Order :</label>
                                            <div>
                                                <input type="text" class="form-control" value="{{strtoupper($dataDlv->no_so)}}" id="salesOrder" name="salesOrder"  />
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>PO Pelanggan :</label>
                                            <div>
                                                <input type="text" class="form-control" id="po_pelanggan" value="{{$dataDlv->no_po_customer}}" readonly>
                                            </div>
                                        </div>

										<div class="form-group">
                                            <label>Alamat Customer :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="hidden" class=" form-control req" name="id_alamat" id="id_alamat" value="{{$dataAlamat->id}}">
                                                    <textarea class="form-control" name="alamat" id="alamat" style="resize:none;" placeholder="Silahkan Pilih Sales Order Terlebih Dahulu" readonly>{{ucwords($dataAlamat->alamat_customer)}}</textarea>
                                                    <div class="input-group-append">
                                                        {{-- <button type="button" class="btn btn-primary" id="btnAlamat" data-toggle="modal" data-target="#modal_list_alamat">Pilih Alamat</button> --}}
                                                    </div>
                                                </div>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih alamat Customer terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Tanggal Surat Jalan :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <input type="hidden" class="form-control req" name="tanggal_sj" id="tanggal_sj" value="{{$dataDlv->tanggal_sj}}">
                                                <input type="text" class="form-control" placeholder="Pilih Tanggal" value="{{ Carbon\Carbon::parse($dataDlv->tanggal_sj)->isoFormat('D MMMM Y') }}" name="tanggal_sj_picker" id="tanggal_sj_picker" readonly>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal surat jalan terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Penggunaan Syarat Dan Ketentuan :</label>
                                            <div>
                                                <div class="radio-inline">
                                                    <label class="radio">
                                                        <input type="radio" id="termsSo" value="termsSo" name="terms_usage" {{ $dataDlv->flag_terms_so == "0" ? "" : "checked" }} disabled="disabled" />
                                                        <span></span>Sales Order
                                                    </label>
                                                    <label class="radio">
                                                        <input type="radio" id="buatTerms" value="buatTerms" name="terms_usage" {{ $dataDlv->flag_terms_so == "0" ? "checked" : "" }} disabled="disabled" />
                                                        <span></span>Buat Baru
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group" id="divTnc">
                                            <label>Syarat & Ketentuan :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <textarea class="form-control elastic" id="tnc" name="tnc" rows="3" readonly>@foreach($dataTerms as $terms){{$terms->terms_and_cond}}@endforeach</textarea>
                                                    <div class="input-group-append">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Metode Pengiriman :</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control"  name="metode_pengiriman" id="metode_pengiriman" value="{{ucwords($dataDlv->metode_kirim)}}" readonly>
                                            </div>
                                        </div>

									</fieldset>
								</div>

								<div class="col-md-6">
									<fieldset>
					                	<legend class="text-muted"><h6><i class="fab la-buffer"></i> Rincian Pengiriman Barang</h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
										<div class="form-group">
                                            <label>Status Surat Jalan :</label>
                                            <div class="input-group">
                                                <input type="text" id="status_dlv" value="{{ucwords($dataDlv->status_pengiriman)}}" class="form-control" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group">
											<label>Status Pengiriman :</label>
											<div class="input-group">
                                                @if ($dataDlv->status_pengiriman == "draft")
                                                <input type="text" id="status_kirim" value="Menunggu Approval" class="form-control" readonly>
                                                @elseif ($dataDlv->status_pengiriman == "posted" && $dataDlv->flag_terkirim == "0")
                                                <input type="text" id="status_kirim" value="Dalam Proses Pengiriman" class="form-control" readonly>
                                                @elseif ($dataDlv->status_pengiriman == "posted" && $dataDlv->flag_terkirim == "1")
                                                <input type="text" id="status_kirim" value="Terkirim oleh {{ucwords($dataDlv->updated_by)}}. (Diterima Oleh : {{$dataDlv->diterima_oleh}})" class="form-control" readonly>
                                                @elseif($dataDlv->flag_terkirim == "1" && $dataDlv->flag_invoiced == "1")
                                                <input type="text" id="status_kirim" value="Sudah dibuat Tagihan" class="form-control" readonly>
                                                @endif
											</div>
										</div>

										<div class="form-group">
											<label>Revisi :</label>
											<div class="input-group">
        										<input type="text" id="revisi" value="{{ $dataDlv->flag_revisi == "1" ? "Revisi" : "Tidak" }}" class="form-control" readonly>
											</div>
                                        </div>

                                        @if ($dataDlv->status_pengiriman == "posted" && $dataDlv->flag_terkirim == "0")
                                        <div class="form-group row" id="divKonfirm">
											<label class="col-lg-3 col-form-label"></label>
											<div class="col-lg-9">
												<button type="button" class="btn btn-primary font-weight-bold" id="btnKonfirm" data-toggle="modal" data-target="#modal_form_konfirmasi">Konfirmasi Pengiriman</button>
											</div>
										</div>
                                        @endif

									</fieldset>
								</div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset>
                                        <legend class="text-muted"><h6><i class="la la-list"></i> List Pengiriman Barang</h6></legend>
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
										<label class="col-lg-3 col-form-label">Total Pengiriman</label>
										<div class="col-lg-9">
											<input type="text"  value="0" id="qtyTtlMask" class="form-control text-center" readonly>
											<input type="hidden" id="qtyTtl" name="qtyTtl" class="form-control text-right" readonly>
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
                                @if($dataDlv->status_pengiriman == "draft")
                                    <button type="button" class="btn btn-secondary mt-2 mt-sm-0 btnSubmit" id="btn_edit" value="ubah">Ubah Pengiriman<i class="flaticon-edit ml-2"></i></button>
                                    @if($hakAkses->approve == "Y")
                                        <button type="button" class="btn btn-light-primary font-weight-bold mr-2 btnSubmit" id="btn_posting" value="posting"> Posting <i class="flaticon-paper-plane-1"></i></button>
                                    @endif

                                @elseif($dataDlv->status_pengiriman == "posted")
                                    <button type="button" class="btn btn-warning mt-2 mt-sm-0 btnSubmit" id="btn_revisi" value="revisi">Revisi<i class="fas fa-file-signature ml-2"></i></button>
                                    <button type="button" class="btn btn-danger mt-2 mt-sm-0 btnSubmit" id="btn_cancel" value="batal">Batal<i class="flaticon2-cancel ml-2"></i></button>
                                    @if($hakAkses->print == "Y")
										<a type="button" class="btn btn-primary mt-2 mt-sm-0" href='{{route('Delivery.Cetak', $dataDlv->id)}}' target="_blank">Cetak<i class="fas fa-print ml-2"></i></a>
									@endif
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal form konfirmasi -->
				<div id="modal_form_konfirmasi" class="modal fade">
				    <div class="modal-dialog">
					    <div class="modal-content">
						    <div class="modal-header bg-primary">

							    <h5 class="modal-title">Konfirmasi Pengiriman</h5>
						    </div>
						    <div class="modal-body">
							    <form >
								    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label">Penerima :</label>
                                                <div class="col-lg-9">
                                                    <input type="text" class="form-control" placeholder="Masukkan Nama Penerima SJ" name="penerima" id="penerima">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
							    </form>

						    </div>

						    <div class="modal-footer">
							    <button type="button" class="btn btn-link" data-dismiss="modal">Tutup</button>
                                <button type="button" class="btn btn-primary btn-sm font-weight-bold" id="btnKonfirmSave">Simpan</button>
						    </div>
					    </div>
				    </div>
			    </div>
                <!-- /form form konfirmasi -->

			</div>
			<!-- /content area -->
@endsection
@section('scripts')
    <script type="text/javascript">

        $(document).ready(function () {
            footerDataForm('{{$dataDlv->id}}');
        });

        $('input[name=terms_usage]').on('change', function() {
			var val = $(this).val();
			if (val == "buatTerms") {
			    $("#divTnc").show();
			}
			else {
                $("#divTnc").hide();
			}
		});

        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        $("#cancel").on('click', function(e) {
            window.location.href = "{{ url('/Delivery') }}";
            // Swal.fire({
            //     title: "Batal?",
            //     text: "Apakah anda ingin membatalkan posting pengiriman barang?",
            //     icon: "warning",
            //     showCancelButton: true,
            //     confirmButtonText: "Ya",
            //     cancelButtonText: "Tidak",
            //     reverseButtons: false
            // }).then(function(result) {
            //     if(result.value) {

            //     }
            //     else if (result.dismiss === "cancel") {
            //         e.preventDefault();
            //     }
            // });
	    });

        $(".btnSubmit").on("click", function(e){
            var btn = $(this).val();
            $("#submit_action").val(btn);
            Swal.fire({
                title: ucwords(btn) + " Pengiriman?",
                text: "Apakah yakin ingin melakukan " + ucwords(btn) +" Pengiriman?",
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
                            url: '/Delivery/GetDetail',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data: {
                                idSalesOrder : "{{$dataDlv->id_so}}",
                                idDelivery: "{{$dataDlv->id}}",
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
                        title: 'Deskripsi Barang',
                        autoHide: false,
                        width: 'auto',
                        textAlign: 'left',
                        template: function(row) {
                            return row.kode_item.toUpperCase() + ' - ' + row.nama_item.toUpperCase();
                        },
                    },
                    {
                        field: 'qty_order',
                        title: 'Jumlah Order',
                        width: 'auto',
                        textAlign: 'right',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.qty_order).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'qty_outstanding',
                        title: 'Outstanding',
                        textAlign: 'right',
                        autoHide: false,
                        width: 'auto',
                        template: function(row) {
                            return parseFloat(row.qty_outstanding).toLocaleString('id-ID', { maximumFractionDigits: 2});
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
                    {
                        field: 'qty_item',
                        title: 'Pengiriman',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.qty_item).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                ],
            });
        });

        $("#btnKonfirmSave").on('click', function(e) {
            var penerima = $("#penerima").val();
			Swal.fire({
                title: "Konfirmasi Pengiriman",
                text: "Apakah Pengiriman telah selesai?",
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
                        url: "/Delivery/ConfirmDelivery",
                        method: 'POST',
                        dataType : 'json',
                        data: {
                            idDelivery : "{{$dataDlv->id}}}",
                            namaPenerima : penerima,
                        },
                        success: function(result){
                            if (result != "false") {
                                Swal.fire(
                                    "Sukses!",
                                    "Konfirmasi Pengiriman Berhasil!.",
                                    "success"
                                )
                                $("#status_kirim").val("Terkirim.(Diterima oleh : " + ucwords(penerima) + ")");
                                $("#divKonfirm").hide();
                                $('#modal_form_konfirmasi').modal('toggle');
                            }
                            else {
                                Swal.fire(
                                    "Gagal!",
                                    "Harap Masukkan Nama Penerima terlebih dahulu!.",
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
		});

        function footerDataForm(idRcv) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/Delivery/GetDataFooter",
                method: 'POST',
                data: {
                    idDelivery: idRcv,
                },
                success: function(result){
                    if (result != "null") {
                        var ttlQty = result.qtyItem;
                        var ttlQtyFixed = ttlQty.toString().replace(".", ",");

                        $("#qtyTtl").val(ttlQtyFixed);
                        $("#qtyTtlMask").val(parseFloat(ttlQtyFixed).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                    }
                    else {
                        $("#qtyTtl").val(0);
                        $("#qtyTtlMask").val(parseFloat(0).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                    }
                }
            });
        }
    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
