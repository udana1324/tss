@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom card-sticky">
					<div class="card-header bg-primary header-elements-sm-inline">
						<h5 class="card-title text-white">Ubah Menu</h5>
					</div>
                    <form action="{{ route('Modules.update', $dataMenu->id) }}" class="form-horizontal" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <fieldset>
										<legend class="font-weight-semibold text-muted"> Detail Menu</legend>
                                        <div class="separator separator-solid separator-border-2 separator-muted"></div>
                                        <br>
                                        <div class="form-group row">
                                            <label class="col-2 col-form-label">Nama Menu</label>
                                            <div class="col-10">
                                            <input type="text" placeholder="Masukkan Nama Menu" name="menu" value="{{$dataMenu->menu}}" class="form-control" autocomplete="off" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-2 col-form-label">Url</label>
                                            <div class="col-10">
                                                <input type="text" placeholder="Masukkan Url Menu" name="url" value="{{$dataMenu->url}}" class="form-control" autocomplete="off" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-2 col-form-label">Parent Modul</label>
                                            <div class="col-10">
                                                <select class="form-control select2" id="modul" name="modul">
                                                    <option label="Label"></option>
                                                    @foreach ($Module as $dataModule)
                                                    <option value="{{$dataModule->id}}">{{ucwords($dataModule->menu)}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-2 col-form-label">Urutan</label>
                                            <div class="col-10">
                                                <input type="number" placeholder="Masukkan Nomor Urut Menu" name="order_number" value="{{$dataMenu->order_number}}" autocomplete="off" class="form-control" required>
                                            </div>
                                        </div>

                                        @if ($dataMenu->active == 'Y')
                                        <div class="form-group row">
                                            <label class="col-2 col-form-label">Status Menu</label>
                                            <div class="col-10">
                                                <div class="checkbox-inline">
                                                    <label class="checkbox checkbox-lg">
                                                        <input type="checkbox" value="Y" name="active" checked>
                                                        <span></span>Aktif
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        @else
                                        <div class="form-group row">
                                            <label class="col-2 col-form-label">Status Menu</label>
                                            <div class="col-10">
                                                <div class="checkbox-inline">
                                                    <label class="checkbox checkbox-lg">
                                                        <input type="checkbox" value="Y" name="active">
                                                        <span></span>Aktif
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

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
            $('#modul').select2({
                placeholder: "Pilih Parent Menu",
                allowClear: true
            });
        });

        $("#cancel").on('click', function(e) {
            Swal.fire({
                title: "Batal?",
                text: "Apakah anda ingin membatalkan perubahan data Menu?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if (result.value) {
                    window.location.href = '{{ url("/Modules") }}';
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
            $("#modul").val("{{$dataMenu->parent}}").trigger("change");
        });

    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
