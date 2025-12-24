@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
                <form action="{{ route('ProductionReceiving.PostStaging', $dataRcv->id) }}" class="form-horizontal" id="form_add" method="POST">
                {{ csrf_field() }}
                    <div class="card card-custom">
                        <div class="card-header bg-primary header-elements-sm-inline">
                            <h5 class="card-title text-white">Detail Pengaturan Penerimaan</h5>
                        </div>

                        <div class="card-body" style="background-color: rgba(245, 245, 245, 0.4);">
                            <!-- Basic initialization -->
                            <div class="d-flex flex-column flex-xl-row">
                                <div class="flex-column flex-lg-row-auto w-xl-400px">
                                    <!--begin::Card-->
                                    <div class="card mb-5">
                                        <div class="card-body">
                                            <div class="pb-5 fs-6">
                                                <div class="font-size-h5 font-weight-bolder">Kode Penerimaan :</div>
                                                <div class="font-size-h6 text-dark-50">
                                                    {{strtoupper($dataRcv->kode_penerimaan)}}
                                                    <span class="label label-primary label-inline ml-1">{{strtoupper($dataRcv->status_penerimaan)}}</span>
                                                    {{ $dataRcv->flag_revisi == "1" ? "Revisi" : "Tidak" }}
                                                </div>

                                                <div class="font-size-h5 font-weight-bolder mt-5">Vendor / Supplier :</div>
                                                <div class="font-size-h6 text-dark-50">{{strtoupper($dataRcv->nama_supplier)}}</div>

                                                <div class="font-size-h5 font-weight-bolder mt-5">Alamat Vendor / Supplier :</div>
                                                <div class="font-size-h6 text-dark-50">{{strtoupper($dataRcv->alamat_pt)}}</div>

                                                <div class="font-size-h5 font-weight-bolder mt-5">No. Perintah Produksi :</div>
                                                <div class="font-size-h6 text-dark-50">{{strtoupper($dataRcv->no_production_order)}}</div>

                                                <div class="font-size-h5 font-weight-bolder mt-5">No. Surat jalan :</div>
                                                <div class="font-size-h6 text-dark-50">{{strtoupper($dataRcv->no_sj_supplier)}}</div>

                                                <div class="font-size-h5 font-weight-bolder mt-5">Tanggal Surat jalan :</div>
                                                <div class="font-size-h6 text-dark-50">{{\Carbon\Carbon::parse($dataRcv->tanggal_sj)->format('d F Y')}}</div>

                                                <div class="font-size-h5 font-weight-bolder mt-5">Tanggal Terima Barang :</div>
                                                <div class="font-size-h6 text-dark-50">{{\Carbon\Carbon::parse($dataRcv->tanggal_terima)->format('d F Y')}}</div>

                                                <div class="font-size-h5 font-weight-bolder mt-5">Keterangan Penerimaan Barang :</div>
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
                                    @foreach ($detailRcv as $details)
                                    <div class="card card-custom mb-5" data-card="true" id="kt_card_{{$details->id}}">
                                        <div class="card-header">
                                            <div class="card-title">
                                                <label class="font-size-h3 font-weight-bolder text-dark mb-0">{{ $details->value_spesifikasi != null ? '('.$details->value_spesifikasi.')' : "" }}{{strtoupper($details->kode_item)}} - {{strtoupper($details->nama_item)}}</label>
                                            </div>

                                            <div class="card-toolbar">
                                                <a href="#" class="btn btn-icon btn-sm btn-hover-light-primary mr-1" data-card-tool="toggle" data-toggle="tooltip" data-placement="top" title="" data-original-title="Toggle Card">
                                                    <i class="ki ki-arrow-down icon-nm"></i>
                                                </a>
                                            </div>

                                        </div>
                                        <div class="card-body" kt-hidden-height="384" style="overflow: hidden; padding-top: 0px; padding-bottom: 0px;">
                                            <div class="row">
                                                <div class="col-xl-4">
                                                    <div class="font-size-h5 font-weight-bolder">Jumlah order :</div>
                                                    <div class="font-size-h6 text-dark-50">{{number_format($details->qty_order , 2, ',', '.')}} {{$details->nama_satuan}}</div>
                                                </div>
                                                <div class="col-xl-4">
                                                    <div class="font-size-h5 font-weight-bolder">Sisa order (Outstanding) :</div>
                                                    <div class="font-size-h6 text-dark-50">{{number_format($details->outstanding_qty , 2, ',', '.')}} {{$details->nama_satuan}}</div>
                                                </div>
                                                <div class="col-xl-4">
                                                    <div class="font-size-h5 font-weight-bolder">Jumlah penerimaan :</div>
                                                    <div class="font-size-h6 text-dark-50">{{number_format($details->qty_item , 2, ',', '.')}} {{$details->nama_satuan}}</div>
                                                </div>
                                            </div>
                                            @if($dataRcv->status_penerimaan == "draft")
                                            <div class="row">
                                                <div class="col-12 mt-5">
                                                    <div class="font-size-h5 font-weight-bolder">Alokasi Penerimaan :</div>
                                                    <div class="row mt-1 dataAlokasi">
                                                        <div class="col-lg-6">
                                                            <input type="hidden" class="form-control idDetail" id="idDetail_{{$details->id}}" name="idDetail_{{$details->id}}" value="{{$details->id}}" />
                                                            <input type="hidden" class="form-control nmSatuan" id="nmSatuan_{{$details->id_item}}" name="idDetails_{{$details->id_item}}" value="{{$details->nama_satuan}}" />
                                                            <input type="hidden" class="form-control idItem" id="idItem_{{$details->id_item}}" name="idItem_{{$details->id_item}}" value="{{$details->id_item}}" />
                                                            <input type="hidden" class="form-control idSatuan" id="idSatuan_{{$details->id_item}}" name="idSatuan_{{$details->id_satuan}}" value="{{$details->id_satuan}}" />
                                                            <input type="hidden" class="form-control jmlTerima" id="jmlTerima_{{$details->id_item}}" name="jmlTerima_{{$details->id_item}}" value="{{$details->qty_item}}" />
                                                            <select class="form-control select2 indexOption" id="index_{{$details->id_item}}" name="index_{{$details->id_item}}" style="width:100%;">
                                                                <option label="Label"></option>
                                                                @foreach($listIndex as $index)
                                                                <option value="{{$index['id']}}">{{strtoupper($index['nama_index'])}}</option>
                                                                @endforeach
                                                            </select>
                                                            <span class="form-text text-danger errItem" style="display:none;">*Harap pilih index terlebih dahulu!</span>
                                                        </div>
                                                        <div class="col-lg-3 alokasiQty">
                                                            <input type="text" class="form-control alokasiInput" id="jmlAlokasiMask_{{$details->id_item}}" name="jmlAlokasiMask_{{$details->id_item}}" placeholder="Jumlah yang diterima" autocomplete="off" data-a-dec="," data-a-sep="." />
                                                            <input type="hidden" class="form-control qtyAlokasi" id="jmlAlokasi_{{$details->id_item}}" name="jmlAlokasi_{{$details->id_item}}" />
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <button type="button" class="btn btn-primary font-weight-bold addItem" id="btnAddItem_{{$details->id_item}}">Tambah Item</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            <div class="row">
                                                <div class="col-12 mt-5">
                                                    <table class="table table-separate table-head-custom table-checkable table_alokasi" id="tableAlokasi_{{$details->id_item}}">
                                                        <thead>
                                                            <tr>
                                                                <th style="text-align:center;">Tempat Penyimpanan</th>

                                                                <th style="text-align:center;">Jumlah Alokasi</th>
                                                                @if($dataRcv->status_penerimaan == "draft")
                                                                <th style="text-align:center;" style="width: 50px;">Aksi</th>
                                                                @endif
                                                            </tr>
                                                        </thead>
                                                        <tbody id="listAllocation_{{$details->id_item}}">
                                                            @if($dataRcv->status_penerimaan != "draft")
                                                                @foreach ($dataAlokasiRcv as $dataAlokasi)
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
                                @if($dataRcv->status_penerimaan == "draft")
                                <button type="button" class="btn btn-secondary mt-2 mt-sm-0 btnSubmit" id="btn_edit" value="ubah">Ubah Penerimaan<i class="flaticon-edit ml-2"></i></button>
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
                placeholder: "Pilih Index Penyimpanan"
            });

            //$(".alokasiInput").autoNumeric('init');
            $(".alokasiInput").each(function() {
            var id = $(this).attr('id');
            $("#"+id).autoNumeric('init');

        });

            footerDataForm('{{$dataRcv->id}}');
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
                text: "Apakah anda ingin membatalkan posting penerimaan barang?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    window.location.href = "{{ url('/ProductionReceiving') }}";
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
	    });

        $(".btnSubmit").on("click", function(e){
            var btn = $(this).val();
            var id = $(this).attr('id');
            $("#submit_action").val(btn);

            var totalAlokasi = 0;
            var ttlPenerimaan = '{{$dataRcv->jumlah_total_sj}}';

            $(".qtyCheck").each(function(){
				totalAlokasi = parseFloat(totalAlokasi) + parseFloat($(this).val());
			});

            // console.log(parseFloat(totalAlokasi));
            // console.log(parseFloat(ttlPenerimaan));

            if (parseFloat(parseFloat(totalAlokasi).toFixed(2)) != parseFloat(ttlPenerimaan) && id == "btn_posting") {
                Swal.fire(
                    "Gagal!",
                    "Jumlah Alokasi Belum sesuai dengan Jumlah Penerimaan !",
                    "warning"
                );
                return false;
            }

            Swal.fire({
                title: ucwords(btn) + " Penerimaan Barang Produksi?",
                text: "Apakah yakin ingin melakukan " + ucwords(btn) +" Penerimaan Barang Produksi?",
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

        function footerDataForm(idRcv) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/ProductionReceiving/GetDataFooter",
                method: 'POST',
                data: {
                    idReceiving: idRcv,
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
            var idItem = $(this).closest('.dataAlokasi').find(".idItem").val();
            var idSatuan = $(this).closest('.dataAlokasi').find(".idSatuan").val();
            var nmSatuan = $(this).closest('.dataAlokasi').find(".nmSatuan").val();
            var idIndex = $(this).closest('.dataAlokasi').find(".indexOption option:selected").val();
            var txtIndex = $(this).closest('.dataAlokasi').find(".indexOption option:selected").html();
            var jmlTerima = $(this).closest('.dataAlokasi').find(".jmlTerima").val();
            var qty = $(this).closest('.dataAlokasi').find(".qtyAlokasi").val();
            var qtyMask = parseFloat(qty).toLocaleString('id-ID', { minimumFractionDigits: 2});

            var totalAlokasi = 0;

            $(".qty_"+idItem).each(function(){
				totalAlokasi = parseFloat(totalAlokasi) + parseFloat($(this).val());
			});

            if (parseFloat(qty) > parseFloat(jmlTerima)) {

                Swal.fire(
                    "Gagal!",
                    "Jumlah Alokasi Item tidak dapat Melebihi Sisa Penerimaan !",
                    "warning"
                );
                return false;
            }

            if ((parseFloat(totalAlokasi) + parseFloat(qty)) > parseFloat(jmlTerima)) {

                Swal.fire(
                    "Gagal!",
                    "Jumlah Total Alokasi Item tidak dapat Melebihi Sisa Penerimaan !",
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
                        data += "<td style='display:none;'><input type='text' class='form-control' id='idSatuan_"+idItem+"_"+idIndex+"' name='isi["+idItem+"_"+idIndex+"][idSatuan]' value='"+idSatuan+"' /></td>";
                        data += "<td style='display:none;'><input type='text' class='form-control  index_"+idItem+"' id='idIndex_"+idItem+"_"+idIndex+"' name='isi["+idItem+"_"+idIndex+"][idIndex]' value='"+idIndex+"' /></td>";
                        data += "<td id='txtIndex_"+idItem+"_"+idIndex+"'>"+txtIndex+"</td>";
                        data += "<td align='center' id='qtyMask_"+idItem+"_"+idIndex+"'>"+qtyMask+" "+nmSatuan+"</td>";
                        data += "<td style='display:none;'><input type='text' class='form-control qtyCheck qty_"+idItem+"' id='qty_"+idItem+"_"+idIndex+"' name='isi["+idItem+"_"+idIndex+"][qty]' value='"+qty+"' /></td>";
                        data += "<td style='text-align:center;'><button type='button' onclick='deleteRow("+idItem+","+idIndex+");' class='btn btn-sm btn-clean btn-icon del' title='Hapus' ><i class='la la-trash'></i></button></td>";
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
            var statusRcv = "{{$dataRcv->status_penerimaan}}";
            if (statusRcv != "posted") {
                var dataAllocation = @json($dataAlokasiRcv);
                console.log(dataAllocation);
                for (var i = 0; i < dataAllocation.length;i++) {
                    var idDetail = dataAllocation[i].id_detail;
                    var nmSatuan = dataAllocation[i].nama_satuan;
                    var idItem = dataAllocation[i].id_item;
                    var idSatuan = dataAllocation[i].id_satuan;
                    var idIndex = dataAllocation[i].id_index;
                    var txtIndex = dataAllocation[i].txt_index;
                    var qty = dataAllocation[i].qty_item;
                    var qtyMask = parseFloat(qty).toLocaleString('id-ID', { minimumFractionDigits: 2});

                    var data = "<tr id='row_"+idItem+"_"+idIndex+"'>";
                        data += "<td style='display:none;'><input type='text' class='form-control' id='idDetail"+idItem+"_"+idIndex+"' name='isi["+idItem+"_"+idIndex+"][idDetail]' value='"+idDetail+"' /></td>";
                        data += "<td style='display:none;'><input type='text' class='form-control' id='idItem_"+idItem+"_"+idIndex+"' name='isi["+idItem+"_"+idIndex+"][idItem]' value='"+idItem+"' /></td>";
                        data += "<td style='display:none;'><input type='text' class='form-control' id='idSatuan_"+idItem+"_"+idIndex+"' name='isi["+idItem+"_"+idIndex+"][idSatuan]' value='"+idSatuan+"' /></td>";
                        data += "<td style='display:none;'><input type='text' class='form-control  index_"+idItem+"' id='idIndex_"+idItem+"_"+idIndex+"' name='isi["+idItem+"_"+idIndex+"][idIndex]' value='"+idIndex+"' /></td>";
                        data += "<td id='txtIndex_"+idItem+"_"+idIndex+"'>"+txtIndex+"</td>";
                        data += "<td align='center' id='qtyMask_"+idItem+"_"+idIndex+"'>"+qtyMask+" "+nmSatuan+"</td>";
                        data += "<td style='display:none;'><input type='text' class='form-control qtyCheck qty_"+idItem+"' id='qty_"+idItem+"_"+idIndex+"' name='isi["+idItem+"_"+idIndex+"][qty]' value='"+qty+"' /></td>";
                        data += "<td style='text-align:center;'><button type='button' onclick='deleteRow("+idItem+","+idIndex+");' class='btn btn-sm btn-clean btn-icon del' title='Hapus' ><i class='la la-trash'></i></button></td>";
                        data += "</tr>";

                        $('#tableAlokasi_'+idItem).DataTable().row.add($(data)).draw();
                        // $('#tableAlokasi_'+idItem).DataTable().data.reload().draw();
                }
            }
        });

	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
