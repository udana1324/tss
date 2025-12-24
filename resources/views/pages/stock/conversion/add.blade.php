@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h6 class="card-title text-white">Buat Konversi Stok Barang</h6>
					</div>
                    <form action="{{ route('StockConversion.store') }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            <div class="row">
								<div class="col-md-6">
									<fieldset>
                                        <legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi Barang </h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>

                                        <div class="form-group">
                                            <label class="col-lg-12 col-form-label">Nama Barang :</label>
                                            <div class="col-lg-12">
                                                <select class="form-control select2 req" id="product" name="product">
                                                    <option label="Label"></option>
                                                    @foreach($dataProduct as $product)
                                                    <option value="{{$product->id}}">{{strtoupper($product->nama_item)}}</option>
                                                    @endforeach
                                                </select>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih barang terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-lg-12 col-form-label">Gudang : </label>
                                                    <div class="col-lg-12">
                                                        <select class="form-control select2 getDataProduct detailIndex" id="index" name="index" style="width:100%;">
                                                            <option label="Label"></option>
                                                            @foreach($listIndex as $index)
                                                            <option value="{{$index['id']}}">{{strtoupper($index['nama_index'])}}</option>
                                                            @endforeach
                                                        </select>
                                                        <span class="form-text text-danger errIndex" style="display:none;">*Harap pilih gudang terlebih dahulu!</span>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-lg-12 col-form-label">Kategori : </label>
                                                    <div class="col-lg-12">
                                                        <input type="text" class="form-control" id="kategori" readonly>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-lg-12 col-form-label">Satuan : </label>
                                                    <div class="col-lg-12">
                                                        <select class="form-control select2 detailUnit getDataProduct" id="productUnit" name="productUnit">
                                                            <option label="Label"></option>
                                                        </select>
                                                        <span class="form-text text-danger errUnit" style="display:none;">*Harap pilih satuan item terlebih dahulu!</span>
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

                                            <div class="col-md-6">

                                                <div class="form-group">
                                                    <label class="col-lg-12 col-form-label">Stok Saat Ini : </label>
                                                    <div class="col-lg-12">
                                                        <input type="text" class="form-control" id="stock_item" readonly>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

									</fieldset>
								</div>

								<div class="col-md-6">
									<fieldset>
					                	<legend class="text-muted"><h6><i class="fab la-buffer"></i> Rincian Konversi</h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-lg-6 col-form-label">Jenis Konversi : </label>
                                                    <div class="col-lg-12">
                                                        <select class="form-control select2 req" id="jenis_conversion" name="jenis_conversion">
                                                            <option label="Label"></option>
                                                            {{-- <option value="retur_purc">Purchase Return</option>
                                                            <option value="retur_sale">Sale Return</option> --}}
                                                            <option value="out">Konversi Keluar</option>
                                                            <option value="in">Konversi Masuk</option>
                                                        </select>
                                                        <span class="form-text text-danger err" style="display:none;">*Harap pilih jenis konversi terlebih dahulu!</span>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-lg-6 col-form-label">Tanggal : </label>
                                                    <div class="col-lg-12">
                                                        <input type="hidden" class="form-control req" name="tanggal_conversion" id="tanggal_conversion" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}">
                                                        <input type="text" class="form-control" placeholder="Pilih Tanggal" name="tanggal_conversion_picker" id="tanggal_conversion_picker" readonly>
                                                        <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal konversi terlebih dahulu!</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-lg-6 col-form-label">Jumlah Item : </label>
                                            <div class="col-lg-12">
                                                <input type="text" id="qtyItemMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right">
                                                <input type="hidden" id="qtyItem" class="form-control text-right req numericVal">
                                                <span class="form-text text-danger err" style="display:none;">*Harap masukkan Jumlah Item terlebih dahulu!</span>
                                                <span class="form-text text-danger errItemNumeric" style="display:none;">*Jumlah Barang tidak dapat dibawah atau 0!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-lg-6 col-form-label">Keterangan : </label>
                                            <div class="col-lg-12">
                                                <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Ketik Keterangan Disini"></textarea>
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
                                <button type="button" style="display: none;" id="btnModalEditItem" data-toggle="modal" data-target="#modal_form_edit_item"></button>
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

							    <h5 class="modal-title" id="editText">Ubah Qty</h5>
						    </div>
						    <div class="modal-body">
							    <form >
								    <table class="table display" id="list_edit_item" width="100%">
									    <thead>
										    <tr>
											    <th align="center" style="text-align:center;display: none;">Id</th>
											    <th align="center" style="text-align:center;">Item</th>
											    <th align="center" style="text-align:center;">Satuan</th>
											    <th align="center" style="text-align:center;">Jumlah</th>
										    </tr>
									    </thead>
									    <tbody id="detil_edit_item">

									    </tbody>
								    </table>
							    </form>

						    </div>

						    <div class="modal-footer">
							    <button type="button" class="btn btn-primary" id="btnEditItem" data-dismiss="modal">Simpan</button>
							    <button type="button" class="btn btn-link" data-dismiss="modal">Tutup</button>
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

            $('#jenis_conversion').select2({
                allowClear: true,
                placeholder: "Silahkan Pilih Jenis Konversi"
            });

            $('#product').select2({
                allowClear: true,
                placeholder: "Silahkan Pilih Item"
            });

            $('#productUnit').select2({
                allowClear: true,
                placeholder: "Pilih satuan..."
            });

            $('#index').select2({
                allowClear: true,
                placeholder: "Pilih Lokasi..."
            });

            $('#tanggal_conversion_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                autoclose : true,
                "setDate": new Date(),
                format : "dd MM yyyy",
            });

            $("#qtyItemMask").autoNumeric('init');

            $("#tanggal_conversion_picker").datepicker('setDate', new Date());
        });

        $("#qtyItemMask").on('change', function() {
            $("#qtyItem").val($("#qtyItemMask").autoNumeric("get"));
        });

        $("#tanggal_conversion_picker").on('change', function() {
            var adjDate = new Date($(this).datepicker('getDate'));
            $("#tanggal_conversion").val($("#tanggal_conversion_picker").data('datepicker').getFormattedDate('yyyy-mm-dd'));
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
                text: "Apakah anda ingin membatalkan Konversi Barang?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    window.location.href = "{{ url('/Stock/Adjustment') }}";
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
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

        $(".getDataProduct").on("change", function() {
            var item = $("#product option:selected").val();
            var satuan = $("#productUnit option:selected").val();
            var index = $("#index option:selected").val();

            if (satuan != "" && index != "") {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/Stock/GetDataProduct",
                    method: 'POST',
                    data: {
                        idProduct: item,
                        idSatuan: satuan,
                        idIndex: index,
                    },
                    success: function(result){
                        if (result.length > 0) {
                            $("#kategori").val(ucwords(result[0].nama_kategori));
                            $("#merk").val(ucwords(result[0].nama_merk));
                            $("#satuan").val(ucwords(result[0].nama_satuan));
                            $("#jenis_barang").val(ucwords(ucwords(result[0].jenis_item)));
                            $("#stock_item_mask").val(parseFloat(result[0].stok_item).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                            $("#stock_item").val(parseFloat(result[0].stok_item));
                        }
                        else {
                            $("#kategori").val("");
                            $("#merk").val("");
                            $("#satuan").val("");
                            $("#jenis_barang").val("");
                            $("#stock_item").val("");
                            $("#stock_item_mask").val("");
                        }
                    }
                });
            }
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
                        },
                    },
                    pageSize: 10,
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
                        width: 85,
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
                    {
                        field: 'Actions',
                        title: 'Aksi',
                        sortable: false,
                        width: 75,
                        overflow: 'visible',
                        autoHide: false,
                        textAlign: 'center',
                        template: function(row) {
                            var txtAction = "<a href='#' class='btn btn-sm btn-clean btn-icon edit' title='Ubah' onclick='editDetailItem("+row.id+");return false;'>";
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
                        },
                    },
                    pageSize: 10,
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
                        width: 85,
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
                    {
                        field: 'Actions',
                        title: 'Aksi',
                        sortable: false,
                        width: 75,
                        overflow: 'visible',
                        autoHide: false,
                        textAlign: 'center',
                        template: function(row) {
                            var txtAction = "<a href='#' class='btn btn-sm btn-clean btn-icon edit' title='Ubah' onclick='editDetailItem("+row.id+");return false;'>";
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

			$(".req").each(function(){
				if($(this).val() == "" || $(this).children("option:selected").val() == ""){
				   	$(this).closest('.form-group, input-group').find('.err').show();
				  	errCount = errCount + 1;
				}
				else {
					$(this).closest('.form-group, input-group').find('.err').hide();
				}
			});

            $(".detailUnit").each(function(){
				if($(this).val() == "" || $(this).children("option:selected").val() == ""){
				   	$(this).closest('.form-group, input-group').find('.errUnit').show();
				  	errCount = errCount + 1;
				}
				else {
					$(this).closest('.form-group, input-group').find('.errUnit').hide();
				}
			});

            $(".detailIndex").each(function(){
				if($(this).val() == "" || $(this).children("option:selected").val() == ""){
				   	$(this).closest('.form-group, input-group').find('.errIndex').show();
				  	errCount = errCount + 1;
				}
				else {
					$(this).closest('.form-group, input-group').find('.errIndex').hide();
				}
			});

            $(".numericVal").each(function() {
                if(parseFloat($(this).val()) < 1){
				   	$(this).closest('.form-group, input-group').find('.errItemNumeric').show();
				  	errCount = errCount + 1;
				}
				else {
					$(this).closest('.form-group, input-group').find('.errItemNumeric').hide();
                    if ($("#jenis_conversion option:selected").val() == "out") {
                        if(parseFloat($("#qtyItem").val()) > parseFloat($("#stock_item").val())) {
                            errCount = errCount + 1;
                            Swal.fire(
                                "Gagal!",
                                "Jumlah Konversi tidak dapat melebihi jumlah stok saat ini !",
                                "warning"
                            )
                        }
                    }
				}
            });

			if (errCount == 0) {
                Swal.fire({
                    title: "Tambah Barang?",
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
                            url: "/StockConversion/StoreDetail",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idItem : $("#product option:selected").val(),
                                idSatuan : $("#productUnit option:selected").val(),
                                idIndex : $("#index option:selected").val(),
                                jenisConversion : $("#jenis_conversion option:selected").val(),
                                qtyItem : $("#qtyItem").val(),
                                tgl : $("#tanggal_conversion").val(),
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Barang Berhasil ditambahkan!.",
                                        "success"
                                    )
                                    $("#product").val("").trigger('change');
                                    $("#jenis_conversion").val("").trigger('change');
                                    $("#qtyItemMask").val("").trigger('change');
                                    $("#tanggal_conversion_picker").datepicker('setDate', new Date());
                                    $("#merk").val("");
                                    $("#kategori").val("");
                                    $("#satuan").val("");
                                    $("#jenis_barang").val("");
                                    $("#stock_item").val("");
                                    $("#stock_item_mask").val("");
                                    var datatableFrom = $('#list_item_from').KTDatatable();
                                        datatableFrom.setDataSourceParam('idConversion', 'DRAFT');
                                        datatableFrom.reload();

                                    var datatableTo = $('#list_item_to').KTDatatable();
                                        datatableTo.setDataSourceParam('idConversion', 'DRAFT');
                                        datatableTo.reload();
                                }
                                else if (result == "failDuplicate") {
                                    Swal.fire(
                                        "Gagal!",
                                        "Data Barang sudah tersedia pada List Konversi !",
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
                url: "/StockConversion/EditDetail",
                method: 'POST',
                data: {
                    idDetail: id,
                },
                success: function(result){
                    if (result.length > 0) {
                        var qty = parseFloat(result[0].qty_item);
                        var qtyFixed = qty.toString().replace(".", ",");
                        var data = "<tr>";
                            data += "<td style='display:none;'><input type='text' class='form-control' id='idRowEdit' value='"+result[0].id+"' /></td>";
                            data += "<td style='display:none;'><input type='text' class='form-control' id='idItemEdit' value='"+result[0].id_item+"' /></td>";
                            data += "<td style='display:none;'><input type='text' class='form-control' id='idSatuanEdit' value='"+result[0].id_satuan+"' /></td>";
                            data += "<td style='display:none;'><input type='text' class='form-control' id='idIndexEdit' value='"+result[0].id_index+"' /></td>";
                            data += "<td style='display:none;'><input type='text' class='form-control' id='stockEdit' value='"+result[0].stok_item+"' /></td>";
                            data += "<td style='display:none;'><input type='text' class='form-control' id='jenisEdit' value='"+result[0].jenis+"' /></td>";
                            data += "<td style='text-align:center;'>"+result[0].kode_item.toUpperCase() + ' - ' + result[0].nama_item.toUpperCase()+"</td>";
                            data += "<td style='text-align:center;'>"+result[0].nama_satuan.toUpperCase()+"</td>";
                            data += "<td style='text-align:center;'><input type='text' class='form-control inputEdit numericValEdit' id='qtyRowEditMask' autocomplete='off' data-a-dec=',' data-a-sep='.' value='"+qtyFixed+"' /></td>";
                            data += "<td style='text-align:center;'><input type='hidden' class='form-control inputEdit numericValEdit' id='qtyRowEdit' value='"+qtyFixed+"' /></td>";
                            data += "</tr>";
                            $('#detil_edit_item').append(data);

                            if (result[0].jenis == "out") {
                                $("#editText").html("Ubah Qty Asal");
                            }
                            else {
                                $("#editText").html("Ubah Qty Hasil");
                            }

                            $("#qtyRowEditMask").autoNumeric('init');
                            $("#qtyRowEditMask").on('change', function() {
                                $("#qtyRowEdit").val($("#qtyRowEditMask").autoNumeric("get"));
                            });

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
                        url: "/StockConversion/DeleteDetail",
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
                    var datatableFrom = $('#list_item_from').KTDatatable();
                        datatableFrom.setDataSourceParam('idConversion', 'DRAFT');
                        datatableFrom.reload();

                    var datatableTo = $('#list_item_to').KTDatatable();
                        datatableTo.setDataSourceParam('idConversion', 'DRAFT');
                        datatableTo.reload();
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
            var idSatuan = $("#idSatuanEdit").val();
            var idIndex = $("#idIndexEdit").val();
	     	var qty = $("#qtyRowEdit").val();
	     	var stokItem = $("#stockEdit").val();
            var jenis = $("#jenisEdit").val();

            $(".inputEdit").each(function(){
                if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                    Swal.fire(
                        "Gagal!",
                        "Terdapat kolom kosong, harap mengisi kolom kosong terlebih dahulu !",
                        "warning"
                    )
                    errCount = parseInt(errCount) + 1;
                }
            });

            $(".numericValEdit").each(function() {
                if(parseFloat($(this).val()) < 1){
                    if (parseFloat(qty) > parseFloat(stokItem)) {
                        Swal.fire(
                            "Gagal!",
                            "Jumlah Barang tidak dapat dibawah atau 0!",
                            "warning"
                        )
                        errCount = parseInt(errCount) + 1;
                    }
				  	errCount = errCount + 1;
				}
				else {
                    if (jenis == "out") {
                        if (parseFloat(qty) > parseFloat(stokItem)) {
                            Swal.fire(
                                "Gagal!",
                                "Jumlah Konversi tidak dapat melebihi jumlah stok saat ini !",
                                "warning"
                            )
                            errCount = parseInt(errCount) + 1;
                        }
                    }
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
                            url: "/StockConversion/UpdateDetail",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idItem : idItem,
                                idSatuan : idSatuan,
                                idIndex : idIndex,
                                idConversion : "",
                                idDetail : idRow,
                                qtyItem : qty,
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Item Berhasil diubah!.",
                                        "success"
                                    )
                                    var datatableFrom = $('#list_item_from').KTDatatable();
                                        datatableFrom.setDataSourceParam('idConversion', 'DRAFT');
                                        datatableFrom.reload();

                                    var datatableTo = $('#list_item_to').KTDatatable();
                                        datatableTo.setDataSourceParam('idConversion', 'DRAFT');
                                        datatableTo.reload();
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
                e.preventDefault();
            }
	    });

        $("#form_add").submit(function(e){
            e.preventDefault();
            var datatableFrom = $('#list_item_from').KTDatatable();

            var datatableTo = $('#list_item_to').KTDatatable();

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
                    if(datatableFrom.getTotalRows() < 1) {
                        $("#errTblFrom").show();
                        count = parseInt(count) + 1;
                    }
                    else {
                        $("#errTblFrom").hide();
                    }
                    if(datatableTo.getTotalRows() < 1) {
                        $("#errTblTo").show();
                        count = parseInt(count) + 1;
                    }
                    else {
                        $("#errTblTo").hide();
                    }
                    if (count == 0) {
                        $("#form_add").off("submit").submit();
                    }
                    else {
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
