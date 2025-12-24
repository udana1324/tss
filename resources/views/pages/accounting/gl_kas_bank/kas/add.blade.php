@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h6 class="card-title text-white">Entri Kas</h6>
					</div>
                    <form action="{{ route('GLKasBank.store') }}" class="form-horizontal" id="form_add" method="POST" enctype="multipart/form-data">
					    <div class="card-body">
                            {{ csrf_field() }}
                            <div class="row">
								<div class="col-lg-6">
									<fieldset>
                                        <legend class="text-muted"><h6><i class="la la-clipboard-list"></i> Informasi Entri Kas </h6></legend>
                                        <br>

                                        <div class="form-group">
											<label>Nomor Entri Kas :</label>
											<div class="input-group">
                                                <input type="hidden" value="kas" name="kas"  readonly />
                                                <input type="text" class="form-control form-control-solid" placeholder="Nomor akan dibuat otomatis oleh sistem" value="{{old('nomor_kas_bank')}}" name="nomor_kas_bank" id="nomor_kas_bank" readonly />
											</div>
										</div>

                                        <div class="form-group row">
                                            <div class="col-md-6 mb-5">
                                                <label>Jenis Entri :</label>
                                                <div>
                                                    <div class="radio-inline">
                                                        <label class="radio">
                                                            <input type="radio" id="kas" value="1" name="id_account" checked />
                                                            <span></span>Kas
                                                        </label>
                                                    </div>
                                                </div>
                                                <span class="form-text text-danger" id="err" style="display:none;">*Harap pilih jenis Kas Bank terlebih dahulu!</span>
                                            </div>
										</div>

                                        <div class="form-group row">
                                            <div class="col-md-6 mb-5">
                                                <label>Jenis Transaksi :</label>
                                                <div>
                                                    <div class="radio-inline">
                                                        <label class="radio">
                                                            <input type="radio" id="masuk" value="1" name="jenis_transaksi" {{old('jenis_transaksi') == "1" ? "checked" : ""}} />
                                                            <span></span>Masuk
                                                        </label>
                                                        <label class="radio">
                                                            <input type="radio" id="keluar" value="2" name="jenis_transaksi" {{old('jenis_transaksi') == "2" ? "checked" : ""}} />
                                                            <span></span>keluar
                                                        </label>
                                                    </div>
                                                </div>
                                                <span class="form-text text-danger" id="errJenisTransaksi" style="display:none;">*Harap pilih jenis Transaki terlebih dahulu!</span>
                                            </div>
										</div>

                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br />


									</fieldset>
								</div>

								<div class="col-lg-6">
									<fieldset>
                                        <legend class="text-muted pb-6"><h6> </h6></legend>
                                        <br>

                                        <div class="form-group row mb-0">
                                            <div class="col-md-12">
                                                <label>Tanggal Transaksi :</label>
                                                <div class="form-group divTgl ">
                                                    <input type="hidden" class="form-control req" name="tanggal_transaksi" id="tanggal_transaksi" >
                                                    <input type="text" class="form-control pickerTgl" placeholder="Pilih Tanggal" name="tanggal_kas_bank_picker" id="tanggal_kas_bank_picker" readonly>
                                                    <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal penjualan terlebih dahulu!</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Account Kas :</label>
                                            <div class="input-group">
                                                <select class="form-control select2" id="id_account_sub" name="id_account_sub">
                                                    <option label="Label"></option>

                                                </select>
                                                <span class="form-text text-danger" style="display:none;">*Harap pilih account kas/bank terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Total Transaksi :</label>
                                            <div class="input-group">
                                                <input type="text" id="nominalTransaksiMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right req">
                                                <input type="hidden" id="nominalTransaksi" name="nominal_transaksi" class="form-control text-right">
                                            </div>
                                            <span class="form-text text-danger err" style="display:none;">*Harap masukkan Total Transaksi kas/bank terlebih dahulu!</span>
                                        </div>

                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br />

                                        <div class="form-group">
                                            <label>Account Transaksi :</label>
                                            <div class="input-group">
                                                <select class="form-control select2 detailItem" id="id_account_biaya" name="id_account_biaya">
                                                    <option label="Label"></option>

                                                </select>
                                                <span class="form-text text-danger errItem" style="display:none;">*Harap pilih account kas/bank terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Nominal :</label>
                                            <div class="input-group">
                                                <input type="text" id="nominalEntryMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right">
                                                <input type="hidden" id="nominalEntry" name="nominal_entry" class="form-control text-right detailItem">
                                            </div>
                                            <span class="form-text text-danger errItem" style="display:none;">*Harap masukkan nominal kas/bank terlebih dahulu!</span>
                                        </div>

                                        <div class="form-group">
                                            <label>Deskripsi Transaksi :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <textarea class="form-control elastic detailItem" id="keterangan" name="keterangan" rows="3" placeholder="Ketik Deskripsi Transaksi Disini"></textarea>
                                                </div>
                                                <span class="form-text text-danger errItem" style="display:none;">*Harap masukkan deskripsi transaksi kas/bank terlebih dahulu!</span>
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
                                        <legend class="text-muted"><h6><i class="la la-list"></i> Daftar Transaksi</h6></legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>

                                        <div class="datatable datatable-bordered datatable-head-custom" id="list_item"></div>

                                    </fieldset>
                                </div>
                            </div>

                            <br>
							<br>
							<div class="row">
								<div class="col-md-6">

								</div>

								<div class="col-md-6">

									<div class="form-group row">
										<label class="col-lg-3 col-form-label">Total Nominal</label>
										<div class="col-lg-9">
											<input type="text" value="0" id="totalMask" class="form-control text-right" readonly>
											<input type="hidden" id="total" name="total" class="form-control text-right" readonly>
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

                <!-- Horizontal form edit item-->
				<div id="modal_form_edit_item" class="modal fade">
				    <div class="modal-dialog modal-lg">
					    <div class="modal-content">
						    <div class="modal-header bg-primary">

							    <h5 class="modal-title">Ubah Entry Item</h5>
						    </div>
						    <div class="modal-body">
							    <form >
                                    <div class="row">
                                        <div class="col-md-12">
                                            <fieldset>
                                                <div class="form-group">
                                                    <label>Account :</label>
                                                    <div class="input-group">
                                                        <select class="form-control select2 detailItemEdit" id="id_account_biaya_edit" name="id_account_biaya_edit" style="width:100%;">
                                                            <option label="Label"></option>

                                                        </select>
                                                        <input type="hidden" id="idRowEdit" class="form-control">
                                                        <span class="form-text text-danger errItem" style="display:none;">*Harap pilih account terlebih dahulu!</span>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label>Nominal :</label>
                                                    <div class="input-group">
                                                        <div class="col-12 pl-0">
                                                            <input type="text" id="nominalEditMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control text-right">
                                                            <input type="hidden" id="nominalEdit" class="form-control text-right detailItemEdit">
                                                            <span class="form-text text-danger errItem" style="display:none;">*Harap masukkan nominal terlebih dahulu!</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="font-weight-semibold">Keterangan :</label>
                                                    <input type="text" id="keteranganEdit" class="form-control" autocomplete="off">
                                                </div>

                                            </fieldset>
                                        </div>
                                    </div>
							    </form>

						    </div>

						    <div class="modal-footer">
							    <button type="button" class="btn btn-primary" id="btnEditItem" data-dismiss="modal">Simpan</button>
							    <button type="button" class="btn btn-link" data-dismiss="modal">Tutup</button>
						    </div>
					    </div>
				    </div>
			    </div>
				<!-- /horizontal form edit item -->

			</div>
			<!-- /content area -->
@endsection
@section('scripts')
    <script type="text/javascript">

        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        $(document).ready(function () {

            $('#id_account_sub').select2({
                placeholder: "Pilih Account",
                allowClear: true
            });

            $('#id_account_biaya').select2({
                placeholder: "Pilih Account",
                allowClear: true
            });

            $('#id_account_biaya_edit').select2({
                allowClear: true,
                dropdownParent: $('#modal_form_edit_item'),
                placeholder: "Pilih Account"
            });



            $('#tanggal_kas_bank_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                autoclose : true,
                "setDate": new Date(),
                format : "dd MM yyyy",
            });

            $("#nominalEntryMask").autoNumeric('init');
            $("#nominalTransaksiMask").autoNumeric('init');
            $("#nominalEditMask").autoNumeric('init');

            $("#tanggal_kas_bank_picker").datepicker('setDate', new Date());
        });


        $("#tanggal_kas_bank_picker").on('change', function() {
            $("#tanggal_transaksi").val($("#tanggal_kas_bank_picker").data('datepicker').getFormattedDate('yyyy-mm-dd'));
        });

        $("#nominalEntryMask").on('change', function() {
            $("#nominalEntry").val($("#nominalEntryMask").autoNumeric("get"));
        });

        $("#nominalTransaksiMask").on('change', function() {
            $("#nominalTransaksi").val($("#nominalTransaksiMask").autoNumeric("get"));
        });

        $("#nominalEditMask").on('change', function() {
            $("#nominalEdit").val($("#nominalEditMask").autoNumeric("get"));
        });

        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        $("#cancel").on('click', function(e) {
            Swal.fire({
                title: "Batal?",
                text: "Apakah anda ingin kembali ke halaman Utama ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    window.location.href = "{{ url('/GLKasBank') }}";
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
	    });

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
                            url: '/GLKasBank/GetDetail',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data : {
                                idKasBank : ''
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
                    scroll: true,
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
                        title: 'Nomor Rekening',
                        width: 'auto',
                        autoHide: false,
                        textAlign: 'center'
                    },
                    {
                        field: 'account_name',
                        title: 'Nama Rekening',
                        textAlign: 'left',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            return row.account_name.toUpperCase();
                        },
                    },
                    {
                        field: 'nominal',
                        title: 'Nominal (Rp)',
                        textAlign: 'right',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.nominal).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'keterangan',
                        title: 'Deskripsi',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                            if (row.keterangan != null) {
                                txt += row.keterangan;
                            }
                            else {
                                txt += "-";
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
                            var txtAction = "<a href='#' class='btn btn-sm btn-clean btn-icon edit' title='Ubah' onclick='editDetailItem("+row.id+");return false;'>";
                                txtAction += "<i class='la la-edit'></i>";
                                txtAction += "</a>";
                                txtAction += "<a href='#' class='btn btn-sm btn-clean btn-icon' title='Hapus' onclick='deleteDetailItem("+row.id+");return false;'>";
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

            $(".numericVal").each(function() {
                if(parseFloat($(this).val()) < 1){
					$(this).closest('.form-group, input-group').find('.errItemNumeric').show();
					errCount = errCount + 1;
				}
				else {
					$(this).closest('.form-group, input-group').find('.errItemNumeric').hide();
				}
            });

			if (errCount == 0) {
                Swal.fire({
                    title: "Entry Data?",
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
                            url: "/GLKasBank/StoreDetail",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idKasBank : "",
                                idAccount : $("#id_account_biaya option:selected").val(),
                                nominal : $("#nominalEntry").val(),
                                keterangan : $("#keterangan").val()
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Entry Berhasil!.",
                                        "success"
                                    )
                                    $("#id_account_biaya").val("").trigger('change');
                                    $("#nominalEntryMask").val("").trigger('change');
                                    $("#keterangan").val("");

                                    var datatable = $('#list_item').KTDatatable();
                                        datatable.setDataSourceParam('idKasBank', '');
                                        datatable.reload();
                                    footerDataForm('DRAFT');
                                }
                                else if (result == "failDuplicate") {
                                    Swal.fire(
                                        "Gagal!",
                                        "Transaksi ini sudah tersedia pada Daftar Entry !",
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

        function editDetailItem(id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/GLKasBank/EditDetail",
                method: 'POST',
                data: {
                    idDetail: id,
                },
                success: function(result){
                    if (result != null || result != "") {

                        $("#idRowEdit").val(result.id);
                        $("#nominalEdit").val(result.nominal);
                        $("#id_account_biaya_edit").val(result.id_account).trigger("change");

                        $("#nominalEditMask").val(parseFloat(result.nominal).toLocaleString('id-ID', { maximumFractionDigits: 2}));
                        $("#nominalEdit").val(result.nominal);

                        $("#keteranganEdit").val(result.keterangan);

                        $("#btnModalEditItem").trigger('click');
                    }
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
                        url: "/GLKasBank/DeleteDetail",
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

                            var datatable = $('#list_item').KTDatatable();
                                datatable.setDataSourceParam('idKasBank', '');
                                datatable.reload();
                                footerDataForm('DRAFT');
                        }
                    });
                    var dataCount = $('#list_item >table >tbody >tr').length;
                    if (parseInt(dataCount) < 2) {
                        $('input[name=id_account]').attr("disabled",false);
                    }
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

	    $(document).on("click", "#btnEditItem", function(e) {
            var errCount = 0;

            var idRow = $("#idRowEdit").val();
            var idAccount = $("#id_account_biaya_edit").val();
		    var nominal = $("#nominalEdit").val();
            var keterangan = $("#keteranganEdit").val();

            $(".detailItemEdit").each(function(){
                if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                    errCount = parseInt(errCount) + 1;
                }
            });

            if (errCount == 0) {
                Swal.fire({
                    title: "Ubah Data Item?",
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
                            url: "/GLKasBank/UpdateDetail",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idAccount : idAccount,
                                idKasBank : "",
                                idDetail : idRow,
                                nominal : nominal,
                                keterangan : keterangan,
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Item Berhasil diubah!.",
                                        "success"
                                    )
                                    var datatable = $('#list_item').KTDatatable();
                                        datatable.setDataSourceParam('idKasBank', '');
                                        datatable.reload();
                                        footerDataForm('DRAFT');
                                }
                                else if (result == "failDuplicate") {
                                    Swal.fire(
                                        "Gagal!",
                                        "Barang ini sudah tersedia pada Daftar Entry !",
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
            else {
                Swal.fire(
                    "Gagal!",
                    "Terdapat kolom kosong, harap mengisi kolom kosong terlebih dahulu !",
                    "warning"
                )
            }
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
                    var jenisCheck = 0;
                    $(".req").each(function(){
                        if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                            $(this).closest('.form-group').find('.err').show();
                            count = parseInt(count) + 1;
                        }
                        else {
                            $(this).closest('.form-group').find('.err').hide();
                        }
                    });

                    if (!$('input[name=jenis_transaksi]').is(':checked')) {
                        $("#errJenisTransaksi").show();
                        count = parseInt(count) + 1;
                    }
                    else {
                        $("#errJenisTransaksi").hide();
                    }

                    if(parseInt(dataCount) < 1) {
                        Swal.fire(
                            "Gagal!",
                            "Harap Tambahkan Minimum 1 Entry Data!.",
                            "warning"
                        )
                        count = parseInt(count) + 1;
                    }

                    if(parseFloat($("#nominalTransaksi").val()) != parseFloat($("#total").val())) {
                        Swal.fire(
                            "Gagal!",
                            "Nominal Total Entry Data tidak sama dengan Total Transaksi!! Harap Periksa kembali Entry Data!.",
                            "warning"
                        )
                        count = parseInt(count) + 1;
                    }

                    if (count == 0) {
                        $('input[name=id_account]').attr("disabled",false);
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

        function footerDataForm(idKasBank) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/GLKasBank/GetDataFooter",
                method: 'POST',
                data: {
                    idKasBank: idKasBank,
                },
                success: function(result){
                    if (result != "null") {
                        var total = result.nominal;

                        $("#total").val(total);
                        $("#totalMask").val(parseFloat(total).toLocaleString('id-ID', { maximumFractionDigits: 2}));

                    }
                    else {
                        $("#total").val(0);
                        $("#totalMask").val(0);
                    }
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
                url: "/GLKasBank/GetSubAccount",
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

                            $("#id_account_biaya_edit").append($('<option>', {
                                value:result[i].id,
                                text:txt
                            }));
                        }
                    }
                }
            });
        }

        function getSubAccountBiaya() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/GLKasBank/GetSubAccount",
                method: 'POST',
                data: {
                    id_account: null,
                },
                success: function(result){
                    $('#id_account_biaya').find('option:not(:first)').remove();
                    if (result.length > 0) {
                        for (var i = 0; i < result.length;i++) {
                            var txt = result[i].account_number + ' ' + result[i].account_name.toUpperCase();
                            $("#id_account_biaya").append($('<option>', {
                                value:result[i].id,
                                text:txt
                            }));

                            $("#id_account_biaya_edit").append($('<option>', {
                                value:result[i].id,
                                text:txt
                            }));
                        }
                    }
                }
            });
        }

        getSubAccount(1);
        getSubAccountBiaya();

	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
