@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h6 class="card-title text-white">Penyesuaian Stok Barang</h6>
					</div>
                    <form action="{{ route('Stock.store') }}" class="form-horizontal" id="form_add" method="POST">
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
                                                    <option value="{{$product->id}}">{{ $product->value_spesifikasi != null ? '('.$product->value_spesifikasi.')' : "" }}{{strtoupper($product->kode_item)}} - {{strtoupper($product->nama_item)}}</option>
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
					                	<legend class="text-muted"><h6><i class="fab la-buffer"></i> Rincian Penyesuaian</h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-lg-12 col-form-label">Jenis Penyesuaian : </label>
                                                    <div class="col-lg-12">
                                                        <select class="form-control select2 req" id="jenis_adjustment" name="jenis_adjustment">
                                                            <option label="Label"></option>
                                                            {{-- <option value="retur_purc">Purchase Return</option>
                                                            <option value="retur_sale">Sale Return</option> --}}
                                                            <option value="penambahan">Penambahan</option>
                                                            <option value="pengurangan">Pengurangan</option>
                                                        </select>
                                                        <span class="form-text text-danger err" style="display:none;">*Harap pilih jenis penyesuaian terlebih dahulu!</span>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-lg-6 col-form-label">Tanggal : </label>
                                                    <div class="col-lg-12">
                                                        <input type="hidden" class="form-control req" name="tanggal_adj" id="tanggal_adj" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}">
                                                        <input type="text" class="form-control" placeholder="Pilih Tanggal" name="tanggal_adj_picker" id="tanggal_adj_picker" readonly>
                                                        <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal Adjustment terlebih dahulu!</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-lg-6 col-form-label">Jumlah Penyesuaian : </label>
                                            <div class="col-lg-12">
                                                <input type="text" id="qtyItemMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right">
                                                <input type="hidden" id="qtyItem" class="form-control text-right detailItem numericVal">
                                                <span class="form-text text-danger err" style="display:none;">*Harap masukkan Jumlah penyesuaian terlebih dahulu!</span>
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
                                <div class="col-md-12">
                                    <fieldset>
                                        <legend class="text-muted"><h6><i class="la la-list"></i> List Penyesuaian</h6></legend>
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
                                <button type="button" style="display: none;" id="btnModalEditItem" data-toggle="modal" data-target="#modal_form_edit_item"></button>
                                <button type="submit" class="btn btn-light-primary font-weight-bold mr-2"> Simpan <i class="flaticon-paper-plane-1"></i></button>
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

            $('#jenis_adjustment').select2({
                allowClear: true,
                placeholder: "Silahkan Pilih Jenis Adjustment"
            });

            $('#index').select2({
                allowClear: true,
                placeholder: "Silahkan Pilih Gudang"
            });

            $('#product').select2({
                allowClear: true,
                placeholder: "Silahkan Pilih Item"
            });

            $('#productUnit').select2({
                allowClear: true,
                placeholder: "Pilih satuan..."
            });

            $('#tanggal_adj_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                autoclose : true,
                "setDate": new Date(),
                format : "dd MM yyyy",
            });

            $("#qtyItemMask").autoNumeric('init');

            $("#tanggal_adj_picker").datepicker('setDate', new Date());
        });

        $("#qtyItemMask").on('change', function() {
            $("#qtyItem").val($("#qtyItemMask").autoNumeric("get"));
        });

        $("#tanggal_adj_picker").on('change', function() {
            var adjDate = new Date($(this).datepicker('getDate'));
            $("#tanggal_adj").val($("#tanggal_adj_picker").data('datepicker').getFormattedDate('yyyy-mm-dd'));
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
                text: "Apakah anda ingin membatalkan Penyesuaian Barang?",
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
                            $("#stock_item").val(parseFloat(result[0].stok_item).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        }
                        else {
                            $("#kategori").val("");
                            $("#merk").val("");
                            $("#satuan").val("");
                            $("#jenis_barang").val("");
                            $("#stock_item").val("");
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
                            url: '/Stock/GetAdjustment',
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
                        title: 'Kode',
                        autoHide: false,
                        width: 75,
                        textAlign: 'center',
                        template: function(row) {
                            if(row.value_spesifikasi != null) {
                                return '('+row.value_spesifikasi+')'+row.kode_item.toUpperCase();
                            }
                            else {
                                return row.kode_item.toUpperCase();
                            }
                        },
                    },
                    {
                        field: 'nama_item',
                        title: 'Nama',
                        width: 150,
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            return ucwords(row.nama_item);
                        },
                    },
                    {
                        field: 'tgl_transaksi',
                        title: 'Tanggal',
                        width: 100,
                        textAlign: 'center',
                        template: function(row) {
                            if (row.tgl_transaksi != null) {
                                return formatDate(row.tgl_transaksi);
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'jenis_adjustment',
                        title: 'Jenis Adjustment',
                        width: 80,
                        textAlign: 'center',
                        template: function(row) {
                            if (row.jenis_adjustment != null) {
                                return ucwords(row.jenis_adjustment);
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
                        field: 'qty_item',
                        width: 80,
                        title: 'Jumlah',
                        textAlign: 'center',
                        template: function(row) {
                            return parseFloat(row.qty_item).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'nama_satuan',
                        width: 85,
                        title: 'Satuan',
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
                        field: 'Actions',
                        title: 'Aksi',
                        sortable: false,
                        width: 70,
                        overflow: 'visible',
                        autoHide: false,
                        textAlign: 'center',
                        template: function(row) {
                            var txtAction = "<a href='#' class='btn btn-sm btn-clean btn-icon' title='Hapus' onclick='deleteDetailItem("+row.id+");return false;'>";
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

			if (errCount == 0) {
                Swal.fire({
                    title: "Tambah Penyesuaian?",
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
                            url: "/Stock/StoreAdjustment",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idItem : $("#product option:selected").val(),
                                idSatuan : $("#productUnit option:selected").val(),
                                idIndex : $("#index option:selected").val(),
                                jenisAdjustment : $("#jenis_adjustment option:selected").val(),
                                qtyItem : $("#qtyItem").val(),
                                tgl : $("#tanggal_adj").val(),
                                keterangan : $("#keterangan").val(),
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Penyesuaian Berhasil ditambahkan!.",
                                        "success"
                                    )
                                    $("#product").val("").trigger('change');
                                    $("#index").val("").trigger('change');
                                    $("#jenis_adjustment").val("").trigger('change');
                                    $("#qtyItemMask").val("").trigger('change');
                                    $("#tanggal_adj_picker").datepicker('setDate', new Date());
                                    $("#keterangan").val("");
                                    $("#merk").val("");
                                    $("#kategori").val("");
                                    $("#satuan").val("");
                                    $("#jenis_barang").val("");
                                    $("#stock_item").val("");
                                    var datatable = $('#list_item').KTDatatable();
                                        datatable.setDataSourceParam('kodeAdjustment', 'DRAFT');
                                        datatable.reload();
                                }
                                else if (result == "failDuplicate") {
                                    Swal.fire(
                                        "Gagal!",
                                        "Data penyesuaian sudah tersedia pada List Penyesuaian !",
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
                        url: "/Stock/DeleteAdjustment",
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
            var datatable = $('#list_item').KTDatatable();
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
                    $("#form_add").off("submit").submit();
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
		});
    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
