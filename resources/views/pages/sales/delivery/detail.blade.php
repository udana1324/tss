@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
                <form action="{{ route('Delivery.Posting', $dataDlv->id) }}" class="form-horizontal" id="form_add" method="POST">
                {{ csrf_field() }}
                    <div class="card card-custom">
                        <div class="card-header bg-primary header-elements-sm-inline">
                            <h5 class="card-title text-white">Detail Pengiriman</h5>
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
                                                <div class="font-size-lg text-dark-50">{{ $dataDlv->flag_revisi == "1" ? "Sudah Revisi" : "Belum ada revisi" }}</div>

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

                                                <div class="font-size-h5 font-weight-bolder mt-5">Catatan Pengiriman Barang :</div>
                                                <div class="font-size-h6 text-dark-50">
                                                    <ul>
                                                        @foreach($dataTerms as $terms)
                                                            <li>{{$terms->terms_and_cond}} </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                @if ($dataDlv->status_pengiriman == "posted" && $dataDlv->flag_terkirim == "0")
                                                <div class="font-size-h5 font-weight-bolder mt-5" id="divKonfirm">
                                                    <label class="col-lg-3 col-form-label"></label>
                                                    <div class="col-lg-9">
                                                        <button type="button" class="btn btn-primary font-weight-bold" id="btnKonfirm" data-toggle="modal" data-target="#modal_form_konfirmasi">Konfirmasi Pengiriman</button>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Card-->
                                </div>

                                <div class="flex-lg-row-fluid ml-xl-10">
                                    <!--begin::Card-->
                                    @foreach ($detailDlv as $details)
                                    <div class="card card-custom mb-5" data-card="true" id="kt_card_{{$details->id}}">
                                        <div class="card-header pr-0">
                                            <span href="#" style="width:100%" class="btn" data-card-tool="toggle" data-toggle="tooltip">
                                                <div class="row">
                                                    <div class="card-title col-11 text-left">
                                                        <label class="font-size-h4 font-weight-bolder text-dark mb-0">{{ $details->value_spesifikasi != null ? '('.$details->value_spesifikasi.')' : "" }}{{strtoupper($details->kode_item)}} - {{strtoupper($details->nama_item)}}</label>
                                                    </div>

                                                    <div class="card-toolbar">
                                                        <a href="#" class="btn btn-icon btn-sm btn-hover-light-primary mr-1" data-card-tool="toggle" data-toggle="tooltip" data-placement="top" title="" data-original-title="Rincian">
                                                            <i class="ki ki-arrow-down icon-nm"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </span>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-xl-4">
                                                    <div class="font-size-h5 font-weight-bolder">Jumlah order :</div>
                                                    <div class="font-size-h6 text-dark-50">{{number_format($details->qty_order , 2, ',', '.')}} {{$details->nama_satuan}}</div>
                                                </div>
                                                <div class="col-xl-4">
                                                    <div class="font-size-h5 font-weight-bolder">Sisa order (Outstanding) :</div>
                                                    <div class="font-size-h6 text-dark-50">{{number_format($details->qty_outstanding , 2, ',', '.')}} {{$details->nama_satuan}}</div>
                                                </div>
                                                <div class="col-xl-4">
                                                    <div class="font-size-h5 font-weight-bolder">Jumlah Pengiriman :</div>
                                                    <div class="font-size-h6 text-dark-50">{{number_format($details->qty_item , 2, ',', '.')}} {{$details->nama_satuan}}</div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xl-12">
                                                    <div class="font-size-h5 font-weight-bolder">Keterangan :</div>
                                                    <div class="font-size-h6 text-dark-50">
                                                        <input type="hidden" class="form-control" name="isi2[txt_{{$details->id}}][id_detail]" value="{{$details->id}}">
                                                        <input type="text" class="form-control form-control-solid" name="isi2[txt_{{$details->id}}][keterangan]" value="{{$details->keterangan}}" readonly>
                                                        {{-- <input type="text" class="form-control form-control-solid" name="isi2[txt_{{$details->id}}][keterangan]" value="{{$details->keterangan == null ? $details->keterangan : number_format($details->ktrg , 0, ',', '.')." Dus x ".number_format($details->qty_per_dus, 0, ',', '.')." ".$details->nama_satuan }}" readonly> --}}
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- @if($dataDlv->status_pengiriman == "draft")
                                            <div class="row">
                                                <div class="col-12 mt-5">
                                                    <div class="row mt-1">
                                                        <div class="col-lg-5">
                                                            <div class="font-size-h5 font-weight-bolder">Gudang Pengiriman :</div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="font-size-h5 font-weight-bolder">Stok :</div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-1 dataAlokasi">
                                                        <div class="col-lg-5">
                                                            <input type="hidden" class="form-control idDetail" id="idDetail_{{$details->id}}" name="idDetail_{{$details->id}}" value="{{$details->id}}" />
                                                            <input type="hidden" class="form-control nmSatuan" id="nmSatuan_{{$details->id_item}}" name="idDetails_{{$details->id_item}}" value="{{$details->nama_satuan}}" />
                                                            <input type="hidden" class="form-control idItem" id="idItem_{{$details->id_item}}" name="idItem_{{$details->id_item}}" value="{{$details->id_item}}" />
                                                            <input type="hidden" class="form-control jmlKirim" id="jmlKirim_{{$details->id_item}}" name="jmlKirim_{{$details->id_item}}" value="{{$details->qty_item}}" />
                                                            <select class="form-control select2 indexOption" id="index_{{$details->id_item}}" name="index_{{$details->id_item}}" style="width:100%;">
                                                                <option label="Label"></option>
                                                                @foreach($listIndex as $index)
                                                                <option value="{{$index['id']}}">{{strtoupper($index['nama_index'])}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="form-text text-danger errItem" style="display:none;">*Harap pilih gudang terlebih dahulu!</span>
                                                        </div>
                                                        <div class="col-lg-2 stockAlokasi">
                                                            <input type="text" class="form-control" id="jmlStokMask_{{$details->id_item}}" name="jmlStokMask_{{$details->id_item}}" autocomplete="off" data-a-dec="," data-a-sep="." readonly />
                                                            <input type="hidden" class="form-control qtyStokAlokasi" id="jmlStok_{{$details->id_item}}" name="jmlStok_{{$details->id_item}}" />
                                                        </div>
                                                        <div class="col-lg-3 alokasiQty">
                                                            <input type="text" class="form-control alokasiInput" id="jmlAlokasiMask_{{$details->id_item}}" name="jmlAlokasiMask_{{$details->id_item}}" placeholder="Jumlah yang dikirim" autocomplete="off" data-a-dec="," data-a-sep="." />
                                                            <input type="hidden" class="form-control qtyAlokasi" id="jmlAlokasi_{{$details->id_item}}" name="jmlAlokasi_{{$details->id_item}}" />
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <button type="button" class="btn btn-primary font-weight-bold addItem" id="btnAddItem_{{$details->id_item}}">Tambah</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif --}}
                                            <div class="row">
                                                <div class="col-12 mt-5">
                                                    <table class="table table-separate table-head-custom table-checkable table_alokasi" id="tableAlokasi_{{$details->id_item}}">
                                                        <thead>
                                                            <tr>
                                                                <th style="text-align:center;">Lokasi</th>

                                                                <th style="text-align:center;">Jumlah Alokasi</th>

                                                                {{-- <th style="text-align:center;">Jumlah per Dus</th> --}}
                                                            </tr>
                                                        </thead>
                                                        <tbody id="listAllocation_{{$details->id_item}}">

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
                                    <button type="button" class="btn btn-secondary mt-2 mt-sm-0 btnSubmit" id="btn_edit_staging" value="ubah alokasi barang">Ubah Alokasi Pengiriman<i class="flaticon2-box ml-2"></i></button>
                                    @if($hakAkses->approve == "Y")
                                        <button type="button" class="btn btn-light-primary font-weight-bold mr-2 btnSubmit" id="btn_posting" value="posting"> Posting <i class="flaticon-paper-plane-1"></i></button>
                                    @endif

                                @elseif($dataDlv->status_pengiriman == "posted")
                                    @if($hakAkses->revisi == "Y")
                                        <button type="button" class="btn btn-warning mt-2 mt-sm-0 btnSubmit" id="btn_revisi" value="revisi">Revisi<i class="fas fa-file-signature ml-2"></i></button>
                                    @endif
                                    @if($hakAkses->approve == "Y")
                                        <button type="button" class="btn btn-danger mt-2 mt-sm-0 btnSubmit" id="btn_cancel" value="batal">Batal<i class="flaticon2-cancel ml-2"></i></button>
                                    @endif
                                    @if($hakAkses->print == "Y")
                                        <a type="button" class="btn btn-light-success mt-2 mt-sm-0" href='{{route('Delivery.CetakOrder', $dataDlv->id)}}' target="_blank">Cetak Ringkasan Kirim<i class="fas fa-print ml-2"></i></a>
                                        <a type="button" class="btn btn-primary mt-2 mt-sm-0" href='{{route('Delivery.Cetak', $dataDlv->id)}}' target="_blank">Cetak<i class="fas fa-print ml-2"></i></a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </form>

                {{-- <!-- Modal form konfirmasi -->
				<div id="modal_form_konfirmasi" class="modal fade">
				    <div class="modal-dialog">
					    <div class="modal-content">
						    <div class="modal-header bg-primary">

							    <h5 class="modal-title">Konfirmasi Pengiriman</h5>
						    </div>
						    <div class="modal-body">
							    <form >
								    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label">Penerima :</label>
                                                <div class="col-lg-9">
                                                    <input type="text" class="form-control" placeholder="Masukkan Nama Penerima SJ" name="penerima" id="penerima">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label">Tanggal Diterima :</label>
                                                <div class="col-lg-9">
                                                    <input type="hidden" class="form-control req" name="tanggal" id="tanggal">
                                                    <input type="text" class="form-control" name="tanggal_picker" id="tanggal_picker" readonly>
                                                    <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal diterima terlebih dahulu!</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
							    </form>

						    </div>

						    <div class="modal-footer">
							    <button type="button" class="btn btn-link" data-dismiss="modal">Tutup</button>
                                <button type="button" class="btn btn-primary btn-sm font-weight-bold" id="btnKonfirmSave">Simpan</button>
						    </div>
					    </div>
				    </div>
			    </div>
                <!-- /form form konfirmasi --> --}}
			</div>
			<!-- /content area -->
@endsection
@section('scripts')
    <script type="text/javascript">

        $(document).ready(function () {

            $('.indexOption').select2({
                allowClear: true,
                placeholder: "Pilih Index Gudang"
            });

            //$(".alokasiInput").autoNumeric('init');
            $(".alokasiInput").each(function() {
                var id = $(this).attr('id');
                $("#"+id).autoNumeric('init', {mDec: '0'});

            });

            $('#tanggal_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                autoclose : true,
                format : "dd MM yyyy",
                locale: "id",
            });

            $("#tanggal_picker").datepicker('setDate', new Date());

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

        $("#tanggal_picker").on('change', function() {
            $("#tanggal").val($("#tanggal_picker").data('datepicker').getFormattedDate('yyyy-mm-dd'));
        });

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

            // console.log(parseFloat(totalAlokasi));
            // console.log(parseFloat(ttlPengiriman));


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
                        $("#qtyTtlMask").val(parseFloat(ttlQtyFixed).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                    }
                    else {
                        $("#qtyTtl").val(0);
                        $("#qtyTtlMask").val(parseFloat(0).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                    }
                }
            });
        }

        $(".addItem").on('click', function(e) {
			var errCount = 0;

            var idDetail = $(this).closest('.dataAlokasi').find(".idDetail").val();
            var nmSatuan = $(this).closest('.dataAlokasi').find(".nmSatuan").val();
            var idItem = $(this).closest('.dataAlokasi').find(".idItem").val();
            var idIndex = $(this).closest('.dataAlokasi').find(".indexOption option:selected").val();
            var txtIndex = $(this).closest('.dataAlokasi').find(".indexOption option:selected").html();
            var jmlKirim = $(this).closest('.dataAlokasi').find(".jmlKirim").val();
            var qty = $(this).closest('.dataAlokasi').find(".qtyAlokasi").val();
            var qtyMask = parseFloat(qty).toLocaleString('id-ID', { maximumFractionDigits: 2});

            var totalAlokasi = 0;

            $(".qty_"+idItem).each(function(){
				totalAlokasi = parseFloat(totalAlokasi) + parseFloat($(this).val());
			});

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

            if (double != 0) {
                Swal.fire(
                    "Gagal!",
                    "sudah terdapat Alokasi Item pada penyimpanan ini!",
                    "warning"
                );
                return false;
            }

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
                    var count = $('#tableAlokasi_'+idItem+' >tbody >tr').length;
                    var data = "<tr id='row_"+idItem+"_"+idIndex+"'>";
                        data += "<td style='display:none;'><input type='text' class='form-control' id='idDetail"+idItem+"_"+idIndex+"' name='isi["+idItem+"_"+idIndex+"][idDetail]' value='"+idDetail+"' /></td>";
                        data += "<td style='display:none;'><input type='text' class='form-control' id='idItem_"+idItem+"_"+idIndex+"' name='isi["+idItem+"_"+idIndex+"][idItem]' value='"+idItem+"' /></td>";
                        data += "<td style='display:none;'><input type='text' class='form-control  index_"+idItem+"' id='idIndex_"+idItem+"_"+idIndex+"' name='isi["+idItem+"_"+idIndex+"][idIndex]' value='"+idIndex+"' /></td>";
                        data += "<td id='txtIndex_"+idItem+"_"+idIndex+"'>"+txtIndex+"</td>";
                        data += "<td align='center' id='qtyMask_"+idItem+"_"+idIndex+"'>"+qtyMask+" "+nmSatuan+"</td>";
                        data += "<td style='display:none;'><input type='text' class='form-control qtyCheck qty_"+idItem+"' id='qty_"+idItem+"_"+idIndex+"' name='isi["+idItem+"_"+idIndex+"][qty]' value='"+qty+"' /></td>";
                        data += "</tr>";

                        $("#index_"+idItem).val("").trigger('change');
                        $("#jmlAlokasiMask_"+idItem).val("").trigger('change');

                        $('#tableAlokasi_'+idItem).DataTable().row.add($(data)).draw();
                        $('#tableAlokasi_'+idItem).DataTable().data.reload().draw();
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
		});

        function deleteRow(idItem, idIndex) {
            var table = $('#tableAlokasi_'+idItem).DataTable();
                table.row('#row_'+idItem+"_"+idIndex).remove().draw();
	    };

        $(document).ready(function() {
            var dataAllocation = @json($dataAlokasiDlv);
            for (var i = 0; i < dataAllocation.length;i++) {
                var idDetail = dataAllocation[i].id_detail;
                var idItem = dataAllocation[i].id_item;
                var nmSatuan = dataAllocation[i].nama_satuan;
                var idIndex = dataAllocation[i].id_index;
                var txtIndex = dataAllocation[i].txt_index;
                var qty = dataAllocation[i].qty_item;
                var qtyMask = parseFloat(qty).toLocaleString('id-ID', { maximumFractionDigits: 2});
                // var qtyDus = dataAllocation[i].qty_dus;
                // var qtyDusMask = parseFloat(qtyDus).toLocaleString('id-ID', { maximumFractionDigits: 2});

                var data = "<tr id='row_"+idItem+"_"+idIndex+"'>";
                    data += "<td style='display:none;'><input type='text' class='form-control' id='idDetail"+idItem+"_"+idIndex+"' name='isi["+idItem+"_"+idIndex+"][idDetail]' value='"+idDetail+"' /></td>";
                    data += "<td style='display:none;'><input type='text' class='form-control' id='idItem_"+idItem+"_"+idIndex+"' name='isi["+idItem+"_"+idIndex+"][idItem]' value='"+idItem+"' /></td>";
                    data += "<td style='display:none;'><input type='text' class='form-control  index_"+idItem+"' id='idIndex_"+idItem+"_"+idIndex+"' name='isi["+idItem+"_"+idIndex+"][idIndex]' value='"+idIndex+"' /></td>";
                    data += "<td id='txtIndex_"+idItem+"_"+idIndex+"'>"+txtIndex+"</td>";
                    data += "<td align='center' id='qtyMask_"+idItem+"_"+idIndex+"'>"+qtyMask+" "+nmSatuan+"</td>";
                    data += "<td style='display:none;'><input type='text' class='form-control qtyCheck qty_"+idItem+"' id='qty_"+idItem+"_"+idIndex+"' name='isi["+idItem+"_"+idIndex+"][qty]' value='"+qty+"' /></td>";
                    // data += "<td align='center' id='qtyDusMask_"+idItem+"_"+idIndex+"'>"+qtyDusMask+"</td>";
                    // data += "<td style='display:none;'><input type='text' class='form-control qtyDus_"+idItem+"' id='qtyDus_"+idItem+"_"+idIndex+"' name='isi["+idItem+"_"+idIndex+"][qtyDus]' value='"+qtyDus+"' /></td>";
                    // data += "<td style='text-align:center;'><button type='button' onclick='deleteRow("+idItem+","+idIndex+");' class='btn btn-sm btn-clean btn-icon del' title='Hapus' ><i class='la la-trash'></i></button></td>";
                    data += "</tr>";

                    $('#tableAlokasi_'+idItem).DataTable().row.add($(data)).draw();
                    // $('#tableAlokasi_'+idItem).DataTable().data.reload().draw();
            }
        });

        $("#btnKonfirmSave").on('click', function(e) {
            var penerima = $("#penerima").val();
            var tgl = $("#tanggal").val();
			Swal.fire({
                title: "Konfirmasi Pengiriman",
                text: "Apakah Pengiriman telah selesai?",
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
                        url: "/Delivery/ConfirmDelivery",
                        method: 'POST',
                        dataType : 'json',
                        data: {
                            idDelivery : "{{$dataDlv->id}}}",
                            namaPenerima : penerima,
                            tanggal: tgl
                        },
                        success: function(result){
                            if (result != "false") {
                                Swal.fire(
                                    "Sukses!",
                                    "Konfirmasi Pengiriman Berhasil!.",
                                    "success"
                                )
                                $("#status_kirim").html("Terkirim");
                                $("#divKonfirm").hide();
                                $('#modal_form_konfirmasi').modal('toggle');
                            }
                            else {
                                Swal.fire(
                                    "Gagal!",
                                    "Harap Masukkan Nama Penerima terlebih dahulu!.",
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
		});

        $(".indexOption").on("change", function() {
            var idIndex = $(this).val();
            var idItem = $(this).closest('.dataAlokasi').find(".idItem").val();
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
                        id_index: idIndex
                    },
                    success: function(result){
                        if (result.length > 0) {
                            var stokItem = result[0].stok_item;
                            var stokItemMask = parseFloat(stokItem).toLocaleString('id-ID', { maximumFractionDigits: 2});
                            $("#jmlStokMask_"+idItem).val(stokItemMask);
                            $("#jmlStok_"+idItem).val(stokItem);
                        }
                        else {
                            $("#jmlStokMask_"+idItem).val(0);
                            $("#jmlStok_"+idItem).val(0);
                        }
                    }
                });
            }
            else {
                $("#jmlStokMask_"+idItem).val(0);
                $("#jmlStok_"+idItem).val(0);
            }

        });

    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
