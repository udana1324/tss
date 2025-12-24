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
                                        <span class="nav-text">Data Supplier</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab_pane_2" id="tab2">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Detail Alamat Supplier</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab_pane_3" id="tab3">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Item Supplier</span>
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
												<label class="col-lg-3 col-form-label">Kode Supplier</label>
												<div class="col-lg-9">
                                                <input type="text" class="form-control bg-slate-600 border-slate-600 border-1" value="{{strtoupper($dataSupplier->kode_supplier)}}" name="kode_supp" id="kode_supp" autocomplete="off" readonly>
												</div>
											</div>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Kategori Supplier</label>
												<div class="col-lg-9">
													<select class="form-control select2 req" id="kategori_supp" name="kategori_supp" disabled>
														<option label="Label"></option>
														@foreach($supplierCategory as $dataCategory)
														<option value="{{$dataCategory->id}}">{{strtoupper($dataCategory->kode_kategori. ' - '.$dataCategory->nama_kategori)}}</option>
														@endforeach
													</select>
													<span class="form-text text-danger err" style="display:none;">*Harap pilih kategori supplier terlebih dahulu!</span>
												</div>
											</div>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">No. Telp Supplier</label>
												<div class="col-lg-9">
                                                    <input type="text" class="form-control req" autocomplete="off" maxlength="20" onkeypress="return validasiTelp(event)" name="telp_supp" id="telp_supp" value="{{$dataSupplier->telp_supplier}}" readonly>
													{{-- <input type="text" class="tlp form-control req" autocomplete="off" style="width:55px; display:inline-block;" maxlength="3" name="head_telp_supp" id="head_telp_supp" readonly>
													-
													<input type="text" class="tlp form-control req" autocomplete="off" style="width:130px;display:inline-block;" maxlength="8" name="body_telp_supp" id="body_telp_supp" readonly> --}}
											        <span class="form-text text-muted">Contoh : (999) 99999999</span>
											        <span class="form-text text-danger err" style="display:none;">*Harap masukkan no. telp supplier terlebih dahulu!</span>
												</div>
											</div>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">No. Fax Supplier</label>
												<div class="col-lg-9">
                                                    <input type="text" class="form-control" autocomplete="off" maxlength="20" onkeypress="return validasiTelp(event)" name="fax_supp" id="fax_supp" value="{{$dataSupplier->fax_supplier}}" readonly>
													{{-- <input type="text" class="fax form-control" placeholder="-" autocomplete="off" style="width:55px; display:inline-block;" maxlength="3" name="head_fax_supp" id="head_fax_supp" readonly>
													-
													<input type="text" class="fax form-control" placeholder="-" autocomplete="off" style="width:130px;display:inline-block;" maxlength="8" name="body_fax_supp" id="body_fax_supp" readonly> --}}
											        <span class="form-text text-muted">Contoh : (999) 99999999</span>
												</div>
											</div>

										</fieldset>
									</div>

									<div class="col-md-6">
										<fieldset>
											<legend class="font-weight-semibold"></legend>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Nama Supplier</label>
												<div class="col-lg-9">
													<input type="text" class="form-control req" autocomplete="off" placeholder="Masukkan Nama Supplier" name="nama_supp" id="nama_supp" value="{{$dataSupplier->nama_supplier}}" readonly>
													<span class="form-text text-danger err" style="display:none;">*Harap masukkan nama supplier terlebih dahulu!</span>
												</div>
											</div>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">NPWP Supplier</label>
												<div class="col-lg-9">
													<input type="text" class="npwp form-control req" maxlength="2" style="width:60px;display:inline-block;text-align:center;" autocomplete="off" name="npwp_suppp1" id="npwp_suppp1" readonly>
													.
													<input type="text" class="npwp form-control req" maxlength="3" style="width:60px;display:inline-block;text-align:center;" autocomplete="off" name="npwp_suppp2" id="npwp_suppp2" readonly>
													.
													<input type="text" class="npwp form-control req" maxlength="3" style="width:60px;display:inline-block;text-align:center;" autocomplete="off" name="npwp_suppp3" id="npwp_suppp3" readonly>
													.
													<input type="text" class="npwp form-control req" maxlength="1" style="width:60px;display:inline-block;text-align:center;" autocomplete="off" name="npwp_suppp4" id="npwp_suppp4" readonly>
													-
													<input type="text" class="npwp form-control req" maxlength="3" style="width:60px;display:inline-block;text-align:center;" autocomplete="off" name="npwp_suppp5" id="npwp_suppp5" readonly>
													.
													<input type="text" class="npwp form-control req" maxlength="3" style="width:60px;display:inline-block;text-align:center;" autocomplete="off" name="npwp_suppp6" id="npwp_suppp6" readonly>
		                        					<span class="form-text text-muted"> Contoh : 99.999.999.9-999.999</span>
		                        					<span class="form-text text-danger err" style="display:none;">*Harap masukkan no. npwp supplier terlebih dahulu!</span>
												</div>
											</div>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Email Supplier</label>
												<div class="col-lg-9">
													<input type="email" class="form-control" autocomplete="off" placeholder="-" name="email_supp" id="email_supp" value="{{$dataSupplier->email_supplier}}" readonly>
													<span class="form-text text-danger err" style="display:none;">*Harap masukkan email supplier terlebih dahulu!</span>
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
                                                        <input type="text" class="form-control" placeholder="Search..." id="table_address_sup_search_query"/>
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

                                <div class="datatable datatable-bordered datatable-head-custom" id="table_address_sup"></div>

                                <!--end: Datatable-->
                            </div>

                            <div class="tab-pane fade" id="tab_pane_3" role="tabpanel" aria-labelledby="tab_pane_3">
								<!--begin: Search Form-->
                                <!--begin::Search Form-->
                                <div class="mb-7">
                                    <div class="row align-items-center">
                                        <div class="col-lg-6">
                                            <div class="row align-items-center">
                                                <div class="col-md-4 my-2 my-md-0">
                                                    <div class="input-icon">
                                                        <input type="text" class="form-control" placeholder="Search..." id="list_item_search_query"/>
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

                                <div class="datatable datatable-bordered datatable-head-custom" id="list_item"></div>

                                <!--end: Datatable-->
							</div>
						</div>

						<div class="card-footer bg-white d-sm-flex justify-content-sm-between align-items-sm-center">
							<div class="btn-group">
                                <button type="button" class="btn btn-light-danger font-weight-bold mr-2" id="cancel">Keluar <i class="flaticon2-cancel icon-sm"></i></button>
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
            $('#kategori_supp').select2({
                placeholder: "Pilih Kategori Supplier",
                allowClear: true
            });
        });

		function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

		$("#cancel").on('click', function(e) {
            window.location.href = "{{ url('/Supplier') }}";
	    });

        $(document).ready(function() {
            var datatable = $('#table_address_sup').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Supplier/GetAddress',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data : {
                                idSupplier: "{{$dataSupplier->id}}"
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
                    input: $('#table_address_sup_search_query')
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
                        field: 'alamat_supplier',
                        title: 'Alamat',
                        width: 'auto',
                        autoHide:false,
                        template: function(row) {
                            return ucwords(row.alamat_supplier);
                        },
                    },
                    {
                        field: 'kelurahan',
                        width: 'auto',
                        title: 'Kelurahan',
                        template: function(row) {
                            if (row.kelurahan != null) {
                                return ucwords(row.kelurahan);
                            }
                            else {
                                return " - ";
                            }
                        },
                    },
                    {
                        field: 'kecamatan',
                        width: 'auto',
                        title: 'Kecamatan',
                        template: function(row) {
                            return ucwords(row.kecamatan);
                        },
                    },
                    {
                        field: 'kota',
                        width: 'auto',
                        title: 'Kota',
                        template: function(row) {
                            return ucwords(row.kota);
                        },
                    },
                    {
                        field: 'kode_pos',
                        width: 'auto',
                        title: 'Kode Pos',
                        template: function(row) {
                            if (row.kode_pos != null) {
                                return ucwords(row.kode_pos);
                            }
                            else {
                                return " - ";
                            }
                        },
                    },
                    {
                        field: 'jenis_alamat',
                        width: 'auto',
                        title: 'Jenis Alamat',
                        autoHide:false,
                    },
                    {
                        field: 'pic_alamat',
                        width: 'auto',
                        title: 'PIC',
                        autoHide:false,
                        template: function(row) {
                            return ucwords(row.pic_alamat);
                        },
                    },
                    {
                        field: 'telp_pic',
                        title: 'Telp PIC',
                        width: 'auto',
                    },
                    {
                        field: 'default',
                        width: 'auto',
                        title: 'Default',
                        textAlign: 'center',
                        overflow: 'visible',
                        autoHide:false,
                        template: function(row) {
                            var txtCheckbox = "<div class='radio-list align-items-center'>";
                                txtCheckbox += "<label class='radio radio-lg'>";
                                if (row.default == "Y")
                                    txtCheckbox += "<input type='radio' class='text-center' value='"+row.id+"' name='alamatDefault' checked disabled>";
                                else {
                                    txtCheckbox += "<input type='radio' class='text-center' value='"+row.id+"' name='alamatDefault' disabled>";
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
            var telp = "{{$dataSupplier->telp_supplier}}";
            var fax = "{{$dataSupplier->fax_supplier}}";
            var npwp = "{{$dataSupplier->npwp_supplier}}";
            npwp_split = npwp.split(".");
            npwp1 = npwp_split[0];
            npwp2 = npwp_split[1];
            npwp3 = npwp_split[2];
            npwp_4 = npwp_split[3];
            npwp_4_5 = npwp_4.split("-");
            npwp4 = npwp_4_5[0];
            npwp5 = npwp_4_5[1];
            npwp6 = npwp_split[4];
            tlp = telp.split("-");
            head_tlp = tlp[0];
            body_tlp = tlp[1];

            if (fax != "") {
            	fx = fax.split("-");
            	head_fax = fx[0];
            	body_fax = fx[1];
            }
            else {
            	head_fax = "";
            	body_fax = "";
            }

            $("#nama_supp").val("{{$dataSupplier->nama_supplier}}");
            $('#kategori_supp').val("{{$dataSupplier->kategori_supplier}}").trigger('change');
            $("#email_supp").val("{{$dataSupplier->email_supplier}}");
            $("#head_telp_supp").val(head_tlp);
            $("#body_telp_supp").val(body_tlp);
            $("#telp_supp").val(telp);
            $("#head_fax_supp").val(head_fax);
            $("#body_fax_supp").val(body_fax);
            $("#npwp_suppp1").val(npwp1);
            $("#npwp_suppp2").val(npwp2);
            $("#npwp_suppp3").val(npwp3);
            $("#npwp_suppp4").val(npwp4);
            $("#npwp_suppp5").val(npwp5);
            $("#npwp_suppp6").val(npwp6);
        });

        $(document).ready(function() {
			var listItem = @json($supplierProduct);
            var datatable = $('#list_item').KTDatatable({
                data: {
                    type: 'local',
                    source: listItem,
                    pageSize: 10,
                    saveState : false,
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
                    input: $('#list_item_search_query')
                },

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
                        title: 'Kode Item',
                        width: 100,
                        textAlign: 'center',
                        template: function(row) {
                            if (row.value_spesifikasi != null) {
                                return '('+row.value_spesifikasi+')'+row.kode_item.toUpperCase();
                            }
                            else {
                                return row.kode_item.toUpperCase();
                            }

                        },
                    },
                    {
                        field: 'nama_item',
                        title: 'Nama Item',
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            return ucwords(row.nama_item);
                        },
                    },
                    // {
                    //     field: 'nama_satuan',
                    //     title: 'Nama Satuan',
                    //     width: 'auto',
                    //     textAlign: 'center',
                    //     template: function(row) {
                    //         return ucwords(row.nama_satuan);
                    //     },
                    // },
                    {
                        field: 'harga_beli',
                        width: 200,
                        title: 'Harga Beli',
                        textAlign: 'center',
                        template: function(row) {
                            return parseFloat(row.harga_beli).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                ],
            });
		});

        $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
            var datatableAdress = $("#table_address_sup").KTDatatable();
            datatableAdress.reload();
            var datatablItem = $("#list_item").KTDatatable();
            datatablItem.reload();
	    });

    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
