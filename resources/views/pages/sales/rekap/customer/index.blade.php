@extends('layout.default')
@section('content')
	<!-- Content area -->
			<div class="content">
			@include('pages.alerts')
				<!-- Basic initialization -->
				<div class="card card-custom">
					<div class="card-header bg-primary header-elements-sm-inline">
                        <div class="card-title">
                            <h3 class="card-label text-white">Ringkasan Penjualan berdasarkan Customer</h3>
                        </div>
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            @if($hakAkses->export == "Y")
                            <button type="button" class="btn btn-success font-weight-bold mr-2" id="btnExport" onclick="ExportExcel('F','table_cust','RekapCustomer','xlsx');"> Export <i class="fas fa-file-excel"></i></button>
                            @endif
                            <!--end::Button-->
                        </div>
					</div>

                    <div class="card-body">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-6">
                                <fieldset>

                                    <div class="form-group row" id="divHarian">
                                        <label class="col-lg-3 col-form-label">Tanggal :</label>
                                        <div class="col-lg-9">
                                            <input type="hidden" class="form-control" id="tanggal_picker_start" name="tanggal_picker_start" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}">
                                            <input type="hidden" class="form-control" id="tanggal_picker_end" name="tanggal_picker_end" value="{{\Carbon\Carbon::now()->format('Y-m-d')}}">
                                            <input type="text" class="form-control" id="tanggal_picker" name="tanggal_picker" >
                                            <span class="form-text text-danger errTanggal" style="display:none;">*Harap pilih tanggal terlebih dahulu!</span>
                                        </div>
                                    </div>

                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset>

                                    <div class="form-group row">
                                        <div class="col-lg-12 text-left">
                                            <button type="button" class="btn btn-primary font-weight-bold" id="btnSearch"><i class="flaticon-search"></i>Cari</button>
                                        </div>
                                    </div>

                                </fieldset>
                            </div>

                        </div>

                    </div>

				</div>
                <div class="card card-custom" style="display: none;" id="cardReport" >

                    <div class="card-body">

                        <!--begin::Search Form-->
                        <div class="mb-7">
                            <div class="row align-items-center">
                                <div class="col-lg-12 col-xl-8">
                                    <div class="row align-items-center">
                                        <div class="col-md-3 my-2 my-md-0">
                                            <div class="align-items-center">
                                                <label style="display: inline-block;"></label>
                                                <div class="input-icon">
                                                    <input type="text" class="form-control" placeholder="Search..." id="table_cust_search_query"/>
                                                    <span>
                                                        <i class="flaticon2-search-1 text-muted"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 my-2 my-md-0">
                                            <div class="align-items-center">
                                                <label class="mr-3 mb-0 d-md-block">Nama Customer :</label>
                                                <select class="form-control select2" id="table_cust_search_cust" style="width: 250px;">
                                                    <option value="">All</option>
                                                    @foreach($dataCustomer as $rowCustomer)
                                                    <option value="{{$rowCustomer->kode_customer}}">{{strtoupper($rowCustomer->kode_customer.' - '.$rowCustomer->nama_customer)}}</option>
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

                        <div class="datatable datatable-bordered datatable-head-custom" style="overflow-y: hidden !important;" id="table_cust"></div>

                        <!--end: Datatable-->

                        <!-- Modal form detail sales -->
                        <div id="modal_detail_sales" class="modal fade">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">

                                        <h5 class="modal-title" id="txtNama"></h5>
                                        <br>
                                        <h6 class="modal-title" id="txtTgl"></h6>
                                    </div>
                                    <div class="modal-body">
                                        <!--begin: Datatable-->

                                        <div class="datatable datatable-bordered datatable-head-custom" id="list_item_detail"></div>

                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-link" data-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /form detail delivery -->
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
            $('#table_detail_search_customer').select2({
                allowClear: true
            });

            $('#table_cust_search_cust').select2({
                allowClear: true
            });

            $('#table_cust_search_status').select2({
                allowClear: true
            });

            $("#tanggal_picker").daterangepicker({
                locale : {
                    format : 'DD MMM YYYY',
                    cancelLabel: 'Clear'
                }
            },
            function(start, end, label) {
                $("#txtTgl").text(start.format('DD MMM YYYY') + ' s.d ' + end.format('DD MMM YYYY'));
                $("#tanggal_picker_start").val(start.format('YYYY-MM-DD'));
                $("#tanggal_picker_end").val(end.format('YYYY-MM-DD'));
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

            var datatable = $('#table_cust').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/RekapPenjualanCustomer/GetDetail',
                            method: 'POST',
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
                    scroll: false,
                    height: 650,
                    footer: false
                },

                rows: {
                    autoHide: false
                },

                sortable: true,

                filterable: true,

                pagination: true,

                search: {
                    input: $('#table_cust_search_query')
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
                        field: 'kode_customer',
                        title: 'Nama Customer',
                        width: 160,
                        textAlign: 'left',
                        autoHide: false,
                        template: function(row) {
                            if (parseInt(row.JmlSO) == 0 || row.JmlSO == null) {
                                return row.nama_customer.toUpperCase() + "<br />" + row.kode_customer.toUpperCase() + " " + row.nama_sales;
                            }
                            else {
                                var sales = "";
                                if (row.nama_sales != null) {
                                    sales = row.nama_sales;
                                }
                                var txtAction = "<a href='#' data-toggle='modal' data-target='#modal_detail_sales' title='Detail' onclick='viewDetailItem(" + row.id + ");return false;'>";
                                    txtAction += row.nama_customer.toUpperCase();
                                    txtAction += "</a>";
                                    txtAction += "<br />" + row.kode_customer.toUpperCase() + " " + sales;
                                return txtAction;
                            }
                        },
                    },
                    {
                        field: 'txtKode',
                        title: 'Txt Barang',
                        width: 'auto',
                        autoHide: false,
                        textAlign: 'center',
                        visible:false,
                        template: function(row) {
                            return row.kode_customer.toUpperCase() + "<span id='txt_"+row.id+"'>"+row.nama_customer.toUpperCase()+"</span>";
                        },
                    },
                    {
                        field: 'JmlSO',
                        width: 'auto',
                        title: 'Jumlah SO',
                        textAlign: 'center',
                        autoHide: true,
                        template: function(row) {
                            if (row.JmlSO != null) {
                                return parseFloat(row.JmlSO).toLocaleString('id-ID', { maximumFractionDigits: 2});
                            }
                            else {
                                return '0';
                            }
                        },
                    },
                    {
                        field: 'JmlSOTerkirim',
                        width: 'auto',
                        title: 'Terkirim',
                        textAlign: 'center',
                        autoHide: true,
                        template: function(row) {
                            if (row.JmlSOTerkirim != null) {
                                return parseFloat(row.JmlSOTerkirim).toLocaleString('id-ID', { maximumFractionDigits: 2});
                            }
                            else {
                                return '0';
                            }
                        },
                    },
                    {
                        field: 'JmlSOFull',
                        title: 'Terkirim Lengkap',
                        width: 'auto',
                        textAlign: 'center',
                        autoHide: true,
                        template: function(row) {
                            if (row.JmlSOFull != null) {
                                return parseFloat(row.JmlSOFull).toLocaleString('id-ID', { maximumFractionDigits: 2});
                            }
                            else {
                                return '0';
                            }
                        },
                    },
                    {
                        field: 'JmlInv',
                        title: 'Penagihan',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: true,
                        template: function(row) {
                            if (row.JmlInv != null) {
                                return parseFloat(row.JmlInv).toLocaleString('id-ID', { maximumFractionDigits: 2});
                            }
                            else {
                                return '0';
                            }
                        },
                    },
                    {
                        field: 'JmlInvLunas',
                        title: 'Terbayarkan',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: true,
                        template: function(row) {
                            if (row.JmlInvLunas != null) {
                                return parseFloat(row.JmlInvLunas).toLocaleString('id-ID', { maximumFractionDigits: 2});
                            }
                            else {
                                return '0';
                            }
                        },
                    },
                ],
            });

            $('#table_cust_search_cust').on('change', function() {
                datatable.search($(this).val(), 'kode_customer');
            });
        });

        $(document).ready(function() {

            var datatable = $('#list_item_detail').KTDatatable({
                data: {
                    type: 'remote',
                    source: {
                        read: {
                            url: '/RekapPenjualanCustomer/GetDetailCustomer',
                            method: 'POST',
                            headers : {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },

                        }
                    },
                    pageSize: 100,
                    serverPaging: false,
                    serverFiltering: false,
                    serverSorting: false,
                    saveState: false
                },

                layout: {
                    scroll: false,
                    footer: false
                },

                sortable: true,

                filterable: true,

                pagination: false,

                rows: {
                    autoHide: false
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
                        field: 'no_so',
                        title: 'Nomor SO',
                        autoHide: false,
                        width: 100,
                        textAlign: 'center',
                        template: function(row) {
                            if (row.no_so != null) {
                                return row.no_so.toUpperCase();
                            }
                        },
                    },
                    {
                        field: 'nama_outlet',
                        title: 'Outlet',
                        textAlign: 'center',
                        width: 100,
                        autoHide: true,
                        template: function(row) {
                            if (row.nama_outlet != null) {
                                return row.nama_outlet.toUpperCase();
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'tanggal_so',
                        width: 75,
                        title: 'Tanggal SO',
                        textAlign: 'center',
                        autoHide: true,
                        template: function(row) {
                            if (row.tanggal_so != null) {
                                return formatDate(row.tanggal_so);
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'nama_item',
                        title: 'Nama Barang',
                        width: 150,
                        textAlign: 'center',
                        autoHide: true,
                        template: function(row) {
                            if (row.nama_item != null) {
                                return row.nama_item.toUpperCase();
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'nama_satuan',
                        title: 'Satuan',
                        textAlign: 'center',
                        width: 100,
                        template: function(row) {
                            if (row.nama_satuan != null) {
                                return ucwords(row.nama_satuan);
                            }
                            else {
                                return '-';
                            }
                        },
                    },
                    {
                        field: 'qty_item',
                        title: 'Jumlah Pesanan',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: true,
                        template: function(row) {
                            if (row.qty_item != null) {
                                return parseFloat(row.qty_item).toLocaleString('id-ID', { maximumFractionDigits: 2});
                            }
                            else {
                                return '0';
                            }
                        },
                    },
                    {
                        field: 'qty_outstanding',
                        title: 'Sisa Pesanan',
                        textAlign: 'center',
                        width: 'auto',
                        autoHide: true,
                        template: function(row) {
                            if (row.qty_outstanding != null) {
                                return parseFloat(row.qty_outstanding).toLocaleString('id-ID', { maximumFractionDigits: 2});
                            }
                            else {
                                return '0';
                            }
                        },
                    },
                ],
            });

        });

        $("#btnSearch").on('click', function(e) {
            var datatable = $('#table_cust').KTDatatable();
                datatable.setDataSourceParam('tglStart', $("#tanggal_picker_start").val());
                datatable.setDataSourceParam('tglEnd', $("#tanggal_picker_end").val());
                datatable.reload();

            $("#cardReport").show();
        });

        function viewDetailItem(id) {

            var datatable = $('#list_item_detail').KTDatatable();
                datatable.setDataSourceParam('idCustomer', id);
                datatable.setDataSourceParam('tglStart', $("#tanggal_picker_start").val());
                datatable.setDataSourceParam('tglEnd', $("#tanggal_picker_end").val());
                datatable.reload();

            $("#txtNama").text($("#txt_"+id).text().toUpperCase());

        }

    </script>
@endsection
