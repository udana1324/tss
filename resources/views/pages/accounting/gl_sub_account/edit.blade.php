@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h5 class="card-title text-white">Ubah Sub Account</h5>
					</div>
                    <form action="{{ route('GLSubAccount.update', $dataAccount->id) }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            @method('PUT')
                            <div class="row">
                                <legend class="font-weight-semibold"> Data Sub Account</legend>
                                <div class="col-md-6">

                                    <fieldset>

                                        <div class="separator separator-solid separator-border-2 separator-dark"></div>
                                        <br>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Mother Account</label>
                                            <div class="col-sm-12">
                                                <select class="form-control select2 req front_number" id="id_mother_account" name="id_mother_account">
                                                    <option label="Label" value="">All</option>
                                                    @foreach($motherAccount as $account)
                                                    <option value="{{$account->id}}">{{ucwords($account->account_number)}} - {{ucwords($account->account_name)}}</option>
                                                    @endforeach
                                                </select>
                                                <span class="form-text text-danger err" style="display:none;">*Harap Pilih Mother Account terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
											<label class="pl-5">Sub Account Number :</label>
											<div class="input-group">
                                                <div class="col-2 pr-0">
												<input type="text" id="front_account_number" name="front_account_number" class="form-control text-center" readonly>
											</div>
											<div class="col-10 pl-0 ml-0">
                                                    <input type="text" placeholder="Masukkan Nomor Account" name="account_number" class="form-control req" autocomplete="off" value="{{explode('-', $dataAccount->account_number)[1]}}">

                                                </div>
                                                <span class="form-text text-danger err" style="display:none;">*Harap Masukkan Nomor Account terlebih dahulu!</span>
											</div>
										</div>

                                    </fieldset>
                                </div>

                                <div class="col-md-6">
                                    <fieldset>
                                        <div class="separator separator-solid separator-border-2 separator-dark"></div>
                                        <br>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Account</label>
                                            <div class="col-sm-12">
                                                <select class="form-control select2 req front_number" id="id_account" name="id_account">
                                                    <option label="Label" value="">All</option>

                                                </select>
                                                <span class="form-text text-danger err" style="display:none;">*Harap Pilih Account terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Sub Account Name</label>
                                            <div class="col-sm-12">
                                                <input type="text" placeholder="Masukkan Nama Account" name="account_name" class="form-control req" autocomplete="off" value="{{ucwords($dataAccount->account_name)}}">
                                                <span class="form-text text-danger err" style="display:none;">*Harap Masukkan Nama Sub Account terlebih dahulu!</span>
                                            </div>
                                        </div>

                                    </fieldset>
                                </div>

                            </div>
                        </div>

                        <div class="card-footer bg-white d-sm-flex justify-content-sm-between align-items-sm-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-light-danger font-weight-bold mr-2" onclick="window.location.href = '{{ url('/GLSubAccount') }}';">Keluar <i class="flaticon2-cancel icon-sm"></i></button>
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

            $('#id_account').select2({
                placeholder: "Pilih Account",
                allowClear: true
            });

            $("#id_mother_account").val("{{$dataAccount->id_mother_account}}").trigger('change');
            $("#id_account").val("{{$dataAccount->id_account}}").trigger('change');
        });

        $(".front_number").on("change", function() {
            var mAcc = $("#id_mother_account option:selected").text();
            var acc = $("#id_account option:selected").text();
            if (mAcc != "" && acc != "") {
                var mAccArr = mAcc.split(' - ');
                var accArr = acc.split(' - ');
                $("#front_account_number").val(mAccArr[0] + accArr[0].split('-')[1]);
            }
        });

        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        $("#id_mother_account").on("change", function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/GLSubAccount/GetParentAccounts",
                method: 'POST',
                data: {
                    idMotherAccount: $(this).val(),
                },
                success: function(result){
                    $('#id_account').find('option:not(:first)').remove();
                    if (result.length > 0) {
                        for (var i = 0; i < result.length;i++) {
                            var account = result[i].account_number+' - '+ucwords(result[i].account_name);

                            $("#id_account").append($('<option>', {
                                value:result[i].id,
                                text:account
                            }));
                        }
                        $("#id_account").val("{{$dataAccount->id_account}}").trigger('change');
                    }
                }
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
