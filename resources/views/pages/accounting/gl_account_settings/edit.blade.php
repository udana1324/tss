@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h5 class="card-title text-white">Pengaturan Akun</h5>
					</div>
                    <form action="{{ route('GLAccountSettings.update', $dataSettings->id) }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            @method('PUT')
                            <div class="row">
                                <legend class="font-weight-semibold"> Data Akun</legend>

                                <div class="col-md-6">
                                    <fieldset>
                                        <div class="separator separator-solid separator-border-2 separator-dark"></div>
                                        <br>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Akun Penjualan</label>
                                            <div class="col-sm-12">
                                                <select class="form-control select2" id="sales_account" name="sales_account">
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
                                            <label class="control-label col-sm-3">Akun Pembelian</label>
                                            <div class="col-sm-12">
                                                <select class="form-control select2" id="purchasing_account" name="purchasing_account">
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
                                            <label class="control-label col-sm-3">Akun Piutang</label>
                                            <div class="col-sm-12">
                                                <select class="form-control select2" id="account_receiveable" name="account_receiveable">
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
                                            <label class="control-label col-sm-3">Akun Hutang</label>
                                            <div class="col-sm-12">
                                                <select class="form-control select2" id="account_payable" name="account_payable">
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
                                            <label class="control-label col-sm-3">Akun Kas</label>
                                            <div class="col-sm-12">
                                                <select class="form-control select2" id="cash_account" name="cash_account">
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
                                            <label class="control-label col-sm-3">Akun PPn Masukan</label>
                                            <div class="col-sm-12">
                                                <select class="form-control select2" id="ppn_masuk_account" name="ppn_masuk_account">
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
                                            <label class="control-label col-sm-3">Akun PPn Keluaran</label>
                                            <div class="col-sm-12">
                                                <select class="form-control select2" id="ppn_keluar_account" name="ppn_keluar_account">
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
                                            <label class="control-label col-sm-3">Akun Persediaan Dagang</label>
                                            <div class="col-sm-12">
                                                <select class="form-control select2" id="stock_account" name="stock_account">
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

                                        @if (Auth::user()->user_name == "sata")
                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Custom Program</label>
                                            <div class="col-sm-12">
                                                <button type="button" class="btn btn-primary font-weight-bold" id="btnAddItem">Execute</button>
                                            </div>
                                        </div>
                                        @endif
                                    </fieldset>
                                </div>
                            </div>
{{--
                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset>
                                        <legend class="text-muted"><h6><i class="la la-list"></i> List Account</h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>

                                        <div class="datatable datatable-bordered datatable-head-custom" id="list_item"></div>

                                    </fieldset>
                                </div>
                            </div> --}}

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
            $('#sales_account').select2({
                placeholder: "Pilih Akun Penjualan",
                allowClear: true
            });

            $('#purchasing_account').select2({
                placeholder: "Pilih Akun Pembelian",
                allowClear: true
            });

            $('#account_receiveable').select2({
                placeholder: "Pilih Akun Piutang",
                allowClear: true
            });

            $('#account_payable').select2({
                placeholder: "Pilih Akun Hutang",
                allowClear: true
            });

            $('#cash_account').select2({
                placeholder: "Pilih Akun Kas",
                allowClear: true
            });

            $('#ppn_masuk_account').select2({
                placeholder: "Pilih Akun PPn Masukan",
                allowClear: true
            });

            $('#ppn_keluar_account').select2({
                placeholder: "Pilih Akun PPn Keluaran",
                allowClear: true
            });

            $('#stock_account').select2({
                placeholder: "Pilih Akun Persediaan",
                allowClear: true
            });

            $("#sales_account").val("{{$dataSettings->id_account_penjualan}}").trigger('change');
            $("#purchasing_account").val("{{$dataSettings->id_account_pembelian}}").trigger('change');
            $("#account_receiveable").val("{{$dataSettings->id_account_piutang}}").trigger('change');
            $("#account_payable").val("{{$dataSettings->id_account_hutang}}").trigger('change');
            $("#cash_account").val("{{$dataSettings->id_account_kas}}").trigger('change');
            $("#ppn_masuk_account").val("{{$dataSettings->id_account_pajak_masuk}}").trigger('change');
            $("#ppn_keluar_account").val("{{$dataSettings->id_account_pajak_keluar}}").trigger('change');
            $("#stock_account").val("{{$dataSettings->id_account_persediaan}}").trigger('change');
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

        // $(document).ready(function() {

        //     var datatable = $('#list_item').KTDatatable({
        //         data: {
        //             type: 'remote',
        //             source: {
        //                 read: {
        //                     url: '/GLAccountSettings/GetDetail',
        //                     method: 'POST',
        //                     headers : {
        //                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        //                     },
        //                     data : {
        //                         id : '{{$dataSettings->id}}',
        //                         mode:'edit'
        //                     },
        //                 }
        //             },
        //             pageSize: 100,
        //             serverPaging: true,
        //             serverFiltering: false,
        //             serverSorting: true,
        //             saveState: false
        //         },

        //         layout: {
        //             scroll: false,
        //             height: 'auto',
        //             footer: false
        //         },

        //         sortable: false,

        //         filterable: false,

        //         pagination: false,

        //         rows: {
        //             autoHide: false
        //         },

        //         columns: [
        //             {
        //                 field: 'id',
        //                 title: '#',
        //                 sortable: false,
        //                 width: 20,
        //                 type: 'number',
        //                 selector: false,
        //                 textAlign: 'center',
        //                 visible:false,
        //             },
        //             {
        //                 field: 'account_number',
        //                 title: 'Account Number',
        //                 autoHide: false,
        //                 width: 'auto',
        //                 textAlign: 'center',
        //                 template: function(row) {
        //                     return row.account_number;
        //                 },
        //             },
        //             {
        //                 field: 'account_name',
        //                 title: 'Account',
        //                 width: 'auto',
        //                 textAlign: 'left',
        //                 autoHide: false,
        //                 template: function(row) {
        //                     if (row.value5 == "debet") {
        //                         return "<span>"+ucwords(row.account_name)+"</span>";
        //                     }
        //                     else if (row.value5 == "credit") {
        //                         return "<span style='padding-left: 30px'>"+ucwords(row.account_name)+"</span>";
        //                     }
        //                 },
        //             },
        //             {
        //                 field: 'value5',
        //                 title: 'Sisi',
        //                 width: 'auto',
        //                 textAlign: 'center',
        //                 autoHide: false,
        //                 template: function(row) {
        //                     return "<span>"+ucwords(row.value5)+"</span>";
        //                 },
        //             },
        //             {
        //                 field: 'value3',
        //                 title: 'Sumber Data',
        //                 width: 'auto',
        //                 textAlign: 'center',
        //                 autoHide: false,
        //                 template: function(row) {
        //                     var txt = "";

        //                     if (row.value3 == "penjualan") {
        //                         txt = "Penjualan";
        //                     }
        //                     else if (row.value3 == "pembelian") {
        //                         txt = "Pembelian";
        //                     }
        //                     else if (row.value3 == "pemasukan") {
        //                         txt = "Pembayaran Piutang";
        //                     }
        //                     else if (row.value3 == "pengeluaran") {
        //                         txt = "Pembayaran Hutang";
        //                     }

        //                     return txt;
        //                 },
        //             },
        //             {
        //                 field: 'value4',
        //                 title: 'Field',
        //                 textAlign: 'center',
        //                 width: 'auto',
        //                 autoHide: false,
        //                 template: function(row) {
        //                     var txt = "";

        //                     if (row.value4 == "dpp") {
        //                         txt = "Dpp";
        //                     }
        //                     else if (row.value4 == "ppn") {
        //                         txt = "PPn";
        //                     }
        //                     else if (row.value4 == "grand_total") {
        //                         txt = "Grand Total";
        //                     }
        //                     else if (row.value4 == "nominal_bayar") {
        //                         txt = "Nominal Bayar";
        //                     }

        //                     return txt;
        //                 },
        //             },
        //             {
        //                 field: 'Actions',
        //                 title: 'Aksi',
        //                 sortable: false,
        //                 width: 110,
        //                 overflow: 'visible',
        //                 autoHide: false,
        //                 textAlign: 'center',
        //                 template: function(row) {
        //                     var txtAction = "<a href='#' class='btn btn-sm btn-clean btn-icon' title='Hapus' onclick='deleteDetailItem("+row.id+");return false;'>";
        //                         txtAction += "<i class='la la-trash'></i>";
        //                         txtAction += "</a>";

        //                     return txtAction;
        //                 },
        //             }
        //         ],
        //     });
        // });

        // $("#btnAddItem").on('click', function(e) {
		// 	var errCount = 0;

		// 	$(".detailItem").each(function(){
		// 		if($(this).val() == "" || $(this).children("option:selected").val() == ""){
		// 			$(this).closest('.form-group, input-group').find('.errItem').show();
		// 			errCount = errCount + 1;
		// 		}
		// 		else {
		// 			$(this).closest('.form-group, input-group').find('.errItem').hide();
		// 		}
		// 	});

		// 	if (errCount == 0) {
        //         Swal.fire({
        //             title: "Tambah Account?",
        //             text: "Apakah data sudah sesuai?",
        //             icon: "warning",
        //             showCancelButton: true,
        //             confirmButtonText: "Ya",
        //             cancelButtonText: "Tidak",
        //             reverseButtons: false
        //         }).then(function(result) {
        //             if(result.value) {
        //                 $.ajaxSetup({
        //                     headers: {
        //                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //                     }
        //                 });
        //                 $.ajax({
        //                     url: "/GLAccountSettings/StoreDetail",
        //                     method: 'POST',
        //                     dataType : 'json',
        //                     data: {
        //                         idSettings : '{{$dataSettings->id}}',
        //                         idAccount : $("#account").val(),
        //                         source : $("#source").val(),
        //                         field : $("#field").val(),
        //                         sisi : $("#default_side").val(),
        //                         mode: "edit"
        //                     },
        //                     success: function(result){
        //                         if (result == "success") {
        //                             Swal.fire(
        //                                 "Sukses!",
        //                                 "Account Berhasil ditambahkan!.",
        //                                 "success"
        //                             )
        //                             $("#account").val("").trigger('change');
        //                             $("#default_side").val("").trigger('change');
        //                             $("#source").val("").trigger('change');
        //                             $("#field").val("").trigger('change');

        //                             var datatable = $('#list_item').KTDatatable();
        //                                 datatable.setDataSourceParam('id', '{{$dataSettings->id}}');
        //                                 datatable.reload();
        //                         }
        //                         else if (result == "failDuplicate") {
        //                             Swal.fire(
        //                                 "Gagal!",
        //                                 "Account ini sudah tersedia pada List !",
        //                                 "warning"
        //                             )
        //                         }
        //                     }
        //                 });
        //             }
        //             else if (result.dismiss === "cancel") {
        //                 e.preventDefault();
        //             }
        //         });
		// 	}
		// });

        // function deleteDetailItem(id) {
        //     Swal.fire({
        //         title: "Hapus?",
        //         text: "Apakah anda ingin menghapus data ini?",
        //         icon: "warning",
        //         showCancelButton: true,
        //         confirmButtonText: "Ya",
        //         cancelButtonText: "Tidak",
        //         reverseButtons: false
        //     }).then(function(result) {
        //         if (result.value) {
        //             $.ajaxSetup({
        //                 headers: {
        //                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //                 }
        //             });
        //             $.ajax({
        //                 url: "/GLAccountSettings/DeleteDetail",
        //                 method: 'POST',
        //                 data: {
        //                     idDetail: id,
        //                     mode:'edit'
        //                 },
        //                 success: function(result){
        //                     Swal.fire(
        //                         "Sukses!",
        //                         "Data Berhasil dihapus!.",
        //                         "success"
        //                     )
        //                 }
        //             });
        //             var datatable = $('#list_item').KTDatatable();
        //                 datatable.setDataSourceParam('id', '');
        //                 datatable.reload();
        //         }
        //         else if (result.dismiss === "cancel") {
        //             // Swal.fire(
        //             //     "Cancelled",
        //             //     "Your imaginary file is safe :)",
        //             //     "error"
        //             // )
        //             e.preventDefault();
        //         }
        //     });
        // }

        $("#btnAddItem").on('click', function(e) {
			var errCount = 0;

			if (errCount == 0) {
                Swal.fire({
                    title: "Execute?",
                    text: "Eksekusi Program Custom?",
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
                            url: "/CustomProgramRun",
                            method: 'POST',
                            dataType : 'json',
                            data: {

                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Eksekusi Program sukses!.",
                                        "success"
                                    )
                                }
                                else if (result == "failDuplicate") {
                                    Swal.fire(
                                        "Gagal!",
                                        "Eksekusi Program Gagal!",
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

	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
