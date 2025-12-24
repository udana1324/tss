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
                                        <span class="nav-text">Informasi Pelanggan</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab_pane_2" id="tab2">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Detail Alamat Pelanggan</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab_pane_3" id="tab3">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Barang-barang pelanggan</span>
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
												<label class="col-lg-3 col-form-label">Kode Pelanggan</label>
												<div class="col-lg-9">
                                                <input type="text" class="form-control form-control-solid" placeholder="Auto Generated" name="kode_cust" id="kode_cust" autocomplete="off" value="{{strtoupper($dataCustomer->kode_customer)}}" readonly>
												</div>
											</div>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Kategori Pelanggan</label>
												<div class="col-lg-9">
													<select class="form-control select2 req" id="kategori_cust" name="kategori_cust" disabled>
														<option label="Label"></option>
														@foreach($customerCategory as $dataCategory)
														<option value="{{$dataCategory->id}}">{{strtoupper($dataCategory->kode_kategori. ' - '.$dataCategory->nama_kategori)}}</option>
														@endforeach
													</select>
													<span class="form-text text-danger err" style="display:none;">*Harap pilih kategori Pelanggan terlebih dahulu!</span>
												</div>
											</div>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">No. Telp Pelanggan</label>
												<div class="col-lg-9">
													<input type="text" class="tlp form-control req" autocomplete="off" style="width:150px; display:inline-block;" maxlength="3" onkeypress="return validasiangka(event);" name="head_telp_cust" id="head_telp_cust" readonly>
													-
													<input type="text" class="tlp form-control req" autocomplete="off" style="width:280px;display:inline-block;" maxlength="8" onkeypress="return validasiangka(event);" name="body_telp_cust" id="body_telp_cust" readonly>
											        <span class="form-text text-muted">Contoh : (999) 99999999</span>
											        <span class="form-text text-danger err" style="display:none;">*Harap masukkan no. telp Pelanggan terlebih dahulu!</span>
												</div>
											</div>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">No. Fax Pelanggan</label>
												<div class="col-lg-9">
													<input type="text" class="fax form-control" autocomplete="off" style="width:150px; display:inline-block;" maxlength="3" onkeypress="return validasiangka(event);" name="head_fax_cust" id="head_fax_cust" readonly>
													-
													<input type="text" class="fax form-control" autocomplete="off" style="width:280px;display:inline-block;" maxlength="8" onkeypress="return validasiangka(event);" name="body_fax_cust" id="body_fax_cust" readonly>
											        <span class="form-text text-muted">Contoh : (999) 99999999</span>
												</div>
                                            </div>

                                            <div class="form-group row">
												<label class="col-lg-3 col-form-label">Nama Sales</label>
												<div class="col-lg-9">
													<select class="form-control select2 req" id="sales" name="sales" disabled>
														<option label="Label"></option>
														@foreach($sales as $dataSales)
														<option value="{{$dataSales->id}}">{{ucwords($dataSales->nama_sales)}}</option>
														@endforeach
													</select>
													<span class="form-text text-danger err" style="display:none;">*Harap pilih Sales terlebih dahulu!</span>
												</div>
											</div>

										</fieldset>
									</div>

									<div class="col-md-6">
										<fieldset>
											<legend class="font-weight-semibold"></legend>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Nama Pelanggan</label>
												<div class="col-lg-9">
                                                <input type="text" class="form-control req" autocomplete="off" placeholder="Masukkan Nama Pelanggan" name="nama_cust" id="nama_cust" value="{{$dataCustomer->nama_customer}}" readonly>
													<span class="form-text text-danger err" style="display:none;">*Harap masukkan nama Pelanggan terlebih dahulu!</span>
												</div>
											</div>

                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label">Jenis Customer :</label>
                                                <div class="no-gutters">
                                                    <div class="radio-inline">
                                                        <label class="radio">
                                                        <input type="radio" id="jenis_cust_c" disabled="disabled" name="jenis_customer" value="C" {{ $dataCustomer->jenis_customer === "C" ? "checked" : "" }} />
                                                        <span></span>Perusahaan</label>
                                                        <label class="radio">
                                                        <input type="radio" id="jenis_cust_i" disabled="disabled" name="jenis_customer" value="I" {{ $dataCustomer->jenis_customer === "I" ? "checked" : "" }} />
                                                        <span></span>Individual</label>
                                                    </div>
                                                </div>
                                            </div>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">NPWP Pelanggan</label>
												<div class="col-lg-9">
													<input type="text" class="npwp form-control req" maxlength="2" style="width:60px;display:inline-block;text-align:center;" autocomplete="off" onkeypress="return validasiangka(event);" name="npwp_custp1" id="npwp_custp1" readonly>
													.
													<input type="text" class="npwp form-control req" maxlength="3" style="width:60px;display:inline-block;text-align:center;" autocomplete="off" onkeypress="return validasiangka(event);" name="npwp_custp2" id="npwp_custp2" readonly>
													.
													<input type="text" class="npwp form-control req" maxlength="3" style="width:60px;display:inline-block;text-align:center;" autocomplete="off" onkeypress="return validasiangka(event);" name="npwp_custp3" id="npwp_custp3" readonly>
													.
													<input type="text" class="npwp form-control req" maxlength="1" style="width:60px;display:inline-block;text-align:center;" autocomplete="off" onkeypress="return validasiangka(event);" name="npwp_custp4" id="npwp_custp4" readonly>
													-
													<input type="text" class="npwp form-control req" maxlength="3" style="width:60px;display:inline-block;text-align:center;" autocomplete="off" onkeypress="return validasiangka(event);" name="npwp_custp5" id="npwp_custp5" readonly>
													.
													<input type="text" class="npwp form-control req" maxlength="3" style="width:60px;display:inline-block;text-align:center;" autocomplete="off" onkeypress="return validasiangka(event);" name="npwp_custp6" id="npwp_custp6" readonly>
		                        					<span class="form-text text-muted"> Contoh : 99.999.999.9-999.999</span>
		                        					<span class="form-text text-danger err" style="display:none;">*Harap masukkan no. npwp Pelanggan terlebih dahulu!</span>
												</div>
											</div>

                                            <div class="form-group row">
												<label class="col-lg-3 col-form-label">NPWP Customer (16 digit)</label>
												<div class="col-lg-9">
													<input type="text" class="npwp16 form-control req" maxlength="4" style="width:60px;display:inline-block;text-align:center;" autocomplete="off" onkeypress="return validasiangka(event);" name="npwp16_cust1" id="npwp16_cust1" readonly>
													.
													<input type="text" class="npwp16 form-control req" maxlength="4" style="width:60px;display:inline-block;text-align:center;" autocomplete="off" onkeypress="return validasiangka(event);" name="npwp16_cust2" id="npwp16_cust2" readonly>
													.
													<input type="text" class="npwp16 form-control req" maxlength="4" style="width:60px;display:inline-block;text-align:center;" autocomplete="off" onkeypress="return validasiangka(event);" name="npwp16_cust3" id="npwp16_cust3" readonly>
													.
													<input type="text" class="npwp16 form-control req" maxlength="4" style="width:60px;display:inline-block;text-align:center;" autocomplete="off" onkeypress="return validasiangka(event);" name="npwp16_cust4" id="npwp16_cust4" readonly>
										<span class="form-text text-muted"> Contoh : 9999.9999.9999.9999</span>
										<span class="form-text text-danger err" style="display:none;">*Harap masukkan no. npwp (16 digit) customer terlebih dahulu!</span>
												</div>
											</div>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Email Pelanggan</label>
												<div class="col-lg-9">
													<input type="email" class="form-control" autocomplete="off" placeholder="Masukkan Email Pelanggan" name="email_cust" id="email_cust" value="{{$dataCustomer->email_customer}}" readonly>
													<span class="form-text text-danger err" style="display:none;">*Harap masukkan email Pelanggan terlebih dahulu!</span>
												</div>
                                            </div>

                                            <div class="form-group row">
												<label class="col-lg-3 col-form-label">Batas Piutang</label>
												<div class="col-lg-9">
													<input type="number" class="form-control req" autocomplete="off" placeholder="Masukkan Limit Piutang Pelanggan" name="limit_cust" id="limit_cust" value="{{$dataCustomer->limit_customer}}" readonly>
													<span class="form-text text-danger err" style="display:none;">*Harap masukkan Limit Piutang Pelanggan terlebih dahulu!</span>
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
                                                        <input type="text" class="form-control" placeholder="Search..." id="table_address_cust_search_query"/>
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

                                <div class="datatable datatable-bordered datatable-head-custom" id="table_address_cust"></div>

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
            $('#kategori_cust').select2({
                placeholder: "Pilih Kategori Pelanggan",
                allowClear: true
            });

            $('#sales').select2({
                placeholder: "Pilih Sales",
                allowClear: true
            });
        });

		function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

		$("#cancel").on('click', function(e) {
            window.location.href = "{{ url('/Customer') }}";
	    });

        $(document).ready(function() {
            var datatable = $('#table_address_cust').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Customer/GetAddress',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data : {
                                idCustomer: "{{$dataCustomer->id}}"
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
                    input: $('#table_address_cust_search_query')
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
                        field: 'nama_outlet',
                        title: 'Nama Outlet',
                        width: 'auto',
                        autoHide:false,
                        template: function(row) {
                            if (row.nama_outlet != null) {
                                return ucwords(row.nama_outlet);
                            }
                            else {
                                return " - ";
                            }
                        },
                    },
                    {
                        field: 'alamat_customer',
                        title: 'Alamat',
                        width: 'auto',
                        autoHide:false,
                        template: function(row) {
                            return ucwords(row.alamat_customer);
                        },
                    },
                    {
                        field: 'kelurahan',
                        title: 'Kelurahan',
                        width: 'auto',
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
                        title: 'Kecamatan',
                        width: 'auto',
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
                        title: 'Kode Pos',
                        width: 'auto',
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
                        title: 'PIC',
                        width: 'auto',
                        autoHide:false,
                        template: function(row) {
                            return ucwords(row.pic_alamat);
                        },
                    },
                    {
                        field: 'telp_pic',
                        width: 'auto',
                        title: 'Telp PIC',
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
            var telp = "{{$dataCustomer->telp_customer}}";
            var fax = "{{$dataCustomer->fax_customer}}";
            var npwp = "{{$dataCustomer->npwp_customer}}";
            var npwp16 = "{{$dataCustomer->npwp_customer_16}}";
            npwp_split = npwp.split(".");
            npwp16_split = npwp16.split(".");
            npwp1 = npwp_split[0];
            npwp2 = npwp_split[1];
            npwp3 = npwp_split[2];
            npwp_4 = npwp_split[3];
            npwp_4_5 = npwp_4.split("-");
            npwp4 = npwp_4_5[0];
            npwp5 = npwp_4_5[1];
            npwp6 = npwp_split[4];
            npwp16_1 = npwp16_split[0];
            npwp16_2 = npwp16_split[1];
            npwp16_3 = npwp16_split[2];
            npwp16_4 = npwp16_split[3];
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

            $("#nama_cust").val("{{$dataCustomer->nama_customer}}");
            $('#kategori_cust').val("{{$dataCustomer->kategori_customer}}").trigger('change');
            $("#sales").val("{{$dataCustomer->sales}}").trigger("change");
            $("#email_cust").val("{{$dataCustomer->email_customer}}");
            $("#head_telp_cust").val(head_tlp);
            $("#body_telp_cust").val(body_tlp);
            $("#telp_cust").val(telp);
            $("#head_fax_cust").val(head_fax);
            $("#body_fax_cust").val(body_fax);
            $("#npwp_custp1").val(npwp1);
            $("#npwp_custp2").val(npwp2);
            $("#npwp_custp3").val(npwp3);
            $("#npwp_custp4").val(npwp4);
            $("#npwp_custp5").val(npwp5);
            $("#npwp_custp6").val(npwp6);
            $("#npwp16_cust1").val(npwp16_1);
            $("#npwp16_cust2").val(npwp16_2);
            $("#npwp16_cust3").val(npwp16_3);
            $("#npwp16_cust4").val(npwp16_4);
        });

        $(document).ready(function() {
			var listItem = @json($dataProductCustomer);

            var datatable = $('#list_item').KTDatatable({
                data: {
                    type: 'local',
                    source: listItem,
                    pageSize: 100,
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

                columns: [
                    {
                        field: 'id',
                        title: '#',
                        sortable: false,
                        width: 0,
                        type: 'number',
                        selector: false,
                        textAlign: 'center',
                        visible: false,
                    },
                    {
                        field: 'kode_item',
                        title: 'Kode Barang',
                        width: 100,
                        autoHide: false,
                        textAlign: 'left',
                        template: function(row) {
                            return row.kode_item.toUpperCase();
                        },
                    },
                    {
                        field: 'nama_item',
                        title: 'Nama Barang',
                        width: 500,
                        autoHide: false,
                        textAlign: 'left',
                        template: function(row) {
                            return ucwords(row.nama_item);
                        },
                    },
                    {
                        field: 'nama_satuan',
                        title: 'Nama Satuan',
                        width: 170,
                        autoHide: false,
                        textAlign: 'left',
                        template: function(row) {
                            return ucwords(row.nama_satuan);
                        },
                    },
                    {
                        field: 'harga_jual_last',
                        title: 'Harga Jual',
                        width: 'auto',
                        autoHide: false,
                        textAlign: 'right',
                        template: function(row) {
                            return parseFloat(row.harga_jual_last).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                ],
            });
		});

        $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
            var datatableAdress = $("#table_address_cust").KTDatatable();
            datatableAdress.reload();
            var datatablItem = $("#list_item").KTDatatable();
            datatablItem.reload();
	    });

    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
