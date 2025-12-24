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
						<form action="" method="POST" id="form_add">
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
                                                <input type="text" class="form-control req" placeholder="Masukkan Nama Ekspedisi" name="nama_ekspedisi" id="nama_ekspedisi" value="{{strtoupper($dataExpedition->nama_ekspedisi)}}" readonly>
                                                    <span class="form-text text-danger err" style="display:none;">*Harap masukkan nama ekspedisi terlebih dahulu!</span>
												</div>
											</div>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Nama Perusahaan</label>
												<div class="col-lg-9">
                                                    <input type="text" placeholder="Masukkan Nama Perusahaan Ekspedisi" class="form-control req" name="nama_perusahaan" id="nama_perusahaan" value="{{$dataExpedition->nama_perusahaan}}" readonly>
                                                    <span class="form-text text-danger err" style="display:none;">*Harap masukkan nama perusahaan ekspedisi terlebih dahulu!</span>
												</div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label">No. Telp</label>
                                                <div class="col-lg-9">
                                                    <input type="text" class="form-control" maxlength="14" name="telp_perusahaan" id="telp_perusahaan" autocomplete="off" value="{{$dataExpedition->telp_perusahaan}}" readonly>
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

                            </div>
                        </div>
						</form>
					</div>
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

		$("#cancel").on('click', function(e) {
            window.location.href = "{{ url('/Expedition') }}";
	    });

        $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
            var datatable = $('#table_cabang').KTDatatable();
                datatable.reload();

            var datatable2 = $('#table_tarif').KTDatatable();
                datatable2.reload();
	    });

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
                            data : {
                                idExpedition : "{{$dataExpedition->id}}",
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
                        field: 'nama_cabang',
                        title: 'Nama Cabang',
                        width: 200,
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
                        width: 'auto',
                        title: 'Telp Cabang',
                    },
                    {
                        field: 'default',
                        title: 'Default',
                        width: 'auto',
                        textAlign: 'center',
                        overflow: 'visible',
                        autoHide:false,
                        template: function(row) {
                            var txtCheckbox = "<div class='radio-list align-items-center'>";
                                txtCheckbox += "<label class='radio radio-lg'>";
                                if (row.default == "Y")
                                    txtCheckbox += "<input type='radio' class='text-center' onchange='setDefault("+row.id+");' value='"+row.id+"' name='alamatDefault' checked disabled>";
                                else {
                                    txtCheckbox += "<input type='radio' class='text-center' onchange='setDefault("+row.id+");' value='"+row.id+"' name='alamatDefault' disabled>";
                                }
                                txtCheckbox += "<span></span>";
                                txtCheckbox += "</label>";
                                txtCheckbox += "</div>";
                            return txtCheckbox;
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
                                idExpedition : "{{$dataExpedition->id}}",
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
                ],
            });
		});

    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
