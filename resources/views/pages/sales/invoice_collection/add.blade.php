@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h5 class="card-title text-white">Buat Tukar Faktur</h5>
					</div>
                    <form action="{{ route('SalesInvoiceCollection.store') }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            <div class="row">
								<div class="col-md-6">
									<fieldset>
										<legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi Pembeli / Customer </h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
										<div class="form-group row d-none">
                                            <label class="col-lg-3 col-form-label">No. Tukar Faktur :</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control bg-slate-600 border-slate-600 border-1" placeholder="Auto Generated" name="kode_tf" id="kode_tf" readonly>
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
                                            <label>Alamat Customer :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <input type="hidden" class=" form-control req" name="id_alamat" id="id_alamat">
                                                    <textarea class="form-control" name="alamat" id="alamat" style="resize:none;" readonly></textarea>
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-primary" id="btnAlamat" data-toggle="modal" data-target="#modal_list_alamat">Pilih Alamat</button>
                                                    </div>
                                                </div>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih alamat customer terlebih dahulu!</span>
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
                                            <label>Tanggal Tukar Faktur :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <input type="hidden" class="form-control req" name="tanggal_tf" id="tanggal_tf">
                                                <input type="text" class="form-control" name="tanggal_tf_picker" id="tanggal_tf_picker" readonly>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal Tukar Faktur terlebih dahulu!</span>
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
                                        <div class="row align-items-center">
                                            <div class="col-lg-12 col-xl-8">
                                                <div class="row align-items-center">
                                                    <div class="col-md-3 my-2 my-md-0">
                                                        <div class="align-items-center">
                                                            <label style="display: inline-block;"></label>
                                                            <div class="input-icon">
                                                                <input type="text" class="form-control" placeholder="Search..." id="list_item_search_query"/>
                                                                <span>
                                                                    <i class="flaticon2-search-1 text-muted"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 my-2 my-md-0">
                                                        <div class=" align-items-center">
                                                            <label class="mr-3 mb-0 d-none d-md-block" style="color:white;">Hapus Sekaligus</label>
                                                            <button type="button" id="btnMass" class="btn btn-success border-white">Hapus Sekaligus</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
                <!-- Modal form list alamat -->
				<div id="modal_list_alamat" class="modal fade">
				    <div class="modal-dialog modal-lg">
					    <div class="modal-content">
						    <div class="modal-header bg-primary">

							    <h5 class="modal-title">List Alamat Customer</h5>
						    </div>
						    <div class="modal-body">
							    <form >
								    <table class="datatable-bordered datatable-head-custom ml-4" id="list_alamat" width="100%">
									    <thead>
										    <tr>
											    <th align="center" style="text-align:center;display:none;">ID</th>
												<th align="center" style="text-align:center;">Alamat</th>
												<th align="center" style="text-align:center;">Jenis Alamat</th>
												<th align="center" style="text-align:center;">PIC</th>
												<th align="center" style="text-align:center;">No. Telp PIC</th>
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
                <!-- Modal form invoice -->
				<div id="modal_detail_invoice" class="modal fade">
				    <div class="modal-dialog modal-xl">
					    <div class="modal-content">
						    <div class="modal-header bg-primary">

							    <h5 class="modal-title" id="txtDetailInvoice"></h5>
						    </div>
						    <div class="modal-body">
                                <div style="text-align: center;" id="container_iframe">
                                    <iframe src="{{ url('preview/preview_invoice.pdf') }}?time={{date('Y-m-d H:i:s')}}" id="framePreview" frameborder="0" style="width: 850px;height: 700px;"></iframe>
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
            $('#customer').select2({
                allowClear: true,
                placeholder: "Pilih Nama Pelanggan"
            });

            $('#invoice').select2({
                allowClear: true,
                placeholder: "Pilih Faktur"
            });

            $('#company_account').select2({
                allowClear: true,
                placeholder: "Pilih Nomor Rekening"
            });


            $('#tanggal_tf_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                autoclose : true,
                format : "dd MM yyyy",
            });
            $("#tanggal_tf_picker").datepicker('setDate', new Date());
            $("#company_account").val("{{$dataPreference->rekening}}").trigger("change");
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
                text: "Apakah anda ingin membatalkan pembuatan Tukar Faktur?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    window.location.href = "{{ url('/SalesInvoiceCollection') }}";
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
                            "Harap Tambahkan Minimum 1 Faktur!.",
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
            if ($(this).val() != "") {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/SalesInvoiceCollection/GetDefaultAddress",
                    method: 'POST',
                    data: {
                        idCustomer: $(this).val(),
                    },
                    success: function(result){
                        if (result != null) {
                            $("#id_alamat").val(result.id);
                            $("#alamat").val(ucwords(result.alamat_customer));
                        }
                    }
                });

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/SalesInvoiceCollection/SetDetail",
                    method: 'POST',
                    data: {
                        idInvoice: '',
                        idCustomer: $(this).val(),
                    },
                    success: function(result){
                        if (result != "") {
                            var datatable = $('#list_item').KTDatatable();
                                datatable.setDataSourceParam('idCustomer', $("#customer").val());
                                datatable.setDataSourceParam('idCollection', '');
                                datatable.reload();
                                footerDataForm('DRAFT');
                                getInvoiceDate('DRAFT');
                        }
                    }
                });

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/SalesInvoiceCollection/GetInvoice",
                    method: 'POST',
                    data: {
                        idCustomer:  $(this).val(),
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

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/Delivery/GetCustomerAddress",
                    method: 'POST',
                    data: {
                        id_customer: $(this).val(),
                    },
                    success: function(result){
                        if (result.length > 0) {
                            $('#list_alamat tbody').empty();
                            if (result.length > 0) {
                                for (var i = 0; i < result.length;i++) {
                                    var idAlamat = result[i].id;
                                    var alamat = result[i].alamat_customer;
                                    var jenisAlamat = result[i].jenis_alamat;
                                    var pic = result[i].pic_alamat;
                                    var tlpPic = result[i].telp_pic;
                                    var data="<tr>";
                                        data +="<td style='text-align:center;display:none;'>"+idAlamat+"</td>";
                                        data +="<td style='text-align:left;word-wrap:break-word;min-width:160px;max-width:160px;'>"+ucwords(alamat)+"</td>";
                                        data +="<td style='text-align:center;'>"+jenisAlamat+"</td>";
                                        data +="<td style='text-align:center;'>"+ucwords(pic)+"</td>";
                                        data +="<td style='text-align:center;'>"+tlpPic+"</td>";
                                        data +="<td style='text-align:center;'><button type='button' data-dismiss='modal' class='btn btn-primary btn-icon select'>Pilih</button></td>";
                                        data +="</tr>";
                                        $("#list_alamat").append(data);
                                }
                            }
                        }
                    }
                });
            }
            else {
                $("#id_alamat").val("");
                $("#alamat").val("");
            }
        });

        $("#invoice").on("change", function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/SalesInvoiceCollection/GetInvoiceData",
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

        function viewDetailItem(id,kode) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/SalesInvoiceCollection/CetakPreview",
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

        function getInvoiceDate(idInv) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/SalesInvoiceCollection/GetInvoiceDate",
                method: 'POST',
                data: {
                    idInvoice: idInv,
                },
                success: function(result){
                    if (result != "null") {
                        var tglMax = result.lastDate;
                        var max = new Date(tglMax);
                        var today = new Date();
                        var selisih = Math.floor((Date.UTC(max.getFullYear(), max.getMonth(), max.getDate()) - Date.UTC(today.getFullYear(), today.getMonth(), today.getDate()) ) /(1000 * 60 * 60 * 24));
                        if (selisih < 0) {
                            $("#tanggal_tf_picker").datepicker('setDate', new Date(tglMax));
                        }
                        else {
                            $("#tanggal_tf_picker").datepicker('setDate', new Date());
                        }

                    }
                }
            });
        }

        $("#list_item").on("click", "table .checkAll", function(){
            $("#list_item .hapusSekaligus:checkbox").prop('checked', $(this).prop("checked"));
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
                            url: '/SalesInvoiceCollection/GetDetail',
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
                        field: 'checkbox',
                        sortable: false,
                        autoHide: false,
                        title: "<div class='checkbox-inline align-items-center'><label class='checkbox checkbox-lg'><input type='checkbox' id='checkAll' class='text-center checkAll'><span></span></label></div>",
                        textAlign: 'center',
                        width: '50',
                        template: function(row) {
                            var txtCheckbox = "<div class='checkbox-inline align-items-center'>";
                                txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                txtCheckbox += "<input type='checkbox' class='text-center hapusSekaligus' value='"+row.id+"'>";
                                txtCheckbox += "<span></span>";
                                txtCheckbox += "</label>";
                                txtCheckbox += "</div>";

                            return txtCheckbox;

                        },
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
                        textAlign: 'center',
                        width: 'auto',
                        template: function(row) {
                            return row.kode_invoice.toUpperCase();
                        },
                    },
                    {
                        field: 'tanggal_invoice',
                        title: 'Tanggal Faktur',
                        textAlign: 'center',
                        width: 'auto',
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
                        title: 'Tanggal JT',
                        width: 'auto',
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
                            url: "/SalesInvoiceCollection/StoreDetail",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idInv : $("#invoice option:selected").val(),
                                idTf : "",
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
                                        datatable.setDataSourceParam('idCollection','');
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
                        url: "/SalesInvoiceCollection/DeleteDetail",
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
                        datatable.setDataSourceParam('idCollection','');
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

        $("#btnMass").on('click', function(e) {
            var check = $('.hapusSekaligus:checkbox:checked').length;
            if (check < 1) {
                Swal.fire(
                    "Peringatan!",
                    "Harap pilih invoice yang akan dihapus!",
                    "warning"
                )
            }
            else {
                var invoiceIDs = $("#list_item .hapusSekaligus:checkbox:checked").map(function(){
                                return $(this).val();
                            }).get();
                            console.log(invoiceIDs);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "/SalesInvoiceCollection/DeleteDetail",
                    method: 'POST',
                    data: {
                        idDetail: invoiceIDs,
                        massDelete : 'Yes',
                    },
                    success: function(result){
                        Swal.fire(
                            "Sukses!",
                            "Data Berhasil dihapus!.",
                            "success"
                        );
                        var datatable = $('#list_item').KTDatatable();
                            datatable.setDataSourceParam('idCollection','');
                            datatable.reload();
                            footerDataForm('DRAFT');
                            getInvoiceDate('DRAFT');
                        $('.checkAll:checkbox:checked').prop('checked', false);
                    }
                });

            }
		});

        function footerDataForm(idInv) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/SalesInvoiceCollection/GetDataFooter",
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
