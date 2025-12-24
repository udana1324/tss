@extends('layout.default')
@section('content')
	<!-- Content area -->
			<div class="content">
			@include('pages.alerts')
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
                        <div class="card-title">
                            <h3 class="card-label text-white">Daftar Pelanggan</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            @if($hakAkses->add == "Y")
                            <button class="btn btn-primary btn-outline-white mr-2" onclick="window.location.href = '{{ url('Customer/Add') }}';">
                                <i class="flaticon2-plus"></i>
                                Tambah Pelanggan
                            </button>
                            @endif
                            @if($hakAkses->export == "Y")
                            <button type="button" class="btn btn-success font-weight-bold mr-2" id="btnExport" onclick="ExportExcel('T','table_customer','Customer','xlsx');"> Export <i class="fas fa-file-excel"></i></button>
                            @endif
                            <!--end::Button-->
                        </div>
					</div>

					<div class="card-body">
                        <!--begin: Search Form-->
                        <!--begin::Search Form-->
                        <div class="mb-7">
                            <div class="row align-items-center">
                                <div class="col-lg-9 col-xl-8">
                                    <div class="row align-items-center">
                                        <div class="col-md-4 my-2 my-md-0">
                                            <div class="input-icon">
                                                <input type="text" class="form-control" placeholder="Search..." id="table_customer_search_query"/>
                                                <span>
                                                    <i class="flaticon2-search-1 text-muted"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-4 my-2 my-md-0">
                                            <div class="d-flex align-items-center">
                                                <label class="mr-3 mb-0 d-none d-md-block">Kota:</label>
                                                <select class="form-control select2" id="table_customer_search_kota">
                                                    <option value="">All</option>
                                                    @foreach($dataKota as $rowKota)
                                                    <option value="{{$rowKota->kota}}">{{ucwords($rowKota->kota)}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 my-2 my-md-0">
                                            <div class="d-flex align-items-center">
                                                <label class="mr-3 mb-0 d-none d-md-block">Kategori:</label>
                                                <select class="form-control select2" id="table_customer_search_kategori">
                                                    <option value="">All</option>
                                                    @foreach($dataKategori as $rowKategori)
                                                    <option value="{{$rowKategori->nama_kategori}}">{{ucwords($rowKategori->nama_kategori)}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Search Form-->
                        <!--end: Search Form-->
                        <!--begin: Datatable-->

                        <div class="datatable datatable-bordered datatable-head-custom" id="table_customer"></div>

                        <!--end: Datatable-->
                    </div>
				</div>
				<!-- /basic initialization -->
			</div>
			<!-- /content area -->
@endsection
@section('scripts')
	<script type="text/javascript">
		//$('div.alert').delay(5000).slideUp(300);
        $(document).ready(function () {
            $('#table_customer_search_kota').select2({
                allowClear: true
            });
        });

		function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

		$(document).ready(function() {

            var datatable = $('#table_customer').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Customer/GetData',
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
                    input: $('#table_customer_search_query')
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
                        textAlign: 'left',
                        width: 180,
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bolder">'+row.nama_customer.toUpperCase()+'</span>';
                                txt += "<br />";
                                txt += '<span class="label label-md label-outline-primary label-inline mt-1 mr-1">'+row.kode_customer.toUpperCase()+'</span>';
                                txt += '<span class="label label-md label-outline-primary label-inline mt-1">'+row.nama_kategori+'</span>';
                                return txt;
                        },
                    },
                    {
                        field: 'list_outlets',
                        title: 'Outlet',
                        textAlign: 'left',
                        width: 180,
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                            if (row.list_outlets != "") {
                                if (row.list_outlets.includes(",")) {

                                    var outlets = row.list_outlets.split(',');

                                    for (i = 0; i < outlets.length; i++) {
                                        txt += '<span class="label label-md label-outline-primary label-inline mt-1 mr-1">'+outlets[i]+'</span>';
                                    }
                                }
                                else {
                                    txt += '<span class="label label-md label-outline-primary label-inline mt-1 mr-1">'+row.list_outlets.toUpperCase()+'</span>';
                                }
                            }

                            return txt;
                        },
                    },
                    {
                        field: 'telp_customer',
                        title: 'No. Telp',
                        width: 'auto',
                        autoHide: true,
                    },
                    {
                        field: 'kota',
                        title: 'Kota',
                        width: 'auto',
                        autoHide: true,
                        textAlign: 'left',
                        template: function(row) {
                            return ucwords(row.kota);
                        },
                    },
                    {
                        field: 'email_customer',
                        title: 'Email',
                        width: 'auto',
                        autoHide: true,
                        template: function(row) {
                            if (row.email_customer != null) {
                                return row.email_customer.toLowerCase();
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'jenis_customer',
                        title: 'Jenis Customer',
                        width: 'auto',
                        autoHide: true,
                        template: function(row) {
                            var txt = "";
                            if (row.jenis_customer == "C") {
                                txt += '<span class="font-weight-bolder">Perusahaan</span>';
                                txt += "<br />";
                                txt += '<span class="label label-md label-outline-primary label-inline mt-1">NPWP : '+row.npwp_customer+'</span>';
                            }
                            else if (row.jenis_customer == "I") {
                                txt += '<span class="font-weight-bolder">Individual</span>';
                                txt += "<br />";
                                txt += '<span class="label label-md label-outline-primary label-inline mt-1">KTP : '+row.ktp_customer+'</span>';
                            }

                            return txt;
                        },
                    },
                    {
                        field: 'fax_customer',
                        title: 'No. Fax',
                        width: 'auto',
                        autoHide: true,
                    },
                    {
                        field: 'Actions',
                        title: 'Aksi',
                        sortable: false,
                        width: 110,
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
                                txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('Customer.DetailBarang', 'idCustomer')}}'><i class='nav-icon la la-history'></i><span class='nav-text'>Histori Transaksi</span></a></li>";
                                // txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('Customer.History', 'idCustomer')}}'><i class='nav-icon la la-history'></i><span class='nav-text'>Riwayat</span></a></li>";
                                txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('Customer.Detail', 'idCustomer')}}'><i class='nav-icon la la-eye'></i><span class='nav-text'>Detail</span></a></li>";
                                if (akses.edit == "Y") {
                                    txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('Customer.edit', 'idCustomer')}}'><i class='nav-icon la la-edit'></i><span class='nav-text'>Ubah</span></a></li>";
                                }
                                if (akses.delete) {
                                    txtAction += "<li class='nav-item'><a class='nav-link' href='#' onclick='deleteData("+row.id+");return false;''><i class='nav-icon la la-trash'></i><span class='nav-text'>Hapus</span></a></li>";
                                }
                                txtAction += '</ul>';
                                txtAction += '</div>';
                                txtAction += '</div>';
                                txtAction = txtAction.replaceAll('idCustomer',row.id);
                                return txtAction;
                        },
                    }
                ],
            });

            $('#table_customer_search_kota').on('change', function() {
                datatable.search($(this).val(), 'kota');
            });

            $('#table_customer_search_kategori').on('change', function() {
                datatable.search($(this).val(), 'nama_kategori');
            });

            $('#table_customer_search_kota').selectpicker();
            $('#table_customer_search_kategori').selectpicker();

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
                        url: "/Customer/Delete",
                        method: 'POST',
                        data: {
                            idCustomer: id
                        },
                        success: function(result){
                            if (result == "success") {
                                Swal.fire(
                                    "Sukses!",
                                    "Data Berhasil dihapus!.",
                                    "success"
                                )
                            }
                            else if (result == "failUsed") {
                                Swal.fire(
                                    "Gagal!",
                                    "Tidak dapat menghapus pelanggan karena sudah terdapat transaksi untuk pelanggan ini !",
                                    "warning"
                                )
                            }
                        }
                    });
                    var datatable = $("#table_customer").KTDatatable();
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
