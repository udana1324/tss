@extends('layout.default')
@section('content')
	<!-- Content area -->
			<div class="content">
			@include('pages.alerts')
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
                        <div class="card-title">
                            <h3 class="card-label text-white">Daftar Pengiriman Barang</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            @if($hakAkses->add == "Y")
                            <button class="btn btn-primary btn-outline-white mr-2" onclick="window.location.href = '{{ url('Delivery/Add/') }}';">
                                <i class="flaticon2-plus"></i>
                                Buat Baru
                            </button>
                            @endif
                            @if($hakAkses->export == "Y")
                            <button type="button" class="btn btn-success font-weight-bold mr-2" id="btnExport"> Export <i class="fas fa-file-excel"></i></button>
                            @endif
                            <!--end::Button-->
                        </div>
					</div>

					<div class="card-body">
                        <form action="{{ route('Delivery.Export') }}" class="form-horizontal" id="form_add" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <!--begin::Search Form-->
                        <div class="mb-7">
                            <div class="row col-12">
                                <div class="col-lg-3">
                                    <div class="align-items-center">
                                        <label style="display: inline-block;"></label>
                                        <div class="input-icon">
                                            <input type="text" class="form-control" placeholder="Search..." id="table_dlv_search_query"/>
                                            <span>
                                                <i class="flaticon2-search-1 text-muted"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class=" align-items-center">
                                        <label>Nama Pelanggan :</label>
                                        <select class="form-control select2" id="table_dlv_search_customer">
                                            <option value="">All</option>
                                            @foreach($dataCustomer as $rowCustomer)
                                            <option value="{{$rowCustomer->nama_customer}}">{{ucwords($rowCustomer->nama_customer)}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class=" align-items-center">
                                        <label>Status Pengiriman :</label>
                                        <select class="form-control select2" id="table_dlv_search_status_kirim">
                                            <option value="">All</option>
                                            <option value="1">Terkirim</option>
                                            <option value="3">Proses Pengiriman</option>
                                            <option value="2">Menunggu Persetujuan</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-2">
                                    <div class=" align-items-center">
                                        <label>Periode Pengiriman :</label>
                                        <input type="text" class="form-control" id="bulan_picker" name="bulan_picker" autocomplete="off" >
                                        <input type="hidden" class="form-control" id="bulan_picker_val" name="bulan_picker_val" readonly >
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class=" align-items-center">
                                        <label>Status :</label>
                                        <select class="form-control select2" id="table_dlv_search_status">
                                            <option value="">All</option>
                                            @foreach($dataStatus as $rowStatus)
                                            <option value="{{$rowStatus->status_pengiriman}}">{{ucwords($rowStatus->status_pengiriman)}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Search Form-->

                        <!--begin: Datatable-->
                        <div class="datatable datatable-bordered datatable-head-custom" id="table_dlv"></div>
                        <!--end: Datatable-->
                        </form>
                    </div>
				</div>
				<!-- /basic initialization -->

                <!-- Modal form konfirmasi -->
				<div id="modal_form_konfirmasi" class="modal fade">
				    <div class="modal-dialog">
					    <div class="modal-content">
						    <div class="modal-header bg-primary">

							    <h5 class="modal-title">Konfirmasi Pengiriman</h5>
						    </div>
						    <div class="modal-body">
							    <form >
								    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label">Penerima :</label>
                                                <div class="col-lg-9">
                                                    <input type="hidden"name="idSJ" id="idSJ">
                                                    <input type="text" class="form-control" placeholder="Masukkan Nama Penerima SJ" name="penerima" id="penerima" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label">Tanggal Diterima :</label>
                                                <div class="form-group form-group-feedback form-group-feedback-right">
                                                    <input type="hidden" class="form-control req" name="tanggal" id="tanggal">
                                                    <input type="text" class="form-control" name="tanggal_picker" id="tanggal_picker" readonly>
                                                    <span class="form-text text-danger err" style="display:none;">*Harap pilih tanggal diterima terlebih dahulu!</span>
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
            $('#table_dlv_search_customer').select2({
                allowClear: true
            });

            $('#table_dlv_search_status').select2({
                allowClear: true
            });

            $('#table_dlv_search_status_kirim').select2({
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

            $('#tanggal_picker').datepicker({
                rtl: KTUtil.isRTL(),
                todayHighlight: true,
                orientation: "bottom left",
                autoclose : true,
                format : "dd MM yyyy",
                locale: "id",
            });

            $("#tanggal_picker").datepicker('setDate', new Date());
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

        $("#tanggal_picker").on('change', function() {
            $("#tanggal").val($("#tanggal_picker").data('datepicker').getFormattedDate('yyyy-mm-dd'));
        });

		$(document).ready(function() {

            var datatable = $('#table_dlv').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Delivery/GetData',
                            method: 'GET',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
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
                    input: $('#table_dlv_search_query')
                },

                columns: [
                    {
                        field: 'id',
                        title: '#',
                        sortable: false,
                        type: 'number',
                        selector: false,
                        textAlign: 'center',
                        width: 'auto',
                        visible:false,
                    },
                    {
                        field: 'kode_pengiriman',
                        title: 'No. Pengiriman',
                        textAlign: 'left',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bolder">'+row.kode_pengiriman.toUpperCase()+'</span>';
                                txt += "<br />";
                                txt += '<span class="label label-md label-outline-primary label-inline mr-2 mb-1 mt-1">'+row.no_so.toUpperCase()+'</span>';
                                txt += "<br />";

                                if (row.no_po_customer != null) {
                                    txt += '<span class="font-weight-bold text-inline text-primary font-size-xs">No. PO : '+row.no_po_customer.toUpperCase()+'</span><br />';
                                }
                                else {
                                    txt += '<span class="font-weight-bold text-inline text-primary font-size-xs">No. PO : - </span><br />';
                                }
                                if (row.flag_invoiced == 1) {
                                    txt += '<span class="label label-md label-dot label-danger mr-2"></span> <span class="font-weight-bold text-inline text-danger font-size-xs">INVOICED</span>';
                                }
                                return txt;
                        },
                    },
                    {
                        field: 'nama_customer',
                        title: 'Nama Pelanggan',
                        textAlign: 'left',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bold">'+row.nama_customer+'</span>';
                                txt += "<br />";
                                txt += '<span class="label label-md label-outline-primary label-inline mt-1">' + row.nama_outlet + '</span>';
                                return txt;
                        },
                    },
                    {
                        field: 'jumlah_total_sj',
                        title: 'Jumlah',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: true,
                        template: function(row) {
                            return parseFloat(row.jumlah_total_sj).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },
                    {
                        field: 'tanggal_sj',
                        title: 'Tanggal SJ',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                txt += formatDate(row.tanggal_sj);
                                txt += "<br />";
                                if (row.tanggal_kirim != null) {
                                    txt += '<span class="label label-md label-outline-success label-inline mt-1"><i class="flaticon2-delivery-truck text-success mt-1 mr-2"></i>'+formatDate(row.tanggal_kirim)+'</span>';
                                }
                                else {
                                    txt += "-";
                                }
                                return txt;
                        },
                    },
                    {
                        field: 'status_pengiriman',
                        title: 'Status',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            var statusTxt = "";
                            if (row.flag_revisi == '1') {
                                statusTxt = ucwords(row.status_pengiriman)+"-R";
                            }
                            else {
                                statusTxt = ucwords(row.status_pengiriman);
                            }

                            if (row.status_pengiriman == "draft") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline">'+statusTxt+'</span>';
                            }
                            else if (row.status_pengiriman == "posted") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-primary">'+statusTxt+'</span>';
                            }
                            else if (row.status_pengiriman == "close") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-warning">'+statusTxt+'</span>';
                            }
                            else if (row.status_pengiriman == "batal") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-danger">'+statusTxt+'</span>';
                            }
                            else if (row.status_pengiriman == "full") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-success">'+statusTxt+'</span>';
                            }

                            return statusTxt;
                        },
                    },
                    {
                        field: 'flag_terkirim',
                        title: 'Status Pengiriman',
                        textAlign: 'center',
                        autoHide: true,
                        width: 'auto',
                        template: function(row) {
                            if (row.status_pengiriman == 'posted' && row.flag_terkirim == '1') {
                                return '<span class="label label-inline label-light-success font-weight-bold">Terkirim oleh '+ucwords(row.updated_by)+'</span>';
                            }
                            else if (row.status_pengiriman == 'posted' && row.flag_terkirim == '0') {
                                return '<span class="label label-inline label-light-primary font-weight-bold">Proses Pengiriman</span>';
                            }
                            else if (row.status_pengiriman == "draft") {
                                return '<span class="label label-inline font-weight-bold">Menunggu Persetujuan</span>';
                            }
                        },
                    },
                    {
                        field: 'Actions',
                        title: 'Aksi',
                        sortable: false,
                        width: 'auto',
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

                                    txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('Delivery.Detail', 'idDlv')}}'><i class='nav-icon la la-eye'></i><span class='nav-text'>Detail</span></a></li>";
                                    if (akses.edit == "Y" && row.status_pengiriman == "draft") {
                                        txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('Delivery.edit', 'idDlv')}}'><i class='nav-icon la la-edit'></i><span class='nav-text'>Ubah Surat Jalan</span></a></li>";
                                        txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('Delivery.Staging', 'idDlv')}}'><i class='nav-icon la la-edit'></i><span class='nav-text'>Ubah Alokasi</span></a></li>";
                                    }
                                    if (akses.delete) {
                                        // txtAction += "<li class='nav-item'><a class='nav-link' href='#' onclick='deleteData("+row.id+");return false;''><i class='nav-icon la la-trash'></i><span class='nav-text'>Hapus</span></a></li>";
                                    }
                                    txtAction += '</ul>';
                                    txtAction += '</div>';
                                    txtAction += '</div>';
                                    txtAction = txtAction.replaceAll('idDlv',row.id);
                                    return txtAction;
                            }
                            else if (usergroup.user_group == "operasional" && row.status_pengiriman == "posted" && row.flag_terkirim == "0") {
                                var txtAction = "<button type='button' onclick='btnKonfirmClick(idDlv)' class='btn btn-primary btn-sm font-weight-bold' id='btnKonfirm' data-toggle='modal' data-target='#modal_form_konfirmasi'>Konfirmasi Pengiriman</button>";
                                    txtAction = txtAction.replaceAll('idDlv',row.id);
                                    return txtAction;
                            }
                            else {
                                return "";
                            }
                        },
                    }
                ],
            });

            $('#table_dlv_search_customer').on('change', function() {
                datatable.search($(this).val(), 'nama_customer');
            });

            $('#table_dlv_search_status').on('change', function() {
                datatable.search($(this).val(), 'status_pengiriman');
            });

            $("#bulan_picker").on('change', function() {
                var bulanDate = $("#bulan_picker").data('datepicker').getFormattedDate('yyyy-mm-dd');
                $("#bulan_picker_val").val(bulanDate);
                datatable.setDataSourceParam('periode', bulanDate);
                datatable.reload();
            });

            $('#table_dlv_search_status_kirim').on('change', function() {
                datatable.search($(this).val(), 'flag_terkirim');
            });
        });

        function btnKonfirmClick(id) {
            $("#idSJ").val(id);
        }

        $("#btnKonfirmSave").on('click', function(e) {
            var id = $("#idSJ").val();
            var penerima = $("#penerima").val();
            var tgl = $("#tanggal").val();
			Swal.fire({
                title: "Konfirmasi Pengiriman",
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
                        url: "/Delivery/ConfirmDelivery",
                        method: 'POST',
                        dataType : 'json',
                        data: {
                            idDelivery : id,
                            namaPenerima : penerima,
                            tanggal: tgl
                        },
                        success: function(result){
                            if (result != "false") {
                                Swal.fire(
                                    "Sukses!",
                                    "Konfirmasi Pengiriman Berhasil!.",
                                    "success"
                                )
                                var datatable = $("#table_dlv").KTDatatable();
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
                        url: "/Delivery/Delete",
                        method: 'POST',
                        data: {
                            idDelivery: id
                        },
                        success: function(result){
                            Swal.fire(
                                "Sukses!",
                                "Data Berhasil dihapus!.",
                                "success"
                            )
                        }
                    });
                    var datatable = $("#table_dlv").KTDatatable();
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

        $(document).ready(function () {
            $("#btnExport").on('click', function(e) {
                var errCount = 0;

                if (errCount == 0) {
                    Swal.fire({
                        title: "Export Data?",
                        text: "Apakah data sudah sesuai?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Ya",
                        cancelButtonText: "Tidak",
                        reverseButtons: false
                    }).then(function(result) {
                        if(result.value) {
                            $("#form_add").off("submit").submit();
                        }
                        else if (result.dismiss === "cancel") {
                            e.preventDefault();
                        }
                    });
                }
            });
        });

    </script>
@endsection
