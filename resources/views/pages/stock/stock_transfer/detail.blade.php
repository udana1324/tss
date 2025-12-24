@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h6 class="card-title text-white">Detail Pemindahan Stok Barang</h6>
					</div>
                    <form action="{{ route('StockTransfer.Posting', $dataTransfer->id) }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            <div class="row">
								<div class="col-md-6">
									<fieldset>
                                        <legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi Barang </h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>

                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label">Kode Transaksi :</label>
                                            <div class="col-lg-8">
                                                <label class="col-form-label">{{strtoupper($dataTransfer->kode_transaksi)}}</label>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label">Tanggal Transaksi :</label>
                                            <div class="col-lg-8">
                                                <label class="col-form-label">{{\Carbon\Carbon::parse($dataTransfer->tgl_transaksi)->format('d F Y')}}</label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-lg-6 col-form-label">Keterangan : </label>
                                            <div class="col-lg-12">
                                                <textarea class="form-control" id="keterangan" name="keterangan" rows="3" readonly>{{$dataTransfer->keterangan}}</textarea>
                                            </div>
                                        </div>

									</fieldset>
								</div>

								<div class="col-md-6">
									<fieldset>
					                	<legend class="text-muted"><h6><i class="fab la-buffer"></i> Rincian Pemindahan</h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
                                        <div class="form-group">
                                            <label>Status Transfer Barang :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="status_trf" id="status_trf" value="{{strtoupper($dataTransfer->status_transfer)}}" readonly>
                                                </div>
                                            </div>
                                        </div>

										<div class="form-group">
											<label>Status Revisi Transfer Barang :</label>
											<div class="input-group">
        										<div class="col-12 pl-0">
        											<input type="text" id="revisi" class="form-control" value="{{ $dataTransfer->flag_revisi == "1" ? "Revisi" : "Tidak" }}" readonly>
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
                                        <legend class="text-muted"><h6><i class="la la-list"></i> List Pemindahan</h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>

                                        <div class="datatable datatable-bordered datatable-head-custom" id="list_item"></div>

                                    </fieldset>
                                </div>
                            </div>

                        </div>

                        <div class="card-footer bg-white d-sm-flex justify-content-sm-between align-items-sm-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-light-danger font-weight-bold mr-2" id="cancel">Keluar <i class="flaticon2-cancel icon-sm"></i></button>
                            </div>

                            <div class="mt-2 mt-sm-0">
                                <input type="hidden" id="submit_action" name="submit_action" class="form-control" readonly>
                                @if($dataTransfer->status_transfer == "draft")
                                    <button type="button" class="btn btn-secondary mt-2 mt-sm-0 btnSubmit" id="btn_edit" value="ubah">Ubah Pemindahan<i class="flaticon-edit ml-2"></i></button>
                                    @if($hakAkses->approve == "Y")
                                        <button type="button" class="btn btn-light-primary font-weight-bold mr-2 btnSubmit" id="btn_posting" value="posting"> Posting <i class="flaticon-paper-plane-1"></i></button>
                                    @endif

                                @elseif($dataTransfer->status_transfer == "posted")
                                    <button type="button" class="btn btn-warning mt-2 mt-sm-0 btnSubmit" id="btn_revisi" value="revisi">Revisi<i class="fas fa-file-signature ml-2"></i></button>
                                    <button type="button" class="btn btn-danger mt-2 mt-sm-0 btnSubmit" id="btn_cancel" value="batal">Batal<i class="flaticon2-cancel ml-2"></i></button>
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

        function formatDate(strDate) {
            var arrMonth = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            var date = new Date(strDate);
            var day = date.getDate();
            var month = date.getMonth();
            var year = date.getFullYear();

            return day + ' ' + arrMonth[month] + ' ' + year;
        }

        $("#cancel").on('click', function(e) {
            Swal.fire({
                title: "Batal?",
                text: "Apakah anda ingin membatalkan Posting Pemindahan Barang?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    window.location.href = "{{ url('/StockTransfer') }}";
                }
                else if (result.dismiss === "cancel") {
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
                            url: '/StockTransfer/GetDetail',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data: {
                                idTransfer: "{{$dataTransfer->id}}"
                            }
                        },
                    },
                    pageSize: 25,
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
                    input: $('#list_item_search_query')
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
                        title: 'Item',
                        autoHide: false,
                        width: 250,
                        textAlign: 'center',
                        template: function(row) {
                            return row.kode_item.toUpperCase() + " - " + ucwords(row.nama_item);
                        },
                    },
                    {
                        field: 'txt_index_f',
                        title: 'Dari',
                        width: 70,
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            if (row.txt_index_f != null) {
                                return row.txt_index_f;
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'txt_index_t',
                        title: 'Ke',
                        width: 70,
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            if (row.txt_index_t != null) {
                                return row.txt_index_t;
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'qty_item',
                        width: 80,
                        title: 'Jumlah',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.qty_item).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'nama_satuan',
                        width: 80,
                        title: 'Satuan',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            if (row.nama_satuan != null) {
                                return row.nama_satuan.toUpperCase();
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                ],
            });
        });

        $(".btnSubmit").on("click", function(e){
            var btn = $(this).val();
            $("#submit_action").val(btn);
            Swal.fire({
                title: ucwords(btn) + " Pemindahan Barang?",
                text: "Apakah yakin ingin melakukan " + ucwords(btn) +" Pemindahan Barang?",
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
    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
