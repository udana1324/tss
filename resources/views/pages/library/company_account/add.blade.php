@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary text-white header-elements-sm-inline">
						<h5 class="card-title font-weight-semibold">Tambah Rekening</h5>
					</div>
                    <form action="{{ route('CompanyAccount.store') }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label class="control-label col-sm-3">Bank</label>
                                        <div class="col-sm-12">
                                            <select class="form-control select2 req" id="bank" name="bank">
                                                <option label="Label"></option>
                                                @foreach($listBank as $idBank => $nm)
                                                <option value="{{$idBank}}">{{ucwords($nm)}}</option>
                                                @endforeach
                                            </select>
                                            <span class="form-text text-danger err" style="display:none;">*Harap Pilih Bank terlebih dahulu!</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-3">Cabang Bank</label>
                                        <div class="col-sm-12">
                                            <input type="text" placeholder="Masukkan Cabang Bank" id="cabang_bank" name="cabang_bank" class="form-control req">
                                            <span class="form-text text-danger err" style="display:none;">*Harap Masukkan Cabang Bank terlebih dahulu!</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-3">No. Rekening</label>
                                        <div class="col-sm-12">
                                            <input type="text" maxlength="20" placeholder="Masukkan No. Rekening Bank" id="rek_bank" name="rek_bank" class="form-control req" onkeypress="return validasiangka(event);">
                                            <span class="form-text text-danger err" style="display:none;">*Harap Masukkan No. Rekening terlebih dahulu!</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-3">Atas Nama</label>
                                        <div class="col-sm-12">
                                            <input type="text" placeholder="Masukkan Nama Pada Rekening Bank" id="nama_bank" name="nama_bank" class="form-control req">
                                            <span class="form-text text-danger err" style="display:none;">*Harap Masukkan Nama pada Rekening terlebih dahulu!</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-3">Akun Rekening</label>
                                        <div class="col-sm-12">
                                            <select class="form-control select2" id="id_account" name="id_account">
                                                <option label="Label"></option>
                                                @foreach ($dataParent as $parentAcc)
                                                <optgroup label="{{$parentAcc->account_number}} - {{ucwords($parentAcc->account_name)}}">
                                                    @foreach ($parentAcc->child as $acc)
                                                        <option value="{{$acc->id}}">{{$acc->account_number}} - {{ucwords($acc->account_name)}}</option>
                                                    @endforeach
                                                </optgroup>
                                                @endforeach

                                            </select>

                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>

                        <div class="card-footer bg-white d-sm-flex justify-content-sm-between align-items-sm-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-light-danger font-weight-bold mr-2" onclick="window.location.href = '{{ url('/CompanyAccount') }}';">Keluar <i class="flaticon2-cancel icon-sm"></i></button>
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
            $('#bank').select2({
                placeholder: "Pilih Bank",
                allowClear: true
            });

            $('#id_account').select2({
                placeholder: "Pilih Akun Rekening",
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
