@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header card-header-tabs-line">
                        <div class="card-toolbar">
                            <ul class="nav nav-tabs nav-bold nav-tabs-line">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#tab_pane_1">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Data Ekspedisi</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab_pane_2" id="tab2">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Cabang Ekspedisi</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab_pane_3" id="tab3">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Tarif Ekspedisi</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
					</div>

					<div class="card-body">
						<form action="{{ route('Expedition.store') }}" method="POST" id="form_add">
						{{ csrf_field() }}
						<div class="tab-content">
							<div class="tab-pane fade show active" id="tab_pane_1" role="tabpanel" aria-labelledby="tab_pane_1">
								<div class="row">
									<div class="col-md-6">
										<fieldset>
											<legend class="font-weight-semibold"></legend>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Nama Ekspedisi</label>
												<div class="col-lg-9">
                                                    <input type="text" class="form-control req" placeholder="Masukkan Nama Ekspedisi" name="nama_ekspedisi" id="nama_ekspedisi">
                                                    <span class="form-text text-danger err" style="display:none;">*Harap masukkan nama ekspedisi terlebih dahulu!</span>
												</div>
											</div>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Nama Perusahaan</label>
												<div class="col-lg-9">
                                                    <input type="text" placeholder="Masukkan Nama Perusahaan Ekspedisi" class="form-control req" name="nama_perusahaan" id="nama_perusahaan">
                                                    <span class="form-text text-danger err" style="display:none;">*Harap masukkan nama perusahaan ekspedisi terlebih dahulu!</span>
												</div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label">No. Telp</label>
                                                <div class="col-lg-9">
                                                    <input type="text" class="form-control" maxlength="14" onkeypress="return validasiangka(event);" name="telp_perusahaan" id="telp_perusahaan" autocomplete="off">
                                                    <span class="form-text text-muted">Contoh : 0812123456789</span>
                                                    <span class="form-text text-danger err" style="display:none;">*Harap masukkan no. telp terlebih dahulu!</span>
                                                </div>
                                            </div>

										</fieldset>
									</div>

								</div>
							</div>

							<div class="tab-pane fade" id="tab_pane_2" role="tabpanel" aria-labelledby="tab_pane_2">
                                <!--begin: Search Form-->
                                <!--begin::Search Form-->
                                <div class="mb-7">
                                    <div class="row align-items-center">
                                        <div class="col-lg-6">
                                            <div class="row align-items-center">
                                                <div class="col-md-4 my-2 my-md-0">
                                                    <div class="input-icon">
                                                        <input type="text" class="form-control" placeholder="Search..." id="table_cabang_search_query"/>
                                                        <span>
                                                            <i class="flaticon2-search-1 text-muted"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 align-items-right" align="right">
                                            <div class="row align-items-right">
                                                <div class="col-md-11 my-md-0 align-items-right">
                                                    <button type="button" class="btn btn-primary font-weight-bold mr-2" data-toggle="modal" data-target="#modal_form_horizontal_add"> Tambah Cabang Baru</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Search Form-->
                                <!--end: Search Form-->
                                <!--begin: Datatable-->

                                <div class="datatable datatable-bordered datatable-head-custom" id="table_cabang"></div>

                                <!--end: Datatable-->

								<div class="row">
									<div class="col-md-12">
										<fieldset>
											<legend class="font-weight-semibold"></legend>

											<div class="form-group row">
												<div class="col-lg-12 text-center">
													<span class="form-text text-danger errTbl" id="errTbl" style="display:none;">*Harap tambahkan Minimum 1 Cabang terlebih dahulu!</span>
												</div>
											</div>

										</fieldset>
									</div>
								</div>
							</div>

                            <div class="tab-pane fade" id="tab_pane_3" role="tabpanel" aria-labelledby="tab_pane_3">
                                <!--begin::Search Form-->
                                <div class="mb-7">
                                    <div class="row align-items-center">
                                        <div class="col-lg-6">
                                            <div class="row align-items-center">
                                                <div class="col-md-4 my-2 my-md-0">
                                                    <div class="input-icon">
                                                        <input type="text" class="form-control" placeholder="Search..." id="table_tarif_search_query"/>
                                                        <span>
                                                            <i class="flaticon2-search-1 text-muted"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 align-items-right" align="right">
                                            <div class="row align-items-right">
                                                <div class="col-md-11 my-md-0 align-items-right">
                                                    <button type="button" class="btn btn-primary font-weight-bold mr-2" data-toggle="modal" data-target="#modal_form_tarif"> Tambah Tarif Baru</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end: Search Form-->
                                <!--begin: Datatable-->

                                <div class="datatable datatable-bordered datatable-head-custom" id="table_tarif"></div>

                                <!--end: Datatable-->

								<div class="row">
									<div class="col-md-12">
										<fieldset>
											<legend class="font-weight-semibold"></legend>

											<div class="form-group row">

											</div>

											<div class="form-group row">
												<div class="col-lg-12 text-center">
													<span class="form-text text-danger errTbl" id="errTbl" style="display:none;">*Harap tambahkan Minimum 1 Cabang terlebih dahulu!</span>
												</div>
											</div>

										</fieldset>
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
				</div>
				<!-- Horizontal form modal Add-->
				<div id="modal_form_horizontal_add" class="modal fade">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header bg-primary">
								<h5 class="modal-title">Tambah Cabang Ekspedisi</h5>
							</div>

							<form class="form-horizontal" id="add-cabang">
							{{ csrf_field() }}
							<div class="modal-body">
							    <div class="form-group row">
									<label class="col-lg-3 col-form-label">Nama Cabang</label>
									<div class="col-lg-9">
                                        <input type="text" class="form-control detilAlamat" placeholder="Masukkan Nama Cabang" id="nama_cabang" autocomplete="off">
                                        <span class="form-text text-danger errAlamat" style="display:none;">*Harap masukkan nama cabang terlebih dahulu!</span>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Alamat Cabang</label>
									<div class="col-lg-9">
										<input type="hidden" class="form-control" id="idAlamat">
										<textarea class="form-control detilAlamat" id="alamat_cabang" cols="4"></textarea>
										<span class="form-text text-danger errAlamat" style="display:none;">*Harap masukkan alamat cabang terlebih dahulu!</span>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Kota</label>
									<div class="col-lg-9">
										<input type="text" class="form-control detilAlamat" placeholder="Masukkan Kota" id="kotaAlamat" autocomplete="off">
										<span class="form-text text-danger errAlamat" style="display:none;">*Harap masukkan kota terlebih dahulu!</span>
									</div>
								</div>

                                <div class="form-group row">
									<label class="col-lg-3 col-form-label">No. Telp</label>
									<div class="col-lg-9">
										<input type="text" class="form-control detilAlamat" maxlength="14" onkeypress="return validasiangka(event);" id="telp_cabang" autocomplete="off">
										<span class="form-text text-muted">Contoh : 0812123456789</span>
										<span class="form-text text-danger errAlamat" style="display:none;">*Harap masukkan no. telp cabang terlebih dahulu!</span>
									</div>
								</div>

							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-link" data-dismiss="modal">Batal</button>
								<button type="button" id="btnSimpanAlamat" class="btn btn-primary">Simpan</button>
							</div>
							</form>

						</div>
					</div>
				</div>
				<!-- /horizontal form Add -->

                <!-- Horizontal form modal Tarif-->
				<div id="modal_form_tarif" class="modal fade">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header bg-primary">
								<h5 class="modal-title">Tambah Tarif Ekspedisi</h5>
							</div>

							<form class="form-horizontal" id="add-tarif">
							{{ csrf_field() }}
							<div class="modal-body">
							    <div class="form-group row">
									<label class="col-lg-3 col-form-label">Nama Kota</label>
									<div class="col-lg-9">
                                        <input type="hidden" class="form-control" id="idTarif">
                                        <input type="text" class="form-control detilTarif" placeholder="Masukkan Nama Kota" id="nama_kota" autocomplete="off">
                                        <span class="form-text text-danger errTarif" style="display:none;">*Harap masukkan nama kota terlebih dahulu!</span>
									</div>
								</div>

                                <div class="form-group row">
									<label class="col-lg-3 col-form-label">Tarif</label>
									<div class="col-lg-9">
										<input type="text" id="tarifMask" autocomplete="off" data-a-dec="," data-a-sep="." class="form-control pltbItem" value="0" >
                                        <input type="hidden" name="tarif" id="tarif" value="0" class="form-control text-right detilTarif">
										<span class="form-text text-danger errTarif" style="display:none;">*Harap masukkan nominal tarif terlebih dahulu!</span>
									</div>
								</div>

							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-link" data-dismiss="modal">Batal</button>
								<button type="button" id="btnSimpanTarif" class="btn btn-primary">Simpan</button>
							</div>
							</form>

						</div>
					</div>
				</div>
				<!-- /horizontal form Tarif -->
			<!-- /content area -->
@endsection
@section('scripts')
    <script type="text/javascript">

        $(document).ready(function() {
            $("#tarifMask").autoNumeric('init');
        });

        $("#tarifMask").on('change', function() {
            $("#tarif").val($("#tarifMask").autoNumeric("get"));
        });

        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

		$("#cancel").on('click', function(e) {
            Swal.fire({
                title: "Batal?",
                text: "Apakah anda ingin membatalkan penambahan Ekspedisi?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    window.location.href = "{{ url('/Expedition') }}";
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
	    });

		function validasiangka(evt) {
          var charCode = (evt.which) ? evt.which : event.keyCode
           if (charCode > 31 && (charCode < 48 || charCode > 57))

            return false;
          return true;
        }

        $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
	        $(".req").each(function(){
				if($(this).val() == "" || $(this).children("option:selected").val() == ""){
				   	$(this).closest('.form-group').find('.err').show();
				  	e.preventDefault();
				}
				else {
					$(this).closest('.form-group').find('.err').hide();
				}
			});
	    });

        $("#form_add").submit(function(e){
            e.preventDefault();
            var datatable = $('#table_cabang').KTDatatable();
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
                        $("#errTbl").show();
                        $("#tab2").trigger('click');
                        count = parseInt(count) + 1;
                    }
                    else {
                        $("#errTbl").hide();
                    }

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

		$("#btnSimpanAlamat").on('click', function(e) {
			var errCount = 0;

			$(".detilAlamat").each(function(){
				if($(this).val() == "" || $(this).children("option:selected").val() == ""){
				   	$(this).closest('.form-group').find('.errAlamat').show();
				  	errCount = errCount + 1;
				}
				else {
					$(this).closest('.form-group').find('.errAlamat').hide();
				}
			});

			if (errCount == 0) {
                Swal.fire({
                    title: "Simpan Alamat?",
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
                            url: "/Expedition/StoreBranch",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idAlamat : $("#idAlamat").val(),
                                alamat : $("#alamat_cabang").val(),
                                nama : $("#nama_cabang").val(),
                                kota : $("#kotaAlamat").val(),
                                telpCabang : $("#telp_cabang").val(),
                                idExpedition : ''
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Data Berhasil disimpan!.",
                                        "success"
                                    )
                                    $("#idAlamat").val("");
                                    $("#nama_cabang").val(""),
                                    $("#alamat_cabang").val("");
                                    $("#kotaAlamat").val("");
                                    $("#telp_cabang").val("");
                                    $('#modal_form_horizontal_add').modal('hide');
                                    getAlamatDraft();
                                }
                                else if (result == "failNama") {
                                    $('#modal_form_horizontal_add').modal('hide');
                                    getAlamatDraft();
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

        $("#btnSimpanTarif").on('click', function(e) {
			var errCount = 0;

			$(".detilTarif").each(function(){
				if($(this).val() == "" || $(this).children("option:selected").val() == ""){
					$(this).closest('.form-group').find('.errTarif').show();
					errCount = errCount + 1;
				}
				else {
					$(this).closest('.form-group').find('.errTarif').hide();
				}
			});

			if (errCount == 0) {
                Swal.fire({
                    title: "Simpan Tarif?",
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
                            url: "/Expedition/StoreTarif",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idTarif : $("#idTarif").val(),
                                namaKota : $("#nama_kota").val(),
                                tarif : $("#tarif").val(),
                                idExpedition : ''
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Data Berhasil disimpan!.",
                                        "success"
                                    )
                                    $("#nama_kota").val("");
                                    $("#tarifMask").val("").trigger('change'),
                                    $('#modal_form_tarif').modal('hide');
                                    getTarifDraft();
                                }
                                else if (result == "failNama") {
                                    $('#modal_form_tarif').modal('hide');
                                    getTarifDraft();
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

		$("#modal_form_horizontal_add").on('hide.bs.modal', function(e) {
	        if ($("#idAlamat").val() != "") {
	        	$("#idAlamat").val("");
				$("#alamat_cabang").val("");
				$("#kotaAlamat").val("");
				$("#telp_cabang").val("");
                $("#nama_cabang").val("");
	        }
	    });

		function getAlamatDraft() {
			var datatable = $('#table_cabang').KTDatatable();
                datatable.setDataSourceParam('idExpedition','');
                datatable.reload();
		}

        $(document).ready(function() {
            var datatable = $('#table_cabang').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Expedition/GetBranch',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data : ''
                        }
                    },
                    pageSize: 10,
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

                sortable: true,

                filterable: true,

                pagination: true,

                search: {
                    input: $('#table_cabang_search_query')
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
                        field: 'nama_cabang',
                        title: 'Nama Cabang',
                        width: 'auto',
                        template: function(row) {
                            return ucwords(row.nama_cabang);
                        },
                    },
                    {
                        field: 'alamat_cabang',
                        title: 'Alamat Cabang',
                        width: 'auto',
                        template: function(row) {
                            return ucwords(row.alamat_cabang);
                        },
                    },
                    {
                        field: 'kota_cabang',
                        title: 'Kota Cabang',
                        width: 'auto',
                        template: function(row) {
                            return ucwords(row.kota_cabang);
                        },
                    },
                    {
                        field: 'telp_cabang',
                        title: 'Telp Cabang',
                        width: 'auto',
                    },
                    {
                        field: 'default',
                        title: 'Default',
                        textAlign: 'center',
                        width: 'auto',
                        overflow: 'visible',
                        autoHide:false,
                        template: function(row) {
                            var txtCheckbox = "<div class='radio-list align-items-center'>";
                                txtCheckbox += "<label class='radio radio-lg'>";
                                if (row.default == "Y")
                                    txtCheckbox += "<input type='radio' class='text-center' onchange='setDefault("+row.id+");' value='"+row.id+"' name='alamatDefault' checked>";
                                else {
                                    txtCheckbox += "<input type='radio' class='text-center' onchange='setDefault("+row.id+");' value='"+row.id+"' name='alamatDefault'>";
                                }
                                txtCheckbox += "<span></span>";
                                txtCheckbox += "</label>";
                                txtCheckbox += "</div>";
                            return txtCheckbox;
                        },
                    },
                    {
                        field: 'actions',
                        title: 'Aksi',
                        width: 'auto',
                        textAlign: 'center',
                        overflow: 'visible',
                        autoHide:false,
                        template: function(row) {
                            var txtAction = "<a href='#' class='btn btn-sm btn-clean btn-icon edit' title='Ubah' onclick='editBranchData("+row.id+");return false;'>";
                                txtAction += "<i class='la la-edit'></i>";
                                txtAction += "</a>";
                                txtAction += "<a href='#' class='btn btn-sm btn-clean btn-icon' title='Hapus' onclick='deleteBranchData("+row.id+");return false;'>";
                                txtAction += "<i class='la la-trash'></i>";
                                txtAction += "</a>";

                            return txtAction;
                        },
                    },
                ],
            });
		});

        $(document).ready(function() {
            var datatable = $('#table_tarif').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Expedition/GetTarif',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data : {
                                idExpedition : "",
                            },
                        }
                    },
                    pageSize: 10,
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

                sortable: true,

                filterable: true,

                pagination: true,

                search: {
                    input: $('#table_cabang_search_query')
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
                        field: 'nama_kota',
                        title: 'Nama Kota Tujuan',
                        width: 200,
                        template: function(row) {
                            return ucwords(row.nama_kota);
                        },
                    },
                    {
                        field: 'tarif',
                        title: 'Tarif (Rp)',
                        textAlign: 'right',
                        width: '215',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.tarif).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'actions',
                        title: 'Aksi',
                        width: 'auto',
                        textAlign: 'center',
                        overflow: 'visible',
                        autoHide:false,
                        template: function(row) {
                            var txtAction = "<a href='#' class='btn btn-sm btn-clean btn-icon edit' title='Ubah' onclick='editTarifData("+row.id+");return false;'>";
                                txtAction += "<i class='la la-edit'></i>";
                                txtAction += "</a>";
                                txtAction += "<a href='#' class='btn btn-sm btn-clean btn-icon' title='Hapus' onclick='deleteTarifData("+row.id+");return false;'>";
                                txtAction += "<i class='la la-trash'></i>";
                                txtAction += "</a>";

                            return txtAction;
                        },
                    },
                ],
            });
		});

        function editBranchData(id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/Expedition/EditBranch",
                method: 'POST',
                data: {
                    idBranch: id
                },
                success: function(result){
                    $("#idAlamat").val(result.id);
                    $("#nama_cabang").val(result.nama_cabang),
                    $("#alamat_cabang").val(result.alamat_cabang);
                    $("#kotaAlamat").val(result.kota_cabang);
                    $("#telp_cabang").val(result.telp_cabang);
                    $('#modal_form_horizontal_add').modal('show');
                }
            });

        }

        function editTarifData(id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/Expedition/EditTarif",
                method: 'POST',
                data: {
                    idTarif: id,
                    idExpedition : ""
                },
                success: function(result){
                    var tarifFixed = parseFloat(result.tarif).toLocaleString('id-ID', { minimumFractionDigits: 2});

                    $("#idTarif").val(result.id);
                    $("#nama_kota").val(result.nama_kota),
                    $("#tarifMask").val(tarifFixed).trigger('change');
                    $('#modal_form_tarif').modal('show');
                }
            });
        }

        function deleteBranchData(id) {
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
                        url: "/Expedition/DeleteBranch",
                        method: 'POST',
                        data: {
                            idBranch: id
                        },
                        success: function(result){
                            Swal.fire(
                                "Sukses!",
                                "Data Berhasil dihapus!.",
                                "success"
                            )
                        }
                    });
                    var datatable = $("#table_cabang").KTDatatable();
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

        function deleteTarifData(id) {
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
                        url: "/Expedition/DeleteTarif",
                        method: 'POST',
                        data: {
                            idTarif: id
                        },
                        success: function(result){
                            Swal.fire(
                                "Sukses!",
                                "Data Berhasil dihapus!.",
                                "success"
                            )
                        }
                    });
                    var datatable = $("#table_tarif").KTDatatable();
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

        function setDefault(id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/Expedition/SetDefault",
                method: 'POST',
                data: {
                    idBranch: id
                },
                success: function(result){
                    Swal.fire(
                        "Sukses!",
                        "Set Alamat Default Berhasil!.",
                        "success"
                    )
                }
            });
            var datatable = $("#table_cabang").KTDatatable();
            datatable.reload();
        }

        function getTarifDraft() {
			var datatable = $('#table_tarif').KTDatatable();
                datatable.setDataSourceParam('idExpedition','');
                datatable.reload();
		}


    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
