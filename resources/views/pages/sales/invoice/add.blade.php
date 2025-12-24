@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h5 class="card-title text-white">Buat Faktur Penjualan</h5>
					</div>
                    <form action="{{ route('SalesInvoice.store') }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            <div class="row">
								<div class="col-md-6">
									<fieldset>
										<legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi Pembeli / Customer </h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
										<div class="form-group row d-none">
                                            <label class="col-lg-3 col-form-label">Kode Invoice :</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control bg-slate-600 border-slate-600 border-1" placeholder="Auto Generated" name="kode_invoice" id="kode_invoice" readonly>
                                            </div>
                                        </div>

										<div class="form-group">
                                            <label>Customer :</label>
                                            <div>
                                                <select class="form-control select2 req" id="customer" name="customer">
                                                    <option label="Label"></option>
                                                    @foreach($dataCustomer as $customer)
                                                    <option value="{{$customer->id}}">{{strtoupper($customer->nama_customer)}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <span class="form-text text-danger err" style="display:none;">*Harap pilih customer terlebih dahulu!</span>
                                        </div>

                                        <div class="form-group">
                                            <label>No. Sales Order :</label>
                                            <div>
                                                <select class="form-control select2 req" id="SalesOrder" name="SalesOrder">
                                                    <option label="Label"></option>
                                                </select>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih sales order terlebih dahulu!</span>
                                            </div>
                                        </div>

										<div class="form-group">
                                            <label>Alamat Customer :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="hidden" class=" form-control req" name="id_alamat" id="id_alamat">
                                                    <textarea class="form-control" name="alamat" id="alamat" style="resize:none;" readonly></textarea>
                                                </div>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih alamat customer terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Tanggal Invoice :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <input type="hidden" class="form-control" name="tanggal_sj_max" id="tanggal_sj_max">
                                                <input type="hidden" class="form-control req" name="tanggal_inv" id="tanggal_inv">
                                                <input type="text" class="form-control" name="tanggal_inv_picker" id="tanggal_inv_picker" readonly>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal invoice terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Rekening Perusahaan :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <select class="form-control select2 req" id="company_account" name="company_account">
                                                    <option label="Label"></option>
                                                    @foreach($dataAccount as $account)
                                                    <option value="{{$account->id}}">{{strtoupper($account->nama_bank).' - '.$account->nomor_rekening.' - '.ucwords($account->atas_nama)}}</option>
                                                    @endforeach
                                                </select>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih nomor rekening perusahaan terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
											<label>Tenor Tagihan :</label>
											<div class="input-group">
        										<div class="col-8 pl-0">
        											<input type="text" id="durasiJT" maxlength="3" name="durasiJT" onkeypress="return validasiAngka(event);" class="form-control ">
        											<span class="form-text text-danger errItem" style="display:none;">*Harap masukkan durasi Tenor invoice terlebih dahulu!</span>
                                                </div>

        										<div class="col-4 pr-0">
        											<input type="text" id="hari" value="Hari" class="form-control" readonly>
        										</div>
											</div>
										</div>

                                        <div class="form-group">
                                            <label>Tanggal Jatuh Tempo :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <input type="hidden" class="form-control" name="tgl_jt" id="tgl_jt">
                                                <input type="text" class="form-control" name="tgl_jt_picker" id="tgl_jt_picker" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Pajak Penjualan :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <input type="hidden" class="form-control" name="stat_ppn" id="stat_ppn" readonly>
                                                <input type="text" class="form-control" name="stat_ppnMask" id="stat_ppnMask" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Metode Pembayaran :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <input type="text" class="form-control" name="metode_bayar" id="metode_bayar" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label id="labelDiskon">Diskon :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <input type="hidden" class="form-control" name="jenis_diskon" id="jenis_diskon" readonly>
                                                <input type="hidden" class="form-control" name="value_diskon" id="value_diskon" readonly>
                                                <input type="text" class="form-control" name="value_diskonMask" id="value_diskonMask" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row divDp">
                                            <label class="col-lg-3 col-form-label">Sisa Down Payment</label>
                                            <div class="col-lg-9">
                                                <input type="hidden" class="form-control" name="sisaDp" id="sisaDp" value="0" readonly>
                                                <input type="text" class="form-control" onkeypress="return validasiDecimal(this,event);" name="sisaDpMask" id="sisaDpMask" value="0" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group divDp" >
                                            <label>Penggunaan Down Payment (DP) :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="hidden" class="form-control" onkeypress="return validasiDecimal(this,event);" name="dp" id="dp" value="0" readonly>
                                                    <input type="text" class="form-control" onkeypress="return validasiDecimal(this,event);" name="dpMask" id="dpMask" value="0" readonly>
                                                </div>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih alamat customer terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Penggunaan Syarat Dan Ketentuan :</label>
                                            <div>
                                                <div class="radio-inline">
                                                    <label class="radio">
                                                        <input type="radio" id="termsSo" value="use" name="terms_usage" checked />
                                                        <span></span>Sales Order
                                                    </label>
                                                    <label class="radio">
                                                        <input type="radio" id="buatTerms" value="new" name="terms_usage" />
                                                        <span></span>Buat Baru
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group" id="divTnc">
                                            <label>Syarat & Ketentuan :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <textarea class="form-control" id="tnc" name="tnc" rows="3" placeholder="Ketik Syarat & Ketentuan Invoice Disini atau gunakan Template pada tombol Template"></textarea>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-primary" id="btnTemplate" data-toggle="modal" data-target="#modal_list_terms">Template</button>
                                                    </div>
                                                </div>
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
                                            <label>No. Surat Jalan :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <select class="form-control select2 detailItem" id="delivery" name="delivery">
                                                        <option label="Label"></option>
                                                    </select>
                                                    <span class="form-text text-danger errItem" style="display:none;">*Harap pilih surat jalan terlebih dahulu!</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Tanggal Surat Jalan :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="text" id="tglSJ" class="form-control text-right" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Tanggal Kirim :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="text" id="tglKirimSJ" class="form-control text-right" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Jumlah Qty Surat Jalan :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="text" id="qtySJMask" class="form-control text-right" readonly>
                                                    <input type="hidden" id="qtySJ" class="form-control text-right" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Subtotal Surat Jalan :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="text" id="subtotalSJMask" class="form-control text-right" readonly>
                                                    <input type="hidden" id="subtotalSJ" class="form-control text-right" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
											<label class="col-lg-3 col-form-label"></label>
											<div class="col-lg-9">
												<button type="button" class="btn btn-primary font-weight-bold" id="btnAddItem">Tambah</button>
											</div>
										</div>

									</fieldset>
								</div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset>
                                        <legend class="text-muted"><h6><i class="la la-list"></i> List Surat Jalan</h6></legend>
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
										<label class="col-lg-3 col-form-label">Total Penjualan</label>
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
										<label class="col-lg-3 col-form-label">Total Dpp</label>
										<div class="col-lg-9">
											<input type="text" value="0" id="dppMask" class="form-control text-right" readonly>
											<input type="hidden" id="dpp" name="dpp" class="form-control text-right" readonly>
										</div>
									</div>

									<div class="form-group row">
										<label class="col-lg-3 col-form-label">Diskon</label>
										<div class="col-lg-9">
											<input type="text" value="0" id="discNominalMask" class="form-control text-right" readonly>
											<input type="hidden" id="discNominal" name="discNominal" class="form-control text-right" readonly>
										</div>
									</div>

									<div class="form-group row">
										<label class="col-lg-3 col-form-label">Total PPn</label>
										<div class="col-lg-9">
											<input type="text" value="0" id="ppnMask" class="form-control text-right" readonly>
											<input type="hidden" id="ppn" name="ppn" class="form-control text-right" readonly>
										</div>
									</div>

									<div class="form-group row">
										<label class="col-lg-3 col-form-label">Grand Total</label>
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
                                <button type="submit" class="btn btn-light-primary font-weight-bold mr-2"> Simpan <i class="flaticon-paper-plane-1"></i></button>
                            </div>
                        </div>
                    </form>
                </div>

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

                <!-- Modal form detail delivery -->
				<div id="modal_detail_delivery" class="modal fade">
				    <div class="modal-dialog modal-xl">
					    <div class="modal-content">
						    <div class="modal-header bg-primary">

							    <h5 class="modal-title" id="txtDetailDelivery"></h5>
						    </div>
						    <div class="modal-body">
                                <div class="mb-7">
                                    <div class="row align-items-center">
                                        <div class="col-lg-9 col-xl-8">
                                            <div class="row align-items-center">
                                                <div class="col-md-6 my-2 my-md-0">
                                                    <div class="input-icon">
                                                        <input type="text" class="form-control" placeholder="Search..." id="list_product_search_query"/>
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

                                <div class="datatable datatable-bordered datatable-head-custom" id="list_item_detail"></div>

						    </div>

						    <div class="modal-footer">
							    <button type="button" class="btn btn-link" data-dismiss="modal">Tutup</button>
						    </div>
					    </div>
				    </div>
			    </div>
                <!-- /form detail delivery -->

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
            $('#customer').select2({
                allowClear: true,
                placeholder: "Pilih Customer"
            });

            $('#SalesOrder').select2({
                allowClear: true,
                placeholder: "Pilih Sales Order"
            });

            $('#delivery').select2({
                allowClear: true,
                placeholder: "Pilih Pengiriman Barang"
            });

            $('#company_account').select2({
                allowClear: true,
                placeholder: "Pilih Rekening Perusahaan"
            });

            $('#tanggal_inv_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                autoclose : true,
                format : "dd MM yyyy",
                locale: "id",
            });

            $("#tanggal_inv_picker").on('change', function() {
                var invDate = new Date($("#tanggal_inv_picker").datepicker('getDate'));
                var maxDate = new Date($("#tanggal_sj_max").val());

                if (invDate < maxDate) {
                    Swal.fire(
                        "Error!",
                        "Tanggal Invoice tidak boleh dibawah dari tanggal SJ paling akhir!.",
                        "warning"
                    )
                    $("#tanggal_inv_picker").datepicker('setDate', maxDate);
                }
                else {
                    $("#tanggal_inv").val($("#tanggal_inv_picker").data('datepicker').getFormattedDate('yyyy-mm-dd'));
                }
                setTenorDate($("#tanggal_inv").val(), $("#durasiJT").val());
            });

            $(".divDp").hide();
            $("#company_account").val("{{$dataPreference->rekening}}").trigger("change");
        });

        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        $("#cancel").on('click', function(e) {
            Swal.fire({
                title: "Batal?",
                text: "Apakah anda ingin membatalkan pembuatan invoice penjualan?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    window.location.href = "{{ url('/SalesInvoice') }}";
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
                            "Harap Tambahkan Minimum 1 Surat Jalan Pengiriman!.",
                            "warning"
                        )
                        count = parseInt(count) + 1;
                    }

                    if(parseInt($("#durasiJT").val()) < 1 && $("#metode_bayar").val() == "CREDIT") {
                        Swal.fire(
                            "Gagal!",
                            "Jatuh Tempo tidak dapat 0 hari atau dibawah 0 hari untuk metode pembayaran CREDIT!.",
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

        $("#dp").on("change", function() {
            if (parseFloat($(this).val()) > parseFloat($("#sisaDp").val())) {

                Swal.fire(
                    "Gagal!",
                    "Alokasi Penggunaan DP Melebihi Nominal DP !",
                    "warning"
                );
                $(this).val(0);
                return false;
            }

            if (parseFloat($(this).val()) > parseFloat($("#gt").val())) {

                Swal.fire(
                    "Gagal!",
                    "Alokasi Penggunaan DP Melebihi Nominal Invoice !",
                    "warning"
                );
                $(this).val(0);
                return false;
            }
        });

        $('input[name=terms_usage]').on('change', function() {
			var val = $(this).val();
            var flag = "";
            var idSo = $("#SalesOrder option:selected").val();
			if (val == "use") {
			    flag = 1;
                $("#tnc").attr('readonly', true);
                $("#btnTemplate").attr('disabled', true);
			}
			else {
                flag = 0;
                $("#tnc").attr('readonly', false);
                $("#btnTemplate").attr('disabled', false);
			}

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/SalesInvoice/GetTermsByOption",
                method: 'POST',
                data: {
                    idInvoice: '',
                    idSalesOrder: idSo,
                    flagTerms: flag
                },
                success: function(result){
                    if (result.length > 0) {
                        var dataTemplate = "";
                        for (var i = 0; i < result.length;i++) {
                            dataTemplate += result[i].terms_and_cond;
                            counter = result.length - 1;
                            if (i != counter) {
                                dataTemplate += "\n";
                            }
                        }
                        $("#tnc").val(dataTemplate);
                    }
                    else {
                        $("#tnc").val("");
                    }
                }
            });
		});

        $("#customer").on("change", function() {
            //getListProduct
            getCustomerSalesOrder($(this).val());

            //Hapus Daftar penjualan
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/SalesInvoice/ResetDetail",
                method: 'POST',
                data: {
                    idInv: 'DRAFT',
                },
                success: function(result){
                    var datatable = $('#list_item').KTDatatable();
                        datatable.setDataSourceParam('idSalesOrder', '');
                        datatable.setDataSourceParam('idInvoice', '');
                        datatable.reload();
                        footerDataForm('DRAFT');
                        getInvoiceDate('DRAFT');
                }
            });
        });

        $('#SalesOrder').on('change', function() {
            var idSalesOrder = $(this).val();
            //getDefaultAddress
            if ($(this).val() != "") {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/SalesInvoice/GetDefaultAddress",
                    method: 'POST',
                    data: {
                        idSalesOrder: $(this).val(),
                    },
                    success: function(result){
                        if (result.length > 0) {
                            $("#id_alamat").val(result[0].id);
                            $("#alamat").val(ucwords(result[0].alamat_customer));
                        }
                    }
                });

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/SalesInvoice/GetSalesOrderData",
                    method: 'POST',
                    data: {
                        idSalesOrder: $(this).val(),
                    },
                    success: function(result){
                        if (result.length > 0) {
                            $("#stat_ppn").val(result[0].flag_ppn);
                            if (result[0].sisa_dp == 0 || result[0].sisa_dp == null ) {
                                $("#sisaDp").val(0);
                                $("#dp").val(0);
                                $(".divDp").hide();
                            }
                            else {
                                var sisaDp = result[0].sisa_dp.toString().replace(".", ",");
                                $("#sisaDp").val(sisaDp);
                                $("#sisaDpMask").val(parseFloat(sisaDp).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                                $("#dp").val(0);
                                $(".divDp").show();
                            }
                            if (result[0].flag_ppn == "I") {
                                $("#stat_ppnMask").val("PPn Incl.");
                            }
                            else if(result[0].flag_ppn == "Y") {
                                $("#stat_ppnMask").val("PPn Excl.");
                            }
                            else {
                                $("#stat_ppnMask").val("Non PPn");
                            }

                            if (result[0].jenis_diskon != "") {
                                var valueDiskon = 0;
                                if (result[0].jenis_diskon == "P") {
                                    $("#labelDiskon").text("Diskon (%) : ");
                                    valueDiskon = result[0].persentase_diskon;
                                }
                                else if (result[0].jenis_diskon == "N") {
                                    $("#labelDiskon").text("Diskon (Rp) : ");
                                    valueDiskon = result[0].nominal_diskon;
                                }
                                var valueDiskonFixed = valueDiskon.toString().replace(".", ",");
                                $("#jenis_diskon").val(result[0].jenis_diskon);
                                $("#value_diskon").val(valueDiskonFixed);
                                $("#value_diskonMask").val(parseFloat(valueDiskonFixed).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                            }
                            else {
                                $("#value_diskon").val(0);
                                $("#value_diskonMask").val("0");
                            }

                            $("#metode_bayar").val(ucwords(result[0].metode_pembayaran).toUpperCase());
                            if (result[0].metode_pembayaran == "cash") {
                                $("#durasiJT").val("0").attr('readonly', true);
                            }
                            else {
                                $("#durasiJT").val(result[0].durasi_jt).attr('readonly', false);
                            }
                        }
                    }
                });
                $('input[name=terms_usage]:checked').trigger("change");
            }
            else {
                $("#id_alamat").val("");
                $("#alamat").val("");
                $("#metode_bayar").val("");
                $("#stat_ppn").val("");
                $("#stat_ppnMask").val("");
            }

            if (idSalesOrder != "") {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/SalesInvoice/SetDetail",
                    method: 'POST',
                    data: {
                        idInvoice: '',
                        idSalesOrder: $(this).val(),
                    },
                    success: function(result){
                        if (result != "") {
                            var datatable = $('#list_item').KTDatatable();
                                datatable.setDataSourceParam('idSalesOrder', idSalesOrder);
                                datatable.setDataSourceParam('idInvoice', '');
                                datatable.reload();
                                footerDataForm('DRAFT');
                                getInvoiceDate('DRAFT');
                        }
                    }
                });
            }
            getDelivery(idSalesOrder);
        });

        $("#delivery").on("change", function() {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/SalesInvoice/GetDataDelivery",
                method: 'POST',
                data: {
                    idDelivery: $(this).val(),
                    idSalesOrder: $("#SalesOrder option:selected").val(),
                },
                success: function(result){
                    if (result.length > 0) {
                        var qtySJ = result[0].jumlah_total_sj;
                        var subtotalSJ = result[0].subtotalDlv;
                        $("#qtySJMask").val(parseFloat(qtySJ).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        $("#qtySJ").val(qtySJ);
                        $("#subtotalSJ").val(subtotalSJ);
                        $("#subtotalSJMask").val(parseFloat(subtotalSJ).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        $("#tglSJ").val(formatDate(result[0].tanggal_sj));
                        if (result[0].tanggal_kirim != null) {
                            $("#tglKirimSJ").val(formatDate(result[0].tanggal_kirim));
                        }
                        else {
                            $("#tglKirimSJ").val("-");
                        }

                    }
                }
            });

        });

        function getDelivery(idSo) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/SalesInvoice/GetDelivery",
                method: 'POST',
                data: {
                    idSalesOrder: idSo,
                },
                success: function(result){
                    $('#delivery').find('option:not(:first)').remove();
                    if (result.length > 0) {
                        for (var i = 0; i < result.length;i++) {
                            $("#delivery").append($('<option>', {
                                value:result[i].id,
                                text:result[i].kode_pengiriman.toUpperCase()
                            }));
                        }
                    }
                }
            });
        }

        function getCustomerSalesOrder(idCustomer) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/SalesInvoice/GetSalesOrder",
                method: 'POST',
                data: {
                    id_customer: idCustomer,
                },
                success: function(result){
                    $('#SalesOrder').find('option:not(:first)').remove();
                    if (result.length > 0) {
                        for (var i = 0; i < result.length;i++) {
                            $("#SalesOrder").append($('<option>', {
                                value:result[i].id,
                                text:result[i].no_so.toUpperCase()
                            }));
                        }
                    }
                    $('#SalesOrder').trigger('change');
                }
            });
        }

        $(document).ready(function() {

            var datatable = $('#list_item').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/SalesInvoice/GetDetail',
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
                        field: 'ViewDetail',
                        title: '',
                        sortable: false,
                        width: 50,
                        overflow: 'visible',
                        autoHide: false,
                        textAlign: 'center',
                        template: function(row) {
                            var txtAction = "<a href='#' class='btn btn-sm btn-clean btn-icon' data-toggle='modal' data-target='#modal_detail_delivery' title='Detail' onclick='viewDetailItem(" + row.id_sj + ");return false;'>";
                                txtAction += "<i class='la la-search'></i>";
                                txtAction += "</a>";

                            return txtAction;
                        },
                    },
                    {
                        field: 'kode_pengiriman',
                        title: 'Kode Pengiriman',
                        autoHide: false,
                        textAlign: 'center',
                        width: 'auto',
                        template: function(row) {
                            return row.kode_pengiriman.toUpperCase();
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
                        width: 'auto',
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
                        field: 'tanggal_kirim',
                        title: 'Tanggal Kirim',
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            if (row.tanggal_kirim != null) {
                                return formatDate(row.tanggal_kirim);
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'qty_sj',
                        title: 'Jumlah Pengiriman',
                        textAlign: 'center',
                        autoHide: false,
                        width: 'auto',
                        template: function(row) {
                            return parseFloat(row.qty_sj).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'subtotal_sj',
                        title: 'Subtotal(Rp)',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            var jenisPPn = $("#stat_ppn").val();
                            var persenPPNInclude = (100 + parseFloat("{{$taxSettings->ppn_percentage}}")) / 100;
                            if (jenisPPn == "I") {
                                var subtotalMask = parseFloat(row.subtotal_sj) / parseFloat(persenPPNInclude);
                            }
                            else {
                                var subtotalMask = parseFloat(row.subtotal_sj);
                            }
                            return parseFloat(subtotalMask).toLocaleString('id-ID', { maximumFractionDigits: 2});
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
                            var txtAction = "<a href='#' class='btn btn-sm btn-clean btn-icon' title='Hapus' onclick='deleteDetailItem("+row.id+");return false;'>";
                                txtAction += "<i class='la la-trash'></i>";
                                txtAction += "</a>";

                            return txtAction;
                        },
                    }
                ],
            });
        });

        $(document).ready(function() {

            var datatable = $('#list_item_detail').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/SalesInvoice/GetDetailDelivery',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },

                        }
                    },
                    pageSize: 100,
                    serverPaging: false,
                    serverFiltering: false,
                    serverSorting: false,
                    saveState: false
                },

                layout: {
                    scroll: false,
                    height: 'auto',
                    footer: false
                },

                sortable: 'auto',

                filterable: 'auto',

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
                        title: 'Kode Item',
                        autoHide: false,
                        width: 70,
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
                        title: 'Nama Item',
                        autoHide: false,
                        width: 200,
                        textAlign: 'center',
                        template: function(row) {
                            return row.nama_item.toUpperCase();
                        },
                    },
                    {
                        field: 'qty_item',
                        title: 'Jumlah Kirim',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.qty_item).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'nama_satuan',
                        title: 'Satuan',
                        autoHide: false,
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            return row.nama_satuan.toUpperCase();
                        },
                    },
                    {
                        field: 'harga_jual',
                        title: 'Harga(Rp)',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.harga_jual).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'subtotal_sj',
                        title: 'Subtotal(Rp)',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.subtotal_sj).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
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

            if (qty > outsQty) {

                Swal.fire(
                    "Gagal!",
                    "Jumlah Item Melebihi Outstanding Order !",
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
                            url: "/SalesInvoice/StoreDetail",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idDlv : $("#delivery option:selected").val(),
                                idInvoice : "",
                                qtyDlv : $("#qtySJ").val().replace('.',''),
                                subtotalDlv : $("#subtotalSJ").val().replace('.',''),
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Item Berhasil ditambahkan!.",
                                        "success"
                                    )
                                    $("#delivery").val("").trigger('change'),
                                    $("#qtySJ").val("");
                                    $("#subtotalSJ").val("");
                                    $("#tglKirimSJ").val("");
                                    $("#tglSJ").val("");
                                    var datatable = $('#list_item').KTDatatable();
                                        datatable.setDataSourceParam('idInvoice','');
                                        datatable.reload();
                                    footerDataForm('DRAFT');
                                    getInvoiceDate('DRAFT');
                                }
                                else if (result == "failDuplicate") {
                                    Swal.fire(
                                        "Gagal!",
                                        "Surat Jalan ini sudah tersedia pada List Invoice Penjualan !",
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
                        url: "/SalesInvoice/DeleteDetail",
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
                        datatable.setDataSourceParam('idInvoice','');
                        datatable.reload();
                        footerDataForm('DRAFT');
                        getInvoiceDate('DRAFT');
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

        function viewDetailItem(id) {

            var datatable = $('#list_item_detail').KTDatatable();
                datatable.setDataSourceParam('idDelivery', id);
                datatable.setDataSourceParam('idSo', $("#SalesOrder").val());
                datatable.reload();

            $("#txtDetailDelivery").text($("#txt_"+id).text().toUpperCase());

        }

        $(document).ready(function() {
            //getTemplateTerms
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/SalesOrder/GetListTerms",
                method: 'POST',
                data: {
                    target: "invoice_penjualan",
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
                url: "/SalesInvoice/GetTerms",
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

        function getInvoiceDate(idInv) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/SalesInvoice/GetInvoiceDate",
                method: 'POST',
                data: {
                    idInvoice: idInv,
                },
                success: function(result){
                    if (result != "null") {
                        var tglMax = result.lastDate;
                        $("#tanggal_inv").val(tglMax);
                        $("#tanggal_sj_max").val(tglMax);
                        $("#tanggal_inv_picker").datepicker('setDate', new Date(tglMax));

                        var tenor = $("#durasiJT").val();
                        if (tenor == "") {
                            tenor = 0;
                            setTenorDate(tglMax, tenor);
                        }
                        else {
                            setTenorDate(tglMax, tenor);
                        }

                    }
                    else {
                        $("#tanggal_inv").val("");
                        $("#tanggal_inv_picker").val("");
                    }
                }
            });
        }

        function setTenorDate(invDate, tenor) {
            var date = new Date(invDate);
            var intTenor = parseInt(tenor, 10);
                date.setDate(date.getDate() + intTenor);

            var day = date.getDate();
            var month = date.getMonth() + 1;
            var year = date.getFullYear();
            if (parseInt(month) < 10) {
                month = "0" + month;
            }
            var tenorDate = year + "-" + month + "-" + day;
            $("#tgl_jt").val(tenorDate);
            $("#tgl_jt_picker").val(formatDate(tenorDate));
        }

        $("#durasiJT").on('change', function() {
            setTenorDate($("#tanggal_inv").val(), $(this).val());
        });

        function footerDataForm(idInv) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/SalesInvoice/GetDataFooter",
                method: 'POST',
                data: {
                    idInvoice: idInv,
                },
                success: function(result){
                    if (result != "null") {
                        var ttlQty = result.qtyInv;
                        var ttlQtyFixed = ttlQty.toString().replace(".", ",");
                        var subtotal = result.subtotalInv;
                        var subtotalFixed = subtotal.toString().replace(".", ",");
                        var jenisPPn = $('#stat_ppn').val();
                        var persenPPNInclude = (100 + parseFloat("{{$taxSettings->ppn_percentage}}")) / 100;
                        var persenPPNExclude = parseFloat("{{$taxSettings->ppn_percentage}}") / 100;
                        var jenisDisc = $('#jenis_diskon').val();

                        $("#qtyTtl").val(ttlQtyFixed);
                        $("#qtyTtlMask").val(parseFloat(ttlQtyFixed).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                        if (jenisPPn == "I") {
                            subtotalFixed = parseFloat(subtotalFixed) / parseFloat(persenPPNInclude);
                        }

                        var subtotalMask = parseFloat(subtotalFixed).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        $("#dpp").val(subtotalFixed);
                        $("#dppMask").val(subtotalMask);

                        var persenDiskon = 0;
                        var diskonNominal = 0;

                        if(jenisDisc == "P") {
                            persenDiskon = $("#value_diskon").val();
                            diskonNominal = parseFloat(subtotalFixed) * (parseFloat(persenDiskon) / 100);
                        }
                        else if(jenisDisc == "N") {
                            diskonNominal = $("#value_diskon").val();
                        }

                        $("#discNominal").val(diskonNominal);
                        $("#discNominalMask").val(parseFloat(diskonNominal).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                        if (jenisPPn != "N") {
                            var ppn = (parseFloat(subtotalFixed) - parseFloat(diskonNominal)) * parseFloat(persenPPNExclude);
                            $("#ppn").val(ppn);
                            $("#ppnMask").val(parseFloat(ppn).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        }
                        else {
                            var ppn = 0;
                            $("#ppn").val(ppn);
                            $("#ppnMask").val(parseFloat(ppn).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        }

                        var grandTotal = (parseFloat(subtotalFixed) - parseFloat(diskonNominal)) + parseFloat(ppn);
                        var dp = $("#sisaDp").val();
                        if(parseFloat(dp) > Math.ceil(grandTotal)) {
                            $("#dp").val(Math.ceil(grandTotal));
                            $("#dpMask").val(parseFloat(Math.ceil(grandTotal)).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        }
                        else {
                            $("#dp").val(Math.ceil(dp));
                            $("#dpMask").val(parseFloat(Math.ceil(dp)).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        }

                        $("#gt").val(Math.ceil(grandTotal));
                        $("#gtMask").val(parseFloat(Math.ceil(grandTotal)).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                    }
                    else {
                        $("#qtyTtl").val(0);
                        $("#qtyTtlMask").val(parseFloat(0).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        $("#dpp").val(0);
                        $("#dppMask").val(parseFloat(0).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        $("#discNominal").val(0);
                        $("#discNominalMask").val(parseFloat(0).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        $("#ppn").val(0);
                        $("#ppnMask").val(parseFloat(0).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        $("#gt").val(0);
                        $("#gtMask").val(parseFloat(0).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                    }
                }
            });
        }
    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
