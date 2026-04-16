@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
                <!--begin::Layout-->
                <div class="flex-row-fluid ml-lg-8">
                    <!--begin::Section-->
                    <div class="card card-custom">
                        <div class="card-toolbar">
                            <ul class="nav nav-pills nav-bold nav-fill">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#tab_pane_1" id="tab1">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Daftar Belanja</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link " data-toggle="tab" href="#tab_pane_2" id="tab2">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Pembayaran</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="tab_pane_1" role="tabpanel" aria-labelledby="tab_pane_1">
                                    <div class="row">
                                        <!--begin::Layout-->
                                        <div class="flex-row-fluid">
                                            <!--begin::Section-->
                                            <div class="card card-custom">
                                                <!--begin::Header-->
                                                <div class="card-header header-elements-sm-inline">
                                                    <div class="col-11 ml-4 mt-5">
                                                        <select class="form-control select2" id="customer" name="customer" style="width=100%;">
                                                            <option label="Label"></option>
                                                            @foreach($dataCustomer as $customer)
                                                            <option value="{{$customer->id}}">{{strtoupper($customer->nama_customer)}}</option>
                                                            @endforeach
                                                        </select>
                                                        <span class="form-text text-danger errItem" style="display:none;">*Harap pilih barang terlebih dahulu!</span>
                                                    </div>
                                                    <div class="card-toolbar col-12">
                                                        <div class="col-5">
                                                            <select class="form-control select2 detailItem" id="product" name="product" style="width=100%;">
                                                                <option label="Label"></option>
                                                                @foreach($dataProduct as $product)
                                                                <option value="{{$product->id}}">{{strtoupper($product->kode_item)}} - {{strtoupper($product->nama_item)}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="form-text text-danger errItem" style="display:none;">*Harap pilih barang terlebih dahulu!</span>
                                                        </div>
                                                        <div class="col-2">
                                                            <select class="form-control select2 detailUnit" id="productUnit" name="productUnit" style="width=100%;">
                                                                <option label="Label"></option>
                                                            </select>
                                                            <span class="form-text text-danger errUnit" style="display:none;">*Harap pilih satuan barang terlebih dahulu!</span>
                                                        </div>
                                                        <div class="col-2">
                                                            <input type="hidden" id="idTransaction" class="form-control text-right" readonly>
                                                            <input type="hidden" id="flagTrigger" class="form-control text-right" value="0" readonly>
                                                            <input type="hidden" id="model_sales" class="form-control text-right" readonly>
                                                            <input type="hidden" id="qty_model" class="form-control text-right" readonly>
                                                            <input type="hidden" id="hargaTemp" class="form-control text-right" readonly>
                                                            <input type="hidden" id="hargaGrosir" class="form-control text-right" readonly>
                                                            <input type="hidden" id="hargaGrosir2" class="form-control text-right" readonly>
                                                            <input type="text" id="hargaNormal" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right" placeholder="Harga Satuan Barang" readonly>
                                                            <span class="form-text text-danger errUnit" style="display:none;">*Harap pilih satuan barang terlebih dahulu!</span>
                                                        </div>
                                                        <div class="col-2">
                                                            <input type="hidden" id="qtyGrosir" class="form-control text-right" readonly>
                                                            <input type="hidden" id="qtyGrosir2" class="form-control text-right" readonly>
                                                            <input type="hidden" id="jenisGrosir" class="form-control text-right" readonly>
                                                            <input type="hidden" id="jenisGrosir2" class="form-control text-right" readonly>
                                                            <input type="text" id="qty" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right" placeholder="Input Qty Penjualan" >
                                                            <span class="form-text text-danger errUnit" style="display:none;">*Harap input qty barang terlebih dahulu!</span>
                                                        </div>
                                                        <div class="col-1 text-center">
                                                            <button type="button" class="btn btn-primary font-weight-bolder font-size-sm" id="btnAdd">Tambah</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end::Header-->
                                                <div class="card-body">
                                                    <!--begin::Shopping Cart-->
                                                    <div class="table-responsive">
                                                        <table class="table" id="list_item">
                                                            <!--begin::Cart Header-->
                                                            <thead>
                                                                <tr>
                                                                    <th style="width:35%;">Nama Barang</th>
                                                                    <th style="width:15%;" class="text-center">Satuan</th>
                                                                    <th style="width:15%;" class="text-center">Qty</th>
                                                                    <th style="width:15%;" class="text-center">Harga</th>
                                                                    <th style="width:15%;" class="text-center">Total</th>
                                                                    <th style="width:5%;"></th>
                                                                </tr>
                                                            </thead>
                                                            <!--end::Cart Header-->
                                                            <tbody>
                                                                <!--begin::Cart Content-->
                                                                {{-- <tr>
                                                                    <td class="align-middle">
                                                                        <span class="text-dark text-hover-primary">Street Sneakers</span>
                                                                        <input type="hidden" class="form-control qty" id="idProduct_" name="idProduct_" />
                                                                    </td>
                                                                    <td class="text-center align-middle">
                                                                        <span class="text-dark text-hover-primary">PCS</span>
                                                                        <input type="hidden" class="form-control qty" id="idSatuan_" name="idSatuan_" />
                                                                    </td>
                                                                    <td class="text-center align-middle">

                                                                        <div class="inputQty row w-150">
                                                                            <div class="col-3">
                                                                                <button type="button" class="btn btn-xs btn-light-success btn-icon mr-2 qtyMinus">
                                                                                    <i class="ki ki-minus icon-xs"></i>
                                                                                </button>
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <input type="text" class="form-control qtyInput" id="qtyListMask_" name="qtyListMask_" autocomplete="off" data-a-dec="," data-a-sep="." />
                                                                                <input type="hidden" class="form-control qty" id="qtyList_" name="qtyList_" />
                                                                            </div>
                                                                            <div class="col-3">
                                                                                <button type="button" class="btn btn-xs btn-light-success btn-icon qtyPlus">
                                                                                    <i class="ki ki-plus icon-xs"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>

                                                                    </td>
                                                                    <td class="text-right align-middle font-weight-bolder font-size-h5">
                                                                        <span class="text-dark text-hover-primary">1.000</span>
                                                                        <input type="hidden" class="form-control harga" id="hargaList_" name="hargaList_" />
                                                                    </td>
                                                                    <td class="text-right align-middle font-weight-bolder font-size-h5">
                                                                        <span class="text-dark text-hover-primary">2.000</span>
                                                                        <input type="hidden" class="form-control subtotal" id="subtotalList_" name="subtotalList_" />
                                                                    </td>
                                                                    <td class="text-right align-middle">
                                                                        <button type="button" class="btn btn-lg btn-danger btn-icon hapusItem">
                                                                            <i class='la la-trash'></i>
                                                                        </button>
                                                                    </td>
                                                                </tr> --}}
                                                                <tr id="footer">
                                                                    <td class="text-center align-middle font-weight-bolder font-size-h5" colspan="2">
                                                                        <span class="text-dark text-hover-primary">Subtotal</span>
                                                                    </td>
                                                                    <td class="text-right align-middle font-weight-bolder font-size-h5">
                                                                        <span class="text-dark text-hover-primary" id="labelTotalQty">0</span>
                                                                        <input type="hidden" id="totalQty" readonly>
                                                                    </td>
                                                                    <td class="align-middle">
                                                                    </td>
                                                                    <td class="text-right align-middle font-weight-bolder font-size-h5">
                                                                        <span class="text-dark text-hover-primary" id="labelSubtotalFooter">0</span>
                                                                        <input type="hidden" id="subtotal" readonly>
                                                                    </td>
                                                                </tr>
                                                                <!--end::Cart Content-->
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <!--end::Shopping Cart-->
                                                </div>
                                            </div>
                                            <!--end::Section-->
                                        </div>
                                        <!--end::Layout-->
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="tab_pane_2" role="tabpanel" aria-labelledby="tab_pane_2">
                                    <div class="card card-custom">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <!--begin::Section-->
                                                    <h6 class="font-weight-bolder mb-3">Detail Belanja:</h6>
                                                    <div class="text-dark-50 line-height-lg">
                                                        <div class="table-responsive">
                                                            <table class="table" id="item_display">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width:5%;" class="text-center">No.</th>
                                                                        <th style="width:35%;">Nama Barang</th>
                                                                        <th style="width:15%;" class="text-center">Satuan</th>
                                                                        <th style="width:15%;" class="text-center">Qty</th>
                                                                        <th style="width:15%;" class="text-center">Harga</th>
                                                                        <th style="width:15%;" class="text-center">Total</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    {{-- <tr class="font-weight-boldest">
                                                                        <td class="border-0 pl-0 pt-7 d-flex align-items-center">
                                                                        <!--begin::Symbol-->
                                                                        <div class="symbol symbol-40 flex-shrink-0 mr-4 bg-light">
                                                                            {{-- <div class="symbol-label" style="background-image: url('assets/media/products/11.png')"></div>
                                                                        </div>
                                                                        <!--end::Symbol-->
                                                                        Street Sneakers</td>
                                                                        <td class="text-right pt-7 align-middle">2</td>
                                                                        <td class="text-right pt-7 align-middle">$90.00</td>
                                                                        <td class="text-primary pr-0 pt-7 text-right align-middle">$180.00</td>
                                                                    </tr> --}}

                                                                    <tr>
                                                                        <td colspan="4" class="border-0 pt-0 font-weight-bolder font-size-h5 text-right">Grand Total</td>
                                                                        <td colspan="2" class="border-0 pt-0 font-weight-bolder font-size-h5 text-success text-right pr-0"><span id="grandTotalDisplay"></span>
                                                                            <input type="hidden" id="finalTotalQty" readonly>
                                                                            <input type="hidden" id="finalSubtotal" readonly>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="separator separator-dashed my-5"></div>
                                                    <!--end::Section-->
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="form-group">
                                                        <label>Pelanggan :</label>
                                                        <input type="text" id="customer_text" class="form-control" readonly>
                                                        <input type="hidden" id="customer_id" readonly>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Metode Pembayaran :</label>
                                                        <div class="radio-inline">
                                                            <label class="radio">
                                                            <input type="radio" id="edc" name="metode_pembayaran" value="edc" />
                                                            <span></span>EDC (BCA)</label>
                                                            <label class="radio">
                                                            <input type="radio" id="qris" name="metode_pembayaran" value="qris" />
                                                            <span></span>QRIS</label>
                                                            <label class="radio">
                                                            <input type="radio" id="transfer" name="metode_pembayaran" value="transfer" />
                                                            <span></span>Transfer</label>
                                                            <label class="radio">
                                                            <input type="radio" id="cash" name="metode_pembayaran" value="cash" />
                                                            <span></span>Cash/Tunai</label>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row rekeningdetail" id="rowRekening">
                                                        <label class="col-lg-3 col-form-label">Rekening Pembayaran :</label>
                                                        <div class="col-lg-9">
                                                            <select class="form-control select2 reqTrf" id="rekening" name="rekening" style="width: 100%;">
                                                                <option label="Label"></option>
                                                                @foreach($dataRekening as $rekening)
                                                                <option value="{{$rekening->id}}">{{strtoupper($rekening->nama_bank)}} - {{strtoupper($rekening->nomor_rekening)}} A/N {{strtoupper($rekening->atas_nama)}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="form-text text-danger errTrf" id="errTrf" style="display:none;">*Harap pilih rekening terlebih dahulu!</span>
                                                        </div>
                                                    </div>

                                                    <!--begin::Input-->
                                                    <div class="form-group">
                                                        <label>Nominal Belanja</label>
                                                        <input type="text" id="cash_charge_mask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right" readonly>
                                                        <input type="hidden" id="cash_charge" readonly>
                                                    </div>
                                                    <!--end::Input-->

                                                    <!--begin::Input-->
                                                    <div class="form-group debt">
                                                        <label>Hutang Transaksi Sebelumnya</label>
                                                        <input type="text" id="prev_debt_mask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right" readonly>
                                                        <input type="hidden" id="prev_debt" readonly>
                                                    </div>
                                                    <!--end::Input-->

                                                    <!--begin::Input-->
                                                    <div class="form-group">
                                                        <label>Nominal Bayar</label>
                                                        <input type="text" id="cash_payment_mask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right">
                                                        <input type="hidden" class="reqCash" id="cash_payment" readonly>
                                                        <span class="form-text text-danger errCash" id="errCash" style="display:none;">*Harap input nominal pembayaran terlebih dahulu!</span>
                                                        <span class="form-text text-danger errCashSufficient" id="errCashSufficient" style="display:none;">*Nominal pembayaran kurang dari total belanja!</span>
                                                    </div>
                                                    <!--end::Input-->

                                                    <!--begin::Input-->
                                                    <div class="form-group cashdetail">
                                                        <label>Kembalian</label>
                                                        <input type="text" id="cash_change_mask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right" readonly>
                                                        <input type="hidden" id="cash_change" readonly>
                                                    </div>
                                                    <!--end::Input-->

                                                    <!--begin::Input-->
                                                    {{-- <div class="form-group carddetail">
                                                        <label>No Kartu</label>
                                                        <input type="text" class="form-control form-control-lg reqEDC" id="ccnumber" name="ccnumber" placeholder="No. Kartu" />
                                                        <span class="form-text text-danger errEDC" style="display:none;">Harap input No. Kartu terlebih dahulu!</span>
                                                    </div>
                                                    <!--end::Input-->
                                                    <!--begin::Input-->
                                                    <div class="form-group carddetail">
                                                        <label>Nama Pada Kartu</label>
                                                        <input type="text" class="form-control form-control-lg reqEDC" id="ccname" name="ccname" placeholder="Nama Pada Kartu" />
                                                        <span class="form-text text-danger errEDC" style="display:none;">Harap input Nama pada Kartu terlebih dahulu!</span>
                                                    </div>
                                                    <!--end::Input-->
                                                        <!--begin::Input-->
                                                    <div class="form-group carddetail">
                                                        <label>Bulan Expiry Kartu</label>
                                                        <input type="number" class="form-control form-control-lg reqEDC" min="01" max="12" maxlength="2" id="ccmonth" name="ccmonth" placeholder="Bulan Expiry Kartu" />
                                                        <span class="form-text text-danger errEDC" style="display:none;">Harap input Bulan Expiry Kartu terlebih dahulu!</span>
                                                    </div>
                                                    <!--end::Input-->
                                                    <!--begin::Input-->
                                                    <div class="form-group carddetail">
                                                        <label>Tahun Expiry Kartu</label>
                                                        <input type="number" class="form-control form-control-lg reqEDC" maxlength="4" name="ccyear" id="ccyear" placeholder="Tahun Expiry Kartu" />
                                                        <span class="form-text text-danger errEDC" style="display:none;">Harap input Tahun Expiry Kartu terlebih dahulu!</span>
                                                    </div>
                                                    <!--end::Input--> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="card-footer bg-white d-sm-flex justify-content-sm-between align-items-sm-center">
							<div class="mt-2 mt-sm-0">
                                <button type="button" id="btnCancel" class="btn btn-light-danger font-weight-bold mr-2" style="display:none ;">Keluar</button>
                                <button type="button" id="btnShop" class="btn btn-light-primary font-weight-bold mr-2" style="display:none ;"> Daftar Belanja</button>
                            </div>

                            <div class="mt-2 mt-sm-0">
                                <button type="button" id="btnReset" class="btn btn-light-primary font-weight-bold mr-2" style="display:none ;"> Buat Transaksi Baru </button>
                                {{-- <button id="btnCetak" style="display:none ;" type="button" class="btn btn-outline-primary" > Cetak Receipt<i class="fas fa-print ml-2"></i></button> --}}
                                <a id="btnCetak" style="display:none ;" type="button" class="btn btn-outline-primary" target="_blank"> Cetak Receipt<i class="fas fa-print ml-2"></i></a>
                                <button type="button" id="btnPembayaran" class="btn btn-light-primary font-weight-bold mr-2" style="display:none ;"> Pembayaran </button>
                                <button type="button" id="btnSubmit" class="btn btn-light-primary font-weight-bold mr-2" style="display:none ;"> Submit </button>
                            </div>
						</div>
                    </div>
                    <!--end::Section-->
                </div>
                <!--end::Layout-->
			</div>
			<!-- /content area -->
@endsection
@section('scripts')
    <script type="text/javascript">
        let tableData = [];
        let tableID = [];

        $(document).ready(function () {

            // var _wizardObj = new KTWizard('kt_wizard', {
            //     startStep: 1,
            //     // initial active step number
            //     clickableSteps: false // allow step clicking

            // });

            $('#customer').select2({
                allowClear: true,
                placeholder: "Pilih Customer"
            });

            $('#rekening').select2({
                allowClear: true,
                placeholder: "Pilih Rekening Pembayaran"
            });

            $('#product').select2({
                allowClear: true,
                placeholder: "Pilih Barang"
            });

            $('#productUnit').select2({
                allowClear: true,
                placeholder: "Pilih Satuan Barang"
            });

            $('#tanggal_sj_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                autoclose : true,
                format : "dd MM yyyy",
                locale: "id",
            });

            // $(".qtyInput").each(function() {
            //     var id = $(this).attr('id');
            //     $("#"+id).autoNumeric('init', {mDec: '0'});

            //     $("#"+id).autoNumeric('set', 1).trigger("change");

            // });
            $("#hargaNormal").autoNumeric('init', {mDec: '0'});
            $("#qty").autoNumeric('init', {mDec: '0'});
            $("#cash_payment_mask").autoNumeric('init', {mDec: '0'});

            showHidePaymentDetails("hide", "carddetail");
            showHidePaymentDetails("hide", "cashdetail");
            showHidePaymentDetails("hide", "rekeningdetail");

            var activeTabId = $('.nav-link.active').attr('id');
            if (activeTabId == "tab1") {
                $("#btnSubmit").hide();
                $("#btnShop").hide();
                $("#btnCancel").show();
                $("#btnPembayaran").show();


            }
            else if (activeTabId == "tab2") {
                $("#btnSubmit").show();
                $("#btnShop").show();
                $("#btnCancel").hide();
                $("#btnPembayaran").hide();
            }
        });

        $("#list_item").on('change', '.qtyInput', function() {
            $(this).closest('.inputQty').find(".qty").val($(this).autoNumeric("get"));

            var qty = $(this).closest('.inputQty').find(".qty").val();

            var flag = $("#flagTrigger").val();
            var mode = $(this).closest('tr').find(".modelSales").val();
            var qtyMode = $(this).closest('.inputQty').find(".qtyModel").val();

            var harga = $(this).closest('tr').find(".harga").val();
            var hargaNormal = $(this).closest('tr').find(".hargaTemp").val();

            var jenisGrosir = $(this).closest('tr').find(".jenisGrosir").val();
            var hargaGrosir = $(this).closest('tr').find(".hargaGrosir").val();
            var qtyGrosir = $(this).closest('.inputQty').find(".qtyGrosir").val();

            var jenisGrosir2 = $(this).closest('tr').find(".jenisGrosir2").val();
            var hargaGrosir2 = $(this).closest('tr').find(".hargaGrosir2").val();
            var qtyGrosir2 = $(this).closest('.inputQty').find(".qtyGrosir2").val();

            var hargaTemp = hargaNormal;
            var subtotal = 0;
            var txtHarga = "";
            var txtSubtotal = "";

            if (mode == "kelipatan") {
                if (qty % qtyMode != 0) {
                    var sisa = qty % qtyMode;
                    qty = parseFloat(qty) - parseFloat(sisa);
                    hargaTemp = harga;

                    $("#flagTrigger").val(1);
                    flag = $("#flagTrigger").val();
                    if (flag == 1) {
                        $(this).autoNumeric('set', qty).trigger("change");
                    }

                    $("#flagTrigger").val("0");
                    flag = $("#flagTrigger").val();

                    Swal.fire(
                        "Peringatan!",
                        "Item ini hanya dapat dijual dengan qty kelipatan " + qtyMode + "!",
                        "warning"
                    );

                    return false;
                }
            }
            //baru set flag
            if (jenisGrosir != "") {
                if (jenisGrosir == "kelipatan" && parseFloat(qty) % parseFloat(qtyGrosir) == 0) {
                    hargaTemp = hargaGrosir;
                }
                else if (jenisGrosir == "minimum" && parseFloat(qty) >= parseFloat(qtyGrosir)) {
                    hargaTemp = hargaGrosir;
                }
            }

            if (jenisGrosir2 != "") {
                if (jenisGrosir2 == "kelipatan" && parseFloat(qty) % parseFloat(qtyGrosir2) == 0) {
                    hargaTemp = hargaGrosir2;
                }
                else if (jenisGrosir2 == "minimum" && parseFloat(qty) >= parseFloat(qtyGrosir2)) {
                    hargaTemp = hargaGrosir2;
                }
            }

            subtotal = parseFloat(qty) * parseFloat(hargaTemp);
            txtSubtotal = parseFloat(subtotal).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0});
            var txtHarga = parseFloat(hargaTemp).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0});
            $(this).closest('tr').find(".harga").val(hargaTemp);
            $(this).closest('tr').find(".subtotal").val(subtotal);
            $(this).closest('tr').find(".labelHarga").text(txtHarga);
            $(this).closest('tr').find(".labelSubtotal").text(txtSubtotal);

            footerTable();
        });

        $("#list_item").on('click', '.qtyMinus', function() {
            var qty = $(this).closest('.inputQty').find(".qty").val();

            var flag = $("#flagTrigger").val();
            var mode = $(this).closest('tr').find(".modelSales").val();
            var qtyMode = $(this).closest('.inputQty').find(".qtyModel").val();

            if (mode == "kelipatan") {
                qty = parseFloat(qty) - parseFloat(qtyMode);
            }
            else {
                qty--;
            }

            $(this).closest('.inputQty').find(".qtyInput").autoNumeric('set', qty).trigger("change");
            footerTable();
        });

        $("#list_item").on('click', '.qtyPlus', function() {
            var qty = $(this).closest('.inputQty').find(".qty").val();

            var flag = $("#flagTrigger").val();
            var mode = $(this).closest('tr').find(".modelSales").val();
            var qtyMode = $(this).closest('.inputQty').find(".qtyModel").val();

            if (mode == "kelipatan") {
                qty = parseFloat(qty) + parseFloat(qtyMode);
            }
            else {
                qty++;
            }

            $(this).closest('.inputQty').find(".qtyInput").autoNumeric('set', qty).trigger("change");
            footerTable();
        });

        $("#list_item").on('click', '.hapusItem', function() {
            var idProduct = $(this).closest('tr').find(".idProduct").val();
            var idSatuan = $(this).closest('tr').find(".idSatuan").val();
            var id = idProduct+'_'+idSatuan;
            tableID = tableID.filter(item => item.id !== id);
            $(this).closest("tr").remove();
            footerTable();
        });

        $("#tanggal_sj_picker").on('change', function() {
            $("#tanggal_sj").val($("#tanggal_sj_picker").data('datepicker').getFormattedDate('yyyy-mm-dd'));
        });

        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        $("#btnSubmit").on("click", function(e) {
            Swal.fire({
                title: "Submit Transaksi",
                text: "Apakah data sudah sesuai?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    var count = 0;

                    if(!$('input[name=metode_pembayaran]:checked').length > 0) {
                        count = parseInt(count) + 1;
                        Swal.fire(
                            "Gagal!",
                            "Harap Pilih metode pembayaran terlebih dahulu!",
                            "warning"
                        );
                    }

                    if($('input[name=metode_pembayaran]:checked').val() == "edc") {
                        $(".reqEDC").each(function(){
                            if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                                $(this).closest('.form-group').find('.errEDC').show();
                                count = parseInt(count) + 1;
                            }
                            else {
                                $(this).closest('.form-group').find('.errEDC').hide();
                            }
                        });
                    }

                    if($("#customer option:selected").html() == "WALK IN") {
                        $(".reqCash").each(function(){
                            if ($(this).val() == "" || $(this).children("option:selected").val() == "" || parseFloat($(this).val()) <= 0){
                                $(this).closest('.form-group').find('.errCash').show();
                                count = parseInt(count) + 1;
                            }
                            else if (parseFloat($(this).val()) < parseFloat($("#cash_charge").val())) {
                                $(this).closest('.form-group').find('.errCashSufficient').show();
                                count = parseInt(count) + 1;
                            }
                            else {
                                $(this).closest('.form-group').find('.errCash').hide();
                                $(this).closest('.form-group').find('.errCashSufficient').hide();
                            }
                        });
                    }

                    if($('input[name=metode_pembayaran]:checked').val() == "transfer") {
                        $(".reqTrf").each(function(){
                            if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                                $(this).closest('.form-group').find('.errTrf').show();
                                count = parseInt(count) + 1;
                            }
                            else {
                                $(this).closest('.form-group').find('.errTrf').hide();
                            }
                        });
                    }

                    if (count == 0) {
                        submitTransaction("post");
                        // $("#form_add").off("submit").submit();
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

        $("#btnAdd").on("click", function() {
            var qty = $("#qty").autoNumeric("get");
            var harga = $("#hargaNormal").autoNumeric("get");
            var idProduct = $("#product option:selected").val();
            var txtProduct = $("#product option:selected").html();
            var idSatuan = $("#productUnit option:selected").val();
            var txtSatuan = $("#productUnit option:selected").html();
            var modelSales = $("#model_sales").val();
            var qtyModel = $("#qty_model").val();
            var hargaNormal = $("#hargaTemp").val();
            var jenisGrosir = $("#jenisGrosir").val();
            var hargaGrosir = $("#hargaGrosir").val();
            var qtyGrosir = $("#qtyGrosir").val();
            var jenisGrosir2 = $("#jenisGrosir2").val();
            var hargaGrosir2 = $("#hargaGrosir2").val();
            var qtyGrosir2 = $("#qtyGrosir2").val();
            var txtHarga = parseFloat(harga).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0});
            var txtQty = parseFloat(qty).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0});

            if (idProduct == "") {
                Swal.fire(
                    "Gagal!",
                    "Harap Pilih barang terlebih dahulu!",
                    "warning"
                );
                return false;
            }

            if (idSatuan == "") {
                Swal.fire(
                    "Gagal!",
                    "Harap Pilih satuan terlebih dahulu!",
                    "warning"
                );
                return false;
            }

            if (parseFloat(harga) <= 0 || harga == "") {
                Swal.fire(
                    "Gagal!",
                    "Harga Tidak dapat 0 atau kurang dari 0 !",
                    "warning"
                );
                return false;
            }

            if (parseFloat(qty) <= 0 || qty == "") {
                Swal.fire(
                    "Gagal!",
                    "Qty Tidak dapat 0 atau kurang dari 0 !",
                    "warning"
                );
                return false;
            }

            var idProductCheck = 0;
            var idSatuanCheck = 0;

            $('#list_item tr').find('.idProduct').each(function(index) {
                if (idProduct == $(this).val()) {
                    idProductCheck++;
                }
            });

            $('#list_item tr').find('.idSatuan').each(function(index) {
                if (idSatuan == $(this).val()) {
                    idSatuanCheck++;
                }
            });

            if (idProductCheck > 0 && idSatuanCheck > 0) {
                Swal.fire(
                    "Gagal!",
                    "Barang yang sama sudah terdaftar di daftar belanja!",
                    "warning"
                );
                return false;
            }

            var subtotal = parseFloat(qty) * parseFloat(harga);
            var txtSubtotal = parseFloat(subtotal).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0});

            var txt = "";
            txt += '<tr>';
            txt += '<td class="align-middle">';
            txt += '<span class="text-dark text-hover-primary" id="txtProduct_'+idProduct+'_'+idSatuan+'">'+txtProduct+'</span>';
            txt += '<input type="hidden" class="form-control idProduct" id="idProduct_'+idProduct+'_'+idSatuan+'" name="idProduct_'+idProduct+'_'+idSatuan+'" value="'+idProduct+'" />';
            txt += '</td>';
            txt += '<td class="text-center align-middle">';
            txt += '<span class="text-dark text-hover-primary" id="txtSatuan_'+idProduct+'_'+idSatuan+'">'+txtSatuan+'</span>';
            txt += '<input type="hidden" class="form-control idSatuan" id="idSatuan_'+idProduct+'_'+idSatuan+'" name="idSatuan_'+idProduct+'_'+idSatuan+'" value="'+idSatuan+'" />';
            txt += '</td>';
            txt += '<td class="text-center align-middle">';
            txt += '<div class="inputQty row w-150">';
            txt += '<div class="col-3">';
            txt += '<button type="button" class="btn btn-xs btn-light-success btn-icon mr-2 qtyMinus">';
            txt += '<i class="ki ki-minus icon-xs"></i>';
            txt += '</button>';
            txt += '</div>';
            txt += '<div class="col-6">';
            txt += '<input type="text" class="form-control qtyInput" id="qtyListMask_'+idProduct+'_'+idSatuan+'" name="qtyListMask_'+idProduct+'_'+idSatuan+'" autocomplete="off" data-a-dec="," data-a-sep="." />';
            txt += '<input type="hidden" class="form-control qty" id="qtyList_'+idProduct+'_'+idSatuan+'" name="qtyList_'+idProduct+'_'+idSatuan+'" />';
            txt += '<input type="hidden" class="form-control qtyModel" id="qtyModelList_'+idProduct+'_'+idSatuan+'" name="qtyModelList_'+idProduct+'_'+idSatuan+'" value="'+qtyModel+'" />';
            txt += '<input type="hidden" class="form-control qtyGrosir" id="qtyGrosirList_'+idProduct+'_'+idSatuan+'" name="qtyGrosirList_'+idProduct+'_'+idSatuan+'" value="'+qtyGrosir+'" />';
            txt += '<input type="hidden" class="form-control qtyGrosir2" id="qtyGrosirList2_'+idProduct+'_'+idSatuan+'" name="qtyGrosirList2_'+idProduct+'_'+idSatuan+'" value="'+qtyGrosir2+'" />';
            txt += '</div>';
            txt += '<div class="col-3">';
            txt += '<button type="button" class="btn btn-xs btn-light-success btn-icon qtyPlus">';
            txt += '<i class="ki ki-plus icon-xs"></i>';
            txt += '</button>';
            txt += '</div>';
            txt += '</div>';
            txt += '</td>';
            txt += '<td class="text-right align-middle font-weight-bolder font-size-h5">';
            txt += '<span class="text-dark text-hover-primary labelHarga">'+txtHarga+'</span>';
            txt += '<input type="hidden" class="form-control harga" id="hargaList_'+idProduct+'_'+idSatuan+'" name="hargaList_'+idProduct+'_'+idSatuan+'" value="'+harga+'" />';
            txt += '<input type="hidden" class="form-control modelSales" id="modelSalesList_'+idProduct+'_'+idSatuan+'" name="modelSalesList_'+idProduct+'_'+idSatuan+'" value="'+modelSales+'" />';
            txt += '<input type="hidden" class="form-control jenisGrosir" id="jenisGrosirList_'+idProduct+'_'+idSatuan+'" name="jenisGrosirList_'+idProduct+'_'+idSatuan+'" value="'+jenisGrosir+'" />';
            txt += '<input type="hidden" class="form-control hargaGrosir" id="hargaGrosirList_'+idProduct+'_'+idSatuan+'" name="hargaGrosirList_'+idProduct+'_'+idSatuan+'" value="'+hargaGrosir+'" />';
            txt += '<input type="hidden" class="form-control jenisGrosir2" id="jenisGrosirList2_'+idProduct+'_'+idSatuan+'" name="jenisGrosirList2_'+idProduct+'_'+idSatuan+'" value="'+jenisGrosir2+'" />';
            txt += '<input type="hidden" class="form-control hargaGrosir2" id="hargaGrosirList2_'+idProduct+'_'+idSatuan+'" name="hargaGrosirList2_'+idProduct+'_'+idSatuan+'" value="'+hargaGrosir2+'" />';
            txt += '<input type="hidden" class="form-control hargaTemp" id="hargaTempList_'+idProduct+'_'+idSatuan+'" name="hargaTempList_'+idProduct+'_'+idSatuan+'" value="'+hargaNormal+'" />';
            txt += '</td>';
            txt += '<td class="text-right align-middle font-weight-bolder font-size-h5">';
            txt += '<span class="text-dark text-hover-primary labelSubtotal">'+txtSubtotal+'</span>';
            txt += '<input type="hidden" class="form-control subtotal" id="subtotalList_'+idProduct+'_'+idSatuan+'" name="subtotalList_'+idProduct+'_'+idSatuan+'" value="'+subtotal+'" />';
            txt += '</td>';
            txt += '<td class="text-right align-middle">';
            txt += '<button type="button" class="btn btn-lg btn-danger btn-icon hapusItem">';
            txt += '<i class="la la-trash"></i>';
            txt += '</button>';
            txt += '</td>';
            txt += '</tr>';

            // console.log(txt);

            $("#list_item tbody tr:last").before(txt);

            $('#qtyListMask_'+idProduct+'_'+idSatuan).autoNumeric('init', {mDec: '0'});
            $('#qtyListMask_'+idProduct+'_'+idSatuan).autoNumeric("set", qty).trigger("change");
            $("#qty").val("");
            $("#hargaNormal").val("");
            $("#product").val("").trigger("change");
            $("#productUnit").val("").trigger("change");
            $("#qtyGrosir").val("");
            $("#hargaGrosir").val("");
            $("#hargaTemp").val("");

            tableID.push({
                id:idProduct+'_'+idSatuan,
                idProduct:idProduct,
                idSatuan:idSatuan,
            });

            footerTable();
        });

        $("#product").on("change", function() {
            //getdataItem
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/Cashier/GetSatuan",
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
                                text:result[i].nama_satuan.toUpperCase()
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
                url: "/Cashier/GetDataItem",
                method: 'POST',
                data: {
                    id_product: $("#product option:selected").val(),
                    id_satuan: $(this).val(),
                },
                success: function(result){
                    if (result.length > 0) {
                        var outsQty = result[0].qty_outstanding;
                        $("#model_sales").val(result[0].mode);
                        $("#qty_model").val(result[0].qty_mode);
                        $("#qtyGrosir").val(result[0].qty_grosir);
                        $("#jenisGrosir").val(result[0].jenis_grosir);
                        $("#hargaGrosir").val(result[0].harga_grosir);
                        $("#qtyGrosir2").val(result[0].qty_grosir_2);
                        $("#jenisGrosir2").val(result[0].jenis_grosir_2);
                        $("#hargaGrosir2").val(result[0].harga_grosir_2);
                        $("#hargaTemp").val(result[0].harga_jual);
                        $("#hargaNormal").autoNumeric('set', result[0].harga_jual).trigger("change");
                        $("#qty").autoNumeric('set', result[0].qty_mode).trigger("change");
                        $("#btnAdd").prop("disabled", false);
                        // if (result[0].mode == "kelipatan") {
                        //     var hargaKelipatan = parseFloat(result[0].harga_jual) * parseFloat(result[0].qty_mode);
                        //     $("#hargaNormal").autoNumeric('set', hargaKelipatan).trigger("change");
                        //     $("#qty").autoNumeric('set', result[0].qty_mode).trigger("change");
                        // }
                        // else {
                        //     $("#qty").autoNumeric('set', result[0].qty_mode).trigger("change");
                        //     $("#hargaNormal").autoNumeric('set', result[0].harga_jual).trigger("change");
                        // }
                    }
                    else {
                        $("#hargaNormal").val("");
                        $("#model_sales").val("");
                        $("#qty_model").val("");
                        $("#qtyGrosir").val("");
                        $("#jenisGrosir").val("");
                        $("#hargaGrosir").val("");
                        $("#qtyGrosir2").val("");
                        $("#jenisGrosir2").val("");
                        $("#hargaGrosir2").val("");
                        $("#hargaTemp").val("");
                    }

                    $("#qty").focus();
                }
            });

        });

         $("#qty").on("change", function() {
            var qty = $(this).autoNumeric("get");
            var mode = $("#model_sales").val();
            var qtyMode = $("#qty_model").val();
            var hargaNormal = $("#hargaTemp").val();
            var jenisGrosir = $("#jenisGrosir").val();
            var hargaGrosir = $("#hargaGrosir").val();
            var qtyGrosir = $("#qtyGrosir").val();
            var jenisGrosir2 = $("#jenisGrosir2").val();
            var hargaGrosir2 = $("#hargaGrosir2").val();
            var qtyGrosir2 = $("#qtyGrosir2").val();
            var harga = hargaNormal;
            if (mode == "kelipatan") {
                if (qty % qtyMode != 0) {
                    Swal.fire(
                        "Peringatan!",
                        "Item ini hanya dapat dijual dengan qty kelipatan " + qtyMode + "!",
                        "warning"
                    );

                    $("#btnAdd").prop("disabled", true);
                    return false;
                }
                else {
                    $("#btnAdd").prop("disabled", false);
                }
            }

            if (jenisGrosir != "") {
                if (jenisGrosir == "kelipatan" && parseFloat(qty) % parseFloat(qtyGrosir) == 0) {
                    harga = hargaGrosir;
                }
                else if (jenisGrosir == "minimum" && parseFloat(qty) >= parseFloat(qtyGrosir)) {
                    harga = hargaGrosir;
                }
            }

            if (jenisGrosir2 != "") {
                if (jenisGrosir2 == "kelipatan" && parseFloat(qty) % parseFloat(qtyGrosir2) == 0) {
                    harga = hargaGrosir2;
                }
                else if (jenisGrosir2 == "minimum" && parseFloat(qty) >= parseFloat(qtyGrosir2)) {
                    harga = hargaGrosir2;
                }
            }

            $("#hargaNormal").autoNumeric('set', harga).trigger("change");
        });

        function footerTable() {
            var totalQty = 0;
            var total = 0;
            var rowCount = $('#list_item tbody tr:not(:last)').length;
            // console.log("rowCount:"+rowCount);
            if (rowCount > 0) {

                $('#list_item tr').find('.subtotal').each(function(index) {
                    total = parseFloat(total) + parseFloat($(this).val());
                });

                $('#list_item tr').find('.qty').each(function(index) {
                    totalQty = parseFloat(totalQty) + parseFloat($(this).val());
                });

                // console.log("subtotal:"+total);
                // console.log("qty:"+totalQty);
                var txtTotalQty = parseFloat(totalQty).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0});
                var txtSubtotal = parseFloat(total).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0});

                $("#subtotal").val(total);
                $("#totalQty").val(totalQty);
                $("#labelSubtotalFooter").text(txtSubtotal);
                $("#labelTotalQty").text(txtTotalQty);
            }
            else {
                $("#subtotal").val(0);
                $("#totalQty").val(0);
                $("#labelSubtotalFooter").text(0);
                $("#labelTotalQty").text(0);
            }
        }

        $('input[name=metode_pembayaran]').on('change', function() {
			var val = $(this).val();
			if (val == "edc") {
				showHidePaymentDetails("show", "carddetail");
                showHidePaymentDetails("hide", "cashdetail");
                showHidePaymentDetails("hide", "rekeningdetail");
			}
			else if (val == "qris") {
				showHidePaymentDetails("hide", "carddetail");
                showHidePaymentDetails("hide", "cashdetail");
                showHidePaymentDetails("hide", "rekeningdetail");
			}
            else if (val == "transfer") {
				showHidePaymentDetails("hide", "carddetail");
                showHidePaymentDetails("hide", "cashdetail");
                showHidePaymentDetails("show", "rekeningdetail");
			}
            else if (val == "cash") {
				showHidePaymentDetails("hide", "carddetail");
                showHidePaymentDetails("show", "cashdetail");
                showHidePaymentDetails("hide", "rekeningdetail");
			}
            else {
                showHidePaymentDetails("hide", "carddetail");
                showHidePaymentDetails("hide", "cashdetail");
                showHidePaymentDetails("hide", "rekeningdetail");
            }
		});

        function showHidePaymentDetails(action, className) {
            if (action == "hide") {
                $("."+className).hide();
            }
            else if (action == "show") {
                $("."+className).show();
            }
        }

        function cloneShoppingList() {
            $("#customer_text").val($("#customer option:selected").html());
            $("#customer_id").val($("#customer option:selected").val());

            if (tableID.length > 0) {
                for (var i = 0;i < tableID.length;i++) {
                    tableData.push({
                        id:tableID[i].idProduct+'_'+tableID[i].idSatuan,
                        idProduct:tableID[i].idProduct,
                        idSatuan:tableID[i].idSatuan,
                        txtProduct:$("#txtProduct_"+tableID[i].idProduct+'_'+tableID[i].idSatuan).text(),
                        txtSatuan:$("#txtSatuan_"+tableID[i].idProduct+'_'+tableID[i].idSatuan).text(),
                        harga:$("#hargaList_"+tableID[i].idProduct+'_'+tableID[i].idSatuan).val(),
                        qty:$("#qtyList_"+tableID[i].idProduct+'_'+tableID[i].idSatuan).val(),
                        subtotal:$("#subtotalList_"+tableID[i].idProduct+'_'+tableID[i].idSatuan).val(),
                    });
                }
            }

            $("#item_display tbody tr:not(:last)").remove();

            var txtTableDisplay = "";
            var grandTotal = 0;

            if (tableData.length > 0) {
                for (var i = 0;i < tableData.length;i++) {

                    txtTableDisplay += '<tr class="font-weight-boldest">';
                    txtTableDisplay += '<td class="text-center">';
                    txtTableDisplay += parseInt(i) + 1;
                    txtTableDisplay += '</td>';

                    txtTableDisplay += '<td>';
                    txtTableDisplay += tableData[i].txtProduct;
                    txtTableDisplay += '</td>';

                    txtTableDisplay += '<td>';
                    txtTableDisplay += tableData[i].txtSatuan;
                    txtTableDisplay += '</td>';

                    txtTableDisplay += '<td class="text-right">';
                    txtTableDisplay += parseFloat(tableData[i].qty).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0});
                    txtTableDisplay += '</td>';

                    txtTableDisplay += '<td class="text-right">';
                    txtTableDisplay += parseFloat(tableData[i].harga).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0});
                    txtTableDisplay += '</td>';

                    txtTableDisplay += '<td class="text-right">';
                    txtTableDisplay += parseFloat(tableData[i].subtotal).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0});
                    txtTableDisplay += '</td>';

                    txtTableDisplay += '</tr>';

                    grandTotal = parseFloat(grandTotal) + parseFloat(tableData[i].subtotal);
                }

                $("#item_display tbody tr:last").before(txtTableDisplay);

                var txtGrandTotal = parseFloat(grandTotal).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0});

                $("#grandTotalDisplay").text(txtGrandTotal);
                $("#cash_charge_mask").val(txtGrandTotal);
                $("#cash_charge").val(grandTotal);
            }
        }

        $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
            var tabName = e.target.id;
            var prevTab = e.relatedTarget.id;
            switch (tabName) {

                case "tab1" : {
                    $("#btnSubmit").hide();
                    $("#btnShop").hide();
                    $("#btnCancel").show();
                    $("#btnPembayaran").show();
                    tableData.length = 0;
                    break;
                }

                case "tab2" : {
                    var rowCount = $('#list_item >tbody >tr').length;
                    if (rowCount == 1) {
                        Swal.fire(
                            "Peringatan!",
                            "Harap Tambahkan Minimum 1 Item Belanja!.",
                            "warning"
                        );
                        e.preventDefault();
                    }
                    else if ($("#customer option:selected").val() == "") {
                        Swal.fire(
                            "Peringatan!",
                            "Harap Pilih Customer Terlebih Dahulu!.",
                            "warning"
                        );
                        e.preventDefault();
                    }
                    else {
                        $("#btnSubmit").show();
                        $("#btnShop").show();
                        $("#btnCancel").hide();
                        $("#btnPembayaran").hide();
                        cloneShoppingList();
                    }
                    break;
                }
            }
	    });

        $("#btnShop").on("click", function() {
            $("#tab1").trigger("click");
        });

        $("#btnPembayaran").on("click", function() {
            $("#tab2").trigger("click");
        });

        $("#btnCancel").on('click', function(e) {
            Swal.fire({
                title: "Keluar?",
                text: "Apakah anda ingin Kembali ke halaman penjualan kasir?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    window.location.href = "{{ url('/Cashier') }}";
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
	    });

        $("#btnReset").on('click', function(e) {
            Swal.fire({
                title: "Mulai Baru?",
                text: "Apakah anda ingin membuat transaksi baru?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    window.location.reload();
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
	    });

        $("#cash_payment_mask").on('change', function() {
            $("#cash_payment").val($("#cash_payment_mask").autoNumeric("get"));

            var charge = $("#cash_charge").val();
            var payment = $("#cash_payment").val();
            var change = -1 * (parseFloat(charge) - parseFloat(payment));

            var txtChange = parseFloat(change).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0});

            $("#cash_change_mask").val(txtChange);
            $("#cash_change").val(change);

        });

        function cleanUp() {
            tableData.length = 0;
            tableID.length = 0;
            $("#item_display tbody tr:not(:last)").remove();
            $("#list_item tbody tr:not(:last)").remove();
            $('input[name="metode_pembayaran"]').prop('checked', false);

            $("#customer").val("").trigger("change");
            $("#rekening").val("").trigger("change");

            $("#cash_charge_mask").autoNumeric('set', 0);
            $("#cash_payment_mask").autoNumeric('set', 0);
            $("#cash_change_mask").autoNumeric('set', 0);

            $("#cash_charge").val(0);
            $("#cash_payment").val(0);
            $("#cash_change").val(0);

            $("#ccnumber").val("");
            $("#ccname").val("");
            $("#ccmonth").val("");
            $("#ccyear").val("");

            showHidePaymentDetails("hide", "carddetail");
            showHidePaymentDetails("hide", "cashdetail");
            showHidePaymentDetails("hide", "rekeningdetail");

            footerTable();
        }

        $("#customer").on('change', function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/Cashier/GetCustomerDebt",
                method: 'POST',
                dataType : 'json',
                data: {
                    idCustomer : $(this).val(),
                },
                success: function(result) {
                    if (JSON.stringify(result) === '{}') {
                        console.log('empty');
                        var txtDebt = parseFloat(0).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0});

                        $("#prev_debt_mask").val(txtDebt);
                        $("#prev_debt").val(0);
                        $(".debt").hide();
                    }
                    else {
                        console.log('not empty');
                        var txtDebt = parseFloat(result.debt_amount).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0});

                        $("#prev_debt_mask").val(txtDebt);
                        $("#prev_debt").val(result.debt_amount);
                        $(".debt").show();
                    }

                }
            });
        });

        function submitTransaction(action) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/Cashier/StoreTransaction",
                method: 'POST',
                dataType : 'json',
                data: {
                    tableData : JSON.stringify(tableData),
                    idCustomer : $("#customer_id").val(),
                    action : action,
                    cashPayment : $("#cash_payment").val(),
                    cashChange : $("#cash_change").val(),
                    ccNumber : $("#ccnumber").val(),
                    ccName : $("#ccname").val(),
                    ccMonth : $("#ccmonth").val(),
                    ccYear : $("#ccyear").val(),
                    rekening : $("#rekening option:selected").val(),
                    debt : $("#prev_debt").val(),
                    metodeBayar : $('input[name=metode_pembayaran]:checked').val(),
                },
                success: function(result){
                    if (result.message == "success") {
                        Swal.fire(
                            "Sukses!",
                            "Transaksi Berhasil disimpan!.",
                            "success"
                        )
                        $("#idTransaction").val(result.id);

                        var routePrint = "{{route('Cashier.Cetak', 'idTransaction')}}";
                        var url = routePrint.replace('idTransaction', result.id);
                        $('#btnCetak').attr('href', url);
                        $("#btnSubmit").hide();
                        $("#btnReset").show();
                        $("#btnCetak").show();
                        // console.log(result);

                    }
                    else if (result == "failDuplicate") {
                        Swal.fire(
                            "Gagal!",
                            "Barang ini sudah tersedia pada Daftar Penjualan Barang !",
                            "warning"
                        )
                    }
                }
            });
        }

        $("#btnCetak").on("click", function(e) {
            //getdataItem
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/Cashier/CetakDirect",
                method: 'POST',
                data: {
                    idStruk: $("#idTransaction").val(),
                },
                success: function(result){
                    console.log(result);
                }
            });
        });

    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
