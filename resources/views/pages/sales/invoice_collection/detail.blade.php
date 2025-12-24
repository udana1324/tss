@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h5 class="card-title text-white">Ubah Tukar Faktur</h5>
					</div>
                    <form action="{{ route('SalesInvoiceCollection.Posting', $dataCollection->id) }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            <div class="row">
								<div class="col-md-6">
									<fieldset>
										<legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi Pembeli / Pelanggan </h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
										<div class="form-group">
                                            <label>No. Tukar Faktur :</label>
                                            <div>
                                                <input type="hidden" value="load" id="mode">
                                                <input type="text" class="form-control form-control-solid" placeholder="Auto Generated" name="kode_tf" id="kode_tf" value="{{strtoupper($dataCollection->kode_tf)}}" readonly>
                                            </div>
                                        </div>

										<div class="form-group">
                                            <label>Nama Pelanggan :</label>
                                            <div>
                                                <input type="text" class="form-control form-control-solid" value="{{$dataCollection->nama_customer}}" id="customer" name="customer">
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih nama pelanggan terlebih dahulu!</span>
                                            </div>
                                        </div>

										<div class="form-group">
                                            <label>Alamat Pelanggan :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="hidden" class=" form-control req" name="id_alamat" id="id_alamat" value="{{$dataCollection->id_alamat}}">
                                                    <textarea class="form-control form-control-solid" name="alamat" id="alamat" style="resize:none;" readonly>{{ucwords($dataCollection->alamat_customer)}}</textarea>
                                                </div>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih alamat pelanggan terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Nomor Rekening :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <input type="text" class="form-control form-control-solid" id="companyAccount" name="companyAccount" value="{{strtoupper($dataCollection->nama_bank).' - '.$dataCollection->nomor_rekening.' - '.ucwords($dataCollection->atas_nama)}}" readonly />
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Tanggal Tukar Faktur :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <input type="text" class="form-control form-control-solid" name="tanggal_tf_picker" id="tanggal_tf_picker" readonly>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal Tukar Faktur terlebih dahulu!</span>
                                            </div>
                                        </div>

									</fieldset>
								</div>

								<div class="col-md-6">
									<fieldset>
					                	<legend class="text-muted"><h6><i class="fab la-buffer"></i> Rincian Tukar Faktur</h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
										<div class="form-group">
                                            <label>Status :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="text" class="form-control form-control-solid" name="status" id="status" value="{{strtoupper($dataCollection->status)}}" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Status Revisi :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="text" id="revisi" class="form-control form-control-solid" value="{{ $dataCollection->flag_revisi == "1" ? "Revisi" : "Tidak" }}" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
											<label>Status Tukar Faktur :</label>
											<div class="input-group input-group-solid">
                                                @if ($dataCollection->status == "draft")
                                                <input type="text" id="status_tf" value="Menunggu Approval" class="form-control" readonly>
                                                @elseif ($dataCollection->status == "posted" && $dataCollection->flag_approved == "0")
                                                <input type="text" id="status_tf" value="Dalam Proses Tukar Faktur" class="form-control" readonly>
                                                @elseif ($dataCollection->status == "posted" && $dataCollection->flag_approved == "1")
                                                <input type="text" id="status_tf" value="Dikonfirmasi Oleh {{$dataCollection->updated_by}}. (Diterima oleh : {{$dataCollection->pic_penerima}})" class="form-control" readonly>
                                                @endif
											</div>
										</div>

                                        @if ($dataCollection->status == "posted" && $dataCollection->flag_approved == "0")
                                        <div class="form-group row" id="divKonfirm">
											<label class="col-lg-3 col-form-label"></label>
											<div class="col-lg-9">
												<button type="button" class="btn btn-primary font-weight-bold" id="btnKonfirm" data-toggle="modal" data-target="#modal_form_konfirmasi">Konfrimasi Tukar Faktur</button>
											</div>
										</div>
                                        @endif

									</fieldset>
								</div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset>
                                        <legend class="text-muted"><h6><i class="la la-list"></i> List Faktur</h6></legend>
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
										<label class="col-lg-3 col-form-label">Grand Total</label>
										<div class="col-lg-9">
											<input type="text" value="0" id="nominalMask" class="form-control text-right" readonly>
											<input type="hidden" id="nominal" name="nominal" class="form-control text-right" readonly>
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
                                @if($dataCollection->status == "draft")
                                    <button type="button" class="btn btn-secondary mt-2 mt-sm-0 btnSubmit" id="btn_edit" value="ubah">Ubah Invoice<i class="flaticon-edit ml-2"></i></button>
                                    @if($hakAkses->approve == "Y")
                                        <button type="button" class="btn btn-light-primary font-weight-bold mr-2 btnSubmit" id="btn_posting" value="posting"> Posting <i class="flaticon-paper-plane-1"></i></button>
                                    @endif

                                @elseif($dataCollection->status == "posted")
                                    @if($hakAkses->revisi == "Y")
                                        <button type="button" class="btn btn-warning mt-2 mt-sm-0 btnSubmit" id="btn_revisi" value="revisi">Revisi<i class="fas fa-file-signature ml-2"></i></button>
                                    @endif
                                    @if($hakAkses->print == "Y")
                                        <a type="button" class="btn btn-primary mt-2 mt-sm-0" href='{{route('SalesInvoiceCollection.CetakKwitansi', $dataCollection->id)}}' target="_blank">Cetak Kwitansi<i class="fas fa-print ml-2"></i></a>
										<a type="button" class="btn btn-primary mt-2 mt-sm-0" href='{{route('SalesInvoiceCollection.Cetak', $dataCollection->id)}}' target="_blank">Cetak<i class="fas fa-print ml-2"></i></a>
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

							    <h5 class="modal-title">Konfirmasi Tukar Faktur</h5>
						    </div>
						    <div class="modal-body">
							    <form >
								    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label class="col-lg-4 col-form-label">Penerima :</label>
                                                <div class="col-lg-8">
                                                    <input type="text" class="form-control" placeholder="Masukkan Nama Penerima Tukar Faktur" name="penerima" id="penerima">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-lg-4 col-form-label">Nomor Tukar Faktur Customer :</label>
                                                <div class="col-lg-8">
                                                    <input type="text" class="form-control" placeholder="Masukkan Nomor Tukar Faktur" name="nmr_faktur" id="nmr_faktur">
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

        function formatDate(strDate) {
            var arrMonth = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            var date = new Date(strDate);
            var day = date.getDate();
            var month = date.getMonth();
            var year = date.getFullYear();

            return day + ' ' + arrMonth[month] + ' ' + year;
        }

        $(document).ready(function () {
            $("#tanggal_tf_picker").val(formatDate('{{$dataCollection->tanggal}}'));
            footerDataForm('{{$dataCollection->id}}');
        });


        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        $("#cancel").on('click', function(e) {
            Swal.fire({
                title: "Batal?",
                text: "Apakah anda ingin membatalkan posting Tukar Faktur?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    window.location.href = "{{ url('/SalesInvoiceCollection') }}";
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
	    });

        $("#btnKonfirmSave").on('click', function(e) {
            var penerima = $("#penerima").val();
            var nmr = $("#nmr_faktur").val();
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
                        url: "/SalesInvoiceCollection/ConfirmCollection",
                        method: 'POST',
                        dataType : 'json',
                        data: {
                            idCollection : "{{$dataCollection->id}}",
                            namaPenerima : penerima,
                            nmrFaktur: nmr
                        },
                        success: function(result){
                            if (result != "false") {
                                Swal.fire(
                                    "Sukses!",
                                    "Konfirmasi Tukar Faktur Berhasil!.",
                                    "success"
                                )
                                $("#status_tf").val("Approved.(Diterima oleh : " + ucwords(penerima) + ")");
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

        $(".btnSubmit").on("click", function(e){
            var btn = $(this).val();
            $("#submit_action").val(btn);
            Swal.fire({
                title: ucwords(btn) + " Tukar Faktur?",
                text: "Apakah yakin ingin melakukan " + ucwords(btn) +" Tukar Faktur?",
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
                            url: '/SalesInvoiceCollection/GetDetail',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data: {
                                idCollection: "{{$dataCollection->id}}",
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
                        field: 'kode_invoice',
                        title: 'Faktur',
                        autoHide: false,
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            return row.kode_invoice.toUpperCase();
                        },
                    },
                    {
                        field: 'tanggal_invoice',
                        title: 'Tanggal Faktur',
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            if (row.tanggal_invoice != null) {
                                return formatDate(row.tanggal_invoice);
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'tanggal_jt',
                        title: 'Tanggal JT',
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            if (row.tanggal_jt != null) {
                                return formatDate(row.tanggal_jt);
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'grand_total',
                        title: 'Nominal(Rp)',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.grand_total).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                ],
            });
        });

        function footerDataForm(idInv) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/SalesInvoiceCollection/GetDataFooter",
                method: 'POST',
                data: {
                    idTf: idInv,
                },
                success: function(result){
                    if (result != "null") {
                        var nominal = result.nominalTf;
                        var nominalFixed = nominal.toString().replace(".", ",");
                        $("#nominal").val(Math.ceil(nominal));
                        $("#nominalMask").val(parseFloat(Math.ceil(nominal)).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                    }
                    else {
                        $("#nominal").val(0);
                        $("#nominalMask").val(parseFloat(0).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                    }
                }
            });
        }
    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
