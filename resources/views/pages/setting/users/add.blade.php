@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h5 class="card-title text-white">Tambah Pengguna</h5>
					</div>

					<div class="card-body">
						<form action="{{ route('Users.store') }}" method="POST" id="form_add">
						{{ csrf_field() }}
							<div class="row">
								<div class="col-md-6">
									<fieldset>
										<legend class="font-weight-semibold"> Data Login Pengguna</legend>
                                        <div class="separator separator-solid separator-border-2 separator-dark"></div>
                                        <br>
										<div class="form-group row">
											<label class="col-lg-3 col-form-label">Username</label>
											<div class="col-lg-9">
												<input type="hidden" value="store" name="metode">
												<input type="text" class="form-control req" name="username" id="username" autocomplete="off">
												<span class="form-text text-danger errU" style="display:none;">*Harap Isi Username Pengguna terlebih dahulu!</span>
												<span class="form-text text-danger errUser" style="display:none;">*Username telah digunakan!</span>
												<span class="form-text text-danger errLength" style="display:none;">*Harap Masukkan Minimum 4 Karakter!</span>
												<span class="form-text text-success errSuccess" style="display:none;">*Username dapat digunakan!</span>
											</div>
										</div>

										<div class="form-group row">
											<label class="control-label col-sm-3">User Password(Default)</label>
											<div class="col-sm-9">
												<input type="text" class="form-control" value="123456" readonly>
											</div>
										</div>

										<div class="form-group row">
											<label class="control-label col-sm-3">Grup User</label>
											<div class="col-sm-9">
												<select class="form-control select2 req" data-placeholder="Pilih Grup User" id="usergroup" name="usergroup">
													<option label="Label"></option>
													<option value="admin">Admin</option>
													<option value="penjualan">Penjualan</option>
													<option value="pembelian">Pembelian</option>
													<option value="gudang">Gudang</option>
                                                    <option value="operasional">Operasional</option>
												</select>
												<span class="form-text text-danger err" style="display:none;">*Harap Pilih Grup Pengguna terlebih dahulu!</span>
											</div>
										</div>

										<div class="form-group row">
											<label class="control-label col-sm-3">Hak Akses Menu User</label>
											<div class="col-sm-9">
												<select class="form-control selectpicker" multiple="multiple" id="module" name="module[]" data-live-search="true" data-actions-box="true" data-dropup-auto="false" title="Pilih Hak Akses Menu" data-selected-text-format="count">
                                                    @foreach ($dataParent as $parentMenu)
                                                    <optgroup label="{{ucwords($parentMenu->menu)}}">
                                                        @foreach ($parentMenu->child as $childMenu)
                                                            <option data-tokens="{{ucwords($parentMenu->menu)}}" value="{{$childMenu->id}}">{{ucwords($childMenu->menu)}}</option>
                                                        @endforeach
                                                    </optgroup>
                                                    @endforeach
												</select>
												<span class="form-text text-danger err" style="display:none;">*Harap Pilih Hak Akses Menu terlebih dahulu!</span>
											</div>
										</div>

									</fieldset>
								</div>

								<div class="col-md-6">
									<fieldset>
					                	<legend class="font-weight-semibold"> Profil Pengguna</legend>
                                        <div class="separator separator-solid separator-border-2 separator-dark"></div>
                                        <br>
										<div class="form-group row">
											<label class="col-lg-3 col-form-label">Nama Pengguna</label>
											<div class="col-lg-9">
												<input type="text" id="nm_user" name="nm_user" placeholder="Masukkan Nama Pengguna" class="form-control req" autocomplete="off">
												<span class="form-text text-danger err" style="display:none;">*Harap Isi Nama Pengguna terlebih dahulu!</span>
											</div>
										</div>

										<div class="form-group row">
											<label class="col-lg-3 col-form-label">No. Telp Pengguna</label>
											<div class="col-lg-9">
												<input type="number" id="telp_user" name="telp_user" placeholder="Masukkan Telp. Pengguna" class="form-control angka req">
												<span class="form-text text-danger err" style="display:none;">*Harap Isi No. Telp Pengguna terlebih dahulu!</span>
											</div>
										</div>

										<div class="form-group row">
											<label class="col-lg-3 col-form-label">Email Pengguna</label>
											<div class="col-lg-9">
												<input type="text" id="email_user" name="email_user" placeholder="Masukkan Email Pengguna" class="form-control req">
												<span class="form-text text-danger err" style="display:none;">*Harap Isi Email Pengguna terlebih dahulu!</span>
											</div>
										</div>

									</fieldset>
								</div>
							</div>

							<div class="card-footer bg-white d-sm-flex justify-content-sm-between align-items-sm-center">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-light-danger font-weight-bold mr-2" onclick="window.location.href = '{{ url('/Users') }}';">Keluar <i class="flaticon2-cancel icon-sm"></i></button>
                                </div>

                                <div class="mt-2 mt-sm-0">
                                    <button type="submit" class="btn btn-light-primary font-weight-bold mr-2" id="btnSubmit" style="display: none;"> Simpan <i class="flaticon-paper-plane-1"></i></button>
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
            $('#usergroup').select2({
                placeholder: "Pilih Grup Pengguna",
                allowClear: true
            });
        });

	    $(".angka").on('keypress', function(e) {
			if (e.which == 46 || e.keyCode == 46) {
				e.preventDefault();
			}
			else if (e.which == 45 || e.keyCode == 45) {
				e.preventDefault();
			}
			else if (e.which == 44 || e.keyCode == 44) {
				e.preventDefault();
			}
			else if (e.which == 43 || e.keyCode == 43) {
				e.preventDefault();
			}
		});

	    $(document).on('change', "#username", function() {
			if ($(this).val() != "" && $(this).val().length >= 4) {
				$.ajaxSetup({
                  	headers: {
                    	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  	}
              	});
               	$.ajax({
                  	url: "/Users/CheckUser",
                  	method: 'POST',
                  	data: {
                     	username: $('#username').val()
                  	},
                  	success: function(result){
                     	if (result.length > 0) {
                     		$(".errU").hide();
                     		$(".errUser").show();
							$(".errSuccess").hide();
							$(".errLength").hide();
							$("#btnSubmit").hide();
                     	}
                     	else {
                     		$(".errU").hide();
                     		$(".errUser").hide();
                     		$(".errLength").hide();
							$(".errSuccess").show();
							$("#btnSubmit").show();
                     	}
                  	}
              	});
			}
			else if ($(this).val().length < 4) {
				$(".errU").hide();
                $(".errUser").hide();
				$(".errSuccess").hide();
				$(".errLength").show();
				$("#btnSubmit").hide();
			}
			else {
				$(".err").show();
				$(".errUser").hide();
				$(".errLength").hide();
				$(".errSuccess").hide();
				$("#btnSubmit").hide();
			}
	    });

	    $("#form_add").submit(function(e){
	    	$(".req").each(function(){
			    if($(this).val() == "" || $(this).children("option:selected").val() == ""){
			    	$(this).closest('.form-group').find('.err').show();
			      	e.preventDefault();
			  	}
			  	else {
			  		$(this).closest('.form-group').find('.err').hide();
                      var array = $("#module").val();
			  	}
			});
		});

    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
