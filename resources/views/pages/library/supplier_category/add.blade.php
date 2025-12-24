@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary text-white header-elements-sm-inline">
						<h5 class="card-title font-weight-semibold">Tambah Kategori Supplier</h5>
					</div>
                    <form action="{{ route('SupplierCategory.store') }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label class="control-label col-sm-3">Kode Kategori</label>
                                        <div class="col-sm-12">
                                            <input type="text" placeholder="Masukkan Kode Kategori" id="kode_kategori" name="kode_kategori" class="form-control req" >
                                            <span class="form-text text-danger err" style="display:none;">*Harap Masukkan Kode Kategori terlebih dahulu!</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-3">Nama Kategori</label>
                                        <div class="col-sm-12">
                                            <input type="text" placeholder="Masukkan Nama Kategori" id="nama_kategori" name="nama_kategori" class="form-control req">
                                            <span class="form-text text-danger err" style="display:none;">*Harap Masukkan Nama Kategori terlebih dahulu!</span>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>

                        <div class="card-footer bg-white d-sm-flex justify-content-sm-between align-items-sm-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-light-danger font-weight-bold mr-2" onclick="window.location.href = '{{ url('/SupplierCategory') }}';">Keluar <i class="flaticon2-cancel icon-sm"></i></button>
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
