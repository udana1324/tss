@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h6 class="card-title text-white">Detail Entri Bank</h6>
					</div>
                    <form action="{{ route('GLKasBank.Posting', $dataKasBank->id) }}" class="form-horizontal" id="form_add" method="POST" enctype="multipart/form-data">
					    <div class="card-body">
                            {{ csrf_field() }}
                            <div class="row">
								<div class="col-lg-6">
									<fieldset>
                                        <legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi Entri Bank </h6></legend>
                                        <br>

                                        <div class="form-group">
											<label>Nomor Entri Bank :</label>
											<div class="input-group">
                                                <input type="text" class="form-control form-control-solid" placeholder="Nomor akan dibuat otomatis oleh sistem" value="{{strtoupper($dataKasBank->nomor_kas_bank)}}" name="nomor_kas_bank" id="nomor_kas_bank" readonly />
											</div>
										</div>

                                        <div class="form-group row">
                                            <div class="col-md-6 mb-5">
                                                <label>Jenis Entri :</label>
                                                <div>
                                                    <div class="radio-inline">
                                                        <label class="radio">
                                                            <input type="radio" id="bank" value="2" name="id_account" {{$dataKasBank->id_account == "2" ? "checked" : ""}} disabled="disabled" />
                                                            <span></span>Bank
                                                        </label>
                                                    </div>
                                                </div>
                                                <span class="form-text text-danger" id="errJenis" style="display:none;">*Harap pilih jenis Kas Bank terlebih dahulu!</span>
                                            </div>
										</div>

                                        <div class="form-group row">
                                            <div class="col-md-6 mb-5">
                                                <label>Jenis Transaksi :</label>
                                                <div>
                                                    <div class="radio-inline">
                                                        <label class="radio">
                                                            <input type="radio" id="masuk" value="1" name="jenis_transaksi" {{$dataKasBank->jenis_transaksi == "1" ? "checked" : ""}} disabled="disabled" />
                                                            <span></span>Masuk
                                                        </label>
                                                        <label class="radio">
                                                            <input type="radio" id="keluar" value="2" name="jenis_transaksi" {{$dataKasBank->jenis_transaksi == "2" ? "checked" : ""}} disabled="disabled" />
                                                            <span></span>keluar
                                                        </label>
                                                    </div>
                                                </div>
                                                <span class="form-text text-danger" id="err" style="display:none;">*Harap pilih jenis Transaki terlebih dahulu!</span>
                                            </div>
										</div>

                                        <div class="form-group row mb-0">
                                            <div class="col-md-12">
                                                <label>Tanggal Entri Bank :</label>
                                                <div class="form-group divTgl ">
                                                    <input type="hidden" class="form-control req" name="tanggal_transaksi" id="tanggal_transaksi" readonly >
                                                    <input type="text" class="form-control pickerTgl" placeholder="Pilih Tanggal" name="tanggal_kas_bank_picker" id="tanggal_kas_bank_picker" disabled>
                                                    <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal penjualan terlebih dahulu!</span>
                                                </div>
                                            </div>
                                        </div>

									</fieldset>
								</div>

								<div class="col-lg-6">
									<fieldset>
                                        <legend class="text-muted pb-6"><h6> </h6></legend>
                                        <br>

                                        <div class="form-group">
                                            <label>Account Bank :</label>
                                            <div class="input-group">
                                                <input type="text" id="id_account_sub" name="id_account_sub" class="form-control" value="{{strtoupper($dataKasBank->account_number.' - '.$dataKasBank->account_name)}}" readonly>
                                            </div>
                                            <span class="form-text text-danger err" style="display:none;">*Harap masukkan Total Transaksi kas/bank terlebih dahulu!</span>
                                        </div>

                                        <div class="form-group">
                                            <label>Total Transaksi :</label>
                                            <div class="input-group">
                                                <input type="text" id="nominalTransaksi" name="nominal_transaksi" class="form-control" value="{{number_format($dataKasBank->nominal_transaksi, 0, ',', '.')}}" readonly>
                                            </div>
                                            <span class="form-text text-danger err" style="display:none;">*Harap masukkan Total Transaksi kas/bank terlebih dahulu!</span>
                                        </div>

                                        <div class="form-group">
                                            <label>Status :</label>
                                            <div class="input-group">
                                                <input type="text" id="status" name="status" class="form-control" value="{{ucwords($dataKasBank->status)}}" readonly>
                                            </div>
                                        </div>

									</fieldset>
								</div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset>
                                        <legend class="text-muted"><h6><i class="la la-list"></i> Daftar Transaksi</h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>

                                        <div class="datatable datatable-bordered datatable-head-custom" id="list_item"></div>

                                    </fieldset>
                                </div>
                            </div>

                            <br>
							<br>
							<div class="row">
								<div class="col-md-6">

								</div>

								<div class="col-md-6">

									<div class="form-group row">
										<label class="col-lg-3 col-form-label">Total Nominal</label>
										<div class="col-lg-9">
											<input type="text" value="0" id="totalMask" class="form-control text-right" readonly>
											<input type="hidden" id="total" name="total" class="form-control text-right" readonly>
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
                                @if($dataKasBank->status == "draft")
                                    <button type="button" class="btn btn-secondary mt-2 mt-sm-0 btnSubmit" id="btn_edit" value="ubah">Ubah Entry<i class="flaticon-edit ml-2"></i></button>
                                    @if($hakAkses->approve == "Y")
                                        <button type="button" class="btn btn-light-primary font-weight-bold mr-2 btnSubmit" id="btn_posting" value="posting"> Posting <i class="flaticon-paper-plane-1"></i></button>
                                    @endif

                                @elseif($dataKasBank->status == "posted")
                                    @if($hakAkses->approve == "Y")
                                    <button type="button" class="btn btn-danger mt-2 mt-sm-0 btnSubmit" id="btn_cancel" value="batal">Batal<i class="flaticon2-cancel ml-2"></i></button>
                                    @endif
                                    @if($hakAkses->revisi == "Y")
                                        <button type="button" class="btn btn-warning mt-2 mt-sm-0 btnSubmit" id="btn_revisi" value="revisi">Revisi<i class="fas fa-file-signature ml-2"></i></button>
                                    @endif
                                @endif
                                @if($hakAkses->print == "Y" && $dataKasBank->status == "posted")
								    <a type="button" class="btn btn-primary mt-2 mt-sm-0" href='{{route('GLKasBank.Cetak', $dataKasBank->id)}}' target="_blank">Cetak<i class="fas fa-print ml-2"></i></a>
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

        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        $(document).ready(function () {


            $('#tanggal_kas_bank_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                autoclose : true,
                "setDate": new Date(),
                format : "dd MM yyyy",
            });

            $("#nominalEntryMask").autoNumeric('init');

            $("#tanggal_kas_bank_picker").datepicker('setDate', new Date("{{$dataKasBank->tanggal_transaksi}}"));
            footerDataForm('{{$dataKasBank->id}}');
        });


        $("#tanggal_kas_bank_picker").on('change', function() {
            $("#tanggal_transaksi").val($("#tanggal_kas_bank_picker").data('datepicker').getFormattedDate('yyyy-mm-dd'));
        });

        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        $("#cancel").on('click', function(e) {
            Swal.fire({
                title: "Batal?",
                text: "Apakah anda ingin kembali ke halaman Utama ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    window.location.href = "{{ url('/GLKasBank') }}";
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
	    });

        function formatDate(strDate) {
            var arrMonth = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            var date = new Date(strDate);
            var day = date.getDate();
            var month = date.getMonth();
            var year = date.getFullYear();

            return day + ' ' + arrMonth[month] + ' ' + year;
        }

        $(document).ready(function() {

            var datatable = $('#list_item').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/GLKasBank/GetDetail',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data : {
                                idKasBank : '{{$dataKasBank->id}}'
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
                        field: 'account_number',
                        title: 'Nomor Rekening',
                        width: 'auto',
                        autoHide: false,
                        textAlign: 'center'
                    },
                    {
                        field: 'account_name',
                        title: 'Nama Rekening',
                        textAlign: 'left',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            return row.account_name.toUpperCase();
                        },
                    },
                    {
                        field: 'nominal',
                        title: 'Nominal (Rp)',
                        textAlign: 'right',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.nominal).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'keterangan',
                        title: 'Deskripsi',
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

        // $(".btnSubmit").on("click", function(e){
        //     var btn = $(this).val();
        //     $("#submit_action").val(btn);
        //     Swal.fire({
        //         title: ucwords(btn) + " Entry Kas Bank ?",
        //         text: "Apakah yakin ingin melakukan " + ucwords(btn) +" Entry Kas Bank?",
        //         icon: "warning",
        //         showCancelButton: true,
        //         confirmButtonText: "Ya",
        //         cancelButtonText: "Tidak",
        //         reverseButtons: false
        //     }).then(function(result) {
        //         if(result.value) {
        //             $("#form_add").off("submit").submit();
        //         }
        //         else if (result.dismiss === "cancel") {
        //             $("html, body").animate({ scrollTop: 0 }, "slow");
        //             e.preventDefault();
        //         }
        //     });
		// });

        $(".btnSubmit").on("click", function(e){
            var btn = $(this).val();
            $("#submit_action").val(btn);
            if (btn == "posting") {
                Swal.fire({
                    title: "Posting Entri Bank",
                    //text: "Apakah yakin ingin melakukan Pembatalan Invoice Penjualan?",
                    html: "Apakah yakin ingin melakukan Posting Entri Bank?" + "<br /><br />" +
                    '<button type="button" id="btn_postingOnly" onclick="posting(' + "'posting'" + ')"  class="btn btn-primary mt-2 mt-sm-0">' + 'Hanya Posting' + '</button><br /><br />' +
                    '<button type="button" id="btn_postingEntry"  onclick="posting(' + "'postingEntry'" + ')" class="btn btn-success mt-2 mt-sm-0">' + 'Posting dan Entry Baru' + '</button>',
                    icon: "warning",
                    showCancelButton: true,
                    showConfirmButton: false,
                    cancelButtonText: "Tidak",
                    reverseButtons: false
                });
            }
            else {
                Swal.fire({
                    title: ucwords(btn) + " Entri Bank?",
                    text: "Apakah yakin ingin melakukan " + ucwords(btn) +" Entri Bank?",
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

        function posting(aksi) {
            $("#submit_action").val(aksi);
            swal.clickConfirm();
            if (aksi == "posting" || aksi == "postingEntry") {
                $("#form_add").off("submit").submit();
            }
        }

        function footerDataForm(idKasBank) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/GLKasBank/GetDataFooter",
                method: 'POST',
                data: {
                    idKasBank: idKasBank
                },
                success: function(result){
                    if (result != "null") {
                        var total = result.nominal;

                        $("#total").val(total);
                        $("#totalMask").val(parseFloat(total).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                    }
                    else {
                        $("#total").val(0);
                        $("#totalMask").val(0);
                    }
                }
            });
        }

	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
