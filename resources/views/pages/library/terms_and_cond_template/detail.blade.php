@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary text-white header-elements-sm-inline">
						<h5 class="card-title font-weight-semibold">Detail Template Terms And Condition</h5>
					</div>
                    <form action="" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <fieldset>
                                        <legend class="font-weight-semibold"></legend>

                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Nama Template</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control req" name="nama_template" id="nama_template" value="{{ucwords($dataTerms->nama_template)}}" readonly>
                                                <span class="form-text text-danger err" style="display:none;">*Harap Masukkan Nama Template terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Target Template</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control req" name="target_template" id="target_template" value="{{ucwords($dataTerms->target_template)}}" readonly>
                                            </div>
                                        </div>

                                    </fieldset>
                                </div>

                                <div class="col-md-6">
                                    <fieldset>
                                        <legend class="font-weight-semibold"></legend>


                                    </fieldset>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <table class="datatable-bordered datatable-head-custom ml-4" id="tbl_template" width="100%">
                                            <thead>
                                                <tr>
                                                    <th style="text-align: center;">Terms And Condition</th>

                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="card-footer bg-white d-sm-flex justify-content-sm-between align-items-sm-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-light-danger font-weight-bold mr-2" onclick="window.location.href = '{{ url('/TermsAndCondTemplate') }}';">Keluar <i class="flaticon2-cancel icon-sm"></i></button>
                            </div>

                            <div class="mt-2 mt-sm-0">

                            </div>
                        </div>
                    </form>
                </div>
			</div>
			<!-- /content area -->
@endsection
@section('scripts')
    <script type="text/javascript">

        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        $(document).ready(function() {
            var listTerms = @json($detailTerms);

            for (var xx = 0; xx < listTerms.length;xx++) {
                var data = "<tr>";
                    data += "<td id='termsMask_"+xx+"' class='termsMask'>"+ucwords(listTerms[xx].terms_and_condition)+"</td>";
                    data += "<td style='display:none;'><input type='text' class='form-control terms' id='terms_"+xx+"' name=isi["+xx+"][terms] value='"+listTerms[xx].terms_and_condition+"' /></td>";
                    data += "</tr>";
                $("#tbl_template tbody").append(data);
            }
        });


    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
