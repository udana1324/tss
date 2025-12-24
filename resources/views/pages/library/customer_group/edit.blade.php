@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary text-white header-elements-sm-inline">
						<h5 class="card-title font-weight-semibold">Ubah Group Customer</h5>
					</div>
                    <form action="{{ route('CustomerGroup.update', $dataGroup->id) }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <fieldset>
                                        <legend class="font-weight-semibold"></legend>
                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Kode Group</label>
                                            <div class="col-sm-12">
                                                <input type="text" placeholder="Masukkan Kode Group" id="kode_group" name="kode_group" class="form-control req" value="{{$dataGroup->kode_group}}" >
                                                <span class="form-text text-danger err" style="display:none;">*Harap Masukkan Kode Group terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-sm-3">Nama Group</label>
                                            <div class="col-sm-12">
                                                <input type="text" placeholder="Masukkan Nama Group" id="nama_group" name="nama_group" class="form-control req" value="{{$dataGroup->nama_group}}">
                                                <span class="form-text text-danger err" style="display:none;">*Harap Masukkan Nama Group terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row ml-3">
                                            <label class="control-label col-lg-6">Samakan History Harga Jual?</label>
                                            <div class="col-lg-6">
                                                <div class="checkbox-inline">
                                                    <label class="checkbox checkbox">
                                                        <input type="checkbox" class="checkFlag" value="1" id="Harga" {{$dataGroup->flag_harga == "1" ? "checked" : ""}}>
                                                        <span></span>
                                                    </label>
                                                </div>
                                                <input type="hidden" id="flagHarga" name="flagHarga" class="form-control" value="{{$dataGroup->flag_harga}}" readonly>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>

                                <div class="col-md-6">
                                    <fieldset>
                                        <legend class="font-weight-semibold"></legend>

                                        <div class="form-group">
                                            <label>Nama perusahaan :</label>
                                            <div class="form-group form-group-feedback form-group-feedback-right">
                                                <div class="input-group">
                                                    <select class="form-control select2" id="customer" name="customer">
                                                        <option label="Label"></option>
                                                        @foreach($dataCustomer as $customer)
                                                        <option value="{{$customer->id}}">{{strtoupper($customer->nama_customer)}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih customer terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row text-center">
                                            <label class="col-lg-5 col-form-label"></label>
                                            <button type="button" class="btn btn-primary font-weight-bold mr-2" onclick="addCust();"><i class="flaticon2-plus"></i>
                                                Tambah
                                            </button>
                                        </div>

                                    </fieldset>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <table class="datatable-bordered datatable-head-custom ml-4" id="tbl_cust" width="100%">
                                            <thead>
                                                <tr>
                                                    <th style="text-align: left;">Customer</th>
                                                    <th style="text-align: center;" width="15%">Aksi</th>
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

        $(document).ready(function () {
            $('#customer').select2({
                allowClear: true,
                placeholder: "Pilih Customer"
            });
        });

        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        $("#Harga").change(function() {
            if(this.checked) {
                $("#flagHarga").val("Y");
            }
            else {
                $("#flagHarga").val("N");
            }
        });

        $(document).on("click", "#tbl_cust .hapus", function() {
			$(this).closest("tr").remove();
	    });

        function addCust() {
            var custID = $("#customer option:selected").val();
            var custTxt = $("#customer option:selected").html();
            var count = $("#tbl_cust").find("tr").not("thead tr").length;
            var duplicate = 0;
            $(".cust").each(function() {
                if ($(this).val() == custID) {
                    duplicate = duplicate + 1;
                }
            });
            console.log(duplicate);
            console.log(custID);
            if (duplicate > 0) {
                Swal.fire(
                    "Gagal!",
                    "Customer ini sudah ditambahkan!.",
                    "warning"
                )
                return;
            }
            if (custID != "") {
                var data = "<tr>";
                    data += "<td id='termsMask_"+count+"' class='termsMask'>"+ucwords(custTxt)+"</td>";
                    data += "<td style='display:none;'><input type='text' class='form-control cust' id='terms_"+count+"' name=isi["+count+"][custID] value='"+custID+"' /></td>";
                    data += "<td style='text-align:center;'><button type='button' class='btn btn-sm btn-clean btn-icon hapus'><i class='nav-icon la la-trash'></i></button></td>";
                    data += "</tr>";
                $("#tbl_cust tbody").append(data);
                $("#customer").val("").trigger("change");
            }
            else {
                Swal.fire(
                    "Gagal!",
                    "Harap Pilih Customer dahulu!.",
                    "warning"
                )
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
                    window.location.href = '{{ url("/CustomerGroup") }}';
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
                text: "Apakah perubahan data sudah sesuai?",
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

        $(document).ready(function() {
            var listCust = @json($detailCust);

            for (var xx = 0; xx < listCust.length;xx++) {
                var data = "<tr>";
                    data += "<td id='CustMask_"+xx+"' class='CustMask'>"+ucwords(listCust[xx].nama_customer)+"</td>";
                    data += "<td style='display:none;'><input type='text' class='form-control terms' id='terms_"+xx+"' name=isi["+xx+"][custID] value='"+listCust[xx].id+"' /></td>";
                    data += "<td style='text-align:center;'><button type='button' class='btn btn-sm btn-clean btn-icon hapus'><i class='nav-icon la la-trash'></i></button></td>";
                    data += "</tr>";
                $("#tbl_cust tbody").append(data);
            }
        });


	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
