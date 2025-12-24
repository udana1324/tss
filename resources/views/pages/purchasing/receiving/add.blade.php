@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h5 class="card-title text-white">Buat Penerimaan</h5>
					</div>
                    <form action="{{ route('Receiving.store') }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            <div class="row">
								<div class="col-md-6">
									<fieldset>
										<legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi Penjual/ Supplier </h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
										<div class="form-group row d-none">
                                            <label class="col-lg-3 col-form-label">Kode Penerimaan :</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control bg-slate-600 border-slate-600 border-1" placeholder="Auto Generated" name="kode_receiving" id="kode_receiving" readonly>
                                            </div>
                                        </div>

										<div class="form-group">
                                            <label>Supplier :</label>
                                            <div>
                                                <select class="form-control select2 req" id="supplier" name="supplier">
                                                    <option label="Label"></option>
                                                    @foreach($dataSupplier as $supplier)
                                                    <option value="{{$supplier->id}}">{{strtoupper($supplier->nama_supplier)}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <span class="form-text text-danger err" style="display:none;">*Harap pilih supplier terlebih dahulu!</span>
                                        </div>

                                        <div class="form-group">
                                            <label>No. Purchase Order :</label>
                                            <div>
                                                <select class="form-control select2 req" id="purchaseOrder" name="purchaseOrder">
                                                    <option label="Label"></option>
                                                </select>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih purchase order terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>No. Surat Jalan Supplier :</label>
                                            <div>
                                                <input type="text" class="form-control" placeholder="Masukkan No. Surat Jalan Supplier" name="no_sj_supplier" id="no_sj_supplier">
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih purchase order terlebih dahulu!</span>
                                            </div>
                                        </div>

										<div class="form-group">
                                            <label>Alamat Kirim :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="hidden" class=" form-control req" name="id_alamat" id="id_alamat">
                                                    <textarea class="form-control" name="alamat" id="alamat" style="resize:none;" placeholder="Silahkan Klik Tombol Pilih Alamat" readonly></textarea>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-primary" id="btnAlamat" data-toggle="modal" data-target="#modal_list_alamat">Pilih Alamat</button>
                                                    </div>
                                                </div>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih alamat supplier terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Tanggal Purchase Order :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <input type="hidden" class="form-control" name="tanggal_po" id="tanggal_po">
                                                <input type="text" class="form-control" name="tanggal_po_picker" id="tanggal_po_picker" readonly>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal surat jalan terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Tanggal Surat Jalan :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <input type="hidden" class="form-control req" name="tanggal_sj" id="tanggal_sj">
                                                <input type="text" class="form-control" placeholder="Pilih Tanggal" name="tanggal_sj_picker" id="tanggal_sj_picker" readonly>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal surat jalan terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Tanggal Terima Surat Jalan :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <input type="hidden" class="form-control req" name="tanggal_terima" id="tanggal_terima">
                                                <input type="text" class="form-control" placeholder="Pilih Tanggal" name="tanggal_terima_picker" id="tanggal_terima_picker" readonly>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal surat jalan terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Penggunaan Syarat Dan Ketentuan :</label>
                                            <div>
                                                <div class="radio-inline">
                                                    <label class="radio">
                                                        <input type="radio" id="termsPo" value="termsPo" name="terms_usage" checked />
                                                        <span></span>Purchase Order
                                                    </label>
                                                    <label class="radio">
                                                        <input type="radio" id="buatTerms" value="buatTerms" name="terms_usage" />
                                                        <span></span>Buat Baru
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group" id="divTnc" style="display: none;">
                                            <label>Syarat & Ketentuan :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <textarea class="form-control elastic" id="tnc" name="tnc" rows="3" placeholder="Ketik Syarat & Ketentuan Penerimaan Disini atau gunakan Template pada tombol Template"></textarea>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-primary" id="btnTemplate" data-toggle="modal" data-target="#modal_list_terms">Template</button>
                                                    </div>
                                                </div>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih alamat supplier terlebih dahulu!</span>
                                            </div>
                                        </div>

									</fieldset>
								</div>

								<div class="col-md-6">
									<fieldset>
					                	<legend class="text-muted"><h6><i class="fab la-buffer"></i> Rincian Penerimaan Barang</h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
										<div class="form-group">
                                            <label>Nama Barang :</label>
                                            <div class="input-group">
                                                <div class="col-8">
                                                    <select class="form-control select2 detailItem" id="product" name="product">
                                                        <option label="Label"></option>
                                                    </select>
                                                    <span class="form-text text-danger errItem" style="display:none;">*Harap pilih item terlebih dahulu!</span>
                                                </div>
                                                <div class="col-4">
                                                    <select class="form-control select2 detailUnit" id="productUnit" name="productUnit">
                                                        <option label="Label"></option>
                                                    </select>
                                                    <span class="form-text text-danger errUnit" style="display:none;">*Harap pilih satuan item terlebih dahulu!</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-6">
                                                <label>Jumlah Outstanding :</label>
                                                <div class="input-group">
                                                    <div class="col-12">
                                                        <input type="text" id="qtyOuts" class="form-control text-right" readonly>
                                                    </div>
                                                    {{-- <div class="col-4">
                                                        <input type="text" id="satuan_item" class="form-control" readonly>
                                                    </div> --}}
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <label>Jumlah Penerimaan Barang :</label>
                                                <div class="input-group">
                                                    <div class="col-12">
                                                        <input type="text" id="qtyItemMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right">
                                                        <input type="hidden" id="qtyItem" class="form-control text-right detailItem">
                                                        <span class="form-text text-danger errItem" style="display:none;">*Harap masukkan Jumlah penerimaan terlebih dahulu!</span>
                                                    </div>
                                                    <div class="col-4">
                                                        {{-- <input type="text" id="satuan_item2" class="form-control" readonly> --}}

                                                    </div>
                                                </div>
                                            </div>
										</div>

										<div class="form-group">

										</div>

                                        <div class="form-group row">
											<label class="col-lg-3 col-form-label"></label>
											<div class="col-lg-9">
												<button type="button" class="btn btn-primary font-weight-bold" id="btnAddItem">Tambah Item</button>
											</div>
										</div>

									</fieldset>
								</div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset>
                                        <legend class="text-muted"><h6><i class="la la-list"></i> List Penerimaan Barang</h6></legend>
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
										<label class="col-lg-3 col-form-label">Total Penerimaan</label>
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

                <!-- Modal form list alamat -->
				<div id="modal_list_alamat" class="modal fade">
				    <div class="modal-dialog modal-lg">
					    <div class="modal-content">
						    <div class="modal-header bg-primary">

							    <h5 class="modal-title">List Alamat Supplier</h5>
						    </div>
						    <div class="modal-body">
							    <form >
								    <table class="datatable-bordered datatable-head-custom ml-4" id="list_alamat" width="100%">
									    <thead>
										    <tr>
											    <th align="center" style="text-align:center;display:none;">ID</th>
												<th align="center" style="text-align:center;">Alamat</th>
												{{-- <th align="center" style="text-align:center;">Jenis Alamat</th>
												<th align="center" style="text-align:center;">PIC</th>
												<th align="center" style="text-align:center;">No. Telp PIC</th> --}}
												<th align="center" style="text-align:center;">Aksi</th>
										    </tr>
									    </thead>
									    <tbody>

									    </tbody>
								    </table>
							    </form>

						    </div>

						    <div class="modal-footer">
							    <button type="button" class="btn btn-link" data-dismiss="modal">Tutup</button>
						    </div>
					    </div>
				    </div>
			    </div>
                <!-- /form list alamat -->

                <!-- Modal form list terms -->
				<div id="modal_list_terms" class="modal fade">
				    <div class="modal-dialog modal-lg">
					    <div class="modal-content">
						    <div class="modal-header bg-primary">

							    <h5 class="modal-title">List Template Terms</h5>
						    </div>
						    <div class="modal-body">
							    <form >
								    <table class="datatable-bordered datatable-head-custom ml-4" id="list_terms" width="100%">
									    <thead>
										    <tr>
											    <th align="center" style="text-align:center;display:none;">ID</th>
												<th align="center" style="text-align:center;">Nama Terms</th>
												<th align="center" style="text-align:center;">Aksi</th>
										    </tr>
									    </thead>
									    <tbody>

									    </tbody>
								    </table>
							    </form>

						    </div>

						    <div class="modal-footer">
							    <button type="button" class="btn btn-link" data-dismiss="modal">Tutup</button>
						    </div>
					    </div>
				    </div>
			    </div>
                <!-- /form list terms -->

                <!-- Horizontal form edit item-->
				<div id="modal_form_edit_item" class="modal fade">
				    <div class="modal-dialog modal-lg">
					    <div class="modal-content">
						    <div class="modal-header bg-primary">

							    <h5 class="modal-title">Ubah Jumlah Penerimaan Item</h5>
						    </div>
						    <div class="modal-body">
							    <form >
								    <table class="table display" id="list_edit_item" width="100%">
									    <thead>
										    <tr>
											    <th align="center" style="text-align:center;display: none;">Id</th>
											    <th align="center" style="text-align:center;">Item</th>
                                                <th align="center" style="text-align:center;">Satuan</th>
                                                <th align="center" style="text-align:center;">Outstanding</th>
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
							    <button type="button" class="btn btn-link closeEdit" data-dismiss="modal">Tutup</button>
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
            $('#supplier').select2({
                allowClear: true,
                placeholder: "Pilih Supplier"
            });

            $('#purchaseOrder').select2({
                allowClear: true,
                placeholder: "Pilih Purchase Order"
            });

            $('#product').select2({
                allowClear: true,
                placeholder: "Pilih Item"
            });

            $('#productUnit').select2({
                allowClear: true,
                placeholder: "Pilih satuan..."
            });

            $('#tanggal_sj_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                autoclose : true,
                format : "dd MM yyyy",
            });

            $('#tanggal_terima_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                autoclose : true,
                format : "dd MM yyyy",
            });

            $("#qtyItemMask").autoNumeric('init');
        });

        $("#qtyItemMask").on('change', function() {
            $("#qtyItem").val($("#qtyItemMask").autoNumeric("get"));
        });

        $("#tanggal_sj_picker").on('change', function() {
            $("#tanggal_sj").val($("#tanggal_sj_picker").data('datepicker').getFormattedDate('yyyy-mm-dd'));
        });

        $("#tanggal_terima_picker").on('change', function() {
            $("#tanggal_terima").val($("#tanggal_terima_picker").data('datepicker').getFormattedDate('yyyy-mm-dd'));
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
                text: "Apakah anda ingin membatalkan pembuatan penerimaan barang?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    window.location.href = "{{ url('/Receiving') }}";
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
	    });

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

                    if(datatable.getTotalRows() < 1) {
                        Swal.fire(
                            "Gagal!",
                            "Harap Tambahkan Minimum 1 Item Penerimaan!.",
                            "warning"
                        )
                        count = parseInt(count) + 1;

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

        $("#supplier").on("change", function() {
            //getListProduct
            getSupplierPurchaseOrder($(this).val());

            //getSupplierAddress
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/PurchaseOrder/GetSupplierAddress",
                method: 'POST',
                data: {
                    id_supplier: $(this).val(),
                },
                success: function(result){
                    if (result.length > 0) {
                        $('#list_alamat tbody').empty();
                        if (result.length > 0) {
                            for (var i = 0; i < result.length;i++) {
                                var idAlamat = result[i].id;
                                var alamat = result[i].alamat_pt;
                                //var jenisAlamat = result[i].jenis_alamat;
                                //var pic = result[i].pic_alamat;
                                //var tlpPic = result[i].telp_pic;
                                var data="<tr>";
                                    data +="<td style='text-align:center;display:none;'>"+idAlamat+"</td>";
                                    data +="<td style='text-align:left;word-wrap:break-word;min-width:160px;max-width:160px;'>"+ucwords(alamat)+"</td>";
                                    // data +="<td style='text-align:center;'>"+jenisAlamat+"</td>";
                                    // data +="<td style='text-align:center;'>"+ucwords(pic)+"</td>";
                                    // data +="<td style='text-align:center;'>"+tlpPic+"</td>";
                                    data +="<td style='text-align:center;'><button type='button' data-dismiss='modal' class='btn btn-primary btn-icon select'>Pilih</button></td>";
                                    data +="</tr>";
                                    $("#list_alamat").append(data);
                            }
                        }
                    }
                }
            });

            //Hapus Daftar pembelian
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/Receiving/ResetDetail",
                method: 'POST',
                data: {
                    idRcv: 'DRAFT',
                },
                success: function(result){
                    var datatable = $('#list_item').KTDatatable();
                        datatable.setDataSourceParam('idPurchaseOrder', '');
                        datatable.setDataSourceParam('idReceiving', '');
                        datatable.reload();
                        footerDataForm('DRAFT');
                }
            });
        });

        $('#purchaseOrder').on('change', function() {
            var idPurchaseOrder = $(this).val();
            //getDefaultAddress
            if ($(this).val() != "") {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/Receiving/GetTanggalPo",
                    method: 'POST',
                    data: {
                        idPurchaseOrder: $(this).val(),
                    },
                    success: function(result){
                        if (result != null) {
                            $("#tanggal_po").val((result.tanggal_po));
                            $("#tanggal_po_picker").val(formatDate(result.tanggal_po));
                            $("#tanggal_sj_picker").trigger("change");
                        }
                    }
                });
            }
            else {
                $("#tanggal_po").val("");
                $("#tanggal_po_picker").val("");
            }

            if ($(this).val() != "") {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/Receiving/GetDefaultAddress",
                    method: 'POST',
                    data: {
                        idPurchaseOrder: $(this).val(),
                    },
                    success: function(result){
                        if (result.length > 0) {
                            $("#id_alamat").val(result[0].id);
                            $("#alamat").val(ucwords(result[0].alamat_pt));
                        }
                    }
                });
            }
            else {
                $("#id_alamat").val("");
                $("#alamat").val("");
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/Receiving/SetDetail",
                method: 'POST',
                data: {
                    idReceiving: '',
                    idPurchaseOrder: $(this).val(),
                },
                success: function(result){
                    if (result != "") {
                        var datatable = $('#list_item').KTDatatable();
                            datatable.setDataSourceParam('idPurchaseOrder', idPurchaseOrder);
                            datatable.setDataSourceParam('idReceiving', '');
                            datatable.reload();
                            footerDataForm('DRAFT');
                    }
                }
            });
            getProduct(idPurchaseOrder);
        });

        $("#product").on("change", function() {
            //getdataItem
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/Receiving/GetSatuan",
                method: 'POST',
                data: {
                    idProduct: $(this).val(),
                    idPo: $("#purchaseOrder option:selected").val(),
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
            //getdataItem
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/Receiving/GetDataItem",
                method: 'POST',
                data: {
                    id_product: $("#product option:selected").val(),
                    idPurchaseOrder: $("#purchaseOrder option:selected").val(),
                    id_satuan: $(this).val(),
                },
                success: function(result){
                    if (result.length > 0) {
                        var outsQty = result[0].outstanding_qty;
                        $("#qtyOuts").val(outsQty);
                        $("#qtyOuts").autoNumeric('init');
                    }
                    else {
                        $("#qtyOuts").val("");
                        $("#qtyOuts").autoNumeric('destroy');
                    }
                }
            });

        });

        function getProduct(idPo) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/Receiving/GetProductByPurchaseOrder",
                method: 'POST',
                data: {
                    idPurchaseOrder: idPo,
                },
                success: function(result){
                    $('#product').find('option:not(:first)').remove();
                    if (result.length > 0) {
                        for (var i = 0; i < result.length;i++) {
                            var kodeItem = "";
                            if (result[i].value_spesifikasi != null) {
                                kodeItem = '('+result[i].value_spesifikasi+')'+result[i].kode_item.toUpperCase();
                            }
                            else {
                                kodeItem = result[i].kode_item.toUpperCase();
                            }
                            $("#product").append($('<option>', {
                                value:result[i].id,
                                text:kodeItem+' - '+result[i].nama_item
                            }));
                        }
                    }
                }
            });
        }

        function getSupplierPurchaseOrder(idSupplier) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/Receiving/GetPurchaseOrder",
                method: 'POST',
                data: {
                    id_supplier: idSupplier,
                },
                success: function(result){
                    $('#purchaseOrder').find('option:not(:first)').remove();
                    if (result.length > 0) {
                        for (var i = 0; i < result.length;i++) {
                            $("#purchaseOrder").append($('<option>', {
                                value:result[i].id,
                                text:result[i].no_po.toUpperCase()
                            }));
                        }
                    }
                    $('#purchaseOrder').trigger('change');
                }
            });
        }

        $("#tanggal_sj_picker").on('change', function() {
            var sjDate = new Date($(this).datepicker('getDate'));
            var poDate = new Date($("#tanggal_po").val());
            var selisih = Math.floor((Date.UTC(sjDate.getFullYear(), sjDate.getMonth(), sjDate.getDate()) - Date.UTC(poDate.getFullYear(), poDate.getMonth(), poDate.getDate()) ) /(1000 * 60 * 60 * 24));
            if ($("#tanggal_po").val() != "" && $("#tanggal_sj").val() != "") {
                if (selisih < 0) {
                    Swal.fire(
                        "Error!",
                        "Tanggal SJ tidak boleh dibawah dari tanggal Purchase Order!.",
                        "warning"
                    )
                    $("#tanggal_sj").val("");
                    $("#tanggal_sj_picker").val("");
                }
            }
        });

        $("#list_alamat").on('click', '.select', function() {
			var id = $(this).parents('tr:first').find('td:first').text();
			var alamat = $(this).parents('tr:first').find('td:eq(1)').text();
			$("#id_alamat").val(id);
			$("#alamat").val(ucwords(alamat));
        });

        $(document).ready(function() {

            var datatable = $('#list_item').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Receiving/GetDetail',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
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
                        field: 'kode_item',
                        title: 'Deskripsi Barang',
                        autoHide: false,
                        textAlign: 'left',
                        width: 'auto',
                        template: function(row) {
                            if(row.value_spesifikasi != null) {
                                return '('+row.value_spesifikasi+')'+row.kode_item.toUpperCase() + ' - ' + row.nama_item.toUpperCase();
                            }
                            else {
                                return row.kode_item.toUpperCase() + ' - ' + row.nama_item.toUpperCase();
                            }
                        },
                    },
                    {
                        field: 'qty_order',
                        title: 'Jumlah Order',
                        textAlign: 'right',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.qty_order).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'outstanding_qty',
                        title: 'Outstanding',
                        textAlign: 'right',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.outstanding_qty).toLocaleString('id-ID', { maximumFractionDigits: 2});
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
                        title: 'Penerimaan',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.qty_item).toLocaleString('id-ID', { maximumFractionDigits: 2});
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

            var qty = parseFloat($("#qtyItem").val());
	     	var outsQty = parseFloat($("#qtyOuts").val());

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

            if (qty > outsQty) {

                Swal.fire(
                    "Gagal!",
                    "Jumlah Item Melebihi Outstanding Order !",
                    "warning"
                );
                return false;
            }

            if (parseFloat(qty) <= 0) {
                Swal.fire(
                    "Gagal!",
                    "Jumlah Item Tidak dapat dibawah atau kurang dari 0 !",
                    "warning"
                );
                return false;
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
                            url: "/Receiving/StoreDetail",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idItem : $("#product option:selected").val(),
                                idSatuan : $("#productUnit option:selected").val(),
                                idReceiving : "",
                                qtyItem : $("#qtyItem").val(),
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Item Berhasil ditambahkan!.",
                                        "success"
                                    )
                                    $("#product").val("").trigger('change'),
                                    $("#qtyItem").val("");
                                    $("#satuan_item").val("");
                                    $("#satuan_item2").val("");
                                    var datatable = $('#list_item').KTDatatable();
                                        datatable.setDataSourceParam('idPurchaseOrder', $("#purchaseOrder option:selected").val());
                                        datatable.setDataSourceParam('idReceiving','');
                                        datatable.reload();
                                    footerDataForm('DRAFT');
                                }
                                else if (result == "failDuplicate") {
                                    Swal.fire(
                                        "Gagal!",
                                        "Item ini sudah tersedia pada List Penerimaan Barang !",
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
                url: "/Receiving/EditDetail",
                method: 'POST',
                data: {
                    idDetail: id,
                    idPurchaseOrder : $("#purchaseOrder option:selected").val(),
                },
                success: function(result){
                    if (result.length > 0) {
                        var qty = parseFloat(result[0].qty_item);
                        var qtyFixed = qty.toString().replace(".", ",");
                        var qtyOutstanding = parseFloat(result[0].outstanding_qty);
                        var qtyOutstandingFixed = qtyOutstanding.toString().replace(".", ",");
                        var kodeItem = "";
                        if (result[0].value_spesifikasi != null) {
                            kodeItem = '('+result[0].value_spesifikasi+')'+result[0].kode_item.toUpperCase();
                        }
                        else {
                            kodeItem = result[0].kode_item.toUpperCase();
                        }
                        var data = "<tr>";
                            data += "<td style='display:none;'><input type='text' class='form-control' id='idRowEdit' value='"+result[0].id+"' /></td>";
                            data += "<td style='display:none;'><input type='text' class='form-control' id='idItemEdit' value='"+result[0].id_item+"' /></td>";
                            data += "<td style='display:none;'><input type='text' class='form-control' id='idSatuanEdit' value='"+result[0].id_satuan+"' /></td>";
                            data += "<td style='text-align:center;'>"+ kodeItem + ' - ' + result[0].nama_item.toUpperCase()+"</td>";
                            data += "<td style='text-align:center;'>"+result[0].nama_satuan.toUpperCase()+"</td>";
                            data += "<td style='text-align:center;'><input type='text' class='form-control' id='outsItemEdit' value='"+qtyOutstandingFixed.toLocaleString('id-ID', { maximumFractionDigits: 2})+"' readonly /></td>";
                            data += "<td style='text-align:center;'><input type='text' class='form-control' id='qtyRowEditMask' autocomplete='off' data-a-dec=',' data-a-sep='.' value='"+qtyFixed+"' /></td>";
                            data += "<td style='text-align:center;'><input type='hidden' class='form-control inputEdit' id='qtyRowEdit' value='"+qtyFixed+"' /></td>";
                            data += "</tr>";
                            $('#detil_edit_item').append(data);

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
                        url: "/Receiving/DeleteDetail",
                        method: 'POST',
                        data: {
                            idDetail: id
                        },
                        success: function(result){
                            Swal.fire(
                                "Sukses!",
                                "Data Berhasil dihapus!.",
                                "success"
                            );
                            var datatable = $('#list_item').KTDatatable();
                                datatable.setDataSourceParam('idPurchaseOrder', $("#purchaseOrder option:selected").val());
                                datatable.setDataSourceParam('idReceiving','');
                                datatable.reload();
                                footerDataForm('DRAFT');
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

            var idRow = $("#idRowEdit").val();
            var idItem = $("#idItemEdit").val();
            var idSatuan = $("#idSatuanEdit").val();
	     	var qty = $("#qtyRowEdit").val();
	     	var outsQty = $("#outsItemEdit").val();

            $(".inputEdit").each(function(){
                if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                    errCount = parseInt(errCount) + 1;
                }
            });

            if (parseFloat(qty) > parseFloat(outsQty)) {

                Swal.fire(
                    "Gagal!",
                    "Jumlah Item Melebihi Outstanding Order !",
                    "warning"
                );
                return false;
            }

            if (parseFloat(qty) <= 0) {
                Swal.fire(
                    "Gagal!",
                    "Jumlah Item Tidak dapat dibawah atau kurang dari 0 !",
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
                            url: "/Receiving/UpdateDetail",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idItem : idItem,
                                idSatuan : idSatuan,
                                idReceiving : "",
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
                                        datatable.setDataSourceParam('idPurchaseOrder', $("#purchaseOrder option:selected").val());
                                        datatable.setDataSourceParam('idReceiving','');
                                        datatable.reload();
                                        footerDataForm('DRAFT');
                                }
                                else if (result == "failDuplicate") {
                                    Swal.fire(
                                        "Gagal!",
                                        "Jumlah Item ini Melebihi Outstanding Order !",
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

        $(document).ready(function() {
            //getTemplateTerms
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/PurchaseOrder/GetListTerms",
                method: 'POST',
                data: {
                    target: "penerimaan",
                },
                success: function(result){
                    if (result.length > 0) {
                        $('#list_terms tbody').empty();
                        if (result.length > 0) {
                            for (var i = 0; i < result.length;i++) {
                                var idTemplate = result[i].id;
                                var nama = result[i].nama_template;
                                var data="<tr>";
                                    data +="<td style='text-align:center;display:none;'>"+idTemplate+"</td>";
                                    data +="<td style='text-align:left;word-wrap:break-word;min-width:160px;max-width:160px;'>"+ucwords(nama)+"</td>";
                                    data +="<td style='text-align:center;'><button type='button' data-dismiss='modal' class='btn btn-primary btn-icon selectTerms'>Pilih</button></td>";
                                    data +="</tr>";
                                    $("#list_terms").append(data);
                            }
                        }
                    }
                }
            });
        });

        $("#list_terms").on('click', '.selectTerms', function() {
			var id = $(this).parents('tr:first').find('td:first').text();
			$.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/Receiving/GetTerms",
                method: 'POST',
                data: {
                    idTemplate: id,
                },
                success: function(result){
                    if (result.length > 0) {
                        var dataTemplate = "";
                        for (var i = 0; i < result.length;i++) {
                            dataTemplate += result[i].terms_and_condition;
                            counter = result.length - 1;
                            if (i != counter) {
                                dataTemplate += "\n";
                            }
                        }
                        $("#tnc").val(dataTemplate);
                    }
                }
            });
        });

        $('input[name=terms_usage]').on('change', function() {
			var val = $(this).val();
			if (val == "buatTerms") {
                $("#tnc").val('');
			    $("#divTnc").show();
			}
			else {
                $("#divTnc").hide();
			}
		});

        function footerDataForm(idRcv) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/Receiving/GetDataFooter",
                method: 'POST',
                data: {
                    idReceiving: idRcv,
                },
                success: function(result){
                    if (result != "null") {
                        var ttlQty = result.qtyItem;
                        var ttlQtyFixed = ttlQty.toString().replace(".", ",");

                        $("#qtyTtl").val(ttlQty);
                        $("#qtyTtlMask").val(parseFloat(ttlQty).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                    }
                    else {
                        $("#qtyTtl").val(0);
                        $("#qtyTtlMask").val(parseFloat(0).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                    }
                }
            });
        }
    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
