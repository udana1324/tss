@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h5 class="card-title text-white">Tambah Account</h5>
					</div>
                    <form action="{{ route('GLAccountSettings.store') }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            <div class="row">
                                <legend class="font-weight-semibold"> Data Account</legend>
                                <div class="col-md-6">

                                    <fieldset>

                                        <div class="separator separator-solid separator-border-2 separator-dark"></div>
                                        <br>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Modul :</label>
                                            <div class="col-sm-12 detailItem">
                                                <select class="form-control select2 req" id="module" name="module">
                                                    <option label="Label"></option>
                                                    <option label="GLKasBank">GL Kas Bank</option>
                                                </select>
                                                <span class="form-text text-danger" style="display:none;">*Harap pilih sub account terlebih dahulu!</span>
                                            </div>
                                        </div>

                                    </fieldset>
                                </div>

                                <div class="col-md-6">
                                    <fieldset>
                                        <div class="separator separator-solid separator-border-2 separator-dark"></div>
                                        <br>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Akun</label>
                                            <div class="col-sm-12">
                                                <select class="form-control select2 detailItem" id="account" name="account">
                                                    <option label="Label"></option>
                                                    @foreach ($dataParent as $parentAcc)
                                                    <optgroup label="{{$parentAcc->account_number}} - {{ucwords($parentAcc->account_name)}}">
                                                        @foreach ($parentAcc->child as $acc)
                                                            <option value="{{$acc->id}}">{{$acc->account_number}} - {{ucwords($acc->account_name)}}</option>
                                                        @endforeach
                                                    </optgroup>
                                                    @endforeach

                                                </select>
                                                <span class="form-text text-danger errItem" style="display:none;">*Harap Pilih Account terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Akun Transaksi</label>
                                            <div class="col-sm-12">
                                                <select class="form-control select2 detailItem" id="account_transaction" name="account_transaction">
                                                    <option label="Label"></option>
                                                    @foreach ($dataParent as $parentAcc)
                                                    <optgroup label="{{$parentAcc->account_number}} - {{ucwords($parentAcc->account_name)}}">
                                                        @foreach ($parentAcc->child as $acc)
                                                            <option value="{{$acc->id}}">{{$acc->account_number}} - {{ucwords($acc->account_name)}}</option>
                                                        @endforeach
                                                    </optgroup>
                                                    @endforeach

                                                </select>
                                                <span class="form-text text-danger errItem" style="display:none;">*Harap Pilih Transaction Account terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Sisi Jurnal</label>
                                            <div class="col-sm-12">
                                                <select class="form-control select2 detailItem" id="default_side" name="default_side">
                                                    <option label="Label"></option>
                                                    <option value="debet">Debet</option>
                                                    <option value="credit">Credit</option>
                                                </select>
                                                <span class="form-text text-danger errItem" style="display:none;">*Harap Pilih Side Account terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Sumber Data</label>
                                            <div class="col-sm-12">
                                                <select class="form-control select2 detailItem" id="source" name="source">
                                                    <option label="Label"></option>
                                                    <option value="penjualan">Penjualan</option>
                                                    <option value="pembelian">Pembelian</option>
                                                    <option value="pemasukan">Pembayaran Piutang</option>
                                                    <option value="pengeluaran">Pembayaran Hutang</option>
                                                </select>
                                                <span class="form-text text-danger errItem" style="display:none;">*Harap Pilih Sumber Data terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Field</label>
                                            <div class="col-sm-12">
                                                <select class="form-control select2 detailItem" id="field" name="field">
                                                    <option label="Label"></option>
                                                    <option value="dpp">Dpp</option>
                                                    <option value="ppn">PPn</option>
                                                    <option value="grand_total">Grand Total</option>
                                                    <option value="nominal_bayar">Nominal Pembayaran</option>
                                                </select>
                                                <span class="form-text text-danger errItem" style="display:none;">*Harap Pilih Side Account terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row">
											<label class="col-lg-3 col-form-label"></label>
											<div class="col-lg-9">
												<button type="button" class="btn btn-primary font-weight-bold" id="btnAddItem">Tambah Account</button>
											</div>
										</div>

                                    </fieldset>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset>
                                        <legend class="text-muted"><h6><i class="la la-list"></i> List Account</h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>

                                        <div class="datatable datatable-bordered datatable-head-custom" id="list_item"></div>

                                    </fieldset>
                                </div>
                            </div>

                        </div>

                        <div class="card-footer bg-white d-sm-flex justify-content-sm-between align-items-sm-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-light-danger font-weight-bold mr-2" onclick="window.location.href = '{{ url('/GLAccountSettings') }}';">Keluar <i class="flaticon2-cancel icon-sm"></i></button>
                            </div>

                            <div class="mt-2 mt-sm-0">
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
            $('#module').select2({
                placeholder: "Pilih Module",
                allowClear: true
            });

            $('#default_side').select2({
                placeholder: "Pilih Default Side",
                allowClear: true
            });

            $('#source').select2({
                placeholder: "Pilih Sumber Data",
                allowClear: true
            });

            $('#field').select2({
                placeholder: "Pilih Kolom Sumber",
                allowClear: true
            });

            $('#account').select2({
                placeholder: "Pilih Akun",
                allowClear: true
            });

            $('#account_transaction').select2({
                placeholder: "Pilih Akun Transaksi",
                allowClear: true
            });

        });

        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        $("#form_add").submit(function(e){
            e.preventDefault();

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

        $(document).ready(function() {

            var datatable = $('#list_item').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/GLAccountSettings/GetDetail',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data : {
                                id : ''
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
                        field: 'account_number',
                        title: 'Account Number',
                        autoHide: false,
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            return row.account_number;
                        },
                    },
                    {
                        field: 'account_name',
                        title: 'Account',
                        width: 'auto',
                        textAlign: 'left',
                        autoHide: false,
                        template: function(row) {
                            if (row.side == "debet") {
                                return "<span>"+ucwords(row.account_name)+"</span>";
                            }
                            else if (row.side == "credit") {
                                return "<span style='padding-left: 30px'>"+ucwords(row.account_name)+"</span>";
                            }
                        },
                    },
                    {
                        field: 'side',
                        title: 'Sisi',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            return "<span>"+ucwords(row.side)+"</span>";
                        },
                    },
                    {
                        field: 'module_source',
                        title: 'Sumber Data',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";

                            if (row.module_source == "penjualan") {
                                txt = "Penjualan";
                            }
                            else if (row.module_source == "pembelian") {
                                txt = "Pembelian";
                            }
                            else if (row.module_source == "pemasukan") {
                                txt = "Pembayaran Piutang";
                            }
                            else if (row.module_source == "pengeluaran") {
                                txt = "Pembayaran Hutang";
                            }

                            return txt;
                        },
                    },
                    {
                        field: 'field_source',
                        title: 'Field',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";

                            if (row.field_source == "dpp") {
                                txt = "Dpp";
                            }
                            else if (row.field_source == "ppn") {
                                txt = "PPn";
                            }
                            else if (row.field_source == "grand_total") {
                                txt = "Grand Total";
                            }
                            else if (row.field_source == "nominal_bayar") {
                                txt = "Nominal Bayar";
                            }

                            return txt;
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
                    title: "Tambah Account?",
                    text: "Apakah data sudah sesuai?",
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
                            url: "/GLAccountSettings/StoreDetail",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idSettings : "",
                                idAccount : $("#account").val(),
                                source : $("#source").val(),
                                field : $("#field").val(),
                                sisi : $("#default_side").val(),
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Account Berhasil ditambahkan!.",
                                        "success"
                                    )
                                    $("#account").val("").trigger('change');
                                    $("#default_side").val("").trigger('change');
                                    $("#source").val("").trigger('change');
                                    $("#field").val("").trigger('change');

                                    var datatable = $('#list_item').KTDatatable();
                                        datatable.setDataSourceParam('id', '');
                                        datatable.reload();
                                }
                                else if (result == "failDuplicate") {
                                    Swal.fire(
                                        "Gagal!",
                                        "Account ini sudah tersedia pada List !",
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
                        url: "/GLAccountSettings/DeleteDetail",
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
                        datatable.setDataSourceParam('id', '');
                        datatable.reload();
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

        function getSubAccount(idAccount) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/GLAccountSettings/GetSubAccount",
                method: 'POST',
                data: {
                    id_account: idAccount,
                },
                success: function(result){
                    $('#id_account_sub').find('option:not(:first)').remove();
                    if (result.length > 0) {
                        for (var i = 0; i < result.length;i++) {
                            var txt = result[i].account_number + ' ' + result[i].account_name.toUpperCase();
                            $("#id_account_sub").append($('<option>', {
                                value:result[i].id,
                                text:txt
                            }));
                        }
                    }
                }
            });
        }

        $("#account").on('change', function() {
            getSubAccount($(this).val());
        });

	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
