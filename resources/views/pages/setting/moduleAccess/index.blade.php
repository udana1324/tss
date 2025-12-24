@extends('layout.default')
@section('content')
    @include('pages.alerts')
    <div class="content">
        <div class="card card-custom">
            <div class="card-header bg-primary header-elements-sm-inline">
                <div class="card-title">
                    <h3 class="card-label text-white">Data Akses User</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="card-toolbar">
                    <div class="col-lg-3">
                        <div class="form-group row">
                            <select class="form-control select2" id="usergroup" name="usergroup">
                                <option label="Label"></option>
                                <option value="admin">Admin</option>
                                <option value="penjualan">Penjualan</option>
                                <option value="pembelian">Pembelian</option>
                                <option value="gudang">Gudang</option>
                                <option value="operasional">Operasional</option>
                            </select>
                        </div>

                        <div class="form-group row">
                            <select class="form-control select2" id="user" name="user">
                                <option label="Label"></option>
                            </select>
                        </div>
                    </div>
                </div>
                <!--begin: Search Form-->
                <!--begin::Search Form-->
                <div class="mb-7">
                    <div class="row align-items-center">
                        <div class="col-lg-9 col-xl-8">
                            <div class="row align-items-center">
                                <div class="col-md-4 my-2 my-md-0">
                                    <div class="input-icon">
                                        <input type="text" class="form-control" placeholder="Search..." id="table_menu_search_query"/>
                                        <span>
                                            <i class="flaticon2-search-1 text-muted"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Search Form-->
                <!--end: Search Form-->
                <!--begin: Datatable-->

                <div class="datatable datatable-bordered datatable-head-custom" id="table_menu"></div>

                <!--end: Datatable-->
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        $(document).ready(function () {
            $('#usergroup').select2({
                placeholder: "Pilih Grup User",
                allowClear: true
            });

            $('#user').select2({
                placeholder: "Pilih Username",
                allowClear: true
            });
        });

		$("#usergroup").on("change", function() {
			$.ajaxSetup({
		    	headers: {
		        	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		        }
		    });
		    $.ajax({
		    	url: "/ModuleAccess/GetUsers",
		        method: 'POST',
		        dataType : 'json',
		        data: {
			    	user_group: $("#usergroup").val()
		        },
		        success: function(result){
		        	$('#user').find('option:not(:first)').remove();
		            if (result.length > 0) {
			        	for (var i = 0; i < result.length;i++) {
			            	$("#user").append($('<option>', {
				                value:result[i].id,
				                text:result[i].user_name
			                }));
			            }
		            }
		            $("#user").trigger("change");
		        }
		    });
		});

		$(document).ready(function() {
            var datatable = $('#table_menu').KTDatatable({
                    data: {
                        type: 'remote',
                        source: {
                            read: {
                                url: 'ModuleAccess/GetMenu',
                                method: 'POST',
                                headers : {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                },
                            }
                        },
                        pageSize: 1000,
                        serverPaging: true,
                        serverFiltering: false,
                        serverSorting: false,
                        saveState: false
                    },

                    layout: {
                        scroll: true,
                        height: 'auto',
                        footer: false
                    },

                    sortable: true,

                    filterable: true,

                    pagination: true,

                    rows: {
                        autoHide: false
                    },

                    search: {
                        input: $('#table_menu_search_query')
                    },

                    columns: [
                        {
                            field: 'id',
                            title: '#',
                            sortable: false,
                            width: 20,
                            type: 'number',
                            selector: false,
                            textAlign: 'center',
                            visible:false,
                        },
                        {
                            field: 'menu',
                            title: 'Menu',
                            width: 'auto',
                            template: function(row) {
                                return ucwords(row.menu);
                            },
                        },
                        {
                            field: 'all',
                            title: 'Full Akses',
                            width: 85,
                            autoHide:false,
                            template: function(row) {
                                if (row.add == "Y" && row.edit == "Y" && row.delete == "Y" && row.posting == "Y" && row.print == "Y" && row.export == "Y" && row.approve == "Y") {
                                    return "<div class='checkbox-inline align-items-center'><label class='checkbox checkbox-lg'><input type='checkbox' id='checkAll' value='"+row.idMenu+"' class='text-center checkAll' checked><span></span></label></div>";
                                }
                                else {
                                    return "<div class='checkbox-inline align-items-center'><label class='checkbox checkbox-lg'><input type='checkbox' id='checkAll' value='"+row.idMenu+"' class='text-center checkAll'><span></span></label></div>";
                                }

                            },
                        },
                        {
                            field: 'add',
                            title: 'Tambah',
                            textAlign: 'center',
                            width: 85,
                            autoHide:false,
                            template: function(row) {
                                var txtCheckbox = "<div class='checkbox-inline align-items-center'>";
                                    txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                    if (row.add == "Y")
                                        txtCheckbox += "<input type='checkbox' class='text-center bisaTambah' value='"+row.idMenu+"' name='bisaTambah' checked>";
                                    else {
                                        txtCheckbox += "<input type='checkbox' class='text-center bisaTambah' value='"+row.idMenu+"' name='bisaTambah'>";
                                    }
                                    txtCheckbox += "<span></span>";
                                    txtCheckbox += "</label>";
                                    txtCheckbox += "</div>";
                                return txtCheckbox;
                            },
                        },
                        {
                            field: 'edit',
                            title: 'Edit',
                            textAlign: 'center',
                            width: 85,
                            autoHide:false,
                            template: function(row) {
                                var txtCheckbox = "<div class='checkbox-inline align-items-center'>";
                                    txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                    if (row.edit == "Y")
                                        txtCheckbox += "<input type='checkbox' class='text-center bisaEdit' value='"+row.idMenu+"' name='bisaEdit' checked>";
                                    else {
                                        txtCheckbox += "<input type='checkbox' class='text-center bisaEdit' value='"+row.idMenu+"' name='bisaEdit'>";
                                    }
                                    txtCheckbox += "<span></span>";
                                    txtCheckbox += "</label>";
                                    txtCheckbox += "</div>";
                                return txtCheckbox;
                            },
                        },
                        {
                            field: 'delete',
                            title: 'Hapus',
                            textAlign: 'center',
                            width: 85,
                            autoHide:false,
                            template: function(row) {
                                var txtCheckbox = "<div class='checkbox-inline align-items-center'>";
                                    txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                    if (row.delete == "Y")
                                        txtCheckbox += "<input type='checkbox' class='text-center bisaHapus' value='"+row.idMenu+"' name='bisaHapus' checked>";
                                    else {
                                        txtCheckbox += "<input type='checkbox' class='text-center bisaHapus' value='"+row.idMenu+"' name='bisaHapus'>";
                                    }
                                    txtCheckbox += "<span></span>";
                                    txtCheckbox += "</label>";
                                    txtCheckbox += "</div>";
                                return txtCheckbox;
                            },
                        },
                        {
                            field: 'posting',
                            title: 'Posting',
                            textAlign: 'center',
                            width: 85,
                            autoHide:false,
                            template: function(row) {
                                var txtCheckbox = "<div class='checkbox-inline align-items-center'>";
                                    txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                    if (row.posting == "Y")
                                        txtCheckbox += "<input type='checkbox' class='text-center bisaPosting' value='"+row.idMenu+"' name='bisaPosting' checked>";
                                    else {
                                        txtCheckbox += "<input type='checkbox' class='text-center bisaPosting' value='"+row.idMenu+"' name='bisaPosting'>";
                                    }
                                    txtCheckbox += "<span></span>";
                                    txtCheckbox += "</label>";
                                    txtCheckbox += "</div>";
                                return txtCheckbox;
                            },
                        },
                        {
                            field: 'print',
                            title: 'Print',
                            textAlign: 'center',
                            width: 85,
                            autoHide:false,
                            template: function(row) {
                                var txtCheckbox = "<div class='checkbox-inline align-items-center'>";
                                    txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                    if (row.print == "Y")
                                        txtCheckbox += "<input type='checkbox' class='text-center bisaPrint' value='"+row.idMenu+"' name='bisaPrint' checked>";
                                    else {
                                        txtCheckbox += "<input type='checkbox' class='text-center bisaPrint' value='"+row.idMenu+"' name='bisaPrint'>";
                                    }
                                    txtCheckbox += "<span></span>";
                                    txtCheckbox += "</label>";
                                    txtCheckbox += "</div>";
                                return txtCheckbox;
                            },
                        },
                        {
                            field: 'export',
                            title: 'Export',
                            textAlign: 'center',
                            width: 85,
                            autoHide:false,
                            template: function(row) {
                                var txtCheckbox = "<div class='checkbox-inline align-items-center'>";
                                    txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                    if (row.export == "Y")
                                        txtCheckbox += "<input type='checkbox' class='text-center bisaExport' value='"+row.idMenu+"' name='bisaExport' checked>";
                                    else {
                                        txtCheckbox += "<input type='checkbox' class='text-center bisaExport' value='"+row.idMenu+"' name='bisaExport'>";
                                    }
                                    txtCheckbox += "<span></span>";
                                    txtCheckbox += "</label>";
                                    txtCheckbox += "</div>";
                                return txtCheckbox;
                            },
                        },
                        {
                            field: 'approve',
                            title: 'Approve',
                            textAlign: 'center',
                            width: 85,
                            autoHide:false,
                            template: function(row) {
                                var txtCheckbox = "<div class='checkbox-inline align-items-center'>";
                                    txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                    if (row.approve == "Y")
                                        txtCheckbox += "<input type='checkbox' class='text-center bisaApprove' value='"+row.idMenu+"' name='bisaApprove' checked>";
                                    else {
                                        txtCheckbox += "<input type='checkbox' class='text-center bisaApprove' value='"+row.idMenu+"' name='bisaApprove'>";
                                    }
                                    txtCheckbox += "<span></span>";
                                    txtCheckbox += "</label>";
                                    txtCheckbox += "</div>";
                                return txtCheckbox;
                            },
                        },
                        {
                            field: 'revisi',
                            title: 'Revisi',
                            textAlign: 'center',
                            width: 85,
                            autoHide:false,
                            template: function(row) {
                                var txtCheckbox = "<div class='checkbox-inline align-items-center'>";
                                    txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                    if (row.revisi == "Y")
                                        txtCheckbox += "<input type='checkbox' class='text-center bisaRevisi' value='"+row.idMenu+"' name='bisaRevisi' checked>";
                                    else {
                                        txtCheckbox += "<input type='checkbox' class='text-center bisaRevisi' value='"+row.idMenu+"' name='bisaRevisi'>";
                                    }
                                    txtCheckbox += "<span></span>";
                                    txtCheckbox += "</label>";
                                    txtCheckbox += "</div>";
                                return txtCheckbox;
                            },
                        },
                    ],
                });
			$("#user").on("change", function() {
                var datatable = $('#table_menu').KTDatatable();
                datatable.setDataSourceParam('id_user',$(this).val());
                datatable.reload();
                //$('#table_menu').KTDatatable('reload');
			});
		});

		$(document).ready(function() {

			$("#table_menu").on("click", "table .bisaTambah", function(){
		    	var id = $(this).val();

		    	if ($(this).prop("checked") == true) {
	            	updateAkses('add', id, 'Y');
	            }
	            else {
	            	updateAkses('add', id, 'N');
	            }
			});

			$("#table_menu").on("click", "table .bisaEdit", function(){
		    	var id = $(this).val();

		    	if ($(this).prop("checked") == true) {
	            	updateAkses('edit', id, 'Y');
	            }
	            else {
	            	updateAkses('edit', id, 'N');
	            }
			});

			$("#table_menu").on("click", "table .bisaHapus", function(){
		    	var id = $(this).val();

		    	if ($(this).prop("checked") == true) {
	            	updateAkses('delete', id, 'Y');
	            }
	            else {
	            	updateAkses('delete', id, 'N');
	            }
			});

			$("#table_menu").on("click", "table .bisaPosting", function(){
		    	var id = $(this).val();

		    	if ($(this).prop("checked") == true) {
	            	updateAkses('posting', id, 'Y');
	            }
	            else {
	            	updateAkses('posting', id, 'N');
	            }
			});

			$("#table_menu").on("click", "table .bisaPrint", function(){
		    	var id = $(this).val();

		    	if ($(this).prop("checked") == true) {
	            	updateAkses('print', id, 'Y');
	            }
	            else {
	            	updateAkses('print', id, 'N');
	            }
			});

			$("#table_menu").on("click", "table .bisaExport", function(){
		    	var id = $(this).val();

		    	if ($(this).prop("checked") == true) {
	            	updateAkses('export', id, 'Y');
	            }
	            else {
	            	updateAkses('export', id, 'N');
	            }
			});

			$("#table_menu").on("click", "table .bisaApprove", function(){
		    	var id = $(this).val();

		    	if ($(this).prop("checked") == true) {
	            	updateAkses('approve', id, 'Y');
	            }
	            else {
	            	updateAkses('approve', id, 'N');
	            }
			});

            $("#table_menu").on("click", "table .bisaRevisi", function(){
			var id = $(this).val();

			if ($(this).prop("checked") == true) {
			updateAkses('revisi', id, 'Y');
	            }
	            else {
			updateAkses('revisi', id, 'N');
	            }
			});

            $("#table_menu").on("click", "table .checkAll", function(){
			var id = $(this).val();

			if ($(this).prop("checked") == true) {
			updateAkses('all', id, 'Y');
	            }
	            else {
			updateAkses('all', id, 'N');
	            }
			});
		});

		function updateAkses(jenisAkses, menuId, valueAkses) {
			$.ajaxSetup({
			    headers: {
			    	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			    }
			});
			$.ajax({
				url: "/ModuleAccess/UpdateAkses",
			    method: 'POST',
			    dataType : 'json',
			    data: {
					id_user : $("#user").val(),
				    id_menu : menuId,
				    jenis_akses : jenisAkses,
				    value_akses : valueAkses
			    },
			    success: function(result){
			    	Swal.fire(
                        "Berhasil!",
                        "Akses Menu Berhasil di Ubah!",
                        "success"
                    )
			    }
			});
            var datatable = $('#table_menu').KTDatatable();
                datatable.reload();
		}

         //$('div.alert').delay(5000).slideUp(300);
      </script>
@endsection
