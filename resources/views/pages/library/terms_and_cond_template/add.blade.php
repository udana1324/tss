@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary text-white header-elements-sm-inline">
						<h5 class="card-title font-weight-semibold">Tambah Template Terms And Condition</h5>
					</div>
                    <form action="{{ route('TermsAndCondTemplate.store') }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-6">
                                    <fieldset>
                                        <legend class="font-weight-semibold"></legend>

                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Nama Template</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control req" placeholder="Masukkan Nama Template" name="nama_template" id="nama_template">
                                                <span class="form-text text-danger err" style="display:none;">*Harap Masukkan Nama Template terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Target Template</label>
                                            <div class="col-lg-9">
                                                <select class="form-control select2 req" id="target_template" name="target_template">
                                                    <option label="Label"></option>
                                                    <option value="pembelian">Pembelian</option>
                                                    <option value="penerimaan">Penerimaan</option>
                                                    <option value="invoice_pembelian">Invoice Pembelian</option>
                                                    <option value="collection_pembelian">Tukar Faktur Pembelian</option>
                                                    <option value="penawaran">Penawaran</option>
                                                    <option value="penjualan">Penjualan</option>
                                                    <option value="pengiriman">Pengiriman</option>
                                                    <option value="invoice_penjualan">Invoice Penjualan</option>
                                                    <option value="collection_penjualan">Tukar Faktur Penjualan</option>
                                                    <option value="produksi">Produksi</option>
                                                    <option value="penerimaan_produksi">Penerimaan Produksi</option>
                                                    <option value="pengiriman_produksi">Pengiriman Produksi</option>
                                                </select>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih Target Template terlebih dahulu!</span>
                                            </div>
                                        </div>

                                    </fieldset>
                                </div>

                                <div class="col-md-6">
                                    <fieldset>
                                        <legend class="font-weight-semibold"></legend>

                                        <div class="form-group row">
                                            <label class="col-lg-3 col-form-label">Terms And Condition</label>
                                            <div class="col-lg-9">
                                                <input type="text" class="form-control" placeholder="Masukkan Terms And Condition" name="terms_and_cond" id="terms_and_cond">
                                                <span class="form-text text-danger err" style="display:none;">*Harap isi terms and condition terlebih dahulu!</span>
                                            </div>
                                        </div>

                                        <div class="form-group row text-center">
                                            <label class="col-lg-5 col-form-label"></label>
                                            <button type="button" class="btn btn-primary font-weight-bold mr-2" onclick="addTerms();"><i class="flaticon2-plus"></i>
                                                Tambah
                                            </button>
                                        </div>

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
                                <button type="button" class="btn btn-light-danger font-weight-bold mr-2" onclick="window.location.href = '{{ url('/TermsAndCondTemplate') }}';">Keluar <i class="flaticon2-cancel icon-sm"></i></button>
                            </div>

                            <div class="mt-2 mt-sm-0">
                                <button type="button" style="display: none;" id="btnModalEdit" data-toggle="modal" data-target="#modal_edit"></button>
                                <button type="submit" class="btn btn-light-primary font-weight-bold mr-2"> Simpan <i class="flaticon-paper-plane-1"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                    <!-- Modal form edit -->
				<div id="modal_edit" class="modal fade">
				    <div class="modal-dialog modal-lg">
					    <div class="modal-content">
						    <div class="modal-header bg-primary">

							    <h5 class="modal-title">Ubah Terms And Condition</h5>
						    </div>
						    <div class="modal-body">
							    <form >
								    <table class="datatable-bordered datatable-head-custom ml-4" id="tbl_edit" width="100%">
									    <thead>
										    <tr>
											    <th align="center" style="text-align:center;display: none;">Id</th>
											    <th align="center" style="text-align:center;">Terms And Condition</th>
										    </tr>
									    </thead>
									    <tbody>

									    </tbody>
								    </table>
							    </form>

						    </div>

						    <div class="modal-footer">
							    <button type="button" class="btn btn-primary" id="btnSubmitEdit" data-dismiss="modal">Simpan</button>
							    <button type="button" class="btn btn-link" data-dismiss="modal">Tutup</button>
						    </div>
					    </div>
				    </div>
			    </div>
				<!-- /form edit -->
			</div>
			<!-- /content area -->
@endsection
@section('scripts')
    <script type="text/javascript">

        $(document).ready(function () {
            $('#target_template').select2({
                allowClear: true,
                placeholder: "Pilih Target Template"
            });
        });

        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        function addTerms() {
            var terms = $("#terms_and_cond").val();
            var count = $("#tbl_template").find("tr").not("thead tr").length;
            if (terms != "") {
                var data = "<tr>";
                    data += "<td id='termsMask_"+count+"' class='termsMask'>"+ucwords(terms)+"</td>";
                    data += "<td style='display:none;'><input type='text' class='form-control terms' id='terms_"+count+"' name=isi["+count+"][terms] value='"+terms+"' /></td>";
                    data += "<td style='text-align:center;'><button type='button' class='btn btn-sm btn-clean btn-icon edit'><i class='nav-icon la la-edit'></i></button>";
                    data += "<button type='button' class='btn btn-sm btn-clean btn-icon hapus'><i class='nav-icon la la-trash'></i></button></td>";
                    data += "</tr>";
                $("#tbl_template tbody").append(data);
                $("#terms_and_cond").val("");
            }
            else {
                Swal.fire(
                    "Gagal!",
                    "Harap Tambahkan isi Terms And Condition Terlebih dahulu!.",
                    "warning"
                )
            }
        }

        $(document).on("click", "#tbl_template .edit", function() {
			$("#tbl_edit tbody").empty();
	     	var idRow = $(this).closest("tr").index();
	     	var terms = $(this).closest("tr").find(".terms").val();

	     	var data = "<tr>";
	     		data += "<td style='display:none;'><input type='text' class='form-control' id='idRowEdit' value='"+idRow+"' /></td>";
	     		data += "<td style='text-align:center;'><input type='text' class='form-control' id='termsRowEdit' value='"+terms+"' /></td>";
	     		data += "</tr>";
	     		$('#tbl_edit tbody').append(data);
	     	$("#btnModalEdit").trigger('click');
	    });

	    $(document).on("click", "#btnSubmitEdit", function() {
	     	var idRow = $("#idRowEdit").val();
	     	var terms = $("#termsRowEdit").val();
            var termsMask = ucwords(terms);
	       	$("#tbl_template tbody tr:eq("+idRow+")").find(".terms").val(terms);
	     	$("#tbl_template tbody tr:eq("+idRow+")").find(".termsMask").html(termsMask);
	    });

        $(document).on("click", "#tbl_template .hapus", function() {
			$(this).closest("tr").remove();
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

                    if($("#tbl_template").find("tr").not("thead tr").length < 1) {
                        Swal.fire(
                            "Gagal!",
                            "Harap Tambahkan Minimum 1 Terms And Condition!.",
                            "warning"
                        )
                        count = parseInt(count) + 1;
                    }

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
