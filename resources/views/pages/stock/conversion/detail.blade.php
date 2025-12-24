@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h6 class="card-title text-white">Detail Konversi Stok Barang</h6>
					</div>
                    <form action="{{ route('StockConversion.Posting', $dataConversion->id) }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            <div class="row">
								<div class="col-md-6">
									<fieldset>
                                        <legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi Barang </h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group row">
                                                    <label class="col-lg-4 col-form-label">Kode Transaksi :</label>
                                                    <div class="col-lg-8">
                                                        <label class="col-form-label">{{strtoupper($dataConversion->kode_transaksi)}}</label>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-lg-4 col-form-label">Tanggal Konversi :</label>
                                                    <div class="col-lg-8">
                                                        <label class="col-form-label">{{\Carbon\Carbon::parse($dataConversion->tgl_transaksi)->format('d F Y')}}</label>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-lg-4 col-form-label">Keterangan : </label>
                                                    <div class="col-lg-8">
                                                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3" style="resize: none;" placeholder="Ketik Keterangan Disini" readonly>{{$dataConversion->keterangan}}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

									</fieldset>
								</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <fieldset>
                                        <legend class="text-muted"><h6><i class="la la-list"></i> List Konversi Asal</h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>

                                        <div class="datatable datatable-bordered datatable-head-custom" id="list_item_from"></div>


                                    </fieldset>
                                    <span class="form-text text-danger errTblFrom" id="errTblFrom" style="display:none;">*Harap tambahkan Minimum 1 Barang Asal terlebih dahulu!</span>
                                </div>
                                <div class="col-md-6">
                                    <fieldset>
                                        <legend class="text-muted"><h6><i class="la la-list"></i> List Konversi Hasil</h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>

                                        <div class="datatable datatable-bordered datatable-head-custom" id="list_item_to"></div>

                                    </fieldset>
                                    <span class="form-text text-danger errTblTo" id="errTblTo" style="display:none;">*Harap tambahkan Minimum 1 Barang Hasil terlebih dahulu!</span>
                                </div>
                            </div>

                        </div>

                        <div class="card-footer bg-white d-sm-flex justify-content-sm-between align-items-sm-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-light-danger font-weight-bold mr-2" id="cancel">Keluar <i class="flaticon2-cancel icon-sm"></i></button>
                            </div>

                            <div class="mt-2 mt-sm-0">
                                <input type="hidden" id="submit_action" name="submit_action" class="form-control" readonly>
                                @if($dataConversion->status_conversion == "draft")
                                    <button type="button" class="btn btn-secondary mt-2 mt-sm-0 btnSubmit" id="btn_edit" value="ubah">Ubah Konversi<i class="flaticon-edit ml-2"></i></button>
                                    @if($hakAkses->approve == "Y")
                                        <button type="button" class="btn btn-light-primary font-weight-bold mr-2 btnSubmit" id="btn_posting" value="posting"> Posting <i class="flaticon-paper-plane-1"></i></button>
                                    @endif

                                @elseif($dataConversion->status_conversion == "posted")
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
                text: "Apakah anda ingin kembali ke daftar Konversi Barang?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    window.location.href = "{{ url('/StockConversion') }}";
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
	    });

        $(document).ready(function() {

            var datatable = $('#list_item_from').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/StockConversion/GetDetailFrom',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data : {
                                idConversion : '{{$dataConversion->id}}'
                            },
                        },
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
                    input: $('#list_item_from_search_query')
                },

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
                        autoHide: false,
                        width: 125,
                        textAlign: 'center',
                        template: function(row) {
                            return row.kode_item.toUpperCase() + " - " + ucwords(row.nama_item);
                        },
                    },
                    // {
                    //     field: 'nama_item',
                    //     title: 'Nama',
                    //     width: 'auto',
                    //     textAlign: 'center',
                    //     autoHide: false,
                    //     template: function(row) {
                    //         return ucwords(row.nama_item);
                    //     },
                    // },
                    {
                        field: 'qty_item',
                        width: 75,
                        autoHide: false,
                        title: 'Jumlah',
                        textAlign: 'center',
                        template: function(row) {
                            return parseFloat(row.qty_item).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'nama_satuan',
                        width: 75,
                        title: 'Satuan',
                        autoHide: false,
                        textAlign: 'center',
                        template: function(row) {
                            if (row.nama_satuan != null) {
                                return row.nama_satuan.toUpperCase();
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'txt_index',
                        title: 'Gudang',
                        width: 80,
                        textAlign: 'center',
                        template: function(row) {
                            if (row.txt_index != null) {
                                return row.txt_index;
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                ],
            });
        });

        $(document).ready(function() {

            var datatable = $('#list_item_to').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/StockConversion/GetDetailTo',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data : {
                                idConversion : '{{$dataConversion->id}}'
                            },
                        },
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
                    input: $('#list_item_to_search_query')
                },

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
                        autoHide: false,
                        width: 125,
                        textAlign: 'center',
                        template: function(row) {
                            return row.kode_item.toUpperCase() + " - " + ucwords(row.nama_item);
                        },
                    },
                    // {
                    //     field: 'nama_item',
                    //     title: 'Nama',
                    //     width: 'auto',
                    //     textAlign: 'center',
                    //     autoHide: false,
                    //     template: function(row) {
                    //         return ucwords(row.nama_item);
                    //     },
                    // },
                    {
                        field: 'qty_item',
                        width: 75,
                        autoHide: false,
                        title: 'Jumlah',
                        textAlign: 'center',
                        template: function(row) {
                            return parseFloat(row.qty_item).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'nama_satuan',
                        width: 75,
                        title: 'Satuan',
                        autoHide: false,
                        textAlign: 'center',
                        template: function(row) {
                            if (row.nama_satuan != null) {
                                return row.nama_satuan.toUpperCase();
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'txt_index',
                        title: 'Gudang',
                        width: 80,
                        textAlign: 'center',
                        template: function(row) {
                            if (row.txt_index != null) {
                                return row.txt_index;
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
                title: ucwords(btn) + " Konversi?",
                text: "Apakah yakin ingin melakukan " + ucwords(btn) +" Konversi?",
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
