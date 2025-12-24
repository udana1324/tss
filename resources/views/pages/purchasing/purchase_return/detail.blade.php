@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h6 class="card-title text-white">Detail Retur Pembelian</h6>
					</div>
                    <form action="{{ route('PurchaseReturn.Posting', $dataRetur->id) }}" class="form-horizontal" id="form_add" method="POST" enctype="multipart/form-data">
					    <div class="card-body">
                            {{ csrf_field() }}
                            <div class="row">
								<div class="col-md-6">
									<fieldset>
                                        <legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi Vendor / Supplier </h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
										<div class="form-group row">
                                            <label class="col-lg-4 col-form-label">Kode Retur :</label>
                                            <div class="col-lg-8">
                                                <input type="hidden" value="load" id="mode" />
                                                <label class="col-form-label">{{strtoupper($dataRetur->kode_retur)}}</label>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label">Supplier :</label>
                                            <div class="col-lg-8">
                                                <label class="col-form-label">{{$dataSupplier->nama_supplier}}</label>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label  class="col-lg-4 col-form-label">No. Retur :</label>
                                            <div class="col-lg-8">
                                                <label class="col-form-label">{{strtoupper($dataRetur->kode_retur_item)}}</label>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label  class="col-lg-4 col-form-label">No. Nota Retur :</label>
                                            <div class="col-lg-8">
                                                <label class="col-form-label">{{$dataRetur->nota_retur}}</label>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label">Tanggal Retur :</label>
                                            <div class="col-lg-8">
                                                <label class="col-form-label">{{\Carbon\Carbon::parse($dataRetur->tanggal_retur)->format('d F Y')}}</label>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label">Keterangan :</label>
                                            <div class="col-lg-8">
                                                <label class="col-form-label">{{ $dataRetur->keterangan }}</label>
                                            </div>
                                        </div>

									</fieldset>
								</div>

								<div class="col-md-6">
									<fieldset>
					                	<legend class="text-muted"><h6><i class="fab la-buffer"></i> Rincian Retur Barang</h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>

										<div class="form-group">
                                            <label>Status Retur :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="status_retur" id="status_retur" value="{{strtoupper($dataRetur->status_retur)}}" readonly>
                                                </div>
                                            </div>
                                        </div>

										<div class="form-group">
											<label>Status Revisi Retur Barang :</label>
											<div class="input-group">
        										<div class="col-12 pl-0">
        											<input type="text" id="revisi" class="form-control" value="{{ $dataRetur->flag_revisi == "1" ? "Revisi" : "Tidak" }}" readonly>
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
                                        <legend class="text-muted"><h6><i class="la la-list"></i> Daftar Retur Barang</h6></legend>
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
										<label class="col-lg-3 col-form-label">Total Retur</label>
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
										<label class="col-lg-3 col-form-label">Total Retur</label>
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
                                @if($dataRetur->status_retur == "draft")
                                    <button type="button" class="btn btn-secondary mt-2 mt-sm-0 btnSubmit" id="btn_edit" value="ubah">Ubah Retur Barang<i class="flaticon-edit ml-2"></i></button>
                                    @if($hakAkses->approve == "Y")
                                        <button type="button" class="btn btn-light-primary font-weight-bold mr-2 btnSubmit" id="btn_posting" value="posting"> Posting <i class="flaticon-paper-plane-1"></i></button>
                                    @endif

                                @elseif($dataRetur->status_retur == "posted")
                                    <button type="button" class="btn btn-warning mt-2 mt-sm-0 btnSubmit" id="btn_revisi" value="revisi">Revisi<i class="fas fa-file-signature ml-2"></i></button>
                                    <button type="button" class="btn btn-danger mt-2 mt-sm-0 btnSubmit" id="btn_cancel" value="batal">Batal<i class="flaticon2-cancel ml-2"></i></button>
                                @endif
                                @if($hakAkses->print == "Y")
								    <a type="button" class="btn btn-primary mt-2 mt-sm-0" href='{{route('PurchaseOrder.Cetak', $dataRetur->id)}}' target="_blank">Cetak Surat Retur<i class="fas fa-print ml-2"></i></a>
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

        $("#cancel").on('click', function(e) {
            Swal.fire({
                title: "Batal?",
                text: "Apakah anda ingin kembali ke daftar retur pembelian?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    window.location.href = "{{ url('/PurchaseReturn') }}";
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
	    });

        $(".btnSubmit").on("click", function(e){
            var btn = $(this).val();
            if (btn == "nota") {
                btn = "buat " + btn;
            }
            $("#submit_action").val(btn);
            Swal.fire({
                title: ucwords(btn) + " Retur Pembelian?",
                text: "Apakah yakin ingin melakukan " + ucwords(btn) +" Retur Pembelian?",
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
                            url: '/PurchaseReturn/GetDetail',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data : {
                                idReturn : '{{$dataRetur->id}}'
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
                        width: 20,
                        type: 'number',
                        selector: false,
                        textAlign: 'center',
                        visible:false,
                    },
                    {
                        field: 'kode_item',
                        title: 'Item',
                        width: 'auto',
                        autoHide: false,
                        textAlign: 'center',
                        template: function(row) {
                            return row.kode_item.toUpperCase() + ' - ' + row.nama_item.toUpperCase();
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
                        title: 'Jumlah',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.qty_item).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'id_index',
                        title: 'Lokasi',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            if (row.txt_index != null) {
                                return row.txt_index.toUpperCase();
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                ],
            });
        });

        function footerDataForm(idReturn) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/PurchaseReturn/GetDataFooter",
                method: 'POST',
                data: {
                    idReturn: idReturn,
                },
                success: function(result){
                    if (result != "null") {
                        var subtotal = result.subtotal;
                        var qtyItem = result.qtyItem;
                        var qtyFixed = qtyItem;
                        var subtotalFixed = subtotal;
                        // var jenisPPn = $('input[name=status_ppn]:checked').val();
                        var persenPPNInclude = (100 + parseFloat("{{$taxSettings->ppn_percentage}}")) / 100;
                        var persenPPNExclude = parseFloat("{{$taxSettings->ppn_percentage}}") / 100;

                        $("#qtyTtl").val(qtyFixed);
                        $("#qtyTtlMask").val(parseFloat(qtyFixed).toLocaleString('id-ID', { maximumFractionDigits: 2}))

                        // if (jenisPPn == "I") {
                        //     subtotalFixed = parseFloat(subtotalFixed) / parseFloat(persenPPNInclude);
                        // }

                        var subtotalMask = parseFloat(subtotalFixed).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        // $("#dpp").val(subtotalFixed);
                        // $("#dppMask").val(subtotalMask);

                        // if (jenisPPn != "N") {
                        //     var ppn = parseFloat(subtotalFixed) * parseFloat(persenPPNExclude);
                        //     $("#ppn").val(ppn);
                        //     $("#ppnMask").val(parseFloat(ppn).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        // }
                        // else {
                        //     var ppn = 0;
                        //     $("#ppn").val(ppn);
                        //     $("#ppnMask").val(parseFloat(ppn).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        // }

                        // var grandTotal = parseFloat(subtotalFixed) + parseFloat(ppn);
                        var grandTotal = parseFloat(subtotalFixed);
                        $("#gt").val(Math.ceil(grandTotal));
                        $("#gtMask").val(parseFloat(Math.ceil(grandTotal)).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                    }
                    else {
                        $("#qtyTtl").val(0);
                        $("#qtyTtlMask").val(0);
                        // $("#dpp").val(0);
                        // $("#dppMask").val(0);
                        // $("#ppn").val(0);
                        // $("#ppnMask").val(0);
                        $("#gt").val(0);
                        $("#gtMask").val(0);
                    }
                }
            });
        }

        $(document).ready(function () {
            footerDataForm('{{$dataRetur->id}}');
            $("#mode").val("edit");
        });
    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
