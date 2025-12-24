@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
    <div class="content">
        <div class="card card-custom">
            <div class="card-header bg-primary header-elements-sm-inline">
                <div class="card-title">
                    <h3 class="card-label text-white">Daftar Pengguna</h3>
                </div>
                <div class="card-toolbar">
                    <!--begin::Button-->
                    @if($hakAkses->add == "Y")
                    <button class="btn btn-primary btn-outline-white mr-2" onclick="window.location.href = '{{ url('/Users/Add') }}';">
                        <i class="flaticon2-plus"></i>
                        Tambah Pengguna
                    </button>
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
                                        <input type="text" class="form-control" placeholder="Search..." id="table_users_search_query"/>
                                        <span>
                                            <i class="flaticon2-search-1 text-muted"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4 my-2 my-md-0">
                                    <div class="d-flex align-items-center">
                                        <label class="mr-3 mb-0 d-none d-md-block">Grup Pengguna:</label>
                                        <select class="form-control select2" id="table_users_search_group">
                                            <option value="">All</option>
                                            <option value="admin">Admin</option>
                                            <option value="penjualan">Penjualan</option>
                                            <option value="pembelian">Pembelian</option>
                                            <option value="gudang">Gudang</option>
                                            <option value="operasional">Operasional</option>
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

                <div class="datatable datatable-bordered datatable-head-custom" id="table_users"></div>

                <!--end: Datatable-->
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">

        $(document).ready(function () {
            $('#table_users_search_group').select2({
                allowClear: true
            });
        });
        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        $(document).ready(function() {

            var datatable = $('#table_users').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Users/GetData',
                            method: 'GET',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                        }
                    },
                    pageSize: 10,
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
                    input: $('#table_users_search_query')
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
                        field: 'user_name',
                        width: 'auto',
                        title: 'Username',
                        template: function(row) {
                            return ucwords(row.user_name);
                        },
                    },
                    {
                        field: 'user_group',
                        width: 'auto',
                        title: 'Grup Pengguna',
                        template: function(row) {
                            return ucwords(row.user_group);
                        },
                    },
                    {
                        field: 'nama_user',
                        width: 'auto',
                        title: 'Nama User',
                        template: function(row) {
                            return ucwords(row.nama_user);
                        },
                    },
                    {
                        field: 'telp_user',
                        width: 'auto',
                        title: 'Telp User',
                        textAlign: 'center',
                    },
                    {
                        field: 'email_user',
                        width: 'auto',
                        title: 'Email User',
                        textAlign: 'center',
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
                                txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('Users.Profile', 'idUsers')}}'><i class='nav-icon la la-user-circle'></i><span class='nav-text'>Detail Pengguna</span></a></li>";
                                if (akses.edit == "Y") {
                                    txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('Users.edit', 'idUsers')}}'><i class='nav-icon la la-edit'></i><span class='nav-text'>Ubah</span></a></li>";
                                    txtAction += "<li class='nav-item'><a class='nav-link' href='#' onclick='resetPassword("+row.id+");return false;''><i class='nav-icon la la-refresh'></i><span class='nav-text'>Reset Password</span></a></li>";
                                }
                                if (akses.delete) {
                                    txtAction += "<li class='nav-item'><a class='nav-link' href='#' onclick='deleteData("+row.id+");return false;''><i class='nav-icon la la-trash'></i><span class='nav-text'>Hapus</span></a></li>";
                                }
                                txtAction += '</ul>';
                                txtAction += '</div>';
                                txtAction += '</div>';
                                txtAction = txtAction.replaceAll('idUsers',row.id);
                                return txtAction;
                        },
                    }
                ],
            });

            $('#table_users_search_group').on('change', function() {
                datatable.search($(this).val(), 'user_group');
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
                        url: "/Users/Delete",
                        method: 'POST',
                        data: {
                            id: id
                        },
                        success: function(result){
                            Swal.fire(
                                "Sukses!",
                                "Data Berhasil dihapus!.",
                                "success"
                            )
                        }
                    });
                    var datatable = $("#table_users").KTDatatable();
                    datatable.reload();
                } else if (result.dismiss === "cancel") {
                    // Swal.fire(
                    //     "Cancelled",
                    //     "Your imaginary file is safe :)",
                    //     "error"
                    // )
                    e.preventDefault();
                }
            });
        }

        function resetPassword(id) {
            Swal.fire({
                title: "Reset?",
                text: "Apakah anda ingin mereset password?",
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
                        url: "/Users/ResetPassword",
                        method: 'POST',
                        data: {
                            id: id
                        },
                        success: function(result){
                            Swal.fire(
                                "Sukses!",
                                "Password berhasil direset!.",
                                "success"
                            )
                        }
                    });
                } else if (result.dismiss === "cancel") {
                    // Swal.fire(
                    //     "Cancelled",
                    //     "Your imaginary file is safe :)",
                    //     "error"
                    // )
                    e.preventDefault();
                }
            });
        }

    	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
