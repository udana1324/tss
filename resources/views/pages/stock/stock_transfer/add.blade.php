@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h6 class="card-title text-white">Buat Pemindahan Stok Barang</h6>
					</div>
                    <form action="{{ route('StockTransfer.store') }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            <div class="row">
								<div class="col-md-6">
									<fieldset>
                                        <legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi Barang </h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>

                                        <div class="form-group">
											<div class="input-group">
        										<label class="col-lg-8 col-form-label">Barang :</label><label class="col-lg-4 col-form-label">Satuan :</label>
                                                <div class="col-lg-8">
                                                    <input type="hidden" id="ModeGetStok" value="add" class="form-control" readonly>
                                                    <select class="form-control select2 detailItem" id="product" name="product">
                                                        <option label="Label"></option>
                                                        @foreach($dataProduct as $product)
                                                        <option value="{{$product->id}}">{{ $product->value_spesifikasi != null ? '('.$product->value_spesifikasi.')' : "" }}{{strtoupper($product->kode_item)}} - {{strtoupper($product->nama_item)}}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="form-text text-danger err" style="display:none;">*Harap pilih barang terlebih dahulu!</span>
                                                </div>

        										<div class="col-4 pr-0">
        											<select class="form-control select2 detailUnit" id="productUnit" name="productUnit">
                                                        <option label="Label"></option>
                                                    </select>
                                                    <span class="form-text text-success" id="txtStok"></span>
                                                    <input type="hidden" id="stokItem" class="form-control form-control-solid">
                                                    <span class="form-text text-danger errUnit" style="display:none;">*Harap pilih satuan item terlebih dahulu!</span>
        										</div>
											</div>
										</div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-lg-12 col-form-label">Kategori : </label>
                                                    <div class="col-lg-12">
                                                        <input type="text" class="form-control" id="kategori" readonly>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-lg-12 col-form-label">Merk : </label>
                                                    <div class="col-lg-12">
                                                        <input type="text" class="form-control" id="merk" readonly>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="col-md-6">

                                                <div class="form-group">
                                                    <label class="col-lg-12 col-form-label">Jenis Barang : </label>
                                                    <div class="col-lg-12">
                                                        <input type="text" class="form-control" id="jenis_barang" readonly>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="col-md-12">

                                                <div class="form-group">
                                                    <label class="col-lg-6 col-form-label">Tanggal Transaksi :</label>
                                                    <div class="col-lg-12">
                                                        <input type="hidden" class="form-control req" name="tanggal" id="tanggal">
                                                        <input type="text" class="form-control" name="tanggal_picker" id="tanggal_picker" readonly>
                                                        <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal kirim terlebih dahulu!</span>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-lg-6 col-form-label">Keterangan : </label>
                                                    <div class="col-lg-12">
                                                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Ketik Keterangan Disini"></textarea>
                                                    </div>
                                                </div>

                                            </div>

                                        </div>

									</fieldset>
								</div>

								<div class="col-md-6">
									<fieldset>
					                	<legend class="text-muted"><h6><i class="fab la-buffer"></i> Rincian Pemindahan</h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-lg-12 col-form-label">Dari : </label>
                                                    <div class="col-lg-12">
                                                        <select class="form-control select2 detailItem getStok" id="indexF" name="indexF" style="width:100%;">
                                                            <option label="Label"></option>
                                                            @foreach($listIndex as $index)
                                                            <option value="{{$index['id']}}">{{strtoupper($index['nama_index'])}}</option>
                                                            @endforeach
                                                        </select>
                                                        <span class="form-text text-danger errItem" style="display:none;">*Harap pilih gudang asal terlebih dahulu!</span>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-lg-12 col-form-label">Ke : </label>
                                                    <div class="col-lg-12">
                                                        <select class="form-control select2 detailItem getStok" id="indexT" name="indexT" style="width:100%;">
                                                            <option label="Label"></option>
                                                            @foreach($listIndex as $index)
                                                            <option value="{{$index['id']}}">{{strtoupper($index['nama_index'])}}</option>
                                                            @endforeach
                                                        </select>
                                                        <span class="form-text text-danger errItem" style="display:none;">*Harap pilih gudang tujuan terlebih dahulu!</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-lg-12 col-form-label">Stok Saat Ini : </label>
                                                    <div class="col-lg-12">
                                                        <input type="text" class="form-control" id="StockFMask" readonly>
                                                        <input type="hidden" class="form-control" id="StockF" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-lg-12 col-form-label">Stok Saat Ini : </label>
                                                    <div class="col-lg-12">
                                                        <input type="text" class="form-control" id="StockT" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-lg-6 col-form-label">Jumlah Pemindahan : </label>
                                            <div class="col-lg-12">
                                                <input type="text" id="qty_itemMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right">
                                                <input type="hidden" id="qty_item" min="0" class="form-control text-right detailItem numeric" autocomplete="off">
                                                <span class="form-text text-danger errItem" style="display:none;">*Harap masukkan qty dus terlebih dahulu!</span>
                                                <span class="form-text text-danger errItemNumeric" style="display:none;">*qty dus tidak dapat dibawah atau 0!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
											<div class="col-lg-12 text-center">
												<button type="button" class="btn btn-primary font-weight-bold" id="btnAddItem">Tambah</button>
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
                                <button type="button" style="display: none;" id="btnModalEditItem" data-toggle="modal" data-backdrop="static" data-target="#modal_form_edit_item"></button>
                                <button type="submit" class="btn btn-light-primary font-weight-bold mr-2"> Simpan <i class="flaticon-paper-plane-1"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Horizontal form edit item-->
				<div id="modal_form_edit_item" class="modal fade">
				    <div class="modal-dialog modal-lg">
					    <div class="modal-content">
						    <div class="modal-header bg-primary">

							    <h5 class="modal-title text-white">Ubah Pemindahan</h5>
						    </div>
						    <div class="modal-body">
							    <div class="row">
                                    <div class="col-md-12">
                                        <fieldset>
                                            <legend class="text-muted"><h6><i class="fab la-buffer"></i> Rincian Pemindahan</h6></legend>
                                            <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                            <br>
                                            <div class="form-group">
                                                <label>Barang :</label>
                                                <div class="form-group form-group-feedback form-group-feedback-right">
                                                    <div class="input-group">
                                                        <input type="hidden" id="idDetail" class="form-control" readonly>
                                                        <input type="hidden" id="idItemEdit" class="form-control" readonly>
                                                        <input type="hidden" id="idSatuanEdit" class="form-control" readonly>
                                                        <input type="hidden" id="idIndexEditF" class="form-control" readonly>
                                                        <input type="text" id="BarangEdit" class="form-control" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row mb-0">
                                                <div class="form-group col-xl-6 col-sm-6 col-xs-12">
                                                    <label>Dari :</label>
                                                    <div class="input-group input-group-solid">
                                                        <select class="form-control select2 detailItemEdit getStok" id="indexEditF" name="indexEditF" style="width:100%;">
                                                            <option label="Label"></option>
                                                            @foreach($listIndex as $index)
                                                            <option value="{{$index['id']}}">{{strtoupper($index['nama_index'])}}</option>
                                                            @endforeach
                                                        </select>
                                                        <span class="form-text text-danger errItemEdit" style="display:none;">*Harap pilih gudang asal terlebih dahulu!</span>
                                                    </div>
                                                </div>

                                                <div class="form-group col-xl-6 col-sm-6 col-xs-12">
                                                    <label>Ke :</label>
                                                    <div class="input-group input-group-solid">
                                                        <select class="form-control select2 detailItemEdit getStok" id="indexEditT" name="indexEditT" style="width:100%;">
                                                            <option label="Label"></option>
                                                            @foreach($listIndex as $index)
                                                            <option value="{{$index['id']}}">{{strtoupper($index['nama_index'])}}</option>
                                                            @endforeach
                                                        </select>
                                                        <span class="form-text text-danger errItemEdit" style="display:none;">*Harap pilih gudang tujuan terlebih dahulu!</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group row mb-0">
                                                <div class="form-group col-xl-6">
                                                    <label>Stok :</label>
                                                    <div class="input-group">
                                                        <input type="text" id="stockEditFMask" class="form-control " autocomplete="off" readonly>
                                                        <input type="hidden" id="stockEditF" class="form-control " autocomplete="off" readonly>
                                                    </div>
                                                </div>

                                                <div class="form-group col-xl-6">
                                                    <label>Stok :</label>
                                                    <div class="input-group">
                                                        <input type="text" id="stockEditT" class="form-control " autocomplete="off" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label>Jumlah :</label>
                                                <div class="form-group form-group-feedback form-group-feedback-right">
                                                    <div class="input-group">
                                                        <input type="text" id="qty_itemEditMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right">
                                                        <input type="hidden" id="qty_itemEdit" min="0" class="form-control text-right detailItemEdit numericEdit" autocomplete="off">
                                                        <span class="form-text text-danger errItemEdit" style="display:none;">*Harap masukkan qty dus terlebih dahulu!</span>
                                                        <span class="form-text text-danger errItemEditNumeric" style="display:none;">*qty dus tidak dapat dibawah atau 0!</span>
                                                    </div>
                                                </div>
                                            </div>

                                        </fieldset>
                                    </div>
                                </div>
						    </div>

						    <div class="modal-footer">
							    <button type="button" class="btn btn-primary" id="btnEditItem">Simpan</button>
							    <button type="button"class="btn btn-light me-3" id="closeModal" data-dismiss="modal">batal</button>
						    </div>
					    </div>
				    </div>
			    </div>
				<!-- /horizontal form edit item -->
			</div>
			<!-- /content area -->
@endsection
@section('scripts')
    <script type="text/javascript">

        $(document).ready(function () {

            $('#indexF').select2({
                allowClear: true,
                placeholder: "Silahkan Pilih Gudang"
            });

            $('#indexT').select2({
                allowClear: true,
                placeholder: "Silahkan Pilih Gudang"
            });

            $('#indexEditF').select2({
                allowClear: true,
                dropdownParent: $('#modal_form_edit_item'),
                placeholder: "Silahkan Pilih Gudang"
            });

            $('#indexEditT').select2({
                allowClear: true,
                dropdownParent: $('#modal_form_edit_item'),
                placeholder: "Silahkan Pilih Gudang"
            });

            $('#product').select2({
                allowClear: true,
                placeholder: "Silahkan Pilih Item"
            });

            $('#productUnit').select2({
                allowClear: true,
                placeholder: "Pilih Satuan"
            });

            $('#tanggal_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                autoclose : true,
                "setDate": new Date(),
                format : "dd MM yyyy",
            });

            $("#qty_itemMask").autoNumeric('init');
            $("#qty_itemEditMask").autoNumeric('init');

            $("#tanggal_picker").datepicker('setDate', new Date());
        });

        $("#qty_itemMask").on('change', function() {
            $("#qty_item").val($("#qty_itemMask").autoNumeric("get"));
        });

        $("#qty_itemEditMask").on('change', function() {
            $("#qty_itemEdit").val($("#qty_itemEditMask").autoNumeric("get"));
        });

        $("#tanggal_picker").on('change', function() {
            $("#tanggal").val($("#tanggal_picker").data('datepicker').getFormattedDate('yyyy-mm-dd'));
        });

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
                text: "Apakah anda ingin membatalkan Pemindahan Barang?",
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

        $("#productUnit").on("change", function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/StockTransfer/GetDataProduct",
                method: 'POST',
                data: {
                    idProduct: $("#product option:selected").val(),
                    idSatuan: $("#productUnit option:selected").val(),
                },
                success: function(result){
                    if (result.length > 0) {
                        $("#kategori").val(ucwords(result[0].nama_kategori));
                        $("#merk").val(ucwords(result[0].nama_merk));
                        $("#jenis_barang").val(ucwords(ucwords(result[0].jenis_item)));
                    }
                }
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/StockTransfer/GetIndexList",
                method: 'POST',
                data: {
                    id_item: $("#product option:selected").val(),
                    idSatuan: $("#productUnit option:selected").val(),
                },
                success: function(result){
                    $('#indexF').find('option:not(:first)').remove();
                    if (result.length > 0) {
                        for (var i = 0; i < result.length;i++) {
                            if (result[i].id != null) {
                                $('#indexF').append($('<option>', {
                                    value:result[i].id,
                                    text:result[i].txt_index
                                }));
                            }
                        }
                    }
                }
            });
        });

        $("#product").on("change", function() {
            //getdataItem
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/SalesOrder/GetSatuan",
                method: 'POST',
                data: {
                    idProduct: $(this).val(),
                },
                success: function(result){
                    $('#productUnit').find('option:not(:first)').remove();
                    if (result.length > 0) {
                        for (var i = 0; i < result.length;i++) {
                            $("#productUnit").append($('<option>', {
                                value:result[i].id,
                                text:result[i].kode_satuan.toUpperCase() + ' - ' + result[i].nama_satuan.toUpperCase()
                            }));
                        }
                    }
                }
            });
        });

        $(".getStok").on("change", function() {
            var idItem = "";
            var idTextBox = $(this).attr('id');

            if ($("#ModeGetStok").val() == "add") {
                idItem = $("#product option:selected").val();
            }
            else if($("#ModeGetStok").val() == "edit") {
                idItem = $("#idItemEdit").val();
            }
            if (idItem != "" && $(this).val() != "") {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/StockTransfer/GetStock",
                    method: 'POST',
                    data: {
                        idProduct: idItem,
                        idIndex: $(this).val(),
                        idSatuan: $("#productUnit option:selected").val(),
                    },
                    success: function(result){
                        if (result.length > 0) {
                            if (idTextBox == "indexF") {
                                $("#StockFMask").val(parseFloat(result[0].stok_item).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                                $("#StockF").val(result[0].stok_item);
                            }
                            else if (idTextBox == "indexT") {
                                $("#StockT").val(parseFloat(result[0].stok_item).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                            }
                            else if (idTextBox == "indexEditF") {
                                $("#stockEditFMask").val(parseFloat(result[0].stok_item).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                                $("#stockEditF").val(result[0].stok_item);
                            }
                            else if (idTextBox == "indexEditT") {
                                $("#stockEditT").val(parseFloat(result[0].stok_item).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                            }
                        }
                    }
                });
            }
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
                    {
                        field: 'Actions',
                        title: 'Aksi',
                        sortable: false,
                        width: 110,
                        overflow: 'visible',
                        autoHide: false,
                        textAlign: 'center',
                        template: function(row) {
                            var txtAction = "<a href='#' class='btn btn-sm btn-clean btn-icon edit' title='Ubah' onclick='editDetailItem("+row.id+","+row.id_item+");return false;'>";
                                txtAction += "<i class='la la-edit'></i>";
                                txtAction += "</a>";
                                txtAction += "<a href='#' class='btn btn-sm btn-clean btn-icon' title='Hapus' onclick='deleteDetailItem("+row.id+");return false;'>";
                                txtAction += "<i class='la la-trash'></i>";
                                txtAction += "</a>";

                            return txtAction;
                        },
                    }
                ],
            });
        });

        $("#btnAddItem").on('click', function(e) {
			var errCount = 0;


            if ($("#indexF option:selected").val() == $("#indexT option:selected").val()) {

                Swal.fire(
                    "Gagal!",
                    "Tidak Dapat melakukan pemindahan barang ke lokasi yang sama !",
                    "warning"
                );
                return false;
            }

            if (parseFloat($("#StockF").val()) < 1) {

                Swal.fire(
                    "Gagal!",
                    "Lokasi Sumber Pemindahan Barang tidak dapat 0 atau dibawahnya!",
                    "warning"
                );
                return false;
            }

            if (parseFloat($("#qty_item").val()) > parseFloat($("#StockF").val())) {

                Swal.fire(
                    "Gagal!",
                    "Pemindahan Barang tidak dapat melebihi jumlah stok pada lokasi!",
                    "warning"
                );
                return false;
            }

			$(".detailItem").each(function(){
				if($(this).val() == "" || $(this).children("option:selected").val() == ""){
				   	$(this).closest('.form-group, input-group').find('.errItem').show();
				  	errCount = errCount + 1;
				}
				else {
					$(this).closest('.form-group, input-group').find('.errItem').hide();
				}
			});

            $(".numeric").each(function() {
                if(parseFloat($(this).val()) < 1){
				   	$(this).closest('.form-group, input-group').find('.errItemNumeric').show();
				  	errCount = errCount + 1;
				}
				else {
					$(this).closest('.form-group, input-group').find('.errItemNumeric').hide();
				}
            });

			if (errCount == 0) {
                Swal.fire({
                    title: "Tambah Pemindahan?",
                    text: "Apakah data sudah sesuai?",
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
                            url: "/StockTransfer/StoreDetail",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idTransfer : '',
                                idItem : $("#product option:selected").val(),
                                idSatuan : $("#productUnit option:selected").val(),
                                idIndexF : $("#indexF option:selected").val(),
                                idIndexT : $("#indexT option:selected").val(),
                                qtyItem : $("#qty_item").val()
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Pemindahan Barang Berhasil ditambahkan!.",
                                        "success"
                                    )
                                    $("#product").val("").trigger('change');
                                    $("#indexF").val("").trigger('change');
                                    $("#indexT").val("").trigger('change');
                                    $("#qty_itemMask").val("");
                                    $("#qty_item").val("");
                                    $("#StockF").val("");
                                    $("#StockFMask").val("");
                                    $("#StockT").val("");
                                    $("#jenis_barang").val("");
                                    $("#merk").val("");
                                    $("#kategori").val("");
                                    var datatable = $('#list_item').KTDatatable();
                                        datatable.setDataSourceParam('idTransfer', '');
                                        datatable.reload();
                                }
                                else if (result == "failDuplicate") {
                                    Swal.fire(
                                        "Gagal!",
                                        "Data Pemindahan sudah tersedia pada List Pemindahan !",
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

        $("#btnEditItem").on('click', function(e) {
			var errCount = 0;

			$(".detailItemEdit").each(function(){
				if($(this).val() == "" || $(this).children("option:selected").val() == ""){
					$(this).closest('.form-group, input-group').find('.errItemEdit').show();
					errCount = errCount + 1;
				}
				else {
					$(this).closest('.form-group, input-group').find('.errItemEdit').hide();
				}
			});

            $(".numericEdit").each(function(){
				if(parseFloat($(this).val()) < 1){
					$(this).closest('.form-group, input-group').find('.errItemEditNumeric').show();
					errCount = errCount + 1;
				}
				else {
					$(this).closest('.form-group, input-group').find('.errItemEditNumeric').hide();
				}
			});

            if (parseFloat($("#stockEditF").val()) < 1) {

                Swal.fire(
                    "Gagal!",
                    "Lokasi Sumber Pemindahan Barang tidak dapat 0 atau dibawahnya!",
                    "warning"
                );
                return false;
            }

            if (parseFloat($("#qty_itemEdit").val()) > parseFloat($("#stockEditF").val())) {

                Swal.fire(
                    "Gagal!",
                    "Pemindahan Barang tidak dapat melebihi jumlah stok pada lokasi!",
                    "warning"
                );
                return false;
            }

			if (errCount == 0) {
                Swal.fire({
                    title: "Update Item?",
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
                            url: "/StockTransfer/UpdateDetail",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idDetail : $("#idDetail").val(),
                                idTransfer : "",
                                idItem : $("#idItemEdit").val(),
                                idSatuan : $("#idSatuanEdit").val(),
                                idIndexF : $("#indexEditF option:selected").val(),
                                idIndexT : $("#indexEditT option:selected").val(),
                                qtyItem : $("#qty_itemEdit").val(),
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Item Berhasil diupdate!.",
                                        "success"
                                    )
                                    $("#idDetail").val("");
                                    $("#indexEditF").val("").trigger('change');
                                    $("#indexEditT").val("").trigger('change');
                                    $("#qty_itemEditMask").val("");
                                    $("#qty_itemEdit").val("");
                                    var datatable = $('#list_item').KTDatatable();
                                        datatable.setDataSourceParam('idTransfer','');
                                        datatable.reload();
                                    $("#closeModal").trigger('click');
                                }
                                else if (result == "failDuplicate") {
                                    Swal.fire(
                                        "Gagal!",
                                        "Data Pemindahan sudah tersedia pada List Pemindahan !",
                                        "warning"
                                    )
                                }
                            }
                        });
                    }
                    else if (result.dismiss === "cancel") {
                        return false;
                    }
                });
			}
            else {
                return false;
            }
		});

        $("#modal_form_edit_item").on("hide.bs.modal", function () {
            $("#ModeGetStok").val("add");
        });

        function editDetailItem(id, idItem) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/StockTransfer/GetIndexList",
                method: 'POST',
                data: {
                    id_item: idItem,
                },
                success: function(result){
                    $('#indexEditF').find('option:not(:first)').remove();
                    if (result.length > 0) {
                        for (var i = 0; i < result.length;i++) {
                            if (result[i].id != null) {
                                $('#indexEditF').append($('<option>', {
                                    value:result[i].id,
                                    text:result[i].txt_index
                                }));
                            }
                        }
                    }
                }
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/StockTransfer/EditDetail",
                method: 'POST',
                data: {
                    idDetail: id
                },
                success: function(result){
                    if (result.length > 0) {
                        for (var i = 0; i < result.length;i++) {
                            $("#idItemEdit").val(result[i].id_item);
                            $("#idSatuanEdit").val(result[i].id_satuan);
                            $("#idIndexEditF").val(result[i].id_index_f);
                            $("#indexEditF").val(result[i].id_index_f);
                            $("#idDetail").val(result[i].id);

                            $("#idIndexEditT").val(result[i].id_index_t);
                            $("#indexEditT").val(result[i].id_index_t);


                            $("#qty_itemEditMask").val(parseFloat(result[i].qty_item).toLocaleString('id-ID', { maximumFractionDigits: 2})).trigger('change');

                            var kodeItem = "";
                            if (result[i].value_spesifikasi != null) {
                                kodeItem = '('+result[i].value_spesifikasi+')'+result[i].kode_item.toUpperCase();
                            }
                            else {
                                kodeItem = result[i].kode_item.toUpperCase();
                            }

                            $("#BarangEdit").val(kodeItem + ' - ' + result[i].nama_item);
                            $("#ModeGetStok").val("edit");
                            $("#btnModalEditItem").trigger('click');
                        }
                    }
                    $("#indexEditF").trigger("change");
                    $("#indexEditT").trigger("change");
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
                        url: "/StockTransfer/DeleteDetail",
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
                        datatable.setDataSourceParam('kodeAdjustment', 'DRAFT');
                        datatable.reload();
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

        $("#form_add").submit(function(e){
            e.preventDefault();

            var dataCount = $('#list_item >table >tbody >tr').length;
            Swal.fire({
                title: "Simpan Data?",
                text: "Apakah data sudah sesuai?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    var count = 0;
                    $(".req").each(function(){
                        if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                            $(this).closest('.form-group').find('.err').show();
                            count = parseInt(count) + 1;
                        }
                        else {
                            $(this).closest('.form-group').find('.err').hide();
                        }
                    });

                    if(parseInt(dataCount) < 1) {
                        Swal.fire(
                            "Gagal!",
                            "Harap Tambahkan Minimum 1 Item Transaksi!.",
                            "warning"
                        )
                        count = parseInt(count) + 1;
                    }

                    if (count == 0) {
                        $("#form_add").off("submit").submit();
                    }
                    else {
                        $("html, body").animate({ scrollTop: 0 }, "slow");
                        e.preventDefault();
                    }
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
		});
    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
