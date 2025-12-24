@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary text-white header-elements-sm-inline">
						<h5 class="card-title font-weight-semibold">Ubah Sales</h5>
					</div>
                    <form action="{{ route('Sales.update', $dataSales->id) }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label class="control-label col-sm-3">Nama Sales</label>
                                        <div class="col-sm-12">
                                        <input type="text" placeholder="Masukkan Nama Sales" id="nama_sales" name="nama_sales" class="form-control req" value="{{$dataSales->nama_sales}}" >
                                            <span class="form-text text-danger err" style="display:none;">*Harap Masukkan Nama Sales terlebih dahulu!</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-3">Telp Sales</label>
                                        <div class="col-sm-12">
                                            <input type="number" placeholder="Masukkan Telp Sales" id="telp_sales" name="telp_sales" class="form-control req" value="{{$dataSales->telp_sales}}">
                                            <span class="form-text text-danger err" style="display:none;">*Harap Masukkan Telp Sales terlebih dahulu!</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-3">Email Sales</label>
                                        <div class="col-sm-12">
                                            <input type="email" placeholder="Masukkan Email Sales" id="email_sales" name="email_sales" class="form-control req" value="{{$dataSales->email_sales}}">
                                            <span class="form-text text-danger err" style="display:none;">*Harap Masukkan Email Sales terlebih dahulu!</span>
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
                    window.location.href = '{{ url("/Sales") }}';
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
