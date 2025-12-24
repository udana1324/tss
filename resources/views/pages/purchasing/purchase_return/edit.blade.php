@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h6 class="card-title text-white">Ubah Retur Pembelian</h6>
					</div>
                    <form action="{{ route('PurchaseReturn.update', $dataRetur->id) }}" class="form-horizontal" id="form_add" method="POST" enctype="multipart/form-data">
					    <div class="card-body">
                            {{ csrf_field() }}
                            @method('PUT')
                            <div class="row">
								<div class="col-md-6">
									<fieldset>
                                        <legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi Vendor / Supplier </h6></legend>
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
                                            <label>Supplier :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <select class="form-control select2 req" id="supplier" name="supplier">
                                                        <option label="Label"></option>
                                                        @foreach($dataSupplier as $supplier)
                                                        <option value="{{$supplier->id}}">{{strtoupper($supplier->nama_supplier)}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih supplier terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>No. Nota Retur :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="nota_retur" id="nota_retur" value="{{$dataRetur->nota_retur}}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Tanggal Retur :</label>
                                            <div class="form-group divTgl form-group-feedback form-group-feedback-right">
                                                <input type="hidden" class="form-control tglValue req" name="tanggal_retur" id="tanggal_retur" >
                                                <input type="text" class="form-control pickerTgl" placeholder="Pilih Tanggal" name="tanggal_retur_picker" id="tanggal_retur_picker" readonly>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal pembelian terlebih dahulu!</span>
                                            </div>
                                        </div>

									</fieldset>
								</div>

								<div class="col-md-6">
									<fieldset>
					                	<legend class="text-muted"><h6><i class="fab la-buffer"></i> Rincian Pembelian Barang</h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>

										<div class="form-group">
                                            <label class="col-lg-7">Retur Pembelian (Barang) :</label> <span class="col-lg-5" id="txtRiwayat"></span>
                                            <div class="input-group">
                                                <select class="form-control select2 detailItem" id="data_retur" name="data_retur">
                                                    <option label="Label"></option>
                                                </select>
                                                <span class="form-text text-danger errItem" style="display:none;">*Harap pilih data retur terlebih dahulu!</span>
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
										<label class="col-lg-3 col-form-label">Total Qty Retur</label>
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
										<label class="col-lg-3 col-form-label">Total Retur</label>
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

            var imgItem = new KTImageInput('file_po');

            $('#supplier').select2({
                allowClear: true,
                placeholder: "Silahkan Pilih Supplier Disini"
            });

            $('#data_retur').select2({
                allowClear: true,
                placeholder: "Silahkan Pilih Retur"
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

            $("#tanggal_retur_picker").datepicker('setDate', new Date());
        });

        $("#qtyItemMask").on('change', function() {
            $("#qtyItem").val($("#qtyItemMask").autoNumeric("get"));
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
                text: "Apakah anda ingin membatalkan pembuatan retur pembelian?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    window.location.href = "{{ url('/PurchaseReturn') }}";
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

        $("#supplier").on("change", function() {
            //getListProduct
            getSupplierReturn($(this).val());

            //Hapus Daftar pembelian
            if ($("#mode").val() == "edit") {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/PurchaseReturn/ResetDetail",
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

        $("#data_retur").on("change", function() {
            //getdataItem
            if ($("#mode").val() == "edit") {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/PurchaseReturn/SetDetail",
                    method: 'POST',
                    data: {
                        idPurchaseReturn: '{{$dataRetur->id}}',
                        idReturn: $(this).val(),
                    },
                    success: function(result){
                        if (result != "") {
                            var datatable = $('#list_item').KTDatatable();
                                datatable.setDataSourceParam('idReturn', '{{$dataRetur->id}}');
                                datatable.reload();
                                footerDataForm('{{$dataRetur->id}}');
                                $("#mode").val("edit");
                        }
                    }
                });
            }
        });

        function getSupplierReturn(idSupplier) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/PurchaseReturn/GetReturnBySupplier",
                method: 'POST',
                data: {
                    id_supplier: idSupplier,
                },
                success: function(result){
                    $('#data_retur').find('option:not(:first)').remove();
                    if (result.length > 0) {
                        for (var i = 0; i < result.length;i++) {
                            $("#data_retur").append($('<option>', {
                                value:result[i].id,
                                text:result[i].kode_retur.toUpperCase()
                            }));
                        }
                    }
                    if ($("#mode").val() == "load") {
                        $("#data_retur").val("{{$dataRetur->id_retur}}").trigger("change");
                    }
                }
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

        $(document).ready(function() {

            var datatable = $('#list_item').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/PurchaseReturn/GetDetail',
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
                            var txtAction = "<a href='#' class='btn btn-sm btn-clean btn-icon' title='Hapus' onclick='deleteDetailItem("+row.id+");return false;'>";
                                txtAction += "<i class='la la-trash'></i>";
                                txtAction += "</a>";

                            return txtAction;
                        },
                    }
                ],
            });
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
                        url: "/PurchaseReturn/DeleteDetail",
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

        function footerDataForm(idReturn) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/PurchaseReturn/GetDataFooter",
                method: 'POST',
                data: {
                    idReturn: idReturn,
                    mode: 'edit'
                },
                success: function(result){
                    if (result != "null") {
                        var subtotal = result.subtotal;
                        var qtyItem = result.qtyItem;
                        var qtyFixed = qtyItem;
                        var subtotalFixed = subtotal;
                        // var jenisPPn = $('input[name=status_ppn]:checked').val();
                        var persenPPNInclude = (100 + parseFloat("{{$taxSettings->ppn_percentage}}")) / 100;
                        var persenPPNExclude = parseFloat("{{$taxSettings->ppn_percentage}}") / 100;

                        $("#qtyTtl").val(qtyFixed);
                        $("#qtyTtlMask").val(parseFloat(qtyFixed).toLocaleString('id-ID', { maximumFractionDigits: 2}))

                        // if (jenisPPn == "I") {
                        //     subtotalFixed = parseFloat(subtotalFixed) / parseFloat(persenPPNInclude);
                        // }

                        var subtotalMask = parseFloat(subtotalFixed).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        // $("#dpp").val(subtotalFixed);
                        // $("#dppMask").val(subtotalMask);

                        // if (jenisPPn != "N") {
                        //     var ppn = parseFloat(subtotalFixed) * parseFloat(persenPPNExclude);
                        //     $("#ppn").val(ppn);
                        //     $("#ppnMask").val(parseFloat(ppn).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        // }
                        // else {
                        //     var ppn = 0;
                        //     $("#ppn").val(ppn);
                        //     $("#ppnMask").val(parseFloat(ppn).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        // }

                        // var grandTotal = parseFloat(subtotalFixed) + parseFloat(ppn);
                        var grandTotal = parseFloat(subtotalFixed);
                        $("#gt").val(Math.ceil(grandTotal));
                        $("#gtMask").val(parseFloat(Math.ceil(grandTotal)).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                    }
                    else {
                        $("#qtyTtl").val(0);
                        $("#qtyTtlMask").val(0);
                        // $("#dpp").val(0);
                        // $("#dppMask").val(0);
                        // $("#ppn").val(0);
                        // $("#ppnMask").val(0);
                        $("#gt").val(0);
                        $("#gtMask").val(0);
                    }
                }
            });
        }

        $(document).ready(function () {
            $("#supplier").val("{{$dataRetur->id_supplier}}").trigger('change');

            footerDataForm('{{$dataRetur->id}}');
            $("#tanggal_retur_picker").datepicker("setDate", new Date("{{$dataRetur->tanggal_retur}}"));
        });

    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
