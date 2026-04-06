@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h5 class="card-title text-white">Detail Data Pengguna</h5>
					</div>

					<div class="card-body">
						<form>
						{{ csrf_field() }}
							<div class="row">
								<div class="col-md-6">
									<fieldset>
										<legend class="font-weight-semibold"><i class="icon-stack2"></i> Data Login Pengguna</legend>
                                        <div class="separator separator-solid separator-border-2 separator-dark"></div>
                                        <br>
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
											<label class="control-label col-sm-3">Status Pengguna</label>
											<div class="col-sm-2">
												<div class="checkbox-inline">
                                                    <label class="checkbox checkbox-lg">
                                                        @if ($dataUsers->active == "Y")
                                                        <input type="checkbox" id="aktif" checked disabled>
                                                        @else
                                                        <input type="checkbox" id="aktif" disabled>
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
												<select class="form-control select-search req" data-placeholder="Pilih Grup User" data-fouc id="usergroup" name="usergroup" disabled>
													<option></option>
													<option value="admin">Admin</option>
													<option value="penjualan">Penjualan</option>
													<option value="pembelian">Pembelian</option>
													<option value="gudang">Gudang</option>
                                                    <option value="cashier">Kasir</option>
                                                    <option value="operasional">Operasional</option>
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
                                        <br>
										<div class="form-group row">
											<label class="col-lg-3 col-form-label">Nama Pengguna</label>
											<div class="col-lg-9">
												<input type="text" id="nm_user" name="nm_user" placeholder="Masukkan Nama Pengguna" class="form-control req" value="{{$dataUsers->nama_user}}" autocomplete="off" readonly>
												<span class="form-text text-danger err" style="display:none;">*Harap Isi Nama Pengguna terlebih dahulu!</span>
											</div>
										</div>

										<div class="form-group row">
											<label class="col-lg-3 col-form-label">No. Telp Pengguna</label>
											<div class="col-lg-9">
												<input type="number" id="telp_user" name="telp_user" placeholder="Masukkan Telp. Pengguna" value="{{$dataUsers->telp_user}}" class="form-control angka req" readonly>
												<span class="form-text text-danger err" style="display:none;">*Harap Isi No. Telp Pengguna terlebih dahulu!</span>
											</div>
										</div>

										<div class="form-group row">
											<label class="col-lg-3 col-form-label">Email Pengguna</label>
											<div class="col-lg-9">
												<input type="text" id="email_user" name="email_user" placeholder="Masukkan Email Pengguna" value="{{$dataUsers->email_user}}" class="form-control req" readonly>
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

                            @if (Auth::user()->user_group == "super_admin" || Auth::user()->user_group == "admin")
                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset>
                                        <legend class="font-weight-semibold"><i class="icon-list"></i> Hak Akses Pengguna</legend>
                                        <div class="separator separator-solid separator-border-2 separator-dark"></div>
                                        <br>
                                        <div class="mb-7">
                                            <div class="row align-items-center">
                                                <div class="col-lg-9 col-xl-8">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-4 my-2 my-md-0">
                                                            <div class="input-icon">
                                                                <input type="text" class="form-control" placeholder="Search..." id="list_menu_search_query"/>
                                                                <span>
                                                                    <i class="flaticon2-search-1 text-muted"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="datatable datatable-bordered datatable-head-custom" id="list_menu"></div>

                                    </fieldset>
                                </div>
                            </div>
                            @endif

                            <div class="card-footer bg-white d-sm-flex justify-content-sm-between align-items-sm-center">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-light-danger font-weight-bold mr-2" id="cancel">Batal <i class="flaticon2-cancel icon-sm"></i></button>
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
			var user = "{{Auth::user()->user_name}}";
			if (user == "admin") {
				window.location.href = '{{ url("/Users") }}';
			}
			else {
				window.location.href = '{{ url("/") }}';
			}
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
            var datatable = $('#list_menu').KTDatatable({
                data: {
                    type: 'local',
                    source: listMenu,
                    pageSize: 10,
                    saveState : true,
                },

                layout: {
                    scroll: true,
                    height: 'auto',
                    footer: false
                },

                sortable: true,

                filterable: true,

                pagination: false,

                search: {
                    input: $('#list_menu_search_query')
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
                        field: 'menu',
                        title: 'Menu',
                        width: 'auto',
                    },
                    {
                        field: 'add',
                        title: 'Tambah',
                        width: '100',
                        textAlign: 'center',
                        template: function(row) {
                            var txtCheckbox = "<div class='checkbox-inline'>";
                                txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                if (row.add == "Y")
                                    txtCheckbox += "<input type='checkbox' class='text-center bisaTambah' value='"+row.id+"' name='bisaTambah' checked disabled>";
                                else {
                                    txtCheckbox += "<input type='checkbox' class='text-center bisaTambah' value='"+row.id+"' name='bisaTambah' disabled>";
                                }
                                txtCheckbox += "<span></span>";
                                txtCheckbox += "</label>";
                                txtCheckbox += "</div>";
                            return txtCheckbox;
                        },
                    },
                    {
                        field: 'edit',
                        title: 'Edit',
                        width: '100',
                        textAlign: 'center',
                        template: function(row) {
                            var txtCheckbox = "<div class='checkbox-inline'>";
                                txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                if (row.edit == "Y")
                                    txtCheckbox += "<input type='checkbox' class='text-center bisaEdit' value='"+row.id+"' name='bisaEdit' checked disabled>";
                                else {
                                    txtCheckbox += "<input type='checkbox' class='text-center bisaEdit' value='"+row.id+"' name='bisaEdit' disabled>";
                                }
                                txtCheckbox += "<span></span>";
                                txtCheckbox += "</label>";
                                txtCheckbox += "</div>";
                            return txtCheckbox;
                        },
                    },
                    {
                        field: 'delete',
                        title: 'Hapus',
                        width: '100',
                        textAlign: 'center',
                        template: function(row) {
                            var txtCheckbox = "<div class='checkbox-inline'>";
                                txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                if (row.delete == "Y")
                                    txtCheckbox += "<input type='checkbox' class='text-center bisaHapus' value='"+row.id+"' name='bisaHapus' checked disabled>";
                                else {
                                    txtCheckbox += "<input type='checkbox' class='text-center bisaHapus' value='"+row.id+"' name='bisaHapus' disabled>";
                                }
                                txtCheckbox += "<span></span>";
                                txtCheckbox += "</label>";
                                txtCheckbox += "</div>";
                            return txtCheckbox;
                        },
                    },
                    {
                        field: 'posting',
                        width: '100',
                        title: 'Posting',
                        textAlign: 'center',
                        template: function(row) {
                            var txtCheckbox = "<div class='checkbox-inline'>";
                                txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                if (row.posting == "Y")
                                    txtCheckbox += "<input type='checkbox' class='text-center bisaPosting' value='"+row.id+"' name='bisaPosting' checked disabled>";
                                else {
                                    txtCheckbox += "<input type='checkbox' class='text-center bisaPosting' value='"+row.id+"' name='bisaPosting' disabled>";
                                }
                                txtCheckbox += "<span></span>";
                                txtCheckbox += "</label>";
                                txtCheckbox += "</div>";
                            return txtCheckbox;
                        },
                    },
                    {
                        field: 'print',
                        width: '100',
                        title: 'Print',
                        textAlign: 'center',
                        template: function(row) {
                            var txtCheckbox = "<div class='checkbox-inline'>";
                                txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                if (row.print == "Y")
                                    txtCheckbox += "<input type='checkbox' class='text-center bisaPrint' value='"+row.id+"' name='bisaPrint' checked disabled>";
                                else {
                                    txtCheckbox += "<input type='checkbox' class='text-center bisaPrint' value='"+row.id+"' name='bisaPrint' disabled>";
                                }
                                txtCheckbox += "<span></span>";
                                txtCheckbox += "</label>";
                                txtCheckbox += "</div>";
                            return txtCheckbox;
                        },
                    },
                    {
                        field: 'export',
                        title: 'Export',
                        width: '100',
                        textAlign: 'center',
                        template: function(row) {
                            var txtCheckbox = "<div class='checkbox-inline'>";
                                txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                if (row.export == "Y")
                                    txtCheckbox += "<input type='checkbox' class='text-center bisaExport' value='"+row.id+"' name='bisaExport' checked disabled>";
                                else {
                                    txtCheckbox += "<input type='checkbox' class='text-center bisaExport' value='"+row.id+"' name='bisaExport' disabled>";
                                }
                                txtCheckbox += "<span></span>";
                                txtCheckbox += "</label>";
                                txtCheckbox += "</div>";
                            return txtCheckbox;
                        },
                    },
                    {
                        field: 'approve',
                        title: 'Approve',
                        width: '100',
                        textAlign: 'center',
                        template: function(row) {
                            var txtCheckbox = "<div class='checkbox-inline'>";
                                txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                if (row.approve == "Y")
                                    txtCheckbox += "<input type='checkbox' class='text-center bisaApprove' value='"+row.id+"' name='bisaApprove' checked disabled>";
                                else {
                                    txtCheckbox += "<input type='checkbox' class='text-center bisaApprove' value='"+row.id+"' name='bisaApprove' disabled>";
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
