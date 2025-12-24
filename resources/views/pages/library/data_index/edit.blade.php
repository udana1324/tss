@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h5 class="card-title text-white">Ubah Index</h5>
					</div>
                    <form action="{{ route('DataIndex.update', $index->id) }}" class="form-horizontal" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <fieldset>
										<legend class="font-weight-semibold text-muted"> Detail Menu</legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Kode Index</label>
                                            <div class="col-sm-12">
                                                <input type="text" placeholder="Masukkan Kode Index" name="kode_index" class="form-control req" autocomplete="off" value="{{$index->kode_index}}">
                                                <span class="form-text text-danger err" style="display:none;">*Harap Masukkan Kode Index terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Nama Index</label>
                                            <div class="col-sm-12">
                                                <input type="text" placeholder="Masukkan nama index" name="nama_index" class="form-control req" autocomplete="off" value="{{$index->nama_index}}">
                                                <span class="form-text text-danger err" style="display:none;">*Harap Masukkan nama index terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Parent</label>
                                            <div class="col-sm-12">
                                                <select class="form-control select2" id="parentIndex" name="parentIndex">
                                                    <option label="Label"></option>
                                                    @foreach ($dataIndex as $rowIndex)
                                                    <option value="{{$rowIndex->id}}">{{ucwords($rowIndex->kode_index)}} - {{ucwords($rowIndex->nama_index)}}</option>
                                                    @endforeach
                                                </select>
                                                {{-- <span class="form-text text-muted"> Contoh : 99.999.999.9-999.999</span> --}}
                                            </div>
                                        </div>

                                    </fieldset>
                                </div>

                            </div>
                        </div>

                        <div class="card-footer bg-white d-sm-flex justify-content-sm-between align-items-sm-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-light-danger font-weight-bold mr-2" id="cancel">Keluar <i class="flaticon2-cancel icon-sm"></i></button>
                            </div>

                            <div class="mt-2 mt-sm-0">
                                <button type="submit" class="btn btn-light-primary font-weight-bold mr-2"> Simpan <i class="flaticon-paper-plane-1"></i> </button>
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
            $('#parentIndex').select2({
                placeholder: "Pilih Parent Index",
                allowClear: true
            });
        });

        $("#cancel").on('click', function(e) {
            Swal.fire({
                title: "Batal?",
                text: "Apakah anda ingin membatalkan perubahan data Index?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if (result.value) {
                    window.location.href = '{{ url("/DataIndex") }}';
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

        $(document).ready(function () {
            $("#parentIndex").val("{{$index->parent}}").trigger("change");
        });

    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
