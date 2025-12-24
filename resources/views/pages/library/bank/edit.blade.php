@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary text-white header-elements-sm-inline">
						<h5 class="card-title font-weight-semibold">Ubah Bank</h5>
					</div>
                    <form action="{{ route('Bank.update', $dataBank->id) }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            @method('PUT')
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label class="control-label col-sm-3">Kode Bank</label>
                                        <div class="col-sm-12">
                                        <input type="text" maxlength="3" placeholder="Masukkan Kode Bank" id="kode_bank" name="kode_bank" class="form-control req" onkeypress='return validasiangka(event);' value="{{$dataBank->kode_bank}}">
                                            <span class="form-text text-danger err" style="display:none;">*Harap Masukkan Kode Bank terlebih dahulu!</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-3">Nama Bank</label>
                                        <div class="col-sm-12">
                                            <input type="text" placeholder="Masukkan Nama Bank" id="nama_bank" name="nama_bank" class="form-control req" value="{{$dataBank->nama_bank}}">
                                            <span class="form-text text-danger err" style="display:none;">*Harap Masukkan Nama Bank terlebih dahulu!</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-3">Deskripsi Bank</label>
                                        <div class="col-sm-12">
                                            <input type="text" placeholder="Masukkan Deskripsi Bank" id="deskripsi_bank" name="deskripsi_bank" class="form-control req" value="{{$dataBank->deskripsi_bank}}">
                                            <span class="form-text text-danger err" style="display:none;">*Harap Masukkan Deskripsi Bank terlebih dahulu!</span>
                                        </div>
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
			<!-- /content area -->
@endsection
@section('scripts')
    <script type="text/javascript">

        function validasiangka(evt) {
          var charCode = (evt.which) ? evt.which : event.keyCode
           	if (charCode > 31 && (charCode < 48 || charCode > 57)) {

	            return false;
	          return true;
	      	}
	      	else if (evt.which == 46 || evt.keyCode == 46) {
				e.preventDefault();
			}
			else if (evt.which == 45 || evt.keyCode == 45) {
				e.preventDefault();
			}
			else if (evt.which == 44 || evt.keyCode == 44) {
				e.preventDefault();
			}
			else if (evt.which == 43 || evt.keyCode == 43) {
				e.preventDefault();
			}
        }

        $("#cancel").on('click', function(e) {
            Swal.fire({
                title: "Batal?",
                text: "Apakah anda ingin membatalkan perubahan data?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if (result.value) {
                    window.location.href = '{{ url("/Bank") }}';
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
