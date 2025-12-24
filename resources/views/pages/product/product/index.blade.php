@extends('layout.default')
@section('content')
	<!-- Content area -->
	@include('pages.alerts')
			<div class="content">
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
						<div class="card-title">
                            <h3 class="card-label text-white">Daftar Barang</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            @if($hakAkses->add == "Y")
                            <button class="btn btn-primary btn-outline-white mr-2" onclick="window.location.href = '{{ url('/Product/Add') }}';">
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

                    <div class="card-body" id="TableProduct">
                        <form action="{{ route('Product.Export') }}" class="form-horizontal" id="form_add" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                        </form>
                        <form action="{{ route('Product.Export') }}" class="form-horizontal" id="form_add" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                        </form>
                        <!--begin: Datatable-->
                        <table class="table table-separate table-head-custom table-checkable" id="table_product">
                            <thead>
                                <tr>
                                    <th>Foto Item</th>

                                    <th>NAMA BARANG</th>

                                    <th>KATEGORI</th>

                                    <th>MERK</th>

                                    <th>JENIS</th>

                                    <th>Aksi</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dataProduct as $data)
                                <tr>
                                    @if($data->product_image_path != "")
                                    <td><img src="{{ url('images/products/'.$data->product_image_path) }}" class="image" width="100px" height="100px" alt="" /></td>
                                    @else
                                    <td><img src="images/img-preview.jpg" class="image" width="100px" height="100px" alt="" /></td>
                                    @endif
                                    <td>
                                        <span class="font-weight-bold">{{ucwords($data->nama_item)}}</span><br>
                                        <span class="label label-rounded label-outline-primary label-inline mt-1">{{ $data->value_spesifikasi != null ? '('.$data->value_spesifikasi.')' : "" }}{{strtoupper($data->kode_item)}}</span>
                                    </td>
                                    <td>
                                        <span class="font-weight-bold">{{ucwords($data->nama_kategori)}}</span><br>
                                    </td>
                                    <td>
                                        <span class="font-weight-bold">{{ucwords($data->nama_merk)}}</span><br>
                                    </td>
                                    <td>
                                        <span class="font-weight-bold">{{ucwords($data->jenis_item)}}</span><br>
                                    </td>
                                    <td style="text-align:center;">
                                        <div class="dropdown dropdown-inline">
                                            <a href="javascript:;" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown">
                                                <i class="la la-cog"></i>
                                            </a>
                                              <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                                <ul class="nav nav-hoverable flex-column">
                                                    <li class='nav-item'><a class='nav-link' href='{{route('Product.History', $data->id)}}'><i class='nav-icon la la-history'></i><span class='nav-text'>Riwayat</span></a></li>
                                                    <li class='nav-item'><a class='nav-link' href='{{route('Product.Detail', $data->id)}}'><i class='nav-icon la la-eye'></i><span class='nav-text'>Detail</span></a></li>
                                                    @if($hakAkses->edit == "Y")
                                                    <li class='nav-item'><a class='nav-link' href='{{route('Product.edit', $data->id)}}'><i class='nav-icon la la-edit'></i><span class='nav-text'>Ubah</span></a></li>
                                                    @endif
                								    @if($hakAkses->delete == "Y")
                                                    <li class='nav-item'><a class='nav-link' href='#' onclick='deleteData({{$data->id}});return false;'><i class='nav-icon la la-trash'></i><span class='nav-text'>Hapus</span></a></li>
                                                    @endif
                                                </ul>
                                              </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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

        $(document).ready(function () {

            $("#tanggal_picker").daterangepicker({
                locale : {
                    format : 'DD MMM YYYY',
                    cancelLabel: 'Clear'
                }
            },
            function(start, end, label) {
                $("#tanggal_picker_start").val(start.format('YYYY-MM-DD'));
                $("#tanggal_picker_end").val(end.format('YYYY-MM-DD'));
            });

            $('#tanggal_picker').on('cancel.daterangepicker', function(ev, picker) {
                $('#tanggal_picker_start').val('');
                $('#tanggal_picker_end').val('');
                $('#tanggal_picker').val('');
            });
        });

        $(document).ready(function() {
            var table = $('#table_product');

            // begin first table
            table.DataTable({
                responsive: true,
                columnDefs: [
                    {
                        responsivePriority: 4,
                        targets: -1,
                        title: 'Aksi',
                        orderable: false,
                    },
                ],
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
                        url: "/Product/Delete",
                        method: 'POST',
                        data: {
                            id_product: id
                        },
                        success: function(result){
                            if (result == "success") {
                                Swal.fire({
                                    title: "Sukses!!",
                                    text: "Data Berhasil dihapus!",
                                    timer: 1000,
                                    onOpen: function() {
                                        Swal.showLoading()
                                    }
                                });
                                window.location.reload();
                            }
                            else if (result == "failUsed") {
                                Swal.fire(
                                    "Gagal!",
                                    "Tidak dapat menghapus barang karena sudah terdapat transaksi untuk barang ini !",
                                    "warning"
                                )
                            }
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

        // function test() {
        //     var datatable = $("#table_product").KTDatatable();
        //         datatable.column('harga_jual').visible(true);

        // }

        $(document).ready(function () {
            $("#btnExport").on('click', function(e) {
                var errCount = 0;

                if (errCount == 0) {
                    Swal.fire({
                        title: "Export Data?",
                        //text: "Apakah data sudah sesuai?",
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
         //$('div.alert').delay(5000).slideUp(300);
      </script>
@endsection
