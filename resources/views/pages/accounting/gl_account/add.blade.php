@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h5 class="card-title text-white">Tambah Account</h5>
					</div>
                    <form action="{{ route('GLAccount.store') }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            <div class="row">
                                <legend class="font-weight-semibold"> Data Account</legend>
                                <div class="col-md-6">

                                    <fieldset>

                                        <div class="separator separator-solid separator-border-2 separator-dark"></div>
                                        <br>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Mother Account</label>
                                            <div class="col-sm-12">
                                                <select class="form-control select2 req" id="id_mother_account" name="id_mother_account">
                                                    <option label="Label" value="">All</option>
                                                    @foreach($motherAccount as $account)
                                                    <option value="{{$account->id}}">{{ucwords($account->account_number)}} - {{ucwords($account->account_name)}}</option>
                                                    @endforeach
                                                </select>
                                                <span class="form-text text-danger err" style="display:none;">*Harap Pilih Mother Account terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Account Name</label>
                                            <div class="col-sm-12">
                                                <input type="text" placeholder="Masukkan Nama Account" name="account_name" class="form-control req" autocomplete="off">
                                                <span class="form-text text-danger err" style="display:none;">*Harap Masukkan Nama Account terlebih dahulu!</span>
                                            </div>
                                        </div>

                                    </fieldset>
                                </div>

                                <div class="col-md-6">
                                    <fieldset>
                                        <div class="separator separator-solid separator-border-2 separator-dark"></div>
                                        <br>

                                        <div class="form-group">
											<label class="pl-5">Account Number :</label>
											<div class="input-group">
                                                <div class="col-2 pr-0">
												<input type="text" id="mother_account_number" name="mother_account_number" class="form-control text-center" readonly>
											</div>
											<div class="col-10 pl-0 ml-0">
                                                    <input type="text" placeholder="Masukkan Nomor Account" name="account_number" class="form-control req" autocomplete="off">

                                                </div>
                                                <span class="form-text text-danger err" style="display:none;">*Harap Masukkan Nomor Account terlebih dahulu!</span>
											</div>
										</div>

                                    </fieldset>
                                </div>

                            </div>
                        </div>

                        <div class="card-footer bg-white d-sm-flex justify-content-sm-between align-items-sm-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-light-danger font-weight-bold mr-2" onclick="window.location.href = '{{ url('/GLAccount') }}';">Keluar <i class="flaticon2-cancel icon-sm"></i></button>
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

            $('#id_mother_account').select2({
                placeholder: "Pilih Mother Account",
                allowClear: true
            });
        });

        $("#id_mother_account").on("change", function() {
            var mAcc = $(this).find('option:selected').text();
            var mAccArr = mAcc.split(' - ');
            $("#mother_account_number").val(mAccArr[0]);
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
