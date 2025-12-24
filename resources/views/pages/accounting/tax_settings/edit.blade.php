@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary text-white header-elements-sm-inline">
						<h5 class="card-title font-weight-semibold text-white">Pengaturan Faktur Pajak</h5>
					</div>
                    <form action="{{ route('TaxSettings.update', $taxSettings->id) }}" class="form-horizontal" id="form_add" method="POST">
					    <div class="card-body">
                            @method('PUT')
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="form-group">
                                        <label class="control-label col-sm-3">Preferensi PKP</label>
                                        <div class="col-sm-12">
                                            <select class="form-control select2 req" id="preferensi" name="preferensi">
                                                <option label="Label"></option>
                                                @foreach($preferensi as $dataPref)
                                                <option value="{{$dataPref->id}}">{{strtoupper($dataPref->nama_pt. ' - '.$dataPref->npwp_pt)}}</option>
                                                @endforeach
                                            </select>
                                            <span class="form-text text-danger err" style="display:none;">*Harap pilih preferensi pkp terlebih dahulu!</span>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-3">Alamat PKP</label>
                                        <div class="col-sm-12">
                                            <textarea class="form-control" name="alamat" id="alamat" style="resize:none;" placeholder="Silahkan Klik Tombol Pilih Alamat" readonly></textarea>

                                        </div>
                                    </div>

                                    <div class="form-group form-group-feedback form-group-feedback-right">
                                        <input type="text" id="mode_ppn" value="display" class="form-control"  style="display: none;" readonly>
                                        <div class="input-group" id="display">
                                            <div class="col-sm-3">
                                                <label class="control-label">Persentase PPn</label>
                                                <select class="form-control select2 req" id="ppn" name="ppn">
                                                    <option label="Label"></option>
                                                    @foreach($ppnList as $dataPPn)
                                                    <option value="{{$dataPPn->id}}">{{strtoupper($dataPPn->ppn_name)}}</option>
                                                    @endforeach
                                                </select>
                                                <span class="form-text text-danger err" style="display:none;">*Harap pilih persentase ppn terlebih dahulu!</span>
                                            </div>
                                            <div class="col-sm-4">
                                                <label class="control-label">Berlaku dari: </label>
                                                <input type="text" id="tax_start_date" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-4">
                                                <label class="control-label">Berlaku Sampai: </label>
                                                <input type="text" id="tax_end_date" class="form-control" readonly>
                                            </div>
                                            <div class="col-sm-1">
                                                <br />
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-primary mr-2" id="btnAddPPN"><i class="flaticon2-plus"></i></button>
                                                    <button type="button" class="btn btn-primary mr-2" id="btnEditPPN"><i class="flaticon2-pen"></i></button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="input-group" id="input" style="display: none;">
                                            <div class="col-sm-2">
                                                <label class="control-label">Persentase PPn</label>
                                                <input type="text" id="ppn_input" class="form-control reqPPN">
                                                <span class="form-text text-danger errPPN" style="display:none;">*Harap input persentase ppn terlebih dahulu!</span>
                                            </div>
                                            <div class="col-sm-4">
                                                <label class="control-label">Berlaku Mulai: </label>
                                                <div class="form-group divTgl">
                                                    <input type="hidden" class="form-control tglValue reqPPN" name="start_date" id="start_date" >
                                                    <input type="text" class="form-control pickerTgl" placeholder="Pilih Tanggal" name="start_date_picker" id="start_date_picker" readonly>
                                                    <span class="form-text text-danger errPPN" style="display:none;">*Harap input tanggal berlaku persentase ppn terlebih dahulu!</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <label class="control-label">Berlaku Sampai: </label>
                                                <div class="form-group divTgl">
                                                    <input type="hidden" class="form-control tglValue" name="end_date" id="end_date">
                                                    <input type="text" class="form-control pickerTgl" placeholder="Pilih Tanggal" name="end_date_picker" id="end_date_picker" readonly>
                                                </div>
                                            </div>
                                            <div class="col-sm-1">
                                                <br />
                                                <div class="input-group-append" data-toggle="tooltip"  title="Simpan" data-placement="top">
                                                    <button type="button" class="btn btn-primary" id="btnSimpanPPN"><i class="flaticon2-paperplane"></i></button>
                                                </div>
                                            </div>
                                            <div class="col-sm-1">
                                                <br />

                                                <div class="input-group-append" data-toggle="tooltip"  title="Batal" data-placement="top">
                                                    <button type="button" class="btn btn-danger" id="btnCancelPPN"><i class="flaticon2-cancel"></i></button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="input-group" id="inputEdit" style="display: none;">
                                            <div class="col-sm-2">
                                                <label class="control-label">Persentase PPn</label>
                                                <input type="hidden" id="ppn_input_edit_id" class="form-control reqPPNEdit">
                                                <input type="text" id="ppn_input_edit" class="form-control reqPPNEdit">
                                                <span class="form-text text-danger errPPNEdit" style="display:none;">*Harap input persentase ppn terlebih dahulu!</span>
                                            </div>
                                            <div class="col-sm-4">
                                                <label class="control-label">Berlaku Mulai: </label>
                                                <div class="form-group divTglEdit">
                                                    <input type="hidden" class="form-control tglValueEdit reqPPNEdit" name="start_date_edit" id="start_date_edit" >
                                                    <input type="text" class="form-control pickerTglEdit" placeholder="Pilih Tanggal" name="start_date_edit_picker" id="start_date_edit_picker" readonly>
                                                    <span class="form-text text-danger errPPNEdit" style="display:none;">*Harap input tanggal berlaku persentase ppn terlebih dahulu!</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <label class="control-label">Berlaku Sampai: </label>
                                                <div class="form-group divTglEdit">
                                                    <input type="hidden" class="form-control tglValueEdit" name="end_date_edit" id="end_date_edit">
                                                    <input type="text" class="form-control pickerTglEdit" placeholder="Pilih Tanggal" name="end_date_edit_picker" id="end_date_edit_picker" readonly>
                                                </div>
                                            </div>
                                            <div class="col-sm-1">
                                                <br />
                                                <div class="input-group-append" data-toggle="tooltip"  title="Simpan" data-placement="top">
                                                    <button type="button" class="btn btn-primary" id="btnUpdatePPN"><i class="flaticon2-paperplane"></i></button>
                                                </div>
                                            </div>
                                            <div class="col-sm-1">
                                                <br />

                                                <div class="input-group-append" data-toggle="tooltip"  title="Batal" data-placement="top">
                                                    <button type="button" class="btn btn-danger" id="btnCancelEditPPN"><i class="flaticon2-cancel"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-3">Status penggunaan Faktur Pajak?</label>
                                        <div class="col-sm-12">
                                            <div class="checkbox-inline">
                                                <label class="checkbox checkbox-lg">
                                                    <input type="checkbox" value="Y" name="enable_tax" {{ $taxSettings->enable_tax === "Y" ? "checked" : "" }}>
                                                    <span></span>Aktif
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-sm-3">Status Auto Generate Faktur Pajak pada saat Posting?</label>
                                        <div class="col-sm-12">
                                            <div class="checkbox-inline">
                                                <label class="checkbox checkbox-lg">
                                                    <input type="checkbox" value="Y" name="auto_generate" {{ $taxSettings->auto_generate_tax_invoice === "Y" ? "checked" : "" }}>
                                                    <span></span>Aktif
                                                </label>
                                            </div>
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

        $(document).ready(function() {
            $('#preferensi').select2({
                allowClear: true,
                placeholder: "Silahkan Pilih Preferensi PKP"
            });

            $('#ppn').select2({
                allowClear: true,
                placeholder: "Silahkan Pilih Persentase PPn"
            });

            $('#start_date_picker, #end_date_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                autoclose : true,
                "setDate": new Date(),
                format : "dd MM yyyy",
            });

            $('#start_date_edit_picker, #end_date_edit_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                autoclose : true,
                format : "dd MM yyyy",
            });

            $("#preferensi").val("{{$taxSettings->id_preferensi}}").trigger('change');
            $("#ppn").val("{{$taxSettings->ppn_percentage_id}}").trigger('change');
        });

        $(".pickerTgl").on('change', function() {
            var endDate = new Date($("#end_date_picker").datepicker('getDate'));
            var startDate = new Date($("#start_date_picker").datepicker('getDate'));
            var selisih = Math.floor((Date.UTC(endDate.getFullYear(), endDate.getMonth(), endDate.getDate()) - Date.UTC(startDate.getFullYear(), startDate.getMonth(), startDate.getDate()) ) /(1000 * 60 * 60 * 24));

            $(this).closest(".divTgl").find(".tglValue").val($(this).data('datepicker').getFormattedDate('yyyy-mm-dd'));

            if ($("#end_date").val() != "") {
                if (selisih < 0) {
                    Swal.fire(
                        "Error!",
                        "Tanggal Berlaku tidak boleh dibawah dari tanggal berakhir!.",
                        "warning"
                    )
                    $("#end_date").val("");
                    $("#end_date_picker").val("");
                    $("#end_date_picker").attr("class","form-control pickerTgl is-invalid");
                }
                else {
                    $("#start_date_picker").attr("class","form-control pickerTgl is-invalid");
                }
            }
        });

        $(".pickerTglEdit").on('change', function() {
            var endDate = new Date($("#end_date_edit_picker").datepicker('getDate'));
            var startDate = new Date($("#start_date_edit_picker").datepicker('getDate'));
            var selisih = Math.floor((Date.UTC(endDate.getFullYear(), endDate.getMonth(), endDate.getDate()) - Date.UTC(startDate.getFullYear(), startDate.getMonth(), startDate.getDate()) ) /(1000 * 60 * 60 * 24));

            $(this).closest(".divTglEdit").find(".tglValueEdit").val($(this).data('datepicker').getFormattedDate('yyyy-mm-dd'));

            if ($("#end_date_edit").val() != "" && $("#end_date_edit").val() != "") {
                if (selisih < 0) {
                    Swal.fire(
                        "Error!",
                        "Tanggal Berlaku tidak boleh dibawah dari tanggal berakhir!.",
                        "warning"
                    )
                    $("#end_date_edit").val("");
                    $("#end_date_edit_picker").val("");
                    $("#end_date_edit_picker").attr("class","form-control pickerTglEdit is-invalid");
                }
                else {
                    $("#start_date_picker").attr("class","form-control pickerTgl is-invalid");
                }
            }
        });

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
                    window.location.href = '{{ url("/") }}';
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

        $("#preferensi").on('change', function(e) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/TaxSettings/GetPrefAddress",
                method: 'POST',
                data: {
                    id_pref: $(this).val(),
                },
                success: function(result){
                    if (result != null) {
                        $("#alamat").val(result.txtAlamat);
                    }
                }
            });
	    });

        $("#ppn").on('change', function(e) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/TaxSettings/GetPPN",
                method: 'POST',
                data: {
                    id_ppn: $(this).val(),
                    mode: $("#mode_ppn").val()
                },
                success: function(result){
                    if (result != null) {
                        const options = {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                        };
                        var startDate = new Date(result.ppn_start_date);
                        if (result.ppn_end_date != null) {
                            var endDate = new Date(result.ppn_end_date);
                            $("#tax_end_date").val(endDate.toLocaleDateString('id-ID', options));
                        }
                        else {
                            $("#tax_end_date").val('-');
                        }
                        $("#tax_start_date").val(startDate.toLocaleDateString('id-ID', options));

                        $("#start_date_edit_picker").datepicker('setDate', null);
                        $("#end_date_edit_picker").datepicker('setDate', null);
                        $("#ppn_input_edit_id").val(result.id);
                        $("#ppn_input_edit").val(result.ppn_percentage);
                        if (result.ppn_end_date != null) {
                            $("#end_date_edit").val(result.ppn_end_date);
                            $("#end_date_edit_picker").datepicker('setDate', new Date(result.ppn_end_date));
                        }
                        else {
                            $("#end_date_edit").val("");
                            $("#end_date_edit_picker").datepicker('setDate', null);
                        }
                        $("#start_date_edit_picker").datepicker('setDate', new Date(result.ppn_start_date));
                        $("#start_date_edit").val(result.ppn_start_date);



                    }
                }
            });
	    });

        function getPPNList() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/TaxSettings/GetPPN",
                method: 'POST',
                data: {
                    mode: $("#mode_ppn").val()
                },
                success: function(result){
                    $('#ppn').find('option:not(:first)').remove();
                    if (result.length > 0) {
                        for (var i = 0; i < result.length;i++) {
                            $("#ppn").append($('<option>', {
                                value:result[i].id,
                                text:result[i].ppn_name.toUpperCase()
                            }));
                        }
                    }

                    $("#mode_ppn").val("display");
                    $("#ppn").val("{{$taxSettings->ppn_percentage_id}}").trigger('change');
                }
            });
        }

        $("#btnAddPPN").on('click', function(e) {
            $("#display").hide();
            $("#input").show();
            $("#mode_ppn").val("add");
	    });

        $("#btnEditPPN").on('click', function(e) {
            $("#display").hide();
            $("#inputEdit").show();
            $("#mode_ppn").val("edit");
	    });

        $("#btnCancelPPN").on('click', function(e) {
            $("#input").hide();
            $("#display").show();
            $("#mode_ppn").val("display");
	    });

        $("#btnCancelEditPPN").on('click', function(e) {
            $("#inputEdit").hide();
            $("#display").show();
            $("#mode_ppn").val("display");
	    });

        $("#btnSimpanPPN").on('click', function(e) {
            var ppnPercentage = $("#ppn_input").val();
            var ppnStartDate = $("#start_date").val();
            var ppnEndDate = $("#end_date").val();
            Swal.fire({
                title: "Tambahkan Data?",
                text: "Apakah anda ingin menambahkan persentase PPn " + ppnPercentage +" ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    var count = 0;
                    $(".reqPPN").each(function(){
                        if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                            $(this).closest('.form-group').find('.errPPN').show();
                            count = parseInt(count) + 1;
                        }
                        else {
                            $(this).closest('.form-group').find('.errPPN').hide();
                        }
                    });

                    if (count == 0) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            url: "/TaxSettings/AddPPN",
                            method: 'POST',
                            data: {
                                mode: "add",
                                percentage: ppnPercentage,
                                start_date: ppnStartDate,
                                end_date: ppnEndDate
                            },
                            success: function(result){
                                Swal.fire(
                                    "Berhasil!",
                                    "Persentase PPn Berhasil ditambahkan!",
                                    "success"
                                )
                            }
                        });

                        getPPNList();
                        $("#input").hide();
                        $("#display").show();
                        $("#mode_ppn").val("display");
                        $("#ppn_input").val("");
                        $("#start_date").val("");
                        $("#end_date").val("");
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

        $("#btnUpdatePPN").on('click', function(e) {
            var ppnID = $("#ppn_input_edit_id").val();
            var ppnPercentage = $("#ppn_input_edit").val();
            var ppnStartDate = $("#start_date_edit").val();
            var ppnEndDate = $("#end_date_edit").val();
            Swal.fire({
                title: "Update Data?",
                text: "Apakah anda yakin melakukan update ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    var count = 0;
                    $(".reqPPNEdit").each(function(){
                        if($(this).val() == "" || $(this).children("option:selected").val() == ""){
                            $(this).closest('.form-group').find('.errPPNEdit').show();
                            count = parseInt(count) + 1;
                        }
                        else {
                            $(this).closest('.form-group').find('.errPPNEdit').hide();
                        }
                    });

                    if (count == 0) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });
                        $.ajax({
                            url: "/TaxSettings/AddPPN",
                            method: 'POST',
                            data: {
                                mode: "edit",
                                ppn_id: ppnID,
                                percentage: ppnPercentage,
                                start_date: ppnStartDate,
                                end_date: ppnEndDate
                            },
                            success: function(result){
                                Swal.fire(
                                    "Berhasil!",
                                    "Persentase PPn Berhasil diupdate!",
                                    "success"
                                )
                            }
                        });

                        getPPNList();
                        $("#inputEdit").hide();
                        $("#display").show();
                        $("#mode_ppn").val("display");
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
