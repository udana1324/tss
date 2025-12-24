@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
    <div class="content">
        <div class="card card-custom">
            <div class="card-header bg-primary header-elements-sm-inline">
                <div class="card-title">
                    <h3 class="card-label text-white">Daftar Menu</h3>
                </div>
                <div class="card-toolbar">
                    <!--begin::Button-->
                    @if($hakAkses->add == "Y")
                    <button class="btn btn-primary btn-outline-white mr-2" onclick="window.location.href = '{{ url('/Modules/Add') }}';">
                        <i class="flaticon2-plus"></i>
                        Tambah Menu
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
                        <div class="col-md-3">
                            <div class="input-icon">
                                <input type="text" class="form-control" placeholder="Search..." id="table_menu_search_query"/>
                                <span>
                                    <i class="flaticon2-search-1 text-muted"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <label class="mr-3">Status:</label>
                                <select class="form-control select2" id="table_menu_search_status">
                                    <option label="Label" value="">All</option>
                                    @foreach($arrayStatus as $rowStatusMenu)
                                    @if($rowStatusMenu == 'Active')
                                    <option value="Y">{{$rowStatusMenu}}</option>
                                    @else
                                    <option value="N">{{$rowStatusMenu}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <label class="mr-3">Parent Menu:</label>
                                <select class="form-control select2" id="table_menu_search_parent">
                                    <option label="Label" value="">All</option>
                                    @foreach($dataParent as $parentData)
                                    <option value="{{$parentData->id}}">{{ucwords($parentData->menu)}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Search Form-->
                <!--end: Search Form-->
                <!--begin: Datatable-->

                <div class="datatable datatable-bordered datatable-head-custom datatable-default" id="table_menu"></div>

                <!--end: Datatable-->
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">

        $(document).ready(function () {
            $('#table_menu_search_status').select2({
                placeholder: "Pilih Status",
                allowClear: true
            });

            $('#table_menu_search_parent').select2({
                placeholder: "Pilih Parent",
                allowClear: true
            });
        });

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
                            url: 'Modules/GetData',
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
                        type: 'number',
                        selector: false,
                        textAlign: 'center',
                        visible:false,
                    },
                    {
                        field: 'menu',
                        title: 'Menu',
                        template: function(row) {
                            return ucwords(row.menu);
                        },
                    },
                    {
                        field: 'url',
                        title: 'URL',
                    },
                    {

                        field: 'menu_name',
                        title: 'Parent',
                        template: function(row) {
                            if (row.menu_name == null) {
                                return "Main";
                            }
                            else {
                                return ucwords(row.menu_name);
                            }
                        },
                        autoHide: false,
                    },
                    {
                        field: 'order_number',
                        title: 'Nomor Urut',
                        textAlign: 'center',
                    },
                    {
                        field: 'parent',
                        title: 'Parent',
                        textAlign: 'center',
                        visible:false
                    },
                    {
                        field: 'menu_icon',
                        title: 'Menu Icon',
                        textAlign: 'center',
                        template: function(row) {
                            return '<i class="' + row.menu_icon + '"></i>';
                        },
                    },
                    {
                        field: 'active',
                        title: 'Status',
                        textAlign: 'center',
                        template: function(row) {
                            if (row.active == "Y") {
                                return '<span class="label label-rounded font-weight-bold label-lg label-light-success label-inline">Active</span>';
                            }
                            else {
                                return '<span class="label label-rounded font-weight-bold label-lg label-light-danger label-inline">Inactive</span>';
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
                                    txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('Modules.edit', 'idModule')}}'><i class='nav-icon la la-edit'></i><span class='nav-text'>Ubah</span></a></li>";
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
                    }
                ],
            });

            $('#table_menu_search_status').on('change', function() {
                datatable.search($(this).val(), 'active');
            });

            $('#table_menu_search_parent').on('change', function() {
                datatable.search($(this).val(), 'parent');
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
                        url: "/Modules/Delete",
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
                    var datatable = $("#table_menu").KTDatatable();
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
