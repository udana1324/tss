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
                                    <a class="nav-link active" data-toggle="tab" href="#tab_pane_1" id="tab1">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Data Barang</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab_pane_2" id="tab2">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Detail Barang</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab_pane_3" id="tab3">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Spesifikasi Barang</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab_pane_4" id="tab4">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Gambar Barang</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab_pane_5" id="tab5">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Pelanggan</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#tab_pane_6" id="tab6">
                                        <span class="nav-icon">

                                        </span>
                                        <span class="nav-text">Supplier</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
					</div>

					<div class="card-body">
						<form method="POST" id="form_add" enctype="multipart/form-data" autocomplete="off">
						{{ csrf_field() }}

						<div class="tab-content">
							<div class="tab-pane fade show active" id="tab_pane_1" role="tabpanel" aria-labelledby="tab_pane_1">
								<div class="row">
									<div class="col-md-6">
										<fieldset>
											<legend class="font-weight-semibold"></legend>

											<div class="form-group row">
                                                <label class="col-lg-3 col-form-label">Kode Item</label>
                                                <div class="col-lg-9">
                                                    <input type="hidden" name="gambar_item" id="gambar_item" value="{{$dataProduct->product_image_path}}" readonly>
                                                    <input type="text" class="form-control bg-slate-600 border-slate-600 border-1" placeholder="Auto Generated" name="kode_item" id="kode_item" value="{{strtoupper($dataProduct->kode_item)}}" readonly>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label">Kategori Item</label>
                                                <div class="col-lg-9">
                                                    <input type="text" class="form-control" value="{{ucwords($dataCategory->nama_kategori)}}" readonly>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label">Merk Item</label>
                                                <div class="col-lg-9">
                                                    <input type="text" class="form-control" value="{{ucwords($dataBrand->nama_merk)}}" readonly>
                                                </div>
                                            </div>



										</fieldset>
									</div>

									<div class="col-md-6">
										<fieldset>
											<legend class="font-weight-semibold"></legend>

											<div class="form-group row">
                                                <label class="col-lg-3 col-form-label">Nama Item</label>
                                                <div class="col-lg-9">
                                                    <input type="text" class="form-control req" placeholder="Masukkan Nama Item" name="nama_item" id="nama_item" value="{{$dataProduct->nama_item}}" readonly>
                                                    <span class="form-text text-danger err" style="display:none;">*Harap isi nama item terlebih dahulu!</span>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label">Jenis Item</label>
                                                <div class="col-lg-9">
                                                    <input type="text" class="form-control" value="{{ucwords($dataProduct->jenis_item)}}" readonly>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label">Keterangan Item</label>
                                                <div class="col-lg-9">
                                                    <textarea class="form-control" name="keterangan_item_txt" id="keterangan_item_txt" style="resize:none;" readonly>{{$dataProduct->keterangan_item}}</textarea>
                                                </div>
                                            </div>

										</fieldset>
									</div>
								</div>
							</div>

							<div class="tab-pane fade" id="tab_pane_2" role="tabpanel" aria-labelledby="tab_pane_2">
                                <!--begin::Search Form-->
                                <div class="mb-7">
                                    <div class="row align-items-center">
                                        <div class="col-lg-6">
                                            <div class="row align-items-center">
                                                <div class="col-md-4 my-2 my-md-0">
                                                    <div class="input-icon">
                                                        <input type="text" class="form-control" placeholder="Search..." id="table_satuan_search_query"/>
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
                                <!--begin: Datatable-->

                                <div class="datatable datatable-bordered datatable-head-custom" id="table_satuan"></div>

                                <!--end: Datatable-->
                            </div>

                            <div class="tab-pane fade" id="tab_pane_3" role="tabpanel" aria-labelledby="tab_pane_3">
                                <div class="row">
                                    <div class="col-md-8">
                                        <fieldset>
                                            <legend class="font-weight-semibold"></legend>
                                            <!--begin: Datatable-->

                                            <div class="datatable datatable-bordered datatable-head-custom" id="table_spek"></div>

                                            <!--end: Datatable-->

                                        </fieldset>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="tab_pane_4" role="tabpanel" aria-labelledby="tab_pane_4">
                                <div class="form-group row">
                                    <label class="col-xl-3 col-lg-3 col-form-label text-right">Gambar Barang</label>
                                    <div class="col-lg-9 col-xl-6">
                                        <div class="image-input image-input-outline" id="image_product">
                                            <div class="image-input-wrapper" style="background-image: url({{asset('images/products/'.$dataProduct->product_image_path)}})"></div>
                                        </div>
                                        <span class="form-text text-muted">Allowed file types: png, jpg, jpeg.</span>
                                    </div>
                                </div>
							</div>

                            <div class="tab-pane fade" id="tab_pane_5" role="tabpanel" aria-labelledby="tab_pane_5">
								<!--begin: Search Form-->
                                <!--begin::Search Form-->
                                <div class="mb-7">
                                    <div class="row align-items-center">
                                        <div class="col-lg-6">
                                            <div class="row align-items-center">
                                                <div class="col-md-4 my-2 my-md-0">
                                                    <div class="input-icon">
                                                        <input type="text" class="form-control" placeholder="Search..." id="list_item_cust_search_query"/>
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

                                <div class="datatable datatable-bordered datatable-head-custom" id="list_item_cust"></div>

                                <!--end: Datatable-->

								<div class="row">
									<div class="col-md-12">
										<fieldset>
											<legend class="font-weight-semibold"></legend>

											<div class="form-group row">
												<div class="col-lg-12 text-center">
													<span class="form-text text-danger errTbl" id="errTbl" style="display:none;">*Harap tambahkan Minimum 1 Alamat terlebih dahulu!</span>
												</div>
											</div>

										</fieldset>
									</div>
								</div>
							</div>

                            <div class="tab-pane fade" id="tab_pane_6" role="tabpanel" aria-labelledby="tab_pane_6">
								<!--begin: Search Form-->
                                <!--begin::Search Form-->
                                <div class="mb-7">
                                    <div class="row align-items-center">
                                        <div class="col-lg-6">
                                            <div class="row align-items-center">
                                                <div class="col-md-4 my-2 my-md-0">
                                                    <div class="input-icon">
                                                        <input type="text" class="form-control" placeholder="Search..." id="list_item_supp_search_query"/>
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

                                <div class="datatable datatable-bordered datatable-head-custom" id="list_item_supp"></div>

                                <!--end: Datatable-->

								<div class="row">
									<div class="col-md-12">
										<fieldset>
											<legend class="font-weight-semibold"></legend>

											<div class="form-group row">
												<div class="col-lg-12 text-center">
													<span class="form-text text-danger errTbl" id="errTbl" style="display:none;">*Harap tambahkan Minimum 1 Alamat terlebih dahulu!</span>
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
						</div>
						</form>
					</div>
				</div>
			<!-- /content area -->
@endsection
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $("#harga_beliMask").autoNumeric('init');
            $("#harga_jualMask").autoNumeric('init');
            $("#stok_minimumMask").autoNumeric('init');
            $("#stok_maksimumMask").autoNumeric('init');
            $("#panjang_itemMask").autoNumeric('init');
            $("#lebar_itemMask").autoNumeric('init');
            $("#tinggi_itemMask").autoNumeric('init');
            $("#berat_itemMask").autoNumeric('init');
            $("#panjang_dusMask").autoNumeric('init');
            $("#lebar_dusMask").autoNumeric('init');
            $("#tinggi_dusMask").autoNumeric('init');
            $("#berat_dusMask").autoNumeric('init');
            // $("#qty_per_dusMask").autoNumeric('init');
        });

		function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

		$("#cancel").on('click', function(e) {
            Swal.fire({
                title: "Batal?",
                text: "Apakah anda ingin keluar?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    window.location.href = "{{ url('/Product') }}";
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
            var tabName = e.target.id;
            var prevTab = e.relatedTarget.id;
            switch (tabName) {

                case "tab1" : {
                    if (prevTab == "tab2") {
                        $(".reqTab2").each(function(){
                            if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                                $(this).closest('.form-group').find('.errTab2').show();
                                e.preventDefault();
                            }
                            else {
                                $(this).closest('.form-group').find('.errTab2').hide();
                            }
                        });
                    }
                    break;
                }

                case "tab2" : {
                    $(".req").each(function(){
                        if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                            $(this).closest('.form-group').find('.err').show();
                            e.preventDefault();
                        }
                        else {
                            $(this).closest('.form-group').find('.err').hide();
                        }
                    });
                    break;
                }

                case "tab3" : {
                    if (prevTab == "tab1") {
                        $(".req").each(function(){
                            if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                                $(this).closest('.form-group').find('.err').show();
                                e.preventDefault();
                            }
                            else {
                                $(this).closest('.form-group').find('.err').hide();
                            }
                        });
                    }
                    $(".reqTab2").each(function(){
                        if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                            $(this).closest('.form-group').find('.errTab2').show();
                            e.preventDefault();
                        }
                        else {
                            $(this).closest('.form-group').find('.errTab2').hide();
                        }
                    });
                    break;
                }
            }
	    });

        $("#btnSimpanHarga").on('click', function(e) {
			var errCount = 0;

			$(".reqHarga").each(function(){
				if($(this).val() == "" || $(this).children("option:selected").val() == ""){
				   	$(this).closest('.form-group').find('.errHarga').show();
				  	errCount = errCount + 1;
				}
				else {
					$(this).closest('.form-group').find('.errHarga').hide();
				}
			});

			if (errCount == 0) {
                Swal.fire({
                    title: "Simpan Harga?",
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
                            url: "/Product/StorePrice",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idProduct : "{{$dataProduct->id}}",
                                hargaBeli : $("#harga_beli").val(),
                                hargaJual : $("#harga_jual").val()
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Data Harga Berhasil disimpan!.",
                                        "success"
                                    )
                                    window.location.reload();
                                }
                                else {
                                    Swal.fire(
                                        "Gagal!",
                                        "Gagal Menyimpan Harga !",
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

        $(document).ready(function() {
            var datatable = $('#table_spek').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Product/GetDetailSpec',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data : {
                                idProduct : '{{$dataProduct->id}}'
                            },
                        }
                    },
                    pageSize: 20,
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

                filterable: false,

                pagination: false,

                search: {
                    input: $('#table_satuan_search_query')
                },

                rows: {
                    autoHide:false
                },

                columns: [
                    {
                        field: 'id',
                        title: '#',
                        sortable: false,
                        type: 'number',
                        selector: false,
                        textAlign: 'center',
                        visible:false,
                    },
                    {
                        field: 'nama_spesifikasi',
                        title: 'Spesifikasi',
                    },
                    {
                        field: 'value_spesifikasi',
                        title: 'Nilai',
                    },
                ],
            });
		});

        $(document).ready(function() {
            var datatable = $('#table_satuan').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Product/GetDetailSatuan',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data : {
                                idProduct : '{{$dataProduct->id}}'
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
                    input: $('#table_satuan_search_query')
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
                        field: 'nama_satuan',
                        title: 'Satuan',
                        width: 'auto',
                        template: function(row) {
                            return ucwords(row.nama_satuan + ' - ' + row.kode_satuan);
                        },
                    },
                    {
                        field: 'panjang_item',
                        title: 'Panjang',
                        width: 'auto',
                    },
                    {
                        field: 'lebar_item',
                        title: 'Lebar',
                        width: 'auto',
                    },
                    {
                        field: 'tinggi_item',
                        title: 'Tinggi',
                        width: 'auto',
                    },
                    {
                        field: 'berat_item',
                        title: 'Berat',
                        width: 'auto',
                    },
                    {
                        field: 'panjang_dus',
                        title: 'Panjang Dus',
                        width: 'auto',
                    },
                    {
                        field: 'lebar_dus',
                        title: 'Lebar Dus',
                        width: 'auto',
                    },
                    {
                        field: 'tinggi_dus',
                        title: 'Tinggi Dus',
                        width: 'auto',
                    },
                    {
                        field: 'berat_dus',
                        title: 'Berat Dus',
                        width: 'auto',
                    },
                    // {
                    //     field: 'qty_per_dus',
                    //     title: 'Qty per Dus',
                    //     width: 'auto',
                    // },
                    {
                        field: 'stok_minimum',
                        title: 'Stok Min',
                        width: 'auto',
                    },
                    {
                        field: 'stok_maksimum',
                        title: 'Stok Max',
                        width: 'auto',
                    },
                    {
                        field: 'harga_beli',
                        title: 'Harga Beli',
                        width: 'auto',
                    },
                    {
                        field: 'harga_jual',
                        title: 'Harga Jual',
                        width: 'auto',
                    },
                    {
                        field: 'default',
                        title: 'Satuan Dasar',
                        textAlign: 'center',
                        width: 'auto',
                        overflow: 'visible',
                        autoHide:false,
                        template: function(row) {
                            var txtCheckbox = "<div class='radio-list align-items-center'>";
                                txtCheckbox += "<label class='radio radio-lg'>";
                                if (row.default == "Y")
                                    txtCheckbox += "<input type='radio' class='text-center' onchange='setDefault("+row.id+");' value='"+row.id+"' name='satuanDefault' checked disabled>";
                                else {
                                    txtCheckbox += "<input type='radio' class='text-center' onchange='setDefault("+row.id+");' value='"+row.id+"' name='satuanDefault' disabled>";
                                }
                                txtCheckbox += "<span></span>";
                                txtCheckbox += "</label>";
                                txtCheckbox += "</div>";
                            return txtCheckbox;
                        },
                    },
                    {
                        field: 'flag_monitor',
                        title: 'Monitor?',
                        width: 'auto',
                        autoHide:false,
                        template: function(row) {
                            if (row.flag_monitor == "1") {
                                return "<div class='checkbox-inline align-items-center'><label class='checkbox checkbox-lg'><input type='checkbox' id='checkMonitor' onchange='setMonitor("+row.id+");' value='"+row.id+"' class='text-center checkMonitor' checked disabled><span></span></label></div>";
                            }
                            else {
                                return "<div class='checkbox-inline align-items-center'><label class='checkbox checkbox-lg'><input type='checkbox' id='checkMonitor' onchange='setMonitor("+row.id+");' value='"+row.id+"' class='text-center checkMonitor' disabled><span></span></label></div>";
                            }

                        },
                    },
                ],
            });

            var hrgBeli = "{{json_encode($hakAksesHargaBeli)}}";
            var hrgJual = "{{json_encode($hakAksesHargaJual)}}";
            if (hrgBeli != null) {
                datatable.columns('harga_beli').visible(true);
            }
            else {
                datatable.columns('harga_beli').visible(false);
            }
            if (hrgJual != null) {
                datatable.columns('harga_jual').visible(true);
            }
            else {
                datatable.columns('harga_jual').visible(false);
            }
		});

        $(document).ready(function() {
            var datatable = $('#list_item_cust').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Product/GetDataItem',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data : {
                                id: '{{$dataProduct->id}}',
                                module: 'customer'
                            },
                        }
                    },
                    pageSize: 25,
                    serverPaging: true,
                    serverFiltering: false,
                    serverSorting: false,
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
                    input: $('#list_item_cust_search_query')
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
                        field: 'kode_customer',
                        title: 'Kode Customer',
                        width: 100,
                        autoHide: false,
                        textAlign: 'left',
                        template: function(row) {
                            return row.kode_customer.toUpperCase();
                        },
                    },
                    {
                        field: 'nama_customer',
                        title: 'Nama Customer',
                        width: 500,
                        autoHide: false,
                        textAlign: 'left',
                        template: function(row) {
                            return ucwords(row.nama_customer);
                        },
                    },
                    {
                        field: 'nama_kategori',
                        title: 'Nama Kategori',
                        width: 170,
                        autoHide: false,
                        textAlign: 'left',
                        template: function(row) {
                            return ucwords(row.nama_kategori);
                        },
                    },
                ],
            });
		});

        $(document).ready(function() {
            var datatable = $('#list_item_supp').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Product/GetDataItem',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data : {
                                id: '{{$dataProduct->id}}',
                                module: 'supplier'
                            },
                        }
                    },
                    pageSize: 25,
                    serverPaging: true,
                    serverFiltering: false,
                    serverSorting: false,
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
                    input: $('#list_item_supp_search_query')
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
                        field: 'kode_supplier',
                        title: 'Kode Supplier',
                        width: 100,
                        autoHide: false,
                        textAlign: 'left',
                        template: function(row) {
                            return row.kode_supplier.toUpperCase();
                        },
                    },
                    {
                        field: 'nama_supplier',
                        title: 'Nama Supplier',
                        width: 500,
                        autoHide: false,
                        textAlign: 'left',
                        template: function(row) {
                            return ucwords(row.nama_supplier);
                        },
                    },
                    {
                        field: 'nama_kategori',
                        title: 'Nama Kategori',
                        width: 170,
                        autoHide: false,
                        textAlign: 'left',
                        template: function(row) {
                            return ucwords(row.nama_kategori);
                        },
                    },
                ],
            });
		});

        $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
            var datatableCust = $("#list_item_cust").KTDatatable();
                datatableCust.reload();

            var datatableSupp = $("#list_item_supp").KTDatatable();
                datatableSupp.reload();

            var datatableSatuan = $("#table_satuan").KTDatatable();
                datatableSatuan.reload();
	    });

    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
