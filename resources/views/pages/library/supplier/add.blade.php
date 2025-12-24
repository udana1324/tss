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
                                        <span class="nav-text">Barang-barang Supplier</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
					</div>

					<div class="card-body">
						<form action="{{ route('Supplier.store') }}" method="POST" id="form_add">
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
													<input type="text" class="form-control bg-slate-600 border-slate-600 border-1" placeholder="Auto Generated" name="kode_supp" id="kode_supp" autocomplete="off" readonly>
												</div>
											</div>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Kategori Supplier</label>
												<div class="col-lg-9">
													<select class="form-control select2 req" id="kategori_supp" name="kategori_supp">
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
													<input type="text" class="form-control req" autocomplete="off" maxlength="20" onkeypress="return validasiTelp(event)" name="telp_supp" id="telp_supp">
													{{-- <input type="text" class="tlp form-control req" autocomplete="off" style="width:55px; display:inline-block;" maxlength="3" onkeypress="return validasiangka(event);" name="head_telp_supp" id="head_telp_supp">
													-
													<input type="text" class="tlp form-control req" autocomplete="off" style="width:130px;display:inline-block;" maxlength="8" onkeypress="return validasiangka(event);" name="body_telp_supp" id="body_telp_supp"> --}}
											        <span class="form-text text-muted">Contoh : (999) 99999999</span>
											        <span class="form-text text-danger err" style="display:none;">*Harap masukkan no. telp supplier terlebih dahulu!</span>
												</div>
											</div>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">No. Fax Supplier</label>
												<div class="col-lg-9">
													<input type="text" class="form-control" autocomplete="off" maxlength="20" onkeypress="return validasiTelp(event)" name="fax_supp" id="fax_supp">
													{{-- <input type="text" class="fax form-control" autocomplete="off" style="width:55px; display:inline-block;" maxlength="3" onkeypress="return validasiangka(event);" name="head_fax_supp" id="head_fax_supp">
													-
													<input type="text" class="fax form-control" autocomplete="off" style="width:130px;display:inline-block;" maxlength="8" onkeypress="return validasiangka(event);" name="body_fax_supp" id="body_fax_supp"> --}}
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
													<input type="text" class="form-control req" autocomplete="off" placeholder="Masukkan Nama Supplier" name="nama_supp" id="nama_supp">
													<span class="form-text text-danger err" style="display:none;">*Harap masukkan nama supplier terlebih dahulu!</span>
												</div>
											</div>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">NPWP Supplier</label>
												<div class="col-lg-9">
													<input type="text" class="npwp form-control req" maxlength="2" style="width:60px;display:inline-block;text-align:center;" autocomplete="off" onkeypress="return validasiangka(event);" name="npwp_suppp1" id="npwp_suppp1">
													.
													<input type="text" class="npwp form-control req" maxlength="3" style="width:60px;display:inline-block;text-align:center;" autocomplete="off" onkeypress="return validasiangka(event);" name="npwp_suppp2" id="npwp_suppp2">
													.
													<input type="text" class="npwp form-control req" maxlength="3" style="width:60px;display:inline-block;text-align:center;" autocomplete="off" onkeypress="return validasiangka(event);" name="npwp_suppp3" id="npwp_suppp3">
													.
													<input type="text" class="npwp form-control req" maxlength="1" style="width:60px;display:inline-block;text-align:center;" autocomplete="off" onkeypress="return validasiangka(event);" name="npwp_suppp4" id="npwp_suppp4">
													-
													<input type="text" class="npwp form-control req" maxlength="3" style="width:60px;display:inline-block;text-align:center;" autocomplete="off" onkeypress="return validasiangka(event);" name="npwp_suppp5" id="npwp_suppp5">
													.
													<input type="text" class="npwp form-control req" maxlength="3" style="width:60px;display:inline-block;text-align:center;" autocomplete="off" onkeypress="return validasiangka(event);" name="npwp_suppp6" id="npwp_suppp6">
		                        					<span class="form-text text-muted"> Contoh : 99.999.999.9-999.999</span>
		                        					<span class="form-text text-danger err" style="display:none;">*Harap masukkan no. npwp supplier terlebih dahulu!</span>
												</div>
											</div>

											<div class="form-group row">
												<label class="col-lg-3 col-form-label">Email Supplier</label>
												<div class="col-lg-9">
													<input type="email" class="form-control" autocomplete="off" placeholder="Masukkan Email Supplier" name="email_supp" id="email_supp">
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

                                        <div class="col-lg-6 align-items-right" align="right">
                                            <div class="row align-items-right">
                                                <div class="col-md-11 my-md-0 align-items-right">
                                                    <button type="button" class="btn btn-primary font-weight-bold mr-2" data-toggle="modal" data-target="#modal_form_horizontal_add"> Tambah Alamat Baru</button>
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

                                        <div class="col-lg-6 align-items-right" align="right">
                                            <div class="row align-items-right">
                                                <div class="col-md-11 my-md-0 align-items-right">
                                                    <button type="button" class="btn btn-primary font-weight-bold mr-2" data-toggle="modal" data-target="#modal_list_product"> Tambah Barang Baru</button>
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
								<h5 class="modal-title">Tambah Alamat Supplier</h5>
							</div>

							<form class="form-horizontal" id="add-supp">
							{{ csrf_field() }}
							<div class="modal-body">
								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Alamat Supplier (*Max 100 Karakter)</label>
									<div class="col-lg-9">
										<input type="hidden" class="form-control" id="idAlamat">
										<textarea maxlength="100" class="form-control detilAlamat" id="alamat_supp" cols="4" style="resize:none;height:100px;"></textarea>
										<span class="form-text text-danger errAlamat" style="display:none;">*Harap masukkan alamat supplier terlebih dahulu!</span>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Kelurahan</label>
									<div class="col-lg-9">
										<input type="text" class="form-control" placeholder="Masukkan Kelurahan" id="kelurahanAlamat" autocomplete="off">
										<span class="form-text text-danger" style="display:none;">*Harap masukkan kelurahan terlebih dahulu!</span>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Kecamatan</label>
									<div class="col-lg-9">
										<input type="text" class="form-control detilAlamat" placeholder="Masukkan Kecamatan" id="kecamatanAlamat" autocomplete="off">
										<span class="form-text text-danger errAlamat" style="display:none;">*Harap masukkan kecamatan terlebih dahulu!</span>
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
									<label class="col-lg-3 col-form-label">Kode Pos</label>
									<div class="col-lg-9">
										<input type="text" class="form-control" maxlength="5" onkeypress='return validasiangka(event);' placeholder="Masukkan Kode Pos" id="kode_pos" autocomplete="off">
										<span class="form-text text-danger errAlamat" style="display:none;">*Harap masukkan kode pos terlebih dahulu!</span>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Jenis Alamat</label>
									<div class="col-lg-9">
                                        <select class="form-control select2 detilAlamat" id="jenis_alamat" style="width:100%;">
											<option label="Label"></option>
											<option value="NPWP">NPWP</option>
											<option value="Kantor">Kantor</option>
                                            <option value="Tukar Faktur">Tukar Faktur</option>
											<option value="Gudang/Pengiriman">Gudang/Pengiriman</option>
										</select>
										<span class="form-text text-danger errAlamat" style="display:none;">*Harap masukkan pilih jenis alamat terlebih dahulu!</span>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">Nama PIC</label>
									<div class="col-lg-9">
										<input type="text" class="form-control detilAlamat" placeholder="Masukkan PIC Alamat" id="pic" autocomplete="off">
										<span class="form-text text-danger errAlamat" style="display:none;">*Harap masukkan pic alamat terlebih dahulu!</span>
									</div>
								</div>

								<div class="form-group row">
									<label class="col-lg-3 col-form-label">No. Telp PIC</label>
									<div class="col-lg-9">
										<input type="text" class="form-control detilAlamat" maxlength="14" onkeypress="return validasiangka(event);" name="telp_pic" id="telp_pic" autocomplete="off">
										<span class="form-text text-muted">Contoh : 0812123456789</span>
										<span class="form-text text-danger errAlamat" style="display:none;">*Harap masukkan no. telp pic terlebih dahulu!</span>
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

                <!-- Modal form list barang -->
				<div id="modal_list_product" class="modal fade">
				    <div class="modal-dialog modal-xl">
					    <div class="modal-content">
						    <div class="modal-header bg-primary">

							    <h5 class="modal-title text-white">Daftar Barang</h5>
						    </div>
						    <div class="modal-body">
                                <div class="mb-7">
                                    <div class="row align-items-center">
                                        <div class="col-lg-10">
                                            <div class="row align-items-center">
                                                <div class="col-md-4 my-2 my-md-0">
                                                    <div class="align-items-center">
                                                        <label style="display: inline-block;"></label>
                                                        <div class="input-icon">
                                                            <input type="text" class="form-control" placeholder="Search..." id="list_product_search_query"/>
                                                            <span>
                                                                <i class="flaticon2-search-1 text-muted"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 my-2 my-md-0">
                                                    <div class="align-items-center">
                                                        <label class="mr-3 mb-0 d-none d-md-block">Merk :</label>
                                                        <select class="form-control select2" id="list_product_search_merk">
                                                            <option value="">All</option>
                                                            @foreach($merk as $rowMerk)
                                                            <option value="{{$rowMerk->nama_merk}}">{{ucwords($rowMerk->nama_merk)}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 my-2 my-md-0">
                                                    <div class="align-items-center">
                                                        <label class="mr-3 mb-0 d-none d-md-block">Kategori :</label>
                                                        <select class="form-control select2" id="list_product_search_kategori">
                                                            <option value="">All</option>
                                                            @foreach($kategori as $rowKategori)
                                                            <option value="{{$rowKategori->nama_kategori}}">{{ucwords($rowKategori->nama_kategori)}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end: Search Form-->
                                <!--begin: Datatable-->

                                <div class="datatable datatable-bordered datatable-head-custom" id="list_product"></div>

						    </div>

						    <div class="modal-footer">
							    <button type="button" class="btn btn-link" data-dismiss="modal">Tutup</button>
						    </div>
					    </div>
				    </div>
			    </div>
                <!-- /form list barang -->
			<!-- /content area -->
@endsection
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#kategori_supp').select2({
                placeholder: "Pilih Kategori Supplier",
                allowClear: true
            });

            $('#jenis_alamat').select2({
                placeholder: "Pilih Jenis Alamat",
                allowClear: true
            });

            $('#list_product_search_merk').select2({
                allowClear: true,
                placeholder: "Silahkan pilih merk barang"
            });

            $('#list_product_search_kategori').select2({
                allowClear: true,
                placeholder: "Silahkan pilih kategori barang"
            });
        });

		function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

		$("#cancel").on('click', function(e) {
            Swal.fire({
                title: "Batal?",
                text: "Apakah anda ingin membatalkan penambahan Supplier?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    window.location.href = "{{ url('/Supplier') }}";
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

        $(document).on('keyup', '.fax', function() {
            txt = $(this).val();
            count = txt.length;
            if (count == 3) {
            	$(this).next('.fax').focus();
            }
        });

        $(document).on('keyup', '.npwp', function() {
            txt1 = $("#npwp_suppp1").val();
            txt2 = $("#npwp_suppp2").val();
            txt3 = $("#npwp_suppp3").val();
            txt4 = $("#npwp_suppp4").val();
            txt5 = $("#npwp_suppp5").val();
            txt6 = $("#npwp_suppp6").val();
            count1 = txt1.length;
            count2 = txt2.length;
            count3 = txt3.length;
            count4 = txt4.length;
            count5 = txt5.length;

            if (txt2=="") {
            	if (count1 == 2) {
	            	$("#npwp_suppp2").focus();
	            }
            }
            else if (txt3=="") {
            	if (count2 == 3) {
	            	$("#npwp_suppp3").focus();
	            }
            }
             else if (txt4=="") {
            	if (count3 == 3) {
	            	$("#npwp_suppp4").focus();
	            }
            }
             else if (txt5=="") {
            	if (count4 == 1) {
	            	$("#npwp_suppp5").focus();
	            }
            }
             else if (txt6=="") {
            	if (count5 == 3) {
	            	$("#npwp_suppp6").focus();
	            }
            }

        });

        $(document).on('keyup', '.tlp', function() {
            txt = $(this).val();
            count = txt.length;
            if (count == 3) {
            	$(this).next('.tlp').focus();
            }
        });

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
            var datatable = $('#table_address_sup').KTDatatable();
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
                            url: "/Supplier/StoreAddress",
                            method: 'POST',
                            dataType : 'json',
                            data: {
                                idAlamat : $("#idAlamat").val(),
                                idSupplier : "",
                                alamat : $("#alamat_supp").val(),
                                kecamatan : $("#kecamatanAlamat").val(),
                                kelurahan : $("#kelurahanAlamat").val(),
                                kota : $("#kotaAlamat").val(),
                                kodePos : $("#kode_pos").val(),
                                jenisAlamat : $("#jenis_alamat").val(),
                                pic : $("#pic").val(),
                                noPic : $("#telp_pic").val()
                            },
                            success: function(result){
                                if (result == "success") {
                                    Swal.fire(
                                        "Sukses!",
                                        "Data Berhasil disimpan!.",
                                        "success"
                                    )
                                    $("#idAlamat").val("");
                                    $("#alamat_supp").val("");
                                    $("#kecamatanAlamat").val("");
                                    $("#kelurahanAlamat").val("");
                                    $("#kotaAlamat").val("");
                                    $("#kode_pos").val("");
                                    $("#jenis_alamat").val("").trigger('change');
                                    $("#pic").val("");
                                    $("#telp_pic").val("");
                                    $('#modal_form_horizontal_add').modal('hide');
                                    getAlamatDraft();
                                }
                                else if (result == "failNpwp") {
                                    Swal.fire(
                                        "Gagal!",
                                        "Hanya Dapat Menambahkan 1 Alamat NPWP !",
                                        "warning"
                                    )
                                    $('#modal_form_horizontal_add').modal('hide');
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
				$("#alamat_supp").val("");
				$("#kecamatanAlamat").val("");
				$("#kelurahanAlamat").val("");
				$("#kotaAlamat").val("");
				$("#kode_pos").val("");
				$("#jenis_alamat").val("").trigger('change');
				$("#pic").val("");
				$("#telp_pic").val("");
	        }
	    });

        function getAlamatDraft() {
			var datatable = $('#table_address_sup').KTDatatable();
                datatable.setDataSourceParam('idSupplier','');
                datatable.reload();
		}

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
                                idSupplier : ''
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
                        title: 'Default',
                        width: 'auto',
                        textAlign: 'center',
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
                            var txtAction = "<a href='#' class='btn btn-sm btn-clean btn-icon edit' title='Ubah' onclick='editAddressData("+row.id+");return false;'>";
                                txtAction += "<i class='la la-edit'></i>";
                                txtAction += "</a>";
                                txtAction += "<a href='#' class='btn btn-sm btn-clean btn-icon' title='Hapus' onclick='deleteAddressData("+row.id+");return false;'>";
                                txtAction += "<i class='la la-trash'></i>";
                                txtAction += "</a>";

                            return txtAction;
                        },
                    },
                ],
            });
		});

        function editAddressData(id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/Supplier/EditAddress",
                method: 'POST',
                data: {
                    idAddress: id
                },
                success: function(result){
                    $("#idAlamat").val(result.id);
                    $("#alamat_supp").val(result.alamat_supplier);
                    $("#kelurahanAlamat").val(result.kelurahan);
                    $("#kecamatanAlamat").val(result.kecamatan);
                    $("#kotaAlamat").val(result.kota);
                    $("#kode_pos").val(result.kode_pos);
                    $("#jenis_alamat").val(result.jenis_alamat).trigger('change');
                    $("#pic").val(result.pic_alamat);
                    $("#telp_pic").val(result.telp_pic);
                    $('#modal_form_horizontal_add').modal('show');
                }
            });
        }

        function deleteAddressData(id) {
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
                        url: "/Supplier/DeleteAddress",
                        method: 'POST',
                        data: {
                            idAddress: id
                        },
                        success: function(result){
                            Swal.fire(
                                "Sukses!",
                                "Data Berhasil dihapus!.",
                                "success"
                            )
                        }
                    });
                    var datatable = $("#table_address_sup").KTDatatable();
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
                url: "/Supplier/SetDefault",
                method: 'POST',
                data: {
                    idAddress: id
                },
                success: function(result){
                    Swal.fire(
                        "Sukses!",
                        "Set Alamat Default Berhasil!.",
                        "success"
                    )
                }
            });
            var datatable = $("#table_address_sup").KTDatatable();
            datatable.reload();
        }

        $(document).ready(function() {
            var datatable = $('#list_item').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Supplier/GetDataItem',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            data : {
                                idSupp: ""
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
                    // {
                    //     field: 'nama_satuan',
                    //     title: 'Nama Satuan',
                    //     width: 170,
                    //     autoHide: false,
                    //     textAlign: 'left',
                    //     template: function(row) {
                    //         return ucwords(row.nama_satuan);
                    //     },
                    // },
                    {
                        field: 'harga_beli_last',
                        title: 'Harga Beli',
                        width: 'auto',
                        autoHide: false,
                        textAlign: 'right',
                        template: function(row) {
                            if (row.harga_beli_last != null) {
                                return parseFloat(row.harga_beli_last).toLocaleString('id-ID', { maximumFractionDigits: 2});
                            }
                            else {
                                return 0;
                            }
                        },
                    },
                    {
                        field: 'actions',
                        title: 'Aksi',
                        textAlign: 'center',
                        overflow: 'visible',
                        width: 'auto',
                        autoHide:false,
                        template: function(row) {
                            var txtAction = "<a href='#' class='btn btn-sm btn-clean btn-icon' title='Hapus' onclick='deleteItem("+row.id+");return false;'>";
                                txtAction += "<i class='la la-trash'></i>";
                                txtAction += "</a>";

                            return txtAction;
                        },
                    },
                ],
            });
		});

        $("#list_product").on('click', 'table .addToList', function() {
            var idItem = $(this).val();

            Swal.fire({
                title: "Tambahkan Data?",
                text: "Apakah anda ingin menambah barang ini pada supplier ?",
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
                        url: "/Supplier/AddSupplierProduct",
                        method: 'POST',
                        data: {
                            id_item: idItem,
                            id_supplier: ''
                        },
                        success: function(result){
                            Swal.fire(
                                "Berhasil!",
                                "Barang Berhasil ditambahkan ke supplier !",
                                "success"
                            )

                            var datatable = $('#list_product').KTDatatable();
                                datatable.setDataSourceParam('id_supplier', '');
                                datatable.reload();
                            var datatable2 = $('#list_item').KTDatatable();
                                datatable2.reload();
                        }
                    });
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
        });

        $(document).ready(function() {

            var datatable = $('#list_product').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Supplier/GetProduct',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
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
                    input: $('#list_product_search_query')
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
                        field: 'nama_item',
                        title: 'Nama',
                        width: 'auto',
                        textAlign: 'left',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                            txt += '<span class="font-weight-bold">'+ucwords(row.nama_item)+'</span><br />';
                            if(row.value_spesifikasi != null) {
                                txt += '<span class="label label-md label-outline-primary label-inline mr-2 mb-1 mt-1">' +'('+row.value_spesifikasi+')'+row.kode_item.toUpperCase()+ '</span>';
                            }
                            else {
                                txt += '<span class="label label-md label-outline-primary label-inline mr-2 mb-1 mt-1">' +row.kode_item.toUpperCase()+ '</span>';
                            }
                            txt += '<span class="label label-md label-outline-primary label-inline mr-2 mb-1 mt-1">' +row.nama_merk.toUpperCase()+ '</span>';
                            txt += '<span class="label label-md label-outline-primary label-inline mr-2 mb-1 mt-1">' +row.nama_kategori.toUpperCase()+ '</span>';

                            return txt;
                        },
                    },
                    {
                        field: 'nama_kategori',
                        title: 'Nama',
                        width: 'auto',
                        textAlign: 'left',
                        visible:false,
                        template: function(row) {
                            var txt = "";
                            txt += '<span class="label label-md label-outline-primary label-inline mr-2 mb-1 mt-1">' +row.nama_kategori.toUpperCase()+ '</span>';

                            return txt;
                        },
                    },
                    {
                        field: 'Actions',
                        title: 'Aksi',
                        sortable: false,
                        width: 'auto',
                        overflow: 'visible',
                        autoHide: false,
                        textAlign: 'center',
                        template: function(row) {
                            return "<button type='button' class='btn btn-primary btn-icon addToList' data-popup='tooltip' title='Tambah' value='" + row.id +"'><i class='flaticon2-plus'></i></button>";
                        },
                    }
                ],
            });

            $('#list_product_search_merk').on('change', function() {
                datatable.search($(this).val(), 'nama_merk');
            });

            $('#list_product_search_kategori').on('change', function() {
                datatable.search($(this).val(), 'nama_kategori');
            });
        });

        function deleteItem(id) {
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
                        url: "/Supplier/DeleteProduct",
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
                    var datatable = $("#list_item").KTDatatable();
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

        $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
            var datatable = $("#list_item").KTDatatable();
                datatable.reload();
	    });

        $("#modal_list_product").on('show.bs.modal', function(e) {
	        var datatablItem = $("#list_product").KTDatatable();
            datatablItem.reload();
	    });

    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
