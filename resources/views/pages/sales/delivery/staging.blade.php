@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
                <form action="{{ route('Delivery.PostStaging', $dataDlv->id) }}" class="form-horizontal" id="form_add" method="POST">
                {{ csrf_field() }}
                    <div class="card card-custom">
                        <div class="card-header bg-primary header-elements-sm-inline">
                            <h5 class="card-title text-white">Detail Pengaturan Alokasi Pengiriman</h5>
                        </div>

                        <div class="card-body" style="background-color: rgba(245, 245, 245, 0.4);">
                            <!-- Basic initialization -->
                            <div class="d-flex flex-column flex-xl-row">
                                <div class="flex-column flex-lg-row-auto w-xl-400px">
                                    <!--begin::Card-->
                                    <div class="card mb-5">
                                        <div class="card-body">
                                            <div class="pb-5">
                                                <div class="font-size-h6 font-weight-bolder">Kode Pengiriman :</div>
                                                <div class="font-size-lg text-dark-50">
                                                    {{strtoupper($dataDlv->kode_pengiriman)}}
                                                    @if ($dataDlv->status_pengiriman == "draft")
                                                    <span class="label label-primary label-inline ml-1" id="status_kirim">Menunggu Approval</span>
                                                    @elseif ($dataDlv->status_pengiriman == "posted" && $dataDlv->flag_terkirim == "0")
                                                    <span class="label label-primary label-inline ml-1" id="status_kirim">Dalam Proses Pengiriman</span>
                                                    @elseif ($dataDlv->status_pengiriman == "posted" && $dataDlv->flag_terkirim == "1")
                                                    <span class="label label-primary label-inline ml-1" id="status_kirim">Terkirim oleh {{ucwords($dataDlv->updated_by)}}. (Diterima Oleh : {{$dataDlv->diterima_oleh}})</span>
                                                    @elseif($dataDlv->flag_terkirim == "1" && $dataDlv->flag_invoiced == "1")
                                                    <span class="label label-primary label-inline ml-1" id="status_kirim">Sudah dibuat Tagihan</span>
                                                    @endif

                                                </div>

                                                <div class="font-size-h6 font-weight-bolder mt-5">Status Revisi :</div>
                                                <div class="font-size-lg text-dark-50">{{ $dataDlv->flag_revisi == "1" ? "Sudah revisi" : "Belum ada revisi" }}</div>

                                                <div class="font-size-h6 font-weight-bolder mt-5">Pelanggan / Customer :</div>
                                                <div class="font-size-lg text-dark-50">{{strtoupper($dataDlv->nama_customer)}}</div>

                                                <div class="font-size-h6 font-weight-bolder mt-5">Alamat Pelanggan / Customer :</div>
                                                <div class="font-size-lg text-dark-50">{{strtoupper($dataAlamat->alamat_customer)}}</div>

                                                <div class="font-size-h6 font-weight-bolder mt-5">No. Sales Order :</div>
                                                <div class="font-size-lg text-dark-50">{{strtoupper($dataDlv->no_so)}}</div>

                                                <div class="font-size-h6 font-weight-bolder mt-5">PO Pelanggan :</div>
                                                <div class="font-size-lg text-dark-50">{{strtoupper($dataDlv->no_po_customer)}}</div>

                                                <div class="font-size-h6 font-weight-bolder mt-5">Tanggal Surat jalan :</div>
                                                <div class="font-size-lg text-dark-50">{{\Carbon\Carbon::parse($dataDlv->tanggal_sj)->format('d F Y')}}</div>

                                                <div class="font-size-h5 font-weight-bolder mt-5">Metode Pengiriman :</div>
                                                @if($dataDlv->metode_kirim == "delivery")
                                                <div class="font-size-h6 text-dark-50">Kirim</div>
                                                @elseif($dataDlv->metode_kirim == "pickup")
                                                <div class="font-size-h6 text-dark-50">Ambil Sendiri</div>
                                                @elseif($dataDlv->metode_kirim == "ekspedisi")
                                                <div class="font-size-h6 text-dark-50">Ekspedisi</div>
                                                @endif

                                                <div class="font-size-h5 font-weight-bolder mt-5">Keterangan Pengiriman Barang :</div>
                                                <div class="font-size-h6 text-dark-50">
                                                    <ul>
                                                        @foreach($dataTerms as $terms)
                                                            <li>{{$terms->terms_and_cond}} </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Card-->
                                </div>

                                <div class="flex-lg-row-fluid ml-xl-10">
                                    <!--begin::Card-->
                                    @foreach ($detailDlv as $details)
                                    <div class="card card-custom mb-5 headerCollapse" data-card="true" id="kt_card_{{$details->id}}">
                                        <div class="card-header pr-0">
                                            <span href="#" style="width:100%" class="btn" data-card-tool="toggle" data-toggle="tooltip">
                                                <div class="row">
                                                    <div class="card-title col-11 text-left">
                                                        <label class="font-size-h4 font-weight-bolder text-dark mb-0">{{ $details->value_spesifikasi != null ? '('.$details->value_spesifikasi.')' : "" }}{{strtoupper($details->kode_item)}} - {{strtoupper($details->nama_item)}}</label>
                                                    </div>

                                                    <div class="card-toolbar headerItem">
                                                        <a href="#" class="btn btn-icon btn-sm btn-hover-light-primary mr-1" data-card-tool="toggle" data-toggle="tooltip" data-placement="top" title="" data-original-title="Rincian">
                                                            <i class="ki ki-arrow-down icon-nm"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </span>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-xl-4 mb-5">
                                                    <div class="font-size-h6 font-weight-bolder">Jumlah order :</div>
                                                    <div class="font-size-lg text-dark-50">{{number_format($details->qty_order , 2, ',', '.')}} {{$details->nama_satuan}}</div>
                                                </div>
                                                <div class="col-xl-4 mb-5">
                                                    <div class="font-size-h6 font-weight-bolder">Sisa order (Outstanding) :</div>
                                                    <div class="font-size-lg text-dark-50">{{number_format($details->qty_outstanding , 2, ',', '.')}} {{$details->nama_satuan}}</div>
                                                </div>
                                                <div class="col-xl-4 mb-5">
                                                    <div class="font-size-h6 font-weight-bolder">Jumlah Pengiriman :</div>
                                                    <div class="font-size-lg text-dark-50">{{number_format($details->qty_item , 2, ',', '.')}} {{$details->nama_satuan}}</div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-xl-12">
                                                    <div class="font-size-h6 font-weight-bolder">Keterangan :</div>
                                                    <div class="font-size-lg text-dark-50">
                                                        <input type="hidden" class="form-control" name="isi2[txt_{{$details->id}}][id_detail]" value="{{$details->id}}">
                                                        <input type="text" class="form-control" name="isi2[txt_{{$details->id}}][keterangan]" value="{{$details->keterangan}}">
                                                        {{-- <input type="text" class="form-control" name="isi2[txt_{{$details->id}}][keterangan]" value="{{$details->keterangan == null ? $details->keterangan : number_format($details->ktrg , 0, ',', '.')." Dus x ".number_format($details->qty_per_dus, 2, ',', '.')." ".$details->nama_satuan }}"> --}}
                                                    </div>
                                                </div>
                                            </div>

                                            @if($dataDlv->status_pengiriman == "draft")
                                            <div class="row">
                                                <div class="col-12 mt-5">
                                                    <div class="row mt-1 dataAlokasi">
                                                        <div class="col-lg-4">
                                                            <div class="font-size-lg font-weight-bolder">Gudang :</div>
                                                            <input type="hidden" class="form-control idDetail" id="idDetail_{{$details->id}}" name="idDetail_{{$details->id}}" value="{{$details->id}}" />
                                                            <input type="hidden" class="form-control nmSatuan" id="nmSatuan_{{$details->id_item}}" name="idDetails_{{$details->id_item}}" value="{{$details->nama_satuan}}" />
                                                            <input type="hidden" class="form-control idItem" id="idItem_{{$details->id_item}}" name="idItem_{{$details->id_item}}" value="{{$details->id_item}}" />
                                                            <input type="hidden" class="form-control idSatuan" id="idSatuan_{{$details->id_item}}" name="idSatuan_{{$details->id_item}}" value="{{$details->id_satuan}}" />
                                                            <input type="hidden" class="form-control jmlKirim" id="jmlKirim_{{$details->id_item}}" name="jmlKirim_{{$details->id_item}}" value="{{$details->qty_item}}" />
                                                            <select class="form-control select2 indexOption" id="index_{{$details->id_item}}" name="index_{{$details->id_item}}" style="width:100%;">
                                                                <option label="Label"></option>
                                                                {{-- @foreach($listIndex as $index)
                                                                <option value="{{$index['id']}}">{{strtoupper($index['nama_index'])}}</option>
                                                                @endforeach --}}
                                                            </select>
                                                            <span class="form-text text-danger errItem" style="display:none;">*Harap pilih gudang terlebih dahulu!</span>
                                                        </div>
                                                        <div class="col-lg-3 stockAlokasi">
                                                            <div class="font-size-lg font-weight-bolder">Stok :</div>
                                                            <input type="text" class="form-control form-control-solid" id="jmlStokMask_{{$details->id_item}}" name="jmlStokMask_{{$details->id_item}}" autocomplete="off" data-a-dec="," data-a-sep="." readonly />
                                                            <input type="hidden" class="form-control qtyStokAlokasi" id="jmlStok_{{$details->id_item}}" name="jmlStok_{{$details->id_item}}" />
                                                        </div>
                                                        {{-- <div class="col-lg-2 alokasiQty">
                                                            <div class="font-size-lg font-weight-bolder">Jumlah :</div>
                                                            <input type="text" class="form-control alokasiInput" id="jmlAlokasiMask_{{$details->id_item}}" name="jmlAlokasiMask_{{$details->id_item}}" autocomplete="off" data-a-dec="," data-a-sep="." value="{{$details->qty_item}}" />
                                                            <input type="hidden" class="form-control qtyAlokasi" id="jmlAlokasi_{{$details->id_item}}" name="jmlAlokasi_{{$details->id_item}}" value="{{$details->qty_item}}" />
                                                        </div> --}}
                                                        <div class="col-lg-3 alokasiQty">
                                                            <div class="font-size-lg font-weight-bolder">Jumlah :</div>
                                                            <div class="row">
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control alokasiInput" id="jmlAlokasiMask_{{$details->id_item}}" name="jmlAlokasiMask_{{$details->id_item}}" autocomplete="off" data-a-dec="," data-a-sep="." value="{{$details->qty_item}}" />
                                                                    <input type="hidden" class="form-control qtyAlokasi" id="jmlAlokasi_{{$details->id_item}}" name="jmlAlokasi_{{$details->id_item}}" value="{{$details->qty_item}}" />

                                                                    <div class="input-group-append" data-toggle="tooltip"  title="Tambah" data-placement="top">
                                                                        <label class="input-group-text btn btn-primary btn-icon font-weight-bold addItem" id="btnAddItem_{{$details->id_item}}">
                                                                            <i class="flaticon2-plus"></i>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- <div class="col-lg-3 qtyPerDus">
                                                            <div class="font-size-lg font-weight-bolder">Qty per Dus :</div>
                                                            <div class="row">
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control qtyDusInput form-control-solid" id="jmlQtyDusMask_{{$details->id_item}}" name="jmlQtyDusMask_{{$details->id_item}}" autocomplete="off" data-a-dec="," data-a-sep="." />
                                                                    <input type="hidden" class="form-control qtyDus" id="jmlQtyDus_{{$details->id_item}}" name="jmlQtyDus_{{$details->id_item}}" />

                                                                    <div class="input-group-append" data-toggle="tooltip"  title="Tambah" data-placement="top">
                                                                        <label class="input-group-text btn btn-primary btn-icon font-weight-bold addItem" id="btnAddItem_{{$details->id_item}}">
                                                                            <i class="flaticon2-plus"></i>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div> --}}
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            <div class="row">
                                                <div class="col-12 mt-5">
                                                    <table class="table table-separate table-head-custom table-checkable table_alokasi" id="tableAlokasi_{{$details->id_item}}">
                                                        <thead>
                                                            <tr>
                                                                <th style="text-align:center;">Gudang</th>

                                                                <th style="text-align:center;">Jumlah Alokasi</th>

                                                                {{-- <th style="text-align:center;">Jumlah per Dus</th> --}}
                                                                @if($dataDlv->status_pengiriman == "draft")
                                                                <th style="text-align:center;" style="width: 50px;">Aksi</th>
                                                                @endif
                                                            </tr>
                                                        </thead>
                                                        <tbody id="listAllocation_{{$details->id_item}}">
                                                            @if($dataDlv->status_pengiriman != "draft")
                                                                @foreach ($dataAlokasiDlv as $dataAlokasi)
                                                                    @if($details->id == $dataAlokasi["id_detail"])
                                                                    <tr>
                                                                        <td style="text-align:center;">{{$dataAlokasi["txt_index"]}}<input type='hidden' class='form-control qtyCheck' value='{{$dataAlokasi["qty_item"]}}' /></td>
                                                                        <td style="text-align:center;">{{number_format($dataAlokasi["qty_item"] , 2, ',', '.')}} {{$details->nama_satuan}}</td>
                                                                    </tr>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    <!--end::Card-->
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-white d-sm-flex justify-content-sm-between align-items-sm-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-light-danger font-weight-bold mr-2" id="cancel">Keluar <i class="flaticon2-cancel icon-sm"></i></button>
                            </div>

                            <div class="mt-2 mt-sm-0">
                                <input type="hidden" id="submit_action" name="submit_action" class="form-control" readonly>
                                <button type="button" style="display: none;" id="btnAlokasiStok" data-toggle="modal" data-backdrop="static" data-target="#modal_alokasi"></button>
                                @if($dataDlv->status_pengiriman == "draft")
                                    <button type="button" class="btn btn-secondary mt-2 mt-sm-0 btnSubmit" id="btn_edit" value="ubah">Ubah Pengiriman<i class="flaticon-edit ml-2"></i></button>
                                    <button type="button" class="btn btn-light-primary font-weight-bold mr-2 btnSubmit" id="btn_posting" value="Simpan Alokasi"> Simpan Alokasi <i class="flaticon-paper-plane-1"></i></button>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
			</div>
			<!-- /content area -->
@endsection
@section('scripts')
    <script type="text/javascript">

        $(document).ready(function () {

            $('.indexOption').select2({
                allowClear: true,
                placeholder: "Pilih Gudang"
            });

            //$(".alokasiInput").autoNumeric('init');
            $(".alokasiInput").each(function() {
                var id = $(this).attr('id');
                $("#"+id).autoNumeric('init', {mDec: '2'});

            });

            // $(".qtyDusInput").each(function() {
            //     var id = $(this).attr('id');
            //     $("#"+id).autoNumeric('init', {mDec: '0'});

            // });

            footerDataForm('{{$dataDlv->id}}');
        });

        $(document).ready(function() {
            var table = $('.table_alokasi');

            // begin first table
            table.DataTable({
                responsive: true,
                searching: false,
                paging: false,
                info: false,
                // columnDefs: [
                //     {
                //         responsivePriority: 4,
                //         targets: -1,
                //         title: 'Aksi',
                //         orderable: false,
                //     },
                // ],
            });
        });

        $(".alokasiInput").on('change', function() {
            $(this).closest('.alokasiQty').find(".qtyAlokasi").val($(this).autoNumeric("get"));
        });

        // $(".qtyDusInput").on('change', function() {
        //     $(this).closest('.qtyPerDus').find(".qtyDus").val($(this).autoNumeric("get"));
        // });

        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

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

        $("#cancel").on('click', function(e) {
            Swal.fire({
                title: "Batal?",
                text: "Apakah anda ingin membatalkan posting pengiriman barang?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    window.location.href = "{{ url('/Delivery') }}";
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
	    });

        $(".btnSubmit").on("click", function(e){
            var btn = $(this).val();
            $("#submit_action").val(btn);

            var totalAlokasi = 0;
            var ttlPengiriman = '{{$dataDlv->jumlah_total_sj}}';
            

            $(".qtyCheck").each(function(){
				totalAlokasi = parseFloat(totalAlokasi) + parseFloat($(this).val());
			});

            if (btn != "ubah") {
                if (parseFloat(totalAlokasi).toFixed(2) != parseFloat(ttlPengiriman).toFixed(2)) {
                    Swal.fire(
                        "Gagal!",
                        "Jumlah Alokasi Belum sesuai dengan Jumlah Pengiriman !",
                        "warning"
                    );
                    return false;
                }
            }

            Swal.fire({
                title: ucwords(btn) + " Pengiriman Barang?",
                text: "Apakah yakin ingin melakukan " + ucwords(btn) +" Pengiriman Barang?",
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
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                }
            });
		});

        function footerDataForm(idDlv) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/Delivery/GetDataFooter",
                method: 'POST',
                data: {
                    idDelivery: idDlv,
                },
                success: function(result){
                    if (result != "null") {
                        var ttlQty = result.qtyItem;
                        var ttlQtyFixed = ttlQty.toString().replace(".", ",");

                        $("#qtyTtl").val(ttlQtyFixed);
                        $("#qtyTtlMask").val(parseFloat(ttlQtyFixed).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2}));

                    }
                    else {
                        $("#qtyTtl").val(0);
                        $("#qtyTtlMask").val(parseFloat(0).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    }
                }
            });
        }

        $(".addItem").on('click', function(e) {
			var errCount = 0;
            var count = "";

            var idDetail = $(this).closest('.dataAlokasi').find(".idDetail").val();
            var nmSatuan = $(this).closest('.dataAlokasi').find(".nmSatuan").val();
            var idItem = $(this).closest('.dataAlokasi').find(".idItem").val();
            var idSatuan = $(this).closest('.dataAlokasi').find(".idSatuan").val();
            var idIndex = $(this).closest('.dataAlokasi').find(".indexOption option:selected").val();
            var txtIndex = $(this).closest('.dataAlokasi').find(".indexOption option:selected").html();
            var jmlKirim = $(this).closest('.dataAlokasi').find(".jmlKirim").val();
            var qty = $(this).closest('.dataAlokasi').find(".qtyAlokasi").val();
            var stok = $(this).closest('.dataAlokasi').find(".qtyStokAlokasi").val();
            var qtyMask = parseFloat(qty).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2});
            // var qtyDus = $(this).closest('.dataAlokasi').find(".qtyDus").val();
            // var qtyDusMask = parseFloat(qtyDus).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2});

            var totalAlokasi = 0;

            $(".qty_"+idItem).each(function(){
				totalAlokasi = parseFloat(totalAlokasi) + parseFloat($(this).val());
			});

            if (parseFloat(qty) > parseFloat(stok)) {

                Swal.fire(
                    "Gagal!",
                    "Jumlah Item Melebihi Stok Tersedia !",
                    "warning"
                );
                return false;
            }

            if (parseFloat(qty) > parseFloat(jmlKirim)) {

                Swal.fire(
                    "Gagal!",
                    "Jumlah Alokasi Item tidak dapat Melebihi Sisa Pengiriman !",
                    "warning"
                );
                return false;
            }

            if ((parseFloat(totalAlokasi) + parseFloat(qty)) > parseFloat(jmlKirim)) {

                Swal.fire(
                    "Gagal!",
                    "Jumlah Total Alokasi Item tidak dapat Melebihi Sisa Pengiriman !",
                    "warning"
                );
                return false;
            }

            if (qty == "" || qty == null) {
                Swal.fire(
                    "Gagal!",
                    "Harap masukkan jumlah alokasi terlebih dahulu !",
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

            var double = 0;

            if (idIndex == "" || idIndex == null) {
                Swal.fire(
                    "Gagal!",
                    "Harap pilih tempat penyimpanan terlebih dahulu!",
                    "warning"
                );
                return false;
            }
            else {
                $(".index_"+idItem).each(function(){
                    if (idIndex == $(this).val()) {
                        double = parseFloat(double) + 1;
                    }
                });
            }

            // if (double != 0) {
            //     Swal.fire(
            //         "Gagal!",
            //         "sudah terdapat Alokasi Item pada penyimpanan ini!",
            //         "warning"
            //     );
            //     return false;
            // }

            // if (qtyDus == "") {
            //     Swal.fire(
            //         "Gagal!",
            //         "Harap masukkan Qty per dus terlebih dahulu!",
            //         "warning"
            //     );
            //     return false;
            // }

			Swal.fire({
                title: "Alokasi Item?",
                text: "Apakah data item sudah sesuai?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    var timeStamp = new Date();
                    count = timeStamp.getTime();
                    var data = "<tr id='row_"+idItem+"_"+idIndex+"_"+count+"'>";
                        data += "<td style='display:none;'><input type='text' class='form-control' id='idDetail"+idItem+"_"+idIndex+"_"+count+"' name='isi["+idItem+"_"+idIndex+"_"+count+"][idDetail]' value='"+idDetail+"' /></td>";
                        data += "<td style='display:none;'><input type='text' class='form-control' id='idItem_"+idItem+"_"+idIndex+"_"+count+"' name='isi["+idItem+"_"+idIndex+"_"+count+"][idItem]' value='"+idItem+"' /></td>";
                        data += "<td style='display:none;'><input type='text' class='form-control' id='idSatuan_"+idItem+"_"+idIndex+"_"+count+"' name='isi["+idItem+"_"+idIndex+"_"+count+"][idSatuan]' value='"+idSatuan+"' /></td>";
                        data += "<td style='display:none;'><input type='text' class='form-control  index_"+idItem+"' id='idIndex_"+idItem+"_"+idIndex+"' name='isi["+idItem+"_"+idIndex+"_"+count+"][idIndex]' value='"+idIndex+"' /></td>";
                        data += "<td id='txtIndex_"+idItem+"_"+idIndex+"'>"+txtIndex+"</td>";
                        data += "<td align='center' id='qtyMask_"+idItem+"_"+idIndex+"'>"+qtyMask+" "+nmSatuan+"</td>";
                        data += "<td style='display:none;'><input type='text' class='form-control qtyCheck qty_"+idItem+"' id='qty_"+idItem+"_"+idIndex+"_"+count+"' name='isi["+idItem+"_"+idIndex+"_"+count+"][qty]' value='"+qty+"' /></td>";
                        // data += "<td align='center' id='qtyDusMask_"+idItem+"_"+idIndex+"'>"+qtyDusMask+"</td>";
                        // data += "<td style='display:none;'><input type='text' class='form-control qtyDus_"+idItem+"' id='qtyDus_"+idItem+"_"+idIndex+"_"+count+"' name='isi["+idItem+"_"+idIndex+"_"+count+"][qtyDus]' value='"+qtyDus+"' /></td>";
                        data += "<td style='text-align:center;'><button type='button' onclick='deleteRow("+idItem+","+idIndex+","+count+");' class='btn btn-sm btn-clean btn-icon del' title='Hapus' ><i class='la la-trash'></i></button></td>";
                        data += "</tr>";

                        $("#index_"+idItem).val("").trigger('change');
                        $("#jmlAlokasiMask_"+idItem).val("").trigger('change');
                        // $("#jmlQtyDusMask_"+idItem).val("").trigger('change');

                        $('#tableAlokasi_'+idItem).DataTable().row.add($(data)).draw();
                        $('#tableAlokasi_'+idItem).DataTable().data.reload().draw();
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
		});

        function deleteRow(idItem, idIndex, count) {
            var table = $('#tableAlokasi_'+idItem).DataTable();
                table.row('#row_'+idItem+"_"+idIndex+"_"+count).remove().draw();
	    };

        $(document).ready(function() {
            var statusRcv = "{{$dataDlv->status_pengiriman}}";
            if (statusRcv != "posted") {
                var dataAllocation = @json($dataAlokasiDlv);
                var timeStamp = new Date();
                var count = timeStamp.getTime();
                for (var i = 0; i < dataAllocation.length;i++) {
                    var idDetail = dataAllocation[i].id_detail;
                    var idItem = dataAllocation[i].id_item;
                    var nmSatuan = dataAllocation[i].nama_satuan;
                    var idIndex = dataAllocation[i].id_index;
                    var idSatuan = dataAllocation[i].id_satuan;
                    var txtIndex = dataAllocation[i].txt_index;
                    var qty = dataAllocation[i].qty_item;
                    var qtyMask = parseFloat(qty).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2});
                    // var qtyDus = dataAllocation[i].qty_dus;
                    // var qtyDusMask = parseFloat(qtyDus).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2});
                    count = count + i;
                    var data = "<tr id='row_"+idItem+"_"+idIndex+"_"+count+"'>";
                        data += "<td style='display:none;'><input type='text' class='form-control' id='idDetail"+idItem+"_"+idIndex+"_"+count+"' name='isi["+idItem+"_"+idIndex+"_"+count+"][idDetail]' value='"+idDetail+"' /></td>";
                        data += "<td style='display:none;'><input type='text' class='form-control' id='idItem_"+idItem+"_"+idIndex+"_"+count+"' name='isi["+idItem+"_"+idIndex+"_"+count+"][idItem]' value='"+idItem+"' /></td>";
                        data += "<td style='display:none;'><input type='text' class='form-control' id='idSatuan_"+idItem+"_"+idIndex+"_"+count+"' name='isi["+idItem+"_"+idIndex+"_"+count+"][idSatuan]' value='"+idSatuan+"' /></td>";
                        data += "<td style='display:none;'><input type='text' class='form-control  index_"+idItem+"' id='idIndex_"+idItem+"_"+idIndex+"_"+count+"' name='isi["+idItem+"_"+idIndex+"_"+count+"][idIndex]' value='"+idIndex+"' /></td>";
                        data += "<td id='txtIndex_"+idItem+"_"+idIndex+"'>"+txtIndex+"</td>";
                        data += "<td align='center' id='qtyMask_"+idItem+"_"+idIndex+"'>"+qtyMask+" "+nmSatuan+"</td>";
                        data += "<td style='display:none;'><input type='text' class='form-control qtyCheck qty_"+idItem+"' id='qty_"+idItem+"_"+idIndex+"_"+count+"' name='isi["+idItem+"_"+idIndex+"_"+count+"][qty]' value='"+qty+"' /></td>";
                        // data += "<td align='center' id='qtyDusMask_"+idItem+"_"+idIndex+"'>"+qtyDusMask+"</td>";
                        // data += "<td style='display:none;'><input type='text' class='form-control qtyDus_"+idItem+"' id='qtyDus_"+idItem+"_"+idIndex+"_"+count+"' name='isi["+idItem+"_"+idIndex+"_"+count+"][qtyDus]' value='"+qtyDus+"' /></td>";
                        data += "<td style='text-align:center;'><button type='button' onclick='deleteRow("+idItem+","+idIndex+","+count+");' class='btn btn-sm btn-clean btn-icon del' title='Hapus' ><i class='la la-trash'></i></button></td>";
                        data += "</tr>";

                        $('#tableAlokasi_'+idItem).DataTable().row.add($(data)).draw();
                        // $('#tableAlokasi_'+idItem).DataTable().data.reload().draw();
                }
            }
        });

        $(".indexOption").on("change", function() {
            var idIndex = $(this).val();
            var idItem = $(this).closest('.dataAlokasi').find(".idItem").val();
            var idSatuan = $(this).closest('.dataAlokasi').find(".idSatuan").val();
            if (idIndex != "" && idItem != "") {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/Delivery/GetDataStock",
                    method: 'POST',
                    data: {
                        id_item: idItem,
                        id_index: idIndex,
                        id_satuan: idSatuan
                    },
                    success: function(result){
                        if (result.length > 0) {
                            var stokItem = result[0].stok_item;
                            var stokItemMask = parseFloat(stokItem).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2});
                            // var qtyPerDus = result[0].qty_per_dus;
                            // if(qtyPerDus == null){
                            //     qtyPerDus = 0;
                            // }
                            // var qtyPerDusMask = parseFloat(qtyPerDus).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2});
                            $("#jmlStokMask_"+idItem).val(stokItemMask);
                            $("#jmlStok_"+idItem).val(stokItem);
                            // $("#jmlQtyDusMask_"+idItem).val(qtyPerDusMask);
                            // $("#jmlQtyDus_"+idItem).val(qtyPerDus);
                        }
                        else {
                            $("#jmlStokMask_"+idItem).val(0);
                            $("#jmlStok_"+idItem).val(0);
                            // $("#jmlQtyDusMask_"+idItem).val(0);
                            // $("#jmlQtyDus_"+idItem).val(0);
                        }
                    }
                });
            }
            else {
                $("#jmlStokMask_"+idItem).val(0);
                $("#jmlStok_"+idItem).val(0);
            }
        });


        $(document).ready(function() {
            $(".headerItem").each(function() {
                var idItem = $(this).closest('.headerCollapse').find(".idItem").val();

                if (idItem != "") {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "/Delivery/GetIndexList",
                        method: 'POST',
                        data: {
                            id_item: idItem
                        },
                        success: function(result){
                            $('#index_'+idItem).find('option:not(:first)').remove();
                            if (result.length > 0) {
                                for (var i = 0; i < result.length;i++) {
                                    if (result[i].id != null) {
                                        $('#index_'+idItem).append($('<option>', {
                                            value:result[i].id,
                                            text:result[i].txt_index
                                        }));
                                    }
                                }
                            }
                        }
                    });
                }
                else {
                    $("#jmlStokMask_"+idItem).val(0);
                    $("#jmlStok_"+idItem).val(0);
                }
            });
        });
    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
