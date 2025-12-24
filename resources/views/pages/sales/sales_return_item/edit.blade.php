@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h6 class="card-title text-white">Ubah Retur Penjualan (Barang)</h6>
					</div>
                    <form action="{{ route('SalesReturnItem.update', $dataRetur->id) }}" class="form-horizontal" id="form_add" method="POST" enctype="multipart/form-data">
					    <div class="card-body">
                            {{ csrf_field() }}
                            @method('PUT')
                            <div class="row">
								<div class="col-md-6">
									<fieldset>
                                        <legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi Pembeli / Customer </h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
										<div class="form-group row d-none">
                                            <input type="hidden" value="load" id="mode" />
                                            <label class="col-lg-3 col-form-label">Kode Retur :</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control bg-slate-600 border-slate-600 border-1" placeholder="Auto Generated" name="kode_so" id="kode_so" value="{{$dataRetur->kode_retur}}" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Customer :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <select class="form-control select2 req" id="customer" name="customer">
                                                        <option label="Label"></option>
                                                        @foreach($dataCustomer as $customer)
                                                        <option value="{{$customer->id}}">{{strtoupper($customer->nama_customer)}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih customer terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>No. Surat Jalan Retur :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="nmr_sj_retur" id="nmr_sj_retur" value="{{$dataRetur->no_dokumen_retur}}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Tanggal Retur :</label>
                                            <div class="form-group divTgl form-group-feedback form-group-feedback-right">
                                                <input type="hidden" class="form-control tglValue req" name="tanggal_retur" id="tanggal_retur" >
                                                <input type="text" class="form-control pickerTgl" placeholder="Pilih Tanggal" name="tanggal_retur_picker" id="tanggal_retur_picker" readonly>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal retur terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Keterangan :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <textarea class="form-control elastic" id="keterangan" name="keterangan" rows="3" placeholder="Ketik keterangan Disini">{{$dataRetur->keterangan}}</textarea>
                                                </div>
                                            </div>
                                        </div>

									</fieldset>
								</div>

								<div class="col-md-6">
									<fieldset>
					                	<legend class="text-muted"><h6><i class="fab la-buffer"></i> Rincian Barang Retur</h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>

										<div class="form-group">
                                            <label class="col-lg-7">Nama Barang :</label> <span class="col-lg-5" id="txtRiwayat"></span>
                                            <div class="input-group">
                                                <div class="col-8 pl-0">
                                                    <select class="form-control select2 detailItem" id="product" name="product">
                                                        <option label="Label"></option>
                                                    </select>
                                                    <span class="form-text text-danger errItem" style="display:none;">*Harap pilih item terlebih dahulu!</span>
                                                </div>
                                                <div class="col-4 pr-0">
        											<select class="form-control select2 detailUnit" id="productUnit" name="productUnit">
                                                        <option label="Label"></option>
                                                    </select>
                                                    <span class="form-text text-danger errUnit" style="display:none;">*Harap pilih satuan item terlebih dahulu!</span>
        										</div>
                                            </div>
                                        </div>

										<div class="form-group">
											<label>Jumlah Retur Barang :</label>
											<div class="input-group">
        										<div class="col-8 pl-0">
                                                    <input type="text" id="qtyItemMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right">
                                                    <input type="hidden" id="qtyItem" class="form-control text-right detailItem numericVal">
        											<span class="form-text text-danger errItem" style="display:none;">*Harap masukkan Jumlah retur item terlebih dahulu!</span>
                                                    <span class="form-text text-danger errItemNumeric" style="display:none;">*Jumlah Barang tidak dapat dibawah atau 0!</span>
                                                    <span class="form-text text-danger errItemQty" style="display:none;">*Jumlah Barang tidak dapat melebihi jumlah yang dijual kepada supplier!</span>
                                                </div>
        										<div class="col-4 pr-0">
                                                    <select class="form-control select2 detailIndex" id="index" name="index">
                                                        <option label="Label"></option>
                                                        @foreach($listIndex as $index)
                                                        <option value="{{$index['id']}}">{{strtoupper($index['nama_index'])}}</option>
                                                        @endforeach
                                                    </select>
                                                    <span class="form-text text-success" id="txtStok"></span>
                                                    <input type="hidden" id="txtStokHidden" class="form-control">
                                                    <span class="form-text text-danger errIndex" style="display:none;">*Harap pilih lokasi gudang terlebih dahulu!</span>
        										</div>
											</div>
										</div>

                                        <div class="form-group row">
											<label class="col-lg-3 col-form-label"></label>
											<div class="col-lg-9">
												<button type="button" class="btn btn-primary font-weight-bold" id="btnAddItem">Tambah List Retur</button>
											</div>
										</div>

									</fieldset>
								</div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset>
                                        <legend class="text-muted"><h6><i class="la la-list"></i> List Retur Barang</h6></legend>
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

							    <h5 class="modal-title text-white">Ubah Retur</h5>
						    </div>
						    <div class="modal-body">
							    <div class="row">
                                    <div class="col-md-12">
                                        <fieldset>
                                            <legend class="text-muted"><h6><i class="fab la-buffer"></i> Rincian Barang Retur</h6></legend>
                                            <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                            <br>
                                            <div class="form-group">
                                                <label>Barang :</label>
                                                <div class="form-group form-group-feedback form-group-feedback-right">
                                                    <div class="input-group">
                                                        <input type="hidden" id="idDetail" class="form-control" readonly>
                                                        <input type="hidden" id="idItemEdit" class="form-control" readonly>
                                                        <input type="hidden" id="idSatuanEdit" class="form-control" readonly>
                                                        <input type="hidden" id="idIndexEdit" class="form-control" readonly>
                                                        <input type="text" id="BarangEdit" class="form-control" readonly>
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

                                            <div class="form-group row mb-0">
                                                <div class="form-group col-xl-12 col-sm-6 col-xs-12">
                                                    <label>Lokasi :</label>
                                                    <div class="input-group input-group-solid">
                                                        <select class="form-control select2 detailItemEdit" id="indexEdit" name="indexEdit" style="width:100%;">
                                                            <option label="Label"></option>
                                                            @foreach($listIndex as $index)
                                                            <option value="{{$index['id']}}">{{strtoupper($index['nama_index'])}}</option>
                                                            @endforeach
                                                        </select>
                                                        <span class="form-text text-success" id="txtStokEdit"></span>
                                                        <input type="hidden" id="txtStokEditHidden" class="form-control">
                                                        <span class="form-text text-danger errItemEdit" style="display:none;">*Harap pilih gudang terlebih dahulu!</span>
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

                <!-- Modal form history barang -->
				<div id="modal_history_product" class="modal fade">
				    <div class="modal-dialog modal-xl">
					    <div class="modal-content">
						    <div class="modal-header bg-primary">

							    <h5 class="modal-title text-white">Riwayat Barang</h5>
						    </div>
						    <div class="modal-body">
                                <div class="mb-7">
                                    <div class="row align-items-center">
                                        <div class="col-lg-6">
                                            <div class="row align-items-center">
                                                <div class="col-md-4 my-2 my-md-0">
                                                    <div class="input-icon">
                                                        <input type="text" class="form-control" placeholder="Search..." id="list_riwayat_search_query"/>
                                                        <span>
                                                            <i class="flaticon2-search-1 text-muted"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Search Form-->
                                <!--end: Search Form-->
                                <!--begin: Datatable-->

                                <div class="datatable datatable-bordered datatable-head-custom" id="list_riwayat"></div>

						    </div>

						    <div class="modal-footer">
							    <button type="button" class="btn btn-link" data-dismiss="modal">Tutup</button>
						    </div>
					    </div>
				    </div>
			    </div>
                <!-- /form history barang -->

			</div>
			<!-- /content area -->
@endsection
@section('scripts')
    <script type="text/javascript">

        $(document).ready(function () {

            var imgItem = new KTImageInput('file_po');

            $('#customer').select2({
                allowClear: true,
                placeholder: "Silahkan Pilih Customer Disini"
            });

            $('#product').select2({
                allowClear: true,
                placeholder: "Silahkan Pilih Item Untuk Dijual"
            });

            $('#productUnit').select2({
                allowClear: true,
                placeholder: "Pilih satuan..."
            });

            $('#index').select2({
                allowClear: true,
                placeholder: "Pilih Lokasi..."
            });

            $('#indexEdit').select2({
                allowClear: true,
                placeholder: "Pilih Lokasi..."
            });

            $('#tanggal_retur_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                autoclose : true,
                "setDate": new Date(),
                format : "dd MM yyyy",
            });

            $("#qtyItemMask").autoNumeric('init');
            $("#qty_itemEditMask").autoNumeric('init');

            $("#tanggal_retur_picker").datepicker('setDate', new Date());
        });

        $("#qtyItemMask").on('change', function() {
            $("#qtyItem").val($("#qtyItemMask").autoNumeric("get"));
        });

        $("#qty_itemEditMask").on('change', function() {
            $("#qty_itemEdit").val($("#qty_itemEditMask").autoNumeric("get"));
        });

        $(".pickerTgl").on('change', function() {
            var soDate = new Date($("#tanggal_retur_picker").datepicker('getDate'));

            $(this).closest(".divTgl").find(".tglValue").val($(this).data('datepicker').getFormattedDate('yyyy-mm-dd'));
        });

        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        $("#cancel").on('click', function(e) {
            Swal.fire({
                title: "Batal?",
                text: "Apakah anda ingin membatalkan pembuatan retur penjualan?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    window.location.href = "{{ url('/SalesReturnItem') }}";
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
	    });

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
                            "Harap Tambahkan Minimum 1 Item Retur!.",
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

        $("#customer").on("change", function() {
            //getListProduct
            getCustomerProduct($(this).val());

            //Hapus Daftar penjualan
            if ($("#mode").val() == "edit") {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/SalesReturnItem/ResetDetail",
                    method: 'POST',
                    data: {
                        idReturn: '{{$dataRetur->id}}',
                    },
                    success: function(result){
                        var datatable = $('#list_item').KTDatatable();
                            datatable.setDataSourceParam('idReturn', '{{$dataRetur->id}}');
                            datatable.setDataSourceParam('mode', 'edit');
                            datatable.reload();
                            footerDataForm('{{$dataRetur->id}}');
                    }
                });
            }
        });

        $("#product").on("change", function() {
            //getdataItem
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/SalesReturnItem/GetSatuan",
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

        $("#productUnit").on("change", function() {
            //getDefaultAddress
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/SalesReturnItem/GetDataItem",
                method: 'POST',
                data: {
                    id_product: $("#product option:selected").val(),
                    id_satuan: $(this).val(),
                    id_customer: $("#customer option:selected").val()
                },
                success: function(result){
                    if (result != "") {
	                  	var stokItem = result;
	                    $("#txtStok").html("Jumlah Total Barang yang Dijual ke Customer : "+stokItem);
                        $("#txtStokHidden").val(stokItem);
                        $("#txtRiwayat").html('<a href="#" class="font-size-sm font-weight-bold text-danger text-right text-hover-muted" id="btnProductHistory" data-toggle="modal" data-target="#modal_history_product">[Lihat Riwayat Barang]</a>');
                    }
                    else {
                        $("#txtStok").html("");
                        $("#txtStokHidden").val(0);
                        $("#txtRiwayat").html("");
                    }
                }
            });
        });

        function getCustomerProduct(idCustomer) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/SalesReturnItem/GetProductByCustomer",
                method: 'POST',
                data: {
                    id_customer: idCustomer,
                },
                success: function(result){
                    $('#product').find('option:not(:first)').remove();
                    if (result.length > 0) {
                        for (var i = 0; i < result.length;i++) {
                            $("#product").append($('<option>', {
                                value:result[i].id,
                                text:result[i].nama_item
                            }));
                        }
                    }
                }
            });
        }

        $(document).ready(function() {

            var datatable = $('#list_riwayat').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/SalesReturnItem/GetProductHistory',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                        }
                    },
                    pageSize: 20,
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
                    input: $('#list_riwayat_search_query')
                },

                rows: {
                    autoHide: false
                },

                columns: [
                    {
                        field: 'id',
                        title: '#',
                        sortable: false,
                        width: 0,
                        type: 'number',
                        selector: false,
                        textAlign: 'center',
                        visible:false,
                    },
                    {
                        field: 'tanggal_sj',
                        title: 'Tanggal Pengiriman',
                        width: 100,
                        textAlign: 'center',
                        autoHide:false,
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
                        field: 'kode_pengiriman',
                        title: 'No. Surat Jalan',
                        width: 150,
                        textAlign: 'left',
                        autoHide:false,
                        template: function(row) {
                            var txtTgl = "";
                            if (row.kode_pengiriman != null) {
                                txtTgl += row.kode_pengiriman.toUpperCase();
                            }
                            if (row.no_so != null) {
                                txtTgl += "<br>";
                                txtTgl += "<span class='label label-md label-outline-primary label-inline mt-1'>SO : " + row.no_so.toUpperCase() + "</span>";
                            }
                            if (row.kode_invoice != null) {
                                txtTgl += "<br>";
                                txtTgl += "<span class='label label-md label-outline-primary label-inline mt-1'>INV : " + row.kode_invoice.toUpperCase() + "</span>";
                            }
                            return txtTgl;
                        },
                    },
                    {
                        field: 'nama_customer',
                        title: 'Nama Pelanggan',
                        width: 200,
                        textAlign: 'left',
                        autoHide:false,
                        template: function(row) {
                            var txt = "";
                            txt += '<span class="font-weight-bold">'+ucwords(row.nama_customer)+'</span>';
                            txt += "<br />";
                            txt += '<span class="label label-md label-outline-primary label-inline mr-2 mb-1 mt-1">' + row.nama_outlet + '</span>';
                            txt += "<br />";

                            if (row.no_po_customer != null) {
                                txt += '<span class="font-weight-bold text-inline text-primary font-size-xs">No. PO : '+row.no_po_customer.toUpperCase()+'</span>';
                            }
                            else {
                                txt += '<span class="font-weight-bold text-inline text-primary font-size-xs">No. PO : - </span>';
                            }
                            return txt;
                        },
                    },
                    {
                        field: 'qty_item',
                        title: 'Qty',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide:false,
                        template: function(row) {
                            if (row.qty_item != null) {
                                return parseFloat(row.qty_item).toLocaleString('id-ID', { maximumFractionDigits: 2});
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'nama_satuan',
                        title: 'Satuan',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide:false,
                        template: function(row) {
                            return ucwords(row.nama_satuan);
                        },
                    },
                ],
            });
        });

        $('#modal_history_product').on('shown.bs.modal', function (e) {
            var datatable = $('#list_riwayat').KTDatatable();
                datatable.setDataSourceParam('id_customer', $("#customer option:selected").val());
                datatable.setDataSourceParam('id_product', $("#product option:selected").val());
                datatable.setDataSourceParam('id_satuan', $("#productUnit option:selected").val());
                datatable.reload();
        })

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
                            url: '/SalesReturnItem/GetDetail',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data : {
                                idReturn : '{{$dataRetur->id}}',
                                mode : 'edit'
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
                    {
                        field: 'Actions',
                        title: 'Aksi',
                        sortable: false,
                        width: 110,
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

			$(".detailItem").each(function(){
				if($(this).val() == "" || $(this).children("option:selected").val() == ""){
				   	$(this).closest('.form-group, input-group').find('.errItem').show();
				  	errCount = errCount + 1;
				}
				else {
					$(this).closest('.form-group, input-group').find('.errItem').hide();
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
				}
            });

            if(parseFloat($("#qtyItem").val()) > parseFloat($("#txtStokHidden").val())){
                $("#qtyItem").closest('.form-group, input-group').find('.errItemQty').show();
                errCount = errCount + 1;
            }
            else {
                $("#qtyItem").closest('.form-group, input-group').find('.errItemQty').hide();
            }

			if (errCount == 0) {
                Swal.fire({
                    title: "Tambah Item?",
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
                            url: "/SalesReturnItem/StoreDetail",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idItem : $("#product option:selected").val(),
                                idSatuan : $("#productUnit option:selected").val(),
                                idIndex : $("#index option:selected").val(),
                                idReturn : "{{$dataRetur->id}}",
                                qtyItem : $("#qtyItem").val(),
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Item Berhasil ditambahkan!.",
                                        "success"
                                    )
                                    $("#product").val("").trigger('change');
                                    $("#index").val("").trigger('change');
                                    $("#qtyItem").val("");
                                    $("#qtyItemMask").val("");
                                    $("#txtStok").html("");
                                    $("#txtRiwayat").html("");
                                    var datatable = $('#list_item').KTDatatable();
                                        datatable.setDataSourceParam('idReturn', '{{$dataRetur->id}}');
                                        datatable.setDataSourceParam('mode', 'edit');
                                        datatable.reload();
                                    footerDataForm('{{$dataRetur->id}}');
                                }
                                else if (result == "failDuplicate") {
                                    Swal.fire(
                                        "Gagal!",
                                        "Item ini sudah tersedia pada List Retur !",
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
                url: "/SalesReturnItem/EditDetail",
                method: 'POST',
                data: {
                    idDetail: id,
                    idCust: $("#customer option:selected").val(),
                    mode: "edit"
                },
                success: function(result){
                    if (result != null) {
                        $("#idItemEdit").val(result.value2);
                        $("#idIndexEdit").val(result.value4);
                        $("#idSatuanEdit").val(result.value3);
                        $("#idDetail").val(result.id);

                        $("#qty_itemEditMask").val(parseFloat(result.value5).toLocaleString('id-ID', { maximumFractionDigits: 2})).trigger('change');

                        var kodeItem = "";
                        if (result.value_spesifikasi != null) {
                            kodeItem = '('+result.value_spesifikasi+')'+result.kode_item.toUpperCase();
                        }
                        else {
                            kodeItem = result.kode_item.toUpperCase();
                        }

                        $("#BarangEdit").val(kodeItem + ' - ' + result.nama_item);
                        $("#indexEdit").val(result.value4).trigger('change');
                        $("#txtStokEdit").html("Jumlah Total Barang yang Dijual ke Customer : " + result.limit_retur);
                        $("#txtStokEditHidden").val(result.limit_retur);
                        $("#ModeGetStok").val("edit");
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
                        url: "/SalesReturnItem/DeleteDetail",
                        method: 'POST',
                        data: {
                            idDetail: id,
                            mode:"edit"
                        },
                        success: function(result){
                            Swal.fire(
                                "Sukses!",
                                "Data Berhasil dihapus!.",
                                "success"
                            )
                            var datatable = $('#list_item').KTDatatable();
                                datatable.setDataSourceParam('idReturn', '{{$dataRetur->id}}');
                                datatable.setDataSourceParam('mode', 'edit');
                                datatable.reload();
                                footerDataForm('{{$dataRetur->id}}');
                        }
                    });
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

            var idRow = $("#idDetail").val();
            var idItem = $("#idItemEdit").val();
            var idSatuan = $("#idSatuanEdit").val();
            var idIndex = $("#indexEdit").val();
		    var qty = $("#qty_itemEdit").val();
            var limit = $("#txtStokEditHidden").val();

            $(".inputEdit").each(function(){
                if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                    errCount = parseInt(errCount) + 1;
                }
            });

            if (parseFloat(qty) > parseFloat(limit)) {
                Swal.fire(
                    "Gagal!",
                    "Jumlah Item Tidak dapat melebihi total retur kepada pelanggan!",
                    "warning"
                );
                return false;
            }

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
                            url: "/SalesReturnItem/UpdateDetail",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idItem : idItem,
                                idSatuan : idSatuan,
                                idIndex : idIndex,
                                idReturn : "{{$dataRetur->id}}",
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
                                    var datatable = $('#list_item').KTDatatable();
                                        datatable.setDataSourceParam('idReturn', '{{$dataRetur->id}}');
                                        datatable.setDataSourceParam('mode', 'edit');
                                        datatable.reload();
                                        footerDataForm('{{$dataRetur->id}}');
                                        $("#closeModal").trigger('click');
                                }
                                else if (result == "failDuplicate") {
                                    Swal.fire(
                                        "Gagal!",
                                        "Item ini sudah tersedia pada List Retur Barang !",
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
                Swal.fire(
                    "Gagal!",
                    "Terdapat kolom kosong, harap mengisi kolom kosong terlebih dahulu !",
                    "warning"
                )
            }
	    });

        function footerDataForm(idReturn) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/SalesReturnItem/GetDataFooter",
                method: 'POST',
                data: {
                    idReturn: idReturn,
                    mode: 'edit'
                },
                success: function(result){
                    if (result != "null") {
                        var qtyItem = result.qtyItem;
                        var qtyFixed = qtyItem;

                        $("#qtyTtl").val(qtyFixed);
                        $("#qtyTtlMask").val(parseFloat(qtyFixed).toLocaleString('id-ID', { maximumFractionDigits: 2}))

                    }
                    else {
                        $("#qtyTtl").val(0);
                        $("#qtyTtlMask").val(0);
                    }
                }
            });
        }

        $(document).ready(function () {
            $("#customer").val("{{$dataRetur->id_customer}}").trigger('change');

            footerDataForm('{{$dataRetur->id}}');
            $("#tanggal_retur_picker").datepicker("setDate", new Date("{{$dataRetur->tanggal_retur}}"));
            $("#mode").val("edit");
        });

    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
