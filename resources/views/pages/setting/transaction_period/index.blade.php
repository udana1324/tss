@extends('layout.default')
@section('content')
    @include('pages.alerts')
    <div class="content">
        <div class="card card-custom">
            <div class="card-header bg-primary header-elements-sm-inline">
                <div class="card-title">
                    <h3 class="card-label text-white">Periode Transaksi</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="card-toolbar">

                </div>
                <!--begin: Search Form-->
                <!--begin::Search Form-->
                <div class="mb-7">
                    <div class="row align-items-center">
                        <div class="col-lg-9 col-xl-8">
                            <div class="row align-items-center">

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

		$(document).ready(function() {
            var datatable = $('#table_menu').KTDatatable({
                    data: {
                        type: 'remote',
                        source: {
                            read: {
                                url: 'TransactionPeriod/GetPeriod',
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
                            field: 'jan',
                            title: 'Januari',
                            textAlign: 'center',
                            width: '100',
                            template: function(row) {
                                var txtCheckbox = "<div class='checkbox-inline align-items-center'>";
                                    txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                    if (row.jan == "Y")
                                        txtCheckbox += "<input type='checkbox' class='text-center jan' value='"+row.id+"' name='jan' checked>";
                                    else {
                                        txtCheckbox += "<input type='checkbox' class='text-center jan' value='"+row.id+"' name='jan'>";
                                    }
                                    txtCheckbox += "<span></span>";
                                    txtCheckbox += "</label>";
                                    txtCheckbox += "</div>";
                                return txtCheckbox;
                            },
                        },
                        {
                            field: 'feb',
                            title: 'Februari',
                            textAlign: 'center',
                            width: '100',
                            template: function(row) {
                                var txtCheckbox = "<div class='checkbox-inline align-items-center'>";
                                    txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                    if (row.feb == "Y")
                                        txtCheckbox += "<input type='checkbox' class='text-center feb' value='"+row.id+"' name='feb' checked>";
                                    else {
                                        txtCheckbox += "<input type='checkbox' class='text-center feb' value='"+row.id+"' name='feb'>";
                                    }
                                    txtCheckbox += "<span></span>";
                                    txtCheckbox += "</label>";
                                    txtCheckbox += "</div>";
                                return txtCheckbox;
                            },
                        },
                        {
                            field: 'mar',
                            title: 'Maret',
                            textAlign: 'center',
                            width: '100',
                            template: function(row) {
                                var txtCheckbox = "<div class='checkbox-inline align-items-center'>";
                                    txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                    if (row.mar == "Y")
                                        txtCheckbox += "<input type='checkbox' class='text-center mar' value='"+row.id+"' name='mar' checked>";
                                    else {
                                        txtCheckbox += "<input type='checkbox' class='text-center mar' value='"+row.id+"' name='mar'>";
                                    }
                                    txtCheckbox += "<span></span>";
                                    txtCheckbox += "</label>";
                                    txtCheckbox += "</div>";
                                return txtCheckbox;
                            },
                        },
                        {
                            field: 'apr',
                            title: 'April',
                            textAlign: 'center',
                            width: '100',
                            template: function(row) {
                                var txtCheckbox = "<div class='checkbox-inline align-items-center'>";
                                    txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                    if (row.apr == "Y")
                                        txtCheckbox += "<input type='checkbox' class='text-center apr' value='"+row.id+"' name='apr' checked>";
                                    else {
                                        txtCheckbox += "<input type='checkbox' class='text-center apr' value='"+row.id+"' name='apr'>";
                                    }
                                    txtCheckbox += "<span></span>";
                                    txtCheckbox += "</label>";
                                    txtCheckbox += "</div>";
                                return txtCheckbox;
                            },
                        },
                        {
                            field: 'may',
                            title: 'Mei',
                            textAlign: 'center',
                            width: '100',
                            template: function(row) {
                                var txtCheckbox = "<div class='checkbox-inline align-items-center'>";
                                    txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                    if (row.may == "Y")
                                        txtCheckbox += "<input type='checkbox' class='text-center may' value='"+row.id+"' name='may' checked>";
                                    else {
                                        txtCheckbox += "<input type='checkbox' class='text-center may' value='"+row.id+"' name='may'>";
                                    }
                                    txtCheckbox += "<span></span>";
                                    txtCheckbox += "</label>";
                                    txtCheckbox += "</div>";
                                return txtCheckbox;
                            },
                        },
                        {
                            field: 'jun',
                            title: 'Juni',
                            textAlign: 'center',
                            width: '100',
                            template: function(row) {
                                var txtCheckbox = "<div class='checkbox-inline align-items-center'>";
                                    txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                    if (row.jun == "Y")
                                        txtCheckbox += "<input type='checkbox' class='text-center jun' value='"+row.id+"' name='jun' checked>";
                                    else {
                                        txtCheckbox += "<input type='checkbox' class='text-center jun' value='"+row.id+"' name='jun'>";
                                    }
                                    txtCheckbox += "<span></span>";
                                    txtCheckbox += "</label>";
                                    txtCheckbox += "</div>";
                                return txtCheckbox;
                            },
                        },
                        {
                            field: 'jul',
                            title: 'Juli',
                            textAlign: 'center',
                            width: '100',
                            template: function(row) {
                                var txtCheckbox = "<div class='checkbox-inline align-items-center'>";
                                    txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                    if (row.jul == "Y")
                                        txtCheckbox += "<input type='checkbox' class='text-center jul' value='"+row.id+"' name='jul' checked>";
                                    else {
                                        txtCheckbox += "<input type='checkbox' class='text-center jul' value='"+row.id+"' name='jul'>";
                                    }
                                    txtCheckbox += "<span></span>";
                                    txtCheckbox += "</label>";
                                    txtCheckbox += "</div>";
                                return txtCheckbox;
                            },
                        },
                        {
                            field: 'aug',
                            title: 'Agustus',
                            textAlign: 'center',
                            width: '100',
                            template: function(row) {
                                var txtCheckbox = "<div class='checkbox-inline align-items-center'>";
                                    txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                    if (row.aug == "Y")
                                        txtCheckbox += "<input type='checkbox' class='text-center aug' value='"+row.id+"' name='aug' checked>";
                                    else {
                                        txtCheckbox += "<input type='checkbox' class='text-center aug' value='"+row.id+"' name='aug'>";
                                    }
                                    txtCheckbox += "<span></span>";
                                    txtCheckbox += "</label>";
                                    txtCheckbox += "</div>";
                                return txtCheckbox;
                            },
                        },
                        {
                            field: 'sep',
                            title: 'September',
                            textAlign: 'center',
                            width: '100',
                            template: function(row) {
                                var txtCheckbox = "<div class='checkbox-inline align-items-center'>";
                                    txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                    if (row.sep == "Y")
                                        txtCheckbox += "<input type='checkbox' class='text-center sep' value='"+row.id+"' name='sep' checked>";
                                    else {
                                        txtCheckbox += "<input type='checkbox' class='text-center sep' value='"+row.id+"' name='sep'>";
                                    }
                                    txtCheckbox += "<span></span>";
                                    txtCheckbox += "</label>";
                                    txtCheckbox += "</div>";
                                return txtCheckbox;
                            },
                        },
                        {
                            field: 'oct',
                            title: 'Oktober',
                            textAlign: 'center',
                            width: '100',
                            template: function(row) {
                                var txtCheckbox = "<div class='checkbox-inline align-items-center'>";
                                    txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                    if (row.oct == "Y")
                                        txtCheckbox += "<input type='checkbox' class='text-center oct' value='"+row.id+"' name='oct' checked>";
                                    else {
                                        txtCheckbox += "<input type='checkbox' class='text-center oct' value='"+row.id+"' name='oct'>";
                                    }
                                    txtCheckbox += "<span></span>";
                                    txtCheckbox += "</label>";
                                    txtCheckbox += "</div>";
                                return txtCheckbox;
                            },
                        },
                        {
                            field: 'nov',
                            title: 'November',
                            textAlign: 'center',
                            width: '100',
                            template: function(row) {
                                var txtCheckbox = "<div class='checkbox-inline align-items-center'>";
                                    txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                    if (row.nov == "Y")
                                        txtCheckbox += "<input type='checkbox' class='text-center nov' value='"+row.id+"' name='nov' checked>";
                                    else {
                                        txtCheckbox += "<input type='checkbox' class='text-center nov' value='"+row.id+"' name='nov'>";
                                    }
                                    txtCheckbox += "<span></span>";
                                    txtCheckbox += "</label>";
                                    txtCheckbox += "</div>";
                                return txtCheckbox;
                            },
                        },
                        {
                            field: 'dec',
                            title: 'Desember',
                            textAlign: 'center',
                            width: '100',
                            template: function(row) {
                                var txtCheckbox = "<div class='checkbox-inline align-items-center'>";
                                    txtCheckbox += "<label class='checkbox checkbox-lg'>";
                                    if (row.dec == "Y")
                                        txtCheckbox += "<input type='checkbox' class='text-center dec' value='"+row.id+"' name='dec' checked>";
                                    else {
                                        txtCheckbox += "<input type='checkbox' class='text-center dec' value='"+row.id+"' name='dec'>";
                                    }
                                    txtCheckbox += "<span></span>";
                                    txtCheckbox += "</label>";
                                    txtCheckbox += "</div>";
                                return txtCheckbox;
                            },
                        },
                    ],
                });
		});

		$(document).ready(function() {

			$("#table_menu").on("click", "table .jan", function(){
		    	var id = $(this).val();

		    	if ($(this).prop("checked") == true) {
	            	updateAkses('jan', id, 'Y');
	            }
	            else {
	            	updateAkses('jan', id, 'N');
	            }
			});

			$("#table_menu").on("click", "table .feb", function(){
		    	var id = $(this).val();

		    	if ($(this).prop("checked") == true) {
	            	updateAkses('feb', id, 'Y');
	            }
	            else {
	            	updateAkses('feb', id, 'N');
	            }
			});

			$("#table_menu").on("click", "table .mar", function(){
		    	var id = $(this).val();

		    	if ($(this).prop("checked") == true) {
	            	updateAkses('mar', id, 'Y');
	            }
	            else {
	            	updateAkses('mar', id, 'N');
	            }
			});

			$("#table_menu").on("click", "table .apr", function(){
		    	var id = $(this).val();

		    	if ($(this).prop("checked") == true) {
	            	updateAkses('apr', id, 'Y');
	            }
	            else {
	            	updateAkses('apr', id, 'N');
	            }
			});

			$("#table_menu").on("click", "table .may", function(){
		    	var id = $(this).val();

		    	if ($(this).prop("checked") == true) {
	            	updateAkses('may', id, 'Y');
	            }
	            else {
	            	updateAkses('may', id, 'N');
	            }
			});

			$("#table_menu").on("click", "table .jun", function(){
		    	var id = $(this).val();

		    	if ($(this).prop("checked") == true) {
	            	updateAkses('jun', id, 'Y');
	            }
	            else {
	            	updateAkses('jun', id, 'N');
	            }
			});

			$("#table_menu").on("click", "table .jul", function(){
		    	var id = $(this).val();

		    	if ($(this).prop("checked") == true) {
	            	updateAkses('jul', id, 'Y');
	            }
	            else {
	            	updateAkses('jul', id, 'N');
	            }
			});

            $("#table_menu").on("click", "table .aug", function(){
		    	var id = $(this).val();

		    	if ($(this).prop("checked") == true) {
	            	updateAkses('aug', id, 'Y');
	            }
	            else {
	            	updateAkses('aug', id, 'N');
	            }
			});

            $("#table_menu").on("click", "table .sep", function(){
		    	var id = $(this).val();

		    	if ($(this).prop("checked") == true) {
	            	updateAkses('sep', id, 'Y');
	            }
	            else {
	            	updateAkses('sep', id, 'N');
	            }
			});

            $("#table_menu").on("click", "table .oct", function(){
		    	var id = $(this).val();

		    	if ($(this).prop("checked") == true) {
	            	updateAkses('oct', id, 'Y');
	            }
	            else {
	            	updateAkses('oct', id, 'N');
	            }
			});

            $("#table_menu").on("click", "table .nov", function(){
		    	var id = $(this).val();

		    	if ($(this).prop("checked") == true) {
	            	updateAkses('nov', id, 'Y');
	            }
	            else {
	            	updateAkses('nov', id, 'N');
	            }
			});

            $("#table_menu").on("click", "table .dec", function(){
		    	var id = $(this).val();

		    	if ($(this).prop("checked") == true) {
	            	updateAkses('dec', id, 'Y');
	            }
	            else {
	            	updateAkses('dec', id, 'N');
	            }
			});
		});

		function updateAkses(jenisAkses, menuId, valueAkses) {
            var txt = "";

            if (valueAkses == "Y") {
                txt ="Akses Periode berhasil dibuka!";
            }
            else {
                txt ="Akses Periode berhasil ditutup!";
            }
			$.ajaxSetup({
			    headers: {
			    	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			    }
			});
			$.ajax({
				url: "/TransactionPeriod/UpdateAkses",
			    method: 'POST',
			    dataType : 'json',
			    data: {
				    id_menu : menuId,
				    jenis_akses : jenisAkses,
				    value_akses : valueAkses
			    },
			    success: function(result){
			    	Swal.fire(
                        "Berhasil!",
                        txt,
                        "success"
                    )
			    }
			});
		}

         //$('div.alert').delay(5000).slideUp(300);
      </script>
@endsection
