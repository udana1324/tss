@extends('layout.default')
@section('content')
	<!-- Content area -->
			<div class="content">
			@include('pages.alerts')
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
                        <div class="card-title">
                            <h3 class="card-label text-white">Daftar Pemasok (Supplier)</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            @if($hakAkses->add == "Y")
                            <button class="btn btn-primary btn-outline-white mr-2" onclick="window.location.href = '{{ url('Supplier/Add') }}';">
                                <i class="flaticon2-plus"></i>
                                Tambah Supplier
                            </button>
                            @endif
                            @if($hakAkses->export == "Y")
                            <button type="button" class="btn btn-success font-weight-bold mr-2" id="btnExport" onclick="ExportExcel('T','table_supplier','Supplier','xlsx');"> Export <i class="fas fa-file-excel"></i></button>
                            @endif
                            <!--end::Button-->
                        </div>
					</div>

					<div class="card-body">
                        <!--begin::Search Form-->
                        <div class="mb-7">
                            <div class="row align-items-center">
                                <div class="col-lg-9 col-xl-8">
                                    <div class="row align-items-center">
                                        <div class="col-md-4 my-2 my-md-0">
                                            <div class="align-items-center">
                                                <label style="display: inline-block;"></label>
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" placeholder="Search..." id="table_supplier_search_query"/>
                                                    <span>
                                                        <i class="flaticon2-search-1 text-muted"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 my-2 my-md-0">
                                            <div class="align-items-center">
                                                <label class="mr-3 mb-0 d-none d-md-block">Kota:</label>
                                                <select class="form-control select2" id="table_supplier_search_kota">
                                                    <option value="">All</option>
                                                    @foreach($dataKota as $rowKota)
                                                    <option value="{{$rowKota->kota}}">{{ucwords($rowKota->kota)}}</option>
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

                        <div class="datatable datatable-bordered datatable-head-custom" id="table_supplier"></div>

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
            $('#table_supplier_search_kota').select2({
                allowClear: true
            });
        });

		function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

		$(document).ready(function() {

            var datatable = $('#table_supplier').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/Supplier/GetData',
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
                    input: $('#table_supplier_search_query')
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
                        field: 'kode_supplier',
                        title: 'Kode Supplier',
                        textAlign: 'left',
                        width: 'auto',
                        autoHide: false,
                        template: function(row) {
                            var txt = "";
                                txt += '<span class="font-weight-bolder">'+row.nama_supplier.toUpperCase()+'</span>';
                                txt += "<br />";
                                txt += '<span class="label label-md label-outline-primary label-inline">NPWP : '+row.npwp_supplier.toUpperCase()+'</span>';
                                txt += "<br />";
                                txt += '<span class="label label-md label-outline-primary label-inline mt-1 mr-1">'+row.kode_supplier.toUpperCase()+'</span>';
                                txt += '<span class="label label-md label-outline-primary label-inline mt-1">'+row.nama_kategori+'</span>';
                                return txt;
                        },
                    },
                    {
                        field: 'telp_supplier',
                        title: 'No. Telp',
                        autoHide: false,
                        width: 'auto',
                    },
                    {
                        field: 'email_supplier',
                        title: 'Email',
                        autoHide: false,
                        width: 'auto',
                        template: function(row) {
                            if (row.email_supplier != null) {
                                return row.email_supplier.toLowerCase();
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'kota',
                        title: 'Kota',
                        textAlign: 'center',
                        width: 'auto',
                        template: function(row) {
                            return ucwords(row.kota);
                        },
                    },
                    {
                        field: 'fax_supplier',
                        title: 'No. Fax',
                        autoHide: true,
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
                                txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('Supplier.DetailBarang', 'idSupplier')}}'><i class='nav-icon la la-history'></i><span class='nav-text'>Detail Barang</span></a></li>";
                                txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('Supplier.Detail', 'idSupplier')}}'><i class='nav-icon la la-eye'></i><span class='nav-text'>Detail</span></a></li>";
                                if (akses.edit == "Y") {
                                    txtAction += "<li class='nav-item'><a class='nav-link' href='{{route('Supplier.edit', 'idSupplier')}}'><i class='nav-icon la la-edit'></i><span class='nav-text'>Ubah</span></a></li>";
                                }
                                if (akses.delete) {
                                    txtAction += "<li class='nav-item'><a class='nav-link' href='#' onclick='deleteData("+row.id+");return false;''><i class='nav-icon la la-trash'></i><span class='nav-text'>Hapus</span></a></li>";
                                }
                                txtAction += '</ul>';
                                txtAction += '</div>';
                                txtAction += '</div>';
                                txtAction = txtAction.replaceAll('idSupplier',row.id);
                                return txtAction;
                        },
                    }
                ],
            });

            $('#table_supplier_search_kota').on('change', function() {
                datatable.search($(this).val(), 'kota');
            });

            $('#table_supplier_search_kota').selectpicker();
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
                        url: "/Supplier/Delete",
                        method: 'POST',
                        data: {
                            id_supplier: id
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
                                    "Tidak dapat menghapus vendor karena sudah terdapat transaksi untuk vendor ini !",
                                    "warning"
                                )
                            }
                        }
                    });
                    var datatable = $("#table_supplier").KTDatatable();
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
