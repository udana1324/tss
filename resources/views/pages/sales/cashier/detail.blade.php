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
                                        <span class="nav-text">Detail Transaksi Belanja</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <form action="{{ route('Cashier.Posting', $dataTransaction->id) }}" class="form-horizontal" id="form_add" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="tab_pane_1" role="tabpanel" aria-labelledby="tab_pane_2">
                                        <div class="card card-custom">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-xl-6">
                                                        <div class="text-dark-50 line-height-lg">
                                                            <div class="table-responsive">
                                                            {{-- <div class="table-responsive overflow-auto" style="height: 475px;"> --}}
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
                                                            <input type="text" id="customer_text" value="{{ucwords($dataCustomer->nama_customer)}}" class="form-control" readonly>
                                                            <input type="hidden" id="customer_id" readonly>
                                                        </div>

                                                        <div class="form-group">
                                                            <label>Tanggal Transaksi :</label>
                                                            <input type="text" id="tgl_transaksi" value="{{\Carbon\Carbon::parse($dataTransaction->tanggal_penjualan)->locale('id')->isoformat('DD MMMM Y HH:mm:ss')}}" class="form-control" readonly>
                                                        </div>

                                                        <div class="form-group">
                                                            <label>Metode Pembayaran :</label>
                                                            <div class="radio-inline">
                                                                <label class="radio">
                                                                <input type="radio" id="edc" name="metode_pembayaran" value="edc" {{ $dataTransaction->metode_pembayaran === "edc" ? "checked" : "" }} disabled="disabled" />
                                                                <span></span>EDC (BCA)</label>
                                                                <label class="radio">
                                                                <input type="radio" id="qris" name="metode_pembayaran" value="qris" {{ $dataTransaction->metode_pembayaran === "qris" ? "checked" : "" }} disabled="disabled" />
                                                                <span></span>QRIS</label>
                                                                <label class="radio">
                                                                <input type="radio" id="transfer" name="metode_pembayaran" value="transfer" {{ $dataTransaction->metode_pembayaran === "transfer" ? "checked" : "" }} disabled="disabled" />
                                                                <span></span>Transfer</label>
                                                                <label class="radio">
                                                                <input type="radio" id="cash" name="metode_pembayaran" value="cash" {{ $dataTransaction->metode_pembayaran === "cash" ? "checked" : "" }} disabled="disabled" />
                                                                <span></span>Cash/Tunai</label>
                                                            </div>
                                                        </div>

                                                        @if ($dataTransaction->metode_pembayaran == "transfer")
                                                        <div class="form-group row">
                                                            <label class="col-lg-3 col-form-label">Rekening Pembayaran :</label>
                                                            <div class="col-lg-9">
                                                                <input type="text" id="rekening" value="{{strtoupper($dataRekening->nama_bank)}} - {{strtoupper($dataRekening->nomor_rekening)}} A/N {{strtoupper($dataRekening->atas_nama)}}" readonly>
                                                                <span class="form-text text-danger errTrf" id="errTrf" style="display:none;">*Harap pilih rekening terlebih dahulu!</span>
                                                            </div>
                                                        </div>
                                                        @endif

                                                        @if ($dataTransaction->metode_pembayaran == "cash")
                                                        <!--begin::Input-->
                                                        <div class="form-group cashdetail">
                                                            <label>Nominal Belanja</label>
                                                            <input type="text" id="cash_charge_mask" autocomplete="off" data-a-dec="," data-a-sep="." value="{{number_format($dataTransaction->nominal_total, 0, ',', '.')}}" class="form-control text-right" disabled>
                                                            <input type="hidden" id="cash_charge" readonly>
                                                        </div>
                                                        <!--end::Input-->
                                                        @endif
                                                        <!--begin::Input-->
                                                        <div class="form-group cashdetail">
                                                            <label>Nominal Bayar</label>
                                                            <input type="text" id="cash_payment_mask" autocomplete="off" data-a-dec="," data-a-sep="." value="{{number_format($dataTransaction->nominal_pembayaran, 0, ',', '.')}}" class="form-control text-right" disabled>
                                                            <input type="hidden" class="reqCash" id="cash_payment" readonly>
                                                            <span class="form-text text-danger errCash" id="errCash" style="display:none;">*Harap input nominal pembayaran terlebih dahulu!</span>
                                                            <span class="form-text text-danger errCashSufficient" id="errCashSufficient" style="display:none;">*Nominal pembayaran kurang dari total belanja!</span>
                                                        </div>
                                                        <!--end::Input-->
                                                        @if ($dataTransaction->metode_pembayaran == "cash")
                                                        <!--begin::Input-->
                                                        <div class="form-group cashdetail">
                                                            <label>Kembalian</label>
                                                            <input type="text" id="cash_change_mask" autocomplete="off" data-a-dec="," data-a-sep="." value="{{number_format($dataTransaction->nominal_change, 0, ',', '.')}}" class="form-control text-right" disabled>
                                                            <input type="hidden" id="cash_change" readonly>
                                                        </div>
                                                        <!--end::Input-->
                                                        @endif

                                                        {{-- @if ($dataTransaction->metode_pembayaran == "edc")
                                                        <!--begin::Input-->
                                                        <div class="form-group carddetail">
                                                            <label>No Kartu</label>
                                                            <input type="text" class="form-control form-control-lg reqEDC" id="ccnumber" name="ccnumber" value="{{$dataTransaction->cc_number}}" placeholder="No. Kartu" />
                                                            <span class="form-text text-danger errEDC" style="display:none;">Harap input No. Kartu terlebih dahulu!</span>
                                                        </div>
                                                        <!--end::Input-->
                                                        <!--begin::Input-->
                                                        <div class="form-group carddetail">
                                                            <label>Nama Pada Kartu</label>
                                                            <input type="text" class="form-control form-control-lg reqEDC" id="ccname" name="ccname" value="{{strtoupper($dataTransaction->cc_name)}}" placeholder="Nama Pada Kartu" />
                                                            <span class="form-text text-danger errEDC" style="display:none;">Harap input Nama pada Kartu terlebih dahulu!</span>
                                                        </div>
                                                        <!--end::Input-->
                                                            <!--begin::Input-->
                                                        <div class="form-group carddetail">
                                                            <label>Bulan Expiry Kartu</label>
                                                            <input type="number" class="form-control form-control-lg reqEDC" min="01" max="12" maxlength="2" id="ccmonth" value="{{$dataTransaction->cc_month}}" name="ccmonth" placeholder="Bulan Expiry Kartu" />
                                                            <span class="form-text text-danger errEDC" style="display:none;">Harap input Bulan Expiry Kartu terlebih dahulu!</span>
                                                        </div>
                                                        <!--end::Input-->
                                                        <!--begin::Input-->
                                                        <div class="form-group carddetail">
                                                            <label>Tahun Expiry Kartu</label>
                                                            <input type="number" class="form-control form-control-lg reqEDC" maxlength="4" name="ccyear" id="ccyear" value="{{$dataTransaction->cc_year}}" placeholder="Tahun Expiry Kartu" />
                                                            <span class="form-text text-danger errEDC" style="display:none;">Harap input Tahun Expiry Kartu terlebih dahulu!</span>
                                                        </div>
                                                        <!--end::Input-->
                                                        @endif --}}

                                                        @if ($group == "admin" || $group == "super_admin")
                                                        <div class="form-group">
                                                            <label>Nama Kasir :</label>
                                                            <input type="text" id="kasir" value="{{ucwords($dataUser->user_name)}}" class="form-control" readonly>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="card-footer bg-white d-sm-flex justify-content-sm-between align-items-sm-center">
                                <input type="hidden" id="submit_action" name="submit_action" class="form-control" readonly>
                                <div class="mt-2 mt-sm-0">
                                    <button type="button" id="btnCancel" class="btn btn-light-danger font-weight-bold mr-2">Keluar</button>
                                </div>

                                <div class="mt-2 mt-sm-0">
                                    <a type="button" class="btn btn-outline-primary" href='{{route('Cashier.Cetak', $dataTransaction->id)}}' target="_blank">Cetak Struk<i class="fas fa-print ml-2"></i></a>
                                    @if(($group == "cashier"  && $dataTransaction->flag_request_revisi != 1))
                                        <button type="button" class="btn btn-warning mt-2 mt-sm-0 btnSubmit" id="btn_req_revisi" value="request_revisi">Request Revisi<i class="fas fa-file-signature ml-2"></i></button>
                                    @endif
                                    @if(($group == "admin" || $group == "super_admin")  && $dataTransaction->flag_request_revisi == 1 && $dataTransaction->status_sales == "request_revisi")
                                        <button type="button" class="btn btn-success mt-2 mt-sm-0 btnSubmit" id="btn_app_revisi" value="approve_revisi">Approve Revisi<i class="fas fa-file-signature ml-2"></i></button>
                                    @endif
                                    @if(($hakAkses->revisi == "Y" && ($group == "admin" || $group == "super_admin")) || ($dataTransaction->flag_request_revisi == 1 && $dataTransaction->flag_approved == 1 ))
                                        <button type="button" class="btn btn-warning mt-2 mt-sm-0 btnSubmit" id="btn_revisi" value="revisi">Revisi<i class="fas fa-file-signature ml-2"></i></button>
                                    @endif
                                </div>
                            </div>
                        </form>
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
            cloneShoppingList();

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

                    if($('input[name=metode_pembayaran]:checked').val() == "cash") {
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


        function cloneShoppingList() {

            // $("#item_display tbody tr:not(:last)").remove();

            var txtTableDisplay = "";
            var grandTotal = 0;
            var tableData = @json($dataDetail);

            if (tableData.length > 0) {
                for (var i = 0;i < tableData.length;i++) {

                    txtTableDisplay += '<tr class="font-weight-boldest">';
                    txtTableDisplay += '<td class="text-center">';
                    txtTableDisplay += parseInt(i) + 1;
                    txtTableDisplay += '</td>';

                    txtTableDisplay += '<td>';
                    txtTableDisplay += ucwords(tableData[i].nama_item);
                    txtTableDisplay += '</td>';

                    txtTableDisplay += '<td>';
                    txtTableDisplay += ucwords(tableData[i].nama_satuan);
                    txtTableDisplay += '</td>';

                    txtTableDisplay += '<td class="text-right">';
                    txtTableDisplay += parseFloat(tableData[i].qty_item).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0});
                    txtTableDisplay += '</td>';

                    txtTableDisplay += '<td class="text-right">';
                    txtTableDisplay += parseFloat(tableData[i].harga_jual).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0});
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
            }
        }

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

            footerTable();
        }

        $(".btnSubmit").on("click", function(e){
            var btn = $(this).val();
            $("#submit_action").val(btn);
            btn = btn.replace("_", " ");
            Swal.fire({
                title: ucwords(btn) + " Transaksi Penjualan?",
                text: "Apakah yakin ingin melakukan " + ucwords(btn) +" Transaksi {{strtoupper($dataTransaction->no_ref)}}?",
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
