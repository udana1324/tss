@extends('layout.default')
@section('content')
	<!-- Content area -->
			<div class="content">
			@include('pages.alerts')
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
                        <div class="card-title">
                            <h3 class="card-label text-white">Daftar Tukar Faktur</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            @if($hakAkses->add == "Y")
                            <button class="btn btn-primary btn-outline-white mr-2" onclick="window.location.href = '{{ url('SalesInvoiceCollection/Add') }}';">
                                <i class="flaticon2-plus"></i>
                                Buat Baru
                            </button>
                            @endif
                            @if($hakAkses->export == "Y")
                            <button type="button" class="btn btn-success font-weight-bold mr-2" id="btnExport" onclick="ExportExcel('T','table_inv_col_sale','Tukar Faktur','xlsx');"> Export <i class="fas fa-file-excel"></i></button>
                            @endif
                            <!--end::Button-->
                        </div>
					</div>

					<div class="card-body">

                        <!--begin::Search Form-->
                        <div class="mb-7">
                            <div class="row align-items-center">
                                <div class="col-lg-10">
                                    <div class="row align-items-center">
                                        <div class="col-md-3 my-2 my-md-0">
                                            <div class="align-items-center">
                                                <label style="display: inline-block;"></label>
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" placeholder="Search..." id="table_inv_col_sale_search_query"/>
                                                    <span>
                                                        <i class="flaticon2-search-1 text-muted"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 my-2 my-md-0">
                                            <div class="align-items-center">
                                                <label class="mr-3 mb-0 d-none d-md-block">Nama Pelanggan :</label>
                                                <select class="form-control select2" id="table_inv_col_sale_search_customer">
                                                    <option value="">All</option>
                                                    @foreach($dataCustomer as $rowCustomer)
                                                    <option value="{{$rowCustomer->nama_customer}}">{{ucwords($rowCustomer->nama_customer)}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2 my-2 my-md-0">
                                            <div class="align-items-center">
                                                <label class="mr-3 mb-0 d-none d-md-block">Status :</label>
                                                <select class="form-control select2" id="table_inv_col_sale_search_status">
                                                    <option value="">All</option>
                                                    @foreach($dataStatus as $rowStatus)
                                                    <option value="{{$rowStatus->status}}">{{ucwords($rowStatus->status)}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 my-2 my-md-0">
                                            <div class=" align-items-center">
                                                <label class="mr-3 mb-0 d-none d-md-block">Periode Tukar Faktur :</label>
                                                <input type="text" class="form-control" id="bulan_picker" name="bulan_picker" autocomplete="off" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end: Search Form-->

                        <!--begin: Datatable-->

                        <div class="datatable datatable-bordered datatable-head-custom" id="table_inv_col_sale"></div>

                        <!--end: Datatable-->
                    </div>
				</div>
				<!-- /basic initialization -->

                <!-- Modal form konfirmasi -->
				<div id="modal_form_konfirmasi" class="modal fade">
				    <div class="modal-dialog">
					    <div class="modal-content">
						    <div class="modal-header bg-primary">

							    <h5 class="modal-title">Konfirmasi Tukar Faktur</h5>
						    </div>
						    <div class="modal-body">
							    <form >
								    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label">Penerima :</label>
                                                <div class="col-lg-9">
                                                    <input type="hidden"name="idTF" id="idTF">
                                                    <input type="text" class="form-control" placeholder="Masukkan Nama Penerima Tukar Faktur" name="penerima" id="penerima" autocomplete="off">
                                                    <input type="text" class="form-control" placeholder="Masukkan Nomor Tukar Faktur" name="nmr_faktur" id="nmr_faktur" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
							    </form>

						    </div>

						    <div class="modal-footer">
							    <button type="button" class="btn btn-link" data-dismiss="modal">Tutup</button>
                                <button type="button" class="btn btn-primary btn-sm font-weight-bold" id="btnKonfirmSave">Simpan</button>
						    </div>
					    </div>
				    </div>
			    </div>
                <!-- /form form konfirmasi -->

			</div>
			<!-- /content area -->
@endsection
@section('scripts')
	<script type="text/javascript">
		//$('div.alert').delay(5000).slideUp(300);
        $(document).ready(function () {
            $('#table_inv_col_sale_search_customer').select2({
                allowClear: true
            });

            $('#table_inv_col_sale_search_status').select2({
                allowClear: true
            });

            $('#bulan_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                autoclose : true,
                startView: "months",
                minViewMode: "months",
                format : "MM yyyy",
                clearBtn: true,
            });
        });

        function formatDate(strDate) {
            var arrMonth = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            var date = new Date(strDate);
            var day = date.getDate();
            var month = date.getMonth();
            var year = date.getFullYear();

            return day + ' ' + arrMonth[month] + ' ' + year;
        }

		function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

		$(document).ready(function() {

            var datatable = $('#table_inv_col_sale').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/SalesInvoiceCollection/GetData',
                            method: 'GET',

                        }
                    },
                    pageSize: 25,
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

                search: {
                    input: $('#table_inv_col_sale_search_query')
                },

                columns: [
                    {
                        field: 'id',
                        title: '#',
                        sortable: false,
                        width: 'auto',
                        type: 'number',
                        selector: false,
                        textAlign: 'center',
                        visible:false,
                    },
                    {
                        field: 'nama_customer',
                        title: 'Nama Pelanggan',
                        width: 200,
                        textAlign: 'left',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bold">'+row.nama_customer+'</span><br />';
                                txt += '<span class="label label-md label-outline-primary label-inline mt-1">'+row.kode_tf.toUpperCase()+'</span>';
                                return txt;
                        },
                    },
                    {
                        field: 'nmr_tf',
                        title: 'Nomor TF',
                        width: 100,
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            if (row.nmr_tf != null) {
                                return row.nmr_tf;
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'nominal',
                        title: 'Nominal (Rp)',
                        width: 'auto',
                        textAlign: 'right',
                        autoHide: false,
                        template: function(row) {
                            return parseFloat(row.nominal).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'tanggal',
                        title: 'Tanggal TF',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            if (row.tanggal != null) {
                                return formatDate(row.tanggal);
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'status',
                        title: 'Status',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            var statusTxt = "";
                            if (row.flag_revisi == '1') {
                                statusTxt = ucwords(row.status)+"-R";
                            }
                            else {
                                statusTxt = ucwords(row.status);
                            }

                            if (row.status == "draft") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline">'+statusTxt+'</span>';
                            }
                            else if (row.status == "posted") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-primary">'+statusTxt+'</span>';
                            }
                            else if (row.status == "close") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-warning">'+statusTxt+'</span>';
                            }
                            else if (row.status == "batal") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-danger">'+statusTxt+'</span>';
                            }
                            else if (row.status == "full") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-success">'+statusTxt+'</span>';
                            }

                            return statusTxt;
                        },
                    },
                    {
                        field: 'flag_approve',
                        title: 'Status Konfirmasi',
                        width: 'auto',
                        autoHide: false,
                        textAlign: 'center',
                        //autoHide: false,
                        template: function(row) {
                            //row.pic_penerima
                            if (row.flag_approved == '1') {
                                return '<span class="label label-md font-weight-bold label-pill label-inline label-success">Approved</span>';
                            }
                            else {
                                return '<span class="label label-md font-weight-bold label-pill label-inline label-primary">Belum Approved</span>';
                            }
                        },
                    },
                    {
                        field: 'Actions',
                        title: 'Aksi',
                        width: 'auto',
                        sortable: false,
                        overflow: 'visible',
                        autoHide: false,
                        textAlign: 'center',
                        template: function(row) {
                            var akses = @json($hakAkses);
                            var usergroup = @json(Auth::user());

                            if (usergroup.user_group != "operasional") {
                                var txtAction = '<div class="dropdown dropdown-inline">';
                                    txtAction += '<a href="#" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">';
                                    txtAction += '<i class="la la-cog"></i>';
                                    txtAction += '</a>';
                                    txtAction += '<div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">';
                                    txtAction += '<ul class="nav nav-hoverable flex-column">';

                                    txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('SalesInvoiceCollection.Detail', 'idInv')}}'><i class='nav-icon la la-eye'></i><span class='nav-text'>Detail</span></a></li>";
                                    if (akses.edit == "Y" && row.status == "draft") {
                                        txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('SalesInvoiceCollection.edit', 'idInv')}}'><i class='nav-icon la la-edit'></i><span class='nav-text'>Ubah</span></a></li>";
                                    }
                                    if (akses.delete) {
                                        // txtAction += "<li class='nav-item'><a class='nav-link' href='#' onclick='deleteData("+row.id+");return false;''><i class='nav-icon la la-trash'></i><span class='nav-text'>Hapus</span></a></li>";
                                    }
                                    txtAction += '</ul>';
                                    txtAction += '</div>';
                                    txtAction += '</div>';
                                    txtAction = txtAction.replaceAll('idInv',row.id);
                                    return txtAction;
                            }
                            else if (usergroup.user_group == "operasional" && row.status == "posted" && row.flag_approved == "0") {
                                var txtAction = "<button type='button' onclick='btnKonfirmClick(idInv)' class='btn btn-primary btn-sm font-weight-bold' id='btnKonfirm' data-toggle='modal' data-target='#modal_form_konfirmasi'>Konfirmasi Tukar Faktur</button>";
                                    txtAction = txtAction.replaceAll('idInv',row.id);
                                    return txtAction;
                            }
                            else {
                                return "";
                            }
                        },
                    }
                ],
            });

            $('#table_inv_col_sale_search_customer').on('change', function() {
                datatable.search($(this).val(), 'nama_customer');
            });

            $('#table_inv_col_sale_search_status').on('change', function() {
                datatable.search($(this).val(), 'status');
            });

            $("#bulan_picker").on('change', function() {
                var bulanDate = $("#bulan_picker").data('datepicker').getFormattedDate('yyyy-mm-dd');
                datatable.setDataSourceParam('periode', bulanDate);
                datatable.reload();
            });
        });

        function btnKonfirmClick(id) {
            $("#idTF").val(id);
        }

        $("#btnKonfirmSave").on('click', function(e) {
            var id = $("#idTF").val();
            var penerima = $("#penerima").val();
            var nmr = $("#nmr_faktur").val();
			Swal.fire({
                title: "Konfirmasi Tukar Faktur",
                text: "Apakah Pengiriman telah selesai?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if(result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "/SalesInvoiceCollection/ConfirmCollection",
                        method: 'POST',
                        dataType : 'json',
                        data: {
                            idCollection : id,
                            namaPenerima : penerima,
                            nmrFaktur: nmr
                        },
                        success: function(result){
                            if (result != "false") {
                                Swal.fire(
                                    "Sukses!",
                                    "Konfirmasi Tukar Faktur Berhasil!.",
                                    "success"
                                )
                                var datatable = $("#table_inv_col_sale").KTDatatable();
                                $('#modal_form_konfirmasi').modal('toggle');
                                datatable.reload();
                            }
                            else {
                                Swal.fire(
                                    "Gagal!",
                                    "Harap Masukkan Nama Penerima terlebih dahulu!.",
                                    "warning"
                                )
                            }
                        }
                    });
                }
                else if (result.dismiss === "cancel") {
                    e.preventDefault();
                }
            });
		});

        function deleteData(id) {
            Swal.fire({
                title: "Hapus?",
                text: "Apakah anda ingin menghapus data ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                reverseButtons: false
            }).then(function(result) {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "/SalesInvoiceCollection/Delete",
                        method: 'POST',
                        data: {
                            idSalesInvoiceCollection: id
                        },
                        success: function(result){
                            Swal.fire(
                                "Sukses!",
                                "Data Berhasil dihapus!.",
                                "success"
                            )
                        }
                    });
                    var datatable = $("#table_inv_col_sale").KTDatatable();
                    datatable.reload();
                }
                else if (result.dismiss === "cancel") {
                    // Swal.fire(
                    //     "Cancelled",
                    //     "Your imaginary file is safe :)",
                    //     "error"
                    // )
                    e.preventDefault();
                }
            });
        }

    </script>
@endsection
