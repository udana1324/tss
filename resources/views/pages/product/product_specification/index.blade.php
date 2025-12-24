@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
                        <div class="card-title">
                            <h3 class="card-label text-white">Daftar Spesifikasi Barang</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            @if($hakAkses->add == "Y")
                            <button class="btn btn-primary font-weight-bolder mr-2" onclick="window.location.href = '{{ url('/ProductSpecification/Add') }}';">
                                <i class="flaticon2-plus"></i>
                                Tambah Spesifikasi
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
                                                <input type="text" class="form-control" placeholder="Search..." id="table_specification_search_query"/>
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

                        <div class="datatable datatable-bordered datatable-head-custom" id="table_specification"></div>

                        <!--end: Datatable-->
                    </div>

				</div>
				<!-- /basic initialization -->

			</div>
			<!-- /content area -->
@endsection
@section('scripts')
	<script type="text/javascript">

        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

		$(document).ready(function() {

            var datatable = $('#table_specification').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/ProductSpecification/GetData',
                            method: 'GET',

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
                    input: $('#table_specification_search_query')
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
                        field: 'kode_spesifikasi',
                        title: 'Kode Spesifikasi',
                        textAlign: 'center',
                        width: 'auto',
                        template: function(row) {
                            return row.kode_spesifikasi.toUpperCase();
                        },
                    },
                    {
                        field: 'nama_spesifikasi',
                        width: 'auto',
                        title: 'Nama Spesifikasi',
                        template: function(row) {
                            return ucwords(row.nama_spesifikasi);
                        },
                    },
                    {
                        field: 'flag_cetak',
                        width: 'auto',
                        title: 'Cetak Nama',
                        template: function(row) {
                            if (row.flag_cetak == "Y") {
                                return '<span class="label label-rounded font-weight-bold label-lg label-light-success label-inline">Ya</span>';
                            }
                            else {
                                return '<span class="label label-rounded font-weight-bold label-lg label-light-danger label-inline">Tidak</span>';
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
                            if (row.idCount == null) {
                                var txtAction = '<div class="dropdown dropdown-inline">';
                                    txtAction += '<a href="#" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">';
                                    txtAction += '<i class="la la-cog"></i>';
                                    txtAction += '</a>';
                                    txtAction += '<div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">';

                                        txtAction += '<ul class="nav nav-hoverable flex-column">';
                                        if (akses.edit == "Y") {
                                            txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('ProductSpecification.edit', 'idProductSpecification')}}'><i class='nav-icon la la-edit'></i><span class='nav-text'>Ubah</span></a></li>";
                                        }
                                        if (akses.delete) {
                                            txtAction += "<li class='nav-item'><a class='nav-link' href='#' onclick='deleteData("+row.id+");return false;''><i class='nav-icon la la-trash'></i><span class='nav-text'>Hapus</span></a></li>";
                                        }
                                        txtAction += '</ul>';

                                    txtAction += '</div>';
                                    txtAction += '</div>';
                                    txtAction = txtAction.replace('idProductSpecification',row.id);
                                    return txtAction;
                            }
                            else {
                                return "-";
                            }
                        },
                    }
                ],
            });

            $('#table_specification_search_status').on('change', function() {
                datatable.search($(this).val(), 'active');
            });

            $('#table_specification_search_status').selectpicker();
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
                        url: "/ProductSpecification/Delete",
                        method: 'POST',
                        data: {
                            id_spesifikasi: id
                        },
                        success: function(result){
                            Swal.fire(
                                "Sukses!",
                                "Data Berhasil dihapus!.",
                                "success"
                            )
                        }
                    });
                    var datatable = $("#table_specification").KTDatatable();
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

         //$('div.alert').delay(5000).slideUp(300);
    </script>
@endsection
