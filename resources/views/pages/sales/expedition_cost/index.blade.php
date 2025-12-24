@extends('layout.default')
@section('content')
	<!-- Content area -->
			<div class="content">
			@include('pages.alerts')
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
                        <div class="card-title">
                            <h3 class="card-label text-white">Daftar Biaya Expedisi</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            @if($hakAkses->add == "Y")
                            <button class="btn btn-primary btn-outline-white mr-2" onclick="window.location.href = '{{ url('ExpeditionCost/Add') }}';">
                                <i class="flaticon2-plus"></i>
                                Buat Baru
                            </button>
                            @endif
                            @if($hakAkses->export == "Y")
                            <button type="button" class="btn btn-success font-weight-bold mr-2" id="btnExport" onclick="ExportExcel('T','table_biaya','Invoice Penjualan','xlsx');"> Export <i class="fas fa-file-excel"></i></button>
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
                                                    <input type="text" class="form-control" placeholder="Search..." id="table_biaya_search_query"/>
                                                    <span>
                                                        <i class="flaticon2-search-1 text-muted"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 my-2 my-md-0">
                                            <div class="align-items-center">
                                                <label class="mr-3 mb-0 d-none d-md-block">Nama Ekspedisi :</label>
                                                <select class="form-control select2" id="table_biaya_search_cabang">
                                                    <option value="">All</option>
                                                    @foreach($dataEkspedisi as $ekspedisi)
                                                    <option value="{{$ekspedisi->nama_cabang}}">{{strtoupper($ekspedisi->nama_cabang)}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2 my-2 my-md-0">
                                            <div class=" align-items-center">
                                                <label class="mr-3 mb-0 d-none d-md-block">Periode Biaya :</label>
                                                <input type="text" class="form-control" id="bulan_picker" name="bulan_picker" autocomplete="off" >
                                            </div>
                                        </div>
                                        <div class="col-md-2 my-2 my-md-0">
                                            <div class="align-items-center">
                                                <label class="mr-3 mb-0 d-none d-md-block">Status :</label>
                                                <select class="form-control select2" id="table_biaya_search_status">
                                                    <option value="">All</option>
                                                    @foreach($dataStatus as $rowStatus)
                                                    <option value="{{$rowStatus->status_biaya}}">{{ucwords($rowStatus->status_biaya)}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end: Search Form-->

                        <!--begin: Datatable-->

                        <div class="datatable datatable-bordered datatable-head-custom" id="table_biaya"></div>

                        <!--end: Datatable-->
                    </div>
				</div>
				<!-- /basic initialization -->

                <!-- Modal form resi -->
				<div id="modal_form_resi" class="modal fade">
				    <div class="modal-dialog">
					    <div class="modal-content">
						    <div class="modal-header bg-primary">

							    <h5 class="modal-title">Input Resi Pengiriman</h5>
						    </div>
						    <div class="modal-body">
							    <form >
								    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label class="col-lg-3 col-form-label">No. Resi :</label>
                                                <div class="col-lg-9">
                                                    <input type="hidden"name="idSJ" id="idSJ">
                                                    <input type="text" class="form-control" placeholder="Masukkan No. Resi Ekspedisi" name="resi" id="resi" autocomplete="off">
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
            $('#table_biaya_search_cabang').select2({
                allowClear: true
            });

            $('#table_biaya_search_status').select2({
                allowClear: true
            });

            $('#table_inv_search_status_bayar').select2({
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

            var datatable = $('#table_biaya').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/ExpeditionCost/GetData',
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
                    input: $('#table_biaya_search_query')
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
                        field: 'no_biaya',
                        title: 'No. Biaya',
                        textAlign: 'left',
                        width: 199,
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                if (row.no_resi != null) {
                                    txt += '<span class="font-weight-bold text-inline text-primary font-size-xs">No. Resi : '+row.no_resi.toUpperCase()+'</span>';
                                }
                                else {
                                    txt += '<span class="font-weight-bold text-inline text-primary font-size-xs">No. Resi : - </span>';
                                }
                                return txt;
                        },
                    },
                    {
                        field: 'nama_cabang',
                        title: 'Nama Ekspedisi',
                        width: 241,
                        textAlign: 'left',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bold">'+row.nama_ekspedisi+'</span>';
                                txt += "<br />";
                                txt += '<span class="label label-md label-outline-primary label-inline mt-1">' + row.nama_cabang +'</span>';
                                return txt;
                        },
                    },
                    {
                        field: 'total_jumlah',
                        title: 'Koli/Dus',
                        textAlign: 'right',
                        width: 'auto',
                        autoHide: true,
                        template: function(row) {
                                return parseFloat(row.total_jumlah).toLocaleString('id-ID', { maximumFractionDigits: 2});
                        },
                    },

                    {
                        field: 'total_berat',
                        title: 'Berat',
                        textAlign: 'right',
                        width: 'auto',
                        autoHide: true,
                        template: function(row) {
                                return parseFloat(row.Berat).toLocaleString('id-ID', { maximumFractionDigits: 2})
                        },
                    },
                    {
                        field: 'total_biaya',
                        title: 'Grand Total',
                        textAlign: 'right',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bold">'+parseFloat(row.total_biaya).toLocaleString('id-ID', { maximumFractionDigits: 2})+'</span><br />';
                                return txt;
                        },
                    },
                    {
                        field: 'tanggal_kirim',
                        title: 'Tanggal',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bold">'+formatDate(row.tanggal_kirim)+'</span>';
                                return txt;
                        },
                    },
                    {
                        field: 'status_biaya',
                        title: 'Status',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            var statusTxt = "";
                            if (row.flag_revisi == '1') {
                                statusTxt = ucwords(row.status_biaya)+"-R";
                            }
                            else {
                                statusTxt = ucwords(row.status_biaya);
                            }

                            if (row.status_biaya == "draft") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline">'+statusTxt+'</span>';
                            }
                            else if (row.status_biaya == "posted") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-primary">'+statusTxt+'</span>';
                            }
                            else if (row.status_biaya == "batal") {
                                statusTxt = '<span class="label label-md font-weight-bold label-pill label-inline label-danger">'+statusTxt+'</span>';
                            }

                            return statusTxt;
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
                            var txtAction = '<div class="dropdown dropdown-inline">';
                                txtAction += '<a href="#" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">';
                                txtAction += '<i class="la la-cog"></i>';
                                txtAction += '</a>';
                                txtAction += '<div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">';
                                txtAction += '<ul class="nav nav-hoverable flex-column">';

                                txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('ExpeditionCost.Detail', 'idCost')}}'><i class='nav-icon la la-eye'></i><span class='nav-text'>Detail</span></a></li>";
                                if (akses.edit == "Y" && row.status_biaya == "draft") {
                                    txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('ExpeditionCost.edit', 'idCost')}}'><i class='nav-icon la la-edit'></i><span class='nav-text'>Ubah</span></a></li>";
                                }
                                if (akses.delete) {
                                    // txtAction += "<li class='nav-item'><a class='nav-link' href='#' onclick='deleteData("+row.id+");return false;''><i class='nav-icon la la-trash'></i><span class='nav-text'>Hapus</span></a></li>";
                                }
                                if (row.no_resi == null) {
                                    txtAction += "<li class='nav-item'><a class='nav-link' href='#' onclick='btnKonfirmClick(idCost)'><i class='nav-icon la la-truck'></i><span class='nav-text'>Input Resi</span></a></li>";
                                }
                                txtAction += '</ul>';
                                txtAction += '</div>';
                                txtAction += '</div>';
                                txtAction = txtAction.replaceAll('idCost',row.id);
                                return txtAction;
                        },
                    }
                ],
            });

            $('#table_biaya_search_cabang').on('change', function() {
                datatable.search($(this).val(), 'nama_cabang');
            });

            $('#table_biaya_search_status').on('change', function() {
                datatable.search($(this).val(), 'status_biaya');
            });

            $("#bulan_picker").on('change', function() {
                var bulanDate = $("#bulan_picker").data('datepicker').getFormattedDate('yyyy-mm-dd');
                datatable.setDataSourceParam('periode', bulanDate);
                datatable.reload();
            });
        });

        function btnKonfirmClick(id) {
            $("#idSJ").val(id);
            $("#modal_form_resi").modal('toggle');
        }

        $("#btnKonfirmSave").on('click', function(e) {
            var id = $("#idSJ").val();
            var resi = $("#resi").val();
			Swal.fire({
                title: "Input No. Resi Ekspedisi",
                text: "Apakah No. Resi sudah benar?",
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
                        url: "/ExpeditionCost/InputResi",
                        method: 'POST',
                        dataType : 'json',
                        data: {
                            idCost : id,
                            resi : resi,
                        },
                        success: function(result){
                            if (result != "false") {
                                Swal.fire(
                                    "Sukses!",
                                    "Input No. Resi Berhasil!.",
                                    "success"
                                )
                                var datatable = $("#table_biaya").KTDatatable();
                                $('#modal_form_resi').modal('toggle');
                                datatable.reload();
                            }
                            else {
                                Swal.fire(
                                    "Gagal!",
                                    "Harap Masukkan No. Resi terlebih dahulu!.",
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
                        url: "/ExpeditionCost/Delete",
                        method: 'POST',
                        data: {
                            idExpeditionCost: id
                        },
                        success: function(result){
                            Swal.fire(
                                "Sukses!",
                                "Data Berhasil dihapus!.",
                                "success"
                            )
                        }
                    });
                    var datatable = $("#table_biaya").KTDatatable();
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
