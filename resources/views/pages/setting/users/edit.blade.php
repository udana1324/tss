@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h5 class="card-title  text-white">Ubah Data Pengguna</h5>
					</div>

					<div class="card-body">
						<form action="{{ route('Users.store') }}" method="POST" id="form_add">
						{{ csrf_field() }}
							<div class="row">
								<div class="col-md-6">
									<fieldset>
										<legend class="font-weight-semibold"><i class="icon-stack2"></i> Data Login Pengguna</legend>
                                        <div class="separator separator-solid separator-border-2 separator-dark"></div>
										<div class="form-group row">
											<label class="col-lg-3 col-form-label">Username</label>
											<div class="col-lg-9">
												<input type="hidden" value="update" name="metode">
												<input type="hidden" class="form-control" name="userId" id="userId" value="{{$dataUsers->id_user}}" readonly>
												<input type="hidden" class="form-control" name="profileId" id="profileId" value="{{$dataUsers->id}}" readonly>
												<input type="text" class="form-control" name="username" id="username" value="{{$dataUsers->user_name}}" autocomplete="off" readonly>
											</div>
										</div>

										<div class="form-group row">
											<label class="control-label col-lg-3">Status Pengguna</label>
											<div class="col-lg-9">
                                                <div class="checkbox-inline">
                                                    <label class="checkbox checkbox-lg">
                                                        @if ($dataUsers->active == "Y")
                                                        <input type="checkbox" id="aktif" checked>
                                                        @else
                                                        <input type="checkbox" id="aktif">
                                                        @endif
                                                        <span></span>Aktif
                                                    </label>
                                                </div>
												<input type="hidden" id="flagAktif" name="flagAktif" class="form-control" value="{{$dataUsers->active}}" readonly>
											</div>
										</div>

										<div class="form-group row">
											<label class="control-label col-sm-3">Grup User</label>
											<div class="col-sm-9">
												<select class="form-control select2 req" id="usergroup" name="usergroup">
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
												<span class="form-text text-danger err" style="display:none;">*Harap Pilih Grup Pengguna terlebih dahulu!</span>
											</div>
										</div>

									</fieldset>
								</div>

								<div class="col-md-6">
									<fieldset>
					                	<legend class="font-weight-semibold"><i class="icon-list"></i> Profil Pengguna</legend>
                                        <div class="separator separator-solid separator-border-2 separator-dark"></div>
										<div class="form-group row">
											<label class="col-lg-3 col-form-label">Nama Pengguna</label>
											<div class="col-lg-9">
												<input type="text" id="nm_user" name="nm_user" placeholder="Masukkan Nama Pengguna" class="form-control req" value="{{$dataUsers->nama_user}}" autocomplete="off">
												<span class="form-text text-danger err" style="display:none;">*Harap Isi Nama Pengguna terlebih dahulu!</span>
											</div>
										</div>

										<div class="form-group row">
											<label class="col-lg-3 col-form-label">No. Telp Pengguna</label>
											<div class="col-lg-9">
												<input type="number" id="telp_user" name="telp_user" placeholder="Masukkan Telp. Pengguna" value="{{$dataUsers->telp_user}}" class="form-control angka req">
												<span class="form-text text-danger err" style="display:none;">*Harap Isi No. Telp Pengguna terlebih dahulu!</span>
											</div>
										</div>

										<div class="form-group row">
											<label class="col-lg-3 col-form-label">Email Pengguna</label>
											<div class="col-lg-9">
												<input type="text" id="email_user" name="email_user" placeholder="Masukkan Email Pengguna" value="{{$dataUsers->email_user}}" class="form-control req">
												<span class="form-text text-danger err" style="display:none;">*Harap Isi Email Pengguna terlebih dahulu!</span>
											</div>
										</div>

										<div class="form-group row">
											<label class="col-lg-3 col-form-label"></label>
											<div class="col-lg-9">
												<button type="button" class="btn btn-primary font-weight-bold" id="btnPassword" data-toggle="modal" data-target="#modal_form_password">Ubah Password</button>
											</div>
										</div>

									</fieldset>
								</div>
							</div>

                            <div class="card-footer bg-white d-sm-flex justify-content-sm-between align-items-sm-center">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-light-danger font-weight-bold mr-2" id="cancel">Batal <i class="flaticon2-cancel icon-sm"></i></button>
                                </div>

                                <div class="mt-2 mt-sm-0">
                                    <button type="submit" class="btn btn-light-primary font-weight-bold mr-2" id="btnSubmit"> Simpan <i class="flaticon-paper-plane-1"></i></button>
                                </div>
                            </div>
						</form>
						<form action="{{ route('Users.UbahPass') }}" method="POST" id="form_password">
                            {{ csrf_field() }}
                            <!-- Horizontal form modal password-->
							<div id="modal_form_password" class="modal fade">
								<div class="modal-dialog modal-lg">
									<div class="modal-content">
										<div class="modal-header bg-gray">

											<h5 class="modal-title">Ubah Password</h5>
										</div>
										<div class="modal-body">
											<div class="form-group row">
                                                <label class="col-lg-3 col-form-label">Password Lama</label>
                                                <div class="input-group">
                                                    <input type="hidden" class="form-control" name="user_id" id="user_id" value="{{$dataUsers->id_user}}" readonly>
                                                    <input type="password" id="old_password" name="old_password" class="form-control pwd reqPass">
                                                    <span class="input-group-btn">
                                                      <button class="btn btn-icon btn-light-primary reveal" type="button"><i class="flaticon-eye"></i></button>
                                                    </span>
                                                </div>
                                                <span class="form-text text-danger errPass" style="display:none;">*Harap Isi Password Lama terlebih dahulu!</span>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label">Password Baru</label>
                                                <div class="input-group">
                                                    <input type="password" id="new_password" name="new_password" class="form-control pwdBaru reqPass">
                                                    <span class="input-group-btn">
                                                      <button class="btn btn-icon btn-light-primary revealBaru" type="button"><i class="flaticon-eye"></i></button>
                                                    </span>
                                                </div>
                                                <span class="form-text text-danger errPass" style="display:none;">*Harap Isi Password Baru terlebih dahulu!</span>
											</div>
										</div>

										<div class="modal-footer">
                                            <button type="submit" class="btn btn-primary" id="btnSubmitPassword">Simpan</button>
											<button type="button" class="btn btn-link" data-dismiss="modal">Tutup</button>
										</div>
									</div>
								</div>
							</div>
						 <!-- /horizontal form Alamat -->
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

        $(".reveal").on('mousedown',function() {
            var $pwd = $(".pwd");
            if ($pwd.attr('type') === 'password') {
                $pwd.attr('type', 'text');
            } else {
                $pwd.attr('type', 'password');
            }
        });

        $(".reveal").on('mouseup',function() {
            var $pwd = $(".pwd");
            if ($pwd.attr('type') === 'password') {
                $pwd.attr('type', 'text');
            } else {
                $pwd.attr('type', 'password');
            }
        });

        $(".revealBaru").on('mousedown',function() {
            var $pwdBaru = $(".pwdBaru");
            if ($pwdBaru.attr('type') === 'password') {
                $pwdBaru.attr('type', 'text');
            } else {
                $pwdBaru.attr('type', 'password');
            }
        });

        $(".revealBaru").on('mouseup',function() {
            var $pwdBaru = $(".pwdBaru");
            if ($pwdBaru.attr('type') === 'password') {
                $pwdBaru.attr('type', 'text');
            } else {
                $pwdBaru.attr('type', 'password');
            }
        });

        $("#cancel").on('click', function(e) {
            Swal.fire({
                title: "Batal?",
                text: "Apakah anda ingin membatalkan perubahan data Pengguna?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if (result.value) {
                    window.location.href = '{{ url("/Users") }}';
                    // Swal.fire(
                    //     "Deleted!",
                    //     "Your file has been deleted.",
                    //     "success"
                    // )
                    // result.dismiss can be "cancel", "overlay",
                    // "close", and "timer"
                } else if (result.dismiss === "cancel") {
                    // Swal.fire(
                    //     "Cancelled",
                    //     "Your imaginary file is safe :)",
                    //     "error"
                    // )
                    e.preventDefault();
                }
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

		$(document).ready(function() {
			var group = "{{$dataUsers->user_group}}";
			$("#usergroup").val(group).trigger("change");

			var listMenu = @json($hakAksesUser);
			var opt =[];
            for (var i = 0; i < listMenu.length;i++) {
            	opt[i] = listMenu[i].menu_id;
            }
            $("#module option:selected").prop("selected", false);
            $("#module").selectpicker('val', opt);
            $("#module").selectpicker('render');
		});

	    $("#form_add").submit(function(e){
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

		$("#aktif").change(function() {
		     if(this.checked) {
		        $("#flagAktif").val("Y");
		    }
		    else {
		    	$("#flagAktif").val("N");
		    }
		});

		$("#form_password").submit(function(e){
            e.preventDefault();

            Swal.fire({
                title: "Ubah Password?",
                text: "Apakah anda ingin merubah Password?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    var count = 0;
                    $(".reqPass").each(function(){
                        if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                            $(this).closest('.form-group').find('.errPass').show();
                            count = parseInt(count) + 1;
                        }
                        else {
                            $(this).closest('.form-group').find('.errPass').hide();
                        }
                    });
                    if (count == 0) {
                        $("#form_password").off("submit").submit();
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
    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
