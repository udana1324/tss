@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h5 class="card-title text-white">Ubah Tukar Faktur</h5>
					</div>
                    <form action="{{ route('PurchaseInvoiceCollection.update', $dataCollection->id) }}" class="form-horizontal" id="form_add" method="POST">
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
                                            <label class="col-lg-3 col-form-label">No. Tukar Faktur :</label>
                                            <div class="col-lg-9">
                                                <input type="hidden" value="load" id="mode">
                                                <input type="text" class="form-control bg-slate-600 border-slate-600 border-1" placeholder="Auto Generated" name="kode_tf" id="kode_tf" value="{{$dataCollection->kode_tf}}" readonly>
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

                                        <!--<div class="form-group">-->
                                        <!--    <label>Rekening Perusahaan :</label>-->
                                        <!--    <div class="form-group form-group-feedback form-group-feedback-right">-->
                                        <!--        <select class="form-control select2 req" id="company_account" name="company_account">-->
                                        <!--            <option label="Label"></option>-->
                                        <!--            @foreach($dataAccount as $account)-->
                                        <!--            <option value="{{$account->id}}">{{strtoupper($account->nama_bank).' - '.$account->nomor_rekening.' - '.ucwords($account->atas_nama)}}</option>-->
                                        <!--            @endforeach-->
                                        <!--        </select>-->
                                        <!--        <span class="form-text text-danger err" style="display:none;">*Harap pilih nomor rekening perusahaan terlebih dahulu!</span>-->
                                        <!--    </div>-->
                                        <!--</div>-->

                                        <div class="form-group">
                                            <label>Tanggal Tukar Faktur :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <input type="hidden" class="form-control req" name="tanggal_tf" id="tanggal_tf" value="{{$dataCollection->tanggal}}">
                                                <input type="text" class="form-control" name="tanggal_tf_picker" id="tanggal_tf_picker" readonly>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal Tukar Faktur terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-lg-3 col-form-label ml-0 pl-0">PIC :</label>
                                            <div class="col-lg-12 ml-0 pl-0">
                                                <input type="text" id="pic" name="pic" class="form-control req" value="{{$dataCollection->pic_pengirim}}">
                                                <span class="form-text text-danger err" style="display:none;">*Harap masukkan PIC/Pengirim Tukar Faktur terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Catatan :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <textarea class="form-control elastic" id="tnc" name="tnc" rows="3" placeholder="Ketik Syarat & Ketentuan Penjualan Disini atau gunakan Template pada tombol Template">@foreach($dataTerms as $terms){{ucwords($terms->terms_and_cond)}}@endforeach</textarea>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-primary" id="btnTemplate" data-toggle="modal" data-target="#modal_list_terms">Template</button>
                                                    </div>
                                                </div>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih alamat customer terlebih dahulu!</span>
                                            </div>
                                        </div>

									</fieldset>
								</div>

								<div class="col-md-6">
									<fieldset>
								<legend class="text-muted"><h6><i class="fab la-buffer"></i> Rincian Tukar Faktur</h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
										<div class="form-group">
                                            <label>Faktur :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <select class="form-control select2 detailItem" id="invoice" name="invoice">
                                                        <option label="Label"></option>
                                                    </select>
                                                    <span class="form-text text-danger errItem" style="display:none;">*Harap pilih faktur terlebih dahulu!</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Tanggal Faktur :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="text" id="tglInv" class="form-control text-right" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Tanggal Jatuh Tempo :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="text" id="tglJT" class="form-control text-right" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Nominal Faktur :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="text" id="nominalInv" class="form-control text-right" readonly>
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
                                        <legend class="text-muted"><h6><i class="la la-list"></i> List Faktur</h6></legend>
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
										<label class="col-lg-3 col-form-label">Grand Total</label>
										<div class="col-lg-9">
											<input type="text" value="0" id="nominalMask" class="form-control text-right" readonly>
											<input type="hidden" id="nominal" name="nominal" class="form-control text-right" readonly>
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

                <!-- Modal form invoice -->
				<div id="modal_detail_invoice" class="modal fade">
				    <div class="modal-dialog modal-xl">
					    <div class="modal-content">
						    <div class="modal-header bg-primary">

							    <h5 class="modal-title" id="txtDetailInvoice"></h5>
						    </div>
						    <div class="modal-body">
                                <div style="text-align: center;" id="container_iframe">
                                    <iframe src="{{ url('preview/purchasing/preview_invoice.pdf') }}?time={{date('Y-m-d H:i:s')}}" id="framePreview" frameborder="0" style="width: 850px;height: 700px;"></iframe>
                                </div>
						    </div>

						    <div class="modal-footer">
							    <button type="button" class="btn btn-link" data-dismiss="modal">Tutup</button>
						    </div>
					    </div>
				    </div>
			    </div>
                <!-- /form invoice -->
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
            $('#supplier').select2({
                allowClear: true,
                placeholder: "Pilih Supplier"
            });

            $('#company_account').select2({
                allowClear: true,
                placeholder: "Pilih Rekening Perusahaan"
            });

            $('#invoice').select2({
                allowClear: true,
                placeholder: "Pilih Faktur"
            });

            $('#tanggal_tf_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                autoclose : true,
                format : "dd MM yyyy",
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/PurchaseInvoiceCollection/GetListTerms",
                method: 'POST',
                data: {
                    target: "collection_pembelian",
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

            $("#tanggal_tf_picker").datepicker('setDate', new Date("{{$dataCollection->tanggal}}"));
            $("#supplier").val("{{$dataCollection->id_supplier}}").trigger("change");
            $("#company_account").val("{{$dataCollection->id_rekening}}").trigger("change");
            footerDataForm('{{$dataCollection->id}}');
            $("#mode").val("edit");
        });

        $("#tanggal_tf_picker").on('change', function() {
            $("#tanggal_tf").val($("#tanggal_tf_picker").data('datepicker').getFormattedDate('yyyy-mm-dd'));
        });

        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        $("#cancel").on('click', function(e) {
            Swal.fire({
                title: "Batal?",
                text: "Apakah anda ingin membatalkan perubahan Tukar Faktur?",
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
                        url: "/PurchaseInvoiceCollection/RestoreDetail",
                        method: 'POST',
                        data: {
                            idTf: '{{$dataCollection->id}}'
                        },
                        success: function(result){
                            window.location.href = "{{ url('/PurchaseInvoiceCollection') }}";
                        }
                    });
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
	    });

        $(document).ready(function() {
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
                                "Harap Tambahkan Minimum 1 Faktur!.",
                                "warning"
                            )
                            count = parseInt(count) + 1;
                            e.preventDefault();
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
        });



        $("#supplier").on("change", function() {
            if ($(this).val() != "") {
                if ($("#mode").val() == "edit") {

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "/PurchaseInvoiceCollection/SetDetail",
                        method: 'POST',
                        data: {
                            idCollection: '{{$dataCollection->id}}',
                            idSupplier: $(this).val(),
                        },
                        success: function(result){
                            if (result != "") {
                                var datatable = $('#list_item').KTDatatable();
                                    datatable.setDataSourceParam('idSupplier', $("#supplier").val());
                                    datatable.setDataSourceParam('idCollection', '{{$dataCollection->id}}');
                                    datatable.reload();
                                    footerDataForm('{{$dataCollection->id}}');
                            }
                        }
                    });
                }

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/PurchaseInvoiceCollection/GetInvoice",
                    method: 'POST',
                    data: {
                        idSupplier:  $(this).val(),
                    },
                    success: function(result){
                        $('#invoice').find('option:not(:first)').remove();
                        if (result.length > 0) {
                            for (var i = 0; i < result.length;i++) {
                                $("#invoice").append($('<option>', {
                                    value:result[i].id,
                                    text:result[i].kode_invoice.toUpperCase()
                                }));
                            }
                        }
                    }
                });
            }
            else {
                $('#invoice').find('option:not(:first)').remove();
            }
        });

        $("#invoice").on("change", function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/PurchaseInvoiceCollection/GetInvoiceData",
                method: 'POST',
                data: {
                    idInvoice: $(this).val(),
                },
                success: function(result){
                    if (result.length > 0) {
                        var nominal = result[0].grand_total.toString().replace(".", ",");
                        $("#nominalInv").val(parseFloat(nominal).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        $("#tglInv").val(formatDate(result[0].tanggal_invoice));
                        $("#tglJT").val(formatDate(result[0].tanggal_jt));
                    }
                }
            });
        });

        $(document).ready(function() {

            var datatable = $('#list_item').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/PurchaseInvoiceCollection/GetDetail',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data: {
                                idCollection: "{{$dataCollection->id}}",
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
                            var txtAction = "<a href='#' class='btn btn-sm btn-clean btn-icon' data-toggle='modal' data-target='#modal_detail_invoice' title='Detail' onclick='viewDetailItem(" + row.id_invoice + ",\"" + row.kode_invoice + "\");return false;'>";
                                txtAction += "<i class='la la-search'></i>";
                                txtAction += "</a>";

                            return txtAction;
                        },
                    },
                    {
                        field: 'kode_invoice',
                        title: 'Faktur',
                        autoHide: false,
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            return row.kode_invoice.toUpperCase();
                        },
                    },
                    {
                        field: 'tanggal_invoice',
                        title: 'Tanggal Faktur',
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            if (row.tanggal_invoice != null) {
                                return formatDate(row.tanggal_invoice);
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'tanggal_jt',
                        width: 'auto',
                        title: 'Tanggal JT',
                        textAlign: 'center',
                        template: function(row) {
                            if (row.tanggal_jt != null) {
                                return formatDate(row.tanggal_jt);
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'grand_total',
                        title: 'Nominal(Rp)',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.grand_total).toLocaleString('id-ID', { maximumFractionDigits: 2});
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
                            url: "/PurchaseInvoiceCollection/StoreDetail",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idInv : $("#invoice option:selected").val(),
                                idCollection : "{{$dataCollection->id}}",
                                nominalInv : $("#nominalInv").val().replace('.',''),
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Item Berhasil ditambahkan!.",
                                        "success"
                                    )
                                    $("#invoice").val("").trigger('change'),
                                    $("#tglInv").val("");
                                    $("#tglJT").val("");
                                    $("#nominalInv").val("");
                                    var datatable = $('#list_item').KTDatatable();
                                        datatable.setDataSourceParam('idCollection','{{$dataCollection->id}}');
                                        datatable.reload();
                                    footerDataForm('{{$dataCollection->id}}');
                                }
                                else if (result == "failDuplicate") {
                                    Swal.fire(
                                        "Gagal!",
                                        "Surat Jalan ini sudah tersedia pada List Tukar Faktur !",
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

        function viewDetailItem(id,kode) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/PurchaseInvoiceCollection/CetakPreview",
                method: 'POST',
                data: {
                    idInvoice: id,
                },
                success: function(result){
                    $("#txtDetailInvoice").text("Preview Invoice "+kode.toUpperCase());
                    var frame = document.getElementById("framePreview");
                        frame.contentWindow.location.reload(true);
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
                        url: "/PurchaseInvoiceCollection/DeleteDetail",
                        method: 'POST',
                        data: {
                            idDetail: id,
                            mode: "edit"
                        },
                        success: function(result){
                            Swal.fire(
                                "Sukses!",
                                "Data Berhasil dihapus!.",
                                "success"
                            );
                            var datatable = $('#list_item').KTDatatable();
                                datatable.setDataSourceParam('idCollection','{{$dataCollection->id}}');
                                datatable.reload();
                                footerDataForm('{{$dataCollection->id}}');
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

        $("#list_terms").on('click', '.selectTerms', function() {
			var id = $(this).parents('tr:first').find('td:first').text();
			$.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/PurchaseInvoiceCollection/GetTerms",
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

        function footerDataForm(idInv) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/PurchaseInvoiceCollection/GetDataFooter",
                method: 'POST',
                data: {
                    idTf: idInv,
                },
                success: function(result){
                    if (result != "null") {
                        var nominal = result.nominalTf;
                        //var nominalFixed = nominal.toString().replace(".", ",");
                        $("#nominal").val(Math.ceil(nominal));
                        $("#nominalMask").val(parseFloat(Math.ceil(nominal)).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                    }
                    else {
                        $("#nominal").val(0);
                        $("#nominalMask").val(parseFloat(0).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                    }
                }
            });
        }
	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
