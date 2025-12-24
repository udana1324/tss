@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h5 class="card-title text-white">Tambah Mother Account</h5>
					</div>
                    <form action="{{ route('GLMotherAccount.store') }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            <div class="row">
                                <legend class="font-weight-semibold"> Data Mother Account</legend>

                                <div class="col-md-6">

                                    <fieldset>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Mother Account Number</label>
                                            <div class="col-sm-12">
                                                <input type="text" placeholder="Masukkan Nomor Account" name="account_number" class="form-control req" autocomplete="off">
                                                <span class="form-text text-danger err" style="display:none;">*Harap Masukkan Nomor Account terlebih dahulu!</span>
                                            </div>
                                        </div>



                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Saldo Normal</label>
                                            <div class="col-sm-12">
                                                <select class="form-control select2 req" id="default_side" name="default_side">
                                                    <option label="Label"></option>
                                                    <option value="debet">Debet</option>
                                                    <option value="credit">Credit</option>
                                                </select>
                                                <span class="form-text text-danger err" style="display:none;">*Harap Pilih Sisi Penambahan terlebih dahulu!</span>
                                            </div>
                                        </div>


                                    </fieldset>
                                </div>

                                <div class="col-md-6">
                                    <fieldset>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Mother Account Name</label>
                                            <div class="col-sm-12">
                                                <input type="text" placeholder="Masukkan Nama Account" name="account_name" class="form-control req" autocomplete="off">
                                                <span class="form-text text-danger err" style="display:none;">*Harap Masukkan Nama Account terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Grup</label>
                                            <div class="col-sm-12">
                                                <select class="form-control select2 req" id="group" name="group">
                                                    <option label="Label"></option>
                                                    <option value="A">Aktiva</option>
                                                    <option value="L">Liabilitas dan Ekuitas</option>
                                                    <option value="I">Pendapatan</option>
                                                    <option value="C">Biaya</option>
                                                </select>
                                                <span class="form-text text-danger err" style="display:none;">*Harap Pilih Sisi Penambahan terlebih dahulu!</span>
                                            </div>
                                        </div>

                                    </fieldset>
                                </div>

                            </div>
                        </div>

                        <div class="card-footer bg-white d-sm-flex justify-content-sm-between align-items-sm-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-light-danger font-weight-bold mr-2" onclick="window.location.href = '{{ url('/GLMotherAccount') }}';">Keluar <i class="flaticon2-cancel icon-sm"></i></button>
                            </div>

                            <div class="mt-2 mt-sm-0">
                                <button type="submit" class="btn btn-light-primary font-weight-bold mr-2"> Simpan <i class="flaticon-paper-plane-1"></i></button>
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
            $('#default_side').select2({
                placeholder: "Pilih Saldo Normal",
                allowClear: true
            });

            $('#group').select2({
                placeholder: "Pilih Group",
                allowClear: true
            });
        });

        $("#form_add").submit(function(e){
            e.preventDefault();

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

	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
