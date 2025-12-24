@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h5 class="card-title text-white">Detail Biaya Ekspedisi</h5>
					</div>
                    <form action="{{ route('ExpeditionCost.Posting', $dataCost->id) }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            <div class="row">
								<div class="col-md-6">
									<fieldset>
										<legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi Pembeli / Customer </h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
										<div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Kode Biaya :</label>
                                            <div class="col-lg-9">
                                                <input type="hidden" name="mode" id="mode" value="load" readonly>
                                                <input type="text" class="form-control bg-slate-600 border-slate-600 border-1" placeholder="Auto Generated" name="kode_biaya" id="kode_biaya" value="{{strtoupper($dataCost->no_biaya)}}" readonly>
                                            </div>
                                        </div>

										<div class="form-group">
                                            <label>Ekspedisi :</label>
                                            <div>
                                                <input type="text" class="form-control" id="ekspedisi" name="ekspedisi" value="{{strtoupper($dataCost->nama_cabang)}}" readonly />
                                            </div>
                                            <span class="form-text text-danger err" style="display:none;">*Harap pilih customer terlebih dahulu!</span>
                                        </div>

										<div class="form-group">
                                            <label>Alamat Expedisi :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <textarea class="form-control" name="alamat" id="alamat" style="resize:none;" readonly>{{ucwords($dataCost->alamat_cabang)}}</textarea>
                                                </div>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih alamat customer terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Tanggal Kirim :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <input type="text" class="form-control" name="tanggal_picker" id="tanggal_picker" readonly>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal invoice terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>No. Resi :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <input type="text" class="form-control" id="resi" name="resi" value="{{strtoupper($dataCost->no_resi)}}" readonly />
                                            </div>
                                        </div>

									</fieldset>
								</div>

								<div class="col-md-6">
									<fieldset>
								<legend class="text-muted"><h6><i class="fab la-buffer"></i> Rincian Invoice</h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>

                                        <div class="form-group">
                                            <label>Status Biaya :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="status_biaya" id="status_biaya" value="{{strtoupper($dataCost->status_biaya)}}" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Status Revisi :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="text" id="revisi" class="form-control" value="{{ $dataCost->flag_revisi == "1" ? "Revisi" : "Tidak" }}" readonly>
                                                </div>
                                            </div>
                                        </div>

									</fieldset>
								</div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset>
                                        <legend class="text-muted"><h6><i class="la la-list"></i> List Biaya</h6></legend>
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
										<label class="col-lg-3 col-form-label">Total Jumlah Dus</label>
										<div class="col-lg-9">
											<input type="text" value="0" id="ttlQtyMask" class="form-control text-right" readonly>
											<input type="hidden" id="ttlQty" name="ttlQty" class="form-control text-right" readonly>
										</div>
									</div>

									<div class="form-group row">
										<label class="col-lg-3 col-form-label">Total Berat/Volume</label>
										<div class="col-lg-9">
											<input type="text" value="0" id="ttlBeratMask" class="form-control text-right" readonly>
											<input type="hidden" id="ttlBerat" name="ttlBerat" class="form-control text-right" readonly>
										</div>
									</div>

									<div class="form-group row">
										<label class="col-lg-3 col-form-label">Total Biaya</label>
										<div class="col-lg-9">
											<input type="text" value="0" id="ttlBiayaMask" class="form-control text-right" readonly>
											<input type="hidden" id="ttlBiaya" name="ttlBiaya" class="form-control text-right" readonly>
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
                                @if($dataCost->status_biaya == "draft")
                                    <button type="button" class="btn btn-secondary mt-2 mt-sm-0 btnSubmit" id="btn_edit" value="ubah">Ubah<i class="flaticon-edit ml-2"></i></button>
                                    @if($hakAkses->approve == "Y")
                                        <button type="button" class="btn btn-light-primary font-weight-bold mr-2 btnSubmit" id="btn_posting" value="posting"> Posting <i class="flaticon-paper-plane-1"></i></button>
                                    @endif

                                @elseif($dataCost->status_biaya == "posted")
                                    @if($hakAkses->revisi == "Y")
                                    <button type="button" class="btn btn-warning mt-2 mt-sm-0 btnSubmit" id="btn_revisi" value="revisi">Revisi<i class="fas fa-file-signature ml-2"></i></button>
                                    @endif
                                    @if($hakAkses->print == "Y")
										<a type="button" class="btn btn-primary mt-2 mt-sm-0" href='{{route('ExpeditionCost.Cetak', $dataCost->id)}}' target="_blank">Cetak<i class="fas fa-print ml-2"></i></a>
									@endif
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

        function formatDate(strDate) {
            var arrMonth = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            var date = new Date(strDate);
            var day = date.getDate();
            var month = date.getMonth();
            var year = date.getFullYear();

            return day + ' ' + arrMonth[month] + ' ' + year;
        }

        $(document).ready(function () {
            $("#tanggal_picker").val(formatDate('{{$dataCost->tanggal_kirim}}'));
            footerDataForm('{{$dataCost->id}}');
        });

        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        $("#cancel").on('click', function(e) {
            Swal.fire({
                title: "Batal?",
                text: "Apakah anda ingin membatalkan posting biaya Ekspedisi?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    window.location.href = "{{ url('/ExpeditionCost') }}";
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
                title: ucwords(btn) + " Biaya Ekspedisi?",
                text: "Apakah yakin ingin melakukan " + ucwords(btn) +" Biaya Ekspedisi?",
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
                            url: '/ExpeditionCost/GetDetail',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data: {
                                idCost: "{{$dataCost->id}}",
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
                        field: 'kode_pengiriman',
                        title: 'Kode Pengiriman',
                        autoHide: false,
                        textAlign: 'center',
                        width: 75,
                        template: function(row) {
                            return row.kode_pengiriman.toUpperCase();
                        },
                    },
                    {
                        field: 'nama_customer',
                        title: 'Nama Customer',
                        autoHide: false,
                        textAlign: 'center',
                        width: 'auto',
                        template: function(row) {
                            return row.nama_customer.toUpperCase();
                        },
                    },
                    {
                        field: 'txtKode',
                        title: 'Kode Pengiriman',
                        autoHide: false,
                        textAlign: 'center',
                        visible:false,
                        width: 'auto',
                        template: function(row) {
                            return row.kode_pengiriman.toUpperCase() + "<span id='txt_"+row.id_sj+"'>"+row.kode_pengiriman+"</span>";
                        },
                    },
                    {
                        field: 'tanggal_sj',
                        title: 'Tanggal Surat Jalan',
                        textAlign: 'center',
                        width: 100,
                        template: function(row) {
                            if (row.tanggal_sj != null) {
                                return formatDate(row.tanggal_sj);
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'nama_resi',
                        title: 'Barang',
                        autoHide: false,
                        textAlign: 'center',
                        width: 55,
                        template: function(row) {
                            return row.nama_resi.toUpperCase();
                        },
                    },
                    {
                        field: 'kota_tujuan',
                        title: 'Tujuan',
                        autoHide: false,
                        textAlign: 'center',
                        width: 70,
                        template: function(row) {
                            return row.kota_tujuan.toUpperCase();
                        },
                    },
                    {
                        field: 'tarif',
                        title: 'Tarif(Rp)',
                        textAlign: 'center',
                        autoHide: false,
                        width: 55,
                        template: function(row) {
                            return parseFloat(row.tarif).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'jumlah',
                        title: 'Jumlah Dus',
                        textAlign: 'center',
                        width: 55,
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.jumlah).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'berat',
                        title: 'Berat/Vol',
                        textAlign: 'center',
                        width: 55,
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.berat).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'discount',
                        title: 'Diskon (%)',
                        textAlign: 'center',
                        width: 55,
                        autoHide: false,
                        template: function(row) {
                            if (row.discount == null) {
                                return "-"
                            }
                            else {
                                return parseFloat(row.discount).toLocaleString('id-ID', { maximumFractionDigits: 2});
                            }

                        },
                    },
                    {
                        field: 'subtotal',
                        title: 'Subtotal',
                        textAlign: 'center',
                        width: 90,
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.subtotal).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'flag_tagih',
                        title: 'Ditagihkan?',
                        textAlign: 'center',
                        width: 50,
                        autoHide: false,
                        template: function(row) {
                            var txtCheckbox = "";

                            if (row.flag_tagih == "Y") {
                                txtCheckbox += "<span>Ya</span>";
                            }
                            else {
                                txtCheckbox += "<span>Tidak</span>";
                            }
                            return txtCheckbox;
                        },
                    },
                ],
            });
        });

        function footerDataForm(idCost) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/ExpeditionCost/GetDataFooter",
                method: 'POST',
                data: {
                    idCost: idCost,
                },
                success: function(result){
                    if (result != "null") {
                        var ttlQty = result.qtyCost;
                        var ttlBerat = result.beratCost;
                        var subtotal = result.subtotalCost;

                        $("#ttlQty").val(ttlQty);
                        $("#ttlQtyMask").val(parseFloat(ttlQty).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                        $("#ttlBerat").val(ttlBerat);
                        $("#ttlBeratMask").val(parseFloat(ttlBerat).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                        $("#ttlBiaya").val(subtotal);
                        $("#ttlBiayaMask").val(parseFloat(subtotal).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                    }
                    else {
                        $("#ttlQty").val(0);
                        $("#ttlQtyMask").val(parseFloat(0).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                        $("#ttlBerat").val(0);
                        $("#ttlBeratMask").val(parseFloat(0).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                        $("#ttlBiaya").val(0);
                        $("#ttlBiayaMask").val(parseFloat(0).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                    }
                }
            });
        }
	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
