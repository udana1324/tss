@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
						<div class="card-title">
                            <h3 class="card-label text-white">Daftar Preferensi</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            @if($hakAkses->add == "Y")
                            <button class="btn btn-primary btn-outline-white mr-2" onclick="window.location.href = '{{ url('/Preference/Add') }}';">
                                <i class="flaticon2-plus"></i>
                                Tambah Preferensi
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
                                                <input type="text" class="form-control" placeholder="Search..." id="table_preference_search_query"/>
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

                        <div class="datatable datatable-bordered datatable-head-custom" id="table_preference"></div>

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

            var datatable = $('#table_preference').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: 'Preference/GetData',
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
                    input: $('#table_preference_search_query')
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
                        field: 'nama_pt',
                        title: 'Nama PT',
                        width: 'auto',
                        template: function(row) {
                            return row.nama_pt.toUpperCase();
                        },
                    },
                    {
                        field: 'alamat_pt',
                        width: 'auto',
                        title: 'Alamat',
                        template: function(row) {
                            return ucwords(row.alamat_pt);
                        },
                    },
                    {
                        field: 'kelurahan_pt',
                        title: 'Kelurahan',
                        width: 'auto',
                        template: function(row) {
                            return ucwords(row.kelurahan_pt);
                        },
                    },
                    {
                        field: 'kecamatan_pt',
                        title: 'Kecamatan',
                        width: 'auto',
                        template: function(row) {
                            return ucwords(row.kecamatan_pt);
                        },
                    },
                    {
                        field: 'kota_pt',
                        width: 'auto',
                        title: 'Kota',
                        template: function(row) {
                            return ucwords(row.kota_pt);
                        },
                    },
                    {
                        field: 'npwp_pt',
                        title: 'NPWP',
                        width: 'auto',
                        textAlign: 'center',
                        template: function(row) {
                            return ucwords(row.npwp_pt);
                        },
                    },
                    {
                        field: 'txtRekening',
                        title: 'No. Rekening',
                        width: 'auto',
                        template: function(row) {
                            return row.txtRekening.toUpperCase();
                        },
                    },
                    {
                        field: 'atas_nama',
                        title: 'Atas Nama',
                        width: 'auto',
                        template: function(row) {
                            return ucwords(row.atas_nama);
                        },
                    },
                    {
                        field: 'telp_pt',
                        title: 'Telp.',
                        width: 'auto',
                        textAlign: 'center',
                    },
                    {
                        field: 'email_pt',
                        width: 'auto',
                        title: 'Email',
                    },
                    {
                        field: 'website_pt',
                        title: 'Website',
                        width: 'auto',
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
                                txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('Preference.Detail', 'idPreference')}}'><i class='nav-icon la la-eye'></i><span class='nav-text'>Detail</span></a></li>";
                                if (akses.edit == "Y") {
                                    txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('Preference.edit', 'idPreference')}}'><i class='nav-icon la la-edit'></i><span class='nav-text'>Ubah</span></a></li>";
                                }
                                if (akses.delete == "Y") {
                                txtAction += "<li class='nav-item'><a class='nav-link' href='#' onclick='deleteData("+row.id+");return false;''><i class='nav-icon la la-trash'></i><span class='nav-text'>Hapus</span></a></li>";
                                }
                                txtAction += '</ul>';
                                txtAction += '</div>';
                                txtAction += '</div>';
                                txtAction = txtAction.replaceAll('idPreference',row.id);
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
                        url: "/Preference/Delete",
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
                    var datatable = $("#table_preference").KTDatatable();
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
