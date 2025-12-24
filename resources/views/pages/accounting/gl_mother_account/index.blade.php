@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
    <div class="content">
        <div class="card card-custom">
            <div class="card-header bg-primary header-elements-sm-inline">
                <div class="card-title">
                    <h3 class="card-label text-white">Daftar Mother Account</h3>
                </div>
                <div class="card-toolbar">
                    <!--begin::Button-->
                    @if($hakAkses->add == "Y")
                    <button class="btn btn-primary font-weight-bolder mr-2" onclick="window.location.href = '{{ url('/GLMotherAccount/Add') }}';">
                        <i class="flaticon2-plus"></i>
                        Tambah Mother Account
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
                                        <input type="text" class="form-control" placeholder="Search..." id="table_mother_account_search_query"/>
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
                <!--begin: Datatable-->

                <div class="datatable datatable-bordered datatable-head-custom datatable-default" id="table_mother_account"></div>

                <!--end: Datatable-->
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">

        $(document).ready(function () {

        });

        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        $(document).ready(function() {

            var datatable = $('#table_mother_account').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: 'GLMotherAccount/GetData',
                            method: 'GET',

                        }
                    },
                    pageSize: 10,
                    serverPaging: true,
                    serverFiltering: false,
                    serverSorting: false,
                    saveState: false,
                    autoHide: false
                },

                layout: {
                    scroll: false,
                    height: 'auto',
                    footer: false
                },

                sortable: true,

                filterable: true,

                pagination: true,

                search: {
                    input: $('#table_mother_account_search_query')
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
                        field: 'account_number',
                        width: 'auto',
                        title: 'Mother Account Number',
                        autoHide: false,
                        template: function(row) {
                            return row.account_number;
                        },
                    },
                    {
                        field: 'account_name',
                        width: 'auto',
                        title: 'Mother Account Name',
                        autoHide: false,
                        template: function(row) {
                            return ucwords(row.account_name);
                        },
                    },
                    {
                        field: 'default_side',
                        title: 'Saldo Normal',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: false,
                        template: function(row) {
                            return ucwords(row.default_side);
                        },
                    },
                    {
                        field: 'group',
                        title: 'Group',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            if (row.group == "A") {
                                return "Aktiva";
                            }
                            else if(row.group == "L") {
                                return "Liabilitas dan Ekuitas";
                            }
                            else if(row.group == "I") {
                                return "Pendapatan";
                            }
                            else if(row.group == "C") {
                                return "Biaya";
                            }
                            else {
                                return "-";
                            }
                        },
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
                                if (akses.edit == "Y") {
                                    txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('GLMotherAccount.edit', 'idModule')}}'><i class='nav-icon la la-edit'></i><span class='nav-text'>Ubah</span></a></li>";
                                }
                                if (akses.delete) {
                                    txtAction += "<li class='nav-item'><a class='nav-link' href='#' onclick='deleteData("+row.id+");return false;''><i class='nav-icon la la-trash'></i><span class='nav-text'>Hapus</span></a></li>";
                                }
                                txtAction += '</ul>';
                                txtAction += '</div>';
                                txtAction += '</div>';
                                txtAction = txtAction.replace('idModule',row.id);
                                return txtAction;
                        },
                    }],
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
                        url: "/GLMotherAccount/Delete",
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
                    var datatable = $("#table_mother_account").KTDatatable();
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

	//$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
