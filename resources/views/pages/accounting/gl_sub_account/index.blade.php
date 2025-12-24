@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
    <div class="content">
        <div class="card card-custom">
            <div class="card-header bg-primary header-elements-sm-inline">
                <div class="card-title">
                    <h3 class="card-label text-white">Daftar Sub Account</h3>
                </div>
                <div class="card-toolbar">
                    <!--begin::Button-->
                    @if($hakAkses->add == "Y")
                    <button class="btn btn-primary font-weight-bolder mr-2" onclick="window.location.href = '{{ url('/GLSubAccount/Add') }}';">
                        <i class="flaticon2-plus"></i>
                        Tambah Sub Account
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
                        <div class="col-lg-12">
                            <div class="row align-items-center">
                                <div class="col-md-4 my-2 my-md-0">
                                    <div class="align-items-center">
                                        <label style="display: inline-block;"></label>
                                        <div class="input-icon">
                                            <input type="text" class="form-control" placeholder="Search..." id="table_account_search_query"/>
                                            <span>
                                                <i class="flaticon2-search-1 text-muted"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 my-2 my-md-0">
                                    <div class="align-items-center">
                                        <label class="mr-3 mb-0">Mother Account :</label>
                                        <select class="form-control select2" id="table_account_search_mother">
                                            <option label="Label" value="">All</option>
                                            @foreach($motherAccount as $account)
                                            <option value="{{$account->id}}">{{ucwords($account->account_number)}} - {{ucwords($account->account_name)}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 my-2 my-md-0">
                                    <div class="align-items-center">
                                        <label class="mr-3 mb-0">Account :</label>
                                        <select class="form-control select2" id="table_account_search_account">
                                            <option label="Label" value="">All</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Search Form-->
                <!--begin: Datatable-->

                <div class="datatable datatable-bordered datatable-head-custom datatable-default" id="table_account"></div>

                <!--end: Datatable-->
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">

        $(document).ready(function () {
            $('#table_account_search_mother').select2({
                placeholder: "Pilih Mother Account",
                allowClear: true
            });

            $('#table_account_search_account').select2({
                placeholder: "Pilih Account",
                allowClear: true
            });
        });

        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        $(document).ready(function() {

            var datatable = $('#table_account').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: 'GLSubAccount/GetData',
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
                    input: $('#table_account_search_query')
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
                        title: 'Sub Account Number',
                        autoHide: false,
                        template: function(row) {
                            return row.account_number;
                        },
                    },
                    {
                        field: 'account_name',
                        width: 'auto',
                        title: 'Sub Account Name',
                        autoHide: false,
                        template: function(row) {
                            return ucwords(row.account_name);
                        },
                    },
                    {
                        field: 'id_mother_account',
                        title: 'Mother Account',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            if (row.id_mother_account == null) {
                                return "-";
                            }
                            else {
                                return ucwords(row.maccount_number) + " - " + ucwords(row.maccount_name);
                            }
                        },
                    },
                    {
                        field: 'id_account',
                        title: 'Account',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            if (row.id_account == null) {
                                return "-";
                            }
                            else {
                                return ucwords(row.paccount_number) + " - " + ucwords(row.paccount_name);
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
                                    txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('GLSubAccount.edit', 'idModule')}}'><i class='nav-icon la la-edit'></i><span class='nav-text'>Ubah</span></a></li>";
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

            $('#table_account_search_mother').on('change', function() {
                getAccount($(this).val());
                datatable.search($(this).val(), 'id_mother_account');
            });

            $('#table_account_search_account').on('change', function() {
                datatable.search($(this).val(), 'id_account');
            });
        });

        function getAccount(idMother) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "/GLSubAccount/GetParentAccounts",
                method: 'POST',
                data: {
                    idMotherAccount: idMother,
                },
                success: function(result){
                    $('#table_account_search_account').find('option:not(:first)').remove();
                    if (result.length > 0) {
                        for (var i = 0; i < result.length;i++) {
                            var account = result[i].account_number+' - '+ucwords(result[i].account_name);

                            $("#table_account_search_account").append($('<option>', {
                                value:result[i].id,
                                text:account
                            }));
                        }
                    }
                }
            });
        }

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
                        url: "/GLSubAccount/Delete",
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
                    var datatable = $("#table_account").KTDatatable();
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
